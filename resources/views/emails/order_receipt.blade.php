<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Receipt #{{ $order->id }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">

    <style>
        :root {
            --receipt-bg: #FFB5FE;
            /* match order summary bg */
            --paper: #ffffff;
            /* “receipt paper” */
            --ink: #111827;
            /* dark text */
            --muted: #6b7280;
            /* grey labels */
            --border: #e5e7eb;
            /* light border */
            --accent: #173F7B;
            /* E&B brand */
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px 0;
            background: var(--receipt-bg);
            font-family: "Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--ink);
        }

        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 16px;
            position: relative;
            z-index: 0;
        }

        .receipt-card {
            background: var(--paper);
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
            padding: 20px 18px;
            position: relative;
            overflow: hidden;
        }

        /* little “tear” line at top to feel like real receipt */
        .receipt-card::before {
            content: "";
            position: absolute;
            left: 16px;
            right: 16px;
            top: 0;
            height: 8px;
            border-bottom: 1px dashed #d1d5db;
        }

        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 8px;
        }

        .receipt-header-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent);
            margin: 0;
        }

        .receipt-sub {
            font-size: 13px;
            color: var(--muted);
            margin: 2px 0 0;
        }

        .receipt-logo {
            text-align: right;
            font-size: 12px;
            color: var(--muted);
        }

        .receipt-logo img {
            max-height: 40px;
            width: auto;
            display: block;
            margin-left: auto;
            margin-bottom: 4px;
        }

        .block-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--accent);
            margin: 18px 0 6px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            padding: 2px 0;
        }

        .info-row .label {
            color: var(--muted);
            min-width: 110px;
            font-weight: 500;
        }

        .info-row .value {
            text-align: right;
            flex: 1;
            font-weight: 600;
        }

        .divider {
            border-top: 1px dashed var(--border);
            margin: 12px 0;
        }

        /* items table */
        .table-sm th,
        .table-sm td {
            padding: .3rem .4rem;
            font-size: 13px;
            vertical-align: middle;
        }

        .totals-box {
            max-width: 260px;
            margin-left: auto;
            font-size: 13px;
        }

        .totals-box .line {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .totals-box .line.label {
            color: var(--muted);
        }

        .totals-box .line.total {
            border-top: 1px solid var(--border);
            margin-top: 4px;
            padding-top: 6px;
            font-weight: 700;
            font-size: 14px;
        }

        .footer-note {
            margin-top: 16px;
            font-size: 11px;
            color: var(--muted);
            text-align: center;
        }

        .actions-bottom {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 12px;
        }

        .btn-download {
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--accent);
            font-weight: 600;
        }

        .btn-download:hover {
            background: var(--accent);
            color: #fff;
        }

        @media (max-width:576px) {
            body {
                padding: 16px 0;
            }

            .receipt-card {
                padding: 16px 14px;
            }

            .receipt-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .receipt-header-title {
                font-size: 20px;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-row .value {
                text-align: left;
            }

            .actions-bottom {
                justify-content: space-between;
            }
        }

        /* Order summary style background (blurred store photo + pink overlay) */
        .bg-blur::before {
            content: "";
            position: fixed;
            inset: 0;
            background: var(--page-bg) center / cover no-repeat;
            filter: blur(10px);
            transform: scale(1.06);
            z-index: -2;
        }

        .bg-blur::after {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(255, 181, 254, .25);
            z-index: -1;
        }

        /* Print: only show the paper, hide buttons, remove outer bg */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0;
            }

            .receipt-container {
                max-width: none;
                padding: 0;
                margin: 0;
            }

            .actions-bottom {
                display: none !important;
            }

            .receipt-card {
                box-shadow: none;
                border-radius: 0;
                border: none;
            }

            .bg-blur::before,
            .bg-blur::after {
                display: none !important;
            }
        }

        .btn-send {
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid #173F7B;
            background: #173F7B;
            color: #fff;
            font-weight: 600;
        }

        .btn-send:hover {
            background: #122c55;
            border-color: #122c55;
            color: #fff;
        }

        .loyalty-pill {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #FFE5FF;
            color: #173F7B;
            font-size: 12px;
            font-weight: 500;
        }

        .loyalty-pill-badge {
            background: #173F7B;
            color: #FFE5FF;
            padding: 2px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 11px;
        }

        .loyalty-pill-amount {
            font-size: 12px;
            opacity: 0.85;
        }
    </style>
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="receipt-container">

        <div class="receipt-card">
            @php
                $tz = config('app.timezone', 'Asia/Manila');
                $when = optional($order->created_at)->timezone($tz);
                $isWalkin = ($order->category ?? '') === 'walkin';
            @endphp

            <div class="receipt-header">
                <div>
                    <h1 class="receipt-header-title">Order Receipt</h1>
                    <p class="receipt-sub mb-0">
                        Order #{{ $order->id }} •
                        {{ $when ? $when->format('M d, Y h:i A') : '—' }}
                    </p>
                    <p class="receipt-sub mb-0">
                        Category: {{ $isWalkin ? 'Walk-in' : 'Pickup & Delivery' }}
                    </p>
                </div>
                <div class="receipt-logo">
                    @if (file_exists(public_path('images/E&B_Logo.png')))
                        <img src="{{ asset('images/E&B_Logo.png') }}" alt="E&B Laundry Hub">
                    @endif
                    <div>E&amp;B Laundry Hub</div>
                </div>
            </div>

            {{-- CUSTOMER BLOCK --}}
            <div class="divider"></div>
            <div class="block-title">Customer Information</div>

            @if (!$isWalkin && $order->user)
                <div class="info-row">
                    <span class="label">Name</span>
                    <span class="value">{{ $order->user->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email</span>
                    <span class="value">{{ $order->user->email ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Phone</span>
                    <span class="value">{{ $order->user->phone_number ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Address</span>
                    <span class="value">{{ $order->user->address ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Location Details</span>
                    <span class="value">{{ $order->user->location_details ?? '—' }}</span>
                </div>
            @else
                <div class="info-row">
                    <span class="label">Name</span>
                    <span class="value">{{ $order->pickup_name ?? 'Walk-in / —' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Phone</span>
                    <span class="value">{{ $order->pickup_phone ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Address</span>
                    <span class="value">{{ $order->pickup_address ?? '—' }}</span>
                </div>
            @endif

            {{-- ORDER DETAILS --}}
            <div class="divider"></div>
            <div class="block-title">Order Details</div>

            <div class="info-row">
                <span class="label">Pickup</span>
                <span class="value">
                    @if ($order->pickup_date)
                        {{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}
                    @else
                        —
                    @endif
                    @if (!empty($order->pickup_time))
                        • {{ $order->pickup_time }}
                    @endif
                </span>
            </div>

            @if (!empty($order->delivery_date) || !empty($order->delivery_time))
                <div class="info-row">
                    <span class="label">Delivery</span>
                    <span class="value">
                        @if ($order->delivery_date)
                            {{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}
                        @else
                            —
                        @endif
                        @if (!empty($order->delivery_time))
                            • {{ $order->delivery_time }}
                        @endif
                    </span>
                </div>
            @endif

            <div class="info-row">
                <span class="label">Service</span>
                <span class="value">{{ $order->service_type ?? 'Full Service' }}</span>
            </div>

            <div class="info-row">
                <span class="label">Payment Method</span>
                <span class="value">{{ strtoupper($order->payment_method ?? 'CASH') }}</span>
            </div>

            @php
                $loadQty = (int) ($order->load_qty ?? 0);
                $over8 = (bool) ($order->exceeds_8kg ?? false);
            @endphp
            <div class="info-row">
                <span class="label">Load Quantity</span>
                <span class="value">
                    {{ $loadQty }} load{{ $loadQty === 1 ? '' : 's' }}
                    @if ($over8)
                        8kg
                    @endif
                </span>
            </div>

            {{-- PAYMENT SUMMARY (ITEMS) --}}
            @if ($order->relationLoaded('items') && $order->items->count())
                <div class="divider"></div>
                <div class="block-title">Payment Summary</div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-2">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $it)
                                <tr>
                                    <td>{{ $it->description }}</td>
                                    <td class="text-end">{{ $it->qty }}</td>
                                    <td class="text-end">
                                        ₱ {{ number_format((int) ($it->unit_price ?? 0), 0) }}
                                    </td>
                                    <td class="text-end">
                                        ₱ {{ number_format((int) ($it->line_total ?? 0), 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- TOTALS --}}







            <div class="divider"></div>
            @php
                $pickupCharge = (int) ($order->pickup_delivery_charge ?? 0);
                $subtotal = (int) ($order->subtotal ?? 0);
                $surcharge = (int) ($order->surcharge ?? 0);

                // If load exceeded 8kg but surcharge wasn't stored, show ₱40 by default
if (!$surcharge && !empty($order->exceeds_8kg)) {
    $surcharge = 40;
}

$total = (int) ($order->total ?? ($order->total_amount ?? 0));

// Loyalty info saved in orders.meta by confirm()
$loyaltyDiscount = (int) data_get($order->meta, 'loyalty.discount_total', 0);
$loyaltyClaimsApplied = (int) data_get($order->meta, 'loyalty.claims_applied', 0);
                $hasLoyalty = $loyaltyDiscount > 0 && $loyaltyClaimsApplied > 0;
            @endphp

            <div class="totals-box">
                <div class="line">
                    <span>Pickup &amp; Delivery</span>
                    <span>₱ {{ number_format($pickupCharge, 0) }}</span>
                </div>
                <div class="line">
                    <span>Subtotal</span>
                    <span>₱ {{ number_format($subtotal, 0) }}</span>
                </div>
                <div class="line">
                    <span>Surcharge</span>
                    <span>₱ {{ number_format($surcharge, 0) }}</span>
                </div>

                @if ($hasLoyalty)
                    <div class="line">
                        <span>Loyalty Discount ({{ $loyaltyClaimsApplied }} free load)</span>
                        <span>-₱ {{ number_format($loyaltyDiscount, 0) }}</span>
                    </div>
                @endif

                <div class="line total">
                    <span>Total</span>
                    <span>₱ {{ number_format($total, 0) }}</span>
                </div>
            </div>
            <div class="footer-note">
                This receipt is system-generated and valid as proof of transaction.<br>
                Thank you for trusting E&amp;B Laundry Hub.
            </div>
        </div>

        @if ($hasLoyalty)
            <div class="loyalty-pill">
                <span class="loyalty-pill-badge">{{ $loyaltyClaimsApplied }}x</span>
                <span>Free load loyalty claim applied</span>
                <span class="loyalty-pill-amount">(-₱ {{ number_format($loyaltyDiscount, 0) }})</span>
            </div>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <p>Pay with GCash: 09091753528</p>

</body>

</html>
