<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Feedback</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/user/dashboard/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/user/feedback/feedback.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>
<body>
    <div class="mobile-topbar d-lg-none">
        <button type="button" id="mobileSidebarToggle" class="btn mobile-toggle-btn" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="mobile-brand">
            <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
            <span>E&amp;B Laundry Hub</span>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-shell">
        <aside id="side-bar" class="d-flex flex-column shrink-0 p-3">
            <div class="d-flex justify-content-end mb-2">
                <button type="button" id="sidebarToggle" class="btn btn-sm sidebar-toggle-btn" title="Collapse sidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="logo-wrapper">
                <a href="{{ route('user.dashboard') }}">
                    <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
                </a>
            </div>

            <hr>

            @php
                $is = fn(...$r) => request()->routeIs(...$r) ? 'active' : '';
            @endphp

            <ul class="nav nav-pills flex-column mb-auto">
                <li>
                    <a href="{{ route('user.dashboard') }}" class="nav-link {{ $is('user.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('loyalty.membership') }}"
                       class="nav-link {{ $is('loyalty.membership', 'loyalty_membemship') }}">
                        <i class="bi bi-award me-2"></i>
                        <span>Loyalty Membership</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.orders.history') }}"
                       class="nav-link {{ $is('user.orders.history', 'user.orderHistory') }}">
                        <i class="bi bi-clock-history me-2"></i>
                        <span>Order History</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.feedback') }}"
                       class="nav-link {{ $is('user.feedback') }}">
                        <i class="bi bi-chat-dots me-2"></i>
                        <span>Feedback</span>
                    </a>
                </li>
            </ul>

            <hr>

            <div class="dropdown text-end">
                <button id="btn-admin" class="btn dropdown-toggle d-flex align-items-center" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-person-circle me-2" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd"
                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                    <span class="fw-bold">{{ explode(' ', auth()->user()->name)[0] }}</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item fw-bold" id="admin-settings" href="{{ route('settings.edit') }}">
                            <i class="bi bi-gear me-2"></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" id="btn-logout" class="dropdown-item fw-bold">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="main-content p-4 feedback-page">
            <h1 class="page-title">Feedback</h1>
            <hr>

            <div class="container-fluid feedback-container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show feedback-alert feedback-alert-success" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show feedback-alert feedback-alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card feedback-unified-card">
                    <div class="card-header feedback-hero" id="card-header">
                        <div class="feedback-hero-badge">
                            <i class="bi bi-chat-heart-fill"></i>
                        </div>

                        <div class="feedback-hero-text">
                            <h2 class="mb-1">Share Your Experience</h2>
                            <p class="mb-0">
                                Your feedback helps us improve our laundry service and customer experience.
                            </p>
                        </div>

                        <div class="feedback-hero-count">
                            <span>{{ ($myFeedback ?? collect())->count() }}</span>
                            <small>{{ ($myFeedback ?? collect())->count() === 1 ? 'Feedback' : 'Feedbacks' }}</small>
                        </div>
                    </div>

                    <div class="card-body feedback-unified-body">
                        <section class="feedback-section-block">
                            <div class="feedback-section-head">
                                <div>
                                    <h3>Leave Feedback</h3>
                                    <p>Tell us what you liked and what we can improve.</p>
                                </div>
                            </div>

                            <form action="{{ route('feedback.store') }}" method="POST" class="feedback-form">
                                @csrf

                                <div class="feedback-form-grid">
                                    <div class="feedback-form-main">
                                        <div class="feedback-field">
                                            <label class="feedback-label">
                                                <i class="bi bi-star-fill me-1"></i>
                                                Rate Your Experience
                                            </label>

                                            <div class="star-input" aria-label="Rating from 1 to 5">
                                                <input type="radio" id="r5" name="rating" value="5" {{ old('rating', 5) == 5 ? 'checked' : '' }}>
                                                <label for="r5" title="5 stars">★</label>

                                                <input type="radio" id="r4" name="rating" value="4" {{ old('rating') == 4 ? 'checked' : '' }}>
                                                <label for="r4" title="4 stars">★</label>

                                                <input type="radio" id="r3" name="rating" value="3" {{ old('rating') == 3 ? 'checked' : '' }}>
                                                <label for="r3" title="3 stars">★</label>

                                                <input type="radio" id="r2" name="rating" value="2" {{ old('rating') == 2 ? 'checked' : '' }}>
                                                <label for="r2" title="2 stars">★</label>

                                                <input type="radio" id="r1" name="rating" value="1" {{ old('rating') == 1 ? 'checked' : '' }}>
                                                <label for="r1" title="1 star">★</label>
                                            </div>

                                            <div class="feedback-rating-note">
                                                Choose from 1 to 5 stars.
                                            </div>
                                        </div>

                                        <div class="feedback-field">
                                            <label for="message" class="feedback-label">
                                                <i class="bi bi-pencil-square me-1"></i>
                                                Your Feedback
                                            </label>

                                            <textarea
                                                id="message"
                                                class="form-control feedback-textarea"
                                                name="message"
                                                rows="5"
                                                maxlength="1000"
                                                placeholder="Share your experience with us... (max 1000 characters)"
                                                required>{{ old('message') }}</textarea>

                                            <div class="feedback-form-help">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Help us improve with honest feedback.
                                            </div>
                                        </div>
                                    </div>

                                    <aside class="feedback-side-note">
                                        <div class="feedback-tip-card">
                                            <div class="feedback-tip-icon">
                                                <i class="bi bi-lightbulb-fill"></i>
                                            </div>
                                            <h4>Helpful Tips</h4>
                                            <ul>
                                                <li>Rate your overall service experience.</li>
                                                <li>Mention service quality and timing.</li>
                                                <li>Tell us what we can improve.</li>
                                            </ul>
                                        </div>
                                    </aside>
                                </div>

                                <div class="feedback-submit-wrap">
                                    <button type="submit" class="btn feedback-submit-btn">
                                        <i class="bi bi-send-fill me-2"></i>
                                        Submit Feedback
                                    </button>
                                </div>
                            </form>
                        </section>

                        <section class="feedback-section-block feedback-history-section">
                            <div class="feedback-section-head">
                                <div>
                                    <h3>My Recent Feedback</h3>
                                    <p>Your latest submitted reviews and comments.</p>
                                </div>
                            </div>

                            @if (($myFeedback ?? collect())->isNotEmpty())
                                <div class="feedback-list">
                                    @foreach($myFeedback as $fb)
                                        <div class="feedback-item">
                                            <div class="feedback-item-top">
                                                <div class="feedback-date-wrap">
                                                    <i class="bi bi-calendar3"></i>
                                                    <span>
                                                        {{ $fb->created_at->format('M d, Y') }}
                                                        <small>at {{ $fb->created_at->format('h:i A') }}</small>
                                                    </span>
                                                </div>

                                                <form action="{{ route('feedback.destroy', $fb) }}" method="POST"
                                                      onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn feedback-delete-btn" title="Delete feedback">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="feedback-stars-row">
                                                <div class="feedback-stars" aria-label="Rating: {{ $fb->rating }} out of 5">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        {!! $i <= (int) $fb->rating ? '★' : '☆' !!}
                                                    @endfor
                                                </div>
                                                <span class="feedback-stars-text">{{ $fb->rating }}/5</span>
                                            </div>

                                            <div class="feedback-message">
                                                {{ $fb->message }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="feedback-empty">
                                    <div class="feedback-empty-icon">
                                        <i class="bi bi-chat-dots"></i>
                                    </div>
                                    <h3>No feedback yet</h3>
                                    <p>You haven’t submitted any feedback yet. Share your experience using the form above.</p>
                                </div>
                            @endif
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;

            const body = document.body;
            const KEY = 'eb_user_sidebar_collapsed';

            try {
                if (localStorage.getItem(KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                body.classList.toggle('sidebar-collapsed');

                try {
                    localStorage.setItem(KEY, body.classList.contains('sidebar-collapsed') ? '1' : '0');
                } catch (e) {}
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const mobileBtn = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (mobileBtn) {
                mobileBtn.addEventListener('click', function () {
                    document.body.classList.add('sidebar-mobile-open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            }

            document.querySelectorAll('#side-bar .nav-link, #side-bar .dropdown-item').forEach(el => {
                el.addEventListener('click', function () {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            });
        });
    </script>
</body>
</html>