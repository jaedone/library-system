import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

document.addEventListener('DOMContentLoaded', function () {
    const roulette = document.querySelector('[data-service-roulette]');

    if (!roulette) {
        return;
    }

    const cards = Array.from(roulette.querySelectorAll('[data-service-card]'));
    const prevButton = roulette.querySelector('[data-service-prev]');
    const nextButton = roulette.querySelector('[data-service-next]');
    const dotsContainer = roulette.querySelector('[data-service-dots]');

    if (cards.length === 0) {
        return;
    }

    let activeIndex = 0;

    function getOffset(index) {
        const length = cards.length;
        let offset = index - activeIndex;

        if (offset > length / 2) {
            offset -= length;
        }

        if (offset < -length / 2) {
            offset += length;
        }

        return offset;
    }

    function setActiveCard(index) {
        activeIndex = (index + cards.length) % cards.length;

        cards.forEach(function (card, cardIndex) {
            const offset = getOffset(cardIndex);

            card.classList.remove(
                'is-active',
                'is-left-1',
                'is-right-1',
                'is-left-2',
                'is-right-2'
            );

            card.setAttribute('tabindex', '-1');

            if (offset === 0) {
                card.classList.add('is-active');
                card.setAttribute('tabindex', '0');
            } else if (offset === -1) {
                card.classList.add('is-left-1');
            } else if (offset === 1) {
                card.classList.add('is-right-1');
            } else if (offset === -2) {
                card.classList.add('is-left-2');
            } else if (offset === 2) {
                card.classList.add('is-right-2');
            }
        });

        updateDots();
    }

    function goNext() {
        setActiveCard(activeIndex + 1);
    }

    function goPrevious() {
        setActiveCard(activeIndex - 1);
    }

    function createDots() {
        if (!dotsContainer) {
            return;
        }

        dotsContainer.innerHTML = '';

        cards.forEach(function (_, index) {
            const dot = document.createElement('button');

            dot.type = 'button';
            dot.className = 'services-roulette-dot';
            dot.setAttribute('aria-label', `Go to service ${index + 1}`);

            dot.addEventListener('click', function () {
                setActiveCard(index);
            });

            dotsContainer.appendChild(dot);
        });
    }

    function updateDots() {
        if (!dotsContainer) {
            return;
        }

        const dots = Array.from(dotsContainer.querySelectorAll('.services-roulette-dot'));

        dots.forEach(function (dot, index) {
            dot.classList.toggle('is-active', index === activeIndex);
        });
    }

    cards.forEach(function (card, index) {
        card.addEventListener('click', function (event) {
            const offset = getOffset(index);

            if (offset !== 0) {
                event.preventDefault();
                setActiveCard(index);
            }
        });
    });

    prevButton?.addEventListener('click', goPrevious);
    nextButton?.addEventListener('click', goNext);

    document.addEventListener('keydown', function (event) {
        const activeElement = document.activeElement;
        const isTyping = activeElement && ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeElement.tagName);

        if (isTyping) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            goPrevious();
        }

        if (event.key === 'ArrowRight') {
            goNext();
        }

        if (event.key === 'Enter') {
            const activeCard = cards[activeIndex];

            if (activeCard) {
                window.location.href = activeCard.href;
            }
        }
    });

    createDots();
    setActiveCard(0);
});

