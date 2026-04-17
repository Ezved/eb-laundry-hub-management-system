<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;

class UserNotificationController extends Controller
{
    // Return the latest unread notification for the logged-in user (JSON)
    public function poll(Request $request)
    {
        $n = UserNotification::where('user_id', $request->user()->id)
            ->unread()
            ->latest('id')
            ->first();

        if (!$n) return response()->json(['ok'=>true, 'notification'=>null]);

        return response()->json([
            'ok'=>true,
            'notification'=>[
                'id'    => $n->id,
                'title' => $n->title,
                'body'  => $n->body,
                'order_id' => $n->order_id,
            ]
        ]);
    }

    // Mark one as read
    public function markRead(Request $request, UserNotification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);
        return response()->json(['ok'=>true]);
    }
}
