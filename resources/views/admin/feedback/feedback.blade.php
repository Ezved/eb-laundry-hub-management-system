<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Feedbacks</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/admin/feedback/feedback.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
</head>
<body>
    <!-- Mobile top bar -->
    <div class="mobile-topbar d-lg-none">
        <button type="button" id="mobileSidebarToggle" class="btn mobile-toggle-btn" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="mobile-brand">
            <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
            <span>E&B Laundry Hub</span>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-shell">
        <!-- Sidebar -->
        <aside id="side-bar" class="d-flex flex-column flex-shrink-0 p-3">
            <div class="d-flex justify-content-end mb-2">
                <button type="button"
                        id="sidebarToggle"
                        class="btn btn-sm sidebar-toggle-btn"
                        title="Collapse sidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="logo-wrapper">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/E&B_Logo.png') }}" alt="Logo">
                </a>
            </div>
            <hr>

            <ul class="nav nav-pills flex-column mb-auto">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       id="nav-dashboard">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('customers') }}"
                       class="nav-link {{ request()->routeIs('customers') ? 'active' : '' }}"
                       id="nav-customers">
                        <i class="bi bi-people-fill me-2"></i>
                        <span>Customers</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.delivery.calendar') }}"
                       class="nav-link {{ request()->routeIs('admin.delivery.calendar') ? 'active' : '' }}"
                       id="nav-delivery-calendar">
                        <i class="bi bi-calendar me-2"></i>
                        <span>Delivery Calendar</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.sales.report') }}"
                       class="nav-link {{ request()->routeIs('admin.sales.report') ? 'active' : '' }}"
                       id="nav-sales-report">
                        <i class="bi bi-clipboard-data me-2"></i>
                        <span>Sales Report</span>
                    </a>
                </li>

                <hr>

                <li>
                    <a href="{{ route('users') }}"
                       class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}"
                       id="nav-users">
                        <i class="bi bi-person-gear me-2"></i>
                        <span>Users</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('services') }}"
                       class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}"
                       id="nav-services">
                        <i class="bi bi-basket2 me-2"></i>
                        <span>Services</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.feedback.index') }}"
                       class="nav-link {{ request()->routeIs('admin.feedback.index') ? 'active' : '' }}"
                       id="nav-feedbacks"
                       aria-current="page">
                        <i class="bi bi-chat-dots me-2"></i>
                        <span>Feedbacks</span>
                    </a>
                </li>
            </ul>

            <hr>

            <div class="dropdown text-end">
                <button id="btn-admin"
                        class="btn dropdown-toggle d-flex align-items-center"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                         class="bi bi-person-circle me-2" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd"
                              d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                    <span class="fw-bold">Admin</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item fw-bold" id="admin-settings"
                        href="{{ route('admin.dashboard.editAdminProfileInfo') }}">
                        <i class="bi bi-person-circle me-2"></i>
                        Profile
                    </a>

                    <li class="">
                        <div class="d-flex align-items-center justify-content-between">
                            <form action="{{ route('logout') }}" method="POST" class="flex-grow-1 mb-0">
                                @csrf
                                <button type="submit" id="btn-logout" class="dropdown-item fw-bold w-100 text-start">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content p-4">
            <h1 class="page-title">Feedbacks</h1>
            <hr>

            <div class="container-fluid">
                <div class="card feedback-card">
                    <div class="card-header feedback-header" id="card-header">
                        <div class="feedback-toolbar">
                            <div>
                                <h2 class="feedback-heading mb-0">All Feedback</h2>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-wrap">
                            <table class="table table-sm table-borderless align-middle feedback-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Rating</th>
                                        <th>Message</th>
                                        <th class="text-center">Visible</th>
                                        <th class="text-center actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @forelse($feedback as $fb)
                                      @php
                                          $modalId = 'fbModal-'.$fb->id;
                                          $hideModalId = 'hideFeedbackModal-'.$fb->id;
                                          $unhideModalId = 'unhideFeedbackModal-'.$fb->id;
                                          $name = $fb->name ?? ($fb->user->name ?? 'Customer');
                                      @endphp

                                      <tr>
                                          <td>
                                              <div class="date-main">
                                                  {{ $fb->created_at->timezone(config('app.timezone'))->format('M d, Y') }}
                                              </div>
                                              <div class="date-sub">
                                                  {{ $fb->created_at->timezone(config('app.timezone'))->format('h:i A') }}
                                              </div>
                                          </td>

                                          <td class="feedback-name">
                                              {{ $name }}
                                          </td>

                                          <td class="text-center">
                                              <span class="fb-stars" aria-label="Rating {{ (int)$fb->rating }} of 5">
                                                  @for($i = 1; $i <= 5; $i++)
                                                      @if($i <= (int)$fb->rating)
                                                          ★
                                                      @else
                                                          ☆
                                                      @endif
                                                  @endfor
                                              </span>
                                          </td>

                                          <td class="text-center">
                                              <button type="button"
                                                      class="btn btn-sm btn-view-feedback"
                                                      data-bs-toggle="modal"
                                                      data-bs-target="#{{ $modalId }}">
                                                  View
                                              </button>

                                              <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" aria-labelledby="{{ $modalId }}Label">
                                                  <div class="modal-dialog modal-dialog-centered">
                                                      <div class="modal-content feedback-modal-content">
                                                          <div class="modal-header feedback-modal-header">
                                                              <h5 class="modal-title" id="{{ $modalId }}Label">
                                                                  {{ $name }}'s Feedback
                                                              </h5>
                                                              <button type="button"
                                                                      class="btn-close btn-close-white"
                                                                      data-bs-dismiss="modal"
                                                                      aria-label="Close"></button>
                                                          </div>
                                                          <div class="modal-body text-start">
                                                              <div class="feedback-modal-meta mb-3">
                                                                  <div><strong>Date:</strong> {{ $fb->created_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</div>
                                                                  <div><strong>Rating:</strong>
                                                                      <span class="fb-stars">
                                                                          @for($i = 1; $i <= 5; $i++)
                                                                              @if($i <= (int)$fb->rating)
                                                                                  ★
                                                                              @else
                                                                                  ☆
                                                                              @endif
                                                                          @endfor
                                                                      </span>
                                                                  </div>
                                                              </div>
                                                              <div class="feedback-message text-prewrap">{{ $fb->message }}</div>
                                                          </div>
                                                          <div class="modal-footer border-0 pt-0">
                                                              <button type="button" class="btn modal-no-btn" data-bs-dismiss="modal">Close</button>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </td>

                                          <td class="text-center">
                                              @if($fb->is_visible)
                                                  <span class="badge bg-success feedback-badge">Yes</span>
                                              @else
                                                  <span class="badge bg-secondary feedback-badge">No</span>
                                              @endif
                                          </td>

                                          <td class="actions text-center">
                                              @if($fb->is_visible)
                                                  <button type="button"
                                                          class="feedback-hide-btn"
                                                          data-bs-toggle="modal"
                                                          data-bs-target="#{{ $hideModalId }}">
                                                      Hide
                                                  </button>

                                                  <div class="modal fade" id="{{ $hideModalId }}" tabindex="-1" aria-hidden="true">
                                                      <div class="modal-dialog modal-dialog-centered">
                                                          <div class="modal-content action-confirm-modal">
                                                              <div class="modal-header action-confirm-header">
                                                                  <h5 class="modal-title">Hide Feedback</h5>
                                                                  <button type="button"
                                                                          class="btn-close btn-close-white"
                                                                          data-bs-dismiss="modal"
                                                                          aria-label="Close"></button>
                                                              </div>
                                                              <div class="modal-body">
                                                                  Are you sure you want to hide this feedback from <strong>{{ $name }}</strong>?
                                                              </div>
                                                              <div class="modal-footer">
                                                                  <button type="button" class="btn modal-no-btn" data-bs-dismiss="modal">No</button>
                                                                  <a href="{{ route('admin.feedback.hide', $fb) }}" class="btn btn-danger modal-yes-btn">
                                                                      Yes, Hide
                                                                  </a>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              @else
                                                  <button type="button"
                                                          class="btn btn-sm btn-action btn-action-primary"
                                                          data-bs-toggle="modal"
                                                          data-bs-target="#{{ $unhideModalId }}">
                                                      Unhide
                                                  </button>

                                                  <div class="modal fade" id="{{ $unhideModalId }}" tabindex="-1" aria-hidden="true">
                                                      <div class="modal-dialog modal-dialog-centered">
                                                          <div class="modal-content action-confirm-modal">
                                                              <div class="modal-header action-confirm-header">
                                                                  <h5 class="modal-title">Unhide Feedback</h5>
                                                                  <button type="button"
                                                                          class="btn-close btn-close-white"
                                                                          data-bs-dismiss="modal"
                                                                          aria-label="Close"></button>
                                                              </div>
                                                              <div class="modal-body">
                                                                  Are you sure you want to unhide this feedback from <strong>{{ $name }}</strong>?
                                                              </div>
                                                              <div class="modal-footer">
                                                                  <button type="button" class="btn modal-no-btn" data-bs-dismiss="modal">No</button>
                                                                  <a href="{{ route('admin.feedback.unhide', $fb) }}" class="btn btn-success modal-yes-btn">
                                                                      Yes, Unhide
                                                                  </a>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              @endif
                                          </td>
                                      </tr>
                                  @empty
                                      <tr>
                                          <td colspan="6" class="text-center text-muted py-4">No feedback found.</td>
                                      </tr>
                                  @endforelse
                                  </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                                {{ $feedback->links() }}
                            </div>
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
            const KEY  = 'eb_admin_sidebar_collapsed';

            try {
                if (localStorage.getItem(KEY) === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}

            btn.addEventListener('click', function (e) {
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileBtn = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (mobileBtn) {
                mobileBtn.addEventListener('click', function() {
                    document.body.classList.add('sidebar-mobile-open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            }

            document.querySelectorAll('#side-bar .nav-link, #side-bar .dropdown-item').forEach(el => {
                el.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-mobile-open');
                });
            });
        });
    </script>
</body>
</html>