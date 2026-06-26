<style>
    .phase-drawer-shell {
        --ph-green: #93c21c;
        --ph-green-soft: #cfe09b;
        --ph-blue: #74b2d4;
        --ph-blue-soft: #c0d8ea;
        --ph-orange: #f8ac00;
        --ph-pink: #e50656;
        --ph-bg: #ffffff;
        --ph-soft: #f8fafc;
        --ph-text: #374151;
        --ph-muted: #6b7280;
        --ph-border: #c0d8ea;
        --ph-danger: #ef4444;
        --ph-warning: #f59e0b;
        --ph-success: #10b981;

        padding: 12px 16px 18px;
        background: #ffffff;
        font-size: 13px;
        color: var(--ph-text);
        max-width: 100%;
        overflow-x: hidden;
    }

    .phase-drawer-shell *,
    .phase-drawer-shell *::before,
    .phase-drawer-shell *::after {
        box-sizing: border-box;
        box-shadow: none !important;
    }

    .phase-drawer-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .phase-drawer-title {
        margin: 0;
        color: var(--ph-blue);
        font-size: 22px;
        font-weight: 950;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .phase-drawer-subtitle {
        margin-top: 4px;
        color: var(--ph-muted);
        font-size: 12px;
        line-height: 1.45;
    }

    .phase-close-main {
        border: 1px solid var(--ph-border);
        background: #ffffff;
        color: var(--ph-text);
        border-radius: 999px;
        min-height: 38px;
        padding: 8px 13px;
        font-size: 12px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
    }

    .phase-close-main:hover {
        background: var(--ph-blue);
        color: #ffffff;
        border-color: var(--ph-blue);
    }

    .phase-header-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .phase-chip {
        background: #ffffff;
        border: 1px solid var(--ph-border);
        border-radius: 18px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-height: 94px;
        overflow: hidden;
    }

    .phase-chip-title {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--ph-blue);
        font-weight: 950;
    }

    .phase-chip-main {
        font-size: 13px;
        color: var(--ph-text);
        font-weight: 850;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .phase-chip-sub {
        font-size: 12px;
        color: var(--ph-muted);
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .phase-customer-main {
        text-transform: uppercase;
    }

    .phase-product-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .phase-product-initial {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 999px;
        background: var(--ph-blue);
        color: #ffffff;
        font-weight: 950;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .phase-product-avatar {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid #ffffff;
        margin-left: -16px;
        background: var(--ph-blue-soft);
    }

    .phase-product-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .phase-note-teaser {
        cursor: pointer;
        max-height: 58px;
        overflow: hidden;
        position: relative;
        line-height: 1.45;
    }

    .phase-note-teaser::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 16px;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0), #ffffff);
    }

    .phase-summary-line {
        margin: 8px 0 0;
        padding: 6px 10px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 7px;
        background: var(--ph-blue-soft);
        font-size: 11px;
        color: var(--ph-text);
        flex-wrap: wrap;
        line-height: 1.4;
    }

    .phase-active-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        background: rgba(147, 194, 28, .18);
        color: #4d7c0f;
        font-size: 12px;
        font-weight: 950;
        margin-top: 5px;
    }

    .phase-doc-search-card {
        background: #ffffff;
        border: 1px solid var(--ph-border);
        border-radius: 20px;
        margin: 12px 0;
        padding: 12px;
    }

    .phase-doc-search-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .phase-doc-search-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 950;
        color: var(--ph-blue);
    }

    .phase-doc-search-sub {
        margin-top: 4px;
        font-size: 12px;
        color: var(--ph-muted);
        line-height: 1.45;
    }

    .phase-doc-search-toggle,
    .phase-doc-btn {
        border: 1px solid var(--ph-border);
        background: #ffffff;
        color: var(--ph-text);
        border-radius: 999px;
        min-height: 38px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 950;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
        white-space: nowrap;
    }

    .phase-doc-search-toggle:hover,
    .phase-doc-btn:hover {
        background: var(--ph-blue);
        color: #ffffff;
        border-color: var(--ph-blue);
    }

    .phase-doc-btn-primary {
        background: var(--ph-blue);
        color: #ffffff;
        border-color: var(--ph-blue);
    }

    .phase-doc-btn-soft {
        background: #f3f4f6;
        color: var(--ph-text);
        border-color: #e5e7eb;
    }

    .phase-doc-search-body {
        display: none;
        margin-top: 12px;
        border-top: 1px dashed var(--ph-border);
        padding-top: 12px;
    }

    .phase-doc-search-card.is-open .phase-doc-search-body {
        display: block;
    }

    .phase-doc-search-card.is-open .phase-doc-search-toggle i {
        transform: rotate(180deg);
    }

    .phase-doc-search-grid {
        display: grid;
        grid-template-columns: 1.5fr .8fr .8fr .8fr auto;
        gap: 10px;
        align-items: end;
    }

    .phase-doc-search-grid label {
        display: block;
        margin-bottom: 5px;
        font-size: 10px;
        font-weight: 950;
        color: var(--ph-blue);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .phase-doc-input {
        width: 100%;
        border: 1px solid var(--ph-border) !important;
        border-radius: 999px !important;
        min-height: 38px;
        padding: 8px 12px !important;
        font-size: 12px !important;
        color: var(--ph-text);
        background: #ffffff;
        outline: none;
    }

    .phase-doc-input:focus {
        border-color: var(--ph-orange) !important;
        outline: 3px solid rgba(248, 172, 0, .18);
        outline-offset: 1px;
    }

    .phase-doc-search-actions {
        display: flex;
        gap: 7px;
        align-items: center;
    }

    .phase-doc-results {
        margin-top: 12px;
        display: grid;
        gap: 8px;
    }

    .phase-doc-item {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 10px;
        background: #ffffff;
    }

    .phase-doc-item.is-hidden {
        display: none;
    }

    .phase-doc-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: var(--ph-blue-soft);
        color: var(--ph-blue);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .phase-doc-name {
        font-size: 13px;
        font-weight: 950;
        color: var(--ph-text);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .phase-doc-meta {
        margin-top: 3px;
        font-size: 11px;
        color: var(--ph-muted);
        line-height: 1.45;
    }

    .phase-doc-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        border: 1px solid var(--ph-border);
        background: #ffffff;
        color: var(--ph-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .phase-doc-action-btn:hover {
        background: var(--ph-blue);
        color: #ffffff;
        text-decoration: none;
    }

    .phase-doc-empty {
        border: 1px dashed var(--ph-border);
        border-radius: 16px;
        padding: 16px;
        text-align: center;
        color: var(--ph-muted);
        font-size: 12px;
        font-weight: 850;
    }

    .phase-stage-accordion {
        margin-top: 10px;
    }

    .phase-stage-card {
        background: #ffffff;
        border-radius: 20px;
        margin-bottom: 10px;
        border: 1px solid var(--ph-border);
        overflow: hidden;
    }

    .phase-stage-card.is-filter-hidden {
        display: none;
    }

    .phase-stage-head {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(120px, .8fr) minmax(0, 1.6fr) auto;
        gap: 12px;
        padding: 12px;
        cursor: pointer;
        align-items: center;
        background: #ffffff;
    }

    .phase-stage-head.is-active {
        background: #f8fafc;
    }

    .phase-stage-title {
        font-size: 13px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--ph-blue);
    }

    .phase-stage-progress {
        margin-top: 6px;
    }

    .phase-stage-progress .progress {
        height: 9px;
        border-radius: 999px;
        overflow: hidden;
        background: var(--ph-blue-soft);
    }

    .phase-stage-progress .progress-bar {
        border-radius: 999px;
        font-size: 9px;
        line-height: 9px;
    }

    .phase-avatar-stack {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 2px;
    }

    .phase-avatar-ring {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        border: 2px solid #e5e7eb;
        overflow: hidden;
        margin-left: -8px;
        background: #f9fafb;
    }

    .phase-avatar-ring:first-child {
        margin-left: 0;
    }

    .phase-avatar-ring img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .phase-stage-next {
        font-size: 12px;
        min-width: 0;
    }

    .phase-stage-next .title {
        font-weight: 950;
        color: var(--ph-text);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .phase-stage-next .desc {
        color: var(--ph-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .phase-stage-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }

    .phase-circle-btn {
        width: 38px;
        height: 38px;
        border-radius: 999px !important;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .phase-stage-toggle {
        border-radius: 999px !important;
        padding: 7px 11px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .phase-stage-body {
        border-top: 1px solid var(--ph-border);
        padding: 10px;
        background: #ffffff;
    }

    .phase-stage-panel {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.25s ease, opacity 0.2s ease;
    }

    .phase-stage-panel.is-open {
        opacity: 1;
    }

    .phase-activities-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .phase-activities-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
        min-width: 1180px;
    }

    .phase-activities-table thead th {
        white-space: nowrap;
        padding: 8px;
        background: #f8fafc;
        border-bottom: 1px solid var(--ph-border);
        font-weight: 950;
        color: var(--ph-blue);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .phase-activities-table tbody td {
        padding: 8px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }

    .phase-row-header {
        background: #f1f5f9;
    }

    .phase-row-header td {
        font-weight: 950;
        font-size: 12px;
        color: var(--ph-text);
    }

    .activities-phase.is-filter-hidden {
        display: none;
    }

    .activity-main-title {
        font-weight: 950;
        color: var(--ph-text);
        margin-bottom: 2px;
        overflow-wrap: anywhere;
    }

    .activity-main-desc {
        font-size: 11px;
        color: var(--ph-muted);
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .status-pill-group {
        display: inline-flex;
        flex-direction: column;
        gap: 4px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 4px 8px;
        border: 1px solid transparent;
        font-size: 11px;
        font-weight: 850;
        cursor: pointer;
        user-select: none;
        transition: .15s ease;
        opacity: .62;
        position: relative;
        white-space: nowrap;
    }

    .status-pill::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: transparent;
    }

    .status-pill.is-active {
        opacity: 1;
        transform: translateY(-1px);
    }

    .status-pill.is-active::before {
        background: currentColor;
    }

    .status-pill input[type="radio"] {
        display: none;
    }

    .status-pill-open {
        border-color: #e5e7eb;
        background: #f9fafb;
        color: #4b5563;
    }

    .status-pill-half {
        border-color: #fbbf24;
        background: #fffbeb;
        color: #92400e;
    }

    .status-pill-done {
        border-color: #22c55e;
        background: #ecfdf3;
        color: #166534;
    }

    .status-pill-open.is-active {
        border-color: #60a5fa;
        background: #e0f2fe;
        color: #0f172a;
    }

    .status-pill-half.is-active {
        border-color: #facc15;
        background: #fef3c7;
        color: #92400e;
    }

    .status-pill-done.is-active {
        border-color: #22c55e;
        background: #dcfce7;
        color: #14532d;
    }

    .duration-wrapper {
        font-size: 11px;
    }

    .duration-display {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .duration-edit {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .d-time-cell {
        min-width: 80px;
    }

    .d-time-cell small {
        display: block;
    }

    .mark-by-cell .badge {
        font-size: 11px;
    }

    .note-textarea {
        min-width: 170px;
        border-radius: 12px !important;
        font-size: 12px !important;
    }

    .upload-icon {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        border: 1px solid var(--ph-border);
        background: #ffffff;
        color: var(--ph-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin: 0;
    }

    .upload-icon:hover {
        background: var(--ph-blue);
        color: #ffffff;
    }

    .activity-doc-current {
        margin-left: 7px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        color: var(--ph-green);
        font-weight: 900;
        max-width: 120px;
    }

    .activity-doc-current span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .phase-mobile-activity-list {
        display: none;
    }

    .phase-mobile-card {
        border: 1px solid var(--ph-border);
        border-radius: 18px;
        background: #ffffff;
        padding: 12px;
        margin-bottom: 10px;
    }

    .phase-mobile-card.is-filter-hidden {
        display: none;
    }

    .phase-mobile-phase-title {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 9px 10px;
        margin: 8px 0;
        font-weight: 950;
        color: var(--ph-blue);
    }

    .phase-mobile-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 9px;
    }

    .phase-mobile-index {
        width: 32px;
        height: 32px;
        border-radius: 12px;
        background: var(--ph-blue-soft);
        color: var(--ph-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 950;
        flex: 0 0 auto;
    }

    .phase-mobile-title {
        font-weight: 950;
        color: var(--ph-text);
        line-height: 1.35;
    }

    .phase-mobile-desc {
        color: var(--ph-muted);
        font-size: 12px;
        line-height: 1.4;
        margin-top: 3px;
    }

    .phase-mobile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 9px;
    }

    .phase-mobile-field {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 14px;
        padding: 8px;
        min-width: 0;
    }

    .phase-mobile-label {
        font-size: 10px;
        font-weight: 950;
        color: var(--ph-blue);
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 5px;
    }

    .phase-mobile-full {
        grid-column: 1 / -1;
    }

    .phase-empty {
        border: 1px dashed var(--ph-border);
        border-radius: 16px;
        padding: 18px;
        text-align: center;
        color: var(--ph-muted);
        font-weight: 850;
    }

    @media (max-width: 1200px) {
        .phase-header-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .phase-doc-search-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .phase-doc-search-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 992px) {
        .phase-stage-head {
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr) auto;
        }

        .phase-stage-next {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .phase-drawer-shell {
            padding: 10px;
        }

        .phase-drawer-toolbar,
        .phase-doc-search-head {
            align-items: stretch;
        }

        .phase-header-grid,
        .phase-doc-search-grid {
            grid-template-columns: 1fr;
        }

        .phase-close-main,
        .phase-doc-search-toggle,
        .phase-doc-btn {
            width: 100%;
        }

        .phase-doc-search-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .phase-doc-item {
            grid-template-columns: 38px minmax(0, 1fr);
        }

        .phase-doc-action-btn {
            grid-column: 1 / -1;
            width: 100%;
        }

        .phase-summary-line {
            border-radius: 14px;
            align-items: flex-start;
        }

        .phase-stage-head {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .phase-stage-actions {
            justify-content: stretch;
        }

        .phase-stage-actions .btn,
        .phase-stage-toggle {
            flex: 1;
        }

        .phase-activities-wrap {
            display: none;
        }

        .phase-mobile-activity-list {
            display: block;
        }

        .note-textarea {
            min-width: 100%;
        }
    }

    @media (max-width: 520px) {
        .phase-mobile-grid {
            grid-template-columns: 1fr;
        }

        .phase-product-main {
            align-items: flex-start;
        }

        .phase-chip {
            border-radius: 16px;
        }
    }

    .activity-doc-modal {
    position: fixed;
    inset: 0;
    z-index: 999999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
}

.activity-doc-modal.is-open {
    display: flex;
}

.activity-doc-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, .72);
    backdrop-filter: blur(6px);
}

.activity-doc-dialog {
    position: relative;
    width: min(1100px, 96vw);
    height: min(820px, 92vh);
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid var(--ph-border, #c0d8ea);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.activity-doc-header {
    padding: 12px 14px;
    border-bottom: 1px solid var(--ph-border, #c0d8ea);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    background: #ffffff;
}

.activity-doc-title {
    font-size: 15px;
    font-weight: 950;
    color: var(--ph-blue, #74b2d4);
}

.activity-doc-subtitle {
    margin-top: 3px;
    font-size: 12px;
    color: var(--ph-muted, #6b7280);
    max-width: 560px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.activity-doc-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.activity-doc-open-btn,
.activity-doc-close-btn {
    min-height: 38px;
    border-radius: 999px;
    border: 1px solid var(--ph-border, #c0d8ea);
    background: #ffffff;
    color: var(--ph-text, #374151);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 950;
    text-decoration: none;
    cursor: pointer;
}

.activity-doc-open-btn:hover,
.activity-doc-close-btn:hover {
    background: var(--ph-blue, #74b2d4);
    border-color: var(--ph-blue, #74b2d4);
    color: #ffffff;
    text-decoration: none;
}

.activity-doc-close-btn {
    width: 38px;
    padding: 0;
}

.activity-doc-body {
    flex: 1;
    min-height: 0;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: auto;
}

.activity-doc-body img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    display: block;
}

.activity-doc-body iframe {
    width: 100%;
    height: 100%;
    border: 0;
    background: #ffffff;
}

.activity-doc-loading,
.activity-doc-unsupported {
    text-align: center;
    color: var(--ph-muted, #6b7280);
    font-size: 13px;
    font-weight: 850;
    padding: 22px;
}

.activity-doc-file-card {
    width: min(430px, 92%);
    border: 1px solid var(--ph-border, #c0d8ea);
    background: #ffffff;
    border-radius: 22px;
    padding: 24px;
    text-align: center;
}

.activity-doc-file-card i,
.activity-doc-file-card svg {
    width: 54px;
    height: 54px;
    color: var(--ph-blue, #74b2d4);
    margin-bottom: 12px;
}

.activity-doc-file-name {
    font-size: 14px;
    font-weight: 950;
    color: var(--ph-text, #374151);
    overflow-wrap: anywhere;
}

.activity-doc-file-note {
    margin-top: 8px;
    color: var(--ph-muted, #6b7280);
    font-size: 12px;
    line-height: 1.5;
}

.activity-doc-preview-btn {
    margin-left: 7px;
    width: 36px;
    height: 36px;
    border-radius: 999px;
    border: 1px solid var(--ph-border, #c0d8ea);
    background: #ffffff;
    color: var(--ph-blue, #74b2d4);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.activity-doc-preview-btn:hover {
    background: var(--ph-blue, #74b2d4);
    color: #ffffff;
}

@media (max-width: 768px) {
    .activity-doc-modal {
        padding: 8px;
    }

    .activity-doc-dialog {
        width: 100vw;
        height: 96vh;
        border-radius: 18px;
    }

    .activity-doc-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .activity-doc-header-actions {
        width: 100%;
    }

    .activity-doc-open-btn {
        flex: 1;
    }

    .activity-doc-subtitle {
        max-width: 88vw;
    }
}
</style>

@php
    use Illuminate\Support\Str;

    $services = [
        'complete' => 'Komplettlösung',
        'montage' => 'Montage',
        'product' => 'Produkt',
        'plan' => 'Planung',
        'maintenance' => 'Wartung',
        'repair' => 'Reparatur',
        'emergency' => 'Notdienst',
        'others' => 'Sonstiges',
    ];

    $interests = [
        'intent' => 'Kaufabsicht',
        'interest' => 'Kaufinteresse',
        'option' => 'Kaufoption',
    ];

    $translatedStages = [
        'lead' => 'Lead',
        'Lead' => 'Lead',
        'open' => 'Lead',
        'offer' => 'Angebot',
        'deal' => 'Auftrag',
        'project' => 'Montage',
        'ticket' => 'Ticket',
        'review' => 'Auswertung',
        'evaluation' => 'Auswertung',
        'archive' => 'Archiv',
        'completed' => 'Abgeschlossen',
        'complete' => 'Abgeschlossen',
        'junk' => 'Junk',
        'cancel' => 'Absage',
        'pause' => 'Pause',
    ];

    $fmtHM = function ($mins) {
        if ($mins === null)
            return '--:--';

        $mins = (int) $mins;
        $sign = $mins < 0 ? '-' : '';
        $mins = abs($mins);

        return $sign . sprintf('%02d:%02d', intdiv($mins, 60), $mins % 60);
    };

    $employeeImage = function ($image) {
        if (!empty($image)) {
            return asset('images/employee/' . $image);
        }

        return asset('images/gender/male.png');
    };

    $fileIcon = function ($file) {
        $ext = strtolower(pathinfo((string) $file, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => 'file-text',
            'jpg', 'jpeg', 'png' => 'image',
            'doc', 'docx' => 'file',
            default => 'paperclip',
        };
    };

    $publicActivityFileUrl = function ($file) {
        if (!$file) {
            return '#';
        }

        return route('customer.activity-document.show', [
            'filename' => basename($file),
        ]);
    };

    $activeGroup = $groupedPhases[$currentStageKey] ?? collect();

    if ($activeGroup->isEmpty()) {
        $activeGroup = collect($groupedPhases)->first(fn($c) => $c && $c->count()) ?? collect();
    }

    $headerFirstItem = $activeGroup->first();
    $activePhaseId = optional(optional($headerFirstItem)->phase)->id;

    $sumPlan = $sumIs = $sumDiff = null;
    $sumPct = null;

    if (!empty($activePhaseId) && isset($timeSummariesPhase[$activePhaseId])) {
        $ts = $timeSummariesPhase[$activePhaseId];
        $sumPlan = (int) $ts->plan_minutes;
        $sumIs = (int) $ts->actual_minutes;
        $sumDiff = (int) $ts->diff_minutes;
        $sumPct = $ts->weighted_percent ?? ($sumPlan > 0 ? round(($sumDiff / $sumPlan) * 100) : null);
    }

    $diffCls = $sumDiff > 0 ? 'text-danger' : ($sumDiff < 0 ? 'text-success' : 'text-muted');
    $pctCls = is_null($sumPct) ? 'text-muted' : ($sumPct > 0 ? 'text-danger' : ($sumPct < 0 ? 'text-success' : 'text-muted'));
    $iconName = is_null($sumPct) ? 'minus-circle' : ($sumDiff > 0 ? 'thumbs-down' : ($sumDiff < 0 ? 'thumbs-up' : 'check-circle'));
    $iconClass = is_null($sumPct) ? 'text-muted' : ($sumDiff > 0 ? 'text-danger' : ($sumDiff < 0 ? 'text-success' : 'text-secondary'));

    $allStages = collect($groupedPhases)->keys();

    $totalActivities = collect($groupedPhases)
        ->flatten(1)
        ->filter(fn($r) => !empty($r->activity))
        ->count();

    $doneActivities = collect($groupedPhases)
        ->flatten(1)
        ->filter(fn($r) => ($r->is_done ?? null) == 1 && !empty($r->activity))
        ->count();

    $overallPercent = $totalActivities > 0 ? round(($doneActivities / $totalActivities) * 100) : 0;

    $employees = \App\Models\Employee::where('status', 'Active')
        ->select('id', 'name', 'lastname', 'image')
        ->orderBy('name')
        ->get();

    $roleColors = [
        'team' => '#17a2b8',
        'leader' => '#28a745',
        'representative' => '#ffc107',
        'monteur' => '#007bff',
        'obermonteur' => '#6610f2',
        'helper' => '#6c757d',
        'innendienst' => '#fd7e14',
        'aussendienst' => '#20c997',
        'bauleiter' => '#dc3545',
        'buchhaltung' => '#343a40',
        'techniker' => '#6f42c1',
        'controller' => '#e83e8c',
    ];

    $allSuggested = \App\Models\CustomerSuggestEmployee::with(['employee', 'department'])
        ->where('customer_id', $customer_id)
        ->where('alternative_id', $alternative_id)
        ->where('product_id', $productId)
        ->get();

    $documentsForSearch = collect($groupedPhases)
        ->flatten(1)
        ->filter(fn($r) => !empty($r->has_document) && !empty($r->activity))
        ->values();
@endphp

<div class="phase-drawer-shell" data-phase-root data-customer-id="{{ $customer_id }}"
    data-alternative-id="{{ $alternative_id }}" data-product-id="{{ $productId }}" data-service-id="{{ $serviceId }}">

    <div class="phase-drawer-toolbar">
        <div>
            <h3 class="phase-drawer-title">
                <i data-feather="clipboard"></i>
                Kundenaufgaben & Arbeitsprozess
            </h3>

            <div class="phase-drawer-subtitle">
                Version: <strong>{{ $usedVersion ?? '—' }}</strong>
                · Fortschritt, Zuständigkeiten, Zeiten, Dokumente und Notizen.
            </div>
        </div>

        <button type="button" class="phase-close-main" onclick="closePhaseSidebar()">
            <i data-feather="x"></i>
            Schließen
        </button>
    </div>

    <div class="phase-header-grid">
        <div class="phase-chip">
            <div class="phase-chip-title">Kunde</div>

            <div class="phase-chip-main phase-customer-main">
                {{ trim(($customer->title ?? '') . ' ' . ($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) ?: 'Unbekannter Kunde' }}
            </div>

            @if(!empty($customer->firma))
                <div class="phase-chip-sub">Firma: {{ $customer->firma }}</div>
            @endif

            <div class="phase-chip-sub">
                @if(!empty($customer->created_at))
                    Angelegt am {{ \Carbon\Carbon::parse($customer->created_at)->format('d.m.Y') }}
                @endif

                @if(!empty($customer->source))
                    · Quelle: {{ $customer->source }}
                @endif
            </div>
        </div>

        <div class="phase-chip">
            <div class="phase-chip-title">Adresse</div>

            <div class="phase-chip-main">
                {{ $customer->street ?? '—' }}<br>
                {{ $customer->postcode ?? '' }} {{ $customer->city ?? '' }}
            </div>

            <div class="phase-chip-sub">
                {{ $customer->email ?? 'Keine E-Mail' }}<br>
                {{ $customer->phone ?? 'Keine Telefonnummer' }}
                @if(!empty($customer->mobile))
                    · {{ $customer->mobile }}
                @endif
            </div>
        </div>

        <div class="phase-chip">
            <div class="phase-chip-title">Produkt & Status</div>

            <div class="phase-product-main">
                <div class="phase-product-initial">
                    {{ $productList->initial ?? 'NA' }}
                </div>

                @if(!empty($productList->image))
                    <div class="phase-product-avatar">
                        <img src="{{ asset('images/employee/' . $productList->image) }}" alt="">
                    </div>
                @endif

                <div style="min-width:0;">
                    <div class="phase-chip-main">
                        {{ $productList->department_name ?? 'Keine Abteilung' }}
                    </div>

                    <div class="phase-chip-sub">
                        {{ $services[$productList->phase_section ?? ''] ?? ($productList->phase_section ?? '—') }}
                        ·
                        {{ $interests[$productList->interest ?? ''] ?? ($productList->interest ?? '—') }}
                    </div>
                </div>
            </div>

            <div class="phase-summary-line">
                <span><strong>Gesamt:</strong> {{ $doneActivities }}/{{ $totalActivities }}</span>
                <span>·</span>
                <span><strong>{{ $overallPercent }}%</strong> erledigt</span>

                @if($activePhaseId)
                    <span>·</span>
                    <span>
                        <strong>P:</strong> {{ $fmtHM($sumPlan) }}
                        · <strong>I:</strong> {{ $fmtHM($sumIs) }}
                        · <strong>D:</strong>
                        <span class="{{ $diffCls }}">
                            {{ $sumDiff > 0 ? '+' : '' }}{{ $fmtHM($sumDiff) }}
                        </span>
                        · <strong>%:</strong>
                        <span class="{{ $pctCls }}">
                            {{ is_null($sumPct) ? '--' : (($sumPct > 0 ? '+' : '') . $sumPct . '%') }}
                        </span>
                    </span>

                    <i class="feather icon-{{ $iconName }} {{ $iconClass }}"></i>
                @endif
            </div>
        </div>

        <div class="phase-chip">
            <div class="phase-chip-title">Projekt-Notiz & Phase</div>

            <div id="noteContainer" class="mb-2" data-customer="{{ $customer_id }}"
                data-alternative="{{ $alternative_id }}" data-product="{{ $productId }}"
                data-title="{{ $note->title ?? '' }}" data-description="{{ $note->description ?? '' }}">
                @if($note)
                    <div id="noteView" onclick="openNoteEditor()" style="cursor:pointer">
                        <div class="phase-chip-main" id="noteTitle">
                            {{ $note->title ?: 'Ohne Titel' }}
                        </div>

                        <div class="phase-note-teaser" id="noteDescription">
                            {!! nl2br(e($note->description ?: 'Keine Beschreibung')) !!}
                        </div>
                    </div>
                @else
                    <div class="text-muted phase-note-teaser" onclick="openNoteEditor()" style="cursor:pointer">
                        <i class="fas fa-pen"></i>
                        Klicken Sie hier, um eine Notiz hinzuzufügen
                    </div>
                @endif
            </div>

            <div class="phase-active-pill">
                <i data-feather="git-branch"></i>
                {{ $translatedStages[$stage] ?? ucfirst((string) $stage) }}
            </div>
        </div>
    </div>

    <div class="phase-doc-search-card" data-phase-doc-search-card>
        <div class="phase-doc-search-head">
            <div>
                <div class="phase-doc-search-title">
                    <i data-feather="search"></i>
                    Suche & Dokumente
                </div>

                <div class="phase-doc-search-sub">
                    Suche nach Aufgabe, Phase, Status, Zuständigkeit, Notiz oder hochgeladenem Dokument.
                </div>
            </div>

            <button type="button" class="phase-doc-search-toggle" onclick="togglePhaseDocumentSearch(this)"
                aria-expanded="false">
                <i data-feather="chevron-down"></i>
                Suche öffnen
            </button>
        </div>

        <div class="phase-doc-search-body" data-phase-document-search-body>
            <div class="phase-doc-search-grid">
                <div>
                    <label>Suche</label>
                    <input type="search" class="form-control phase-doc-input" data-doc-search-q
                        placeholder="Aufgabe, Phase, Dokument, Mitarbeiter, Notiz...">
                </div>

                <div>
                    <label>Status</label>
                    <select class="form-control phase-doc-input" data-doc-search-status>
                        <option value="">Alle</option>
                        <option value="open">Offen</option>
                        <option value="half">Teilweise</option>
                        <option value="done">Komplett</option>
                    </select>
                </div>

                <div>
                    <label>Dokument</label>
                    <select class="form-control phase-doc-input" data-doc-search-document>
                        <option value="">Alle</option>
                        <option value="yes">Mit Dokument</option>
                        <option value="no">Ohne Dokument</option>
                    </select>
                </div>

                <div>
                    <label>Datum</label>
                    <input type="date" class="form-control phase-doc-input" data-doc-search-date>
                </div>

                <div class="phase-doc-search-actions">
                    <button type="button" class="phase-doc-btn phase-doc-btn-primary"
                        onclick="runPhaseActivitySearch()">
                        <i data-feather="search"></i>
                        Suchen
                    </button>

                    <button type="button" class="phase-doc-btn phase-doc-btn-soft" onclick="resetPhaseActivitySearch()">
                        <i data-feather="x-circle"></i>
                        Reset
                    </button>
                </div>
            </div>

            <div class="phase-doc-results" data-phase-document-results>
                @if($documentsForSearch->isEmpty())
                    <div class="phase-doc-empty">
                        Noch keine Aktivitätsdokumente hochgeladen.
                    </div>
                @else
                    @foreach($documentsForSearch as $docRow)
                        @php
        $docActivity = $docRow->activity;
        $docPhase = $docRow->phase;
        $docName = $docRow->has_document;
        $docExt = strtolower(pathinfo((string) $docName, PATHINFO_EXTENSION));
        $docIcon = $fileIcon($docName);
                        @endphp

                        <div class="phase-doc-item" data-doc-item
                            data-search-text="{{ strtolower(($docName ?? '') . ' ' . ($docActivity->title ?? '') . ' ' . ($docActivity->description ?? '') . ' ' . ($docPhase->phase_name ?? '') . ' ' . ($docRow->stage_name ?? '')) }}"
                            data-doc-ext="{{ $docExt }}"
                            data-doc-date="{{ $docRow->done_date ? \Carbon\Carbon::parse($docRow->done_date)->format('Y-m-d') : '' }}">
                            <div class="phase-doc-icon">
                                <i data-feather="{{ $docIcon }}"></i>
                            </div>

                            <div style="min-width:0;">
                                <div class="phase-doc-name">
                                    {{ $docName }}
                                </div>

                                <div class="phase-doc-meta">
                                    {{ $docPhase->phase_name ?? 'Keine Phase' }}
                                    ·
                                    {{ $docActivity->title ?? 'Keine Aufgabe' }}
                                </div>
                            </div>

                            <a href="{{ $publicActivityFileUrl($docName) }}" target="_blank" class="phase-doc-action-btn"
                                title="Dokument öffnen">
                                <i data-feather="external-link"></i>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="phase-stage-accordion" data-nx-accordion="stages">
        @forelse ($allStages as $stageKey)
                    @php
            $phaseGroup = $groupedPhases[$stageKey] ?? collect();
            $firstItem = $phaseGroup->first();

            $activeStageKey = $stage ?? $currentStageKey;
            $isCurrentStage = (string) $activeStageKey === (string) $stageKey;

            $stageLabel = strtoupper($translatedStages[$stageKey] ?? $stageKey);

            $total = $phaseGroup->whereNotNull('activity')->count();
            $doneCount = $phaseGroup->filter(fn($r) => ($r->is_done ?? null) == 1 && $r->activity)->count();
            $pct = $total ? round(($doneCount / $total) * 100) : 0;

            $phaseId = optional(optional($firstItem)->phase)->id;
            $productIdBlock = optional($firstItem)->product_id ?? $productId;
            $stageId = optional($firstItem)->stage_id ?? null;

            $safeStageKey = Str::slug($stageKey, '-') ?: 'stage';
            $panelId = "stage-panel-{$safeStageKey}-{$loop->index}";

            $suggestedEmployees = $phaseId
                ? $allSuggested->where('phase_id', $phaseId)
                : collect();

            $phasesInStage = $phaseGroup->groupBy(fn($r) => optional($r->phase)->id);

            $nextRealActivity = null;

            if ($phaseId) {
                $phaseActsForPhase = \App\Models\PhaseActivities::query()
                    ->where('phase_id', $phaseId)
                    ->when(\Illuminate\Support\Facades\Schema::hasColumn('phase_activities', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                    ->orderBy('sort_order')
                    ->get();

                foreach ($phaseActsForPhase as $act) {
                    $isDone = \App\Models\CustomerHistory::where([
                        ['customer_id', $customer_id],
                        ['alternative_id', $alternative_id],
                        ['product_id', $productIdBlock],
                        ['phase_id', $phaseId],
                        ['activity_id', $act->id],
                        ['section_id', $serviceId],
                        ['is_done', 1],
                    ])->exists();

                    if (!$isDone) {
                        $nextRealActivity = $act;
                        break;
                    }
                }
            }
                    @endphp

                    <div class="phase-stage-card" data-stage-key="{{ $stageKey }}">
                        <div class="phase-stage-head {{ $isCurrentStage ? 'is-active' : '' }}" role="button"
                            data-panel-id="{{ $panelId }}" aria-expanded="{{ $isCurrentStage ? 'true' : 'false' }}">
                            <div>
                                <div class="phase-stage-title">{{ $stageLabel }}</div>

                                <div class="phase-stage-progress">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $pct }}%">
                                            {{ $doneCount }}/{{ $total }} · {{ $pct }}%
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="text-muted mb-1 font-weight-bold" style="font-size:11px;">
                                    Mitarbeiter
                                </div>

                                <div class="phase-avatar-stack">
                                    @if($phaseId)
                                        @foreach($suggestedEmployees->take(6) as $sug)
                                            @php
                    $emp = $sug->employee;
                    $color = $roleColors[$sug->role ?? ''] ?? '#cbd5e1';
                                            @endphp

                                            @if($emp)
                                                <button type="button" class="btn p-0 border-0 bg-transparent edit-suggested-employee"
                                                    title="{{ $emp->name }} {{ $emp->lastname }} – {{ $sug->department->department_name ?? '—' }} ({{ $sug->role ?? '—' }})"
                                                    data-suggestion-id="{{ $sug->id }}" data-employee-id="{{ $emp->id }}"
                                                    data-employee-name="{{ $emp->name }} {{ $emp->lastname }}"
                                                    data-customer-id="{{ $customer_id }}" data-alternative-id="{{ $alternative_id }}"
                                                    data-product-id="{{ $productIdBlock }}" data-phase-id="{{ $phaseId }}"
                                                    data-role="{{ $sug->role }}" data-department-id="{{ $sug->department_id }}">
                                                    <div class="phase-avatar-ring" style="border-color: {{ $color }}">
                                                        <img src="{{ $employeeImage($emp->image) }}"
                                                            alt="{{ $emp->name }} {{ $emp->lastname }}">
                                                    </div>
                                                </button>
                                            @endif
                                        @endforeach

                                        <button type="button" class="btn btn-sm btn-warning ml-1 suggest-employees-btn"
                                            data-customer-id="{{ $customer_id }}" data-alternative-id="{{ $alternative_id }}"
                                            data-product-id="{{ $productIdBlock }}" data-phase-id="{{ $phaseId }}">
                                            <i data-feather="user-plus"></i>
                                        </button>
                                    @else
                                        <span class="text-muted" style="font-size:11px;">
                                            Keine Phase / Mitarbeiterzuordnung vorhanden
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="phase-stage-next">
                                <div class="text-muted mb-1 font-weight-bold" style="font-size:11px;">
                                    Nächster Schritt
                                </div>

                                @if($phaseGroup->isEmpty())
                                    <div class="text-muted">
                                        Für diese Phase wurden noch keine Aufgaben definiert.
                                    </div>
                                @else
                                    @if($nextRealActivity)
                                        <div class="title">{{ $nextRealActivity->title }}</div>
                                        <div class="desc" title="{{ $nextRealActivity->description }}">
                                            {{ $nextRealActivity->description }}
                                        </div>
                                    @else
                                        <div class="text-muted">Alle Schritte erledigt</div>
                                    @endif
                                @endif
                            </div>

                            <div class="phase-stage-actions">
                                @if($firstItem && !empty($productIdBlock))
                                    <button type="button" class="btn btn-outline-danger phase-circle-btn change_stages"
                                        data-customer-id="{{ $customer_id }}" data-alternative-id="{{ $alternative_id }}"
                                        data-product-id="{{ $productIdBlock }}" data-phase-id="{{ $phaseId }}"
                                        data-stage="{{ $stageId }}" data-service="{{ $firstItem->service ?? null }}"
                                        data-service-id="{{ $firstItem->service_id ?? null }}"
                                        data-employee-id="{{ $firstItem->employee_id ?? 0 }}"
                                        data-department-id="{{ $firstItem->department_id ?? 0 }}">
                                        <i data-feather="git-branch"></i>
                                    </button>
                                @endif

                                <button type="button" class="btn btn-outline-primary phase-stage-toggle"
                                    data-panel-id="{{ $panelId }}" aria-expanded="{{ $isCurrentStage ? 'true' : 'false' }}">
                                    <i data-feather="chevron-{{ $isCurrentStage ? 'up' : 'down' }}"></i>
                                </button>
                            </div>
                        </div>

                        <div id="{{ $panelId }}" class="phase-stage-panel {{ $isCurrentStage ? 'is-open' : '' }}" role="region">
                            <div class="phase-stage-body">
                                @if($phaseGroup->isEmpty())
                                    <div class="phase-empty">
                                        Für diese Phase wurden noch keine Aufgaben definiert.
                                    </div>
                                @else
                                                    <div class="phase-activities-wrap">
                                                        <table class="phase-activities-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Aufgabe</th>
                                                                    <th>Status</th>
                                                                    <th>Plan</th>
                                                                    <th>Ist</th>
                                                                    <th>Diff</th>
                                                                    <th>Erledigt am</th>
                                                                    <th>Markiert von</th>
                                                                    <th>Zuständig</th>
                                                                    <th>Dokument</th>
                                                                    <th>Notiz</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                @foreach($phasesInStage as $phaseIdBlock => $rows)
                                                                                                    @php
                                                                    $phaseObj = optional($rows->first())->phase;
                                                                                                    @endphp

                                                                                                    @if($phaseObj)
                                                                                                        <tr class="phase-row-header">
                                                                                                            <td colspan="11">
                                                                                                                <strong>{{ $phaseObj->phase_name }}</strong>

                                                                                                                @if($phaseObj->phase_description)
                                                                                                                    <span class="text-muted">
                                                                                                                        · {{ $phaseObj->phase_description }}
                                                                                                                    </span>
                                                                                                                @endif
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endif

                                                                                                    @foreach($rows as $index => $row)
                                                                                                                                        @php
                                                                                                        $activity = $row->activity;
                                                                                                        if (!$activity)
                                                                                                            continue;

                                                                                                        $history = $phaseActs->first(function ($h) use ($row, $activity) {
                                                                                                            return $h->phase_id == $row->phase->id
                                                                                                                && $h->activity_id == $activity->id;
                                                                                                        });

                                                                                                        $isDone = (string) $row->is_done === '1';
                                                                                                        $isHalf = (string) $row->is_done === 'half'
                                                                                                            || (!empty($row->done_reason) && is_array($row->done_reason));

                                                                                                        if ($isDone) {
                                                                                                            $statusValue = 'done';
                                                                                                        } elseif ($isHalf || !empty($row->is_time)) {
                                                                                                            $statusValue = 'half';
                                                                                                        } else {
                                                                                                            $statusValue = 'open';
                                                                                                        }

                                                                                                        $doneBy = $history?->done_by ? \App\Models\Employee::find($history->done_by) : null;
                                                                                                        $markedBy = $history?->marked_by ? \App\Models\Employee::find($history->marked_by) : null;

                                                                                                        $rowSearchText = strtolower(
                                                                                                            ($activity->title ?? '') . ' ' .
                                                                                                            ($activity->description ?? '') . ' ' .
                                                                                                            ($phaseObj->phase_name ?? '') . ' ' .
                                                                                                            ($phaseObj->phase_description ?? '') . ' ' .
                                                                                                            ($row->has_document ?? '') . ' ' .
                                                                                                            ($row->stage_name ?? '') . ' ' .
                                                                                                            ($doneBy?->name ?? '') . ' ' .
                                                                                                            ($doneBy?->lastname ?? '') . ' ' .
                                                                                                            ($markedBy?->name ?? '') . ' ' .
                                                                                                            ($markedBy?->lastname ?? '') . ' ' .
                                                                                                            (is_array($row->notes) ? implode(' ', $row->notes) : ($row->notes ?? ''))
                                                                                                        );

                                                                                                        $rowDoneDate = $row->done_date ? \Carbon\Carbon::parse($row->done_date)->format('Y-m-d') : '';
                                                                                                        $hasDocument = !empty($row->has_document);
                                                                                                                                        @endphp

                                                                                                                                        <tr class="activities-phase" data-activity-id="{{ $activity->id }}"
                                                                                                                                            data-phase-id="{{ $row->phase->id }}" data-customer-id="{{ $customer_id }}"
                                                                                                                                            data-alternative-id="{{ $alternative_id }}" data-product-id="{{ $productId }}"
                                                                                                                                            data-service-id="{{ $serviceId }}" data-search-text="{{ e($rowSearchText) }}"
                                                                                                                                            data-search-status="{{ $statusValue }}"
                                                                                                                                            data-search-document="{{ $hasDocument ? 'yes' : 'no' }}"
                                                                                                                                            data-search-date="{{ $rowDoneDate }}">
                                                                                                                                            <td>{{ $activity->sort_order ?? $loop->iteration }}</td>

                                                                                                                                            <td style="padding-left:0;">
                                                                                                                                                <div class="activity-main-title">
                                                                                                                                                    {{ $activity->title }}
                                                                                                                                                </div>

                                                                                                                                                @if($activity->description)
                                                                                                                                                    <div class="activity-main-desc">
                                                                                                                                                        {{ $activity->description }}
                                                                                                                                                    </div>
                                                                                                                                                @endif
                                                                                                                                            </td>

                                                                                                                                            <td class="text-center align-middle">
                                                                                                                                                <div class="status-pill-group">
                                                                                                                                                    <label
                                                                                                                                                        class="status-pill status-pill-open {{ $statusValue === 'open' ? 'is-active' : '' }}">
                                                                                                                                                        <input class="status-option" type="radio"
                                                                                                                                                            name="status-{{ $activity->id }}" value="open"
                                                                                                                                                            data-activity-id="{{ $activity->id }}"
                                                                                                                                                            data-phase-id="{{ $row->phase->id }}" {{ $statusValue === 'open' ? 'checked' : '' }}>
                                                                                                                                                        <i data-feather="circle"></i>
                                                                                                                                                        <span>Offen</span>
                                                                                                                                                    </label>

                                                                                                                                                    <label
                                                                                                                                                        class="status-pill status-pill-half {{ $statusValue === 'half' ? 'is-active' : '' }}">
                                                                                                                                                        <input class="status-option" type="radio"
                                                                                                                                                            name="status-{{ $activity->id }}" value="half"
                                                                                                                                                            data-activity-id="{{ $activity->id }}"
                                                                                                                                                            data-phase-id="{{ $row->phase->id }}" {{ $statusValue === 'half' ? 'checked' : '' }}>
                                                                                                                                                        <i data-feather="alert-circle"></i>
                                                                                                                                                        <span>Teilweise</span>
                                                                                                                                                    </label>

                                                                                                                                                    <label
                                                                                                                                                        class="status-pill status-pill-done {{ $statusValue === 'done' ? 'is-active' : '' }}">
                                                                                                                                                        <input class="status-option" type="radio"
                                                                                                                                                            name="status-{{ $activity->id }}" value="1"
                                                                                                                                                            data-activity-id="{{ $activity->id }}"
                                                                                                                                                            data-phase-id="{{ $row->phase->id }}" {{ $statusValue === 'done' ? 'checked' : '' }}>
                                                                                                                                                        <i data-feather="check-circle"></i>
                                                                                                                                                        <span>Komplett</span>
                                                                                                                                                    </label>
                                                                                                                                                </div>
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <div class="duration-wrapper" data-activity-id="{{ $activity->id }}">
                                                                                                                                                    <span class="duration-display">
                                                                                                                                                        {{ $row->plan_time ?? $activity->duration ?? '00:00:00' }}
                                                                                                                                                        <i class="feather icon-edit text-primary ml-1 edit-duration-btn"
                                                                                                                                                            style="cursor:pointer;"></i>
                                                                                                                                                    </span>

                                                                                                                                                    <span class="duration-edit d-none">
                                                                                                                                                        <input type="time" class="form-control form-control-sm duration-input"
                                                                                                                                                            data-type="plan_time"
                                                                                                                                                            value="{{ $row->plan_time ?? $activity->duration ?? '00:00:00' }}"
                                                                                                                                                            style="width:100px;display:inline-block;">

                                                                                                                                                        <button class="btn btn-sm btn-success save-duration-btn">
                                                                                                                                                            <i data-feather="check"></i>
                                                                                                                                                        </button>
                                                                                                                                                    </span>
                                                                                                                                                </div>
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <input type="time" class="form-control form-control-sm" data-type="is_time"
                                                                                                                                                    value="{{ $row->is_time ?? '' }}">
                                                                                                                                            </td>

                                                                                                                                            <td class="d-time-cell">
                                                                                                                                                <p class="mb-0">
                                                                                                                                                    <small class="d-percent-cell text-muted">-</small>
                                                                                                                                                </p>

                                                                                                                                                <p class="mb-0 mt-0">
                                                                                                                                                    <small class="d-share-cell text-muted">-</small>
                                                                                                                                                </p>
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <input type="date" name="history[{{ $activity->id }}][done_date]"
                                                                                                                                                    value="{{ $rowDoneDate }}" class="form-control form-control-sm">
                                                                                                                                            </td>

                                                                                                                                            <td class="mark-by-cell">
                                                                                                                                                @if($markedBy)
                                                                                                                                                    <span class="badge badge-light-primary" data-toggle="tooltip" data-html="true"
                                                                                                                                                        title="{{ $markedBy->name }} {{ $markedBy->lastname }}">
                                                                                                                                                        {{ Str::limit($markedBy->name . ' ' . $markedBy->lastname, 12) }}
                                                                                                                                                    </span>
                                                                                                                                                @else
                                                                                                                                                    <span class="badge badge-light-secondary" data-toggle="tooltip" data-html="true"
                                                                                                                                                        title="Unbekannt">
                                                                                                                                                        –
                                                                                                                                                    </span>
                                                                                                                                                @endif
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <select name="done_by" class="form-control employeeDone done-by-select"
                                                                                                                                                    data-activity-id="{{ $activity->id }}"
                                                                                                                                                    data-phase-id="{{ $row->phase->id }}">
                                                                                                                                                    <option value="">-- Bitte wählen --</option>

                                                                                                                                                    @foreach($employees as $emp)
                                                                                                                                                        <option value="{{ $emp->id }}"
                                                                                                                                                            data-image="{{ $employeeImage($emp->image) }}" {{ $doneBy && $doneBy->id == $emp->id ? 'selected' : '' }}>
                                                                                                                                                            {{ $emp->name }} {{ $emp->lastname }}
                                                                                                                                                        </option>
                                                                                                                                                    @endforeach
                                                                                                                                                </select>
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                                    <form action="{{ url('/activity-document-upload') }}" method="POST"
                                                                                                                                                        enctype="multipart/form-data"
                                                                                                                                                        class="upload-form d-flex align-items-center">
                                                                                                                                                        @csrf

                                                                                                                                                        <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                                                                                                                                                        <input type="hidden" name="alternative_id"
                                                                                                                                                            value="{{ $alternative_id }}">
                                                                                                                                                        <input type="hidden" name="product_id" value="{{ $productId }}">
                                                                                                                                                        <input type="hidden" name="phase_id" value="{{ $row->phase->id }}">
                                                                                                                                                        <input type="hidden" name="task_id" value="{{ $activity->id }}">
                                                                                                                                                        <input type="hidden" name="stage" value="{{ $stageKey }}">

                                                                                                                                                        <label class="upload-icon" title="Datei hochladen">
                                                                                                                                                            <i data-feather="upload-cloud"></i>

                                                                                                                                                            <input type="file" name="document" class="d-none"
                                                                                                                                                                onchange="uploadActivityFile(this)">
                                                                                                                                                        </label>
                                                                                                                                                    </form>

                                                                                                                                                    @if($hasDocument)
                                                                                                                                                        @php
                                                                                                                                                            $docUrl = $publicActivityFileUrl($row->has_document);
                                                                                                                                                            $docExt = strtolower(pathinfo((string) $row->has_document, PATHINFO_EXTENSION));
                                                                                                                                                            $docTitle = $activity->title ?? 'Aktivitätsdokument';
                                                                                                                                                            $docPhaseTitle = $row->phase->phase_name ?? 'Phase';
                                                                                                                                                        @endphp

                                                                                                                                                        <button type="button" class="activity-doc-preview-btn" title="Dokument ansehen" data-doc-url="{{ $docUrl }}"
                                                                                                                                                            data-doc-name="{{ $row->has_document }}" data-doc-ext="{{ $docExt }}" data-doc-title="{{ $docTitle }}"
                                                                                                                                                            data-doc-subtitle="{{ $docPhaseTitle }} · {{ $docTitle }}" onclick="openActivityDocumentModal(this)">
                                                                                                                                                            <i data-feather="eye"></i>
                                                                                                                                                        </button>

                                                                                                                                                        <a href="{{ $docUrl }}" target="_blank" class="activity-doc-current" title="{{ $row->has_document }}">
                                                                                                                                                            <i data-feather="paperclip"></i>
                                                                                                                                                            <span>{{ $row->has_document }}</span>
                                                                                                                                                        </a>
                                                                                                                                                    @endif
                                                                                                                                                </div>
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <textarea class="form-control form-control-sm note-textarea" rows="2"
                                                                                                                                                    data-activity-id="{{ $activity->id }}" data-phase-id="{{ $row->phase->id }}"
                                                                                                                                                    placeholder="Notiz eingeben...">{{ is_array($row->notes) ? implode(' ', $row->notes) : ($row->notes ?? '') }}</textarea>
                                                                                                                                            </td>
                                                                                                                                        </tr>
                                                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="phase-mobile-activity-list">
                                                        @foreach($phasesInStage as $phaseIdBlock => $rows)
                                                            @php
                                    $phaseObj = optional($rows->first())->phase;
                                                            @endphp

                                                            @if($phaseObj)
                                                                <div class="phase-mobile-phase-title">
                                                                    {{ $phaseObj->phase_name }}

                                                                    @if($phaseObj->phase_description)
                                                                        <div class="phase-chip-sub">
                                                                            {{ $phaseObj->phase_description }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            @foreach($rows as $index => $row)
                                                                @php
                                        $activity = $row->activity;
                                        if (!$activity)
                                            continue;

                                        $history = $phaseActs->first(function ($h) use ($row, $activity) {
                                            return $h->phase_id == $row->phase->id
                                                && $h->activity_id == $activity->id;
                                        });

                                        $isDone = (string) $row->is_done === '1';
                                        $isHalf = (string) $row->is_done === 'half'
                                            || (!empty($row->done_reason) && is_array($row->done_reason));

                                        if ($isDone) {
                                            $statusValue = 'done';
                                            $statusLabel = 'Komplett';
                                        } elseif ($isHalf || !empty($row->is_time)) {
                                            $statusValue = 'half';
                                            $statusLabel = 'Teilweise';
                                        } else {
                                            $statusValue = 'open';
                                            $statusLabel = 'Offen';
                                        }

                                        $doneBy = $history?->done_by ? \App\Models\Employee::find($history->done_by) : null;
                                        $markedBy = $history?->marked_by ? \App\Models\Employee::find($history->marked_by) : null;

                                        $rowSearchText = strtolower(
                                            ($activity->title ?? '') . ' ' .
                                            ($activity->description ?? '') . ' ' .
                                            ($phaseObj->phase_name ?? '') . ' ' .
                                            ($phaseObj->phase_description ?? '') . ' ' .
                                            ($row->has_document ?? '') . ' ' .
                                            ($row->stage_name ?? '') . ' ' .
                                            ($doneBy?->name ?? '') . ' ' .
                                            ($doneBy?->lastname ?? '') . ' ' .
                                            ($markedBy?->name ?? '') . ' ' .
                                            ($markedBy?->lastname ?? '') . ' ' .
                                            (is_array($row->notes) ? implode(' ', $row->notes) : ($row->notes ?? ''))
                                        );

                                        $rowDoneDate = $row->done_date ? \Carbon\Carbon::parse($row->done_date)->format('Y-m-d') : '';
                                        $hasDocument = !empty($row->has_document);
                                                                @endphp

                                                                <div class="phase-mobile-card" data-mobile-activity-card data-activity-id="{{ $activity->id }}"
                                                                    data-phase-id="{{ $row->phase->id }}" data-search-text="{{ e($rowSearchText) }}"
                                                                    data-search-status="{{ $statusValue }}"
                                                                    data-search-document="{{ $hasDocument ? 'yes' : 'no' }}"
                                                                    data-search-date="{{ $rowDoneDate }}">
                                                                    <div class="phase-mobile-card-top">
                                                                        <div class="phase-mobile-index">
                                                                            {{ $activity->sort_order ?? $loop->iteration }}
                                                                        </div>

                                                                        <div style="min-width:0; flex:1;">
                                                                            <div class="phase-mobile-title">
                                                                                {{ $activity->title }}
                                                                            </div>

                                                                            @if($activity->description)
                                                                                <div class="phase-mobile-desc">
                                                                                    {{ $activity->description }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="phase-mobile-grid">
                                                                        <div class="phase-mobile-field phase-mobile-full">
                                                                            <div class="phase-mobile-label">Status</div>

                                                                            <div class="status-pill-group">
                                                                                <label
                                                                                    class="status-pill status-pill-open {{ $statusValue === 'open' ? 'is-active' : '' }}">
                                                                                    <input class="status-option" type="radio"
                                                                                        name="m-status-{{ $activity->id }}" value="open"
                                                                                        data-activity-id="{{ $activity->id }}"
                                                                                        data-phase-id="{{ $row->phase->id }}" {{ $statusValue === 'open' ? 'checked' : '' }}>
                                                                                    <i data-feather="circle"></i>
                                                                                    <span>Offen</span>
                                                                                </label>

                                                                                <label
                                                                                    class="status-pill status-pill-half {{ $statusValue === 'half' ? 'is-active' : '' }}">
                                                                                    <input class="status-option" type="radio"
                                                                                        name="m-status-{{ $activity->id }}" value="half"
                                                                                        data-activity-id="{{ $activity->id }}"
                                                                                        data-phase-id="{{ $row->phase->id }}" {{ $statusValue === 'half' ? 'checked' : '' }}>
                                                                                    <i data-feather="alert-circle"></i>
                                                                                    <span>Teilweise</span>
                                                                                </label>

                                                                                <label
                                                                                    class="status-pill status-pill-done {{ $statusValue === 'done' ? 'is-active' : '' }}">
                                                                                    <input class="status-option" type="radio"
                                                                                        name="m-status-{{ $activity->id }}" value="1"
                                                                                        data-activity-id="{{ $activity->id }}"
                                                                                        data-phase-id="{{ $row->phase->id }}" {{ $statusValue === 'done' ? 'checked' : '' }}>
                                                                                    <i data-feather="check-circle"></i>
                                                                                    <span>Komplett</span>
                                                                                </label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="phase-mobile-field">
                                                                            <div class="phase-mobile-label">Plan</div>
                                                                            {{ $row->plan_time ?? $activity->duration ?? '00:00:00' }}
                                                                        </div>

                                                                        <div class="phase-mobile-field">
                                                                            <div class="phase-mobile-label">Ist</div>
                                                                            <input type="time" class="form-control form-control-sm" data-type="is_time"
                                                                                value="{{ $row->is_time ?? '' }}">
                                                                        </div>

                                                                        <div class="phase-mobile-field">
                                                                            <div class="phase-mobile-label">Erledigt am</div>
                                                                            <input type="date" name="history_mobile[{{ $activity->id }}][done_date]"
                                                                                value="{{ $rowDoneDate }}" class="form-control form-control-sm">
                                                                        </div>

                                                                        <div class="phase-mobile-field">
                                                                            <div class="phase-mobile-label">Markiert von</div>
                                                                            {{ $markedBy ? Str::limit($markedBy->name . ' ' . $markedBy->lastname, 16) : '–' }}
                                                                        </div>

                                                                        <div class="phase-mobile-field phase-mobile-full">
                                                                            <div class="phase-mobile-label">Zuständig</div>

                                                                            <select name="done_by" class="form-control employeeDone done-by-select"
                                                                                data-activity-id="{{ $activity->id }}" data-phase-id="{{ $row->phase->id }}">
                                                                                <option value="">-- Bitte wählen --</option>

                                                                                @foreach($employees as $emp)
                                                                                    <option value="{{ $emp->id }}" data-image="{{ $employeeImage($emp->image) }}" {{ $doneBy && $doneBy->id == $emp->id ? 'selected' : '' }}>
                                                                                        {{ $emp->name }} {{ $emp->lastname }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="phase-mobile-field phase-mobile-full">
                                                                            <div class="phase-mobile-label">Dokument</div>

                                                                            <form action="{{ url('/activity-document-upload') }}" method="POST"
                                                                                enctype="multipart/form-data" class="upload-form d-flex align-items-center">
                                                                                @csrf

                                                                                <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                                                                                <input type="hidden" name="alternative_id" value="{{ $alternative_id }}">
                                                                                <input type="hidden" name="product_id" value="{{ $productId }}">
                                                                                <input type="hidden" name="phase_id" value="{{ $row->phase->id }}">
                                                                                <input type="hidden" name="task_id" value="{{ $activity->id }}">
                                                                                <input type="hidden" name="stage" value="{{ $stageKey }}">

                                                                                <label class="upload-icon" title="Datei hochladen">
                                                                                    <i data-feather="upload-cloud"></i>

                                                                                    <input type="file" name="document" class="d-none"
                                                                                        onchange="uploadActivityFile(this)">
                                                                                </label>

                                                                                @if($hasDocument)
                                                                                    <a href="{{ $publicActivityFileUrl($row->has_document) }}" target="_blank"
                                                                                        class="activity-doc-current" title="{{ $row->has_document }}">
                                                                                        <i data-feather="paperclip"></i>
                                                                                        <span>{{ $row->has_document }}</span>
                                                                                    </a>
                                                                                @endif
                                                                            </form>
                                                                        </div>

                                                                        <div class="phase-mobile-field phase-mobile-full">
                                                                            <div class="phase-mobile-label">Notiz</div>

                                                                            <textarea class="form-control form-control-sm note-textarea" rows="2"
                                                                                data-activity-id="{{ $activity->id }}" data-phase-id="{{ $row->phase->id }}"
                                                                                placeholder="Notiz eingeben...">{{ is_array($row->notes) ? implode(' ', $row->notes) : ($row->notes ?? '') }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endforeach
                                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
        @empty
            <div class="phase-empty">
                Keine Arbeitsprozess-Daten für diese Version gefunden.
            </div>
        @endforelse
    </div>
</div>


{{-- Activity Document Preview Modal --}}
<div id="activityDocumentPreviewModal" class="activity-doc-modal" aria-hidden="true">
    <div class="activity-doc-backdrop" onclick="closeActivityDocumentModal()"></div>

    <div class="activity-doc-dialog" role="dialog" aria-modal="true">
        <div class="activity-doc-header">
            <div>
                <div class="activity-doc-title" id="activityDocModalTitle">Dokument Vorschau</div>
                <div class="activity-doc-subtitle" id="activityDocModalSubtitle">Aktivitätsdokument</div>
            </div>

            <div class="activity-doc-header-actions">
                <a href="#" target="_blank" id="activityDocModalOpen" class="activity-doc-open-btn">
                    <i data-feather="external-link"></i>
                    Öffnen
                </a>

                <button type="button" class="activity-doc-close-btn" onclick="closeActivityDocumentModal()">
                    <i data-feather="x"></i>
                </button>
            </div>
        </div>

        <div class="activity-doc-body" id="activityDocModalBody">
            <div class="activity-doc-loading">
                <i data-feather="loader"></i>
                Dokument wird geladen...
            </div>
        </div>
    </div>
</div>

