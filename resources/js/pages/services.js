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