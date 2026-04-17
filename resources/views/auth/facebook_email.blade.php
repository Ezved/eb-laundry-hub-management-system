<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>E&B Laundry Hub — Link Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Keep it standalone for reliability --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-3 text-center">Finish Facebook sign-in</h1>
                        <p class="text-muted small text-center">
                            Facebook didn’t share your email. Please enter it once to create or link your account.
                        </p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('auth.facebook.email.store', [], false) }}"
                            class="mt-3"> @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Continue
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('login', [], false) }}" class="small">Back to login</a>
                        </div>
                    </div>
                </div>
                <p class="text-center text-muted small mt-3">
                    We’ll store this email to create/link your account and log you in.
                </p>
            </div>
        </div>
    </div>
</body>

</html>