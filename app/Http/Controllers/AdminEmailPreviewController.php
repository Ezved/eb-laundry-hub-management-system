<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminEmailPreviewController extends Controller
{
  public function orderConfirmation(Request $request)
  {
    // Try a specific order (?order_id=123), else latest if any, else a safe dummy
    $order = null;
    if ($request->filled('order_id')) {
      $order = Order::with(['items', 'user'])->find($request->integer('order_id'));
    }
    if (!$order) {
      $order = Order::with(['items', 'user'])->latest()->first();
    }

    if (!$order) {
      // Dummy unsaved order for empty DB preview
      $order = new Order([
        'id'               => 0,
        'payment_method'   => 'cod',
        'total'            => 360,
        'pickup_date'      => Carbon::today()->toDateString(),
        'pickup_time'      => '9:00 AM - 12:00 PM',
        'delivery_date'    => Carbon::today()->addDays(2)->toDateString(),
        'delivery_time'    => '1:00 PM - 5:00 PM',
      ]);
    }

    $customerName = optional($order->user)->name ?? ($order->pickup_name ?: 'Customer');
    $pickupDate   = $order->pickup_date ? Carbon::parse($order->pickup_date)->format('Y-m-d') : '—';
    $pickupTime   = $order->pickup_time ?: '—';
    $deliveryDate = $order->delivery_date ? Carbon::parse($order->delivery_date)->format('Y-m-d') : '—';
    $deliveryTime = $order->delivery_time ?: '—';

    // Reuse your email blade as a web preview
    return view('emails.order-confirmed', [
      'order'        => $order,
      'customerName' => $customerName,
      'pickupDate'   => $pickupDate,
      'pickupTime'   => $pickupTime,
      'deliveryDate' => $deliveryDate,
      'deliveryTime' => $deliveryTime,
    ]);
  }
}