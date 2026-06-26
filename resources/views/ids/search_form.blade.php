@extends('admin.layouts.app')

@section('title', 'GC Online Artikelsuche')

@section('content')

<style>
    .gc-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 32px 24px 48px;
        font-family: system-ui, Arial, sans-serif;
        color: #2d3748;
    }

    .gc-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 6px;
    }

    .gc-subtext {
        font-size: 14px;
        color: #4a5568;
        margin-bottom: 22px;
        line-height: 1.6;
    }

    .gc-box {
        background: #ffffff;
        padding: 20px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }

    .gc-input-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .gc-input {
        flex: 1 1 220px;
        padding: 11px 14px;
        border: 1px solid #cbd5e0;
        border-radius: 10px;
        font-size: 15px;
        outline: none;
        transition: border-color .25s ease;
    }

    .gc-input:focus {
        border-color: #4299e1;
    }

    .gc-btn {
        flex: 0 0 auto;
        background: #74b2d4;
        color: white;
        padding: 11px 20px;
        border-radius: 10px;
        border: none;
        font-size: 15px;
        cursor: pointer;
        transition: background .2s;
        white-space: nowrap;
    }

    .gc-btn:hover {
        background: #225ea8;
    }

    .gc-note {
        margin-top: 10px;
        font-size: 13px;
        color: #6b7280;
    }

    /* Search history */
    .gc-history {
        margin-top: 14px;
        border-top: 1px dashed #e5e7eb;
        padding-top: 10px;
    }

    .gc-history-header {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .gc-history-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .gc-history-chip {
        border: none;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        background: #f3f4f6;
        color: #374151;
        cursor: pointer;
        max-width: 220px;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }

    .gc-history-chip:hover {
        background: #e5e7eb;
    }

    /* Local results */
    .gc-local-results {
        margin-top: 16px;
        border-top: 1px solid #e5e7eb;
        padding-top: 10px;
    }

    .gc-local-header {
        font-size: 12px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .gc-local-list {
        max-height: 220px;
        overflow-y: auto;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .gc-local-item {
        padding: 8px 10px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 13px;
    }

    .gc-local-item:last-child {
        border-bottom: none;
    }

    .gc-local-item strong {
        font-size: 13px;
        color: #111827;
    }

    .gc-local-item small {
        font-size: 11px;
        color: #6b7280;
    }

    .gc-local-empty {
        padding: 8px 10px;
        font-size: 12px;
        color: #9ca3af;
    }

    /* Drawer for IDS results (remote) */
    #gcDrawer {
        position: fixed;
        top: 0;
        right: -380px;
        width: 380px;
        height: 100vh;
        background: #ffffff;
        box-shadow: -4px 0 18px rgba(15,23,42,0.2);
        padding: 18px 18px 24px;
        overflow-y: auto;
        transition: right .35s ease;
        z-index: 99999;
        font-family: system-ui, Arial, sans-serif;
    }

    #gcDrawer.open {
        right: 0;
    }

    #gcDrawer h2 {
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
        color: #111827;
    }

    .drawer-close {
        float: right;
        cursor: pointer;
        font-size: 20px;
        color: #6b7280;
        border: none;
        background: transparent;
    }

    .drawer-item {
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
        font-size: 13px;
    }

    .drawer-item strong {
        font-size: 13px;
        color: #111827;
    }

    .drawer-item small {
        font-size: 12px;
        color: #6b7280;
    }

    .gc-local-badge {
    display: inline-block;
    margin-left: 6px;
    padding: 2px 8px;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
    font-size: 11px;
    font-weight: 600;
}
.gc-local-add-btn {
    border: none;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 12px;
    background: #10b981;
    color: white;
    cursor: pointer;
}
.gc-local-add-btn:hover {
    background: #059669;
}
.gc-local-add-btn[disabled] {
    background: #9ca3af;
    cursor: default;
}


/* History chips */
.gc-history {
    margin-top: 14px;
    border-top: 1px dashed #e5e7eb;
    padding-top: 10px;
}

