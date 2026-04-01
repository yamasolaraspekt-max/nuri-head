@extends('admin.layouts.app')

@section('title') KUNDEN @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">
 
 <style>
            /* ===== Custom Modal (replaces Bootstrap modal) ===== */
            .cmodal{position:fixed;inset:0;display:none;z-index:9999;padding:18px}
            .cmodal.is-open{display:block}
            .cmodal__backdrop{position:absolute;inset:0;background:rgba(0,0,0,.55)}
            .cmodal__dialog{
                position:relative;
                width:min(1477px, calc(100% - 32px));
                margin:48px auto;
                background:#fff;
                border-radius:16px;
                overflow:hidden;
                box-shadow:0 25px 80px rgba(0,0,0,.35);
            }
            .cmodal__dialog.sm{width:min(560px, calc(100% - 32px))}
            .cmodal__header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px}
            .cmodal__title{margin:0;font-size:16px;font-weight:600}
            .cmodal__close{border:0;background:transparent;cursor:pointer;width:38px;height:38px;border-radius:12px;font-size:20px;line-height:1}
            .cmodal__close:hover{background:rgba(0,0,0,.06)}
            .cmodal__body{padding:14px 16px;max-height:calc(100vh - 220px);overflow:auto}
            .cmodal__footer{padding:14px 16px;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;background:#fff}
            body.cmodal-open{overflow:hidden}

            /* Select2 over modal */
            .select2-container{z-index:10000}
            </style>
 <style>
    .circle {
      width: 35px;
      height: 35px;
      background-color: #7DC242;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .line {
         width: 9px;
            height: 4px;
            background-color: #7DC242;
            margin-left: -3px;
            margin-right: -2px;
            position: relative;
            top: 2px;
    }
    .profile {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #7DC242;
    }

    .profile-s {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #f4a459;
    }
    .profile-r {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #ea5455;
    }
    .text {
      font-size: 10px;
      font-weight: 500;
      color: #555;
      text-align: center;
      margin-top: 10px;
    }
  </style>

  <style>
        .accordion-row {
            cursor: pointer;
            background: white;
            position: relative;
        }

 
        .accordion-content {
            display: none;
            position: relative;
        }

        .accordion-content.visible {
            display: table-row;
        }
        .table {
            color: #464545 !important;
        }

       #danger_1 .popover-header {
            background-color: #ff0000 !important;
        }

        
        .employee-img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
    }

    .employee-img-small {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 5px;
        vertical-align: middle;
    }



    @keyframes flash {
        0%   { background-color: #c3f3c3; }
        50%  { background-color: #a8e6a8; }
        100% { background-color: #c3f3c3; }
        }

        .animated.flash {
        animation: flash 2s ease-in-out 1;
        }


        .custom-edit-modal .modal-dialog {
    z-index: 1055;
    }

</style>


<style>
    #fundingSidebar {
    position: fixed;
    top: 0;
    right: -100%;
    width: 100%;
    max-width: 1385px;
    height: 100%;
    background: #121212;
    z-index: 1050;
    transition: right 0.5s ease-in-out;
    overflow-y: auto;
    }
    #fundingSidebar.active {
    right: 0;
    }
</style>
  
<style>
    .js-menu-panel{display:none;}
    .js-menu-panel.is-open{display:block;}
    .js-menu-portal{position:fixed!important;transform:none!important;z-index:1075!important;}


    .custom-menu {
    position: absolute;
    display: none;
    opacity: 0;
    transform: translateY(4px);
    min-width: 200px;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: .75rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    flex-direction: column;       /* Ensure vertical layout */
    z-index: 1080;
    padding: .35rem 0;
    transition: opacity .15s ease, transform .15s ease;
    }

    .custom-menu.is-open {
        display: flex;
        opacity: 1;
        transform: translateY(0);
        flex-direction: column;
        background: white;
        padding: 9px;
    }

    .custom-menu-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .5rem 1rem;
    color: #334155;
    text-decoration: none;
    white-space: nowrap;
    transition: background .15s ease;
    }

    .custom-menu-item:hover {
    background: #f1f5f9;
    color: #0f172a;
    }

    .js-menu-portal {
    position: fixed !important;
    left: 0;
    top: 0;
    transform: none;
    }
    @keyframes pulse-bg {
    0%   { background-color: #fff3cd; }
    50%  { background-color: #ffe8a1; }
    100% { background-color: #fff3cd; }
    }
    .row-pulse {
    animation: pulse-bg 2s ease-in-out 3; /* ~6s lang */
    }


</style>
 
 <style>
    /* BACKDROP & DRAWER */
        .dup-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease, visibility .2s ease;
            z-index: 1050;
        }
        .dup-drawer-backdrop.is-open {
            opacity: 1;
            visibility: visible;
        }

        .dup-drawer {
            position: absolute;
            top: 0;
            right: 0;
            width: 1095px;            /* requested width */
            max-width: 100%;
            height: 100%;
            background: #f2f2f2ff;
            color: #181818ff;
            border-left: 1px solid rgba(148, 163, 184, .4);
            box-shadow: -10px 0 40px rgba(15, 23, 42, .85);
            transform: translateX(100%);
            transition: transform .25s ease;
            display: flex;
            flex-direction: column;
        }
        .dup-drawer-backdrop.is-open .dup-drawer {
            transform: translateX(0);
        }

        .dup-drawer__header {
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(148, 163, 184, .3);
        }
        .dup-drawer__body {
            padding: 1rem 1.25rem 1.5rem;
            overflow-y: auto;
        }

        /* GROUP */
        .dup-group {
            margin-bottom: 1.1rem;
            padding-bottom: 0.9rem;
            border-bottom: 1px dashed rgba(148, 163, 184, 0.35);
        }
        .dup-group:last-child {
            border-bottom: none;
            margin-bottom: 0.4rem;
            padding-bottom: 0;
        }

        .dup-group__header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 0.4rem;
        }
        .dup-group__title {
            font-size: 0.95rem;
            font-weight: 600;
        }
        .dup-group__meta {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        .dup-group__count {
            font-size: 0.75rem;
            color: #e5e7eb;
            opacity: 0.85;
        }

        /* 3 cards per row */
        .dup-group__list {
            margin-top: 0.4rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr)); /* exactly 3 per row */
            gap: 0.75rem;
        }

        /* CUSTOMER CARD */
        .dup-card-customer {
            position: relative;
            border-radius: 0.9rem;
            padding: 0.75rem 0.8rem 0.8rem;
            background:#e7e7e7ff ;                         /* card color gradient */
            color: #0f172a;
            box-shadow:
                0 10px 25px rgba(15, 23, 42, 0.38),
                inset 0 0 0 1px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }
        .dup-card-customer::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 0 0, rgba(255, 255, 255, 0.18), transparent 55%);
            opacity: 0.75;
            pointer-events: none;
        }
        .dup-card-customer:last-child {
            margin-bottom: 0;
        }

        .dup-card__title {
            position: relative;
            z-index: 1;
            font-weight: 700;
            font-size: 0.95rem;
            color: #0f172a;
        }
        .dup-card__title .text-xs {
            font-weight: 500;
            opacity: 0.9;
        }
        .dup-card__meta {
            position: relative;
            z-index: 1;
            font-size: 0.78rem;
            color: #1f2937;
            margin-top: .15rem;
        }

        /* chips use card colors but darker */
        .dup-card__chips {
            position: relative;
            z-index: 1;
            margin-top: 0.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.28rem;
        }
        .dup-chip {
            font-size: 0.72rem;
            padding: 0.12rem 0.45rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.12);
            color: #0f172a;
            border: 1px solid rgba(15, 23, 42, 0.08);
            white-space: nowrap;
        }

        .dup-products {
            position: relative;
            z-index: 1;
            margin-top: 0.4rem;
            font-size: 0.75rem;
            color: #111827;
        }
        .dup-products .text-xs {
            font-size: 0.7rem;
            color: #374151;
        }

        /* PROGRESS */
        .dup-progress {
            position: relative;
            z-index: 1;
            margin-top: 0.6rem;
        }
        .dup-progress-bar {
            height: 6px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.15);
            overflow: hidden;
        }
        .dup-progress-bar-inner {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(
                90deg,
                #93c21c,
                #cfe09b,
                #c0d8ea,
                #74b2d4
            ); /* use same palette */
            width: 0;
        }
        .dup-progress-label {
            margin-top: 0.25rem;
            font-size: 0.7rem;
            color: #111827;
        }

        /* DELETE BUTTON */
        .dup-delete-btn {
            position: absolute;
            top: 0.45rem;
            right: 0.45rem;
            border: none;
            background: rgba(15, 23, 42, 0.08);
            color: #0f172a;
            border-radius: 999px;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            cursor: pointer;
            z-index: 2;
        }
        .dup-delete-btn:hover {
            background: rgba(15, 23, 42, 0.15);
        }
        .dup-actions {
            position: relative;
            z-index: 1;
            margin-top: 0.5rem;
            display: flex;
            justify-content: flex-end;
        }

        .dup-profile-btn {
            font-size: 0.75rem;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
        }


        /* STAGE BADGES – still tinted but on top of card colors */
        .dup-stages {
            position: relative;
            z-index: 1;
            margin-top: 0.45rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }
        .dup-stage-badge {
            font-size: 0.7rem;
            padding: 0.12rem 0.5rem;
            border-radius: 999px;
            border: 1px solid rgba(15, 23, 42, 0.2);
            background: rgba(255, 255, 255, 0.3);  /* “badge ground” blends with card colors */
            color: #0f172a;
        }

        /* optional per-stage emphasis using palette as border only */
        .dup-stage-lead      { border-color: #93c21c; }
        .dup-stage-offer     { border-color: #cfe09b; }
        .dup-stage-deal      { border-color: #c0d8ea; }
        .dup-stage-project   { border-color: #74b2d4; }
        .dup-stage-ticket    { border-color: #74b2d4; }
        .dup-stage-pause     { border-color: #c0d8ea; }
        .dup-stage-cancel    { border-color: #93c21c; }
        .dup-stage-junk      { border-color: #93c21c; }
        .dup-stage-archive   { border-color: #cfe09b; }
        .dup-stage-feedback  { border-color: #74b2d4; }

 </style>
 
  <style>
        :root {
            --sa-green:   #94c11b;
            --sa-blue:    #73b1d4;
            --sa-soft:    #e8f0d0;
            --sa-navy:    #164194;
            --sa-red:     #e53060;
            --sa-navy2:   #213985;

            --lead-bg:        #020617;
            --lead-surface:   #0b1220;
            --lead-surface-soft: #020617;
            --lead-border:    rgba(148, 163, 184, 0.35);
            --lead-radius-xl: 22px;
            --lead-radius-lg: 18px;
            --lead-text-main: #e5e7eb;
            --lead-text-soft: #9ca3af;
        }

    /* MAIN SHELL */
    .lead-overview {
        padding: 1.5rem 1.75rem 2rem;
        border-radius: var(--lead-radius-xl);
        background: #ffffff;
        color: var(--lead-text-main); 
        position: relative;
        overflow: hidden;
    }

    /* HEADER */
    .lead-overview-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .lead-overview-kicker {
        font-size: 0.75rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--lead-text-soft);
        margin-bottom: 0.2rem;
    }

    .lead-overview-title {
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .lead-overview-subtitle {
        font-size: 0.85rem;
        max-width: 320px;
        color: var(--lead-text-soft);
    }

    /* GENERIC STRIP – HORIZONTAL SCROLLER */
    .scrollable-container {
        display: flex;
        gap: 0.9rem;
        padding-bottom: 0.25rem;
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.7) transparent;
    }
    .scrollable-container::-webkit-scrollbar {
        height: 6px;
    }
    .scrollable-container::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollable-container::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.7);
        border-radius: 999px;
    }

    /* KPI CARDS STRIP */
    .lead-overview-strip {
        margin-bottom: 1.6rem;
    }

    /* KPI CARD BASE */
    .lead-kpi-card {
        min-width: 168px;
        border-radius: var(--lead-radius-lg);
        padding: 0.9rem 1rem; 
        background: radial-gradient(circle at top left, rgba(15, 23, 42, 0.9), #020617);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.22rem;
        position: relative;
        overflow: hidden;
    }
    .lead-kpi-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 0 -40%, rgba(255, 255, 255, 0.16), transparent 55%);
        opacity: 0.3;
        pointer-events: none;
    }

    .lead-kpi-header {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin-bottom: 0.35rem;
    }

    .lead-kpi-dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        border: 2px solid rgba(15, 23, 42, 0.9);
        box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.8);
    }

    .lead-kpi-label {
        font-size: 0.72rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--lead-text-soft);
    }

    .lead-kpi-value {
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    .lead-kpi-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.1rem;
        font-size: 0.78rem;
    }

    .lead-kpi-percentage {
        font-weight: 600;
    }

    .lead-kpi-caption {
        color: var(--lead-text-soft);
    }

    /* KPI DOT COLORS */
    .lead-kpi-dot--open    { background: var(--sa-red);     }
    .lead-kpi-dot--active  { background: var(--sa-green);   }
    .lead-kpi-dot--inactive{ background: var(--sa-blue);    }
    .lead-kpi-dot--ended   { background: var(--sa-navy2);   }
    .lead-kpi-dot--cancel  { background: #7e7d7d;           }
    .lead-kpi-dot--all     { background: #782568;           }

    /* KPI CARD ACCENT BACKGROUNDS */
    .opens {
        background: #e8f0d0;
    }
    .actives {
        background: #93c21d;
    }
    .inactives {
        background: #e2e2e2;
    }
    .project_ends {
        background: #c0d8ea;
    }
    .project_cancel {
        background:#e74510;
    }
    .lead-kpi-card--all {
        background: #74b2d4;
    }

    /* ARTICLE SECTION HEADER */
    .lead-articles-wrapper {
        margin-top: 1.75rem;
    }

    .lead-articles-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }

    .lead-articles-title {
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .lead-articles-subtitle {
        font-size: 0.82rem;
        color: var(--lead-text-soft);
        max-width: 380px;
    }

    /* ARTICLE STRIP */
    .lead-articles-strip {
        padding-top: 0.25rem;
    }

    /* ARTICLE CARD */
    .lead-article-card {
        min-width: 270px;
        max-width: 280px;
        border-radius: var(--lead-radius-lg); 
        background: #ffffff;
        padding: 0.9rem 0.9rem 0.8rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.7rem;
        position: relative;
        transition:
            transform 0.16s ease-out,
            box-shadow 0.16s ease-out,
            border-color 0.16s ease-out,
            background 0.16s ease-out;
    }
    .lead-article-card:hover {
        transform: translateY(-3px); 
        border-color: rgba(148, 193, 27, 0.75);
        background: #040404;
    }

    /* ARTICLE CARD MAIN ROW */
    .lead-article-main {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    /* ROUND TOGGLE */
    .articles {
        background: rgba(232, 240, 208, 0.14);
        border-radius: 999px;
        height: 48px;
        width: 48px;
        display: grid;
        place-items: center;
        cursor: pointer;
        border: 1px solid rgba(148, 163, 184, 0.6);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.8);
        overflow: hidden;
        transition: all 0.16s ease-out;
    }
    .articles-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--lead-text-main);
    }
    .articles.is-active {
        background: linear-gradient(135deg, var(--sa-green), var(--sa-blue));
        border-color: var(--sa-soft);
        box-shadow:
            0 10px 30px rgba(14, 148, 136, 0.85),
            0 0 0 1px rgba(15, 23, 42, 0.8);
    }
    .articles.is-active .articles-label {
        color: #ffffff;
    }

    /* ARTICLE TEXT AREA */
    .article_text {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .article-name {
        font-size: 0.95rem;
        font-weight: 600;
    }

    .article-meta {
        display: grid;
        gap: 0.15rem;
        font-size: 0.78rem;
        color: var(--lead-text-soft);
    }

    .article-meta-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .article-meta-label {
        opacity: 0.85;
    }

    .article-meta-value {
        font-weight: 600;
    }

    .article-meta-chip {
        padding: 0.08rem 0.4rem;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.65);
        font-size: 0.74rem;
    }

    /* STATUS LIST RIGHT SIDE */
    .lead-article-status {
        border-top: 1px dashed rgba(148, 163, 184, 0.4);
        padding-top: 0.6rem;
        margin-top: 0.2rem;
    }

    .lead-status-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 0.18rem;
        font-size: 0.78rem;
    }

    .lead-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* STATUS LABEL PILL */
    .lead-status-label {
        min-width: 60px;
        padding: 0.05rem 0.45rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
    }

    /* STATUS COLORS */
    .openli   { background: rgba(229, 48, 96, 0.16);  border: 1px solid rgba(229, 48, 96, 0.8); }
    .activeli { background: rgba(148, 193, 27, 0.16); border: 1px solid rgba(148, 193, 27, 0.9); }
    .inactiveli { background: rgba(115, 177, 212, 0.16); border: 1px solid rgba(115, 177, 212, 0.9); }
    .endedli  { background: rgba(33, 57, 133, 0.16); border: 1px solid rgba(33, 57, 133, 0.9); }
    .cancelli { background: rgba(126, 125, 125, 0.16); border: 1px solid rgba(126, 125, 125, 0.9); }
    .sumli    { background: rgba(120, 37, 103, 0.16); border: 1px solid rgba(120, 37, 103, 0.9); }

    .simpleli {
        font-variant-numeric: tabular-nums;
        color: var(--lead-text-soft);
    }

    /* (Optional) PRODUCT DOTS IN TABLE – unchanged, just slightly tuned */
    .circle {
        width: 35px;
        height: 35px;
        background-color: var(--sa-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        cursor: pointer;
        transition: transform 0.12s ease-out, box-shadow 0.12s ease-out;
    }
    .circle:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }
    .line {
        width: 9px;
        height: 4px;
        background-color: var(--sa-green);
        margin-left: -3px;
        margin-right: -2px;
        position: relative;
        top: 2px;
    }
</style>
 <style>
    .lead-toolbar-row {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;              /* force single row */
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        overflow-x: auto;               /* scroll horizontally on small screens */
        padding-bottom: 0.25rem;
    }

    /* pill buttons with icons */
    .sa-btn-pill {
        border-radius: 999px !important;
        padding: 0.4rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    .sa-btn-pill i {
        font-size: 15px;
        line-height: 1;
    }

    /* icon-only pill */
    .sa-btn-pill-icon {
        width: 38px;
        height: 38px;
        padding: 0 !important;
        justify-content: center;
    }

    /* search area */
    .lead-toolbar-search {
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    .lead-toolbar-search .input-group {
        min-width: 718px;
        max-width: 718px;
    }

    /* product filter area */
    .lead-toolbar-products {
        min-width: 260px;
        max-width: 320px;
        flex: 0 0 auto;
    }

    .lead-toolbar-products select {
        width: 100%;
    }

    /* make Select2 100% width */
    .lead-toolbar-products .select2-container {
        width: 100% !important;
    }

    .lead-toolbar-products .select2-selection--multiple {
        min-height: 38px;
        border-radius: 999px !important;
        display: flex;
        align-items: center;
        border-color: #d1d5db;
    }

    .lead-toolbar-products .select2-selection__rendered {
        padding-left: 0.75rem;
    }

    .lead-toolbar-products .select2-search__field {
        margin-top: 0;
    }

    /* small label next to filter */
    .lead-toolbar-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b7280;
        margin-right: 0.25rem;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
</style> 
 
 <style>
  :root {
    --feed-shell-radius: 18px;
    --feed-shell-border: rgba(15, 23, 42, 0.12);

    --feed-header-from: #73b1d4;
    --feed-header-to: #5881a6;

    --feed-bg: #f3f4f6;
    --feed-item-border: rgba(148, 163, 184, 0.4);

    --feed-text-main: #111827;
    --feed-text-soft: #6b7280;
    --feed-text-muted: #9ca3af;

    --feed-pill-bg: #f9fafb;
    --feed-pill-radius: 999px;

    --feed-avatar-border: rgba(15, 23, 42, 0.15);
    --feed-avatar-bg: #e5e7eb;
  }

  /* SHELL / HEADER / BODY -------------------------------------------------- */

  .feed-modal {
    border-radius: var(--feed-shell-radius);
    overflow: hidden;
    border: 1px solid var(--feed-shell-border);
    box-shadow:
      0 18px 45px rgba(15, 23, 42, 0.18),
      0 0 0 1px rgba(15, 23, 42, 0.04);
    background: #ffffff;
  }

  .feed-modal-header {
    background: linear-gradient(135deg, var(--feed-header-from), var(--feed-header-to));
    color: #f9fafb;
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid rgba(15, 23, 42, 0.18);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .feed-modal-title-wrapper {
    display: flex;
    align-items: center;
    gap: 0.55rem;
  }

  .feed-modal-title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 999px;
    background-color: rgba(15, 23, 42, 0.18);
    box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.12);
  }

  .feed-modal-title-icon i {
    font-size: 14px;
  }

  .feed-modal-title-text {
    font-size: 0.95rem;
    font-weight: 600;
    letter-spacing: 0.01em;
  }

  .feed-modal-subtitle {
    font-size: 0.8rem;
    color: rgba(249, 250, 251, 0.8);
  }

  .feed-modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }

  .feed-modal-close span {
    font-size: 26px;
    line-height: 1;
  }

  .feed-modal-body {
    background: var(--feed-bg);
    padding: 1rem 1.25rem 1.25rem;
  }

  /* TOOLBAR ---------------------------------------------------------------- */

  .feed-modal-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.8rem;
  }

  .feed-modal-search {
    flex: 1 1 230px;
    min-width: 180px;
  }

  .feed-modal-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
  }

  .feed-modal-filter-pill {
    font-size: 0.75rem;
    border-radius: 999px;
    padding: 0.22rem 0.7rem;
    border: 1px solid rgba(148, 163, 184, 0.6);
    background: #f9fafb;
    color: var(--feed-text-soft);
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
  }

  .feed-modal-filter-pill.is-active {
    background: rgba(59, 130, 246, 0.1);
    color: #1d4ed8;
    border-color: rgba(59, 130, 246, 0.6);
  }

  /* LIST / EMPTY ----------------------------------------------------------- */

  .feed-modal-list {
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 0.3rem;
  }

  .feed-modal-empty {
    padding: 1.25rem 0.5rem;
    text-align: center;
    color: var(--feed-text-soft);
    font-size: 0.9rem;
  }

  /* ITEMS ------------------------------------------------------------------ */

  .feed-modal-item {
    display: flex;
    padding: 0.75rem 0.25rem;
    border-bottom: 1px solid var(--feed-item-border);
  }

  .feed-modal-item:last-child {
    border-bottom: none;
  }

  .feed-modal-item-icon {
    flex: 0 0 42px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    margin-right: 0.55rem;
  }

  .feed-modal-icon-pill {
    width: 30px;
    height: 30px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    background: #e5e7eb;
    color: var(--feed-text-main);
    box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.45);
  }

  .feed-modal-icon-pill--product {
    background: rgba(34, 197, 94, 0.14);
    color: #15803d;
  }

  .feed-modal-icon-pill--appointment {
    background: rgba(59, 130, 246, 0.14);
    color: #1d4ed8;
  }

  .feed-modal-icon-pill--task {
    background: rgba(245, 158, 11, 0.14);
    color: #b45309;
  }

  .feed-modal-icon-pill--ticket {
    background: rgba(236, 72, 153, 0.14);
    color: #be185d;
  }

  .feed-modal-icon-pill--history {
    background: rgba(107, 114, 128, 0.14);
    color: #374151;
  }

  .feed-modal-item-main {
    flex: 1 1 auto;
    min-width: 0;
  }

  .feed-modal-item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.12rem;
    gap: 0.4rem;
  }

  .feed-modal-item-title {
    font-weight: 600;
    color: var(--feed-text-main);
    font-size: 0.95rem;
    margin-right: 0.25rem;
  }

  .feed-modal-item-time {
    font-size: 0.8rem;
    color: var(--feed-text-soft);
    white-space: nowrap;
  }

  .feed-modal-item-time i {
    font-size: 0.8rem;
    margin-right: 3px;
  }

  .feed-modal-item-text {
    font-size: 0.84rem;
    color: #374151;
    margin-bottom: 0.25rem;
    word-break: break-word;
  }

  .feed-modal-item-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem 0.5rem;
  }

  .feed-modal-pill {
    font-size: 0.72rem;
    border-radius: var(--feed-pill-radius);
    padding: 0.1rem 0.6rem;
    background: var(--feed-pill-bg);
    border: 1px solid rgba(148, 163, 184, 0.6);
    color: var(--feed-text-main);
  }

  .feed-modal-kind-label {
    font-size: 0.75rem;
    color: var(--feed-text-soft);
  }

  /* INLINE FEED AVATARS (CARD) -------------------------------------------- */

  .live-feed-employees {
    margin-top: 0.2rem;
    display: flex;
    align-items: center;
  }

  .live-feed-avatar {
    width: 20px;
    height: 20px;
    border-radius: 999px;
    overflow: hidden;
    border: 1px solid var(--feed-avatar-border);
    margin-right: -4px;
    background: var(--feed-avatar-bg);
  }

  .live-feed-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .live-feed-employees-more {
    margin-left: 6px;
    font-size: 0.7rem;
    color: var(--feed-text-soft);
  }

  /* MODAL AVATARS ---------------------------------------------------------- */

  .feed-modal-avatars {
    margin-top: 0.3rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
  }

  .feed-modal-avatar {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    overflow: hidden;
    border: 1px solid var(--feed-avatar-border);
    background: var(--feed-avatar-bg);
  }

  .feed-modal-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .feed-modal-avatars-more {
    font-size: 0.75rem;
    color: var(--feed-text-soft);
    margin-left: 2px;
  }

  /* RESPONSIVE ------------------------------------------------------------- */

  @media (max-width: 768px) {
    .feed-modal-header {
      flex-direction: row;
      align-items: flex-start;
      gap: 0.5rem;
    }

    .feed-modal-item-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .feed-modal-item-time {
      margin-top: 2px;
    }
  }
