import { domReady } from '../utils/dom-ready';

domReady(function () {
    const authContainer = document.getElementById('authContainer');

    if (!authContainer) {
        return;
    }

    const signUpBtn = document.getElementById('signUpBtn');
    const signInBtn = document.getElementById('signInBtn');

    if (signUpBtn) {
        signUpBtn.addEventListener('click', function () {
            authContainer.classList.add('right-panel-active');
        });
    }

    if (signInBtn) {
        signInBtn.addEventListener('click', function () {
            authContainer.classList.remove('right-panel-active');
        });
    }

    document.querySelectorAll('[data-toggle]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            event.preventDefault();

            authContainer.classList.toggle(
                'right-panel-active',
                element.dataset.toggle === 'signup'
            );
        });
    });

    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.togglePassword);

            if (!input) {
                return;
            }

            input.type = input.type === 'password' ? 'text' : 'password';

            const icon = button.querySelector('i');

            if (icon) {
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            }
        });
    });
});