.gc-history-header {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.gc-history-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.gc-history-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: none;
    border-radius: 999px;
    padding: 4px 8px 4px 10px;
    font-size: 12px;
    background: #f3f4f6;
    color: #374151;
    cursor: pointer;
    max-width: 220px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

.gc-history-chip:hover {
    background: #e5e7eb;
}

.gc-history-chip-text {
    overflow: hidden;
    text-overflow: ellipsis;
}

.gc-history-chip-remove {
    border: none;
    background: transparent;
    color: #9ca3af;
    font-size: 13px;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.gc-history-chip-remove:hover {
    color: #ef4444;
}

/* "Show more" button */
.gc-history-more-btn {
    border: none;
    background: none;
    color: #2563eb;
    font-size: 11px;
    cursor: pointer;
    padding: 0;
}

/* History panel (full list + search) */
#gcHistoryPanelOverlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.4);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99998;
}

#gcHistoryPanelOverlay.open {
    display: flex;
}

#gcHistoryPanel {
    width: 420px;
    max-width: 95%;
    max-height: 420px;
    background: #ffffff;
    border-radius: 14px;
    padding: 16px 18px 18px;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
    font-family: system-ui, Arial, sans-serif;
}

.gc-history-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.gc-history-panel-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.gc-history-panel-close {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 18px;
    color: #6b7280;
}

.gc-history-panel-close:hover {
    color: #111827;
}

.gc-history-panel-search {
    width: 100%;
    padding: 7px 9px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    font-size: 13px;
    margin-bottom: 8px;
}

.gc-history-panel-list {
    max-height: 260px;
    overflow-y: auto;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}

.gc-history-panel-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 9px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 13px;
}

.gc-history-panel-item:last-child {
    border-bottom: none;
}

.gc-history-panel-term {
    flex: 1;
    margin-right: 8px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
}

.gc-history-panel-term:hover {
    color: #2563eb;
}

.gc-history-panel-delete {
    border: none;
    background: transparent;
    color: #9ca3af;
    cursor: pointer;
    font-size: 13px;
}

.gc-history-panel-delete:hover {
    color: #ef4444;
}
.gc-product-preview {
        margin-top: 18px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        padding: 12px;
        display: none; /* hidden until first click */
    }

    .gc-product-preview-header {
        font-size: 13px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .gc-product-preview-close {
        border: none;
        background: transparent;
        font-size: 16px;
        cursor: pointer;
        color: #9ca3af;
    }

    .gc-product-preview-close:hover {
        color: #ef4444;
    }

    .gc-product-iframe {
        width: 100%;
        min-height: 420px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .gc-product-link {
        border: none;
        background: none;
        padding: 0;
        margin: 0;
        font: inherit;
        color: #2563eb;
        cursor: pointer;
        text-decoration: underline;
    }

    .gc-product-link:hover {
        color: #1d4ed8;
    }
     .gc-product-link {
        border: none;
        background: none;
        padding: 0;
        margin: 0;
        font: inherit;
        color: #2563eb;
        cursor: pointer;
        text-decoration: underline;
    }
    .gc-product-link:hover {
        color: #1d4ed8;
    }

    /* Product modal */
    #gcProductModalOverlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 100000;
    }
    #gcProductModalOverlay.open {
        display: flex;
    }

    #gcProductModal {
        width: 900px;
        max-width: 95%;
        max-height: 90vh;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        font-family: system-ui, Arial, sans-serif;
    }

    .gc-product-modal-header {
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .gc-product-modal-close {
        border: none;
        background: transparent;
        font-size: 20px;
        cursor: pointer;
        color: #6b7280;
    }
    .gc-product-modal-close:hover {
        color: #ef4444;
    }

    #gcProductModalFrame {
        border: none;
        width: 100%;
        height: 80vh;
        background: #f9fafb;
    }
</style>

