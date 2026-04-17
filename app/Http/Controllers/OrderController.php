<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Models\Order as OrderModel;
use App\Models\OrderItem;
use App\Models\Service;
use App\Mail\OrderReceipt;
use App\Mail\OrderConfirmedMail;
use App\Support\Capacity;

class OrderController extends Controller
{

    public function reschedule(OrderModel $order)
    {
        // Only the owner can see this page
        abort_if($order->user_id !== Auth::id(), 403);

        // Only allow rescheduling for pending / for_pickup
        if (! in_array($order->status, ['pending', 'for_pickup'], true)) {
            return redirect()->route('user.dashboard')
                ->with('fail', 'Only pending or for pickup orders can be rescheduled.');
        }

        // View path: resources/views/user/dashboard/reschedule.blade.php
        return view('user.dashboard.reschedule', compact('order'));
    }

    public function submitReschedule(Request $request, OrderModel $order)
    {
        // Only the owner can submit a reschedule
        abort_if($order->user_id !== Auth::id(), 403);

        if (! in_array($order->status, ['pending', 'for_pickup'], true)) {
            return redirect()->route('user.dashboard')
                ->with('fail', 'Only pending or for pickup orders can be rescheduled.');
        }

        $data = $request->validate([
            'pickup_date'       => ['required', 'date', 'after_or_equal:today'],
            'pickup_time'       => ['required', 'string'],
            'delivery_date'     => ['nullable', 'date', 'after_or_equal:pickup_date'],
            'delivery_time'     => ['nullable', 'string'],
            'self_pickup'       => ['nullable', 'boolean'],
            'reschedule_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        // Update pickup schedule
        $order->pickup_date = $data['pickup_date'];
        $order->pickup_time = $data['pickup_time'];

        // If user will do self-pickup, clear delivery fields
        if (!empty($data['self_pickup'])) {
            $order->delivery_date = null;
            $order->delivery_time = null;
        } else {
            $order->delivery_date = $data['delivery_date'] ?? $order->delivery_date;
            $order->delivery_time = $data['delivery_time'] ?? $order->delivery_time;
        }

        // Store reason in meta (non-breaking)
        $meta = $order->meta ?? [];
        $meta['reschedule_reason'] = $data['reschedule_reason'] ?? null;
        $meta['rescheduled_at']    = now()->toDateTimeString();
        $order->meta = $meta;

        $order->save();

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Your order has been rescheduled.');
    }


    public function myOrders(Request $request)
    {
        $orders = OrderModel::where('user_id', Auth::id())
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'paid')
                    ->orWhere('status', '!=', 'completed');
            })
            ->orderByDesc('created_at')
            ->get();

        return view('user.dashboard.dashboard', compact('orders'));
    }

    /**
     * Public printable receipt (signed URL, no auth).
     */
    public function publicReceipt(Request $request, OrderModel $order)
    {
        // Signature is verified by route middleware.
        $order->load(['items', 'user']);
        return view('user.dashboard.orderReceipt', compact('order'));
    }

    /**
     * Order History: only completed & paid orders.
     */
    public function orderHistory(Request $request)
    {
        $orders = OrderModel::where('user_id', Auth::id())
            ->where('payment_status', 'paid')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->get();

        return view('user.orderHistory.orderHistory', compact('orders'));
    }

    /**
     * Read-only Order Summary page for the signed-in owner.
     */
    public function show(OrderModel $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load('items');

        $comforterLine = $order->items->where('kind', 'comforter')
            ->map(fn($it) => $it->qty . 'x ' . ($it->description ?? 'Comforter'))
            ->implode(', ');

        $addonsLine = $order->items->where('kind', 'addon')
            ->map(fn($it) => $it->qty . 'x ' . ($it->description ?? 'Add-on'))
            ->implode(', ');

        $lines = [
            'service'   => $order->service_type ?? 'Full Service',
            'load_qty'  => (int)($order->load_qty ?? 0),
            'comforter' => $comforterLine ?: '—',
            'addons'    => $addonsLine ?: '—',
            'payment'   => strtoupper($order->payment_method ?? 'COD'),
            'datetime'  => ($order->pickup_date ? \Illuminate\Support\Carbon::parse($order->pickup_date)->format('Y-m-d') : '—')
                . ' • ' . ($order->pickup_time ?: '—'),
            'exceeds'   => ($order->exceeds_8kg ?? false) ? 'Yes (₱40)' : 'No',
            'special_instructions' => ($t = trim((string)($order->special_instructions ?? ''))) === '' ? '—' : $t,
        ];

        $user = Auth::user();
        $customer = [
            'name'     => optional($user)->name ?? '—',
            'mobile'   => optional($user)->phone_number ?? '—',
            'email'    => optional($user)->email ?? '—',
            'address'  => $order->pickup_address ?: (optional($user)->address ?? '—'),
            'location' => data_get($order->meta, 'form.pickup_location_details')
                ?? data_get($order->meta, 'location_details')
                ?? (optional($user)->location_details ?? '—'),
        ];

        $sum = [
    'pickup_delivery' => (int)($order->pickup_delivery_charge ?? 50),
    'total'           => (int)($order->total ?? $order->total_amount ?? 0),
];


        $data = [
            'pickup_date'   => $order->pickup_date,
            'pickup_time'   => $order->pickup_time,
            'delivery_date' => $order->delivery_date,
            'delivery_time' => $order->delivery_time,
        ];

        $readonly = true;
        $backUrl  = route('user.dashboard');

        return view('user.dashboard.orderSummary', compact('customer', 'lines', 'sum', 'data', 'readonly', 'backUrl'));
    }

    /**
     * Optional printable receipt view (auth'd).
     */


    public function receipt(OrderModel $order)
    {
        // Only the owner can view their receipt
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load(['items', 'user']);

        return view('admin.orders.receipt', compact('order'));
    }

    public function cancel(OrderModel $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        if (in_array($order->status, ['completed', 'canceled'], true) || $order->payment_status === 'paid') {
            return back()->with('fail', 'This order can no longer be canceled.');
        }

        $cancellable = ['pending', 'for_pickup', 'on_going'];
        if (!in_array($order->status, $cancellable, true)) {
            return back()->with('fail', 'This order can no longer be canceled.');
        }

        $order->status = 'canceled';
        $order->save();

        return back()->with('success', 'Order canceled.');
    }

    // central pricing
   // central pricing
