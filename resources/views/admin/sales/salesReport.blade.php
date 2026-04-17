<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sales Report</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/admin/sales/salesReport.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
            <h1 class="page-title">Sales Report</h1>
            <hr>

            <div class="container-fluid">
                <div class="delivery-calendar-card sales-report-card single-panel-card">
                    <div class="calendar-head">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <h3 class="cal-heading mb-0">Sales Report</h3>

                            <div id="sales-range" class="btn-group range-like-nav" role="group"
                                aria-label="Sales range">
                                <button type="button" class="btn btn-sm sales-range-btn active"
                                    data-range="day" id="rng-day">Day</button>
                                <button type="button" class="btn btn-sm sales-range-btn"
                                    data-range="week" id="rng-week">Week</button>
                                <button type="button" class="btn btn-sm sales-range-btn"
                                    data-range="month" id="rng-month">Month</button>

                                <div id="picker-day" class="mini-popover shadow" hidden></div>
                                <div id="picker-week" class="mini-popover shadow" hidden></div>
                                <div id="picker-month" class="mini-popover shadow" hidden></div>
                            </div>
                        </div>
                    </div>

                    <div class="calendar-panel">
                        <div class="p-3 p-lg-4">
                            <div class="card border-rose mb-3">
                                <div class="card-body p-0 bg-white rounded">
                                    <table class="table table-sm mb-0 admin-orders-table eb-stat-table">
                                        <thead>
                                            <tr>
                                                <th>Metric</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Gross Revenue</td>
                                                <td id="kpi-gross">₱ 0</td>
                                            </tr>
                                            <tr>
                                                <td>Paid Orders</td>
                                                <td id="kpi-count">0</td>
                                            </tr>
                                            <tr>
                                                <td>Average Order Value</td>
                                                <td id="kpi-aov">₱ 0.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="card border-rose">
                                        <div class="card-body p-0 bg-white rounded-bottom">
                                            <table class="table table-sm mb-0">
                                                <thead class="table-eb">
                                                    <tr>
                                                        <th>Service Type</th>
                                                        <th class="text-center">Orders</th>
                                                        <th class="text-start">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbl-by-service"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="card border-rose">
                                        <div class="card-body p-0 bg-white rounded-bottom">
                                            <table class="table table-sm mb-0">
                                                <thead class="table-eb">
                                                    <tr>
                                                        <th>Payment Method</th>
                                                        <th class="text-center">Orders</th>
                                                        <th class="text-start">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbl-by-payment"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-rose mt-3 eb-weekly-card" id="weekly-trend-card" hidden>
                                <div class="card-body p-3 bg-white rounded">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="fw-semibold" style="color:#173F7B; background: white">
                                            Weekly Trend (Sun–Sat)
                                        </div>
                                        <div class="small text-muted" id="weekly-trend-range"></div>
                                    </div>
                                    <div class="eb-weekly-wrap mt-2" id="weekly-bars"
                                        aria-label="Gross revenue per day, Sunday to Saturday"></div>
                                </div>
                            </div>

                            <div id="sales-values"
                                data-day="{{ (float) ($salesDay ?? 0) }}"
                                data-week="{{ (float) ($salesWeek ?? 0) }}"
                                data-month="{{ (float) ($salesMonth ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function() {
            const fmtPeso = (n, frac = 0) => {
                const v = Number(n || 0);
                try {
                    return new Intl.NumberFormat(undefined, {
                        minimumFractionDigits: frac,
                        maximumFractionDigits: frac
                    }).format(v);
                } catch {
                    return (frac ? v.toFixed(frac) : Math.round(v)).toString();
                }
            };

            const btns = document.querySelectorAll('.sales-range-btn');
            const apiSum = "{{ route('admin.sales.summary') }}";
            const tzNow = new Date();

            const popDay = document.getElementById('picker-day');
            const popWeek = document.getElementById('picker-week');
            const popMonth = document.getElementById('picker-month');

            const el = (tag, cls, txt) => {
                const e = document.createElement(tag);
                if (cls) e.className = cls;
                if (txt != null) e.textContent = txt;
                return e;
            };

            const pad = (n) => String(n).padStart(2, '0');
            const ymd = (d) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            const startOfWeek = (d) => {
                const c = new Date(d);
                c.setDate(c.getDate() - c.getDay());
                c.setHours(0, 0, 0, 0);
                return c;
            };

            async function fetchJSON(url, params) {
                const full = url + '?' + new URLSearchParams(params).toString();
                const res = await fetch(full, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                return await res.json();
            }

            async function fetchSummary(params) {
                const j = await fetchJSON(apiSum, params);
                if (!j.ok) throw new Error(j.message || 'Failed');
                return j;
            }

            const SCALE_MAX = 10000;
            const LOW_THRESHOLD = 5000;

            (function injectWeeklyCSS() {
                if (document.getElementById('eb-weekly-style')) return;
                const style = document.createElement('style');
                style.id = 'eb-weekly-style';
                style.textContent = `
                    .eb-weekly-card { border:1px solid #FFB5FE; }
                    .eb-weekly-row { display:grid; grid-template-columns:48px 1fr; gap:8px; align-items:end; }
                    .eb-axis { height:160px; display:flex; flex-direction:column; justify-content:space-between; align-items:flex-end; padding-right:4px; }
                    .eb-axis span { font-size:.75rem; color:#173F7B; }
                    .eb-chart { height:160px; position:relative; }
                    .eb-bars { position:relative; height:100%; display:grid; grid-template-columns:repeat(7,1fr); gap:8px; align-items:end; }
                    .eb-weekly-cell { display:flex; flex-direction:column; align-items:center; }
                    .eb-weekly-total { font-size:.75rem; color:#173F7B; min-height:1em; margin-bottom:4px; }
                    .eb-weekly-bar { width:100%; background:#FFF; border:1px solid #f2d5ef; border-radius:10px; position:relative; height:100%; overflow:visible; }
                    .eb-weekly-fill { width:100%; background:#FFB5FE; border-radius:10px 10px 0 0; height:0; transition:height .7s ease; will-change:height; }
                    .eb-weekly-label { margin-top:6px; font-size:.8rem; color:#173F7B; font-weight:600; text-align:center; }
                    .eb-weekly-legend { margin-top:6px; font-size:.75rem; color:#6c757d; }
                    .eb-weekly-bar.is-low .eb-weekly-fill { opacity:.85; }
                    .eb-weekly-bar.is-high .eb-weekly-fill { box-shadow:0 0 0 2px rgba(255,181,254,.35) inset; }
                `;
                document.head.appendChild(style);
            })();

            const anchor = document.getElementById('sales-values');
            let weeklyCard, weeklyBars, weeklyRange, prevWeeklyVals = null;

            (function buildWeeklyCard() {
                if (!anchor) return;
                const parent = anchor.parentElement;

                weeklyCard = el('div', 'card border-rose mt-3 eb-weekly-card');
                weeklyCard.id = 'weekly-trend-card';
                weeklyCard.hidden = true;

                const body = el('div', 'card-body p-3 bg-white rounded');
                const head = el('div', 'd-flex align-items-center justify-content-between');
                const title = el('div', 'fw-semibold', 'Weekly Trend (Sun–Sat)');
                title.style.color = '#173F7B';
                weeklyRange = el('div', 'small text-muted', '');
                head.append(title, weeklyRange);

                const row = el('div', 'eb-weekly-row mt-2');

                const axis = el('div', 'eb-axis');
                axis.append(
                    el('span', '', '10k'),
                    el('span', '', '5k'),
                    el('span', '', '0')
                );

                const chart = el('div', 'eb-chart');
                weeklyBars = el('div', 'eb-bars');
                chart.appendChild(weeklyBars);

                row.append(axis, chart);

                const legend = el('div', 'eb-weekly-legend',
                    'Low: ₱0–₱5,000 • High: ₱5,001–₱10,000 (scaled to ₱10,000)');
                body.append(head, row, legend);
                weeklyCard.appendChild(body);

                parent.insertBefore(weeklyCard, anchor);
            })();

            function weekDatesFrom(startISO) {
                const out = [];
                const base = new Date(startISO + 'T00:00:00');
                for (let i = 0; i < 7; i++) {
                    const d = new Date(base);
                    d.setDate(base.getDate() + i);
                    out.push(ymd(d));
                }
                return out;
            }

            async function fetchWeekSeries(startISO) {
                const dates = weekDatesFrom(startISO);
                const promises = dates.map(date =>
                    fetchSummary({
                        mode: 'day',
                        date
                    })
                    .then(j => Number(j?.kpis?.gross ?? 0))
                    .catch(() => 0)
                );
                const vals = await Promise.all(promises);
                return { dates, vals };
            }

            function tweenNumber(el, from, to, ms = 650) {
                const start = performance.now();

                function step(t) {
                    const k = Math.min(1, (t - start) / ms);
                    const v = Math.round(from + (to - from) * k);
                    el.textContent = '₱ ' + fmtPeso(v, 0);
                    if (k < 1) requestAnimationFrame(step);
                }

                requestAnimationFrame(step);
            }

            function pctOfScale(val) {
                const v = Math.max(0, Math.min(SCALE_MAX, Number(val) || 0));
                return (v / SCALE_MAX) * 100;
            }

            function renderWeeklyChart(vals, startISO) {
                if (!weeklyCard || !weeklyBars) return;
                if (!Array.isArray(vals) || vals.length !== 7) {
                    weeklyCard.hidden = true;
                    prevWeeklyVals = null;
                    return;
                }

                weeklyBars.innerHTML = '';
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

                for (let i = 0; i < 7; i++) {
                    const now = Number(vals[i]) || 0;

                    const cell = el('div', 'eb-weekly-cell');
                    const totalEl = el('div', 'eb-weekly-total', '₱ ' + fmtPeso(now, 0));
                    cell.appendChild(totalEl);

                    const bar = el('div', 'eb-weekly-bar');
                    const fill = el('div', 'eb-weekly-fill');

                    (now > LOW_THRESHOLD ? bar.classList.add('is-high') : bar.classList.add('is-low'));

                    bar.appendChild(fill);
                    cell.appendChild(bar);

                    fill.style.height = '0%';
                    void fill.offsetHeight;

                    requestAnimationFrame(() => {
                        fill.style.height = pctOfScale(now) + '%';
                    });

                    const lbl = el('div', 'eb-weekly-label', days[i]);
                    cell.appendChild(lbl);

                    bar.title = `${days[i]}: ₱ ${fmtPeso(now,0)}${now > LOW_THRESHOLD ? ' • High' : ' • Low'}`;

                    weeklyBars.appendChild(cell);
                    tweenNumber(totalEl, 0, now, 700);
                }

                if (weeklyRange && startISO) {
                    const s = new Date(startISO + 'T00:00:00');
                    const e = new Date(s);
                    e.setDate(e.getDate() + 6);
                    weeklyRange.textContent =
                        s.toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) + ' – ' +
                        e.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
                } else if (weeklyRange) {
                    weeklyRange.textContent = '';
                }

                weeklyCard.hidden = false;
                prevWeeklyVals = vals.slice();
            }

            let currentRange = 'day';
            let selectedDayISO = ymd(tzNow);
            let selectedWeekStartISO = ymd(startOfWeek(tzNow));
            let selectedMonth = {
                year: tzNow.getFullYear(),
                month: tzNow.getMonth() + 1
            };

            function renderKPIs(kpis) {
                document.getElementById('kpi-gross').textContent = '₱ ' + fmtPeso(kpis.gross, 0);
                document.getElementById('kpi-count').textContent = fmtPeso(kpis.count, 0);
                document.getElementById('kpi-aov').textContent = '₱ ' + fmtPeso(kpis.aov, 2);
            }

            function renderTableBody(tbodyEl, rows, cols) {
                tbodyEl.innerHTML = '';
                if (!rows.length) {
                    const tr = el('tr');
                    const td = el('td', 'text-center text-muted', 'No data');
                    td.colSpan = cols;
                    tr.append(td);
                    tbodyEl.append(tr);
                    return;
                }

                rows.forEach(r => {
                    const tr = el('tr');
                    tr.append(el('td', '', r.name ?? r.bucket ?? '—'));
                    tr.append(el('td', 'text-center', fmtPeso(r.c, 0)));
                    tr.append(el('td', 'text-start', '₱ ' + fmtPeso(r.rev, 0)));
                    tbodyEl.append(tr);
                });
            }

            function rangeParams() {
                if (currentRange === 'day') return { mode: 'day', date: selectedDayISO };
                if (currentRange === 'week') return { mode: 'week', start: selectedWeekStartISO };
                return { mode: 'month', year: selectedMonth.year, month: selectedMonth.month };
            }

            async function refreshSummary() {
                const sum = await fetchSummary(rangeParams());
                renderKPIs(sum.kpis);

                const tbService = document.getElementById('tbl-by-service');
                renderTableBody(
                    tbService,
                    sum.by_service.map(r => ({
                        name: r.service_type,
                        c: r.c,
                        rev: r.rev
                    })),
                    3
                );

                const tbPayment = document.getElementById('tbl-by-payment');
                renderTableBody(
                    tbPayment,
                    sum.by_payment.map(r => ({
                        name: (r.payment_method || '—').toUpperCase(),
                        c: r.c,
                        rev: r.rev
                    })),
                    3
                );

                if (currentRange === 'week') {
                    if (!Array.isArray(prevWeeklyVals)) prevWeeklyVals = [0, 0, 0, 0, 0, 0, 0];
                    try {
                        const { vals } = await fetchWeekSeries(selectedWeekStartISO);
                        renderWeeklyChart(vals, selectedWeekStartISO);
                    } catch {
                        if (weeklyCard) weeklyCard.hidden = true;
                        prevWeeklyVals = null;
                    }
                } else {
                    if (weeklyCard) weeklyCard.hidden = true;
                    prevWeeklyVals = null;
                }
            }

            function hideAll() {
                [popDay, popWeek, popMonth].forEach(p => p.hidden = true);
            }

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#sales-range') && !e.target.closest('.mini-popover')) hideAll();
            });

            function renderDayPicker(refDate = new Date()) {
                popDay.innerHTML = '';
                const y = refDate.getFullYear(), m = refDate.getMonth();

                const head = el('div', 'mini-header');
                const prev = el('button', 'mini-nav');
                prev.innerHTML = '&laquo;';
                const next = el('button', 'mini-nav');
                next.innerHTML = '&raquo;';
                const title = el('div', 'fw-semibold', refDate.toLocaleString(undefined, {
                    month: 'long',
                    year: 'numeric'
                }));
                head.append(prev, title, next);
                popDay.append(head);

                const grid = el('div', 'mini-cal');
                ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(d => grid.append(el('div', 'dow', d)));

                const firstDow = new Date(y, m, 1).getDay();
                const dim = new Date(y, m + 1, 0).getDate();

                for (let i = 0; i < firstDow; i++) grid.append(el('div', ''));

                for (let d = 1; d <= dim; d++) {
                    const cell = el('div', 'd', d);
                    const iso = `${y}-${pad(m+1)}-${pad(d)}`;
                    if (selectedDayISO === iso) cell.classList.add('selected');

                    cell.addEventListener('click', async () => {
                        selectedDayISO = iso;
                        hideAll();
                        currentRange = 'day';
                        await refreshSummary();
                    });

                    grid.append(cell);
                }

                popDay.append(grid);

                prev.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    renderDayPicker(new Date(y, m - 1, 1));
                    placePopover(popDay);
                });

                next.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    renderDayPicker(new Date(y, m + 1, 1));
                    placePopover(popDay);
                });
            }

            function renderWeekPicker(base = new Date()) {
                popWeek.innerHTML = '';
                const y = base.getFullYear(), m = base.getMonth();

                const head = el('div', 'mini-header');
                const prev = el('button', 'mini-nav');
                prev.innerHTML = '&laquo;';
                const next = el('button', 'mini-nav');
                next.innerHTML = '&raquo;';
                const title = el('div', 'fw-semibold', base.toLocaleString(undefined, {
                    month: 'long',
                    year: 'numeric'
                }));
                head.append(prev, title, next);
                popWeek.append(head);

                const grid = el('div', 'mini-cal');
                const firstDow = new Date(y, m, 1).getDay();
                const dim = new Date(y, m + 1, 0).getDate();

                for (let i = 0; i < firstDow; i++) grid.append(el('div', ''));

                for (let d = 1; d <= dim; d++) {
                    const dateObj = new Date(y, m, d);
                    const cell = el('div', 'd', d);

                    const offset = firstDow + (d - 1);
                    const weekIndex = Math.floor(offset / 7);
                    cell.dataset.weekIndex = weekIndex;

                    cell.addEventListener('click', async () => {
                        const ws = new Date(dateObj);
                        ws.setDate(ws.getDate() - ws.getDay());
                        selectedWeekStartISO = ymd(ws);
                        hideAll();
                        currentRange = 'week';
                        await refreshSummary();
                    });

                    cell.addEventListener('mouseenter', () => {
                        const wi = cell.dataset.weekIndex;
                        popWeek.querySelectorAll(`.mini-cal .d[data-week-index="${wi}"]`)
                            .forEach(c => c.classList.add('hover-week'));
                    });

                    cell.addEventListener('mouseleave', () => {
                        const wi = cell.dataset.weekIndex;
                        popWeek.querySelectorAll(`.mini-cal .d[data-week-index="${wi}"]`)
                            .forEach(c => c.classList.remove('hover-week'));
                    });

                    grid.append(cell);
                }

                popWeek.append(grid);

                prev.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    renderWeekPicker(new Date(y, m - 1, 1));
                    placePopover(popWeek);
                });

                next.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    renderWeekPicker(new Date(y, m + 1, 1));
                    placePopover(popWeek);
                });
            }

            function renderMonthPicker(viewYear = (new Date()).getFullYear()) {
                popMonth.innerHTML = '';

                const head = el('div', 'mini-header');
                const prev = el('button', 'mini-nav');
                prev.innerHTML = '&laquo;';
                const next = el('button', 'mini-nav');
                next.innerHTML = '&raquo;';
                const title = el('div', 'fw-semibold', String(viewYear));
                head.append(prev, title, next);
                popMonth.append(head);

                const names = Array.from({ length: 12 }, (_, i) =>
                    new Date(viewYear, i, 1).toLocaleString(undefined, { month: 'short' })
                );

                const grid = el('div', 'months-grid');

                names.forEach((name, i) => {
                    const mbtn = el('div', 'm', name);

                    if (viewYear === selectedMonth.year && (i + 1) === selectedMonth.month) {
                        mbtn.classList.add('selected');
                    }

                    mbtn.addEventListener('click', async () => {
                        selectedMonth = { year: viewYear, month: i + 1 };
                        hideAll();
                        currentRange = 'month';
                        await refreshSummary();
                    });

                    grid.append(mbtn);
                });

                popMonth.append(grid);

                prev.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    renderMonthPicker(viewYear - 1);
                    placePopover(popMonth);
                });

                next.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    renderMonthPicker(viewYear + 1);
                    placePopover(popMonth);
                });
            }

            function placePopover(pop) {
                pop.style.position = 'absolute';
                pop.style.right = '0';
                pop.style.top = 'calc(100% + 8px)';
                pop.hidden = false;
            }

            async function setActive(range, openPicker = true) {
                currentRange = range;

                btns.forEach(b => {
                    const on = b.dataset.range === range;
                    b.classList.toggle('active', on);
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                });

                hideAll();

                if (openPicker) {
                    if (range === 'day') {
                        renderDayPicker(new Date(selectedDayISO));
                        placePopover(popDay);
                    }
                    if (range === 'week') {
                        renderWeekPicker(new Date());
                        placePopover(popWeek);
                    }
                    if (range === 'month') {
                        renderMonthPicker((new Date()).getFullYear());
                        placePopover(popMonth);
                    }
                } else {
                    await refreshSummary();
                }
            }

            btns.forEach(b => b.addEventListener('click', () => setActive(b.dataset.range, true)));

            const weekBtn = document.getElementById('rng-week');
            if (weekBtn) {
                weekBtn.addEventListener('click', () => {
                    prevWeeklyVals = [0, 0, 0, 0, 0, 0, 0];
                    setTimeout(() => {
                        if (currentRange === 'week') refreshSummary().catch(() => {});
                    }, 0);
                });
            }

            (async function initialLoad() {
                try {
                    await refreshSummary();
                } catch (err) {
                    console.error('Failed to load initial sales data:', err);
                }
            })();
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