<div class="app-content">
    <div class="content-wrapper"> 

        {{-- Body --}}
        <div class="content-body">
            <div class="gc-wrapper">

                <p class="gc-subtext">
                    Gib einen Suchbegriff ein. GC Online wird in einem eigenen Popup-Fenster geöffnet
                    (wie ein eigener Browser). Diese Seite bleibt offen und zeigt dir nach der Rückgabe
                    die importierten IDS-Artikel rechts im Panel. Darunter siehst du auch bereits
                    vorhandene Treffer aus deiner Datenbank.
                </p>

                <div class="gc-box">
                    <form id="gcSearchForm"
                          method="POST"
                          action="https://gconlineplus.de/ids.aspx"
                          enctype="multipart/form-data">

                        @csrf

                        {{-- IDS Pflichtfelder --}}
                        <input type="hidden" name="action"  value="AS">
                        <input type="hidden" name="version" value="2.5">
                        <input type="hidden" name="target"  value="TOP">

                        {{-- IDS Callback (Server → Laravel) --}}
                        <input type="hidden"
                                    id="gcHookUrl"
                                    name="hookurl"
                                    value="{{ route('ids.callback') }}"
                                    data-base="{{ route('ids.callback') }}">

                                {{-- current user id for callback --}}
                                <input type="hidden"
                                    id="gcHookUid"
                                    value="{{ auth()->id() }}">


                        {{-- Browser-Rücksprung – im GC-Fenster --}}
                        <input type="hidden" name="rueckurl" value="{{ route('ids.search.form') }}">

                        {{-- Login Infos --}}
                        <input type="hidden" name="kndnr"      value="{{ config('services.ids.kndnr') }}">
                        <input type="hidden" name="name_kunde" value="{{ config('services.ids.username') }}">
                        <input type="hidden" name="pw_kunde"   value="{{ config('services.ids.password') }}">

                        <div class="gc-input-row">
                            <input
                                type="text"
                                name="searchterm"
                                id="gcSearchInput"
                                required
                                placeholder="z.B. Waschtisch · Armatur · Artikelnummer"
                                class="gc-input"
                            >
                            <button type="submit" class="gc-btn">
                                In GC Online suchen
                            </button>
                        </div>

                        <p class="gc-note">
                            Das GC-Fenster darf von deinem Browser nicht blockiert werden (Pop-up zulassen).
                        </p>

                        <div style="margin-top:8px; font-size:12px; color:#4b5563;">
                            <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox"
                                    id="gcAutoPromote"
                                    style="accent-color:#10b981;">
                                <span>Ergebnisse automatisch als Produkte + Händlerpreise anlegen</span>
                            </label>
                        </div>


                        {{-- Previous searches --}}
                            <div class="gc-history">
                                <div class="gc-history-header">
                                    <span>Letzte Suchbegriffe</span>
                                    <button type="button"
                                            id="gcHistoryMoreBtn"
                                            class="gc-history-more-btn"
                                            style="display:none;">
                                        Alle anzeigen
                                    </button>
                                </div>
                                <div id="gcHistoryList" class="gc-history-list"></div>
                            </div> 

                        {{-- Local DB results for this search --}}
                            <div class="gc-local-results">
                                <div class="gc-local-header">
                                    <span>Neueste IDS-Treffer</span>
                                    <button type="button"
                                            id="gcLocalRefreshBtn"
                                            style="border:none;background:none;color:#2563eb;font-size:11px;cursor:pointer;padding:0;">
                                        Neu laden
                                    </button>
                                </div>
                                <div id="gcLocalResults" class="gc-local-list">
                                    <div class="gc-local-empty">Noch keine Suche ausgeführt.</div>
                                </div>
                            </div>

                            {{-- NEW: product profile preview --}}
                            <div id="gcProductPreview" class="gc-product-preview">
                                <div class="gc-product-preview-header">
                                    <span>Produktprofil</span>
                                    <button type="button"
                                            class="gc-product-preview-close"
                                            onclick="closeProductPreview()">
                                        ×
                                    </button>
                                </div>
                                <iframe id="gcProductFrame"
                                        class="gc-product-iframe"
                                        src=""
                                        loading="lazy">
                                </iframe>
                            </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Right-side drawer for IDS results (live imported) --}}
