@extends('admin.layouts.app')
{{-- OFFER_INDEX_SELECT2_MODAL_FIX_V2: Customer/Object Select2 dropdown locked to modal field container --}}
{{-- OFFER_INDEX_FOLDER_EMPTY_TOTAL_FIX_V1: show 0,00 € when all folders are deleted --}}

@section('title', 'Angebote')

@once
  @push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
      :root {
        --app-bg: #f3f4f6;
        --card-bg: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;

        --primary: var(--sa-accent);
        --primary-hover: var(--sa-accent-hover);
        --primary-light: var(--sa-accent-light);

        --blue: #74b2d4;
        --blue-light: #eff6ff;

        --success: #10b981;
        --success-light: #ecfdf5;

        --warning: #f59e0b;
        --warning-light: #fffbeb;

        --danger: #ef4444;
        --danger-hover: #dc2626;
        --danger-light: #fef2f2;

        --gray: #6b7280;
        --gray-light: #f3f4f6;

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
        margin-bottom: 18px;
      }

      .oc-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
      }

      /* Modals Z-Indexes */
      #crud-modal {
        z-index: 1100;
      }

      #folder-modal {
        z-index: 1200;
      }

      #offer-folders-modal {
        z-index: 1300;
      }

      #alert-modal {
        z-index: 1800;
      }

      #duplicate-offer-modal {
        z-index: 1450;
      }

      #clone-folder-modal {
        z-index: 1450;
      }

      #offer-team-modal {
        z-index: 1500;
      }

      body.oc-modal-open {
        overflow: hidden;
      }

      .oc-title {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.025em;
        color: #111827
      }

      .oc-sub {
        font-size: 14px;
        color: var(--text-muted);
        margin-top: 4px
      }

      .select2-container--open {
        z-index: 1900 !important;
      }

      .select2-dropdown {
        z-index: 1901 !important;
      }

      .select2-search--dropdown .select2-search__field {
        width: 100% !important;
        box-sizing: border-box;
      }

      /* OFFER_INDEX_SELECT2_MODAL_FIX_V2
                 The customer/object Select2 dropdown was jumping to another place because it
                 was initialized while the modal was hidden and its dropdown was attached to
                 the transformed modal container. This host keeps the dropdown physically
                 inside the same field row. */
      #crud-modal.open .oc-modal {
        transform: none !important;
      }

      .oc-select2-host {
        position: relative;
        width: 100%;
        min-height: 42px;
      }

      #customer-object-select2-host .select2-container {
        width: 100% !important;
        display: block !important;
      }

      #customer-object-select2-host .select2-dropdown {
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        min-width: 100% !important;
        z-index: 1755 !important;
        border-color: var(--primary) !important;
        border-radius: 0 0 10px 10px !important;
        box-shadow: 0 18px 40px rgba(17, 24, 39, .18) !important;
      }

      #customer-object-select2-host .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid var(--border) !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        background: #fff !important;
      }

      #customer-object-select2-host .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 12px !important;
        padding-right: 36px !important;
        color: var(--text-main) !important;
      }

      #customer-object-select2-host .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
      }

      #customer-object-select2-host .select2-container--default.select2-container--open .select2-selection--single,
      #customer-object-select2-host .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px var(--primary-light) !important;
      }

      #customer-object-select2-host .select2-results__options {
        max-height: 280px !important;
        overflow-y: auto !important;
      }

      #customer-object-select2-host .select2-search--dropdown {
        padding: 8px !important;
      }

      #customer-object-select2-host .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--border) !important;
        border-radius: 8px !important;
        padding: 9px 10px !important;
        outline: none !important;
      }

      #customer-object-select2-host .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px var(--primary-light) !important;
      }

      /* Select2 inside fixed modals: keep dropdown attached to the modal layer, not the page body */
      #offer-team-modal .select2-container,
      #crud-modal .select2-container {
        width: 100% !important;
      }

      #offer-team-modal .select2-dropdown,
      #crud-modal .select2-dropdown {
        z-index: 1701 !important;
      }

      #offer-team-modal .select2-container--open,
      #crud-modal .select2-container--open {
        z-index: 1700 !important;
      }

      .oc-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 900;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }

      .oc-btn:hover {
        background: var(--primary-hover)
      }

      .oc-btn.danger {
        background: var(--danger)
      }

      .oc-btn.danger:hover {
        background: var(--danger-hover)
      }

      .oc-btn-soft {
        background: #fff;
        color: var(--text-main);
        border: 1px solid var(--border);
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        transition: var(--transition);
      }

      .oc-btn-soft:hover {
        background: #f9fafb
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
        transition: var(--transition)
      }

      .oc-btn-ic:hover {
        background: #f9fafb;
        color: var(--text-main);
        border-color: #d1d5db
      }

      .oc-btn-ic.primary {
        color: var(--primary);
        border-color: var(--primary-light);
        background: var(--primary-light)
      }

      .oc-btn-ic.primary:hover {
        border-color: var(--primary)
      }

      .oc-btn-ic.danger {
        color: var(--danger);
        border-color: rgba(239, 68, 68, .18);
        background: var(--danger-light)
      }

      .oc-btn-ic.danger:hover {
        border-color: rgba(239, 68, 68, .35)
      }

      .oc-btn-ic.active {
        background: var(--primary-light);
        color: var(--primary);
        border-color: var(--primary);
      }

      .oc-analytics {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
      }

      @media(max-width:1200px) {
        .oc-analytics {
          grid-template-columns: repeat(3, minmax(0, 1fr));
        }
      }

      @media(max-width:700px) {
        .oc-analytics {
          grid-template-columns: repeat(2, minmax(0, 1fr));
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
      }

      .oc-stat-icon.total {
        background: var(--blue-light);
        color: var(--blue)
      }

      .oc-stat-icon.draft {
        background: var(--gray-light);
        color: var(--gray)
      }

      .oc-stat-icon.sent {
        background: #eff6ff;
        color: #74b2d4
      }

      .oc-stat-icon.negotiation {
        background: var(--warning-light);
        color: #d97706
      }

      .oc-stat-icon.final {
        background: var(--success-light);
        color: var(--success)
      }

      .oc-stat-icon.cancel {
        background: var(--danger-light);
        color: var(--danger)
      }

      .oc-stat-meta {
        min-width: 0
      }

      .oc-stat-label {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      .oc-stat-value {
        font-size: 24px;
        font-weight: 900;
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
        box-shadow: var(--shadow-sm)
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
        font-weight: 800;
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
        transition: var(--transition);
        min-width: 240px;
        width: 100%;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 10px center;
        background-size: 16px
      }

      .oc-input:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light)
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
        background-size: 14px
      }

      /* -------------------
                             GRID VIEW (NEW)
                          ------------------- */
      .oc-grid-layout {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
      }

      .oc-grid-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 14px;
        position: relative;
      }

      .oc-grid-card:hover {
        border-color: var(--primary);
        transform: translateY(-4px);
        box-shadow: var(--shadow);
      }


      .oc-recent-card {
        border: 1px solid rgba(116, 178, 212, .32);
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        border-radius: 14px;
        padding: 10px 12px;
        margin: 10px 0 0;
      }

      .oc-recent-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
      }

      .oc-recent-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 8px;
        background: var(--blue-light);
        color: #1d4ed8;
        border: 1px solid rgba(116, 178, 212, .45);
        font-size: 10px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      .oc-recent-date {
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 850;
        white-space: nowrap;
      }

      .oc-recent-title {
        margin-top: 7px;
        color: #111827;
        font-size: 13px;
        font-weight: 950;
        line-height: 1.3;
      }

      .oc-recent-message,
      .oc-recent-change-line {
        margin-top: 3px;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
      }

      .oc-recent-change-line {
        display: flex;
        align-items: flex-start;
        gap: 5px;
      }

      .oc-recent-change-line::before {
        content: "•";
        color: var(--blue);
        font-weight: 900;
      }

      .oc-recent-card.compact {
        padding: 8px 10px;
        margin-top: 8px;
      }

      .oc-recent-card.compact .oc-recent-title {
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .oc-grid-card.is-recent {
        border-color: rgba(116, 178, 212, .75);
        box-shadow: 0 14px 30px -18px rgba(116, 178, 212, .75);
      }

      .oc-grid-card-price {
        font-size: 26px;
        font-weight: 900;
        color: #111827;
        letter-spacing: -0.02em;
      }

      .oc-grid-card-customer {
        font-size: 16px;
        font-weight: 900;
        color: #111827;
        line-height: 1.3;
      }

      .oc-grid-card-object {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 4px;
      }


      /* -------------------
                             LIST (COLLAPSE) VIEW
                          ------------------- */
      .oc-list-head {
        display: grid;
        grid-template-columns: 76px minmax(200px, 1.3fr) minmax(180px, 1fr) minmax(160px, .9fr) 150px 120px 120px;
        gap: 14px;
        align-items: center;
        padding: 0 16px 10px 16px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      @media(max-width:1180px) {
        .oc-list-head {
          display: none;
        }
      }

      .oc-head-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        user-select: none;
        background: none;
        border: none;
        padding: 0;
        color: inherit;
        font: inherit;
        font-weight: 900;
      }

      .oc-sort-mark {
        font-size: 12px;
        color: #9ca3af;
      }

      .oc-sort-mark.active {
        color: var(--primary)
      }

      .oc-list {
        display: flex;
        flex-direction: column;
        gap: 12px
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
        grid-template-columns: 76px minmax(200px, 1.3fr) minmax(180px, 1fr) minmax(160px, .9fr) 150px 120px 120px;
        cursor: pointer;
      }

      @media(max-width:1180px) {
        .oc-item-header {
          grid-template-columns: 56px 1fr;
          grid-template-rows: auto auto auto auto;
          gap: 12px;
        }

        .oc-responsive-col {
          grid-column: 2;
        }

        .oc-actions-wrap {
          grid-column: 2;
          justify-self: start;
        }

        .oc-actions {
          grid-column: 2;
          justify-self: start;
        }
      }

      .oc-cell {
        min-width: 0
      }

      .oc-cell-title {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 4px;
        display: none;
      }

      @media(max-width:1180px) {
        .oc-cell-title {
          display: block;
        }
      }

      .oc-ic {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-light);
        color: var(--primary);
        transition: transform .2s;
      }

      .oc-ic.is-open {
        transform: rotate(90deg);
      }

      .oc-ic svg {
        width: 24px;
        height: 24px
      }

      .oc-main {
        display: flex;
        flex-direction: column;
        min-width: 0
      }

      .oc-ttl {
        font-weight: 800;
        font-size: 15px;
        margin-bottom: 4px;
        color: #111827
      }

      .oc-subt {
        font-size: 13px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
      }

      .oc-tag {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        background: var(--primary-light);
        color: #6d8c12;
        margin-right: 6px;
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

      .oc-status-pill.gray {
        background: #f3f4f6;
        color: #4b5563;
      }

      .oc-status-pill.blue {
        background: #eff6ff;
        color: #1d4ed8;
      }

      .oc-status-pill.orange {
        background: #fffbeb;
        color: #b45309;
      }

      .oc-status-pill.green {
        background: #ecfdf5;
        color: #047857;
      }

      .oc-status-pill.red {
        background: #fef2f2;
        color: #b91c1c;
      }

      .oc-actions-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
      }

      .oc-actions {
        display: flex;
        gap: 8px;
      }

      .oc-item-body {
        padding: 0 16px 16px 16px;
        border-top: 1px solid var(--border);
        background: #fafafa;
        display: none;
      }

      .oc-item-body.is-open {
        display: block;
      }

      .oc-item-body-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
        margin-bottom: 8px;
        flex-wrap: wrap;
      }

      .oc-item-body-title {
        margin: 0;
        font-size: 14px;
        font-weight: 900;
        color: #111827;
      }

      .oc-meta-inline {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
      }

      .oc-mini-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid var(--border);
        font-size: 12px;
        font-weight: 800;
        color: var(--text-muted);
      }

      /* -------------------
                             SPLIT VIEW
                          ------------------- */
      .oc-split-layout {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 20px;
        align-items: start;
      }

      @media(max-width:992px) {
        .oc-split-layout {
          grid-template-columns: 1fr;
        }
      }

      .oc-sidebar {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        height: 75vh;
        min-height: 600px;
        overflow-y: auto;
        box-shadow: var(--shadow-sm);
      }

      .oc-sidebar::-webkit-scrollbar {
        width: 6px;
      }

      .oc-sidebar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
      }

      .oc-sidebar-item {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: var(--transition);
      }

      .oc-sidebar-item:last-child {
        border-bottom: none;
      }

      .oc-sidebar-item:hover {
        background: #f9fafb;
      }

      .oc-sidebar-item.active {
        background: var(--blue-light);
        border-left: 4px solid var(--blue);
      }

      .oc-detail-pane {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        min-height: 75vh;
        box-shadow: var(--shadow-sm);
      }

      .oc-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 20px;
      }

      /* Stacked Avatars for Sidebar */
      .oc-avatar-group {
        display: flex;
        align-items: center;
      }

      .oc-avatar-tiny {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        border: 2px solid #fff;
        margin-left: -8px;
        object-fit: cover;
        background: #f3f4f6;
        box-shadow: var(--shadow-sm);
      }

      .oc-avatar-tiny:first-child {
        margin-left: 0;
      }

      .oc-avatar-more {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        border: 2px solid #fff;
        margin-left: -8px;
        background: #e5e7eb;
        color: #4b5563;
        font-size: 10px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
      }

      /* -------------------
                             FOLDERS (SHARED)
                          ------------------- */
      .folder-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        margin-top: 8px;
      }

      .folder-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
      }

      .folder-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
      }

      .folder-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
      }

      .folder-meta {
        min-width: 0;
        flex: 1;
      }

      .folder-title {
        font-weight: 800;
        font-size: 13px;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .folder-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 3px;
      }

      .folder-footer {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
      }

      .folder-user {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
      }

      .folder-avatar {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        object-fit: cover;
        border: 1px solid var(--border);
        background: #f3f4f6;
        flex: 0 0 auto;
      }

      .folder-user-name {
        font-size: 12px;
        font-weight: 700;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
      }

      .folder-date {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 700;
      }

      .oc-empty {
        text-align: center;
        padding: 60px;
        color: var(--text-muted);
        background: #fff;
        border: 1px dashed var(--border);
        border-radius: 16px;
      }

      /* Modals & Toasts */
      .oc-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1100;
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
        pointer-events: auto
      }

      .oc-modal {
        width: 100%;
        max-width: 560px;
        background: #fff;
        border: 1px solid rgba(229, 231, 235, .9);
        border-radius: 16px;
        box-shadow: var(--shadow);
        transform: translateY(12px) scale(.985);
        transition: transform .22s ease;
        overflow: visible;
      }

      .oc-modal-backdrop.open .oc-modal {
        transform: translateY(0) scale(1)
      }

      .oc-modal-h {
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        border-bottom: 1px solid var(--border);
        background: #fafafa;
        border-radius: 16px 16px 0 0;
      }

      .oc-modal-ttl {
        font-weight: 900;
        font-size: 16px;
        line-height: 1.2;
        margin: 0;
        color: #111827
      }

      .oc-modal-b {
        padding: 20px 18px;
        max-height: 70vh;
        overflow-y: auto;
      }

      .oc-modal-f {
        padding: 14px 18px;
        border-top: 1px solid var(--border);
        background: #fafafa;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-radius: 0 0 16px 16px;
      }

      .oc-form-group {
        margin-bottom: 16px;
      }

      .oc-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
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
        max-width: 360px;
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
        from {
          transform: translateX(100%);
          opacity: 0
        }

        to {
          transform: translateX(0);
          opacity: 1
        }
      }

      .oc-toast-ic {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto
      }

      .oc-toast-ic.ok {
        background: var(--success-light);
        color: var(--success)
      }

      .oc-toast-ic.warn {
        background: var(--warning-light);
        color: var(--warning)
      }

      .oc-toast-ic.bad {
        background: var(--danger-light);
        color: var(--danger)
      }

      .oc-toast-ttl {
        font-weight: 900;
        font-size: 13px;
        margin: 0;
        color: #111827
      }

      .oc-toast-msg {
        font-size: 12px;
        color: #374151;
        margin: 4px 0 0 0;
        line-height: 1.4
      }

      .oc-toast-x {
        margin-left: auto;
        background: transparent;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
      }

      .oc-btn-ic.clone {
        color: var(--blue);
        border-color: #dbeafe;
        background: #eff6ff;
      }

      .oc-btn-ic.clone:hover {
        border-color: var(--blue);
        background: #dbeafe;
      }

      .folder-card.is-locked {
        border-color: #fecaca;
        background: linear-gradient(180deg, #ffffff 0%, #fef2f2 100%);
      }

      .folder-card.is-locked:hover {
        border-color: #ef4444;
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(239, 68, 68, 0.10);
      }

      /* -------------------
                             OFFER TEAM CONNECTION
                          ------------------- */
      .oc-team-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-width: 0;
      }

      .oc-team-mini {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border: 1px solid var(--border);
        background: #fff;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        color: #374151;
        max-width: 180px;
      }

      .oc-team-avatar {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        object-fit: cover;
        background: #f3f4f6;
        border: 1px solid #fff;
        box-shadow: var(--shadow-sm);
        flex: 0 0 auto;
      }

      .oc-team-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .oc-team-empty {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 700;
      }

      .oc-team-panel {
        border: 1px solid var(--border);
        background: #f9fafb;
        border-radius: 12px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      .oc-team-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
      }

      .oc-team-panel-title {
        font-size: 13px;
        font-weight: 900;
        color: #111827;
      }

      .oc-team-help {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
      }

      .oc-team-chip-list {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
      }

      .oc-team-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
      }

      .oc-team-chip small {
        color: var(--text-muted);
        font-weight: 700;
      }

      .oc-team-action-btn {
        background: #fff;
        border: 1px solid var(--border);
        color: var(--text-main);
        border-radius: 999px;
        padding: 7px 11px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
      }

      .oc-team-action-btn:hover {
        border-color: var(--primary);
        color: #6d8c12;
        background: var(--primary-light);
      }

      .oc-team-action-btn svg {
        width: 15px;
        height: 15px;
      }

      .select2-container--default .select2-selection--multiple {
        border: 1px solid var(--border) !important;
        border-radius: 8px !important;
        min-height: 42px !important;
        padding: 3px 5px !important;
      }

      .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px var(--primary-light) !important;
      }
    </style>
  @endpush
