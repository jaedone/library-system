<section class="service-workspace">
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

                    <select id="borrow_transaction_id" name="borrow_transaction_id" class="form-select renewal-book-select" required>
    <option value="">Select borrowed book</option>

    @forelse ($activeBorrowTransactions ?? [] as $transaction)
        <option
            value="{{ $transaction->id }}"
            @selected(old('borrow_transaction_id') == $transaction->id)
        >
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

                    <div class="input-group">
    <span class="input-group-text">
        <i class="bi bi-calendar-check"></i>
    </span>

    <input
        type="text"
        id="requested_renewal_date"
        name="requested_renewal_date"
        class="form-control service-date-picker"
        value="{{ old('requested_renewal_date') }}"
        placeholder="Select new due date"
        readonly
        required
    >
</div>
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