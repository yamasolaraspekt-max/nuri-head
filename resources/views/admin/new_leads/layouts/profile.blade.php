
<style>
/* =========================================================
   FINAL OBJECT RIGHT-CLICK CONTEXT MENU
========================================================= */
.ma-object-context-menu {
    position: fixed;
    left: -9999px;
    top: -9999px;
    z-index: 2147483646;
    display: none;
    min-width: 230px;
    padding: 7px;
    border: 1px solid var(--ma-border, #c0d8ea);
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 22px 55px rgba(15, 23, 42, .18);
}
.ma-object-context-menu.is-open {
    display: block;
}
.ma-object-context-item {
    width: 100%;
    min-height: 38px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 11px;
    border: 0;
    border-radius: 12px;
    background: transparent;
    color: var(--ma-text, #374151);
    font-size: 13px;
    font-weight: 850;
    text-align: left;
    cursor: pointer;
}
.ma-object-context-item:hover {
    background: #f8fbfd;
    color: var(--ma-heading, #74b2d4);
}
.ma-object-context-item.is-danger {
    color: var(--ma-pink, #e50656);
}
.ma-object-context-item.is-danger:hover {
    background: rgba(229, 6, 86, .08);
}
.ma-object-context-item:disabled {
    opacity: .45;
    pointer-events: none;
}
.ma-object-context-item svg,
.ma-object-context-item i {
    width: 16px;
    height: 16px;
    flex: 0 0 auto;
}
.ma-object-context-separator {
    height: 1px;
    margin: 6px 4px;
    background: var(--ma-border, #c0d8ea);
}
.object-thumb-link[data-screenshot-click-disabled="1"] {
    cursor: default !important;
}
.object-thumb-link[data-screenshot-click-disabled="1"] img {
    cursor: default !important;
}
</style>

<style>
/* Context feed final override - generated fix */
/* customer-context-feed.css */

#customerNotesRightPanel {
    min-height: 0;
}

#customerNotesRightPanel .ma-notes-header {
    border-bottom: 1px solid var(--ma-border, #c0d8ea);
    flex-shrink: 0;
    padding: .55rem;
    background: #fff;
}

#customerNotesRightPanel #note_title {
    font-size: 1.1rem;
    font-weight: 900;
    color: #94c11f !important;
    letter-spacing: .08em;
}

#customerNotesRightPanel #note-scroll-wrapper {
    min-height: 0;
}

#customerNotesRightPanel #note-list {
    min-height: 100%;
    padding: .5rem;
}

#note-list #maNoteTypeSwitcher,
#note-list .ma-note-type-switcher,
#note-list [data-note-feed-current],
#note-list [data-note-feed-menu],
#note-list #note_title {
    display: none !important;
}

.ma-note-type-switcher {
    position: relative;
    flex-shrink: 0;
    margin: .5rem;
    z-index: 500;
}

.ma-note-type-current {
    width: 100%;
    min-height: 54px;
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .65rem .75rem;
    border: 1px solid var(--ma-border, #c0d8ea);
    border-radius: 16px;
    background: #fff;
    color: var(--ma-text, #374151);
    box-shadow: 0 12px 26px rgba(116, 178, 212, .12);
}

.ma-note-type-current:hover {
    background: #f8fbfd;
}

.ma-note-type-text {
    flex: 1;
    min-width: 0;
    text-align: left;
}

.ma-note-type-text strong,
.ma-note-type-item strong {
    display: block;
    font-size: 13px;
    font-weight: 900;
    color: var(--ma-heading, #74b2d4);
}

.ma-note-type-text small,
.ma-note-type-item small {
    display: block;
    font-size: 11px;
    color: var(--ma-muted, #6b7280);
}

.ma-note-type-chevron {
    transition: transform .18s ease;
}

.ma-note-type-switcher.is-open .ma-note-type-chevron {
    transform: rotate(180deg);
}

.ma-note-type-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    display: none;
    padding: .45rem;
    border: 1px solid var(--ma-border, #c0d8ea);
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 20px 45px rgba(15, 23, 42, .14);
    z-index: 99999;
}

.ma-note-type-switcher.is-open .ma-note-type-menu {
    display: block;
}

.ma-note-type-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .6rem;
    border: 0;
    border-radius: 14px;
    background: transparent;
    text-align: left;
}

.ma-note-type-item:hover,
.ma-note-type-item.active {
    background: #f8fbfd;
}

.ma-note-type-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    color: #fff !important;
    flex: 0 0 auto;
}

.ma-note-type-icon.bg-blue {
    background: #74b2d4;
}

.ma-note-type-icon.bg-green {
    background: #93c21c;
}

.ma-note-type-icon.bg-orange {
    background: #f8ac00;
}

.ma-note-type-icon.bg-pink {
    background: #e50656;
}

.ma-feed-card {
    border: 1px solid var(--ma-border, #c0d8ea);
    border-radius: 16px;
    background: #fff;
    margin-bottom: .65rem;
    overflow: hidden;
}

.ma-feed-head {
    width: 100%;
    border: 0;
    background: #fff;
    padding: .7rem;
    display: flex;
    align-items: flex-start;
    gap: .55rem;
    text-align: left;
}

.ma-feed-head:hover {
    background: #f8fbfd;
}

.ma-feed-body {
    display: none;
    padding: .7rem;
    border-top: 1px solid var(--ma-border, #c0d8ea);
    background: #f8fbfd;
}

.ma-feed-card.is-open .ma-feed-body {
    display: block;
}

.ma-feed-title {
    font-size: 13px;
    font-weight: 900;
    color: var(--ma-heading, #74b2d4);
}

.ma-feed-meta {
    font-size: 11px;
    color: var(--ma-muted, #6b7280);
}

.ma-feed-empty {
    padding: 1rem;
    border: 1px dashed var(--ma-border, #c0d8ea);
    border-radius: 16px;
    color: var(--ma-muted, #6b7280);
    background: #fff;
}

.ma-feed-preview {
    margin-top: 2px;
    font-size: 11px;
    color: var(--ma-muted, #6b7280);
}

.ma-feed-comment {
    padding: .55rem;
    margin-bottom: .45rem;
    border: 1px solid var(--ma-border, #c0d8ea);
    border-radius: 14px;
    background: #fff;
}

.ma-feed-comment.is-reply {
    margin-left: 1.25rem;
    background: #f8fbfd;
}

.ma-feed-replies {
    margin-top: .5rem;
}

.ma-feed-mini-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    padding: .35rem 0;
    border-bottom: 1px dashed rgba(192, 216, 234, .75);
    font-size: 12px;
}

.ma-feed-mini-row:last-child {
    border-bottom: 0;
}

.ma-feed-mini-row span {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    min-width: 0;
}

.ma-feed-mini-row small {
    color: var(--ma-muted, #6b7280);
    text-align: right;
}

body.ma-context-feed-active #toggleNewNoteBtn,
body.ma-context-feed-active #noteDeletedModal {
    display: none !important;
}

@media (max-width: 768px) {
    .ma-notes-header .d-flex {
        gap: .4rem;
    }

    .ma-note-search-wrap {
        max-width: 130px;
    }

    .ma-note-type-current {
        min-height: 50px;
        padding: .55rem;
    }

    .ma-note-type-text strong {
        font-size: 12px;
    }

    .ma-note-type-text small {
        font-size: 10px;
    }
}

</style>
<style>
    /* =========================================================
       CUSTOMER PROFILE RESPONSIVE UI
       Palette only:
       #93c21c, #cfe09b, #74b2d4, #c0d8ea, #f8ac00, #e50656
       Background: white
       Headings: #74b2d4
       Text: dark gray
    ========================================================= */

    :root {
        --ma-green: #93c21c;
        --ma-green-soft: #cfe09b;
        --ma-blue: #74b2d4;
        --ma-blue-soft: #c0d8ea;
        --ma-orange: #f8ac00;
        --ma-pink: #e50656;

        --ma-bg: #ffffff;
        --ma-card: #ffffff;
        --ma-heading: #74b2d4;
        --ma-text: #374151;
        --ma-muted: #6b7280;
        --ma-border: #c0d8ea;

        --ma-radius-xl: 28px;
        --ma-radius-lg: 22px;
        --ma-radius-md: 16px;
        --ma-radius-sm: 12px;

        --ma-shadow-soft: 0 16px 38px rgba(116, 178, 212, .15);
        --ma-shadow-hover: 0 18px 42px rgba(147, 194, 28, .18);
    }

    html,
    body {
        margin: 0;
        padding: 0;
        min-height: 100%;
        background: var(--ma-bg);
        color: var(--ma-text);
    }

    * {
        box-sizing: border-box;
    }

    .gallery-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
        gap: .75rem;
    }

    .gallery-thumb {
        display: block;
        text-decoration: none !important;
        padding: .45rem;
        border-radius: 16px;
        background: var(--ma-soft-bg, #f8fbfd);
        color: var(--ma-text) !important;
    }

    .gallery-thumb:hover {
        background: var(--ma-blue-soft);
    }

    .sidebar-gallery {
        z-index: 20000 !important;
    }

    .sidebar-gallery.active {
        right: 0 !important;
    }

    button,
    a,
    [role="button"],
    .project-link,
    .project-card,
    .cn-notes-small,
    .total-purchase-trigger,
    .price-edit-trigger,
    .badge-trigger,
    .object-header,
    .nav-section-btn {
        cursor: pointer;
    }

    button:disabled,
    .badge-trigger[disabled] {
        opacity: .45;
        cursor: not-allowed;
        pointer-events: none;
    }

    button:focus,
    a:focus,
    input:focus,
    select:focus,
    textarea:focus {
        outline: 3px solid rgba(248, 172, 0, .35);
        outline-offset: 2px;
        box-shadow: none !important;
    }

    svg,
    .feather {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        flex-shrink: 0;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .modal-title,
    .cmodal__title,
    .ccp-modal-title,
    #note_title,
    .customer-nav-title,
    .cn-name,
    .cn-collapsed-name,
    .cn-welcome-label,
    .cn-block-label,
    .cn-info-content .fw-bold,
    .umsatz-label,
    .umsatz-total,
    .project-card-title,
    .project-status-pill,
    .stage-title,
    .pt-section-title,
    .feed-modal-item-title,
    .ph-title {
        color: var(--ma-heading) !important;
    }

    p,
    span,
    small,
    label,
    td,
    th,
    input,
    textarea,
    select,
    .cn-meta-line,
    .cn-firma,
    .cn-info-content,
    .cn-notes-small,
    .cn-notes,
    .cn-collapsed-meta,
    .project-card-meta,
    .metric-value,
    .metric-label,
    .umsatz-meta,
    .cfs-text,
    .cfs-time,
    .cfs-counter,
    .live-feed-text,
    .live-feed-time,
    .feed-modal-item-text,
    .feed-modal-item-time,
    .pt-info-meta,
    .pt-stat-label,
    .pt-stat-value,
    .stage-meta,
    .stage-next .desc {
        color: var(--ma-text) !important;
    }

    .text-muted,
    .text-muted *,
    .small,
    .text-black,
    .text-danger {
        color: var(--ma-muted) !important;
    }

    /* =========================================================
       MAIN WRAPPER
    ========================================================= */

    .container-flex {
        display: flex;
        height: 100vh;
        overflow: hidden;
        background: var(--ma-bg);
    }

    .customer-wrapper {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        color: var(--ma-text);
    }

    /* =========================================================
       CUSTOMER TOP PROFILE HEADER
    ========================================================= */

    .customer-nav-wrap {
        padding: .65rem .75rem .35rem;
        flex-shrink: 0;
    }

    .customer-nav,
    .customer-navs {
        position: relative;
        margin-bottom: .5rem;
        border-radius: var(--ma-radius-xl);
        background: var(--ma-card) !important;
        border: 1px solid var(--ma-border);
        box-shadow: var(--ma-shadow-soft);
        overflow: hidden;
        transition: all .25s ease;
    }

    .customer-navs {
        padding: 1rem 1.5rem;
        font-size: 14px;
    }

    .customer-nav-inner {
        padding: 1.1rem 1.4rem;
    }

    .customer-nav .row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        row-gap: 1rem;
    }

    .customer-nav .col {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }


    .nav-section-btn {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: .6rem !important;
    }

    .nav-section-btn>span:first-child {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        min-width: 0;
    }

    .sidebar-count-badge {
        min-width: 24px;
        height: 24px;
        padding: 0 .45rem;
        border-radius: 999px;
        background: var(--ma-green) !important;
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        flex-shrink: 0;
    }

    .sidebar-count-badge.is-zero {
        background: var(--ma-blue-soft) !important;
        color: var(--ma-text) !important;
    }

    .sidebar-count-badge.is-loading {
        background: var(--ma-orange) !important;
        color: #ffffff !important;
    }

    .sidebar-count-badge.is-error {
        background: var(--ma-pink) !important;
        color: #ffffff !important;
    }

    .customer-nav .inner-col {
        height: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .customer-nav .inner-col.border-start {
        border-left: 1px solid var(--ma-border);
    }

    .customer-nav-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
    }

    .customer-nav-title {
        font-weight: 900;
        font-size: 1.1rem;
    }

    .customer-nav-icons {
        display: flex;
        gap: 1rem;
        align-items: center;
        color: var(--ma-heading);
    }

    .customer-nav-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
        font-size: .9rem;
        color: var(--ma-text);
    }

    .customer-nav-tabs {
        margin-top: .25rem;
    }

    .customer-nav .text-uppercase {
        letter-spacing: .5px;
        font-size: 20px;
        font-weight: 900;
        color: var(--ma-heading);
    }

    /* =========================================================
       PROFILE COLLAPSE
    ========================================================= */

    #customerNavWrapper {
        position: relative;
        transition: all .25s ease;
    }

    .cn-toggle-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 34px;
        height: 34px;
        border: 1px solid var(--ma-border);
        border-radius: 999px;
        background: var(--ma-bg);
        color: var(--ma-heading);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1;
        transition: all .2s ease;
        box-shadow: 0 8px 18px rgba(116, 178, 212, .12);
    }

    .cn-toggle-btn:hover {
        background: var(--ma-blue-soft);
        border-color: var(--ma-heading);
        color: var(--ma-text);
        transform: translateY(-1px);
    }

    .customer-nav.is-collapsed .cn-toggle-btn svg,
    #customerNavWrapper.is-collapsed .cn-toggle-btn svg {
        transform: rotate(180deg);
    }

    .cn-expanded-content {
        display: block;
        animation: maFadeUp .22s ease;
    }

    .cn-collapsed-content,
    .cn-collapsed-header {
        display: none;
        animation: maFadeUp .22s ease;
    }

    .customer-nav.is-collapsed .cn-expanded-content,
    #customerNavWrapper.is-collapsed .cn-expanded-content,
    .customer-nav.is-collapsed .customer-nav-inner {
        display: none !important;
    }

    .customer-nav.is-collapsed .cn-collapsed-content,
    #customerNavWrapper.is-collapsed .cn-collapsed-content,
    .customer-nav.is-collapsed .cn-collapsed-header {
        display: flex !important;
    }

    .cn-collapsed-content {
        width: 100%;
        flex-direction: column;
        align-items: stretch !important;
        gap: .75rem;
        padding: .85rem 3.5rem .85rem 1rem;
    }

    .cn-collapsed-main-row {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding-right: 2.6rem;
    }

    .cn-collapsed-profile-info {
        min-width: 0;
        flex: 1;
    }

    .cn-collapsed-name,
    .cn-mini-name {
        font-size: 16px;
        font-weight: 900;
        color: var(--ma-heading) !important;
        margin: 0;
        line-height: 1.2;
    }

    .cn-collapsed-meta,
    .cn-mini-address {
        font-size: 12px;
        color: var(--ma-text) !important;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .cn-collapsed-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-width: 0;
    }

    .cn-collapsed-feed {
        display: none;
        width: 100%;
    }

    #customerNavWrapper.is-collapsed .cn-collapsed-feed {
        display: block;
    }

    #customerNavWrapper:not(.is-collapsed) .cn-collapsed-feed {
        display: none !important;
    }

    /* =========================================================
       PROFILE CONTENT
    ========================================================= */

    .cn-left {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .cn-avatar {
        width: 54px;
        height: 54px;
        min-width: 54px;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--ma-blue), var(--ma-blue-soft));
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.1rem;
        border: 3px solid var(--ma-blue-soft);
        box-shadow: 0 14px 30px rgba(116, 178, 212, .25);
        flex-shrink: 0;
    }

    .cn-avatar-initials {
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .cn-welcome-label,
    .cn-block-label,
    .umsatz-label,
    .metric-label,
    .pt-info-label,
    .price-label {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .12em;
        font-weight: 900;
    }

    .cn-name-line {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .35rem;
    }

    .cn-name {
        font-size: clamp(1rem, 2vw, 1.25rem);
        font-weight: 900;
        line-height: 1.2;
    }

    .cn-firma {
        font-size: .75rem;
        font-weight: 800;
        margin-top: .15rem;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .cn-meta-line {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        margin-top: .35rem;
        font-size: .75rem;
        font-weight: 700;
    }

    .cn-dot {
        opacity: .6;
    }

    .cn-meta-pills,
    .product-initials-row {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        margin-top: .45rem;
    }

    .cn-pill,
    .product-mini-badge,
    .ph-pill,
    .status-badge,
    .pt-status-pill,
    .feed-modal-pill,
    .cfs-pill,
    .live-feed-pill,
    .badge,
    .badge-primary,
    .badge-danger,
    .badge-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: var(--ma-blue-soft) !important;
        color: var(--ma-text) !important;
        border: 1px solid var(--ma-heading) !important;
        font-size: .72rem;
        font-weight: 800;
        padding: .24rem .65rem;
        line-height: 1;
    }

    .product-mini-badge {
        min-width: 34px;
        width: auto;
        height: 24px;
        padding: 0 .55rem;
        border-radius: 8px !important;
        background: var(--ma-orange) !important;
        border-color: var(--ma-orange) !important;
        white-space: nowrap;
        letter-spacing: .02em;
    }

    .product-mini-badge.active {
        background: var(--ma-green) !important;
        border-color: var(--ma-green) !important;
        color: #ffffff !important;
    }

    .cn-icon {
        font-size: 13px;
        color: var(--ma-heading);
    }

    .btn-xs.cn-edit-btn,
    .cn-edit-btn,
    .btn-pill-sm,
    .cfs-btn,
    .live-feed-btn,
    .minimize-btn,
    .dashboard-btn,
    .nav-section-btn,
    .project-metric,
    .ph-close-btn,
    .ccp-modal-close-btn,
    .right-panel .btn,
    .btn-outline-primary,
    .btn-outline-danger,
    .btn-success,
    .btn-primary {
        border-radius: 999px !important;
        border: 1px solid var(--ma-border) !important;
        background: var(--ma-bg) !important;
        color: var(--ma-heading) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        transition: all .18s ease;
        box-shadow: none !important;
    }

    .btn-xs.cn-edit-btn {
        padding: .18rem .5rem;
        font-size: 11px;
    }

    .cn-edit-btn:hover,
    .btn-pill-sm:hover,
    .cfs-btn:hover,
    .live-feed-btn:hover,
    .minimize-btn:hover,
    .dashboard-btn:hover,
    .nav-section-btn:hover,
    .project-metric:hover,
    .ph-close-btn:hover,
    .ccp-modal-close-btn:hover,
    .right-panel .btn:hover,
    .btn-outline-primary:hover,
    .btn-outline-danger:hover,
    .btn-success:hover,
    .btn-primary:hover {
        background: var(--ma-blue-soft) !important;
        border-color: var(--ma-heading) !important;
        color: var(--ma-text) !important;
        transform: translateY(-1px);
    }

    .btn-pill-info,
    .cfs-btn-expand,
    .live-feed-btn[data-feed-expand] {
        background: var(--ma-heading) !important;
        color: #ffffff !important;
        border-color: var(--ma-heading) !important;
    }

    .btn-pill-edit {
        background: var(--ma-bg) !important;
        color: var(--ma-heading) !important;
    }

    .btn-pill-sm {
        padding: .3rem .85rem;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .cn-notes,
    .cn-notes-small,
    .cn-info-block,
    .inner-col-umsatz {
        background: var(--ma-bg);
        border: 1px solid var(--ma-border);
        border-radius: var(--ma-radius-md);
        color: var(--ma-text);
    }

    .cn-notes,
    .cn-notes-small {
        margin-top: .5rem;
        padding: .6rem .75rem;
        max-height: 72px;
        overflow: auto;
        font-size: .78rem;
        line-height: 1.45;
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    .cn-notes:hover,
    .cn-notes-small:hover {
        border-color: var(--ma-heading);
        background: #ffffff;
    }

    .cn-info-block {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        padding: .65rem .75rem;
        margin-bottom: .5rem;
    }

    .cn-info-block:last-child {
        margin-bottom: 0;
    }

    .cn-info-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 999px;
        background: var(--ma-blue-soft);
        color: var(--ma-heading);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .cn-info-content {
        font-size: .78rem;
        line-height: 1.45;
    }

    .inner-col-umsatz {
        padding: .8rem;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .75rem;
        align-items: center;
        min-height: 100%;
    }

    .inner-col-umsatz.compact {
        padding: .65rem .8rem;
        gap: .6rem;
    }

    .umsatz-main {
        display: flex;
        flex-direction: column;
        gap: .15rem;
        min-width: 0;
    }

    .umsatz-label {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }

    .umsatz-dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: var(--ma-orange);
        box-shadow: 0 0 0 4px rgba(248, 172, 0, .2);
    }

    .umsatz-total {
        font-size: clamp(1rem, 2vw, 1.3rem);
        font-weight: 900;
        margin-top: .15rem;
    }

    .umsatz-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        font-size: .72rem;
    }

    .badge-icons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .25rem;
    }

    .badge-icon {
        display: none;
        width: 26px;
        height: 26px;
        border-radius: 999px;
        object-fit: contain;
    }

    .badge-icons[data-tier="bronze"] .badge-bronze,
    .badge-icons[data-tier="silver"] .badge-silver,
    .badge-icons[data-tier="gold"] .badge-gold,
    .badge-icons[data-tier="platinum"] .badge-platinum {
        display: inline-block;
    }

    .badge-trigger[disabled] {
        opacity: .4;
        cursor: not-allowed;
    }

    /* =========================================================
       REMOVED ANALYTICS UI
    ========================================================= */

    .fk-switcher-wrapper,
    .fk-tabs,
    .fk-tab,
    .fk-pane,
    .fk-analytics-row,
    .fk-kpi-item,
    .fk-kpi-icon,
    .fk-kpi-label,
    .fk-kpi-count {
        display: none !important;
    }

    /* =========================================================
       CUSTOMER FEED STRIP
       Only place it inside .cn-collapsed-feed
    ========================================================= */

    .customer-feed-strip,
    .customer-live-feed {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .65rem;
        margin-top: 0;
        min-height: 44px;
        border-radius: var(--ma-radius-lg);
        background: var(--ma-bg) !important;
        color: var(--ma-text);
        border: 1px solid var(--ma-border);
        padding: .65rem !important;
        font-size: .82rem;
    }

    .customer-feed-strip .cfs-icon,
    .customer-live-feed .live-feed-icon,
    .feed-modal-title-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 999px;
        background: var(--ma-blue-soft);
        color: var(--ma-heading);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--ma-border);
    }

    .customer-feed-strip .cfs-main,
    .customer-live-feed .live-feed-main {
        min-width: 0;
        flex: 1;
    }

    .customer-feed-strip .cfs-line,
    .customer-live-feed .live-feed-line {
        display: flex;
        flex-direction: column;
        gap: .15rem;
        min-width: 0;
    }

    .customer-feed-strip .cfs-line-top,
    .customer-feed-strip .cfs-bottom,
    .customer-live-feed .live-feed-meta {
        display: flex;
        align-items: center;
        gap: .4rem;
        min-width: 0;
    }

    .customer-feed-strip .cfs-title,
    .customer-live-feed .live-feed-title {
        font-weight: 900;
        color: var(--ma-heading);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .customer-feed-strip .cfs-text,
    .customer-live-feed .live-feed-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: .8rem;
    }

    .customer-feed-strip .cfs-time,
    .customer-feed-strip .cfs-counter,
    .customer-feed-strip .cfs-empty-sub,
    .customer-live-feed .live-feed-time,
    .customer-live-feed .live-feed-counter {
        font-size: .72rem;
        color: var(--ma-muted) !important;
    }

    .customer-feed-strip .cfs-empty {
        display: none;
        flex-direction: column;
        font-size: .78rem;
    }

    .customer-feed-strip.is-empty .cfs-line {
        display: none;
    }

    .customer-feed-strip.is-empty .cfs-empty {
        display: flex;
    }

    .customer-feed-strip .cfs-controls,
    .customer-live-feed .live-feed-controls {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        flex-shrink: 0;
    }

    .customer-feed-strip .cfs-btn,
    .customer-live-feed .live-feed-btn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        padding: 0;
    }

    .customer-feed-strip .cfs-btn-expand,
    .customer-live-feed .live-feed-btn[data-feed-expand] {
        background: var(--ma-heading) !important;
        border-color: var(--ma-heading) !important;
        color: #ffffff !important;
    }

    /* =========================================================
       MAIN DESKTOP LAYOUT
    ========================================================= */

    .layout {
        flex: 1;
        min-height: 0;
        display: grid;
        grid-template-columns: 310px minmax(0, 1fr) 360px;
        gap: .65rem;
        padding: .35rem .75rem .75rem;
        overflow: hidden;
    }

    .customerSidebar,
    .contentStation,
    .right-panel {
        min-height: 0;
        border-radius: var(--ma-radius-lg);
        border: 1px solid var(--ma-border);
        box-shadow: var(--ma-shadow-soft);
        overflow: hidden;
        background: var(--ma-bg);
    }

    .customerSidebar {
        width: 100%;
        color: var(--ma-text);
        padding: .75rem;
        overflow-y: auto;
        transition: width .25s ease, padding .25s ease, transform .25s ease;
    }

    .customerSidebar.minimized {
        width: 70px;
        padding: .75rem .35rem;
    }

    .customerSidebar.minimized .text,
    .customerSidebar.minimized small,
    .customerSidebar.minimized .sub-nav,
    .customerSidebar.minimized .object-address,
    .customerSidebar.minimized .customer-summary,
    .customerSidebar.minimized .project-card-title-block,
    .customerSidebar.minimized .project-card-footer,
    .customerSidebar.minimized .project-status-pill {
        display: none !important;
    }

    .customerSidebar::-webkit-scrollbar,
    .scroll-wrapper::-webkit-scrollbar,
    .feed-modal-list::-webkit-scrollbar,
    .ph-body::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }

    .customerSidebar::-webkit-scrollbar-thumb,
    .scroll-wrapper::-webkit-scrollbar-thumb,
    .feed-modal-list::-webkit-scrollbar-thumb,
    .ph-body::-webkit-scrollbar-thumb {
        background: var(--ma-blue);
        border-radius: 999px;
    }

    .customerSidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .minimize-btn,
    .dashboard-btn {
        width: 100%;
        min-height: 40px;
        margin-bottom: .5rem;
        padding: .5rem .65rem;
        justify-content: flex-start;
        text-align: left;
        font-weight: 900;
    }

    .customerSidebar.minimized .minimize-btn,
    .customerSidebar.minimized .dashboard-btn {
        justify-content: center;
        padding: .5rem;
    }

    .object-section {
        margin-bottom: .75rem;
    }

    .object-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .65rem;
        padding: .75rem;
        border-radius: var(--ma-radius-md);
        background: var(--ma-bg);
        border: 1px solid var(--ma-border);
        color: var(--ma-text);
        transition: all .18s ease;
    }

    .object-header:hover {
        background: var(--ma-blue-soft);
        border-color: var(--ma-heading);
        transform: translateY(-1px);
    }

    .object-header .text {
        color: var(--ma-heading) !important;
        font-weight: 900;
    }

    .object-header small {
        color: var(--ma-muted) !important;
    }

    .object-header img {
        border-radius: var(--ma-radius-sm) !important;
        border: 2px solid var(--ma-blue-soft);
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: cover;
    }

    .object-address {
        font-size: .8rem;
        margin-left: 2rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--ma-border);
        color: var(--ma-muted);
    }

    .product-list {
        padding-top: .55rem !important;
    }

    .project-link,
    .project-card,
    .project-link.project-card {
        display: flex;
        flex-direction: column;
        gap: .55rem;
        width: 100%;
        margin-bottom: .6rem;
        padding: .75rem;
        border-radius: var(--ma-radius-md);
        background: var(--ma-bg);
        color: var(--ma-text);
        border: 1px solid var(--ma-border);
        transition: all .18s ease;
    }

    .project-link:hover,
    .project-card:hover {
        border-color: var(--ma-heading);
        background: #ffffff;
        transform: translateY(-1px);
        box-shadow: var(--ma-shadow-hover);
    }

    .project-card-main,
    .project-card-title-row {
        display: flex;
        align-items: flex-start;
        gap: .55rem;
        min-width: 0;
        width: 100%;
    }

    .project-card-title-block {
        min-width: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .project-card-title,
    .product-title {
        font-size: .9rem;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .project-card-meta,
    .product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
        font-size: .74rem;
        line-height: 1.35;
        color: var(--ma-muted) !important;
        margin-top: .15rem;
    }

    .project-card-meta .meta-sep,
    .product-meta .meta-sep {
        margin: 0 4px;
        opacity: .6;
    }

    .project-status-dot,
    .product-dot {
        width: 10px;
        height: 10px;
        min-width: 10px;
        border-radius: 999px;
        background: var(--ma-orange);
        margin-top: .25rem;
        box-shadow: 0 0 0 4px rgba(248, 172, 0, .2);
    }

    .project-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        flex-wrap: wrap;
        margin-top: .35rem;
    }

    .project-footer-left,
    .project-footer-right,
    .project-meta {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        flex-wrap: wrap;
    }

    .project-status-pill {
        font-size: .65rem;
        padding: .38rem .65rem;
        border-radius: 999px;
        background: var(--ma-blue-soft) !important;
        color: var(--ma-text) !important;
        border: 1px solid var(--ma-heading) !important;
        text-transform: uppercase;
        letter-spacing: .07em;
        white-space: nowrap;
    }

    .project-metric,
    .price-badge,
    .time-badge {
        min-height: 34px;
        padding: .35rem .65rem;
        font-size: .78rem;
        font-weight: 900;
        border-radius: 999px !important;
        border: 1px solid var(--ma-border);
        background: var(--ma-bg);
        color: var(--ma-text);
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        line-height: 1.1;
    }

    .project-metric--price,
    .price-badge {
        border-color: var(--ma-orange) !important;
    }

    .project-metric--calendar,
    .project-metric--time,
    .time-badge {
        border-color: var(--ma-heading) !important;
        background: var(--ma-blue-soft) !important;
    }

    .metric-text {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.1;
    }

    .metric-label,
    .price-label {
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--ma-muted) !important;
    }

    .metric-value,
    .price-value {
        font-size: .78rem;
        font-weight: 900;
        color: var(--ma-text) !important;
    }

    .sub-nav {
        display: none;
        margin: .2rem 0 .75rem .75rem;
        padding-left: .65rem;
        border-left: 3px solid var(--ma-heading);
    }

    .sub-nav.show {
        display: block !important;
    }

    .sub-nav button,
    .nav-section-btn {
        width: 100%;
        min-height: 38px;
        margin-bottom: .25rem;
        padding: .48rem .7rem;
        justify-content: flex-start;
        text-align: left;
        font-weight: 800;
        border-radius: 999px !important;
    }

    .nav-section-btn.active,
    .sub-nav button:hover,
    .nav-section-btn:hover {
        background: var(--ma-blue-soft) !important;
        border-color: var(--ma-heading) !important;
        color: var(--ma-text) !important;
    }

    .contentStation {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        position: relative;
        background: var(--ma-bg);
    }

    .main-content,
    .main,
    #mainContent {
        height: 100%;
        min-height: 0;
        overflow: auto;
        background: var(--ma-bg);
        margin-top: 20px;
    }

    #mainContentToggle {
        position: absolute !important;
        top: .55rem !important;
        right: .55rem !important;
        z-index: 20;
        background: var(--ma-heading) !important;
        color: #ffffff !important;
        border: 1px solid var(--ma-heading) !important;
        border-radius: 999px !important;
    }

    .right-panel {
        display: flex;
        flex-direction: column;
        width: 100%;
        flex-shrink: 0;
        background: var(--ma-bg);
        color: var(--ma-text);
    }

    .right-panel>div:first-child {
        padding: .65rem;
        background: var(--ma-bg);
        border-bottom: 1px solid var(--ma-border) !important;
        flex-shrink: 0;
    }

    .right-panel h4,
    #note_title {
        font-weight: 900;
        letter-spacing: .08em;
    }

    .right-panel .form-control,
    .ccp-modal-panel .form-control,
    .feed-modal-search .form-control,
    .feed-modal-body select,
    input.form-control,
    textarea.form-control,
    select.form-control {
        border: 1px solid var(--ma-border) !important;
        background: var(--ma-bg) !important;
        color: var(--ma-text) !important;
        border-radius: 999px;
    }

    textarea.form-control {
        border-radius: var(--ma-radius-md);
    }

    .form-control::placeholder,
    textarea::placeholder,
    input::placeholder {
        color: var(--ma-muted) !important;
    }

    .scroll-wrapper {
        max-height: 100%;
        overflow-y: auto;
        padding-right: .25rem;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .scroll-wrapper::-webkit-scrollbar {
        display: none;
    }

    .panel-controls {
        position: relative;
        z-index: 1000;
    }

    .floating-show-btn {
        position: fixed;
        top: 110px;
        z-index: 1000;
        background: var(--ma-bg);
        border: 1px solid var(--ma-border);
        padding: 6px 10px;
        border-radius: 999px;
        color: var(--ma-heading);
    }

    .floating-show-btn.start {
        left: 10px;
    }

    .floating-show-btn.end {
        right: 10px;
    }

    .main-hidden,
    .sidebar-hidden,
    .right-hidden {
        display: none !important;
    }

    .right-fullscreen {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        position: relative;
        z-index: 999;
        background: var(--ma-bg);
    }

    .collapse {
        transition: height .25s ease, opacity .25s ease;
        overflow: hidden;
    }

    /* =========================================================
       TABLES / STAGE / DASHBOARD CARDS
    ========================================================= */

    .table {
        color: var(--ma-text);
    }

    .table thead th {
        background: var(--ma-blue-soft) !important;
        color: var(--ma-text) !important;
        border-color: var(--ma-border) !important;
        white-space: nowrap;
    }

    .table td,
    .table th {
        color: var(--ma-text) !important;
        border-color: var(--ma-border) !important;
        vertical-align: middle;
    }

    .table-responsive,
    .pt-info-card,
    .pt-chart-card,
    .pt-stat-card,
    .card.stage-card,
    .product-chip,
    .customer-chip {
        background: var(--ma-bg) !important;
        border: 1px solid var(--ma-border);
        border-radius: var(--ma-radius-md);
        color: var(--ma-text);
        box-shadow: none;
    }

    .customer-strip {
        display: grid;
        gap: .75rem;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        align-items: stretch;
    }

    .customer-chip,
    .product-chip {
        padding: 14px 16px;
        height: 100%;
    }

    .customer-chip .note {
        max-height: 80px;
        overflow: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        cursor: pointer;
        border: 1px dashed var(--ma-border);
        border-radius: var(--ma-radius-sm);
        padding: 8px;
    }

    .avatar-stack {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .avatar-ring {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        object-fit: cover;
        display: block;
        border: 2px solid var(--ma-border);
    }

    .progress {
        height: 1.8rem;
        background: var(--ma-blue-soft);
        border-radius: 999px;
        overflow: hidden;
    }

    .progress .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .stage-card {
        overflow: hidden;
    }

    .stage-head {
        background: var(--ma-blue-soft);
        padding: 14px 16px;
        display: grid;
        gap: 16px;
        grid-template-columns: 1.2fr 1fr 1.4fr auto;
        align-items: center;
        cursor: pointer;
    }

    .stage-head.active {
        background: var(--ma-blue-soft);
    }

    .stage-next .desc {
        max-width: 320px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .stage-actions .btn {
        margin-left: 8px;
    }

    .stage-body {
        background: var(--ma-bg);
    }

    .pt-info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .pt-info-card,
    .pt-chart-card,
    .pt-stat-card {
        padding: .65rem .75rem;
    }

    .pt-stats-row,
    .pt-section-split {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .9rem;
        margin-bottom: 1.1rem;
    }

    .pt-stat-cards {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
    }

    .pt-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 999px;
        margin-right: .25rem;
        vertical-align: middle;
    }

    .pt-dot-used {
        background: var(--ma-pink);
    }

    .pt-dot-remaining {
        background: var(--ma-green);
    }

    .pt-chart-wrapper {
        margin-top: .25rem;
        min-height: 160px;
    }

    .pt-timeline .pt-node {
        border-left: 2px solid var(--ma-border);
        padding-left: .6rem;
        margin-bottom: .75rem;
        position: relative;
    }

    .pt-timeline .pt-node::before {
        content: '';
        position: absolute;
        left: -5px;
        top: 3px;
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: var(--ma-green);
    }

    .gold-icon svg {
        width: 48px;
        height: auto;
        display: block;
    }

    /* =========================================================
       DRAWERS / MODALS
    ========================================================= */

    .sidebar-gallery {
        position: fixed;
        top: 0;
        right: -110%;
        width: min(900px, 92vw);
        height: 100%;
        background: var(--ma-bg);
        color: var(--ma-text);
        border-left: 1px solid var(--ma-border);
        padding: 1rem;
        z-index: 9999;
        overflow-y: auto;
        transition: right .25s ease;
        box-shadow: -20px 0 45px rgba(116, 178, 212, .22);
    }

    .sidebar-gallery.active {
        right: 0;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid var(--ma-border);
        margin-bottom: .75rem;
    }

    .gallery-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: .5rem;
    }

    .new_task {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(55, 65, 81, .42);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        padding: 1rem;
    }

    .new_task.active {
        display: flex !important;
    }

    .ph-backdrop {
        position: fixed;
        inset: 0;
        display: none;
        background: rgba(55, 65, 81, .42);
        z-index: 9998;
    }

    .ph-backdrop.is-open,
    .ph-backdrop.active {
        display: block;
    }

    .ph-drawer {
        position: absolute;
        top: 0;
        right: 0;
        width: min(460px, 100vw);
        height: 100%;
        background: var(--ma-bg);
        color: var(--ma-text);
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: .8rem;
        border-left: 1px solid var(--ma-border);
        box-shadow: -18px 0 45px rgba(116, 178, 212, .22);
        border-radius: 24px 0 0 24px;
    }

    .ph-header,
    .ph-meta-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .ph-title {
        font-size: 1rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .ph-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        font-size: .82rem;
    }

    .ph-entry {
        border-radius: var(--ma-radius-md);
        background: var(--ma-bg);
        border: 1px solid var(--ma-border);
        padding: .75rem;
        margin-bottom: .5rem;
        color: var(--ma-text);
    }

    .ph-entry-head,
    .ph-entry-prices {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .ph-entry-title {
        font-weight: 900;
        color: var(--ma-heading);
    }

    .ph-entry-prices {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    .ph-entry-prices span {
        white-space: nowrap;
    }

    .modal-content,
    .cmodal__dialog,
    .ccp-modal-panel {
        border-radius: var(--ma-radius-lg) !important;
        overflow: hidden;
        border: 1px solid var(--ma-border) !important;
        background: var(--ma-bg) !important;
        color: var(--ma-text) !important;
        box-shadow: var(--ma-shadow-soft);
    }

    .modal-header,
    .feed-modal-header,
    .cmodal__header,
    .ccp-modal-header {
        background: var(--ma-bg) !important;
        color: var(--ma-heading) !important;
        border-bottom: 1px solid var(--ma-border) !important;
        padding: .9rem 1.1rem;
    }

    .modal-body,
    .feed-modal-body,
    .cmodal__body,
    .ccp-modal-body {
        background: var(--ma-bg) !important;
        color: var(--ma-text) !important;
        padding: 1rem 1.2rem;
    }

    .feed-modal-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .55rem;
        margin-bottom: .75rem;
    }

    .feed-modal-toolbar .btn-group {
        flex-wrap: wrap;
        gap: .3rem;
    }

    .feed-modal-toolbar .btn {
        border-radius: 999px !important;
        background: var(--ma-bg) !important;
        color: var(--ma-heading) !important;
        border-color: var(--ma-border) !important;
        font-size: .75rem;
        padding: .28rem .65rem;
    }

    .feed-modal-toolbar .btn.active,
    .feed-modal-toolbar .btn:hover {
        background: var(--ma-heading) !important;
        border-color: var(--ma-heading) !important;
        color: #ffffff !important;
    }

    .feed-modal-search {
        flex: 1 1 230px;
        min-width: 180px;
    }

    .feed-modal-body select[data-feed-modal-sort] {
        width: 170px;
        font-size: .78rem;
    }

    .feed-modal-list {
        max-height: 60vh;
        overflow-y: auto;
        padding-right: .3rem;
    }

    .feed-modal-empty {
        padding: 1.1rem .5rem;
        text-align: center;
        color: var(--ma-muted);
        font-size: .86rem;
    }

    .feed-modal-item {
        display: flex;
        gap: .65rem;
        padding: .75rem 0;
        border-bottom: 1px solid var(--ma-border);
        color: var(--ma-text);
    }

    .feed-modal-item:last-child {
        border-bottom: none;
    }

    .feed-modal-item-icon {
        flex: 0 0 40px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        margin-top: .1rem;
    }

    .feed-modal-icon-pill {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--ma-blue-soft);
        color: var(--ma-heading);
    }

    .feed-modal-item-main {
        flex: 1 1 auto;
        min-width: 0;
    }

    .feed-modal-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .1rem;
    }

    .feed-modal-item-title {
        font-weight: 900;
        font-size: .94rem;
        margin-right: .5rem;
    }

    .feed-modal-item-time {
        font-size: .78rem;
        white-space: nowrap;
    }

    .feed-modal-item-text {
        font-size: .84rem;
        margin-bottom: .2rem;
        word-break: break-word;
    }

    .feed-modal-avatars {
        margin-top: .25rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .18rem;
    }

    .feed-modal-avatar,
    .live-feed-avatar {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        overflow: hidden;
        border: 1px solid var(--ma-border);
        background: var(--ma-blue-soft);
    }

    .live-feed-avatar {
        width: 18px;
        height: 18px;
        margin-right: -4px;
    }

    .feed-modal-avatar img,
    .live-feed-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .feed-modal-item-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .3rem;
        margin-top: .15rem;
    }

    [data-feed-modal-count] {
        font-size: .78rem;
    }

    .ccp-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(55, 65, 81, .42);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        padding: 1rem;
    }

    .ccp-modal-backdrop.is-open {
        display: flex;
    }

    .ccp-modal-panel {
        max-width: 960px;
        width: 100%;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        font-family: inherit;
    }

    .ccp-modal-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 900;
        font-size: 14px;
    }

    .ccp-modal-close-btn {
        padding: 4px;
    }

    .ccp-modal-body {
        display: flex;
        flex-direction: column;
        gap: 12px;
        overflow: auto;
    }

    .ccp-modal-top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .ccp-modal-table-wrap {
        border-radius: var(--ma-radius-md);
        border: 1px solid var(--ma-border);
        padding: 8px;
        background: var(--ma-bg);
    }

    .ccp-modal-form-wrap {
        border-top: 1px solid var(--ma-border);
        padding-top: 10px;
    }

    .ccp-modal-actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .note-composer {
        background: var(--ma-bg);
        border: 1px solid var(--ma-border);
        color: var(--ma-text);
        border-radius: var(--ma-radius-lg);
        padding: .75rem;
    }

    .note-backdrop {
        background: rgba(55, 65, 81, .42);
    }

    /* =========================================================
       MOBILE SIDEBAR DRAWER
       Add these after .customer-wrapper:
       <button type="button" class="mobile-sidebar-open-btn" id="mobileSidebarOpenBtn">
           <i data-feather="menu"></i>
       </button>
       <div class="mobile-sidebar-backdrop" id="mobileSidebarBackdrop"></div>
    ========================================================= */

    .mobile-sidebar-backdrop,
    .mobile-sidebar-open-btn {
        display: none;
    }

    @media (max-width: 1199.98px) {
        .layout {
            grid-template-columns: 280px minmax(0, 1fr);
            grid-template-rows: minmax(0, 1fr) 330px;
        }

        .right-panel {
            grid-column: 1 / -1;
            min-height: 300px;
        }
    }

    @media (max-width: 991.98px) {
        body.mobile-sidebar-open {
            overflow: hidden;
        }

        .customer-wrapper {
            height: auto !important;
            min-height: 100vh;
            overflow: visible !important;
        }

        .layout {
            display: flex !important;
            flex-direction: column !important;
            gap: .75rem;
            padding: .5rem !important;
            overflow: visible !important;
            min-height: auto;
        }

        .mobile-sidebar-open-btn {
            position: fixed;
            left: 14px;
            bottom: 18px;
            z-index: 10050;
            width: 52px;
            height: 52px;
            border-radius: 999px;
            border: 1px solid var(--ma-heading);
            background: var(--ma-heading);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 16px 35px rgba(116, 178, 212, .35);
        }

        .mobile-sidebar-open-btn svg,
        .mobile-sidebar-open-btn i,
        .mobile-sidebar-open-btn .feather {
            color: #ffffff !important;
            stroke: currentColor;
        }

        .mobile-sidebar-open-btn:hover {
            background: var(--ma-orange);
            border-color: var(--ma-orange);
            color: #ffffff;
        }

        .mobile-sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 10030;
            background: rgba(55, 65, 81, .42);
            display: none;
            backdrop-filter: blur(2px);
        }

        body.mobile-sidebar-open .mobile-sidebar-backdrop {
            display: block;
        }

        .customerSidebar {
            position: fixed !important;
            top: 0;
            left: 0;
            bottom: 0;
            width: min(88vw, 390px) !important;
            max-width: min(88vw, 390px) !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            z-index: 10040;
            transform: translateX(-105%);
            transition: transform .28s ease, box-shadow .28s ease;
            overflow-y: auto !important;
            overflow-x: hidden;
            padding: .85rem !important;
            border-radius: 0 24px 24px 0 !important;
            background: var(--ma-bg) !important;
            border: 1px solid var(--ma-border) !important;
            box-shadow: 22px 0 50px rgba(55, 65, 81, .22);
        }

        body.mobile-sidebar-open .customerSidebar {
            transform: translateX(0);
        }

        .customerSidebar.minimized {
            width: min(88vw, 390px) !important;
            max-width: min(88vw, 390px) !important;
            padding: .85rem !important;
        }

        .customerSidebar.minimized .text,
        .customerSidebar.minimized small,
        .customerSidebar.minimized .project-card-title-block,
        .customerSidebar.minimized .project-card-footer,
        .customerSidebar.minimized .project-status-pill {
            display: initial !important;
        }

        .customerSidebar.minimized .sub-nav {
            display: none !important;
        }

        .customerSidebar.minimized .sub-nav.show {
            display: block !important;
        }

        .customerSidebar .minimize-btn {
            position: sticky;
            top: 0;
            z-index: 5;
            width: 100%;
            min-height: 46px;
            margin-bottom: .75rem;
            justify-content: center !important;
            background: var(--ma-heading) !important;
            color: #ffffff !important;
            border-color: var(--ma-heading) !important;
            border-radius: 999px !important;
        }

        .customerSidebar .minimize-btn::after {
            content: " Menü schließen";
            font-weight: 900;
            font-size: .85rem;
            margin-left: .35rem;
        }

        .customerSidebar .minimize-btn svg,
        .customerSidebar .minimize-btn i,
        .customerSidebar .minimize-btn .feather {
            color: #ffffff !important;
        }

        .customerSidebar .dashboard-btn {
            width: 100%;
            min-height: 46px;
            justify-content: center !important;
            background: var(--ma-bg) !important;
            color: var(--ma-heading) !important;
            border: 1px solid var(--ma-border) !important;
            border-radius: 999px !important;
            margin-bottom: .9rem;
        }

        .customerSidebar .dashboard-btn .text {
            display: inline !important;
        }

        .object-section {
            margin-bottom: .85rem;
        }

        .object-header {
            border-radius: 18px !important;
            padding: .85rem !important;
            background: var(--ma-bg) !important;
            border: 1px solid var(--ma-border) !important;
            box-shadow: 0 10px 24px rgba(116, 178, 212, .12);
            align-items: flex-start !important;
        }

        .object-header:hover {
            background: var(--ma-bg) !important;
            border-color: var(--ma-heading) !important;
            transform: none !important;
        }

        .object-header .text {
            display: inline !important;
            color: var(--ma-heading) !important;
            font-size: .95rem;
        }

        .object-header small {
            display: block !important;
            color: var(--ma-muted) !important;
            font-size: .75rem;
            line-height: 1.25;
            margin-top: .2rem;
        }



        .project-link.project-card,
        .project-card {
            border-radius: 18px !important;
            padding: .85rem !important;
            margin-bottom: .65rem !important;
            background: var(--ma-bg) !important;
            border: 1px solid var(--ma-border) !important;
            box-shadow: 0 10px 24px rgba(116, 178, 212, .10);
        }

        .project-card-main,
        .project-card-title-row {
            width: 100%;
            align-items: flex-start !important;
        }

        .project-card-title {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: unset !important;
            font-size: .92rem !important;
            line-height: 1.25;
        }

        .project-card-meta {
            font-size: .75rem !important;
            line-height: 1.35;
        }

        .project-footer-left {
            margin-left: auto;
        }

        .project-card-footer {
            width: 100%;
            margin-top: .35rem;
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: .45rem !important;
        }

        .project-footer-right {
            width: 100%;
            display: grid !important;
            grid-template-columns: 1fr 46px;
            gap: .45rem;
        }

        .project-metric {
            width: 100%;
            min-height: 42px;
            border-radius: 14px !important;
            justify-content: center !important;
        }

        .sub-nav {
            margin: .2rem 0 .8rem 0 !important;
            padding: .65rem !important;
            border-left: 0 !important;
            border-radius: 18px;
            background: var(--ma-bg) !important;
            border: 1px solid var(--ma-border);
            box-shadow: 0 8px 22px rgba(116, 178, 212, .10);
        }

        .sub-nav.show {
            display: grid !important;
            grid-template-columns: 1fr;
            gap: .4rem;
        }

        .sub-nav button,
        .nav-section-btn {
            width: 100%;
            min-height: 44px;
            justify-content: flex-start !important;
            text-align: left !important;
            padding: .65rem .8rem !important;
            border-radius: 14px !important;
            background: var(--ma-bg) !important;
            color: var(--ma-text) !important;
            border: 1px solid var(--ma-border) !important;
            font-size: .84rem;
        }

        .sub-nav button:hover,
        .nav-section-btn:hover,
        .nav-section-btn.active {
            background: var(--ma-blue-soft) !important;
            border-color: var(--ma-heading) !important;
            color: var(--ma-text) !important;
        }

        .sub-nav button svg,
        .sub-nav button i,
        .nav-section-btn svg,
        .nav-section-btn i {
            color: var(--ma-heading) !important;
            margin-right: .35rem;
        }

        .contentStation {
            min-height: 65vh !important;
            background: var(--ma-bg) !important;
            border: 1px solid var(--ma-border) !important;
        }

        .right-panel {
            min-height: 380px !important;
            background: var(--ma-bg) !important;
            border: 1px solid var(--ma-border) !important;
        }

        #mainContentToggle {
            display: none !important;
        }

        .inner-col-umsatz {
            grid-template-columns: 1fr;
            border-radius: 18px;
        }

        .stage-head {
            grid-template-columns: 1fr;
        }

        .pt-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pt-stats-row,
        .pt-section-split {
            grid-template-columns: 1fr;
        }

        .ph-drawer {
            width: 100%;
            border-radius: 0;
        }
    }

    @media (max-width: 767.98px) {
        .customer-nav-wrap {
            padding: .5rem;
        }

        .customer-nav {
            border-radius: 20px;
        }

        .customer-nav-inner {
            padding: .85rem;
        }

        .cn-collapsed-content,
        .cn-collapsed-header {
            padding: .75rem 3.25rem .75rem .75rem;
        }

        .cn-collapsed-main-row {
            align-items: flex-start;
            padding-right: 2.4rem;
        }

        .cn-collapsed-meta {
            gap: .45rem;
        }

        .cn-name-line {
            align-items: flex-start !important;
        }

        .customer-feed-strip,
        .customer-live-feed {
            flex-wrap: wrap;
            border-radius: var(--ma-radius-md);
        }

        .customer-feed-strip .cfs-controls,
        .customer-live-feed .live-feed-controls {
            width: 100%;
            justify-content: flex-end;
        }

        .feed-modal-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .feed-modal-toolbar .d-flex {
            flex-direction: column;
            gap: .5rem;
            align-items: stretch !important;
        }

        .feed-modal-search,
        .feed-modal-body select[data-feed-modal-sort] {
            width: 100%;
        }

        .feed-modal-item-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar-gallery {
            width: 100vw;
        }
    }

    @media (max-width: 575.98px) {
        .customerSidebar {
            width: 92vw !important;
            max-width: 92vw !important;
            border-radius: 0 22px 22px 0 !important;
        }

        .mobile-sidebar-open-btn {
            left: 12px;
            bottom: 14px;
            width: 50px;
            height: 50px;
        }

        .cn-avatar {
            width: 44px;
            height: 44px;
            min-width: 44px;
            font-size: .95rem;
        }

        .cn-info-block {
            flex-direction: column;
        }

        .pt-info-grid,
        .pt-stat-cards {
            grid-template-columns: 1fr;
        }

        .object-header {
            padding: .75rem !important;
        }

        .project-link.project-card,
        .project-card {
            padding: .75rem !important;
        }

        .project-card-title-row {
            gap: .45rem !important;
        }

        .project-footer-right {
            grid-template-columns: 1fr 44px;
        }

        .right-panel>div:first-child .d-flex {
            flex-wrap: wrap;
            gap: .5rem;
        }

        .right-panel .search {
            width: 100%;
            flex-wrap: wrap;
        }

        .right-panel fieldset {
            flex: 1 1 100%;
        }

        .right-panel .btn-group {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @keyframes maFadeUp {
        from {
            opacity: 0;
            transform: translateY(4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<style>
    /* =========================================================
       BIGGER HOUSE SCREENSHOT GALLERY + HOVER PREVIEW
       Add this at the very END of profile CSS
    ========================================================= */

    .sidebar-gallery {
        width: min(1180px, 96vw) !important;
    }

    .sidebar-gallery .gallery-wrapper {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)) !important;
        gap: 1rem !important;
    }

    .sidebar-gallery .gallery-thumb {
        padding: .7rem !important;
        border-radius: 20px !important;
        background: var(--ma-soft-bg, #f8fbfd) !important;
    }

    .sidebar-gallery .gallery-thumb img {
        width: 100% !important;
        height: 220px !important;
        min-height: 220px !important;
        object-fit: cover !important;
        border-radius: 18px !important;
        background: #f8fbfd !important;
    }

    .sidebar-gallery .gallery-thumb:hover img {
        transform: scale(1.015);
    }

    .sidebar-gallery .gallery-thumb img {
        transition: transform .18s ease;
    }

    #maHoverPreviewOverlay {
        position: fixed !important;
        z-index: 2147483647 !important;
        display: none;
        width: min(760px, 58vw) !important;
        max-width: min(760px, 58vw) !important;
        padding: 10px !important;
        border-radius: 24px !important;
        background: #ffffff !important;
        border: 1px solid rgba(192, 216, 234, .75) !important;
        pointer-events: none !important;
    }

    #maHoverPreviewImg {
        display: block !important;
        width: 100% !important;
        max-height: 72vh !important;
        object-fit: contain !important;
        border-radius: 18px !important;
        background: #f8fbfd !important;
    }

    #maLightboxOverlay {
        z-index: 2147483646 !important;
    }

    #maLightboxImg {
        max-width: 94vw !important;
        max-height: 88vh !important;
        object-fit: contain !important;
    }

    @media (max-width: 991.98px) {
        .sidebar-gallery {
            width: 100vw !important;
        }

        .sidebar-gallery .gallery-wrapper {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
        }

        .sidebar-gallery .gallery-thumb img {
            height: 190px !important;
            min-height: 190px !important;
        }

        #maHoverPreviewOverlay {
            width: min(92vw, 680px) !important;
            max-width: 92vw !important;
        }
    }

    @media (max-width: 575.98px) {
        .sidebar-gallery .gallery-wrapper {
            grid-template-columns: 1fr !important;
        }

        .sidebar-gallery .gallery-thumb img {
            height: 240px !important;
            min-height: 240px !important;
        }

        #maHoverPreviewOverlay {
            display: none !important;
        }
    }
</style>

<style>
    /* =========================================================
   CLEAN OVERRIDE: NO SHADOWS + MINIMAL BORDERS
   Add this at the END of the same <style>
========================================================= */

    :root {
        --ma-shadow-soft: none;
        --ma-shadow-hover: none;
    }

    /* Remove all heavy shadows globally inside this page */
    .customer-wrapper *,
    .customer-wrapper *::before,
    .customer-wrapper *::after {
        box-shadow: none !important;
    }

    /* Keep only structural borders where needed */
    .customer-nav,
    .customer-navs,
    .customerSidebar,
    .contentStation,
    .right-panel,
    .modal-content,
    .cmodal__dialog,
    .ccp-modal-panel,
    .ph-entry,
    .feed-modal-item,
    .table-responsive,
    .card.stage-card,
    .pt-info-card,
    .pt-chart-card,
    .pt-stat-card,
    .product-chip,
    .customer-chip {
        border-color: var(--ma-border) !important;
    }

    /* Remove borders from soft/simple elements */
    .cn-pill,
    .product-mini-badge,
    .status-badge,
    .pt-status-pill,
    .feed-modal-pill,
    .cfs-pill,
    .live-feed-pill,
    .badge,
    .badge-primary,
    .badge-danger,
    .badge-pill,
    .project-status-pill,
    .project-metric,
    .price-badge,
    .time-badge,
    .cn-info-icon,
    .feed-modal-icon-pill,
    .customer-feed-strip .cfs-icon,
    .customer-live-feed .live-feed-icon,
    .feed-modal-title-icon,
    .badge-icon {
        border: 0 !important;
    }

    /* Buttons: no border unless important action */
    .btn-xs.cn-edit-btn,
    .cn-edit-btn,
    .btn-pill-sm,
    .cfs-btn,
    .live-feed-btn,
    .minimize-btn,
    .dashboard-btn,
    .nav-section-btn,
    .ph-close-btn,
    .ccp-modal-close-btn,
    .right-panel .btn,
    .btn-outline-primary,
    .btn-outline-danger,
    .btn-success,
    .btn-primary {
        border: 0 !important;
        box-shadow: none !important;
    }

    /* Keep primary action buttons visible */
    .btn-pill-info,
    .cfs-btn-expand,
    .live-feed-btn[data-feed-expand],
    #mainContentToggle,
    .mobile-sidebar-open-btn,
    .customerSidebar .minimize-btn {
        border: 0 !important;
        background: var(--ma-heading) !important;
        color: #ffffff !important;
    }

    /* Header: clean white card, no shadow */
    .customer-nav,
    .customer-navs {
        box-shadow: none !important;
        border: 1px solid var(--ma-border) !important;
        background: #ffffff !important;
    }

    /* Toggle button clean */
    .cn-toggle-btn {
        box-shadow: none !important;
        border: 0 !important;
        background: var(--ma-blue-soft) !important;
        color: var(--ma-heading) !important;
    }

    .cn-toggle-btn:hover {
        background: var(--ma-heading) !important;
        color: #ffffff !important;
        transform: none !important;
    }


    /* Lead history quick buttons */
    .cn-history-btn {
        position: absolute;
        top: 12px;
        right: 54px;
        min-height: 34px;
        padding: 0 12px;
        border: 0 !important;
        border-radius: 999px !important;
        background: var(--ma-green, #93c21c) !important;
        color: #ffffff !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 950;
        text-decoration: none !important;
        box-shadow: none !important;
        z-index: 2;
        white-space: nowrap;
    }

    .cn-history-btn:hover {
        background: var(--ma-heading, #74b2d4) !important;
        color: #ffffff !important;
        transform: none !important;
    }

    .cn-history-btn svg,
    .cn-history-btn i {
        width: 15px;
        height: 15px;
        color: #ffffff !important;
        stroke: currentColor;
    }

    .customer-history-side-btn {
        background: var(--ma-green, #93c21c) !important;
        color: #ffffff !important;
        border-color: var(--ma-green, #93c21c) !important;
    }

    .customer-history-side-btn:hover {
        background: var(--ma-heading, #74b2d4) !important;
        color: #ffffff !important;
        border-color: var(--ma-heading, #74b2d4) !important;
    }

    .customer-history-side-btn svg,
    .customer-history-side-btn i {
        color: #ffffff !important;
        stroke: currentColor;
    }

    @media (max-width: 575.98px) {
        .cn-history-btn {
            right: 54px;
            width: 34px;
            min-width: 34px;
            padding: 0;
        }

        .cn-history-btn .cn-history-text {
            display: none !important;
        }
    }

    /* Avatar: no border/shadow */
    .cn-avatar {
        border: 0 !important;
        box-shadow: none !important;
    }

    /* Notes/info/umsatz boxes: only light background, no hard border */
    .cn-notes,
    .cn-notes-small,
    .cn-info-block,
    .inner-col-umsatz {
        border: 0 !important;
        background: var(--ma-soft-bg, #f8fbfd) !important;
        box-shadow: none !important;
    }

    /* Sidebar cards: cleaner */
    .object-header,
    .project-link,
    .project-card,
    .project-link.project-card {
        border: 0 !important;
        background: var(--ma-soft-bg, #f8fbfd) !important;
        box-shadow: none !important;
    }

    .object-header:hover,
    .project-link:hover,
    .project-card:hover {
        background: var(--ma-blue-soft) !important;
        border: 0 !important;
        transform: none !important;
        box-shadow: none !important;
    }

    /* Object images: no border */
    .object-header img {
        border: 0 !important;
    }

    /* Sub nav clean */
    .sub-nav {
        border-left: 0 !important;
        border: 0 !important;
        background: var(--ma-soft-bg, #f8fbfd) !important;
        box-shadow: none !important;
    }

    .sub-nav button,
    .nav-section-btn {
        border: 0 !important;
        background: #ffffff !important;
    }

    .sub-nav button:hover,
    .nav-section-btn:hover,
    .nav-section-btn.active {
        background: var(--ma-blue-soft) !important;
        border: 0 !important;
    }

    /* Feed strip clean */
    .customer-feed-strip,
    .customer-live-feed {
        border: 0 !important;
        background: var(--ma-soft-bg, #f8fbfd) !important;
        box-shadow: none !important;
    }

    /* Table/card cleanup */
    .table-responsive,
    .pt-info-card,
    .pt-chart-card,
    .pt-stat-card,
    .card.stage-card,
    .product-chip,
    .customer-chip {
        border: 0 !important;
        background: var(--ma-soft-bg, #f8fbfd) !important;
        box-shadow: none !important;
    }

    .table thead th {
        border: 0 !important;
        background: var(--ma-blue-soft) !important;
    }

    .table td,
    .table th {
        border-color: rgba(192, 216, 234, .45) !important;
    }

    /* Drawers/modals: remove strong borders/shadows */
    .sidebar-gallery,
    .ph-drawer,
    .modal-content,
    .cmodal__dialog,
    .ccp-modal-panel {
        border: 0 !important;
        box-shadow: none !important;
        background: #ffffff !important;
    }

    .modal-header,
    .feed-modal-header,
    .cmodal__header,
    .ccp-modal-header,
    .sidebar-header {
        border-bottom: 1px solid rgba(192, 216, 234, .45) !important;
    }

    .modal-body,
    .feed-modal-body,
    .cmodal__body,
    .ccp-modal-body {
        background: #ffffff !important;
    }

    /* Feed modal list: minimal separators */
    .feed-modal-item {
        border-bottom: 1px solid rgba(192, 216, 234, .45) !important;
    }

    /* Inputs: keep light border for usability */
    .right-panel .form-control,
    .ccp-modal-panel .form-control,
    .feed-modal-search .form-control,
    .feed-modal-body select,
    input.form-control,
    textarea.form-control,
    select.form-control {
        border: 1px solid rgba(192, 216, 234, .65) !important;
        box-shadow: none !important;
    }

    /* Mobile sidebar: no heavy drawer shadow */
    @media (max-width: 991.98px) {
        .customerSidebar {
            box-shadow: none !important;
            border: 0 !important;
            border-right: 1px solid rgba(192, 216, 234, .65) !important;
        }

        .object-header,
        .project-link.project-card,
        .project-card,
        .sub-nav {
            border: 0 !important;
            box-shadow: none !important;
        }

        .contentStation,
        .right-panel {
            border: 0 !important;
            box-shadow: none !important;
        }

        .mobile-sidebar-open-btn {
            box-shadow: none !important;
        }
    }
</style>
<style>
    /* =========================================================
       SIDEBAR MINIMIZE FIX
       - Sidebar column becomes icon-only
       - Content area automatically grows
       - Product/sub nav closes cleanly
    ========================================================= */

    .layout {
        transition: grid-template-columns .25s ease;
    }

    .layout.sidebar-minimized {
        grid-template-columns: 74px minmax(0, 1fr) 360px !important;
    }

    .layout.sidebar-minimized .customerSidebar {
        width: 74px !important;
        min-width: 74px !important;
        max-width: 74px !important;
        padding: .65rem .4rem !important;
        overflow-x: hidden !important;
    }

    .layout.sidebar-minimized .contentStation {
        width: 100% !important;
        max-width: 100% !important;
    }

    .layout.sidebar-minimized .minimize-btn,
    .layout.sidebar-minimized .dashboard-btn {
        width: 46px !important;
        height: 46px !important;
        min-height: 46px !important;
        padding: 0 !important;
        margin-left: auto !important;
        margin-right: auto !important;
        justify-content: center !important;
        border-radius: 999px !important;
    }

    .layout.sidebar-minimized .minimize-btn .text,
    .layout.sidebar-minimized .dashboard-btn .text,
    .layout.sidebar-minimized .object-header .text,
    .layout.sidebar-minimized .object-header small,
    .layout.sidebar-minimized .object-header img,
    .layout.sidebar-minimized .project-card,
    .layout.sidebar-minimized .project-link,
    .layout.sidebar-minimized .product-list,
    .layout.sidebar-minimized .sub-nav,
    .layout.sidebar-minimized .object-address,
    .layout.sidebar-minimized .project-card-title-block,
    .layout.sidebar-minimized .project-card-footer,
    .layout.sidebar-minimized .project-status-pill {
        display: none !important;
    }

    .layout.sidebar-minimized .object-section {
        margin-bottom: .45rem !important;
    }

    .layout.sidebar-minimized .object-header {
        width: 46px !important;
        height: 46px !important;
        min-height: 46px !important;
        padding: 0 !important;
        margin: 0 auto .45rem !important;
        justify-content: center !important;
        align-items: center !important;
        border-radius: 999px !important;
        background: var(--ma-soft-bg, #f8fbfd) !important;
    }

    .layout.sidebar-minimized .object-header>.d-flex {
        width: 100% !important;
        height: 100% !important;
        justify-content: center !important;
        align-items: center !important;
    }

    .layout.sidebar-minimized .object-header svg,
    .layout.sidebar-minimized .object-header i,
    .layout.sidebar-minimized .object-header .feather {
        margin: 0 !important;
        width: 18px !important;
        height: 18px !important;
        color: var(--ma-heading) !important;
    }

    .layout.sidebar-minimized .minimize-btn svg,
    .layout.sidebar-minimized .dashboard-btn svg,
    .layout.sidebar-minimized .minimize-btn i,
    .layout.sidebar-minimized .dashboard-btn i {
        margin: 0 !important;
    }

    .product-list,
    .sub-nav {
        display: none;
    }

    .product-list.show {
        display: block !important;
    }

    .sub-nav.show {
        display: block !important;
    }

    .contentStation.expanded {
        width: 100% !important;
    }

    @media (max-width: 991.98px) {

        .layout,
        .layout.sidebar-minimized {
            display: flex !important;
            grid-template-columns: none !important;
        }

        .layout.sidebar-minimized .customerSidebar,
        .customerSidebar.minimized {
            width: min(88vw, 390px) !important;
            max-width: min(88vw, 390px) !important;
            padding: .85rem !important;
        }

        .layout.sidebar-minimized .object-header .text,
        .layout.sidebar-minimized .object-header small,
        .layout.sidebar-minimized .object-header img,
        .layout.sidebar-minimized .project-card,
        .layout.sidebar-minimized .project-link {
            display: initial !important;
        }

        .layout.sidebar-minimized .product-list,
        .layout.sidebar-minimized .sub-nav {
            display: none !important;
        }

        .layout.sidebar-minimized .product-list.show,
        .layout.sidebar-minimized .sub-nav.show {
            display: block !important;
        }
    }

    .layout.sidebar-minimized {
        grid-template-columns: 74px minmax(0, 1fr) 360px !important;
    }

    .layout.sidebar-minimized .customerSidebar {
        width: 74px !important;
        min-width: 74px !important;
        max-width: 74px !important;
        padding: .65rem .4rem !important;
        overflow-x: hidden !important;
    }

    .layout.sidebar-minimized .contentStation {
        width: 100% !important;
        max-width: 100% !important;
    }

    .layout.sidebar-minimized .object-header .text,
    .layout.sidebar-minimized .object-header small,
    .layout.sidebar-minimized .object-header img,
    .layout.sidebar-minimized .product-list,
    .layout.sidebar-minimized .sub-nav,
    .layout.sidebar-minimized .project-link,
    .layout.sidebar-minimized .project-card,
    .layout.sidebar-minimized .dashboard-btn .text {
        display: none !important;
    }

    .layout.sidebar-minimized .object-header,
    .layout.sidebar-minimized .dashboard-btn,
    .layout.sidebar-minimized .minimize-btn {
        width: 46px !important;
        height: 46px !important;
        min-height: 46px !important;
        padding: 0 !important;
        margin-left: auto !important;
        margin-right: auto !important;
        justify-content: center !important;
        align-items: center !important;
        border-radius: 999px !important;
    }

    .product-list,
    .sub-nav {
        display: none;
    }

    .product-list.show,
    .sub-nav.show {
        display: block !important;
    }

    /* =========================================================
   PROFILE FULLSCREEN FIX
   Main content and notes panel fullscreen without inline display
========================================================= */

    .layout.main-fullscreen-mode,
    .layout.notes-fullscreen-mode {
        grid-template-columns: minmax(0, 1fr) !important;
        grid-template-rows: minmax(0, 1fr) !important;
    }

    .fullscreen-hidden {
        display: none !important;
    }

    .layout.main-fullscreen-mode .contentStation,
    .layout.notes-fullscreen-mode .right-panel {
        grid-column: 1 / -1 !important;
        grid-row: 1 / -1 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100% !important;
        min-height: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        border-radius: var(--ma-radius-lg) !important;
        overflow: hidden !important;
    }

    .layout.main-fullscreen-mode .main-content,
    .layout.main-fullscreen-mode #mainContent {
        height: 100% !important;
        min-height: 0 !important;
        overflow: auto !important;
    }

    .layout.notes-fullscreen-mode .right-panel {
        min-height: calc(100vh - 180px) !important;
    }

    .layout.notes-fullscreen-mode #note-scroll-wrapper {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        overflow-y: auto !important;
    }

    .layout.notes-fullscreen-mode #note-list {
        min-height: 100% !important;
    }

    #mainContentToggle,
    #btnToggleRightPanelFullscreen {
        z-index: 50 !important;
    }

    @media (max-width: 991.98px) {

        .layout.main-fullscreen-mode,
        .layout.notes-fullscreen-mode {
            display: flex !important;
            flex-direction: column !important;
        }

        .layout.main-fullscreen-mode .contentStation,
        .layout.notes-fullscreen-mode .right-panel {
            width: 100% !important;
            min-height: calc(100vh - 130px) !important;
        }
    }
</style>
<style>
    /* =========================================================
   RIGHT NOTE SIDEBAR HIDE / SHOW
========================================================= */

    .layout.notes-hidden-mode {
        grid-template-columns: 310px minmax(0, 1fr) !important;
    }

    .layout.notes-hidden-mode .right-panel {
        display: none !important;
    }

    .layout.notes-hidden-mode .contentStation {
        grid-column: auto !important;
        width: 100% !important;
        max-width: 100% !important;
    }



    #showRightPanelBtn svg,
    #showRightPanelBtn i,
    #showRightPanelBtn .feather {
        color: #ffffff !important;
        stroke: currentColor;
    }

    #showRightPanelBtn.d-none {
        display: none !important;
    }

    .customerSidebar.minimized~.contentStation {
        width: 100% !important;
    }

    @media (max-width: 1199.98px) {
        .layout.notes-hidden-mode {
            grid-template-columns: 280px minmax(0, 1fr) !important;
            grid-template-rows: minmax(0, 1fr) !important;
        }
    }

    @media (max-width: 991.98px) {
        .layout.notes-hidden-mode {
            display: flex !important;
            flex-direction: column !important;
        }

        .layout.notes-hidden-mode .right-panel {
            display: none !important;
        }


    }

    /* =========================================================
   SHOW NOTES BUTTON INSIDE MAIN CONTENT
========================================================= */

    /* Show notes button: same style as #mainContentToggle */
    .show-notes-inside-main {
        position: absolute !important;
        top: .55rem !important;
        right: 3.35rem !important;
        z-index: 55 !important;

        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        padding: 0 !important;

        border: 0 !important;
        border-radius: 999px !important;
        background: var(--ma-heading) !important;
        color: #ffffff !important;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        line-height: 1 !important;
    }

    .show-notes-inside-main span {
        display: none !important;
    }

    .show-notes-inside-main svg,
    .show-notes-inside-main i,
    .show-notes-inside-main .feather {
        width: 16px !important;
        height: 16px !important;
        margin: 0 !important;
        color: #ffffff !important;
        stroke: currentColor !important;
    }

    .show-notes-inside-main.d-none {
        display: none !important;
    }

    .layout:not(.notes-hidden-mode) .show-notes-inside-main {
        display: none !important;
    }

    .layout.notes-hidden-mode .show-notes-inside-main:not(.d-none) {
        display: inline-flex !important;
    }

    .show-notes-inside-main svg,
    .show-notes-inside-main i,
    .show-notes-inside-main .feather {
        width: 16px;
        height: 16px;
        color: #ffffff !important;
        stroke: currentColor;
    }

    .show-notes-inside-main.d-none {
        display: none !important;
    }

    .layout:not(.notes-hidden-mode) .show-notes-inside-main {
        display: none !important;
    }

    .layout.notes-hidden-mode .show-notes-inside-main:not(.d-none) {
        display: inline-flex !important;
    }

    /* ✅ When left sidebar is hidden/minimized AND notes sidebar is hidden,
   main content must take full width */
    .layout.sidebar-minimized.notes-hidden-mode {
        grid-template-columns: minmax(0, 1fr) !important;
    }

    /* ✅ Fully remove the left sidebar space in this combined mode */
    .layout.sidebar-minimized.notes-hidden-mode .customerSidebar {
        display: none !important;
    }

    /* ✅ Main content becomes full width */
    .layout.sidebar-minimized.notes-hidden-mode .contentStation {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* ✅ Keep object icon bar visible, hide notes, main content uses the rest */
    .layout.sidebar-minimized.notes-hidden-mode {
        grid-template-columns: 74px minmax(0, 1fr) !important;
    }

    .layout.sidebar-minimized.notes-hidden-mode .customerSidebar {
        display: block !important;
        width: 74px !important;
        min-width: 74px !important;
        max-width: 74px !important;
    }

    .layout.sidebar-minimized.notes-hidden-mode .right-panel {
        display: none !important;
    }

    .layout.sidebar-minimized.notes-hidden-mode .contentStation {
        width: 100% !important;
        max-width: 100% !important;
    }

    .layout.sidebar-minimized.notes-hidden-mode {
        grid-template-columns: minmax(0, 1fr) !important;
    }

    .layout.sidebar-minimized.notes-hidden-mode .customerSidebar {
        display: none !important;
    }

    .layout.sidebar-minimized.notes-hidden-mode .right-panel {
        display: none !important;
    }

    .layout.sidebar-minimized.notes-hidden-mode .contentStation {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    @media (max-width: 991.98px) {
        .show-notes-inside-main {
            top: .65rem;
            right: .75rem;
            height: 40px;
            min-width: 104px;
        }
    }

    .ma-sub-nav-btn {
        width: 100%;
        min-height: 44px;
        padding: .42rem .55rem !important;
        border: 0 !important;
        border-radius: 14px !important;
        background: #ffffff !important;
        color: var(--ma-text) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: .5rem !important;
        text-align: left !important;
        transition: background .18s ease, transform .18s ease;
    }

    .ma-sub-nav-left {
        display: flex;
        align-items: center;
        gap: .55rem;
        min-width: 0;
        flex: 1;
    }

    .ma-sub-nav-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--ma-blue-soft);
        color: var(--ma-heading);
    }

    .ma-sub-nav-icon svg,
    .ma-sub-nav-icon i,
    .ma-sub-nav-icon .feather {
        width: 14px !important;
        height: 14px !important;
        stroke: currentColor !important;
        color: currentColor !important;
        margin: 0 !important;
    }

    .ma-sub-nav-text {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        min-width: 0;
        flex: 1;
        line-height: 1.05;
    }

    .ma-sub-nav-label {
        display: block;
        width: 100%;
        font-size: 14px;
        font-weight: 900;
        color: var(--ma-text) !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ma-sub-nav-desc {
        display: block;
        width: 100%;
        margin-top: .1rem;
        font-size: 12px !important;
        line-height: 1.1 !important;
        font-weight: 700;
        color: var(--ma-muted) !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-count-badge {
        min-width: 24px;
        height: 22px;
        padding: 0 .45rem;
        border-radius: 999px;
        background: var(--ma-green) !important;
        color: #ffffff !important;
        font-size: .66rem;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    @media (max-width: 575.98px) {
        .ma-sub-nav-desc {
            font-size: .52rem !important;
        }

        .ma-sub-nav-label {
            font-size: .74rem;
        }

        .ma-sub-nav-btn {
            min-height: 42px;
            padding: .38rem .5rem !important;
        }

        .ma-sub-nav-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
        }
    }

    /* =========================================================
   MOBILE OBJECT SIDEBAR - VISIBLE DRAWER + PEEK HANDLE
========================================================= */

    .mobile-sidebar-backdrop,
    .mobile-sidebar-open-btn {
        display: none;
    }

    @media (max-width: 991.98px) {
        body.mobile-sidebar-open {
            overflow: hidden !important;
            touch-action: none;
        }

        .customer-wrapper {
            height: auto !important;
            min-height: 100vh !important;
            overflow: visible !important;
        }

        .layout,
        .layout.sidebar-minimized,
        .layout.notes-hidden-mode,
        .layout.sidebar-minimized.notes-hidden-mode {
            display: flex !important;
            flex-direction: column !important;
            grid-template-columns: none !important;
            grid-template-rows: none !important;
            gap: .75rem !important;
            padding: .5rem !important;
            overflow: visible !important;
            min-height: auto !important;
        }

        /*
      Floating button visible on mobile.
      It clearly shows that objects/sidebar exist.
    */
        .mobile-sidebar-open-btn {
            position: fixed !important;
            left: 14px !important;
            bottom: 18px !important;
            z-index: 10050 !important;

            min-width: 118px !important;
            height: 52px !important;
            padding: 0 16px !important;

            border-radius: 999px !important;
            border: 0 !important;
            background: var(--ma-heading) !important;
            color: #ffffff !important;

            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: .5rem !important;

            font-size: .86rem !important;
            font-weight: 900 !important;
            letter-spacing: .02em !important;
            box-shadow: 0 12px 30px rgba(116, 178, 212, .35) !important;
        }

        .mobile-sidebar-open-btn span {
            color: #ffffff !important;
            font-size: .86rem !important;
            font-weight: 900 !important;
        }

        .mobile-sidebar-open-btn svg,
        .mobile-sidebar-open-btn i,
        .mobile-sidebar-open-btn .feather {
            width: 18px !important;
            height: 18px !important;
            color: #ffffff !important;
            stroke: currentColor !important;
        }

        .mobile-sidebar-open-btn:hover {
            background: var(--ma-orange) !important;
            color: #ffffff !important;
        }

        body.mobile-sidebar-open .mobile-sidebar-open-btn {
            display: none !important;
        }

        /*
      Small left handle, so the sidebar is "visible somehow"
      even before opening.
    */

        /* =========================================================
   CUSTOMER REVIEW SUMMARY NEAR NAME
========================================================= */

        .cn-review-summary {
            display: inline-flex;
            align-items: center;
            gap: .28rem;
            min-height: 28px;
            padding: .25rem .55rem;
            border-radius: 999px;
            background: rgba(248, 172, 0, .13) !important;
            color: var(--ma-text) !important;
            font-size: .72rem;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
        }

        .cn-review-summary svg,
        .cn-review-summary i,
        .cn-review-summary .feather {
            width: 13px !important;
            height: 13px !important;
            color: var(--ma-orange) !important;
            stroke: currentColor !important;
        }

        .cn-review-summary-stars {
            display: inline-flex;
            align-items: center;
            gap: 1px;
        }

        .cn-review-summary-stars .is-filled {
            color: var(--ma-orange) !important;
        }

        .cn-review-summary-stars .is-empty {
            color: var(--ma-blue-soft) !important;
        }

        .cn-review-summary-score {
            color: var(--ma-text) !important;
            font-weight: 950;
        }

        .cn-review-summary-count {
            color: var(--ma-muted) !important;
            font-size: .68rem;
            font-weight: 800;
        }

        .cn-review-summary-critical {
            background: rgba(229, 6, 86, .12) !important;
            color: var(--ma-pink) !important;
        }

        .cn-collapsed-review-summary {
            margin-top: 5px;
        }

        @media (max-width: 575.98px) {
            .cn-review-summary {
                width: max-content;
                max-width: 100%;
                flex-wrap: wrap;
                white-space: normal;
                line-height: 1.25;
            }
        }

        .customerSidebar::after {
            content: "Objekte";
            position: fixed;
            top: 48%;
            right: -72px;
            transform: translateY(-50%) rotate(-90deg);
            transform-origin: center;

            width: 104px;
            height: 34px;
            border-radius: 999px 999px 0 0;
            background: var(--ma-heading);
            color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: .78rem;
            font-weight: 900;
            letter-spacing: .04em;
            z-index: 10041;
            pointer-events: none;
        }

        body.mobile-sidebar-open .customerSidebar::after {
            display: none !important;
        }

        .mobile-sidebar-backdrop {
            position: fixed !important;
            inset: 0 !important;
            z-index: 10030 !important;
            background: rgba(55, 65, 81, .42) !important;
            display: none !important;
            backdrop-filter: blur(2px);
        }

        body.mobile-sidebar-open .mobile-sidebar-backdrop {
            display: block !important;
        }

        /*
      Drawer sidebar.
      It is slightly visible with translateX(calc(-100% + 18px)).
      When opened, it slides fully in.
    */
        .customerSidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;

            width: min(88vw, 390px) !important;
            min-width: min(88vw, 390px) !important;
            max-width: min(88vw, 390px) !important;

            height: 100dvh !important;
            max-height: 100dvh !important;

            z-index: 10040 !important;

            transform: translateX(calc(-100% + 18px)) !important;
            transition: transform .28s ease, box-shadow .28s ease !important;

            overflow-y: auto !important;
            overflow-x: hidden !important;

            padding: .85rem !important;
            border-radius: 0 24px 24px 0 !important;
            background: var(--ma-bg) !important;
            border: 0 !important;
            border-right: 1px solid rgba(192, 216, 234, .75) !important;
            box-shadow: none !important;
        }

        body.mobile-sidebar-open .customerSidebar {
            transform: translateX(0) !important;
            box-shadow: 18px 0 45px rgba(55, 65, 81, .18) !important;
        }

        .customerSidebar.minimized,
        .layout.sidebar-minimized .customerSidebar,
        .layout.sidebar-minimized.notes-hidden-mode .customerSidebar {
            display: block !important;

            width: min(88vw, 390px) !important;
            min-width: min(88vw, 390px) !important;
            max-width: min(88vw, 390px) !important;

            padding: .85rem !important;
            transform: translateX(calc(-100% + 18px)) !important;
        }

        body.mobile-sidebar-open .customerSidebar.minimized,
        body.mobile-sidebar-open .layout.sidebar-minimized .customerSidebar,
        body.mobile-sidebar-open .layout.sidebar-minimized.notes-hidden-mode .customerSidebar {
            transform: translateX(0) !important;
        }

        /*
      In mobile, never hide object/product text because users need context.
    */
        .layout.sidebar-minimized .customerSidebar .text,
        .layout.sidebar-minimized .customerSidebar small,
        .layout.sidebar-minimized .customerSidebar .object-header .text,
        .layout.sidebar-minimized .customerSidebar .object-header small,
        .layout.sidebar-minimized .customerSidebar .object-header img,
        .layout.sidebar-minimized .customerSidebar .project-card,
        .layout.sidebar-minimized .customerSidebar .project-link,
        .layout.sidebar-minimized .customerSidebar .project-card-title-block,
        .layout.sidebar-minimized .customerSidebar .project-card-footer,
        .layout.sidebar-minimized .customerSidebar .project-status-pill,
        .customerSidebar.minimized .text,
        .customerSidebar.minimized small,
        .customerSidebar.minimized .object-header .text,
        .customerSidebar.minimized .object-header small,
        .customerSidebar.minimized .object-header img,
        .customerSidebar.minimized .project-card-title-block,
        .customerSidebar.minimized .project-card-footer,
        .customerSidebar.minimized .project-status-pill {
            display: initial !important;
        }

        .customerSidebar.minimized .dashboard-btn .text,
        .layout.sidebar-minimized .dashboard-btn .text {
            display: inline !important;
        }

        .customerSidebar.minimized .project-card,
        .customerSidebar.minimized .project-link,
        .layout.sidebar-minimized .project-card,
        .layout.sidebar-minimized .project-link {
            display: flex !important;
        }

        .customerSidebar.minimized .product-list,
        .customerSidebar.minimized .sub-nav,
        .layout.sidebar-minimized .product-list,
        .layout.sidebar-minimized .sub-nav {
            display: none !important;
        }

        .customerSidebar.minimized .product-list.show,
        .customerSidebar.minimized .sub-nav.show,
        .layout.sidebar-minimized .product-list.show,
        .layout.sidebar-minimized .sub-nav.show {
            display: block !important;
        }

        /*
      Close button inside drawer.
    */
        .customerSidebar .minimize-btn {
            position: sticky !important;
            top: 0 !important;
            z-index: 8 !important;

            width: 100% !important;
            height: 46px !important;
            min-height: 46px !important;

            margin: 0 0 .75rem 0 !important;
            padding: 0 1rem !important;

            justify-content: center !important;
            background: var(--ma-heading) !important;
            color: #ffffff !important;
            border: 0 !important;
            border-radius: 999px !important;

            font-weight: 900 !important;
        }

        .customerSidebar .minimize-btn::after {
            content: "Menü schließen";
            color: #ffffff !important;
            font-size: .86rem;
            font-weight: 900;
            margin-left: .45rem;
        }

        .customerSidebar .minimize-btn svg,
        .customerSidebar .minimize-btn i,
        .customerSidebar .minimize-btn .feather {
            color: #ffffff !important;
            stroke: currentColor !important;
        }

        .customerSidebar .dashboard-btn {
            width: 100% !important;
            min-height: 46px !important;
            justify-content: center !important;
            background: var(--ma-bg) !important;
            color: var(--ma-heading) !important;
            border: 1px solid var(--ma-border) !important;
            border-radius: 999px !important;
            margin-bottom: .9rem !important;
        }

        .customerSidebar .dashboard-btn .text {
            color: var(--ma-heading) !important;
            display: inline !important;
        }

        .object-section {
            margin-bottom: .85rem !important;
        }

        .object-header {
            border-radius: 18px !important;
            padding: .85rem !important;
            background: var(--ma-soft-bg, #f8fbfd) !important;
            border: 0 !important;
            align-items: flex-start !important;
            gap: .65rem !important;
        }

        .object-header .text {
            display: inline !important;
            color: var(--ma-heading) !important;
            font-size: .95rem !important;
            font-weight: 900 !important;
        }

        .object-header small {
            display: block !important;
            color: var(--ma-muted) !important;
            font-size: .75rem !important;
            line-height: 1.25 !important;
            margin-top: .2rem !important;
        }

        .object-header img {
            width: 56px !important;
            height: 56px !important;
            max-width: 56px !important;
            max-height: 56px !important;
            border-radius: 16px !important;
            object-fit: cover !important;
            border: 0 !important;
        }

        .project-link.project-card,
        .project-card {
            display: flex !important;
            border-radius: 18px !important;
            padding: .85rem !important;
            margin-bottom: .65rem !important;
            background: var(--ma-soft-bg, #f8fbfd) !important;
            border: 0 !important;
        }

        .project-card-title {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: unset !important;
            font-size: .92rem !important;
            line-height: 1.25 !important;
        }

        .project-card-meta {
            font-size: .75rem !important;
            line-height: 1.35 !important;
        }

        .project-card-footer {
            width: 100% !important;
            margin-top: .35rem !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: .45rem !important;
        }

        .project-footer-right {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: 1fr 46px !important;
            gap: .45rem !important;
        }

        .project-metric {
            width: 100% !important;
            min-height: 42px !important;
            border-radius: 14px !important;
            justify-content: center !important;
        }

        .sub-nav {
            margin: .2rem 0 .8rem 0 !important;
            padding: .65rem !important;
            border-left: 0 !important;
            border-radius: 18px !important;
            background: var(--ma-soft-bg, #f8fbfd) !important;
            border: 0 !important;
        }

        .sub-nav.show {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: .4rem !important;
        }

        .sidebar-amount-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 20px;
            padding: 2px 7px;
            border-radius: 999px;
            background: rgba(147, 194, 28, .12);
            color: #93c21c;
            border: 1px solid rgba(147, 194, 28, .22);
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .sidebar-amount-badge.is-zero {
            background: rgba(100, 116, 139, .10);
            color: #64748b;
            border-color: rgba(100, 116, 139, .18);
        }

        .sidebar-amount-badge.is-loading {
            opacity: .65;
        }

        .sidebar-amount-badge.is-error {
            background: rgba(220, 38, 38, .10);
            color: #991b1b;
            border-color: rgba(220, 38, 38, .22);
        }

        .sub-nav button,
        .nav-section-btn,
        .ma-sub-nav-btn {
            width: 100% !important;
            min-height: 44px !important;
            justify-content: space-between !important;
            text-align: left !important;
            padding: .65rem .8rem !important;
            border-radius: 14px !important;
            background: #ffffff !important;
            color: var(--ma-text) !important;
            border: 0 !important;
            font-size: .84rem !important;
        }

        .contentStation {
            min-height: 65vh !important;
            background: var(--ma-bg) !important;
            border: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .right-panel {
            min-height: 380px !important;
            background: var(--ma-bg) !important;
            border: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        #mainContentToggle {
            display: none !important;
        }
    }

    @media (max-width: 575.98px) {

        .customerSidebar,
        .customerSidebar.minimized,
        .layout.sidebar-minimized .customerSidebar {
            width: 92vw !important;
            min-width: 92vw !important;
            max-width: 92vw !important;
            border-radius: 0 22px 22px 0 !important;
        }

        .mobile-sidebar-open-btn {
            left: 12px !important;
            bottom: 14px !important;
            min-width: 108px !important;
            height: 50px !important;
            padding: 0 14px !important;
        }

        .mobile-sidebar-open-btn span {
            font-size: .82rem !important;
        }

        .customerSidebar::after {
            right: -68px;
            width: 96px;
            height: 32px;
            font-size: .72rem;
        }
    }

    .ma-sub-nav-amount-line {
        display: block;
        width: max-content;
        max-width: 100%;
        margin-top: 4px;
        padding: 3px 7px;
        border-radius: 999px;
        background: rgba(147, 194, 28, .12) !important;
        color: #93c21c !important;
        font-size: 10px !important;
        font-weight: 900 !important;
        line-height: 1.1 !important;
        white-space: nowrap;
    }

    .ma-sub-nav-amount-line.is-zero {
        background: rgba(100, 116, 139, .10) !important;
        color: #64748b !important;
    }

    .ma-sub-nav-amount-line.is-loading {
        opacity: .65;
    }

    .ma-sub-nav-amount-line.is-error {
        background: rgba(220, 38, 38, .10) !important;
        color: #991b1b !important;
    }

    .layout.sidebar-minimized .ma-sub-nav-amount-line {
        display: none !important;
    }
</style>
<style>
    .sidebar-gallery {
        position: fixed;
        top: 0;
        right: 0;
        width: min(520px, 100vw);
        height: 100vh;
        max-height: 100vh;
        background: #fff;
        z-index: 9999;
        transform: translateX(100%);
        transition: transform .25s ease;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .sidebar-gallery.active {
        transform: translateX(0);
    }

    .sidebar-gallery .map-container,
    [id^="mapContainer"] {
        width: 100% !important;
        height: 520px !important;
        min-height: 520px !important;
        display: block !important;
        position: relative !important;
        overflow: hidden !important;
        border-radius: 16px;
        background: #eef2f7;
    }

    .sidebar-gallery .gm-style {
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 768px) {
        .sidebar-gallery {
            width: 100vw;
        }

        .sidebar-gallery .map-container,
        [id^="mapContainer"] {
            height: 65vh !important;
            min-height: 65vh !important;
        }
    }

    /* =========================================================
   🔥 GOOGLE MAPS KACHEL- & DARSTELLUNGS-RESET
   Verhindert, dass Theme-CSS die Map-Tiles verformt.
========================================================= */

    /* 1. Kacheln (Bilder) absolut sauber und kantenlos rendern */
    .gm-style img {
        max-width: none !important;
        min-width: 0 !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        /* Entfernt die runden Ecken der Kacheln */
        box-shadow: none !important;
        object-fit: fill !important;
    }

    /* 2. Interne Map-Layer vor Flexbox/Grid/Margin-Vererbung schützen */
    .gm-style div {
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        box-sizing: content-box !important;
        background: transparent !important;
    }

    /* 3. Stellt sicher, dass der Hauptcontainer saubere Dimensionen hat */
    .sidebar-gallery .map-container,
    [id^="mapContainer"] {
        width: 100% !important;
        height: 520px !important;
        min-height: 520px !important;
        display: block !important;
        position: relative !important;
        overflow: hidden !important;
        border-radius: 16px !important;
        /* Nur der Außenrahmen darf rund sein */
        background: #eef2f7 !important;
        box-sizing: border-box !important;
    }

    @media (max-width: 768px) {

        .sidebar-gallery .map-container,
        [id^="mapContainer"] {
            height: 65vh !important;
            min-height: 65vh !important;
        }
    }

    .object-thumb-link {
        display: inline-block;
        position: relative;
        width: 301px;
        height: 91px;
        min-width: 100px;
        border-radius: 12px;
        overflow: hidden;
        background: var(--ma-blue-soft, #c0d8ea);
    }



    .object-thumb-link-fallback::after {
        content: "Street View";
        position: absolute;
        left: 6px;
        bottom: 5px;
        z-index: 2;
        padding: 2px 6px;
        border-radius: 999px;
        background: rgba(55, 65, 81, .82);
        color: #ffffff;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
    }

    .object-thumb-streetview {
        filter: saturate(1.08) contrast(1.03);
    }
</style>
<style>
    .house-shot-tabs {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .35rem;
        border-radius: 999px;
        background: var(--ma-soft-bg, #f8fbfd);
    }

    .house-shot-tab-btn {
        border: 0 !important;
        border-radius: 999px;
        padding: .45rem .85rem;
        background: transparent;
        color: var(--ma-heading);
        font-size: 12px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        cursor: pointer;
    }

    .house-shot-tab-btn.is-active {
        background: var(--ma-heading) !important;
        color: #ffffff !important;
    }

    .house-shot-panel {
        width: 100%;
    }

    .house-shot-upload-box {
        padding: 1rem;
        border-radius: 16px;
        border: 1px solid var(--ma-border);
        background: var(--ma-bg);
    }

    @media (max-width: 575.98px) {
        .house-shot-tabs {
            flex-direction: column;
            align-items: stretch;
            border-radius: 16px;
        }

        .house-shot-tab-btn {
            width: 100%;
        }
    }
</style>

<style>
    .object-thumb-link {
        position: relative;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 16px;
    }

    .object-thumb-link.has-house-screenshot {
        border: 2px solid var(--ma-green) !important;
        background: #ffffff !important;
    }

    .object-thumb-link .object-thumb-screenshot,
    .object-thumb-link img.object-thumb-screenshot {
        width: 160px !important;
        height: 100px !important;
        max-width: 160px !important;
        max-height: 100px !important;
        min-width: 160px !important;
        min-height: 100px !important;
        object-fit: cover !important;
        border-radius: 14px !important;
    }

    .object-thumb-link .object-thumb-screenshot.is-live-updated {
        animation: houseShotPulse .9s ease;
    }

    .object-shot-live-badge {
        position: absolute;
        left: 8px;
        bottom: 8px;
        z-index: 3;
        padding: .22rem .55rem;
        border-radius: 999px;
        background: var(--ma-green);
        color: #ffffff !important;
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        pointer-events: none;
    }

    @keyframes houseShotPulse {
        0% {
            transform: scale(.96);
            opacity: .55;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @media (max-width: 991.98px) {

        .object-thumb-link .object-thumb-screenshot,
        .object-thumb-link img.object-thumb-screenshot {
            width: 120px !important;
            height: 78px !important;
            min-width: 120px !important;
            min-height: 78px !important;
        }
    }

    .sidebar-count-badge.is-pulse {
        animation: sidebarCountPulse .42s ease;
    }

    @keyframes sidebarCountPulse {
        0% {
            transform: scale(1);
        }

        45% {
            transform: scale(1.18);
        }

        100% {
            transform: scale(1);
        }
    }

    .ma-feed-date-block {
    margin-bottom: .75rem;
}

.ma-feed-date-title {
    display: flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .55rem;
    margin-bottom: .45rem;
    border-radius: 999px;
    background: #f8fbfd;
    color: var(--ma-heading, #74b2d4);
    font-size: 12px;
    font-weight: 900;
}

.ma-feed-preview {
    margin-top: 2px;
    font-size: 11px;
    color: var(--ma-muted, #6b7280);
}

.ma-feed-author {
    font-size: 12px;
    color: var(--ma-heading, #74b2d4);
}

.ma-feed-comment {
    padding: .55rem;
    margin-bottom: .45rem;
    border: 1px solid var(--ma-border, #c0d8ea);
    border-radius: 14px;
    background: #fff;
}

.ma-feed-comment.is-reply {
    margin-left: 1.25rem;
    background: #f8fbfd;
}

.ma-feed-replies {
    margin-top: .5rem;
}

.ma-feed-mini-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    padding: .35rem 0;
    border-bottom: 1px dashed rgba(192, 216, 234, .75);
    font-size: 12px;
}

.ma-feed-mini-row:last-child {
    border-bottom: 0;
}

.ma-feed-mini-row span {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    min-width: 0;
}

.ma-feed-mini-row small {
    color: var(--ma-muted, #6b7280);
    text-align: right;
}

.ma-feed-people {
    display: flex;
    flex-wrap: wrap;
    gap: .35rem;
}

.ma-feed-person {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .2rem .45rem;
    border: 1px solid var(--ma-border, #c0d8ea);
    border-radius: 999px;
    background: #fff;
    font-size: 11px;
}

.ma-feed-avatar-fallback {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--ma-blue, #74b2d4);
    color: #fff;
    font-size: 11px;
    font-weight: 900;
    flex: 0 0 auto;
}

.ma-feed-next-step {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .5rem;
    border-radius: 999px;
    background: #fff7e6;
    color: #9a6700;
    font-size: 11px;
    font-weight: 800;
}
</style>

<style>
/* =========================================================
   FINAL FIX: CUSTOMER SIDEBAR PRODUCT OPEN / CLOSE
   Use this once, after the main customer-sidebar CSS.
========================================================= */

#customerSidebar .ma-product-card[data-product-key],
.customerSidebar .ma-product-card[data-product-key],
#customerSidebar .project-card[data-product-key],
.customerSidebar .project-card[data-product-key],
#customerSidebar .project-link[data-product-key],
.customerSidebar .project-link[data-product-key] {
    position: relative !important;
    user-select: none;
    cursor: pointer;
    padding-right: 2.45rem !important;
}

#customerSidebar .ma-product-card[data-product-key]::after,
.customerSidebar .ma-product-card[data-product-key]::after,
#customerSidebar .project-card[data-product-key]::after,
.customerSidebar .project-card[data-product-key]::after,
#customerSidebar .project-link[data-product-key]::after,
.customerSidebar .project-link[data-product-key]::after {
    content: "";
    position: absolute;
    top: 1.05rem;
    right: .95rem;
    width: 9px;
    height: 9px;
    border-right: 2px solid var(--ma-heading, #74b2d4);
    border-bottom: 2px solid var(--ma-heading, #74b2d4);
    transform: rotate(45deg);
    transition: transform .18s ease, top .18s ease;
    pointer-events: none;
}

#customerSidebar .ma-product-card[data-product-key].is-open::after,
.customerSidebar .ma-product-card[data-product-key].is-open::after,
#customerSidebar .project-card[data-product-key].is-open::after,
.customerSidebar .project-card[data-product-key].is-open::after,
#customerSidebar .project-link[data-product-key].is-open::after,
.customerSidebar .project-link[data-product-key].is-open::after {
    top: 1.2rem;
    transform: rotate(-135deg);
}

#customerSidebar .ma-product-card[data-product-key].is-open,
.customerSidebar .ma-product-card[data-product-key].is-open,
#customerSidebar .project-card[data-product-key].is-open,
.customerSidebar .project-card[data-product-key].is-open,
#customerSidebar .project-link[data-product-key].is-open,
.customerSidebar .project-link[data-product-key].is-open {
    border-color: var(--ma-heading, #74b2d4) !important;
    background: #f8fbfd !important;
}

#customerSidebar .sub-nav,
#customerSidebar .ma-sub-nav,
.customerSidebar .sub-nav,
.customerSidebar .ma-sub-nav {
    display: none;
}

#customerSidebar .sub-nav.show,
#customerSidebar .sub-nav.is-open,
#customerSidebar .ma-sub-nav.show,
#customerSidebar .ma-sub-nav.is-open,
.customerSidebar .sub-nav.show,
.customerSidebar .sub-nav.is-open,
.customerSidebar .ma-sub-nav.show,
.customerSidebar .ma-sub-nav.is-open {
    display: block !important;
}

@media (max-width: 991.98px) {
    #customerSidebar .ma-product-card[data-product-key],
    .customerSidebar .ma-product-card[data-product-key],
    #customerSidebar .project-card[data-product-key],
    .customerSidebar .project-card[data-product-key],
    #customerSidebar .project-link[data-product-key],
    .customerSidebar .project-link[data-product-key] {
        padding-right: 2.55rem !important;
    }

    #customerSidebar .sub-nav.show,
    #customerSidebar .sub-nav.is-open,
    #customerSidebar .ma-sub-nav.show,
    #customerSidebar .ma-sub-nav.is-open,
    .customerSidebar .sub-nav.show,
    .customerSidebar .sub-nav.is-open,
    .customerSidebar .ma-sub-nav.show,
    .customerSidebar .ma-sub-nav.is-open {
        display: grid !important;
        grid-template-columns: 1fr;
        gap: .4rem;
    }
}
</style>




<div class="customer-wrapper">
    <div class="customer-nav-wrap">
        @php
$tier = $customer->tier; // null if not explicitly set
$hasPurchase = (float) $customer->total_purchase > 0;

$purchaseDate = $customer->purchase_date
    ? \Carbon\Carbon::parse($customer->purchase_date)->format('d.m.Y')
    : '–';

$totalPurchase = number_format((float) $customer->total_purchase, 2, ',', '.');

$created = \Carbon\Carbon::parse($customer->created_at);
$initials = trim(mb_substr($customer->name ?? '', 0, 1) . mb_substr($customer->lastname ?? '', 0, 1));

// Tier-Label-Logik:
// - Wenn KEIN Umsatz: "Kein Kauf"
// - Wenn Umsatz > 0 und tier gesetzt: "Bronze Kunde" / "Gold Kunde" etc.
// - Wenn Umsatz > 0 und tier NICHT gesetzt: einfach "Kunde"
if (!$hasPurchase) {
    $tierLabel = 'Kein Kauf';
} else {
    $tierLabel = $tier
        ? ucfirst($tier) . ' Kunde'
        : 'Kunde';
}

$purchaseStatus = $customer->purchase_status ?? 'unbekannt';
        @endphp

        @php
$reviewTotal = (int) ($customerReviewSummary['total'] ?? 0);
$reviewAvg = (float) ($customerReviewSummary['avg'] ?? 0);
$reviewCritical = (int) ($customerReviewSummary['critical'] ?? 0);
$reviewRounded = (int) round($reviewAvg);
        @endphp



        {{-- ADD 'is-collapsed' HERE SO IT STARTS HIDDEN --}}
        <div class="customer-nav shadow-sm is-collapsed" id="customerNavWrapper">

            @php
$leadHistoryCustomerId = $customer->id ?? request()->id ?? request()->route('customer') ?? null;
if (is_object($leadHistoryCustomerId)) {
    $leadHistoryCustomerId = $leadHistoryCustomerId->id ?? null;
}
$leadHistoryUrl = \Illuminate\Support\Facades\Route::has('customers.history.show') && $leadHistoryCustomerId
    ? route('customers.history.show', $leadHistoryCustomerId)
    : url('/customers/' . ($leadHistoryCustomerId ?: '') . '/history');
            @endphp

            <a href="{{ $leadHistoryUrl }}" class="cn-history-btn" title="Kundenhistorie öffnen">
                <i data-feather="clock"></i>
                <span class="cn-history-text">Historie</span>
            </a>

            {{-- COLLAPSE TOGGLE BUTTON (SVG) --}}
            <button class="cn-toggle-btn" id="cnToggleBtn" onclick="toggleCustomerNav()" title="Profil ein-/ausklappen">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>


            {{-- COLLAPSED VIEW: Customer compact info + feed only --}}
            <div class="cn-collapsed-content">

                <div class="cn-collapsed-main-row">
                    <div class="cn-avatar" style="width: 42px; height: 42px; font-size: 16px; box-shadow: none;">
                        <span class="cn-avatar-initials">{{ $initials ?: 'K' }}</span>
                    </div>

                    <div class="cn-collapsed-profile-info">
                        <div class="cn-collapsed-name">
                            {{ $customer->title ?? '' }} {{ $customer->academic_title ?? '' }}
                            {{ $customer->name }} {{ $customer->lastname }}
                        </div>

                        <div class="cn-collapsed-review-summary">
                            <span
                                class="cn-review-summary {{ $reviewCritical > 0 ? 'cn-review-summary-critical' : '' }}"
                                title="{{ $reviewTotal }} Bewertungen · Ø {{ number_format($reviewAvg, 1, ',', '.') }} Sterne">
                                <span class="cn-review-summary-stars">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i
                                            class="feather icon-star {{ $s <= $reviewRounded ? 'is-filled' : 'is-empty' }}"></i>
                                    @endfor
                                </span>

                                <span class="cn-review-summary-score">
                                    {{ $reviewTotal > 0 ? number_format($reviewAvg, 1, ',', '.') : '0,0' }}
                                </span>

                                <span class="cn-review-summary-count">
                                    ({{ $reviewTotal }})
                                </span>

                                @if($reviewCritical > 0)
                                    <span class="cn-review-summary-count">
                                        · {{ $reviewCritical }} kritisch
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="cn-collapsed-meta">
                            <span>
                                <i class="feather icon-map-pin"></i>
                                {{ $customer->street }}, {{ $customer->postcode }} {{ $customer->city }}
                            </span>

                            @if($customer->phone || $customer->telephone)
                                <span>
                                    <i class="feather icon-phone"></i>
                                    {{ $customer->phone ?: $customer->telephone }}
                                </span>
                            @endif

                            @if($customer->email)
                                <span>
                                    <i class="feather icon-mail"></i>
                                    {{ $customer->email }}
                                </span>
                            @endif

                            {{-- Jitsi Videocall: Start-Button (F1) — nur bei aktiviertem Feature --}}
                            @if(config('jitsi.enabled'))
                                <span>
                                    <form action="{{ route('video-calls.store', $customer->id) }}" method="POST"
                                          target="_blank" style="display:inline;">
                                        @csrf
                                        <button type="submit" title="Video-Call mit dem Kunden starten"
                                            style="display:inline-flex;align-items:center;gap:4px;border:0;background:#93c21c;color:#fff;border-radius:4px;padding:2px 8px;font-size:12px;cursor:pointer;">
                                            <i class="feather icon-video"></i> Video-Call
                                        </button>
                                    </form>
                                </span>
                            @endif
                        </div>

                        {{-- Jitsi Videocall: schlichte read-only Liste der bisherigen Kunden-Calls (Leitplanke 2) --}}
                        @if(config('jitsi.enabled') && \Illuminate\Support\Facades\Schema::hasTable('video_calls'))
                            @php
                                $customerVideoCalls = \App\Models\VideoCall::with('creator.employee')
                                    ->where('customer_id', $customer->id)
                                    ->whereNotNull('customer_id')
                                    ->latest()->limit(10)->get();
                            @endphp
                            @if($customerVideoCalls->isNotEmpty())
                                <div class="cn-videocalls" style="margin-top:8px;font-size:12px;">
                                    <div style="color:#6b7280;text-transform:uppercase;letter-spacing:.03em;margin-bottom:4px;">Video-Calls</div>
                                    @foreach($customerVideoCalls as $vc)
                                        @php
                                            $emp = optional($vc->creator)->employee;
                                            $creatorName = $emp ? trim(($emp->name ?? '').' '.($emp->lastname ?? '')) : (optional($vc->creator)->name ?? '');
                                            $statusLabel = ['created' => 'gestartet', 'active' => 'aktiv', 'ended' => 'beendet'][$vc->status] ?? $vc->status;
                                        @endphp
                                        <div style="display:flex;gap:10px;padding:2px 0;border-top:1px solid #f0f0f0;">
                                            <span>{{ optional($vc->created_at)->format('d.m.Y H:i') }}</span>
                                            <span>{{ $creatorName !== '' ? $creatorName : '—' }}</span>
                                            <span>{{ $statusLabel }}</span>
                                            <span>{{ $vc->durationHuman() ?: '—' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Feed shown only when profile is collapsed --}}
                <div class="cn-collapsed-feed">
                    <div class="customer-feed-strip customer-live-feed m-0" data-feed-root
                        data-customer-id="{{ $customer->id }}" data-feed-limit="10"
                        data-customer-title="{{ $customer->title }} {{ $customer->name }} {{ $customer->lastname }}">

                        <div class="cfs-icon">
                            <i class="feather icon-activity"></i>
                        </div>

                        <div class="cfs-main">
                            <div class="cfs-line" data-feed-line>
                                <div class="cfs-line-top">
                                    <span class="cfs-pill" data-feed-pill>Info</span>
                                    <span class="cfs-title" data-feed-title>Aktivität</span>
                                </div>

                                <div class="cfs-text" data-feed-text></div>

                                <div class="cfs-bottom">
                                    <span class="cfs-time" data-feed-time>–</span>
                                    <span class="cfs-counter" data-feed-counter></span>
                                </div>
                            </div>

                            <div class="cfs-empty" data-feed-empty>
                                <span class="cfs-empty-label">Keine Aktivitäten</span>
                                <span class="cfs-empty-sub">Noch keine Produkte, Termine oder Aufgaben.</span>
                            </div>

                            <div class="cfs-error text-danger small d-none" data-feed-error></div>
                        </div>

                        <div class="cfs-controls">
                            <button type="button" class="cfs-btn" title="Zurück" data-feed-prev>
                                <i class="feather icon-chevrons-left"></i>
                            </button>

                            <button type="button" class="cfs-btn" title="Pause / Abspielen" data-feed-toggle>
                                <i class="feather icon-pause" data-feed-icon-pause></i>
                                <i class="feather icon-play d-none" data-feed-icon-play></i>
                            </button>

                            <button type="button" class="cfs-btn" title="Weiter" data-feed-next>
                                <i class="feather icon-chevrons-right"></i>
                            </button>

                            <button type="button" class="cfs-btn cfs-btn-expand" title="Liste öffnen" data-feed-expand>
                                <i class="feather icon-maximize-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- EXPANDED VIEW (Your Full Original Content) --}}
            <div class="cn-expanded-content">
                <div class="customer-nav-inner">
                    <div class="row align-items-start gx-3 gy-3">

                        {{-- LEFT: Avatar, Name, Badges, Product Initials, Notes --}}
                        <div class="col-xl-4 col-lg-5 col-md-6">
                            <div class="d-flex gap-3">
                                <div class="cn-avatar">
                                    <span class="cn-avatar-initials">{{ $initials ?: 'K' }}</span>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="cn-welcome-label">Kundenprofil</div>

                                    <div class="cn-name-line d-flex align-items-center flex-wrap gap-1">
                                        <div class="cn-name">
                                            {{ $customer->title ?? '' }} {{ $customer->academic_title ?? '' }}
                                            {{ $customer->name }} {{ $customer->lastname }}
                                        </div>

                                        <span
                                            class="cn-review-summary {{ $reviewCritical > 0 ? 'cn-review-summary-critical' : '' }}"
                                            title="{{ $reviewTotal }} Bewertungen · Ø {{ number_format($reviewAvg, 1, ',', '.') }} Sterne">
                                            <span class="cn-review-summary-stars">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <i
                                                        class="feather icon-star {{ $s <= $reviewRounded ? 'is-filled' : 'is-empty' }}"></i>
                                                @endfor
                                            </span>

                                            <span class="cn-review-summary-score">
                                                {{ $reviewTotal > 0 ? number_format($reviewAvg, 1, ',', '.') : '0,0' }}
                                            </span>

                                            <span class="cn-review-summary-count">
                                                ({{ $reviewTotal }})
                                            </span>

                                            @if($reviewCritical > 0)
                                                <span class="cn-review-summary-count">
                                                    · {{ $reviewCritical }} kritisch
                                                </span>
                                            @endif
                                        </span>

                                        <button type="button" class="btn btn-xs cn-edit-btn customer-edit-trigger"
                                            data-customer-id="{{ $customer->id }}" title="Bearbeiten">
                                            <i class="feather icon-edit-2"></i>
                                        </button>

                                        <button type="button"
                                            class="btn btn-xs cn-edit-btn cn-contact-people-trigger ms-1"
                                            data-customer-id="{{ $customer->id }}" title="Kontaktpersonen">
                                            <i class="feather icon-users"></i>
                                            <span class="ms-1" id="contactPeopleCountBadge-{{ $customer->id }}">0</span>
                                        </button>
                                    </div>

                                    @if($customer->firma)
                                        <div class="cn-firma">{{ $customer->firma }}</div>
                                    @endif

                                    <div class="cn-meta-line">
                                        <span>ID #{{ $customer->id }}</span>
                                        <span class="cn-dot">•</span>
                                        <span>{{ $created->format('d.m.Y') }}</span>
                                    </div>

                                    <div class="cn-meta-pills">
                                        <span class="cn-pill">
                                            <i class="feather icon-tag cn-icon"></i>
                                            {{ $customer->source ?: 'unbekannt' }}
                                        </span>
                                        <span class="cn-pill">
                                            <i class="feather icon-award cn-icon"></i>
                                            {{ $tierLabel }}
                                        </span>
                                    </div>

                                    @if(isset($customer->leadProductLists) && $customer->leadProductLists->count() > 0)
                                        <div class="product-initials-row">
                                            @foreach($customer->leadProductLists as $lp)
                                                @php
        $product = $lp->product ?? null;

        $productName = $product->article_group
            ?? $product->product
            ?? 'Produkt';

        $rawInitial = $product->initial
            ?? ($productName ? mb_substr($productName, 0, 2) : '—');

        $pInitial = mb_strtoupper($rawInitial);

        $statusMap = [
            'open' => 'Offen',
            'lead' => 'Lead (Anfrage)',
            'offer' => 'Angebot',
            'project' => 'Projekt/Montage',
            'archive' => 'Archiv',
            'junk' => 'Junk',
            'feedback' => 'Feedback',
            'completed' => 'Abgeschlossen',
        ];

        $rawStatus = $lp->status ?? '';
        $germanStatus = $statusMap[$rawStatus] ?? ($rawStatus !== '' ? ucfirst($rawStatus) : 'Unbekannt');

        $tooltipText = $productName . ': ' . $germanStatus;
                                                @endphp

                                                <span class="product-mini-badge" data-toggle="tooltip" data-placement="top"
                                                    title="{{ $tooltipText }}">
                                                    {{ $pInitial }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="cn-notes-small" onclick="showFullNote(this)"
                                        data-note="{{ $customer->info ?? '' }}" title="Klicken für vollständige Notiz">
                                        <i class="feather icon-file-text me-1"></i>
                                        @if(!empty($customer->info))
                                            {{ Str::limit($customer->info, 80) }}
                                        @else
                                            <span class="text-muted font-italic">Keine Notizen hinterlegt...</span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- MIDDLE: Address & Contact (Stacked) --}}
                        <div class="col-xl-4 col-lg-4 col-md-6 border-start-md">
                            <div class="d-flex flex-column h-100 justify-content-center">

                                @php
$customerStreet = trim((string) ($customer->street ?? ''));
$customerHouseNo = trim((string) (
    $customer->street_number
    ?? $customer->house_number
    ?? $customer->housenumber
    ?? $customer->house_no
    ?? $customer->street_no
    ?? $customer->number
    ?? ''
));

$customerStreetLine = $customerStreet;

if ($customerHouseNo !== '' && $customerStreet !== '' && !preg_match('/(^|\s)' . preg_quote($customerHouseNo, '/') . '($|\s|,)/i', $customerStreet)) {
    $customerStreetLine = trim($customerStreet . ' ' . $customerHouseNo);
} elseif ($customerStreet === '') {
    $customerStreetLine = $customerHouseNo;
}
                                @endphp

                                <div class="cn-info-block">
                                    <div class="cn-info-icon"><i class="feather icon-map-pin"></i></div>
                                    <div class="cn-info-content">
                                        <div class="fw-bold text-muted text-uppercase"
                                            style="font-size:10px; letter-spacing:1px;">Adresse</div>
                                        <div>{{ $customerStreetLine ?: 'Adresse nicht verfügbar' }}</div>
                                        <div>{{ trim(($customer->postcode ?? '') . ' ' . ($customer->city ?? '')) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="cn-info-block">
                                    <div class="cn-info-icon"><i class="feather icon-phone"></i></div>
                                    <div class="cn-info-content">
                                        <div class="fw-bold text-muted text-uppercase"
                                            style="font-size:10px; letter-spacing:1px;">Kontakt</div>
                                        @if($customer->phone)
                                        <div>{{ $customer->phone }}</div> @endif
                                        @if($customer->telephone)
                                        <div>{{ $customer->telephone }}</div> @endif
                                        <div class="text-truncate" style="max-width: 220px;">{{ $customer->email }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- RIGHT: Compact Umsatz --}}
                        <div class="col-xl-4 col-lg-3 col-md-12">
                            <div class="inner-col inner-col-umsatz compact h-100 justify-content-center">

                                <div class="umsatz-main total-purchase-trigger" role="button" tabindex="0"
                                    data-customer-id="{{ $customer->id }}"
                                    data-total-purchase-raw="{{ (float) $customer->total_purchase }}"
                                    data-customer-name="{{ $customer->name }} {{ $customer->lastname }}">

                                    <div class="umsatz-label">
                                        <span class="umsatz-dot"></span>
                                        <span>Umsatz</span>
                                    </div>

                                    <div class="umsatz-total tp-display" id="customerTotalPurchase">
                                        {{ $totalPurchase }} €
                                    </div>

                                    <div class="umsatz-meta">
                                        <span class="umsatz-purchase-date" style="font-size:10px;">
                                            Letzter: <strong>{{ $purchaseDate }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-1">
                                    <button type="button"
                                        class="btn btn-pill-sm btn-pill-info btn-price-info w-100 justify-content-center"
                                        data-customer-id="{{ $customer->id }}"
                                        data-customer-name="{{ $customer->name }} {{ $customer->lastname }}">
                                        <i class="feather icon-activity"></i> Info
                                    </button>

                                    <button type="button"
                                        class="btn btn-pill-sm btn-pill-edit total-purchase-trigger w-100 justify-content-center"
                                        data-customer-id="{{ $customer->id }}"
                                        data-total-purchase-raw="{{ (float) $customer->total_purchase }}"
                                        title="Bearbeiten">
                                        <i class="feather icon-edit-2"></i> Edit
                                    </button>

                                    <button type="button"
                                        class="btn p-0 border-0 bg-transparent badge-trigger w-100 mt-1"
                                        data-customer-id="{{ $customer->id }}" @if(!$hasPurchase && !$tier) disabled
                                        @endif title="{{ $tierLabel }}">
                                        <div class="badge-icons justify-content-center"
                                            data-tier="{{ $tier ?: ($hasPurchase ? 'bronze' : '') }}">
                                            <img src="{{ asset('icons/bronze.png')}}" alt="Bronze"
                                                class="badge-icon badge-bronze">
                                            <img src="{{ asset('icons/silver.png')}}" alt="Silver"
                                                class="badge-icon badge-silver">
                                            <img src="{{ asset('icons/gold.png')}}" alt="Gold"
                                                class="badge-icon badge-gold">
                                            <img src="{{ asset('icons/platinum.png')}}" alt="Platinum"
                                                class="badge-icon badge-platinum">
                                        </div>
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- CUSTOMER FEED MODAL --}}
    <div class="modal fade feed-modal" id="customerFeedModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">

                <div class="feed-modal-header modal-header">
                    <div class="d-flex align-items-center">
                        <span class="feed-modal-title-icon mr-2">
                            <i class="feather icon-activity"></i>
                        </span>
                        <div>
                            <h5 class="modal-title mb-0" data-feed-modal-title>Aktivitäten</h5>
                            <div class="small text-black" data-feed-modal-subtitle></div>
                        </div>
                    </div>
                    <button type="button" class="close feed-modal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="feed-modal-body modal-body">
                    {{-- Toolbar: kind filter + search + sort --}}
                    <div class="feed-modal-toolbar mb-2">
                        <div class="btn-group btn-group-sm mb-2 mb-sm-0" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-feed-modal-kind="all">
                                Alle
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-feed-modal-kind="product">
                                Produkte
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-feed-modal-kind="appointment">
                                Termine
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-feed-modal-kind="task">
                                Aufgaben
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-feed-modal-kind="ticket">
                                Tickets
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-feed-modal-kind="history">
                                Historie
                            </button>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="feed-modal-search input-group input-group-sm mr-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="feather icon-search"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" placeholder="Suchen..." data-feed-modal-search>
                            </div>

                            <select class="form-control form-control-sm" data-feed-modal-sort>
                                <option value="desc">Neueste zuerst</option>
                                <option value="asc">Älteste zuerst</option>
                            </select>
                        </div>
                    </div>

                    {{-- List --}}
                    <div class="feed-modal-list" data-feed-modal-list></div>

                    <div class="feed-modal-empty d-none" data-feed-modal-empty>
                        Keine Einträge gefunden.
                    </div>

                    <div class="small text-muted mt-2" data-feed-modal-count></div>
                </div>

            </div>
        </div>
    </div>


    <button type="button" class="mobile-sidebar-open-btn" id="mobileSidebarOpenBtn" aria-label="Objektmenü öffnen">
        <i data-feather="menu"></i>
        <span>Objekte</span>
    </button>

    <div class="mobile-sidebar-backdrop" id="mobileSidebarBackdrop"></div>

    <div class="layout">
        <div class="customerSidebar" id="customerSidebar">
            <button class="minimize-btn" onclick="togglecustomerSidebar()">
                <i data-feather="chevrons-left"></i>
            </button>
            @php
$maFirstAlternative = $alternative ?? null;

if ($maFirstAlternative instanceof \Illuminate\Support\Collection) {
    $maFirstAlternative = $maFirstAlternative->first();
} elseif (is_array($maFirstAlternative)) {
    $maFirstAlternative = collect($maFirstAlternative)->first();
}

if (is_array($maFirstAlternative)) {
    $maFirstAlternativeId = $maFirstAlternative['id'] ?? '';
} elseif (is_object($maFirstAlternative)) {
    $maFirstAlternativeId = $maFirstAlternative->id ?? '';
} else {
    $maFirstAlternativeId = $maFirstAlternative ?: '';
}
            @endphp

            <button type="button" class="dashboard-btn" onclick="showDashboard(this)"
                data-customer-id="{{ request()->id }}" data-alternative-id="{{ $maFirstAlternativeId }}">
                <i data-feather="grid"></i>
                <span class="text">Dashboard</span>
            </button>

            <a href="{{ $leadHistoryUrl }}" class="dashboard-btn customer-history-side-btn" title="Alle Kundendaten und Historie öffnen">
                <i data-feather="clock"></i>
                <span class="text">Kundenhistorie</span>
            </a>

            @foreach ($alternative as $key => $object)
                                        <div class="object-section"
                                            data-object-section
                                            data-customer-id="{{ $customer->id ?? request()->id }}"
                                            data-alternative-id="{{ $object->id }}"
                                            data-edit-url="{{ url('/new_lead_edit/' . ($customer->id ?? request()->id) . '/' . $object->id) }}"
                                            data-delete-url="{{ url('/lead/objects/' . $object->id) }}">
                                            {{-- Object Header --}}
                                            <div class="object-header d-flex justify-content-between align-items-center"
                                                onclick="return maSafeObjectHeaderClick(event, 'object{{ $key }}')">
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="home" class="mr-2"></i>
                                                    <div class="d-flex flex-column" style="margin-left:-17px;">
                                                        <span class="text font-weight-bold">{{ $object->object_name ?? 'Object' }}</span>
                                                        @php
    $objectHeaderStreet = trim((string) ($object->street ?? ''));
    $objectHeaderHouseNo = trim((string) (
        $object->street_number
        ?? $object->house_number
        ?? $object->housenumber
        ?? $object->house_no
        ?? $object->street_no
        ?? $object->number
        ?? ''
    ));

    $objectHeaderStreetLine = $objectHeaderStreet;

    if ($objectHeaderHouseNo !== '' && $objectHeaderStreet !== '' && !preg_match('/(^|\s)' . preg_quote($objectHeaderHouseNo, '/') . '($|\s|,)/i', $objectHeaderStreet)) {
        $objectHeaderStreetLine = trim($objectHeaderStreet . ' ' . $objectHeaderHouseNo);
    } elseif ($objectHeaderStreet === '') {
        $objectHeaderStreetLine = $objectHeaderHouseNo;
    }
                                                        @endphp
                                                        <small class="text-muted">
                                                            {{ trim($objectHeaderStreetLine . ' ' . ($object->postcode ?? '') . ' ' . ($object->city ?? '')) }}
                                                        </small>
                                                    </div>
                                                </div>

                                                <!-- Picture placeholder -->
                                                @php
    $latestImage = $screenshots->where('alternative_id', $object->id)->last();

    $street = trim((string) ($object->street ?? ''));
    $houseNo = trim((string) (
        $object->street_number
        ?? $object->house_number
        ?? $object->housenumber
        ?? $object->house_no
        ?? $object->street_no
        ?? $object->number
        ?? ''
    ));
    $postcode = trim((string) ($object->postcode ?? ''));
    $city = trim((string) ($object->city ?? ''));

    $streetLine = $street;

    if ($houseNo !== '' && $street !== '' && !preg_match('/(^|\s)' . preg_quote($houseNo, '/') . '($|\s|,)/i', $street)) {
        $streetLine = trim($street . ' ' . $houseNo);
    } elseif ($street === '') {
        $streetLine = $houseNo;
    }

    $addressParts = array_filter([
        $streetLine,
        trim($postcode . ' ' . $city),
        'Germany',
    ]);

    $fullAddress = implode(', ', $addressParts);

    // Fallback to customer address if object address is empty
    if (trim(str_replace([',', 'Germany'], '', $fullAddress)) === '') {
        $customerStreet = trim((string) ($customer->street ?? ''));
        $customerHouseNo = trim((string) (
            $customer->street_number
            ?? $customer->house_number
            ?? $customer->housenumber
            ?? $customer->house_no
            ?? $customer->street_no
            ?? $customer->number
            ?? ''
        ));
        $customerPostcode = trim((string) ($customer->postcode ?? ''));
        $customerCity = trim((string) ($customer->city ?? ''));

        $customerStreetLine = $customerStreet;

        if ($customerHouseNo !== '' && $customerStreet !== '' && !preg_match('/(^|\s)' . preg_quote($customerHouseNo, '/') . '($|\s|,)/i', $customerStreet)) {
            $customerStreetLine = trim($customerStreet . ' ' . $customerHouseNo);
        } elseif ($customerStreet === '') {
            $customerStreetLine = $customerHouseNo;
        }

        $customerAddressParts = array_filter([
            $customerStreetLine,
            trim($customerPostcode . ' ' . $customerCity),
            'Germany',
        ]);

        $fullAddress = implode(', ', $customerAddressParts);
    }
    $objectLat = $object->lat ?? $object->latitude ?? null;
    $objectLng = $object->lng ?? $object->longitude ?? null;

    $hasCoords = is_numeric($objectLat) && is_numeric($objectLng);

    $streetViewLocation = $hasCoords
        ? trim($objectLat) . ',' . trim($objectLng)
        : $fullAddress;

    $googleMapsKey = config('services.google.maps_key');


    $streetViewFallbackUrl = 'https://maps.googleapis.com/maps/api/streetview?' . http_build_query([
        'size' => '420x260',
        'location' => $streetViewLocation,
        'fov' => 30,
        'heading' => 0,
        'pitch' => 0,
        'source' => 'outdoor',
        'key' => $googleMapsKey,
    ]);

    $satelliteFallbackUrl = 'https://maps.googleapis.com/maps/api/staticmap?' . http_build_query([
        'center' => $streetViewLocation,
        'zoom' => 21,
        'size' => '420x260',
        'scale' => 2,
        'maptype' => 'satellite',
        'key' => $googleMapsKey,
    ]) . '&markers=' . urlencode('color:red|' . $streetViewLocation);
                                                @endphp

                                                @if ($latestImage && !empty($latestImage->image))
                                                    @php
        $secureImageUrl = url('/secure-image/file/' . urlencode($latestImage->image)) . '?v=' . time();
                                                    @endphp

                                                    <a href="javascript:void(0);" class="object-thumb-link"
                                                        data-no-object-toggle="1" data-no-menu-toggle="1"
                                                        onclick="return window.openSidebarGallery(this, event);"
                                                        onmouseenter="window.maShowHoverPreview(event, '{{ $secureImageUrl }}')"
                                                        onmouseleave="window.maHideHoverPreview()" data-customer-id="{{ $customer->id }}"
                                                        data-alternative-id="{{ $object->id }}" data-address="{{ e($fullAddress) }}"
                                                        data-lat="{{ $objectLat ?? '' }}" data-lng="{{ $objectLng ?? '' }}">

                                                        <img src="{{ $secureImageUrl }}" alt="{{ $latestImage->image_name ?? 'Object Image' }}"
                                                            class="object-thumb-img"
                                                            style="width:100%; height:auto; object-fit:cover; cursor:pointer; border-radius:12px; pointer-events:none;">
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0);" class="object-thumb-link object-thumb-link-fallback"
                                                        data-no-object-toggle="1" data-no-menu-toggle="1"
                                                        onclick="return window.openSidebarGallery(this, event);"
                                                        onmouseenter="window.maShowHoverPreview(event, '{{ $streetViewFallbackUrl }}')"
                                                        onmouseleave="window.maHideHoverPreview()" data-customer-id="{{ $customer->id }}"
                                                        data-alternative-id="{{ $object->id }}" data-address="{{ e($fullAddress) }}"
                                                        data-lat="{{ $objectLat ?? '' }}" data-lng="{{ $objectLng ?? '' }}"
                                                        data-has-streetview-fallback="1" data-streetview-url="{{ $streetViewFallbackUrl }}"
                                                        data-satellite-url="{{ $satelliteFallbackUrl }}">

                                                        <img src="{{ $streetViewFallbackUrl }}" alt="Google Street View"
                                                            class="object-thumb-img object-thumb-streetview" loading="lazy"
                                                            onerror="this.onerror=null; this.src='{{ $satelliteFallbackUrl }}';"
                                                            style="width:100%; height:auto; object-fit:cover; cursor:pointer; border-radius:12px; pointer-events:none;">
                                                    </a>
                                                @endif
                                            </div>

                                                <div id="sidebarGallery{{ $object->id }}" class="sidebar-gallery" data-gallery-sidebar data-no-object-toggle="1"
                                                    data-no-menu-toggle="1" data-house-shot-root="{{ $object->id }}">

                                                    <div class="sidebar-header">
                                                        <div class="min-w-0">
                                                            <strong class="d-block">Haus-Screenshot</strong>
                                                            <small id="galleryAddress{{ $object->id }}" class="text-muted d-block text-truncate">
                                                                {{ $fullAddress }}
                                                            </small>
                                                        </div>

                                                        <button type="button" onclick="return closeSidebarGallery({{ $object->id }}, event)"
                                                            data-gallery-close="1"
                                                            class="btn btn-sm btn-outline-secondary ma-gallery-close">
                                                            &times;
                                                        </button>
                                                    </div>

                                                    {{-- UNIQUE TABS --}}
                                                    <div class="house-shot-tabs mb-3">
                                                        <button type="button" class="house-shot-tab-btn is-active" data-house-shot-tab-btn="saved">
                                                            <i class="fa fa-images mr-1"></i>
                                                            Aktuelle Screenshots
                                                        </button>

                                                        <button type="button" class="house-shot-tab-btn" data-house-shot-tab-btn="google">
                                                            <i class="fa fa-map-marker-alt mr-1"></i>
                                                            Google Map Screenshot
                                                        </button>
                                                    </div>

                                                    {{-- TAB 1: SAVED SCREENSHOTS + MANUAL UPLOAD --}}
                                                    <div class="house-shot-panel is-active" data-house-shot-panel="saved">

                                                        <div class="house-shot-upload-box mb-3">
                                                            <div class="mb-2">
                                                                <strong class="d-block">Eigenen Screenshot hochladen</strong>
                                                                <small class="text-muted">
                                                                    JPG, PNG oder WEBP. Wird automatisch als Screenshot gespeichert.
                                                                </small>
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1">Name</label>
                                                                <input type="text" id="houseShotManualName{{ $object->id }}"
                                                                    class="form-control form-control-sm" placeholder="z.B. Vorderansicht Haus">
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1">Screenshot-Datei</label>
                                                                <input type="file" id="houseShotManualFile{{ $object->id }}"
                                                                    class="form-control form-control-sm"
                                                                    accept="image/png,image/jpeg,image/jpg,image/webp">
                                                            </div>

                                                            <div id="houseShotPreviewWrap{{ $object->id }}" class="mb-2" style="display:none;">
                                                                <img id="houseShotPreviewImg{{ $object->id }}" src="" alt="Screenshot Vorschau"
                                                                    style="width:100%; max-height:260px; object-fit:cover; border-radius:12px; border:1px solid var(--ma-border);">
                                                            </div>

                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                                    onclick="event.preventDefault(); event.stopPropagation(); return houseShotClearManual({{ $object->id }}, event);">
                                                                    Zurücksetzen
                                                                </button>

                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    data-house-shot-manual-upload data-customer-id="{{ $customer->id }}" data-alternative-id="{{ $object->id }}"
                                                                    onclick="event.preventDefault(); event.stopPropagation(); return houseShotUploadManual('{{ $customer->id }}', '{{ $object->id }}', event);">
                                                                    Screenshot hochladen
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div>
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div class="font-weight-bold">Gespeicherte Screenshots</div>

                                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                                    onclick="event.preventDefault(); event.stopPropagation(); return loadSidebarGallery('{{ $customer->id }}', '{{ $object->id }}');">
                                                                    Aktualisieren
                                                                </button>
                                                            </div>

                                                            <div class="gallery-wrapper" id="galleryImages{{ $object->id }}">
                                                                <span class="text-muted">Bilder werden geladen...</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- TAB 2: GOOGLE MAP SCREENSHOT --}}
                                                    <div class="house-shot-panel" data-house-shot-panel="google" style="display:none;">

                                                        <div class="mb-3">
                                                            <label class="font-weight-bold mb-1">Ansichtsmodus</label>
                                                            <select id="screenshotMode{{ $object->id }}" class="form-control form-control-sm"
                                                                onchange="refreshHousePreview({{ $object->id }})">
                                                                <option value="satellite" selected>Satellit</option>
                                                                <option value="roadmap">Karte</option>
                                                                <option value="terrain">Gelände</option>
                                                                <option value="hybrid">Hybrid</option>
                                                                <option value="streetview">Street View</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="font-weight-bold mb-1">Zoom</label>
                                                            <select id="screenshotZoom{{ $object->id }}" class="form-control form-control-sm"
                                                                onchange="refreshHousePreview({{ $object->id }})">
                                                                <option value="18">18 - Straße</option>
                                                                <option value="19">19 - Haus</option>
                                                                <option value="20" selected>20 - Sehr nah</option>
                                                                <option value="21">21 - Maximal</option>
                                                            </select>
                                                        </div>

                                                        <div id="mapScreenshotWrapper{{ $object->id }}" class="mb-3 text-center">
                                                            <div id="mapContainer{{ $object->id }}" class="google-map border mx-auto"
                                                                style="width:100%; max-width:760px; height:420px; background:#f9f9f9; border-radius:12px; overflow:hidden; margin:0 auto;">
                                                                <div class="text-muted p-3">Karte wird geladen...</div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                                onclick="event.preventDefault(); event.stopPropagation(); return refreshHousePreview({{ $object->id }});">
                                                                Aktualisieren
                                                            </button>

                                                            <button type="button"
                                                                    class="btn btn-primary ma-house-shot-save-btn"
                                                                    data-house-shot-save
                                                                    data-customer-id="{{ $customer->id }}"
                                                                    data-alternative-id="{{ $object->id }}"
                                                                    onclick="event.preventDefault(); event.stopPropagation(); if (event.stopImmediatePropagation) event.stopImmediatePropagation(); window.triggerScreenshot('{{ $customer->id }}', '{{ $object->id }}', event); return false;">
                                                                Google Screenshot speichern
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                            {{-- Collapsible Product List --}}
                                            <div id="object{{ $key }}" class="product-list ma-product-list" style="padding: 0;">
                                                @foreach ($products->where('alternative_id', $object->id) as $i => $product)
                                                    @php
        $productId = "product{$key}_{$i}";

        $cid = $product->customer_id;
        $aid = $product->alternative_id;
        $pid = $product->product_id;
        $pl_id = $product->p_list_id ?? $product->p_id ?? null;
        $serviceId = $product->service_id;

        $serviceLabel = [
            'complete' => 'Komplettlösung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'maintenance' => 'Wartung',
            'repair' => 'Reparatur',
            'emergency' => 'Notdienst',
            'others' => 'Sonstiges',
        ][$product->phase_section ?? ''] ?? ucfirst($product->phase_section ?? 'Unbekannt');

        $statusLabel = [
            'lead' => 'Lead',
            'inquiry' => 'Anfrage',
            'deal' => 'Auftrag',
            'project' => 'Montage',
            'ticket' => 'Ticket',
            'pause' => 'Pausiert',
            'completed' => 'Abschluss',
            'junk' => 'Junk',
            'offer' => 'Angebot',
            'accept' => 'Offen',
        ][$product->status ?? ''] ?? ucfirst($product->status ?? 'Unbekannt');

        $kpiKey = $aid . '_' . $pid;

        $photosCount = $kpiDetailsMap[$kpiKey]['photos'] ?? 0;
        $docsCount = $kpiDetailsMap[$kpiKey]['documents'] ?? 0;
        $totalFiles = (int) $photosCount + (int) $docsCount;

        $sidebarCounts = $sidebarCounts ?? [];

        $countKey = $cid . '_' . $aid . '_' . $pid;

        $counts = $sidebarCounts[$countKey] ?? [
            'documents' => $totalFiles,
            'checklist' => 0,
            'tasks' => 0,
            'offers' => 0,
            'orders' => 0,
            'projects' => 0,
            'invoices' => 0,
            'customer_product' => 0,
            'appointments' => 0,
            'tickets' => 0,
            'reviews' => 0,
            'history' => 0,
            'stages' => 0,
        ];

        $counts['documents'] = $counts['documents'] ?? $totalFiles;

        $subNavItems = [
            [
                'label' => 'Bilder & Dokumente',
                'desc' => 'Fotos, Dateien und Unterlagen',
                'icon' => 'folder',
                'tone' => 'blue',
                'count_key' => 'documents',
                'count' => $counts['documents'] ?? 0,
                'onclick' => 'setActiveSubNav(this); loadDocuments(this)',
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                    'data-product-list-id' => $pl_id,
                ],
            ],
            
            [
                'label' => 'Aufgaben',
                'desc' => 'Interne Aufgaben zum Produkt',
                'icon' => 'clipboard',
                'tone' => 'orange',
                'count_key' => 'tasks',
                'count' => $counts['tasks'] ?? 0,
                'onclick' => 'setActiveSubNav(this); loadTask(this)',
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                    'data-product-list-id' => $pl_id,
                ],
            ],
            [
                'label' => 'Termin',
                'desc' => 'Termine und Planung',
                'icon' => 'calendar',
                'tone' => 'orange',
                'count_key' => 'appointments',
                'count' => $counts['appointments'] ?? 0,
                'onclick' => "setActiveSubNav(this); loadCalendar({$cid}, {$aid}, {$pid})",
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Angebote',
                'desc' => 'Angebotsdokumente',
                'icon' => 'file-text',
                'tone' => 'blue',
                'count_key' => 'offers',
                'count' => $counts['offers'] ?? 0,
                'onclick' => "setActiveSubNav(this); loadSectionPartial({$cid}, {$aid}, {$pid}, 'angebote')",
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Auftrag',
                'desc' => 'Auftragsdaten und Freigaben',
                'icon' => 'briefcase',
                'tone' => 'green',
                'count_key' => 'orders',
                'count' => $counts['orders'] ?? 0,
                'onclick' => "setActiveSubNav(this); loadSectionPartial({$cid}, {$aid}, {$pid}, 'auftraege')",
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Montage',
                'desc' => 'Projekt- und Montagebereich',
                'icon' => 'tool',
                'tone' => 'orange',
                'count_key' => 'projects',
                'count' => $counts['projects'] ?? 0,
                'onclick' => "setActiveSubNav(this); loadSectionPartial({$cid}, {$aid}, {$pid}, 'projekte')",
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Rechnungen',
                'desc' => 'Rechnungen und Zahlstatus',
                'icon' => 'credit-card',
                'tone' => 'green',

                'count_key' => 'invoices',
                'count' => $counts['invoices'] ?? 0,

                'amount_key' => 'invoice_total_amount',
                'amount_label' => 'Gesamt',
                'amount' => $counts['invoice_total_amount'] ?? 0,

                // First sync invoice total into lead_product_lists.price, then show Rechnung part
                'onclick' => "setActiveSubNav(this); syncInvoicePriceAndLoad(this, {$cid}, {$aid}, {$pid})",

                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Produkt',
                'desc' => 'Produktdaten und Seriennummern',
                'icon' => 'package',
                'tone' => 'blue',
                'count_key' => 'customer_product',
                'count' => $counts['customer_product'] ?? 0,
                'onclick' => 'setActiveSubNav(this); leadProduct(this)',
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],

            [
                'label' => 'Tickets',
                'desc' => 'Probleme, Service und Support',
                'icon' => 'life-buoy',
                'tone' => 'pink',
                'count_key' => 'tickets',
                'count' => $counts['tickets'] ?? 0,
                'onclick' => "setActiveSubNav(this); LoadCustomerTicket({$cid}, {$aid}, {$pid}, 'tickets')",
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Bewertungen',
                'desc' => 'Feedback und Bewertung',
                'icon' => 'star',
                'tone' => 'orange',

                'count_key' => 'reviews',
                'count' => $counts['reviews'] ?? 0,

                'onclick' => "setActiveSubNav(this); loadCustomerReviews({$cid}, {$aid}, {$pid})",

                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Historie',
                'desc' => 'Verlauf und Änderungen',
                'icon' => 'clock',
                'tone' => 'blue',
                'count_key' => 'history',
                'count' => $counts['history'] ?? 0,
                'onclick' => "setActiveSubNav(this); loadHistory({$cid}, {$aid}, {$pid})",
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                ],
            ],
            [
                'label' => 'Arbeitsprozess',
                'desc' => 'Phasen und Arbeitsschritte',
                'icon' => 'git-branch',
                'tone' => 'green',
                'count_key' => 'stages',
                'count' => $counts['stages'] ?? 0,
                'onclick' => "setActiveSubNav(this); loadStages({$cid}, {$aid}, {$pid}, {$serviceId})",
                'attrs' => [
                    'data-customer-id' => $cid,
                    'data-alternative-id' => $aid,
                    'data-product-id' => $pid,
                    'data-service-id' => $serviceId,
                ],
            ],
        ];
                                                    @endphp

                                                    {{-- Product Row --}}
                                                    <div class="project-link project-card ma-product-card" data-product-key="{{ $productId }}"
                                                        data-object-customer-id="{{ $cid }}" data-object-alternative-id="{{ $aid }}"
                                                        data-object-product="{{ $pid }}" data-pl-id="{{ $pl_id }}">

                                                        <div class="project-card-main">
                                                            <div class="project-card-title-row">
                                                                <span class="project-status-dot"></span>

                                                                <div class="project-card-title-block">
                                                                    <div class="project-card-title">
                                                                        {{ $product->article_group ?? 'Produkt' }}
                                                                    </div>

                                                                    <div class="project-card-meta">
                                                                        <span>{{ $product->department_name ?? 'Keine Abteilung' }}</span>
                                                                        <span class="meta-sep">•</span>
                                                                        <span>{{ $serviceLabel }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="project-footer-left">
                                                                    <span class="project-status-pill">
                                                                        {{ $statusLabel }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="project-card-footer">
                                                            <div class="project-footer-right">
                                                                <button type="button"
                                                                    class="project-metric project-metric--price price-badge price-edit-trigger"
                                                                    data-pl-id="{{ $pl_id }}" data-current-price="{{ (float) ($product->price ?? 0) }}"
                                                                    data-toggle="tooltip" title="Preis bearbeiten"
                                                                    onclick="event.preventDefault(); event.stopPropagation();">

                                                                    <i data-feather="tag"></i>

                                                                    <span class="metric-text">
                                                                        <span class="metric-value price-value">
                                                                            {{ number_format((float) ($product->price ?? 0), 2, ',', '.') }} €
                                                                        </span>
                                                                    </span>
                                                                </button>

                                                                <button type="button" class="project-metric project-metric--calendar"
                                                                    onclick="event.preventDefault(); event.stopPropagation(); return loadCalendar({{ $cid }}, {{ $aid }}, {{ $pid }});" data-toggle="tooltip"
                                                                    title="Termin ansehen">

                                                                    <i data-feather="calendar"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Sub Nav Section --}}
                                                    <div id="{{ $productId }}" class="sub-nav ma-sub-nav" style="display: none;">
                                                        @foreach($subNavItems as $item)
                                                            <button type="button" class="nav-section-btn ma-sub-nav-btn ma-sub-nav-btn-{{ $item['tone'] }}"
                                                                onclick="{{ $item['onclick'] }}" @foreach($item['attrs'] as $attr => $value)
                                                                @if(!is_null($value)) {{ $attr }}="{{ $value }}" @endif @endforeach>

                                                                <span class="ma-sub-nav-left">
                                                                    <span class="ma-sub-nav-icon">
                                                                        <i data-feather="{{ $item['icon'] }}"></i>
                                                                    </span>

                                                                    <span class="ma-sub-nav-text">
                                                                        <span class="ma-sub-nav-label">{{ $item['label'] }}</span>
                                                                        <span class="ma-sub-nav-desc">{{ $item['desc'] }}</span>

                                                                        @if(!empty($item['amount_key']))
                                                                            <span
                                                                                class="ma-sub-nav-amount-line {{ (float) ($item['amount'] ?? 0) == 0.0 ? 'is-zero' : '' }}"
                                                                                data-lead-sidebar-amount="{{ $item['amount_key'] }}"
                                                                                data-sidebar-amount-label="{{ $item['amount_label'] ?? 'Gesamt' }}"
                                                                                data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}"
                                                                                data-product-id="{{ $pid }}">
                                                                                {{ $item['amount_label'] ?? 'Gesamt' }}:
                                                                                {{ number_format((float) ($item['amount'] ?? 0), 2, ',', '.') }} €
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                </span>

                                                                <span class="ma-sub-nav-badges">
                                                                    <span class="sidebar-count-badge {{ (int) $item['count'] === 0 ? 'is-zero' : '' }}"
                                                                        data-lead-sidebar-count="{{ $item['count_key'] }}"
                                                                        data-count-value="{{ (int) $item['count'] }}" data-customer-id="{{ $cid }}"
                                                                        data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}">
                                                                        {{ (int) $item['count'] }}
                                                                    </span>
                                                                </span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
            @endforeach

        </div>
        <div class="contentStation position-relative p-0 pt-2 m-0">
            <button id="mainContentToggle" class="btn btn-icon btn-flat-primary waves-effect waves-light"
                title="Inhalt maximieren">
                <i class="feather icon-maximize-2"></i>
            </button>

            <button type="button" id="showRightPanelBtn" class="show-notes-inside-main d-none" title="Notizen anzeigen">
                <i class="feather icon-message-square"></i>
            </button>

            <div class="main-content">
                <div class="main" id="mainContent">
                    @include('admin.new_leads.layouts.dashboard')
                </div>
            </div>
        </div>
            {{-- 
|--------------------------------------------------------------------------
| Fixed Customer Notes / Context Feed Right Panel
|--------------------------------------------------------------------------
| Fixes:
| - Prevents "Property [id] does not exist on this collection instance."
| - Keeps the context-feed dropdown OUTSIDE #note-list
| - Adds safe data-* attributes for AJAX loader
| - Prevents duplicate dropdown rendering inside loaded partials
|--------------------------------------------------------------------------
--}}

@php
use Illuminate\Support\Collection;

/**
 * Safely extract an id from:
 * - model
 * - collection
 * - array
 * - scalar
 * - null
 */
$maContextId = function ($value, $fallback = '') {
    if ($value instanceof Collection) {
        $value = $value->first();
    }

    if (is_array($value)) {
        return $value['id'] ?? $fallback;
    }

    if (is_object($value)) {
        return $value->id ?? $fallback;
    }

    return $value ?: $fallback;
};

/**
 * Customer ID resolver
 */
$maCustomerId = $customer_id
    ?? $customerId
    ?? $maContextId($leads ?? null)
    ?? $maContextId($lead ?? null)
    ?? $id
    ?? request('customer_id')
    ?? request('lead_id')
    ?? '';

/**
 * Alternative/Object ID resolver.
 * IMPORTANT:
 * $alternative can be a model OR collection depending on your controller.
 */
$maAlternativeId = $alternative_id
    ?? $alternativeId
    ?? $maContextId($alternative ?? null)
    ?? $maContextId($alternatives ?? null)
    ?? request('alternative_id')
    ?? request('object_id')
    ?? '';

/**
 * Product ID resolver
 */
$maProductId = $product_id
    ?? $productId
    ?? $maContextId($product ?? null)
    ?? request('product_id')
    ?? '';

/**
 * Lead product list ID resolver
 */
$maLeadProductListId = $lead_product_list_id
    ?? $leadProductListId
    ?? $maContextId($leadProductList ?? null)
    ?? $maContextId($productListItem ?? null)
    ?? request('lead_product_list_id')
    ?? '';

$maNoteType = !empty($maProductId) ? 'product' : 'general';
@endphp

<div class="right-panel d-flex flex-column p-0" id="customerNotesRightPanel">

    {{-- Header --}}
    <div class="ma-notes-header">
        <div class="d-flex justify-content-between align-items-center px-1 py-1">
            <h4 class="mb-0 mr-1 ml-1" id="note_title">
                NOTIZEN
            </h4>

            <div class="search d-flex align-items-center" id="noteHeaderActions">
                <fieldset class="form-group position-relative mb-0 ma-note-search-wrap">
                    <input type="text" class="form-control" id="searchNote" placeholder="Suchen">
                    <div class="form-control-position">
                        <i class="feather icon-search"></i>
                    </div>
                </fieldset>

                <div class="btn-group ma-note-only-actions" role="group">
                    <button
                        type="button"
                        id="toggleNewNoteBtn"
                        onclick="toggleNewNoteArea()"
                        class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light"
                        title="Neue Notiz"
                    >
                        <i class="feather icon-plus"></i>
                    </button>

                    <button
                        type="button"
                        id="btnToggleRightPanelHidden"
                        class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light"
                        title="Notizen ausblenden"
                    >
                        <i class="feather icon-sidebar"></i>
                    </button>

                    <button
                        type="button"
                        id="noteDeletedModal"
                        onclick="loadAllDeletedNotes()"
                        class="btn btn-icon rounded-circle btn-outline-danger waves-effect waves-light"
                        title="Gelöschte Notizen anzeigen"
                    >
                        <i class="feather icon-trash-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Type switcher: only once, never inside #note-list --}}
    <div class="ma-note-type-switcher" id="maNoteTypeSwitcher">
        <button type="button" class="ma-note-type-current" data-note-feed-current>
            <span class="ma-note-type-icon bg-blue">
                <i data-feather="message-square"></i>
            </span>

            <span class="ma-note-type-text">
                <strong>Aktuelle Notizen</strong>
                <small>Kunden-, Objekt- und Produktnotizen</small>
            </span>

            <i data-feather="chevron-down" class="ma-note-type-chevron"></i>
        </button>

        <div class="ma-note-type-menu" data-note-feed-menu>
            <button
                type="button"
                class="ma-note-type-item active"
                data-feed-type="notes"
                data-label="Aktuelle Notizen"
                data-subtitle="Kunden-, Objekt- und Produktnotizen"
                data-icon="message-square"
                data-color="blue"
            >
                <span class="ma-note-type-icon bg-blue">
                    <i data-feather="message-square"></i>
                </span>
                <span>
                    <strong>Aktuelle Notizen</strong>
                    <small>Standard-Notizen</small>
                </span>
            </button>

            <button
                type="button"
                class="ma-note-type-item"
                data-feed-type="offers"
                data-label="Angebot"
                data-subtitle="Offer Folder, Angebote und Kommentare"
                data-icon="folder"
                data-color="orange"
            >
                <span class="ma-note-type-icon bg-orange">
                    <i data-feather="folder"></i>
                </span>
                <span>
                    <strong>Angebot</strong>
                    <small>Offer Folder, Angebote, Kommentare</small>
                </span>
            </button>

            <button
                type="button"
                class="ma-note-type-item"
                data-feed-type="tickets"
                data-label="Tickets"
                data-subtitle="Probleme, Kommentare und Aufgaben"
                data-icon="alert-triangle"
                data-color="pink"
            >
                <span class="ma-note-type-icon bg-pink">
                    <i data-feather="alert-triangle"></i>
                </span>
                <span>
                    <strong>Tickets</strong>
                    <small>Probleme, Kommentare, Aufgaben</small>
                </span>
            </button>

            <button
                type="button"
                class="ma-note-type-item"
                data-feed-type="appointments"
                data-label="Termine"
                data-subtitle="Kalender, Berichte und Kommentare"
                data-icon="calendar"
                data-color="green"
            >
                <span class="ma-note-type-icon bg-green">
                    <i data-feather="calendar"></i>
                </span>
                <span>
                    <strong>Termine</strong>
                    <small>Kalender, Berichte, Kommentare</small>
                </span>
            </button>

            <button
                type="button"
                class="ma-note-type-item"
                data-feed-type="tasks"
                data-label="Aufgaben"
                data-subtitle="Tasks, Schritte und Kommentare"
                data-icon="check-square"
                data-color="orange"
            >
                <span class="ma-note-type-icon bg-orange">
                    <i data-feather="check-square"></i>
                </span>
                <span>
                    <strong>Aufgaben</strong>
                    <small>Tasks, Schritte, Kommentare</small>
                </span>
            </button>

            <button
                type="button"
                class="ma-note-type-item"
                data-feed-type="deals"
                data-label="Auftrag"
                data-subtitle="Aufträge und Auftragsnotizen"
                data-icon="package"
                data-color="blue"
            >
                <span class="ma-note-type-icon bg-blue">
                    <i data-feather="package"></i>
                </span>
                <span>
                    <strong>Auftrag</strong>
                    <small>Aufträge und Auftragsnotizen</small>
                </span>
            </button>

            <button
                type="button"
                class="ma-note-type-item"
                data-feed-type="customer_reports"
                data-label="Kundenberichte"
                data-subtitle="Reports und Kommentare"
                data-icon="file-text"
                data-color="green"
            >
                <span class="ma-note-type-icon bg-green">
                    <i data-feather="file-text"></i>
                </span>
                <span>
                    <strong>Kundenberichte</strong>
                    <small>Reports und Kommentare</small>
                </span>
            </button>
        </div>
    </div>

    {{-- Optional new note area should stay outside note-list --}}
    <div id="newNoteArea" class="px-1 py-1" style="display:none;">
        {{-- Keep your existing new-note form here if you already have it somewhere else --}}
    </div>

    {{-- Render target only --}}
    <div id="note-scroll-wrapper" class="flex-grow-1 overflow-auto p-0 scroll-wrapper">
        <div
            id="note-list"
            class="scroll-wrapper"
            data-customer-id="{{ $maCustomerId }}"
            data-alternative-id="{{ $maAlternativeId }}"
            data-product-id="{{ $maProductId }}"
            data-generic-id="{{ $maProductId }}"
            data-unique-id="{{ $maLeadProductListId }}"
            data-note-type="{{ $maNoteType }}"
            data-feed-type="notes"
        >
            @isset($notes)
                @include('admin.new_leads.layouts.context-feed.note-list-body', [
        'notes' => $notes,
        'customer_id' => $maCustomerId,
        'alternative_id' => $maAlternativeId,
        'product_id' => $maProductId,
    ])
            @else
                <div class="ma-feed-empty">
                    <div class="d-flex align-items-center">
                        <span class="ma-note-type-icon bg-blue mr-2">
                            <i data-feather="message-square"></i>
                        </span>
                        <div>
                            <div class="ma-feed-title">Notizen</div>
                            <div class="ma-feed-meta">Bitte Objekt oder Produkt auswählen.</div>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </div>
</div>
    </div>
</div>


<!-- PRICE HISTORY DRAWER -->
<div id="priceHistoryBackdrop" class="ph-backdrop">
    <div class="ph-drawer">
        <div class="ph-header">
            <div>
                <div class="ph-title">Preisverlauf</div>
                <div class="ph-subtitle">
                    <span id="phCustomerName"></span>
                </div>
            </div>
            <button type="button" class="ph-close-btn" aria-label="Schließen">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <div class="ph-meta-strip">
            <div>
                Letzter Kauf:
                <span id="phPurchaseDate" class="ph-pill"></span>
            </div>
            <div>
                Gesamt:
                <span id="phTotalPurchase" class="ph-pill"></span>
            </div>
        </div>

        <div id="priceHistoryContent" class="ph-body">
            <!-- filled by JS -->
        </div>
    </div>
</div>

<div id="phaseSidebar" class="phase-sidebar">
    <div class="phase-sidebar-body" data-customer-id="" data-alternative-id="" data-product-id="" data-service-id="">

        <p>Lade...</p>
    </div>
</div>

<!-- Modal Purchase -->
<div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kaufübersicht</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="purchaseModalBody" class="modal-body">
                <div class="text-muted">Laden…</div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="noteDeletedModalWrapper" tabindex="-1" role="dialog" aria-labelledby="noteDeletedModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="noteDeletedModalLabel">Gelöschte Notizen</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="noteDeletedModalBody">
                <div class="text-muted">Lade gelöschte Notizen...</div>
            </div>
        </div>
    </div>
</div>


<div id="newNoteComposer" class="note-composer">
    <textarea id="newNoteText" class="form-control my-2" rows="3" placeholder="Write a new note..."></textarea>

    <!-- ✅ Hidden fields: dynamically filled from dataset -->
    <input type="hidden" id="noteType" name="type" value="">
    <input type="hidden" id="noteProductId" name="product_id" value="">

    <button onclick="submitNote()" class="btn btn-success float-end mb-2">
        <i class="feather icon-send me-1"></i> Send
    </button>

</div>
<div id="noteBackdrop" class="note-backdrop" onclick="toggleNewNoteArea()" style="display: none;"></div>

@include('admin.new_leads.layouts.taskModal')


<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        const mobileOpenBtn = document.getElementById('mobileSidebarOpenBtn');
        const mobileBackdrop = document.getElementById('mobileSidebarBackdrop');

        if (mobileOpenBtn) {
            mobileOpenBtn.addEventListener('click', function () {
                document.body.classList.add('mobile-sidebar-open');
            });
        }

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', function () {
                document.body.classList.remove('mobile-sidebar-open');
            });
        }
    });

    window.toggleCustomerNav = window.toggleCustomerNav || function () {
        const wrapper = document.getElementById('customerNavWrapper');

        if (!wrapper) return;

        wrapper.classList.toggle('is-collapsed');

        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    };

    window.togglecustomerSidebar = window.togglecustomerSidebar || function () {
        const sidebar = document.getElementById('customerSidebar');
        const layout = document.querySelector('.layout');

        if (window.innerWidth <= 991) {
            document.body.classList.toggle('mobile-sidebar-open');
            return;
        }

        if (!sidebar || !layout) return;

        sidebar.classList.toggle('minimized');
        layout.classList.toggle('sidebar-minimized');

        const icon = sidebar.querySelector('.minimize-btn i, .minimize-btn svg');

        if (icon && icon.setAttribute) {
            icon.setAttribute(
                'data-feather',
                sidebar.classList.contains('minimized') ? 'chevrons-right' : 'chevrons-left'
            );
        }

        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    };

    window.toggleObject = window.toggleObject || function (targetId) {
        const target = document.getElementById(targetId);

        if (!target) return;

        const isOpen = target.classList.contains('show');

        document.querySelectorAll('.product-list.show').forEach(function (list) {
            if (list !== target) {
                list.classList.remove('show');
                list.style.display = 'none';
            }
        });

        target.classList.toggle('show', !isOpen);
        target.style.display = isOpen ? 'none' : 'block';

        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    };

    window.setActiveSubNav = window.setActiveSubNav || function (button) {
        if (!button) return;

        document.querySelectorAll('.nav-section-btn.active, .ma-sub-nav-btn.active').forEach(function (btn) {
            btn.classList.remove('active');
        });

        button.classList.add('active');

        if (window.innerWidth <= 991) {
            document.body.classList.remove('mobile-sidebar-open');
        }
    };

    window.showFullNote = window.showFullNote || function (element) {
        const note = element?.dataset?.note || element?.innerText || '';

        if (window.Swal) {
            Swal.fire({
                title: 'Kundennotiz',
                html: `<div style="text-align:left; white-space:pre-wrap;">${escapeHtmlForProfile(note)}</div>`,
                confirmButtonText: 'Schließen'
            });
            return;
        }

        alert(note);
    };

    function escapeHtmlForProfile(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>

<script>
    (function () {
        'use strict';

        /*
        |--------------------------------------------------------------------------
        | GLOBAL HELPERS
        |--------------------------------------------------------------------------
        */

        window.maCsrfToken = window.maCsrfToken || function () {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        };

        window.maEscapeHtml = window.maEscapeHtml || function (value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        window.maShowMessage = window.maShowMessage || function (type, title, text) {
            if (window.Swal) {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: text,
                    timer: type === 'success' ? 1400 : undefined,
                    showConfirmButton: type !== 'success',
                });
                return;
            }

            alert(text || title);
        };

        window.maRefreshIcons = window.maRefreshIcons || function () {
            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        };

        window.maHasGoogleMaps = window.maHasGoogleMaps || function () {
            return typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined';
        };

        window.maCleanAddress = window.maCleanAddress || function (addr) {
            return String(addr || '').replace(/\s+/g, ' ').replace(/^,+|,+$/g, '').trim();
        };

        /*
        |--------------------------------------------------------------------------
        | GLOBAL STATE
        |--------------------------------------------------------------------------
        */

        window.MayarObjectMaps = window.MayarObjectMaps || {
            previews: {},
            sidebars: {},
            geocoder: null,
            observers: {},
        };

        window.googleMapsInstances = window.googleMapsInstances || {};

        window.HouseScreenshotGallery = {
            googleKey: "{{ config('services.google.maps_key') }}",
            routes: {
                loadBase: "{{ url('/load-images') }}",
                store: "{{ url('/save-screenshot') }}",
                upload: "{{ url('/upload-screenshot') }}",
                delete: "{{ url('/delete-screenshot') }}",
            },
            current: {
                customerId: null,
                alternativeId: null,
                address: '',
                center: null,
                imageUrl: '',
                mode: 'satellite',
                zoom: 20,
            },
            loadedImages: [],
        };

        /*
        |--------------------------------------------------------------------------
        | MAP HELPERS
        |--------------------------------------------------------------------------
        */

        function getDirectCoords(el) {
            const lat = parseFloat(el?.dataset?.lat || '');
            const lng = parseFloat(el?.dataset?.lng || '');

            return Number.isFinite(lat) && Number.isFinite(lng)
                ? { lat, lng }
                : null;
        }

        function geocodeAddress(address) {
            return new Promise(function (resolve, reject) {
                if (!window.maHasGoogleMaps()) {
                    reject(new Error('Google Maps API fehlt.'));
                    return;
                }

                const cleaned = window.maCleanAddress(address);

                if (!cleaned || cleaned.length < 5) {
                    reject(new Error('Adresse ungültig.'));
                    return;
                }

                window.MayarObjectMaps.geocoder = window.MayarObjectMaps.geocoder || new google.maps.Geocoder();

                window.MayarObjectMaps.geocoder.geocode(
                    {
                        address: cleaned,
                        region: 'DE',
                    },
                    function (results, status) {
                        if (status === 'OK' && results?.[0]) {
                            const loc = results[0].geometry.location;

                            resolve({
                                lat: loc.lat(),
                                lng: loc.lng(),
                            });

                            return;
                        }

                        reject(new Error('Geocoding fehlgeschlagen: ' + status));
                    }
                );
            });
        }

        async function resolveMapCenter(sourceEl, fallbackAddress) {
            const direct = getDirectCoords(sourceEl);

            if (direct) {
                return direct;
            }

            return await geocodeAddress(fallbackAddress || sourceEl?.dataset?.address);
        }

        function forceContainerSize(mapEl) {
            if (!mapEl) return;

            mapEl.style.width = '100%';
            mapEl.style.height = window.innerWidth <= 768 ? '65vh' : '520px';
            mapEl.style.minHeight = window.innerWidth <= 768 ? '65vh' : '520px';
            mapEl.style.display = 'block';
        }

        /*
        |--------------------------------------------------------------------------
        | MINI MAPS
        |--------------------------------------------------------------------------
        */

        window.initObjectMiniMap = async function (el) {
            if (!el || el.dataset.mapReady === '1' || !window.maHasGoogleMaps()) {
                return;
            }

            try {
                const center = await resolveMapCenter(el);

                el.innerHTML = '';
                el.dataset.mapReady = '1';

                const map = new google.maps.Map(el, {
                    center: center,
                    zoom: 20,
                    mapTypeId: 'satellite',
                    disableDefaultUI: true,
                    gestureHandling: 'none',
                    clickableIcons: false,
                    tilt: 0,
                });

                new google.maps.Marker({
                    position: center,
                    map: map,
                });

                window.MayarObjectMaps.previews[el.dataset.alternativeId] = map;

            } catch (err) {
                el.innerHTML = '<div class="ma-object-map-loading small text-muted text-center p-2">Karte nicht verfügbar</div>';
            }
        };

        window.initObjectMiniMaps = function () {
            if (!window.maHasGoogleMaps()) return;

            document.querySelectorAll('.ma-object-map-preview').forEach(function (el) {
                window.initObjectMiniMap(el);
            });
        };

        /*
        |--------------------------------------------------------------------------
        | SIDEBAR MAP
        |--------------------------------------------------------------------------
        */

        window.initSidebarObjectMap = async function (alternativeId, sourceEl = null) {
            if (!window.maHasGoogleMaps()) return;

            const mapEl = document.getElementById(`mapContainer${alternativeId}`);

            if (!mapEl) return;

            const state = window.HouseScreenshotGallery.current;
            const source = sourceEl || mapEl;

            forceContainerSize(mapEl);

            try {
                const center = await resolveMapCenter(source, state.address);

                state.center = center;
                mapEl.innerHTML = '';

                const map = new google.maps.Map(mapEl, {
                    center: center,
                    zoom: state.zoom,
                    mapTypeId: state.mode === 'streetview' ? 'satellite' : state.mode,
                    disableDefaultUI: false,
                    streetViewControl: true,
                    mapTypeControl: true,
                    fullscreenControl: true,
                    gestureHandling: 'greedy',
                    tilt: 0,
                });

                new google.maps.Marker({
                    position: center,
                    map: map,
                });

                window.MayarObjectMaps.sidebars[alternativeId] = map;
                window.googleMapsInstances[alternativeId] = map;

                window.applySidebarMapMode(alternativeId);

                const fixResize = function () {
                    if (mapEl.clientWidth > 0) {
                        google.maps.event.trigger(map, 'resize');
                        map.setCenter(state.center);
                    }
                };

                [50, 150, 350, 600, 1000].forEach(function (time) {
                    setTimeout(fixResize, time);
                });

                if ('ResizeObserver' in window) {
                    if (window.MayarObjectMaps.observers[alternativeId]) {
                        window.MayarObjectMaps.observers[alternativeId].disconnect();
                    }

                    const observer = new ResizeObserver(fixResize);
                    observer.observe(mapEl);

                    window.MayarObjectMaps.observers[alternativeId] = observer;
                }

            } catch (err) {
                mapEl.innerHTML = '<div class="ma-object-map-loading text-muted p-3 text-center small">Karte konnte nicht geladen werden.</div>';
            }
        };

        window.applySidebarMapMode = function (alternativeId) {
            const map = window.MayarObjectMaps.sidebars[alternativeId];

            if (!map) return;

            const state = window.HouseScreenshotGallery.current;
            const modeSelect = document.getElementById(`screenshotMode${alternativeId}`);
            const zoomSelect = document.getElementById(`screenshotZoom${alternativeId}`);

            if (modeSelect) {
                state.mode = modeSelect.value;
            }

            if (zoomSelect) {
                state.zoom = parseInt(zoomSelect.value || '20', 10);
            }

            forceContainerSize(document.getElementById(`mapContainer${alternativeId}`));

            map.setZoom(Number.isFinite(state.zoom) ? state.zoom : 20);

            if (state.mode === 'streetview') {
                const panorama = map.getStreetView();
                const position = state.center || map.getCenter();
                const streetViewService = new google.maps.StreetViewService();

                streetViewService.getPanorama(
                    {
                        location: position,
                        radius: 80,
                        source: google.maps.StreetViewSource.OUTDOOR,
                    },
                    function (data, status) {
                        if (status === google.maps.StreetViewStatus.OK && data?.location?.latLng) {
                            panorama.setPosition(data.location.latLng);
                            panorama.setPov({
                                heading: 0,
                                pitch: 0,
                                zoom: 2,
                            });
                            panorama.setVisible(true);
                        } else {
                            panorama.setVisible(false);
                            map.setMapTypeId('satellite');
                            map.setZoom(21);
                            map.setCenter(position);

                            window.maShowMessage(
                                'info',
                                'Street View nicht verfügbar',
                                'Für diese Adresse wurde keine Street-View-Ansicht gefunden. Satellitenansicht wird angezeigt.'
                            );
                        }
                    }
                );
            } else {
                map.getStreetView().setVisible(false);
                map.setMapTypeId(state.mode || 'satellite');
            }

            setTimeout(function () {
                google.maps.event.trigger(map, 'resize');
            }, 100);
        };

        window.refreshHousePreview = function (alternativeId) {
            window.applySidebarMapMode(alternativeId);
        };

        window.changeObjectSidebarMapMode = function (alternativeId, mode) {
            const select = document.getElementById(`screenshotMode${alternativeId}`);

            if (select && mode) {
                select.value = mode;
            }

            window.applySidebarMapMode(alternativeId);
        };

        /*
        |--------------------------------------------------------------------------
        | STATIC SCREENSHOT URL
        |--------------------------------------------------------------------------
        */

        window.buildHouseStaticScreenshotUrl = function (alternativeId) {
            const state = window.HouseScreenshotGallery.current || {};

            if (String(state.alternativeId || '') !== String(alternativeId || '')) {
                console.warn('Screenshot state mismatch:', {
                    stateAlternativeId: state.alternativeId,
                    clickedAlternativeId: alternativeId,
                });

                return '';
            }

            const key = String(window.HouseScreenshotGallery.googleKey || '').trim();

            if (!key) {
                console.error('Google Maps key is missing.');
                return '';
            }

            const modeSelect = document.getElementById(`screenshotMode${alternativeId}`);
            const zoomSelect = document.getElementById(`screenshotZoom${alternativeId}`);

            const mode = modeSelect?.value || state.mode || 'satellite';
            const zoom = parseInt(zoomSelect?.value || state.zoom || '20', 10);

            let location = '';

            if (
                state.center &&
                Number.isFinite(parseFloat(state.center.lat)) &&
                Number.isFinite(parseFloat(state.center.lng))
            ) {
                location = `${parseFloat(state.center.lat)},${parseFloat(state.center.lng)}`;
            } else {
                location = window.maCleanAddress(state.address || '');
            }

            if (!location || location.length < 3) {
                console.error('Screenshot location is missing.', state);
                return '';
            }

            if (mode === 'streetview') {
                const params = new URLSearchParams({
                    size: '1200x720',
                    location: location,
                    fov: '35',
                    heading: '0',
                    pitch: '0',
                    source: 'outdoor',
                    key: key,
                });

                return `https://maps.googleapis.com/maps/api/streetview?${params.toString()}`;
            }

            const mapType = ['satellite', 'hybrid', 'roadmap', 'terrain'].includes(mode)
                ? mode
                : 'satellite';

            const params = new URLSearchParams({
                center: location,
                zoom: String(Number.isFinite(zoom) ? zoom : 20),
                size: '1200x720',
                scale: '2',
                maptype: mapType,
                key: key,
            });

            return `https://maps.googleapis.com/maps/api/staticmap?${params.toString()}&markers=${encodeURIComponent('color:red|' + location)}`;
        };

        /*
        |--------------------------------------------------------------------------
        | SIDEBAR OPEN / CLOSE
        |--------------------------------------------------------------------------
        */

        window.openObjectMapSidebar = function (el) {
            if (!el) return;

            const alternativeId = el.dataset.alternativeId || '';
            const customerId = el.dataset.customerId || '';
            const address = window.maCleanAddress(el.dataset.address || '');

            if (!alternativeId) {
                window.maShowMessage('error', 'Fehler', 'Alternative-ID fehlt.');
                return;
            }

            const directCenter = getDirectCoords(el);

            window.HouseScreenshotGallery.current = {
                customerId: customerId,
                alternativeId: alternativeId,
                address: address,
                center: directCenter,
                imageUrl: '',
                mode: document.getElementById(`screenshotMode${alternativeId}`)?.value || 'satellite',
                zoom: parseInt(document.getElementById(`screenshotZoom${alternativeId}`)?.value || '20', 10),
            };

            document.querySelectorAll('.sidebar-gallery.active').forEach(function (item) {
                item.classList.remove('active');
            });

            const sidebar = document.getElementById(`sidebarGallery${alternativeId}`);

            if (sidebar) {
                sidebar.classList.add('active');
            }

            const addrLabel = document.getElementById(`galleryAddress${alternativeId}`);

            if (addrLabel) {
                addrLabel.textContent = address || 'Adresse nicht verfügbar';
            }

            if (typeof window.loadSidebarGallery === 'function') {
                window.loadSidebarGallery(customerId, alternativeId);
                window.houseShotUpdateObjectCardThumb(alternativeId);
            }

            setTimeout(function () {
                window.initSidebarObjectMap(alternativeId, el);
            }, 150);

            window.maRefreshIcons();
        };

        window.openSidebarGallery = window.openObjectMapSidebar;

        window.closeSidebarGallery = function (alternativeId) {
            document.getElementById(`sidebarGallery${alternativeId}`)?.classList.remove('active');

            if (window.MayarObjectMaps.observers[alternativeId]) {
                window.MayarObjectMaps.observers[alternativeId].disconnect();
                delete window.MayarObjectMaps.observers[alternativeId];
            }
        };

        /*
        |--------------------------------------------------------------------------
        | UNIQUE HOUSE-SHOT TABS
        |--------------------------------------------------------------------------
        */

        window.houseShotSwitchTab = function (objectId, tabName) {
            const root = document.querySelector(`[data-house-shot-root="${objectId}"]`);

            if (!root) return;

            root.querySelectorAll('[data-house-shot-tab-btn]').forEach(function (btn) {
                btn.classList.remove('is-active');
            });

            root.querySelectorAll('[data-house-shot-panel]').forEach(function (panel) {
                panel.classList.remove('is-active');
                panel.style.display = 'none';
            });

            const activeBtn = root.querySelector(`[data-house-shot-tab-btn="${tabName}"]`);
            const activePanel = root.querySelector(`[data-house-shot-panel="${tabName}"]`);

            if (activeBtn) {
                activeBtn.classList.add('is-active');
            }

            if (activePanel) {
                activePanel.classList.add('is-active');
                activePanel.style.display = 'block';
            }

            if (tabName === 'google' && typeof window.refreshHousePreview === 'function') {
                setTimeout(function () {
                    window.refreshHousePreview(objectId);
                }, 200);
            }
        };

        /*
        |--------------------------------------------------------------------------
        | SECURE IMAGE URL
        |--------------------------------------------------------------------------
        */

        window.buildSecureImageUrl = function (img) {
            if (!img) {
                return '/images/icons/placeholder.svg';
            }

            if (img.url) {
                return img.url;
            }

            if (img.id) {
                return `/secure-image/id/${encodeURIComponent(img.id)}`;
            }

            if (img.image) {
                return `/secure-image/file/${encodeURIComponent(img.image)}`;
            }

            return '/images/icons/placeholder.svg';
        };

        /*
        |--------------------------------------------------------------------------
        | HOVER PREVIEW
        |--------------------------------------------------------------------------
        */

        window.maShowHoverPreview = function (srcEvent, imgUrl) {
            const overlay = document.getElementById('maHoverPreviewOverlay');
            const img = document.getElementById('maHoverPreviewImg');

            if (!overlay || !img || !imgUrl) return;

            img.src = imgUrl;
            overlay.style.display = 'block';

            const updatePos = function (e) {
                const cursorX = e.clientX;
                const cursorY = e.clientY;
                const gap = 16;

                let x = cursorX + gap;
                let y = cursorY + gap;

                if (x + overlay.offsetWidth > window.innerWidth) {
                    x = cursorX - overlay.offsetWidth - gap;
                }

                if (y + overlay.offsetHeight > window.innerHeight) {
                    y = window.innerHeight - overlay.offsetHeight - gap;
                }

                overlay.style.left = `${Math.max(10, x)}px`;
                overlay.style.top = `${Math.max(10, y)}px`;
            };

            updatePos(srcEvent);

            window._maHoverPosListener = updatePos;
            window.addEventListener('mousemove', updatePos);
        };

        window.maHideHoverPreview = function () {
            const overlay = document.getElementById('maHoverPreviewOverlay');

            if (overlay) {
                overlay.style.display = 'none';
            }

            if (window._maHoverPosListener) {
                window.removeEventListener('mousemove', window._maHoverPosListener);
                delete window._maHoverPosListener;
            }
        };

        /*
        |--------------------------------------------------------------------------
        | LIGHTBOX
        |--------------------------------------------------------------------------
        */

        window.maActiveLightboxIndex = 0;

        window.maOpenLightbox = function (targetIndex) {
            const overlay = document.getElementById('maLightboxOverlay');
            const img = document.getElementById('maLightboxImg');
            const caption = document.getElementById('maLightboxCaption');
            const prevBtn = document.getElementById('maLightboxPrevBtn');
            const nextBtn = document.getElementById('maLightboxNextBtn');
            const cache = window.HouseScreenshotGallery.loadedImages || [];

            if (!overlay || !img || cache.length === 0) return;

            window.maActiveLightboxIndex = targetIndex;

            const currentItem = cache[window.maActiveLightboxIndex];

            img.src = currentItem.url;

            if (caption) {
                caption.innerText = currentItem.caption || '';
            }

            if (prevBtn) {
                prevBtn.style.display = cache.length > 1 ? 'flex' : 'none';
            }

            if (nextBtn) {
                nextBtn.style.display = cache.length > 1 ? 'flex' : 'none';
            }

            overlay.style.display = 'flex';
        };

        window.maCloseLightbox = function () {
            const overlay = document.getElementById('maLightboxOverlay');

            if (overlay) {
                overlay.style.display = 'none';
            }
        };

        window.maLightboxNavigate = function (direction) {
            const cache = window.HouseScreenshotGallery.loadedImages || [];

            if (cache.length <= 1) return;

            window.maActiveLightboxIndex = (window.maActiveLightboxIndex + direction + cache.length) % cache.length;
            window.maOpenLightbox(window.maActiveLightboxIndex);
        };

        /*
        |--------------------------------------------------------------------------
        | LOAD SCREENSHOTS
        |--------------------------------------------------------------------------
        */

        window.loadSidebarGallery = function (customerId, alternativeId) {
            const wrapper = document.getElementById(`galleryImages${alternativeId}`);

            if (!wrapper) return;

            wrapper.innerHTML = '<span class="text-muted small">Bilder werden geladen...</span>';

            fetch(`${window.HouseScreenshotGallery.routes.loadBase}/${encodeURIComponent(alternativeId)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('Ladefehler.');
                    }

                    return res.json();
                })
                .then(function (data) {
                    const images = Array.isArray(data) ? data : (data.images || []);

                    window.HouseScreenshotGallery.loadedImages = images.map(function (img) {
                        return {
                            id: img.id || '',
                            url: window.buildSecureImageUrl(img),
                            caption: img.created_at || img.image_name || 'Screenshot',
                        };
                    });

                    if (!images.length) {
                        wrapper.innerHTML = '<span class="text-muted small">Noch keine Screenshots gespeichert.</span>';
                        return;
                    }

                    wrapper.innerHTML = window.HouseScreenshotGallery.loadedImages.map(function (item, index) {
                        const safeUrl = window.maEscapeHtml(item.url);
                        const safeCaption = window.maEscapeHtml(item.caption);

                        return `
                        <div class="gallery-thumb">
                            <a href="javascript:void(0);"
                               onclick="window.maOpenLightbox(${index})"
                               onmouseenter="window.maShowHoverPreview(event, '${safeUrl}')"
                               onmouseleave="window.maHideHoverPreview()"
                               style="display:block;">
                                <img src="${safeUrl}"
                                     alt="Screenshot"
                                     style="width:100%; height:220px; min-height:220px; object-fit:cover; border-radius:18px; pointer-events:none;">
                            </a>

                            <div class="d-flex justify-content-between align-items-center mt-1" style="gap:.35rem;">
                                <small class="text-muted text-truncate" style="min-width:0;">${safeCaption}</small>

                                ${item.id ? `
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger p-1"
                                            style="line-height:0.8;"
                                            title="Löschen"
                                            onclick="event.preventDefault(); event.stopPropagation(); window.maHideHoverPreview(); window.deleteSidebarScreenshot('${item.id}', '${alternativeId}');">
                                        &times;
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    }).join('');

                    window.maRefreshIcons();
                })
                .catch(function () {
                    wrapper.innerHTML = '<span class="text-danger small">Ladefehler.</span>';
                });
        };

        /*
        |--------------------------------------------------------------------------
        | SAVE GOOGLE SCREENSHOT
        |--------------------------------------------------------------------------
        */

        window.fetchHouseShotBlob = async function (url) {
            if (!url) {
                throw new Error('Screenshot-URL konnte nicht erstellt werden.');
            }

            const response = await fetch(url, {
                method: 'GET',
                mode: 'cors',
                cache: 'no-store',
            });

            if (!response.ok) {
                throw new Error(`Google Screenshot konnte nicht geladen werden (${response.status}).`);
            }

            const blob = await response.blob();

            if (!blob || !String(blob.type || '').startsWith('image/')) {
                throw new Error('Google hat kein Bild zurückgegeben.');
            }

            return blob;
        };

        window.uploadHouseShotBlob = async function ({ customerId, alternativeId, blob, fileName, imageName, mode, zoom, lat, lng, address }) {
            const file = new File([blob], fileName, { type: blob.type || 'image/png' });
            const formData = new FormData();

            formData.append('file', file);
            formData.append('customer_id', customerId);
            formData.append('alternative_id', alternativeId);
            formData.append('stage', 'screenshot');
            formData.append('status', 'screenshot');
            formData.append('image_name', imageName || 'Google Map Screenshot');
            formData.append('mode', mode || 'satellite');
            formData.append('zoom', String(zoom || 20));
            formData.append('lat', String(lat || ''));
            formData.append('lng', String(lng || ''));
            formData.append('address', address || '');

            const response = await fetch(window.HouseScreenshotGallery.routes.upload, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.maCsrfToken(),
                },
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Screenshot-Upload fehlgeschlagen.');
            }

            return data;
        };

        window.triggerScreenshot = async function (customerId, alternativeId, eventObject = null) {
            
            if (eventObject) {
                eventObject.preventDefault();
                eventObject.stopPropagation();
                if (typeof eventObject.stopImmediatePropagation === 'function') {
                    eventObject.stopImmediatePropagation();
                }
            }
const state = window.HouseScreenshotGallery.current;

            if (!state || String(state.alternativeId) !== String(alternativeId)) {
                window.maShowMessage('error', 'Fehler', 'Menü erneut öffnen.');
                return;
            }

            const button = eventObject?.target?.closest('button') || null;
            const oldHtml = button ? button.innerHTML : '';

            try {
                if (button) {
                    button.disabled = true;
                    button.innerHTML = 'Screenshot wird erstellt...';
                }

                if (!state.center && state.address) {
                    state.center = await geocodeAddress(state.address);
                }

                const modeSelect = document.getElementById(`screenshotMode${alternativeId}`);
                const zoomSelect = document.getElementById(`screenshotZoom${alternativeId}`);

                const originalMode = modeSelect?.value || state.mode || 'satellite';
                const zoom = parseInt(zoomSelect?.value || state.zoom || '20', 10);
                const lat = parseFloat(state.center?.lat || '');
                const lng = parseFloat(state.center?.lng || '');

                if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                    throw new Error('Koordinaten fehlen. Bitte Objekt-Adresse prüfen.');
                }

                state.mode = originalMode;
                state.zoom = Number.isFinite(zoom) ? zoom : 20;

                let screenshotUrl = window.buildHouseStaticScreenshotUrl(alternativeId);
                let usedMode = originalMode;
                let blob = null;

                try {
                    blob = await window.fetchHouseShotBlob(screenshotUrl);
                } catch (firstError) {
                    if (originalMode !== 'streetview') {
                        throw firstError;
                    }

                    console.warn('Street View Screenshot failed. Falling back to satellite.', firstError);
                    usedMode = 'satellite';
                    state.mode = 'satellite';
                    if (modeSelect) modeSelect.value = 'satellite';
                    screenshotUrl = window.buildHouseStaticScreenshotUrl(alternativeId);
                    blob = await window.fetchHouseShotBlob(screenshotUrl);
                }

                const extension = String(blob.type || '').includes('jpeg') ? 'jpg' : 'png';
                const data = await window.uploadHouseShotBlob({
                    customerId,
                    alternativeId,
                    blob,
                    fileName: `google-map-${alternativeId}-${Date.now()}.${extension}`,
                    imageName: `Google ${usedMode === 'streetview' ? 'Street View' : 'Map'} Screenshot`,
                    mode: usedMode,
                    zoom: state.zoom,
                    lat,
                    lng,
                    address: state.address || '',
                });

                await window.loadSidebarGallery(customerId, alternativeId);

                if (typeof window.houseShotUpdateObjectCardThumb === 'function') {
                    await window.houseShotUpdateObjectCardThumb(alternativeId, data.image || null);
                }

                window.maShowMessage('success', 'Gespeichert', 'Google Screenshot wurde als echtes Bild gespeichert.');
            } catch (err) {
                console.error('Screenshot Save Error:', err);
                window.maShowMessage('error', 'Fehler', err.message || 'Screenshot konnte nicht gespeichert werden.');
            } finally {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = oldHtml;
                }
            }
        };
        /*
        |--------------------------------------------------------------------------
        | MANUAL SCREENSHOT UPLOAD
        |--------------------------------------------------------------------------
        */

        window.houseShotClearManual = function (objectId) {
            const fileInput = document.getElementById(`houseShotManualFile${objectId}`);
            const nameInput = document.getElementById(`houseShotManualName${objectId}`);
            const wrap = document.getElementById(`houseShotPreviewWrap${objectId}`);
            const img = document.getElementById(`houseShotPreviewImg${objectId}`);

            if (fileInput) fileInput.value = '';
            if (nameInput) nameInput.value = '';
            if (img) img.src = '';
            if (wrap) wrap.style.display = 'none';
        };

        window.houseShotUploadManual = async function (customerId, objectId, eventObject = null) {
            const fileInput = document.getElementById(`houseShotManualFile${objectId}`);
            const nameInput = document.getElementById(`houseShotManualName${objectId}`);

            if (!fileInput || !fileInput.files || !fileInput.files.length) {
                window.maShowMessage('error', 'Fehler', 'Bitte zuerst einen Screenshot auswählen.');
                return;
            }

            const file = fileInput.files[0];

            if (!file.type.match(/^image\/(jpeg|jpg|png|webp)$/)) {
                window.maShowMessage('error', 'Fehler', 'Nur JPG, PNG oder WEBP erlaubt.');
                return;
            }

            const button = eventObject?.target?.closest('button') || null;
            const oldHtml = button ? button.innerHTML : '';

            try {
                if (button) {
                    button.disabled = true;
                    button.innerHTML = 'Wird hochgeladen...';
                }

                const formData = new FormData();
                formData.append('file', file);
                formData.append('customer_id', customerId);
                formData.append('alternative_id', objectId);
                formData.append('stage', 'screenshot');
                formData.append('status', 'screenshot');
                formData.append('image_name', nameInput?.value || 'Haus Screenshot');

                const response = await fetch(window.HouseScreenshotGallery.routes.upload, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.maCsrfToken(),
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Upload fehlgeschlagen.');
                }

                window.houseShotClearManual(objectId);
                window.loadSidebarGallery(customerId, objectId);
                window.houseShotUpdateObjectCardThumb(objectId);
                window.maShowMessage('success', 'Gespeichert', 'Screenshot wurde erfolgreich hochgeladen.');

            } catch (error) {
                window.maShowMessage('error', 'Fehler', error.message);
            } finally {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = oldHtml;
                }
            }
        };

        /*
        |--------------------------------------------------------------------------
        | DELETE SCREENSHOT
        |--------------------------------------------------------------------------
        */

        window.deleteSidebarScreenshot = function (id, alternativeId) {
            if (!confirm('Wirklich unwiderruflich löschen?')) {
                return;
            }

            fetch(window.HouseScreenshotGallery.routes.delete, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.maCsrfToken(),
                },
                body: JSON.stringify({ id: id }),
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    if (!data.success) {
                        throw new Error(data.message || 'Löschen fehlgeschlagen.');
                    }

                    window.loadSidebarGallery(window.HouseScreenshotGallery.current?.customerId, alternativeId);
                    window.houseShotUpdateObjectCardThumb(alternativeId);
                    window.maShowMessage('success', 'Gelöscht', 'Erfolgreich entfernt.');
                })
                .catch(function (err) {
                    window.maShowMessage('error', 'Fehler', err.message);
                });
        };

        /*
        |--------------------------------------------------------------------------
        | EVENTS
        |--------------------------------------------------------------------------
        */

        document.addEventListener('click', function (event) {
            const tabBtn = event.target.closest('[data-house-shot-tab-btn]');

            if (!tabBtn) return;

            const root = tabBtn.closest('[data-house-shot-root]');

            if (!root) return;

            const objectId = root.getAttribute('data-house-shot-root');
            const tabName = tabBtn.getAttribute('data-house-shot-tab-btn');

            if (objectId && tabName) {
                window.houseShotSwitchTab(objectId, tabName);
            }
        });

        document.addEventListener('change', function (event) {
            const input = event.target;

            if (!input.id || !input.id.startsWith('houseShotManualFile')) {
                return;
            }

            const objectId = input.id.replace('houseShotManualFile', '');
            const wrap = document.getElementById(`houseShotPreviewWrap${objectId}`);
            const img = document.getElementById(`houseShotPreviewImg${objectId}`);

            if (!wrap || !img || !input.files || !input.files.length) {
                return;
            }

            const file = input.files[0];

            if (!file.type.match(/^image\/(jpeg|jpg|png|webp)$/)) {
                input.value = '';
                wrap.style.display = 'none';
                img.src = '';

                window.maShowMessage('error', 'Fehler', 'Nur JPG, PNG oder WEBP erlaubt.');
                return;
            }

            img.src = URL.createObjectURL(file);
            wrap.style.display = 'block';
        });

        document.addEventListener('DOMContentLoaded', function () {
            let tries = 0;

            const timer = setInterval(function () {
                tries++;

                if (window.maHasGoogleMaps()) {
                    clearInterval(timer);
                    window.initObjectMiniMaps();
                }

                if (tries > 40) {
                    clearInterval(timer);
                }
            }, 250);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    window.maCloseLightbox();
                    window.maHideHoverPreview();

                    document.querySelectorAll('.sidebar-gallery.active').forEach(function (sidebar) {
                        sidebar.classList.remove('active');
                    });
                }

                if (event.key === 'ArrowLeft') {
                    if (document.getElementById('maLightboxOverlay')?.style.display === 'flex') {
                        window.maLightboxNavigate(-1);
                    }
                }

                if (event.key === 'ArrowRight') {
                    if (document.getElementById('maLightboxOverlay')?.style.display === 'flex') {
                        window.maLightboxNavigate(1);
                    }
                }
            });
        });

    })();
</script>

<script data-customer-review-global="1">
    (function () {
        "use strict";

        window.CustomerReviewAjax = window.CustomerReviewAjax || {
            routes: {
                index: @json(\Illuminate\Support\Facades\Route::has('customer-reviews.index') ? route('customer-reviews.index') : url('/customer-reviews')),
                store: @json(\Illuminate\Support\Facades\Route::has('customer-reviews.store') ? route('customer-reviews.store') : url('/customer-reviews')),
                base: @json(url('/customer-reviews')),
            },
            current: {
                customerId: null,
                alternativeId: null,
                productId: null
            }
        };

        Object.assign(window.CustomerReviewAjax, {
            normalizeId: function (val) {
                return val === undefined || val === null || val === '' || val === 'null' || val === 'undefined'
                    ? ''
                    : String(val);
            },

            loadingHtml: function () {
                return `
                <div class="p-4 text-center">
                    <div class="d-inline-flex align-items-center gap-2 text-muted fw-bold">
                        <span class="spinner-border spinner-border-sm"></span>
                        Bewertungen werden geladen...
                    </div>
                </div>
            `;
            },

            errorHtml: function (msg) {
                return `
                <div class="m-3 p-3 rounded text-danger fw-bold"
                     style="border:1px solid rgba(229,6,86,.25); background:rgba(229,6,86,.08);">
                    ${window.maEscapeHtml(msg || 'Ladefehler.')}
                </div>
            `;
            },

            parseError: function (err) {
                return typeof err === 'string' ? err : (err?.message || 'Serverfehler.');
            },

            executeScripts: function (container) {
                if (!container) return;

                container.querySelectorAll('script').forEach(function (oldScript) {
                    if (oldScript.dataset.customerReviewGlobal === '1') return;

                    const newScript = document.createElement('script');

                    Array.from(oldScript.attributes).forEach(function (attr) {
                        newScript.setAttribute(attr.name, attr.value);
                    });

                    newScript.textContent = oldScript.textContent;
                    oldScript.replaceWith(newScript);
                });
            },

            load: async function (customerId, alternativeId, productId) {
                const container = document.getElementById('mainContent');

                if (!container) return;

                this.current.customerId = this.normalizeId(customerId);
                this.current.alternativeId = this.normalizeId(alternativeId);
                this.current.productId = this.normalizeId(productId);

                container.innerHTML = this.loadingHtml();

                const params = new URLSearchParams({
                    customer_id: this.current.customerId,
                    alternative_id: this.current.alternativeId,
                    product_id: this.current.productId,
                });

                try {
                    const res = await fetch(`${this.routes.index}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                    });

                    if (!res.ok) {
                        throw new Error('Ressource nicht verfügbar.');
                    }

                    container.innerHTML = await res.text();

                    this.executeScripts(container);

                    if (typeof this.afterLoad === 'function') {
                        this.afterLoad(container);
                    } else if (typeof this.bind === 'function') {
                        this.bind();
                    }

                    window.maRefreshIcons();

                } catch (err) {
                    container.innerHTML = this.errorHtml(this.parseError(err));
                }
            },

            reload: async function () {
                const root = document.querySelector('[data-customer-review-root]');

                if (!this.current.customerId && root) {
                    this.current.customerId = this.normalizeId(root.dataset.customerId);
                    this.current.alternativeId = this.normalizeId(root.dataset.alternativeId);
                    this.current.productId = this.normalizeId(root.dataset.productId);
                }

                if (this.current.customerId) {
                    await this.load(this.current.customerId, this.current.alternativeId, this.current.productId);
                }
            }
        });

        window.loadCustomerReviews = function (customerId, alternativeId, productId) {
            return window.CustomerReviewAjax.load(customerId, alternativeId, productId);
        };
    })();
</script>
<script>
    /*
    |--------------------------------------------------------------------------
    | REALTIME OBJECT CARD SCREENSHOT THUMBNAIL
    |--------------------------------------------------------------------------
    | Updates the object card image immediately after screenshot upload/save/delete.
    */

    window.houseShotGetLatestImage = async function (alternativeId) {
        const res = await fetch(`${window.HouseScreenshotGallery.routes.loadBase}/${encodeURIComponent(alternativeId)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!res.ok) {
            throw new Error('Screenshot konnte nicht geladen werden.');
        }

        const data = await res.json();
        const images = Array.isArray(data) ? data : (data.images || []);

        if (!images.length) {
            return null;
        }

        return images[0];
    };

    window.houseShotUpdateObjectCardThumb = async function (alternativeId, imageObject = null) {
        try {
            const latest = imageObject || await window.houseShotGetLatestImage(alternativeId);

            const links = document.querySelectorAll(
                `.object-thumb-link[data-alternative-id="${CSS.escape(String(alternativeId))}"]`
            );

            if (!links.length) {
                return;
            }

            if (!latest) {
                links.forEach(function (link) {
                    link.classList.remove('has-house-screenshot');
                    link.dataset.latestScreenshotUrl = '';

                    const fallback = link.dataset.streetviewUrl || link.dataset.satelliteUrl || '';
                    const img = link.querySelector('img');

                    if (img && fallback) {
                        img.src = fallback;
                    }
                });

                return;
            }

            const url = window.buildSecureImageUrl(latest);
            const caption = latest.created_at || latest.image_name || 'Aktueller Screenshot';

            links.forEach(function (link) {
                const img = link.querySelector('img');

                link.classList.add('has-house-screenshot');
                link.dataset.latestScreenshotUrl = url;

                link.setAttribute('onmouseenter', `window.maShowHoverPreview(event, '${window.maEscapeHtml(url)}')`);
                link.setAttribute('onmouseleave', 'window.maHideHoverPreview()');

                if (img) {
                    img.src = url;
                    img.alt = caption;
                    img.classList.add('object-thumb-img', 'object-thumb-screenshot', 'is-live-updated');
                    img.style.width = '160px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '14px';
                    img.style.pointerEvents = 'none';
                }

                let badge = link.querySelector('.object-shot-live-badge');

                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'object-shot-live-badge';
                    badge.textContent = 'Screenshot';
                    link.appendChild(badge);
                }
            });

        } catch (error) {
            console.warn('Object thumbnail refresh failed:', error);
        }
    };

    window.houseShotRefreshAllObjectCards = function () {
        document.querySelectorAll('.object-thumb-link[data-alternative-id]').forEach(function (link) {
            const alternativeId = link.dataset.alternativeId;

            if (alternativeId) {
                window.houseShotUpdateObjectCardThumb(alternativeId);
            }
        });
    };
</script>

@once
    <style>
        .fw-collapse-toolbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            margin: 0 0 14px 0;
            flex-wrap: wrap;
        }

        .fw-collapse-action {
            border: 1px solid #dbe4ef;
            background: #ffffff;
            color: #334155;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 7px 18px rgba(15, 23, 42, .06);
            transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, background .16s ease;
        }

        .fw-collapse-action:hover {
            transform: translateY(-1px);
            border-color: #b9c8d8;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .10);
            background: #f8fafc;
        }

        .fw-section.fw-collapsible-card {
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease, transform .18s ease;
        }

        .fw-section.fw-collapsible-card>.fw-section-head {
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .fw-section.fw-collapsible-card>.fw-section-head .fw-badge {
            min-width: 0;
        }

        .fw-collapse-toggle {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            border-radius: 999px;
            border: 1px solid #dbe4ef;
            background: #ffffff;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(15, 23, 42, .07);
            transition: transform .18s ease, background .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .fw-collapse-toggle:hover {
            background: #f8fafc;
            border-color: #b9c8d8;
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(15, 23, 42, .10);
        }

        .fw-collapse-toggle i {
            width: 16px;
            height: 16px;
            transition: transform .2s ease;
        }

        .fw-section.fw-collapsible-card.is-collapsed {
            box-shadow: 0 7px 18px rgba(15, 23, 42, .045);
        }

        .fw-section.fw-collapsible-card.is-collapsed>.fw-section-head {
            margin-bottom: 0;
        }

        .fw-section.fw-collapsible-card.is-collapsed> :not(.fw-section-head) {
            display: none !important;
        }

        .fw-section.fw-collapsible-card.is-collapsed .fw-collapse-toggle i {
            transform: rotate(-90deg);
        }

        @media (max-width: 640px) {
            .fw-collapse-toolbar {
                justify-content: stretch;
            }

            .fw-collapse-action {
                flex: 1 1 auto;
                justify-content: center;
            }

            .fw-section.fw-collapsible-card>.fw-section-head {
                align-items: flex-start;
            }
        }
    </style>

    <script>
        (function () {
            const STORAGE_KEY = 'fwCollapsibleCardsStateV2';

            function readState() {
                try {
                    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}') || {};
                } catch (e) {
                    return {};
                }
            }

            function writeState(state) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                } catch (e) {
                    // localStorage can be blocked in some browsers.
                }
            }

            const state = readState();

            function refreshIcons() {
                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }

            function cardTitle(section) {
                const badge = section.querySelector(':scope > .fw-section-head .fw-badge');
                return (badge ? badge.textContent : 'card').replace(/\s+/g, ' ').trim();
            }

            function cardKey(section, index) {
                const form = section.closest('.partial-form');
                const sectionName = form?.dataset?.section || 'form';
                const formId = form?.dataset?.id || 'new';
                return `${sectionName}:${formId}:${index}:${cardTitle(section).toLowerCase()}`;
            }

            function setCollapsed(section, collapsed, persist = true) {
                const btn = section.querySelector(':scope > .fw-section-head .fw-collapse-toggle');

                section.classList.toggle('is-collapsed', collapsed);

                if (btn) {
                    btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    btn.setAttribute('title', collapsed ? 'Karte öffnen' : 'Karte einklappen');
                }

                if (persist && section.dataset.fwCollapseKey) {
                    state[section.dataset.fwCollapseKey] = collapsed ? 1 : 0;
                    writeState(state);
                }
            }

            function getCards(scope) {
                return Array.from(scope.querySelectorAll('.partial-form .fw-section.fw-collapsible-card'));
            }

            function addToolbar(shell) {
                if (shell.querySelector(':scope > .fw-collapse-toolbar')) {
                    return;
                }

                const body = shell.querySelector(':scope > .fw-body');

                if (!body) {
                    return;
                }

                const toolbar = document.createElement('div');

                toolbar.className = 'fw-collapse-toolbar';
                toolbar.innerHTML = `
                            <button type="button" class="fw-collapse-action" data-fw-collapse-action="open">
                                <i data-feather="maximize-2"></i>
                                Alle öffnen
                            </button>
                            <button type="button" class="fw-collapse-action" data-fw-collapse-action="close">
                                <i data-feather="minimize-2"></i>
                                Alle einklappen
                            </button>
                        `;

                shell.insertBefore(toolbar, body);

                toolbar.addEventListener('click', function (event) {
                    const btn = event.target.closest('[data-fw-collapse-action]');

                    if (!btn) {
                        return;
                    }

                    const shouldClose = btn.dataset.fwCollapseAction === 'close';

                    getCards(shell).forEach(function (section) {
                        setCollapsed(section, shouldClose, true);
                    });

                    refreshIcons();
                });
            }

            function initFwCollapsibleCards(root = document) {
                root.querySelectorAll('.partial-form .fw-shell').forEach(addToolbar);

                root.querySelectorAll('.partial-form .fw-section').forEach(function (section, index) {
                    if (section.dataset.fwCollapsibleReady === '1') {
                        return;
                    }

                    const head = section.querySelector(':scope > .fw-section-head');

                    if (!head) {
                        return;
                    }

                    section.dataset.fwCollapsibleReady = '1';
                    section.classList.add('fw-collapsible-card');

                    const key = cardKey(section, index);
                    section.dataset.fwCollapseKey = key;

                    head.classList.add('fw-collapsible-head');
                    head.setAttribute('role', 'button');
                    head.setAttribute('tabindex', '0');
                    head.setAttribute('aria-label', cardTitle(section) + ' öffnen oder einklappen');

                    if (!head.querySelector(':scope > .fw-collapse-toggle')) {
                        const toggle = document.createElement('button');
                        toggle.type = 'button';
                        toggle.className = 'fw-collapse-toggle';
                        toggle.setAttribute('aria-expanded', 'true');
                        toggle.setAttribute('title', 'Karte einklappen');
                        toggle.innerHTML = '<i data-feather="chevron-down"></i>';
                        head.appendChild(toggle);
                    }

                    setCollapsed(section, state[key] === 1, false);

                    head.addEventListener('click', function (event) {
                        if (event.target.closest('a, button, input, select, textarea, label')) {
                            if (!event.target.closest('.fw-collapse-toggle')) {
                                return;
                            }
                        }

                        event.preventDefault();

                        setCollapsed(section, !section.classList.contains('is-collapsed'), true);
                        refreshIcons();
                    });

                    head.addEventListener('keydown', function (event) {
                        if (event.key !== 'Enter' && event.key !== ' ') {
                            return;
                        }

                        event.preventDefault();

                        setCollapsed(section, !section.classList.contains('is-collapsed'), true);
                        refreshIcons();
                    });
                });

                refreshIcons();
            }

            window.initFwCollapsibleCards = initFwCollapsibleCards;

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    initFwCollapsibleCards(document);
                });
            } else {
                initFwCollapsibleCards(document);
            }

            if (!window.__fwCollapsibleObserverReady) {
                window.__fwCollapsibleObserverReady = true;

                new MutationObserver(function (mutations) {
                    const shouldInit = mutations.some(function (mutation) {
                        return Array.from(mutation.addedNodes || []).some(function (node) {
                            return node.nodeType === 1 && (
                                node.matches?.('.partial-form, .fw-section, .fw-shell') ||
                                node.querySelector?.('.partial-form .fw-section, .partial-form .fw-shell')
                            );
                        });
                    });

                    if (shouldInit) {
                        initFwCollapsibleCards(document);
                    }
                }).observe(document.documentElement, {
                    childList: true,
                    subtree: true
                });
            }
        })();
    </script>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            if (typeof window.houseShotRefreshAllObjectCards === 'function') {
                window.houseShotRefreshAllObjectCards();
            }
        }, 800);
    });
</script>



<script>
    (function () {
        "use strict";

        window.toggleOfferCollapse = function (button) {
            const card = button.closest('[data-offer-card]');
            if (!card) return;

            card.classList.toggle('is-open');

            if (window.feather) {
                window.feather.replace();
            }
        };

        function replaceIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        function bindOfferCards() {
            document.querySelectorAll('[data-offer-toggle]').forEach(function (row) {
                if (row.dataset.bound === '1') return;

                row.dataset.bound = '1';

                row.addEventListener('click', function (event) {
                    const ignored = event.target.closest('a, button, input, select, textarea, label, form');

                    if (ignored) return;

                    const card = row.closest('[data-offer-card]');
                    if (!card) return;

                    card.classList.toggle('is-open');

                    replaceIcons();
                });
            });
        }

        replaceIcons();
        bindOfferCards();

        if (window.jQuery && $.fn.tooltip) {
            $('[title]').tooltip();
        }
    })();
</script>


<script>
    (function () {
        "use strict";

        const quickOfferUrl = @json(route('admin.offers.quick-open'));

        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : '';
        }

        function setButtonLoading(button, loading) {
            if (!button) return;

            if (loading) {
                button.dataset.originalHtml = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `
                    <span style="width:16px;height:16px;border:2px solid rgba(255,255,255,.45);border-top-color:#fff;border-radius:50%;display:inline-block;animation:ofSpin .7s linear infinite;"></span>
                    Wird geöffnet...
                `;
            } else {
                button.disabled = false;

                if (button.dataset.originalHtml) {
                    button.innerHTML = button.dataset.originalHtml;
                }

                if (window.feather) {
                    window.feather.replace();
                }
            }
        }

        function showOfferError(message) {
            alert(message || 'Angebot konnte nicht geöffnet werden.');
        }

        window.quickOpenOfferFolder = async function (button) {
            const customerId = button?.dataset?.customerId || '';
            const alternativeId = button?.dataset?.alternativeId || '';
            const productId = button?.dataset?.productId || '';

            if (!customerId || !alternativeId || !productId) {
                showOfferError('Kunde, Objekt oder Produkt fehlt. Bitte Seite neu laden.');
                return;
            }

            setButtonLoading(button, true);

            try {
                const response = await fetch(quickOfferUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        customer_id: customerId,
                        alternative_id: alternativeId,
                        product_id: productId,
                    }),
                });

                const json = await response.json().catch(() => null);

                if (!response.ok || !json || !json.success) {
                    const validationMessage = json?.message
                        || Object.values(json?.errors || {})?.flat()?.[0]
                        || 'Angebot konnte nicht erstellt/geöffnet werden.';

                    throw new Error(validationMessage);
                }

                if (json.redirect_url) {
                    window.location.href = json.redirect_url;
                    return;
                }

                throw new Error('Keine Weiterleitungs-URL erhalten.');
            } catch (error) {
                console.error('Offer quick open failed:', error);
                showOfferError(error.message);
                setButtonLoading(button, false);
            }
        };

        document.addEventListener('click', function (event) {
            const button = event.target.closest('#quickOpenOfferBtn');

            if (!button) return;

            event.preventDefault();
            window.quickOpenOfferFolder(button);
        });

        if (!document.getElementById('ofQuickOfferSpinStyle')) {
            const style = document.createElement('style');
            style.id = 'ofQuickOfferSpinStyle';
            style.textContent = `
                @keyframes ofSpin {
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
    })();
</script>

<script>
    (function () {
        'use strict';

        const COUNT_URL = @json(route('new-leads.sidebar-counts'));
        const loadingScopes = new Set();

        function numberValue(value) {
            const number = Number(value || 0);
            return Number.isFinite(number) ? number : 0;
        }

        function formatCount(value) {
            const count = numberValue(value);

            if (count <= 0) {
                return '0';
            }

            if (count > 99) {
                return '99+';
            }

            return String(count);
        }

        function formatEuro(value) {
            const amount = numberValue(value);

            return amount.toLocaleString('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }) + ' €';
        }

        function setBadgeLoading(badge, loading) {
            if (!badge) return;

            badge.classList.toggle('is-loading', Boolean(loading));
            badge.classList.remove('is-error');
        }

        function updateCountBadge(badge, value) {
            if (!badge) return;

            const count = numberValue(value);
            const oldValue = numberValue(badge.dataset.countValue);

            badge.dataset.countValue = String(count);
            badge.textContent = formatCount(count);

            badge.classList.toggle('is-zero', count === 0);
            badge.classList.remove('is-loading', 'is-error');

            badge.title = count > 0 ? count + ' Einträge' : 'Keine Einträge';

            if (oldValue !== count) {
                badge.classList.remove('is-pulse');
                void badge.offsetWidth;
                badge.classList.add('is-pulse');
            }
        }

        function updateAmountBadge(badge, value) {
            if (!badge) return;

            const amount = numberValue(value);
            const label = badge.dataset.sidebarAmountLabel || 'Gesamt';

            badge.textContent = label + ': ' + formatEuro(amount);
            badge.classList.toggle('is-zero', amount === 0);
            badge.classList.remove('is-loading', 'is-error');
        }

        function getScopes() {
            const scopeMap = new Map();

            document.querySelectorAll('[data-lead-sidebar-count][data-customer-id][data-alternative-id][data-product-id]').forEach(function (badge) {
                const customerId = badge.dataset.customerId || '';
                const alternativeId = badge.dataset.alternativeId || '';
                const productId = badge.dataset.productId || '';
                const key = customerId + ':' + alternativeId + ':' + productId;

                if (!customerId || !alternativeId || !productId) {
                    return;
                }

                if (!scopeMap.has(key)) {
                    scopeMap.set(key, {
                        key,
                        customerId,
                        alternativeId,
                        productId,
                    });
                }
            });

            return Array.from(scopeMap.values());
        }

        function applyCountsToScope(scope, counts) {
            const selector = [
                '[data-customer-id="' + CSS.escape(String(scope.customerId)) + '"]',
                '[data-alternative-id="' + CSS.escape(String(scope.alternativeId)) + '"]',
                '[data-product-id="' + CSS.escape(String(scope.productId)) + '"]'
            ].join('');

            document.querySelectorAll('[data-lead-sidebar-count]' + selector).forEach(function (badge) {
                const countKey = badge.dataset.leadSidebarCount || '';
                updateCountBadge(badge, counts[countKey] || 0);
            });

            document.querySelectorAll('[data-lead-sidebar-amount]' + selector).forEach(function (badge) {
                const amountKey = badge.dataset.leadSidebarAmount || '';
                updateAmountBadge(badge, counts[amountKey] || 0);
            });
        }

        async function refreshScope(scope) {
            if (!scope || loadingScopes.has(scope.key)) {
                return;
            }

            loadingScopes.add(scope.key);

            const selector = [
                '[data-customer-id="' + CSS.escape(String(scope.customerId)) + '"]',
                '[data-alternative-id="' + CSS.escape(String(scope.alternativeId)) + '"]',
                '[data-product-id="' + CSS.escape(String(scope.productId)) + '"]'
            ].join('');

            document.querySelectorAll('[data-lead-sidebar-count]' + selector).forEach(function (badge) {
                setBadgeLoading(badge, true);
            });

            document.querySelectorAll('[data-lead-sidebar-amount]' + selector).forEach(function (badge) {
                badge.classList.add('is-loading');
                badge.classList.remove('is-error');
            });

            try {
                const url = new URL(COUNT_URL, window.location.origin);

                url.searchParams.set('customer_id', scope.customerId);
                url.searchParams.set('alternative_id', scope.alternativeId);
                url.searchParams.set('product_id', scope.productId);

                const response = await fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const payload = await response.json().catch(function () {
                    return {};
                });

                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Zähler konnten nicht geladen werden.');
                }

                applyCountsToScope(scope, payload.counts || {});
            } catch (error) {
                console.warn('Lead sidebar counts failed:', error);

                document.querySelectorAll('[data-lead-sidebar-count]' + selector).forEach(function (badge) {
                    badge.classList.remove('is-loading');
                    badge.classList.add('is-error');
                });

                document.querySelectorAll('[data-lead-sidebar-amount]' + selector).forEach(function (badge) {
                    badge.classList.remove('is-loading');
                    badge.classList.add('is-error');
                });
            } finally {
                loadingScopes.delete(scope.key);
            }
        }

        function refreshLeadSidebarCounts() {
            getScopes().forEach(refreshScope);
        }

        window.refreshLeadSidebarCounts = refreshLeadSidebarCounts;

        document.addEventListener('DOMContentLoaded', function () {
            refreshLeadSidebarCounts();
        });

        document.addEventListener('click', function (event) {
            const opensProductList = event.target.closest('.project-link, .project-card, .object-header');

            if (opensProductList) {
                setTimeout(refreshLeadSidebarCounts, 250);
            }
        });

        window.addEventListener('sa:lead-sidebar-counts-refresh', refreshLeadSidebarCounts);
    })();
</script>

<script>
    (function () {
        "use strict";

        function getPanel(root) {
            return (root || document).querySelector("#docsUploadPanel");
        }

        function showPanel(root) {
            const panel = getPanel(root);

            if (!panel) {
                return;
            }

            panel.hidden = false;
            panel.style.display = "block";

            setTimeout(function () {
                panel.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            }, 40);
        }

        function hidePanel(root) {
            const panel = getPanel(root);

            if (!panel) {
                return;
            }

            panel.hidden = true;
            panel.style.display = "none";
        }

        function togglePanel(root) {
            const panel = getPanel(root);

            if (!panel) {
                return;
            }

            const isHidden = panel.hidden || panel.style.display === "none";

            if (isHidden) {
                showPanel(root);
            } else {
                hidePanel(root);
            }
        }

        window.showDocsUploadPanel = function () {
            showPanel(document);
        };

        window.hideDocsUploadPanel = function () {
            hidePanel(document);
        };

        window.toggleDocsUploadPanel = function () {
            togglePanel(document);
        };

        document.addEventListener("click", function (event) {
            const openBtn = event.target.closest("[data-docs-toggle-upload]");
            const closeBtn = event.target.closest("[data-docs-close-upload]");

            if (openBtn) {
                event.preventDefault();

                const root = openBtn.closest("#docsShell") || document;
                togglePanel(root);
                return;
            }

            if (closeBtn) {
                event.preventDefault();

                const root = closeBtn.closest("#docsShell") || document;
                hidePanel(root);
            }
        });

        document.addEventListener("keydown", function (event) {
            if (event.key !== "Escape") {
                return;
            }

            document.querySelectorAll("#docsUploadPanel").forEach(function (panel) {
                panel.hidden = true;
                panel.style.display = "none";
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll("#docsUploadPanel[hidden]").forEach(function (panel) {
                panel.style.display = "none";
            });
        });


    })();

    (function () {
        "use strict";

        function getRoot() {
            return document.getElementById("docsShell");
        }

        function getActivePane(root) {
            return root.querySelector(".docs-tab-pane.show.active")
                || root.querySelector(".docs-tab-pane.active")
                || root.querySelector(".docs-tab-pane");
        }

        function applyDocumentFilters(root) {
            if (!root) return;

            const activePane = getActivePane(root);
            if (!activePane) return;

            const searchInput = root.querySelector("#searchImage");
            const stageFilter = root.querySelector("#stageFilter");

            const keyword = (searchInput?.value || "").trim().toLowerCase();
            const stage = (stageFilter?.value || "").trim();

            activePane.querySelectorAll(".docs-gallery-item").forEach(function (item) {
                const itemStage = (item.dataset.stage || "").trim();

                const searchText = (
                    item.dataset.search ||
                    item.dataset.name ||
                    item.textContent ||
                    ""
                ).toLowerCase();

                const matchesSearch = !keyword || searchText.includes(keyword);
                const matchesStage = !stage || itemStage === stage;

                item.style.display = matchesSearch && matchesStage ? "" : "none";
            });
        }

        function resetHiddenItemsInInactiveTabs(root) {
            root.querySelectorAll(".docs-tab-pane:not(.active) .docs-gallery-item").forEach(function (item) {
                item.style.display = "";
            });
        }

        function switchDocumentTab(root, tabLink) {
            if (!root || !tabLink) return;

            const targetSelector = tabLink.dataset.tab || tabLink.getAttribute("href");
            if (!targetSelector) return;

            const targetPane = root.querySelector(targetSelector);
            if (!targetPane) return;

            root.querySelectorAll(".docs-tab-link").forEach(function (link) {
                link.classList.remove("active");
            });

            root.querySelectorAll(".docs-tab-pane").forEach(function (pane) {
                pane.classList.remove("show", "active");
                pane.style.display = "none";
            });

            tabLink.classList.add("active");
            targetPane.classList.add("show", "active");
            targetPane.style.display = "block";

            resetHiddenItemsInInactiveTabs(root);
            applyDocumentFilters(root);

            if (window.GLightbox) {
                try {
                    if (window.__docsLightbox) {
                        window.__docsLightbox.destroy();
                    }

                    window.__docsLightbox = GLightbox({
                        selector: "#docsShell .glightbox",
                        loop: true,
                        openEffect: "zoom",
                        closeEffect: "fade"
                    });
                } catch (e) {
                    console.warn("GLightbox refresh failed:", e);
                }
            }
        }

        function setDocumentView(root, view) {
            if (!root) return;

            const cleanView = view === "list" ? "list" : "grid";

            root.dataset.view = cleanView;

            try {
                localStorage.setItem("customerDocumentsView", cleanView);
            } catch (e) { }

            root.querySelectorAll(".docs-view-btn").forEach(function (btn) {
                btn.classList.toggle("is-active", btn.dataset.view === cleanView);
            });
        }

        function initDocumentTabsAndFilters() {
            const root = getRoot();
            if (!root) return;

            if (root.dataset.docsTabsReady === "1") {
                applyDocumentFilters(root);
                return;
            }

            root.dataset.docsTabsReady = "1";

            root.querySelectorAll(".docs-tab-pane").forEach(function (pane) {
                pane.style.display = pane.classList.contains("active") ? "block" : "none";
            });

            root.addEventListener("click", function (event) {
                const tabLink = event.target.closest(".docs-tab-link");
                if (tabLink && root.contains(tabLink)) {
                    event.preventDefault();
                    switchDocumentTab(root, tabLink);
                    return;
                }

                const viewBtn = event.target.closest(".docs-view-btn");
                if (viewBtn && root.contains(viewBtn)) {
                    event.preventDefault();
                    setDocumentView(root, viewBtn.dataset.view || "grid");
                }
            });

            const searchInput = root.querySelector("#searchImage");
            const stageFilter = root.querySelector("#stageFilter");

            if (searchInput) {
                searchInput.addEventListener("input", function () {
                    applyDocumentFilters(root);
                });
            }

            if (stageFilter) {
                stageFilter.addEventListener("change", function () {
                    applyDocumentFilters(root);
                });
            }

            const savedView = localStorage.getItem("customerDocumentsView") || root.dataset.view || "grid";
            setDocumentView(root, savedView);

            const activeTab = root.querySelector(".docs-tab-link.active") || root.querySelector(".docs-tab-link");
            if (activeTab) {
                switchDocumentTab(root, activeTab);
            } else {
                applyDocumentFilters(root);
            }
        }

        window.initDocumentTabsAndFilters = initDocumentTabsAndFilters;
        window.applyDocumentFilters = function () {
            applyDocumentFilters(getRoot());
        };

        document.addEventListener("DOMContentLoaded", initDocumentTabsAndFilters);

        const observer = new MutationObserver(function () {
            const root = getRoot();

            if (root && root.dataset.docsTabsReady !== "1") {
                initDocumentTabsAndFilters();
            }
        });

        observer.observe(document.documentElement, {
            childList: true,
            subtree: true
        });
    })();
</script>

<script>
    (function () {
        "use strict";

        function getDocsRoot() {
            return document.getElementById("docsShell");
        }

        function openDocsPdfModal(url, title) {
            const root = getDocsRoot();
            if (!root || !url) return;

            const modal = root.querySelector("#pdfViewerModal");
            const iframe = root.querySelector("#pdfViewerIframe");
            const titleEl = root.querySelector("#pdfViewerTitle");

            if (!modal || !iframe) {
                console.error("PDF modal elements not found.");
                return;
            }

            iframe.src = url;

            if (titleEl) {
                titleEl.textContent = title || "PDF Vorschau";
            }

            modal.classList.add("is-open");
            modal.setAttribute("aria-hidden", "false");
            document.body.classList.add("docs-pdf-open");
        }

        function closeDocsPdfModal() {
            const root = getDocsRoot();
            if (!root) return;

            const modal = root.querySelector("#pdfViewerModal");
            const iframe = root.querySelector("#pdfViewerIframe");

            if (iframe) {
                iframe.src = "";
            }

            if (modal) {
                modal.classList.remove("is-open");
                modal.setAttribute("aria-hidden", "true");
            }

            document.body.classList.remove("docs-pdf-open");
        }

        document.addEventListener("click", function (event) {
            const openBtn = event.target.closest("[data-docs-open-pdf]");

            if (openBtn) {
                event.preventDefault();
                event.stopPropagation();

                openDocsPdfModal(
                    openBtn.getAttribute("data-pdf-url"),
                    openBtn.getAttribute("data-pdf-title")
                );

                return;
            }

            const closeBtn = event.target.closest("[data-docs-close-pdf]");

            if (closeBtn) {
                event.preventDefault();
                closeDocsPdfModal();
            }
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                closeDocsPdfModal();
            }
        });

        window.openDocsPdfModal = openDocsPdfModal;
        window.closeDocsPdfModal = closeDocsPdfModal;
    })();
</script>


<script>
    (function () {
        'use strict';

        const referenceRoutes = {
            view: @json(route('lead.reference')),
            nearby: @json(url('/leads-nearby')),
        };

        function getMainContent() {
            return document.getElementById('mainContent');
        }

        function refreshReferenceIcons() {
            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }

        function setReferenceLoading(mainContent) {
            mainContent.innerHTML = `
            <div class="p-4 text-center">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <div class="font-weight-bold">Referenzen werden geladen...</div>
                <div class="text-muted small">Bitte warten</div>
            </div>
        `;
        }

        function setReferenceError(mainContent, message) {
            mainContent.innerHTML = `
            <div class="alert alert-danger m-3">
                <strong>Fehler</strong><br>
                ${String(message || 'Referenzen konnten nicht geladen werden.')}
            </div>
        `;
        }

        async function loadReferencePartial(customerId = '', alternativeId = '') {
            const mainContent = getMainContent();

            if (!mainContent) {
                console.error('#mainContent not found.');
                return;
            }

            setReferenceLoading(mainContent);

            const params = new URLSearchParams();

            if (customerId) {
                params.set('customer_id', customerId);
            }

            if (alternativeId) {
                params.set('alternative_id', alternativeId);
            }

            const url = `${referenceRoutes.view}?${params.toString()}`;

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const html = await response.text();

                if (!response.ok) {
                    throw new Error(html || `HTTP ${response.status}`);
                }

                mainContent.innerHTML = html;

                refreshReferenceIcons();

                if (typeof window.initLeadReferenceView === 'function') {
                    window.initLeadReferenceView({
                        customerId,
                        alternativeId,
                        nearbyUrl: referenceRoutes.nearby,
                    });
                } else {
                    loadNearbyReferenceList();
                }
            } catch (error) {
                console.error('Reference partial could not be loaded:', error);
                setReferenceError(mainContent, error.message);
            }
        }

        async function loadNearbyReferenceList() {
            const list = document.getElementById('referenceResultList');

            if (!list) return;

            list.innerHTML = '<div class="text-muted">Referenzen werden geladen...</div>';

            try {
                const response = await fetch(referenceRoutes.nearby, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const rows = await response.json();

                if (!response.ok) {
                    throw new Error('Referenzen konnten nicht geladen werden.');
                }

                if (!Array.isArray(rows) || !rows.length) {
                    list.innerHTML = '<div class="text-muted">Keine Referenzen gefunden.</div>';
                    return;
                }

                list.innerHTML = rows.map(item => {
                    const name = `${item.customer_name || ''} ${item.customer_lastname || ''}`.trim() || 'Unbekannter Kunde';

                    return `
                    <div class="border rounded p-2 mb-2">
                        <strong>${escapeHtml(name)}</strong>
                        <div class="small text-muted">${escapeHtml(item.full_address || '')}</div>
                        <div class="small">${escapeHtml(item.product_statuses || '')}</div>
                    </div>
                `;
                }).join('');
            } catch (error) {
                console.error('Nearby references could not be loaded:', error);
                list.innerHTML = '<div class="text-danger">Referenzen konnten nicht geladen werden.</div>';
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        /*
        |--------------------------------------------------------------------------
        | Global function for onclick usage
        |--------------------------------------------------------------------------
        */
        window.openReferenceView = function (customerId = '', alternativeId = '') {
            return loadReferencePartial(customerId, alternativeId);
        };

        /*
        |--------------------------------------------------------------------------
        | Delegated click handler for dynamic object buttons
        |--------------------------------------------------------------------------
        */
        document.addEventListener('click', function (event) {
            const button = event.target.closest('.openReferenceView');

            if (!button) return;

            event.preventDefault();
            event.stopPropagation();

            const customerId = button.dataset.customerId || '';
            const alternativeId = button.dataset.alternativeId || '';

            loadReferencePartial(customerId, alternativeId);
        });

        /*
        |--------------------------------------------------------------------------
        | Button inside partial
        |--------------------------------------------------------------------------
        */
        document.addEventListener('click', function (event) {
            const button = event.target.closest('#loadNearbyReferencesBtn');

            if (!button) return;

            event.preventDefault();
            loadNearbyReferenceList();
        });
    })();
</script>


<style>
    /* FINAL SIDEBAR OBJECT/PRODUCT MENU FIX */
    .customerSidebar .product-list,
    .customerSidebar .sub-nav {
        display: none !important;
    }
    .customerSidebar .product-list.show,
    .customerSidebar .sub-nav.show {
        display: block !important;
    }
    .customerSidebar .object-section.is-open > .object-header {
        background: var(--ma-blue-soft) !important;
        border-color: var(--ma-heading) !important;
    }
    .customerSidebar .ma-product-card.is-open {
        border-color: var(--ma-heading) !important;
        background: #ffffff !important;
    }
</style>


<script>
(function (window, document) {
    'use strict';

    /**
     * FINAL SAFE BOOT FOR PROFILE PARTIAL
     * This file is included inside customer_profile.blade.php, so nothing here uses
     * top-level const/let variable names like quill, updateUrlTemplate, etc.
     */
    window.CustomerProfileObjectMenu = window.CustomerProfileObjectMenu || {};

    function qsa(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function closeProductMenus(scope, exceptMenu) {
        qsa('.sub-nav.show', scope).forEach(function (menu) {
            if (menu !== exceptMenu) {
                menu.classList.remove('show');
                menu.style.display = 'none';
                var card = menu.previousElementSibling;
                if (card && card.classList) card.classList.remove('is-open');
            }
        });
    }

    function closeObjectMenus(exceptList) {
        qsa('.customerSidebar .product-list.show').forEach(function (list) {
            if (list !== exceptList) {
                list.classList.remove('show');
                list.style.display = 'none';
                var section = list.closest('.object-section');
                if (section) section.classList.remove('is-open');
                closeProductMenus(list, null);
            }
        });
    }

    function openObjectList(list, forceOpen) {
        if (!list) return false;

        var section = list.closest('.object-section');
        var header = section ? section.querySelector('.object-header') : null;
        var isOpen = list.classList.contains('show') || list.style.display === 'block';
        var shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !isOpen;

        closeObjectMenus(shouldOpen ? list : null);

        list.classList.toggle('show', shouldOpen);
        list.style.display = shouldOpen ? 'block' : 'none';

        if (section) section.classList.toggle('is-open', shouldOpen);
        if (header) header.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');

        if (!shouldOpen) closeProductMenus(list, null);

        if (shouldOpen && window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        return shouldOpen;
    }

    function findObjectList(target) {
        if (!target) return null;

        if (typeof target === 'string') {
            return document.getElementById(target);
        }

        var section = target.closest ? target.closest('.object-section') : null;
        return section ? section.querySelector('.product-list') : null;
    }

    function toggleProductCard(card) {
        if (!card) return false;

        var menu = card.nextElementSibling;
        while (menu && !(menu.classList && menu.classList.contains('sub-nav'))) {
            menu = menu.nextElementSibling;
        }

        if (!menu) return false;

        var list = card.closest('.product-list');
        if (list && !list.classList.contains('show')) {
            openObjectList(list, true);
        }

        var isOpen = menu.classList.contains('show') || menu.style.display === 'block';
        var shouldOpen = !isOpen;

        closeProductMenus(list || document, shouldOpen ? menu : null);

        menu.classList.toggle('show', shouldOpen);
        menu.style.display = shouldOpen ? 'block' : 'none';
        card.classList.toggle('is-open', shouldOpen);

        if (shouldOpen && window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        return shouldOpen;
    }

    window.CustomerProfileObjectMenu.openObjectList = openObjectList;
    window.CustomerProfileObjectMenu.toggleProductCard = toggleProductCard;

    // Override older partial/page functions. Do not use "||" here because old broken versions may already exist.
    window.toggleObject = function (target) {
        return openObjectList(findObjectList(target));
    };

    window.toggleProduct = function (target) {
        if (typeof target === 'string') {
            return toggleProductCard(document.querySelector('[data-product-key="' + target + '"], #' + CSS.escape(target)));
        }
        return toggleProductCard(target && target.closest ? target.closest('.ma-product-card, .project-card, .project-link') : null);
    };

    document.addEventListener('click', function (event) {
        if (!event.target || !event.target.closest) return;

        var protectedClick = event.target.closest([
            'a',
            'button',
            'input',
            'select',
            'textarea',
            'label',
            '.sidebar-gallery',
            '[data-gallery-sidebar]',
            '.house-shot-upload-box',
            '.house-shot-tabs',
            '.house-shot-panel',
            '.house-shot-tab-btn',
            '.object-thumb-link',
            '.object-thumb-img',
            '.ma-gallery-close',
            '[data-gallery-close]',
            '[data-no-object-toggle]',
            '[data-no-menu-toggle]',
            '.price-edit-trigger',
            '.project-metric--calendar'
        ].join(','));

        if (protectedClick) {
            return;
        }

        var subButton = event.target.closest('.customerSidebar .sub-nav button, .customerSidebar .nav-section-btn');
        if (subButton) return;

        var productCard = event.target.closest('.customerSidebar .ma-product-card, .customerSidebar .project-card, .customerSidebar .project-link');
        if (productCard) {
            event.preventDefault();
            event.stopPropagation();
            toggleProductCard(productCard);
            return;
        }

        var header = event.target.closest('.customerSidebar .object-header');
        if (header) {
            event.preventDefault();
            event.stopPropagation();
            openObjectList(findObjectList(header));
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        var header = event.target.closest ? event.target.closest('.customerSidebar .object-header') : null;
        if (!header) return;
        event.preventDefault();
        openObjectList(findObjectList(header));
    });

    function bootObjectMenuAccessibility() {
        qsa('.customerSidebar .object-header').forEach(function (header) {
            header.setAttribute('role', 'button');
            header.setAttribute('tabindex', '0');
            header.setAttribute('aria-expanded', 'false');
        });

        qsa('.customerSidebar .product-list, .customerSidebar .sub-nav').forEach(function (el) {
            if (!el.classList.contains('show')) el.style.display = 'none';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootObjectMenuAccessibility);
    } else {
        bootObjectMenuAccessibility();
    }
})(window, document);
</script>



{{-- =========================================================
   FINAL FIX: HOUSE SCREENSHOT SIDEBAR + GOOGLE MAP TAB
   Safe for profile.blade.php partial includes. No top-level let/const.
========================================================= --}}
<style>
    .sidebar-gallery[data-gallery-sidebar] {
        position: fixed !important;
        top: 0 !important;
        right: -110% !important;
        width: min(1180px, 96vw) !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        display: block !important;
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        z-index: 2147483000 !important;
        overflow-y: auto !important;
        background: #ffffff !important;
        transition: right .25s ease, opacity .18s ease, visibility .18s ease;
    }

    .sidebar-gallery[data-gallery-sidebar].active,
    .sidebar-gallery[data-gallery-sidebar].is-open {
        right: 0 !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .house-shot-panel { display: none; }
    .house-shot-panel.is-active { display: block !important; }

    .sidebar-gallery .google-map,
    .sidebar-gallery [id^="mapContainer"] {
        width: 100% !important;
        min-height: 420px !important;
        height: 420px !important;
        display: block !important;
    }

    .ma-shot-map-fallback {
        width: 100%;
        min-height: 420px;
        border-radius: 14px;
        border: 1px solid var(--ma-border, #c0d8ea);
        background: #f8fafc;
        display: grid;
        place-items: center;
        overflow: hidden;
    }

    .ma-shot-map-fallback img {
        width: 100%;
        height: 100%;
        min-height: 420px;
        object-fit: cover;
        display: block;
    }
</style>
<script>
(function (window, document) {
    'use strict';

    function byId(id) { return document.getElementById(id); }

    function clean(value) {
        return String(value || '').replace(/\s+/g, ' ').replace(/^,+|,+$/g, '').trim();
    }

    function hasGoogleMaps() {
        return !!(window.google && window.google.maps);
    }

    function getState() {
        window.HouseScreenshotGallery = window.HouseScreenshotGallery || {};
        window.HouseScreenshotGallery.current = window.HouseScreenshotGallery.current || {};
        return window.HouseScreenshotGallery.current;
    }

    function getCoordsFromSource(sourceEl) {
        var lat = parseFloat(sourceEl && sourceEl.dataset ? sourceEl.dataset.lat : '');
        var lng = parseFloat(sourceEl && sourceEl.dataset ? sourceEl.dataset.lng : '');
        if (Number.isFinite(lat) && Number.isFinite(lng)) return { lat: lat, lng: lng };
        return null;
    }

    function forceMapSize(mapEl) {
        if (!mapEl) return;
        mapEl.style.display = 'block';
        mapEl.style.width = '100%';
        mapEl.style.minHeight = '420px';
        mapEl.style.height = mapEl.offsetHeight && mapEl.offsetHeight > 80 ? mapEl.offsetHeight + 'px' : '420px';
    }

    function fallbackImageUrl(alternativeId, sourceEl) {
        if (sourceEl && sourceEl.dataset) {
            if (sourceEl.dataset.satelliteUrl) return sourceEl.dataset.satelliteUrl;
            if (sourceEl.dataset.streetviewUrl) return sourceEl.dataset.streetviewUrl;
        }

        if (typeof window.buildHouseStaticScreenshotUrl === 'function') {
            try { return window.buildHouseStaticScreenshotUrl(alternativeId); } catch (e) {}
        }

        return '';
    }

    function showStaticFallback(alternativeId, sourceEl, message) {
        var mapEl = byId('mapContainer' + alternativeId);
        if (!mapEl) return;
        var url = fallbackImageUrl(alternativeId, sourceEl);
        if (url) {
            mapEl.innerHTML = '<div class="ma-shot-map-fallback"><img src="' + String(url).replace(/"/g, '&quot;') + '" alt="Google Screenshot"></div>';
        } else {
            mapEl.innerHTML = '<div class="ma-shot-map-fallback text-muted p-3 text-center small">' + (message || 'Karte konnte nicht geladen werden.') + '</div>';
        }
    }

    function resolveCenter(alternativeId, sourceEl) {
        return new Promise(function (resolve, reject) {
            var state = getState();
            var direct = state.center || getCoordsFromSource(sourceEl);
            if (direct && Number.isFinite(parseFloat(direct.lat)) && Number.isFinite(parseFloat(direct.lng))) {
                resolve({ lat: parseFloat(direct.lat), lng: parseFloat(direct.lng) });
                return;
            }

            var address = clean(state.address || (sourceEl && sourceEl.dataset ? sourceEl.dataset.address : ''));
            if (!address || !hasGoogleMaps()) {
                reject(new Error('No coordinates or Google Maps'));
                return;
            }

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: address }, function (results, status) {
                if (status === 'OK' && results && results[0] && results[0].geometry) {
                    var loc = results[0].geometry.location;
                    resolve({ lat: loc.lat(), lng: loc.lng() });
                } else {
                    reject(new Error('Geocode failed: ' + status));
                }
            });
        });
    }

    function resizeMapLater(alternativeId) {
        var maps = window.MayarObjectMaps && window.MayarObjectMaps.sidebars ? window.MayarObjectMaps.sidebars : {};
        var map = maps[alternativeId];
        var state = getState();
        if (!map || !hasGoogleMaps()) return;

        [40, 120, 280, 600, 1000].forEach(function (delay) {
            setTimeout(function () {
                try {
                    var mapEl = byId('mapContainer' + alternativeId);
                    forceMapSize(mapEl);
                    google.maps.event.trigger(map, 'resize');
                    if (state.center) map.setCenter(state.center);
                } catch (e) {}
            }, delay);
        });
    }

    window.initSidebarObjectMap = function (alternativeId, sourceEl) {
        var mapEl = byId('mapContainer' + alternativeId);
        if (!mapEl) return;
        forceMapSize(mapEl);

        if (!hasGoogleMaps()) {
            showStaticFallback(alternativeId, sourceEl, 'Google Maps wird noch geladen...');
            var tries = 0;
            var timer = setInterval(function () {
                tries += 1;
                if (hasGoogleMaps()) {
                    clearInterval(timer);
                    window.initSidebarObjectMap(alternativeId, sourceEl);
                }
                if (tries > 30) clearInterval(timer);
            }, 200);
            return;
        }

        resolveCenter(alternativeId, sourceEl).then(function (center) {
            var state = getState();
            state.center = center;
            var modeSelect = byId('screenshotMode' + alternativeId);
            var zoomSelect = byId('screenshotZoom' + alternativeId);
            state.mode = modeSelect ? modeSelect.value : (state.mode || 'satellite');
            state.zoom = parseInt(zoomSelect ? zoomSelect.value : (state.zoom || '20'), 10) || 20;

            mapEl.innerHTML = '';
            forceMapSize(mapEl);

            window.MayarObjectMaps = window.MayarObjectMaps || {};
            window.MayarObjectMaps.sidebars = window.MayarObjectMaps.sidebars || {};

            var map = window.MayarObjectMaps.sidebars[alternativeId];
            if (!map) {
                map = new google.maps.Map(mapEl, {
                    center: center,
                    zoom: state.zoom,
                    mapTypeId: state.mode === 'streetview' ? 'satellite' : state.mode,
                    disableDefaultUI: false,
                    streetViewControl: true,
                    mapTypeControl: true,
                    fullscreenControl: true,
                    gestureHandling: 'greedy',
                    tilt: 0
                });
                new google.maps.Marker({ position: center, map: map });
                window.MayarObjectMaps.sidebars[alternativeId] = map;
            } else {
                map.setCenter(center);
                map.setZoom(state.zoom);
                map.setMapTypeId(state.mode === 'streetview' ? 'satellite' : state.mode);
            }

            if (typeof window.applySidebarMapMode === 'function') {
                try { window.applySidebarMapMode(alternativeId); } catch (e) { console.warn(e); }
            }

            resizeMapLater(alternativeId);
        }).catch(function () {
            showStaticFallback(alternativeId, sourceEl, 'Karte konnte nicht geladen werden.');
        });
    };

    window.openSidebarGallery = window.openObjectMapSidebar = function (sourceEl) {
        if (!sourceEl || !sourceEl.dataset) return;

        var alternativeId = sourceEl.dataset.alternativeId || '';
        var customerId = sourceEl.dataset.customerId || '';
        var address = clean(sourceEl.dataset.address || '');
        if (!alternativeId) return;

        var state = getState();
        state.customerId = customerId;
        state.alternativeId = alternativeId;
        state.address = address;
        state.center = getCoordsFromSource(sourceEl);
        state.mode = byId('screenshotMode' + alternativeId) ? byId('screenshotMode' + alternativeId).value : 'satellite';
        state.zoom = parseInt(byId('screenshotZoom' + alternativeId) ? byId('screenshotZoom' + alternativeId).value : '20', 10) || 20;
        state.sourceEl = sourceEl;

        document.querySelectorAll('.sidebar-gallery[data-gallery-sidebar].active, .sidebar-gallery[data-gallery-sidebar].is-open').forEach(function (item) {
            item.classList.remove('active', 'is-open');
        });

        var sidebar = byId('sidebarGallery' + alternativeId);
        if (!sidebar) return;

        sidebar.classList.add('active', 'is-open');
        sidebar.style.right = '0';
        sidebar.style.visibility = 'visible';
        sidebar.style.opacity = '1';
        sidebar.style.pointerEvents = 'auto';

        var label = byId('galleryAddress' + alternativeId);
        if (label) label.textContent = address || 'Adresse nicht verfügbar';

        if (typeof window.loadSidebarGallery === 'function') {
            try { window.loadSidebarGallery(customerId, alternativeId); } catch (e) { console.warn(e); }
        }

        window.houseShotSwitchTab(alternativeId, 'saved');

        if (window.feather && typeof window.feather.replace === 'function') window.feather.replace();
    };

    window.closeSidebarGallery = function (alternativeId) {
        var sidebar = byId('sidebarGallery' + alternativeId);
        if (!sidebar) return;
        sidebar.classList.remove('active', 'is-open');
        sidebar.style.right = '-110%';
        sidebar.style.visibility = 'hidden';
        sidebar.style.opacity = '0';
        sidebar.style.pointerEvents = 'none';
    };

    window.houseShotSwitchTab = function (objectId, tabName) {
        var root = document.querySelector('[data-house-shot-root="' + objectId + '"]');
        if (!root) return;

        root.querySelectorAll('[data-house-shot-tab-btn]').forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-house-shot-tab-btn') === tabName);
        });

        root.querySelectorAll('[data-house-shot-panel]').forEach(function (panel) {
            var active = panel.getAttribute('data-house-shot-panel') === tabName;
            panel.classList.toggle('is-active', active);
            panel.style.display = active ? 'block' : 'none';
        });

        if (tabName === 'google') {
            var state = getState();
            var sourceEl = state.sourceEl || document.querySelector('[data-alternative-id="' + objectId + '"][data-customer-id]');
            setTimeout(function () { window.initSidebarObjectMap(objectId, sourceEl); }, 80);
            setTimeout(function () { resizeMapLater(objectId); }, 260);
        }
    };

    document.addEventListener('click', function (event) {
        var gallery = event.target.closest ? event.target.closest('.sidebar-gallery[data-gallery-sidebar]') : null;
        /* Do not stop every gallery click here: upload/refresh buttons need their own handlers. */

        var tabBtn = event.target.closest ? event.target.closest('.house-shot-tab-btn[data-house-shot-tab-btn]') : null;
        if (tabBtn) {
            event.preventDefault();
            event.stopPropagation();
            var root = tabBtn.closest('[data-house-shot-root]');
            if (root) window.houseShotSwitchTab(root.getAttribute('data-house-shot-root'), tabBtn.getAttribute('data-house-shot-tab-btn'));
        }

        var thumb = event.target.closest ? event.target.closest('.object-thumb-link[data-alternative-id]') : null;
        if (thumb) {
            event.preventDefault();
            event.stopPropagation();
            if (typeof event.stopImmediatePropagation === 'function') {
                event.stopImmediatePropagation();
            }
            if (typeof window.openSidebarGallery === 'function') {
                return window.openSidebarGallery(thumb, event);
            }
            return false;
        }
    }, true);
})(window, document);
</script>


<script>
/* =========================================================
   FINAL FIX: Screenshot sidebar tabs must not open sidebar
   Reason: profile.blade.php is loaded as a partial and old broad click handlers
   can catch tab clicks. This capture listener runs on window before document
   handlers and fully owns only the screenshot tab buttons.
========================================================= */
(function (window, document) {
    'use strict';

    function byId(id) {
        return document.getElementById(id);
    }

    function getRootFromButton(btn) {
        return btn ? btn.closest('[data-house-shot-root]') : null;
    }

    function getObjectId(root) {
        return root ? root.getAttribute('data-house-shot-root') : '';
    }

    function getSourceElement(objectId) {
        if (!objectId) return null;
        try {
            return document.querySelector('.object-thumb-link[data-alternative-id="' + CSS.escape(String(objectId)) + '"]');
        } catch (e) {
            return document.querySelector('.object-thumb-link[data-alternative-id="' + String(objectId).replace(/"/g, '\\"') + '"]');
        }
    }

    function ensureGalleryIsVisible(objectId) {
        var sidebar = byId('sidebarGallery' + objectId);
        if (!sidebar) return;
        sidebar.classList.add('active', 'is-open');
        sidebar.style.visibility = 'visible';
        sidebar.style.opacity = '1';
        sidebar.style.pointerEvents = 'auto';
    }

    function refreshGoogleMapPanel(objectId) {
        var sourceEl = getSourceElement(objectId);

        if (window.HouseScreenshotGallery && window.HouseScreenshotGallery.current) {
            window.HouseScreenshotGallery.current.alternativeId = String(objectId);
            if (sourceEl && sourceEl.dataset) {
                window.HouseScreenshotGallery.current.customerId = sourceEl.dataset.customerId || window.HouseScreenshotGallery.current.customerId || '';
                window.HouseScreenshotGallery.current.address = sourceEl.dataset.address || window.HouseScreenshotGallery.current.address || '';
                window.HouseScreenshotGallery.current.sourceEl = sourceEl;
            }
        }

        if (typeof window.initSidebarObjectMap === 'function') {
            setTimeout(function () {
                window.initSidebarObjectMap(objectId, sourceEl);
            }, 60);
        }

        if (typeof window.refreshHousePreview === 'function') {
            setTimeout(function () {
                window.refreshHousePreview(objectId);
            }, 220);
        }

        if (window.google && google.maps && window.MayarObjectMaps && window.MayarObjectMaps.sidebars) {
            setTimeout(function () {
                var map = window.MayarObjectMaps.sidebars[objectId];
                if (map) {
                    google.maps.event.trigger(map, 'resize');
                    var center = map.getCenter && map.getCenter();
                    if (center) map.setCenter(center);
                }
            }, 420);
        }
    }

    window.houseShotSwitchTab = function (objectId, tabName) {
        objectId = String(objectId || '');
        tabName = String(tabName || 'saved');

        var root = document.querySelector('[data-house-shot-root="' + objectId.replace(/"/g, '\\"') + '"]');
        if (!root) return;

        root.querySelectorAll('[data-house-shot-tab-btn]').forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-house-shot-tab-btn') === tabName);
        });

        root.querySelectorAll('[data-house-shot-panel]').forEach(function (panel) {
            var isActive = panel.getAttribute('data-house-shot-panel') === tabName;
            panel.classList.toggle('is-active', isActive);
            panel.style.display = isActive ? 'block' : 'none';
        });

        if (tabName === 'google') {
            refreshGoogleMapPanel(objectId);
        }

        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    };

    window.addEventListener('click', function (event) {
        var tabBtn = event.target && event.target.closest ? event.target.closest('.house-shot-tab-btn[data-house-shot-tab-btn]') : null;
        if (!tabBtn) return;

        var root = getRootFromButton(tabBtn);
        if (!root) return;

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }

        var objectId = getObjectId(root);
        var tabName = tabBtn.getAttribute('data-house-shot-tab-btn') || 'saved';

        window.houseShotSwitchTab(objectId, tabName);
    }, true);

    window.addEventListener('change', function (event) {
        var el = event.target;
        if (!el || !el.id) return;

        if (el.id.indexOf('screenshotMode') === 0 || el.id.indexOf('screenshotZoom') === 0) {
            var objectId = el.id.replace('screenshotMode', '').replace('screenshotZoom', '');
            refreshGoogleMapPanel(objectId);
        }
    }, true);
})(window, document);
</script>


<script>
/* =========================================================
   FINAL HARD FIX: Screenshot save button must never open gallery sidebar
   - profile.blade.php is included as partial, so old document click handlers can remain active.
   - This capture handler owns only [data-house-shot-save].
   - It stops bubbling before broad .object-thumb-link / gallery open handlers can catch the click.
========================================================= */
(function (window, document) {
    'use strict';

    if (window.__MA_HOUSE_SCREENSHOT_SAVE_GUARD__) {
        return;
    }
    window.__MA_HOUSE_SCREENSHOT_SAVE_GUARD__ = true;

    function getSaveButton(target) {
        return target && target.closest ? target.closest('[data-house-shot-save], .ma-house-shot-save-btn') : null;
    }

    window.addEventListener('click', function (event) {
        var btn = getSaveButton(event.target);
        if (!btn) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }

        var customerId = btn.getAttribute('data-customer-id') || '';
        var alternativeId = btn.getAttribute('data-alternative-id') || '';

        if (!customerId || !alternativeId) {
            var inline = btn.getAttribute('onclick') || '';
            var matches = inline.match(/triggerScreenshot\('([^']+)'\s*,\s*'([^']+)'/);
            if (matches) {
                customerId = matches[1];
                alternativeId = matches[2];
            }
        }

        if (typeof window.triggerScreenshot === 'function') {
            window.triggerScreenshot(customerId, alternativeId, event);
        }

        return false;
    }, true);
})(window, document);
</script>



<script>
/* =========================================================
   FINAL FIX: Google screenshot sidebar close + note context
   Problem 1:
   - Google screenshot drawer/sidebar could open but not close reliably because
     older handlers and inline state only removed one class.
   Problem 2:
   - New note composer showed "Kunde oder Alternative fehlt" because #note-list
     sometimes lost data-customer-id / data-alternative-id after AJAX partial loads.
========================================================= */
(function (window, document) {
    'use strict';

    if (window.__MA_PROFILE_GOOGLE_SIDEBAR_NOTE_FIX__) {
        return;
    }
    window.__MA_PROFILE_GOOGLE_SIDEBAR_NOTE_FIX__ = true;

    function qs(selector, root) {
        return (root || document).querySelector(selector);
    }

    function qsa(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function safeCssValue(value) {
        return String(value || '').replace(/"/g, '\\"');
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            window.csrf ||
            '';
    }

    function showError(title, message) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire(title || 'Fehler', message || 'Ein Fehler ist aufgetreten.', 'error');
            return;
        }

        alert((title ? title + ': ' : '') + (message || 'Ein Fehler ist aufgetreten.'));
    }

    function getFirstAlternativeId() {
        var dashboardBtn = qs('.dashboard-btn[data-alternative-id]');
        if (dashboardBtn && dashboardBtn.dataset.alternativeId) {
            return dashboardBtn.dataset.alternativeId;
        }

        var firstObject = qs('.object-thumb-link[data-alternative-id], [data-object-alternative-id], [data-alternative-id]');
        if (firstObject && firstObject.dataset.alternativeId) {
            return firstObject.dataset.alternativeId;
        }

        if (firstObject && firstObject.dataset.objectAlternativeId) {
            return firstObject.dataset.objectAlternativeId;
        }

        return '';
    }

    function getCurrentNoteContext() {
        var noteList = document.getElementById('note-list');
        var activeProject = qs('.project-link.active[data-object-customer-id], .project-link.active[data-customer-id]');
        var activeObject = qs('.object-thumb-link.active[data-customer-id], .object-thumb-link[data-customer-id]');
        var dashboardBtn = qs('.dashboard-btn[data-customer-id]');
        var activeSubNav = qs('.nav-section-btn.active[data-customer-id], .ma-sub-nav-btn.active[data-customer-id]');

        var customerId =
            noteList?.dataset?.customerId ||
            activeSubNav?.dataset?.customerId ||
            activeProject?.dataset?.objectCustomerId ||
            activeProject?.dataset?.customerId ||
            activeObject?.dataset?.customerId ||
            dashboardBtn?.dataset?.customerId ||
            '';

        var alternativeId =
            noteList?.dataset?.alternativeId ||
            activeSubNav?.dataset?.alternativeId ||
            activeProject?.dataset?.objectAlternativeId ||
            activeProject?.dataset?.alternativeId ||
            activeObject?.dataset?.alternativeId ||
            dashboardBtn?.dataset?.alternativeId ||
            getFirstAlternativeId() ||
            '';

        var uniqueId =
            noteList?.dataset?.uniqueId ||
            activeProject?.dataset?.plId ||
            activeProject?.dataset?.leadProductListId ||
            '';

        var genericId =
            noteList?.dataset?.genericId ||
            noteList?.dataset?.productId ||
            activeProject?.dataset?.objectProduct ||
            activeProject?.dataset?.productId ||
            activeSubNav?.dataset?.productId ||
            '';

        var noteType =
            noteList?.dataset?.noteType ||
            (uniqueId || genericId ? 'product' : 'general');

        return {
            customerId: String(customerId || ''),
            alternativeId: String(alternativeId || ''),
            uniqueId: String(uniqueId || ''),
            genericId: String(genericId || ''),
            noteType: String(noteType || 'general')
        };
    }

    window.maEnsureNoteContext = function () {
        var noteList = document.getElementById('note-list');
        if (!noteList) return null;

        var ctx = getCurrentNoteContext();

        if (ctx.customerId) noteList.dataset.customerId = ctx.customerId;
        if (ctx.alternativeId) noteList.dataset.alternativeId = ctx.alternativeId;

        noteList.dataset.uniqueId = ctx.uniqueId || '';
        noteList.dataset.genericId = ctx.genericId || '';
        noteList.dataset.productId = ctx.genericId || '';
        noteList.dataset.noteType = ctx.noteType || 'general';

        var dashboardBtn = qs('.dashboard-btn');
        if (dashboardBtn) {
            if (ctx.customerId && !dashboardBtn.dataset.customerId) {
                dashboardBtn.dataset.customerId = ctx.customerId;
            }
            if (ctx.alternativeId && !dashboardBtn.dataset.alternativeId) {
                dashboardBtn.dataset.alternativeId = ctx.alternativeId;
            }
        }

        return ctx;
    };

    /* ---------------------------------------------------------
       Robust Google screenshot sidebar close
    --------------------------------------------------------- */
    function closeMapStreetView(alternativeId) {
        try {
            var map = window.MayarObjectMaps?.sidebars?.[alternativeId] ||
                window.googleMapsInstances?.[alternativeId];

            if (map && map.getStreetView) {
                map.getStreetView().setVisible(false);
            }
        } catch (error) {
            // no-op
        }
    }

    window.closeSidebarGallery = function (alternativeId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
            if (typeof event.stopImmediatePropagation === 'function') {
                event.stopImmediatePropagation();
            }
        }

        alternativeId = String(alternativeId || '');

        var sidebar = document.getElementById('sidebarGallery' + alternativeId);
        if (!sidebar) {
            sidebar = qs('[data-gallery-sidebar].active, [data-gallery-sidebar].is-open, .sidebar-gallery.active, .sidebar-gallery.is-open');
        }

        if (!sidebar) return false;

        sidebar.classList.remove('active', 'is-open', 'show');
        sidebar.setAttribute('aria-hidden', 'true');
        sidebar.style.right = '';
        sidebar.style.visibility = '';
        sidebar.style.opacity = '';
        sidebar.style.pointerEvents = '';
        sidebar.style.transform = '';

        var objectId = alternativeId || sidebar.getAttribute('data-house-shot-root') || '';
        closeMapStreetView(objectId);

        if (window.MayarObjectMaps?.observers?.[objectId]) {
            try {
                window.MayarObjectMaps.observers[objectId].disconnect();
            } catch (error) {
                // no-op
            }
            delete window.MayarObjectMaps.observers[objectId];
        }

        document.body.classList.remove('house-shot-sidebar-open', 'screenshot-sidebar-open');

        return false;
    };

    window.openSidebarGallery = function (elOrAlternativeId) {
        var alternativeId = '';
        var customerId = '';
        var address = '';
        var sourceEl = null;

        if (elOrAlternativeId && typeof elOrAlternativeId === 'object' && elOrAlternativeId.dataset) {
            sourceEl = elOrAlternativeId;
            alternativeId = sourceEl.dataset.alternativeId || '';
            customerId = sourceEl.dataset.customerId || '';
            address = sourceEl.dataset.address || '';
        } else {
            alternativeId = String(elOrAlternativeId || '');
            sourceEl = qs('.object-thumb-link[data-alternative-id="' + safeCssValue(alternativeId) + '"]');
            customerId = sourceEl?.dataset?.customerId || qs('.dashboard-btn')?.dataset?.customerId || '';
            address = sourceEl?.dataset?.address || '';
        }

        if (!alternativeId) return false;

        var sidebar = document.getElementById('sidebarGallery' + alternativeId);
        if (!sidebar) return false;

        if (window.HouseScreenshotGallery && window.HouseScreenshotGallery.current) {
            window.HouseScreenshotGallery.current.customerId = customerId || window.HouseScreenshotGallery.current.customerId || '';
            window.HouseScreenshotGallery.current.alternativeId = alternativeId;
            window.HouseScreenshotGallery.current.address = address || window.HouseScreenshotGallery.current.address || '';
            window.HouseScreenshotGallery.current.sourceEl = sourceEl || window.HouseScreenshotGallery.current.sourceEl || null;
        }

        sidebar.classList.add('active', 'is-open');
        sidebar.removeAttribute('aria-hidden');
        sidebar.style.visibility = 'visible';
        sidebar.style.opacity = '1';
        sidebar.style.pointerEvents = 'auto';

        document.body.classList.add('house-shot-sidebar-open');

        var label = document.getElementById('galleryAddress' + alternativeId);
        if (label && address) {
            label.textContent = address;
        }

        if (typeof window.loadSidebarGallery === 'function') {
            window.loadSidebarGallery(customerId, alternativeId);
        }

        setTimeout(function () {
            if (typeof window.initSidebarObjectMap === 'function') {
                window.initSidebarObjectMap(alternativeId, sourceEl);
            }
        }, 120);

        return false;
    };

    window.addEventListener('click', function (event) {
        var closeBtn = event.target && event.target.closest
            ? event.target.closest('[data-gallery-close], .ma-gallery-close, .sidebar-gallery .sidebar-header button')
            : null;

        if (!closeBtn) return;

        var sidebar = closeBtn.closest('[data-gallery-sidebar], .sidebar-gallery');
        if (!sidebar) return;

        var alternativeId = sidebar.getAttribute('data-house-shot-root') ||
            (sidebar.id || '').replace('sidebarGallery', '');

        window.closeSidebarGallery(alternativeId, event);
    }, true);

    window.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        var openSidebar = qs('[data-gallery-sidebar].active, [data-gallery-sidebar].is-open, .sidebar-gallery.active, .sidebar-gallery.is-open');
        if (!openSidebar) return;

        var alternativeId = openSidebar.getAttribute('data-house-shot-root') ||
            (openSidebar.id || '').replace('sidebarGallery', '');

        window.closeSidebarGallery(alternativeId, event);
    }, true);

    /* ---------------------------------------------------------
       Note context + safe submit override
    --------------------------------------------------------- */
    var oldToggleNewNoteArea = window.toggleNewNoteArea;
    window.toggleNewNoteArea = function (event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        window.maEnsureNoteContext();

        if (typeof oldToggleNewNoteArea === 'function') {
            return oldToggleNewNoteArea(event);
        }

        var composer = document.getElementById('newNoteComposer');
        var backdrop = document.getElementById('noteBackdrop');

        if (!composer || !backdrop) return false;

        var isOpen = composer.classList.contains('open');
        if (isOpen && typeof window.closeComposer === 'function') {
            window.closeComposer();
            return false;
        }

        composer.classList.add('open');
        backdrop.style.display = 'block';
        setTimeout(function () {
            document.getElementById('newNoteText')?.focus();
        }, 80);

        return false;
    };

    window.submitNote = async function () {
        var input = document.getElementById('newNoteText');
        var container = document.getElementById('note-list');
        var btn = qs('#newNoteComposer button[onclick*="submitNote"], #newNoteComposer button[type="button"], #newNoteComposer button');

        var text = (input?.value || '').trim();

        if (!text) {
            if (window.Swal) {
                return window.Swal.fire('Hinweis', 'Bitte eine Notiz eingeben.', 'warning');
            }
            return alert('Bitte eine Notiz eingeben.');
        }

        if (!container) {
            return showError('Fehler', 'Notizbereich wurde nicht gefunden.');
        }

        var ctx = window.maEnsureNoteContext();

        if (!ctx || !ctx.customerId || !ctx.alternativeId) {
            return showError('Fehler', 'Kunde oder Alternative fehlt. Bitte zuerst Dashboard oder ein Produkt im linken Menü öffnen.');
        }

        var oldHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Senden...';
        }

        var payload = {
            customer_id: ctx.customerId,
            alternative_id: ctx.alternativeId,
            lead_product_list_id: ctx.uniqueId || null,
            product_id: ctx.genericId || null,
            type: ctx.noteType || 'general',
            description: text,
            priority: 'normal',
            color: '#cfe09b'
        };

        try {
            var response = await fetch('/customer-notes/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });

            var data = await response.json().catch(function () {
                return {};
            });

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Notiz konnte nicht gespeichert werden.');
            }

            if (data.html) {
                container.insertAdjacentHTML('afterbegin', data.html);
            } else if (typeof window.showDashboard === 'function' && payload.type === 'general') {
                window.showDashboard(qs('.dashboard-btn'));
            }

            input.value = '';

            if (typeof window.closeComposer === 'function') {
                window.closeComposer();
            }

            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }

            if (typeof window.initNoteListeners === 'function') {
                window.initNoteListeners();
            }

        } catch (error) {
            console.error('Submit Note Error:', error);
            showError('Fehler', error.message || 'Notiz konnte nicht gespeichert werden.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldHtml || '<i class="feather icon-send me-1"></i> Senden';
            }
        }

        return false;
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.maEnsureNoteContext();

        qsa('.dashboard-btn[data-customer-id]').forEach(function (btn) {
            if (!btn.dataset.alternativeId) {
                var firstAlternativeId = getFirstAlternativeId();
                if (firstAlternativeId) {
                    btn.dataset.alternativeId = firstAlternativeId;
                }
            }
        });

        qsa('[onclick*="closeSidebarGallery"]').forEach(function (btn) {
            btn.setAttribute('data-gallery-close', '1');
        });
    });
})(window, document);
</script>


{{-- =========================================================
   FINAL PATCH: Google map inside screenshot sidebar
   Fixes hidden/blank Google map when opening the Google tab.
   Safe for partial/dynamic script execution.
========================================================= --}}
<style>
    .sidebar-gallery .house-shot-panel[data-house-shot-panel="google"].is-active,
    .sidebar-gallery .house-shot-panel[data-house-shot-panel="google"][style*="block"] {
        display: block !important;
    }

    .sidebar-gallery .google-map,
    .sidebar-gallery [id^="mapContainer"] {
        display: block !important;
        width: 100% !important;
        max-width: 760px !important;
        height: 520px !important;
        min-height: 520px !important;
        border-radius: 14px !important;
        overflow: hidden !important;
        background: #f8fbfd !important;
    }

    .sidebar-gallery .gm-style,
    .sidebar-gallery .gm-style > div {
        max-width: none !important;
    }

    @media (max-width: 768px) {
        .sidebar-gallery .google-map,
        .sidebar-gallery [id^="mapContainer"] {
            height: 65vh !important;
            min-height: 65vh !important;
            max-width: 100% !important;
        }
    }
</style>

<script>
(function () {
    'use strict';

    const d = document;
    const w = window;

    function id(value) {
        return String(value || '').trim();
    }

    function hasGoogleMaps() {
        return !!(w.google && w.google.maps && typeof w.google.maps.Map === 'function');
    }

    function mapContainer(objectId) {
        return d.getElementById('mapContainer' + objectId);
    }

    function root(objectId) {
        return d.querySelector('[data-house-shot-root="' + objectId + '"]');
    }

    function sidebar(objectId) {
        return d.getElementById('sidebarGallery' + objectId);
    }

    function cleanAddress(value) {
        if (typeof w.maCleanAddress === 'function') {
            return w.maCleanAddress(value);
        }

        return String(value || '').replace(/\s+/g, ' ').replace(/^,+|,+$/g, '').trim();
    }

    function getCustomerId(objectId) {
        const saveBtn = d.querySelector('[data-house-shot-save][data-alternative-id="' + objectId + '"]');
        return id(saveBtn && saveBtn.dataset.customerId);
    }

    function getAddress(objectId) {
        const label = d.getElementById('galleryAddress' + objectId);
        return cleanAddress(label ? label.textContent : '');
    }

    function forceSize(objectId) {
        const el = mapContainer(objectId);
        if (!el) return null;

        el.style.display = 'block';
        el.style.width = '100%';
        el.style.maxWidth = '760px';
        el.style.height = window.innerWidth <= 768 ? '65vh' : '520px';
        el.style.minHeight = window.innerWidth <= 768 ? '65vh' : '520px';
        el.style.visibility = 'visible';

        return el;
    }

    function ensureState(objectId) {
        w.HouseScreenshotGallery = w.HouseScreenshotGallery || {};
        w.HouseScreenshotGallery.current = w.HouseScreenshotGallery.current || {};

        const state = w.HouseScreenshotGallery.current;

        if (id(state.alternativeId) !== id(objectId)) {
            state.alternativeId = id(objectId);
            state.customerId = getCustomerId(objectId) || state.customerId || null;
            state.address = getAddress(objectId) || state.address || '';
            state.center = state.center || null;
        }

        const modeSelect = d.getElementById('screenshotMode' + objectId);
        const zoomSelect = d.getElementById('screenshotZoom' + objectId);

        state.mode = modeSelect ? modeSelect.value : (state.mode || 'satellite');
        state.zoom = parseInt(zoomSelect ? zoomSelect.value : (state.zoom || 20), 10) || 20;
        state.imageUrl = state.imageUrl || '';

        return state;
    }

    function showGooglePanel(objectId) {
        const r = root(objectId);
        if (!r) return;

        r.querySelectorAll('[data-house-shot-tab-btn]').forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-house-shot-tab-btn') === 'google');
        });

        r.querySelectorAll('[data-house-shot-panel]').forEach(function (panel) {
            const active = panel.getAttribute('data-house-shot-panel') === 'google';
            panel.classList.toggle('is-active', active);
            panel.style.display = active ? 'block' : 'none';
        });
    }

    function resizeExistingMap(objectId) {
        const state = ensureState(objectId);
        const map = w.MayarObjectMaps && w.MayarObjectMaps.sidebars
            ? w.MayarObjectMaps.sidebars[objectId]
            : null;

        if (!map || !hasGoogleMaps()) return false;

        forceSize(objectId);

        [40, 120, 260, 500, 900].forEach(function (delay) {
            setTimeout(function () {
                try {
                    google.maps.event.trigger(map, 'resize');
                    if (state.center) {
                        map.setCenter(state.center);
                    }
                    if (typeof w.applySidebarMapMode === 'function') {
                        w.applySidebarMapMode(objectId);
                    }
                } catch (e) {}
            }, delay);
        });

        return true;
    }

    w.ensureHouseScreenshotGoogleMap = function (objectId, sourceEl) {
        objectId = id(objectId);
        if (!objectId) return;

        const side = sidebar(objectId);
        if (side) {
            side.classList.add('active');
        }

        showGooglePanel(objectId);
        ensureState(objectId);
        const mapEl = forceSize(objectId);

        if (!mapEl) return;

        if (!hasGoogleMaps()) {
            mapEl.innerHTML = '<div class="text-muted p-3 text-center">Google Maps wurde noch nicht geladen. Bitte Seite neu laden oder Google-Key prüfen.</div>';
            return;
        }

        if (resizeExistingMap(objectId)) return;

        if (typeof w.initSidebarObjectMap === 'function') {
            mapEl.innerHTML = '<div class="text-muted p-3 text-center">Karte wird geladen...</div>';

            setTimeout(function () {
                try {
                    w.initSidebarObjectMap(objectId, sourceEl || mapEl);
                } catch (e) {
                    mapEl.innerHTML = '<div class="text-muted p-3 text-center">Karte konnte nicht geladen werden.</div>';
                    console.error('initSidebarObjectMap failed:', e);
                }
            }, 80);
        }
    };

    const oldRefresh = typeof w.refreshHousePreview === 'function' ? w.refreshHousePreview : null;
    w.refreshHousePreview = function (objectId) {
        objectId = id(objectId);
        if (!objectId) return;

        ensureState(objectId);
        forceSize(objectId);

        const hasMap = !!(w.MayarObjectMaps && w.MayarObjectMaps.sidebars && w.MayarObjectMaps.sidebars[objectId]);

        if (!hasMap) {
            w.ensureHouseScreenshotGoogleMap(objectId);
            return;
        }

        if (typeof w.applySidebarMapMode === 'function') {
            w.applySidebarMapMode(objectId);
        } else if (oldRefresh) {
            oldRefresh(objectId);
        }

        resizeExistingMap(objectId);
    };

    w.houseShotSwitchTab = function (objectId, tabName) {
        objectId = id(objectId);
        tabName = id(tabName || 'saved');

        const r = root(objectId);
        if (!r) return;

        r.querySelectorAll('[data-house-shot-tab-btn]').forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-house-shot-tab-btn') === tabName);
        });

        r.querySelectorAll('[data-house-shot-panel]').forEach(function (panel) {
            const active = panel.getAttribute('data-house-shot-panel') === tabName;
            panel.classList.toggle('is-active', active);
            panel.style.display = active ? 'block' : 'none';
        });

        if (tabName === 'google') {
            w.ensureHouseScreenshotGoogleMap(objectId);
        }
    };

    d.addEventListener('click', function (event) {
        const tabBtn = event.target.closest('[data-house-shot-tab-btn]');
        if (!tabBtn) return;

        const r = tabBtn.closest('[data-house-shot-root]');
        if (!r) return;

        event.preventDefault();
        event.stopPropagation();
        if (event.stopImmediatePropagation) event.stopImmediatePropagation();

        w.houseShotSwitchTab(r.getAttribute('data-house-shot-root'), tabBtn.getAttribute('data-house-shot-tab-btn'));
    }, true);

    const oldOpen = typeof w.openObjectMapSidebar === 'function' ? w.openObjectMapSidebar : null;
    w.openObjectMapSidebar = function (el) {
        if (oldOpen) {
            oldOpen(el);
        }

        const objectId = id(el && el.dataset ? el.dataset.alternativeId : '');
        if (!objectId) return;

        const r = root(objectId);
        const googleActive = r && r.querySelector('[data-house-shot-panel="google"].is-active');

        if (googleActive) {
            setTimeout(function () {
                w.ensureHouseScreenshotGoogleMap(objectId, el);
            }, 180);
        }
    };

    const oldClose = typeof w.closeSidebarGallery === 'function' ? w.closeSidebarGallery : null;
    w.closeSidebarGallery = function (objectId, eventObject) {
        if (eventObject) {
            eventObject.preventDefault();
            eventObject.stopPropagation();
            if (eventObject.stopImmediatePropagation) eventObject.stopImmediatePropagation();
        }

        const side = sidebar(objectId);
        if (side) {
            side.classList.remove('active');
        }

        if (oldClose) {
            try { oldClose(objectId); } catch (e) {}
        }

        return false;
    };
})();
</script>

<script>
// customer-context-feed.js

(function (window, document) {
    'use strict';

    if (window.__MA_CONTEXT_FEED_SWITCHER_FIXED__) {
        return;
    }

    window.__MA_CONTEXT_FEED_SWITCHER_FIXED__ = true;

    function qs(selector, root) {
        return (root || document).querySelector(selector);
    }

    function qsa(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function csrf() {
        return qs('meta[name="csrf-token"]')?.getAttribute('content') || window.csrf || '';
    }

    function getSwitcher() {
        return qs('#maNoteTypeSwitcher');
    }

    function getNoteList() {
        return qs('#note-list');
    }

    function getCtx() {
        if (typeof window.maEnsureNoteContext === 'function') {
            var ensured = window.maEnsureNoteContext();
            if (ensured) {
                return ensured;
            }
        }

        var noteList = getNoteList();

        return {
            customerId: noteList?.dataset?.customerId || '',
            alternativeId: noteList?.dataset?.alternativeId || '',
            productId: noteList?.dataset?.productId || noteList?.dataset?.genericId || '',
            genericId: noteList?.dataset?.genericId || noteList?.dataset?.productId || '',
            uniqueId: noteList?.dataset?.uniqueId || '',
            leadProductListId: noteList?.dataset?.uniqueId || '',
            noteType: noteList?.dataset?.noteType || 'general'
        };
    }

    function refreshIconsAndNotes() {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }

        if (typeof window.initNoteListeners === 'function') {
            window.initNoteListeners();
        }
    }

    function setLoading(text) {
        var noteList = getNoteList();

        if (!noteList) {
            return;
        }

        noteList.innerHTML =
            '<div class="ma-feed-empty">' +
                '<div class="d-flex align-items-center">' +
                    '<span class="ma-note-type-icon bg-blue mr-2">' +
                        '<span class="spinner-border spinner-border-sm"></span>' +
                    '</span>' +
                    '<div>' +
                        '<div class="ma-feed-title">Wird geladen</div>' +
                        '<div class="ma-feed-meta">' + (text || 'Bitte warten...') + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function setError(message) {
        var noteList = getNoteList();

        if (!noteList) {
            return;
        }

        noteList.innerHTML =
            '<div class="ma-feed-empty">' +
                '<div class="d-flex align-items-center">' +
                    '<span class="ma-note-type-icon bg-pink mr-2">' +
                        '<i data-feather="alert-triangle"></i>' +
                    '</span>' +
                    '<div>' +
                        '<div class="ma-feed-title">Fehler</div>' +
                        '<div class="ma-feed-meta">' + (message || 'Bereich konnte nicht geladen werden.') + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        refreshIconsAndNotes();
    }

    function cleanLoadedHtml(html) {
        var wrapper = document.createElement('div');
        wrapper.innerHTML = html || '';

        qsa('#maNoteTypeSwitcher, .ma-note-type-switcher, [data-note-feed-menu], [data-note-feed-current]', wrapper)
            .forEach(function (el) {
                var parentSwitcher = el.closest('.ma-note-type-switcher');
                if (parentSwitcher) {
                    parentSwitcher.remove();
                } else {
                    el.remove();
                }
            });

        qsa('#note_title', wrapper).forEach(function (el) {
            el.remove();
        });

        return wrapper.innerHTML;
    }

    function updateSwitcherTitle(item) {
        var switcher = getSwitcher();

        if (!switcher || !item) {
            return;
        }

        var current = qs('[data-note-feed-current]', switcher);

        if (!current) {
            return;
        }

        var label = item.dataset.label || 'Aktuelle Notizen';
        var subtitle = item.dataset.subtitle || 'Gefilterter Kundenbereich';
        var icon = item.dataset.icon || 'message-square';
        var color = item.dataset.color || 'blue';

        current.innerHTML =
            '<span class="ma-note-type-icon bg-' + color + '">' +
                '<i data-feather="' + icon + '"></i>' +
            '</span>' +
            '<span class="ma-note-type-text">' +
                '<strong>' + label + '</strong>' +
                '<small>' + subtitle + '</small>' +
            '</span>' +
            '<i data-feather="chevron-down" class="ma-note-type-chevron"></i>';

        refreshIconsAndNotes();
    }

    window.maLoadContextFeed = async function (type) {
        var noteList = getNoteList();

        if (!noteList) {
            return;
        }

        var ctx = getCtx();

        if (!ctx || !ctx.customerId) {
            setError('Kunde fehlt. Bitte zuerst Kunde oder Objekt öffnen.');
            return;
        }

        noteList.dataset.feedType = type;
        document.body.classList.toggle('ma-context-feed-active', type !== 'notes');

        if (type === 'notes') {
            var notesUrl =
                '/customer-notes/context/' +
                encodeURIComponent(ctx.customerId || 0) + '/' +
                encodeURIComponent(ctx.alternativeId || 0) + '/' +
                encodeURIComponent(ctx.genericId || ctx.productId || 0) + '/' +
                encodeURIComponent(ctx.uniqueId || ctx.leadProductListId || '');

            setLoading('Lade Notizen...');

            try {
                var notesRes = await fetch(notesUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!notesRes.ok) {
                    throw new Error('Notizen konnten nicht geladen werden.');
                }

                var notesHtml = await notesRes.text();

                noteList.innerHTML = cleanLoadedHtml(notesHtml);
                noteList.dataset.feedType = 'notes';

                refreshIconsAndNotes();
            } catch (error) {
                console.error(error);
                setError(error.message || 'Notizen konnten nicht geladen werden.');
            }

            return;
        }

        var params = new URLSearchParams({
            customer_id: ctx.customerId || '',
            alternative_id: ctx.alternativeId || '',
            product_id: ctx.genericId || ctx.productId || '',
            lead_product_list_id: ctx.uniqueId || ctx.leadProductListId || ''
        });

        setLoading('Lade Bereich...');

        try {
            var res = await fetch('/customer-context-feed/' + encodeURIComponent(type) + '?' + params.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!res.ok) {
                throw new Error('Bereich konnte nicht geladen werden.');
            }

            var html = await res.text();

            noteList.innerHTML = cleanLoadedHtml(html);
            noteList.dataset.feedType = type;

            refreshIconsAndNotes();
        } catch (error) {
            console.error(error);
            setError(error.message || 'Dieser Bereich konnte nicht geladen werden.');
        }
    };

    document.addEventListener('click', function (event) {
        var switcher = getSwitcher();

        var switcherButton = switcher && event.target.closest('#maNoteTypeSwitcher [data-note-feed-current]');

        if (switcherButton) {
            event.preventDefault();
            event.stopPropagation();

            switcher.classList.toggle('is-open');
            return;
        }

        var feedItem = switcher && event.target.closest('#maNoteTypeSwitcher [data-feed-type]');

        if (feedItem) {
            event.preventDefault();
            event.stopPropagation();

            qsa('#maNoteTypeSwitcher .ma-note-type-item').forEach(function (btn) {
                btn.classList.remove('active');
            });

            feedItem.classList.add('active');
            switcher.classList.remove('is-open');

            updateSwitcherTitle(feedItem);

            window.maLoadContextFeed(feedItem.dataset.feedType || 'notes');
            return;
        }

        var collapseBtn = event.target.closest('#note-list [data-feed-collapse]');

        if (collapseBtn) {
            event.preventDefault();
            event.stopPropagation();

            var card = collapseBtn.closest('.ma-feed-card');

            if (card) {
                card.classList.toggle('is-open');
            }

            return;
        }

        if (switcher && !event.target.closest('#maNoteTypeSwitcher')) {
            switcher.classList.remove('is-open');
        }
    }, false);

    document.addEventListener('submit', async function (event) {
        var form = event.target.closest('#note-list .ma-context-form');

        if (!form) {
            return;
        }

        event.preventDefault();

        var url = form.dataset.contextPost;

        if (!url) {
            return;
        }

        var btn = form.querySelector('button[type="submit"]');
        var oldHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }

        try {
            var res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form),
                credentials: 'same-origin'
            });

            var data = await res.json().catch(function () {
                return {};
            });

            if (!res.ok || data.success === false) {
                throw new Error(data.message || 'Speichern fehlgeschlagen.');
            }

            var activeType = getNoteList()?.dataset?.feedType || 'notes';
            await window.maLoadContextFeed(activeType);
        } catch (error) {
            console.error(error);

            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire('Fehler', error.message || 'Konnte nicht gespeichert werden.', 'error');
            } else {
                alert(error.message || 'Konnte nicht gespeichert werden.');
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        }
    });

})(window, document);

</script>

<script>
/* =========================================================
   FINAL OBJECT CONTEXT MENU + SCREENSHOT LEFT-CLICK DISABLE
   Put this after all old profile scripts.
========================================================= */
(function (window, document) {
    'use strict';

    if (window.__MA_OBJECT_CONTEXT_MENU_FINAL_BLADE_FIX__) {
        return;
    }
    window.__MA_OBJECT_CONTEXT_MENU_FINAL_BLADE_FIX__ = true;

    function csrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function safeCss(value) {
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(String(value));
        }
        return String(value).replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1');
    }

    function toast(type, title, text) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire(title || '', text || '', type || 'info');
        }
        if (text || title) {
            alert((title ? title + '\n' : '') + (text || ''));
        }
    }

    function confirmDelete() {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire({
                title: 'Objekt löschen?',
                text: 'Dieses Objekt wird gelöscht. Diese Aktion kann nicht einfach rückgängig gemacht werden.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#e50656'
            }).then(function (result) {
                return !!result.isConfirmed;
            });
        }
        return Promise.resolve(window.confirm('Objekt wirklich löschen?'));
    }

    function closestObjectSection(target) {
        if (!target || !target.closest) return null;
        return target.closest('[data-object-section], .object-section');
    }

    function getThumb(section) {
        if (!section) return null;
        return section.querySelector('.object-thumb-link[data-alternative-id]');
    }

    function getObjectData(section) {
        var thumb = getThumb(section);
        var customerId = section?.dataset?.customerId || thumb?.dataset?.customerId || document.querySelector('.dashboard-btn[data-customer-id]')?.dataset?.customerId || '';
        var alternativeId = section?.dataset?.alternativeId || thumb?.dataset?.alternativeId || '';
        return {
            section: section,
            thumb: thumb,
            customerId: String(customerId || ''),
            alternativeId: String(alternativeId || ''),
            editUrl: section?.dataset?.editUrl || (customerId && alternativeId ? ('/new_lead_edit/' + encodeURIComponent(customerId) + '/' + encodeURIComponent(alternativeId)) : ''),
            deleteUrl: section?.dataset?.deleteUrl || (alternativeId ? ('/lead/objects/' + encodeURIComponent(alternativeId)) : '')
        };
    }

    function removeScreenshotInlineHandlers(root) {
        (root || document).querySelectorAll('.object-thumb-link').forEach(function (link) {
            link.removeAttribute('onclick');
            link.dataset.screenshotClickDisabled = '1';
        });
    }

    function ensureMenu() {
        var menu = document.getElementById('maObjectContextMenu');
        if (menu) return menu;

        menu = document.createElement('div');
        menu.id = 'maObjectContextMenu';
        menu.className = 'ma-object-context-menu';
        menu.setAttribute('role', 'menu');
        menu.setAttribute('aria-hidden', 'true');
        menu.innerHTML = '' +
            '<button type="button" class="ma-object-context-item" data-object-context-action="screenshot">' +
                '<i data-feather="image"></i><span>Screenshot öffnen</span>' +
            '</button>' +
            '<button type="button" class="ma-object-context-item" data-object-context-action="edit">' +
                '<i data-feather="edit-2"></i><span>Objekt bearbeiten</span>' +
            '</button>' +
            '<button type="button" class="ma-object-context-item" data-object-context-action="add-product">' +
                '<i data-feather="plus-circle"></i><span>Neues Produkt</span>' +
            '</button>' +
            '<div class="ma-object-context-separator"></div>' +
            '<button type="button" class="ma-object-context-item is-danger" data-object-context-action="delete">' +
                '<i data-feather="trash-2"></i><span>Objekt löschen</span>' +
            '</button>';

        document.body.appendChild(menu);
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
        return menu;
    }

    function hideMenu() {
        var menu = document.getElementById('maObjectContextMenu');
        if (!menu) return;
        menu.classList.remove('is-open');
        menu.setAttribute('aria-hidden', 'true');
        menu.style.left = '-9999px';
        menu.style.top = '-9999px';
        delete menu.dataset.customerId;
        delete menu.dataset.alternativeId;
    }

    function showMenu(event, data) {
        var menu = ensureMenu();
        menu.dataset.customerId = data.customerId || '';
        menu.dataset.alternativeId = data.alternativeId || '';

        var screenshotBtn = menu.querySelector('[data-object-context-action="screenshot"]');
        if (screenshotBtn) {
            screenshotBtn.disabled = !data.thumb;
        }

        var addBtn = menu.querySelector('[data-object-context-action="add-product"]');
        if (addBtn) {
            addBtn.disabled = !(data.customerId && data.alternativeId);
        }

        var editBtn = menu.querySelector('[data-object-context-action="edit"]');
        if (editBtn) {
            editBtn.disabled = !(data.customerId && data.alternativeId);
        }

        var deleteBtn = menu.querySelector('[data-object-context-action="delete"]');
        if (deleteBtn) {
            deleteBtn.disabled = !data.alternativeId;
        }

        menu.classList.add('is-open');
        menu.setAttribute('aria-hidden', 'false');

        var x = event.clientX;
        var y = event.clientY;
        var rect = menu.getBoundingClientRect();
        var margin = 12;

        if (x + rect.width + margin > window.innerWidth) {
            x = window.innerWidth - rect.width - margin;
        }
        if (y + rect.height + margin > window.innerHeight) {
            y = window.innerHeight - rect.height - margin;
        }

        menu.style.left = Math.max(margin, x) + 'px';
        menu.style.top = Math.max(margin, y) + 'px';
    }

    function openScreenshot(customerId, alternativeId) {
        var thumb = null;
        try {
            thumb = document.querySelector('.object-thumb-link[data-alternative-id="' + safeCss(alternativeId) + '"]');
        } catch (error) {
            thumb = document.querySelector('.object-thumb-link[data-alternative-id="' + String(alternativeId).replace(/"/g, '\\"') + '"]');
        }

        if (!thumb) {
            return toast('warning', 'Nicht gefunden', 'Für dieses Objekt wurde kein Screenshot-Link gefunden.');
        }

        if (typeof window.openSidebarGallery === 'function') {
            window.openSidebarGallery(thumb);
        } else if (typeof window.openObjectMapSidebar === 'function') {
            window.openObjectMapSidebar(thumb);
        } else {
            toast('error', 'Fehler', 'Screenshot-Funktion wurde nicht gefunden.');
        }
    }

    function openEdit(customerId, alternativeId) {
        if (!customerId || !alternativeId) {
            return toast('warning', 'Fehlt', 'Kunde oder Objekt-ID fehlt.');
        }
        window.location.href = '/new_lead_edit/' + encodeURIComponent(customerId) + '/' + encodeURIComponent(alternativeId);
    }

    function openAddProduct(customerId, alternativeId) {
        if (!customerId || !alternativeId) {
            return toast('warning', 'Fehlt', 'Kunde oder Objekt-ID fehlt.');
        }

        var existing = document.querySelector('.addNewProduct[data-id="' + safeCss(customerId) + '"][data-alternative-id="' + safeCss(alternativeId) + '"]');
        if (existing) {
            existing.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
            return;
        }

        var btn = document.createElement('button');
        btn.className = 'kebab-item addNewProduct';
        btn.type = 'button';
        btn.dataset.id = customerId;
        btn.dataset.alternativeId = alternativeId;
        btn.style.position = 'fixed';
        btn.style.left = '-9999px';
        btn.style.top = '-9999px';
        btn.innerHTML = '<i class="feather icon-plus-circle text-success"></i> Neues Produkt';
        document.body.appendChild(btn);
        btn.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
        setTimeout(function () {
            btn.remove();
        }, 500);
    }

    async function deleteObject(customerId, alternativeId) {
        if (!alternativeId) {
            return toast('warning', 'Fehlt', 'Objekt-ID fehlt.');
        }

        var ok = await confirmDelete();
        if (!ok) return;

        var section = null;
        try {
            section = document.querySelector('[data-object-section][data-alternative-id="' + safeCss(alternativeId) + '"]');
        } catch (error) {
            section = null;
        }
        if (!section) {
            var thumb = document.querySelector('.object-thumb-link[data-alternative-id="' + safeCss(alternativeId) + '"]');
            section = closestObjectSection(thumb);
        }

        try {
            var res = await fetch('/lead/objects/' + encodeURIComponent(alternativeId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            var data = await res.json().catch(function () { return {}; });
            if (!res.ok || data.success === false) {
                throw new Error(data.message || 'Objekt konnte nicht gelöscht werden.');
            }

            var sidebar = document.getElementById('sidebarGallery' + alternativeId);
            if (sidebar) sidebar.remove();

            if (section) {
                section.style.transition = 'opacity .22s ease, transform .22s ease, max-height .28s ease';
                section.style.opacity = '0';
                section.style.transform = 'translateX(-14px)';
                section.style.maxHeight = section.scrollHeight + 'px';
                setTimeout(function () {
                    section.style.maxHeight = '0px';
                }, 20);
                setTimeout(function () {
                    section.remove();
                }, 320);
            }

            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({ icon: 'success', title: 'Gelöscht', timer: 1200, showConfirmButton: false });
            }
        } catch (error) {
            console.error(error);
            toast('error', 'Fehler', error.message || 'Objekt konnte nicht gelöscht werden.');
        }
    }

    /* Disable only left-click on screenshot thumbnail. Context menu can still open it. */
    window.addEventListener('click', function (event) {
        var thumb = event.target && event.target.closest ? event.target.closest('.object-thumb-link[data-alternative-id]') : null;
        if (!thumb) return;
        if (event.__maAllowScreenshotSidebar === true) return;

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }
        return false;
    }, true);

    document.addEventListener('contextmenu', function (event) {
        var section = closestObjectSection(event.target);
        if (!section) return;
        if (!section.closest('.customerSidebar')) return;

        event.preventDefault();
        event.stopPropagation();
        var data = getObjectData(section);
        if (!data.alternativeId) return;
        showMenu(event, data);
    }, true);

    document.addEventListener('click', function (event) {
        var menu = document.getElementById('maObjectContextMenu');
        var item = event.target.closest ? event.target.closest('#maObjectContextMenu [data-object-context-action]') : null;

        if (!item) {
            if (menu && !event.target.closest('#maObjectContextMenu')) {
                hideMenu();
            }
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }

        var customerId = menu?.dataset?.customerId || '';
        var alternativeId = menu?.dataset?.alternativeId || '';
        var action = item.dataset.objectContextAction;
        hideMenu();

        if (action === 'screenshot') return openScreenshot(customerId, alternativeId);
        if (action === 'edit') return openEdit(customerId, alternativeId);
        if (action === 'add-product') return openAddProduct(customerId, alternativeId);
        if (action === 'delete') return deleteObject(customerId, alternativeId);
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') hideMenu();
    });

    document.addEventListener('DOMContentLoaded', function () {
        removeScreenshotInlineHandlers(document);
        ensureMenu();
    });

    removeScreenshotInlineHandlers(document);
})(window, document);

</script>




<style>
/* =========================================================
   FINAL CUSTOMER SIDEBAR MASTER FIX
   - only one hidden scrollbar in the left sidebar
   - screenshot drawer is fixed and independent
   - object/product menus remain clickable
========================================================= */
#customerSidebar,
.customerSidebar {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
#customerSidebar::-webkit-scrollbar,
.customerSidebar::-webkit-scrollbar,
#customerSidebar *::-webkit-scrollbar,
.customerSidebar *::-webkit-scrollbar {
    width: 0 !important;
    height: 0 !important;
    display: none !important;
}
#customerSidebar .product-list,
#customerSidebar .ma-product-list,
#customerSidebar .sub-nav,
#customerSidebar .ma-sub-nav,
.customerSidebar .product-list,
.customerSidebar .ma-product-list,
.customerSidebar .sub-nav,
.customerSidebar .ma-sub-nav {
    max-height: none !important;
    overflow: visible !important;
    overflow-x: hidden !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
#customerSidebar .product-list.show,
#customerSidebar .ma-product-list.show,
#customerSidebar .sub-nav.show,
#customerSidebar .ma-sub-nav.show,
.customerSidebar .product-list.show,
.customerSidebar .ma-product-list.show,
.customerSidebar .sub-nav.show,
.customerSidebar .ma-sub-nav.show {
    display: block !important;
}
.sidebar-gallery[data-gallery-sidebar],
.sidebar-gallery {
    position: fixed !important;
    top: 0 !important;
    right: -110% !important;
    bottom: 0 !important;
    width: min(920px, 94vw) !important;
    max-width: 94vw !important;
    height: 100dvh !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    z-index: 2147483000 !important;
    pointer-events: none;
    visibility: hidden;
    opacity: 0;
    transition: right .25s ease, opacity .18s ease, visibility .18s ease;
}
.sidebar-gallery[data-gallery-sidebar].active,
.sidebar-gallery[data-gallery-sidebar].is-open,
.sidebar-gallery.active,
.sidebar-gallery.is-open {
    right: 0 !important;
    pointer-events: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
}
.house-shot-tabs,
.house-shot-upload-box,
.house-shot-panel,
.house-shot-tab-btn,
.ma-house-shot-save-btn,
.ma-gallery-close,
.object-thumb-link {
    position: relative;
    z-index: 2;
}
</style>

<script>
/* =========================================================
   FINAL CUSTOMER SIDEBAR MASTER FIX
   Fixes:
   1) screenshot thumbnail opens gallery, not object collapse
   2) upload / refresh / close / tab buttons keep their own action
   3) object header only toggles when clicking the real header area
   4) product cards still open/collapse normally
========================================================= */
(function (window, document) {
    'use strict';

    window.__MA_CUSTOMER_SIDEBAR_MASTER_FIX__ = true;

    function qs(selector, root) {
        return (root || document).querySelector(selector);
    }

    function qsa(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function safeCss(value) {
        value = String(value || '');
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return CSS.escape(value);
        }
        return value.replace(/"/g, '\\"');
    }

    function refreshIcons() {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    }

    function stopOnly(event) {
        if (!event) return;
        event.stopPropagation();
    }

    function stopFull(event) {
        if (!event) return;
        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }
    }

    function protectedTarget(event) {
        if (!event || !event.target || !event.target.closest) return null;
        return event.target.closest([
            'a',
            'button',
            'input',
            'select',
            'textarea',
            'label',
            '.sidebar-gallery',
            '[data-gallery-sidebar]',
            '.house-shot-upload-box',
            '.house-shot-tabs',
            '.house-shot-panel',
            '.house-shot-tab-btn',
            '.object-thumb-link',
            '.object-thumb-img',
            '.ma-gallery-close',
            '[data-gallery-close]',
            '[data-no-object-toggle]',
            '[data-no-menu-toggle]',
            '.price-edit-trigger',
            '.project-metric--calendar'
        ].join(','));
    }

    function currentState() {
        window.HouseScreenshotGallery = window.HouseScreenshotGallery || {};
        window.HouseScreenshotGallery.current = window.HouseScreenshotGallery.current || {};
        return window.HouseScreenshotGallery.current;
    }

    function getSourceElement(elOrAlternativeId) {
        if (elOrAlternativeId && typeof elOrAlternativeId === 'object' && elOrAlternativeId.dataset) {
            return elOrAlternativeId;
        }
        var alternativeId = String(elOrAlternativeId || '');
        return qs('.object-thumb-link[data-alternative-id="' + safeCss(alternativeId) + '"]') ||
            qs('[data-alternative-id="' + safeCss(alternativeId) + '"][data-customer-id]');
    }

    function getAlternativeId(elOrAlternativeId) {
        if (elOrAlternativeId && typeof elOrAlternativeId === 'object' && elOrAlternativeId.dataset) {
            return String(elOrAlternativeId.dataset.alternativeId || '');
        }
        return String(elOrAlternativeId || '');
    }

    function readAddress(sourceEl) {
        return sourceEl && sourceEl.dataset ? String(sourceEl.dataset.address || '') : '';
    }

    function readCustomerId(sourceEl) {
        return (sourceEl && sourceEl.dataset && sourceEl.dataset.customerId) ||
            (qs('.dashboard-btn[data-customer-id]') ? qs('.dashboard-btn[data-customer-id]').dataset.customerId : '') ||
            '';
    }

    function readCoords(sourceEl) {
        if (!sourceEl || !sourceEl.dataset) return null;
        var lat = parseFloat(sourceEl.dataset.lat || '');
        var lng = parseFloat(sourceEl.dataset.lng || '');
        if (Number.isFinite(lat) && Number.isFinite(lng)) {
            return { lat: lat, lng: lng };
        }
        return null;
    }

    window.openSidebarGallery = window.openObjectMapSidebar = function (elOrAlternativeId, event) {
        stopFull(event);

        var sourceEl = getSourceElement(elOrAlternativeId);
        var alternativeId = getAlternativeId(sourceEl || elOrAlternativeId);
        if (!alternativeId) return false;

        var sidebar = document.getElementById('sidebarGallery' + alternativeId);
        if (!sidebar) return false;

        var customerId = readCustomerId(sourceEl);
        var address = readAddress(sourceEl);
        var state = currentState();

        state.customerId = customerId || state.customerId || '';
        state.alternativeId = alternativeId;
        state.address = address || state.address || '';
        state.center = readCoords(sourceEl) || state.center || null;
        state.sourceEl = sourceEl || state.sourceEl || null;
        state.mode = (document.getElementById('screenshotMode' + alternativeId) || {}).value || state.mode || 'satellite';
        state.zoom = parseInt(((document.getElementById('screenshotZoom' + alternativeId) || {}).value || state.zoom || '20'), 10) || 20;

        qsa('.sidebar-gallery[data-gallery-sidebar].active, .sidebar-gallery[data-gallery-sidebar].is-open, .sidebar-gallery.active, .sidebar-gallery.is-open')
            .forEach(function (item) {
                if (item !== sidebar) {
                    item.classList.remove('active', 'is-open');
                    item.style.right = '-110%';
                    item.style.visibility = 'hidden';
                    item.style.opacity = '0';
                    item.style.pointerEvents = 'none';
                }
            });

        sidebar.classList.add('active', 'is-open');
        sidebar.removeAttribute('aria-hidden');
        sidebar.style.right = '0';
        sidebar.style.visibility = 'visible';
        sidebar.style.opacity = '1';
        sidebar.style.pointerEvents = 'auto';
        document.body.classList.add('house-shot-sidebar-open');

        var addressLabel = document.getElementById('galleryAddress' + alternativeId);
        if (addressLabel && address) {
            addressLabel.textContent = address;
        }

        if (typeof window.loadSidebarGallery === 'function') {
            try { window.loadSidebarGallery(customerId, alternativeId); } catch (e) { console.warn(e); }
        }

        if (typeof window.houseShotSwitchTab === 'function') {
            window.houseShotSwitchTab(alternativeId, 'saved');
        }

        setTimeout(function () {
            if (typeof window.initSidebarObjectMap === 'function') {
                try { window.initSidebarObjectMap(alternativeId, sourceEl); } catch (e) { console.warn(e); }
            }
        }, 120);

        refreshIcons();
        return false;
    };

    window.closeSidebarGallery = function (alternativeId, event) {
        stopFull(event);
        alternativeId = String(alternativeId || '');
        var sidebar = document.getElementById('sidebarGallery' + alternativeId);
        if (!sidebar) {
            sidebar = qs('.sidebar-gallery[data-gallery-sidebar].active, .sidebar-gallery[data-gallery-sidebar].is-open, .sidebar-gallery.active, .sidebar-gallery.is-open');
        }
        if (!sidebar) return false;

        sidebar.classList.remove('active', 'is-open');
        sidebar.setAttribute('aria-hidden', 'true');
        sidebar.style.right = '-110%';
        sidebar.style.visibility = 'hidden';
        sidebar.style.opacity = '0';
        sidebar.style.pointerEvents = 'none';
        document.body.classList.remove('house-shot-sidebar-open');
        return false;
    };

    window.maSafeObjectHeaderClick = function (event, objectListId) {
        if (protectedTarget(event)) {
            stopOnly(event);
            return false;
        }

        stopFull(event);
        if (typeof window.toggleObject === 'function') {
            window.toggleObject(objectListId);
        }
        return false;
    };

    function bindStaticProtection() {
        qsa('.customerSidebar .object-thumb-link[data-alternative-id]').forEach(function (thumb) {
            thumb.setAttribute('data-no-object-toggle', '1');
            thumb.setAttribute('data-no-menu-toggle', '1');
            if (!thumb.__maThumbBound) {
                thumb.__maThumbBound = true;
                thumb.addEventListener('click', function (event) {
                    return window.openSidebarGallery(thumb, event);
                }, false);
            }
        });

        qsa('.sidebar-gallery[data-gallery-sidebar], .sidebar-gallery').forEach(function (gallery) {
            gallery.setAttribute('data-no-object-toggle', '1');
            gallery.setAttribute('data-no-menu-toggle', '1');
        });
    }

    window.addEventListener('click', function (event) {
        var target = event.target && event.target.closest ? event.target : null;
        if (!target) return;

        var thumb = target.closest('.customerSidebar .object-thumb-link[data-alternative-id]');
        if (thumb) {
            return window.openSidebarGallery(thumb, event);
        }

        var closeBtn = target.closest('[data-gallery-close], .ma-gallery-close');
        if (closeBtn) {
            var gallery = closeBtn.closest('[data-gallery-sidebar], .sidebar-gallery');
            var alternativeId = gallery ? (gallery.getAttribute('data-house-shot-root') || String(gallery.id || '').replace('sidebarGallery', '')) : '';
            return window.closeSidebarGallery(alternativeId, event);
        }

        var tabBtn = target.closest('.house-shot-tab-btn[data-house-shot-tab-btn]');
        if (tabBtn) {
            stopFull(event);
            var root = tabBtn.closest('[data-house-shot-root]');
            if (root && typeof window.houseShotSwitchTab === 'function') {
                window.houseShotSwitchTab(root.getAttribute('data-house-shot-root'), tabBtn.getAttribute('data-house-shot-tab-btn') || 'saved');
            }
            return false;
        }

        var saveBtn = target.closest('[data-house-shot-save], .ma-house-shot-save-btn');
        if (saveBtn) {
            stopFull(event);
            var cid = saveBtn.getAttribute('data-customer-id') || currentState().customerId || '';
            var aid = saveBtn.getAttribute('data-alternative-id') || currentState().alternativeId || '';
            if (typeof window.triggerScreenshot === 'function') {
                window.triggerScreenshot(cid, aid, event);
            }
            return false;
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        var openGallery = qs('.sidebar-gallery[data-gallery-sidebar].active, .sidebar-gallery[data-gallery-sidebar].is-open, .sidebar-gallery.active, .sidebar-gallery.is-open');
        if (!openGallery) return;
        var alternativeId = openGallery.getAttribute('data-house-shot-root') || String(openGallery.id || '').replace('sidebarGallery', '');
        window.closeSidebarGallery(alternativeId, event);
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindStaticProtection);
    } else {
        bindStaticProtection();
    }
})(window, document);
</script>

<script>
/* =========================================================
   FINAL FIX: PRODUCT CARD OPEN / CLOSE TOGGLE
   Replace the old product-card toggle script with this one.
========================================================= */
(function (window, document) {
    'use strict';

    /*
     * Do not reuse the old guard name.
     * The old guard can make the new script return before it binds.
     */
    if (window.__MA_PRODUCT_CARD_TOGGLE_REWRITE_20260616__) {
        return;
    }

    window.__MA_PRODUCT_CARD_TOGGLE_REWRITE_20260616__ = true;

    function qsa(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function escapeCss(value) {
        value = String(value || '');

        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(value);
        }

        return value.replace(/([ #;?%&,.+*~\':"!^$[\]()=>|/@])/g, '\$1');
    }

    function stopHard(event) {
        if (!event) return;

        event.preventDefault();
        event.stopPropagation();

        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }
    }

    function refreshIcons() {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    }

    function isElementVisible(el) {
        if (!el) return false;

        return (
            el.classList.contains('show') ||
            el.classList.contains('is-open') ||
            window.getComputedStyle(el).display !== 'none'
        );
    }

    function getSidebar(el) {
        return el && el.closest ? (el.closest('#customerSidebar, .customerSidebar') || document) : document;
    }

    function getProductCard(target) {
        if (!target || !target.closest) return null;

        return target.closest([
            '#customerSidebar .ma-product-card[data-product-key]',
            '#customerSidebar .project-card[data-product-key]',
            '#customerSidebar .project-link[data-product-key]',
            '.customerSidebar .ma-product-card[data-product-key]',
            '.customerSidebar .project-card[data-product-key]',
            '.customerSidebar .project-link[data-product-key]'
        ].join(','));
    }

    function getProductSubNav(card) {
        if (!card) return null;

        var key = card.getAttribute('data-product-key') || '';

        if (key) {
            var byId = document.getElementById(key);

            if (!byId) {
                try {
                    byId = document.querySelector('#' + escapeCss(key));
                } catch (e) {
                    byId = null;
                }
            }

            if (byId && (byId.classList.contains('sub-nav') || byId.classList.contains('ma-sub-nav'))) {
                return byId;
            }
        }

        var next = card.nextElementSibling;

        while (next) {
            if (next.classList && (next.classList.contains('sub-nav') || next.classList.contains('ma-sub-nav'))) {
                return next;
            }

            next = next.nextElementSibling;
        }

        return null;
    }

    function isProtectedClick(event) {
        if (!event || !event.target || !event.target.closest) return true;

        return !!event.target.closest([
            'a[href]:not([href="javascript:void(0);"])',
            'button',
            'input',
            'select',
            'textarea',
            'label',
            '[contenteditable="true"]',
            '.project-card-footer',
            '.project-footer-left',
            '.project-footer-right',
            '.project-metric',
            '.price-edit-trigger',
            '.project-metric--price',
            '.project-metric--calendar',
            '.project-metric--time',
            '.sub-nav',
            '.ma-sub-nav',
            '.nav-section-btn',
            '.ma-sub-nav-btn',
            '.sidebar-gallery',
            '[data-gallery-sidebar]',
            '.object-thumb-link',
            '.object-thumb-img',
            '[data-no-object-toggle]',
            '[data-no-menu-toggle]',
            '[data-no-product-toggle]'
        ].join(','));
    }

    function closeSubNav(subNav) {
        if (!subNav) return;

        subNav.classList.remove('show', 'is-open');
        subNav.style.display = 'none';
        subNav.setAttribute('aria-hidden', 'true');
    }

    function openSubNav(subNav) {
        if (!subNav) return;

        subNav.classList.add('show', 'is-open');
        subNav.style.display = 'block';
        subNav.setAttribute('aria-hidden', 'false');
    }

    function closeCard(card, options) {
        options = options || {};

        if (!card) return;

        var subNav = getProductSubNav(card);

        closeSubNav(subNav);

        card.classList.remove('is-open');
        card.setAttribute('aria-expanded', 'false');

        if (options.removeActive !== false) {
            card.classList.remove('active');
        }
    }

    function openCard(card, options) {
        options = options || {};

        if (!card) return false;

        var subNav = getProductSubNav(card);

        if (!subNav) {
            return false;
        }

        closeOtherCards(card, subNav);

        card.classList.add('active', 'is-open');
        card.setAttribute('aria-expanded', 'true');

        openSubNav(subNav);

        if (options.loadNotes !== false && typeof window.maLoadProductNotesFromCard === 'function') {
            try {
                window.maLoadProductNotesFromCard(card);
            } catch (error) {
                console.error('Produkt-Notizen konnten nicht geladen werden.', error);
            }
        }

        refreshIcons();
        return true;
    }

    function closeOtherCards(currentCard, currentSubNav) {
        var sidebar = getSidebar(currentCard);

        qsa('.sub-nav, .ma-sub-nav', sidebar).forEach(function (subNav) {
            if (subNav !== currentSubNav) {
                closeSubNav(subNav);
            }
        });

        qsa('.ma-product-card[data-product-key], .project-card[data-product-key], .project-link[data-product-key]', sidebar)
            .forEach(function (card) {
                if (card !== currentCard) {
                    card.classList.remove('active', 'is-open');
                    card.setAttribute('aria-expanded', 'false');
                }
            });
    }

    function toggleCard(card, options) {
        options = options || {};

        if (!card) return false;

        var subNav = getProductSubNav(card);
        var currentlyOpen = isElementVisible(subNav) || card.classList.contains('is-open');

        if (currentlyOpen) {
            /*
             * Collapse only the menu.
             * removeActive=true makes the visual state clean and prevents stale active cards.
             */
            closeCard(card, { removeActive: true });
            refreshIcons();
            return false;
        }

        return openCard(card, {
            loadNotes: options.loadNotes !== false
        });
    }

    window.toggleProduct = function (target, options) {
        var card = null;

        if (typeof target === 'string') {
            card = document.querySelector(
                '#customerSidebar [data-product-key="' + escapeCss(target) + '"], ' +
                '.customerSidebar [data-product-key="' + escapeCss(target) + '"]'
            );
        } else if (target && target.closest) {
            card = getProductCard(target);
        }

        return toggleCard(card, options || { loadNotes: false });
    };

    window.maOpenProductCard = function (target, options) {
        var card = typeof target === 'string'
            ? document.querySelector('#customerSidebar [data-product-key="' + escapeCss(target) + '"], .customerSidebar [data-product-key="' + escapeCss(target) + '"]')
            : getProductCard(target);

        return openCard(card, options || {});
    };

    window.maCloseProductCard = function (target) {
        var card = typeof target === 'string'
            ? document.querySelector('#customerSidebar [data-product-key="' + escapeCss(target) + '"], .customerSidebar [data-product-key="' + escapeCss(target) + '"]')
            : getProductCard(target);

        closeCard(card, { removeActive: true });
        refreshIcons();
        return false;
    };

    function bindInitialState() {
        qsa('#customerSidebar .ma-product-card[data-product-key], #customerSidebar .project-card[data-product-key], #customerSidebar .project-link[data-product-key], .customerSidebar .ma-product-card[data-product-key], .customerSidebar .project-card[data-product-key], .customerSidebar .project-link[data-product-key]')
            .forEach(function (card) {
                card.setAttribute('role', card.getAttribute('role') || 'button');
                card.setAttribute('tabindex', card.getAttribute('tabindex') || '0');

                var subNav = getProductSubNav(card);
                var open = card.classList.contains('is-open') || isElementVisible(subNav);

                card.setAttribute('aria-expanded', open ? 'true' : 'false');

                if (subNav) {
                    subNav.setAttribute('aria-hidden', open ? 'false' : 'true');

                    if (open) {
                        openSubNav(subNav);
                        card.classList.add('is-open');
                    } else {
                        closeSubNav(subNav);
                        card.classList.remove('is-open');
                    }
                }
            });
    }

    /*
     * Window capture is required because older customer-profile scripts use
     * document-level stopImmediatePropagation() on product cards.
     * This replacement must be the only product-card window capture handler.
     */
    window.addEventListener('click', function (event) {
        if (isProtectedClick(event)) {
            return;
        }

        var card = getProductCard(event.target);

        if (!card) {
            return;
        }

        stopHard(event);
        toggleCard(card, { loadNotes: true });

        return false;
    }, true);

    window.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        if (isProtectedClick(event)) {
            return;
        }

        var card = getProductCard(event.target);

        if (!card) {
            return;
        }

        stopHard(event);
        toggleCard(card, { loadNotes: true });

        return false;
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindInitialState);
    } else {
        bindInitialState();
    }

})(window, document);
</script>
 