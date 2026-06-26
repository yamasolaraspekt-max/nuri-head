<style>
    /* ============================================================
       SA-DESK ACTIVITY SIDEBAR + DETAIL MODAL + SELECT2 FILTERS
       Engineered UI block mapping perfectly to core CSS Variables:
       --bg-surface, --bg-body, --bg-hover, --border-color,
       --text-main, --text-muted, --brand-blue, --brand-green
    ============================================================ */

    .sa-activity-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.46);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        z-index: 1090;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.22s ease, visibility 0.22s ease;
    }

    .sa-activity-backdrop.is-active {
        opacity: 1;
        visibility: visible;
    }

    .sa-activity-sider {
        position: fixed;
        top: 0;
        right: 0;
        width: min(520px, 96vw);
        height: 100vh;
        background: radial-gradient(circle at top right, rgba(116, 178, 212, 0.20), transparent 36%), var(--bg-surface);
        color: var(--text-main);
        z-index: 1100;
        transform: translateX(105%);
        display: flex;
        flex-direction: column;
        box-shadow: -18px 0 55px rgba(15, 23, 42, 0.22);
        border-left: 1px solid var(--border-color);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .sa-activity-sider.is-open {
        transform: translateX(0);
    }

    .sa-activity-head {
        padding: 18px 18px 16px;
        background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
        color: #ffffff;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .sa-activity-head::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 999px;
        right: -70px;
        top: -80px;
        background: rgba(255, 255, 255, 0.18);
    }

    .sa-activity-head-row {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .sa-activity-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sa-activity-title-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .sa-activity-title {
        margin: 0;
        color: #ffffff;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 900;
    }

    .sa-activity-subtitle {
        margin: 3px 0 0;
        color: rgba(255, 255, 255, 0.82);
        font-size: 12px;
        font-weight: 700;
    }

    .sa-activity-close {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.22);
        transition: background 0.18s ease, transform 0.18s ease;
        position: relative;
        z-index: 2;
    }

    .sa-activity-close:hover {
        background: rgba(255, 255, 255, 0.24);
        transform: scale(1.04);
    }

    .sa-activity-tools {
        padding: 14px 16px;
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    /* Layout updating Search + Filter Trigger */
    .sa-activity-search-bar-row {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }

    .sa-activity-search {
        position: relative;
        flex: 1;
    }

    .sa-activity-search i,
    .sa-activity-search svg {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }

    .sa-activity-search input {
        width: 100%;
        height: 42px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        background: var(--bg-hover);
        color: var(--text-main);
        outline: none;
        padding: 0 14px 0 40px;
        font-size: 13px;
        font-weight: 700;
        transition: border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
    }

    .sa-activity-search input:focus {
        background: var(--bg-surface);
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 3px rgba(116, 178, 212, 0.20);
    }

    .sa-activity-filter-btn {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        background: var(--bg-hover);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.18s ease;
    }

    .sa-activity-filter-btn:hover,
    .sa-activity-filter-btn.active {
        background: var(--brand-blue);
        border-color: var(--brand-blue);
        color: #ffffff;
    }

    .sa-activity-filter-row {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: none;
    }

    .sa-activity-filter-row::-webkit-scrollbar {
        display: none;
    }

    .sa-activity-chip {
        height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
    }

    .sa-activity-chip:hover,
    .sa-activity-chip.active {
        background: rgba(147, 194, 28, 0.13);
        border-color: rgba(147, 194, 28, 0.45);
        color: var(--brand-green);
    }

    /* Advanced Advanced Filter Configuration section */
    .sa-activity-settings-panel {
        display: none;
        background: var(--bg-hover);
        border-bottom: 1px solid var(--border-color);
        max-height: 50vh;
        overflow-y: auto;
    }

    /* Custom Integration bridging Select2 with SA-DESK UI Guidelines */
    .custom-select2-box {
        text-align: left;
    }

    .custom-select2-box .select2-container--default .select2-selection--multiple {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        min-height: 42px;
        padding: 2px 8px;
        transition: border-color var(--transition), box-shadow var(--transition);
    }

    .custom-select2-box .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 3px rgba(116, 178, 212, 0.20);
    }

    .custom-select2-box .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-main);
        padding: 4px 8px;
        margin-top: 5px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        flex-direction: row-reverse;
        gap: 6px;
    }

    .custom-select2-box .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--text-muted);
        border: none;
        position: relative;
        font-size: 14px;
        font-weight: bold;
        padding: 0;
        margin: 0;
    }

    .custom-select2-box .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background: transparent;
        color: var(--color-danger);
    }

    .select2-container--default .select2-dropdown {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        z-index: 1200;
    }

    .select2-container--default .select2-results__option {
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: rgba(147, 194, 28, 0.15);
        color: var(--brand-green);
        font-weight: bold;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: var(--bg-hover);
        color: var(--text-muted);
    }

    .sa-activity-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 14px;
    }

    .sa-activity-state {
        padding: 34px 18px;
        text-align: center;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 700;
    }

    .sa-activity-state-icon {
        width: 54px;
        height: 54px;
        margin: 0 auto 12px;
        border-radius: 18px;
        background: var(--bg-hover);
        color: var(--brand-blue);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sa-activity-card {
        position: relative;
        display: flex;
        gap: 12px;
        width: 100%;
        text-align: left;
        padding: 13px;
        margin-bottom: 10px;
        border-radius: 18px;
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
        color: var(--text-main);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
    }

    .sa-activity-card:hover {
        transform: translateY(-2px);
        border-color: rgba(116, 178, 212, 0.65);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
    }

    .sa-activity-avatar-wrap {
        position: relative;
        flex: 0 0 auto;
    }

    .sa-activity-avatar {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        object-fit: cover;
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
    }

    .sa-activity-type-dot {
        position: absolute;
        right: -5px;
        bottom: -5px;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: var(--brand-blue);
        color: #ffffff;
        border: 3px solid var(--bg-surface);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sa-activity-card-main {
        flex: 1;
        min-width: 0;
    }

    .sa-activity-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 5px;
    }

    .sa-activity-model {
        max-width: 160px;
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(116, 178, 212, 0.14);
        color: var(--brand-blue);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-activity-time {
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .sa-activity-customer {
        margin: 0;
        color: var(--text-main);
        font-size: 14px;
        font-weight: 900;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-activity-text {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .sa-activity-meta {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 800;
    }

    .sa-activity-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-width: 0;
    }

    .sa-activity-meta svg {
        width: 13px;
        height: 13px;
    }

    /* Quick Dropdown in Header */
    .sa-activity-quick-item {
        width: 100%;
        padding: 12px;
        border-bottom: 1px solid var(--border-light);
        background: var(--bg-surface);
        color: var(--text-main);
        display: flex;
        gap: 10px;
        text-align: left;
        cursor: pointer;
        transition: background 0.18s ease;
    }

    .sa-activity-quick-item:hover {
        background: var(--bg-hover);
    }

    .sa-activity-quick-icon {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        background: rgba(116, 178, 212, 0.14);
        color: var(--brand-blue);
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .sa-activity-quick-title {
        margin: 0;
        font-size: 12px;
        color: var(--text-main);
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-activity-quick-text {
        margin: 3px 0 0;
        color: var(--text-muted);
        font-size: 11px;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Target Modal Window Container */
    .sa-activity-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .sa-activity-modal-backdrop.is-active {
        display: flex;
    }

    .sa-activity-modal {
        width: min(780px, 96vw);
        max-height: 88vh;
        background: var(--bg-surface);
        color: var(--text-main);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 30px 90px rgba(15, 23, 42, 0.30);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
    }

    .sa-activity-modal-head {
        padding: 18px 20px;
        background: radial-gradient(circle at top right, rgba(147, 194, 28, 0.20), transparent 34%), var(--bg-surface);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-shrink: 0;
    }

    .sa-activity-modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sa-activity-modal-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: rgba(116, 178, 212, 0.14);
        color: var(--brand-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sa-activity-modal-title {
        margin: 0;
        color: var(--text-main);
        font-size: 17px;
        font-weight: 900;
        line-height: 1.2;
    }

    .sa-activity-modal-subtitle {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .sa-activity-modal-body {
        padding: 20px;
        overflow-y: auto;
    }

    .sa-activity-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .sa-activity-detail-box {
        padding: 12px;
        border-radius: 16px;
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        min-width: 0;
    }

    .sa-activity-detail-label {
        margin: 0 0 5px;
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .sa-activity-detail-value {
        margin: 0;
        color: var(--text-main);
        font-size: 13px;
        font-weight: 900;
        word-break: break-word;
    }

    .sa-activity-message-box {
        padding: 15px;
        border-radius: 18px;
        background: rgba(116, 178, 212, 0.10);
        border: 1px solid rgba(116, 178, 212, 0.25);
        color: var(--text-main);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.55;
        margin-bottom: 16px;
    }

    .sa-activity-section-title {
        margin: 18px 0 10px;
        font-size: 12px;
        font-weight: 900;
        color: var(--text-main);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .sa-activity-change-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .sa-activity-change-row {
        padding: 12px;
        border-radius: 16px;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
    }

    .sa-activity-change-field {
        margin: 0 0 8px;
        color: var(--brand-blue);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .sa-activity-change-values {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 8px;
        align-items: center;
    }

    .sa-activity-change-pill {
        min-width: 0;
        padding: 8px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.35;
        word-break: break-word;
        border: 1px solid transparent;
    }

    .sa-activity-change-pill.old {
        background: rgba(229, 6, 86, 0.08);
        color: #e50656;
        border-color: rgba(229, 6, 86, 0.18);
    }

    .sa-activity-change-pill.new {
        background: rgba(147, 194, 28, 0.12);
        color: var(--brand-green);
        border-color: rgba(147, 194, 28, 0.28);
    }

    .sa-activity-arrow {
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dark .sa-activity-card,
    .dark .sa-activity-modal,
    .dark .sa-activity-detail-box,
    .dark .sa-activity-change-row {
        box-shadow: none;
    }

    @media (max-width: 575px) {
        .sa-activity-sider {
            width: 100vw;
        }

        .sa-activity-detail-grid {
            grid-template-columns: 1fr;
        }

        .sa-activity-change-values {
            grid-template-columns: 1fr;
        }

        .sa-activity-arrow {
            justify-content: flex-start;
            transform: rotate(90deg);
            width: 22px;
        }
    }
</style>
<div id="activityBackdrop" class="sa-activity-backdrop" onclick="toggleActivitySidebar()"></div>

<aside id="activitySidebar" class="sa-activity-sider" aria-hidden="true">
    <div class="sa-activity-head">
        <div class="sa-activity-head-row">
            <div class="sa-activity-title-wrap">
                <div class="sa-activity-title-icon">
                    <i data-lucide="activity" class="icon-lg"></i>
                </div>

                <div style="min-width:0;">
                    <h5 class="sa-activity-title">Live-Aktivitäten</h5>
                    <p class="sa-activity-subtitle">Kürzliche Änderungen im System</p>
                </div>
            </div>

            <button type="button" class="sa-activity-close" onclick="toggleActivitySidebar()" title="Schließen">
                <i data-lucide="x" class="icon-lg"></i>
            </button>
        </div>
    </div>

    <div class="sa-activity-tools">
        <div class="sa-activity-search-bar-row">
            <div class="sa-activity-search">
                <i data-lucide="search"></i>
                <input type="text" id="activitySearchInput" placeholder="Aktivitäten durchsuchen..." autocomplete="off">
            </div>

            <button type="button" class="sa-activity-filter-btn" id="activityFilterTriggerBtn"
                onclick="toggleActivitySettingsPanel()" title="Erweiterte Filter">
                <i data-lucide="sliders-horizontal" class="icon-lg"></i>
            </button>
        </div>

        <div id="activityFilters" class="sa-activity-filter-row">
            <button type="button" class="sa-activity-chip active" data-type="all">
                <i data-lucide="list-filter" class="icon-sm"></i> Alle
            </button>

            <button type="button" class="sa-activity-chip" data-type="customer">
                <i data-lucide="user" class="icon-sm"></i> Kunden
            </button>

            <button type="button" class="sa-activity-chip" data-type="ticket">
                <i data-lucide="alert-triangle" class="icon-sm"></i> Tickets
            </button>

            <button type="button" class="sa-activity-chip" data-type="appointment">
                <i data-lucide="calendar" class="icon-sm"></i> Termine
            </button>

            <button type="button" class="sa-activity-chip" data-type="offer">
                <i data-lucide="file-text" class="icon-sm"></i> Angebote
            </button>
        </div>
    </div>

    <div id="activityFilterSection" class="sa-activity-settings-panel p-3">
        <div class="mb-3"
            style="padding:10px 12px; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:12px;">
            <p class="small text-muted mb-0" style="line-height:1.5;">
                Wähle aus, wessen Aktivitäten du sehen möchtest. Lässt du ein Feld leer, werden alle Einträge angezeigt.
            </p>
        </div>

        {{-- Kunde --}}
        <div class="mb-3 custom-select2-box">
            <label class="sa-ms-label d-block mb-1 font-weight-bold" style="font-size:12px; color:var(--text-main);">
                Kunde filtern
            </label>

            <select id="filter-customers" class="select2-activity-static w-full" multiple="multiple"
                data-placeholder="Kunden wählen...">
                @foreach(\App\Models\NewLeads::whereNull('deleted_at')->orderBy('lastname')->orderBy('name')->get() as $c)
                    @php
                        $customerName = trim(($c->lastname ?? '') . ' ' . ($c->name ?? ''));
                        $customerText = trim($customerName . ($c->firma ? ' (' . $c->firma . ')' : ''));
                    @endphp

                    <option value="{{ $c->id }}">
                        {{ $customerText ?: 'Kunde #' . $c->id }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Mitarbeiter --}}
        <div class="mb-3 custom-select2-box">
            <label class="sa-ms-label d-block mb-1 font-weight-bold" style="font-size:12px; color:var(--text-main);">
                Mitarbeiter filtern
            </label>

            <select id="filter-employees" class="select2-activity-static w-full" multiple="multiple"
                data-placeholder="Mitarbeiter wählen...">
                @foreach(\App\Models\Employee::where('status', 'Active')->orderBy('name')->orderBy('lastname')->get() as $e)
                    @php
                        $employeeName = trim(($e->name ?? '') . ' ' . ($e->lastname ?? ''));
                    @endphp

                    <option value="{{ $e->id }}">
                        {{ $employeeName ?: 'Mitarbeiter #' . $e->id }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Abteilung --}}
        <div class="mb-3 custom-select2-box">
            <label class="sa-ms-label d-block mb-1 font-weight-bold" style="font-size:12px; color:var(--text-main);">
                Abteilung filtern
            </label>

            <select id="filter-departments" class="select2-activity-static w-full" multiple="multiple"
                data-placeholder="Abteilungen wählen...">
                <option value="sales">Vertrieb</option>
                <option value="office">Büro</option>
                <option value="support">Support</option>
                <option value="technic">Technik</option>
                <option value="management">Management</option>
                <option value="general">Allgemein</option>
            </select>
        </div>

        {{-- Produkt --}}
        <div class="mb-3 custom-select2-box">
            <label class="sa-ms-label d-block mb-1 font-weight-bold" style="font-size:12px; color:var(--text-main);">
                Produkt filtern
            </label>

            <select id="filter-products" class="select2-activity-static w-full" multiple="multiple"
                data-placeholder="Produkte wählen...">
                @foreach(\App\Models\ArticleGroup::orderBy('article_group')->get() as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->article_group }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Typ --}}
        <div class="mb-3 custom-select2-box">
            <label class="sa-ms-label d-block mb-1 font-weight-bold" style="font-size:12px; color:var(--text-main);">
                Typ filtern
            </label>

            <select id="filter-types" class="select2-activity-static w-full" multiple="multiple"
                data-placeholder="Typen wählen...">
                <option value="notes">Notizen</option>
                <option value="process">Prozess</option>
                <option value="ticket">Ticket</option>
                <option value="appointment">Termin</option>
                <option value="customer">Kunde</option>
                <option value="address">Objekt / Adresse</option>
                <option value="offer">Angebot</option>
                <option value="deal">Deal</option>
                <option value="invoice">Rechnung</option>
                <option value="product">Produkt</option>
                <option value="general">Allgemein</option>
            </select>
        </div>

        <div class="mt-3"
            style="padding:12px; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:12px;">
            <label class="mb-0 d-flex align-items-center justify-content-between" style="cursor:pointer; gap:12px;">
                <div>
                    <div style="font-weight:800; color:var(--text-main); font-size:13px;">
                        Ton stummschalten
                    </div>

                    <small class="text-muted" style="font-size:11px;">
                        Keine Audio-Benachrichtigung bei neuen Live-Aktivitäten
                    </small>
                </div>

                <input type="checkbox" id="activityMuteToggle" style="transform:scale(1.2);">
            </label>
        </div>

        <div class="d-flex mt-3" style="gap:8px;">
            <button type="button" class="btn btn-block waves-effect waves-light flex-1"
                style="background:var(--primary); color:#fff; font-weight:800; border-radius:10px; padding:10px; border:none; cursor:pointer;"
                onclick="saveActivityFilters(event)">
                Filter anwenden
            </button>

            <button type="button" class="btn btn-light waves-effect waves-light"
                style="font-weight:700; border-radius:10px; padding:10px 14px; border:1px solid var(--border-color); background:var(--bg-surface); color:var(--text-main); cursor:pointer;"
                onclick="clearActivityFilters()">
                Leeren
            </button>
        </div>
    </div>

    <div id="activityList" class="sa-activity-body">
        <div class="sa-activity-state">
            <div class="sa-activity-state-icon">
                <i data-lucide="loader-circle" class="icon-lg"></i>
            </div>
            Aktivitäten werden geladen...
        </div>
    </div>
</aside>

<div id="activityDetailModalBackdrop" class="sa-activity-modal-backdrop" onclick="closeActivityDetailModal(event)">
    <div class="sa-activity-modal" onclick="event.stopPropagation()">
        <div class="sa-activity-modal-head">
            <div class="sa-activity-modal-title-wrap">
                <div class="sa-activity-modal-icon">
                    <i data-lucide="activity" class="icon-lg"></i>
                </div>

                <div style="min-width:0;">
                    <h5 id="activityDetailModalTitle" class="sa-activity-modal-title">
                        Details
                    </h5>

                    <p id="activityDetailModalSubtitle" class="sa-activity-modal-subtitle">
                        Aktivitätsdetails
                    </p>
                </div>
            </div>

            <button type="button" class="icon-btn" onclick="closeActivityDetailModal()">
                <i data-lucide="x" class="icon-lg"></i>
            </button>
        </div>

        <div id="activityDetailModalBody" class="sa-activity-modal-body"></div>
    </div>
</div>