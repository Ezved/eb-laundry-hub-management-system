<h2>Hello {{ $customerName }},</h2>
<p>We're happy to let you know that your order has been successfully <strong>confirmed!</strong></p>
<p>Below are your pickup and delivery details:</p>

<div class="details">
  <p><strong>Order No:</strong> #{{ $order->id }}</p>
  <p><strong>Pickup Date:</strong> {{ $pickupDate }}</p>
  <p><strong>Pickup Time:</strong> {{ $pickupTime }}</p>
  <p><strong>Delivery Date:</strong> {{ $deliveryDate }}</p>
  <p><strong>Delivery Time:</strong> {{ $deliveryTime }}</p>
  <p><strong>Total Amount:</strong>
    ₱{{ number_format(($order->display_total ?? $order->total ?? $order->total_amount ?? 0), 2) }}</p>
  <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? '—') }}</p>
</div>

<p>We'll notify you once your laundry is ready for delivery.
  Thank you for choosing <span class="highlight">E&amp;B Laundry Hub</span> — we appreciate your trust!</p>

@isset($actionUrl)
  <a href="{{ $actionUrl }}" class="btn">View Receipt</a>
@else
  <a href="{{ route('orders.show', $order) }}" class="btn">View My Order</a>
@endisset
