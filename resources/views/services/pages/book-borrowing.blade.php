<section class="service-workspace">

    <div class="service-rules-grid">
        <article class="service-rule-card">
            <h2>Borrowing Rules</h2>

            <ul>
                <li><i class="bi bi-check2-circle"></i> Borrowing is allowed only for approved library accounts.</li>
                <li><i class="bi bi-check2-circle"></i> The borrower must present a valid PUP ID, CoR, or Employee ID when claiming materials.</li>
                <li><i class="bi bi-check2-circle"></i> Borrowed materials must be returned on or before the due date.</li>
                <li><i class="bi bi-check2-circle"></i> Materials reserved by another user may not be renewed.</li>
            </ul>
        </article>

        <article class="service-rule-card">
            <h2>Borrowing Days Allowed</h2>

            <ul>
                <li><i class="bi bi-calendar-check"></i> Fiction Books — 7 days</li>
                <li><i class="bi bi-calendar-check"></i> Graduate School Materials — 2 days</li>
                <li><i class="bi bi-calendar-check"></i> College of Law Materials — 2 days</li>
                <li><i class="bi bi-calendar-check"></i> Faculty and Employees — up to 7 days, subject to availability</li>
            </ul>
        </article>

        <article class="service-rule-card warning">
            <h2>Penalties and Restrictions</h2>

            <ul>
                <li><i class="bi bi-exclamation-circle"></i> Overdue books may result in penalties.</li>
                <li><i class="bi bi-exclamation-circle"></i> Users with unpaid penalties cannot borrow new materials.</li>
                <li><i class="bi bi-exclamation-circle"></i> Lost or damaged materials are subject to library assessment.</li>
                <li><i class="bi bi-exclamation-circle"></i> Repeated violations may temporarily restrict borrowing privileges.</li>
            </ul>
        </article>
    </div>

    <section class="service-form-panel">
        <div class="service-form-header">
            <span class="service-detail-eyebrow">Borrow Book</span>
            <h2>Borrowing Request Form</h2>
            <p>
                Fill out the form to request borrowing of an eligible library material.
                The allowed borrowing period will be computed based on the selected category.
            </p>
        </div>

        <form class="service-form" method="POST" action="{{ route('services.store', $serviceKey) }}">
            @csrf
@php
    $selectedResourceId = old('resource_id', request('resource_id'));
    $selectedCopyId = old('copy_id');
@endphp
            <div class="service-form-grid">
                <div class="service-field service-field-full">
                    <label for="copy_id">Available Book Copy</label>

                    <select id="copy_id" name="copy_id" required>
                        <option value="">Select available book copy</option>

                        @forelse ($availableCopies ?? [] as $copy)
                            <option
    value="{{ $copy->copy_id }}"
    @selected(
        (string) $selectedCopyId === (string) $copy->copy_id ||
        ($selectedResourceId && (string) $copy->resource_id === (string) $selectedResourceId)
    )
>
    {{ $copy->title }}

    @if (!empty($copy->isbn))
        — ISBN: {{ $copy->isbn }}
    @endif

    — Accession: {{ $copy->accession_number }}
</option>
                        @empty
                            <option value="" disabled>No available book copies found</option>
                        @endforelse
                    </select>
                </div>

                <div class="service-field">
                    <label for="borrower_type">Borrower Type</label>

                    <select id="borrower_type" name="borrower_type" required>
                        <option value="">Select borrower type</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>

                <div class="service-field">
                    <label for="material_group">Material Type / Borrowing Category</label>

                    <select id="material_group" name="material_group" required>
                        <option value="">Select material category</option>
                        <option value="fiction">Fiction Books — 7 days</option>
                        <option value="graduate_school">Graduate School Materials — 2 days</option>
                        <option value="college_of_law">College of Law Materials — 2 days</option>
                        <option value="general">General Circulation Materials — 7 days</option>
                    </select>
                </div>

                <div class="service-field service-field-full">
                    <label for="purpose">Purpose</label>

                    <textarea
                        id="purpose"
                        name="purpose"
                        rows="4"
                        placeholder="Academic use, research, personal learning, etc."
                        required
                    ></textarea>
                </div>
            </div>

            <button type="submit" class="service-submit-btn">
                <i class="bi bi-send"></i>
                Submit Borrowing Request
            </button>
        </form>
    </section>

    <section class="service-feature-grid">
        <a href="#borrowing-history" class="service-feature-card">
            <i class="bi bi-clock-history"></i>
            <h3>View Borrowing History</h3>
            <p>Review previously borrowed materials and transaction records.</p>
        </a>

        <a href="#tracked-books" class="service-feature-card">
            <i class="bi bi-journal-check"></i>
            <h3>Track Borrowed Books</h3>
            <p>Monitor current borrowed books, due dates, and borrowing status.</p>
        </a>

        <a href="{{ route('services.show', 'book-renewal') }}" class="service-feature-card">
            <i class="bi bi-arrow-repeat"></i>
            <h3>Book Renewal</h3>
            <p>Extend eligible borrowed materials before the due date.</p>
        </a>

        <a href="{{ route('services.show', 'book-return') }}" class="service-feature-card">
            <i class="bi bi-box-arrow-in-left"></i>
            <h3>Book Return</h3>
            <p>Manage books due for return and upload proof of return.</p>
        </a>
    </section>

    <section id="tracked-books" class="service-table-panel">
        <div>
            <span class="service-detail-eyebrow">Tracking</span>
            <h2>Currently Borrowed Books</h2>
        </div>

        @forelse ($activeBorrowTransactions ?? [] as $transaction)
            <div class="service-record-card">
                <div>
                    <h3>{{ $transaction->title }}</h3>

                    <p>
                        Accession: {{ $transaction->accession_number }}
                        <br>
                        Due:
                        {{ \Carbon\Carbon::parse($transaction->due_at)->format('M d, Y') }}
                        <br>
                        Status: {{ $transaction->status_name }}
                    </p>
                </div>
            </div>
        @empty
            <div class="service-empty-state">
                <i class="bi bi-journal-x"></i>
                <p>No borrowed books to display yet.</p>
            </div>
        @endforelse
    </section>

    <section id="borrowing-history" class="service-table-panel">
        <div>
            <span class="service-detail-eyebrow">History</span>
            <h2>Borrowing History</h2>
        </div>

        <div class="service-empty-state">
            <i class="bi bi-clock-history"></i>
            <p>Your borrowing history will appear here once the history query is added.</p>
        </div>
    </section>

</section>