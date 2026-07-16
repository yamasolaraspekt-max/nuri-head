@extends('admin.layouts.app')

@section('title', 'MEIN ANTRÄGE')

@php
    $pageTitle = 'MEIN ANTRÄGE';

    $hasAdminAccess = $hasAdminAccess ?? false;
    $canCreateLeave = $canCreateLeave ?? false;

    $search = $search ?? request('search', '');
    $sort = $sort ?? request('sort', 'desc');
    $filter = $filter ?? request('request_filter', 'all');
    $activeTab = request('active_tab', 'open');

    if (!$hasAdminAccess && $activeTab === 'answer') {
        $activeTab = 'open';
    }

    $totalCount = $stats['total'] ?? 0;
    $openCount = $stats['open'] ?? 0;
    $todayLeaveCount = $stats['today_on_leave'] ?? 0;
    $pendingToMeCount = $stats['pending_to_me'] ?? 0;
    $newRequestsCount = $stats['new_requests'] ?? 0;
    $unapprovedRequestsCount = $stats['unapproved_requests'] ?? $pendingToMeCount;
    $approvedRequestsCount = $stats['approved_requests'] ?? 0;
    $allRequestsCount = $stats['all_requests'] ?? 0;
@endphp

@once
    @push('style')
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css" rel="stylesheet">
        <link href="{{ asset('css/custom-menu.css') }}" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>

        <style>
            :root {
                --card-bg:#ffffff;
                --text-main:#1f2937;
                --text-muted:#6b7280;
                --border:#e5e7eb;
                --primary:var(--sa-accent);
                --primary-hover:var(--sa-accent-hover);
                --primary-light:#f2fae8;
                --blue:#74b2d4;
                --blue-light:#eff6ff;
                --success:#10b981;
                --success-light:#ecfdf5;
                --warning:#f59e0b;
                --warning-light:#fffbeb;
                --danger:#ef4444;
                --danger-light:#fef2f2;
                --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
                --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
                --radius:14px;
                --transition:all .2s ease-in-out;
            }

            .oc-wrap {
                font-family: Inter, system-ui, -apple-system, sans-serif;
                color: var(--text-main);
            }

            .oc-header { margin-bottom:18px; }

            .oc-titlebar {
                display:flex;
                align-items:flex-end;
                justify-content:space-between;
                gap:12px;
                margin-bottom:16px;
                flex-wrap:wrap;
            }

            .oc-title {
                font-size:26px;
                font-weight:800;
                letter-spacing:-.025em;
                color:#111827;
            }

            .oc-sub {
                font-size:14px;
                color:var(--text-muted);
                margin-top:4px;
            }

            .oc-breadcrumb {
                display:flex;
                align-items:center;
                flex-wrap:wrap;
                gap:8px;
                margin-top:10px;
                font-size:13px;
                color:var(--text-muted);
            }

            .oc-breadcrumb a {
                color:var(--text-muted);
                text-decoration:none;
                font-weight:700;
            }

            .oc-breadcrumb a:hover {
                color:var(--text-main);
            }

            .oc-breadcrumb span.current {
                color:#111827;
                font-weight:800;
            }

            .oc-btn,
            .oc-btn-soft,
            .oc-btn-ic {
                transition:var(--transition);
            }

            .oc-btn {
                background:var(--primary);
                color:#fff;
                border:none;
                padding:10px 16px;
                border-radius:10px;
                font-weight:900;
                cursor:pointer;
                display:inline-flex;
                align-items:center;
                gap:8px;
                text-decoration:none;
            }

            .oc-btn:hover {
                background:var(--primary-hover);
                color:#fff;
                text-decoration:none;
            }

            .oc-btn-soft {
                background:#fff;
                color:var(--text-main);
                border:1px solid var(--border);
                padding:10px 14px;
                border-radius:10px;
                font-weight:800;
                cursor:pointer;
                text-decoration:none;
            }

            .oc-btn-soft:hover {
                background:#f9fafb;
                color:var(--text-main);
                text-decoration:none;
            }

            .oc-btn-ic {
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
                text-decoration:none;
            }

            .oc-btn-ic:hover {
                background:#f9fafb;
                color:var(--text-main);
                border-color:#d1d5db;
                text-decoration:none;
            }

            .oc-btn-ic.primary {
                color:var(--primary);
                border-color:var(--primary-light);
                background:var(--primary-light);
            }

            .oc-btn-ic.warning {
                color:#d97706;
                border-color:#fde7b0;
                background:#fffbeb;
            }

            .oc-btn-ic.success {
                color:var(--success);
                border-color:#c7f2df;
                background:var(--success-light);
            }

            .oc-btn-ic.danger {
                color:var(--danger);
                border-color:rgba(239,68,68,.18);
                background:var(--danger-light);
            }

            .oc-analytics {
                display:grid;
                grid-template-columns:repeat(4, minmax(0,1fr));
                gap:14px;
                margin-bottom:18px;
            }

            @media(max-width:1200px) {
                .oc-analytics { grid-template-columns:repeat(2, minmax(0,1fr)); }
            }

            @media(max-width:700px) {
                .oc-analytics { grid-template-columns:1fr; }
            }

            .oc-stat {
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

            .oc-stat-icon {
                width:48px;
                height:48px;
                border-radius:14px;
                display:flex;
                align-items:center;
                justify-content:center;
                flex:0 0 auto;
            }

            .oc-stat-icon.total { background:var(--blue-light); color:var(--blue); }
            .oc-stat-icon.open { background:var(--warning-light); color:#d97706; }
            .oc-stat-icon.today { background:var(--success-light); color:var(--success); }
            .oc-stat-icon.pending { background:#fef3c7; color:#b45309; }

            .oc-stat-meta { min-width:0; }

            .oc-stat-label {
                font-size:11px;
                font-weight:800;
                color:var(--text-muted);
                text-transform:uppercase;
                letter-spacing:.06em;
            }

            .oc-stat-value {
                font-size:24px;
                font-weight:900;
                color:#111827;
                line-height:1.1;
                margin-top:4px;
            }

            .oc-stat-sub {
                font-size:12px;
                color:var(--text-muted);
                margin-top:4px;
            }

            .oc-toolbar {
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

            .oc-toolbar-left,
            .oc-toolbar-right {
                display:flex;
                align-items:flex-end;
                gap:12px;
                flex-wrap:wrap;
            }

            .oc-toolbar-left { flex:1; }

            .oc-filter-block {
                display:flex;
                flex-direction:column;
                gap:6px;
                min-width:170px;
            }

            .oc-filter-block.search {
                flex:1;
                min-width:280px;
            }

            .oc-filter-label {
                font-size:11px;
                font-weight:800;
                color:var(--text-muted);
                text-transform:uppercase;
                letter-spacing:.06em;
            }

            .oc-input {
                background:#f9fafb;
                border:1px solid var(--border);
                border-radius:8px;
                padding:10px 12px;
                font-size:14px;
                outline:none;
                transition:var(--transition);
                min-width:240px;
                width:100%;
            }

            .oc-input:focus {
                background:#fff;
                border-color:var(--primary);
                box-shadow:0 0 0 3px var(--primary-light);
            }

            .oc-select {
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

            .oc-select:focus {
                border-color:var(--primary);
                box-shadow:0 0 0 3px var(--primary-light);
            }

            .oc-card {
                background:#fff;
                border:1px solid var(--border);
                border-radius:16px;
                box-shadow:var(--shadow-sm);
                overflow:hidden;
            }

            .oc-tabs {
                display:flex;
                gap:10px;
                flex-wrap:wrap;
                padding:16px 16px 0 16px;
            }

            .oc-tab-btn {
                border:none;
                background:#f3f4f6;
                color:#4b5563;
                font-size:13px;
                font-weight:800;
                padding:10px 16px;
                border-radius:999px;
                transition:var(--transition);
                cursor:pointer;
            }

            .oc-tab-btn.active {
                background:var(--primary);
                color:#fff;
            }

            .oc-pane {
                display:none;
                padding:16px;
            }

            .oc-pane.active {
                display:block;
            }

            .oc-list-head {
                display:grid;
                grid-template-columns:110px minmax(230px,1.5fr) 170px 160px 170px 130px 180px;
                gap:14px;
                align-items:center;
                padding:16px 16px 10px 16px;
                color:var(--text-muted);
                font-size:11px;
                font-weight:900;
                text-transform:uppercase;
                letter-spacing:.06em;
            }

            @media(max-width:1280px) {
                .oc-list-head { display:none; }
            }

            .oc-list {
                display:flex;
                flex-direction:column;
                gap:12px;
                padding:0 0 16px 0;
            }

            .oc-item {
                background:var(--card-bg);
                border:1px solid var(--border);
                border-radius:var(--radius);
                transition:var(--transition);
                overflow:hidden;
                margin:0 16px;
            }

            .oc-item:hover {
                border-color:var(--primary);
                box-shadow:var(--shadow);
            }

            .oc-item.is-new-request {
                border-left:5px solid #2563eb;
                background:linear-gradient(90deg, #eff6ff 0%, #ffffff 45%);
            }

            .oc-item.is-unapproved-request {
                border-left:5px solid #f59e0b;
            }

            .oc-item-row {
                padding:16px;
                display:grid;
                gap:16px;
                align-items:center;
                grid-template-columns:110px minmax(230px,1.5fr) 170px 160px 170px 130px 180px;
            }

            @media(max-width:1280px) {
                .oc-item-row { grid-template-columns:1fr; }
            }

            .oc-cell { min-width:0; }

            .oc-cell-title {
                font-size:11px;
                font-weight:800;
                color:var(--text-muted);
                text-transform:uppercase;
                margin-bottom:4px;
                display:none;
            }

            @media(max-width:1280px) {
                .oc-cell-title { display:block; }
            }

            .oc-id-badge {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                min-width:72px;
                height:36px;
                padding:0 12px;
                border-radius:10px;
                background:var(--blue-light);
                color:var(--blue);
                font-size:13px;
                font-weight:900;
            }

            .oc-main {
                display:flex;
                flex-direction:column;
                min-width:0;
            }

            .oc-ttl {
                font-weight:800;
                font-size:15px;
                margin-bottom:4px;
                color:#111827;
            }

            .oc-subt {
                font-size:13px;
                color:var(--text-muted);
                white-space:nowrap;
                overflow:hidden;
                text-overflow:ellipsis;
            }

            .oc-status-pill {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                padding:6px 10px;
                border-radius:999px;
                font-size:12px;
                font-weight:900;
                white-space:nowrap;
            }

            .oc-status-pill.green { background:#ecfdf5; color:#047857; }
            .oc-status-pill.orange { background:#fffbeb; color:#b45309; }
            .oc-status-pill.red { background:#fef2f2; color:#b91c1c; }
            .oc-status-pill.blue { background:#eff6ff; color:#1d4ed8; }
            .oc-status-pill.gray { background:#f3f4f6; color:#374151; }
            .oc-status-pill.new { background:#dbeafe; color:#1d4ed8; }
            .oc-status-pill.unapproved { background:#fffbeb; color:#b45309; }

            .oc-new-badge {
                display:inline-flex;
                align-items:center;
                gap:5px;
                padding:4px 9px;
                border-radius:999px;
                background:#dbeafe;
                color:#1d4ed8;
                font-size:11px;
                font-weight:900;
                margin-left:6px;
                vertical-align:middle;
            }

            .oc-new-badge::before {
                content:'';
                width:7px;
                height:7px;
                border-radius:999px;
                background:#2563eb;
            }

            .oc-actions {
                display:flex;
                align-items:center;
                justify-content:flex-end;
                gap:8px;
                flex-wrap:wrap;
            }

            .oc-empty {
                text-align:center;
                padding:60px;
                color:var(--text-muted);
                background:#fff;
                border:1px dashed var(--border);
                border-radius:16px;
                margin:16px;
            }

            .oc-pagination {
                margin:18px 16px 0 16px;
                background:#fff;
                border:1px solid var(--border);
                border-radius:14px;
                padding:14px 16px;
                box-shadow:var(--shadow-sm);
            }

            .oc-pagination .pagination {
                margin:0;
                display:flex;
                flex-wrap:wrap;
                gap:6px;
            }

            .oc-pagination .page-item .page-link {
                border-radius:10px !important;
                border:1px solid var(--border);
                color:var(--text-main);
                padding:8px 12px;
                line-height:1.1;
                box-shadow:none !important;
            }

            .oc-pagination .page-item.active .page-link {
                background:var(--primary);
                border-color:var(--primary);
                color:#fff;
            }

            .oc-modal-backdrop {
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

            .oc-modal-backdrop.open {
                opacity:1;
                pointer-events:auto;
            }

            .oc-modal {
                width:100%;
                max-width:760px;
                background:#fff;
                border:1px solid rgba(229,231,235,.9);
                border-radius:16px;
                box-shadow:var(--shadow);
                transform:translateY(12px) scale(.985);
                transition:transform .22s ease;
                overflow:hidden;
            }

            .oc-modal-backdrop.open .oc-modal {
                transform:translateY(0) scale(1);
            }

            .oc-modal-h {
                display:flex;
                gap:12px;
                align-items:center;
                justify-content:space-between;
                padding:16px 18px;
                border-bottom:1px solid var(--border);
                background:#fafafa;
            }

            .oc-modal-ttl {
                font-weight:900;
                font-size:16px;
                line-height:1.2;
                margin:0;
                color:#111827;
            }

            .oc-modal-b {
                padding:20px 18px;
                max-height:72vh;
                overflow-y:auto;
            }

            .oc-modal-f {
                padding:14px 18px;
                border-top:1px solid var(--border);
                background:#fafafa;
                display:flex;
                gap:10px;
                justify-content:flex-end;
                flex-wrap:wrap;
            }

            .oc-form-grid {
                display:grid;
                grid-template-columns:repeat(2, minmax(0,1fr));
                gap:16px;
            }

            @media(max-width:760px) {
                .oc-form-grid { grid-template-columns:1fr; }
            }

            .oc-form-group { margin-bottom:16px; }

            .oc-label {
                display:block;
                font-size:13px;
                font-weight:700;
                color:var(--text-main);
                margin-bottom:6px;
            }

            .oc-input-form,
            .oc-textarea {
                width:100%;
                padding:10px 12px;
                border-radius:8px;
                border:1px solid var(--border);
                background:#fff;
                font-size:14px;
                outline:none;
                transition:var(--transition);
            }

            .oc-input-form:focus,
            .oc-textarea:focus {
                border-color:var(--primary);
                box-shadow:0 0 0 3px var(--primary-light);
            }

            .oc-textarea {
                min-height:100px;
                resize:vertical;
            }

            .leave-sidebar {
                position:fixed;
                top:0;
                right:-420px;
                width:420px;
                max-width:100%;
                height:100%;
                background:#fff;
                box-shadow:-2px 0 10px rgba(0, 0, 0, 0.15);
                z-index:9999;
                transition:right 0.3s ease-in-out;
                overflow-y:auto;
            }

            .leave-sidebar.active {
                right:0;
            }

            #mentionSuggestions li {
                padding:5px 10px;
                cursor:pointer;
            }

            #mentionSuggestions li:hover {
                background-color:#f1f1f1;
            }

            .select2-container {
                width:100% !important;
            }

            .select2-container--open {
                z-index:30002 !important;
            }

            .swal2-container {
                z-index:30000 !important;
            }

            .swal2-popup {
                z-index:30001 !important;
            }

            .leave-conflict-grid {
                display:grid;
                grid-template-columns:repeat(3, minmax(0, 1fr));
                gap:14px;
            }

            @media(max-width:1000px) {
                .leave-conflict-grid {
                    grid-template-columns:1fr;
                }
            }

            .leave-conflict-col {
                border:1px solid var(--border);
                border-radius:14px;
                background:#fff;
                padding:14px;
                min-height:360px;
            }

            .leave-conflict-title {
                font-size:13px;
                font-weight:900;
                color:#111827;
                margin-bottom:12px;
            }

            .leave-conflict-title.danger {
                color:#b91c1c;
            }

            .leave-conflict-title.success {
                color:#047857;
            }

            .leave-conflict-list {
                max-height:420px;
                overflow-y:auto;
                display:flex;
                flex-direction:column;
                gap:10px;
            }

            .leave-person-card {
                border:1px solid var(--border);
                border-radius:12px;
                padding:10px;
                display:flex;
                align-items:flex-start;
                gap:10px;
                background:#f9fafb;
            }

            .leave-person-card img {
                width:42px;
                height:42px;
                border-radius:999px;
                object-fit:cover;
                flex:0 0 auto;
            }

            .leave-person-name {
                font-size:13px;
                font-weight:900;
                color:#111827;
            }

            .leave-person-meta {
                font-size:12px;
                color:#6b7280;
                line-height:1.45;
            }

            .leave-status-mini {
                display:inline-flex;
                margin-top:4px;
                padding:3px 8px;
                border-radius:999px;
                font-size:11px;
                font-weight:900;
            }

            .leave-status-mini.success { background:#ecfdf5; color:#047857; }
            .leave-status-mini.warning { background:#fffbeb; color:#b45309; }
            .leave-status-mini.danger { background:#fef2f2; color:#b91c1c; }
            .leave-status-mini.gray { background:#f3f4f6; color:#374151; }

            @media(max-width:991px) {
                .oc-wrap {
                    padding:20px;
                    padding-right:20px;
                }
            }
        </style>
    @endpush
@endonce

@section('content')
    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">{{ $pageTitle }}</div>
                    <div class="oc-sub">
                        Verwalten Sie Ihre Urlaubsanträge, offene Anfragen und Freigaben an einem Ort.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <span>›</span>
                        <span class="current">{{ $pageTitle }}</span>
                    </div>
                </div>

                <div class="oc-inline-actions">
                    @if($canCreateLeave)
                        <button type="button" class="oc-btn new_leave" id="create_leave_btn">
                            <i class="feather icon-plus"></i>
                            Urlaub erstellen
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-list"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Gesamt</div>
                    <div class="oc-stat-value">{{ $totalCount }}</div>
                    <div class="oc-stat-sub">Meine Urlaubsanträge</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon open">
                    <i class="feather icon-clock"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Offen</div>
                    <div class="oc-stat-value">{{ $openCount }}</div>
                    <div class="oc-stat-sub">Meine offenen Anträge</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon today">
                    <i class="feather icon-sun"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Heute im Urlaub</div>
                    <div class="oc-stat-value">{{ $todayLeaveCount }}</div>
                    <div class="oc-stat-sub">Aktueller Tag</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon pending">
                    <i class="feather icon-message-square"></i>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">
                        {{ $hasAdminAccess ? 'Offene Anfragen' : 'Anfragen an mich' }}
                    </div>
                    <div class="oc-stat-value">{{ $unapprovedRequestsCount }}</div>
                    <div class="oc-stat-sub">
                        @if($newRequestsCount > 0)
                            {{ $newRequestsCount }} neue Anfrage(n)
                        @else
                            Keine neue Anfrage
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="oc-toolbar" id="notificationFilterForm">
            <input type="hidden" name="active_tab" id="active_tab" value="{{ $activeTab }}">

            <div class="oc-toolbar-left">
                <div class="oc-filter-block search">
                    <label class="oc-filter-label">Suche</label>
                    <input
                        id="search-input"
                        name="search"
                        type="text"
                        class="oc-input"
                        value="{{ $search }}"
                        placeholder="Suche nach Mitarbeiter, Datum, Status, Grund oder Beschreibung"
                    >
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Sortierung</label>
                    <select id="sort-order" name="sort" class="oc-select">
                        <option value="desc" @selected($sort === 'desc')>Neueste zuerst</option>
                        <option value="asc" @selected($sort === 'asc')>Älteste zuerst</option>
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Anfrage-Status</label>
                    <select id="request-filter" name="request_filter" class="oc-select">
                        <option value="all" @selected($filter === 'all')>Alle</option>
                        <option value="new" @selected($filter === 'new')>Neu / unbeantwortet</option>
                        <option value="unapproved" @selected($filter === 'unapproved')>Nicht genehmigt</option>
                        <option value="approved" @selected($filter === 'approved')>Genehmigt</option>
                    </select>
                </div>
            </div>

            <div class="oc-toolbar-right">
                <button id="search-btn" class="oc-btn-soft" type="submit">Anwenden</button>
                <a id="search-reset" class="oc-btn-soft" href="{{ url()->current() }}">Zurücksetzen</a>
            </div>
        </form>

        <div class="oc-card">
            <div class="oc-tabs">
                <button
                    class="oc-tab-btn {{ $activeTab === 'open' ? 'active' : '' }}"
                    id="open-tab"
                    type="button"
                    data-tab="open"
                >
                    Meine Anträge ({{ $openCount }} offen)
                </button>

                @if($hasAdminAccess)
                    <button
                        class="oc-tab-btn {{ $activeTab === 'answer' ? 'active' : '' }}"
                        id="answer-tab"
                        type="button"
                        data-tab="answer"
                    >
                        Alle Urlaubsanfragen ({{ $unapprovedRequestsCount }})

                        @if($newRequestsCount > 0)
                            <span class="oc-new-badge">{{ $newRequestsCount }} Neu</span>
                        @endif
                    </button>
                @else
                    <button
                        class="oc-tab-btn {{ $activeTab === 'answer' ? 'active' : '' }}"
                        id="answer-tab"
                        type="button"
                        data-tab="answer"
                    >
                        Anfragen an mich ({{ $pendingToMeCount }})

                        @if($newRequestsCount > 0)
                            <span class="oc-new-badge">{{ $newRequestsCount }} Neu</span>
                        @endif
                    </button>
                @endif
            </div>

            <div class="oc-pane {{ $activeTab === 'open' ? 'active' : '' }}" id="open-page">
                <div class="oc-list-head">
                    <div>ID / Zeitraum</div>
                    <div>Antrag</div>
                    <div>Status</div>
                    <div>Grund</div>
                    <div>Anfrage an</div>
                    <div>Notizen</div>
                    <div style="text-align:right;">Aktionen</div>
                </div>

                @if(isset($leave) && $leave->count() > 0)
                    <div class="oc-list">
                        @foreach($leave as $item)
                            @php
                                $requestImage = $item->rimage ? asset('images/employee/' . $item->rimage) : asset('images/gender/male.png');

                                $approvedLabel = $item->approved === 'Yes' ? 'Genehmigt' : 'Ausstehend';
                                $approvedClass = $item->approved === 'Yes' ? 'green' : 'orange';

                                $statusLabel = $item->status === 'accept' ? 'Akzeptiert' : ($item->status ?: 'Offen');
                                $statusClass = $item->status === 'accept' ? 'green' : 'gray';

                                $isNewRequest = (int) ($item->is_new_request ?? 0) === 1;
                                $isUnapproved = (int) ($item->is_unapproved ?? 0) === 1;
                            @endphp

                            <div class="oc-item {{ $isNewRequest ? 'is-new-request' : ($isUnapproved ? 'is-unapproved-request' : '') }}">
                                <div class="oc-item-row">
                                    <div class="oc-cell">
                                        <div class="oc-cell-title">ID / Zeitraum</div>
                                        <span class="oc-id-badge">#{{ $item->leave_id }}</span>

                                        <div class="oc-subt mt-1">
                                            {{ $item->start_date }} – {{ $item->end_date }}
                                        </div>

                                        @if($item->old_start)
                                            <div class="oc-subt mt-1" style="color:#d97706;">
                                                Alt: {{ $item->old_start }} – {{ $item->old_end }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Antrag</div>

                                        <div class="oc-main">
                                            <div class="oc-ttl">
                                                Urlaubsanfrage

                                                @if($isNewRequest)
                                                    <span class="oc-new-badge">Neu</span>
                                                @endif
                                            </div>

                                            <div class="oc-subt">
                                                {{ $item->duration ?? 0 }} Tag(e) · {{ $item->reason ?? 'Urlaub' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Status</div>

                                        <span class="oc-status-pill {{ $approvedClass }}">
                                            {{ $approvedLabel }}
                                        </span>

                                        <span class="oc-status-pill {{ $statusClass }} mt-1">
                                            {{ $statusLabel }}
                                        </span>

                                        @if($isNewRequest)
                                            <span class="oc-status-pill new mt-1">Neu / unbeantwortet</span>
                                        @elseif($isUnapproved)
                                            <span class="oc-status-pill unapproved mt-1">Nicht genehmigt</span>
                                        @endif
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Grund</div>

                                        <div class="oc-main">
                                            <div class="oc-ttl" style="font-size:14px;">
                                                {{ $item->reason ?? '—' }}
                                            </div>

                                            <div class="oc-subt">
                                                {{ $item->description ?? 'Keine Beschreibung' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Anfrage an</div>

                                        <div class="d-flex align-items-center">
                                            <img src="{{ $requestImage }}" class="rounded-circle mr-2" width="34" height="34" style="object-fit:cover;" alt="">

                                            <div>
                                                <div class="oc-ttl" style="font-size:13px;">
                                                    {{ $item->rlastname }} {{ $item->rname }}
                                                </div>
                                                <div class="oc-subt">Freigabe</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Notizen</div>

                                        <button type="button" class="oc-btn-ic primary leave-notes" data-id="{{ $item->leave_id }}" title="Notizen">
                                            <i class="feather icon-file-text"></i>
                                        </button>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Aktionen</div>

                                        <div class="oc-actions">
                                            <button
                                                type="button"
                                                class="oc-btn-ic warning check-leave"
                                                data-id="{{ $item->leave_id }}"
                                                data-start-date="{{ $item->start_date }}"
                                                data-end-date="{{ $item->end_date }}"
                                                data-employee-id="{{ $item->emp_id }}"
                                                title="Konflikt prüfen"
                                            >
                                                <i class="feather icon-calendar"></i>
                                            </button>

                                            @if($item->request_answer !== 'accept')
                                                <button
                                                    type="button"
                                                    class="oc-btn-ic success accept-btn"
                                                    data-leave-id="{{ $item->leave_id }}"
                                                    data-employee-id="{{ $item->emp_id }}"
                                                    title="Akzeptieren"
                                                >
                                                    <i class="feather icon-check"></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="oc-btn-ic danger reject-btn"
                                                    data-leave-id="{{ $item->leave_id }}"
                                                    data-start="{{ $item->start_date }}"
                                                    data-end="{{ $item->end_date }}"
                                                    data-employee-id="{{ $item->emp_id }}"
                                                    title="Ablehnen"
                                                >
                                                    <i class="feather icon-x"></i>
                                                </button>
                                            @endif

                                            <button type="button" class="oc-btn-ic danger delete-leave" data-id="{{ $item->leave_id }}" title="Löschen">
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($leave->hasPages())
                                        <div class="oc-pagination">
                                            {{ $leave->appends([
                            'search' => $search,
                            'sort' => $sort,
                            'request_filter' => $filter,
                            'active_tab' => 'open',
                        ])->links('pagination::bootstrap-4') }}
                                        </div>
                    @endif
                @else
                    <div class="oc-empty">Keine Anträge gefunden.</div>
                @endif
            </div>

            <div class="oc-pane {{ $activeTab === 'answer' ? 'active' : '' }}" id="answer-page">
                <div class="oc-list-head">
                    <div>ID / Zeitraum</div>
                    <div>Antrag</div>
                    <div>Status</div>
                    <div>Grund</div>
                    <div>Mitarbeiter</div>
                    <div>Notizen</div>
                    <div style="text-align:right;">Aktionen</div>
                </div>

                @if(isset($response) && $response->count() > 0)
                    <div class="oc-list">
                        @foreach($response as $item)
                            @php
                                $empImage = $item->emp_image ? asset('images/employee/' . $item->emp_image) : asset('images/gender/male.png');

                                $approvedLabel = $item->approved === 'Yes' ? 'Genehmigt' : 'Ausstehend';
                                $approvedClass = $item->approved === 'Yes' ? 'green' : 'orange';

                                $statusLabel = $item->status ?: 'Offen';
                                $statusClass = $item->status === 'accept' ? 'green' : 'gray';

                                $isNewRequest = (int) ($item->is_new_request ?? 0) === 1;
                                $isUnapproved = (int) ($item->is_unapproved ?? 0) === 1;
                            @endphp

                            <div class="oc-item {{ $isNewRequest ? 'is-new-request' : ($isUnapproved ? 'is-unapproved-request' : '') }}">
                                <div class="oc-item-row">
                                    <div class="oc-cell">
                                        <div class="oc-cell-title">ID / Zeitraum</div>

                                        <span class="oc-id-badge">#{{ $item->leave_id }}</span>

                                        <div class="oc-subt mt-1">
                                            {{ $item->start_date }} – {{ $item->end_date }}
                                        </div>

                                        @if($item->old_start)
                                            <div class="oc-subt mt-1" style="color:#d97706;">
                                                Alt: {{ $item->old_start }} – {{ $item->old_end }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Antrag</div>

                                        <div class="oc-main">
                                            <div class="oc-ttl">
                                                Urlaubsanfrage

                                                @if($isNewRequest)
                                                    <span class="oc-new-badge">Neu</span>
                                                @endif
                                            </div>

                                            <div class="oc-subt">
                                                {{ $item->duration ?? 0 }} Tag(e) · {{ $item->reason ?? 'Urlaub' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Status</div>

                                        <span class="oc-status-pill {{ $approvedClass }}">
                                            {{ $approvedLabel }}
                                        </span>

                                        @if($item->status !== 'accept')
                                            <span class="oc-status-pill {{ $statusClass }} mt-1">
                                                {{ $statusLabel }}
                                            </span>
                                        @endif

                                        @if($isNewRequest)
                                            <span class="oc-status-pill new mt-1">Neu / unbeantwortet</span>
                                        @elseif($isUnapproved)
                                            <span class="oc-status-pill unapproved mt-1">Nicht genehmigt</span>
                                        @endif
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Grund</div>

                                        <div class="oc-main">
                                            <div class="oc-ttl" style="font-size:14px;">
                                                {{ $item->reason ?? '—' }}
                                            </div>

                                            <div class="oc-subt">
                                                {{ $item->description ?? 'Keine Beschreibung' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Mitarbeiter</div>

                                        <div class="d-flex align-items-center">
                                            <img src="{{ $empImage }}" class="rounded-circle mr-2" width="34" height="34" style="object-fit:cover;" alt="">

                                            <div>
                                                <div class="oc-ttl" style="font-size:13px;">
                                                    {{ $item->emp_lastname }} {{ $item->emp_name }}
                                                </div>
                                                <div class="oc-subt">Antragsteller</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Notizen</div>

                                        <button type="button" class="oc-btn-ic primary leave-notes" data-id="{{ $item->leave_id }}" title="Notizen">
                                            <i class="feather icon-file-text"></i>
                                        </button>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Aktionen</div>

                                        <div class="oc-actions">
                                            <button
                                                type="button"
                                                class="oc-btn-ic warning check-leave"
                                                data-id="{{ $item->leave_id }}"
                                                data-start-date="{{ $item->start_date }}"
                                                data-end-date="{{ $item->end_date }}"
                                                data-employee-id="{{ $item->emp_id }}"
                                                title="Konflikt prüfen"
                                            >
                                                <i class="feather icon-calendar"></i>
                                            </button>

                                            @if($item->approved !== 'Yes')
                                                <button
                                                    type="button"
                                                    class="oc-btn-ic success approve-btn"
                                                    data-leave-id="{{ $item->leave_id }}"
                                                    data-employee-id="{{ $item->emp_id }}"
                                                    title="Genehmigen"
                                                >
                                                    <i class="feather icon-check-circle"></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="oc-btn-ic danger change-btn"
                                                    data-leave-id="{{ $item->leave_id }}"
                                                    data-start="{{ $item->start_date }}"
                                                    data-end="{{ $item->end_date }}"
                                                    data-employee-id="{{ $item->emp_id }}"
                                                    title="Ablehnen"
                                                >
                                                    <i class="feather icon-x-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($response->hasPages())
                                        <div class="oc-pagination">
                                            {{ $response->appends([
                            'search' => $search,
                            'sort' => $sort,
                            'request_filter' => $filter,
                            'active_tab' => 'answer',
                        ])->links('pagination::bootstrap-4') }}
                                        </div>
                    @endif
                @else
                    <div class="oc-empty">Keine Anfragen gefunden.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="oc-modal-backdrop" id="new_leave_modal_wrap">
        <div class="oc-modal">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl">Urlaub erstellen</h3>

                <button class="oc-btn-ic" type="button" onclick="closeModal('new_leave_modal_wrap')">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('leave.store') }}" id="new_leave_form">
                @csrf

                <div class="oc-modal-b">
                    <input type="hidden" name="active_tab" value="leave">
                    <input type="hidden" name="department_id" id="department_id">

                    <div class="oc-form-grid">
                        <div class="oc-form-group" style="grid-column:1 / -1;">
                            <label class="oc-label">Mitarbeiter</label>
                            <select name="emp_id" id="emp_select" class="oc-select" style="width:100%"></select>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Jahr</label>
                            <select name="year" id="yearSelect" class="oc-select">
                                <option value="">Jahr wählen</option>
                            </select>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Grund</label>
                            <select class="oc-select" name="reason">
                                <option value="Urlaub" selected>Urlaub</option>
                                <option value="Freizeitausgleich">Freizeitausgleich</option>
                                <option value="Vorjahresurlaub">Vorjahresurlaub</option>
                                <option value="Elternzeit">Elternzeit</option>
                                <option value="Schulung">Schulung</option>
                                <option value="Schule">Schule</option>
                                <option value="Unbezahte Urlaub">Unbezahte Urlaub</option>
                                <option value="Freigeschtilt">Freigeschtilt</option>
                            </select>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Ab Datum</label>
                            <input type="date" class="oc-input-form leave_start_date" name="start_date">
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Bis Datum</label>
                            <input type="date" class="oc-input-form leave_end_date" name="end_date">
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Urlaubstage</label>
                            <input type="number" class="oc-input-form leave_day" name="leave_day">
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Resturlaubstage</label>
                            <input type="number" class="oc-input-form remaining_day" name="remaining_day">
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Urlaubstage letztes Jahr</label>
                            <input type="number" class="oc-input-form last_year_remainings" name="last_year_remainings" readonly>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Eingereichte Urlaubstage</label>
                            <input type="number" class="oc-input-form leave_duration" name="duration">

                            <label class="duration_label" style="color:red; display:none;">
                                Die Dauer überschreitet die zulässigen Urlaubstage
                            </label>
                        </div>

                        <div class="oc-form-group" style="grid-column:1 / -1;">
                            <label class="oc-label">Anfrage an (Abteilungsleiter)</label>
                            <select class="oc-select request_to" id="employee_leader_select" name="request_to" style="width:100%"></select>
                        </div>

                        <div class="oc-form-group" style="grid-column:1 / -1;">
                            <label class="oc-label">Beschreibung</label>
                            <textarea name="description" class="oc-textarea"></textarea>
                        </div>
                    </div>
                </div>

                <div class="oc-modal-f">
                    <button type="button" class="oc-btn-soft" onclick="closeModal('new_leave_modal_wrap')">Abbrechen</button>
                    <button type="button" class="oc-btn save_button">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    <div id="leaveNotesSidebar" class="leave-sidebar p-3">
        <div class="d-flex justify-content-between align-items-center mb-3" style="background:#8fc73e; padding:.75rem 1rem; border-radius:10px;">
            <h5 class="mb-0">
                <i class="feather icon-edit-3 mr-25"></i> Notizen
            </h5>

            <button onclick="closeLeaveSidebar()" class="btn btn-sm btn-danger">×</button>
        </div>

        <div id="leaveNotesContent" class="mb-3"></div>

        <div class="position-relative">
            <textarea id="newNoteText" class="oc-textarea mb-2" rows="3" placeholder="Neue Notiz… @Mitarbeiter"></textarea>
            <ul id="mentionSuggestions" class="list-group position-absolute bg-white border" style="top:100%; left:0; width:100%; z-index:9999; display:none;"></ul>
        </div>

        <button class="oc-btn w-100 mt-2" onclick="saveLeaveNote()">
            <i class="feather icon-save mr-25"></i> Speichern
        </button>
    </div>

    <div class="oc-modal-backdrop" id="leaveConfirmModal">
        <div class="oc-modal" style="max-width:520px;">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl" id="leaveConfirmTitle">Bestätigung</h3>

                <button type="button" class="oc-btn-ic" onclick="closeModal('leaveConfirmModal')">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="oc-modal-b">
                <p id="leaveConfirmText" style="font-size:14px;color:#4b5563;margin:0;">
                    Möchten Sie diese Aktion ausführen?
                </p>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('leaveConfirmModal')">
                    Abbrechen
                </button>

                <button type="button" class="oc-btn" id="leaveConfirmSubmit">
                    Bestätigen
                </button>
            </div>
        </div>
    </div>

    <div class="oc-modal-backdrop" id="leaveRejectModal">
        <div class="oc-modal" style="max-width:560px;">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl" id="leaveRejectTitle">Urlaub ablehnen</h3>

                <button type="button" class="oc-btn-ic" onclick="closeModal('leaveRejectModal')">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="oc-modal-b">
                <input type="hidden" id="reject_leave_id">
                <input type="hidden" id="reject_employee_id">
                <input type="hidden" id="reject_action_type">
                <input type="hidden" id="reject_route_type">

                <div class="oc-form-grid">
                    <div class="oc-form-group">
                        <label class="oc-label">Neues Startdatum</label>
                        <input type="date" id="reject_start_date" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Neues Enddatum</label>
                        <input type="date" id="reject_end_date" class="oc-input-form">
                    </div>

                    <div class="oc-form-group" style="grid-column:1 / -1;">
                        <label class="oc-label">Kommentar</label>
                        <textarea id="reject_comment" class="oc-textarea" placeholder="Optionaler Grund oder Hinweis..."></textarea>
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('leaveRejectModal')">
                    Abbrechen
                </button>

                <button type="button" class="oc-btn" id="leaveRejectSubmit" style="background:var(--danger);">
                    Ablehnen
                </button>
            </div>
        </div>
    </div>

    <div class="oc-modal-backdrop" id="leaveConflictModal">
        <div class="oc-modal" style="max-width:1180px;">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl">Abteilungsübersicht</h3>

                <button type="button" class="oc-btn-ic" onclick="closeModal('leaveConflictModal')">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="oc-modal-b">
                <div class="leave-conflict-grid">
                    <div class="leave-conflict-col">
                        <h6 class="leave-conflict-title danger">
                            <span id="conflictCount">0</span> im Urlaub
                        </h6>

                        <div id="conflictList" class="leave-conflict-list"></div>
                    </div>

                    <div class="leave-conflict-col">
                        <h6 class="leave-conflict-title success">
                            <span id="presentCount">0</span> anwesend
                        </h6>

                        <div id="presentList" class="leave-conflict-list"></div>
                    </div>

                    <div class="leave-conflict-col">
                        <h6 class="leave-conflict-title">Kalender</h6>
                        <div id="leave-calendar"></div>
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('leaveConflictModal')">
                    Schließen
                </button>
            </div>
        </div>
    </div>
@endsection

@once
    @push('scripts')
        <script>
            function openModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.add('open');
            }

            function closeModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.remove('open');
            }

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('oc-modal-backdrop')) {
                    e.target.classList.remove('open');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const form = document.getElementById('notificationFilterForm');
                const activeTabInput = document.getElementById('active_tab');

                document.querySelectorAll('.oc-tab-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const tab = this.dataset.tab || 'open';

                        document.querySelectorAll('.oc-tab-btn').forEach(item => item.classList.remove('active'));
                        document.querySelectorAll('.oc-pane').forEach(item => item.classList.remove('active'));

                        this.classList.add('active');

                        if (tab === 'answer') {
                            document.getElementById('answer-page')?.classList.add('active');
                        } else {
                            document.getElementById('open-page')?.classList.add('active');
                        }

                        if (activeTabInput) {
                            activeTabInput.value = tab;
                        }
                    });
                });

                document.getElementById('sort-order')?.addEventListener('change', function () {
                    form?.submit();
                });

                document.getElementById('request-filter')?.addEventListener('change', function () {
                    form?.submit();
                });

                if (window.feather) {
                    window.feather.replace();
                }

                let currentLeaveId = null;
                let employeesList = [];
                const baseUrl = window.location.origin;

                fetch('/get-employee-usernames')
                    .then(res => res.json())
                    .then(data => employeesList = data || [])
                    .catch(() => employeesList = []);

                window.closeLeaveSidebar = function () {
                    document.getElementById('leaveNotesSidebar')?.classList.remove('active');
                    currentLeaveId = null;
                };

                function renderLeaveNotes(notes) {
                    const content = document.getElementById('leaveNotesContent');
                    if (!content) return;

                    content.innerHTML = '';

                    if (!Array.isArray(notes)) {
                        notes = [];
                    }

                    if (!notes.length) {
                        content.innerHTML = '<div class="oc-empty" style="margin:0;padding:24px;">Keine Notizen vorhanden.</div>';
                        return;
                    }

                    notes.forEach((note, index) => {
                        const image = note.image
                            ? `${baseUrl}/images/employee/${note.image}`
                            : `${baseUrl}/images/gender/male.png`;

                        content.innerHTML += `
                            <div class="border p-2 mb-2 d-flex" style="border-radius:10px;">
                                <img src="${image}" alt="${note.employee || ''}" class="rounded-circle mr-2" style="width:40px;height:40px;object-fit:cover;">
                                <div class="flex-grow-1">
                                    <small><strong>${note.employee || ''}</strong> – ${note.date || ''}</small>
                                    <p class="mb-1">${note.text || ''}</p>
                                    <button class="btn btn-sm btn-warning" onclick="editLeaveNote(${index})">
                                        <i class="feather icon-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteLeaveNote(${index})">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });

                    if (window.feather) {
                        window.feather.replace();
                    }
                }

                function loadLeaveNotes() {
                    if (!currentLeaveId) return;

                    fetch(`/leaves/${currentLeaveId}/notes`)
                        .then(res => res.json())
                        .then(data => renderLeaveNotes(data || []))
                        .catch(err => console.error('Fehler beim Laden der Notizen:', err));
                }

                document.addEventListener('click', function (event) {
                    const notesBtn = event.target.closest('.leave-notes');

                    if (!notesBtn) return;

                    currentLeaveId = notesBtn.dataset.id;
                    document.getElementById('leaveNotesSidebar')?.classList.add('active');
                    loadLeaveNotes();
                });

                window.saveLeaveNote = function () {
                    const input = document.getElementById('newNoteText');
                    const text = input?.value || '';

                    if (!text.trim() || !currentLeaveId) return;

                    fetch(`/leaves/${currentLeaveId}/notes/store`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ text })
                    })
                    .then(res => res.json())
                    .then(data => {
                        input.value = '';
                        renderLeaveNotes(data.notes || []);
                    });
                };

                window.deleteLeaveNote = function (index) {
                    if (!currentLeaveId) return;

                    Swal.fire({
                        title: 'Löschen?',
                        text: 'Diese Notiz wirklich entfernen?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ja, löschen',
                        cancelButtonText: 'Abbrechen'
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        fetch(`/leaves/${currentLeaveId}/notes/delete/${index}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => renderLeaveNotes(data.notes || []));
                    });
                };

                window.editLeaveNote = function (index) {
                    if (!currentLeaveId) return;

                    const newText = prompt('Neue Notiz eingeben:');

                    if (!newText) return;

                    fetch(`/leaves/${currentLeaveId}/notes/update/${index}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ text: newText })
                    })
                    .then(res => res.json())
                    .then(data => renderLeaveNotes(data.notes || []));
                };

                const noteInput = document.getElementById('newNoteText');

                noteInput?.addEventListener('input', function () {
                    const val = this.value;
                    const caretPos = this.selectionStart;
                    const match = val.substring(0, caretPos).match(/@([\w\.]*)$/);
                    const suggestionBox = document.getElementById('mentionSuggestions');

                    if (!suggestionBox) return;

                    if (match) {
                        const term = match[1].toLowerCase();
                        const matches = employeesList
                            .filter(name => String(name).toLowerCase().includes(term))
                            .slice(0, 5);

                        suggestionBox.innerHTML = '';

                        matches.forEach(name => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.textContent = name;

                            li.onclick = () => {
                                noteInput.value = val.substring(0, caretPos - match[0].length) + `@${name} ` + val.substring(caretPos);
                                noteInput.focus();
                                suggestionBox.style.display = 'none';
                            };

                            suggestionBox.appendChild(li);
                        });

                        suggestionBox.style.display = matches.length ? 'block' : 'none';
                    } else {
                        suggestionBox.style.display = 'none';
                    }
                });

                const routes = {
                    accept: "{{ route('accept.leave.date') }}",
                    change: "{{ route('change.leave.date') }}"
                };

                let confirmCallback = null;

                function showToast(title, text, type = 'success') {
                    if (window.toastr) {
                        type === 'error' ? toastr.error(text, title) : toastr.success(text, title);
                        return;
                    }

                    if (window.Swal) {
                        Swal.fire(title, text, type);
                        return;
                    }

                    alert(title + '\n' + text);
                }

                function openConfirmModal(title, text, callback) {
                    document.getElementById('leaveConfirmTitle').innerText = title;
                    document.getElementById('leaveConfirmText').innerText = text;
                    confirmCallback = callback;
                    openModal('leaveConfirmModal');
                }

                document.getElementById('leaveConfirmSubmit')?.addEventListener('click', function () {
                    if (typeof confirmCallback === 'function') {
                        confirmCallback();
                    }

                    confirmCallback = null;
                    closeModal('leaveConfirmModal');
                });

                function sendLeaveRequest(route, payload) {
                    return fetch(route, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.error || 'Fehler beim Verarbeiten der Anfrage.');
                        }

                        showToast('Erfolg', 'Die Anfrage wurde erfolgreich bearbeitet.');
                        setTimeout(() => location.reload(), 700);
                    })
                    .catch(error => {
                        showToast('Fehler', error.message, 'error');
                    });
                }

                document.addEventListener('click', function (event) {
                    const acceptBtn = event.target.closest('.accept-btn');

                    if (acceptBtn) {
                        const leaveId = acceptBtn.dataset.leaveId;
                        const employeeId = acceptBtn.dataset.employeeId;

                        openConfirmModal(
                            'Urlaub akzeptieren',
                            'Möchten Sie diesen Urlaub wirklich akzeptieren?',
                            function () {
                                sendLeaveRequest(routes.accept, {
                                    leave_id: leaveId,
                                    employee_id: employeeId,
                                    type: 'accept'
                                });
                            }
                        );
                    }

                    const approveBtn = event.target.closest('.approve-btn');

                    if (approveBtn) {
                        const leaveId = approveBtn.dataset.leaveId;
                        const employeeId = approveBtn.dataset.employeeId;

                        openConfirmModal(
                            'Urlaub genehmigen',
                            'Möchten Sie diesen Mitarbeiterurlaub wirklich genehmigen?',
                            function () {
                                sendLeaveRequest(routes.change, {
                                    leave_id: leaveId,
                                    employee_id: employeeId,
                                    type: 'accept'
                                });
                            }
                        );
                    }

                    const rejectBtn = event.target.closest('.reject-btn');

                    if (rejectBtn) {
                        document.getElementById('leaveRejectTitle').innerText = 'Urlaub ablehnen';
                        document.getElementById('reject_leave_id').value = rejectBtn.dataset.leaveId || '';
                        document.getElementById('reject_employee_id').value = rejectBtn.dataset.employeeId || '';
                        document.getElementById('reject_start_date').value = rejectBtn.dataset.start || '';
                        document.getElementById('reject_end_date').value = rejectBtn.dataset.end || '';
                        document.getElementById('reject_action_type').value = 'reject';
                        document.getElementById('reject_route_type').value = 'accept';
                        document.getElementById('reject_comment').value = '';
                        openModal('leaveRejectModal');
                    }

                    const changeBtn = event.target.closest('.change-btn');

                    if (changeBtn) {
                        document.getElementById('leaveRejectTitle').innerText = 'Änderung ablehnen';
                        document.getElementById('reject_leave_id').value = changeBtn.dataset.leaveId || '';
                        document.getElementById('reject_employee_id').value = changeBtn.dataset.employeeId || '';
                        document.getElementById('reject_start_date').value = changeBtn.dataset.start || '';
                        document.getElementById('reject_end_date').value = changeBtn.dataset.end || '';
                        document.getElementById('reject_action_type').value = 'reject';
                        document.getElementById('reject_route_type').value = 'change';
                        document.getElementById('reject_comment').value = '';
                        openModal('leaveRejectModal');
                    }
                });

                document.getElementById('leaveRejectSubmit')?.addEventListener('click', function () {
                    const leaveId = document.getElementById('reject_leave_id').value;
                    const employeeId = document.getElementById('reject_employee_id').value;
                    const startDate = document.getElementById('reject_start_date').value;
                    const endDate = document.getElementById('reject_end_date').value;
                    const type = document.getElementById('reject_action_type').value;
                    const routeType = document.getElementById('reject_route_type').value;
                    const comment = document.getElementById('reject_comment').value;

                    if (!startDate || !endDate) {
                        showToast('Fehler', 'Bitte geben Sie Start- und Enddatum ein.', 'error');
                        return;
                    }

                    closeModal('leaveRejectModal');

                    sendLeaveRequest(routeType === 'change' ? routes.change : routes.accept, {
                        leave_id: leaveId,
                        employee_id: employeeId,
                        start_date: startDate,
                        end_date: endDate,
                        type: type,
                        comment: comment
                    });
                });

                function statusClass(status) {
                    const value = String(status || '').toLowerCase();

                    if (value === 'approved' || value === 'accept' || value === 'yes') return 'success';
                    if (value === 'pending') return 'warning';
                    if (value === 'rejected' || value === 'no') return 'danger';

                    return 'gray';
                }

                function employeeImage(image) {
                    return image ? `/images/employee/${image}` : `/images/gender/male.png`;
                }

                function renderConflictList(data) {
                    const conflictList = document.getElementById('conflictList');
                    const presentList = document.getElementById('presentList');

                    document.getElementById('conflictCount').innerText = data.conflict_count || 0;
                    document.getElementById('presentCount').innerText = data.present_count || 0;

                    conflictList.innerHTML = '';

                    (data.conflicts || []).forEach(item => {
                        conflictList.innerHTML += `
                            <div class="leave-person-card">
                                <img src="${employeeImage(item.image)}" alt="">
                                <div>
                                    <div class="leave-person-name">${item.name || ''} ${item.lastname || ''}</div>
                                    <div class="leave-person-meta">${item.position || ''} – ${item.department_name || ''}</div>
                                    <div class="leave-person-meta">📅 ${item.start_date || ''} → ${item.end_date || ''}</div>
                                    <span class="leave-status-mini ${statusClass(item.status)}">${item.status || '—'}</span>
                                </div>
                            </div>
                        `;
                    });

                    if (!data.conflicts || !data.conflicts.length) {
                        conflictList.innerHTML = `<div class="oc-empty" style="margin:0;padding:28px;">Keine Konflikte gefunden.</div>`;
                    }

                    presentList.innerHTML = '';

                    (data.present || []).forEach(item => {
                        let departments = '';

                        (item.departments || []).forEach(dep => {
                            departments += `<div>${dep.position || ''} – ${dep.department_name || ''}</div>`;
                        });

                        presentList.innerHTML += `
                            <div class="leave-person-card">
                                <img src="${employeeImage(item.image)}" alt="">
                                <div>
                                    <div class="leave-person-name">${item.name || ''} ${item.lastname || ''}</div>
                                    <div class="leave-person-meta">${departments}</div>
                                </div>
                            </div>
                        `;
                    });

                    if (!data.present || !data.present.length) {
                        presentList.innerHTML = `<div class="oc-empty" style="margin:0;padding:28px;">Keine anwesenden Mitarbeiter gefunden.</div>`;
                    }
                }

                document.addEventListener('click', function (event) {
                    const btn = event.target.closest('.check-leave');

                    if (!btn) return;

                    const employeeId = btn.dataset.employeeId;
                    const startDate = btn.dataset.startDate;
                    const endDate = btn.dataset.endDate;

                    fetch(`/check/department-holidays/${employeeId}/${startDate}/${endDate}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        renderConflictList(data || {});
                        openModal('leaveConflictModal');

                        setTimeout(() => {
                            const calendar = document.getElementById('leave-calendar');

                            if (calendar && window.Litepicker) {
                                calendar.innerHTML = '';

                                new Litepicker({
                                    element: calendar,
                                    inlineMode: true,
                                    singleMode: false,
                                    showTooltip: false,
                                    startDate: startDate,
                                    endDate: endDate,
                                    numberOfMonths: 1,
                                    numberOfColumns: 1
                                });
                            }
                        }, 80);
                    })
                    .catch(() => {
                        showToast('Fehler', 'Daten konnten nicht geladen werden.', 'error');
                    });
                });
            });
        </script>

        <script>
            const path_image = "{{ asset('images/employee') }}";

            $(document).ready(function () {
                $('#emp_select').select2({
                    placeholder: 'Mitarbeiter auswählen',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#new_leave_modal_wrap'),
                    templateResult: formatEmployee,
                    templateSelection: formatEmployee
                });

                $('#employee_leader_select').select2({
                    placeholder: 'Abteilungsleiter auswählen',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#new_leave_modal_wrap'),
                    templateResult: formatEmployee,
                    templateSelection: formatEmployee
                });

                function formatEmployee(employee) {
                    if (!employee.id) return employee.text;

                    const image = $(employee.element).data('img') || '/default-avatar.png';

                    return $(`<span><img src="${image}" class="rounded-circle" width="30" height="30" style="margin-right:10px;object-fit:cover;"> ${employee.text}</span>`);
                }

                function populateYearDropdown() {
                    const currentYear = new Date().getFullYear();
                    const yearSelect = document.getElementById('yearSelect');

                    if (!yearSelect) return;

                    yearSelect.innerHTML = '';

                    for (let year = currentYear - 5; year <= currentYear + 1; year++) {
                        yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
                    }

                    yearSelect.value = currentYear;
                }

                function calculateWorkingDays(startDate, endDate) {
                    let start = new Date(startDate);
                    let end = new Date(endDate);
                    let count = 0;

                    while (start <= end) {
                        const day = start.getDay();

                        if (day !== 0 && day !== 6) {
                            count++;
                        }

                        start.setDate(start.getDate() + 1);
                    }

                    return count;
                }

                function updateDuration(modal) {
                    const start = modal.querySelector('.leave_start_date')?.value;
                    const end = modal.querySelector('.leave_end_date')?.value;
                    const leaveDays = parseInt(modal.querySelector('.leave_day')?.value) || 0;
                    const durationInput = modal.querySelector('.leave_duration');
                    const remainingInput = modal.querySelector('.remaining_day');
                    const label = modal.querySelector('.duration_label');
                    const saveBtn = modal.querySelector('.save_button');

                    if (!start || !end || !durationInput || !remainingInput) return;

                    const workDays = calculateWorkingDays(start, end);

                    durationInput.value = workDays;
                    remainingInput.value = Math.max(leaveDays - workDays, 0);

                    if (workDays > leaveDays) {
                        if (label) label.style.display = 'block';
                        if (saveBtn) saveBtn.style.display = 'none';

                        Swal.fire('Achtung!', 'Sie haben mehr Urlaubstage beantragt als verfügbar!', 'warning');
                    } else {
                        if (label) label.style.display = 'none';
                        if (saveBtn) saveBtn.style.display = 'inline-flex';
                    }
                }

                function fetchLeaveDays(empId, year) {
                    if (!empId) return;

                    fetch(`/employee/remaining/days/${empId}?year=${year}`)
                        .then(res => res.json())
                        .then(data => {
                            $('.leave_day').val(data.total_leave_days || 0);
                            $('.remaining_day').val(data.remaining_days || 0);
                            $('.last_year_remainings').val(data.last_year_remainings || 0);
                        });
                }

                function loadLeaders(departmentId) {
                    $.get(`/getDepartment/leader/${departmentId}`, function (leaders) {
                        const $select = $('#employee_leader_select');

                        $select.empty().append('<option value="">Leiter auswählen</option>');

                        leaders.forEach(leader => {
                            const img = leader.image ? `${path_image}/${leader.image}` : '/default-avatar.png';
                            const option = new Option(`${leader.name} ${leader.lastname}`, leader.emp_id, false, false);

                            $(option).attr('data-img', img);
                            $select.append(option);
                        });

                        $select.trigger('change');
                    });
                }

                function getDepartment(empId) {
                    return fetch(`/employee/${empId}/main-department`).then(res => res.json());
                }

                $('.new_leave, #create_leave_btn').on('click', function () {
                    populateYearDropdown();
                    openModal('new_leave_modal_wrap');
                });

                $('#emp_select').on('change', async function () {
                    const empId = $(this).val();
                    const year = $('#yearSelect').val();

                    fetchLeaveDays(empId, year);

                    if (empId) {
                        const data = await getDepartment(empId);

                        $('#department_id').val(data.department_id || '');

                        if (data.department_id) {
                            loadLeaders(data.department_id);
                        }
                    }
                });

                $('#yearSelect').on('change', function () {
                    fetchLeaveDays($('#emp_select').val(), $(this).val());
                });

                $('.leave_start_date, .leave_end_date').on('change', function () {
                    const modal = document.getElementById('new_leave_modal_wrap');
                    updateDuration(modal);
                });

                $('.save_button').on('click', function () {
                    $('#new_leave_form').submit();
                });
            });
        </script>
    @endpush
@endonce