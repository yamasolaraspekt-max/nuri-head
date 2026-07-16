@extends('admin.layouts.app')

@section('title', 'Wartungs-Checklisten')

@php
    use Illuminate\Pagination\AbstractPaginator;
    use Illuminate\Pagination\LengthAwarePaginator;

    $currentScope = $scope ?? 'active';

    $isPaginator = $maintenanceChecklists instanceof LengthAwarePaginator || $maintenanceChecklists instanceof AbstractPaginator;
    $checklistItems = $isPaginator ? collect($maintenanceChecklists->items()) : collect($maintenanceChecklists);

    $totalCount = $isPaginator ? $maintenanceChecklists->total() : $checklistItems->count();
    $activeCount = (int) $checklistItems->where('status', 'active')->count();
    $draftCount = (int) $checklistItems->where('status', 'draft')->count();
    $archivedCount = (int) $checklistItems->where('status', 'archived')->count();
    $fieldCount = (int) $checklistItems->sum(fn($item) => $item->items?->count() ?? 0);

    $scopeLabels = [
        'active' => 'Aktiv / Entwurf',
        'archived' => 'Archiv',
        'deleted' => 'Papierkorb',
    ];

    $statusLabels = [
        'active' => 'Aktiv',
        'draft' => 'Entwurf',
        'archived' => 'Archiviert',
    ];
@endphp

