<style>
    /* ============================================================
       SA-DESK NOTIFICATION SIDEBAR
       Same design style as activity sidebar
    ============================================================ */

    .sa-notif-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .46);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        z-index: 1090;
        opacity: 0;
        visibility: hidden;
        transition: opacity .22s ease, visibility .22s ease;
    }

    .sa-notif-backdrop.is-active {
        opacity: 1;
        visibility: visible;
    }

    .sa-notif-sider {
        position: fixed;
        top: 0;
        right: 0;
        width: min(520px, 96vw);
        height: 100vh;
        background:
            radial-gradient(circle at top right, rgba(116, 178, 212, .20), transparent 36%),
            var(--bg-surface);
        color: var(--text-main);
        z-index: 1100;
        transform: translateX(105%);
        display: flex;
        flex-direction: column;
        box-shadow: -18px 0 55px rgba(15, 23, 42, .22);
        border-left: 1px solid var(--border-color);
        transition: transform .28s cubic-bezier(.16, 1, .3, 1);
    }

    .sa-notif-sider.is-open {
        transform: translateX(0);
    }

    .sa-notif-head {
        padding: 18px 18px 16px;
        background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
        color: #fff;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .sa-notif-head::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 999px;
        right: -70px;
        top: -80px;
        background: rgba(255, 255, 255, .18);
    }

    .sa-notif-head-row {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .sa-notif-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sa-notif-title-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: rgba(255, 255, 255, .18);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, .22);
    }

    .sa-notif-title {
        margin: 0;
        color: #fff;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 900;
    }

    .sa-notif-subtitle {
        margin: 3px 0 0;
        color: rgba(255, 255, 255, .82);
        font-size: 12px;
        font-weight: 700;
    }

    .sa-notif-close {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .22);
        transition: background .18s ease, transform .18s ease;
        position: relative;
        z-index: 2;
    }

    .sa-notif-close:hover {
        background: rgba(255, 255, 255, .24);
        transform: scale(1.04);
    }

    .sa-notif-tools {
        padding: 14px 16px;
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .sa-notif-search {
        position: relative;
        margin-bottom: 12px;
    }

    .sa-notif-search i,
    .sa-notif-search svg {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        pointer-events: none;
    }

    .sa-notif-search input {
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
        transition: border-color .18s ease, background .18s ease, box-shadow .18s ease;
    }

    .sa-notif-search input:focus {
        background: var(--bg-surface);
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 3px rgba(116, 178, 212, .20);
    }

    .sa-notif-filter-row {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: none;
    }

    .sa-notif-filter-row::-webkit-scrollbar {
        display: none;
    }

    .sa-notif-chip {
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
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .sa-notif-chip:hover,
    .sa-notif-chip.active {
        background: rgba(147, 194, 28, .13);
        border-color: rgba(147, 194, 28, .45);
        color: var(--brand-green);
    }

    .sa-notif-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 14px;
    }

    .sa-notif-footer {
        padding: 12px 14px;
        background: var(--bg-surface);
        border-top: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .sa-notif-footer-btn {
        width: 100%;
        height: 42px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        background: var(--bg-hover);
        color: var(--brand-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 900;
        transition: background .18s ease, border-color .18s ease, transform .18s ease;
    }

    .sa-notif-footer-btn:hover {
        background: var(--bg-surface);
        border-color: var(--brand-blue);
        transform: translateY(-1px);
    }

    .sa-notif-state {
        padding: 34px 18px;
        text-align: center;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 700;
    }

    .sa-notif-state-icon {
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

    .sa-notif-card {
        position: relative;
        width: 100%;
        text-align: left;
        display: flex;
        gap: 12px;
        padding: 13px;
        margin-bottom: 10px;
        border-radius: 18px;
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
        color: var(--text-main);
        box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        transition: transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease;
    }

    .sa-notif-card:hover {
        transform: translateY(-2px);
        border-color: rgba(116, 178, 212, .65);
        box-shadow: 0 14px 32px rgba(15, 23, 42, .08);
    }

    .sa-notif-card.unread {
        background: rgba(147, 194, 28, .08);
        border-color: rgba(147, 194, 28, .28);
    }

    .sa-notif-unread-dot {
        position: absolute;
        right: 14px;
        top: 14px;
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: var(--brand-green);
        box-shadow: 0 0 0 4px rgba(147, 194, 28, .16);
    }

    .sa-notif-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: rgba(116, 178, 212, .14);
        color: var(--brand-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border: 1px solid rgba(116, 178, 212, .22);
    }

    .sa-notif-card.unread .sa-notif-icon {
        background: rgba(147, 194, 28, .14);
        color: var(--brand-green);
        border-color: rgba(147, 194, 28, .25);
    }

    .sa-notif-content {
        flex: 1;
        min-width: 0;
        padding-right: 10px;
    }

    .sa-notif-card-top {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
    }

    .sa-notif-type {
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(116, 178, 212, .12);
        color: var(--brand-blue);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .03em;
        white-space: nowrap;
    }

    .sa-notif-time {
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
        margin-left: auto;
    }

    .sa-notif-card-title {
        margin: 0;
        color: var(--text-main);
        font-size: 14px;
        font-weight: 900;
        line-height: 1.25;
    }

    .sa-notif-card-text {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.45;
    }

    .sa-notif-actions {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .sa-notif-action-btn {
        height: 30px;
        padding: 0 10px;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .sa-notif-action-btn:hover {
        background: var(--bg-hover);
        border-color: var(--brand-blue);
        color: var(--brand-blue);
    }

    .sa-notif-action-btn.primary {
        background: rgba(116, 178, 212, .12);
        color: var(--brand-blue);
        border-color: rgba(116, 178, 212, .24);
    }

    /* Quick dropdown */
    .sa-notif-quick-item {
        width: 100%;
        padding: 12px;
        border-bottom: 1px solid var(--border-light);
        background: var(--bg-surface);
        color: var(--text-main);
        display: flex;
        gap: 10px;
        text-align: left;
        cursor: pointer;
        transition: background .18s ease;
        position: relative;
    }

    .sa-notif-quick-item:hover {
        background: var(--bg-hover);
    }

    .sa-notif-quick-item.unread {
        background: rgba(147, 194, 28, .07);
    }

    .sa-notif-quick-icon {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        background: rgba(116, 178, 212, .14);
        color: var(--brand-blue);
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .sa-notif-quick-title {
        margin: 0;
        font-size: 12px;
        color: var(--text-main);
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-notif-quick-text {
        margin: 3px 0 0;
        color: var(--text-muted);
        font-size: 11px;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .sa-notif-card,
    .dark .sa-notif-sider {
        box-shadow: none;
    }

    @media (max-width: 575px) {
        .sa-notif-sider {
            width: 100vw;
        }
    }
</style>

<div id="notificationSidebarOverlay"
     class="sa-notif-backdrop"
     onclick="toggleNotificationSidebar()"></div>

<aside id="notificationSidebar"
       class="sa-notif-sider"
       aria-hidden="true">

    <div class="sa-notif-head">
        <div class="sa-notif-head-row">
            <div class="sa-notif-title-wrap">
                <div class="sa-notif-title-icon">
                    <i data-lucide="bell" class="icon-lg"></i>
                </div>

                <div style="min-width:0;">
                    <h5 class="sa-notif-title">Posteingang</h5>
                    <p class="sa-notif-subtitle">
                        Benachrichtigungen, Aufgaben und Systemmeldungen
                    </p>
                </div>
            </div>

            <button type="button"
                    class="sa-notif-close"
                    onclick="toggleNotificationSidebar()"
                    title="Schließen">
                <i data-lucide="x" class="icon-lg"></i>
            </button>
        </div>
    </div>

    <div class="sa-notif-tools">
        <div class="sa-notif-search">
            <i data-lucide="search"></i>
            <input type="text"
                   id="notifSidebarSearch"
                   placeholder="Benachrichtigungen filtern..."
                   autocomplete="off">
        </div>

        <div id="notifFilters" class="sa-notif-filter-row">
            <button type="button" class="sa-notif-chip active" data-filter="all">
                <i data-lucide="inbox" class="icon-sm"></i> Alle
            </button>

            <button type="button" class="sa-notif-chip" data-filter="unread">
                <i data-lucide="mail" class="icon-sm"></i> Ungelesen
            </button>

            <button type="button" class="sa-notif-chip" data-filter="lead">
                <i data-lucide="user-plus" class="icon-sm"></i> Leads
            </button>

            <button type="button" class="sa-notif-chip" data-filter="inquiry">
                <i data-lucide="help-circle" class="icon-sm"></i> Anfragen
            </button>

            <button type="button" class="sa-notif-chip" data-filter="appointment">
                <i data-lucide="calendar" class="icon-sm"></i> Termine
            </button>

            <button type="button" class="sa-notif-chip" data-filter="ticket">
                <i data-lucide="alert-triangle" class="icon-sm"></i> Tickets
            </button>

            <button type="button" class="sa-notif-chip" data-filter="task">
                <i data-lucide="check-square" class="icon-sm"></i> Aufgaben
            </button>
        </div>
    </div>

    <div id="notificationSidebarList" class="sa-notif-body">
        <div class="sa-notif-state">
            <div class="sa-notif-state-icon">
                <i data-lucide="loader-circle" class="icon-lg"></i>
            </div>
            Benachrichtigungen werden geladen...
        </div>
    </div>

    <div class="sa-notif-footer">
        <button type="button"
                onclick="markAllNotificationsRead()"
                class="sa-notif-footer-btn">
            <i data-lucide="check-check" class="icon-sm"></i>
            Alle als gelesen markieren
        </button>
    </div>
</aside>
