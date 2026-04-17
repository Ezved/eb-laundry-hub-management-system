@php
    $isBlockedFn = fn($u) => ($u->status ?? 'active') === 'blocked';
@endphp

@forelse ($all_users as $user)
    @php
        $isSelf      = ($authId ?? null) === $user->id;
        $isLastAdmin = $user->role === 'admin' && ($adminCount ?? 0) <= 1 && !$user->is_hidden;
        $canToggle   = ! $isSelf && ! $isLastAdmin;
        $isBlocked   = $isBlockedFn($user);
    @endphp

    <tr class="{{ $user->is_hidden ? 'table-secondary' : '' }}">
        <td>
            {{ $user->name }}
            @if($user->is_hidden)
                <span class="badge bg-dark ms-1">Hidden</span>
            @endif
            @if($isBlocked)
                <span class="badge bg-warning text-dark ms-1">Blocked</span>
            @endif
        </td>

        <td>{{ $user->email }}</td>

        <td>{{ $user->phone_number ?? '—' }}</td>

        <td>{{ optional($user->created_at)->format('F d, Y') }}</td>

        <td>{{ optional($user->updated_at)->format('F d, Y') }}</td>

        <td>
            @php
                $role = $user->role ? ucfirst($user->role) : '—';
            @endphp
            <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                {{ $role }}
            </span>
        </td>

        <td class="actions text-center">
            <div class="dropdown action-dropdown">
                <button class="btn btn-light btn-sm eandb-kebab"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="More actions">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end eandb-menu shadow-sm">
                    <li>
                        <a class="dropdown-item action-item"
                           href="/edit/{{ $user->id }}?from=users">
                            Edit
                        </a>
                    </li>

                    @if(!$user->is_hidden)
                        <li>
                            <button type="button"
                                    class="dropdown-item action-item"
                                    data-bs-toggle="modal"
                                    data-bs-target="#hideUserModal-{{ $user->id }}">
                                Hide
                            </button>
                        </li>
                    @else
                        <li>
                            <button type="button"
                                    class="dropdown-item action-item"
                                    data-bs-toggle="modal"
                                    data-bs-target="#unhideUserModal-{{ $user->id }}">
                                Unhide
                            </button>
                        </li>
                    @endif

                    @if ($canToggle)
                        @if ($isBlocked)
                            <li>
                                <button type="button"
                                        class="dropdown-item action-item"
                                        data-bs-toggle="modal"
                                        data-bs-target="#unblockUserModal-{{ $user->id }}">
                                    Unblock
                                </button>
                            </li>
                        @else
                            <li>
                                <button type="button"
                                        class="dropdown-item action-item action-item-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#blockUserModal-{{ $user->id }}">
                                    Block
                                </button>
                            </li>
                        @endif
                    @else
                        <li>
                            <button class="dropdown-item action-item" type="button" disabled>
                                {{ $isSelf ? 'Admin' : 'Protected' }}
                            </button>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Hide Modal --}}
            <div class="modal fade" id="hideUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content action-confirm-modal">
                        <div class="modal-header action-confirm-header">
                            <h5 class="modal-title">Hide User</h5>
                            <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to hide <strong>{{ $user->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn modal-no-btn" data-bs-dismiss="modal">No</button>
                            <a href="{{ route('users.hide', $user->id) }}" class="btn btn-danger modal-yes-btn">
                                Yes, Hide
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unhide Modal --}}
            <div class="modal fade" id="unhideUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content action-confirm-modal">
                        <div class="modal-header action-confirm-header">
                            <h5 class="modal-title">Unhide User</h5>
                            <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to unhide <strong>{{ $user->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn modal-no-btn" data-bs-dismiss="modal">No</button>
                            <a href="{{ route('users.unhide', $user->id) }}" class="btn btn-success modal-yes-btn">
                                Yes, Unhide
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Block Modal --}}
            <div class="modal fade" id="blockUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content action-confirm-modal">
                        <div class="modal-header action-confirm-header">
                            <h5 class="modal-title">Block User</h5>
                            <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to block <strong>{{ $user->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn modal-no-btn" data-bs-dismiss="modal">No</button>
                            <a href="{{ route('users.toggleBlock', $user->id) }}" class="btn btn-danger modal-yes-btn">
                                Yes, Block
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unblock Modal --}}
            <div class="modal fade" id="unblockUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content action-confirm-modal">
                        <div class="modal-header action-confirm-header">
                            <h5 class="modal-title">Unblock User</h5>
                            <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to unblock <strong>{{ $user->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn modal-no-btn" data-bs-dismiss="modal">No</button>
                            <a href="{{ route('users.toggleBlock', $user->id) }}" class="btn btn-success modal-yes-btn">
                                Yes, Unblock
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">No users found.</td>
    </tr>
@endforelse