import axios from "axios";

/**
 * IDS Listener
 *
 * This file now handles BOTH:
 * 1) old IDS import listener:
 *      channel: ids
 *      event: .IdsItemsImported
 *
 * 2) offer supplier return listener:
 *      private channel: offer-folder.{folderId}
 *      event: .supplier.products.imported
 *
 * Important:
 * - Do NOT create a new Echo instance here.
 * - app.js already creates window.Echo.
 * - This file only subscribes/listens.
 */

// ------------------------------------------------------
// Shared boot helpers
// ------------------------------------------------------
function waitForEcho(callback, tries = 0) {
    if (window.Echo) {
        callback();
        return;
    }

    if (tries < 60) {
        setTimeout(() => waitForEcho(callback, tries + 1), 250);
        return;
    }

    console.error("❌ Echo not loaded after waiting. window.Echo is undefined.");
}

function safeNumber(value, fallback = 0) {
    const number = Number(value);
    return Number.isFinite(number) ? number : fallback;
}

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

// ------------------------------------------------------
// 1) Helper: Fetch Form Options (Brands/Groups)
// ------------------------------------------------------
async function fetchPromoteOptions() {
    try {
        const response = await axios.get("/ids/promote-options");
        return response.data;
    } catch (error) {
        console.warn(
            "⚠️ Could not fetch promote options. Ensure the route '/ids/promote-options' exists and returns { brands, articleGroups }.",
            error
        );
        return { brands: [], articleGroups: [] };
    }
}

// ------------------------------------------------------
// 2) Helper: Fetch Sub-Groups
// ------------------------------------------------------
async function fetchSubGroups(groupId) {
    if (!groupId) return [];

    try {
        const response = await axios.get(`/article-groups/${groupId}/sub-groups`);
        return response.data;
    } catch (error) {
        console.error("Error fetching sub-groups:", error);
        return [];
    }
}

// ------------------------------------------------------
// 3) Old IDS modal HTML
// ------------------------------------------------------
function ensureIdsModalExists() {
    if (document.getElementById("idsModalOverlay")) return;

    const modalHtml = `
        <div id="idsModalOverlay" style="display:none;">
            <div id="idsModal">
                <div class="ids-modal-header">
                    <h2 id="idsModalTitle">IDS Artikel importiert</h2>
                    <span id="idsCloseBtn">&times;</span>
                </div>
                <div id="idsModalContent"></div>
            </div>
        </div>
        <style>
            #idsModalOverlay {
                position: fixed; inset: 0; background: rgba(0,0,0,0.6);
                display: flex; justify-content: center; align-items: center; z-index: 9999;
                backdrop-filter: blur(2px);
            }
            #idsModal {
                background: white; border-radius: 12px; width: 600px; max-width: 95%;
                box-shadow: 0 20px 25px -5px rgba(0,0,0,.1), 0 10px 10px -5px rgba(0,0,0,.04);
                overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;
            }
            .ids-modal-header {
                padding: 16px 24px; border-bottom: 1px solid #e5e7eb;
                display: flex; justify-content: space-between; align-items: center; background: #f9fafb;
            }
            #idsModalTitle { margin: 0; font-size: 18px; font-weight: 600; color: #111827; }
            #idsCloseBtn { cursor: pointer; font-size: 24px; color: #6b7280; line-height: 1; }
            #idsCloseBtn:hover { color: #111827; }
            #idsModalContent { padding: 24px; overflow-y: auto; }

            .idsItem { border-bottom: 1px solid #f3f4f6; padding: 12px 0; }
            .idsItem:last-child { border-bottom: none; }
            .idsItem strong { color: #1f2937; }

            .ids-form-group { margin-bottom: 16px; }
            .ids-form-label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px; }
            .ids-form-select {
                width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;
                background-color: #fff; font-size: 14px; color: #111827;
                transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            }
            .ids-form-select:focus {
                border-color: #3b82f6;
                outline: none;
                box-shadow: 0 0 0 3px rgba(59,130,246,.18);
            }
            .ids-btn-primary {
                width: 100%; display: inline-flex; justify-content: center; align-items: center;
                padding: 10px 16px; border: none; border-radius: 6px;
                background-color: #2563eb; color: white; font-weight: 500; font-size: 14px;
                cursor: pointer; transition: background-color .2s; margin-top: 8px;
            }
            .ids-btn-primary:hover { background-color: #1d4ed8; }
            .ids-btn-primary:disabled { background-color: #9ca3af; cursor: not-allowed; }
            .ids-loading { text-align: center; color: #6b7280; font-size: 14px; padding: 20px; }

            @keyframes idsSupplierToastIn {
                from { opacity:0; transform:translateY(-8px); }
                to { opacity:1; transform:translateY(0); }
            }

            .fresh-supplier-row {
                animation: freshSupplierPulse 3.8s ease;
            }

            @keyframes freshSupplierPulse {
                0% { box-shadow: inset 0 0 0 9999px rgba(147,194,28,.28); }
                60% { box-shadow: inset 0 0 0 9999px rgba(147,194,28,.10); }
                100% { box-shadow: inset 0 0 0 9999px rgba(147,194,28,0); }
            }
        </style>
    `;

    document.body.insertAdjacentHTML("beforeend", modalHtml);

    const closeBtn = document.getElementById("idsCloseBtn");
    if (closeBtn) {
        closeBtn.onclick = closeIdsModal;
    }
}

