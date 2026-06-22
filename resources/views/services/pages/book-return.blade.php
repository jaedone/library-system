<section class="service-workspace">

    <div class="service-rules-grid">
        <article class="service-rule-card">
            <h2>Return Policy</h2>

            <ul>
                <li><i class="bi bi-check2-circle"></i> Materials should be returned on or before the due date.</li>
                <li><i class="bi bi-check2-circle"></i> Returned materials will be checked by library staff.</li>
                <li><i class="bi bi-check2-circle"></i> The return record is connected to the borrowing transaction.</li>
                <li><i class="bi bi-check2-circle"></i> Proof of return is required for verification.</li>
            </ul>
        </article>

        <article class="service-rule-card warning">
            <h2>Penalties</h2>

            <ul>
                <li><i class="bi bi-exclamation-circle"></i> Overdue materials may be subject to penalties.</li>
                <li><i class="bi bi-exclamation-circle"></i> Damaged or lost materials may require replacement or payment.</li>
                <li><i class="bi bi-exclamation-circle"></i> Unsettled penalties may block future borrowing.</li>
            </ul>
        </article>
    </div>

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
                    <label for="return_date">Return Date</label>

                    <input
                        type="date"
                        id="return_date"
                        name="return_date"
                        required
                    >
                </div>

                <div class="service-field">
                    <label for="material_condition">Material Condition</label>

                    <select id="material_condition" name="material_condition" required>
                        <option value="">Select condition</option>
                        <option value="good">Good Condition</option>
                        <option value="minor_damage">Minor Damage</option>
                        <option value="damaged">Damaged</option>
                        <option value="lost">Lost</option>
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