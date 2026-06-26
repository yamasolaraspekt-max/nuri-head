@extends('admin.layouts.app')

@section('title', 'Lieferanten')

@once
@push('style')
<style>
    :root {
        --app-bg: #f3f4f6;
        --card-bg: #ffffff;
        --card-soft: #fafafa;
        --text-main: #111827;
        --text-muted: #6b7280;
        --border: #e5e7eb;

        --primary: #93c21c;
        --primary-hover: #7baa18;
        --primary-light: #f4fae7;

        --blue: #74b2d4;
        --blue-light: #eff6ff;

        --success: #10b981;
        --success-hover: #0d9668;
        --success-light: #ecfdf5;

        --warning: #f59e0b;
        --warning-light: #fffbeb;
        --warning-dark: #b45309;

        --danger: #ef4444;
        --danger-hover: #dc2626;
        --danger-light: #fef2f2;

        --gray: #6b7280;
        --gray-light: #f3f4f6;

        --purple: #7c3aed;
        --purple-light: #f5f3ff;

        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
        --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
        --radius: 14px;
        --transition: all .2s ease-in-out;
    }

    .oc-wrap {
        font-family: Inter, system-ui, -apple-system, sans-serif;
        color: var(--text-main);
    }

    .oc-header {
        margin: 0 18px;
    }

    .oc-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .oc-title {
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -.025em;
        color: #111827;
    }

    .oc-sub {
        font-size: 14px;
        color: var(--text-muted);
        margin-top: 4px;
        line-height: 1.5;
    }

    .oc-btn,
    .oc-btn-soft,
    .oc-btn-ic {
        transition: var(--transition);
        text-decoration: none;
    }

    .oc-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow-sm);
        white-space: nowrap;
    }

    .oc-btn:hover {
        background: var(--primary-hover);
        color: #fff;
        text-decoration: none;
    }

    .oc-btn.success {
        background: var(--success);
    }

    .oc-btn.success:hover {
        background: var(--success-hover);
    }

    .oc-btn.danger {
        background: var(--danger);
    }

    .oc-btn.danger:hover {
        background: var(--danger-hover);
    }

    .oc-btn-soft {
        background: #fff;
        color: var(--text-main);
        border: 1px solid var(--border);
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: var(--shadow-sm);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .oc-btn-soft:hover {
        background: #f9fafb;
        color: var(--text-main);
        text-decoration: none;
    }

    .oc-btn-ic {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        cursor: pointer;
        box-shadow: var(--shadow-sm);
        font-weight: 900;
        font-size: 13px;
    }

    .oc-btn-ic:hover {
        background: #f9fafb;
        color: var(--text-main);
        border-color: #d1d5db;
        text-decoration: none;
    }

    .oc-btn-ic.primary {
        color: var(--primary);
        border-color: var(--primary-light);
        background: var(--primary-light);
    }

    .oc-btn-ic.blue {
        color: #1d4ed8;
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .oc-btn-ic.purple {
        color: #6d28d9;
        border-color: #ddd6fe;
        background: #f5f3ff;
    }

    .oc-btn-ic.warning {
        color: #d97706;
        border-color: #fde7b0;
        background: #fffbeb;
    }

    .oc-btn-ic.success {
        color: var(--success);
        border-color: #c7f2df;
        background: var(--success-light);
    }

    .oc-btn-ic.danger {
        color: var(--danger);
        border-color: rgba(239, 68, 68, .18);
        background: var(--danger-light);
    }

    .oc-analytics {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    @media(max-width: 1300px) {
        .oc-analytics {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media(max-width: 800px) {
        .oc-analytics {
            grid-template-columns: 1fr;
        }
    }

    .oc-stat {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 16px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 92px;
    }

    .oc-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-weight: 900;
    }

    .oc-stat-icon.total { background: var(--blue-light); color: var(--blue); }
    .oc-stat-icon.active { background: var(--success-light); color: var(--success); }
    .oc-stat-icon.unpublished { background: var(--warning-light); color: #d97706; }
    .oc-stat-icon.contact { background: var(--gray-light); color: var(--gray); }
    .oc-stat-icon.connector { background: var(--purple-light); color: var(--purple); }

    .oc-stat-label {
        font-size: 11px;
        font-weight: 900;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .oc-stat-value {
        font-size: 24px;
        font-weight: 950;
        color: #111827;
        line-height: 1.1;
        margin-top: 4px;
    }

    .oc-stat-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .oc-toolbar {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 14px 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 16px;
        box-shadow: var(--shadow-sm);
    }

    .oc-toolbar-left,
    .oc-toolbar-right {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .oc-toolbar-left {
        flex: 1;
    }

    .oc-filter-block {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 170px;
    }

    .oc-filter-block.search {
        flex: 1;
        min-width: 260px;
    }

    .oc-filter-label {
        font-size: 11px;
        font-weight: 900;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .oc-input {
        background: #f9fafb;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 12px 10px 36px;
        font-size: 14px;
        outline: none;
        min-width: 240px;
        width: 100%;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 10px center;
        background-size: 16px;
    }

    .oc-input:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .oc-select {
        padding: 10px 34px 10px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background-color: #fff;
        font-size: 13px;
        cursor: pointer;
        outline: none;
        appearance: none;
        min-height: 42px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px;
    }

    .oc-list-head {
        display: grid;
        grid-template-columns: 90px 80px minmax(320px, 1.5fr) minmax(260px, 1.1fr) minmax(220px, 1fr) 140px 230px;
        gap: 14px;
        align-items: center;
        padding: 0 16px 10px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    @media(max-width: 1180px) {
        .oc-list-head {
            display: none;
        }
    }

    .oc-head-link {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 900;
    }

    .oc-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .oc-item {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .oc-item:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow);
    }

    .oc-item-header {
        padding: 16px;
        display: grid;
        gap: 16px;
        align-items: center;
        grid-template-columns: 90px 80px minmax(320px, 1.5fr) minmax(260px, 1.1fr) minmax(220px, 1fr) 140px 230px;
    }

    @media(max-width: 1180px) {
        .oc-item-header {
            grid-template-columns: 70px 1fr;
            gap: 12px;
        }

        .oc-responsive-col,
        .oc-actions-wrap {
            grid-column: 2;
        }

        .oc-actions-wrap {
            justify-self: start;
        }
    }

    .oc-cell {
        min-width: 0;
    }

    .oc-cell-title {
        font-size: 11px;
        font-weight: 900;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 4px;
        display: none;
    }

    @media(max-width: 1180px) {
        .oc-cell-title {
            display: block;
        }
    }

    .oc-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 54px;
        height: 36px;
        padding: 0 12px;
        border-radius: 10px;
        background: var(--blue-light);
        color: var(--blue);
        font-size: 13px;
        font-weight: 900;
    }

    .oc-logo {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #fff;
        object-fit: contain;
        padding: 6px;
        box-shadow: var(--shadow-sm);
    }

    .oc-main {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .oc-ttl {
        font-weight: 900;
        font-size: 15px;
        margin-bottom: 4px;
        color: #111827;
    }

    .oc-subt {
        font-size: 13px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .oc-mini {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
        line-height: 1.45;
    }

    .oc-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .oc-status-pill.gray { background: #f3f4f6; color: #4b5563; }
    .oc-status-pill.green { background: #ecfdf5; color: #047857; }
    .oc-status-pill.orange { background: #fffbeb; color: #b45309; }
    .oc-status-pill.red { background: #fef2f2; color: #b91c1c; }

    .oc-actions-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .oc-inline-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .oc-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        background: #f3f4f6;
        color: #4b5563;
        gap: 4px;
    }

    .oc-chip.blue { background: #eff6ff; color: #1d4ed8; }
    .oc-chip.green { background: #ecfdf5; color: #047857; }
    .oc-chip.orange { background: #fffbeb; color: #b45309; }
    .oc-chip.purple { background: #f5f3ff; color: #6d28d9; }

    .oc-connector-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 9px;
    }

    .oc-connector-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #4b5563;
        text-decoration: none;
        max-width: 100%;
    }

    .oc-connector-badge:hover {
        text-decoration: none;
        color: #111827;
        background: #fff;
        border-color: #d1d5db;
    }

    .oc-connector-badge.ids {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .oc-connector-badge.oci {
        background: #ecfdf5;
        color: #047857;
        border-color: #bbf7d0;
    }

    .oc-connector-badge.api {
        background: #f5f3ff;
        color: #6d28d9;
        border-color: #ddd6fe;
    }

    .oc-connector-badge.csv,
    .oc-connector-badge.xml,
    .oc-connector-badge.bmecat,
    .oc-connector-badge.datanorm {
        background: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }

    .oc-connector-badge.inactive {
        opacity: .65;
        filter: grayscale(.15);
    }

    .oc-connector-add {
        background: #f4fae7;
        color: #5f7f10;
        border-color: #d9efad;
    }

    .oc-connector-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
        display: inline-flex;
        opacity: .9;
    }

    .oc-empty {
        text-align: center;
        padding: 60px;
        color: var(--text-muted);
        background: #fff;
        border: 1px dashed var(--border);
        border-radius: 16px;
    }

    .oc-pagination {
        margin-top: 18px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: var(--shadow-sm);
    }

    .oc-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1200;
        background: rgba(17, 24, 39, .55);
        backdrop-filter: blur(3px);
        opacity: 0;
        pointer-events: none;
        transition: opacity .22s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .oc-modal-backdrop.open {
        opacity: 1;
        pointer-events: auto;
    }

    .oc-modal {
        width: 100%;
        max-width: 760px;
        background: #fff;
        border: 1px solid rgba(229, 231, 235, .9);
        border-radius: 16px;
        box-shadow: var(--shadow);
        transform: translateY(12px) scale(.985);
        transition: transform .22s ease;
        overflow: hidden;
    }

    .oc-modal-backdrop.open .oc-modal {
        transform: translateY(0) scale(1);
    }

    .oc-modal-h {
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        border-bottom: 1px solid var(--border);
        background: #fafafa;
    }

    .oc-modal-ttl {
        font-weight: 950;
        font-size: 16px;
        line-height: 1.2;
        margin: 0;
        color: #111827;
    }

    .oc-modal-b {
        padding: 20px 18px;
        max-height: 72vh;
        overflow-y: auto;
    }

    .oc-modal-f {
        padding: 14px 18px;
        border-top: 1px solid var(--border);
        background: #fafafa;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .oc-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    @media(max-width: 700px) {
        .oc-form-grid {
            grid-template-columns: 1fr;
        }
    }

    .oc-form-group {
        margin-bottom: 16px;
    }

    .oc-label {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 6px;
    }

    .oc-input-form {
        width: 100%;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #fff;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
    }

    .oc-input-form:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .oc-help {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
    }

    .oc-thumb-preview {
        margin-top: 10px;
        width: 72px;
        height: 72px;
        border-radius: 12px;
        border: 1px solid var(--border);
        object-fit: contain;
        padding: 6px;
        background: #fff;
    }

    .oc-toast-wrap {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .oc-toast {
        pointer-events: auto;
        min-width: 280px;
        max-width: 380px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        padding: 12px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        animation: ocToastIn .3s cubic-bezier(.175, .885, .32, 1.275) forwards;
    }

    @keyframes ocToastIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .oc-toast-ic {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-weight: 950;
    }

    .oc-toast-ic.ok { background: var(--success-light); color: var(--success); }
    .oc-toast-ic.warn { background: var(--warning-light); color: var(--warning); }
    .oc-toast-ic.bad { background: var(--danger-light); color: var(--danger); }

    .oc-toast-ttl {
        font-weight: 900;
        font-size: 13px;
        margin: 0;
        color: #111827;
    }

    .oc-toast-msg {
        font-size: 12px;
        color: #374151;
        margin: 4px 0 0 0;
        line-height: 1.4;
    }

    .oc-toast-x {
        margin-left: auto;
        background: transparent;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
    }

    .oc-image-large {
        width: 100%;
        max-height: 70vh;
        object-fit: contain;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: #fff;
        padding: 10px;
    }

    /*
    |--------------------------------------------------------------------------
    | Distributor products modal
    |--------------------------------------------------------------------------
    */

    .oc-products-modal {
        max-width: 1220px;
    }

    .oc-products-modal .oc-modal-b {
        padding: 0;
        max-height: 82vh;
    }

    .dp-toolbar {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
        justify-content: space-between;
        background: #fafafa;
    }

    .dp-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        padding: 16px;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .dp-stat {
        border: 1px solid var(--border);
        border-radius: 14px;
        background: white;
        padding: 12px;
    }

    .dp-stat-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .dp-stat-value {
        margin-top: 5px;
        font-size: 18px;
        font-weight: 950;
        color: #111827;
    }

    .dp-list {
        max-height: 58vh;
        overflow: auto;
        padding: 14px 16px 18px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        background: #f8fafc;
    }

    .dp-card {
        border: 1px solid var(--border);
        border-radius: 16px;
        background: white;
        padding: 12px;
        display: grid;
        grid-template-columns: 78px minmax(0, 1fr);
        gap: 12px;
        box-shadow: var(--shadow-sm);
    }

    .dp-img {
        width: 78px;
        height: 78px;
        object-fit: contain;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #fff;
        padding: 5px;
        cursor: pointer;
    }

    .dp-img-empty {
        width: 78px;
        height: 78px;
        border-radius: 14px;
        border: 1px dashed var(--border);
        background: #f9fafb;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 950;
    }

    .dp-title {
        color: #111827;
        font-size: 14px;
        font-weight: 950;
        margin-bottom: 4px;
        line-height: 1.35;
    }

    .dp-meta {
        color: var(--text-muted);
        font-size: 12px;
        line-height: 1.45;
        margin-bottom: 8px;
    }

    .dp-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .dp-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 900;
        background: #f3f4f6;
        color: #374151;
        border: 1px solid var(--border);
    }

    .dp-pill.green {
        background: #ecfdf5;
        color: #047857;
        border-color: #bbf7d0;
    }

    .dp-pill.blue {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .dp-pill.orange {
        background: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }

    .dp-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .dp-compare-box {
        display: none;
        padding: 16px;
        border-top: 1px solid var(--border);
        background: #f8fafc;
    }

    .dp-compare-box.active {
        display: block;
    }

    .dp-compare-table-wrap {
        overflow: auto;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #fff;
    }

    .dp-compare-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .dp-compare-table th,
    .dp-compare-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        white-space: nowrap;
    }

    .dp-compare-table th {
        background: #f3f4f6;
        color: #6b7280;
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .dp-best-row td {
        background: #ecfdf5;
        color: #047857;
        font-weight: 900;
    }

    .dp-current-row td {
        outline: 2px solid rgba(116, 178, 212, .35);
        outline-offset: -2px;
    }

    .dp-loader {
        padding: 22px;
        color: #6b7280;
        font-weight: 900;
        text-align: center;
        grid-column: 1 / -1;
    }

    @media(max-width: 900px) {
        .dp-summary,
        .dp-list {
            grid-template-columns: 1fr;
        }

        .dp-card {
            grid-template-columns: 64px minmax(0, 1fr);
        }

        .dp-img,
        .dp-img-empty {
            width: 64px;
            height: 64px;
        }
    }
</style>
@endpush
@endonce

@section('content')
@php
    $items = collect($data->items());

    $totalCount = $data->total();
    $activeCount = $items->filter(fn($item) => in_array(($item->status ?? ''), ['active', 'Published'], true))->count();
    $unpublishedCount = $items->filter(fn($item) => ($item->status ?? '') === 'Unpublished' || empty($item->status))->count();
    $withContactCount = $items->filter(fn($item) => !empty($item->phone) || !empty($item->email) || !empty($item->mobile))->count();
    $withConnectorCount = $items->filter(fn($item) => (int) ($item->supplier_connections_count ?? 0) > 0)->count();

    $currentSortBy = request('sort_by', 'id');
    $currentSortDir = request('sort_dir', 'desc');

    $connectorLabels = [
        'ids' => 'IDS',
        'oci' => 'OCI',
        'api' => 'API',
        'csv' => 'CSV',
        'xml' => 'XML',
        'bmecat' => 'BMEcat',
        'datanorm' => 'DATANORM',
    ];

    $connectorCreateUrl = function ($distributor) {
        return Route::has('admin.supplier-connectors.create')
            ? route('admin.supplier-connectors.create', ['distributor_id' => $distributor->id])
            : '#';
    };

    $connectorEditUrl = function ($connector) {
        return Route::has('admin.supplier-connectors.edit')
            ? route('admin.supplier-connectors.edit', $connector)
            : '#';
    };

    $connectorSearchUrl = function ($connector) {
        return Route::has('admin.supplier-connectors.search')
            ? route('admin.supplier-connectors.search', $connector)
            : '#';
    };

    $sortLink = function ($column) use ($currentSortBy, $currentSortDir) {
        $nextDir = ($currentSortBy === $column && $currentSortDir === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery([
            'sort_by' => $column,
            'sort_dir' => $nextDir,
        ]);
    };

    $sortIcon = function ($column) use ($currentSortBy, $currentSortDir) {
        if ($currentSortBy !== $column) {
            return '↕';
        }

        return $currentSortDir === 'asc' ? '↑' : '↓';
    };
@endphp

<div class="oc-wrap">
    <div class="oc-header">
        <div class="oc-titlebar">
            <div>
                <div class="oc-title">Lieferantenverwaltung</div>
                <div class="oc-sub">
                    Übersicht, Filter, Import, Schnittstellen, Produkte und Preisvergleich aller Lieferanten.
                </div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                @if(Route::has('admin.supplier-connectors.index'))
                    <a class="oc-btn-soft" href="{{ route('admin.supplier-connectors.index') }}">
                        Schnittstellen
                    </a>
                @endif

                <button class="oc-btn success" type="button" onclick="openModal('modalImportCsv')">
                    CSV-Import
                </button>

                <button class="oc-btn" type="button" onclick="openModal('modalCreate')">
                    Neuer Lieferant
                </button>
            </div>
        </div>
    </div>

    <div class="oc-analytics">
        <div class="oc-stat">
            <div class="oc-stat-icon total">#</div>
            <div>
                <div class="oc-stat-label">Gesamt</div>
                <div class="oc-stat-value">{{ $totalCount }}</div>
                <div class="oc-stat-sub">Alle Lieferanten</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon active">✓</div>
            <div>
                <div class="oc-stat-label">Aktiv</div>
                <div class="oc-stat-value">{{ $activeCount }}</div>
                <div class="oc-stat-sub">Auf dieser Seite</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon unpublished">!</div>
            <div>
                <div class="oc-stat-label">Unveröffentlicht</div>
                <div class="oc-stat-value">{{ $unpublishedCount }}</div>
                <div class="oc-stat-sub">Auf dieser Seite</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon contact">@</div>
            <div>
                <div class="oc-stat-label">Mit Kontakt</div>
                <div class="oc-stat-value">{{ $withContactCount }}</div>
                <div class="oc-stat-sub">Telefon, Mobil oder E-Mail</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon connector">↔</div>
            <div>
                <div class="oc-stat-label">Mit Schnittstelle</div>
                <div class="oc-stat-value">{{ $withConnectorCount }}</div>
                <div class="oc-stat-sub">IDS, OCI, API, CSV, XML...</div>
            </div>
        </div>
    </div>

    <form action="{{ route('distributors.index') }}" method="GET" class="oc-toolbar">
        <div class="oc-toolbar-left">
            <div class="oc-filter-block search">
                <label class="oc-filter-label">Suche</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="oc-input"
                    placeholder="Name, Kurzname, Stadt, E-Mail, Schnittstelle ..."
                >
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Status</label>
                <select name="status" class="oc-select">
                    <option value="">Alle</option>
                    <option value="active" @selected(request('status') === 'active')>Aktiv</option>
                    <option value="Published" @selected(request('status') === 'Published')>Veröffentlicht</option>
                    <option value="Unpublished" @selected(request('status') === 'Unpublished')>Unveröffentlicht</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inaktiv</option>
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Schnittstelle</label>
                <select name="connector_type" class="oc-select">
                    <option value="">Alle</option>
                    @foreach($connectorLabels as $type => $label)
                        <option value="{{ $type }}" @selected(request('connector_type') === $type)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Connector-Status</label>
                <select name="connector_status" class="oc-select">
                    <option value="">Alle</option>
                    <option value="with_connector" @selected(request('connector_status') === 'with_connector')>
                        Mit Schnittstelle
                    </option>
                    <option value="without_connector" @selected(request('connector_status') === 'without_connector')>
                        Ohne Schnittstelle
                    </option>
                    <option value="active_connector" @selected(request('connector_status') === 'active_connector')>
                        Aktive Schnittstelle
                    </option>
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Sortieren</label>
                <select name="sort_by" class="oc-select">
                    <option value="id" @selected(request('sort_by', 'id') === 'id')>ID</option>
                    <option value="name" @selected(request('sort_by') === 'name')>Name</option>
                    <option value="city" @selected(request('sort_by') === 'city')>Stadt</option>
                    <option value="status" @selected(request('sort_by') === 'status')>Status</option>
                    <option value="created_at" @selected(request('sort_by') === 'created_at')>Erstellt am</option>
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Richtung</label>
                <select name="sort_dir" class="oc-select">
                    <option value="asc" @selected(request('sort_dir') === 'asc')>Aufsteigend</option>
                    <option value="desc" @selected(request('sort_dir', 'desc') === 'desc')>Absteigend</option>
                </select>
            </div>
        </div>

        <div class="oc-toolbar-right">
            <button class="oc-btn-soft" type="submit">Filtern</button>
            <a href="{{ route('distributors.index') }}" class="oc-btn-soft">Zurücksetzen</a>
        </div>
    </form>

    <div class="oc-list-head">
        <a class="oc-head-link" href="{{ $sortLink('id') }}">
            ID <span>{{ $sortIcon('id') }}</span>
        </a>

        <div>Logo</div>

        <a class="oc-head-link" href="{{ $sortLink('name') }}">
            Lieferant <span>{{ $sortIcon('name') }}</span>
        </a>

        <a class="oc-head-link" href="{{ $sortLink('city') }}">
            Adresse <span>{{ $sortIcon('city') }}</span>
        </a>

        <div>Kontakt / Konditionen</div>

        <a class="oc-head-link" href="{{ $sortLink('status') }}">
            Status <span>{{ $sortIcon('status') }}</span>
        </a>

        <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="oc-list">
        @forelse($data as $item)
            @php
                $status = (string) ($item->status ?? '');
                $isActive = in_array($status, ['active', 'Published'], true);

                $logoSrc = $item->image
                    ? asset('images/distributor/' . $item->image)
                    : asset('images/icons/placeholder.svg');

                $addressParts = array_filter([
                    $item->street ?? null,
                    trim(($item->postal_code ?? '') . ' ' . ($item->city ?? '')) ?: null,
                ]);

                $addressText = count($addressParts)
                    ? implode(', ', $addressParts)
                    : 'Keine Adresse';

                $kontaktText = collect([
                    $item->phone ? 'Tel: ' . $item->phone : null,
                    $item->mobile ? 'Mobil: ' . $item->mobile : null,
                    $item->email ? 'E-Mail: ' . $item->email : null,
                ])->filter()->implode(' • ');

                $connections = $item->relationLoaded('supplierConnections')
                    ? $item->supplierConnections
                    : collect();

                $connectorGroups = $connections->groupBy(fn($connector) => strtolower((string) $connector->connector_type));
            @endphp

            <div class="oc-item">
                <div class="oc-item-header">
                    <div class="oc-cell">
                        <div class="oc-cell-title">ID</div>
                        <span class="oc-id-badge">#{{ $item->id }}</span>
                    </div>

                    <div class="oc-cell">
                        <div class="oc-cell-title">Logo</div>
                        <button
                            type="button"
                            class="oc-btn-ic js-open-image"
                            data-name="{{ $item->name }}"
                            data-logo="{{ $logoSrc }}"
                            title="Logo anzeigen"
                            style="width:auto;height:auto;padding:0;border:none;background:transparent;"
                        >
                            <img src="{{ $logoSrc }}" alt="Logo" class="oc-logo">
                        </button>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Lieferant</div>

                        <div class="oc-main">
                            <div class="oc-ttl">{{ $item->name ?: 'Ohne Namen' }}</div>

                            @if(!empty($item->short_name))
                                <div class="oc-subt">{{ $item->short_name }}</div>
                            @else
                                <div class="oc-subt">Kein Kurzname hinterlegt</div>
                            @endif

                            <div class="oc-inline-stack" style="margin-top:8px;">
                                @if(!empty($item->account_number))
                                    <span class="oc-chip blue">Kontonummer: {{ $item->account_number }}</span>
                                @endif

                                @if(!is_null($item->cash_discount) && $item->cash_discount !== '')
                                    <span class="oc-chip green">
                                        Skonto: {{ number_format((float) $item->cash_discount, 2, ',', '.') }}%
                                    </span>
                                @endif

                                @if(!empty($item->payment_terms))
                                    <span class="oc-chip orange">Zahlungsziel: {{ $item->payment_terms }}</span>
                                @endif

                                @if(isset($item->distributor_prices_count))
                                    <span class="oc-chip blue">Preise: {{ $item->distributor_prices_count }}</span>
                                @endif
                            </div>

                            <div class="oc-connector-row">
                                @forelse($connectorGroups as $type => $connectors)
                                    @php
                                        $firstConnector = $connectors->first();
                                        $activeConnectorCount = $connectors->where('is_active', true)->count();
                                        $totalConnectorCount = $connectors->count();
                                        $label = $connectorLabels[$type] ?? strtoupper($type ?: 'Connector');
                                        $targetUrl = in_array($type, ['ids', 'oci'], true)
                                            ? $connectorSearchUrl($firstConnector)
                                            : $connectorEditUrl($firstConnector);
                                    @endphp

                                    <a href="{{ $targetUrl }}"
                                       class="oc-connector-badge {{ $type }} {{ $activeConnectorCount ? '' : 'inactive' }}"
                                       title="{{ $activeConnectorCount }} aktiv von {{ $totalConnectorCount }}">
                                        <span class="oc-connector-dot"></span>
                                        {{ $label }}
                                        <span>{{ $activeConnectorCount }}/{{ $totalConnectorCount }}</span>
                                    </a>
                                @empty
                                    <span class="oc-connector-badge inactive">
                                        Keine Schnittstelle
                                    </span>
                                @endforelse

                                <a href="{{ $connectorCreateUrl($item) }}"
                                   class="oc-connector-badge oc-connector-add"
                                   title="Neue Schnittstelle für diesen Lieferanten erstellen">
                                    + Schnittstelle
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Adresse</div>

                        <div class="oc-main">
                            <div class="oc-ttl" style="font-size:14px;">{{ $addressText }}</div>

                            @if(!empty($item->city) || !empty($item->postal_code))
                                <div class="oc-subt">
                                    {{ trim(($item->postal_code ?? '') . ' ' . ($item->city ?? '')) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Kontakt / Konditionen</div>

                        <div class="oc-main">
                            <div class="oc-ttl" style="font-size:14px;">
                                {{ $kontaktText ?: 'Keine Kontaktdaten' }}
                            </div>

                            <div class="oc-mini">
                                @if($item->phone)<span>Telefon vorhanden</span>@endif
                                @if($item->phone && ($item->email || $item->mobile)) <span> • </span> @endif
                                @if($item->mobile)<span>Mobil vorhanden</span>@endif
                                @if(($item->phone || $item->mobile) && $item->email) <span> • </span> @endif
                                @if($item->email)<span>E-Mail vorhanden</span>@endif
                            </div>

                            @if(!is_null($item->cash_discount) || !empty($item->payment_terms))
                                <div class="oc-mini">
                                    @if(!is_null($item->cash_discount) && $item->cash_discount !== '')
                                        <span>Skonto: {{ number_format((float) $item->cash_discount, 2, ',', '.') }}%</span>
                                    @endif

                                    @if(!is_null($item->cash_discount) && $item->cash_discount !== '' && !empty($item->payment_terms))
                                        <span> • </span>
                                    @endif

                                    @if(!empty($item->payment_terms))
                                        <span>Zahlungsziel: {{ $item->payment_terms }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Status</div>

                        @if($status === 'inactive')
                            <span class="oc-status-pill red">Inaktiv</span>
                        @elseif($isActive)
                            <span class="oc-status-pill green">Aktiv</span>
                        @elseif($status === 'Unpublished' || empty($status))
                            <span class="oc-status-pill orange">Unveröffentlicht</span>
                        @else
                            <span class="oc-status-pill gray">{{ $status ?: '–' }}</span>
                        @endif
                    </div>

                    <div class="oc-actions-wrap">
                        @if($item->phone)
                            <a class="oc-btn-ic" href="tel:{{ $item->phone }}" title="Anrufen">☎</a>
                        @endif

                        @if($item->email)
                            <a class="oc-btn-ic" href="mailto:{{ $item->email }}" title="E-Mail senden">@</a>
                        @endif

                        <a class="oc-btn-ic primary"
                           href="{{ route('distributors.departments.index', $item->id) }}"
                           title="Abteilungen">
                            D
                        </a>

                        <button
                            type="button"
                            class="oc-btn-ic blue js-open-distributor-products"
                            title="Produkte dieses Lieferanten anzeigen"
                            data-products_url="{{ route('distributors.products', $item) }}"
                            data-name="{{ $item->name ?: $item->short_name }}"
                        >
                            P
                        </button>

                        @if($status === 'Unpublished' || empty($status))
                            <form method="POST" action="{{ route('distributors.publish', $item) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="oc-btn-ic success" title="Veröffentlichen">✓</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('distributors.unpublish', $item) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="oc-btn-ic warning" title="Unveröffentlichen">×</button>
                            </form>
                        @endif

                        <button
                            type="button"
                            class="oc-btn-ic js-open-edit"
                            title="Bearbeiten"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-short_name="{{ $item->short_name }}"
                            data-account_number="{{ $item->account_number }}"
                            data-street="{{ $item->street }}"
                            data-postal_code="{{ $item->postal_code }}"
                            data-city="{{ $item->city }}"
                            data-phone="{{ $item->phone }}"
                            data-mobile="{{ $item->mobile }}"
                            data-email="{{ $item->email }}"
                            data-cash_discount="{{ $item->cash_discount }}"
                            data-payment_terms="{{ $item->payment_terms }}"
                            data-status="{{ $item->status }}"
                            data-image="{{ $item->image ? asset('images/distributor/' . $item->image) : '' }}"
                            data-update_url="{{ route('distributors.update', ['distributor' => $item->id]) }}"
                        >
                            ✎
                        </button>

                        <button
                            type="button"
                            class="oc-btn-ic danger js-open-delete"
                            title="Löschen"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-delete_url="{{ route('distributors.destroy', ['distributor' => $item->id]) }}"
                        >
                            🗑
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="oc-empty">Keine Daten gefunden.</div>
        @endforelse
    </div>

    @if($data->hasPages())
        <div class="oc-pagination">
            {{ $data->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

{{-- Logo Preview --}}
<div class="oc-modal-backdrop" id="globalImageModal">
    <div class="oc-modal" style="max-width:700px;">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl" id="globalImageTitle">Logo-Vorschau</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('globalImageModal')">×</button>
        </div>

        <div class="oc-modal-b text-center">
            <img src="" alt="Logo" class="oc-image-large" id="globalImagePreview">
        </div>
    </div>
</div>

{{-- Distributor Products Modal --}}
<div class="oc-modal-backdrop" id="distributorProductsModal">
    <div class="oc-modal oc-products-modal">
        <div class="oc-modal-h">
            <div>
                <h3 class="oc-modal-ttl" id="dpModalTitle">Produkte des Lieferanten</h3>
                <div style="font-size:12px;color:#6b7280;margin-top:4px;" id="dpModalSub">
                    Produkte werden geladen...
                </div>
            </div>

            <button class="oc-btn-ic" type="button" onclick="closeModal('distributorProductsModal')">×</button>
        </div>

        <div class="oc-modal-b">
            <div class="dp-toolbar">
                <div class="oc-filter-block search" style="min-width:320px;">
                    <label class="oc-filter-label">Produkt suchen</label>
                    <input type="text"
                           id="dpSearchInput"
                           class="oc-input"
                           placeholder="Produkt, EAN, Artikelnummer..."
                           style="min-width:unset;">
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button type="button" class="oc-btn-soft" id="dpReloadBtn">
                        Aktualisieren
                    </button>
                </div>
            </div>

            <div class="dp-summary">
                <div class="dp-stat">
                    <div class="dp-stat-label">Produkte</div>
                    <div class="dp-stat-value" id="dpTotalProducts">0</div>
                </div>

                <div class="dp-stat">
                    <div class="dp-stat-label">Günstigster EK</div>
                    <div class="dp-stat-value" id="dpMinPrice">—</div>
                </div>

                <div class="dp-stat">
                    <div class="dp-stat-label">Höchster EK</div>
                    <div class="dp-stat-value" id="dpMaxPrice">—</div>
                </div>

                <div class="dp-stat">
                    <div class="dp-stat-label">Differenz</div>
                    <div class="dp-stat-value" id="dpDifference">—</div>
                </div>
            </div>

            <div id="dpProductList" class="dp-list">
                <div class="dp-loader">Produkte werden geladen...</div>
            </div>

            <div id="dpCompareBox" class="dp-compare-box">
                <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-weight:950;color:#111827;" id="dpCompareTitle">Preisvergleich</div>
                        <div style="font-size:12px;color:#6b7280;" id="dpCompareSub"></div>
                    </div>

                    <button type="button" class="oc-btn-soft" onclick="hideDistributorCompareBox()">
                        Schließen
                    </button>
                </div>

                <div id="dpCompareSummary" style="margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;"></div>

                <div id="dpCompareTable"></div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="oc-modal-backdrop" id="globalDeleteModal">
    <div class="oc-modal" style="max-width:520px;">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Eintrag löschen</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('globalDeleteModal')">×</button>
        </div>

        <div class="oc-modal-b">
            <p style="margin:0 0 10px;color:#374151;">
                Möchtest du diesen Lieferanten wirklich löschen?
            </p>

            <div class="oc-inline-stack">
                <span class="oc-chip blue">ID: <span id="deleteDistributorId">–</span></span>
                <span class="oc-chip orange">Name: <span id="deleteDistributorName">–</span></span>
            </div>
        </div>

        <div class="oc-modal-f">
            <button type="button" class="oc-btn-soft" onclick="closeModal('globalDeleteModal')">Abbrechen</button>

            <form method="POST" id="globalDeleteForm">
                @csrf
                @method('DELETE')
                <button type="submit" class="oc-btn danger">Löschen</button>
            </form>
        </div>
    </div>
</div>

{{-- CSV Import Modal --}}
<div class="oc-modal-backdrop" id="modalImportCsv">
    <div class="oc-modal" style="max-width:560px;">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">CSV importieren</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('modalImportCsv')">×</button>
        </div>

        <form method="POST" action="{{ route('distributors.importCsv') }}" enctype="multipart/form-data">
            @csrf

            <div class="oc-modal-b">
                <div class="oc-form-group">
                    <label class="oc-label">CSV-Datei</label>
                    <input type="file" name="csv_file" class="oc-input-form" accept=".csv,.txt" required>
                    <div class="oc-help">
                        Unterstützt CSV/TXT mit Komma oder Semikolon. Umlaute werden automatisch erkannt.
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('modalImportCsv')">Abbrechen</button>
                <button type="submit" class="oc-btn success">Importieren</button>
            </div>
        </form>
    </div>
</div>

{{-- Create Modal --}}
<div class="oc-modal-backdrop" id="modalCreate">
    <div class="oc-modal">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Neuer Lieferant</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('modalCreate')">×</button>
        </div>

        <form method="POST" action="{{ route('distributors.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="oc-modal-b">
                @include('admin.product.distributor._form-fields', [
                    'prefix' => '',
                    'distributor' => null,
                ])
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('modalCreate')">Abbrechen</button>
                <button type="submit" class="oc-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="oc-modal-backdrop" id="globalEditModal">
    <div class="oc-modal">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Lieferant bearbeiten</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('globalEditModal')">×</button>
        </div>

        <form method="POST" id="globalEditForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="oc-modal-b">
                <div class="oc-form-grid">
                    <div class="oc-form-group">
                        <label class="oc-label">Name *</label>
                        <input type="text" name="name" id="edit_name" class="oc-input-form" required>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Kurzname</label>
                        <input type="text" name="short_name" id="edit_short_name" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Kontonummer</label>
                        <input type="text" name="account_number" id="edit_account_number" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Status</label>
                        <select name="status" id="edit_status" class="oc-input-form">
                            <option value="Unpublished">Unveröffentlicht</option>
                            <option value="Published">Veröffentlicht</option>
                            <option value="active">Aktiv</option>
                            <option value="inactive">Inaktiv</option>
                        </select>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Straße</label>
                        <input type="text" name="street" id="edit_street" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">PLZ</label>
                        <input type="text" name="postal_code" id="edit_postal_code" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Stadt</label>
                        <input type="text" name="city" id="edit_city" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Telefon</label>
                        <input type="text" name="phone" id="edit_phone" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Mobil</label>
                        <input type="text" name="mobile" id="edit_mobile" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">E-Mail</label>
                        <input type="email" name="email" id="edit_email" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Skonto %</label>
                        <input type="number" step="0.01" min="0" max="100" name="cash_discount" id="edit_cash_discount" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Zahlungsziel</label>
                        <input type="text" name="payment_terms" id="edit_payment_terms" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Logo</label>
                        <input type="file" name="image" class="oc-input-form" accept="image/*">
                        <img src="" alt="Logo" id="edit_logo_preview" class="oc-thumb-preview" style="display:none;">
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('globalEditModal')">Abbrechen</button>
                <button type="submit" class="oc-btn">Aktualisieren</button>
            </div>
        </form>
    </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
@push('scripts')
<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('open');
        }
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('open');
        }
    }

    function toast(kind, title, msg) {
        const wrap = document.getElementById('toast-wrap');
        if (!wrap) return;

        const icons = {
            ok: '✓',
            warn: '!',
            bad: '×'
        };

        const safeTitle = escapeDp(title);
        const safeMsg = escapeDp(msg);

        const el = document.createElement('div');
        el.className = 'oc-toast';
        el.innerHTML = `
            <div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
            <div style="flex:1;">
                <p class="oc-toast-ttl">${safeTitle}</p>
                <p class="oc-toast-msg">${safeMsg}</p>
            </div>
            <button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
        `;

        wrap.appendChild(el);

        setTimeout(() => {
            try {
                el.remove();
            } catch (e) {}
        }, 4000);
    }

    function moneyDp(value) {
        if (value === null || value === undefined || value === '') {
            return '—';
        }

        return Number(value).toFixed(2) + ' €';
    }

    function escapeDp(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    let activeDistributorProductsUrl = null;
    let activeDistributorName = null;
    let dpSearchTimer = null;

    function loadDistributorProducts(search = '') {
        const list = document.getElementById('dpProductList');

        if (!activeDistributorProductsUrl || !list) {
            return;
        }

        list.innerHTML = `<div class="dp-loader">Produkte werden geladen...</div>`;

        const url = new URL(activeDistributorProductsUrl, window.location.origin);

        if (search) {
            url.searchParams.set('search', search);
        }

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                return response.json();
            })
            .then(data => {
                renderDistributorProducts(data);
            })
            .catch(error => {
                console.error(error);

                list.innerHTML = `
                    <div class="oc-empty" style="grid-column:1/-1;">
                        Produkte konnten nicht geladen werden.
                    </div>
                `;

                toast('bad', 'Fehler', 'Produkte konnten nicht geladen werden.');
            });
    }

    function renderDistributorProducts(data) {
        document.getElementById('dpModalTitle').textContent =
            'Produkte von ' + (data.distributor?.name || activeDistributorName || 'Lieferant');

        document.getElementById('dpModalSub').textContent =
            'Alle Produkte, die über distributor_prices mit diesem Lieferanten verbunden sind.';

        document.getElementById('dpTotalProducts').textContent = data.summary?.total_products ?? 0;
        document.getElementById('dpMinPrice').textContent = moneyDp(data.summary?.min_price);
        document.getElementById('dpMaxPrice').textContent = moneyDp(data.summary?.max_price);
        document.getElementById('dpDifference').textContent = moneyDp(data.summary?.difference);

        const products = Array.isArray(data.products) ? data.products : [];
        const list = document.getElementById('dpProductList');

        hideDistributorCompareBox();

        if (!products.length) {
            list.innerHTML = `
                <div class="oc-empty" style="grid-column:1/-1;">
                    Keine Produkte für diesen Lieferanten gefunden.
                </div>
            `;
            return;
        }

        list.innerHTML = products.map(product => {
            return `
                <div class="dp-card">
                    <div>
                        ${product.image_url
                            ? `<img src="${escapeDp(product.image_url)}" class="dp-img js-open-product-image-from-dp" data-image="${escapeDp(product.image_url)}" data-title="${escapeDp(product.product_name)}" alt="">`
                            : `<div class="dp-img-empty">IMG</div>`
                        }
                    </div>

                    <div>
                        <div class="dp-title">${escapeDp(product.product_name)}</div>

                        <div class="dp-meta">
                            ${product.brand ? `Hersteller: ${escapeDp(product.brand)}<br>` : ''}
                            ${product.article_group ? `Gruppe: ${escapeDp(product.article_group)}<br>` : ''}
                            ${product.model ? `Modell: ${escapeDp(product.model)}<br>` : ''}
                            ${product.ean ? `EAN: ${escapeDp(product.ean)}<br>` : ''}
                            Hersteller-Nr.: ${escapeDp(product.manufacturer_article_no || '—')}<br>
                            Lieferanten-Nr.: ${escapeDp(product.supplier_article_no || '—')}
                        </div>

                        <div class="dp-pills">
                            <span class="dp-pill green">EK: ${moneyDp(product.purchase_price)}</span>
                            <span class="dp-pill blue">VK: ${moneyDp(product.price)}</span>
                            <span class="dp-pill orange">Datum: ${escapeDp(product.price_date || '—')}</span>
                            <span class="dp-pill">Verfügbar: ${escapeDp(product.availability || '—')}</span>
                        </div>

                        <div class="dp-actions">
                            ${product.product_url
                                ? `<a href="${escapeDp(product.product_url)}" target="_blank" class="oc-btn-soft">Produkt öffnen</a>`
                                : ''
                            }

                            <button type="button"
                                    class="oc-btn"
                                    onclick="loadDistributorProductDifference(${Number(product.product_id)}, '${escapeDp(product.product_name)}')">
                                Preisvergleich
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function loadDistributorProductDifference(productId, productName) {
        if (!productId || !activeDistributorProductsUrl) {
            return;
        }

        const box = document.getElementById('dpCompareBox');
        const table = document.getElementById('dpCompareTable');

        box.classList.add('active');
        table.innerHTML = `<div class="dp-loader">Preisvergleich wird geladen...</div>`;

        const cleanUrl = activeDistributorProductsUrl.split('?')[0];
        const url = cleanUrl.replace(/\/products$/, '/products/' + productId + '/price-difference');

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                return response.json();
            })
            .then(data => {
                renderDistributorProductDifference(data, productName);
            })
            .catch(error => {
                console.error(error);

                table.innerHTML = `
                    <div class="oc-empty">
                        Preisvergleich konnte nicht geladen werden.
                    </div>
                `;

                toast('bad', 'Fehler', 'Preisvergleich konnte nicht geladen werden.');
            });
    }

    function renderDistributorProductDifference(data, fallbackName) {
        document.getElementById('dpCompareTitle').textContent =
            'Preisvergleich: ' + (data.product?.name || fallbackName || 'Produkt');

        document.getElementById('dpCompareSub').textContent =
            'Vergleich dieses Produkts über alle Lieferanten.';

        document.getElementById('dpCompareSummary').innerHTML = `
            <span class="oc-chip green">Günstigster: ${moneyDp(data.summary?.min_price)}</span>
            <span class="oc-chip orange">Höchster: ${moneyDp(data.summary?.max_price)}</span>
            <span class="oc-chip blue">Differenz: ${moneyDp(data.summary?.difference)}</span>
            <span class="oc-chip purple">Abstand: ${data.summary?.percent_difference ?? '—'}%</span>
        `;

        const prices = Array.isArray(data.prices) ? data.prices : [];

        if (!prices.length) {
            document.getElementById('dpCompareTable').innerHTML = `
                <div class="oc-empty">
                    Keine Vergleichspreise gefunden.
                </div>
            `;
            return;
        }

        document.getElementById('dpCompareTable').innerHTML = `
            <div class="dp-compare-table-wrap">
                <table class="dp-compare-table">
                    <thead>
                        <tr>
                            <th>Lieferant</th>
                            <th>Lieferanten-Nr.</th>
                            <th>EK</th>
                            <th>VK</th>
                            <th>Rabatt</th>
                            <th>Verfügbarkeit</th>
                            <th>Preisdatum</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${prices.map(row => `
                            <tr class="${row.is_best ? 'dp-best-row' : ''} ${row.is_current_distributor ? 'dp-current-row' : ''}">
                                <td>
                                    ${escapeDp(row.distributor_name)}
                                    ${row.is_current_distributor ? '<span class="oc-chip blue" style="margin-left:6px;">Aktuell</span>' : ''}
                                    ${row.is_best ? '<span class="oc-chip green" style="margin-left:6px;">Bestpreis</span>' : ''}
                                </td>
                                <td>${escapeDp(row.supplier_article_no || '—')}</td>
                                <td>${moneyDp(row.purchase_price)}</td>
                                <td>${moneyDp(row.price)}</td>
                                <td>${row.discount_percent !== null && row.discount_percent !== undefined ? escapeDp(row.discount_percent) + '%' : '—'}</td>
                                <td>${escapeDp(row.availability || '—')}</td>
                                <td>${escapeDp(row.price_date || '—')}</td>
                                <td>${escapeDp(row.status || '—')}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    function hideDistributorCompareBox() {
        document.getElementById('dpCompareBox')?.classList.remove('active');
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('oc-modal-backdrop')) {
            e.target.classList.remove('open');
            return;
        }

        const imageBtn = e.target.closest('.js-open-image');

        if (imageBtn) {
            document.getElementById('globalImageTitle').textContent = 'Logo-Vorschau — ' + (imageBtn.dataset.name || '');
            document.getElementById('globalImagePreview').src = imageBtn.dataset.logo || '';
            openModal('globalImageModal');
            return;
        }

        const productImageBtn = e.target.closest('.js-open-product-image-from-dp');

        if (productImageBtn) {
            document.getElementById('globalImageTitle').textContent = 'Produktbild — ' + (productImageBtn.dataset.title || '');
            document.getElementById('globalImagePreview').src = productImageBtn.dataset.image || '';
            openModal('globalImageModal');
            return;
        }

        const distributorProductsBtn = e.target.closest('.js-open-distributor-products');

        if (distributorProductsBtn) {
            activeDistributorProductsUrl = distributorProductsBtn.dataset.products_url;
            activeDistributorName = distributorProductsBtn.dataset.name || 'Lieferant';

            document.getElementById('dpSearchInput').value = '';
            hideDistributorCompareBox();

            openModal('distributorProductsModal');
            loadDistributorProducts();
            return;
        }

        const deleteBtn = e.target.closest('.js-open-delete');

        if (deleteBtn) {
            document.getElementById('deleteDistributorId').textContent = deleteBtn.dataset.id || '–';
            document.getElementById('deleteDistributorName').textContent = deleteBtn.dataset.name || '–';
            document.getElementById('globalDeleteForm').action = deleteBtn.dataset.delete_url || '#';
            openModal('globalDeleteModal');
            return;
        }

        const editBtn = e.target.closest('.js-open-edit');

        if (editBtn) {
            document.getElementById('globalEditForm').action = editBtn.dataset.update_url || '';

            document.getElementById('edit_name').value = editBtn.dataset.name || '';
            document.getElementById('edit_short_name').value = editBtn.dataset.short_name || '';
            document.getElementById('edit_account_number').value = editBtn.dataset.account_number || '';
            document.getElementById('edit_street').value = editBtn.dataset.street || '';
            document.getElementById('edit_postal_code').value = editBtn.dataset.postal_code || '';
            document.getElementById('edit_city').value = editBtn.dataset.city || '';
            document.getElementById('edit_phone').value = editBtn.dataset.phone || '';
            document.getElementById('edit_mobile').value = editBtn.dataset.mobile || '';
            document.getElementById('edit_email').value = editBtn.dataset.email || '';
            document.getElementById('edit_cash_discount').value = editBtn.dataset.cash_discount || '';
            document.getElementById('edit_payment_terms').value = editBtn.dataset.payment_terms || '';
            document.getElementById('edit_status').value = editBtn.dataset.status || 'Unpublished';

            const preview = document.getElementById('edit_logo_preview');

            if (editBtn.dataset.image) {
                preview.src = editBtn.dataset.image;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }

            openModal('globalEditModal');
        }
    });

    document.getElementById('dpReloadBtn')?.addEventListener('click', function () {
        loadDistributorProducts(document.getElementById('dpSearchInput')?.value || '');
    });

    document.getElementById('dpSearchInput')?.addEventListener('input', function () {
        clearTimeout(dpSearchTimer);

        dpSearchTimer = setTimeout(() => {
            loadDistributorProducts(this.value || '');
        }, 350);
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
        }
    });

    @if(session('update_msg'))
        toast('ok', 'Aktualisiert', @json(session('update_msg')));
    @endif

    @if(session('save_msg'))
        toast('ok', 'Gespeichert', @json(session('save_msg')));
    @endif

    @if(session('delete_msg'))
        toast('bad', 'Gelöscht', @json(session('delete_msg')));
    @endif

    @if($errors->any())
        openModal('modalCreate');
    @endif
</script>
@endpush
@endonce

@push('scripts')
<script>
    window.GlobalBreadcrumbs = [
        {
            label: 'Dashboard',
            url: "{{ url('/') }}"
        },
        {
            label: 'Lieferanten',
            url: "{{ url()->current() }}",
            clickable: false
        }
    ];

    if (window.setGlobalBreadcrumbs) {
        window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
</script>
@endpush