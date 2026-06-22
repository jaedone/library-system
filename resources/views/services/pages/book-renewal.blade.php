<section class="service-workspace">

    <div class="service-rules-grid">
        <article class="service-rule-card">
            <h2>Renewal Conditions</h2>

            <ul>
                <li><i class="bi bi-check2-circle"></i> The material must be renewable.</li>
                <li><i class="bi bi-check2-circle"></i> The material must not be reserved by another user.</li>
                <li><i class="bi bi-check2-circle"></i> The borrower must have no pending penalties.</li>
                <li><i class="bi bi-check2-circle"></i> Renewal is connected to an active borrowing record.</li>
            </ul>
        </article>

        <article class="service-rule-card warning">
            <h2>Important Reminder</h2>

            <ul>
                <li><i class="bi bi-info-circle"></i> Renewal requests may be rejected if the book is overdue.</li>
                <li><i class="bi bi-info-circle"></i> Renewal approval depends on library policy and material availability.</li>
                <li><i class="bi bi-info-circle"></i> Requested renewal date must be later than today.</li>
            </ul>
        </article>
    </div>

    <section class="service-form-panel">
        <div class="service-form-header">
            <span class="service-detail-eyebrow">Renew Book</span>
            <h2>Book Renewal Form</h2>
            <p>Request an extension for an eligible borrowed material.</p>
        </div>

        <form class="service-form" method="POST" action="{{ route('services.store', $serviceKey) }}">
            @csrf

            <div class="service-form-grid">
                <div class="service-field service-field-full">
                    <label for="borrow_transaction_id">Borrowed Book</label>

                    <select id="borrow_transaction_id" name="borrow_transaction_id" required>
                        <option value="">Select borrowed book</option>

                        @forelse ($activeBorrowTransactions ?? [] as $transaction)
                            <option value="{{ $transaction->id }}">
                                {{ $transaction->title }}
                                — Due: {{ \Carbon\Carbon::parse($transaction->due_at)->format('M d, Y') }}
                                — Status: {{ $transaction->status_name }}
                            </option>
                        @empty
                            <option value="" disabled>No active borrowed books found</option>
                        @endforelse
                    </select>
                </div>

                <div class="service-field">
                    <label for="requested_renewal_date">Requested New Due Date</label>

                    <input
                        type="date"
                        id="requested_renewal_date"
                        name="requested_renewal_date"
                        required
                    >
                </div>

                <div class="service-field service-field-full">
                    <label for="renewal_reason">Reason for Renewal</label>

                    <textarea
                        id="renewal_reason"
                        name="renewal_reason"
                        rows="4"
                        placeholder="Explain why you need to renew the material"
                        required
                    ></textarea>
                </div>
            </div>

            <button type="submit" class="service-submit-btn">
                <i class="bi bi-arrow-repeat"></i>
                Submit Renewal Request
            </button>
        </form>
    </section>

</section>