<div id="gcDrawer">
    <button class="drawer-close" type="button" onclick="closeGcDrawer()">×</button>
    <h2>Importierte IDS-Artikel</h2>
    <div id="gcDrawerContent"></div>
</div>


{{-- History panel overlay --}}
<div id="gcHistoryPanelOverlay">
    <div id="gcHistoryPanel">
        <div class="gc-history-panel-header">
            <div class="gc-history-panel-title">Alle Suchbegriffe</div>
            <button type="button"
                    class="gc-history-panel-close"
                    onclick="closeHistoryPanel()">
                ×
            </button>
        </div>
        <input type="text"
               id="gcHistorySearchInput"
               class="gc-history-panel-search"
               placeholder="Suchbegriffe filtern…" />

        <div id="gcHistoryPanelList" class="gc-history-panel-list">
            {{-- Filled by JS --}}
        </div>
    </div>
</div>


 {{-- Product modal overlay --}}
<div id="gcProductModalOverlay">
    <div id="gcProductModal">
        <div class="gc-product-modal-header">
            <span id="gcProductModalTitle">Produktprofil</span>
            <button type="button"
                    class="gc-product-modal-close"
                    onclick="closeProductModal()">
                ×
            </button>
        </div>

        <iframe id="gcProductModalFrame"
                src=""
                loading="lazy">
        </iframe>
    </div>
</div>

@endsection