</style> 

<style>
    /* ---------- INLINE CUSTOMER LIVE FEED (ROW WIDGET) ---------- */

    .customer-live-feed {
    display: flex;
    align-items: stretch;
    background: #f9fafb;
    border-radius: 16px; 
    padding: 0.35rem 0.55rem;
    font-size: 0.8rem;
    line-height: 1.2;
    color: #111827;
    min-width: 260px;
    max-width: 100%;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
    }

    /* Left icon */

    .live-feed-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.55rem;
    }

    .live-feed-icon-pill {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    background: linear-gradient(135deg, #73b1d4, #5881a6);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.15);
    }

    .live-feed-icon-pill i {
    font-size: 14px;
    color: #f9fafb;
    }

    /* Middle content */

    .live-feed-main {
    flex: 1 1 auto;
    min-width: 0;
    display: flex;
    flex-direction: column;
    }

    .live-feed-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
    }

    .live-feed-title {
    font-size: 0.82rem;
    font-weight: 600;
    color: #111827;
    margin-right: 0.35rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    }

    .live-feed-time {
    font-size: 0.7rem;
    color: #6b7280;
    white-space: nowrap;
    }

    .live-feed-time::before {
    content: "•";
    margin-right: 4px;
    color: #9ca3af;
    }

    .live-feed-text {
    font-size: 0.78rem;
    color: #374151;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    }

    /* Employees (appointments/tasks) */

    .live-feed-employees {
    display: flex;
    align-items: center;
    margin-top: 1px;
    margin-bottom: 1px;
    }

    .live-feed-employees:empty {
    display: none;
    }

    .live-feed-avatar {
    width: 18px;
    height: 18px;
    border-radius: 999px;
    overflow: hidden;
    border: 1px solid rgba(15, 23, 42, 0.18);
    background: #e5e7eb;
    margin-right: -4px;
    }

    .live-feed-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    }

    .live-feed-employees-more {
    margin-left: 6px;
    font-size: 0.7rem;
    color: #6b7280;
    }

    /* Meta line */

    .live-feed-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem 0.45rem;
    margin-top: 1px;
    }

    .live-feed-pill {
    font-size: 0.7rem;
    border-radius: 999px;
    padding: 0.1rem 0.6rem; 
    background: rgba(255, 255, 255, 0.9);
    }

    .customer-live-feed[data-kind="product"]   .live-feed-pill { border-color: rgba(34, 197, 94, 0.5);  color: #166534; }
    .customer-live-feed[data-kind="appointment"] .live-feed-pill { border-color: rgba(59, 130, 246, 0.5); color: #1d4ed8; }
    .customer-live-feed[data-kind="task"]      .live-feed-pill { border-color: rgba(245, 158, 11, 0.5); color: #b45309; }
    .customer-live-feed[data-kind="ticket"]    .live-feed-pill { border-color: rgba(236, 72, 153, 0.5); color: #be185d; }
    .customer-live-feed[data-kind="history"]   .live-feed-pill { border-color: rgba(107, 114, 128, 0.6); color: #374151; }

    .live-feed-counter {
    font-size: 0.68rem;
    color: #9ca3af;
    }

    /* Empty & error */

    .live-feed-empty {
    display: none;
    font-size: 0.76rem;
    color: #6b7280;
    }

    .customer-live-feed.is-empty .live-feed-line {
    display: none;
    }

    .customer-live-feed.is-empty .live-feed-empty {
    display: block;
    }

    .live-feed-error {
    margin-top: 2px;
    font-size: 0.7rem;
    }

    /* Right controls */

    .live-feed-controls {
    display: flex;
    align-items: center;
    margin-left: 0.35rem;
    gap: 0.15rem;
    }

    .live-feed-btn {
    border: none;
    background: transparent;
    padding: 0.1rem 0.25rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.12s ease, transform 0.1s ease;
    }

    .live-feed-btn i {
    font-size: 13px;
    color: #6b7280;
    }

    .live-feed-btn:hover {
    background: rgba(148, 163, 184, 0.2);
    }

    .live-feed-btn:active {
    transform: translateY(1px);
    }

    /* Inline widget responsiveness */

    @media (max-width: 992px) {
    .customer-live-feed {
        border-radius: 14px;
        padding-right: 0.4rem;
    }
    }

    @media (max-width: 768px) {
    .customer-live-feed {
        flex-wrap: wrap;
    }

    .live-feed-controls {
        margin-left: 0;
        margin-top: 2px;
    }

    .live-feed-main {
        margin-top: 2px;
    }
    }

</style>
 <style>
    :root {
        --cm-blue: #74b2d4;
        --cm-green: #93c21c;
        --cm-light-green: #cfe09b;
        --cm-bg-overlay: rgba(0, 0, 0, 0.6);
    }

    /* 1. The Modal Overlay */
    .custom-modal-overlay {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 12; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto; /* Enable scroll if needed */
        background-color: var(--cm-bg-overlay);
        backdrop-filter: blur(4px); /* Modern blur effect */
        animation: fadeIn 0.3s;
    }

    /* 2. The Modal Content Box */
    .custom-modal-content {
        background-color: #fefefe;
        margin: 2% auto; /* 2% from top and centered */
        border: 1px solid #888;
        width: 95%; /* Responsive width */
        max-width: 1600px; /* Max width for large screens */
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        height: 90vh; /* Fixed height */
        overflow: hidden;
    }

    /* 3. Header */
    .custom-modal-header {
        background-color: var(--cm-blue);
        color: white;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 4px solid var(--cm-green);
    }

    .custom-close-btn {
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }
    .custom-close-btn:hover { color: var(--cm-light-green); }

    /* 4. Body (Scrollable) */
    .custom-modal-body {
        padding: 20px;
        overflow-y: auto;
        background-color: #f4f7f6;
        flex: 1;
    }

    /* 5. Custom UI Elements */
    .cm-filter-bar {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .cm-customer-card {
        background: white;
        border-left: 5px solid var(--cm-blue);
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-radius: 4px;
    }

    .cm-customer-header {
        padding: 15px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        font-weight: bold;
        background: #fff;
        border-bottom: 1px solid #eee;
    }
    .cm-customer-header:hover { background-color: #f9f9f9; }

    .cm-object-container {
        padding: 15px;
        background-color: #fafafa;
        display: none; /* Hidden initially */
    }

    .cm-object-card {
        border: 1px solid var(--cm-light-green);
        background: white;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .cm-object-title {
        color: var(--cm-green);
        font-weight: 800;
        margin-bottom: 10px;
        border-bottom: 2px solid var(--cm-light-green);
        padding-bottom: 5px;
        display: flex;
        justify-content: space-between;
    }

    /* Table Styling */
    .cm-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    .cm-table th {
        background-color: var(--cm-light-green);
        color: #333;
        padding: 8px;
        text-align: left;
    }
    .cm-table td {
        border-bottom: 1px solid #eee;
        padding: 5px;
    }
    .cm-input {
        width: 100%;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    /* Buttons */
    .btn-cm-primary { background-color: var(--cm-blue); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
    .btn-cm-add { background-color: var(--cm-green); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; }
    .btn-cm-save { background-color: var(--cm-green); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    .btn-cm-del { background-color: #ff6b6b; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }

    .alert-info {
        padding: 10px 15px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #b8daff;
        font-size: 14px;
    }
    @keyframes fadeIn {
        from {opacity: 0}
        to {opacity: 1}
    }
</style>

<style>
    /* Define the flash animation */
    @keyframes niceFlash {
        0% { background-color: #74b2d4; transform: scale(1); }
        50% { background-color: #c0d8ea; transform: scale(1.02); box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        100% { background-color: white; transform: scale(1); }
    }

    /* The class to apply */
    .new-item-highlight {
        animation: niceFlash 4s ease-out forwards;
        border-left: 5px solid #93c21c !important; /* Visual indicator on the left */
        background: #cfe09b4f !important;
        transition: all 0.5s ease;
    }
</style>
 <style>
    /* My Customer Toggle & Modal Styles */
    .my-customer-wrapper {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 50px;
        padding: 5px 15px 5px 5px;
        margin-right: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .my-customer-wrapper:hover {
        border-color: #74b2d4;
        box-shadow: 0 4px 8px rgba(116, 178, 212, 0.15);
    }
    .my-customer-wrapper.active {
        background: #eef7fc;
        border-color: #74b2d4;
    }

    .mc-switch {
        position: relative;
        display: inline-block;
        width: 36px;
        height: 20px;
        margin-right: 10px;
        margin-bottom: 0;
    }
    .mc-switch input { opacity: 0; width: 0; height: 0; }
    .mc-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        border-radius: 34px;
        transition: .4s;
    }
    .mc-slider:before {
        position: absolute;
        content: "";
        height: 16px; width: 16px;
        left: 2px; bottom: 2px;
        background-color: white;
        border-radius: 50%;
        transition: .4s;
    }
    input:checked + .mc-slider { background-color: #74b2d4; }
    input:checked + .mc-slider:before { transform: translateX(16px); }

    .mc-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        user-select: none;
    }
    .mc-count {
        font-size: 0.85rem;
        color: #74b2d4;
        font-weight: 700;
        margin-left: 5px;
    }
    .mc-info-btn {
        background: #f3f4f6;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        margin-left: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .mc-info-btn:hover { background: #74b2d4; color: white; }
    .mc-info-btn i { font-size: 12px; }

    /* Modal Cards */
    .mc-stat-card {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
    }
    .mc-stat-card:hover { transform: translateY(-3px); border-color: #74b2d4; }
    
    .mc-icon-box {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        margin-right: 15px;
        color: #fff;
        flex-shrink: 0;
    }
    .mc-icon-contact { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .mc-icon-inner { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .mc-icon-field { background: linear-gradient(135deg, #10b981, #059669); }
    
    .mc-stat-info h6 { margin: 0 0 2px; font-size: 0.85rem; color: #64748b; text-transform: uppercase; font-weight: 600; }
    .mc-stat-info h3 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #0f172a; }
</style>

<style>
    /* Main Container Flexbox */
    .lead-toolbar-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px; /* Space between elements */
        margin-bottom: 15px;
        width: 100%;
    }

    /* Small wrapper for grouping buttons together */
    .toolbar-group {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    /* Make the Form Flexible */
    .lead-toolbar-search {
        display: flex;
        align-items: center;
        min-width: 300px; /* Prevent it from getting too small */
    }

    /* Wrapper inside form to handle Search + Select2 side-by-side or stacked */
    .search-filter-wrapper {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        gap: 8px;
        align-items: center;
    }

    /* Search Input Group specific sizing */
    .search-group {
        flex: 1 1 200px; /* Grow to fill space, shrink to 200px */
        min-width: 200px;
    }

    /* Product Filter specific sizing */
    .product-group {
        flex: 0 1 300px; /* Don't grow too huge, base 300px */
        min-width: 180px;
        z-index: 1;
    }

    /* Overwrite your existing Select2 container width logic for flexibility */
    .lead-toolbar-products {
        display: flex;
        align-items: center;
        width: 100%;
    }
    
    .lead-toolbar-products .select2-container {
        width: 100% !important; /* Force select2 to fill its flex parent */
    }
    
    /* Ensure the My Customer pill doesn't break */
    .my-customer-wrapper {
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* Responsive adjustments for very small mobile screens */
    @media (max-width: 576px) {
        .lead-toolbar-container {
            flex-direction: column;
            align-items: stretch;
        }
        .toolbar-group {
            justify-content: space-between;
        }
        .my-customer-wrapper {
            justify-content: center;
            width: 100%;
        }
        .lead-toolbar-search {
            width: 100%;
            min-width: 100%;
        }
        .search-group, .product-group {
            flex: 1 1 100%; /* Stack filters on mobile */
        }
    }
</style>
@endsection
@php
    // Always an array, even if no filter is set
    $selectedProducts = (array) request()->input('products', []);
@endphp
@section('content')

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
                 
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-left mb-0">LEAD-LISTE</h2>
                                <div class="breadcrumb-wrapper col-12">
                                     <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                                        @if(Route::currentRouteName() == 'deleted.leads')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Gelöschte Leads</a></li>
                                        @elseif(Route::currentRouteName() == 'my.leads')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Meine Leads</a></li>
                                        @elseif(Route::currentRouteName() == 'new.leads')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Neue Leads</a></li>
                                               @elseif(Route::currentRouteName() == 'inquiry.junk.list')
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Junk Liste</a></li>
                                        @else
                                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Alle Leads</a></li>
                                        @endif
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                     
                </div>

                <div class="content-body">
                        <!-- Table Hover Animation start -->
                    <div class="row" id="table-hover-animation">
                        <div class="col-12">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">   
                                        <!-- Colors Section --> 
                                        <section id="upper_view" class="lead-overview" style="display:none">
                                            {{-- HEADER --}}
                                            <div class="lead-overview-header">
                                                <div>
                                                    <p class="lead-overview-kicker">Pipeline</p>
                                                    <h4 class="lead-overview-title">LEADS ÜBERSICHT</h4>
                                                </div>
                                                <p class="lead-overview-subtitle">
                                                    Überblick über alle Anfragen und deren Status – kompakt nach Phase und Gewerk.
                                                </p>
                                            </div>

                                            {{-- TOP STATUS CARDS --}}
                                            <div class="lead-overview-strip scrollable-container">
                                                <div class="lead-kpi-card opens">
                                                    <div class="lead-kpi-header">
                                                        <span class="lead-kpi-dot lead-kpi-dot--open"></span>
                                                        <span class="lead-kpi-label">NEUE ANFRAGE</span>
                                                    </div>
                                                    <div class="lead-kpi-value">{{ $counts['open'] }}</div>
                                                    <div class="lead-kpi-meta">
                                                        <span class="lead-kpi-percentage">{{ number_format($counts['open_per'], 0) }}%</span>
                                                        <span class="lead-kpi-caption">Anteil gesamt</span>
                                                    </div>
                                                </div>

                                                <div class="lead-kpi-card actives">
                                                    <div class="lead-kpi-header">
                                                        <span class="lead-kpi-dot lead-kpi-dot--active"></span>
                                                        <span class="lead-kpi-label">AKTIVE ANFRAGE</span>
                                                    </div>
                                                    <div class="lead-kpi-value">{{ $counts['active'] }}</div>
                                                    <div class="lead-kpi-meta">
                                                        <span class="lead-kpi-percentage">{{ number_format($counts['active_per'], 0) }}%</span>
                                                        <span class="lead-kpi-caption">Anteil gesamt</span>
                                                    </div>
                                                </div>

                                                <div class="lead-kpi-card inactives">
                                                    <div class="lead-kpi-header">
                                                        <span class="lead-kpi-dot lead-kpi-dot--inactive"></span>
                                                        <span class="lead-kpi-label">INAKTIVE ANFRAGE</span>
                                                    </div>
                                                    <div class="lead-kpi-value">{{ $counts['inactive'] }}</div>
                                                    <div class="lead-kpi-meta">
                                                        <span class="lead-kpi-percentage">{{ number_format($counts['inactive_per'], 0) }}%</span>
                                                        <span class="lead-kpi-caption">Anteil gesamt</span>
                                                    </div>
                                                </div>

                                                <div class="lead-kpi-card project_ends">
                                                    <div class="lead-kpi-header">
                                                        <span class="lead-kpi-dot lead-kpi-dot--ended"></span>
                                                        <span class="lead-kpi-label">JUNK ANFRAGE</span>
                                                    </div>
                                                    <div class="lead-kpi-value">{{ $counts['ended'] }}</div>
                                                    <div class="lead-kpi-meta">
                                                        <span class="lead-kpi-percentage">{{ number_format($counts['end_per'], 0) }}%</span>
                                                        <span class="lead-kpi-caption">Anteil gesamt</span>
                                                    </div>
                                                </div>

                                                <div class="lead-kpi-card project_cancel">
                                                    <div class="lead-kpi-header">
                                                        <span class="lead-kpi-dot lead-kpi-dot--cancel"></span>
                                                        <span class="lead-kpi-label">ABSAGE</span>
                                                    </div>
                                                    <div class="lead-kpi-value">{{ $counts['cancel'] }}</div>
                                                    <div class="lead-kpi-meta">
                                                        <span class="lead-kpi-percentage">{{ number_format($counts['cancel_per'], 0) }}%</span>
                                                        <span class="lead-kpi-caption">Anteil gesamt</span>
                                                    </div>
                                                </div>

                                                <div class="lead-kpi-card lead-kpi-card--all">
                                                    <div class="lead-kpi-header">
                                                        <span class="lead-kpi-dot lead-kpi-dot--all"></span>
                                                        <span class="lead-kpi-label">ALLE GEWERKE</span>
                                                    </div>
                                                    <div class="lead-kpi-value">{{ $counts['all'] }}</div>
                                                    <div class="lead-kpi-meta">
                                                        <span class="lead-kpi-percentage">100%</span>
                                                        <span class="lead-kpi-caption">Gesamt</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ARTICLE GROUPS SECTION --}}
                                            <div class="lead-articles-wrapper">
                                                <div class="lead-articles-header">
                                                    <div>
                                                        <p class="lead-overview-kicker">Gewerke</p>
                                                        <h4 class="lead-articles-title">SORTIERUNG NACH GEWERKEN</h4>
                                                    </div>
                                                    <p class="lead-articles-subtitle">
                                                        Für jedes Gewerk: Anzahl Leads und Verteilung nach Status.
                                                    </p>
                                                </div>

                                                <div class="lead-articles-strip scrollable-container">
                                                    @foreach ($article as $index => $ar)
                                                        @php
                                                            $allCount      = $counts['all'] ?? 0;
                                                            $count_product = $customer_product_count->where('article_group', $ar->article_group)->count();
                                                            $percentage    = $allCount > 0 ? ($count_product / $allCount) * 100 : 0;

                                                            $open     = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'open')->count();
                                                            $active   = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'active')->count();
                                                            $inactive = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'inactive')->count();
                                                            $ended    = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'ended')->count();
                                                            $cancel   = $customer_product_count->where('article_group', $ar->article_group)->where('status', 'cancel')->count();

                                                            if ($count_product > 0) {
                                                                $open_per     = ($open     / $count_product) * 100;
                                                                $active_per   = ($active   / $count_product) * 100;
                                                                $inactive_per = ($inactive / $count_product) * 100;
                                                                $ended_per    = ($ended    / $count_product) * 100;
                                                                $cancel_per   = ($cancel   / $count_product) * 100;
                                                            } else {
                                                                $open_per = $active_per = $inactive_per = $ended_per = $cancel_per = 0;
                                                            }

                                                            $sum_per = $open_per + $active_per + $inactive_per + $ended_per + $cancel_per;

                                                            $isActive = in_array($ar->id, $selectedProducts);
                                                        @endphp

                                                        <div class="lead-article-card">
                                                            <div class="lead-article-main">
                                                                <div class="articles js-article-filter {{ $isActive ? 'is-active' : '' }}"
                                                                    data-product-id="{{ $ar->id }}"
                                                                    title="Nach {{ $ar->article_group }} filtern">
                                                                    <span class="articles-label">{{ $ar->initial }}</span>
                                                                </div>

                                                                <div class="article_text">
                                                                    <div class="article-name">{{ $ar->article_group }}</div>

                                                                    <div class="article-meta">
                                                                        <div class="article-meta-line">
                                                                            <span class="article-meta-label">Anzahl Leads</span>
                                                                            <span class="article-meta-value">{{ $count_product }}</span>
                                                                        </div>
                                                                        <div class="article-meta-line">
                                                                            <span class="article-meta-label">Anteil gesamt</span>
                                                                            <span class="article-meta-chip">
                                                                                {{ number_format($percentage, 2) }}%
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="lead-article-status">
                                                                <ul class="lead-status-list">
                                                                    <li class="lead-status-row openli1">
                                                                        <span class="lead-status-label openli">
                                                                            #{{ $open }}
                                                                        </span>
                                                                        <span class="simpleli">
                                                                            {{ number_format($open_per, 0) }}%
                                                                        </span>
                                                                    </li>

                                                                    <li class="lead-status-row activeli1">
                                                                        <span class="lead-status-label activeli">
                                                                            #{{ $active }}
                                                                        </span>
                                                                        <span class="simpleli">
                                                                            {{ number_format($active_per, 0) }}%
                                                                        </span>
                                                                    </li>

                                                                    <li class="lead-status-row inactiveli1">
                                                                        <span class="lead-status-label inactiveli">
                                                                            #{{ $inactive }}
                                                                        </span>
                                                                        <span class="simpleli">
                                                                            {{ number_format($inactive_per, 0) }}%
                                                                        </span>
                                                                    </li>

                                                                    <li class="lead-status-row endedli1">
                                                                        <span class="lead-status-label endedli">
                                                                            #{{ $ended }}
                                                                        </span>
                                                                        <span class="simpleli">
                                                                            {{ number_format($ended_per, 0) }}%
                                                                        </span>
                                                                    </li>

                                                                    <li class="lead-status-row cancelli1">
                                                                        <span class="lead-status-label cancelli">
                                                                            #{{ $cancel }}
                                                                        </span>
                                                                        <span class="simpleli">
                                                                            {{ number_format($cancel_per, 0) }}%
                                                                        </span>
                                                                    </li>

                                                                    <li class="lead-status-row sumli1">
                                                                        <span class="lead-status-label sumli">
                                                                            Σ {{ $count_product }}
                                                                        </span>
                                                                        <span class="simpleli">
                                                                            {{ number_format($sum_per, 0) }}%
                                                                        </span>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                {{-- Optional hidden chart container --}}
                                                <div class="col-md-12 d-none">
                                                    <canvas id="statusPieChart"></canvas>
                                                </div>
                                            </div>
                                        </section>

                                 
                                        <!-- Search Section -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="lead-toolbar-container">

                                                        {{-- Group 1: Primary Actions --}}
                                                        <div class="toolbar-group">
                                                            {{-- Overview / Pipeline button --}}
                                                            <a type="button" 
                                                            class="btn btn-outline-primary sa-btn-pill"
                                                            data-toggle="popover"
                                                            data-content="Liste der Produkte und deren Prozentsatz nach jeder Artikelgruppe."
                                                            data-trigger="hover"
                                                            data-original-title="KUNDENÜBERSICHT"
                                                            href="{{ url('lead/kanban') }}">
                                                                <i class="feather icon-activity"></i>
                                                                <span class="d-none d-sm-inline">Pipeline Übersicht</span>
                                                            </a>

                                                            {{-- Massenverwaltung --}}
                                                            <button id="openMassManager" class="btn btn-outline-primary sa-btn-pill" style="border-color: #74b2d4; color: #74b2d4;">
                                                                <i class="feather icon-layers"></i> 
                                                                <span class="d-none d-sm-inline">Massenverwaltung</span>
                                                            </button>
                                                        </div>

                                                        {{-- Group 2: Meine Kunden Filter --}}
                                                        <div class="my-customer-wrapper {{ request('my_customers') ? 'active' : '' }}">
                                                            <label class="mc-switch">
                                                                <input type="checkbox" id="chkMyCustomers" {{ request('my_customers') ? 'checked' : '' }}>
                                                                <span class="mc-slider"></span>
                                                            </label>
                                                            
                                                            <span class="mc-label" onclick="document.getElementById('chkMyCustomers').click()">
                                                                Meine Kunden
                                                            </span>
                                                            
                                                            {{-- FIXED LINE BELOW: Changed 'total' to 'total_unique' --}}
                                                            <span class="mc-count">({{ $myCounts['total_unique'] ?? 0 }})</span>
                                                            
                                                            <button type="button" class="mc-info-btn" data-toggle="modal" data-target="#myCustomerStatsModal" title="Details anzeigen">
                                                                <i class="feather icon-info"></i>
                                                            </button>
                                                        </div>

                                                        {{-- Group 3: Search Form & Filters --}}
                                                        <form id="leadFilterForm" 
                                                            action="{{ action('App\Http\Controllers\NewLeadsController@index') }}" 
                                                            method="GET" 
                                                            class="lead-toolbar-search flex-grow-1">
                                                            
                                                            <input type="hidden" name="my_customers" id="inputMyCustomers" value="{{ request('my_customers', 0) }}">

                                                            <div class="search-filter-wrapper">
                                                                {{-- TEXT SEARCH --}}
                                                                <div class="input-group search-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            <i class="feather icon-search"></i>
                                                                        </span>
                                                                    </div>
                                                                    <input type="text"
                                                                        name="search"
                                                                        value="{{ request('search') }}"
                                                                        class="form-control"
                                                                        placeholder="Suche..."
                                                                        aria-describedby="btn-search-go">
                                                                    <div class="input-group-append" id="btn-search-go">
                                                                        <button class="btn btn-primary" type="submit">
                                                                            Filtern
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                {{-- PRODUCT MULTI-FILTER (Select2) --}}
                                                                <div class="product-group">
                                                                    <div class="lead-toolbar-products">
                                                                        <span class="lead-toolbar-label d-none d-lg-inline-flex">
                                                                            <i class="feather icon-filter"></i> Gewerke
                                                                        </span>
                                                                        <select name="products[]" id="filterProducts" class="form-control" multiple>
                                                                            @foreach($productInfo as $p)
                                                                                <option value="{{ $p->id }}" @if(in_array($p->id, $selectedProducts)) selected @endif>
                                                                                    {{ $p->article_group }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        {{-- Group 4: Secondary Actions --}}
                                                        <div class="toolbar-group">
                                                            {{-- Duplicates button --}}
                                                            <button type="button" 
                                                                    id="btnShowDuplicates"
                                                                    class="btn btn-outline-warning sa-btn-pill"
                                                                    title="Doppelte Kunden anzeigen">
                                                                <i class="feather icon-alert-triangle"></i>
                                                                <span class="d-none d-xl-inline">Doppelte</span>
                                                            </button>

                                                            {{-- Collapse / overview toggle --}}
                                                            <button type="button"
                                                                    class="btn btn-outline-secondary sa-btn-pill sa-btn-pill-icon"
                                                                    id="colaps"
                                                                    name="colaps"
                                                                    data-toggle="popover"
                                                                    data-content="Liste der Produkte und deren Prozentsatz nach jeder Artikelgruppe."
                                                                    data-trigger="hover"
                                                                    data-original-title="KUNDENÜBERSICHT">
                                                                <i class="feather icon-chevron-down"></i>
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                    
                                        <!-- Contents Details of Customer -->
                                        <div class="row"> 
                                                 @if ($errors->any())
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                            
                                                <div class="table-responsive">
                                                    <table class="table">
                                                       @php
                                                            $direction = request()->get('sort_order') === 'asc' ? 'desc' : 'asc';
                                                            $icon = request()->get('sort_order') === 'asc' ? 'feather icon-chevron-up' : 'feather icon-chevron-down';
                                                        @endphp
                                                        @php $highlightId = session('highlight_lead_id'); @endphp
 


                                                        <thead style="background:white;">
                                                        <tr  >
                                                            <th class="{{ request()->get('sort_by') == 'new_leads.customer_no' ? 'table-active' : '' }}">
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'new_leads.customer_no', 'sort_order' => $direction]) }}">
                                                                    KUNDE-NR <i class="{{ request()->get('sort_by') == 'new_leads.customer_no' ? $icon : '' }}"></i>
                                                                </a>
                                                            </th>

                                                            <th class="{{ request()->get('sort_by') == 'new_leads.quelle' ? 'table-active' : '' }}">
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'new_leads.quelle', 'sort_order' => $direction]) }}">
                                                                    QUELLE <i class="{{ request()->get('sort_by') == 'new_leads.quelle' ? $icon : '' }}"></i>
                                                                </a>
                                                            </th>

                                                            <th class="{{ request()->get('sort_by') == 'new_leads.name' ? 'table-active' : '' }}">
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'new_leads.name', 'sort_order' => $direction]) }}">
                                                                    NAME <i class="{{ request()->get('sort_by') == 'new_leads.name' ? $icon : '' }}"></i>
                                                                </a>
                                                            </th>

                                                            <th>KONTAKT</th>
                                                            <th>GEWERKE</th>

                                                            <th style="width:100px !important;">NOTIZ</th>

                                                            <th style="width:30px !important;">
                                                                <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">STATUS</span>
                                                                <div class="dropdown-menu">
                                                                    <span><label for="">Filtern nach</label></span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_qualified_sort') }}">
                                                                            <i class="fa fa-circle primary"></i> QUALIFIZIERT
                                                                        </a>
                                                                    </span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_not_qualified_sort') }}">
                                                                            <i class="fa fa-circle warning"></i> ERFORDERLICHE INFORMATIONEN
                                                                        </a>
                                                                    </span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_incomplete_sort') }}">
                                                                            <i class="fa fa-circle danger"></i> NICHT QUALIFIZIERT
                                                                        </a>
                                                                    </span>

                                                                    <span class="dropdown-item">
                                                                        <a href="{{ url('/lead_junk_sort') }}">
                                                                            <i class="fa fa-power-off danger"></i> JUNKS
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </th> 

                                                              <th class="{{ request()->get('sort_by') == 'new_leads.contact_person' ? 'table-active' : '' }}">
                                                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'new_leads.contact_person', 'sort_order' => $direction]) }}">
                                                                    VERFASSER <i class="{{ request()->get('sort_by') == 'new_leads.contact_person' ? $icon : '' }}"></i>
                                                                </a>
                                                            </th>

                                                            <th width="2">BEARBEITEN</th>
                                                        </tr>
                                                        </thead>

                                                        @php
                                                            $highlightId = session('highlight_lead_id');
                                                        @endphp
                                                        <tbody id="accordion-table-body">
                                                            @foreach($data as $item)
    @php
                                                                    // --- 1. Collect IDs from all sources (Singular URL, Plural URL, Session) ---
                                                                    $idsToHighlight = [];

                                                                    // Single Verify (URL: ?highlight_id=123)
                                                                    if (request()->has('highlight_id')) {
                                                                        $idsToHighlight[] = request('highlight_id');
                                                                    }

                                                                    // Bulk Verify (URL: ?highlight_ids=123,124)
                                                                    if (request()->has('highlight_ids')) {
                                                                        $idsToHighlight = array_merge($idsToHighlight, explode(',', request('highlight_ids')));
                                                                    }

                                                                    // Session Fallback (Non-AJAX redirects)
                                                                    if (session('highlight_lead_id')) {
                                                                        $idsToHighlight[] = session('highlight_lead_id');
                                                                    }

                                                                    // --- 2. Check if current row is in the list ---
                                                                    // We use loose comparison (in_array does loose by default) to match string "123" with int 123
                                                                    $isNew = in_array($item->id, $idsToHighlight);
                                                                @endphp

                                                                <tr class="accordion-row mb-2 {{ $isNew ? 'new-item-highlight' : '' }}" 
                                                                    id="lead-row-{{ $item->id }}" 
                                                                    data-row="{{$item->id}}"> 
                                                                    <td>{{$item->customer_no}}</td>
                                                                    <td>{{$item->source}}</td>
                                                                    <td><a href="{{ url('new_lead_profile/'.$item->id) }} " class="black">{{ $item->title ?? '' }}  {{ $item->academic_title ?? '' }} {{ $item->name ?? '' }} {{ $item->lastname ?? '' }}<br>
                                                                        <small>Firma: {{ $item->firma ?? '' }}</small><br>
                                                                        <small><i class="feather icon-map"></i> {{ $item->full_address ?? null }} </small>
                                                                    
                                                                        </a>
                                                                    </td> 
                                                                    <td>
                                                                        <p class="mb-0" ><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                        <p class="mb-0" ><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                        <p class="mb-0" ><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                    </td> 
                                                                    <td>     

                                                                       @php
                                                                            // Ensure the filtered collection only contains non-soft-deleted leads
                                                                            $filteredProducts = $productcount->where('customer_id', $item->id);
                                                                            $groupedProducts = $filteredProducts->groupBy('product_id');
                                                                        @endphp

                                                                        @foreach ($groupedProducts as $productId => $products)
                                                                            @php
                                                                                $productC = $products->first(); // Get the first product instance
                                                                                $productCount = $products->count(); // Count how many times the product exists
                                                                            @endphp

                                                                          <div class="position-relative d-inline-block mr-2 js-product-pill"
                                                                                data-product-id="{{ $productC->product_id }}"
                                                                                title="Nach {{ $productC->article_group }} filtern"
                                                                                style="cursor:pointer;">
                                                                                <div class="circle" style="width:32px;height:32px;">
                                                                                    <span style="font-size:11px; position:relative; top:-1px;">
                                                                                        {{ $productC->initial }}
                                                                                    </span>
                                                                                </div>
                                                                                <span class="badge badge-pill badge-primary badge-up"
                                                                                    style="position: absolute; top: -7px; right: -7px; border: 1px solid; font-size: 8px !important; background:#73b1d4 !important;">
                                                                                    {{ $productCount }}
                                                                                </span>
                                                                            </div>

                                                                        @endforeach

                                                                    </td>
                                                                    <td>
                                                                        @if($item->info)
                                                                        <!-- Button to open modal -->
                                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#note{{$item->id}}">
                                                                            <i class="fa fa-sticky-note-o"></i>
                                                                        </button>
                                                                        @else
                                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                                                            <i class="fa fa-sticky-note-o"></i>
                                                                        </button>
                                                                        @endif
                                                                        <!-- Modal -->
                                                                        <div class="modal fade" id="note{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header bg-primary white">
                                                                                        <h5 class="modal-title" id="myModalLabel120">Notizen</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <div class="col-md-10"> 
                                                                                            <h1>{{ $item->title }} {{$item->name}} {{ $item->lastname}}</h1> 
                                                                                            <p>{{ $item->street}}<br>{{ $item->postcode }} 
                                                                                            </p>
                                                                                            <p style="margin:0; line-height:0px"><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                                            <p style="margin:0; line-height:0px"><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                                            <p style="margin:0; line-height:0px"><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <h1 class="mb-2">Notizen</h1>
                                                                                        <div class="col-md-12">
                                                                                            <p>{{ $item->info }}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <!-- Modal footer (optional) -->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>   
                                                                    <td style="width: 20px;">
                                            
                                                                        @if($item->status_msg=="QUALIFIZIERT")  
                                                                        <div class="image d-flex">
                                                                            <div class="image">
                                                                                <img src="{{ asset('images/icons/ampel-gruen.svg') }}" alt="Icon"  style="width:20px"
                                                                                    data-content="DIE ANFRAGE IST BEREIT ZU QUALIFIZIEREN" 
                                                                                    data-trigger="hover" 
                                                                                    data-original-title="QUALIFIZIERT"> 
                                                                            </div> 
                                                                        </div>       

                                                                        @elseif($item->status_msg == "um zu qualifizieren, bitte per Brief  Kontakt aufnehmen")   
                                                                    <div class="image d-flex">
                                                                        <div class="image">
                                                                        <img src="{{ asset('images/icons/ampel-rot.svg') }}" alt="Icon" style="width:20px" 
                                                                                data-toggle="popover" 
                                                                                    data-content=" {{ $item->status_msg }}" 
                                                                                    data-trigger="hover" 
                                                                                color="red"
                                                                                    data-original-title="NICHT QUALIFIZIERT">
                                                                        </div>
                                                                        
                                                                    </div>

                                                                    @elseif($item->status == "Junk")   
                                                                    <div class="image d-flex">
                                                                        <div class="image"> 
                                                                        <img src="{{ asset('images/icons/ampel-rot.svg') }}" alt="Icon" style="width:20px" 
                                                                                data-toggle="popover" 
                                                                                    data-content=" {{ $item->status_msg }}" 
                                                                                    data-trigger="hover" 
                                                                                color="red"
                                                                                    data-original-title="NICHT QUALIFIZIERT">  
                                                                        </div>
                                                                        
                                                                    </div>
                                                                

                                                                        @else 
                                                                        <div class="image d-flex">
                                                                            <div class="image">
                                                                            <img src="{{ asset('images/icons/ampel-gelb.svg') }}" alt="Icon"  style="width:20px"
                                                                                data-toggle="popover" 
                                                                                    data-content=" {{ $item->status_msg }}" 
                                                                                    data-trigger="hover" 
                                                                                    data-original-title="NICHT QUALIFIZIERT">   
                                                                                </div> 
                                                                            </div>

                                                                        @endif
                                                                    </td>
                                                                    <td style="width:20px">
                                                                        <div class="image">
                                                                            <div class="avatar mr-1 ">
                                                                                <img src="{{ asset('images/employee/'.$item->c_image)}}" alt="avtar img holder" height="32" width="32" data-toggle="tooltip" data-placement="top" title data-original-tiitle="{{ $item->c_name }} {{ $item->c_lastname}}">
                                                                            </div>
                                                                            <div class="text">
                                                                                <span class="font-weight-bold"></span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                         <div id="menu-quick-{{ $item->id }}"
                                                                                    class="js-menu inline-block relative"
                                                                                    data-menu-id="menu-quick-{{ $item->id }}"
                                                                                    data-menu-scope="lead-{{ $item->id }}"
                                                                                    data-menu-align="end"
                                                                                    data-menu-portal="true"
                                                                                    data-menu-offset="0,8">

                                                                                <!-- Toggle Button -->
                                                                                <button id="toggle-quick-{{ $item->id }}"
                                                                                        type="button"
                                                                                        class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light js-menu-toggle"
                                                                                        aria-haspopup="true"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="panel-quick-{{ $item->id }}">
                                                                                    <i class="feather icon-menu"></i>
                                                                                </button>

                                                                                <!-- Custom Menu Panel (not Bootstrap .dropdown-menu anymore) -->
                                                                                <div id="panel-quick-{{ $item->id }}"
                                                                                    class="js-menu-panel custom-menu"
                                                                                    role="menu"
                                                                                    aria-labelledby="toggle-quick-{{ $item->id }}">
                                                                                    
                                                                                    <a href="{{ url('new_object/'.$item->id) }}" class="custom-menu-item">
                                                                                        <i class="feather icon-plus text-success"></i> Neue Objekt
                                                                                    </a>

                                                                                    <a href="{{ url('new_lead_details_edit/'.$item->id) }}" class="custom-menu-item">
                                                                                        <i class="feather icon-edit text-warning"></i> Bearbeiten
                                                                                    </a>

                                                                                    @if(is_null($item->deleted_at))
                                                                                        <a href="#" class="custom-menu-item"
                                                                                            data-toggle="modal"
                                                                                            data-target="#delete-pro{{ $item->id }}">
                                                                                            <i class="feather icon-trash-2 text-danger"></i> Löschen
                                                                                        </a>
                                                                                    @else
                                                                                        <a href="#" class="custom-menu-item"
                                                                                            data-toggle="modal"
                                                                                            data-target="#delete-pro{{ $item->id }}">
                                                                                            <i class="feather icon-refresh-ccw text-primary"></i> Wiederherstellen
                                                                                        </a>
                                                                                    @endif

                                                                                    @php
                                                                                        $canDelete = DB::table('user_rolls')
                                                                                            ->where('user_id', auth()->user()->name)
                                                                                            ->where('item_id', 'Customer')
                                                                                            ->where('is_delete', 'on')
                                                                                            ->exists();
                                                                                    @endphp

                                                                                    @if($canDelete)
                                                                                        @if($item->status !== 'Junk')
                                                                                            <a href="#" class="custom-menu-item"
                                                                                                data-toggle="modal"
                                                                                                data-target="#junk{{ $item->id }}">
                                                                                                <i class="fa fa-power-off text-danger"></i> Junk
                                                                                            </a>
                                                                                        @else
                                                                                            <a href="#" class="custom-menu-item"
                                                                                                data-toggle="modal"
                                                                                                data-target="#unjunk{{ $item->id }}">
                                                                                                <i class="fa fa-power-off text-primary"></i> Unjunk
                                                                                            </a>
                                                                                        @endif
                                                                                    @endif
                                                                                </div>
                                                                                </div>

                                                            
                                                                        <!-- Delete Modal -->
                                                                            <div class="modal fade" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header bg-danger white">
                                                                                            <h5 class="modal-title" id="myModalLabel120"> @if(Route::currentRouteName() != 'deleted.leads') Daten Löschen @else Wiederherstellen @endif</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        @if(Route::currentRouteName() != 'deleted.leads')
                                                                                        <div class="modal-body">
                                                                                            <h5>Aufzeichnung löschen</h5>
                                                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                            <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/new_lead_delete').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                        </div>
                                                                                        @else

                                                                                        <div class="modal-body">
                                                                                        <h5>Daten wiederherstellen: </h5>
                                                                                        <p>Möchten Sie diese Daten wirklich wiederherstellen?</p>
                                                                                        <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/restore_leads').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                        </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Delete Modal -->
                                                                            <div class="modal fade" id="junk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header bg-danger white">
                                                                                            <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <h5>Junk record</h5>
                                                                                            <p>Möchten Sie diese Anfrage als Junk festlegen?</p>
                                                                                            <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/lead_junk').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                                <!-- Unjunk Modal -->
                                                                            <div class="modal fade" id="unjunk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="false" data-backdrop="false">
                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header bg-primary white">
                                                                                            <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <h5>Junk record</h5>
                                                                                            <p>Möchten Sie die Junk-Anfrage wiederherstellen?</p>
                                                                                            <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <a type="button" href="{{url('/lead_unjunk').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>          <!-- Unjunk Modal -->
                                                                    </div>
                                                                </td>
                                                                </tr>
                                                                <tr class="customer-feed-row" data-customer-id="{{ $item->id }}">
                                                                    <td colspan="12">
                                                                        @include('admin.new_leads.partials.customer_live_feed_bar')
                                                                    </td>
                                                                </tr>

                                                                <tr class="accordion-content hidden" data-row="{{$item->id}}">
                                                                    <td colspan="12"> 
                                                                        <div class="table-responsive">
                                                                            <table class="table">
                                                                                <thead>
                                                                                    <tr style="background:white; ">  
                                                                                        <th>ID</th>     
                                                                                        <th>OBJEKT</th>  
                                                                                        <th>NOTIZ</th> 
                                                                                        <th>PRODUKT</th>  
                                                                                        <th width="2">BEARBEITEN</th> 
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($alternative->where('lead_id', $item->id) as $alter) 
                                                                                        <tr style="background:white;border-bottom: 1px solidrgb(81, 81, 81);" class="mb-2">   
                                                                                            <td> 
                                                                                            <div class="col-md-12">
                                                                                                    <p>{{$alter->id}}</p>
                                                                                                    <div class="d-flex flex-column align-items-start gap-2 stars">
                                                                                                        <?php 
                                                                                                        $currentDateTime = new DateTime(); // Current date and time
                                                                                                        $requestDateTime = new DateTime($alter->request_date); // Request date and time

                                                                                                        $interval = $currentDateTime->diff($requestDateTime); // Difference between current date and request date
                                                                                                        $hoursDifference = ($interval->days * 24) + $interval->h; // Convert difference to hours

                                                                                                        // Alert icon for "sehr dringend"
                                                                                                        if (strtolower($alter->periority) === 'sehr dringend') {
                                                                                                            echo '<a class="danger" data-toggle="popover" data-content="Bitte dringend die Anfrage bearbeiten" data-trigger="hover" data-original-title="Wichtigkeit grad sehr hoch!">
                                                                                                                    <i class="feather icon-alert-circle blink" id="alert' . $alter->id . '" style="font-size: 20px;"></i>
                                                                                                                </a>';
                                                                                                        } else {
                                                                                                            echo '<a class="secondary" data-toggle="popover" data-content="Normale Priorität" data-trigger="hover" data-original-title="Standard Priorität">
                                                                                                                    <i class="feather icon-alert-circle" id="alert' . $alter->id . '" style="font-size: 20px;"></i>
                                                                                                                </a>';
                                                                                                        }

                                                                                                        // Bell icon for request > 48 hours
                                                                                                        if ($hoursDifference > 48) {
                                                                                                            echo '<a class="danger" data-toggle="popover" data-content="Die Anfrage liegt länger als 48 Stunden, es muss dringend bearbeitet werden" data-trigger="hover" data-original-title="Zeit von 48 Stunden überschritten!">
                                                                                                                    <i class="feather icon-bell blink" id="bell' . $alter->id . '" style="font-size: 20px;"></i>
                                                                                                                </a>';
                                                                                                        } else {
                                                                                                            echo '<a class="secondary" data-toggle="popover" data-content="Weniger als 48 Stunden seit der Anfrage" data-trigger="hover" data-original-title="Noch in Zeitrahmen">
                                                                                                                    <i class="feather icon-bell" id="bell' . $alter->id . '" style="font-size: 20px;"></i>
                                                                                                                </a>';
                                                                                                        }

                                                                                                        // Star icon for request <= 48 hours
                                                                                                        if ($hoursDifference <= 48) {
                                                                                                            echo '<a class="warning" data-toggle="popover" data-content="Bitte innerhalb von 48 Stunden die Anfrage qualifizieren" data-trigger="hover" data-original-title="Neue Anfrage">
                                                                                                                    <i class="feather icon-star" id="stars' . $alter->id . '" style="font-size: 20px;"></i>
                                                                                                                </a>';
                                                                                                        } else {
                                                                                                            echo '<a class="secondary" data-toggle="popover" data-content="Die Anfrage ist älter als 48 Stunden" data-trigger="hover" data-original-title="Nicht mehr neu">
                                                                                                                    <i class="feather icon-star" id="stars' . $alter->id . '" style="font-size: 20px;"></i>
                                                                                                                </a>';
                                                                                                        }
                                                                                                        ?>
                                                                                                    </div>
                                                                                                </div>


                                                                                            </td>  
                                                                                            <td>
                                                                                                <p>
                                                                                                    <a href="{{url('new_lead_profile/'.$item->id )}}">
                                                                                                    {{ $alter->object_name }}   
                                                                                                    </a>
                                                                                                </p>
                                                                                                <p>
                                                                                                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($alter->request_date)->isoFormat('DD.MM.YY') }} <br>
                                                                                                    <code> <strong> 
                                                                                                        {{ \Carbon\Carbon::parse($alter->request_date)->diffForHumans() }}                                   
                                                                                                    </strong></code>  
                                                                                                </p>
                                                                                                <small>
                                                                                                    <i class="feather icon-map"></i> {{ $alter->street ?? null }} <br>
                                                                                                    {{ $alter->postcode }} <br>
                                                                                                    {{ $alter->city }}
                                                                                                </small>
                                                                                            </td>  
                                                                                            
                                                                                            <td>
                                                                                                @if($alter->note)
                                                                                                <!-- Button to open modal -->
                                                                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#info{{$alter->id}}">
                                                                                                    <i class="fa fa-sticky-note-o"></i>
                                                                                                </button>
                                                                                                @else
                                                                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                                                                                    <i class="fa fa-sticky-note-o"></i>
                                                                                                </button>
                                                                                                @endif
                                                                                                <!-- Modal -->
                                                                                                <div class="modal fade" id="info{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                        <div class="modal-content">
                                                                                                            <div class="modal-header bg-primary white">
                                                                                                                <h5 class="modal-title" id="myModalLabel120">Notizen</h5>
                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                    <span aria-hidden="true">×</span>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                            <div class="modal-body">
                                                                                                                <div class="col-md-10"> 
                                                                                                                    <h1>{{ $item->title }} {{$item->name}} {{ $item->lastname}}</h1> 
                                                                                                                    <p>{{ $item->street}}<br>{{ $item->postcode }}
                                                                                                                        @if($alter->main == 1)
                                                                                                                        <small><code>Die Adresse des Kunden stimmt nicht mit seiner Hauptwohnadresse überein</code></small>
                                                                                                                        @endif
                                                                                                                    </p>
                                                                                                                    <p style="margin:0; line-height:0px"><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                                                                    <p style="margin:0; line-height:0px"><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                                                                    <p style="margin:0; line-height:0px"><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                                                                </div>
                                                                                                                <hr>
                                                                                                                <h1 class="mb-2">Notizen</h1>
                                                                                                                <div class="col-md-12">
                                                                                                                    <p>{{ $alter->note }}</p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="modal-footer">
                                                                                                                <!-- Modal footer (optional) -->
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </td> 
                                                                                                <td class="text-center align-top">
                                                                                                    @php
                                                                                                        $productEmployees = collect($productEmployees)->keyBy('id');
                                                                                                    @endphp

                                                                                                    <div class="d-flex flex-wrap justify-content-center">
                                                                                                        @foreach (collect($customer_product_lists)
                                                                                                            ->where('customer_id', $item->id)
                                                                                                            ->where('alternative_id', $alter->id) as $product)

                                                                                                            @php
                                                                                                                // Translations
                                                                                                                $services = [
                                                                                                                    'complete' => 'Komplettlösung', 'montage' => 'Montage', 'product' => 'Verkauf',
                                                                                                                    'plan' => 'Planung', 'maintenance' => 'Wartung', 'repair' => 'Reparatur',
                                                                                                                    'emergency' => 'Notdienst', 'others' => 'Sonstiges'
                                                                                                                ];
                                                                                                                $realizations = [
                                                                                                                    'soon' => 'Schnellstmöglich', '3' => '3 Monate', '6' => '6 Monate', 'other' => 'Sonstiges'
                                                                                                                ];

                                                                                                                $service = $services[$product->service] ?? $product->service;
                                                                                                                $realization = $realizations[$product->realization_time] ?? $product->realization_time;
                                                                                                                $department = $product->department_name ?? 'Keine Abteilung zugewiesen';
                                                                                                                $status = $product->status ?? 'send';
                                                                                                                $productCreated = \Carbon\Carbon::parse($product->product_created)->isoFormat('DD.MM.YYYY');

                                                                                                                // Helper: build employee image
                                                                                                               $empImg = function ($emp, $fallbackMale, $fallbackFemale) {
                                                                                                                    if (!$emp) return $fallbackMale;
                                                                                                                    $gender = $emp->gender ?? 'Male';
                                                                                                                    $img = $emp->image ?? null;
                                                                                                                    $default = $gender === 'Male' ? $fallbackMale : $fallbackFemale;
                                                                                                                    return (!empty($img) && file_exists('images/employee/' . $img))
                                                                                                                        ? asset('images/employee/' . $img)
                                                                                                                        : $default;
                                                                                                                };


                                                                                                                $fallbackMale = asset('images/gender/male.png');
                                                                                                                $fallbackFemale = asset('images/gender/female.png');

                                                                                                                $inner = $productEmployees[$product->employee_id] ?? null;
                                                                                                                $field = $productEmployees[$product->field_employee] ?? null;

                                                                                                                $innerImg = $empImg($inner, $fallbackMale, $fallbackFemale);
                                                                                                                $fieldImg = $empImg($field, $fallbackMale, $fallbackFemale);

                                                                                                                $innerName = trim(($inner->name ?? '') . ' ' . ($inner->lastname ?? ''));
                                                                                                                $fieldName = trim(($field->name ?? '') . ' ' . ($field->lastname ?? ''));
                                                                                                            @endphp

                                                                                                            <div class="d-flex flex-column align-items-center mx-2 mb-3">

                                                                                                                {{-- Chain layout --}}
                                                                                                                <div class="d-flex align-items-center justify-content-center">
                                                                                                                    {{-- Product circle --}}
                                                                                                                    <div class="circle" data-toggle="tooltip" title="{{ $product->article_group }}">
                                                                                                                        {{ $product->initial }}
                                                                                                                    </div>

                                                                                                                    <div class="line"></div>

                                                                                                                    {{-- Inner employee --}}
                                                                                                                    <div data-toggle="tooltip" title="{{ $innerName ?: 'Innendienst – Nicht zugewiesen' }}">
                                                                                                                        <img src="{{ $innerImg }}" alt="Innendienst"
                                                                                                                            class="@if($status=='accept') profile @elseif($status=='reject') profile-r @else profile-s @endif">
                                                                                                                    </div>

                                                                                                                    <div class="line"></div>

                                                                                                                    {{-- Field employee --}}
                                                                                                                    <div data-toggle="tooltip" title="{{ $fieldName ?: 'Außendienst – Nicht zugewiesen' }}">
                                                                                                                        <img src="{{ $fieldImg }}" alt="Außendienst"
                                                                                                                            class="@if($status=='accept') profile @elseif($status=='reject') profile-r @else profile-s @endif">
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                {{-- Texts below --}}
                                                                                                                <div class="text mt-1">{{ $service }}</div>
                                                                                                                <div class="text text-muted" style="font-size:9px;">{{ $department }}</div>

                                                                                                                <div class="text mt-0" style="font-size:9px;">
                                                                                                                    @if($product->interest == 'interest')
                                                                                                                        <img src="{{ asset('images/icons/kaufinteresse.svg') }}" alt="" style="width:14px;"> Kaufinteresse
                                                                                                                    @elseif($product->interest == 'intent')
                                                                                                                        <img src="{{ asset('images/icons/kaufabsicht.svg') }}" alt="" style="width:14px;"> Kaufabsicht
                                                                                                                    @elseif($product->interest == 'option')
                                                                                                                        <img src="{{ asset('images/icons/kaufoption.svg') }}" alt="" style="width:14px;"> Kaufoption
                                                                                                                    @endif
                                                                                                                    @if($product->realization_time)
                                                                                                                        <i class="ficon feather icon-calendar primary"></i> {{ $realization }}
                                                                                                                    @endif
                                                                                                                    · {{ $productCreated }}
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    </div>
                                                                                                    </td>


                                                                                            <td> 
                                                                                               <div class="mt-1">
                                                                                                    <div id="submenu-{{ $item->id }}-{{ $alter->id }}"
                                                                                                            class="js-menu inline-block"
                                                                                                            data-menu-id="submenu-{{ $item->id }}-{{ $alter->id }}"
                                                                                                            data-menu-scope="lead-{{ $item->id }}"   {{-- ← same scope as main --}}
                                                                                                            data-menu-align="end"
                                                                                                            data-menu-portal="true"
                                                                                                            data-menu-offset="0,8"
                                                                                                            data-menu-placement="top">
                                                                                                        <button id="subtoggle-{{ $item->id }}-{{ $alter->id }}"
                                                                                                                type="button"
                                                                                                                class="btn dropdown-toggle dropdown-toggle-split waves-effect waves-light js-menu-toggle"
                                                                                                                aria-haspopup="true"
                                                                                                                aria-expanded="false"
                                                                                                                aria-controls="subpanel-{{ $item->id }}-{{ $alter->id }}">
                                                                                                            <i class="feather icon-menu dropdown-icon"></i>
                                                                                                        </button>
                                                                                                        <div id="subpanel-{{ $item->id }}-{{ $alter->id }}" class="dropdown-menu js-menu-panel" role="menu"
                                                                                                            aria-labelledby="subtoggle-{{ $item->id }}-{{ $alter->id }}">


                                                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                                                            <a class="dropdown-item" href="{{ url('/new_lead_edit/'.$item->id.'/'.$alter->id) }}">
                                                                                                            <i class="feather icon-edit"></i> Bearbeiten
                                                                                                            </a>
                                                                                                        @endif

                                                                                                        @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Customer')->where('is_delete', 'on')->exists())
                                                                                                            <a href="javascript:void(0)"
                                                                                                            class="dropdown-item addNewProduct js-action"
                                                                                                            data-action="open-modal"
                                                                                                            data-id="{{ $item->id }}"
                                                                                                            data-alternative-id="{{ $alter->id }}"
                                                                                                            data-target="#addProductModal">
                                                                                                            <i class="feather icon-plus text-success"></i> Produkt Erstellen
                                                                                                            </a>
                                                                                                        @endif

                                                                                                        <a href="javascript:void(0)"
                                                                                                            class="dropdown-item customer_fund js-action"
                                                                                                            data-action="customer-fund"
                                                                                                            data-id="{{ $item->id }}"
                                                                                                            data-alternative-id="{{ $alter->id }}">
                                                                                                            <i class="feather icon-plus text-success"></i> Förderungen
                                                                                                        </a>

                                                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                                                            @if(Route::currentRouteName() != 'deleted.leads')
                                                                                                            <a class="dropdown-item"
                                                                                                                data-toggle="modal"
                                                                                                                data-target="#delete-alter{{ $alter->id }}">Löschen
                                                                                                            </a>
                                                                                                            @else
                                                                                                            <a href="javascript:void(0)"
                                                                                                                class="dropdown-item js-action"
                                                                                                                data-action="open-modal"
                                                                                                                data-target="#delete-alter{{ $alter->id }}">
                                                                                                                <i class="feather icon-trash-2"></i> Wiederherstellen
                                                                                                            </a>
                                                                                                            @endif
                                                                                                        @endif

                                                                                                        {{-- Optional Junk/Unjunk (uncomment if you want them here)
                                                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                                                            @if($item->status!="Junk")
                                                                                                            <a href="javascript:void(0)" class="dropdown-item js-action" data-action="open-modal" data-target="#alter_junk{{ $alter->id }}">
                                                                                                                <i class="fa fa-power-off danger"></i> Junk
                                                                                                            </a>
                                                                                                            @else
                                                                                                            <a href="javascript:void(0)" class="dropdown-item js-action" data-action="open-modal" data-target="#alter_unjunk{{ $alter->id }}">
                                                                                                                <i class="fa fa-power-off primary"></i> Unjunk
                                                                                                            </a>
                                                                                                            @endif
                                                                                                        @endif
                                                                                                        --}}
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    </div>


                                                                                                <div class="modal fade" id="delete-alter{{$alter->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                        <div class="modal-content">
                                                                                                            <div class="modal-header bg-danger white">
                                                                                                                <h5 class="modal-title" id="myModalLabel120"> @if(Route::currentRouteName() != 'deleted.leads') Objekt Löschen @else Wiederherstellen @endif</h5>
                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                    <span aria-hidden="true">×</span>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                            @if(Route::currentRouteName() != 'deleted.leads')
                                                                                                            <div class="modal-body">
                                                                                                                <h5>Aufzeichnung löschen</h5>
                                                                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                                                <p>Die Datensatznummer lautet:{{$item->customer_no}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                                                <p>Die Objektname: {{$alter->object_name}}</p>
                                                                                                            </div>
                                                                                                            <div class="modal-footer">
                                                                                                                <a type="button" href="{{url('/delete_lead_alternative').'/'.$alter->id}}" class="btn btn-danger">Ja</a>
                                                                                                            </div>
                                                                                                            @else

                                                                                                    <div class="modal-body">
                                                                                                            <h5>Daten wiederherstellen: </h5>
                                                                                                            <p>Möchten Sie diese Daten wirklich wiederherstellen?</p>
                                                                                                            <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                                            </div>
                                                                                                            <div class="modal-footer">
                                                                                                                <a type="button" href="{{url('/restore_alternative_leads').'/'.$alter->id}}" class="btn btn-danger">Ja</a>
                                                                                                            </div>
                                                                                                            @endif
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div> 
                                                                                            </td>  
                                                                                        </tr>   
                                                                                    @endforeach
                                                                                </tbody> 
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr> 
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                        </div>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $data->appends(request()->query())->links() }}
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Table head options end -->
                </div>
            </div>
        </div>
    </div>
