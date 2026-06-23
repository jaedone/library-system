<section class="service-form-panel">
    <div class="service-form-header">
        <span class="service-detail-eyebrow">Borrow Book</span>
        <h2>Borrowing Request Form</h2>
        <p>
            Search or select a book title. The book details will automatically appear,
            and the borrowing date range will be checked based on the material type.
        </p>
    </div>

    <form class="service-form" method="POST" action="{{ route('services.store', $serviceKey) }}">
        @csrf

        @php
            $selectedResourceId = old('resource_id', request('resource_id'));
            $selectedCopyId = old('copy_id');
        @endphp

        <div class="service-form-grid">
            <div class="service-field service-field-full book-picker-wrapper">
                <label for="book_search">Search Book</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        id="book_search"
                        class="form-control"
                        placeholder="Search book title, ISBN, author, or accession"
                        autocomplete="off"
                    >
                </div>

                <input type="hidden" id="copy_id" name="copy_id" value="{{ old('copy_id') }}">

                <div id="book_search_results" class="book-search-results"></div>

                @foreach ($availableCopies ?? [] as $book)
    <div
        class="book-search-source"
        data-resource-id="{{ $book->resource_id }}"
        data-default-copy-id="{{ $book->default_copy_id }}"
        data-title="{{ $book->title }}"
        data-isbn="{{ $book->isbn }}"
        data-author="{{ $book->authors ?? 'Unknown Author' }}"
        data-material="{{ $book->material_type_name }}"
        data-category="{{ $book->category_name }}"
        data-has-available-copies="{{ $book->has_available_copies ? 'true' : 'false' }}"
        data-copies='@json($book->copies)'
    ></div>
@endforeach

                @error('copy_id')
                    <small class="service-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="service-field service-field-full">
                <label for="book_title">Title</label>
                <input
                    type="text"
                    id="book_title"
                    placeholder="Click to select available book"
                    readonly
                    required
                >
            </div>

            <div class="service-field">
                <label for="book_isbn">ISBN</label>
                <input type="text" id="book_isbn" readonly>
            </div>

            <div class="service-field">
    <label for="book_accession">Available Copy</label>

    <select id="book_accession" class="form-select book-copy-select">
        <option value=""></option>
    </select>
</div>

            <div class="service-field">
                <label for="book_author">Author</label>
                <input type="text" id="book_author" readonly>
            </div>

            <div class="service-field">
                <label for="book_material">Material Type</label>
                <input type="text" id="book_material" readonly>
            </div>

            <input type="hidden" id="material_group" name="material_group" value="{{ old('material_group') }}">

            <div class="service-field">
                <label for="borrow_start_date">Borrowing Start Date</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>

                    <input
                        type="text"
                        id="borrow_start_date"
                        name="borrow_start_date"
                        class="form-control service-date-picker"
                        value="{{ old('borrow_start_date') }}"
                        placeholder="Select start date"
                        readonly
                        required
                    >
                </div>

                @error('borrow_start_date')
                    <small class="service-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="service-field">
                <label for="borrow_end_date">Borrowing End Date</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-check"></i>
                    </span>

                    <input
                        type="text"
                        id="borrow_end_date"
                        name="borrow_end_date"
                        class="form-control service-date-picker"
                        value="{{ old('borrow_end_date') }}"
                        placeholder="Select end date"
                        readonly
                        required
                    >
                </div>

                <small id="borrow_date_hint" class="service-field-hint">
                    Select a book title to check the allowed borrowing period.
                </small>

                @error('borrow_end_date')
                    <small class="service-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="service-field service-field-full">
                <label for="purpose">Purpose</label>

                <textarea
                    id="purpose"
                    name="purpose"
                    rows="4"
                    placeholder="Academic use, research, personal learning, etc."
                    required
                >{{ old('purpose') }}</textarea>

                @error('purpose')
                    <small class="service-field-error">{{ $message }}</small>
                @enderror

                @error('penalty')
                    <small class="service-field-error">{{ $message }}</small>
                @enderror
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

        @forelse ($borrowingHistory ?? [] as $transaction)
    <div class="service-record-card">
        <div>
            <h3>{{ $transaction->title }}</h3>

            <p>
                Accession: {{ $transaction->accession_number }}
                <br>
                Borrowed: {{ \Carbon\Carbon::parse($transaction->borrowed_at)->format('M d, Y') }}
                <br>
                Due: {{ \Carbon\Carbon::parse($transaction->due_at)->format('M d, Y') }}
                <br>
                Returned:
                {{ $transaction->returned_at
                    ? \Carbon\Carbon::parse($transaction->returned_at)->format('M d, Y')
                    : 'Not yet returned'
                }}
                <br>
                Status: {{ $transaction->status_name }}
            </p>
        </div>
    </div>
@empty
    <div class="service-empty-state">
        <i class="bi bi-clock-history"></i>
        <p>No borrowing history yet.</p>
    </div>
@endforelse
    </section>