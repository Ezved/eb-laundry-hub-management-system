{{-- resources/views/auth/enter_new_password.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Set New Password • E&B Laundry Hub</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ffb5fe, #f9d3ff, #f7c1ff);
        }

        .forgot-wrapper {
            width: 100%;
            max-width: 430px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
            padding: 32px 30px 28px;
        }

        .logo-text {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo-text h1 {
            font-size: 34px;
            letter-spacing: 4px;
            font-weight: 800;
            color: #2e2a60;
            margin-bottom: 4px;
        }

        .logo-text span {
            display: block;
            font-size: 18px;
            font-weight: 600;
            color: #ff4fb9;
        }

        .title {
            text-align: center;
            margin: 18px 0 8px;
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        .subtitle {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }

        .countdown-text {
            text-align: center;
            font-size: 12px;
            color: #aa336a;
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #444;
        }

        .input-field {
            width: 100%;
            padding: 11px 12px;
            border-radius: 8px;
            border: 1px solid #e0b9ff;
            outline: none;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-field:focus {
            border-color: #ff5fd3;
            box-shadow: 0 0 0 2px rgba(255, 95, 211, 0.2);
        }

        .input-field.input-error {
            border-color: #e63946;
        }

        .error-text {
            font-size: 12px;
            color: #e63946;
            margin-top: 4px;
        }

        .btn {
            width: 100%;
            margin-top: 18px;
            padding: 11px 12px;
            border-radius: 999px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(90deg, #ff6bd9, #ff7ee8);
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease, opacity 0.1s ease;
        }

        .btn:hover {
            box-shadow: 0 8px 16px rgba(255, 107, 217, 0.35);
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: none;
            opacity: 0.9;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .back-link {
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: #555;
        }

        .back-link a {
            color: #ff40c0;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="forgot-wrapper">
        <!-- Brand text -->
        <div class="logo-text">
            <h1>E&B</h1>
            <span>Laundry Hub</span>
        </div>

        <h2 class="title">Set New Password</h2>
        <p class="subtitle">Enter your new password below to secure your account.</p>

        {{-- Countdown text --}}
        <p class="countdown-text" id="expiry-subtitle">
            This password reset link will expire in
            <span id="countdown">10:00</span>.
        </p>

        <form action="{{ route('password.update') }}" method="post" id="reset-form">
            @csrf

            {{-- Hidden email and token --}}
            <input type="hidden" name="email" value="{{ $email ?? request('email') }}">
            <input type="hidden" name="token" value="{{ $token ?? request('token') }}">

            <label for="password">New password</label>
            <input type="password" id="password" name="password"
                class="input-field @error('password') input-error @enderror" placeholder="Enter new password"
                required />
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror

            <label for="password_confirmation" style="margin-top: 10px;">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="input-field"
                placeholder="Re-enter new password" required />

            <button type="submit" class="btn" id="submit-btn">Update password</button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </div>

    {{-- Accurate countdown timer --}}
    <script>
        (function() {
            // Safely embed PHP value into JS:
            // - If $expiresAtMs is a number, this becomes: const expiresAtMs = 1731765000000;
            // - If it's null/missing, this becomes:       const expiresAtMs = null;
            const expiresAtMs = {{ $expiresAtMs ?? 'null' }};

            if (expiresAtMs === null) {
                return; // no expiry info → don't run timer
            }

            const countdownEl = document.getElementById('countdown');
            const subtitleEl = document.getElementById('expiry-subtitle');
            const form = document.getElementById('reset-form');
            const submitBtn = document.getElementById('submit-btn');
            let timerId = null;

            function updateCountdown() {
                const now = Date.now();
                let diffMs = expiresAtMs - now;

                if (diffMs <= 0) {
                    if (timerId !== null) {
                        clearInterval(timerId);
                    }

                    if (countdownEl) {
                        countdownEl.textContent = '00:00';
                    }
                    if (subtitleEl) {
                        subtitleEl.textContent = 'This password reset link has expired. Please request a new one.';
                    }

                    if (form) {
                        Array.from(form.elements).forEach(function(el) {
                            el.disabled = true;
                        });
                    }

                    return;
                }

                const totalSeconds = Math.floor(diffMs / 1000);
                const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
                const seconds = String(totalSeconds % 60).padStart(2, '0');

                if (countdownEl) {
                    countdownEl.textContent = minutes + ':' + seconds;
                }
            }

            // Initial draw and then every second
            updateCountdown();
            timerId = setInterval(updateCountdown, 1000);
        })();
    </script>
</body>

</html>
