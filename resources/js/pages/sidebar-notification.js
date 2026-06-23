document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-sidebar-notification-toggle]');
    const popover = document.querySelector('[data-sidebar-notification-popover]');

    if (!toggle || !popover) {
        return;
    }

    function openPopover() {
        popover.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
    }

    function closePopover() {
        popover.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
    }

    function isOpen() {
        return toggle.getAttribute('aria-expanded') === 'true';
    }

    toggle.addEventListener('click', function (event) {
        event.stopPropagation();

        if (isOpen()) {
            closePopover();
        } else {
            openPopover();
        }
    });

    popover.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('click', function () {
        if (isOpen()) {
            closePopover();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && isOpen()) {
            closePopover();
        }
    });
});