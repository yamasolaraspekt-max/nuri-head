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

    /* Local results box */
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

    /* History (chips under search) */
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

    .gc-history-more-btn {
        border: none;
        background: none;
        color: #2563eb;
        font-size: 11px;
        cursor: pointer;
        padding: 0;
    }

    /* Full history panel */
    #gcHistoryPanelOverlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.4);
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
        box-shadow: 0 20px 40px rgba(15,23,42,0.25);
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

    /* Drawer for live imported IDS results (Reverb) */
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

    /* GC iframe overlay */
    #gcOverlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.75);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99990;
    }

    #gcOverlay.open {
        display: flex;
    }

    #gcOverlayInner {
        width: 96%;
        height: 92%;
        background: #1f2937;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        overflow: hidden;
        position: relative;
    }

    #gcOverlayCloseBtn {
        position: absolute;
        top: 10px;
        right: 14px;
        z-index: 2;
        border: none;
        background: rgba(15,23,42,0.75);
        color: #f9fafb;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 13px;
        cursor: pointer;
    }

    #gcOverlayCloseBtn:hover {
        background: rgba(15,23,42,0.95);
    }

    #gcFrame {
        width: 100%;
        height: 100%;
        border: none;
        background: #ffffff;
    }
</style>

<div class="app-content content">
    <div class="content-wrapper">

        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title">GC Online Artikelsuche</h2>
            </div>
        </div>

        <div class="content-body">
            <div class="gc-wrapper">
                <h1 class="gc-title">Artikelsuche bei GC Online</h1>

                <p class="gc-subtext">
                    GC Online wird in einem Overlay direkt in dieser Seite geöffnet (als große Ansicht im „Frame“).
                    Nach Auswahl eines Artikels sendet GC eine IDS-Rückgabe an dein System. Sobald diese ankommt,
                    wird das Overlay automatisch geschlossen und die importierten Artikel erscheinen im rechten Panel.
                </p>

                <div class="gc-box">
                    <form id="gcSearchForm"
                          method="POST"
                          action="https://gconlineplus.de/ids.aspx"
                          target="gcFrame"
                          enctype="multipart/form-data">

                        {{-- IDS Pflichtfelder --}}
                        <input type="hidden" name="action"  value="AS">
                        <input type="hidden" name="version" value="2.5">
                        <input type="hidden" name="target"  value="TOP">

                        {{-- IDS Callback (Server → Laravel) --}}
                        <input type="hidden" name="hookurl" value="{{ route('ids.callback') }}">

                        {{-- Browser-Rücksprung – passiert innerhalb des iframes --}}
                        <input type="hidden" name="rueckurl" value="{{ route('ids.search.form.inline') }}">

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
                            Wenn GC das Einbetten in Frames verbietet (X-Frame-Options / CSP),
                            kann die Seite nicht im Overlay erscheinen. Dann bleibt nur ein echtes Browser-Tab/Fenster.
                        </p>

                        {{-- History unter dem Suchfeld --}}
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

                        {{-- Lokale Treffer (ImportedIdsItem + Product-Badge) --}}
                        <div class="gc-local-results">
                            <div class="gc-local-header">
                                <span>Lokale IDS-Treffer</span>
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

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Drawer: Echtzeit-IDS-Ergebnisse (Reverb) --}}
<div id="gcDrawer">
    <button class="drawer-close" type="button" onclick="closeGcDrawer()">×</button>
    <h2>Importierte IDS-Artikel</h2>
    <div id="gcDrawerContent"></div>
</div>

{{-- GC iframe overlay --}}
<div id="gcOverlay">
    <div id="gcOverlayInner">
        <button id="gcOverlayCloseBtn" type="button" onclick="closeGcOverlayManually()">Schließen</button>
        <iframe id="gcFrame" name="gcFrame"></iframe>
    </div>
</div>

{{-- History Panel --}}
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
               placeholder="Suchbegriffe filtern…">

        <div id="gcHistoryPanelList" class="gc-history-panel-list"></div>
    </div>
</div>

@endsection

@section('script')
<script>
    const GC_HISTORY_KEY        = "gc_ids_search_history";
    const GC_HISTORY_LIMIT      = 100;
    const GC_HISTORY_CHIPS_MAX  = 10;
    const IDS_LOCAL_SEARCH_URL  = @json(route('ids.local_search'));
    const IDS_PROMOTE_BASE_URL  = "/ids/promote/";

    // -------------------------------------------------
    // History helpers
    // -------------------------------------------------
    function loadHistory() {
        try {
            const raw = localStorage.getItem(GC_HISTORY_KEY);
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
            localStorage.setItem(GC_HISTORY_KEY, JSON.stringify(list));
        } catch (e) {
            console.warn("GC history save error:", e);
        }
    }

    function addToHistory(term) {
        term = (term || "").trim();
        if (!term) return;
        let history = loadHistory();
        history = history.filter(t => t !== term);
        history.unshift(term);
        if (history.length > GC_HISTORY_LIMIT) {
            history = history.slice(0, GC_HISTORY_LIMIT);
        }
        saveHistory(history);
        renderHistoryChips();
    }

    function deleteFromHistory(term) {
        term = (term || "").trim();
        if (!term) return;
        let history = loadHistory();
        history = history.filter(t => t !== term);
        saveHistory(history);
        renderHistoryChips();
        renderHistoryPanel();
    }

    // -------------------------------------------------
    // History chips under search
    // -------------------------------------------------
    function renderHistoryChips() {
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
            const chip = document.createElement("div");
            chip.className = "gc-history-chip";
            chip.dataset.term = term;

            chip.innerHTML = `
                <span class="gc-history-chip-text">${term}</span>
                <button type="button"
                        class="gc-history-chip-remove"
                        title="Eintrag löschen">×</button>
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

    // -------------------------------------------------
    // Full history panel
    // -------------------------------------------------
    function openHistoryPanel() {
        const overlay = document.getElementById("gcHistoryPanelOverlay");
        if (!overlay) return;
        renderHistoryPanel();
        overlay.classList.add("open");
        const inp = document.getElementById("gcHistorySearchInput");
        if (inp) {
            inp.value = "";
            inp.focus();
        }
    }

    function closeHistoryPanel() {
        const overlay = document.getElementById("gcHistoryPanelOverlay");
        if (!overlay) return;
        overlay.classList.remove("open");
    }

    function renderHistoryPanel() {
        const listEl = document.getElementById("gcHistoryPanelList");
        const searchInput = document.getElementById("gcHistorySearchInput");
        if (!listEl) return;

        const history = loadHistory();
        const q = (searchInput?.value || "").toLowerCase().trim();

        listEl.innerHTML = "";

        if (!history.length) {
            listEl.innerHTML = '<div class="gc-local-empty" style="padding:8px 10px;">Keine Suchbegriffe vorhanden.</div>';
            return;
        }

        const filtered = q ? history.filter(t => t.toLowerCase().includes(q)) : history;

        if (!filtered.length) {
            listEl.innerHTML = '<div class="gc-local-empty" style="padding:8px 10px;">Keine Treffer für diese Suche.</div>';
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
                        title="Eintrag löschen">×</button>
            `;
            listEl.appendChild(row);
        });
    }

    // -------------------------------------------------
    // Local results (ImportedIdsItem + product badge)
    // -------------------------------------------------
    function renderLocalResults(items) {
        const box = document.getElementById("gcLocalResults");
        if (!box) return;

        if (!items.length) {
            box.innerHTML = '<div class="gc-local-empty">Keine lokalen Treffer für diese Suche gefunden.</div>';
            return;
        }

        let html = "";
        items.forEach(i => {
            const hasProduct = !!i.product_id;
            const badgeHtml = hasProduct
                ? '<span class="gc-local-badge">bereits im System</span>'
                : "";
            const disabledAttr = hasProduct ? "disabled" : "";
            const btnText = hasProduct ? "Bereits als Produkt angelegt" : "Als Produkt übernehmen";

            html += `
                <div class="gc-local-item">
                    <strong>${i.article_no ?? ""}</strong>
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

    function loadLocalMatches(term) {
        term = (term || "").trim();
        const box = document.getElementById("gcLocalResults");
        if (!box) return;

        if (!term) {
            box.innerHTML = '<div class="gc-local-empty">Noch keine Suche ausgeführt.</div>';
            return;
        }

        box.innerHTML = '<div class="gc-local-empty">Suche lokale Daten…</div>';

        fetch(IDS_LOCAL_SEARCH_URL + "?q=" + encodeURIComponent(term))
            .then(r => r.json())
            .then(data => {
                renderLocalResults(Array.isArray(data) ? data : []);
            })
            .catch(err => {
                console.error("Fehler beim lokalen IDS-Suchaufruf:", err);
                box.innerHTML = '<div class="gc-local-empty">Fehler bei der lokalen Suche.</div>';
            });
    }

    function setupLocalPromoteHandler() {
        const box = document.getElementById("gcLocalResults");
        if (!box) return;

        box.addEventListener("click", function (event) {
            const btn = event.target.closest(".gc-local-add-btn");
            if (!btn) return;

            const id = btn.dataset.id;
            if (!id) return;

            if (!confirm("Diesen IDS-Artikel als Produkt übernehmen?")) {
                return;
            }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch(IDS_PROMOTE_BASE_URL + id, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrf,
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({}),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Produkt erfolgreich angelegt/aktualisiert (ID: " + data.product_id + ").");
                    const inp = document.getElementById("gcSearchInput");
                    if (inp && inp.value) {
                        loadLocalMatches(inp.value);
                    }
                } else {
                    alert("Fehler beim Anlegen des Produkts.");
                    console.error("Promote error:", data);
                }
            })
            .catch(err => {
                console.error("Promote fetch error:", err);
                alert("Fehler beim Anlegen des Produkts (Netzwerkfehler).");
            });
        });
    }

    // -------------------------------------------------
    // Drawer for imported IDS results (Reverb)
    // -------------------------------------------------
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
                    container.innerHTML = "<p style='font-size:13px;color:#6b7280;'>Keine Artikel gefunden.</p>";
                    openGcDrawer();
                    return;
                }

                let html = "";
                items.forEach(i => {
                    const hasProduct = !!i.product_id;
                    const badgeHtml = hasProduct
                        ? '<span style="display:inline-block;margin-left:6px;padding:2px 8px;border-radius:999px;background:#dcfce7;color:#166534;font-size:11px;font-weight:600;">bereits im System</span>'
                        : "";

                    html += `
                        <div class="drawer-item">
                            <strong>${i.article_no ?? ""}</strong>
                            ${badgeHtml}
                            <br>
                            ${i.short_text ?? ""}<br>
                            <small>Menge: ${i.qty ?? 0} ${i.unit ?? ""}</small>
                        </div>
                    `;
                });

                container.innerHTML = html;
                openGcDrawer();

                // GC callback done → overlay schließen
                closeGcOverlay();
            })
            .catch(err => {
                console.error("Fehler beim Laden der IDS-Ergebnisse:", err);
            });
    }

    // -------------------------------------------------
    // GC iframe overlay
    // -------------------------------------------------
    function openGcOverlay() {
        const overlay = document.getElementById("gcOverlay");
        if (overlay) overlay.classList.add("open");
    }

    function closeGcOverlay() {
        const overlay = document.getElementById("gcOverlay");
        if (overlay) overlay.classList.remove("open");
    }

    function closeGcOverlayManually() {
        if (!confirm("GC Online schließen? Nicht gespeicherte Warenkörbe gehen verloren.")) {
            return;
        }
        closeGcOverlay();
    }

    // -------------------------------------------------
    // DOMContentLoaded
    // -------------------------------------------------
    document.addEventListener("DOMContentLoaded", function () {
        const form               = document.getElementById("gcSearchForm");
        const input              = document.getElementById("gcSearchInput");
        const refreshBtn         = document.getElementById("gcLocalRefreshBtn");
        const moreBtn            = document.getElementById("gcHistoryMoreBtn");
        const historyList        = document.getElementById("gcHistoryList");
        const historySearchInput = document.getElementById("gcHistorySearchInput");
        const historyPanelList   = document.getElementById("gcHistoryPanelList");

        renderHistoryChips();
        setupLocalPromoteHandler();

        if (refreshBtn && input) {
            refreshBtn.addEventListener("click", function () {
                loadLocalMatches(input.value || "");
            });
        }

        if (moreBtn) {
            moreBtn.addEventListener("click", openHistoryPanel);
        }

        if (historyList) {
            historyList.addEventListener("click", function (event) {
                const removeBtn = event.target.closest(".gc-history-chip-remove");
                if (removeBtn) {
                    const chip = removeBtn.closest(".gc-history-chip");
                    const term = chip?.dataset.term || "";
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

        if (historySearchInput) {
            historySearchInput.addEventListener("input", renderHistoryPanel);
        }

        if (historyPanelList) {
            historyPanelList.addEventListener("click", function (event) {
                const deleteBtn = event.target.closest(".gc-history-panel-delete");
                if (deleteBtn) {
                    const row  = deleteBtn.closest(".gc-history-panel-item");
                    const term = row?.dataset.term || "";
                    if (term) deleteFromHistory(term);
                    return;
                }

                const termEl = event.target.closest(".gc-history-panel-term");
                if (termEl) {
                    const row  = termEl.closest(".gc-history-panel-item");
                    const term = row?.dataset.term || termEl.textContent || "";
                    if (input) {
                        input.value = term;
                        input.focus();
                    }
                    loadLocalMatches(term);
                    closeHistoryPanel();
                }
            });
        }

        if (form) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const term = input ? input.value : "";
                addToHistory(term);
                loadLocalMatches(term);

                // GC Online im iframe öffnen
                openGcOverlay();

                // jetzt normal submitten (Ziel ist das iframe gcFrame)
                form.submit();
            });
        }

        if (window.Echo) {
            window.Echo.channel("ids")
                .listen("IdsItemsImported", function (e) {
                    if (!e.batchId) return;
                    loadIdsResults(e.batchId);
                });
        }
    });
</script>
@endsection
