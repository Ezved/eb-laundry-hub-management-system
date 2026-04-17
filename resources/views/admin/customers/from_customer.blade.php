<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Add Walk-in</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
          rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard/walkin.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">

    <style>
        .card-header {
            background: #173F7B;
            color: #fff;
        }

        .card-header h1 {
            color: #fff;
        }

        .btn-back {
            background: #fff;
            border: 1px solid rgba(220, 53, 69, .28);
            color: #dc3545;
        }

        .btn-back:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-primary {
            background: #ff6ef7;
            border: 1px solid #ff6ef7;
            color: #fff;
        }

        .btn-primary:hover {
            background: #f954ea;
            border-color: #f954ea;
            color: #fff;
        }

        .pill {
            display: none;
        }
    </style>
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Walk-In Customers</h1>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.walkin.preview') }}" method="POST">
                @csrf

                <input type="hidden" name="origin" value="from_customer">
                <input type="hidden" name="linked_customer_id" value="{{ $customer->id }}">
                @if($userId)
                    <input type="hidden" name="linked_user_id" value="{{ $userId }}">
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                @endif

                @php
                    $svcSel = old('service_type', data_get($prefill, 'service_type', 'Full Service'));
                    $uomSel = old('uom', data_get($prefill, 'uom', 'Load(s)'));
                    $paySel = old('payment_method', data_get($prefill, 'payment_method'));
                @endphp

                <div class="grid">
                    {{-- Identity --}}
                    <div class="field">
                        <label>Name <span class="hint">*</span></label>
                        <input type="text"
                               name="customer_name"
                               value="{{ old('customer_name', data_get($prefill, 'customer_name')) }}"
                               required>
                    </div>

                    <div class="field">
                        <label>Contact Number <span class="hint">(Optional)</span></label>
                        <input type="text"
                               name="customer_phone"
                               value="{{ old('customer_phone', data_get($prefill, 'customer_phone')) }}">
                    </div>

                    <div class="field" style="grid-column: 1 / -1;">
                        <label>Address <span class="hint">(Optional)</span></label>
                        <input type="text"
                               name="customer_address"
                               value="{{ old('customer_address', data_get($prefill, 'customer_address')) }}"
                               placeholder="House/Blk, Street, Barangay, City">
                    </div>

                    {{-- Order date & time --}}
                    <div class="field">
                        <label>Order Date <span class="hint">*</span></label>
                        <input type="date"
                               name="order_date"
                               value="{{ old('order_date', data_get($prefill, 'order_date', now()->toDateString())) }}"
                               min="{{ now()->toDateString() }}"
                               required>
                    </div>

                    <div class="field">
                        <label>Time (Walk-in) <span class="hint">*</span></label>
                        <input type="text"
                               name="walkin_time"
                               placeholder="e.g., 3:30 PM"
                               value="{{ old('walkin_time', data_get($prefill, 'walkin_time')) }}"
                               required>
                    </div>

                    {{-- Service type --}}
                    <div class="field">
                        <label>Service Type <span class="hint">*</span></label>
                        <select name="service_type" required id="service-type-select">
                            <option value="Full Service" {{ $svcSel === 'Full Service' ? 'selected' : '' }}>Full Service</option>
                            <option value="Drop-Off Service" {{ $svcSel === 'Drop-Off Service' ? 'selected' : '' }}>Drop-Off Service</option>
                            <option value="Self-Service" {{ $svcSel === 'Self-Service' ? 'selected' : '' }}>Self-Service</option>
                        </select>
                        <div class="hint" id="per-load-hint" style="margin-top:4px;"></div>
                    </div>

                    <div class="field">
                        <label>UOM <span class="hint">(Unit of Measure)</span></label>
                        <input type="text"
                               name="uom"
                               id="uom-field"
                               value="{{ $uomSel }}"
                               readonly>
                    </div>

                    {{-- Loads --}}
                    <div class="field">
                        <label>Load Quantity <span class="hint">(7 kg = 1 load)</span> <span class="hint">*</span></label>
                        <input type="number"
                               name="load_qty"
                               min="0"
                               step="1"
                               value="{{ old('load_qty', data_get($prefill, 'load_qty', 0)) }}"
                               required>
                        <label>
                            <input type="checkbox"
                                   name="exceeds_8kg"
                                   value="1"
                                   {{ old('exceeds_8kg', data_get($prefill, 'exceeds_8kg')) ? 'checked' : '' }}>
                            Load Exceeds 8 kg (₱ 40.00 extra)
                        </label>
                    </div>

                    <div class="field">
                        <label>Base Rate <span class="hint">(Auto-computed)</span></label>
                        <input type="text" id="base-rate-display" value="₱ 0.00" readonly>
                    </div>

                    {{-- Comforters --}}
                    <div class="field" style="grid-column:1 / -1;">
                        <label>Comforters <span class="hint">(Optional)</span></label>
                        <div class="tiny-list">
                            <div class="tiny-row">
                                <input class="qty" type="number" name="comforter_single_double" min="0" step="1"
                                       value="{{ old('comforter_single_double', data_get($prefill, 'comforter_single_double', 0)) }}">
                                <div class="desc">
                                    <span>1–2 pcs. Single / Double</span><span class="price">₱ 200.00</span>
                                </div>
                            </div>
                            <div class="tiny-row">
                                <input class="qty" type="number" name="comforter_queen" min="0" step="1"
                                       value="{{ old('comforter_queen', data_get($prefill, 'comforter_queen', 0)) }}">
                                <div class="desc">
                                    <span>1 pc. Queen</span><span class="price">₱ 220.00</span>
                                </div>
                            </div>
                            <div class="tiny-row">
                                <input class="qty" type="number" name="comforter_king" min="0" step="1"
                                       value="{{ old('comforter_king', data_get($prefill, 'comforter_king', 0)) }}">
                                <div class="desc">
                                    <span>1 pc. King</span><span class="price">₱ 230.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Add-ons --}}
                    <div class="field" style="grid-column:1 / -1;">
                        <label>Add-Ons <span class="hint">(Optional)</span></label>
                        <div class="tiny-list">
                            <div class="tiny-row">
                                <input class="qty" type="number" name="addon_spin" min="0" step="1"
                                       value="{{ old('addon_spin', data_get($prefill, 'addon_spin', 0)) }}">
                                <div class="desc">
                                    <span>Spin (11 mins)</span><span class="price">₱ 20.00</span>
                                </div>
                            </div>
                            <div class="tiny-row">
                                <input class="qty" type="number" name="addon_dry" min="0" step="1"
                                       value="{{ old('addon_dry', data_get($prefill, 'addon_dry', 0)) }}">
                                <div class="desc">
                                    <span>Dry (10 mins)</span><span class="price">₱ 20.00</span>
                                </div>
                            </div>
                            <div class="tiny-row">
                                <input class="qty" type="number" name="addon_liquid_detergent" min="0" step="1"
                                       value="{{ old('addon_liquid_detergent', data_get($prefill, 'addon_liquid_detergent', 0)) }}">
                                <div class="desc">
                                    <span>Liquid Detergent (Triple Pack)</span><span class="price">₱ 20.00</span>
                                </div>
                            </div>
                            <div class="tiny-row">
                                <input class="qty" type="number" name="addon_fabric_conditioner" min="0" step="1"
                                       value="{{ old('addon_fabric_conditioner', data_get($prefill, 'addon_fabric_conditioner', 0)) }}">
                                <div class="desc">
                                    <span>Fabric Conditioner (Twin Pack)</span><span class="price">₱ 20.00</span>
                                </div>
                            </div>
                            <div class="tiny-row">
                                <input class="qty" type="number" name="addon_color_safe" min="0" step="1"
                                       value="{{ old('addon_color_safe', data_get($prefill, 'addon_color_safe', 0)) }}">
                                <div class="desc">
                                    <span>Color Safe</span><span class="price">₱ 8.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="field" style="grid-column:1 / -1;">
                        <label>Special Instructions <span class="hint">(Optional)</span></label>
                        <textarea name="special_instructions"
                                  placeholder="Anything we should know?">{{ old('special_instructions', data_get($prefill, 'special_instructions')) }}</textarea>
                    </div>
                </div>

                {{-- Payment + GCash Reference + Total --}}
                <div class="grid-2" style="margin-top:8px;">
                    <div class="payment-row">
                        <div class="field">
                            <label>Payment Through <span class="hint">*</span></label>
                            <div class="inline-help">
                                <select name="payment_method" id="payment-method-select" required>
                                    <option value="">Select payment</option>
                                    <option value="cash" {{ $paySel === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="gcash" {{ $paySel === 'gcash' ? 'selected' : '' }}>GCash</option>
                                </select>
                            </div>
                        </div>

                        <div class="field gcash-ref-field" id="gcash-ref-field">
                            <label>GCash Reference No. <span class="hint">*</span></label>
                            <input type="text"
                                   name="gcash_reference"
                                   id="gcash-reference-input"
                                   placeholder="Enter reference number"
                                   value="{{ old('gcash_reference', data_get($prefill, 'gcash_reference')) }}">
                        </div>
                    </div>

                    <div class="field">
                        <label>TOTAL AMOUNT</label>
                        <input type="text" name="ui_total" id="ui-total-display" value="₱ 0.00" readonly>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="actions">
                    <a href="{{ route('customers') }}" class="btn btn-back">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.SERVICE_PRICES = @json($priceMap);
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const SERVICE_PRICES = window.SERVICE_PRICES || {};
        const EXTRAS = {
            surcharge: 40,
            comforter_single_double: 200,
            comforter_queen: 220,
            comforter_king: 230,
            addon_spin: 20,
            addon_dry: 20,
            addon_liquid_detergent: 20,
            addon_fabric_conditioner: 20,
            addon_color_safe: 8
        };

        const paymentSelect = document.getElementById('payment-method-select');
        const gcashRefField = document.getElementById('gcash-ref-field');
        const gcashRefInput = document.getElementById('gcash-reference-input');
        const svcSel = document.getElementById('service-type-select');
        const hint = document.getElementById('per-load-hint');
        const uomField = document.getElementById('uom-field');
        const baseRateDisplay = document.getElementById('base-rate-display');
        const totalDisplay = document.getElementById('ui-total-display');

        function formatMoney(value) {
            const amount = Number(value || 0);
            return "₱ " + amount.toLocaleString(undefined, {
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
                gcashRefInput.value = '';
            }
        }

        function updateUOM() {
            const service = svcSel?.value || 'Full Service';

            if (service === 'Self-Service') {
                uomField.value = 'Load(s)';
            } else if (service === 'Drop-Off Service') {
                uomField.value = 'Load(s)';
            } else {
                uomField.value = 'Load(s)';
            }
        }

        function paintPerLoadHint() {
            const svc = svcSel?.value || 'Full Service';
            const p = Number(SERVICE_PRICES[svc] || 0);
            hint.textContent = p ? `Price per load: ${formatMoney(p)}` : '';
        }

        function valNum(name) {
            return parseFloat(document.querySelector(`[name="${name}"]`)?.value) || 0;
        }

        function calcTotal() {
            let total = 0;

            const service = document.querySelector("[name='service_type']")?.value || 'Full Service';
            const perLoad = Number(SERVICE_PRICES[service] || 0);
            const loadQty = valNum('load_qty');

            const baseRate = loadQty * perLoad;
            total += baseRate;

            if (document.querySelector("[name='exceeds_8kg']")?.checked) {
                total += EXTRAS.surcharge;
            }

            total += valNum('comforter_single_double') * EXTRAS.comforter_single_double;
            total += valNum('comforter_queen') * EXTRAS.comforter_queen;
            total += valNum('comforter_king') * EXTRAS.comforter_king;

            total += valNum('addon_spin') * EXTRAS.addon_spin;
            total += valNum('addon_dry') * EXTRAS.addon_dry;
            total += valNum('addon_liquid_detergent') * EXTRAS.addon_liquid_detergent;
            total += valNum('addon_fabric_conditioner') * EXTRAS.addon_fabric_conditioner;
            total += valNum('addon_color_safe') * EXTRAS.addon_color_safe;

            baseRateDisplay.value = formatMoney(baseRate);
            totalDisplay.value = formatMoney(total);
        }

        paymentSelect.addEventListener('change', toggleGCashReference);

        document.querySelectorAll("input, select, textarea").forEach(el => {
            el.addEventListener("input", () => {
                calcTotal();
                paintPerLoadHint();
                updateUOM();
            });

            el.addEventListener("change", () => {
                calcTotal();
                paintPerLoadHint();
                updateUOM();
            });
        });

        toggleGCashReference();
        updateUOM();
        paintPerLoadHint();
        calcTotal();
    });
</script>
</body>
</html>