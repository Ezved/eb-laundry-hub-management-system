{{-- resources/views/user/orderHistory/orderHistory.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">

    <style>
        .history-wrap .orders-count {
            display: inline-block;
            margin-top: 4px;
        }

        .history-empty {
            padding: 28px 16px !important;
            color: var(--muted) !important;
            font-weight: 600;
        }

        .history-receipt-link {
            color: var(--navy) !important;
            font-weight: 700;
            text-decoration: none;
        }

        .history-receipt-link:hover {
            color: var(--pink) !important;
            text-decoration: underline;
            text-decoration-color: var(--pink);
        }

        .history-status-badge {
            min-width: 92px;
        }

        .history-paid-badge {
            min-width: 70px;
        }

        .history-table col.col-ordered {
            width: 18%;
        }

        .history-table col.col-pickup {
            width: 18%;
        }

        .history-table col.col-service {
            width: 14%;
        }

        .history-table col.col-load {
            width: 10%;
        }

        .history-table col.col-total {
            width: 12%;
        }

        .history-table col.col-payment {
            width: 10%;
        }

        .history-table col.col-status {
            width: 10%;
        }

        .history-table col.col-receipt {
            width: 8%;
        }

        .history-table td:nth-child(5) {
            font-weight: 700;
            color: var(--navy);
        }
    </style>
</head>

<body>
    <div class="mobile-topbar d-lg-none">
        <button type="button" id="mobileSidebarToggle" class="btn mobile-toggle-btn" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="mobile-brand">
            <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
            <span>E&amp;B Laundry Hub</span>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-shell">
        <aside id="side-bar" class="d-flex flex-column shrink-0 p-3">
            <div class="d-flex justify-content-end mb-2">
                <button type="button" id="sidebarToggle" class="btn btn-sm sidebar-toggle-btn" title="Collapse sidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="logo-wrapper">
                <a href="{{ route('user.dashboard') }}">
                    <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
                </a>
            </div>

            <hr>

            @php
                $is = fn(...$r) => request()->routeIs(...$r) ? 'active' : '';
            @endphp

            <ul class="nav nav-pills flex-column mb-auto">
                <li>
                    <a href="{{ route('user.dashboard') }}" class="nav-link {{ $is('user.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('loyalty.membership') }}"
                        class="nav-link {{ $is('loyalty.membership', 'loyalty_membemship') }}">
                        <i class="bi bi-award me-2"></i>
                        <span>Loyalty Membership</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.orders.history') }}"
                        class="nav-link {{ $is('user.orders.history', 'user.orderHistory') }}">
                        <i class="bi bi-clock-history me-2"></i>
                        <span>Order History</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.feedback') }}" class="nav-link {{ $is('user.feedback') }}">
                        <i class="bi bi-chat-dots me-2"></i>
                        <span>Feedback</span>
                    </a>
                </li>
            </ul>

            <hr>

            <div class="dropdown text-end">
                <button id="btn-admin" class="btn dropdown-toggle d-flex align-items-center" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-person-circle me-2" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                        <path fill-rule="evenodd"
                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                    </svg>
                    <span class="fw-bold">{{ explode(' ', auth()->user()->name)[0] }}</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item fw-bold" id="admin-settings" href="{{ route('settings.edit') }}">
                            <i class="bi bi-gear me-2"></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" id="btn-logout" class="dropdown-item fw-bold">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="main-content p-4 history-wrap">
            <h1 class="page-title">Order History</h1>
            <hr>

            <div class="container-fluid">
                <div class="card">
                    <div class="card-header" id="card-header">
                        <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h2 class="mb-0">Completed Orders</h2>
                                    <span class="orders-count">
                                        {{ $orders->count() }} order{{ $orders->count() !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                style="border-radius: 14px; border: none; box-shadow: 0 10px 26px rgba(25,135,84,.12);">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('fail'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert"
                                style="border-radius: 14px; border: none; box-shadow: 0 10px 26px rgba(220,53,69,.12);">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ session('fail') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="recent-orders-table-scroll">
                            <table class="table table-sm align-middle admin-orders-table history-table">
                                <colgroup>
                                    <col class="col-ordered">
                                    <col class="col-pickup">
                                    <col class="col-service">
                                    <col class="col-load">
                                    <col class="col-total">
                                    <col class="col-payment">
                                    <col class="col-status">
                                    <col class="col-receipt">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Date &amp; Time Ordered</th>
                                        <th>Pickup Date &amp; Time</th>
                                        <th>Service</th>
                                        <th>Load Qty.</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        @php
                                            $displayTotal = ($order->total ?? 0) > 0 ? $order->total : ($order->total_amount ?? 0);
                                        @endphp

                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $order->created_at->timezone(config('app.timezone'))->format('h:i A') }}
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    <strong>
                                                        {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') : '—' }}
                                                    </strong>
                                                </div>
                                                <div class="text-muted small">{{ $order->pickup_time ?: '—' }}</div>
                                            </td>

                                            <td>{{ $order->service_type ?? '—' }}</td>

                                            <td>{{ $order->load_qty ?? 0 }}</td>

                                            <td>₱ {{ number_format($displayTotal, 2) }}</td>

                                            <td>
                                                <span class="badge bg-success history-paid-badge">Paid</span>
                                            </td>

                                            <td>
                                                <span class="badge bg-success history-status-badge">Completed</span>
                                            </td>

                                            <td>
                                                <a href="{{ route('orders.show', $order) }}" class="history-receipt-link">
                                                    Details
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center history-empty">
                                                No completed orders yet.
                                            </td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            const body = document.body;
            const KEY = 'eb_user_sidebar_collapsed';

            try {
                if (localStorage.getItem(KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                body.classList.toggle('sidebar-collapsed');

                try {
                    localStorage.setItem(KEY, body.classList.contains('sidebar-collapsed') ? '1' : '0');
                } catch (e) {}
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileBtn = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (mobileBtn) {
                mobileBtn.addEventListener('click', function () {
                    document.body.classList.add('sidebar-mobile-open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            }

            document.querySelectorAll('#side-bar .nav-link, #side-bar .dropdown-item').forEach(el => {
                el.addEventListener('click', function () {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            });
        });
    </script>
</body>

</html>