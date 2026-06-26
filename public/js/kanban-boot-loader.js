/*
 * Kanban boot loader
 * Put in: public/js/kanban-boot-loader.js
 *
 * Load order:
 * 1. #kanban-external-boot textarea in Blade
 * 2. kanban-boot-loader.js
 * 3. kanban.js
 * 4. kanban-saved-filters.js
 * 5. kanban-value-analytics.js
 */
(function () {
    "use strict";

    if (window.__KANBAN_BOOT_LOADER_DONE__) return;
    window.__KANBAN_BOOT_LOADER_DONE__ = true;

    const DEFAULT_STAGE_NAMES = {
        lead: "Lead",
        offer: "Angebot",
        follow_up: "Nachfassen",
        accepted: "Annehmen",
        deal: "Auftrag",
        project: "Montage",
        completed: "Abschluss",
        archive: "Archive",
        junk: "Junk",
    };

    const SYSTEM_COLUMNS_TO_HIDE_FROM_MAIN_KANBAN = ["junk", "ticket"];

    const OLD_TERMIN_DOM_SELECTORS = [
        "#ap-drawer",
        "#ap-backdrop",
        "#ap-calendar",
        "#ap-calendar-wrap",
        "#ap-card-view",
        "#ap-form",
        "#ap-employee_ids",
        "#ap-emp-filter",
        "#ap-jump",
        '[data-menu="termin"]',
        "[data-ap-count]",
    ];

    const OLD_TERMIN_ENDPOINT_KEYS = [
        "appointmentsIndex",
        "appointmentsStore",
        "appointmentsUpdate",
        "appointmentsDestroy",
        "appointmentsCustomerSearch",
        "reportsIndex",
        "reportsReact",
        "reportsComment",
        "reportsStore",
    ];

    function isPlainObject(value) {
        return value !== null && typeof value === "object" && !Array.isArray(value);
    }

    function isNonEmptyPlainObject(value) {
        return isPlainObject(value) && Object.keys(value).length > 0;
    }

    function normalizeArrayOrObject(value, fallback) {
        if (Array.isArray(value) || isPlainObject(value)) return value;
        return fallback;
    }

    function safeConsoleError() {
        try {
            console.error.apply(console, arguments);
        } catch (_) {}
    }

    function safeConsoleWarn() {
        try {
            console.warn.apply(console, arguments);
        } catch (_) {}
    }

    function parseJSON(raw, fallback) {
        if (!raw) return fallback;

        try {
            const parsed = JSON.parse(String(raw).trim());
            return parsed === null || parsed === undefined ? fallback : parsed;
        } catch (error) {
            safeConsoleError("[Kanban] Invalid boot JSON:", error);
            return fallback;
        }
    }

    function parseBootTextarea() {
        const el = document.getElementById("kanban-external-boot");
        if (!el) return {};

        const raw = String(el.value || el.textContent || "").trim();
        if (!raw) return {};

        const parsed = parseJSON(raw, {});
        return isPlainObject(parsed) ? parsed : {};
    }

    function applyBootObject(boot) {
        if (!isPlainObject(boot)) return;

        Object.keys(boot).forEach(function (key) {
            if (!key) return;
            window[key] = boot[key];
        });
    }

    function withoutSystemColumns(names) {
        const out = {};

        Object.entries(names || {}).forEach(function ([key, label]) {
            const cleanKey = String(key || "").toLowerCase();

            if (SYSTEM_COLUMNS_TO_HIDE_FROM_MAIN_KANBAN.includes(cleanKey)) {
                return;
            }

            out[key] = label;
        });

        return out;
    }

    function ensureGlobalObject(name) {
        window[name] = isPlainObject(window[name]) ? window[name] : {};
        return window[name];
    }

    function ensureKanbanBootFallbacks() {
        const boot = ensureGlobalObject("KANBAN_BOOT");

        if (!isNonEmptyPlainObject(boot.leadStageNamesForJs)) {
            boot.leadStageNamesForJs = DEFAULT_STAGE_NAMES;
        }

        if (!isNonEmptyPlainObject(boot.kanbanStageNamesForJs)) {
            boot.kanbanStageNamesForJs = withoutSystemColumns(boot.leadStageNamesForJs);
        }

        boot.leadStageMetaForJs = isPlainObject(boot.leadStageMetaForJs)
            ? boot.leadStageMetaForJs
            : {};

        boot.kanbanStageMetaForJs = isPlainObject(boot.kanbanStageMetaForJs)
            ? boot.kanbanStageMetaForJs
            : withoutSystemColumns(boot.leadStageMetaForJs || {});

        boot.kanbanProductsForJs = Array.isArray(boot.kanbanProductsForJs)
            ? boot.kanbanProductsForJs
            : [];

        boot.employees = normalizeArrayOrObject(boot.employees, []);

        boot.branchColorMap = isPlainObject(boot.branchColorMap)
            ? boot.branchColorMap
            : {};

        boot.authUserId = boot.authUserId || "";

        /*
         |--------------------------------------------------------------------------
         | Route groups
         |--------------------------------------------------------------------------
         */
        ensureGlobalObject("KANBAN_CUSTOMER_PANEL_ROUTES");
        ensureGlobalObject("KANBAN_SAVED_FILTER_ROUTES");
        ensureGlobalObject("KANBAN_PERSONAL_TASK_PANEL_ROUTES");
        ensureGlobalObject("KANBAN_LEAD_TASK_ROUTES");

        /*
         |--------------------------------------------------------------------------
         | New value analytics route group
         |--------------------------------------------------------------------------
         | Used by:
         | public/js/kanban-value-analytics.js
         |--------------------------------------------------------------------------
         */
        ensureGlobalObject("KANBAN_VALUE_ANALYTICS_ROUTES");

        if (!window.KANBAN_VALUE_ANALYTICS_ROUTES.index) {
            window.KANBAN_VALUE_ANALYTICS_ROUTES.index = "/lead/kanban/value-analytics";
        }

        /*
         |--------------------------------------------------------------------------
         | Safety flags
         |--------------------------------------------------------------------------
         */
        window.KANBAN_DISABLE_OLD_TERMIN = true;
        window.KANBAN_DEFAULT_PANEL_TAB = window.KANBAN_DEFAULT_PANEL_TAB || "notes";

        boot.disableOldTermin = true;
        boot.defaultPanelTab = boot.defaultPanelTab || window.KANBAN_DEFAULT_PANEL_TAB;
    }

    function replaceAllSafe(value, search, replacement) {
        return String(value || "").split(search).join(replacement);
    }

    function normalizeRoutes() {
        /*
         * Blade placeholders:
         * "__TASK__", "__COMMENT__", "__KEY__", "__ID__", "__CUSTOMER__", etc.
         */
        window.KanbanRoute = window.KanbanRoute || function (template, replacements) {
            let url = String(template || "");

            Object.entries(replacements || {}).forEach(function ([key, value]) {
                const token = "__" + String(key).toUpperCase() + "__";
                url = replaceAllSafe(url, token, encodeURIComponent(value ?? ""));
            });

            return url;
        };

        window.KanbanUrl = window.KanbanUrl || window.KanbanRoute;
    }

    function getKanbanEndpoints() {
        return window.APP?.endpoints || window.KanbanAPP?.endpoints || null;
    }

    function removeOldTerminEndpointsWhenAppIsReady() {
        const endpoints = getKanbanEndpoints();
        if (!endpoints) return false;

        OLD_TERMIN_ENDPOINT_KEYS.forEach(function (key) {
            if (Object.prototype.hasOwnProperty.call(endpoints, key)) {
                delete endpoints[key];
            }
        });

        return true;
    }

    function removeOldTerminDom() {
        OLD_TERMIN_DOM_SELECTORS.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (el) {
                if (!el) return;

                const btn = el.closest?.('[data-menu="termin"]');
                const target = btn || el;

                try {
                    target.remove();
                } catch (_) {
                    if (target.parentNode) target.parentNode.removeChild(target);
                }
            });
        });
    }

    function installOldTerminClickBlocker() {
        if (window.__KANBAN_OLD_TERMIN_CLICK_BLOCKER__) return;
        window.__KANBAN_OLD_TERMIN_CLICK_BLOCKER__ = true;

        document.addEventListener(
            "click",
            function (event) {
                const oldTerminBtn = event.target.closest?.('[data-menu="termin"]');
                if (!oldTerminBtn) return;

                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();

                const card = oldTerminBtn.closest(".card, tr[data-customer-id], [data-card-id]");
                const notesBtn = card ? card.querySelector("[data-open-notes]") : null;

                if (notesBtn) {
                    notesBtn.click();
                    return;
                }

                safeConsoleWarn(
                    "[Kanban] Old Termin button is disabled. Use Termin Bericht inside customer panel."
                );
            },
            true
        );
    }

    function installDomSafetyObserver() {
        if (!window.MutationObserver) return;
        if (window.__KANBAN_OLD_TERMIN_DOM_OBSERVER__) return;

        window.__KANBAN_OLD_TERMIN_DOM_OBSERVER__ = true;

        const observer = new MutationObserver(function (mutations) {
            let shouldClean = false;

            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (!node || node.nodeType !== 1) return;

                    if (
                        node.matches?.('[data-menu="termin"], [data-ap-count], #ap-drawer, #ap-backdrop') ||
                        node.querySelector?.('[data-menu="termin"], [data-ap-count], #ap-drawer, #ap-backdrop')
                    ) {
                        shouldClean = true;
                    }
                });
            });

            if (shouldClean) {
                removeOldTerminDom();
                removeOldTerminEndpointsWhenAppIsReady();
            }
        });

        observer.observe(document.documentElement, {
            childList: true,
            subtree: true,
        });
    }

    function installEndpointCleanupRetry() {
        let attempts = 0;
        const maxAttempts = 40;

        const timer = window.setInterval(function () {
            attempts += 1;

            const cleaned = removeOldTerminEndpointsWhenAppIsReady();

            if (cleaned || attempts >= maxAttempts) {
                window.clearInterval(timer);
            }
        }, 250);
    }

    function installCardIdFallbacks() {
        function kbGlobalCardId(item) {
            item = item || {};

            const id =
                item.lead_product_id ||
                item.lead_product_list_id ||
                item.lpl_id ||
                item.id ||
                item.card_id ||
                item.cardId ||
                "";

            return "card-" + String(id || "").replace(/^card-/, "");
        }

        window.kbGlobalCardId = window.kbGlobalCardId || kbGlobalCardId;
        window.cardId = window.cardId || window.kbGlobalCardId;
        window.Cardid = window.Cardid || window.kbGlobalCardId;
        window.CardId = window.CardId || window.kbGlobalCardId;
    }

    function installSharedHelpers() {
        window.escapeHTML = window.escapeHTML || function (value) {
            return String(value ?? "").replace(/[&<>"']/g, function (m) {
                return {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;",
                }[m];
            });
        };

        window.featherRefreshSoon = window.featherRefreshSoon || function () {
            requestAnimationFrame(function () {
                if (window.feather && typeof window.feather.replace === "function") {
                    window.feather.replace();
                }
            });
        };

        window.KB_DND_MIME = window.KB_DND_MIME || "application/x-leadui-cards";
    }

    function dispatchBootReadyEvent() {
        try {
            window.dispatchEvent(
                new CustomEvent("kanban:boot-ready", {
                    detail: {
                        boot: window.KANBAN_BOOT || {},
                        customerPanelRoutes: window.KANBAN_CUSTOMER_PANEL_ROUTES || {},
                        savedFilterRoutes: window.KANBAN_SAVED_FILTER_ROUTES || {},
                        personalTaskRoutes: window.KANBAN_PERSONAL_TASK_PANEL_ROUTES || {},
                        leadTaskRoutes: window.KANBAN_LEAD_TASK_ROUTES || {},
                        valueAnalyticsRoutes: window.KANBAN_VALUE_ANALYTICS_ROUTES || {},
                    },
                })
            );
        } catch (_) {}
    }

    function onReady(callback) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", callback, { once: true });
            return;
        }

        callback();
    }

    /*
     |--------------------------------------------------------------------------
     | Execute boot
     |--------------------------------------------------------------------------
     */
    applyBootObject(parseBootTextarea());
    ensureKanbanBootFallbacks();
    normalizeRoutes();
    installCardIdFallbacks();
    installSharedHelpers();
    installOldTerminClickBlocker();
    dispatchBootReadyEvent();

    onReady(function () {
        removeOldTerminDom();
        removeOldTerminEndpointsWhenAppIsReady();
        installEndpointCleanupRetry();
        installDomSafetyObserver();

        window.ALL_EMPLOYEES = window.KANBAN_BOOT?.employees || [];
    });

    window.KanbanBootLoader = {
        parseBootTextarea,
        applyBootObject,
        ensureKanbanBootFallbacks,
        normalizeRoutes,
        removeOldTerminDom,
        removeOldTerminEndpointsWhenAppIsReady,
        installDomSafetyObserver,
    };
})();