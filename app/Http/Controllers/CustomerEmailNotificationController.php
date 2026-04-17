<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Order as OrderModel;
use App\Models\OrderItem;
use App\Mail\OrderConfirmedMail;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

class CustomerEmailNotificationController extends Controller
{
    // Base pricing reference (some values now overridden by DB/computed totals)
    // Base pricing reference (some values now overridden by DB/computed totals)
private array $prices = [
    'full_service'            => 180, // fallback only; DB overrides it
    'per_load_kg_cap'         => 7,
    'surcharge_threshold'     => 8.0,
    'surcharge'               => 40,
    'pickup_delivery'         => 50,  // pickup + delivery
    'pickup_only'             => 25,  // pickup only (no drop-off)
    'comforter_single_double' => 200,
    'comforter_queen'         => 220,
    'comforter_king'          => 230,
    'addon_spin'              => 20,
    'addon_dry'               => 20,
    'addon_liquid_detergent'  => 20,
    'addon_fabric_conditioner'=> 20,
    'addon_color_safe'        => 8,
];


    /**
     * Finalize order + send confirmation email to the customer.
     * Mirrors the previous OrderController@confirm behavior.
     */
    public function confirm(Request $request)
    {
        $draft = session('order_draft');
        if (!$draft) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Order draft not found.'], 400);
            }
            abort(400, 'Order draft not found.');
        }

        $payload  = $draft['payload'];
        $computed = $draft['computed'];

        // Optional map: service label -> id (if your schema has service_id)
        $service = Service::where('title', $payload['service_type'])->first();
        // Decide pickup fee: ₱50 (pickup + delivery) vs ₱25 (pickup only)
$selfPickup  = !empty($payload['self_pickup']);
$hasDelivery = !empty($payload['delivery_date']) && ! $selfPickup;

