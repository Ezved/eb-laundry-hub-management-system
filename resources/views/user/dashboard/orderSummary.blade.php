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
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    @php
        $payload = old() ?: request()->all();

        $serviceName = $payload['service_type'] ?? 'Full Service';
        $servicePrice = 180.00;

        $loadQty = (int) ($payload['load_qty'] ?? 0);

        $comforterSingleDouble = (int) ($payload['comforter_single_double'] ?? 0);
        $comforterQueen = (int) ($payload['comforter_queen'] ?? 0);
        $comforterKing = (int) ($payload['comforter_king'] ?? 0);

        $addonSpin = (int) ($payload['addon_spin'] ?? 0);
        $addonDry = (int) ($payload['addon_dry'] ?? 0);
        $addonLiquidDetergent = (int) ($payload['addon_liquid_detergent'] ?? 0);
        $addonFabricConditioner = (int) ($payload['addon_fabric_conditioner'] ?? 0);
        $addonColorSafe = (int) ($payload['addon_color_safe'] ?? 0);

        $selfPickup = !empty($payload['self_pickup']);
        $pickupCharge = $selfPickup ? 25.00 : 50.00;

        $serviceSubtotal = $loadQty * $servicePrice;
        $comforterSubtotal = ($comforterSingleDouble * 200.00) + ($comforterQueen * 220.00) + ($comforterKing * 230.00);
        $addonSubtotal = ($addonSpin * 20.00) + ($addonDry * 20.00) + ($addonLiquidDetergent * 20.00) + ($addonFabricConditioner * 20.00) + ($addonColorSafe * 8.00);

        $estimatedTotal = $serviceSubtotal + $comforterSubtotal + $addonSubtotal + $pickupCharge;

        $money = fn($value) => '₱ ' . number_format((float) $value, 2);

        $paymentMethod = $payload['payment_method'] ?? '';
        $pickupDate = $payload['pickup_date'] ?? '';
        $pickupTime = $payload['pickup_time'] ?? '';
        $deliveryDate = $payload['delivery_date'] ?? '';
        $deliveryTime = $payload['delivery_time'] ?? '';
        $specialInstructions = $payload['special_instructions'] ?? '';
        $gcashReference = $payload['gcash_reference'] ?? '';
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
                                <div class="detail-value">{{ $money($servicePrice) }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Service Subtotal</div>
                                <div class="detail-value strong">{{ $money($serviceSubtotal) }}</div>
                            </div>
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
                                    <div class="detail-value strong">{{ $money($comforterSubtotal) }}</div>
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
                                    <div class="detail-value strong">{{ $money($addonSubtotal) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="summary-grid">
                    <section class="summary-panel">
                        <div class="panel-head">
                            <h2>Payment Details</h2>
                        </div>

                        <div class="detail-list">
                            <div class="detail-row">
                                <div class="detail-label">Pickup / Delivery Charge</div>
                                <div class="detail-value">{{ $money($pickupCharge) }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Payment Method</div>
                                <div class="detail-value">
                                    @if ($paymentMethod === 'gcash')
                                        GCash
                                    @elseif ($paymentMethod === 'cod')
                                        Cash On Delivery
                                    @else
                                        Not set
                                    @endif
                                </div>
                            </div>

                            @if ($paymentMethod === 'gcash')
                                <div class="detail-row">
                                    <div class="detail-label">GCash Reference</div>
                                    <div class="detail-value">{{ $gcashReference ?: 'Not provided' }}</div>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="summary-panel total-panel">
                        <div class="panel-head">
                            <h2>Total</h2>
                        </div>

                        <div class="total-breakdown">
                            <div class="total-line">
                                <span>Service</span>
                                <strong>{{ $money($serviceSubtotal) }}</strong>
                            </div>
                            <div class="total-line">
                                <span>Comforters</span>
                                <strong>{{ $money($comforterSubtotal) }}</strong>
                            </div>
                            <div class="total-line">
                                <span>Add-ons</span>
                                <strong>{{ $money($addonSubtotal) }}</strong>
                            </div>
                            <div class="total-line">
                                <span>Pickup / Delivery</span>
                                <strong>{{ $money($pickupCharge) }}</strong>
                            </div>

                            <div class="grand-total">
                                <span>Estimated Total</span>
                                <div>{{ $money($estimatedTotal) }}</div>
                            </div>
                        </div>
                    </section>
                </div>

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
                        <a href="{{ url()->previous() }}" class="btn btn-back">Back</a>
                        <button type="submit" id="confirmBtn" class="btn btn-primary">Confirm Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Thank You Modal -->
    <div class="modal fade" id="thankYouModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content thankyou-modal">
                <button type="button" class="btn-close thankyou-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="thankyou-icon">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M20 7L10 17L4 11" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h2>Order Received.<br>Thank You For Choosing Us!</h2>
                <p id="thankYouMessage">An order receipt has been sent to your email.</p>

                <a href="{{ route('user.dashboard') }}" class="btn-pink" id="thankYouBackBtn">Back to Dashboard</a>
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
                        let message = 'An order receipt has been sent to your email.';
                        if (result.order_id) {
                            message = `Your order #${result.order_id} has been placed successfully. An order receipt has been sent to your email.`;
                        }
                        thankYouMessage.textContent = message;
                    }

                    thankYouModal.show();

                    setTimeout(function () {
                        window.location.href = "{{ route('user.dashboard') }}";
                    }, 2600);

                } catch (error) {
                    alert(error.message || 'Something went wrong while confirming your order.');
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Confirm Booking';
                }
            });
        });
    </script>
</body>

</html>