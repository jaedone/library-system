<div class="book-details-overlay" data-book-details-overlay hidden></div>

<aside class="book-details-panel" data-book-details-panel aria-hidden="true">
    <button type="button" class="book-details-close" data-book-details-close>
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="book-details-cover">
        <img data-book-cover src="" alt="Book cover">
    </div>

    <div class="book-details-content">
        <span class="book-details-kicker" data-book-material>Library Material</span>

        <h2 data-book-title>Book Title</h2>

        <p class="book-details-author" data-book-authors>Author</p>

        <p class="book-details-description" data-book-description>
            Description will appear here.
        </p>

        <div class="book-details-grid">
            <div>
                <span>ISBN</span>
                <strong data-book-isbn>N/A</strong>
            </div>

            <div>
                <span>Category</span>
                <strong data-book-category>N/A</strong>
            </div>

            <div>
                <span>Publication Year</span>
                <strong data-book-year>N/A</strong>
            </div>

            <div>
                <span>Availability</span>
                <strong data-book-availability>N/A</strong>
            </div>

            <div>
                <span>Location</span>
                <strong data-book-location>N/A</strong>
            </div>

            <div>
                <span>Copies</span>
                <strong data-book-copies>N/A</strong>
            </div>
        </div>

        <div class="book-details-actions">
            <a href="#" class="book-details-primary" data-book-borrow>
                <i class="bi bi-book"></i>
                Borrow Book
            </a>

            <a href="#" class="book-details-secondary" data-book-reserve>
                <i class="bi bi-bookmark-check"></i>
                Reserve Book
            </a>
        </div>
    </div>
</aside>

<script type="application/json" data-book-details-json>
{!! json_encode($bookDetailsResources ?? $catalogResources ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
</script>