function closeIdsModal() {
    const overlay = document.getElementById("idsModalOverlay");
    if (overlay) overlay.style.display = "none";
}

// ------------------------------------------------------
// 4) Old IDS result loader
// ------------------------------------------------------
function loadIdsResults(batchId) {
    const autoPromoteCheckbox = document.getElementById("gcAutoPromote");
    const isAutoPromote = autoPromoteCheckbox && autoPromoteCheckbox.checked;

    axios
        .get(`/ids/results/${batchId}`)
        .then(async (res) => {
            const items = res.data;
            if (!items || items.length === 0) return;

            ensureIdsModalExists();

            const contentDiv = document.getElementById("idsModalContent");
            const titleEl = document.getElementById("idsModalTitle");
            const overlay = document.getElementById("idsModalOverlay");

            if (!contentDiv || !titleEl || !overlay) return;

            overlay.style.display = "flex";

            if (isAutoPromote) {
                titleEl.textContent = "Produkte kategorisieren & speichern";
                contentDiv.innerHTML = '<div class="ids-loading">Lade Optionen...</div>';

                const options = await fetchPromoteOptions();
                renderPromoteForm(contentDiv, items, options);
            } else {
                titleEl.textContent = "IDS Artikel importiert";

                let html = "";
                items.forEach((i) => {
                    html += `
                        <div class="idsItem">
                            <strong>${escapeHtml(i.article_no)}</strong><br>
                            ${escapeHtml(i.short_text || "")}<br>
                            <span style="font-size:.85em; color:#6b7280;">
                                ${escapeHtml(i.qty)} ${escapeHtml(i.unit)}
                            </span>
                        </div>`;
                });

                contentDiv.innerHTML = html;
            }
        })
        .catch((err) => console.error("IDS load error:", err));
}

