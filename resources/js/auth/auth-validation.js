import { domReady } from '../utils/dom-ready';

domReady(function () {
    const roleSelect = document.getElementById('role');
    const emailInput = document.getElementById('reg_email');
    const emailFeedback = document.getElementById('emailFeedback');

    const contactInput = document.getElementById('contact_number');
    const contactFeedback = document.getElementById('contactFeedback');

    const passwordInput = document.getElementById('reg_password');
    const confirmPasswordInput = document.getElementById('reg_password_confirmation');
    const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');

    const passwordRules = {
        length: document.querySelector('[data-password-rule="length"]'),
        lowercase: document.querySelector('[data-password-rule="lowercase"]'),
        uppercase: document.querySelector('[data-password-rule="uppercase"]'),
        number: document.querySelector('[data-password-rule="number"]'),
        special: document.querySelector('[data-password-rule="special"]'),
    };

    const restrictedEmailRoles = [
        'student',
        'employee',
        'alumni',
    ];

    function setFeedback(input, feedback, isValid, validMessage, invalidMessage) {
        if (!input || !feedback) {
            return;
        }

        input.classList.remove('is-valid-live', 'is-invalid-live');
        feedback.classList.remove('valid', 'invalid');

        if (!input.value.trim()) {
            feedback.textContent = '';
            return;
        }

        if (isValid) {
            input.classList.add('is-valid-live');
            feedback.classList.add('valid');
            feedback.textContent = validMessage;
        } else {
            input.classList.add('is-invalid-live');
            feedback.classList.add('invalid');
            feedback.textContent = invalidMessage;
        }
    }

    function isValidEmailFormat(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isAllowedPupEmail(email) {
        const lowerEmail = email.toLowerCase();

        return lowerEmail.endsWith('@iskolarngbayan.pup.edu.ph')
            || lowerEmail.endsWith('@pup.edu.ph');
    }

    function validateEmailLive() {
        if (!emailInput || !roleSelect || !emailFeedback) {
            return;
        }

        const email = emailInput.value.trim();
        const role = roleSelect.value;

        if (!email) {
            emailInput.classList.remove('is-valid-live', 'is-invalid-live');
            emailFeedback.classList.remove('valid', 'invalid');
            emailFeedback.textContent = '';
            return;
        }

        if (!isValidEmailFormat(email)) {
            setFeedback(
                emailInput,
                emailFeedback,
                false,
                '',
                'Please enter a valid email address.'
            );
            return;
        }

        if (restrictedEmailRoles.includes(role) && !isAllowedPupEmail(email)) {
            setFeedback(
                emailInput,
                emailFeedback,
                false,
                '',
                'This role must use @iskolarngbayan.pup.edu.ph or @pup.edu.ph.'
            );
            return;
        }

        setFeedback(
            emailInput,
            emailFeedback,
            true,
            restrictedEmailRoles.includes(role) ? 'Valid PUP email.' : 'Valid visitor email.',
            ''
        );
    }

    function validateContactLive() {
        if (!contactInput || !contactFeedback) {
            return;
        }

        const contact = contactInput.value.trim();

        contactInput.classList.remove('is-valid-live', 'is-invalid-live');
        contactFeedback.classList.remove('valid', 'invalid');

        if (!contact) {
            contactInput.classList.add('is-invalid-live');
            contactFeedback.classList.add('invalid');
            contactFeedback.textContent = 'Contact number is required.';
            return;
        }

        const isValid = /^(09\d{9}|\+639\d{9})$/.test(contact);

        setFeedback(
            contactInput,
            contactFeedback,
            isValid,
            'Valid Philippine mobile number.',
            'Use 09171234567 or +639171234567.'
        );
    }

    function setPasswordRule(ruleElement, passed) {
        if (!ruleElement) {
            return;
        }

        ruleElement.classList.remove('passed', 'failed');
        ruleElement.classList.add(passed ? 'passed' : 'failed');
    }

    function validatePasswordLive() {
        if (!passwordInput) {
            return;
        }

        const password = passwordInput.value;

        const checks = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password),
        };

        setPasswordRule(passwordRules.length, checks.length);
        setPasswordRule(passwordRules.lowercase, checks.lowercase);
        setPasswordRule(passwordRules.uppercase, checks.uppercase);
        setPasswordRule(passwordRules.number, checks.number);
        setPasswordRule(passwordRules.special, checks.special);

        passwordInput.classList.remove('is-valid-live', 'is-invalid-live');

        if (!password) {
            return;
        }

        const isStrong = Object.values(checks).every(Boolean);

        passwordInput.classList.add(isStrong ? 'is-valid-live' : 'is-invalid-live');

        validateConfirmPasswordLive();
    }

    function validateConfirmPasswordLive() {
        if (!passwordInput || !confirmPasswordInput || !confirmPasswordFeedback) {
            return;
        }

        const password = passwordInput.value;
        const confirmation = confirmPasswordInput.value;

        confirmPasswordInput.classList.remove('is-valid-live', 'is-invalid-live');
        confirmPasswordFeedback.classList.remove('valid', 'invalid');

        if (!confirmation) {
            confirmPasswordFeedback.textContent = '';
            return;
        }

        if (password === confirmation) {
            confirmPasswordInput.classList.add('is-valid-live');
            confirmPasswordFeedback.classList.add('valid');
            confirmPasswordFeedback.textContent = 'Passwords match.';
        } else {
            confirmPasswordInput.classList.add('is-invalid-live');
            confirmPasswordFeedback.classList.add('invalid');
            confirmPasswordFeedback.textContent = 'Passwords do not match.';
        }
    }

    roleSelect?.addEventListener('change', validateEmailLive);
    emailInput?.addEventListener('input', validateEmailLive);
    contactInput?.addEventListener('input', validateContactLive);
    passwordInput?.addEventListener('input', validatePasswordLive);
    confirmPasswordInput?.addEventListener('input', validateConfirmPasswordLive);

    validateEmailLive();
    validateContactLive();
    validatePasswordLive();
    validateConfirmPasswordLive();
});