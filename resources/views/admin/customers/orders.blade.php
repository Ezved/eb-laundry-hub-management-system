<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $user->name }} Order History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard/customerOrderHistory.css') }}" rel="stylesheet">
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
                        id="nav-dashboard">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('customers') }}"
                        class="nav-link {{ request()->routeIs('customers') ? 'active' : '' }}"
                        id="nav-customers" aria-current="page"> 
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
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h1 class="page-title mb-1">Order History</h1>
                </div>

                <a href="{{ route('customers') }}" class="btn btn-history-back">
                    <i class="bi bi-arrow-left-short"></i>
                    Back
                </a>
            </div>

            <hr>

            <div class="container-fluid">
                <div class="card">
                    <div class="card-header d-flex flex-column gap-1" id="card-header">
                        <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">
                            <div class="d-flex flex-column">
                                <h2 class="mb-1 fw-bold" style="color:#173F7B;">
                                    {{ $user->name }}
                                </h2>

                                <div class="small text-muted">
                                    <span>{{ $user->email }}</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $user->phone_number }}</span>
                                </div>

                                <div class="small mt-1" style="color:#173F7B; font-weight:600;">
                                    Number of Orders:
                                    {{ method_exists($orders, 'total') ? $orders->total() : ($orders?->count() ?? 0) }}
                                </div>
                            </div>

                            <form method="GET" id="history-filter-form"
                                class="d-flex align-items-end gap-2 flex-wrap history-filter-form">
                                <div>
                                    <label class="history-filter-label">Show orders on</label>
                                    <input type="date"
                                        name="date"
                                        class="form-control form-control-sm history-filter-control"
                                        value="{{ request('date') }}"
                                        onchange="this.form.submit()">
                                </div>

                                <div>
                                    <label class="history-filter-label">Sort by date</label>
                                    <select name="sort"
                                        id="history-sort"
                                        class="form-select form-select-sm history-filter-control"
                                        onchange="handleHistorySortChange(this)"
                                        onclick="resetHistoryIfAllSelected(this)">
                                        <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>
                                            Newest first
                                        </option>
                                        <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>
                                            Oldest first
                                        </option>
                                        <option value="all" {{ request('sort') === 'all' ? 'selected' : '' }}>
                                            All orders
                                        </option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success mx-3 mt-3 mb-0">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('fail'))
                        <div class="alert alert-danger mx-3 mt-3 mb-0">
                            {{ session('fail') }}
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive-md recent-orders-table-scroll history-orders-table-scroll">
                            <table class="table table-sm table-borderless align-middle admin-orders-table history-orders-table">
                                <thead>
                                    <tr>
                                        <th>Date &amp; Time</th>
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Customer Information</th>
                                        <th>Order Details</th>
                                        <th>Total Payment</th>
                                        <th>Receipt</th>
                                        <th>Payment Status</th>
                                        <th>Laundry Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                        @php
                                            $isWalkin = $order->category === 'walkin';

                                            $statusVal = strtolower((string) ($order->status ?? ''));
                                            $isCanceled = $statusVal === 'canceled';
                                            $isCompleted = $statusVal === 'completed';
                                            $isNewByStatus = in_array($statusVal, ['new', 'pending', 'for_pickup']);
                                            $createdLocal = optional($order->created_at)?->setTimezone(config('app.timezone'));
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

                                            <td class="text-start">
                                                {{ $isWalkin ? 'Walk-in' : 'Pickup & Delivery' }}
                                            </td>

                                            <td class="text-start">
                                                {{ $isWalkin ? ($order->pickup_name ?? 'Walk-in / —') : ($order->user->name ?? '—') }}
                                            </td>

                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#orderCustomerModal-{{ $order->id }}">
                                                    View
                                                </button>

                                                @php
                                                    $infoName =
                                                        $order->pickup_name ?? (optional($order->user)->name ?? (optional($order->customer)->name ?? '—'));
                                                    $infoEmail =
                                                        optional($order->user)->email ?? (data_get($order, 'meta.email') ?? (optional($order->customer)->email ?? '—'));
                                                    $infoPhone =
                                                        $order->pickup_phone ?? (optional($order->user)->phone_number ?? (optional($order->customer)->phone_number ?? (data_get($order, 'meta.phone_number') ?? '—')));
                                                    $infoAddress =
                                                        $order->pickup_address ?? (optional($order->user)->address ?? (optional($order->customer)->address ?? (data_get($order, 'meta.address') ?? '—')));
                                                    $infoLocation =
                                                        optional($order->user)->location_details ?? (data_get($order, 'meta.location_details') ?? '—');
                                                @endphp

                                                <div class="modal fade"
                                                    id="orderCustomerModal-{{ $order->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="orderCustomerModalLabel-{{ $order->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header history-modal-header">
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
                                                                    data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('admin.orders.show', $order) }}">
                                                    View
                                                </a>
                                            </td>

                                            <td>
                                                ₱ {{ number_format($order->display_total ?? ($order->total ?? ($order->total_amount ?? 0)), 0) }}
                                            </td>

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
                                                        class="d-flex align-items-center justify-content-center history-select-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        @php $pay = $order->payment_status ?? 'unpaid'; @endphp
                                                        <select name="payment_status"
                                                            class="form-select form-select-sm undo-select js-colored-select"
                                                            onchange="this.form.submit()">
                                                            <option value="unpaid" {{ $pay === 'unpaid' ? 'selected' : '' }}>
                                                                Not Paid
                                                            </option>
                                                            <option value="paid" {{ $pay === 'paid' ? 'selected' : '' }}>
                                                                Paid
                                                            </option>
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
                                                        class="d-flex align-items-center justify-content-center history-select-form">
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
                                                        @endphp

                                                        <select name="status"
                                                            class="form-select form-select-sm undo-select js-colored-select"
                                                            onchange="this.form.submit()">
                                                            @foreach ($statuses as $value => $label)
                                                                <option value="{{ $value }}"
                                                                    {{ $status === $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                No orders found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($orders instanceof \Illuminate\Contracts\Pagination\Paginator && $orders->hasPages())
                    <div class="history-pagination-wrap">
                        <div class="small text-muted">
                            Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }}
                            of {{ $orders->total() }} orders
                        </div>

                        {{ $orders->appends([
                            'sort' => request('sort', 'desc'),
                            'date' => request('date'),
                        ])->links('pagination::simple-bootstrap-5') }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function handleHistorySortChange(sel) {
            const form = sel.form;
            if (!form) return;

            if (sel.value === 'all') {
                const dateInput = form.querySelector('input[name="date"]');
                if (dateInput) dateInput.value = '';
            }

            form.submit();
        }

        function resetHistoryIfAllSelected(sel) {
            if (!sel || sel.value !== 'all') return;

            const form = sel.form;
            if (!form) return;

            const dateInput = form.querySelector('input[name="date"]');
            if (dateInput && dateInput.value) {
                dateInput.value = '';
                form.submit();
            }
        }
    </script>

    <script>
        (function() {
            document.querySelectorAll('select.js-colored-select').forEach(function(sel) {
                sel.style.color = '';
                Array.from(sel.options).forEach(opt => {
                    opt.style.color = '';
                });

                sel.addEventListener('change', () => {
                    sel.style.color = '';
                    Array.from(sel.options).forEach(opt => {
                        opt.style.color = '';
                    });
                });
            });
        })();
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