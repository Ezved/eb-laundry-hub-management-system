<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loyalty Membership</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>

<body>
    <div class="d-flex">
        <!-- Side Bar -->
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
                        <a class="dropdown-item fw-bold" href="{{ route('settings.edit') }}">
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

        <!-- Main Content -->
        <div class="p-4" style="flex-grow: 1; background-color: #ffffff; height: 100vh; overflow-y: auto;">
            <h1 class="page-title">Loyalty Membership</h1>
            <hr>

            <div class="container">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-start justify-content-between gap-3" id="card-header">
                        <!-- LEFT: Your Loyalty Status (bold, dark blue, moved up) -->
                        <div class="d-flex align-items-center" style="margin-top: -8px;">
                            <h2 style="margin: 0; color: #173F7B; font-weight: 800; font-size: 1.75rem;">
                                Your Loyalty Status
                            </h2>
                        </div>

                        {{-- RIGHT: Progress summary --}}
                        <div class="text-end" style="min-width: 300px;">
                            <div class="small text-muted mb-1">Orders toward next free service</div>
                            <div class="d-flex align-items-center gap-2" style="min-width:260px;">
                                <div class="progress flex-grow-1" style="height: 10px; background-color: #e9ecef; border-radius: 5px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ ($progress / 10) * 100 }}%; background-color: #10a80d; transition: width 0.6s ease;"
                                        aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="10"></div>
                                </div>
                                <span class="fw-semibold" style="color:#173F7B;">{{ $progress }}/10</span>
                            </div>

                            <div class="mt-2 small">
                                <span class="badge bg-success">
                                    Free services available: {{ $freeEarned }}
                                </span>
                                <span class="ms-2 text-muted">
                                    Next in {{ $nextIn }} order{{ $nextIn == 1 ? '' : 's' }}
                                </span>
                            </div>

                            <!-- Counter + button -->
                            <div class="mt-3 d-flex align-items-center justify-content-end gap-2">
                                {{-- Counter box: available free loads --}}
                                <div style="background: linear-gradient(135deg, #173F7B 0%, #2563a8 100%); color: #fff; padding: 0.5rem 1rem; border-radius: 12px; font-size: 1.1rem; font-weight: 700; min-width: 60px; text-align: center; box-shadow: 0 2px 8px rgba(23, 63, 123, 0.3); display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                    <i class="bi bi-gift-fill me-1"></i>
                                    <span class="fw-bold">{{ $freeEarned }}</span>
                                </div>

                                <form method="POST" action="{{ route('loyalty.claim') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                        style="background: {{ $freeEarned < 1 ? '#6c757d' : '#10a80d' }} !important; color: #fff !important; border: none; border-radius: 20px; font-weight: 600; padding: 0.45rem 1.2rem; transition: all 0.2s ease; {{ $freeEarned < 1 ? 'opacity: 0.6; cursor: not-allowed;' : '' }}"
                                        class="btn btn-sm"
                                        {{ $freeEarned < 1 ? 'disabled' : '' }}
                                        onmouseover="if(!this.disabled) this.style.background='#307a2e'; if(!this.disabled) this.style.transform='translateY(-1px)'; if(!this.disabled) this.style.boxShadow='0 3px 6px rgba(0,0,0,0.15)';"
                                        onmouseout="if(!this.disabled) this.style.background='#10a80d'; if(!this.disabled) this.style.transform='translateY(0)'; if(!this.disabled) this.style.boxShadow='none';">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Claim Free Load
                                    </button>
                                </form>
                            </div>
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
                                        <th>#</th>
                                        <th>Date & Time Ordered</th>
                                        <th>Pickup Date & Time</th>
                                        <th>Service</th>
                                        <th class="text-center">Load Qty.</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Counts Toward Free?</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recent as $idx => $order)
                                        @php
                                            // Only completed orders count
                                            $counts = $order->status === 'completed';

                                            $status = $order->status;
                                            $statusMap = [
                                                'pending' => 'bg-secondary',
                                                'for_pickup' => 'bg-info',
                                                'on_going' => 'bg-warning text-dark',
                                                'to_be_received' => 'bg-primary',
                                                'for_delivery' => 'bg-primary',
                                                'canceled' => 'bg-danger',
                                                'completed' => 'bg-success',
                                            ];
                                            $statusBadge = $statusMap[$status] ?? 'bg-secondary';
                                        @endphp

                                        <tr style="transition: background-color 0.2s ease;">
                                            <td>{{ $idx + 1 }}</td>
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
                                                <span class="badge {{ $counts ? 'bg-success' : 'bg-secondary' }}" style="min-width: 50px; padding: 0.35rem 0.65rem; font-weight: 600;">
                                                    {{ $counts ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $statusBadge }}" style="min-width: 80px; padding: 0.35rem 0.65rem; font-weight: 600;">
                                                    {{ str_replace('_', ' ', ucfirst($status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No orders yet.</td>
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