<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Your Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/pickupDetails.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header"><h2 style="color: #173F7B; font-weight:bold; margin-top:15px">Pickup Details</h2></div>
            
            <div class="card-body">
                <form action="{{ route('pickup.update') }}" method="post">
                    @csrf

                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control"
                               value="{{ old('phone_number', auth()->user()->phone_number) }}">
                        @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3" required>{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location Details</label>
                        <textarea class="form-control" name="location_details" rows="3">{{ old('location_details', auth()->user()->location_details) }}</textarea>
                        @error('location_details') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="choice">
                        <div class="btn-save"><button type="submit" class="btn btn-pink" id="save-btn">Save</button></div>
                        <div class="btn-cancel"><a href="{{  route('user.dashboard') }}" class="btn btn-pink" id="cancel-btn">Cancel</a></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
