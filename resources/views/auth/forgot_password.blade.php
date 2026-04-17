<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password • E&amp;B Laundry Hub</title>

    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/forgotPassword.css') }}">
</head>
<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="forgot-page">
        <div class="forgot-card">
            <div class="forgot-card-header">
                <h1>Forgot Password</h1>
                <p>Enter your email address and we’ll send you a reset link for your account.</p>
            </div>

            <div class="forgot-card-body">
                @if (session('status'))
                    <div class="status-text">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="forgot-form">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') input-error @enderror"
                            required
                            autocomplete="email"
                            autofocus>
                        @error('email')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary-action">
                        Continue
                    </button>
                </form>

                <div class="back-link">
                    Remembered your password?
                    <a href="{{ route('login') }}">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>