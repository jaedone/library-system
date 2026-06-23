    @extends('common.main')

    @section('title', 'Services Management | PUP Library')

    @section('content')
    <section class="admin-page admin-services-management-page">

        <div class="admin-page-header">
            <div>
                <span class="admin-eyebrow">Admin Dashboard</span>
                <h1>Services Management</h1>
                <p>Review, approve, reject, and process library service requests.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="admin-success-alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="admin-error-alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-error-alert">
                <strong>Please check the form.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.services-management.index') }}" class="admin-filter-card">
            <div class="admin-form-grid">
                <div class="admin-field">
                    <label for="search">Search Request</label>
                    <input
                        type="search"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Requester, email, service, book, facility..."
                    >
                </div>
                <div class="admin-field">
                    <label for="service">Service</label>
                    <select id="service" name="service">
                        <option value="">All services</option>
                        @foreach ($serviceLabels as $key => $label)
                            <option value="{{ $key }}" @selected($serviceFilter === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="admin-field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All statuses</option>
                        <option value="pending"  @selected($statusFilter === 'pending')>Pending</option>
                        <option value="approved" @selected($statusFilter === 'approved')>Approved</option>
                        <option value="rejected" @selected($statusFilter === 'rejected')>Rejected</option>
                        <option value="borrowed" @selected($statusFilter === 'borrowed')>Borrowed</option>
                        <option value="returned" @selected($statusFilter === 'returned')>Returned</option>
                        <option value="overdue"  @selected($statusFilter === 'overdue')>Overdue</option>
                    </select>
                </div>
            </div>
            <div class="admin-filter-actions">
                <button type="submit" class="admin-primary-btn">
                    <i class="bi bi-search"></i> Apply Filters
                </button>
                <a href="{{ route('admin.services-management.index') }}" class="admin-secondary-btn">
                    Clear
                </a>
            </div>
        </form>

        <div class="admin-push-layout" data-admin-push-layout>

            <main class="admin-push-main">

                <div class="admin-services-summary-grid">
                    <article class="admin-services-summary-card">
                        <i class="bi bi-inboxes"></i>
                        <div>
                            <span>Total Requests</span>
                            <strong>{{ $requests->count() }}</strong>
                        </div>
                    </article>
                    <article class="admin-services-summary-card">
                        <i class="bi bi-hourglass-split"></i>
                        <div>
                            <span>Pending</span>
                            <strong>{{ $requests->where('status_key', 'pending')->count() }}</strong>
                        </div>
                    </article>
                    <article class="admin-services-summary-card">
                        <i class="bi bi-check-circle"></i>
                        <div>
                            <span>Approved</span>
                            <strong>
                                {{
                                    $requests->where('status_key', 'approved')->count()
                                    + $requests->where('status_key', 'borrowed')->count()
                                }}
                            </strong>
                        </div>
                    </article>
                    <article class="admin-services-summary-card">
                        <i class="bi bi-x-circle"></i>
                        <div>
                            <span>Rejected</span>
                            <strong>{{ $requests->where('status_key', 'rejected')->count() }}</strong>
                        </div>
                    </article>
                </div>

                <div class="admin-table-card">
                    <table class="admin-table admin-services-table">
                        <thead>
                            <tr>
                                <th>Request</th>
                                <th>Service</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $requestItem)
                                <tr data-service-row="{{ $requestItem->service_key }}-{{ $requestItem->request_id }}">
                                    <td>
                                        <div class="admin-table-title">
                                            <i class="bi {{ match ($requestItem->service_key) {
                                                'book-borrowing'       => 'bi-book',
                                                'book-reservation'     => 'bi-bookmark-check',
                                                'facility-reservation' => 'bi-building-check',
                                                'book-renewal'         => 'bi-arrow-repeat',
                                                'book-return'          => 'bi-box-arrow-in-left',
                                                'referral-letter'      => 'bi-file-earmark-text',
                                                default                => 'bi-inbox',
                                            } }}"></i>
                                            <div>
                                                <strong>{{ $requestItem->title ?? 'Untitled Request' }}</strong>
                                                <span>{{ $requestItem->subtitle ?? 'No additional details' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $requestItem->service_label }}</td>
                                    <td>
                                        <strong>{{ $requestItem->requester_name }}</strong>
                                        <br>
                                        <span>{{ $requestItem->requester_email }}</span>
                                    </td>
                                    <td>
                                        <span class="admin-status {{ in_array($requestItem->status_key, ['approved', 'borrowed', 'returned'], true) ? 'active' : 'inactive' }}">
                                            {{ $requestItem->status_name }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $requestItem->requested_at
                                            ? \Carbon\Carbon::parse($requestItem->requested_at)->format('M d, Y')
                                            : 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="admin-table-actions">
                                            <button
                                                type="button"
                                                data-service-action="view"
                                                data-service-key="{{ $requestItem->service_key }}"
                                                data-request-id="{{ $requestItem->request_id }}"
                                            >
                                                View
                                            </button>
                                            <button
                                                type="button"
                                                data-service-action="review"
                                                data-service-key="{{ $requestItem->service_key }}"
                                                data-request-id="{{ $requestItem->request_id }}"
                                            >
                                                Review
                                            </button>

                                            @if ($requestItem->service_key === 'book-return')
                                                <button
                                                    type="button"
                                                    data-service-action="confirm-return"
                                                    data-service-key="{{ $requestItem->service_key }}"
                                                    data-request-id="{{ $requestItem->request_id }}"
                                                >
                                                    Confirm
                                                </button>
                                                <button
                                                    type="button"
                                                    data-service-action="follow-up"
                                                    data-service-key="{{ $requestItem->service_key }}"
                                                    data-request-id="{{ $requestItem->request_id }}"
                                                >
                                                    Follow-up
                                                </button>

                                            @elseif ($requestItem->service_key === 'referral-letter')
                                                <button
                                                    type="button"
                                                    data-service-action="send-letter"
                                                    data-service-key="{{ $requestItem->service_key }}"
                                                    data-request-id="{{ $requestItem->request_id }}"
                                                >
                                                    Send Letter
                                                </button>

                                            @else
                                                <button
                                                    type="button"
                                                    data-service-action="approve"
                                                    data-service-key="{{ $requestItem->service_key }}"
                                                    data-request-id="{{ $requestItem->request_id }}"
                                                >
                                                    Approve
                                                </button>
                                            @endif

                                            <button
                                                type="button"
                                                data-service-action="reject"
                                                data-service-key="{{ $requestItem->service_key }}"
                                                data-request-id="{{ $requestItem->request_id }}"
                                            >
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No service requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </main>

            {{-- REUSABLE PUSH PANEL --}}
            <aside class="admin-push-panel" data-admin-push-panel aria-hidden="true">
                <div class="admin-push-panel-inner">

                    <div class="admin-push-panel-top">
                        <div>
                            <span class="admin-eyebrow" data-panel-eyebrow>Services Management</span>
                            <h2 data-panel-title>Select a request</h2>
                            <p data-panel-subtitle>Review request details and process the user's service request.</p>
                        </div>
                        <button type="button" class="admin-push-panel-close" data-panel-close>
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="admin-push-panel-body">

                        <div data-panel-alert></div>

                        {{-- VIEW --}}
                        <section class="admin-push-panel-section" data-panel-section="view">
                            <div data-service-view-output></div>
                        </section>

                        {{-- DECISION FORM --}}
                        <section class="admin-push-panel-section" data-panel-section="decision" hidden>
                            <form method="POST" action="#" data-service-decision-form>
                                @csrf
                                @method('PATCH')
                                <div class="admin-delete-warning">
                                    <i class="bi bi-clipboard-check" data-decision-icon></i>
                                    <h3 data-decision-title>Process request</h3>
                                    <p data-decision-message>Add a short note for the user.</p>
                                </div>
                                <div class="admin-field">
                                    <label>Remarks / Message to User</label>
                                    <textarea
                                        name="remarks"
                                        rows="5"
                                        data-decision-remarks
                                        placeholder="Write your message to the user..."
                                    ></textarea>
                                </div>
                                <button type="submit" class="admin-primary-btn" data-decision-submit>
                                    Submit Decision
                                </button>
                            </form>
                        </section>

                        {{-- PENALTY FORM --}}
                        <section class="admin-push-panel-section" data-panel-section="penalty" hidden>
                            <form method="POST" action="#" data-penalty-form>
                                @csrf
                                <div class="admin-delete-warning">
                                    <i class="bi bi-cash-coin"></i>
                                    <h3>Add Manual Penalty</h3>
                                    <p>This penalty will be sent to the user notification inbox and reflected in their account standing.</p>
                                </div>
                                <div class="admin-field">
                                    <label>Penalty Type</label>
                                    <select name="penalty_type_id" required>
                                        <option value="">Select penalty type</option>
                                        @foreach ($penaltyTypes as $penaltyType)
                                            <option value="{{ $penaltyType->id }}">
                                                {{ $penaltyType->penalty_type_name }}
                                                @if (!empty($penaltyType->default_amount))
                                                    — ₱{{ number_format($penaltyType->default_amount, 2) }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-grid two">
                                    <div class="admin-field">
                                        <label>Amount</label>
                                        <input type="number" name="amount" min="0" step="0.01" required>
                                    </div>
                                    <div class="admin-field">
                                        <label>Credit Score Deduction</label>
                                        <input type="number" name="credit_points" min="0" max="100" value="10">
                                    </div>
                                </div>
                                <div class="admin-field">
                                    <label>Description / Reason</label>
                                    <textarea
                                        name="description"
                                        rows="5"
                                        required
                                        placeholder="Explain why this penalty is added."
                                    ></textarea>
                                </div>
                                <button type="submit" class="admin-danger-btn">
                                    <i class="bi bi-cash-coin"></i> Add Penalty
                                </button>
                            </form>
                        </section>

                        {{-- REFERRAL LETTER FORM --}}
                        <section class="admin-push-panel-section" data-panel-section="letter" hidden>
                            <form method="POST" action="#" enctype="multipart/form-data" data-letter-form>
                                @csrf
                                <div class="admin-delete-warning">
                                    <i class="bi bi-file-earmark-arrow-up"></i>
                                    <h3>Send Referral Letter Update</h3>
                                    <p>Upload the approved letter if available, then send the update to the user notification inbox.</p>
                                </div>
                                <div class="admin-field">
                                    <label>Approved Letter File</label>
                                    <input
                                        type="file"
                                        name="approved_letter"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    >
                                </div>
                                <div class="admin-field">
                                    <label>Message to User</label>
                                    <textarea
                                        name="remarks"
                                        rows="5"
                                        required
                                        placeholder="Example: Your referral letter request has been processed. Please check the attached file or visit the library office."
                                    ></textarea>
                                </div>
                                <button type="submit" class="admin-primary-btn">
                                    <i class="bi bi-send"></i> Send Letter Update
                                </button>
                            </form>
                        </section>

                    </div>

                    <div class="admin-push-panel-footer" data-panel-footer hidden>
                        <button type="button" class="admin-secondary-btn" data-footer-action="penalty">
                            <i class="bi bi-cash-coin"></i> Add Penalty
                        </button>
                    </div>

                </div>
            </aside>

        </div>

    </section>

    <script type="application/json" data-service-management-data>
        {!! json_encode($panelRequests, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
    @endsection