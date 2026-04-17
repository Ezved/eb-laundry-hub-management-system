{{-- resources/views/user/dashboard/reschedule.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reschedule Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/booking.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">

    <style>
        .order-info {
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(23, 63, 123, 0.10);
            padding: 18px 20px;
            border-radius: 18px;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(13, 42, 82, 0.06);
        }

        .order-info h5 {
            color: #173F7B;
            font-weight: 800;
            margin-bottom: 14px;
            font-size: 18px;
            font-family: 'Inter', sans-serif;
        }

        .order-info .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(23, 63, 123, 0.08);
        }

        .order-info .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .order-info .info-row:first-of-type {
            padding-top: 0;
        }

        .order-info .info-label {
            font-weight: 700;
            color: #6b7280;
            font-size: 14px;
        }

        .order-info .info-value {
            font-weight: 700;
            color: #173F7B;
            text-align: right;
            font-size: 14px;
        }

        .self-pickup-wrap {
            margin-top: 4px;
        }

        .self-pickup-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
        }

        .self-pickup-label input[type="checkbox"] {
            accent-color: #ff6ef7;
            width: 15px;
            height: 15px;
        }

        .reschedule-submit {
            min-width: 190px;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            .order-info {
                padding: 16px;
            }

            .order-info .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .order-info .info-value {
                text-align: left;
            }

            .reschedule-submit {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Reschedule Order</h1>
            </div>

            <div class="card-body">

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Order Information Display --}}
                <div class="order-info">
                    <h5>Order Information</h5>

                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#{{ $order->id }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Service:</span>
                        <span class="info-value">{{ $order->service_type ?? 'Full Service' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Current Pickup Date:</span>
                        <span class="info-value">
                            {{ $order->pickup_date ? $order->pickup_date->format('F d, Y') : '—' }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Current Pickup Time:</span>
                        <span class="info-value">{{ $order->pickup_time ?? '—' }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Current Delivery Date:</span>
                        <span class="info-value">
                            {{ $order->delivery_date ? $order->delivery_date->format('F d, Y') : '—' }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Current Delivery Time:</span>
                        <span class="info-value">{{ $order->delivery_time ?? '—' }}</span>
                    </div>
                </div>

                <form action="{{ route('order.reschedule.submit', $order) }}" method="POST">
                    @csrf

                    <div class="grid">
                        <div class="field">
                            <label>New Pickup Date <span class="hint">*</span></label>
                            <input
                                type="date"
                                name="pickup_date"
                                id="pickup_date"
                                required
                                value="{{ old('pickup_date', $order->pickup_date ? $order->pickup_date->format('Y-m-d') : '') }}">
                        </div>

                        <div class="field">
                            <label>New Pickup Time (8am – 6pm) <span class="hint">*</span></label>
                            <select name="pickup_time" id="pickup_time" required>
                                <option value="">Select time</option>
                                @foreach (['8 am', '9 am', '10 am', '11 am', '12 pm', '1 pm', '2 pm', '3 pm', '4 pm', '5 pm', '6 pm'] as $time)
                                    <option value="{{ $time }}"
                                        {{ old('pickup_time', $order->pickup_time) === $time ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>New Delivery Date <span class="hint">(Optional)</span></label>
                            <input
                                type="date"
                                name="delivery_date"
                                id="delivery_date"
                                value="{{ old('delivery_date', $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}">

                            <div class="self-pickup-wrap">
                                <label class="self-pickup-label">
                                    <input
                                        type="checkbox"
                                        name="self_pickup"
                                        id="self_pickup"
                                        value="1"
                                        {{ old('self_pickup') ? 'checked' : '' }}>
                                    I will pickup my own laundry
                                </label>
                            </div>
                        </div>

                        <div class="field">
                            <label>New Delivery Time (8am – 8pm)</label>
                            <select name="delivery_time" id="delivery_time">
                                <option value="">Select time</option>
                                @foreach (['8 am', '9 am', '10 am', '11 am', '12 pm', '1 pm', '2 pm', '3 pm', '4 pm', '5 pm', '6 pm', '7 pm', '8 pm'] as $time)
                                    <option value="{{ $time }}"
                                        {{ old('delivery_time', $order->delivery_time) === $time ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Reason for Reschedule --}}
                    <div class="field" style="margin-top: 20px;">
                        <label>Reason for Rescheduling <span class="hint">(Optional)</span></label>
                        <textarea name="reschedule_reason" placeholder="Please let us know why you need to reschedule...">{{ old('reschedule_reason') }}</textarea>
                    </div>

                    {{-- Notice --}}
                    <div class="notice">
                        <p>Important Notes:</p>
                        <ul>
                            <li>Rescheduling is subject to availability.</li>
                            <li>E&B Laundry Hub is open daily from <strong>8 am to 8 pm</strong>.</li>
                            <li>Pickups are only available from <strong>8 am to 6 pm</strong>.</li>
                            <li>You can only reschedule orders that are <strong>pending</strong> or <strong>for pickup</strong>.</li>
                            <li>For urgent concerns, contact <strong>09301902025</strong> or message us on Messenger.</li>
                        </ul>
                    </div>

                    {{-- Actions --}}
                    <div class="actions">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-back">Cancel</a>
                        <button type="submit" class="btn btn-primary reschedule-submit">Confirm Reschedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, "0");
            const dd = String(today.getDate()).padStart(2, "0");
            const minDate = `${yyyy}-${mm}-${dd}`;

            const pickupInput = document.getElementById("pickup_date");
            const deliveryInput = document.getElementById("delivery_date");
            const pickupTimeSel = document.getElementById("pickup_time");
            const deliveryTimeSel = document.getElementById("delivery_time");
            const selfPickup = document.getElementById("self_pickup");

            if (pickupInput) pickupInput.setAttribute("min", minDate);
            if (deliveryInput) deliveryInput.setAttribute("min", minDate);

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
            }

            pickupInput?.addEventListener("change", updateTimeOptions);
            deliveryInput?.addEventListener("change", updateTimeOptions);
            selfPickup?.addEventListener("change", applySelfPickupEffects);

            updateTimeOptions();
            applySelfPickupEffects();
        });
    </script>
</body>

</html>