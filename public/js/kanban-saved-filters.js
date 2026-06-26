/*
 * Kanban saved filters + improved filter drawer + multi-customer + Unternehmen address loader
 * Put in: public/js/kanban-saved-filters.js
 * Load AFTER public/js/kanban.js
 */
(function () {
    "use strict";

    if (window.__KANBAN_SAVED_FILTERS_READY__) return;
    window.__KANBAN_SAVED_FILTERS_READY__ = true;

    const ROUTES = window.KANBAN_SAVED_FILTER_ROUTES || {};

    const ENDPOINTS = {
        index: ROUTES.index || "/lead/kanban/filter-settings",
        store: ROUTES.store || "/lead/kanban/filter-settings",
        base: ROUTES.base || "/lead/kanban/filter-settings",
        update: ROUTES.update || "/lead/kanban/filter-settings/__ID__",
        destroy: ROUTES.destroy || "/lead/kanban/filter-settings/__ID__/delete",
        makeDefault: ROUTES.makeDefault || "/lead/kanban/filter-settings/__ID__/default",
        customers: ROUTES.customers || "/lead/kanban/customers/search",
        branchAddresses: ROUTES.branchAddresses || "/lead/kanban/branch-addresses",
    };

    const $ = window.jQuery;
    const qs = (s, ctx = document) => ctx.querySelector(s);
    const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
    const csrf = () => qs('meta[name="csrf-token"]')?.content || "";

    let booted = false;
    let loadingSavedFilters = false;
    let applyingSavedFilter = false;
    let reloadTimer = null;
    let lastReloadQS = "";

    function endpointUrl(template, id) {
        const value = encodeURIComponent(String(id || ""));

        if (String(template || "").includes("__ID__")) {
            return String(template).replace("__ID__", value);
        }

        return `${ENDPOINTS.base}/${value}`;
    }

    function safeParseJSON(value, fallback = {}) {
        if (!value) return fallback;
        if (typeof value === "object") return value;

        try {
            return JSON.parse(value);
        } catch {
            return fallback;
        }
    }

    function isSelect2Ready(el) {
        return !!($ && $.fn && $.fn.select2 && el);
    }

    function notify(title, text = "", icon = "info") {
        if (window.KBModal?.alert) {
            return window.KBModal.alert({ title, text, icon });
        }

        if (window.Swal?.fire) {
            return window.Swal.fire(title, text, icon);
        }

        alert([title, text].filter(Boolean).join("\n"));
        return Promise.resolve();
    }

    async function askFilterName(defaultName = "") {
        if (window.KBModal?.prompt) {
            return window.KBModal.prompt({
                title: "Filter speichern",
                text: "Name für diese persönliche Filteransicht:",
                inputValue: defaultName,
                confirmButtonText: "Speichern",
            });
        }

        if (window.Swal?.fire) {
            const result = await window.Swal.fire({
                title: "Filter speichern",
                text: "Name für diese persönliche Filteransicht:",
                input: "text",
                inputValue: defaultName,
                showCancelButton: true,
                confirmButtonText: "Speichern",
                cancelButtonText: "Abbrechen",
                inputValidator: (value) => !value ? "Bitte einen Namen eingeben." : undefined,
            });

            return result.isConfirmed ? result.value : null;
        }

        return prompt("Name für diese persönliche Filteransicht:", defaultName);
    }

    async function fetchJSON(url, options = {}) {
        const res = await fetch(url, {
            credentials: "same-origin",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                ...(options.headers || {}),
            },
            ...options,
        });

        const text = await res.text();

        let data = {};
        try {
            data = text ? JSON.parse(text) : {};
        } catch {
            data = { message: text };
        }

        if (!res.ok || data?.success === false) {
            throw new Error(data?.message || `HTTP ${res.status}`);
        }

        return data;
    }

    function setScope(scope) {
        const cleanScope = scope === "mine" ? "mine" : "all";
        localStorage.setItem("kanban_filter_scope", cleanScope);

        qsa("[data-kb-filter-scope]").forEach((btn) => {
            btn.classList.toggle("is-active", btn.dataset.kbFilterScope === cleanScope);
        });

        qs("#kbFilterModeBar")?.classList.toggle("is-my-filter-mode", cleanScope === "mine");
    }

    function buildQueryString(filters = {}) {
        const params = new URLSearchParams();

        Object.entries(filters || {}).forEach(([key, value]) => {
            if (value === null || value === undefined || value === "") return;

            const cleanKey = String(key || "").replace(/\[\]$/, "");
            if (!cleanKey) return;

            if (Array.isArray(value)) {
                value.forEach((v) => {
                    if (v !== null && v !== undefined && String(v).trim() !== "") {
                        params.append(cleanKey, String(v));
                    }
                });
                return;
            }

            params.set(cleanKey, String(value));
        });

        return params.toString();
    }

    function debouncedKanbanReload(qsStr = "") {
        lastReloadQS = String(qsStr || "");
        clearTimeout(reloadTimer);

        reloadTimer = setTimeout(() => {
            if (typeof window.KanbanReloadOnce === "function") {
                window.KanbanReloadOnce(lastReloadQS);
                return;
            }

            if (typeof window.LeadUIFetchKanban === "function") {
                window.LeadUIFetchKanban(lastReloadQS);
                return;
            }

            if (typeof window.fetchKanbanView === "function") {
                window.fetchKanbanView(lastReloadQS);
                return;
            }

            const applyBtn = qs("#btnApplyFilters");
            if (applyBtn) applyBtn.click();
        }, 180);
    }

    window.KanbanReloadOnce = window.KanbanReloadOnce || debouncedKanbanReload;

    window.updateNoteBadgesForVisibleCards = window.updateNoteBadgesForVisibleCards || function () {
        try {
            if (window.LeadUI?.notes?.updateNoteBadgesForVisibleCards) {
                return window.LeadUI.notes.updateNoteBadgesForVisibleCards();
            }
        } catch (error) {
            console.warn("Note badge fallback failed", error);
        }
    };

    window.KanbanNextStepHoverFix = window.KanbanNextStepHoverFix || function () {
        document.querySelectorAll(".kb-next-step-preview").forEach((el) => {
            el.classList.add("kb-next-step-collapsible");
        });
    };

    function setSelectValue(selector, value) {
        const el = qs(selector);
        if (!el) return;

        const normalized = el.multiple
            ? (Array.isArray(value) ? value.map(String) : (value ? [String(value)] : []))
            : (Array.isArray(value) ? String(value[0] || "") : String(value || ""));

        if (isSelect2Ready(el)) {
            $(el).val(normalized).trigger("change.select2");
            el.dispatchEvent(new Event("change", { bubbles: true }));
            return;
        }

        if (el.multiple) {
            Array.from(el.options).forEach((option) => {
                option.selected = normalized.includes(String(option.value));
            });
        } else {
            el.value = normalized;
        }

        el.dispatchEvent(new Event("change", { bubbles: true }));
    }

    function getFormFilters() {
        const form = qs("#kanbanFilterForm");
        const filters = {};
        if (!form) return filters;

        new FormData(form).forEach((value, key) => {
            if (value === null || String(value).trim() === "") return;

            const cleanKey = String(key || "").replace(/\[\]$/, "");
            if (!cleanKey) return;

            if (String(key).endsWith("[]")) {
                if (!Array.isArray(filters[cleanKey])) filters[cleanKey] = [];
                filters[cleanKey].push(String(value));
                return;
            }

            if (filters[cleanKey] !== undefined) {
                if (!Array.isArray(filters[cleanKey])) filters[cleanKey] = [filters[cleanKey]];
                filters[cleanKey].push(String(value));
            } else {
                filters[cleanKey] = String(value);
            }
        });

        const sortSelect = qs("#listSortSelect");
        if (sortSelect?.value && sortSelect.value.includes("|")) {
            const [sortBy, sortDir] = sortSelect.value.split("|");
            filters.sort_by = sortBy;
            filters.sort_dir = sortDir;
        }

        return filters;
    }

    async function ensureCustomerOptions(ids = []) {
        const el = qs("#customerFilter");
        if (!el) return;

        const values = Array.isArray(ids) ? ids : (ids ? [ids] : []);

        values.forEach((id) => {
            if (!id) return;

            const value = String(id);
            const exists = Array.from(el.options).some((option) => String(option.value) === value);

            if (!exists) {
                el.appendChild(new Option(`Kunde #${value}`, value, true, true));
            }
        });

        if (isSelect2Ready(el)) {
            $(el).trigger("change.select2");
        }
    }

    function initCustomerSelect2() {
        const el = qs("#customerFilter");
        if (!el || !isSelect2Ready(el)) return;

        el.setAttribute("multiple", "multiple");
        if (el.name !== "customer[]") el.name = "customer[]";

        const $el = $(el);

        if ($el.hasClass("select2-hidden-accessible")) {
            try {
                $el.select2("destroy");
            } catch {}
        }

        const parent = $el.closest(".drawer, .modal, .notes-drawer").length
            ? $el.closest(".drawer, .modal, .notes-drawer")
            : $(document.body);

        $el.select2({
            placeholder: el.dataset.placeholder || "Mehrere Kunden suchen…",
            allowClear: true,
            width: "100%",
            closeOnSelect: false,
            dropdownParent: parent,
            ajax: {
                url: ENDPOINTS.customers,
                dataType: "json",
                delay: 250,
                cache: true,
                data: function (params) {
                    return {
                        q: params.term || "",
                        page: params.page || 1,
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    const rows = Array.isArray(data?.results) ? data.results : [];

                    return {
                        results: rows.map((row) => ({
                            id: String(row.id),
                            text: row.text || row.name || row.label || `Kunde #${row.id}`,
                        })),
                        pagination: {
                            more: !!data?.pagination?.more,
                        },
                    };
                },
            },
        });
    }

    async function loadBranchAddresses(branchId = null, selectedId = null) {
        const select = qs("#branchAddressFilter");
        if (!select) return [];

        const previous = selectedId || select.value || "";
        const params = new URLSearchParams();

        if (branchId) params.set("branch_id", branchId);

        select.disabled = true;
        select.innerHTML = `<option value="">Adressen werden geladen…</option>`;

        if (isSelect2Ready(select)) {
            $(select).val("").trigger("change.select2");
        }

        try {
            const url = params.toString()
                ? `${ENDPOINTS.branchAddresses}?${params.toString()}`
                : ENDPOINTS.branchAddresses;

            const data = await fetchJSON(url);

            const rows = Array.isArray(data?.results)
                ? data.results
                : (Array.isArray(data?.addresses) ? data.addresses : []);

            select.innerHTML = `<option value="">Alle Adressen</option>`;

            rows.forEach((row) => {
                const id = String(row.id ?? "");
                if (!id) return;

                const text =
                    row.text ||
                    row.label ||
                    row.name ||
                    row.full_address ||
                    [row.street, row.postcode, row.city].filter(Boolean).join(" ") ||
                    `Adresse #${id}`;

                const opt = new Option(text, id, false, false);

                if (row.branch_id) {
                    opt.dataset.branchId = String(row.branch_id);
                }

                select.appendChild(opt);
            });

            const shouldSelect = previous && Array.from(select.options).some((option) => {
                return String(option.value) === String(previous);
            });

            select.value = shouldSelect ? String(previous) : "";
            select.disabled = false;

            if (isSelect2Ready(select)) {
                const $select = $(select);

                if ($select.hasClass("select2-hidden-accessible")) {
                    try {
                        $select.select2("destroy");
                    } catch {}
                }

                const parent = $select.closest(".drawer, .modal, .notes-drawer").length
                    ? $select.closest(".drawer, .modal, .notes-drawer")
                    : $(document.body);

                $select.select2({
                    placeholder: "Unternehmensadresse wählen…",
                    allowClear: true,
                    width: "100%",
                    dropdownParent: parent,
                });

                $select.val(select.value || "").trigger("change.select2");
            }

            return rows;
        } catch (error) {
            console.warn("Branch addresses could not be loaded", error);

            select.disabled = false;
            select.innerHTML = `<option value="">Keine Adresse geladen</option>`;

            if (isSelect2Ready(select)) {
                $(select).val("").trigger("change.select2");
            }

            return [];
        }
    }

    async function applyFiltersToForm(filters = {}, options = {}) {
        const form = qs("#kanbanFilterForm");
        if (!form) return;

        const shouldReload = options.reload !== false;

        applyingSavedFilter = true;

        try {
            qsa("input, select, textarea", form).forEach((el) => {
                if (!el.name) return;

                if (el.type === "checkbox" || el.type === "radio") {
                    el.checked = false;
                    return;
                }

                if (el.tagName === "SELECT") {
                    setSelectValue(`#${el.id}`, el.multiple ? [] : "");
                    return;
                }

                el.value = "";
            });

            if (filters.customer) {
                await ensureCustomerOptions(filters.customer);
            }

            if (filters.branch) {
                setSelectValue("#branchFilter", filters.branch);
                await loadBranchAddresses(filters.branch, filters.branch_address || null);
            } else {
                await loadBranchAddresses(null, filters.branch_address || null);
            }

            Object.entries(filters || {}).forEach(([key, value]) => {
                if (key === "sort_by" || key === "sort_dir") return;

                const normalizedKey = String(key || "").replace(/\[\]$/, "");
                if (!normalizedKey) return;

                const el = form.elements[normalizedKey] || form.elements[`${normalizedKey}[]`];
                if (!el) return;

                if (el instanceof RadioNodeList) {
                    Array.from(el).forEach((node) => {
                        if (node.type === "checkbox" || node.type === "radio") {
                            node.checked = Array.isArray(value)
                                ? value.map(String).includes(String(node.value))
                                : String(node.value) === String(value);
                        }
                    });
                    return;
                }

                if (el.tagName === "SELECT") {
                    setSelectValue(`#${el.id}`, value);
                    return;
                }

                el.value = value || "";
                el.dispatchEvent(new Event("input", { bubbles: true }));
                el.dispatchEvent(new Event("change", { bubbles: true }));
            });

            if (filters.sort_by && filters.sort_dir) {
                const sortSelect = qs("#listSortSelect");
                if (sortSelect) {
                    sortSelect.value = `${filters.sort_by}|${filters.sort_dir}`;
                    sortSelect.dispatchEvent(new Event("change", { bubbles: true }));
                }
            }

            if (shouldReload) {
                debouncedKanbanReload(buildQueryString(filters));
            }
        } finally {
            setTimeout(() => {
                applyingSavedFilter = false;
            }, 250);
        }
    }

    function savedFilterOptionFilters(option) {
        return safeParseJSON(option?.dataset?.filters, {});
    }

    async function applySavedFilterOption(option) {
        if (!option || !option.value) return false;

        const filters = savedFilterOptionFilters(option);

        setScope("mine");
        await applyFiltersToForm(filters, { reload: true });

        updateSavedFilterManageButtons();

        if (typeof window.KanbanNextStepHoverFix === "function") {
            window.KanbanNextStepHoverFix();
        }

        return true;
    }

    function refreshSavedFilterSelect2(select) {
        if (!select || !isSelect2Ready(select)) return;

        const $select = $(select);
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.trigger("change.select2");
        }
    }

    function renderSavedFilterList() {
        const wrap = qs("#kbSavedFilterList");
        if (wrap) wrap.remove();
    }

    async function loadSavedFilters(selectAndApplyFirst = false) {
        const select = qs("#kbSavedFilterSelect");
        if (!select || loadingSavedFilters) return [];

        loadingSavedFilters = true;

        try {
            const data = await fetchJSON(ENDPOINTS.index);
            const settings = Array.isArray(data?.settings) ? data.settings : [];
            const previousValue = select.value;

            select.innerHTML = `<option value="">Gespeicherten Filter wählen…</option>`;

            settings.forEach((setting) => {
                const option = new Option(`${setting.is_default ? "★ " : ""}${setting.name}`, String(setting.id));
                option.dataset.default = setting.is_default ? "1" : "0";
                option.dataset.filters = JSON.stringify(setting.filters || {});
                select.appendChild(option);
            });

            renderSavedFilterList(settings);

            let valueToSelect = "";

            if (selectAndApplyFirst && settings.length) {
                const defaultSetting = settings.find((item) => !!item.is_default);
                valueToSelect = String((defaultSetting || settings[0]).id);
            } else if (previousValue && settings.some((item) => String(item.id) === String(previousValue))) {
                valueToSelect = String(previousValue);
            }

            if (valueToSelect) {
                select.value = valueToSelect;
                if (isSelect2Ready(select)) {
                    $(select).val(valueToSelect).trigger("change.select2");
                }
            } else {
                select.value = "";
                if (isSelect2Ready(select)) {
                    $(select).val("").trigger("change.select2");
                }
            }

            refreshSavedFilterSelect2(select);
            updateSavedFilterManageButtons();

            if (selectAndApplyFirst && valueToSelect) {
                const option = Array.from(select.options).find((opt) => String(opt.value) === String(valueToSelect));
                await applySavedFilterOption(option);
            }

            return settings;
        } catch (error) {
            console.warn("Saved filters could not be loaded", error);
            return [];
        } finally {
            loadingSavedFilters = false;
        }
    }

    async function saveCurrentFilter() {
        const name = await askFilterName("");
        if (!name) return;

        const filters = getFormFilters();

        try {
            const data = await fetchJSON(ENDPOINTS.store, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                },
                body: JSON.stringify({
                    name,
                    filters,
                    is_default: false,
                }),
            });

            await loadSavedFilters(false);

            const select = qs("#kbSavedFilterSelect");
            if (select && data.setting?.id) {
                select.value = String(data.setting.id);

                if (isSelect2Ready(select)) {
                    $(select).val(String(data.setting.id)).trigger("change.select2");
                }
            }

            setScope("mine");

            await notify(
                "Gespeichert",
                "Dein persönlicher Kanban-Filter wurde als neue Ansicht gespeichert.",
                "success"
            );
        } catch (error) {
            await notify("Fehler", error.message || "Filter konnte nicht gespeichert werden.", "error");
        }
    }

    function selectedSavedFilterOption() {
        const select = qs("#kbSavedFilterSelect");
        if (!select || !select.value) return null;
        return select.selectedOptions?.[0] || null;
    }

    function selectedSavedFilterId() {
        const option = selectedSavedFilterOption();
        return option?.value || null;
    }

    function selectedSavedFilterName() {
        const option = selectedSavedFilterOption();
        if (!option) return "";

        return String(option.textContent || "")
            .replace(/^★\s*/, "")
            .trim();
    }

    function savedFilterMenuActionButton(id) {
        const menu = qs("#kbSavedFilterMenu");
        return (menu ? menu.querySelector(`#${id}`) : null) || qs(`#${id}`);
    }

    function cleanupDuplicatedSavedFilterActionButtons() {
        const menu = qs("#kbSavedFilterMenu");
        const topBar = qs(".kb-filter-mode-actions");
        if (!menu) return;

        [
            "kbUpdateCurrentFilter",
            "kbRenameCurrentFilter",
            "kbSetDefaultFilter",
            "kbDeleteCurrentFilter",
        ].forEach((id) => {
            const all = Array.from(document.querySelectorAll(`#${id}`));
            const inside = all.find((node) => menu.contains(node));

            all.forEach((node) => {
                if (inside && node !== inside && topBar && topBar.contains(node)) {
                    node.remove();
                }
            });
        });
    }

    function updateSavedFilterManageButtons() {
        cleanupDuplicatedSavedFilterActionButtons();
        const hasSelected = !!selectedSavedFilterId();

        [
            "kbUpdateCurrentFilter",
            "kbRenameCurrentFilter",
            "kbSetDefaultFilter",
            "kbDeleteCurrentFilter",
        ].forEach((id) => {
            const btn = savedFilterMenuActionButton(id);
            if (btn) btn.disabled = !hasSelected;
        });

        const menuBtn = qs("#kbSavedFilterMenuBtn");
        if (menuBtn) {
            menuBtn.disabled = !hasSelected;
            menuBtn.setAttribute("aria-disabled", hasSelected ? "false" : "true");
            menuBtn.setAttribute("aria-expanded", "false");
        }

        const menuWrap = qs("#kbSavedFilterMenuWrap");
        if (menuWrap && !hasSelected) {
            menuWrap.classList.remove("is-open");
        }
    }

    async function updateSelectedFilterWithCurrentForm() {
        const id = selectedSavedFilterId();

        if (!id) {
            await notify("Kein Filter gewählt", "Bitte zuerst einen gespeicherten Filter auswählen.", "info");
            return;
        }

        const name = selectedSavedFilterName() || "Mein Filter";
        const filters = getFormFilters();

        try {
            const data = await fetchJSON(endpointUrl(ENDPOINTS.update, id), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                },
                body: JSON.stringify({ name, filters }),
            });

            await loadSavedFilters(false);

            const select = qs("#kbSavedFilterSelect");
            if (select) {
                select.value = String(data.setting?.id || id);

                if (isSelect2Ready(select)) {
                    $(select).val(select.value).trigger("change.select2");
                }
            }

            updateSavedFilterManageButtons();

            await notify(
                "Aktualisiert",
                "Der ausgewählte Filter wurde mit den aktuellen Einstellungen überschrieben.",
                "success"
            );
        } catch (error) {
            await notify("Fehler", error.message || "Filter konnte nicht aktualisiert werden.", "error");
        }
    }

    async function renameSelectedFilter() {
        const id = selectedSavedFilterId();

        if (!id) {
            await notify("Kein Filter gewählt", "Bitte zuerst einen gespeicherten Filter auswählen.", "info");
            return;
        }

        const currentName = selectedSavedFilterName();
        const name = await askFilterName(currentName);

        if (!name) return;

        const option = selectedSavedFilterOption();
        const filters = savedFilterOptionFilters(option);

        try {
            const data = await fetchJSON(endpointUrl(ENDPOINTS.update, id), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                },
                body: JSON.stringify({ name, filters }),
            });

            await loadSavedFilters(false);

            const select = qs("#kbSavedFilterSelect");
            if (select) {
                select.value = String(data.setting?.id || id);

                if (isSelect2Ready(select)) {
                    $(select).val(select.value).trigger("change.select2");
                }
            }

            updateSavedFilterManageButtons();

            await notify("Umbenannt", "Der Filtername wurde aktualisiert.", "success");
        } catch (error) {
            await notify("Fehler", error.message || "Filter konnte nicht umbenannt werden.", "error");
        }
    }

    async function setSelectedFilterDefault() {
        const id = selectedSavedFilterId();

        if (!id) {
            await notify("Kein Filter gewählt", "Bitte zuerst einen gespeicherten Filter auswählen.", "info");
            return;
        }

        try {
            await fetchJSON(endpointUrl(ENDPOINTS.makeDefault, id), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                },
                body: JSON.stringify({}),
            });

            await loadSavedFilters(false);

            const select = qs("#kbSavedFilterSelect");
            if (select) {
                select.value = String(id);

                if (isSelect2Ready(select)) {
                    $(select).val(String(id)).trigger("change.select2");
                }
            }

            updateSavedFilterManageButtons();

            await notify(
                "Standard gesetzt",
                "Dieser Filter wird jetzt beim Klick auf „Meine Filter“ zuerst geladen.",
                "success"
            );
        } catch (error) {
            await notify("Fehler", error.message || "Standardfilter konnte nicht gesetzt werden.", "error");
        }
    }

    async function deleteSelectedFilter() {
        const id = selectedSavedFilterId();
        const name = selectedSavedFilterName();

        if (!id) {
            await notify("Kein Filter gewählt", "Bitte zuerst einen gespeicherten Filter auswählen.", "info");
            return;
        }

        let confirmed = true;

        if (window.KBModal?.confirm) {
            confirmed = await window.KBModal.confirm({
                title: "Filter löschen",
                text: `Soll „${name || "dieser Filter"}“ wirklich gelöscht werden?`,
                icon: "warning",
                confirmButtonText: "Löschen",
                cancelButtonText: "Abbrechen",
            });
        } else if (window.Swal?.fire) {
            const result = await window.Swal.fire({
                title: "Filter löschen",
                text: `Soll „${name || "dieser Filter"}“ wirklich gelöscht werden?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Löschen",
                cancelButtonText: "Abbrechen",
            });

            confirmed = !!result.isConfirmed;
        } else {
            confirmed = confirm(`Filter „${name || id}“ wirklich löschen?`);
        }

        if (!confirmed) return;

        try {
            await fetchJSON(endpointUrl(ENDPOINTS.destroy, id), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                },
                body: JSON.stringify({}),
            });

            const select = qs("#kbSavedFilterSelect");
            if (select) {
                select.value = "";

                if (isSelect2Ready(select)) {
                    $(select).val("").trigger("change.select2");
                }
            }

            await loadSavedFilters(false);
            updateSavedFilterManageButtons();

            await notify("Gelöscht", "Der gespeicherte Filter wurde gelöscht.", "success");
        } catch (error) {
            await notify("Fehler", error.message || "Filter konnte nicht gelöscht werden.", "error");
        }
    }

    function makeSection(id, title, subtitle, icon = "sliders", open = true) {
        const section = document.createElement("section");

        section.className = "kb-filter-section";
        section.dataset.section = id;

        section.innerHTML = `
            <button type="button" class="kb-filter-section-head" data-kb-filter-section-toggle="${id}">
                <span class="kb-filter-section-icon"><i class="feather icon-${icon}"></i></span>
                <span class="kb-filter-section-copy">
                    <strong>${title}</strong>
                    <small>${subtitle || ""}</small>
                </span>
                <span class="kb-filter-section-arrow"><i class="feather icon-chevron-down"></i></span>
            </button>
            <div class="kb-filter-section-body"></div>
        `;

        if (open) section.classList.add("is-open");

        return section;
    }

    function initFilterDrawerSections() {
        const form = qs("#kanbanFilterForm");
        if (!form || form.dataset.sectionsReady === "1") return;

        form.dataset.sectionsReady = "1";

        const sections = {
            customers: makeSection("customers", "Kunden & Pipeline", "Kunden, Phase und Lead-Alter filtern", "users", true),
            company: makeSection("company", "Unternehmen & Adresse", "Unternehmen wählen und passende Unternehmensadresse laden", "briefcase", true),
            team: makeSection("team", "Team & Produkt", "Mitarbeiter, Abteilung, Produkt und Interesse", "layers", true),
            date: makeSection("date", "Zeitraum", "Datum von/bis einschränken", "calendar", false),
            columns: makeSection("columns", "Spalten anzeigen", "Kanban-Spalten ein- oder ausblenden", "eye", false),
        };

        const map = [
            ["#customerFilter", "customers"],
            ["#stageFilter", "customers"],
            ["#leadAgeFilter", "customers"],
            ["#branchFilter", "company"],
            ["#branchAddressFilter", "company"],
            ["#employeeFilter", "team"],
            ["#departmentFilter", "team"],
            ["#productFilter", "team"],
            ["#interestFilter", "team"],
            ["#dateFrom", "date"],
            ["#dateTo", "date"],
            ["#columnTogglesContainer", "columns"],
        ];

        const originalChildren = Array.from(form.children);

        originalChildren.forEach((child) => {
            const matched = map.find(([selector]) => {
                return child.querySelector?.(selector) || child.matches?.(selector);
            });

            if (matched) {
                sections[matched[1]].querySelector(".kb-filter-section-body").appendChild(child);
            }
        });

        form.innerHTML = "";

        Object.values(sections).forEach((section) => {
            const body = section.querySelector(".kb-filter-section-body");
            if (body && body.children.length) {
                form.appendChild(section);
            }
        });

        const hint = document.createElement("div");
        hint.className = "kb-filter-hint";
        hint.innerHTML = 'Tipp: <kbd>Enter</kbd> = Anwenden, <kbd>Esc</kbd> = Schließen.';
        form.appendChild(hint);

        qsa("[data-kb-filter-section-toggle]", form).forEach((btn) => {
            btn.addEventListener("click", () => {
                const section = btn.closest(".kb-filter-section");
                section?.classList.toggle("is-open");
            });
        });

        if (window.feather?.replace) {
            window.feather.replace();
        }
    }

    function bindSavedFilterMenu() {
        const wrap = qs("#kbSavedFilterMenuWrap");
        const btn = qs("#kbSavedFilterMenuBtn");
        const menu = qs("#kbSavedFilterMenu");

        if (!wrap || !btn || !menu) return;

        cleanupDuplicatedSavedFilterActionButtons();

        if (wrap.dataset.savedFilterMenuBound !== "1") {
            wrap.dataset.savedFilterMenuBound = "1";

            btn.setAttribute("aria-haspopup", "true");
            btn.setAttribute("aria-expanded", "false");

            btn.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (btn.disabled) return;

                const willOpen = !wrap.classList.contains("is-open");

                qsa(".kb-filter-menu-wrap.is-open").forEach((openWrap) => {
                    if (openWrap !== wrap) openWrap.classList.remove("is-open");
                });

                wrap.classList.toggle("is-open", willOpen);
                btn.setAttribute("aria-expanded", willOpen ? "true" : "false");
            });

            menu.addEventListener("click", function (event) {
                const item = event.target.closest(".kb-filter-menu-item");
                if (!item || item.disabled) return;

                wrap.classList.remove("is-open");
                btn.setAttribute("aria-expanded", "false");
            });
        }

        if (document.documentElement.dataset.savedFilterMenuDocumentBound !== "1") {
            document.documentElement.dataset.savedFilterMenuDocumentBound = "1";

            document.addEventListener("click", function (event) {
                qsa(".kb-filter-menu-wrap.is-open").forEach((openWrap) => {
                    if (!openWrap.contains(event.target)) {
                        openWrap.classList.remove("is-open");
                        openWrap.querySelector("#kbSavedFilterMenuBtn")?.setAttribute("aria-expanded", "false");
                    }
                });
            });

            document.addEventListener("keydown", function (event) {
                if (event.key !== "Escape") return;

                qsa(".kb-filter-menu-wrap.is-open").forEach((openWrap) => {
                    openWrap.classList.remove("is-open");
                    openWrap.querySelector("#kbSavedFilterMenuBtn")?.setAttribute("aria-expanded", "false");
                });
            });
        }
    }

    function removeSavedFilterCards() {
        document.querySelectorAll(".kb-saved-filter-list, #kbSavedFilterList").forEach((el) => {
            el.remove();
        });
    }

    function bindSavedFilterUI() {
        removeSavedFilterCards();
        initFilterDrawerSections();
        initCustomerSelect2();
        bindSavedFilterMenu();

        const savedSelect = qs("#kbSavedFilterSelect");

        if (savedSelect && isSelect2Ready(savedSelect) && !$(savedSelect).hasClass("select2-hidden-accessible")) {
            $(savedSelect).select2({
                placeholder: "Gespeicherten Filter wählen…",
                allowClear: true,
                width: "260px",
                dropdownParent: $(document.body),
            });
        }

        qsa("[data-kb-filter-scope]").forEach((btn) => {
            if (btn.dataset.savedFilterBound === "1") return;
            btn.dataset.savedFilterBound = "1";

            btn.addEventListener("click", async () => {
                const scope = btn.dataset.kbFilterScope || "all";
                setScope(scope);

                if (scope === "mine") {
                    await loadSavedFilters(true);
                    return;
                }

                const select = qs("#kbSavedFilterSelect");
                if (select) {
                    setSelectValue("#kbSavedFilterSelect", "");
                }

                const clearBtn = qs("#btnClearFilters");
                if (clearBtn) {
                    clearBtn.click();
                    return;
                }

                const form = qs("#kanbanFilterForm");
                form?.reset();

                qsa("select", form || document).forEach((el) => {
                    if (!el.id) return;
                    setSelectValue(`#${el.id}`, el.multiple ? [] : "");
                });

                debouncedKanbanReload("");
            });
        });

        if (savedSelect && savedSelect.dataset.savedFilterBound !== "1") {
            savedSelect.dataset.savedFilterBound = "1";

            savedSelect.addEventListener("change", async (event) => {
                if (applyingSavedFilter || loadingSavedFilters) return;

                const select = event.target;
                const option = select?.selectedOptions?.[0];

                updateSavedFilterManageButtons();

                if (!select?.value || !option) return;

                await applySavedFilterOption(option);
            });

            if (isSelect2Ready(savedSelect)) {
                $(savedSelect)
                    .off("select2:select.kbSavedFilters")
                    .on("select2:select.kbSavedFilters", async function () {
                        if (applyingSavedFilter || loadingSavedFilters) return;

                        const option = savedSelect.selectedOptions?.[0];
                        updateSavedFilterManageButtons();

                        if (!savedSelect.value || !option) return;

                        await applySavedFilterOption(option);
                    });

                $(savedSelect)
                    .off("select2:clear.kbSavedFilters")
                    .on("select2:clear.kbSavedFilters", function () {
                        setScope("all");
                        updateSavedFilterManageButtons();

                        const clearBtn = qs("#btnClearFilters");
                        if (clearBtn) {
                            clearBtn.click();
                        } else {
                            debouncedKanbanReload("");
                        }
                    });
            }
        }

        const saveBtn = qs("#kbSaveCurrentFilter");
        if (saveBtn && saveBtn.dataset.savedFilterBound !== "1") {
            saveBtn.dataset.savedFilterBound = "1";
            saveBtn.addEventListener("click", saveCurrentFilter);
        }

        const updateFilterBtn = savedFilterMenuActionButton("kbUpdateCurrentFilter");
        if (updateFilterBtn && updateFilterBtn.dataset.savedFilterBound !== "1") {
            updateFilterBtn.dataset.savedFilterBound = "1";
            updateFilterBtn.addEventListener("click", updateSelectedFilterWithCurrentForm);
        }

        const renameFilterBtn = savedFilterMenuActionButton("kbRenameCurrentFilter");
        if (renameFilterBtn && renameFilterBtn.dataset.savedFilterBound !== "1") {
            renameFilterBtn.dataset.savedFilterBound = "1";
            renameFilterBtn.addEventListener("click", renameSelectedFilter);
        }

        const setDefaultFilterBtn = savedFilterMenuActionButton("kbSetDefaultFilter");
        if (setDefaultFilterBtn && setDefaultFilterBtn.dataset.savedFilterBound !== "1") {
            setDefaultFilterBtn.dataset.savedFilterBound = "1";
            setDefaultFilterBtn.addEventListener("click", setSelectedFilterDefault);
        }

        const deleteFilterBtn = savedFilterMenuActionButton("kbDeleteCurrentFilter");
        if (deleteFilterBtn && deleteFilterBtn.dataset.savedFilterBound !== "1") {
            deleteFilterBtn.dataset.savedFilterBound = "1";
            deleteFilterBtn.addEventListener("click", deleteSelectedFilter);
        }

        updateSavedFilterManageButtons();

        const branch = qs("#branchFilter");
        if (branch && branch.dataset.branchAddressBound !== "1") {
            branch.dataset.branchAddressBound = "1";

            const handler = async (event) => {
                if (applyingSavedFilter) return;

                const branchId = event.target.value || null;
                await loadBranchAddresses(branchId, null);
                setSelectValue("#branchAddressFilter", "");
            };

            branch.addEventListener("change", handler);

            if (isSelect2Ready(branch)) {
                $(branch)
                    .off("select2:select.kbBranchAddress select2:clear.kbBranchAddress")
                    .on("select2:select.kbBranchAddress select2:clear.kbBranchAddress", handler);
            }
        }

        if (!booted) {
            booted = true;

            const storedScope = localStorage.getItem("kanban_filter_scope") || "all";
            setScope(storedScope);

            loadSavedFilters(storedScope === "mine");
        } else {
            loadSavedFilters(false);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        setTimeout(bindSavedFilterUI, 120);
        setTimeout(bindSavedFilterUI, 650);
    });

    document.addEventListener("click", (event) => {
        if (event.target.closest("#btnOpenDrawer")) {
            setTimeout(bindSavedFilterUI, 120);
        }
    });

    window.KanbanSavedFilters = {
        loadSavedFilters,
        saveCurrentFilter,
        updateSelectedFilterWithCurrentForm,
        renameSelectedFilter,
        setSelectedFilterDefault,
        deleteSelectedFilter,
        applyFiltersToForm,
        loadBranchAddresses,
        getFormFilters,
        initCustomerSelect2,
        initFilterDrawerSections,
        renderSavedFilterList,
        bindSavedFilterMenu,
        bindSavedFilterUI,
    };
})();