@section('script')
<script>
    const GC_HISTORY_KEY       = "gc_ids_search_history";
    const GC_HISTORY_LIMIT     = 100;
    const GC_HISTORY_CHIPS_MAX = 10;
    const IDS_LOCAL_SEARCH_URL = @json(route('ids.local_search'));
    const PRODUCT_DETAILS_BASE = @json(url('product_details'));

    // -------------------- small helper: debounce --------------------
    function debounce(fn, delay) {
        let t = null;
        return function (...args) {
            if (t) clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }


    function prependImportedItemsToLocalResults(importedItems) {
        const box = document.getElementById("gcLocalResults");
        if (!box || !Array.isArray(importedItems) || !importedItems.length) return;

        const existingNodes = Array.from(box.querySelectorAll(".gc-local-item"));
        const existingIds = new Set(
            existingNodes
                .map(node => node.getAttribute("data-imported-id"))
                .filter(Boolean)
        );

        const newItems = importedItems.filter(item => !existingIds.has(String(item.id)));

        if (!newItems.length) return;

        // unsaved first, newest first
        newItems.sort((a, b) => {
            const aUnsaved = a.product_id ? 1 : 0;
            const bUnsaved = b.product_id ? 1 : 0;

            if (aUnsaved !== bUnsaved) {
                return aUnsaved - bUnsaved; // null product_id first
            }

            return (b.id || 0) - (a.id || 0);
        });

        const html = newItems.map(i => {
            const hasProduct = !!i.product_id;
            const badgeHtml = hasProduct
                ? '<span class="gc-local-badge">bereits im System</span>'
                : '<span class="gc-local-badge" style="background:#fef3c7;color:#92400e;">neu / noch nicht übernommen</span>';

            const disabledAttr = hasProduct ? "disabled" : "";
            const btnText = hasProduct
                ? "Bereits als Produkt angelegt"
                : "Als Produkt übernehmen";

            const articleNo = i.article_no ?? "";

            const titleHtml = hasProduct
                ? `<button type="button"
                        class="gc-product-link"
                        data-product-id="${i.product_id}"
                        data-title="${articleNo}">
                    ${articleNo}
                </button>`
                : articleNo;

            return `
                <div class="gc-local-item" data-imported-id="${i.id}">
                    <strong>${titleHtml}</strong>
                    ${badgeHtml}
                    <br>
                    ${i.short_text ?? ""}
                    <br>
                    <small>Batch: ${i.batch_id ?? "-"} · Menge: ${i.qty ?? 0} ${i.unit ?? ""}</small>
                    <div style="margin-top:6px;">
                        <button type="button"
                                class="gc-local-add-btn"
                                data-id="${i.id}"
                                ${disabledAttr}>
                            ${btnText}
                        </button>
                    </div>
                </div>
            `;
        }).join("");

        const emptyState = box.querySelector(".gc-local-empty");
        if (emptyState) {
            box.innerHTML = html;
            return;
        }

        box.insertAdjacentHTML("afterbegin", html);
    }
    // -------------------- History helpers --------------------
    function loadHistory() {
        try {
            const raw = window.localStorage.getItem(GC_HISTORY_KEY);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            console.warn("GC history parse error:", e);
            return [];
        }
    }

    function saveHistory(list) {
        try {
            window.localStorage.setItem(GC_HISTORY_KEY, JSON.stringify(list));
        } catch (e) {
            console.warn("GC history save error:", e);
        }
    }

    function addToHistory(term) {
        term = (term || "").trim();
        if (!term) return;

        let history = loadHistory();
        history = history.filter(t => t !== term); // remove old occurrence
        history.unshift(term);                     // add to front

        if (history.length > GC_HISTORY_LIMIT) {
            history = history.slice(0, GC_HISTORY_LIMIT);
        }

        saveHistory(history);
        renderHistory();
    }

    function deleteFromHistory(term) {
        term = (term || "").trim();
        if (!term) return;

        let history = loadHistory();
        history = history.filter(t => t !== term);
        saveHistory(history);
        renderHistory();
        renderHistoryPanel(); // keep full panel in sync if open
    }

    function renderHistory() {
        const container = document.getElementById("gcHistoryList");
        const moreBtn   = document.getElementById("gcHistoryMoreBtn");
        if (!container) return;

        const history = loadHistory();
        container.innerHTML = "";

        const total = history.length;

        if (!total) {
            const span = document.createElement("span");
            span.style.fontSize = "12px";
            span.style.color = "#9ca3af";
            span.textContent = "Noch keine Suchbegriffe gespeichert.";
            container.appendChild(span);

            if (moreBtn) moreBtn.style.display = "none";
            return;
        }

        const toShow = history.slice(0, GC_HISTORY_CHIPS_MAX);

        toShow.forEach(term => {
            if (!term) return;
            const chip = document.createElement("div");
            chip.className = "gc-history-chip";
            chip.dataset.term = term;

            chip.innerHTML = `
                <span class="gc-history-chip-text">${term}</span>
                <button type="button"
                        class="gc-history-chip-remove"
                        title="Eintrag löschen">
                    ×
                </button>
            `;

            container.appendChild(chip);
        });

        if (moreBtn) {
            if (total > GC_HISTORY_CHIPS_MAX) {
                moreBtn.style.display = "inline-block";
                moreBtn.textContent = "Alle anzeigen (" + total + ")";
            } else {
                moreBtn.style.display = "none";
            }
        }
    }

    // -------------------- History panel (full list + search) --------------------
    function openHistoryPanel() {
        const overlay = document.getElementById("gcHistoryPanelOverlay");
        if (!overlay) return;
        renderHistoryPanel();
        overlay.classList.add("open");

        const input = document.getElementById("gcHistorySearchInput");
        if (input) {
            input.value = "";
            input.focus();
        }
    }

    function closeHistoryPanel() {
        const overlay = document.getElementById("gcHistoryPanelOverlay");
        if (!overlay) return;
        overlay.classList.remove("open");
    }

    function renderHistoryPanel() {
        const listEl      = document.getElementById("gcHistoryPanelList");
        const searchInput = document.getElementById("gcHistorySearchInput");
        if (!listEl) return;

        const history = loadHistory();
        const q = (searchInput && searchInput.value ? searchInput.value : "")
            .toLowerCase()
            .trim();

        listEl.innerHTML = "";

        if (!history.length) {
            listEl.innerHTML =
                '<div class="gc-local-empty" style="padding:8px 10px;">Keine Suchbegriffe vorhanden.</div>';
            return;
        }

        const filtered = q
            ? history.filter(t => t.toLowerCase().includes(q))
            : history;

        if (!filtered.length) {
            listEl.innerHTML =
                '<div class="gc-local-empty" style="padding:8px 10px;">Keine Treffer für diese Suche.</div>';
            return;
        }

        filtered.forEach(term => {
            const row = document.createElement("div");
            row.className = "gc-history-panel-item";
            row.dataset.term = term;

            row.innerHTML = `
                <div class="gc-history-panel-term">${term}</div>
                <button type="button"
                        class="gc-history-panel-delete"
                        title="Eintrag löschen">
                    ×
                </button>
            `;

            listEl.appendChild(row);
        });
    }

    // -------------------- Product modal --------------------
    function openProductModal(productId, title) {
        if (!productId) return;

        const overlay = document.getElementById("gcProductModalOverlay");
        const frame   = document.getElementById("gcProductModalFrame");
        const titleEl = document.getElementById("gcProductModalTitle");

        if (!overlay || !frame) return;

        const base = PRODUCT_DETAILS_BASE.replace(/\/$/, "");
        frame.src  = base + "/" + productId;

        if (titleEl && title) {
            titleEl.textContent = "Produktprofil – " + title;
        }

        overlay.classList.add("open");
    }

    function closeProductModal() {
        const overlay = document.getElementById("gcProductModalOverlay");
        const frame   = document.getElementById("gcProductModalFrame");

        if (overlay) overlay.classList.remove("open");
        if (frame)   frame.src = "";
    }

    // -------------------- Local IDS results + promote --------------------
    function setupLocalPromoteHandler() {
            const box = document.getElementById("gcLocalResults");
            if (!box) return;

            box.addEventListener("click", function (event) {
                const btn = event.target.closest(".gc-local-add-btn");
                if (!btn) return;

                const id = btn.dataset.id;
                if (!id) return;

                // Einfach auf das Formular für "Produkt übernehmen" weiterleiten
                window.location.href = `/ids/promote/${id}`;
            });
        }

    function renderLocalResults(items) {
        const box = document.getElementById("gcLocalResults");
        if (!box) return;

        if (!items.length) {
            box.innerHTML =
                '<div class="gc-local-empty">Keine lokalen Treffer für diese Suche gefunden.</div>';
            return;
        }

        let html = "";
        items.forEach(i => {
            const hasProduct = !!i.product_id;

            const badgeHtml = hasProduct
                ? '<span class="gc-local-badge">bereits im System</span>'
                : '<span class="gc-local-badge" style="background:#fef3c7;color:#92400e;">neu / noch nicht übernommen</span>';

            const disabledAttr = hasProduct ? "disabled" : "";
            const btnText = hasProduct
                ? "Bereits als Produkt angelegt"
                : "Als Produkt übernehmen";

            const articleNo = i.article_no ?? "";

            const titleHtml = hasProduct
                ? `<button type="button"
                        class="gc-product-link"
                        data-product-id="${i.product_id}"
                        data-title="${articleNo}">
                    ${articleNo}
                </button>`
                : articleNo;

            html += `
                <div class="gc-local-item" data-imported-id="${i.id}">
                    <strong>${titleHtml}</strong>
                    ${badgeHtml}
                    <br>
                    ${i.short_text ?? ""}<br>
                    <small>Batch: ${i.batch_id ?? "-"} · Menge: ${i.qty ?? 0} ${i.unit ?? ""}</small>
                    <div style="margin-top:6px;">
                        <button type="button"
                                class="gc-local-add-btn"
                                data-id="${i.id}"
                                ${disabledAttr}>
                            ${btnText}
                        </button>
                    </div>
                </div>
            `;
        });

        box.innerHTML = html;
    }

    function loadLocalMatches(term = '') {
        term = (term || "").trim();
        const box = document.getElementById("gcLocalResults");
        if (!box) return;

        box.innerHTML = '<div class="gc-local-empty">Suche lokale Daten…</div>';

        const url = term
            ? IDS_LOCAL_SEARCH_URL + "?q=" + encodeURIComponent(term)
            : IDS_LOCAL_SEARCH_URL;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                renderLocalResults(Array.isArray(data) ? data : []);
            })
            .catch(err => {
                console.error("Fehler beim lokalen IDS-Suchaufruf:", err);
                box.innerHTML =
                    '<div class="gc-local-empty">Fehler bei der lokalen Suche.</div>';
            });
    }
    const loadLocalMatchesDebounced = debounce(loadLocalMatches, 300);

    // -------------------- Drawer for remote IDS imports --------------------
    function openGcDrawer() {
        const el = document.getElementById("gcDrawer");
        if (el) el.classList.add("open");
    }

    function closeGcDrawer() {
        const el = document.getElementById("gcDrawer");
        if (el) el.classList.remove("open");
    }

    function loadIdsResults(batchId) {
        fetch(`/ids/results/${batchId}`)
            .then(response => response.json())
            .then(items => {
                const container = document.getElementById("gcDrawerContent");
                if (!container) return;

                if (!items.length) {
                    container.innerHTML =
                        "<p style='font-size:13px;color:#6b7280;'>Keine Artikel gefunden.</p>";
                    openGcDrawer();
                    return;
                }

                let html = "";
                items.forEach(i => {
                    const hasProduct = !!i.product_id;
                    const badgeHtml = hasProduct
                        ? '<span style="display:inline-block;margin-left:6px;padding:2px 8px;border-radius:999px;background:#dcfce7;color:#166534;font-size:11px;font-weight:600;">bereits im System</span>'
                        : "";

                    const articleNo = i.article_no ?? "";

                    const titleHtml = hasProduct
                        ? `<button type="button"
                                   class="gc-product-link"
                                   data-product-id="${i.product_id}"
                                   data-title="${articleNo}">
                               ${articleNo}
                           </button>`
                        : articleNo;

                    html += `
                        <div class="drawer-item">
                            <strong>${titleHtml}</strong>
                            ${badgeHtml}
                            <br>
                            ${i.short_text ?? ""}<br>
                            <small>Menge: ${i.qty ?? 0} ${i.unit ?? ""}</small>
                        </div>
                    `;
                });

                container.innerHTML = html;
                openGcDrawer();

                container.addEventListener("click", function (event) {
                    const link = event.target.closest(".gc-product-link");
                    if (!link) return;
                    const productId = link.dataset.productId;
                    const title     = link.dataset.title || link.textContent || "";
                    openProductModal(productId, title);
                }, { once: true });
            })
            .catch(err => {
                console.error("Fehler beim Laden der IDS-Ergebnisse:", err);
            });
    }

    // -------------------- DOMContentLoaded: wire everything --------------------
    document.addEventListener("DOMContentLoaded", function () {
        const form                = document.getElementById("gcSearchForm");
        const input               = document.getElementById("gcSearchInput");
        const refreshBtn          = document.getElementById("gcLocalRefreshBtn");
        const moreBtn             = document.getElementById("gcHistoryMoreBtn");
        const historyList         = document.getElementById("gcHistoryList");
        const historySearchInput  = document.getElementById("gcHistorySearchInput");
        const historyPanelList    = document.getElementById("gcHistoryPanelList");

        const autoCheckbox        = document.getElementById("gcAutoPromote");
        const hookInput           = document.getElementById("gcHookUrl");
        const hookUidEl           = document.getElementById("gcHookUid");
        const hookBase            = hookInput
            ? (hookInput.getAttribute("data-base") || hookInput.value)
            : "";

        renderHistory();
        setupLocalPromoteHandler();
       loadLocalMatches("");

        // LIVE local search: as you type in the field, search ImportedIdsItem first
        if (input) {
            input.addEventListener("input", function () {
                const term = input.value || "";
                loadLocalMatchesDebounced(term);
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener("click", function () {
                if (input) {
                    input.value = "";
                }
                loadLocalMatches("");
            });
        }

        // "Alle anzeigen" → open panel
        if (moreBtn) {
            moreBtn.addEventListener("click", function () {
                openHistoryPanel();
            });
        }

        // Chips: × = delete; chip = reuse term + update local list
        if (historyList) {
            historyList.addEventListener("click", function (event) {
                const removeBtn = event.target.closest(".gc-history-chip-remove");
                if (removeBtn) {
                    const chip = removeBtn.closest(".gc-history-chip");
                    const term = chip && chip.dataset ? chip.dataset.term : "";
                    if (term) deleteFromHistory(term);
                    return;
                }

                const chip = event.target.closest(".gc-history-chip");
                if (chip) {
                    const term = chip.dataset.term || "";
                    if (!term) return;
                    if (input) {
                        input.value = term;
                        input.focus();
                    }
                    loadLocalMatches(term);
                }
            });
        }

        // Panel search
        if (historySearchInput) {
            historySearchInput.addEventListener("input", function () {
                renderHistoryPanel();
            });
        }

        // Panel: click term to reuse; × to delete
        if (historyPanelList) {
            historyPanelList.addEventListener("click", function (event) {
                const deleteBtn = event.target.closest(".gc-history-panel-delete");
                if (deleteBtn) {
                    const row = deleteBtn.closest(".gc-history-panel-item");
                    const term = row && row.dataset ? row.dataset.term : "";
                    if (term) deleteFromHistory(term);
                    return;
                }

                const termEl = event.target.closest(".gc-history-panel-term");
                if (termEl) {
                    const row  = termEl.closest(".gc-history-panel-item");
                    const term = (row && row.dataset ? row.dataset.term : "") || termEl.textContent || "";
                    if (input) {
                        input.value = term;
                        input.focus();
                    }
                    loadLocalMatches(term);
                    closeHistoryPanel();
                }
            });
        }

        // Submit: add term to history, run local search (again), open GC popup
        if (form) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const term = input ? input.value : "";
                addToHistory(term);
                loadLocalMatches(term); // local first

                // build callback URL with uid + auto flag
                if (hookInput && hookBase) {
                    const params = new URLSearchParams();
                    const uid = hookUidEl ? (hookUidEl.value || "") : "";
                    if (uid) {
                        params.set("uid", uid);
                    }
                    if (autoCheckbox && autoCheckbox.checked) {
                        params.set("auto", "1");
                    }
                    const query = params.toString();
                    hookInput.value = hookBase + (query ? ("?" + query) : "");
                }

                // then open GC Online in popup
                const width  = 1280;
                const height = 900;
                const left   = window.screenX + 40;
                const top    = window.screenY + 40;

                const features = [
                    "toolbar=yes",
                    "location=yes",
                    "status=yes",
                    "menubar=yes",
                    "scrollbars=yes",
                    "resizable=yes",
                    "width=" + width,
                    "height=" + height,
                    "left=" + left,
                    "top=" + top,
                ].join(",");

                const winName = "GC_ONLINE_POPUP";
                const popup = window.open("", winName, features);
                if (!popup) {
                    alert("Bitte Pop-ups für diese Seite im Browser erlauben.");
                    return;
                }

                form.target = winName;
                form.submit();
            });
        }

        // Realtime IDS drawer via Echo
        if (window.Echo) {
            console.log("🔵 IDS listener active on this page…");

            window.Echo.channel("ids")
                .listen("IdsItemsImported", function (e) {
                    console.log("📦 IDS event received:", e);
                    if (!e.batchId) return;

                    // keep drawer behavior
                    loadIdsResults(e.batchId);

                    // also inject newest imported rows into local area
                    fetch(`/ids/results/${e.batchId}`)
                        .then(response => response.json())
                        .then(items => {
                            prependImportedItemsToLocalResults(items);

                            // optional: if the user already typed something,
                            // re-run filtered search after prepend
                            const input = document.getElementById("gcSearchInput");
                            const currentTerm = input ? (input.value || "").trim() : "";

                            if (currentTerm !== "") {
                                loadLocalMatches(currentTerm);
                            }
                        })
                        .catch(err => {
                            console.error("Fehler beim Nachladen der lokalen IDS-Items:", err);
                        });
                });
        } else {
            console.warn("Echo not available on this page – IDS realtime drawer disabled.");
        }
    });
</script>
@endsection


@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Produktliste',
                url: "{{ url('product') }}", 
            },
            {
                label: 'IDS - GC-Online',
                url: "{{ url('product') }}",
                clickable: false
            },
            
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush