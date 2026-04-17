{{-- resources/views/admin/customers/loyalty.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Loyalty Membership</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
          rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard/loyalty.css') }}" rel="stylesheet">
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
        {{-- Sidebar --}}
        <aside id="side-bar" class="d-flex flex-column flex-shrink-0 p-3">
            <div class="d-flex justify-content-end mb-2">
                <button type="button"
                        id="sidebarToggle"
                        class="btn btn-sm sidebar-toggle-btn"
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
                       id="nav-customers"
                       aria-current="page">
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
                <button id="btn-admin"
                        class="btn dropdown-toggle d-flex align-items-center"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="20"
                         height="20"
                         fill="currentColor"
                         class="bi bi-person-circle me-2"
                         viewBox="0 0 16 16">
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

        {{-- Main Content --}}
        <main class="main-content p-4">
            <div class="loyalty-page-top">
                <div>
                    <h1 class="page-title mb-1">Loyalty Membership</h1>
                </div>

                <a href="{{ route('customers') }}" class="btn loyalty-back-btn">
                    <i class="bi bi-arrow-left-short"></i>
                    Back
                </a>
            </div>

            <hr>

            <div class="container-fluid">
                <div class="card loyalty-card">
                    <div class="card-header loyalty-header" id="card-header">
                        <div class="loyalty-summary-grid">
                            <div class="loyalty-customer-block">

                                <div class="loyalty-customer-meta">
                                    <h2 class="loyalty-customer-name mb-1">{{ $user->name }}</h2>
                                    <div class="loyalty-customer-line">
                                        <span>{{ $user->email }}</span>
                                        <span class="dot">•</span>
                                        <span>{{ $user->phone_number }}</span>
                                    </div>
                                    <div class="loyalty-customer-line">
                                        Completed orders:
                                        <strong>{{ $completed }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="loyalty-stats-grid">
                                <div class="loyalty-stat-card">
                                    <div class="loyalty-stat-label">Progress</div>
                                    <div class="loyalty-progress-wrap">
                                        <div class="progress loyalty-progress">
                                            <div class="progress-bar loyalty-progress-bar"
                                                 role="progressbar"
                                                 style="width: {{ ($progress / 10) * 100 }}%;"
                                                 aria-valuenow="{{ $progress }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="10">
                                            </div>
                                        </div>
                                        <span class="loyalty-progress-text">{{ $progress }}/10</span>
                                    </div>
                                    <div class="loyalty-stat-help">Orders toward next free 1 load Full Service</div>
                                </div>

                                <div class="loyalty-stat-card earned">
                                    <div class="loyalty-stat-label">Rewards Earned</div>
                                    <div class="loyalty-earned-value">{{ $earned }}</div>
                                    <div class="loyalty-stat-help">Free full service rewards accumulated</div>
                                </div>
                            </div>
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
                        <div class="loyalty-section-title-wrap">
                            <h3 class="loyalty-section-title mb-0">Recent Qualified Orders</h3>
                            <span class="loyalty-section-count">{{ count($recent) }} shown</span>
                        </div>

                        <div class="loyalty-table-scroll">
                            <table class="table table-sm table-borderless align-middle loyalty-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date &amp; Time</th>
                                        <th>Category</th>
                                        <th>Service Type</th>
                                        <th>Load Qty.</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @forelse ($recent as $i => $order)
                                    @php
                                        $isWalkin = ($order->category ?? null) === 'walkin';
                                        $map = [
                                            'pending'        => 'status-pending',
                                            'for_pickup'     => 'status-pickup',
                                            'on_going'       => 'status-ongoing',
                                            'for_delivery'   => 'status-delivery',
                                            'to_be_received' => 'status-received',
                                            'canceled'       => 'status-canceled',
                                            'completed'      => 'status-completed',
                                        ];
                                        $badge = $map[$order->status] ?? 'status-pending';
                                    @endphp

                                    <tr>
                                        <td class="text-center fw-semibold">{{ $i + 1 }}</td>

                                        <td>
                                            <div class="date-main">
                                                {{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }}
                                            </div>
                                            <div class="date-sub">
                                                {{ $order->created_at->timezone(config('app.timezone'))->format('h:i A') }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <span class="category-pill {{ $isWalkin ? 'walkin' : 'pickup' }}">
                                                {{ $isWalkin ? 'Walk-in' : 'Pickup & Delivery' }}
                                            </span>
                                        </td>

                                        <td class="text-center">{{ $order->service_type }}</td>

                                        <td class="text-center fw-semibold">{{ $order->load_qty }}</td>

                                        <td class="text-center fw-semibold">
                                            ₱ {{ number_format($order->display_total ?? ($order->total ?? ($order->total_amount ?? 0)), 0) }}
                                        </td>

                                        <td class="text-center">
                                            <span class="status-pill {{ $badge }}">
                                                {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No orders yet.</td>
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
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            const body = document.body;
            const KEY  = 'eb_admin_sidebar_collapsed';

            try {
                if (localStorage.getItem(KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            btn.addEventListener('click', function (e) {
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