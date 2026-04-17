<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Admin Profile</title>

    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin/dashboard/editAdminProfileInfo.css') }}">
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
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
                </a>
            </div>

            <hr>

            <ul class="nav nav-pills flex-column mb-auto">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('customers') }}"
                       class="nav-link {{ request()->routeIs('customers') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i>
                        <span>Customers</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.delivery.calendar') }}"
                       class="nav-link {{ request()->routeIs('admin.delivery.calendar') ? 'active' : '' }}">
                        <i class="bi bi-calendar me-2"></i>
                        <span>Delivery Calendar</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.sales.report') }}"
                       class="nav-link {{ request()->routeIs('admin.sales.report') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data me-2"></i>
                        <span>Sales Report</span>
                    </a>
                </li>

                <hr>

                <li>
                    <a href="{{ route('users') }}"
                       class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}">
                        <i class="bi bi-person-gear me-2"></i>
                        <span>Users</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('services') }}"
                       class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}">
                        <i class="bi bi-basket2 me-2"></i>
                        <span>Services</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.feedback.index') }}"
                       class="nav-link {{ request()->routeIs('admin.feedback.index') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots me-2"></i>
                        <span>Feedbacks</span>
                    </a>
                </li>
            </ul>

            <hr>

            <div class="dropdown text-end">
                <button id="btn-admin" class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle me-2" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                    <span class="fw-bold">Admin</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item fw-bold active-profile-link" id="admin-settings"
                           href="{{ route('admin.dashboard.editAdminProfileInfo') }}">
                            <i class="bi bi-person-circle me-2"></i>
                            Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="px-2">
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" id="btn-logout" class="dropdown-item fw-bold w-100 text-start">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="main-content p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h1 class="page-title mb-1">Edit Admin Profile</h1>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn-profile-back">
                    <i class="bi bi-arrow-left-short"></i>
                    Back to Dashboard
                </a>
            </div>

            <hr>

            @if (session('success'))
                <div class="alert alert-success eb-alert-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger eb-alert-danger mb-3">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="container-fluid">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <div class="profile-card-title-wrap">
                            <div class="profile-avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div>
                                <h2 class="profile-card-title mb-1">Admin Information</h2>
                                <p class="profile-card-subtitle mb-0">Update your account details below.</p>
                            </div>
                        </div>
                    </div>

                    <div class="profile-card-body">
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="profile-label">Full Name</label>
                                            <input
                                                type="text"
                                                id="name"
                                                name="name"
                                                class="form-control profile-input @error('name') is-invalid @enderror"
                                                value="{{ old('name', $admin->name ?? auth()->user()->name ?? '') }}">
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="profile-label">Email Address</label>
                                            <input
                                                type="email"
                                                id="email"
                                                name="email"
                                                class="form-control profile-input @error('email') is-invalid @enderror"
                                                value="{{ old('email', $admin->email ?? auth()->user()->email ?? '') }}">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="phone_number" class="profile-label">Phone Number</label>
                                            <input
                                                type="text"
                                                id="phone_number"
                                                name="phone_number"
                                                class="form-control profile-input @error('phone_number') is-invalid @enderror"
                                                value="{{ old('phone_number', $admin->phone_number ?? auth()->user()->phone_number ?? '') }}">
                                            @error('phone_number')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="address" class="profile-label">Address</label>
                                            <textarea
                                                id="address"
                                                name="address"
                                                rows="3"
                                                class="form-control profile-input profile-textarea @error('address') is-invalid @enderror">{{ old('address', $admin->address ?? auth()->user()->address ?? '') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="profile-side-card">
                                        <h3 class="profile-side-title">Security</h3>

                                        <div class="mb-3">
                                            <label for="current_password" class="profile-label">Current Password</label>
                                            <input
                                                type="password"
                                                id="current_password"
                                                name="current_password"
                                                class="form-control profile-input @error('current_password') is-invalid @enderror">
                                            @error('current_password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="profile-label">New Password</label>
                                            <input
                                                type="password"
                                                id="password"
                                                name="password"
                                                class="form-control profile-input @error('password') is-invalid @enderror">
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label for="password_confirmation" class="profile-label">Confirm Password</label>
                                            <input
                                                type="password"
                                                id="password_confirmation"
                                                name="password_confirmation"
                                                class="form-control profile-input">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-form-actions">
                                <a href="{{ route('admin.dashboard') }}" class="btn-cancel-profile">
                                    Cancel
                                </a>

                                <button type="submit" class="btn-save-profile">
                                    Save Changes
                                </button>
                            </div>
                        </form>
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
            const KEY = 'eb_admin_sidebar_collapsed';

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
                        localStorage.setItem(
                            KEY,
                            body.classList.contains('sidebar-collapsed') ? '1' : '0'
                        );
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
                    body.classList.remove('sidebar-mobile-open');
                });
            });
        });
    </script>
</body>
</html>