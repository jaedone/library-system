document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('[data-password-toggle]');

    toggleButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const input = button.closest('.password-input-wrap').querySelector('input');
            const icon = button.querySelector('i');

            if (!input) {
                return;
            }

            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';

            icon.classList.toggle('bi-eye', !isPassword);
            icon.classList.toggle('bi-eye-slash', isPassword);
        });
    });
});