// ------------------------------------------------------
// 5) Old IDS promote form
// ------------------------------------------------------
function renderPromoteForm(container, items, { brands, articleGroups }) {
    const brandOptions = (brands || [])
        .map((b) => `<option value="${escapeHtml(b.id)}">${escapeHtml(b.name)}</option>`)
        .join("");

    const groupOptions = (articleGroups || [])
        .map((g) => `<option value="${escapeHtml(g.id)}">${escapeHtml(g.article_group)}</option>`)
        .join("");

    const html = `
        <div style="margin-bottom:20px; padding:12px; background:#eff6ff; border-radius:6px; color:#1e40af; font-size:14px;">
            <strong>${items.length} Artikel importiert.</strong><br>
            Bitte wähle die globalen Eigenschaften für diese Artikel aus.
        </div>

        <form id="idsPromoteForm">
            <div class="ids-form-group">
                <label class="ids-form-label">Hersteller / Marke</label>
                <select id="idsBrandSelect" class="ids-form-select" required>
                    <option value="">Bitte wählen...</option>
                    ${brandOptions}
                </select>
            </div>

            <div class="ids-form-group">
                <label class="ids-form-label">Artikelgruppe</label>
                <select id="idsGroupSelect" class="ids-form-select" required>
                    <option value="">Bitte wählen...</option>
                    ${groupOptions}
                </select>
            </div>

            <div class="ids-form-group">
                <label class="ids-form-label">Untergruppe</label>
                <select id="idsSubGroupSelect" class="ids-form-select" disabled>
                    <option value="">Erst Artikelgruppe wählen...</option>
                </select>
            </div>

            <button type="submit" id="idsPromoteBtn" class="ids-btn-primary">
                Speichern & Artikel anlegen
            </button>
        </form>
    `;

    container.innerHTML = html;

    const groupSelect = document.getElementById("idsGroupSelect");
    const subGroupSelect = document.getElementById("idsSubGroupSelect");
    const form = document.getElementById("idsPromoteForm");

    if (!groupSelect || !subGroupSelect || !form) return;

    groupSelect.addEventListener("change", async function () {
        const groupId = this.value;

        subGroupSelect.innerHTML = '<option value="">Lade...</option>';
        subGroupSelect.disabled = true;

        if (groupId) {
            const subGroups = await fetchSubGroups(groupId);

            if (subGroups.length > 0) {
                subGroupSelect.innerHTML =
                    '<option value="">Bitte wählen (optional)...</option>' +
                    subGroups
                        .map((sg) => `<option value="${escapeHtml(sg.id)}">${escapeHtml(sg.sub_article)}</option>`)
                        .join("");

                subGroupSelect.disabled = false;
            } else {
                subGroupSelect.innerHTML = '<option value="">Keine Untergruppen verfügbar</option>';
            }
        } else {
            subGroupSelect.innerHTML = '<option value="">Erst Artikelgruppe wählen...</option>';
        }
    });

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const brandId = document.getElementById("idsBrandSelect")?.value;
        const groupId = document.getElementById("idsGroupSelect")?.value;
        const subGroupId = document.getElementById("idsSubGroupSelect")?.value;
        const btn = document.getElementById("idsPromoteBtn");

        if (!brandId || !groupId) {
            alert("Bitte Hersteller und Artikelgruppe wählen.");
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.textContent = "Speichere...";
        }

        try {
            const promises = items.map((item) => {
                return axios.post(`/ids/promote/${item.id}`, {
                    brand_id: brandId,
                    article_group_id: groupId,
                    sub_article_group_id: subGroupId || null,
                    product_name: item.short_text || item.article_no,
                    measure_unit: item.unit,
                });
            });

            await Promise.all(promises);

            container.innerHTML = `
                <div style="text-align:center; padding:40px 0;">
                    <div style="color:#10b981; font-size:48px; margin-bottom:16px;">✓</div>
                    <h3 style="margin:0; color:#111827;">Erfolgreich gespeichert!</h3>
                    <p style="color:#6b7280;">${items.length} Artikel wurden angelegt/aktualisiert.</p>
                    <button class="ids-btn-primary" onclick="document.getElementById('idsModalOverlay').style.display='none'">Schließen</button>
                </div>
            `;
        } catch (error) {
            console.error("Batch promote error:", error);
            alert("Fehler beim Speichern. Bitte Konsole prüfen.");

            if (btn) {
                btn.disabled = false;
                btn.textContent = "Speichern & Artikel anlegen";
            }
        }
    });
}

// ------------------------------------------------------
// 6) NEW: Offer supplier Reverb listener
// ------------------------------------------------------
function initializeOfferSupplierListener() {
    const config = window.OfferSupplierConfig || {};
    const folderId = safeNumber(config.folderId);

    if (!folderId) {
        console.log("[IDS Listener] No OfferSupplierConfig.folderId found. Offer supplier listener skipped on this page.");
        return;
    }

    if (window.__offerSupplierIdsListenerStarted === folderId) {
        console.log("[IDS Listener] Offer supplier listener already started for folder:", folderId);
        return;
    }

    window.__offerSupplierIdsListenerStarted = folderId;

    console.log("[IDS Listener] Listening on private channel offer-folder." + folderId);

    window.Echo.private("offer-folder." + folderId)
        .listen(".supplier.products.imported", (payload) => {
            console.log("📦 OFFER SUPPLIER EVENT RECEIVED:", payload);
            handleOfferSupplierProductsImported(payload);
        })
        .error((err) => {
            console.error("❌ Offer supplier channel error:", err);
        });

    initializeOfferSupplierFallbacks(folderId);
}

function initializeOfferSupplierFallbacks(folderId) {
    if (window.__offerSupplierFallbackStarted === folderId) {
        return;
    }

    window.__offerSupplierFallbackStarted = folderId;

    window.addEventListener("message", function (event) {
        if (event.origin !== window.location.origin) return;

        if (event.data?.type === "offer_supplier_import_done") {
            handleOfferSupplierProductsImported(event.data);
        }
    });

    window.addEventListener("storage", function (event) {
        if (event.key !== "offer_supplier_import_" + folderId) return;

        try {
            handleOfferSupplierProductsImported(JSON.parse(event.newValue || "{}"));
        } catch (error) {
            console.error("[IDS Listener] Could not parse supplier localStorage payload:", error);
        }
    });
}

function handleOfferSupplierProductsImported(payload) {
    const currentFolderId = safeNumber(window.OfferSupplierConfig?.folderId);
    const eventFolderId = safeNumber(payload?.folder_id);

    if (currentFolderId && eventFolderId && currentFolderId !== eventFolderId) {
        return;
    }

    const items = Array.isArray(payload?.items) ? payload.items : [];

    if (!items.length) {
        showSupplierToast("Keine Lieferantenartikel empfangen.", "error");
        return;
    }

    if (window.App?.SupplierSearch?.appendItems) {
        window.App.SupplierSearch.appendItems(items, payload.target_section_index);
    } else {
        appendSupplierItemsFallback(items, payload.target_section_index);
    }

    showSupplierToast(payload.message || `${items.length} Lieferantenartikel eingefügt.`, "success");
}

