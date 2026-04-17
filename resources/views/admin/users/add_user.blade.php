<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add New User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin/users/add_user.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>
<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container">
        <div class="card">
            <div class="card-header border-0">
                <h2>Add New User</h2>
                <p>Create a new user account for E&B Laundry Hub.</p>
            </div>

            @if (Session::has('fail'))
                <div class="alert alert-danger mx-4 mt-3 mb-0">
                    {{ Session::get('fail') }}
                </div>
            @endif

            @if (Session::has('success'))
                <div class="alert alert-success mx-4 mt-3 mb-0">
                    {{ Session::get('success') }}
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('user.add') }}" method="post">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control" required>
                            @error('full_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" value="{{ old('email') }}" class="form-control" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="form-control">
                            @error('phone_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="User" readonly>
                            <input type="hidden" name="role" value="user">
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('users') }}" class="btn btn-cancel" id="cancel-btn">Cancel</a>
                        <button type="submit" class="btn btn-save" id="save-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>