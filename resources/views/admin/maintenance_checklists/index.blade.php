@extends('admin.layouts.app')

@section('title', 'Wartungs-Checklisten')

@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

<style>
    :root {
        --mcl-blue: #74b2d4;
        --mcl-green: #95c11f;
        --mcl-black: #505050;
        --mcl-white: #ffffff;
        --mcl-border: #d1d5db;
    }

    .mcl-shell {
        background: var(--mcl-white);
        border-radius: 16px;
        border: 1px solid var(--mcl-border);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .mcl-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .mcl-header-title {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .mcl-header-title-logo {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--mcl-blue);
        color: var(--mcl-white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        font-weight: 600;
    }

    .mcl-header-title h1 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--mcl-black);
    }

    .mcl-header-title small {
        display: block;
        font-size: .8rem;
        color: #4b5563;
    }

    .mcl-header-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .5rem;
    }

    .mcl-tabs {
        display: inline-flex;
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        overflow: hidden;
        background: var(--mcl-white);
    }

    .mcl-tabs a {
        padding: .35rem .85rem;
        font-size: .8rem;
        text-decoration: none;
        color: #4b5563;
        border-right: 1px solid var(--mcl-border);
        white-space: nowrap;
    }

    .mcl-tabs a:last-child {
        border-right: none;
    }

    .mcl-tabs a.active {
        background: var(--mcl-black);
        color: var(--mcl-white);
    }

    .mcl-input {
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        padding: .4rem .75rem;
        font-size: .85rem;
        min-width: 200px;
    }

    .mcl-select {
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        padding: .4rem .75rem;
        font-size: .85rem;
    }

    .mcl-btn {
        border-radius: 999px;
        padding: .45rem .9rem;
        font-size: .85rem;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        cursor: pointer;
        background: #8dc53d;
        color: var(--mcl-white);
        border: 1px solid transparent;
    }

    .mcl-btn-outline {
        background: var(--mcl-white);
        color: var(--mcl-black);
        border-color: var(--mcl-blue);
    }

    .mcl-btn-ghost {
        background: transparent;
        color: var(--mcl-black);
        border-color: transparent;
    }

    .mcl-btn-sm {
        padding: .3rem .7rem;
        font-size: .8rem;
    }

    .mcl-view-toggle {
        display: inline-flex;
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        overflow: hidden;
    }

    .mcl-view-toggle button {
        border: none;
        background: transparent;
        padding: .35rem .7rem;
        cursor: pointer;
        font-size: .8rem;
        color: #4b5563;
    }

    .mcl-view-toggle button.active {
        background: var(--mcl-black);
        color: var(--mcl-white);
    }

    /* Card grid */
    .mcl-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1rem;
    }

    .mcl-card {
        border-radius: 14px;
        border: 1px solid var(--mcl-border);
        padding: .8rem .8rem .7rem;
        display: flex;
        flex-direction: column;
        gap: .4rem;
        position: relative;
        background: var(--mcl-white);
    }

    .mcl-card-header {
        display: flex;
        gap: .6rem;
        margin-top:20px;
    }

    .mcl-card-logo {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #e5f1f8;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        font-size: .8rem;
        font-weight: 600;
        color: var(--mcl-black);
    }

    .mcl-card-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .mcl-card-title {
        flex: 1;
    }

    .mcl-card-title h2 {
        margin: 0;
        font-size: .95rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--mcl-black);
    }

    .mcl-card-title small {
        display: block;
        font-size: .75rem;
        color: #6b7280;
        margin-top: .1rem;
    }

    .mcl-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .3rem .5rem;
        font-size: .7rem;
        color: #6b7280;
        margin-top: .25rem;
    }

    .mcl-pill {
        border-radius: 999px;
        padding: .1rem .6rem;
        border: 1px solid var(--mcl-border);
        background: #f9fafb;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mcl-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: .4rem;
        gap: .5rem;
    }

    .mcl-card-footer-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
    }

    .mcl-status-badge {
        border-radius: 999px;
        padding: .1rem .55rem;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        border: 1px solid var(--mcl-border);
        background: #f9fafb;
        color: #111827;
    }

    .mcl-status-active {
        background: #f3fae5;
        border-color: var(--mcl-green);
        color: #3f6212;
    }

    .mcl-status-draft {
        background: #eaf3fb;
        border-color: var(--mcl-blue);
        color: #1d4ed8;
    }

    .mcl-status-archived {
        background: #f3f4f6;
        color: #4b5563;
    }

    .mcl-card-creator {
        display: flex;
        align-items: center;
        gap: .3rem;
        margin-top: .3rem;
        font-size: .7rem;
        color: #4b5563;
    }

    .mcl-creator-avatar {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        overflow: hidden;
        border: 1px solid var(--mcl-border);
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        font-size: .7rem;
    }

    .mcl-creator-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* List table */
    .mcl-table-wrapper {
        overflow: auto;
    }

    .mcl-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .85rem;
    }

    .mcl-table th,
    .mcl-table td {
        padding: .45rem .6rem;
        border-bottom: 1px solid var(--mcl-border);
        text-align: left;
        white-space: nowrap;
    }

    .mcl-table th {
        font-size: .8rem;
        color: #4b5563;
        font-weight: 500;
        background: #f9fafb;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .mcl-table tr:hover td {
        background: #f3f4f6;
    }

    /* Modal */
    .mcl-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }

    .mcl-modal-backdrop.active {
        display: flex;
    }

   
    /* Make the modal a proper flex container with a fixed viewport height */
    .mcl-modal {
        width: 100%;
        max-width: 860px;
        max-height: 90vh;
        background: var(--mcl-white);
        border-radius: 16px;
        border: 1px solid var(--mcl-border);
        display: flex;
        flex-direction: column;
        overflow: hidden; /* keep header/footer fixed, body scrolls */
    }

    /* Let the body take the remaining height and scroll vertically */
    .mcl-modal-body {
        padding: .9rem 1.1rem;
        flex: 1 1 auto;    /* <-- important */
        min-height: 0;     /* <-- important for flex children */
        overflow-y: auto;  /* vertical scroll */
    }

    .mcl-items-shell {
        max-height: 50vh;
        overflow-y: auto;
    }
    .mcl-modal-header {
        padding: .9rem 1.1rem;
        border-bottom: 1px solid var(--mcl-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .7rem;
    }

    .mcl-modal-header-left {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .mcl-modal-header h2 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
    }

    .mcl-modal-body {
        padding: .9rem 1.1rem;
        overflow: auto;
    }

    .mcl-modal-footer {
        padding: .9rem 1.1rem;
        border-top: 1px solid var(--mcl-border);
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
    }

    .mcl-form-row {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: .75rem;
    }

    .mcl-form-col {
        flex: 1 1 200px;
        min-width: 200px;
    }

    .mcl-label {
        font-size: .75rem;
        font-weight: 500;
        color: #374151;
        display: block;
        margin-bottom: .2rem;
    }

    .mcl-control,
    .mcl-control-textarea,
    .mcl-control-file {
        width: 100%;
        border-radius: 10px;
        border: 1px solid var(--mcl-border);
        padding: .4rem .55rem;
        font-size: .8rem;
        background: var(--mcl-white);
        color: var(--mcl-black);
    }

    .mcl-control-textarea {
        min-height: 60px;
        resize: vertical;
    }

    .mcl-control-file {
        padding: .3rem .55rem;
    }

    .mcl-items-shell {
        margin-top: .9rem;
        border-radius: 12px;
        border: 1px dashed var(--mcl-border);
        padding: .6rem;
        background: #f9fafb;
    }

    .mcl-items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .4rem;
        font-size: .8rem;
        color: #4b5563;
    }

    .mcl-item-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: .5rem;
        padding: .5rem .5rem;
        margin-bottom: .35rem;
        border-radius: 10px;
        background: var(--mcl-white);
        border: 1px solid var(--mcl-border);
        cursor: grab;
    }

    .mcl-item-row.dragging {
        opacity: .7;
        border-style: dashed;
    }

    .mcl-item-handle {
        width: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: grab;
        color: #6b7280;
        font-size: 1rem;
    }

    .mcl-item-main {
        flex: 1 1 210px;
        min-width: 210px;
    }

    .mcl-item-side {
        flex: 1 1 160px;
        min-width: 160px;
        display: flex;
        flex-direction: column;
        gap: .25rem;
    }

    .mcl-item-footer {
        display: flex;
        align-items: center;
        gap: .4rem;
        flex-wrap: wrap;
        font-size: .75rem;
    }

    .mcl-item-footer label {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        cursor: pointer;
    }

    /* Custom JS menu */
    .mcl-menu-shell {
        position: relative;
    }

    .mcl-menu-trigger {
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        background: var(--mcl-white);
        color: var(--mcl-black);
        cursor: pointer;
        padding: 0;
    }

    .mcl-menu {
        position: absolute;
        top: 100%;
        right: -172px;
        margin-top: -8.75rem;
        background: var(--mcl-white);
        border: 1px solid var(--mcl-border);
        border-radius: 12px;
        min-width: 190px;
        padding: .25rem 0;
        display: none;
        z-index: 40;
    }

    .mcl-menu.open {
        display: block;
    }

    .mcl-menu-item {
        width: 100%;
        padding: .4rem .75rem;
        border: 0;
        background: transparent;
        text-align: left;
        font-size: .8rem;
        cursor: pointer;
        color: var(--mcl-black);
    }

    .mcl-menu-item:hover {
        background: #f3f4f6;
    }

    .mcl-menu-item-danger {
        color: #b91c1c;
    }

    /* Drawer preview */
    .mcl-drawer-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.3);
        display: none;
        justify-content: flex-end;
        align-items: stretch;
        z-index: 45;
    }

    .mcl-drawer-backdrop.active {
        display: flex;
    }

    .mcl-drawer {
        width: 420px;
        max-width: 100%;
        height: 100%;
        background: var(--mcl-white);
        border-left: 1px solid var(--mcl-border);
        display: flex;
        flex-direction: column;
    }

    .mcl-drawer-header {
        padding: .75rem 1rem;
        border-bottom: 1px solid var(--mcl-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .5rem;
    }

    .mcl-drawer-header h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--mcl-black);
    }

    .mcl-drawer-close {
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        background: var(--mcl-white);
        color: var(--mcl-black);
        cursor: pointer;
        padding: 0;
    }

    .mcl-drawer-body {
        padding: .75rem 1rem 1rem;
        overflow: auto;
        font-size: .85rem;
    }

    .mcl-preview-meta p {
        margin: 0 0 .25rem 0;
    }

    .mcl-preview-pill {
        display: inline-flex;
        padding: .1rem .55rem;
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        font-size: .75rem;
        margin: 0 .25rem .25rem 0;
        background: #f9fafb;
    }

    .mcl-preview-items {
        margin-top: .75rem;
    }

    .mcl-preview-item {
        border-radius: 10px;
        border: 1px solid var(--mcl-border);
        padding: .45rem .55rem;
        margin-bottom: .4rem;
        background: #f9fafb;
    }

    .mcl-preview-item-title {
        font-size: .8rem;
        font-weight: 600;
        margin-bottom: .1rem;
        color: var(--mcl-black);
    }

    .mcl-preview-item-meta {
        font-size: .75rem;
        color: #4b5563;
    }

    .mcl-hidden {
        display: none !important;
    }

        /* Bulk selection + highlighting */
    .mcl-bulk-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .35rem .75rem;
        margin-bottom: .75rem;
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        background: #f9fafb;
        font-size: .8rem;
    }

    .mcl-bulk-bar-left {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-weight: 500;
        color: #111827;
    }

    .mcl-bulk-count {
        padding: .1rem .6rem;
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        background: #ffffff;
    }

    .mcl-bulk-bar-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .35rem;
    }

    .mcl-a4{
        page-break-after: always;
        }
        .mcl-a4:last-child{
        page-break-after: auto;
        }


    .mcl-bulk-btn {
        border-radius: 999px;
        border: 1px solid var(--mcl-border);
        background: #ffffff;
        padding: .3rem .7rem;
        font-size: .75rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }

    .mcl-bulk-btn-danger {
        border-color: #b91c1c;
        color: #b91c1c;
    }

    .mcl-bulk-status-group {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        margin-left: .4rem;
        padding-left: .4rem;
        border-left: 1px dashed var(--mcl-border);
    }

    .mcl-bulk-status-group span {
        font-size: .75rem;
        color: #6b7280;
    }

    .mcl-card-select {
        position: absolute;
        top: .45rem;
        left: .45rem;
        z-index: 2;
        background: rgba(255,255,255,0.85);
        border-radius: 999px;
        padding: .15rem .3rem;
        border: 1px solid rgba(209,213,219,0.9);
    }

    .mcl-card-select input[type="checkbox"] {
        cursor: pointer;
    }

    .mcl-card.mcl-selected {
        border-color: #74b2d4;
        box-shadow: 0 0 0 1px rgba(79,70,229,0.25);
        background: #74b2d433;
    }

    #mcl-table tbody tr.mcl-selected td {
        background: #eef2ff;
    }

    @media (max-width: 640px) {
        .mcl-shell {
            padding: 1rem;
        }
        .mcl-modal {
            max-width: 100%;
            border-radius: 0;
        }
    }

        .mcl-preview-items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: .75rem 0 .35rem 0;
    }

    .mcl-preview-items-header h4 {
        margin: 0;
        font-size: .9rem;
        font-weight: 600;
        color: var(--mcl-black);
    }

    .mcl-preview-items-hint {
        font-size: .75rem;
        color: #6b7280;
    }

    .mcl-preview-items-list {
        margin-top: .25rem;
    }

    .mcl-preview-item {
        display: flex;
        align-items: flex-start;
        gap: .5rem;
        border-radius: 10px;
        border: 1px solid var(--mcl-border);
        padding: .45rem .55rem;
        margin-bottom: .4rem;
        background: #f9fafb;
    }

    .mcl-preview-item-main {
        flex: 1;
        min-width: 0;
    }

    .mcl-preview-item-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: .5rem;
        margin-bottom: .2rem;
    }

    .mcl-preview-item-title {
        font-size: .8rem;
        font-weight: 600;
        color: var(--mcl-black);
    }

    .mcl-preview-item-meta {
        font-size: .75rem;
        color: #4b5563;
    }

    .mcl-preview-item-control {
        margin-top: .2rem;
    }

    .mcl-preview-option-group {
        display: flex;
        flex-direction: column;
        gap: .15rem;
        margin-top: .15rem;
    }

    .mcl-preview-option {
        font-size: .8rem;
        color: #111827;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }


        /* PDF print layout */
        .mcl-a4 {
        width: 210mm;
        min-height: 297mm;
        padding: 14mm 14mm;
        box-sizing: border-box;
        background: #fff;
        color: #111827;
        font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }

        .mcl-a4 + .mcl-a4 { margin-top: 10mm; }

        .mcl-pdf-header {
        display:flex; align-items:flex-start; justify-content:space-between;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 8mm; margin-bottom: 8mm;
        }

        .mcl-pdf-title { font-size: 18px; font-weight: 700; margin: 0 0 2mm 0; }
        .mcl-pdf-sub { font-size: 12px; color:#6b7280; margin:0; }

        .mcl-pdf-meta {
        font-size: 11px; color:#374151; text-align:right;
        }
        .mcl-pdf-meta div { margin-bottom: 2mm; }

        .mcl-pdf-section-title {
        font-size: 13px; font-weight: 700;
        margin: 0 0 4mm 0;
        padding: 2mm 3mm;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        }

        .mcl-pdf-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 4mm 4mm;
        margin-bottom: 3mm;
        page-break-inside: avoid;
        }

        .mcl-pdf-item-label { font-size: 12px; font-weight: 600; margin-bottom: 1mm; }
        .mcl-pdf-item-meta  { font-size: 10px; color:#6b7280; margin-bottom: 2mm; }

        .mcl-pdf-line {
        display:flex; gap: 3mm; align-items:center;
        }
        .mcl-pdf-box {
        width: 5mm; height: 5mm; border: 1px solid #111827; border-radius: 2px;
        }
        .mcl-pdf-input {
        flex: 1;
        border-bottom: 1px solid #9ca3af;
        height: 6mm;
        }

        .mcl-pdf-footer {
        margin-top: 10mm;
        font-size: 10px; color:#6b7280;
        border-top: 1px solid #e5e7eb;
        padding-top: 4mm;
        }

       /* PDF render root must be layout-renderable for html2canvas */
        #mcl-pdf-render-root{
        position: fixed;
        top: 0;
        left: -10000px;      /* put it far offscreen (reliable) */
        width: 210mm;
        background: #fff;
        pointer-events: none;
        z-index: 0;
        transform: none;     /* avoid transform issues */
        }


        .mcl-pdf-banner{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 10mm;
            padding: 8mm 9mm;
            border-radius: 10px;
            background: linear-gradient(135deg, #74b2d4 0%, #95c11f 110%);
            color: #fff;
            margin-bottom: 8mm;
            page-break-inside: avoid;
            }

            .mcl-pdf-banner-left{
            display:flex;
            align-items:center;
            gap: 6mm;
            min-width: 0;
            }

            .mcl-pdf-badge{
            width: 14mm;
            height: 14mm;
            border-radius: 8px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight: 900;
            font-size: 10pt;
            letter-spacing: .04em;
            }

            .mcl-pdf-banner-titlewrap{
            min-width: 0;
            }

            .mcl-pdf-banner-kicker{
            font-size: 10pt;
            opacity: .92;
            margin: 0 0 1mm 0;
            }

            .mcl-pdf-banner-title{
            font-size: 18pt;
            font-weight: 900;
            margin: 0;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120mm;
            }

            .mcl-pdf-banner-right{
            text-align:right;
            font-size: 10pt;
            opacity: .95;
            white-space: nowrap;
            }
            .mcl-pdf-banner-right div{
            margin-bottom: 1.2mm;
            }

            /* A4 modal viewer */
            .mcl-a4-modal-body{
                background: #f3f4f6;
                padding: 18px !important;
            }

            .mcl-a4-viewer{
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 16px;
            }

            /* Make A4 look like paper in modal */
            .mcl-a4{
                box-shadow: 0 10px 30px rgba(0,0,0,0.12);
                border-radius: 10px;
                overflow: hidden;
            }

            /* On small screens: scale down A4 to fit */
            @media (max-width: 900px){
                .mcl-a4{
                    width: 100% !important;
                    min-height: auto !important;
                    padding: 14px !important;
                }
            }





</style>
@endsection

@section('content')
@php
    $currentScope = $scope ?? 'active';
@endphp

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Produkt</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">Wartungs-Checklisten</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="mcl-shell">
                <div class="mcl-header"> 
                    <div class="mcl-bulk-bar mcl-hidden" id="mcl-bulk-bar">
                        <div class="mcl-bulk-bar-left">
                            <span class="mcl-bulk-count" id="mcl-bulk-count">0 ausgewählt</span>
                            <span>Mehrere Checklisten bearbeiten</span>
                        </div>
                        <div class="mcl-bulk-bar-actions">
                            @if($currentScope === 'active' || $currentScope === 'draft')
                                <button type="button" class="mcl-bulk-btn" data-bulk-action="archive">
                                    Archivieren
                                </button>
                                <button type="button" class="mcl-bulk-btn mcl-bulk-btn-danger" data-bulk-action="trash">
                                    In Papierkorb
                                </button>
                            @elseif($currentScope === 'archived')
                                <button type="button" class="mcl-bulk-btn" data-bulk-action="unarchive">
                                    Archiv aufheben
                                </button>
                                <button type="button" class="mcl-bulk-btn mcl-bulk-btn-danger" data-bulk-action="trash">
                                    In Papierkorb
                                </button>
                            @elseif($currentScope === 'deleted')
                                <button type="button" class="mcl-bulk-btn" data-bulk-action="restore">
                                    Wiederherstellen
                                </button>
                            @endif

                            {{-- Status ändern, unabhängig vom Scope --}}
                            <div class="mcl-bulk-status-group">
                                <span>Status:</span>
                                <button type="button" class="mcl-bulk-btn" data-bulk-action="status_active">Aktiv</button>
                                <button type="button" class="mcl-bulk-btn" data-bulk-action="status_draft">Entwurf</button>
                                <button type="button" class="mcl-bulk-btn" data-bulk-action="status_archived">Archiviert</button>
                            </div>
                        </div>
                    </div>

                    <div>
                    
                        <div style="margin-top:.5rem;">
                            <div class="mcl-tabs">
                                <a href="{{ route('admin.maintenance_checklists.index', ['scope' => 'active']) }}"
                                   class="{{ $currentScope === 'active' ? 'active' : '' }}">
                                    Aktiv / Entwurf
                                </a>
                                <a href="{{ route('admin.maintenance_checklists.index', ['scope' => 'archived']) }}"
                                   class="{{ $currentScope === 'archived' ? 'active' : '' }}">
                                    Archiv
                                </a>
                                <a href="{{ route('admin.maintenance_checklists.index', ['scope' => 'deleted']) }}"
                                   class="{{ $currentScope === 'deleted' ? 'active' : '' }}">
                                    Papierkorb
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mcl-header-actions">
                        <input type="text"
                               id="mcl-search"
                               class="mcl-input"
                               placeholder="Nach Titel, Typ, Marke suchen…"
                               value="{{ $search ?? '' }}"/>

                        <select id="mcl-sort" class="mcl-select">
                            <option value="title_asc"  {{ ($sort ?? '') === 'title_asc' ? 'selected' : '' }}>Sortierung: Titel A–Z</option>
                            <option value="title_desc" {{ ($sort ?? '') === 'title_desc' ? 'selected' : '' }}>Sortierung: Titel Z–A</option>
                            <option value="latest"     {{ ($sort ?? '') === 'latest' ? 'selected' : '' }}>Sortierung: Neueste zuerst</option>
                        </select>

                        <div class="mcl-view-toggle">
                            <button type="button" data-view="card" class="active">Karten</button>
                            <button type="button" data-view="list">Tabelle</button>
                        </div>

                        @if($currentScope !== 'deleted')
                            <button type="button" class="mcl-btn" id="mcl-btn-create">
                                <span>Neue Wartungs-Checkliste</span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- CARD VIEW --}}
                <div id="mcl-view-card">
                    <div class="mcl-grid" id="mcl-card-grid">
                        @foreach($maintenanceChecklists as $checklist)
                            @php
                                $creator = optional($checklist->creatorEmployee);
                                $creatorName = $creator?->full_name ?? $creator?->name ?? 'Unbekannt';
                                $creatorInitials = collect(explode(' ', $creatorName))
                                    ->filter()
                                    ->map(fn($p) => mb_substr($p, 0, 1))
                                    ->take(2)
                                    ->implode('');
                            @endphp
                            <div class="mcl-card"
                                data-id="{{ $checklist->id }}"
                                data-title="{{ strtolower($checklist->title) }}"
                                data-title-display="{{ e($checklist->title) }}"
                                data-status="{{ $checklist->status }}"
                                data-type="{{ strtolower($checklist->type) }}"
                                data-brands="{{ strtolower($checklist->brands->pluck('name')->join(' ')) }}"
                                data-distributors="{{ strtolower($checklist->distributors->pluck('name')->join(' ')) }}">

                                {{-- bulk-select checkbox (card view) --}}
                                <label class="mcl-card-select">
                                    <input type="checkbox"
                                           class="mcl-select-checkbox"
                                           value="{{ $checklist->id }}">
                                </label>

                                <div class="mcl-card-header">
                                    <div class="mcl-card-logo">
                                        @if($checklist->logo_path)
                                            <img src="{{ asset($checklist->logo_path) }}" alt="">
                                        @else
                                            <span>{{ strtoupper(mb_substr($checklist->title, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="mcl-card-title">
                                        <h2>{{ $checklist->title }}</h2>
                                        <small>{{ $checklist->type }} · {{ $checklist->items->count() }} Felder</small>
                                        <div class="mcl-card-creator">
                                            <div class="mcl-creator-avatar">
                                                @if($creator && $creator->image)
                                                    <img src="{{ asset('images/employee/'.$creator->image) }}" alt="">
                                                @else
                                                    <span>{{ $creatorInitials }}</span>
                                                @endif
                                            </div>
                                            <span>Erstellt von {{ $creatorName }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mcl-card-meta">
                                    @foreach($checklist->brands as $brand)
                                        <span class="mcl-pill">{{ $brand->name }}</span>
                                    @endforeach
                                    @foreach($checklist->distributors as $dist)
                                        <span class="mcl-pill">{{ $dist->name }}</span>
                                    @endforeach
                                </div>

                                <div class="mcl-card-footer">
                                    <div class="mcl-card-footer-actions">
                                        <div class="mcl-menu-shell">
                                            <button type="button" class="mcl-menu-trigger" aria-label="Aktionen">⋯</button>
                                            <div class="mcl-menu">
                                                <button type="button" class="mcl-menu-item"
                                                        data-action="preview"
                                                        data-id="{{ $checklist->id }}">
                                                    Vorschau
                                                </button>
                                                <button type="button" class="mcl-menu-item"
                                                    data-action="pdf"
                                                    data-id="{{ $checklist->id }}">
                                                PDF Vorschau (A4)
                                            </button>

                                                @if($currentScope !== 'deleted')
                                                    <button type="button" class="mcl-menu-item"
                                                            data-action="edit"
                                                            data-id="{{ $checklist->id }}">
                                                        Bearbeiten
                                                    </button>
                                                @endif
                                                @if($currentScope === 'active')
                                                    <button type="button" class="mcl-menu-item"
                                                            data-action="duplicate"
                                                            data-id="{{ $checklist->id }}">
                                                        Duplizieren
                                                    </button>
                                                @endif
                                                @if($currentScope === 'active' || $currentScope === 'draft')
                                                    <button type="button" class="mcl-menu-item"
                                                            data-action="archive"
                                                            data-id="{{ $checklist->id }}">
                                                        Archivieren
                                                    </button>
                                                    <button type="button" class="mcl-menu-item mcl-menu-item-danger"
                                                            data-action="trash"
                                                            data-id="{{ $checklist->id }}">
                                                        In Papierkorb
                                                    </button>
                                                @elseif($currentScope === 'archived')
                                                    <button type="button" class="mcl-menu-item"
                                                            data-action="unarchive"
                                                            data-id="{{ $checklist->id }}">
                                                        Archiv aufheben
                                                    </button>
                                                    <button type="button" class="mcl-menu-item mcl-menu-item-danger"
                                                            data-action="trash"
                                                            data-id="{{ $checklist->id }}">
                                                        In Papierkorb
                                                    </button>
                                                @elseif($currentScope === 'deleted')
                                                    <button type="button" class="mcl-menu-item"
                                                            data-action="restore"
                                                            data-id="{{ $checklist->id }}">
                                                        Wiederherstellen
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="mcl-status-badge
                                            {{ $checklist->status === 'active' ? 'mcl-status-active' : '' }}
                                            {{ $checklist->status === 'draft' ? 'mcl-status-draft' : '' }}
                                            {{ $checklist->status === 'archived' ? 'mcl-status-archived' : '' }}">
                                            {{ ucfirst($checklist->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- LIST VIEW --}}
                <div id="mcl-view-list" class="mcl-hidden">
                    <div class="mcl-table-wrapper">
                        <table class="mcl-table" id="mcl-table">
                                <thead>
                                    <tr>
                                        <th style="width:32px;">
                                            <input type="checkbox" id="mcl-select-all-list">
                                        </th>
                                        <th>Titel</th>
                                        <th>Typ</th>
                                        <th>Status</th>
                                        <th>Felder</th>
                                        <th>Marken</th>
                                        <th>Vertrieb</th>
                                        <th>Erstellt von</th>
                                        <th>Aktualisiert</th>
                                        <th style="text-align:right;">Aktionen</th>
                                    </tr>
                                </thead> 
                            <tbody>
                                @foreach($maintenanceChecklists as $checklist)
                                    @php
                                        $creator = optional($checklist->creatorEmployee);
                                        $creatorName = $creator?->full_name ?? $creator?->name ?? 'Unbekannt';
                                    @endphp
                                    <tr
                                        data-id="{{ $checklist->id }}"
                                        data-title="{{ strtolower($checklist->title) }}"
                                        data-title-display="{{ e($checklist->title) }}"
                                        data-status="{{ $checklist->status }}"
                                        data-type="{{ strtolower($checklist->type) }}"
                                        data-brands="{{ strtolower($checklist->brands->pluck('name')->join(' ')) }}"
                                        data-distributors="{{ strtolower($checklist->distributors->pluck('name')->join(' ')) }}"
                                    >
                                        <td>
                                            <input type="checkbox"
                                                   class="mcl-select-checkbox"
                                                   value="{{ $checklist->id }}">
                                        </td>
                                        <td>{{ $checklist->title }}</td> 
                                        <td>{{ $checklist->type }}</td>
                                        <td>{{ ucfirst($checklist->status) }}</td>
                                        <td>{{ $checklist->items->count() }}</td>
                                        <td>{{ $checklist->brands->pluck('name')->join(', ') }}</td>
                                        <td>{{ $checklist->distributors->pluck('name')->join(', ') }}</td>
                                        <td>{{ $creatorName }}</td>
                                        <td>{{ $checklist->updated_at?->format('d.m.Y H:i') }}</td>
                                        <td style="text-align:right;">
                                            <div class="mcl-menu-shell">
                                                <button type="button" class="mcl-menu-trigger" aria-label="Aktionen">⋯</button>
                                                <div class="mcl-menu">
                                                    <button type="button" class="mcl-menu-item"
                                                            data-action="preview"
                                                            data-id="{{ $checklist->id }}">
                                                        Vorschau
                                                    </button>

                                                    <button type="button" class="mcl-menu-item"
                                                            data-action="pdf"
                                                            data-id="{{ $checklist->id }}">
                                                        PDF Vorschau (A4)
                                                    </button>

                                                    @if($currentScope !== 'deleted')
                                                        <button type="button" class="mcl-menu-item"
                                                                data-action="edit"
                                                                data-id="{{ $checklist->id }}">
                                                            Bearbeiten
                                                        </button>
                                                    @endif
                                                    @if($currentScope === 'active')
                                                        <button type="button" class="mcl-menu-item"
                                                                data-action="duplicate"
                                                                data-id="{{ $checklist->id }}">
                                                            Duplizieren
                                                        </button>
                                                    @endif
                                                    @if($currentScope === 'active' || $currentScope === 'draft')
                                                        <button type="button" class="mcl-menu-item"
                                                                data-action="archive"
                                                                data-id="{{ $checklist->id }}">
                                                            Archivieren
                                                        </button>
                                                        <button type="button" class="mcl-menu-item mcl-menu-item-danger"
                                                                data-action="trash"
                                                                data-id="{{ $checklist->id }}">
                                                            In Papierkorb
                                                        </button>
                                                    @elseif($currentScope === 'archived')
                                                        <button type="button" class="mcl-menu-item"
                                                                data-action="unarchive"
                                                                data-id="{{ $checklist->id }}">
                                                            Archiv aufheben
                                                        </button>
                                                        <button type="button" class="mcl-menu-item mcl-menu-item-danger"
                                                                data-action="trash"
                                                                data-id="{{ $checklist->id }}">
                                                            In Papierkorb
                                                        </button>
                                                    @elseif($currentScope === 'deleted')
                                                        <button type="button" class="mcl-menu-item"
                                                                data-action="restore"
                                                                data-id="{{ $checklist->id }}">
                                                            Wiederherstellen
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(method_exists($maintenanceChecklists, 'links'))
                    <div style="margin-top: 1rem;">
                        {{ $maintenanceChecklists->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- CREATE / EDIT MODAL --}}
<div class="mcl-modal-backdrop" id="mcl-modal-backdrop">
    <div class="mcl-modal">
        <div class="mcl-modal-header">
            <div class="mcl-modal-header-left">
                <h2 id="mcl-modal-title">Neue Wartungs-Checkliste</h2>
                <button type="button"
                        class="mcl-btn mcl-btn-sm mcl-btn-ghost"
                        id="mcl-help-btn">
                    Hilfe
                </button>
            </div>
            <button type="button" class="mcl-btn mcl-btn-sm mcl-btn-outline" id="mcl-modal-close">
                Schließen
            </button>
        </div>
        <form id="mcl-form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mcl-modal-body">
                <input type="hidden" id="mcl-form-id" name="id">

                <div class="mcl-form-row">
                    <div class="mcl-form-col">
                        <label class="mcl-label">Titel</label>
                        <input type="text" class="mcl-control" name="title" id="mcl-form-title" required>
                    </div>
                    <div class="mcl-form-col">
                        <label class="mcl-label">Typ</label>
                        <input type="text" class="mcl-control" name="type" id="mcl-form-type" placeholder="z.B. Wartung, Abnahme">
                    </div>
                    <div class="mcl-form-col">
                        <label class="mcl-label">Status</label>
                        <select class="mcl-control" name="status" id="mcl-form-status">
                            <option value="draft">Entwurf</option>
                            <option value="active">Aktiv</option>
                            <option value="archived">Archiviert</option>
                        </select>
                    </div>
                </div>

                <div class="mcl-form-row">
                    <div class="mcl-form-col">
                        <label class="mcl-label">Logo</label>
                        <input type="file" class="mcl-control-file" name="logo" id="mcl-form-logo">
                    </div>
                    <div class="mcl-form-col">
                        <label class="mcl-label">Marken</label>
                        <select class="mcl-control mcl-select2" name="brand_ids[]" id="mcl-form-brands" multiple>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mcl-form-col">
                        <label class="mcl-label">Vertriebspartner</label>
                        <select class="mcl-control mcl-select2" name="distributor_ids[]" id="mcl-form-distributors" multiple>
                            @foreach($distributors as $dist)
                                <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mcl-form-row">
                    <div class="mcl-form-col" style="flex-basis:100%; min-width:100%;">
                        <label class="mcl-label">Beschreibung</label>
                        <textarea class="mcl-control-textarea" name="description" id="mcl-form-description"
                                  placeholder="Kurzbeschreibung, wann und wie die Checkliste verwendet wird."></textarea>
                    </div>
                </div>

                {{-- ITEMS --}}
                <div class="mcl-items-shell">
                    <div class="mcl-items-header">
                        <span>Checklisten-Positionen</span>
                        <button type="button" class="mcl-btn mcl-btn-sm mcl-btn-outline" id="mcl-btn-add-item">
                            + Feld hinzufügen
                        </button>
                    </div>
                    <div id="mcl-items-list"></div>
                </div>

                <input type="hidden" name="items_json" id="mcl-items-json">
            </div>
            <div class="mcl-modal-footer">
                <button type="button" class="mcl-btn mcl-btn-outline" id="mcl-modal-cancel">Abbrechen</button>
                <button type="submit" class="mcl-btn" id="mcl-modal-save">Checkliste speichern</button>
            </div>
        </form>
    </div>
</div>

{{-- PREVIEW DRAWER --}}
<div class="mcl-drawer-backdrop" id="mcl-drawer-backdrop">
    <div class="mcl-drawer">
        <div class="mcl-drawer-header">
            <h3 id="mcl-drawer-title">Checkliste</h3>
            <button type="button" class="mcl-drawer-close" id="mcl-drawer-close">×</button>
        </div>
        <div class="mcl-drawer-body" id="mcl-drawer-body">
            {{-- dynamically filled by JS --}}
        </div>
    </div>
</div>
 
{{-- A4 PREVIEW MODAL (HTML, no PDF) --}}
<div class="mcl-modal-backdrop" id="mcl-pdf-backdrop">
    <div class="mcl-modal" style="max-width: 1060px;">
        <div class="mcl-modal-header">
            <div class="mcl-modal-header-left">
                <h2 id="mcl-pdf-title">A4 Vorschau</h2>
            </div>
            <div style="display:flex; gap:.5rem; align-items:center;">
                <button type="button" class="mcl-btn mcl-btn-sm mcl-btn-outline" id="mcl-pdf-close">
                    Schließen
                </button>
            </div>
        </div>

        {{-- IMPORTANT: this is scrollable and shows A4 pages centered --}}
        <div class="mcl-modal-body mcl-a4-modal-body">
            <div id="mcl-a4-viewer" class="mcl-a4-viewer"></div>
        </div>
    </div>
</div>

 

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
 
<script>
    window.MCL_BRANDS = @json($brands->pluck('name', 'id'));
    window.MCL_DISTRIBUTORS = @json($distributors->pluck('name', 'id'));
</script>

<script>
/**
 * MaintenanceChecklistUI — FULL REWRITE
 * ✅ Replaces PDF generation with an A4 HTML preview inside the existing "pdf" modal.
 * ✅ Uses #mcl-a4-viewer as the render target (must exist in modal body).
 * ✅ Keeps all existing CRUD, preview drawer, bulk actions, filtering, sorting, select2, sortable.
 *
 * NOTE:
 * - Ensure your modal contains: <div id="mcl-a4-viewer"></div>
 * - You can remove html2pdf / iframe references from the Blade if you want.
 */
const MaintenanceChecklistUI = (function () {
  // -----------------------
  // DOM refs (Main modal)
  // -----------------------
  const modalBackdrop = document.getElementById('mcl-modal-backdrop');
  const modalTitle    = document.getElementById('mcl-modal-title');
  const modalClose    = document.getElementById('mcl-modal-close');
  const modalCancel   = document.getElementById('mcl-modal-cancel');
  const helpBtn       = document.getElementById('mcl-help-btn');
  const btnCreate     = document.getElementById('mcl-btn-create');

  const viewCard      = document.getElementById('mcl-view-card');
  const viewList      = document.getElementById('mcl-view-list');
  const searchInput   = document.getElementById('mcl-search');
  const sortSelect    = document.getElementById('mcl-sort');

  const form          = document.getElementById('mcl-form');
  const itemsList     = document.getElementById('mcl-items-list');
  const itemsJson     = document.getElementById('mcl-items-json');

  // -----------------------
  // Drawer (Preview)
  // -----------------------
  const drawerBackdrop = document.getElementById('mcl-drawer-backdrop');
  const drawerTitle    = document.getElementById('mcl-drawer-title');
  const drawerBody     = document.getElementById('mcl-drawer-body');
  const drawerClose    = document.getElementById('mcl-drawer-close');

  // -----------------------
  // Bulk
  // -----------------------
  const bulkBar       = document.getElementById('mcl-bulk-bar');
  const bulkCountEl   = document.getElementById('mcl-bulk-count');
  const selectAllList = document.getElementById('mcl-select-all-list');
  const selectedIds   = new Set();

  // -----------------------
  // A4 Preview Modal (re-using existing "pdf" modal shell)
  // -----------------------
  const pdfBackdrop = document.getElementById('mcl-pdf-backdrop');
  const pdfTitleEl  = document.getElementById('mcl-pdf-title');
  const pdfCloseBtn = document.getElementById('mcl-pdf-close');
  const a4Viewer    = document.getElementById('mcl-a4-viewer'); // REQUIRED

  let sorter = null;

  // -----------------------
  // Routes
  // -----------------------
  const routes = {
    editJson:  "{{ route('admin.maintenance_checklists.edit_json', ['maintenance_checklist' => '__ID__']) }}",
    archive:   "{{ route('admin.maintenance_checklists.archive', ['maintenance_checklist' => '__ID__']) }}",
    unarchive: "{{ route('admin.maintenance_checklists.unarchive', ['maintenance_checklist' => '__ID__']) }}",
    destroy:   "{{ route('admin.maintenance_checklists.destroy', ['maintenance_checklist' => '__ID__']) }}",
    restore:   "{{ route('admin.maintenance_checklists.restore', ['id' => '__ID__']) }}",
    store:     "{{ route('admin.maintenance_checklists.store') }}",
    update:    "{{ route('admin.maintenance_checklists.update', ['maintenance_checklist' => '__ID__']) }}",
    bulk:      "{{ route('admin.maintenance_checklists.bulk') }}",
    csrf:      "{{ csrf_token() }}",
  };

  // -----------------------
  // Helpers
  // -----------------------
  function lockBody()   { document.body.style.overflow = 'hidden'; }
  function unlockBody() { document.body.style.overflow = ''; }

  function escapeHtml(s) {
    return String(s ?? '')
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;');
  }

  function chunkIntoPages(items, perPage) {
    const pages = [];
    for (let i = 0; i < items.length; i += perPage) pages.push(items.slice(i, i + perPage));
    return pages.length ? pages : [[]];
  }

  // -----------------------
  // Select2
  // -----------------------
  function initSelect2() {
    const $parent = $('#mcl-modal-backdrop');

    $('#mcl-form-brands').select2({
      placeholder: 'Marken wählen',
      allowClear: true,
      width: '100%',
      dropdownParent: $parent
    });

    $('#mcl-form-distributors').select2({
      placeholder: 'Vertriebspartner wählen',
      allowClear: true,
      width: '100%',
      dropdownParent: $parent
    });
  }

  function resetSelect2() {
    $('#mcl-form-brands').val(null).trigger('change');
    $('#mcl-form-distributors').val(null).trigger('change');
  }

  // -----------------------
  // Form (Create/Edit/Duplicate)
  // -----------------------
  function removeMethodOverride() {
    const existing = form.querySelector('input[name="_method"]');
    if (existing) existing.remove();
  }

  function fillForm(data) {
    document.getElementById('mcl-form-id').value = data.id || '';
    document.getElementById('mcl-form-title').value = data.title || '';
    document.getElementById('mcl-form-type').value = data.type || '';
    document.getElementById('mcl-form-status').value = data.status || 'draft';
    document.getElementById('mcl-form-description').value = data.description || '';

    const brandIds = data.brand_ids || [];
    const distIds  = data.distributor_ids || [];

    $('#mcl-form-brands').val(brandIds.map(id => String(id))).trigger('change');
    $('#mcl-form-distributors').val(distIds.map(id => String(id))).trigger('change');

    itemsList.innerHTML = '';
    if (Array.isArray(data.items) && data.items.length) data.items.forEach(item => addItemRow(item));
    updateItemsJson();
  }

  function openModal(mode, data) {
    modalBackdrop.classList.add('active');
    lockBody();

    if (mode === 'create') {
      modalTitle.textContent = 'Neue Wartungs-Checkliste';
      form.reset();
      document.getElementById('mcl-form-id').value = '';
      itemsList.innerHTML = '';
      resetSelect2();
      form.action = routes.store;
      removeMethodOverride();
    }

    if (mode === 'edit') {
      modalTitle.textContent = 'Wartungs-Checkliste bearbeiten';
      fillForm(data);
      form.action = routes.update.replace('__ID__', data.id);
      removeMethodOverride();
      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'PUT';
      form.appendChild(methodInput);
    }

    if (mode === 'duplicate') {
      modalTitle.textContent = 'Wartungs-Checkliste duplizieren';
      fillForm(data);
      document.getElementById('mcl-form-id').value = '';
      const titleInput = document.getElementById('mcl-form-title');
      if (titleInput && titleInput.value) titleInput.value = titleInput.value + ' (Kopie)';
      form.action = routes.store;
      removeMethodOverride();
    }

    if (!sorter && typeof Sortable !== 'undefined') {
      sorter = Sortable.create(itemsList, {
        handle: '.mcl-item-handle',
        animation: 150,
        onEnd: updateItemsJson
      });
    }
  }

  function closeModal() {
    modalBackdrop.classList.remove('active');
    unlockBody();
  }

  // -----------------------
  // Items builder
  // -----------------------
  function slugify(str) {
    return String(str ?? '')
      .trim()
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '_')
      .replace(/^_+|_+$/g, '');
  }

  function addItemRow(item = {}) {
    const row = document.createElement('div');
    row.className = 'mcl-item-row';
    row.dataset.id = item.id || '';
    row.dataset.sortOrder = item.sort_order || '0';

    const optionsText = (item.options || []).join(', ');

    row.innerHTML = `
      <div class="mcl-item-handle" title="Ziehen zum Sortieren">⋮⋮</div>
      <div class="mcl-item-main">
        <label class="mcl-label">Feldbeschriftung</label>
        <input type="text" class="mcl-control js-item-label"
               value="${escapeHtml(item.label || '')}"
               placeholder="z.B. Zustand der Pumpe">
        <label class="mcl-label" style="margin-top:.25rem;">Technischer Feldname</label>
        <input type="text" class="mcl-control js-item-name"
               value="${escapeHtml(item.field_name || '')}" readonly>
      </div>
      <div class="mcl-item-side">
        <label class="mcl-label">Feldtyp</label>
        <select class="mcl-control js-item-type">
          <option value="text"          ${item.field_type === 'text' ? 'selected' : ''}>Text</option>
          <option value="textarea"      ${item.field_type === 'textarea' ? 'selected' : ''}>Mehrzeiliger Text</option>
          <option value="select"        ${item.field_type === 'select' ? 'selected' : ''}>Auswahlliste</option>
          <option value="checkbox"      ${item.field_type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
          <option value="radio"         ${item.field_type === 'radio' ? 'selected' : ''}>Radio (Einzelauswahl)</option>
          <option value="file_image"    ${item.field_type === 'file_image' ? 'selected' : ''}>Bild hochladen</option>
          <option value="file_document" ${item.field_type === 'file_document' ? 'selected' : ''}>Dokument hochladen</option>
        </select>
        <label class="mcl-label" style="margin-top:.25rem;">Optionen (durch Komma getrennt)</label>
        <input type="text" class="mcl-control js-item-options"
               value="${escapeHtml(optionsText)}"
               placeholder="OK, Mangel, Nicht geprüft">
        <label class="mcl-label" style="margin-top:.25rem;">Hilfetext (optional)</label>
        <input type="text" class="mcl-control js-item-help"
               value="${escapeHtml(item.help_text || '')}"
               placeholder="Hinweis für den Monteur">
      </div>
      <div class="mcl-item-footer">
        <label>
          <input type="checkbox" class="js-item-required" ${item.is_required ? 'checked' : ''}>
          Pflichtfeld
        </label>
        <button type="button" class="mcl-btn mcl-btn-sm mcl-btn-outline js-item-remove">Entfernen</button>
      </div>
    `;

    itemsList.appendChild(row);
    attachRowEvents(row);
    updateItemsJson();
  }

  function attachRowEvents(row) {
    const removeBtn  = row.querySelector('.js-item-remove');
    const labelInput = row.querySelector('.js-item-label');
    const nameInput  = row.querySelector('.js-item-name');
    const inputs     = row.querySelectorAll('.js-item-type, .js-item-options, .js-item-help, .js-item-required');

    removeBtn.addEventListener('click', function () {
      row.remove();
      updateItemsJson();
    });

    labelInput.addEventListener('input', function () {
      nameInput.value = slugify(this.value);
      updateItemsJson();
    });

    inputs.forEach(input => {
      input.addEventListener('change', updateItemsJson);
      input.addEventListener('keyup', updateItemsJson);
    });
  }

  function collectItemsData() {
    const rows = itemsList.querySelectorAll('.mcl-item-row');
    const items = [];

    rows.forEach((row, index) => {
      const label      = row.querySelector('.js-item-label').value.trim();
      const name       = row.querySelector('.js-item-name').value.trim();
      const type       = row.querySelector('.js-item-type').value;
      const optionsRaw = row.querySelector('.js-item-options').value;
      const help       = row.querySelector('.js-item-help').value.trim();
      const required   = row.querySelector('.js-item-required').checked;

      const options = optionsRaw
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);

      items.push({
        id: row.dataset.id || null,
        label,
        field_name: name || slugify(label) || ('feld_' + (index + 1)),
        field_type: type,
        options,
        help_text: help,
        is_required: required,
        sort_order: index
      });
    });

    return items;
  }

  function updateItemsJson() {
    itemsJson.value = JSON.stringify(collectItemsData());
  }

  // -----------------------
  // Filtering / Sorting UI
  // -----------------------
  function sortDomNodes(containerSelector, itemSelector, sortValue) {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    const nodes = Array.from(container.querySelectorAll(itemSelector));
    nodes.sort((a, b) => {
      const titleA = (a.getAttribute('data-title') || '').toLowerCase();
      const titleB = (b.getAttribute('data-title') || '').toLowerCase();
      const idA    = parseInt(a.getAttribute('data-id') || '0', 10);
      const idB    = parseInt(b.getAttribute('data-id') || '0', 10);

      if (sortValue === 'title_asc')  return titleA.localeCompare(titleB);
      if (sortValue === 'title_desc') return titleB.localeCompare(titleA);
      if (sortValue === 'latest')     return idB - idA;
      return 0;
    });

    nodes.forEach(n => container.appendChild(n));
  }

  function filterAndSort() {
    const q = (searchInput?.value || '').toLowerCase();
    const sortValue = sortSelect?.value || '';

    const cardNodes = document.querySelectorAll('#mcl-card-grid .mcl-card');
    const rowNodes  = document.querySelectorAll('#mcl-table tbody tr');

    [cardNodes, rowNodes].forEach(nodes => {
      nodes.forEach(node => {
        const title  = node.getAttribute('data-title') || '';
        const brands = node.getAttribute('data-brands') || '';
        const dists  = node.getAttribute('data-distributors') || '';
        const type   = node.getAttribute('data-type') || '';

        const text = [title, brands, dists, type].join(' ');
        const matches = !q || text.indexOf(q) !== -1;

        node.style.display = matches ? '' : 'none';
      });
    });

    if (sortValue) {
      sortDomNodes('#mcl-card-grid', '.mcl-card', sortValue);
      sortDomNodes('#mcl-table tbody', 'tr', sortValue);
    }
  }

  function setupViewToggle() {
    document.querySelectorAll('.mcl-view-toggle button').forEach(btn => {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.mcl-view-toggle button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const view = this.getAttribute('data-view');
        if (view === 'card') {
          viewCard.classList.remove('mcl-hidden');
          viewList.classList.add('mcl-hidden');
        } else {
          viewCard.classList.add('mcl-hidden');
          viewList.classList.remove('mcl-hidden');
        }
      });
    });
  }

  function setupSearchSort() {
    if (searchInput) searchInput.addEventListener('input', filterAndSort);
    if (sortSelect)  sortSelect.addEventListener('change', filterAndSort);
    filterAndSort();
  }

  function setupCreateButton() {
    if (!btnCreate) return;
    btnCreate.addEventListener('click', () => openModal('create', {}));
  }

  // -----------------------
  // Load checklist JSON
  // -----------------------
  function loadChecklistData(id, onSuccess) {
    const url = routes.editJson.replace('__ID__', id);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.json())
      .then(data => onSuccess(data))
      .catch(err => {
        console.error(err);
        Swal.fire('Fehler', 'Die Checkliste konnte nicht geladen werden.', 'error');
      });
  }

  // -----------------------
  // Dropdown Menus
  // -----------------------
  function closeAllMenus() {
    document.querySelectorAll('.mcl-menu').forEach(menu => menu.classList.remove('open'));
  }

  function setupMenus() {
    document.addEventListener('click', function (e) {
      const trigger = e.target.closest('.mcl-menu-trigger');
      const menuItem = e.target.closest('.mcl-menu-item');

      if (trigger) {
        e.preventDefault();
        const shell = trigger.closest('.mcl-menu-shell');
        const menu = shell ? shell.querySelector('.mcl-menu') : null;
        if (!menu) return;

        const isOpen = menu.classList.contains('open');
        closeAllMenus();
        if (!isOpen) menu.classList.add('open');
        return;
      }

      if (menuItem) {
        e.preventDefault();

        const action = menuItem.dataset.action;
        const id     = menuItem.dataset.id;

        const host = menuItem.closest('.mcl-card, tr');
        const titleDisplay = host ? (host.getAttribute('data-title-display') || '') : '';

        closeAllMenus();

        if (action === 'edit')       return loadChecklistData(id, data => openModal('edit', data));
        if (action === 'duplicate')  return loadChecklistData(id, data => openModal('duplicate', data));
        if (action === 'preview')    return loadChecklistData(id, data => openPreviewDrawer(data));

        // ✅ "pdf" action now opens A4 HTML modal (no pdf)
        if (action === 'pdf') {
          return loadChecklistData(id, data => openA4Modal(data));
        }

        if (action === 'archive')    return confirmArchive(id, titleDisplay);
        if (action === 'unarchive')  return confirmUnarchive(id, titleDisplay);
        if (action === 'trash')      return confirmDelete(id, titleDisplay);
        if (action === 'restore')    return confirmRestore(id, titleDisplay);

        return;
      }

      if (!e.target.closest('.mcl-menu') && !e.target.closest('.mcl-menu-trigger')) closeAllMenus();
    });
  }

  // -----------------------
  // A4 HTML Preview (Modal)
  // -----------------------
  function buildChecklistA4Html(data) {
    const title  = escapeHtml(data.title || 'Checkliste');
    const type   = escapeHtml(data.type || '–');
    const status = escapeHtml(data.status || '–');
    const desc   = escapeHtml(data.description || '');

    const items = (data.items || [])
      .slice()
      .sort((a,b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));

    const pages = chunkIntoPages(items, 12);

    return pages.map((pageItems, pageIndex) => {
      const itemsHtml = pageItems.map(it => {
        const label = escapeHtml(it.label || '');
        const fname = escapeHtml(it.field_name || '');
        const ftype = escapeHtml(it.field_type || 'text');
        const required = it.is_required ? 'Pflicht' : 'Optional';

        return `
          <div class="mcl-pdf-item">
            <div class="mcl-pdf-item-label">${label}</div>
            <div class="mcl-pdf-item-meta">Typ: ${ftype} · Feld: ${fname} · ${required}</div>
            ${
              (ftype === 'checkbox' || ftype === 'radio')
                ? `<div class="mcl-pdf-line"><div class="mcl-pdf-box"></div><div class="mcl-pdf-input"></div></div>`
                : `<div class="mcl-pdf-line"><div class="mcl-pdf-input"></div></div>`
            }
          </div>
        `;
      }).join('');

      return `
        <div class="mcl-a4">
          <div class="mcl-pdf-banner">
            <div class="mcl-pdf-banner-left">
              <div class="mcl-pdf-badge">✓</div>
              <div class="mcl-pdf-banner-titlewrap">
                <p class="mcl-pdf-banner-kicker">CHECKLISTS</p>
                <h1 class="mcl-pdf-banner-title">${title}</h1>
              </div>
            </div>
            <div class="mcl-pdf-banner-right">
              <div><b>Typ:</b> ${type}</div>
              <div><b>Status:</b> ${status}</div>
              <div><b>Seite:</b> ${pageIndex + 1} / ${pages.length}</div>
            </div>
          </div>

          ${desc ? `<p class="mcl-pdf-sub" style="margin:-4mm 0 7mm 0;">${desc}</p>` : ''}

          <div class="mcl-pdf-section-title">Checklisten-Felder</div>
          ${itemsHtml || '<div style="font-size:12px;color:#6b7280;">Keine Felder definiert.</div>'}

          <div class="mcl-pdf-footer">
            Hinweise: Vor Inbetriebnahme vollständig prüfen · Dokumentation/Signaturen separat im Auftrag.
          </div>
        </div>
      `;
    }).join('');
  }

  function openA4Modal(data) {
    if (!pdfBackdrop) return Swal.fire('Fehler', 'A4 Modal nicht gefunden (mcl-pdf-backdrop).', 'error');
    if (!a4Viewer)    return Swal.fire('Fehler', 'A4 Viewer fehlt: #mcl-a4-viewer', 'error');

    const title = data?.title || 'Checkliste';

    pdfTitleEl.textContent = 'A4 Vorschau · ' + title;

    // Render A4 pages (HTML)
    a4Viewer.innerHTML = buildChecklistA4Html(data);

    pdfBackdrop.classList.add('active');
    lockBody();
  }

  function closeA4Modal() {
    if (!pdfBackdrop) return;
    pdfBackdrop.classList.remove('active');
    unlockBody();
    if (a4Viewer) a4Viewer.innerHTML = '';
  }

  // -----------------------
  // Add item + submit
  // -----------------------
  function setupAddItemButton() {
    const btnAdd = document.getElementById('mcl-btn-add-item');
    if (!btnAdd) return;
    btnAdd.addEventListener('click', () => addItemRow());
  }

  function setupFormSubmit() {
    form.addEventListener('submit', () => updateItemsJson());
  }

  // -----------------------
  // Post actions (archive/delete/etc)
  // -----------------------
  function postAction(url, method) {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = url;

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = routes.csrf;
    f.appendChild(csrf);

    const m = document.createElement('input');
    m.type = 'hidden';
    m.name = '_method';
    m.value = method;
    f.appendChild(m);

    document.body.appendChild(f);
    f.submit();
  }

  // -----------------------
  // Bulk selection UI
  // -----------------------
  function syncCheckboxesForId(id, checked, source) {
    document.querySelectorAll('.mcl-select-checkbox').forEach(cb => {
      if (cb.value !== String(id)) return;
      if (cb === source) return;
      cb.checked = checked;
    });
  }

  function updateBulkUI() {
    const count = selectedIds.size;

    if (count > 0) {
      bulkBar.classList.remove('mcl-hidden');
      bulkCountEl.textContent = count + ' ausgewählt';
    } else {
      bulkBar.classList.add('mcl-hidden');
      bulkCountEl.textContent = '0 ausgewählt';
    }

    const selectedSet = new Set(selectedIds);

    document.querySelectorAll('#mcl-card-grid .mcl-card').forEach(card => {
      const id = card.getAttribute('data-id');
      card.classList.toggle('mcl-selected', !!(id && selectedSet.has(id)));
    });

    document.querySelectorAll('#mcl-table tbody tr').forEach(row => {
      const id = row.getAttribute('data-id');
      row.classList.toggle('mcl-selected', !!(id && selectedSet.has(id)));
    });

    if (selectAllList) {
      const rowCbs = Array.from(document.querySelectorAll('#mcl-view-list tbody .mcl-select-checkbox'));
      if (!rowCbs.length) {
        selectAllList.checked = false;
        selectAllList.indeterminate = false;
      } else {
        const checkedCount = rowCbs.filter(cb => cb.checked).length;
        selectAllList.checked = checkedCount === rowCbs.length && checkedCount > 0;
        selectAllList.indeterminate = checkedCount > 0 && checkedCount < rowCbs.length;
      }
    }
  }

  function setupCheckboxes() {
    document.addEventListener('change', function (e) {
      const cb = e.target.closest('.mcl-select-checkbox');
      if (!cb) return;

      const id = cb.value;
      if (!id) return;

      if (cb.checked) selectedIds.add(id);
      else selectedIds.delete(id);

      syncCheckboxesForId(id, cb.checked, cb);
      updateBulkUI();
    });

    if (selectAllList) {
      selectAllList.addEventListener('change', function () {
        const checked = this.checked;
        const rowCheckboxes = document.querySelectorAll('#mcl-view-list tbody .mcl-select-checkbox');

        rowCheckboxes.forEach(cb => {
          const id = cb.value;
          cb.checked = checked;
          if (checked) selectedIds.add(id);
          else selectedIds.delete(id);
          syncCheckboxesForId(id, checked, cb);
        });

        updateBulkUI();
      });
    }
  }

  function postBulkAction(action) {
    if (!selectedIds.size) {
      Swal.fire('Hinweis', 'Bitte zuerst mindestens eine Checkliste auswählen.', 'info');
      return;
    }

    const f = document.createElement('form');
    f.method = 'POST';
    f.action = routes.bulk;

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = routes.csrf;
    f.appendChild(csrf);

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    f.appendChild(actionInput);

    selectedIds.forEach(id => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'ids[]';
      input.value = id;
      f.appendChild(input);
    });

    document.body.appendChild(f);
    f.submit();
  }

  function confirmBulk(action) {
    const messages = {
      archive: { title: 'Ausgewählte Checklisten archivieren?', text: 'Die ausgewählten Checklisten bleiben lesbar.', icon: 'warning' },
      unarchive: { title: 'Archivierung aufheben?', text: 'Die ausgewählten Checklisten werden wieder als „Entwurf“ geführt.', icon: 'question' },
      trash: { title: 'In den Papierkorb verschieben?', text: 'Nicht endgültig löschen. Später wiederherstellbar.', icon: 'warning' },
      restore: { title: 'Aus Papierkorb wiederherstellen?', text: 'Die ausgewählten Checklisten werden zurückgeholt.', icon: 'question' },
      status_active: { title: 'Status auf „Aktiv“ setzen?', text: 'Alle ausgewählten Checklisten werden aktiv.', icon: 'question' },
      status_draft: { title: 'Status auf „Entwurf“ setzen?', text: 'Alle ausgewählten Checklisten werden Entwurf.', icon: 'question' },
      status_archived: { title: 'Status auf „Archiviert“ setzen?', text: 'Alle ausgewählten Checklisten werden archiviert.', icon: 'question' },
    };

    const cfg = messages[action] || { title: 'Aktion ausführen?', text: 'Die ausgewählten Checklisten werden aktualisiert.', icon: 'question' };

    if (!selectedIds.size) {
      Swal.fire('Hinweis', 'Bitte zuerst mindestens eine Checkliste auswählen.', 'info');
      return;
    }

    Swal.fire({
      title: cfg.title,
      text: cfg.text,
      icon: cfg.icon,
      showCancelButton: true,
      confirmButtonText: 'Ja, ausführen',
      cancelButtonText: 'Abbrechen'
    }).then(res => {
      if (res.isConfirmed) postBulkAction(action);
    });
  }

  function setupBulkActions() {
    if (!bulkBar) return;
    bulkBar.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-bulk-action]');
      if (!btn) return;
      const action = btn.getAttribute('data-bulk-action');
      if (!action) return;
      confirmBulk(action);
    });
  }

  // -----------------------
  // Confirm single actions
  // -----------------------
  function confirmArchive(id) {
    Swal.fire({
      title: 'Checkliste archivieren?',
      text: 'Die Checkliste wird als „archiviert“ markiert und bleibt lesbar.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, archivieren',
      cancelButtonText: 'Abbrechen'
    }).then((result) => {
      if (result.isConfirmed) postAction(routes.archive.replace('__ID__', id), 'PATCH');
    });
  }

  function confirmUnarchive(id) {
    Swal.fire({
      title: 'Archiv aufheben?',
      text: 'Die Checkliste wird wieder als „Entwurf“ geführt.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ja, wieder aktivieren',
      cancelButtonText: 'Abbrechen'
    }).then((result) => {
      if (result.isConfirmed) postAction(routes.unarchive.replace('__ID__', id), 'PATCH');
    });
  }

  function confirmDelete(id) {
    Swal.fire({
      title: 'In den Papierkorb verschieben?',
      text: 'Die Checkliste wird nicht endgültig gelöscht und kann später wiederhergestellt werden.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, in den Papierkorb',
      cancelButtonText: 'Abbrechen'
    }).then((result) => {
      if (result.isConfirmed) postAction(routes.destroy.replace('__ID__', id), 'DELETE');
    });
  }

  function confirmRestore(id) {
    Swal.fire({
      title: 'Checkliste wiederherstellen?',
      text: 'Die Checkliste wird aus dem Papierkorb zurückgeholt.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ja, wiederherstellen',
      cancelButtonText: 'Abbrechen'
    }).then((result) => {
      if (result.isConfirmed) postAction(routes.restore.replace('__ID__', id), 'PATCH');
    });
  }

  // -----------------------
  // Drawer preview builder
  // -----------------------
  function buildPreviewControlHtml(type, options, index) {
    const safeIndex = index ?? 0;
    const optHtml = (options || []).map((opt, i) => {
      const escaped = String(opt).replace(/"/g, '&quot;');
      return { escaped, id: `mcl_preview_${type}_${safeIndex}_${i}` };
    });

    switch (type) {
      case 'textarea':
        return `<textarea class="mcl-control-textarea" placeholder="Text eingeben…"></textarea>`;
      case 'select':
        return `<select class="mcl-control">${
          optHtml.length
            ? optHtml.map(o => `<option value="${o.escaped}">${o.escaped}</option>`).join('')
            : '<option>Bitte wählen…</option>'
        }</select>`;
      case 'checkbox':
        return `<div class="mcl-preview-option-group">${
          optHtml.length
            ? optHtml.map(o => `
              <label class="mcl-preview-option" for="${o.id}">
                <input type="checkbox" id="${o.id}"><span>${o.escaped}</span>
              </label>
            `).join('')
            : `<label class="mcl-preview-option"><input type="checkbox"><span>Option</span></label>`
        }</div>`;
      case 'radio':
        return `<div class="mcl-preview-option-group">${
          optHtml.length
            ? optHtml.map(o => `
              <label class="mcl-preview-option" for="${o.id}">
                <input type="radio" name="mcl_preview_radio_${safeIndex}" id="${o.id}">
                <span>${o.escaped}</span>
              </label>
            `).join('')
            : `<label class="mcl-preview-option"><input type="radio" name="mcl_preview_radio_${safeIndex}"><span>Option</span></label>`
        }</div>`;
      case 'file_image':
        return `<input type="file" class="mcl-control-file" accept="image/*">`;
      case 'file_document':
        return `<input type="file" class="mcl-control-file">`;
      case 'text':
      default:
        return `<input type="text" class="mcl-control" placeholder="Text eingeben…">`;
    }
  }

  function buildPreviewItemHtml(item, index) {
    const label       = item.label || ('Feld ' + (index + 1));
    const fieldName   = item.field_name || '';
    const type        = item.field_type || 'text';
    const options     = item.options || [];
    const requiredStr = item.is_required ? 'Pflichtfeld' : 'Optional';

    return `
      <div class="mcl-preview-item" data-index="${index}">
        <div class="mcl-item-handle" title="Ziehen zum Sortieren">⋮⋮</div>
        <div class="mcl-preview-item-main">
          <div class="mcl-preview-item-header">
            <div>
              <div class="mcl-preview-item-title">${escapeHtml(label)}</div>
              <div class="mcl-preview-item-meta">
                Typ: ${escapeHtml(type)} · <code>${escapeHtml(fieldName)}</code> · ${requiredStr}
              </div>
            </div>
          </div>
          <div class="mcl-preview-item-control">
            ${buildPreviewControlHtml(type, options, index)}
          </div>
        </div>
      </div>
    `;
  }

  function openPreviewDrawer(data) {
    drawerTitle.textContent = data.title || 'Checkliste';

    const brands = (data.brand_ids || []).map(id => window.MCL_BRANDS?.[id] || ('#' + id));
    const dists  = (data.distributor_ids || []).map(id => window.MCL_DISTRIBUTORS?.[id] || ('#' + id));

    const statusMap = { draft: 'Entwurf', active: 'Aktiv', archived: 'Archiviert' };

    const itemsHtml = (data.items && data.items.length)
      ? data.items
          .slice()
          .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
          .map((item, idx) => buildPreviewItemHtml(item, idx))
          .join('')
      : '<p>Keine Felder definiert.</p>';

    drawerBody.innerHTML = `
      <div class="mcl-preview-meta">
        <p><strong>Typ:</strong> ${escapeHtml(data.type || '–')}</p>
        <p><strong>Status:</strong> ${escapeHtml(statusMap[data.status] || data.status || '–')}</p>
        <p><strong>Marken:</strong>
          ${brands.length ? brands.map(b => `<span class="mcl-preview-pill">${escapeHtml(b)}</span>`).join('') : '–'}
        </p>
        <p><strong>Vertriebspartner:</strong>
          ${dists.length ? dists.map(d => `<span class="mcl-preview-pill">${escapeHtml(d)}</span>`).join('') : '–'}
        </p>
        <p style="margin-top:.4rem;"><strong>Beschreibung:</strong><br>
          ${data.description ? escapeHtml(data.description) : '–'}
        </p>
      </div>
      <div class="mcl-preview-items">
        <div class="mcl-preview-items-header">
          <h4>Felder</h4>
          <span class="mcl-preview-items-hint">Per Drag & Drop sortieren (nur Vorschau)</span>
        </div>
        <div class="mcl-preview-items-list" id="mcl-preview-items-list">
          ${itemsHtml}
        </div>
      </div>
    `;

    const previewList = document.getElementById('mcl-preview-items-list');
    if (previewList && typeof Sortable !== 'undefined') {
      Sortable.create(previewList, { handle: '.mcl-item-handle', animation: 150 });
    }

    drawerBackdrop.classList.add('active');
    lockBody();
  }

  function closePreviewDrawer() {
    drawerBackdrop.classList.remove('active');
    unlockBody();
  }

  // -----------------------
  // Modal buttons + Help
  // -----------------------
  function setupModalButtons() {
    modalClose.addEventListener('click', closeModal);
    modalCancel.addEventListener('click', function (e) { e.preventDefault(); closeModal(); });

    modalBackdrop.addEventListener('click', function (e) {
      if (e.target === modalBackdrop) closeModal();
    });

    helpBtn.addEventListener('click', function () {
      Swal.fire({
        title: 'Hilfe zur Checkliste',
        html: `
          <p><b>Feldbeschriftung</b> – Text, den der Monteur im Formular sieht (z.B. „Zustand der Pumpe“).</p>
          <p><b>Technischer Feldname</b> – wird automatisch generiert (z.B. <code>zustand_der_pumpe</code>).</p>
          <p><b>Feldtyp</b> – Text, Auswahl, Checkbox, Radio oder Upload-Feld.</p>
          <p><b>Optionen</b> – relevant für Auswahl/Radio/Checkbox (z.B. „OK, Mangel, Nicht geprüft“).</p>
          <p><b>Pflichtfeld</b> – wenn aktiv, muss das Feld ausgefüllt werden.</p>
        `,
        icon: 'info',
        confirmButtonText: 'Verstanden'
      });
    });
  }

  // -----------------------
  // Drawer + A4 Modal close wiring
  // -----------------------
  function setupDrawerAndA4Modal() {
    // Drawer
    drawerClose.addEventListener('click', closePreviewDrawer);
    drawerBackdrop.addEventListener('click', function (e) {
      if (e.target === drawerBackdrop) closePreviewDrawer();
    });

    // A4 modal
    if (pdfCloseBtn) pdfCloseBtn.addEventListener('click', closeA4Modal);
    if (pdfBackdrop) {
      pdfBackdrop.addEventListener('click', function (e) {
        if (e.target === pdfBackdrop) closeA4Modal();
      });
    }

    // ESC closes topmost modal
    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      if (pdfBackdrop?.classList.contains('active')) return closeA4Modal();
      if (drawerBackdrop?.classList.contains('active')) return closePreviewDrawer();
      if (modalBackdrop?.classList.contains('active')) return closeModal();
    });
  }

  // -----------------------
  // Init
  // -----------------------
  function init() {
    initSelect2();
    setupViewToggle();
    setupSearchSort();
    setupCreateButton();
    setupModalButtons();
    setupMenus();
    setupAddItemButton();
    setupFormSubmit();
    setupDrawerAndA4Modal();
    setupCheckboxes();
    setupBulkActions();
  }

  return {
    init,
    confirmArchive,
    confirmUnarchive,
    confirmDelete,
    confirmRestore,
  };
})();

document.addEventListener('DOMContentLoaded', function () {
  MaintenanceChecklistUI.init();
});
</script>

@endsection
