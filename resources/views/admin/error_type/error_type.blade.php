@extends('admin.layouts.app')
@section('title') FEHLERHANDBUCH @endsection
@php
$errorItems = collect($data instanceof \Illuminate\Pagination\AbstractPaginator ? $data->items() : $data);

$totalCount = $data instanceof \Illuminate\Pagination\AbstractPaginator ? $data->total() : $errorItems->count();
$activeCount = (int) $errorItems->where('status', 'Published')->count();
$inactiveCount = (int) $errorItems->where('status', 'Unpublished')->count();
$withFileCount = (int) $errorItems->filter(fn($item) => !empty($item->file))->count();
@endphp

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        :root {
            --app-bg:#f3f4f6;
            --card-bg:#ffffff;
            --text-main:#1f2937;
            --text-muted:#6b7280;
            --border:#e5e7eb;
            --primary:#93c21c;
            --primary-hover:#7baa18;
            --primary-light:#f4fae7;
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

        .fh-wrap {
            font-family: Inter, system-ui, -apple-system, sans-serif;
            color:var(--text-main);
        }

        .fh-header {
            margin-bottom:18px;
        }

        .fh-titlebar {
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap:12px;
            margin-bottom:16px;
            flex-wrap:wrap;
        }

        .fh-title {
            font-size:26px;
            font-weight:800;
            letter-spacing:-.025em;
            color:#111827;
            text-transform:uppercase;
        }

        .fh-sub {
            font-size:14px;
            color:var(--text-muted);
            margin-top:4px;
        }

        .fh-breadcrumb {
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:8px;
            margin-top:10px;
            font-size:13px;
            color:var(--text-muted);
        }

        .fh-breadcrumb a {
            color:var(--text-muted);
            text-decoration:none;
            font-weight:700;
        }

        .fh-breadcrumb a:hover {
            color:var(--text-main);
        }

        .fh-breadcrumb span.current {
            color:#111827;
            font-weight:800;
        }

        .fh-inline-actions {
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }

        .fh-btn {
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

        .fh-btn:hover {
            background:var(--primary-hover);
            color:#fff;
            text-decoration:none;
        }

        .fh-btn-soft {
            background:#fff;
            color:var(--text-main);
            border:1px solid var(--border);
            padding:10px 14px;
            border-radius:10px;
            font-weight:800;
            cursor:pointer;
            transition:var(--transition);
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:8px;
        }

        .fh-btn-soft:hover {
            background:#f9fafb;
            color:var(--text-main);
            text-decoration:none;
        }

        .fh-btn-ic {
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
            padding:0;
        }

        .fh-btn-ic:hover {
            background:#f9fafb;
            color:var(--text-main);
            border-color:#d1d5db;
            text-decoration:none;
        }

        .fh-btn-ic.primary {
            color:var(--primary);
            border-color:var(--primary-light);
            background:var(--primary-light);
        }

        .fh-btn-ic.warning {
            color:#d97706;
            border-color:#fde7b0;
            background:#fffbeb;
        }

        .fh-btn-ic.success {
            color:var(--success);
            border-color:#c7f2df;
            background:var(--success-light);
        }

        .fh-btn-ic.danger {
            color:var(--danger);
            border-color:rgba(239,68,68,.18);
            background:var(--danger-light);
        }

        .fh-btn-ic.info {
            color:var(--blue);
            border-color:rgba(116,178,212,.25);
            background:var(--blue-light);
        }

        .fh-analytics {
            display:grid;
            grid-template-columns:repeat(4, minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
        }

        @media(max-width:1200px) {
            .fh-analytics {
                grid-template-columns:repeat(2, minmax(0,1fr));
            }
        }

        @media(max-width:700px) {
            .fh-analytics {
                grid-template-columns:1fr;
            }
        }

        .fh-stat {
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

        .fh-stat-icon {
            width:48px;
            height:48px;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex:0 0 auto;
        }

        .fh-stat-icon.total { background:var(--blue-light); color:var(--blue); }
        .fh-stat-icon.active { background:var(--success-light); color:var(--success); }
        .fh-stat-icon.inactive { background:var(--danger-light); color:var(--danger); }
        .fh-stat-icon.file { background:var(--primary-light); color:var(--primary); }

        .fh-stat-label {
            font-size:11px;
            font-weight:800;
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        .fh-stat-value {
            font-size:24px;
            font-weight:900;
            color:#111827;
            line-height:1.1;
            margin-top:4px;
        }

        .fh-stat-sub {
            font-size:12px;
            color:var(--text-muted);
            margin-top:4px;
        }

        .fh-toolbar {
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

        .fh-toolbar-left,
        .fh-toolbar-right {
            display:flex;
            align-items:flex-end;
            gap:12px;
            flex-wrap:wrap;
        }

        .fh-toolbar-left {
            flex:1;
        }

        .fh-filter-block {
            display:flex;
            flex-direction:column;
            gap:6px;
            min-width:170px;
        }

        .fh-filter-block.search {
            flex:1;
            min-width:280px;
        }

        .fh-filter-label {
            font-size:11px;
            font-weight:800;
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        .fh-input {
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

        .fh-input:focus {
            background:#fff;
            border-color:var(--primary);
            box-shadow:0 0 0 3px var(--primary-light);
        }

        .fh-card {
            background:#fff;
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:var(--shadow-sm);
            overflow:hidden;
        }

        .fh-list-head {
            display:grid;
            grid-template-columns:minmax(260px,1.4fr) minmax(210px,1.1fr) minmax(260px,1.3fr) 130px 110px 150px;
            gap:14px;
            align-items:center;
            padding:16px 16px 10px 16px;
            color:var(--text-muted);
            font-size:11px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        @media(max-width:1350px) {
            .fh-list-head {
                display:none;
            }
        }

        .fh-list {
            display:flex;
            flex-direction:column;
            gap:12px;
            padding:0 0 16px 0;
        }

        .fh-item {
            background:var(--card-bg);
            border:1px solid var(--border);
            border-radius:var(--radius);
            transition:var(--transition);
            overflow:hidden;
            margin:0 16px;
        }

        .fh-item:hover {
            border-color:var(--primary);
            box-shadow:var(--shadow);
        }

        .fh-item-row {
            padding:16px;
            display:grid;
            gap:16px;
            align-items:center;
            grid-template-columns:minmax(260px,1.4fr) minmax(210px,1.1fr) minmax(260px,1.3fr) 130px 110px 150px;
        }

        @media(max-width:1350px) {
            .fh-item-row {
                grid-template-columns:1fr;
            }
        }

        .fh-cell {
            min-width:0;
        }

        .fh-cell-title {
            font-size:11px;
            font-weight:800;
            color:var(--text-muted);
            text-transform:uppercase;
            margin-bottom:4px;
            display:none;
        }

        @media(max-width:1350px) {
            .fh-cell-title {
                display:block;
            }
        }

        .fh-main {
            display:flex;
            flex-direction:column;
            min-width:0;
        }

        .fh-code-line {
            display:flex;
            align-items:flex-start;
            gap:12px;
            min-width:0;
        }

        .fh-code-icon {
            width:46px;
            height:46px;
            border-radius:14px;
            background:var(--blue-light);
            color:var(--blue);
            border:1px solid rgba(116,178,212,.25);
            display:flex;
            align-items:center;
            justify-content:center;
            flex:0 0 auto;
        }

        .fh-ttl {
            font-weight:900;
            font-size:15px;
            margin-bottom:4px;
            color:#111827;
            line-height:1.35;
        }

        .fh-subt {
            font-size:13px;
            color:var(--text-muted);
            line-height:1.45;
        }

        .fh-subt.ellipsis {
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .fh-tag {
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

        .fh-tag.green {
            background:var(--primary-light);
            color:#365314;
        }

        .fh-tag.gray {
            background:var(--gray-light);
            color:var(--gray);
        }

        .fh-status {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:6px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:900;
            white-space:nowrap;
        }

        .fh-status.active {
            background:var(--success-light);
            color:#047857;
        }

        .fh-status.inactive {
            background:var(--danger-light);
            color:#b91c1c;
        }

        .fh-preview-actions {
            display:flex;
            align-items:center;
            gap:8px;
            flex-wrap:wrap;
        }

        .fh-actions {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
            flex-wrap:wrap;
        }

        .fh-empty {
            text-align:center;
            padding:60px;
            color:var(--text-muted);
            background:#fff;
            border:1px dashed var(--border);
            border-radius:16px;
            margin:16px;
        }

        .fh-pagination {
            margin-top:18px;
            background:#fff;
            border:1px solid var(--border);
            border-radius:14px;
            padding:14px 16px;
            box-shadow:var(--shadow-sm);
        }

        .fh-pagination .pagination {
            margin:0;
            display:flex;
            flex-wrap:wrap;
            gap:6px;
        }

        .fh-pagination .page-item .page-link {
            border-radius:10px !important;
            border:1px solid var(--border);
            color:var(--text-main);
            padding:8px 12px;
            line-height:1.1;
            box-shadow:none !important;
        }

        .fh-pagination .page-item.active .page-link {
            background:var(--primary);
            border-color:var(--primary);
            color:#fff;
        }

        .fh-pagination .page-item.disabled .page-link {
            color:#9ca3af;
            background:#f9fafb;
        }

        .modal-content {
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:var(--shadow);
            overflow:hidden;
        }

        .modal-header {
            background:#fafafa;
            border-bottom:1px solid var(--border);
            padding:16px 18px;
        }

        .modal-title {
            font-weight:900;
            color:#111827;
        }

        .modal-body {
            padding:20px 18px;
        }

        .modal-footer {
            background:#fafafa;
            border-top:1px solid var(--border);
        }

        .form-group label {
            font-size:11px;
            font-weight:900;
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:.06em;
        }

        .form-control,
        .form-control-file {
            border-radius:10px;
            border:1px solid var(--border);
            box-shadow:none !important;
        }

        .form-control:focus {
            border-color:var(--primary);
            box-shadow:0 0 0 3px var(--primary-light) !important;
        }

        .quill-editor {
            height:150px;
            background:#fff;
            border:1px solid var(--border);
            border-radius:10px;
        }

        .ql-toolbar.ql-snow {
            border-radius:10px 10px 0 0;
            border-color:var(--border);
        }

        .ql-container.ql-snow {
            border-radius:0 0 10px 10px;
            border-color:var(--border);
        }

        @media(max-width:640px) {
            .fh-toolbar {
                align-items:stretch;
            }

            .fh-toolbar-left,
            .fh-toolbar-right {
                width:100%;
            }

            .fh-filter-block,
            .fh-filter-block.search {
                width:100%;
                min-width:100%;
            }

            .fh-input {
                min-width:100%;
            }

            .fh-actions {
                justify-content:flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="fh-wrap">
        <div class="fh-header">
            <div class="fh-titlebar">
                <div>
                    <div class="fh-title">Fehlerhandbuch</div>
                    <div class="fh-sub">
                        Fehlercodes, Ursachen, Lösungen, Hersteller- und Produktinformationen zentral verwalten.
                    </div>

                    <div class="fh-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Home</a>
                        <span>›</span>
                        <span class="current">Fehlerhandbuch</span>
                    </div>
                </div>

                <div class="fh-inline-actions">
                    <button type="button" class="fh-btn" data-toggle="modal" data-target="#default">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        Neuer Fehlereintrag
                    </button>
                </div>
            </div>
        </div>

        <div class="fh-analytics">
            <div class="fh-stat">
                <div class="fh-stat-icon total">
                    <i class="feather icon-list"></i>
                </div>
                <div>
                    <div class="fh-stat-label">Gesamt</div>
                    <div class="fh-stat-value">{{ $totalCount }}</div>
                    <div class="fh-stat-sub">Fehlereinträge</div>
                </div>
            </div>

            <div class="fh-stat">
                <div class="fh-stat-icon active">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div>
                    <div class="fh-stat-label">Aktiv</div>
                    <div class="fh-stat-value">{{ $activeCount }}</div>
                    <div class="fh-stat-sub">Veröffentlichte Einträge</div>
                </div>
            </div>

            <div class="fh-stat">
                <div class="fh-stat-icon inactive">
                    <i class="feather icon-x-circle"></i>
                </div>
                <div>
                    <div class="fh-stat-label">Inaktiv</div>
                    <div class="fh-stat-value">{{ $inactiveCount }}</div>
                    <div class="fh-stat-sub">Nicht veröffentlichte Einträge</div>
                </div>
            </div>

            <div class="fh-stat">
                <div class="fh-stat-icon file">
                    <i class="feather icon-paperclip"></i>
                </div>
                <div>
                    <div class="fh-stat-label">Dateien</div>
                    <div class="fh-stat-value">{{ $withFileCount }}</div>
                    <div class="fh-stat-sub">Mit Anhang</div>
                </div>
            </div>
        </div>

        <form action="{{ route('error.info') }}" method="GET" class="fh-toolbar">
            <div class="fh-toolbar-left">
                <div class="fh-filter-block search">
                    <label class="fh-filter-label">Suche</label>
                    <input
                        type="text"
                        name="search"
                        class="fh-input"
                        value="{{ request('search') }}"
                        placeholder="Nach Fehlercode, Beschreibung, Hersteller, Produkt oder Seriennummer suchen..."
                    >
                </div>
            </div>

            <div class="fh-toolbar-right">
                <button class="fh-btn-soft" type="submit">
                    <i class="feather icon-search"></i>
                    Suchen
                </button>
                <a href="{{ route('error.info') }}" class="fh-btn-soft">
                    Zurücksetzen
                </a>
            </div>
        </form>

        <div class="fh-card">
            <div class="fh-list-head">
                <div>Fehler</div>
                <div>Produktdetails</div>
                <div>Dokumentation</div>
                <div>Datei</div>
                <div>Status</div>
                <div style="text-align:right;">Aktionen</div>
            </div>

            <div class="fh-list">
                @forelse($data as $item)
                    @php
    $fileUrl = $item->file ? asset('public/uploads/errors/' . $item->file) : null;
    $isImage = $item->file ? preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $item->file) : false;
    $isPdf = $item->file ? preg_match('/\.pdf$/i', $item->file) : false;
    $isActive = $item->status === 'Published';
                    @endphp

                    <div class="fh-item" data-error-row="{{ $item->id }}">
                        <div class="fh-item-row">
                            <div class="fh-cell">
                                <div class="fh-cell-title">Fehler</div>
                                <div class="fh-code-line">
                                    <div class="fh-code-icon">
                                        <i class="feather icon-alert-triangle"></i>
                                    </div>
                                    <div class="fh-main">
                                        <div class="fh-ttl">
                                            {{ $item->error_code ?: 'Ohne Fehlercode' }}
                                        </div>
                                        <div class="fh-subt">
                                            {{ $item->problem_types }}
                                        </div>
                                        <div style="margin-top:8px;">
                                            <span class="fh-tag gray">ID: #{{ $item->id }}</span>
                                            @if($item->latest_update)
                                                <span class="fh-tag green">
                                                    Letzte Änderung: {{ \Carbon\Carbon::parse($item->latest_update)->format('d.m.Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fh-cell">
                                <div class="fh-cell-title">Produktdetails</div>
                                <div class="fh-main">
                                    <div class="fh-ttl" style="font-size:14px;">
                                        {{ $item->article_group ?: '–' }}
                                    </div>
                                    <div class="fh-subt">
                                        <div><i class="fa fa-building-o"></i> Hersteller: {{ $item->brand_name ?: '–' }}</div>
                                        <div><i class="feather icon-codepen"></i> Artikel: {{ $item->article_name ?: '–' }}</div>
                                        <div><i class="feather icon-hash"></i> Seriennummer: {{ $item->serial_no ?: '–' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="fh-cell">
                                <div class="fh-cell-title">Dokumentation</div>
                                <div class="fh-preview-actions">
                                    <button class="fh-btn-soft" type="button" data-toggle="modal" data-target="#reasonModal{{ $item->id }}">
                                        <i class="feather icon-info"></i>
                                        Ursache
                                    </button>

                                    <button class="fh-btn-soft" type="button" data-toggle="modal" data-target="#solutionModal{{ $item->id }}">
                                        <i class="feather icon-check-square"></i>
                                        Lösung
                                    </button>
                                </div>
                            </div>

                            <div class="fh-cell">
                                <div class="fh-cell-title">Datei</div>
                                @if($item->file)
                                    @if($isImage)
                                        <a href="{{ $fileUrl }}" target="_blank" class="fh-tag">
                                            <i class="feather icon-image"></i> Bild
                                        </a>
                                    @elseif($isPdf)
                                        <a href="{{ $fileUrl }}" target="_blank" class="fh-tag gray">
                                            <i class="feather icon-file-text"></i> PDF
                                        </a>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank" class="fh-tag gray">
                                            <i class="feather icon-file"></i> Datei
                                        </a>
                                    @endif
                                @else
                                    <span class="fh-subt">–</span>
                                @endif
                            </div>

                            <div class="fh-cell">
                                <div class="fh-cell-title">Status</div>
                                <span class="fh-status {{ $isActive ? 'active' : 'inactive' }}">
                                    {{ $isActive ? 'Aktiv' : 'Inaktiv' }}
                                </span>
                            </div>

                            <div class="fh-cell">
                                <div class="fh-cell-title">Aktionen</div>
                                <div class="fh-actions">
                                    <button
                                        type="button"
                                        class="fh-btn-ic warning"
                                        data-toggle="modal"
                                        data-target="#editModal{{ $item->id }}"
                                        title="Bearbeiten"
                                    >
                                        <i class="feather icon-edit"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="fh-btn-ic danger delete-btn"
                                        data-id="{{ $item->id }}"
                                        title="Löschen"
                                    >
                                        <i class="feather icon-trash"></i>
                                    </button>

                                    @if($item->status === 'Published')
                                        <button
                                            type="button"
                                            onclick="toggleStatus({{ $item->id }}, 'Unpublished')"
                                            class="fh-btn-ic info"
                                            title="Deaktivieren"
                                        >
                                            <i class="feather icon-download"></i>
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            onclick="toggleStatus({{ $item->id }}, 'Published')"
                                            class="fh-btn-ic primary"
                                            title="Aktivieren"
                                        >
                                            <i class="feather icon-upload"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reason Modal --}}
                    <div class="modal fade" id="reasonModal{{ $item->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Fehlerursache · ID: {{ $item->id }}</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    {!! $item->reason ?: '<p class="text-muted">Keine Ursache hinterlegt.</p>' !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Solution Modal --}}
                    <div class="modal fade" id="solutionModal{{ $item->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Lösung · ID: {{ $item->id }}</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    {!! $item->solution ?: '<p class="text-muted">Keine Lösung hinterlegt.</p>' !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Modal kept for compatibility, AJAX delete is used --}}
                    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Löschen bestätigen</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    Möchten Sie den Fehler <strong>#{{ $item->id }}</strong> wirklich löschen?
                                </div>
                                <div class="modal-footer">
                                    <a href="{{ route('error.destroy', $item->id) }}" class="btn btn-danger">Ja, löschen</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Modal --}}
                    <div class="modal fade text-left" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="editLabel{{ $item->id }}">Fehler bearbeiten · ID: {{ $item->id }}</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <form class="edit-error-form" data-id="{{ $item->id }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $item->id }}">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Fehlercode</label>
                                                    <input type="text" class="form-control" name="error_code" value="{{ $item->error_code }}">
                                                </div>

                                                <div class="form-group">
                                                    <label>Problemfall</label>
                                                    <input type="text" class="form-control" name="problem_types" value="{{ $item->problem_types }}" required>
                                                    <small class="text-danger error" data-error="problem_types"></small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Produkt</label>
                                                    <select name="product_id" class="form-control select2 edit-product-select">
                                                        <option disabled>Produkt auswählen</option>
                                                        @foreach ($product as $pr)
                                                            <option value="{{ $pr->id }}" @if($pr->id == $item->product_id) selected @endif>
                                                                {{ $pr->article_group }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Artikelname</label>
                                                    <input type="text" name="article_name" class="form-control" value="{{ $item->article_name }}">
                                                </div>

                                                <div class="form-group">
                                                    <label>Datei neu hochladen</label>
                                                    <input type="file" name="file" class="form-control-file">
                                                    @if($item->file)
                                                        <small>
                                                            Aktuell:
                                                            <a href="{{ asset('public/uploads/errors/' . $item->file) }}" target="_blank">
                                                                {{ $item->file }}
                                                            </a>
                                                        </small>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="">Status auswählen</option>
                                                        <option value="Published" {{ $item->status == 'Published' ? 'selected' : '' }}>Aktiv</option>
                                                        <option value="Unpublished" {{ $item->status == 'Unpublished' ? 'selected' : '' }}>Inaktiv</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Grund</label>
                                                    <div id="reason_editor_{{ $item->id }}" class="quill-editor">{!! $item->reason !!}</div>
                                                    <input type="hidden" name="reason" id="reason_input_{{ $item->id }}">
                                                </div>

                                                <div class="form-group">
                                                    <label>Lösung</label>
                                                    <div id="solution_editor_{{ $item->id }}" class="quill-editor">{!! $item->solution !!}</div>
                                                    <input type="hidden" name="solution" id="solution_input_{{ $item->id }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="fh-btn-soft" data-dismiss="modal">Abbrechen</button>
                                            <button type="submit" class="fh-btn">Speichern</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="fh-empty">Keine Daten gefunden.</div>
                @endforelse
            </div>
        </div>

        <div class="fh-pagination">
            {{ $data->links() }}
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="createLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="createLabel">Neuer Fehlereintrag</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="errorForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fehlercode</label>
                                    <input type="text" class="form-control" name="error_code">
                                </div>

                                <div class="form-group">
                                    <label>Fehlerbeschreibung</label>
                                    <input type="text" class="form-control" name="problem_types" required>
                                    @if ($errors->has('problem_types'))
                                        <p class="text-danger">{{ $errors->first('problem_types') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>Hersteller</label>
                                    <select name="brand_id" id="brand_id" class="form-control select2">
                                        <option disabled selected>Hersteller auswählen</option>
                                        @foreach ($brands as $br)
                                            <option value="{{ $br->id }}">{{ $br->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Produkt</label>
                                    <select name="product_id" id="product_id" class="form-control select2">
                                        <option disabled selected>Produkt auswählen</option>
                                        @foreach ($product as $pr)
                                            <option value="{{ $pr->id }}">{{ $pr->article_group }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Produktbezeichnung</label>
                                    <input type="text" name="article_name" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Seriennummer</label>
                                    <input type="text" name="serial_no" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Datei hochladen</label>
                                    <input type="file" name="file" class="form-control-file">
                                </div>

                                <div class="form-group">
                                    <label>Links</label>
                                    <input type="text" name="links" id="links" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Status auswählen</option>
                                        <option value="Published" selected>Aktiv</option>
                                        <option value="Unpublished">Inaktiv</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fehlerursache</label>
                                    <div id="reason_editor" class="quill-editor"></div>
                                    <input type="hidden" name="reason" id="reason_input">
                                </div>

                                <div class="form-group">
                                    <label>Letzte Änderung</label>
                                    <input type="date" class="form-control" name="latest_update" id="latest_update">
                                </div>

                                <div class="form-group">
                                    <label>Lösung</label>
                                    <div id="solution_editor" class="quill-editor"></div>
                                    <input type="hidden" name="solution" id="solution_input">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="fh-btn-soft" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="fh-btn" id="submitErrorBtn">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
    $('#product_id').select2({
        placeholder: 'Produkt auswählen',
        width: '100%',
        dropdownParent: $('#default') // important if used inside a modal
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
  
<!-- Quill & AJAX -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    $(document).ready(function () {
        // 🖋️ Quill Editors Initialization (Create Form)
        const reasonEditor = new Quill('#reason_editor', { theme: 'snow' });
        const solutionEditor = new Quill('#solution_editor', { theme: 'snow' });

        // 🧾 File Size Check (2MB max)
        $('input[type="file"]').on('change', function () {
            const file = this.files[0];
            if (file && file.size > 2 * 1024 * 1024) {
                $(this).val('');
                Swal.fire({
                    icon: 'warning',
                    title: 'Datei zu groß!',
                    text: 'Die Datei darf nicht größer als 2 MB sein.'
                });
            }
        });

        // 💾 Create Form Submission
        $('#errorForm').on('submit', function (e) {
            e.preventDefault();
            $('.error').remove(); // Remove old errors

            // 🖋️ Sync Quill content
            $('#reason_input').val(reasonEditor.root.innerHTML);
            $('#solution_input').val(solutionEditor.root.innerHTML);

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('errors.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#submitErrorBtn').prop('disabled', true).text('Speichern...');
                },
                success: function (res) {
                    $('#submitErrorBtn').prop('disabled', false).text('Speichern');
                    $('#errorForm')[0].reset();
                    reasonEditor.setText('');
                    solutionEditor.setText('');
                    $('#default').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Erfolg!',
                        text: 'Fehlereintrag wurde erfolgreich gespeichert.',
                        confirmButtonText: 'OK'
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    $('#submitErrorBtn').prop('disabled', false).text('Speichern');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const importantFields = ['error_code', 'problem_types', 'product_id'];
                        let alertMessages = [];

                        importantFields.forEach(field => {
                            if (errors[field]) {
                                alertMessages.push(errors[field][0]);
                            }
                        });

                        if (alertMessages.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validierungsfehler',
                                html: alertMessages.join('<br>')
                            });
                        } else {
                            let messages = '';
                            Object.keys(errors).forEach(key => {
                                messages += `${errors[key][0]}<br>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Fehlerhafte Eingabe',
                                html: messages
                            });
                        }
                    } else {
                        Swal.fire('Fehler', 'Ein unerwarteter Fehler ist aufgetreten.', 'error');
                    }
                }
            });
        });

        // 🛠️ Quill Initialization for Edit Modals
        @foreach($data as $item)
            const reasonQuill{{ $item->id }} = new Quill('#reason_editor_{{ $item->id }}', { theme: 'snow' });
            const solutionQuill{{ $item->id }} = new Quill('#solution_editor_{{ $item->id }}', { theme: 'snow' });
        @endforeach

        // 📝 Edit Form Submission
        $('.edit-error-form').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const id = form.data('id');

            // 🖋️ Sync Quill content
            $('#reason_input_' + id).val($('#reason_editor_' + id + ' .ql-editor').html());
            $('#solution_input_' + id).val($('#solution_editor_' + id + ' .ql-editor').html());

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('error.update') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    form.find('button[type="submit"]').prop('disabled', true).text('Speichern...');
                },
                success: function (res) {
                    form.find('button[type="submit"]').prop('disabled', false).text('Speichern');
                    Swal.fire({
                        icon: 'success',
                        title: 'Aktualisiert!',
                        text: 'Der Fehler wurde erfolgreich aktualisiert.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    form.find('button[type="submit"]').prop('disabled', false).text('Speichern');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const importantFields = ['error_code', 'problem_types', 'product_id'];
                        let alertMessages = [];

                        importantFields.forEach(field => {
                            if (errors[field]) {
                                alertMessages.push(errors[field][0]);
                            }
                        });

                        if (alertMessages.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validierungsfehler',
                                html: alertMessages.join('<br>')
                            });
                        } else {
                            let messages = '';
                            Object.keys(errors).forEach(key => {
                                messages += `${errors[key][0]}<br>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Fehlerhafte Eingabe',
                                html: messages
                            });
                        }
                    } else {
                        Swal.fire('Fehler', 'Etwas ist schief gelaufen.', 'error');
                    }
                }
            });
        });

        // 🚦 Status Toggle
        window.toggleStatus = function (id, status) {
            $.ajax({
                url: "{{ route('error.status') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status aktualisiert!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        Swal.fire('Fehler', 'Ungültiger Status oder fehlende ID.', 'error');
                    } else {
                        Swal.fire('Fehler', 'Unbekannter Fehler beim Statuswechsel.', 'error');
                    }
                }
            });
        }

        // 🗑️ Delete Handler
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Bist du sicher?',
                text: "Dieser Eintrag wird gelöscht!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/error/delete/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            Swal.fire('Gelöscht!', res.message, 'success').then(() => {
                                $(`button[data-id="${id}"]`).closest('tr').remove();
                            });
                        },
                        error: function () {
                            Swal.fire('Fehler!', 'Löschen fehlgeschlagen.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>


@endsection
@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Fehlerhandbuch',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush