<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;
use App\Support\Capacity; // ✅ NEW: load-based daily capacity helper

class AdminWalkinController extends Controller
{
    /**
     * Show walk-in form (prefilled from preview if available).
     */
    public function create()
    {
        // ✅ Capacity guard: no new walk-ins when today's load limit is reached
        if (Capacity::isCapped()) {
            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'fail',
                    "Today’s load limit has been reached (" . Capacity::DAILY_LIMIT .
                    " loads for 8:00 AM–8:00 PM). You can no longer add walk-in orders for today."
                );
        }

        // Keep user inputs when coming back from preview
        $prefill = session('walkin_preview_data', []);

        // Centralised price map (keys = service titles as in Services page)
        $priceMap = $this->buildServicePriceMap();

        // View: resources/views/admin/dashboard/walkin.blade.php
        return view('admin.dashboard.walkin', compact('priceMap', 'prefill'));
    }

    /**
     * Validate + compute a preview summary (but don't save yet).
     * Used by BOTH:
     *  - normal Add Walk-in page
     *  - Customers -> New Order (from_customer)
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'customer_name'              => 'required|string|max:255',
            'customer_phone'             => 'nullable|string|max:50',
            'customer_address'           => 'nullable|string|max:255',
            'order_date'                 => 'required|date',
            'walkin_time'                => 'required|string|max:50',
            // must match <option value="..."> in the Blade
'service_type'               => 'required|string|in:Full Service,Drop-Off Service,Self-Service',
            'load_qty'                   => 'required|integer|min:0',
            'exceeds_8kg'                => 'nullable|boolean',
            'comforter_single_double'    => 'nullable|integer|min:0',
            'comforter_queen'            => 'nullable|integer|min:0',
            'comforter_king'             => 'nullable|integer|min:0',
            'addon_spin'                 => 'nullable|integer|min:0',
            'addon_dry'                  => 'nullable|integer|min:0',
            'addon_liquid_detergent'     => 'nullable|integer|min:0',
            'addon_fabric_conditioner'   => 'nullable|integer|min:0',
            'addon_color_safe'           => 'nullable|integer|min:0',
            'special_instructions'       => 'nullable|string|max:1000',
            'payment_method'             => 'required|string|in:cash,gcash,cod',
        ]);

        // checkbox normalisation
        $data['exceeds_8kg'] = (bool) ($data['exceeds_8kg'] ?? false);

        // ✅ Load-based guard: block if this walk-in’s loads would exceed today’s cap
        $incomingLoads = (int) ($data['load_qty'] ?? 0);
        if ($incomingLoads > 0 && (Capacity::wouldExceedWith($incomingLoads) || Capacity::isCapped())) {
            return back()
                ->withInput()
                ->with(
                    'fail',
                    "This walk-in adds {$incomingLoads} load(s), which would exceed today’s limit of " .
                    Capacity::DAILY_LIMIT . " loads (8:00 AM–8:00 PM). Please adjust the loads or schedule on another day."
                );
        }

        // Extra info coming from Customers -> New Order
        $data['origin']             = $request->input('origin');               // e.g. "from_customer"
        $data['linked_customer_id'] = $request->input('linked_customer_id');   // customer id
        $data['linked_user_id']     = $request->input('linked_user_id');       // user id (if any)

        $services = Service::whereIn('title', [
        'Full Service',
        'Drop-Off Service',
        'Self-Service',
    ])
    ->pluck('price', 'title');   // ['Full Service' => 180, 'Drop-Off Service' => 150, ...]

$pricePerLoad = (int) ($services[$data['service_type']] ?? 0);


        // Add-on & surcharge constants
        $EXTRAS = [
            'surcharge'               => 40,
            'comforter_single_double' => 200,
            'comforter_queen'         => 220,
            'comforter_king'          => 230,
            'addon_spin'              => 20,
            'addon_dry'               => 20,
            'addon_liquid_detergent'  => 20,
            'addon_fabric_conditioner'=> 20,
            'addon_color_safe'        => 8,
        ];

        [$sum, $lines] = $this->computeWalkinSummary($data, $pricePerLoad, $EXTRAS);

        $customer = [
            'name'    => $data['customer_name'],
            'mobile'  => $data['customer_phone'] ?: '—',
            'address' => $data['customer_address'] ?? '',
        ];

        // Keep everything in session so store() knows where this came from
        session([
            'walkin_preview_data'     => $data,
            'walkin_preview_sum'      => $sum,
            'walkin_preview_lines'    => $lines,
            'walkin_preview_customer' => $customer,
        ]);

        return view('admin.dashboard.walkinSummary', compact('data', 'sum', 'lines', 'customer'));
    }

    /**
     * Persist the walk-in order using the previewed data.
     * Also respects origin (dashboard vs from_customer).
     */
    public function store(Request $request)
    {
        $data = session('walkin_preview_data');
        $sum  = session('walkin_preview_sum');

        if (! $data || ! $sum) {
            return redirect()->route('admin.walkin.create')
                ->with('fail', 'Session expired. Please re-enter details.');
        }

        // ✅ Final capacity guard at the moment of saving (protects against race conditions)
        $incomingLoads = (int) ($data['load_qty'] ?? 0);
        if ($incomingLoads > 0 && (Capacity::wouldExceedWith($incomingLoads) || Capacity::isCapped())) {
            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'fail',
                    "Today’s load limit has been reached (" . Capacity::DAILY_LIMIT .
                    " loads for 8:00 AM–8:00 PM). This walk-in order was not saved."
                );
        }

        // Read what we stashed in preview()
        $origin           = $data['origin']             ?? null;
        $linkedCustomerId = $data['linked_customer_id'] ?? null;
        $linkedUserId     = $data['linked_user_id']     ?? null;

        // 1️⃣ If we came from Customers “New Order”, REUSE that Customer
        if ($origin === 'from_customer' && $linkedCustomerId) {
            $customer = Customer::find($linkedCustomerId);
            $isNew    = false;
        } else {
            // 2️⃣ Otherwise, match (or create) by phone_number for real walk-ins
            $customer = Customer::firstOrNew([
                'phone_number' => $data['customer_phone'] ?: null,
            ]);
            $isNew = ! $customer->exists; // true if this is a brand-new walk-in customer
        }

        // Fallback safety
        if (! $customer) {
            $customer = new Customer();
            $customer->phone_number = $data['customer_phone'] ?: null;
            $isNew = true;
        }

        // Only fill blanks so we don't overwrite existing data
        if (empty($customer->name)) {
            $customer->name = $data['customer_name'];
        }
        if (! empty($data['customer_address']) && empty($customer->address)) {
            $customer->address = $data['customer_address'];
        }

        // Only set email if not already filled
        if (! isset($customer->email)) {
            $customer->email = null;
        }

        // ✅ Make sure brand-new walk-ins are VISIBLE by default
        if ($isNew && Schema::hasColumn('customers', 'is_hidden')) {
            $customer->is_hidden = false;
        }

        // 🚫 Do NOT touch user_id (keeps Category logic)
        $customer->save();

        // If this came from Customers -> New Order and that customer has a linked user,
        // also link the order to that user so it appears in their user-side history.
        $userIdForOrder = null;
        if ($origin === 'from_customer' && $linkedUserId) {
            $userIdForOrder = (int) $linkedUserId;
        }

        // Map service label -> service_id (if column exists)
        $service   = Service::where('title', $data['service_type'])->first();
        $hasSvcId  = Schema::hasColumn('orders', 'service_id');
        $hasExFlag = Schema::hasColumn('orders', 'exceeds_8kg');

        // Meta
        $meta = [
            'comforters' => [
                'single_double' => (int) ($data['comforter_single_double'] ?? 0),
                'queen'         => (int) ($data['comforter_queen'] ?? 0),
                'king'          => (int) ($data['comforter_king'] ?? 0),
            ],
            'addons' => [
                'spin'               => (int) ($data['addon_spin'] ?? 0),
                'dry'                => (int) ($data['addon_dry'] ?? 0),
                'liquid_detergent'   => (int) ($data['addon_liquid_detergent'] ?? 0),
                'fabric_conditioner' => (int) ($data['addon_fabric_conditioner'] ?? 0),
                'color_safe'         => (int) ($data['addon_color_safe'] ?? 0),
            ],
            'flags' => [
                'exceeds_8kg' => (bool) ($data['exceeds_8kg'] ?? false),
            ],
            'service' => [
                'label' => $data['service_type'],
                'id'    => $service?->id,
            ],
        ];

        $payload = [
            'customer_id'         => $customer->id,
            'user_id'             => $userIdForOrder,        // null for pure walk-ins; user id if linked
            'category'            => 'walkin',
            'service_type'        => $data['service_type'],
            'pickup_name'         => $data['customer_name'],
            'pickup_phone'        => $data['customer_phone'] ?: null,
            'pickup_address'      => $data['customer_address'] ?: null,
            'pickup_date'         => $data['order_date'],
            'pickup_time'         => $data['walkin_time'],
            'load_qty'            => (int) $data['load_qty'],
            'payment_method'      => $data['payment_method'],
            'status'              => 'on_going',
            'payment_status'      => 'unpaid',
            'total_amount'        => (int) $sum['total'],
            'special_instructions'=> $data['special_instructions'] ?? null,
            'meta'                => $meta,
        ];

        if ($hasSvcId) {
            $payload['service_id'] = $service?->id;
        }
        if ($hasExFlag) {
            $payload['exceeds_8kg'] = (bool) ($data['exceeds_8kg'] ?? false);
        }

        $order = Order::create($payload);

        // Clear preview session
        session()->forget([
            'walkin_preview_data',
            'walkin_preview_sum',
            'walkin_preview_lines',
            'walkin_preview_customer',
        ]);

        // 🔁 REDIRECT LOGIC:
        // - If created from Customers page → go back to that customer's orders page
        // - If created from Admin Walk-in page → go back to Admin Dashboard
        if ($origin === 'from_customer' && $linkedCustomerId) {
            return redirect()
                ->route('customers.orders', $customer)   // customer page
                ->with('success', 'Walk-in order saved.');
        }

        return redirect()
            ->route('admin.dashboard')                  // admin dashboard
            ->with('success', 'Walk-in order saved.');
    }

    /**
     * Pricing & line-builder used by preview().
     */
    private function computeWalkinSummary(array $data, int $pricePerLoad, array $X): array
    {
        $total = (int) $data['load_qty'] * $pricePerLoad;
        if (! empty($data['exceeds_8kg'])) {
            $total += $X['surcharge'];
        }

        // Comforters
        $csd = (int) ($data['comforter_single_double'] ?? 0);
        $cq  = (int) ($data['comforter_queen'] ?? 0);
        $ck  = (int) ($data['comforter_king'] ?? 0);

        $cLines = [];
        if ($csd) {
            $total += $csd * $X['comforter_single_double'];
            $cLines[] = "{$csd} × Single/Double";
        }
        if ($cq)  {
            $total += $cq  * $X['comforter_queen'];
            $cLines[] = "{$cq} × Queen";
        }
        if ($ck)  {
            $total += $ck  * $X['comforter_king'];
            $cLines[] = "{$ck} × King";
        }

        // Add-ons
        $aLines = [];
        $adds = [
            'Spin (11 mins)'                 => ['key' => 'addon_spin'],
            'Dry (10 mins)'                  => ['key' => 'addon_dry'],
            'Liquid Detergent (Triple Pack)' => ['key' => 'addon_liquid_detergent'],
            'Fabric Conditioner (Twin Pack)' => ['key' => 'addon_fabric_conditioner'],
            'Color Safe'                     => ['key' => 'addon_color_safe'],
        ];
        foreach ($adds as $label => $def) {
            $qty = (int) ($data[$def['key']] ?? 0);
            if ($qty) {
                $total += $qty * $X[$def['key']];
                $aLines[] = "{$qty} × {$label}";
            }
        }

        $lines = [
            'datetime'             => date('M d, Y', strtotime($data['order_date'])) . ' • ' . $data['walkin_time'],
            'service'              => $data['service_type'],
            'service_price'        => $pricePerLoad,
            'load_qty'             => (int) $data['load_qty'],
            'comforter'            => $cLines ? implode(', ', $cLines) : '—',
            'addons'               => $aLines ? implode(', ', $aLines) : '—',
            'payment'              => strtoupper($data['payment_method']),
            'exceeds'              => ! empty($data['exceeds_8kg']) ? 'Yes (₱'.$X['surcharge'].')' : 'No',
            'special_instructions' => trim((string) ($data['special_instructions'] ?? '')) ?: '—',
        ];

        return [['total' => $total], $lines];
    }

    /**
     * Show walk-in form prefilled from a Customer row (Customers -> New Order).
     */
    public function createFromCustomer(Customer $customer)
    {
        $user = $customer->user; // can be null for true walk-in

        $prefill = [
            'customer_name'    => $customer->name
                                 ?? optional($user)->name
                                 ?? '',
            'customer_phone'   => $customer->phone_number
                                 ?? optional($user)->phone_number
                                 ?? '',
            'customer_address' => $customer->address
                                 ?? optional($user)->address
                                 ?? '',
            // default service type
            'service_type'     => 'Full Service',
        ];

        $priceMap = $this->buildServicePriceMap();
        $userId   = $user?->id;

        // View: resources/views/admin/customers/from_customer.blade.php
        return view('admin.customers.from_customer', compact('prefill', 'priceMap', 'customer', 'userId'));
    }

    /**
     * Centralised map of service title => price,
     * used by both normal walk-in and from_customer.
     */
    protected function buildServicePriceMap(): array
{
    // Titles as stored in DB (from Services page)
    $titles = [
        'Full Service',
        'Drop-Off Service',
        'Self-Service',
    ];

    $map = [];

    Service::whereIn('title', $titles)
        ->where('is_active', true) // optional: only active services
        ->get()
        ->each(function ($service) use (&$map) {
            // Key = exact title, e.g. "Full Service"
            $map[$service->title] = (float) $service->price;
        });

    // Ensure all expected keys exist, even if service is missing
    foreach ($titles as $title) {
        if (! array_key_exists($title, $map)) {
            $map[$title] = 0; // fallback
        }
    }

    return $map;
}

}
