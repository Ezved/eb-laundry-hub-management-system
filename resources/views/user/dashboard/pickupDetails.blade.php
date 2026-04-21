<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $fullName  = auth()->user()->name ?? '';
        $parts     = preg_split('/\s+/', trim($fullName));
        $firstName = $parts[0] ?? $fullName;
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup Details</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/user/dashboard/pickupDetails.css') }}">
</head>

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="edit-page">
        <div class="edit-card">
            <div class="edit-card-header">
                <h2>Pickup Details</h2>
                <p>Review and update your pickup information before continuing.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success mx-4 mt-4 mb-0">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mx-4 mt-4 mb-0">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="edit-card-body">
                <form action="{{ route('pickup.update') }}" method="POST">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Full Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', auth()->user()->name) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', auth()->user()->email) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input
                                type="text"
                                name="phone_number"
                                class="form-control"
                                value="{{ old('phone_number', auth()->user()->phone_number) }}">
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <textarea
                                name="address"
                                class="form-control"
                                rows="3"
                                required>{{ old('address', auth()->user()->address) }}</textarea>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Location Details</label>
                            <textarea
                                name="location_details"
                                class="form-control"
                                rows="3">{{ old('location_details', auth()->user()->location_details) }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-cancel">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-save">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>