@endonce

@section('content')
  <div class="oc-wrap" id="offer-app">
    {{-- CI-Vereinheitlichung 2026-07-15 (Welle 2): Alt-Kopf durch das gemeinsame Bauteil ersetzt. --}}
    <x-page-head title="Angebote"
        sub="Analytics, Filter, Sortierung und automatisches Ordner-Management."
        current="Angebote">
        <x-slot:actions>
            <button class="oc-btn" type="button" onclick="openModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Neues Angebot
            </button>
        </x-slot:actions>
    </x-page-head>

    <div class="oc-analytics" id="analytics-cards">
      <div class="oc-stat">
        <div class="oc-stat-icon total"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="M3 7h18M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
          </svg></div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Gesamt</div>
          <div class="oc-stat-value" id="stat-total">0</div>
          <div class="oc-stat-sub">Alle Angebote</div>
        </div>
      </div>
      <div class="oc-stat">
        <div class="oc-stat-icon draft"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
          </svg></div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Entwurf</div>
          <div class="oc-stat-value" id="stat-draft">0</div>
          <div class="oc-stat-sub">In Bearbeitung</div>
        </div>
      </div>
      <div class="oc-stat">
        <div class="oc-stat-icon sent"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="M22 2L11 13" />
            <path d="M22 2l-7 20-4-9-9-4 20-7z" />
          </svg></div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Gesendet</div>
          <div class="oc-stat-value" id="stat-sent">0</div>
          <div class="oc-stat-sub">An Kunden verschickt</div>
        </div>
      </div>
      <div class="oc-stat">
        <div class="oc-stat-icon negotiation"><svg viewBox="0 0 24 24" width="22" height="22" fill="none"
            stroke="currentColor" stroke-width="2">
            <path d="M8 12h8" />
            <path d="M12 8v8" />
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
          </svg></div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Verhandlung</div>
          <div class="oc-stat-value" id="stat-negotiation">0</div>
          <div class="oc-stat-sub">Offene Kommunikation</div>
        </div>
      </div>
      <div class="oc-stat">
        <div class="oc-stat-icon final"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="M20 6L9 17l-5-5" />
          </svg></div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Abgeschlossen</div>
          <div class="oc-stat-value" id="stat-final">0</div>
          <div class="oc-stat-sub">Erfolgreich finalisiert</div>
        </div>
      </div>
      <div class="oc-stat">
        <div class="oc-stat-icon cancel"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
            stroke-width="2">
            <path d="M18 6L6 18M6 6l12 12" />
          </svg></div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Storniert</div>
          <div class="oc-stat-value" id="stat-cancel">0</div>
          <div class="oc-stat-sub">Abgebrochen oder verloren</div>
        </div>
      </div>
    </div>

    <div class="oc-toolbar">
      <div class="oc-toolbar-left">
        <div class="oc-filter-block search">
          <label class="oc-filter-label">Suche</label>
          <input class="oc-input" id="search-input" placeholder="Suchen nach Kunde, Produkt, Straße..." autocomplete="off"
            oninput="handleSearch(this.value)" />
        </div>
        <div class="oc-filter-block">
          <label class="oc-filter-label">Status</label>
          <select class="oc-select" id="status-filter" onchange="handleFilter('status', this.value)">
            <option value="">Alle Status</option>
            <option value="draft">Entwurf</option>
            <option value="sent">Gesendet</option>
            <option value="negotiation">Verhandlung</option>
            <option value="final">Abgeschlossen</option>
            <option value="cancel">Storniert</option>
          </select>
        </div>
        <div class="oc-filter-block">
          <label class="oc-filter-label">Produkt</label>
          <select class="oc-select" id="product-filter" onchange="handleFilter('product', this.value)">
            <option value="">Alle Produkte</option>
          </select>
        </div>
        <div class="oc-filter-block">
          <label class="oc-filter-label">Erstellt von</label>
          <select class="oc-select" id="creator-filter" onchange="handleFilter('creator', this.value)">
            <option value="">Alle Benutzer</option>
          </select>
        </div>
        <div class="oc-filter-block">
          <label class="oc-filter-label">Ordner</label>
          <select class="oc-select" id="folder-filter" onchange="handleFilter('hasFolders', this.value)">
            <option value="">Alle</option>
            <option value="yes">Mit Ordner</option>
            <option value="no">Ohne Ordner</option>
          </select>
        </div>
      </div>

      <div class="oc-toolbar-right">
        <div class="oc-filter-block">
          <label class="oc-filter-label">Ansicht</label>
          <div style="display:flex; background:#f9fafb; border:1px solid var(--border); border-radius:8px; padding:2px;">
            <button class="oc-btn-ic active" id="view-grid-btn" type="button" onclick="setViewMode('grid')"
              style="border:none; border-radius:6px; box-shadow:none; width:36px; height:32px;" title="Kartenansicht">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
              </svg>
            </button>
            <button class="oc-btn-ic" id="view-split-btn" type="button" onclick="setViewMode('split')"
              style="border:none; border-radius:6px; box-shadow:none; width:36px; height:32px;" title="Split View">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="9" y1="3" x2="9" y2="21"></line>
              </svg>
            </button>
            <button class="oc-btn-ic" id="view-list-btn" type="button" onclick="setViewMode('list')"
              style="border:none; border-radius:6px; box-shadow:none; width:36px; height:32px;"
              title="Listenansicht (Collapse)">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="8" y1="6" x2="21" y2="6"></line>
                <line x1="8" y1="12" x2="21" y2="12"></line>
                <line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                <line x1="3" y1="18" x2="3.01" y2="18"></line>
              </svg>
            </button>
          </div>
        </div>
        <div class="oc-filter-block">
          <label class="oc-filter-label">Sortieren</label>
          <select class="oc-select" id="sort-by" onchange="handleSortChange()">
            <option value="id">ID</option>
            <option value="customer">Kunde</option>
            <option value="created_at">Datum</option>
          </select>
        </div>
        <button class="oc-btn-soft" type="button" onclick="resetFilters()">Reset</button>
      </div>
    </div>

    <div id="list-head-container" style="display:none;">
      <div class="oc-list-head">
        <div></div>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('customer')">Kunde / Objekt <span
            class="oc-sort-mark" id="sort-mark-customer">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('product')">Produkt / Service <span
            class="oc-sort-mark" id="sort-mark-product">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('creator')">Erstellt von <span
            class="oc-sort-mark" id="sort-mark-creator">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('status')">Status <span class="oc-sort-mark"
            id="sort-mark-status">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('folders')">Ordner <span class="oc-sort-mark"
            id="sort-mark-folders">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('created_at')">Datum <span
            class="oc-sort-mark" id="sort-mark-created_at">↕</span></button>
      </div>
    </div>

    <div id="offer-list" class="oc-list" aria-live="polite">
      <div class="oc-empty">Lade Daten...</div>
    </div>
  </div>

  {{-- 🟢 NEU: Offer Folders Modal (The Window File Layout) --}}
  <div class="oc-modal-backdrop" id="offer-folders-modal">
    <div class="oc-modal" style="max-width:760px; display:flex; flex-direction:column; max-height:85vh;">
      <div class="oc-modal-h">
        <h3 id="offer-folders-modal-title" class="oc-modal-ttl" style="line-height:1.4;"></h3>
        <button class="oc-btn-ic" type="button" onclick="closeOfferFoldersModal()" style="width:32px;height:32px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="oc-modal-b" style="background:#f9fafb; flex:1; overflow-y:auto;">
        <div id="offer-folders-modal-list"></div>
      </div>
      <div class="oc-modal-f" style="justify-content:space-between;">
        <button type="button" class="oc-btn-soft" onclick="closeOfferFoldersModal()">Schließen</button>
        <button type="button" class="oc-btn" id="offer-folders-new-btn">
          <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
            <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2z" />
          </svg>
          Neuer Ordner
        </button>
      </div>
    </div>
  </div>

  {{-- CRUD Modal --}}
  <div class="oc-modal-backdrop" id="crud-modal">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <h3 id="modal-title" class="oc-modal-ttl">Neues Angebot erstellen</h3>
        <button class="oc-btn-ic" type="button" onclick="closeModal()" style="width:32px;height:32px"><svg
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg></button>
      </div>
      <div class="oc-modal-b">
        <form id="crud-form" onsubmit="submitForm(event)">
          <input type="hidden" id="inp-id">
          <div class="oc-form-group">
            <label class="oc-label">Kunde & Objekt suchen *</label>
            <div id="customer-object-select2-host" class="oc-select2-host">
              <select id="inp-customer-object" style="width:100%;"></select>
            </div>
            <input type="hidden" id="inp-customer" required>
            <input type="hidden" id="inp-alternative" required>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Produkt auswählen *</label>
            <select id="inp-product" class="oc-select" style="width:100%" required disabled>
              <option value="">Bitte zuerst Kunde & Objekt wählen</option>
            </select>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Service / Projekt (Optional)</label>
            <input type="text" id="inp-service" class="oc-input-form" placeholder="z.B. Installation">
          </div>
          <div id="folder-creation-section">
            <hr style="border:0; border-top:1px solid var(--border); margin: 24px 0;">
            <div style="font-weight:800; font-size:14px; margin-bottom:12px; color:var(--text-main);">Standard-Ordner
              Einstellungen</div>
            <div class="oc-form-group">
              <label class="oc-label">Ordner Name *</label>
              <input type="text" id="inp-folder-name" class="oc-input-form" placeholder="z.B. V1 Entwurf">
            </div>
            <div class="oc-form-group">
              <label class="oc-label">Ordner Farbe</label>
              <div style="display:flex; align-items:center; gap:12px;">
                <input type="color" id="inp-folder-color" value="#93c21c"
                  style="height:42px; width:60px; border:1px solid var(--border); border-radius:8px; cursor:pointer; padding:2px;">
                <span style="font-size:12px; color:var(--text-muted);">Farbe zur besseren Erkennung</span>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal()">Abbrechen</button>
        <button type="submit" form="crud-form" class="oc-btn" id="btn-submit">Speichern</button>
      </div>
    </div>
  </div>

  {{-- Alert Modal --}}
  <div class="oc-modal-backdrop" id="alert-modal">
    <div class="oc-modal" style="max-width:460px;">
      <div class="oc-modal-h">
        <h3 id="alert-modal-title" class="oc-modal-ttl">Hinweis</h3>
        <button class="oc-btn-ic" type="button" onclick="closeAlertModal()" style="width:32px;height:32px"><svg
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg></button>
      </div>
      <div class="oc-modal-b">
        <div id="alert-modal-message" style="font-size:14px; color:var(--text-main); line-height:1.6;"></div>
      </div>
      <div class="oc-modal-f" id="alert-modal-actions"><button type="button" class="oc-btn"
          onclick="closeAlertModal()">Schließen</button></div>
    </div>
  </div>

  {{-- Folder Modal --}}
  <div class="oc-modal-backdrop" id="folder-modal">
    <div class="oc-modal" style="max-width:520px;">
      <div class="oc-modal-h">
        <h3 id="folder-modal-title" class="oc-modal-ttl">Ordner erstellen</h3>
        <button class="oc-btn-ic" type="button" onclick="closeFolderModal()" style="width:32px;height:32px"><svg
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg></button>
      </div>
      <div class="oc-modal-b">
        <form id="folder-form" onsubmit="submitFolderForm(event)">
          <input type="hidden" id="folder-offer-id">
          <input type="hidden" id="folder-id">
          <div class="oc-form-group">
            <label class="oc-label">Ordner Name *</label>
            <input type="text" id="folder-name" class="oc-input-form" placeholder="z.B. V2 Kalkulation" required>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Ordner Farbe</label>
            <input type="color" id="folder-color" value="#93c21c"
              style="height:42px; width:70px; border:1px solid var(--border); border-radius:8px; cursor:pointer; padding:2px;">
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Status</label>
            <select id="folder-status" class="oc-select" style="width:100%;">
              <option value="draft">Entwurf</option>
              <option value="sent">Gesendet</option>
              <option value="negotiation">Verhandlung</option>
              <option value="final">Abgeschlossen</option>
              <option value="cancel">Storniert</option>
            </select>
          </div>
        </form>
      </div>
      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeFolderModal()">Abbrechen</button>
        <button type="submit" form="folder-form" class="oc-btn" id="folder-submit-btn">Speichern</button>
      </div>
    </div>
  </div>

  {{-- Duplicate Offer Modal --}}
  <div class="oc-modal-backdrop" id="duplicate-offer-modal">
    <div class="oc-modal" style="max-width:620px;">
      <div class="oc-modal-h">
        <h3 class="oc-modal-ttl">Angebot bereits vorhanden</h3>
        <button class="oc-btn-ic" type="button" onclick="closeDuplicateOfferModal()" style="width:32px;height:32px"><svg
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg></button>
      </div>
      <div class="oc-modal-b">
        <div style="font-size:14px; color:var(--text-main); line-height:1.7; margin-bottom:14px;">Für diese Kombination
          aus <strong>Kunde</strong>, <strong>Objekt</strong> und <strong>Produkt</strong> wurde bereits ein Angebot
          erstellt.</div>
        <div id="duplicate-offer-content"></div>
      </div>
      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeDuplicateOfferModal()">Schließen</button>
        <button type="button" class="oc-btn" id="duplicate-offer-open-btn">Datensatz öffnen</button>
      </div>
    </div>
  </div>

  {{-- Clone Folder Modal --}}
  <div class="oc-modal-backdrop" id="clone-folder-modal">
    <div class="oc-modal" style="max-width:520px;">
      <div class="oc-modal-h">
        <h3 class="oc-modal-ttl">Ordner klonen</h3>
        <button class="oc-btn-ic" type="button" onclick="closeCloneFolderModal()" style="width:32px;height:32px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="oc-modal-b">
        <form id="clone-folder-form" onsubmit="submitCloneFolderForm(event)">
          <input type="hidden" id="clone-folder-id">

          <div class="oc-form-group">
            <label class="oc-label">Neuer Ordnername *</label>
            <input type="text" id="clone-folder-name" class="oc-input-form" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Farbe</label>
            <input type="color" id="clone-folder-color" value="#93c21c"
              style="height:42px; width:70px; border:1px solid var(--border); border-radius:8px; cursor:pointer; padding:2px;">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Status</label>
            <select id="clone-folder-status" class="oc-select" style="width:100%;">
              <option value="draft">Entwurf</option>
              <option value="sent">Gesendet</option>
              <option value="negotiation">Verhandlung</option>
              <option value="final">Abgeschlossen</option>
              <option value="cancel">Storniert</option>
            </select>
          </div>

          <div class="oc-form-group" style="margin-bottom:0;">
            <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer;">
              <input type="checkbox" id="clone-folder-everything" checked style="margin-top:3px;">
              <span style="font-size:13px; color:var(--text-main);">
                Alle Inhalte mitklonen
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                  Positionen, Details, Anhänge und weitere Ordnerdaten übernehmen.
                </div>
              </span>
            </label>
          </div>
        </form>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeCloneFolderModal()">Abbrechen</button>
        <button type="submit" form="clone-folder-form" class="oc-btn" id="clone-folder-submit-btn">Klonen</button>
      </div>
    </div>
  </div>


  {{-- Offer Team Modal --}}
  <div class="oc-modal-backdrop" id="offer-team-modal">
    <div class="oc-modal" style="max-width:640px;">
      <div class="oc-modal-h">
        <h3 id="offer-team-modal-title" class="oc-modal-ttl">Team bearbeiten</h3>
        <button class="oc-btn-ic" type="button" onclick="closeTeamModal()" style="width:32px;height:32px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="oc-modal-b">
        <form id="offer-team-form" onsubmit="submitTeamForm(event)">
          <input type="hidden" id="team-offer-id">
          <div class="oc-team-panel" style="margin-bottom:16px;">
            <div class="oc-team-panel-head">
              <div class="oc-team-panel-title">Aktuelles Angebotsteam</div>
              <span class="oc-mini-badge">Quelle: lead_product_lists.teams</span>
            </div>
            <div id="team-current-list" class="oc-team-chip-list">
              <span class="oc-team-empty">Kein Team zugeordnet.</span>
            </div>
            <div class="oc-team-help">
              Diese Auswahl wird direkt mit dem Team aus Kanban und Kundenprofil verbunden. Dadurch bleibt Angebot, Kunde
              und Kanban gleich synchron.
            </div>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Mitarbeiter auswählen *</label>
            <select id="team-employee-select" multiple style="width:100%;"></select>
          </div>

          <div class="oc-form-group" style="margin-bottom:0;">
            <label class="oc-label">Stage / Bereich</label>
            <select id="team-stage" class="oc-select" style="width:100%;">
              <option value="offer">Angebot</option>
              <option value="deal">Deal</option>
              <option value="auftrag">Auftrag</option>
            </select>
          </div>
        </form>
      </div>
      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeTeamModal()">Abbrechen</button>
        <button type="submit" form="offer-team-form" class="oc-btn" id="team-submit-btn">Team speichern</button>
      </div>
    </div>
  </div>

  <div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
      const ENDPOINTS = {
        data: @json(route('admin.offers.data')),
        store: @json(route('admin.offers.store')),
        updateBase: @json(url('/admin/offers')),
        searchCustomerObjects: @json(route('admin.offers.search-customer-objects')),
        getProducts: @json(route('admin.offers.get-products')),
        folderStoreBase: @json(url('/admin/offers')),
        folderUpdateBase: @json(url('/admin/offers/folders')),
        cloneFolderBase: @json(url('/admin/offers/folders')),
        teamBase: @json(url('/admin/offers')),
        employeeSearch: @json(url('/admin/offers/employees/search')),  // fixed: real route inside admin/offers
      };

      const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const EMPLOYEE_IMAGE_BASE = @json(asset('images/employee'));
      const DEFAULT_AVATAR = @json(asset('images/default-avatar.png'));

      const STATUS_MAP = {
        draft: { label: 'Entwurf', color: 'gray' },
        sent: { label: 'Gesendet', color: 'blue' },
        negotiation: { label: 'Verhandlung', color: 'orange' },
        final: { label: 'Abgeschlossen', color: 'green' },
        cancel: { label: 'Storniert', color: 'red' }
      };

      let state = {
        viewMode: 'grid', // 🟢 Set Grid as default initially to show it off
        selectedOfferId: null,
        offers: [],
        expanded: {},
        search: '',
        filters: { status: '', product: '', creator: '', hasFolders: '' },
        sort: { by: 'latest_change', dir: 'desc' }
      };

      let alertModalResolver = null;

      const MODAL_IDS = [
        'crud-modal',
        'folder-modal',
        'offer-folders-modal',
        'duplicate-offer-modal',
        'clone-folder-modal',
        'offer-team-modal',
        'alert-modal'
      ];

      function closeAllModals(exceptId = null) {
        MODAL_IDS.forEach(id => {
          if (id !== exceptId) document.getElementById(id)?.classList.remove('open');
        });
        try {
          const $customerObject = $('#inp-customer-object');
          if ($customerObject.data('select2')) $customerObject.select2('close');
        } catch (e) { }

        if (!exceptId || exceptId !== 'offer-team-modal') {
          try {
            const $teamSelect = $('#team-employee-select');
            if ($teamSelect.data('select2')) $teamSelect.select2('close');
          } catch (e) { }
        }
        document.body.classList.toggle('oc-modal-open', !!exceptId);
      }

      function openExclusiveModal(id) {
        closeAllModals(id);
        document.getElementById(id)?.classList.add('open');
        document.body.classList.add('oc-modal-open');
      }

      function closeExclusiveModal(id) {
        document.getElementById(id)?.classList.remove('open');
        const hasOpenModal = MODAL_IDS.some(modalId => document.getElementById(modalId)?.classList.contains('open'));
        document.body.classList.toggle('oc-modal-open', hasOpenModal);
      }

      const esc = (s) => String(s ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');

      function iconSvg(name) {
        if (name === 'chevron') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>`;
        if (name === 'folder') return `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2z"/></svg>`;
        if (name === 'edit') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`;
        if (name === 'trash') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>`;
        if (name === 'clone') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="9" width="11" height="11" rx="2"/>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                              </svg>`;
        if (name === 'users') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`;
        if (name === 'lock') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="5" y="11" width="14" height="10" rx="2"/>
                                <path d="M8 11V8a4 4 0 1 1 8 0v3"/>
                              </svg>`;
        return '';
      }



      function getEmployeeImageUrl(employeeOrTeam) {
        const image = employeeOrTeam?.employee_image || employeeOrTeam?.image || employeeOrTeam?.avatar || '';
        if (!image) return DEFAULT_AVATAR;
        if (/^https?:\/\//i.test(image) || image.startsWith('/')) return image;
        return `${EMPLOYEE_IMAGE_BASE}/${image}`;
      }

      function getOfferTeams(o) {
        const possible = [
          o?.lead_product_list?.teams,
          o?.leadProductList?.teams,
          o?.lead_product_list_teams,
          o?.team_members,
          o?.teams,
        ];

        let teams = possible.find(v => Array.isArray(v));

        if (!teams && typeof o?.lead_product_list?.teams === 'string') {
          try { teams = JSON.parse(o.lead_product_list.teams); } catch { teams = []; }
        }
        if (!teams && typeof o?.leadProductList?.teams === 'string') {
          try { teams = JSON.parse(o.leadProductList.teams); } catch { teams = []; }
        }
        if (!teams && typeof o?.teams === 'string') {
          try { teams = JSON.parse(o.teams); } catch { teams = []; }
        }

        return Array.isArray(teams) ? teams : [];
      }

      function getTeamEmployeeId(team) {
        return Number(team?.employee_id || team?.id || team?.employee?.id || 0);
      }

      function getTeamEmployeeName(team) {
        const direct = team?.employee_name || team?.name_full || team?.full_name;
        if (direct) return direct;
        if (team?.employee) return [team.employee.name, team.employee.lastname].filter(Boolean).join(' ').trim() || 'Mitarbeiter';
        return [team?.name, team?.lastname].filter(Boolean).join(' ').trim() || (team?.employee_id ? `Mitarbeiter #${team.employee_id}` : 'Mitarbeiter');
      }

      function getKnownEmployeeOptions() {
        const map = new Map();

        state.offers.forEach(o => {
          if (o.creator?.id) map.set(Number(o.creator.id), { id: Number(o.creator.id), text: getCreatorName(o), image: o.creator.image || null });
          if (o.assignee?.id) map.set(Number(o.assignee.id), { id: Number(o.assignee.id), text: [o.assignee.name, o.assignee.lastname].filter(Boolean).join(' ').trim(), image: o.assignee.image || null });
          getOfferTeams(o).forEach(t => {
            const id = getTeamEmployeeId(t);
            if (id) map.set(id, { id, text: getTeamEmployeeName(t), image: t.employee_image || t.image || t.employee?.image || null });
          });
        });

        return Array.from(map.values()).filter(x => x.id && x.text);
      }

      function buildTeamAvatarsHTML(o, limit = 4) {
        const teams = getOfferTeams(o);
        if (!teams.length) return `<span class="oc-team-empty">Kein Team</span>`;

        const unique = [];
        const seen = new Set();
        teams.forEach(t => {
          const id = getTeamEmployeeId(t);
          if (id && !seen.has(id)) {
            seen.add(id);
            unique.push(t);
          }
        });

        const visible = unique.slice(0, limit);
        let html = `<div class="oc-team-row">`;
        visible.forEach(t => {
          html += `<span class="oc-team-mini" title="${esc(getTeamEmployeeName(t))}">
                                <img class="oc-team-avatar" src="${esc(getEmployeeImageUrl(t.employee || t))}" onerror="this.src='${DEFAULT_AVATAR}'" alt="">
                                <span class="oc-team-name">${esc(getTeamEmployeeName(t))}</span>
                              </span>`;
        });
        if (unique.length > limit) html += `<span class="oc-avatar-more">+${unique.length - limit}</span>`;
        html += `</div>`;
        return html;
      }

      function buildTeamPanelHTML(o) {
        return `
                              <div class="oc-team-panel">
                                <div class="oc-team-panel-head">
                                  <div class="oc-team-panel-title">Team</div>
                                  <button type="button" class="oc-team-action-btn" onclick="event.stopPropagation(); openTeamModal(${o.id})">
                                    ${iconSvg('users')} Team bearbeiten
                                  </button>
                                </div>
                                ${buildTeamAvatarsHTML(o, 6)}
                              </div>
                            `;
      }

      async function fetchOfferTeamIntoState(offerId) {
        if (!offerId) return [];

        try {
          const res = await fetch(`${ENDPOINTS.teamBase}/${offerId}/team`, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const data = await res.json();
          if (res.ok && data.success) {
            refreshOfferTeamInState(offerId, data.teams || []);
            return data.teams || [];
          }
        } catch (e) { }

        return [];
      }

      async function hydrateOfferTeams() {
        const offers = Array.isArray(state.offers) ? state.offers : [];
        if (!offers.length) return;

        await Promise.all(offers.map(o => fetchOfferTeamIntoState(o.id)));
      }

      function formatDate(d) { try { const dt = new Date(d); return Number.isNaN(dt.getTime()) ? '-' : dt.toLocaleDateString('de-DE'); } catch { return '-'; } }
      function formatDateTime(d) { return formatDate(d); }
      function getCustomerName(o) { return [o.customer?.firma, o.customer?.name, o.customer?.lastname].filter(Boolean).join(' ').trim() || 'Unbekannt'; }
      function getObjectName(o) { return [o.alternative?.street, o.alternative?.city].filter(Boolean).join(', ') || 'Kein Objekt'; }
      function getProductName(o) { return o.product?.article_group || 'Unbekannt'; }
      function getCreatorName(o) { return [o.creator?.name, o.creator?.lastname].filter(Boolean).join(' ').trim() || o.creator?.name || 'Unbekannt'; }
      function getFolderCount(o) { return Array.isArray(o.folders) ? o.folders.length : 0; }
      function getStatusPill(sk) { const st = STATUS_MAP[sk] || STATUS_MAP.draft; return `<span class="oc-status-pill ${st.color}">${esc(st.label)}</span>`; }
      function getFolderCreatorName(f) { return [f.creator?.name, f.creator?.lastname].filter(Boolean).join(' ').trim() || f.creator?.name || 'Unbekannt'; }
      function getFolderCreatorImage(f) { return f.creator?.image ? `${EMPLOYEE_IMAGE_BASE}/${f.creator.image}` : DEFAULT_AVATAR; }

      function isFolderLocked(folder) {
        const status = String(folder?.status || '').toLowerCase().trim();
        return status === 'cancel' || status === 'cancelled' || status === 'storniert';
      }

      function isFinalFolderStatus(status) {
        const s = String(status || '').toLowerCase().trim();
        return s === 'final' || s === 'abgeschlossen';
      }
      function getFolderStatusLabel(status) {
        const key = String(status || '').toLowerCase().trim();
        if (STATUS_MAP[key]) return STATUS_MAP[key].label;
        if (key === 'abgeschlossen') return 'Abgeschlossen';
        return 'Entwurf';
      }


      function offerHasFinalFolder(offerId) {
        const offer = state.offers.find(x => Number(x.id) === Number(offerId));
        if (!offer || !Array.isArray(offer.folders)) return false;
        return offer.folders.some(folder => isFinalFolderStatus(folder.status));
      }

      function showOfferFinalizedMessage() {
        return openAlertModal({
          title: 'Angebot finalisiert',
          message: 'Das Angebot ist finalisiert und kann nicht geändert werden.',
          type: 'alert',
          confirmText: 'OK'
        });
      }

      function tryOpenFolder(folder) {
        openFolderPreview(folder.id);
        return true;
      }
      // Get the most recent date between the offer itself and all its folders
      function getLatestUpdateDate(o) {
        let latest = new Date(o.updated_at || o.created_at || 0);
        if (Array.isArray(o.folders)) {
          o.folders.forEach(f => {
            let d = new Date(f.updated_at || f.created_at || 0);
            if (d > latest) latest = d;
          });
        }
        if (Number.isNaN(latest.getTime()) || latest.getTime() === 0) return '-';
        return latest.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) + ' Uhr';
      }



      function getRecentChange(o) {
        const rc = o?.recent_change || null;
        if (rc && (rc.changed_at || rc.changed_at_human || rc.title || rc.message)) return rc;

        return {
          badge: 'Geändert',
          title: 'Letzte Änderung',
          message: '',
          changed_at_human: getLatestUpdateDate(o),
          sort_at: getRecentTimestamp(o),
          changes: []
        };
      }

      function getRecentTimestamp(o) {
        const rc = o?.recent_change || null;
        if (rc?.sort_at) return Number(rc.sort_at) * (Number(rc.sort_at) < 100000000000 ? 1000 : 1);
        if (rc?.changed_at) {
          const t = new Date(rc.changed_at).getTime();
          if (!Number.isNaN(t)) return t;
        }

        let latest = new Date(o?.updated_at || o?.created_at || 0).getTime();
        if (Number.isNaN(latest)) latest = 0;

        if (Array.isArray(o?.folders)) {
          o.folders.forEach(f => {
            const values = [
              f?.recent_change?.changed_at,
              f?.detail?.updated_at,
              f?.updated_at,
              f?.created_at
            ];
            values.forEach(value => {
              const t = new Date(value || 0).getTime();
              if (!Number.isNaN(t) && t > latest) latest = t;
            });
          });
        }

        return latest || 0;
      }

      function formatRecentDate(rc) {
        if (rc?.changed_at_human) return rc.changed_at_human + ' Uhr';
        if (rc?.changed_at) return formatDateTime(rc.changed_at);
        return '-';
      }

      function buildRecentChangeHTML(o, compact = false) {
        const rc = getRecentChange(o);
        const changes = Array.isArray(rc.changes) ? rc.changes.filter(Boolean).slice(0, compact ? 1 : 3) : [];
        const actor = rc.employee_name ? ` · ${esc(rc.employee_name)}` : '';
        const folder = rc.folder_name ? ` · ${esc(rc.folder_name)}` : '';

        return `
                      <div class="oc-recent-card ${compact ? 'compact' : ''}">
                        <div class="oc-recent-top">
                          <span class="oc-recent-badge">${iconSvg('clock')} ${esc(rc.badge || 'Aktuell')}</span>
                          <span class="oc-recent-date">${esc(formatRecentDate(rc))}</span>
                        </div>
                        <div class="oc-recent-title">${esc(rc.title || 'Letzte Änderung')}${folder}${actor}</div>
                        ${rc.message && !compact ? `<div class="oc-recent-message">${esc(rc.message)}</div>` : ''}
                        ${changes.map(line => `<div class="oc-recent-change-line">${esc(line)}</div>`).join('')}
                      </div>
                    `;
      }

      // Extract unique creators from folders and generate overlapping tiny pictures
      function getFolderCreatorsAvatars(o) {
        if (!Array.isArray(o.folders) || !o.folders.length) return '';
        const creators = new Map();
        if (o.creator) creators.set(o.creator.id, o.creator);
        o.folders.forEach(f => { if (f.creator) creators.set(f.creator.id, f.creator); });
        if (creators.size === 0) return '';
        let html = '<div class="oc-avatar-group" title="Beteiligte Mitarbeiter">';
        let count = 0;
        for (let [id, c] of creators) {
          if (count >= 4) { html += `<div class="oc-avatar-more">+${creators.size - 4}</div>`; break; }
          const img = c.image ? `${EMPLOYEE_IMAGE_BASE}/${c.image}` : DEFAULT_AVATAR;
          const name = esc([c.name, c.lastname].filter(Boolean).join(' '));
          html += `<img src="${img}" class="oc-avatar-tiny" title="${name}" onerror="this.src='${DEFAULT_AVATAR}'">`;
          count++;
        }
        html += '</div>';
        return html;
      }

      function setViewMode(mode) {
        state.viewMode = mode;

        ['split', 'list', 'grid'].forEach(m => {
          const btn = document.getElementById(`view-${m}-btn`);
          if (btn) btn.classList.toggle('active', mode === m);
        });

        const listHead = document.getElementById('list-head-container');
        if (listHead) {
          listHead.style.display = mode === 'list' ? 'block' : 'none';
        }

        renderList();
      }

      function selectOfferInSplitView(id) {
        state.selectedOfferId = id;
        renderList();
      }

      async function loadData() {
        try {
          const res = await fetch(ENDPOINTS.data, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
          const json = await res.json();
          if (json.success) {
            state.offers = Array.isArray(json.data) ? json.data : [];
            populateFilters();
            renderAnalytics();
            setViewMode(state.viewMode);
            await hydrateOfferTeams();
            populateFilters();
            renderAnalytics();
            renderList();
          } else {
            document.getElementById('offer-list').innerHTML = `<div class="oc-empty">Fehler beim Laden der Daten.</div>`;
          }
        } catch (e) {
          document.getElementById('offer-list').innerHTML = `<div class="oc-empty">Daten konnten nicht geladen werden.</div>`;
        }
      }

      function renderList() {
        const listEl = document.getElementById('offer-list');
        const filtered = getProcessedOffers();

        if (!filtered.length) {
          listEl.innerHTML = `<div class="oc-empty">Keine Angebote gefunden.</div>`;
          return;
        }

        if (state.viewMode === 'split') {
          listEl.innerHTML = buildSplitViewHTML(filtered);
        } else if (state.viewMode === 'grid') {
          // 🟢 NEU: Grid View Rendering
          listEl.innerHTML = `<div class="oc-grid-layout">${filtered.map(o => buildGridCardHTML(o)).join('')}</div>`;
        } else {
          listEl.innerHTML = `<div class="oc-list">${filtered.map(o => buildListItemHTML(o)).join('')}</div>`;
        }
      }

      /* OFFER_INDEX_FOLDER_EMPTY_TOTAL_FIX_V1
       * Prices on the offer index must come from active folders only.
       * If all folders are deleted, do NOT fallback to stale o.detail.
       */
      function getActiveOfferFolders(o) {
        return (Array.isArray(o?.folders) ? o.folders : [])
          .filter(f => !f?.deleted_at)
          .sort((a, b) => {
            const ad = new Date(a?.detail?.updated_at || a?.updated_at || a?.created_at || 0).getTime() || 0;
            const bd = new Date(b?.detail?.updated_at || b?.updated_at || b?.created_at || 0).getTime() || 0;
            if (bd !== ad) return bd - ad;
            return Number(b?.id || 0) - Number(a?.id || 0);
          });
      }

      function emptyOfferIndexDetail() {
        return {
          total_net: 0,
          total_gross: 0,
          tax_rate: 19,
          document_status: 'offer',
          source: 'no_active_folders'
        };
      }

      function getOfferIndexDetail(o) {
        const folders = getActiveOfferFolders(o);

        if (!folders.length) {
          return emptyOfferIndexDetail();
        }

        if (o?.index_detail && String(o.index_detail.source || '') !== 'no_active_folders') {
          return o.index_detail;
        }

        return folders.find(f => f?.detail)?.detail || emptyOfferIndexDetail();
      }

      // 🟢 NEU: Grid Card Function
      function buildGridCardHTML(o) {
        const folders = getActiveOfferFolders(o);
        const detail = getOfferIndexDetail(o);

        const rawGross = Number(detail.total_gross || 0);
        const formattedGross = new Intl.NumberFormat('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(rawGross) + ' €';

        const docStatus = (detail.document_status || 'offer').toLowerCase();
        const isDeal = docStatus === 'deal';
        const docBadge = isDeal
          ? `<span class="oc-status-pill green" style="padding:4px 8px; font-size:11px;">Auftrag</span>`
          : `<span class="oc-status-pill blue" style="padding:4px 8px; font-size:11px;">Angebot</span>`;

        return `
                                  <div class="oc-grid-card is-recent" onclick="openOfferFoldersModal(${o.id})">
                                      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                          <div>
                                              <div style="font-size:11px; font-weight:900; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Angebot #${o.id}</div>
                                              <div class="oc-grid-card-price">${formattedGross}</div>
                                          </div>
                                          ${docBadge}
                                      </div>

                                      <div>
                                          <div class="oc-grid-card-customer">${esc(getCustomerName(o))}</div>
                                          <div class="oc-grid-card-object">${esc(getObjectName(o))}</div>
                                          ${buildRecentChangeHTML(o, true)}
                                      </div>

                                      <div onclick="event.stopPropagation()">${buildTeamPanelHTML(o)}</div>

                                      <div style="margin-top:auto; padding-top:14px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                          <div style="display:flex; gap:6px;">
                                              ${getStatusPill(o.status)}
                                          </div>
                                          <div style="font-size:12px; font-weight:800; color:var(--text-muted); display:flex; align-items:center; gap:4px;">
                                              ${iconSvg('folder')} ${folders.length} Ordner
                                          </div>
                                      </div>
                                  </div>
                              `;
      }

      // 🟢 NEU: Folder Modal for Grid View
      async function openOfferFoldersModal(offerId) {
        const offer = state.offers.find(o => Number(o.id) === Number(offerId));
        if (!offer) return;

        openExclusiveModal('offer-folders-modal');
        await fetchOfferTeamIntoState(offer.id);

        const folders = Array.isArray(offer.folders) ? offer.folders : [];
        const hasFinalFolder = folders.some(f => isFinalFolderStatus(f.status));

        document.getElementById('offer-folders-modal-title').innerHTML = `
                                  Ordner für ${esc(getCustomerName(offer))}
                                  <div style="font-size:13px; color:var(--text-muted); font-weight:600; margin-top:4px;">
                                    Angebot #${offer.id} | ${esc(getObjectName(offer))}
                                  </div>
                              `;

        const newFolderBtn = document.getElementById('offer-folders-new-btn');
        if (hasFinalFolder) {
          newFolderBtn.disabled = true;
          newFolderBtn.style.opacity = '0.6';
          newFolderBtn.style.cursor = 'not-allowed';
          newFolderBtn.onclick = showOfferFinalizedMessage;
        } else {
          newFolderBtn.disabled = false;
          newFolderBtn.style.opacity = '1';
          newFolderBtn.style.cursor = 'pointer';
          newFolderBtn.onclick = () => {
            closeOfferFoldersModal();
            openFolderModal(offer.id);
          };
        }

        const listContainer = document.getElementById('offer-folders-modal-list');
        const teamBox = `<div style="padding:20px 20px 0 20px;">${buildTeamPanelHTML(offer)}</div>`;
        if (folders.length) {
          // Re-use the exact same Folder Card HTML as the split/list views, 
          // just force them into a 1-column grid or 2-column grid depending on width.
          listContainer.innerHTML = teamBox + `
                                      <div class="folder-grid" style="grid-template-columns: 1fr; padding: 20px;">
                                          ${folders.map(f => buildFolderCardHTML(offer.id, f)).join('')}
                                      </div>
                                  `;
        } else {
          listContainer.innerHTML = teamBox + `<div class="oc-empty" style="margin:20px;">Keine Ordner angelegt.</div>`;
        }

        document.getElementById('offer-folders-modal').classList.add('open');
      }

      function closeOfferFoldersModal() {
        closeExclusiveModal('offer-folders-modal');
      }

      function buildSplitViewHTML(filtered) {
        if (!state.selectedOfferId || !filtered.find(o => o.id === state.selectedOfferId)) {
          state.selectedOfferId = filtered[0].id;
        }
        const selectedOffer = filtered.find(o => o.id === state.selectedOfferId);

        // Sidebar
        let sidebarHTML = `<div class="oc-sidebar">`;
        sidebarHTML += filtered.map(o => {
          const isActive = o.id === state.selectedOfferId;
          const folderCount = getFolderCount(o);
          const latestUpdate = getLatestUpdateDate(o);
          const avatarsHtml = getFolderCreatorsAvatars(o);

          return `
                                    <div class="oc-sidebar-item ${isActive ? 'active' : ''}" onclick="selectOfferInSplitView(${o.id})">
                                        <div class="oc-ttl" style="font-size:14px; display:flex; justify-content:space-between; align-items:flex-start;">
                                            <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; padding-right:8px;">${esc(getCustomerName(o))}</span>
                                            <span class="oc-tag" style="margin:0; font-size:10px; flex-shrink:0;">#${o.id}</span>
                                        </div>
                                        <div class="oc-subt" style="margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${esc(getProductName(o))}</div>
                                        ${buildRecentChangeHTML(o, true)}

                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                            ${getStatusPill(o.status)}
                                            <div style="font-size:11px; color:var(--text-muted); font-weight:700; display:flex; align-items:center; gap:4px;" title="Letzte Änderung">
                                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                ${esc(latestUpdate)}
                                            </div>
                                        </div>

                                        <div style="display:flex; justify-content:space-between; align-items:center;">
                                            ${avatarsHtml}
                                            <span style="font-size:11px; color:var(--text-muted); font-weight:800;">${folderCount} Ordner</span>
                                        </div>
                                    </div>`;
        }).join('');
        sidebarHTML += `</div>`;

        // Right Detail Pane
        let detailHTML = `<div class="oc-detail-pane">`;
        if (selectedOffer) {
          const folders = Array.isArray(selectedOffer.folders) ? selectedOffer.folders : [];
          const hasFinalFolder = Array.isArray(selectedOffer?.folders) && selectedOffer.folders.some(f => isFinalFolderStatus(f.status));
          detailHTML += `
                                    <div class="oc-detail-header">
                                        <div>
                                            <h2 style="margin:0 0 6px 0; font-size:22px; font-weight:900;">${esc(getCustomerName(selectedOffer))}</h2>
                                            <div style="font-size:14px; color:var(--text-muted); margin-bottom:4px;"><strong>Objekt:</strong> ${esc(getObjectName(selectedOffer))}</div>
                                            <div style="font-size:14px; color:var(--text-muted);"><strong>Produkt:</strong> ${esc(getProductName(selectedOffer))} | <strong>Erstellt von:</strong> ${esc(getCreatorName(selectedOffer))}</div>
                                        </div>
                                        <div class="oc-actions-wrap">
                                            <select class="oc-select" onchange="quickStatus(${selectedOffer.id}, this.value)" style="padding:6px 28px 6px 10px; min-height:36px;">
                                                ${Object.keys(STATUS_MAP).map(k => `<option value="${k}" ${selectedOffer.status === k ? 'selected' : ''}>${STATUS_MAP[k].label}</option>`).join('')}
                                            </select>
                                            <button class="oc-btn-ic" type="button" onclick="openModal(${selectedOffer.id})" title="Bearbeiten">${iconSvg('edit')}</button>
                                            <button class="oc-btn-ic danger" type="button" onclick="deleteOffer(${selectedOffer.id})" title="Löschen">${iconSvg('trash')}</button>
                                        </div>
                                    </div>
                                    <div style="margin-bottom:18px;">${buildRecentChangeHTML(selectedOffer, false)}</div>
                                    <div style="margin-bottom:18px;">${buildTeamPanelHTML(selectedOffer)}</div>
                                    <div class="oc-item-body-top" style="margin-top:0;">
                                        <h4 class="oc-item-body-title" style="font-size:16px;">Ordnerübersicht (${folders.length})</h4>
                                        <button
                                          type="button"
                                          class="oc-btn"
                                          ${hasFinalFolder ? 'disabled style="opacity:.6;cursor:not-allowed;"' : ''}
                                          onclick="${hasFinalFolder ? 'showOfferFinalizedMessage()' : `openFolderModal(${selectedOffer.id})`}"
                                        >
                                          ${iconSvg('folder')} Neuer Ordner
                                        </button>
                                    </div>
                                    <div class="folder-grid">
                                        ${folders.length ? folders.map(f => buildFolderCardHTML(selectedOffer.id, f)).join('') : '<div class="oc-empty" style="padding:30px;">Keine Ordner angelegt.</div>'}
                                    </div>
                                  `;
        }
        detailHTML += `</div>`;

        return `<div class="oc-split-layout">${sidebarHTML}${detailHTML}</div>`;
      }

      function buildListItemHTML(o) {
        const isOpen = !!state.expanded[o.id];
        const folders = Array.isArray(o.folders) ? o.folders : [];
        const hasFinalFolder = folders.some(f => isFinalFolderStatus(f.status));
        return `
                                <div class="oc-item">
                                    <div class="oc-item-header" onclick="toggleOffer(${o.id})">
                                    <div class="oc-ic ${isOpen ? 'is-open' : ''}">${iconSvg('chevron')}</div>
                                    <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Kunde / Objekt</div><div class="oc-main"><div class="oc-ttl"><span class="oc-tag">#${o.id}</span>${esc(getCustomerName(o))}</div><div class="oc-subt">${esc(getObjectName(o))}</div></div></div>
                                    <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Produkt / Service</div><div class="oc-main"><div class="oc-ttl" style="font-size:14px;">${esc(getProductName(o))}</div><div class="oc-subt">${esc(o.service || 'Standard')}</div></div></div>
                                    <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Letzte Änderung</div><div class="oc-main"><div class="oc-ttl" style="font-size:14px;">${esc(getRecentChange(o).title || 'Geändert')}</div><div class="oc-subt">${esc(formatRecentDate(getRecentChange(o)))}</div></div></div>
                                    <div class="oc-cell oc-responsive-col" onclick="event.stopPropagation()"><div class="oc-cell-title">Status</div><div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">${getStatusPill(o.status || 'draft')}</div></div>
                                    <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Ordner</div><div class="oc-main"><div class="oc-ttl" style="font-size:14px;">${folders.length}</div><div class="oc-subt">Verknüpfte Ordner</div></div></div>
                                    <div class="oc-actions-wrap" onclick="event.stopPropagation()">
                                        <select class="oc-select" onchange="quickStatus(${o.id}, this.value)" style="padding:6px 28px 6px 10px; min-height:36px;">
                                        ${Object.keys(STATUS_MAP).map(k => `<option value="${k}" ${o.status === k ? 'selected' : ''}>${STATUS_MAP[k].label}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="oc-actions" onclick="event.stopPropagation()">
                                        <button class="oc-btn-ic" type="button" onclick="openModal(${o.id})" title="Bearbeiten">${iconSvg('edit')}</button>
                                        <button class="oc-btn-ic danger" type="button" onclick="deleteOffer(${o.id})" title="Löschen">${iconSvg('trash')}</button>
                                    </div>
                                    </div>
                                    <div class="oc-item-body ${isOpen ? 'is-open' : ''}">
                                    <div class="oc-item-body-top">
                                        <h4 class="oc-item-body-title">Ordnerübersicht</h4>
                                        <div class="oc-meta-inline">
                                            <span class="oc-mini-badge">Angebot #${o.id}</span><span class="oc-mini-badge">${folders.length} Ordner</span><span class="oc-mini-badge">${esc(STATUS_MAP[o.status]?.label || 'Entwurf')}</span>
                                            <button
                                              type="button"
                                              class="oc-btn"
                                              ${hasFinalFolder ? 'disabled style="opacity:.6;cursor:not-allowed;"' : ''}
                                              onclick="event.stopPropagation(); ${hasFinalFolder ? 'showOfferFinalizedMessage()' : `openFolderModal(${o.id})`}"
                                            >
                                              ${iconSvg('folder')} Ordner hinzufügen
                                            </button>
                                        </div>
                                    </div>
                                    <div style="margin-bottom:14px;">${buildRecentChangeHTML(o, false)}</div>
                                    <div style="margin-bottom:14px;">${buildTeamPanelHTML(o)}</div>
                                    <div class="folder-grid">
                                        ${folders.length ? folders.map(f => buildFolderCardHTML(o.id, f)).join('') : '<div class="oc-empty" style="padding:30px;">Keine Ordner angelegt.</div>'}
                                    </div>
                                    </div>
                                </div>
                              `;
      }

      function buildFolderCardHTML(offerId, f) {
        const locked = isFolderLocked(f);

        // Preis-Daten ermitteln und formatieren
        const parentOffer = state.offers.find(o => Number(o.id) === Number(offerId));
        const cardDetail = f?.detail || emptyOfferIndexDetail();
        const rawGross = Number(cardDetail?.total_gross || 0);
        const formattedGross = new Intl.NumberFormat('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(rawGross) + ' €';

        return `
                              <div
                                  class="folder-card ${locked ? 'is-locked' : ''}"
                                  onclick='tryOpenFolder(${JSON.stringify({
          id: f.id,
          name: f.name || '',
          color: f.color || '#93c21c',
          status: f.status || 'draft'
        })})'
                                >
                                  <div class="folder-icon" style="background:${esc(f.color || '#93c21c')}20; color:${esc(f.color || '#93c21c')};">
                                    ${locked ? iconSvg('lock') : iconSvg('folder')}
                                  </div>

                                  <div class="folder-meta">
                                      <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                                          <div style="min-width:0; flex:1;">

                                              <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; justify-content:space-between;">

                                                <div style="display:flex; align-items:center; gap:8px;">
                                                    <div class="folder-title">${esc(f.name || 'Ordner')}</div>
                                                    ${locked ? `
                                                      <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border-radius:999px; background:#fef2f2; color:#b91c1c; font-size:10px; font-weight:900; text-transform:uppercase; border:1px solid #fecaca;">
                                                        ${iconSvg('lock')}
                                                        Gesperrt
                                                      </span>
                                                    ` : ''}
                                                </div>

                                                <div style="font-weight: 800; font-size: 12px; color: #111827; background: #f3f4f6; padding: 2px 8px; border-radius: 6px; white-space:nowrap;">
                                                    ${formattedGross}
                                                </div>

                                              </div>

                                              <div class="folder-sub">${esc(getFolderStatusLabel(f.status))}</div>
                                              ${f.recent_change ? buildRecentChangeHTML({ recent_change: f.recent_change }, true) : ''}
                                              <div style="margin-top:10px;" onclick="event.stopPropagation()">
                                                ${parentOffer ? buildTeamAvatarsHTML(parentOffer, 3) : '<span class="oc-team-empty">Kein Team</span>'}
                                              </div>
                                          </div>

                                          <div style="display:flex; gap:6px;" onclick="event.stopPropagation()">
                                              <button
                                                  class="oc-btn-ic clone"
                                                  type="button"
                                                  title="${locked ? 'Nicht verfügbar – storniert' : 'Klonen'}"
                                                  onclick='${locked ? `
                                                    openAlertModal({
                                                      title: "Ordner gesperrt",
                                                      message: "Dieser Ordner ist storniert und kann nicht geklont werden.",
                                                      type: "alert",
                                                      confirmText: "OK"
                                                    })
                                                  ` : `
                                                    openCloneFolderModal(${JSON.stringify({
          id: f.id,
          name: f.name || '',
          color: f.color || '#93c21c',
          status: f.status || 'draft'
        })})
                                                  `}'
                                              >
                                                  ${iconSvg('clone')}
                                              </button>

                                              <button
                                                  class="oc-btn-ic"
                                                  type="button"
                                                  title="${locked ? 'Nicht verfügbar – storniert' : 'Bearbeiten'}"
                                                  onclick='${locked ? `
                                                    openAlertModal({
                                                      title: "Ordner gesperrt",
                                                      message: "Dieser Ordner ist storniert und kann nicht bearbeitet werden.",
                                                      type: "alert",
                                                      confirmText: "OK"
                                                    })
                                                  ` : `
                                                    openFolderModal(${offerId}, ${JSON.stringify({
          id: f.id,
          name: f.name || '',
          color: f.color || '#93c21c',
          status: f.status || 'draft'
        })})
                                                  `}'
                                              >
                                                  ${iconSvg('edit')}
                                              </button>

                                              <button
                                                  class="oc-btn-ic danger"
                                                  type="button"
                                                  title="${locked ? 'Nicht verfügbar – storniert' : 'Löschen'}"
                                                  onclick="${locked ? `
                                                    openAlertModal({
                                                      title: 'Ordner gesperrt',
                                                      message: 'Dieser Ordner ist storniert und kann nicht gelöscht werden.',
                                                      type: 'alert',
                                                      confirmText: 'OK'
                                                    })
                                                  ` : `deleteFolder(${offerId}, ${f.id})`}"
                                              >
                                                  ${iconSvg('trash')}
                                              </button>
                                          </div>
                                      </div>

                                      <div class="folder-footer">
                                          <div class="folder-user">
                                              <img src="${getFolderCreatorImage(f)}" alt="${esc(getFolderCreatorName(f))}" class="folder-avatar" onerror="this.src='${DEFAULT_AVATAR}'">
                                              <div class="folder-user-name">${esc(getFolderCreatorName(f))}</div>
                                          </div>
                                          <div class="folder-date">${esc(formatDate(f.created_at))}</div>
                                      </div>
                                  </div>
                              </div>`;
      }

      function toggleOffer(id) { state.expanded[id] = !state.expanded[id]; renderList(); }
      function handleSearch(val) { state.search = (val || '').toLowerCase().trim(); renderList(); }
      function handleFilter(key, val) { state.filters[key] = val || ''; renderList(); }

      function getProcessedOffers() {
        let filtered = [...state.offers];
        filtered = filtered.filter(o => {
          const searchHaystack = [String(o.id || ''), getCustomerName(o), getObjectName(o), getProductName(o), getCreatorName(o), o.service || '', o.status || '', o.alternative?.street || '', o.alternative?.city || ''].join(' ').toLowerCase();
          const matchesSearch = !state.search || searchHaystack.includes(state.search);
          const matchesStatus = !state.filters.status || (o.status || 'draft') === state.filters.status;
          const matchesProduct = !state.filters.product || getProductName(o) === state.filters.product;
          const matchesCreator = !state.filters.creator || getCreatorName(o) === state.filters.creator;
          let matchesFolders = true;
          if (state.filters.hasFolders === 'yes') matchesFolders = getFolderCount(o) > 0;
          if (state.filters.hasFolders === 'no') matchesFolders = getFolderCount(o) === 0;
          return matchesSearch && matchesStatus && matchesProduct && matchesCreator && matchesFolders;
        });

        filtered.sort((a, b) => {
          const dir = state.sort.dir === 'asc' ? 1 : -1;
          let av = '', bv = '';
          switch (state.sort.by) {
            case 'id': return (Number(a.id || 0) - Number(b.id || 0)) * dir;
            case 'customer': return getCustomerName(a).toLowerCase().localeCompare(getCustomerName(b).toLowerCase(), 'de') * dir;
            case 'product': return getProductName(a).toLowerCase().localeCompare(getProductName(b).toLowerCase(), 'de') * dir;
            case 'creator': return getCreatorName(a).toLowerCase().localeCompare(getCreatorName(b).toLowerCase(), 'de') * dir;
            case 'status': return (STATUS_MAP[a.status]?.label || '').localeCompare(STATUS_MAP[b.status]?.label || '', 'de') * dir;
            case 'folders': return (getFolderCount(a) - getFolderCount(b)) * dir;
            case 'created_at': return (new Date(a.created_at || 0).getTime() - new Date(b.created_at || 0).getTime()) * dir;
            case 'latest_change': return (getRecentTimestamp(a) - getRecentTimestamp(b)) * dir;
            default: return (getRecentTimestamp(a) - getRecentTimestamp(b)) * dir;
          }
        });
        return filtered;
      }

      function populateFilters() {
        const productFilter = document.getElementById('product-filter');
        const creatorFilter = document.getElementById('creator-filter');
        const products = [...new Set(state.offers.map(o => getProductName(o)).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'de'));
        const creators = [...new Set(state.offers.map(o => getCreatorName(o)).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'de'));
        productFilter.innerHTML = `<option value="">Alle Produkte</option>` + products.map(p => `<option value="${esc(p)}">${esc(p)}</option>`).join('');
        creatorFilter.innerHTML = `<option value="">Alle Benutzer</option>` + creators.map(c => `<option value="${esc(c)}">${esc(c)}</option>`).join('');
        productFilter.value = state.filters.product || ''; creatorFilter.value = state.filters.creator || '';
      }

      function renderAnalytics() {
        const offers = state.offers || [];
        const countBy = (status) => offers.filter(o => (o.status || 'draft') === status).length;
        document.getElementById('stat-total').textContent = offers.length;
        document.getElementById('stat-draft').textContent = countBy('draft');
        document.getElementById('stat-sent').textContent = countBy('sent');
        document.getElementById('stat-negotiation').textContent = countBy('negotiation');
        document.getElementById('stat-final').textContent = countBy('final');
        document.getElementById('stat-cancel').textContent = countBy('cancel');
      }

      function handleSortChange() { state.sort.by = document.getElementById('sort-by').value; state.sort.dir = 'desc'; syncSortMarks(); renderList(); }
      function toggleColumnSort(column) {
        if (state.sort.by === column) state.sort.dir = state.sort.dir === 'asc' ? 'desc' : 'asc';
        else { state.sort.by = column; state.sort.dir = column === 'id' || column === 'created_at' || column === 'folders' ? 'desc' : 'asc'; }
        document.getElementById('sort-by').value = state.sort.by;
        syncSortMarks(); renderList();
      }
      function syncSortMarks() {
        document.querySelectorAll('.oc-sort-mark').forEach(el => { el.textContent = '↕'; el.classList.remove('active'); });
        const active = document.getElementById(`sort-mark-${state.sort.by}`);
        if (active) { active.textContent = state.sort.dir === 'asc' ? '↑' : '↓'; active.classList.add('active'); }
      }
      function resetFilters() {
        state.search = ''; state.filters = { status: '', product: '', creator: '', hasFolders: '' }; state.sort = { by: 'id', dir: 'desc' };
        document.getElementById('search-input').value = ''; document.getElementById('status-filter').value = '';
        document.getElementById('product-filter').value = ''; document.getElementById('creator-filter').value = '';
        document.getElementById('folder-filter').value = ''; document.getElementById('sort-by').value = 'id';
        syncSortMarks(); renderList();
      }

      /* MODALS & ALERTS */
      function toast(kind, title, msg) {
        const wrap = document.getElementById('toast-wrap');
        if (!wrap) return;
        const icons = {
          ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
          warn: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>`,
          bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
        };
        const el = document.createElement('div'); el.className = 'oc-toast';
        el.innerHTML = `<div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div><div style="flex:1;"><p class="oc-toast-ttl">${esc(title)}</p><p class="oc-toast-msg">${esc(msg)}</p></div><button class="oc-toast-x" onclick="this.parentElement.remove()"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>`;
        wrap.appendChild(el); setTimeout(() => { try { el.remove(); } catch (e) { } }, 4000);
      }

      function openCloneFolderModal(folder) {
        if (!folder) return;

        openExclusiveModal('clone-folder-modal');

        document.getElementById('clone-folder-id').value = folder.id || '';
        document.getElementById('clone-folder-name').value = (folder.name || 'Ordner') + ' - Kopie';
        document.getElementById('clone-folder-color').value = folder.color || '#93c21c';
        document.getElementById('clone-folder-status').value = folder.status || 'draft';
        document.getElementById('clone-folder-everything').checked = true;

        document.getElementById('clone-folder-modal').classList.add('open');
      }

      function closeCloneFolderModal() {
        closeExclusiveModal('clone-folder-modal');
      }

      async function submitCloneFolderForm(e) {
        e.preventDefault();

        const folderId = document.getElementById('clone-folder-id').value;
        const payload = {
          name: document.getElementById('clone-folder-name').value.trim(),
          color: document.getElementById('clone-folder-color').value,
          status: document.getElementById('clone-folder-status').value,
          clone_everything: document.getElementById('clone-folder-everything').checked ? 1 : 0,
        };

        if (!payload.name) {
          return openAlertModal({
            title: 'Pflichtfeld fehlt',
            message: 'Bitte einen neuen Ordnernamen eingeben.',
            type: 'alert'
          });
        }

        const submitBtn = document.getElementById('clone-folder-submit-btn');
        submitBtn.disabled = true;

        try {
          const res = await fetch(`${ENDPOINTS.cloneFolderBase}/${folderId}/clone`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify(payload)
          });

          const text = await res.text();
          let data = {};

          try {
            data = JSON.parse(text);
          } catch (err) {
            throw new Error('Der Server hat keine gültige JSON-Antwort zurückgegeben.');
          }

          if (!res.ok || !data.success) {
            throw new Error(data.message || 'Ordner konnte nicht geklont werden.');
          }

          closeCloneFolderModal();
          await loadData();
          renderList();
          toast('ok', 'Geklont', data.message || 'Ordner wurde erfolgreich geklont.');
        } catch (err) {
          openAlertModal({
            title: 'Fehler',
            message: esc(err.message),
            type: 'alert'
          });
        } finally {
          submitBtn.disabled = false;
        }
      }

      function openAlertModal({ title = 'Hinweis', message = '', type = 'alert', confirmText = 'OK', cancelText = 'Abbrechen' }) {
        const modal = document.getElementById('alert-modal'); const actionsEl = document.getElementById('alert-modal-actions');
        if (!modal) return Promise.resolve(false);
        document.getElementById('alert-modal-title').textContent = title;
        document.getElementById('alert-modal-message').innerHTML = message;
        if (type === 'confirm') { actionsEl.innerHTML = `<button type="button" class="oc-btn-soft" onclick="resolveAlertModal(false)">${cancelText}</button><button type="button" class="oc-btn danger" onclick="resolveAlertModal(true)">${confirmText}</button>`; }
        else { actionsEl.innerHTML = `<button type="button" class="oc-btn" onclick="resolveAlertModal(true)">${confirmText}</button>`; }
        modal.classList.add('open');
        document.body.classList.add('oc-modal-open');
        return new Promise(resolve => { alertModalResolver = resolve; });
      }
      function resolveAlertModal(value) {
        document.getElementById('alert-modal')?.classList.remove('open');
        const hasOpenModal = MODAL_IDS.some(modalId => document.getElementById(modalId)?.classList.contains('open'));
        document.body.classList.toggle('oc-modal-open', hasOpenModal);
        if (typeof alertModalResolver === 'function') alertModalResolver(value);
        alertModalResolver = null;
      }
      function closeAlertModal() { resolveAlertModal(false); }
      function closeDuplicateOfferModal() { closeExclusiveModal('duplicate-offer-modal'); }

      function openDuplicateOfferModal(existingOffer) {
        openExclusiveModal('duplicate-offer-modal');
        const content = document.getElementById('duplicate-offer-content'); const openBtn = document.getElementById('duplicate-offer-open-btn');
        if (!existingOffer) {
          content.innerHTML = `<div class="oc-empty" style="padding:20px;">Kein bestehendes Angebot gefunden.</div>`; openBtn.onclick = closeDuplicateOfferModal;
          document.getElementById('duplicate-offer-modal').classList.add('open'); return;
        }
        content.innerHTML = `<div style="border:1px solid var(--border); border-radius:14px; padding:16px; background:#fafafa;">
                              <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;"><span class="oc-mini-badge">Angebot #${existingOffer.id}</span><span class="oc-mini-badge">${esc(STATUS_MAP[existingOffer.status]?.label || 'Entwurf')}</span><span class="oc-mini-badge">${getFolderCount(existingOffer)} Ordner</span></div>
                              <div style="display:grid; gap:10px;"><div><strong>Kunde:</strong> ${esc(getCustomerName(existingOffer))}</div><div><strong>Objekt:</strong> ${esc(getObjectName(existingOffer))}</div><div><strong>Produkt:</strong> ${esc(getProductName(existingOffer))}</div><div><strong>Erstellt von:</strong> ${esc(getCreatorName(existingOffer))}</div></div>
                            </div>`;
        openBtn.onclick = function () {
          closeDuplicateOfferModal();
          if (state.viewMode === 'split') { selectOfferInSplitView(existingOffer.id); }
          else { state.expanded[existingOffer.id] = true; renderList(); const row = document.querySelector(`[onclick="toggleOffer(${existingOffer.id})"]`); if (row) row.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        };
        document.getElementById('duplicate-offer-modal').classList.add('open');
      }

      function resetCustomerObjectSelect() {
        const $customerObject = $('#inp-customer-object');
        if ($customerObject.length && $customerObject.data('select2')) {
          $customerObject.select2('close');
          $customerObject.val(null).trigger('change.select2');
        } else {
          $customerObject.val(null);
        }
      }

      function initCustomerObjectSelect2() {
        const $customerObject = $('#inp-customer-object');
        const $host = $('#customer-object-select2-host');

        if (!$customerObject.length || !$host.length || typeof $.fn.select2 !== 'function') {
          return;
        }

        if ($customerObject.data('select2')) {
          $customerObject.select2('destroy');
        }

        $customerObject.select2({
          dropdownParent: $host,
          width: '100%',
          dropdownAutoWidth: false,
          placeholder: 'Kunde oder Objekt suchen...',
          allowClear: true,
          minimumInputLength: 1,
          ajax: {
            url: ENDPOINTS.searchCustomerObjects,
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
              return { q: params.term || '' };
            },
            processResults: function (data) {
              return { results: data.results || data.data?.results || [] };
            }
          }
        });

        $customerObject.off('select2:select.offerCustomer select2:clear.offerCustomer');

        $customerObject.on('select2:select.offerCustomer', function (e) {
          const data = e.params.data || {};
          document.getElementById('inp-customer').value = data.customer_id || '';
          document.getElementById('inp-alternative').value = data.alternative_id || '';
          fetchProducts(data.customer_id, data.alternative_id);
        });

        $customerObject.on('select2:clear.offerCustomer', function () {
          document.getElementById('inp-customer').value = '';
          document.getElementById('inp-alternative').value = '';
          $('#inp-product').html('<option value="">Bitte zuerst Kunde & Objekt wählen</option>').prop('disabled', true);
        });
      }

      function focusCustomerObjectSelectWhenReady() {
        setTimeout(function () {
          const $customerObject = $('#inp-customer-object');
          if ($customerObject.data('select2')) {
            $customerObject.select2('close');
          }
        }, 50);
      }

      function openModal(id = null) {
        openExclusiveModal('crud-modal');
        const modal = document.getElementById('crud-modal'); const form = document.getElementById('crud-form');
        initCustomerObjectSelect2();
        form.reset(); resetCustomerObjectSelect(); $('#inp-product').html('<option value="">Bitte zuerst Kunde & Objekt wählen</option>').prop('disabled', true);
        document.getElementById('inp-customer').value = ''; document.getElementById('inp-alternative').value = '';
        if (id) {
          const offer = state.offers.find(x => Number(x.id) === Number(id)); if (!offer) return;
          document.getElementById('modal-title').textContent = 'Angebot bearbeiten';
          document.getElementById('inp-id').value = offer.id; document.getElementById('inp-service').value = offer.service || '';
          document.getElementById('btn-submit').textContent = 'Änderungen speichern';
          document.getElementById('folder-creation-section').style.display = 'none'; document.getElementById('inp-folder-name').required = false;
          document.getElementById('inp-customer').value = offer.customer_id || ''; document.getElementById('inp-alternative').value = offer.alternative_id || '';
          const newOption = new Option(`${getCustomerName(offer)} | Objekt: ${getObjectName(offer)}`, offer.alternative_id || `lead_${offer.customer_id}`, true, true);
          $('#inp-customer-object').append(newOption).trigger('change');
          fetchProducts(offer.customer_id, offer.alternative_id, offer.product_id);
        } else {
          document.getElementById('modal-title').textContent = 'Neues Angebot & Ordner'; document.getElementById('inp-id').value = ''; document.getElementById('btn-submit').textContent = 'Erstellen';
          document.getElementById('folder-creation-section').style.display = 'block'; document.getElementById('inp-folder-name').required = true; document.getElementById('inp-folder-color').value = '#93c21c';
        }
        modal.classList.add('open');
        focusCustomerObjectSelectWhenReady();
      }
      function closeModal() {
        try {
          const $customerObject = $('#inp-customer-object');
          if ($customerObject.data('select2')) $customerObject.select2('close');
        } catch (e) { }
        closeExclusiveModal('crud-modal');
      }

      function fetchProducts(customerId, alternativeId, selectedProductId = null) {
        const prodSel = $('#inp-product'); const currentOfferId = document.getElementById('inp-id').value || '';
        prodSel.prop('disabled', true).html('<option>Lade...</option>');
        fetch(`${ENDPOINTS.getProducts}?customer_id=${encodeURIComponent(customerId || '')}&alternative_id=${encodeURIComponent(alternativeId || '')}&offer_id=${encodeURIComponent(currentOfferId)}`)
          .then(r => r.json()).then(products => {
            let html = '<option value="">Produkt wählen...</option>';
            (products || []).forEach(p => {
              const sel = (selectedProductId && Number(p.id) === Number(selectedProductId)) ? 'selected' : '';
              const duplicateText = p.has_existing_offer ? ` — Angebot existiert #${p.existing_offer_id}` : '';
              html += `<option value="${p.id}" ${sel} data-has-existing-offer="${p.has_existing_offer ? 1 : 0}" data-existing-offer-id="${p.existing_offer_id || ''}">${esc(p.text + duplicateText)}</option>`;
            });
            prodSel.html(html).prop('disabled', false);
          }).catch(() => { prodSel.html('<option value="">Fehler beim Laden</option>').prop('disabled', false); });
      }

      async function submitForm(e) {
        e.preventDefault();
        const id = document.getElementById('inp-id').value; const isUpdate = !!id;
        const payload = { customer_id: document.getElementById('inp-customer').value, alternative_id: document.getElementById('inp-alternative').value, product_id: document.getElementById('inp-product').value, service: document.getElementById('inp-service').value };
        if (!isUpdate) { payload.folder_name = document.getElementById('inp-folder-name').value; payload.folder_color = document.getElementById('inp-folder-color').value; }
        if (!payload.customer_id || !payload.alternative_id || !payload.product_id) return openAlertModal({ title: 'Pflichtfelder fehlen', message: 'Bitte Kunde, Objekt und Produkt auswählen.', type: 'alert' });
        const submitBtn = document.getElementById('btn-submit'); submitBtn.disabled = true;
        try {
          const existingSameOffer = state.offers.find(o =>
            Number(o.customer_id || 0) === Number(payload.customer_id || 0) &&
            Number(o.alternative_id || 0) === Number(payload.alternative_id || 0) &&
            Number(o.product_id || 0) === Number(payload.product_id || 0)
          );

          if (!isUpdate && existingSameOffer && Array.isArray(existingSameOffer.folders)) {
            const hasFinalFolder = existingSameOffer.folders.some(folder => isFinalFolderStatus(folder.status));
            if (hasFinalFolder) {
              await showOfferFinalizedMessage();
              submitBtn.disabled = false;
              return;
            }
          }
          const res = await fetch(isUpdate ? `${ENDPOINTS.updateBase}/${id}` : ENDPOINTS.store, { method: isUpdate ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify(payload) });
          const text = await res.text(); let data = {}; try { data = JSON.parse(text); } catch (err) { throw new Error('Der Server hat keine gültige JSON-Antwort zurückgegeben.'); }
          if (!res.ok || !data.success) {
            if (data.code === 'OFFER_ALREADY_EXISTS') { closeModal(); openDuplicateOfferModal(data.existing_offer || null); return; }
            throw new Error(data.message || 'Speichern fehlgeschlagen.');
          }
          if (isUpdate) { const idx = state.offers.findIndex(x => Number(x.id) === Number(id)); if (idx !== -1) state.offers[idx] = data.data; toast('ok', 'Aktualisiert', 'Angebot gespeichert.'); }
          else { state.offers.unshift(data.data); toast('ok', 'Erstellt', 'Angebot & Ordner angelegt.'); state.selectedOfferId = data.data.id; }
          renderAnalytics(); populateFilters(); renderList(); closeModal();
        } catch (err) { openAlertModal({ title: 'Fehler', message: esc(err.message), type: 'alert' }); }
        finally { submitBtn.disabled = false; }
      }

      function openFolderModal(offerId, folder = null) {
        const isEdit = !!folder?.id;

        if (!isEdit && offerHasFinalFolder(offerId)) {
          showOfferFinalizedMessage();
          return;
        }

        openExclusiveModal('folder-modal');

        document.getElementById('folder-offer-id').value = offerId || '';
        document.getElementById('folder-id').value = folder?.id || '';
        document.getElementById('folder-name').value = folder?.name || '';
        document.getElementById('folder-color').value = folder?.color || '#93c21c';
        document.getElementById('folder-status').value = folder?.status || 'draft';
        document.getElementById('folder-modal-title').textContent = folder?.id ? 'Ordner bearbeiten' : 'Ordner erstellen';
        document.getElementById('folder-submit-btn').textContent = folder?.id ? 'Aktualisieren' : 'Erstellen';
        document.getElementById('folder-modal').classList.add('open');
      }
      function closeFolderModal() { closeExclusiveModal('folder-modal'); }

      async function submitFolderForm(e) {
        e.preventDefault();

        const offerId = document.getElementById('folder-offer-id').value;
        const folderId = document.getElementById('folder-id').value;
        const isEdit = !!folderId;

        const payload = {
          name: document.getElementById('folder-name').value.trim(),
          color: document.getElementById('folder-color').value,
          status: document.getElementById('folder-status').value
        };

        if (!payload.name) {
          return openAlertModal({
            title: 'Pflichtfeld fehlt',
            message: 'Bitte Ordnernamen eingeben.',
            type: 'alert'
          });
        }

        const submitBtn = document.getElementById('folder-submit-btn');
        submitBtn.disabled = true;

        if (!isEdit && offerHasFinalFolder(offerId)) {
          submitBtn.disabled = false;
          await showOfferFinalizedMessage();
          return;
        }

        try {
          const res = await fetch(
            isEdit
              ? `${ENDPOINTS.folderUpdateBase}/${folderId}`
              : `${ENDPOINTS.folderStoreBase}/${offerId}/folders`,
            {
              method: isEdit ? 'PUT' : 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF
              },
              body: JSON.stringify(payload)
            }
          );

          const text = await res.text();
          let data = JSON.parse(text);

          if (!res.ok || !data.success) throw new Error(data.message || 'Ordner konnte nicht gespeichert werden.');

          closeFolderModal();
          await loadData();
          renderList();
          toast('ok', isEdit ? 'Aktualisiert' : 'Erstellt', data.message || 'Erfolgreich.');
        } catch (e) {
          openAlertModal({ title: 'Fehler', message: esc(e.message), type: 'alert' });
        } finally {
          submitBtn.disabled = false;
        }
      }

      async function deleteFolder(offerId, folderId) {
        const confirmed = await openAlertModal({ title: 'Ordner löschen', message: 'Wirklich löschen?', type: 'confirm', confirmText: 'Ja, löschen' });
        if (!confirmed) return;
        try {
          const res = await fetch(`${ENDPOINTS.folderUpdateBase}/${folderId}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF } });
          const data = await res.json();
          if (!res.ok || !data.success) throw new Error(data.message || 'Löschen fehlgeschlagen.');
          await loadData(); renderList(); toast('ok', 'Gelöscht', data.message || 'Ordner wurde entfernt.');
        } catch (e) { openAlertModal({ title: 'Fehler', message: esc(e.message), type: 'alert' }); }
      }

      async function deleteOffer(id) {
        const confirmed = await openAlertModal({ title: 'Angebot löschen', message: 'Möchten Sie dieses Angebot wirklich löschen?', type: 'confirm', confirmText: 'Ja, löschen' });
        if (!confirmed) return;
        try {
          const res = await fetch(`${ENDPOINTS.updateBase}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
          const data = await res.json();
          if (data.success) { state.offers = state.offers.filter(x => Number(x.id) !== Number(id)); renderAnalytics(); populateFilters(); renderList(); toast('ok', 'Gelöscht', 'Angebot wurde entfernt.'); }
          else { openAlertModal({ title: 'Fehler', message: esc(data.message), type: 'alert' }); }
        } catch (e) { openAlertModal({ title: 'Netzwerkfehler', message: 'Konnte Angebot nicht löschen.', type: 'alert' }); }
      }

      async function quickStatus(id, newStatus) {
        try {
          const res = await fetch(`${ENDPOINTS.updateBase}/${id}/status`, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify({ status: newStatus }) });
          const data = await res.json();
          if (data.success) { const offer = state.offers.find(x => Number(x.id) === Number(id)); if (offer) offer.status = newStatus; renderAnalytics(); renderList(); toast('ok', 'Status aktualisiert', 'Angebot aktualisiert.'); }
          else { openAlertModal({ title: 'Fehler', message: esc(data.message), type: 'alert' }); }
        } catch (e) { openAlertModal({ title: 'Netzwerkfehler', message: 'Netzwerkfehler beim Statuswechsel.', type: 'alert' }); }
      }



      function refreshOfferTeamInState(offerId, teams) {
        const offer = state.offers.find(x => Number(x.id) === Number(offerId));
        if (!offer) return;

        const normalizedTeams = Array.isArray(teams) ? teams : [];

        offer.teams = normalizedTeams;
        if (!offer.lead_product_list) offer.lead_product_list = {};
        offer.lead_product_list.teams = normalizedTeams;
        if (offer.leadProductList) offer.leadProductList.teams = normalizedTeams;
      }

      function ensureTeamSelectOptions(offer) {
        const select = document.getElementById('team-employee-select');
        if (!select) return;

        const known = getKnownEmployeeOptions();
        const existing = getOfferTeams(offer).map(t => ({
          id: getTeamEmployeeId(t),
          text: getTeamEmployeeName(t),
          image: t.employee_image || t.image || t.employee?.image || null,
        })).filter(x => x.id);

        [...known, ...existing].forEach(emp => {
          if (!select.querySelector(`option[value="${emp.id}"]`)) {
            const opt = new Option(emp.text, emp.id, false, false);
            opt.dataset.image = emp.image || '';
            select.appendChild(opt);
          }
        });
      }

      function renderCurrentTeamList(offer) {
        const target = document.getElementById('team-current-list');
        if (!target) return;
        const teams = getOfferTeams(offer);
        if (!teams.length) {
          target.innerHTML = `<span class="oc-team-empty">Kein Team zugeordnet.</span>`;
          return;
        }
        target.innerHTML = teams.map(t => `
                              <span class="oc-team-chip">
                                <img class="oc-team-avatar" src="${esc(getEmployeeImageUrl(t.employee || t))}" onerror="this.src='${DEFAULT_AVATAR}'" alt="">
                                ${esc(getTeamEmployeeName(t))}
                                <small>${esc(t.stage || 'offer')}</small>
                              </span>
                            `).join('');
      }

      function initTeamEmployeeSelect() {
        const $select = $('#team-employee-select');
        if (!$select.length) return;

        if ($select.data('select2')) {
          return;
        }

        $select.select2({
          dropdownParent: $('#offer-team-modal'),
          width: '100%',
          placeholder: 'Mitarbeiter suchen oder auswählen...',
          allowClear: true,
          closeOnSelect: false,
          minimumInputLength: 0,
          ajax: {
            url: ENDPOINTS.employeeSearch,
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
              return {
                q: params.term || '',
                search: params.term || '',
                page: params.page || 1
              };
            },
            processResults: function (data) {
              const rows = data.results || data.data || data.employees || [];
              return {
                results: rows.map(item => ({
                  id: item.id || item.employee_id,
                  text: item.text || item.full_name || item.name_full || [item.name, item.lastname].filter(Boolean).join(' ') || [item.firstname, item.lastname].filter(Boolean).join(' ') || `Mitarbeiter #${item.id || item.employee_id}`,
                  image: item.image || item.employee_image || null
                })).filter(item => item.id)
              };
            }
          },
          templateResult: function (item) {
            if (!item.id) return item.text;
            const image = item.image ? `${EMPLOYEE_IMAGE_BASE}/${item.image}` : DEFAULT_AVATAR;
            return $(`<span style="display:flex;align-items:center;gap:8px;"><img src="${image}" onerror="this.src='${DEFAULT_AVATAR}'" style="width:24px;height:24px;border-radius:999px;object-fit:cover;"> <span>${esc(item.text)}</span></span>`);
          },
          templateSelection: function (item) {
            return item.text || item.id;
          }
        });
      }

      async function openTeamModal(offerId) {
        const offer = state.offers.find(o => Number(o.id) === Number(offerId));
        if (!offer) return;

        openExclusiveModal('offer-team-modal');
        await fetchOfferTeamIntoState(offer.id);

        document.getElementById('team-offer-id').value = offer.id;
        document.getElementById('offer-team-modal-title').textContent = `Team bearbeiten – Angebot #${offer.id}`;
        document.getElementById('team-stage').value = String(offer.detail?.document_status || 'offer').toLowerCase();

        const modal = document.getElementById('offer-team-modal');
        modal.classList.add('open');

        initTeamEmployeeSelect();
        ensureTeamSelectOptions(offer);

        const ids = getOfferTeams(offer).map(t => String(getTeamEmployeeId(t))).filter(Boolean);
        $('#team-employee-select').val(ids).trigger('change.select2');
        renderCurrentTeamList(offer);
      }

      function closeTeamModal() {
        closeExclusiveModal('offer-team-modal');
      }

      async function submitTeamForm(e) {
        e.preventDefault();

        const offerId = document.getElementById('team-offer-id').value;
        const employeeIds = ($('#team-employee-select').val() || []).map(v => Number(v)).filter(Boolean);
        const stage = document.getElementById('team-stage').value || 'offer';

        if (!offerId) return;
        if (!employeeIds.length) {
          return openAlertModal({ title: 'Team fehlt', message: 'Bitte mindestens einen Mitarbeiter auswählen.', type: 'alert' });
        }

        const btn = document.getElementById('team-submit-btn');
        btn.disabled = true;

        try {
          const res = await fetch(`${ENDPOINTS.teamBase}/${offerId}/team`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ employee_ids: employeeIds, stage })
          });

          const text = await res.text();
          let data = {};
          try { data = JSON.parse(text); } catch { throw new Error('Der Server hat keine gültige JSON-Antwort zurückgegeben.'); }

          if (!res.ok || !data.success) throw new Error(data.message || 'Team konnte nicht gespeichert werden.');

          refreshOfferTeamInState(offerId, data.teams || []);
          closeTeamModal();
          await loadData();
          renderList();
          toast('ok', 'Team gespeichert', data.message || 'Das Angebotsteam wurde aktualisiert.');
        } catch (err) {
          openAlertModal({ title: 'Fehler', message: esc(err.message), type: 'alert' });
        } finally {
          btn.disabled = false;
        }
      }

      function openFolderPreview(folderId) { window.location.href = `/admin/offers/folders/${folderId}`; }

      $(document).ready(function () {
        initCustomerObjectSelect2();
        $('#inp-product').on('change', function () { const sel = this.options[this.selectedIndex]; if (!sel) return; if (String(sel.dataset.hasExistingOffer || '0') === '1' && sel.dataset.existingOfferId) { const existing = state.offers.find(o => Number(o.id) === Number(sel.dataset.existingOfferId)); if (existing) openDuplicateOfferModal(existing); } });



        initTeamEmployeeSelect();

        syncSortMarks(); loadData();
      });
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
        label: 'Kundenliste',
        url: "{{ url('new_lead_view') }}",
      },
      {
        label: 'Angebotliste',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush