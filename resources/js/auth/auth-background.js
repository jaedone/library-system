import { domReady } from '../utils/dom-ready';

domReady(function () {
    const authBackground = document.getElementById('authBackground');

    if (!authBackground) {
        return;
    }

    const authBgImages = [
        '/images/auth-bg/bg1.jpg',
        '/images/auth-bg/bg2.jpg',
        '/images/auth-bg/bg3.jpg',
    ];

    authBgImages.forEach(function (imagePath, index) {
        const slide = document.createElement('div');

        slide.classList.add('auth-bg-slide');
        slide.style.backgroundImage = `url("${imagePath}")`;

        if (index === 0) {
            slide.classList.add('active');
        }

        authBackground.appendChild(slide);
    });

    const slides = authBackground.querySelectorAll('.auth-bg-slide');

    if (slides.length === 0) {
        return;
    }

    let currentSlide = 0;

    setInterval(function () {
        slides[currentSlide].classList.remove('active');

        currentSlide = (currentSlide + 1) % slides.length;

        slides[currentSlide].classList.add('active');
    }, 6000);
});