// Use pickup_delivery from the computed summary when available
$pickupDeliveryComputed = (int) ($computed['pickup_delivery'] ?? 0);
if ($pickupDeliveryComputed <= 0) {
    $pickupDeliveryComputed = $hasDelivery
        ? ($this->prices['pickup_delivery'] ?? 50)
        : ($this->prices['pickup_only'] ?? 25);
}
// normalize any old 49 values to ₱50
if ($pickupDeliveryComputed === 49) {
    $pickupDeliveryComputed = $this->prices['pickup_delivery'] ?? 50;
}

        // 🔹 Determine the per-load price from the Services table (dynamic)
        $servicePerLoadPrice = $service
            ? (float) $service->price
            : (float) $this->prices['full_service']; // fallback if service row missing

        // 🔹 Loyalty: free load value = current per-load price of chosen service
        $loyaltyClaimsForOrder   = (int) session('loyalty_claims_for_current_order', 0);
        $loyaltyDiscountPerClaim = $servicePerLoadPrice; // e.g. current Full Service price
        $loyaltyDiscount         = $loyaltyClaimsForOrder > 0
            ? $loyaltyClaimsForOrder * $loyaltyDiscountPerClaim
            : 0;

        $baseTotal  = (int) ($computed['total'] ?? 0);
        $finalTotal = max($baseTotal - $loyaltyDiscount, 0);

        // Use pickup_delivery from the computed summary (keeps everything in sync)
        $pickupDeliveryComputed = (int) ($computed['pickup_delivery'] ?? $this->prices['pickup_delivery']);

        // Rich meta (pricing + flags + original form values + loyalty info)
        $meta = [
            'pricing' => [
                'subtotal'             => $computed['subtotal']  ?? 0,
                'surcharge'            => $computed['surcharge'] ?? 0,
                'pickup_delivery'      => $pickupDeliveryComputed,
                'total_before_loyalty' => $baseTotal,
                'loyalty_discount'     => $loyaltyDiscount,
                'total_after_loyalty'  => $finalTotal,
            ],
            'flags' => [
                'exceeds_8kg' => (bool) ($payload['exceeds_8kg'] ?? false),
                'no_scale'    => (bool) ($payload['no_scale'] ?? false),
            ],
            'form' => [
                'service_label'           => $payload['service_type'],
                'special_instructions'    => $payload['special_instructions'] ?? null,
                'pickup_email'            => $request->input('pickup_email'),
                'pickup_location_details' => $request->input('pickup_location_details'),
            ],
            'loyalty' => [
                'used'               => $loyaltyClaimsForOrder > 0,
                'claims_applied'     => $loyaltyClaimsForOrder,
                'discount_per_claim' => $loyaltyDiscountPerClaim,
                'discount_total'     => $loyaltyDiscount,
            ],
        ];

        // Build safe payload (only set optional columns if they exist)
        $orderData = [
            'user_id'       => $draft['user_id'],
            'service_type'  => $payload['service_type'], // keep label for existing views
            'pickup_date'   => $payload['pickup_date'],
            'pickup_time'   => $payload['pickup_time'],
            'delivery_date' => $payload['delivery_date'] ?? null,
            'delivery_time' => $payload['delivery_time'] ?? null,

            // amounts (use computed pickup_delivery so it matches summary)
            'pickup_delivery_charge' => $pickupDeliveryComputed,
            'subtotal'               => $computed['subtotal']  ?? 0,
            'surcharge'              => $computed['surcharge'] ?? 0,
            'total'                  => $finalTotal,
            'total_amount'           => $finalTotal,

            // status
            'status'         => 'pending',
            'payment_status' => 'unpaid',

            // contact / address (fallbacks to logged-in user)
            'pickup_name'             => $request->input('pickup_name', optional(Auth::user())->name),
            'pickup_email'            => $request->input('pickup_email', optional(Auth::user())->email),
            'pickup_phone'            => $request->input('pickup_phone', optional(Auth::user())->phone_number),
            'pickup_address'          => $request->input('pickup_address', optional(Auth::user())->address),
            'pickup_location_details' => $request->input('pickup_location_details', optional(Auth::user())->location_details),

            // misc
            'load_qty'             => $payload['load_qty'] ?? 0,
            'special_instructions' => $payload['special_instructions'] ?? null,
            'payment_method'       => $payload['payment_method'],
            'meta'                 => $meta,
        ];

        // Guarded/optional columns
        if (Schema::hasColumn('orders', 'category')) {
            $orderData['category'] = 'pickup_delivery';
        }
        if (Schema::hasColumn('orders', 'service_id')) {
            $orderData['service_id'] = $service?->id;
        }
        if (Schema::hasColumn('orders', 'exceeds_8kg')) {
            $orderData['exceeds_8kg'] = (bool) ($payload['exceeds_8kg'] ?? false);
        }
        if (Schema::hasColumn('orders', 'no_scale')) {
            $orderData['no_scale'] = (bool) ($payload['no_scale'] ?? false);
        }

        $order = OrderModel::create($orderData);

        // Items (already computed in OrderSummaryController using Service prices)
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

        // Clear draft + one-time loyalty flag (so next order is normal)
        session()->forget('order_draft');
        session()->forget('loyalty_claims_for_current_order');

        // Email: prefer logged-in user's email, then pickup_email, then meta.form.pickup_email
        try {
            $recipient = optional(Auth::user())->email
                ?: $order->pickup_email
                ?: data_get($order->meta, 'form.pickup_email');

            if ($recipient) {
                Mail::to($recipient)->send(new OrderConfirmedMail($order));
                \Log::info('Order mail sent', ['order_id' => $order->id, 'to' => $recipient]);
            } else {
                \Log::warning('Order mail skipped: no recipient', ['order_id' => $order->id]);
            }
        } catch (\Throwable $e) {
            \Log::error('Order mail failed: ' . $e->getMessage(), [
                'order_id'  => $order->id,
                'exception' => $e,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'       => true,
                'order_id' => $order->id,
                'total'    => $order->total_amount,
                'redirect' => route('user.dashboard'),
                'message'  => 'Order received. Thank you!',
            ]);
        }

        return redirect()->route('user.dashboard')->with('success', 'Order received. Thank you!');
    }
}