private array $prices = [
    'full_service'            => 180,
    'per_load_kg_cap'         => 7,
    'surcharge_threshold'     => 8.0,
    'surcharge'               => 40,

    // logistics
    'pickup_delivery'         => 50, // pickup + delivery
    'pickup_only'             => 25, // pickup only (no drop-off)

    // items
    'comforter_single_double' => 200,
    'comforter_queen'         => 220,
    'comforter_king'          => 230,
    'addon_spin'              => 20,
    'addon_dry'               => 20,
    'addon_liquid_detergent'  => 20,
    'addon_fabric_conditioner'=> 20,
    'addon_color_safe'        => 8,
];


    public function summary(Request $request)
{
    $data = $request->validate([
        'pickup_date' => ['required', 'date', 'after_or_equal:today'],
        'pickup_time' => ['required', 'string'],
        'delivery_date' => ['nullable', 'date', 'after_or_equal:pickup_date'],
        'delivery_time' => ['nullable', 'string'],
        'service_type'  => ['required', 'string', 'max:100'],
        'load_qty'      => ['nullable', 'integer', 'min:0'],
        'exceeds_8kg'   => ['nullable', 'boolean'],
        'no_scale'      => ['nullable', 'boolean'],
        'comforter_single_double' => ['nullable', 'integer', 'min:0'],
        'comforter_queen'         => ['nullable', 'integer', 'min:0'],
        'comforter_king'          => ['nullable', 'integer', 'min:0'],
        'addon_spin'               => ['nullable', 'integer', 'min:0'],
        'addon_dry'                => ['nullable', 'integer', 'min:0'],
        'addon_liquid_detergent'   => ['nullable', 'integer', 'min:0'],
        'addon_fabric_conditioner' => ['nullable', 'integer', 'min:0'],
        'addon_color_safe'         => ['nullable', 'integer', 'min:0'],
        'special_instructions'   => ['nullable', 'string', 'max:1000'],
        'pickup_delivery_charge' => ['nullable', 'numeric'],
        'payment_method'         => ['required', 'in:cod,gcash'],
        'ui_total'               => ['nullable'],
        // NEW: “I will pick up from the shop”
        'self_pickup'            => ['nullable', 'boolean'],
    ]);

    $data['exceeds_8kg'] = (bool)($data['exceeds_8kg'] ?? false);
    $data['no_scale']    = (bool)($data['no_scale'] ?? false);
    $data['self_pickup'] = (bool)($data['self_pickup'] ?? false);

    $computed = $this->computeTotals($data);

    session(['order_draft' => [
        'user_id'  => optional(Auth::user())->id,
        'payload'  => $data,
        'computed' => $computed,
    ]]);

    return back()->with(['summary' => $computed, 'form' => $data]);
}


    public function confirm(Request $request)
    {
        $draft = session('order_draft');
        if (!$draft) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Order draft not found.'], 400);
            }
            abort(400, 'Order draft not found.');
        }

        // ✅ CAPACITY CHECK: Block if adding this order would exceed today's 30-load limit
        $incomingLoads = (int) data_get($draft, 'payload.load_qty', 0);

        if ($incomingLoads > 0) {
            if (Capacity::isCapped()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => false,
                        'message' => "Today's load limit of " . Capacity::DAILY_LIMIT . " loads has been reached. Please try again tomorrow."
                    ], 422);
                }

                return redirect()->route('booking.show')
                    ->with('fail', "Today's load limit of " . Capacity::DAILY_LIMIT . " loads has been reached. Please try again tomorrow.");
            }

            if (Capacity::wouldExceedWith($incomingLoads)) {
                $remaining = Capacity::remaining();

                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => false,
                        'message' => "Only {$remaining} load(s) remaining today. You're trying to add {$incomingLoads} load(s)."
                    ], 422);
                }

                return redirect()->route('booking.show')
                    ->with('fail', "Only {$remaining} load(s) remaining today. You're trying to add {$incomingLoads} load(s). Please reduce your load quantity or try again tomorrow.");
            }
        }

        // Continue with rest of the method (keep your existing code after this point)
        $payload  = $draft['payload'];

        $payload  = $draft['payload'];
        $computed = $draft['computed'];

        // Decide pickup fee: ₱50 (pickup + delivery) vs ₱25 (pickup only)
