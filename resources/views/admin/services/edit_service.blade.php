<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin/services/edit_service.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>
<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container">
        <div class="card">
            <div class="card-header border-0">
                <h2>Edit Service</h2>
                <p>Update service details for E&B Laundry Hub.</p>
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

            @if ($errors->any())
                <div class="alert alert-danger mx-4 mt-3 mb-0">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body">
                <form method="POST" action="{{ route('service.edit') }}">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Title</label>
                            <input name="title"
                                   class="form-control"
                                   value="{{ old('title', $service->title) }}"
                                   required>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="5">{{ old('description', $service->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Price (₱)</label>
                            <input name="price"
                                   type="number"
                                   step="0.01"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('price', $service->price) }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sort Order</label>
                            <input name="sort_order"
                                   type="number"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('sort_order', $service->sort_order) }}">
                        </div>

                        <div class="form-group full-width">
                            <div class="service-toggle-wrap">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('services') }}" class="btn btn-cancel" id="cancel-btn">Cancel</a>
                        <button type="submit" class="btn btn-save" id="save-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>