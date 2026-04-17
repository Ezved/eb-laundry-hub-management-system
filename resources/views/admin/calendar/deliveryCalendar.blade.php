<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery Calendar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/admin/dashboard/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin/calendar/deliveryCalendar.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<style>
    .mini-popover .mini-cal .d.hover-week {
        background-color: #FFB5FE;
        color: #173F7B;
        border-radius: 999px;
        font-weight: 600;
    }
</style>

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
            <h1 class="page-title">Delivery Calendar</h1>
            <hr>

            <div class="container-fluid">
                <div class="delivery-calendar-card single-panel-card">
                    <div class="calendar-head">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <h3 class="cal-heading mb-0">Calendar</h3>
                            <div class="d-flex align-items-center gap-3">
                                <span class="legend"><span class="legend-dot legend-pickup"></span>Pickup</span>
                                <span class="legend"><span class="legend-dot legend-delivery"></span>Delivery</span>
                            </div>
                        </div>
                        <hr class="calendar-sep mb-0">
                        <div class="calendar-nav d-flex align-items-center justify-content-between">
                            <button class="btn btn-sm btn-outline-primary" id="cal-prev">&laquo; Prev</button>
                            <div id="cal-title" class="fw-semibold" style="color:#173F7B;"></div>
                            <button class="btn btn-sm btn-outline-primary" id="cal-next">Next &raquo;</button>
                        </div>
                    </div>

                    <div class="calendar-panel">
                        <div class="calendar-grid" id="calendar-grid"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Day Events Modal -->
    <div class="modal fade" id="dayEventsModal" tabindex="-1" aria-labelledby="dayEventsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background:#173F7B;color:#fff;">
                    <h5 class="modal-title">
                        <span class="me-2">Schedules for</span>
                        <span id="dayModalDate" class="fw-semibold"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="day-list"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @php $calendarEvents = $calendarEvents ?? []; @endphp

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function() {
            const pad = (n) => String(n).padStart(2, '0');
            const fmtKey = (d) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
            const groupByDate = (arr) => arr.reduce((acc, e) => {
                if (!e?.date) return acc;
                (acc[e.date] ??= []).push(e);
                return acc;
            }, {});

            const GREEN_BORDER = '#136f2a';
            const GREEN_DAY_BG = '#f4fbf6';

            const paidFlag = (v) => ['paid', 'settled', 'complete', 'completed'].includes(String(v || '').toLowerCase());

            function isPaid(ev) {
                return paidFlag(ev?.payment_status) || paidFlag(ev?.order_payment_status) || ev?.paid === true || ev?.is_paid === true;
            }

            const doneFlag = (v) => ['completed', 'done', 'delivered'].includes(String(v || '').toLowerCase());

            function isCompleted(ev) {
                return doneFlag(ev?.status) || doneFlag(ev?.order_status) || ev?.completed === true;
            }

            const grid = document.getElementById('calendar-grid');
            const title = document.getElementById('cal-title');
            const prev = document.getElementById('cal-prev');
            const next = document.getElementById('cal-next');

            let view = new Date();
            view.setDate(1);
            let EVENTS = [], GROUPED = {};

            function render() {
                grid.innerHTML = '';
                const y = view.getFullYear(), m = view.getMonth();
                const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
                    'September', 'October', 'November', 'December'
                ];
                title.textContent = `${MONTHS[m]} ${y}`;

                ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(w => {
                    const h = document.createElement('div');
                    h.className = 'cell header';
                    h.textContent = w;
                    grid.appendChild(h);
                });

                const firstDow = new Date(y, m, 1).getDay();
                const dim = new Date(y, m + 1, 0).getDate();
                for (let i = 0; i < firstDow; i++) {
                    const c = document.createElement('div');
                    c.className = 'cell pad';
                    grid.appendChild(c);
                }

                GROUPED = groupByDate(EVENTS);

                for (let d = 1; d <= dim; d++) {
                    const date = new Date(y, m, d);
                    const key = fmtKey(date);

                    const cell = document.createElement('div');
                    cell.className = 'cell day';
                    cell.dataset.key = key;
                    cell.tabIndex = 0;
                    cell.setAttribute('role', 'button');
                    cell.innerHTML = `<div class="day-num">${d}</div>`;

                    const list = document.createElement('div');
                    list.className = 'badges';

                    const dayEvents = GROUPED[key] ?? [];
                    const hasDone = dayEvents.some(ev => isPaid(ev) && isCompleted(ev));

                    const pickupCount = dayEvents.filter(ev => ev.type !== 'delivery').length;
                    const deliveryCount = dayEvents.filter(ev => ev.type === 'delivery').length;

                    if (pickupCount > 0) {
                        const pickupBadge = document.createElement('span');
                        pickupBadge.className = 'badge-count is-pickup';
                        pickupBadge.textContent = pickupCount;
                        pickupBadge.title = `${pickupCount} pickup order${pickupCount > 1 ? 's' : ''}`;
                        list.appendChild(pickupBadge);
                    }

                    if (deliveryCount > 0) {
                        const deliveryBadge = document.createElement('span');
                        deliveryBadge.className = 'badge-count is-delivery';
                        deliveryBadge.textContent = deliveryCount;
                        deliveryBadge.title = `${deliveryCount} delivery order${deliveryCount > 1 ? 's' : ''}`;
                        list.appendChild(deliveryBadge);
                    }

                    if (hasDone) {
                        cell.style.setProperty('background', GREEN_DAY_BG, 'important');
                        cell.style.boxShadow = `inset 0 0 0 2px ${GREEN_BORDER}`;
                        cell.style.borderRadius = '8px';
                    }

                    cell.appendChild(list);
                    grid.appendChild(cell);
                }
            }

            function openDayModal(key, events, dateObj) {
                const list = document.getElementById('day-list');
                const dateEl = document.getElementById('dayModalDate');

                if (dateEl) {
                    dateEl.textContent = dateObj.toLocaleDateString(undefined, {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }

                list.innerHTML = '';

                if (!events.length) {
                    list.innerHTML = `<div class="text-muted">No schedules for this date.</div>`;
                    new bootstrap.Modal(document.getElementById('dayEventsModal')).show();
                    return;
                }

                const SHOW_URL = "{{ route('admin.orders.show', ['order' => '__ORDER__']) }}";

                events.forEach(ev => {
                    const row = document.createElement('div');
                    row.className = 'day-item d-flex align-items-center justify-content-between border rounded p-2 mb-2';

                    const left = document.createElement('div');
                    left.className = 'd-flex align-items-center gap-2';

                    const done = isPaid(ev) && isCompleted(ev);

                    left.innerHTML = `
                        <span class="legend-dot ${ev.type === 'delivery' ? 'legend-delivery' : 'legend-pickup'}"></span>
                        <div>
                            <div class="fw-semibold">${ev.label || (ev.type === 'delivery' ? 'Delivery' : 'Pickup')}</div>
                            <div class="small text-muted">
                                ${(ev.type === 'delivery' ? 'Delivery' : 'Pickup')} • ${key}${ev.time ? ' • ' + ev.time : ''}${done ? ' • Paid & Completed' : ''}
                            </div>
                        </div>
                    `;

                    const right = document.createElement('div');
                    const completedOnly = String(ev?.status || ev?.order_status || '').toLowerCase() === 'completed' || ev?.completed === true;

                    if (completedOnly) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm';
                        btn.style.cssText = 'background:#173F7B;color:#fff;border-color:#173F7B;opacity:.75;cursor:not-allowed;';
                        btn.textContent = 'Completed';
                        btn.disabled = true;
                        right.appendChild(btn);
                    } else {
                        const viewA = document.createElement('a');
                        viewA.className = 'btn btn-sm me-2';
                        viewA.style.cssText = 'background:#173F7B;color:#fff;border-color:#173F7B;';
                        viewA.href = SHOW_URL.replace('__ORDER__', ev.order_id);
                        viewA.textContent = 'View';
                        right.appendChild(viewA);
                    }

                    row.appendChild(left);
                    row.appendChild(right);
                    list.appendChild(row);
                });

                new bootstrap.Modal(document.getElementById('dayEventsModal')).show();
            }

            function onGridClick(e) {
                const cell = e.target.closest('.cell.day');
                if (!cell || !grid.contains(cell)) return;
                const key = cell.dataset.key;
                openDayModal(key, GROUPED[key] ?? [], new Date(key + 'T00:00:00'));
            }

            function onGridKey(e) {
                if (e.key !== 'Enter' && e.key !== ' ') return;
                const cell = e.target.closest('.cell.day');
                if (!cell) return;
                e.preventDefault();
                const key = cell.dataset.key;
                openDayModal(key, GROUPED[key] ?? [], new Date(key + 'T00:00:00'));
            }

            grid.addEventListener('click', onGridClick);
            grid.addEventListener('keydown', onGridKey);

            prev.addEventListener('click', () => {
                view.setMonth(view.getMonth() - 1);
                render();
            });

            next.addEventListener('click', () => {
                view.setMonth(view.getMonth() + 1);
                render();
            });

            function loadEvents() {
                fetch("{{ route('admin.calendar.events') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(list => {
                    EVENTS = (Array.isArray(list) ? list : []).filter(e => e && e.date);
                    render();
                })
                .catch(() => render());
            }

            loadEvents();
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('sidebarToggle');
            if (btn) {
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
                        localStorage.setItem(KEY, body.classList.contains('sidebar-collapsed') ? '1' : '0');
                    } catch (e) {}
                });
            }

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