$selfPickup  = !empty($payload['self_pickup']);
$hasDelivery = !empty($payload['delivery_date']) && ! $selfPickup;

$pickupCharge = (int) ($computed['pickup_delivery'] ?? 0);
if ($pickupCharge <= 0) {
    $pickupCharge = $hasDelivery
        ? ($this->prices['pickup_delivery'] ?? 50)
        : ($this->prices['pickup_only'] ?? 25);
}
// normalize any old 49 values to the new ₱50 rule
if ($pickupCharge === 49) {
    $pickupCharge = $this->prices['pickup_delivery'] ?? 50;
}


        // Map service label -> service_id if column exists
        $service = Service::where('title', $payload['service_type'])->first();

        // Rich meta (keeps extra fields even if DB lacks columns)
        $meta = [
    'pricing' => [
        'subtotal'        => $computed['subtotal'] ?? 0,
        'surcharge'       => $computed['surcharge'] ?? 0,
        'pickup_delivery' => $pickupCharge,
    ],
            'flags' => [
                'exceeds_8kg' => (bool)($payload['exceeds_8kg'] ?? false),
                'no_scale'    => (bool)($payload['no_scale'] ?? false),
            ],
            'form' => [
                'service_label'        => $payload['service_type'],
                'special_instructions' => $payload['special_instructions'] ?? null,
                'pickup_email'            => $request->input('pickup_email'),
                'pickup_location_details' => $request->input('pickup_location_details'),
            ],
        ];

        // Build order data (keep your existing columns for compatibility)
        $orderData = [
            'user_id'       => $draft['user_id'],
            'service_type'  => $payload['service_type'],
            'pickup_date'   => $payload['pickup_date'],
            'pickup_time'   => $payload['pickup_time'],
            'delivery_date' => $payload['delivery_date'] ?? null,
            'delivery_time' => $payload['delivery_time'] ?? null,

            'load_qty'      => $payload['load_qty'] ?? 0,
            'exceeds_8kg'   => $payload['exceeds_8kg'] ?? false,
            'no_scale'      => $payload['no_scale'] ?? false,
            'special_instructions' => $payload['special_instructions'] ?? null,
            'payment_method' => $payload['payment_method'],

'pickup_delivery_charge' => $pickupCharge,
            'subtotal'      => $computed['subtotal'] ?? 0,
            'surcharge'     => $computed['surcharge'] ?? 0,
            'total'         => $computed['total'] ?? 0,
            'total_amount'  => $computed['total'] ?? 0,

            'status'         => 'pending',
            'payment_status' => 'unpaid',

            'pickup_name'             => $request->input('pickup_name', optional(Auth::user())->name),
            'pickup_email'            => $request->input('pickup_email', optional(Auth::user())->email),
            'pickup_phone'            => $request->input('pickup_phone', optional(Auth::user())->phone_number),
            'pickup_address'          => $request->input('pickup_address', optional(Auth::user())->address),
            'pickup_location_details' => $request->input('pickup_location_details', optional(Auth::user())->location_details),

            'meta' => $meta,
        ];

        // Add partner's fields when the schema supports them
        if (Schema::hasColumn('orders', 'category')) {
            $orderData['category'] = 'pickup_delivery';
        }
        if (Schema::hasColumn('orders', 'service_id')) {
            $orderData['service_id'] = $service?->id;
        }

        $order = OrderModel::create($orderData);

        foreach ($computed['items'] as $it) {
            OrderItem::create([
                'order_id'    => $order->id,
                'kind'        => $it['kind'],
                'code'        => $it['code'],
                'description' => $it['label'],
                'qty'         => $it['qty'],
                'unit_price'  => $it['unit_price'],
                'line_total'  => $it['line_total'],
            ]);
        }

        session()->forget('order_draft');

        // Email: prefer logged-in user's email, then pickup_email, then meta.form.pickup_email
        try {
            $recipient = optional(Auth::user())->email
                ?: ($order->pickup_email ?? null)
                ?: data_get($order->meta, 'form.pickup_email');

            if ($recipient) {
                if (class_exists(OrderConfirmedMail::class)) {
                    Mail::to($recipient)->send(new OrderConfirmedMail($order));
                } else {
                    // fallback to your previous mailable if OrderConfirmedMail is absent
                    if (class_exists(OrderReceipt::class)) {
                        Mail::to($recipient)->send(new OrderReceipt($order));
                    }
                }
            }
        } catch (\Throwable $e) {
            // swallow mail failures
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'        => true,
                'order_id'  => $order->id,
                'total'     => $order->total_amount,
                'redirect' => route('order.thankyou', $order),
                'message'   => 'Order received. Thank you!',
            ]);
        }

        return redirect()->route('order.thankyou', $order);
    }

    /**
     * ₱180/load + comforters + add-ons + surcharge + pickup/delivery.
     */
    /**
 * ₱180/load + comforters + add-ons + surcharge + pickup/delivery.
 *
 * Rules:
 *  - Pickup + Delivery      => ₱50
 *  - Pickup only (no drop)  => ₱25
 *    (either no delivery_date, or self_pickup = true)
 */