</div>
 
        <!-- Accept Modal  -->
        <div class="modal fade text-left acceptModal" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Antwort</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('accept.lead')}}">
                        @csrf
                    <div class="modal-body"> 
                        <input type="hidden" name="customer_id" value="">
                        <input type="hidden" name="product_name" id="product_name" value="">
                        <input type="hidden" name="employee_id" value="" >
                        <input type="hidden" name="product_id" value="" >
                        <input type="hidden" name="service" value="" >
                        <input type="hidden" name="product_list" value="" >
                        <input type="hidden" name="alternative_id" value="" >
                        <label for="">Antwort</label>
                        <select name="response" id="" class="form-control">
                            <option value="accept">akzeptieren</option>
                            <option value="reject">Nicht akzeptieren</option>
                        </select>
                        <label for="">Grund <code><small>.Erforderlich, wenn Sie den Job ablehnen</small></code></label>
                        <textarea name="reason" id="" cols="30" rows="10" class="form-control"></textarea>  
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light" >Speichern</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Accept Modal  -->
       

            <!-- ===== Add Product Modal (custom) ===== -->
            <div class="cmodal" id="addProductModal" aria-hidden="true">
            <div class="cmodal__backdrop" data-modal-close></div>

            <div class="cmodal__dialog" role="dialog" aria-modal="true" aria-labelledby="addProductTitle">
                <form id="addProductForm" action="{{ route('lead.products.save') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_id" id="modal_customer_id">
                <input type="hidden" name="alternative_id" id="modal_alternative_id">

                <div class="cmodal__header bg-primary text-white">
                    <h5 class="cmodal__title" id="addProductTitle">Produkt hinzufügen</h5>
                    <button type="button" class="cmodal__close text-white" data-modal-close aria-label="Close">×</button>
                </div>

                <div class="cmodal__body">
                    <table class="table table-bordered">
                    <thead>
                        <tr>
                        <th>Produkt</th>
                        <th>Dienstleistung</th>
                        <th>Abteilung</th>
                        <th>Innendienst</th>
                        <th>Außendienst</th>
                        <th>Interesse</th>
                        <th>Realisierungszeit</th>
                        <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody id="existingProductRows"></tbody>
                    <tbody id="modalNewRows"></tbody>
                    </table>

                    <button type="button" class="btn btn-sm btn-success" id="modalAddRow">+ Neue Zeile</button>
                </div>

                <div class="cmodal__footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
                </form>
            </div>
            </div>

            <!-- ===== Edit Product Modal (custom) ===== -->
            <div class="cmodal" id="editProductModal" aria-hidden="true">
            <div class="cmodal__backdrop" data-modal-close></div>

            <div class="cmodal__dialog sm" role="dialog" aria-modal="true" aria-labelledby="editProductTitle">
                <form id="editProductForm" method="POST" action="{{ route('lead.products.update') }}">
                @csrf
                <input type="hidden" name="id" id="edit_product_id">

                <div class="cmodal__header bg-warning text-white">
                    <h5 class="cmodal__title" id="editProductTitle">Produkt bearbeiten</h5>
                    <button type="button" class="cmodal__close text-white" data-modal-close aria-label="Close">×</button>
                </div>

                <div class="cmodal__body">
                    <div class="form-group">
                    <label>Produkt</label>
                    <select name="product_id" id="edit_product" class="form-control select2">
                        <option value="">Wählen...</option>
                        @foreach($productInfo as $p)
                        <option value="{{ $p->id }}">{{ $p->article_group }}</option>
                        @endforeach
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Dienstleistung</label>
                    <select name="service_id" id="edit_service" class="form-control select2">
                        <option value="">Wählen...</option>
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Abteilung</label>
                    <select name="department_id" id="edit_department" class="form-control select2">
                        <option value="">Wählen...</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->department_name }}</option>
                        @endforeach
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Innendienst</label>
                    <select name="employee_id" id="edit_employee" class="form-control select2">
                        <option value="">Wählen...</option>
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Außendienst</label>
                    <select name="field_employee" id="edit_field_employee" class="form-control select2">
                        <option value="">Wählen...</option>
                    </select>
                    </div>

                    <div class="form-group">
                    <label>Interesse</label>
                    <select name="interest" id="edit_interest" class="form-control select2"></select>
                    </div>

                    <div class="form-group">
                    <label>Realisierungszeit</label>
                    <select name="realization_time" id="edit_realization_time" class="form-control select2"></select>
                    </div>
                </div>

                <div class="cmodal__footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Abbrechen</button>
                    <button type="submit" class="btn btn-warning">Aktualisieren</button>
                </div>
                </form>
            </div>
            </div>


        <div id="fundingSidebar" class="funding-content p-4" style="background-color:rgb(239, 239, 239); color:rgb(34, 34, 34); font-family: sans-serif;">
            <div class="container-fluid">
                <div class="d-flex justify-content-end mb-2">
                    <button onclick="maximizeSidebar()" class="btn btn-sm btn-secondary me-2">
                        <i class="feather icon-maximize"></i>
                    </button>
                    <button onclick="closeSidebar()" class="btn btn-sm btn-danger">
                        <i class="feather icon-x"></i>
                    </button>
                    </div>
                <!-- Top Section: Customer Info + Percent Cards -->
                <div class="row g-4 mb-4">
                <!-- Customer Info -->
                <div class="col-md-4">
                    <div class="card shadow-sm p-3 border-0"  >
                    <div class="d-flex align-items-center">
                            <div>
                        <h5 class="card-title mb-0" data-role="customer_name">-</h5>
                        <small class="text-muted d-block" data-role="house_type">Haus: -</small>
                        <small class="text-muted d-block" data-role="product_name">Produkt: -</small>
                        <small class="text-muted d-block" data-role="installation_year">Installation: -</small>
                        </div>
                    </div>
                    </div>
                </div>

                </div>

                <!-- Details and Chart -->
                <div class="row g-4">
                <!-- Building Details -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm"  >
                    <div class="card-header bg-primary text-white fw-bold">Gebäudedetails & Voraussetzungen</div>
                    <div class="card-body px-3 py-2">
                        <div class="chat-box">
                            <div id="chat"></div>
                            <div class="user-input">
                            <div id="input-wrapper"></div>
                            <!-- <button class="btn btn-success" onclick="nextQuestion()" >Weiter</button> -->
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                    <!-- Chart Section -->
                    <div class="col-md-7">
                        <div class="card shadow-sm border-0">
                            <div class="card-body" id="foerderResult"></div>
                        </div>
                    </div>


                </div>
            </div>
        </div> 
            <!-- Update Umsatz Modal -->
        <div class="modal fade" id="updateUmsatzModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <form id="updateUmsatzForm" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Umsatz bearbeiten</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="um-customer-id">
                    <div class="form-group">
                    <label for="um-total">Gesamtumsatz (EUR)</label>
                    <input type="text" class="form-control" id="um-total" name="total_purchase" placeholder="z. B. 12.345,67">
                    <small class="form-text text-muted">Dezimal mit Komma oder Punkt möglich.</small>
                    <div class="invalid-feedback" id="um-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Abbrechen</button>
                    <button class="btn btn-primary" type="submit">Speichern</button>
                </div>
                </form>
            </div>
        </div>
           
        {{-- DUPLICATE CUSTOMER DRAWER --}}
        <div id="duplicateDrawerBackdrop" class="dup-drawer-backdrop">
            <div class="dup-drawer">
                <div class="dup-drawer__header">
                    <div>
                        <h5 class="mb-0">Doppelte Kunden</h5>
                        <small class="text-muted">Kunden mit gleicher E-Mail / Telefonnummer.</small>
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-outline-light dup-drawer__close"
                            id="btnCloseDuplicates">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="dup-drawer__body" id="duplicateCards">
                    {{-- Cards will be injected via JS --}}
                </div>
            </div>
        </div>


        <div class="modal fade" id="customerFeedModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content feed-modal">
                <div class="modal-header feed-modal-header">
                    <div>
                    <h5 class="modal-title d-flex align-items-center">
                        <span class="feed-modal-title-icon mr-2">
                        <i class="feather icon-activity"></i>
                        </span>
                        <span data-feed-modal-title>Aktivitäten</span>
                    </h5>
                    <small class="text-muted" data-feed-modal-subtitle>Letzte Aktivitäten des Kunden.</small>
                    </div>
                    <button type="button" class="close feed-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body feed-modal-body">
                    <!-- Toolbar -->
                    <div class="feed-modal-toolbar">
                    <div class="form-inline mb-2 mr-3">
                        <label class="mr-2 small text-muted">Typ</label>
                        <select class="form-control " data-feed-modal-kind>
                        <option value="all">Alle</option>
                        <option value="product">Produkte</option>
                        <option value="appointment">Termine</option>
                        <option value="task">Aufgaben</option>
                        <option value="ticket">Tickets</option>
                        <option value="history">Historie</option>
                        </select>
                    </div>

                    <div class="form-inline mb-2 mr-3">
                        <label class="mr-2 small text-muted">Sortierung</label>
                        <select class="form-control " data-feed-modal-sort>
                        <option value="date_desc">Neueste zuerst</option>
                        <option value="date_asc">Älteste zuerst</option>
                        <option value="title_asc">Titel A–Z</option>
                        <option value="status_asc">Status A–Z</option>
                        </select>
                    </div>

                    <div class="feed-modal-search mb-2">
                        <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                            <i class="feather icon-search"></i>
                            </span>
                        </div>
                        <input type="text"
                                class="form-control"
                                placeholder="Suchen in Titel, Text, Status…"
                                data-feed-modal-search>
                        </div>
                    </div>
                    </div>

                    <!-- List -->
                    <div class="feed-modal-list" data-feed-modal-list>
                    <!-- Filled via JS -->
                    </div>
                </div>
                </div>
            </div>
        </div>

     

        <div id="massProductModal" class="custom-modal-overlay">
            <div class="custom-modal-content">
                
                <div class="custom-modal-header">
                    <h3><i class="feather icon-settings"></i> Produkt & Objekt Manager</h3>
                    <span class="custom-close-btn" id="closeMassManager">&times;</span>
                </div>

                <div class="custom-modal-body">
                    
                    <div class="cm-filter-bar">
                        <div style="flex: 1;">
                            <label>Quelle (Source)</label>
                            <select id="cmFilterSource" class="cm-input">
                                <option value="">-- Quelle wählen --</option>
                                <option value="Moser">Moser</option>
                                <option value="Website">Website</option>
                                <option value="Recommendation">Empfehlung</option>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label>Produkt Filter</label>
                            <select id="cmFilterProduct" class="cm-input">
                                <option value="">-- Alle Produkte --</option>
                                {{-- $productInfo MUST exist in the main controller that loads this page --}}
                                @foreach($productInfo as $p)
                                    <option value="{{ $p->id }}">{{ $p->article_group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label>Suche (Name/Stadt)</label>
                            <input type="text" id="cmFilterSearch" class="cm-input" placeholder="Name oder Stadt...">
                        </div>
                        <div>
                            <button id="cmBtnLoad" class="btn-cm-primary">
                                <i class="feather icon-search"></i> Laden
                            </button>
                        </div>
                    </div>

                    <div id="cmResultsArea">
                        <div style="text-align:center; color:#999; margin-top:50px;">
                            <i class="feather icon-filter" style="font-size:40px;"></i><br>
                            Bitte Filter wählen und auf "Laden" klicken.
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="myCustomerStatsModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header" style="background: #fff; border-bottom: 1px solid #f1f5f9;">
                        <h5 class="modal-title fw-bold">
                            <i class="feather icon-user-check text-primary mr-1"></i> Meine Kunden Übersicht
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">Hier sehen Sie eine Aufschlüsselung Ihrer zugewiesenen Kunden nach Verantwortlichkeit.</p>

                        <div class="mc-stat-card">
                            <div class="mc-icon-box mc-icon-contact">
                                <i class="feather icon-user"></i>
                            </div>
                            <div class="mc-stat-info">
                                <h6>Als Ansprechpartner</h6>
                                <h3>{{ $myCounts['contact'] ?? 0 }}</h3>
                            </div>
                        </div>

                        <div class="mc-stat-card">
                            <div class="mc-icon-box mc-icon-inner">
                                <i class="feather icon-monitor"></i>
                            </div>
                            <div class="mc-stat-info">
                                <h6>Im Innendienst</h6>
                                <h3>{{ $myCounts['inner'] ?? 0 }}</h3>
                            </div>
                        </div>

                        <div class="mc-stat-card">
                            <div class="mc-icon-box mc-icon-field">
                                <i class="feather icon-briefcase"></i>
                            </div>
                            <div class="mc-stat-info">
                                <h6>Im Außendienst</h6>
                                <h3>{{ $myCounts['field'] ?? 0 }}</h3>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="font-weight-bold text-dark">Gesamt (Eindeutig)</span>
                            <span class="badge badge-primary badge-pill" style="font-size: 1rem; padding: 8px 12px;">
                                {{ $myCounts['total_unique'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                        <button type="button" class="btn btn-primary" onclick="$('#chkMyCustomers').click(); $('#myCustomerStatsModal').modal('hide');">
                            Jetzt filtern
                        </button>
                    </div>
                </div>
            </div>
        </div>



<!-- END: Content-->
@endsection
 
@section('script')  
 <!-- Accordian:start  -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.querySelectorAll('.accordion-row').forEach(row => {
        row.addEventListener('click', () => {
            const rowId = row.dataset.row;
            const contentRow = document.querySelector(`.accordion-content[data-row="${rowId}"]`);
            contentRow.classList.toggle('visible');
        });
    });
</script>

<script>
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script>


<script>
    $(document).ready(function() {
        $('.articles input[type="radio"]').on('change', function() {
            // Reset styles for all labels
            $('.articles input[type="radio"] + label').css({
                'background': '#b1aaaa',
                'color': 'inherit',
                'border-radius': '50%'
            });

            // Apply styles for the selected label
            if (this.checked) {
                $(this).next('label').css({
                    'background': '#92b532',
                    'color': 'white',
                    'border-radius': '50%'
                });

                // Send AJAX request
                let articleGroup = $(this).val();
                $.ajax({
                    url: '/customer_details', // Your endpoint for searching article group
                    method: 'GET',
                    data: { search: articleGroup, is_ajax: true },
                    success: function(response) {
                        // Handle the response here
                        console.log(response);
                        // Update the page content based on the response
                        $('#results').html(response); // Assuming 'results' is the id of the element where you want to display the results
                    },
                    error: function(error) {
                        // Handle the error here
                        console.error(error);
                    }
                });
            }
        });
    });
</script>

<!-- Showing the accept button modal: start -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add an event listener for all buttons with the class "acceptModal"
    document.querySelectorAll('.btn.acceptModal').forEach(function(button) {
        button.addEventListener('click', function(event) {
            // Get the modal element by its class
            const modal = document.querySelector('.modal.acceptModal');

            // Retrieve data attributes from the button
            const customerId = button.getAttribute('data-id');
            const productName = button.getAttribute('data-product-name');
            const currentEmployee = button.getAttribute('data-employee');
            const productId = button.getAttribute('data-product-id');
            const service = button.getAttribute('data-service');
            const productList = button.getAttribute('data-product-list');
            const alternativeId = button.getAttribute('data-alternative-id');

            // Populate the modal inputs with the retrieved data
            modal.querySelector('input[name="customer_id"]').value = customerId;
            modal.querySelector('input[name="product_name"]').value = productName;
            modal.querySelector('input[name="employee_id"]').value = currentEmployee;
            modal.querySelector('input[name="product_id"]').value = productId;
            modal.querySelector('input[name="service"]').value = service;
            modal.querySelector('input[name="product_list"]').value = productList;
            modal.querySelector('input[name="alternative_id"]').value = alternativeId;

            // Show the modal if not automatically triggered
            $(modal).modal('show');
        });
    });
});

</script>
<!-- Showing the accept button modal: end -->
<!-- deleteing the responsible and product from the list of empoyee modal:start  -->
<script>
    document.addEventListener('DOMContentLoaded', function () { 
        // Handle delete button click and set modal input values
        document.querySelectorAll('.delete-responsible').forEach(function(button) {
            button.addEventListener('click', function() {
                const responsibleId = this.dataset.responsible || '';
                const productId = this.dataset.product || '';
                const alternativeId = this.dataset.alternative || '';

                // Set hidden input values in the modal
                document.querySelector('input[name="employee"]').value = responsibleId;
                document.querySelector('input[name="product"]').value = productId;
                document.querySelector('input[name="alternative"]').value = alternativeId;
            });
        });

        // Handle the confirm delete button click
        document.querySelectorAll('[id^="confirmDelete"]').forEach(function(deleteButton) {
            deleteButton.addEventListener('click', function () {
                const modalId = this.id.replace('confirmDelete', '');
                const responsibleId = document.querySelector('input[name="employee"]').value;
                const productId = document.querySelector('input[name="product"]').value;

                // Check which radio button is selected
                const deleteEmployeeRadio = document.getElementById(`deleteEmployee${modalId}`).checked;
                const deleteProductRadio = document.getElementById(`deleteProduct${modalId}`).checked;

                let url = '';

                if (deleteEmployeeRadio && responsibleId) {
                    url = `/delete_lead_responsible/${responsibleId}`;
                } else if (deleteProductRadio && productId) {
                    url = `/delete_lead_product/${productId}`;
                } else {
                    alert('Bitte wählen Sie eine gültige Option aus.');
                    return;
                }

                // Update the href attribute on the button
                this.setAttribute('href', url);
            });
        });
    });
</script>


 
<!-- deleteing the responsible and product from the list of empoyee modal:end  -->

<!-- seding the customer to planing phase: start:  -->
<script>
  document.querySelectorAll('.sendToPlaning').forEach(button => {
    button.addEventListener('click', function() {
        // Get data from button attributes
        const employeeId = this.getAttribute('data-employee');
        const productId = this.getAttribute('data-product');
        const customerId = this.getAttribute('data-customer');
        const service = this.getAttribute('data-service');
        const productList = this.getAttribute('data-product-list');

        Swal.fire({
            title: "Bist du sicher?",
            text: "Möchten Sie diesen Kunden zur Planung senden?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ja, senden!",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform POST request
                fetch("{{ route('planing.save') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        employee_id: employeeId,
                        product_id: productId,
                        customer_id: customerId,
                        service: service,
                        product_list: productList,
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP-Fehler! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            "Erfolg!",
                            "Der Kunde wurde erfolgreich zur Planung gesendet.",
                            "success"
                        ).then(() => {
                            location.reload();  // Refresh the page on success
                        });
                    } else {
                        Swal.fire(
                            "Fehler!",
                            data.error || "Beim Senden des Kunden ist ein Fehler aufgetreten.",
                            "error"
                        );
                    }
                })
                .catch((error) => {
                    console.error("Fehler:", error);
                    Swal.fire(
                        "Fehler!",
                        "Ein unerwarteter Fehler ist aufgetreten.",
                        "error"
                    );
                });
            }
        });
    });
});

</script>
<!-- seding the customer to planing phase: end:  -->

<script>
document.getElementById('colaps').addEventListener('click', function() {
    var section = document.getElementById('upper_view');
    var icon = this.querySelector('i');
    
    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        icon.classList.remove('feather', 'icon-chevron-down');
        icon.classList.add('feather', 'icon-chevron-up');
    } else {
        section.style.display = 'none';
        icon.classList.remove('feather', 'icon-chevron-up');
        icon.classList.add('feather', 'icon-chevron-down');
    }
});
</script>

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
        // Pass the PHP data to JavaScript
        const statusCounts = @json($statusCounts);

        // Data for Chart.js
        const data = {
            labels: ['Open', 'Active', 'Inactive', 'Ended', 'Cancel'],
            datasets: [{
                data: [
                    statusCounts.open,
                    statusCounts.active,
                    statusCounts.inactive,
                    statusCounts.ended,
                    statusCounts.cancel
                ],
                backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#cc65fe', '#ff9f40']
            }]
        };

        // Config for Chart.js
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' (' + tooltipItem.raw + '%)';
                            }
                        }
                    }
                }
            },
        };

        // Render the pie chart
        window.onload = function() {
            const ctx = document.getElementById('statusPieChart').getContext('2d');
            new Chart(ctx, config);
        };
    </script>



