<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Summary</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/orderSummary.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">

    <style>
        .actions{
            margin-top: 34px !important;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-dashboard-glow{
            background: var(--pink, #ff6ef7);
            border: 1px solid var(--pink, #ff6ef7);
            color: #fff !important;
            box-shadow: 0 12px 28px rgba(255, 110, 247, 0.28);
        }

        .btn-dashboard-glow:hover{
            background: #f954ea;
            border-color: #f954ea;
            color: #fff !important;
            transform: translateY(-1px);
            box-shadow: 0 16px 32px rgba(255, 110, 247, 0.34);
        }

        @media (max-width: 767.98px){
            .actions{
                margin-top: 28px !important;
            }
        }
    </style>
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    @php
        $payload  = old() ?: ($data ?? []);
        $lines    = $lines ?? [];
        $sum      = $sum ?? [];
        $readonly = $readonly ?? false;
        $customer = $customer ?? [];

        /*
        |--------------------------------------------------------------------------
        | Back button
        |--------------------------------------------------------------------------
        */
        $defaultBackUrl = route('user.dashboard');
        $defaultBackLabel = 'Back to User Dashboard';

        if (auth()->check() && strtolower((string) (auth()->user()->role ?? '')) === 'admin') {
            $defaultBackUrl = route('admin.dashboard');
            $defaultBackLabel = 'Back to Admin Dashboard';
        }

        $backHref = $readonly
            ? ($backUrl ?? $defaultBackUrl)
            : url()->previous();

        $backLabel = $readonly ? $defaultBackLabel : 'Back';

        /*
        |--------------------------------------------------------------------------
        | Customer details
        |--------------------------------------------------------------------------
        */
        $customerName = $customer['name'] ?? (auth()->user()->name ?? '—');
        $customerMobile = $customer['mobile'] ?? (auth()->user()->phone_number ?? '—');
        $customerEmail = $customer['email'] ?? (auth()->user()->email ?? '—');
        $customerAddress = $customer['address'] ?? (auth()->user()->address ?? '—');
        $customerLocation = $customer['location'] ?? (auth()->user()->location_details ?? '—');

        /*
        |--------------------------------------------------------------------------
        | Basic payload values
        |--------------------------------------------------------------------------
        */
        $serviceName = $payload['service_type'] ?? ($lines['service'] ?? 'Full Service');
        $loadQty = (int) ($payload['load_qty'] ?? ($lines['load_qty'] ?? 0));

        $pickupDate = $payload['pickup_date'] ?? '';
        $pickupTime = $payload['pickup_time'] ?? '';
        $deliveryDate = $payload['delivery_date'] ?? '';
        $deliveryTime = $payload['delivery_time'] ?? '';
        $specialInstructions = $payload['special_instructions'] ?? ($lines['special_instructions'] ?? '');
        $paymentMethod = strtolower((string) ($payload['payment_method'] ?? ($lines['payment'] ?? '')));
        $gcashReference = $payload['gcash_reference'] ?? '';

        $selfPickup = !empty($payload['self_pickup'])
            || (($lines['self_pickup'] ?? '') === 'Yes');

        /*
        |--------------------------------------------------------------------------
        | Parse readonly line text when qty fields are not present
        |--------------------------------------------------------------------------
        | In dashboard readonly view, controller sends:
        | - $lines['comforter'] like "2x Comforter (...), 1x Comforter (...)"
        | - $lines['addons'] like "1x Dry (10 mins), 2x Spin (11 mins)"
        |--------------------------------------------------------------------------
        */
        $comforterSingleDouble = (int) ($payload['comforter_single_double'] ?? 0);
        $comforterQueen = (int) ($payload['comforter_queen'] ?? 0);
        $comforterKing = (int) ($payload['comforter_king'] ?? 0);

        $addonSpin = (int) ($payload['addon_spin'] ?? 0);
        $addonDry = (int) ($payload['addon_dry'] ?? 0);
        $addonLiquidDetergent = (int) ($payload['addon_liquid_detergent'] ?? 0);
        $addonFabricConditioner = (int) ($payload['addon_fabric_conditioner'] ?? 0);
        $addonColorSafe = (int) ($payload['addon_color_safe'] ?? 0);

        if ($readonly) {
            $comforterText = strtolower((string) ($lines['comforter'] ?? ''));
            $addonsText = strtolower((string) ($lines['addons'] ?? ''));

            if ($comforterSingleDouble === 0 && $comforterQueen === 0 && $comforterKing === 0 && $comforterText !== '' && $comforterText !== '—') {
                preg_match_all('/(\d+)\s*x\s*([^,]+)/i', $lines['comforter'] ?? '', $comforterMatches, PREG_SET_ORDER);

                foreach ($comforterMatches as $match) {
                    $qty = (int) ($match[1] ?? 0);
                    $label = strtolower(trim((string) ($match[2] ?? '')));

                    if (str_contains($label, 'queen')) {
                        $comforterQueen += $qty;
                    } elseif (str_contains($label, 'king')) {
                        $comforterKing += $qty;
                    } else {
                        $comforterSingleDouble += $qty;
                    }
                }
            }

            if (
                $addonSpin === 0 &&
                $addonDry === 0 &&
                $addonLiquidDetergent === 0 &&
                $addonFabricConditioner === 0 &&
                $addonColorSafe === 0 &&
                $addonsText !== '' &&
                $addonsText !== '—'
            ) {
                preg_match_all('/(\d+)\s*x\s*([^,]+)/i', $lines['addons'] ?? '', $addonMatches, PREG_SET_ORDER);

                foreach ($addonMatches as $match) {
                    $qty = (int) ($match[1] ?? 0);
                    $label = strtolower(trim((string) ($match[2] ?? '')));

                    if (str_contains($label, 'spin')) {
                        $addonSpin += $qty;
                    } elseif (str_contains($label, 'dry')) {
                        $addonDry += $qty;
                    } elseif (str_contains($label, 'liquid detergent')) {
                        $addonLiquidDetergent += $qty;
                    } elseif (str_contains($label, 'fabric conditioner')) {
                        $addonFabricConditioner += $qty;
                    } elseif (str_contains($label, 'color safe')) {
                        $addonColorSafe += $qty;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Prices
        |--------------------------------------------------------------------------
        */
        $comforterSingleDoublePrice = 200.00;
        $comforterQueenPrice = 220.00;
        $comforterKingPrice = 230.00;

        $addonSpinPrice = 20.00;
        $addonDryPrice = 20.00;
        $addonLiquidDetergentPrice = 20.00;
        $addonFabricConditionerPrice = 20.00;
        $addonColorSafePrice = 8.00;

        $comforterSubtotal =
            ($comforterSingleDouble * $comforterSingleDoublePrice) +
            ($comforterQueen * $comforterQueenPrice) +
            ($comforterKing * $comforterKingPrice);

        $addonSubtotal =
            ($addonSpin * $addonSpinPrice) +
            ($addonDry * $addonDryPrice) +
            ($addonLiquidDetergent * $addonLiquidDetergentPrice) +
            ($addonFabricConditioner * $addonFabricConditionerPrice) +
            ($addonColorSafe * $addonColorSafePrice);

        /*
        |--------------------------------------------------------------------------
        | Pickup / Delivery charge
        |--------------------------------------------------------------------------
        */
        $pickupCharge = (float) ($sum['pickup_delivery'] ?? 0);

        if ($pickupCharge <= 0) {
            $pickupChargeRaw = $payload['pickup_delivery_charge'] ?? null;

            if ($pickupChargeRaw !== null && $pickupChargeRaw !== '') {
                $pickupCharge = (float) preg_replace('/[^\d.]/', '', (string) $pickupChargeRaw);
            } else {
                $pickupCharge = $selfPickup ? 25.00 : 50.00;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        | Booking flow has controller-computed subtotal/total.
        | Readonly flow often only has total + pickup_delivery, so derive the rest.
        |--------------------------------------------------------------------------
        */
        $grandTotal = (float) ($sum['total'] ?? 0);
        $subtotalFromController = (float) ($sum['subtotal'] ?? 0);

        if ($subtotalFromController > 0) {
            $serviceSubtotal = $subtotalFromController - $comforterSubtotal - $addonSubtotal;
        } elseif ($grandTotal > 0) {
            $serviceSubtotal = $grandTotal - $pickupCharge - $comforterSubtotal - $addonSubtotal;
        } else {
            $serviceSubtotal = 0;
        }

        if ($serviceSubtotal < 0) {
            $serviceSubtotal = 0;
        }

        $servicePrice = $loadQty > 0 ? ($serviceSubtotal / $loadQty) : 0;

        if ($grandTotal <= 0) {
            $grandTotal = $serviceSubtotal + $comforterSubtotal + $addonSubtotal + $pickupCharge;
        }
    @endphp

    <div class="wrap">
        <div class="summary-card">
            <div class="summary-header">
                <h1>Order Summary</h1>
                <p>Please review your booking details before confirming your order.</p>
            </div>

            <div class="summary-body">
                <div class="summary-grid">
                    <section class="summary-panel">
                        <div class="panel-head">
                            <h2>Customer Details</h2>
                        </div>

                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-label">Full Name</div>
                                <div class="detail-value">{{ $customerName }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Mobile Number</div>
                                <div class="detail-value">{{ $customerMobile }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Email</div>
                                <div class="detail-value">{{ $customerEmail }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Address</div>
                                <div class="detail-value">{{ $customerAddress }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Location Details</div>
                                <div class="detail-value">{{ $customerLocation }}</div>
                            </div>
                        </div>
                    </section>

                    <section class="summary-panel">
                        <div class="panel-head">
                            <h2>Schedule Details</h2>
                        </div>

                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-label">Pickup Date</div>
                                <div class="detail-value">{{ $pickupDate ?: 'Not set' }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Pickup Time</div>
                                <div class="detail-value">{{ $pickupTime ?: 'Not set' }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Delivery Date</div>
                                <div class="detail-value">{{ $selfPickup ? 'Self Pickup' : ($deliveryDate ?: 'Not set') }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Delivery Time</div>
                                <div class="detail-value">{{ $selfPickup ? 'Self Pickup' : ($deliveryTime ?: 'Not set') }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Special Instructions</div>
                                <div class="detail-value">{{ $specialInstructions ?: 'None' }}</div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="summary-grid">
                    <section class="summary-panel">
                        <div class="panel-head">
                            <h2>Service Details</h2>
                        </div>

                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-label">Service Type</div>
                                <div class="detail-value">{{ $serviceName }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Load Quantity</div>
                                <div class="detail-value">{{ $loadQty }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Price per Load</div>
                                <div class="detail-value">
                                    <span class="peso">₱</span> {{ number_format((float) $servicePrice, 2) }}
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Service Subtotal</div>
                                <div class="detail-value strong">
                                    <span class="peso">₱</span> {{ number_format((float) $serviceSubtotal, 2) }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="summary-panel">
                        <div class="panel-head">
                            <h2>Payment Details</h2>
                        </div>

                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-label">Pickup / Delivery Charge</div>
                                <div class="detail-value">
                                    <span class="peso">₱</span> {{ number_format((float) $pickupCharge, 2) }}
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Payment Method</div>
                                <div class="detail-value">
                                    @if ($paymentMethod === 'gcash')
                                        GCash
                                    @elseif ($paymentMethod === 'cod')
                                        Cash On Delivery
                                    @elseif (!empty($lines['payment']))
                                        {{ $lines['payment'] }}
                                    @else
                                        Not set
                                    @endif
                                </div>
                            </div>

                            @if ($paymentMethod === 'gcash' && !empty($gcashReference))
                                <div class="detail-row">
                                    <div class="detail-label">GCash Reference</div>
                                    <div class="detail-value">{{ $gcashReference }}</div>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>

                <section class="summary-panel summary-panel-wide">
                    <div class="panel-head">
                        <h2>Optional Items</h2>
                    </div>

                    <div class="summary-grid summary-grid-optional">
                        <div class="mini-panel">
                            <div class="mini-panel-title">Comforters</div>

                            <div class="detail-list compact">
                                <div class="detail-row">
                                    <div class="detail-label">Single / Double</div>
                                    <div class="detail-value">{{ $comforterSingleDouble }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Queen</div>
                                    <div class="detail-value">{{ $comforterQueen }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">King</div>
                                    <div class="detail-value">{{ $comforterKing }}</div>
                                </div>
                                <div class="detail-row subtotal-row">
                                    <div class="detail-label">Subtotal</div>
                                    <div class="detail-value strong">
                                        <span class="peso">₱</span> {{ number_format((float) $comforterSubtotal, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mini-panel">
                            <div class="mini-panel-title">Add-ons</div>

                            <div class="detail-list compact">
                                <div class="detail-row">
                                    <div class="detail-label">Spin</div>
                                    <div class="detail-value">{{ $addonSpin }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Dry</div>
                                    <div class="detail-value">{{ $addonDry }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Liquid Detergent</div>
                                    <div class="detail-value">{{ $addonLiquidDetergent }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Fabric Conditioner</div>
                                    <div class="detail-value">{{ $addonFabricConditioner }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Color Safe</div>
                                    <div class="detail-value">{{ $addonColorSafe }}</div>
                                </div>
                                <div class="detail-row subtotal-row">
                                    <div class="detail-label">Subtotal</div>
                                    <div class="detail-value strong">
                                        <span class="peso">₱</span> {{ number_format((float) $addonSubtotal, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="summary-panel total-panel">
                    <div class="panel-head">
                        <h2>Total</h2>
                    </div>

                    <div class="total-breakdown">
                        <div class="total-line">
                            <span>Service</span>
                            <strong><span class="peso">₱</span> {{ number_format((float) $serviceSubtotal, 2) }}</strong>
                        </div>
                        <div class="total-line">
                            <span>Comforters</span>
                            <strong><span class="peso">₱</span> {{ number_format((float) $comforterSubtotal, 2) }}</strong>
                        </div>
                        <div class="total-line">
                            <span>Add-ons</span>
                            <strong><span class="peso">₱</span> {{ number_format((float) $addonSubtotal, 2) }}</strong>
                        </div>
                        <div class="total-line">
                            <span>Pickup / Delivery</span>
                            <strong><span class="peso">₱</span> {{ number_format((float) $pickupCharge, 2) }}</strong>
                        </div>

                        <div class="grand-total">
                            <span>Estimated Total</span>
                            <div><span class="peso">₱</span> {{ number_format((float) $grandTotal, 2) }}</div>
                        </div>
                    </div>
                </section>

                @if (!$readonly)
                    <form action="{{ route('order.confirm') }}" method="POST" id="confirmOrderForm">
                        @csrf

                        @foreach ($payload as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $subValue)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="actions">
                            <a href="{{ $backHref }}" class="btn btn-back">{{ $backLabel }}</a>
                            <button type="submit" id="confirmBtn" class="btn btn-primary">Confirm Booking</button>
                        </div>
                    </form>
                @else
                    <div class="actions">
                        <a href="{{ $backHref }}" class="btn btn-primary btn-dashboard-glow">{{ $backLabel }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (!$readonly)
        <div class="modal fade" id="thankYouModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content thankyou-modal">
                    <button type="button" class="btn-close thankyou-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    <div class="thankyou-topbar"></div>

                    <div class="thankyou-body">
                        <div class="thankyou-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 7L10 17L4 11" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <h2>Order Received</h2>
                        <p id="thankYouMessage">
                            Thank you for choosing E&amp;B Laundry Hub. Your order has been placed successfully and an order receipt has been sent to your email.
                        </p>

                        <a href="{{ $defaultBackUrl }}" class="btn-pink" id="thankYouBackBtn">
                            {{ $defaultBackLabel }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('confirmOrderForm');
                const confirmBtn = document.getElementById('confirmBtn');
                const thankYouModalEl = document.getElementById('thankYouModal');
                const thankYouMessage = document.getElementById('thankYouMessage');

                if (!form || !confirmBtn || !thankYouModalEl) return;

                const thankYouModal = new bootstrap.Modal(thankYouModalEl);

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    if (confirmBtn.disabled) return;

                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'Confirming...';

                    try {
                        const formData = new FormData(form);

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (!response.ok || !result.ok) {
                            throw new Error(result.message || 'Unable to confirm order.');
                        }

                        if (thankYouMessage) {
                            let message = 'Thank you for choosing E&B Laundry Hub. Your order has been placed successfully and an order receipt has been sent to your email.';
                            if (result.order_id) {
                                message = `Your order #${result.order_id} has been placed successfully. An order receipt has been sent to your email.`;
                            }
                            thankYouMessage.textContent = message;
                        }

                        thankYouModal.show();

                        setTimeout(function () {
                            window.location.href = @json($defaultBackUrl);
                        }, 2600);

                    } catch (error) {
                        alert(error.message || 'Something went wrong while confirming your order.');
                        confirmBtn.disabled = false;
                        confirmBtn.textContent = 'Confirm Booking';
                    }
                });
            });
        </script>
    @endif
</body>
</html>