private function computeTotals(array $d): array
{
    $items    = [];
    $subtotal = 0;

    // Loads
    $loads = (int)($d['load_qty'] ?? 0);
    if ($loads > 0) {
        $u    = $this->prices['full_service'];
        $line = $loads * $u;
        $items[] = [
            'kind'       => 'load',
            'code'       => 'full_service_load',
            'label'      => 'Full Service Load(s)',
            'qty'        => $loads,
            'unit_price' => $u,
            'line_total' => $line,
        ];
        $subtotal += $line;
    }

    // Comforters
    $comforters = [
        'comforter_single_double' => ['Comforter (1-2 pcs. Single/Double)', $this->prices['comforter_single_double']],
        'comforter_queen'         => ['Comforter (1 pc. Queen)',            $this->prices['comforter_queen']],
        'comforter_king'          => ['Comforter (1 pc. King)',             $this->prices['comforter_king']],
    ];
    foreach ($comforters as $code => [$label, $u]) {
        $q = (int)($d[$code] ?? 0);
        if ($q > 0) {
            $line = $q * $u;
            $items[] = [
                'kind'       => 'comforter',
                'code'       => $code,
                'label'      => $label,
                'qty'        => $q,
                'unit_price' => $u,
                'line_total' => $line,
            ];
            $subtotal += $line;
        }
    }

    // Add-ons
    $addons = [
        'addon_spin'               => ['Spin (11 mins)',                 $this->prices['addon_spin']],
        'addon_dry'                => ['Dry (10 mins)',                  $this->prices['addon_dry']],
        'addon_liquid_detergent'   => ['Liquid Detergent (Triple Pack)', $this->prices['addon_liquid_detergent']],
        'addon_fabric_conditioner' => ['Fabric Conditioner (Twin Pack)', $this->prices['addon_fabric_conditioner']],
        'addon_color_safe'         => ['Color Safe',                     $this->prices['addon_color_safe']],
    ];
    foreach ($addons as $code => [$label, $u]) {
        $q = (int)($d[$code] ?? 0);
        if ($q > 0) {
            $line = $q * $u;
            $items[] = [
                'kind'       => 'addon',
                'code'       => $code,
                'label'      => $label,
                'qty'        => $q,
                'unit_price' => $u,
                'line_total' => $line,
            ];
            $subtotal += $line;
        }
    }

    $surcharge = (!empty($d['exceeds_8kg'])) ? $this->prices['surcharge'] : 0;

    // Decide if this booking has delivery or is pickup-only
    $selfPickup  = !empty($d['self_pickup']);          // “I will pick up from shop”
    $hasDelivery = !empty($d['delivery_date']) && ! $selfPickup;

    $pickupCharge = $hasDelivery
        ? ($this->prices['pickup_delivery'] ?? 50)     // pickup + delivery
        : ($this->prices['pickup_only'] ?? 25);        // pickup only

    $total = $subtotal + $surcharge + $pickupCharge;

    return [
        'items'           => $items,
        'subtotal'        => $subtotal,
        'surcharge'       => $surcharge,
        'pickup_delivery' => $pickupCharge,
        'total'           => $total,
    ];
}

    public function thankYou(OrderModel $order)
    {
        // Only the owner can see their thank-you page
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load(['items', 'user']);

        return view('user.dashboard.thankyou', compact('order'));
    }
}
