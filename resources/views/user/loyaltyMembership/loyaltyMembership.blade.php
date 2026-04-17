<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Loyalty Membership</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/user/loyaltyMembership/loyaltyMembership.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
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
        <aside id="side-bar" class="d-flex flex-column flex-shrink-0 p-3">
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
                $loyaltyOrders = collect($orders ?? $recent ?? []);
                $completedCount = isset($completed)
                    ? $completed
                    : $loyaltyOrders->where('status', 'completed')->count();
            @endphp

            <ul class="nav nav-pills flex-column mb-auto">
                <li>
                    <a href="{{ route('user.dashboard') }}"
                       class="nav-link {{ $is('user.dashboard') }}"
                       id="nav-dashboard">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('loyalty.membership') }}"
                       class="nav-link {{ $is('loyalty.membership', 'loyalty_membemship') }}"
                       id="nav-customers">
                        <i class="bi bi-award me-2"></i>
                        <span>Loyalty Membership</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('user.orders.history') }}"
                       class="nav-link {{ $is('user.orders.history') }}"
                       id="nav-services">
                        <i class="bi bi-clock-history me-2"></i>
                        <span>Order History</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('user.feedback') }}"
                       class="nav-link {{ $is('user.feedback') }}"
                       id="nav-feedback">
                        <i class="bi bi-chat-dots me-2"></i>
                        <span>Feedback</span>
                    </a>
                </li>
            </ul>

            <hr>

            <div class="dropdown text-end mt-auto">
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
                         viewBox="0 0 16 16"
                         aria-hidden="true">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd"
                              d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
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
                    <li><hr class="dropdown-divider"></li>
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

        <main class="main-content p-4 p-lg-5">
            <div class="container-fluid">
                <div class="loyalty-page-top">
                    <div>
                        <h1 class="page-title mb-1">Loyalty Membership</h1>
                        <p class="loyalty-page-subtitle mb-0">Track your progress and free-load rewards.</p>
                    </div>
                </div>

                <hr>

                @if (session('success'))
                    <div class="alert eb-alert-success mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('fail'))
                    <div class="alert eb-alert-danger mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('fail') }}
                    </div>
                @endif

                <div class="card loyalty-card">
                    <div class="card-header loyalty-header" id="card-header">
                        <div class="loyalty-summary-grid">
                            <div class="loyalty-customer-block">
                                <div class="loyalty-avatar">
                                    <i class="bi bi-award-fill"></i>
                                </div>

                                <div class="loyalty-customer-meta">
                                    <h2 class="loyalty-customer-name mb-1">{{ auth()->user()->name }}</h2>

                                    <div class="loyalty-customer-line">
                                        <span>{{ auth()->user()->email }}</span>

                                        @if(auth()->user()->phone_number)
                                            <span class="dot">•</span>
                                            <span>{{ auth()->user()->phone_number }}</span>
                                        @endif
                                    </div>

                                    <div class="loyalty-customer-line">
                                        Completed orders:
                                        <strong>{{ $completedCount }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="loyalty-stats-grid">
                                <div class="loyalty-stat-card">
                                    <div class="loyalty-stat-label">Progress</div>

                                    <div class="loyalty-progress-wrap">
                                        <div class="progress loyalty-progress flex-grow-1">
                                            <div class="progress-bar loyalty-progress-bar"
                                                 role="progressbar"
                                                 style="width: {{ min(100, max(0, (($progress ?? 0) / 10) * 100)) }}%;"
                                                 aria-valuenow="{{ $progress ?? 0 }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="10"></div>
                                        </div>

                                        <div class="loyalty-progress-text">{{ $progress ?? 0 }}/10</div>
                                    </div>

                                    <div class="loyalty-stat-help">
                                        Next free load in
                                        <strong>{{ $nextIn ?? 0 }}</strong>
                                        order{{ ($nextIn ?? 0) == 1 ? '' : 's' }}.
                                    </div>
                                </div>

                                <div class="loyalty-stat-card earned">
                                    <div class="loyalty-stat-label">Free Loads Available</div>
                                    <div class="loyalty-earned-value">{{ $freeEarned ?? 0 }}</div>

                                    <div class="loyalty-stat-help">
                                        Claim your free load once available.
                                    </div>

                                    <form method="POST" action="{{ route('loyalty.claim') }}" class="mt-3">
                                        @csrf
                                        <button type="submit"
                                                class="loyalty-claim-btn w-100"
                                                {{ ($freeEarned ?? 0) < 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-gift-fill me-1"></i>
                                            Claim Free Load
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="loyalty-section-title-wrap">
                            <h3 class="loyalty-section-title mb-0">Eligible Order History</h3>
                            <span class="loyalty-section-count">
                                {{ $loyaltyOrders->count() }} order{{ $loyaltyOrders->count() !== 1 ? 's' : '' }}
                            </span>
                        </div>

                        <div class="loyalty-table-scroll">
                            <table class="table loyalty-table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date Ordered</th>
                                        <th>Pickup Schedule</th>
                                        <th>Service</th>
                                        <th>Loads</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($loyaltyOrders as $index => $order)
                                        @php
                                            $serviceType = strtolower($order->service_type ?? $order->service ?? 'pickup');
                                            $status = strtolower($order->status ?? 'pending');

                                            $statusClass = match($status) {
                                                'pending' => 'status-pending',
                                                'for_pickup' => 'status-pickup',
                                                'on_going' => 'status-ongoing',
                                                'for_delivery' => 'status-delivery',
                                                'to_be_received' => 'status-received',
                                                'completed' => 'status-completed',
                                                'canceled' => 'status-canceled',
                                                default => 'status-pending',
                                            };

                                            $displayTotal = ($order->total ?? 0) > 0 ? $order->total : ($order->total_amount ?? 0);
                                            $loadCount = $order->load_count ?? $order->loads ?? $order->load_qty ?? 0;
                                        @endphp

                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            <td>
                                                @if(!empty($order->created_at))
                                                    <div class="date-main">
                                                        {{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }}
                                                    </div>
                                                    <div class="date-sub">
                                                        {{ $order->created_at->timezone(config('app.timezone'))->format('h:i A') }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">No date</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if(!empty($order->pickup_date))
                                                    <div class="date-main">
                                                        {{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}
                                                    </div>
                                                    <div class="date-sub">
                                                        @if(!empty($order->pickup_time))
                                                            {{ \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}
                                                        @else
                                                            No time set
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="category-pill {{ in_array($serviceType, ['walkin', 'walk-in']) ? 'walkin' : 'pickup' }}">
                                                    {{ ucfirst($serviceType) }}
                                                </span>
                                            </td>

                                            <td>{{ $loadCount }}</td>

                                            <td>₱ {{ number_format($displayTotal, 2) }}</td>

                                            <td>
                                                <span class="status-pill {{ $statusClass }}">
                                                    {{ str_replace('_', ' ', ucfirst($status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                No loyalty records yet.
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const KEY = 'eb_user_sidebar_collapsed';

            try {
                if (localStorage.getItem(KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    body.classList.toggle('sidebar-collapsed');

                    try {
                        localStorage.setItem(KEY, body.classList.contains('sidebar-collapsed') ? '1' : '0');
                    } catch (e) {}
                });
            }

            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function () {
                    body.classList.add('sidebar-mobile-open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    body.classList.remove('sidebar-mobile-open');
                });
            }

            document.querySelectorAll('#side-bar .nav-link, #side-bar .dropdown-item').forEach(el => {
                el.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        body.classList.remove('sidebar-mobile-open');
                    }
                });
            });
        });
    </script>
</body>
</html>