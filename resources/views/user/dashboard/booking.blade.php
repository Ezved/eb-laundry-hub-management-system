<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Schedule Pickup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/booking.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Schedule a Pickup</h1>
            </div>

            <div class="card-body">
                @php
                    $serviceName = 'Full Service';
                    $servicePrice = 180;

                    $draft = session('order_draft.payload', []);
                    $pickupChargeDisplay = $draft['pickup_delivery_charge'] ?? '₱ 50.00';

                    $loyaltyClaimsForOrder = session('loyalty_claims_for_current_order', 0);
                    $totalLoyaltyClaims = optional(auth()->user())->loyalty_claims ?? 0;
                @endphp

                <form action="{{ route('order.summary') }}" method="POST">
                    @csrf

                    <div class="grid">
                        <div class="field">
                            <label>Pickup Date <span class="hint">*</span></label>
                            <input
                                type="date"
                                name="pickup_date"
                                id="pickup_date"
                                required
                                value="{{ old('pickup_date', $draft['pickup_date'] ?? '') }}">
                        </div>

                        <div class="field">
                            <label>Pickup Time (8am – 6pm) <span class="hint">*</span></label>
                            @php
                                $pickupTimeOld = old('pickup_time', $draft['pickup_time'] ?? '');
                            @endphp
                            <select name="pickup_time" id="pickup_time" required>
                                <option value="">Select time</option>
                                <option value="8 am" {{ $pickupTimeOld === '8 am' ? 'selected' : '' }}>8 am</option>
                                <option value="9 am" {{ $pickupTimeOld === '9 am' ? 'selected' : '' }}>9 am</option>
                                <option value="10 am" {{ $pickupTimeOld === '10 am' ? 'selected' : '' }}>10 am</option>
                                <option value="11 am" {{ $pickupTimeOld === '11 am' ? 'selected' : '' }}>11 am</option>
                                <option value="12 pm" {{ $pickupTimeOld === '12 pm' ? 'selected' : '' }}>12 pm</option>
                                <option value="1 pm" {{ $pickupTimeOld === '1 pm' ? 'selected' : '' }}>1 pm</option>
                                <option value="2 pm" {{ $pickupTimeOld === '2 pm' ? 'selected' : '' }}>2 pm</option>
                                <option value="3 pm" {{ $pickupTimeOld === '3 pm' ? 'selected' : '' }}>3 pm</option>
                                <option value="4 pm" {{ $pickupTimeOld === '4 pm' ? 'selected' : '' }}>4 pm</option>
                                <option value="5 pm" {{ $pickupTimeOld === '5 pm' ? 'selected' : '' }}>5 pm</option>
                                <option value="6 pm" {{ $pickupTimeOld === '6 pm' ? 'selected' : '' }}>6 pm</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>When to Deliver <span class="hint">(Optional)</span></label>
                            <input
                                type="date"
                                name="delivery_date"
                                id="delivery_date"
                                value="{{ old('delivery_date', $draft['delivery_date'] ?? '') }}">
                            <label class="hint" style="margin-top: 2px;">
                                <input
                                    type="checkbox"
                                    name="self_pickup"
                                    id="self_pickup"
                                    value="1"
                                    {{ old('self_pickup', $draft['self_pickup'] ?? false) ? 'checked' : '' }}>
                                I will pickup my own laundry
                            </label>
                        </div>

                        <div class="field">
                            <label>Delivery Time (8am – 8pm)</label>
                            @php
                                $deliveryTimeOld = old('delivery_time', $draft['delivery_time'] ?? '');
                            @endphp
                            <select name="delivery_time" id="delivery_time">
                                <option value="">Select time</option>
                                <option value="8 am" {{ $deliveryTimeOld === '8 am' ? 'selected' : '' }}>8 am</option>
                                <option value="9 am" {{ $deliveryTimeOld === '9 am' ? 'selected' : '' }}>9 am</option>
                                <option value="10 am" {{ $deliveryTimeOld === '10 am' ? 'selected' : '' }}>10 am</option>
                                <option value="11 am" {{ $deliveryTimeOld === '11 am' ? 'selected' : '' }}>11 am</option>
                                <option value="12 pm" {{ $deliveryTimeOld === '12 pm' ? 'selected' : '' }}>12 pm</option>
                                <option value="1 pm" {{ $deliveryTimeOld === '1 pm' ? 'selected' : '' }}>1 pm</option>
                                <option value="2 pm" {{ $deliveryTimeOld === '2 pm' ? 'selected' : '' }}>2 pm</option>
                                <option value="3 pm" {{ $deliveryTimeOld === '3 pm' ? 'selected' : '' }}>3 pm</option>
                                <option value="4 pm" {{ $deliveryTimeOld === '4 pm' ? 'selected' : '' }}>4 pm</option>
                                <option value="5 pm" {{ $deliveryTimeOld === '5 pm' ? 'selected' : '' }}>5 pm</option>
                                <option value="6 pm" {{ $deliveryTimeOld === '6 pm' ? 'selected' : '' }}>6 pm</option>
                                <option value="7 pm" {{ $deliveryTimeOld === '7 pm' ? 'selected' : '' }}>7 pm</option>
                                <option value="8 pm" {{ $deliveryTimeOld === '8 pm' ? 'selected' : '' }}>8 pm</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>Service Type</label>
                            <input type="hidden" name="service_type" value="{{ $serviceName }}">
                            <input
                                type="text"
                                value="{{ $serviceName }} (₱ {{ number_format($servicePrice, 2) }})"
                                readonly
                                class="readonly">
                        </div>

                        <div class="field">
                            <label>Load Quantity <span class="hint">(7 kg = 1 load)</span></label>
                            <input
                                type="number"
                                name="load_qty"
                                placeholder="0"
                                min="0"
                                required
                                value="{{ old('load_qty', $draft['load_qty'] ?? '') }}">
                            <span class="hint">Notice: 8 kg = additional ₱ 40</span>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>Comforters <span class="hint">(Optional)</span></label>
                            <div class="tiny-list">
                                <div class="tiny-row">
                                    <input class="qty" type="number" name="comforter_single_double" min="0" placeholder="0"
                                        value="{{ old('comforter_single_double', $draft['comforter_single_double'] ?? '') }}">
                                    <div class="desc">
                                        <span>1-2 pcs. Single / Double</span>
                                        <span class="price">₱ 200.00</span>
                                    </div>
                                </div>

                                <div class="tiny-row">
                                    <input class="qty" type="number" name="comforter_queen" min="0" placeholder="0"
                                        value="{{ old('comforter_queen', $draft['comforter_queen'] ?? '') }}">
                                    <div class="desc">
                                        <span>1 pc. Queen</span>
                                        <span class="price">₱ 220.00</span>
                                    </div>
                                </div>

                                <div class="tiny-row">
                                    <input class="qty" type="number" name="comforter_king" min="0" placeholder="0"
                                        value="{{ old('comforter_king', $draft['comforter_king'] ?? '') }}">
                                    <div class="desc">
                                        <span>1 pc. King</span>
                                        <span class="price">₱ 230.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Add-Ons <span class="hint">(Optional)</span></label>
                            <div class="tiny-list">
                                <div class="tiny-row">
                                    <input class="qty" type="number" name="addon_spin" min="0" placeholder="0"
                                        value="{{ old('addon_spin', $draft['addon_spin'] ?? '') }}">
                                    <div class="desc">
                                        <span>Spin (11 mins)</span>
                                        <span class="price">₱ 20.00</span>
                                    </div>
                                </div>

                                <div class="tiny-row">
                                    <input class="qty" type="number" name="addon_dry" min="0" placeholder="0"
                                        value="{{ old('addon_dry', $draft['addon_dry'] ?? '') }}">
                                    <div class="desc">
                                        <span>Dry (10 mins)</span>
                                        <span class="price">₱ 20.00</span>
                                    </div>
                                </div>

                                <div class="tiny-row">
                                    <input class="qty" type="number" name="addon_liquid_detergent" min="0" placeholder="0"
                                        value="{{ old('addon_liquid_detergent', $draft['addon_liquid_detergent'] ?? '') }}">
                                    <div class="desc">
                                        <span>Liquid Detergent (Triple Pack)</span>
                                        <span class="price">₱ 20.00</span>
                                    </div>
                                </div>

                                <div class="tiny-row">
                                    <input class="qty" type="number" name="addon_fabric_conditioner" min="0" placeholder="0"
                                        value="{{ old('addon_fabric_conditioner', $draft['addon_fabric_conditioner'] ?? '') }}">
                                    <div class="desc">
                                        <span>Fabric Conditioner (Twin Pack)</span>
                                        <span class="price">₱ 20.00</span>
                                    </div>
                                </div>

                                <div class="tiny-row">
                                    <input class="qty" type="number" name="addon_color_safe" min="0" placeholder="0"
                                        value="{{ old('addon_color_safe', $draft['addon_color_safe'] ?? '') }}">
                                    <div class="desc">
                                        <span>Color Safe</span>
                                        <span class="price">₱ 8.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field" style="margin-top: 8px;">
                        <label>Special Instructions <span class="hint">(Optional)</span></label>
                        <textarea name="special_instructions" placeholder="Anything we should know?">{{ old('special_instructions', $draft['special_instructions'] ?? '') }}</textarea>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>Pickup & Delivery Service Charge</label>
                            <input
                                type="text"
                                name="pickup_delivery_charge"
                                id="pickup_delivery_charge"
                                value="{{ $pickupChargeDisplay }}"
                                readonly
                                class="readonly">
                        </div>

                        <div class="field">
                            <label>ESTIMATED TOTAL: <span class="hint">(Not Final)</span></label>
                            <input type="text" name="ui_total" value="₱ 0.00" readonly class="readonly">
                        </div>
                    </div>

                    <div class="grid-2" style="margin-top: 8px;">
                        <div class="field">
                            <label>Payment Through <span class="hint">*</span></label>
                            @php
                                $paymentOld = old('payment_method', $draft['payment_method'] ?? '');
                            @endphp
                            <select name="payment_method" id="payment_method" required>
                                <option value="">Select payment</option>
                                <option value="cod" {{ $paymentOld === 'cod' ? 'selected' : '' }}>Cash On Delivery</option>
                                <option value="gcash" {{ $paymentOld === 'gcash' ? 'selected' : '' }}>GCash</option>
                            </select>
                        </div>

                        <div class="field gcash-ref-field" id="gcash-ref-field">
                            <label>GCash Reference No. <span class="hint">*</span></label>
                            <input
                                type="text"
                                name="gcash_reference"
                                id="gcash-reference-input"
                                placeholder="Enter reference number"
                                value="{{ old('gcash_reference', $draft['gcash_reference'] ?? '') }}">
                        </div>
                    </div>

                    <div class="notice">
                        <p>Notice:</p>
                        <ul>
                            <li>E&B Laundry Hub are open daily from <strong>8 am to 8 pm</strong>.</li>
                            <li>Pickups are only available from <strong>8 am to 6 pm</strong>.</li>
                            <li>We weighs your laundry and send the final total amount receipt through Gmail.</li>
                            <li>One load is 8kg max, an additional charge of <strong>₱ 40.00</strong> will be added.</li>
                            <li>For inquiries, contact <strong>09301902025</strong> or message us on Messenger.</li>
                        </ul>
                    </div>

                    <div class="actions">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-back">Cancel</a>

                        @if ($loyaltyClaimsForOrder > 0)
                            <div class="loyalty-claim-indicator">
                                <span class="claim-badge">{{ $loyaltyClaimsForOrder }}</span>
                                <span class="claim-text">Loyalty free load applied</span>
                                <span class="claim-total">(Total claims used: {{ $totalLoyaltyClaims }})</span>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const paymentSelect = document.getElementById('payment_method');
            const gcashRefField = document.getElementById('gcash-ref-field');
            const gcashRefInput = document.getElementById('gcash-reference-input');

            function formatMoney(value) {
                return "₱ " + Number(value || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function toggleGCashReference() {
                if (paymentSelect.value === 'gcash') {
                    gcashRefField.classList.add('show');
                    gcashRefInput.required = true;
                } else {
                    gcashRefField.classList.remove('show');
                    gcashRefInput.required = false;
                }
            }

            paymentSelect.addEventListener('change', toggleGCashReference);
            toggleGCashReference();

            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, "0");
            const dd = String(today.getDate()).padStart(2, "0");
            const minDate = `${yyyy}-${mm}-${dd}`;

            const pickupInput = document.getElementById("pickup_date");
            const deliveryInput = document.getElementById("delivery_date");
            const pickupTimeSel = document.getElementById("pickup_time");
            const deliveryTimeSel = document.getElementById("delivery_time");

            if (pickupInput) pickupInput.setAttribute("min", minDate);
            if (deliveryInput) deliveryInput.setAttribute("min", minDate);

            const selfPickup = document.getElementById("self_pickup");
            const pickupChargeEl = document.getElementById("pickup_delivery_charge");

            const PRICES = {
                full_service: {{ (float) $servicePrice }},
                pickup_delivery: 50,
                self_pickup: 25,
                comforter_single_double: 200,
                comforter_queen: 220,
                comforter_king: 230,
                addon_spin: 20,
                addon_dry: 20,
                addon_liquid_detergent: 20,
                addon_fabric_conditioner: 20,
                addon_color_safe: 8
            };

            function calcTotal() {
                let total = 0;

                const loadQtyEl = document.querySelector("[name=load_qty]");
                const loads = parseInt(loadQtyEl?.value || "0", 10) || 0;
                total += loads * PRICES.full_service;

                total += (parseInt(document.querySelector("[name=comforter_single_double]")?.value || "0", 10) || 0) * PRICES.comforter_single_double;
                total += (parseInt(document.querySelector("[name=comforter_queen]")?.value || "0", 10) || 0) * PRICES.comforter_queen;
                total += (parseInt(document.querySelector("[name=comforter_king]")?.value || "0", 10) || 0) * PRICES.comforter_king;

                total += (parseInt(document.querySelector("[name=addon_spin]")?.value || "0", 10) || 0) * PRICES.addon_spin;
                total += (parseInt(document.querySelector("[name=addon_dry]")?.value || "0", 10) || 0) * PRICES.addon_dry;
                total += (parseInt(document.querySelector("[name=addon_liquid_detergent]")?.value || "0", 10) || 0) * PRICES.addon_liquid_detergent;
                total += (parseInt(document.querySelector("[name=addon_fabric_conditioner]")?.value || "0", 10) || 0) * PRICES.addon_fabric_conditioner;
                total += (parseInt(document.querySelector("[name=addon_color_safe]")?.value || "0", 10) || 0) * PRICES.addon_color_safe;

                const isSelf = selfPickup && selfPickup.checked;
                const pickupCharge = isSelf ? PRICES.self_pickup : PRICES.pickup_delivery;
                total += pickupCharge;

                if (pickupChargeEl) {
                    pickupChargeEl.value = formatMoney(pickupCharge);
                }

                const uiTotal = document.querySelector("[name=ui_total]");
                if (uiTotal) uiTotal.value = formatMoney(total);
            }

            function parseHour(label) {
                if (!label) return null;
                label = label.toLowerCase().trim();
                const parts = label.split(" ");
                if (parts.length < 2) return null;
                let hour = parseInt(parts[0], 10);
                if (isNaN(hour)) return null;
                const ampm = parts[1];
                if (ampm === "pm" && hour !== 12) hour += 12;
                if (ampm === "am" && hour === 12) hour = 0;
                return hour;
            }

            function updateTimeOptions() {
                const now = new Date();
                const currentHour = now.getHours();

                const pickupDateVal = pickupInput?.value || "";
                const deliveryDateVal = deliveryInput?.value || "";

                const isPickupToday = pickupDateVal === minDate;
                const isDeliveryToday = deliveryDateVal === minDate;

                function processSelect(selectEl, isToday) {
                    if (!selectEl) return;
                    for (let i = 0; i < selectEl.options.length; i++) {
                        const opt = selectEl.options[i];

                        if (!opt.value) {
                            opt.disabled = false;
                            continue;
                        }

                        if (!isToday) {
                            opt.disabled = false;
                            continue;
                        }

                        const hour24 = parseHour(opt.textContent || opt.value);
                        if (hour24 === null) {
                            opt.disabled = false;
                            continue;
                        }

                        opt.disabled = hour24 <= currentHour;
                    }
                }

                processSelect(pickupTimeSel, isPickupToday);
                processSelect(deliveryTimeSel, isDeliveryToday);
            }

            function applySelfPickupEffects() {
                if (!selfPickup) return;
                const isSelf = selfPickup.checked;

                if (deliveryInput) {
                    deliveryInput.disabled = isSelf;
                    if (isSelf) deliveryInput.value = "";
                }

                if (deliveryTimeSel) {
                    deliveryTimeSel.disabled = isSelf;
                    if (isSelf) deliveryTimeSel.value = "";
                }

                calcTotal();
            }

            if (selfPickup) {
                selfPickup.addEventListener("change", function() {
                    applySelfPickupEffects();
                    updateTimeOptions();
                });
            }

            document.querySelectorAll("input, select").forEach(el => {
                if (el.id === "self_pickup") return;
                el.addEventListener("input", function() {
                    calcTotal();
                    updateTimeOptions();
                });
                el.addEventListener("change", function() {
                    calcTotal();
                    updateTimeOptions();
                });
            });

            applySelfPickupEffects();
            updateTimeOptions();
            calcTotal();
        });
    </script>
</body>

</html>