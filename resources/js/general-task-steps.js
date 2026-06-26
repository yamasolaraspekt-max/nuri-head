/* Include after your current General Tasks script, or paste inside the existing IIFE. */
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const routes = window.generalTaskStepRoutes || {
        toggleBase: '/general-task-steps'
    };

    function q(id) { return document.getElementById(id); }

    function initStepSelect2(scope = document) {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;
        jQuery(scope).find('.gt-step-assignee-select').each(function () {
            const $select = jQuery(this);
            if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
            const parentModal = $select.closest('.gt-modal-backdrop');
            $select.select2({
                width: '100%',
                placeholder: $select.data('placeholder') || 'Mitarbeiter suchen...',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: parentModal.length ? parentModal : jQuery(document.body)
            });
        });
    }

    function updateStepNames() {
        document.querySelectorAll('#gtStepsRepeater [data-step-editor]').forEach((row, index) => {
            row.querySelector('[data-step-number]').textContent = index + 1;
            row.querySelectorAll('[data-step-field]').forEach(field => {
                const key = field.dataset.stepField;
                if (!key) return;
                field.name = `steps[${index}][${key}]`;
            });
        });
    }

    function addStep(data = {}, single = false) {
        const tpl = q('gtStepTemplate');
        const wrap = q('gtStepsRepeater');
        if (!tpl || !wrap) return;

        const node = tpl.content.firstElementChild.cloneNode(true);
        if (single) node.dataset.single = '1';

        node.querySelector('[data-step-field="id"]').value = data.id || '';
        node.querySelector('[data-step-field="title"]').value = data.title || '';
        node.querySelector('[data-step-field="description"]').value = data.description || '';
        node.querySelector('[data-step-field="soll_hours"]').value = data.soll_hours || '';
        node.querySelector('[data-step-field="ist_hours"]').value = data.ist_hours || '';

        wrap.appendChild(node);
        const assigneeSelect = node.querySelector('[data-step-field="assignee_ids"]');
        if (assigneeSelect && Array.isArray(data.assignee_ids)) {
            Array.from(assigneeSelect.options).forEach(opt => opt.selected = data.assignee_ids.map(String).includes(String(opt.value)));
        }

        updateStepNames();
        initStepSelect2(node);
        if (window.lucide) window.lucide.createIcons();
    }

    function setMode(mode) {
        const bulk = mode === 'bulk';
        q('gtAddStepBtn')?.classList.toggle('hidden', !bulk);
        document.querySelectorAll('[name="task_mode"]').forEach(r => r.checked = r.value === mode);

        const rows = [...document.querySelectorAll('#gtStepsRepeater [data-step-editor]')];
        if (!bulk) {
            rows.slice(1).forEach(row => row.remove());
            const first = document.querySelector('#gtStepsRepeater [data-step-editor]');
            if (first) first.dataset.single = '1';
            if (!first) addStep({ title: q('gtTitle')?.value || '' }, true);
        } else {
            rows.forEach(row => delete row.dataset.single);
            if (!rows.length) addStep({}, false);
        }
        updateStepNames();
    }

    window.gtLoadTaskStepsIntoModal = function (taskMode = 'single', steps = []) {
        const wrap = q('gtStepsRepeater');
        if (!wrap) return;
        wrap.innerHTML = '';
        const mode = taskMode === 'bulk' ? 'bulk' : 'single';
        if (steps && steps.length) {
            steps.forEach((step, i) => addStep(step, mode === 'single' && i === 0));
        } else {
            addStep({ title: q('gtTitle')?.value || '' }, mode === 'single');
        }
        setMode(mode);
    };

    window.gtReadStepsFromCard = function (card) {
        try { return JSON.parse(card?.dataset.steps || '[]'); } catch (e) { return []; }
    };

    document.addEventListener('change', e => {
        const modeRadio = e.target.closest('[name="task_mode"]');
        if (modeRadio) setMode(modeRadio.value);
    });

    document.addEventListener('click', e => {
        if (e.target.closest('#gtAddStepBtn')) {
            addStep({}, false);
        }

        const remove = e.target.closest('[data-step-remove]');
        if (remove) {
            const row = remove.closest('[data-step-editor]');
            const del = row.querySelector('[data-step-field="_delete"]');
            if (row.querySelector('[data-step-field="id"]').value) {
                del.value = '1';
                row.style.display = 'none';
            } else {
                row.remove();
            }
            updateStepNames();
        }

        const toggle = e.target.closest('[data-step-toggle]');
        if (toggle) {
            e.preventDefault();
            toggleStep(toggle);
        }
    });

    async function askReason(title) {
        if (typeof window.gtAskChangeReason === 'function') {
            return await window.gtAskChangeReason(title || 'Schritt aktualisieren', 'Bitte gib den Grund für diese Schrittänderung ein.');
        }
        return window.prompt('Grund für die Änderung:');
    }

    async function toggleStep(btn) {
        const stepId = btn.dataset.stepId;
        const nextDone = !['1', 'true', 'yes'].includes(String(btn.dataset.stepDone || '').toLowerCase());
        const reason = await askReason(nextDone ? 'Schritt erledigen' : 'Schritt wieder öffnen');
        if (!reason) return;

        let istHours = null;
        if (nextDone) {
            istHours = window.prompt('Ist Stunden für diesen Schritt (optional):', '');
        }

        try {
            const res = await fetch(`${routes.toggleBase}/${stepId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_done: nextDone, reason, ist_hours: istHours })
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Schritt konnte nicht aktualisiert werden.');
            if (typeof window.gtToast === 'function') window.gtToast('ok', 'Aktualisiert', data.message);
            location.reload();
        } catch (err) {
            if (typeof window.gtToast === 'function') window.gtToast('bad', 'Fehler', err.message);
            else alert(err.message);
        }
    }

    q('gtTitle')?.addEventListener('input', () => {
        const mode = document.querySelector('[name="task_mode"]:checked')?.value || 'single';
        if (mode !== 'single') return;
        const firstTitle = document.querySelector('#gtStepsRepeater [data-step-field="title"]');
        if (firstTitle && !firstTitle.dataset.manuallyChanged) firstTitle.value = q('gtTitle').value;
    });

    document.addEventListener('input', e => {
        if (e.target.matches('#gtStepsRepeater [data-step-field="title"]')) {
            e.target.dataset.manuallyChanged = '1';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        window.gtLoadTaskStepsIntoModal?.('single', []);
    });
})();
