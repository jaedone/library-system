import { domReady } from '../utils/dom-ready';

domReady(function () {
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (!sidebarToggle) {
        return;
    }

    const mobileQuery = window.matchMedia('(max-width: 768px)');

    function isMobile() {
        return mobileQuery.matches;
    }

    if (!isMobile() && localStorage.getItem('sidebar-collapsed') === 'true') {
        document.body.classList.add('sidebar-collapsed');
    }

    sidebarToggle.addEventListener('click', function () {
        if (isMobile()) {
            document.body.classList.toggle('mobile-menu-open');
            return;
        }

        document.body.classList.toggle('sidebar-collapsed');

        localStorage.setItem(
            'sidebar-collapsed',
            document.body.classList.contains('sidebar-collapsed')
        );
    });

    mobileQuery.addEventListener('change', function () {
        document.body.classList.remove('mobile-menu-open');

        if (isMobile()) {
            document.body.classList.remove('sidebar-collapsed');
        } else if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    });
});