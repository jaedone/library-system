<section class="service-workspace">
    <section class="service-form-panel">
        <div class="service-form-header">
            <span class="service-detail-eyebrow">Reserve Book</span>
            <h2>Book Reservation Form</h2>
            <p>
                Search or select a book title. Once approved, the reserved book must be claimed
                within 2 days or it may expire, apply a penalty, and become available again.
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

                    <input type="hidden" id="resource_id" name="resource_id" value="{{ old('resource_id') }}">
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

                    @error('resource_id')
                        <small class="service-field-error">{{ $message }}</small>
                    @enderror

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

                <div class="service-field service-field-full">
                    <label for="usage_date">Usage Date</label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-calendar-event"></i>
                        </span>

                        <input
                            type="text"
                            id="usage_date"
                            name="usage_date"
                            class="form-control service-date-picker"
                            value="{{ old('usage_date') }}"
                            placeholder="Select usage date"
                            readonly
                            required
                        >
                    </div>

                    <small id="reservation_date_hint" class="service-field-hint">
                        Approved reservations must be claimed within 2 days.
                    </small>

                    @error('usage_date')
                        <small class="service-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="service-field service-field-full">
                    <label for="reservation_notes">Notes</label>

                    <textarea
                        id="reservation_notes"
                        name="reservation_notes"
                        rows="4"
                        placeholder="Additional details or request notes"
                    >{{ old('reservation_notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="service-submit-btn">
                <i class="bi bi-bookmark-plus"></i>
                Submit Reservation
            </button>
        </form>
    </section>
</section>