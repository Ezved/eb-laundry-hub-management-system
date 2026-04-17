<div class="content">
    <h2>Hello {{ $customerName }},</h2>
    @if (!empty($messageText))
        <p>{{ $messageText }}</p>
    @endif

    <div class="details">
        <p><strong>Order No:</strong> #{{ $order->id }}</p>
        <p><strong>Pickup Date:</strong> {{ optional($order->pickup_date)->format('Y-m-d') ?? '—' }}</p>
        <p><strong>Pickup Time:</strong> {{ $order->pickup_time ?? '—' }}</p>
        @php
            $addr = $order->pickup_address ?? (optional($order->user)->address ?? data_get($order, 'meta.address'));
        @endphp
        <p><strong>Pickup Address:</strong> {{ $addr ?? '—' }}</p>
        <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? '—') }}</p>
        <p><strong>Estimated Total:</strong> ₱ ----</p>
    </div>

    <p style="margin-top:20px">
        Thanks for choosing <span class="highlight">E&B Laundry Hub</span>!
    </p>
</div>
