@forelse ($customers as $customer)
    @php
        $isHidden = (bool) ($customer->is_hidden ?? false);

        $userModel = $customer->user ?? null;
        $hasUser = !is_null($userModel);
        $userId = $hasUser ? $userModel->id : null;

        $categoryLabel = $hasUser ? 'User' : 'Walk-in';

        $latestPickupAddr = optional($customer->latestOrder ?? null)->pickup_address;
        $latestPickupLoc = optional($customer->latestOrder ?? null)->pickup_location_details;

        $custName =
            $customer->name ??
            optional($userModel)->name ??
            '—';

        $custPhone =
            $customer->phone_number ??
            optional($userModel)->phone_number ??
            data_get($customer, 'meta.phone_number') ??
            '—';

        $custEmail =
            $customer->email ??
            data_get($customer, 'meta.email') ??
            '—';

        $userEmail = optional($userModel)->email;

        $custAddress =
            $customer->address ??
            optional($userModel)->address ??
            $latestPickupAddr ??
            data_get($customer, 'meta.address') ??
            '—';

        $custLocationDetails =
            optional($userModel)->location_details ??
            $latestPickupLoc ??
            data_get($customer, 'meta.location_details') ??
            '—';
    @endphp

    <tr class="{{ $isHidden ? 'table-secondary' : '' }}"
        data-category="{{ $hasUser ? 'user' : 'walkin' }}">

        {{-- Category --}}
        <td>
            <span class="badge rounded-pill {{ $hasUser ? 'text-bg-primary' : 'text-bg-secondary' }}">
                {{ $categoryLabel }}
            </span>
        </td>

        {{-- Full Name --}}
        <td class="text-start fw-semibold">{{ $custName }}</td>

        {{-- Information --}}
        <td class="text-center">
            <button type="button"
                class="btn btn-outline-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#customerInfoModal-{{ $customer->id }}">
                View
            </button>

            <div class="modal fade"
                id="customerInfoModal-{{ $customer->id }}"
                tabindex="-1"
                aria-labelledby="customerInfoModalLabel-{{ $customer->id }}"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header" style="background:#173F7B;color:#fff;">
                            <h5 class="modal-title" id="customerInfoModalLabel-{{ $customer->id }}">
                                Customer Information
                            </h5>
                            <button type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-start">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Name</dt>
                                <dd class="col-sm-8">{{ $custName }}</dd>

                                <dt class="col-sm-4">Email</dt>
                                <dd class="col-sm-8">{{ $custEmail }}</dd>

                                @if ($hasUser && $userEmail && $userEmail !== $custEmail)
                                    <dt class="col-sm-4">User Email</dt>
                                    <dd class="col-sm-8">{{ $userEmail }}</dd>
                                @endif

                                <dt class="col-sm-4">Phone</dt>
                                <dd class="col-sm-8">{{ $custPhone }}</dd>

                                <dt class="col-sm-4">Address</dt>
                                <dd class="col-sm-8">{{ $custAddress }}</dd>

                                <dt class="col-sm-4">Location Details</dt>
                                <dd class="col-sm-8">{{ $custLocationDetails }}</dd>
                            </dl>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </td>

        {{-- Order History --}}
        <td class="text-center">
            <a href="{{ route('customers.orders', $customer->id) }}"
                class="btn btn-outline-primary btn-sm">
                View
            </a>
        </td>

        {{-- Loyalty Membership --}}
        <td class="text-center">
            <a href="{{ route('customers.loyalty', $customer->id) }}"
                class="btn btn-outline-primary btn-sm">
                View
            </a>
        </td>

        {{-- Account Exist --}}
        <td class="text-center align-middle">
            <span class="fw-semibold {{ $hasUser ? 'text-success' : 'text-muted' }}">
                {{ $hasUser ? 'Yes' : 'No' }}
            </span>
        </td>

        {{-- Actions --}}
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
                            href="{{ route('admin.customers.from_customer', $customer->id) }}">
                            New Order
                        </a>
                    </li>

                    <li>
                        @if ($hasUser)
                            <a class="dropdown-item action-item"
                                href="/edit/{{ $userId ?? $customer->id }}?from=customers">
                                Edit
                            </a>
                        @else
                            <a class="dropdown-item action-item"
                                href="{{ route('customers.edit', $customer->id) }}">
                                Edit
                            </a>
                        @endif
                    </li>
                    @if (!$isHidden)
                        <li>
                            <button type="button"
                                class="dropdown-item action-item action-item-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#hideCustomerModal-{{ $customer->id }}">
                                Hide
                            </button>
                        </li>
                    @else
                        <li>
                            <button type="button"
                                class="dropdown-item action-item action-item-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#unhideCustomerModal-{{ $customer->id }}">
                                Unhide
                            </button>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Hide Modal --}}
            <div class="modal fade"
                id="hideCustomerModal-{{ $customer->id }}"
                tabindex="-1"
                aria-labelledby="hideCustomerModalLabel-{{ $customer->id }}"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content action-confirm-modal">
                        <div class="modal-header action-confirm-header">
                            <h5 class="modal-title" id="hideCustomerModalLabel-{{ $customer->id }}">
                                Hide Customer
                            </h5>
                            <button type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-start">
                            Are you sure you want to hide
                            <strong>{{ $custName }}</strong>?
                            <br>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light btn-sm modal-no-btn" data-bs-dismiss="modal">
                                No
                            </button>
                            <a href="{{ route('customers.hide', ['customer' => $customer->id]) }}"
                                class="btn btn-danger btn-sm modal-yes-btn">
                                Yes, hide
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unhide Modal --}}
            <div class="modal fade"
                id="unhideCustomerModal-{{ $customer->id }}"
                tabindex="-1"
                aria-labelledby="unhideCustomerModalLabel-{{ $customer->id }}"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content action-confirm-modal">
                        <div class="modal-header action-confirm-header">
                            <h5 class="modal-title" id="unhideCustomerModalLabel-{{ $customer->id }}">
                                Unhide Customer
                            </h5>
                            <button type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-start">
                            Are you sure you want to unhide
                            <strong>{{ $custName }}</strong>?
                            <br>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light btn-sm modal-no-btn" data-bs-dismiss="modal">
                                No
                            </button>
                            <a href="{{ route('customers.unhide', ['customer' => $customer->id]) }}"
                                class="btn btn-success btn-sm modal-yes-btn">
                                Yes, unhide
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">
            No customers found.
        </td>
    </tr>
@endforelse