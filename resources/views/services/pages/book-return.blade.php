<section class="service-workspace">
    <section class="service-form-panel">
        <div class="service-form-header">
            <span class="service-detail-eyebrow">Return Book</span>
            <h2>Book Return Form</h2>
            <p>
                Submit return information for a borrowed material.
                Upload proof of return for staff verification.
            </p>
        </div>

        <form
            class="service-form"
            method="POST"
            action="{{ route('services.store', $serviceKey) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="service-form-grid">
                <div class="service-field service-field-full">
                    <label for="borrow_transaction_id">Borrowed Book</label>

                    <select id="return_borrow_transaction_id" name="borrow_transaction_id" class="form-select return-book-select" required>
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
                    <label for="return_date">Return Date</label>

                    <div class="input-group">
    <span class="input-group-text">
        <i class="bi bi-calendar-check"></i>
    </span>

    <input
        type="text"
        id="return_date"
        name="return_date"
        class="form-control service-date-picker"
        value="{{ old('return_date') }}"
        placeholder="Select return date"
        readonly
        required
    >
</div>
                </div>

                <div class="service-field">
                    <label for="material_condition">Material Condition</label>

                    <select id="material_condition" name="material_condition" class="form-select return-condition-select" required>
    <option value="">Select condition</option>
    <option value="good" @selected(old('material_condition') === 'good')>Good Condition</option>
    <option value="minor_damage" @selected(old('material_condition') === 'minor_damage')>Minor Damage</option>
    <option value="damaged" @selected(old('material_condition') === 'damaged')>Damaged</option>
    <option value="lost" @selected(old('material_condition') === 'lost')>Lost</option>
</select>
                </div>

                <div class="service-field">
                    <label for="proof_of_return">Proof of Return</label>

                    <input
                        type="file"
                        id="proof_of_return"
                        name="proof_of_return"
                        accept=".jpg,.jpeg,.png,.pdf"
                        required
                    >
                </div>

                <div class="service-field service-field-full">
                    <label for="return_notes">Return Notes</label>

                    <textarea
                        id="return_notes"
                        name="return_notes"
                        rows="4"
                        placeholder="Add notes about the returned material"
                    ></textarea>
                </div>
            </div>

            <button type="submit" class="service-submit-btn">
                <i class="bi bi-box-arrow-in-left"></i>
                Submit Return Information
            </button>
        </form>
    </section>

</section>