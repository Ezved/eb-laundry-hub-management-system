<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Users</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin/users/users.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
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
                        id="nav-users" aria-current="page">
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
        <main id="main-area" class="main-content p-4">
            <h1 class="page-title">Users</h1>
            <hr>

            <div class="container-fluid">
                <div class="card users-card">
                    <div class="card-header users-header" id="card-header">
                        <div id="search-add-button" class="users-toolbar">
                            <!-- Live Search -->
                            <div class="input-group search-group">
                                <input id="live-search"
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search user, email, phone number..."
                                    value="{{ request('search') }}">
                                <button id="search-btn" type="button" class="btn btn-pink">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>

                            <!-- Show Hidden toggle -->
                            <form method="GET" action="{{ route('users') }}" class="toggle-hidden-form">
                                <input type="hidden" name="show_hidden" value="{{ ($show_hidden ?? false) ? 0 : 1 }}">
                                <button type="submit"
                                    class="btn btn-sm d-flex align-items-center gap-1 btn-pink icon-btn"
                                    title="{{ ($show_hidden ?? false) ? 'Show Visible Users' : 'Show Hidden Users' }}">
                                    @if($show_hidden ?? false)
                                        <i class="bi bi-eye"></i>
                                    @else
                                        <i class="bi bi-eye-slash"></i>
                                    @endif
                                </button>
                            </form>

                            <!-- Add user -->
                            <div class="toolbar-action">
                                <a href="{{ route('user.add') }}" id="btn-add-new"
                                    class="btn btn-sm d-flex align-items-center gap-1">
                                    <i class="bi bi-plus"></i>
                                    <span>Add User</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-wrap">
                            <table class="table table-sm table-borderless align-middle users-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact #</th>
                                        <th>Joined Date</th>
                                        <th>Last Update</th>
                                        <th>Role</th>
                                        <th class="actions text-center align-middle">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="users-tbody">
                                    @include('admin.users.partials.rows', [
                                        'all_users'   => $all_users,
                                        'authId'      => auth()->id(),
                                        'adminCount'  => \App\Models\User::where('role','admin')->where('is_hidden', false)->count(),
                                        'show_hidden' => $show_hidden ?? false,
                                    ])
                                </tbody>
                            </table>
                        </div>

                        @if ($all_users->isEmpty())
                            <div class="alert alert-info">No users found matching your search.</div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function() {
            const input = document.getElementById('live-search');
            const btn = document.getElementById('search-btn');
            const tbody = document.getElementById('users-tbody');
            const showHidden = {!! ($show_hidden ?? false) ? 'true' : 'false' !!};

            let timer = null;

            function fetchRows(q) {
                const url = new URL("{{ route('users.search.ajax') }}", window.location.origin);
                url.searchParams.set('q', q || '');
                url.searchParams.set('show_hidden', showHidden ? '1' : '0');

                return fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    tbody.innerHTML = html;
                })
                .catch(() => {});
            }

            function schedule() {
                clearTimeout(timer);
                timer = setTimeout(() => fetchRows(input.value.trim()), 250);
            }

            if (input) {
                input.addEventListener('input', schedule);

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(timer);
                        fetchRows(input.value.trim());
                    }
                });
            }

            if (btn) {
                btn.addEventListener('click', () => fetchRows(input.value.trim()));
            }
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            const body = document.body;
            const KEY = 'eb_admin_sidebar_collapsed';

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