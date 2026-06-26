<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const routes = {!! $gtRoutesJson !!};
    const employees = {!! $employeePayloadJson !!};
    const taskOptions = {!! $taskSelectPayloadJson !!};

    let stepIndex = 0;
    let gtReasonResolver = null;
    let pendingMoveCard = null;
    let pendingMoveOldZone = null;
    let draggedStepRow = null;
    let draggedCard = null;
    let draggedCardOriginZone = null;

    const qs = (selector, root = document) => root.querySelector(selector);
    const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));
    const setText = (selector, value) => { const el = qs(selector); if (el) el.textContent = value; };
    const setHtml = (selector, value) => { const el = qs(selector); if (el) el.innerHTML = value; };
    const setValue = (selector, value) => { const el = qs(selector); if (el) el.value = value; };

    function esc(value) {
        return String(value ?? '').replace(/[&<>'"]/g, char => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
        }[char]));
    }

    function hoursLabel(hours) {
        const n = Number(hours || 0);
        if (!n) return 'Optional';
        return n.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' h';
    }

    function minutesLabel(minutes) {
        const n = Number(minutes || 0);
        if (!n) return 'Optional';
        return (n / 60).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' h';
    }

    window.gtOpenModal = function (id) {
        qs('#' + id)?.classList.add('open');
    };

    window.gtCloseModal = function (id) {
        qs('#' + id)?.classList.remove('open');
    };

    window.gtToast = function (kind, title, message) {
        const wrap = qs('#gtToastWrap');
        if (!wrap) return;

        const el = document.createElement('div');
        el.className = 'gt-toast';
        el.innerHTML = `
            <div class="gt-toast-ic ${kind || 'info'}">${kind === 'ok' ? '✓' : kind === 'bad' ? '!' : 'i'}</div>
            <div style="flex:1">
                <p class="gt-toast-ttl">${esc(title || 'Info')}</p>
                <p class="gt-toast-msg">${esc(message || '')}</p>
            </div>
            <button class="gt-toast-x" type="button" onclick="this.parentElement.remove()">×</button>
        `;
        wrap.appendChild(el);
        setTimeout(() => { try { el.remove(); } catch (e) {} }, 5200);
    };

    async function gtJson(url, options = {}) {
        const headers = {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(options.headers || {}),
        };

        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options,
            headers,
        });

        const data = await response.json().catch(() => ({ message: 'Serverantwort konnte nicht gelesen werden.' }));
        if (!response.ok) throw new Error(data.message || 'Aktion fehlgeschlagen.');
        return data;
    }

    function initSelect2(scope = document) {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;

        jQuery(scope).find('.gt-select2').each(function () {
            const $select = jQuery(this);
            if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
            const parentModal = $select.closest('.gt-modal-backdrop');
            $select.select2({
                width: '100%',
                placeholder: $select.data('placeholder') || 'Bitte auswählen',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: parentModal.length ? parentModal : jQuery(document.body),
            });
        });
    }

    function selectedTaskMode() {
        return qs('input[name="task_mode"]:checked')?.value || 'single';
    }

    function setTaskMode(mode) {
        const isBulk = mode === 'bulk';
        qs('#gtSingleAssigneeSection')?.classList.toggle('is-hidden', isBulk);
        qs('#gtStepBuilder')?.classList.toggle('active', isBulk);
        qsa('[data-assignee-checkbox]').forEach(cb => cb.disabled = isBulk);

        if (isBulk && !qs('[data-step-row]')) addStepRow();
    }

    function employeeOptions(selected = []) {
        const selectedStrings = (selected || []).map(String);
        return employees.map(employee => {
            const isSelected = selectedStrings.includes(String(employee.id)) ? 'selected' : '';
            return `<option value="${esc(employee.id)}" ${isSelected}>${esc(employee.name)}</option>`;
        }).join('');
    }

    function stepRowHtml(i, data = {}) {
        const selected = data.assignee_ids || [];
        const planned = data.planned_hours ?? data.soll_hours ?? '';
        const actual = data.actual_hours ?? data.ist_hours ?? '';
        const title = data.title || '';
        const description = data.description || '';
        const dueAt = data.due_at || '';
        const overdueClass = (!data.is_done && (data.is_overdue || isPastDateTime(dueAt))) ? ' is-step-overdue' : '';

        return `
            <div class="gt-step-row${overdueClass}" data-step-row data-step-index="${i}" draggable="true">
                <input type="hidden" name="steps[${i}][id]" value="${esc(data.id || '')}">
                <input type="hidden" name="steps[${i}][sort_order]" value="${i + 1}">

                <div class="gt-step-row-head">
                    <div class="gt-step-head-left">
                        <button type="button" class="gt-step-drag-btn" data-step-drag-handle title="Schritt per Drag & Drop sortieren">
                            <i data-lucide="grip-vertical" style="width:16px;height:16px"></i>
                        </button>
                        <button type="button" class="gt-step-collapse-btn" data-collapse-step title="Schritt einklappen/ausklappen">
                            <i data-lucide="chevron-down" style="width:16px;height:16px"></i>
                        </button>
                        <div style="min-width:0">
                            <div class="gt-step-row-title">Schritt ${i + 1}</div>
                            <div class="gt-step-row-summary" data-step-summary>${esc(title || 'Noch kein Titel')}</div>
                        </div>
                    </div>
                    <div class="gt-step-row-actions">
                        <button type="button" class="gt-btn-ic danger" data-remove-step title="Schritt entfernen">
                            <i data-lucide="trash-2" style="width:15px;height:15px"></i>
                        </button>
                    </div>
                </div>

                <div class="gt-step-row-body">
                    <div class="gt-step-grid">
                        <div>
                            <label class="gt-label">Schritt-Titel *</label>
                            <input class="gt-input" name="steps[${i}][title]" value="${esc(title)}" placeholder="z.B. Kunde anrufen" data-step-title-input>
                        </div>
                        <div>
                            <label class="gt-label">Fällig bis</label>
                            <input class="gt-input" type="datetime-local" name="steps[${i}][due_at]" value="${esc(dueAt)}">
                            <div class="gt-step-time-help">Eigene Frist für diesen Schritt</div>
                        </div>
                        <div>
                            <label class="gt-label">Geplante Zeit</label>
                            <input class="gt-input" type="number" step="0.25" min="0" name="steps[${i}][planned_hours]" value="${esc(planned)}" placeholder="Optional">
                            <div class="gt-step-time-help">Optional, in Stunden</div>
                        </div>
                        <div>
                            <label class="gt-label">Tatsächliche Zeit</label>
                            <input class="gt-input" type="number" step="0.25" min="0" name="steps[${i}][actual_hours]" value="${esc(actual)}" placeholder="Optional">
                            <div class="gt-step-time-help">Optional, in Stunden</div>
                        </div>
                        <div class="full">
                            <label class="gt-label">Beschreibung</label>
                            <textarea class="gt-textarea" name="steps[${i}][description]" placeholder="Was muss in diesem Schritt gemacht werden?">${esc(description)}</textarea>
                        </div>
                        <div class="full">
                            <label class="gt-label">Verantwortliche Mitarbeiter</label>
                            <select class="gt-select gt-select2" name="steps[${i}][assignee_ids][]" multiple data-placeholder="Mitarbeiter suchen">
                                ${employeeOptions(selected)}
                            </select>
                            <div class="gt-help">Bei Bulk Aufgaben werden Mitarbeiter nur in den jeweiligen Schritten gewählt.</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function addStepRow(data = {}) {
        const container = qs('#gtStepsContainer');
        if (!container) return;
        container.insertAdjacentHTML('beforeend', stepRowHtml(stepIndex, data));
        stepIndex += 1;
        refreshStepOrder();
        initSelect2(container);
        if (window.lucide) window.lucide.createIcons();
    }

    function refreshStepOrder() {
        const container = qs('#gtStepsContainer');
        if (!container) return;

        qsa('[data-step-row]', container).forEach((row, index) => {
            row.dataset.stepIndex = String(index);

            const orderInput = qs('input[name$="[sort_order]"]', row);
            if (orderInput) {
                orderInput.name = `steps[${index}][sort_order]`;
                orderInput.value = String(index + 1);
            }

            qsa('[name]', row).forEach(input => {
                input.name = input.name.replace(/steps\[\d+\]/, `steps[${index}]`);
            });

            const title = qs('.gt-step-row-title', row);
            if (title) title.textContent = `Schritt ${index + 1}`;
        });
    }

    function getStepDragAfterElement(container, y) {
        const rows = qsa('[data-step-row]:not(.step-dragging)', container);

        return rows.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset, element: child };
            }

            return closest;
        }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
    }

    function getTaskDragAfterElement(zone, y) {
        const cards = qsa('.gt-task:not(.dragging)', zone);

        return cards.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset, element: child };
            }

            return closest;
        }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
    }

    async function saveTaskOrder(zone, silent = false) {
        if (!zone || !routes.reorder) return;

        const orderedTaskIds = qsa('.gt-task', zone).map(card => card.dataset.taskId).filter(Boolean);

        try {
            zone.classList.add('order-saving');
            await gtJson(routes.reorder, {
                method: 'POST',
                body: JSON.stringify({
                    status: zone.dataset.status,
                    ordered_task_ids: orderedTaskIds,
                })
            });

            if (!silent) gtToast('ok', 'Reihenfolge gespeichert', 'Die Karten-Reihenfolge wurde gespeichert.');
        } catch (error) {
            if (!silent) gtToast('bad', 'Reihenfolge nicht gespeichert', error.message);
        } finally {
            zone.classList.remove('order-saving');
        }
    }

    function resetSteps() {
        stepIndex = 0;
        if (qs('#gtStepsContainer')) qs('#gtStepsContainer').innerHTML = '';
    }

    function loadTaskSteps(taskId) {
        const json = qs('#gtTaskStepsJson-' + taskId);
        if (!json) return [];
        try { return JSON.parse(json.textContent || '[]'); } catch (e) { return []; }
    }

    function isPastDateTime(value) {
        if (!value) return false;
        const dt = new Date(String(value).replace(' ', 'T'));
        return !Number.isNaN(dt.getTime()) && dt.getTime() < Date.now();
    }

    function dateTimeLabel(value) {
        if (!value) return '';
        const dt = new Date(String(value).replace(' ', 'T'));
        if (Number.isNaN(dt.getTime())) return value;
        return dt.toLocaleString('de-DE', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
    }

    function toggleRecurrenceSettings() {
        const checkbox = qs('#gtIsRecurring');
        const settings = qs('#gtRecurrenceSettings');
        const frequency = qs('#gtRecurrenceFrequency');
        const weekdayWrap = qs('#gtRecurrenceWeekdayWrap');
        if (settings && checkbox) settings.hidden = !checkbox.checked;
        if (weekdayWrap && frequency) weekdayWrap.style.display = ['weekly', 'biweekly'].includes(frequency.value) ? '' : 'none';
    }

    window.gtOpenCreateTask = function () {
        const form = qs('#gtTaskForm');
        qs('#gtTaskModalTitle').textContent = 'Neue Aufgabe erstellen';
        form.reset();
        form.action = routes.store;
        qs('#gtTaskMethod').value = 'POST';
        qs('#gtTaskId').value = '';
        if (qs('#gtChangeReasonWrap')) qs('#gtChangeReasonWrap').hidden = true;
        if (qs('#gtTaskModeSingle')) qs('#gtTaskModeSingle').checked = true;
        if (qs('#gtShowDueDateTime')) qs('#gtShowDueDateTime').checked = true;
        if (qs('#gtIsRecurring')) qs('#gtIsRecurring').checked = false;
        resetSteps();
        setTaskMode('single');
        toggleRecurrenceSettings();
        if (window.jQuery) jQuery('#gtDependencyParentIds').val(null).trigger('change');
        gtOpenModal('gtTaskModal');
        setTimeout(() => initSelect2(qs('#gtTaskModal')), 70);
        if (window.lucide) window.lucide.createIcons();
    };

    window.gtOpenEditTask = function (button) {
        const card = button.closest('.gt-task');
        if (!card) return;
        const d = card.dataset;
        const form = qs('#gtTaskForm');

        qs('#gtTaskModalTitle').textContent = 'Aufgabe bearbeiten';
        form.reset();
        form.action = routes.updateBase + '/' + d.taskId;
        qs('#gtTaskMethod').value = 'PUT';
        qs('#gtTaskId').value = d.taskId || '';
        qs('#gtTitle').value = d.title || '';
        qs('#gtDescription').value = d.description || '';
        qs('#gtPriority').value = d.priority || 'normal';
        qs('#gtVisibility').value = d.visibility || 'all';
        qs('#gtDepartment').value = d.departmentId || '';
        qs('#gtDueAt').value = d.dueAtLocal || '';
        if (qs('#gtPlannedHoursToday')) qs('#gtPlannedHoursToday').value = d.plannedHoursToday || '';
        qs('#gtShowDueDateTime').checked = ['1','true','yes'].includes(String(d.showDueDatetime || '1').toLowerCase());
        qs('#gtIsRecurring').checked = ['1','true','yes'].includes(String(d.isRecurring || '').toLowerCase());
        qs('#gtRecurrenceFrequency').value = d.recurrenceFrequency || 'weekly';
        qs('#gtRecurrenceWeekday').value = d.recurrenceWeekday || '';
        qs('#gtRecurrenceEndsAt').value = d.recurrenceEndsAtLocal || '';
        if (qs('#gtChangeReasonWrap')) qs('#gtChangeReasonWrap').hidden = false;
        if (qs('#gtChangeReason')) qs('#gtChangeReason').required = true;

        const mode = d.taskMode || 'single';
        if (mode === 'bulk') qs('#gtTaskModeBulk').checked = true; else qs('#gtTaskModeSingle').checked = true;

        qsa('[data-assignee-checkbox]').forEach(cb => {
            cb.checked = (d.assigneeIds || '').split(',').includes(cb.value);
        });

        resetSteps();
        loadTaskSteps(d.taskId).forEach(step => addStepRow(step));
        setTaskMode(mode);
        toggleRecurrenceSettings();
        gtOpenModal('gtTaskModal');

        setTimeout(() => {
            initSelect2(qs('#gtTaskModal'));
            if (window.jQuery) jQuery('#gtDependencyParentIds').val((d.dependencyParentIds || '').split(',').filter(Boolean)).trigger('change');
        }, 70);
        if (window.lucide) window.lucide.createIcons();
    };

    function askReason(title, subtitle) {
        return new Promise(resolve => {
            gtReasonResolver = resolve;
            qs('#gtReasonTitle').textContent = title || 'Grund';
            qs('#gtReasonSubtitle').textContent = subtitle || 'Bitte gib eine kurze Notiz ein.';
            qs('#gtReasonText').value = '';
            gtOpenModal('gtReasonModal');
            setTimeout(() => qs('#gtReasonText')?.focus(), 80);
        });
    }

    function setDrawerTab(tab) {
        const selected = tab || 'team';
        qsa('[data-gt-drawer-tab]').forEach(btn => btn.classList.toggle('active', btn.dataset.gtDrawerTab === selected));
        qsa('[data-gt-drawer-panel]').forEach(panel => panel.classList.toggle('active', panel.dataset.gtDrawerPanel === selected));
    }

    window.gtOpenTeamSidebar = function (tab = 'team') {
        qs('#gtTeamDrawer')?.classList.add('open');
        qs('#gtSidebarOverlay')?.classList.add('open');
        qs('#gtTeamDrawer')?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('gt-drawer-lock');
        setDrawerTab(tab);
    };

    window.gtCloseTeamSidebar = function () {
        qs('#gtTeamDrawer')?.classList.remove('open');
        qs('#gtSidebarOverlay')?.classList.remove('open');
        qs('#gtTeamDrawer')?.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('gt-drawer-lock');
    };

    window.gtOpenTaskInfo = function (button) {
        const card = button.closest('.gt-task');
        if (!card) return;

        const modal = qs('#gtTaskDetailModal');
        if (!modal) {
            gtToast('bad', 'Detail-Modal fehlt', 'Bitte kopiere auch partials/task-detail-modal.blade.php und include es im index.');
            return;
        }

        const d = card.dataset;
        const steps = loadTaskSteps(d.taskId);
        const doneCount = steps.filter(step => step.is_done).length;
        const progress = Number(d.progressPercent || (steps.length ? Math.round(doneCount / steps.length * 100) : 0));
        const statusLabel = d.status || card.closest('.gt-dropzone')?.dataset.status || '—';

        setText('#gtDetailTitle', d.title || 'Aufgabe');
        setText('#gtDetailDescription', d.description || 'Keine Beschreibung vorhanden.');
        setText('#gtDetailStatus', statusLabel);
        setText('#gtDetailMode', (d.taskMode || 'single') === 'bulk' ? 'Bulk / Schritte' : 'Single');
        setText('#gtDetailPlanned', minutesLabel(d.plannedMinutes || d.sollMinutes || 0));
        setText('#gtDetailActual', minutesLabel(d.actualMinutes || d.istMinutes || 0));
        setText('#gtDetailProgressText', progress + '%');
        const fill = qs('#gtDetailProgressFill');
        if (fill) fill.style.width = Math.max(0, Math.min(100, progress)) + '%';

        const list = qs('#gtDetailSteps');
        if (list) {
            if (!steps.length) {
                list.innerHTML = '<div class="gt-detail-empty">Für diese Aufgabe wurden noch keine Schritte erstellt.</div>';
            } else {
                list.innerHTML = steps.map(step => {
                    const assignees = (step.assignee_names || []).join(', ') || 'Keine Mitarbeiter gewählt';
                    const status = step.is_done
                        ? `Erledigt${step.checked_by_name ? ' von ' + esc(step.checked_by_name) : ''}${step.checked_at ? ' am ' + esc(step.checked_at) : ''}`
                        : 'Offen';
                    const dueLabel = step.due_at_label || dateTimeLabel(step.due_at);
                    const overdue = !step.is_done && (step.is_overdue || isPastDateTime(step.due_at));
                    return `
                        <div class="gt-detail-step ${overdue ? 'is-step-overdue' : ''}">
                            <div class="gt-detail-step-head">
                                <div>
                                    <div class="gt-detail-step-title">${esc(step.title || 'Schritt')}</div>
                                    <div class="gt-detail-step-meta">${esc(status)} · Verantwortlich: ${esc(assignees)}${dueLabel ? ' · Fällig: ' + esc(dueLabel) : ''}${overdue ? ' · Überfällig' : ''}</div>
                                </div>
                                <button type="button" class="gt-detail-check-btn ${step.is_done ? 'undo' : ''}" data-step-toggle-modal data-task-id="${esc(d.taskId)}" data-step-id="${esc(step.id)}" data-step-title="${esc(step.title || 'Schritt')}" data-next-done="${step.is_done ? 0 : 1}">
                                    <i data-lucide="${step.is_done ? 'rotate-ccw' : 'check'}" style="width:15px;height:15px"></i>
                                    ${step.is_done ? 'Wieder öffnen' : 'Erledigen'}
                                </button>
                            </div>
                            <div class="gt-detail-step-body">
                                ${step.description ? esc(step.description) : '<span class="gt-person-meta">Keine Beschreibung.</span>'}
                                <div class="gt-person-meta" style="margin-top:8px">Geplante Zeit: ${hoursLabel(step.planned_hours)} · Tatsächliche Zeit: ${hoursLabel(step.actual_hours)}</div>
                            </div>
                        </div>`;
                }).join('');
            }
        }

        const editBtn = qs('#gtDetailEditBtn');
        if (editBtn) editBtn.onclick = () => {
            gtCloseModal('gtTaskDetailModal');
            const editTrigger = card.querySelector('[onclick*="gtOpenEditTask"]') || card;
            gtOpenEditTask(editTrigger);
        };

        const reportBtn = qs('#gtDetailReportBtn');
        if (reportBtn) reportBtn.onclick = () => {
            gtCloseModal('gtTaskDetailModal');
            const reportTrigger = card.querySelector('[onclick*="gtOpenReport"]') || card;
            gtOpenReport(reportTrigger);
        };

        gtOpenModal('gtTaskDetailModal');
        if (window.lucide) window.lucide.createIcons();
    };

    function openStepCheckModal(button) {
        qs('#gtStepCheckTaskId').value = button.dataset.taskId || '';
        qs('#gtStepCheckStepId').value = button.dataset.stepId || '';
        qs('#gtStepCheckDone').value = button.dataset.nextDone || '1';
        qs('#gtStepCheckReason').value = '';
        qs('#gtStepCheckTitle').textContent = button.dataset.nextDone === '1' ? 'Schritt als erledigt markieren' : 'Schritt wieder öffnen';
        qs('#gtStepCheckSubtitle').textContent = (button.dataset.stepTitle || 'Schritt') + ' — Mitarbeiter und Uhrzeit werden automatisch gespeichert.';
        gtOpenModal('gtStepCheckModal');
    }

    async function saveStepCheck() {
        const taskId = qs('#gtStepCheckTaskId').value;
        const stepId = qs('#gtStepCheckStepId').value;
        const isDone = qs('#gtStepCheckDone').value === '1';
        const reason = qs('#gtStepCheckReason').value.trim();
        if (!reason) { gtToast('bad', 'Notiz fehlt', 'Bitte gib eine kurze Notiz ein.'); return; }

        try {
            await gtJson(routes.stepsBase + '/' + taskId + '/steps/' + stepId + '/toggle', {
                method: 'POST',
                body: JSON.stringify({ is_done: isDone, reason: reason })
            });
            gtToast('ok', 'Gespeichert', 'Schritt wurde aktualisiert.');
            window.location.reload();
        } catch (error) {
            gtToast('bad', 'Fehler', error.message);
        }
    }

    function openMoveStatusModal(card, status) {
        pendingMoveCard = card;
        pendingMoveOldZone = card.closest('.gt-dropzone');
        const steps = loadTaskSteps(card.dataset.taskId);
        qs('#gtMoveTaskId').value = card.dataset.taskId;
        qs('#gtMoveTargetStatus').value = status;
        qs('#gtMoveTaskTitle').textContent = card.dataset.title || 'Aufgabe';
        qs('#gtMoveStatusLabel').textContent = 'Neuer Status: ' + status;
        qs('#gtMoveReportText').value = '';

        const list = qs('#gtMoveStepList');
        list.innerHTML = steps.length ? steps.map(step => {
            const dueLabel = step.due_at_label || dateTimeLabel(step.due_at);
            const overdue = !step.is_done && (step.is_overdue || isPastDateTime(step.due_at));
            return `
            <label class="gt-move-step-row ${overdue ? 'is-step-overdue' : ''}">
                <input type="checkbox" value="${esc(step.id)}" ${step.is_done ? 'checked disabled' : ''} data-move-step-checkbox>
                <span>
                    <span class="gt-move-step-title">${esc(step.title || 'Schritt')}</span>
                    <span class="gt-move-step-meta">${step.is_done ? 'Bereits erledigt' : 'Als erledigt markieren, wenn dieser Schritt durch den Statuswechsel fertig ist'}${dueLabel ? ' · Fällig: ' + esc(dueLabel) : ''}${overdue ? ' · Überfällig' : ''}</span>
                </span>
            </label>`;
        }).join('') : '<div class="gt-detail-empty">Keine Schritte vorhanden. Bericht ist optional.</div>';

        gtOpenModal('gtMoveStatusModal');
        if (window.lucide) window.lucide.createIcons();
    }

    window.gtCancelMoveStatus = function () {
        pendingMoveCard = null;
        pendingMoveOldZone = null;
        gtCloseModal('gtMoveStatusModal');
    };

    async function confirmMoveStatus() {
        if (!pendingMoveCard) return;
        const card = pendingMoveCard;
        const status = qs('#gtMoveTargetStatus').value;
        const report = qs('#gtMoveReportText').value.trim();
        const stepUpdates = qsa('[data-move-step-checkbox]:checked:not(:disabled)').map(input => ({ id: input.value, is_done: true }));
        const oldZone = pendingMoveOldZone;
        const newZone = qs(`.gt-dropzone[data-status="${status}"]`);

        try {
            newZone?.appendChild(card);
            removeEmpty(status);
            ensureEmpty(oldZone);
            updateCounts();

            await gtJson(routes.move, {
                method: 'POST',
                body: JSON.stringify({
                    task_id: card.dataset.taskId,
                    status: status,
                    report_body: report || null,
                    change_reason: report || null,
                    step_checks: stepUpdates,
                })
            });

            await saveTaskOrder(newZone, true);
            gtToast('ok', 'Verschoben', 'Aufgabe wurde verschoben.');
            gtCloseModal('gtMoveStatusModal');
            window.location.reload();
        } catch (error) {
            gtToast('bad', 'Fehler', error.message);
            window.location.reload();
        }
    }

    function removeEmpty(status) { qs(`.gt-empty[data-empty="${status}"]`)?.remove(); }
    function ensureEmpty(zone) {
        if (!zone || zone.querySelector('.gt-task') || zone.querySelector('.gt-empty')) return;
        const empty = document.createElement('div');
        empty.className = 'gt-empty';
        empty.dataset.empty = zone.dataset.status;
        empty.textContent = 'Keine Aufgaben';
        zone.appendChild(empty);
    }
    function updateCounts() {
        qsa('.gt-dropzone').forEach(zone => {
            const count = zone.querySelectorAll('.gt-task').length;
            const counter = qs(`[data-column-count="${zone.dataset.status}"]`);
            const stat = qs(`[data-stat="${zone.dataset.status}"]`);
            if (counter) counter.textContent = count;
            if (stat) stat.textContent = count;
        });
    }

    window.gtOpenReport = async function (button) {
        const card = button.closest('.gt-task');
        const taskId = card?.dataset.taskId;
        if (!taskId) return;
        qs('#gtReportTaskId').value = taskId;
        qs('#gtReportForm').reset();
        gtOpenModal('gtReportModal');

        try {
            const data = await gtJson(routes.reportBase + '/' + taskId + '/reports', { method: 'GET', headers: { 'Accept': 'application/json' } });
            qs('#gtReportHistory').innerHTML = (data.reports || []).map(r => `
                <div class="gt-comment">
                    <div class="gt-comment-head"><strong>${esc(r.employee_name || 'Mitarbeiter')}</strong><span>${esc(r.created_at || '')}</span></div>
                    <div class="gt-comment-body">${esc(r.body || '')}</div>
                </div>`).join('') || 'Noch keine Berichte vorhanden.';
        } catch (e) {
            qs('#gtReportHistory').textContent = 'Berichte konnten nicht geladen werden.';
        }
    };

    window.gtArchiveTask = async function (button) {
        const taskId = button.dataset.taskId || button.closest('.gt-task')?.dataset.taskId;
        if (!taskId) return;
        const reason = await askReason('Aufgabe archivieren', 'Optional kannst du kurz schreiben, warum die Aufgabe archiviert wird.');
        if (reason === null) return;
        try {
            await gtJson((routes.archiveBase || routes.updateBase) + '/' + taskId + '/archive', {
                method: 'POST',
                body: JSON.stringify({ reason: reason || null })
            });
            gtToast('ok', 'Archiviert', 'Aufgabe wurde archiviert.');
            window.location.reload();
        } catch (error) { gtToast('bad', 'Fehler', error.message); }
    };


    window.gtDeleteTask = async function (button) {
        const taskId = button.dataset.taskId || button.closest('.gt-task')?.dataset.taskId;
        if (!taskId) return;

        const reason = await askReason('Aufgabe löschen', 'Optional kannst du kurz schreiben, warum diese Aufgabe gelöscht wird. Die Aufgabe wird in den Papierkorb verschoben und kann über gelöschte/archivierte Aufgaben wiederhergestellt werden.');
        if (reason === null) return;

        try {
            await gtJson((routes.deleteBase || routes.updateBase) + '/' + taskId, {
                method: 'DELETE',
                body: JSON.stringify({ change_reason: reason || null, reason: reason || null })
            });

            const card = button.closest('.gt-task');
            const zone = card?.closest('.gt-dropzone');
            if (card) card.remove();
            if (zone) {
                const count = zone.querySelectorAll('.gt-task').length;
                const counter = qs(`[data-column-count="${zone.dataset.status}"]`);
                const stat = qs(`[data-stat="${zone.dataset.status}"]`);
                if (counter) counter.textContent = count;
                if (stat) stat.textContent = count;
                if (!count && !zone.querySelector('.gt-empty')) {
                    const empty = document.createElement('div');
                    empty.className = 'gt-empty';
                    empty.textContent = 'Keine Aufgaben';
                    zone.appendChild(empty);
                }
            }

            gtToast('ok', 'Gelöscht', 'Aufgabe wurde in den Papierkorb verschoben.');
            if (window.lucide) window.lucide.createIcons();
        } catch (error) {
            gtToast('bad', 'Fehler', error.message);
        }
    };

    window.gtRestoreTask = async function (button) {
        const reason = await askReason('Aufgabe wiederherstellen', 'Warum wird diese Aufgabe wiederhergestellt?');
        if (reason === null) return;
        try {
            await gtJson(routes.updateBase + '/' + button.dataset.taskId + '/restore', {
                method: 'POST',
                body: JSON.stringify({ reason: reason || null })
            });
            gtToast('ok', 'Wiederhergestellt', 'Die Aufgabe wurde wiederhergestellt.');
            window.location.reload();
        } catch (error) { gtToast('bad', 'Fehler', error.message); }
    };

    function drawGanttDependencies() {
        const inner = qs('#gtGanttProInner') || qs('.gt-gantt-stage');
        const svg = qs('#gtGanttLinkSvg') || qs('.gt-gantt-dependency-svg');
        if (!inner || !svg) return;

        const innerRect = inner.getBoundingClientRect();
        const bars = qsa('.gt-gantt-bar-pro[data-parent-ids]', inner);
        const width = inner.scrollWidth;
        const height = inner.scrollHeight;
        svg.setAttribute('width', width);
        svg.setAttribute('height', height);
        svg.setAttribute('viewBox', `0 0 ${Math.max(1, width - 360)} ${Math.max(1, height)}`);
        svg.innerHTML = '<defs><marker id="gtGanttArrowPro" markerWidth="10" markerHeight="10" refX="8" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L0,8 L9,4 z" fill="#ef4444" /></marker></defs>';

        bars.forEach(childBar => {
            (childBar.dataset.parentIds || '').split(',').filter(Boolean).forEach(parentId => {
                const parentBar = qs(`.gt-gantt-bar-pro[data-gantt-task-id="${CSS.escape(parentId)}"]`, inner);
                if (!parentBar) return;

                const pr = parentBar.getBoundingClientRect();
                const cr = childBar.getBoundingClientRect();

                const x1 = pr.right - innerRect.left - 360;
                const y1 = pr.top + pr.height / 2 - innerRect.top;
                const x2 = cr.left - innerRect.left - 360;
                const y2 = cr.top + cr.height / 2 - innerRect.top;
                const midX = Math.max(x1 + 24, (x1 + x2) / 2);

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('class', 'gt-gantt-link-path');
                path.setAttribute('d', `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`);
                svg.appendChild(path);

                const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                dot.setAttribute('class', 'gt-gantt-link-dot');
                dot.setAttribute('cx', x2);
                dot.setAttribute('cy', y2);
                dot.setAttribute('r', '4');
                svg.appendChild(dot);
            });
        });
    }

    window.gtDrawGanttDependencies = drawGanttDependencies;

    document.addEventListener('click', event => {
        if (event.target.classList.contains('gt-modal-backdrop')) event.target.classList.remove('open');

        const sidebarOpenBtn = event.target.closest('[data-gt-sidebar-open]');
        if (sidebarOpenBtn) gtOpenTeamSidebar(sidebarOpenBtn.dataset.gtSidebarOpen || 'team');
        if (event.target.closest('[data-gt-sidebar-close]')) gtCloseTeamSidebar();

        const drawerTab = event.target.closest('[data-gt-drawer-tab]');
        if (drawerTab) setDrawerTab(drawerTab.dataset.gtDrawerTab);

        const tab = event.target.closest('[data-view-tab]');
        if (tab) {
            qsa('[data-view-tab]').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            qsa('.gt-view').forEach(view => view.classList.remove('active'));
            const viewId = 'gt' + tab.dataset.viewTab.charAt(0).toUpperCase() + tab.dataset.viewTab.slice(1) + 'View';
            qs('#' + viewId)?.classList.add('active');
            if (tab.dataset.viewTab === 'gantt') setTimeout(drawGanttDependencies, 80);
            if (tab.dataset.viewTab === 'org') setTimeout(drawOrgDependencies, 80);
        }

        if (event.target.closest('#gtAddStepBtn')) {
            if (qs('#gtTaskModeBulk')) qs('#gtTaskModeBulk').checked = true;
            setTaskMode('bulk');
            addStepRow();
        }

        const removeStep = event.target.closest('[data-remove-step]');
        if (removeStep) {
            removeStep.closest('[data-step-row]')?.remove();
            refreshStepOrder();
        }

        const collapseStep = event.target.closest('[data-collapse-step]');
        if (collapseStep) collapseStep.closest('[data-step-row]')?.classList.toggle('is-collapsed');

        const settingsToggle = event.target.closest('[data-settings-toggle]');
        if (settingsToggle) {
            const key = settingsToggle.dataset.settingsToggle;
            const body = qs(`[data-settings-body="${key}"]`);
            if (body) body.hidden = !body.hidden;
        }

        const stepButton = event.target.closest('[data-step-toggle-modal]');
        if (stepButton) openStepCheckModal(stepButton);

        if (event.target.closest('[data-step-check-cancel]')) gtCloseModal('gtStepCheckModal');
        if (event.target.closest('#gtStepCheckSaveBtn')) saveStepCheck();
        if (event.target.closest('#gtConfirmMoveStatusBtn')) confirmMoveStatus();

        if (event.target.closest('[data-gt-reason-cancel]')) {
            gtCloseModal('gtReasonModal');
            if (gtReasonResolver) { gtReasonResolver(null); gtReasonResolver = null; }
        }
        if (event.target.closest('[data-gt-reason-confirm]')) {
            const reason = qs('#gtReasonText').value.trim();
            gtCloseModal('gtReasonModal');
            if (gtReasonResolver) { gtReasonResolver(reason); gtReasonResolver = null; }
        }
    });

    document.addEventListener('input', event => {
        if (event.target.matches('[data-step-title-input]')) {
            const row = event.target.closest('[data-step-row]');
            const summary = qs('[data-step-summary]', row);
            if (summary) summary.textContent = event.target.value || 'Noch kein Titel';
        }
    });

    document.addEventListener('change', event => {
        if (event.target.matches('input[name="task_mode"]')) setTaskMode(event.target.value);
        if (event.target.id === 'gtIsRecurring' || event.target.id === 'gtRecurrenceFrequency') toggleRecurrenceSettings();
    });

    document.addEventListener('dragstart', event => {
        const stepRow = event.target.closest('[data-step-row]');
        if (stepRow) {
            if (event.target.closest('input, textarea, select, .select2-container') && !event.target.closest('[data-step-drag-handle]')) {
                event.preventDefault();
                return;
            }

            draggedStepRow = stepRow;
            stepRow.classList.add('step-dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', 'step:' + stepRow.dataset.stepIndex);
            return;
        }

        const card = event.target.closest('.gt-task');
        if (!card) return;

        draggedCard = card;
        draggedCardOriginZone = card.closest('.gt-dropzone');
        card.classList.add('dragging');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', card.dataset.taskId);
    });

    document.addEventListener('dragover', event => {
        if (draggedStepRow) {
            const container = qs('#gtStepsContainer');
            if (!container || !container.contains(event.target)) return;

            event.preventDefault();
            const afterElement = getStepDragAfterElement(container, event.clientY);
            if (afterElement == null) {
                container.appendChild(draggedStepRow);
            } else {
                container.insertBefore(draggedStepRow, afterElement);
            }
            refreshStepOrder();
        }
    });

    document.addEventListener('dragend', () => {
        if (draggedStepRow) {
            draggedStepRow.classList.remove('step-dragging');
            draggedStepRow = null;
            refreshStepOrder();
        }

        draggedCard?.classList.remove('dragging');
        draggedCard = null;
        draggedCardOriginZone = null;
        qsa('.gt-dropzone').forEach(zone => zone.classList.remove('drag-over'));
    });

    qsa('.gt-dropzone').forEach(zone => {
        zone.addEventListener('dragover', event => {
            if (!draggedCard) return;
            event.preventDefault();
            zone.classList.add('drag-over');

            if (draggedCardOriginZone === zone) {
                const afterElement = getTaskDragAfterElement(zone, event.clientY);
                if (afterElement == null) {
                    zone.appendChild(draggedCard);
                } else {
                    zone.insertBefore(draggedCard, afterElement);
                }
            }
        });

        zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));

        zone.addEventListener('drop', event => {
            event.preventDefault();
            zone.classList.remove('drag-over');

            if (!draggedCard) return;

            const newStatus = zone.dataset.status;
            const oldZone = draggedCardOriginZone;

            if (!newStatus || !oldZone) return;

            if (oldZone === zone) {
                updateCounts();
                saveTaskOrder(zone);
                return;
            }

            openMoveStatusModal(draggedCard, newStatus);
        });
    });


    qs('#gtReportForm')?.addEventListener('submit', async event => {
        event.preventDefault();
        const taskId = qs('#gtReportTaskId').value;
        try {
            const data = await gtJson(routes.reportBase + '/' + taskId + '/reports', {
                method: 'POST',
                body: JSON.stringify({
                    type: qs('#gtReportType').value,
                    hours: qs('#gtReportHours').value,
                    body: qs('#gtReportText').value,
                })
            });
            gtToast('ok', 'Gespeichert', data.message || 'Bericht wurde gespeichert.');
            gtCloseModal('gtReportModal');
        } catch (error) { gtToast('bad', 'Fehler', error.message); }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            qsa('.gt-modal-backdrop.open').forEach(modal => modal.classList.remove('open'));
            gtCloseTeamSidebar();
        }
    });

    window.addEventListener('resize', () => { setTimeout(drawGanttDependencies, 120); setTimeout(drawOrgDependencies, 120); });
    qs('.gt-gantt-timeline-wrap')?.addEventListener('scroll', () => requestAnimationFrame(drawGanttDependencies));
    qs('.gt-gantt-pro-scroll')?.addEventListener('scroll', () => requestAnimationFrame(drawGanttDependencies));
    qs('#gtOrgStage')?.parentElement?.addEventListener('scroll', () => requestAnimationFrame(drawOrgDependencies));

    qsa('[data-gantt-zoom]').forEach(button => {
        button.addEventListener('click', () => {
            const body = qs('#gtGanttBody');
            const current = Number(body?.dataset.ganttZoomLevel || 1);
            const next = button.dataset.ganttZoom === 'in' ? Math.min(3, current + 0.25) : Math.max(0.75, current - 0.25);
            if (body) {
                body.dataset.ganttZoomLevel = String(next);
                body.style.fontSize = next + 'em';
            }
            setTimeout(drawGanttDependencies, 80);
        });
    });



    function priorityRank(priority) {
        return ({ urgent: 1, important: 2, normal: 3, low: 4 }[priority || 'normal'] || 5);
    }

    function removeEmptyPlaceholder(zone) {
        zone?.querySelector('.gt-empty')?.remove();
    }

    function ensureEmptyPlaceholder(zone) {
        if (!zone) return;
        if (!zone.querySelector('.gt-task') && !zone.querySelector('.gt-empty')) {
            const empty = document.createElement('div');
            empty.className = 'gt-empty';
            empty.textContent = 'Keine Aufgaben';
            zone.appendChild(empty);
        }
    }

    function insertCardByPriority(card, zone) {
        if (!card || !zone) return;

        removeEmptyPlaceholder(zone);

        const cardRank = priorityRank(card.dataset.priority);
        const cardOrder = Number(card.dataset.sortOrder || 999999);
        const cardDue = card.dataset.dueAtLocal || '9999-12-31T23:59';
        const cards = qsa('.gt-task', zone).filter(existing => existing !== card);

        const before = cards.find(existing => {
            const existingRank = priorityRank(existing.dataset.priority);
            const existingOrder = Number(existing.dataset.sortOrder || 999999);
            const existingDue = existing.dataset.dueAtLocal || '9999-12-31T23:59';

            if (cardRank !== existingRank) return cardRank < existingRank;
            if (cardOrder !== existingOrder) return cardOrder < existingOrder;
            return String(cardDue).localeCompare(String(existingDue)) < 0;
        });

        if (before) zone.insertBefore(card, before);
        else zone.appendChild(card);

        updateCounts();
        if (window.lucide) window.lucide.createIcons();
    }

    async function fetchTaskCardHtml(taskId) {
        if (!taskId || !routes.cardBase) return null;
        const data = await gtJson(`${routes.cardBase}/${taskId}/card`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
        });
        return data.html || null;
    }

    async function insertRealtimeTaskCard(task) {
        const taskId = task?.id;
        if (!taskId) return false;
        if (qs(`.gt-task[data-task-id="${CSS.escape(String(taskId))}"]`)) return true;

        const status = task.status || 'open';
        const zone = qs(`.gt-dropzone[data-status="${CSS.escape(String(status))}"]`) || qs('.gt-dropzone[data-status="open"]');
        if (!zone) return false;

        try {
            const html = await fetchTaskCardHtml(taskId);
            if (!html) return false;
            const template = document.createElement('template');
            template.innerHTML = html.trim();
            const card = template.content.querySelector('.gt-task');
            if (!card) return false;
            insertCardByPriority(card, zone);
            initSelect2(card);
            return true;
        } catch (error) {
            console.warn('[GeneralTasks] Realtime card konnte nicht geladen werden.', error);
            return false;
        }
    }

    function drawOrgDependencies() {
        const stage = qs('#gtOrgStage');
        const svg = qs('#gtOrgLinkSvg');
        if (!stage || !svg) return;

        const stageRect = stage.getBoundingClientRect();
        const width = Math.max(stage.scrollWidth, stage.offsetWidth, 1);
        const height = Math.max(stage.scrollHeight, stage.offsetHeight, 1);

        svg.setAttribute('width', width);
        svg.setAttribute('height', height);
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        svg.innerHTML = '<defs><marker id="gtOrgArrow" markerWidth="10" markerHeight="10" refX="8" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L0,8 L9,4 z" fill="#94a3b8" /></marker></defs>';

        qsa('[data-org-node][data-org-parent-ids]', stage).forEach(child => {
            const parentIds = (child.dataset.orgParentIds || '').split(',').filter(Boolean);
            if (!parentIds.length) return;

            const cr = child.getBoundingClientRect();
            const x2 = cr.left + cr.width / 2 - stageRect.left + stage.scrollLeft;
            const y2 = cr.top - stageRect.top + stage.scrollTop;

            parentIds.forEach(parentId => {
                const parent = qs(`[data-org-node][data-org-task-id="${CSS.escape(parentId)}"]`, stage);
                if (!parent) return;

                const pr = parent.getBoundingClientRect();
                const x1 = pr.left + pr.width / 2 - stageRect.left + stage.scrollLeft;
                const y1 = pr.bottom - stageRect.top + stage.scrollTop;
                const midY = y1 + Math.max(28, (y2 - y1) / 2);

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('class', 'gt-org-link-path');
                path.setAttribute('marker-end', 'url(#gtOrgArrow)');
                path.setAttribute('d', `M ${x1} ${y1} C ${x1} ${midY}, ${x2} ${midY}, ${x2} ${y2}`);
                svg.appendChild(path);

                const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                dot.setAttribute('class', 'gt-org-link-dot');
                dot.setAttribute('cx', x2);
                dot.setAttribute('cy', y2);
                dot.setAttribute('r', '4');
                svg.appendChild(dot);
            });
        });
    }

    window.gtDrawOrgDependencies = drawOrgDependencies;

    function setupRealtimeTaskToasts() {
        if (!window.Echo || window.__gtRealtimeTaskToastReady) return;
        window.__gtRealtimeTaskToastReady = true;

        const authEmployeeId = Number(document.querySelector('.gt-wrap')?.dataset.authEmployeeId || 0);

        try {
            window.Echo.channel('general-tasks')
                .listen('.GeneralTaskChanged', event => {
                    const actorId = Number(event.actor_id || 0);
                    const action = String(event.action || 'updated');
                    const task = event.task || {};

                    if (actorId && authEmployeeId && actorId === authEmployeeId) return;

                    if (action === 'created') {
                        insertRealtimeTaskCard(task).then(inserted => {
                            gtToast(
                                inserted ? 'ok' : 'info',
                                'Neue Aufgabe hinzugefügt',
                                (event.actor_name || 'Ein Mitarbeiter') + ' hat „' + (task.title || 'eine Aufgabe') + '“ erstellt.' + (inserted ? ' Die Karte wurde eingefügt.' : ' Bitte aktualisieren, falls sie nicht sichtbar ist.')
                            );
                        });
                        return;
                    }

                    if (action === 'deleted') {
                        gtToast('info', 'Aufgabe gelöscht', (task.title || 'Eine Aufgabe') + ' wurde gelöscht.');
                        return;
                    }

                    if (action === 'moved') {
                        gtToast('info', 'Aufgabe verschoben', (task.title || 'Eine Aufgabe') + ' wurde aktualisiert.');
                    }
                });
        } catch (error) {
            console.warn('[GeneralTasks] Realtime toast konnte nicht gestartet werden.', error);
        }
    }

    setTaskMode(selectedTaskMode());
    toggleRecurrenceSettings();
    setTimeout(() => { initSelect2(document); drawGanttDependencies(); drawOrgDependencies(); setupRealtimeTaskToasts(); }, 100);
    if (window.lucide) window.lucide.createIcons();
})();
</script>