function appendSupplierItemsFallback(items, targetSectionIndex = null) {
    if (typeof State === "undefined") {
        console.warn("[IDS Listener] State not found. Cannot append supplier items.");
        return;
    }

    if (!Array.isArray(State.sections)) {
        State.sections = [];
    }

    let sectionIndex = Number.isInteger(Number(targetSectionIndex))
        ? Number(targetSectionIndex)
        : -1;

    if (
        sectionIndex < 0 ||
        !State.sections[sectionIndex] ||
        State.sections[sectionIndex]._pageBreak ||
        State.sections[sectionIndex]._virtualSection ||
        State.sections[sectionIndex].isLocked
    ) {
        sectionIndex = State.sections.findIndex((section) => {
            return section &&
                !section._pageBreak &&
                !section._virtualSection &&
                !section.isLocked;
        });
    }

    if (sectionIndex < 0) {
        State.sections.push({
            id: "supplier_section_" + Date.now(),
            title: "Lieferantenartikel",
            name: "Lieferantenartikel",
            items: [],
            config: { hidePrices: false },
        });

        sectionIndex = State.sections.length - 1;
    }

    if (!Array.isArray(State.sections[sectionIndex].items)) {
        State.sections[sectionIndex].items = [];
    }

    items.forEach((item) => {
        const node = JSON.parse(JSON.stringify(item));

        node.id = node.id || "supplier_" + Date.now() + "_" + Math.floor(Math.random() * 100000);
        node.active = node.active !== false;
        node.status = node.status || "normal";
        node.kind = node.kind || "article";
        node.item_type = node.item_type || "product";
        node.subItems = Array.isArray(node.subItems) ? node.subItems : [];
        node._freshFromSupplier = true;

        State.sections[sectionIndex].items.push(node);
    });

    if (window.App?.renderQuotePage) {
        window.App.renderQuotePage();
    }

    if (window.App?.ListView?.render) {
        window.App.ListView.render();
    }

    if (typeof window.saveNow === "function") {
        window.saveNow();
    } else if (window.App?.saveNow) {
        window.App.saveNow();
    }
}

function showSupplierToast(message, type = "success") {
    const old = document.getElementById("ids-supplier-toast");
    if (old) old.remove();

    const toast = document.createElement("div");
    toast.id = "ids-supplier-toast";

    const success = type === "success";

    toast.style.cssText = `
        position: fixed;
        right: 24px;
        top: 24px;
        z-index: 999999;
        max-width: 440px;
        padding: 14px 16px;
        border-radius: 18px;
        font-weight: 900;
        font-size: 13px;
        line-height: 1.45;
        box-shadow: 0 18px 45px rgba(15,23,42,.18);
        background: ${success ? "#ecfdf5" : "#fef2f2"};
        color: ${success ? "#047857" : "#b91c1c"};
        border: 1px solid ${success ? "#bbf7d0" : "#fecaca"};
        transform: translateY(-8px);
        opacity: 0;
        transition: all .22s ease;
        animation: idsSupplierToastIn .22s ease;
    `;

    toast.innerHTML = `
        <div style="display:flex;align-items:flex-start;gap:10px;">
            <span>${success ? "✅" : "⚠️"}</span>
            <span>${escapeHtml(message)}</span>
        </div>
    `;

    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.transform = "translateY(0)";
        toast.style.opacity = "1";
    });

    setTimeout(() => {
        toast.style.transform = "translateY(-8px)";
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 250);
    }, 4500);
}

// ------------------------------------------------------
// 7) Old IDS public channel listener
// ------------------------------------------------------
function initializeOldIdsBatchListener() {
    if (window.__oldIdsBatchListenerStarted) {
        console.log("[IDS Listener] Old IDS batch listener already started.");
        return;
    }

    window.__oldIdsBatchListenerStarted = true;

    console.log("[IDS Listener] Listening on public channel ids");

    window.Echo.channel("ids")
        .listen(".IdsItemsImported", (e) => {
            console.log("📦 IDS EVENT RECEIVED:", e);

            if (e.batchId) {
                loadIdsResults(e.batchId);
            }
        })
        .error((err) => {
            console.error("❌ IDS channel error:", err);
        });
}

// ------------------------------------------------------
// 8) Main export
// ------------------------------------------------------
export default function initializeIdsListener() {
    console.log("🔵 Initializing IDS Listener…");

    waitForEcho(() => {
        initializeOldIdsBatchListener();
        initializeOfferSupplierListener();
    });
}
