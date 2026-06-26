@extends('admin.layouts.app')

@section('title', 'MITARBEITER DETAILS')

@section('style')
    <style>
        :root {
            --app-bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --primary: #8fc73e;
            --primary-hover: #7baa18;
            --primary-light: #f4fae7;
            --blue: #74b2d4;
            --blue-light: #eff6ff;
            --success: #10b981;
            --success-light: #ecfdf5;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --gray: #6b7280;
            --gray-light: #f3f4f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --radius: 16px;
            --transition: all .2s ease-in-out;
        }

        .oc-wrap {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text-main);
            margin: 20px auto;
            padding-right: 79px;
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

        .oc-title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -.025em;
            color: #111827;
            text-transform: uppercase;
        }

        .oc-sub {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .oc-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .oc-breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 800;
        }

        .oc-breadcrumb a:hover {
            color: var(--text-main);
        }

        .oc-breadcrumb span.current {
            color: #111827;
            font-weight: 900;
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
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            line-height: 1.2;
        }

        .oc-btn:hover {
            background: var(--primary-hover);
            color: #fff;
            text-decoration: none;
        }

        .oc-btn-danger {
            background: var(--danger);
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            line-height: 1.2;
        }

        .oc-btn-danger:hover {
            background: #dc2626;
            color: #fff;
            text-decoration: none;
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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .oc-btn-soft:hover {
            background: #f9fafb;
            color: var(--text-main);
            text-decoration: none;
        }

        .oc-btn-ic {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            flex: 0 0 auto;
        }

        .oc-btn-ic:hover {
            background: #f9fafb;
            color: var(--text-main);
            border-color: #d1d5db;
            text-decoration: none;
        }

        .oc-btn-ic.primary {
            color: var(--blue);
            border-color: var(--blue-light);
            background: var(--blue-light);
        }

        .oc-btn-ic.success {
            color: var(--success);
            border-color: var(--success-light);
            background: var(--success-light);
        }

        .oc-btn-ic.warning {
            color: #d97706;
            border-color: var(--warning-light);
            background: var(--warning-light);
        }

        .oc-btn-ic.danger {
            color: var(--danger);
            border-color: rgba(239, 68, 68, .18);
            background: var(--danger-light);
        }

        .oc-analytics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        @media(max-width:1200px) {
            .oc-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:700px) {
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
        }

        .oc-stat-icon.total {
            background: var(--blue-light);
            color: var(--blue);
        }

        .oc-stat-icon.active {
            background: var(--success-light);
            color: var(--success);
        }

        .oc-stat-icon.inactive {
            background: var(--gray-light);
            color: var(--gray);
        }

        .oc-stat-icon.codes {
            background: var(--warning-light);
            color: #d97706;
        }

        .oc-stat-label {
            font-size: 11px;
            font-weight: 900;
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

        .oc-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .oc-card-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .oc-card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
        }

        .oc-card-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .oc-card-body {
            padding: 18px;
        }

        .oc-tabs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .oc-tab {
            min-height: 42px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            color: #374151;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            transition: var(--transition);
        }

        .oc-tab:hover {
            background: #f9fafb;
            color: #111827;
            text-decoration: none;
        }

        .oc-tab.active {
            background: var(--primary-light);
            color: #365314;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(143, 199, 62, .12);
        }

        .oc-tab-count {
            min-width: 24px;
            height: 24px;
            padding: 0 8px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #374151;
            font-size: 12px;
            font-weight: 900;
        }

        .oc-tab.active .oc-tab-count {
            background: #fff;
            color: #365314;
        }

        .oc-filter-form {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 190px 170px auto;
            gap: 12px;
            align-items: end;
        }

        @media(max-width:1000px) {
            .oc-filter-form {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media(max-width:650px) {
            .oc-filter-form {
                grid-template-columns: 1fr;
            }
        }

        .oc-form-group {
            min-width: 0;
        }

        .oc-label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 7px;
        }

        .oc-input,
        .oc-select {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            color: #111827;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            min-height: 42px;
        }

        .oc-input:focus,
        .oc-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .oc-table-wrap {
            overflow-x: auto;
        }

        .oc-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            min-width: 1120px;
        }

        .oc-table thead th {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 8px 10px;
            border: 0;
            white-space: nowrap;
            text-align: left;
        }

        .oc-table thead th.text-right {
            text-align: right;
        }

        .oc-table tbody tr {
            background: #fff;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .oc-table tbody td {
            padding: 12px 10px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            background: #fff;
        }

        .oc-table tbody td:first-child {
            border-left: 1px solid var(--border);
            border-radius: 14px 0 0 14px;
        }

        .oc-table tbody td:last-child {
            border-right: 1px solid var(--border);
            border-radius: 0 14px 14px 0;
        }

        .emp-index-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .emp-row-number {
            min-width: 28px;
            height: 28px;
            border-radius: 10px;
            background: #f9fafb;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            color: #111827;
        }

        .emp-color-form {
            margin: 0;
            display: inline-flex;
            align-items: center;
        }

        .emp-color-input {
            border: none;
            background: transparent;
            padding: 0;
            width: 28px;
            height: 28px;
            cursor: pointer;
            border-radius: 999px;
            overflow: hidden;
        }

        .emp-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 300px;
        }

        .emp-avatar-btn {
            width: 48px;
            height: 48px;
            padding: 0;
            border: none;
            background: transparent;
            border-radius: 999px;
            overflow: hidden;
            cursor: pointer;
            flex: 0 0 auto;
            box-shadow: 0 0 0 3px #fff, 0 0 0 4px var(--border);
        }

        .emp-avatar-btn img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            display: block;
        }

        .emp-name-link {
            color: #111827;
            font-weight: 900;
            text-decoration: none;
            font-size: 14px;
            line-height: 1.2;
        }

        .emp-name-link:hover {
            color: var(--blue);
            text-decoration: none;
        }

        .emp-contact-line {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.35;
        }

        .emp-contact-line a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .emp-contact-line a:hover {
            color: #111827;
            text-decoration: underline;
        }

        .oc-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            line-height: 1.1;
            white-space: nowrap;
        }

        .oc-badge.blue {
            background: var(--blue-light);
            color: #075985;
        }

        .oc-badge.gray {
            background: var(--gray-light);
            color: #374151;
        }

        .oc-badge.primary {
            background: var(--primary-light);
            color: #365314;
        }

        .oc-badge.success {
            background: var(--success-light);
            color: #047857;
        }

        .oc-badge.danger {
            background: var(--danger-light);
            color: #b91c1c;
        }

        .oc-badge.warning {
            background: var(--warning-light);
            color: #b45309;
        }

        .emp-badge-stack {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .emp-position-row,
        .emp-postcode-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 6px;
        }

        .emp-position-box {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            max-width: 360px;
        }

        .emp-position-main {
            padding: 8px;
            border: 1px solid rgba(16, 185, 129, .18);
            border-radius: 12px;
            background: var(--success-light);
            width: 100%;
        }

        .emp-position-side-wrap {
            width: 100%;
        }

        .emp-position-more-btn {
            border:none;
            background:var(--primary-light);
            color:#365314;
            border-radius:999px;
            padding:6px 10px;
            font-size:11px;
            font-weight:900;
            display:inline-flex;
            align-items:center;
            gap:5px;
            cursor:pointer;
            line-height:1.1;
            margin-top:2px;
        }

        .emp-position-more-btn:hover {
            background:#e7f4cc;
        }

        .emp-position-muted {
            font-size:11px;
            font-weight:800;
            color:var(--text-muted);
            margin:2px 0 0;
        }

        .emp-position-modal-summary {
            padding:12px;
            border-radius:14px;
            background:#f9fafb;
            border:1px solid var(--border);
            margin-bottom:12px;
        }

        .emp-position-modal-list {
            display:flex;
            flex-direction:column;
            gap:8px;
            max-height:55vh;
            overflow-y:auto;
            padding-right:4px;
        }

        .emp-position-modal-item {
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:7px;
            padding:10px;
            border:1px solid var(--border);
            border-radius:12px;
            background:#fff;
        }

        .emp-actions {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:7px;
            flex-wrap:wrap;
        }

        .oc-empty {
            padding:34px 18px !important;
            text-align:center;
            color:var(--text-muted);
            font-weight:800;
        }

        .oc-pagination {
            margin-top:18px;
            display:flex;
            justify-content:flex-end;
        }

        .modal-content {
            border:0;
            border-radius:18px;
            overflow:hidden;
            box-shadow:var(--shadow);
        }

        .modal-header {
            border-bottom:1px solid var(--border);
            background:#fafafa;
        }

        .modal-title {
            font-weight:900;
            color:#111827;
        }

        .modal-footer {
            border-top:1px solid var(--border);
            background:#fafafa;
        }

        .emp-modal-avatar {
            width:190px;
            height:190px;
            border-radius:999px;
            object-fit:cover;
            box-shadow:0 0 0 6px #fff, 0 0 0 7px var(--border);
        }

        .generated-code-badge {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:8px 14px;
            border-radius:999px;
            background:var(--primary-light);
            color:#365314;
            font-size:18px;
            font-weight:900;
            letter-spacing:2px;
        }

        @media print {
            body * {
                visibility:hidden;
            }

            #showGeneratedCodesModal,
            #showGeneratedCodesModal * {
                visibility:visible;
            }

            #showGeneratedCodesModal {
                position:absolute;
                left:0;
                top:0;
                width:100%;
            }

            .modal-footer,
            .close {
                display:none !important;
            }
        }

        @media(max-width:768px) {
            .oc-wrap {
                padding:18px;
                margin:0;
            }

            .oc-header {
                margin-top:70px;
            }

            .oc-title {
                font-size:21px;
            }

            .oc-card-body {
                padding:14px;
            }

            .oc-titlebar > div:last-child {
                width:100%;
            }

            .oc-titlebar .oc-btn {
                width:100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="oc-wrap">
        @php
            $statusTab = request('status_tab', $statusTab ?? 'active');
            $canDeleteEmployee = DB::table('user_rolls')
                ->where('user_id', auth()->user()->name)
                ->where('item_id', 'Employee')
                ->where('is_delete', 'on')
                ->exists();

            $totalEmployeeCount = ($activeCount ?? 0) + ($inactiveCount ?? 0);
        @endphp

        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">Mitarbeiter Details</div>
                    <div class="oc-sub">
                        Mitarbeiter verwalten, Status prüfen, Gebiete einsehen und Passcodes administrieren.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                        <span>›</span>
                        <span class="current">Mitarbeiter</span>
                    </div>
                </div>

                <div>
                    <a class="oc-btn" href="{{ url('emp_create') }}">
                        <i class="feather icon-user-plus"></i>
                        Mitarbeiter erstellen
                    </a>
                </div>
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-users"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Gesamt</div>
                    <div class="oc-stat-value">{{ $totalEmployeeCount }}</div>
                    <div class="oc-stat-sub">Alle Mitarbeiter</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon active">
                    <i class="feather icon-user-check"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Aktiv</div>
                    <div class="oc-stat-value">{{ $activeCount ?? 0 }}</div>
                    <div class="oc-stat-sub">Aktive Mitarbeiter</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon inactive">
                    <i class="feather icon-user-x"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Deaktiviert</div>
                    <div class="oc-stat-value">{{ $inactiveCount ?? 0 }}</div>
                    <div class="oc-stat-sub">Ehemalige / inaktive Mitarbeiter</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon codes">
                    <i class="feather icon-lock"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Passcodes</div>
                    <div class="oc-stat-value">4</div>
                    <div class="oc-stat-sub">4-stellige Mitarbeitercodes</div>
                </div>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Übersicht</h3>
                    <div class="oc-card-sub">
                        @if($statusTab === 'active')
                            Aktive Mitarbeiter ({{ $activeCount ?? 0 }})
                        @else
                            Deaktivierte / ehemalige Mitarbeiter ({{ $inactiveCount ?? 0 }})
                        @endif
                    </div>
                </div>

                <button type="button" class="oc-btn-danger" data-toggle="modal" data-target="#generateAllModal">
                    <i class="feather icon-refresh-cw"></i>
                    Passcodes generieren
                </button>
            </div>

            <div class="oc-card-body">
                <div class="oc-tabs">
                    <a
                        class="oc-tab {{ $statusTab === 'active' ? 'active' : '' }}"
                        href="{{ route('emp.info', array_merge(request()->except('page', 'status_tab'), ['status_tab' => 'active'])) }}"
                    >
                        <i class="feather icon-user-check"></i>
                        Aktiv
                        <span class="oc-tab-count">{{ $activeCount ?? 0 }}</span>
                    </a>

                    <a
                        class="oc-tab {{ $statusTab === 'inactive' ? 'active' : '' }}"
                        href="{{ route('emp.info', array_merge(request()->except('page', 'status_tab'), ['status_tab' => 'inactive'])) }}"
                    >
                        <i class="feather icon-user-x"></i>
                        Deaktiviert
                        <span class="oc-tab-count">{{ $inactiveCount ?? 0 }}</span>
                    </a>
                </div>

                <form action="{{ route('emp.info') }}" method="GET" class="oc-filter-form">
                    <input type="hidden" name="status_tab" value="{{ $statusTab }}">

                    <div class="oc-form-group">
                        <label class="oc-label">Suche</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="oc-input"
                            placeholder="Mitarbeiter suchen..."
                        >
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Sortierung</label>
                        <select name="sort" class="oc-select" onchange="this.form.submit()">
                            <option value="lastname" {{ request('sort', 'lastname') == 'lastname' ? 'selected' : '' }}>Nachname</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Vorname</option>
                            <option value="department" {{ request('sort') == 'department' ? 'selected' : '' }}>Abteilung</option>
                            <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Richtung</label>
                        <select name="direction" class="oc-select" onchange="this.form.submit()">
                            <option value="asc" {{ request('direction', 'asc') == 'asc' ? 'selected' : '' }}>Aufsteigend</option>
                            <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Absteigend</option>
                        </select>
                    </div>

                    <div class="oc-form-group">
                        <button class="oc-btn" type="submit">
                            <i class="feather icon-filter"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Mitarbeiterliste</h3>
                    <div class="oc-card-sub">Profil, Abteilung, Gebiet, Betrieb und Aktionen</div>
                </div>
            </div>

            <div class="oc-card-body">
                <div class="oc-table-wrap">
                    <table class="oc-table">
                        <thead>
                            <tr>
                                <th style="width:90px;"># / Farbe</th>
                                <th>Mitarbeiter</th>
                                <th>Abteilung</th>
                                <th>Gebiet</th>
                                <th>Betrieb</th>
                                <th>Status</th>
                                <th class="text-right">Aktionen</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($data as $item)
                                @php
                                    $main_address = DB::table('employee_addresses')
                                        ->where('emp_id', $item->id)
                                        ->where('main', 'active')
                                        ->select('street', 'postal', 'city')
                                        ->first();

                                    $employeePositions = DB::table('department_positions')
                                        ->join('departments', 'departments.id', '=', 'department_positions.department_id')
                                        ->join('positions', 'positions.id', '=', 'department_positions.position_id')
                                        ->where('department_positions.employee_id', $item->id)
                                        ->select(
                                            'departments.department_name',
                                            'positions.position',
                                            'department_positions.main'
                                        )
                                        ->orderByRaw("CASE WHEN department_positions.main = 'active' THEN 0 ELSE 1 END")
                                        ->orderBy('departments.department_name')
                                        ->orderBy('positions.position')
                                        ->get()
                                        ->unique(function ($pos) {
                                            return trim((string) $pos->department_name) . '|' . trim((string) $pos->position);
                                        })
                                        ->values();

                                    $mainPositions = $employeePositions
                                        ->filter(fn($pos) => (string) $pos->main === 'active')
                                        ->values();

                                    $sidePositions = $employeePositions
                                        ->reject(fn($pos) => (string) $pos->main === 'active')
                                        ->values();

                                    $visibleSidePositions = $sidePositions->take(2);
                                    $hiddenSidePositions = $sidePositions->slice(2)->values();

                                    $employeePostcodes = $postcodeLists->where('employee_id', $item->id);

                                    $avatar = $item->image
                                        ? asset('images/employee/' . $item->image)
                                        : asset('images/default-user.png');
                                @endphp

                                <tr>
                                    <td style="border-left:6px solid {{ $item->color ?? '#8fc73e' }};">
                                        <div class="emp-index-cell">
                                            <span class="emp-row-number">{{ $loop->iteration }}</span>

                                            <form action="{{ url('/employee_color/' . $item->id) }}" method="POST" class="emp-color-form">
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    type="color"
                                                    name="color"
                                                    value="{{ $item->color ?? '#8fc73e' }}"
                                                    class="emp-color-input"
                                                    title="Farbe ändern"
                                                    onchange="this.form.submit()"
                                                >
                                            </form>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="emp-card">
                                            <button
                                                type="button"
                                                class="emp-avatar-btn"
                                                data-toggle="modal"
                                                data-target="#image{{ $item->id }}"
                                                title="Bild anzeigen"
                                            >
                                                <img src="{{ $avatar }}" alt="avatar">
                                            </button>

                                            <div>
                                                <a href="{{ url('employee_profile/' . $item->id) }}" class="emp-name-link">
                                                    {{ $item->lastname }}, {{ $item->name }}
                                                </a>

                                                <p class="emp-contact-line">
                                                    <i class="feather icon-phone"></i>
                                                    <a href="tel:{{ $item->phone }}">{{ $item->phone ?: '—' }}</a>
                                                </p>

                                                <p class="emp-contact-line">
                                                    <i class="feather icon-mail"></i>
                                                    <a href="mailto:{{ $item->email }}">{{ $item->email ?: '—' }}</a>
                                                </p>

                                                @if ($main_address)
                                                    <p class="emp-contact-line">
                                                        <i class="feather icon-map-pin"></i>
                                                        {{ $main_address->street }} {{ $main_address->postal }} {{ $main_address->city }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="modal fade text-left" id="image{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Mitarbeiterbild</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body text-center">
                                                        <img src="{{ $avatar }}" alt="avatar" class="emp-modal-avatar mb-2">

                                                        <h3 class="mb-1">
                                                            <a href="{{ url('next_employee/' . $item->id) }}">
                                                                {{ $item->name }} {{ $item->lastname }}
                                                            </a>
                                                        </h3>

                                                        <span class="oc-badge blue">
                                                            <i class="feather icon-briefcase"></i>
                                                            {{ $item->branch ?: 'Kein Betrieb' }}
                                                        </span>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="oc-btn-soft" data-dismiss="modal">Schließen</button>
                                                        <a href="{{ url('next_employee/' . $item->id) }}" class="oc-btn">
                                                            <i class="feather icon-edit"></i>
                                                            Bearbeiten
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($employeePositions->count())
                                            <div class="emp-position-box" data-position-box>
                                                @if($mainPositions->count())
                                                    @foreach($mainPositions as $pos)
                                                        <div class="emp-position-main">
                                                            <div class="emp-position-row mb-0">
                                                                <span class="oc-badge success">
                                                                    <i class="feather icon-star"></i>
                                                                    Hauptposition
                                                                </span>
                                                                <span class="oc-badge blue">{{ $pos->department_name }}</span>
                                                                <span class="oc-badge gray">{{ $pos->position }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="emp-position-row">
                                                        <span class="oc-badge warning">
                                                            <i class="feather icon-alert-circle"></i>
                                                            Keine Hauptposition
                                                        </span>
                                                    </div>
                                                @endif

                                                @if($sidePositions->count())
                                                    <div class="emp-position-side-wrap">
                                                        <div class="emp-position-muted">Weitere Positionen</div>

                                                        @foreach($visibleSidePositions as $pos)
                                                            <div class="emp-position-row">
                                                                <span class="oc-badge blue">{{ $pos->department_name }}</span>
                                                                <span class="oc-badge gray">{{ $pos->position }}</span>
                                                                <span class="oc-badge primary">Neben</span>
                                                            </div>
                                                        @endforeach

                                                        @if($hiddenSidePositions->count())
                                                            <button
                                                                type="button"
                                                                class="emp-position-more-btn"
                                                                data-toggle="modal"
                                                                data-target="#employeePositionsModal{{ $item->id }}"
                                                                title="Weitere Positionen anzeigen"
                                                            >
                                                                <i class="feather icon-plus"></i>
                                                                +{{ $hiddenSidePositions->count() }} mehr
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            @if($hiddenSidePositions->count())
                                                <div class="modal fade text-left" id="employeePositionsModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="employeePositionsModalLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div>
                                                                    <h5 class="modal-title" id="employeePositionsModalLabel{{ $item->id }}">
                                                                        Weitere Positionen
                                                                    </h5>
                                                                    <div class="oc-card-sub">
                                                                        {{ $item->lastname }}, {{ $item->name }} · {{ $hiddenSidePositions->count() }} weitere Nebenpositionen
                                                                    </div>
                                                                </div>

                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                @if($mainPositions->count())
                                                                    <div class="emp-position-modal-summary">
                                                                        <div class="emp-position-muted">Hauptposition</div>

                                                                        @foreach($mainPositions as $pos)
                                                                            <div class="emp-position-row mb-0">
                                                                                <span class="oc-badge success">
                                                                                    <i class="feather icon-star"></i>
                                                                                    Haupt
                                                                                </span>
                                                                                <span class="oc-badge blue">{{ $pos->department_name }}</span>
                                                                                <span class="oc-badge gray">{{ $pos->position }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif

                                                                <div class="emp-position-muted mb-2">Nicht direkt in der Tabelle angezeigt</div>

                                                                <div class="emp-position-modal-list">
                                                                    @foreach($hiddenSidePositions as $pos)
                                                                        <div class="emp-position-modal-item">
                                                                            <span class="oc-badge blue">{{ $pos->department_name }}</span>
                                                                            <span class="oc-badge gray">{{ $pos->position }}</span>
                                                                            <span class="oc-badge primary">Neben</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="oc-btn-soft" data-dismiss="modal">Schließen</button>
                                                                <a href="{{ url('next_employee/' . $item->id) }}" class="oc-btn">
                                                                    <i class="feather icon-edit"></i>
                                                                    Positionen bearbeiten
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <span class="oc-badge gray">Keine Abteilung</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($employeePostcodes->count())
                                            @foreach($employeePostcodes as $pList)
                                                <div class="emp-postcode-row">
                                                    <span class="oc-badge gray">
                                                        {{ $pList->postcode_from }} - {{ $pList->postcode_to }}
                                                    </span>

                                                    @if($pList->country)
                                                        <span class="oc-badge blue">{{ $pList->country }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="oc-badge gray">Kein Gebiet</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="oc-badge blue">
                                            <i class="feather icon-home"></i>
                                            {{ $item->branch ?: '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($item->status === 'Active')
                                            <span class="oc-badge success">
                                                <i class="feather icon-check-circle"></i>
                                                Aktiv
                                            </span>
                                        @else
                                            <span class="oc-badge gray">
                                                <i class="feather icon-slash"></i>
                                                Deaktiviert
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="emp-actions">
                                            <button
                                                type="button"
                                                class="oc-btn-ic warning"
                                                data-toggle="modal"
                                                data-target="#reset-passcode{{ $item->id }}"
                                                title="Passcode Reset"
                                            >
                                                <i class="feather icon-lock"></i>
                                            </button>

                                            <a href="{{ url('next_employee/' . $item->id) }}" class="oc-btn-ic primary" title="Bearbeiten">
                                                <i class="feather icon-edit"></i>
                                            </a>

                                            @if($item->status != 'Active')
                                                <a href="{{ url('/employee_active/' . $item->id) }}" class="oc-btn-ic success" title="Aktivieren">
                                                    <i class="feather icon-check-square"></i>
                                                </a>
                                            @else
                                                <a href="{{ url('/employee_deactive/' . $item->id) }}" class="oc-btn-ic danger" title="Deaktivieren">
                                                    <i class="feather icon-power"></i>
                                                </a>
                                            @endif

                                            @if($canDeleteEmployee)
                                                <button
                                                    type="button"
                                                    class="oc-btn-ic danger"
                                                    data-toggle="modal"
                                                    data-target="#delete-pro{{ $item->id }}"
                                                    title="Löschen"
                                                >
                                                    <i class="feather icon-trash"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="modal fade text-left" id="reset-passcode{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ url('/employee_passcode/' . $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Passcode neu setzen: {{ $item->name }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body text-left">
                                                            <div class="alert alert-warning mb-2">
                                                                <i class="feather icon-alert-triangle mr-1"></i>
                                                                Der alte Passcode wird überschrieben.
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="oc-label">Neuer 4-stelliger Passcode</label>
                                                                <input
                                                                    type="text"
                                                                    class="oc-input"
                                                                    name="passcode"
                                                                    placeholder="z.B. 1234"
                                                                    required
                                                                    pattern="\d{4}"
                                                                    maxlength="4"
                                                                    autocomplete="off"
                                                                    inputmode="numeric"
                                                                >
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="oc-btn-soft" data-dismiss="modal">Abbrechen</button>
                                                            <button type="submit" class="oc-btn-danger">
                                                                <i class="feather icon-save"></i>
                                                                Speichern
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        @if($canDeleteEmployee)
                                            <div class="modal fade text-left" id="delete-pro{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Löschen bestätigen</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body text-left">
                                                            <div class="alert alert-danger mb-2">
                                                                <i class="feather icon-alert-triangle mr-1"></i>
                                                                Diese Aktion kann je nach Systemlogik Daten entfernen oder deaktivieren.
                                                            </div>

                                                            <p class="mb-0">
                                                                Möchten Sie <strong>{{ $item->name }} {{ $item->lastname }}</strong> wirklich löschen?
                                                            </p>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="oc-btn-soft" data-dismiss="modal">Nein</button>
                                                            <a href="{{ url('/emp_destroy/' . $item->id) }}" class="oc-btn-danger">
                                                                <i class="feather icon-trash"></i>
                                                                Ja, löschen
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="oc-empty">
                                        <i class="feather icon-users"></i>
                                        Keine Mitarbeiter gefunden.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="oc-pagination">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="generateAllModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('emp.generate_all') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Warnung: Alle Passcodes überschreiben</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-danger mb-2">
                            <i class="feather icon-alert-triangle mr-1"></i>
                            Sind Sie sicher? Dies generiert für alle aktiven Mitarbeiter neue zufällige Passcodes.
                        </div>

                        <p class="mb-1">
                            Die alten Passcodes werden unwiderruflich überschrieben.
                        </p>

                        <p class="text-muted mb-0">
                            <small>Nach dem Generieren wird Ihnen eine Liste aller neuen Codes angezeigt.</small>
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="oc-btn-soft" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="oc-btn-danger">
                            <i class="feather icon-refresh-cw"></i>
                            Ja, alles neu generieren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('generated_codes'))
        <div
            class="modal fade text-left"
            id="showGeneratedCodesModal"
            tabindex="-1"
            role="dialog"
            aria-hidden="true"
            data-backdrop="static"
            data-keyboard="false"
        >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background:var(--success);">
                        <h5 class="modal-title text-white">Passcodes erfolgreich generiert</h5>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-warning mb-2">
                            <i class="feather icon-alert-triangle mr-1"></i>
                            <strong>Achtung:</strong> Bitte drucken oder speichern Sie diese Liste jetzt. Die Codes sind nach dem Schließen dieses Fensters nicht mehr einsehbar.
                        </div>

                        <div class="oc-table-wrap">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Mitarbeiter</th>
                                        <th>Betrieb</th>
                                        <th class="text-center">Neuer Passcode</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach(session('generated_codes') as $code)
                                        <tr>
                                            <td>{{ $code['name'] }}</td>
                                            <td>{{ $code['branch'] }}</td>
                                            <td class="text-center">
                                                <span class="generated-code-badge">{{ $code['code'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="oc-btn-soft" onclick="window.print()">
                            <i class="feather icon-printer"></i>
                            Drucken
                        </button>

                        <button type="button" class="oc-btn" data-dismiss="modal">
                            Ich habe die Codes gespeichert
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            @if(Session::has('update_msg'))
                toastr.success(@json(session('update_msg') ?? session('updated_msg')));
            @endif

            @if(Session::has('updated_msg'))
                toastr.success(@json(session('updated_msg')));
            @endif

            @if(Session::has('save_msg'))
                toastr.success(@json(session('save_msg')));
            @endif

            @if(Session::has('delete_msg'))
                toastr.error(@json(session('delete_msg')));
            @endif

            @if(session('generated_codes'))
                $('#showGeneratedCodesModal').modal('show');
            @endif

            if (window.feather) {
                window.feather.replace();
            }
        });
    </script>
@endsection


@push('scripts')
    <script>
        window.GlobalBreadcrumbs =[
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Mitarbeiterliste',
                url: "{{ url('emp?status_tab=active')}}", 
            }

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush