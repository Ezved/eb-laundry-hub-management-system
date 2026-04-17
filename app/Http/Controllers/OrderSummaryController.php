<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Service;

class OrderSummaryController extends Controller
{
    // Base prices – load price will be fetched from Services table whenever possible
    private array $prices = [
        'full_service'             => 180, // fallback if matching Service record is missing
        'surcharge'                => 40,  // kept for future use (8kg+), not applied now
        'pickup_delivery'          => 50,  // normal pickup + delivery
        'self_pickup'              => 25,  // when customer picks up own laundry
        'comforter_single_double'  => 200,
        'comforter_queen'          => 220,
        'comforter_king'           => 230,
        'addon_spin'               => 20,
        'addon_dry'                => 20,
        'addon_liquid_detergent'   => 20,
        'addon_fabric_conditioner' => 20,
        'addon_color_safe'         => 8,
    ];

    /**
     * Show summary right after the Booking form (booking.blade.php).
     */
    public function summary(Request $request)
    {
        // 1) Validate the booking form (aligned with your current booking.blade.php)
        $data = $request->validate([
            'pickup_date'   => ['required', 'date', 'after_or_equal:today'],
            'pickup_time'   => ['required', 'string'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:pickup_date'],
            'delivery_time' => ['nullable', 'string'],

            'service_type'  => ['required', 'string', 'max:100'],

            // Bag quantity (this is your "load_qty" field)
            'load_qty'      => ['nullable', 'integer', 'min:0'],

            // Comforters
            'comforter_single_double' => ['nullable', 'integer', 'min:0'],
            'comforter_queen'         => ['nullable', 'integer', 'min:0'],
            'comforter_king'          => ['nullable', 'integer', 'min:0'],

            // Add-ons
            'addon_spin'               => ['nullable', 'integer', 'min:0'],
            'addon_dry'                => ['nullable', 'integer', 'min:0'],
            'addon_liquid_detergent'   => ['nullable', 'integer', 'min:0'],
            'addon_fabric_conditioner' => ['nullable', 'integer', 'min:0'],
            'addon_color_safe'         => ['nullable', 'integer', 'min:0'],

            'special_instructions'   => ['nullable', 'string', 'max:1000'],

            // Display-only text ("₱ 50" / "₱ 25"), so just accept as string
            'pickup_delivery_charge' => ['nullable', 'string'],

            // Checkbox: "I will pickup my own laundry"
            'self_pickup'            => ['nullable', 'boolean'],

            'payment_method'         => ['required', 'in:cod,gcash'],
            'ui_total'               => ['nullable'], // ignored; we compute on server
        ]);

        // Normalize checkbox for self pickup
        $data['self_pickup'] = $request->boolean('self_pickup');

        // 2) Compute totals (now uses the selected service's price from Services table)
        $sum = $this->computeTotals($data);

        // 3) Human-readable lines for the summary blade
        $summaryLines = [
            'service'   => $data['service_type'],
            'load_qty'  => (int)($data['load_qty'] ?? 0),   // Bag Quantity
            'comforter' => $this->buildComforterLine($data),
            'addons'    => $this->buildAddonsLine($data),
            'payment'   => strtoupper($data['payment_method']),
            'datetime'  => $data['pickup_date'] . ' • ' . $data['pickup_time'],

            // Self Pickup display
            'self_pickup' => !empty($data['self_pickup']) ? 'Yes' : 'No',

            'special_instructions' =>
                ($t = trim((string)($data['special_instructions'] ?? ''))) === '' ? '—' : $t,
        ];

        // 4) Customer details from authenticated user
        $user = Auth::user();
        $customer = [
            'name'     => $user->name ?? '—',
            'mobile'   => $user->phone_number ?? '—',
            'email'    => $user->email ?? '—',
            'address'  => $user->address ?? '—',
            'location' => $user->location_details ?? '—',
        ];

        // 5) Store draft in session for confirm() step
        session([
            'order_draft' => [
                'user_id'  => optional($user)->id,
                'payload'  => $data,
                'computed' => $sum,
            ],
        ]);

        // 6) Show order summary page
        return view('user.dashboard.orderSummary', [
            'customer' => $customer,
            'lines'    => $summaryLines,
            'sum'      => $sum,
            'data'     => $data, // used for hidden inputs in the blade
        ]);
    }

    /**
     * Compute totals from the validated payload.
     * Mirrors the JS in booking.blade.php, but the per-load price now comes from
     * the Services table using the selected service_type (e.g. Full Service,
     * Drop-Off Service, Self-Service). Falls back to 180 if not found.
     */
    private function computeTotals(array $d): array
    {
        $items    = [];
        $subtotal = 0;

        // --- Resolve which service (Full Service / Drop-Off / Self-Service) and its price ---
        $serviceTitle = $d['service_type'] ?? 'Full Service';

        // Try to fetch the matching Service row by title
        $serviceModel = Service::where('title', $serviceTitle)->first();

        // If found, use its price; otherwise fall back to the base full_service price (180)
        $perLoadPrice = $serviceModel
            ? (float) $serviceModel->price
            : $this->prices['full_service'];

        // Loads (bag quantity)
        $loads = (int)($d['load_qty'] ?? 0);
        if ($loads > 0) {
            $line = $loads * $perLoadPrice;
            $items[] = [
                'kind'       => 'load',
                'code'       => 'service_load',
                'label'      => $serviceTitle . ' Load(s)', // e.g. "Drop-Off Service Load(s)"
                'qty'        => $loads,
                'unit_price' => $perLoadPrice,
                'line_total' => $line,
            ];
            $subtotal += $line;
        }

        // Comforters
        $mapComforters = [
            'comforter_single_double' => [
                'Comforter (1-2 pcs. Single/Double)',
                $this->prices['comforter_single_double'],
            ],
            'comforter_queen' => [
                'Comforter (1 pc. Queen)',
                $this->prices['comforter_queen'],
            ],
            'comforter_king' => [
                'Comforter (1 pc. King)',
                $this->prices['comforter_king'],
            ],
        ];

        foreach ($mapComforters as $code => [$label, $price]) {
            $q = (int)($d[$code] ?? 0);
            if ($q > 0) {
                $line = $q * $price;
                $items[] = [
                    'kind'       => 'comforter',
                    'code'       => $code,
                    'label'      => $label,
                    'qty'        => $q,
                    'unit_price' => $price,
                    'line_total' => $line,
                ];
                $subtotal += $line;
            }
        }

        // Add-ons
        $mapAddons = [
            'addon_spin'               => ['Spin (11 mins)',                 $this->prices['addon_spin']],
            'addon_dry'                => ['Dry (10 mins)',                  $this->prices['addon_dry']],
            'addon_liquid_detergent'   => ['Liquid Detergent (Triple Pack)', $this->prices['addon_liquid_detergent']],
            'addon_fabric_conditioner' => ['Fabric Conditioner (Twin Pack)', $this->prices['addon_fabric_conditioner']],
            'addon_color_safe'         => ['Color Safe',                     $this->prices['addon_color_safe']],
        ];

        foreach ($mapAddons as $code => [$label, $price]) {
            $q = (int)($d[$code] ?? 0);
            if ($q > 0) {
                $line = $q * $price;
                $items[] = [
                    'kind'       => 'addon',
                    'code'       => $code,
                    'label'      => $label,
                    'qty'        => $q,
                    'unit_price' => $price,
                    'line_total' => $line,
                ];
                $subtotal += $line;
            }
        }

        // 8kg surcharge is UI notice only for now
        $surcharge = 0;

        // Pickup & delivery charge: 50 normal, 25 if self-pickup
        $isSelfPickup   = !empty($d['self_pickup']);
        $pickupDelivery = $isSelfPickup
            ? $this->prices['self_pickup']
            : $this->prices['pickup_delivery'];

        $total = $subtotal + $surcharge + $pickupDelivery;

        return [
            'items'           => $items,
            'subtotal'        => $subtotal,
            'surcharge'       => $surcharge,
            'pickup_delivery' => $pickupDelivery,
            'total'           => $total,
        ];
    }

    private function buildComforterLine(array $d): string
    {
        $parts = [];

        if ((int)($d['comforter_single_double'] ?? 0) > 0) {
            $parts[] = $d['comforter_single_double'] . 'x Single/Double';
        }
        if ((int)($d['comforter_queen'] ?? 0) > 0) {
            $parts[] = $d['comforter_queen'] . 'x Queen';
        }
        if ((int)($d['comforter_king'] ?? 0) > 0) {
            $parts[] = $d['comforter_king'] . 'x King';
        }

        return $parts ? implode(', ', $parts) : '—';
    }

    private function buildAddonsLine(array $d): string
    {
        $map = [
            'addon_spin'               => 'Spin',
            'addon_dry'                => 'Dry',
            'addon_liquid_detergent'   => 'Liquid Detergent',
            'addon_fabric_conditioner' => 'Fabric Conditioner',
            'addon_color_safe'         => 'Color Safe',
        ];

        $parts = [];
        foreach ($map as $key => $label) {
            $q = (int)($d[$key] ?? 0);
            if ($q > 0) {
                $parts[] = $q . 'x ' . $label;
            }
        }

        return $parts ? implode(', ', $parts) : '—';
    }

    /**
     * View an already-saved order (from dashboard).
     */
    public function show(Order $order)
    {
        // Only the owner or an admin can view
        abort_if(
            $order->user_id !== Auth::id() && !optional(Auth::user())->is_admin,
            403
        );

        $order->load('items', 'user');

        $user = $order->user;
        $customer = [
            'name'     => $user->name ?? '—',
            'mobile'   => $user->phone_number ?? '—',
            'email'    => $user->email ?? '—',
            'address'  => $user->address ?? '—',
            'location' => $user->location_details ?? '—',
        ];

        $comforterLine = $order->items->where('kind', 'comforter')
            ->map(fn($it) => $it->qty . 'x ' . ($it->description ?? 'Comforter'))
            ->implode(', ');

        $addonsLine = $order->items->where('kind', 'addon')
            ->map(fn($it) => $it->qty . 'x ' . ($it->description ?? 'Add-on'))
            ->implode(', ');

        $lines = [
            'service'   => $order->service_type ?? 'Full Service',
            'load_qty'  => (int)($order->load_qty ?? 0), // Bag quantity
            'comforter' => $comforterLine ?: '—',
            'addons'    => $addonsLine ?: '—',
            'payment'   => strtoupper($order->payment_method ?? 'COD'),
            'datetime'  => (
                $order->pickup_date
                    ? \Carbon\Carbon::parse($order->pickup_date)->format('Y-m-d')
                    : '—'
            ) . ' • ' . ($order->pickup_time ?: '—'),

            'self_pickup' => $order->self_pickup ? 'Yes' : 'No',

            'special_instructions' =>
                ($t = trim((string)($order->special_instructions ?? ''))) === '' ? '—' : $t,
        ];

        $sum = [
            'pickup_delivery' => (int)($order->pickup_delivery_charge ?? 0),
            'total'           => (int)($order->total ?? $order->total_amount ?? 0),
        ];

        $data = [
            'pickup_date'   => optional($order->pickup_date)->format('Y-m-d'),
            'pickup_time'   => $order->pickup_time,
            'delivery_date' => optional($order->delivery_date)->format('Y-m-d'),
            'delivery_time' => $order->delivery_time,
        ];

        // Choose "Back" target based on context
        $isAdminView = request()->routeIs('admin.*')
            || (optional(Auth::user())->is_admin && request()->is('admin/*'));

        $backUrl  = $isAdminView ? route('admin.dashboard') : route('user.dashboard');
        $readonly = true;

        return view('user.dashboard.orderSummary', compact(
            'customer',
            'lines',
            'sum',
            'data',
            'readonly',
            'backUrl'
        ));
    }
}
