<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <!-- Mobile top bar -->
    <div class="mobile-topbar d-lg-none">
        <button type="button" id="mobileSidebarToggle" class="btn mobile-toggle-btn" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="mobile-brand">
            <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
            <span>E&B Laundry Hub</span>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-shell">
        <!-- Sidebar -->
        <aside id="side-bar" class="d-flex flex-column flex-shrink-0 p-3">
            <div class="d-flex justify-content-end mb-2">
                <button type="button" id="sidebarToggle" class="btn btn-sm sidebar-toggle-btn"
                    title="Collapse sidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="logo-wrapper">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
                </a>
            </div>
            <hr>

            <ul class="nav nav-pills flex-column mb-auto">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        id="nav-dashboard" aria-current="page">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('customers') }}"
                        class="nav-link {{ request()->routeIs('customers') ? 'active' : '' }}"
                        id="nav-customers">
                        <i class="bi bi-people-fill me-2"></i>
                        <span>Customers</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.delivery.calendar') }}"
                        class="nav-link {{ request()->routeIs('admin.delivery.calendar') ? 'active' : '' }}"
                        id="nav-delivery-calendar">
                        <i class="bi bi-calendar me-2"></i>
                        <span>Delivery Calendar</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.sales.report') }}"
                        class="nav-link {{ request()->routeIs('admin.sales.report') ? 'active' : '' }}"
                        id="nav-sales-report">
                        <i class="bi bi-clipboard-data me-2"></i>
                        <span>Sales Report</span>
                    </a>
                </li>

                <hr>

                <li>
                    <a href="{{ route('users') }}"
                        class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}"
                        id="nav-users">
                        <i class="bi bi-person-gear me-2"></i>
                        <span>Users</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('services') }}"
                        class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}"
                        id="nav-services">
                        <i class="bi bi-basket2 me-2"></i>
                        <span>Services</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.feedback.index') }}"
                        class="nav-link {{ request()->routeIs('admin.feedback.index') ? 'active' : '' }}"
                        id="nav-feedbacks">
                        <i class="bi bi-chat-dots me-2"></i>
                        <span>Feedbacks</span>
                    </a>
                </li>
            </ul>

            <hr>

            <div class="dropdown text-end">
                <button id="btn-admin" class="btn dropdown-toggle d-flex align-items-center" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-person-circle me-2" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                        <path fill-rule="evenodd"
                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                    </svg>
                    <span class="fw-bold">Admin</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item fw-bold" id="admin-settings"
                        href="{{ route('admin.dashboard.editAdminProfileInfo') }}">
                        <i class="bi bi-person-circle me-2"></i>
                        Profile
                    </a>

                    <li class="">
                        <div class="d-flex align-items-center justify-content-between">
                            <form action="{{ route('logout') }}" method="POST" class="flex-grow-1 mb-0">
                                @csrf
                                <button type="submit" id="btn-logout" class="dropdown-item fw-bold w-100 text-start">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content p-4">
            <h1 class="page-title">Admin Dashboard</h1>
            <hr>

            <div class="container-fluid">
                <div class="card">
                    <div class="card-header d-flex flex-column gap-1" id="card-header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <h2 class="mb-0 fw-bold" style="color:#173F7B;">
                                    Recent Orders Today
                                </h2>
                                <span class="orders-count ms-2"
                                    style="font-size:.9rem; color:#173F7B; font-weight:600;">
                                    ({{ $orders?->count() ?? 0 }})
                                </span>
                            </div>

                            @php
                                $walkinLimit = $todayOrdersLimit ?? \App\Support\Capacity::DAILY_LIMIT;
                                $walkinLoads = $todayOrdersCount ?? 0;
                                $walkinLocked = isset($walkinDisabled)
                                    ? $walkinDisabled
                                    : ($walkinLoads >= $walkinLimit);
                            @endphp

                            <div class="btn-walkin">
                                <a href="{{ route('admin.walkin.create') }}" id="btn-booking"
                                    class="btn btn-sm {{ $walkinLocked ? 'disabled' : '' }}"
                                    @if ($walkinLocked) aria-disabled="true" tabindex="-1" @endif>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                    </svg>
                                    New Walk-In
                                </a>
                            </div>
                        </div>

                        <div class="quick-tabs-row">
                            <div id="order-quick-tabs" class="btn-group range-like-nav" role="group"
                                aria-label="Order quick filters">
                                <a href="#" class="btn btn-sm" data-status="all" role="button">Orders</a>
                                <a href="#" class="btn btn-sm" data-status="pickup" role="button">Pick up</a>
                                <a href="#" class="btn btn-sm" data-status="in_progress" role="button">In progress</a>
                                <a href="#" class="btn btn-sm" data-status="out_for_delivery" role="button">For delivery</a>
                                <a href="{{ route('admin.orders.orderHistory') }}" class="btn btn-sm" data-status="history"
                                    role="button">History</a>
                            </div>

                            <div class="loads-indicator">
                                {{ $walkinLoads }}/{{ $walkinLimit }} Loads
                                @if ($walkinLocked)
                                    <span class="text-danger ms-1">(Limit reached)</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="flash-data" data-success="{{ Session::pull('success') }}"
                        data-fail="{{ Session::pull('fail') }}"></div>

                    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
                        <div id="flashToast" class="toast" role="alert" data-bs-delay="2000"
                            aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto" id="flashTitle">Notice</strong>
                                <small>now</small>
                                <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast"
                                    aria-label="Close"></button>
                            </div>
                            <div class="toast-body" id="flashMsg"></div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive-md recent-orders-table-scroll">
                            <table class="table table-sm table-borderless align-middle admin-orders-table">
                                <thead>
                                    <tr>
                                        <th>Date &amp; Time</th>
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Customer Information</th>
                                        <th>Order Summary</th>
                                        <th>Total Payment</th>
                                        <th>Receipt</th>
                                        <th>Payment Status</th>
                                        <th>Laundry Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders ?? [] as $order)
                                        @php
                                            $isWalkin = $order->category === 'walkin';

                                            $statusVal = strtolower((string) ($order->status ?? ''));
                                            $isCanceled = $statusVal === 'canceled';
                                            $isCompleted = $statusVal === 'completed';
                                            $isNewByStatus = in_array($statusVal, ['new', 'pending', 'for_pickup']);
                                            $createdLocal = optional($order->created_at)?->setTimezone(
                                                config('app.timezone'),
                                            );
                                            $isNewByDate = $createdLocal ? $createdLocal->isToday() : false;
                                        @endphp

                                        <tr @class([
                                            'table-danger' => $isCanceled,
                                            'table-success' => !$isCanceled && $isCompleted,
                                            'bg-success text-white' =>
                                                !$isCanceled && !$isCompleted && ($isNewByStatus || $isNewByDate),
                                        ])
                                            data-status="{{ strtolower($order->status ?? '') }}"
                                            data-payment="{{ strtolower($order->payment_status ?? 'unpaid') }}">

                                            <td>
                                                <div>
                                                    <strong>{{ optional($order->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="text-muted small">
                                                    {{ optional($order->created_at)->timezone(config('app.timezone'))->format('h:i A') }}
                                                </div>
                                            </td>

                                            <td class="text-start">{{ $isWalkin ? 'Walk-in' : 'Pickup & Delivery' }}</td>

                                            <td class="text-start">
                                                {{ $isWalkin ? $order->pickup_name ?? 'Walk-in / —' : $order->user->name ?? '—' }}
                                            </td>

                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#orderCustomerModal-{{ $order->id }}">View</button>

                                                @php
                                                    $infoName =
                                                        $order->pickup_name ??
                                                        (optional($order->user)->name ??
                                                            (optional($order->customer)->name ?? '—'));
                                                    $infoEmail =
                                                        optional($order->user)->email ??
                                                        (data_get($order, 'meta.email') ??
                                                            (optional($order->customer)->email ?? '—'));
                                                    $infoPhone =
                                                        $order->pickup_phone ??
                                                        (optional($order->user)->phone_number ??
                                                            (optional($order->customer)->phone_number ??
                                                                (data_get($order, 'meta.phone_number') ?? '—')));
                                                    $infoAddress =
                                                        $order->pickup_address ??
                                                        (optional($order->user)->address ??
                                                            (optional($order->customer)->address ??
                                                                (data_get($order, 'meta.address') ?? '—')));
                                                    $infoLocation =
                                                        optional($order->user)->location_details ??
                                                        (data_get($order, 'meta.location_details') ?? '—');
                                                @endphp

                                                <div class="modal fade" id="orderCustomerModal-{{ $order->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="orderCustomerModalLabel-{{ $order->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header"
                                                                style="background:#173F7B;color:#fff;">
                                                                <h5 class="modal-title"
                                                                    id="orderCustomerModalLabel-{{ $order->id }}">
                                                                    Customer Information
                                                                </h5>
                                                                <button type="button"
                                                                    class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <dl class="row mb-0">
                                                                    <dt class="col-sm-4">Name</dt>
                                                                    <dd class="col-sm-8">{{ $infoName }}</dd>
                                                                    <dt class="col-sm-4">Email</dt>
                                                                    <dd class="col-sm-8">{{ $infoEmail }}</dd>
                                                                    <dt class="col-sm-4">Phone</dt>
                                                                    <dd class="col-sm-8">{{ $infoPhone }}</dd>
                                                                    <dt class="col-sm-4">Address</dt>
                                                                    <dd class="col-sm-8">{{ $infoAddress }}</dd>
                                                                    <dt class="col-sm-4">Location Details</dt>
                                                                    <dd class="col-sm-8">{{ $infoLocation }}</dd>
                                                                </dl>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-secondary btn-sm"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('admin.orders.show', $order) }}">View</a>
                                            </td>

                                            <td>
                                            ₱ {{ number_format((float) ($order->display_total ?? ($order->total ?? ($order->total_amount ?? 0))), 2) }}                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $hasFinalReceipt = !is_null($order->display_total);
                                                @endphp

                                                @if ($hasFinalReceipt)
                                                    <a class="link-primary"
                                                        href="{{ route('orders.receipt.show', $order) }}">
                                                        View
                                                    </a>
                                                @else
                                                    <span class="text-muted small">Unavailable</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($order->status === 'canceled')
                                                    <span class="badge bg-secondary">N/A</span>
                                                @else
                                                    <form action="{{ route('admin.orders.payment', $order) }}"
                                                        method="POST"
                                                        class="d-flex align-items-center justify-content-center"
                                                        style="background:white;">
                                                        @csrf
                                                        @method('PATCH')
                                                        @php $pay = $order->payment_status ?? 'unpaid'; @endphp
                                                        <select name="payment_status"
                                                            class="form-select form-select-sm undo-select js-colored-select"
                                                            onchange="this.form.submit()">
                                                            <option value="unpaid" data-color="#a30000"
                                                                {{ $pay === 'unpaid' ? 'selected' : '' }}>Not Paid</option>
                                                            <option value="paid" data-color="#136f2a"
                                                                {{ $pay === 'paid' ? 'selected' : '' }}>Paid</option>
                                                        </select>
                                                    </form>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @php $status = $order->status; @endphp
                                                @if ($status === 'canceled')
                                                    <span class="badge bg-danger">Canceled</span>
                                                @else
                                                    <form action="{{ route('admin.orders.status', $order) }}"
                                                        method="POST"
                                                        class="d-flex align-items-center justify-content-center"
                                                        style="background:white;">
                                                        @csrf
                                                        @method('PATCH')
                                                        @php
                                                            $statuses = $isWalkin
                                                                ? [
                                                                    'pending' => 'Pending',
                                                                    'on_going' => 'On Going',
                                                                    'to_be_received' => 'To be Received',
                                                                    'completed' => 'Completed',
                                                                ]
                                                                : [
                                                                    'pending' => 'Pending',
                                                                    'for_pickup' => 'For Pickup',
                                                                    'on_going' => 'On Going',
                                                                    'to_be_received' => 'To be Received',
                                                                    'for_delivery' => 'For Delivery',
                                                                    'completed' => 'Completed',
                                                                    'canceled' => 'Canceled',
                                                                ];
                                                            $statusColors = [
                                                                'pending' => '#444',
                                                                'for_pickup' => '#0b5cad',
                                                                'on_going' => '#7a4d00',
                                                                'to_be_received' => '#5b3dac',
                                                                'for_delivery' => '#134a9e',
                                                                'completed' => '#136f2a',
                                                                'canceled' => '#dc3545',
                                                            ];
                                                        @endphp
                                                        <select name="status"
                                                            class="form-select form-select-sm undo-select js-colored-select"
                                                            onchange="this.form.submit()">
                                                            @foreach ($statuses as $value => $label)
                                                                <option value="{{ $value }}"
                                                                    data-color="{{ $statusColors[$value] ?? '#333' }}"
                                                                    {{ $status === $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#receiptModal-{{ $order->id }}">
                                                    Create Receipt
                                                </button>

                                                <div class="modal fade" id="receiptModal-{{ $order->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="receiptModalLabel-{{ $order->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <form method="POST"
                                                            action="{{ route('admin.orders.receipt.prepare', $order) }}"
                                                            class="modal-content receipt-finalize"
                                                            data-order-id="{{ $order->id }}">
                                                            @csrf

                                                            <div class="modal-header"
                                                                style="background:#173F7B;color:#fff;">
                                                                <h5 class="modal-title"
                                                                    id="receiptModalLabel-{{ $order->id }}">
                                                                    Finalize Receipt
                                                                </h5>
                                                                <button type="button"
                                                                    class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>

                                                            @php
                                                                $currentService =
                                                                    $order->service_type ??
                                                                    (optional($order->service)->name ?? 'Full Service');

                                                                $baseTotal =
                                                                    $order->display_total ??
                                                                    ($order->total ?? ($order->total_amount ?? 0));

                                                                $perLoadRate = 180;
                                                                $startLoadQty = (int) ($order->load_qty ?? 0);
                                                                $startOver8 = $order->exceeds_8kg ? 1 : 0;
                                                            @endphp

                                                            <div class="modal-body">
                                                                <div class="container-fluid">
                                                                    <div class="row mb-3">
                                                                        <label class="col-sm-4 col-form-label fw-semibold">
                                                                            Service
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="receipt-service-select">
                                                                                <select
                                                                                    id="serviceType-{{ $order->id }}"
                                                                                    class="form-select pe-4" disabled>
                                                                                    <option value="Full Service"
                                                                                        {{ $currentService === 'Full Service' ? 'selected' : '' }}>
                                                                                        Full Service
                                                                                    </option>
                                                                                    <option value="Drop Service"
                                                                                        {{ $currentService === 'Drop Service' ? 'selected' : '' }}>
                                                                                        Drop Service
                                                                                    </option>
                                                                                    <option value="Self Service"
                                                                                        {{ $currentService === 'Self Service' ? 'selected' : '' }}>
                                                                                        Self Service
                                                                                    </option>
                                                                                </select>
                                                                                <input type="hidden" name="service_type"
                                                                                    value="{{ $currentService }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-3">
                                                                        <label class="col-sm-4 col-form-label fw-semibold"
                                                                            for="finalLoadQty-{{ $order->id }}">
                                                                            Loads
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="number"
                                                                                class="form-control js-final-load"
                                                                                id="finalLoadQty-{{ $order->id }}"
                                                                                name="final_load_qty" min="0"
                                                                                step="1"
                                                                                value="{{ $startLoadQty }}"
                                                                                required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-3 align-items-center receipt-over8-row">
                                                                        <label class="col-sm-4 col-form-label fw-semibold"
                                                                            for="exceeds8kg-{{ $order->id }}">
                                                                            Over 8 kg
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input js-over8"
                                                                                    type="checkbox"
                                                                                    name="exceeds_8kg"
                                                                                    id="exceeds8kg-{{ $order->id }}"
                                                                                    value="1"
                                                                                    {{ $order->exceeds_8kg ? 'checked' : '' }}>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-3">
                                                                        <label class="col-sm-4 col-form-label fw-semibold"
                                                                            for="finalTotal-{{ $order->id }}">
                                                                            Total (₱)
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <input type="number"
                                                                                class="form-control js-final-total"
                                                                                id="finalTotal-{{ $order->id }}"
                                                                                name="final_total" min="0"
                                                                                step="0.01"
                                                                                value="{{ number_format($baseTotal, 2, '.', '') }}"
                                                                                data-base-total="{{ number_format($baseTotal, 2, '.', '') }}"
                                                                                data-per-load="{{ $perLoadRate }}"
                                                                                data-start-load="{{ $startLoadQty }}"
                                                                                data-start-over8="{{ $startOver8 }}"
                                                                                data-surcharge="40" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                    class="btn btn-secondary btn-sm"
                                                                    data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                    Next
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">No orders today.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('flash-data');
            if (!el) return;
            const ok = el.dataset.success?.trim();
            const err = el.dataset.fail?.trim();
            const toastEl = document.getElementById('flashToast');
            const titleEl = document.getElementById('flashTitle');
            const msgEl = document.getElementById('flashMsg');
            if (!toastEl || !msgEl || (!ok && !err)) return;

            const t = new bootstrap.Toast(toastEl);
            toastEl.classList.remove('text-bg-success', 'text-bg-danger');

            if (ok) {
                toastEl.classList.add('text-bg-success');
                titleEl.textContent = 'Success';
                msgEl.textContent = ok;
            } else {
                toastEl.classList.add('text-bg-danger');
                titleEl.textContent = 'Error';
                msgEl.textContent = err;
            }

            t.show();
        });
    </script>

    <script>
        (function() {
            document.querySelectorAll('select.js-colored-select').forEach(function(sel) {
                sel.style.color = '';
                sel.classList.remove('pay-paid', 'pay-unpaid');

                Array.from(sel.options).forEach(opt => {
                    opt.style.color = '';
                });

                sel.addEventListener('change', () => {
                    sel.style.color = '';
                    sel.classList.remove('pay-paid', 'pay-unpaid');

                    Array.from(sel.options).forEach(opt => {
                        opt.style.color = '';
                    });
                });
            });
        })();
    </script>

    <script>
        (function() {
            const KEY = 'admin.quickTab';
            const group = document.getElementById('order-quick-tabs');
            if (!group) return;

            const links = Array.from(group.querySelectorAll('a[data-status]'));
            const rowsQ = '.admin-orders-table tbody tr[data-status]';
            const norm = (v) => String(v || '')
                .trim().toLowerCase()
                .replace(/[\s-]+/g, '_')
                .replace(/[^\w]/g, '');

            const countEl = document.querySelector('.orders-count');

            function updateCount() {
                const visible = Array.from(document.querySelectorAll(rowsQ)).filter(tr => !tr.hidden).length;
                if (countEl) countEl.textContent = `(${visible})`;
            }

            function setActive(key) {
                links.forEach(a => a.classList.toggle('active', a.dataset.status === key));
                try {
                    localStorage.setItem(KEY, key);
                } catch {}
            }

            function applyFilter(key) {
                const rows = document.querySelectorAll(rowsQ);

                rows.forEach(tr => {
                    const st = norm(tr.dataset.status);
                    const paid = norm(tr.dataset.payment);

                    const keepActive = !(st === 'completed' && paid === 'paid');

                    if (key === 'all') {
                        tr.hidden = !keepActive;
                        return;
                    }

                    let match = false;
                    if (key === 'pickup') {
                        match = (st === 'for_pickup');
                    } else if (key === 'in_progress') {
                        match = (st === 'on_going' || st === 'in_progress');
                    } else if (key === 'out_for_delivery') {
                        match = (st === 'for_delivery' || st === 'to_be_received' || st === 'out_for_delivery');
                    }

                    tr.hidden = !(keepActive && match);
                });

                updateCount();
            }

            group.addEventListener('click', (e) => {
                const a = e.target.closest('a[data-status]');
                if (!a) return;

                const key = a.dataset.status;
                if (key === 'history') return;

                e.preventDefault();
                setActive(key);
                applyFilter(key);
            });

            const saved = localStorage.getItem(KEY);
            const first = (saved && group.querySelector(`[data-status="${saved}"]`)) ? saved : 'all';
            setActive(first);
            applyFilter(first);
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form.receipt-finalize').forEach(function(form) {
                const checkbox = form.querySelector('.js-over8');
                const totalInput = form.querySelector('.js-final-total');
                const loadInput = form.querySelector('.js-final-load');

                if (!checkbox || !totalInput || !loadInput) return;

                const SURCHARGE = parseFloat(totalInput.dataset.surcharge || '40') || 40;
                const baseTotal = parseFloat(totalInput.dataset.baseTotal || '0') || 0;
                const perLoad = parseFloat(totalInput.dataset.perLoad || '0') || 0;
                const startLoad = parseInt(totalInput.dataset.startLoad || loadInput.value || '0', 10) || 0;
                const startOver8 = (totalInput.dataset.startOver8 === '1');

                function recalc() {
                    const curLoad = parseInt(loadInput.value || '0', 10) || 0;
                    const curOver8 = checkbox.checked;

                    const deltaLoad = curLoad - startLoad;
                    const deltaSurcharge = (curOver8 ? 1 : 0) - (startOver8 ? 1 : 0);

                    const final = baseTotal +
                        (deltaLoad * perLoad) +
                        (deltaSurcharge * SURCHARGE);

                    totalInput.value = final.toFixed(2);
                }

                const modalEl = form.closest('.modal');
                if (modalEl) {
                    modalEl.addEventListener('show.bs.modal', function() {
                        loadInput.value = startLoad;
                        checkbox.checked = startOver8;
                        totalInput.value = baseTotal.toFixed(2);
                    });
                }

                loadInput.addEventListener('input', recalc);
                checkbox.addEventListener('change', recalc);
                totalInput.value = baseTotal.toFixed(2);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            const body = document.body;
            const KEY = 'eb_admin_sidebar_collapsed';

            try {
                if (localStorage.getItem(KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                body.classList.toggle('sidebar-collapsed');

                try {
                    localStorage.setItem(
                        KEY,
                        body.classList.contains('sidebar-collapsed') ? '1' : '0'
                    );
                } catch (e) {}
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileBtn = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (mobileBtn) {
                mobileBtn.addEventListener('click', function() {
                    document.body.classList.add('sidebar-mobile-open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            }

            document.querySelectorAll('#side-bar .nav-link, #side-bar .dropdown-item').forEach(el => {
                el.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            });
        });
    </script>
</body>

</html>