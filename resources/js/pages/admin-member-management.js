document.addEventListener('DOMContentLoaded', function () {
    const panel = document.querySelector('[data-member-panel]');
    const closeButton = document.querySelector('[data-member-panel-close]');
    const usersJson = document.querySelector('[data-member-users-json]');
    const restrictionsJson = document.querySelector('[data-member-restrictions-json]');

    if (!panel || !usersJson) {
        return;
    }

    let users = [];
    let restrictions = {};

    try {
        users = JSON.parse(usersJson.textContent || '[]');
        restrictions = JSON.parse(restrictionsJson?.textContent || '{}');
    } catch (error) {
        console.error('Invalid member management JSON.', error);
        return;
    }

    const userMap = new Map();

    users.forEach(function (user) {
        userMap.set(String(user.id), user);
    });

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');

        if (meta) {
            return meta.getAttribute('content');
        }

        const tokenInput = document.querySelector('input[name="_token"]');

        return tokenInput ? tokenInput.value : '';
    }

    function setText(selector, value, fallback = 'N/A') {
        const element = panel.querySelector(selector);

        if (element) {
            element.textContent = value || fallback;
        }
    }

    function setValue(selector, value) {
        const element = panel.querySelector(selector);

        if (element) {
            element.value = value ?? '';
        }
    }

    function setSection(mode) {
        panel.querySelectorAll('[data-panel-section]').forEach(function (section) {
            section.hidden = section.dataset.panelSection !== mode;
        });

        const labels = {
            view: 'User Profile',
            edit: 'Edit Profile',
            ban: 'Service Restriction',
            delete: 'Delete User',
        };

        setText('[data-panel-mode-label]', labels[mode] || 'User Profile');
    }

    function openPanel() {
        panel.classList.add('is-open');
        panel.setAttribute('aria-hidden', 'false');
    }

    function closePanel() {
        panel.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');
    }

    function getInitials(user) {
        const first = user.first_name || '';
        const last = user.last_name || '';

        const initials = `${first.charAt(0)}${last.charAt(0)}`.trim();

        return initials || 'U';
    }

    function fillHeader(user) {
        setText('[data-panel-name]', user.full_name || 'No profile name');
        setText('[data-panel-email]', user.email || '');

        const initials = panel.querySelector('[data-panel-initials]');
        const photo = panel.querySelector('[data-panel-photo]');

        if (initials) {
            initials.textContent = getInitials(user);
        }

        if (photo) {
            if (user.profile_photo_url) {
                photo.src = user.profile_photo_url;
                photo.hidden = false;

                if (initials) {
                    initials.hidden = true;
                }
            } else {
                photo.hidden = true;
                photo.removeAttribute('src');

                if (initials) {
                    initials.hidden = false;
                }
            }
        }
    }

    function fillView(user) {
        setText('[data-view-role]', user.role);
        setText('[data-view-status]', user.status);
        setText('[data-view-library-account]', user.library_account_number);
        setText('[data-view-contact]', user.contact_number);
        setText('[data-view-student-number]', user.student_number);
        setText('[data-view-employee-number]', user.employee_number);
        setText('[data-view-college]', user.college);
        setText('[data-view-program]', user.program);
        setText('[data-view-department]', user.department);
        setText('[data-view-created]', user.created_at);
    }

    function fillEdit(user) {
        const form = panel.querySelector('[data-edit-form]');

        if (form) {
            form.action = `/admin/member-management/${user.id}`;
        }

        setValue('[data-edit-email]', user.email);
        setValue('[data-edit-role]', user.role_id);
        setValue('[data-edit-status]', user.account_status_id);

        setValue('[data-edit-first-name]', user.first_name);
        setValue('[data-edit-middle-name]', user.middle_name);
        setValue('[data-edit-last-name]', user.last_name);
        setValue('[data-edit-contact]', user.contact_number);

        setValue('[data-edit-student-number]', user.student_number);
        setValue('[data-edit-college]', user.college_id);
        setValue('[data-edit-program]', user.program_id);
        setValue('[data-edit-year-level]', user.year_level);

        setValue('[data-edit-employee-number]', user.employee_number);
        setValue('[data-edit-employee-id-number]', user.employee_id_number);
        setValue('[data-edit-employee-type]', user.employee_type_id);
        setValue('[data-edit-department]', user.department_id);
    }

    function getServiceName(serviceKey) {
        const option = panel.querySelector(`[data-ban-form] select[name="service_key"] option[value="${serviceKey}"]`);

        return option ? option.textContent.trim() : serviceKey;
    }

    function formatRestrictionDate(value) {
        if (!value) {
            return 'No end date';
        }

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString(undefined, {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: 'numeric',
            minute: '2-digit',
        });
    }

    function fillBan(user) {
        const form = panel.querySelector('[data-ban-form]');
        const list = panel.querySelector('[data-ban-list]');

        if (form) {
            form.action = `/admin/member-management/${user.id}/ban-service`;
            form.reset();
        }

        if (!list) {
            return;
        }

        const userRestrictions = restrictions[String(user.id)] || [];

        if (userRestrictions.length === 0) {
            list.innerHTML = `
                <div class="admin-member-empty">
                    <i class="bi bi-shield-check"></i>
                    <p>No active service restrictions.</p>
                </div>
            `;
            return;
        }

        list.innerHTML = userRestrictions.map(function (restriction) {
            return `
                <div class="admin-member-ban-item">
                    <div>
                        <strong>${getServiceName(restriction.service_key)}</strong>
                        <span>${restriction.reason || 'No reason provided.'}</span>
                        <small>Until: ${formatRestrictionDate(restriction.restricted_until)}</small>
                    </div>

                    <form method="POST" action="/admin/member-management/${user.id}/ban-service/${restriction.id}">
                        <input type="hidden" name="_token" value="${getCsrfToken()}">
                        <input type="hidden" name="_method" value="DELETE">

                        <button type="submit">
                            Unban
                        </button>
                    </form>
                </div>
            `;
        }).join('');
    }

    function fillDelete(user) {
        const form = panel.querySelector('[data-delete-form]');

        if (form) {
            form.action = `/admin/member-management/${user.id}`;
        }
    }

    function openMode(userId, mode) {
        const user = userMap.get(String(userId));

        if (!user) {
            console.warn('User not found:', userId);
            return;
        }

        fillHeader(user);
        setSection(mode);

        if (mode === 'view') {
            fillView(user);
        }

        if (mode === 'edit') {
            fillEdit(user);
        }

        if (mode === 'ban') {
            fillBan(user);
        }

        if (mode === 'delete') {
            fillDelete(user);
        }

        openPanel();
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-member-action]');

        if (!button) {
            return;
        }

        const mode = button.dataset.memberAction;
        const userId = button.dataset.memberId;

        openMode(userId, mode);
    });

    if (closeButton) {
        closeButton.addEventListener('click', closePanel);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePanel();
        }
    });
});