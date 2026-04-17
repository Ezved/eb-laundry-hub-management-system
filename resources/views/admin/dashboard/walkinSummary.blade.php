<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Walk-in Summary</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard/walkinSummary.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container wrap">
        <div class="card">
            <div class="summary-header">
                <h1>Walk-in Summary</h1>
                <p>Review the walk-in customer details before confirming.</p>
            </div>

            <div class="summary-section">
                <div class="section-title">Walk-in Details</div>

                <div class="info-list">
                    <div class="info-row">
                        <span class="label">Name</span>
                        <span class="value">{{ $customer['name'] }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Mobile No.</span>
                        <span class="value">{{ $customer['mobile'] }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Address</span>
                        <span class="value">{{ $customer['address'] ?: '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Order Date &amp; Time</span>
                        <span class="value">{{ $lines['datetime'] }}</span>
                    </div>
                </div>
            </div>

            <div class="order-box">
                <div class="section-title">Walk-in Order</div>

                <div class="info-list">
                    <div class="info-row">
                        <span class="label">Service</span>
                        <span class="value">{{ $lines['service'] }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Load Quantity</span>
                        <span class="value">{{ $lines['load_qty'] }}</span>
                    </div>

                    @if(!empty($lines['uom']))
                        <div class="info-row">
                            <span class="label">UOM</span>
                            <span class="value">{{ $lines['uom'] }}</span>
                        </div>
                    @endif

                    <div class="info-row">
                        <span class="label">Exceeds 8kg</span>
                        <span class="value">{{ $lines['exceeds'] ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Comforters</span>
                        <span class="value">{{ $lines['comforter'] }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Add-Ons</span>
                        <span class="value">{{ $lines['addons'] }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Special Instructions</span>
                        <span class="value">{{ $lines['special_instructions'] ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="label">Payment</span>
                        <span class="value">{{ $lines['payment'] }}</span>
                    </div>

                    @if(isset($lines['gcash_reference']) && !empty($lines['gcash_reference']))
                        <div class="info-row">
                            <span class="label">GCash Reference No.</span>
                            <span class="value">{{ $lines['gcash_reference'] }}</span>
                        </div>
                    @endif

                    <div class="info-row total-row">
                        <span class="label">Total Amount</span>
                        <span class="value amount">₱ {{ number_format((float) $sum['total'], 2) }}</span>
                    </div>
                </div>
            </div>

            @if (!isset($readonly) || !$readonly)
                <form action="{{ route('admin.walkin.store') }}" method="POST" class="mt-3">
                    @csrf

                    @if(isset($lines['gcash_reference']))
                        <input type="hidden" name="gcash_reference" value="{{ $lines['gcash_reference'] }}">
                    @endif

                    @if(isset($lines['uom']))
                        <input type="hidden" name="uom" value="{{ $lines['uom'] }}">
                    @endif

                    <div class="actions">
                        <a href="{{ route('admin.walkin.create') }}" class="btn btn-back">Back</a>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            @else
                <div class="actions mt-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-back">Back to Dashboard</a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>