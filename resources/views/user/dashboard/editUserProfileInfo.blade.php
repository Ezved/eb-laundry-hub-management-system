<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Profile</title>

    <!-- Bootstrap + Fonts + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>

<body>
<div class="d-flex">
    {{-- Sidebar (same as user dashboard) --}}
    <div id="side-bar" class="d-flex flex-column flex-shrink-0 p-3" style="width:280px;height:100vh;">

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
    </div>

    {{-- Main content --}}
    <div class="p-4" style="flex-grow:1; background-color:#ffffff; height:100vh; overflow-y:auto;">
        <h1 class="page-title">Profile Settings</h1>
        <hr>

        <div class="container">
            <div class="card">
                {{-- Card header --}}
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" id="card-header">
                    <div>
                        <h2 style="margin: 0 0 0.25rem 0; color: #173F7B; font-weight: 800; font-size: 1.75rem;">
                            <i class="bi bi-person-circle me-2"></i>
                            Edit Profile
                        </h2>
                        <div class="small text-muted">
                            Update your personal information and optionally change your password.
                        </div>
                    </div>

                    {{-- Back button --}}
                    <a class="btn btn-sm"
                       href="{{ route('user.dashboard') }}"
                       style="background:#173F7B; color:#fff; font-weight:600; border-radius:20px; border:none; padding: 0.45rem 1.2rem; transition: all 0.2s ease;"
                       onmouseover="this.style.background='#102c59'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 3px 6px rgba(0,0,0,0.15)';"
                       onmouseout="this.style.background='#173F7B'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Dashboard
                    </a>
                </div>

                <div class="card-body p-4">
                    {{-- Flash success --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background-color: #d4edda; color: #155724;">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background-color: #f8d7da; color: #721c24;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div class="fw-bold mb-1">Please fix the following:</div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.update') }}" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Personal Information Section --}}
                        <div class="mb-4">
                            <h5 class="mb-3" style="color:#173F7B; font-weight:700;">
                                <i class="bi bi-info-circle me-2"></i>
                                Personal Information
                            </h5>

                            <div class="row g-3">
                                {{-- Name --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color:#173F7B;">
                                        <i class="bi bi-person me-1"></i>
                                        Full Name
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}"
                                        required
                                        style="border-radius: 8px; border: 1px solid #ced4da;"
                                    >
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color:#173F7B;">
                                        <i class="bi bi-envelope me-1"></i>
                                        Email Address
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}"
                                        required
                                        style="border-radius: 8px; border: 1px solid #ced4da;"
                                    >
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color:#173F7B;">
                                        <i class="bi bi-telephone me-1"></i>
                                        Phone Number
                                    </label>
                                    <input
                                        type="text"
                                        name="phone_number"
                                        class="form-control @error('phone_number') is-invalid @enderror"
                                        value="{{ old('phone_number', $user->phone_number) }}"
                                        required
                                        style="border-radius: 8px; border: 1px solid #ced4da;"
                                    >
                                    @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Address --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color:#173F7B;">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        Address
                                    </label>
                                    <input
                                        type="text"
                                        name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        value="{{ old('address', $user->address) }}"
                                        required
                                        style="border-radius: 8px; border: 1px solid #ced4da;"
                                    >
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Location Details --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold" style="color:#173F7B;">
                                        <i class="bi bi-pin-map me-1"></i>
                                        Location Details (Optional)
                                    </label>
                                    <input
                                        type="text"
                                        name="location_details"
                                        class="form-control @error('location_details') is-invalid @enderror"
                                        value="{{ old('location_details', $user->location_details) }}"
                                        placeholder="e.g. Landmark, floor, gate, building details"
                                        style="border-radius: 8px; border: 1px solid #ced4da;"
                                    >
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Help us find you easily by providing nearby landmarks or specific directions
                                    </div>
                                    @error('location_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Change Password section --}}
                        <hr class="my-4">
                        
                        <div class="mb-4">
                            <h5 class="mb-3" style="color:#173F7B; font-weight:700;">
                                <i class="bi bi-shield-lock me-2"></i>
                                Change Password (Optional)
                            </h5>
                            <p class="text-muted small mb-3">
                                Leave password fields blank if you don't want to change your current password.
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color:#173F7B;">
                                        <i class="bi bi-key me-1"></i>
                                        New Password
                                    </label>
                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Enter new password"
                                        style="border-radius: 8px; border: 1px solid #ced4da;"
                                    >
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color:#173F7B;">
                                        <i class="bi bi-key-fill me-1"></i>
                                        Confirm New Password
                                    </label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Re-type new password"
                                        style="border-radius: 8px; border: 1px solid #ced4da;"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-4">
                            <div class="d-flex justify-content-between flex-wrap gap-2 p-3"
                                 style="background:#FFB5FE; border-radius:12px;">
                                <button type="submit"
                                        class="btn px-4"
                                        style="background: #10a80d; color: #fff; font-weight:600; border-radius:20px; border:none; padding: 0.5rem 2rem; transition: all 0.2s ease;"
                                        onmouseover="this.style.background='#307a2e'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 3px 6px rgba(0,0,0,0.15)';"
                                        onmouseout="this.style.background='#10a80d'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Save Changes
                                </button>

                                <a href="{{ route('user.dashboard') }}"
                                   class="btn px-4"
                                   style="background: #dc3545; color: #fff; font-weight:600; border-radius:20px; border:none; padding: 0.5rem 2rem; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='#c82333'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 3px 6px rgba(0,0,0,0.15)';"
                                   onmouseout="this.style.background='#dc3545'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- /.container -->
    </div> <!-- /.main area -->
</div> <!-- /.d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

{{-- Sidebar collapse logic (same as user dashboard) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn  = document.getElementById('sidebarToggle');
        if (!btn) return;

        const body = document.body;
        const KEY  = 'eb_user_sidebar_collapsed';

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
</body>
</html>