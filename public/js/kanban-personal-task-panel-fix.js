/*
 * Kanban Personal Task Panel Fix
 * Put in: public/js/kanban-personal-task-panel-fix.js
 * Load AFTER public/js/kanban.js and AFTER the #pt-drawer HTML exists.
 *
 * Fixes:
 * - Select2 employee dropdown inside Kanban task drawer
 * - Schritt / PersonalTaskKey creation inside Kanban task drawer
 * - Auto stage/sub-stage context from the opened Kanban card
 * - Task drawer opens above the Unterphasen sidebar
 */
(function () {
    "use strict";

    if (window.__KANBAN_PERSONAL_TASK_PANEL_FIX__) return;
    window.__KANBAN_PERSONAL_TASK_PANEL_FIX__ = true;

    const qs = (selector, root = document) => root.querySelector(selector);
    const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));

    function esc(value) {
        return String(value ?? "").replace(/[&<>\"']/g, function (m) {
            return {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;",
            }[m];
        });
    }

    function csrf() {
        return qs('meta[name="csrf-token"]')?.content || window.KANBAN_BOOT?.csrf || "";
    }

    function employeeLabel(employee) {
        const last = employee?.lastname || employee?.last_name || "";
        const first = employee?.name || employee?.first_name || "";
        const label = `${last} ${first}`.trim() || employee?.full_name || employee?.text || `Mitarbeiter #${employee?.id || ""}`;
        return label.trim();
    }

    function employeesFromBoot() {
        const raw = window.KANBAN_BOOT?.employees || window.ALL_EMPLOYEES || [];
        if (Array.isArray(raw)) return raw;
        if (raw && typeof raw === "object") return Object.values(raw);
        return [];
    }

    function fillEmployeeOptions(select) {
        if (!select) return;
        if (select.options && select.options.length > 0) return;

        employeesFromBoot().forEach(function (employee) {
            const id = employee?.id ?? employee?.employee_id ?? employee?.value ?? "";
            if (!id) return;
            const option = document.createElement("option");
            option.value = String(id);
            option.textContent = employeeLabel(employee);
            select.appendChild(option);
        });
    }

    function select2Ready() {
        return !!(window.jQuery && window.jQuery.fn && window.jQuery.fn.select2);
    }

    function initSelect2(select, options = {}) {
        if (!select || !select2Ready()) return;
        const $ = window.jQuery;
        const $select = $(select);
        const $parent = $(qs("#pt-drawer") || document.body);

        fillEmployeeOptions(select);

        if ($select.hasClass("select2-hidden-accessible")) {
            try { $select.select2("destroy"); } catch (_) {}
        }

        $select.select2({
            width: "100%",
            allowClear: true,
            dropdownParent: $parent,
            placeholder: select.getAttribute("data-placeholder") || options.placeholder || "Mitarbeiter wählen…",
            ...options,
        });
    }

    function initTaskDrawerSelect2() {
        initSelect2(qs("#pt-employee_ids"), {
            placeholder: "Mitarbeiter wählen…",
            closeOnSelect: false,
        });

        qsa("#pt-steps .pt-step-employees").forEach(function (select) {
            initSelect2(select, {
                placeholder: "Mitarbeiter für diesen Schritt…",
                closeOnSelect: false,
            });
        });
    }

    function refreshIcons() {
        try {
            if (window.lucide && typeof window.lucide.createIcons === "function") {
                window.lucide.createIcons();
            }
            if (window.feather && typeof window.feather.replace === "function") {
                window.feather.replace();
            }
        } catch (_) {}
    }

    function stageMetaMap() {
        return {
            ...(window.KANBAN_BOOT?.leadStageMetaForJs || {}),
            ...(window.KANBAN_BOOT?.kanbanStageMetaForJs || {}),
            ...(window.APP?.stageMeta || {}),
            ...(window.APP?.kanbanStageMeta || {}),
            ...(window.KanbanAPP?.stageMeta || {}),
            ...(window.KanbanAPP?.kanbanStageMeta || {}),
        };
    }

    function stageNamesMap() {
        return {
            ...(window.KANBAN_BOOT?.leadStageNamesForJs || {}),
            ...(window.KANBAN_BOOT?.kanbanStageNamesForJs || {}),
            ...(window.APP?.stageNames || {}),
            ...(window.APP?.kanbanStageNames || {}),
            ...(window.KanbanAPP?.stageNames || {}),
            ...(window.KanbanAPP?.kanbanStageNames || {}),
        };
    }

    function canonicalStage(value) {
        const raw = String(value || "").trim();
        if (!raw) return "";
        if (raw.startsWith("product_stage_")) return raw;
        if (window.LeadUI?.utils?.canonicalStage) {
            try { return window.LeadUI.utils.canonicalStage(raw); } catch (_) {}
        }
        const key = raw.toLowerCase();
        const aliases = {
            open: "lead",
            new: "lead",
            neue: "lead",
            lead: "lead",
            angebot: "offer",
            offer: "offer",
            nachfassen: "follow_up",
            follow_up: "follow_up",
            annehmen: "accepted",
            angenommen: "accepted",
            accepted: "accepted",
            auftrag: "deal",
            deal: "deal",
            montage: "project",
            project: "project",
            abschluss: "completed",
            completed: "completed",
            archive: "archive",
            archiv: "archive",
            junk: "junk",
        };
        return aliases[key] || key;
    }

    function findStageById(stageId) {
        if (!stageId) return { key: "", meta: null };
        const metas = stageMetaMap();
        for (const [key, meta] of Object.entries(metas)) {
            if (String(meta?.id || meta?.stage_id || "") === String(stageId)) {
                return { key, meta };
            }
        }
        return { key: "", meta: null };
    }

    function findSubStage(stageMeta, subStageId) {
        if (!subStageId || !stageMeta) return null;
        const list = Array.isArray(stageMeta.sub_stages)
            ? stageMeta.sub_stages
            : (Array.isArray(stageMeta.subStages) ? stageMeta.subStages : []);
        return list.find((row) => String(row?.id || row?.stage_sub_stage_id || "") === String(subStageId)) || null;
    }

    function closestTaskCardFromEvent(event, detail = {}) {
        let card = event?.target?.closest?.(".card, [data-lead-product-list-id], [data-lead-product-id]") || null;
        const leadProductId = detail.leadProductListId || detail.lead_product_list_id || "";

        if (!card && leadProductId && window.CSS?.escape) {
            card = qs(`[data-lead-product-list-id="${CSS.escape(String(leadProductId))}"], [data-lead-product-id="${CSS.escape(String(leadProductId))}"]`);
        }

        return card;
    }

    function resolveLeadStageContext(detail = {}, sourceEvent = null) {
        const card = closestTaskCardFromEvent(sourceEvent, detail);
        const zone = card?.closest?.("[data-understage-dropzone]") || sourceEvent?.target?.closest?.("[data-understage-dropzone]") || null;
        const column = card?.closest?.(".column") || null;

        const rawStage =
            detail.stage ||
            detail.companyStage ||
            detail.company_stage ||
            detail.lead_stage_key ||
            card?.dataset?.stage ||
            card?.dataset?.companyStage ||
            zone?.dataset?.stageKey ||
            column?.id ||
            "";

        let stageKey = canonicalStage(rawStage);
        let metas = stageMetaMap();
        let stageMeta = metas[stageKey] || null;

        const explicitStageId =
            detail.leadStageId ||
            detail.lead_stage_id ||
            card?.dataset?.leadStageId ||
            card?.dataset?.stageId ||
            "";

        if (!stageMeta && explicitStageId) {
            const found = findStageById(explicitStageId);
            stageKey = found.key || stageKey;
            stageMeta = found.meta || stageMeta;
        }

        const subStageId =
            detail.leadStageSubStageId ||
            detail.lead_stage_sub_stage_id ||
            card?.dataset?.leadStageSubStageId ||
            card?.dataset?.leadStageSubstageId ||
            card?.dataset?.subStageId ||
            zone?.dataset?.subStageId ||
            "";

        const subStageMeta = findSubStage(stageMeta, subStageId);
        const names = stageNamesMap();

        const context = {
            lead_product_list_id:
                detail.leadProductListId ||
                detail.lead_product_list_id ||
                card?.dataset?.leadProductListId ||
                card?.dataset?.leadProductId ||
                "",
            lead_stage_key: stageKey || "",
            lead_stage_id: explicitStageId || stageMeta?.id || stageMeta?.stage_id || "",
            lead_stage_name: stageMeta?.name || names[stageKey] || stageKey || "",
            lead_stage_color: stageMeta?.color || "#74b2d4",
            lead_stage_sub_stage_id: subStageId || "",
            lead_stage_sub_stage_name: subStageMeta?.name || (subStageId ? `Unterphase #${subStageId}` : ""),
            lead_stage_sub_stage_color: subStageMeta?.color || "#93c21c",
            is_sub_stage_context: !!subStageId,
        };

        window.__KANBAN_CURRENT_PERSONAL_TASK_CONTEXT__ = context;
        return context;
    }

    function ensureStageHiddenInputs() {
        const form = qs("#pt-form");
        if (!form) return;

        const fields = [
            "lead_stage_id",
            "lead_stage_sub_stage_id",
            "lead_stage_key",
            "lead_stage_name",
            "lead_stage_sub_stage_name",
        ];

        fields.forEach(function (name) {
            const id = "pt-" + name;
            if (qs("#" + id)) return;
            const input = document.createElement("input");
            input.type = "hidden";
            input.id = id;
            input.name = name;
            form.appendChild(input);
        });
    }

    function applyStageContextToForm(context = {}) {
        ensureStageHiddenInputs();
        const set = (selector, value) => {
            const el = qs(selector);
            if (el) el.value = value || "";
        };

        set("#pt-lead_product_list_id", context.lead_product_list_id || "");
        set("#pt-lead_stage_id", context.lead_stage_id || "");
        set("#pt-lead_stage_sub_stage_id", context.lead_stage_sub_stage_id || "");
        set("#pt-lead_stage_key", context.lead_stage_key || "");
        set("#pt-lead_stage_name", context.lead_stage_name || "");
        set("#pt-lead_stage_sub_stage_name", context.lead_stage_sub_stage_name || "");

        let preview = qs("#pt-stage-context-preview");
        const intro = qs("#pt-tab-create .ptx-create-intro");
        if (!preview && intro) {
            preview = document.createElement("div");
            preview.id = "pt-stage-context-preview";
            preview.className = "pt-stage-context-preview";
            intro.insertAdjacentElement("afterend", preview);
        }

        if (preview) {
            const stage = context.lead_stage_name || "Hauptphase automatisch";
            const sub = context.lead_stage_sub_stage_name || "Keine Unterphase";
            preview.innerHTML = `
                <span class="pt-stage-chip" style="border-color:${esc(context.lead_stage_color || "#74b2d4")}">
                    <i data-lucide="git-branch"></i>${esc(stage)}
                </span>
                <span class="pt-stage-chip ${context.lead_stage_sub_stage_id ? "" : "is-muted"}" style="border-color:${esc(context.lead_stage_sub_stage_color || "#93c21c")}">
                    <i data-lucide="workflow"></i>${esc(sub)}
                </span>
            `;
        }

        refreshIcons();
    }

    function employeeOptionsHTML() {
        let base = qs("#pt-employee_ids");
        if (base) fillEmployeeOptions(base);
        const options = base ? Array.from(base.options) : [];

        if (options.length) {
            return options.map((option) => `<option value="${esc(option.value)}">${esc(option.textContent || option.innerText || option.value)}</option>`).join("");
        }

        return employeesFromBoot().map(function (employee) {
            const id = employee?.id ?? employee?.employee_id ?? employee?.value ?? "";
            if (!id) return "";
            return `<option value="${esc(id)}">${esc(employeeLabel(employee))}</option>`;
        }).join("");
    }

    function nextStepIndex() {
        return qsa("#pt-steps .pt-step-card").length;
    }

    function addStepRow(prefill = {}) {
        const list = qs("#pt-steps");
        if (!list) return;

        const index = nextStepIndex();
        const html = `
            <article class="pt-step-card" data-pt-step-row>
                <div class="pt-step-head">
                    <strong>Schritt <span data-pt-step-no>${index + 1}</span></strong>
                    <button type="button" class="pt-step-remove" data-pt-remove-step title="Schritt entfernen">
                        <i data-lucide="trash-2"></i>
                    </button>
                </div>

                <div class="pt-step-grid">
                    <div class="pt-step-field is-wide">
                        <label>Aufgabenschritt</label>
                        <input type="text" class="form-control pt-step-task" placeholder="z. B. Unterlagen prüfen" value="${esc(prefill.task || "")}">
                    </div>
                    <div class="pt-step-field">
                        <label>Dauer / Stunden</label>
                        <input type="number" min="0" step="0.25" class="form-control pt-step-duration" value="${esc(prefill.duration || "")}" placeholder="0.5">
                    </div>
                    <div class="pt-step-field">
                        <label>Status</label>
                        <select class="form-control pt-step-status">
                            <option value="accepted">Offen</option>
                            <option value="on_progress">In Bearbeitung</option>
                            <option value="completed">Erledigt</option>
                        </select>
                    </div>
                    <div class="pt-step-field is-wide">
                        <label>Mitarbeiter für diesen Schritt</label>
                        <select class="form-control pt-step-employees" multiple>${employeeOptionsHTML()}</select>
                    </div>
                    <div class="pt-step-field is-wide">
                        <label>Beschreibung</label>
                        <textarea class="form-control pt-step-description" rows="2" placeholder="Beschreibung für diesen Schritt…">${esc(prefill.key_description || prefill.description || "")}</textarea>
                    </div>
                </div>
            </article>
        `;

        list.insertAdjacentHTML("beforeend", html);
        const row = list.lastElementChild;

        const status = row?.querySelector(".pt-step-status");
        if (status) status.value = prefill.status || "accepted";

        initSelect2(row?.querySelector(".pt-step-employees"), {
            placeholder: "Mitarbeiter für diesen Schritt…",
            closeOnSelect: false,
        });

        if (prefill.employee_id || prefill.employee_ids) {
            const ids = Array.isArray(prefill.employee_ids)
                ? prefill.employee_ids
                : (Array.isArray(prefill.employee_id) ? prefill.employee_id : []);
            if (ids.length && select2Ready()) {
                window.jQuery(row).find(".pt-step-employees").val(ids.map(String)).trigger("change");
            }
        }

        renumberSteps();
        updateStepTotals();
        refreshIcons();
    }

    function renumberSteps() {
        qsa("#pt-steps .pt-step-card").forEach(function (row, index) {
            const no = row.querySelector("[data-pt-step-no]");
            if (no) no.textContent = String(index + 1);
        });
    }

    function updateStepTotals() {
        let total = 0;
        qsa("#pt-steps .pt-step-duration").forEach(function (input) {
            total += Number(String(input.value || "").replace(",", ".")) || 0;
        });
        if (total > 0) {
            const totalTime = qs("#pt-total_time");
            const totalDay = qs("#pt-total_day");
            if (totalTime) totalTime.value = total.toFixed(2);
            if (totalDay) totalDay.value = (total / 8).toFixed(2);
        }
    }

    function collectSteps() {
        return qsa("#pt-steps .pt-step-card").map(function (row) {
            const task = row.querySelector(".pt-step-task")?.value?.trim() || "";
            const description = row.querySelector(".pt-step-description")?.value?.trim() || "";
            const duration = row.querySelector(".pt-step-duration")?.value || "";
            const status = row.querySelector(".pt-step-status")?.value || "accepted";
            let employeeIds = [];

            const select = row.querySelector(".pt-step-employees");
            if (select2Ready() && select) {
                employeeIds = window.jQuery(select).val() || [];
            } else if (select) {
                employeeIds = Array.from(select.selectedOptions || []).map((option) => option.value).filter(Boolean);
            }

            return {
                task,
                duration,
                status,
                key_description: description,
                employee_id: employeeIds,
            };
        }).filter(function (row) {
            return !!(row.task || row.key_description || row.duration || (row.employee_id || []).length);
        });
    }

    function resetStepRows() {
        const list = qs("#pt-steps");
        if (!list) return;
        list.innerHTML = "";
        addStepRow();
    }

    function bindStepUI() {
        const addButton = qs("#pt-add-step");
        if (addButton && addButton.dataset.ptStepBound !== "1") {
            addButton.dataset.ptStepBound = "1";
            addButton.addEventListener("click", function (event) {
                event.preventDefault();
                addStepRow();
            });
        }

        const resetButton = qs("#pt-reset-form");
        if (resetButton && resetButton.dataset.ptResetBound !== "1") {
            resetButton.dataset.ptResetBound = "1";
            resetButton.addEventListener("click", function (event) {
                event.preventDefault();
                qs("#pt-form")?.reset();
                if (select2Ready()) {
                    window.jQuery("#pt-employee_ids").val(null).trigger("change");
                }
                resetStepRows();
                applyStageContextToForm(window.__KANBAN_CURRENT_PERSONAL_TASK_CONTEXT__ || {});
            });
        }

        document.addEventListener("click", function (event) {
            const remove = event.target.closest("[data-pt-remove-step]");
            if (!remove) return;
            event.preventDefault();
            const rows = qsa("#pt-steps .pt-step-card");
            if (rows.length <= 1) {
                if (window.Swal) Swal.fire("Hinweis", "Es muss mindestens ein Schritt bleiben.", "info");
                return;
            }
            remove.closest("[data-pt-step-row]")?.remove();
            renumberSteps();
            updateStepTotals();
        });

        document.addEventListener("input", function (event) {
            if (event.target.closest("#pt-steps") && event.target.classList.contains("pt-step-duration")) {
                updateStepTotals();
            }
        });
    }

    function collectEmployeeIds() {
        const select = qs("#pt-employee_ids");
        if (!select) return [];
        if (select2Ready()) return window.jQuery(select).val() || [];
        return Array.from(select.selectedOptions || []).map((option) => option.value).filter(Boolean);
    }

    function buildTaskPayload(ptk) {
        const ctx = ptk?._ctx || {};
        const stageContext = window.__KANBAN_CURRENT_PERSONAL_TASK_CONTEXT__ || {};
        const customerId = Number(qs("#pt-customer_id")?.value || ctx.customerId || 0);
        const alternativeId = Number(qs("#pt-alternative_id")?.value || ctx.alternativeId || 0);
        const productIdRaw = qs("#pt-product_id")?.value || ctx.productId || "";
        const leadProductListIdRaw = qs("#pt-lead_product_list_id")?.value || ctx.leadProductListId || stageContext.lead_product_list_id || "";
        const employees = collectEmployeeIds();
        const steps = collectSteps();

        return {
            is_customer: 1,
            customer_id: customerId,
            alternative_id: alternativeId || null,
            product_id: productIdRaw ? Number(productIdRaw) : null,
            lead_product_list_id: leadProductListIdRaw ? Number(leadProductListIdRaw) : null,

            lead_stage_id: stageContext.lead_stage_id ? Number(stageContext.lead_stage_id) : null,
            lead_stage_sub_stage_id: stageContext.lead_stage_sub_stage_id ? Number(stageContext.lead_stage_sub_stage_id) : null,
            lead_stage_key: stageContext.lead_stage_key || null,

            task_title: qs("#pt-task_title")?.value.trim() || "",
            description: qs("#pt-description")?.value.trim() || null,
            start_date: qs("#pt-start_date")?.value || null,
            due_date: qs("#pt-due_date")?.value || null,
            due_time: qs("#pt-due_time")?.value || null,
            reminder_date: qs("#pt-reminder_date")?.value || null,
            reminder_time: qs("#pt-reminder_time")?.value || null,
            priority: qs("#pt-priority")?.value || "normal",
            color: qs("#pt-color")?.value || "#93c21c",
            public: qs("#pt-public")?.checked ? 1 : 0,
            total_day: qs("#pt-total_day")?.value || null,
            total_time: qs("#pt-total_time")?.value || null,

            employee_ids: employees,
            employee: employees,
            key: steps,
        };
    }

    async function submitTaskWithSteps(ptk, event) {
        event?.preventDefault?.();

        const payload = buildTaskPayload(ptk);
        if (!payload.task_title) {
            if (window.Swal) Swal.fire("Fehler", "Aufgabentitel ist erforderlich.", "error");
            return;
        }
        if (!payload.customer_id) {
            if (window.Swal) Swal.fire("Fehler", "Kunde fehlt. Bitte Aufgabe aus einer Kanban-Karte öffnen.", "error");
            return;
        }

        const isEdit = !!ptk._editingId;
        const url = isEdit
            ? window.APP.endpoints.personalTasksUpdate(ptk._editingId)
            : window.APP.endpoints.personalTasksStore;
        const method = isEdit ? "PUT" : "POST";

        try {
            const response = await fetch(url, {
                method,
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || data?.success === false || data?.status === "error") {
                const msg = data?.message || data?.errors?.task_title?.[0] || "Aufgabe konnte nicht gespeichert werden.";
                throw new Error(msg);
            }

            ptk._editingId = null;
            qs("#pt-form")?.reset();
            if (select2Ready()) window.jQuery("#pt-employee_ids").val(null).trigger("change");
            resetStepRows();
            applyStageContextToForm(window.__KANBAN_CURRENT_PERSONAL_TASK_CONTEXT__ || {});

            if (typeof ptk.loadTasks === "function") await ptk.loadTasks();
            if (typeof ptk.updateCardBadge === "function") ptk.updateCardBadge();

            if (window.Swal) Swal.fire("Gespeichert", "Aufgabe wurde gespeichert.", "success");
        } catch (error) {
            if (window.Swal) Swal.fire("Fehler", error.message || "Serverfehler", "error");
            else alert(error.message || "Serverfehler");
        }
    }

    function patchPersonalTasksUI() {
        const PTK = window.PersonalTasksUI;
        if (!PTK || PTK.__kanbanPanelFixed) return !!PTK;

        PTK.__kanbanPanelFixed = true;

        const originalOpen = typeof PTK.open === "function" ? PTK.open.bind(PTK) : null;
        if (originalOpen) {
            PTK.open = function () {
                const result = originalOpen.apply(PTK, arguments);
                setTimeout(function () {
                    ensureStageHiddenInputs();
                    initTaskDrawerSelect2();
                    bindStepUI();

                    if (!qsa("#pt-steps .pt-step-card").length) {
                        addStepRow();
                    }

                    applyStageContextToForm(window.__KANBAN_CURRENT_PERSONAL_TASK_CONTEXT__ || {});

                    qs("#pt-backdrop")?.classList.add("pt-open-above-understage");
                    qs("#pt-drawer")?.classList.add("pt-open-above-understage");
                }, 60);
                return result;
            };
        }

        PTK.submitForm = function (event) {
            return submitTaskWithSteps(PTK, event);
        };

        return true;
    }

    function installOpenEventContextBridge() {
        if (window.__KANBAN_PERSONAL_TASK_CONTEXT_BRIDGE__) return;
        window.__KANBAN_PERSONAL_TASK_CONTEXT_BRIDGE__ = true;

        document.addEventListener("open-personal-tasks", function (event) {
            const detail = event.detail || {};
            const context = resolveLeadStageContext(detail, event);

            detail.leadStageId = context.lead_stage_id;
            detail.lead_stage_id = context.lead_stage_id;
            detail.leadStageSubStageId = context.lead_stage_sub_stage_id;
            detail.lead_stage_sub_stage_id = context.lead_stage_sub_stage_id;
            detail.stage = context.lead_stage_key;
            detail.leadProductListId = context.lead_product_list_id || detail.leadProductListId || detail.lead_product_list_id || "";
        }, true);
    }

    function installZIndexFix() {
        if (qs("#kanban-personal-task-panel-fix-style")) return;
        const style = document.createElement("style");
        style.id = "kanban-personal-task-panel-fix-style";
        style.textContent = `
            #pt-backdrop.show,
            #pt-backdrop.pt-open-above-understage {
                z-index: 70000 !important;
            }

            #pt-drawer.open,
            #pt-drawer.pt-open-above-understage {
                z-index: 70001 !important;
            }

            body .select2-container--open {
                z-index: 70010 !important;
            }

            #pt-drawer .select2-container {
                width: 100% !important;
            }

            .pt-stage-context-preview {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin: 0 0 14px;
                padding: 10px;
                border: 1px solid #dbeafe;
                border-radius: 16px;
                background: #f8fafc;
            }

            .pt-stage-chip {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                min-height: 30px;
                padding: 0 10px;
                border-radius: 999px;
                border: 1px solid #dbeafe;
                background: #fff;
                color: #0f172a;
                font-size: 12px;
                font-weight: 900;
            }

            .pt-stage-chip.is-muted {
                color: #64748b;
                background: #f1f5f9;
            }

            .pt-stage-chip svg,
            .pt-stage-chip i {
                width: 14px;
                height: 14px;
            }

            .ptx-steps-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .pt-step-card {
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                background: #ffffff;
                padding: 12px;
                box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
            }

            .pt-step-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 10px;
            }

            .pt-step-head strong {
                color: #0f172a;
                font-size: 13px;
                font-weight: 950;
            }

            .pt-step-remove {
                width: 32px;
                height: 32px;
                border: 1px solid #fecaca;
                border-radius: 12px;
                background: #fef2f2;
                color: #b91c1c;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .pt-step-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .pt-step-field.is-wide {
                grid-column: 1 / -1;
            }

            .pt-step-field label {
                display: block;
                margin-bottom: 5px;
                color: #64748b;
                font-size: 11px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .05em;
            }

            .pt-step-field input,
            .pt-step-field select,
            .pt-step-field textarea {
                border: 1px solid #dbeafe !important;
                border-radius: 13px !important;
                background: #f8fafc !important;
                color: #0f172a !important;
                font-size: 13px !important;
                font-weight: 750 !important;
            }

            .pt-step-field textarea {
                resize: vertical;
            }

            @media (max-width: 720px) {
                .pt-step-grid { grid-template-columns: 1fr; }
            }
        `;
        document.head.appendChild(style);
    }

    function boot() {
        installZIndexFix();
        installOpenEventContextBridge();
        bindStepUI();
        patchPersonalTasksUI();
        initTaskDrawerSelect2();

        let tries = 0;
        const timer = setInterval(function () {
            tries += 1;
            const ok = patchPersonalTasksUI();
            if (ok) {
                bindStepUI();
                initTaskDrawerSelect2();
            }
            if (ok || tries > 80) clearInterval(timer);
        }, 150);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot, { once: true });
    } else {
        boot();
    }
})();
