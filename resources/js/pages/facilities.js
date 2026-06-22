document.addEventListener('DOMContentLoaded', function () {
    const slider = document.querySelector('[data-facility-slider]');

    if (!slider) {
        console.warn('Facility slider not found.');
        return;
    }

    const slides = Array.from(slider.querySelectorAll('[data-facility-slide]'));
    const nextButtons = Array.from(slider.querySelectorAll('[data-facility-next]'));
    const currentText = slider.querySelector('[data-facility-current]');

    if (slides.length === 0) {
        console.warn('No facility slides found.');
        return;
    }

    let activeIndex = 0;

    function showSlide(index) {
        activeIndex = (index + slides.length) % slides.length;

        slides.forEach(function (slide, slideIndex) {
            slide.classList.toggle('is-active', slideIndex === activeIndex);
        });

        if (currentText) {
            currentText.textContent = activeIndex + 1;
        }
    }

    function nextSlide() {
        showSlide(activeIndex + 1);
    }

    function previousSlide() {
        showSlide(activeIndex - 1);
    }

    nextButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            nextSlide();
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowRight') {
            nextSlide();
        }

        if (event.key === 'ArrowLeft') {
            previousSlide();
        }
    });

    showSlide(0);
});