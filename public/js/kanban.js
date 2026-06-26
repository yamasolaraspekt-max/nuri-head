/*
 * Kanban main script
 * Put in: public/js/kanban.js
 *
 * Load order:
 * 1. #kanban-external-boot textarea in Blade
 * 2. public/js/kanban-boot-loader.js
 * 3. public/js/kanban.js
 * 4. public/js/kanban-saved-filters.js
 * 5. public/js/kanban-value-analytics.js
 *
 * Important:
 * The boot loader is intentionally NOT included in this file anymore.
 * It must stay in public/js/kanban-boot-loader.js to avoid double booting,
 * duplicate route fallbacks, and hard-to-debug cached Hetzner scripts.
 */

/* ===== Early global card id fallback for Blade / cached Hetzner scripts ===== */
(function () {
  function kbGlobalCardId(item) {
    item = item || {};
    var id = item.lead_product_id || item.lead_product_list_id || item.lpl_id || item.id || item.card_id || item.cardId || '';
    return 'card-' + String(id || '').replace(/^card-/, '');
  }
  window.kbGlobalCardId = window.kbGlobalCardId || kbGlobalCardId;
  window.cardId = window.cardId || window.kbGlobalCardId;
  window.Cardid = window.Cardid || window.kbGlobalCardId;
  window.CardId = window.CardId || window.kbGlobalCardId;
})();

/*
 * Extracted Kanban JavaScript from Pasted text(489).txt
 * Inline <script> blocks only. External libraries must remain included in the Blade/layout.
 * Clean public JavaScript version. Dynamic Laravel values are read from window.KANBAN_BOOT.
 */


/* ===================== Extracted inline script block #1 ===================== */
      window.KB_DND_MIME = window.KB_DND_MIME || 'application/x-leadui-cards';
    

/* ===================== Extracted inline script block #10 ===================== */
              window.ALL_EMPLOYEES = window.KANBAN_BOOT?.employees || []; 
          

/* ===================== Extracted inline script block #11 ===================== */
            window.escapeHTML = window.escapeHTML || function(value) {
              return String(value ?? '').replace(/[&<>"']/g, function(m) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
              });
            };
            window.featherRefreshSoon = window.featherRefreshSoon || function() {
              requestAnimationFrame(function(){ if (window.feather && typeof window.feather.replace === 'function') window.feather.replace(); });
            };
          

/* ===================== Extracted inline script block #12 ===================== */
          /* =============================================================================
           * LeadUI – Core (Segment 1/2)
           * - Config, State, Storage, URL Sync
           * - Utilities + Polyfills
           * - Network layer: safeFetchJSON / postJSON
           * - Filters + Drawer
           * - Kanban renderers
           * - Notes drawer
           * - Junk partial loaders
           * - LiveFeed: per-card mini feed + full-screen modal (LiveFeedModal)
           * =============================================================================*/
          (function () {
            "use strict";

            /* --- Polyfills --- */
            window.requestIdleCallback ||= (cb) => setTimeout(() => cb({ timeRemaining: () => 10 }), 0);

            if (!window.CSS || !CSS.escape) {
              window.CSS = {
                ...(window.CSS || {}),
                escape: (s) => String(s).replace(/[^a-zA-Z0-9_\-]/g, "\\$&"),
              };
            }

            /* --- Config --- */
            const APP = {
              EMP_SRC: "/images/employee",
              endpoints: {
                kanbanSearch: "/lead/kanban/search", 
                listSearch: "/lead/kanban/ajax", 
                changeStage: "/lead-product/change-stage", 
                progress: "/lead-product/progress", 
                purge: "/lead-product/purge", 

                notesIndex: "/customer-notes", 
                notesStore: "/customer-notes", 
                notesInlineUpdate: (id) => `/customer-notes/inline-update/${id}`, 
                notesDestroy: (id) => `/customer-notes/delete/${id}`, 

                junk: "/lead/kanban/junk", 

                personalTasksIndex: "/personal-tasks/index", 
                personalTasksStore: "/personal-tasks/store", 
                personalTasksUpdate: (id) => `/personal-tasks/${id}/update`, 
                personalTasksDestroy: (id) => `/personal-tasks/${id}/destroy`, 
                ptEmployeesSync: (id) => `/personal-tasks/${id}/employees/sync`, 

                ptStepsIndex: (taskId) => `/personal-tasks/${taskId}/steps`, 
                ptStepsStore: (taskId) => `/personal-tasks/${taskId}/steps`, 
                ptStepsUpdate: (stepId) => `/personal-tasks/steps/${stepId}`, 
                ptStepsDestroy: (stepId) => `/personal-tasks/steps/${stepId}`, 
                ptStepsEmpSync: (stepId) => `/personal-tasks/steps/${stepId}/employees/sync`, 

                ticketize: (id) => `/lead-product/ticketize/${id}`,
                tickets: "/lead/kanban/tickets", 

                appointmentsIndex: "appointments/index", 
                appointmentsStore: "appointments/store", 
                appointmentsUpdate: (id) => `appointments/${id}/update`, 
                appointmentsDestroy: (id) => `appointments/${id}/destroy`, 
                appointmentsCustomerSearch: "appointments/customer-search", 

                reportsIndex: "/kanban/appointments/reports",
                reportsReact: (id) => "/kanban/appointments/reports/" + id + "/react",
                reportsComment: (id) => "/kanban/appointments/reports/" + id + "/comment",
                reportsStore: (appointmentId) => "/kanban/appointments/" + appointmentId + "/reports",

                customerReportsIndex: "/kanban/customer-reports", 
                customerReportsStore: "/kanban/customer-reports", 
                customerReportsComment: (id) => "/kanban/customer-reports/" + id + "/comment",

                liveFeed: "/lead/kanban/feed",

                remindersStore: "/kanban/reminders",
                remindersCardsSummary: "/kanban/reminders/cards-summary",

                leadStagesIndex: "/admin/lead-stages",
                leadStagesStore: "/admin/lead-stages",
                leadStagesUpdate: (id) => `/admin/lead-stages/${id}`,
                leadStagesDestroy: (id) => `/admin/lead-stages/${id}`,
                leadStagesReorder: "/lead-stages/reorder",
                leadStageSubStagesIndex: (id) => `/admin/kanban/stages/${id}/sub-stages`,
                updateLeadSubStage: (id) => `/kanban/lead-product/${encodeURIComponent(id)}/sub-stage`,

                stageWorkflowConfig: "/kanban-stage-workflow/config",
                stageWorkflowMove: (id) => `/kanban-stage-workflow/move/${id}`,
                stageWorkflowMoveNext: (id) => `/kanban-stage-workflow/move-next/${id}`,

                valueAnalytics: window.KANBAN_VALUE_ANALYTICS_ROUTES?.index || "/lead/kanban/value-analytics",
              },
              stageNames: window.KANBAN_BOOT?.leadStageNamesForJs || {},
              stageMeta: window.KANBAN_BOOT?.leadStageMetaForJs || {},
              kanbanStageNames: window.KANBAN_BOOT?.kanbanStageNamesForJs || {},
              kanbanStageMeta: window.KANBAN_BOOT?.kanbanStageMetaForJs || {},
              companyKanbanStageNames: window.KANBAN_BOOT?.kanbanStageNamesForJs || {},
              companyKanbanStageMeta: window.KANBAN_BOOT?.kanbanStageMetaForJs || {},
              products: window.KANBAN_BOOT?.kanbanProductsForJs || [],
              stageWorkflow: {
                mode: "company",
                productId: null,
                productStages: [],
                productStageMeta: {},
                productStageNames: {},
                previousProductFilter: undefined,
              },
              stageAlias: {
                open: "lead",
                neue: "lead",
                new: "lead",
                Lead: "lead",
                angebot: "offer",
                offer: "offer",
                nachfassen: "follow_up",
                follow_up: "follow_up",
                annehmen: "accepted",
                annemen: "accepted",
                angenommen: "accepted",
                accepted: "accepted",
                accept: "accepted",
                auftrag: "deal",
                deal: "deal",
                montage: "project",
                project: "project",
                abschluss: "completed",
                complete: "completed",
                completed: "completed",
                archiv: "archive",
                archive: "archive",
                reject: "junk",
                rejeck: "junk",
                junk: "junk",
              },
              defaults: {
                sort: { key: "created_at", dir: "desc" },
                page: 1,
              },
              authUserId: window.KANBAN_BOOT?.authUserId || "",
            };

            window.APP = APP;
            window.KanbanAPP = APP;


            function refreshEnterpriseKanbanRealtime() {
              try {
                if (window.LeadUI?.silentRefreshBoth) {
                  window.LeadUI.silentRefreshBoth();
                  return;
                }
                if (typeof window.LeadUIFetchKanban === 'function') {
                  window.LeadUIFetchKanban(State?.filtersQS || buildFilterQS?.() || '');
                }
              } catch (e) {
                console.warn('Realtime refresh failed', e);
              }
            }

            function initEnterpriseOfferConsistencyRealtime() {
              if (typeof window.Echo === 'undefined' || window.__enterpriseOfferConsistencyRealtime) return;
              window.__enterpriseOfferConsistencyRealtime = true;

              const handle = (event) => {
                const type = String(event?.type || event?.action || '').toLowerCase();
                if (
                  type.includes('kanban_offer_consistency') ||
                  type.includes('accepted_from_kanban') ||
                  type.includes('auto_cancelled_by_kanban') ||
                  type.includes('offer_sub_stage_synced_from_kanban') ||
                  type.includes('deal_sub_stage_synced_from_kanban')
                ) {
                  refreshEnterpriseKanbanRealtime();
                }
              };

              try {
                window.Echo.channel('offers')
                  .listen('OffersChanged', handle)
                  .listen('.OffersChanged', handle)
                  .listen('OfferFolderUpdated', handle)
                  .listen('.OfferFolderUpdated', handle);
              } catch (e) {
                console.warn('Offer realtime channel unavailable', e);
              }
            }

            initEnterpriseOfferConsistencyRealtime();

            const RUN = {
              badgeTone: { playing: "success", paused: "warning", stopped: "danger" },
              icon: { playing: "icon-play", paused: "icon-pause", stopped: "icon-square" },
              label: { playing: "Aktiv", paused: "Pausiert", stopped: "Gestoppt" },
            };

            /* --- Quill for Notes --- */
            let noteQuill = null;
            function ensureNoteQuill() {
                if (typeof window.Quill === "undefined") return null;
                if (noteQuill) return noteQuill;

                let editorHost = document.getElementById("noteEditor");
                const textarea = document.getElementById("noteText");

                if (!editorHost && textarea) {
                    editorHost = document.createElement("div");
                    editorHost.id = "noteEditor";
                    textarea.parentNode.insertBefore(editorHost, textarea);
                    textarea.style.display = "none";
                }

                if (!editorHost) return null;

                noteQuill = new Quill("#" + editorHost.id, {
                    theme: "snow",
                    placeholder: "Neue Notiz schreiben …",
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link']
                        ]
                    }
                });

                return noteQuill;
            }
            function getNoteEditorHTML() {
              const textarea = document.getElementById("noteText");
              if (noteQuill) {
                return (noteQuill.root.innerHTML || "").trim();
              }
              return (textarea?.value || "").trim();
            }
            function setNoteEditorHTML(html) {
              const textarea = document.getElementById("noteText");
              if (noteQuill) {
                noteQuill.root.innerHTML = html || "";
                try {
                  const len = noteQuill.getLength();
                  noteQuill.setSelection(len, len);
                } catch {}
              } else if (textarea) {
                textarea.value = html || "";
              }
            }

            /* --- State --- */
            const STORAGE_KEY = "leadOverview.filters.v4";
            const State = {
              sort: { ...APP.defaults.sort },
              page: APP.defaults.page,
              filtersQS: "",
              lastAppliedQS: "",
              lastKanbanData: [],
              loaded: { kanban: false, list: false },
              req: { kanban: null, list: null },
              statusGroup: null,
              selectedIds: new Set(),
            };

            /* --- Utils --- */
            const qs = (s, ctx = document) => ctx.querySelector(s);
            const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
            const CSRF = () => qs('meta[name="csrf-token"]')?.content || "";
            const isLikelyHTML = (t) => /^\s*</.test(t || "");
            const fmtDE = (v) => {
              try {
                return v ? new Date(v).toLocaleString("de-DE") : "";
              } catch {
                return "";
              }
            };

              const getDateAgeIndicator = (dateString, stage) => {
                // We removed the 'if (currentStage !== "lead") return;' check 
                // so this now runs for ALL columns.

                if (!dateString) return '';
                const targetDate = new Date(dateString);
                if (isNaN(targetDate.getTime())) return '';

                const now = new Date();
                const diffMs = now - targetDate;
                const diffHours = diffMs / (1000 * 60 * 60);

                let state = 'green';
                let title = 'Neu (Unter 24 Stunden)';

                if (diffHours > 48) {
                    state = 'red';
                    title = 'Überfällig (Älter als 48 Stunden)';
                } else if (diffHours > 24) {
                    state = 'orange';
                    title = 'Letzter Tag (Läuft in unter 24h ab)';
                }

                return `
                <div class="traffic-light-wrapper" title="${title}">
                    <span class="tl-dot tl-green ${state === 'green' ? 'is-active splash-green' : ''}"></span>
                    <span class="tl-dot tl-orange ${state === 'orange' ? 'is-active splash-orange' : ''}"></span>
                    <span class="tl-dot tl-red ${state === 'red' ? 'is-active splash-red' : ''}"></span>
                </div>`;
            };
            const featherRefreshSoon = () => {
              if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
            };
            const shortNum = (n) => {
              n = Number(n || 0);
              if (n < 1e3) return "" + n;
              if (n < 1e6) return (n / 1e3).toFixed(n % 1e3 ? 1 : 0).replace(/\.0$/, "") + "k";
              if (n < 1e9) return (n / 1e6).toFixed(n % 1e6 ? 1 : 0).replace(/\.0$/, "") + "M";
              return (n / 1e9).toFixed(n % 1e9 ? 1 : 0).replace(/\.0$/, "") + "B";
            };
            const canonicalStage = (s) => {
              const k = String(s || "").toLowerCase();
              if (k.startsWith("product_stage_")) return k;
              return APP.stageNames[k] ? k : APP.stageAlias[k] || "lead";
            };
            const escapeHTML = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));
            window.escapeHTML = escapeHTML;
            window.featherRefreshSoon = featherRefreshSoon;

            const branchSVG = (size = 14) => `
              <svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" style="vertical-align:-2px;">
                <path d="M3 21h18"/>
                <path d="M5 21V7a2 2 0 0 1 2-2h3v16"/>
                <path d="M10 21V4h7a2 2 0 0 1 2 2v15"/>
                <path d="M8 9h1"/>
                <path d="M8 12h1"/>
                <path d="M8 15h1"/>
                <path d="M13 9h1"/>
                <path d="M13 12h1"/>
                <path d="M13 15h1"/>
              </svg>
            `;

            function orderedStageEntries(namesObj) {
              const names = namesObj || {};
              const meta = APP.kanbanStageMeta || APP.stageMeta || {};
              return Object.entries(names).sort((a, b) => {
                const ao = Number(meta?.[a[0]]?.sort_order ?? 999999);
                const bo = Number(meta?.[b[0]]?.sort_order ?? 999999);
                if (ao !== bo) return ao - bo;
                return String(a[1]).localeCompare(String(b[1]), "de");
              });
            }
            window.orderedStageEntries = orderedStageEntries;

            const STAGE_ORDER = orderedStageEntries(APP.stageNames || {}).map(([key]) => key);
            const stageRank = (s) => STAGE_ORDER.indexOf(canonicalStage(s));
            const isBackward = (from, to) => stageRank(to) < stageRank(from);

            function enforceActionVisibility(cardOrStage) {
              const cards = cardOrStage && cardOrStage.nodeType === 1 ? [cardOrStage] : Array.from(document.querySelectorAll(".card"));
              cards.forEach((c) => {
                const stage = canonicalStage(c.dataset.stage || c.closest(".column")?.id || "lead");
                const hideJunk = stageRank(stage) >= stageRank("deal"); 
                const junkBtn = c.querySelector('[data-act="delete"]');
                if (junkBtn) {
                  junkBtn.disabled = hideJunk;
                  junkBtn.classList.toggle("d-none", hideJunk);
                  junkBtn.setAttribute("aria-hidden", hideJunk ? "true" : "false");
                }
              });
            }

            function stageFilterExcludes(newStage) {
              const p = new URLSearchParams(State.filtersQS || "");
              const f = p.get("stage");
              if (!f) return false;
              return canonicalStage(f) !== canonicalStage(newStage);
            }

            function saveToLocal() {
              try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                  sort: State.sort,
                  page: State.page,
                  filtersQS: State.filtersQS,
                  statusGroup: State.statusGroup,
                }));
              } catch {}
            }

            function restoreFromLocal() {
              try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) return;
                const { sort, page, filtersQS, statusGroup } = JSON.parse(raw);
                if (sort?.key && sort?.dir) State.sort = sort;
                if (page) State.page = Number(page) || 1;
                if (typeof filtersQS === "string") State.filtersQS = filtersQS;
                if (statusGroup === null || ["offen", "zusage", "absage"].includes(statusGroup)) State.statusGroup = statusGroup;
              } catch {}
            }

            function syncURL() {
              const url = new URL(location.href);
              const p = new URLSearchParams(State.filtersQS || "");
              p.set("sort_by", State.sort.key);
              p.set("sort_dir", State.sort.dir);
              p.set("page", String(State.page));
              const newQS = p.toString();
              if (url.search.slice(1) !== newQS) {
                url.search = newQS;
                history.replaceState(null, "", url.toString());
              }
            }

            function initFromURL() {
              const p = new URLSearchParams(location.search);
              const form = qs("#kanbanFilterForm");
              if (form && p.size) {
                p.forEach((v, k) => {
                  const el = form.elements[k];
                  if (el) {
                    try { el.value = v; } catch {}
                  }
                });
                if (window.jQuery) {
                  jQuery(form).find(".select2").each(function () {
                    const name = this.getAttribute("name");
                    if (name && p.has(name)) jQuery(this).val(p.get(name)).trigger("change");
                  });
                }
              }
              State.sort.key = p.get("sort_by") || State.sort.key;
              State.sort.dir = (p.get("sort_dir") || State.sort.dir).toLowerCase() === "asc" ? "asc" : "desc";
              State.page = parseInt(p.get("page") || State.page, 10) || 1;
              State.filtersQS = buildFilterQS();
            }

            /* --- Networking --- */
            function cancel(key) {
              try { State.req[key]?.abort(); } catch {}
              State.req[key] = new AbortController();
              return State.req[key].signal;
            }
            async function safeFetchJSON(url, { method = "GET", headers = {}, body, signal, retries = 0, retryDelay = 240 } = {}) {
              const go = async () => {
                const res = await fetch(url, {
                  method, credentials: "same-origin",
                  headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", ...headers },
                  body, signal,
                });
                const text = await res.text();
                let data = {};
                try { data = text ? JSON.parse(text) : {}; } catch { data = { message: text || `HTTP ${res.status} ${res.statusText}` }; }

                // Business decision from backend: not a technical error.
                // Example: the user must select which offer folder is accepted when entering Auftrag / Deal.
                if (data?.requires_offer_selection) {
                  return data;
                }

                if (!res.ok || isLikelyHTML(text) || data?.success === false) {
                  const message = data?.message || data?.help_text || `HTTP ${res.status} ${res.statusText}`;
                  const error = new Error(message);
                  error.status = res.status;
                  error.payload = data;
                  throw error;
                }

                return data;
              };
              try {
                return await go();
              } catch (err) {
                if (retries > 0 && method === "GET") {
                  await new Promise((r) => setTimeout(r, retryDelay));
                  return safeFetchJSON(url, { method, headers, body, signal, retries: retries - 1, retryDelay: retryDelay * 1.6 });
                }
                throw err;
              }
            }
            const postJSON = (url, payload = {}) =>
              safeFetchJSON(url, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() },
                body: JSON.stringify(payload),
              });

            function safeJSON(value, fallback = null) {
              if (value === null || value === undefined || value === '') return fallback;
              if (typeof value !== 'string') return value;
              try {
                return JSON.parse(value);
              } catch (error) {
                return fallback;
              }
            }

            /* --- Company/Product workflow stage support --- */
            function workflowColumnKey(item) {
              if (APP.stageWorkflow?.mode === "product") {
                const psId = Number(item?.product_stage_id || item?.productStageId || 0);
                return psId > 0 ? `product_stage_${psId}` : Object.keys(APP.stageWorkflow.productStageNames || {})[0] || "lead";
              }
              return canonicalStage(item?.stage || "lead");
            }

            function workflowLabel(key) {
              if (APP.stageWorkflow?.mode === "product") {
                return APP.stageWorkflow.productStageNames?.[key] || key;
              }
              return APP.stageNames?.[canonicalStage(key)] || key;
            }

            function workflowStageIdFromKey(key) {
              const m = String(key || "").match(/^product_stage_(\d+)$/);
              return m ? Number(m[1]) : null;
            }
            window.workflowColumnKey = workflowColumnKey;
            window.workflowLabel = workflowLabel;
            window.workflowStageIdFromKey = workflowStageIdFromKey;

            function initWorkflowProductSelect2() {
              if (!window.jQuery || !window.jQuery.fn.select2) return;
              const $sel = window.jQuery("#kbWorkflowProduct");
              if (!$sel.length) return;
              if ($sel.hasClass("select2-hidden-accessible")) $sel.select2("destroy");

              const formatProduct = (option) => {
                if (!option.id) return option.text;
                const el = option.element;
                const initial = el?.dataset?.initial || "";
                const name = el?.dataset?.name || option.text || "";
                return window.jQuery(`
                  <span class="kb-workflow-select2-option">
                    <span class="kb-workflow-select2-icon"><i class="feather icon-box"></i></span>
                    <span class="kb-workflow-select2-text">
                      <span class="kb-workflow-select2-title">${escapeHTML(name || option.text)}</span>
                      <span class="kb-workflow-select2-sub">${escapeHTML(initial ? ('Kürzel: ' + initial) : 'Produkt-Workflow')}</span>
                    </span>
                  </span>
                `);
              };

              $sel.select2({
                placeholder: "Produkt für Workflow wählen…",
                allowClear: true,
                width: "260px",
                dropdownParent: window.jQuery(document.body),
                templateResult: formatProduct,
                templateSelection: formatProduct,
                escapeMarkup: (m) => m,
              });

              setTimeout(() => { if (window.feather) window.feather.replace(); }, 30);
            }

            function syncWorkflowProductSelect(productId = null) {
              const productSelect = document.getElementById("kbWorkflowProduct");
              if (!productSelect) return;
              productSelect.disabled = APP.stageWorkflow.mode !== "product";
              if (productId !== null && productId !== undefined) productSelect.value = String(productId || "");
              if (window.jQuery && window.jQuery.fn.select2) {
                window.jQuery(productSelect).prop("disabled", productSelect.disabled).trigger("change.select2");
              }
            }

            function refreshWorkflowBoardFromCache() {
              const board = qs("#kanban");
              if (board) board.innerHTML = "";
              ensureColumns();
              renderKanbanDiff(State.lastKanbanData || []);
              updateCounts();
              featherRefreshSoon();
              enforceActionVisibility();
            }

            function reloadKanbanAfterWorkflowSwitch() {
              State.page = 1;
              State.filtersQS = buildFilterQS();
              saveToLocal();
              syncURL();

              if (typeof window.LeadUIFetchKanban === "function") {
                State.loaded.kanban = false;
                return window.LeadUIFetchKanban(State.filtersQS);
              }

              refreshWorkflowBoardFromCache();
              return Promise.resolve();
            }

            function setWorkflowMode(mode, productId = null) {
              APP.stageWorkflow.mode = mode === "product" ? "product" : "company";
              APP.stageWorkflow.productId = productId ? Number(productId) : null;
              document.querySelectorAll("[data-kb-workflow-mode]").forEach((btn) => {
                btn.classList.toggle("is-active", btn.dataset.kbWorkflowMode === APP.stageWorkflow.mode);
              });
              syncWorkflowProductSelect(productId);
              const hint = document.getElementById("kbWorkflowHint");
              if (hint) hint.textContent = APP.stageWorkflow.mode === "product" ? "Produktphasen aktiv" : "Unternehmensphasen aktiv";
            }

            async function loadWorkflowColumns(mode = "company", productId = null) {
              const productFilter = qs("#productFilter");

              if (mode === "product") {
                if (!productId) {
                  setWorkflowMode("product", null);
                  Swal.fire("Produkt wählen", "Bitte wählen Sie zuerst ein Produkt für den Produkt-Workflow.", "info");
                  return false;
                }

                if (APP.stageWorkflow.previousProductFilter === undefined) {
                  APP.stageWorkflow.previousProductFilter = productFilter ? productFilter.value : "";
                }

                setWorkflowMode("product", productId);

                if (productFilter) {
                  productFilter.value = String(productId);
                  if (window.jQuery && window.jQuery.fn.select2) window.jQuery(productFilter).trigger("change.select2");
                }

                const url = `${APP.endpoints.stageWorkflowConfig}?mode=product&product_id=${encodeURIComponent(productId)}`;
                const res = await safeFetchJSON(url);
                if (!res?.success) {
                  Swal.fire("Fehler", res?.message || "Produktphasen konnten nicht geladen werden.", "error");
                  return false;
                }

                const names = {};
                const meta = {};
                (res.stages || []).forEach((stage, idx) => {
                  const key = `product_stage_${stage.id}`;
                  names[key] = stage.name || `Produktphase #${stage.id}`;
                  meta[key] = {
                    id: stage.id,
                    key,
                    color: stage.color || "#93c21c",
                    icon: stage.icon || "layers",
                    sort_order: Number(stage.sort_order ?? ((idx + 1) * 10)),
                    phases: Array.isArray(stage.phases) ? stage.phases : [],
                    product_id: stage.product_id,
                    section_name: stage.section_name || "",
                  };
                });

                APP.stageWorkflow.productStages = res.stages || [];
                APP.stageWorkflow.productStageNames = names;
                APP.stageWorkflow.productStageMeta = meta;
                APP.kanbanStageNames = names;
                APP.kanbanStageMeta = meta;

                refreshWorkflowBoardFromCache();
                return true;
              }

              // Unternehmen: restore the real company stages and restore/clear the product filter.
              setWorkflowMode("company", null);
              APP.kanbanStageNames = { ...(APP.companyKanbanStageNames || APP.stageNames || {}) };
              APP.kanbanStageMeta = { ...(APP.companyKanbanStageMeta || APP.stageMeta || {}) };
              APP.stageWorkflow.productStages = [];
              APP.stageWorkflow.productStageNames = {};
              APP.stageWorkflow.productStageMeta = {};

              if (productFilter) {
                const oldValue = APP.stageWorkflow.previousProductFilter;
                productFilter.value = oldValue !== undefined ? String(oldValue || "") : "";
                if (window.jQuery && window.jQuery.fn.select2) window.jQuery(productFilter).trigger("change.select2");
              }
              APP.stageWorkflow.previousProductFilter = undefined;

              refreshWorkflowBoardFromCache();
              return true;
            }

            function setWorkflowToolbarDraft(mode = "company") {
              const switchEl = qs("#kbWorkflowSwitch");
              const productBox = qs("#kbWorkflowProductBox");
              const productSelect = qs("#kbWorkflowProduct");
              const applyBtn = qs("#kbWorkflowApplyProduct");
              const hint = qs("#kbWorkflowHint");

              qsa("[data-kb-workflow-mode]").forEach((btn) => {
                btn.classList.toggle("is-active", btn.dataset.kbWorkflowMode === mode);
              });

              if (mode === "product") {
                switchEl?.classList.add("is-product-draft");
                productBox?.classList.remove("d-none");
                if (productSelect) productSelect.disabled = false;
                if (applyBtn) applyBtn.disabled = !productSelect?.value;
                if (hint) hint.textContent = "Produkt wählen und Anwenden klicken";
              } else {
                switchEl?.classList.remove("is-product-draft");
                productBox?.classList.add("d-none");
                if (productSelect) productSelect.disabled = true;
                if (applyBtn) applyBtn.disabled = true;
                if (hint) hint.textContent = "Unternehmensphasen aktiv";
              }

              if (window.jQuery && window.jQuery.fn.select2 && productSelect) {
                window.jQuery(productSelect).prop("disabled", productSelect.disabled).trigger("change.select2");
              }
              featherRefreshSoon();
            }

            async function applyWorkflowFromToolbar() {
              const productId = qs("#kbWorkflowProduct")?.value || null;
              if (!productId) {
                Swal.fire("Produkt wählen", "Bitte wählen Sie zuerst ein Produkt aus und klicken Sie dann auf Anwenden.", "info");
                return false;
              }

              const ok = await loadWorkflowColumns("product", productId);
              if (!ok) return false;

              qs("#kbWorkflowSwitch")?.classList.remove("is-product-draft");
              const hint = qs("#kbWorkflowHint");
              if (hint) hint.textContent = "Produktphasen aktiv";
              await reloadKanbanAfterWorkflowSwitch();
              return true;
            }

            function bindWorkflowControls() {
              initWorkflowProductSelect2();

              const productSelect = qs("#kbWorkflowProduct");
              const applyBtn = qs("#kbWorkflowApplyProduct");

              qsa("[data-kb-workflow-mode]").forEach((btn) => {
                btn.addEventListener("click", async (e) => {
                  e.preventDefault();
                  e.stopImmediatePropagation();

                  const mode = btn.dataset.kbWorkflowMode || "company";

                  if (mode === "product") {
                    setWorkflowToolbarDraft("product");
                    return;
                  }

                  const ok = await loadWorkflowColumns("company", null);
                  if (ok) {
                    setWorkflowToolbarDraft("company");
                    await reloadKanbanAfterWorkflowSwitch();
                  }
                }, true);
              });

              productSelect?.addEventListener("change", (e) => {
                const hasProduct = !!e.target.value;
                if (applyBtn) applyBtn.disabled = !hasProduct;
                const hint = qs("#kbWorkflowHint");
                if (hint) hint.textContent = hasProduct ? "Jetzt Anwenden klicken" : "Produkt wählen und Anwenden klicken";
              }, true);

              applyBtn?.addEventListener("click", async (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();
                await applyWorkflowFromToolbar();
              }, true);

              // Initial UI state: company mode, product select hidden until Product tab is clicked.
              setWorkflowToolbarDraft(APP.stageWorkflow?.mode === "product" ? "product" : "company");
            }

            window.KanbanWorkflow = Object.assign(window.KanbanWorkflow || {}, {
              initWorkflowProductSelect2,
              syncWorkflowProductSelect,
              setWorkflowMode,
              setWorkflowToolbarDraft,
              loadWorkflowColumns,
              applyWorkflowFromToolbar,
              reloadKanbanAfterWorkflowSwitch,
            });

            /* --- Filters/UI --- */
            function initSelect2(root = null) {
              if (!window.jQuery || !jQuery.fn.select2) return;
              const $root = root ? jQuery(root) : jQuery("#sideDrawer");

              function stageTemplate(option, mode = "option") {
                if (!option.id) return option.text;
                const el = option.element;
                const color = el?.dataset?.color || APP.stageMeta?.[option.id]?.color || "#93c21c";
                const icon = el?.dataset?.icon || APP.stageMeta?.[option.id]?.icon || "circle";
                const label = option.text || APP.stageNames?.[option.id] || option.id;
                return jQuery(`
                  <span class="stage-select2-${mode}">
                    <span class="stage-color-dot" style="background:${escapeHTML(color)}"></span>
                    <span class="stage-select2-icon"><i class="feather icon-${escapeHTML(icon)}"></i></span>
                    <span class="stage-select2-label">${escapeHTML(label)}</span>
                  </span>
                `);
              }

              $root.find(".select2").each(function () {
                const $el = jQuery(this);
                if ($el.hasClass("select2-hidden-accessible")) $el.select2("destroy");
                const isStage = this.id === "stageFilter" || $el.hasClass("stage-color-select");
                const $dropdownParent = $root.closest(".drawer, .notes-drawer, .modal").length
                  ? $root.closest(".drawer, .notes-drawer, .modal")
                  : jQuery(document.body);

                $el.select2({
                  placeholder: "Auswählen…",
                  allowClear: true,
                  width: "100%",
                  dropdownParent: $dropdownParent,
                  templateResult: isStage ? (option) => stageTemplate(option, "option") : undefined,
                  templateSelection: isStage ? (option) => stageTemplate(option, "selection") : undefined,
                  escapeMarkup: (m) => m,
                });
              });

              setTimeout(() => { if (window.feather) window.feather.replace(); }, 30);
            }

            function getFilterValues() {
              const f = qs("#kanbanFilterForm");
              if (!f) return {};
              const fd = new FormData(f), obj = {};
              fd.forEach((v, k) => (obj[k] = v === "" ? null : v));
              return obj;
            }

          function updateFilterBadges() {
              const vals = getFilterValues(); 
              const keys = ["customer", "stage", "employee", "department", "product", "interest", "date_from", "date_to", "lead_age"];
              const n = keys.reduce((t, k) => t + (vals[k] && String(vals[k]).trim() ? 1 : 0), 0) + (State.statusGroup ? 1 : 0);
              const rail = qs("#filterBadge");
              const tab = qs("#tabFilterCount");
              const btn = qs("#btnOpenDrawer");
              if (rail) { rail.textContent = n; rail.classList.toggle("d-none", !n); }
              if (tab) { tab.textContent = n; tab.classList.toggle("d-none", !n); }
              if (btn) btn.classList.toggle("rail-btn--active", !!n);
            }

            function buildFilterQS() {
              const form = qs("#kanbanFilterForm") || document.createElement("form");
              const p = new URLSearchParams(new FormData(form));
              if (State.statusGroup) {
                p.set("status_group", State.statusGroup);
                p.delete("stage");
                const stageSel = qs("#stageFilter");
                if (stageSel) stageSel.value = "";
              } else {
                p.delete("status_group");
              }
              p.set("sort_by", State.sort.key);
              p.set("sort_dir", State.sort.dir);
              p.delete("page");
              return p.toString();
            }

            const Drawer = (() => {
              const el = qs("#sideDrawer"), bd = qs("#drawerBackdrop");
              function open() {
                el?.classList.add("open");
                bd?.classList.add("show");
                document.body.style.overflow = "hidden";
                setTimeout(initSelect2, 10);
                updateFilterBadges();
              }
              function close() {
                el?.classList.remove("open");
                bd?.classList.remove("show");
                document.body.style.overflow = "";
              }
              bd?.addEventListener("click", close);
              qsa("[data-close-drawer]").forEach((b) => b.addEventListener("click", close));
              qs("#btnOpenDrawer")?.addEventListener("click", open);
              return { open, close };
            })();

            function closeOverlays() {
              qs("#drawerBackdrop")?.classList.remove("show");
              qs("#sideDrawer")?.classList.remove("open");
              qs("#notesBackdrop")?.classList.remove("show");
              qs("#notesDrawer")?.classList.remove("open");
              document.body.style.overflow = "";
            }

            /* --- Kanban DOM --- */
          /* --- Kanban DOM --- */
          function ensureColumns() {
              const board = qs("#kanban");
              if (!board) return;
              if (board.querySelector(".column")) return;

              const frag = document.createDocumentFragment();
              orderedStageEntries(APP.kanbanStageNames || APP.stageNames).forEach(([id, title]) => {
                const col = document.createElement("div");
                const meta = APP.kanbanStageMeta?.[id] || APP.stageMeta?.[id] || {};
                const stageDbId = meta.id || meta.stage_id || null;
                const subStages = Array.isArray(meta.sub_stages) ? meta.sub_stages : (Array.isArray(meta.subStages) ? meta.subStages : []);
                const subStageCount = subStages.length || Number(meta.sub_stage_count || meta.sub_stages_count || meta.subStageCount || 0);
                const subStageUrl = stageDbId && APP.endpoints?.leadStageSubStagesIndex
                  ? APP.endpoints.leadStageSubStagesIndex(stageDbId)
                  : "#";

                const safeTitle = escapeHTML(title || id);
                const safeId = escapeHTML(id);
                const safeIcon = escapeHTML(meta.icon || "columns");

                const underStageButtonHTML = `
                  <button type="button"
                          class="kb-understage-btn"
                          data-understage-stage="${safeId}"
                          title="Unterphasen von ${safeTitle} anzeigen">
                    <i class="feather icon-git-branch"></i>
                    <span>Unterphasen</span>
                    <b>${subStageCount}</b>
                  </button>`;

                const subStageConfigButtonHTML = `
                  <span class="kb-column-substage-wrap">
                    <a href="${escapeHTML(subStageUrl)}"
                       class="kb-column-substage-btn"
                       data-substage-config-link="1"
                       title="Unterphasen für ${safeTitle} konfigurieren">
                      <i class="feather icon-settings"></i>
                    </a>
                    <span class="kb-column-substage-count" title="${subStageCount} Unterphasen">${subStageCount}</span>
                  </span>`;

                col.className = "column";
                col.id = id;
                col.ondragover = (e) => e.preventDefault();

                col.innerHTML = `
                  <h3 data-workflow-stage-key="${safeId}">
                    <span class="kb-column-head-left">
                      <span class="kb-column-title"><i class="feather icon-${safeIcon}"></i> ${safeTitle}</span>
                    </span>
                    <span class="kb-column-actions">
                      ${underStageButtonHTML}
                      <button type="button"
                              class="kb-toggle-analytics"
                              data-kb-toggle-analytics="${safeId}"
                              title="Analyse-Badges ein-/ausblenden">
                        <i class="feather icon-bar-chart-2"></i>
                      </button>
                      ${subStageConfigButtonHTML}
                      <span class="kb-header-counts" data-count-for="${safeId}" aria-live="polite" title="Gesamt / Neu / 24-48 Std. / Über 48 Std.">
                        <span class="kb-count-pill kb-count-pill--total" title="Gesamt">0</span>
                        <span class="kb-count-pill kb-count-pill--green" title="Neu / Unter 24 Stunden"><span class="kb-count-dot"></span>0</span>
                        <span class="kb-count-pill kb-count-pill--orange" title="24 bis 48 Stunden"><span class="kb-count-dot"></span>0</span>
                        <span class="kb-count-pill kb-count-pill--red" title="Überfällig / Älter als 48 Stunden"><span class="kb-count-dot"></span>0</span>
                      </span>
                    </span>
                  </h3>
                  <div class="column-toolbar">
                    <input type="text" class="col-search-input" data-col="${safeId}" placeholder="In ${safeTitle} suchen...">
                    <button type="button" class="col-sort-btn" data-col="${safeId}" data-sort="desc" title="Nach Datum sortieren">
                      <i class="feather icon-arrow-down"></i>
                    </button>
                  </div>
                  <div class="column-content"></div>
                `;

                const header = col.querySelector("h3");
                if (header) header.style.background = (window.localStorage?.getItem("kb_use_stage_colors") === "1" && meta.color) ? meta.color : "#93c21c";
                frag.appendChild(col);
              });

              board.appendChild(frag);
              bindColumnTools();
              featherRefreshSoon();
            }

          document.addEventListener("click", function (event) {
            const link = event.target.closest("[data-substage-config-link]");
            if (!link) return;
            event.preventDefault();
            event.stopPropagation();
            const header = link.closest("h3[data-workflow-stage-key]");
            const stageKey = header?.dataset?.workflowStageKey || "";
            const meta = (APP.kanbanStageMeta?.[stageKey] || APP.stageMeta?.[stageKey] || {});
            const stageDbId = meta.id || meta.stage_id || null;
            const stageName = (APP.kanbanStageNames || APP.stageNames || {})[stageKey] || meta.name || stageKey || "Hauptphase";
            if (!stageDbId) {
              if (window.Swal) Swal.fire("Fehler", "Unterphasen-Link konnte nicht erstellt werden. Stage-ID fehlt.", "error");
              else alert("Unterphasen-Link konnte nicht erstellt werden. Stage-ID fehlt.");
              return;
            }
            if (typeof window.openLeadStageSubstageConfig === "function") {
              window.openLeadStageSubstageConfig(stageDbId);
            }
          });

          // Column search + sorting
          function bindColumnTools() {
              // 1. Search Logic (Filters cards by text)
              document.querySelectorAll('.col-search-input').forEach(input => {
                  input.addEventListener('input', function() {
                      const colId = this.dataset.col;
                      const term = this.value.toLowerCase().trim();
                      const container = document.querySelector(`#${colId} .column-content`);
                      if (!container) return;

                      const cards = container.querySelectorAll('.card');
                      cards.forEach(card => {
                          // Check all text inside the card (Name, City, Tags, etc.)
                          const cardText = card.innerText.toLowerCase();
                          card.style.display = cardText.includes(term) ? '' : 'none';
                      });

                      updateCounts();
                  });
              });

              // 2. Date Sorting Logic (Sorts cards up/down)
              document.querySelectorAll('.col-sort-btn').forEach(btn => {
                  btn.addEventListener('click', function() {
                      const colId = this.dataset.col;
                      const isDesc = this.dataset.sort === 'desc';

                      // Toggle state
                      this.dataset.sort = isDesc ? 'asc' : 'desc';
                      this.innerHTML = isDesc ? '<i class="feather icon-arrow-up"></i>' : '<i class="feather icon-arrow-down"></i>';

                      const container = document.querySelector(`#${colId} .column-content`);
                      if (!container) return;

                      const cards = Array.from(container.querySelectorAll('.card'));

                      // Sort cards based on the data-updated-at attribute you already set
                      cards.sort((a, b) => {
                          const dateA = new Date(a.dataset.updatedAt || 0).getTime();
                          const dateB = new Date(b.dataset.updatedAt || 0).getTime();

                          return isDesc ? (dateB - dateA) : (dateA - dateB); 
                      });

                      // Re-append sorted cards to the DOM (this physically moves them)
                      cards.forEach(card => container.appendChild(card));

                      updateCounts();
                  });
              });
          }
            function clearColumns() {
              qsa(".column .column-content").forEach((el) => (el.innerHTML = ""));
              qsa("#kanban > :not(.column)").forEach((n) => n.remove());
            }

            const colContent = (s) => qs(`#${CSS.escape(s)} .column-content`);

            function getCardTrafficLightState(card) {
              if (!card) return null;

              const wrapper = card.querySelector(".traffic-light-wrapper");
              if (!wrapper) return null;

              if (wrapper.querySelector(".tl-green.is-active")) return "green";
              if (wrapper.querySelector(".tl-orange.is-active")) return "orange";
              if (wrapper.querySelector(".tl-red.is-active")) return "red";

              return null;
            }

            function updateCounts() {
              qsa(".column").forEach((col) => {
                const cards = Array.from(col.querySelectorAll(".column-content .card"))
                  .filter((card) => card.style.display !== "none");

                const counts = {
                  total: cards.length,
                  green: 0,
                  orange: 0,
                  red: 0,
                };

                cards.forEach((card) => {
                  const state = getCardTrafficLightState(card);

                  if (state === "green") counts.green++;
                  else if (state === "orange") counts.orange++;
                  else if (state === "red") counts.red++;
                });

                const oldBadge = col.querySelector(".count-badge");
                if (oldBadge) oldBadge.textContent = String(counts.total);

                const wrap = col.querySelector(".kb-header-counts");
                if (!wrap) return;

                const totalEl = wrap.querySelector(".kb-count-pill--total");
                const greenEl = wrap.querySelector(".kb-count-pill--green");
                const orangeEl = wrap.querySelector(".kb-count-pill--orange");
                const redEl = wrap.querySelector(".kb-count-pill--red");

                if (totalEl) totalEl.textContent = shortNum(counts.total);
                if (greenEl) greenEl.innerHTML = '<span class="kb-count-dot"></span>' + shortNum(counts.green);
                if (orangeEl) orangeEl.innerHTML = '<span class="kb-count-dot"></span>' + shortNum(counts.orange);
                if (redEl) redEl.innerHTML = '<span class="kb-count-dot"></span>' + shortNum(counts.red);

                wrap.setAttribute(
                  "title",
                  `Gesamt: ${counts.total} / Neu: ${counts.green} / 24-48 Std.: ${counts.orange} / Über 48 Std.: ${counts.red}`
                );
              });
            }


            /* -------------------------------------------------------------------------- */
            /* Under Stage Board                                                           */
            /* -------------------------------------------------------------------------- */
            APP.underStage = APP.underStage || { active: false, stageKey: null };
            APP.allLeads = APP.allLeads || [];

            function showKanbanLoading(message = "Lade Unterphasen...") {
              let loader = document.getElementById("kb-understage-loader");
              if (!loader) {
                loader = document.createElement("div");
                loader.id = "kb-understage-loader";
                loader.className = "kb-understage-loader";
                loader.innerHTML = `<div class="kb-understage-spinner"></div><div class="kb-understage-loader-text"></div>`;
                document.body.appendChild(loader);
              }
              const text = loader.querySelector(".kb-understage-loader-text");
              if (text) text.textContent = message;
              loader.classList.add("show");
            }

            function hideKanbanLoading() {
              document.getElementById("kb-understage-loader")?.classList.remove("show");
            }

            function underStageMeta(stageKey) {
              const key = canonicalStage(stageKey || 'lead');
              const meta = APP.companyKanbanStageMeta?.[key] || APP.kanbanStageMeta?.[key] || APP.stageMeta?.[key] || {};
              const subStages = Array.isArray(meta.sub_stages)
                ? meta.sub_stages
                : (Array.isArray(meta.subStages) ? meta.subStages : []);
              return { ...meta, sub_stages: subStages };
            }

            function getLeadSubStageId(item) {
              const raw = item?.lead_stage_sub_stage_id
                ?? item?.leadStageSubStageId
                ?? item?.lead_stage_substage_id
                ?? item?.stage_sub_stage_id
                ?? item?.sub_stage_id
                ?? item?.leadStageSubStage?.id
                ?? item?.lead_stage_sub_stage?.id
                ?? null;
              if (raw === null || raw === undefined || raw === '' || raw === 0 || raw === '0') return '';
              return String(raw);
            }

            function getLeadProductKey(item) {
              return String(item?.lead_product_id ?? item?.lead_product_list_id ?? item?.id ?? '');
            }

            function setLeadSubStageOnCachedData(leadProductId, subStageId, subStageMeta = null) {
              const updater = (arr) => {
                if (!Array.isArray(arr)) return;
                const item = arr.find((x) => getLeadProductKey(x) === String(leadProductId));
                if (!item) return;
                item.lead_stage_sub_stage_id = subStageId || null;
                item.lead_stage_sub_stage_name = subStageMeta?.name || '';
                item.lead_stage_sub_stage_color = subStageMeta?.color || '';
                item.lead_stage_sub_stage_icon = subStageMeta?.icon || '';
              };
              updater(APP.allLeads);
              updater(State?.lastKanbanData);
            }

            function findUnderStageMetaById(stageKey, subStageId) {
              const meta = underStageMeta(stageKey);
              const subStages = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
              return subStages.find((s) => String(s.id) === String(subStageId)) || null;
            }

            function setKanbanMainColumns() {
              APP.underStage.active = false;
              APP.underStage.stageKey = null;
              APP.kanbanStageNames = { ...(APP.companyKanbanStageNames || APP.stageNames || {}) };
              APP.kanbanStageMeta = { ...(APP.companyKanbanStageMeta || APP.stageMeta || {}) };
            }

            function openUnderStageSidebar(stageKey, meta = {}) {
              const sidebar = qs("#kbUnderstageSidebar");
              const backdrop = qs("#kbUnderstageSidebarBackdrop");
              const title = qs("#kbUnderstageSidebarTitle");
              const subtitle = qs("#kbUnderstageSidebarSubtitle");
              const stageLabel = meta.name || APP.stageNames?.[stageKey] || stageKey || "Hauptphase";

              if (title) title.textContent = `Unterphasen · ${stageLabel}`;
              if (subtitle) {
                subtitle.innerHTML = `Karten der Hauptphase <strong>${escapeHTML(stageLabel)}</strong>. Drag & Drop ändert nur die Unterphase, nicht die Hauptphase.`;
              }

              sidebar?.classList.add("open");
              sidebar?.setAttribute("aria-hidden", "false");
              backdrop?.classList.add("show");
              document.body.classList.add("kb-understage-sidebar-open");
              featherRefreshSoon();
            }

            function closeUnderStageSidebar() {
              qs("#kbUnderstageSidebar")?.classList.remove("open");
              qs("#kbUnderstageSidebar")?.setAttribute("aria-hidden", "true");
              qs("#kbUnderstageSidebarBackdrop")?.classList.remove("show");
              document.body.classList.remove("kb-understage-sidebar-open");
              APP.underStage.active = false;
              APP.underStage.stageKey = null;
            }

            function buildUnderStageColumns(stageKey) {
              const meta = underStageMeta(stageKey);
              const subStages = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
              const board = qs("#kbUnderstageBoard");
              if (!board) return;
              board.innerHTML = "";

              openUnderStageSidebar(stageKey, meta);

              if (!subStages.length) {
                const empty = document.createElement("div");
                empty.className = "kb-understage-sidebar-empty";
                empty.innerHTML = `Für <strong>${escapeHTML(meta.name || stageKey)}</strong> sind noch keine Unterphasen konfiguriert. Öffne <strong>Phasen konfigurieren</strong> und klicke bei der Hauptphase auf <strong>Unterphasen</strong>, um Unterphasen anzulegen.`;
                board.appendChild(empty);
              }

              const makeColumn = (sub) => {
                const id = sub?.id ? String(sub.id) : "";
                const name = sub?.name || "Ohne Unterphase";
                const icon = sub?.icon || (id ? "list" : "help-circle");
                const color = sub?.color || (id ? meta.color : "#64748b") || "#93c21c";
                const col = document.createElement("div");
                col.className = "column";
                col.dataset.subStageId = id;
                col.innerHTML = `
                  <h3 style="background:${escapeHTML(color)};">
                    <span class="kb-column-title"><i class="feather icon-${escapeHTML(icon)}"></i> ${escapeHTML(name)}</span>
                    <span class="kb-header-counts"><span class="kb-count-pill kb-count-pill--total" data-understage-count="${escapeHTML(id)}">0</span></span>
                  </h3>
                  <div class="column-content" data-understage-dropzone="1" data-stage-key="${escapeHTML(stageKey)}" data-sub-stage-id="${escapeHTML(id)}"></div>`;
                return col;
              };

              board.appendChild(makeColumn({ id: "", name: "Ohne Unterphase", icon: "help-circle", color: "#64748b" }));
              subStages.forEach((sub) => board.appendChild(makeColumn(sub)));
              featherRefreshSoon();
            }

            function renderUnderStageBoard(stageKey) {
              APP.underStage.active = true;
              APP.underStage.stageKey = stageKey;
              showKanbanLoading("Lade Unterphasen...");
              window.setTimeout(() => {
                try {
                  buildUnderStageColumns(stageKey);
                  let leads = [];
                  if (Array.isArray(APP.allLeads) && APP.allLeads.length) leads = APP.allLeads;
                  else if (Array.isArray(State?.lastKanbanData) && State.lastKanbanData.length) leads = State.lastKanbanData;
                  const mainStageLeads = leads.filter((item) => canonicalStage(item?.stage || item?.status || item?.company_stage || 'lead') === canonicalStage(stageKey));
                  mainStageLeads.forEach((item) => {
                    const subId = getLeadSubStageId(item);
                    const selector = `[data-understage-dropzone][data-sub-stage-id="${CSS.escape(subId)}"]`;
                    const zone = qs(selector) || qs('[data-understage-dropzone][data-sub-stage-id=""]');
                    if (!zone) return;
                    const card = mountOrUpdateCard(stageKey, item, null);
                    card.dataset.understageCard = "1";
                    card.dataset.leadStageSubStageId = subId;
                    const subName = item.lead_stage_sub_stage_name || item.sub_stage_name || "";
                    if (subName && !card.querySelector(".kb-understage-chip")) {
                      const chip = document.createElement("div");
                      chip.className = "kb-understage-chip";
                      chip.innerHTML = `<i class="feather icon-git-branch"></i>${escapeHTML(subName)}`;
                      card.appendChild(chip);
                    }
                    zone.appendChild(card);
                  });
                  updateUnderStageCounts();
                  featherRefreshSoon();
                } finally {
                  hideKanbanLoading();
                }
              }, 180);
            }

            function updateUnderStageCounts() {
              qsa("[data-understage-count]").forEach((pill) => {
                const subId = pill.getAttribute("data-understage-count") || "";
                const count = qsa(`[data-understage-dropzone][data-sub-stage-id="${CSS.escape(subId)}"] .card`).length;
                pill.textContent = shortNum(count);
              });
            }

            async function saveLeadSubStage(leadProductId, subStageId, reason = '') {
              if (!leadProductId) throw new Error("LeadProduct-ID fehlt.");
              const url = APP.endpoints?.updateLeadSubStage
                ? APP.endpoints.updateLeadSubStage(encodeURIComponent(leadProductId))
                : `/admin/kanban/lead-product/${encodeURIComponent(leadProductId)}/sub-stage`;
              const data = await postJSON(url, {
                lead_stage_sub_stage_id: subStageId || null,
                reason: reason || '',
              });
              if (!data?.success) throw new Error(data?.message || "Unterphase konnte nicht gespeichert werden.");
              return data;
            }

            async function askUnderStageReason(subStageId, stageKey) {
              const subMeta = findUnderStageMetaById(stageKey, subStageId);
              const targetLabel = subMeta?.name || (subStageId ? `Unterphase #${subStageId}` : 'Ohne Unterphase');

              if (!window.Swal) {
                return { confirmed: true, reason: '' };
              }

              const result = await Swal.fire({
                title: 'Unterphase ändern',
                html: `
                  <div style="text-align:left">
                    <div class="mb-2 small text-muted">Ziel-Unterphase</div>
                    <div style="border:1px solid #dbeafe;background:#f8fafc;border-radius:12px;padding:10px;font-weight:900;color:#0f172a;">
                      ${escapeHTML(targetLabel)}
                    </div>
                    <label class="small text-muted font-weight-bold text-uppercase mt-3">Grund / Notiz</label>
                    <textarea id="swal-understage-reason" class="form-control" rows="3" placeholder="Warum wird die Unterphase geändert?"></textarea>
                  </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                width: 520,
                preConfirm: () => ({
                  reason: document.getElementById('swal-understage-reason')?.value || '',
                }),
              });

              return { confirmed: !!result.isConfirmed, reason: result.value?.reason || '' };
            }

            document.addEventListener("click", function (event) {
              const btn = event.target.closest("[data-understage-stage]");
              if (!btn) return;
              event.preventDefault();
              event.stopPropagation();
              const stageKey = btn.dataset.understageStage;
              if (!stageKey) return;
              const hasData = (Array.isArray(APP.allLeads) && APP.allLeads.length) || (Array.isArray(State?.lastKanbanData) && State.lastKanbanData.length);
              if (!hasData && typeof window.LeadUIFetchKanban === 'function') {
                showKanbanLoading('Lade Daten...');
                Promise.resolve(window.LeadUIFetchKanban(State?.filtersQS || ''))
                  .finally(() => renderUnderStageBoard(stageKey));
                return;
              }
              renderUnderStageBoard(stageKey);
            });

            document.addEventListener("click", function (event) {
              const btn = event.target.closest("[data-understage-close]");
              if (!btn) return;
              event.preventDefault();
              event.stopPropagation();
              closeUnderStageSidebar();
            });

            document.addEventListener("click", function (event) {
              const btn = event.target.closest("#kbUnderstageRefresh");
              if (!btn) return;
              event.preventDefault();
              event.stopPropagation();
              const stageKey = APP.underStage?.stageKey;
              if (!stageKey) return;
              if (typeof window.LeadUIFetchKanban === 'function') {
                showKanbanLoading('Aktualisiere Unterphasen...');
                Promise.resolve(window.LeadUIFetchKanban(State?.filtersQS || ''))
                  .finally(() => renderUnderStageBoard(stageKey));
              } else {
                renderUnderStageBoard(stageKey);
              }
            });

            document.addEventListener("keydown", function (event) {
              if (event.key === "Escape" && qs("#kbUnderstageSidebar")?.classList.contains("open")) {
                closeUnderStageSidebar();
              }
            });

            document.addEventListener("dragover", function (event) {
              const zone = event.target.closest("[data-understage-dropzone]");
              if (!zone) return;
              event.preventDefault();
              event.stopPropagation();
              zone.classList.add("drag-over");
            }, true);

            document.addEventListener("dragleave", function (event) {
              const zone = event.target.closest("[data-understage-dropzone]");
              if (!zone) return;
              const next = event.relatedTarget;
              if (!next || !zone.contains(next)) zone.classList.remove("drag-over");
            }, true);

            document.addEventListener("drop", async function (event) {
              const zone = event.target.closest("[data-understage-dropzone]");
              if (!zone) return;
              event.preventDefault();
              event.stopPropagation();
              event.stopImmediatePropagation();
              zone.classList.remove("drag-over");

              const raw =
                event.dataTransfer?.getData(window.KB_DND_MIME || "application/x-leadui-cards") ||
                event.dataTransfer?.getData("text/plain") ||
                "";
              const parsedDragIds = safeJSON(raw, []);
              const ids = Array.isArray(parsedDragIds) ? parsedDragIds : [];
              const draggedId = ids.length ? String(ids[0]) : "";
              const localRoot = zone.closest("#kbUnderstageBoard") || zone.closest(".kb-understage-sidebar") || document;
              let card = draggedId ? localRoot.querySelector(`#${CSS.escape(draggedId)}`) : null;

              // The main Kanban and the Unterphasen sidebar can contain cards with the same id.
              // Prefer the card inside the Unterphasen board, otherwise the browser may pick
              // the card from the main Kanban and the drop appears to do nothing.
              if (!card && draggedId) {
                card = Array.from(document.querySelectorAll(`#${CSS.escape(draggedId)}`))
                  .find((el) => el.closest("#kbUnderstageBoard") || el.closest(".kb-understage-sidebar")) || qs(`#${CSS.escape(draggedId)}`);
              }

              if (!card) card = event.target.closest(".card");
              if (!card) return;

              const leadProductId = card.dataset.leadProductId || card.dataset.leadProductListId || card.id?.replace("card-", "");
              const subStageId = zone.dataset.subStageId || "";
              const stageKey = zone.dataset.stageKey || APP.underStage?.stageKey || canonicalStage(card.dataset.companyStage || card.dataset.stage || 'lead');

              if (String(card.dataset.leadStageSubStageId || '') === String(subStageId || '')) {
                updateUnderStageCounts();
                return;
              }

              const previousParent = card.parentElement;
              const ask = await askUnderStageReason(subStageId || null, stageKey);
              if (!ask.confirmed) {
                updateUnderStageCounts();
                return;
              }

              zone.appendChild(card);
              try {
                await saveLeadSubStage(leadProductId, subStageId || null, ask.reason || '');
                card.dataset.leadStageSubStageId = subStageId;

                const subMeta = findUnderStageMetaById(stageKey, subStageId);
                setLeadSubStageOnCachedData(leadProductId, subStageId || null, subMeta);

                const oldChip = card.querySelector('.kb-understage-chip');
                if (oldChip) oldChip.remove();
                if (subMeta?.name) {
                  const chip = document.createElement('div');
                  chip.className = 'kb-understage-chip';
                  chip.innerHTML = `<i class="feather icon-git-branch"></i>${escapeHTML(subMeta.name)}`;
                  card.appendChild(chip);
                }

                updateUnderStageCounts();
                featherRefreshSoon();
              } catch (err) {
                if (previousParent) previousParent.appendChild(card);
                updateUnderStageCounts();
                Swal.fire("Fehler", err?.message || "Unterphase konnte nicht gespeichert werden.", "error");
              }
            }, true);

            function statusBadge(stage) {
              if (["lead", "offer", "follow_up"].includes(stage)) return ["Offen", "warning", "text-dark"];
              if (["accepted", "deal", "project", "completed"].includes(stage)) return ["Zusage", "success", ""];
              if (["archive", "archiv"].includes(stage)) return ["Archiv", "secondary", ""];
              if (["junk"].includes(stage)) return ["Junk", "danger", ""];
              return [APP.stageNames?.[stage] || stage || "Phase", "primary", ""];
            }

            function buildStatusBlock(lead) {
              const ws = String(lead.work_status || "").toLowerCase();

              // 👇 If Paused or Stopped
              if (ws === 'paused' || ws === 'stopped') {
                  let reason = "Kein Grund angegeben.";
                  try {
                      const historyStr = typeof lead.stage_history === 'string' ? lead.stage_history : JSON.stringify(lead.stage_history || "[]");
                      const history = JSON.parse(historyStr);
                      if (Array.isArray(history) && history.length > 0) {
                          const latest = history[history.length - 1];
                          if (latest && latest.description) {
                              reason = latest.description;
                          }
                      }
                  } catch(e) {
                      console.warn("Could not parse stage_history for status block", e);
                  }

                  // Added the 'status-reason' class so CSS can collapse it
                  const reasonHtml = `<div class="mt-1 small status-reason" style="color: #666; font-style: italic; line-height: 1.2; word-wrap: break-word; background: #fff; padding: 4px; border-radius: 4px; border: 1px dashed #ccc;">
                      <strong>Grund:</strong> ${escapeHTML(reason)}
                  </div>`;

                  const stateLabel = ws === "paused" ? "Pausiert" : "Gestoppt";
                  const tone = ws === "paused" ? "warning" : "danger";
                  const iconClass = ws === "paused" ? "icon-pause" : "icon-square";
                  const textClass = ws === "paused" ? "text-dark" : "";

                  return `
                    <div class="kb-status" title="Klicken zum Aus-/Einklappen">
                      <div>
                        <span class="badge bg-${tone} ${textClass} ">
                          <i class="feather ${iconClass}"></i> ${stateLabel} 
                          <i class="feather icon-chevron-down meta-toggle-icon"></i>
                        </span>
                      </div>
                      ${reasonHtml}
                    </div>`;
              }

              // 👇 If Active (Playing): show the preloaded Kanban next-step summary.
              const s = canonicalStage(lead.stage);
              const [txt, tone, extra] = statusBadge(s);

              const nextTitle =
                lead.next_kanban_task_title ||
                lead.next_task_title ||
                lead.kanban_next_step?.title ||
                lead.latest_activity ||
                lead.latest_phase ||
                "Noch keine Aufgabe";

              const previousTitle =
                lead.previous_kanban_task_title ||
                lead.kanban_next_step?.previous_title ||
                "-";

              const landedAt =
                lead.stage_landed_at ||
                lead.kanban_next_step?.stage_landed_at ||
                lead.done_date ||
                lead.updated_at;

              const landedText = fmtDE(landedAt) || "-";
              const openCount = Number(lead.kanban_open_task_count || lead.kanban_next_step?.open_count || 0);
              const doneCount = Number(lead.kanban_done_task_count || lead.kanban_next_step?.done_count || 0);

              return `
                <div class="kb-status" title="Nächster Schritt">
                  <div class="d-flex align-items-center gap-1">
                    <span class="badge bg-${tone} badge-${tone} ${extra} mr-1">${txt}</span>
                    <span class="badge bg-primary">
                      <i class="feather icon-arrow-right-circle"></i> Nächster Schritt
                      <i class="feather icon-chevron-down meta-toggle-icon"></i>
                    </span>
                  </div>
                  <div class="meta">
                    <div class="rowline"><i class="feather icon-log-in"></i></div>
                    <div class="rowline value">Seit Stage-Start: <strong>${escapeHTML(landedText)}</strong></div>

                    <div class="rowline"><i class="feather icon-check-circle"></i></div>
                    <div class="rowline value">Vorher: ${escapeHTML(previousTitle)}</div>

                    <div class="rowline"><i class="feather icon-list"></i></div>
                    <div class="rowline value"><strong>${escapeHTML(nextTitle)}</strong></div>

                    <div class="rowline"><i class="feather icon-activity"></i></div>
                    <div class="rowline value">Offen: ${openCount} · Erledigt: ${doneCount}</div>
                  </div>
                </div>`;
            }

            function applyRunStateUI(card, state) {
              const cls = { playing: "status-playing", paused: "status-paused", stopped: "status-stopped" };
              state = ["playing", "paused", "stopped"].includes(String(state || "").toLowerCase()) ? String(state).toLowerCase() : "playing";
              card.dataset.runState = state;
              card.classList.remove("status-playing", "status-paused", "status-stopped", "card-has-overlay");
              card.classList.add(cls[state] || cls.playing);
              const overlay = card.querySelector(".card-status-overlay");
              if (!overlay) return;

              if (state === "paused" || state === "stopped") {
                card.classList.add("card-has-overlay");
                overlay.style.display = "flex";
                overlay.style.flexDirection = "column"; 

                let reason = "Kein Grund angegeben.";
                try {
                    const historyStr = typeof card.dataset.stageHistory === 'string' ? card.dataset.stageHistory : JSON.stringify(card.dataset.stageHistory || "[]");
                    const history = JSON.parse(historyStr);
                    if (Array.isArray(history) && history.length > 0) {
                        const latest = history[history.length - 1];
                        if (latest && latest.description) {
                            reason = latest.description;
                        }
                    }
                } catch(e) {
                    console.warn("Could not parse stage_history for overlay", e);
                }

                const safeReason = escapeHTML(reason);
                const reasonHtml = safeReason 
                    ? `<div style="margin-top: 8px; font-size: 12px; font-weight: 600; color: #444; background: rgba(255,255,255,0.85); padding: 4px 8px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 90%; word-wrap: break-word;">${safeReason}</div>` 
                    : '';

                // 👇 Hardcoded German labels 👇
                const stateLabel = state === "paused" ? "Pausiert" : "Gestoppt";
                const iconClass = state === "paused" ? "icon-pause" : "icon-square";

                overlay.innerHTML = `
                  <span class="card-status-badge">
                    <i class="feather ${iconClass}"></i> 
                    ${stateLabel}
                  </span>
                  ${reasonHtml}
                `;
              } else {
                overlay.style.display = "none";
                overlay.style.flexDirection = "";
                overlay.innerHTML = "";
              }
              card.dataset.runState = state;
            }

            const cardId = (it) => `card-${it?.lead_product_id ?? it?.lead_product_list_id ?? it?.id ?? ''}`;
            // Production-safe global aliases. Some cached Hetzner scripts/plugins referenced Cardid/CardId.
            window.cardId = window.cardId || cardId;
            window.Cardid = window.Cardid || cardId;
            window.CardId = window.CardId || cardId;

            function parseStageHistorySafe(value) {
              if (!value) return [];

              if (Array.isArray(value)) return value;

              try {
                  const parsed = JSON.parse(value);
                  return Array.isArray(parsed) ? parsed : [];
              } catch (e) {
                  return [];
              }
          }

          function normalizeDateValue(value) {
              if (!value) return null;

              const d = new Date(value);
              return isNaN(d.getTime()) ? null : d;
          }

          function currentStageEnteredAt(item, stageKey) {
              const currentStage = String(stageKey || item?.stage || "lead").toLowerCase();
              const history = parseStageHistorySafe(item?.stage_history);

              const matching = history
                  .filter(row => {
                      const rowStage = String(row?.stage || row?.to || "").toLowerCase();
                      return rowStage === currentStage && row?.changed_at;
                  })
                  .sort((a, b) => new Date(b.changed_at) - new Date(a.changed_at));

              if (matching.length) {
                  return matching[0].changed_at;
              }

              return item?.created_at || item?.updated_at || null;
          }

          function formatDateTimeDE(value) {
              const d = normalizeDateValue(value);
              if (!d) return "-";

              return d.toLocaleString("de-DE", {
                  day: "2-digit",
                  month: "2-digit",
                  year: "numeric",
                  hour: "2-digit",
                  minute: "2-digit",
              });
          }

          function stageDurationText(value) {
              const start = normalizeDateValue(value);
              if (!start) return "-";

              const now = new Date();
              let diffMs = now - start;

              if (diffMs < 0) diffMs = 0;

              const minutes = Math.floor(diffMs / 60000);
              const hours = Math.floor(minutes / 60);
              const days = Math.floor(hours / 24);

              const restHours = hours % 24;
              const restMinutes = minutes % 60;

              if (days > 0) {
                  return `${days} Tag${days === 1 ? "" : "e"} ${restHours} Std.`;
              }

              if (hours > 0) {
                  return `${hours} Std. ${restMinutes} Min.`;
              }

              return `${Math.max(1, minutes)} Min.`;
          }

          function stageTimeHTML(item, stageKey) {
              const enteredAt = currentStageEnteredAt(item, stageKey);

              return `
                  <div class="kb-stage-time"
                      data-stage-entered-at="${escapeHTML(enteredAt || "")}">
                      <div class="kb-stage-time-row">
                          <i class="feather icon-calendar"></i>
                          <span>Seit: <strong>${escapeHTML(formatDateTimeDE(enteredAt))}</strong></span>
                      </div>
                      <div class="kb-stage-time-row">
                          <i class="feather icon-clock"></i>
                          <span>Dauer: <strong data-stage-duration>${escapeHTML(stageDurationText(enteredAt))}</strong></span>
                      </div>
                  </div>
              `;
          }


          function refreshVisibleStageDurations() {
              document.querySelectorAll(".kb-stage-time").forEach((box) => {
                  const enteredAt = box.dataset.stageEnteredAt || "";
                  const target = box.querySelector("[data-stage-duration]");
                  if (target) target.textContent = stageDurationText(enteredAt);
              });
          }

          function refreshCardStageTime(card, item, stageKey) {
              if (!card) return;
              const box = card.querySelector(".kb-stage-time");
              const html = stageTimeHTML(item || {}, stageKey || card.dataset.stage || card.dataset.companyStage || "lead");
              if (box) {
                  box.outerHTML = html;
              } else {
                  const meta = card.querySelector(".kb-card-meta");
                  if (meta) meta.insertAdjacentHTML("afterend", html);
              }
              refreshVisibleStageDurations();
              featherRefreshSoon?.();
          }


            function offerWorkflowHTML(item) {
                const safeStr = (v) => (v == null ? "" : String(v));
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);

                const workflow = item?.offer_workflow || item?.offerWorkflow || null;
                const leadStage = canonicalStage(item?.stage || "lead");
                const shouldShowEmpty = ["offer", "deal", "auftrag"].includes(leadStage);

                if (!workflow || workflow.exists === false) {
                    if (!shouldShowEmpty) return "";

                    return `
                      <div class="kb-offer-workflow-empty">
                        Dieser Kunde ist in ${leadStage === "offer" ? "Angebot" : "Auftrag"}, aber es wurde noch kein passender Angebot-/Auftrag-Ordner gefunden.
                      </div>`;
                }

                const documentLabel = workflow.document_status_label
                    || (String(workflow.document_status || "").toLowerCase() === "deal" ? "Auftrag" : "Angebot");

                const color = safeStr(workflow.status_color || (documentLabel === "Auftrag" ? "#10b981" : "#74b2d4"));
                const statusLabel = safeStr(workflow.status_label || workflow.status_key || "-");
                const offerNo = safeStr(workflow.offer_no || "").trim();
                const folderName = safeStr(workflow.folder_name || "").trim();
                const updatedRaw = safeStr(workflow.updated_at || "").trim();
                const updated = (() => {
                    if (!updatedRaw) return "-";
                    const d = new Date(updatedRaw);
                    return Number.isNaN(d.getTime()) ? updatedRaw : d.toLocaleDateString("de-DE");
                })();

                const openUrl = safeStr(workflow.url || "").trim();

                return `
                  <details class="kb-offer-workflow" data-offer-workflow="${esc(documentLabel)}">
                    <summary class="kb-offer-workflow-head">
                      <div class="kb-offer-workflow-left">
                        <span class="kb-offer-workflow-chevron"><i class="feather icon-chevron-right"></i></span>
                        <div class="kb-offer-workflow-title">
                          <i class="feather icon-activity"></i>
                          <span>Status</span>
                        </div>
                      </div>
                      <span class="kb-offer-workflow-status" style="background:${esc(color)}" title="${esc(statusLabel)}">
                        ${esc(statusLabel)}
                      </span>
                    </summary>

                    <div class="kb-offer-workflow-body">
                      ${offerNo ? `
                        <div class="kb-offer-workflow-row">
                          <span class="kb-offer-workflow-label">Nr.</span>
                          <span class="kb-offer-workflow-value">${esc(offerNo)}</span>
                        </div>` : ``}

                      ${folderName ? `
                        <div class="kb-offer-workflow-row">
                          <span class="kb-offer-workflow-label">Ordner</span>
                          <span class="kb-offer-workflow-value" title="${esc(folderName)}">${esc(folderName)}</span>
                        </div>` : ``}

                      <div class="kb-offer-workflow-row">
                        <span class="kb-offer-workflow-label">Aktualisiert</span>
                        <span class="kb-offer-workflow-value">${esc(updated)}</span>
                      </div>

                      ${openUrl ? `
                        <a class="kb-offer-workflow-open" href="${esc(openUrl)}" target="_blank" rel="noopener">
                          Öffnen <i class="feather icon-external-link"></i>
                        </a>` : ``}
                    </div>
                  </details>`;
            }



            function reminderSummaryHTML(item) {
                const safeStr = (v) => (v == null ? "" : String(v));
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);

                const r = item?.latest_reminder || item?.next_reminder || item?.reminder || null;
                if (!r) {
                    return `
                      <div class="kb-reminder-summary is-empty">
                        <div class="kb-reminder-head">
                          <div class="kb-reminder-title"><i class="feather icon-bell"></i> Keine Erinnerung</div>
                          <span class="kb-reminder-priority normal">Offen</span>
                        </div>
                        <div class="kb-reminder-body">
                          <i class="feather icon-info"></i>
                          <span>Noch kein nächster Schritt geplant.</span>
                        </div>
                      </div>`;
                }

                const title = safeStr(r.title || r.task_title || "Reminder").trim();
                const desc = safeStr(r.description || "").trim();
                const priority = safeStr(r.priority || "normal").toLowerCase();
                const dueDate = safeStr(r.reminder_date || r.due_date || "").slice(0, 10);
                const dueTime = safeStr(r.reminder_time || r.due_time || "").slice(0, 5);
                const responsible = safeStr(r.responsible_name || r.employee_name || r.owner_name || "").trim();
                const today = new Date();
                const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
                const boxState = dueDate && dueDate < todayStr ? " kb-reminder-overdue" : (dueDate === todayStr ? " kb-reminder-due-today" : "");

                return `
                  <div class="kb-reminder-summary${boxState}">
                    <div class="kb-reminder-head">
                      <div class="kb-reminder-title"><i class="feather icon-bell"></i> Nächster Schritt</div>
                      <span class="kb-reminder-priority ${esc(priority)}">${esc(priority || "normal")}</span>
                    </div>
                    <div class="kb-reminder-body">
                      <i class="feather icon-check-square"></i>
                      <span><strong>${esc(title)}</strong>${desc ? `<br>${esc(desc).slice(0, 120)}` : ``}</span>
                      <i class="feather icon-calendar"></i>
                      <span class="kb-reminder-due">${esc(dueDate || "kein Datum")}${dueTime ? `, ${esc(dueTime)} Uhr` : ``}</span>
                      <i class="feather icon-user"></i>
                      <span>${esc(responsible || "Automatisch / nicht zugewiesen")}</span>
                    </div>
                  </div>`;
            }


            function currentStageLandedAt(item, fallbackDate = null) {
                const safeStr = (v) => (v == null ? "" : String(v));
                const stage = canonicalStage(item?.stage || item?.status || item?.company_stage || "lead");
                let history = [];
                try {
                    history = Array.isArray(item?.stage_history)
                      ? item.stage_history
                      : JSON.parse(safeStr(item?.stage_history || "[]"));
                } catch (e) {
                    history = [];
                }

                if (Array.isArray(history) && history.length) {
                    for (let i = history.length - 1; i >= 0; i--) {
                        const h = history[i] || {};
                        const to = canonicalStage(h.to || h.stage || h.status || "");
                        if (to === stage && (h.changed_at || h.created_at || h.date)) {
                            return h.changed_at || h.created_at || h.date;
                        }
                    }
                }

                return fallbackDate || item?.updated_at || item?.created_at || null;
            }

            function formatDateTimeDE(value) {
                if (!value) return "Neu / gerade gestartet";
                const d = new Date(value);
                if (!d || Number.isNaN(d.getTime())) return "Neu / gerade gestartet";
                return d.toLocaleDateString("de-DE", { day: "2-digit", month: "2-digit", year: "2-digit" }) + " " +
                       d.toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" });
            }

            function nextStepPreviewHTML(item) {
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);

                const landed = formatDateTimeDE(item?.stage_landed_at || item?.kanban_next_step?.stage_landed_at || currentStageLandedAt(item));
                const nextTitle =
                    item?.next_kanban_task_title ||
                    item?.next_task_title ||
                    item?.kanban_next_step?.title ||
                    item?.latest_activity ||
                    item?.latest_phase ||
                    item?.product_task_phase_name ||
                    "Noch keine Aufgabe";
                const previousTitle = item?.previous_kanban_task_title || item?.kanban_next_step?.previous_title || "-";
                const openCount = Number(item?.kanban_open_task_count || item?.kanban_next_step?.open_count || 0);
                const doneCount = Number(item?.kanban_done_task_count || item?.kanban_next_step?.done_count || 0);
                const lpId = item?.lead_product_id || item?.lead_product_list_id || item?.id || "";

                return `
                  <div class="kb-next-step-preview">
                    <div class="kb-next-step-preview-head">
                      <span><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</span>
                      <button type="button"
                              class="kb-next-step-preview-btn"
                              data-open-kanban-task-management
                              data-lead-product-list-id="${esc(lpId)}">
                        Details
                      </button>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-log-in"></i>
                      <span>Seit: <strong>${esc(landed)}</strong></span>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-check-circle"></i>
                      <span>Vorher: <strong>${esc(previousTitle)}</strong></span>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-list"></i>
                      <span>${esc(nextTitle)}</span>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-activity"></i>
                      <span>Offen: <strong>${esc(openCount)}</strong> · Erledigt: <strong>${esc(doneCount)}</strong></span>
                    </div>
                  </div>`;
            }


            function cardHTML(item, stageKey) {
                "use strict";

                const safeStr = (v) => (v == null ? "" : String(v));
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);
                const isNonEmpty = (v) => safeStr(v).trim().length > 0;
                const stage = canonicalStage(stageKey || item?.stage || item?.status || "lead");

                const fullName = (() => {
                    const fn = safeStr(item?.customer_name).trim();
                    const ln = safeStr(item?.customer_lastname).trim();
                    const firma = safeStr(item?.firma).trim();
                    const name = `${fn} ${ln}`.trim();
                    return name || firma || "Unbekannt";
                })();

                const address = [item?.street, item?.postcode, item?.city]
                    .map((v) => safeStr(v).trim())
                    .filter(Boolean)
                    .join(", ");

                const productInitial = safeStr(item?.initial || item?.product_initial || item?.article_group_initial || "").trim();
                const leadProductId = safeStr(item?.lead_product_id || item?.lead_product_list_id || item?.id || "");

                const createdRaw = item?.created_at || item?.updated_at || null;
                const compactDate = (() => {
                    const d = createdRaw ? new Date(createdRaw) : null;
                    if (!d || Number.isNaN(d.getTime())) return "–";
                    return d.toLocaleDateString("de-DE", { day: "2-digit", month: "2-digit" }) + " " + d.toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" });
                })();

                const currentSubStageId = typeof getLeadSubStageId === "function" ? getLeadSubStageId(item) : safeStr(item?.lead_stage_sub_stage_id || item?.sub_stage_id || item?.stage_sub_stage_id || "");
                const subStageName = safeStr(item?.lead_stage_sub_stage_name || item?.sub_stage_name || "").trim();
                const subStageChip = subStageName
                    ? `<div class="kb-understage-chip"><i class="feather icon-git-branch"></i>${esc(subStageName)}</div>`
                    : (currentSubStageId ? `<div class="kb-understage-chip"><i class="feather icon-git-branch"></i>Unterphase #${esc(currentSubStageId)}</div>` : ``);

                const employee = item?.employee && (item.employee.employee_id || item.employee.id) ? item.employee : null;
                const fieldEmployee = item?.field_employee && (item.field_employee.employee_id || item.field_employee.id) ? item.field_employee : null;

                const mkEmp = (emp, fallbackTitle) => {
                    if (!emp) return null;
                    const title = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || fallbackTitle;
                    return {
                        title,
                        image: safeStr(emp?.image).trim(),
                        id: Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0,
                    };
                };

                const empList = [mkEmp(employee, "Innendienst"), mkEmp(fieldEmployee, "Außendienst")].filter(Boolean);

                const empHTML = empList.length > 0
                    ? `<ul class="list-unstyled users-list m-0 d-flex align-items-center">
                        ${empList.map((e) => `
                          <li class="avatar pull-up" title="${esc(e.title)}">
                            <img class="media-object rounded-circle"
                                 src="${APP.EMP_SRC}/${esc(e.image || "noimage.png")}"
                                 height="26" width="26" alt=""
                                 style="object-fit:cover;border:2px solid #fff;">
                          </li>`).join("")}
                      </ul>`
                    : `<small>&ndash;</small>`;

                const teamHTML = (() => {
                    const currentAssignments = Array.isArray(item?.current_team_assignments) && item.current_team_assignments.length
                        ? item.current_team_assignments
                        : (Array.isArray(item?.team_assignments)
                            ? item.team_assignments.filter(a => canonicalStage(a?.stage || stage) === stage)
                            : []);

                    const fallbackMembers = Array.isArray(item?.team_members) ? item.team_members.map(m => ({ member: m })) : [];
                    const list = currentAssignments.length ? currentAssignments : fallbackMembers;
                    const visible = list.slice(0, 2);
                    const rest = Math.max(0, list.length - visible.length);

                    const avatarHtml = visible.map((x) => {
                        const emp = x?.member || x || {};
                        const img = emp?.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
                        const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || "Team";
                        return `<img src="${esc(img)}" alt="${esc(name)}" title="${esc(name)}">`;
                    }).join("");

                    return `
                      <div class="kb-card-team-compact">
                        <button type="button"
                                class="kb-team-pill"
                                data-show-stage-team="${esc(stage)}"
                                title="Teamübersicht öffnen">
                          <span class="kb-team-mini-avatars">${avatarHtml}</span>
                          <span>Teams</span>
                          <span class="kb-team-pill-count">${list.length}</span>
                          ${rest > 0 ? `<span class="kb-team-pill-count">+${rest}</span>` : ``}
                        </button>
                      </div>`;
                })();

                const hideJunk = stageRank(stage) >= stageRank("deal");

                return `
                  <div class="card-status-overlay" aria-hidden="true"></div>

                  ${getDateAgeIndicator(item?.created_at, stage)}

                  <div class="kb-menu kb-menu--card" aria-label="Kartenmenü">
                    <button type="button" class="btn-icon kb-menu-toggle" data-act="custom-menu-toggle" title="Menü" aria-haspopup="menu" aria-expanded="false">
                      <i class="feather icon-more-vertical" aria-hidden="true"></i>
                    </button>

                    <div class="kb-menu-dropdown" role="menu" aria-label="Menü" hidden>
                      <button type="button" class="kb-menu-item" data-menu="verlauf" role="menuitem"><i class="feather icon-clock mr-50"></i> Verlauf</button>
                      <button type="button" class="kb-menu-item" data-menu="termin" role="menuitem"><i class="feather icon-calendar mr-50"></i> Termin</button>
                      <button type="button" class="kb-menu-item" data-menu="aufgabe" role="menuitem"><i class="feather icon-check-square mr-50"></i> Aufgabe</button>
                      <button type="button" class="kb-menu-item" data-open-notes data-customer="${esc(item.customer_id)}" data-alt="${esc(item.alternative_id)}" data-product="${esc(item.product_id)}" role="menuitem"><i class="feather icon-message-square mr-50"></i> Notizen</button>
                      <a class="kb-menu-item" href="/new_lead_profile/${encodeURIComponent(safeStr(item?.customer_id))}" role="menuitem"><i class="feather icon-eye mr-50"></i> Profil</a>
                      <hr class="my-50">
                      <button type="button" class="kb-menu-item text-success" data-run="playing" role="menuitem"><i class="feather icon-play mr-50"></i> Start</button>
                      <button type="button" class="kb-menu-item text-warning" data-run="paused" role="menuitem"><i class="feather icon-pause mr-50"></i> Pause</button>
                      <button type="button" class="kb-menu-item text-danger" data-run="stopped" role="menuitem"><i class="feather icon-square mr-50"></i> Stopp</button>
                      ${!hideJunk ? `<button type="button" class="kb-menu-item text-danger" data-act="delete" role="menuitem"><i class="feather icon-trash-2 mr-50"></i> Junk</button>` : ``}
                      ${stage === "completed" ? `<button type="button" class="kb-menu-item" data-act="archive" role="menuitem"><i class="feather icon-archive mr-50"></i> Archivieren</button>` : ``}
                    </div>
                  </div>

                  <div class="card-header card-header--kb">
                    <div class="card-title">
                      <strong class="card-name" title="${esc(fullName)}">${esc(fullName)}</strong>
                      ${productInitial ? `<div class="circle product_circle" aria-hidden="true">${esc(productInitial)}</div>` : ``}
                    </div>
                  </div>

                  <div class="kb-card-meta">
                    <div class="kb-meta-row">
                      <span class="kb-meta-item"><i class="feather icon-calendar"></i> ${esc(compactDate)}</span>
                    </div>
                    ${isNonEmpty(address) ? `<small class="kb-meta-address" title="${esc(address)}">${esc(address)}</small>` : ``}
                  </div>

                  ${subStageChip}
                  ${nextStepPreviewHTML(item)}

                  <div class="employeeList d-flex align-items-center mt-2">
                    ${empHTML}
                    ${teamHTML}
                  </div>

                  <div class="card-actions" role="group" aria-label="Aktionen">
                    <div class="left-actions">
                      <button class="btn-icon" data-menu="aufgabe" title="Aufgabe">
                        <i class="feather icon-check-square"></i>
                        <span class="badge-notes" data-pt-count style="display:none">0</span>
                      </button>
                      <button type="button"
                              class="btn-icon kb-task-management-btn"
                              data-open-kanban-task-management
                              data-lead-product-list-id="${esc(leadProductId)}"
                              data-customer-id="${esc(item.customer_id || '')}"
                              data-alternative-id="${esc(item.alternative_id || '')}"
                              data-product-id="${esc(item.product_id || '')}"
                              data-customer-name="${esc(fullName)}"
                              data-product-name="${esc(item.article_group || item.product_name || item.product || item.initial || '')}"
                              title="Aufgabenmanagement">
                        <i class="feather icon-list"></i>
                        <span class="kb-task-count-badge d-none" data-kanban-task-count>0</span>
                      </button>
                    </div>

                    <div class="right-actions">
                      <button type="button" class="btn-icon btn-notes note"
                              data-open-notes
                              data-customer="${esc(item.customer_id)}"
                              data-alt="${esc(item.alternative_id)}"
                              data-product="${esc(item.product_id)}"
                              data-lead-product-list-id="${esc(leadProductId)}"
                              data-customer-name="${esc(fullName)}"
                              data-product-name="${esc(item.article_group || item.product_name || item.product || item.initial || '')}"
                              title="Notizen / Berichte">
                        <i class="feather icon-message-square"></i>
                        <span class="badge-notes" data-count="0" style="display:none">0</span>
                      </button>
                      <a href="/new_lead_profile/${encodeURIComponent(safeStr(item?.customer_id))}" class="btn-icon" title="Profil">
                        <i class="feather icon-eye"></i>
                      </a>
                      ${!hideJunk ? `<button class="btn-icon" data-act="delete" title="In Junk verschieben"><i class="feather icon-trash-2"></i></button>` : ``}
                      ${stage === "completed" ? `<button class="btn-icon" data-act="archive" title="Archivieren"><i class="feather icon-archive"></i></button>` : ``}
                    </div>
                  </div>
                `;
            }

            async function updateCardLeadSubStage(select) {
                const card = select.closest(".card");
                const leadProductId = Number(select.dataset.leadProductId || card?.dataset.leadProductId || 0);
                const stageKey = canonicalStage(select.dataset.stageKey || card?.dataset.companyStage || card?.dataset.stage || "lead");
                const subStageId = select.value || null;
                const previous = select.dataset.previousValue || "";

                if (!leadProductId || !APP.endpoints?.stageWorkflowMove) {
                    if (window.Swal) Swal.fire("Fehler", "LeadProduct-ID oder Speicherroute fehlt.", "error");
                    else alert("LeadProduct-ID oder Speicherroute fehlt.");
                    select.value = previous;
                    return;
                }

                select.disabled = true;

                try {
                    const payload = {
                        mode: "company",
                        company_stage_key: stageKey,
                        lead_stage_sub_stage_id: subStageId,
                        reason: "Unterphase geändert",
                        teams: card?.dataset?.teamIds ? safeJSON(card.dataset.teamIds, []) : []
                    };

                    const data = await postJSON(APP.endpoints.stageWorkflowMove(leadProductId), payload);
                    if (!data?.success) throw new Error(data?.message || "Unterphase konnte nicht gespeichert werden.");

                    select.dataset.previousValue = subStageId || "";
                    if (card) {
                        card.dataset.leadStageSubStageId = subStageId || "";
                    }
                } catch (error) {
                    select.value = previous;
                    if (window.Swal) Swal.fire("Fehler", error.message || "Unterphase konnte nicht gespeichert werden.", "error");
                    else alert(error.message || "Unterphase konnte nicht gespeichert werden.");
                } finally {
                    select.disabled = false;
                }
            }

            document.addEventListener("change", function (event) {
                const select = event.target.closest("[data-substage-change]");
                if (!select) return;
                updateCardLeadSubStage(select);
            });

            document.addEventListener("focusin", function (event) {
                const select = event.target.closest("[data-substage-change]");
                if (!select) return;
                select.dataset.previousValue = select.value || "";
            });




            function renderStageTeamRowsForSwal(assignments, currentStage = null) {
              const arr = Array.isArray(assignments) ? assignments : [];
              const stageKeys = orderedStageEntries(APP.stageNames || {}).map(([k]) => k).filter((k) => !["junk", "ticket"].includes(k));
              const stage = canonicalStage(currentStage || "lead");
              const currentIdx = stageKeys.indexOf(stage);
              const visibleStages = currentIdx >= 0 ? stageKeys.slice(0, currentIdx + 1) : stageKeys;

              const byStage = new Map();
              arr.forEach((a) => {
                const st = canonicalStage(a?.stage || stage || "lead");
                if (!byStage.has(st)) byStage.set(st, []);
                byStage.get(st).push(a);
              });

              const currentMembers = byStage.get(stage) || [];
              const memberChip = (x) => {
                const emp = x?.member || {};
                const name = `${emp?.lastname || ""} ${emp?.name || ""}`.trim() || `Mitarbeiter #${x?.employee_id || ""}`;
                const img = emp?.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
                return `<span class="swal-team-chip"><img src="${escapeHTML(img)}" alt="">${escapeHTML(name)}</span>`;
              };

              return `
                <div style="text-align:left">
                  <div class="swal-team-current-box">
                    <div class="swal-team-current-title">Aktuelles Team in ${escapeHTML(APP.stageNames?.[stage] || stage)}</div>
                    <div class="swal-team-current-list">
                      ${currentMembers.length ? currentMembers.map(memberChip).join("") : `<span class="swal-stage-team-empty">Kein aktuelles Team gespeichert</span>`}
                    </div>
                  </div>

                  <div class="swal-stage-team-grid">
                    ${visibleStages.map((st) => {
                      const members = byStage.get(st) || [];
                      const isCurrent = st === stage ? " is-current-stage" : "";
                      return `<div class="swal-stage-team-row${isCurrent}">
                        <div class="swal-stage-team-title">${escapeHTML(APP.stageNames?.[st] || st)}</div>
                        <div>
                          ${members.length ? members.map((x) => {
                            const emp = x?.member || {};
                            const name = `${emp?.lastname || ""} ${emp?.name || ""}`.trim() || `Mitarbeiter #${x?.employee_id || ""}`;
                            const u = x?.assigned_by_user || {};
                            const by = `${u?.lastname || ""} ${u?.name || ""}`.trim() || (x?.assigned_by ? `Mitarbeiter #${x.assigned_by}` : "-");
                            const at = x?.assigned_at ? fmtDE(x.assigned_at) : "-";
                            return `<div class="swal-stage-team-member"><strong>${escapeHTML(name)}</strong><br><span class="text-muted">von ${escapeHTML(by)} • ${escapeHTML(at)}</span></div>`;
                          }).join("") : `<div class="swal-stage-team-empty">Kein Team gespeichert</div>`}
                        </div>
                      </div>`;
                    }).join("")}
                  </div>
                </div>`;
            }

            function openStageTeamModal(holder, stageKey) {
              if (!holder) return;
              let assignments = [];
              try {
                assignments = JSON.parse(holder.dataset.teamAssignments || holder.dataset.teams || "[]");
              } catch (_) {
                assignments = [];
              }
              const stage = canonicalStage(stageKey || holder.dataset.stage || "lead");
              const html = renderStageTeamRowsForSwal(assignments, stage);
              Swal.fire({
                title: `Teams`,
                html,
                width: 780,
                confirmButtonText: "Schließen"
              });
            }

            document.addEventListener("click", (e) => {
              const btn = e.target.closest("[data-show-stage-team]");
              if (!btn) return;
              e.preventDefault();
              e.stopPropagation();
              const holder = btn.closest(".card, tr.list-row-item, [data-team-assignments]");
              openStageTeamModal(holder, btn.dataset.showStageTeam);
            });

              function normalizeTeamIds(item) {
                const toId = (x) => {
                  const n = Number(
                    x?.id ??
                    x?.employee_id ??
                    x?.emp_id ??
                    x
                  );
                  return Number.isFinite(n) && n > 0 ? n : null;
                };

                // preferred: backend sends ids directly
                const direct =
                  item?.team_ids ??
                  item?.teamIds ??
                  item?.teams_ids ??
                  item?.teamsIds ??
                  null;

                if (Array.isArray(direct)) return direct.map(toId).filter(Boolean);

                // fallback: arrays of objects
                const arr =
                  Array.isArray(item?.team_members) ? item.team_members :
                  Array.isArray(item?.teams) ? item.teams :
                  [];

                return arr.map(toId).filter(Boolean);
              }

            function mountOrUpdateCard(stageKey, item, existing) {
              let card = existing;
              if (!card) {
                card = document.createElement("div");
                card.className = "card";
                card.id = cardId(item);
                card.draggable = true;
                card.dataset.customerId = item.customer_id ?? "";
                card.dataset.alternativeId = item.alternative_id ?? "";
                card.dataset.productId = item.product_id ?? "";
                card.dataset.leadProductId = item.lead_product_id ?? item.lead_product_list_id ?? item.id ?? "";
                card.dataset.leadProductListId = item.lead_product_id ?? item.lead_product_list_id ?? item.id ?? "";
              }
              card.dataset.employeeId = item.employee?.employee_id ?? 0;
              card.dataset.fieldEmployeeId = item.field_employee?.employee_id ?? 0;
              card.dataset.service = item.service ?? "complete";
              card.dataset.serviceId = item.service_id ?? 0;
              card.dataset.departmentId = item.department_id ?? 0;
              const columnKey = workflowColumnKey(item);
              card.dataset.stage = columnKey;
              card.dataset.companyStage = canonicalStage(item.stage);
              card.dataset.productStageId = item.product_stage_id || "";
              card.dataset.productTaskPhaseId = item.product_task_phase_id || "";
              card.dataset.productStageName = item.product_stage_name || "";
              card.dataset.productTaskPhaseName = item.product_task_phase_name || "";
              card.dataset.stageMode = item.stage_mode || APP.stageWorkflow.mode || "company";
              card.dataset.latestPhase = item.latest_phase || "";
              card.dataset.latestActivity = item.latest_activity || "";
              card.dataset.doneDate = item.done_date || "";
              card.dataset.createdAt = item.created_at || "";
              card.dataset.updatedAt = item.updated_at || "";
              card.dataset.fullAddress = item.full_address || "";
              card.dataset.street = item.street || "";
              card.dataset.postcode = item.postcode || "";
              card.dataset.city = item.city || "";
              card.dataset.phone = item.phone || "";
              card.dataset.email = item.email || "";
              card.dataset.latitude = item.latitude || "";
              card.dataset.longitude = item.longitude || "";
              card.dataset.teamIds = JSON.stringify(Array.isArray(item.team_ids) ? item.team_ids : normalizeTeamIds(item));
              card.dataset.teamAssignments = JSON.stringify(Array.isArray(item.team_assignments) ? item.team_assignments : []);
              card.dataset.stageHistory = typeof item.stage_history === 'string' ? item.stage_history : JSON.stringify(item.stage_history || []);
              card.dataset.leadStageSubStageId = getLeadSubStageId(item);

              card.innerHTML = cardHTML(item, stageKey);
              enforceActionVisibility(card);
              const ws = (item.work_status || "playing").toString().toLowerCase();
              applyRunStateUI(card, ["playing", "paused", "stopped"].includes(ws) ? ws : "playing");
              return card;
            }

            function renderKanbanDiff(leads) {
              APP.allLeads = Array.isArray(leads) ? leads : [];
              if (APP.underStage?.active && APP.underStage.stageKey) { renderUnderStageBoard(APP.underStage.stageKey); return; }
              ensureColumns();
              const existing = new Map();
              qsa("#kanban .card").forEach((el) => existing.set(el.id, el));
              const visibleStageNames = APP.kanbanStageNames || APP.stageNames;
              const stageBuckets = new Map(Object.keys(visibleStageNames).map((k) => [k, []]));

              const filtered = (leads || []).filter((it) => !["junk", "ticket"].includes(canonicalStage(it.stage)));

              for (const it of filtered) {
                const s = workflowColumnKey(it);
                if (stageBuckets.has(s)) stageBuckets.get(s).push(it);
              }

              for (const [stage, arr] of stageBuckets) {
                const container = colContent(stage);
                if (!container) continue;
                const frag = document.createDocumentFragment();
                for (const item of arr) {
                  const id = cardId(item);
                  const prev = existing.get(id) || null;
                  const card = mountOrUpdateCard(stage, item, prev);
                  frag.appendChild(card);
                  existing.delete(id);
                }
                container.innerHTML = "";
                container.appendChild(frag);
              }
              for (const [, el] of existing) el.remove();
              updateCounts();
              featherRefreshSoon();
              updateNoteBadgesForVisibleCards();
              // Compact cards: live feed is intentionally not loaded on cards.
            }

            function autoChunk() {
              const low = (navigator.hardwareConcurrency || 4) < 6;
              const narrow = window.matchMedia?.("(max-width: 768px)").matches;
              return low || narrow ? 24 : 60;
            }

            function renderKanbanIncremental(leads, chunkSize = autoChunk(), done = () => {}) {
              APP.allLeads = Array.isArray(leads) ? leads : [];
              if (APP.underStage?.active && APP.underStage.stageKey) { renderUnderStageBoard(APP.underStage.stageKey); done?.(); return; }
              ensureColumns();
              clearColumns();
              const list = (leads || []).filter((it) => !["junk", "ticket"].includes(canonicalStage(it?.stage)));
              let i = 0;
              (function pump() {
                const frags = new Map();
                const getFrag = (s) => {
                  if (!frags.has(s)) frags.set(s, document.createDocumentFragment());
                  return frags.get(s);
                };
                for (let c = 0; c < chunkSize && i < list.length; c++, i++) {
                  const item = list[i];
                  const stage = workflowColumnKey(item);
                  if ((APP.kanbanStageNames || APP.stageNames)[stage] || APP.stageAlias[stage]) {
                    const card = mountOrUpdateCard(stage, item, null);
                    getFrag(stage).appendChild(card);
                  }
                }
                for (const [stage, frag] of frags) colContent(stage)?.appendChild(frag);
                if (i < list.length) {
                  requestIdleCallback(pump);
                } else {
                  updateCounts();
                  featherRefreshSoon();
                  updateNoteBadgesForVisibleCards();
                  enforceActionVisibility();
                  // Compact cards: live feed is intentionally not loaded on cards.
                  refreshVisibleStageDurations();
                  done();
                }
              })();
            }

            /* --- Note Logic (Unified for List & Kanban) --- */
            const visibleCardTuples = () => {
              const cards = qsa("#kanban .card");
              const rows = qsa("#kanbanTableBody tr.list-row-item");
              return [...cards, ...rows].map((el) => ({
                el,
                customer_id: el.dataset.customerId,
                alternative_id: el.dataset.alternativeId,
                product_id: el.dataset.productId || null,
              }));
            };

            async function fetchNoteCountOnce(t) {
              const params = new URLSearchParams({ customer_id: t.customer_id, alternative_id: t.alternative_id, per_page: 1 });
              if (t.product_id) params.set("product_id", t.product_id);
              try {
                const p = await safeFetchJSON(`${APP.endpoints.notesIndex}?${params.toString()}`);
                return Number(p?.total || 0);
              } catch { return 0; }
            }

            function updateBadge(el, n) {
              const bd = el.querySelector(".badge-notes");
              if (!bd) return;
              bd.dataset.count = String(n);
              bd.textContent = shortNum(n);
              bd.style.display = n > 0 ? 'block' : 'none'; 
            }

            function updateNoteBadgesForVisibleCards() {
              const tuples = visibleCardTuples();
              tuples.forEach((t) => updateBadge(t.el, 0));
              let i = 0;
              (function next() {
                const batch = tuples.slice(i, (i += 4));
                if (!batch.length) return;
                Promise.all(batch.map(async (t) => updateBadge(t.el, await fetchNoteCountOnce(t)))).finally(() => setTimeout(next, 30));
              })();
            }

           function setNotesTab(tab) {
                const tabs = document.querySelectorAll("[data-notes-tab]");
                const panels = document.querySelectorAll("[data-notes-panel]");
                tabs.forEach((btn) => {
                  const isActive = btn.dataset.notesTab === tab;
                  btn.classList.toggle("notes-tab--active", isActive);
                  btn.setAttribute("aria-selected", isActive ? "true" : "false");
                });
                panels.forEach((panel) => {
                  const isActive = panel.dataset.notesPanel === tab;
                  panel.classList.toggle("d-none", !isActive);
                });

                // 🔴 FIX: Hide the Quill Editor footer if we are not on the "notes" tab
                const footer = document.querySelector("#notesDrawer .notes-foot");
                if (footer) {
                    footer.style.display = (tab === 'notes') ? 'block' : 'none';
                }
            }

            async function loadNotesReport() {
                const panel = document.getElementById("notesReport");
                const content = document.getElementById("kbTerminReportContent") || panel;
                if (!panel || !content) return;

                const cId = document.getElementById("notesCustomerId")?.value || "";

                if (!cId) {
                    content.innerHTML = `<div class="text-muted small p-2">Kein Kunde vorhanden.</div>`;
                    return;
                }

                if (content.innerHTML.trim() === "") {
                    content.innerHTML = `<div class="text-muted small p-2">Termin Bericht wird geladen…</div>`;
                }

                try {
                    /*
                     * IMPORTANT:
                     * Termin Bericht is now the only appointment view in Kanban.
                     * Load ALL appointments for this customer.
                     * Do not send alternative_id/product_id here, because older appointments
                     * often only have customer_id or mixed products JSON.
                     */
                    const routes = window.KANBAN_CUSTOMER_PANEL_ROUTES || {};
                    const endpoint = routes.appointmentsIndex || APP?.endpoints?.appointmentsIndex;

                    if (!endpoint) {
                        throw new Error("Termin Bericht Route fehlt.");
                    }

                    const params = new URLSearchParams({ customer_id: cId });
                    const res = await safeFetchJSON(`${endpoint}?${params.toString()}`, { method: "GET" });

                    content.innerHTML = res.html || `<div class="kb-empty-state">Keine Termine gefunden.</div>`;

                    const appointmentCount = Number(
                        res.count ??
                        content.querySelectorAll(".kb-appointment-group, [data-appointment-id]").length ??
                        0
                    );

                    const reportCount = Number(
                        res.reports_count ??
                        content.querySelectorAll(".ap-report-card, .kb-report-card.ap-report-card, [data-report-id]").length ??
                        0
                    );

                    const doneCount = content.querySelectorAll(".kb-appointment-group.is-done").length;
                    const openCount = Math.max(0, appointmentCount - doneCount);

                    const badge = document.getElementById("tabBadgeTerminReport");
                    if (badge) {
                        badge.textContent = appointmentCount;
                        badge.dataset.count = String(appointmentCount);
                        badge.classList.remove("d-none");
                    }

                    const openInfo = document.getElementById("kbTerminOpenInfo");
                    if (openInfo) {
                        openInfo.textContent = appointmentCount > 0
                            ? `${appointmentCount} Termin(e) · ${reportCount} Bericht(e) · ${openCount} offen`
                            : "Keine Termine gefunden";
                        openInfo.classList.toggle("text-danger", openCount > 0);
                        openInfo.classList.toggle("text-success", appointmentCount > 0 && openCount === 0);
                    }

                    if (window.feather?.replace) window.feather.replace();
                } catch (e) {
                    content.innerHTML = `<div class="text-danger small p-2">Termin Bericht konnte nicht geladen werden.<br>${(e && e.message) ? e.message : ''}</div>`;
                }
            }

          async function loadCustomerReport() {
                const panel = document.getElementById("customerReportList");
                if (!panel) return;
                const cId = document.getElementById("notesCustomerId")?.value || "";
                const aId = document.getElementById("notesAlternativeId")?.value || "";
                const pId = document.getElementById("notesProductId")?.value || "";

                if (!cId || !aId) {
                    panel.innerHTML = `<div class="text-muted small p-2">Kein Kontext (Kunde/Alternative) vorhanden.</div>`;
                    return;
                }

                // Only show loading if empty (prevents flashing during background load)
                if(panel.innerHTML.trim() === "") {
                    panel.innerHTML = `<div class="text-muted small p-2">Kundenreport wird geladen…</div>`;
                }

                try {
                    const params = new URLSearchParams({ customer_id: cId, alternative_id: aId });
                    if (pId) params.set("product_id", pId);
                    const res = await safeFetchJSON(`${APP.endpoints.customerReportsIndex}?${params.toString()}`, { method: "GET" });
                    if (!res || typeof res.html !== "string") throw new Error(res?.message || "Unerwartete Serverantwort.");
                    panel.innerHTML = res.html;

                    // 🔴 FIX: Count the reports and update the badge
                    const count = panel.querySelectorAll('.cr-card, .ap-report-card').length;
                    const badge = document.getElementById('tabBadgeCustomerReport');
                    if (badge) {
                        badge.textContent = count;
                        badge.classList.remove('d-none');
                    }

                } catch (e) {
                    panel.innerHTML = `<div class="text-danger small p-2">Kundenreport konnte nicht geladen werden.<br>${(e && e.message) ? e.message : ''}</div>`;
                }
            }

            // NOTE HANDLERS
            function noteHTML(n) {
              const me = String(n.created_by ?? "") === String(APP.authUserId);
              const img = n?.author?.image ? `${APP.EMP_SRC}/${n.author.image}` : `${APP.EMP_SRC}/noimage.png`;
              const who = n.author ? `${n.author.lastname ?? ""} ${n.author.name ?? ""}`.trim() : "Unbekannt";
              const when = n.created_at ? new Date(n.created_at).toLocaleString("de-DE") : "";
              const bubble = `<div class="note-bubble ${me ? "me" : "other"}"><div class="note-bubble-body" data-note-body>${n.description || ""}</div><div class="note-meta"><span class="note-meta-author">${who}</span><span class="note-meta-sep">•</span><span class="note-meta-time">${when}</span></div>${me ? `<div class="note-actions"><button type="button" class="note-action note-action-edit" data-note-edit data-note-id="${n.id}"><i class="feather icon-edit-2"></i></button><button type="button" class="note-action note-action-delete" data-note-delete data-note-id="${n.id}"><i class="feather icon-trash-2"></i></button></div>` : ""}</div>`;
              return `<div class="note-row ${me ? "me" : "other"}" data-note-id="${n.id}">${me ? bubble + `<img class="note-avatar" src="${img}" alt="">` : `<img class="note-avatar" src="${img}" alt="">` + bubble}</div>`;
            }

            function adjustNotesCounters(delta) {
                const badge = document.getElementById("notesCountBadge");
                if (badge) {
                  const next = Math.max(0, Number(badge.dataset.count || 0) + delta);
                  badge.dataset.count = String(next);
                  badge.textContent = shortNum(next);
                }
                const cId = document.getElementById("notesCustomerId")?.value;
                const aId = document.getElementById("notesAlternativeId")?.value;
                const pId = document.getElementById("notesProductId")?.value;

                if (!cId || !aId) return;

                const selector = `
                    .card[data-customer-id="${CSS.escape(cId)}"][data-alternative-id="${CSS.escape(aId)}"][data-product-id="${CSS.escape(pId)}"] .badge-notes,
                    tr[data-customer-id="${CSS.escape(cId)}"][data-alternative-id="${CSS.escape(aId)}"][data-product-id="${CSS.escape(pId)}"] .badge-notes
                `;

                document.querySelectorAll(selector).forEach((b) => {
                  const next = Math.max(0, Number(b.dataset.count || 0) + delta);
                  b.dataset.count = String(next);
                  b.textContent = shortNum(next);
                  b.style.display = next > 0 ? 'block' : 'none';
                });
            }

          async function openNotesDrawerFor(cId, aId, pId, title, lId, productName = '') {
              const drawer = qs("#notesDrawer"), list = qs("#notesList"), titleEl = qs("#notesTitle");
              const fC = qs("#notesCustomerId"), fA = qs("#notesAlternativeId"), fP = qs("#notesProductId");
              const fL = qs("#notesLeadProductListId"); // <--- Select the new hidden input

              titleEl.textContent = title || "Kunden-Notizen";
              drawer.dataset.customerId = cId || "";
              drawer.dataset.alternativeId = aId || "";
              drawer.dataset.productId = pId || "";
              drawer.dataset.leadProductListId = lId || "";
              drawer.dataset.productName = productName || "";
              drawer.classList.add("open");
              qs("#notesBackdrop").classList.add("show");
              document.body.style.overflow = "hidden";

              ensureNoteQuill();
              setNoteEditorHTML("");
              setNotesTab("notes");

              fC.value = cId; 
              fA.value = aId; 
              fP.value = pId || "";
              if (fL) fL.value = lId || ""; // <--- Set the new hidden input value

              // Clear old report panels so they don't show wrong data briefly
              const rPanel = document.getElementById("notesReport");
              const cPanel = document.getElementById("customerReportList");
              if(rPanel) rPanel.innerHTML = "";
              if(cPanel) cPanel.innerHTML = "";

              try {
                  const params = new URLSearchParams({ customer_id: cId, alternative_id: aId, per_page: 50 });
                  if (pId) params.set("product_id", pId);

                  const payload = await safeFetchJSON(`${APP.endpoints.notesIndex}?${params.toString()}`);
                  const items = (Array.isArray(payload?.notes) ? payload.notes : payload || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                  list.innerHTML = items.map(noteHTML).join("");

                  const total = payload?.total ?? items.length;

                  // Update Header Badge
                  const headerBadge = document.getElementById("notesCountBadge");
                  if (headerBadge) { headerBadge.dataset.count = String(total); headerBadge.textContent = shortNum(total); }

                  // Update the specific Tab Badge
                  const tabBadge = document.getElementById("tabBadgeNotes");
                  if (tabBadge) { tabBadge.textContent = total; }

                  list.scrollTop = list.scrollHeight;
              } catch (e) { 
                  Swal.fire("Fehler", e.message, "error"); 
              }

              loadNotesReport();
              loadCustomerReport();
          }
            // NOTE DRAWER CLOSE LOGIC
            const closeNotes = () => {
                qs("#notesDrawer")?.classList.remove("open");
                qs("#notesBackdrop")?.classList.remove("show");
                document.body.style.overflow = "";
            };
            qs("#notesBackdrop")?.addEventListener("click", closeNotes);
            qsa("[data-notes-close]").forEach(b => b.addEventListener("click", closeNotes));

            // NOTE SUBMIT LOGIC
          qs("#notesForm").onsubmit = async (ev) => {
              ev.preventDefault();
              const text = getNoteEditorHTML();
              if (!text) return;

              // Grab all hidden inputs
              const fC = qs("#notesCustomerId");
              const fA = qs("#notesAlternativeId");
              const fP = qs("#notesProductId");
              const fL = qs("#notesLeadProductListId"); // <--- Grab the new hidden input

              try {
                  const res = await safeFetchJSON(APP.endpoints.notesStore, { 
                      method: "POST", 
                      headers: { 
                          "Content-Type": "application/json", 
                          "X-CSRF-TOKEN": CSRF(), 
                          "X-Requested-With": "XMLHttpRequest" 
                      }, 
                      body: JSON.stringify({ 
                          customer_id: Number(fC.value), 
                          alternative_id: Number(fA.value), 
                          product_id: fP.value ? Number(fP.value) : null, 
                          lead_product_list_id: fL && fL.value ? Number(fL.value) : null, // <--- Add to payload
                          description: text 
                      }) 
                  });

                  qs("#notesList").insertAdjacentHTML("beforeend", noteHTML(res.note || res));
                  qs("#notesList").scrollTop = qs("#notesList").scrollHeight;
                  setNoteEditorHTML("");
                  adjustNotesCounters(+1);
              } catch (e) { 
                  Swal.fire("Fehler", e.message, "error"); 
              }
          };
            document.addEventListener("submit", async (e) => {
              const form = e.target.closest(".ap-report-create-form");
              if (!form) return;
              e.preventDefault();
              const title = (form.querySelector('input[name="title"]')?.value || "").trim();
              const content = (form.querySelector('textarea[name="content"]')?.value || "").trim();
              if (!title || !content) { Swal.fire("Hinweis", "Titel und Text sind Pflichtfelder.", "info"); return; }
              const appointmentId = form.dataset.appointmentId || null;
              try {
                const payload = { title, content, stage: (form.querySelector('select[name="stage"]')?.value || "").trim(), report: `${title}\n\n${content}`, report_date: form.querySelector('input[name="report_date"]')?.value || null };
                const res = await safeFetchJSON(APP.endpoints.reportsStore(appointmentId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify(payload) });
                if (!res || res.status !== "ok") throw new Error(res?.message || "Fehler.");
                const group = form.closest(".ap-appointment-group");
                group?.querySelector(".ap-report-list")?.insertAdjacentHTML("afterbegin", res.html);
                form.reset();
                group.querySelector(".ap-report-create-wrapper").style.display = "none";
                Swal.fire("Gespeichert", "Report wurde hinzugefügt.", "success");
              } catch (err) { Swal.fire("Fehler", err.message, "error"); }
            });

            document.addEventListener("click", async (e) => {
              const btn = e.target.closest(".ap-report-like, .ap-report-dislike");
              if (!btn) return;
              const card = btn.closest(".ap-report-card");
              const reportId = card.getAttribute("data-report-id");
              if (!reportId) return;
              let reaction = btn.classList.contains("ap-report-like") ? "like" : "dislike";
              if (btn.classList.contains("is-active")) reaction = "none";
              try {
                const res = await safeFetchJSON(APP.endpoints.reportsReact(reportId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify({ reaction }) });
                card.querySelector(".ap-report-like-count").textContent = res.likes ?? 0;
                card.querySelector(".ap-report-dislike-count").textContent = res.dislikes ?? 0;
                card.querySelectorAll(".ap-report-like, .ap-report-dislike").forEach((b) => b.classList.remove("is-active"));
                if (res.my_reaction === "like") card.querySelector(".ap-report-like")?.classList.add("is-active");
                else if (res.my_reaction === "dislike") card.querySelector(".ap-report-dislike")?.classList.add("is-active");
              } catch (err) { Swal.fire("Fehler", err.message, "error"); }
            });

            document.addEventListener("click", (e) => {
              const btn = e.target.closest(".ap-open-report-form");
              if (!btn) return;
              const wrapper = btn.closest(".ap-appointment-group").querySelector(".ap-report-create-wrapper");
              const isVisible = wrapper.style.display !== "none";
              wrapper.style.display = isVisible ? "none" : "block";
              if (!btn.dataset.originalLabel) btn.dataset.originalLabel = btn.innerHTML;
              btn.innerHTML = !isVisible ? `<i class="feather icon-file-text"></i> Report schließen` : btn.dataset.originalLabel;
            });

            document.addEventListener("click", (e) => {
              const toggleBtn = e.target.closest("[data-report-toggle-comments]");
              if (!toggleBtn) return;
              const section = toggleBtn.closest(".ap-report-card").querySelector(".ap-report-comments");
              if (section.hasAttribute("hidden")) section.removeAttribute("hidden"); else section.setAttribute("hidden", "");
            });

            document.addEventListener("click", async (e) => {
              const submitBtn = e.target.closest(".ap-report-comment-submit");
              if (!submitBtn) return;
              const card = submitBtn.closest(".ap-report-card");
              const reportId = card.getAttribute("data-report-id");
              const textarea = card.querySelector(".ap-report-comment-text");
              const text = textarea.value.trim();
              if (!text) return;
              try {
                const res = await safeFetchJSON(APP.endpoints.reportsComment(reportId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify({ comment: text }) });
                if (res && typeof res.html === "string") {
                  card.querySelector(".ap-report-comments-list").insertAdjacentHTML("beforeend", res.html);
                  const toggleBtn = card.querySelector("[data-report-toggle-comments]");
                  const current = parseInt(toggleBtn.textContent.match(/(\d+)/)?.[1] || 0, 10);
                  toggleBtn.innerHTML = `<i class="feather icon-message-circle mr-25"></i> Kommentare (${current + 1})`;
                }
                textarea.value = "";
              } catch (err) { Swal.fire("Fehler", err.message, "error"); }
            });

            document.addEventListener("click", (e) => {
              const btn = e.target.closest("[data-notes-tab]");
              if (!btn) return;
              const tab = btn.dataset.notesTab;
              setNotesTab(tab);
              if (tab === "report") loadNotesReport();
              else if (tab === "customerReport") loadCustomerReport();
            });

            /* --------------------- Custom card menu (kb-menu) ------------------------ */
            (function () {
              const closeAllMenus = () => {
                document.querySelectorAll(".kb-menu-dropdown").forEach((d) => d.setAttribute("hidden", ""));
                document.querySelectorAll('[data-act="custom-menu-toggle"][aria-expanded="true"]').forEach((btn) => btn.setAttribute("aria-expanded", "false"));
              };
              document.addEventListener("click", (e) => {
                const toggleBtn = e.target.closest('[data-act="custom-menu-toggle"]');
                if (toggleBtn) {
                  const dd = toggleBtn.parentElement.querySelector(".kb-menu-dropdown");
                  const isOpen = dd && !dd.hasAttribute("hidden");
                  closeAllMenus();
                  if (dd && !isOpen) { dd.removeAttribute("hidden"); toggleBtn.setAttribute("aria-expanded", "true"); }
                  e.stopImmediatePropagation();
                  return;
                }
                const item = e.target.closest(".kb-menu-item");
                if (item) {
                 const card = item.closest(".card");
                  const type = item.dataset.menu;
                  const runState = item.dataset.run;

                  // IMPORTANT: If it's a Play/Pause/Stop button, DO NOT call stopPropagation.
                  // Let it bubble up to the global "Run" handler.
                  if (runState) {
                      closeAllMenus();
                      return; // Let the global handler take over
                  }

                  closeAllMenus();

                  if (type === "verlauf" && card) {
                      const a = document.createElement("a");
                      a.href = `/lead/process/history/${encodeURIComponent(card.dataset.customerId)}/${encodeURIComponent(card.dataset.alternativeId)}/${encodeURIComponent(card.dataset.productId)}`;
                      a.setAttribute("data-lh-history", "");
                      a.style.display = "none";
                      document.body.appendChild(a);
                      a.click();
                      a.remove();
                  }
                  if (type === "verlauf" && card) {
                    const a = document.createElement("a");
                    a.href = `/lead/process/history/${encodeURIComponent(card.dataset.customerId)}/${encodeURIComponent(card.dataset.alternativeId)}/${encodeURIComponent(card.dataset.productId)}`;
                    a.setAttribute("data-lh-history", "");
                    a.style.display = "none";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                  }
                  if (type === "product-stage-info" && card) {
                    showProductStageInfoFromElement(card);
                  }
                  if (type === "ticket" && card) { /* Ticket Logic */ }
                  if (type === "termin" && card) {
                     const name = card.querySelector(".card-header strong")?.textContent?.trim() || "Kunde";
                     card.dispatchEvent(new CustomEvent("open-appointments", { bubbles: true, detail: { customerId: card.dataset.customerId, alternativeId: card.dataset.alternativeId, productId: card.dataset.productId, title: `Termine • ${name}`, full_address: card.dataset.fullAddress || "" } }));
                  }
                  if (type === "aufgabe" && card) {
                     const name = card.querySelector(".card-header strong")?.textContent?.trim() || "Kunde";
                     card.dispatchEvent(new CustomEvent("open-personal-tasks", { bubbles: true, detail: { customerId: card.dataset.customerId, alternativeId: card.dataset.alternativeId, productId: card.dataset.productId, leadProductListId: card.dataset.leadProductListId || card.dataset.leadProductId || "", title: `Aufgaben • ${name}` } }));
                  }
                  e.stopImmediatePropagation();
                }
              });
              document.addEventListener("click", (e) => { if (!e.target.closest(".kb-menu")) closeAllMenus(); });
            })();

            /* --------------------------- Junk tab ------------------------- */
              async function fetchJunkTab(qsStr) {
                const pane = document.querySelector("#junk");
                if (!pane) return;

                try {
                  const res = await fetch(`${APP.endpoints.junk}${qsStr ? `?${qsStr}` : ""}`, {
                    headers: { Accept: "text/html", "X-Requested-With": "XMLHttpRequest" },
                    credentials: "same-origin",
                  });

                  const html = await res.text();

                  // Replace the whole tab content (safe and avoids nested #junkInner)
                  pane.innerHTML = html;
                } catch (e) {}
              }


              document.addEventListener("click", async (e) => {
                const btn = e.target.closest(".btn-restore");
                if (!btn) return;

                const row = btn.closest(".oc-item") || btn.closest("tr");
                if (!row) return;

                const select = row.querySelector(".restore-select");
                const target = select?.value;

                if (!target) {
                    Swal.fire("Hinweis", "Bitte Zielphase wählen.", "info");
                    return;
                }

                const customerId = row.dataset.customerId || "";
                const alternativeId = row.dataset.alternativeId || row.dataset.altId || "";
                const productId = row.dataset.productId || "";
                const leadProductId = btn.dataset.id || row.dataset.leadProductId || "";

                if (!customerId || !alternativeId || !productId) {
                    Swal.fire("Fehler", "Fehlende IDs in der Zeile (customer/alternative/product).", "error");
                    return;
                }

                const { value: reason, isConfirmed } = await Swal.fire({
                    title: "Grund",
                    input: "textarea",
                    inputPlaceholder: "Optionaler Grund für die Wiederherstellung…",
                    showCancelButton: true,
                    confirmButtonText: "Wiederherstellen",
                    cancelButtonText: "Abbrechen"
                });

                if (!isConfirmed) return;

                try {
                    const url = `${APP.endpoints.changeStage}/${encodeURIComponent(customerId)}/${encodeURIComponent(alternativeId)}/${encodeURIComponent(productId)}`;

                    const res = await postJSON(url, {
                        lead_product_id: Number(leadProductId),
                        stage: target,
                        description: reason || "",
                        source: "junk"
                    });

                    if (!res?.success) {
                        throw new Error(res?.message || "Fehler beim Wiederherstellen");
                    }

                    row.remove();

                    Swal.fire({
                        icon: "success",
                        title: "Wiederhergestellt",
                        text: "Der Lead wurde erfolgreich verschoben.",
                        timer: 1400,
                        showConfirmButton: false
                    });

                    if (window.LeadUI?.silentRefreshBoth) {
                        window.LeadUI.silentRefreshBoth();
                    }
                } catch (err) {
                    Swal.fire("Fehler", err?.message || "Serverfehler", "error");
                }
            });

            /* ====================== Live Feed Modal ================== */
          /* ====================== Live Feed Modal (Robust) ================== */
            const LiveFeedModal = (() => {
              const modalId = "liveFeedModal";
              const backdropId = "liveFeedModalBackdrop";

              // Cache DOM elements
              const getEl = (id) => document.getElementById(id);
              const listEl = () => getEl("liveFeedModalList");
              const countEl = () => getEl("liveFeedModalCount");

              let allItems = [];
              let typeFilter = "all";

              function render() {
                const list = listEl();
                const count = countEl();
                if (!list) return;

                const items = typeFilter === "all" ? allItems : allItems.filter(i => i.type === typeFilter);

                if (count) count.textContent = `${items.length} von ${allItems.length} Einträgen`;

                list.innerHTML = items.length ? items.map(i => `
                  <div class="lfm-item">
                    <div class="lfm-item-type ${i.type === 'task' ? 'task' : i.type === 'appointment' ? 'appointment' : 'ticket'}">
                      ${i.type_label || i.type}
                    </div>
                    <div class="lfm-item-main">
                      <div class="lfm-item-title">${i.title}</div>
                      <div class="lfm-item-sub">${i.text}</div>
                    </div>
                    <div class="lfm-item-time">
                      <span>${i.when_human}</span>
                    </div>
                  </div>`).join("") : `<div class="lfm-empty">Keine Aktivitäten gefunden.</div>`;
              }

              function open(items) {
                console.log("Opening Modal with items:", items); // Debug
                allItems = Array.isArray(items) ? items : [];
                typeFilter = "all";

                const modal = getEl(modalId);
                const backdrop = getEl(backdropId);

                if(modal && backdrop) {
                    render();
                    modal.style.display = "flex"; // Force flex
                    backdrop.style.display = "block";
                    document.body.style.overflow = "hidden";
                } else {
                    console.error("LiveFeedModal elements not found in DOM.");
                }
              }

              function close() {
                const modal = getEl(modalId);
                const backdrop = getEl(backdropId);
                if (modal) modal.style.display = "none";
                if (backdrop) backdrop.style.display = "none";
                document.body.style.overflow = "";
              }

              // Attach global listeners once
              document.addEventListener("DOMContentLoaded", () => {
                  getEl(backdropId)?.addEventListener("click", close);
                  getEl("liveFeedModalClose")?.addEventListener("click", close);

                  getEl("liveFeedTypeFilters")?.addEventListener("click", (e) => {
                      const btn = e.target.closest(".lfm-filter-btn");
                      if (btn) {
                          typeFilter = btn.dataset.type;
                          document.querySelectorAll(".lfm-filter-btn").forEach(b => b.classList.toggle("is-active", b === btn));
                          render();
                      }
                  });
              });

              return {
                open,
                close,
                openForCard: (wrapper) => {
                   // This wrapper is the .card or .list-row-item
                   if(!wrapper) return;
                   // Use the shared LiveFeed module to get data
                   const items = window.LeadUI.liveFeed.getItemsForCard(wrapper);

                   if (items && items.length > 0) {
                       open(items);
                   } else {
                       // If data isn't loaded yet, try loading it then opening
                       // This uses the wrapper's dataset
                       if(window.LeadUI.liveFeed.loadForCard) {
                           window.LeadUI.liveFeed.loadForCard(wrapper).then(() => {
                               // Retry getting items after fetch
                               const freshItems = window.LeadUI.liveFeed.getItemsForCard(wrapper);
                               open(freshItems);
                           });
                       }
                   }
                }
              };
            })();

            /* ====================== Per-card Live Feed ================== */
            const LiveFeed = (() => {
              const registry = new WeakMap();
              function createInstance(root) {
                let items = [], index = 0, timer = null;
                const textEl = root.querySelector("[data-feed-text]"); 
                const render = () => {
                  if (!items.length) { root.style.display = "none"; return; }
                  root.style.display = "";
                  const item = items[index];
                  if(textEl) textEl.textContent = item.text || "";
                  root.querySelector("[data-feed-title]").textContent = item.title || "Aktivität";
                  root.querySelector("[data-feed-time]").textContent = item.when_human || "";
                };
                const go = (step) => { index = (index + step + items.length) % items.length; render(); };
                return { 
                  setItems: (next) => { items = next; index = 0; render(); }, 
                  loadForTuple: async (c, a, p, l) => {
                      try {
                          const res = await safeFetchJSON(`${APP.endpoints.liveFeed}?customer_id=${c}`);
                          items = res.items || [];
                          render();
                      } catch(e) { console.error(e); }
                  },
                  getItems: () => items 
                };
              }
              function getInstance(root) {
                  if (!root) return null;
                  if (!registry.has(root)) registry.set(root, createInstance(root));
                  return registry.get(root);
              }
              return {
                  loadForCard: (card) => getInstance(card.querySelector("[data-feed-root]"))?.loadForTuple(card.dataset.customerId),
                  getItemsForCard: (card) => getInstance(card.querySelector("[data-feed-root]"))?.getItems() || [],
                  bootstrapFromFirstCard: () => { const c = qs("#kanban .card"); if(c) getInstance(c.querySelector("[data-feed-root]"))?.loadForTuple(c.dataset.customerId); },
                  bootstrapAllCards: () => { qsa("#kanban .card").forEach(c => getInstance(c.querySelector("[data-feed-root]"))?.loadForTuple(c.dataset.customerId)); }
              };
            })();

            /* ------------------------------- Expose Core ----------------------------- */

            document.addEventListener("DOMContentLoaded", () => {
              try { bindWorkflowControls(); } catch (e) { console.warn("Workflow controls init failed", e); }
            });

            window.KanbanStageTime = {
              parseStageHistorySafe,
              currentStageEnteredAt,
              formatDateTimeDE,
              stageDurationText,
              stageTimeHTML,
              refreshVisibleStageDurations,
              refreshCardStageTime,
            };
            window.parseStageHistorySafe = parseStageHistorySafe;
            window.refreshCardStageTime = refreshCardStageTime;
            window.refreshVisibleStageDurations = refreshVisibleStageDurations;

            setInterval(refreshVisibleStageDurations, 60000);
            document.addEventListener("DOMContentLoaded", refreshVisibleStageDurations);

            window.LeadUI = {
              APP, State,
              utils: { qs, qsa, CSRF, fmtDE, getDateAgeIndicator, featherRefreshSoon, shortNum, canonicalStage, escapeHTML, stageFilterExcludes, saveToLocal, restoreFromLocal, syncURL, initFromURL, closeOverlays, enforceActionVisibility, isBackward, stageRank, workflowColumnKey, workflowLabel, workflowStageIdFromKey },
              net: { safeFetchJSON, postJSON, cancel },
              filters: { initSelect2, getFilterValues, updateFilterBadges, buildFilterQS, Drawer },
              kanban: { ensureColumns, clearColumns, colContent, updateCounts, statusBadge, buildStatusBlock, offerWorkflowHTML, applyRunStateUI, cardId, cardHTML, mountOrUpdateCard, renderKanbanDiff, renderKanbanIncremental, autoChunk },
              notes: { openNotesDrawerFor, updateNoteBadgesForVisibleCards },
              partials: { fetchJunkTab, fetchTicketsTab: async () => {} },
              liveFeed: LiveFeed,
              liveFeedModal: LiveFeedModal,
            };
          })();
          

/* ===================== Extracted inline script block #13 ===================== */
            (function () {
              "use strict";

              // 1. Safe Access to Core Modules
              const LeadUI = window.LeadUI || {};
              const { APP, utils, net, notes, kanban, liveFeed, liveFeedModal } = LeadUI;

              // ---------------------------------------------------------
              // 2. Global Helpers for this Closure
              // ---------------------------------------------------------
              const safeStr = (v) => (v == null ? "" : String(v));
              const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

              const fmtDE = (v) => {
                try {
                  return v ? new Date(v).toLocaleDateString("de-DE") : "-";
                } catch {
                  return "-";
                }
              };

              // ---------------------------------------------------------
              // 3. HTML Generator: Live Feed Container
              // ---------------------------------------------------------
              function listFeedHTML() {
                return `
                  <div class="live-feed-bar list-live-feed card-live-feed"
                      data-feed-root
                      data-feed-count="0"
                      style="display:none; margin-top:0.6rem; width: 100%; max-width: 100%;">
                    <div class="live-feed-left">
                      <div class="live-feed-icon"><i class="feather icon-zap"></i></div>
                    </div>
                    <div class="live-feed-body">
                      <div class="live-feed-line" data-feed-empty>
                        <span class="live-feed-title">Keine Aktivitäten</span>
                        <span class="live-feed-dot">•</span>
                        <span class="live-feed-text">Noch keine Einträge.</span>
                      </div>
                      <div class="live-feed-line" data-feed-line>
                        <span class="live-feed-title" data-feed-title>Aktivität</span>
                        <span class="live-feed-dot">•</span>
                        <span class="live-feed-text" data-feed-text>Details…</span>
                      </div>
                      <div class="live-feed-meta">
                        <span class="live-feed-pill" data-feed-pill>Info</span>
                        <span class="live-feed-time">
                          <i class="feather icon-clock mr-25"></i>
                          <span data-feed-time>–</span>
                        </span>
                        <span class="live-feed-counter" data-feed-counter></span>
                      </div>
                    </div>
                    <div class="live-feed-controls">
                      <button type="button" class="live-feed-btn" title="Zurück" data-feed-prev>
                        <i class="feather icon-skip-back"></i>
                      </button>
                      <button type="button" class="live-feed-btn" title="Pause / Abspielen" data-feed-toggle>
                        <i class="feather icon-pause" data-feed-icon-pause></i>
                        <i class="feather icon-play d-none" data-feed-icon-play></i>
                      </button>
                      <button type="button" class="live-feed-btn" title="Weiter" data-feed-next>
                        <i class="feather icon-skip-forward"></i>
                      </button>
                      <button type="button" class="live-feed-btn" title="Vergrößern" data-feed-open-modal>
                        <i class="feather icon-maximize-2"></i>
                      </button>
                    </div>
                  </div>
                `;
              }

              // ---------------------------------------------------------
              // 4. HTML Generator: Avatar List Item
              // ---------------------------------------------------------
              function avatarLiFromEmp(emp, { withData = false, assignedBy = "", assignedAt = "", stageLabel = "" } = {}) {
                if (!emp) return "";
                const EMP_SRC = APP.EMP_SRC || '/images/employee';

                const id = Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0;
                const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;
                const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || `#${id}`;

                return `
                  <li class="avatar pull-up"
                      ${withData ? `data-emp-id="${esc(id)}"` : ""}
                      ${withData ? `data-assigned-by="${esc(assignedBy)}"` : ""}
                      ${withData ? `data-assigned-at="${esc(assignedAt)}"` : ""}
                      ${withData ? `data-stage-label="${esc(stageLabel)}"` : ""}
                      title="${esc(name)}"
                      style="margin-left:-8px;">
                    <img class="media-object rounded-circle"
                        src="${esc(img)}"
                        width="26" height="26"
                        alt="${esc(name)}"
                        style="border:2px solid #fff; object-fit:cover;">
                  </li>
                `;
              }

              // ---------------------------------------------------------
              // 5. HTML Generator: Employee & Team Column
              // ---------------------------------------------------------
              function listEmpAndTeamHTML(lead) {
                const stageKey = utils.canonicalStage(lead?.stage);
                const stageLabel = APP.stageNames?.[stageKey] || stageKey;

                const main = [];
                if (lead?.employee && (lead.employee.employee_id || lead.employee.id)) main.push(lead.employee);
                if (lead?.field_employee && (lead.field_employee.employee_id || lead.field_employee.id)) main.push(lead.field_employee);

                const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                let teamMembers = [];
                if (teamAssignments.length > 0) {
                    teamMembers = teamAssignments;
                } else if (Array.isArray(lead?.team_members)) {
                    teamMembers = lead.team_members.map(m => ({ member: m }));
                } else if (Array.isArray(lead?.teams)) {
                    teamMembers = lead.teams.map(m => ({ member: m }));
                }

                if (!main.length && !teamMembers.length) return `<span class="text-muted small">&ndash;</span>`;

                const mainHtml = main.length
                  ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center">
                      ${main.map((e) => avatarLiFromEmp(e, { withData: false })).join("")}
                    </ul>`
                  : "";

                const teamHtml = teamMembers.length
                  ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center"
                        data-team-hover
                        style="margin-left:10px; padding-left:10px; border-left:1px solid #e0e0e0;">
                      ${teamMembers.map((a) => {
                        const member = a?.member || a;
                        const u = a?.assigned_by_user;
                        let ab = "";
                        if (u && (u.name || u.lastname)) ab = `${safeStr(u.lastname)} ${safeStr(u.name)}`.trim();
                        else if (a?.assigned_by) ab = `Mitarbeiter #${a.assigned_by}`;
                        const at = safeStr(a?.assigned_at || "").trim();
                        return avatarLiFromEmp(member, { withData: true, assignedBy: ab, assignedAt: at, stageLabel });
                      }).join("")}
                    </ul>`
                  : "";

                return `<div class="d-flex align-items-center">${mainHtml}${teamHtml}</div>`;
              }

              // ---------------------------------------------------------
              // 6. MAIN FUNCTION: Build Table Row
              // ---------------------------------------------------------
              function buildRowHTML(lead) {
                    // 1. Define helper 'esc' immediately to avoid errors
                    const safeStr = (v) => (v == null ? "" : String(v));
                    const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        '"': "&quot;",
                        "'": "&#039;"
                    })[m]);

                    // Helper for Date formatting
                    const fmtDE = (v) => {
                        try {
                            return v ? new Date(v).toLocaleDateString("de-DE") : "-";
                        } catch {
                            return "-";
                        }
                    };

                    // 2. DEFINE DISPLAY NAME HERE (This fixes the "not defined" error!)
                    const cName = safeStr(lead?.customer_name).trim();
                    const cLastname = safeStr(lead?.customer_lastname).trim();
                    const cFirma = safeStr(lead?.firma).trim();
                    const displayName = `${cLastname} ${cName}`.trim() || cFirma || "Unbekannt";

                    const stageKey = (window.LeadUI && window.LeadUI.utils) ? window.LeadUI.utils.canonicalStage(lead?.stage) : (lead?.stage || "lead");
                    const cId = lead?.customer_id ?? "";
                    const aId = lead?.alternative_id ?? "";
                    const pId = lead?.product_id ?? "";
                    const lpId = lead?.lead_product_id ?? "";
                    const ws = String(lead?.work_status || "playing").toLowerCase();

                    // 3. Get Status Block from Kanban (Core)
                    const statusBlockHTML = (window.LeadUI && window.LeadUI.kanban) ? window.LeadUI.kanban.buildStatusBlock(lead) : `<span class="badge badge-secondary">${stageKey}</span>`;

                    // 4. Get Live Feed HTML
                    const liveFeedRow = typeof listFeedHTML === 'function' ? listFeedHTML() : '';

                    // 5. Meta Logic (Assigned By...)
                    const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                    let teamsRaw = lead?.teams;
                    if (typeof teamsRaw === "string") {
                        try { teamsRaw = JSON.parse(teamsRaw); } catch { teamsRaw = []; }
                    }
                    if (!Array.isArray(teamsRaw)) teamsRaw = [];

                    const assignments = teamAssignments.length ?
                        teamAssignments :
                        teamsRaw.map((t) => ({
                            assigned_at: t?.assigned_at ?? null,
                            assigned_at_iso: t?.assigned_at_iso ?? null,
                            assigned_by: t?.assigned_by ?? null,
                            assigned_by_user: t?.assigned_by_user ?? null,
                            stage_label: t?.stage_label ?? null,
                        }));

                    const parseAssignedAt = (a) => {
                        const raw = (a?.assigned_at_iso || a?.assigned_at || "").trim();
                        if (!raw) return 0;
                        const isoish = raw.includes("T") ? raw : raw.replace(" ", "T");
                        const ts = Date.parse(isoish);
                        return Number.isFinite(ts) ? ts : 0;
                    };

                    const latestA = assignments.reduce((best, a) => {
                        const ta = parseAssignedAt(a);
                        const tb = parseAssignedAt(best);
                        return ta > tb ? a : best;
                    }, null);

                    const assignedBy = (() => {
                        const u = latestA?.assigned_by_user;
                        if (u && (u.name || u.lastname)) return `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
                        const id = Number(latestA?.assigned_by ?? 0);
                        return id > 0 ? `Mitarbeiter #${id}` : "";
                    })();

                    const assignedAtRaw = (latestA?.assigned_at_iso || latestA?.assigned_at || "").trim();
                    const STAGE_DE = {
                        lead: "Lead",
                        offer: "Angebot",
                        follow_up: "Nachfassen",
                        accepted: "Annehmen",
                        deal: "Auftrag",
                        project: "Montage",
                        completed: "Abschluss",
                        archive: "Archiv",
                        junk: "Junk"
                    };

                    const phaseLabel = (() => {
                        const lbl = (latestA?.stage_label || "").trim();
                        if (lbl) return lbl;
                        const key = String(latestA?.stage || "").trim().toLowerCase();
                        return STAGE_DE[key] || "";
                    })();

                    const assignedMetaHTML =
                        assignedBy || assignedAtRaw || phaseLabel ?
                        `<div class="small text-muted mt-1">
                              ${phaseLabel ? `<span class="mr-2"><i class="feather icon-layers mr-25"></i><span>Phase: <strong>${esc(phaseLabel)}</strong></span></span><span class="mx-1">•</span>` : ``}
                              <i class="feather icon-user mr-25"></i><span>Zugewiesen von: <strong>${esc(assignedBy || "-")}</strong></span>
                              <span class="mx-1">•</span>
                              <i class="feather icon-calendar mr-25"></i><span>${esc(assignedAtRaw ? fmtDE(assignedAtRaw) : "-")}</span>
                            </div>` :
                        "";

                    return `
                        <tr id="row-${esc(lpId)}"
                            class="list-row-item"
                            data-customer-id="${esc(cId)}"
                            data-alternative-id="${esc(aId)}"
                            data-product-id="${esc(pId)}"
                            data-lead-product-id="${esc(lpId)}"
                            data-stage="${esc(stageKey)}"
                            data-product-stage-id="${esc(lead?.product_stage_id || '')}"
                            data-product-task-phase-id="${esc(lead?.product_task_phase_id || '')}"
                            data-product-stage-name="${esc(lead?.product_stage_name || '')}"
                            data-product-task-phase-name="${esc(lead?.product_task_phase_name || '')}"
                            data-initial="${esc(lead?.initial || '')}"
                            data-run-state="${esc(ws)}"
                            data-stage-history="${esc(typeof lead?.stage_history === 'string' ? lead?.stage_history : JSON.stringify(lead?.stage_history || []))}"
                            data-team-assignments="${esc(JSON.stringify(Array.isArray(lead?.team_assignments) ? lead.team_assignments : []))}">

                          <td style="width: 110px;" class="list-date-cell">
                            ${window.LeadUI.utils.getDateAgeIndicator(lead?.created_at, stageKey)}
                            ${lead?.created_at ? fmtDE(lead.created_at) : "-"}
                          </td>

                          <td style="min-width: 350px;">
                            <a href="/new_lead_profile/${encodeURIComponent(safeStr(cId))}" class="customer-link" style="font-size:1.05rem;">
                              ${esc(displayName)}
                            </a>

                            ${assignedMetaHTML}

                            <div class="list-action-bar">
                              <button type="button" class="btn-list-icon" data-menu="termin" title="Termin">
                                <i class="feather icon-calendar"></i>
                                <span class="badge-notes" data-ap-count style="display:none">0</span>
                              </button>
                              <button type="button" class="btn-list-icon" data-menu="aufgabe" title="Aufgabe">
                                <i class="feather icon-check-square"></i>
                                <span class="badge-notes" data-pt-count style="display:none">0</span>
                              </button>

                              <span style="border-left:1px solid #ddd; height:14px; margin:0 4px;"></span>

                              <button type="button" class="btn-list-icon note" data-open-notes data-customer="${esc(cId)}" data-alt="${esc(aId)}" data-product="${esc(pId)}" data-lead-product-list-id="${esc(lead?.lead_product_id || lead?.lead_product_list_id || lead?.id || '')}" data-customer-name="${esc(displayName)}" data-product-name="${esc(lead?.article_group || lead?.product_name || lead?.product || lead?.initial || '')}">
                                <i class="feather icon-message-square"></i>
                                <span class="badge-notes" data-count="0" style="display:none">0</span>
                              </button>

                              <div class="btn-group">
                                <button type="button" class="btn-list-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item text-success" data-run="playing"><i class="feather icon-play mr-50"></i> Start</button>
                                    <button class="dropdown-item text-warning" data-run="paused"><i class="feather icon-pause mr-50"></i> Pause</button>
                                    <button class="dropdown-item text-danger" data-run="stopped"><i class="feather icon-square mr-50"></i> Stopp</button>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/lead/process/history/${encodeURIComponent(safeStr(cId))}/${encodeURIComponent(safeStr(aId))}/${encodeURIComponent(safeStr(pId))}" data-lh-history>
                                        <i class="feather icon-activity mr-50"></i> Verlauf
                                    </a>
                                    <button type="button" class="dropdown-item" data-menu="product-stage-info">
                                        <i class="feather icon-info mr-50"></i> Produktstatus
                                    </button>
                                </div>
                              </div>
                            </div>

                            ${liveFeedRow}
                          </td>

                          <td>${esc(lead?.city ?? "")}</td>
                          <td>${esc(lead?.initial ?? "")}</td>
                          <td>${typeof listEmpAndTeamHTML === 'function' ? listEmpAndTeamHTML(lead) : ''}</td>

                          <td>
                            ${statusBlockHTML}
                            ${(window.LeadUI && window.LeadUI.kanban && typeof window.LeadUI.kanban.offerWorkflowHTML === "function") ? window.LeadUI.kanban.offerWorkflowHTML(lead) : ""}
                          </td>

                          <td>
                            <select class="form-control stage-select" data-id="${esc(lpId)}">
                              ${Object.entries((window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.stageNames) || {})
                                .filter(([k]) => !["junk", "ticket"].includes(String(k).toLowerCase()))
                                .map(([k, l]) => {
                                  const meta = (window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.stageMeta && window.LeadUI.APP.stageMeta[k]) || {};
                                  return `<option value="${esc(k)}" data-color="${esc(meta.color || "#93c21c")}" data-icon="${esc(meta.icon || "circle")}" ${stageKey === k ? "selected" : ""}>${esc(l)}</option>`;
                                })
                                .join("")}
                            </select>
                          </td>
                        </tr>
                      `;
              }
              // ---------------------------------------------------------
              // 7. Bootstrapper: Activates the Feed on List Load
              // ---------------------------------------------------------
              function bootstrapListLiveFeed(container) {
                  if (!liveFeed || typeof liveFeed.loadForCard !== "function") return;

                  const root = container || document;
                  // MATCH the class used in buildRowHTML (list-row-item)
                  const rows = root.querySelectorAll("tr.list-row-item");

                  if (!rows.length) return;

                  let i = 0;
                  const BATCH = 4;

                  (function pump() {
                    const slice = Array.prototype.slice.call(rows, i, i + BATCH);
                    i += BATCH;
                    slice.forEach((row) => {
                        if(row.dataset.customerId) {
                            // This call makes the AJAX request and removes 'display:none' if data is found
                            liveFeed.loadForCard(row);
                        }
                    });
                    if (i < rows.length) {
                        if ("requestIdleCallback" in window) requestIdleCallback(pump);
                        else setTimeout(pump, 0);
                    }
                  })();
              }

              // ---------------------------------------------------------
              // 8. Update & Fetch Logic
              // ---------------------------------------------------------
              function updateListView(leads, meta) {
                const tbody = utils.qs("#kanbanTableBody");
                if (!tbody) return;

                if (!Array.isArray(leads) || !leads.length) { 
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center p-3 text-muted">Keine Ergebnisse gefunden.</td></tr>'; 
                    return; 
                }

                // Inject HTML
                tbody.innerHTML = leads.map(buildRowHTML).join("");

                // Activate Features
                notes.updateNoteBadgesForVisibleCards(); 
                bootstrapListLiveFeed(tbody);
                if(utils.featherRefreshSoon) utils.featherRefreshSoon();
              }

              // Expose only functions that exist in this script scope.
              // fetchKanbanView is defined in the main Kanban script below, so referencing it here directly breaks the page.
              if (typeof fetchKanbanView === "function") {
                window.LeadUIFetchKanban = fetchKanbanView;
              }
              if (typeof fetchListView === "function") {
                window.LeadUIFetchList = fetchListView;
              }

              function fetchListView(qsStr) {
                  net.safeFetchJSON(`${APP.endpoints.listSearch}?${qsStr}`).then(res => {
                      const leads = res.leads || res.data || [];
                      updateListView(leads);
                  });
              }

              // Initial Load
              document.addEventListener("DOMContentLoaded", () => {
                fetchListView(""); 
              });

              /* ---------------------------------------------------------
                9. EVENT LISTENERS
              --------------------------------------------------------- */

              // A. Notes Click 
              document.addEventListener("click", (e) => {
                  const btn = e.target.closest("[data-open-notes]");
                  if (!btn) return;
                  e.stopPropagation();

                  let name = btn.dataset.customerName || "Kunde";
                  let productName = btn.dataset.productName || "";

                  // Check if we are in the List view
                  const row = btn.closest("tr");
                  if (row) {
                      const link = row.querySelector(".customer-link");
                      if (!btn.dataset.customerName && link) name = link.textContent.trim();
                      productName = productName || row.dataset.productName || row.dataset.productStageName || row.dataset.initial || "";
                  } else {
                      // Check if we are in the Kanban view
                      const card = btn.closest(".card");
                      if (card) {
                          const nameEl = card.querySelector(".card-name");
                          if (!btn.dataset.customerName && nameEl) name = nameEl.textContent.trim();
                          productName = productName || card.dataset.productName || card.dataset.productStageName || card.dataset.initial || "";
                      }
                  }

                  // Extract the lead_product_list_id from the row or card wrapper
                  const wrapper = row || btn.closest(".card");
                  const leadProductId = btn.dataset.leadProductListId || (wrapper ? (wrapper.dataset.leadProductListId || wrapper.dataset.leadProductId) : null);

                  notes.openNotesDrawerFor(
                      btn.dataset.customer,
                      btn.dataset.alt,
                      btn.dataset.product,
                      productName ? `Notizen • ${name} • ${productName}` : `Notizen • ${name}`,
                      leadProductId,
                      productName
                  );
              });
              // B. Live Feed Controls (Maximize, Prev, Next)
              document.addEventListener("click", (e) => {
                  // Look for any button inside the feed controls
                  const btn = e.target.closest(".live-feed-btn");
                  if (!btn) return;

                  const feedRoot = btn.closest(".live-feed-bar");
                  if (!feedRoot) return;

                  // The wrapper is the table row
                  const wrapper = feedRoot.closest("tr.list-row-item") || feedRoot.closest(".card");
                  if (!wrapper) return;

                  e.preventDefault();
                  e.stopPropagation();

                  // 1. Maximize Button
                  if (btn.hasAttribute("data-feed-open-modal")) {
                      if (liveFeedModal && typeof liveFeedModal.openForCard === 'function') {
                          liveFeedModal.openForCard(wrapper);
                      } else {
                          console.error("LeadUI.liveFeedModal is missing or invalid.");
                      }
                  }

                  // Note: Next/Prev/Pause are usually handled via internal state in your 'liveFeed' module. 
                  // If those buttons aren't working, it's because 'liveFeed.js' likely attaches listeners 
                  // locally or not at all for dynamically added list rows. 
                  // Ensure 'liveFeed.js' uses delegation or attaches listeners on 'loadForCard'.
              });

            })();


            document.addEventListener("click", (e) => {
                const btn = e.target.closest('[data-menu]');
                if (!btn) return;

                const menuType = btn.dataset.menu;
                const card = btn.closest('.card') || btn.closest('tr'); // Works for both Kanban and List

                if (menuType === 'termin') {
                    // Trigger your existing Appointment open logic
                    const event = new CustomEvent("open-appointments", {
                        bubbles: true,
                        detail: { 
                            customerId: card.dataset.customerId, 
                            alternativeId: card.dataset.alternativeId, 
                            productId: card.dataset.productId,
                            leadProductListId: card.dataset.leadProductListId || card.dataset.leadProductId || ""
                        }
                    });
                    btn.dispatchEvent(event);
                }

                if (menuType === 'aufgabe') {
                    // Trigger your existing Task open logic
                    const event = new CustomEvent("open-personal-tasks", {
                        bubbles: true,
                        detail: { 
                            customerId: card.dataset.customerId, 
                            alternativeId: card.dataset.alternativeId, 
                            productId: card.dataset.productId,
                            leadProductListId: card.dataset.leadProductListId || card.dataset.leadProductId || ""
                        }
                    });
                    btn.dispatchEvent(event);
                }
            });
          

/* ===================== Extracted inline script block #14 ===================== */
            /* =============================================================================
            * LeadUI – Interactions & Boot (Segment 2/2) — REWRITE
            * - Selection + Drag & Drop (Kanban)
            * - Stage-change flow (SweetAlert + Select2 team + optional reason)
            * - List rendering + pagination (+ LiveFeed row under each list row)
            * - Fetchers (Kanban + List)
            * - All event bindings, keyboard shortcuts
            * - Bootstrap on DOMContentLoaded
            * ============================================================================= */
            (() => {
              "use strict";

              /* -------------------------------------------------------------------------- */
              /* Guard                                                                       */
              /* -------------------------------------------------------------------------- */
              if (!window.LeadUI) {
                console.error("LeadUI missing on window.");
                return;
              }

              const { APP, State, utils, net, filters, kanban, notes, partials, liveFeed } =
                window.LeadUI;

              const {
                qs,
                qsa,
                canonicalStage,
                featherRefreshSoon,
                stageFilterExcludes,
                saveToLocal,
                restoreFromLocal,
                syncURL,
                initFromURL,
                closeOverlays,
                enforceActionVisibility,
                isBackward,
                stageRank,
                workflowLabel: workflowLabelFromCore,
                workflowStageIdFromKey: workflowStageIdFromKeyFromCore,
                escapeHTML: escapeHTMLFromCore,
              } = utils;

              const workflowLabel = typeof workflowLabelFromCore === "function"
                ? workflowLabelFromCore
                : (key) => (APP.stageWorkflow?.mode === "product"
                    ? (APP.stageWorkflow.productStageNames?.[key] || key)
                    : (APP.stageNames?.[canonicalStage(key)] || key));

              const workflowStageIdFromKey = typeof workflowStageIdFromKeyFromCore === "function"
                ? workflowStageIdFromKeyFromCore
                : (key) => {
                    const m = String(key || "").match(/^product_stage_(\d+)$/);
                    return m ? Number(m[1]) : null;
                  };

              const { safeFetchJSON, postJSON, cancel } = net;

              const {
                ensureColumns,
                colContent,
                updateCounts,
                buildStatusBlock,
                applyRunStateUI,
                renderKanbanDiff,
                renderKanbanIncremental,
                autoChunk,
                mountOrUpdateCard: coreMountOrUpdateCard,
                cardId: coreCardId,
              } = kanban;

              // Fix for extracted-script scope: these helpers live inside LeadUI.kanban,
              // not as free variables in this later script block.
              const mountOrUpdateCard = typeof coreMountOrUpdateCard === "function"
                ? coreMountOrUpdateCard
                : function (stageKey, lead, existing) {
                    const id = (typeof coreCardId === "function" ? coreCardId(lead) : (window.cardId ? window.cardId(lead) : ("card-" + (lead?.lead_product_id || lead?.lead_product_list_id || lead?.id || ""))));
                    const card = existing || document.getElementById(id) || document.createElement("div");
                    card.id = id;
                    card.className = card.className || "card";
                    card.dataset.stage = stageKey || lead?.stage || "lead";
                    card.dataset.leadProductId = lead?.lead_product_id || lead?.lead_product_list_id || lead?.id || "";
                    card.innerHTML = `<strong>${esc(lead?.customer_name || lead?.name || lead?.firma || "Kunde")}</strong>`;
                    return card;
                  };

              const cardId = typeof coreCardId === "function"
                ? coreCardId
                : (typeof window.cardId === "function" ? window.cardId : function (lead) {
                    return "card-" + String(lead?.lead_product_id || lead?.lead_product_list_id || lead?.id || "").replace(/^card-/, "");
                  });


              const safeStr = (v) => (v == null ? "" : String(v));

              const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                  "&": "&amp;",
                  "<": "&lt;",
                  ">": "&gt;",
                  '"': "&quot;",
                  "'": "&#039;"
              })[m]);

              const escapeHTML = typeof escapeHTMLFromCore === "function" ? escapeHTMLFromCore : esc;

              const fmtDE = (v) => {
                try {
                  return v ? new Date(v).toLocaleDateString("de-DE") : "-";
                } catch {
                  return "-";
                }
              };
              /* -------------------------------------------------------------------------- */
              /* Constants                                                                   */
              /* -------------------------------------------------------------------------- */
              window.KB_DND_MIME = window.KB_DND_MIME || "application/x-leadui-cards";

              const interestIcons = {
                interest: { icon: "kaufinteresse.svg", label: "Kaufinteresse" },
                intent: { icon: "kaufabsicht.svg", label: "Kaufabsicht" },
                option: { icon: "kaufoption.svg", label: "Kaufoption" },
              };

              const servicesMap = {
                complete: "Komplett",
                montage: "Montage",
                product: "Produkt",
                plan: "Planung",
                maintenance: "Wartung",
                repair: "Reparatur",
                emergency: "Notdienst",
                others: "Sonstiges",
              };

              /* -------------------------------------------------------------------------- */
              /* Small helpers                                                               */
              /* -------------------------------------------------------------------------- */

              function parseDT(raw) {
                  const s = String(raw || "").trim();
                  if (!s) return null;

                  // MySQL "YYYY-MM-DD HH:MM:SS" -> ISO-like "YYYY-MM-DDTHH:MM:SS"
                  const isoLike = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(s) ? s.replace(" ", "T") : s;

                  const d = new Date(isoLike);
                  if (!Number.isFinite(d.getTime())) return null;
                  return d;
                }

                function fmtDEDate(raw) {
                  const d = parseDT(raw);
                  return d ? d.toLocaleDateString("de-DE") : "-";
                }

                function fmtDEDateTime(raw) {
                  const d = parseDT(raw);
                  return d ? d.toLocaleString("de-DE") : "-";
                }

              const toInt = (v, def = 0) => {
                const n = Number(v);
                return Number.isFinite(n) ? n : def;
              };

              const safeJSON = (raw, fallback) => {
                try {
                  return JSON.parse(raw);
                } catch (_) {
                  return fallback;
                }
              };

              function runIdle(fn) {
                if ("requestIdleCallback" in window) window.requestIdleCallback(fn);
                else window.setTimeout(fn, 0);
              }

              function addPage(qsStr, page) {
                const p = new URLSearchParams(qsStr || "");
                p.set("page", String(page));
                return p.toString();
              }

              function isKanbanActive() {
                return qs("#home")?.classList.contains("active");
              }

              function setTabCount(selector, n) {
                const el = qs(selector);
                if (el) el.textContent = String(toInt(n, 0));
              }

              function normalizePaginationMeta(input) {
                if (!input) return null;
                const direct = input.meta || input.pagination || input;

                const cp = toInt(direct.current_page ?? direct.currentPage ?? direct.page ?? 1, 1);
                const lp = toInt(
                  direct.last_page ??
                    direct.lastPage ??
                    (direct.total && direct.per_page ? Math.ceil(toInt(direct.total, 0) / toInt(direct.per_page, 1)) : 1),
                  1
                );

                return { current_page: Math.max(1, cp), last_page: Math.max(1, lp) };
              }

              /* -------------------------------------------------------------------------- */
              /* Selection (Kanban)                                                         */
              /* -------------------------------------------------------------------------- */
              function selectCard(card, ev) {
                if (!card) return;

                const multi = !!(ev?.ctrlKey || ev?.metaKey);

                if (!multi) {
                  qsa("#kanban .card.selected").forEach((c) => c.classList.remove("selected"));
                  State.selectedIds?.clear?.();
                }

                if (!State.selectedIds) State.selectedIds = new Set();

                if (multi && State.selectedIds.has(card.id)) {
                  card.classList.remove("selected");
                  State.selectedIds.delete(card.id);
                  return;
                }

                card.classList.add("selected");
                State.selectedIds.add(card.id);
              }

              /* -------------------------------------------------------------------------- */
              /* Drag & Drop (Kanban)                                                       */
              /* -------------------------------------------------------------------------- */
              function getDragIds(card) {
                if (!State.selectedIds) State.selectedIds = new Set();
                let ids = Array.from(State.selectedIds);
                if (!ids.length || !State.selectedIds.has(card.id)) ids = [card.id];
                return ids;
              }

              function onKanbanDragStart(ev, card) {
                if (!ev?.dataTransfer || !card) return;
                const ids = getDragIds(card);

                // Use a custom MIME to avoid browser default "open new tab" behavior elsewhere.
                ev.dataTransfer.setData(window.KB_DND_MIME, JSON.stringify(ids));
                ev.dataTransfer.effectAllowed = "move";
              }

              function refreshCardStatus(card, overrides = {}) {
                const s = canonicalStage(overrides.stage || card.dataset.stage || card.closest(".column")?.id || "lead");
                const ws = String(overrides.work_status || card.dataset.runState || "playing").toLowerCase();
                const stamp = overrides.updated_at || card.dataset.updatedAt || card.dataset.doneDate || new Date().toISOString();

                card.dataset.stage = s;

                if (overrides.latest_phase != null) card.dataset.latestPhase = overrides.latest_phase;
                if (overrides.latest_activity != null) card.dataset.latestActivity = overrides.latest_activity;
                if (overrides.updated_at != null) card.dataset.updatedAt = overrides.updated_at;

                const old = card.querySelector(".kb-status");
                if (old) {
                  old.outerHTML = buildStatusBlock({
                    stage: s,
                    work_status: ws,
                    latest_phase: overrides.latest_phase ?? card.dataset.latestPhase ?? "-",
                    latest_activity: overrides.latest_activity ?? card.dataset.latestActivity ?? "-",
                    updated_at: stamp,
                    done_date: stamp,
                  });
                }

                applyRunStateUI(card, ["playing", "paused", "stopped"].includes(ws) ? ws : "playing");
                featherRefreshSoon();
              }

             function moveOrRefreshKanbanCard({ newStage, cardFromDOM }) {
                const card = cardFromDOM;
                if (!card) return;

                if (stageFilterExcludes(newStage)) {
                  card.remove();
                } else {
                  const targetCol = colContent(newStage);
                  if (targetCol && card.parentElement !== targetCol) {
                      targetCol.prepend(card);
                      // Ensure moved/latest card is visible at top when dropped into a new column
                      card.style.display = ''; 
                  }

                  refreshCardStatus(card, { stage: newStage, updated_at: new Date().toISOString() });
                  if (targetCol && card.parentElement === targetCol) {
                    targetCol.prepend(card);
                    card.style.display = "";
                  }

                  card.classList.remove("selected");
                  State.selectedIds?.delete?.(card.id);
                }

                updateCounts();
              }


              window.orderedStageEntries = window.orderedStageEntries || function (namesObj) {
                const names = namesObj || {};
                const meta = window.APP?.kanbanStageMeta || window.APP?.stageMeta || {};

                return Object.entries(names)
                  .filter(([key]) => !["junk", "ticket"].includes(String(key).toLowerCase()))
                  .sort((a, b) => {
                    const ao = Number(meta?.[a[0]]?.sort_order ?? 999999);
                    const bo = Number(meta?.[b[0]]?.sort_order ?? 999999);

                    if (ao !== bo) return ao - bo;

                    return String(a[1] || a[0]).localeCompare(String(b[1] || b[0]), "de");
                  });
              };

              function buildStageTeamHistoryHTML(assignments = [], currentStage = null) {
                const arr = Array.isArray(assignments) ? assignments : [];

                const getOrderedStageEntriesSafe = () => {
                  const names = APP.stageNames || {};
                  const meta = APP.kanbanStageMeta || APP.stageMeta || {};

                  return Object.entries(names)
                    .filter(([key]) => !["junk", "ticket"].includes(canonicalStage(key)))
                    .sort((a, b) => {
                      const ak = canonicalStage(a[0]);
                      const bk = canonicalStage(b[0]);

                      const ao = Number(meta?.[ak]?.sort_order ?? meta?.[a[0]]?.sort_order ?? 999999);
                      const bo = Number(meta?.[bk]?.sort_order ?? meta?.[b[0]]?.sort_order ?? 999999);

                      if (ao !== bo) return ao - bo;

                      return String(a[1] || ak).localeCompare(String(b[1] || bk), "de");
                    });
                };

                const orderedStages = getOrderedStageEntriesSafe();
                const currentKey = currentStage ? canonicalStage(currentStage) : null;

                const currentIdx = currentKey
                  ? orderedStages.findIndex(([key]) => canonicalStage(key) === currentKey)
                  : -1;

                const visibleStages = currentIdx >= 0
                  ? orderedStages.slice(0, currentIdx + 1)
                  : orderedStages;

                const byStage = new Map();

                arr.forEach((a) => {
                  const st = canonicalStage(a?.stage || currentStage || "lead");

                  if (!byStage.has(st)) {
                    byStage.set(st, []);
                  }

                  byStage.get(st).push(a);
                });

                return `
                  <div class="mb-3">
                    <label class="small text-muted font-weight-bold text-uppercase d-block mb-2">
                      Bisherige Teams je Phase
                    </label>

                    <div class="swal-stage-team-grid">
                      ${visibleStages.map(([stageKey, stageLabel]) => {
                        const stage = canonicalStage(stageKey);
                        const members = byStage.get(stage) || [];
                        const isCurrent = currentKey && stage === currentKey;

                        return `
                          <div class="swal-stage-team-row ${isCurrent ? "is-current-stage" : ""}">
                            <div class="swal-stage-team-title">
                              ${esc(stageLabel || APP.stageNames?.[stage] || stage)}
                            </div>

                            <div>
                              ${
                                members.length
                                  ? members.map((x) => {
                                      const emp = x?.member || {};
                                      const name = `${emp?.lastname || ""} ${emp?.name || ""}`.trim()
                                        || `Mitarbeiter #${x?.employee_id || ""}`;

                                      const u = x?.assigned_by_user || {};
                                      const by = `${u?.lastname || ""} ${u?.name || ""}`.trim()
                                        || (x?.assigned_by ? `Mitarbeiter #${x.assigned_by}` : "-");

                                      const at = x?.assigned_at ? fmtDEDateTime(x.assigned_at) : "-";

                                      return `
                                        <div class="swal-stage-team-member">
                                          <strong>${esc(name)}</strong><br>
                                          <span class="text-muted">
                                            von ${esc(by)} • ${esc(at)}
                                          </span>
                                        </div>
                                      `;
                                    }).join("")
                                  : `<div class="swal-stage-team-empty">Kein Team gespeichert</div>`
                              }
                            </div>
                          </div>
                        `;
                      }).join("")}
                    </div>
                  </div>
                `;
              }

              /* -------------------------------------------------------------------------- */
              /* Stage-change confirm (SweetAlert + Select2 team + reason)                   */
              /* -------------------------------------------------------------------------- */
              async function loadProductStagesForModal(productId) {
                const pid = toInt(productId || 0);
                if (!pid) return { names: {}, meta: {}, stages: [] };

                const currentPid = toInt(APP.stageWorkflow?.productId || 0);
                const hasCurrent = currentPid === pid && APP.stageWorkflow?.productStages?.length;
                if (hasCurrent) {
                  return {
                    names: APP.stageWorkflow.productStageNames || {},
                    meta: APP.stageWorkflow.productStageMeta || {},
                    stages: APP.stageWorkflow.productStages || [],
                  };
                }

                try {
                  const res = await safeFetchJSON(`${APP.endpoints.stageWorkflowConfig}?mode=product&product_id=${encodeURIComponent(pid)}`);
                  if (!res?.success) return { names: {}, meta: {}, stages: [] };

                  const names = {};
                  const meta = {};
                  (res.stages || []).forEach((stage, idx) => {
                    const key = `product_stage_${stage.id}`;
                    names[key] = stage.name || `Produktphase #${stage.id}`;
                    meta[key] = {
                      id: stage.id,
                      key,
                      color: stage.color || "#93c21c",
                      icon: stage.icon || "layers",
                      sort_order: Number(stage.sort_order ?? ((idx + 1) * 10)),
                      phases: Array.isArray(stage.phases) ? stage.phases : [],
                      product_id: stage.product_id,
                      section_name: stage.section_name || "",
                    };
                  });

                  return { names, meta, stages: res.stages || [] };
                } catch (e) {
                  console.warn("Product stages could not be loaded for modal", e);
                  return { names: {}, meta: {}, stages: [] };
                }
              }

              function buildReadonlyTargetBox({ title, sub, icon = "arrow-right" }) {
                return `
                  <div class="swal-enterprise-target">
                    <div class="swal-enterprise-target-icon"><i class="feather icon-${escapeHTML(icon)}"></i></div>
                    <div>
                      <div class="swal-enterprise-target-title">${escapeHTML(title || "Zielphase")}</div>
                      <div class="swal-enterprise-target-sub">${escapeHTML(sub || "Die Ablage-Spalte entscheidet den Status.")}</div>
                    </div>
                  </div>`;
              }

              function buildProductStageSelectBox(productWorkflow, selectedKey = null, allowForward = true) {
                const names = productWorkflow?.names || {};
                const meta = productWorkflow?.meta || {};
                const entries = orderedStageEntries(names);
                const currentKey = selectedKey || entries?.[0]?.[0] || "";

                const productOptions = entries.map(([key, label]) => {
                  const id = workflowStageIdFromKey(key);
                  const icon = meta?.[key]?.icon || "layers";
                  const section = meta?.[key]?.section_name || "";
                  const selected = String(key) === String(currentKey) ? "selected" : "";
                  return `<option value="${id}" data-key="${escapeHTML(key)}" data-icon="${escapeHTML(icon)}" data-section="${escapeHTML(section)}" ${selected}>${escapeHTML(label)}</option>`;
                }).join("");

                const currentMeta = currentKey ? (meta?.[currentKey] || {}) : {};
                const taskOptions = [`<option value="">Keine Unterphase</option>`]
                  .concat((currentMeta.phases || []).map((phase) => `<option value="${phase.id}">${escapeHTML(phase.name || phase.phase_name || ('Phase #' + phase.id))}</option>`))
                  .join("");

                return `
                  <div class="swal-product-info-box">
                    <label class="small text-muted font-weight-bold text-uppercase">Produktstatus / Produktphase</label>
                    <div class="small text-muted mb-2">Optional: Produktfortschritt direkt mitführen, ohne die Unternehmensspalte zu wechseln.</div>
                    <div class="swal-product-info-grid">
                      <div class="form-group mb-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Produktphase</label>
                        <select id="swal-product-stage" class="form-control" style="width:100%;">${productOptions || '<option value="">Keine Produktphasen</option>'}</select>
                      </div>
                      <div class="form-group mb-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Unterphase</label>
                        <select id="swal-product-task-phase" class="form-control" style="width:100%;">${taskOptions}</select>
                      </div>
                    </div>
                    ${allowForward ? `<button type="button" id="swal-move-forward" class="swal-workflow-forward"><i class="feather icon-arrow-right"></i> Eine Produktphase weiter</button>` : ``}
                  </div>`;
              }

              function productStageInfoHTMLFromDataset(data = {}) {
                const stageName = data.productStageName || data.product_stage_name || "Noch keine Produktphase";
                const phaseName = data.productTaskPhaseName || data.product_task_phase_name || "Keine Unterphase";
                const productName = data.productName || data.initial || data.product || "Produkt";
                const mode = APP.stageWorkflow?.mode === "product" ? "Produkt-Workflow" : "Unternehmen-Workflow";
                return `
                  <div class="product-stage-info-card">
                    <div class="product-stage-info-row">
                      <i class="feather icon-box"></i>
                      <div><strong>Produkt</strong><br><span class="text-muted">${escapeHTML(productName)}</span></div>
                    </div>
                    <div class="product-stage-info-row">
                      <i class="feather icon-layers"></i>
                      <div><strong>Aktuelle Produktphase</strong><br><span class="text-muted">${escapeHTML(stageName)}</span></div>
                    </div>
                    <div class="product-stage-info-row">
                      <i class="feather icon-list"></i>
                      <div><strong>Unterphase</strong><br><span class="text-muted">${escapeHTML(phaseName)}</span></div>
                    </div>
                    <div class="product-stage-info-row">
                      <i class="feather icon-briefcase"></i>
                      <div><strong>Ansicht</strong><br><span class="text-muted">${escapeHTML(mode)}</span></div>
                    </div>
                  </div>`;
              }


              window.escapeHTML = window.escapeHTML || function (value) {
                  return String(value ?? '')
                      .replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
              };

              window.featherRefreshSoon = window.featherRefreshSoon || function () {
                  setTimeout(function () {
                      if (window.feather && typeof window.feather.replace === 'function') {
                          window.feather.replace();
                      }
                  }, 30);
              };

              window.productStageInfoHTMLFromDataset = function (data = {}) {
                  const stageName =
                      data.productStageName ||
                      data.product_stage_name ||
                      data.productStage ||
                      'Noch keine Produktphase';

                  const phaseName =
                      data.productTaskPhaseName ||
                      data.product_task_phase_name ||
                      data.productTaskPhase ||
                      'Keine Unterphase';

                  const productName =
                      data.productName ||
                      data.initial ||
                      data.product ||
                      'Produkt';

                  const companyStage =
                      data.companyStage ||
                      data.stage ||
                      'Unternehmen';

                  const mode =
                      window.APP?.stageWorkflow?.mode === 'product'
                          ? 'Produkt-Workflow'
                          : 'Unternehmen-Workflow';

                  return `
                      <div class="product-stage-info-card" style="text-align:left;">
                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-box"></i>
                              <div>
                                  <strong>Produkt</strong><br>
                                  <span class="text-muted">${window.escapeHTML(productName)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-layers"></i>
                              <div>
                                  <strong>Aktuelle Produktphase</strong><br>
                                  <span class="text-muted">${window.escapeHTML(stageName)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-list"></i>
                              <div>
                                  <strong>Unterphase</strong><br>
                                  <span class="text-muted">${window.escapeHTML(phaseName)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-briefcase"></i>
                              <div>
                                  <strong>Unternehmensphase</strong><br>
                                  <span class="text-muted">${window.escapeHTML(companyStage)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;">
                              <i class="feather icon-eye"></i>
                              <div>
                                  <strong>Ansicht</strong><br>
                                  <span class="text-muted">${window.escapeHTML(mode)}</span>
                              </div>
                          </div>
                      </div>
                  `;
              };

              window.showProductStageInfoFromElement = function (el) {
                  const d = el?.dataset || {};

                  if (!window.Swal) {
                      alert(
                          'Produktstatus:\n\n' +
                          'Produkt: ' + (d.productName || d.initial || d.product || 'Produkt') + '\n' +
                          'Produktphase: ' + (d.productStageName || d.product_stage_name || 'Noch keine Produktphase') + '\n' +
                          'Unterphase: ' + (d.productTaskPhaseName || d.product_task_phase_name || 'Keine Unterphase')
                      );
                      return;
                  }

                  Swal.fire({
                      title: 'Produktstatus',
                      html: window.productStageInfoHTMLFromDataset(d),
                      width: 520,
                      confirmButtonText: 'Schließen',
                      customClass: {
                          popup: 'swal-product-stage-info-popup'
                      },
                      didOpen: function () {
                          window.featherRefreshSoon();
                      }
                  });
              };

              function showProductStageInfoFromElement(el) {
                const d = el?.dataset || {};
                Swal.fire({
                  title: "Produktstatus",
                  html: productStageInfoHTMLFromDataset(d),
                  width: 520,
                  confirmButtonText: "Schließen",
                  didOpen: () => featherRefreshSoon(),
                });
              }

              /* -------------------------------------------------------------------------- */
              /* Stage-change confirm (Enterprise Workflow)                                 */
              /* -------------------------------------------------------------------------- */


              function tomorrowDateValue() {
                const d = new Date();
                d.setDate(d.getDate() + 1);
                return d.toISOString().slice(0, 10);
              }

              function buildStageReminderBox(opts = {}) {
                const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];
                const selectedEmployeeId = toInt(opts.employeeId || 0);
                const employeeOptions = [`<option value="">Automatisch / Keine</option>`].concat(employees.map((emp) => {
                  const id = toInt(emp.id);
                  const selected = selectedEmployeeId && selectedEmployeeId === id ? 'selected' : '';
                  const text = `${emp.lastname || ""} ${emp.name || ""}`.trim() || `Mitarbeiter #${id}`;
                  return `<option value="${id}" ${selected}>${escapeHTML(text)}</option>`;
                })).join("");

                const title = opts.title || 'Nächster Schritt';
                const description = opts.description || '';
                return `
                  <div class="swal-reminder-toggle-box">
                    <div class="swal-reminder-toggle-head">
                      <div class="swal-reminder-toggle-title"><i class="feather icon-bell"></i> Nächster Schritt / Erinnerung</div>
                      <label class="swal-reminder-switch">
                        <input type="checkbox" id="swal-create-reminder">
                        <span class="swal-reminder-slider"></span>
                        <span>Erstellen</span>
                      </label>
                    </div>

                    <div id="swal-reminder-fields" class="swal-reminder-fields">
                      <div class="swal-reminder-grid">
                        <div class="swal-reminder-field swal-reminder-field-full">
                          <label>Titel *</label>
                          <input type="text" id="swal-reminder-title" value="${escapeHTML(title)}" placeholder="z. B. Kunde morgen anrufen">
                        </div>
                        <div class="swal-reminder-field swal-reminder-field-full">
                          <label>Beschreibung</label>
                          <textarea id="swal-reminder-description" placeholder="Was ist der nächste Schritt?">${escapeHTML(description)}</textarea>
                        </div>
                      </div>
                      <div class="swal-reminder-grid-3 mt-2">
                        <div class="swal-reminder-field">
                          <label>Datum *</label>
                          <input type="date" id="swal-reminder-date" value="${tomorrowDateValue()}">
                        </div>
                        <div class="swal-reminder-field">
                          <label>Uhrzeit</label>
                          <input type="time" id="swal-reminder-time" value="09:00">
                        </div>
                        <div class="swal-reminder-field">
                          <label>Priorität</label>
                          <select id="swal-reminder-priority">
                            <option value="normal">Normal</option>
                            <option value="important" selected>Wichtig</option>
                            <option value="critical">Kritisch</option>
                          </select>
                        </div>
                      </div>
                      <div class="swal-reminder-grid mt-2">
                        <div class="swal-reminder-field">
                          <label>Verantwortlich</label>
                          <select id="swal-reminder-employee" style="width:100%;">${employeeOptions}</select>
                        </div>
                        <div class="swal-reminder-field">
                          <label>Abteilung</label>
                          <input type="number" id="swal-reminder-department" value="${escapeHTML(opts.departmentId || '')}" placeholder="Optional">
                        </div>
                      </div>
                    </div>
                  </div>`;
              }

              async function createReminderFromStageChange(context = {}, reminder = null) {
                if (!reminder || !reminder.enabled) return null;
                const leadProductId = toInt(context.leadProductId || context.lead_product_list_id || 0);
                if (!leadProductId) throw new Error('LeadProduct-ID fehlt für die Erinnerung.');

                const payload = {
                  lead_product_list_id: leadProductId,
                  title: reminder.title || 'Nächster Schritt',
                  description: reminder.description || '',
                  reminder_date: reminder.reminder_date,
                  reminder_time: reminder.reminder_time || null,
                  priority: reminder.priority || 'normal',
                  department_id: reminder.department_id || null,
                  responsible_employee_id: reminder.responsible_employee_id || null,
                };

                const url = APP.endpoints.remindersStore || "/kanban/reminders";
                const data = await postJSON(url, payload);
                if (!data?.status && !data?.success) throw new Error(data?.message || 'Erinnerung konnte nicht gespeichert werden.');
                return data;
              }

              async function confirmStageChange(newStage, currentStage, currentTeamIds = [], opts = {}) {
                const workflowMode = APP.stageWorkflow?.mode === "product" ? "product" : "company";
                const isProductWorkflow = workflowMode === "product";
                const labelNew = workflowLabel(newStage);
                const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];
                const teamSet = new Set((currentTeamIds || []).map((x) => toInt(x)));
                const modalProductId = toInt(opts.productId || APP.stageWorkflow?.productId || 0);
                const productWorkflow = await loadProductStagesForModal(modalProductId);

                const removedIds = (opts.removedTeamIds || []).map((x) => toInt(x)).filter(Boolean);
                const removedListHTML = removedIds.length
                  ? `<div class="mb-3 p-2" style="border:1px solid #f1c40f;background:#fff8e1;border-radius:8px;">
                      <div class="font-weight-bold mb-1">Achtung: Rückwärtswechsel</div>
                      <div class="small text-muted mb-2">Folgende Mitarbeiter werden in der vorherigen Phase nicht übernommen:</div>
                      <ul class="mb-0" style="padding-left:18px;">
                        ${removedIds.map((id) => {
                          const emp = employees.find((e) => toInt(e.id) === id);
                          const name = emp ? `${emp.lastname || ""} ${emp.name || ""}`.trim() : `#${id}`;
                          return `<li>${escapeHTML(name)}</li>`;
                        }).join("")}
                      </ul>
                    </div>`
                  : "";

                const teamOptions = employees.map((emp) => {
                  const id = toInt(emp.id);
                  const selected = teamSet.has(id) ? "selected" : "";
                  const imgUrl = emp.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
                  const text = `${emp.lastname || ""} ${emp.name || ""}`.trim();
                  return `<option value="${id}" data-image="${escapeHTML(imgUrl)}" ${selected}>${escapeHTML(text)}</option>`;
                }).join("");

                const productTargetKey = isProductWorkflow
                  ? (String(newStage || "").startsWith("product_stage_") ? newStage : `product_stage_${workflowStageIdFromKey(newStage) || ""}`)
                  : (opts.productStageId ? `product_stage_${opts.productStageId}` : (opts.currentProductStageId ? `product_stage_${opts.currentProductStageId}` : Object.keys(productWorkflow.names || {})[0]));

                const workflowContent = isProductWorkflow
                  ? buildReadonlyTargetBox({
                      title: `Produktphase: ${labelNew}`,
                      sub: "Produkt-Workflow: Beim Verschieben werden nur Produktstatus, Team und Grund gespeichert.",
                      icon: "layers",
                    })
                  : buildReadonlyTargetBox({
                      title: `Unternehmensphase: ${labelNew}`,
                      sub: "Die Ablage-Spalte setzt den Hauptstatus. Unterphasen werden über den Under-Stage-Board verwaltet.",
                      icon: "briefcase",
                    });

                const htmlContent = `
                  <div style="text-align:left; overflow:visible;">
                    ${removedListHTML}
                    ${buildStageTeamHistoryHTML(opts.stageTeams || [], currentStage)}
                    ${workflowContent}
                    <div class="mb-3">
                      <label class="small text-muted font-weight-bold text-uppercase">Team zuweisen</label>
                      <select id="swal-team-select" class="form-control" multiple style="width:100%;">${teamOptions}</select>
                    </div>
                    <div class="mb-1">
                      <label class="small text-muted font-weight-bold text-uppercase">Grund / Notiz</label>
                      <textarea id="swal-reason-text" class="form-control" rows="3" placeholder="Optional: Grund für den Wechsel..."></textarea>
                    </div>
                    ${buildStageReminderBox({
                      title: `Nächster Schritt nach Wechsel zu ${labelNew}`,
                      description: `Bitte nächsten Schritt für Phase ${labelNew} prüfen.`,
                      employeeId: currentTeamIds?.[0] || opts.employeeId || '',
                      departmentId: opts.departmentId || ''
                    })}
                  </div>`;

                const formatEmployee = (state) => {
                  if (!state?.id) return state?.text || "";
                  const img = state.element?.dataset?.image;
                  if (!img) return state.text;
                  const wrap = document.createElement("span");
                  wrap.className = "employee-option";
                  wrap.innerHTML = `<img src="${img}" style="width:20px;height:20px;border-radius:999px;object-fit:cover;margin-right:8px;">${state.text}`;
                  return wrap;
                };

                const formatProductStage = (state) => {
                  if (!state?.id) return state?.text || "";
                  const icon = state.element?.dataset?.icon || "layers";
                  const section = state.element?.dataset?.section || "";
                  const wrap = document.createElement("span");
                  wrap.className = "kb-workflow-select2-option";
                  wrap.innerHTML = `<span class="kb-workflow-select2-icon"><i class="feather icon-${escapeHTML(icon)}"></i></span><span class="kb-workflow-select2-text"><span class="kb-workflow-select2-title">${escapeHTML(state.text || "")}</span><span class="kb-workflow-select2-sub">${escapeHTML(section || "Produktphase")}</span></span>`;
                  return wrap;
                };

                const result = await Swal.fire({
                  title: `Wechsel zu ${labelNew}`,
                  html: htmlContent,
                  showCancelButton: true,
                  confirmButtonText: "Speichern",
                  cancelButtonText: "Abbrechen",
                  width: isProductWorkflow ? 700 : 860,
                  customClass: { popup: "swal-overflow-visible" },
                  didOpen: () => {
                    const popup = Swal.getPopup();

                    const refreshProductTaskOptions = () => {
                      const sel = qs("#swal-product-stage", popup);
                      const taskSel = qs("#swal-product-task-phase", popup);
                      if (!sel || !taskSel) return;
                      const key = sel.selectedOptions?.[0]?.dataset?.key || `product_stage_${sel.value || ''}`;
                      const meta = productWorkflow?.meta?.[key] || {};
                      taskSel.innerHTML = `<option value="">Keine Unterphase</option>` + (meta.phases || []).map((phase) => `<option value="${phase.id}">${escapeHTML(phase.name || phase.phase_name || ('Phase #' + phase.id))}</option>`).join("");
                      if (window.jQuery && window.jQuery.fn.select2) window.jQuery(taskSel).trigger("change.select2");
                    };

                    const reminderToggle = qs("#swal-create-reminder", popup);
                    const reminderFields = qs("#swal-reminder-fields", popup);
                    reminderToggle?.addEventListener("change", () => {
                      reminderFields?.classList.toggle("is-open", reminderToggle.checked);
                    });

                    qs("#swal-product-stage", popup)?.addEventListener("change", refreshProductTaskOptions);
                    qs("#swal-move-forward", popup)?.addEventListener("click", () => {
                      const sel = qs("#swal-product-stage", popup);
                      if (!sel) return;
                      const idx = sel.selectedIndex;
                      if (idx < sel.options.length - 1) {
                        sel.selectedIndex = idx + 1;
                        sel.dispatchEvent(new Event("change"));
                        if (window.jQuery && window.jQuery.fn.select2) window.jQuery(sel).trigger("change.select2");
                      }
                    });

                    if (window.jQuery && window.jQuery.fn.select2) {
                      const selectors = isProductWorkflow
                        ? ["#swal-team-select", "#swal-reminder-employee"]
                        : ["#swal-team-select", "#swal-reminder-employee"];

                      selectors.forEach((selector) => {
                        const $sel = window.jQuery(selector);
                        if (!$sel.length) return;
                        $sel.select2({
                          dropdownParent: window.jQuery(popup),
                          width: "100%",
                          closeOnSelect: selector !== "#swal-team-select",
                          templateResult: selector === "#swal-team-select" ? formatEmployee : (selector === "#swal-product-stage" ? formatProductStage : undefined),
                          templateSelection: selector === "#swal-team-select" ? formatEmployee : (selector === "#swal-product-stage" ? formatProductStage : undefined),
                          escapeMarkup: (m) => m,
                        });
                      });
                    }

                    refreshProductTaskOptions();
                    featherRefreshSoon();
                  },
                  preConfirm: () => {
                    let teams = currentTeamIds.slice();
                    if (window.jQuery) {
                      const v = window.jQuery("#swal-team-select").val();
                      if (Array.isArray(v)) teams = v.map((x) => toInt(x)).filter(Boolean);
                    }

                    const selectedProductStageId = isProductWorkflow
                      ? (workflowStageIdFromKey(newStage) || toInt(opts.productStageId || 0) || null)
                      : null;

                    const createReminder = !!qs("#swal-create-reminder")?.checked;
                    const reminderTitle = (qs("#swal-reminder-title")?.value || "").trim();
                    const reminderDate = (qs("#swal-reminder-date")?.value || "").trim();
                    if (createReminder && (!reminderTitle || !reminderDate)) {
                      Swal.showValidationMessage("Bitte Titel und Datum für die Erinnerung ausfüllen.");
                      return false;
                    }

                    return {
                      mode: workflowMode,
                      reason: qs("#swal-reason-text")?.value || "",
                      teams,
                      companyStageKey: isProductWorkflow ? null : newStage,
                      productStageId: selectedProductStageId,
                      productTaskPhaseId: null,
                      reminder: createReminder ? {
                        enabled: true,
                        title: reminderTitle,
                        description: qs("#swal-reminder-description")?.value || "",
                        reminder_date: reminderDate,
                        reminder_time: qs("#swal-reminder-time")?.value || null,
                        priority: qs("#swal-reminder-priority")?.value || "normal",
                        responsible_employee_id: toInt(qs("#swal-reminder-employee")?.value || 0) || null,
                        department_id: toInt(qs("#swal-reminder-department")?.value || 0) || null,
                      } : null,
                    };
                  },
                });

                if (!result.isConfirmed) return { ok: false };
                return {
                  ok: true,
                  mode: workflowMode,
                  reasonHTML: result.value?.reason || "",
                  teams: Array.isArray(result.value?.teams) ? result.value.teams : [],
                  companyStageKey: isProductWorkflow ? null : newStage,
                  productStageId: result.value?.productStageId || (isProductWorkflow ? workflowStageIdFromKey(newStage) : null),
                  productTaskPhaseId: result.value?.productTaskPhaseId || null,
                  reminder: result.value?.reminder || null,
                };
              }


              function defaultLeadSubStageForStage(stageKey) {
                const key = canonicalStage(stageKey || "");
                const meta = APP.stageMeta?.[key] || APP.kanbanStageMeta?.[key] || APP.companyKanbanStageMeta?.[key] || {};
                const subs = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
                if (!subs.length) return null;
                const def = subs.find(s => s && (s.is_default === true || s.is_default === 1 || String(s.is_default) === "1"));
                return (def || subs[0])?.id || null;
              }

              function subStageMetaForStage(stageKey, subStageId) {
                const key = canonicalStage(stageKey || "");
                const meta = APP.stageMeta?.[key] || APP.kanbanStageMeta?.[key] || APP.companyKanbanStageMeta?.[key] || {};
                const subs = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
                return subs.find(s => String(s.id) === String(subStageId || "")) || null;
              }


              function formatOfferFolderPrice(value) {
                const n = Number(value || 0);
                if (!Number.isFinite(n) || n <= 0) return '';
                try {
                  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(n);
                } catch (e) {
                  return `${n.toFixed(2)} €`;
                }
              }

              async function askAcceptedOfferFolderSelection(payload) {
                const folders = Array.isArray(payload?.folders) ? payload.folders : [];

                if (!folders.length) {
                  if (!window.Swal) {
                    return null;
                  }

                  const result = await Swal.fire({
                    icon: 'warning',
                    title: payload?.title || 'Kein Angebot gefunden',
                    html: `
                      <div style="text-align:left">
                        <div style="border:1px solid #fde68a;background:#fffbeb;color:#92400e;border-radius:14px;padding:12px;margin-bottom:12px;line-height:1.55;font-size:13px;">
                          <div style="font-weight:900;margin-bottom:4px;">Kein aktiver Angebotsordner gefunden</div>
                          <div>${escapeHTML(payload?.message || 'Dieser Vorgang kann noch nicht in die Phase Auftrag verschoben werden, weil kein aktiver Angebotsordner gefunden wurde.')}</div>
                          ${payload?.help_text ? `<div style="margin-top:8px;color:#78350f;">${escapeHTML(payload.help_text)}</div>` : ''}
                        </div>

                        <div style="border:1px solid #fecaca;background:#fef2f2;color:#7f1d1d;border-radius:14px;padding:12px;line-height:1.55;font-size:13px;">
                          <div style="font-weight:900;margin-bottom:4px;">Trotzdem verschieben?</div>
                          <div>
                            Wenn du fortfährst, wird <strong>kein Angebot zum Auftrag umgewandelt</strong>.
                            Die Kanban-Karte wird nur in die neue Phase verschoben und dieser Übersprung wird in der Historie gespeichert.
                          </div>
                        </div>
                      </div>
                    `,
                    width: 620,
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Trotzdem verschieben',
                    denyButtonText: 'Angebot erstellen',
                    cancelButtonText: 'Abbrechen',
                    confirmButtonColor: '#dc2626',
                    denyButtonColor: '#93c21c',
                    focusCancel: true,
                  });

                  if (result.isConfirmed) {
                    return { skipWithoutFolder: true };
                  }

                  if (result.isDenied) {
                    const openUrl = payload?.create_offer_url || payload?.offer_url || payload?.url || null;
                    if (openUrl) {
                      window.open(openUrl, '_blank', 'noopener');
                    }
                  }

                  return null;
                }

                if (!window.Swal) {
                  return null;
                }

                const steps = Array.isArray(payload?.next_steps) ? payload.next_steps : [];
                const html = `
                  <div style="text-align:left">
                    <div style="border:1px solid #fde68a;background:#fffbeb;color:#92400e;border-radius:14px;padding:12px;margin-bottom:12px;line-height:1.55;font-size:13px;">
                      <div style="font-weight:900;margin-bottom:4px;">Warum wird der Kanban-Umzug gestoppt?</div>
                      <div>${escapeHTML(payload?.message || 'Bitte wählen Sie das angenommene Angebot aus.')}</div>
                      ${payload?.help_text ? `<div style="margin-top:8px;color:#78350f;">${escapeHTML(payload.help_text)}</div>` : ''}
                    </div>
                    ${steps.length ? `<div style="border:1px solid #e5e7eb;background:#f8fafc;border-radius:14px;padding:12px;margin-bottom:12px;font-size:12px;color:#374151;line-height:1.55;">
                      <div style="font-weight:900;margin-bottom:6px;">Was passiert danach?</div>
                      <ol style="margin:0;padding-left:18px;">${steps.map(step => `<li>${escapeHTML(step)}</li>`).join('')}</ol>
                    </div>` : ''}
                    <div style="font-size:12px;font-weight:900;color:#111827;margin-bottom:8px;">Verfügbare Angebotsordner</div>
                    <div style="display:flex;flex-direction:column;gap:10px;max-height:420px;overflow:auto;">
                      ${folders.map((folder, index) => {
                        const checked = folder.is_accepted || index === 0 ? 'checked' : '';
                        const price = formatOfferFolderPrice(folder.total_gross);
                        const doc = folder.document_status === 'deal' ? 'Auftrag' : 'Angebot';
                        const statusText = `${folder.status || '-'} · Angebot: ${folder.offer_status || '-'} · Auftrag: ${folder.deal_status || '-'}`;
                        return `
                          <label style="display:flex;gap:12px;align-items:flex-start;border:1px solid #e5e7eb;border-radius:14px;padding:12px;background:#fff;cursor:pointer;">
                            <input type="radio" name="accepted_offer_folder_id" value="${escapeHTML(folder.id)}" ${checked} style="margin-top:4px;accent-color:#93c21c;">
                            <span style="display:block;min-width:0;flex:1;">
                              <span style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                <strong style="font-size:14px;color:#111827;">${escapeHTML(folder.name || ('Ordner #' + folder.id))}</strong>
                                <span style="font-size:10px;font-weight:900;border:1px solid #d9ef9d;background:#f4fae7;color:#55720d;border-radius:999px;padding:4px 8px;white-space:nowrap;">${escapeHTML(doc)}</span>
                              </span>
                              <span style="display:block;margin-top:6px;color:#6b7280;font-size:12px;line-height:1.45;">
                                Angebot #${escapeHTML(folder.offer_id || '-')} · Ordner #${escapeHTML(folder.id || '-')} · ${escapeHTML(statusText)}
                                ${price ? ` · <strong>${escapeHTML(price)}</strong>` : ''}
                              </span>
                              <span style="display:block;margin-top:6px;color:#92400e;font-size:11px;line-height:1.4;">
                                Wenn Sie diesen Ordner auswählen, wird er Auftrag. Die anderen aktiven Ordner werden automatisch storniert.
                              </span>
                            </span>
                          </label>`;
                      }).join('')}
                    </div>
                  </div>`;

                const result = await Swal.fire({
                  icon: 'warning',
                  title: payload?.title || 'Welches Angebot wurde angenommen?',
                  html,
                  width: 760,
                  showCancelButton: true,
                  confirmButtonText: 'Dieses Angebot annehmen',
                  cancelButtonText: 'Abbrechen',
                  focusConfirm: false,
                  preConfirm: () => {
                    const selected = document.querySelector('input[name="accepted_offer_folder_id"]:checked');
                    if (!selected) {
                      Swal.showValidationMessage('Bitte ein Angebot auswählen.');
                      return false;
                    }
                    return Number(selected.value || 0);
                  },
                });

                return result.isConfirmed ? Number(result.value || 0) : null;
              }

              async function applyStageChange({
                customerId,
                alternativeId,
                productId,
                leadProductId,
                newStage,
                noteHTML,
                teams = [],
                mode = null,
                companyStageKey = null,
                productStageId = null,
                productTaskPhaseId = null,
                leadStageSubStageId = undefined,
              }) {
                const workflowMode = mode || APP.stageWorkflow?.mode || "company";
                const cleanTeams = Array.isArray(teams) ? teams.map((x) => toInt(x)).filter(Boolean) : [];

                if (workflowMode === "product") {
                  if (!leadProductId) throw new Error("LeadProduct-ID fehlt für Produkt-Workflow.");
                  const payload = {
                    mode: "product",
                    product_stage_id: productStageId || workflowStageIdFromKey(newStage),
                    product_task_phase_id: productTaskPhaseId || null,
                    reason: noteHTML || "",
                    teams: cleanTeams,
                  };
                  const data = await postJSON(APP.endpoints.stageWorkflowMove(toInt(leadProductId)), payload);
                  if (!data?.success) throw new Error(data?.message || "Fehler");
                  return data;
                }

                if (APP.endpoints.stageWorkflowMove && leadProductId) {
                  const resolvedCompanyStage = companyStageKey || newStage;
                  const resolvedSubStageId = leadStageSubStageId === undefined
                    ? defaultLeadSubStageForStage(resolvedCompanyStage)
                    : (leadStageSubStageId || null);

                  const payload = {
                    mode: "company",
                    company_stage_key: resolvedCompanyStage,
                    lead_stage_sub_stage_id: resolvedSubStageId,
                    reason: noteHTML || "",
                    teams: cleanTeams,
                  };
                  let data = await postJSON(APP.endpoints.stageWorkflowMove(toInt(leadProductId)), payload);

                  if (data?.requires_offer_selection) {
                    const offerDecision = await askAcceptedOfferFolderSelection(data);
                    if (!offerDecision) {
                      const cancelError = new Error('Aktion abgebrochen.');
                      cancelError.cancelled = true;
                      throw cancelError;
                    }

                    const retryPayload = { ...payload };

                    if (typeof offerDecision === 'object' && offerDecision.skipWithoutFolder) {
                      retryPayload.skip_offer_gate_without_folder = true;
                    } else {
                      retryPayload.accepted_offer_folder_id = Number(offerDecision || 0);
                    }

                    data = await postJSON(APP.endpoints.stageWorkflowMove(toInt(leadProductId)), retryPayload);
                  }

                  if (data?.success) return data;
                  throw new Error(data?.message || 'Fehler');
                }

                const url = `${APP.endpoints.changeStage}/${encodeURIComponent(customerId)}/${encodeURIComponent(alternativeId)}/${encodeURIComponent(productId)}`;
                const payload = {
                  stage: companyStageKey || newStage,
                  description: noteHTML || "",
                  lead_product_id: toInt(leadProductId) || undefined,
                  teams: cleanTeams,
                };
                const data = await postJSON(url, payload);
                if (!data?.success) throw new Error(data?.message || "Fehler");
                return data;
              }

              /* -------------------------------------------------------------------------- */
              /* Collapsed recent stage changes + realtime card highlight                    */
              /* -------------------------------------------------------------------------- */
              const KB_RECENT_MOVES_KEY = "leadOverview.recentStageMoves.v2";
              const KB_RECENT_OPEN_KEY = "leadOverview.recentStageMoves.open";
              const KB_RECENT_MOVES_MAX = 8;

              function kbStageColor(stage) {
                const raw = String(stage || "").trim();
                const key = canonicalStage(raw);
                const meta =
                  APP.kanbanStageMeta?.[raw] ||
                  APP.kanbanStageMeta?.[key] ||
                  APP.stageMeta?.[raw] ||
                  APP.stageMeta?.[key] ||
                  {};

                if (meta.color) return meta.color;

                const fallback = {
                  lead: "#74b2d4",
                  offer: "#f59e0b",
                  follow_up: "#8b5cf6",
                  accepted: "#22c55e",
                  deal: "#93c21c",
                  project: "#6366f1",
                  completed: "#16a34a",
                  archive: "#64748b",
                  junk: "#dc2626",
                };

                return fallback[key] || "#93c21c";
              }

              function kbStageClass(stage) {
                const key = canonicalStage(stage || "lead");
                return "is-stage-" + String(key || "lead").replace(/[^a-z0-9_-]/gi, "-");
              }

              function kbStageLabel(stage) {
                const raw = String(stage || "").trim();
                if (!raw) return "Unbekannt";

                const key = canonicalStage(raw);

                try {
                  if (typeof workflowLabel === "function") {
                    const label = workflowLabel(raw);
                    if (label && label !== raw) return label;
                  }
                } catch {}

                return (
                  APP.kanbanStageNames?.[raw] ||
                  APP.kanbanStageNames?.[key] ||
                  APP.stageNames?.[raw] ||
                  APP.stageNames?.[key] ||
                  raw.replace(/^product_stage_/, "Produktphase #")
                );
              }

              function kbRecentLoad() {
                try {
                  const raw = sessionStorage.getItem(KB_RECENT_MOVES_KEY);
                  const parsed = raw ? JSON.parse(raw) : [];
                  return Array.isArray(parsed) ? parsed : [];
                } catch {
                  return [];
                }
              }

              function kbRecentSave(items) {
                try {
                  sessionStorage.setItem(KB_RECENT_MOVES_KEY, JSON.stringify((items || []).slice(0, KB_RECENT_MOVES_MAX)));
                } catch {}
              }

              function kbRecentIsOpen() {
                try {
                  return localStorage.getItem(KB_RECENT_OPEN_KEY) === "1";
                } catch {
                  return false;
                }
              }

              function kbRecentSetOpen(open) {
                try {
                  localStorage.setItem(KB_RECENT_OPEN_KEY, open ? "1" : "0");
                } catch {}
              }

              function kbCustomerNameFromEl(el) {
                if (!el) return "Unbekannter Kunde";

                return (
                  el.dataset.customerName ||
                  el.dataset.customerFullName ||
                  el.querySelector?.(".card-name")?.textContent?.trim() ||
                  el.querySelector?.(".customer-link")?.textContent?.trim() ||
                  el.querySelector?.("[data-customer-name]")?.textContent?.trim() ||
                  el.querySelector?.("td")?.textContent?.trim() ||
                  "Unbekannter Kunde"
                );
              }

              function kbProductNameFromEl(el) {
                if (!el) return "";

                return (
                  el.dataset.productName ||
                  el.dataset.productStageName ||
                  el.dataset.initial ||
                  el.querySelector?.(".product-name")?.textContent?.trim() ||
                  el.querySelector?.(".product_circle")?.textContent?.trim() ||
                  ""
                );
              }

              function kbProductInitialFromEl(el) {
                return (
                  el?.dataset?.initial ||
                  el?.querySelector?.(".product_circle")?.textContent?.trim() ||
                  kbProductNameFromEl(el) ||
                  "KD"
                ).slice(0, 4);
              }

              function kbCardDomIdFrom(el, leadProductId = "") {
                if (el?.id && el.id.startsWith("card-")) return el.id;
                if (leadProductId) return "card-" + String(leadProductId).replace(/^card-/, "");
                if (el?.id) return el.id;
                return "";
              }

              function kbRecentEnsureUI() {
                let wrap = document.getElementById("kbRecentToggleWrap");
                let panel = document.getElementById("kbRecentPanel");

                // If one element exists without the other, remove the broken leftover first.
                // This prevents duplicated IDs after hot reloads or partial renders.
                if ((wrap && !panel) || (!wrap && panel)) {
                  try { wrap?.remove(); } catch {}
                  try { panel?.remove(); } catch {}
                  wrap = null;
                  panel = null;
                }

                if (wrap && panel) return { wrap, panel };

                wrap = document.createElement("div");
                wrap.id = "kbRecentToggleWrap";
                wrap.className = "kb-recent-toggle-wrap";
                wrap.innerHTML = `
                  <button type="button" class="kb-recent-toggle" id="kbRecentToggle">
                    <i class="feather icon-activity"></i>
                    <span>Letzte Änderung</span>
                    <b class="kb-recent-toggle-count" id="kbRecentCount">0</b>
                  </button>
                `;

                panel = document.createElement("div");
                panel.id = "kbRecentPanel";
                panel.className = "kb-recent-panel";
                panel.innerHTML = `
                  <div class="kb-recent-panel-head">
                    <div class="kb-recent-panel-title">
                      <i class="feather icon-zap"></i>
                      Kürzlich geänderte Kunden
                    </div>
                    <button type="button" class="kb-recent-close" id="kbRecentClose" title="Schließen">
                      <i class="feather icon-x"></i>
                    </button>
                  </div>
                  <div class="kb-recent-list" id="kbRecentList"></div>
                `;

                const board = document.getElementById("kanban");

                // IMPORTANT:
                // insertBefore(referenceNode) only works when referenceNode is a direct child
                // of the parent. Do not use board.closest(...), because #kanban is usually
                // nested inside .kanban-zoom-card and is not a direct child of that element.
                const parent = board?.parentNode || document.body;

                if (board && parent && board.parentNode === parent) {
                  parent.insertBefore(wrap, board);
                  parent.insertBefore(panel, board);
                } else {
                  document.body.prepend(panel);
                  document.body.prepend(wrap);
                }

                document.getElementById("kbRecentToggle")?.addEventListener("click", () => {
                  const isOpen = !panel.classList.contains("is-open");
                  panel.classList.toggle("is-open", isOpen);
                  kbRecentSetOpen(isOpen);
                });

                document.getElementById("kbRecentClose")?.addEventListener("click", () => {
                  panel.classList.remove("is-open");
                  kbRecentSetOpen(false);
                });

                featherRefreshSoon();
                return { wrap, panel };
              }

              function kbRecentRender({ hot = false, open = null, stage = "" } = {}) {
                const { panel } = kbRecentEnsureUI();
                const list = document.getElementById("kbRecentList");
                const toggle = document.getElementById("kbRecentToggle");
                const count = document.getElementById("kbRecentCount");
                const items = kbRecentLoad();

                if (count) count.textContent = String(items.length);

                if (toggle) {
                  Array.from(toggle.classList).forEach((cls) => {
                    if (cls === "is-hot" || cls.startsWith("is-stage-")) toggle.classList.remove(cls);
                  });

                  if (stage) toggle.classList.add(kbStageClass(stage));

                  if (hot) {
                    void toggle.offsetWidth;
                    toggle.classList.add("is-hot");
                  }
                }

                const shouldOpen = open === null ? kbRecentIsOpen() : !!open;
                panel.classList.toggle("is-open", shouldOpen);

                if (!list) return;

                if (!items.length) {
                  list.innerHTML = `
                    <div class="text-muted small font-weight-bold px-2 py-1">
                      Noch keine Änderung in dieser Sitzung.
                    </div>
                  `;
                  featherRefreshSoon();
                  return;
                }

                list.innerHTML = items.map((item, index) => `
                  <div class="kb-recent-item ${index === 0 ? "is-new" : ""}" style="--kb-recent-color:${escapeHTML(item.color || "#93c21c")}">
                    <div class="kb-recent-dot">${escapeHTML(item.productInitial || "KD")}</div>

                    <div class="kb-recent-body">
                      <div class="kb-recent-customer" title="${escapeHTML(item.customerName || "")}">
                        ${escapeHTML(item.customerName || "Unbekannter Kunde")}
                      </div>

                      <div class="kb-recent-route">
                        ${item.productName ? `<span>${escapeHTML(item.productName)} · </span>` : ""}
                        <strong>${escapeHTML(item.fromLabel || item.oldStage || "-")}</strong>
                        &nbsp;➜&nbsp;
                        <strong>${escapeHTML(item.toLabel || item.newStage || "-")}</strong>
                      </div>

                      <div class="kb-recent-time">
                        ${escapeHTML(item.timeLabel || "")}
                      </div>
                    </div>

                    <button type="button"
                            class="kb-recent-jump"
                            data-kb-recent-jump="${escapeHTML(item.cardId || "")}"
                            data-kb-recent-lead="${escapeHTML(item.leadProductId || "")}"
                            data-kb-recent-stage="${escapeHTML(item.newStage || "")}"
                            title="Karte anzeigen">
                      <i class="feather icon-crosshair"></i>
                    </button>
                  </div>
                `).join("");

                featherRefreshSoon();
              }

              function kbFindCard(cardId = "", leadProductId = "") {
                if (cardId) {
                  const byId = document.getElementById(cardId);
                  if (byId) return byId;
                }

                if (leadProductId) {
                  const cleanId = String(leadProductId).replace(/^card-/, "");
                  return (
                    document.querySelector(`#card-${CSS.escape(cleanId)}`) ||
                    document.querySelector(`.card[data-lead-product-id="${CSS.escape(String(leadProductId))}"]`) ||
                    document.querySelector(`.card[data-lead-product-list-id="${CSS.escape(String(leadProductId))}"]`)
                  );
                }

                return null;
              }

              function kbFocusChangedCard(cardId = "", leadProductId = "", stage = "") {
                const card = kbFindCard(cardId, leadProductId);

                if (!card) {
                  Swal.fire({
                    icon: "info",
                    title: "Karte nicht sichtbar",
                    text: "Die Karte ist wahrscheinlich durch Filter, Suche oder Pagination ausgeblendet.",
                    confirmButtonText: "OK"
                  });
                  return;
                }

                const finalStage = stage || card.dataset.stage || card.closest(".column")?.id || "";
                const color = kbStageColor(finalStage);

                card.style.setProperty("--kb-live-color", color);
                card.style.setProperty("--kb-live-soft", `${color}33`);

                card.scrollIntoView({
                  behavior: "smooth",
                  block: "center",
                  inline: "center"
                });

                card.classList.remove("kb-live-changed");
                void card.offsetWidth;
                card.classList.add("kb-live-changed");

                window.setTimeout(() => {
                  card.classList.remove("kb-live-changed");
                }, 3800);
              }

              function kbRecentAddStageMove({ card, oldStage, newStage, response = null, openPanel = false }) {
                const final = response?.final || {};
                const leadProductId =
                  final.id ||
                  card?.dataset?.leadProductId ||
                  card?.dataset?.leadProductListId ||
                  card?.id?.replace(/^card-/, "") ||
                  "";

                const cardId = kbCardDomIdFrom(card, leadProductId);
                const color = kbStageColor(newStage);
                const now = new Date();

                const item = {
                  id: `${Date.now()}-${leadProductId || cardId}`,
                  cardId,
                  leadProductId,
                  customerName: kbCustomerNameFromEl(card),
                  productName: kbProductNameFromEl(card),
                  productInitial: kbProductInitialFromEl(card),
                  oldStage,
                  newStage,
                  fromLabel: kbStageLabel(oldStage),
                  toLabel: kbStageLabel(newStage),
                  color,
                  changedAt: now.toISOString(),
                  timeLabel: now.toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" }),
                };

                const items = kbRecentLoad()
                  .filter((x) => String(x.cardId) !== String(item.cardId))
                  .filter((x) => String(x.leadProductId) !== String(item.leadProductId));

                items.unshift(item);
                kbRecentSave(items);

                kbRecentRender({
                  hot: true,
                  open: openPanel || kbRecentIsOpen(),
                  stage: newStage,
                });

                window.setTimeout(() => {
                  document.querySelector("#kbRecentList .kb-recent-item.is-new")?.classList.remove("is-new");
                }, 2600);
              }

              function kbRealtimeAfterMove(card, oldStage, newStage, response = null) {
                const leadProductId =
                  response?.final?.id ||
                  card?.dataset?.leadProductId ||
                  card?.dataset?.leadProductListId ||
                  card?.id?.replace(/^card-/, "") ||
                  "";

                const cardId = kbCardDomIdFrom(card, leadProductId);

                requestAnimationFrame(() => {
                  const movedCard = kbFindCard(cardId, leadProductId) || card;
                  if (!movedCard) return;

                  movedCard.dataset.stage = canonicalStage(newStage);
                  movedCard.classList.remove("kb-live-moving");
                  kbFocusChangedCard(cardId, leadProductId, newStage);
                });

                kbRecentAddStageMove({
                  card,
                  oldStage,
                  newStage,
                  response,
                  openPanel: false
                });
              }

              document.addEventListener("click", function (e) {
                const btn = e.target.closest("[data-kb-recent-jump]");
                if (!btn) return;

                e.preventDefault();
                e.stopPropagation();

                kbFocusChangedCard(
                  btn.dataset.kbRecentJump || "",
                  btn.dataset.kbRecentLead || "",
                  btn.dataset.kbRecentStage || ""
                );
              });

              document.addEventListener("DOMContentLoaded", () => {
                kbRecentRender({ open: false });
              });

              /* -------------------------------------------------------------------------- */
              /* Kanban Drop                                                                 */
              /* -------------------------------------------------------------------------- */
              async function onKanbanDrop(ev) {
                ev.preventDefault();
                ev.stopPropagation();

                if (ev.target.closest("[data-understage-dropzone]")) return;
                const col = ev.target.closest(".column");
                if (!col) return;

                const raw = ev.dataTransfer?.getData(window.KB_DND_MIME) || "";
                const ids = Array.isArray(safeJSON(raw, [])) ? safeJSON(raw, []) : [];
                if (!ids.length) return;

                const card = qs(`#${CSS.escape(ids[0])}`);
                if (!card) return;

                const newStage = APP.stageWorkflow?.mode === "product" ? col.id : canonicalStage(col.id);
                const currentStage = APP.stageWorkflow?.mode === "product"
                  ? (card.dataset.productStageId ? `product_stage_${card.dataset.productStageId}` : (card.dataset.stage || ""))
                  : canonicalStage(card.dataset.companyStage || card.dataset.stage);
                if (currentStage === newStage) return;

                // 👇 ADDED PAUSE/STOP CHECK BLOCK 👇
                const runState = card.dataset.runState || 'playing';
                if (runState === 'paused' || runState === 'stopped') {
                    let reason = "Kein Grund angegeben.";
                    try {
                        const history = JSON.parse(card.dataset.stageHistory || "[]");
                        if (Array.isArray(history) && history.length > 0) {
                            // Get the most recent entry from the history array
                            const latest = history[history.length - 1];
                            if (latest && latest.description) {
                                reason = latest.description;
                            }
                        }
                    } catch(e) {
                        console.warn("Could not parse stage_history", e);
                    }

                    const stateDe = runState === 'paused' ? 'pausiert' : 'gestoppt';
                    Swal.fire({
                        icon: "warning",
                        title: "Aktion nicht möglich",
                        html: `Dieser Eintrag ist momentan <b>${stateDe}</b> und kann nicht verschoben werden.<br><br><b>Grund:</b> ${esc(reason)}`
                    });
                    return; // Block the drop!
                }
                // 👆 END PAUSE/STOP CHECK BLOCK 👆

                // teams from card (if you store it)
                let currentTeamIds = safeJSON(card.dataset.teamIds || "[]", []);
                if (!Array.isArray(currentTeamIds)) currentTeamIds = [];
                currentTeamIds = currentTeamIds.map((x) => toInt(x)).filter(Boolean);

                const backward = isBackward(currentStage, newStage);
                const removedTeamIds = backward ? currentTeamIds.slice() : [];

                const stageTeams = safeJSON(card.dataset.teamAssignments || "[]", []);
                const confirm = await confirmStageChange(newStage, currentStage, currentTeamIds, {
                  removedTeamIds,
                  stageTeams,
                  productId: card.dataset.productId,
                  productStageId: card.dataset.productStageId,
                  currentProductStageId: card.dataset.productStageId,
                });
                if (!confirm.ok) return;

                try {
                  const { customerId, alternativeId, productId, leadProductId } = card.dataset;

                  const stageResponse = await applyStageChange({
                    customerId,
                    alternativeId,
                    productId,
                    leadProductId,
                    newStage,
                    noteHTML: confirm.reasonHTML,
                    teams: confirm.teams,
                    mode: confirm.mode || APP.stageWorkflow?.mode || "company",
                    companyStageKey: confirm.companyStageKey || newStage,
                    productStageId: confirm.productStageId || workflowStageIdFromKey(newStage),
                    productTaskPhaseId: confirm.productTaskPhaseId || null,
                  });

                  if (confirm.reminder?.enabled) {
                    await createReminderFromStageChange({ leadProductId, customerId, alternativeId, productId }, confirm.reminder);
                    if (window.preloadLeadReminderSummaries) window.setTimeout(window.preloadLeadReminderSummaries, 350);
                  }

                  card.dataset.teamIds = JSON.stringify(stageResponse?.final?.team_ids || confirm.teams || []);
                  if (stageResponse?.final?.team_assignments) {
                    card.dataset.teamAssignments = JSON.stringify(stageResponse.final.team_assignments);
                  }
                  const finalColumnStage = (confirm.mode === "product" || APP.stageWorkflow?.mode === "product")
                    ? `product_stage_${stageResponse?.lead?.product_stage_id || confirm.productStageId || workflowStageIdFromKey(newStage)}`
                    : canonicalStage(stageResponse?.stage || stageResponse?.lead?.status || confirm.companyStageKey || newStage);
                  card.dataset.stage = finalColumnStage;
                  card.dataset.companyStage = stageResponse?.lead?.status || confirm.companyStageKey || card.dataset.companyStage || "";
                  card.dataset.productStageId = stageResponse?.lead?.product_stage_id || confirm.productStageId || card.dataset.productStageId || "";
                  card.dataset.productTaskPhaseId = stageResponse?.lead?.product_task_phase_id || confirm.productTaskPhaseId || "";
                  card.dataset.productStageName = stageResponse?.lead?.product_stage_name || stageResponse?.lead?.product_stage?.stage || card.dataset.productStageName || "";
                  card.dataset.productTaskPhaseName = stageResponse?.lead?.product_task_phase_name || stageResponse?.lead?.product_task_phase?.phase_name || card.dataset.productTaskPhaseName || "";

                  const finalSubStageId = stageResponse?.lead?.lead_stage_sub_stage_id
                    || stageResponse?.lead?.lead_sub_stage_id
                    || defaultLeadSubStageForStage(stageResponse?.lead?.status || confirm.companyStageKey || newStage)
                    || "";
                  card.dataset.leadStageSubStageId = finalSubStageId ? String(finalSubStageId) : "";
                  const finalSubMeta = subStageMetaForStage(stageResponse?.lead?.status || confirm.companyStageKey || newStage, finalSubStageId);
                  const oldSubChip = card.querySelector(".kb-understage-chip");
                  if (oldSubChip) oldSubChip.remove();
                  if (finalSubMeta?.name) {
                    const chip = document.createElement("div");
                    chip.className = "kb-understage-chip";
                    chip.innerHTML = `<i class="feather icon-git-branch"></i>${escapeHTML(finalSubMeta.name)}`;
                    const preview = card.querySelector(".kb-next-step-preview");
                    if (preview) card.insertBefore(chip, preview);
                    else card.appendChild(chip);
                  }

                  const stageHistoryFromResponse = stageResponse?.lead?.stage_history || stageResponse?.final?.stage_history || null;
                  if (stageHistoryFromResponse) {
                    card.dataset.stageHistory = typeof stageHistoryFromResponse === "string" ? stageHistoryFromResponse : JSON.stringify(stageHistoryFromResponse);
                  } else {
                    const fallbackHistory = (window.KanbanStageTime?.parseStageHistorySafe || window.parseStageHistorySafe || function(){ return []; })(card.dataset.stageHistory || "[]");
                    fallbackHistory.push({
                      from: currentStage,
                      to: newStage,
                      stage: newStage,
                      changed_at: new Date().toISOString(),
                      description: confirm.reasonHTML || "",
                    });
                    card.dataset.stageHistory = JSON.stringify(fallbackHistory);
                  }

                  (window.KanbanStageTime?.refreshCardStageTime || window.refreshCardStageTime)(card, {
                    stage: stageResponse?.stage || stageResponse?.lead?.status || confirm.companyStageKey || newStage,
                    status: stageResponse?.lead?.status || stageResponse?.stage || confirm.companyStageKey || newStage,
                    created_at: stageResponse?.lead?.created_at || card.dataset.createdAt || new Date().toISOString(),
                    updated_at: stageResponse?.lead?.updated_at || new Date().toISOString(),
                    stage_history: card.dataset.stageHistory,
                  }, finalColumnStage);

                  card.classList.add("kb-live-moving");

                  moveOrRefreshKanbanCard({ newStage: finalColumnStage, cardFromDOM: card });

                  kbRealtimeAfterMove(card, currentStage, finalColumnStage, stageResponse);

                  enforceActionVisibility(card);
                  updateCounts();
                  featherRefreshSoon();

                  /*
                   * Important:
                   * Do not call silentRefreshBoth() after drag/drop.
                   * It reloads the board and destroys open modals/forms.
                   */

                  Swal.fire({
                    icon: "success",
                    title: "Geändert",
                    text: "Die Karte wurde direkt verschoben und markiert.",
                    timer: 950,
                    showConfirmButton: false,
                  });
                } catch (err) {
                  if (err?.cancelled) return;
                  Swal.fire("Fehler", err?.message || "Serverfehler.", "error");
                }
              }
              /* -------------------------------------------------------------------------- */
              /* List rendering (+ LiveFeed row)                                             */
              /* -------------------------------------------------------------------------- */
                function priorityMeta(raw) {
                  const p = String(raw || "normal").toLowerCase();
                  if (p === "high" || p === "urgent") return { label: "Hoch", cls: "prio-high", icon: "alert-triangle" };
                  if (p === "low") return { label: "Niedrig", cls: "prio-low", icon: "arrow-down-circle" };
                  return { label: "Normal", cls: "prio-normal", icon: "circle" };
                }

                function employeeCellHTML(lead) {
                  const esc = (s) =>
                    String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                  const office = lead?.employee || null;
                  const field = lead?.field_employee || lead?.fieldEmployee || null;

                  // team can come as: team_members OR teams (array)
                  const teamArr = Array.isArray(lead?.team_members)
                    ? lead.team_members
                    : Array.isArray(lead?.teams)
                      ? lead.teams
                      : [];

                  const hasOffice = !!(office && (office.name || office.lastname));
                  const hasField  = !!(field && (field.name || field.lastname));
                  const hasTeam   = teamArr.length > 0;

                  if (!hasOffice && !hasField && !hasTeam) return "<small>&ndash;</small>";

                  const imgOrNo = (img) => (img ? esc(img) : "noimage.png");

                  const chunks = [];

                  // wrapper to align employees + team similar to blade
                  chunks.push(`<div class="d-flex align-items-start flex-wrap" style="gap:10px;">`);

                  // employee stack
                  if (hasOffice || hasField) {
                    const empChunks = [];

                    if (hasOffice) {
                      empChunks.push(`
                        <div class="d-flex align-items-center">
                          <img src="/images/employee/${imgOrNo(office.image)}" width="30" height="30" class="rounded-circle mr-1" alt="" style="object-fit:cover;">
                          <div>
                            <div style="line-height:1.1"><strong>${esc(office.lastname || "")}</strong> ${esc(office.name || "")}</div>
                            <small class="text-muted">Innendienst</small>
                          </div>
                        </div>
                      `);
                    }

                    if (hasField) {
                      empChunks.push(`
                        <div class="d-flex align-items-center">
                          <img src="/images/employee/${imgOrNo(field.image)}" width="26" height="26" class="rounded-circle mr-1" alt="" style="object-fit:cover;">
                          <div>
                            <div style="line-height:1.1"><strong>${esc(field.lastname || "")}</strong> ${esc(field.name || "")}</div>
                            <small class="text-muted">Außendienst</small>
                          </div>
                        </div>
                      `);
                    }

                    chunks.push(`<div class="d-flex flex-column" style="gap:6px;">${empChunks.join("")}</div>`);
                  }

                  // team avatars
                  if (hasTeam) {
                    const avatars = teamArr
                      .map((t) => {
                        const name = `${t?.lastname ?? ""} ${t?.name ?? ""}`.trim() || "Team";
                        const img = t?.image ? `/images/employee/${esc(t.image)}` : `/images/employee/noimage.png`;
                        return `
                          <li class="avatar pull-up" title="${esc(name)}" style="margin-left:-8px;">
                            <img class="media-object rounded-circle"
                                src="${img}"
                                width="26" height="26"
                                alt="${esc(name)}"
                                style="border:2px solid #fff; object-fit:cover;">
                          </li>`;
                      })
                      .join("");

                    chunks.push(`
                      <div class="d-flex align-items-center" style="margin-top:2px; padding-left:10px; border-left:1px solid #e0e0e0;">
                        <ul class="list-unstyled users-list m-0 d-flex align-items-center" style="gap:0; padding:0;">
                          ${avatars}
                        </ul>
                      </div>
                    `);
                  }

                  chunks.push(`</div>`);
                  return chunks.join("");
                }

                // ---------------------------------------------------------
                // 1. Helper: Live Feed HTML Structure
                // ---------------------------------------------------------
                  function listFeedHTML() {
                    return `
                    <div class="live-feed-bar list-live-feed card-live-feed"
                        data-feed-root
                        data-feed-count="0"
                        style="display:none; margin-top:0.5rem; width: 100%; max-width: 450px;">
                      <div class="live-feed-left">
                        <div class="live-feed-icon"><i class="feather icon-zap"></i></div>
                      </div>
                      <div class="live-feed-body">
                        <div class="live-feed-line" data-feed-empty>
                          <span class="live-feed-title">Keine Aktivitäten</span>
                          <span class="live-feed-dot">•</span>
                          <span class="live-feed-text">Noch keine Termine oder Aufgaben.</span>
                        </div>
                        <div class="live-feed-line" data-feed-line>
                          <span class="live-feed-title" data-feed-title>Aktivität</span>
                          <span class="live-feed-dot">•</span>
                          <span class="live-feed-text" data-feed-text>Details…</span>
                        </div>
                        <div class="live-feed-meta">
                          <span class="live-feed-pill" data-feed-pill>Info</span>
                          <span class="live-feed-time">
                            <i class="feather icon-clock mr-25"></i>
                            <span data-feed-time>–</span>
                          </span>
                          <span class="live-feed-counter" data-feed-counter></span>
                        </div>
                      </div>
                      <div class="live-feed-controls">
                        <button type="button" class="live-feed-btn" title="Zurück" data-feed-prev>
                          <i class="feather icon-skip-back"></i>
                        </button>
                        <button type="button" class="live-feed-btn" title="Pause / Abspielen" data-feed-toggle>
                          <i class="feather icon-pause" data-feed-icon-pause></i>
                          <i class="feather icon-play d-none" data-feed-icon-play></i>
                        </button>
                        <button type="button" class="live-feed-btn" title="Weiter" data-feed-next>
                          <i class="feather icon-skip-forward"></i>
                        </button>
                        <button type="button" class="live-feed-btn" title="Alle Aktivitäten anzeigen" data-feed-open-modal>
                            <i class="feather icon-maximize-2"></i>
                        </button>
                      </div>
                    </div>
                  `;
                }

                // ---------------------------------------------------------
                // 2. Helper: Avatar Generator
                // ---------------------------------------------------------
                function avatarLiFromEmp(emp, { withData = false, assignedBy = "", assignedAt = "", stage = "", stageLabel = "" } = {}) {
                  if (!emp) return "";

                  // Constants from parent scope or fallback
                  const EMP_SRC = (window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.EMP_SRC) ? window.LeadUI.APP.EMP_SRC : '/images/employee';
                  const safeStr = (v) => (v == null ? "" : String(v));
                  const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                  const id = Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0;
                  const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;
                  const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || `#${id}`;

                  return `
                    <li class="avatar pull-up"
                        ${withData ? `data-emp-id="${esc(id)}"` : ""}
                        ${withData ? `data-assigned-by="${esc(assignedBy)}"` : ""}
                        ${withData ? `data-assigned-at="${esc(assignedAt)}"` : ""}
                        ${withData ? `data-stage="${esc(stage)}"` : ""}
                        ${withData ? `data-stage-label="${esc(stageLabel)}"` : ""}
                        title="${esc(name)}"
                        style="margin-left:-8px;">
                      <img class="media-object rounded-circle"
                          src="${esc(img)}"
                          width="26" height="26"
                          alt="${esc(name)}"
                          style="border:2px solid #fff; object-fit:cover;">
                    </li>
                  `;
                }

                // ---------------------------------------------------------
                // 3. Helper: Employee & Team Column Generator
                // ---------------------------------------------------------
                function listEmpAndTeamHTML(lead) {
                  const stageKey = window.LeadUI.utils.canonicalStage(lead?.stage);
                  const stageLabel = window.LeadUI.APP.stageNames?.[stageKey] || stageKey;
                  const safeStr = (v) => (v == null ? "" : String(v));
                  const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                  const main = [];
                  if (lead?.employee && (lead.employee.employee_id || lead.employee.id)) main.push(lead.employee);
                  if (lead?.field_employee && (lead.field_employee.employee_id || lead.field_employee.id)) main.push(lead.field_employee);

                  const allAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                  const currentAssignments = Array.isArray(lead?.current_team_assignments) && lead.current_team_assignments.length
                    ? lead.current_team_assignments
                    : allAssignments.filter((a) => window.LeadUI.utils.canonicalStage(a?.stage || stageKey) === stageKey);

                  const fallbackCurrent = currentAssignments.length
                    ? currentAssignments
                    : (Array.isArray(lead?.team_members) ? lead.team_members.map((m) => ({ member: m, stage: stageKey, stage_label: stageLabel })) : []);

                  const visible = fallbackCurrent.slice(0, 2);
                  const rest = Math.max(0, fallbackCurrent.length - visible.length);

                  const mainHtml = main.length
                    ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center list-main-users" title="Hauptverantwortliche">
                        ${main.slice(0, 2).map((e) => avatarLiFromEmp(e, { withData: false })).join("")}
                      </ul>`
                    : "";

                  const miniAvatars = visible.map((a) => {
                    const member = a?.member || a || {};
                    const id = Number(member?.employee_id ?? member?.id ?? a?.employee_id ?? 0) || 0;
                    const img = member?.image ? `/images/employee/${member.image}` : `/images/employee/noimage.png`;
                    const name = `${safeStr(member?.lastname).trim()} ${safeStr(member?.name).trim()}`.trim() || `#${id}`;
                    const u = a?.assigned_by_user;
                    let ab = "";
                    if (u && (u.name || u.lastname)) ab = `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
                    else if (a?.assigned_by) ab = `Mitarbeiter #${a.assigned_by}`;
                    const at = safeStr(a?.assigned_at || a?.assigned_at_iso || "").trim();
                    const itemStage = window.LeadUI.utils.canonicalStage(a?.stage || stageKey);
                    const itemStageLabel = a?.stage_label || window.LeadUI.APP.stageNames?.[itemStage] || stageLabel;
                    return `<li class="avatar pull-up"
                                data-emp-id="${esc(id)}"
                                data-assigned-by="${esc(ab)}"
                                data-assigned-at="${esc(at)}"
                                data-stage="${esc(itemStage)}"
                                data-stage-label="${esc(itemStageLabel)}"
                                title="${esc(name)}">
                              <img class="media-object rounded-circle" src="${esc(img)}" width="24" height="24" alt="${esc(name)}" style="border:2px solid #fff; object-fit:cover;">
                            </li>`;
                  }).join("");

                  const teamButton = `
                    <button type="button"
                            class="kb-team-pill kb-team-pill--list"
                            data-show-stage-team="${esc(stageKey)}"
                            title="Team nach Phasen anzeigen">
                      <ul class="list-unstyled users-list m-0 d-inline-flex align-items-center" data-team-hover>
                        ${miniAvatars}
                      </ul>
                      <span>Teams</span>
                      <span class="kb-team-pill-count">${fallbackCurrent.length}</span>
                      ${rest > 0 ? `<span class="kb-team-pill-count">+${rest}</span>` : ``}
                    </button>`;

                  if (!main.length && !fallbackCurrent.length && !allAssignments.length) {
                    return `<button type="button" class="kb-team-pill kb-team-pill--list" data-show-stage-team="${esc(stageKey)}"><span>Teams</span><span class="kb-team-pill-count">0</span></button>`;
                  }

                  return `<div class="list-team-cell d-flex align-items-center" style="gap:8px; min-width:180px;">${mainHtml}${teamButton}</div>`;
                }

                // ---------------------------------------------------------
                // 4. Main Function: Build Row
                // ---------------------------------------------------------
                   function buildRowHTML(lead) {
                    // 1. Define helper 'esc' immediately to avoid errors
                    const safeStr = (v) => (v == null ? "" : String(v));
                    const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" })[m]);
                    const fmtDE = (v) => { try { return v ? new Date(v).toLocaleDateString("de-DE") : "-"; } catch { return "-"; } };

                    const cName = safeStr(lead?.customer_name).trim();
                    const cLastname = safeStr(lead?.customer_lastname).trim();
                    const cFirma = safeStr(lead?.firma).trim();
                    const displayName = `${cLastname} ${cName}`.trim() || cFirma || "Unbekannt";

                    const stageKey = utils.canonicalStage(lead?.stage);

                    const cId = lead?.customer_id ?? "";
                    const aId = lead?.alternative_id ?? "";
                    const pId = lead?.product_id ?? "";
                    const lpId = lead?.lead_product_id ?? "";

                    const ws = String(lead?.work_status || "playing").toLowerCase();

                    // 1. Get Status Block from Kanban (Core)
                    const statusBlockHTML = kanban ? kanban.buildStatusBlock(lead) : `<span class="badge badge-secondary">${stageKey}</span>`;

                    // 2. Get Live Feed HTML
                    const liveFeedRow = listFeedHTML();

                    // 3. Meta Logic (Assigned By...)
                    const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                    let teamsRaw = lead?.teams;
                    if (typeof teamsRaw === "string") {
                        try {
                            teamsRaw = JSON.parse(teamsRaw);
                        } catch {
                            teamsRaw = [];
                        }
                    }
                    if (!Array.isArray(teamsRaw)) teamsRaw = [];

                    const assignments = teamAssignments.length ?
                        teamAssignments :
                        teamsRaw.map((t) => ({
                            assigned_at: t?.assigned_at ?? null,
                            assigned_at_iso: t?.assigned_at_iso ?? null,
                            assigned_by: t?.assigned_by ?? null,
                            assigned_by_user: t?.assigned_by_user ?? null,
                            stage_label: t?.stage_label ?? null,
                        }));

                    const parseAssignedAt = (a) => {
                        const raw = (a?.assigned_at_iso || a?.assigned_at || "").trim();
                        if (!raw) return 0;
                        const isoish = raw.includes("T") ? raw : raw.replace(" ", "T");
                        const ts = Date.parse(isoish);
                        return Number.isFinite(ts) ? ts : 0;
                    };

                    const latestA = assignments.reduce((best, a) => {
                        const ta = parseAssignedAt(a);
                        const tb = parseAssignedAt(best);
                        return ta > tb ? a : best;
                    }, null);

                    const assignedBy = (() => {
                        const u = latestA?.assigned_by_user;
                        if (u && (u.name || u.lastname)) return `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
                        const id = Number(latestA?.assigned_by ?? 0);
                        return id > 0 ? `Mitarbeiter #${id}` : "";
                    })();

                    const assignedAtRaw = (latestA?.assigned_at_iso || latestA?.assigned_at || "").trim();
                    const STAGE_DE = {
                        lead: "Lead",
                        offer: "Angebot",
                        follow_up: "Nachfassen",
                        accepted: "Annehmen",
                        deal: "Auftrag",
                        project: "Montage",
                        completed: "Abschluss",
                        archive: "Archiv",
                        junk: "Junk"
                    };
                    const phaseLabel = (() => {
                        const lbl = (latestA?.stage_label || "").trim();
                        if (lbl) return lbl;
                        const key = String(latestA?.stage || "").trim().toLowerCase();
                        return STAGE_DE[key] || "";
                    })();

                    const assignedMetaHTML =
                        assignedBy || assignedAtRaw || phaseLabel ?
                        `<div class="small text-muted mt-1 w-100">
                              ${phaseLabel ? `<span class="mr-2"><i class="feather icon-layers mr-25"></i><span>Phase: <strong>${esc(phaseLabel)}</strong></span></span><span class="mx-1">•</span>` : ``}
                              <i class="feather icon-user mr-25"></i><span>Zugewiesen von: <strong>${esc(assignedBy || "-")}</strong></span>
                              <span class="mx-1">•</span>
                              <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E" style="vertical-align:-1px; margin-right:3px;" alt="" /><span>${esc(assignedAtRaw ? fmtDE(assignedAtRaw) : "-")}</span>
                            </div>` :
                        "";

                    // CHANGED: Grouped customer link and action bar into .customer-name-wrapper
                    return `
                        <tr id="row-${esc(lpId)}"
                            class="list-row-item"
                            data-customer-id="${esc(cId)}"
                            data-alternative-id="${esc(aId)}"
                            data-product-id="${esc(pId)}"
                            data-lead-product-id="${esc(lpId)}"
                            data-stage="${esc(stageKey)}"
                            data-product-stage-id="${esc(lead?.product_stage_id || '')}"
                            data-product-task-phase-id="${esc(lead?.product_task_phase_id || '')}"
                            data-product-stage-name="${esc(lead?.product_stage_name || '')}"
                            data-product-task-phase-name="${esc(lead?.product_task_phase_name || '')}"
                            data-initial="${esc(lead?.initial || '')}"
                            data-run-state="${esc(ws)}"
                            data-stage-history="${esc(typeof lead?.stage_history === 'string' ? lead?.stage_history : JSON.stringify(lead?.stage_history || []))}"
                            data-team-assignments="${esc(JSON.stringify(Array.isArray(lead?.team_assignments) ? lead.team_assignments : []))}">

                          <td style="width: 100px;">${lead?.created_at ? fmtDE(lead.created_at) : "-"}</td>

                          <td style="min-width: 350px;">

                            <div class="customer-name-wrapper">
                                <a href="/new_lead_profile/${encodeURIComponent(safeStr(cId))}" class="customer-link" style="font-size:1.05rem;">
                                  ${esc(displayName)}
                                </a>

                                <div class="list-action-bar">
                                  <button type="button" class="btn-list-icon" data-menu="termin" title="Termin">
                                    <i class="feather icon-calendar"></i>
                                    <span class="badge-notes" data-ap-count style="display:none">0</span>
                                  </button>
                                  <button type="button" class="btn-list-icon" data-menu="aufgabe" title="Aufgabe">
                                    <i class="feather icon-check-square"></i>
                                    <span class="badge-notes" data-pt-count style="display:none">0</span>
                                  </button>

                                  <span style="border-left:1px solid #ddd; height:14px; margin:0 4px;"></span>

                                  <button type="button" class="btn-list-icon note" data-open-notes data-customer="${esc(cId)}" data-alt="${esc(aId)}" data-product="${esc(pId)}" data-lead-product-list-id="${esc(lead?.lead_product_id || lead?.lead_product_list_id || lead?.id || '')}" data-customer-name="${esc(displayName)}" data-product-name="${esc(lead?.article_group || lead?.product_name || lead?.product || lead?.initial || '')}">
                                    <i class="feather icon-message-square"></i>
                                    <span class="badge-notes" data-count="0" style="display:none">0</span>
                                  </button>

                                  <button type="button" class="btn-list-icon toggle-feed-btn" title="Aktivitäten anzeigen">
                                      <i class="feather icon-zap"></i>
                                  </button>

                                  <div class="kb-menu" style="position:relative; display:inline-block;">
                                    <button type="button" class="btn-list-icon kb-menu-toggle" data-act="custom-menu-toggle" aria-haspopup="menu" aria-expanded="false">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="kb-menu-dropdown" role="menu" hidden style="right:0; left:auto; top:100%; min-width:140px; z-index: 1050;">
                                        <button type="button" class="kb-menu-item text-success" data-run="playing"><i class="feather icon-play mr-50"></i> Start</button>
                                        <button type="button" class="kb-menu-item text-warning" data-run="paused"><i class="feather icon-pause mr-50"></i> Pause</button>
                                        <button type="button" class="kb-menu-item text-danger" data-run="stopped"><i class="feather icon-square mr-50"></i> Stopp</button>
                                        <hr class="my-50">
                                        <button type="button" class="kb-menu-item" data-menu="verlauf">
                                            <i class="feather icon-activity mr-50"></i> Verlauf
                                        </button>
                                        <button type="button" class="kb-menu-item" data-menu="product-stage-info">
                                            <i class="feather icon-info mr-50"></i> Produktstatus
                                        </button>
                                    </div>
                                  </div>
                                </div>
                            </div>

                            ${assignedMetaHTML}

                            ${liveFeedRow}
                          </td>

                          <td>${esc(lead?.city ?? "")}</td>
                          <td>${esc(lead?.initial ?? "")}</td>
                          <td>${typeof listEmpAndTeamHTML === 'function' ? listEmpAndTeamHTML(lead) : ''}</td>

                          <td>
                            ${statusBlockHTML}
                            ${(window.LeadUI && window.LeadUI.kanban && typeof window.LeadUI.kanban.offerWorkflowHTML === "function") ? window.LeadUI.kanban.offerWorkflowHTML(lead) : ""}
                          </td>

                          <td>
                            <select class="form-control stage-select" data-id="${esc(lpId)}">
                              ${Object.entries(APP.stageNames || {})
                                .filter(([k]) => !["junk", "ticket"].includes(String(k).toLowerCase()))
                                .map(([k, l]) => {
                                  const meta = APP.stageMeta?.[k] || {};
                                  return `<option value="${esc(k)}" data-color="${esc(meta.color || "#93c21c")}" data-icon="${esc(meta.icon || "circle")}" ${stageKey === k ? "selected" : ""}>${esc(l)}</option>`;
                                })
                                .join("")}
                            </select>
                          </td>
                        </tr>
                      `;
                  }
                // ---------------------------------------------------------
                // 5. Updated Bootstrapper
                // ---------------------------------------------------------
                function bootstrapListLiveFeed(container) {
                      if (!window.LeadUI.liveFeed || typeof window.LeadUI.liveFeed.loadForCard !== "function") return;

                      const root = container || document;
                      // CHANGED: .list-feed-row -> .list-row-item
                      const rows = root.querySelectorAll("tr.list-row-item"); 

                      if (!rows.length) return;

                      let i = 0;
                      const BATCH = 4; // Process in batches to avoid freezing UI

                      (function pump() {
                          const slice = Array.prototype.slice.call(rows, i, i + BATCH);
                          i += BATCH;
                          slice.forEach((row) => {
                              // IMPORTANT: Ensure the row has a customer ID before trying to load
                              if (row.dataset.customerId) {
                                  window.LeadUI.liveFeed.loadForCard(row);
                              }
                          });
                          if (i < rows.length) {
                              if ("requestIdleCallback" in window) requestIdleCallback(pump);
                              else setTimeout(pump, 0);
                          }
                      })();
                  }

                  // Expose helpers globally
                  window.listFeedHTML = listFeedHTML;
                  window.LeadUI.bootstrapListLiveFeed = bootstrapListLiveFeed;

                  function stageSelectTemplate(option, mode = "option") {
                      if (!option.id) return option.text;
                      const meta = APP.stageMeta?.[option.id] || window.LeadUI?.APP?.stageMeta?.[option.id] || {};
                      const color = option.element?.dataset?.color || meta.color || "#93c21c";
                      const icon = option.element?.dataset?.icon || meta.icon || "circle";
                      const label = option.text || APP.stageNames?.[option.id] || option.id;

                      return jQuery(`
                        <span class="stage-select2-${mode}">
                          <span class="stage-color-dot" style="background:${esc(color)}"></span>
                          <span class="stage-select2-icon"><i class="feather icon-${esc(icon)}"></i></span>
                          <span class="stage-select2-label">${esc(label)}</span>
                        </span>
                      `);
                  }

                  function initListStageSelect2(container = document) {
                      if (!window.jQuery || !jQuery.fn.select2) return;
                      const $root = jQuery(container);

                      $root.find("select.stage-select").each(function () {
                          const $el = jQuery(this);
                          if ($el.hasClass("select2-hidden-accessible")) $el.select2("destroy");

                          $el.select2({
                              width: "170px",
                              minimumResultsForSearch: 8,
                              dropdownParent: jQuery(document.body),
                              templateResult: (option) => stageSelectTemplate(option, "option"),
                              templateSelection: (option) => stageSelectTemplate(option, "selection"),
                              escapeMarkup: (m) => m,
                          });
                      });

                      setTimeout(() => { if (window.feather) window.feather.replace(); }, 30);
                  }

                  function syncListSortHeaders() {
                      const key = State.sort?.key || "created_at";
                      const dir = State.sort?.dir || "desc";

                      qsa("#profile th.sortable").forEach((th) => {
                          const active = th.dataset.sort === key;
                          th.classList.toggle("active", active);
                          th.classList.toggle("desc", active && dir === "desc");
                      });

                      if (window.feather) setTimeout(() => window.feather.replace(), 30);
                  }

              function syncSummary(data) {
                const setTxt = (sel, v) => {
                  const el = qs(sel);
                  if (el) el.textContent = String(v ?? "");
                };
                const setHTML = (sel, v) => {
                  const el = qs(sel);
                  if (el) el.innerHTML = v;
                };

                setTxt("#totalEmployees", data?.totalEmployees);
                setTxt("#totalProduct", data?.totalProducts);
                setTxt("#totalCustomer", data?.totalCustomers);

                setHTML("#statusOffen", `${data?.statusCounts?.offen ?? 0} <small>(${data?.statusPercentages?.offen ?? 0}%)</small>`);
                setHTML("#statusZusage", `${data?.statusCounts?.zusage ?? 0} <small>(${data?.statusPercentages?.zusage ?? 0}%)</small>`);
                setHTML("#statusAbsage", `${data?.statusCounts?.absage ?? 0} <small>(${data?.statusPercentages?.absage ?? 0}%)</small>`);

                setTxt("#countCustomers", data?.totalCustomers);
                setTxt("#countProducts", data?.totalProducts);
                setTxt("#countDepartments", data?.totalDepartments);
                setTxt("#countEmployees", data?.totalEmployees);
              }

              function updateListView(leads, meta) {
                const tbody = qs("#kanbanTableBody");
                if (!tbody) return;

                if (!Array.isArray(leads) || !leads.length) {
                  tbody.innerHTML = '<tr><td colspan="8" class="text-center">Keine Ergebnisse gefunden</td></tr>';
                  syncSummary(meta);
                  return;
                }

                const tmp = document.createElement("tbody");
                tmp.innerHTML = leads.map(buildRowHTML).join("");

                tbody.innerHTML = "";
                tbody.append(...tmp.childNodes);

                syncSummary(meta);
                featherRefreshSoon();

                // Notes badges (list)
                window.LeadUI?.notes?.updateNoteBadgesForVisibleCards?.();

                bootstrapListLiveFeed(tbody);
                initListStageSelect2(tbody);
                syncListSortHeaders();
              }

              function renderPagination(metaLike) {
                const wrap = qs("#listPagination");
                if (!wrap) return;

                const meta = normalizePaginationMeta(metaLike);
                if (!meta || meta.last_page <= 1) {
                  wrap.innerHTML = "";
                  return;
                }

                const { current_page, last_page } = meta;

                let html = `<nav aria-label="Seiten"><ul class="pagination mb-0">`;

                const add = (p, label, disabled = false, active = false) => {
                  if (disabled) html += `<li class="page-item disabled"><span class="page-link">${label}</span></li>`;
                  else if (active) html += `<li class="page-item active"><span class="page-link">${label}</span></li>`;
                  else html += `<li class="page-item"><a class="page-link" href="#" data-page="${p}">${label}</a></li>`;
                };

                add(current_page - 1, "«", current_page === 1);

                const win = 2;
                const st = Math.max(1, current_page - win);
                const en = Math.min(last_page, current_page + win);

                if (st > 1) {
                  add(1, "1", false, current_page === 1);
                  if (st > 2) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }

                for (let p = st; p <= en; p++) add(p, String(p), false, p === current_page);

                if (en < last_page) {
                  if (en < last_page - 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
                  add(last_page, String(last_page), false, current_page === last_page);
                }

                add(current_page + 1, "»", current_page === last_page);

                wrap.innerHTML = html + "</ul></nav>";
              }

              /* -------------------------------------------------------------------------- */
              /* Fetchers                                                                    */
              /* -------------------------------------------------------------------------- */
              function normalizeLead(raw) {
                const pick = (obj, ...keys) => {
                  for (const k of keys) {
                    const v = obj?.[k];
                    if (v !== undefined && v !== null && v !== "") return v;
                  }
                  return null;
                };
                const latest_phase = pick(raw, "latest_phase", "phase_name", "phase_title", "phase_section_title");
                const latest_activity = pick(raw, "latest_activity", "activity_title");
                const done_date = pick(raw, "done_date", "updated_at", "history_at");
                const updated_at = pick(raw, "updated_at", done_date);
                return { ...raw, latest_phase, latest_activity, done_date, updated_at };
              }

              function ensureLoadedMap() {
                if (!State.loaded || typeof State.loaded !== "object") State.loaded = { kanban: false, list: false };
                if (!("kanban" in State.loaded)) State.loaded.kanban = false;
                if (!("list" in State.loaded)) State.loaded.list = false;
              }

              function syncTabCountsFromListPayload(payload) {
                const total =
                  payload?.pagination?.total ||
                  payload?.meta?.total ||
                  (Array.isArray(payload?.leads) ? payload.leads.length : 0);

                setTabCount("#tabCountList", total);
                setTabCount("#tabCountKanban", total);
              }

              function syncTabCountsFromKanban(leads, payload = null) {
                const total = Number(payload?.total ?? payload?.pagination?.total ?? payload?.meta?.total ?? 0);
                const count = total > 0 ? total : (Array.isArray(leads) ? leads.length : 0);
                setTabCount("#tabCountKanban", count);
              }

              const KANBAN_INITIAL_LIMIT = 20;
              const KANBAN_COLUMN_LIMIT_DEFAULT = 20;
              let KANBAN_COLUMN_LIMIT = Number(localStorage.getItem("kanban.column.limit") || KANBAN_COLUMN_LIMIT_DEFAULT);
              if (![10, 20, 30, 50, 80].includes(KANBAN_COLUMN_LIMIT)) KANBAN_COLUMN_LIMIT = KANBAN_COLUMN_LIMIT_DEFAULT;
              const ColumnLoadState = {
                filtersKey: "",
                byStage: new Map(),
                bound: false,
                loading: new Set(),
                requestId: 0,
              };

              function kanbanBaseParams(qsStr = "") {
                const params = new URLSearchParams(qsStr || "");
                params.delete("page");
                params.delete("offset");
                params.delete("limit");
                return params;
              }

              function kanbanUrl(params) {
                const query = params.toString();
                return `${APP.endpoints.kanbanSearch}${query ? `?${query}` : ""}`;
              }

              function currentFilterKey(qsStr = "") {
                const params = kanbanBaseParams(qsStr);
                params.delete("stage");
                return params.toString();
              }

              function ensureColumnState(stageKey) {
                const key = String(stageKey || "lead");
                if (!ColumnLoadState.byStage.has(key)) {
                  ColumnLoadState.byStage.set(key, {
                    offset: 0,
                    limit: getKanbanColumnLimit(),
                    hasMore: true,
                    initialized: false,
                  });
                }
                return ColumnLoadState.byStage.get(key);
              }


              function getKanbanColumnLimit() {
                const select = document.getElementById("kbPerColumnLimitSelect");
                const raw = select?.value || localStorage.getItem("kanban.column.limit") || KANBAN_COLUMN_LIMIT;
                const n = parseInt(raw, 10);
                return Number.isFinite(n) && n > 0 ? Math.min(Math.max(n, 5), 100) : KANBAN_COLUMN_LIMIT;
              }

              function kanbanStageEntriesForLoad() {
                const names = APP.kanbanStageNames && Object.keys(APP.kanbanStageNames).length
                  ? APP.kanbanStageNames
                  : APP.stageNames;

                return orderedStageEntries(names)
                  .filter(([stage]) => !["junk", "ticket", "archive", "archiv"].includes(canonicalStage(stage)));
              }

              function extractKanbanRows(payload) {
                return Array.isArray(payload?.leads)
                  ? payload.leads
                  : Array.isArray(payload?.data)
                  ? payload.data
                  : Array.isArray(payload)
                  ? payload
                  : [];
              }

              function setColumnInitialMessage(stageKey, html) {
                const container = colContent(stageKey);
                if (!container) return;
                container.innerHTML = html;
              }

              function renderInitialColumn(stageKey, rows, payload = {}) {
                const stage = String(stageKey || "");
                const container = colContent(stage);
                const state = ensureColumnState(stage);
                if (!container) return [];

                const leads = (Array.isArray(rows) ? rows : []).map(normalizeLead);
                const frag = document.createDocumentFragment();
                const seen = new Set();
                const accepted = [];

                leads.forEach((lead) => {
                  const normalizedStage = workflowColumnKey(lead);
                  if (normalizedStage !== stage && canonicalStage(lead.stage) !== canonicalStage(stage)) return;

                  const id = cardId(lead);
                  if (!id || seen.has(id)) return;
                  seen.add(id);

                  const card = mountOrUpdateCard(stage, lead, null);
                  card.classList.add("kb-card-enter");
                  frag.appendChild(card);
                  accepted.push(lead);
                });

                container.innerHTML = "";
                if (frag.childNodes.length) {
                  container.appendChild(frag);
                } else {
                  container.innerHTML = '<div class="kb-column-empty text-muted small p-2">Keine Einträge in dieser Spalte.</div>';
                }

                const limit = getKanbanColumnLimit();
                state.limit = limit;
                state.offset = leads.length;
                state.initialized = true;
                state.hasMore = !!payload?.has_more || !!payload?.hasMore || !!payload?.limited || leads.length >= limit;

                return accepted;
              }

              function bindPerColumnLimitSelect() {
                const select = document.getElementById("kbPerColumnLimitSelect");
                if (!select || select.dataset.bound === "1") return;
                select.dataset.bound = "1";
                const saved = localStorage.getItem("kanban.column.limit");
                if (saved) select.value = saved;
                select.addEventListener("change", function () {
                  localStorage.setItem("kanban.column.limit", this.value || String(KANBAN_COLUMN_LIMIT));
                  if (typeof fetchKanbanView === "function") fetchKanbanView(State.filtersQS || buildFilterQS?.() || "");
                });
              }

              function resetColumnLoadStates(qsStr = "") {
                ColumnLoadState.filtersKey = currentFilterKey(qsStr);
                ColumnLoadState.byStage.clear();
                ColumnLoadState.loading.clear();
                ColumnLoadState.requestId++;
              }

              function visibleColumnCards(stageKey) {
                const container = colContent(stageKey);
                if (!container) return [];
                return Array.from(container.querySelectorAll(".card"));
              }

              function syncColumnOffsetsFromDom(hasPossibleMore = false) {
                orderedStageEntries(APP.kanbanStageNames || APP.stageNames).forEach(([stage]) => {
                  const state = ensureColumnState(stage);
                  state.offset = visibleColumnCards(stage).length;
                  state.initialized = true;
                  state.hasMore = hasPossibleMore || state.offset >= KANBAN_COLUMN_LIMIT;
                });
                refreshColumnLoadControls();
              }

              function ensureKanbanLimitInfo() {
                let box = document.getElementById("kbLimitInfoBox");
                if (box) return box;

                const toolbar = document.querySelector(".kanban-zoom-toolbar") || document.querySelector(".pro-tabs-topbar");
                if (!toolbar) return null;

                box = document.createElement("div");
                box.id = "kbLimitInfoBox";
                box.className = "kb-limit-info-box d-none";
                box.innerHTML = `
                  <button type="button" class="kb-limit-info-btn" id="kbLimitInfoBtn" title="Hinweis zur geladenen Datenmenge">
                    <i class="feather icon-info"></i>
                    <span id="kbLimitInfoText">Geladene Einträge</span>
                  </button>
                `;

                toolbar.insertAdjacentElement("afterbegin", box);

                if (!document.getElementById("kbLimitInfoStyles")) {
                  const style = document.createElement("style");
                  style.id = "kbLimitInfoStyles";
                  style.textContent = `
                    .kb-limit-info-box{display:flex;align-items:center;margin-right:10px}
                    .kb-limit-info-box.d-none{display:none!important}
                    .kb-limit-info-btn{border:1px solid #bfdbfe;background:#eff6ff;color:#1e3a8a;border-radius:999px;padding:7px 11px;font-size:12px;font-weight:900;display:inline-flex;align-items:center;gap:7px;box-shadow:0 8px 22px rgba(30,64,175,.10);cursor:pointer}
                    .kb-limit-info-btn:hover{background:#dbeafe;transform:translateY(-1px)}
                    .kb-load-more-wrap{padding:10px 0 12px;display:flex;justify-content:center}
                    .kb-load-more-btn{border:1px solid #dbeafe;background:#fff;color:#334155;border-radius:999px;padding:7px 12px;font-size:12px;font-weight:900;display:inline-flex;align-items:center;gap:7px;box-shadow:0 5px 16px rgba(15,23,42,.08);cursor:pointer}
                    .kb-load-more-btn:hover{background:#eef7fb;color:#0f172a}
                    .kb-load-more-btn[disabled]{opacity:.6;cursor:wait}
                    .kb-load-more-btn.is-hidden{display:none}
                    .kb-load-spinner{width:14px;height:14px;border-radius:50%;border:2px solid #cbd5e1;border-top-color:#74b2d4;animation:kbspin .75s linear infinite}
                    .card.kb-card-enter{animation:kbCardIn .28s ease-out both}
                    @keyframes kbspin{to{transform:rotate(360deg)}}
                    @keyframes kbCardIn{from{opacity:0;transform:translateY(10px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
                  `;
                  document.head.appendChild(style);
                }

                box.querySelector("#kbLimitInfoBtn")?.addEventListener("click", function () {
                  const loaded = Number(box.dataset.loaded || 0);
                  const limit = Number(box.dataset.limit || KANBAN_INITIAL_LIMIT);
                  const isLimited = box.dataset.limited === "1";

                  if (window.Swal) {
                    Swal.fire({
                      icon: "info",
                      title: "Kanban wurde begrenzt geladen",
                      html: `
                        <div style="text-align:left;line-height:1.55">
                          <p><strong>Es wurden ${loaded || limit} Einträge geladen.</strong></p>
                          <p>Damit die Kanban-Seite auf Hetzner schnell bleibt, wird die erste Ansicht begrenzt geladen.</p>
                          <p>Nutze die Filter, um Kunden, Produkt, Mitarbeiter, Zeitraum oder Status genauer einzugrenzen.</p>
                        </div>
                      `,
                      showCancelButton: true,
                      confirmButtonText: "Filter öffnen",
                      cancelButtonText: "Schließen"
                    }).then((result) => {
                      if (result.isConfirmed) openKanbanFilterDrawer();
                    });
                  } else {
                    openKanbanFilterDrawer();
                  }
                });

                featherRefreshSoon();
                return box;
              }

              function ensureKanbanColumnLimitControl() {
                let wrap = document.getElementById("kbColumnLimitControl");
                if (wrap) return wrap;

                const toolbar = document.querySelector(".kanban-zoom-toolbar") || document.querySelector(".pro-tabs-topbar");
                if (!toolbar) return null;

                wrap = document.createElement("label");
                wrap.id = "kbColumnLimitControl";
                wrap.className = "kb-column-limit-control";
                wrap.innerHTML = `
                  <span>Pro Spalte</span>
                  <select id="kbColumnLimitSelect" class="kb-column-limit-select">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="80">80</option>
                  </select>
                `;

                toolbar.insertAdjacentElement("afterbegin", wrap);

                if (!document.getElementById("kbColumnLimitStyles")) {
                  const style = document.createElement("style");
                  style.id = "kbColumnLimitStyles";
                  style.textContent = `
                    .kb-column-limit-control{display:inline-flex;align-items:center;gap:7px;margin-right:10px;border:1px solid #dbeafe;background:#fff;border-radius:999px;padding:5px 8px;color:#334155;font-size:12px;font-weight:900;box-shadow:0 8px 22px rgba(15,23,42,.08)}
                    .kb-column-limit-select{border:0;background:#eef7fb;border-radius:999px;padding:4px 8px;font-size:12px;font-weight:900;color:#0f172a;outline:0;min-width:64px}
                    .kb-column-loading-state{padding:12px;color:#64748b;font-size:12px;font-weight:800;text-align:center;background:#f8fafc;border:1px dashed #dbeafe;border-radius:14px;margin:8px}
                    .kb-column-empty-state{padding:12px;color:#94a3b8;font-size:12px;font-weight:800;text-align:center;background:#fff;border:1px dashed #e2e8f0;border-radius:14px;margin:8px}
                    .kb-load-more-wrap{padding:10px 0 12px;display:flex;justify-content:center}
                  `;
                  document.head.appendChild(style);
                }

                const select = wrap.querySelector("#kbColumnLimitSelect");
                if (select) {
                  select.value = String(KANBAN_COLUMN_LIMIT);
                  select.addEventListener("change", function () {
                    const next = Number(this.value || KANBAN_COLUMN_LIMIT_DEFAULT);
                    KANBAN_COLUMN_LIMIT = [10, 20, 30, 50, 80].includes(next) ? next : KANBAN_COLUMN_LIMIT_DEFAULT;
                    localStorage.setItem("kanban.column.limit", String(KANBAN_COLUMN_LIMIT));
                    if (typeof fetchKanbanView === "function") fetchKanbanView(State.filtersQS || "");
                  });
                }

                featherRefreshSoon();
                return wrap;
              }

              function setKanbanLimitInfo(payload, loadedCount) {
                const box = ensureKanbanLimitInfo();
                if (!box) return;

                const limit = Number(payload?.limit || KANBAN_INITIAL_LIMIT);
                const limited = !!payload?.limited || loadedCount >= limit;

                if (!limited) {
                  box.classList.add("d-none");
                  return;
                }

                box.dataset.loaded = String(loadedCount || limit);
                box.dataset.limit = String(limit);
                box.dataset.limited = "1";

                const text = box.querySelector("#kbLimitInfoText");
                if (text) text.textContent = `Es wurden ${loadedCount || limit} Einträge geladen`;

                box.classList.remove("d-none");
                featherRefreshSoon();
              }

              function openKanbanFilterDrawer() {
                const btn = document.getElementById("btnOpenDrawer");
                if (btn) {
                  btn.click();
                  return;
                }

                const drawer = document.getElementById("sideDrawer");
                const backdrop = document.getElementById("drawerBackdrop") || document.querySelector(".drawer-backdrop");
                drawer?.classList.add("open");
                backdrop?.classList.add("show");
              }

              function ensureColumnLoadButton(stageKey) {
                const container = colContent(stageKey);
                if (!container) return null;

                let wrap = container.querySelector(":scope > .kb-load-more-wrap");
                if (!wrap) {
                  wrap = document.createElement("div");
                  wrap.className = "kb-load-more-wrap";
                  wrap.innerHTML = `
                    <button type="button" class="kb-load-more-btn" data-kb-load-more-stage="${escapeHTML(stageKey)}">
                      <i class="feather icon-chevron-down"></i>
                      <span>Mehr laden</span>
                    </button>
                  `;
                  container.appendChild(wrap);

                  wrap.querySelector("button")?.addEventListener("click", function () {
                    loadMoreForColumn(stageKey);
                  });
                }

                return wrap.querySelector("button");
              }

              function setColumnLoading(stageKey, loading) {
                const btn = ensureColumnLoadButton(stageKey);
                if (!btn) return;

                btn.disabled = !!loading;
                btn.innerHTML = loading
                  ? `<span class="kb-load-spinner"></span><span>Lade weitere Einträge…</span>`
                  : `<i class="feather icon-chevron-down"></i><span>Mehr laden</span>`;

                featherRefreshSoon();
              }

              function refreshColumnLoadControls() {
                orderedStageEntries(APP.kanbanStageNames || APP.stageNames).forEach(([stage]) => {
                  const btn = ensureColumnLoadButton(stage);
                  const state = ensureColumnState(stage);
                  if (!btn) return;

                  btn.classList.toggle("is-hidden", !state.hasMore);
                  btn.disabled = ColumnLoadState.loading.has(stage);
                });
              }

              function bindColumnInfiniteLoading() {
                if (ColumnLoadState.bound) {
                  refreshColumnLoadControls();
                  return;
                }

                ColumnLoadState.bound = true;

                document.addEventListener("scroll", function (event) {
                  const container = event.target?.closest?.(".column-content");
                  if (!container) return;

                  const col = container.closest(".column");
                  const stage = col?.id;
                  if (!stage) return;

                  const state = ensureColumnState(stage);
                  if (!state.hasMore || ColumnLoadState.loading.has(stage)) return;

                  const distance = container.scrollHeight - container.scrollTop - container.clientHeight;
                  if (distance <= 80) loadMoreForColumn(stage);
                }, true);

                document.addEventListener("click", function (event) {
                  const btn = event.target.closest("[data-kb-load-more-stage]");
                  if (!btn) return;

                  event.preventDefault();
                  loadMoreForColumn(btn.dataset.kbLoadMoreStage);
                });

                refreshColumnLoadControls();
              }

              function columnRowsFromPayload(payload) {
                return Array.isArray(payload?.leads)
                  ? payload.leads
                  : Array.isArray(payload?.data)
                  ? payload.data
                  : Array.isArray(payload)
                  ? payload
                  : [];
              }

              function columnStageMatches(lead, stage) {
                const normalizedStage = workflowColumnKey(lead);
                return normalizedStage === stage || canonicalStage(lead.stage) === canonicalStage(stage);
              }

              function clearColumnContentForLoad(stage) {
                const container = colContent(stage);
                if (!container) return null;
                container.innerHTML = `<div class="kb-column-loading-state"><span class="kb-load-spinner"></span> Lade ${escapeHTML(workflowLabel(stage))} …</div>`;
                return container;
              }

              function renderColumnRows(stage, rows, append = true) {
                const leads = rows.map(normalizeLead);
                const container = colContent(stage);
                if (!container) return 0;

                container.querySelectorAll(":scope > .kb-column-loading-state, :scope > .kb-column-empty-state").forEach((el) => el.remove());

                const frag = document.createDocumentFragment();
                let added = 0;

                leads.forEach((lead) => {
                  if (!columnStageMatches(lead, stage)) return;

                  const id = cardId(lead);
                  if (id && document.getElementById(id)) return;

                  const card = mountOrUpdateCard(stage, lead, null);
                  card.classList.add("kb-card-enter");
                  frag.appendChild(card);
                  State.lastKanbanData.push(lead);
                  added++;
                });

                const wrap = container.querySelector(":scope > .kb-load-more-wrap");
                if (!append) {
                  Array.from(container.querySelectorAll(":scope > .card")).forEach((el) => el.remove());
                }

                if (frag.childNodes.length) {
                  if (wrap) container.insertBefore(frag, wrap);
                  else container.appendChild(frag);
                } else if (!append || !visibleColumnCards(stage).length) {
                  const empty = document.createElement("div");
                  empty.className = "kb-column-empty-state";
                  empty.textContent = "Keine Einträge in dieser Spalte.";
                  if (wrap) container.insertBefore(empty, wrap);
                  else container.appendChild(empty);
                }

                APP.allLeads = State.lastKanbanData;
                return added;
              }

              async function loadInitialForColumn(stageKey, signal, requestId) {
                const stage = String(stageKey || "");
                if (!stage) return { stage, rows: 0, added: 0 };

                const state = ensureColumnState(stage);
                state.offset = 0;
                state.limit = KANBAN_COLUMN_LIMIT;
                state.hasMore = true;
                state.initialized = false;

                ColumnLoadState.loading.add(stage);
                clearColumnContentForLoad(stage);
                setColumnLoading(stage, true);

                try {
                  const params = kanbanBaseParams(State.filtersQS || "");
                  params.set("stage", stage);
                  params.set("offset", "0");
                  params.set("limit", String(state.limit));

                  const payload = await safeFetchJSON(kanbanUrl(params), { signal, retries: 0 });
                  if (requestId !== ColumnLoadState.requestId) return { stage, stale: true };

                  const rows = columnRowsFromPayload(payload);
                  const added = renderColumnRows(stage, rows, false);

                  state.offset = Number(payload?.next_offset ?? rows.length ?? 0);
                  state.hasMore = !!payload?.has_more || !!payload?.hasMore || !!payload?.limited || rows.length >= state.limit;
                  state.initialized = true;

                  return { stage, rows: rows.length, added, hasMore: state.hasMore };
                } catch (error) {
                  if (error?.name !== "AbortError") {
                    const container = colContent(stage);
                    if (container) {
                      container.innerHTML = `<div class="kb-column-empty-state text-danger">${escapeHTML(error?.message || "Spalte konnte nicht geladen werden.")}</div>`;
                    }
                    console.error("Kanban column load failed", stage, error);
                  }
                  state.hasMore = false;
                  return { stage, error };
                } finally {
                  ColumnLoadState.loading.delete(stage);
                  setColumnLoading(stage, false);
                  refreshColumnLoadControls();
                }
              }

              async function loadMoreForColumn(stageKey) {
                const stage = String(stageKey || "");
                if (!stage) return;

                const state = ensureColumnState(stage);
                if (!state.hasMore || ColumnLoadState.loading.has(stage)) return;

                ColumnLoadState.loading.add(stage);
                setColumnLoading(stage, true);

                try {
                  const params = kanbanBaseParams(State.filtersQS || "");
                  params.set("stage", stage);
                  params.set("offset", String(state.offset || visibleColumnCards(stage).length || 0));
                  params.set("limit", String(state.limit || getKanbanColumnLimit()));

                  const payload = await safeFetchJSON(kanbanUrl(params), { retries: 0 });
                  const rows = columnRowsFromPayload(payload);
                  const added = renderColumnRows(stage, rows, true);

                  state.offset = Number(payload?.next_offset ?? ((state.offset || 0) + rows.length));
                  state.hasMore = !!payload?.has_more || !!payload?.hasMore || !!payload?.limited || rows.length >= (state.limit || getKanbanColumnLimit());

                  if (!rows.length || (added === 0 && rows.length < (state.limit || KANBAN_COLUMN_LIMIT))) {
                    state.hasMore = false;
                  }

                  updateCounts();
                  featherRefreshSoon();
                  updateNoteBadgesForVisibleCards();
                  enforceActionVisibility();
                  refreshVisibleStageDurations();
                } catch (error) {
                  if (error?.name !== "AbortError") {
                    Swal.fire("Fehler", error?.message || "Weitere Einträge konnten nicht geladen werden.", "error");
                  }
                } finally {
                  ColumnLoadState.loading.delete(stage);
                  setColumnLoading(stage, false);
                  refreshColumnLoadControls();
                }
              }

              function fetchKanbanView(qsStr) {
                ensureLoadedMap();

                const signal = cancel("kanban");
                const board = qs("#kanban");
                const firstLoad = !State.loaded.kanban;

                resetColumnLoadStates(qsStr || "");
                ensureColumns();
                bindPerColumnLimitSelect();

                const stageEntries = kanbanStageEntriesForLoad();
                const limit = getKanbanColumnLimit();

                if (board && firstLoad) {
                  stageEntries.forEach(([stage]) => {
                    setColumnInitialMessage(stage, '<div class="p-2 text-muted small">Lade Spalte…</div>');
                  });
                } else {
                  stageEntries.forEach(([stage]) => {
                    setColumnInitialMessage(stage, '<div class="p-2 text-muted small">Aktualisiere Spalte…</div>');
                  });
                }

                const requests = stageEntries.map(([stage]) => {
                  const params = kanbanBaseParams(qsStr || "");
                  params.set("stage", stage);
                  params.set("limit", String(limit));
                  params.set("offset", "0");

                  const state = ensureColumnState(stage);
                  state.limit = limit;
                  state.offset = 0;
                  state.hasMore = true;
                  state.initialized = false;
                  ColumnLoadState.loading.add(stage);
                  setColumnLoading(stage, true);

                  const url = kanbanUrl(params);

                  return safeFetchJSON(url, { signal, retries: 0 })
                    .then((payload) => ({ stage, payload, rows: extractKanbanRows(payload), url }))
                    .catch((error) => ({ stage, error, url }));
                });

                return Promise.all(requests)
                  .then((results) => {
                    const all = [];
                    let totalFromPayload = 0;
                    let hasErrors = false;

                    results.forEach((result) => {
                      const stage = result.stage;
                      ColumnLoadState.loading.delete(stage);

                      if (result.error) {
                        const isAbort =
                          result.error?.name === "AbortError" ||
                          String(result.error?.message || "").toLowerCase().includes("aborted");

                        // Important: aborted requests are normal when a newer Kanban load starts.
                        // Do not show them as failed columns.
                        if (isAbort) {
                          return;
                        }

                        hasErrors = true;

                        const payload = result.error?.payload || {};
                        const realMessage =
                          payload.error ||
                          payload.message ||
                          result.error?.message ||
                          "Spalte konnte nicht geladen werden.";

                        console.error("[Kanban] Column load failed", {
                          stage,
                          url: result.url,
                          status: result.error?.status || null,
                          message: realMessage,
                          payload,
                        });

                        setColumnInitialMessage(stage, `
                          <div class="p-2 text-danger small">
                            <strong>${escapeHTML(stage)}:</strong> ${escapeHTML(realMessage)}
                            <div class="mt-1"><code>${escapeHTML(result.url || "")}</code></div>
                          </div>
                        `);

                        const state = ensureColumnState(stage);
                        state.hasMore = false;
                        state.initialized = true;

                        return;
                      } 

                      const accepted = renderInitialColumn(stage, result.rows, result.payload);
                      all.push(...accepted);
                      const payloadTotal = Number(result.payload?.total ?? result.payload?.pagination?.total ?? result.payload?.meta?.total ?? 0);
                      if (payloadTotal > 0) totalFromPayload += payloadTotal;
                    });

                    State.lastKanbanData = all;
                    APP.allLeads = State.lastKanbanData;
                    ensureLoadedMap();
                    State.loaded.kanban = true;

                    const payloadForCount = totalFromPayload > 0 ? { total: totalFromPayload } : { total: all.length };
                    syncTabCountsFromKanban(State.lastKanbanData, payloadForCount);
                    setKanbanLimitInfo({ limit: limit, limited: true }, State.lastKanbanData.length);
                    bindColumnInfiniteLoading();
                    refreshColumnLoadControls();
                    updateCounts();
                    featherRefreshSoon();
                    updateNoteBadgesForVisibleCards();
                    enforceActionVisibility();
                    refreshVisibleStageDurations();

                    if (hasErrors && !all.length) {
                      const failed = results.filter((r) => r.error);
                      const html = failed.slice(0, 6).map((r) => {
                        const payload = r.error?.payload || {};
                        const msg = payload.error || payload.message || r.error?.message || 'Unbekannter Fehler';
                        return `
                          <div style="text-align:left;margin-bottom:8px;padding:8px;border:1px solid #fee2e2;border-radius:10px;background:#fff7f7;">
                            <div><strong>Stage:</strong> ${escapeHTML(r.stage)}</div>
                            <div><strong>Status:</strong> ${escapeHTML(r.error?.status || '-')}</div>
                            <div><strong>Fehler:</strong> ${escapeHTML(msg)}</div>
                            <div style="font-size:11px;word-break:break-all;"><strong>URL:</strong> ${escapeHTML(r.url || '')}</div>
                          </div>
                        `;
                      }).join('');

                      Swal.fire({
                        icon: "error",
                        title: "Kanban-Spalten konnten nicht geladen werden",
                        html: html || "Alle Spalten-Requests sind fehlgeschlagen.",
                        width: 760
                      });
                    }
                  })
                  .catch((e) => {
                    if (e?.name !== "AbortError") Swal.fire("Fehler", e?.message || "Fehler", "error");
                  });
              }

              function fetchListView(qsStr) {
                ensureLoadedMap();

                const signal = cancel("list");
                const tbody = qs("#kanbanTableBody");
                if (tbody && !State.loaded.list) {
                  tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Lade Liste…</td></tr>';
                }

                return safeFetchJSON(`${APP.endpoints.listSearch}${qsStr ? `?${qsStr}` : ""}`, { signal, retries: 0 })
                  .then((payload) => {
                    ensureLoadedMap();
                    State.loaded.list = true;

                    const leads = Array.isArray(payload?.leads) ? payload.leads : Array.isArray(payload?.data) ? payload.data : [];
                    updateListView(leads, payload);

                    renderPagination(payload.pagination || payload.meta || payload);
                    syncTabCountsFromListPayload(payload);
                  })
                  .catch((e) => {
                    if (e?.name === "AbortError") return;
                    Swal.fire("Fehler", e?.message || "Serverfehler.", "error");
                    updateListView([], {});
                    renderPagination(null);
                  });
              }

              // Public fetch hooks used by the workflow switch (Unternehmen/Produkt).
              window.LeadUIFetchKanban = fetchKanbanView;
              window.fetchKanbanView = fetchKanbanView;
              window.LeadUIFetchList = fetchListView;
              let kbReloadTimer = null;
              let kbLastReloadQS = "";

              window.KanbanReloadOnce = function (qsStr = "") {
                kbLastReloadQS = String(qsStr || "");

                clearTimeout(kbReloadTimer);

                kbReloadTimer = setTimeout(function () {
                  if (typeof window.LeadUIFetchKanban === "function") {
                    window.LeadUIFetchKanban(kbLastReloadQS);
                  }
                }, 180);
              };

              /* -------------------------------------------------------------------------- */
              /* Partials: Ticket & Investment tabs                                          */
              /* -------------------------------------------------------------------------- */
              partials.fetchTicketsTab = async function (qsStr = "") {
                const pane = qs("#ticket");
                if (!pane) return;

                const url = `${APP.endpoints.tickets}${qsStr ? `?${qsStr}` : ""}`;

                try {
                  const res = await fetch(url, {
                    headers: { Accept: "text/html", "X-Requested-With": "XMLHttpRequest" },
                    credentials: "same-origin",
                  });

                  const html = await res.text();
                  pane.innerHTML = html;

                  const totalNode = pane.querySelector("[data-ticket-total]") || pane.querySelector("[data-total]");
                  const total = totalNode
                    ? toInt(
                        totalNode.getAttribute("data-ticket-total") ||
                          totalNode.getAttribute("data-total") ||
                          totalNode.dataset.ticketTotal ||
                          totalNode.dataset.total ||
                          0,
                        0
                      )
                    : 0;

                  const badge = qs("#tabCountTicket");
                  if (badge) badge.textContent = String(total);
                } catch (e) {
                  console.error("Ticket partial load failed:", e);
                }
              };



              function refreshArchiveAndJunk(qsStr) {
                partials.fetchJunkTab?.(qsStr);
                partials.fetchTicketsTab?.(qsStr);
              }

              /* -------------------------------------------------------------------------- */
              /* Unified run state prompt                                                    */
              /* -------------------------------------------------------------------------- */
              async function promptRunReason(state) {
                const label =
                  state === "playing" ? "Start" : state === "paused" ? "Pause" : state === "stopped" ? "Stopp" : state;

                const { value: reason, isConfirmed } = await Swal.fire({
                  title: `Grund für ${label}`,
                  input: "textarea",
                  showCancelButton: true,
                  confirmButtonText: "Speichern",
                  inputValidator: (v) => (!v?.trim() ? "Bitte Grund eingeben" : undefined),
                });

                if (!isConfirmed) return null;
                return String(reason || "").trim();
              }

              /* -------------------------------------------------------------------------- */
              /* Click handlers (Unified: List + Kanban)                                     */
              /* -------------------------------------------------------------------------- */
            /* -------------------------------------------------------------------------- */
          /* Click handlers (Unified: List + Kanban)                                    */
          /* -------------------------------------------------------------------------- */
          document.addEventListener("click", async (e) => {
            // 1. Find the button (works for both direct clicks and nested icon clicks)
            const actBtn = e.target.closest("[data-act], [data-run]");
            if (!actBtn) return;

            // 2. Identify if we are in Kanban or List
            const card = actBtn.closest(".card"); // Kanban
            const row = actBtn.closest("tr.list-row-item"); // List

            // 3. Handle the 'Run' (Play/Pause/Stop) logic
            if (actBtn.dataset.run) {
                e.preventDefault();
                e.stopPropagation();

                const state = actBtn.dataset.run;
                const target = card || row;
                const lpId = target.dataset.leadProductId;

                if (!lpId) {
                    console.error("Lead Product ID missing on target element");
                    return;
                }

                // Optional: Ask for a reason (matches your controller logic)
                const { value: reason } = await Swal.fire({
                    title: `Grund für ${state}`,
                    input: 'textarea',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern'
                });

                if (reason === undefined) return; // User cancelled

                try {
                    const res = await window.LeadUI.net.postJSON(`/lead-product/progress/${lpId}/${state}`, {
                        reason: reason
                    });

                    if (res.success) {
                        // Update the UI immediately
                        if (card) window.LeadUI.kanban.applyRunStateUI(card, state);
                        window.LeadUI.silentRefreshBoth(); // Sync the other view
                        Swal.fire("Aktualisiert", "", "success");
                    }
                } catch (err) {
                    Swal.fire("Fehler", "Status konnte nicht geändert werden", "error");
                }
                return;
            }

            // 👇 --- ADD THIS NEW BLOCK BELOW YOUR RUN LOGIC --- 👇

            // 4. Handle 'Archive' and 'Delete' (Junk) logic
            if (actBtn.dataset.act === "archive" || actBtn.dataset.act === "delete") {
                e.preventDefault();
                e.stopPropagation();

                const target = card || row;
                if (!target) return;

                const currentStage = target.dataset.stage;
                const newStage = actBtn.dataset.act === "archive" ? "archive" : "junk";

                // Extract current team IDs to prefill the SweetAlert Select2
                let currentTeamIds = [];
                try {
                    currentTeamIds = JSON.parse(target.dataset.teamIds || "[]").map(x => Number(x));
                } catch(err) {}

                // Prompt user with your existing SweetAlert logic
                const stageTeams = safeJSON(target.dataset.teamAssignments || "[]", []);
                const confirm = await confirmStageChange(newStage, currentStage, currentTeamIds, { stageTeams, productId: target.dataset.productId });
                if (!confirm.ok) return;

                try {
                    const { customerId, alternativeId, productId, leadProductId } = target.dataset;

                    const stageResponse = await applyStageChange({
                        customerId,
                        alternativeId,
                        productId,
                        leadProductId,
                        newStage,
                        noteHTML: confirm.reasonHTML,
                        teams: confirm.teams,
                    });

                    if (confirm.reminder?.enabled) {
                        await createReminderFromStageChange({ leadProductId, customerId, alternativeId, productId }, confirm.reminder);
                        if (window.preloadLeadReminderSummaries) window.setTimeout(window.preloadLeadReminderSummaries, 350);
                    }

                    if (stageResponse?.final?.team_assignments && target) {
                        target.dataset.teamAssignments = JSON.stringify(stageResponse.final.team_assignments);
                    }

                    // Update DOM gracefully
                    if (card) {
                        if (newStage === "junk" || stageFilterExcludes(newStage)) {
                            card.remove();
                        } else {
                            moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
                            enforceActionVisibility(card);
                        }
                    } else if (row) {
                        if (newStage === "junk" || stageFilterExcludes(newStage)) {
                            row.remove();
                        } else {
                            row.dataset.stage = newStage;
                        }
                    }

                    if (card) {
                        kbRealtimeAfterMove(card, currentStage, newStage, stageResponse);
                    } else if (row) {
                        kbRecentAddStageMove({
                            card: row,
                            oldStage: currentStage,
                            newStage,
                            response: stageResponse,
                            openPanel: false
                        });
                    }

                    updateCounts();
                    featherRefreshSoon();

                    /*
                     * Do not call silentRefreshBoth() here.
                     * The local DOM is already updated; a full refresh hides open forms/modals.
                     */

                    Swal.fire({
                        icon: "success",
                        title: newStage === "archive" ? "Archiviert" : "In Junk verschoben",
                        text: newStage === "archive" ? "Erfolgreich ins Archiv verschoben." : "Eintrag wurde aussortiert.",
                        timer: 1200,
                        showConfirmButton: false
                    });
                } catch (err) {
                    Swal.fire("Fehler", err?.message || "Serverfehler beim Verschieben.", "error");
                }
                return;
            }
          });

              /* -------------------------------------------------------------------------- */
              /* Kanban: click selection + dragstart delegation                              */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("click", (e) => {
                const card = e.target.closest("#kanban .card");
                if (!card) return;

                // Avoid selecting when clicking action buttons/links/inputs
                if (e.target.closest(".card-actions, button, a, input, select, textarea")) return;

                selectCard(card, e);
              });

              document.addEventListener("dragstart", (e) => {
                const card = e.target.closest("#kanban .card");
                if (!card) return;
                onKanbanDragStart(e, card);
              });

              // Enable drop only on columns (and avoid "open in new tab" elsewhere)
              document.addEventListener("dragover", (e) => {
                if (!e.dataTransfer) return;

                // Only handle our own DND type
                if (!Array.from(e.dataTransfer.types || []).includes(window.KB_DND_MIME)) return;

                const col = e.target.closest(".column");
                document.querySelectorAll("#kanban .column.kb-drop-target").forEach((c) => {
                  if (c !== col) c.classList.remove("kb-drop-target");
                });
                if (col) {
                  e.preventDefault();
                  col.classList.add("kb-drop-target");
                }
              });

              document.addEventListener("dragleave", (e) => {
                const col = e.target.closest?.(".column");
                if (!col) return;
                const next = e.relatedTarget;
                if (!next || !col.contains(next)) col.classList.remove("kb-drop-target");
              });

              document.addEventListener("dragend", () => {
                document.querySelectorAll("#kanban .column.kb-drop-target").forEach((c) => c.classList.remove("kb-drop-target"));
              });

              document.addEventListener(
                "drop",
                (e) => {
                  if (!e.dataTransfer) return;
                  if (!Array.from(e.dataTransfer.types || []).includes(window.KB_DND_MIME)) return;

                  const col = e.target.closest(".column");
                  if (!col) {
                    // Prevent browser from navigating when dropping our internal drag payload
                    e.preventDefault();
                    return;
                  }

                  document.querySelectorAll("#kanban .column.kb-drop-target").forEach((c) => c.classList.remove("kb-drop-target"));
                  onKanbanDrop(e);
                },
                true
              );

              /* -------------------------------------------------------------------------- */
              /* List: stage select change                                                   */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("change", async (e) => {
                const sel = e.target.closest("select.stage-select");
                if (!sel) return;

                const row = sel.closest("tr.list-row-item");
                if (!row) return;

                const newStage = sel.value;

                // old stage from defaultSelected (Laravel often renders the current one)
                const prevIndex = Array.from(sel.options).findIndex((o) => o.defaultSelected);
                const oldStage = prevIndex >= 0 ? canonicalStage(sel.options[prevIndex].value) : canonicalStage(row.dataset.stage);

                if (newStage === oldStage) return;

                // 👇 ADDED PAUSE/STOP CHECK BLOCK 👇
                const runState = row.dataset.runState || 'playing';
                if (runState === 'paused' || runState === 'stopped') {
                    // Revert the dropdown selection visually immediately
                    sel.selectedIndex = Math.max(0, prevIndex);

                    let reason = "Kein Grund angegeben.";
                    try {
                        const history = JSON.parse(row.dataset.stageHistory || "[]");
                        if (Array.isArray(history) && history.length > 0) {
                            // Get the most recent entry
                            const latest = history[history.length - 1];
                            if (latest && latest.description) {
                                reason = latest.description;
                            }
                        }
                    } catch(e) {
                        console.warn("Could not parse stage_history", e);
                    }

                    const stateDe = runState === 'paused' ? 'pausiert' : 'gestoppt';
                    Swal.fire({
                        icon: "warning",
                        title: "Aktion nicht möglich",
                        html: `Dieser Eintrag ist momentan <b>${stateDe}</b> und kann nicht verschoben werden.<br><br><b>Grund:</b> ${esc(reason)}`
                    });
                    return; // Stop execution!
                }
                // 👆 END PAUSE/STOP CHECK BLOCK 👆

                const customerId = row.dataset.customerId;
                const alternativeId = row.dataset.alternativeId;
                const productId = row.dataset.productId;
                const leadProductId = sel.dataset.id || row.dataset.leadProductId || row.id?.split("-")[1];

                // teams from row if you ever store them (optional)
                const currentTeamIds = Array.isArray(safeJSON(row.dataset.teamIds || "[]", []))
                  ? safeJSON(row.dataset.teamIds || "[]", [])
                  : [];

                try {
                  const confirm = await confirmStageChange(newStage, oldStage, currentTeamIds, {
                    productId: row.dataset.productId,
                    productStageId: row.dataset.productStageId,
                    currentProductStageId: row.dataset.productStageId,
                    stageTeams: safeJSON(row.dataset.teamAssignments || "[]", []),
                  });
                  if (!confirm.ok) {
                    sel.selectedIndex = Math.max(0, prevIndex);
                    return;
                  }

                  const stageResponse = await applyStageChange({
                    customerId,
                    alternativeId,
                    productId,
                    leadProductId,
                    newStage,
                    noteHTML: confirm.reasonHTML,
                    teams: confirm.teams,
                    mode: confirm.mode || "company",
                    companyStageKey: confirm.companyStageKey || newStage,
                    productStageId: confirm.productStageId || null,
                    productTaskPhaseId: confirm.productTaskPhaseId || null,
                  });

                  if (confirm.reminder?.enabled) {
                    await createReminderFromStageChange({ leadProductId, customerId, alternativeId, productId }, confirm.reminder);
                    if (window.preloadLeadReminderSummaries) window.setTimeout(window.preloadLeadReminderSummaries, 350);
                  }

                  // Update defaultSelected to keep oldStage detection correct next time.
                  sel.querySelectorAll("option").forEach((o) => (o.defaultSelected = false));
                  if (sel.options[sel.selectedIndex]) sel.options[sel.selectedIndex].defaultSelected = true;

                  const finalColumnStage = (confirm.mode === "product" || APP.stageWorkflow?.mode === "product")
                    ? `product_stage_${stageResponse?.lead?.product_stage_id || confirm.productStageId || workflowStageIdFromKey(newStage)}`
                    : canonicalStage(stageResponse?.stage || stageResponse?.lead?.status || confirm.companyStageKey || newStage);

                  // Remove list rows only when the active filter excludes the new stage.
                  if (stageFilterExcludes(finalColumnStage)) {
                    const feedRow = row.nextElementSibling?.classList?.contains("list-feed-row") ? row.nextElementSibling : null;
                    row.remove();
                    feedRow?.remove?.();
                  } else {
                    row.dataset.stage = canonicalStage(finalColumnStage);
                    row.dataset.companyStage = stageResponse?.lead?.status || confirm.companyStageKey || row.dataset.companyStage || "";
                    row.dataset.productStageId = stageResponse?.lead?.product_stage_id || confirm.productStageId || row.dataset.productStageId || "";
                    row.dataset.productTaskPhaseId = stageResponse?.lead?.product_task_phase_id || confirm.productTaskPhaseId || row.dataset.productTaskPhaseId || "";
                  }

                  // Update Kanban card if it is visible. Do not reload the whole Kanban.
                  const card =
                    qs(`#card-${CSS.escape(String(leadProductId).replace(/^card-/, ""))}`) ||
                    qs(`#${CSS.escape(String(leadProductId))}`) ||
                    qs(`.card[data-lead-product-id="${CSS.escape(String(leadProductId))}"]`) ||
                    qs(`.card[data-lead-product-list-id="${CSS.escape(String(leadProductId))}"]`);

                  if (card) {
                    card.classList.add("kb-live-moving");

                    moveOrRefreshKanbanCard({
                      newStage: finalColumnStage,
                      cardFromDOM: card
                    });

                    kbRealtimeAfterMove(card, oldStage, finalColumnStage, stageResponse);
                    enforceActionVisibility(card);
                  } else {
                    kbRecentAddStageMove({
                      card: row,
                      oldStage,
                      newStage: finalColumnStage,
                      response: stageResponse,
                      openPanel: false
                    });
                  }

                  updateCounts();
                  featherRefreshSoon();

                  Swal.fire({
                    icon: "success",
                    title: "Geändert",
                    text: "Phase aktualisiert und markiert.",
                    timer: 950,
                    showConfirmButton: false
                  });
                } catch (err) {
                  sel.selectedIndex = Math.max(0, prevIndex);
                  Swal.fire("Fehler", err?.message || "Serverfehler.", "error");
                }
              });
              document.addEventListener("click", (e) => {
                const btn = e.target.closest('tr.list-row-item [data-menu="product-stage-info"]');
                if (!btn) return;
                const row = btn.closest("tr.list-row-item");
                if (!row) return;
                showProductStageInfoFromElement(row);
              });

              /* -------------------------------------------------------------------------- */
              /* Sorting + pagination clicks                                                 */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("click", (e) => {
                const th = e.target.closest("#profile th.sortable");
                if (!th) return;

                const key = th.dataset.sort;
                if (!key) return;

                State.sort = State.sort?.key === key
                  ? { key, dir: State.sort.dir === "asc" ? "desc" : "asc" }
                  : { key, dir: "asc" };

                qsa("#profile th.sortable").forEach((h) => h.classList.remove("active", "desc"));
                th.classList.add("active");
                if (State.sort.dir === "desc") th.classList.add("desc");

                State.page = 1;
                State.filtersQS = filters.buildFilterQS();
                saveToLocal();
                syncURL();

                fetchListView(addPage(State.filtersQS, State.page));
                if (isKanbanActive()) fetchKanbanView(State.filtersQS);
              });

              document.addEventListener("click", (e) => {
                const a = e.target.closest("#listPagination a.page-link[data-page]");
                if (!a) return;

                e.preventDefault();
                const p = toInt(a.getAttribute("data-page"), 1);
                State.page = p;

                saveToLocal();
                syncURL();

                fetchListView(addPage(State.filtersQS, State.page));
              });

              /* -------------------------------------------------------------------------- */
              /* Tabs                                                                        */
              /* -------------------------------------------------------------------------- */
              if (window.jQuery) {
                jQuery('a[data-toggle="tab"][href="#home"]').on("shown.bs.tab", () => {
                  ensureColumns();
                  renderKanbanDiff(State.lastKanbanData || []);
                  featherRefreshSoon();
                  enforceActionVisibility();
                });

                jQuery('a[data-toggle="tab"][href="#junk"]').on("shown.bs.tab", () => {
                  partials.fetchJunkTab?.(State.filtersQS);
                });

              }

              document.addEventListener("shown.bs.tab", (e) => {
                const trg = e.target?.getAttribute("href") || "";
                if (trg === "#ticket") {
                  const qsStr = filters.buildFilterQS();
                  partials.fetchTicketsTab?.(qsStr);
                }
              });

              /* -------------------------------------------------------------------------- */
              /* Summary cards + filter buttons                                              */
              /* -------------------------------------------------------------------------- */
              function setSummaryActive(id) {
                qsa(".summary-card").forEach((c) => c.classList.remove("active"));
                if (id) qs("#" + id)?.classList.add("active");
              }

              function applyStatusGroup(g, cardId) {
                State.statusGroup = g;
                State.page = 1;

                State.filtersQS = filters.buildFilterQS();
                saveToLocal();
                syncURL();

                const withPage = addPage(State.filtersQS, State.page);

                fetchListView(withPage);
                fetchKanbanView(State.filtersQS);
                refreshArchiveAndJunk(State.filtersQS);

                setSummaryActive(cardId || null);
                filters.updateFilterBadges?.();
              }

              qs("#cardOffen")?.addEventListener("click", () => applyStatusGroup("offen", "cardOffen"));
              qs("#cardZusage")?.addEventListener("click", () => applyStatusGroup("zusage", "cardZusage"));
              qs("#cardAbsage")?.addEventListener("click", () => applyStatusGroup("absage", "cardAbsage"));

              qs("#btnApplyFilters")?.addEventListener("click", () => {
                State.page = 1;
                State.filtersQS = filters.buildFilterQS();
                State.lastAppliedQS = State.filtersQS;

                saveToLocal();
                syncURL();

                const withPage = addPage(State.filtersQS, State.page);

                fetchListView(withPage);
                fetchKanbanView(State.filtersQS);
                refreshArchiveAndJunk(State.filtersQS);

                partials.fetchTicketsTab?.(State.filtersQS);

                closeOverlays();
              });

              qs("#btnClearFilters")?.addEventListener("click", () => {
                const form = qs("#kanbanFilterForm");
                if (!form) return;

                form.reset();
                if (window.jQuery) window.jQuery(form).find(".select2").val(null).trigger("change");

                State.statusGroup = null;
                setSummaryActive(null);

                State.page = 1;
                State.filtersQS = filters.buildFilterQS();

                saveToLocal();
                syncURL();

                filters.updateFilterBadges?.();

                const withPage = addPage(State.filtersQS, State.page);

                fetchListView(withPage);
                fetchKanbanView(State.filtersQS);
                refreshArchiveAndJunk(State.filtersQS); 
                partials.fetchTicketsTab?.(State.filtersQS);
              });

              /* -------------------------------------------------------------------------- */
              /* LiveFeed row click                                                          */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("click", (e) => {
                const row = e.target.closest("#kanbanTableBody tr.list-row-item");
                if (!row) return;
                if (e.target.closest("button, a, select, input, textarea")) return;

                if (liveFeed && typeof liveFeed.loadForCard === "function") liveFeed.loadForCard(row);
              });

              /* -------------------------------------------------------------------------- */
              /* Keyboard                                                                     */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("keydown", (e) => {
                if (e.ctrlKey && e.key.toLowerCase() === "f") {
                  e.preventDefault();
                  qs("#btnOpenDrawer")?.click();
                }
                if (e.key === "Escape") closeOverlays();
              });

              /* -------------------------------------------------------------------------- */
              /* Silent refresh (public)                                                     */
              /* -------------------------------------------------------------------------- */
              function silentRefreshBoth() {
                const qsStr = State.filtersQS || "";
                fetchListView(addPage(qsStr, State.page || 1));
                fetchKanbanView(qsStr);
                partials.fetchTicketsTab?.(qsStr);
              }
              window.LeadUI.silentRefreshBoth = silentRefreshBoth;

              /* -------------------------------------------------------------------------- */
              /* Boot                                                                         */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("DOMContentLoaded", () => {
                featherRefreshSoon();
                filters.initSelect2?.();
                filters.updateFilterBadges?.();

                initFromURL();
                if (!location.search) restoreFromLocal();

                State.filtersQS = filters.buildFilterQS();
                saveToLocal();
                syncURL();

                ensureLoadedMap();
                State.loaded.kanban = false;
                State.loaded.list = false;

                // initial loads
                fetchListView(addPage(State.filtersQS, State.page || 1));
                fetchKanbanView(State.filtersQS);

                // side tabs initial refresh
                refreshArchiveAndJunk(State.filtersQS);
              });
            })();
          

/* ===================== Extracted inline script block #15 ===================== */
            (function() {
                "use strict";

                document.addEventListener("DOMContentLoaded", () => {
                    // Function to toggle column visibility
                    function toggleColumn(stageId, isVisible) {
                        const col = document.getElementById(stageId);
                        if (!col) return;

                        if (isVisible) {
                            // We use 'flex' because your .column class likely uses display:flex
                            col.style.display = 'flex'; 
                            col.classList.remove('d-none');
                        } else {
                            col.style.display = 'none';
                            col.classList.add('d-none');
                        }
                    }

                    // 1. Bind Click Events to Checkboxes
                    const toggles = document.querySelectorAll('.col-toggle-checkbox');
                    toggles.forEach(chk => {
                        // Initial check to sync JS with HTML state
                        // (Optional, but good if you have cached values)

                        chk.addEventListener('change', () => {
                            toggleColumn(chk.value, chk.checked);
                        });
                    });

                    // 2. Patch Kanban Renderer 
                    // This ensures that if the board re-renders (e.g. after a search),
                    // we re-apply the visibility rules based on the checkboxes.
                    if (window.LeadUI && window.LeadUI.kanban) {
                        const originalEnsureColumns = window.LeadUI.kanban.ensureColumns;

                        window.LeadUI.kanban.ensureColumns = function() {
                            originalEnsureColumns(); // Let the core create the columns

                            // Immediately apply visibility based on current checkbox state
                            document.querySelectorAll('.col-toggle-checkbox').forEach(chk => {
                                toggleColumn(chk.value, chk.checked);
                            });
                        };
                    }
                });
            })();
          

/* ===================== Extracted inline script block #16 ===================== */
              (function(){
                "use strict";

              /* Maps */
                const DATE_FMT = { hour:'2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'numeric' };

                /* ✅ German labels for your stages */
                const LABEL = (s) => ({
                  // your set
                  lead:      'Lead',        // or 'Interessent'
                  offer:     'Angebot',
                  follow_up: 'Nachfassen',
                  accepted:  'Annehmen',
                  deal:      'Auftrag',
                  project:   'Projekt',     // or 'Montage'
                  junk:      'Aussortiert',
                  canceled:  'Abgebrochen', // or 'Storniert'
                  ticket:    'Ticket',
                  pause:     'Pausiert',

                  // optional extras (kept for safety; remove if unused)
                  completed: 'Abgeschlossen',
                  qualify:   'Qualifizierung',
                  negotiation:'Verhandlung',
                  won:       'Gewonnen',
                  lost:      'Verloren',
                  maintenance:'Wartung',
                  repair:    'Reparatur',
                  planning:  'Planung',
                  complete:  'Komplett'
                }[String(s||'').toLowerCase()] || (s ? String(s) : 'Unbekannt'));

                /* 🎨 Badge classes per stage (lh- namespaced) */
                const BADGE = (s) => ({
                  lead:      'lh-badge lh-badge--secondary',
                  offer:     'lh-badge lh-badge--info',
                  follow_up: 'lh-badge lh-badge--warning',
                  accepted:  'lh-badge lh-badge--success',
                  deal:      'lh-badge lh-badge--primary',
                  project:   'lh-badge lh-badge--primary',
                  completed: 'lh-badge lh-badge--success',
                  junk:      'lh-badge lh-badge--secondary',
                  canceled:  'lh-badge lh-badge--danger',
                  ticket:    'lh-badge lh-badge--secondary',
                  pause:     'lh-badge lh-badge--warning',

                  // optional extras
                  qualify:    'lh-badge lh-badge--secondary',
                  negotiation:'lh-badge lh-badge--warning',
                  won:        'lh-badge lh-badge--success',
                  lost:       'lh-badge lh-badge--danger',
                  maintenance:'lh-badge lh-badge--secondary',
                  repair:     'lh-badge lh-badge--secondary',
                  planning:   'lh-badge lh-badge--secondary',
                  complete:   'lh-badge lh-badge--primary'
                }[String(s||'').toLowerCase()] || 'lh-badge');


                const ICONS = {
                  lead:`<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M3 11h18v2H3z"/></svg>`,
                  qualify:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M9 16.2l-3.5-3.5 1.4-1.4L9 13.4l7.1-7.1 1.4 1.41z"/></svg>`,
                  offer:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 6h16v12H4zM6 8h12v2H6z"/></svg>`,
                  negotiation:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 4h10v6H4zM14 10l6 4-6 4z"/></svg>`,
                  won:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24 7.46 13.97 5.82 21z"/></svg>`,
                  lost:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L10.59 13.4l-6.3 6.3L2.88 18.3l6.3-6.29-6.3-6.3L4.3 4.29l6.29 6.3 6.3-6.3z"/></svg>`
                };

                /* DOM */
                const root = document.getElementById('lh-drawer');
                const panel = root?.querySelector('.lh-panel');
                const title = document.getElementById('lh-title-text');
                const tl    = document.getElementById('lh-timeline');
                const acts  = document.getElementById('lh-activities');
                if (!root || !panel || !title || !tl || !acts) return;

                /* Drawer controls */
                const open = () => { root.setAttribute('aria-hidden','false'); panel.focus({preventScroll:true}); document.body.style.overflow='hidden'; };
                const close = () => { root.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };
                document.addEventListener('click', e => { if (e.target.closest('[data-lh-close]')) close(); });
                document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

                /* Helpers */
                const esc = s => (s==null?'':String(s)).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

                let apAutocomplete = null;

                function initAddressAutocomplete(){
                  const input = qs('#ap-full_address');
                  if (!input) return;
                  if (!window.google || !google.maps || !google.maps.places) return;

                  if (apAutocomplete) return; // only once

                  apAutocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['geocode'],
                    componentRestrictions: { country: 'de' }
                  });

                  apAutocomplete.addListener('place_changed', () => {
                    const place = apAutocomplete.getPlace();
                    if (!place || !place.geometry) return;

                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    qs('#ap-latitude').value  = lat;
                    qs('#ap-longitude').value = lng;

                    let street = '', streetNo = '', postcode = '', city = '';

                    (place.address_components || []).forEach(c => {
                      const types = c.types || [];
                      if (types.includes('route'))          street   = c.long_name;
                      if (types.includes('street_number'))  streetNo = c.long_name;
                      if (types.includes('postal_code'))    postcode = c.long_name;
                      if (types.includes('locality'))       city     = c.long_name;
                      if (!city && types.includes('postal_town')) city = c.long_name;
                    });

                    const streetField   = qs('#ap-street');
                    const postcodeField = qs('#ap-postcode');
                    const cityField     = qs('#ap-city');

                    if (streetField && !streetField.value)
                      streetField.value = [street, streetNo].filter(Boolean).join(' ');
                    if (postcodeField && !postcodeField.value)
                      postcodeField.value = postcode;
                    if (cityField && !cityField.value)
                      cityField.value = city;
                  });
                }
                window.initAddressAutocomplete = initAddressAutocomplete;

                const fmt = s => s ? new Date(String(s).replace(' ','T')).toLocaleString('de-DE', DATE_FMT) : '';

                function skeleton(){
                  title.textContent = 'Verlauf wird geladen …';
                  tl.innerHTML = `
                    <li class="lh-item">
                      <div class="lh-icowrap"><div class="lh-ico"></div></div>
                      <div class="lh-content">
                        <div class="lh-skel" style="width:55%"></div>
                        <div class="lh-skel" style="width:35%"></div>
                        <div class="lh-skel" style="width:80%"></div>
                      </div>
                    </li>`;
                  acts.innerHTML = `
                    <div class="lh-card">
                      <div class="lh-skel" style="width:60%"></div>
                      <div class="lh-skel" style="width:40%"></div>
                      <div class="lh-skel" style="width:85%"></div>
                    </div>`;
                }

                function render(data){
                  title.textContent = 'Verlauf – ' + (data.customerName || '');

                  // Timeline
                  tl.innerHTML = (data.timeline?.length ? data.timeline : []).map(t => {
                    const key = String(t.to_stage||'').toLowerCase();
                    const to  = esc(LABEL(key));
                    const from = t.from_stage ? `<small class="lh-muted ml-2">von ${esc(LABEL(t.from_stage))}</small>` : '';
                    const when = t.changed_at ? `<small class="lh-muted ml-2">${fmt(t.changed_at)}</small>` : '';
                    const by   = t.changed_by ? `<small class="lh-muted ml-2">· ${esc(t.changed_by)}</small>` : '';
                    const desc = t.description ? `<div class="mt-2">${esc(t.description).replace(/\n/g,'<br>')}</div>` : '';
                    return `
                      <li class="lh-item">
                        <div class="lh-icowrap"><div class="lh-ico" title="${to}">${ICONS[key]||''}</div></div>
                        <div class="lh-content">
                          <div class="d-flex align-items-center flex-wrap">
                            <span class="${BADGE(key)} mr-2">${to}</span>${from}${when}${by}
                          </div>
                          ${desc}
                        </div>
                      </li>`;
                  }).join('') || `<li class="lh-muted" style="padding:.5rem 0">Kein Phasenverlauf vorhanden.</li>`;

                  // Activities
                  acts.innerHTML = (data.customerHistory?.length ? data.customerHistory : []).map(h => {
                    const when = h.at ? `<span class="lh-muted">${fmt(h.at)}</span>` : '';
                    const ch   = h.channel ? ` · <span class="lh-muted">#${esc(h.channel)}</span>` : '';
                    const note = h.note ? `<div class="mt-2">${esc(h.note).replace(/\n/g,'<br>')}</div>` : '';
                    const meta = (h.meta && typeof h.meta==='object')
                      ? `<div class="mt-2">` + Object.entries(h.meta).map(([k,v]) =>
                          `<span class="lh-badge" style="margin-right:6px;margin-bottom:6px">
                            <span class="lh-muted">${esc(k)}:</span> ${esc(typeof v==='string'||typeof v==='number'? v : JSON.stringify(v))}
                          </span>`
                        ).join('') + `</div>` : '';
                    return `
                      <div class="lh-card">
                        <div class="d-flex justify-content-between">
                          <div class="font-weight-bold">${esc(h.phase_name||'–')}${h.activity_title?` · ${esc(h.activity_title)}`:''}</div>
                          ${when}
                        </div>
                        <div class="lh-muted mt-1"><i class="feather icon-user" style="font-size:12px"></i> ${esc(h.by||'Unbekannt')}${ch}</div>
                        ${note}${meta}
                      </div>`;
                  }).join('') || `<div class="lh-muted" style="padding:.5rem 0">Keine Aktivitäten gefunden.</div>`;
                }

                async function fetchJSON(href){
                  const url = href.includes('?') ? `${href}&format=json` : `${href}?format=json`;
                  const res = await fetch(url, {
                    headers:{ 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
                    credentials:'same-origin', cache:'no-store'
                  });
                  const ct = res.headers.get('content-type') || '';
                  if (!ct.includes('application/json')) throw new Error('Non-JSON response: ' + ct);
                  if (!res.ok) throw new Error('HTTP ' + res.status);
                  return res.json();
                }

                function onClick(e){
                  const a = e.target.closest('a[data-lh-history]');
                  if (!a) return;
                  e.preventDefault();
                  open(); skeleton();
                  fetchJSON(a.href).then(render).catch(err=>{
                    console.error('[lh] fetch failed:', err);
                    title.textContent = 'Fehler beim Laden';
                    tl.innerHTML = '<li class="lh-muted" style="color:#b91c1c;padding:.5rem 0">Fehler beim Laden des Verlaufs.</li>';
                    acts.innerHTML = '';
                  });
                }

                document.addEventListener('click', onClick);
                document.addEventListener('turbo:load',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
                document.addEventListener('turbolinks:load',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
                document.addEventListener('livewire:navigated',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
              })();
          

/* ===================== Extracted inline script block #17 ===================== */
            (function () {
              "use strict";

              // ------------------------------------------------
              // Bootstrap from LeadUI (with safe fallbacks)
              // ------------------------------------------------
              const { APP = {}, net = {}, utils = {} } = window.LeadUI || {};

              const {
                safeFetchJSON: leadSafeFetchJSON
              } = net;

              const {
                qs: leadQs,
                qsa: leadQsa,
                CSRF: leadCSRF,
                featherRefreshSoon: leadFeatherRefreshSoon,
              } = utils;

              const qs =
                leadQs ||
                function (selector, ctx = document) {
                  return ctx.querySelector(selector);
                };

              const qsa =
                leadQsa ||
                function (selector, ctx = document) {
                  return Array.from(ctx.querySelectorAll(selector));
                };

              const CSRF =
                leadCSRF ||
                function () {
                  return (
                    document.querySelector('meta[name="csrf-token"]')?.content || ""
                  );
                };

              const featherRefreshSoon =
                leadFeatherRefreshSoon ||
                function () {
                  /* noop */
                };

              const safeFetchJSON =
                leadSafeFetchJSON ||
                async function (url, { method = "GET", headers = {}, body, retries = 0 } = {}) {
                  async function go() {
                    const res = await fetch(url, {
                      method,
                      credentials: "same-origin",
                      headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        ...headers,
                      },
                      body,
                    });

                    const text = await res.text();
                    if (!res.ok) {
                      throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
                    }

                    try {
                      return JSON.parse(text);
                    } catch {
                      throw new Error("Invalid JSON response");
                    }
                  }

                  try {
                    return await go();
                  } catch (err) {
                    if (retries > 0 && method === "GET") {
                      await new Promise((r) => setTimeout(r, 200));
                      return safeFetchJSON(url, { method, headers, body, retries: retries - 1 });
                    }
                    throw err;
                  }
                };

              // ------------------------------------------------
              // Tiny helpers
              // ------------------------------------------------
              const esc = (val) =>
                String(val ?? "").replace(/[&<>]/g, (m) => {
                  return { "&": "&amp;", "<": "&lt;", ">": "&gt;" }[m];
                });

              const norm = (val) => String(val || "").toLowerCase().trim();

              const debounce = (fn, ms = 200) => {
                let t;
                return (...args) => {
                  clearTimeout(t);
                  t = setTimeout(() => fn(...args), ms);
                };
              };

              // safe highlight for simple plain text
              function hl(text, query) {
                const src = esc(text ?? "");
                const q = norm(query);
                if (!q) return src;
                const re = new RegExp(
                  `(${q.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`,
                  "ig"
                );
                return src.replace(re, '<mark class="ptk-hl">$1</mark>');
              }

              // ------------------------------------------------
              // Config: columns & labels
              // ------------------------------------------------
              const PTK_STATUS = [
                { key: "open",        label: "Offen" },
                { key: "in_progress", label: "In Arbeit" },
                { key: "paused",      label: "Pausiert" },
                { key: "done",        label: "Erledigt" },
                { key: "canceled",    label: "Storniert" },
              ];

              const STATUS_ORDER = PTK_STATUS.map((s) => s.key);

              const statusLabel = (key) =>
                PTK_STATUS.find((s) => s.key === key)?.label || key;

              // ------------------------------------------------
              // Personal Tasks Kanban (PTK)
              // ------------------------------------------------
              const PTK = {
                _tasks: [],
                _query: "",
                _ctx: null,
                _searchEl: null,
                _editingId: null,
                _selectedTaskId: null,

                routes() {
                  window.KANBAN_PERSONAL_TASK_PANEL_ROUTES = window.KANBAN_PERSONAL_TASK_PANEL_ROUTES || {};
                  return window.KANBAN_PERSONAL_TASK_PANEL_ROUTES;
                },

                route(template, replacements = {}) {
                  let url = String(template || "");
                  Object.entries(replacements || {}).forEach(([key, value]) => {
                    url = url.replaceAll(`__${key}__`, encodeURIComponent(value ?? ""));
                  });
                  return url;
                },

                normalizeTaskStatus(status) {
                  const raw = String(status || "open").toLowerCase();
                  if (["on_progress", "in_progress", "progress", "doing"].includes(raw)) return "in_progress";
                  if (["completed", "done", "complete"].includes(raw)) return "done";
                  if (["pause", "paused"].includes(raw)) return "paused";
                  if (["cancel", "canceled", "cancelled", "rejected", "junk"].includes(raw)) return "canceled";
                  return "open";
                },

                statusText(status) {
                  const key = this.normalizeTaskStatus(status);
                  return {
                    open: "Offen",
                    in_progress: "In Arbeit",
                    paused: "Pausiert",
                    done: "Erledigt",
                    canceled: "Storniert",
                  }[key] || "Offen";
                },

                priorityText(priority) {
                  const key = String(priority || "normal").toLowerCase();
                  return {
                    low: "Niedrig",
                    normal: "Normal",
                    high: "Hoch",
                    urgent: "Dringend",
                  }[key] || (priority || "Normal");
                },

                avatar(employee, size = 28) {
                  const name = employee?.name || employee?.lastname || "MA";
                  const image = employee?.image || "";
                  const initials = String(name).trim().split(/\s+/).map((x) => x[0]).join("").slice(0, 2).toUpperCase() || "MA";

                  if (image) {
                    const src = String(image).startsWith("http") || String(image).startsWith("/")
                      ? image
                      : `${APP.EMP_SRC}/${image}`;

                    return `<img src="${esc(src)}" alt="${esc(name)}" width="${size}" height="${size}" class="pt-list-avatar">`;
                  }

                  return `<span class="pt-list-avatar pt-list-avatar--text" style="width:${size}px;height:${size}px">${esc(initials)}</span>`;
                },

                employeeChips(employees = []) {
                  if (!Array.isArray(employees) || !employees.length) {
                    return `<span class="pt-list-muted">Keine Mitarbeiter</span>`;
                  }

                  return employees.map((emp) => `
                    <span class="pt-list-employee">
                      ${this.avatar(emp, 24)}
                      <span>${esc(emp?.name || "Mitarbeiter")}</span>
                    </span>
                  `).join("");
                },

                formatDate(value) {
                  if (!value) return "";
                  try {
                    return new Date(value).toLocaleDateString("de-DE");
                  } catch {
                    return String(value || "");
                  }
                },

                formatDateTime(value) {
                  if (!value) return "";
                  try {
                    return new Date(value).toLocaleString("de-DE");
                  } catch {
                    return String(value || "");
                  }
                },

                taskMatches(task, query) {
                  const q = norm(query);
                  if (!q) return true;

                  const blob = [
                    task.title,
                    task.task_title,
                    task.description,
                    task.status,
                    task.priority,
                    task.product?.name,
                    ...(task.employees || []).map((e) => e.name),
                    ...(task.keys || []).map((k) => `${k.task || ""} ${k.description || ""}`),
                    ...(task.comments || []).map((c) => `${c.comment || ""} ${c.author?.name || ""}`),
                  ].join(" ").toLowerCase();

                  return blob.includes(q);
                },

                // --------------- public API ----------------

                open(customerId, alternativeId, productId, title, leadProductListId = "") {
                  const titleEl = qs("#pt-title");
                  if (titleEl) {
                    titleEl.textContent = title || "Aufgaben";
                  }

                  const cField = qs("#pt-customer_id");
                  const aField = qs("#pt-alternative_id");
                  const pField = qs("#pt-product_id");
                  const lField = qs("#pt-lead_product_list_id");

                  if (cField) cField.value = customerId || "";
                  if (aField) aField.value = alternativeId || "";
                  if (pField) pField.value = productId || "";
                  if (lField) lField.value = leadProductListId || "";

                  this._ctx = {
                    customerId: customerId || "",
                    alternativeId: alternativeId || "",
                    productId: productId || "",
                    leadProductListId: leadProductListId || "",
                    title: title || "Aufgaben",
                  };

                  this._editingId = null;
                  this._selectedTaskId = null;

                  const form = qs("#pt-form");
                  if (form) form.reset();

                  if (window.jQuery) {
                    jQuery("#pt-employee_ids").val(null).trigger("change");
                  }

                  this.show();
                  this.ensureListShell();
                  this.renderSkeletonContent();
                  this.loadTasks();
                },

                show() {
                  qs("#pt-backdrop")?.classList.add("show");
                  qs("#pt-drawer")?.classList.add("open");
                  document.body.style.overflow = "hidden";
                },

                hide() {
                  qs("#pt-backdrop")?.classList.remove("show");
                  qs("#pt-drawer")?.classList.remove("open");
                  document.body.style.overflow = "";
                  this._editingId = null;
                  this._selectedTaskId = null;
                },

                setQuery(query) {
                  this._query = norm(query || "");
                  this.renderFiltered();
                },

                updateCardBadge() {
                  const ctx = this._ctx;
                  if (!ctx) return;

                  const c = ctx.customerId || "";
                  const a = ctx.alternativeId || "";
                  const p = ctx.productId || "";
                  const count = this._tasks.length;

                  const selector = `.card[data-customer-id="${c}"][data-alternative-id="${a}"][data-product-id="${p}"]`;

                  qsa(selector).forEach((card) => {
                    const btn =
                      card.querySelector('.kb-menu-item[data-menu="aufgabe"]') ||
                      card.querySelector('[data-menu="aufgabe"]') ||
                      card.querySelector("[data-open-personal-tasks]");

                    if (!btn) return;

                    let pill = btn.querySelector("[data-pt-count]");
                    if (!pill) {
                      pill = document.createElement("span");
                      pill.className = "kb-menu-pill kb-menu-pill--pt";
                      pill.setAttribute("data-pt-count", "");
                      btn.appendChild(pill);
                    }

                    pill.textContent = String(count);
                    pill.style.display = count ? "inline-flex" : "none";
                  });
                },

                // --------------- list shell ----------------

                ensureListShell() {
                  const wrap = qs("#pt-list");
                  if (!wrap) return;

                  wrap.classList.remove("ptk-board");
                  wrap.classList.add("pt-list-panel");
                  wrap.dataset.ptList = "1";
                  wrap.dataset.ptkBoard = "";

                  wrap.innerHTML = `
                    <div class="pt-list-context" id="ptTaskContext">
                      <div class="pt-list-context-icon"><i class="feather icon-check-square"></i></div>
                      <div class="pt-list-context-main">
                        <strong>${esc(this._ctx?.title || "Aufgaben")}</strong>
                        <span id="ptTaskContextSub">Aufgaben, Checklisten und Kommentare</span>
                      </div>
                      <span class="pt-list-count" id="pt-count">0</span>
                    </div>

                    <div class="pt-list-toolbar">
                      <div class="pt-list-search">
                        <i class="feather icon-search"></i>
                        <input id="ptk-search"
                               type="search"
                               autocomplete="off"
                               placeholder="Aufgabe, Mitarbeiter, Kommentar suchen…">
                      </div>
                      <div class="pt-list-filter-pills" id="ptTaskStatusPills">
                        <button type="button" class="pt-list-pill is-active" data-pt-filter-status="">Alle</button>
                        <button type="button" class="pt-list-pill" data-pt-filter-status="open">Offen</button>
                        <button type="button" class="pt-list-pill" data-pt-filter-status="in_progress">In Arbeit</button>
                        <button type="button" class="pt-list-pill" data-pt-filter-status="done">Erledigt</button>
                      </div>
                    </div>

                    <div class="pt-list-layout">
                      <div class="pt-task-list" id="ptTaskList">
                        <div class="pt-empty">Lade Aufgaben…</div>
                      </div>
                      <aside class="pt-task-detail" id="ptTaskDetail">
                        <div class="pt-task-detail-empty">
                          <i class="feather icon-mouse-pointer"></i>
                          <strong>Aufgabe auswählen</strong>
                          <span>Wähle links eine Aufgabe, um Details, PersonalTaskKey und Kommentare zu sehen.</span>
                        </div>
                      </aside>
                    </div>
                  `;

                  this.bindListShellEvents();
                  featherRefreshSoon();
                },

                bindListShellEvents() {
                  this._searchEl = qs("#ptk-search");

                  if (this._searchEl && !this._searchEl._wired) {
                    const run = debounce((ev) => {
                      this.setQuery(ev.target.value || "");
                    }, 180);

                    this._searchEl.addEventListener("input", run);
                    this._searchEl._wired = true;
                  }

                  qsa("[data-pt-filter-status]").forEach((btn) => {
                    if (btn._ptStatusWired) return;
                    btn._ptStatusWired = true;

                    btn.addEventListener("click", () => {
                      qsa("[data-pt-filter-status]").forEach((b) => b.classList.remove("is-active"));
                      btn.classList.add("is-active");
                      this.renderFiltered();
                    });
                  });
                },

                renderSkeletonContent() {
                  const list = qs("#ptTaskList");
                  if (list) {
                    list.innerHTML = `
                      <div class="pt-task-skeleton"></div>
                      <div class="pt-task-skeleton"></div>
                      <div class="pt-task-skeleton"></div>
                    `;
                  }

                  const detail = qs("#ptTaskDetail");
                  if (detail) {
                    detail.innerHTML = `
                      <div class="pt-task-detail-empty">
                        <i class="feather icon-loader"></i>
                        <strong>Lade Aufgaben…</strong>
                        <span>Bitte warten.</span>
                      </div>
                    `;
                  }

                  const head = qs("#pt-title");
                  if (head) head.classList.add("ptk-loading");
                },

                setLoading(on) {
                  const head = qs("#pt-title");
                  if (!head) return;
                  head.classList.toggle("ptk-loading", !!on);
                },

                // --------------- data IO ----------------

                async loadTasks() {
                  const c = qs("#pt-customer_id")?.value || "";
                  const a = qs("#pt-alternative_id")?.value || "";
                  const p = qs("#pt-product_id")?.value || "";
                  const l = qs("#pt-lead_product_list_id")?.value || "";

                  if (!c) {
                    this._tasks = [];
                    this.renderFiltered();
                    this.updateCardBadge();
                    return;
                  }

                  this.setLoading(true);

                  const routes = this.routes();
                  const base = routes.tasks || APP.endpoints.personalTasksIndex || "/kanban/personal-task-panel/tasks";

                  const buildUrl = (mode = "context") => {
                    const params = new URLSearchParams();
                    params.set("customer_id", c);

                    // Important: context mode sends object/product so exact tasks come first.
                    // Customer mode is a fallback for old tasks saved only with customer_id.
                    if (mode === "context") {
                      if (a) params.set("alternative_id", a);
                      if (p) params.set("product_id", p);
                      if (l) params.set("lead_product_list_id", l);
                    }

                    return `${base}?${params.toString()}`;
                  };

                  try {
                    let data = await safeFetchJSON(buildUrl("context"));

                    let tasks =
                      Array.isArray(data?.tasks) ? data.tasks :
                      Array.isArray(data?.data) ? data.data :
                      Array.isArray(data) ? data :
                      [];

                    // Fallback: if context returns no tasks, load all tasks of the customer.
                    // This fixes old tasks that were saved without alternative_id/product_id.
                    if (!tasks.length && (a || p || l)) {
                      data = await safeFetchJSON(buildUrl("customer"));
                      tasks =
                        Array.isArray(data?.tasks) ? data.tasks :
                        Array.isArray(data?.data) ? data.data :
                        Array.isArray(data) ? data :
                        [];
                    }

                    this._tasks = tasks.map((task) => ({
                      ...task,
                      title: task.title || task.task_title || "Aufgabe",
                      status: this.normalizeTaskStatus(task.status || task.task_status),
                      priority: task.priority || "normal",
                      keys: Array.isArray(task.keys) ? task.keys : (Array.isArray(task.taskKeys) ? task.taskKeys : []),
                      comments: Array.isArray(task.comments) ? task.comments : [],
                      employees: Array.isArray(task.employees) ? task.employees : [],
                    }));

                    this.renderFiltered();

                    const badge = qs("#pt-count");
                    if (badge) badge.textContent = String(this._tasks.length);

                    this.updateCardBadge();
                  } catch (err) {
                    const list = qs("#ptTaskList");
                    if (list) {
                      list.innerHTML = `
                        <div class="pt-empty pt-empty--error">
                          <i class="feather icon-alert-triangle"></i>
                          <strong>Aufgaben konnten nicht geladen werden.</strong>
                          <span>${esc(err.message || "Serverfehler")}</span>
                        </div>
                      `;
                    }
                  } finally {
                    this.setLoading(false);
                    featherRefreshSoon();
                  }
                },

                renderFiltered() {
                  const list = qs("#ptTaskList");
                  if (!list) return;

                  const activeStatus = qs("[data-pt-filter-status].is-active")?.dataset.ptFilterStatus || "";
                  const filtered = this._tasks.filter((task) => {
                    const statusOk = !activeStatus || this.normalizeTaskStatus(task.status || task.task_status) === activeStatus;
                    return statusOk && this.taskMatches(task, this._query);
                  });

                  const totalBadge = qs("#pt-count");
                  if (totalBadge) totalBadge.textContent = String(filtered.length);

                  if (!filtered.length) {
                    list.innerHTML = `
                      <div class="pt-empty">
                        <i class="feather icon-inbox"></i>
                        <strong>Keine Aufgaben gefunden</strong>
                        <span>Für diesen Kunden/Produkt-Kontext gibt es keine passende Aufgabe.</span>
                      </div>
                    `;
                    this.renderDetail(null);
                    featherRefreshSoon();
                    return;
                  }

                  list.innerHTML = filtered.map((task) => this.taskListItemHTML(task, this._query)).join("");

                  const selectedStillExists = filtered.some((task) => String(task.id) === String(this._selectedTaskId));
                  if (!selectedStillExists) {
                    this._selectedTaskId = filtered[0]?.id || null;
                  }

                  this.highlightSelectedTask();
                  const selectedTask = this._tasks.find((task) => String(task.id) === String(this._selectedTaskId)) || filtered[0];
                  this.renderDetail(selectedTask);
                  featherRefreshSoon();
                },

                taskListItemHTML(task, query = "") {
                  const status = this.normalizeTaskStatus(task.status || task.task_status);
                  const title = task.title || task.task_title || "Aufgabe";
                  const priority = String(task.priority || "normal").toLowerCase();
                  const due = task.due_date ? this.formatDate(task.due_date) : "";
                  const start = task.start_date ? this.formatDate(task.start_date) : "";
                  const keysCount = Array.isArray(task.keys) ? task.keys.length : 0;
                  const keysDone = Array.isArray(task.keys) ? task.keys.filter((k) => k.is_completed || k.done_status === "done").length : 0;
                  const comments = Number(task.comments_count ?? (Array.isArray(task.comments) ? task.comments.length : 0));

                  return `
                    <article class="pt-task-item"
                             data-pt-task-item="${esc(task.id)}"
                             data-status="${esc(status)}">
                      <div class="pt-task-item-head">
                        <span class="pt-task-status pt-task-status--${esc(status)}">${esc(this.statusText(status))}</span>
                        <span class="pt-task-priority pt-task-priority--${esc(priority)}">${esc(this.priorityText(priority))}</span>
                      </div>

                      <button type="button" class="pt-task-item-main" data-ptk-open="${esc(task.id)}">
                        <strong>${hl(title, query)}</strong>
                        ${task.description ? `<span>${hl(String(task.description).replace(/<[^>]*>/g, "").slice(0, 140), query)}</span>` : ""}
                      </button>

                      <div class="pt-task-meta-grid">
                        ${start ? `<span><i class="feather icon-play-circle"></i>${esc(start)}</span>` : ""}
                        ${due ? `<span><i class="feather icon-calendar"></i>${esc(due)}${task.due_time ? " · " + esc(task.due_time) : ""}</span>` : ""}
                        <span><i class="feather icon-check-square"></i>${keysDone}/${keysCount} Keys</span>
                        <span><i class="feather icon-message-circle"></i>${comments} Kommentare</span>
                      </div>

                      <div class="pt-task-employees">
                        ${this.employeeChips((task.employees || []).slice(0, 4))}
                      </div>
                    </article>
                  `;
                },

                highlightSelectedTask() {
                  qsa("[data-pt-task-item]").forEach((el) => {
                    el.classList.toggle("is-active", String(el.dataset.ptTaskItem) === String(this._selectedTaskId));
                  });
                },

                renderDetail(task) {
                  const detail = qs("#ptTaskDetail");
                  if (!detail) return;

                  if (!task) {
                    detail.innerHTML = `
                      <div class="pt-task-detail-empty">
                        <i class="feather icon-mouse-pointer"></i>
                        <strong>Aufgabe auswählen</strong>
                        <span>Wähle links eine Aufgabe, um Details, PersonalTaskKey und Kommentare zu sehen.</span>
                      </div>
                    `;
                    return;
                  }

                  const status = this.normalizeTaskStatus(task.status || task.task_status);
                  const priority = String(task.priority || "normal").toLowerCase();
                  if (window.jQuery) {
          const employeeIds = Array.isArray(task.employees) ? task.employees.map(function (emp) { return String(emp.id || emp.employee_id || ""); }).filter(Boolean) : [];
          const controllerIds = Array.isArray(task.controllers) ? task.controllers.map(function (emp) { return String(emp.id || emp.employee_id || ""); }).filter(Boolean) : [];
          window.jQuery(SELECTORS.employee).val(employeeIds).trigger("change");
          window.jQuery(SELECTORS.controller).val(controllerIds).trigger("change");
          if (task.team_id || task.team?.id) window.jQuery(SELECTORS.team).val(String(task.team_id || task.team.id)).trigger("change");
        }

        const keys = Array.isArray(task.keys) ? task.keys : [];
                  const comments = Array.isArray(task.comments) ? task.comments : [];
                  const employees = Array.isArray(task.employees) ? task.employees : [];
                  const controllers = Array.isArray(task.controllers) ? task.controllers : [];

                  detail.innerHTML = `
                    <div class="pt-detail-head">
                      <div>
                        <div class="pt-detail-kicker">
                          <span class="pt-task-status pt-task-status--${esc(status)}">${esc(this.statusText(status))}</span>
                          <span class="pt-task-priority pt-task-priority--${esc(priority)}">${esc(this.priorityText(priority))}</span>
                        </div>
                        <h4>${esc(task.title || task.task_title || "Aufgabe")}</h4>
                        <small>Aufgabe #${esc(task.id)}${task.task_id ? " · " + esc(task.task_id) : ""}</small>
                      </div>
                      <div class="pt-detail-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-ptk-edit="${esc(task.id)}" title="Bearbeiten">
                          <i class="feather icon-edit-2"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-ptk-del="${esc(task.id)}" title="Löschen">
                          <i class="feather icon-trash-2"></i>
                        </button>
                      </div>
                    </div>

                    ${task.description ? `
                      <section class="pt-detail-section">
                        <h5><i class="feather icon-align-left"></i> Beschreibung</h5>
                        <div class="pt-detail-rich">${task.description}</div>
                      </section>
                    ` : ""}

                    <section class="pt-detail-section">
                      <h5><i class="feather icon-clock"></i> Zeit & Termine</h5>
                      <div class="pt-detail-info-grid">
                        <div><span>Start</span><strong>${esc(this.formatDate(task.start_date) || "-")}</strong></div>
                        <div><span>Fällig</span><strong>${esc(this.formatDate(task.due_date) || "-")}${task.due_time ? " · " + esc(task.due_time) : ""}</strong></div>
                        <div><span>Erinnerung</span><strong>${esc(this.formatDate(task.reminder_date) || "-")}${task.reminder_time ? " · " + esc(task.reminder_time) : ""}</strong></div>
                        <div><span>Aufwand</span><strong>${esc(task.total_day || 0)} Tag · ${esc(task.total_time || 0)} Std.</strong></div>
                      </div>
                    </section>

                    <section class="pt-detail-section">
                      <h5><i class="feather icon-users"></i> Mitarbeiter</h5>
                      <div class="pt-detail-chip-row">
                        ${this.employeeChips(employees)}
                      </div>
                      ${controllers.length ? `
                        <div class="pt-detail-subtitle">Controller</div>
                        <div class="pt-detail-chip-row">${this.employeeChips(controllers)}</div>
                      ` : ""}
                    </section>

                    <section class="pt-detail-section">
                      <h5><i class="feather icon-list"></i> PersonalTaskKey</h5>
                      <div class="pt-key-list">
                        ${keys.length ? keys.map((key) => this.keyHTML(key)).join("") : `<div class="pt-empty pt-empty--small">Keine Keys vorhanden.</div>`}
                      </div>
                    </section>

                    <section class="pt-detail-section">
                      <h5><i class="feather icon-message-circle"></i> Kommentare / Berichte</h5>
                      <form class="pt-comment-form" data-pt-comment-form="${esc(task.id)}">
                        <textarea rows="3" placeholder="Kommentar oder Bericht schreiben…" required></textarea>
                        <button type="submit" class="btn btn-sm btn-primary">
                          <i class="feather icon-send"></i> Speichern
                        </button>
                      </form>

                      <div class="pt-comment-list" id="ptCommentList-${esc(task.id)}">
                        ${comments.length ? comments.map((comment) => this.commentHTML(comment)).join("") : `<div class="pt-empty pt-empty--small">Noch keine Kommentare.</div>`}
                      </div>
                    </section>
                  `;

                  featherRefreshSoon();
                },

                keyHTML(key) {
                  const done = !!(key.is_completed || key.done_status === "done");
                  const employees = Array.isArray(key.employees) ? key.employees : [];

                  return `
                    <article class="pt-key-row ${done ? "is-done" : ""}" data-pt-key-row="${esc(key.id)}">
                      <button type="button" class="pt-key-check" data-pt-key-toggle="${esc(key.id)}">
                        <i class="feather ${done ? "icon-check-circle" : "icon-circle"}"></i>
                      </button>
                      <div class="pt-key-main">
                        <strong>${esc(key.task || "Schritt")}</strong>
                        ${key.description ? `<span>${esc(key.description)}</span>` : ""}
                        <div class="pt-key-meta">
                          ${key.duration ? `<small><i class="feather icon-clock"></i>${esc(key.duration)} Min.</small>` : ""}
                          ${key.total_time ? `<small><i class="feather icon-activity"></i>${esc(key.total_time)} Std.</small>` : ""}
                          ${key.submit_time ? `<small><i class="feather icon-upload"></i>${esc(key.submit_time)}</small>` : ""}
                        </div>
                        <div class="pt-detail-chip-row pt-detail-chip-row--small">
                          ${this.employeeChips(employees)}
                        </div>
                      </div>
                    </article>
                  `;
                },

                commentHTML(comment) {
                  const replies = Array.isArray(comment.replies) ? comment.replies : [];
                  const author = comment.author || {};

                  return `
                    <article class="pt-comment" data-pt-comment="${esc(comment.id)}">
                      <div class="pt-comment-avatar">${this.avatar(author, 30)}</div>
                      <div class="pt-comment-body">
                        <div class="pt-comment-head">
                          <strong>${esc(author.name || "Mitarbeiter")}</strong>
                          <span>${esc(this.formatDateTime(comment.created_at) || "")}</span>
                        </div>
                        <div class="pt-comment-text">${comment.comment || ""}</div>
                        <button type="button" class="pt-comment-reply-toggle" data-pt-reply-toggle="${esc(comment.id)}">
                          Antworten
                        </button>
                        <form class="pt-comment-reply-form d-none" data-pt-reply-form="${esc(comment.id)}">
                          <textarea rows="2" placeholder="Antwort schreiben…" required></textarea>
                          <button type="submit" class="btn btn-xs btn-primary">Antwort speichern</button>
                        </form>
                        ${replies.length ? `<div class="pt-comment-replies">${replies.map((reply) => this.commentHTML(reply)).join("")}</div>` : ""}
                      </div>
                    </article>
                  `;
                },

                async selectTask(id) {
                  this._selectedTaskId = id;
                  this.highlightSelectedTask();

                  const local = this._tasks.find((task) => String(task.id) === String(id));
                  this.renderDetail(local);

                  const routes = this.routes();
                  if (!routes.show) return;

                  try {
                    const data = await safeFetchJSON(this.route(routes.show, { TASK: id }));
                    if (data?.task) {
                      const idx = this._tasks.findIndex((task) => String(task.id) === String(id));
                      if (idx >= 0) {
                        this._tasks[idx] = {
                          ...data.task,
                          status: this.normalizeTaskStatus(data.task.status || data.task.task_status),
                        };
                      }
                      this.renderDetail(this._tasks[idx] || data.task);
                    }
                  } catch (err) {
                    console.warn("[Kanban] Task details could not be refreshed", err);
                  }
                },

                async storeComment(taskId, text) {
                  const routes = this.routes();
                  const url = routes.commentStore
                    ? this.route(routes.commentStore, { TASK: taskId })
                    : `/kanban/personal-task-panel/tasks/${encodeURIComponent(taskId)}/comments`;

                  const data = await postJSON(url, { comment: text });

                  const task = this._tasks.find((item) => String(item.id) === String(taskId));
                  if (task && data?.comment) {
                    task.comments = Array.isArray(task.comments) ? task.comments : [];
                    task.comments.unshift(data.comment);
                    task.comments_count = task.comments.length;
                    this.renderDetail(task);
                    this.renderFiltered();
                  }

                  return data;
                },

                async storeReply(commentId, text) {
                  const routes = this.routes();
                  const url = routes.replyStore
                    ? this.route(routes.replyStore, { COMMENT: commentId })
                    : `/kanban/personal-task-panel/comments/${encodeURIComponent(commentId)}/reply`;

                  await postJSON(url, { comment: text });

                  if (this._selectedTaskId) {
                    await this.selectTask(this._selectedTaskId);
                    this.renderFiltered();
                  }
                },

                async toggleKey(keyId) {
                  const routes = this.routes();
                  const url = routes.keyToggle
                    ? this.route(routes.keyToggle, { KEY: keyId })
                    : `/kanban/personal-task-panel/keys/${encodeURIComponent(keyId)}/toggle`;

                  await postJSON(url, {});

                  if (this._selectedTaskId) {
                    await this.selectTask(this._selectedTaskId);
                    this.renderFiltered();
                  }
                },

                // --------------- legacy create/update/delete kept ----------------

                async updateStatus(id, status) {
                  const url = APP.endpoints.personalTasksUpdate(id);
                  const resp = await fetch(url, {
                    method: "PUT",
                    credentials: "same-origin",
                    headers: {
                      "Content-Type": "application/json",
                      "X-CSRF-TOKEN": CSRF(),
                      "Accept": "application/json",
                      "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({ task_status: status }),
                  });

                  if (!resp.ok) {
                    throw new Error("Status konnte nicht gespeichert werden.");
                  }

                  return resp.json().catch(() => ({}));
                },

                async submitForm(ev) {
                  ev.preventDefault();

                  const title = qs("#pt-task_title")?.value.trim() || "";
                  if (!title) {
                    Swal.fire("Fehler", "Aufgabentitel ist erforderlich.", "error");
                    return;
                  }

                  const customerId = Number(qs("#pt-customer_id")?.value || 0);
                  const alternativeId = Number(qs("#pt-alternative_id")?.value || 0);
                  const productIdRaw = qs("#pt-product_id")?.value || "";
                  const leadProductListIdRaw = qs("#pt-lead_product_list_id")?.value || "";

                  if (!customerId) {
                    Swal.fire("Fehler", "Kunde fehlt.", "error");
                    return;
                  }

                  const payload = {
                    is_customer: 1,
                    customer_id: customerId,
                    alternative_id: alternativeId || null,
                    product_id: productIdRaw ? Number(productIdRaw) : null,
                    lead_product_list_id: leadProductListIdRaw ? Number(leadProductListIdRaw) : null,
                    task_title: title,
                    description: qs("#pt-description")?.value.trim() || null,
                    start_date: qs("#pt-start_date")?.value || null,
                    due_date: qs("#pt-due_date")?.value || null,
                    due_time: qs("#pt-due_time")?.value || null,
                    priority: qs("#pt-priority")?.value || "normal",
                    color: qs("#pt-color")?.value || "#8fc73e",
                  };

                  if (window.jQuery) {
                    const emps = jQuery("#pt-employee_ids").val() || [];
                    payload.employee_ids = emps;
                    payload.employee = emps;
                  }

                  const isEdit = !!this._editingId;
                  const url = isEdit
                    ? APP.endpoints.personalTasksUpdate(this._editingId)
                    : APP.endpoints.personalTasksStore;
                  const method = isEdit ? "PUT" : "POST";

                  try {
                    const resp = await fetch(url, {
                      method,
                      credentials: "same-origin",
                      headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CSRF(),
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                      },
                      body: JSON.stringify(payload),
                    });

                    const data = await resp.json().catch(() => ({}));
                    if (!resp.ok || data?.success === false) {
                      throw new Error(data?.message || "Aufgabe konnte nicht gespeichert werden.");
                    }

                    this._editingId = null;
                    if (qs("#pt-form")) qs("#pt-form").reset();
                    if (window.jQuery) {
                      jQuery("#pt-employee_ids").val(null).trigger("change");
                    }

                    await this.loadTasks();
                    Swal.fire("Gespeichert", "Aufgabe wurde gespeichert.", "success");
                  } catch (err) {
                    Swal.fire("Fehler", err.message || "Serverfehler", "error");
                  }
                },

                fillForm(id) {
                  const task = this._tasks.find((t) => String(t.id) === String(id));
                  if (!task) return;

                  this._editingId = id;

                  const set = (sel, val) => {
                    const el = qs(sel);
                    if (el) el.value = val ?? "";
                  };

                  set("#pt-task_title", task.title || task.task_title || "");
                  set("#pt-description", task.description || "");
                  set("#pt-start_date", task.start_date || "");
                  set("#pt-due_date", task.due_date || "");
                  set("#pt-due_time", task.due_time || "");
                  set("#pt-priority", task.priority || "normal");
                  set("#pt-color", task.color || "#8fc73e");

                  if (window.jQuery) {
                    const ids = Array.isArray(task.employees)
                      ? task.employees.map((e) => e.id)
                      : [];
                    jQuery("#pt-employee_ids").val(ids).trigger("change");
                  }

                  const form = qs("#pt-form");
                  form?.scrollIntoView({ behavior: "smooth", block: "nearest" });
                },

                async deleteTask(id) {
                  const ok = await Swal.fire({
                    title: "Aufgabe löschen?",
                    text: "Diese Aufgabe wird gelöscht.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ja, löschen",
                    cancelButtonText: "Abbrechen",
                  });

                  if (!ok.isConfirmed) return;

                  try {
                    const resp = await fetch(APP.endpoints.personalTasksDestroy(id), {
                      method: "DELETE",
                      credentials: "same-origin",
                      headers: {
                        "X-CSRF-TOKEN": CSRF(),
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                      },
                    });

                    const data = await resp.json().catch(() => ({}));

                    if (!resp.ok || data?.success === false) {
                      throw new Error(data?.message || "Aufgabe konnte nicht gelöscht werden.");
                    }

                    this._tasks = this._tasks.filter((t) => String(t.id) !== String(id));
                    if (String(this._selectedTaskId) === String(id)) this._selectedTaskId = null;

                    this.renderFiltered();
                    this.updateCardBadge();

                    Swal.fire("Gelöscht", "Aufgabe wurde gelöscht.", "success");
                  } catch (err) {
                    Swal.fire("Fehler", err.message || "Serverfehler", "error");
                  }
                },
              };

              // ------------------------------------------------
              // Global bindings
              // ------------------------------------------------

              qs("#pt-backdrop")?.addEventListener("click", () => PTK.hide());
              qsa("[data-pt-close]").forEach((btn) =>
                btn.addEventListener("click", () => PTK.hide())
              );

              qs("#pt-form")?.addEventListener("submit", (ev) => PTK.submitForm(ev));

              document.addEventListener("click", (e) => {
                const open = e.target.closest("[data-ptk-open]");
                if (open) {
                  e.preventDefault();
                  const id = open.getAttribute("data-ptk-open");
                  if (id) PTK.selectTask(id);
                  return;
                }

                const keyToggle = e.target.closest("[data-pt-key-toggle]");
                if (keyToggle) {
                  e.preventDefault();
                  const id = keyToggle.getAttribute("data-pt-key-toggle");
                  if (id) PTK.toggleKey(id);
                  return;
                }

                const replyToggle = e.target.closest("[data-pt-reply-toggle]");
                if (replyToggle) {
                  e.preventDefault();
                  const id = replyToggle.getAttribute("data-pt-reply-toggle");
                  const form = qs(`[data-pt-reply-form="${CSS.escape(id)}"]`);
                  form?.classList.toggle("d-none");
                  return;
                }

                const del = e.target.closest("[data-ptk-del]");
                if (del) {
                  e.preventDefault();
                  const id = del.getAttribute("data-ptk-del");
                  if (id) PTK.deleteTask(id);
                  return;
                }

                const edit = e.target.closest("[data-ptk-edit]");
                if (edit) {
                  e.preventDefault();
                  const id = edit.getAttribute("data-ptk-edit");
                  if (id) PTK.fillForm(id);
                }
              });

              document.addEventListener("submit", async (e) => {
                const commentForm = e.target.closest("[data-pt-comment-form]");
                if (commentForm) {
                  e.preventDefault();
                  const taskId = commentForm.getAttribute("data-pt-comment-form");
                  const textarea = commentForm.querySelector("textarea");
                  const text = (textarea?.value || "").trim();

                  if (!text) return;

                  try {
                    await PTK.storeComment(taskId, text);
                    if (textarea) textarea.value = "";
                  } catch (err) {
                    Swal.fire("Fehler", err.message || "Kommentar konnte nicht gespeichert werden.", "error");
                  }

                  return;
                }

                const replyForm = e.target.closest("[data-pt-reply-form]");
                if (replyForm) {
                  e.preventDefault();
                  const commentId = replyForm.getAttribute("data-pt-reply-form");
                  const textarea = replyForm.querySelector("textarea");
                  const text = (textarea?.value || "").trim();

                  if (!text) return;

                  try {
                    await PTK.storeReply(commentId, text);
                    if (textarea) textarea.value = "";
                  } catch (err) {
                    Swal.fire("Fehler", err.message || "Antwort konnte nicht gespeichert werden.", "error");
                  }
                }
              });

              document.addEventListener("open-personal-tasks", (e) => {
                const d = e.detail || {};
                PTK.open(
                  d.customerId,
                  d.alternativeId,
                  d.productId,
                  d.title,
                  d.leadProductListId || d.lead_product_list_id || ""
                );
              });

              window.PersonalTasksUI = PTK;
            })();
          

/* ===================== Extracted inline script block #18 ===================== */
              (() => {
                "use strict";

                /* --------------------------------------------------------------------------
                * Team Hover Popover (fixed)
                * - Reads assigned-by / assigned-at from:
                *    1) avatar element itself (img/li/span with data-emp-id)
                *    2) closest <li> wrapper (even if LI does NOT have data-emp-id)
                *    3) closest parent element
                * - Shows Stage (German) from nearest .card or tr.list-row-item dataset.stage
                *   using window.LeadUI.APP.stageNames when available
                * ------------------------------------------------------------------------ */

                const EMP_SRC = "/images/employee";
                const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];

                const byId = new Map(
                  employees
                    .map((e) => {
                      const id = Number(e?.id);
                      return Number.isFinite(id) ? [id, e] : null;
                    })
                    .filter(Boolean)
                );

                const fallbackStageNames = {
                  lead: "Lead",
                  offer: "Angebot",
                  follow_up: "Nachfassen",
                  accepted: "Annehmen",
                  deal: "Auftrag",
                  project: "Montage",
                  completed: "Abschluss",
                  archive: "Archiv",
                  junk: "Junk",
                };

                const stageNames =
                  (window.LeadUI?.APP?.stageNames && typeof window.LeadUI.APP.stageNames === "object"
                    ? window.LeadUI.APP.stageNames
                    : fallbackStageNames);

                let pop = null;
                let anchor = null;
                let hideTimer = null;

                const esc = (s) =>
                  String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                const pad2 = (n) => String(n).padStart(2, "0");

                const parseAnyDate = (raw) => {
                  const s = String(raw || "").trim();
                  if (!s) return null;

                  // ISO works directly
                  let d = new Date(s);
                  if (!Number.isNaN(d.getTime())) return d;

                  // "YYYY-MM-DD HH:mm:ss" -> "YYYY-MM-DDTHH:mm:ss"
                  if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/.test(s)) {
                    d = new Date(s.replace(" ", "T"));
                    if (!Number.isNaN(d.getTime())) return d;
                  }

                  return null;
                };

                const fmtDE = (raw) => {
                  const d = parseAnyDate(raw);
                  if (!d) return "–";
                  try {
                    return d.toLocaleString("de-DE");
                  } catch {
                    return `${pad2(d.getDate())}.${pad2(d.getMonth() + 1)}.${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
                  }
                };

                function ensurePop() {
                  if (pop) return pop;

                  pop = document.createElement("div");
                  pop.className = "team-popover";
                  pop.setAttribute("role", "dialog");
                  pop.setAttribute("aria-label", "Team");

                  pop.innerHTML = `
                    <div class="team-popover__title">
                      <div class="t1">Team</div>
                      <div class="t2" data-subline></div>
                    </div>
                    <div class="team-popover__list" data-list></div>
                  `;

                  document.body.appendChild(pop);

                  pop.addEventListener("mouseenter", () => hideTimer && clearTimeout(hideTimer));
                  pop.addEventListener("mouseleave", () => scheduleHide());

                  return pop;
                }

                function readAttrChain(node, keyKebab, keyDataset) {
                  if (!node) return "";
                  const direct = node.getAttribute?.(keyKebab) || node.dataset?.[keyDataset] || "";
                  return String(direct || "").trim();
                }

                function getContextStage(ul) {
                  const ctx = ul.closest?.(".card, tr.list-row-item") || null;
                  const raw = String(ctx?.dataset?.stage || "").trim().toLowerCase();
                  if (!raw) return "";
                  return stageNames[raw] || raw;
                }

                function collectAvatars(ul) {
                  // Keep DOM order: select anything with data-emp-id (img or li etc.)
                  const nodes = Array.from(ul.querySelectorAll("[data-emp-id]"));

                  const out = [];
                  for (const n of nodes) {
                    const id = Number(n.getAttribute("data-emp-id"));
                    if (!Number.isFinite(id) || id <= 0) continue;

                    // IMPORTANT FIX:
                    // Your markup usually has data-emp-id on IMG but assigned-by/date on LI.
                    // So we read from: n, closest LI, and parent.
                    const li = n.closest("li");
                    const parent = n.parentElement;

                    const assignedBy =
                      readAttrChain(n, "data-assigned-by", "assignedBy") ||
                      readAttrChain(li, "data-assigned-by", "assignedBy") ||
                      readAttrChain(parent, "data-assigned-by", "assignedBy");

                    const assignedAt =
                      readAttrChain(n, "data-assigned-at", "assignedAt") ||
                      readAttrChain(li, "data-assigned-at", "assignedAt") ||
                      readAttrChain(parent, "data-assigned-at", "assignedAt");

                    const position =
                      readAttrChain(n, "data-position", "position") ||
                      readAttrChain(li, "data-position", "position") ||
                      readAttrChain(parent, "data-position", "position");

                    const stage =
                      readAttrChain(n, "data-stage", "stage") ||
                      readAttrChain(li, "data-stage", "stage") ||
                      readAttrChain(parent, "data-stage", "stage");

                    const stageLabel =
                      readAttrChain(n, "data-stage-label", "stageLabel") ||
                      readAttrChain(li, "data-stage-label", "stageLabel") ||
                      readAttrChain(parent, "data-stage-label", "stageLabel") ||
                      getContextStage(ul);

                    out.push({ id, assignedBy, assignedAt, position, stage, stageLabel });
                  }
                  return out;
                }

                function uniqueById(list) {
                  const seen = new Set();
                  const out = [];
                  for (const it of list) {
                    if (seen.has(it.id)) continue;
                    seen.add(it.id);
                    out.push(it);
                  }
                  return out;
                }

                // 1) Your buildRow is OK now (it WILL show phase) ✅
                  // The missing part is: you must PASS stage / stageLabel into buildRow
                  // from the DOM (data-* attrs) OR from the API payload (team_assignments).

                  function buildRow({ id, assignedBy, assignedAt, position, stage, stageLabel }) {
                    const emp = byId.get(Number(id)) || null;

                    const name = emp ? `${emp.lastname || ""} ${emp.name || ""}`.trim() : `#${id}`;
                    const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;

                    const role =
                      (position && String(position).trim()) ||
                      (emp?.position ? String(emp.position) : "") ||
                      (emp?.role ? String(emp.role) : "") ||
                      "Mitarbeiter";

                    const by = (assignedBy && String(assignedBy).trim()) || "–";
                    const when = fmtDE(assignedAt);

                    const stageText =
                      (stageLabel && String(stageLabel).trim()) ||
                      (stage && String(stage).trim()) ||
                      "–";

                    return `
                      <div class="team-popover__item">
                        <img class="team-popover__avatar" src="${esc(img)}" alt="${esc(name)}">
                        <div style="min-width:0;">
                          <div class="team-popover__name">${esc(name)}</div>
                          <div class="team-popover__meta">${esc(role)}</div>

                          <div class="team-popover__meta">
                            <strong>Phase:</strong> ${esc(stageText)}
                          </div>

                          <div class="team-popover__meta">
                            <strong>Zugewiesen von:</strong> ${esc(by)}
                            <span style="padding:0 6px;">•</span>
                            <strong><i class="feather icon-calendar"></i></strong> ${esc(when)}
                          </div>
                        </div>
                      </div>
                    `;
                  }

                  // 2) Build popover rows from EACH avatar <li> dataset (this is what makes Phase show)
                  function rowsFromTeamEl(teamEl) {
                    const lis = Array.from(teamEl.querySelectorAll('li[data-emp-id]'));
                    return lis.map((li) => ({
                      id: li.dataset.empId,
                      assignedBy: li.dataset.assignedBy,     // must exist on li
                      assignedAt: li.dataset.assignedAt,     // must exist on li
                      position: li.dataset.position,
                      stage: li.dataset.stage,              // must exist on li
                      stageLabel: li.dataset.stageLabel,    // must exist on li (German label)
                    }));
                  }

                  // Example usage inside your hover/open logic:
                  function renderTeamPopover(teamEl, popoverEl) {
                    const rows = rowsFromTeamEl(teamEl);
                    popoverEl.innerHTML = rows.map(buildRow).join("") || `<div class="team-popover__empty">–</div>`;
                    if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
                  }



                function renderFor(ul) {
                  const p = ensurePop();
                  const listEl = p.querySelector("[data-list]");
                  const subEl = p.querySelector("[data-subline]");

                  const stageLabel = getContextStage(ul);
                  const avatars = uniqueById(collectAvatars(ul));

                  const countText = `${avatars.length} Mitglied${avatars.length === 1 ? "" : "er"}`;
                  subEl.textContent = stageLabel ? `${countText} • Phase: ${stageLabel}` : countText;

                  if (!avatars.length) {
                    listEl.innerHTML = `
                      <div class="team-popover__item">
                        <div style="min-width:0;">
                          <div class="team-popover__name">Kein Team</div>
                          <div class="team-popover__meta">—</div>
                        </div>
                      </div>
                    `;
                    return;
                  }

                  listEl.innerHTML = avatars.map(buildRow).join("");
                }

                function placeNear(el) {
                  const p = ensurePop();
                  const r = el.getBoundingClientRect();

                  const pw = p.offsetWidth || 320;
                  const ph = p.offsetHeight || 220;

                  const pad = 12;
                  const vw = window.innerWidth;
                  const vh = window.innerHeight;

                  let left = r.left + r.width / 2 - pw / 2;
                  let top = r.top - ph - 10;

                  left = Math.max(pad, Math.min(left, vw - pw - pad));
                  if (top < pad) top = r.bottom + 10;
                  if (top + ph > vh - pad) top = Math.max(pad, vh - ph - pad);

                  p.style.left = `${Math.round(left)}px`;
                  p.style.top = `${Math.round(top)}px`;
                }

                function openFor(ul) {
                  if (!ul) return;
                  hideTimer && clearTimeout(hideTimer);
                  anchor = ul;

                  renderFor(ul);
                  placeNear(ul);

                  ensurePop().classList.add("is-open");
                }

                function closeNow() {
                  if (!pop) return;
                  pop.classList.remove("is-open");
                  anchor = null;
                }

                function scheduleHide() {
                  hideTimer && clearTimeout(hideTimer);
                  hideTimer = setTimeout(closeNow, 120);
                }

                function getTeamTarget(node) {
                  return node?.closest ? node.closest("ul[data-team-hover]") : null;
                }

                document.addEventListener(
                  "mouseover",
                  (e) => {
                    const ul = getTeamTarget(e.target);
                    if (!ul) return;

                    const from = e.relatedTarget;
                    if (from && ul.contains(from)) return;

                    if (anchor === ul && pop?.classList.contains("is-open")) return;
                    openFor(ul);
                  },
                  true
                );

                document.addEventListener(
                  "mouseout",
                  (e) => {
                    if (!anchor) return;

                    const to = e.relatedTarget;
                    if (to && (anchor.contains(to) || (pop && pop.contains(to)))) return;

                    scheduleHide();
                  },
                  true
                );

                window.addEventListener(
                  "scroll",
                  () => {
                    if (anchor && pop?.classList.contains("is-open")) placeNear(anchor);
                  },
                  true
                );

                window.addEventListener("resize", () => {
                  if (anchor && pop?.classList.contains("is-open")) placeNear(anchor);
                });

                document.addEventListener("keydown", (e) => {
                  if (e.key === "Escape") closeNow();
                });
              })();
          

/* ===================== Extracted inline script block #19 ===================== */
          (function () {
            "use strict";

            const root  = window.LeadUI || {};
            const APP   = root.APP || {};
            const net   = root.net || {};
            const utils = root.utils || {};

            const qs  = (s, el=document) => el.querySelector(s);
            const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));

            const esc = (s) => String(s ?? "").replace(/[&<>"]/g, (m) => ({ "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;" }[m]));
            const dateOnly = (v) => (v ? String(v).slice(0, 10) : "");
            const timeOnly = (v) => (v ? String(v).slice(0, 8) : "");
            const isZeroTime = (v) => !v || String(v).startsWith("00:00");
            const addDays = (yyyy_mm_dd, days) => {
              if (!yyyy_mm_dd) return "";
              const d = new Date(yyyy_mm_dd + "T00:00:00");
              d.setDate(d.getDate() + (days || 0));
              return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,"0")}-${String(d.getDate()).padStart(2,"0")}`;
            };

            const EMP_IMG_BASE = (window.__EMP_IMG_BASE || "/images/employee").replace(/\/+$/, "");
            const employeeImageUrl = (img) => {
              if (!img) return "";
              if (/^https?:\/\//i.test(img)) return img;
              return EMP_IMG_BASE + "/" + String(img).replace(/^\/+/, "");
            };

            const EMP_ENDPOINT = APP?.endpoints?.getAllEmployees || "/getAllEmployees";


            function resolveEmployeeCalendarUrl(empId) {
              if (APP?.endpoints?.employeeCalendar && typeof APP.endpoints.employeeCalendar === "function") {
                return APP.endpoints.employeeCalendar(empId);
              }
              const sel = qs("#ap-emp-filter");
              const base = sel?.dataset?.empCalBase || "";
              if (base) return String(base).replace(/\/+$/, "") + "/" + encodeURIComponent(empId);
              return "/get_employee_calendar/" + encodeURIComponent(empId);
            }

            function safeJsonFetch(url) {
              return fetch(url, {
                method: "GET",
                credentials: "same-origin",
                headers: { "Accept": "application/json" }
              }).then(async (r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
              });
            }

            // small styles
            (function injectStyles() {
              if (qs("#ap-unified-style")) return;
              const st = document.createElement("style");
              st.id = "ap-unified-style";
              st.textContent = `
                .ap-loading-mask{position:absolute;inset:0;background:rgba(255,255,255,.72);display:flex;align-items:center;justify-content:center;z-index:50;border-radius:6px;}
                .ap-loading-mask .spin{width:18px;height:18px;border-radius:999px;border:2px solid rgba(0,0,0,.12);border-top-color:rgba(0,0,0,.55);animation:apspin 1s linear infinite;}
                @keyframes apspin{to{transform:rotate(360deg)}}
                .ap-creator-line{font-size:11px;opacity:.85;margin-top:2px;display:flex;gap:6px;align-items:center;}
                .ap-emp-avatars{display:flex;gap:6px;flex-wrap:wrap;align-items:center;margin-top:4px;}
                .ap-emp-pill{display:inline-flex;align-items:center;gap:6px;padding:2px 8px 2px 2px;border-radius:999px;border:1px solid rgba(0,0,0,.10);background:#fff;}
                .ap-emp-pill .av{width:22px;height:22px;border-radius:999px;overflow:hidden;border:1px solid rgba(0,0,0,.12);display:inline-flex;align-items:center;justify-content:center;background:#f8f9fa;}
                .ap-emp-pill .av img{width:100%;height:100%;object-fit:cover;display:block;}
                .ap-emp-pill .fb{font-size:10px;font-weight:900;opacity:.75;}
                .ap-emp-pill .nm{font-size:12px;font-weight:700;}
                .ap-source-badge{font-size:10px;font-weight:900;padding:2px 8px;border-radius:999px;border:1px solid rgba(0,0,0,.12);opacity:.85;}
              `;
              document.head.appendChild(st);
            })();

            const UI = {
              _ctx: {},
              _calendar: null,

              _employees: [],
              _selectedEmpId: "",

              // mode:
              // - "customer" => load customer appointmentsIndex
              // - "employee" => load ONLY employee calendar endpoint
              _mode: "customer",

              _customerAppointments: [],
              _employeeEvents: [],
              _rendered: [],

              open(customerId, alternativeId, productId, title, contact) {
                this._ctx = { customerId: customerId || "", alternativeId: alternativeId || "", productId: productId || "" };

                const titleEl = qs("#ap-title");
                if (titleEl) titleEl.textContent = title || "Termine";

                this.resetForm();
                this.prefillFromContact(contact || {});

                qs("#ap-backdrop")?.classList.add("show");
                qs("#ap-drawer")?.classList.add("open");
                document.body.style.overflow = "hidden";

                this.switchTab("calendar");
                this.switchView("calendar");

                this.ensureEmployeesLoaded()
                  .then(() => {
                    this.initSelect2ForFormEmployees();
                    return this.onEmpFilterChanged(true);
                  })
                  .catch(() => this.onEmpFilterChanged(true));
              },

              hide() {
                qs("#ap-backdrop")?.classList.remove("show");
                qs("#ap-drawer")?.classList.remove("open");
                document.body.style.overflow = "";
              },

              switchTab(tab) {
                qsa(".ap-tab-link").forEach(b => b.classList.toggle("active", b.dataset.tab === tab));
                qsa(".ap-tab-content").forEach(c => c.classList.remove("active"));
                const target = qs(tab === "calendar" ? "#ap-tab-calendar" : "#ap-tab-form");
                if (target) target.classList.add("active");
                if (tab === "calendar") setTimeout(() => { this.initCalendar(); this.refreshCalendar(); }, 150);
              },

              switchView(viewName) {
                qsa("[data-view]").forEach(btn => {
                  const active = btn.dataset.view === viewName;
                  btn.classList.toggle("active", active);
                  btn.classList.toggle("btn-outline-primary", active);
                  btn.classList.toggle("btn-outline-secondary", !active);
                });

                const calWrap = qs("#ap-calendar-wrap");
                const cardWrap = qs("#ap-card-view");
                if (calWrap) calWrap.style.display = viewName === "calendar" ? "block" : "none";
                if (cardWrap) cardWrap.style.display = viewName === "cards" ? "grid" : "none";
                if (viewName === "calendar") setTimeout(() => this.refreshCalendar(), 120);
              },

              showLoading(on) {
                const wrap = qs("#ap-calendar-wrap");
                if (!wrap) return;

                let mask = qs("#ap-loading-mask");
                if (on) {
                  if (!mask) {
                    mask = document.createElement("div");
                    mask.id = "ap-loading-mask";
                    mask.className = "ap-loading-mask";
                    mask.innerHTML = `<div class="spin"></div>`;
                    wrap.style.position = "relative";
                    wrap.appendChild(mask);
                  }
                } else {
                  mask?.remove();
                }
              },

              /* =========================
               * EMPLOYEES (filter + form select2)
               * =======================*/
              async ensureEmployeesLoaded() {
                const sel = qs("#ap-emp-filter");
                if (!sel) return;
                if (this._employees.length) return;

                const res = await net.safeFetchJSON(EMP_ENDPOINT, { retries: 0 });
                const rows = Array.isArray(res) ? res : (Array.isArray(res?.employees) ? res.employees : []);

                this._employees = rows.map(e => ({
                  id: String(e.emp_id ?? e.id ?? ""),
                  name: e.name || "",
                  lastname: e.lastname || "",
                  image: e.image || null
                })).filter(e => e.id);

                // IMPORTANT: empty option means "customer calendar"
                sel.innerHTML =
                  `<option value="">Aktueller Kunde (Termine)</option>` +
                  this._employees
                    .slice()
                    .sort((a,b) => (a.lastname||"").localeCompare(b.lastname||""))
                    .map(e => `<option value="${esc(e.id)}">${esc((e.lastname + " " + e.name).trim())}</option>`)
                    .join("");

                // If filter select2 exists, attach hooks
                if (window.jQuery) {
                  const $sel = window.jQuery(sel);
                  if ($sel.length && !$sel.data("select2")) {
                    $sel.select2({
                      placeholder: "Aktueller Kunde (Termine)",
                      allowClear: true,
                      dropdownParent: window.jQuery("#ap-drawer"),
                      width: "100%"
                    });
                    $sel.on("select2:select select2:clear", () => this.onEmpFilterChanged(false));
                  }

                  const $jump = window.jQuery("#ap-jump");
                  if ($jump.length && !$jump.data("select2")) {
                    $jump.select2({
                      placeholder: "Termin auswählen…",
                      allowClear: true,
                      dropdownParent: window.jQuery("#ap-drawer"),
                      width: "100%"
                    });
                  }
                }
              },

              initSelect2ForFormEmployees() {
                if (!window.jQuery) return;
                const el = qs("#ap-employee_ids");
                if (!el) return;

                const $el = window.jQuery(el);
                if ($el.data("select2")) return;

                $el.select2({
                  placeholder: "Mitarbeiter wählen…",
                  dropdownParent: window.jQuery("#ap-drawer"),
                  width: "100%"
                });
              },

              getEmpFilterValue() {
                const sel = qs("#ap-emp-filter");
                if (!sel) return "";
                return String(sel.value || "");
              },

              async onEmpFilterChanged(isFirstOpen) {
                const empId = this.getEmpFilterValue();
                this._selectedEmpId = empId;

                // Always return to calendar view
                this.switchTab("calendar");
                this.switchView("calendar");
                this.initCalendar();

                if (empId) {
                  // EMPLOYEE MODE => only employee calendar
                  this._mode = "employee";
                  await this.loadEmployeeCalendar(empId);
                } else {
                  // CUSTOMER MODE => customer appointments
                  this._mode = "customer";
                  await this.loadCustomerAppointments();
                }

                this.renderCalendarEvents();
                this.renderCardList();
                this.populateJumpDropdown();
                this.updateCount();

                setTimeout(() => this.refreshCalendar(), 160);
              },

              /* =========================
               * DATA LOADERS
               * =======================*/
              async loadCustomerAppointments() {
                const { customerId, alternativeId, productId } = this._ctx;
                if (!customerId || !APP?.endpoints?.appointmentsIndex) {
                  this._customerAppointments = [];
                  this._rendered = [];
                  return;
                }

                const url =
                  `${APP.endpoints.appointmentsIndex}?customer_id=${encodeURIComponent(customerId)}` +
                  (alternativeId ? `&alternative_id=${encodeURIComponent(alternativeId)}` : "") +
                  (productId ? `&product_id=${encodeURIComponent(productId)}` : "");

                this.showLoading(true);
                try {
                  const res = await net.safeFetchJSON(url, { retries: 0 });
                  const list = Array.isArray(res?.appointments) ? res.appointments : (Array.isArray(res) ? res : []);

                  this._customerAppointments = list.map(a => ({
                    ...a,
                    __creator_label:
                      (typeof a.created_by === "string" ? a.created_by : null) ||
                      a.created_by_name ||
                      a.creator_name ||
                      (a.created_by ? `User #${a.created_by}` : "")
                  }));

                  this._rendered = this._customerAppointments.map(a => this.mapLeadAppointment(a));
                } catch (e) {
                  this._customerAppointments = [];
                  this._rendered = [];
                  window.Swal?.fire("Fehler", "Kunden-Termine konnten nicht geladen werden.", "error");
                } finally {
                  this.showLoading(false);
                }
              },

              async loadEmployeeCalendar(empId) {
                const url = resolveEmployeeCalendarUrl(empId);

                this.showLoading(true);
                try {
                  const json = await safeJsonFetch(url);
                  const rows = Array.isArray(json?.data) ? json.data : [];

                  this._employeeEvents = rows;
                  this._rendered = rows.map(r => this.mapEmployeeEvent(r));
                } catch (e) {
                  this._employeeEvents = [];
                  this._rendered = [];
                  window.Swal?.fire("Fehler", "Mitarbeiter-Kalender konnte nicht geladen werden.", "error");
                } finally {
                  this.showLoading(false);
                }
              },

              /* =========================
               * NORMALIZERS
               * =======================*/
              mapLeadAppointment(a) {
                const sd = dateOnly(a.start_date);
                const ed = dateOnly(a.end_date) || sd;
                const st = timeOnly(a.start_time);
                const et = timeOnly(a.end_time);

                const allDay = !st || isZeroTime(st);

                let start, end;
                if (allDay) {
                  start = sd;
                  end = addDays(ed, 1);
                } else {
                  start = `${sd}T${st || "00:00:00"}`;
                  end   = `${ed}T${et || st || "23:59:59"}`;
                }

                const emps = Array.isArray(a.employees) ? a.employees : [];
                const employees = emps.map(e => {
                  const full = ((e.lastname ? e.lastname + " " : "") + (e.name || "")).trim();
                  return { id: String(e.id), full, initials: (e.lastname || e.name || "?").slice(0,2).toUpperCase(), image: e.image ? employeeImageUrl(e.image) : "" };
                });

                return {
                  _source: "customer",
                  _raw: a,
                  id: `lead-${a.id}`,
                  title: a.name || "Termin",
                  start, end, allDay,
                  color: a.color || "#74b2d4",
                  creator_label: a.__creator_label || "",
                  description: a.note || "",
                  type: "appointment",
                  employees
                };
              },

              mapEmployeeEvent(r) {
                const sd = dateOnly(r.start_date);
                const ed = dateOnly(r.end_date) || sd;
                const st = timeOnly(r.start_time);
                const et = timeOnly(r.end_time);

                const allDay = isZeroTime(st) && isZeroTime(et);

                let start, end;
                if (allDay) {
                  start = sd;
                  end = addDays(ed, 1);
                } else {
                  start = `${sd}T${st || "00:00:00"}`;
                  end   = `${ed}T${et || "23:59:59"}`;
                }

                const emps = Array.isArray(r.employees) ? r.employees : [];
                const employees = emps.map(e => {
                  const full = ((e.lastname ? e.lastname + " " : "") + (e.name || "")).trim();
                  return { id: String(e.employee_id), full, initials: (e.lastname || e.name || "?").slice(0,2).toUpperCase(), image: e.image ? employeeImageUrl(e.image) : "" };
                });

                return {
                  _source: "employee",
                  _raw: r,
                  id: `${r.type || "event"}-${r.id}`,
                  title: r.title || "Eintrag",
                  start, end, allDay,
                  color: r.taskColor || "#74b2d4",
                  creator_label: "",
                  description: r.description || "",
                  type: r.type || "event",
                  employees
                };
              },

              /* =========================
               * CALENDAR
               * =======================*/
              initCalendar() {
                const calEl = qs("#ap-fullcalendar");
                if (!calEl || !window.FullCalendar) return;
                if (this._calendar) return;

                this._calendar = new FullCalendar.Calendar(calEl, {
                  locale: "de",
                  initialView: "dayGridMonth",
                  headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek,listWeek" },
                  height: "100%",
                  navLinks: true,
                  editable: false,
                  dayMaxEvents: true,
                  events: [],

                  eventContent: (arg) => {
                    const p = arg.event.extendedProps || {};
                    const title = esc(arg.event.title || "");
                    const creator = p.creator_label ? `<div class="ap-creator-line"><i class="feather icon-user" style="font-size:12px;"></i><span>${esc(p.creator_label)}</span></div>` : "";

                    const emps = Array.isArray(p.employees) ? p.employees : [];
                    const avatars = emps.length ? `
                      <div class="ap-emp-avatars">
                        ${emps.slice(0,4).map(e => {
                          const nm = esc(e.full || "");
                          const img = e.image ? esc(e.image) : "";
                          const fb  = esc((e.initials || "?").slice(0,2));
                          return `<span title="${nm}" style="width:18px;height:18px;border-radius:999px;overflow:hidden;border:1px solid rgba(0,0,0,.12);display:inline-flex;align-items:center;justify-content:center;background:#fff;">
                            ${img ? `<img src="${img}" alt="${nm}" style="width:100%;height:100%;object-fit:cover;display:block;">` : `<span style="font-size:9px;font-weight:900;opacity:.75;">${fb}</span>`}
                          </span>`;
                        }).join("")}
                        ${emps.length > 4 ? `<span style="font-size:10px;font-weight:900;opacity:.8;">+${emps.length-4}</span>` : ""}
                      </div>` : "";

                    return { html: `<div><div style="font-weight:800;font-size:12px;">${title}</div>${creator}${avatars}</div>` };
                  },

                  dateClick: (info) => {
                    // ALWAYS allow creation (even if employee is selected)
                    this.resetForm();

                    // preset date
                    qs("#ap-start_date") && (qs("#ap-start_date").value = info.dateStr);

                    // preset selected employee into appointment employees
                    this.preselectEmployeeIntoForm();

                    qs("#ap-form-title") && (qs("#ap-form-title").textContent =
                      "Neuer Termin am " + new Date(info.dateStr + "T00:00:00").toLocaleDateString("de-DE")
                    );

                    this.switchTab("form");
                  },

                  eventClick: (info) => {
                    const id = String(info.event.id || "");
                    // only lead appointments are editable
                    if (id.startsWith("lead-")) {
                      this.fillForm(id.replace("lead-",""));
                      this.switchTab("form");
                    } else {
                      this.switchView("cards");
                    }
                  }
                });

                this._calendar.render();
              },

              refreshCalendar() {
                if (!this._calendar) return;
                try { this._calendar.updateSize(); } catch(_) {}
              },

              renderCalendarEvents() {
                if (!this._calendar) return;

                const events = (this._rendered || []).map(ev => ({
                  id: ev.id,
                  title: ev.title,
                  start: ev.start,
                  end: ev.end,
                  allDay: !!ev.allDay,
                  backgroundColor: ev.color,
                  borderColor: ev.color,
                  extendedProps: {
                    creator_label: ev.creator_label || "",
                    employees: ev.employees || [],
                    raw: ev._raw
                  }
                }));

                this._calendar.removeAllEvents();
                this._calendar.addEventSource(events);

                // if employee mode, jump to first event date
                if (this._mode === "employee" && this._employeeEvents?.length) {
                  const first = dateOnly(this._employeeEvents[0]?.start_date);
                  if (first) this._calendar.gotoDate(first);
                }
              },

              /* =========================
               * LIST VIEW + JUMP + COUNT
               * =======================*/
              updateCount() {
                const el = qs("#ap-count");
                if (el) el.textContent = String((this._rendered || []).length);
              },

              populateJumpDropdown() {
                const sel = qs("#ap-jump");
                if (!sel) return;

                const list = (this._rendered || []).slice().sort((a,b) => String(b.start).localeCompare(String(a.start)));
                sel.innerHTML =
                  `<option value="">— Termin auswählen (Springen) —</option>` +
                  list.map(ev => {
                    const d = String(ev.start || "").slice(0,10);
                    const dateLabel = d ? d.split("-").reverse().join(".") : "";
                    const timeLabel = ev.allDay ? " Ganztägig" : (" " + String(ev.start).slice(11,16));
                    return `<option value="${esc(ev.id)}">${esc(dateLabel + timeLabel)} — ${esc(ev.title)}</option>`;
                  }).join("");
              },

              jumpToEvent(eventId) {
                const ev = (this._rendered || []).find(x => String(x.id) === String(eventId));
                if (!ev || !this._calendar) return;

                const d = String(ev.start || "").slice(0,10);
                if (!d) return;

                this.switchView("calendar");
                this.switchTab("calendar");

                setTimeout(() => {
                  this._calendar.gotoDate(d);
                  if (String(ev.id).startsWith("lead-")) {
                    this.fillForm(String(ev.id).replace("lead-",""));
                    this.switchTab("form");
                  }
                }, 160);
              },

              renderCardList() {
                const wrap = qs("#ap-card-view");
                if (!wrap) return;

                const list = (this._rendered || []);
                if (!list.length) {
                  wrap.innerHTML = '<div class="text-center text-muted col-12 small my-3">Keine Einträge gefunden.</div>';
                  return;
                }

                wrap.innerHTML = list.map(ev => {
                  const d = String(ev.start || "").slice(0,10);
                  const date = d ? d.split("-").reverse().join(".") : "";

                  const time = ev.allDay
                    ? "Ganztägig"
                    : `${String(ev.start).slice(11,16)} – ${String(ev.end || "").slice(11,16)}`;

                  const creator = ev.creator_label ? `
                    <div class="small text-muted" style="margin-top:2px;">
                      <i class="feather icon-user" style="font-size:12px;"></i>
                      <span class="ml-1">${esc(ev.creator_label)}</span>
                    </div>` : "";

                  const empPills = (ev.employees || []).map(e => {
                    const full = e.full || "Mitarbeiter";
                    const img  = e.image || "";
                    const fb   = esc((e.initials || "?").slice(0,2));
                    return `
                      <span class="ap-emp-pill" title="${esc(full)}">
                        <span class="av">
                          ${img ? `<img src="${esc(img)}" alt="${esc(full)}">` : `<span class="fb">${fb}</span>`}
                        </span>
                        <span class="nm">${esc(full)}</span>
                      </span>`;
                  }).join("");

                  return `
                    <article class="ap-card" style="cursor:pointer" onclick="AppointmentsUI.jumpToEvent('${esc(ev.id)}')">
                      <div class="ap-color" style="background:${esc(ev.color || "#74b2d4")};"></div>
                      <div class="ap-main">
                        <div class="d-flex justify-content-between">
                          <div class="ap-title font-weight-bold">${esc(ev.title)}</div>
                          <div class="text-muted" style="font-size:10px;"><i class="feather icon-calendar"></i> ${esc(date)}</div>
                        </div>
                        <div class="text-muted small mb-1">${esc(time)}</div>
                        ${creator}
                        <div class="ap-note small text-muted mb-2" style="line-height:1.2;">${esc(ev.description || "").slice(0,110)}</div>
                        <div class="ap-emp-avatars">${empPills}</div>
                      </div>
                    </article>
                  `;
                }).join("");
              },

              /* =========================
               * FORM (create/edit lead appointments)
               * =======================*/
              preselectEmployeeIntoForm() {
                const empId = String(this._selectedEmpId || "");
                if (!empId) return;

                // ensure select2 exists
                this.initSelect2ForFormEmployees();

                if (!window.jQuery || !qs("#ap-employee_ids")) return;

                const $sel = window.jQuery("#ap-employee_ids");
                const existing = $sel.val() || [];
                if (!existing.includes(empId)) {
                  $sel.val([...existing, empId]).trigger("change");
                }
              },

              resetForm() {
                const form = qs("#ap-form");
                if (!form) return;
                form.reset();

                qs("#ap-form-title") && (qs("#ap-form-title").textContent = "Neuer Termin");

                const delBtn = qs("#ap-btn-delete");
                if (delBtn) { delBtn.classList.add("d-none"); delBtn.onclick = null; }

                qs("#ap-customer_id") && (qs("#ap-customer_id").value = this._ctx.customerId);
                qs("#ap-alternative_id") && (qs("#ap-alternative_id").value = this._ctx.alternativeId);
                qs("#ap-product_id") && (qs("#ap-product_id").value = this._ctx.productId);
                qs("#ap-id") && (qs("#ap-id").value = "");

                qs("#ap-color") && (qs("#ap-color").value = "#74b2d4");

                if (window.jQuery) window.jQuery("#ap-employee_ids").val(null).trigger("change");
              },

              fillForm(id) {
                const appt = (this._customerAppointments || []).find(x => String(x.id) === String(id));
                if (!appt) return;

                this.resetForm();
                qs("#ap-form-title") && (qs("#ap-form-title").textContent = "Termin bearbeiten");

                const delBtn = qs("#ap-btn-delete");
                if (delBtn) {
                  delBtn.classList.remove("d-none");
                  delBtn.onclick = () => this.delete(id);
                }

                const set = (sel, val) => { const el = qs(sel); if (el) el.value = val ?? ""; };

                set("#ap-id", appt.id);
                set("#ap-name", appt.name);
                set("#ap-note", appt.note);

                set("#ap-start_date", dateOnly(appt.start_date));
                set("#ap-start_time", timeOnly(appt.start_time).slice(0,5));
                set("#ap-end_time", timeOnly(appt.end_time).slice(0,5));

                set("#ap-appointment_type", appt.appointment_type);
                set("#ap-contact_mode", appt.contact_mode);
                set("#ap-priority", appt.priority || "normal");
                set("#ap-color", appt.color || "#74b2d4");

                set("#ap-full_address", appt.full_address);
                set("#ap-street", appt.street);
                set("#ap-postcode", appt.postcode);
                set("#ap-city", appt.city);

                this.initSelect2ForFormEmployees();
                if (window.jQuery && Array.isArray(appt.employees)) {
                  const ids = appt.employees.map(e => String(e.id));
                  window.jQuery("#ap-employee_ids").val(ids).trigger("change");
                }
              },

              prefillFromContact(contact) {
                if (!contact) return;
                const map = {
                  full_address:"#ap-full_address",
                  street:"#ap-street",
                  postcode:"#ap-postcode",
                  city:"#ap-city",
                  latitude:"#ap-latitude",
                  longitude:"#ap-longitude"
                };
                for (const [k, sel] of Object.entries(map)) {
                  if (contact[k]) { const el = qs(sel); if (el) el.value = contact[k]; }
                }
              },

              async submitForm(ev) {
                ev.preventDefault();

                const name = qs("#ap-name")?.value.trim();
                const startDate = qs("#ap-start_date")?.value;
                const customerId = qs("#ap-customer_id")?.value;

                if (!name || !startDate || !customerId) {
                  window.Swal?.fire("Fehler", "Titel und Datum sind Pflichtfelder.", "error");
                  return;
                }

                const payload = {
                  customer_id: customerId,
                  alternative_id: qs("#ap-alternative_id")?.value || null,
                  product_id: qs("#ap-product_id")?.value || null,
                  name,
                  note: qs("#ap-note")?.value || null,
                  start_date: startDate,
                  start_time: qs("#ap-start_time")?.value || null,
                  end_time: qs("#ap-end_time")?.value || null,
                  appointment_type: qs("#ap-appointment_type")?.value || null,
                  contact_mode: qs("#ap-contact_mode")?.value || null,
                  priority: qs("#ap-priority")?.value || "normal",
                  color: qs("#ap-color")?.value || "#74b2d4",
                  full_address: qs("#ap-full_address")?.value || null,
                  street: qs("#ap-street")?.value || null,
                  postcode: qs("#ap-postcode")?.value || null,
                  city: qs("#ap-city")?.value || null,
                  latitude: qs("#ap-latitude")?.value || null,
                  longitude: qs("#ap-longitude")?.value || null,
                  employee_ids: window.jQuery ? (window.jQuery("#ap-employee_ids").val() || []) : []
                };

                const id = qs("#ap-id")?.value || "";
                const isEdit = !!id;
                const url = isEdit ? APP.endpoints.appointmentsUpdate(id) : APP.endpoints.appointmentsStore;
                const method = isEdit ? "PUT" : "POST";

                try {
                  const res = await net.safeFetchJSON(url, {
                    method,
                    headers: {
                      "Content-Type": "application/json",
                      "X-CSRF-TOKEN": (utils.CSRF ? utils.CSRF() : "")
                    },
                    body: JSON.stringify(payload)
                  });

                  if (res?.success !== false) {
                    window.Swal?.fire("Gespeichert", isEdit ? "Termin aktualisiert." : "Termin angelegt.", "success");

                    // after save: reload CUSTOMER calendar (since appointment belongs to customer context)
                    await this.loadCustomerAppointments();

                    // keep employee filter selection as is, but rendering should match mode:
                    // - if employee selected => show employee calendar (not customer)
                    // - if empty => show customer calendar
                    await this.onEmpFilterChanged(false);

                    this.switchTab("calendar");
                  } else {
                    throw new Error(res?.message || "Fehler beim Speichern.");
                  }
                } catch (err) {
                  window.Swal?.fire("Fehler", err.message || "Fehler beim Speichern.", "error");
                }
              },

              async delete(id) {
                const ok = window.Swal
                  ? await Swal.fire({ title:"Löschen?", text:"Wirklich löschen?", icon:"warning", showCancelButton:true })
                  : { isConfirmed: confirm("Wirklich löschen?") };

                if (!ok.isConfirmed) return;

                try {
                  await net.safeFetchJSON(APP.endpoints.appointmentsDestroy(id), {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": (utils.CSRF ? utils.CSRF() : "") }
                  });

                  window.Swal?.fire("Gelöscht", "Termin entfernt.", "success");

                  await this.loadCustomerAppointments();
                  await this.onEmpFilterChanged(false);
                  this.switchTab("calendar");
                } catch (err) {
                  window.Swal?.fire("Fehler", err.message || "Fehler beim Löschen.", "error");
                }
              }
            };

            // delegated events (works with ajax-injected drawer too)
            if (!window.__AP_UNIFIED_BOUND) {
              window.__AP_UNIFIED_BOUND = true;

              document.addEventListener("click", (e) => {
                const t = e.target;
                if (t?.id === "ap-backdrop" || t?.closest?.("[data-ap-close]")) UI.hide();
              });

              document.addEventListener("click", (e) => {
                const btn = e.target?.closest?.(".ap-tab-link");
                if (btn) UI.switchTab(btn.dataset.tab);
              });

              document.addEventListener("click", (e) => {
                const btn = e.target?.closest?.("[data-view]");
                if (btn) UI.switchView(btn.dataset.view);
              });

              document.addEventListener("change", (e) => {
                if (e.target?.id === "ap-jump") {
                  const v = e.target.value;
                  if (v) UI.jumpToEvent(v);
                  e.target.value = "";
                }
                if (e.target?.id === "ap-emp-filter") UI.onEmpFilterChanged(false);
              });

              document.addEventListener("submit", (e) => {
                if (e.target?.id === "ap-form") UI.submitForm(e);
              });

              document.addEventListener("click", (e) => {
                if (e.target?.closest?.("#ap-btn-back-to-cal")) UI.switchTab("calendar");
              });

              document.addEventListener("transitionend", (e) => {
                const drawer = qs("#ap-drawer");
                if (drawer && e.target === drawer && drawer.classList.contains("open")) {
                  UI.initCalendar();
                  UI.refreshCalendar();
                }
              });
            }

            document.addEventListener("open-appointments", (e) => {
              const d = e.detail || {};
              UI.open(d.customerId, d.alternativeId, d.productId, d.title, d);
            });

            window.AppointmentsUI = UI;
          })();
          

/* ===================== Extracted inline script block #20 ===================== */
          (function () {
            "use strict";

            const BRANCH_COLOR_MAP = window.KANBAN_BOOT?.branchColorMap || {};

            const DEFAULT_COLOR = "#93c21c";
            const norm = (v) => (v ?? "").toString().trim().toLowerCase();

            function setImportant(el, prop, value) {
              if (!el) return;
              el.style.setProperty(prop, value, "important");
            }

            function pickBranchName(branchEl) {
              if (!branchEl) return "";
              const t = norm(branchEl.getAttribute("title"));
              if (t) return t;

              const nameEl = branchEl.querySelector(".kb-branch-name");
              const txt = norm(nameEl ? nameEl.textContent : branchEl.textContent);
              return txt;
            }

            function resolveColor(branchName) {
              const key = norm(branchName);
              return BRANCH_COLOR_MAP[key] || DEFAULT_COLOR;
            }

            function findCard(el) {
              // Your circle lives inside `.card`, so include that.
              return (
                el.closest(".kb-card") ||
                el.closest(".kanban-card") ||
                el.closest(".kb-item") ||
                el.closest(".card") ||
                el.closest("[data-lead-id]") ||
                el.closest("[data-id]") ||
                el.parentElement
              );
            }

            function paintCardCircle(card, color) {
              if (!card) return;

              // IMPORTANT: target product_circle specifically
              const circle =
                card.querySelector(".circle.product_circle") ||
                card.querySelector(".product_circle") ||
                card.querySelector(".circle");

              if (!circle) return;

              circle.style.setProperty("--branch-color", color);
              setImportant(circle, "background-color", color);
              setImportant(circle, "color", "#fff");
            }

            function paintBranch(branchEl) {
              const card = findCard(branchEl);
              const branchName = pickBranchName(branchEl);
              const color = resolveColor(branchName);

              // color branch label + svg
              branchEl.style.setProperty("--branch-color", color);
              setImportant(branchEl, "color", color);

              // color product circle in the same card
              paintCardCircle(card, color);
            }

            function paintCircle(circleEl) {
              // only force product circle (avoid random circles elsewhere)
              if (!circleEl.classList.contains("product_circle")) return;

              const card = findCard(circleEl);
              if (!card) return;

              const branchEl = card.querySelector(".kb-meta-item.kb-branch");
              const branchName = pickBranchName(branchEl);
              if (!branchName) return;

              const color = resolveColor(branchName);
              paintCardCircle(card, color);
            }

            function paintAll(root = document) {
              root.querySelectorAll(".kb-meta-item.kb-branch").forEach(paintBranch);
              root.querySelectorAll(".circle.product_circle, .product_circle").forEach(paintCircle);
            }

            document.addEventListener("DOMContentLoaded", () => paintAll());

            const container =
              document.querySelector("#kanban") ||
              document.querySelector(".kanban-board") ||
              document.body;

            const obs = new MutationObserver((mutations) => {
              for (const m of mutations) {
                if (!m.addedNodes) continue;
                m.addedNodes.forEach((node) => {
                  if (node && node.nodeType === 1) paintAll(node);
                });
              }
            });

            obs.observe(container, { childList: true, subtree: true });

            // optional manual trigger after your own render
            window.paintBranchColors = paintAll;
          })();


          document.addEventListener("click", function(e) {
              // Target pagination links specifically inside the Junk pane
              const paginationLink = e.target.closest("#junk .pagination a");

              if (paginationLink) {
                  e.preventDefault();

                  const url = paginationLink.getAttribute("href");
                  const junkPane = document.querySelector("#junk");

                  // Show a loading state
                  junkPane.style.opacity = '0.5';

                  fetch(url, {
                      headers: {
                          'X-Requested-With': 'XMLHttpRequest'
                      }
                  })
                  .then(response => response.text())
                  .then(html => {
                      // Update the content
                      junkPane.innerHTML = html;
                      junkPane.style.opacity = '1';

                      // Re-initialize any specific UI elements if needed
                      // e.g., if you have Tooltips or specific button styles
                      if (window.feather) {
                          feather.replace();
                      }

                      // Optional: Smooth scroll back to the top of the table
                      junkPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  })
                  .catch(error => {
                      console.error('Error loading junk pagination:', error);
                      junkPane.style.opacity = '1';
                  });
              }
          });

          

/* ===================== Extracted inline script block #21 ===================== */
          document.addEventListener("click", function(e) {
              const toggleBtn = e.target.closest(".toggle-feed-btn");
              if (toggleBtn) {
                  e.preventDefault();
                  // Find the closest row and toggle the feed visibility class
                  const row = toggleBtn.closest("tr.list-row-item");
                  if (row) {
                      const feed = row.querySelector(".list-live-feed");
                      if (feed) {
                          feed.classList.toggle("force-show-feed");
                      }
                  }
              }
          });
          

/* ===================== Extracted inline script block #22 ===================== */
          document.addEventListener("click", function(e) {
              // Look for a click inside the status block specifically inside the table
              const kbStatus = e.target.closest(".table .kb-status");

              if (kbStatus) {
                  e.preventDefault();
                  e.stopPropagation();

                  // Simply toggle the expanded state class
                  kbStatus.classList.toggle("is-expanded");
              }
          });
          

/* ===================== Extracted inline script block #23 ===================== */
            /* =========================================================
             * Dynamic Lead Stage Manager - restored + icon Select2 + drag/drop
             * ========================================================= */
            (function () {
              "use strict";

              const LeadUI = window.LeadUI || {};
              const APP_ROOT = LeadUI.APP || {};
              const APP = APP_ROOT.endpoints || APP_ROOT || {};

              const STAGE_ADMIN_BASE = "/task-phase/ajax/stage-admin";
              const PHASE_API = {
                index: `${STAGE_ADMIN_BASE}/stages`,
                store: `${STAGE_ADMIN_BASE}/stages`,
                update: (id) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(id)}/update`,
                destroy: (id) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(id)}`,
                reorder: `${STAGE_ADMIN_BASE}/stages/reorder`,
                show: (id) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(id)}`,
                options: `${STAGE_ADMIN_BASE}/stage-transfer/options`,
                bulkMoveSummary: `${STAGE_ADMIN_BASE}/stage-transfer/summary`,
                bulkMove: `${STAGE_ADMIN_BASE}/stage-transfer/move`,
              };
              APP.leadStagesIndex   = PHASE_API.index;
              APP.leadStagesStore   = PHASE_API.store;
              APP.leadStagesUpdate  = PHASE_API.update;
              APP.leadStagesDestroy = PHASE_API.destroy;
              APP.leadStagesReorder = PHASE_API.reorder;

              const qs = (s, ctx = document) => ctx.querySelector(s);
              const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
              const csrf = () => qs('meta[name="csrf-token"]')?.content || "";
              const esc = (v) => String(v ?? "").replace(/[&<>"']/g, (m) => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[m]));

              const modal = qs("#leadStageManagerModal");
              const panel = modal?.querySelector(".lsm-panel");
              const list = qs("#leadStagesList");
              const form = qs("#leadStageForm");
              const formTitle = qs("#lsmFormTitle");
              const idInput = qs("#lsmStageId");
              const nameInput = qs("#lsmStageName");
              const colorInput = qs("#lsmStageColor");
              const colorText = qs("#lsmStageColorText");
              const iconInput = qs("#lsmStageIcon");
              const activeInput = qs("#lsmStageActive");
              const closedInput = qs("#lsmStageClosed");

              const ICONS = [
                "circle", "user-plus", "users", "file-text", "phone-call", "check-circle", "briefcase", "tool", "flag", "archive", "trash-2", "clock", "calendar", "activity", "target", "send", "mail", "message-square", "clipboard", "list", "layers", "box", "truck", "home", "map-pin", "star", "award", "alert-triangle", "zap", "settings", "edit-2"
              ];

              let stages = [];
              let dragId = null;

              let selectedSubstageStageId = null;
              let substageDragId = null;

              const subStageApi = {
                create: (stageId) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(stageId)}/sub-stages`,
                update: (subId) => `${STAGE_ADMIN_BASE}/sub-stages/${encodeURIComponent(subId)}/update`,
                destroy: (subId) => `${STAGE_ADMIN_BASE}/sub-stages/${encodeURIComponent(subId)}`,
                reorder: (stageId) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(stageId)}/sub-stages/reorder`,
              };

              const subDrawer = qs("#lsmSubstageDrawer");
              const subTitle = qs("#lsmSubstageTitle");
              const subSubtitle = qs("#lsmSubstageSubtitle");
              const subStageIdInput = qs("#lsmSubstageStageId");
              const subNameInput = qs("#lsmSubstageName");
              const subKeyInput = qs("#lsmSubstageKey");
              const subColorInput = qs("#lsmSubstageColor");
              const subIconInput = qs("#lsmSubstageIcon");
              const subActiveInput = qs("#lsmSubstageActive");
              const subList = qs("#lsmSubstageList");

              function normalizeSubStages(stage) {
                if (!stage) return [];
                const raw = stage.sub_stages || stage.subStages || stage.active_sub_stages || stage.activeSubStages || [];
                return Array.isArray(raw) ? raw : [];
              }

              function normalizeStage(stage) {
                const copy = { ...(stage || {}) };
                copy.sub_stages = normalizeSubStages(copy);
                copy.sub_stage_count = copy.sub_stages.length || Number(copy.sub_stage_count || copy.sub_stages_count || copy.subStageCount || 0);
                return copy;
              }

              function setKanbanPhaseName(name, hint = "Aktuelle Ansicht") {
                // The global centered phase banner was removed by design.
                // Column headers now show each phase name directly at the top of every column.
              }

              function refreshIcons() {
                if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
              }

              function formatIconOption(state) {
                if (!state.id) return state.text;
                const span = document.createElement("span");
                span.className = "lsm-icon-option";
                span.innerHTML = `<i data-feather="${esc(state.id)}"></i><span>${esc(state.text)}</span>`;
                setTimeout(refreshIcons, 0);
                return span;
              }

              function initIconSelect() {
                if (!iconInput || iconInput.dataset.ready === "1") return;
                iconInput.innerHTML = ICONS.map((i) => `<option value="${esc(i)}">${esc(i)}</option>`).join("");
                iconInput.dataset.ready = "1";
                if (window.jQuery && window.jQuery.fn.select2) {
                  window.jQuery(iconInput).select2({
                    dropdownParent: window.jQuery("#leadStageManagerModal"),
                    width: "100%",
                    templateResult: formatIconOption,
                    templateSelection: formatIconOption,
                    minimumResultsForSearch: 0,
                  });
                }
                iconInput.value = "circle";
                if (window.jQuery) window.jQuery(iconInput).trigger("change.select2");
              }

              function setIconValue(value) {
                initIconSelect();
                iconInput.value = value || "circle";
                if (window.jQuery) window.jQuery(iconInput).val(iconInput.value).trigger("change");
                refreshIcons();
              }

              function updateColorText() {
                if (colorText && colorInput) colorText.textContent = colorInput.value || "#74b2d4";
              }

              function openModal() {
                if (!modal) return;
                modal.classList.add("is-open");
                modal.setAttribute("aria-hidden", "false");
                document.body.style.overflow = "hidden";
                initIconSelect();
                setTimeout(() => panel?.focus?.({ preventScroll: true }), 30);
                loadStages();
                refreshIcons();
              }

              function closeModal() {
                if (!modal) return;
                modal.classList.remove("is-open");
                modal.setAttribute("aria-hidden", "true");
                document.body.style.overflow = "";
                closeSubstageDrawer();
              }

              async function requestJSON(url, options = {}) {
                const res = await fetch(url, {
                  credentials: "same-origin",
                  headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrf(),
                    ...(options.headers || {}),
                  },
                  ...options,
                });
                const text = await res.text();
                let data = {};
                try { data = text ? JSON.parse(text) : {}; } catch { data = { message: text || "Ungültige Serverantwort." }; }
                if (!res.ok || data.success === false) {
                  const err = new Error(data.message || `HTTP ${res.status}`);
                  err.payload = data;
                  throw err;
                }
                return data;
              }

              function setSaveButtonLabel(label) {
                const btn = form?.querySelector(".lsm-save-btn");
                if (btn) btn.innerHTML = `<i class="feather icon-save"></i> ${esc(label || "Speichern")}`;
                refreshIcons();
              }

              function markEditingRow(stageId) {
                qsa("#leadStagesList [data-stage-id]").forEach((row) => {
                  row.classList.toggle("is-editing", String(row.dataset.stageId) === String(stageId || ""));
                });
              }

              function resetForm() {
                if (!form) return;
                idInput.value = "";
                form.dataset.mode = "create";
                delete form.dataset.stageId;
                delete form.dataset.stageKey;
                delete form.dataset.originalKey;
                formTitle.textContent = "Neue Phase";
                nameInput.value = "";
                colorInput.value = "#74b2d4";
                updateColorText();
                setIconValue("circle");
                activeInput.checked = true;
                closedInput.checked = false;
                markEditingRow(null);
                setSaveButtonLabel("Speichern");
                nameInput.focus();
              }

              function stageIdOf(stage) {
                return String(stage?.id ?? stage?.stage_id ?? stage?.lead_stage_id ?? stage?.value ?? "").trim();
              }

              function stageFromRow(stageId) {
                const row = qs(`#leadStagesList [data-stage-id="${CSS.escape(String(stageId || ""))}"]`);
                if (!row) return null;
                const nameFromStrong = row.querySelector(".lsm-stage-name strong")?.textContent?.trim() || "";
                const codeKey = row.querySelector("code")?.textContent?.trim() || "";
                const dotColor = row.querySelector(".lsm-color-dot")?.style?.backgroundColor || "";
                return normalizeStage({
                  id: stageId,
                  key: row.dataset.stageKey || codeKey,
                  name: row.dataset.stageName || nameFromStrong,
                  color: row.dataset.stageColor || dotColor || "#74b2d4",
                  icon: row.dataset.stageIcon || "circle",
                  is_active: String(row.dataset.stageActive ?? "1") === "1",
                  is_closed: String(row.dataset.stageClosed ?? "0") === "1",
                });
              }

              function fillForm(stage, opts = {}) {
                if (!stage || !form) return false;
                const stageId = stageIdOf(stage);
                if (!stageId) return false;

                idInput.value = stageId;
                form.dataset.mode = "edit";
                form.dataset.stageId = stageId;
                form.dataset.stageKey = String(stage.key || stage.stage_key || "").trim();
                form.dataset.originalKey = String(stage.key || stage.stage_key || "").trim();
                formTitle.textContent = `Phase bearbeiten #${stageId}`;
                nameInput.value = stage.name || stage.label || "";
                colorInput.value = stage.color || "#74b2d4";
                updateColorText();
                setIconValue(stage.icon || "circle");
                activeInput.checked = Number(stage.is_active ?? 1) === 1 || stage.is_active === true;
                closedInput.checked = Number(stage.is_closed ?? 0) === 1 || stage.is_closed === true;
                markEditingRow(stageId);
                setSaveButtonLabel("Aktualisieren");
                if (opts.focus !== false) {
                  nameInput.focus();
                  nameInput.select?.();
                  form.scrollIntoView?.({ behavior: "smooth", block: "start" });
                }
                return true;
              }

              async function fillFormById(stageId) {
                stageId = String(stageId || "").trim();
                if (!stageId) return;

                // Force edit mode immediately, even before the optional detail request finishes.
                // This prevents protected/default stages from being posted to the create endpoint.
                if (idInput) idInput.value = stageId;
                if (form) {
                  form.dataset.mode = "edit";
                  form.dataset.stageId = stageId;
                  const fallbackStage = stageFromRow(stageId) || stages.find((s) => String(stageIdOf(s)) === stageId);
                  const fallbackKey = String(fallbackStage?.key || fallbackStage?.stage_key || "").trim();
                  if (fallbackKey) {
                    form.dataset.stageKey = fallbackKey;
                    form.dataset.originalKey = fallbackKey;
                  }
                }
                if (formTitle) formTitle.textContent = `Phase bearbeiten #${stageId}`;
                setSaveButtonLabel("Aktualisieren");
                markEditingRow(stageId);

                let stage = stageFromRow(stageId) || stages.find((s) => String(stageIdOf(s)) === stageId);

                // Fill from cached/DOM data synchronously first, so Save can never create a new phase.
                if (stage) fillForm(stage, { focus: true });

                try {
                  const data = await requestJSON(PHASE_API.show(stageId), { method: "GET" });
                  const fresh = normalizeStage(data.stage || data.data || data);
                  if (stageIdOf(fresh)) {
                    stages = stages.map((s) => String(stageIdOf(s)) === stageId ? { ...s, ...fresh } : s);
                    fillForm(fresh, { focus: false });
                  }
                } catch (error) {
                  if (!stage) {
                    Swal.fire("Fehler", "Die Phase konnte nicht in das Bearbeiten-Formular geladen werden. Bitte Phasen neu laden.", "error");
                  }
                }
              }

              function rowsToItems() {
                return qsa("#leadStagesList [data-stage-id]").map((row, index) => ({
                  id: Number(row.dataset.stageId),
                  sort_order: (index + 1) * 10,
                })).filter((x) => x.id > 0);
              }

              function renderStages() {
                if (!list) return;
                if (!stages.length) {
                  list.innerHTML = `<div class="lsm-empty">Keine Phasen gefunden.</div>`;
                  return;
                }
                list.innerHTML = stages.map((stage) => {
                  const usage = Number(stage.usage_count || 0);
                  const isProtected = !!stage.is_protected;
                  const color = stage.color || "#74b2d4";
                  const icon = stage.icon || "circle";
                  const subCount = Number(stage.sub_stage_count || normalizeSubStages(stage).length || 0);
                  return `
                    <div class="lsm-stage-row"
                         data-stage-id="${esc(stage.id)}"
                         data-stage-key="${esc(stage.key)}"
                         data-stage-name="${esc(stage.name)}"
                         data-stage-color="${esc(color)}"
                         data-stage-icon="${esc(icon)}"
                         data-stage-active="${stage.is_active ? 1 : 0}"
                         data-stage-closed="${stage.is_closed ? 1 : 0}"
                         draggable="true">
                      <div><span class="lsm-drag-handle" title="Ziehen zum Sortieren"><i class="feather icon-move"></i></span></div>
                      <div class="lsm-stage-name">
                        <span class="lsm-color-dot" style="background:${esc(color)}"></span>
                        <div>
                          <strong><i class="feather icon-${esc(icon)} mr-50"></i>${esc(stage.name)}</strong>
                          <small>${stage.is_default ? "Standard" : "Eigene Phase"}${stage.is_closed ? " • geschlossen" : ""}</small>
                          <span class="lsm-stage-subcount-line"><i class="feather icon-git-branch"></i>${subCount} Unterphasen</span>
                        </div>
                      </div>
                      <div><code>${esc(stage.key)}</code></div>
                      <div><span class="lsm-badge ${usage > 0 ? "lsm-badge--warn" : "lsm-badge--ok"}">${usage} Einträge</span></div>
                      <div class="lsm-actions">
                        ${stage.is_active ? `<span class="lsm-badge lsm-badge--blue">Aktiv</span>` : `<span class="lsm-badge">Inaktiv</span>`}
                        <button type="button" class="lsm-substage-btn" data-lsm-substages="${esc(stage.id)}" title="Unterphasen für diese Hauptphase konfigurieren">
                          <i class="feather icon-list"></i><span>Unterphasen</span><span class="lsm-substage-count-pill">${subCount}</span>
                        </button>
                        <button type="button" class="lsm-icon-btn" data-lsm-bulk-move="${esc(stage.key)}" title="Alle Einträge aus dieser Phase verschieben"><i class="feather icon-shuffle"></i></button>
                        <button type="button" class="lsm-icon-btn" data-lsm-edit="${esc(stage.id)}" onclick="if(window.LeadStageManagerEdit){window.LeadStageManagerEdit('${esc(stage.id)}'); return false;}" title="Bearbeiten"><i class="feather icon-edit-2"></i></button>
                        <button type="button" class="lsm-icon-btn danger" data-lsm-delete="${esc(stage.id)}" data-usage="${usage}" data-protected="${isProtected ? 1 : 0}" title="Löschen"><i class="feather icon-trash-2"></i></button>
                      </div>
                    </div>`;
                }).join("");
                bindDragRows();
                refreshIcons();
              }


              function selectedStageForSubstages() {
                return stages.find((stage) => String(stage.id) === String(selectedSubstageStageId)) || null;
              }

              function resetSubstageForm(stage = null) {
                if (subNameInput) subNameInput.value = "";
                if (subKeyInput) subKeyInput.value = "";
                if (subColorInput) subColorInput.value = stage?.color || "#93c21c";
                if (subIconInput) subIconInput.value = "list";
                if (subActiveInput) subActiveInput.checked = true;
              }

              async function ensureStageSubStages(stageId) {
                let stage = selectedStageForSubstages();
                if (!stage) return null;
                if (Array.isArray(stage.sub_stages)) return stage;
                try {
                  const data = await requestJSON(PHASE_API.show(stageId), { method: "GET" });
                  const full = normalizeStage(data.stage || data.data || data);
                  stages = stages.map((item) => String(item.id) === String(stageId) ? { ...item, ...full } : item);
                  return selectedStageForSubstages();
                } catch (err) {
                  // Keep the drawer usable even if the detail route is not available.
                  stage.sub_stages = normalizeSubStages(stage);
                  return stage;
                }
              }

              async function openSubstageDrawer(stageId) {
                selectedSubstageStageId = stageId;
                let stage = await ensureStageSubStages(stageId);
                if (!stage) {
                  Swal.fire("Hinweis", "Diese Hauptphase konnte nicht gefunden werden. Bitte lade die Phasen neu.", "info");
                  return;
                }
                if (subStageIdInput) subStageIdInput.value = stage.id;
                if (subTitle) subTitle.textContent = `Unterphasen · ${stage.name || 'Hauptphase'}`;
                if (subSubtitle) {
                  const subCount = Number(stage.sub_stage_count || normalizeSubStages(stage).length || 0);
                  subSubtitle.innerHTML = `Hier verwaltest du <strong>${subCount}</strong> Unterphasen von <strong>${esc(stage.name || stage.key || stage.id)}</strong>. Die Hauptphasenliste bleibt geöffnet.`;
                }
                setKanbanPhaseName(stage.name || stage.key || "Hauptphase", "Ausgewählte Hauptphase");
                resetSubstageForm(stage);
                renderSubstages();
                subDrawer?.classList.add("is-open");
                subDrawer?.setAttribute("aria-hidden", "false");
                setTimeout(() => subNameInput?.focus?.(), 80);
                refreshIcons();
              }

              function closeSubstageDrawer() {
                subDrawer?.classList.remove("is-open");
                subDrawer?.setAttribute("aria-hidden", "true");
                selectedSubstageStageId = null;
                setKanbanPhaseName("Hauptphasen", "Aktuelle Ansicht");
              }

              function substageRowsToItems() {
                return qsa("#lsmSubstageList [data-sub-id]")
                  .map((row) => Number(row.dataset.subId))
                  .filter((id) => id > 0);
              }

              function renderSubstages() {
                if (!subList) return;
                const stage = selectedStageForSubstages();
                if (!stage) {
                  subList.innerHTML = `<div class="lsm-substage-empty">Bitte zuerst links eine Hauptphase auswählen.</div>`;
                  return;
                }
                const subs = normalizeSubStages(stage);
                if (!subs.length) {
                  subList.innerHTML = `<div class="lsm-substage-empty">Für <strong>${esc(stage.name)}</strong> gibt es noch keine Unterphasen. Erstelle oben die erste Unterphase.</div>`;
                  return;
                }
                subList.innerHTML = subs.map((sub) => `
                  <div class="lsm-substage-row" data-sub-id="${esc(sub.id)}" draggable="true">
                    <span class="lsm-substage-handle" title="Ziehen zum Sortieren"><i class="feather icon-menu"></i></span>
                    <input class="form-control js-lsm-sub-name" value="${esc(sub.name)}" placeholder="Name">
                    <input class="form-control js-lsm-sub-key" value="${esc(sub.key)}" placeholder="Key">
                    <input class="form-control js-lsm-sub-color" type="color" value="${esc(sub.color || stage.color || '#93c21c')}">
                    <input class="form-control js-lsm-sub-icon" value="${esc(sub.icon || 'list')}" placeholder="Icon">
                    <div class="lsm-substage-actions">
                      <label class="mb-0 small text-muted" title="Aktiv"><input type="checkbox" class="js-lsm-sub-active" ${sub.is_active ? 'checked' : ''}> Aktiv</label>
                      <button type="button" class="lsm-mini-btn" data-lsm-sub-save="${esc(sub.id)}" title="Speichern"><i class="feather icon-save"></i></button>
                      <button type="button" class="lsm-mini-btn danger" data-lsm-sub-delete="${esc(sub.id)}" data-usage="${esc(sub.usage_count || 0)}" title="Löschen"><i class="feather icon-trash-2"></i></button>
                    </div>
                  </div>
                `).join("");
                bindSubstageDragRows();
                refreshIcons();
              }

              function bindSubstageDragRows() {
                qsa("#lsmSubstageList [data-sub-id]").forEach((row) => {
                  row.addEventListener("dragstart", (e) => {
                    substageDragId = row.dataset.subId;
                    row.classList.add("is-dragging");
                    e.dataTransfer.effectAllowed = "move";
                    e.dataTransfer.setData("text/plain", substageDragId);
                  });
                  row.addEventListener("dragend", () => {
                    row.classList.remove("is-dragging");
                    qsa("#lsmSubstageList .is-drop-target").forEach((el) => el.classList.remove("is-drop-target"));
                    substageDragId = null;
                  });
                  row.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    const dragging = qs("#lsmSubstageList .is-dragging");
                    if (!dragging || dragging === row) return;
                    row.classList.add("is-drop-target");
                    const rect = row.getBoundingClientRect();
                    const after = e.clientY > rect.top + rect.height / 2;
                    subList.insertBefore(dragging, after ? row.nextSibling : row);
                  });
                  row.addEventListener("dragleave", () => row.classList.remove("is-drop-target"));
                });
              }

              async function reloadStagesAndKeepSubstageDrawer() {
                await loadStages();
                if (selectedSubstageStageId) {
                  const stage = selectedStageForSubstages();
                  if (stage) renderSubstages();
                  else closeSubstageDrawer();
                }
              }

              async function createSubstage() {
                const stageId = subStageIdInput?.value || selectedSubstageStageId;
                if (!stageId) return;
                const payload = {
                  lead_stage_id: Number(stageId),
                  name: (subNameInput?.value || "").trim(),
                  key: (subKeyInput?.value || "").trim(),
                  color: subColorInput?.value || "#93c21c",
                  icon: (subIconInput?.value || "list").trim() || "list",
                  is_active: subActiveInput?.checked ? 1 : 0,
                };
                if (!payload.name) { Swal.fire("Hinweis", "Bitte gib einen Namen für die Unterphase ein.", "info"); return; }
                try {
                  await requestJSON(subStageApi.create(stageId), { method: "POST", body: JSON.stringify(payload) });
                  resetSubstageForm(selectedStageForSubstages());
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphase gespeichert", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Unterphase konnte nicht erstellt werden.", "error"); }
              }

              async function saveSubstage(subId) {
                const row = qs(`#lsmSubstageList [data-sub-id="${CSS.escape(String(subId))}"]`);
                const stageId = subStageIdInput?.value || selectedSubstageStageId;
                if (!row || !stageId) return;
                const payload = {
                  lead_stage_id: Number(stageId),
                  name: row.querySelector(".js-lsm-sub-name")?.value?.trim() || "",
                  key: row.querySelector(".js-lsm-sub-key")?.value?.trim() || "",
                  color: row.querySelector(".js-lsm-sub-color")?.value || "#93c21c",
                  icon: row.querySelector(".js-lsm-sub-icon")?.value?.trim() || "list",
                  is_active: row.querySelector(".js-lsm-sub-active")?.checked ? 1 : 0,
                };
                if (!payload.name) { Swal.fire("Hinweis", "Bitte gib einen Namen ein.", "info"); return; }
                try {
                  await requestJSON(subStageApi.update(subId), { method: "POST", body: JSON.stringify(payload) });
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphase aktualisiert", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Unterphase konnte nicht gespeichert werden.", "error"); }
              }

              async function deleteSubstage(subId, usage) {
                usage = Number(usage || 0);
                if (usage > 0) {
                  Swal.fire("Nicht möglich", `Diese Unterphase enthält noch ${usage} Einträge. Bitte verschiebe diese Einträge zuerst.`, "warning");
                  return;
                }
                const ask = await Swal.fire({ icon:"warning", title:"Unterphase löschen?", text:"Diese Aktion kann nicht rückgängig gemacht werden.", showCancelButton:true, confirmButtonText:"Ja, löschen", cancelButtonText:"Abbrechen", confirmButtonColor:"#ef4444" });
                if (!ask.isConfirmed) return;
                try {
                  await requestJSON(subStageApi.destroy(subId), { method: "DELETE" });
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphase gelöscht", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Unterphase konnte nicht gelöscht werden.", "error"); }
              }

              async function saveSubstageOrder() {
                const stageId = subStageIdInput?.value || selectedSubstageStageId;
                if (!stageId) return;
                const items = substageRowsToItems();
                if (!items.length) return;
                try {
                  await requestJSON(subStageApi.reorder(stageId), { method: "POST", body: JSON.stringify({ items }) });
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphasen sortiert", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Sortierung konnte nicht gespeichert werden.", "error"); }
              }

              function bindDragRows() {
                qsa("#leadStagesList [data-stage-id]").forEach((row) => {
                  row.addEventListener("dragstart", (e) => {
                    dragId = row.dataset.stageId;
                    row.classList.add("is-dragging");
                    e.dataTransfer.effectAllowed = "move";
                    e.dataTransfer.setData("text/plain", dragId);
                  });
                  row.addEventListener("dragend", () => {
                    row.classList.remove("is-dragging");
                    qsa("#leadStagesList .is-drop-target").forEach((el) => el.classList.remove("is-drop-target"));
                    dragId = null;
                  });
                  row.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    const dragging = qs("#leadStagesList .is-dragging");
                    if (!dragging || dragging === row) return;
                    row.classList.add("is-drop-target");
                    const rect = row.getBoundingClientRect();
                    const after = e.clientY > rect.top + rect.height / 2;
                    list.insertBefore(dragging, after ? row.nextSibling : row);
                  });
                  row.addEventListener("dragleave", () => row.classList.remove("is-drop-target"));
                });
              }

              async function loadStages() {
                if (list) list.innerHTML = `<div class="lsm-empty">Phasen werden geladen…</div>`;
                try {
                  const data = await requestJSON(PHASE_API.index, { method: "GET" });
                  const rawStages = Array.isArray(data.stages) ? data.stages : (Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []));
                  const currentEditId = String(idInput?.value || form?.dataset?.stageId || "").trim();
                  stages = rawStages.map(normalizeStage);
                  renderStages();
                  if (currentEditId) {
                    const currentStage = stages.find((s) => String(stageIdOf(s)) === currentEditId);
                    if (currentStage) fillForm(currentStage, { focus: false });
                  }
                  if (selectedSubstageStageId) {
                    const stage = selectedStageForSubstages();
                    if (stage) renderSubstages();
                    else closeSubstageDrawer();
                  }
                } catch (err) {
                  if (list) list.innerHTML = `<div class="lsm-empty text-danger">${esc(err.message || "Phasen konnten nicht geladen werden.")}<br><small>Prüfe die Route: /task-phase/ajax/stage-admin/stages</small></div>`;
                }
              }

              async function saveStage(event) {
                event.preventDefault();

                const id = String(idInput?.value || form?.dataset?.stageId || "").trim();
                const mode = String(form?.dataset?.mode || (id ? "edit" : "create"));
                const originalKey = String(form?.dataset?.originalKey || form?.dataset?.stageKey || "").trim();

                const payload = {
                  stage_id: id || null,
                  id: id || null,
                  original_key: originalKey || null,
                  stage_key: originalKey || null,
                  name: nameInput.value.trim(),
                  color: colorInput.value || "#74b2d4",
                  icon: iconInput.value || "circle",
                  is_active: activeInput.checked ? 1 : 0,
                  is_closed: closedInput.checked ? 1 : 0,
                };

                if (!payload.name) {
                  Swal.fire("Hinweis", "Bitte gib einen Namen ein.", "info");
                  return;
                }

                if (mode === "edit" && !id) {
                  Swal.fire("Bearbeitung nicht aktiv", "Bitte klicke zuerst bei der Phase auf Bearbeiten. Danach steht oben \"Phase bearbeiten\" und der bestehende Eintrag wird aktualisiert.", "warning");
                  return;
                }

                try {
                  const url = id ? PHASE_API.update(id) : PHASE_API.store;
                  await requestJSON(url, { method: "POST", body: JSON.stringify(payload) });
                  await loadStages();
                  if (id) fillFormById(id);
                  else resetForm();
                  Swal.fire({ icon:"success", title: id ? "Phase aktualisiert" : "Phase erstellt", text:"Die Phase wurde gespeichert.", timer:900, showConfirmButton:false });
                  setTimeout(() => window.location.reload(), 950);
                } catch (err) {
                  Swal.fire("Fehler", err.message || "Phase konnte nicht gespeichert werden.", "error");
                }
              }

              async function deleteStage(id, usage, isProtected) {
                usage = Number(usage || 0);
                isProtected = Number(isProtected || 0) === 1;

                const current = stages.find((stage) => String(stage.id) === String(id));
                const previous = stages
                  .filter((stage) => String(stage.id) !== String(id))
                  .filter((stage) => Number(stage.is_active ?? 1) === 1)
                  .filter((stage) => Number(stage.sort_order || 0) < Number(current?.sort_order || 0))
                  .sort((a, b) => Number(b.sort_order || 0) - Number(a.sort_order || 0) || Number(b.id || 0) - Number(a.id || 0))[0];

                if (!previous) {
                  Swal.fire("Nicht möglich", "Diese Phase hat keine vorherige aktive Phase. Sie kann nicht automatisch verschoben werden.", "warning");
                  return;
                }

                const ask = await Swal.fire({
                  icon: "warning",
                  title: "Phase löschen und Daten verschieben?",
                  html: `
                    <div style="text-align:left;line-height:1.55">
                      <p>Die Phase <strong>${esc(current?.name || "diese Phase")}</strong> wird gelöscht.</p>
                      <p>Alle aktuellen Karten, Aufgaben und Unterphasen-Daten werden automatisch verschoben nach:</p>
                      <div style="padding:10px;border-radius:12px;background:#f8fafc;border:1px solid #dbeafe;font-weight:900;">
                        ${esc(previous.name || previous.key)}
                      </div>
                      ${usage > 0 ? `<p style="margin-top:10px;color:#b45309;font-weight:800;">${usage} Einträge werden verschoben.</p>` : ``}
                      ${isProtected ? `<p style="margin-top:10px;color:#b45309;font-weight:800;">Diese Phase ist geschützt. Bitte nur löschen, wenn du sie wirklich nicht mehr im Prozess brauchst.</p>` : ``}
                    </div>
                  `,
                  showCancelButton: true,
                  confirmButtonText: "Ja, löschen und verschieben",
                  cancelButtonText: "Abbrechen",
                  confirmButtonColor: "#e50656"
                });

                if (!ask.isConfirmed) return;

                try {
                  await requestJSON(PHASE_API.destroy(id), {
                    method: "DELETE",
                    body: JSON.stringify({
                      move_to_previous: true,
                      force_delete_protected: isProtected
                    })
                  });

                  await loadStages();
                  Swal.fire({
                    icon: "success",
                    title: "Gelöscht",
                    text: "Die Daten wurden in die vorherige Phase verschoben.",
                    timer: 1100,
                    showConfirmButton: false
                  });
                  setTimeout(() => window.location.reload(), 1150);
                } catch (err) {
                  const payload = err.payload || {};

                  if (payload.requires_transfer || payload.requires_protected_confirmation) {
                    Swal.fire("Bestätigung nötig", payload.message || "Bitte bestätige das Verschieben der Daten.", "warning");
                    return;
                  }

                  Swal.fire("Nicht möglich", err.message || "Phase konnte nicht gelöscht werden.", "warning");
                }
              }


              async function bulkMoveStageRecords(sourceKey = "") {
                let activeStages = [];

                try {
                  const optionsRes = await requestJSON(PHASE_API.options, { method: "GET" });
                  activeStages = Array.isArray(optionsRes.stages) ? optionsRes.stages : [];
                } catch (error) {
                  console.warn("[Kanban] Stage transfer options endpoint failed, using local stage cache.", error);
                  if (!Array.isArray(stages) || !stages.length) {
                    try { await loadStages(); } catch (loadError) { console.warn("[Kanban] local stage load failed", loadError); }
                  }
                  activeStages = Array.isArray(stages) ? stages : [];
                }

                activeStages = activeStages
                  .filter((stage) => Number(stage.is_active ?? 1) === 1)
                  .sort((a, b) => {
                    const ao = Number(a.sort_order ?? 999999);
                    const bo = Number(b.sort_order ?? 999999);
                    if (ao !== bo) return ao - bo;
                    return String(a.name || a.label || a.key || "").localeCompare(String(b.name || b.label || b.key || ""), "de");
                  });

                if (activeStages.length < 2) {
                  Swal.fire("Nicht möglich", "Es müssen mindestens zwei aktive Phasen vorhanden sein.", "warning");
                  return;
                }

                const stageValue = (stage) => String(stage?.key || stage?.value || stage?.id || "");
                const stageLabel = (stage) => stage?.name || stage?.label || stage?.key || `Phase #${stage?.id || ""}`;
                const sameStage = (stage, value) => {
                  const v = String(value || "");
                  return stageValue(stage) === v || String(stage?.id || "") === v;
                };

                const sourceOptionsHTML = (selectedValue = "") => activeStages.map((stage) => {
                  const key = stageValue(stage);
                  const selected = sameStage(stage, selectedValue) ? "selected" : "";
                  const label = stageLabel(stage);
                  return `<option value="${esc(key)}" ${selected}>${esc(label)} (${esc(key)})</option>`;
                }).join("");

                const targetOptionsHTML = (sourceValue = "") => activeStages
                  .filter((stage) => !sameStage(stage, sourceValue))
                  .map((stage) => {
                    const key = stageValue(stage);
                    const label = stageLabel(stage);
                    return `<option value="${esc(key)}">${esc(label)} (${esc(key)})</option>`;
                  }).join("");

                const firstAsk = await Swal.fire({
                  icon: "warning",
                  title: "Alle Einträge verschieben",
                  html: `
                    <div style="text-align:left">
                      <label class="small font-weight-bold text-uppercase text-muted">Von Phase</label>
                      <select id="bulkMoveSourceStage" class="form-control mb-3">${sourceOptionsHTML(sourceKey)}</select>

                      <label class="small font-weight-bold text-uppercase text-muted">Nach Phase</label>
                      <select id="bulkMoveTargetStage" class="form-control mb-3">
                        <option value="" selected>Bitte wählen…</option>
                        ${targetOptionsHTML(sourceKey)}
                      </select>

                      <label class="small font-weight-bold text-uppercase text-muted">Grund / Notiz</label>
                      <textarea id="bulkMoveReason" class="form-control" rows="3" placeholder="Optionaler Grund…"></textarea>

                      <div class="small text-danger mt-3">
                        Diese Aktion verschiebt alle Karten aus der Quell-Phase in die Ziel-Phase.
                      </div>
                    </div>
                  `,
                  showCancelButton: true,
                  confirmButtonText: "Prüfen",
                  cancelButtonText: "Abbrechen",
                  didOpen: () => {
                    const sourceEl = document.getElementById("bulkMoveSourceStage");
                    const targetEl = document.getElementById("bulkMoveTargetStage");

                    const rebuildTargetOptions = () => {
                      if (!sourceEl || !targetEl) return;

                      const currentSource = sourceEl.value || "";
                      const currentTarget = targetEl.value || "";
                      targetEl.innerHTML = `<option value="" selected>Bitte wählen…</option>${targetOptionsHTML(currentSource)}`;

                      const canKeepCurrentTarget = currentTarget
                        && currentTarget !== currentSource
                        && Array.from(targetEl.options).some((option) => option.value === currentTarget);

                      targetEl.value = canKeepCurrentTarget ? currentTarget : "";

                      if (window.jQuery && window.jQuery.fn.select2) {
                        window.jQuery(targetEl).trigger("change.select2");
                      }
                    };

                    rebuildTargetOptions();

                    if (window.jQuery && window.jQuery.fn.select2) {
                      const $popup = window.jQuery(Swal.getPopup());
                      const $source = window.jQuery(sourceEl);
                      const $target = window.jQuery(targetEl);

                      $source.select2({
                        width: "100%",
                        dropdownParent: $popup
                      });

                      $target.select2({
                        width: "100%",
                        dropdownParent: $popup,
                        placeholder: "Bitte wählen…",
                        allowClear: true
                      });

                      $source.on("change", rebuildTargetOptions);
                    } else if (sourceEl) {
                      sourceEl.addEventListener("change", rebuildTargetOptions);
                    }
                  },
                  preConfirm: () => {
                    const source = document.getElementById("bulkMoveSourceStage")?.value || "";
                    const target = document.getElementById("bulkMoveTargetStage")?.value || "";
                    const reason = document.getElementById("bulkMoveReason")?.value || "";

                    if (!source || !target) {
                      Swal.showValidationMessage("Bitte Quelle und Ziel wählen.");
                      return false;
                    }

                    if (source === target) {
                      Swal.showValidationMessage("Quelle und Ziel dürfen nicht gleich sein.");
                      return false;
                    }

                    return { source, target, reason };
                  }
                });

                if (!firstAsk.isConfirmed || !firstAsk.value) return;

                const source = firstAsk.value.source;
                const target = firstAsk.value.target;
                const reason = firstAsk.value.reason || "";

                let summary;
                try {
                  const params = new URLSearchParams({
                    source_stage: source,
                    target_stage: target
                  });

                  summary = await requestJSON(`${PHASE_API.bulkMoveSummary}?${params.toString()}`, {
                    method: "GET"
                  });
                } catch (err) {
                  const payloadStages = Array.isArray(err.payload?.stages) ? err.payload.stages : [];
                  if (payloadStages.length) {
                    console.warn("[Kanban] Summary failed but stages were returned", payloadStages);
                  }
                  Swal.fire("Fehler", err.message || "Zusammenfassung konnte nicht geladen werden.", "error");
                  return;
                }

                const subStages = Array.isArray(summary.target_sub_stages)
                  ? summary.target_sub_stages
                  : (Array.isArray(summary.target_stage?.sub_stages) ? summary.target_stage.sub_stages : []);

                const subStageOptions = [
                  `<option value="">Standard-Unterphase / keine Unterphase</option>`
                ].concat(
                  subStages.map((sub) => `<option value="${esc(sub.id)}">${esc(sub.name || sub.label || sub.key)}${sub.is_default ? " — Standard" : ""}</option>`)
                ).join("");

                const count = Number(summary.count || 0);

                if (count <= 0) {
                  Swal.fire("Keine Daten", "In dieser Phase gibt es keine Einträge zum Verschieben.", "info");
                  return;
                }

                const confirmAsk = await Swal.fire({
                  icon: "warning",
                  title: `${count} Einträge verschieben?`,
                  html: `
                    <div style="text-align:left;line-height:1.55">
                      <p>
                        Von <strong>${esc(summary.source_stage?.name || summary.source_stage?.label || source)}</strong>
                        nach <strong>${esc(summary.target_stage?.name || summary.target_stage?.label || target)}</strong>
                      </p>

                      <label class="small font-weight-bold text-uppercase text-muted">Ziel-Unterphase</label>
                      <select id="bulkMoveTargetSubStage" class="form-control mb-3">${subStageOptions}</select>

                      <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" id="bulkMoveRelatedTasks" checked>
                        <span>Aufgaben / Task-Referenzen ebenfalls anpassen</span>
                      </label>

                      <div class="small text-danger mt-3">
                        Diese Aktion betrifft alle aktuellen Karten in der Quell-Phase.
                      </div>
                    </div>
                  `,
                  showCancelButton: true,
                  confirmButtonText: "Ja, alle verschieben",
                  cancelButtonText: "Abbrechen",
                  confirmButtonColor: "#e50656",
                  didOpen: () => {
                    if (window.jQuery && window.jQuery.fn.select2) {
                      window.jQuery("#bulkMoveTargetSubStage").select2({
                        width: "100%",
                        dropdownParent: window.jQuery(".swal2-container")
                      });
                    }
                  },
                  preConfirm: () => ({
                    targetSubStageId: document.getElementById("bulkMoveTargetSubStage")?.value || "",
                    moveRelatedTasks: document.getElementById("bulkMoveRelatedTasks")?.checked ? 1 : 0
                  })
                });

                if (!confirmAsk.isConfirmed) return;

                try {
                  const result = await requestJSON(PHASE_API.bulkMove, {
                    method: "POST",
                    body: JSON.stringify({
                      source_stage: source,
                      target_stage: target,
                      target_sub_stage_id: confirmAsk.value.targetSubStageId || null,
                      move_related_tasks: confirmAsk.value.moveRelatedTasks,
                      reason: reason,
                      confirm: true
                    })
                  });

                  Swal.fire({
                    icon: "success",
                    title: "Verschoben",
                    text: result.message || "Alle Einträge wurden verschoben.",
                    timer: 1300,
                    showConfirmButton: false
                  });

                  setTimeout(() => window.location.reload(), 1350);
                } catch (err) {
                  Swal.fire("Fehler", err.message || "Einträge konnten nicht verschoben werden.", "error");
                }
              }

              window.bulkMoveStageRecords = bulkMoveStageRecords;

              async function saveOrder() {
                const items = rowsToItems();
                if (!items.length) return;

                const ask = await Swal.fire({
                  icon: "warning",
                  title: "Pipeline-Reihenfolge ändern?",
                  html: `
                    <div style="text-align:left;line-height:1.55">
                      Die neue Reihenfolge wird direkt für das Kanban, Filter und den Workflow verwendet.<br>
                      <strong>Alle vorhandenen Einträge bleiben in ihrer Phase</strong>, werden aber ab sofort nach dieser Pipeline-Reihenfolge angezeigt und verarbeitet.
                    </div>
                  `,
                  showCancelButton: true,
                  confirmButtonText: "Ja, Reihenfolge speichern",
                  cancelButtonText: "Abbrechen",
                  confirmButtonColor: "#93c21c"
                });
                if (!ask.isConfirmed) return;

                try {
                  await requestJSON(PHASE_API.reorder, { method:"POST", body:JSON.stringify({ items }) });
                  await loadStages();
                  Swal.fire({ icon:"success", title:"Sortierung gespeichert", text:"Die Pipeline wird neu geladen.", timer:1000, showConfirmButton:false });
                  setTimeout(() => window.location.reload(), 1050);
                } catch (err) { Swal.fire("Fehler", err.message || "Sortierung konnte nicht gespeichert werden.", "error"); }
              }

              window.LeadStageManagerEdit = function (stageId) {
                fillFormById(stageId);
              };

              // Capture-phase edit handler: protected/default stages must be editable.
              // Some older handlers can leave the form in create mode; this forces edit mode first.
              document.addEventListener("click", (event) => {
                const editBtn = event.target.closest("[data-lsm-edit]");
                if (!editBtn) return;
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                fillFormById(editBtn.dataset.lsmEdit);
              }, true);

              document.addEventListener("click", (event) => {
                if (event.target.closest("#btnOpenStageManager") || event.target.closest("#btnOpenStageManagerTop")) { event.preventDefault(); openModal(); return; }
                if (event.target.closest("[data-lsm-close]")) { event.preventDefault(); closeModal(); return; }
                const subBtn = event.target.closest("[data-lsm-substages]");
                if (subBtn) { event.preventDefault(); openSubstageDrawer(subBtn.dataset.lsmSubstages); return; }
                const saveSubBtn = event.target.closest("[data-lsm-sub-save]");
                if (saveSubBtn) { event.preventDefault(); saveSubstage(saveSubBtn.dataset.lsmSubSave); return; }
                const deleteSubBtn = event.target.closest("[data-lsm-sub-delete]");
                if (deleteSubBtn) { event.preventDefault(); deleteSubstage(deleteSubBtn.dataset.lsmSubDelete, deleteSubBtn.dataset.usage); return; }
                const bulkMoveBtn = event.target.closest("[data-lsm-bulk-move]");
                if (bulkMoveBtn) { event.preventDefault(); bulkMoveStageRecords(bulkMoveBtn.dataset.lsmBulkMove || ""); return; }
                const editBtn = event.target.closest("[data-lsm-edit]");
                if (editBtn) {
                  event.preventDefault();
                  event.stopPropagation();
                  event.stopImmediatePropagation?.();
                  fillFormById(editBtn.dataset.lsmEdit);
                  return;
                }
                const deleteBtn = event.target.closest("[data-lsm-delete]");
                if (deleteBtn) deleteStage(deleteBtn.dataset.lsmDelete, deleteBtn.dataset.usage, deleteBtn.dataset.protected);
              });
              document.addEventListener("keydown", (event) => { if (event.key === "Escape" && modal?.classList.contains("is-open")) closeModal(); });
              colorInput?.addEventListener("input", updateColorText);
              form?.addEventListener("submit", saveStage);
              qs("#lsmResetForm")?.addEventListener("click", resetForm);
              qs("#lsmReloadStages")?.addEventListener("click", loadStages);
              qs("#lsmSaveOrder")?.addEventListener("click", saveOrder);
              qs("#lsmCloseSubstageDrawer")?.addEventListener("click", closeSubstageDrawer);
              qs("#lsmCreateSubstage")?.addEventListener("click", createSubstage);
              qs("#lsmSaveSubstageOrder")?.addEventListener("click", saveSubstageOrder);
              window.openLeadStageSubstageConfig = async function (stageId) {
                if (!stageId) return;
                openModal();
                if (!stages.length) await loadStages();
                await openSubstageDrawer(stageId);
              };
              document.addEventListener("DOMContentLoaded", () => { initIconSelect(); updateColorText(); refreshIcons(); setKanbanPhaseName("Hauptphasen", "Aktuelle Ansicht"); });
            })();
          

/* ===================== Extracted inline script block #24 ===================== */
            (function () {
              "use strict";

              const STORAGE_KEY = "leadKanban.viewOptions.v2";
              const zoomSteps = [0.7, 0.8, 0.9, 1];
              const DEFAULT_COLUMN_COLOR = "#93c21c";

              const qs = (s, ctx = document) => ctx.querySelector(s);
              const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));

              function readState() {
                try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}"); } catch { return {}; }
              }
              function saveState(state) {
                try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch {}
              }
              function clampZoom(value) {
                const n = Number(value);
                if (!Number.isFinite(n)) return 1;
                return Math.min(1, Math.max(0.7, n));
              }
              function nearestStep(value) {
                const z = clampZoom(value);
                return zoomSteps.reduce((best, current) => Math.abs(current - z) < Math.abs(best - z) ? current : best, 1);
              }
              function safeWidth(value) {
                return ["compact", "normal", "wide"].includes(value) ? value : "normal";
              }
              function stageColorFor(key) {
                const meta = window.LeadUI?.APP?.kanbanStageMeta?.[key] || window.LeadUI?.APP?.stageMeta?.[key] || {};
                return meta.color || DEFAULT_COLUMN_COLOR;
              }
              function paintColumnColors(useStageColors) {
                qsa("#kanban .column").forEach((col) => {
                  const color = useStageColors ? stageColorFor(col.id) : DEFAULT_COLUMN_COLOR;
                  col.style.setProperty("--stage-color", color || DEFAULT_COLUMN_COLOR);
                  col.dataset.stageColor = useStageColors ? "1" : "0";
                  const head = col.querySelector("h3");
                  if (head) head.style.setProperty("background", color || DEFAULT_COLUMN_COLOR, "important");
                });
              }
              function applyKanbanViewOptions(next = {}) {
                const card = qs(".kanban-zoom-card");
                const area = qs("#kanbanZoomArea");
                const compactToggle = qs("#kbCompactToggle");
                const useStageColorsToggle = qs("#kbUseStageColorsToggle");
                const widthSelect = qs("#kbColumnWidthSelect");
                if (!card || !area) return;

                const current = readState();
                const state = {
                  zoom: nearestStep(next.zoom ?? current.zoom ?? 1),
                  compact: typeof next.compact === "boolean" ? next.compact : !!current.compact,
                  width: safeWidth(next.width ?? current.width ?? "normal"),
                  useStageColors: typeof next.useStageColors === "boolean" ? next.useStageColors : !!current.useStageColors,
                };

                card.style.setProperty("--kb-zoom", String(state.zoom));
                card.classList.toggle("kb-compact", state.compact);
                card.classList.remove("kb-width-compact", "kb-width-normal", "kb-width-wide");
                card.classList.add(`kb-width-${state.width}`);

                if (compactToggle) compactToggle.checked = state.compact;
                if (useStageColorsToggle) useStageColorsToggle.checked = state.useStageColors;
                if (widthSelect) widthSelect.value = state.width;

                qsa("[data-kb-zoom]").forEach((btn) => {
                  btn.classList.toggle("is-active", Number(btn.dataset.kbZoom) === state.zoom);
                });

                paintColumnColors(state.useStageColors);
                saveState(state);

                if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
              }
              function changeZoom(direction) {
                const state = readState();
                const current = nearestStep(state.zoom ?? 1);
                const index = zoomSteps.indexOf(current);
                const nextIndex = Math.min(zoomSteps.length - 1, Math.max(0, index + direction));
                applyKanbanViewOptions({ zoom: zoomSteps[nextIndex] });
              }

              document.addEventListener("click", (event) => {
                const zoomBtn = event.target.closest("[data-kb-zoom]");
                if (zoomBtn) { event.preventDefault(); applyKanbanViewOptions({ zoom: Number(zoomBtn.dataset.kbZoom) }); return; }
                if (event.target.closest("#kbZoomOutBtn")) { event.preventDefault(); changeZoom(-1); return; }
                if (event.target.closest("#kbZoomInBtn")) { event.preventDefault(); changeZoom(1); }
              });
              document.addEventListener("change", (event) => {
                if (event.target?.id === "kbCompactToggle") applyKanbanViewOptions({ compact: event.target.checked });
                if (event.target?.id === "kbUseStageColorsToggle") applyKanbanViewOptions({ useStageColors: event.target.checked });
                if (event.target?.id === "kbColumnWidthSelect") applyKanbanViewOptions({ width: event.target.value });
              });
              document.addEventListener("DOMContentLoaded", () => applyKanbanViewOptions());
              const obs = new MutationObserver(() => applyKanbanViewOptions());
              document.addEventListener("DOMContentLoaded", () => {
                const board = qs("#kanban");
                if (board) obs.observe(board, { childList:true, subtree:false });
              });
              window.applyKanbanViewOptions = applyKanbanViewOptions;
            })();
          

/* ===================== Extracted inline script block #25 ===================== */
          window.GlobalBreadcrumbs = [
              {
                  label: 'Workspace',
                  url: "/"
              },
              {
                  label: 'Kunden',
                  url: "/new_lead_view"
              },
              {
                  label: 'Prozess',
                  url: window.location.href,
                  clickable: false
              }
          ];

          if (window.setGlobalBreadcrumbs) {
              window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
          }
      


/* ===================== Extracted inline script block #26 ===================== */
      /* =========================================================
         Final Kanban Safety Bridge
         Keeps old inline/list handlers working even if functions are scoped.
      ========================================================= */
      (function(){
        'use strict';

        window.escapeHTML = window.escapeHTML || function(value) {
          return String(value ?? '').replace(/[&<>"']/g, function(m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
          });
        };

        window.showProductStageInfoFromElement = window.showProductStageInfoFromElement || function(el) {
          const d = el?.dataset || {};
          const product = d.productName || d.initial || d.product || 'Produkt';
          const companyStage = d.stage || d.companyStage || '-';
          const productStage = d.productStageName || d.product_stage_name || d.productStage || d.productStageId || 'Noch keine Produktphase';
          const taskPhase = d.productTaskPhaseName || d.product_task_phase_name || d.productTaskPhase || 'Keine Unterphase';
          const html = `
            <div style="text-align:left">
              <div style="border:1px solid #dbeafe;background:#f8fafc;border-radius:14px;padding:12px">
                <div style="font-weight:900;margin-bottom:8px">${window.escapeHTML(product)}</div>
                <div><strong>Unternehmensphase:</strong> ${window.escapeHTML(companyStage)}</div>
                <div><strong>Produktphase:</strong> ${window.escapeHTML(productStage)}</div>
                <div><strong>Unterphase:</strong> ${window.escapeHTML(taskPhase)}</div>
              </div>
            </div>`;
          if (window.Swal) {
            Swal.fire({title:'Produktstatus', html, width:560, confirmButtonText:'Schließen', didOpen:function(){ if(window.feather) window.feather.replace(); }});
          } else {
            alert(`Produkt: ${product}
      Unternehmensphase: ${companyStage}
      Produktphase: ${productStage}
      Unterphase: ${taskPhase}`);
          }
        };

        document.addEventListener('DOMContentLoaded', function(){
          const applyBtn = document.getElementById('kbWorkflowApplyProduct');
          const productSelect = document.getElementById('kbWorkflowProduct');
          const productBox = document.getElementById('kbWorkflowProductBox');

          if (productSelect && window.jQuery && window.jQuery.fn.select2 && !window.jQuery(productSelect).hasClass('select2-hidden-accessible')) {
            window.jQuery(productSelect).select2({
              placeholder: 'Produkt für Workflow wählen…',
              allowClear: true,
              width: '260px',
              dropdownParent: window.jQuery(document.body),
              templateResult: function(option){
                if (!option.id) return option.text;
                const el = option.element;
                const initial = el?.dataset?.initial || '';
                const name = el?.dataset?.name || option.text || '';
                return window.jQuery(`<span class="kb-workflow-select2-option"><span class="kb-workflow-select2-icon"><i class="feather icon-box"></i></span><span class="kb-workflow-select2-text"><span class="kb-workflow-select2-title">${window.escapeHTML(name || option.text)}</span><span class="kb-workflow-select2-sub">${window.escapeHTML(initial ? ('Kürzel: ' + initial) : 'Produkt-Workflow')}</span></span></span>`);
              },
              templateSelection: function(option){
                if (!option.id) return option.text;
                const el = option.element;
                const initial = el?.dataset?.initial || '';
                const name = el?.dataset?.name || option.text || '';
                return window.jQuery(`<span class="kb-workflow-select2-option"><span class="kb-workflow-select2-icon"><i class="feather icon-box"></i></span><span class="kb-workflow-select2-text"><span class="kb-workflow-select2-title">${window.escapeHTML(name || option.text)}</span><span class="kb-workflow-select2-sub">${window.escapeHTML(initial ? ('Kürzel: ' + initial) : 'Produkt-Workflow')}</span></span></span>`);
              },
              escapeMarkup: function(m){ return m; }
            });
          }

          document.querySelectorAll('[data-kb-workflow-mode="product"]').forEach(function(btn){
            btn.addEventListener('click', function(){
              productBox?.classList.remove('d-none');
              if (productSelect) productSelect.disabled = false;
              if (applyBtn) applyBtn.disabled = !productSelect?.value;
              if (window.jQuery && productSelect) window.jQuery(productSelect).prop('disabled', false).trigger('change.select2');
            }, true);
          });
        });
      })();




        

/* ===================== Extracted inline script block #27 ===================== */
      /* =========================================================
         Kanban Lead Task Management - AJAX loaded on demand
         Replaces the old Next Step button on Kanban cards.
         ========================================================= */
      (function () {
        'use strict';

        if (window.__kanbanLeadTaskManagementBooted) return;
        window.__kanbanLeadTaskManagementBooted = true;

        const TASK_URLS = {
          context: (leadProductId) => `/admin/kanban/tasks/context/${encodeURIComponent(leadProductId)}`,
          manual: `/admin/kanban/tasks/manual`,
          template: `/admin/kanban/tasks/template`,
          status: (taskId) => `/admin/kanban/tasks/${encodeURIComponent(taskId)}/status`,
          destroy: (taskId) => `/admin/kanban/tasks/${encodeURIComponent(taskId)}`
        };

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || (window.KANBAN_BOOT?.csrf || '');

        const state = {
          open: false,
          leadProductListId: null,
          context: null,
          templates: [],
          tasks: [],
          employees: [],
          authEmployeeId: "' + (window.KANBAN_BOOT?.authUserId || '') + '",
          search: '',
          status: ''
        };

        const qs = (selector, root = document) => root.querySelector(selector);
        const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));
        const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
        const cssEscape = (value) => window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&');

        function featherRefresh() {
          if (window.feather) window.feather.replace();
        }

        function notify(message, type = 'success') {
          if (window.toastr) {
            window.toastr[type === 'error' ? 'error' : 'success'](message);
            return;
          }
          if (type === 'error') alert(message);
        }

        async function requestJson(url, options = {}) {
          const res = await fetch(url, {
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              ...(options.headers || {})
            },
            ...options
          });

          const data = await res.json().catch(() => ({}));
          if (!res.ok || data.success === false) {
            throw new Error(data.message || 'Serverfehler.');
          }
          return data;
        }

        function openMainModal() {
          qs('#kbTaskModal')?.classList.add('open');
          qs('#kbTaskModalBackdrop')?.classList.add('show');
          qs('#kbTaskModal')?.setAttribute('aria-hidden', 'false');
          state.open = true;
        }

        function closeMainModal() {
          qs('#kbTaskModal')?.classList.remove('open');
          qs('#kbTaskModalBackdrop')?.classList.remove('show');
          qs('#kbTaskModal')?.setAttribute('aria-hidden', 'true');
          state.open = false;
        }

        function openFormModal() {
          qs('#kbTaskFormModal')?.classList.add('open');
          qs('#kbTaskFormBackdrop')?.classList.add('show');
          qs('#kbTaskFormModal')?.setAttribute('aria-hidden', 'false');
        }

        function closeFormModal() {
          qs('#kbTaskFormModal')?.classList.remove('open');
          qs('#kbTaskFormBackdrop')?.classList.remove('show');
          qs('#kbTaskFormModal')?.setAttribute('aria-hidden', 'true');
          qs('#kbTaskForm')?.reset();
          setSelectValue('#kbFormPerformer', '');
          setSelectValue('#kbFormEmployees', []);
        }

        function setSelectValue(selector, value) {
          const el = qs(selector);
          if (!el) return;
          if (window.jQuery && window.jQuery.fn.select2) window.jQuery(el).val(value).trigger('change');
          else if (Array.isArray(value)) qsa('option', el).forEach(o => o.selected = value.includes(o.value));
          else el.value = value;
        }

        function initEmployeeSelects() {
          const performer = qs('#kbFormPerformer');
          const employees = qs('#kbFormEmployees');

          const performerOptions = ['<option value="">Automatisch: Ich selbst</option>']
            .concat(state.employees.map(e => `<option value="${esc(e.id)}">${esc(e.text || ((e.lastname || '') + ' ' + (e.name || '')).trim() || ('#' + e.id))}</option>`))
            .join('');

          if (performer) performer.innerHTML = performerOptions;
          if (employees) {
            employees.innerHTML = state.employees
              .map(e => `<option value="${esc(e.id)}">${esc(e.text || ((e.lastname || '') + ' ' + (e.name || '')).trim() || ('#' + e.id))}</option>`)
              .join('');
          }

          if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery('.kb-task-select2').select2({
              width: '100%',
              dropdownParent: window.jQuery('#kbTaskFormModal')
            });
          }
        }

        async function openTaskManagement(leadProductListId) {
          if (!leadProductListId) {
            notify('lead_product_list_id fehlt.', 'error');
            return;
          }

          state.leadProductListId = leadProductListId;
          openMainModal();

          const contextText = qs('#kbTaskContextText');
          if (contextText) contextText.textContent = 'Aufgaben werden geladen …';
          qs('#kbTaskTemplates').innerHTML = '<div class="kb-task-empty">Stage-Aufgaben werden geladen …</div>';
          qs('#kbTaskSaved').innerHTML = '<div class="kb-task-empty">Gespeicherte Aufgaben werden geladen …</div>';

          try {
            const data = await requestJson(TASK_URLS.context(leadProductListId));
            state.context = data.context || {};
            const sourceCard = document.querySelector(`[data-lead-product-list-id="${CSS.escape(String(leadProductListId))}"]`)
              || document.querySelector(`.card[data-lead-product-id="${CSS.escape(String(leadProductListId))}"]`);
            if (sourceCard && !state.context.stage_started_at) {
              state.context.stage_started_at = sourceCard.dataset.stageStartedAt || sourceCard.dataset.updatedAt || sourceCard.dataset.createdAt || null;
            }
            state.templates = data.templates || [];
            state.tasks = data.tasks || [];
            state.employees = data.employees || [];
            state.authEmployeeId = String(data.auth_employee_id || state.authEmployeeId || '');

            if (contextText) {
              const ctx = state.context;
              contextText.textContent = `${ctx.customer_name || 'Kunde'} · ${ctx.product_name || 'Produkt'} · ${ctx.stage_label || 'Stage'}${ctx.sub_stage_label ? ' / ' + ctx.sub_stage_label : ''}`;
            }

            initEmployeeSelects();
            renderAll();
            updateCardBadge(leadProductListId, state.tasks);
          } catch (e) {
            qs('#kbTaskTemplates').innerHTML = `<div class="kb-task-empty">${esc(e.message)}</div>`;
            qs('#kbTaskSaved').innerHTML = '<div class="kb-task-empty">Fehler beim Laden.</div>';
            notify(e.message, 'error');
          }
        }

        function filteredTasks() {
          const q = state.search.toLowerCase().trim();
          const status = state.status;
          return state.tasks.filter(task => {
            if (status && task.status !== status) return false;
            if (!q) return true;
            const hay = [
              task.title,
              task.description,
              task.internal_note,
              task.status,
              task.performer?.display_name,
              ...(task.employees || []).map(e => e.display_name)
            ].join(' ').toLowerCase();
            return hay.includes(q);
          });
        }

        function taskTitle(task) {
          return String(task?.title || task?.phase_name || task?.activity_title || '-');
        }

        function firstTemplateTaskTitle(offset = 0) {
          const flat = [];
          (state.templates || []).forEach(phase => {
            const acts = Array.isArray(phase.activities) ? phase.activities : [];
            if (acts.length) {
              acts.forEach(activity => flat.push({
                title: activity.title || phase.phase_name,
                description: activity.description || phase.description || '',
                minutes: activity.estimated_minutes || ''
              }));
            } else {
              flat.push({ title: phase.phase_name, description: phase.description || '', minutes: '' });
            }
          });
          return flat[offset] || null;
        }

        function updateSequenceSummary() {
          const landed = state.context?.stage_started_at || state.context?.landed_at || state.context?.updated_at || null;
          const doneTasks = (state.tasks || []).filter(t => t.status === 'done');
          const openTasks = (state.tasks || []).filter(t => !['done', 'cancelled'].includes(t.status));
          const previous = doneTasks.length ? doneTasks[doneTasks.length - 1] : null;
          const current = openTasks.length ? openTasks[0] : firstTemplateTaskTitle(0);
          const next = openTasks.length > 1 ? openTasks[1] : firstTemplateTaskTitle(openTasks.length ? 0 : 1);

          const set = (id, html) => {
            const el = qs(id);
            if (el) el.innerHTML = html;
          };

          set('#kbTaskSeqLanded', esc(landed ? String(landed).replace('T', ' ').slice(0, 16) : 'Neu / gerade gestartet'));
          set('#kbTaskSeqPrevious', previous ? esc(taskTitle(previous)) : 'Noch nichts erledigt');
          set('#kbTaskSeqCurrent', current ? esc(taskTitle(current)) : 'Keine offene Aufgabe');
          set('#kbTaskSeqNext', next ? esc(taskTitle(next)) : 'Keine weitere Aufgabe');
        }

        function renderAll() {
          renderTemplates();
          renderSavedTasks();
          updateSequenceSummary();
          featherRefresh();
        }

        function renderTemplates() {
          const host = qs('#kbTaskTemplates');
          if (!host) return;
          if (!state.templates.length) {
            host.innerHTML = '<div class="kb-task-empty">Keine passenden Aufgaben für diese Stage/Sub-Stage gefunden.</div>';
            return;
          }

          host.innerHTML = state.templates.map(phase => {
            const activities = phase.activities || [];
            const activityHtml = activities.length
              ? activities.map(activity => templateActivityHtml(phase, activity)).join('')
              : templatePhaseHtml(phase);

            return `
              <div class="kb-task-card">
                <div class="kb-task-card-title">${esc(phase.phase_name)}</div>
                ${phase.section_name ? `<div class="kb-task-card-desc">${esc(phase.section_name)}</div>` : ''}
                ${phase.description ? `<div class="kb-task-card-desc">${esc(phase.description)}</div>` : ''}
                <div class="kb-task-list" style="margin-top:10px;">${activityHtml}</div>
              </div>`;
          }).join('');
        }

        function templatePhaseHtml(phase) {
          return `
            <div class="kb-task-card">
              <div class="kb-task-card-title">${esc(phase.phase_name)}</div>
              <div class="kb-task-card-actions">
                <button type="button" class="kb-task-mini-btn" data-kb-template-plan data-phase-id="${esc(phase.id)}" data-activity-id="" data-title="${esc(phase.phase_name)}" data-description="${esc(phase.description || '')}" data-minutes="">
                  <i class="feather icon-calendar"></i> Planen
                </button>
                <button type="button" class="kb-task-mini-btn" data-kb-template-direct data-phase-id="${esc(phase.id)}" data-activity-id="">
                  <i class="feather icon-plus"></i> Übernehmen
                </button>
              </div>
            </div>`;
        }

        function templateActivityHtml(phase, activity) {
          return `
            <div class="kb-task-card">
              <div class="kb-task-card-title">${esc(activity.title || phase.phase_name)}</div>
              ${activity.description ? `<div class="kb-task-card-desc">${esc(activity.description)}</div>` : ''}
              <div class="kb-task-card-meta">
                ${activity.estimated_minutes ? `<span class="kb-task-pill blue"><i class="feather icon-clock"></i>${esc(activity.estimated_minutes)} Min.</span>` : ''}
                ${activity.photo_required ? '<span class="kb-task-pill red"><i class="feather icon-camera"></i> Foto Pflicht</span>' : ''}
              </div>
              <div class="kb-task-card-actions">
                <button type="button" class="kb-task-mini-btn" data-kb-template-plan data-phase-id="${esc(phase.id)}" data-activity-id="${esc(activity.id)}" data-title="${esc(activity.title || phase.phase_name)}" data-description="${esc(activity.description || '')}" data-minutes="${esc(activity.estimated_minutes || '')}">
                  <i class="feather icon-calendar"></i> Planen
                </button>
                <button type="button" class="kb-task-mini-btn" data-kb-template-direct data-phase-id="${esc(phase.id)}" data-activity-id="${esc(activity.id)}">
                  <i class="feather icon-plus"></i> Übernehmen
                </button>
              </div>
            </div>`;
        }

        function renderSavedTasks() {
          const host = qs('#kbTaskSaved');
          if (!host) return;
          const tasks = filteredTasks();
          if (!tasks.length) {
            host.innerHTML = '<div class="kb-task-empty">Keine Aufgaben gefunden.</div>';
            return;
          }
          host.innerHTML = tasks.map(savedTaskHtml).join('');
          featherRefresh();
        }

        function statusLabel(status) {
          return ({ open: 'Offen', scheduled: 'Geplant', in_progress: 'In Bearbeitung', done: 'Erledigt', cancelled: 'Abgebrochen' }[status] || status || 'Offen');
        }

        function savedTaskHtml(task) {
          const overdue = task.is_overdue ? ' is-overdue' : '';
          const performer = task.performer?.display_name || 'Automatisch / Ich';
          const nextText = task.status === 'done'
            ? `Erledigt${task.done_at ? ' am ' + task.done_at : ''}`
            : (task.planned_start_at ? `Nächste Aktion: ${String(task.planned_start_at).replace('T', ' ')}` : 'Nächste Aktion noch nicht geplant');

          return `
            <div class="kb-task-card${overdue}" data-kb-task-id="${esc(task.id)}">
              <div class="kb-task-card-title">${esc(task.title)}</div>
              ${task.description ? `<div class="kb-task-card-desc">${esc(task.description)}</div>` : ''}

              <div class="kb-task-card-meta">
                <span class="kb-task-pill ${task.status === 'done' ? 'green' : 'blue'}">${esc(statusLabel(task.status))}</span>
                ${task.is_overdue ? '<span class="kb-task-pill red">Überfällig</span>' : ''}
                ${task.photo_required ? '<span class="kb-task-pill red"><i class="feather icon-camera"></i> Foto Pflicht</span>' : ''}
                ${task.estimated_minutes ? `<span class="kb-task-pill"><i class="feather icon-clock"></i>${esc(task.estimated_minutes)} Min.</span>` : ''}
                ${task.planned_start_at ? `<span class="kb-task-pill">Start: ${esc(String(task.planned_start_at).replace('T', ' '))}</span>` : ''}
                ${task.planned_end_at ? `<span class="kb-task-pill">Ende: ${esc(String(task.planned_end_at).replace('T', ' '))}</span>` : ''}
                <span class="kb-task-pill"><i class="feather icon-user"></i>${esc(performer)}</span>
                ${(task.has_personal_task || task.external_links?.personal_task_id) ? '<span class="kb-task-pill green"><i class="feather icon-check-square"></i> Persönliche Aufgabe</span>' : ''}
                ${(task.has_appointment || task.external_links?.appointment_id) ? '<span class="kb-task-pill blue"><i class="feather icon-calendar"></i> Termin</span>' : ''}
              </div>

              ${task.internal_note ? `<div class="kb-task-card-desc"><strong>Ablauf:</strong> ${esc(task.internal_note)}</div>` : ''}
              <div class="kb-task-card-next"><strong>${esc(nextText)}</strong></div>

              <div class="kb-task-card-actions">
                ${task.status !== 'done' ? `<button type="button" class="kb-task-mini-btn" data-kb-task-done="${esc(task.id)}"><i class="feather icon-check"></i> Erledigt</button>` : ''}
                ${task.status !== 'in_progress' && task.status !== 'done' ? `<button type="button" class="kb-task-mini-btn" data-kb-task-progress="${esc(task.id)}"><i class="feather icon-play"></i> Starten</button>` : ''}
                <button type="button" class="kb-task-mini-btn" data-kb-task-plan-existing="${esc(task.id)}"><i class="feather icon-calendar"></i> Planen</button>
                <button type="button" class="kb-task-mini-btn" data-kb-task-delete="${esc(task.id)}"><i class="feather icon-trash"></i> Löschen</button>
              </div>
            </div>`;
        }

        function resetFormBase(title, mode) {
          const setValue = (selector, value = '') => {
            const el = qs(selector);
            if (el) el.value = value;
          };

          const setChecked = (selector, checked = false) => {
            const el = qs(selector);
            if (el) el.checked = checked;
          };

          const setText = (selector, value = '') => {
            const el = qs(selector);
            if (el) el.textContent = value;
          };

          setText('#kbTaskFormTitle', title);

          setValue('#kbFormMode', mode);
          setValue('#kbFormLeadProductListId', state.leadProductListId || '');
          setValue('#kbFormTaskPhaseId', '');
          setValue('#kbFormPhaseActivityId', '');
          setValue('#kbFormExistingTaskId', '');
          setValue('#kbFormTitle', '');
          setValue('#kbFormDescription', '');
          setValue('#kbFormStart', '');
          setValue('#kbFormEnd', '');
          setValue('#kbFormMinutes', '');
          setValue('#kbFormInternalNote', '');

          setChecked('#kbFormScheduled', false);
          setChecked('#kbFormCreatePersonalTask', false);

          // Appointment fields were removed from Blade, so keep them optional.
          setChecked('#kbFormCreateAppointment', false);
          setValue('#kbFormAppointmentType', 'kanban_task');
          setValue('#kbFormAppointmentContactMode', '');
          setValue('#kbFormAppointmentPriority', 'normal');

          qs('#kbAppointmentOptions')?.classList.add('d-none');

          setSelectValue('#kbFormPerformer', state.authEmployeeId || '');
          setSelectValue('#kbFormEmployees', []);
        }

        function openManualForm() {
          resetFormBase('Manuelle Aufgabe erstellen', 'manual');
          openFormModal();
        }

        function openTemplateForm(btn) {
          resetFormBase('Vorlagen-Aufgabe planen', 'template');
          qs('#kbFormTaskPhaseId').value = btn.dataset.phaseId || '';
          qs('#kbFormPhaseActivityId').value = btn.dataset.activityId || '';
          qs('#kbFormTitle').value = btn.dataset.title || 'Aufgabe';
          qs('#kbFormDescription').value = btn.dataset.description || '';
          qs('#kbFormMinutes').value = btn.dataset.minutes || '';
          qs('#kbFormScheduled').checked = true;
          openFormModal();
        }

        function openExistingPlanForm(taskId) {
          const task = state.tasks.find(t => Number(t.id) === Number(taskId));
          if (!task) return;
          resetFormBase('Aufgabe planen / aktualisieren', 'existing');
          qs('#kbFormExistingTaskId').value = task.id;
          qs('#kbFormTitle').value = task.title || '';
          qs('#kbFormDescription').value = task.description || '';
          qs('#kbFormStart').value = task.planned_start_at || '';
          qs('#kbFormEnd').value = task.planned_end_at || '';
          qs('#kbFormMinutes').value = task.estimated_minutes || '';
          qs('#kbFormScheduled').checked = true;
          qs('#kbFormInternalNote').value = task.internal_note || '';
          setSelectValue('#kbFormPerformer', task.performer?.id || state.authEmployeeId || '');
          setSelectValue('#kbFormEmployees', (task.employees || []).map(e => String(e.id)));
          openFormModal();
        }

        function employeeIdsFromForm() {
          if (window.jQuery && window.jQuery.fn.select2) return window.jQuery('#kbFormEmployees').val() || [];
          return qsa('#kbFormEmployees option:checked').map(o => o.value);
        }

        function formPayloadBase() {
          return {
            lead_product_list_id: qs('#kbFormLeadProductListId').value,
            title: qs('#kbFormTitle').value,
            description: qs('#kbFormDescription').value,
            internal_note: qs('#kbFormInternalNote').value,
            is_scheduled: qs('#kbFormScheduled').checked,
            planned_start_at: qs('#kbFormStart').value || null,
            planned_end_at: qs('#kbFormEnd').value || null,
            estimated_minutes: qs('#kbFormMinutes').value || null,
            performer_employee_id: qs('#kbFormPerformer').value || null,
            employee_ids: employeeIdsFromForm(),

            // NEW
            create_personal_task: qs('#kbFormCreatePersonalTask')?.checked || false,
            create_appointment: qs('#kbFormCreateAppointment')?.checked || false,
            appointment_type: qs('#kbFormAppointmentType')?.value || 'kanban_task',
            appointment_contact_mode: qs('#kbFormAppointmentContactMode')?.value || null,
            appointment_priority: qs('#kbFormAppointmentPriority')?.value || 'normal'
          };
        }

        async function directTemplateStore(btn) {
          const payload = {
            lead_product_list_id: state.leadProductListId,
            task_phase_id: btn.dataset.phaseId,
            phase_activity_id: btn.dataset.activityId || null,
            performer_employee_id: state.authEmployeeId || null,
            employee_ids: [],
            is_scheduled: false
          };
          const data = await requestJson(TASK_URLS.template, { method: 'POST', body: JSON.stringify(payload) });
          state.tasks.unshift(data.task);
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify(data.message || 'Aufgabe wurde übernommen.');
        }

        async function submitForm(e) {
          e.preventDefault();
          const mode = qs('#kbFormMode').value;
          const payload = formPayloadBase();
          let url = TASK_URLS.manual;
          let method = 'POST';

          if (mode === 'template') {
            url = TASK_URLS.template;
            payload.task_phase_id = qs('#kbFormTaskPhaseId').value;
            payload.phase_activity_id = qs('#kbFormPhaseActivityId').value || null;
            delete payload.title;
            delete payload.description;
          }

          if (mode === 'existing') {
            const taskId = qs('#kbFormExistingTaskId').value;
            url = TASK_URLS.status(taskId);
            method = 'PATCH';
            payload.status = payload.is_scheduled ? 'scheduled' : 'open';
            delete payload.lead_product_list_id;
            delete payload.title;
            delete payload.description;
            delete payload.is_scheduled;
            delete payload.estimated_minutes;
          }

          const data = await requestJson(url, { method, body: JSON.stringify(payload) });

          if (mode === 'existing') {
            const index = state.tasks.findIndex(t => Number(t.id) === Number(data.task.id));
            if (index >= 0) state.tasks[index] = data.task;
          } else {
            state.tasks.unshift(data.task);
          }

          closeFormModal();
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify(data.message || 'Aufgabe wurde gespeichert.');
        }

        async function updateTaskStatus(taskId, status) {
          const data = await requestJson(TASK_URLS.status(taskId), { method: 'PATCH', body: JSON.stringify({ status }) });
          const index = state.tasks.findIndex(t => Number(t.id) === Number(taskId));
          if (index >= 0) state.tasks[index] = data.task;
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify(data.message || 'Aufgabe wurde aktualisiert.');
        }

        async function deleteTask(taskId) {
          const ok = window.Swal
            ? await Swal.fire({ title: 'Aufgabe löschen?', text: 'Diese Aufgabe wird entfernt.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Löschen', cancelButtonText: 'Abbrechen' }).then(r => r.isConfirmed)
            : confirm('Aufgabe wirklich löschen?');
          if (!ok) return;
          await requestJson(TASK_URLS.destroy(taskId), { method: 'DELETE' });
          state.tasks = state.tasks.filter(t => Number(t.id) !== Number(taskId));
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify('Aufgabe wurde gelöscht.');
        }

        function updateCardBadge(leadProductListId, tasks) {
          const card = document.querySelector(`[data-lead-product-list-id="${cssEscape(leadProductListId)}"], [data-lead-product-id="${cssEscape(leadProductListId)}"]`);
          if (!card) return;
          const badge = card.querySelector('[data-kanban-task-count]');
          if (!badge) return;
          const openCount = (tasks || []).filter(t => !['done', 'cancelled'].includes(t.status)).length;
          badge.textContent = openCount > 99 ? '99+' : String(openCount);
          badge.classList.toggle('d-none', openCount <= 0);
        }

        window.openKanbanTaskManagement = openTaskManagement;

        document.addEventListener('click', function (e) {
          const openBtn = e.target.closest('[data-open-kanban-task-management]');
          if (openBtn) {
            e.preventDefault();
            e.stopPropagation();
            const leadProductId = openBtn.dataset.leadProductListId || openBtn.closest('[data-lead-product-list-id]')?.dataset.leadProductListId;
            openTaskManagement(leadProductId);
            return;
          }

          const templatePlan = e.target.closest('[data-kb-template-plan]');
          if (templatePlan) {
            e.preventDefault();
            openTemplateForm(templatePlan);
            return;
          }

          const templateDirect = e.target.closest('[data-kb-template-direct]');
          if (templateDirect) {
            e.preventDefault();
            directTemplateStore(templateDirect).catch(err => notify(err.message, 'error'));
            return;
          }

          const doneBtn = e.target.closest('[data-kb-task-done]');
          if (doneBtn) {
            e.preventDefault();
            updateTaskStatus(doneBtn.dataset.kbTaskDone, 'done').catch(err => notify(err.message, 'error'));
            return;
          }

          const progressBtn = e.target.closest('[data-kb-task-progress]');
          if (progressBtn) {
            e.preventDefault();
            updateTaskStatus(progressBtn.dataset.kbTaskProgress, 'in_progress').catch(err => notify(err.message, 'error'));
            return;
          }

          const planExistingBtn = e.target.closest('[data-kb-task-plan-existing]');
          if (planExistingBtn) {
            e.preventDefault();
            openExistingPlanForm(planExistingBtn.dataset.kbTaskPlanExisting);
            return;
          }

          const deleteBtn = e.target.closest('[data-kb-task-delete]');
          if (deleteBtn) {
            e.preventDefault();
            deleteTask(deleteBtn.dataset.kbTaskDelete).catch(err => notify(err.message, 'error'));
          }
        }, true);

        qs('#kbTaskModalClose')?.addEventListener('click', closeMainModal);
        qs('#kbTaskModalBackdrop')?.addEventListener('click', closeMainModal);
        qs('#kbTaskFormClose')?.addEventListener('click', closeFormModal);
        qs('#kbTaskFormCancel')?.addEventListener('click', closeFormModal);
        qs('#kbTaskFormBackdrop')?.addEventListener('click', closeFormModal);
        qs('#kbManualTaskBtn')?.addEventListener('click', openManualForm);
        qs('#kbTaskForm')?.addEventListener('submit', submitForm);

        qs('#kbTaskSearch')?.addEventListener('input', function () {
          state.search = this.value || '';
          renderSavedTasks();
        });

        qs('#kbTaskStatusFilter')?.addEventListener('change', function () {
          state.status = this.value || '';
          renderSavedTasks();
        });
        qs('#kbFormCreateAppointment')?.addEventListener('change', function () {
          qs('#kbAppointmentOptions')?.classList.toggle('d-none', !this.checked);

          if (this.checked) {
            qs('#kbFormScheduled').checked = true;
          }
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            closeFormModal();
            closeMainModal();
          }
        });
      })();
    

/* ===================== Extracted inline script block #28 ===================== */
      /* =========================================================
         Lead Aktivität / Erinnerung / Nächster Schritt Add-on
         Preload summaries, counters, carousel, searchable activity modal
         ========================================================= */
      (function () {
        'use strict';
        // Disabled: replaced by Kanban Lead Task Management modal.
        return;

        const ENDPOINTS = {
          store: "/kanban/reminders",
          due: "/kanban/reminders/due",
          context: "/kanban/reminders/context",
          cardsSummary: "/kanban/reminders/cards-summary",
          doneBase: "/kanban/reminders"
        };

        const DE = {
          priority: { normal: 'Normal', important: 'Wichtig', critical: 'Kritisch', success: 'Erfolgreich', error: 'Fehler', warning: 'Warnung' },
          status: { open: 'Offen', done: 'Erledigt', cancelled: 'Abgebrochen', in_progress: 'In Bearbeitung', overdue: 'Überfällig' },
          event: {
            created: 'Erstellt', updated: 'Aktualisiert', deleted: 'Gelöscht', reminder_created: 'Erinnerung erstellt', reminder_done: 'Erinnerung erledigt',
            stage_changed: 'Phase geändert', status_changed: 'Status geändert', updated_stage: 'Phase aktualisiert', moved: 'Verschoben', activity: 'Aktivität'
          }
        };

        const state = { booted: false, pollingStarted: false, active: null, activities: [], reminders: [], filter: 'all', search: '', sort: 'oldest', preloadRunning: false };

        function csrf() { return document.querySelector('meta[name="csrf-token"]')?.content || ''; }
        function esc(value) { return String(value ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
        function cssEscape(value) { return window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&'); }
        function translate(map, value, fallback) { const key = String(value || '').toLowerCase(); return map[key] || fallback || value || ''; }

        function parseDateValue(value, timeValue) {
          if (!value) return null;
          const raw = String(value).trim();
          const time = timeValue ? String(timeValue).slice(0,5) : '';
          if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
            const [y,m,d] = raw.split('-').map(Number);
            const [hh,mm] = time ? time.split(':').map(Number) : [0,0];
            return new Date(y, m - 1, d, hh || 0, mm || 0);
          }
          const normalized = raw.replace(' ', 'T');
          const date = new Date(normalized);
          return Number.isNaN(date.getTime()) ? null : date;
        }

        function formatGermanDateTime(value, timeValue) {
          const date = parseDateValue(value, timeValue);
          if (!date) return '';
          const d = date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
          const hasTime = !!timeValue || /\d{2}:\d{2}/.test(String(value || ''));
          if (!hasTime) return d;
          const t = date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
          return `${d}, ${t} Uhr`;
        }

        function dateSortValue(item) {
          const d = item.bucket === 'reminder'
            ? parseDateValue(item.reminder_date, item.reminder_time)
            : parseDateValue(item.date_raw || item.created_at || item.activity_date || item.date || '');
          return d ? d.getTime() : 0;
        }

        function normalizeActivities(items) {
          return (Array.isArray(items) ? items : []).map(item => {
            const typeRaw = item.type_raw || item.type || item.event_type || item.activity_type || 'activity';
            const rawDate = item.date_raw || item.created_at || item.activity_date || '';
            return {
              id: item.id || '', bucket: 'activity', type: item.type_label || translate(DE.event, typeRaw, 'Aktivität'), type_raw: typeRaw,
              text: item.text || item.description || item.result || '', employee: item.employee || item.employee_name || item.user_name || 'System',
              date: item.date_human || item.date || item.created_at_formatted || formatGermanDateTime(rawDate),
              date_raw: rawDate
            };
          });
        }

        function normalizeReminders(items, ctx) {
          return (Array.isArray(items) ? items : []).map(item => {
            const priorityRaw = String(item.priority || 'normal').toLowerCase();
            const statusRaw = String(item.status || 'open').toLowerCase();
            const dateRaw = item.reminder_date || item.due_date || '';
            const timeRaw = item.reminder_time || item.due_time || '';
            return {
              id: item.id || '', bucket: 'reminder', kind: 'reminder', title: item.title || 'Erinnerung', text: item.description || item.text || '',
              priority: priorityRaw, priority_label: item.priority_label || translate(DE.priority, priorityRaw, 'Normal'),
              status: statusRaw, status_label: item.status_label || translate(DE.status, statusRaw, 'Offen'),
              reminder_date: dateRaw, reminder_time: timeRaw, due_text: item.due_text || formatGermanDateTime(dateRaw, timeRaw),
              employee: item.employee || item.responsible_employee_name || item.owner_name || 'Nicht zugewiesen',
              customer_id: item.customer_id || ctx?.customer_id || state.active?.customer_id || '', alternative_id: item.alternative_id || ctx?.alternative_id || state.active?.alternative_id || '',
              product_id: item.product_id || ctx?.product_id || state.active?.product_id || '', lead_product_list_id: item.lead_product_list_id || ctx?.lead_product_list_id || state.active?.lead_product_list_id || ''
            };
          });
        }

        function ensureReminderButtonBadge(card) {
          const btn = card.querySelector('[data-open-lead-reminder], .kb-open-activity-panel');
          if (!btn) return null;
          let badge = btn.querySelector('[data-kb-reminder-button-count]');
          if (!badge) {
            badge = document.createElement('span');
            badge.className = 'badge-notes kb-reminder-button-count';
            badge.dataset.kbReminderButtonCount = '1';
            badge.style.display = 'none';
            badge.textContent = '0';
            btn.appendChild(badge);
          }
          return badge;
        }

        function contextFromCard(card) {
          return {
            lead_product_list_id: card.dataset.leadProductListId || card.dataset.leadProductId || '',
            customer_id: card.dataset.customerId || '', alternative_id: card.dataset.alternativeId || '', product_id: card.dataset.productId || ''
          };
        }

        function findKanbanCard(ctx) {
          const lpl = ctx.lead_product_list_id || '';
          const c = ctx.customer_id || '';
          const a = ctx.alternative_id || '';
          const p = ctx.product_id || '';
          const selectors = [
            lpl ? `.card[data-lead-product-list-id="${cssEscape(lpl)}"], .card[data-lead-product-id="${cssEscape(lpl)}"]` : '',
            c && a && p ? `.card[data-customer-id="${cssEscape(c)}"][data-alternative-id="${cssEscape(a)}"][data-product-id="${cssEscape(p)}"]` : ''
          ].filter(Boolean);
          for (const selector of selectors) { const found = document.querySelector(selector); if (found) return found; }
          return null;
        }

        function renderCardReminderSummary(card, remindersRaw, activityCountRaw) {
          const reminders = normalizeReminders(remindersRaw || [], contextFromCard(card)).sort((a,b) => dateSortValue(a) - dateSortValue(b));
          const activityCount = Number(activityCountRaw || 0);
          const reminderCount = reminders.length;
          const totalCount = reminderCount + activityCount;
          const badge = ensureReminderButtonBadge(card);
          if (badge) {
            badge.textContent = totalCount > 99 ? '99+' : String(totalCount);
            badge.style.display = totalCount > 0 ? 'inline-flex' : 'none';
            badge.title = `${reminderCount} offene nächste Schritte • ${activityCount} Aktivitäten`;
          }

          let summary = card.querySelector('.kb-reminder-summary');
          if (!summary) {
            summary = document.createElement('div');
            summary.className = 'kb-reminder-summary is-empty';
            const stageTime = card.querySelector('.kb-stage-time');
            if (stageTime) stageTime.insertAdjacentElement('afterend', summary);
            else card.appendChild(summary);
          }

          if (!reminderCount) {
            summary.className = 'kb-reminder-summary is-empty';
            summary.innerHTML = `
              <div class="kb-reminder-head">
                <div class="kb-reminder-title"><i class="feather icon-bell"></i> Keine Erinnerung</div>
                <span class="kb-reminder-priority normal">Offen</span>
              </div>
              <div class="kb-reminder-body">
                <i class="feather icon-info"></i>
                <span>Noch kein nächster Schritt geplant.</span>
              </div>
              <div class="kb-card-summary-counts">
                <span class="is-reminder">${reminderCount} nächste Schritte</span>
                <span class="is-activity">${activityCount} Aktivitäten</span>
              </div>`;
            card.classList.add('kb-summary-ready');
            if (window.feather) window.feather.replace();
            return;
          }

          const today = new Date();
          const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
          const firstDate = String(reminders[0].reminder_date || '').slice(0,10);
          const boxState = firstDate && firstDate < todayStr ? ' kb-reminder-overdue' : (firstDate === todayStr ? ' kb-reminder-due-today' : '');
          summary.className = `kb-reminder-summary${boxState}`;
          summary.dataset.kbReminderIndex = summary.dataset.kbReminderIndex || '0';
          const activeIndex = Math.min(Number(summary.dataset.kbReminderIndex || 0), reminders.length - 1);

          const slides = reminders.map((r, i) => `
            <div class="kb-card-reminder-slide ${i === activeIndex ? 'is-active' : ''}" data-kb-reminder-slide="${i}">
              <div class="kb-reminder-head">
                <div class="kb-reminder-title"><i class="feather icon-bell"></i> Nächster Schritt</div>
                <span class="kb-reminder-priority ${esc(r.priority || 'normal')}">${esc(r.priority_label || 'Normal')}</span>
              </div>
              <div class="kb-reminder-body">
                <i class="feather icon-check-square"></i>
                <span><strong>${esc(r.title || 'Erinnerung')}</strong>${r.text ? `<br>${esc(r.text).slice(0,120)}` : ''}</span>
                <i class="feather icon-calendar"></i>
                <span class="kb-reminder-due">${esc(r.due_text || 'Kein Datum')}</span>
                <i class="feather icon-user"></i>
                <span>${esc(r.employee || 'Nicht zugewiesen')}</span>
              </div>
            </div>`).join('');

          summary.innerHTML = `
            <div class="kb-card-reminder-carousel" data-kb-card-reminder-carousel>
              <div class="kb-card-reminder-track">${slides}</div>
              ${reminders.length > 1 ? `
                <div class="kb-card-reminder-nav">
                  <button type="button" data-kb-reminder-prev title="Zurück">‹</button>
                  <span class="kb-card-reminder-counter">${activeIndex + 1} / ${reminders.length}</span>
                  <button type="button" data-kb-reminder-next title="Weiter">›</button>
                </div>` : ''}
              <div class="kb-card-summary-counts">
                <span class="is-reminder">${reminderCount} nächste Schritte</span>
                <span class="is-activity">${activityCount} Aktivitäten</span>
              </div>
            </div>`;
          card.classList.add('kb-summary-ready');
          if (window.feather) window.feather.replace();
        }

        function moveCardCarousel(summary, dir) {
          const slides = [...summary.querySelectorAll('[data-kb-reminder-slide]')];
          if (!slides.length) return;
          let index = Number(summary.dataset.kbReminderIndex || 0);
          index = (index + dir + slides.length) % slides.length;
          summary.dataset.kbReminderIndex = String(index);
          slides.forEach((s, i) => s.classList.toggle('is-active', i === index));
          const counter = summary.querySelector('.kb-card-reminder-counter');
          if (counter) counter.textContent = `${index + 1} / ${slides.length}`;
        }

        function preloadCardSummaries() {
          if (state.preloadRunning) return;
          const cards = [...document.querySelectorAll('.card[data-customer-id]')].filter(card => !card.dataset.kbSummaryLoaded);
          if (!cards.length) return;
          state.preloadRunning = true;
          cards.forEach(card => { ensureReminderButtonBadge(card); card.dataset.kbSummaryLoaded = 'loading'; });
          const contexts = cards.map(contextFromCard);

          fetch(ENDPOINTS.cardsSummary, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            body: JSON.stringify({ contexts })
          })
          .then(async res => {
            if (!res.ok) throw new Error('summary endpoint failed');
            return await res.json();
          })
          .then(data => {
            const items = data.items || data.cards || {};
            cards.forEach((card, idx) => {
              const ctx = contexts[idx];
              const key = ctx.lead_product_list_id || `${ctx.customer_id}:${ctx.alternative_id}:${ctx.product_id}`;
              const payload = items[key] || items[String(ctx.lead_product_list_id)] || items[idx] || {};
              renderCardReminderSummary(card, payload.reminders || [], payload.activities_count || payload.activity_count || 0);
              card.dataset.kbSummaryLoaded = '1';
            });
          })
          .catch(() => {
            Promise.all(cards.map((card) => {
              const ctx = contextFromCard(card);
              const params = new URLSearchParams(ctx);
              return fetch(ENDPOINTS.context + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => renderCardReminderSummary(card, data.reminders || data.next_steps || [], (data.activities || data.activity_logs || []).length))
                .catch(() => renderCardReminderSummary(card, [], 0))
                .finally(() => { card.dataset.kbSummaryLoaded = '1'; });
            }));
          })
          .finally(() => { state.preloadRunning = false; });
        }

        function setLoadingState() {
          state.activities = []; state.reminders = [];
          const activityList = document.getElementById('kbActivityList');
          const reminderList = document.getElementById('kbReminderList');
          const activityCount = document.getElementById('kbActivityCount');
          const reminderCount = document.getElementById('kbReminderCount');
          if (activityList) activityList.innerHTML = '<div class="kb-empty-state">Aktivitäten werden geladen...</div>';
          if (reminderList) reminderList.innerHTML = '<div class="kb-empty-state">Erinnerungen werden geladen...</div>';
          if (activityCount) activityCount.textContent = '0';
          if (reminderCount) reminderCount.textContent = '0';
        }

        function getCombinedItems() { return [...state.activities.map(x => ({...x, bucket:'activity'})), ...state.reminders.map(x => ({...x, bucket:'reminder'}))]; }

        function renderActivities() {
          const list = document.getElementById('kbActivityList');
          const count = document.getElementById('kbActivityCount');
          if (!list) return;
          const q = String(state.search || '').toLowerCase();
          let filtered = getCombinedItems().filter(item => state.filter === 'all' || item.bucket === state.filter);
          if (q) filtered = filtered.filter(item => [item.type, item.title, item.text, item.employee, item.date, item.due_text, item.priority_label, item.status_label].join(' ').toLowerCase().includes(q));
          filtered.sort((a,b) => {
            if (state.sort === 'newest') return dateSortValue(b) - dateSortValue(a);
            if (state.sort === 'type') return String((a.type || a.title || '')).localeCompare(String((b.type || b.title || '')), 'de');
            if (state.sort === 'employee') return String(a.employee || '').localeCompare(String(b.employee || ''), 'de');
            return dateSortValue(a) - dateSortValue(b);
          });
          if (count) count.textContent = String(filtered.length);
          if (!filtered.length) { list.innerHTML = '<div class="kb-empty-state">Keine Aktivitäten gefunden</div>'; return; }
          list.innerHTML = filtered.map(item => {
            const isReminder = item.bucket === 'reminder';
            const priority = item.priority || 'normal';
            const priorityLabel = item.priority_label || translate(DE.priority, priority, 'Normal');
            const dateLabel = isReminder ? (item.due_text || formatGermanDateTime(item.reminder_date, item.reminder_time)) : item.date;
            const title = isReminder ? (item.title || 'Erinnerung') : (item.type || 'Aktivität');
            return `
              <div class="kb-activity-item${isReminder ? ` is-reminder is-${esc(priority)}` : ''}">
                <div class="kb-activity-top"><span>${esc(item.employee || (isReminder ? 'Nicht zugewiesen' : 'System'))}</span><span>${esc(dateLabel || '')}</span></div>
                <div class="kb-activity-text"><strong>${esc(title)}</strong><br>${esc(item.text || '')}</div>
                <div class="kb-activity-meta"><span>${isReminder ? 'Nächster Schritt' : 'Aktivität'}</span>${isReminder ? `<span>Priorität: ${esc(priorityLabel)}</span>` : ''}</div>
              </div>`;
          }).join('');
        }

        function renderReminders() {
          const list = document.getElementById('kbReminderList');
          const count = document.getElementById('kbReminderCount');
          if (!list) return;
          const items = [...state.reminders].sort((a,b) => dateSortValue(a) - dateSortValue(b));
          if (count) count.textContent = String(items.length);
          if (!items.length) { list.innerHTML = '<div class="kb-empty-state">Keine Erinnerung / kein nächster Schritt</div>'; return; }
          list.innerHTML = items.map(item => {
            const priority = item.priority || 'normal';
            const priorityLabel = item.priority_label || translate(DE.priority, priority, 'Normal');
            const statusLabel = item.status_label || translate(DE.status, item.status, 'Offen');
            const due = item.due_text || formatGermanDateTime(item.reminder_date, item.reminder_time);
            return `
              <div class="kb-activity-item is-reminder is-${esc(priority)}" data-reminder-id="${esc(item.id)}">
                <div class="kb-activity-top"><span>${esc(item.employee || 'Nicht zugewiesen')}</span><span>${esc(due || 'Kein Datum')}</span></div>
                <div class="kb-activity-text"><strong>${esc(item.title || 'Erinnerung')}</strong><br>${esc(item.text || '')}</div>
                <div class="kb-activity-meta">
                  <span>Status: ${esc(statusLabel)}</span><span>Priorität: ${esc(priorityLabel)}</span>
                  ${item.id ? `<button type="button" class="lead-reminder-toast-btn" data-kb-reminder-done="${esc(item.id)}">Erledigt</button>` : ''}
                </div>
              </div>`;
          }).join('');
        }

        function openActivityModal() { document.getElementById('kbActivityModal')?.classList.add('is-open'); document.getElementById('kbActivityBackdrop')?.classList.add('is-open'); document.getElementById('kbActivityModal')?.setAttribute('aria-hidden','false'); document.body.style.overflow = 'hidden'; if (window.feather) window.feather.replace(); }
        function closeActivityModal() { document.getElementById('kbActivityModal')?.classList.remove('is-open'); document.getElementById('kbActivityBackdrop')?.classList.remove('is-open'); document.getElementById('kbActivityModal')?.setAttribute('aria-hidden','true'); document.body.style.overflow = ''; }
        function getCustomerNameFromHolder(holder) { return holder?.querySelector('.card-name, .customer-link')?.textContent?.trim() || ''; }

        function openFromElement(btn) {
          const holder = btn.closest('.card, tr.list-row-item, tr, [data-customer-id]');
          const lpl = btn.dataset.leadProductListId || holder?.dataset.leadProductListId || holder?.dataset.leadProductId || '';
          state.active = {
            lead_product_list_id: lpl, customer_id: btn.dataset.customerId || holder?.dataset.customerId || '', alternative_id: btn.dataset.alternativeId || holder?.dataset.alternativeId || '', product_id: btn.dataset.productId || holder?.dataset.productId || '',
            customer_name: btn.dataset.customerName || getCustomerNameFromHolder(holder) || 'Kunde', product_name: btn.dataset.productName || holder?.dataset.productStageName || holder?.dataset.initial || 'Produkt',
            object_text: holder?.dataset.fullAddress || [holder?.dataset.street, holder?.dataset.postcode, holder?.dataset.city].filter(Boolean).join(', ')
          };
          const form = document.getElementById('kbReminderForm'); form?.reset();
          const lplInput = document.getElementById('kb_reminder_lpl_id'); if (lplInput) lplInput.value = state.active.lead_product_list_id;
          const context = document.getElementById('kbActivityContextText'); if (context) context.textContent = [state.active.customer_name, state.active.object_text, state.active.product_name].filter(Boolean).join(' • ');
          setLoadingState(); openActivityModal(); loadContext();
        }

        function loadContext() {
          if (!state.active?.customer_id) { showKbToast('Fehler', 'Customer-ID fehlt auf der Kanban-Karte.', 'error'); return; }
          const params = new URLSearchParams({ customer_id: state.active.customer_id, alternative_id: state.active.alternative_id || '', product_id: state.active.product_id || '', lead_product_list_id: state.active.lead_product_list_id || '' });
          fetch(ENDPOINTS.context + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
            .then(async res => { const data = await res.json().catch(() => ({})); if (!res.ok) throw data; return data; })
            .then(data => {
              state.activities = normalizeActivities(data.activities || data.activity_logs || []);
              state.reminders = normalizeReminders(data.reminders || data.next_steps || [], state.active).sort((a,b) => dateSortValue(a) - dateSortValue(b));
              renderActivities(); renderReminders();
              const card = findKanbanCard(state.active); if (card) renderCardReminderSummary(card, state.reminders, state.activities.length);
            })
            .catch(err => { const msg = err?.message || 'Aktivitäten konnten nicht geladen werden. Prüfe die Route kanban.reminders.context.'; document.getElementById('kbActivityList').innerHTML = `<div class="kb-empty-state">${esc(msg)}</div>`; document.getElementById('kbReminderList').innerHTML = '<div class="kb-empty-state">Erinnerungen konnten nicht geladen werden.</div>'; });
        }

        function submitReminderForm(e) {
          e.preventDefault(); if (!state.active) return;
          const form = e.currentTarget; const btn = form.querySelector('button[type="submit"]'); if (btn) btn.disabled = true;
          fetch(ENDPOINTS.store, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf(), 'Accept':'application/json' }, body:new FormData(form) })
            .then(async res => { const data = await res.json().catch(() => ({})); if (!res.ok) throw data; return data; })
            .then(data => { form.reset(); const lplInput = document.getElementById('kb_reminder_lpl_id'); if (lplInput) lplInput.value = state.active.lead_product_list_id; showKbToast('Erinnerung gespeichert', data.message || 'Der nächste Schritt wurde gespeichert.', 'success'); loadContext(); document.querySelectorAll('.card').forEach(c => delete c.dataset.kbSummaryLoaded); preloadCardSummaries(); })
            .catch(err => { const msg = err?.message || (err?.errors ? Object.values(err.errors || {}).flat().join('\n') : 'Serverfehler beim Speichern.'); showKbToast('Fehler', msg, 'error'); })
            .finally(() => { if (btn) btn.disabled = false; });
        }

        function markReminderDone(id, toast, reloadContext) {
          if (!id) return;
          fetch(`${ENDPOINTS.doneBase}/${encodeURIComponent(id)}/done`, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf(), 'Accept':'application/json' } })
            .then(() => { if (toast) toast.remove(); showKbToast('Erledigt', 'Erinnerung wurde erledigt.', 'success'); if (reloadContext) loadContext(); document.querySelectorAll('.card').forEach(c => delete c.dataset.kbSummaryLoaded); preloadCardSummaries(); })
            .catch(() => showKbToast('Fehler', 'Erinnerung konnte nicht erledigt werden.', 'error'));
        }

        function focusKanbanCard(item) {
          const ctx = { lead_product_list_id: item.lead_product_list_id || item.lead_product_id || '', customer_id: item.customer_id || item.customer?.id || '', alternative_id: item.alternative_id || '', product_id: item.product_id || item.product?.id || '' };
          const card = findKanbanCard(ctx);
          if (card) { card.scrollIntoView({ behavior:'smooth', block:'center', inline:'center' }); card.classList.add('kanban-card-highlight-reminder'); window.setTimeout(() => card.classList.remove('kanban-card-highlight-reminder'), 7000); return; }
          const url = new URL(window.location.href); if (ctx.customer_id) url.searchParams.set('customer_id', ctx.customer_id); if (ctx.alternative_id) url.searchParams.set('alternative_id', ctx.alternative_id); if (ctx.product_id) url.searchParams.set('product_id', ctx.product_id); if (ctx.lead_product_list_id) url.searchParams.set('lead_product_list_id', ctx.lead_product_list_id); window.location.href = url.toString();
        }

        function showLeadReminderToast(item) {
          const wrap = document.getElementById('leadReminderToastWrap'); if (!wrap) return;
          const toast = document.createElement('div'); const priority = String(item.priority || item.type || 'success').toLowerCase(); toast.className = 'lead-reminder-toast ' + esc(priority); toast.dataset.reminderId = item.id || '';
          const customerName = item.customer ? ((item.customer.firma || '') || `${item.customer.name || ''} ${item.customer.lastname || ''}`.trim()) : (item.customer_name || 'Kunde');
          const productName = item.product ? (item.product.article_group || item.product.initial || '') : (item.product_name || '');
          const due = item.due_text || formatGermanDateTime(item.reminder_date || item.due_date || '', item.reminder_time || item.due_time || '');
          toast.innerHTML = `<div class="lead-reminder-toast-head"><span><i class="feather icon-bell"></i> ${esc(item.title || 'Erinnerung')}</span><button type="button" class="lead-reminder-toast-close" aria-label="Schließen">×</button></div><div class="lead-reminder-toast-body"><strong>${esc(customerName)}</strong><br>${productName ? `${esc(productName)}<br>` : ''}${due ? `<small><strong>Fällig:</strong> ${esc(due)}</small><br>` : ''}<small>${esc(item.description || item.message || '')}</small></div><div class="lead-reminder-toast-actions"><button type="button" class="lead-reminder-toast-btn" data-reminder-focus>Kanban anzeigen</button>${item.id ? `<button type="button" class="lead-reminder-toast-btn" data-reminder-done>Erledigt</button>` : ''}</div>`;
          toast.addEventListener('click', e => { if (e.target.closest('.lead-reminder-toast-close')) { e.preventDefault(); toast.remove(); return; } if (e.target.closest('[data-reminder-done]')) { e.preventDefault(); markReminderDone(item.id, toast, false); return; } focusKanbanCard(item); toast.remove(); });
          wrap.prepend(toast); if (window.feather) window.feather.replace(); window.setTimeout(() => toast.parentNode && toast.remove(), 20000);
        }
        function showKbToast(title, message, type) { const priority = type === 'error' ? 'critical' : (type === 'warning' ? 'important' : 'success'); showLeadReminderToast({ title, description: message, priority }); }
        function checkDueLeadReminders() { fetch(ENDPOINTS.due, { headers:{ 'Accept':'application/json' } }).then(res => res.json()).then(data => (data.items || []).forEach(showLeadReminderToast)).catch(() => {}); }

        function bootLeadActivityAddon() {
          if (state.booted) return; state.booted = true;
          const modal = document.getElementById('kbActivityModal'); const backdrop = document.getElementById('kbActivityBackdrop'); const closeBtn = document.getElementById('kbActivityCloseBtn'); const form = document.getElementById('kbReminderForm'); if (!modal || !backdrop || !form) return;
          document.addEventListener('click', function(e) {
            const carouselBtn = e.target.closest('[data-kb-reminder-prev], [data-kb-reminder-next]');
            if (carouselBtn) { e.preventDefault(); e.stopPropagation(); const summary = carouselBtn.closest('.kb-reminder-summary'); if (summary) moveCardCarousel(summary, carouselBtn.hasAttribute('data-kb-reminder-next') ? 1 : -1); return; }
            const doneBtn = e.target.closest('[data-kb-reminder-done]'); if (doneBtn) { e.preventDefault(); e.stopPropagation(); markReminderDone(doneBtn.dataset.kbReminderDone, null, true); return; }
            const btn = e.target.closest('[data-open-lead-reminder], .kb-open-activity-panel'); if (btn) { e.preventDefault(); e.stopPropagation(); openFromElement(btn); }
          }, true);
          closeBtn?.addEventListener('click', closeActivityModal); backdrop?.addEventListener('click', closeActivityModal); document.addEventListener('keydown', e => { if (e.key === 'Escape') closeActivityModal(); });
          document.querySelectorAll('[data-kb-activity-filter]').forEach(btn => btn.addEventListener('click', function(){ state.filter = this.dataset.kbActivityFilter || 'all'; document.querySelectorAll('[data-kb-activity-filter]').forEach(b => b.classList.remove('is-active')); this.classList.add('is-active'); renderActivities(); }));
          document.getElementById('kbActivitySearch')?.addEventListener('input', function(){ state.search = this.value || ''; renderActivities(); });
          document.getElementById('kbActivitySort')?.addEventListener('change', function(){ state.sort = this.value || 'oldest'; renderActivities(); });
          form.addEventListener('submit', submitReminderForm);
          window.openLeadActivityPanel = openFromElement; window.checkDueLeadReminders = checkDueLeadReminders; window.preloadLeadReminderSummaries = preloadCardSummaries;
          preloadCardSummaries(); window.setTimeout(preloadCardSummaries, 800); window.setTimeout(preloadCardSummaries, 2000);
          const mo = new MutationObserver(() => window.setTimeout(preloadCardSummaries, 250)); mo.observe(document.body, { childList:true, subtree:true });
          if (!state.pollingStarted) { state.pollingStarted = true; checkDueLeadReminders(); window.setInterval(checkDueLeadReminders, 30000); }
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bootLeadActivityAddon); else bootLeadActivityAddon();
      })();

      

/* ===================== Extracted inline script block #29 ===================== */
            (function () {
                'use strict';

                if (window.__KANBAN_LEAD_STAGE_SUB_STAGE_ADMIN__) return;
                window.__KANBAN_LEAD_STAGE_SUB_STAGE_ADMIN__ = true;

                const base = '/task-phase/ajax/stage-admin';
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || (window.KANBAN_BOOT?.csrf || '');
                const qs = (s, r = document) => r.querySelector(s);
                const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));
                const modal = qs('#kanbanStageAdminModal');
                const list = qs('#kbsaStageList');
                const err = qs('#kbsaError');

                function esc(v) {
                    return String(v ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function showError(message) {
                    if (!err) return;
                    err.textContent = message || 'Fehler';
                    err.classList.add('is-visible');
                }

                function clearError() {
                    if (!err) return;
                    err.textContent = '';
                    err.classList.remove('is-visible');
                }

                function notify(type, message) {
                    if (window.toastr && toastr[type]) {
                        toastr[type](message);
                        return;
                    }
                    if (window.Swal && type === 'success') {
                        Swal.fire({ icon: 'success', title: message, timer: 900, showConfirmButton: false });
                        return;
                    }
                    console[type === 'error' ? 'error' : 'log'](message);
                }

                function apiMessage(payload, fallback) {
                    if (!payload) return fallback || 'Fehler';
                    if (payload.message) return payload.message;
                    if (payload.errors) return Object.values(payload.errors).flat().join('\n');
                    return fallback || 'Fehler';
                }

                function refreshIcons() {
                    if (window.feather) {
                        window.requestAnimationFrame(() => feather.replace());
                    }
                }

                function refreshKanbanAfterStageChange() {
                    if (typeof window.LeadUIFetchKanban === 'function') {
                        window.LeadUIFetchKanban(window.State?.filtersQS || '');
                        return;
                    }

                    if (typeof window.fetchKanbanView === 'function') {
                        window.fetchKanbanView(window.State?.filtersQS || '');
                        return;
                    }

                    if (typeof window.loadKanban === 'function') {
                        window.loadKanban();
                        return;
                    }
                }

                function openModal() {
                    if (!modal) return;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    loadStages();
                    refreshIcons();
                }

                function closeModal() {
                    if (!modal) return;
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }

                async function requestJson(url, options = {}) {
                    const response = await fetch(url, {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            ...(options.headers || {})
                        },
                        ...options
                    });

                    const raw = await response.text();
                    let data = {};
                    try {
                        data = raw ? JSON.parse(raw) : {};
                    } catch (e) {
                        data = { message: raw || 'Ungültige Serverantwort.' };
                    }

                    if (!response.ok || data.success === false) {
                        const error = new Error(apiMessage(data, 'Anfrage fehlgeschlagen.'));
                        error.payload = data;
                        throw error;
                    }

                    return data;
                }

                function postJson(url, data = {}, method = 'POST') {
                    return requestJson(url, {
                        method,
                        body: JSON.stringify(data)
                    });
                }

                function stageItems() {
                    return qsa('#kbsaStageList > .kbsa-stage')
                        .map(el => parseInt(el.dataset.stageId, 10))
                        .filter(Boolean);
                }

                function subStageItems(stageEl) {
                    return qsa('.js-kbsa-sub-list > .kbsa-sub', stageEl)
                        .map(el => parseInt(el.dataset.subId, 10))
                        .filter(Boolean);
                }

                function renderStages(stages) {
                    if (!list) return;

                    if (!Array.isArray(stages) || !stages.length) {
                        list.innerHTML = '<div class="kbsa-small">Keine LeadStages vorhanden.</div>';
                        return;
                    }

                    list.innerHTML = stages.map(stage => `
                        <div class="kbsa-stage" data-stage-id="${stage.id}">
                            <div class="kbsa-stage-head">
                                <span class="kbsa-handle" title="Ziehen zum Sortieren"><i class="feather icon-menu"></i></span>
                                <input class="kbsa-input js-kbsa-stage-name" value="${esc(stage.name)}" placeholder="Name">
                                <input class="kbsa-input js-kbsa-stage-key" value="${esc(stage.key)}" placeholder="Key" readonly disabled title="Technischer Key wird beim Umbenennen nicht geändert.">
                                <input class="kbsa-input js-kbsa-stage-color" type="color" value="${esc(stage.color || '#93c21c')}">
                                <input class="kbsa-input js-kbsa-stage-icon" value="${esc(stage.icon || 'columns')}" placeholder="Icon">
                                <label class="kbsa-check"><input type="checkbox" class="js-kbsa-stage-active" ${stage.is_active ? 'checked' : ''}> Aktiv</label>
                                <span class="kbsa-usage">${Number(stage.usage_count || 0)} Einträge</span>
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    <button type="button" class="kbsa-btn-soft js-kbsa-stage-save"><i class="feather icon-save"></i></button>
                                    <button type="button" class="kbsa-btn-danger js-kbsa-stage-delete"><i class="feather icon-trash"></i></button>
                                </div>
                            </div>

                            <div class="kbsa-sub-list">
                                <div class="kbsa-toolbar" style="grid-template-columns:minmax(160px,1fr) 130px 90px 90px auto; margin-bottom:10px;">
                                    <input class="kbsa-input js-kbsa-sub-name" placeholder="Neue SubStage">
                                    <input class="kbsa-input js-kbsa-sub-key" placeholder="Key auto">
                                    <input class="kbsa-input js-kbsa-sub-color" type="color" value="${esc(stage.color || '#93c21c')}">
                                    <input class="kbsa-input js-kbsa-sub-icon" value="list">
                                    <button type="button" class="kbsa-btn js-kbsa-sub-create">
                                        <i class="feather icon-plus"></i> SubStage
                                    </button>
                                </div>

                                <div class="js-kbsa-sub-list">
                                    ${(stage.sub_stages || []).map(sub => `
                                        <div class="kbsa-sub" data-sub-id="${sub.id}">
                                            <span class="kbsa-handle" title="Ziehen zum Sortieren"><i class="feather icon-menu"></i></span>
                                            <input class="kbsa-input js-kbsa-sub-edit-name" value="${esc(sub.name)}">
                                            <input class="kbsa-input js-kbsa-sub-edit-key" value="${esc(sub.key)}">
                                            <input class="kbsa-input js-kbsa-sub-edit-color" type="color" value="${esc(sub.color || stage.color || '#93c21c')}">
                                            <input class="kbsa-input js-kbsa-sub-edit-icon" value="${esc(sub.icon || 'list')}">
                                            <label class="kbsa-check"><input type="checkbox" class="js-kbsa-sub-edit-active" ${sub.is_active ? 'checked' : ''}> Aktiv</label>
                                            <span class="kbsa-usage">${Number(sub.usage_count || 0)} Einträge</span>
                                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                                <button type="button" class="kbsa-btn-soft js-kbsa-sub-save"><i class="feather icon-save"></i></button>
                                                <button type="button" class="kbsa-btn-danger js-kbsa-sub-delete"><i class="feather icon-trash"></i></button>
                                            </div>
                                        </div>
                                    `).join('') || '<div class="kbsa-small">Keine SubStages.</div>'}
                                </div>
                            </div>
                        </div>
                    `).join('');

                    initSortables();
                    refreshIcons();
                }

                async function loadStages() {
                    clearError();

                    if (list) {
                        list.innerHTML = '<div class="kbsa-small">Lade LeadStages...</div>';
                    }

                    try {
                        const data = await requestJson(base + '/stages', { method: 'GET' });
                        renderStages(data.stages || data.data || []);
                    } catch (error) {
                        showError(error.message || 'LeadStages konnten nicht geladen werden.');
                    }
                }

                function initSortables() {
                    if (!window.jQuery || !jQuery.fn.sortable) return;

                    const stageList = jQuery('#kbsaStageList');

                    if (stageList.data('ui-sortable')) {
                        stageList.sortable('destroy');
                    }

                    stageList.sortable({
                        items: '> .kbsa-stage',
                        handle: '.kbsa-handle',
                        placeholder: 'kbsa-stage',
                        stop: function () {
                            postJson(base + '/stages/reorder', { items: stageItems() })
                                .then(() => {
                                    notify('success', 'Phasen-Reihenfolge gespeichert.');
                                    refreshKanbanAfterStageChange();
                                })
                                .catch(error => showError(error.message || 'Phasen-Sortierung fehlgeschlagen.'));
                        }
                    });

                    jQuery('.js-kbsa-sub-list').each(function () {
                        const subList = jQuery(this);

                        if (subList.data('ui-sortable')) {
                            subList.sortable('destroy');
                        }

                        subList.sortable({
                            items: '> .kbsa-sub',
                            handle: '.kbsa-handle',
                            placeholder: 'kbsa-sub',
                            stop: function () {
                                const stageEl = subList.closest('.kbsa-stage')[0];

                                postJson(base + '/stages/' + stageEl.dataset.stageId + '/sub-stages/reorder', {
                                    items: subStageItems(stageEl)
                                })
                                    .then(() => {
                                        notify('success', 'SubStage-Reihenfolge gespeichert.');
                                        refreshKanbanAfterStageChange();
                                    })
                                    .catch(error => showError(error.message || 'SubStage-Sortierung fehlgeschlagen.'));
                            }
                        });
                    });
                }

                qsa('.kb-stage-admin-open, #btnOpenKanbanStageAdmin').forEach(button => {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        openModal();
                    });
                });

                qsa('[data-kbsa-close]').forEach(button => button.addEventListener('click', closeModal));

                modal?.addEventListener('click', function (event) {
                    if (event.target === modal) closeModal();
                });

                qs('#kbsaReloadStages')?.addEventListener('click', loadStages);

                qs('#kbsaCreateStage')?.addEventListener('click', async function () {
                    clearError();

                    try {
                        await postJson(base + '/stages', {
                            name: qs('#kbsaStageName')?.value || '',
                            // Key is generated by backend. Do not send a user-entered key from the legacy modal.
                            color: qs('#kbsaStageColor')?.value || '#93c21c',
                            icon: qs('#kbsaStageIcon')?.value || 'columns',
                            is_active: qs('#kbsaStageActive')?.checked ? 1 : 0
                        });

                        qs('#kbsaStageName').value = '';
                        qs('#kbsaStageKey').value = '';
                        notify('success', 'Phase erstellt.');
                        await loadStages();
                        refreshKanbanAfterStageChange();
                    } catch (error) {
                        showError(error.message || 'Phase konnte nicht erstellt werden.');
                    }
                });

                document.addEventListener('click', async function (event) {
                    const stageEl = event.target.closest('.kbsa-stage');
                    const subEl = event.target.closest('.kbsa-sub');

                    if (event.target.closest('.js-kbsa-stage-save') && stageEl) {
                        clearError();

                        try {
                            await postJson(base + '/stages/' + stageEl.dataset.stageId + '/update', {
                                // IMPORTANT:
                                // Do not send `key` when renaming a stage.
                                // The key is the technical value stored in lead_product_lists.status.
                                // Renaming must only update the visible label/name.
                                name: qs('.js-kbsa-stage-name', stageEl).value,
                                color: qs('.js-kbsa-stage-color', stageEl).value,
                                icon: qs('.js-kbsa-stage-icon', stageEl).value || 'columns',
                                is_active: qs('.js-kbsa-stage-active', stageEl).checked ? 1 : 0
                            });

                            notify('success', 'Phase gespeichert.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'Phase konnte nicht gespeichert werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-stage-delete') && stageEl) {
                        const ask = window.Swal
                            ? await Swal.fire({
                                icon: 'warning',
                                title: 'Phase löschen?',
                                text: 'Diese Aktion kann nicht rückgängig gemacht werden.',
                                showCancelButton: true,
                                confirmButtonText: 'Löschen',
                                cancelButtonText: 'Abbrechen',
                                confirmButtonColor: '#ef4444'
                            })
                            : { isConfirmed: confirm('Phase löschen?') };

                        if (!ask.isConfirmed) return;

                        try {
                            await postJson(base + '/stages/' + stageEl.dataset.stageId, {}, 'DELETE');
                            notify('success', 'Phase gelöscht.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'Phase konnte nicht gelöscht werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-sub-create') && stageEl) {
                        clearError();

                        try {
                            await postJson(base + '/stages/' + stageEl.dataset.stageId + '/sub-stages', {
                                lead_stage_id: parseInt(stageEl.dataset.stageId, 10),
                                name: qs('.js-kbsa-sub-name', stageEl).value,
                                key: qs('.js-kbsa-sub-key', stageEl).value,
                                color: qs('.js-kbsa-sub-color', stageEl).value,
                                icon: qs('.js-kbsa-sub-icon', stageEl).value || 'list',
                                is_active: 1
                            });

                            notify('success', 'SubStage erstellt.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'SubStage konnte nicht erstellt werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-sub-save') && subEl && stageEl) {
                        clearError();

                        try {
                            await postJson(base + '/sub-stages/' + subEl.dataset.subId + '/update', {
                                lead_stage_id: parseInt(stageEl.dataset.stageId, 10),
                                name: qs('.js-kbsa-sub-edit-name', subEl).value,
                                key: qs('.js-kbsa-sub-edit-key', subEl).value,
                                color: qs('.js-kbsa-sub-edit-color', subEl).value,
                                icon: qs('.js-kbsa-sub-edit-icon', subEl).value || 'list',
                                is_active: qs('.js-kbsa-sub-edit-active', subEl).checked ? 1 : 0
                            });

                            notify('success', 'SubStage gespeichert.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'SubStage konnte nicht gespeichert werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-sub-delete') && subEl) {
                        const ask = window.Swal
                            ? await Swal.fire({
                                icon: 'warning',
                                title: 'SubStage löschen?',
                                text: 'Diese Aktion kann nicht rückgängig gemacht werden.',
                                showCancelButton: true,
                                confirmButtonText: 'Löschen',
                                cancelButtonText: 'Abbrechen',
                                confirmButtonColor: '#ef4444'
                            })
                            : { isConfirmed: confirm('SubStage löschen?') };

                        if (!ask.isConfirmed) return;

                        try {
                            await postJson(base + '/sub-stages/' + subEl.dataset.subId, {}, 'DELETE');
                            notify('success', 'SubStage gelöscht.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'SubStage konnte nicht gelöscht werden.');
                        }
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
                        closeModal();
                    }
                });
            })();
        

/* ===================== Extracted inline script block #30 ===================== */
      /* =========================================================
         Auto-load Kanban next-step preview on board/list load
         - Uses the same task context endpoint as the modal
         - Updates card preview, list status, badges, overdue animation/toast
         - Adds compact analytics toggle to each Kanban column
         ========================================================= */
      (function () {
        'use strict';

        if (window.__kanbanNextStepAutoPreviewBooted) return;
        window.__kanbanNextStepAutoPreviewBooted = true;

        const CONTEXT_URL = (leadProductId) => `/admin/kanban/tasks/context/${encodeURIComponent(leadProductId)}`;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || (window.KANBAN_BOOT?.csrf || '');
        const loaded = new Map();
        const warned = new Set();
        let queueRunning = false;

        const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({
          '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m]));

        const cssEscape = (value) => window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\$&');

        function parseDate(value) {
          if (!value) return null;
          const normalized = String(value).includes('T') ? String(value) : String(value).replace(' ', 'T');
          const date = new Date(normalized);
          return Number.isNaN(date.getTime()) ? null : date;
        }

        function formatDateTimeDE(value) {
          const date = parseDate(value);
          if (!date) return '-';
          return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: '2-digit' }) + ' ' +
            date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        }

        function statusLabel(status) {
          return ({ open: 'Offen', scheduled: 'Geplant', in_progress: 'In Bearbeitung', done: 'Erledigt', cancelled: 'Abgebrochen' }[status] || status || 'Offen');
        }

        function isTaskOverdue(task) {
          const status = String(task?.status || '').toLowerCase();
          if (['done', 'cancelled'].includes(status)) return false;
          if (task?.is_overdue) return true;
          const end = parseDate(task?.planned_end_at || task?.due_at || task?.due_date);
          return !!(end && end.getTime() < Date.now());
        }

        function templateTitleFromContext(data) {
          const templates = Array.isArray(data?.templates) ? data.templates : [];
          for (const phase of templates) {
            const activities = Array.isArray(phase?.activities) ? phase.activities : [];
            const firstActivity = activities.find(a => a && (a.title || a.description));
            if (firstActivity) {
              return {
                title: firstActivity.title || phase.phase_name || 'Nächste Aufgabe',
                description: firstActivity.description || phase.description || '',
                estimated_minutes: firstActivity.estimated_minutes || null,
                photo_required: !!firstActivity.photo_required,
                source: 'task_phase'
              };
            }
            if (phase?.phase_name) {
              return {
                title: phase.phase_name,
                description: phase.description || '',
                estimated_minutes: null,
                photo_required: false,
                source: 'task_phase'
              };
            }
          }
          return null;
        }

        function summarizeContext(data, fallback = {}) {
          const tasks = Array.isArray(data?.tasks) ? data.tasks : [];
          const openTasks = tasks.filter(t => !['done', 'cancelled'].includes(String(t?.status || '').toLowerCase()));
          const doneTasks = tasks.filter(t => String(t?.status || '').toLowerCase() === 'done');
          const current = openTasks[0] || null;
          const previous = doneTasks.slice().sort((a, b) => String(b.done_at || b.updated_at || '').localeCompare(String(a.done_at || a.updated_at || '')))[0] || null;
          const template = current ? null : templateTitleFromContext(data);
          const next = current || template || null;
          const overdueTasks = openTasks.filter(isTaskOverdue);
          const ctx = data?.context || {};

          return {
            lead_product_list_id: ctx.lead_product_list_id || fallback.leadProductId || '',
            customer_name: ctx.customer_name || fallback.customerName || '',
            product_name: ctx.product_name || fallback.productName || '',
            stage_label: ctx.stage_label || fallback.stageLabel || '',
            sub_stage_label: ctx.sub_stage_label || fallback.subStageLabel || '',
            title: next?.title || 'Noch keine Aufgabe',
            description: next?.description || '',
            previous_title: previous?.title || '-',
            open_count: openTasks.length,
            done_count: doneTasks.length,
            estimated_minutes: next?.estimated_minutes || null,
            photo_required: !!next?.photo_required,
            status: current?.status || (template ? 'open' : ''),
            planned_start_at: current?.planned_start_at || null,
            planned_end_at: current?.planned_end_at || null,
            overdue: overdueTasks.length > 0,
            overdue_count: overdueTasks.length,
            source: current ? 'saved_task' : (template ? 'task_phase' : null),
            stage_landed_at: fallback.stageLandedAt || fallback.updatedAt || fallback.createdAt || null
          };
        }

        async function fetchContext(leadProductId) {
          if (!leadProductId) return null;
          if (loaded.has(String(leadProductId))) return loaded.get(String(leadProductId));
          const promise = fetch(CONTEXT_URL(leadProductId), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
          }).then(async (res) => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok || data.success === false) throw new Error(data.message || 'Aufgaben konnten nicht geladen werden.');
            return data;
          }).catch((error) => {
            console.warn('[Kanban next-step preload]', leadProductId, error);
            return null;
          });
          loaded.set(String(leadProductId), promise);
          return promise;
        }

        function previewHtml(summary, leadProductId) {
          return `
            <div class="kb-next-step-preview ${summary.source ? 'kb-has-next-step' : ''} ${summary.overdue ? 'kb-overdue' : ''}">
              <div class="kb-next-step-preview-head">
                <span><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</span>
                <button type="button" class="kb-next-step-preview-btn" data-open-kanban-task-management data-lead-product-list-id="${esc(leadProductId)}">Details</button>
              </div>
              <div class="kb-next-step-preview-line"><i class="feather icon-log-in"></i><span>Seit: <strong>${esc(formatDateTimeDE(summary.stage_landed_at))}</strong></span></div>
              <div class="kb-next-step-preview-line"><i class="feather icon-check-circle"></i><span>Vorher: <strong>${esc(summary.previous_title || '-')}</strong></span></div>
              <div class="kb-next-step-preview-line"><i class="feather icon-list"></i><span><strong>${esc(summary.title || 'Noch keine Aufgabe')}</strong></span></div>
              ${summary.planned_end_at ? `<div class="kb-next-step-preview-line"><i class="feather icon-clock"></i><span>Fällig: <strong>${esc(formatDateTimeDE(summary.planned_end_at))}</strong></span></div>` : ''}
              <div class="kb-next-step-preview-line"><i class="feather icon-activity"></i><span>Offen: <strong>${esc(summary.open_count)}</strong> · Erledigt: <strong>${esc(summary.done_count)}</strong>${summary.overdue ? ` · <strong class="text-danger">Überfällig: ${esc(summary.overdue_count)}</strong>` : ''}</span></div>
            </div>`;
        }

        function listStatusHtml(row, summary) {
          const stage = row?.dataset?.stage || summary.stage_label || 'lead';
          const status = summary.overdue ? 'Überfällig' : (summary.status ? statusLabel(summary.status) : 'Offen');
          return `
            <div class="kb-status kb-list-next-step-status ${summary.overdue ? 'kb-overdue' : ''}" title="Nächster Schritt">
              <div class="kb-list-next-head">
                <span class="badge ${summary.overdue ? 'badge-danger bg-danger' : 'badge-primary bg-primary'}">${esc(status)}</span>
                <button type="button" class="kb-next-step-preview-btn" data-open-kanban-task-management data-lead-product-list-id="${esc(summary.lead_product_list_id || row?.dataset?.leadProductId || '')}">Details</button>
              </div>
              <div class="kb-list-next-title"><i class="feather icon-arrow-right-circle"></i><span>${esc(summary.title || 'Noch keine Aufgabe')}</span></div>
              <div class="kb-list-next-meta mt-1">
                <i class="feather icon-layers"></i><span>Phase: <strong>${esc(summary.stage_label || stage)}</strong>${summary.sub_stage_label ? ' / ' + esc(summary.sub_stage_label) : ''}</span>
                <i class="feather icon-log-in"></i><span>Seit: <strong>${esc(formatDateTimeDE(summary.stage_landed_at))}</strong></span>
                <i class="feather icon-check-circle"></i><span>Vorher: <strong>${esc(summary.previous_title || '-')}</strong></span>
                ${summary.planned_end_at ? `<i class="feather icon-clock"></i><span>Fällig: <strong>${esc(formatDateTimeDE(summary.planned_end_at))}</strong></span>` : ''}
              </div>
              <div class="kb-list-next-counts">
                <span class="kb-mini-pill">Offen: ${esc(summary.open_count)}</span>
                <span class="kb-mini-pill">Erledigt: ${esc(summary.done_count)}</span>
                ${summary.overdue ? `<span class="kb-mini-pill red">Überfällig: ${esc(summary.overdue_count)}</span>` : ''}
              </div>
            </div>`;
        }

        function updateBadge(root, summary) {
          const badge = root.querySelector('[data-kanban-task-count]');
          if (!badge) return;
          const count = Number(summary.open_count || 0);
          badge.textContent = count > 99 ? '99+' : String(count);
          badge.classList.toggle('d-none', count <= 0);
          badge.style.display = count > 0 ? '' : 'none';
        }

        function warnOverdueOnce(summary) {
          if (!summary.overdue || !summary.lead_product_list_id) return;
          const key = String(summary.lead_product_list_id);
          if (warned.has(key) || sessionStorage.getItem('kb_overdue_warned_' + key)) return;
          warned.add(key);
          sessionStorage.setItem('kb_overdue_warned_' + key, '1');
          const message = `Überfällige Aufgabe: ${summary.customer_name || 'Lead'}${summary.product_name ? ' · ' + summary.product_name : ''} — ${summary.title || ''}`;
          if (window.toastr) window.toastr.warning(message, 'Kanban Aufgabe überfällig');
          else if (window.Swal) Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: message, showConfirmButton: false, timer: 5500 });
          else console.warn(message);
        }

        function updateCard(card, summary) {
          if (!card) return;
          const leadProductId = card.dataset.leadProductListId || card.dataset.leadProductId || summary.lead_product_list_id || '';
          const old = card.querySelector('.kb-next-step-preview');
          const html = previewHtml(summary, leadProductId);
          if (old) old.outerHTML = html;
          else {
            const before = card.querySelector('.employeeList') || card.querySelector('.card-actions');
            if (before) before.insertAdjacentHTML('beforebegin', html);
          }
          card.classList.toggle('kb-task-overdue-card', !!summary.overdue);
          updateBadge(card, summary);
          warnOverdueOnce(summary);
        }

        function updateRow(row, summary) {
          if (!row) return;
          const statusCell = row.querySelector('td:nth-child(6)') || row.querySelector('td');
          const old = row.querySelector('.kb-status');
          const html = listStatusHtml(row, summary);
          if (old) old.outerHTML = html;
          else if (statusCell) statusCell.insertAdjacentHTML('afterbegin', html);
          row.classList.toggle('kb-task-overdue-row', !!summary.overdue);
          warnOverdueOnce(summary);
        }

        function fallbackFromElement(el) {
          return {
            leadProductId: el.dataset.leadProductListId || el.dataset.leadProductId || '',
            customerName: el.dataset.customerName || el.querySelector?.('.card-name')?.textContent?.trim() || el.querySelector?.('.customer-link')?.textContent?.trim() || '',
            productName: el.dataset.productName || el.dataset.initial || '',
            stageLabel: el.dataset.stage || el.dataset.companyStage || '',
            stageLandedAt: el.dataset.stageLandedAt || el.dataset.updatedAt || el.dataset.createdAt || '',
            updatedAt: el.dataset.updatedAt || '',
            createdAt: el.dataset.createdAt || ''
          };
        }

        async function hydrateElement(el) {
          const id = el.dataset.leadProductListId || el.dataset.leadProductId || '';
          if (!id || el.dataset.nextStepAutoLoaded === '1') return;
          el.dataset.nextStepAutoLoaded = '1';
          const data = await fetchContext(id);
          if (!data) return;
          const summary = summarizeContext(data, fallbackFromElement(el));
          if (el.matches('tr.list-row-item')) updateRow(el, summary);
          else updateCard(el, summary);
          if (window.feather) window.feather.replace();
        }

        function hydrateVisibleNextSteps(root = document) {
          const nodes = Array.from(root.querySelectorAll('.card[data-lead-product-list-id], .card[data-lead-product-id], tr.list-row-item[data-lead-product-id], tr.list-row-item[data-lead-product-list-id]'));
          const todo = nodes.filter(el => el.dataset.nextStepAutoLoaded !== '1');
          if (!todo.length || queueRunning) return;
          queueRunning = true;
          let index = 0;
          const CONCURRENCY = 3;
          const worker = async () => {
            while (index < todo.length) {
              const el = todo[index++];
              await hydrateElement(el);
            }
          };
          Promise.all(Array.from({ length: CONCURRENCY }, worker)).finally(() => { queueRunning = false; });
        }

        function installAnalyticsToggles(root = document) {
          root.querySelectorAll('.column').forEach((column) => {
            const h3 = column.querySelector('h3');
            const actions = h3?.querySelector('.kb-column-actions');
            if (!h3 || !actions || actions.querySelector('[data-kb-toggle-analytics]')) return;
            const key = h3.dataset.workflowStageKey || column.id || 'stage';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kb-column-analytics-toggle';
            btn.dataset.kbToggleAnalytics = key;
            btn.title = 'Analytics ein-/ausblenden';
            btn.innerHTML = '<i class="feather icon-bar-chart-2"></i>';
            actions.appendChild(btn);
            const hidden = localStorage.getItem('kb_analytics_hidden_' + key) === '1';
            column.classList.toggle('kb-analytics-hidden', hidden);
          });
        }

        document.addEventListener('click', function (event) {
          const btn = event.target.closest('[data-kb-toggle-analytics]');
          if (!btn) return;
          event.preventDefault();
          event.stopPropagation();
          const column = btn.closest('.column');
          const key = btn.dataset.kbToggleAnalytics || column?.id || 'stage';
          const hidden = !column.classList.contains('kb-analytics-hidden');
          column.classList.toggle('kb-analytics-hidden', hidden);
          localStorage.setItem('kb_analytics_hidden_' + key, hidden ? '1' : '0');
        }, true);

        function boot(root = document) {
          installAnalyticsToggles(root);
          hydrateVisibleNextSteps(root);
        }

        document.addEventListener('DOMContentLoaded', function () {
          boot();
          setTimeout(boot, 300);
          setTimeout(boot, 1200);
        });

        const observer = new MutationObserver((mutations) => {
          let shouldBoot = false;
          for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
              if (node.nodeType === 1 && (node.matches?.('.card, tr.list-row-item, .column') || node.querySelector?.('.card, tr.list-row-item, .column'))) {
                shouldBoot = true;
                break;
              }
            }
            if (shouldBoot) break;
          }
          if (shouldBoot) setTimeout(() => boot(), 60);
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });

        window.refreshKanbanNextStepPreviews = function () {
          document.querySelectorAll('[data-next-step-auto-loaded="1"]').forEach(el => delete el.dataset.nextStepAutoLoaded);
          boot();
        };
      })();
    

/* ===================== Extracted inline script block #31 ===================== */
    /* ===== Boss final: bulk next-step preload + column analytics default hidden ===== */
    (function () {
      'use strict';

      if (window.__bossKanbanNextStepBulkBooted) return;
      window.__bossKanbanNextStepBulkBooted = true;

      const SUMMARY_URL = `/admin/kanban/tasks/summaries`;
      const CONTEXT_URL = (id) => `/admin/kanban/tasks/context/${encodeURIComponent(id)}`;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || (window.KANBAN_BOOT?.csrf || '');

      const loaded = new Set();
      const overdueToastShown = new Set();

      const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[m]));

      const cssEscape = (value) => window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&');

      function fmtDate(value) {
        if (!value) return '-';
        const s = String(value).replace('T', ' ');
        const d = new Date(s);
        if (!isNaN(d.getTime())) {
          return d.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: '2-digit' }) + ' ' +
            d.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        }
        return s.slice(0, 16);
      }

      function getLeadProductId(el) {
        return el?.dataset?.leadProductListId || el?.dataset?.leadProductId || el?.dataset?.id || '';
      }

      function collectVisibleLeadProductIds(root = document) {
        const ids = new Set();

        root.querySelectorAll(
          '.card[data-lead-product-list-id], .card[data-lead-product-id], tr[data-lead-product-id], [data-open-kanban-task-management][data-lead-product-list-id]'
        ).forEach((el) => {
          const id = getLeadProductId(el);
          if (id && /^\d+$/.test(String(id))) ids.add(String(id));
        });

        return Array.from(ids);
      }

      function findCard(id) {
        const safe = cssEscape(id);
        return document.querySelector(`.card[data-lead-product-list-id="${safe}"], .card[data-lead-product-id="${safe}"]`);
      }

      function findListRow(id) {
        const safe = cssEscape(id);
        return document.querySelector(`tr#row-${safe}, tr[data-lead-product-id="${safe}"], tr[data-lead-product-list-id="${safe}"]`);
      }

      function firstTemplateTitle(templates, offset = 0) {
        const flat = [];
        (templates || []).forEach((phase) => {
          const acts = Array.isArray(phase.activities) ? phase.activities : [];
          if (acts.length) {
            acts.forEach((activity) => flat.push({
              title: activity.title || phase.phase_name,
              description: activity.description || phase.description || '',
              minutes: activity.estimated_minutes || ''
            }));
          } else if (phase.phase_name) {
            flat.push({ title: phase.phase_name, description: phase.description || '', minutes: '' });
          }
        });
        return flat[offset] || null;
      }

      function normalizeSummary(id, payload) {
        const summary = payload?.summary || payload || {};
        const tasks = Array.isArray(payload?.tasks) ? payload.tasks : (Array.isArray(summary.tasks) ? summary.tasks : []);
        const templates = Array.isArray(payload?.templates) ? payload.templates : (Array.isArray(summary.templates) ? summary.templates : []);

        const openTasks = tasks.filter(t => !['done', 'cancelled'].includes(String(t.status || '').toLowerCase()));
        const doneTasks = tasks.filter(t => String(t.status || '').toLowerCase() === 'done');
        const previous = doneTasks.length ? doneTasks[doneTasks.length - 1] : null;
        const current = openTasks[0] || null;
        const tmplCurrent = firstTemplateTitle(templates, 0);
        const tmplNext = firstTemplateTitle(templates, openTasks.length ? 0 : 1);
        const overdueTasks = openTasks.filter(t => t.is_overdue);

        return {
          lead_product_list_id: id,
          stage_landed_at: summary.stage_landed_at || summary.stage_started_at || summary.landed_at || payload?.context?.stage_started_at || null,
          previous_title: summary.previous_title || previous?.title || null,
          current_title: summary.current_title || current?.title || tmplCurrent?.title || null,
          next_title: summary.next_title || (openTasks[1]?.title || tmplNext?.title || null),
          description: summary.description || current?.description || tmplCurrent?.description || null,
          open_count: Number(summary.open_count ?? openTasks.length ?? 0),
          done_count: Number(summary.done_count ?? doneTasks.length ?? 0),
          overdue_count: Number(summary.overdue_count ?? overdueTasks.length ?? 0),
          is_overdue: Boolean(summary.is_overdue || overdueTasks.length),
          overdue_title: summary.overdue_title || overdueTasks[0]?.title || null,
          overdue_at: summary.overdue_at || overdueTasks[0]?.planned_end_at || null,
          sub_stage_label: summary.sub_stage_label || payload?.context?.sub_stage_label || null,
          source: summary.source || (current ? 'saved_task' : (tmplCurrent ? 'task_phase' : null)),
          has_personal_task: Boolean(summary.has_personal_task || current?.has_personal_task || current?.external_links?.personal_task_id),
          has_appointment: Boolean(summary.has_appointment || current?.has_appointment || current?.external_links?.appointment_id),
        };
      }

      function renderPreviewHtml(id, summary) {
        const title = summary.current_title || summary.next_title || 'Noch keine Aufgabe';
        const previous = summary.previous_title || '-';
        const landed = fmtDate(summary.stage_landed_at);
        const overdueLine = summary.is_overdue
          ? `<div class="kb-next-step-preview-line text-danger">
               <i class="feather icon-alert-triangle"></i>
               <span>Überfällig: <strong>${esc(summary.overdue_title || title)}</strong>${summary.overdue_at ? ` · ${esc(fmtDate(summary.overdue_at))}` : ''}</span>
             </div>`
          : '';

        const linkedLine = (summary.has_personal_task || summary.has_appointment)
          ? `<div class="kb-next-step-preview-line">
               <i class="feather icon-link"></i>
               <span>
                 ${summary.has_personal_task ? '<span class="kb-task-pill green"><i class="feather icon-check-square"></i> Persönliche Aufgabe</span>' : ''}
                 ${summary.has_appointment ? '<span class="kb-task-pill blue"><i class="feather icon-calendar"></i> Termin</span>' : ''}
               </span>
             </div>`
          : '';

        return `
          <div class="kb-next-step-preview-head">
            <span><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</span>
            <button type="button"
                    class="kb-next-step-preview-btn"
                    data-open-kanban-task-management
                    data-lead-product-list-id="${esc(id)}">
              Details
            </button>
          </div>
          <div class="kb-next-step-preview-line">
            <i class="feather icon-log-in"></i>
            <span>Seit: <strong>${esc(landed)}</strong></span>
          </div>
          ${summary.sub_stage_label ? `
            <div class="kb-next-step-preview-line">
              <i class="feather icon-git-branch"></i>
              <span>Unterphase: <strong>${esc(summary.sub_stage_label)}</strong></span>
            </div>` : ''}
          <div class="kb-next-step-preview-line">
            <i class="feather icon-check-circle"></i>
            <span>Vorher: <strong>${esc(previous)}</strong></span>
          </div>
          <div class="kb-next-step-preview-line">
            <i class="feather icon-list"></i>
            <span>${esc(title)}</span>
          </div>
          ${linkedLine}
          ${summary.next_title ? `
            <div class="kb-next-step-preview-line">
              <i class="feather icon-corner-down-right"></i>
              <span>Danach: <strong>${esc(summary.next_title)}</strong></span>
            </div>` : ''}
          ${overdueLine}
          <div class="kb-next-step-preview-line">
            <i class="feather icon-activity"></i>
            <span>Offen: <strong>${esc(summary.open_count)}</strong> · Erledigt: <strong>${esc(summary.done_count)}</strong></span>
          </div>`;
      }

      function applySummaryToCard(id, summary) {
        const card = findCard(id);
        if (!card) return;

        let preview = card.querySelector('.kb-next-step-preview');

        if (!preview) {
          preview = document.createElement('div');
          preview.className = 'kb-next-step-preview';
          const anchor = card.querySelector('.kb-card-meta') || card.querySelector('.card-header') || card.firstElementChild;
          if (anchor && anchor.parentNode) anchor.insertAdjacentElement('afterend', preview);
          else card.appendChild(preview);
        }

        preview.classList.remove('is-loading');
        preview.classList.toggle('is-overdue', !!summary.is_overdue);
        preview.innerHTML = renderPreviewHtml(id, summary);

        card.classList.toggle('kb-task-overdue-card', !!summary.is_overdue);

        const badge = card.querySelector('[data-kanban-task-count]');
        if (badge) {
          const count = Number(summary.open_count || 0);
          badge.textContent = count > 99 ? '99+' : String(count);
          badge.classList.toggle('d-none', count <= 0);
        }

        if (summary.is_overdue) {
          showOverdueToast(id, summary, card);
        }
      }

      function renderListHtml(id, summary) {
        const title = summary.current_title || summary.next_title || 'Noch keine Aufgabe';
        const overdue = summary.is_overdue ? ' is-overdue' : '';

        return `
          <div class="kb-list-next-step-box${overdue}">
            <div class="kb-list-next-head">
              <span class="kb-list-next-title">
                <i class="feather ${summary.is_overdue ? 'icon-alert-triangle' : 'icon-arrow-right-circle'}"></i>
                <strong>${esc(summary.is_overdue ? 'Überfällig' : 'Nächster Schritt')}</strong>
              </span>
              <button type="button"
                      class="kb-list-next-box-btn"
                      data-open-kanban-task-management
                      data-lead-product-list-id="${esc(id)}">
                Details
              </button>
            </div>
            <div class="kb-list-next-line">
              <i class="feather icon-list"></i>
              <span>${esc(title)}</span>
            </div>
            ${(summary.has_personal_task || summary.has_appointment) ? `
              <div class="kb-list-next-line">
                <i class="feather icon-link"></i>
                <span>
                  ${summary.has_personal_task ? '<span class="kb-task-pill green"><i class="feather icon-check-square"></i> Persönliche Aufgabe</span>' : ''}
                  ${summary.has_appointment ? '<span class="kb-task-pill blue"><i class="feather icon-calendar"></i> Termin</span>' : ''}
                </span>
              </div>` : ''}
            ${summary.sub_stage_label ? `
              <div class="kb-list-next-line">
                <i class="feather icon-git-branch"></i>
                <span>Unterphase: <strong>${esc(summary.sub_stage_label)}</strong></span>
              </div>` : ''}
            <div class="kb-list-next-line">
              <i class="feather icon-log-in"></i>
              <span>Seit: <strong>${esc(fmtDate(summary.stage_landed_at))}</strong></span>
            </div>
            <div class="kb-list-next-line">
              <i class="feather icon-check-circle"></i>
              <span>Vorher: <strong>${esc(summary.previous_title || '-')}</strong></span>
            </div>
            <div class="kb-list-next-line">
              <i class="feather icon-activity"></i>
              <span>Offen: <strong>${esc(summary.open_count)}</strong> · Erledigt: <strong>${esc(summary.done_count)}</strong>${summary.overdue_count ? ` · Überfällig: <strong>${esc(summary.overdue_count)}</strong>` : ''}</span>
            </div>
          </div>`;
      }

      function applySummaryToList(id, summary) {
        const row = findListRow(id);
        if (!row) return;

        row.classList.toggle('kb-task-overdue-row', !!summary.is_overdue);

        let box = row.querySelector('.kb-list-next-step-box');
        if (box) {
          box.outerHTML = renderListHtml(id, summary);
          return;
        }

        const oldStatus = row.querySelector('.kb-status');
        if (oldStatus) {
          oldStatus.outerHTML = renderListHtml(id, summary);
          return;
        }

        const cells = row.querySelectorAll('td');
        const target = cells.length >= 6 ? cells[5] : row.lastElementChild;
        if (target) {
          target.insertAdjacentHTML('afterbegin', renderListHtml(id, summary));
        }
      }

      function applySummary(id, raw) {
        const summary = normalizeSummary(id, raw);
        applySummaryToCard(id, summary);
        applySummaryToList(id, summary);
        if (window.feather) window.feather.replace();
      }

      function showOverdueToast(id, summary, card) {
        if (overdueToastShown.has(String(id))) return;
        overdueToastShown.add(String(id));

        const wrapId = 'kbKanbanTaskToastWrap';
        let wrap = document.getElementById(wrapId);
        if (!wrap) {
          wrap = document.createElement('div');
          wrap.id = wrapId;
          wrap.className = 'kb-kanban-task-toast-wrap';
          document.body.appendChild(wrap);
        }

        const customer = card?.querySelector('.card-name')?.textContent?.trim() || `Prozess #${id}`;
        const toast = document.createElement('div');
        toast.className = 'kb-kanban-task-toast';
        toast.innerHTML = `
          <div class="kb-kanban-task-toast-head">
            <span><i class="feather icon-alert-triangle"></i> Überfällige Aufgabe</span>
            <span>#${esc(id)}</span>
          </div>
          <div class="kb-kanban-task-toast-body">
            <strong>${esc(customer)}</strong><br>
            ${esc(summary.overdue_title || summary.current_title || 'Aufgabe')}<br>
            ${summary.overdue_at ? `Fällig: ${esc(fmtDate(summary.overdue_at))}` : ''}
          </div>
        `;
        toast.addEventListener('click', () => {
          const btn = card?.querySelector('[data-open-kanban-task-management]');
          if (btn) btn.click();
        });

        wrap.appendChild(toast);
        if (window.feather) window.feather.replace();

        setTimeout(() => toast.remove(), 9000);
      }

      async function fetchSummaries(ids) {
        const cleanIds = ids.filter(id => id && !loaded.has(String(id)));
        if (!cleanIds.length) return;

        cleanIds.forEach(id => loaded.add(String(id)));

        cleanIds.forEach(id => {
          const card = findCard(id);
          const preview = card?.querySelector('.kb-next-step-preview');
          if (preview) preview.classList.add('is-loading');
        });

        try {
          const res = await fetch(SUMMARY_URL, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ lead_product_list_ids: cleanIds }),
          });

          const json = await res.json().catch(() => ({}));

          if (!res.ok || json.success === false || !json.summaries) {
            throw new Error(json.message || 'Bulk summaries unavailable');
          }

          Object.entries(json.summaries).forEach(([id, summary]) => applySummary(id, summary));
        } catch (bulkError) {
          // Fallback: old context endpoint one-by-one, so it still works before routes are cached.
          await Promise.all(cleanIds.map(async (id) => {
            try {
              const res = await fetch(CONTEXT_URL(id), {
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrf,
                },
              });
              const json = await res.json().catch(() => ({}));
              if (res.ok && json.success !== false) {
                applySummary(id, {
                  context: json.context,
                  tasks: json.tasks || [],
                  templates: json.templates || [],
                });
              }
            } catch (e) {
              const card = findCard(id);
              const preview = card?.querySelector('.kb-next-step-preview');
              if (preview) preview.classList.remove('is-loading');
              console.warn('Kanban next-step preload failed for lead_product_list_id', id, e);
            }
          }));
        }
      }

      function normalizeColumnHeaders(root = document) {
        root.querySelectorAll('.column').forEach((column) => {
          const h3 = column.querySelector('h3');
          if (!h3) return;

          const stageKey =
            h3.dataset.workflowStageKey ||
            column.dataset.stage ||
            column.id ||
            h3.textContent.trim().toLowerCase().replace(/\s+/g, '_');

          column.dataset.analyticsKey = stageKey;

          // Default is hidden. Only show if user explicitly turned it on.
          const stored = localStorage.getItem('kb_analytics_hidden_' + stageKey);
          const hidden = stored === null ? true : stored !== '0';
          column.classList.toggle('kb-analytics-hidden', hidden);

          const underBtn = h3.querySelector('.kb-understage-btn');
          if (underBtn) {
            const count = underBtn.querySelector('b')?.textContent?.trim() || '';
            const icon = underBtn.querySelector('i')?.outerHTML || '<i class="feather icon-git-branch"></i>';
            underBtn.innerHTML = `${icon}<span>Unterphasen</span>${count ? `<b>${esc(count)}</b>` : ''}`;
            underBtn.title = 'Unterphasen anzeigen';
          }

          const actions = h3.querySelector('.kb-column-actions') || h3;
          if (!actions.querySelector('[data-kb-toggle-analytics]')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kb-toggle-analytics';
            btn.dataset.kbToggleAnalytics = stageKey;
            btn.title = 'Analyse-Badges ein-/ausblenden';
            btn.innerHTML = '<i class="feather icon-bar-chart-2"></i>';
            actions.appendChild(btn);
          }

          const toggle = actions.querySelector('[data-kb-toggle-analytics]');
          if (toggle) toggle.classList.toggle('is-active', !hidden);
        });

        if (window.feather) window.feather.replace();
      }

      document.addEventListener('click', function (event) {
        const btn = event.target.closest('[data-kb-toggle-analytics]');
        if (!btn) return;

        event.preventDefault();
        event.stopPropagation();

        const column = btn.closest('.column');
        if (!column) return;

        const key = btn.dataset.kbToggleAnalytics || column.dataset.analyticsKey || column.id || 'stage';
        const nextHidden = !column.classList.contains('kb-analytics-hidden');

        column.classList.toggle('kb-analytics-hidden', nextHidden);
        btn.classList.toggle('is-active', !nextHidden);
        localStorage.setItem('kb_analytics_hidden_' + key, nextHidden ? '1' : '0');
      }, true);

      function boot(root = document) {
        normalizeColumnHeaders(root);
        fetchSummaries(collectVisibleLeadProductIds(root));
      }

      document.addEventListener('DOMContentLoaded', function () {
        boot();
        setTimeout(() => boot(), 250);
        setTimeout(() => boot(), 1000);
        setTimeout(() => boot(), 2500);
      });

      const observer = new MutationObserver((mutations) => {
        let shouldBoot = false;
        for (const mutation of mutations) {
          for (const node of mutation.addedNodes) {
            if (node.nodeType === 1 && (node.matches?.('.card, tr.list-row-item, .column') || node.querySelector?.('.card, tr.list-row-item, .column'))) {
              shouldBoot = true;
              break;
            }
          }
          if (shouldBoot) break;
        }
        if (shouldBoot) setTimeout(() => boot(), 80);
      });

      observer.observe(document.documentElement, { childList: true, subtree: true });

      window.refreshKanbanNextStepPreviews = function () {
        loaded.clear();
        boot();
      };
    })();
  

/* ===================== Extracted inline script block #32 ===================== */
  /* ===== Boss final: normalize column header order + restore Unterphasen drag/drop ===== */
  (function () {
    'use strict';

    const MIME = window.KB_DND_MIME || 'application/x-leadui-cards';

    function cssEscape(value) {
      return window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&');
    }

    function normalizeColumnHeader(column) {
      if (!column) return;
      const h3 = column.querySelector('h3[data-workflow-stage-key]') || column.querySelector('h3');
      if (!h3) return;

      const stageKey = h3.dataset.workflowStageKey || column.id || column.dataset.stage || 'stage';
      h3.dataset.workflowStageKey = stageKey;
      column.dataset.analyticsKey = stageKey;

      let titleWrap = h3.querySelector('.kb-column-head-left');
      let title = h3.querySelector('.kb-column-title');

      if (!title) {
        title = document.createElement('span');
        title.className = 'kb-column-title';
        title.textContent = h3.textContent.trim() || stageKey;
      }

      if (!titleWrap) {
        titleWrap = document.createElement('span');
        titleWrap.className = 'kb-column-head-left';
      }

      if (!titleWrap.contains(title)) titleWrap.appendChild(title);

      let actions = h3.querySelector('.kb-column-actions');
      if (!actions) {
        actions = document.createElement('span');
        actions.className = 'kb-column-actions';
      }

      const underBtn = h3.querySelector('.kb-understage-btn');
      if (underBtn) {
        const count = (underBtn.querySelector('b')?.textContent || '').trim();
        const icon = underBtn.querySelector('i')?.outerHTML || '<i class="feather icon-git-branch"></i>';
        underBtn.innerHTML = icon + '<span>Unterphasen</span>' + (count ? '<b>' + count + '</b>' : '<b>0</b>');
        underBtn.title = 'Unterphasen anzeigen';
        actions.appendChild(underBtn);
      }

      let toggle = h3.querySelector('[data-kb-toggle-analytics]');
      if (!toggle) {
        toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'kb-toggle-analytics';
        toggle.dataset.kbToggleAnalytics = stageKey;
        toggle.title = 'Analyse-Badges ein-/ausblenden';
        toggle.innerHTML = '<i class="feather icon-bar-chart-2"></i>';
      }
      actions.appendChild(toggle);

      const configWrap = h3.querySelector('.kb-column-substage-wrap');
      if (configWrap) actions.appendChild(configWrap);

      const counts = h3.querySelector('.kb-header-counts');
      if (counts) actions.appendChild(counts);

      h3.innerHTML = '';
      h3.appendChild(titleWrap);
      h3.appendChild(actions);

      // Default OFF. Only show analytics if user explicitly turned them on.
      const stored = localStorage.getItem('kb_analytics_hidden_' + stageKey);
      const hidden = stored === null ? true : stored !== '0';
      column.classList.toggle('kb-analytics-hidden', hidden);
      toggle.classList.toggle('is-active', !hidden);
    }

    function normalizeAllColumnHeaders(root = document) {
      root.querySelectorAll('.column').forEach(normalizeColumnHeader);
      if (window.feather) window.feather.replace();
    }

    document.addEventListener('click', function (event) {
      const btn = event.target.closest('[data-kb-toggle-analytics]');
      if (!btn) return;

      event.preventDefault();
      event.stopPropagation();

      const column = btn.closest('.column');
      if (!column) return;

      const key = btn.dataset.kbToggleAnalytics || column.dataset.analyticsKey || column.id || 'stage';
      const nextHidden = !column.classList.contains('kb-analytics-hidden');

      column.classList.toggle('kb-analytics-hidden', nextHidden);
      btn.classList.toggle('is-active', !nextHidden);
      localStorage.setItem('kb_analytics_hidden_' + key, nextHidden ? '1' : '0');
    }, true);

    // Dragging inside Unterphasen sidebar was broken because the original dragstart only listened to #kanban .card.
    document.addEventListener('dragstart', function (event) {
      const card = event.target.closest('#kbUnderstageBoard .card, .kb-understage-sidebar .card, [data-understage-dropzone] .card');
      if (!card || !event.dataTransfer) return;

      const id = card.id || ('card-' + (card.dataset.leadProductId || card.dataset.leadProductListId || ''));
      if (!id) return;

      event.dataTransfer.setData(MIME, JSON.stringify([id]));
      event.dataTransfer.setData('text/plain', JSON.stringify([id]));
      event.dataTransfer.effectAllowed = 'move';
      card.classList.add('kb-understage-dragging');
    }, true);

    document.addEventListener('dragend', function (event) {
      const card = event.target.closest('#kbUnderstageBoard .card, .kb-understage-sidebar .card, [data-understage-dropzone] .card');
      if (card) card.classList.remove('kb-understage-dragging');
    }, true);

    // Make sure every card inside Unterphasen remains draggable after rendering.
    function enableUnderstageCards(root = document) {
      root.querySelectorAll('#kbUnderstageBoard .card, .kb-understage-sidebar .card, [data-understage-dropzone] .card').forEach((card) => {
        card.draggable = true;
        if (!card.dataset.leadProductListId && card.dataset.leadProductId) {
          card.dataset.leadProductListId = card.dataset.leadProductId;
        }
      });
    }

    function boot(root = document) {
      normalizeAllColumnHeaders(root);
      enableUnderstageCards(root);
    }

    document.addEventListener('DOMContentLoaded', function () {
      boot();
      setTimeout(boot, 250);
      setTimeout(boot, 1000);
      setTimeout(boot, 2500);
    });

    const observer = new MutationObserver(function (mutations) {
      let shouldBoot = false;
      for (const mutation of mutations) {
        for (const node of mutation.addedNodes) {
          if (node.nodeType === 1 && (node.matches?.('.column, #kbUnderstageBoard, .card') || node.querySelector?.('.column, #kbUnderstageBoard, .card'))) {
            shouldBoot = true;
            break;
          }
        }
        if (shouldBoot) break;
      }
      if (shouldBoot) setTimeout(() => boot(), 80);
    });

    observer.observe(document.documentElement, { childList: true, subtree: true });

    window.normalizeKanbanColumnHeaders = normalizeAllColumnHeaders;
    window.enableUnderstageCardDragDrop = enableUnderstageCards;
  })();

/* ===================== Extracted inline script block #33 ===================== */
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#btnOpenKanbanStageAdminTop, #btnOpenKanbanStageAdminMain, .kb-stage-admin-open').forEach(function (el) {
      el.remove();
    });
  });



  /*
   * Old Kanban appointment drawer is disabled.
   * Termin Bericht inside the customer panel is the only appointment/report UI.
   */
  window.KANBAN_DISABLE_OLD_TERMIN = true;
  document.addEventListener("click", function (event) {
    const oldTerminBtn = event.target.closest('[data-menu="termin"]');
    if (!oldTerminBtn) return;
    event.preventDefault();
    event.stopImmediatePropagation();
    const notesBtn = oldTerminBtn.closest(".card, tr")?.querySelector("[data-open-notes]");
    if (notesBtn) notesBtn.click();
  }, true);

  /* =========================================================
   Kanban Customer Panel Redesign
   Add to: public/js/kanban.js
   Requires Blade config: window.KANBAN_CUSTOMER_PANEL_ROUTES
   ========================================================= */

(function () {
  "use strict";

  const qs = (sel, root = document) => root.querySelector(sel);
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  const Routes = () => window.KANBAN_CUSTOMER_PANEL_ROUTES || {};
  const csrf = () => qs('meta[name="csrf-token"]')?.content || "";

  let currentContext = {
    customer_id: null,
    alternative_id: null,
    product_id: null,
    lead_product_list_id: null,
    customer_name: "",
    product_name: "",
  };

  function escapeHTML(value) {
    return String(value ?? "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    }[m]));
  }

  function stripHtml(html) {
    const div = document.createElement("div");
    div.innerHTML = String(html || "");
    return (div.textContent || div.innerText || "").trim();
  }

  function buildQuery(params) {
    const query = new URLSearchParams();
    Object.entries(params || {}).forEach(([key, value]) => {
      if (value !== null && value !== undefined && String(value) !== "") {
        query.set(key, value);
      }
    });
    return query.toString();
  }

  function panelParams(extra = {}) {
    return {
      customer_id: currentContext.customer_id,
      alternative_id: currentContext.alternative_id,
      product_id: currentContext.product_id,
      lead_product_list_id: currentContext.lead_product_list_id,
      ...extra,
    };
  }

  function appointmentPanelParams(extra = {}) {
    /*
     * Termin Bericht is customer-wide inside Kanban.
     * This avoids hiding old appointments that were saved only with customer_id
     * or with mixed products JSON formats.
     */
    return {
      customer_id: currentContext.customer_id,
      ...extra,
    };
  }

  async function getJSON(url, params = {}) {
    const fullUrl = `${url}${url.includes("?") ? "&" : "?"}${buildQuery(params)}`;
    const res = await fetch(fullUrl, {
      method: "GET",
      credentials: "same-origin",
      headers: {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.status === "error" || data.success === false) {
      throw new Error(data.message || `HTTP ${res.status}`);
    }
    return data;
  }

  async function postJSON(url, payload = {}) {
    const res = await fetch(url, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": csrf(),
      },
      body: JSON.stringify(payload),
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.status === "error" || data.success === false) {
      const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
      throw new Error(firstError || data.message || `HTTP ${res.status}`);
    }
    return data;
  }

  function refreshFeather() {
    requestAnimationFrame(() => {
      if (window.feather && typeof window.feather.replace === "function") {
        window.feather.replace();
      }
    });
  }

  function setLoading(target, text = "Wird geladen…") {
    const el = typeof target === "string" ? qs(target) : target;
    if (!el) return;
    el.innerHTML = `<div class="kb-loading-state">${escapeHTML(text)}</div>`;
  }

  function showToast(title, message, type = "success") {
    if (window.Swal) {
      window.Swal.fire({
        icon: type === "error" ? "error" : "success",
        title,
        text: message,
        timer: type === "error" ? undefined : 1800,
        showConfirmButton: type === "error",
      });
      return;
    }
    alert(`${title}\n${message}`);
  }

  function showPanel(tabName) {
    const drawer = qs("#notesDrawer");
    if (!drawer) return;

    qsa("[data-notes-tab]", drawer).forEach((btn) => {
      const active = btn.dataset.notesTab === tabName;
      btn.classList.toggle("notes-tab--active", active);
      btn.setAttribute("aria-selected", active ? "true" : "false");
    });

    qsa("[data-notes-panel]", drawer).forEach((panel) => {
      panel.classList.toggle("d-none", panel.dataset.notesPanel !== tabName);
    });

    const foot = qs(".notes-foot", drawer);
    if (foot) foot.style.display = tabName === "notes" ? "" : "none";

    if (tabName === "customerReport") loadCustomerReports();
    if (tabName === "report") loadAppointmentsAndReports();

    refreshFeather();
  }

  function updateBadges(counts = {}) {
    const badgeNotes = qs("#tabBadgeNotes");
    const badgeCustomer = qs("#tabBadgeCustomerReport");
    const badgeTermin = qs("#tabBadgeTerminReport");
    const badgeTotal = qs("#notesCountBadge");

    if (badgeNotes && counts.notes !== undefined) badgeNotes.textContent = counts.notes;
    if (badgeCustomer && counts.customer_reports !== undefined) badgeCustomer.textContent = counts.customer_reports;
    /*
     * Do not update #tabBadgeTerminReport or #kbTerminOpenInfo from counts here.
     * Termin Bericht loads customer-wide appointment data and owns those numbers.
     * Counts may still be context-based for notes/customer reports.
     */
    if (badgeTotal && counts.total !== undefined) {
      badgeTotal.textContent = counts.total;
      badgeTotal.dataset.count = counts.total;
    }
  }

  async function loadCounts() {
    const routes = Routes();
    if (!routes.counts || !currentContext.customer_id) return;

    try {
      const data = await getJSON(routes.counts, panelParams());
      updateBadges(data.counts || {});
    } catch (error) {
      console.warn("Kanban customer-panel counts failed:", error);
    }
  }

  function ensureCustomerPanelShell() {
    const customerPanel = qs("#customerReportList");
    const terminPanel = qs("#notesReport");

    if (customerPanel && !customerPanel.dataset.enhanced) {
      customerPanel.dataset.enhanced = "1";
      customerPanel.innerHTML = `
        <div class="kb-panel-header">
          <div>
            <div class="kb-panel-header-title">
              <span class="kb-panel-icon"><i class="feather icon-file-text"></i></span>
              <span>Kunden Bericht</span>
            </div>
            <div class="kb-panel-header-sub">Aktuelle Berichte zu diesem Kunden, Objekt und Produkt.</div>
          </div>
          <button type="button" class="btn btn-primary kb-btn-brand kb-new-customer-report">
            <i class="feather icon-plus"></i> Bericht hinzufügen
          </button>
        </div>
        <div id="kbCustomerReportContent"></div>
      `;
    }

    if (terminPanel && !terminPanel.dataset.enhanced) {
      terminPanel.dataset.enhanced = "1";
      terminPanel.innerHTML = `
        <div class="kb-panel-header">
          <div>
            <div class="kb-panel-header-title">
              <span class="kb-panel-icon"><i class="feather icon-calendar"></i></span>
              <span>Termin Bericht</span>
            </div>
            <div class="kb-panel-header-sub">
              Neueste Termine oben. Jeder Termin zeigt, ob ein Bericht vorhanden ist.
              <span id="kbTerminOpenInfo" class="ml-50"></span>
            </div>
          </div>
        </div>
        <div id="kbTerminReportContent"></div>
      `;
    }

    refreshFeather();
  }

  async function loadCustomerReports() {
    const routes = Routes();
    ensureCustomerPanelShell();

    const content = qs("#kbCustomerReportContent") || qs("#customerReportList");
    if (!routes.customerReportsIndex || !content || !currentContext.customer_id) return;

    setLoading(content, "Kunden Bericht wird geladen…");

    try {
      const data = await getJSON(routes.customerReportsIndex, panelParams());
      content.innerHTML = data.html || `<div class="kb-empty-state">Noch kein Kunden Bericht vorhanden.</div>`;
      const badge = qs("#tabBadgeCustomerReport");
      if (badge && data.count !== undefined) badge.textContent = data.count;
      bindPanelSearch(content);
      refreshFeather();
    } catch (error) {
      content.innerHTML = `<div class="kb-empty-state text-danger">${escapeHTML(error.message)}</div>`;
    }
  }

  async function loadAppointmentsAndReports() {
    const routes = Routes();
    ensureCustomerPanelShell();

    const content = qs("#kbTerminReportContent") || qs("#notesReport");
    if (!routes.appointmentsIndex || !content || !currentContext.customer_id) return;

    setLoading(content, "Termin Bericht wird geladen…");

    try {
      /*
       * Load all appointments for this customer.
       * Do not send alternative_id/product_id/lead_product_list_id for this tab,
       * otherwise appointments saved only with customer_id disappear.
       */
      const data = await getJSON(routes.appointmentsIndex, appointmentPanelParams());
      content.innerHTML = data.html || `<div class="kb-empty-state">Keine Termine gefunden.</div>`;

      const appointmentCount = Number(
        data.appointments_count ??
        data.count ??
        qsa(".kb-appointment-group, [data-appointment-id]", content).length ??
        0
      );

      const reportCount = Number(
        data.reports_count ??
        qsa(".ap-report-card, .kb-report-card.ap-report-card, [data-report-id]", content).length ??
        0
      );

      const doneCount = qsa(".kb-appointment-group.is-done", content).length;
      const openCount = Math.max(0, appointmentCount - doneCount);

      const badge = qs("#tabBadgeTerminReport");
      if (badge) {
        badge.textContent = appointmentCount;
        badge.dataset.count = String(appointmentCount);
        badge.classList.remove("d-none");
      }

      const openInfo = qs("#kbTerminOpenInfo");
      if (openInfo) {
        openInfo.textContent = appointmentCount > 0
          ? `${appointmentCount} Termin(e) · ${reportCount} Bericht(e) · ${openCount} offen`
          : "Keine Termine gefunden";
        openInfo.classList.toggle("text-danger", openCount > 0);
        openInfo.classList.toggle("text-success", appointmentCount > 0 && openCount === 0);
      }

      bindPanelSearch(content);
      refreshFeather();

      /*
       * Keep notes/customer-report badges fresh, but Termin badge is intentionally
       * controlled above by appointmentCount.
       */
      loadCounts();
    } catch (error) {
      content.innerHTML = `<div class="kb-empty-state text-danger">${escapeHTML(error.message)}</div>`;
    }
  }

  function bindPanelSearch(root) {
    const input = qs("[data-kb-panel-search]", root);
    if (!input || input.dataset.bound === "1") return;
    input.dataset.bound = "1";

    input.addEventListener("input", () => {
      const term = input.value.trim().toLowerCase();
      qsa("[data-search-text]", root).forEach((item) => {
        const text = String(item.dataset.searchText || item.innerText || "").toLowerCase();
        item.style.display = text.includes(term) ? "" : "none";
      });
    });
  }

  function openReportModal(type, data = {}) {
    const modal = qs("#kbReportModalBackdrop");
    const form = qs("#kbReportForm");
    if (!modal || !form) return;

    form.reset();
    form.dataset.type = type;
    form.dataset.appointmentId = data.appointmentId || "";

    qs("#kbReportModalTitleText").textContent = type === "appointment"
      ? "Termin Bericht hinzufügen"
      : "Kunden Bericht hinzufügen";

    qs("#kbReportModalIcon").innerHTML = type === "appointment"
      ? '<i class="feather icon-calendar"></i>'
      : '<i class="feather icon-file-text"></i>';

    const appointmentInfo = qs("#kbReportAppointmentInfo");
    if (appointmentInfo) {
      appointmentInfo.hidden = type !== "appointment";
      appointmentInfo.innerHTML = type === "appointment"
        ? `<strong>${escapeHTML(data.title || "Termin")}</strong><br><small>${escapeHTML(data.date || "")}</small>`
        : "";
    }

    const typeField = qs('[name="type"]', form);
    if (typeField) typeField.value = type === "appointment" ? "Termin Bericht" : "Kunden Bericht";

    modal.classList.add("open");
    refreshFeather();
    setTimeout(() => qs('[name="title"]', form)?.focus(), 80);
  }

  function closeReportModal() {
    qs("#kbReportModalBackdrop")?.classList.remove("open");
  }

  async function submitReportForm(event) {
    event.preventDefault();

    const routes = Routes();
    const form = event.currentTarget;
    const type = form.dataset.type || "customer";
    const appointmentId = form.dataset.appointmentId || "";

    const title = form.elements.title?.value?.trim() || "";
    const stage = form.elements.stage?.value || "";
    const report = form.elements.report?.value?.trim() || "";
    const reportDate = form.elements.report_date?.value || "";
    const nextStep = form.elements.next_step?.value?.trim() || "";
    const dueDate = form.elements.due_date?.value || "";

    if (!report) {
      showToast("Pflichtfeld", "Bitte Bericht schreiben.", "error");
      return;
    }

    const submitBtn = qs("[data-kb-report-submit]", form.closest(".kb-report-modal"));
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="feather icon-loader"></i> Speichern…';
    }

    try {
      if (type === "appointment") {
        if (!routes.appointmentReportsStore || !appointmentId) throw new Error("Termin Report Route fehlt.");
        const url = routes.appointmentReportsStore.replace("__APPOINTMENT__", encodeURIComponent(appointmentId));

        await postJSON(url, {
          customer_id: currentContext.customer_id,
          alternative_id: currentContext.alternative_id,
          product_id: currentContext.product_id,
          lead_product_list_id: currentContext.lead_product_list_id,
          type: "Termin Bericht",
          title,
          report,
          plain_text: stripHtml(report),
          report_date: reportDate,
          next_step: nextStep,
          due_date: dueDate,
        });

        closeReportModal();
        await loadAppointmentsAndReports();
        await loadCounts();
        showToast("Gespeichert", "Termin Bericht wurde gespeichert.");
      } else {
        if (!routes.customerReportsStore) throw new Error("Kunden Bericht Route fehlt.");

        await postJSON(routes.customerReportsStore, {
          customer_id: currentContext.customer_id,
          alternative_id: currentContext.alternative_id,
          product_id: currentContext.product_id,
          stage: stage || "Kunden Bericht",
          title,
          report_date: reportDate,
          report,
          plain_text: stripHtml(report),
        });

        closeReportModal();
        await loadCustomerReports();
        await loadCounts();
        showToast("Gespeichert", "Kunden Bericht wurde gespeichert.");
      }
    } catch (error) {
      showToast("Fehler", error.message, "error");
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="feather icon-save"></i> Speichern';
        refreshFeather();
      }
    }
  }

  function readContextFromDrawer() {
    const drawer = qs("#notesDrawer");
    if (!drawer) return { ...currentContext };

    const hidden = {
      customer_id: qs("#notesCustomerId")?.value || "",
      alternative_id: qs("#notesAlternativeId")?.value || "",
      product_id: qs("#notesProductId")?.value || "",
      lead_product_list_id: qs("#notesLeadProductListId")?.value || "",
    };

    const reportForm = qs(".cr-create-form", drawer);

    /*
     * Important:
     * Do NOT fallback to document.querySelector(".card[data-customer-id]").
     * That was stealing the first visible card and caused another customer's
     * Termin Bericht / numbers to load.
     */
    return {
      customer_id:
        drawer.dataset.customerId ||
        hidden.customer_id ||
        reportForm?.elements?.customer_id?.value ||
        currentContext.customer_id ||
        "",
      alternative_id:
        drawer.dataset.alternativeId ||
        hidden.alternative_id ||
        reportForm?.elements?.alternative_id?.value ||
        currentContext.alternative_id ||
        "",
      product_id:
        drawer.dataset.productId ||
        hidden.product_id ||
        reportForm?.elements?.product_id?.value ||
        currentContext.product_id ||
        "",
      lead_product_list_id:
        drawer.dataset.leadProductListId ||
        hidden.lead_product_list_id ||
        currentContext.lead_product_list_id ||
        "",
      customer_name:
        drawer.dataset.customerName ||
        currentContext.customer_name ||
        "",
      product_name:
        drawer.dataset.productName ||
        currentContext.product_name ||
        "",
    };
  }

  function writeContextToDom() {
    const drawer = qs("#notesDrawer");
    if (!drawer) return;

    const map = {
      customer_id: "customerId",
      alternative_id: "alternativeId",
      product_id: "productId",
      lead_product_list_id: "leadProductListId",
      customer_name: "customerName",
      product_name: "productName",
    };

    Object.entries(map).forEach(([contextKey, datasetKey]) => {
      const value = currentContext[contextKey];
      if (value !== null && value !== undefined && String(value) !== "") {
        drawer.dataset[datasetKey] = String(value);
        drawer.setAttribute("data-" + contextKey.replaceAll("_", "-"), String(value));
      }
    });

    const hiddenMap = {
      notesCustomerId: "customer_id",
      notesAlternativeId: "alternative_id",
      notesProductId: "product_id",
      notesLeadProductListId: "lead_product_list_id",
    };

    Object.entries(hiddenMap).forEach(([elementId, contextKey]) => {
      const input = document.getElementById(elementId);
      if (input) input.value = currentContext[contextKey] || "";
    });

    const titleEl = document.getElementById("notesTitle");
    const customer = currentContext.customer_name || drawer.dataset.customerName || "Kunden Kommunikation";
    const product = currentContext.product_name || drawer.dataset.productName || "";
    if (titleEl) {
      titleEl.textContent = product ? `${customer} · ${product}` : customer;
    }

  }

  function setContext(payload = {}) {
    currentContext = {
      ...currentContext,
      ...readContextFromDrawer(),
      ...payload,
    };

    writeContextToDom();
  }

  function bindCustomerPanelEvents() {
    document.addEventListener("click", (event) => {
      const tab = event.target.closest("[data-notes-tab]");
      if (tab) {
        event.preventDefault();
        setContext();
        showPanel(tab.dataset.notesTab || "notes");
        return;
      }

      if (event.target.closest(".kb-new-customer-report") || event.target.closest(".cr-toggle-new")) {
        event.preventDefault();
        setContext();
        if (!currentContext.customer_id) {
          showToast("Fehler", "Customer-ID fehlt. Öffne den Bericht über eine Kanban-Karte oder Liste.", "error");
          return;
        }
        openReportModal("customer");
        return;
      }

      const appointmentBtn = event.target.closest(".kb-open-appointment-report-form, .ap-open-report-form");
      if (appointmentBtn) {
        event.preventDefault();
        setContext();
        const group = appointmentBtn.closest("[data-appointment-id]");
        openReportModal("appointment", {
          appointmentId: appointmentBtn.dataset.appointmentId || group?.dataset.appointmentId,
          title: appointmentBtn.dataset.appointmentTitle || group?.dataset.appointmentTitle || group?.querySelector("strong")?.textContent,
          date: appointmentBtn.dataset.appointmentDate || group?.dataset.appointmentDate || "",
        });
        return;
      }

      if (event.target.closest("[data-kb-report-close]")) {
        event.preventDefault();
        closeReportModal();
        return;
      }

      const commentToggle = event.target.closest("[data-report-toggle-comments], .cr-toggle-comments, .ap-report-comments-toggle");
      if (commentToggle) {
        const card = commentToggle.closest(".cr-card, .ap-report-card, .kb-ap-report-card");
        const comments = card?.querySelector(".cr-comments, .ap-report-comments");
        if (comments) comments.hidden = !comments.hidden;
      }
    });

    qs("#kbReportForm")?.addEventListener("submit", submitReportForm);

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && qs("#kbReportModalBackdrop.open")) {
        closeReportModal();
      }
    });
  }

  function init() {
    ensureCustomerPanelShell();
    bindCustomerPanelEvents();

    const active = qs(".notes-tab--active[data-notes-tab]")?.dataset.notesTab || "notes";
    showPanel(active);

    window.KanbanCustomerPanel = {
      setContext,
      showPanel,
      loadCounts,
      loadCustomerReports,
      loadAppointmentsAndReports,
      openReportModal,
    };
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();



/* ==========================================================================
 * Aufgabe Sidebar v3 hotfix
 * Fixes:
 * - postJSON is not defined outside the LeadUI closure
 * - keep the Lucide two-tab Blade layout instead of rewriting it to old detail UI
 * - render comments directly under each task and make them collapsible
 * ========================================================================== */
(function () {
  "use strict";

  function ready(fn) {
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", fn);
    else fn();
  }

  function qs(selector, ctx) {
    return (ctx || document).querySelector(selector);
  }

  function qsa(selector, ctx) {
    return Array.from((ctx || document).querySelectorAll(selector));
  }

  function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || "";
  }

  function esc(value) {
    return String(value ?? "").replace(/[&<>"']/g, function (m) {
      return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" })[m];
    });
  }

  function stripTags(value) {
    const div = document.createElement("div");
    div.innerHTML = String(value || "");
    return div.textContent || div.innerText || "";
  }

  function refreshIcons() {
    requestAnimationFrame(function () {
      if (window.lucide && typeof window.lucide.createIcons === "function") {
        window.lucide.createIcons();
      }
      if (window.feather && typeof window.feather.replace === "function") {
        window.feather.replace();
      }
    });
  }

  async function ptxFetchJSON(url, options) {
    const res = await fetch(url, {
      credentials: "same-origin",
      headers: {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        ...(options?.headers || {})
      },
      ...options
    });

    const text = await res.text();
    let data = {};

    try {
      data = text ? JSON.parse(text) : {};
    } catch (error) {
      data = { message: text || ("HTTP " + res.status) };
    }

    if (!res.ok || data.status === "error" || data.success === false) {
      const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
      throw new Error(firstError || data.message || ("HTTP " + res.status));
    }

    return data;
  }

  async function ptxPostJSON(url, payload) {
    return ptxFetchJSON(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf()
      },
      body: JSON.stringify(payload || {})
    });
  }

  function route(template, replacements) {
    let url = String(template || "");
    Object.entries(replacements || {}).forEach(function ([key, value]) {
      url = url.replaceAll("__" + key + "__", encodeURIComponent(value ?? ""));
    });
    return url;
  }

  function dateText(value) {
    if (!value) return "";
    try {
      return new Date(value).toLocaleDateString("de-DE");
    } catch (error) {
      return String(value || "");
    }
  }

  function dateTimeText(value) {
    if (!value) return "";
    try {
      return new Date(value).toLocaleString("de-DE");
    } catch (error) {
      return String(value || "");
    }
  }

  function statusKey(task) {
    const raw = String(task?.status || task?.task_status || "open").toLowerCase();
    if (["on_progress", "in_progress", "progress", "doing"].includes(raw)) return "in_progress";
    if (["completed", "done", "complete"].includes(raw)) return "done";
    if (["pause", "paused"].includes(raw)) return "paused";
    if (["cancel", "canceled", "cancelled", "rejected", "junk"].includes(raw)) return "canceled";
    return "open";
  }

  function statusLabel(status) {
    return {
      open: "Offen",
      in_progress: "In Arbeit",
      paused: "Pausiert",
      done: "Erledigt",
      canceled: "Storniert"
    }[statusKey({ status })] || "Offen";
  }

  function priorityLabel(priority) {
    const key = String(priority || "normal").toLowerCase();
    return {
      low: "Niedrig",
      normal: "Normal",
      high: "Hoch",
      urgent: "Dringend"
    }[key] || (priority || "Normal");
  }

  function avatar(employee) {
    const name = String(employee?.name || employee?.lastname || "MA").trim();
    const img = employee?.image || "";
    const initials = name
      .split(/\s+/)
      .filter(Boolean)
      .slice(0, 2)
      .map(function (part) { return part.charAt(0).toUpperCase(); })
      .join("") || "MA";

    if (img) {
      return '<img class="ptx-avatar" src="' + esc(img) + '" alt="' + esc(name) + '">';
    }

    return '<span class="ptx-avatar ptx-avatar-fallback">' + esc(initials) + '</span>';
  }

  function employeeAvatars(employees) {
    const list = Array.isArray(employees) ? employees : [];
    if (!list.length) return '<span class="ptx-muted">Keine Mitarbeiter</span>';

    const visible = list.slice(0, 5).map(avatar).join("");
    const rest = list.length > 5 ? '<span class="ptx-avatar ptx-avatar-more">+' + (list.length - 5) + '</span>' : "";
    return visible + rest;
  }

  function keyHTML(key) {
    const done = !!(key?.is_completed || key?.done_status === "done");
    return [
      '<div class="ptx-key-row ' + (done ? "is-done" : "") + '" data-pt-key-row="' + esc(key?.id) + '">',
        '<button type="button" class="ptx-key-check" data-pt-key-toggle="' + esc(key?.id) + '">',
          '<i data-lucide="' + (done ? "check-circle-2" : "circle") + '"></i>',
        '</button>',
        '<div class="ptx-key-copy">',
          '<strong>' + esc(key?.task || key?.title || "Schritt") + '</strong>',
          key?.description ? '<span>' + esc(stripTags(key.description)) + '</span>' : '',
          '<div class="ptx-key-meta">',
            key?.duration ? '<small><i data-lucide="clock-3"></i>' + esc(key.duration) + ' Min.</small>' : '',
            key?.total_time ? '<small><i data-lucide="timer"></i>' + esc(key.total_time) + ' Std.</small>' : '',
            key?.submit_time ? '<small><i data-lucide="upload-cloud"></i>' + esc(key.submit_time) + '</small>' : '',
          '</div>',
        '</div>',
      '</div>'
    ].join("");
  }

  function commentHTML(comment, isReply) {
    const author = comment?.author || comment?.employee || {};
    const replies = Array.isArray(comment?.replies) ? comment.replies : [];

    return [
      '<article class="ptx-comment ' + (isReply ? "is-reply" : "") + '" data-pt-comment="' + esc(comment?.id) + '">',
        '<div class="ptx-comment-avatar">' + avatar(author) + '</div>',
        '<div class="ptx-comment-body">',
          '<div class="ptx-comment-head">',
            '<strong>' + esc(author?.name || "Mitarbeiter") + '</strong>',
            '<span>' + esc(dateTimeText(comment?.created_at)) + '</span>',
          '</div>',
          '<div class="ptx-comment-text">' + (comment?.comment || "") + '</div>',
          !isReply ? [
            '<button type="button" class="ptx-reply-toggle" data-pt-reply-toggle="' + esc(comment?.id) + '">',
              '<i data-lucide="reply"></i> Antworten',
            '</button>',
            '<form class="ptx-reply-form d-none" data-pt-reply-form="' + esc(comment?.id) + '">',
              '<textarea rows="2" placeholder="Antwort schreiben…" required></textarea>',
              '<button type="submit" class="ptx-btn ptx-btn-primary"><i data-lucide="send"></i> Antwort speichern</button>',
            '</form>'
          ].join("") : '',
          replies.length ? '<div class="ptx-replies">' + replies.map(function (reply) { return commentHTML(reply, true); }).join("") + '</div>' : '',
        '</div>',
      '</article>'
    ].join("");
  }

  function taskHTML(task, query, openComments) {
    const status = statusKey(task);
    const priority = String(task?.priority || "normal").toLowerCase();
    const keys = Array.isArray(task?.keys) ? task.keys : [];
    const comments = Array.isArray(task?.comments) ? task.comments : [];
    const employees = Array.isArray(task?.employees) ? task.employees : [];
    const keysDone = keys.filter(function (key) { return key?.is_completed || key?.done_status === "done"; }).length;
    const title = task?.title || task?.task_title || "Aufgabe";
    const due = dateText(task?.due_date);
    const start = dateText(task?.start_date);
    const reminder = dateText(task?.reminder_date);
    const open = openComments ? " is-comments-open" : "";

    return [
      '<article class="ptx-task-card' + open + '" data-pt-task-item="' + esc(task.id) + '" data-status="' + esc(status) + '">',
        '<div class="ptx-task-main">',
          '<div class="ptx-task-state ptx-status-' + esc(status) + '"><i data-lucide="' + (status === "done" ? "check-circle-2" : "circle-dot") + '"></i></div>',
          '<div class="ptx-task-copy">',
            '<div class="ptx-task-topline">',
              '<span class="ptx-status-badge ptx-status-' + esc(status) + '">' + esc(statusLabel(status)) + '</span>',
              '<span class="ptx-priority-badge ptx-priority-' + esc(priority) + '">' + esc(priorityLabel(priority)) + '</span>',
              '<span class="ptx-task-id">#' + esc(task.id) + (task.task_id ? " · " + esc(task.task_id) : "") + '</span>',
            '</div>',
            '<h3>' + esc(title) + '</h3>',
            task?.description ? '<p>' + esc(stripTags(task.description).slice(0, 220)) + '</p>' : '',
            '<div class="ptx-task-meta">',
              start ? '<span><i data-lucide="play-circle"></i> Start: ' + esc(start) + '</span>' : '',
              due ? '<span class="is-due"><i data-lucide="calendar-clock"></i> Fällig: ' + esc(due) + (task.due_time ? " · " + esc(task.due_time) : "") + '</span>' : '',
              reminder ? '<span><i data-lucide="bell"></i> Erinnerung: ' + esc(reminder) + '</span>' : '',
              '<span><i data-lucide="list-checks"></i> Keys: ' + keysDone + '/' + keys.length + '</span>',
              '<span><i data-lucide="messages-square"></i> Kommentare: ' + comments.length + '</span>',
            '</div>',
            '<div class="ptx-avatar-row">' + employeeAvatars(employees) + '</div>',
          '</div>',
          '<div class="ptx-task-actions">',
            '<button type="button" class="ptx-icon-action" data-ptk-edit="' + esc(task.id) + '" title="Bearbeiten"><i data-lucide="pencil"></i></button>',
            '<button type="button" class="ptx-icon-action is-danger" data-ptk-del="' + esc(task.id) + '" title="Löschen"><i data-lucide="trash-2"></i></button>',
            '<button type="button" class="ptx-icon-action" data-pt-comments-toggle="' + esc(task.id) + '" title="Kommentare öffnen"><i data-lucide="chevron-down"></i></button>',
          '</div>',
        '</div>',
        '<div class="ptx-task-details">',
          '<div class="ptx-task-section">',
            '<h4><i data-lucide="list-checks"></i> PersonalTaskKey</h4>',
            '<div class="ptx-key-list">',
              keys.length ? keys.map(keyHTML).join("") : '<div class="ptx-empty-small">Keine Keys vorhanden.</div>',
            '</div>',
          '</div>',
          '<div class="ptx-task-section ptx-comment-section">',
            '<h4><i data-lucide="messages-square"></i> Kommentare / Bericht</h4>',
            '<form class="ptx-comment-form" data-pt-comment-form="' + esc(task.id) + '">',
              '<textarea rows="3" placeholder="Kommentar oder Bericht schreiben…" required></textarea>',
              '<button type="submit" class="ptx-btn ptx-btn-primary"><i data-lucide="send"></i> Kommentar speichern</button>',
            '</form>',
            '<div class="ptx-comment-list">',
              comments.length ? comments.map(function (comment) { return commentHTML(comment, false); }).join("") : '<div class="ptx-empty-small">Noch keine Kommentare vorhanden.</div>',
            '</div>',
          '</div>',
        '</div>',
      '</article>'
    ].join("");
  }

  function patchPersonalTaskUI() {
    const PTK = window.PersonalTasksUI;
    if (!PTK || PTK.__ptxPatched) return false;

    PTK.__ptxPatched = true;
    PTK._openComments = PTK._openComments || new Set();

    PTK.ensureListShell = function () {
      const wrap = qs("#pt-list");
      if (!wrap) return;

      wrap.classList.add("ptx-task-feed", "pt-list-view");
      wrap.dataset.ptList = "1";
      wrap.dataset.ptkBoard = "";

      if (!qs("#ptTaskList", wrap)) {
        wrap.innerHTML = '<div id="ptTaskList" class="ptx-task-list pt-task-list"></div>';
      }

      this._searchEl = qs("#ptk-search");

      if (this._searchEl && !this._searchEl._ptxWired) {
        this._searchEl._ptxWired = true;
        this._searchEl.addEventListener("input", (event) => {
          this._query = String(event.target.value || "").toLowerCase().trim();
          this.renderFiltered();
        });
      }

      qsa("[data-pt-filter-status]").forEach((btn) => {
        if (btn._ptxWired) return;
        btn._ptxWired = true;
        btn.addEventListener("click", () => {
          qsa("[data-pt-filter-status]").forEach((item) => item.classList.remove("is-active"));
          btn.classList.add("is-active");
          this.renderFiltered();
        });
      });

      refreshIcons();
    };

    PTK.renderSkeletonContent = function () {
      const list = qs("#ptTaskList");
      if (list) {
        list.innerHTML = [
          '<div class="ptx-skeleton"></div>',
          '<div class="ptx-skeleton"></div>',
          '<div class="ptx-skeleton"></div>'
        ].join("");
      }
      refreshIcons();
    };

    PTK.renderFiltered = function () {
      const list = qs("#ptTaskList");
      if (!list) return;

      const activeStatus = qs("[data-pt-filter-status].is-active")?.dataset.ptFilterStatus || "";
      const normalizedActive = activeStatus === "completed" ? "done" : activeStatus;

      const tasks = Array.isArray(this._tasks) ? this._tasks : [];
      const query = String(this._query || "").toLowerCase().trim();

      const filtered = tasks.filter((task) => {
        const status = statusKey(task);
        const haystack = [
          task.title,
          task.task_title,
          stripTags(task.description),
          task.priority,
          status,
          ...(Array.isArray(task.employees) ? task.employees.map((e) => e.name || e.lastname || "") : []),
          ...(Array.isArray(task.comments) ? task.comments.map((c) => stripTags(c.comment || "")) : []),
          ...(Array.isArray(task.keys) ? task.keys.map((k) => k.task || k.title || k.description || "") : []),
        ].join(" ").toLowerCase();

        return (!normalizedActive || status === normalizedActive) && (!query || haystack.includes(query));
      });

      const counts = {
        all: tasks.length,
        open: tasks.filter((task) => statusKey(task) === "open").length,
        progress: tasks.filter((task) => statusKey(task) === "in_progress").length,
        done: tasks.filter((task) => statusKey(task) === "done").length,
      };

      const setText = (id, value) => {
        const el = qs(id);
        if (el) el.textContent = String(value);
      };

      setText("#pt-count", filtered.length);
      setText("#ptFilterAllCount", counts.all);
      setText("#ptFilterOpenCount", counts.open);
      setText("#ptFilterProgressCount", counts.progress);
      setText("#ptFilterCompletedCount", counts.done);

      if (!filtered.length) {
        list.innerHTML = [
          '<div class="ptx-empty">',
            '<i data-lucide="inbox"></i>',
            '<strong>Keine Aufgaben gefunden</strong>',
            '<span>Für diesen Kunden gibt es keine passende Aufgabe oder der Filter ist zu eng.</span>',
          '</div>'
        ].join("");
        refreshIcons();
        document.dispatchEvent(new CustomEvent("pt:tasks-rendered"));
        return;
      }

      filtered.sort((a, b) => {
        const av = new Date(a.due_date || a.reminder_date || a.start_date || a.created_at || 0).getTime();
        const bv = new Date(b.due_date || b.reminder_date || b.start_date || b.created_at || 0).getTime();
        return av - bv;
      });

      list.innerHTML = filtered.map((task) => taskHTML(task, query, this._openComments.has(String(task.id)))).join("");

      refreshIcons();
      document.dispatchEvent(new CustomEvent("pt:tasks-rendered"));
    };

    PTK.storeComment = async function (taskId, text) {
      const routes = this.routes ? this.routes() : (window.KANBAN_PERSONAL_TASK_PANEL_ROUTES || {});
      const url = routes.commentStore
        ? route(routes.commentStore, { TASK: taskId })
        : "/kanban/personal-task-panel/tasks/" + encodeURIComponent(taskId) + "/comments";

      const data = await ptxPostJSON(url, { comment: text });

      const task = (this._tasks || []).find((item) => String(item.id) === String(taskId));
      if (task && data?.comment) {
        task.comments = Array.isArray(task.comments) ? task.comments : [];
        task.comments.unshift(data.comment);
        task.comments_count = task.comments.length;
      }

      this._openComments.add(String(taskId));
      this.renderFiltered();
      return data;
    };

    PTK.storeReply = async function (commentId, text) {
      const routes = this.routes ? this.routes() : (window.KANBAN_PERSONAL_TASK_PANEL_ROUTES || {});
      const url = routes.replyStore
        ? route(routes.replyStore, { COMMENT: commentId })
        : "/kanban/personal-task-panel/comments/" + encodeURIComponent(commentId) + "/reply";

      await ptxPostJSON(url, { comment: text });
      await this.loadTasks();
    };

    PTK.toggleKey = async function (keyId) {
      const routes = this.routes ? this.routes() : (window.KANBAN_PERSONAL_TASK_PANEL_ROUTES || {});
      const url = routes.keyToggle
        ? route(routes.keyToggle, { KEY: keyId })
        : "/kanban/personal-task-panel/keys/" + encodeURIComponent(keyId) + "/toggle";

      await ptxPostJSON(url, {});
      await this.loadTasks();
    };

    document.addEventListener("click", function (event) {
      const toggle = event.target.closest("[data-pt-comments-toggle]");
      if (toggle) {
        event.preventDefault();
        const taskId = String(toggle.getAttribute("data-pt-comments-toggle") || "");
        if (!taskId) return;
        if (PTK._openComments.has(taskId)) PTK._openComments.delete(taskId);
        else PTK._openComments.add(taskId);
        PTK.renderFiltered();
        return;
      }

      const replyToggle = event.target.closest("[data-pt-reply-toggle]");
      if (replyToggle) {
        event.preventDefault();
        const id = replyToggle.getAttribute("data-pt-reply-toggle");
        const form = qs('[data-pt-reply-form="' + CSS.escape(id) + '"]');
        form?.classList.toggle("d-none");
        refreshIcons();
      }
    }, true);

    return true;
  }

  ready(function () {
    let tries = 0;
    const timer = setInterval(function () {
      tries += 1;
      if (patchPersonalTaskUI() || tries > 40) {
        clearInterval(timer);
      }
    }, 100);
  });
})();


/* ===== Kanban value analytics bridge ===== */
(function () {
  "use strict";

  window.KanbanValueAnalyticsNotifyFiltersChanged = window.KanbanValueAnalyticsNotifyFiltersChanged || function () {
    try {
      if (typeof window.KanbanValueAnalyticsReload === "function") {
        window.KanbanValueAnalyticsReload();
      }
    } catch (error) {
      console.warn("[Kanban] Value analytics reload failed", error);
    }
  };
})();



/* ==========================================================================
 * Kanban Personal Task integrated create/context patch
 * Adds directly into public/js/kanban.js:
 * - Employee Select2 inside Kanban task drawer
 * - Schritt / PersonalTaskKey builder
 * - Auto LeadStage/SubStage context from main Kanban vs Unterphasen board
 * - Task drawer opens above Unterphasen sidebar
 * ========================================================================== */
(function () {
  "use strict";

  if (window.__KANBAN_PERSONAL_TASK_CREATE_CONTEXT_PATCH__) return;
  window.__KANBAN_PERSONAL_TASK_CREATE_CONTEXT_PATCH__ = true;

  const SELECTORS = {
    drawer: "#pt-drawer",
    backdrop: "#pt-backdrop",
    form: "#pt-form",
    steps: "#pt-steps",
    addStep: "#pt-add-step",
    employee: "#pt-employee_ids",
    controller: "#pt-controller_ids",
    team: "#pt-team_id",
    customer: "#pt-customer_id",
    alternative: "#pt-alternative_id",
    product: "#pt-product_id",
    leadProduct: "#pt-lead_product_list_id",
    taskTitle: "#pt-task_title",
    description: "#pt-description",
    startDate: "#pt-start_date",
    dueDate: "#pt-due_date",
    dueTime: "#pt-due_time",
    priority: "#pt-priority",
    color: "#pt-color",
    totalDay: "#pt-total_day",
    totalTime: "#pt-total_time",
    public: "#pt-public",
    isCustomer: "#pt-is_customer",
    leadStage: "#pt-lead_stage_id",
    leadSubStage: "#pt-lead_stage_sub_stage_id",
    stageChips: "#pt-stage-context-chips"
  };

  function ready(fn) {
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", fn);
    else fn();
  }

  function qs(selector, ctx) {
    return (ctx || document).querySelector(selector);
  }

  function qsa(selector, ctx) {
    return Array.from((ctx || document).querySelectorAll(selector));
  }

  function csrf() {
    return qs('meta[name="csrf-token"]')?.content || window.KANBAN_BOOT?.csrf || "";
  }

  function esc(value) {
    return String(value ?? "").replace(/[&<>"']/g, function (m) {
      return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" })[m];
    });
  }

  function numOrNull(value) {
    if (value === null || value === undefined || value === "") return null;
    const n = Number(value);
    return Number.isFinite(n) && n > 0 ? n : null;
  }

  function refreshIcons() {
    requestAnimationFrame(function () {
      if (window.lucide && typeof window.lucide.createIcons === "function") window.lucide.createIcons();
      if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    });
  }

  function employees() {
    const raw = window.ALL_EMPLOYEES || window.KANBAN_BOOT?.employees || [];
    if (Array.isArray(raw)) return raw;
    if (raw && typeof raw === "object") return Object.values(raw);
    return [];
  }

  function employeeName(emp) {
    return [emp?.lastname, emp?.name].filter(Boolean).join(" ").trim() || emp?.full_name || emp?.text || ("Mitarbeiter #" + (emp?.id || ""));
  }

  function employeeOptions(selectedIds = []) {
    const selected = new Set((selectedIds || []).map(String));
    return employees().map(function (emp) {
      const id = emp?.id ?? emp?.employee_id ?? "";
      if (!id) return "";
      return '<option value="' + esc(id) + '"' + (selected.has(String(id)) ? " selected" : "") + ">" + esc(employeeName(emp)) + "</option>";
    }).join("");
  }

  function teams() {
    const raw = window.KANBAN_BOOT?.teams || window.PERSONAL_TASK_TEAMS || window.ALL_TEAMS || [];
    if (Array.isArray(raw)) return raw;
    if (raw && typeof raw === "object") return Object.values(raw);
    return [];
  }

  function teamName(team) {
    return team?.name || team?.team_name || team?.title || ("Team #" + (team?.id || ""));
  }

  function teamOptions(selectedId = "") {
    const selected = String(selectedId || "");
    return teams().map(function (team) {
      const id = team?.id || team?.team_id || "";
      if (!id) return "";
      return '<option value="' + esc(id) + '"' + (selected === String(id) ? " selected" : "") + ">" + esc(teamName(team)) + "</option>";
    }).join("");
  }

  function stageMaps() {
    const app = window.APP || window.KanbanAPP || {};
    return {
      stageNames: app.stageNames || window.KANBAN_BOOT?.leadStageNamesForJs || {},
      stageMeta: app.stageMeta || app.companyKanbanStageMeta || window.KANBAN_BOOT?.leadStageMetaForJs || {},
      kanbanStageMeta: app.kanbanStageMeta || window.KANBAN_BOOT?.kanbanStageMetaForJs || {}
    };
  }

  function canonicalStage(raw) {
    const app = window.APP || window.KanbanAPP || {};
    const aliases = app.stageAlias || {
      open: "lead",
      neue: "lead",
      new: "lead",
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
      archiv: "archive",
      archive: "archive",
      junk: "junk"
    };
    const key = String(raw || "").toLowerCase().trim();
    if (!key) return "lead";
    if (key.startsWith("product_stage_")) return key;
    return aliases[key] || key;
  }

  function stageMetaForKey(stageKey) {
    const maps = stageMaps();
    return maps.stageMeta?.[stageKey] || maps.kanbanStageMeta?.[stageKey] || {};
  }

  function stageNameForKey(stageKey) {
    const maps = stageMaps();
    const meta = stageMetaForKey(stageKey);
    return meta?.name || maps.stageNames?.[stageKey] || stageKey || "LeadStage";
  }

  function findSubStage(stageKey, subStageId) {
    if (!subStageId) return null;
    const meta = stageMetaForKey(stageKey);
    const subs = Array.isArray(meta.sub_stages)
      ? meta.sub_stages
      : (Array.isArray(meta.subStages) ? meta.subStages : []);
    return subs.find(function (sub) {
      return String(sub?.id) === String(subStageId);
    }) || null;
  }

  function stageContextFromCard(card) {
    card = card || null;
    const app = window.APP || window.KanbanAPP || {};

    const zone = card?.closest?.("[data-understage-dropzone]") || null;
    const insideUnderstage = !!(zone || card?.closest?.("#kbUnderstageBoard") || card?.closest?.(".kb-understage-sidebar"));

    const stageKey = canonicalStage(
      zone?.dataset?.stageKey ||
      app.underStage?.stageKey ||
      card?.dataset?.companyStage ||
      card?.dataset?.stage ||
      card?.closest?.(".column")?.id ||
      "lead"
    );

    const stageMeta = stageMetaForKey(stageKey);
    const stageId = stageMeta?.id || stageMeta?.stage_id || "";

    let subStageId = "";
    if (insideUnderstage) {
      subStageId =
        zone?.dataset?.subStageId ||
        card?.dataset?.leadStageSubStageId ||
        card?.dataset?.subStageId ||
        "";
    }

    const subStage = findSubStage(stageKey, subStageId);

    return {
      lead_stage_key: stageKey,
      lead_stage_id: stageId || "",
      lead_stage_name: stageNameForKey(stageKey),
      lead_stage_color: stageMeta?.color || "#74b2d4",
      lead_stage_sub_stage_id: subStageId || "",
      lead_stage_sub_stage_name: subStage?.name || "",
      lead_stage_sub_stage_color: subStage?.color || "#93c21c",
      is_understage_context: !!insideUnderstage
    };
  }

  function patchOpenEventContext() {
    if (window.__KANBAN_TASK_OPEN_CONTEXT_CAPTURE__) return;
    window.__KANBAN_TASK_OPEN_CONTEXT_CAPTURE__ = true;

    document.addEventListener("open-personal-tasks", function (event) {
      const detail = event.detail || {};
      const card =
        event.target?.closest?.(".card") ||
        document.querySelector(
          '.card[data-customer-id="' + CSS.escape(String(detail.customerId || "")) + '"][data-alternative-id="' + CSS.escape(String(detail.alternativeId || "")) + '"][data-product-id="' + CSS.escape(String(detail.productId || "")) + '"]'
        );

      const context = stageContextFromCard(card);
      detail.leadStageContext = context;
      detail.lead_stage_context = context;
      detail.lead_stage_id = context.lead_stage_id;
      detail.lead_stage_name = context.lead_stage_name;
      detail.lead_stage_sub_stage_id = context.lead_stage_sub_stage_id;
      detail.lead_stage_sub_stage_name = context.lead_stage_sub_stage_name;

      window.__KANBAN_TASK_LAST_OPEN_CONTEXT__ = {
        customerId: detail.customerId || "",
        alternativeId: detail.alternativeId || "",
        productId: detail.productId || "",
        leadProductListId: detail.leadProductListId || detail.lead_product_list_id || "",
        title: detail.title || "",
        leadStageContext: context,
        cardId: card?.id || ""
      };
    }, true);
  }

  function ensureStyle() {
    if (qs("#kanban-task-integrated-patch-style")) return;

    const style = document.createElement("style");
    style.id = "kanban-task-integrated-patch-style";
    style.textContent = `
      #pt-backdrop,
      .pt-backdrop,
      .ptx-backdrop {
        z-index: 450000 !important;
      }

      #pt-drawer,
      .pt-drawer,
      .pt-lucide-drawer,
      .ptx-drawer {
        z-index: 450010 !important;
      }

      .select2-container--open {
        z-index: 450050 !important;
      }

      .kanban-task-stage-chips {
        display:flex;
        flex-wrap:wrap;
        gap:7px;
        margin:0 0 12px 0;
        padding:10px 12px;
        border:1px solid #dbeafe;
        border-radius:16px;
        background:linear-gradient(135deg,#fff 0%,#eef7fb 100%);
      }

      .kanban-task-stage-chip {
        display:inline-flex;
        align-items:center;
        gap:7px;
        min-height:30px;
        padding:0 10px;
        border-radius:999px;
        border:1px solid #dbeafe;
        background:#fff;
        color:#0f172a;
        font-size:11px;
        font-weight:950;
        white-space:nowrap;
      }

      .kanban-task-stage-chip i,
      .kanban-task-stage-chip svg {
        width:14px;
        height:14px;
      }

      .kanban-task-assignment-grid {
        display:grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap:12px;
        margin:12px 0;
      }

      .kanban-task-assignment-grid .ptx-form-group {
        min-width:0;
      }

      @media (max-width: 768px) {
        .kanban-task-assignment-grid { grid-template-columns: 1fr; }
      }

      .ptx-steps-box,
      .kanban-task-steps-box {
        grid-column:1 / -1;
        border:1px solid #dbeafe;
        background:#fff;
        border-radius:18px;
        padding:12px;
        margin-top:12px;
      }

      .ptx-steps-head,
      .kanban-task-steps-head {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-bottom:10px;
      }

      .ptx-steps-head strong,
      .kanban-task-steps-head strong {
        display:block;
        color:#0f172a;
        font-size:13px;
        font-weight:950;
      }

      .ptx-steps-head small,
      .kanban-task-steps-head small {
        display:block;
        color:#64748b;
        font-size:11px;
        font-weight:800;
        margin-top:2px;
      }

      .ptx-secondary-btn,
      .kanban-task-step-add,
      .kanban-task-step-remove {
        border:1px solid #dbeafe;
        background:#fff;
        color:#334155;
        min-height:34px;
        padding:0 10px;
        border-radius:12px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:6px;
        font-size:12px;
        font-weight:950;
        cursor:pointer;
      }

      .ptx-secondary-btn:hover,
      .kanban-task-step-add:hover {
        background:#eef7fb;
        border-color:#74b2d4;
      }

      .kanban-task-step-remove {
        color:#b91c1c;
        border-color:#fecaca;
        background:#fef2f2;
      }

      #pt-steps,
      .ptx-steps-list,
      .kanban-task-steps-list {
        display:flex;
        flex-direction:column;
        gap:10px;
      }

      .kanban-task-step-row {
        display:grid;
        grid-template-columns:minmax(180px,1fr) 110px 150px minmax(180px,1fr) 42px;
        gap:8px;
        align-items:start;
        padding:10px;
        border:1px solid #e5eef8;
        border-radius:16px;
        background:#f8fafc;
      }

      .kanban-task-step-row textarea,
      .kanban-task-step-row input,
      .kanban-task-step-row select {
        width:100%;
        min-height:36px;
        border:1px solid #dbeafe;
        border-radius:12px;
        background:#fff;
        padding:7px 9px;
        color:#0f172a;
        font-size:12px;
        font-weight:750;
      }

      .kanban-task-step-row textarea {
        grid-column:1 / -2;
        min-height:58px;
        resize:vertical;
      }

      .kanban-task-step-employees {
        grid-column:1 / -2;
      }

      @media(max-width:980px) {
        .kanban-task-step-row {
          grid-template-columns:1fr;
        }

        .kanban-task-step-row textarea,
        .kanban-task-step-employees {
          grid-column:auto;
        }
      }
    `;
    document.head.appendChild(style);
  }

  function ensureHidden(name, id) {
    const form = qs(SELECTORS.form);
    if (!form) return null;

    let input = qs("#" + id, form) || qs("#" + id);
    if (!input) {
      input = document.createElement("input");
      input.type = "hidden";
      input.name = name;
      input.id = id;
      form.appendChild(input);
    }
    return input;
  }

  function ensureStageFields() {
    ensureHidden("lead_stage_id", "pt-lead_stage_id");
    ensureHidden("lead_stage_sub_stage_id", "pt-lead_stage_sub_stage_id");
    ensureHidden("stage_mode", "pt-stage_mode");
  }

  function ensureStageChips() {
    const form = qs(SELECTORS.form);
    if (!form) return null;

    let chips = qs(SELECTORS.stageChips, form);
    if (!chips) {
      chips = document.createElement("div");
      chips.id = "pt-stage-context-chips";
      chips.className = "kanban-task-stage-chips";
      const first = form.firstElementChild;
      form.insertBefore(chips, first || null);
    }
    return chips;
  }

  function renderStageChips(context) {
    const chips = ensureStageChips();
    if (!chips) return;

    const stage = context || {};
    const stageName = stage.lead_stage_name || "Hauptphase";
    const subName = stage.lead_stage_sub_stage_name || "";
    const isSub = !!stage.lead_stage_sub_stage_id;

    chips.innerHTML = [
      '<span class="kanban-task-stage-chip" style="border-color:' + esc(stage.lead_stage_color || "#74b2d4") + '">',
        '<i class="feather icon-git-branch"></i>',
        esc(stageName),
      '</span>',
      isSub
        ? '<span class="kanban-task-stage-chip" style="border-color:' + esc(stage.lead_stage_sub_stage_color || "#93c21c") + '"><i class="feather icon-layers"></i>' + esc(subName) + '</span>'
        : '<span class="kanban-task-stage-chip"><i class="feather icon-columns"></i>Nur Hauptphase</span>'
    ].join("");

    refreshIcons();
  }

  function applyStageContext(context) {
    ensureStageFields();

    const c = context || {};
    const stageInput = qs(SELECTORS.leadStage);
    const subInput = qs(SELECTORS.leadSubStage);
    const modeInput = qs("#pt-stage_mode");

    if (stageInput) stageInput.value = c.lead_stage_id || "";
    if (subInput) subInput.value = c.lead_stage_sub_stage_id || "";
    if (modeInput) modeInput.value = c.lead_stage_sub_stage_id ? "sub_stage" : "main_stage";

    renderStageChips(c);
  }

  function ensureAssignmentFields() {
    const form = qs(SELECTORS.form);
    if (!form) return;

    const employeeWrap = qs("#pt-employee-wrap", form) || qs(SELECTORS.employee)?.closest?.(".ptx-form-group, .pt-form-group, div");

    let grid = qs("#pt-assignment-grid", form);
    if (!grid) {
      grid = document.createElement("div");
      grid.id = "pt-assignment-grid";
      grid.className = "kanban-task-assignment-grid";
      if (employeeWrap) {
        employeeWrap.insertAdjacentElement("afterend", grid);
      } else {
        form.appendChild(grid);
      }
    }

    if (!qs(SELECTORS.controller, form)) {
      const box = document.createElement("div");
      box.className = "ptx-form-group";
      box.innerHTML = [
        '<label for="pt-controller_ids">Controller / Kontrolle</label>',
        '<select id="pt-controller_ids" name="controller[]" class="form-control select2" multiple data-placeholder="Controller wählen">',
          employeeOptions([]),
        '</select>'
      ].join("");
      grid.appendChild(box);
    }

    if (!qs(SELECTORS.team, form)) {
      const box = document.createElement("div");
      box.className = "ptx-form-group";
      box.innerHTML = [
        '<label for="pt-team_id">Team</label>',
        '<select id="pt-team_id" name="team_id" class="form-control select2" data-placeholder="Team wählen">',
          '<option value="">Kein Team</option>',
          teamOptions(""),
        '</select>'
      ].join("");
      grid.appendChild(box);
    } else if (teams().length && qs(SELECTORS.team, form)?.options?.length <= 1) {
      qs(SELECTORS.team, form).insertAdjacentHTML("beforeend", teamOptions(""));
    }
  }

  function initSelect2ForTaskDrawer(root = document) {
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) return;

    const $ = window.jQuery;
    const drawer = $(SELECTORS.drawer);
    const parent = drawer.length ? drawer : $(document.body);

    const init = function (selector, options = {}) {
      $(selector, root).each(function () {
        const $el = $(this);
        if (!$el.length) return;

        if ($el.hasClass("select2-hidden-accessible")) {
          try { $el.select2("destroy"); } catch (_) {}
        }

        $el.select2({
          width: "100%",
          dropdownParent: parent,
          allowClear: true,
          closeOnSelect: !this.multiple,
          placeholder: this.getAttribute("data-placeholder") || "Auswählen…",
          ...options
        });
      });
    };

    ensureAssignmentFields();
    init(SELECTORS.employee);
    init(SELECTORS.controller);
    init(SELECTORS.team);
    init(".kanban-task-step-employee-select");
    init("#pt-form .select2");
  }

  function ensureStepsBox() {
    const form = qs(SELECTORS.form);
    if (!form) return null;

    let steps = qs(SELECTORS.steps, form) || qs(SELECTORS.steps);
    let addBtn = qs(SELECTORS.addStep, form) || qs(SELECTORS.addStep);

    if (!steps) {
      const box = document.createElement("div");
      box.className = "kanban-task-steps-box ptx-steps-box";
      box.innerHTML = [
        '<div class="kanban-task-steps-head ptx-steps-head">',
          '<div>',
            '<strong>Personal Task Keys / Arbeitsschritte</strong>',
            '<small>Optional: Schritte mit eigener Mitarbeiter-Zuordnung.</small>',
          '</div>',
          '<button type="button" class="kanban-task-step-add ptx-secondary-btn" id="pt-add-step">',
            '<i class="feather icon-plus"></i> Schritt hinzufügen',
          '</button>',
        '</div>',
        '<div id="pt-steps" class="kanban-task-steps-list ptx-steps-list"></div>'
      ].join("");

      const actions =
        qs(".ptx-create-actions", form) ||
        qs(".pt-form-actions", form) ||
        qs('[type="submit"]', form)?.parentElement ||
        null;

      form.insertBefore(box, actions || null);
      steps = qs(SELECTORS.steps, form);
      addBtn = qs(SELECTORS.addStep, form);
    }

    if (addBtn && !addBtn.__kanbanStepBound) {
      addBtn.__kanbanStepBound = true;
      addBtn.addEventListener("click", function (event) {
        event.preventDefault();
        addStepRow();
      });
    }

    return steps;
  }

  function stepRowHTML(index, data = {}) {
    const selected = data.employee_ids || data.employee_id || [];
    const selectedIds = Array.isArray(selected) ? selected : [selected].filter(Boolean);

    return [
      '<div class="kanban-task-step-row" data-pt-step-row>',
        '<input type="text" name="key[' + index + '][task]" data-step-field="task" placeholder="Schritt Titel" value="' + esc(data.task || data.title || "") + '">',
        '<input type="number" min="0" step="1" name="key[' + index + '][duration]" data-step-field="duration" placeholder="Min." value="' + esc(data.duration || "") + '">',
        '<select name="key[' + index + '][status]" data-step-field="status">',
          '<option value="pending"' + (String(data.status || "pending") === "pending" ? " selected" : "") + '>Offen</option>',
          '<option value="in_progress"' + (String(data.status || "") === "in_progress" ? " selected" : "") + '>In Arbeit</option>',
          '<option value="completed"' + (String(data.status || "") === "completed" ? " selected" : "") + '>Erledigt</option>',
        '</select>',
        '<select class="kanban-task-step-employee-select" name="key[' + index + '][employee_id][]" data-step-field="employee_id" multiple data-placeholder="Mitarbeiter">',
          employeeOptions(selectedIds),
        '</select>',
        '<button type="button" class="kanban-task-step-remove" data-remove-step title="Schritt entfernen"><i class="feather icon-trash-2"></i></button>',
        '<textarea name="key[' + index + '][key_description]" data-step-field="key_description" placeholder="Beschreibung / Hinweis">' + esc(data.description || data.key_description || "") + '</textarea>',
      '</div>'
    ].join("");
  }

  function renumberStepRows() {
    qsa("[data-pt-step-row]").forEach(function (row, idx) {
      qsa("[name]", row).forEach(function (field) {
        field.name = field.name.replace(/key\[\d+\]/, "key[" + idx + "]");
      });
    });
  }

  function addStepRow(data = {}) {
    const steps = ensureStepsBox();
    if (!steps) return;

    const index = qsa("[data-pt-step-row]", steps).length;
    const wrap = document.createElement("div");
    wrap.innerHTML = stepRowHTML(index, data);
    const row = wrap.firstElementChild;
    steps.appendChild(row);

    const remove = qs("[data-remove-step]", row);
    if (remove) {
      remove.addEventListener("click", function () {
        row.remove();
        renumberStepRows();
      });
    }

    initSelect2ForTaskDrawer(row);
    refreshIcons();
  }

  function clearSteps() {
    const steps = ensureStepsBox();
    if (steps) steps.innerHTML = "";
  }

  function collectSteps() {
    return qsa("[data-pt-step-row]").map(function (row) {
      const get = (field) => qs('[data-step-field="' + field + '"]', row)?.value || "";
      let employeeIds = [];
      const emp = qs('[data-step-field="employee_id"]', row);
      if (emp) {
        employeeIds = Array.from(emp.selectedOptions || []).map((opt) => opt.value).filter(Boolean);
      }

      return {
        task: get("task").trim(),
        title: get("task").trim(),
        duration: get("duration") || null,
        status: get("status") || "pending",
        key_description: get("key_description").trim() || null,
        description: get("key_description").trim() || null,
        employee_id: employeeIds,
        employee_ids: employeeIds
      };
    }).filter(function (step) {
      return step.task || step.description || (step.employee_ids && step.employee_ids.length);
    });
  }

  async function postJSON(url, payload) {
    const response = await fetch(url, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf(),
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: JSON.stringify(payload || {})
    });

    const text = await response.text();
    let data = {};
    try {
      data = text ? JSON.parse(text) : {};
    } catch (_) {
      data = { message: text || ("HTTP " + response.status) };
    }

    if (!response.ok || data.success === false || data.status === "error") {
      const first = data.errors ? Object.values(data.errors).flat()[0] : null;
      throw new Error(first || data.message || ("HTTP " + response.status));
    }

    return data;
  }

  async function putJSON(url, payload) {
    const response = await fetch(url, {
      method: "PUT",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf(),
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: JSON.stringify(payload || {})
    });

    const text = await response.text();
    let data = {};
    try {
      data = text ? JSON.parse(text) : {};
    } catch (_) {
      data = { message: text || ("HTTP " + response.status) };
    }

    if (!response.ok || data.success === false || data.status === "error") {
      const first = data.errors ? Object.values(data.errors).flat()[0] : null;
      throw new Error(first || data.message || ("HTTP " + response.status));
    }

    return data;
  }

  function taskIdFromResponse(data) {
    return (
      data?.task?.id ||
      data?.personal_task?.id ||
      data?.data?.id ||
      data?.id ||
      data?.task_id ||
      null
    );
  }

  async function createStepsForTask(taskId, steps) {
    if (!taskId || !steps.length) return [];

    const app = window.APP || window.KanbanAPP || {};
    const endpoint = app.endpoints?.ptStepsStore
      ? app.endpoints.ptStepsStore(taskId)
      : "/personal-tasks/" + encodeURIComponent(taskId) + "/steps";

    const created = [];
    for (const step of steps) {
      const payload = {
        task: step.task,
        title: step.task,
        key_description: step.description,
        description: step.description,
        duration: step.duration,
        status: step.status || "pending",
        employee_id: step.employee_ids,
        employee_ids: step.employee_ids,
        employees: step.employee_ids
      };

      if (!payload.task && !payload.description) continue;
      created.push(await postJSON(endpoint, payload));
    }

    return created;
  }

  function currentContextForSubmit(PTK) {
    const last = window.__KANBAN_TASK_LAST_OPEN_CONTEXT__ || {};
    return {
      customerId: qs(SELECTORS.customer)?.value || last.customerId || PTK?._ctx?.customerId || "",
      alternativeId: qs(SELECTORS.alternative)?.value || last.alternativeId || PTK?._ctx?.alternativeId || "",
      productId: qs(SELECTORS.product)?.value || last.productId || PTK?._ctx?.productId || "",
      leadProductListId: qs(SELECTORS.leadProduct)?.value || last.leadProductListId || PTK?._ctx?.leadProductListId || "",
      leadStageContext: last.leadStageContext || {}
    };
  }

  function selectedValues(selector) {
    if (window.jQuery) {
      const value = window.jQuery(selector).val() || [];
      return Array.isArray(value) ? value.map(String).filter(Boolean) : [value].map(String).filter(Boolean);
    }

    const select = qs(selector);
    return select ? Array.from(select.selectedOptions || []).map((opt) => String(opt.value)).filter(Boolean) : [];
  }

  function employeeIdsFromTaskSelect() {
    return selectedValues(SELECTORS.employee);
  }

  function controllerIdsFromTaskSelect() {
    return selectedValues(SELECTORS.controller);
  }

  function teamIdFromTaskSelect() {
    if (window.jQuery) return String(window.jQuery(SELECTORS.team).val() || "");
    return String(qs(SELECTORS.team)?.value || "");
  }

  function appendArray(fd, name, values) {
    (values || []).map(String).filter(Boolean).forEach(function (value) {
      fd.append(name, value);
    });
  }

  function appendStepsToFormData(fd, steps) {
    (steps || []).forEach(function (step, index) {
      fd.append('key[' + index + '][task]', step.task || step.title || '');
      if (step.duration !== null && step.duration !== undefined && step.duration !== '') fd.append('key[' + index + '][duration]', step.duration);
      fd.append('key[' + index + '][status]', step.status || 'pending');
      if (step.description) fd.append('key[' + index + '][key_description]', step.description);
      appendArray(fd, 'key[' + index + '][employee_id][]', step.employee_ids || []);
    });
  }

  function buildTaskFormData(payload, steps, employeeIds, controllerIds, teamId, isEdit) {
    const fd = new FormData();

    Object.entries(payload || {}).forEach(function ([key, value]) {
      if (key === 'employee' || key === 'employee_ids' || key === 'employees' || key === 'controller' || key === 'controller_ids' || key === 'controllers' || key === 'key' || key === 'keys' || key === 'step_keys') return;
      if (value === null || value === undefined) return;
      fd.append(key, value);
    });

    // These names match the normal personal task Blade: name="employee[]", name="controller[]", name="team_id".
    appendArray(fd, 'employee[]', employeeIds);
    appendArray(fd, 'employee_ids[]', employeeIds);
    appendArray(fd, 'employees[]', employeeIds);

    appendArray(fd, 'controller[]', controllerIds);
    appendArray(fd, 'controller_ids[]', controllerIds);
    appendArray(fd, 'controllers[]', controllerIds);

    if (teamId) {
      fd.append('team_id', teamId);
      fd.append('team', teamId);
    }

    appendStepsToFormData(fd, steps);

    if (isEdit) fd.append('_method', 'PUT');

    return fd;
  }

  async function submitFormData(url, fd, isEdit) {
    const response = await fetch(url, {
      method: isEdit ? 'POST' : 'POST',
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': csrf(),
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: fd
    });

    const text = await response.text();
    let data = {};
    try {
      data = text ? JSON.parse(text) : {};
    } catch (_) {
      data = { message: text || ('HTTP ' + response.status) };
    }

    if (!response.ok || data.success === false || data.status === 'error') {
      const first = data.errors ? Object.values(data.errors).flat()[0] : null;
      throw new Error(first || data.message || ('HTTP ' + response.status));
    }

    return data;
  }

  function patchPersonalTasksUI() {
    const PTK = window.PersonalTasksUI;
    if (!PTK || PTK.__kanbanTaskCreateContextReady) return !!PTK;

    PTK.__kanbanTaskCreateContextReady = true;

    const originalOpen = typeof PTK.open === "function" ? PTK.open.bind(PTK) : null;
    const originalShow = typeof PTK.show === "function" ? PTK.show.bind(PTK) : null;
    const originalFillForm = typeof PTK.fillForm === "function" ? PTK.fillForm.bind(PTK) : null;

    PTK.show = function () {
      if (originalShow) originalShow();

      ensureStyle();
      ensureStageFields();
      ensureAssignmentFields();
      ensureStepsBox();

      qs(SELECTORS.backdrop)?.classList.add("show");
      qs(SELECTORS.drawer)?.classList.add("open");
      qs(SELECTORS.drawer)?.classList.add("is-task-on-top");
      document.body.classList.add("kanban-task-drawer-on-top");

      window.setTimeout(function () {
        initSelect2ForTaskDrawer();
      }, 30);
    };

    PTK.open = function (customerId, alternativeId, productId, title, leadProductListId) {
      const last = window.__KANBAN_TASK_LAST_OPEN_CONTEXT__ || {};
      const stageContext = last.leadStageContext || {};

      if (originalOpen) {
        originalOpen(customerId, alternativeId, productId, title, leadProductListId);
      }

      ensureStyle();
      ensureStageFields();
      ensureAssignmentFields();
      ensureStepsBox();
      applyStageContext(stageContext);

      // The Kanban card already gives the customer/object/product context.
      // Keep the normal customer search hidden/unused inside the Kanban drawer.
      const isCustomer = qs(SELECTORS.isCustomer);
      if (isCustomer) isCustomer.checked = true;

      clearSteps();

      window.setTimeout(function () {
        initSelect2ForTaskDrawer();
      }, 50);
    };

    PTK.submitForm = async function (event) {
      event.preventDefault();

      const app = window.APP || window.KanbanAPP || {};
      const title = (qs(SELECTORS.taskTitle)?.value || "").trim();

      if (!title) {
        if (window.Swal) window.Swal.fire("Fehler", "Aufgabentitel ist erforderlich.", "error");
        else alert("Aufgabentitel ist erforderlich.");
        return;
      }

      const ctx = currentContextForSubmit(this);
      const steps = collectSteps();
      const employeeIds = employeeIdsFromTaskSelect();
      const controllerIds = controllerIdsFromTaskSelect();
      const teamId = teamIdFromTaskSelect();

      if (!ctx.customerId) {
        if (window.Swal) window.Swal.fire("Fehler", "Kunde fehlt im Kanban-Kontext.", "error");
        else alert("Kunde fehlt im Kanban-Kontext.");
        return;
      }

      const payload = {
        is_customer: 1,
        customer_id: numOrNull(ctx.customerId),
        alternative_id: numOrNull(ctx.alternativeId),
        product_id: numOrNull(ctx.productId),
        lead_product_list_id: numOrNull(ctx.leadProductListId),

        lead_stage_id: numOrNull(qs(SELECTORS.leadStage)?.value),
        lead_stage_sub_stage_id: numOrNull(qs(SELECTORS.leadSubStage)?.value),
        stage_mode: qs("#pt-stage_mode")?.value || (qs(SELECTORS.leadSubStage)?.value ? "sub_stage" : "main_stage"),

        task_title: title,
        description: (qs(SELECTORS.description)?.value || "").trim() || null,
        start_date: qs(SELECTORS.startDate)?.value || null,
        due_date: qs(SELECTORS.dueDate)?.value || null,
        due_time: qs(SELECTORS.dueTime)?.value || null,
        priority: qs(SELECTORS.priority)?.value || "normal",
        color: qs(SELECTORS.color)?.value || "#8fc73e",
        total_day: qs(SELECTORS.totalDay)?.value || null,
        total_time: qs(SELECTORS.totalTime)?.value || null,
        public: qs(SELECTORS.public)?.checked ? 1 : 0,
        task_status: "open",
        board_column: "open",

        employee_ids: employeeIds,
        employee: employeeIds,
        employees: employeeIds,
        controller_ids: controllerIds,
        controller: controllerIds,
        controllers: controllerIds,
        team_id: teamId || null,
        team: teamId || null,

        key: steps,
        keys: steps,
        step_keys: steps
      };

      const isEdit = !!this._editingId;
      const url = isEdit
        ? app.endpoints.personalTasksUpdate(this._editingId)
        : app.endpoints.personalTasksStore;
      const method = isEdit ? "PUT" : "POST";

      if (!url) {
        if (window.Swal) window.Swal.fire("Fehler", "PersonalTask Store Route fehlt.", "error");
        return;
      }

      try {
        const formData = buildTaskFormData(payload, steps, employeeIds, controllerIds, teamId, isEdit);
        const data = await submitFormData(url, formData, isEdit);

        const taskId = taskIdFromResponse(data);
        let stepsWarning = "";

        // If your main PersonalTask store does not process key[], create steps via the dedicated endpoint.
        if (!isEdit && taskId && steps.length) {
          try {
            await createStepsForTask(taskId, steps);
          } catch (stepError) {
            console.warn("[Kanban] Task saved, but steps could not be created:", stepError);
            stepsWarning = " Aufgabe wurde gespeichert, aber ein Schritt konnte nicht gespeichert werden.";
          }
        }

        this._editingId = null;

        const form = qs(SELECTORS.form);
        if (form) form.reset();

        if (window.jQuery) {
          window.jQuery(SELECTORS.employee).val(null).trigger("change");
          window.jQuery(SELECTORS.controller).val(null).trigger("change");
          window.jQuery(SELECTORS.team).val(null).trigger("change");
        }

        clearSteps();
        applyStageContext((window.__KANBAN_TASK_LAST_OPEN_CONTEXT__ || {}).leadStageContext || {});

        if (typeof this.loadTasks === "function") {
          await this.loadTasks();
        }

        try {
          if (typeof window.LeadUIFetchKanban === "function") {
            window.LeadUIFetchKanban(window.LeadUI?.State?.filtersQS || "");
          }
        } catch (_) {}

        if (window.Swal) {
          window.Swal.fire("Gespeichert", "Aufgabe wurde gespeichert." + stepsWarning, stepsWarning ? "warning" : "success");
        }
      } catch (error) {
        if (window.Swal) window.Swal.fire("Fehler", error.message || "Aufgabe konnte nicht gespeichert werden.", "error");
        else alert(error.message || "Aufgabe konnte nicht gespeichert werden.");
      }
    };

    PTK.fillForm = function (id) {
      if (originalFillForm) originalFillForm(id);

      const task = (this._tasks || []).find(function (item) {
        return String(item.id) === String(id);
      });

      ensureStyle();
      ensureStageFields();
      ensureAssignmentFields();
      ensureStepsBox();

      clearSteps();

      if (task) {
        const stageContext = task.lead_stage_context || {
          lead_stage_id: task.lead_stage_id || "",
          lead_stage_name: task.lead_stage_name || "",
          lead_stage_color: task.lead_stage_color || "#74b2d4",
          lead_stage_sub_stage_id: task.lead_stage_sub_stage_id || "",
          lead_stage_sub_stage_name: task.lead_stage_sub_stage_name || "",
          lead_stage_sub_stage_color: task.lead_stage_sub_stage_color || "#93c21c"
        };
        applyStageContext(stageContext);

        if (window.jQuery) {
          const employeeIds = Array.isArray(task.employees) ? task.employees.map(function (emp) { return String(emp.id || emp.employee_id || ""); }).filter(Boolean) : [];
          const controllerIds = Array.isArray(task.controllers) ? task.controllers.map(function (emp) { return String(emp.id || emp.employee_id || ""); }).filter(Boolean) : [];
          window.jQuery(SELECTORS.employee).val(employeeIds).trigger("change");
          window.jQuery(SELECTORS.controller).val(controllerIds).trigger("change");
          if (task.team_id || task.team?.id) window.jQuery(SELECTORS.team).val(String(task.team_id || task.team.id)).trigger("change");
        }

        const keys = Array.isArray(task.keys) ? task.keys : [];
        keys.forEach(function (key) {
          addStepRow({
            task: key.task || key.title || "",
            duration: key.duration || "",
            status: key.status || (key.is_completed ? "completed" : "pending"),
            description: key.description || key.key_description || "",
            employee_ids: key.employee_ids || (Array.isArray(key.employees) ? key.employees.map((emp) => emp.id || emp.employee_id).filter(Boolean) : [])
          });
        });
      }

      window.setTimeout(function () {
        initSelect2ForTaskDrawer();
      }, 30);
    };

    const form = qs(SELECTORS.form);
    if (form && !form.__kanbanTaskSubmitBound) {
      form.__kanbanTaskSubmitBound = true;
      form.addEventListener("submit", function (event) {
        if (window.PersonalTasksUI && typeof window.PersonalTasksUI.submitForm === "function") {
          window.PersonalTasksUI.submitForm(event);
        }
      }, true);
    }

    ensureStyle();
    ensureStageFields();
    ensureStepsBox();
    initSelect2ForTaskDrawer();

    return true;
  }

  function boot() {
    patchOpenEventContext();

    let tries = 0;
    const timer = window.setInterval(function () {
      tries += 1;
      if (patchPersonalTasksUI() || tries > 80) {
        window.clearInterval(timer);
      }
    }, 100);

    document.addEventListener("click", function (event) {
      const remove = event.target.closest("[data-remove-step]");
      if (remove) {
        event.preventDefault();
        const row = remove.closest("[data-pt-step-row]");
        if (row) {
          row.remove();
          renumberStepRows();
        }
      }
    }, true);
  }

  ready(boot);
})();
/*
 * Kanban Notes Substage Context Fix
 * Put in: public/js/kanban-notes-substage-context-fix.js
 * Load AFTER public/js/kanban.js
 *
 * Fixes:
 * - Notes drawer opens from cards inside the Unterphasen sidebar.
 * - Notes drawer appears above the Unterphasen sidebar.
 * - Notes save with main LeadStage + optional Unterphase context.
 * - Notes list shows nice badges for stage/sub-stage context.
 */
(function () {
  "use strict";

  if (window.__KANBAN_NOTES_SUBSTAGE_CONTEXT_FIX__) return;
  window.__KANBAN_NOTES_SUBSTAGE_CONTEXT_FIX__ = true;

  const qs = (sel, root = document) => root.querySelector(sel);
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  function esc(value) {
    return String(value ?? "").replace(/[&<>\"']/g, function (m) {
      return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" })[m];
    });
  }

  function csrf() {
    return qs('meta[name="csrf-token"]')?.content || "";
  }

  function shortNum(value) {
    const n = Number(value || 0);
    if (n < 1000) return String(n);
    if (n < 1000000) return (n / 1000).toFixed(n % 1000 ? 1 : 0).replace(/\.0$/, "") + "k";
    return (n / 1000000).toFixed(n % 1000000 ? 1 : 0).replace(/\.0$/, "") + "M";
  }

  function app() {
    return window.APP || window.KanbanAPP || window.LeadUI?.APP || {};
  }

  function canonicalStage(value) {
    const raw = String(value || "").toLowerCase();
    const A = app();
    if (window.LeadUI?.utils?.canonicalStage) return window.LeadUI.utils.canonicalStage(raw);
    const alias = A.stageAlias || {
      open: "lead",
      new: "lead",
      neue: "lead",
      angebot: "offer",
      nachfassen: "follow_up",
      annehmen: "accepted",
      annemen: "accepted",
      angenommen: "accepted",
      auftrag: "deal",
      montage: "project",
      abschluss: "completed",
      archiv: "archive",
      reject: "junk",
      rejeck: "junk",
    };
    return alias[raw] || raw || "lead";
  }

  function stageMetaForKey(stageKey) {
    const key = canonicalStage(stageKey || "lead");
    const A = app();
    return (
      A.companyKanbanStageMeta?.[key] ||
      A.kanbanStageMeta?.[key] ||
      A.stageMeta?.[key] ||
      {}
    );
  }

  function stageNameForKey(stageKey) {
    const key = canonicalStage(stageKey || "lead");
    const A = app();
    return (
      A.companyKanbanStageNames?.[key] ||
      A.kanbanStageNames?.[key] ||
      A.stageNames?.[key] ||
      stageMetaForKey(key)?.name ||
      key
    );
  }

  function normalizeSubStages(meta) {
    return Array.isArray(meta?.sub_stages)
      ? meta.sub_stages
      : (Array.isArray(meta?.subStages) ? meta.subStages : []);
  }

  function subStageForId(stageKey, subStageId) {
    if (!subStageId) return null;
    const subs = normalizeSubStages(stageMetaForKey(stageKey));
    return subs.find((sub) => String(sub?.id) === String(subStageId)) || null;
  }

  function getContextFromButton(btn) {
    const wrapper = btn?.closest?.(".card, tr.list-row-item, tr") || null;
    const card = wrapper?.classList?.contains("card") ? wrapper : null;
    const row = wrapper?.tagName === "TR" ? wrapper : null;
    const zone = card?.closest?.("[data-understage-dropzone]") || null;
    const insideUnderstage = !!(zone || card?.closest?.("#kbUnderstageBoard") || card?.closest?.(".kb-understage-sidebar"));

    const stageKey = canonicalStage(
      btn?.dataset?.leadStageKey ||
      zone?.dataset?.stageKey ||
      card?.dataset?.companyStage ||
      card?.dataset?.stage ||
      row?.dataset?.companyStage ||
      row?.dataset?.stage ||
      card?.closest?.(".column")?.id ||
      row?.dataset?.columnStage ||
      app().underStage?.stageKey ||
      "lead"
    );

    const stageMeta = stageMetaForKey(stageKey);
    const stageId =
      btn?.dataset?.leadStageId ||
      stageMeta?.id ||
      stageMeta?.stage_id ||
      "";

    let subStageId = "";
    if (insideUnderstage) {
      subStageId =
        btn?.dataset?.leadStageSubStageId ||
        zone?.dataset?.subStageId ||
        card?.dataset?.leadStageSubStageId ||
        card?.dataset?.subStageId ||
        "";
    } else {
      subStageId = btn?.dataset?.leadStageSubStageId || "";
    }

    const subStage = subStageForId(stageKey, subStageId);

    const customerId =
      btn?.dataset?.customer ||
      btn?.dataset?.customerId ||
      wrapper?.dataset?.customerId ||
      "";

    const alternativeId =
      btn?.dataset?.alt ||
      btn?.dataset?.alternativeId ||
      wrapper?.dataset?.alternativeId ||
      "";

    const productId =
      btn?.dataset?.product ||
      btn?.dataset?.productId ||
      wrapper?.dataset?.productId ||
      "";

    const leadProductListId =
      btn?.dataset?.leadProductListId ||
      btn?.dataset?.leadProductId ||
      wrapper?.dataset?.leadProductListId ||
      wrapper?.dataset?.leadProductId ||
      (card?.id ? String(card.id).replace(/^card-/, "") : "") ||
      "";

    const customerName =
      btn?.dataset?.customerName ||
      wrapper?.dataset?.customerName ||
      wrapper?.querySelector?.(".card-name")?.textContent?.trim() ||
      wrapper?.querySelector?.(".customer-link")?.textContent?.trim() ||
      "Kunde";

    const productName =
      btn?.dataset?.productName ||
      wrapper?.dataset?.productName ||
      wrapper?.dataset?.productStageName ||
      wrapper?.dataset?.initial ||
      wrapper?.querySelector?.(".product-name")?.textContent?.trim() ||
      wrapper?.querySelector?.(".product_circle")?.textContent?.trim() ||
      "";

    return {
      customer_id: customerId,
      alternative_id: alternativeId,
      product_id: productId,
      lead_product_list_id: leadProductListId,
      customer_name: customerName,
      product_name: productName,
      lead_stage_id: stageId || "",
      lead_stage_key: stageKey,
      lead_stage_name: stageNameForKey(stageKey),
      lead_stage_color: stageMeta?.color || "#74b2d4",
      lead_stage_sub_stage_id: subStageId || "",
      lead_stage_sub_stage_name: subStage?.name || "",
      lead_stage_sub_stage_color: subStage?.color || "#93c21c",
      is_understage_context: insideUnderstage,
    };
  }

  function injectStyle() {
    if (qs("#kanban-notes-substage-context-style")) return;
    const style = document.createElement("style");
    style.id = "kanban-notes-substage-context-style";
    style.textContent = `
      #notesBackdrop { z-index: 560000 !important; }
      #notesDrawer, .notes-drawer { z-index: 560010 !important; }
      body.kb-notes-context-open #kbUnderstageSidebar { z-index: 540000 !important; }
      body.kb-notes-context-open #kbUnderstageSidebarBackdrop { z-index: 539990 !important; }
      #pt-backdrop, .pt-backdrop, .ptx-backdrop { z-index: 570000 !important; }
      #pt-drawer, .pt-drawer, .pt-lucide-drawer, .ptx-drawer { z-index: 570010 !important; }
      .select2-container--open { z-index: 580000 !important; }

      .notes-context-badges {
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:7px;
        margin:10px 0 0;
      }
      .notes-context-badge,
      .note-stage-badge {
        display:inline-flex;
        align-items:center;
        gap:7px;
        min-height:28px;
        padding:0 10px;
        border-radius:999px;
        border:1px solid #dbeafe;
        background:#fff;
        color:#0f172a;
        font-size:11px;
        font-weight:950;
        line-height:1;
        white-space:nowrap;
      }
      .notes-context-badge i,
      .note-stage-badge i {
        width:9px;
        height:9px;
        border-radius:999px;
        display:inline-block;
        flex:0 0 auto;
      }
      .notes-context-badge.is-substage,
      .note-stage-badge.is-substage {
        background:#f7fbef;
        border-color:rgba(147,194,28,.35);
      }
      .notes-context-badge.is-main-note {
        background:#eef7fb;
        border-color:rgba(116,178,212,.35);
      }
      .note-stage-line {
        display:flex;
        flex-wrap:wrap;
        gap:6px;
        margin:0 0 8px;
      }
      .note-bubble-body + .note-stage-line,
      .note-stage-line + .note-bubble-body {
        margin-top:6px;
      }
    `;
    document.head.appendChild(style);
  }

  function ensureNoteContextFields() {
    const form = qs("#notesForm");
    if (!form) return;
    const fields = [
      "notesLeadStageId",
      "notesLeadStageKey",
      "notesLeadStageName",
      "notesLeadStageColor",
      "notesLeadStageSubStageId",
      "notesLeadStageSubStageName",
      "notesLeadStageSubStageColor",
    ];
    fields.forEach((id) => {
      if (qs("#" + id)) return;
      const input = document.createElement("input");
      input.type = "hidden";
      input.id = id;
      input.name = id.replace(/^notes/, "").replace(/[A-Z]/g, (m) => "_" + m.toLowerCase()).replace(/^_/, "");
      form.appendChild(input);
    });
  }

  function setHiddenContext(context) {
    ensureNoteContextFields();
    const map = {
      notesLeadStageId: context.lead_stage_id,
      notesLeadStageKey: context.lead_stage_key,
      notesLeadStageName: context.lead_stage_name,
      notesLeadStageColor: context.lead_stage_color,
      notesLeadStageSubStageId: context.lead_stage_sub_stage_id,
      notesLeadStageSubStageName: context.lead_stage_sub_stage_name,
      notesLeadStageSubStageColor: context.lead_stage_sub_stage_color,
    };
    Object.entries(map).forEach(([id, value]) => {
      const el = qs("#" + id);
      if (el) el.value = value || "";
    });

    const drawer = qs("#notesDrawer");
    if (drawer) {
      Object.entries(context).forEach(([key, value]) => {
        drawer.dataset[key.replace(/_([a-z])/g, (_, c) => c.toUpperCase())] = value || "";
      });
    }
  }

  function renderContextBadge(context) {
    const stage = context.lead_stage_name || context.lead_stage_key || "Hauptphase";
    const sub = context.lead_stage_sub_stage_name || "";
    return `
      <div class="notes-context-badges" id="notesStageContextBadge">
        <span class="notes-context-badge" title="Hauptphase">
          <i style="background:${esc(context.lead_stage_color || "#74b2d4")}"></i>
          Hauptphase: ${esc(stage)}
        </span>
        ${sub ? `
          <span class="notes-context-badge is-substage" title="Unterphase">
            <i style="background:${esc(context.lead_stage_sub_stage_color || "#93c21c")}"></i>
            Unterphase: ${esc(sub)}
          </span>` : `
          <span class="notes-context-badge is-main-note" title="Keine Unterphase">
            <i style="background:#64748b"></i>
            Notiz zur Hauptphase
          </span>`}
      </div>`;
  }

  function updateDrawerContextBadge(context) {
    const title = qs("#notesTitle");
    if (!title) return;
    let badge = qs("#notesStageContextBadge");
    const html = renderContextBadge(context);
    if (badge) {
      badge.outerHTML = html;
      return;
    }
    title.insertAdjacentHTML("afterend", html);
  }

  function noteStageBadges(note) {
    const stageName =
      note.lead_stage_name ||
      note.stage_name ||
      note.stage_context?.lead_stage_name ||
      note.stage ||
      "";
    const stageColor =
      note.lead_stage_color ||
      note.stage_color ||
      note.stage_context?.lead_stage_color ||
      "#74b2d4";
    const subStageName =
      note.lead_stage_sub_stage_name ||
      note.sub_stage_name ||
      note.stage_context?.lead_stage_sub_stage_name ||
      "";
    const subStageColor =
      note.lead_stage_sub_stage_color ||
      note.sub_stage_color ||
      note.stage_context?.lead_stage_sub_stage_color ||
      "#93c21c";

    if (!stageName && !subStageName) return "";

    return `
      <div class="note-stage-line">
        ${stageName ? `<span class="note-stage-badge"><i style="background:${esc(stageColor)}"></i>${esc(stageName)}</span>` : ""}
        ${subStageName ? `<span class="note-stage-badge is-substage"><i style="background:${esc(subStageColor)}"></i>${esc(subStageName)}</span>` : ""}
      </div>`;
  }

  function creatorName(note) {
    const c = note.creator || note.author || {};
    return [c.lastname, c.name].filter(Boolean).join(" ").trim() || c.name || note.created_by_name || "Mitarbeiter";
  }

  function creatorImage(note) {
    const c = note.creator || note.author || {};
    const raw = c.image || c.image_url || "";
    if (!raw) return "/images/employee/noimage.png";
    if (/^https?:\/\//i.test(raw) || raw.startsWith("/")) return raw;
    return "/images/employee/" + String(raw).replace(/^\/+/, "");
  }

  function renderNote(note) {
    const currentEmployeeId = String(window.KANBAN_BOOT?.authEmployeeId || window.KANBAN_BOOT?.authUserId || app().authUserId || "");
    const me = currentEmployeeId && String(note.created_by || note.creator?.id || "") === currentEmployeeId;
    const when = note.created_at ? new Date(note.created_at).toLocaleString("de-DE") : "";
    const body = `
      <div class="note-bubble ${me ? "me" : "other"}">
        ${noteStageBadges(note)}
        <div class="note-bubble-body" data-note-body>${note.description || ""}</div>
        <div class="note-meta"><span class="note-meta-author">${esc(creatorName(note))}</span><span class="note-meta-sep">•</span><span class="note-meta-time">${esc(when)}</span></div>
        ${me ? `<div class="note-actions"><button type="button" class="note-action note-action-edit" data-note-edit data-note-id="${esc(note.id)}"><i class="feather icon-edit-2"></i></button><button type="button" class="note-action note-action-delete" data-note-delete data-note-id="${esc(note.id)}"><i class="feather icon-trash-2"></i></button></div>` : ""}
      </div>`;

    return `<div class="note-row ${me ? "me" : "other"}" data-note-id="${esc(note.id || "")}">${me ? body + `<img class="note-avatar" src="${esc(creatorImage(note))}" alt="">` : `<img class="note-avatar" src="${esc(creatorImage(note))}" alt="">` + body}</div>`;
  }

  function buildContextParams(context, perPage = 50) {
    const params = new URLSearchParams();
    params.set("customer_id", context.customer_id || "");
    params.set("alternative_id", context.alternative_id || "");
    params.set("per_page", String(perPage));
    if (context.product_id) params.set("product_id", context.product_id);
    if (context.lead_product_list_id) params.set("lead_product_list_id", context.lead_product_list_id);
    if (context.lead_stage_id) params.set("lead_stage_id", context.lead_stage_id);
    if (context.lead_stage_key) params.set("lead_stage_key", context.lead_stage_key);
    if (context.lead_stage_sub_stage_id) params.set("lead_stage_sub_stage_id", context.lead_stage_sub_stage_id);
    params.set("only_context", "1");
    return params;
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
    try { data = text ? JSON.parse(text) : {}; } catch (_) { data = { message: text || "Ungültige Serverantwort" }; }
    if (!res.ok || data.success === false || data.status === "error") {
      throw new Error(data.message || `HTTP ${res.status}`);
    }
    return data;
  }

  async function reloadNotesForContext(context) {
    const list = qs("#notesList");
    if (!list) return;

    const endpoint = app().endpoints?.notesIndex || window.LeadUI?.APP?.endpoints?.notesIndex || "/customer-notes";
    const params = buildContextParams(context, 50);

    try {
      const payload = await fetchJSON(`${endpoint}?${params.toString()}`);
      const notes = Array.isArray(payload?.notes)
        ? payload.notes
        : (Array.isArray(payload?.data) ? payload.data : (Array.isArray(payload) ? payload : []));
      notes.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
      list.innerHTML = notes.length
        ? notes.map(renderNote).join("")
        : `<div class="text-muted small p-3">Keine Notizen für diesen Stage-Kontext.</div>`;
      list.scrollTop = list.scrollHeight;

      const total = Number(payload?.total ?? notes.length);
      ["notesCountBadge", "tabBadgeNotes"].forEach((id) => {
        const badge = qs("#" + id);
        if (!badge) return;
        badge.dataset.count = String(total);
        badge.textContent = shortNum(total);
        badge.classList.remove("d-none");
      });

      if (window.feather?.replace) window.feather.replace();
    } catch (error) {
      // Keep the original drawer content if the backend does not support the new filters yet.
      console.warn("[Kanban Notes] Context reload failed", error);
    }
  }

  function updateAllMatchingBadges(context, delta) {
    const c = String(context.customer_id || "");
    const a = String(context.alternative_id || "");
    const p = String(context.product_id || "");
    if (!c || !a) return;
    const selectors = [
      `.card[data-customer-id="${CSS.escape(c)}"][data-alternative-id="${CSS.escape(a)}"]${p ? `[data-product-id="${CSS.escape(p)}"]` : ""} .badge-notes`,
      `tr[data-customer-id="${CSS.escape(c)}"][data-alternative-id="${CSS.escape(a)}"]${p ? `[data-product-id="${CSS.escape(p)}"]` : ""} .badge-notes`,
    ];
    document.querySelectorAll(selectors.join(",")).forEach((badge) => {
      const next = Math.max(0, Number(badge.dataset.count || 0) + Number(delta || 0));
      badge.dataset.count = String(next);
      badge.textContent = shortNum(next);
      badge.style.display = next > 0 ? "block" : "none";
    });
  }

  function setContext(context) {
    window.__KANBAN_NOTES_LAST_CONTEXT__ = context;
    setHiddenContext(context);
    updateDrawerContextBadge(context);
    document.body.classList.add("kb-notes-context-open");
  }

  function openOriginalDrawer(context) {
    const notes = window.LeadUI?.notes;
    const title = context.product_name
      ? `Notizen • ${context.customer_name || "Kunde"} • ${context.product_name}`
      : `Notizen • ${context.customer_name || "Kunde"}`;

    if (notes?.openNotesDrawerFor) {
      notes.openNotesDrawerFor(
        context.customer_id,
        context.alternative_id,
        context.product_id,
        title,
        context.lead_product_list_id,
        context.product_name
      );
    } else {
      // Fallback: open the drawer and fill hidden fields if the LeadUI note API is not ready.
      const drawer = qs("#notesDrawer");
      const backdrop = qs("#notesBackdrop");
      if (qs("#notesTitle")) qs("#notesTitle").textContent = title;
      drawer?.classList.add("open");
      backdrop?.classList.add("show");
      document.body.style.overflow = "hidden";
    }

    setTimeout(() => setContext(context), 0);
    setTimeout(() => reloadNotesForContext(context), 120);
  }

  function bindNotesOpenCapture() {
    document.addEventListener("click", function (event) {
      const btn = event.target.closest("[data-open-notes]");
      if (!btn) return;

      const card = btn.closest(".card");
      const insideUnderstage = !!(card?.closest("#kbUnderstageBoard") || card?.closest(".kb-understage-sidebar") || card?.closest("[data-understage-dropzone]"));

      // In the main board, the old handler is fine. In Unterphasen, intercept it
      // so document.getElementById / duplicate card IDs cannot steal the main-board card.
      if (!insideUnderstage) {
        const context = getContextFromButton(btn);
        setTimeout(() => setContext(context), 60);
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      const context = getContextFromButton(btn);
      openOriginalDrawer(context);
    }, true);
  }

  function bindTaskOpenContextCapture() {
    document.addEventListener("click", function (event) {
      const btn = event.target.closest('[data-menu="aufgabe"], [data-open-personal-tasks]');
      if (!btn) return;
      const card = btn.closest(".card");
      if (!card || !card.closest("#kbUnderstageBoard, .kb-understage-sidebar, [data-understage-dropzone]")) return;
      const context = getContextFromButton(btn);
      window.__KANBAN_TASK_LAST_OPEN_CONTEXT__ = window.__KANBAN_TASK_LAST_OPEN_CONTEXT__ || {};
      window.__KANBAN_TASK_LAST_OPEN_CONTEXT__.leadStageContext = {
        lead_stage_id: context.lead_stage_id,
        lead_stage_key: context.lead_stage_key,
        lead_stage_name: context.lead_stage_name,
        lead_stage_color: context.lead_stage_color,
        lead_stage_sub_stage_id: context.lead_stage_sub_stage_id,
        lead_stage_sub_stage_name: context.lead_stage_sub_stage_name,
        lead_stage_sub_stage_color: context.lead_stage_sub_stage_color,
        is_understage_context: context.is_understage_context,
      };
    }, true);
  }

  function bindNotesSubmitOverride() {
    const install = () => {
      const form = qs("#notesForm");
      if (!form || form.dataset.notesSubstageSubmitReady === "1") return;
      form.dataset.notesSubstageSubmitReady = "1";

      form.onsubmit = async function (event) {
        event.preventDefault();
        event.stopPropagation();

        const context = window.__KANBAN_NOTES_LAST_CONTEXT__ || {};
        const editorHTML = (function () {
          if (window.Quill && window.noteQuill?.root) return window.noteQuill.root.innerHTML.trim();
          const editor = qs("#noteEditor .ql-editor");
          if (editor) return editor.innerHTML.trim();
          return (qs("#noteText")?.value || "").trim();
        })();

        const text = editorHTML && editorHTML !== "<p><br></p>" ? editorHTML : "";
        if (!text) return;

        const payload = {
          customer_id: Number(qs("#notesCustomerId")?.value || context.customer_id || 0),
          alternative_id: Number(qs("#notesAlternativeId")?.value || context.alternative_id || 0),
          product_id: (qs("#notesProductId")?.value || context.product_id) ? Number(qs("#notesProductId")?.value || context.product_id) : null,
          lead_product_list_id: (qs("#notesLeadProductListId")?.value || context.lead_product_list_id) ? Number(qs("#notesLeadProductListId")?.value || context.lead_product_list_id) : null,
          description: text,
          stage: context.lead_stage_key || qs("#notesLeadStageKey")?.value || "",
          lead_stage_id: (context.lead_stage_id || qs("#notesLeadStageId")?.value) ? Number(context.lead_stage_id || qs("#notesLeadStageId")?.value) : null,
          lead_stage_key: context.lead_stage_key || qs("#notesLeadStageKey")?.value || "",
          lead_stage_name: context.lead_stage_name || qs("#notesLeadStageName")?.value || "",
          lead_stage_color: context.lead_stage_color || qs("#notesLeadStageColor")?.value || "#74b2d4",
          lead_stage_sub_stage_id: (context.lead_stage_sub_stage_id || qs("#notesLeadStageSubStageId")?.value) ? Number(context.lead_stage_sub_stage_id || qs("#notesLeadStageSubStageId")?.value) : null,
          lead_stage_sub_stage_name: context.lead_stage_sub_stage_name || qs("#notesLeadStageSubStageName")?.value || "",
          lead_stage_sub_stage_color: context.lead_stage_sub_stage_color || qs("#notesLeadStageSubStageColor")?.value || "#93c21c",
          type: (context.lead_stage_sub_stage_id || qs("#notesLeadStageSubStageId")?.value) ? "sub_stage_note" : "stage_note",
        };

        const endpoint = app().endpoints?.notesStore || window.LeadUI?.APP?.endpoints?.notesStore || "/customer-notes";

        try {
          const data = await fetchJSON(endpoint, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrf(),
              "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify(payload),
          });
          const note = data.note || data.data || data;
          const list = qs("#notesList");
          if (list) {
            const empty = list.querySelector(".text-muted.small.p-3");
            if (empty) empty.remove();
            list.insertAdjacentHTML("beforeend", renderNote(note));
            list.scrollTop = list.scrollHeight;
          }

          const editor = qs("#noteEditor .ql-editor");
          if (editor) editor.innerHTML = "";
          if (qs("#noteText")) qs("#noteText").value = "";
          updateAllMatchingBadges(context, +1);
          if (window.feather?.replace) window.feather.replace();
        } catch (error) {
          if (window.Swal) window.Swal.fire("Fehler", error.message || "Notiz konnte nicht gespeichert werden.", "error");
          else alert(error.message || "Notiz konnte nicht gespeichert werden.");
        }
      };
    };

    install();
    document.addEventListener("DOMContentLoaded", install);
    const observer = new MutationObserver(install);
    observer.observe(document.documentElement, { childList: true, subtree: true });
  }

  function bindCloseCleanup() {
    document.addEventListener("click", function (event) {
      if (event.target.closest("#notesBackdrop, [data-notes-close]")) {
        document.body.classList.remove("kb-notes-context-open");
      }
    }, true);
  }

  injectStyle();
  ensureNoteContextFields();
  bindNotesOpenCapture();
  bindTaskOpenContextCapture();
  bindNotesSubmitOverride();
  bindCloseCleanup();
})();
