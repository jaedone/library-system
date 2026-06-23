document.addEventListener('DOMContentLoaded', function () {
    const dataScript = document.querySelector('[data-website-data]');
    const layout = document.querySelector('[data-admin-push-layout]');
    const panel = document.querySelector('[data-admin-push-panel]');
    const closeButton = document.querySelector('[data-panel-close]');

    if (!dataScript || !layout || !panel) {
        return;
    }

    let websiteData = {
        announcement: [],
        resource: [],
        facility: [],
    };

    try {
        websiteData = JSON.parse(dataScript.textContent || '{}');
    } catch (error) {
        console.error('Invalid website information JSON.', error);
        return;
    }

    const routes = {
        announcement: '/admin/website-information/announcements',
        resource: '/admin/website-information/resources',
        facility: '/admin/website-information/facilities',
    };

    const labels = {
        announcement: 'Announcement',
        resource: 'Resource',
        facility: 'Facility',
    };

    let currentState = {
        module: null,
        action: null,
        id: null,
        dirty: false,
    };

    const form = panel.querySelector('[data-website-form]');
    const methodInput = panel.querySelector('[data-website-method]');
    const archiveForm = panel.querySelector('[data-archive-form]');
    const archiveValue = panel.querySelector('[data-archive-value]');
    const deleteForm = panel.querySelector('[data-delete-form]');

    const viewOutput = panel.querySelector('[data-view-output]');
    const livePreviewOutput = panel.querySelector('[data-live-preview-output]');

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');

        if (meta) {
            return meta.getAttribute('content');
        }

        const tokenInput = document.querySelector('input[name="_token"]');

        return tokenInput ? tokenInput.value : '';
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getItems(module) {
        return Array.isArray(websiteData[module]) ? websiteData[module] : [];
    }

    function findItem(module, id) {
        return getItems(module).find(item => String(item.id) === String(id));
    }

    function setPanelTitle(title, subtitle = 'View, add, update, archive, or delete selected information.') {
        const titleOutput = panel.querySelector('[data-panel-title]');
        const subtitleOutput = panel.querySelector('[data-panel-subtitle]');
        const eyebrowOutput = panel.querySelector('[data-panel-eyebrow]');

        if (titleOutput) titleOutput.textContent = title;
        if (subtitleOutput) subtitleOutput.textContent = subtitle;
        if (eyebrowOutput) eyebrowOutput.textContent = 'Website Information';
    }

    function openPanel() {
        layout.classList.add('is-panel-open');
        panel.setAttribute('aria-hidden', 'false');
    }

    function closePanel() {
        layout.classList.remove('is-panel-open');
        panel.setAttribute('aria-hidden', 'true');

        currentState = {
            module: null,
            action: null,
            id: null,
            dirty: false,
        };
    }

    function confirmDiscardChanges() {
        if (!currentState.dirty) {
            return true;
        }

        return confirm('You have unsaved changes. Do you want to discard them?');
    }

    function setSection(mode) {
        panel.querySelectorAll('[data-panel-section]').forEach(function (section) {
            section.hidden = section.dataset.panelSection !== mode;
        });
    }

    function setActiveFormGroup(module) {
        panel.querySelectorAll('[data-form-group]').forEach(function (group) {
            const isActive = group.dataset.formGroup === module;

            group.hidden = !isActive;

            group.querySelectorAll('input, textarea, select').forEach(function (field) {
                field.disabled = !isActive;
            });
        });
    }

    function getActiveGroup() {
        if (!currentState.module) {
            return null;
        }

        return panel.querySelector(`[data-form-group="${currentState.module}"]`);
    }

    function setField(name, value) {
        const group = getActiveGroup();

        if (!group) return;

        const field = group.querySelector(`[data-field="${name}"]`);

        if (!field) return;

        if (field.type === 'checkbox') {
            field.checked = Boolean(value);
            return;
        }

        field.value = value ?? '';
    }

    function getField(name) {
        const group = getActiveGroup();

        if (!group) return '';

        const field = group.querySelector(`[data-field="${name}"]`);

        if (!field) return '';

        if (field.type === 'checkbox') {
            return field.checked;
        }

        return field.value;
    }

    function resetForm() {
        if (!form) return;

        form.reset();

        form.querySelectorAll('input[type="file"]').forEach(function (input) {
            input.value = '';
        });
    }

    function formatDateTimeLocal(value) {
        if (!value) return '';

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) return '';

        const pad = number => String(number).padStart(2, '0');

        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    function showAlert(message, type = 'success') {
        let alert = panel.querySelector('[data-panel-alert]');

        if (!alert) {
            alert = document.createElement('div');
            alert.dataset.panelAlert = '';
            panel.querySelector('.admin-push-panel-body').prepend(alert);
        }

        alert.className = type === 'success' ? 'admin-success-alert' : 'admin-error-alert';
        alert.textContent = message;

        setTimeout(function () {
            alert.remove();
        }, 3500);
    }

    function getAnnouncementImage(item) {
        if (!item || !item.image_path) {
            return null;
        }

        return `/${String(item.image_path).replace(/^\/+/, '')}`;
    }

    function getFacilityImage(item) {
        if (!item || !item.image_path) {
            return '/images/facilities/discussion-room.jpg';
        }

        return `/${String(item.image_path).replace(/^\/+/, '')}`;
    }

    function getResourceCover(item) {
        if (!item || !item.cover_image_path) {
            return '/images/auth-bg/bg4.jpg';
        }

        return `/storage/${item.cover_image_path}`;
    }

    function getImagePreview(fieldName, fallback) {
        const group = getActiveGroup();

        if (!group) return fallback;

        const input = group.querySelector(`[data-field="${fieldName}"]`);

        if (input && input.files && input.files[0]) {
            return URL.createObjectURL(input.files[0]);
        }

        const item = currentState.id ? findItem(currentState.module, currentState.id) : null;

        if (item) {
            if (fieldName === 'announcement_image') {
                return getAnnouncementImage(item) || fallback;
            }

            if (fieldName === 'facility_image') {
                return getFacilityImage(item);
            }

            if (fieldName === 'cover_image') {
                return getResourceCover(item);
            }
        }

        return fallback;
    }

    function renderFormPreview() {
        if (!livePreviewOutput || !currentState.module) {
            return;
        }

        const module = currentState.module;

        if (module === 'announcement') {
            const image = getImagePreview('announcement_image', null);

            livePreviewOutput.innerHTML = `
                <article class="announcement-card admin-live-preview">
                    <div class="announcement-icon">
                        ${
                            image
                                ? `<img src="${escapeHtml(image)}" alt="Announcement image">`
                                : `<i class="bi bi-megaphone"></i>`
                        }
                    </div>

                    <h3>${escapeHtml(getField('title') || 'Announcement Title')}</h3>

                    <p>${escapeHtml(getField('description') || 'Announcement description will appear here.')}</p>

                    <div class="admin-workspace-meta">
                        <span>
                            ${getField('published_at') ? `Scheduled: ${escapeHtml(getField('published_at'))}` : 'Publish immediately'}
                        </span>
                    </div>
                </article>
            `;

            return;
        }

        if (module === 'resource') {
            const image = getImagePreview('cover_image', '/images/auth-bg/bg4.jpg');

            livePreviewOutput.innerHTML = `
                <article class="admin-panel-preview-card">
                    <div class="admin-panel-preview-image" style="background-image: url('${escapeHtml(image)}');"></div>

                    <span class="admin-eyebrow">Library Resource</span>

                    <h3>${escapeHtml(getField('title') || 'Resource Title')}</h3>

                    <p>${escapeHtml(getField('description') || 'Resource description will appear here.')}</p>

                    <div class="admin-panel-preview-grid">
                        <div><span>Authors</span><strong>${escapeHtml(getField('authors') || 'Unknown Author')}</strong></div>
                        <div><span>ISBN</span><strong>${escapeHtml(getField('isbn') || 'N/A')}</strong></div>
                        <div><span>Year</span><strong>${escapeHtml(getField('publication_year') || 'N/A')}</strong></div>
                        <div><span>Digital</span><strong>${getField('is_digital') ? 'Yes' : 'No'}</strong></div>
                    </div>
                </article>
            `;

            return;
        }

        if (module === 'facility') {
            const image = getImagePreview('facility_image', '/images/facilities/discussion-room.jpg');

            livePreviewOutput.innerHTML = `
                <article class="admin-panel-preview-card">
                    <div class="admin-panel-preview-image" style="background-image: url('${escapeHtml(image)}');"></div>

                    <span class="admin-eyebrow">Library Facility</span>

                    <h3>${escapeHtml(getField('facility_name') || 'Facility Name')}</h3>

                    <p>${escapeHtml(getField('description') || 'Facility description will appear here.')}</p>

                    <div class="admin-panel-preview-grid">
                        <div><span>Location</span><strong>${escapeHtml(getField('location') || 'N/A')}</strong></div>
                        <div><span>Capacity</span><strong>${getField('capacity') ? `${escapeHtml(getField('capacity'))} users` : 'N/A'}</strong></div>
                        <div><span>Days</span><strong>${escapeHtml(getField('availability_days') || 'N/A')}</strong></div>
                        <div><span>Hours</span><strong>${escapeHtml(getField('availability_hours') || 'N/A')}</strong></div>
                    </div>
                </article>
            `;
        }
    }

    function renderView(module, item) {
        if (!viewOutput) return;

        if (module === 'announcement') {
            const image = getAnnouncementImage(item);

            viewOutput.innerHTML = `
                <article class="announcement-card admin-live-preview">
                    <div class="announcement-icon">
                        ${
                            image
                                ? `<img src="${escapeHtml(image)}" alt="Announcement image">`
                                : `<i class="bi bi-megaphone"></i>`
                        }
                    </div>

                    <h3>${escapeHtml(item.title || 'Untitled Announcement')}</h3>

                    <p>${escapeHtml(item.description || 'No description available.')}</p>

                    <div class="admin-workspace-meta">
                        <span>${item.is_archived ? 'Archived' : 'Visible'}</span>
                        <span>${item.published_at ? `Scheduled: ${escapeHtml(item.published_at)}` : 'Publish immediately'}</span>
                    </div>
                </article>
            `;

            return;
        }

        if (module === 'resource') {
            const cover = getResourceCover(item);

            viewOutput.innerHTML = `
                <article class="admin-panel-preview-card">
                    <div class="admin-panel-preview-image" style="background-image: url('${escapeHtml(cover)}');"></div>

                    <span class="admin-eyebrow">${escapeHtml(item.material_type_name || 'Library Material')}</span>

                    <h3>${escapeHtml(item.title || 'Untitled Resource')}</h3>

                    <p>${escapeHtml(item.description || 'No description available.')}</p>

                    <div class="admin-panel-preview-grid">
                        <div><span>Authors</span><strong>${escapeHtml(item.authors || 'Unknown Author')}</strong></div>
                        <div><span>ISBN</span><strong>${escapeHtml(item.isbn || 'N/A')}</strong></div>
                        <div><span>Category</span><strong>${escapeHtml(item.category_name || 'N/A')}</strong></div>
                        <div><span>Year</span><strong>${escapeHtml(item.publication_year || 'N/A')}</strong></div>
                        <div><span>Status</span><strong>${item.is_archived ? 'Archived' : 'Visible'}</strong></div>
                    </div>
                </article>
            `;

            return;
        }

        if (module === 'facility') {
            const image = getFacilityImage(item);

            viewOutput.innerHTML = `
                <article class="admin-panel-preview-card">
                    <div class="admin-panel-preview-image" style="background-image: url('${escapeHtml(image)}');"></div>

                    <span class="admin-eyebrow">Library Facility</span>

                    <h3>${escapeHtml(item.facility_name || 'Facility Name')}</h3>

                    <p>${escapeHtml(item.description || 'No description available.')}</p>

                    <div class="admin-panel-preview-grid">
                        <div><span>Location</span><strong>${escapeHtml(item.location || 'N/A')}</strong></div>
                        <div><span>Capacity</span><strong>${item.capacity ? `${escapeHtml(item.capacity)} users` : 'N/A'}</strong></div>
                        <div><span>Days</span><strong>${escapeHtml(item.availability_days || 'N/A')}</strong></div>
                        <div><span>Hours</span><strong>${escapeHtml(item.availability_hours || 'N/A')}</strong></div>
                        <div><span>Equipment</span><strong>${escapeHtml(item.equipment_text || 'N/A')}</strong></div>
                        <div><span>Usage For</span><strong>${escapeHtml(item.usage_for_text || 'N/A')}</strong></div>
                        <div><span>Status</span><strong>${item.is_archived ? 'Archived' : 'Visible'}</strong></div>
                    </div>
                </article>
            `;
        }
    }

    function fillCreate(module) {
        currentState = {
            module,
            action: 'add',
            id: null,
            dirty: false,
        };

        setSection('form');
        setActiveFormGroup(module);
        resetForm();

        form.action = routes[module];
        methodInput.value = 'POST';

        setPanelTitle(
            `Add ${labels[module]}`,
            `Create a new ${labels[module].toLowerCase()} without leaving this page.`
        );

        renderFormPreview();
        openPanel();
    }

    function fillEdit(module, id) {
        const item = findItem(module, id);

        if (!item) {
            showAlert(`${labels[module]} not found.`, 'error');
            return;
        }

        currentState = {
            module,
            action: 'edit',
            id,
            dirty: false,
        };

        setSection('form');
        setActiveFormGroup(module);
        resetForm();

        form.action = `${routes[module]}/${id}`;
        methodInput.value = 'PUT';

        if (module === 'announcement') {
            setField('title', item.title);
            setField('description', item.description);
            setField('published_at', formatDateTimeLocal(item.published_at));
        }

        if (module === 'resource') {
            setField('title', item.title);
            setField('description', item.description);
            setField('material_type_id', item.material_type_id);
            setField('category_id', item.category_id);
            setField('isbn', item.isbn);
            setField('publication_year', item.publication_year);
            setField('authors', item.authors);
            setField('is_reference_only', item.is_reference_only);
            setField('is_digital', item.is_digital);
            setField('digital_url', item.digital_url);
        }

        if (module === 'facility') {
            setField('facility_name', item.facility_name);
            setField('description', item.description);
            setField('capacity', item.capacity);
            setField('location', item.location);
            setField('availability_days', item.availability_days);
            setField('availability_hours', item.availability_hours);
            setField('equipment', item.equipment_text);
            setField('usage_for', item.usage_for_text);
        }

        setPanelTitle(
            `Update ${labels[module]}`,
            `Edit the selected ${labels[module].toLowerCase()} below.`
        );

        renderFormPreview();
        openPanel();
    }

    function fillView(module, id) {
        const item = findItem(module, id);

        if (!item) {
            showAlert(`${labels[module]} not found.`, 'error');
            return;
        }

        currentState = {
            module,
            action: 'view',
            id,
            dirty: false,
        };

        setSection('view');
        renderView(module, item);

        setPanelTitle(
            `View ${labels[module]}`,
            `Review the selected ${labels[module].toLowerCase()} information.`
        );

        openPanel();
    }

    function fillArchive(module, id) {
        const item = findItem(module, id);

        if (!item) {
            showAlert(`${labels[module]} not found.`, 'error');
            return;
        }

        const willArchive = !item.is_archived;

        currentState = {
            module,
            action: 'archive',
            id,
            dirty: false,
        };

        setSection('archive');

        const archiveTitle = panel.querySelector('[data-archive-title]');
        const archiveMessage = panel.querySelector('[data-archive-message]');

        if (archiveTitle) {
            archiveTitle.textContent = willArchive
                ? `Archive this ${labels[module].toLowerCase()}?`
                : `Restore this ${labels[module].toLowerCase()}?`;
        }

        if (archiveMessage) {
            archiveMessage.textContent = willArchive
                ? 'This will remove the item from the homepage but keep it in the database.'
                : 'This will make the item visible on the homepage again.';
        }

        archiveForm.action = `${routes[module]}/${id}/archive`;
        archiveValue.value = willArchive ? '1' : '0';

        setPanelTitle(
            willArchive ? `Archive ${labels[module]}` : `Restore ${labels[module]}`,
            willArchive
                ? `Hide this ${labels[module].toLowerCase()} from the homepage.`
                : `Show this ${labels[module].toLowerCase()} on the homepage again.`
        );

        openPanel();
    }

    function fillDelete(module, id) {
        const item = findItem(module, id);

        if (!item) {
            showAlert(`${labels[module]} not found.`, 'error');
            return;
        }

        currentState = {
            module,
            action: 'delete',
            id,
            dirty: false,
        };

        setSection('delete');

        const deleteTitle = panel.querySelector('[data-delete-title]');

        if (deleteTitle) {
            deleteTitle.textContent = `Delete this ${labels[module].toLowerCase()}?`;
        }

        deleteForm.action = `${routes[module]}/${id}`;

        setPanelTitle(
            `Delete ${labels[module]}`,
            `Confirm if you want to permanently remove this ${labels[module].toLowerCase()}.`
        );

        openPanel();
    }

    function openAction(module, action, id = null) {
        if (!confirmDiscardChanges()) {
            return;
        }

        if (action === 'add') fillCreate(module);
        if (action === 'view') fillView(module, id);
        if (action === 'edit') fillEdit(module, id);
        if (action === 'archive') fillArchive(module, id);
        if (action === 'delete') fillDelete(module, id);
    }

    function normalizeResponseItem(module, item) {
        if (!item) return null;

        if (module === 'facility') {
            item.equipment_text = item.equipment_text || '';
            item.usage_for_text = item.usage_for_text || '';
        }

        return item;
    }

    function upsertItem(module, item) {
        if (!item || !item.id) return;

        item = normalizeResponseItem(module, item);

        const list = getItems(module);
        const index = list.findIndex(existing => String(existing.id) === String(item.id));

        if (index >= 0) {
            list[index] = item;
        } else {
            list.unshift(item);
        }

        websiteData[module] = list;

        renderItemRow(module, item);
        applySearch(module);
    }

    function removeItem(module, id) {
        websiteData[module] = getItems(module).filter(item => String(item.id) !== String(id));

        const row = document.querySelector(`[data-item-row="${module}-${id}"]`);

        if (row) {
            row.remove();
        }

        applySearch(module);
    }

    function getItemTitle(module, item) {
        if (module === 'announcement') return item.title || 'Untitled Announcement';
        if (module === 'resource') return item.title || 'Untitled Resource';
        if (module === 'facility') return item.facility_name || 'Untitled Facility';

        return 'Untitled';
    }

    function getItemSubtitle(module, item) {
        if (item.is_archived) return 'Archived';

        if (module === 'announcement') return 'Visible';
        if (module === 'resource') return item.material_type_name || 'Library Material';
        if (module === 'facility') return 'Visible';

        return '';
    }

    function getItemIcon(module) {
        if (module === 'announcement') return 'bi-megaphone';
        if (module === 'resource') return 'bi-book';
        if (module === 'facility') return 'bi-building';

        return 'bi-circle';
    }

    function getSearchText(module, item) {
        if (module === 'announcement') {
            return `${item.title || ''} ${item.description || ''}`.toLowerCase();
        }

        if (module === 'resource') {
            return `${item.title || ''} ${item.authors || ''} ${item.isbn || ''} ${item.material_type_name || ''}`.toLowerCase();
        }

        if (module === 'facility') {
            return `${item.facility_name || ''} ${item.description || ''} ${item.location || ''}`.toLowerCase();
        }

        return '';
    }

    function getModuleList(module) {
        return document.querySelector(`[data-card-list="${module}"]`);
    }

    function buildItemRow(module, item) {
        const archiveLabel = item.is_archived ? 'Restore' : 'Archive';

        return `
            <div
                class="admin-website-item"
                data-item-row="${module}-${item.id}"
                data-search-text="${escapeHtml(getSearchText(module, item))}"
            >
                <div class="admin-website-item-main">
                    <i class="bi ${escapeHtml(getItemIcon(module))}"></i>

                    <div>
                        <strong>${escapeHtml(getItemTitle(module, item))}</strong>
                        <span>${escapeHtml(getItemSubtitle(module, item))}</span>
                    </div>
                </div>

                <div class="admin-website-item-actions">
                    <button type="button" data-website-action="view" data-module="${module}" data-id="${item.id}">View</button>
                    <button type="button" data-website-action="edit" data-module="${module}" data-id="${item.id}">Update</button>
                    <button type="button" data-website-action="archive" data-module="${module}" data-id="${item.id}">${archiveLabel}</button>
                    <button type="button" data-website-action="delete" data-module="${module}" data-id="${item.id}">Delete</button>
                </div>
            </div>
        `;
    }

    function renderItemRow(module, item) {
        const oldRow = document.querySelector(`[data-item-row="${module}-${item.id}"]`);

        if (oldRow) {
            oldRow.outerHTML = buildItemRow(module, item);
            return;
        }

        const list = getModuleList(module);

        if (!list) return;

        const emptyText = list.querySelector('.admin-empty-text');

        if (emptyText) {
            emptyText.remove();
        }

        list.insertAdjacentHTML('afterbegin', buildItemRow(module, item));
    }

    function applySearch(module) {
        const input = document.querySelector(`[data-card-search="${module}"]`);
        const list = getModuleList(module);

        if (!input || !list) return;

        const query = input.value.trim().toLowerCase();
        const rows = list.querySelectorAll('.admin-website-item');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const text = row.dataset.searchText || row.textContent.toLowerCase();
            const isVisible = query === '' || text.includes(query);

            row.hidden = !isVisible;

            if (isVisible) {
                visibleCount++;
            }
        });

        list.classList.toggle('has-no-results', rows.length > 0 && visibleCount === 0);
    }

    async function submitWebsiteForm(event) {
        event.preventDefault();

        if (!currentState.module || !form) return;

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            if (data.item) {
                upsertItem(currentState.module, data.item);
            }

            currentState.dirty = false;

            showAlert(data.message || `${labels[currentState.module]} saved successfully.`);

            closePanel();
        } catch (error) {
            showAlert(error.message || 'Something went wrong. Please check the form.', 'error');

            if (error.errors) {
                console.error(error.errors);
            }
        }
    }

    async function submitArchiveForm(event) {
        event.preventDefault();

        if (!currentState.module || !currentState.id || !archiveForm) return;

        const formData = new FormData(archiveForm);

        try {
            const response = await fetch(archiveForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            if (data.item) {
                upsertItem(currentState.module, data.item);
            }

            showAlert(data.message || `${labels[currentState.module]} updated successfully.`);

            closePanel();
        } catch (error) {
            showAlert(error.message || 'Unable to update archive status.', 'error');
        }
    }

    async function submitDeleteForm(event) {
        event.preventDefault();

        if (!currentState.module || !currentState.id || !deleteForm) return;

        const formData = new FormData(deleteForm);

        try {
            const response = await fetch(deleteForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            removeItem(currentState.module, currentState.id);

            showAlert(data.message || `${labels[currentState.module]} deleted successfully.`);

            closePanel();
        } catch (error) {
            showAlert(error.message || 'Unable to delete item.', 'error');
        }
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-website-action]');

        if (!button) return;

        const module = button.dataset.module;
        const action = button.dataset.websiteAction;
        const id = button.dataset.id || null;

        openAction(module, action, id);
    });

    document.querySelectorAll('[data-card-search]').forEach(function (input) {
        input.addEventListener('input', function () {
            applySearch(input.dataset.cardSearch);
        });
    });

    if (form) {
        form.addEventListener('submit', submitWebsiteForm);

        form.addEventListener('input', function () {
            if (currentState.action === 'add' || currentState.action === 'edit') {
                currentState.dirty = true;
                renderFormPreview();
            }
        });

        form.addEventListener('change', function () {
            if (currentState.action === 'add' || currentState.action === 'edit') {
                currentState.dirty = true;
                renderFormPreview();
            }
        });
    }

    if (archiveForm) {
        archiveForm.addEventListener('submit', submitArchiveForm);
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', submitDeleteForm);
    }

    if (closeButton) {
        closeButton.addEventListener('click', function () {
            if (!confirmDiscardChanges()) return;

            closePanel();
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && layout.classList.contains('is-panel-open')) {
            if (!confirmDiscardChanges()) return;

            closePanel();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    if (typeof flatpickr === 'undefined') {
        return;
    }

    flatpickr('.facility-available-dates-picker', {
        mode: 'multiple',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'F j, Y',
        conjunction: ', ',
        allowInput: false,
    });
});