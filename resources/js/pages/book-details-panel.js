document.addEventListener('DOMContentLoaded', function () {
    const panel = document.querySelector('[data-book-details-panel]');
    const overlay = document.querySelector('[data-book-details-overlay]');
    const closeButton = document.querySelector('[data-book-details-close]');
    const jsonScript = document.querySelector('[data-book-details-json]');

    if (!panel || !overlay || !jsonScript) {
        return;
    }

    let resources = [];

    try {
        resources = JSON.parse(jsonScript.textContent || '[]');
    } catch (error) {
        console.error('Invalid book details JSON.', error);
        return;
    }

    const resourceMap = new Map();

    resources.forEach(function (resource) {
        resourceMap.set(String(resource.id), resource);
    });

    function setText(selector, value) {
        const element = panel.querySelector(selector);

        if (element) {
            element.textContent = value || 'N/A';
        }
    }

    function openPanel(resource) {
        const cover = panel.querySelector('[data-book-cover]');
        const borrow = panel.querySelector('[data-book-borrow]');
        const reserve = panel.querySelector('[data-book-reserve]');

        if (cover) {
            cover.src = resource.cover_url || '/images/default-book-cover.png';
            cover.alt = resource.title || 'Book cover';
        }

        setText('[data-book-title]', resource.title);
        setText('[data-book-material]', resource.material_type_name);
        setText('[data-book-authors]', resource.authors);
        setText('[data-book-description]', resource.description);
        setText('[data-book-isbn]', resource.isbn);
        setText('[data-book-category]', resource.category_name);
        setText('[data-book-year]', resource.publication_year);
        setText('[data-book-availability]', resource.availability_statuses);
        setText('[data-book-location]', resource.library_locations);

        setText(
            '[data-book-copies]',
            `${resource.available_copies || 0} available / ${resource.total_copies || 0} total`
        );

        if (borrow) {
            borrow.href = `/services/book-borrowing?resource_id=${resource.id}`;
        }

        if (reserve) {
            reserve.href = `/services/book-reservation?resource_id=${resource.id}`;
        }

        panel.classList.add('is-open');
        panel.setAttribute('aria-hidden', 'false');
    }

    function closePanel() {
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden', 'true');
}

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-book-details-id]');

        if (!button) {
            return;
        }

        event.preventDefault();

        const resourceId = button.dataset.bookDetailsId;
        const resource = resourceMap.get(String(resourceId));

        if (!resource) {
            console.warn('Book details not found for resource ID:', resourceId);
            return;
        }

        openPanel(resource);
    });

    if (closeButton) {
        closeButton.addEventListener('click', closePanel);
    }

    overlay.addEventListener('click', closePanel);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePanel();
        }
    });
});