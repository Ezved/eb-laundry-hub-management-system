<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
    rel="stylesheet">
    <link href="{{ asset('css/auth/register.css') }}" rel="stylesheet">
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
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>

                                <ul class="mb-0">
                                    {{-- Extra note for "email has already been taken" --}}
                                    @if ($errors->has('email') && $errors->first('email') === 'The email has already been taken.')
                                        <p class="mt-2 mb-0 small">
                                            Please login with that email.
                                        </p>
                                    @endif
                                </ul>

                                <ul class="mb-0">
                                    {{-- Extra note for "email has already been taken" --}}
                                    @if ($errors->has('email') && $errors->first('email') === 'The email has already been taken.')
                                        <p class="mt-2 mb-0 small">
                                            Or sign in using social media account.
                                        </p>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('register.post') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-pink">Register</button>
                        </form>
                        <div class="mt-3">
                            Already have an account? <a href="{{ route('login') }}">Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
