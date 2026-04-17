<div class="content">
    <h2>Hello {{ $customerName }},</h2>
    <p><strong>Good news!</strong> Your laundry is <strong>now out for delivery</strong>.</p>
    @if (!empty($messageText))
        <p>{{ $messageText }}</p>
    @endif

    <div class="details">
        <p><strong>Order No:</strong> #{{ $order->id }}</p>
        <p><strong>Delivery Date:</strong> {{ optional($order->delivery_date)->format('Y-m-d') ?? '—' }}</p>
        <p><strong>Delivery Time:</strong> {{ $order->delivery_time ?? '—' }}</p>
        <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount ?? ($order->total ?? 0), 2) }}</p>
        <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? '—') }}</p>
    </div>

    <p style="margin-top:20px">
        Thanks for choosing <span class="highlight">E&B Laundry Hub</span>!
    </p>
</div>
