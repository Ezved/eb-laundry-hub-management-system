<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class AdminCalendarController extends Controller
{
    /**
     * JSON feed for the Delivery Calendar.
     * - Emits separate events for pickup and delivery (when dates exist)
     * - 3 months back, 6 months forward window to keep payload reasonable
     * - EXCLUDES walk-in orders (category = 'walkin')
     */
    public function events(Request $request)
    {
        $from = Carbon::now()->subMonths(3)->startOfDay();
        $to   = Carbon::now()->addMonths(6)->endOfDay();

        $orders = Order::query()
            ->select([
                'id',
                'user_id',
                'category',
                'status',
                'pickup_name',
                'pickup_date',
                'delivery_date',
                'created_at',
            ])
            // 🔴 NEW: exclude walk-in orders from the calendar
            ->where(function ($q) {
                $q->whereNull('category')
                  ->orWhere('category', '!=', 'walkin');
            })
            ->latest('id')
            ->limit(1000)
            ->get();

        $events = [];

        foreach ($orders as $o) {
            // Normalize date fields (string|Carbon|null)
            // fallback is now only for non-walkins (walkins are filtered above)
            $pickupRaw   = $o->pickup_date ?: $o->created_at;
            $deliveryRaw = $o->delivery_date; // may be null

            $pickupDate   = $pickupRaw   ? Carbon::parse($pickupRaw)   : null;
            $deliveryDate = $deliveryRaw ? Carbon::parse($deliveryRaw) : null;

            // PICKUP event
            if ($pickupDate && $pickupDate->betweenIncluded($from, $to)) {
                $events[] = [
                    'id'       => "P{$o->id}", // string IDs avoid clashes with delivery
                    'date'     => $pickupDate->toDateString(),
                    'type'     => 'pickup',
                    'label'    => 'Pickup • Order #' . $o->id . ' • ' . ($o->pickup_name ?: optional($o->user)->name ?: 'Customer'),
                    'order_id' => $o->id,
                    'user_id'  => $o->user_id,
                ];
            }

            // DELIVERY event (only if a delivery date exists)
            if ($deliveryDate && $deliveryDate->betweenIncluded($from, $to)) {
                $events[] = [
                    'id'       => "D{$o->id}",
                    'date'     => $deliveryDate->toDateString(),
                    'type'     => 'delivery',
                    'label'    => 'Delivery • Order #' . $o->id . ' • ' . (optional($o->user)->name ?: $o->pickup_name ?: 'Customer'),
                    'order_id' => $o->id,
                    'user_id'  => $o->user_id,
                ];
            }
        }

        return response()->json($events);
    }

    /**
     * Notify a customer about a pickup or delivery.
     */
    public function notify(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required',
            'order_id' => 'required|integer|exists:orders,id',
            'user_id'  => 'nullable|integer|exists:users,id',
            'kind'     => 'nullable|in:pickup,delivery',
            'message'  => 'nullable|string|max:500',
        ]);

        $order = Order::with('user')->findOrFail($data['order_id']);
        $user  = $data['user_id'] ? User::find($data['user_id']) : $order->user;

        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'No customer to notify.'], 422);
        }

        $kind    = $data['kind'] ?? 'delivery';
        $message = $data['message'] ?? (
            $kind === 'pickup'
                ? 'Our rider is on the way to pick up your laundry.'
                : 'Your delivery is now in progress.'
        );

        try {
            // Check if notification classes exist
            if ($kind === 'pickup' && class_exists(\App\Notifications\PickupOnTheWayNotification::class)) {
                $user->notify(new \App\Notifications\PickupOnTheWayNotification($order, $message));
            } elseif ($kind === 'delivery' && class_exists(\App\Notifications\DeliveryInProgressNotification::class)) {
                $user->notify(new \App\Notifications\DeliveryInProgressNotification($order, $message));
            } else {
                // Fallback: create in-app notification
                if (class_exists(\App\Models\UserNotification::class)) {
                    \App\Models\UserNotification::create([
                        'user_id'  => $user->id,
                        'order_id' => $order->id,
                        'title'    => $kind === 'pickup' ? 'Pickup on the way' : 'Delivery in progress',
                        'body'     => $message,
                    ]);
                }
            }

            return response()->json(['ok' => true, 'message' => 'Customer notified successfully.']);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'Failed to send notification: ' . $e->getMessage()], 500);
        }
    }

    public function deliveryCalendar()
{
    return view('admin.delivery-calendar');
}

public function salesReport()
{
    $salesDay = 0;
    $salesWeek = 0;
    $salesMonth = 0;

    return view('admin.sales-report', compact('salesDay', 'salesWeek', 'salesMonth'));
}
}
