document.addEventListener('DOMContentLoaded', function () {
    const dataScript = document.querySelector('[data-service-management-data]');
    const layout = document.querySelector('[data-admin-push-layout]');
    const panel = document.querySelector('[data-admin-push-panel]');
    const closeButton = document.querySelector('[data-panel-close]');

    if (!dataScript || !layout || !panel) {
        return;
    }

    let requests = [];

    try {
        requests = JSON.parse(dataScript.textContent || '[]');
    } catch (error) {
        console.error('Invalid services management JSON.', error);
        return;
    }

    const requestMap = new Map();

    requests.forEach(function (item) {
        requestMap.set(getRequestKey(item.service_key, item.id), item);
    });

    const serviceIcons = {
        'book-borrowing': 'bi-book',
        'book-reservation': 'bi-bookmark-check',
        'facility-reservation': 'bi-building-check',
        'book-renewal': 'bi-arrow-repeat',
        'book-return': 'bi-box-arrow-in-left',
        'referral-letter': 'bi-file-earmark-text',
    };

    const actionLabels = {
        view: 'View Request',
        review: 'Review Request',
        approve: 'Approve Request',
        reject: 'Reject Request',
        'confirm-return': 'Confirm Return',
        'follow-up': 'Send Follow-up',
        'send-letter': 'Send Referral Letter',
        penalty: 'Add Manual Penalty',
    };

    let currentState = {
        serviceKey: null,
        requestId: null,
        action: null,
        item: null,
    };

    const viewOutput = panel.querySelector('[data-service-view-output]');
    const decisionForm = panel.querySelector('[data-service-decision-form]');
    const decisionTitle = panel.querySelector('[data-decision-title]');
    const decisionMessage = panel.querySelector('[data-decision-message]');
    const decisionRemarks = panel.querySelector('[data-decision-remarks]');
    const decisionSubmit = panel.querySelector('[data-decision-submit]');
    const decisionIcon = panel.querySelector('[data-decision-icon]');

    const penaltyForm = panel.querySelector('[data-penalty-form]');
    const letterForm = panel.querySelector('[data-letter-form]');
    const panelFooter = panel.querySelector('[data-panel-footer]');
    const footerPenaltyButton = panel.querySelector('[data-footer-action="penalty"]');

    function getRequestKey(serviceKey, requestId) {
        return `${serviceKey}-${requestId}`;
    }

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

    function formatDate(value) {
        if (!value) {
            return 'N/A';
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

    function money(value) {
        const amount = Number(value || 0);

        return `₱${amount.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    }

    function findRequest(serviceKey, requestId) {
        return requestMap.get(getRequestKey(serviceKey, requestId));
    }

    function setPanelTitle(title, subtitle = 'Review request details and process the user’s service request.') {
        const titleOutput = panel.querySelector('[data-panel-title]');
        const subtitleOutput = panel.querySelector('[data-panel-subtitle]');
        const eyebrowOutput = panel.querySelector('[data-panel-eyebrow]');

        if (titleOutput) {
            titleOutput.textContent = title;
        }

        if (subtitleOutput) {
            subtitleOutput.textContent = subtitle;
        }

        if (eyebrowOutput) {
            eyebrowOutput.textContent = 'Services Management';
        }
    }

    function openPanel() {
        layout.classList.add('is-panel-open');
        panel.setAttribute('aria-hidden', 'false');
    }

    function closePanel() {
        layout.classList.remove('is-panel-open');
        panel.setAttribute('aria-hidden', 'true');

        currentState = {
            serviceKey: null,
            requestId: null,
            action: null,
            item: null,
        };
    }

    function setSection(sectionName) {
        panel.querySelectorAll('[data-panel-section]').forEach(function (section) {
            section.hidden = section.dataset.panelSection !== sectionName;
        });
    }

    function showAlert(message, type = 'success') {
        const alertSlot = panel.querySelector('[data-panel-alert]');

        if (!alertSlot) {
            return;
        }

        alertSlot.className = type === 'success' ? 'admin-success-alert' : 'admin-error-alert';
        alertSlot.textContent = message;
        alertSlot.hidden = false;

        setTimeout(function () {
            alertSlot.textContent = '';
            alertSlot.className = '';
            alertSlot.hidden = true;
        }, 3500);
    }

    function setCurrent(serviceKey, requestId, action) {
        const item = findRequest(serviceKey, requestId);

        if (!item) {
            console.warn('Request not found:', serviceKey, requestId);
            return false;
        }

        currentState = {
            serviceKey,
            requestId,
            action,
            item,
        };

        return true;
    }

    function buildInfoGrid(details) {
        return Object.entries(details)
            .filter(function ([, value]) {
                return value !== null && value !== undefined && value !== '';
            })
            .map(function ([label, value]) {
                return `
                    <div>
                        <span>${escapeHtml(label)}</span>
                        <strong>${escapeHtml(value)}</strong>
                    </div>
                `;
            })
            .join('');
    }

    function buildSmallList(items, emptyMessage, formatter) {
        if (!Array.isArray(items) || items.length === 0) {
            return `
                <div class="admin-service-empty-mini">
                    ${escapeHtml(emptyMessage)}
                </div>
            `;
        }

        return items.map(formatter).join('');
    }

    function renderUserContext(item) {
        const context = item.user_context || {};
        const credit = context.credit_score || {};
        const activeBorrowedBooks = context.active_borrowed_books || [];
        const pastBorrowedBooks = context.past_borrowed_books || [];
        const activeReservations = context.active_reservations || [];
        const outstandingPenalties = context.outstanding_penalties || [];

        return `
            <section class="admin-service-panel-section">
                <h3>User Account Context</h3>

                <div class="admin-panel-preview-grid">
                    <div>
                        <span>Credit Score</span>
                        <strong>${escapeHtml(credit.current_score ?? 100)}</strong>
                    </div>

                    <div>
                        <span>Credit Level</span>
                        <strong>${escapeHtml(credit.level_name || 'Starter Reader')}</strong>
                    </div>

                    <div>
                        <span>Outstanding Penalties</span>
                        <strong>${outstandingPenalties.length}</strong>
                    </div>

                    <div>
                        <span>Active Borrowed Books</span>
                        <strong>${activeBorrowedBooks.length}</strong>
                    </div>
                </div>
            </section>

            <section class="admin-service-panel-section">
                <h3>Active Borrowed Books</h3>

                ${buildSmallList(
                    activeBorrowedBooks,
                    'No active borrowed books.',
                    function (book) {
                        return `
                            <div class="admin-service-mini-record">
                                <strong>${escapeHtml(book.title)}</strong>
                                <span>Accession: ${escapeHtml(book.accession_number || 'N/A')}</span>
                                <span>Due: ${escapeHtml(formatDate(book.due_at))}</span>
                                <span>Status: ${escapeHtml(book.status_name || 'N/A')}</span>
                            </div>
                        `;
                    }
                )}
            </section>

            <section class="admin-service-panel-section">
                <h3>Active Reservations</h3>

                ${buildSmallList(
                    activeReservations,
                    'No active reservations.',
                    function (reservation) {
                        return `
                            <div class="admin-service-mini-record">
                                <strong>${escapeHtml(reservation.title)}</strong>
                                <span>${escapeHtml(reservation.reservation_type || 'Reservation')}</span>
                                <span>Status: ${escapeHtml(reservation.status_name || 'N/A')}</span>
                            </div>
                        `;
                    }
                )}
            </section>

            <section class="admin-service-panel-section">
                <h3>Outstanding Penalties</h3>

                ${buildSmallList(
                    outstandingPenalties,
                    'No outstanding penalties.',
                    function (penalty) {
                        return `
                            <div class="admin-service-mini-record danger">
                                <strong>${escapeHtml(penalty.penalty_type_name || 'Penalty')}</strong>
                                <span>${escapeHtml(penalty.description || 'No description.')}</span>
                                <span>Amount: ${escapeHtml(money(penalty.amount))}</span>
                                <span>Status: ${escapeHtml(penalty.status_name || 'N/A')}</span>
                            </div>
                        `;
                    }
                )}
            </section>

            <section class="admin-service-panel-section">
                <h3>Past Borrowing History</h3>

                ${buildSmallList(
                    pastBorrowedBooks,
                    'No past borrowing history found.',
                    function (book) {
                        return `
                            <div class="admin-service-mini-record">
                                <strong>${escapeHtml(book.title)}</strong>
                                <span>Borrowed: ${escapeHtml(formatDate(book.borrowed_at))}</span>
                                <span>Returned: ${escapeHtml(formatDate(book.returned_at))}</span>
                                <span>Status: ${escapeHtml(book.status_name || 'N/A')}</span>
                            </div>
                        `;
                    }
                )}
            </section>
        `;
    }

    function renderAvailability(item) {
        const availability = item.availability || {};

        return `
            <section class="admin-service-panel-section">
                <h3>Availability Check</h3>

                <div class="admin-service-availability ${availability.message && availability.message.includes('No') ? 'ok' : 'warning'}">
                    <i class="bi ${availability.message && availability.message.includes('No') ? 'bi-check-circle' : 'bi-exclamation-triangle'}"></i>
                    <span>${escapeHtml(availability.message || 'No availability warning.')}</span>
                </div>
            </section>
        `;
    }

    function renderMainDetails(item) {
        const details = item.details || {};
        const serviceKey = item.service_key;

        if (serviceKey === 'book-borrowing') {
            return buildInfoGrid({
                'Book Title': details.title,
                'ISBN': details.isbn,
                'Copy ID': details.copy_id,
                'Accession Number': details.accession_number,
                'Due Date': formatDate(details.due_at),
                'Status': item.status_name,
                'Remarks': item.raw_remarks,
            });
        }

        if (serviceKey === 'book-reservation') {
            return buildInfoGrid({
                'Book Title': details.title,
                'ISBN': details.isbn,
                'Resource ID': details.resource_id,
                'Specific Copy': details.accession_number || 'No specific copy',
                'Reserved At': formatDate(details.requested_at),
                'Expires At': formatDate(details.expires_at),
                'Status': item.status_name,
                'Remarks': item.raw_remarks,
            });
        }

        if (serviceKey === 'facility-reservation') {
            return buildInfoGrid({
                'Facility': details.title,
                'Facility ID': details.facility_id,
                'Capacity': details.capacity,
                'Reservation Date': details.reservation_date,
                'Start Time': details.start_time,
                'End Time': details.end_time,
                'Participants': details.participants_count,
                'Purpose': details.purpose,
                'Status': item.status_name,
                'Remarks': item.raw_remarks,
            });
        }

        if (serviceKey === 'book-renewal') {
            return buildInfoGrid({
                'Book Title': details.title,
                'ISBN': details.isbn,
                'Accession Number': details.accession_number,
                'Current Due Date': formatDate(details.due_at),
                'Requested New Due Date': formatDate(details.new_due_at),
                'Status': item.status_name,
                'Reason / Remarks': item.raw_remarks,
            });
        }

        if (serviceKey === 'book-return') {
            const proofPath = details.proof_path
                ? `/storage/${details.proof_path}`
                : null;

            return `
                ${buildInfoGrid({
                    'Book Title': details.title,
                    'ISBN': details.isbn,
                    'Accession Number': details.accession_number,
                    'Due Date': formatDate(details.due_at),
                    'Returned At': formatDate(details.returned_at),
                    'Material Condition': details.material_condition,
                    'Status': item.status_name,
                    'Remarks': item.raw_remarks,
                })}

                ${
                    proofPath
                        ? `
                            <div class="admin-service-proof-link">
                                <a href="${escapeHtml(proofPath)}" target="_blank" rel="noopener">
                                    <i class="bi bi-paperclip"></i>
                                    View proof of return
                                </a>
                            </div>
                        `
                        : ''
                }
            `;
        }

        if (serviceKey === 'referral-letter') {
            return buildInfoGrid({
                'Destination Library': details.title,
                'Material Needed': details.material_needed,
                'Purpose': details.purpose,
                'Approved Letter Path': details.approved_letter_path,
                'Status': item.status_name,
                'Remarks': item.raw_remarks,
            });
        }

        return buildInfoGrid(details);
    }

    function renderView(item) {
        if (!viewOutput) {
            return;
        }

        const icon = serviceIcons[item.service_key] || 'bi-inbox';

        viewOutput.innerHTML = `
            <article class="admin-panel-preview-card">
                <div class="admin-service-panel-heading">
                    <div class="admin-service-panel-icon">
                        <i class="bi ${escapeHtml(icon)}"></i>
                    </div>

                    <div>
                        <span class="admin-eyebrow">${escapeHtml(item.service_label)}</span>
                        <h3>${escapeHtml(item.title || 'Untitled Request')}</h3>
                        <p>${escapeHtml(item.subtitle || 'No additional details.')}</p>
                    </div>
                </div>

                <div class="admin-panel-preview-grid">
                    <div>
                        <span>Requester</span>
                        <strong>${escapeHtml(item.requester_name)}</strong>
                    </div>

                    <div>
                        <span>Email</span>
                        <strong>${escapeHtml(item.requester_email)}</strong>
                    </div>

                    <div>
                        <span>Status</span>
                        <strong>${escapeHtml(item.status_name)}</strong>
                    </div>

                    <div>
                        <span>Requested At</span>
                        <strong>${escapeHtml(formatDate(item.requested_at))}</strong>
                    </div>
                </div>

                <section class="admin-service-panel-section">
                    <h3>Request Details</h3>

                    <div class="admin-panel-preview-grid">
                        ${renderMainDetails(item)}
                    </div>
                </section>

                ${renderAvailability(item)}

                ${renderUserContext(item)}
            </article>
        `;
    }

    function openView(serviceKey, requestId) {
        if (!setCurrent(serviceKey, requestId, 'view')) {
            return;
        }

        setSection('view');
        renderView(currentState.item);

        setPanelTitle(
            currentState.item.service_label,
            'Review the full request, user history, credit score, and availability checks.'
        );

        if (panelFooter) {
            panelFooter.hidden = currentState.item.service_key !== 'book-return';
        }

        openPanel();
    }

    function getDecisionRoute(action, serviceKey, requestId) {
        return `/admin/services-management/${serviceKey}/${requestId}/${action}`;
    }

    function openDecision(serviceKey, requestId, action) {
        if (!setCurrent(serviceKey, requestId, action)) {
            return;
        }

        setSection('decision');

        const titleMap = {
            review: 'Mark Request for Review',
            approve: 'Approve Request',
            reject: 'Reject Request',
            'confirm-return': 'Confirm Book Return',
            'follow-up': 'Send Return Follow-up',
        };

        const messageMap = {
            review: 'The user will receive a notification that the request is being reviewed.',
            approve: 'The user will receive an approval notification.',
            reject: 'The user will receive a rejection notification. Remarks are required.',
            'confirm-return': 'This will confirm the book return and update the borrowing record.',
            'follow-up': 'Send a follow-up message to the user about this return request.',
        };

        const buttonMap = {
            review: 'Mark as Review',
            approve: 'Approve',
            reject: 'Reject',
            'confirm-return': 'Confirm Return',
            'follow-up': 'Send Follow-up',
        };

        if (decisionForm) {
            decisionForm.action = getDecisionRoute(action, serviceKey, requestId);
        }

        if (decisionTitle) {
            decisionTitle.textContent = titleMap[action] || 'Process Request';
        }

        if (decisionMessage) {
            decisionMessage.textContent = messageMap[action] || 'Add a message for the user.';
        }

        if (decisionSubmit) {
            decisionSubmit.textContent = buttonMap[action] || 'Submit Decision';
            decisionSubmit.className = action === 'reject' ? 'admin-danger-btn' : 'admin-primary-btn';
        }

        if (decisionRemarks) {
            decisionRemarks.value = '';
            decisionRemarks.required = action === 'reject' || action === 'follow-up';

            if (action === 'review') {
                decisionRemarks.placeholder = 'Example: Your request is now under review by library staff.';
            } else if (action === 'approve') {
                decisionRemarks.placeholder = 'Example: Your request has been approved. Please proceed with the next steps.';
            } else if (action === 'reject') {
                decisionRemarks.placeholder = 'Explain why this request was rejected.';
            } else if (action === 'confirm-return') {
                decisionRemarks.placeholder = 'Example: Your returned material has been verified.';
            } else if (action === 'follow-up') {
                decisionRemarks.placeholder = 'Write the follow-up message for the user.';
            }
        }

        if (decisionIcon) {
            decisionIcon.className = `bi ${
                action === 'reject'
                    ? 'bi-x-circle'
                    : action === 'follow-up'
                        ? 'bi-chat-dots'
                        : 'bi-clipboard-check'
            }`;
        }

        setPanelTitle(
            actionLabels[action] || 'Process Request',
            `${currentState.item.service_label} • ${currentState.item.requester_name}`
        );

        if (panelFooter) {
            panelFooter.hidden = currentState.item.service_key !== 'book-return';
        }

        openPanel();
    }

    function openPenalty() {
        if (!currentState.item) {
            return;
        }

        setSection('penalty');

        if (penaltyForm) {
            penaltyForm.reset();
            penaltyForm.action = getDecisionRoute('penalty', currentState.serviceKey, currentState.requestId);
        }

        setPanelTitle(
            'Add Manual Penalty',
            `${currentState.item.service_label} • ${currentState.item.requester_name}`
        );

        openPanel();
    }

    function openLetter(serviceKey, requestId) {
        if (!setCurrent(serviceKey, requestId, 'send-letter')) {
            return;
        }

        setSection('letter');

        if (letterForm) {
            letterForm.reset();
            letterForm.action = getDecisionRoute('send-letter', serviceKey, requestId);
        }

        setPanelTitle(
            'Send Referral Letter',
            `${currentState.item.service_label} • ${currentState.item.requester_name}`
        );

        if (panelFooter) {
            panelFooter.hidden = true;
        }

        openPanel();
    }

    function updateRequestData(item) {
        if (!item || !item.service_key || !item.id) {
            return;
        }

        const key = getRequestKey(item.service_key, item.id);
        requestMap.set(key, item);

        const row = document.querySelector(`[data-service-row="${key}"]`);

        if (!row) {
            return;
        }

        const statusBadge = row.querySelector('.admin-status');

        if (statusBadge) {
            statusBadge.textContent = item.status_name;
            statusBadge.classList.toggle(
                'active',
                ['approved', 'borrowed', 'returned'].includes(item.status_key)
            );
            statusBadge.classList.toggle(
                'inactive',
                !['approved', 'borrowed', 'returned'].includes(item.status_key)
            );
        }
    }

    async function submitForm(formElement, successCallback) {
        const formData = new FormData(formElement);

        try {
            const response = await fetch(formElement.action, {
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
                updateRequestData(data.item);
                currentState.item = data.item;
            }

            showAlert(data.message || 'Action completed successfully.');

            if (typeof successCallback === 'function') {
                successCallback(data);
            }
        } catch (error) {
            showAlert(error.message || 'Something went wrong. Please check the form.', 'error');

            if (error.errors) {
                console.error(error.errors);
            }
        }
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-service-action]');

        if (!button) {
            return;
        }

        const serviceKey = button.dataset.serviceKey;
        const requestId = button.dataset.requestId;
        const action = button.dataset.serviceAction;

        if (action === 'view') {
            openView(serviceKey, requestId);
            return;
        }

        if (action === 'send-letter') {
            openLetter(serviceKey, requestId);
            return;
        }

        openDecision(serviceKey, requestId, action);
    });

    if (decisionForm) {
        decisionForm.addEventListener('submit', function (event) {
            event.preventDefault();

            submitForm(decisionForm, function () {
                openView(currentState.serviceKey, currentState.requestId);
            });
        });
    }

    if (penaltyForm) {
        penaltyForm.addEventListener('submit', function (event) {
            event.preventDefault();

            submitForm(penaltyForm, function () {
                openView(currentState.serviceKey, currentState.requestId);
            });
        });
    }

    if (letterForm) {
        letterForm.addEventListener('submit', function (event) {
            event.preventDefault();

            submitForm(letterForm, function () {
                openView(currentState.serviceKey, currentState.requestId);
            });
        });
    }

    if (footerPenaltyButton) {
        footerPenaltyButton.addEventListener('click', openPenalty);
    }

    if (closeButton) {
        closeButton.addEventListener('click', closePanel);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && layout.classList.contains('is-panel-open')) {
            closePanel();
        }
    });
});