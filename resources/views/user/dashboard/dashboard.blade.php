<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>User Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>

<body>
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
                    <a href="{{ route('user.orders.history') }}" class="nav-link {{ $is('user.orders.history') }}">
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

        <main class="main-content p-4">
            <h1 class="page-title">Dashboard</h1>
            <hr>

            <div class="container-fluid">
                <div class="card">
                    <div class="card-header" id="card-header">
                        <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">
                            <div id="txt-myOrders" class="d-flex align-items-center">
                                <div>
                                    <h2 class="mb-0">My Orders</h2>
                                    <span class="orders-count">
                                        {{ $orders->count() }} order{{ $orders->count() !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>

                            <div class="btn-walkin">
                                <a href="{{ route('booking.start') }}" id="btn-booking" class="btn">
                                    <span>+ Schedule a Pickup</span>
                                </a>
                                <span id="cap-note" class="cap-note" style="display:none;">Fully booked for today</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="recent-orders-table-scroll">
                            <table class="table table-sm align-middle admin-orders-table">
                                <colgroup>
                                    <col class="col-booked-on">
                                    <col class="col-order-details">
                                    <col class="col-total-payment">
                                    <col class="col-receipt">
                                    <col class="col-payment-status">
                                    <col class="col-laundry-status">
                                    <col class="col-actions">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Booked On</th>
                                        <th>Order Details</th>
                                        <th>Total Payment</th>
                                        <th>Receipt</th>
                                        <th>Payment Status</th>
                                        <th>Laundry Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        @php
                                            $paid = ($order->payment_status ?? 'unpaid') === 'paid';
                                            $isCanceled = $order->status === 'canceled';
                                            $displayTotal = ($order->total ?? 0) > 0 ? $order->total : $order->total_amount ?? 0;

                                            $canCancel = in_array($order->status, ['pending', 'for_pickup', 'on_going'], true)
                                                && !$paid
                                                && !$isCanceled;

                                            $statusBadgeMap = [
                                                'pending' => 'bg-secondary',
                                                'for_pickup' => 'bg-info',
                                                'on_going' => 'bg-warning text-dark',
                                                'to_be_received' => 'bg-primary',
                                                'for_delivery' => 'bg-primary',
                                                'completed' => 'bg-success',
                                                'canceled' => 'bg-danger',
                                            ];

                                            $statusBadge = $statusBadgeMap[$order->status] ?? 'bg-secondary';
                                        @endphp

                                        <tr class="{{ $isCanceled ? 'table-danger' : '' }}">
                                            <td>
                                                <div>
                                                    <strong>{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $order->created_at->timezone(config('app.timezone'))->format('h:i A') }}
                                                </div>
                                            </td>

                                            <td>
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="{{ route('orders.show', $order) }}">
                                                    View
                                                </a>
                                            </td>

                                            <td>₱ {{ number_format($displayTotal, 2) }}</td>

                                            <td>
                                                <a href="{{ route('orders.receipt.show', $order) }}"
                                                    class="link-primary" target="_blank">
                                                    Details
                                                </a>
                                            </td>

                                            <td>
                                                @if ($isCanceled)
                                                    <span class="badge bg-secondary">N/A</span>
                                                @else
                                                    <span class="badge {{ $paid ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $paid ? 'Paid' : 'Not Paid' }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge {{ $statusBadge }}">
                                                    {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                                </span>
                                            </td>

                                            <td class="actions">
                                                @if ($isCanceled)
                                                    <span class="text-muted small">Canceled</span>
                                                @else
                                                    <div class="action-dropdown">
                                                        <button
                                                            type="button"
                                                            class="eandb-kebab js-action-toggle"
                                                            aria-haspopup="true"
                                                            aria-expanded="false"
                                                            {{ !$canCancel ? 'disabled' : '' }}>
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>

                                                        <div class="action-menu-template d-none">
                                                            @if ($canCancel)
                                                                <form action="{{ route('orders.cancel', $order) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Cancel this order?');">
                                                                    @csrf
                                                                    <button type="submit" class="action-item action-item-danger">
                                                                        Cancel Order
                                                                    </button>
                                                                </form>

                                                                <a href="{{ route('order.reschedule', $order) }}" class="action-item">
                                                                    Reschedule
                                                                </a>
                                                            @else
                                                                <span class="action-item text-muted">No available actions</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No orders yet.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <div class="modal fade" id="deliveryNoticeModal" tabindex="-1" aria-labelledby="deliveryNoticeLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#173F7B;color:#fff;">
                    <h5 class="modal-title" id="deliveryNoticeLabel">Heads up!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deliveryNoticeBody">
                    Your laundry is for delivery.
                </div>
                <div class="modal-footer">
                    <a id="deliveryNoticeViewBtn" href="#" class="btn btn-outline-primary btn-sm"
                        style="display:none;">View Order</a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="capacityLimitModal" tabindex="-1" aria-labelledby="capacityLimitLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#173F7B;color:#fff;">
                    <h5 class="modal-title" id="capacityLimitLabel">We're at full capacity today</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Thanks for choosing <strong>E&amp;B Laundry Hub</strong>! We've reached our daily limit of
                    <strong>30 loads</strong> for today's service window (8:00 AM–8:00 PM).
                    <br><br>
                    Please schedule your pickup <strong>after 8:00 PM</strong> or choose another day.
                </div>
                <div class="modal-footer">
                    <a href="{{ route('booking.start') }}" class="btn btn-primary btn-sm" id="capTryLaterBtn"
                        style="display:none;">Pick a later time</a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const POLL_MS = 15000;
            let currentId = null;

            function showNotice(n) {
                if (!n) return;
                currentId = n.id;

                const titleEl = document.getElementById('deliveryNoticeLabel');
                const bodyEl = document.getElementById('deliveryNoticeBody');
                const viewBtn = document.getElementById('deliveryNoticeViewBtn');

                titleEl.textContent = n.title || 'Heads up!';
                bodyEl.textContent = n.body || 'Your laundry is for delivery.';

                if (n.order_id) {
                    viewBtn.style.display = 'inline-block';
                    viewBtn.href = "{{ route('orders.show', ['order' => '__ORDER__']) }}".replace('__ORDER__', n.order_id);
                } else {
                    viewBtn.style.display = 'none';
                }

                const modal = new bootstrap.Modal(document.getElementById('deliveryNoticeModal'));
                modal.show();

                const modalEl = document.getElementById('deliveryNoticeModal');
                modalEl.addEventListener('hidden.bs.modal', markReadOnce, { once: true });
            }

            function markReadOnce() {
                if (!currentId) return;

                fetch(`{{ url('/notifications') }}/${currentId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                }).finally(() => {
                    currentId = null;
                });
            }

            function poll() {
                fetch(`{{ route('user.notifications.poll') }}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(json => {
                    if (json?.ok && json.notification) {
                        showNotice(json.notification);
                    }
                })
                .catch(() => {})
                .finally(() => setTimeout(poll, POLL_MS));
            }

            document.addEventListener('DOMContentLoaded', poll);
        })();
    </script>

    <script>
        (function() {
            const CHECK_MS = 60000;
            const capUrl = "{{ route('capacity.today') }}";

            const btn = document.getElementById('btn-booking');
            const note = document.getElementById('cap-note');

            let shownOnce = false;

            function setCappedUI(capped) {
                if (!btn) return;

                if (capped) {
                    btn.classList.add('disabled');
                    btn.setAttribute('aria-disabled', 'true');
                    btn.setAttribute('tabindex', '-1');
                    btn.style.pointerEvents = 'none';

                    if (note) note.style.display = 'inline';

                    if (!shownOnce) {
                        shownOnce = true;
                        const tryLaterBtn = document.getElementById('capTryLaterBtn');
                        if (tryLaterBtn) tryLaterBtn.style.display = 'inline-block';

                        const m = new bootstrap.Modal(document.getElementById('capacityLimitModal'));
                        m.show();
                    }
                } else {
                    btn.classList.remove('disabled');
                    btn.removeAttribute('aria-disabled');
                    btn.removeAttribute('tabindex');
                    btn.style.pointerEvents = '';
                    if (note) note.style.display = 'none';
                }
            }

            function checkCapacity() {
                fetch(capUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(j => {
                    if (!j || !j.ok) return;
                    setCappedUI(!!j.capped);
                })
                .catch(() => {})
                .finally(() => setTimeout(checkCapacity, CHECK_MS));
            }

            document.addEventListener('DOMContentLoaded', checkCapacity);
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            const body = document.body;
            const KEY = 'eb_user_sidebar_collapsed';

            try {
                if (localStorage.getItem(KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                body.classList.toggle('sidebar-collapsed');

                try {
                    localStorage.setItem(KEY, body.classList.contains('sidebar-collapsed') ? '1' : '0');
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let activeMenu = null;
            let activeButton = null;

            function closeMenu() {
                if (activeMenu) {
                    activeMenu.remove();
                    activeMenu = null;
                }

                if (activeButton) {
                    activeButton.setAttribute('aria-expanded', 'false');
                    activeButton = null;
                }
            }

            function positionMenu(button, menu) {
                const rect = button.getBoundingClientRect();
                const menuRect = menu.getBoundingClientRect();
                const gap = 8;

                let top = rect.bottom + gap;
                let left = rect.right - menuRect.width;

                if (left < 12) left = 12;
                if (left + menuRect.width > window.innerWidth - 12) {
                    left = window.innerWidth - menuRect.width - 12;
                }

                if (top + menuRect.height > window.innerHeight - 12) {
                    top = rect.top - menuRect.height - gap;
                }

                if (top < 12) top = 12;

                menu.style.top = `${top}px`;
                menu.style.left = `${left}px`;
            }

            function openMenu(button) {
                closeMenu();

                const wrapper = button.closest('.action-dropdown');
                const template = wrapper ? wrapper.querySelector('.action-menu-template') : null;
                if (!template) return;

                const menu = document.createElement('div');
                menu.className = 'action-menu-portal';
                menu.innerHTML = template.innerHTML;

                document.body.appendChild(menu);

                activeMenu = menu;
                activeButton = button;
                activeButton.setAttribute('aria-expanded', 'true');

                positionMenu(button, menu);

                menu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }

            document.addEventListener('click', function (e) {
                const toggle = e.target.closest('.js-action-toggle');

                if (toggle) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (toggle.disabled) return;

                    if (activeButton === toggle && activeMenu) {
                        closeMenu();
                    } else {
                        openMenu(toggle);
                    }
                    return;
                }

                if (e.target.closest('.action-menu-portal')) {
                    return;
                }

                closeMenu();
            });

            window.addEventListener('resize', function () {
                if (activeButton && activeMenu) {
                    positionMenu(activeButton, activeMenu);
                }
            });

            window.addEventListener('scroll', function () {
                if (activeButton && activeMenu) {
                    positionMenu(activeButton, activeMenu);
                }
            }, true);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMenu();
                }
            });
        });
    </script>
</body>
</html>