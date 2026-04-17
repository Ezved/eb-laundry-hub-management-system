<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Notification; // partner add
use Illuminate\Support\Facades\Mail;        // partner add
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\OrderReceipt;
use App\Support\Capacity; // ⬅ ADD THIS


class AdminOrderController extends Controller
{
    /**
     * ALL orders (paginated, newest first).
     * Route to this if you need a full listing page.
     */
    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Manila');

        $sortParam = $request->query('sort', 'desc');
        $sort = in_array($sortParam, ['asc', 'desc']) ? $sortParam : 'desc';

        $date = $request->query('date');

        $query = Order::with('user')
            ->where('hidden_from_history', false); // ⬅ NEW

        try {
            if ($date) {
                $day = Carbon::parse($date, $tz)->toDateString();
                $query->whereDate('created_at', $day);
            }
        } catch (\Throwable $e) {
            // ignore invalid date
        }

        $orders = $query
            ->orderBy('created_at', $sort)
            ->paginate(50)
            ->withQueryString();

        return view('admin.orders.orderHistory', compact('orders'));
    }


    /**
     * Dashboard "today" view.
     *
     * Recent Orders list rule:
     *  - SHOW all orders (any date)
     *  - UNTIL they are BOTH: status = "completed" AND payment_status = "paid"
     *
     * Delivery calendar + sales still use Asia/Manila date ranges.
     */
    public function today(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Manila');

        // Manila day range (still useful for labels / calendar / sales)
        $start = Carbon::now($tz)->startOfDay();
        $end   = Carbon::now($tz)->endOfDay();

        /*
     * "Recent Orders Today" list:
     * - include ONLY orders created today (Manila),
     * - any status / payment_status.
     */
        $orders = Order::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        // ===== Delivery Calendar (exclude walk-ins) =====
        $windowStart = Carbon::now($tz)->startOfMonth()->subMonth();
        $windowEnd   = Carbon::now($tz)->endOfMonth()->addMonth();

        $calendarOrders = Order::with('user')
            ->where('category', '!=', 'walkin')
            ->where(function ($q) use ($windowStart, $windowEnd) {
                $q->whereBetween('pickup_date',   [$windowStart->toDateString(), $windowEnd->toDateString()])
                    ->orWhereBetween('delivery_date', [$windowStart->toDateString(), $windowEnd->toDateString()]);
            })
            ->get();

        $calendarEvents = $calendarOrders->flatMap(function ($o) {
            $customerName  = $o->user->name ?? ($o->pickup_name ?? 'Customer');
            $base = [
                'order_id'       => $o->id,
                'user_id'        => $o->user_id,
                'customer'       => $customerName,
                'status'         => $o->status,
                'payment_status' => $o->payment_status ?? 'unpaid',
            ];

            $events = [];
            if ($o->pickup_date) {
                $events[] = $base + [
                    'id'    => $o->id . '-p',
                    'date'  => Carbon::parse($o->pickup_date)->format('Y-m-d'),
                    'time'  => $o->pickup_time,
                    'type'  => 'pickup',
                    'label' => 'Pickup • ' . $customerName,
                ];
            }
            if ($o->delivery_date) {
                $events[] = $base + [
                    'id'    => $o->id . '-d',
                    'date'  => Carbon::parse($o->delivery_date)->format('Y-m-d'),
                    'time'  => $o->delivery_time,
                    'type'  => 'delivery',
                    'label' => 'Delivery • ' . $customerName,
                ];
            }
            return $events;
        })->values();

        // ===== Sales totals (PAID only) — Manila clock =====
        $sumFn = fn($o) => (float)($o->display_total ?? $o->total ?? $o->total_amount ?? 0);

        $dayStart   = Carbon::now($tz)->startOfDay();
        $dayEnd     = Carbon::now($tz)->endOfDay();
        $weekStart  = Carbon::now($tz)->startOfWeek(Carbon::SUNDAY);
        $weekEnd    = Carbon::now($tz)->endOfWeek(Carbon::SUNDAY);
        $monthStart = Carbon::now($tz)->startOfMonth();
        $monthEnd   = Carbon::now($tz)->endOfMonth();

        $salesDay = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->get()
            ->sum($sumFn);

        $salesWeek = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->get()
            ->sum($sumFn);

        $salesMonth = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->get()
            ->sum($sumFn);

        // === NEW: limit for "New Walk-In" ===
        $todayOrdersCount = Capacity::todaysLoads();   // sum of load_qty today (8:00–20:00, not canceled)
        $todayOrdersLimit = Capacity::DAILY_LIMIT;     // 30
        $walkinDisabled   = Capacity::isCapped();

        return view('admin.dashboard.dashboard', compact(
            'orders',
            'calendarEvents',
            'salesDay',
            'salesWeek',
            'salesMonth',
            'todayOrdersCount',
            'todayOrdersLimit',
            'walkinDisabled'
        ));
    }


    /**
     * Update payment status (unpaid/paid).
     */
    public function updatePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
        ]);

        $order->payment_status = $request->payment_status;
        $order->save();

        return back()->with('success', 'Payment updated.');
    }

    /**
     * Update laundry status. (merged w/ partner’s notifications)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,for_pickup,on_going,to_be_received,for_delivery,completed,canceled',
        ]);

        $old = $order->status;
        $order->status = $request->status;
        $order->save();

        // Send emails only if the status actually changed
        if ($old !== $order->status) {
            // Build recipient list (unique, non-empty)
            $recipients = [];
            if ($order->user && $order->user->email) {
                $recipients[] = $order->user->email;
            }
            if ($e = data_get($order, 'meta.email')) {
                $recipients[] = $e;
            }
            $recipients = array_values(array_unique(array_filter($recipients)));

            $viewData = [
                'order'        => $order->loadMissing('user'),
                'customerName' => optional($order->user)->name ?? ($order->pickup_name ?? 'Customer'),
                'messageText'  => null,
                'actionUrl'    => route('orders.show', $order),
            ];

            // Out for delivery / To be received
            if (in_array($order->status, ['for_delivery', 'to_be_received'], true)) {
                foreach ($recipients as $email) {
                    if (class_exists(\App\Notifications\OutForDeliveryNotification::class)) {
                        Notification::route('mail', $email)
                            ->notify(new \App\Notifications\OutForDeliveryNotification($order));
                    } else {
                        Mail::send('emails.notify_customer_of_delivery_in_progress', $viewData, function ($m) use ($email) {
                            $m->to($email)->subject('Your Order Is Out for Delivery 🚚');
                        });
                    }
                }
            }

            // Rider on the way (For Pickup)
            if ($order->status === 'for_pickup') {
                foreach ($recipients as $email) {
                    if (class_exists(\App\Notifications\PickupOnTheWayNotification::class)) {
                        Notification::route('mail', $email)
                            ->notify(new \App\Notifications\PickupOnTheWayNotification($order));
                    } else {
                        Mail::send('emails.notify_customer_pick_up_on_the_way', $viewData, function ($m) use ($email) {
                            $m->to($email)->subject('Your Rider Is On the Way 🚚');
                        });
                    }
                }
            }
        }

        // Keep your existing in-app notification
        if ($order->status === 'for_delivery' && $order->user_id && class_exists(\App\Models\UserNotification::class)) {
            \App\Models\UserNotification::create([
                'user_id'  => $order->user_id,
                'order_id' => $order->id,
                'title'    => 'Your laundry is for delivery',
                'body'     => 'Heads up! Your laundry is now on the way.',
            ]);
        }

        return back()->with('success', 'Status updated.');
    }

    /**
     * Delete an order (and child items if relation exists).
     */
    public function destroy(Order $order)
    {
        if (method_exists($order, 'items')) {
            $order->items()->delete();
        }

        $order->delete();

        return back()->with('success', 'Order deleted.');
    }

    /**
     * DETAILS page
     * - Walk-in: renders admin.dashboard.walkinSummary
     * - Pickup/Delivery: renders user.dashboard.orderSummary
     */
    public function show(Order $order)
    {
        $order->load('items', 'user');

        // Walk-in branch
        if (($order->category ?? '') === 'walkin') {
            $customer = [
                'name'    => $order->pickup_name ?? 'Walk-in / —',
                'mobile'  => $order->pickup_phone ?? '—',
                'address' => $order->pickup_address ?? '',
            ];

            $meta = (array) ($order->meta ?? []);

            $comforters = (array) ($meta['comforters'] ?? []);
            $cLines = [];
            $sd = (int) ($comforters['single_double'] ?? 0);
            $q  = (int) ($comforters['queen'] ?? 0);
            $k  = (int) ($comforters['king'] ?? 0);
            if ($sd) $cLines[] = "{$sd} × Single/Double";
            if ($q)  $cLines[] = "{$q} × Queen";
            if ($k)  $cLines[] = "{$k} × King";

            $addons = (array) ($meta['addons'] ?? []);
            $aLines = [];
            $map = [
                'spin'               => 'Spin (11 mins)',
                'dry'                => 'Dry (10 mins)',
                'liquid_detergent'   => 'Liquid Detergent (Triple Pack)',
                'fabric_conditioner' => 'Fabric Conditioner (Twin Pack)',
                'color_safe'         => 'Color Safe',
            ];
            foreach ($map as $key => $label) {
                $qty = (int) ($addons[$key] ?? 0);
                if ($qty) $aLines[] = "{$qty} × {$label}";
            }

            $lines = [
                'datetime'             => ($order->pickup_date ? Carbon::parse($order->pickup_date)->format('M d, Y') : '—')
                    . ' • ' . ($order->pickup_time ?: '—'),
                'service'              => $order->service_type ?? 'Full Service',
                'load_qty'             => (int) ($order->load_qty ?? 0),
                'exceeds'              => ($order->exceeds_8kg ?? false) ? 'Yes (₱40)' : 'No',
                'comforter'            => $cLines ? implode(', ', $cLines) : '—',
                'addons'               => $aLines ? implode(', ', $aLines) : '—',
                'payment'              => strtoupper($order->payment_method ?? 'CASH'),
                'special_instructions' => (trim((string)($order->special_instructions ?? '')) === '') ? '—' : $order->special_instructions,
            ];

            $sum = [
                'total' => (int) ($order->total_amount ?? $order->total ?? 0),
            ];

            $readonly = true;
            return view('admin.dashboard.walkinSummary', compact('customer', 'lines', 'sum', 'readonly'));
        }

        // Pickup & delivery
        $user = $order->user;
        $customer = [
            'name'     => $user->name ?? ($order->pickup_name ?? '—'),
            'mobile'   => $user->phone_number ?? ($order->pickup_phone ?? '—'),
            'email'    => $user->email ?? '—',
            'address'  => $order->pickup_address ?? ($user->address ?? '—'),
            'location' => data_get($order->meta, 'location_details') ?? ($user->location_details ?? '—'),
        ];

        $comforterLine = $order->items->where('kind', 'comforter')
            ->map(fn($it) => $it->qty . 'x ' . ($it->description ?? 'Comforter'))
            ->implode(', ');

        $addonsLine = $order->items->where('kind', 'addon')
            ->map(fn($it) => $it->qty . 'x ' . ($it->description ?? 'Add-on'))
            ->implode(', ');

        $SURCHARGE = 40;
        $lines = [
            'service'   => $order->service_type ?? 'Full Service',
            'load_qty'  => (int)($order->load_qty ?? 0),
            'comforter' => $comforterLine ?: '—',
            'addons'    => $addonsLine ?: '—',
            'payment'   => strtoupper($order->payment_method ?? 'COD'),
            'datetime'  => ($order->pickup_date ? Carbon::parse($order->pickup_date)->format('Y-m-d') : '—')
                . ' • ' . ($order->pickup_time ?: '—'),
            'exceeds'   => $order->exceeds_8kg ? 'Yes (₱' . $SURCHARGE . ')' : 'No',
            'special_instructions' => (trim((string)($order->special_instructions ?? '')) === '') ? '—' : $order->special_instructions,
        ];

       $sum = [
    'pickup_delivery' => (int)($order->pickup_delivery_charge ?? 50),
    'total'           => (int)($order->total ?? $order->total_amount ?? 0),
];



        $data = [
            'pickup_date'   => $order->pickup_date ? Carbon::parse($order->pickup_date)->format('Y-m-d') : null,
            'pickup_time'   => $order->pickup_time,
            'delivery_date' => $order->delivery_date ? Carbon::parse($order->delivery_date)->format('Y-m-d') : null,
            'delivery_time' => $order->delivery_time,
        ];

        $readonly = true;
        $backUrl  = route('admin.dashboard');

        return view('user.dashboard.orderSummary', compact('customer', 'lines', 'sum', 'data', 'readonly', 'backUrl'));
    }

    /**
     * Printable receipt page.
     */
    public function receipt(Order $order)
    {
        $order->load('items', 'user');

        // matches: resources/views/admin/dashboard/orderReceipt.blade.php
        return view('admin.dashboard.orderReceipt', compact('order'));
    }



    /**
     * All orders for a specific customer (admin view).
     */
    public function customerOrders(User $user)
    {
        $orders = Order::with('items')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.customers.orders', compact('user', 'orders'));
    }

    /**
     * Simple total for a range (kept from your version; used elsewhere).
     */
    public function salesTotal(Request $r)
    {
        $tz   = config('app.timezone', 'Asia/Manila');
        $mode = $r->query('mode', 'day'); // day|week|month

        try {
            switch ($mode) {
                case 'day':
                    $date  = Carbon::parse($r->query('date'), $tz)->startOfDay();
                    $start = $date->copy();
                    $end   = $date->copy()->endOfDay();
                    break;

                case 'week':
                    $ws     = Carbon::parse($r->query('start'), $tz)->startOfDay();
                    $start  = $ws->copy();
                    $end    = $ws->copy()->addDays(6)->endOfDay();
                    break;

                case 'month':
                    $year   = (int) $r->query('year');
                    $month  = (int) $r->query('month'); // 1..12
                    $m      = Carbon::createFromDate($year, $month, 1, $tz);
                    $start  = $m->copy()->startOfMonth();
                    $end    = $m->copy()->endOfMonth();
                    break;

                default:
                    return response()->json(['ok' => false, 'message' => 'Invalid mode'], 422);
            }

            $sumFn = fn($o) => (float) ($o->display_total ?? $o->total ?? $o->total_amount ?? 0);

            $total = Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$start, $end])
                ->get()
                ->sum($sumFn);

            return response()->json(['ok' => true, 'total' => (float) $total]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Bad parameters'], 422);
        }
    }

    public function prepareReceipt(Request $request, Order $order)
    {
        $data = $request->validate([
            'service_type'    => ['nullable', 'string', 'max:255'],
            'final_load_qty'  => ['required', 'integer', 'min:0'],
            'exceeds_8kg'     => ['nullable'],
            'final_total'     => ['required', 'numeric', 'min:0'],
        ]);

        if (!empty($data['service_type'])) {
            $order->service_type = $data['service_type'];
        }

        $PER_LOAD  = 180;
        $SURCHARGE = 40;

        $newLoadQty   = (int) $data['final_load_qty'];
        $oldLoadQty   = (int) ($order->load_qty ?? 0);

        $newOver8     = $request->boolean('exceeds_8kg');
        $oldOver8     = (bool) ($order->exceeds_8kg ?? false);

        $meta = (array) ($order->meta ?? []);

        // Normalize old buggy pickup fee values
        $pickupCharge = (int) ($order->pickup_delivery_charge ?? 0);
        if ($pickupCharge === 49) {
            $pickupCharge = 50;
        }

        // Best source of truth: original total before loyalty already saved in meta
        $storedBeforeLoyalty = data_get($meta, 'pricing.total_before_loyalty');

        if (is_numeric($storedBeforeLoyalty)) {
            $originalBeforeLoyalty = (float) $storedBeforeLoyalty;
        } else {
            // fallback for older records with no pricing meta
            $originalSubtotal = (float) ($order->subtotal ?? 0);
            $originalSurcharge = (float) ($order->surcharge ?? 0);
            $originalBeforeLoyalty = $pickupCharge + $originalSubtotal + $originalSurcharge;
        }

        $loyaltyDiscount = (int) data_get($meta, 'loyalty.discount_total', 0);

        // Only adjust what changed
        $deltaLoadQty = $newLoadQty - $oldLoadQty;
        $loadAdjustment = $deltaLoadQty * $PER_LOAD;

        $oldSurcharge = $oldOver8 ? $SURCHARGE : 0;
        $newSurcharge = $newOver8 ? $SURCHARGE : 0;
        $surchargeAdjustment = $newSurcharge - $oldSurcharge;

        $newBeforeLoyalty = max($originalBeforeLoyalty + $loadAdjustment + $surchargeAdjustment, 0);
        $finalTotal = max($newBeforeLoyalty - $loyaltyDiscount, 0);

        // Rebuild subtotal from the new before-loyalty total
        $newSubtotal = max($newBeforeLoyalty - $pickupCharge - $newSurcharge, 0);

        $order->load_qty       = $newLoadQty;
        $order->exceeds_8kg    = $newOver8;
        $order->pickup_delivery_charge = $pickupCharge;
        $order->surcharge      = $newSurcharge;
        $order->subtotal       = $newSubtotal;
        $order->total          = $finalTotal;
        $order->total_amount   = $finalTotal;
        $order->display_total  = $finalTotal;

        $meta['pricing'] = [
            'per_load'             => $PER_LOAD,
            'old_load_qty'         => $oldLoadQty,
            'load_qty'             => $newLoadQty,
            'old_exceeds_8kg'      => $oldOver8,
            'exceeds_8kg'          => $newOver8,
            'pickup_delivery'      => $pickupCharge,
            'surcharge'            => $newSurcharge,
            'subtotal'             => $newSubtotal,
            'loyalty_discount'     => $loyaltyDiscount,
            'total_before_loyalty' => $newBeforeLoyalty,
            'total_after_loyalty'  => $finalTotal,
        ];

        $order->meta = $meta;
        $order->save();

        return redirect()
            ->route('orders.receipt.show', $order)
            ->with('success', 'Receipt details saved. You can now print or email the receipt.');
    }


    public function sendReceipt(Request $request, Order $order)
    {
        $order->loadMissing('user');

        $email = optional($order->user)->email
            ?? data_get($order, 'meta.email'); // fallback if you store email in meta

        if (!$email) {
            return response()->json([
                'ok'      => false,
                'message' => 'No email address on file for this customer.',
            ], 422);
        }

        Mail::to($email)->send(new OrderReceipt($order));

        return response()->json([
            'ok'      => true,
            'message' => 'Receipt sent to ' . $email,
        ]);
    }


    public function showReceipt(Order $order)
    {
        // Authorization: Only the order owner (user) or any admin can view
        if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Optional: block access if receipt not finalized yet
        if (is_null($order->display_total)) {
            $redirectRoute = auth()->user()->role === 'admin'
                ? 'admin.dashboard'
                : 'user.dashboard';

            return redirect()
                ->route($redirectRoute)
                ->with('fail', 'Final receipt has not been created yet for this order.');
        }

        return view('admin.orders.receipt', [
            'order' => $order,
        ]);
    }

    public function hide(Order $order)
{
    $order->hidden_from_history = true;
    $order->save();

    return back()->with('success', 'Order hidden from history.');
}

}