document.addEventListener('DOMContentLoaded', function () {
    const startDate = document.getElementById('borrow_start_date');
    const endDate = document.getElementById('borrow_end_date');
    const hint = document.getElementById('borrow_date_hint');
    const materialGroup = document.getElementById('material_group');

    if (!copySelect) return;

    function getAllowedDays(category, material) {
        const text = `${category} ${material}`.toLowerCase();

        if (text.includes('graduate')) return 2;
        if (text.includes('law')) return 2;
        if (text.includes('fiction')) return 7;

        return 7;
    }

    function fillBookDetails() {
        if (!option || !option.value) return;
        document.getElementById('book_isbn').value = option.dataset.isbn || '';
        document.getElementById('book_accession').value = option.dataset.accession || '';
        document.getElementById('book_author').value = option.dataset.author || '';
        document.getElementById('book_material').value = option.dataset.material || '';

        const category = option.dataset.category || '';
        const material = option.dataset.material || '';
        const allowedDays = getAllowedDays(category, material);

        materialGroup.value = category.toLowerCase().includes('graduate')
            ? 'graduate_school'
            : category.toLowerCase().includes('law')
                ? 'college_of_law'
                : material.toLowerCase().includes('fiction')
                    ? 'fiction'
                    : 'general';

        hint.textContent = `Allowed borrowing period: up to ${allowedDays} day(s).`;

        validateDateRange();
    }

    function validateDateRange() {
        if (!startDate.value || !endDate.value) return;

        const option = copySelect.selectedOptions[0];
        const allowedDays = getAllowedDays(option.dataset.category || '', option.dataset.material || '');

        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

        if (diffDays > allowedDays) {
            endDate.setCustomValidity(`Borrowing range cannot exceed ${allowedDays} day(s).`);
            hint.textContent = `Not allowed. Maximum is ${allowedDays} day(s), but selected range is ${diffDays} day(s).`;
        } else {
            endDate.setCustomValidity('');
            hint.textContent = `Allowed borrowing period: ${diffDays} of ${allowedDays} day(s).`;
        }
    }

    copySelect.addEventListener('change', fillBookDetails);
    startDate?.addEventListener('change', validateDateRange);
    endDate?.addEventListener('change', validateDateRange);

    fillBookDetails();
});

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('book_search');
    const titleField = document.getElementById('book_title');
    const resultsBox = document.getElementById('book_search_results');
    const resourceIdField = document.getElementById('resource_id');
    const copyIdField = document.getElementById('copy_id');
    const accessionField = document.getElementById('book_accession');

    if (!searchInput || !titleField || !resultsBox || !copyIdField || !accessionField) {
        return;
    }

    const sources = Array.from(document.querySelectorAll('.book-search-source'));

    const isbnField = document.getElementById('book_isbn');
    const authorField = document.getElementById('book_author');
    const materialField = document.getElementById('book_material');
    const materialGroupField = document.getElementById('material_group');
    const hintField = document.getElementById('borrow_date_hint') || document.getElementById('reservation_date_hint');
    const startDateField = document.getElementById('borrow_start_date');
    const endDateField = document.getElementById('borrow_end_date');

    const copyTomSelect = new TomSelect(accessionField, {
        create: false,
        allowEmptyOption: true,
        placeholder: 'Select available copy',
        controlInput: null,
    });

    function getAllowedDays(category, material) {
        const text = `${category || ''} ${material || ''}`.toLowerCase();

        if (text.includes('graduate')) return 2;
        if (text.includes('law')) return 2;
        if (text.includes('fiction')) return 7;

        return 7;
    }

    function determineMaterialGroup(category, material) {
        const text = `${category || ''} ${material || ''}`.toLowerCase();

        if (text.includes('graduate')) return 'graduate_school';
        if (text.includes('law')) return 'college_of_law';
        if (text.includes('fiction')) return 'fiction';

        return 'general';
    }

    function validateBorrowingDateRange() {
        if (!startDateField || !endDateField || !hintField) return;
        if (!startDateField.value || !endDateField.value || !copyIdField.value) return;

        const selectedSource = sources.find(function (source) {
            const copies = JSON.parse(source.dataset.copies || '[]');

            return copies.some(function (copy) {
                return String(copy.copy_id) === String(copyIdField.value);
            });
        });

        if (!selectedSource) return;

        const allowedDays = getAllowedDays(
            selectedSource.dataset.category,
            selectedSource.dataset.material
        );

        const start = new Date(startDateField.value);
        const end = new Date(endDateField.value);
        const selectedDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

        if (selectedDays > allowedDays) {
            endDateField.setCustomValidity(`Borrowing range cannot exceed ${allowedDays} day(s).`);
            hintField.textContent = `Not allowed. Maximum is ${allowedDays} day(s), but selected range is ${selectedDays} day(s).`;
        } else {
            endDateField.setCustomValidity('');
            hintField.textContent = `Allowed borrowing period: ${selectedDays} of ${allowedDays} day(s).`;
        }
    }

    function fillCopyOptions(source) {
        const copies = JSON.parse(source.dataset.copies || '[]');
        const availableCopies = copies.filter(copy => copy.is_available);

        copyTomSelect.clear(true);
        copyTomSelect.clearOptions();

        if (availableCopies.length === 0) {
            copyIdField.value = '';

            copyTomSelect.addOption({
                value: '',
                text: 'No copies available',
            });

            copyTomSelect.setValue('', true);
            copyTomSelect.disable();
            return;
        }

        availableCopies.forEach(function (copy, index) {
            copyTomSelect.addOption({
                value: String(copy.copy_id),
                text: `${copy.accession_number || `Copy ${index + 1}`} — Available`,
            });
        });

        copyTomSelect.enable();
        copyTomSelect.refreshOptions(false);

        copyTomSelect.setValue(String(availableCopies[0].copy_id), true);
        copyIdField.value = availableCopies[0].copy_id;
    }

    function selectBook(source) {
        const hasAvailableCopies = source.dataset.hasAvailableCopies === 'true';

        searchInput.value = source.dataset.title || '';
        titleField.value = source.dataset.title || '';

        if (resourceIdField) {
            resourceIdField.value = source.dataset.resourceId || '';
        }

        copyIdField.value = source.dataset.defaultCopyId || '';

        if (isbnField) isbnField.value = source.dataset.isbn || '';
        if (authorField) authorField.value = source.dataset.author || '';
        if (materialField) materialField.value = source.dataset.material || '';

        if (materialGroupField) {
            materialGroupField.value = determineMaterialGroup(
                source.dataset.category,
                source.dataset.material
            );
        }

        fillCopyOptions(source);

        if (hintField) {
            if (!hasAvailableCopies) {
                hintField.textContent = 'This book has no available copies.';
            } else if (startDateField && endDateField) {
                const allowedDays = getAllowedDays(
                    source.dataset.category,
                    source.dataset.material
                );

                hintField.textContent = `Allowed borrowing period: up to ${allowedDays} day(s).`;
            } else {
                hintField.textContent = 'Approved reservations must be claimed within 2 days.';
            }
        }

        resultsBox.innerHTML = '';
        resultsBox.classList.remove('is-open');

        validateBorrowingDateRange();
    }

    function clearBookDetails() {
        if (resourceIdField) resourceIdField.value = '';

        copyIdField.value = '';
        titleField.value = '';

        if (isbnField) isbnField.value = '';
        if (authorField) authorField.value = '';
        if (materialField) materialField.value = '';
        if (materialGroupField) materialGroupField.value = '';

        copyTomSelect.clear(true);
        copyTomSelect.clearOptions();
        copyTomSelect.addOption({
            value: '',
            text: '',
        });
        copyTomSelect.setValue('', true);
        copyTomSelect.enable();

        if (hintField) {
            hintField.textContent = startDateField && endDateField
                ? 'Select a book title to check the allowed borrowing period.'
                : 'Approved reservations must be claimed within 2 days.';
        }
    }

    function renderResults(keyword = '') {
        const cleanKeyword = keyword.toLowerCase().trim();

        resultsBox.innerHTML = '';

        const matches = sources
            .filter(function (source) {
                if (!cleanKeyword) return true;

                const searchableText = [
                    source.dataset.title,
                    source.dataset.isbn,
                    source.dataset.author,
                    source.dataset.material,
                    source.dataset.category,
                ].join(' ').toLowerCase();

                return searchableText.includes(cleanKeyword);
            })
            .slice(0, 30);

        if (matches.length === 0) {
            resultsBox.innerHTML = `
                <div class="book-search-empty">
                    No matching books found.
                </div>
            `;
            resultsBox.classList.add('is-open');
            return;
        }

        matches.forEach(function (source) {
            const isAvailable = source.dataset.hasAvailableCopies === 'true';
            const item = document.createElement('button');

            item.type = 'button';
            item.disabled = !isAvailable;
            item.className = isAvailable
                ? 'book-search-result-item'
                : 'book-search-result-item is-disabled';

            item.innerHTML = `
                <strong>${source.dataset.title || 'Untitled'}</strong>
                <span>${source.dataset.author || 'Unknown Author'}</span>
                <small>
                    ISBN: ${source.dataset.isbn || 'N/A'}
                    ${isAvailable ? '· Available copies found' : '· No copies available'}
                </small>
            `;

            if (isAvailable) {
                item.addEventListener('click', function () {
                    selectBook(source);
                });
            }

            resultsBox.appendChild(item);
        });

        resultsBox.classList.add('is-open');
    }

    searchInput.addEventListener('input', function () {
        clearBookDetails();
        renderResults(searchInput.value);
    });

    searchInput.addEventListener('focus', function () {
        renderResults(searchInput.value);
    });

    titleField.addEventListener('click', function () {
        renderResults('');
    });

    copyTomSelect.on('change', function (value) {
        copyIdField.value = value || '';
        validateBorrowingDateRange();
    });

    startDateField?.addEventListener('change', validateBorrowingDateRange);
    endDateField?.addEventListener('change', validateBorrowingDateRange);

    document.addEventListener('click', function (event) {
        if (
            !event.target.closest('.book-picker-wrapper') &&
            event.target !== titleField
        ) {
            resultsBox.classList.remove('is-open');
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);

    if (document.getElementById('borrow_start_date')) {
        flatpickr('#borrow_start_date', {
            dateFormat: 'Y-m-d',
            minDate: tomorrow,
            allowInput: false,
            clickOpens: true,
        });
    }

    if (document.getElementById('borrow_end_date')) {
        flatpickr('#borrow_end_date', {
            dateFormat: 'Y-m-d',
            minDate: tomorrow,
            allowInput: false,
            clickOpens: true,
        });
    }

    if (document.getElementById('usage_date')) {
        flatpickr('#usage_date', {
            dateFormat: 'Y-m-d',
            minDate: tomorrow,
            allowInput: false,
            clickOpens: true,
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);

    flatpickr('#borrow_start_date', {
        dateFormat: 'Y-m-d',
        minDate: tomorrow,
        allowInput: false,
        clickOpens: true,
    });

    flatpickr('#borrow_end_date', {
        dateFormat: 'Y-m-d',
        minDate: tomorrow,
        allowInput: false,
        clickOpens: true,
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const panel = document.querySelector('.facility-booking-panel');
    if (!panel || typeof flatpickr === 'undefined') return;

    const facilitySelect = document.getElementById('facility_id');
    const participantsInput = document.getElementById('participants');
    const capacityHint = document.getElementById('facilityCapacityHint');
    const participantsHint = document.getElementById('participantsHint');

    const reservationDateInput = document.getElementById('reservation_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    const selectedDateLabel = document.getElementById('selectedDateLabel');
    const timeSlotsContainer = document.getElementById('facilityTimeSlots');

    const availabilityUrl = panel.dataset.availabilityUrl;

    let availableDates = [];
    let selectedFacilityId = null;

    const calendar = flatpickr('#facilityCalendar', {
        inline: true,
        minDate: new Date().fp_incr(1),
        maxDate: new Date().fp_incr(14),
        disable: [
            function (date) {
                const value = flatpickr.formatDate(date, 'Y-m-d');
                return !availableDates.includes(value);
            }
        ],
        onChange: function (_, dateStr) {
            reservationDateInput.value = dateStr;
            loadTimeSlots(dateStr);
        }
    });

    facilitySelect.addEventListener('change', async function () {
        selectedFacilityId = this.value;

        reservationDateInput.value = '';
        startTimeInput.value = '';
        endTimeInput.value = '';
        timeSlotsContainer.innerHTML = `<p class="facility-empty-message">Select an available date.</p>`;

        const capacity = this.selectedOptions[0]?.dataset.capacity;

        capacityHint.textContent = capacity
            ? `Maximum capacity: ${capacity} participant(s).`
            : '';

        checkCapacity();

        if (!selectedFacilityId) return;

        const response = await fetch(`${availabilityUrl}?facility_id=${selectedFacilityId}`);
        const data = await response.json();

        availableDates = data.available_dates || [];

        calendar.redraw();

        if (availableDates.length === 0) {
            selectedDateLabel.textContent = 'No available dates';
            timeSlotsContainer.innerHTML = `<p class="facility-empty-message">This facility is fully booked within the allowed reservation period.</p>`;
        } else {
            selectedDateLabel.textContent = 'Select a date';
        }
    });

    participantsInput.addEventListener('input', checkCapacity);

    function checkCapacity() {
        const capacity = Number(facilitySelect.selectedOptions[0]?.dataset.capacity || 0);
        const participants = Number(participantsInput.value || 0);

        if (capacity && participants > capacity) {
            participantsHint.textContent = `This exceeds the room capacity of ${capacity}.`;
            participantsInput.setCustomValidity('Participants exceed facility capacity.');
        } else {
            participantsHint.textContent = '';
            participantsInput.setCustomValidity('');
        }
    }

    async function loadTimeSlots(dateStr) {
        selectedDateLabel.textContent = flatpickr.formatDate(new Date(dateStr), 'l, F j');
        startTimeInput.value = '';
        endTimeInput.value = '';

        const response = await fetch(`${availabilityUrl}?facility_id=${selectedFacilityId}&date=${dateStr}`);
        const data = await response.json();

        const slots = data.available_slots || [];

        if (slots.length === 0) {
            timeSlotsContainer.innerHTML = `<p class="facility-empty-message">No time slots available for this date.</p>`;
            return;
        }

        timeSlotsContainer.innerHTML = slots.map(slot => `
            <button type="button" class="facility-time-btn" data-start="${slot.start}" data-end="${slot.end}">
                ${slot.label}
            </button>
        `).join('');

        document.querySelectorAll('.facility-time-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.facility-time-btn').forEach(item => item.classList.remove('is-selected'));

                this.classList.add('is-selected');
                startTimeInput.value = this.dataset.start;
                endTimeInput.value = this.dataset.end;
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const usageDate = document.getElementById('usage_date');

    if (!usageDate) return;

    flatpickr('#usage_date', {
        dateFormat: 'Y-m-d',
        minDate: new Date().fp_incr(1),
        allowInput: false,
        clickOpens: true,
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('reservationSuccessModal');

    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    modal.show();
});

document.addEventListener('DOMContentLoaded', function () {
    const borrowingModalElement = document.getElementById('borrowingSuccessModal');

    if (!borrowingModalElement) return;

    const modal = new bootstrap.Modal(borrowingModalElement);
    modal.show();
});

document.addEventListener('DOMContentLoaded', function () {
    const renewalSelect = document.getElementById('borrow_transaction_id');

    if (renewalSelect && renewalSelect.classList.contains('renewal-book-select')) {
        new TomSelect(renewalSelect, {
            create: false,
            allowEmptyOption: true,
            placeholder: 'Select borrowed book',
            controlInput: null,
        });
    }

    if (document.getElementById('requested_renewal_date')) {
        flatpickr('#requested_renewal_date', {
            dateFormat: 'Y-m-d',
            minDate: new Date().fp_incr(1),
            allowInput: false,
            clickOpens: true,
        });
    }

    const renewalModalElement = document.getElementById('renewalSuccessModal');

    if (renewalModalElement) {
        const modal = new bootstrap.Modal(renewalModalElement);
        modal.show();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const serviceMessageModal = document.getElementById('serviceMessageModal');

    if (!serviceMessageModal) return;

    const modal = new bootstrap.Modal(serviceMessageModal);
    modal.show();
});

document.addEventListener('DOMContentLoaded', function () {
    const selects = [
    '#return_borrow_transaction_id',
    '#borrow_transaction_id',
    '#material_condition',
    '#book_accession',
    '#destination_library',
    '#material_needed'
];

    selects.forEach(function (selector) {
        const element = document.querySelector(selector);

        if (!element) return;
        if (element.tomselect) return;

        new TomSelect(element, {
            create: false,
            allowEmptyOption: true,
            placeholder: element.querySelector('option')?.textContent || 'Select option',
            controlInput: null,
        });
    });
});
