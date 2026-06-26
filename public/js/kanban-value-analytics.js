/*
 * Kanban Value Analytics
 * Put in: public/js/kanban-value-analytics.js
 *
 * Load AFTER:
 * 1. public/js/kanban-boot-loader.js
 * 2. public/js/kanban.js
 * 3. public/js/kanban-saved-filters.js
 *
 * This version:
 * - uses the active Kanban filters
 * - separates Investment into two internal tabs:
 *   1. LeadStage Status + Unterphasen
 *   2. Kundenwert Tabelle
 * - uses a clean object-card layout inspired by the Brand/Partner page
 */
(function () {
    "use strict";

    if (window.__KANBAN_VALUE_ANALYTICS_READY__) return;
    window.__KANBAN_VALUE_ANALYTICS_READY__ = true;

    const ROUTES = window.KANBAN_VALUE_ANALYTICS_ROUTES || {};
    const ENDPOINT =
        ROUTES.index ||
        window.APP?.endpoints?.valueAnalytics ||
        window.KanbanAPP?.endpoints?.valueAnalytics ||
        "/lead/kanban/value-analytics";

    const qs = (selector, ctx = document) => ctx.querySelector(selector);
    const qsa = (selector, ctx = document) => Array.from(ctx.querySelectorAll(selector));

    let currentController = null;
    let lastQueryString = "";
    let lastData = null;
    let isBound = false;
    let filterDebounceTimer = null;
    let activeInnerTab = "stages";

    function escapeHTML(value) {
        return String(value ?? "").replace(/[&<>"']/g, function (m) {
            return {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;",
            }[m];
        });
    }

    function number(value) {
        return Number(value || 0);
    }

    function int(value) {
        return Math.round(number(value));
    }

    function money(value) {
        const n = Number(value || 0);

        try {
            return new Intl.NumberFormat("de-DE", {
                style: "currency",
                currency: "EUR",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(n);
        } catch (_) {
            return n.toFixed(2).replace(".", ",") + " €";
        }
    }

    function shortMoney(value) {
        const n = Number(value || 0);

        if (Math.abs(n) >= 1000000) {
            return new Intl.NumberFormat("de-DE", {
                maximumFractionDigits: 1,
                minimumFractionDigits: 0,
            }).format(n / 1000000) + " Mio. €";
        }

        if (Math.abs(n) >= 1000) {
            return new Intl.NumberFormat("de-DE", {
                maximumFractionDigits: 1,
                minimumFractionDigits: 0,
            }).format(n / 1000) + " Tsd. €";
        }

        return money(n);
    }

    function percent(value, max) {
        const n = number(value);
        const m = Math.max(1, number(max));
        return Math.max(2, Math.min(100, Math.round((n / m) * 100)));
    }

    function panel() {
        return qs("#kanbanValueAnalyticsPanel");
    }

    function isValueTabActive() {
        const pane = qs("#valueAnalytics");
        const tab = qs("#value-analytics-tab");

        return !!(
            pane?.classList.contains("active") ||
            pane?.classList.contains("show") ||
            tab?.classList.contains("active")
        );
    }

    function buildFilterQS() {
        if (window.LeadUI?.filters?.buildFilterQS) {
            return window.LeadUI.filters.buildFilterQS();
        }

        if (typeof window.LeadUIBuildFilterQS === "function") {
            return window.LeadUIBuildFilterQS();
        }

        const form = qs("#kanbanFilterForm");
        const params = form ? new URLSearchParams(new FormData(form)) : new URLSearchParams();

        const sortValue = qs("#listSortSelect")?.value || "";
        if (sortValue.includes("|")) {
            const parts = sortValue.split("|");
            params.set("sort_by", parts[0] || "created_at");
            params.set("sort_dir", parts[1] || "desc");
        }

        params.delete("page");

        return params.toString();
    }

    function readActiveFilterLabels() {
        const form = qs("#kanbanFilterForm");
        const items = [];

        if (!form) return items;

        const interestingNames = [
            "customer",
            "stage",
            "sub_stage",
            "substage",
            "lead_stage_sub_stage_id",
            "employee",
            "department",
            "product",
            "branch",
            "branch_address",
            "interest",
            "date_from",
            "date_to",
            "lead_age",
            "status_group",
        ];

        interestingNames.forEach(function (name) {
            const el = form.elements[name];
            if (!el) return;

            let raw = "";

            if (el instanceof RadioNodeList) {
                raw = el.value || "";
            } else {
                raw = el.value || "";
            }

            raw = String(raw || "").trim();
            if (!raw) return;

            let label = raw;

            if (el.tagName === "SELECT") {
                const opt = el.options?.[el.selectedIndex];
                label = opt?.textContent?.trim() || raw;
            }

            items.push({
                name,
                label: label.replace(/\s+/g, " "),
            });
        });

        const urlParams = new URLSearchParams(buildFilterQS());
        if (urlParams.get("status_group") && !items.find((x) => x.name === "status_group")) {
            items.push({
                name: "status_group",
                label: urlParams.get("status_group"),
            });
        }

        return items.slice(0, 8);
    }

    function setLoading() {
        const root = panel();
        if (!root) return;

        root.innerHTML = `
            <div class="kva-loading">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span>Investment Analyse wird geladen …</span>
            </div>
        `;
    }

    function setError(error) {
        const root = panel();
        if (!root) return;

        root.innerHTML = `
            <div class="kva-error">
                <div class="kva-error-icon">!</div>
                <div>
                    <strong>Investment Analyse konnte nicht geladen werden.</strong>
                    <p>${escapeHTML(error?.message || "Unbekannter Fehler")}</p>
                    <button type="button" class="kva-btn kva-btn-primary" data-kva-refresh>
                        Erneut laden
                    </button>
                </div>
            </div>
        `;
    }

    async function fetchAnalytics(force = false) {
        const root = panel();
        if (!root) return;

        const queryString = buildFilterQS();

        if (!force && lastData && lastQueryString === queryString) {
            render(lastData);
            return;
        }

        lastQueryString = queryString;

        try {
            if (currentController) currentController.abort();
            currentController = new AbortController();

            setLoading();

            const url = ENDPOINT + (queryString ? "?" + queryString : "");
            const response = await fetch(url, {
                method: "GET",
                credentials: "same-origin",
                signal: currentController.signal,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            const text = await response.text();
            let data = {};

            try {
                data = text ? JSON.parse(text) : {};
            } catch (_) {
                data = {
                    success: false,
                    message: text || "Ungültige JSON Antwort vom Server.",
                };
            }

            if (!response.ok || data.success === false) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            lastData = data;
            render(data);
        } catch (error) {
            if (error.name === "AbortError") return;
            console.error("[Kanban Value Analytics]", error);
            setError(error);
        }
    }

    function statIcon(type) {
        const icons = {
            products: "box",
            customers: "users",
            min: "trending-down",
            max: "trending-up",
            avg: "activity",
            perProduct: "bar-chart-2",
        };

        return `<i class="feather icon-${icons[type] || "circle"}"></i>`;
    }

    function summaryCard(type, label, value, sub, tone = "blue") {
        return `
            <div class="kva-oc-stat">
                <div class="kva-oc-stat-icon ${escapeHTML(tone)}">
                    ${statIcon(type)}
                </div>
                <div class="kva-oc-stat-meta">
                    <div class="kva-oc-stat-label">${escapeHTML(label)}</div>
                    <div class="kva-oc-stat-value">${escapeHTML(value)}</div>
                    ${sub ? `<div class="kva-oc-stat-sub">${escapeHTML(sub)}</div>` : ""}
                </div>
            </div>
        `;
    }

    function filterChipsHTML() {
        const chips = readActiveFilterLabels();

        if (!chips.length) {
            return `
                <span class="kva-filter-chip kva-filter-chip-empty">
                    Keine Filter aktiv
                </span>
            `;
        }

        return chips.map(function (item) {
            return `
                <span class="kva-filter-chip" title="${escapeHTML(item.name)}">
                    ${escapeHTML(item.label)}
                </span>
            `;
        }).join("");
    }

    function render(data) {
        const root = panel();
        if (!root) return;

        const summary = data.summary || {};
        const stages = Array.isArray(data.stages) ? data.stages : [];
        const customers = Array.isArray(data.customers) ? data.customers : [];
        const stageCount = stages.length;
        const customerCount = customers.length;

        root.innerHTML = `
            <div class="kva-oc-wrap">
                <div class="kva-oc-header">
                    <div class="kva-oc-titlebar">
                        <div>
                            <div class="kva-oc-title">Investment Analyse</div>
                            <div class="kva-oc-sub">
                                Firmenwert, Kundenwert, LeadStage Status und Unterphasen. Die aktiven Kanban-Filter werden automatisch übernommen.
                            </div>
                            <div class="kva-filter-chip-row">
                                ${filterChipsHTML()}
                            </div>
                        </div>

                        <div class="kva-oc-actions">
                            <button type="button" class="kva-btn kva-btn-soft" data-kva-refresh>
                                <i class="feather icon-refresh-cw"></i>
                                Aktualisieren
                            </button>
                            <button type="button" class="kva-btn kva-btn-primary" data-kva-open-filter>
                                <i class="feather icon-filter"></i>
                                Filter öffnen
                            </button>
                        </div>
                    </div>

                    <div class="kva-oc-analytics">
                        ${summaryCard("products", "Produkte / Anfragen", String(summary.product_count || 0), `${summary.product_type_count || 0} Produkttypen`, "published")}
                        ${summaryCard("customers", "Kunden", String(summary.customer_count || 0), `${summary.object_count || 0} Objekte / Häuser`, "total")}
                        ${summaryCard("min", "Firmenwert Min", money(summary.total_min), "untere Schätzung", "warning")}
                        ${summaryCard("max", "Firmenwert Max", money(summary.total_max), "obere Schätzung", "published")}
                        ${summaryCard("avg", "Firmenwert Ø", money(summary.total_avg), "Min + Max / 2", "type")}
                        ${summaryCard("perProduct", "Ø pro Produkt", money(summary.avg_value_per_product), "durchschnittlicher Einzelwert", "total")}
                    </div>

                    <div class="kva-oc-toolbar">
                        <div class="kva-oc-toolbar-left">
                            <div class="kva-filter-block search">
                                <label class="kva-filter-label" for="kvaSearch">Suchen</label>
                                <input type="search"
                                       id="kvaSearch"
                                       class="kva-input"
                                       placeholder="Kunde, Objekt, Produkt, Phase oder Unterphase suchen …">
                            </div>

                            <div class="kva-filter-block">
                                <label class="kva-filter-label" for="kvaStageQuickFilter">LeadStage</label>
                                <select id="kvaStageQuickFilter" class="kva-select">
                                    <option value="">Alle LeadStages</option>
                                    ${stages.map(function (stage) {
                                        return `<option value="${escapeHTML(stage.key || stage.name || "")}">${escapeHTML(stage.name || stage.key || "Phase")}</option>`;
                                    }).join("")}
                                </select>
                            </div>
                        </div>

                        <div class="kva-oc-toolbar-right">
                            <div class="kva-mini-total">
                                <strong>${stageCount}</strong>
                                <span>LeadStages</span>
                            </div>
                            <div class="kva-mini-total">
                                <strong>${customerCount}</strong>
                                <span>Kundenzeilen</span>
                            </div>
                        </div>
                    </div>

                    <div class="kva-inner-tabs" role="tablist" aria-label="Investment Analyse Tabs">
                        <button type="button"
                                class="kva-inner-tab ${activeInnerTab === "stages" ? "is-active" : ""}"
                                data-kva-inner-tab="stages">
                            <i class="feather icon-git-branch"></i>
                            LeadStage Status + Unterphasen
                            <b>${stageCount}</b>
                        </button>

                        <button type="button"
                                class="kva-inner-tab ${activeInnerTab === "customers" ? "is-active" : ""}"
                                data-kva-inner-tab="customers">
                            <i class="feather icon-list"></i>
                            Kundenwert Tabelle
                            <b>${customerCount}</b>
                        </button>
                    </div>
                </div>

                <div class="kva-inner-panel ${activeInnerTab === "stages" ? "is-active" : ""}"
                     data-kva-panel="stages">
                    ${renderStagePanel(stages, summary)}
                </div>

                <div class="kva-inner-panel ${activeInnerTab === "customers" ? "is-active" : ""}"
                     data-kva-panel="customers">
                    ${renderCustomerPanel(customers, data)}
                </div>
            </div>
        `;

        const countEl = qs("#tabCountValueAnalytics");
        if (countEl) countEl.textContent = String(summary.product_count || 0);

        bindInnerTabs();
        bindSearchAndStageFilter();
        bindCustomerToggles();
        bindSubStageSidebar();
        refreshIcons();
    }

    function renderStagePanel(stages, summary) {
        const maxStageCount = Math.max(1, ...stages.map((stage) => number(stage.product_count)));
        const maxStageValue = Math.max(1, ...stages.map((stage) => number(stage.total_avg)));
        const totalSubStages = stages.reduce(function (sum, stage) {
            const subStages = Array.isArray(stage.sub_stages) ? stage.sub_stages : [];
            return sum + subStages.length;
        }, 0);

        return `
            <div class="kva-stage-layout">
                <div class="kva-stage-left">
                    <div class="kva-card-head kva-card-head-wide">
                        <div>
                            <h4>LeadStage Status</h4>
                            <p>Alle Phasen mit Produktanzahl, Mindestwert, Maximalwert und Durchschnittswert. Unterphasen sind standardmäßig ausgeblendet.</p>
                        </div>

                        <button type="button"
                                class="kva-btn kva-btn-soft kva-substage-open-all"
                                data-kva-open-substage="__all">
                            <i class="feather icon-sidebar"></i>
                            Unterphasen öffnen
                            <b>${totalSubStages}</b>
                        </button>
                    </div>

                    <div class="kva-stage-list">
                        ${stages.length ? stages.map((stage, index) => renderStageRow(stage, index, maxStageCount, maxStageValue)).join("") : renderEmptyBox("Keine LeadStage Daten gefunden.")}
                    </div>
                </div>

                <div class="kva-substage-backdrop" data-kva-close-substage></div>

                <aside class="kva-substage-sidebar"
                       data-kva-substage-sidebar
                       aria-hidden="true">
                    <div class="kva-substage-sidebar-head">
                        <div>
                            <span class="kva-sidebar-kicker">Collapsible Sidebar</span>
                            <h4 data-kva-substage-title>Unterphasen</h4>
                            <p data-kva-substage-subtitle>Wähle links eine LeadStage aus, um ihre Unterphasen hier zu sehen.</p>
                        </div>
                        <button type="button"
                                class="kva-substage-close"
                                data-kva-close-substage
                                aria-label="Unterphasen schließen">
                            <i class="feather icon-x"></i>
                        </button>
                    </div>

                    <div class="kva-substage-board">
                        ${stages.length ? stages.map(renderSubStageGroup).join("") : renderEmptyBox("Keine Unterphasen vorhanden.")}
                    </div>
                </aside>
            </div>
        `;
    }

    function renderStageRow(stage, index, maxStageCount, maxStageValue) {
        const color = stage.color || "#74b2d4";
        const count = int(stage.product_count);
        const value = number(stage.total_avg);
        const productPercent = percent(count, maxStageCount);
        const valuePercent = percent(value, maxStageValue);
        const subStages = Array.isArray(stage.sub_stages) ? stage.sub_stages : [];
        const activeSubStages = subStages.filter((x) => int(x.product_count) > 0).length;

        return `
            <div class="kva-stage-row"
                 data-kva-stage-card
                 data-stage-key="${escapeHTML(stage.key || "")}"
                 data-kva-search="${escapeHTML(stageSearchText(stage))}">
                <div class="kva-stage-index">${index + 1}</div>

                <div class="kva-stage-main">
                    <div class="kva-stage-title-row">
                        <span class="kva-stage-dot" style="background:${escapeHTML(color)}"></span>
                        <strong>${escapeHTML(stage.name || stage.key || "LeadStage")}</strong>
                        <span class="kva-status-pill green">${count} Produkte</span>
                    </div>

                    <div class="kva-stage-bars">
                        <div>
                            <span>Produktmenge</span>
                            <div class="kva-bar">
                                <i style="width:${productPercent}%;background:${escapeHTML(color)}"></i>
                            </div>
                        </div>
                        <div>
                            <span>Ø Wert</span>
                            <div class="kva-bar">
                                <i style="width:${valuePercent}%;background:#93c21c"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kva-stage-numbers">
                    <div>
                        <span>Min</span>
                        <strong>${shortMoney(stage.total_min)}</strong>
                    </div>
                    <div>
                        <span>Max</span>
                        <strong>${shortMoney(stage.total_max)}</strong>
                    </div>
                    <div>
                        <span>Ø</span>
                        <strong>${shortMoney(stage.total_avg)}</strong>
                    </div>
                    <div>
                        <span>Unterphasen</span>
                        <strong>${activeSubStages}/${subStages.length}</strong>
                    </div>
                </div>

                <div class="kva-stage-action-cell">
                    <button type="button"
                            class="kva-substage-open-btn"
                            data-kva-open-substage="${escapeHTML(stage.key || stage.name || "")}">
                        <i class="feather icon-sidebar"></i>
                        Öffnen
                    </button>
                </div>
            </div>
        `;
    }

    function renderSubStageGroup(stage) {
        const color = stage.color || "#74b2d4";
        const subStages = Array.isArray(stage.sub_stages) ? stage.sub_stages : [];
        const total = int(stage.product_count);

        return `
            <div class="kva-substage-group"
                 data-kva-stage-card
                 data-stage-key="${escapeHTML(stage.key || "")}"
                 data-stage-name="${escapeHTML(stage.name || stage.key || "LeadStage")}"
                 data-kva-search="${escapeHTML(stageSearchText(stage))}">
                <div class="kva-substage-group-head">
                    <div>
                        <span class="kva-stage-dot" style="background:${escapeHTML(color)}"></span>
                        <strong>${escapeHTML(stage.name || stage.key || "LeadStage")}</strong>
                    </div>
                    <span class="kva-status-pill blue">${total} Produkte</span>
                </div>

                <div class="kva-substage-list-modern">
                    ${subStages.length ? subStages.map((sub) => renderSubStageLine(sub, total)).join("") : `
                        <div class="kva-empty-mini">Keine Unterphasen konfiguriert</div>
                    `}
                </div>
            </div>
        `;
    }

    function renderSubStageLine(subStage, stageTotal) {
        const color = subStage.color || "#cbd5e1";
        const count = int(subStage.product_count);
        const width = percent(count, Math.max(1, stageTotal));

        return `
            <div class="kva-substage-line"
                 data-kva-search="${escapeHTML(subStageSearchText(subStage))}">
                <div class="kva-substage-line-top">
                    <div>
                        <span style="background:${escapeHTML(color)}"></span>
                        <strong>${escapeHTML(subStage.name || subStage.key || "Unterphase")}</strong>
                    </div>
                    <b>${count}</b>
                </div>
                <div class="kva-bar">
                    <i style="width:${width}%;background:${escapeHTML(color)}"></i>
                </div>
                <div class="kva-substage-values">
                    <span>Min ${shortMoney(subStage.total_min)}</span>
                    <span>Ø ${shortMoney(subStage.total_avg)}</span>
                    <span>Max ${shortMoney(subStage.total_max)}</span>
                </div>
            </div>
        `;
    }

    function renderCustomerPanel(customers, data) {
        return `
            <div class="kva-oc-card">
                <div class="kva-list-head">
                    <div>Kunde</div>
                    <div>Objekte</div>
                    <div>Produkte</div>
                    <div>LeadStage Status</div>
                    <div>Min</div>
                    <div>Max</div>
                    <div>Ø Wert</div>
                    <div>Details</div>
                </div>

                <div class="kva-list" id="kvaCustomerRows">
                    ${customers.length ? customers.map(renderCustomerItem).join("") : renderEmptyBox("Keine Kundenwerte gefunden.")}
                </div>

                <div class="kva-list-footer">
                    ${customers.length} von ${data.total_customers_loaded || customers.length} Kunden geladen.
                </div>
            </div>
        `;
    }

    function renderCustomerItem(customer) {
        const safeId = String(customer.id || "");
        const objects = Array.isArray(customer.objects) ? customer.objects : [];
        const stages = Array.isArray(customer.stage_summaries) ? customer.stage_summaries : [];

        return `
            <div class="kva-item"
                 data-kva-customer-item="${escapeHTML(safeId)}"
                 data-kva-search="${escapeHTML(customerSearchText(customer))}">
                <div class="kva-item-row">
                    <div class="kva-cell kva-main-cell">
                        <span class="kva-cell-title">Kunde</span>
                        <div class="kva-customer-avatar">
                            ${escapeHTML(initials(customer.name || safeId))}
                        </div>
                        <div class="kva-main">
                            <div class="kva-ttl">${escapeHTML(customer.name || ("Kunde #" + safeId))}</div>
                            <div class="kva-subt">${customer.customer_no ? "#" + escapeHTML(customer.customer_no) : "Kunde ID " + escapeHTML(safeId)}</div>
                        </div>
                    </div>

                    <div class="kva-cell">
                        <span class="kva-cell-title">Objekte</span>
                        <span class="kva-id-badge">${int(customer.object_count)}</span>
                    </div>

                    <div class="kva-cell">
                        <span class="kva-cell-title">Produkte</span>
                        <strong>${int(customer.product_count)}</strong>
                        <small>${int(customer.product_type_count)} Typen</small>
                    </div>

                    <div class="kva-cell">
                        <span class="kva-cell-title">LeadStage Status</span>
                        <div class="kva-stage-chip-wrap">
                            ${stages.length ? stages.map(renderCustomerStageChip).join("") : `<span class="kva-empty-mini">Keine Phase</span>`}
                        </div>
                    </div>

                    <div class="kva-cell">
                        <span class="kva-cell-title">Min</span>
                        ${money(customer.total_min)}
                    </div>

                    <div class="kva-cell">
                        <span class="kva-cell-title">Max</span>
                        ${money(customer.total_max)}
                    </div>

                    <div class="kva-cell">
                        <span class="kva-cell-title">Ø Wert</span>
                        <strong>${money(customer.total_avg)}</strong>
                    </div>

                    <div class="kva-cell kva-actions-cell">
                        <button type="button"
                                class="kva-btn-ic primary"
                                data-kva-toggle-customer="${escapeHTML(safeId)}"
                                title="Objekte und Produkte anzeigen">
                            <i class="feather icon-chevron-down"></i>
                        </button>
                    </div>
                </div>

                <div class="kva-object-collapse d-none"
                     data-kva-object-parent="${escapeHTML(safeId)}">
                    <div class="kva-object-grid">
                        ${objects.length ? objects.map(renderObjectCard).join("") : renderEmptyBox("Keine Objektdetails vorhanden.")}
                    </div>
                </div>
            </div>
        `;
    }

    function renderCustomerStageChip(stage) {
        return `
            <span class="kva-customer-stage-chip" style="--kva-chip:${escapeHTML(stage.color || "#74b2d4")}">
                ${escapeHTML(stage.name || stage.key || "Phase")}
                <b>${int(stage.product_count)}</b>
            </span>
        `;
    }

    function renderObjectCard(object) {
        const products = Array.isArray(object.products) ? object.products : [];

        return `
            <div class="kva-object-card">
                <div class="kva-object-head">
                    <div>
                        <strong>${escapeHTML(object.name || "Objekt")}</strong>
                        <small>${int(object.product_count)} Produkte</small>
                    </div>
                    <b>${money(object.total_avg)}</b>
                </div>

                <div class="kva-object-values">
                    <span>Min <b>${shortMoney(object.total_min)}</b></span>
                    <span>Max <b>${shortMoney(object.total_max)}</b></span>
                </div>

                <div class="kva-product-list">
                    ${products.length ? products.map(renderProductLine).join("") : `<div class="kva-empty-mini">Keine Produktdetails</div>`}
                </div>
            </div>
        `;
    }

    function renderProductLine(product) {
        return `
            <div class="kva-product-line"
                 data-kva-search="${escapeHTML(productSearchText(product))}">
                <div>
                    <strong>${escapeHTML(product.name || "Produkt")}</strong>
                    <small>
                        ${escapeHTML(product.stage_name || "")}
                        · ${escapeHTML(product.sub_stage_name || "Ohne Unterphase")}
                        · ${int(product.count)}×
                    </small>
                </div>
                <div>
                    <span>${shortMoney(product.sum_min)}</span>
                    <b>${shortMoney(product.sum_avg)}</b>
                    <span>${shortMoney(product.sum_max)}</span>
                </div>
            </div>
        `;
    }

    function renderEmptyBox(message) {
        return `
            <div class="kva-empty-box">
                <i class="feather icon-info"></i>
                <span>${escapeHTML(message)}</span>
            </div>
        `;
    }

    function initials(value) {
        const parts = String(value || "")
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        if (!parts.length) return "#";
        if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();

        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }

    function stageSearchText(stage) {
        const subStages = Array.isArray(stage.sub_stages) ? stage.sub_stages : [];
        return [
            stage.key,
            stage.name,
            stage.product_count,
            stage.total_min,
            stage.total_max,
            stage.total_avg,
            ...subStages.flatMap((sub) => [
                sub.key,
                sub.name,
                sub.product_count,
                sub.total_min,
                sub.total_max,
                sub.total_avg,
            ]),
        ].filter(Boolean).join(" ").toLowerCase();
    }

    function subStageSearchText(subStage) {
        return [
            subStage.key,
            subStage.name,
            subStage.product_count,
            subStage.total_min,
            subStage.total_max,
            subStage.total_avg,
        ].filter(Boolean).join(" ").toLowerCase();
    }

    function productSearchText(product) {
        return [
            product.name,
            product.stage_name,
            product.sub_stage_name,
            product.count,
            product.sum_min,
            product.sum_avg,
            product.sum_max,
        ].filter(Boolean).join(" ").toLowerCase();
    }

    function customerSearchText(customer) {
        const objects = Array.isArray(customer.objects) ? customer.objects : [];
        const stages = Array.isArray(customer.stage_summaries) ? customer.stage_summaries : [];

        const pieces = [
            customer.name,
            customer.customer_no,
            customer.product_count,
            customer.product_type_count,
            customer.total_min,
            customer.total_max,
            customer.total_avg,
            ...stages.flatMap((stage) => [stage.key, stage.name]),
            ...objects.flatMap((object) => {
                const products = Array.isArray(object.products) ? object.products : [];

                return [
                    object.name,
                    object.total_min,
                    object.total_max,
                    object.total_avg,
                    ...products.flatMap((product) => [
                        product.name,
                        product.stage_name,
                        product.sub_stage_name,
                    ]),
                ];
            }),
        ];

        return pieces.filter(Boolean).join(" ").toLowerCase();
    }

    function applyClientFilters() {
        const root = panel();
        if (!root) return;

        const term = String(qs("#kvaSearch")?.value || "").trim().toLowerCase();
        const stageFilter = String(qs("#kvaStageQuickFilter")?.value || "").trim().toLowerCase();

        qsa("[data-kva-stage-card]", root).forEach(function (card) {
            const search = String(card.dataset.kvaSearch || "").toLowerCase();
            const key = String(card.dataset.stageKey || "").toLowerCase();

            const matchesTerm = !term || search.includes(term);
            const matchesStage = !stageFilter || key === stageFilter || search.includes(stageFilter);

            card.classList.toggle("d-none", !(matchesTerm && matchesStage));
        });

        qsa("[data-kva-customer-item]", root).forEach(function (item) {
            const search = String(item.dataset.kvaSearch || "").toLowerCase();

            const matchesTerm = !term || search.includes(term);
            const matchesStage = !stageFilter || search.includes(stageFilter);

            item.classList.toggle("d-none", !(matchesTerm && matchesStage));
        });
    }

    function bindSearchAndStageFilter() {
        const search = qs("#kvaSearch");
        const stageFilter = qs("#kvaStageQuickFilter");

        search?.addEventListener("input", applyClientFilters);
        stageFilter?.addEventListener("change", applyClientFilters);
    }

    function bindInnerTabs() {
        qsa("[data-kva-inner-tab]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                activeInnerTab = btn.dataset.kvaInnerTab || "stages";

                qsa("[data-kva-inner-tab]").forEach(function (item) {
                    item.classList.toggle("is-active", item === btn);
                });

                qsa("[data-kva-panel]").forEach(function (panelEl) {
                    panelEl.classList.toggle("is-active", panelEl.dataset.kvaPanel === activeInnerTab);
                });

                applyClientFilters();
                refreshIcons();
            });
        });
    }

    function bindCustomerToggles() {
        qsa("[data-kva-toggle-customer]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                const id = btn.dataset.kvaToggleCustomer || "";
                const item = btn.closest("[data-kva-customer-item]");
                const body = qs(`[data-kva-object-parent="${cssEscape(id)}"]`);

                if (!body) return;

                const isClosed = body.classList.contains("d-none");
                body.classList.toggle("d-none", !isClosed);
                item?.classList.toggle("is-open", isClosed);

                const icon = btn.querySelector("i");
                if (icon) {
                    icon.className = isClosed ? "feather icon-chevron-up" : "feather icon-chevron-down";
                }

                refreshIcons();
            });
        });
    }


    function bindSubStageSidebar() {
        const root = panel();
        if (!root) return;

        qsa("[data-kva-open-substage]", root).forEach(function (btn) {
            if (btn.dataset.kvaSubstageBound === "1") return;
            btn.dataset.kvaSubstageBound = "1";

            btn.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                openSubStageSidebar(btn.dataset.kvaOpenSubstage || "__all");
            });
        });

        qsa("[data-kva-close-substage]", root).forEach(function (btn) {
            if (btn.dataset.kvaSubstageCloseBound === "1") return;
            btn.dataset.kvaSubstageCloseBound = "1";

            btn.addEventListener("click", function (event) {
                event.preventDefault();
                closeSubStageSidebar();
            });
        });
    }

    function openSubStageSidebar(stageKey) {
        const root = panel();
        if (!root) return;

        const sidebar = qs("[data-kva-substage-sidebar]", root);
        if (!sidebar) return;

        const showAll = !stageKey || stageKey === "__all";
        const groups = qsa(".kva-substage-group", sidebar);
        let selectedName = "Alle Unterphasen";
        let visibleCount = 0;

        groups.forEach(function (group) {
            const key = String(group.dataset.stageKey || "");
            const visible = showAll || key === String(stageKey);
            group.classList.toggle("d-none", !visible);

            if (visible) {
                visibleCount += 1;
                if (!showAll) selectedName = group.dataset.stageName || key || "Unterphasen";
            }
        });

        qsa(".kva-stage-row", root).forEach(function (row) {
            const isActive = !showAll && String(row.dataset.stageKey || "") === String(stageKey);
            row.classList.toggle("is-substage-active", isActive);
        });

        const title = qs("[data-kva-substage-title]", sidebar);
        const subtitle = qs("[data-kva-substage-subtitle]", sidebar);
        if (title) title.textContent = selectedName;
        if (subtitle) {
            subtitle.textContent = showAll
                ? `${visibleCount} LeadStages mit ihren Unterphasen. Klicke links auf eine Phase, um zu filtern.`
                : "Unterphasen dieser LeadStage. Die Haupttabelle bleibt links vollständig sichtbar.";
        }

        sidebar.classList.add("is-open");
        sidebar.setAttribute("aria-hidden", "false");
        root.classList.add("kva-substage-sidebar-open");
        refreshIcons();
    }

    function closeSubStageSidebar() {
        const root = panel();
        if (!root) return;

        const sidebar = qs("[data-kva-substage-sidebar]", root);
        sidebar?.classList.remove("is-open");
        sidebar?.setAttribute("aria-hidden", "true");
        root.classList.remove("kva-substage-sidebar-open");

        qsa(".kva-stage-row", root).forEach(function (row) {
            row.classList.remove("is-substage-active");
        });
    }

    function cssEscape(value) {
        if (window.CSS?.escape) return CSS.escape(String(value));
        return String(value).replace(/[^a-zA-Z0-9_-]/g, "\\$&");
    }

    function refreshIcons() {
        requestAnimationFrame(function () {
            if (window.feather && typeof window.feather.replace === "function") {
                window.feather.replace();
            }
        });
    }

    function scheduleFilterRefresh() {
        window.clearTimeout(filterDebounceTimer);

        filterDebounceTimer = window.setTimeout(function () {
            if (isValueTabActive()) {
                fetchAnalytics(true);
            }
        }, 420);
    }

    function bindTabLoading() {
        if (isBound) return;
        isBound = true;

        const tab = qs("#value-analytics-tab");
        if (tab) {
            tab.addEventListener("click", function () {
                window.setTimeout(function () {
                    fetchAnalytics(false);
                }, 80);
            });

            if (window.jQuery && window.jQuery.fn.tab) {
                window.jQuery(tab).on("shown.bs.tab", function () {
                    fetchAnalytics(false);
                });
            }
        }

        document.addEventListener("click", function (event) {
            const refreshBtn = event.target.closest("[data-kva-refresh]");
            if (refreshBtn) {
                event.preventDefault();
                fetchAnalytics(true);
                return;
            }

            const filterBtn = event.target.closest("[data-kva-open-filter]");
            if (filterBtn) {
                event.preventDefault();

                const drawerBtn = qs("#btnOpenDrawer");
                if (drawerBtn) {
                    drawerBtn.click();
                }
            }
        });

        qsa("#btnApplyFilters, #btnResetFilters").forEach(function (button) {
            button.addEventListener("click", function () {
                setTimeout(function () {
                    if (isValueTabActive()) fetchAnalytics(true);
                }, 350);
            });
        });

        qsa("[data-status-group], [data-kb-filter-scope]").forEach(function (button) {
            button.addEventListener("click", function () {
                setTimeout(function () {
                    if (isValueTabActive()) fetchAnalytics(true);
                }, 350);
            });
        });

        const form = qs("#kanbanFilterForm");
        if (form) {
            form.addEventListener("change", scheduleFilterRefresh);
            form.addEventListener("input", function (event) {
                if (event.target?.matches?.("input, select, textarea")) {
                    scheduleFilterRefresh();
                }
            });
        }

        const sortSelect = qs("#listSortSelect");
        if (sortSelect) {
            sortSelect.addEventListener("change", scheduleFilterRefresh);
        }
    }

    function injectStyles() {
        if (qs("#kanban-value-analytics-style")) return;

        const style = document.createElement("style");
        style.id = "kanban-value-analytics-style";
        style.textContent = `
            :root {
                --kva-bg:#f3f4f6;
                --kva-card:#ffffff;
                --kva-text:#1f2937;
                --kva-muted:#6b7280;
                --kva-border:#e5e7eb;
                --kva-primary:#93c21c;
                --kva-primary-hover:#7baa18;
                --kva-primary-light:#f4fae7;
                --kva-blue:#74b2d4;
                --kva-blue-light:#eff6ff;
                --kva-success:#10b981;
                --kva-success-light:#ecfdf5;
                --kva-warning:#f59e0b;
                --kva-warning-light:#fffbeb;
                --kva-danger:#ef4444;
                --kva-danger-light:#fef2f2;
                --kva-gray:#6b7280;
                --kva-gray-light:#f3f4f6;
                --kva-shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
                --kva-shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
                --kva-radius:14px;
            }

            .kva-shell {
                min-height: 220px;
            }

            .kva-oc-wrap {
                font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: var(--kva-text);
                padding: 18px;
                background: var(--kva-bg);
                border-radius: 18px;
            }

            .kva-loading,
            .kva-error {
                min-height: 220px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                background: #fff;
                border: 1px solid var(--kva-border);
                border-radius: 16px;
                color: var(--kva-muted);
                font-weight: 900;
                padding: 22px;
            }

            .kva-error {
                justify-content: flex-start;
                align-items: flex-start;
                color: #991b1b;
                background: #fff;
            }

            .kva-error-icon {
                width: 38px;
                height: 38px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                background: var(--kva-danger-light);
                color: var(--kva-danger);
                font-size: 22px;
                font-weight: 950;
                flex: 0 0 auto;
            }

            .kva-error p {
                margin: 6px 0 12px;
                color: #7f1d1d;
                font-weight: 700;
                max-width: 860px;
                white-space: pre-wrap;
            }

            .kva-oc-header {
                margin-bottom: 16px;
            }

            .kva-oc-titlebar {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 16px;
                flex-wrap: wrap;
            }

            .kva-oc-title {
                font-size: 26px;
                font-weight: 950;
                letter-spacing: -.025em;
                color: #111827;
            }

            .kva-oc-sub {
                font-size: 14px;
                color: var(--kva-muted);
                margin-top: 4px;
                font-weight: 700;
            }

            .kva-filter-chip-row {
                display: flex;
                flex-wrap: wrap;
                gap: 7px;
                margin-top: 10px;
            }

            .kva-filter-chip {
                display: inline-flex;
                align-items: center;
                min-height: 28px;
                padding: 0 10px;
                border-radius: 999px;
                background: #fff;
                border: 1px solid var(--kva-border);
                color: #334155;
                font-size: 11px;
                font-weight: 950;
            }

            .kva-filter-chip-empty {
                color: var(--kva-muted);
                background: var(--kva-gray-light);
            }

            .kva-oc-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .kva-btn {
                border: 1px solid transparent;
                min-height: 40px;
                padding: 0 14px;
                border-radius: 10px;
                font-weight: 950;
                cursor: pointer;
                transition: all .18s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                text-decoration: none;
                font-size: 13px;
            }

            .kva-btn-primary {
                background: var(--kva-primary);
                border-color: var(--kva-primary);
                color: #fff;
            }

            .kva-btn-primary:hover {
                background: var(--kva-primary-hover);
                border-color: var(--kva-primary-hover);
                color: #fff;
                transform: translateY(-1px);
            }

            .kva-btn-soft {
                background: #fff;
                color: var(--kva-text);
                border-color: var(--kva-border);
            }

            .kva-btn-soft:hover {
                background: #f9fafb;
                color: var(--kva-text);
                transform: translateY(-1px);
            }

            .kva-oc-analytics {
                display: grid;
                grid-template-columns: repeat(6, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 18px;
            }

            @media(max-width:1600px) {
                .kva-oc-analytics {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media(max-width:860px) {
                .kva-oc-analytics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:620px) {
                .kva-oc-analytics {
                    grid-template-columns: 1fr;
                }
            }

            .kva-oc-stat {
                background: var(--kva-card);
                border: 1px solid var(--kva-border);
                border-radius: 16px;
                padding: 16px;
                box-shadow: var(--kva-shadow-sm);
                display: flex;
                align-items: center;
                gap: 12px;
                min-height: 92px;
            }

            .kva-oc-stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .kva-oc-stat-icon.total { background: var(--kva-blue-light); color: var(--kva-blue); }
            .kva-oc-stat-icon.published { background: var(--kva-success-light); color: var(--kva-success); }
            .kva-oc-stat-icon.warning { background: var(--kva-warning-light); color: #d97706; }
            .kva-oc-stat-icon.type { background: var(--kva-gray-light); color: var(--kva-gray); }

            .kva-oc-stat-meta { min-width: 0; }
            .kva-oc-stat-label {
                font-size: 11px;
                font-weight: 900;
                color: var(--kva-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .kva-oc-stat-value {
                font-size: 22px;
                font-weight: 950;
                color: #111827;
                line-height: 1.1;
                margin-top: 4px;
                word-break: break-word;
            }

            .kva-oc-stat-sub {
                font-size: 12px;
                color: var(--kva-muted);
                margin-top: 4px;
                font-weight: 700;
            }

            .kva-oc-toolbar {
                background: var(--kva-card);
                border: 1px solid var(--kva-border);
                border-radius: var(--kva-radius);
                padding: 14px 16px;
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                align-items: flex-end;
                justify-content: space-between;
                margin-bottom: 16px;
                box-shadow: var(--kva-shadow-sm);
            }

            .kva-oc-toolbar-left,
            .kva-oc-toolbar-right {
                display: flex;
                align-items: flex-end;
                gap: 12px;
                flex-wrap: wrap;
            }

            .kva-oc-toolbar-left { flex: 1; }

            .kva-filter-block {
                display: flex;
                flex-direction: column;
                gap: 6px;
                min-width: 190px;
            }

            .kva-filter-block.search {
                flex: 1;
                min-width: 280px;
            }

            .kva-filter-label {
                font-size: 11px;
                font-weight: 900;
                color: var(--kva-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .kva-input {
                background: #f9fafb;
                border: 1px solid var(--kva-border);
                border-radius: 8px;
                padding: 10px 12px 10px 36px;
                font-size: 14px;
                outline: none;
                min-width: 240px;
                width: 100%;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: 10px center;
                background-size: 16px;
            }

            .kva-select {
                background: #f9fafb;
                border: 1px solid var(--kva-border);
                border-radius: 8px;
                padding: 10px 12px;
                font-size: 14px;
                outline: none;
                min-width: 190px;
                width: 100%;
            }

            .kva-input:focus,
            .kva-select:focus {
                background-color: #fff;
                border-color: var(--kva-primary);
                box-shadow: 0 0 0 3px var(--kva-primary-light);
            }

            .kva-mini-total {
                min-width: 104px;
                background: #fff;
                border: 1px solid var(--kva-border);
                border-radius: 12px;
                padding: 9px 11px;
            }

            .kva-mini-total strong {
                display: block;
                color: #111827;
                font-size: 20px;
                font-weight: 950;
                line-height: 1.1;
            }

            .kva-mini-total span {
                display: block;
                color: var(--kva-muted);
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                margin-top: 3px;
            }

            .kva-inner-tabs {
                display: flex;
                gap: 8px;
                border-bottom: 1px solid var(--kva-border);
                margin-bottom: 0;
                overflow-x: auto;
            }

            .kva-inner-tab {
                min-height: 48px;
                border: 1px solid var(--kva-border);
                border-bottom: 0;
                border-radius: 16px 16px 0 0;
                background: #f9fafb;
                color: var(--kva-muted);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 0 16px;
                font-size: 13px;
                font-weight: 950;
                white-space: nowrap;
                cursor: pointer;
            }

            .kva-inner-tab:hover {
                background: #fff;
                color: #111827;
            }

            .kva-inner-tab.is-active {
                background: #fff;
                color: #111827;
                border-color: var(--kva-primary);
                box-shadow: 0 -2px 0 var(--kva-primary) inset;
            }

            .kva-inner-tab b {
                min-width: 22px;
                height: 22px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0 7px;
                border-radius: 999px;
                background: #e5e7eb;
                color: #374151;
                font-size: 11px;
            }

            .kva-inner-tab.is-active b {
                background: var(--kva-primary);
                color: #fff;
            }

            .kva-inner-panel {
                display: none;
                padding-top: 16px;
            }

            .kva-inner-panel.is-active {
                display: block;
            }

            .kva-stage-layout {
                position: relative;
                display: block;
                min-height: 520px;
            }

            .kva-stage-left,
            .kva-stage-right,
            .kva-oc-card {
                background: #fff;
                border: 1px solid var(--kva-border);
                border-radius: 16px;
                box-shadow: var(--kva-shadow-sm);
                overflow: hidden;
            }

            .kva-stage-left {
                width: 100%;
            }

            .kva-card-head-wide {
                align-items: center;
            }

            .kva-substage-open-all {
                white-space: nowrap;
            }

            .kva-substage-open-all b {
                min-width: 22px;
                height: 22px;
                padding: 0 7px;
                border-radius: 999px;
                background: rgba(15,23,42,.08);
                color: inherit;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
            }

            .kva-card-head {
                padding: 16px;
                border-bottom: 1px solid var(--kva-border);
                background: #fafafa;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .kva-card-head h4 {
                margin: 0;
                color: #111827;
                font-size: 16px;
                font-weight: 950;
            }

            .kva-card-head p {
                margin: 4px 0 0;
                color: var(--kva-muted);
                font-size: 12px;
                font-weight: 700;
            }

            .kva-stage-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 16px;
            }

            .kva-stage-row {
                display: grid;
                grid-template-columns: 48px minmax(280px, 1fr) minmax(520px, 1.25fr) 132px;
                gap: 14px;
                align-items: center;
                border: 1px solid var(--kva-border);
                border-radius: var(--kva-radius);
                padding: 14px;
                background: #fff;
                transition: all .18s ease;
            }

            .kva-stage-row:hover {
                border-color: var(--kva-primary);
                box-shadow: var(--kva-shadow);
            }

            @media(max-width:1050px) {
                .kva-stage-row {
                    grid-template-columns: 1fr;
                }

                .kva-stage-index {
                    width: max-content;
                }

                .kva-stage-action-cell {
                    justify-content: flex-start;
                }
            }

            .kva-stage-index {
                min-width: 42px;
                height: 36px;
                border-radius: 10px;
                background: var(--kva-blue-light);
                color: #0369a1;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 950;
            }

            .kva-stage-title-row,
            .kva-substage-group-head,
            .kva-substage-line-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .kva-stage-title-row {
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .kva-stage-dot {
                width: 12px;
                height: 12px;
                border-radius: 999px;
                display: inline-block;
                flex: 0 0 auto;
                box-shadow: 0 0 0 3px rgba(116,178,212,.16);
            }

            .kva-status-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 950;
                white-space: nowrap;
            }

            .kva-status-pill.green { background: #ecfdf5; color: #047857; }
            .kva-status-pill.blue { background: #eff6ff; color: #0369a1; }

            .kva-stage-bars {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
                margin-top: 10px;
            }

            @media(max-width:720px) {
                .kva-stage-bars {
                    grid-template-columns: 1fr;
                }
            }

            .kva-stage-bars span,
            .kva-substage-values span {
                color: var(--kva-muted);
                font-size: 11px;
                font-weight: 900;
            }

            .kva-bar {
                height: 8px;
                border-radius: 999px;
                background: #e5e7eb;
                overflow: hidden;
                margin-top: 5px;
            }

            .kva-bar i {
                display: block;
                height: 100%;
                border-radius: 999px;
            }

            .kva-stage-numbers {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px;
            }

            @media(max-width:720px) {
                .kva-stage-numbers {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            .kva-stage-numbers div {
                border: 1px solid var(--kva-border);
                border-radius: 12px;
                background: #f9fafb;
                padding: 9px 10px;
                min-width: 0;
            }

            .kva-stage-numbers span {
                display: block;
                font-size: 10px;
                font-weight: 950;
                text-transform: uppercase;
                color: var(--kva-muted);
                margin-bottom: 4px;
            }

            .kva-stage-numbers strong {
                display: block;
                font-size: 13px;
                color: #111827;
                font-weight: 950;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .kva-substage-board {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 16px;
                max-height: 760px;
                overflow: auto;
            }

            .kva-substage-group {
                border: 1px solid var(--kva-border);
                border-radius: var(--kva-radius);
                background: #fff;
                padding: 12px;
            }

            .kva-substage-group-head {
                padding-bottom: 10px;
                border-bottom: 1px solid #f1f5f9;
                margin-bottom: 10px;
            }

            .kva-substage-group-head > div {
                min-width: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .kva-substage-list-modern {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .kva-substage-line {
                background: #f9fafb;
                border: 1px solid #eef2f7;
                border-radius: 12px;
                padding: 10px;
            }

            .kva-substage-line-top > div {
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 0;
            }

            .kva-substage-line-top span {
                width: 10px;
                height: 10px;
                border-radius: 999px;
                flex: 0 0 auto;
            }

            .kva-substage-line-top strong {
                color: #111827;
                font-size: 13px;
                font-weight: 950;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .kva-substage-line-top b {
                color: #111827;
                font-size: 13px;
                font-weight: 950;
            }

            .kva-substage-values {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-top: 7px;
                flex-wrap: wrap;
            }

            .kva-stage-action-cell {
                display: flex;
                align-items: center;
                justify-content: flex-end;
            }

            .kva-substage-open-btn {
                min-height: 38px;
                border: 1px solid var(--kva-border);
                border-radius: 12px;
                background: #fff;
                color: #374151;
                padding: 0 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                font-size: 12px;
                font-weight: 950;
                cursor: pointer;
                transition: all .16s ease;
                white-space: nowrap;
            }

            .kva-substage-open-btn:hover {
                background: var(--kva-primary-light);
                border-color: var(--kva-primary);
                color: #365314;
                transform: translateY(-1px);
            }

            .kva-stage-row.is-substage-active {
                border-color: var(--kva-primary);
                box-shadow: 0 0 0 3px rgba(147,194,28,.15), var(--kva-shadow);
                background: linear-gradient(135deg, #fff 0%, #f7fbef 100%);
            }

            .kva-substage-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1498;
                background: rgba(15,23,42,.18);
                backdrop-filter: blur(2px);
                opacity: 0;
                pointer-events: none;
                transition: opacity .18s ease;
            }

            .kva-substage-sidebar-open .kva-substage-backdrop {
                opacity: 1;
                pointer-events: auto;
            }

            .kva-substage-sidebar {
                position: fixed;
                top: 0;
                right: 0;
                z-index: 1499;
                width: min(520px, 94vw);
                height: 100vh;
                background: #fff;
                border-left: 1px solid var(--kva-border);
                box-shadow: -26px 0 70px rgba(15,23,42,.20);
                transform: translateX(105%);
                transition: transform .24s ease;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .kva-substage-sidebar.is-open {
                transform: translateX(0);
            }

            .kva-substage-sidebar-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                padding: 18px;
                border-bottom: 1px solid var(--kva-border);
                background: linear-gradient(135deg, #ffffff 0%, #eef7fb 100%);
                flex: 0 0 auto;
            }

            .kva-sidebar-kicker {
                display: block;
                color: var(--kva-blue);
                font-size: 11px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .08em;
                margin-bottom: 4px;
            }

            .kva-substage-sidebar-head h4 {
                margin: 0;
                color: #111827;
                font-size: 18px;
                font-weight: 950;
                line-height: 1.2;
            }

            .kva-substage-sidebar-head p {
                margin: 5px 0 0;
                color: var(--kva-muted);
                font-size: 12px;
                font-weight: 750;
                line-height: 1.35;
            }

            .kva-substage-close {
                width: 38px;
                height: 38px;
                border: 1px solid var(--kva-border);
                border-radius: 13px;
                background: #fff;
                color: #374151;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .kva-substage-close:hover {
                background: #f8fafc;
                color: #111827;
            }

            .kva-substage-sidebar .kva-substage-board {
                flex: 1;
                min-height: 0;
                max-height: none;
                overflow: auto;
            }


            @media(max-width:760px) {
                .kva-card-head-wide {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .kva-substage-open-all,
                .kva-substage-open-btn {
                    width: 100%;
                }
            }

            .kva-list-head {
                display: grid;
                grid-template-columns: minmax(240px, 1.3fr) 100px 120px minmax(190px, 1fr) 130px 130px 140px 90px;
                gap: 14px;
                align-items: center;
                padding: 16px 16px 10px;
                color: var(--kva-muted);
                font-size: 11px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            @media(max-width:1350px) {
                .kva-list-head {
                    display: none;
                }
            }

            .kva-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 0 0 16px;
            }

            .kva-item {
                background: var(--kva-card);
                border: 1px solid var(--kva-border);
                border-radius: var(--kva-radius);
                transition: all .18s ease;
                overflow: hidden;
                margin: 0 16px;
            }

            .kva-item:hover,
            .kva-item.is-open {
                border-color: var(--kva-primary);
                box-shadow: var(--kva-shadow);
            }

            .kva-item-row {
                padding: 16px;
                display: grid;
                gap: 16px;
                align-items: center;
                grid-template-columns: minmax(240px, 1.3fr) 100px 120px minmax(190px, 1fr) 130px 130px 140px 90px;
            }

            @media(max-width:1350px) {
                .kva-item-row {
                    grid-template-columns: 1fr;
                }
            }

            .kva-cell {
                min-width: 0;
                color: #111827;
                font-size: 13px;
                font-weight: 800;
            }

            .kva-cell small {
                display: block;
                margin-top: 3px;
                color: var(--kva-muted);
                font-size: 12px;
                font-weight: 700;
            }

            .kva-cell-title {
                font-size: 11px;
                font-weight: 900;
                color: var(--kva-muted);
                text-transform: uppercase;
                margin-bottom: 4px;
                display: none;
            }

            @media(max-width:1350px) {
                .kva-cell-title {
                    display: block;
                }
            }

            .kva-main-cell {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            @media(max-width:1350px) {
                .kva-main-cell {
                    align-items: flex-start;
                }
            }

            .kva-customer-avatar {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: var(--kva-blue-light);
                color: #0369a1;
                font-size: 14px;
                font-weight: 950;
                flex: 0 0 auto;
            }

            .kva-main {
                display: flex;
                flex-direction: column;
                min-width: 0;
            }

            .kva-ttl {
                font-weight: 950;
                font-size: 15px;
                margin-bottom: 4px;
                color: #111827;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .kva-subt {
                font-size: 13px;
                color: var(--kva-muted);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .kva-id-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 54px;
                height: 36px;
                padding: 0 12px;
                border-radius: 10px;
                background: var(--kva-blue-light);
                color: #0369a1;
                font-size: 13px;
                font-weight: 950;
            }

            .kva-stage-chip-wrap {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .kva-customer-stage-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                min-height: 26px;
                border-radius: 999px;
                padding: 0 9px;
                background: color-mix(in srgb, var(--kva-chip) 14%, white);
                border: 1px solid color-mix(in srgb, var(--kva-chip) 35%, white);
                color: #111827;
                font-size: 11px;
                font-weight: 950;
                white-space: nowrap;
            }

            @supports not (background: color-mix(in srgb, red 10%, white)) {
                .kva-customer-stage-chip {
                    background: #f8fafc;
                    border-color: #e2e8f0;
                }
            }

            .kva-customer-stage-chip b {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 20px;
                height: 20px;
                border-radius: 999px;
                background: #fff;
                color: #111827;
            }

            .kva-actions-cell {
                display: flex;
                align-items: center;
                justify-content: flex-end;
            }

            .kva-btn-ic {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                border: 1px solid var(--kva-border);
                background: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: var(--kva-muted);
                cursor: pointer;
                transition: all .18s ease;
                text-decoration: none;
            }

            .kva-btn-ic:hover {
                background: #f9fafb;
                color: var(--kva-text);
                border-color: #d1d5db;
            }

            .kva-btn-ic.primary {
                color: var(--kva-primary);
                border-color: var(--kva-primary-light);
                background: var(--kva-primary-light);
            }

            .kva-btn-ic.primary:hover {
                border-color: var(--kva-primary);
            }

            .kva-object-collapse {
                border-top: 1px solid var(--kva-border);
                background: #f9fafb;
                padding: 14px 16px 16px;
            }

            .kva-object-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            @media(max-width:1000px) {
                .kva-object-grid {
                    grid-template-columns: 1fr;
                }
            }

            .kva-object-card {
                background: #fff;
                border: 1px solid var(--kva-border);
                border-radius: 14px;
                padding: 13px;
                box-shadow: var(--kva-shadow-sm);
            }

            .kva-object-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                border-bottom: 1px solid #f1f5f9;
                padding-bottom: 10px;
                margin-bottom: 10px;
            }

            .kva-object-head strong {
                display: block;
                color: #111827;
                font-size: 14px;
                font-weight: 950;
            }

            .kva-object-head small {
                display: block;
                margin-top: 3px;
                color: var(--kva-muted);
                font-size: 12px;
                font-weight: 700;
            }

            .kva-object-head > b {
                color: #111827;
                font-size: 14px;
                font-weight: 950;
                white-space: nowrap;
            }

            .kva-object-values {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }

            .kva-object-values span {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                min-height: 27px;
                padding: 0 8px;
                border-radius: 999px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                color: #475569;
                font-size: 11px;
                font-weight: 800;
            }

            .kva-product-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .kva-product-line {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 12px;
                align-items: center;
                border: 1px solid #eef2f7;
                background: #f9fafb;
                border-radius: 12px;
                padding: 10px;
            }

            @media(max-width:680px) {
                .kva-product-line {
                    grid-template-columns: 1fr;
                }
            }

            .kva-product-line strong {
                display: block;
                color: #111827;
                font-size: 13px;
                font-weight: 950;
            }

            .kva-product-line small {
                display: block;
                margin-top: 3px;
                color: var(--kva-muted);
                font-size: 12px;
                font-weight: 700;
            }

            .kva-product-line > div:last-child {
                display: grid;
                grid-template-columns: repeat(3, auto);
                gap: 8px;
                align-items: center;
                text-align: right;
                white-space: nowrap;
                color: var(--kva-muted);
                font-size: 11px;
                font-weight: 800;
            }

            .kva-product-line > div:last-child b {
                color: #111827;
                font-size: 12px;
            }

            .kva-list-footer {
                border-top: 1px solid var(--kva-border);
                background: #fafafa;
                color: var(--kva-muted);
                font-size: 12px;
                font-weight: 800;
                padding: 12px 16px;
            }

            .kva-empty-mini {
                border: 1px dashed #d1d5db;
                background: #f9fafb;
                color: var(--kva-muted);
                border-radius: 12px;
                padding: 10px 12px;
                font-size: 12px;
                font-weight: 800;
                text-align: center;
            }

            .kva-empty-box {
                border: 1px dashed #d1d5db;
                background: #fff;
                color: var(--kva-muted);
                border-radius: 14px;
                padding: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-weight: 850;
                min-height: 90px;
            }

            .d-none {
                display: none !important;
            }
        `;

        document.head.appendChild(style);
    }

    window.KanbanValueAnalyticsReload = function () {
        fetchAnalytics(true);
    };

    document.addEventListener("DOMContentLoaded", function () {
        injectStyles();
        bindTabLoading();

        if (isValueTabActive()) {
            fetchAnalytics(false);
        }
    });

    window.addEventListener("kanban:boot-ready", function () {
        bindTabLoading();
    });
})();
