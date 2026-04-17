<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserNotification;

class AdminNotificationController extends Controller
{
    /**
     * POST /admin/calendar/notify
     * Body: { order_id, message }
     */
    public function notify(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'message'  => 'nullable|string|max:1000',
        ]);

        $order = Order::with('user')->findOrFail($data['order_id']);
        if (!$order->user_id) {
            return response()->json(['ok' => false, 'message' => 'No user is linked to this order.'], 422);
        }

        // In your Admin notify controller method:
UserNotification::create([
    'user_id'   => $order->user_id,
    'order_id'  => $order->id,
    'type'      => 'for_delivery',         // or whatever you use
    'title'     => 'Your laundry is for delivery',
    'message'   => $request->input('message') ?: 'Heads up! Your laundry is now on the way.',
    // 🔗 simple, real path that exists in your app:
    'deep_link' => '/dashboard?order=' . $order->id,
]);


        return response()->json(['ok' => true]);
    }
}
