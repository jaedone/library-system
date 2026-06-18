import { domReady } from '../utils/dom-ready';

domReady(function () {
    const roleSelect = document.getElementById('role');
    const roleSections = document.querySelectorAll('[data-roles]');
    const employeeTypeSelect = document.getElementById('employee_type_id');
    const employeeTypeSections = document.querySelectorAll('[data-employee-type-section]');

    if (!roleSelect) {
        return;
    }

    function setFieldsDisabled(container, disabled) {
        const fields = container.querySelectorAll('input, select, textarea');

        fields.forEach(function (field) {
            field.disabled = disabled;
        });
    }

    function updateEmployeeTypeFields() {
        const selectedRole = roleSelect.value;
        let selectedType = '';

        if (employeeTypeSelect && employeeTypeSelect.selectedOptions.length > 0) {
            selectedType = employeeTypeSelect.selectedOptions[0].dataset.employeeType || '';
        }

        employeeTypeSections.forEach(function (section) {
            const requiredType = section.dataset.employeeTypeSection;
            const shouldShow = selectedRole === 'employee' && selectedType === requiredType;

            section.style.display = shouldShow ? 'block' : 'none';
            setFieldsDisabled(section, !shouldShow);
        });
    }

    function updateRoleFields() {
        const selectedRole = roleSelect.value;

        roleSections.forEach(function (section) {
            const allowedRoles = section.dataset.roles
                .split(',')
                .map(role => role.trim());

            const shouldShow = allowedRoles.includes(selectedRole);

            section.style.display = shouldShow ? 'block' : 'none';
            setFieldsDisabled(section, !shouldShow);
        });

        updateEmployeeTypeFields();
    }

    roleSelect.addEventListener('change', updateRoleFields);

    if (employeeTypeSelect) {
        employeeTypeSelect.addEventListener('change', updateEmployeeTypeFields);
    }

    updateRoleFields();
});