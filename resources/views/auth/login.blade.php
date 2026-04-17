<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
    rel="stylesheet">
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <a href="{{ route('home') }}" class="text-center pt-3">
                        <img src="{{ asset('images/E&B_Logo.png') }}" alt="E&B Laundry Hub Logo" width="72"
                            height="72">
                    </a>

                    <div class="card-body">
                        {{-- success messages (logout, resend link, etc.) --}}
                        @if (Session::has('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif

                        {{-- error message (includes verify-first message) --}}
                        @if (Session::has('fail'))
                            <div class="alert alert-danger">
                                {{ Session::get('fail') }}

                                @if (Session::get('fail') === 'Please verify your email first. Check your inbox.')
                                    <br>
                                    <button type="button"
                                        class="btn btn-link p-0 fw-semibold text-decoration-underline"
                                        onclick="
                            event.preventDefault();
                            var loginEmail = document.getElementById('email')
                                ? document.getElementById('email').value
                                : '';
                            document.getElementById('resend-email-hidden').value = loginEmail;
                            document.getElementById('resend-verification-form').submit();
                        ">
                                        Resend verification email
                                    </button>
                                @endif
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- HIDDEN FORM for resend verification --}}
                        <form id="resend-verification-form" method="POST" action="{{ route('verification.resend') }}"
                            class="d-none">
                            @csrf
                            <input type="hidden" name="email" id="resend-email-hidden">
                        </form>


                        <form action="{{ route('login.post') }}" method="POST" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" autocomplete="email" required>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label mb-0">Password</label>
                                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                                </div>

                                <input type="password" class="form-control" id="password" name="password"
                                    autocomplete="current-password" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-pink w-100">Login</button>
                        </form>

                        <div class="text-center text-muted my-3">or</div>

                        @include('auth.LoginWithGoogle')
                        @include('auth.LoginWithFacebook')

                        <div class="mt-3 text-center">
                            Don’t have an account?
                            <a href="{{ route('register') }}">Register here</a><br>
                            {{-- added 11/16/2025 --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Bootstrap JS (if you need dropdowns/toasts here) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    @if (session('verify_notice'))
        <!-- Email Verification Modal -->
        <div class="modal fade" id="verifyEmailModal" tabindex="-1" aria-labelledby="verifyEmailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="verifyEmailModalLabel">Verify your email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{ session('verify_notice') }}
                        <br><br>
                        <small class="text-muted">
                            If you don’t see the email, please check your Spam or Promotions folder.
                        </small>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-pink" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalEl = document.getElementById('verifyEmailModal');
                if (modalEl) {
                    var verifyModal = new bootstrap.Modal(modalEl);
                    verifyModal.show();
                }
            });
        </script>
    @endif
</body>

</html>
