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
    <p class="text-muted">
      Placed {{ optional($order->created_at)->timezone(config('app.timezone'))->format('M d, Y h:i A') }}
    </p>

    {{-- Customer (profile if linked user; else walk-in fields) --}}
    @if ($order->relationLoaded('user') ? $order->user : $order->user)
      <div class="mb-3">
        <h5>Customer</h5>
        <div><strong>Name:</strong> {{ $order->user->name }}</div>
        <div><strong>Email:</strong> {{ $order->user->email }}</div>
        <div><strong>Phone:</strong> {{ $order->user->phone_number ?? '—' }}</div>
        <div><strong>Address:</strong> {{ $order->user->address ?? '—' }}</div>
        <div><strong>Location Details:</strong> {{ $order->user->location_details ?? '—' }}</div>
      </div>
    @else
      <div class="mb-3">
        <h5>Customer</h5>
        <div><strong>Name:</strong> {{ $order->pickup_name ?? 'Walk-in / —' }}</div>
        <div><strong>Email:</strong> {{ $order->pickup_email ?? '—' }}</div>
        <div><strong>Phone:</strong> {{ $order->pickup_phone ?? '—' }}</div>
        <div><strong>Address:</strong> {{ $order->pickup_address ?? '—' }}</div>
        <div><strong>Location Details:</strong> {{ $order->pickup_location_details ?? '—' }}</div>
      </div>
    @endif

    <div class="mb-3">
      <strong>Pickup:</strong>
      {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') : '—' }}
      @if($order->pickup_time) • {{ $order->pickup_time }} @endif
    </div>

    <div class="mb-3"><strong>Service:</strong> {{ $order->service_type ?? '—' }}</div>
    <div class="mb-3"><strong>Load Quantity:</strong> {{ $order->load_qty ?? 0 }}</div>

    <div class="mb-3"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? 'COD') }}</div>

    <div class="mb-3">
      <strong>Payment:</strong>

      @php $paid = ($order->payment_status ?? 'unpaid') === 'paid'; @endphp
      <span class="badge {{ $paid ? 'bg-success' : 'bg-secondary' }}">
        {{ $paid ? 'Paid' : 'Not Paid' }}
      </span>


    </div>

    <div class="mb-3">
      <strong>Status:</strong>
      {{ $order->status ? str_replace('_',' ', ucfirst($order->status)) : '—' }}
    </div>

    <hr>

    {{-- Totals (defensive: show 0 if null) --}}
    <div class="mb-1"><strong>Pickup &amp; Delivery:</strong> ₱ {{ number_format((int)($order->pickup_delivery_charge ?? 0), 0) }}</div>
    <div class="mb-1"><strong>Subtotal:</strong> ₱ {{ number_format((int)($order->subtotal ?? 0), 0) }}</div>
    <div class="mb-1"><strong>Surcharge:</strong> ₱ {{ number_format((int)($order->surcharge ?? 0), 0) }}</div>
    <div class="mb-3"><strong>Total:</strong> ₱ ----</div>

    {{-- Line items if relationship is loaded --}}
    @if ($order->relationLoaded('items') && $order->items->count())
      <h5>Details</h5>
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Item</th><th class="text-end">Qty</th><th class="text-end">Price</th><th class="text-end">Line Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($order->items as $it)
            <tr>
              <td>{{ $it->description }}</td>
              <td class="text-end">{{ $it->qty }}</td>
              <td class="text-end">₱ {{ number_format((int)$it->unit_price, 0) }}</td>
              <td class="text-end">₱ {{ number_format((int)$it->line_total, 0) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">Back to Dashboard</a>
  </div>
</body>
</html>
