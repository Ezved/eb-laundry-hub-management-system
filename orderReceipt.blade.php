<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order #{{ $order->id }} • Receipt</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
  <div class="container">
    <h1>Order #{{ $order->id }}</h1>
    <p class="text-muted">Placed {{ $order->created_at->format('M d, Y h:i A') }}</p>

    <div class="mb-3">
      <strong>Pickup:</strong>
      {{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }} • {{ $order->pickup_time }}
    </div>

    <div class="mb-3"><strong>Service:</strong> {{ $order->service_type }}</div>
    <div class="mb-3"><strong>Load Quantity:</strong> {{ $order->load_qty }}</div>

    <div class="mb-3"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</div>
    <div class="mb-3">
      <strong>Payment:</strong>
      <span class="badge {{ ($order->payment_status ?? 'unpaid') === 'paid' ? 'bg-success' : 'bg-secondary' }}">
        {{ ($order->payment_status ?? 'unpaid') === 'paid' ? 'Paid' : 'Not Paid' }}
      </span>
    </div>

    <div class="mb-3">
      <strong>Status:</strong>
      {{ str_replace('_',' ', ucfirst($order->status)) }}
    </div>

    <hr>
    <div class="mb-1"><strong>Pickup & Delivery:</strong> ₱ {{ number_format($order->pickup_delivery_charge,0) }}</div>
    <div class="mb-1"><strong>Subtotal:</strong> ₱ {{ number_format($order->subtotal,0) }}</div>
    <div class="mb-1"><strong>Surcharge:</strong> ₱ {{ number_format($order->surcharge,0) }}</div>
    <div class="mb-3"><strong>Total:</strong> ₱ {{ number_format($order->total,0) }}</div>

    {{-- If items relationship exists --}}
    @if ($order->relationLoaded('items') && $order->items->count())
      <h5>Details</h5>
      <table class="table table-sm">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Line Total</th></tr></thead>
        <tbody>
          @foreach ($order->items as $it)
            <tr>
              <td>{{ $it->description }}</td>
              <td>{{ $it->qty }}</td>
              <td>₱ {{ number_format($it->unit_price,0) }}</td>
              <td>₱ {{ number_format($it->line_total,0) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">Back to Dashboard</a>
  </div>
</body>
</html>
