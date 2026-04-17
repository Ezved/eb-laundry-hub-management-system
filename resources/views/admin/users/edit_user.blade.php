<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $fullName  = $user->name ?? '';
        $parts     = preg_split('/\s+/', trim($fullName));
        $firstName = $parts[0] ?? $fullName;

    $from    = request('from', 'users'); // default to users if missing
    $backTo  = $from === 'customers' ? route('customers') : route('users');
@endphp


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Title: Edit + first name only --}}
    <title>Edit {{ $firstName }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr"
          crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
      rel="stylesheet">
    <link href="{{ asset('css/admin/users/edit_user.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>
<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container">
        <div class="card">
            <div class="card-header border-0 bg-transparent">
                {{-- Header: Edit + first name only --}}
                <h2 class="mb-0">Edit {{ $firstName }}</h2>
            </div>

            @if (Session::has('fail'))
                <div class="alert alert-danger m-3">{{ Session::get('fail') }}</div>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success m-3">{{ Session::get('success') }}</div>
            @endif

            <div class="card-body">
                <form action="{{ route('user.edit') }}" method="POST">
                    @csrf

                    {{-- Hidden user id --}}
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <input type="hidden" name="from" value="{{ $from }}">


                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text"
                               name="full_name"
                               value="{{ old('full_name', $user->name) }}"
                               class="form-control"
                               required>
                        @error('full_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="form-control"
                               required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text"
                               name="phone_number"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               class="form-control"
                               required>
                        @error('phone_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text"
                               name="address"
                               value="{{ old('address', $user->address) }}"
                               class="form-control"
                               required>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Location Details (optional) --}}
                    <div class="mb-3">
                        <label class="form-label">Location Details (optional)</label>
                        <textarea name="location_details"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Landmark, gate color, instructions...">{{ old('location_details', $user->location_details) }}</textarea>
                    </div>

                    @php $editingSelf = auth()->id() === $user->id; @endphp

                    {{-- Role (cannot demote yourself) --}}
                    @if (! $editingSelf)
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user"  {{ old('role', $user->role)  === 'user'  ? 'selected' : '' }}>User</option>
                            </select>
                            @error('role')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <input type="hidden" name="role" value="admin">
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ $backTo }}" class="btn btn-cancel" id="cancel-btn">Cancel</a>
                        <button type="submit" class="btn btn-pink" id="save-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
