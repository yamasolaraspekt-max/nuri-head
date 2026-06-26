<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const employeePayload = {!! json_encode($employeePayload ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

    const routes = {
        archiveBase: @json(url('/general-tasks')),
        stepToggleBase: @json(url('/general-tasks')),
        move: @json(route('general-tasks.move')),
    };

    function qs(selector, scope = document) { return scope.querySelector(selector); }
    function qsa(selector, scope = document) { return Array.from(scope.querySelectorAll(selector)); }

    window.gtOpenModal = window.gtOpenModal || function (id) { qs('#' + id)?.classList.add('open'); };
    window.gtCloseModal = window.gtCloseModal || function (id) { qs('#' + id)?.classList.remove('open'); };

    async function gtJson(url, options = {}) {
        const res = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            ...options
        });
        const data = await res.json().catch(() => ({ message: 'Serverantwort konnte nicht gelesen werden.' }));
        if (!res.ok) throw new Error(data.message || 'Aktion fehlgeschlagen.');
        return data;
    }

    function gtToast(kind, title, msg) {
        if (window.gtToast && window.gtToast !== gtToast) {
            window.gtToast(kind, title, msg);
            return;
        }
        const wrap = qs('#gtToastWrap');
        if (!wrap) return;
        const el = document.createElement('div');
        el.className = 'gt-toast';
        el.innerHTML = '<div class="gt-toast-ic ' + (kind || 'info') + '">' + (kind === 'ok' ? '✓' : kind === 'bad' ? '!' : 'i') + '</div><div style="flex:1"><p class="gt-toast-ttl">' + escapeHtml(title || 'Info') + '</p><p class="gt-toast-msg">' + escapeHtml(msg || '') + '</p></div><button class="gt-toast-x" type="button">×</button>';
        el.querySelector('button')?.addEventListener('click', () => el.remove());
        wrap.appendChild(el);
        setTimeout(() => el.remove(), 5500);
    }
    window.gtToast = gtToast;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>'"]/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[s]));
    }

    function initSelect2(scope = document) {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;
        jQuery(scope).find('.gt-select2').each(function () {
            const select = jQuery(this);
            if (select.hasClass('select2-hidden-accessible')) select.select2('destroy');
            const parentModal = select.closest('.gt-modal-backdrop');
            select.select2({
                width: '100%',
                placeholder: select.data('placeholder') || 'Bitte auswählen',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: parentModal.length ? parentModal : jQuery(document.body)
            });
        });
    }
    window.gtInitSelect2 = initSelect2;

    function setTaskMode(mode) {
        const isBulk = mode === 'bulk';
        qs('#gtSingleAssigneeSection')?.classList.toggle('hidden', isBulk);
        qs('#gtStepBuilder')?.classList.toggle('active', isBulk);

        if (isBulk && qsa('[data-step-row]', qs('#gtStepsContainer')).length === 0) {
            addStepRow();
        }
    }

    qsa('[data-task-mode-radio]').forEach(input => {
        input.addEventListener('change', () => setTaskMode(input.value));
    });

    function fillEmployeeOptions(select, selectedIds = []) {
        select.innerHTML = '';
        employeePayload.forEach(employee => {
            const option = document.createElement('option');
            option.value = String(employee.id);
            option.textContent = employee.name;
            if (selectedIds.map(String).includes(String(employee.id))) option.selected = true;
            select.appendChild(option);
        });
    }

    function renumberSteps() {
        qsa('[data-step-row]', qs('#gtStepsContainer')).forEach((row, index) => {
            row.dataset.index = String(index);
            const number = index + 1;
            qs('[data-step-number]', row).textContent = number;
            qsa('[data-step-field]', row).forEach(field => {
                const key = field.dataset.stepField;
                if (!key) return;
                field.name = key === 'assignee_ids'
                    ? 'steps[' + index + '][assignee_ids][]'
                    : 'steps[' + index + '][' + key + ']';
            });
        });
    }

    function addStepRow(data = {}) {
        const template = qs('#gtStepTemplate');
        const container = qs('#gtStepsContainer');
        if (!template || !container) return;

        const fragment = template.content.cloneNode(true);
        const row = qs('[data-step-row]', fragment);
        const select = qs('[data-step-field="assignee_ids"]', row);
        fillEmployeeOptions(select, data.assignee_ids || []);

        qs('[data-step-field="id"]', row).value = data.id || '';
        qs('[data-step-field="title"]', row).value = data.title || '';
        qs('[data-step-field="description"]', row).value = data.description || '';
        qs('[data-step-field="planned_hours"]', row).value = data.planned_hours || '';
        qs('[data-step-field="actual_hours"]', row).value = data.actual_hours || '';
        qs('[data-step-field="is_done"]', row).checked = !!data.is_done;
        qs('[data-step-preview-title]', row).textContent = data.title || 'Neuer Schritt';

        qs('[data-step-collapse]', row).addEventListener('click', event => {
            if (event.target.closest('[data-step-remove]')) return;
            row.classList.toggle('collapsed');
        });

        qs('[data-step-remove]', row).addEventListener('click', event => {
            event.stopPropagation();
            row.remove();
            renumberSteps();
        });

        qs('[data-step-field="title"]', row).addEventListener('input', event => {
            qs('[data-step-preview-title]', row).textContent = event.target.value || 'Neuer Schritt';
        });

        container.appendChild(fragment);
        renumberSteps();
        initSelect2(row);
        if (window.lucide) window.lucide.createIcons();
    }

    qs('#gtAddStepBtn')?.addEventListener('click', () => addStepRow());

    window.gtResetSteps = function () {
        const container = qs('#gtStepsContainer');
        if (container) container.innerHTML = '';
        setTaskMode(qs('[name="task_mode"]:checked')?.value || 'single');
    };

    window.gtLoadStepsForEdit = function (steps) {
        const container = qs('#gtStepsContainer');
        if (!container) return;
        container.innerHTML = '';
        (steps || []).forEach(step => addStepRow(step));
        setTaskMode(qs('[name="task_mode"]:checked')?.value || 'single');
    };

    document.addEventListener('click', event => {
        const settingsToggle = event.target.closest('[data-settings-toggle]');
        if (settingsToggle) {
            const key = settingsToggle.dataset.settingsToggle;
            const body = qs('[data-settings-body="' + key + '"]');
            if (body) body.hidden = !body.hidden;
        }

        const stepButton = event.target.closest('[data-step-toggle-modal]');
        if (stepButton) {
            openStepCheckModal(stepButton);
        }
    });

    function openStepCheckModal(button) {
        qs('#gtStepCheckTaskId').value = button.dataset.taskId || '';
        qs('#gtStepCheckStepId').value = button.dataset.stepId || '';
        qs('#gtStepCheckDone').value = button.dataset.nextDone || '1';
        qs('#gtStepCheckReason').value = '';
        qs('#gtStepCheckTitle').textContent = button.dataset.nextDone === '1' ? 'Schritt abschließen' : 'Schritt wieder öffnen';
        qs('#gtStepCheckSubtitle').textContent = button.dataset.stepTitle || 'Der Abschluss wird mit Mitarbeiter und Zeit gespeichert.';
        gtOpenModal('gtStepCheckModal');
    }

    qsa('[data-step-check-cancel]').forEach(button => {
        button.addEventListener('click', () => gtCloseModal('gtStepCheckModal'));
    });

    qs('#gtStepCheckSaveBtn')?.addEventListener('click', async () => {
        const taskId = qs('#gtStepCheckTaskId').value;
        const stepId = qs('#gtStepCheckStepId').value;
        const isDone = qs('#gtStepCheckDone').value === '1';
        const reason = qs('#gtStepCheckReason').value.trim();

        if (!reason) {
            gtToast('bad', 'Grund fehlt', 'Bitte gib eine Notiz oder einen Grund ein.');
            return;
        }

        try {
            await gtJson(routes.stepToggleBase + '/' + taskId + '/steps/' + stepId + '/toggle', {
                method: 'POST',
                body: JSON.stringify({ is_done: isDone, reason: reason })
            });
            gtToast('ok', 'Gespeichert', 'Schritt wurde aktualisiert.');
            window.location.reload();
        } catch (error) {
            gtToast('bad', 'Fehler', error.message);
        }
    });

    window.gtArchiveTask = async function (button) {
        const taskId = button.dataset.taskId || button.closest('.gt-task')?.dataset.taskId;
        if (!taskId) return;
        const reason = prompt('Warum soll diese Aufgabe archiviert werden?');
        if (!reason) return;
        try {
            await gtJson(routes.archiveBase + '/' + taskId + '/archive', {
                method: 'POST',
                body: JSON.stringify({ reason: reason })
            });
            gtToast('ok', 'Archiviert', 'Aufgabe wurde archiviert.');
            window.location.reload();
        } catch (error) {
            gtToast('bad', 'Fehler', error.message);
        }
    };

    function drawGanttDependencies() {
        const stage = qs('.gt-gantt-stage');
        const svg = qs('.gt-gantt-dependency-svg');
        if (!stage || !svg) return;
        svg.innerHTML = '<defs><marker id="gtArrow" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L0,6 L9,3 z" fill="#2563eb" /></marker></defs>';
        const stageRect = stage.getBoundingClientRect();
        qsa('[data-parent-ids]').forEach(childBar => {
            const childIds = (childBar.dataset.parentIds || '').split(',').filter(Boolean);
            childIds.forEach(parentId => {
                const parentBar = qs('[data-gantt-task-id="' + parentId + '"]');
                if (!parentBar) return;
                const parentRect = parentBar.getBoundingClientRect();
                const childRect = childBar.getBoundingClientRect();
                const x1 = parentRect.right - stageRect.left;
                const y1 = parentRect.top + parentRect.height / 2 - stageRect.top;
                const x2 = childRect.left - stageRect.left;
                const y2 = childRect.top + childRect.height / 2 - stageRect.top;
                const midX = Math.max(x1 + 24, (x1 + x2) / 2);
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', 'M ' + x1 + ' ' + y1 + ' C ' + midX + ' ' + y1 + ', ' + midX + ' ' + y2 + ', ' + x2 + ' ' + y2);
                path.setAttribute('stroke', '#2563eb');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('fill', 'none');
                path.setAttribute('marker-end', 'url(#gtArrow)');
                path.setAttribute('opacity', '0.8');
                svg.appendChild(path);
            });
        });
    }

    window.gtDrawGanttDependencies = drawGanttDependencies;
    window.addEventListener('resize', drawGanttDependencies);
    setTimeout(drawGanttDependencies, 250);

    qs('#gtIsRecurring')?.addEventListener('change', event => {
        const settings = qs('#gtRecurrenceSettings');
        if (settings) settings.hidden = !event.target.checked;
    });

    document.addEventListener('DOMContentLoaded', () => {
        setTaskMode(qs('[name="task_mode"]:checked')?.value || 'single');
        initSelect2(document);
        if (window.lucide) window.lucide.createIcons();
    });
})();
</script>
