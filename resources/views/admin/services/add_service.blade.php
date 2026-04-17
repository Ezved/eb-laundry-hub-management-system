<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Add New Service</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
          rel="stylesheet">
    <link href="{{ asset('css/admin/services/add_service.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>
<body class="bg-blur" style="--page-bg: url('{{ asset('images/E&B_Pic.png') }}');">
    <div class="container">
        <div class="card">
            <div class="card-header border-0">
                <h2>Add New Service</h2>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif

                @if (session('fail'))
                    <div class="alert alert-danger mb-3">{{ session('fail') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('service.add') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Title</label>
                            <input name="title"
                                   class="form-control"
                                   value="{{ old('title') }}"
                                   required>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="5">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Price (₱)</label>
                            <input name="price"
                                   type="number"
                                   step="0.01"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('price') }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sort Order</label>
                            <input name="sort_order"
                                   type="number"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('sort_order', 0) }}">
                        </div>

                        <div class="form-group full-width">
                            <div class="service-toggle-wrap">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', 1) ? 'checked' : '' }}>
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