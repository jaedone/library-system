document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.querySelector('[data-profile-edit]');
    const cancelButton = document.querySelector('[data-profile-cancel]');
    const fields = Array.from(document.querySelectorAll('[data-profile-field]'));
    const actions = document.querySelector('[data-profile-edit-actions]');

    if (!editButton || fields.length === 0 || !actions) {
        return;
    }

    const originalValues = new Map();

    fields.forEach(function (field) {
        originalValues.set(field, field.value);
    });

    function enableEditMode() {
        fields.forEach(function (field) {
            field.disabled = false;
        });

        actions.hidden = false;
        editButton.hidden = true;
    }

    function cancelEditMode() {
        fields.forEach(function (field) {
            field.value = originalValues.get(field);
            field.disabled = true;
        });

        actions.hidden = true;
        editButton.hidden = false;
    }

    editButton.addEventListener('click', enableEditMode);

    if (cancelButton) {
        cancelButton.addEventListener('click', cancelEditMode);
    }
});

const photoInput = document.querySelector('[data-profile-photo-input]');
const photoForm = document.querySelector('[data-profile-photo-form]');

if (photoInput && photoForm) {
    photoInput.addEventListener('change', function () {
        if (photoInput.files.length > 0) {
            photoForm.submit();
        }
    });
}

const creditCarousel = document.querySelector('[data-credit-carousel]');

if (creditCarousel) {
    const slides = Array.from(creditCarousel.querySelectorAll('[data-credit-slide]'));
    const nextButton = creditCarousel.querySelector('[data-credit-next]');
    const title = creditCarousel.querySelector('[data-credit-title]');
    let activeIndex = 0;

    function showCreditSlide(index) {
        activeIndex = (index + slides.length) % slides.length;

        slides.forEach(function (slide, slideIndex) {
            slide.classList.toggle('is-active', slideIndex === activeIndex);
        });

        if (title) {
            title.textContent = slides[activeIndex].dataset.title || 'Credit Information';
        }
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            showCreditSlide(activeIndex + 1);
        });
    }

    showCreditSlide(0);
}