@section('style')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

    <style>
      :root {
        --app-bg:#f3f4f6;
        --card-bg:#ffffff;
        --text-main:#1f2937;
        --text-muted:#6b7280;
        --border:#e5e7eb;
        --primary:var(--sa-accent);
        --primary-hover:var(--sa-accent-hover);
        --primary-light:var(--sa-accent-light);
        --blue:#74b2d4;
        --blue-light:#eff6ff;
        --success:#10b981;
        --success-light:#ecfdf5;
        --warning:#f59e0b;
        --warning-light:#fffbeb;
        --danger:#ef4444;
        --danger-hover:#dc2626;
        --danger-light:#fef2f2;
        --gray:#6b7280;
        --gray-light:#f3f4f6;
        --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
        --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
        --radius:14px;
        --transition:all .2s ease-in-out;
      }

      .mcl-wrap {
        font-family: Inter, system-ui, -apple-system, sans-serif;
        color:var(--text-main);
      }

      .mcl-page-header {
        margin-bottom:18px;
      }

      .mcl-titlebar {
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:12px;
        margin-bottom:16px;
        flex-wrap:wrap;
      }

      .mcl-page-title {
        font-size:26px;
        font-weight:800;
        letter-spacing:-.025em;
        color:#111827;
        text-transform:uppercase;
      }

      .mcl-page-subtitle {
        font-size:14px;
        color:var(--text-muted);
        margin-top:4px;
      }

      .mcl-breadcrumb {
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:8px;
        margin-top:10px;
        font-size:13px;
        color:var(--text-muted);
      }

      .mcl-breadcrumb a {
        color:var(--text-muted);
        text-decoration:none;
        font-weight:700;
      }

      .mcl-breadcrumb a:hover {
        color:var(--text-main);
      }

      .mcl-breadcrumb span.current {
        color:#111827;
        font-weight:800;
      }

      .mcl-inline-actions {
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        align-items:center;
      }

      .mcl-btn {
        background:var(--primary);
        color:#fff;
        border:none;
        padding:10px 16px;
        border-radius:10px;
        font-weight:900;
        cursor:pointer;
        transition:var(--transition);
        display:inline-flex;
        align-items:center;
        gap:8px;
        text-decoration:none;
      }

      .mcl-btn:hover {
        background:var(--primary-hover);
        color:#fff;
        text-decoration:none;
      }

      .mcl-btn-soft,
      .mcl-btn-outline {
        background:#fff;
        color:var(--text-main);
        border:1px solid var(--border);
        padding:10px 14px;
        border-radius:10px;
        font-weight:800;
        cursor:pointer;
        transition:var(--transition);
        text-decoration:none;
      }

      .mcl-btn-soft:hover,
      .mcl-btn-outline:hover {
        background:#f9fafb;
        color:var(--text-main);
        text-decoration:none;
      }

      .mcl-btn-ghost {
        background:transparent;
        color:var(--text-main);
        border:1px solid transparent;
      }

      .mcl-btn-sm {
        padding:7px 11px;
        font-size:12px;
        border-radius:9px;
      }

      .mcl-btn-ic {
        width:36px;
        height:36px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        color:var(--text-muted);
        cursor:pointer;
        transition:var(--transition);
        text-decoration:none;
      }

      .mcl-btn-ic:hover {
        background:#f9fafb;
        color:var(--text-main);
        border-color:#d1d5db;
        text-decoration:none;
      }

      .mcl-btn-ic.primary {
        color:var(--primary);
        border-color:var(--primary-light);
        background:var(--primary-light);
      }

      .mcl-btn-ic.warning {
        color:#d97706;
        border-color:#fde7b0;
        background:#fffbeb;
      }

      .mcl-btn-ic.success {
        color:var(--success);
        border-color:#c7f2df;
        background:var(--success-light);
      }

      .mcl-btn-ic.danger {
        color:var(--danger);
        border-color:rgba(239,68,68,.18);
        background:var(--danger-light);
      }

      .mcl-view-toggle {
        display:inline-flex;
        background:#fff;
        border:1px solid var(--border);
        border-radius:12px;
        padding:4px;
        gap:4px;
        box-shadow:var(--shadow-sm);
      }

      .mcl-view-toggle button {
        border:none;
        background:transparent;
        color:var(--text-muted);
        padding:9px 14px;
        border-radius:9px;
        font-size:13px;
        font-weight:800;
        display:inline-flex;
        align-items:center;
        gap:8px;
        cursor:pointer;
        transition:var(--transition);
      }

      .mcl-view-toggle button.active,
      .mcl-view-toggle button.is-active {
        background:var(--primary-light);
        color:#365314;
      }

      .mcl-analytics {
        display:grid;
        grid-template-columns:repeat(5, minmax(0,1fr));
        gap:14px;
        margin-bottom:18px;
      }

      @media(max-width:1300px) {
        .mcl-analytics {
          grid-template-columns:repeat(3, minmax(0,1fr));
        }
      }

      @media(max-width:900px) {
        .mcl-analytics {
          grid-template-columns:repeat(2, minmax(0,1fr));
        }
      }

      @media(max-width:700px) {
        .mcl-analytics {
          grid-template-columns:1fr;
        }
      }

      .mcl-stat {
        background:var(--card-bg);
        border:1px solid var(--border);
        border-radius:16px;
        padding:16px;
        box-shadow:var(--shadow-sm);
        display:flex;
        align-items:center;
        gap:12px;
        min-height:92px;
      }

      .mcl-stat-icon {
        width:48px;
        height:48px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
      }

      .mcl-stat-icon.total { background:var(--blue-light); color:var(--blue); }
      .mcl-stat-icon.active { background:var(--success-light); color:var(--success); }
      .mcl-stat-icon.draft { background:var(--warning-light); color:#d97706; }
      .mcl-stat-icon.archived { background:var(--gray-light); color:var(--gray); }
      .mcl-stat-icon.fields { background:var(--primary-light); color:var(--primary); }

      .mcl-stat-meta { min-width:0; }

      .mcl-stat-label {
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        letter-spacing:.06em;
      }

      .mcl-stat-value {
        font-size:24px;
        font-weight:900;
        color:#111827;
        line-height:1.1;
        margin-top:4px;
      }

      .mcl-stat-sub {
        font-size:12px;
        color:var(--text-muted);
        margin-top:4px;
      }

      .mcl-toolbar {
        background:var(--card-bg);
        border:1px solid var(--border);
        border-radius:var(--radius);
        padding:14px 16px;
        display:flex;
        flex-wrap:wrap;
        gap:14px;
        align-items:flex-end;
        justify-content:space-between;
        margin-bottom:16px;
        box-shadow:var(--shadow-sm);
      }

      .mcl-toolbar-left,
      .mcl-toolbar-right {
        display:flex;
        align-items:flex-end;
        gap:12px;
        flex-wrap:wrap;
      }

      .mcl-toolbar-left {
        flex:1;
      }

      .mcl-filter-block {
        display:flex;
        flex-direction:column;
        gap:6px;
        min-width:170px;
      }

      .mcl-filter-block.search {
        flex:1;
        min-width:280px;
      }

      .mcl-filter-label {
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        letter-spacing:.06em;
      }

      .mcl-input {
        background:#f9fafb;
        border:1px solid var(--border);
        border-radius:8px;
        padding:10px 12px 10px 36px;
        font-size:14px;
        outline:none;
        transition:var(--transition);
        min-width:240px;
        width:100%;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
        background-repeat:no-repeat;
        background-position:10px center;
        background-size:16px;
      }

      .mcl-input:focus {
        background:#fff;
        border-color:var(--primary);
        box-shadow:0 0 0 3px var(--primary-light);
      }

      .mcl-select {
        width:100%;
        padding:10px 12px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        font-size:14px;
        outline:none;
        transition:var(--transition);
        min-width:180px;
      }

      .mcl-select:focus {
        border-color:var(--primary);
        box-shadow:0 0 0 3px var(--primary-light);
      }

      .mcl-scope-tabs {
        display:inline-flex;
        background:#fff;
        border:1px solid var(--border);
        border-radius:12px;
        padding:4px;
        gap:4px;
        box-shadow:var(--shadow-sm);
      }

      .mcl-scope-tabs a {
        color:var(--text-muted);
        padding:9px 14px;
        border-radius:9px;
        font-size:13px;
        font-weight:800;
        text-decoration:none;
        transition:var(--transition);
        white-space:nowrap;
      }

      .mcl-scope-tabs a.active {
        background:var(--primary-light);
        color:#365314;
      }

      .mcl-scope-tabs a:hover {
        color:#111827;
        text-decoration:none;
      }

      .mcl-card-shell {
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        box-shadow:var(--shadow-sm);
        overflow:hidden;
      }

      .mcl-hidden {
        display:none !important;
      }

      .mcl-bulk-bar {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        padding:14px 16px;
        margin-bottom:16px;
        border-radius:14px;
        border:1px solid rgba(116,178,212,.35);
        background:linear-gradient(135deg, var(--blue-light), var(--primary-light));
        box-shadow:var(--shadow-sm);
      }

      .mcl-bulk-bar-left {
        display:flex;
        align-items:center;
        gap:8px;
        font-weight:900;
        color:#111827;
      }

      .mcl-bulk-count {
        padding:5px 10px;
        border-radius:999px;
        background:#fff;
        border:1px solid var(--border);
        font-size:12px;
        font-weight:900;
      }

      .mcl-bulk-bar-actions {
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:8px;
      }

      .mcl-bulk-btn {
        border-radius:999px;
        border:1px solid var(--border);
        background:#fff;
        padding:7px 11px;
        font-size:12px;
        font-weight:800;
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        gap:6px;
      }

      .mcl-bulk-btn-danger {
        border-color:rgba(239,68,68,.35);
        color:#b91c1c;
        background:var(--danger-light);
      }

      .mcl-bulk-status-group {
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding-left:8px;
        margin-left:4px;
        border-left:1px dashed #cbd5e1;
      }

      .mcl-bulk-status-group span {
        font-size:12px;
        color:var(--text-muted);
        font-weight:800;
      }

      .mcl-list-head {
        display:grid;
        grid-template-columns:minmax(260px,1.5fr) minmax(130px,.7fr) minmax(130px,.7fr) minmax(220px,1.1fr) minmax(220px,1.1fr) minmax(170px,.9fr) 130px;
        gap:14px;
        align-items:center;
        padding:16px 16px 10px 16px;
        color:var(--text-muted);
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
      }

      @media(max-width:1450px) {
        .mcl-list-head {
          display:none;
        }
      }

      .mcl-list {
        display:flex;
        flex-direction:column;
        gap:12px;
        padding:0 0 16px 0;
      }

      .mcl-item {
        background:var(--card-bg);
        border:1px solid var(--border);
        border-radius:var(--radius);
        transition:var(--transition);
        overflow:hidden;
        margin:0 16px;
      }

      .mcl-item:hover {
        border-color:var(--primary);
        box-shadow:var(--shadow);
      }

      .mcl-item.mcl-selected {
        border-color:var(--blue);
        background:linear-gradient(135deg, rgba(116,178,212,.08), rgba(147,194,28,.08));
        box-shadow:0 0 0 1px rgba(116,178,212,.22);
      }

      .mcl-item-row {
        padding:16px;
        display:grid;
        gap:16px;
        align-items:center;
        grid-template-columns:minmax(260px,1.5fr) minmax(130px,.7fr) minmax(130px,.7fr) minmax(220px,1.1fr) minmax(220px,1.1fr) minmax(170px,.9fr) 130px;
      }

      @media(max-width:1450px) {
        .mcl-item-row {
          grid-template-columns:1fr;
        }
      }

      .mcl-cell {
        min-width:0;
      }

      .mcl-cell-title {
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        margin-bottom:4px;
        display:none;
      }

      @media(max-width:1450px) {
        .mcl-cell-title {
          display:block;
        }
      }

      .mcl-main {
        display:flex;
        flex-direction:column;
        min-width:0;
      }

      .mcl-title-line {
        display:flex;
        align-items:flex-start;
        gap:10px;
        min-width:0;
      }

      .mcl-row-check {
        width:18px;
        height:18px;
        margin-top:2px;
        flex:0 0 auto;
        cursor:pointer;
      }

      .mcl-logo {
        width:42px;
        height:42px;
        border-radius:12px;
        background:var(--blue-light);
        color:var(--blue);
        border:1px solid rgba(116,178,212,.25);
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        flex:0 0 auto;
        font-weight:900;
        font-size:13px;
      }

      .mcl-logo img {
        width:100%;
        height:100%;
        object-fit:cover;
      }

      .mcl-ttl {
        font-weight:900;
        font-size:15px;
        margin-bottom:4px;
        color:#111827;
        line-height:1.35;
      }

      .mcl-subt {
        font-size:13px;
        color:var(--text-muted);
        line-height:1.45;
      }

      .mcl-subt.ellipsis {
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
      }

      .mcl-tag,
      .mcl-pill {
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:6px 10px;
        border-radius:999px;
        background:var(--blue-light);
        color:var(--blue);
        font-size:12px;
        font-weight:800;
        width:max-content;
        max-width:100%;
        margin:0 4px 4px 0;
        white-space:nowrap;
      }

      .mcl-pill.green {
        background:var(--primary-light);
        color:#365314;
      }

      .mcl-status-badge,
      .mcl-status-pill {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding:6px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        white-space:nowrap;
        text-transform:capitalize;
      }

      .mcl-status-active,
      .mcl-status-pill.active {
        background:var(--success-light);
        color:#047857;
      }

      .mcl-status-draft,
      .mcl-status-pill.draft {
        background:#f3f4f6;
        color:#4b5563;
      }

      .mcl-status-archived,
      .mcl-status-pill.archived {
        background:var(--warning-light);
        color:#b45309;
      }

      .mcl-creator {
        display:flex;
        align-items:center;
        gap:9px;
        min-width:0;
      }

      .mcl-creator-avatar {
        width:34px;
        height:34px;
        border-radius:999px;
        overflow:hidden;
        border:1px solid var(--border);
        display:flex;
        align-items:center;
        justify-content:center;
        background:#f9fafb;
        font-size:12px;
        font-weight:900;
        color:var(--text-main);
        flex:0 0 auto;
      }

      .mcl-creator-avatar img {
        width:100%;
        height:100%;
        object-fit:cover;
      }

      .mcl-actions {
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:8px;
        flex-wrap:wrap;
      }

      .mcl-grid {
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(310px, 1fr));
        gap:16px;
        padding:16px;
      }

      .mcl-card {
        position:relative;
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        box-shadow:var(--shadow-sm);
        transition:var(--transition);
        padding:16px;
        display:flex;
        flex-direction:column;
        gap:14px;
        min-height:230px;
      }

      .mcl-card:hover {
        border-color:var(--primary);
        box-shadow:var(--shadow);
      }

      .mcl-card.mcl-selected {
        border-color:var(--blue);
        background:linear-gradient(135deg, rgba(116,178,212,.08), rgba(147,194,28,.08));
        box-shadow:0 0 0 1px rgba(116,178,212,.22);
      }

      .mcl-card-select {
        position:absolute;
        top:14px;
        left:14px;
        z-index:2;
        background:#fff;
        border:1px solid var(--border);
        border-radius:999px;
        width:28px;
        height:28px;
        display:flex;
        align-items:center;
        justify-content:center;
        box-shadow:var(--shadow-sm);
      }

      .mcl-card-select input {
        cursor:pointer;
      }

      .mcl-card-header {
        display:flex;
        align-items:flex-start;
        gap:12px;
        padding-left:34px;
      }

      .mcl-card-title {
        min-width:0;
        flex:1;
      }

      .mcl-card-title h2 {
        margin:0;
        font-size:16px;
        font-weight:900;
        color:#111827;
        line-height:1.35;
      }

      .mcl-card-title small {
        display:block;
        color:var(--text-muted);
        font-size:12px;
        margin-top:5px;
        font-weight:700;
      }

      .mcl-card-meta {
        display:flex;
        flex-wrap:wrap;
        gap:4px;
      }

      .mcl-card-footer {
        margin-top:auto;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
      }

      .mcl-card-footer-actions {
        display:flex;
        align-items:center;
        gap:8px;
      }

      .mcl-card-creator {
        display:flex;
        align-items:center;
        gap:8px;
        margin-top:10px;
        font-size:12px;
        color:var(--text-muted);
        font-weight:700;
      }

      .mcl-table-wrapper {
        overflow:auto;
      }

      .mcl-table {
        width:100%;
        border-collapse:collapse;
        font-size:14px;
      }

      .mcl-table th,
      .mcl-table td {
        padding:13px 14px;
        border-bottom:1px solid var(--border);
        text-align:left;
        white-space:nowrap;
        vertical-align:middle;
      }

      .mcl-table th {
        font-size:11px;
        color:var(--text-muted);
        font-weight:900;
        background:#f9fafb;
        text-transform:uppercase;
        letter-spacing:.06em;
        position:sticky;
        top:0;
        z-index:1;
      }

      .mcl-table tr:hover td {
        background:#f9fafb;
      }

      #mcl-table tbody tr.mcl-selected td {
        background:rgba(116,178,212,.12);
      }

      .mcl-empty {
        text-align:center;
        padding:60px;
        color:var(--text-muted);
        background:#fff;
        border:1px dashed var(--border);
        border-radius:16px;
        margin:16px;
      }

      .mcl-pagination {
        margin-top:18px;
        background:#fff;
        border:1px solid var(--border);
        border-radius:14px;
        padding:14px 16px;
        box-shadow:var(--shadow-sm);
      }

      .mcl-pagination .pagination {
        margin:0;
        display:flex;
        flex-wrap:wrap;
        gap:6px;
      }

      .mcl-pagination .page-item .page-link {
        border-radius:10px !important;
        border:1px solid var(--border);
        color:var(--text-main);
        padding:8px 12px;
        line-height:1.1;
        box-shadow:none !important;
      }

      .mcl-pagination .page-item.active .page-link {
        background:var(--primary);
        border-color:var(--primary);
        color:#fff;
      }

      .mcl-pagination .page-item.disabled .page-link {
        color:#9ca3af;
        background:#f9fafb;
      }

      .mcl-menu-shell {
        position:relative;
        display:inline-flex;
      }

      .mcl-menu-trigger {
        width:36px;
        height:36px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        color:var(--text-muted);
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:18px;
        font-weight:900;
        transition:var(--transition);
      }

      .mcl-menu-trigger:hover {
        background:#f9fafb;
        color:#111827;
      }

      .mcl-menu {
        position:absolute;
        top:calc(100% + 8px);
        right:0;
        min-width:210px;
        background:#fff;
        border:1px solid var(--border);
        border-radius:14px;
        box-shadow:var(--shadow);
        padding:6px;
        display:none;
        z-index:50;
      }

      .mcl-menu.open {
        display:block;
      }

      .mcl-menu-item {
        width:100%;
        border:0;
        background:transparent;
        text-align:left;
        padding:9px 10px;
        border-radius:10px;
        color:var(--text-main);
        font-size:13px;
        font-weight:800;
        cursor:pointer;
        transition:var(--transition);
      }

      .mcl-menu-item:hover {
        background:#f9fafb;
      }

      .mcl-menu-item-danger {
        color:#b91c1c;
      }

      .mcl-menu-item-danger:hover {
        background:var(--danger-light);
      }

      /* Modal / Drawer kept compatible with your existing JS */
      .mcl-modal-backdrop {
        position:fixed;
        inset:0;
        z-index:1200;
        background:rgba(17,24,39,.55);
        backdrop-filter:blur(3px);
        opacity:0;
        pointer-events:none;
        transition:opacity .22s ease;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:18px;
      }

      .mcl-modal-backdrop.active {
        opacity:1;
        pointer-events:auto;
      }

      .mcl-modal {
        width:100%;
        max-width:860px;
        max-height:90vh;
        background:#fff;
        border:1px solid rgba(229,231,235,.9);
        border-radius:16px;
        box-shadow:var(--shadow);
        display:flex;
        flex-direction:column;
        overflow:hidden;
        transform:translateY(12px) scale(.985);
        transition:transform .22s ease;
      }

      .mcl-modal-backdrop.active .mcl-modal {
        transform:translateY(0) scale(1);
      }

      .mcl-modal-header {
        padding:16px 18px;
        border-bottom:1px solid var(--border);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        background:#fafafa;
      }

      .mcl-modal-header-left {
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
      }

      .mcl-modal-header h2 {
        margin:0;
        font-size:16px;
        font-weight:900;
        color:#111827;
      }

      .mcl-modal-body {
        padding:20px 18px;
        flex:1 1 auto;
        min-height:0;
        overflow-y:auto;
      }

      .mcl-modal-footer {
        padding:14px 18px;
        border-top:1px solid var(--border);
        background:#fafafa;
        display:flex;
        justify-content:flex-end;
        gap:10px;
        flex-wrap:wrap;
      }

      .mcl-form-row {
        display:flex;
        flex-wrap:wrap;
        gap:14px;
        margin-bottom:14px;
      }

      .mcl-form-col {
        flex:1 1 220px;
        min-width:220px;
      }

      .mcl-label {
        font-size:11px;
        font-weight:900;
        color:var(--text-muted);
        text-transform:uppercase;
        letter-spacing:.06em;
        display:block;
        margin-bottom:6px;
      }

      .mcl-control,
      .mcl-control-textarea,
      .mcl-control-file {
        width:100%;
        border-radius:10px;
        border:1px solid var(--border);
        padding:10px 12px;
        font-size:14px;
        background:#fff;
        color:var(--text-main);
        outline:none;
        transition:var(--transition);
      }

      .mcl-control:focus,
      .mcl-control-textarea:focus {
        border-color:var(--primary);
        box-shadow:0 0 0 3px var(--primary-light);
      }

      .mcl-control-textarea {
        min-height:90px;
        resize:vertical;
      }

      .mcl-items-shell {
        margin-top:18px;
        border-radius:16px;
        border:1px dashed var(--border);
        padding:14px;
        background:#f9fafb;
        max-height:50vh;
        overflow-y:auto;
      }

      .mcl-items-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        margin-bottom:12px;
        font-size:13px;
        color:var(--text-main);
        font-weight:900;
      }

      .mcl-item-row {
        display:flex;
        flex-wrap:wrap;
        align-items:flex-start;
        gap:10px;
        padding:12px;
        margin-bottom:10px;
        border-radius:14px;
        background:#fff;
        border:1px solid var(--border);
        cursor:grab;
      }

      .mcl-item-row.dragging {
        opacity:.7;
        border-style:dashed;
      }

      .mcl-item-handle {
        width:24px;
        display:flex;
        align-items:center;
        justify-content:center;
        cursor:grab;
        color:var(--text-muted);
        font-size:18px;
      }

      .mcl-item-main {
        flex:1 1 230px;
        min-width:230px;
      }

      .mcl-item-side {
        flex:1 1 190px;
        min-width:190px;
        display:flex;
        flex-direction:column;
        gap:8px;
      }

      .mcl-item-footer {
        display:flex;
        align-items:center;
        gap:8px;
        flex-wrap:wrap;
        font-size:13px;
        font-weight:800;
      }

      .mcl-item-footer label {
        display:inline-flex;
        align-items:center;
        gap:6px;
        cursor:pointer;
      }

      .mcl-drawer-backdrop {
        position:fixed;
        inset:0;
        background:rgba(17,24,39,.45);
        backdrop-filter:blur(3px);
        display:none;
        justify-content:flex-end;
        align-items:stretch;
        z-index:1150;
      }

      .mcl-drawer-backdrop.active {
        display:flex;
      }

      .mcl-drawer {
        width:460px;
        max-width:100%;
        height:100%;
        background:#fff;
        border-left:1px solid var(--border);
        display:flex;
        flex-direction:column;
        box-shadow:var(--shadow);
      }

      .mcl-drawer-header {
        padding:16px 18px;
        border-bottom:1px solid var(--border);
        background:#fafafa;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
      }

      .mcl-drawer-header h3 {
        margin:0;
        font-size:16px;
        font-weight:900;
        color:#111827;
      }

      .mcl-drawer-close {
        width:36px;
        height:36px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        color:var(--text-muted);
        cursor:pointer;
        font-size:22px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
      }

      .mcl-drawer-body {
        padding:18px;
        overflow:auto;
        font-size:14px;
      }

      .mcl-preview-meta p {
        margin:0 0 8px 0;
      }

      .mcl-preview-pill {
        display:inline-flex;
        padding:5px 9px;
        border-radius:999px;
        background:var(--blue-light);
        color:var(--blue);
        font-size:12px;
        font-weight:800;
        margin:0 4px 4px 0;
      }

      .mcl-preview-items-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin:18px 0 10px 0;
      }

      .mcl-preview-items-header h4 {
        margin:0;
        font-size:15px;
        font-weight:900;
        color:#111827;
      }

      .mcl-preview-items-hint {
        font-size:12px;
        color:var(--text-muted);
        font-weight:700;
      }

      .mcl-preview-item {
        display:flex;
        align-items:flex-start;
        gap:10px;
        border-radius:14px;
        border:1px solid var(--border);
        padding:12px;
        margin-bottom:10px;
        background:#f9fafb;
      }

      .mcl-preview-item-main {
        flex:1;
        min-width:0;
      }

      .mcl-preview-item-title {
        font-size:14px;
        font-weight:900;
        color:#111827;
      }

      .mcl-preview-item-meta {
        font-size:12px;
        color:var(--text-muted);
        margin-top:3px;
      }

      .mcl-preview-option-group {
        display:flex;
        flex-direction:column;
        gap:6px;
        margin-top:8px;
      }

      .mcl-preview-option {
        font-size:14px;
        color:#111827;
        display:inline-flex;
        align-items:center;
        gap:8px;
      }

      .mcl-a4-modal-body {
        background:#f3f4f6;
        padding:18px !important;
      }

      .mcl-a4-viewer {
        display:flex;
        flex-direction:column;
        align-items:center;
        gap:16px;
      }

      .mcl-a4 {
        width:210mm;
        min-height:297mm;
        padding:14mm;
        box-sizing:border-box;
        background:#fff;
        color:#111827;
        font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        box-shadow:0 10px 30px rgba(0,0,0,.12);
        border-radius:10px;
        overflow:hidden;
        page-break-after:always;
      }

      .mcl-a4:last-child {
        page-break-after:auto;
      }

      .mcl-pdf-banner {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10mm;
        padding:8mm 9mm;
        border-radius:10px;
        background:linear-gradient(135deg, #74b2d4 0%, #93c21c 110%);
        color:#fff;
        margin-bottom:8mm;
        page-break-inside:avoid;
      }

      .mcl-pdf-banner-left {
        display:flex;
        align-items:center;
        gap:6mm;
        min-width:0;
      }

      .mcl-pdf-badge {
        width:14mm;
        height:14mm;
        border-radius:8px;
        background:rgba(255,255,255,.18);
        border:1px solid rgba(255,255,255,.35);
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:900;
        font-size:10pt;
      }

      .mcl-pdf-banner-titlewrap {
        min-width:0;
      }

      .mcl-pdf-banner-kicker {
        font-size:10pt;
        opacity:.92;
        margin:0 0 1mm 0;
      }

      .mcl-pdf-banner-title {
        font-size:18pt;
        font-weight:900;
        margin:0;
        line-height:1.1;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
        max-width:120mm;
      }

      .mcl-pdf-banner-right {
        text-align:right;
        font-size:10pt;
        opacity:.95;
        white-space:nowrap;
      }

      .mcl-pdf-section-title {
        font-size:13px;
        font-weight:900;
        margin:0 0 4mm 0;
        padding:2mm 3mm;
        background:#f3f4f6;
        border:1px solid #e5e7eb;
        border-radius:6px;
      }

      .mcl-pdf-sub {
        font-size:12px;
        color:#6b7280;
        margin:0;
      }

      .mcl-pdf-item {
        border:1px solid #e5e7eb;
        border-radius:8px;
        padding:4mm;
        margin-bottom:3mm;
        page-break-inside:avoid;
      }

      .mcl-pdf-item-label {
        font-size:12px;
        font-weight:900;
        margin-bottom:1mm;
      }

      .mcl-pdf-item-meta {
        font-size:10px;
        color:#6b7280;
        margin-bottom:2mm;
      }

      .mcl-pdf-line {
        display:flex;
        gap:3mm;
        align-items:center;
      }

      .mcl-pdf-box {
        width:5mm;
        height:5mm;
        border:1px solid #111827;
        border-radius:2px;
      }

      .mcl-pdf-input {
        flex:1;
        border-bottom:1px solid #9ca3af;
        height:6mm;
      }

      .mcl-pdf-footer {
        margin-top:10mm;
        font-size:10px;
        color:#6b7280;
        border-top:1px solid #e5e7eb;
        padding-top:4mm;
      }

      @media(max-width:900px) {
        .mcl-a4 {
          width:100% !important;
          min-height:auto !important;
          padding:14px !important;
        }
      }

      @media(max-width:640px) {
        .mcl-modal {
          max-width:100%;
          border-radius:0;
          max-height:100vh;
        }

        .mcl-scope-tabs {
          width:100%;
          overflow:auto;
        }

        .mcl-toolbar {
          align-items:stretch;
        }

        .mcl-toolbar-left,
        .mcl-toolbar-right {
          width:100%;
        }

        .mcl-filter-block,
        .mcl-filter-block.search {
          width:100%;
          min-width:100%;
        }

        .mcl-input,
        .mcl-select {
          min-width:100%;
        }
      }
    </style>
@endsection

@section('content')
    <div class="mcl-wrap">
      <div class="mcl-page-header">
        <div class="mcl-titlebar">
          <div>
            <div class="mcl-page-title">Wartungs-Checklisten</div>
            <div class="mcl-page-subtitle">
              Verwalten Sie Checklisten-Vorlagen, Marken, Vertriebspartner und Felder für Wartungsabläufe zentral.
            </div>

             
          </div>

          <div class="mcl-inline-actions">
            <div class="mcl-view-toggle">
              <button type="button" data-view="card" class="active">
                <i class="fa fa-th-large"></i>
                <span>Karten</span>
              </button>
              <button type="button" data-view="list">
                <i class="fa fa-list-ul"></i>
                <span>Tabelle</span>
              </button>
            </div>

            @if($currentScope !== 'deleted')
                  <button type="button" class="mcl-btn" id="mcl-btn-create">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Neue Checkliste
                  </button>
            @endif
          </div>
        </div>
      </div>

      <div class="mcl-analytics">
        <div class="mcl-stat">
          <div class="mcl-stat-icon total">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12h18M3 6h18M3 18h18"/>
            </svg>
          </div>
          <div class="mcl-stat-meta">
            <div class="mcl-stat-label">Gesamt</div>
            <div class="mcl-stat-value">{{ $totalCount }}</div>
            <div class="mcl-stat-sub">Checklisten insgesamt</div>
          </div>
        </div>

        <div class="mcl-stat">
          <div class="mcl-stat-icon active">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </div>
          <div class="mcl-stat-meta">
            <div class="mcl-stat-label">Aktiv</div>
            <div class="mcl-stat-value">{{ $activeCount }}</div>
            <div class="mcl-stat-sub">Freigegebene Vorlagen</div>
          </div>
        </div>

        <div class="mcl-stat">
          <div class="mcl-stat-icon draft">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 20h9"/>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
            </svg>
          </div>
          <div class="mcl-stat-meta">
            <div class="mcl-stat-label">Entwurf</div>
            <div class="mcl-stat-value">{{ $draftCount }}</div>
            <div class="mcl-stat-sub">In Bearbeitung</div>
          </div>
        </div>

        <div class="mcl-stat">
          <div class="mcl-stat-icon archived">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 8v13H3V8"/>
              <path d="M1 3h22v5H1z"/>
              <path d="M10 12h4"/>
            </svg>
          </div>
          <div class="mcl-stat-meta">
            <div class="mcl-stat-label">Archiv</div>
            <div class="mcl-stat-value">{{ $archivedCount }}</div>
            <div class="mcl-stat-sub">Archivierte Vorlagen</div>
          </div>
        </div>

        <div class="mcl-stat">
          <div class="mcl-stat-icon fields">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 11l3 3L22 4"/>
              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
          </div>
          <div class="mcl-stat-meta">
            <div class="mcl-stat-label">Felder</div>
            <div class="mcl-stat-value">{{ $fieldCount }}</div>
            <div class="mcl-stat-sub">Checklisten-Positionen</div>
          </div>
        </div>
      </div>

      <div class="mcl-bulk-bar mcl-hidden" id="mcl-bulk-bar">
        <div class="mcl-bulk-bar-left">
          <span class="mcl-bulk-count" id="mcl-bulk-count">0 ausgewählt</span>
          <span>Mehrere Checklisten bearbeiten</span>
        </div>

        <div class="mcl-bulk-bar-actions">
          @if($currentScope === 'active' || $currentScope === 'draft')
            <button type="button" class="mcl-bulk-btn" data-bulk-action="archive">Archivieren</button>
            <button type="button" class="mcl-bulk-btn mcl-bulk-btn-danger" data-bulk-action="trash">In Papierkorb</button>
          @elseif($currentScope === 'archived')
            <button type="button" class="mcl-bulk-btn" data-bulk-action="unarchive">Archiv aufheben</button>
            <button type="button" class="mcl-bulk-btn mcl-bulk-btn-danger" data-bulk-action="trash">In Papierkorb</button>
          @elseif($currentScope === 'deleted')
            <button type="button" class="mcl-bulk-btn" data-bulk-action="restore">Wiederherstellen</button>
          @endif

          <div class="mcl-bulk-status-group">
            <span>Status:</span>
            <button type="button" class="mcl-bulk-btn" data-bulk-action="status_active">Aktiv</button>
            <button type="button" class="mcl-bulk-btn" data-bulk-action="status_draft">Entwurf</button>
            <button type="button" class="mcl-bulk-btn" data-bulk-action="status_archived">Archiviert</button>
          </div>
        </div>
      </div>

      <div class="mcl-toolbar">
        <div class="mcl-toolbar-left">
          <div class="mcl-filter-block search">
            <label class="mcl-filter-label">Suche</label>
            <input
              type="text"
              id="mcl-search"
              class="mcl-input"
              placeholder="Nach Titel, Typ, Marke oder Vertriebspartner suchen..."
              value="{{ $search ?? '' }}"
            >
          </div>

          <div class="mcl-filter-block">
            <label class="mcl-filter-label">Bereich</label>
            <div class="mcl-scope-tabs">
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

          <div class="mcl-filter-block">
            <label class="mcl-filter-label">Sortierung</label>
            <select id="mcl-sort" class="mcl-select">
              <option value="title_asc"  {{ ($sort ?? '') === 'title_asc' ? 'selected' : '' }}>Titel A–Z</option>
              <option value="title_desc" {{ ($sort ?? '') === 'title_desc' ? 'selected' : '' }}>Titel Z–A</option>
              <option value="latest"     {{ ($sort ?? '') === 'latest' ? 'selected' : '' }}>Neueste zuerst</option>
            </select>
          </div>
        </div>

        <div class="mcl-toolbar-right">
          <a href="{{ route('admin.maintenance_checklists.index', ['scope' => $currentScope]) }}" class="mcl-btn-soft">
            Zurücksetzen
          </a>
        </div>
      </div>

      {{-- CARD VIEW --}}
      <div id="mcl-view-card">
        <div class="mcl-card-shell">
          <div class="mcl-grid" id="mcl-card-grid">
            @forelse($maintenanceChecklists as $checklist)
                  @php
                    $creator = optional($checklist->creatorEmployee);
                    $creatorName = $creator?->full_name ?? $creator?->name ?? 'Unbekannt';
                    $creatorInitials = collect(explode(' ', $creatorName))
                        ->filter()
                        ->map(fn($p) => mb_substr($p, 0, 1))
                        ->take(2)
                        ->implode('');
                    $status = $checklist->status ?? 'draft';
                    $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                  @endphp

                  <div class="mcl-card"
                    data-id="{{ $checklist->id }}"
                    data-title="{{ strtolower($checklist->title) }}"
                    data-title-display="{{ e($checklist->title) }}"
                    data-status="{{ $status }}"
                    data-type="{{ strtolower($checklist->type) }}"
                    data-brands="{{ strtolower($checklist->brands->pluck('name')->join(' ')) }}"
                    data-distributors="{{ strtolower($checklist->distributors->pluck('name')->join(' ')) }}">

                    <label class="mcl-card-select">
                      <input type="checkbox" class="mcl-select-checkbox" value="{{ $checklist->id }}">
                    </label>

                    <div class="mcl-card-header">
                      <div class="mcl-logo">
                        @if($checklist->logo_path)
                              <img src="{{ asset($checklist->logo_path) }}" alt="">
                        @else
                              <span>{{ strtoupper(mb_substr($checklist->title, 0, 2)) }}</span>
                        @endif
                      </div>

                      <div class="mcl-card-title">
                        <h2>{{ $checklist->title }}</h2>
                        <small>{{ $checklist->type ?: 'Ohne Typ' }} · {{ $checklist->items->count() }} Felder</small>

                        <div class="mcl-card-creator">
                          <div class="mcl-creator-avatar">
                            @if($creator && $creator->image)
                                  <img src="{{ asset('images/employee/' . $creator->image) }}" alt="">
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
                        <span class="mcl-pill green">{{ $brand->name }}</span>
                      @endforeach

                      @foreach($checklist->distributors as $dist)
                        <span class="mcl-pill">{{ $dist->name }}</span>
                      @endforeach

                      @if($checklist->brands->isEmpty() && $checklist->distributors->isEmpty())
                        <span class="mcl-subt">Keine Marken oder Vertriebspartner verknüpft.</span>
                      @endif
                    </div>

                    <div class="mcl-card-footer">
                      <span class="mcl-status-pill {{ $status }}">{{ $statusLabel }}</span>

                      <div class="mcl-card-footer-actions">
                        <button type="button" class="mcl-btn-ic primary mcl-menu-item"
                                data-action="preview"
                                data-id="{{ $checklist->id }}"
                                title="Vorschau">
                          <i class="fa fa-eye"></i>
                        </button>

                        @if($currentScope !== 'deleted')
                              <button type="button" class="mcl-btn-ic warning mcl-menu-item"
                                      data-action="edit"
                                      data-id="{{ $checklist->id }}"
                                      title="Bearbeiten">
                                <i class="fa fa-pencil"></i>
                              </button>
                        @endif

                        <div class="mcl-menu-shell">
                          <button type="button" class="mcl-menu-trigger" aria-label="Aktionen">⋯</button>
                          <div class="mcl-menu">
                            <button type="button" class="mcl-menu-item" data-action="preview" data-id="{{ $checklist->id }}">
                              Vorschau
                            </button>

                            <button type="button" class="mcl-menu-item" data-action="pdf" data-id="{{ $checklist->id }}">
                              PDF Vorschau (A4)
                            </button>

                            @if($currentScope !== 'deleted')
                                  <button type="button" class="mcl-menu-item" data-action="edit" data-id="{{ $checklist->id }}">
                                    Bearbeiten
                                  </button>
                            @endif

                            @if($currentScope === 'active')
                                  <button type="button" class="mcl-menu-item" data-action="duplicate" data-id="{{ $checklist->id }}">
                                    Duplizieren
                                  </button>
                            @endif

                            @if($currentScope === 'active' || $currentScope === 'draft')
                                  <button type="button" class="mcl-menu-item" data-action="archive" data-id="{{ $checklist->id }}">
                                    Archivieren
                                  </button>
                                  <button type="button" class="mcl-menu-item mcl-menu-item-danger" data-action="trash" data-id="{{ $checklist->id }}">
                                    In Papierkorb
                                  </button>
                            @elseif($currentScope === 'archived')
                                  <button type="button" class="mcl-menu-item" data-action="unarchive" data-id="{{ $checklist->id }}">
                                    Archiv aufheben
                                  </button>
                                  <button type="button" class="mcl-menu-item mcl-menu-item-danger" data-action="trash" data-id="{{ $checklist->id }}">
                                    In Papierkorb
                                  </button>
                            @elseif($currentScope === 'deleted')
                                  <button type="button" class="mcl-menu-item" data-action="restore" data-id="{{ $checklist->id }}">
                                    Wiederherstellen
                                  </button>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
            @empty
                  <div class="mcl-empty" style="grid-column:1/-1;">
                    Keine Wartungs-Checklisten gefunden.
                  </div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- LIST VIEW --}}
      <div id="mcl-view-list" class="mcl-hidden">
        <div class="mcl-card-shell">
          <div class="mcl-list-head">
            <div>Checkliste</div>
            <div>Typ</div>
            <div>Status</div>
            <div>Marken</div>
            <div>Vertriebspartner</div>
            <div>Erstellt von</div>
            <div style="text-align:right;">Aktionen</div>
          </div>

          <div class="mcl-list">
            @forelse($maintenanceChecklists as $checklist)
                  @php
                    $creator = optional($checklist->creatorEmployee);
                    $creatorName = $creator?->full_name ?? $creator?->name ?? 'Unbekannt';
                    $creatorInitials = collect(explode(' ', $creatorName))
                        ->filter()
                        ->map(fn($p) => mb_substr($p, 0, 1))
                        ->take(2)
                        ->implode('');
                    $status = $checklist->status ?? 'draft';
                    $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                  @endphp

                  <div class="mcl-item"
                    data-id="{{ $checklist->id }}"
                    data-title="{{ strtolower($checklist->title) }}"
                    data-title-display="{{ e($checklist->title) }}"
                    data-status="{{ $status }}"
                    data-type="{{ strtolower($checklist->type) }}"
                    data-brands="{{ strtolower($checklist->brands->pluck('name')->join(' ')) }}"
                    data-distributors="{{ strtolower($checklist->distributors->pluck('name')->join(' ')) }}">

                    <div class="mcl-item-row">
                      <div class="mcl-cell">
                        <div class="mcl-cell-title">Checkliste</div>

                        <div class="mcl-title-line">
                          <input type="checkbox" class="mcl-select-checkbox mcl-row-check" value="{{ $checklist->id }}">

                          <div class="mcl-logo">
                            @if($checklist->logo_path)
                                  <img src="{{ asset($checklist->logo_path) }}" alt="">
                            @else
                                  <span>{{ strtoupper(mb_substr($checklist->title, 0, 2)) }}</span>
                            @endif
                          </div>

                          <div class="mcl-main">
                            <div class="mcl-ttl">{{ $checklist->title }}</div>
                            <div class="mcl-subt">
                              <span class="mcl-tag">{{ $checklist->items->count() }} Felder</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="mcl-cell">
                        <div class="mcl-cell-title">Typ</div>
                        <div class="mcl-tag">{{ $checklist->type ?: '–' }}</div>
                      </div>

                      <div class="mcl-cell">
                        <div class="mcl-cell-title">Status</div>
                        <span class="mcl-status-pill {{ $status }}">{{ $statusLabel }}</span>
                      </div>

                      <div class="mcl-cell">
                        <div class="mcl-cell-title">Marken</div>
                        @forelse($checklist->brands as $brand)
                              <span class="mcl-pill green">{{ $brand->name }}</span>
                        @empty
                              <span class="mcl-subt">–</span>
                        @endforelse
                      </div>

                      <div class="mcl-cell">
                        <div class="mcl-cell-title">Vertriebspartner</div>
                        @forelse($checklist->distributors as $dist)
                              <span class="mcl-pill">{{ $dist->name }}</span>
                        @empty
                              <span class="mcl-subt">–</span>
                        @endforelse
                      </div>

                      <div class="mcl-cell">
                        <div class="mcl-cell-title">Erstellt von</div>
                        <div class="mcl-creator">
                          <div class="mcl-creator-avatar">
                            @if($creator && $creator->image)
                                  <img src="{{ asset('images/employee/' . $creator->image) }}" alt="">
                            @else
                                  <span>{{ $creatorInitials }}</span>
                            @endif
                          </div>
                          <div class="mcl-main">
                            <div class="mcl-ttl" style="font-size:14px;">{{ $creatorName }}</div>
                            <div class="mcl-subt">Aktualisiert: {{ $checklist->updated_at?->format('d.m.Y H:i') ?? '–' }}</div>
                          </div>
                        </div>
                      </div>

                      <div class="mcl-cell">
                        <div class="mcl-cell-title">Aktionen</div>
                        <div class="mcl-actions">
                          <button type="button" class="mcl-btn-ic primary mcl-menu-item"
                                  data-action="preview"
                                  data-id="{{ $checklist->id }}"
                                  title="Vorschau">
                            <i class="fa fa-eye"></i>
                          </button>

                          @if($currentScope !== 'deleted')
                            <button type="button" class="mcl-btn-ic warning mcl-menu-item"
                                    data-action="edit"
                                    data-id="{{ $checklist->id }}"
                                    title="Bearbeiten">
                              <i class="fa fa-pencil"></i>
                            </button>
                          @endif

                          <div class="mcl-menu-shell">
                            <button type="button" class="mcl-menu-trigger" aria-label="Aktionen">⋯</button>
                            <div class="mcl-menu">
                              <button type="button" class="mcl-menu-item" data-action="preview" data-id="{{ $checklist->id }}">
                                Vorschau
                              </button>

                              <button type="button" class="mcl-menu-item" data-action="pdf" data-id="{{ $checklist->id }}">
                                PDF Vorschau (A4)
                              </button>

                              @if($currentScope !== 'deleted')
                                <button type="button" class="mcl-menu-item" data-action="edit" data-id="{{ $checklist->id }}">
                                  Bearbeiten
                                </button>
                              @endif

                              @if($currentScope === 'active')
                                <button type="button" class="mcl-menu-item" data-action="duplicate" data-id="{{ $checklist->id }}">
                                  Duplizieren
                                </button>
                              @endif

                              @if($currentScope === 'active' || $currentScope === 'draft')
                                <button type="button" class="mcl-menu-item" data-action="archive" data-id="{{ $checklist->id }}">
                                  Archivieren
                                </button>
                                <button type="button" class="mcl-menu-item mcl-menu-item-danger" data-action="trash" data-id="{{ $checklist->id }}">
                                  In Papierkorb
                                </button>
                              @elseif($currentScope === 'archived')
                                <button type="button" class="mcl-menu-item" data-action="unarchive" data-id="{{ $checklist->id }}">
                                  Archiv aufheben
                                </button>
                                <button type="button" class="mcl-menu-item mcl-menu-item-danger" data-action="trash" data-id="{{ $checklist->id }}">
                                  In Papierkorb
                                </button>
                              @elseif($currentScope === 'deleted')
                                <button type="button" class="mcl-menu-item" data-action="restore" data-id="{{ $checklist->id }}">
                                  Wiederherstellen
                                </button>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
            @empty
                  <div class="mcl-empty">Keine Wartungs-Checklisten gefunden.</div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- HIDDEN TABLE FOR EXISTING JS COMPATIBILITY --}}
      <div class="mcl-hidden">
        <table class="mcl-table" id="mcl-table">
          <thead>
            <tr>
              <th style="width:32px;"><input type="checkbox" id="mcl-select-all-list"></th>
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
                    data-distributors="{{ strtolower($checklist->distributors->pluck('name')->join(' ')) }}">
                    <td><input type="checkbox" class="mcl-select-checkbox" value="{{ $checklist->id }}"></td>
                    <td>{{ $checklist->title }}</td>
                    <td>{{ $checklist->type }}</td>
                    <td>{{ ucfirst($checklist->status) }}</td>
                    <td>{{ $checklist->items->count() }}</td>
                    <td>{{ $checklist->brands->pluck('name')->join(', ') }}</td>
                    <td>{{ $checklist->distributors->pluck('name')->join(', ') }}</td>
                    <td>{{ $creatorName }}</td>
                    <td>{{ $checklist->updated_at?->format('d.m.Y H:i') }}</td>
                    <td></td>
                  </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($isPaginator && method_exists($maintenanceChecklists, 'links') && $maintenanceChecklists->hasPages())
        <div class="mcl-pagination">
          <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <div style="font-size:12px;color:#6b7280;">
              Zeige <strong>{{ $maintenanceChecklists->firstItem() ?? 0 }}</strong>
              bis <strong>{{ $maintenanceChecklists->lastItem() ?? 0 }}</strong>
              von <strong>{{ $maintenanceChecklists->total() }}</strong> Einträgen
            </div>
            <div>
              {{ $maintenanceChecklists->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
          </div>
        </div>
      @elseif(method_exists($maintenanceChecklists, 'links'))
        <div style="margin-top:1rem;">
          {{ $maintenanceChecklists->links() }}
        </div>
      @endif
    </div>

    {{-- CREATE / EDIT MODAL --}}
    <div class="mcl-modal-backdrop" id="mcl-modal-backdrop">
      <div class="mcl-modal">
        <div class="mcl-modal-header">
          <div class="mcl-modal-header-left">
            <h2 id="mcl-modal-title">Neue Wartungs-Checkliste</h2>
            <button type="button" class="mcl-btn mcl-btn-sm mcl-btn-ghost" id="mcl-help-btn">
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
        <div class="mcl-drawer-body" id="mcl-drawer-body"></div>
      </div>
    </div>

    {{-- A4 PREVIEW MODAL --}}
    <div class="mcl-modal-backdrop" id="mcl-pdf-backdrop">
      <div class="mcl-modal" style="max-width:1060px;">
        <div class="mcl-modal-header">
          <div class="mcl-modal-header-left">
            <h2 id="mcl-pdf-title">A4 Vorschau</h2>
          </div>
          <button type="button" class="mcl-btn mcl-btn-sm mcl-btn-outline" id="mcl-pdf-close">
            Schließen
          </button>
        </div>

        <div class="mcl-modal-body mcl-a4-modal-body">
          <div id="mcl-a4-viewer" class="mcl-a4-viewer"></div>
        </div>
      </div>
    </div>
@endsection


@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Wartungsverträge',
        url: "{{ url('admin/maintenance/contracts') }}", 
      },
       {
        label: 'Wartungs-Checklisten',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush