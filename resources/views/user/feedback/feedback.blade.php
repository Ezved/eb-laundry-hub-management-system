<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Feedback</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
  <link href="{{ asset('css/user/dashboard/dashboard.css') }}" rel="stylesheet">
  <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">

  <style>
    /* Star rating input styling */
    .star-input {
      display: flex;
      flex-direction: row-reverse;
      justify-content: flex-end;
      gap: 0.25rem;
    }
    .star-input input[type="radio"] {
      display: none;
    }
    .star-input label {
      font-size: 2rem;
      color: #ddd;
      cursor: pointer;
      transition: color 0.2s ease;
    }
    .star-input input:checked ~ label,
    .star-input label:hover,
    .star-input label:hover ~ label {
      color: #ffc107;
    }

    /* My feedback item styling */
    .myfb-item {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      transition: all 0.2s ease;
    }
    .myfb-item:hover {
      background: #fff;
      border-color: #FFB5FE;
      box-shadow: 0 2px 8px rgba(255, 181, 254, 0.2);
    }
    .myfb-stars {
      color: #ffc107;
      font-size: 1.1rem;
      letter-spacing: 2px;
    }
    .myfb-msg {
      color: #333;
      line-height: 1.6;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- Side Bar -->
    <div id="side-bar" class="d-flex flex-column flex-shrink-0 p-3" style="width:280px;height:100vh;">
      
      {{-- Top-right toggle above the logo --}}
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
          <a href="{{ route('user.dashboard') }}" 
             class="nav-link {{ $is('user.dashboard') }}" 
             id="nav-dashboard">
            <i class="bi bi-speedometer2 me-2"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="{{ route('loyalty.membership') }}" 
             class="nav-link {{ $is('loyalty.membership', 'loyalty_membemship') }}" 
             id="nav-customers">
            <i class="bi bi-award me-2"></i>
            <span>Loyalty Membership</span>
          </a>
        </li>
        <li>
          <a href="{{ route('user.orders.history') }}" 
             class="nav-link {{ $is('user.orders.history') }}" 
             id="nav-services">
            <i class="bi bi-clock-history me-2"></i>
            <span>Order History</span>
          </a>
        </li>
        <li>
          <a href="{{ route('user.feedback') }}" 
             class="nav-link {{ $is('user.feedback') }}" 
             id="nav-feedback">
            <i class="bi bi-chat-dots me-2"></i>
            <span>Feedback</span>
          </a>
        </li>
      </ul>

      <hr>
      <div class="dropdown text-end">
        <button id="btn-admin" class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle me-2" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
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
          <li><hr class="dropdown-divider"></li>
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
    </div>

    <!-- Main Content -->
    <div class="p-4" style="flex-grow:1; background-color:#ffffff; height:100vh; overflow-y:auto;">
      <h1 class="page-title">Feedback</h1>
      <hr>

      {{-- Flash Messages --}}
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background-color: #d4edda; color: #155724;">
          <i class="bi bi-check-circle-fill me-2"></i>
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background-color: #f8d7da; color: #721c24;">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <ul class="mb-0">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <div class="container">
        {{-- Leave Feedback Form Card --}}
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center justify-content-between" id="card-header">
            <div class="d-flex align-items-center">
              <h2 style="margin: 0; color: #173F7B; font-weight: 800; font-size: 1.75rem;">
                <i class="bi bi-chat-left-quote me-2"></i>
                Leave Feedback
              </h2>
            </div>
          </div>
          <div class="card-body">
            <form action="{{ route('feedback.store') }}" method="POST">
              @csrf

              <div class="mb-4">
                <label class="form-label fw-semibold" style="color: #173F7B;">
                  <i class="bi bi-star-fill me-1" style="color: #ffc107;"></i>
                  Please Rate Your Experience
                </label>
                <div class="star-input" aria-label="Rating from 1 to 5">
                  <input type="radio" id="r5" name="rating" value="5" checked>
                  <label for="r5">★</label>
                  <input type="radio" id="r4" name="rating" value="4">
                  <label for="r4">★</label>
                  <input type="radio" id="r3" name="rating" value="3">
                  <label for="r3">★</label>
                  <input type="radio" id="r2" name="rating" value="2">
                  <label for="r2">★</label>
                  <input type="radio" id="r1" name="rating" value="1">
                  <label for="r1">★</label>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold" style="color: #173F7B;">
                  <i class="bi bi-pencil-square me-1"></i>
                  Your Feedback
                </label>
                <textarea 
                  class="form-control" 
                  name="message" 
                  rows="5" 
                  maxlength="1000" 
                  placeholder="Share your experience with us... (max 1000 characters)" 
                  required
                  style="border-radius: 8px; border: 1px solid #ced4da;"></textarea>
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Help us improve by sharing your honest feedback
                </div>
              </div>

              <button 
                type="submit" 
                class="btn btn-primary"
                style="background: #173F7B !important; color: #fff !important; border: none; border-radius: 20px; font-weight: 600; padding: 0.5rem 2rem; transition: all 0.2s ease;"
                onmouseover="this.style.background='#102c59'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 3px 6px rgba(0,0,0,0.15)';"
                onmouseout="this.style.background='#173F7B'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <i class="bi bi-send-fill me-2"></i>
                Submit Feedback
              </button>
            </form>
          </div>
        </div>

        {{-- My Recent Feedback Card --}}
        @if(($myFeedback ?? collect())->isNotEmpty())
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between" id="card-header">
              <div class="d-flex align-items-center">
                <h2 style="margin: 0; color: #173F7B; font-weight: 800; font-size: 1.75rem;">
                  <i class="bi bi-clock-history me-2"></i>
                  My Recent Feedback
                </h2>
              </div>
              <span class="badge" style="background: #173F7B; font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                {{ $myFeedback->count() }} {{ $myFeedback->count() === 1 ? 'feedback' : 'feedbacks' }}
              </span>
            </div>

            <div class="card-body">
              <ul class="list-unstyled mb-0">
                @foreach($myFeedback as $fb)
                  <li class="mb-3 p-3 rounded-3 myfb-item">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                      <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                          <i class="bi bi-calendar3" style="color: #6c757d;"></i>
                          <span class="text-muted small">{{ $fb->created_at->format('M d, Y') }} at {{ $fb->created_at->format('h:i A') }}</span>
                        </div>
                        <div class="mb-2 myfb-stars">
                          @for($i=1;$i<=5;$i++) 
                            {!! $i <= (int)$fb->rating ? '★' : '☆' !!}
                          @endfor
                          <span class="ms-2 text-muted small">({{ $fb->rating }}/5)</span>
                        </div>
                        <div class="myfb-msg">{{ $fb->message }}</div>
                      </div>

                      <form action="{{ route('feedback.destroy', $fb) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                        @csrf @method('DELETE')
                        <button 
                          type="submit" 
                          class="btn btn-sm btn-outline-danger"
                          style="border-radius: 8px; padding: 0.35rem 0.75rem;"
                          title="Delete feedback">
                          <i class="bi bi-trash3"></i>
                        </button>
                      </form>
                    </div>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        @else
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between" id="card-header">
              <div class="d-flex align-items-center">
                <h2 style="margin: 0; color: #173F7B; font-weight: 800; font-size: 1.75rem;">
                  <i class="bi bi-clock-history me-2"></i>
                  My Recent Feedback
                </h2>
              </div>
            </div>
            <div class="card-body text-center py-5">
              <i class="bi bi-chat-dots" style="font-size: 3rem; color: #FFB5FE;"></i>
              <p class="text-muted mt-3 mb-0">You haven't submitted any feedback yet.</p>
              <p class="text-muted small">Share your experience using the form above!</p>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const btn = document.getElementById('sidebarToggle');
      if (!btn) return;

      const body = document.body;
      const KEY = 'eb_user_sidebar_collapsed';

      // Restore last state
      try {
        if (localStorage.getItem(KEY) === '1') {
          body.classList.add('sidebar-collapsed');
        }
      } catch (e) {}

      btn.addEventListener('click', function(e) {
        e.preventDefault();
        body.classList.toggle('sidebar-collapsed');

        try {
          localStorage.setItem(
            KEY,
            body.classList.contains('sidebar-collapsed') ? '1' : '0'
          );
        } catch (e) {}
      });
    });
  </script>

</body>
</html>