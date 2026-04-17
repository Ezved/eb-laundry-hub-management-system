<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $fullName  = $customer->name ?? '';
        $parts     = preg_split('/\s+/', trim($fullName));
        $firstName = $parts[0] ?? $fullName;
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $firstName }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/admin/customers/edit.css') }}">
</head> 

<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="edit-page">
        <div class="edit-card">
            <div class="edit-card-header">
                <h2>Edit {{ $firstName }}</h2>
            </div>

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
                <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Full Name</label>
                            <input
                                name="name"
                                class="form-control"
                                value="{{ old('name', $customer->name) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input
                                name="email"
                                type="email"
                                class="form-control"
                                value="{{ old('email', $customer->email) }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input
                                name="phone_number"
                                class="form-control"
                                value="{{ old('phone_number', $customer->phone_number) }}">
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <input
                                name="address"
                                class="form-control"
                                value="{{ old('address', $customer->address) }}">
                        </div>
                    </div>

                    <div class="form-actions">                   
                        <a href="{{ route('customers') }}" class="btn btn-cancel">
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