<!-- Adding new Responsible  -->

 <script>
   $(document).ready(function () {
    let newLeadId = null;
    let productId = null;
    let alternative = null;

    // Open the modal and load employees
    $('[data-target="#addEmployee"]').on('click', function () {
        const employeeId = $(this).data('employee-id'); // Current employee ID
        newLeadId = $(this).data('new-lead-id'); // Lead ID
        productId = $(this).data('product-id'); // Product ID
        alternative = $(this).data('alternative-id'); // Product ID

        // Populate hidden inputs in the modal
        $('#modalEmployeeId').val(employeeId);
        $('#modalProductId').val(productId);
        $('#modalLeadId').val(newLeadId);
        $('#modalAlternativeId').val(alternative);

        // Fetch available employees via AJAX
        $.ajax({
            url: '/checkEmployeeAvailability',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                product_id: productId
            },
            success: function (response) {
                // Clear previous options
                $('#employeeSelect').empty();

                // Populate select2 with available employees or fallback
                const employees = response.availableEmployees.length > 0 
                    ? response.availableEmployees 
                    : response.inCaseEmployees;

                if (employees.length > 0) {
                    employees.forEach(employee => {
                        $('#employeeSelect').append(new Option(
                            `${employee.name} ${employee.lastname}`,
                            employee.id
                        ));
                    });
                } else {
                    toastr.warning('No employees found for this product.');
                }

                // Initialize or refresh select2
                $('#employeeSelect').select2();
            },
            error: function (xhr) {
                toastr.error('Failed to fetch employees. Please try again.');
                console.error(xhr.responseText);
            }
        });
    });
});

 </script>


<script>
    $(document).ready(function () {
    loadEmployees(); // Call function to fetch employees

    function loadEmployees() {
        $.ajax({
            url: "/getEmployees",
            method: "POST",
            data: { _token: $('meta[name="csrf-token"]').attr("content") },
            success: function (response) {
                console.log("📌 Employees Loaded:", response); // Debugging

                let employees = response.map(emp => {
                    return {
                        id: emp.id,
                        text: `${emp.name} ${emp.lastname}`,
                        image: emp.image ? `/images/employees/${emp.image}` : "/images/default-user.png"
                    };
                });

                // Initialize Select2 with images
                $("#employeeSelect").select2({
                    data: employees,
                    templateResult: formatEmployee,  // Customize how options appear
                    templateSelection: formatEmployeeSelection, // Customize selected item
                    escapeMarkup: function (m) { return m; }, // Allow HTML rendering
                    width: '100%'
                });
            },
            error: function (xhr, status, error) {
                console.error("❌ Error fetching employees:", error);
            }
        });
    }

    // Function to format options in the dropdown
    function formatEmployee(employee) {
        if (!employee.id) {
            return employee.text;
        }
        return $(`<span><img src="${employee.image}" class="employee-img" /> ${employee.text}</span>`);
    }

    // Function to format the selected item
    function formatEmployeeSelection(employee) {
        return $(`<span><img src="${employee.image}" class="employee-img-small" /> ${employee.text}</span>`);
    }
});

</script>
 

<script>
(() => {
  'use strict';
  if (window.__LEAD_PRODUCTS_MODAL_BOOT__) return;
  window.__LEAD_PRODUCTS_MODAL_BOOT__ = true;

  /* ===== Boot data from Blade ===== */
  const STAGE       = @json($stage ?? 'lead');
  const SERVICES    = @json($serviceList ?? []);
  const PRODUCTS    = @json($productInfo ?? []);
  const DEPARTMENTS = @json($departments ?? []);
  const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

  const EMP_IMG_DIR = "{{ asset('images/employee') }}";
  const DEFAULT_AVA = "{{ asset('images/gender/male.png') }}";

  /* ===== Custom modal refs ===== */
  const $addModal  = $('#addProductModal');   // your custom modal wrapper with .cmodal
  const $editModal = $('#editProductModal');  // your custom modal wrapper with .cmodal
  const addDropdownParent  = $('#addProductModal .cmodal__dialog');
  const editDropdownParent = $('#editProductModal .cmodal__dialog');

  function openModal($m){
    if (!$m || !$m.length) return;
    $m.addClass('is-open').attr('aria-hidden','false');
    document.body.classList.add('cmodal-open');
  }
  function closeModal($m){
    if (!$m || !$m.length) return;
    $m.removeClass('is-open').attr('aria-hidden','true');
    if (!$('.cmodal.is-open').length) document.body.classList.remove('cmodal-open');
  }

  // close: click backdrop or any [data-modal-close]
  $(document).on('click', '[data-modal-close]', function(){
    const $m = $(this).closest('.cmodal');
    if ($m.length) closeModal($m);
  });

  // close: ESC closes top-most open modal
  $(document).on('keydown', function(e){
    if (e.key === 'Escape') {
      const $m = $('.cmodal.is-open').last();
      if ($m.length) closeModal($m);
    }
  });

  /* ===== Label helpers ===== */
  const SERVICE_LABEL = {
    complete:'Komplettlösung', montage:'Montage', product:'Verkauf', plan:'Planung',
    maintenance:'Wartung', repair:'Reparatur', reclaim:'Reklamation',
    emergency:'Notdienst', others:'Sonstiges'
  };
  const INTEREST_LABEL = { intent:'Kaufabsicht', interest:'Kaufinteresse', option:'Kaufoption' };
  const REALIZATION_LABEL = { soon:'Schnellstmöglich', '3':'3 Monate', '6':'6 Monate', other:'Sonstiges' };

  const tService     = k => SERVICE_LABEL[(k||'').toLowerCase()] || (k||'');
  const tInterest    = v => INTEREST_LABEL[v] || '-';
  const tRealization = v => REALIZATION_LABEL[String(v)] || '-';
  const empImg       = f => f ? `${EMP_IMG_DIR}/${f}` : DEFAULT_AVA;

  let modalRowIndex = 0;

  const $existing = $('#existingProductRows');
  const $newRows  = $('#modalNewRows');

  const debounce = (fn, ms) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; };
  const autoPickIfSingle = ($select) => {
    const opts = $select.find('option[value!=""]');
    if (!$select.val() && opts.length === 1) $select.val(opts.first().val()).trigger('change');
  };

  /* ===== Select2 template for employees ===== */
  function employeeTpl(opt){
    if (!opt.id) return opt.text;
    const $el = $(opt.element);
    const img = $el.data('img') ? `${EMP_IMG_DIR}/${$el.data('img')}` : DEFAULT_AVA;
    const pos = $el.data('positions') || '';
    return $(`
      <div style="display:flex;align-items:center;gap:8px;">
        <img src="${img}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
        <div><strong>${opt.text}</strong><br><small>${pos}</small></div>
      </div>
    `);
  }

  function s2($el, opts){
    if (!$el || !$el.length) return;
    if ($el.data('select2')) $el.select2('destroy');
    $el.select2(opts);
  }

  /* ===== Open Add modal: preload saved rows and add one new row ===== */
  $(document).on('click', '.addNewProduct', function () {
    const customerId    = $(this).data('id');
    const alternativeId = $(this).data('alternative-id');

    $('#modal_customer_id').val(customerId);
    $('#modal_alternative_id').val(alternativeId);

    $existing.empty();
    $newRows.empty();
    modalRowIndex = 0;

    $.get(`/lead/get/products/${customerId}/${alternativeId}`, (rows = []) => {
      $existing.empty();
      rows.forEach(row => {
        const svcKey = row.service_phase_section
          || (SERVICES.find(s => String(s.id) === String(row.service_id))?.phase_section)
          || row.service_id;

        $existing.append(`
          <tr>
            <td>${row.article_group || '-'}</td>
            <td>${tService(svcKey)}</td>
            <td>${row.department_name || '-'}</td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <img src="${empImg(row.image)}" style="width:32px;height:32px;border-radius:50%;">
                <span>${(`${row.name??''} ${row.lastname??''}`).trim() || '-'}</span>
              </div>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <img src="${empImg(row.feimage)}" style="width:32px;height:32px;border-radius:50%;">
                <span>${(`${row.fename??''} ${row.felastname??''}`).trim() || '-'}</span>
              </div>
            </td>
            <td>${tInterest(row.interest)}</td>
            <td>${tRealization(row.realization_time)}</td>
            <td>
              <button type="button" class="btn btn-sm btn-warning edit-product"
                data-id="${row.id}"
                data-product-id="${row.product_id}"
                data-service-id="${row.service_id ?? ''}"
                data-department-id="${row.department_id ?? ''}"
                data-employee-id="${row.employee_id ?? ''}"
                data-field-employee="${row.field_employee_id ?? row.field_employee ?? ''}"
                data-interest="${row.interest ?? ''}"
                data-realization-time="${row.realization_time ?? ''}">
                <i class="feather icon-edit"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger delete-product"
                data-id="${row.id}">
                <i class="feather icon-trash"></i>
              </button>
            </td>
          </tr>
        `);
      });
    });

    openModal($addModal);
    addRow();
  });

  /* ===== Add new editable row ===== */
  $('#modalAddRow').on('click', addRow);

  function addRow(){
    modalRowIndex++;
    $newRows.append(rowTemplate(modalRowIndex));
    initRow(modalRowIndex);
  }

  function rowTemplate(i){
    return `
      <tr data-index="${i}">
        <td>
          <select class="form-control product-select" data-index="${i}" name="product_id[]" style="width:100%">
            <option value="">Produkt wählen</option>
            ${PRODUCTS.map(p => `<option value="${p.id}" data-img="${p.image||''}">${p.article_group}</option>`).join('')}
          </select>
        </td>
        <td>
          <select class="form-control service-select" data-index="${i}" name="service_id[]" style="width:100%">
            <option value="">Service wählen</option>
          </select>
        </td>
        <td>
          <select class="form-control department-select" data-index="${i}" name="department_id[]" style="width:100%">
            <option value="">Abteilung wählen</option>
            ${DEPARTMENTS.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('')}
          </select>
        </td>
        <td>
          <select class="form-control employee-select" data-index="${i}" name="employee_id[]" style="width:100%">
            <option value="">Innendienst wählen</option>
          </select>
        </td>
        <td>
          <select class="form-control field-employee-select" data-index="${i}" name="field_employee[]" style="width:100%">
            <option value="">Außendienst wählen</option>
          </select>
        </td>
        <td>
          <select class="form-control interest-select" data-index="${i}" name="interest[]" style="width:100%">
            <option value="intent" selected>Kaufabsicht</option>
            <option value="interest">Kaufinteresse</option>
            <option value="option">Kaufoption</option>
          </select>
        </td>
        <td>
          <select class="form-control realization-select" data-index="${i}" name="realization_time[]" style="width:100%">
            <option value="">Bitte auswählen</option>
            <option value="soon" selected>Schnellstmöglich</option>
            <option value="3">3 Monate</option>
            <option value="6">6 Monate</option>
            <option value="other">Sonstiges</option>
          </select>
        </td>
        <td>
          <button type="button" class="btn btn-sm btn-outline-danger removeRow">
            <i class="feather icon-trash"></i>
          </button>
        </td>
      </tr>`;
  }

  /* ===== Select2 + per-row logic ===== */
  function initRow(i){
    const q = cls => $(`.${cls}[data-index="${i}"]`);
    const $prod = q('product-select');
    const $serv = q('service-select');
    const $dept = q('department-select');
    const $emp  = q('employee-select');
    const $femp = q('field-employee-select');

    // basic select2 (inside modal)
    [$prod,$serv,$dept,q('interest-select'),q('realization-select')].forEach($el => {
      s2($el, { width:'100%', dropdownParent: addDropdownParent });
    });

    // employee select2 (template + inside modal)
    [$emp,$femp].forEach($el => {
      s2($el, {
        width:'100%',
        dropdownParent: addDropdownParent,
        templateResult: employeeTpl,
        templateSelection: o => o.text,
        escapeMarkup: m => m
      });
    });

    $prod.off('change.modal').on('change.modal', () => onProductChanged(i));
    $serv.off('change.modal').on('change.modal', debounce(() => loadEmployees(i), 120));
    $dept.off('change.modal').on('change.modal', debounce(() => loadEmployees(i), 120));
  }

  async function onProductChanged(i){
    const $prod = $(`.product-select[data-index="${i}"]`);
    const $serv = $(`.service-select[data-index="${i}"]`);
    const $dept = $(`.department-select[data-index="${i}"]`);
    const pid   = $prod.val();

    const list = SERVICES.filter(s => String(s.product_id) === String(pid));
    $serv.empty().append('<option value="">Service wählen</option>');
    list.forEach(s => $serv.append(`<option value="${s.id}">${tService(s.phase_section)}</option>`));
    $serv.trigger('change');

    if (!pid) { renderBothEmployees(i, [], []); return; }

    try {
      const resp = await $.post('{{ route("inquiry.department.employees") }}', {
        _token: CSRF,
        product_id: pid,
        stage: STAGE
      });

      const suggestion = resp || {};
      let internalEmployees = [];
      let externalEmployees = [];

      if (Array.isArray(suggestion)) {
        internalEmployees = suggestion;
        externalEmployees = suggestion;
      } else {
        internalEmployees = suggestion.internal_employees || suggestion.employees || [];
        externalEmployees = suggestion.external_employees || suggestion.employees || [];
      }

      if (suggestion.department_id) {
        const did = String(suggestion.department_id);
        if (!$dept.find(`option[value="${did}"]`).length) {
          $dept.append(`<option value="${did}">${DEPARTMENTS.find(d=>String(d.id)===did)?.department_name || ('Abt. '+did)}</option>`);
        }
        $dept.val(did).trigger('change.select2');
      }

      if (suggestion.service_id) {
        const sid = String(suggestion.service_id);
        if (!$serv.find(`option[value="${sid}"]`).length) {
          const svc = SERVICES.find(s=>String(s.id)===sid);
          $serv.append(`<option value="${sid}">${tService(svc?.phase_section || '') || 'Service '+sid}</option>`);
        }
        $serv.val(sid).trigger('change.select2');
      }

      if (internalEmployees.length || externalEmployees.length) {
        renderBothEmployees(i, internalEmployees, externalEmployees);
      } else {
        loadEmployees(i);
      }

    } catch (e) {
      autoPickIfSingle($serv);
      autoPickIfSingle($dept);
      loadEmployees(i);
    }
  }

  function renderBothEmployees(i, internalList, externalList){
    const $emp  = $(`.employee-select[data-index="${i}"]`);
    const $femp = $(`.field-employee-select[data-index="${i}"]`);

    const render = ($select, placeholder, list) => {
      $select.empty().append(`<option value="">${placeholder}</option>`);
      (list || []).forEach(e => {
        $select.append(
          `<option value="${e.id}" data-img="${e.image||''}" data-positions="${(e.positions||[]).join(', ')}">
            ${e.name} ${e.lastname}
           </option>`
        );
      });
      s2($select, {
        width:'100%',
        dropdownParent: addDropdownParent,
        templateResult: employeeTpl,
        templateSelection: o => o.text,
        escapeMarkup: m => m
      });
      autoPickIfSingle($select);
    };

    render($emp,  'Innendienst wählen', internalList);
    render($femp, 'Außendienst wählen', externalList);
  }

  function loadEmployees(i){
    const $prod = $(`.product-select[data-index="${i}"]`);
    const $serv = $(`.service-select[data-index="${i}"]`);
    const $dept = $(`.department-select[data-index="${i}"]`);

    const pid = $prod.val();
    const sid = $serv.val();
    const did = $dept.val();

    if (!pid) { renderBothEmployees(i, [], []); return; }

    $.post('{{ route("inquiry.department.employees") }}', {
      _token: CSRF,
      product_id: pid,
      service_id: sid || null,
      department_id: did || null,
      stage: STAGE
    }, (resp = {}) => {
      let internalEmployees = [];
      let externalEmployees = [];

      if (Array.isArray(resp)) {
        internalEmployees = resp;
        externalEmployees = resp;
      } else {
        if (resp.department_id && String(resp.department_id) !== String(did || '')) {
          $dept.val(String(resp.department_id)).trigger('change.select2');
        }
        if (resp.service_id && String(resp.service_id) !== String(sid || '')) {
          $serv.val(String(resp.service_id)).trigger('change.select2');
        }
        internalEmployees = resp.internal_employees || resp.employees || [];
        externalEmployees = resp.external_employees || resp.employees || [];
      }

      renderBothEmployees(i, internalEmployees, externalEmployees);
    }).fail(() => {
      renderBothEmployees(i, [], []);
      Swal.fire({ icon:'error', title:'Fehler', text:'Mitarbeiter konnten nicht geladen werden.' });
    });
  }

  /* ===== Remove new row ===== */
  $(document).on('click', '.removeRow', function(){ $(this).closest('tr').remove(); });

  /* ===== Submit Add form ===== */
  $('#addProductForm').on('submit', async function (e) {
    e.preventDefault();

    if ($newRows.find('tr').length === 0) {
      return Swal.fire({ icon:'warning', title:'Hinweis', text:'Bitte mindestens eine Zeile hinzufügen.' });
    }

    const $btn = $(this).find('button[type="submit"]');
    const old  = $btn.html();
    $btn.prop('disabled', true).html('Speichern…');

    try {
      const res  = await fetch($(this).attr('action'), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF },
        body: new FormData(this)
      });

      const txt = await res.text();
      let data; try{ data = JSON.parse(txt); } catch{ data = { ok: res.ok, message: txt || '' }; }

      if (!res.ok) {
        if (res.status === 422 && data?.errors) {
          const list = Object.values(data.errors).flat().join('\n');
          return Swal.fire({ icon:'error', title:'Validierung', text:list || 'Bitte Eingaben prüfen.' });
        }
        return Swal.fire({ icon:'error', title:'Fehler', text: data?.message || `HTTP ${res.status}` });
      }

      Swal.fire({ icon:'success', title:'Gespeichert', text: data?.message || 'Produkte erfolgreich gespeichert.' })
        .then(() => {
          $newRows.empty();

          const cid = $('#modal_customer_id').val();
          const aid = $('#modal_alternative_id').val();

          if (cid && aid) {
            $.get(`/lead/get/products/${cid}/${aid}`, (rows = []) => {
              $existing.empty();
              rows.forEach(row => {
                const svcKey = row.service_phase_section
                  || (SERVICES.find(s => String(s.id) === String(row.service_id))?.phase_section)
                  || row.service_id;

                $existing.append(`
                  <tr>
                    <td>${row.article_group || '-'}</td>
                    <td>${tService(svcKey)}</td>
                    <td>${row.department_name || '-'}</td>
                    <td>
                      <div style="display:flex;align-items:center;gap:8px;">
                        <img src="${empImg(row.image)}" style="width:32px;height:32px;border-radius:50%;">
                        <span>${(`${row.name??''} ${row.lastname??''}`).trim() || '-'}</span>
                      </div>
                    </td>
                    <td>
                      <div style="display:flex;align-items:center;gap:8px;">
                        <img src="${empImg(row.feimage)}" style="width:32px;height:32px;border-radius:50%;">
                        <span>${(`${row.fename??''} ${row.felastname??''}`).trim() || '-'}</span>
                      </div>
                    </td>
                    <td>${tInterest(row.interest)}</td>
                    <td>${tRealization(row.realization_time)}</td>
                    <td>
                      <button type="button" class="btn btn-sm btn-warning edit-product"
                        data-id="${row.id}"
                        data-product-id="${row.product_id}"
                        data-service-id="${row.service_id ?? ''}"
                        data-department-id="${row.department_id ?? ''}"
                        data-employee-id="${row.employee_id ?? ''}"
                        data-field-employee="${row.field_employee_id ?? row.field_employee ?? ''}"
                        data-interest="${row.interest ?? ''}"
                        data-realization-time="${row.realization_time ?? ''}">
                        <i class="feather icon-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger delete-product" data-id="${row.id}">
                        <i class="feather icon-trash"></i>
                      </button>
                    </td>
                  </tr>
                `);
              });
            });
          }
        });

    } catch (err) {
      Swal.fire({ icon:'error', title:'Fehler', text: err?.message || 'Unerwarteter Fehler.' });
    } finally {
      $btn.prop('disabled', false).html(old);
    }
  });

  /* ===== Edit modal: open custom modal instead of bootstrap ===== */
  function fillEditStaticSelects(){
    const $interest = $('#edit_interest');
    const $real     = $('#edit_realization_time');

    if (!$interest.find('option').length) {
      $interest.append(`<option value="intent">Kaufabsicht</option>`)
               .append(`<option value="interest">Kaufinteresse</option>`)
               .append(`<option value="option">Kaufoption</option>`);
    }
    if (!$real.find('option').length) {
      $real.append(`<option value="soon">Schnellstmöglich</option>`)
           .append(`<option value="3">3 Monate</option>`)
           .append(`<option value="6">6 Monate</option>`)
           .append(`<option value="other">Sonstiges</option>`);
    }

    ['#edit_product','#edit_service','#edit_department','#edit_interest','#edit_realization_time'].forEach(sel => {
      s2($(sel), { width:'100%', dropdownParent: editDropdownParent });
    });

    s2($('#edit_employee'), {
      width:'100%',
      dropdownParent: editDropdownParent,
      templateResult: employeeTpl,
      templateSelection: o => o.text,
      escapeMarkup: m => m
    });

    s2($('#edit_field_employee'), {
      width:'100%',
      dropdownParent: editDropdownParent,
      templateResult: employeeTpl,
      templateSelection: o => o.text,
      escapeMarkup: m => m
    });
  }

  function setEditServices(pid, selectedSid){
    const $serv = $('#edit_service');
    $serv.empty().append('<option value="">Wählen...</option>');
    SERVICES.filter(s => String(s.product_id) === String(pid))
      .forEach(s => $serv.append(`<option value="${s.id}">${tService(s.phase_section)}</option>`));
    $serv.val(selectedSid || '').trigger('change.select2');
  }

  function renderEditEmployees(internalList, externalList, selectedEmp, selectedFemp){
    const $emp  = $('#edit_employee');
    const $femp = $('#edit_field_employee');

    const render = ($select, placeholder, list, selected) => {
      $select.empty().append(`<option value="">${placeholder}</option>`);
      (list || []).forEach(e => {
        $select.append(
          `<option value="${e.id}" data-img="${e.image||''}" data-positions="${(e.positions||[]).join(', ')}">
            ${e.name} ${e.lastname}
           </option>`
        );
      });
      $select.val(selected || '').trigger('change.select2');
    };

    render($emp,  'Wählen...', internalList, selectedEmp);
    render($femp, 'Wählen...', externalList, selectedFemp);

    fillEditStaticSelects();
  }

  async function loadEditEmployees(pid, sid, did, selectedEmp, selectedFemp){
    try {
      const resp = await $.post('{{ route("inquiry.department.employees") }}', {
        _token: CSRF,
        product_id: pid,
        service_id: sid || null,
        department_id: did || null,
        stage: STAGE
      });

      let internalEmployees = [];
      let externalEmployees = [];
      if (Array.isArray(resp)) {
        internalEmployees = resp;
        externalEmployees = resp;
      } else {
        internalEmployees = resp.internal_employees || resp.employees || [];
        externalEmployees = resp.external_employees || resp.employees || [];
      }

      renderEditEmployees(internalEmployees, externalEmployees, selectedEmp, selectedFemp);
    } catch(e) {
      renderEditEmployees([], [], selectedEmp, selectedFemp);
    }
  }

  $(document).on('click', '.edit-product', function(){
    const id   = $(this).data('id');
    const pid  = $(this).data('product-id');
    const sid  = $(this).data('service-id');
    const did  = $(this).data('department-id');
    const emp  = $(this).data('employee-id');
    const femp = $(this).data('field-employee');

    $('#edit_product_id').val(id);

    fillEditStaticSelects();
    $('#edit_product').val(pid).trigger('change.select2');
    setEditServices(pid, sid);
    $('#edit_department').val(did || '').trigger('change.select2');

    $('#edit_interest').val($(this).data('interest') || 'intent').trigger('change.select2');
    $('#edit_realization_time').val($(this).data('realization-time') || 'soon').trigger('change.select2');

    openModal($editModal);
    loadEditEmployees(pid, sid, did, emp, femp);
  });

  $(document).on('change', '#edit_product', function(){
    const pid = $(this).val();
    setEditServices(pid, null);
    loadEditEmployees(pid, $('#edit_service').val(), $('#edit_department').val(), $('#edit_employee').val(), $('#edit_field_employee').val());
  });

  $(document).on('change', '#edit_service, #edit_department', debounce(function(){
    loadEditEmployees($('#edit_product').val(), $('#edit_service').val(), $('#edit_department').val(), $('#edit_employee').val(), $('#edit_field_employee').val());
  }, 120));

})();
</script>




<script>
    $(document).on('click', '.add_employees', function () {
        const productId = $(this).data('product-id');
        const leadId = $(this).data('new-lead-id');
        const altId = $(this).data('alternative-id');

        $.post('/getEmployees', {_token: '{{ csrf_token() }}'}, function (employees) {
            let html = '<select id="employeeSelect" class="swal2-select">';
            html += `<option value="">-- Kein Mitarbeiter --</option>`; // Add null option

            employees.forEach(emp => {
                html += `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`;
            });
            html += '</select>';

            Swal.fire({
                title: 'Mitarbeiter zuweisen',
                html: html,
                showCancelButton: true,
                confirmButtonText: 'Zuweisen',
                preConfirm: () => {
                    return $('#employeeSelect').val(); // This can be empty string
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const employeeId = result.value; // can be null (empty string)

                    $.ajax({
                        url: '/update-lead-employee',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            employee_id: employeeId,
                            product_id: productId,
                            alternative_id: altId,
                            customer_id: leadId
                        },
                        success: function (res) {
                            Swal.fire('Erfolgreich zugewiesen!', '', 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function () {
                            Swal.fire('Fehler beim Speichern', '', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
 

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.customer_fund').forEach(button => {
            button.addEventListener('click', async function (e) {
                e.preventDefault();
                const customerId = this.dataset.id;

                try {
                    const res = await fetch(`/get-product-lists/${customerId}`);
                    const data = await res.json();

                    if (!data.length) {
                        return Swal.fire('Keine Produkte gefunden', 'Es wurden keine Produkte für diesen Kunden gefunden.', 'warning');
                    }

                    const options = {};
                    data.forEach(product => {
                        options[`${product.product_id}|${product.alternative_id}`] = `Produkt: ${product.article_group} | Service: ${product.service}`;
                    });

                    const result = await Swal.fire({
                        title: 'Produkt auswählen',
                        input: 'select',
                        inputOptions: options,
                        inputPlaceholder: 'Bitte Produkt auswählen',
                        showCancelButton: true,
                        confirmButtonText: 'Weiter',
                        cancelButtonText: 'Abbrechen',
                        inputValidator: value => !value && 'Bitte wählen Sie ein Produkt aus'
                    });

                    if (!result.isConfirmed) return;

                    const [product_id, alternative_id] = result.value.split('|');

                    try {
                        const res = await fetch(`/funding/sidebar/${customerId}/${alternative_id}/${product_id}`);
                        const data = await res.json();

                        console.log('Sidebar data:', data);

                        window.sidebarLeadId = customerId;
                        window.sidebarAltId = alternative_id;
                        window.sidebarProductId = product_id;
                        updateSidebarContent(data);
                        openSidebar();
                    } catch (error) {
                        Swal.fire('Fehler', 'Daten konnten nicht geladen werden.', 'error');
                    }
                } catch (error) {
                    Swal.fire('Fehler', 'Produkte konnten nicht geladen werden.', 'error');
                }
            });
        });

        function openSidebar() {
            const sidebar = document.getElementById('fundingSidebar');
            if (!sidebar) {
                console.warn('⚠️ Sidebar element #fundingSidebar not found.');
                return;
            }
            sidebar.classList.add('active');
        }

        window.closeSidebar = function () {
            document.getElementById('fundingSidebar')?.classList.remove('active', 'fullscreen');
        };

        window.maximizeSidebar = function () {
            document.getElementById('fundingSidebar')?.classList.toggle('fullscreen');
        };


        function updateSidebarContent(data) {
            const lead = data.lead || {};
            const alt = data.alternative || {};
            const prod = data.product || {};
            window.dataAlt = data.alternative;
            document.querySelector('[data-role="customer_name"]').textContent = `${lead.name ?? '-'} ${lead.lastname ?? '-'}`;
            document.querySelector('[data-role="house_type"]').textContent = `Haus: ${alt.objective ?? '-'}`;
            document.querySelector('[data-role="product_name"]').textContent = `Produkt: ${prod.article_group ?? '-'}`;
            document.querySelector('[data-role="installation_year"]').textContent = `Installation: ${alt.heating_system_year ?? '-'}`;

            const container = document.getElementById('chat');
            const isBadHomburg = alt.city?.toLowerCase().includes('bad homburg');

            let bhQuestions = '';
            if (isBadHomburg) {
                bhQuestions = `
                    ${generateInputRow('PV-Module (kWp)', 'solar_module_kwp', null, alt.solar_module_kwp ?? '', 'number')}
                    ${generateInputRow('Solarthermie vorhanden', 'solar_thermal', ['Ja', 'Nein'], alt.solar_thermal ? 'Ja' : 'Nein')}
                    ${generateInputRow('Solarthermie Fläche (m²)', 'solar_thermal_area', null, alt.solar_thermal_area ?? '', 'number')}
                    ${generateInputRow('Simulation vorhanden', 'solar_thermal_simulation', ['Ja', 'Nein'], alt.solar_thermal_simulation ? 'Ja' : 'Nein')}
                    ${generateInputRow('Balkonmodule (Anzahl)', 'balcony_modules', null, alt.balcony_modules ?? '', 'number')}
                    ${generateInputRow('Hocheffizienzpumpe', 'has_pump_upgrade', ['Ja', 'Nein'], alt.has_pump_upgrade ? 'Ja' : 'Nein')}
                    ${generateInputRow('Hydraulischer Abgleich', 'hydraulic_only', ['Ja', 'Nein'], alt.hydraulic_only ? 'Ja' : 'Nein')}
                `;
            }

            container.innerHTML = `
                <div class="row g-2">
                    ${generateInputRow('Nutzung', 'nutzung', ['selbstgenutzt', 'vermietet', 'gemischt'], alt.usage_type)}
                    ${generateInputRow('Wohnheiten', 'wohnheiten', null, alt.number_we ?? 1, 'number')}
                    ${generateInputRow('Selbst genutzt', 'selbst_anzahl', ['Ja', 'Nein'], alt.number_self_used > 0 ? 'Ja' : 'Nein')}
                    ${generateInputRow('Einkommen', 'einkommen', null, alt.income_taxed, 'number')}
                    ${generateInputRow('Heizungsalter', 'heizungsalter', ['jünger als 20 Jahre', '20 Jahre oder älter'], alt.heating_age_group)}
                    ${generateInputRow('Kältemittel (natürlich)', 'kaeltemittel', ['Ja', 'Nein'], alt.natural_refrigerant ? 'Ja' : 'Nein')}
                    ${generateInputRow('Investitionskosten', 'invest', null, alt.investment_costs, 'number')}
                    ${bhQuestions}
                </div> 
                <div class="text-end mt-2">
                    <button class="btn btn-success" id="recalcBtn">Neu berechnen</button>
                </div>
            `;

            const d = {
                nutzung: document.getElementById('nutzung').value,
                wohnheiten: parseInt(document.getElementById('wohnheiten').value) || 1,
                selbst_anzahl: document.getElementById('selbst_anzahl').value === 'Ja' ? 1 : 0,
                einkommen: parseFloat(document.getElementById('einkommen').value) || 0,
                heizungsalter: document.getElementById('heizungsalter').value,
                kaeltemittel: document.getElementById('kaeltemittel').value === 'Ja',
                invest: parseFloat(document.getElementById('invest').value) || 0
            };

            if (isBadHomburg) {
                d.solar_module_kwp = parseFloat(document.getElementById('solar_module_kwp')?.value || 0);
                d.solar_thermal = document.getElementById('solar_thermal')?.value === 'Ja';
                d.solar_thermal_area = parseFloat(document.getElementById('solar_thermal_area')?.value || 0);
                d.solar_thermal_simulation = document.getElementById('solar_thermal_simulation')?.value === 'Ja';
                d.balcony_modules = parseInt(document.getElementById('balcony_modules')?.value || 0);
                d.has_pump_upgrade = document.getElementById('has_pump_upgrade')?.value === 'Ja';
                d.hydraulic_only = document.getElementById('hydraulic_only')?.value === 'Ja';
            }

            window.data = d;
            calculateFoerderung();

            document.getElementById('recalcBtn').addEventListener('click', async () => {
                // Refresh data from current inputs
                const d = {
                    nutzung: document.getElementById('nutzung').value,
                    wohnheiten: parseInt(document.getElementById('wohnheiten').value) || 1,
                    selbst_anzahl: document.getElementById('selbst_anzahl').value === 'Ja' ? 1 : 0,
                    einkommen: parseFloat(document.getElementById('einkommen').value) || 0,
                    heizungsalter: document.getElementById('heizungsalter').value,
                    kaeltemittel: document.getElementById('kaeltemittel').value === 'Ja',
                    invest: parseFloat(document.getElementById('invest').value) || 0
                };

                if (isBadHomburg) {
                    d.solar_module_kwp = parseFloat(document.getElementById('solar_module_kwp')?.value || 0);
                    d.solar_thermal = document.getElementById('solar_thermal')?.value === 'Ja';
                    d.solar_thermal_area = parseFloat(document.getElementById('solar_thermal_area')?.value || 0);
                    d.solar_thermal_simulation = document.getElementById('solar_thermal_simulation')?.value === 'Ja';
                    d.balcony_modules = parseInt(document.getElementById('balcony_modules')?.value || 0);
                    d.has_pump_upgrade = document.getElementById('has_pump_upgrade')?.value === 'Ja';
                    d.hydraulic_only = document.getElementById('hydraulic_only')?.value === 'Ja';
                }

                window.data = d;
                calculateFoerderung(); // recalc UI locally

                try {
                    const response = await fetch(`/funding/save/${window.sidebarLeadId}/${window.sidebarAltId}/${window.sidebarProductId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ funding_data: d })
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        Swal.fire('Gespeichert', 'Die Daten wurden erfolgreich gespeichert.', 'success');
                    } else {
                        Swal.fire('Fehler', result.message ?? 'Beim Speichern ist ein Fehler aufgetreten.', 'error');
                    }
                } catch (err) {
                    console.error('❌ Save error:', err);
                    Swal.fire('Fehler', 'Verbindungsfehler beim Speichern.', 'error');
                }
            });

        }


    function generateInputRow(label, id, options = null, value = '', type = 'select') {
        if (type === 'number') {
            return `
                <div class="col-md-6">
                    <label>${label}:</label>
                    <input type="number" class="form-control" id="${id}" value="${value ?? ''}" />
                </div>`;
        }

        let opts = '';
        options?.forEach(opt => {
            const selected = (opt === value) ? 'selected' : '';
            opts += `<option value="${opt}" ${selected}>${opt}</option>`;
        });

        return `
            <div class="col-md-6">
                <label>${label}:</label>
                <select class="form-control" id="${id}">${opts}</select>
            </div>`;
    }


    window.calculateFoerderung = function () {
        const d = window.data;
        const alt = window.dataAlt;
        if (!d || !alt) return;

        const w = d.wohnheiten;
        const maxKosten = 30000 + Math.min(w - 1, 5) * 15000 + Math.max(0, w - 6) * 8000;
        const anteilSelbst = d.selbst_anzahl / w;
        const anteilVermietet = 1 - anteilSelbst;

        let quoteSelbst = 30;
        if (d.heizungsalter === '20 Jahre oder älter') quoteSelbst += 20;
        if (d.kaeltemittel) quoteSelbst += 5;
        if (d.einkommen <= 40000) quoteSelbst += 30;
        quoteSelbst = Math.min(quoteSelbst, 70);

        const quoteVermietet = 30 + (d.kaeltemittel ? 5 : 0);
        const foerderbareKosten = Math.min(d.invest, maxKosten);
        const nichtFoerderfaehig = Math.max(0, d.invest - maxKosten);

        const zuschuss = Math.round(
            (quoteSelbst * foerderbareKosten * anteilSelbst +
            quoteVermietet * foerderbareKosten * anteilVermietet) / 100
        );

        const kreditbedarf = Math.max(0, d.invest - zuschuss);
        const kfwProgramm = (d.nutzung !== 'vermietet' && d.einkommen <= 90000) ? 'KfW 358' : 'KfW 359';

        const zinssatz = 0.0088;
        const monate = 120;
        const zinsMonat = zinssatz / 12;
        const rate = kreditbedarf * (zinsMonat * Math.pow(1 + zinsMonat, monate)) / (Math.pow(1 + zinsMonat, monate) - 1);
        const zinsbetrag = rate * monate - kreditbedarf;

        let monateZahlung = 0;
        if (kfwProgramm === 'KfW 358' && kreditbedarf > 0) {
            let rest = kreditbedarf;
            for (let i = 1; i <= 120; i++) {
                const zahlung = rest * (zinsMonat * Math.pow(1 + zinsMonat, 120 - i + 1)) / (Math.pow(1 + zinsMonat, 120 - i + 1) - 1);
                rest = rest * (1 + zinsMonat) - zahlung;
                if (i === 60) rest = Math.max(0, rest - 3000); // Sondertilgung
                monateZahlung++;
                if (rest <= 0) break;
            }
        }

        let bhzuschuss = 0;
        bhzuschuss += alt.solar_module_kwp ? Math.min(alt.solar_module_kwp * 300, 6000) : 0;
        bhzuschuss += alt.solar_tile_kwp ? Math.min(alt.solar_tile_kwp * 400, 8000) : 0;
        bhzuschuss += alt.battery_kwh ? Math.min(alt.battery_kwh * 300, 3000) : 0;
        bhzuschuss += alt.balcony_modules ? Math.min(alt.balcony_modules * 200, 600) : 0;
        bhzuschuss += alt.has_pump_upgrade ? 500 : 0;
        bhzuschuss += alt.hydraulic_only ? 1000 : 0;

        const innovationEligible = w >= 9 && alt.heating_type?.toLowerCase().includes('wp');
        const innovationZuschuss = innovationEligible ? Math.min(d.invest * 0.2, 20000) : 0;

        const totalFunding = zuschuss + bhzuschuss + innovationZuschuss;
        const foerderQuote = (anteilSelbst * quoteSelbst + anteilVermietet * quoteVermietet).toFixed(1);

        const html = `
        <div class="row row-cols-1 row-cols-md-2 g-4">
            ${renderCard('KfW / BEG Förderung', 'success', `
                <p><strong>Zuschuss (KfW/BEG):</strong> ${zuschuss.toLocaleString('de-DE')} €</p>
                <p><strong>KfW-Programm:</strong> ${kfwProgramm}</p>
                <p><strong>Förderquote:</strong> ${foerderQuote}%</p>
            `)}
            ${renderCard('Bad Homburg Förderung', 'info', `
                <p><strong>Zuschuss (BH):</strong> ${bhzuschuss.toLocaleString('de-DE')} €</p>
                <ul class="mb-0 small">
                    ${alt.solar_module_kwp ? `<li>PV-Module: ${alt.solar_module_kwp} kWp</li>` : ''}
                    ${alt.solar_tile_kwp ? `<li>Solarziegel: ${alt.solar_tile_kwp} kWp</li>` : ''}
                    ${alt.battery_kwh ? `<li>Batterie: ${alt.battery_kwh} kWh</li>` : ''}
                    ${alt.balcony_modules ? `<li>Balkonmodule: ${alt.balcony_modules}</li>` : ''}
                    ${alt.solar_thermal ? `<li>Solarthermie: Ja</li>` : ''}
                    ${alt.solar_thermal_area ? `<li>Thermische Fläche: ${alt.solar_thermal_area} m²</li>` : ''}
                    ${alt.solar_thermal_simulation ? `<li>Simulation: Ja</li>` : ''}
                    ${alt.has_pump_upgrade ? `<li>Hocheffizienzpumpen</li>` : ''}
                    ${alt.hydraulic_only ? `<li>Hydraulischer Abgleich</li>` : ''}
                </ul>
            `)}
            ${renderCard('Finanzierung', 'warning', `
                <p><strong>Kreditbedarf:</strong> ${kreditbedarf.toLocaleString('de-DE')} €</p>
                <p><strong>Monatliche Rate:</strong> ${rate.toFixed(2)} €</p>
                <p><strong>Zinsaufwand:</strong> ${zinsbetrag.toFixed(2).toLocaleString('de-DE')} €</p>
                ${kfwProgramm === 'KfW 358' 
                    ? `<p><strong>Laufzeit mit Sondertilgung:</strong> ${Math.floor(monateZahlung / 12)} Jahre, ${monateZahlung % 12} Monate</p>`
                    : '<p>⚠️ Keine KfW-Finanzierung bei KfW 359</p>'
                }
            `)}
            ${renderCard('Allgemein', 'secondary', `
                <p><strong>Investition:</strong> ${d.invest.toLocaleString('de-DE')} €</p>
                <p><strong>Max. förderfähig:</strong> ${maxKosten.toLocaleString('de-DE')} €</p>
                <p><strong>Nicht förderfähig:</strong> ${nichtFoerderfaehig.toLocaleString('de-DE')} €</p>
            `)}
            ${innovationEligible ? renderCard('🏢 Innovation Förderung', 'dark', `
                <p><strong>Voraussetzungen erfüllt:</strong> Ja</p>
                <p><strong>Förderquote:</strong> 20%</p>
                <p><strong>Zuschuss (max. 20.000 €):</strong> ${innovationZuschuss.toLocaleString('de-DE')} €</p>
            `) : ''}
        </div>
        <div class="mt-4 p-3 bg-white border rounded shadow-sm">
            <h5 class="mb-1"><i class="feather icon-award"></i> Gesamtförderung</h5>
            <div class="row">
                <div class="col-md-4"><strong>KfW / BEG:</strong></div>
                <div class="col-md-8 text-end text-primary">${zuschuss.toLocaleString('de-DE')} €</div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Bad Homburg:</strong></div>
                <div class="col-md-8 text-end text-primary">${bhzuschuss.toLocaleString('de-DE')} €</div>
            </div>
            ${innovationZuschuss > 0 ? `
            <div class="row">
                <div class="col-md-4"><strong>Innovation:</strong></div>
                <div class="col-md-8 text-end text-dark">${innovationZuschuss.toLocaleString('de-DE')} €</div>
            </div>` : ''}
            <hr>
            <div class="row fw-bold">
                <div class="col-md-4">Gesamtersparnis:</div>
                <div class="col-md-8 text-end fs-5 text-primary">${totalFunding.toLocaleString('de-DE')} €</div>
            </div>
        </div>
        `;

        document.getElementById('foerderResult').innerHTML = html;
    };

    // Helper to render a card block
    function renderCard(title, color, body) {
        return `
        <div class="col">
            <div class="card border-${color} shadow-sm">
                <div class="card-header bg-${color} text-white">${title}</div>
                <div class="card-body">${body}</div>
            </div>
        </div>`;
    }


    });
    </script>
 


<script>
$(document).on('click', '.delete-product', function () {
  const $btn = $(this);
  const rowEl = $btn.closest('tr');

  const row_id        = $btn.data('id');              // lead_product_lists.id
  const customer_id   = $btn.data('customer-id');
  const alternative_id= $btn.data('alternative-id');
  const product_id    = $btn.data('product-id');

  Swal.fire({
    title: 'Löschen?',
    text: 'Dieses Produkt und zugehörige Zuständigkeiten werden gelöscht.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ja, löschen',
    cancelButtonText: 'Abbrechen'
  }).then((res) => {
    if (!res.isConfirmed) return;

    $.ajax({
      url:  "{{ route('lead.products.delete') }}",
      method: 'POST',                    // use POST + method spoofing
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: {
        _method: 'DELETE',
        // Prefer row_id for precision; the controller can also use the tuple.
        row_id,
        customer_id,
        alternative_id,
        product_id
      },
      success: function(resp){
        if (resp?.success) {
          // Remove the row from the modal table
          rowEl.fadeOut(200, function(){ $(this).remove(); });
          toastr && toastr.success(resp.message || 'Gelöscht.');
        } else {
          toastr && toastr.error(resp?.message || 'Löschen fehlgeschlagen.');
        }
      },
      error: function(xhr){
        const msg = xhr.responseJSON?.message || `Fehler ${xhr.status}`;
        toastr && toastr.error(msg);
        console.error(xhr.responseText);
      }
    });
  });
});
</script>


<script>
(function(){
  const SINGLE_OPEN = true; // set false if you want multiple open in different rows

  const clamp = (v,a,b) => Math.max(a, Math.min(b, v));
  const parseOffset = (s) => { if(!s) return {x:0,y:8}; const [x='0',y='8']=String(s).split(','); return {x:+x||0,y:+y||0}; };

  const openByScope = new Map(); // scope -> ctx

  function parts(group){
    const toggle = group.querySelector('[aria-controls]') || group.querySelector('.js-menu-toggle');
    let panel = null;
    if (toggle && toggle.getAttribute('aria-controls')) {
      panel = document.getElementById(toggle.getAttribute('aria-controls'));
    }
    if (!panel) panel = group.querySelector('.js-menu-panel');
    return { toggle, panel };
  }

  function place(panel, toggle, align, offset, placement){
    const r  = toggle.getBoundingClientRect();
    const pw = panel.offsetWidth, ph = panel.offsetHeight;
    const vw = document.documentElement.clientWidth, vh = document.documentElement.clientHeight;

    let left = (align === 'end') ? (r.right - pw) : r.left;
    let top  = (placement === 'top') ? (r.top - ph) : r.bottom;

    const ySign = (placement === 'top') ? -1 : 1;
    left += (offset.x || 0);
    top  += (offset.y || 0) * ySign;

    left = clamp(left, 8, vw - pw - 8);
    top  = clamp(top,  8, vh - ph - 8);

    panel.style.left = left + 'px';
    panel.style.top  = top  + 'px';
  }

  function portal(panel, enable){
    if (enable){
      panel.classList.add('js-menu-portal');
      panel.__parent = panel.parentElement;
      document.body.appendChild(panel);
    } else {
      if (panel.__parent){ panel.__parent.appendChild(panel); delete panel.__parent; }
      panel.classList.remove('js-menu-portal');
    }
  }

  const scopeOf = g => g.dataset.menuScope || '__global__';

  function closeMenu(scope){
    const ctx = openByScope.get(scope);
    if (!ctx) return;
    const { toggle, panel, onScroll, onResize, doPortal } = ctx;

    toggle.setAttribute('aria-expanded','false');
    panel.classList.remove('is-open');

    // 🔧 reset inline styles so it truly hides and doesn’t “drop” below
    panel.style.display = '';     // rely on CSS to hide
    panel.style.visibility = '';
    panel.style.left = '';
    panel.style.top = '';

    window.removeEventListener('scroll', onScroll, true);
    window.removeEventListener('resize', onResize);

    if (doPortal) portal(panel, false);
    openByScope.delete(scope);
  }

  function closeAll(){
    for (const scope of Array.from(openByScope.keys())) closeMenu(scope);
  }

  function openMenu(group){
    const { toggle, panel } = parts(group);
    if (!toggle || !panel) return;

    const scope = scopeOf(group);
    if (SINGLE_OPEN) closeAll();
    else {
      const existing = openByScope.get(scope);
      if (existing && existing.group !== group) closeMenu(scope);
    }

    const align     = (group.dataset.menuAlign || 'start').toLowerCase();
    const offset    = parseOffset(group.dataset.menuOffset);
    const placement = (group.dataset.menuPlacement || 'bottom').toLowerCase();
    const doPortal  = group.dataset.menuPortal !== 'false';

    if (doPortal) portal(panel, true);

    // ✅ make visible first, then measure/position; no temp display hacks
    panel.classList.add('is-open');

    place(panel, toggle, align, offset, placement);

    toggle.setAttribute('aria-expanded','true');

    const onScroll = () => place(panel, toggle, align, offset, placement);
    const onResize = () => place(panel, toggle, align, offset, placement);
    window.addEventListener('scroll', onScroll, true);
    window.addEventListener('resize', onResize);

    openByScope.set(scope, { group, toggle, panel, onScroll, onResize, doPortal });
  }

  function closeAllOutside(target){
    for (const [scope, ctx] of Array.from(openByScope.entries())){
      const onToggle = ctx.toggle.contains(target);
      const inPanel  = ctx.panel.contains(target);
      if (!onToggle && !inPanel) closeMenu(scope);
    }
  }

  // Outside close on pointerdown (before click)
  document.addEventListener('pointerdown', (e)=> closeAllOutside(e.target));

  // Toggle + item actions
  document.addEventListener('click', (e)=>{
    const t = e.target.closest('.js-menu .js-menu-toggle, .js-menu [aria-controls]');
    if (t){
      e.preventDefault();
      const g = t.closest('.js-menu');
      const scope = scopeOf(g);
      const current = openByScope.get(scope);
      if (SINGLE_OPEN){
        // Outside handler already closed others; just open this one
        openMenu(g);
      } else {
        (current && current.group === g) ? closeMenu(scope) : openMenu(g);
      }
      return;
    }

    // Inside any open panel → emit action + close that scope
    for (const [scope, ctx] of Array.from(openByScope.entries())){
      if (ctx.panel.contains(e.target)){
        const a = e.target.closest('.js-action');
        if (a){
          const detail = {
            action: a.dataset.action || null,
            target: a.dataset.target || null,
            anchor: a,
            menuId: ctx.group.dataset.menuId || ctx.group.id,
            scope
          };
          document.dispatchEvent(new CustomEvent('menu:action', { detail }));
        }
        closeMenu(scope);
        return;
      }
    }
  });

  // ESC closes all
  document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') closeAll(); });

  // Public helpers (optional)
  window.openMenuById  = id => { const g = document.getElementById(id); if (g) openMenu(g); };
  window.closeMenuById = id => { for (const [scope, ctx] of openByScope){ if (ctx.group.id === id) { closeMenu(scope); break; } } };
})();
</script>
 <script>
document.addEventListener('DOMContentLoaded', function () {
  const highlightId = @json(session('highlight_lead_id'));
  if (highlightId) {
    const row = document.getElementById('lead-row-' + highlightId);
    if (row) {
      // Sicherheitshalber nochmal Klasse setzen (falls SSR nicht gegriffen hat)
      row.classList.add('row-pulse');
      row.scrollIntoView({ behavior: 'smooth', block: 'center' });

      // Optional: nach der Animation die Klasse wieder entfernen
      setTimeout(() => row.classList.remove('row-pulse'), 7000);
    }
  }
});
</script>
  
 <script>
    $(function () {
        const backdrop = document.getElementById('duplicateDrawerBackdrop');
        const btnOpen  = document.getElementById('btnShowDuplicates');
        const btnClose = document.getElementById('btnCloseDuplicates');
        const cardsEl  = document.getElementById('duplicateCards');

        // Require drawer + container, but NOT the button
        if (!backdrop || !cardsEl) {
            console.warn('Duplicate drawer: required elements not found.', { backdrop, cardsEl });
            return;
        }

        function openDrawer() {
            backdrop.classList.add('is-open');
        }

        function closeDrawer() {
            backdrop.classList.remove('is-open');
        }

        if (btnOpen) {
            btnOpen.addEventListener('click', function () {
                openDrawer();
                loadDuplicates();
            });
        } else {
            console.warn('Duplicate drawer: btnShowDuplicates not found.');
        }

        if (btnClose) {
            btnClose.addEventListener('click', function () {
                closeDrawer();
            });
        }

        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) {
                closeDrawer();
            }
        });

        function stageClass(stage) {
            if (!stage) return '';
            return 'dup-stage-' + stage.toLowerCase();
        }

        function loadDuplicates() {
            cardsEl.innerHTML = '<div class="text-center text-muted py-2">Lade doppelte Kunden ...</div>';

            $.get("{{ route('admin.new_leads.duplicates') }}")
                .done(function (res) {
                    console.log('duplicate response', res);

                    const groups = res.groups || [];

                    if (!groups.length) {
                        cardsEl.innerHTML = '<div class="text-center text-muted py-2">Keine doppelten Kunden gefunden.</div>';
                        return;
                    }

                    let html = '';

                    groups.forEach(function (group) {
                        const groupCount = group.count || (group.customers ? group.customers.length : 0);
                        const title = group.label || 'Gruppe';

                        let groupMeta = [];
                        if (group.email) groupMeta.push(group.email);
                        if (group.phone) groupMeta.push(group.phone);

                        html +=
                            '<div class="dup-group" data-group-key="' + (group.key || '') + '">' +
                                '<div class="dup-group__header">' +
                                    '<div>' +
                                        '<div class="dup-group__title">' + title + '</div>' +
                                        '<div class="dup-group__meta">' +
                                            (groupMeta.length ? groupMeta.join(' • ') : '') +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="dup-group__count">' +
                                        '<span data-role="group-count">' + groupCount + '</span>x' +
                                    '</div>' +
                                '</div>' +
                                '<div class="dup-group__list">';

                        (group.customers || []).forEach(function (cust) {
                            const fullName = cust.full_name || ((cust.lastname || '') + ' ' + (cust.name || ''));
                            const altCount = cust.alternatives_count || 0;
                            const prdCount = cust.products_count || 0;
                            const hisCount = cust.histories_count || 0;

                            const objectsBadges = (cust.objects || []).map(function (o) {
                                return '<span class="dup-chip">' + (o.object_name || 'Ohne Objekt') + '</span>';
                            }).join('');

                            const productsPerObject = (cust.objects || []).map(function (o) {
                                if (!o.products || !o.products.length) return '';
                                return '<div class="mb-25">' +
                                    '<span class="text-xs text-muted">' + (o.object_name || 'Objekt') + ':</span> ' +
                                    '<span>' + o.products.join(', ') + '</span>' +
                                '</div>';
                            }).join('');

                            const stagesBadges = (cust.stages || []).map(function (s) {
                                const st = s.stage || '';
                                const total = s.total || 0;
                                return '<span class="dup-stage-badge ' + stageClass(st) + '">' +
                                    st + ' (' + total + ')' +
                                '</span>';
                            }).join('');

                             html +=
                                '<div class="dup-card-customer" data-lead-id="' + cust.id + '">' +
                                    // delete icon (top-right)
                                    '<button type="button" class="dup-delete-btn" title="Kunde löschen" data-action="delete-duplicate">' +
                                        '<i class="feather icon-trash-2"></i>' +
                                    '</button>' +

                                    // title
                                    '<div class="dup-card__title">' +
                                        fullName +
                                        (cust.customer_no ? ' <span class="text-xs text-muted">#' + cust.customer_no + '</span>' : '') +
                                    '</div>' +

                                    // meta
                                    '<div class="dup-card__meta">' +
                                        (cust.city ? (cust.postcode || '') + ' ' + cust.city + ' • ' : '') +
                                        (cust.email ? cust.email + ' • ' : '') +
                                        (cust.phone ? cust.phone : '') +
                                    '</div>' +

                                    // objects
                                    '<div class="dup-card__chips">' + objectsBadges + '</div>' +

                                    // products (optional)
                                    (productsPerObject ? '<div class="dup-products">' + productsPerObject + '</div>' : '') +

                                    // stages (optional)
                                    (stagesBadges ? '<div class="dup-stages">' + stagesBadges + '</div>' : '') +

                                    // progress
                                    '<div class="dup-progress mt-1">' +
                                        '<div class="dup-progress-bar">' +
                                            '<div class="dup-progress-bar-inner" style="width:' + (cust.progress || 0) + '%;"></div>' +
                                        '</div>' +
                                        '<div class="dup-progress-label">' +
                                            'Fortschritt: ' + (cust.progress || 0) + '% ' +
                                            '(Objekte: ' + altCount +
                                            ', Produkte: ' + prdCount +
                                            ', Historie: ' + hisCount + ')' +
                                        '</div>' +
                                    '</div>' +

                                    // NEW: profile button row
                                    '<div class="dup-actions mt-50">' +
                                        '<a href="{{ url('new_lead_profile') }}/' + cust.id + '" ' +
                                        'class="btn btn-sm btn-outline-primary dup-profile-btn"  >' +
                                            '<i class="feather icon-user mr-25"></i> Profil' +
                                        '</a>' +
                                    '</div>' +

                                '</div>';

                        });

                        html += '</div></div>'; // list + group
                    });

                    cardsEl.innerHTML = html;
                })
                .fail(function (xhr) {
                    console.error('Fehler beim Laden der doppelten Kunden', xhr);
                    cardsEl.innerHTML = '<div class="text-center text-danger py-2">Fehler beim Laden der doppelten Kunden.</div>';
                });
        }

        // Delete one duplicated customer
        $(document).on('click', '[data-action="delete-duplicate"]', function (e) {
            e.preventDefault();

            const card = this.closest('.dup-card-customer');
            if (!card) return;

            const leadId = card.getAttribute('data-lead-id');
            if (!leadId) return;

            if (!confirm('Diesen Kunden wirklich löschen?')) {
                return;
            }

            $.ajax({
                url: "{{ url('admin/new-leads') }}/" + leadId + "/duplicate",
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            })
                .done(function () {
                    const groupEl = card.closest('.dup-group');
                    card.remove();

                    if (groupEl) {
                        const remaining = groupEl.querySelectorAll('.dup-card-customer').length;
                        const countEl = groupEl.querySelector('[data-role="group-count"]');

                        if (countEl) {
                            countEl.textContent = remaining;
                        }

                        if (remaining === 0) {
                            groupEl.remove();
                        }
                    }
                })
                .fail(function (xhr) {
                    console.error('Fehler beim Löschen des Kunden', xhr);
                    alert('Löschen nicht möglich.');
                });
        });
    });
</script>
<script>
$(function () {
    // INIT product multi-select
    const $filterProducts = $('#filterProducts');
    const selectedProducts = @json($selectedProducts);

    if ($filterProducts.length) {
        $filterProducts.select2({
            placeholder: 'Nach Produkt/Gewerk filtern …',
            allowClear: true,
            width: '100'
        });

        if (selectedProducts && selectedProducts.length) {
            $filterProducts.val(selectedProducts.map(String)).trigger('change.select2');
        }

        // Auto-submit on change
        $filterProducts.on('change', function () {
            $('#leadFilterForm').submit();
        });
    }

    // ARTICLE CARDS -> toggle product filter
    $('.js-article-filter').on('click', function () {
        const pid = String($(this).data('product-id'));
        if (!$filterProducts.length) return;

        let current = $filterProducts.val() || [];

        if (current.includes(pid)) {
            current = current.filter(v => v !== pid);
            $(this).removeClass('is-active');
        } else {
            current.push(pid);
            $(this).addClass('is-active');
        }

        $filterProducts.val(current).trigger('change'); // submit via change handler
    });

    // PRODUCT DOTS in table -> add product to filter and submit
    $(document).on('click', '.js-product-pill', function () {
        const pid = String($(this).data('product-id'));
        if (!$filterProducts.length) return;

        let current = $filterProducts.val() || [];
        if (!current.includes(pid)) {
            current.push(pid);
            $filterProducts.val(current);
        }
        $filterProducts.trigger('change'); // triggers submit
    });
});
</script>
   <script>
(function () {
  "use strict";

  const FEED_ENDPOINT = "/lead/customer-feed"; // /{id}?limit=10&debug=1

  /**
   * Modal state
   */
  const modalState = {
    items: [],
    filterKind: "all",
    sortMode: "date_desc",
    searchQuery: ""
  };

  const kindLabels = {
    product: "Produkt",
    appointment: "Termin",
    task: "Aufgabe",
    ticket: "Ticket",
    history: "Historie"
  };

  /**
   * Load feed items from backend
   */
  async function fetchCustomerFeed(customerId, limit) {
    const url = `${FEED_ENDPOINT}/${encodeURIComponent(customerId)}?limit=${limit || 10}`;

    const res = await fetch(url, {
      headers: {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      credentials: "same-origin"
    });

    let json;
    try {
      json = await res.json();
    } catch (e) {
      throw new Error("Antwort ist kein gültiges JSON");
    }

    if (!res.ok) {
      const msg = (json && (json.error || json.message)) || "Feed lädt nicht";
      throw new Error(msg);
    }

    if (!json || json.success === false) {
      const msg = (json && (json.error || json.message)) || "Feed Fehler";
      throw new Error(msg);
    }

    const items = Array.isArray(json.items) ? json.items : [];
    return items;
  }

  /**
   * Apply a single feed item to DOM
   */
  function applyItemToDOM(root, item, index, total) {
  const emptyLine   = root.querySelector("[data-feed-empty]");
  const line        = root.querySelector("[data-feed-line]");
  const titleEl     = root.querySelector("[data-feed-title]");
  const textEl      = root.querySelector("[data-feed-text]");
  const pillEl      = root.querySelector("[data-feed-pill]");
  const timeEl      = root.querySelector("[data-feed-time]");
  const counterEl   = root.querySelector("[data-feed-counter]");
  const iconNode    = root.querySelector(".live-feed-icon i");
  const errorEl     = root.querySelector("[data-feed-error]");
  const avatarsWrap = root.querySelector("[data-feed-employees]"); // NEW

  if (errorEl) {
    errorEl.textContent = "";
    errorEl.classList.add("d-none");
  }

  if (emptyLine) emptyLine.classList.add("d-none");
  if (line)      line.classList.remove("d-none");

  if (titleEl) titleEl.textContent = item.title || "Aktivität";
  if (textEl)  textEl.textContent  = item.text || "";
  if (pillEl)  pillEl.textContent  = item.pill || "Info";
  if (timeEl)  timeEl.textContent  = item.time || "–";

  if (counterEl) {
    counterEl.textContent = total > 1 ? (index + 1) + " / " + total : "";
  }

  if (root) {
    root.dataset.kind = item.kind || "";
    root.style.display = "flex";
    root.classList.remove("is-empty");
  }

  if (iconNode) {
    const base    = "feather";
    const iconCls = item.icon || "icon-zap";
    iconNode.className = `${base} ${iconCls}`;
  }

  // NEW: render up to 3 employee avatars for appointments/tasks
  if (avatarsWrap) {
    avatarsWrap.innerHTML = "";
    const employees = Array.isArray(item.employees) ? item.employees : [];
    if (!employees.length) return;

    const maxShow = 3;
    employees.slice(0, maxShow).forEach(function (emp) {
      if (!emp || !emp.avatar) return;
      const span = document.createElement("div");
      span.className = "live-feed-avatar";
      const img = document.createElement("img");
      img.src = emp.avatar;
      img.alt = emp.name || "";
      span.appendChild(img);
      avatarsWrap.appendChild(span);
    });

    if (employees.length > maxShow) {
      const more = document.createElement("span");
      more.className = "live-feed-employees-more";
      more.textContent = "+" + (employees.length - maxShow);
      avatarsWrap.appendChild(more);
    }
  }
}

  /**
   * Show empty / fallback state
   */
  function showEmptyState(root, errorMessage) {
    const emptyLine   = root.querySelector("[data-feed-empty]");
    const line        = root.querySelector("[data-feed-line]");
    const counterEl   = root.querySelector("[data-feed-counter]");
    const errorEl     = root.querySelector("[data-feed-error]");

    if (line) line.classList.add("d-none");
    if (counterEl) counterEl.textContent = "";

    if (errorMessage && errorEl) {
      errorEl.textContent = errorMessage;
      errorEl.classList.remove("d-none");
      if (emptyLine) emptyLine.classList.add("d-none");
    } else {
      if (errorEl) {
        errorEl.textContent = "";
        errorEl.classList.add("d-none");
      }
      if (emptyLine) emptyLine.classList.remove("d-none");
    }

    root.classList.add("is-empty");
    root.style.display = "flex";
  }

  /**
   * Modal helpers
   */
  function getModal() {
    return document.getElementById("customerFeedModal");
  }

  function applyModalFilters() {
    const modal = getModal();
    if (!modal) return;

    const listEl = modal.querySelector("[data-feed-modal-list]");
    if (!listEl) return;

    const kind = modalState.filterKind;
    const search = (modalState.searchQuery || "").toLowerCase();
    const sortMode = modalState.sortMode;

    let items = modalState.items.slice();

    if (kind !== "all") {
      items = items.filter(function (item) {
        return (item.kind || "") === kind;
      });
    }

    if (search) {
      items = items.filter(function (item) {
        const title = (item.title || "").toLowerCase();
        const text  = (item.text || "").toLowerCase();
        const pill  = (item.pill || "").toLowerCase();
        return (
          title.indexOf(search) !== -1 ||
          text.indexOf(search) !== -1 ||
          pill.indexOf(search) !== -1
        );
      });
    }

    if (sortMode === "date_asc") {
      items = items.slice().reverse();
    } else if (sortMode === "title_asc") {
      items.sort(function (a, b) {
        return (a.title || "").localeCompare(b.title || "");
      });
    } else if (sortMode === "status_asc") {
      items.sort(function (a, b) {
        return (a.pill || "").localeCompare(b.pill || "");
      });
    }
    // date_desc: keep API order

    renderModalList(listEl, items);
  }

    function renderModalList(listEl, items) {
    if (!items.length) {
        listEl.innerHTML = '<div class="feed-modal-empty">Keine Aktivitäten gefunden.</div>';
        return;
    }

    listEl.innerHTML = items.map(function (item) {
        var kind = item.kind || "";
        var kindLabel = kindLabels[kind] || "Aktivität";
        var icon = item.icon || "icon-zap";
        var pill = item.pill || "Info";
        var time = item.time || "–";
        var title = item.title || "Aktivität";
        var text = item.text || "";
        var employees = Array.isArray(item.employees) ? item.employees : [];

        var avatarHtml = "";
        if (employees.length) {
        avatarHtml = '<div class="feed-modal-avatars">';
        employees.slice(0, 4).forEach(function (emp) {
            if (!emp || !emp.avatar) return;
            avatarHtml +=
            '<div class="feed-modal-avatar">' +
                '<img src="' + emp.avatar + '" alt="' + (emp.name || "") + '">' +
            '</div>';
        });
        if (employees.length > 4) {
            avatarHtml +=
            '<span class="feed-modal-avatars-more">+' + (employees.length - 4) + '</span>';
        }
        avatarHtml += '</div>';
        }

        return (
        '<div class="feed-modal-item">' +
            '<div class="feed-modal-item-icon">' +
            '<div class="feed-modal-icon-pill feed-modal-icon-pill--' + kind + '">' +
                '<i class="feather ' + icon + '"></i>' +
            '</div>' +
            '</div>' +
            '<div class="feed-modal-item-main">' +
            '<div class="feed-modal-item-header">' +
                '<span class="feed-modal-item-title">' + title + '</span>' +
                '<span class="feed-modal-item-time">' +
                '<i class="feather icon-clock"></i> ' + time +
                '</span>' +
            '</div>' +
            '<div class="feed-modal-item-text">' + text + '</div>' +
            '<div class="feed-modal-item-meta">' +
                '<span class="badge badge-light feed-modal-pill">' + pill + '</span>' +
                '<span class="feed-modal-kind-label">' + kindLabel + '</span>' +
            '</div>' +
            avatarHtml +
            '</div>' +
        '</div>'
        );
    }).join("");
    }


  function openFeedModal(root) {
    const modal = getModal();
    if (!modal) return;

    const state = root._feedState || { items: [] };
    modalState.items = state.items || [];
    modalState.filterKind = "all";
    modalState.sortMode = "date_desc";
    modalState.searchQuery = "";

    const titleEl = modal.querySelector("[data-feed-modal-title]");
    const subtitleEl = modal.querySelector("[data-feed-modal-subtitle]");
    const kindSelect = modal.querySelector("[data-feed-modal-kind]");
    const sortSelect = modal.querySelector("[data-feed-modal-sort]");
    const searchInput = modal.querySelector("[data-feed-modal-search]");

    if (titleEl) {
      const rowTitle =
        root.getAttribute("data-customer-title") ||
        "Aktivitäten";
      titleEl.textContent = rowTitle;
    }

    if (subtitleEl) {
      subtitleEl.textContent = "Produkte, Termine, Aufgaben und Tickets dieses Kunden.";
    }

    if (kindSelect) {
      kindSelect.value = "all";
    }
    if (sortSelect) {
      sortSelect.value = "date_desc";
    }
    if (searchInput) {
      searchInput.value = "";
    }

    applyModalFilters();

    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
      window.jQuery(modal).modal("show");
    } else {
      modal.style.display = "block";
      modal.classList.add("show");
    }
  }

  function wireModalControls() {
    const modal = getModal();
    if (!modal || modal._feedModalBound) return;
    modal._feedModalBound = true;

    const kindSelect = modal.querySelector("[data-feed-modal-kind]");
    const sortSelect = modal.querySelector("[data-feed-modal-sort]");
    const searchInput = modal.querySelector("[data-feed-modal-search]");

    if (kindSelect) {
      kindSelect.addEventListener("change", function () {
        modalState.filterKind = this.value || "all";
        applyModalFilters();
      });
    }

    if (sortSelect) {
      sortSelect.addEventListener("change", function () {
        modalState.sortMode = this.value || "date_desc";
        applyModalFilters();
      });
    }

    if (searchInput) {
      searchInput.addEventListener("input", function () {
        modalState.searchQuery = this.value || "";
        applyModalFilters();
      });
    }
  }

  /**
   * Initialize a single live feed widget
   */
  function initFeed(root) {
    if (!root || root._feedInitialized) return;
    root._feedInitialized = true;

    const customerId = root.getAttribute("data-customer-id");
    if (!customerId) {
      showEmptyState(root);
      return;
    }

    const limitAttr = root.getAttribute("data-feed-limit") || "10";
    const limit     = parseInt(limitAttr, 10) || 10;

    const state = {
      items: [],
      index: 0,
      playing: false,
      timer: null
    };
    root._feedState = state;

    const btnPrev    = root.querySelector("[data-feed-prev]");
    const btnNext    = root.querySelector("[data-feed-next]");
    const btnToggle  = root.querySelector("[data-feed-toggle]");
    const btnExpand  = root.querySelector("[data-feed-expand]");
    const iconPause  = root.querySelector("[data-feed-icon-pause]");
    const iconPlay   = root.querySelector("[data-feed-icon-play]");

    function updateToggleIcons() {
      if (!btnToggle) return;
      if (state.playing) {
        if (iconPause) iconPause.classList.remove("d-none");
        if (iconPlay)  iconPlay.classList.add("d-none");
      } else {
        if (iconPause) iconPause.classList.add("d-none");
        if (iconPlay)  iconPlay.classList.remove("d-none");
      }
    }

    function show(index) {
      if (!state.items.length) {
        showEmptyState(root);
        return;
      }
      const total = state.items.length;
      const idx   = (index % total + total) % total;
      state.index = idx;

      const item = state.items[idx];
      applyItemToDOM(root, item, idx, total);
    }

    function stop() {
      if (state.timer) {
        clearInterval(state.timer);
        state.timer = null;
      }
      state.playing = false;
      updateToggleIcons();
    }

    function start() {
      if (state.playing || state.items.length <= 1) {
        state.playing = false;
        updateToggleIcons();
        return;
      }
      state.playing = true;
      updateToggleIcons();
      state.timer = setInterval(function () {
        show(state.index + 1);
      }, 6000);
    }

    if (btnPrev) {
      btnPrev.addEventListener("click", function () {
        if (!state.items.length) return;
        stop();
        show(state.index - 1);
      });
    }

    if (btnNext) {
      btnNext.addEventListener("click", function () {
        if (!state.items.length) return;
        stop();
        show(state.index + 1);
      });
    }

    if (btnToggle) {
      btnToggle.addEventListener("click", function () {
        if (!state.items.length) return;
        if (state.playing) {
          stop();
        } else {
          start();
        }
      });
    }

    if (btnExpand) {
      btnExpand.addEventListener("click", function () {
        wireModalControls();
        openFeedModal(root);
      });
    }

    // Initial load
    fetchCustomerFeed(customerId, limit)
      .then(function (items) {
        state.items = items || [];
        root.setAttribute("data-feed-count", String(state.items.length || 0));

        if (!state.items.length) {
          showEmptyState(root);
          return;
        }

        show(0);
        start();
      })
      .catch(function (err) {
        showEmptyState(root, err && err.message ? err.message : null);
      });
  }

  /**
   * Bootstrap all feed widgets on page
   */
  function bootstrapCustomerFeeds() {
    const roots = Array.prototype.slice.call(
      document.querySelectorAll(".customer-live-feed[data-feed-root][data-customer-id]")
    );
    if (!roots.length) {
      return;
    }

    let i = 0;
    const BATCH = 5;

    (function pump() {
      const slice = roots.slice(i, i + BATCH);
      i += BATCH;
      slice.forEach(initFeed);
      if (i < roots.length) {
        setTimeout(pump, 80);
      }
    })();
  }

  document.addEventListener("DOMContentLoaded", bootstrapCustomerFeeds);
})();
</script>
 

<script>
$(document).ready(function() {
    
    // 1. Open Modal
    $('#openMassManager').on('click', function(e) {
        e.preventDefault();
        $('#massProductModal').fadeIn(200); // Uses jQuery to show the div
        // Or use: $('#massProductModal').css('display', 'block');
    });

    // 2. Close Modal (X Button)
    $('#closeMassManager').on('click', function() {
        $('#massProductModal').fadeOut(200);
        // Optional: Reload page on close if you want to refresh data
        // location.reload(); 
    });

    // 3. Close if clicked outside the white box
    $(window).on('click', function(event) {
        if ($(event.target).is('#massProductModal')) {
            $('#massProductModal').fadeOut(200);
        }
    });

});
</script>

<script>
$(document).ready(function() {
    
    // --- CONFIGURATION ---
    // Ensure these match your actual route names and paths
    const MASS_ROUTE_EMPLOYEES = '{{ route("inquiry.department.employees") }}'; 
    const MASS_ROUTE_STORE     = '{{ route("mass.store") }}';
    const MASS_IMG_PATH        = "{{ asset('images/employee/') }}";
    const MASS_CSRF            = '{{ csrf_token() }}';
    const MASS_STAGE           = 'lead'; 

    // --- 1. ADD NEW ROW ---
    $(document).on('click', '.btnAddRow', function() {
        let objId  = $(this).data('object');
        let custId = $(this).data('customer');
        let tbody  = $('#cm-tbody-' + objId);

        // Build Options for Product & Department from global JSON (window.massData)
        // window.massData is defined at the top of this blade file
        let optsProd = window.massData.products.map(p => 
            `<option value="${p.id}">${p.article_group}</option>`
        ).join('');
        
        let optsDept = window.massData.departments.map(d => 
            `<option value="${d.id}">${d.department_name}</option>`
        ).join('');

        // HTML Template for New Row
        let rowHtml = `
            <tr class="new-row align-middle" data-cust="${custId}" data-obj="${objId}" style="background-color: #f9fbfd;">
                <td style="min-width: 200px; padding: 5px;">
                    <select class="form-control select2-mass cm-prod-select" style="width: 100%;">
                        <option value="">Produkt wählen</option>
                        ${optsProd}
                    </select>
                </td>
                <td style="min-width: 160px; padding: 5px;">
                    <select class="form-control select2-mass cm-dept-select" style="width: 100%;">
                        <option value="">Abteilung wählen</option>
                        ${optsDept}
                    </select>
                </td>
                <td style="min-width: 160px; padding: 5px;">
                    <select class="form-control select2-mass cm-service-select" style="width: 100%;">
                        <option value="">Service wählen</option>
                    </select>
                </td>
                <td style="min-width: 200px; padding: 5px;">
                    <select class="form-control select2-mass cm-inner-select" style="width: 100%;">
                        <option value="">Innendienst wählen</option>
                    </select>
                </td>
                <td style="min-width: 200px; padding: 5px;">
                    <select class="form-control select2-mass cm-field-select" style="width: 100%;">
                        <option value="">Außendienst wählen</option>
                    </select>
                </td>
                <td style="min-width: 140px; padding: 5px;">
                    <select class="form-control select2-mass cm-interest-select" style="width: 100%;">
                        <option value="intent">Kaufabsicht</option>
                        <option value="interest">Interesse</option>
                        <option value="option">Kaufoption</option>
                    </select>
                </td>
                <td class="text-center" style="padding: 5px;">
                    <button class="btn-cm-save cmSaveRow" title="Speichern"><i class="feather icon-save"></i></button>
                    <button class="btn-cm-del cmRemoveUnsaved" title="Entfernen"><i class="feather icon-trash"></i></button>
                </td>
            </tr>
        `;

        let $row = $(rowHtml);
        tbody.append($row);

        // Init Select2 on this specific row
        initRowPlugins($row);
        
        // Attach Events to this row
        attachRowEvents($row);
    });

    // --- 2. INITIALIZE SELECT2 ---
    function initRowPlugins($row) {
        // Standard Select2 (Product, Dept, Service, Interest)
        // CRITICAL: dropdownParent: $('#massProductModal') ensures it works inside your custom modal
        $row.find('.cm-prod-select, .cm-dept-select, .cm-service-select, .cm-interest-select').select2({
            width: '100%',
            dropdownParent: $('#massProductModal')
        });

        // Employee Select2 (With Images)
        $row.find('.cm-inner-select, .cm-field-select').select2({
            width: '100%',
            dropdownParent: $('#massProductModal'),
            templateResult: formatMassEmployee,
            templateSelection: formatMassEmployeeSelection,
            escapeMarkup: m => m
        });
    }

    // --- 3. ROW EVENTS (Change Listeners) ---
    function attachRowEvents($row) {
        const $prod    = $row.find('.cm-prod-select');
        const $dept    = $row.find('.cm-dept-select');
        const $service = $row.find('.cm-service-select');

        // Product Changed
        $prod.on('change', function() {
            loadServicesForRow($row);        // Filter services locally
            fetchEmployeesForRow($row, true); // AJAX fetch (autofill = true)
        });

        // Department Changed
        $dept.on('change', function() {
            fetchEmployeesForRow($row, false);
        });

        // Service Changed
        $service.on('change', function() {
            fetchEmployeesForRow($row, false);
        });
    }

    // --- 4. LOGIC: FILTER SERVICES (Client-Side) ---
    function loadServicesForRow($row) {
        const $prod    = $row.find('.cm-prod-select');
        const $service = $row.find('.cm-service-select');
        const pid      = $prod.val();

        // Clear existing
        $service.empty().append('<option value="">Service wählen</option>');

        if (!pid) return;

        // Filter services array (from window.massData) where product_id matches
        const list = window.massData.services.filter(s => String(s.product_id) === String(pid));

        list.forEach(s => {
            $service.append(new Option(s.phase_section, s.id));
        });

        // Refresh Select2
        $service.trigger('change.select2');
    }

    // --- 5. LOGIC: FETCH EMPLOYEES (AJAX) ---
    function fetchEmployeesForRow($row, autofill = false) {
        const $prod    = $row.find('.cm-prod-select');
        const $dept    = $row.find('.cm-dept-select');
        const $service = $row.find('.cm-service-select');
        const $inner   = $row.find('.cm-inner-select');
        const $field   = $row.find('.cm-field-select');

        const pid = $prod.val();
        let did   = $dept.val();
        let sid   = $service.val();

        if (!pid) {
            $inner.empty().trigger('change');
            $field.empty().trigger('change');
            return;
        }

        // Call your getEmployee route
        $.post(MASS_ROUTE_EMPLOYEES, {
            _token:        MASS_CSRF,
            product_id:    pid,
            department_id: did || null,
            service_id:    sid || null,
            stage:         MASS_STAGE
        })
        .done(function(res) {
            
            // Auto-Suggest Department
            if (autofill && !did && res.department_id) {
                $dept.val(res.department_id).trigger('change.select2');
            }

            // Auto-Suggest Service
            if (autofill && !sid && res.service_id) {
                // If the service isn't in the list yet, reload lists
                if ($service.find(`option[value="${res.service_id}"]`).length === 0) {
                     loadServicesForRow($row); 
                }
                $service.val(res.service_id).trigger('change.select2');
            }

            // Fill Employee Dropdowns
            fillMassEmployeeSelect($inner, res.internal_employees || [], 'Innendienst wählen');
            fillMassEmployeeSelect($field, res.external_employees || [], 'Außendienst wählen');

        })
        .fail(function(err) {
            console.error('Error loading employees', err);
        });
    }

    // Helper to populate Employee Select2
    function fillMassEmployeeSelect($select, data, placeholder) {
        $select.empty().append(`<option value="">${placeholder}</option>`);
        
        data.forEach(emp => {
            let img = emp.image ? `${MASS_IMG_PATH}/${emp.image}` : '';
            let positions = (emp.positions && emp.positions.length) ? emp.positions.join(', ') : '';
            let fullName = emp.name + ' ' + emp.lastname;

            let option = new Option(fullName, emp.id);
            $(option).attr('data-img', img);
            $(option).attr('data-positions', positions);
            
            $select.append(option);
        });
        
        $select.trigger('change.select2');
    }

    // --- 6. SELECT2 FORMATTERS (With Images) ---
    function formatMassEmployee(opt) {
        if (!opt.id) return opt.text;
        
        let $el = $(opt.element);
        let img = $el.data('img');
        let pos = $el.data('positions');

        return `
            <div style="display:flex;align-items:center;">
                ${img 
                    ? `<img src="${img}" class="rounded-circle" style="width:30px;height:30px;object-fit:cover;margin-right:10px;">` 
                    : `<div class="rounded-circle" style="width:30px;height:30px;background:#eee;margin-right:10px;"></div>`
                }
                <div style="line-height:1.2">
                    <strong style="font-size:13px;">${opt.text}</strong><br>
                    <small class="text-muted" style="font-size:10px;">${pos || ''}</small>
                </div>
            </div>
        `;
    }

    function formatMassEmployeeSelection(opt) {
        return opt.text;
    }

    // --- 7. SAVE ROW ---
    $(document).on('click', '.cmSaveRow', function() {
        let row = $(this).closest('tr');
        let btn = $(this);

        // Collect Data
        let data = {
            _token:           MASS_CSRF,
            customer_id:      row.data('cust'),
            alternative_id:   row.data('obj'),
            product_id:       row.find('.cm-prod-select').val(),
            department_id:    row.find('.cm-dept-select').val(),
            service_id:       row.find('.cm-service-select').val(),
            employee_id:      row.find('.cm-inner-select').val(),
            field_employee:   row.find('.cm-field-select').val(),
            interest:         row.find('.cm-interest-select').val(),
            realization_time: 'soon' 
        };

        if(!data.product_id) { 
            Swal.fire('Fehler', 'Bitte wählen Sie ein Produkt.', 'error'); 
            return; 
        }

        // Send AJAX
        $.post(MASS_ROUTE_STORE, data)
        .done(function(res) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Gespeichert', 
                toast: true, 
                position: 'top-end', 
                timer: 2000, 
                showConfirmButton: false 
            });

            // Lock UI & Visual Feedback
            row.find('select').prop('disabled', true);
            row.css('background-color', '#ffffff'); 
            
            // Switch Save -> Delete Button
            btn.remove();
            let delBtn = row.find('.cmRemoveUnsaved');
            delBtn.removeClass('cmRemoveUnsaved')
                  .addClass('cmDeleteRow')
                  .attr('data-id', res.id)
                  .attr('title', 'Löschen');
        })
        .fail(function(xhr) {
            Swal.fire('Fehler', 'Speichern fehlgeschlagen.', 'error');
        });
    });

    // --- 8. DELETE ROW ---
    $(document).on('click', '.cmDeleteRow', function() {
        let id = $(this).data('id');
        let row = $(this).closest('tr');

        Swal.fire({
            title: 'Löschen?',
            text: "Möchten Sie dieses Produkt wirklich entfernen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ja, löschen'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/lead/mass-manager/delete-product/' + id, // Check this route matches web.php
                    type: 'DELETE',
                    data: { _token: MASS_CSRF },
                    success: function() { 
                        row.fadeOut(300, function() { $(this).remove(); }); 
                    }
                });
            }
        });
    });

    // Remove unsaved row (Visual only)
    $(document).on('click', '.cmRemoveUnsaved', function() {
        $(this).closest('tr').fadeOut(200, function(){ $(this).remove(); });
    });

});
</script>


<script>
$(document).ready(function() {

    // ==========================================
    // 1. MODAL OPEN / CLOSE (You already have this)
    // ==========================================
    $('#openMassManager').on('click', function(e) {
        e.preventDefault();
        $('#massProductModal').fadeIn(200);
    });

    $('#closeMassManager').on('click', function() {
        $('#massProductModal').fadeOut(200);
    });

    $(window).on('click', function(event) {
        if ($(event.target).is('#massProductModal')) {
            $('#massProductModal').fadeOut(200);
        }
    });

    // ==========================================
    // 2. THE MISSING PART: LOAD DATA AJAX
    // ==========================================
    $('#cmBtnLoad').on('click', function() {
        // 1. Get Values
        let source = $('#cmFilterSource').val();
        let prod   = $('#cmFilterProduct').val();
        let search = $('#cmFilterSearch').val();

        // 2. Optional: simple validation
        if(!source && !prod && !search) {
            Swal.fire('Info', 'Bitte mindestens einen Filter wählen (Quelle, Produkt oder Suche).', 'info');
            return;
        }

        // 3. Show Loading Spinner
        $('#cmResultsArea').html('<div style="text-align:center; padding:50px; color:#666;"><div class="spinner-border text-primary" role="status"></div><br>Daten werden geladen...</div>');

        // 4. Perform AJAX Call
        $.ajax({
            url: "{{ route('mass.load') }}", // Ensure this route exists in web.php
            method: 'GET',
            data: { 
                source: source, 
                product_id: prod, 
                search: search 
            },
            success: function(response) {
                // 5. Inject the Partial HTML into the container
                $('#cmResultsArea').html(response);
            },
            error: function(xhr) {
                console.error(xhr);
                $('#cmResultsArea').html('<div class="text-danger text-center p-5">Fehler beim Laden der Daten.</div>');
            }
        });
    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Get ID from URL param OR Blade session
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight_id') || @json(session('highlight_lead_id'));

    if (highlightId) {
        const row = document.getElementById('lead-row-' + highlightId);
        if (row) {
            // Wait a split second to ensure table is rendered
            setTimeout(() => {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);

            // Optional: Clean up URL after highlighting (removes ?highlight_id=123)
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: newUrl}, '', newUrl);
        }
    }
});
</script>

<script>
$(document).ready(function() {
    // Handle the visual toggle switch change
    $('#chkMyCustomers').on('change', function() {
        const isChecked = $(this).is(':checked');
        
        // Update visual state (optional, page will reload anyway)
        if(isChecked) {
            $('.my-customer-wrapper').addClass('active');
            $('#inputMyCustomers').val('1'); // Update hidden input in filter form
        } else {
            $('.my-customer-wrapper').removeClass('active');
            $('#inputMyCustomers').val('0'); // Update hidden input in filter form
        }

        // Submit the main filter form to reload the page with new filter
        $('#leadFilterForm').submit();
    });
});
</script>
@endsection
