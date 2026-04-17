{{-- resources/views/user/orderHistory/orderHistory.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>

<body>
    <div class="d-flex">
        {{-- Sidebar --}}
        <div id="side-bar" class="d-flex flex-column flex-shrink-0 p-3" style="width: 280px; height: 100vh;">
            
            {{-- Top-right toggle above the logo --}}
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
                       class="nav-link {{ $is('user.orders.history', 'user.orderHistory') }}" 
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
                        <hr class="dropdown-divider">
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
        </div>

        {{-- Main content --}}
        <div class="p-4" style="flex-grow: 1; background-color: #ffffff; height: 100vh; overflow-y: auto;">
            <h1 class="page-title">Order History</h1>
            <hr>

            <div class="container">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between" id="card-header">
                        <div class="d-flex align-items-center">
                            <h2 style="margin: 0; color: #173F7B; font-weight: 800; font-size: 1.75rem;">
                                Completed Orders
                            </h2>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background-color: #d4edda; color: #155724;">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('fail'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background-color: #f8d7da; color: #721c24;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ session('fail') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle admin-orders-table">
                                <thead>
                                    <tr>
                                        <th>Date & Time Ordered</th>
                                        <th>Pickup Date & Time</th>
                                        <th>Service</th>
                                        <th class="text-center">Load Qty.</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Payment</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        <tr style="transition: background-color 0.2s ease;">
                                            <!-- Date & Time Ordered - LEFT ALIGNED -->
                                            <td style="text-align: left;">
                                                <div>
                                                    <strong>{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $order->created_at->timezone(config('app.timezone'))->format('h:i A') }}
                                                </div>
                                            </td>
                                            <!-- Pickup Date & Time - LEFT ALIGNED -->
                                            <td style="text-align: left;">
                                                <div>
                                                    <strong>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="text-muted small">{{ $order->pickup_time }}</div>
                                            </td>
                                            <td>{{ $order->service_type ?? '—' }}</td>
                                            <td class="text-center">{{ $order->load_qty ?? 0 }}</td>
                                            <td class="text-center">₱ {{ number_format($order->total ?? 0, 0) }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success" style="min-width: 60px; padding: 0.35rem 0.65rem; font-weight: 600;">
                                                    Paid
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success" style="min-width: 80px; padding: 0.35rem 0.65rem; font-weight: 600;">
                                                    Completed
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('orders.show', $order) }}" class="link-primary" style="text-decoration: none;">
                                                    Details
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No completed orders yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            const body = document.body;
            const KEY = 'eb_user_sidebar_collapsed';

            // Restore last state
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

        // Add hover effect to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.admin-orders-table tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    if (this.children.length > 1) {
                        this.style.backgroundColor = '#f8f9fa';
                    }
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>

</body>

</html>