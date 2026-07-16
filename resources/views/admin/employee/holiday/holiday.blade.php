@extends('admin.layouts.app')
@section('title', 'Feiertage')

@section('style')
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
            --danger-light:#fef2f2;
            --gray:#6b7280;
            --gray-light:#f3f4f6;
            --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
            --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --radius:16px;
            --transition:all .2s ease-in-out;
        }

        .oc-wrap {
            font-family:Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color:var(--text-main);
            margin:20px auto;
            padding-right:79px;
        }

        .oc-header {
            margin-bottom:18px;
        }

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
            font-weight:900;
            letter-spacing:-.025em;
            color:#111827;
            text-transform:uppercase;
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
            font-weight:800;
        }

        .oc-breadcrumb a:hover {
            color:var(--text-main);
        }

        .oc-breadcrumb span.current {
            color:#111827;
            font-weight:900;
        }

        .oc-btn,
        .oc-btn-soft,
        .oc-btn-success,
        .oc-btn-danger,
        .oc-btn-blue {
            border:none;
            padding:10px 16px;
            border-radius:10px;
            font-weight:900;
            cursor:pointer;
            transition:var(--transition);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            text-decoration:none;
            line-height:1.2;
            white-space:nowrap;
        }

        .oc-btn {
            background:var(--primary);
            color:#fff;
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
        }

        .oc-btn-soft:hover {
            background:#f9fafb;
            color:var(--text-main);
            text-decoration:none;
        }

        .oc-btn-success {
            background:var(--success);
            color:#fff;
        }

        .oc-btn-success:hover {
            background:#059669;
            color:#fff;
            text-decoration:none;
        }

        .oc-btn-danger {
            background:var(--danger);
            color:#fff;
        }

        .oc-btn-danger:hover {
            background:#dc2626;
            color:#fff;
            text-decoration:none;
        }

        .oc-btn-blue {
            background:var(--blue);
            color:#fff;
        }

        .oc-btn-blue:hover {
            background:#559fc7;
            color:#fff;
            text-decoration:none;
        }

        .oc-btn-icon {
            width:38px;
            height:38px;
            border-radius:10px;
            border:1px solid var(--border);
            background:#fff;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition:var(--transition);
            color:var(--text-muted);
            text-decoration:none;
        }

        .oc-btn-icon:hover {
            background:#f9fafb;
            color:#111827;
            text-decoration:none;
        }

        .oc-btn-icon.edit {
            background:var(--blue-light);
            color:#075985;
            border-color:#c0d8ea;
        }

        .oc-btn-icon.delete {
            background:var(--danger-light);
            color:var(--danger);
            border-color:#fecaca;
        }

        .oc-btn-icon.success {
            background:var(--success-light);
            color:#047857;
            border-color:#bbf7d0;
        }

        .oc-btn-icon.warning {
            background:var(--warning-light);
            color:#b45309;
            border-color:#fde68a;
        }

        .oc-analytics {
            display:grid;
            grid-template-columns:repeat(4, minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
        }

        @media(max-width:1200px) {
            .oc-analytics {
                grid-template-columns:repeat(2, minmax(0,1fr));
            }
        }

        @media(max-width:700px) {
            .oc-analytics {
                grid-template-columns:1fr;
            }
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

        .oc-stat-icon.total {
            background:var(--blue-light);
            color:var(--blue);
        }

        .oc-stat-icon.published {
            background:var(--success-light);
            color:var(--success);
        }

        .oc-stat-icon.unpublished {
            background:var(--warning-light);
            color:#b45309;
        }

        .oc-stat-icon.year {
            background:var(--primary-light);
            color:#365314;
        }

        .oc-stat-label {
            font-size:11px;
            font-weight:900;
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

        .oc-card {
            background:#fff;
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:var(--shadow-sm);
            overflow:hidden;
            margin-bottom:18px;
        }

        .oc-card-header {
            padding:16px 18px;
            border-bottom:1px solid var(--border);
            background:#fafafa;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
        }

        .oc-card-title {
            margin:0;
            font-size:16px;
            font-weight:900;
            color:#111827;
            text-transform:uppercase;
        }

        .oc-card-sub {
            font-size:12px;
            color:var(--text-muted);
            margin-top:4px;
        }

        .oc-card-body {
            padding:18px;
        }

        .oc-toolbar {
            display:grid;
            grid-template-columns:minmax(260px,1fr) auto;
            gap:12px;
            align-items:end;
            margin-bottom:18px;
        }

        @media(max-width:768px) {
            .oc-toolbar {
                grid-template-columns:1fr;
            }
        }

        .oc-label {
            display:block;
            font-size:12px;
            font-weight:900;
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:.05em;
            margin-bottom:7px;
        }

        .oc-input,
        .oc-select,
        .oc-textarea {
            width:100%;
            padding:11px 12px;
            border-radius:10px;
            border:1px solid var(--border);
            background:#fff;
            color:#111827;
            font-size:14px;
            outline:none;
            transition:var(--transition);
            min-height:42px;
        }

        .oc-textarea {
            min-height:104px;
            resize:vertical;
        }

        .oc-input:focus,
        .oc-select:focus,
        .oc-textarea:focus {
            border-color:var(--primary);
            box-shadow:0 0 0 3px var(--primary-light);
        }

        .oc-search-row {
            display:flex;
            gap:10px;
            align-items:center;
        }

        .oc-search-row .oc-input {
            flex:1;
        }

        .oc-table-wrap {
            overflow-x:auto;
        }

        .oc-table {
            width:100%;
            border-collapse:separate;
            border-spacing:0 10px;
            min-width:900px;
        }

        .oc-table thead th {
            font-size:11px;
            font-weight:900;
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:.06em;
            padding:8px 10px;
            border:0;
            white-space:nowrap;
            text-align:left;
        }

        .oc-table thead th.text-right {
            text-align:right;
        }

        .oc-table tbody tr {
            background:#fff;
            border:1px solid var(--border);
            box-shadow:var(--shadow-sm);
        }

        .oc-table tbody td,
        .oc-table tbody th {
            padding:12px 10px;
            border-top:1px solid var(--border);
            border-bottom:1px solid var(--border);
            vertical-align:middle;
            background:#fff;
        }

        .oc-table tbody td:first-child,
        .oc-table tbody th:first-child {
            border-left:1px solid var(--border);
            border-radius:14px 0 0 14px;
        }

        .oc-table tbody td:last-child {
            border-right:1px solid var(--border);
            border-radius:0 14px 14px 0;
        }

        .oc-id {
            font-weight:900;
            color:#111827;
        }

        .holiday-year {
            font-size:15px;
            font-weight:900;
            color:#111827;
        }

        .holiday-text {
            font-size:13px;
            color:#374151;
            font-weight:700;
            max-width:480px;
            white-space:normal;
            line-height:1.45;
        }

        .oc-status {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            padding:5px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:900;
            white-space:nowrap;
        }

        .oc-status.published {
            background:var(--success-light);
            color:#047857;
        }

        .oc-status.unpublished {
            background:var(--warning-light);
            color:#b45309;
        }

        .oc-status.draft {
            background:var(--gray-light);
            color:#374151;
        }

        .oc-actions {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
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

        .oc-error-box {
            border:1px solid #fecaca;
            background:var(--danger-light);
            color:#991b1b;
            border-radius:14px;
            padding:14px 16px;
            margin-bottom:18px;
            font-weight:800;
        }

        .oc-error-box ul {
            margin:0;
            padding-left:18px;
        }

        .oc-modal-backdrop {
            position:fixed;
            inset:0;
            z-index:9998;
            background:rgba(15,23,42,.56);
            display:none;
            align-items:center;
            justify-content:center;
            padding:18px;
        }

        .oc-modal-backdrop.is-open {
            display:flex;
        }

        .oc-modal {
            width:100%;
            max-width:620px;
            background:#fff;
            border-radius:20px;
            border:1px solid var(--border);
            box-shadow:var(--shadow);
            overflow:hidden;
            transform:translateY(10px) scale(.98);
            opacity:0;
            transition:var(--transition);
        }

        .oc-modal.sm {
            max-width:520px;
        }

        .oc-modal-backdrop.is-open .oc-modal {
            transform:translateY(0) scale(1);
            opacity:1;
        }

        .oc-modal-header {
            padding:16px 18px;
            border-bottom:1px solid var(--border);
            background:#fafafa;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
        }

        .oc-modal-title {
            margin:0;
            font-size:16px;
            font-weight:900;
            color:#111827;
        }

        .oc-modal-sub {
            font-size:12px;
            color:var(--text-muted);
            margin-top:4px;
            font-weight:700;
        }

        .oc-modal-close {
            width:38px;
            height:38px;
            border-radius:10px;
            border:1px solid var(--border);
            background:#fff;
            cursor:pointer;
            color:var(--text-muted);
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }

        .oc-modal-close:hover {
            background:#f9fafb;
            color:#111827;
        }

        .oc-modal-body {
            padding:18px;
        }

        .oc-modal-footer {
            padding:14px 18px;
            border-top:1px solid var(--border);
            background:#fafafa;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
        }

        .oc-form-grid {
            display:grid;
            grid-template-columns:1fr;
            gap:14px;
        }

        .delete-warning {
            border:1px solid #fecaca;
            background:var(--danger-light);
            border-radius:14px;
            padding:14px;
            color:#991b1b;
            font-weight:800;
            line-height:1.55;
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

            .oc-search-row {
                flex-direction:column;
                align-items:stretch;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $totalCount = method_exists($data, 'total') ? $data->total() : $data->count();
        $publishedCount = collect($data->items())->where('status', 'Published')->count();
        $unpublishedCount = collect($data->items())->where('status', '!=', 'Published')->count();
        $searchValue = request('search', $search ?? '');
    @endphp

    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">Feiertage</div>
                    <div class="oc-sub">
                        Feiertagsjahre verwalten, aktivieren, bearbeiten und löschen.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                        <span>›</span>
                        <span class="current">Feiertage</span>
                    </div>
                </div>

                <button type="button" class="oc-btn" data-open-modal="createHolidayModal">
                    <i class="feather icon-plus"></i>
                    Neuer Eintrag
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="oc-error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-calendar"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Einträge</div>
                    <div class="oc-stat-value">{{ $totalCount }}</div>
                    <div class="oc-stat-sub">Gesamt im Filter</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon published">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Aktiv</div>
                    <div class="oc-stat-value">{{ $publishedCount }}</div>
                    <div class="oc-stat-sub">Auf dieser Seite</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon unpublished">
                    <i class="feather icon-pause-circle"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Inaktiv</div>
                    <div class="oc-stat-value">{{ $unpublishedCount }}</div>
                    <div class="oc-stat-sub">Auf dieser Seite</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon year">
                    <i class="feather icon-search"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Suche</div>
                    <div class="oc-stat-value">{{ $searchValue ? 'Aktiv' : 'Alle' }}</div>
                    <div class="oc-stat-sub">{{ $searchValue ?: 'Kein Suchfilter' }}</div>
                </div>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Filter</h3>
                    <div class="oc-card-sub">Nach Jahr, Feiertag oder Status suchen</div>
                </div>
            </div>

            <div class="oc-card-body">
                <form action="{{ route('holiday.info') }}" method="GET" class="oc-toolbar">
                    <div>
                        <label class="oc-label">Suche</label>
                        <div class="oc-search-row">
                            <input
                                type="text"
                                name="search"
                                class="oc-input"
                                value="{{ $searchValue }}"
                                placeholder="Jahr, Feiertage oder Status suchen..."
                            >

                            <button type="submit" class="oc-btn">
                                <i class="feather icon-search"></i>
                                Suchen
                            </button>

                            @if($searchValue)
                                <a href="{{ route('holiday.info') }}" class="oc-btn-soft">
                                    <i class="feather icon-x"></i>
                                    Zurücksetzen
                                </a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <button type="button" class="oc-btn-soft" data-open-modal="createHolidayModal">
                            <i class="feather icon-plus-circle"></i>
                            Hinzufügen
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Feiertagsliste</h3>
                    <div class="oc-card-sub">Alle gespeicherten Feiertagsjahre</div>
                </div>
            </div>

            <div class="oc-card-body">
                <div class="oc-table-wrap">
                    <table class="oc-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jahr</th>
                                <th>Feiertage</th>
                                <th>Status</th>
                                <th class="text-right">Aktionen</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($data as $item)
                                @php
                                    $statusClass = match ($item->status) {
                                        'Published' => 'published',
                                        'Unpublished' => 'unpublished',
                                        default => 'draft',
                                    };

                                    $statusLabel = match ($item->status) {
                                        'Published' => 'Aktiv',
                                        'Unpublished' => 'Inaktiv',
                                        'Not published' => 'Nicht veröffentlicht',
                                        default => $item->status ?: 'Unbekannt',
                                    };
                                @endphp

                                <tr>
                                    <th class="oc-id">#{{ $item->id }}</th>

                                    <td>
                                        <div class="holiday-year">{{ $item->year }}</div>
                                    </td>

                                    <td>
                                        <div class="holiday-text">{{ $item->holiday }}</div>
                                    </td>

                                    <td>
                                        <span class="oc-status {{ $statusClass }}">
                                            @if($item->status === 'Published')
                                                <i class="feather icon-check-circle"></i>
                                            @elseif($item->status === 'Unpublished')
                                                <i class="feather icon-pause-circle"></i>
                                            @else
                                                <i class="feather icon-clock"></i>
                                            @endif
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="oc-actions">
                                            <button
                                                type="button"
                                                class="oc-btn-icon edit"
                                                title="Bearbeiten"
                                                data-open-modal="editHolidayModal{{ $item->id }}"
                                            >
                                                <i class="feather icon-edit"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="oc-btn-icon delete"
                                                title="Löschen"
                                                data-open-modal="deleteHolidayModal{{ $item->id }}"
                                            >
                                                <i class="feather icon-trash-2"></i>
                                            </button>

                                            @if($item->status !== 'Published')
                                                <a
                                                    href="{{ route('holiday.active', $item->id) }}"
                                                    class="oc-btn-icon success"
                                                    title="Aktivieren"
                                                >
                                                    <i class="feather icon-check"></i>
                                                </a>
                                            @else
                                                <a
                                                    href="{{ route('holiday.deactive', $item->id) }}"
                                                    class="oc-btn-icon warning"
                                                    title="Deaktivieren"
                                                >
                                                    <i class="feather icon-power"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="oc-empty">
                                        Keine Feiertage gefunden.
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

    {{-- CREATE MODAL --}}
    <div class="oc-modal-backdrop" id="createHolidayModal" aria-hidden="true">
        <div class="oc-modal" role="dialog" aria-modal="true">
            <form method="POST" action="{{ route('holiday.create') }}">
                @csrf

                <div class="oc-modal-header">
                    <div>
                        <h3 class="oc-modal-title">Neuen Feiertagseintrag erstellen</h3>
                        <div class="oc-modal-sub">Jahr und Feiertage eintragen</div>
                    </div>

                    <button type="button" class="oc-modal-close" data-close-modal="createHolidayModal">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="oc-modal-body">
                    <div class="oc-form-grid">
                        <div>
                            <label class="oc-label">Jahr</label>
                            <input
                                type="text"
                                class="oc-input"
                                name="year"
                                value="{{ old('year') }}"
                                placeholder="z. B. 2026"
                                required
                            >
                        </div>

                        <div>
                            <label class="oc-label">Feiertage</label>
                            <textarea
                                class="oc-textarea"
                                name="holiday"
                                placeholder="Feiertage eintragen..."
                                required
                            >{{ old('holiday') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="oc-modal-footer">
                    <button type="button" class="oc-btn-soft" data-close-modal="createHolidayModal">
                        Abbrechen
                    </button>

                    <button type="submit" class="oc-btn-success">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT / DELETE MODALS --}}
    @foreach($data as $item)
        <div class="oc-modal-backdrop" id="editHolidayModal{{ $item->id }}" aria-hidden="true">
            <div class="oc-modal" role="dialog" aria-modal="true">
                <form method="POST" action="{{ route('holiday.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $item->id }}">

                    <div class="oc-modal-header">
                        <div>
                            <h3 class="oc-modal-title">Feiertagseintrag bearbeiten</h3>
                            <div class="oc-modal-sub">Datensatz #{{ $item->id }}</div>
                        </div>

                        <button type="button" class="oc-modal-close" data-close-modal="editHolidayModal{{ $item->id }}">
                            <i class="feather icon-x"></i>
                        </button>
                    </div>

                    <div class="oc-modal-body">
                        <div class="oc-form-grid">
                            <div>
                                <label class="oc-label">Jahr</label>
                                <input
                                    type="text"
                                    class="oc-input"
                                    name="year"
                                    value="{{ old('year', $item->year) }}"
                                    required
                                >
                            </div>

                            <div>
                                <label class="oc-label">Feiertage</label>
                                <textarea
                                    class="oc-textarea"
                                    name="holiday"
                                    required
                                >{{ old('holiday', $item->holiday) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="oc-modal-footer">
                        <button type="button" class="oc-btn-soft" data-close-modal="editHolidayModal{{ $item->id }}">
                            Abbrechen
                        </button>

                        <button type="submit" class="oc-btn-success">
                            <i class="feather icon-save"></i>
                            Aktualisieren
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="oc-modal-backdrop" id="deleteHolidayModal{{ $item->id }}" aria-hidden="true">
            <div class="oc-modal sm" role="dialog" aria-modal="true">
                <div class="oc-modal-header">
                    <div>
                        <h3 class="oc-modal-title">Eintrag löschen</h3>
                        <div class="oc-modal-sub">Diese Aktion kann nicht rückgängig gemacht werden</div>
                    </div>

                    <button type="button" class="oc-modal-close" data-close-modal="deleteHolidayModal{{ $item->id }}">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="oc-modal-body">
                    <div class="delete-warning">
                        Möchten Sie den Feiertagseintrag für das Jahr
                        <strong>{{ $item->year }}</strong>
                        wirklich löschen?
                        <br>
                        Datensatznummer: <strong>#{{ $item->id }}</strong>
                    </div>
                </div>

                <div class="oc-modal-footer">
                    <button type="button" class="oc-btn-soft" data-close-modal="deleteHolidayModal{{ $item->id }}">
                        Abbrechen
                    </button>

                    <form action="{{ route('holiday.destroy', $item->id) }}" method="POST" style="display:inline-flex;margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="oc-btn-danger">
                            <i class="feather icon-trash-2"></i>
                            Ja, löschen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('script')
    <script>
    (function () {
        'use strict';

        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            setTimeout(function () {
                const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, button');
                if (firstInput) firstInput.focus();
            }, 80);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeAllModals() {
            document.querySelectorAll('.oc-modal-backdrop.is-open').forEach(function (modal) {
                closeModal(modal.id);
            });
        }

        document.addEventListener('click', function (event) {
            const openBtn = event.target.closest('[data-open-modal]');
            if (openBtn) {
                openModal(openBtn.dataset.openModal);
                return;
            }

            const closeBtn = event.target.closest('[data-close-modal]');
            if (closeBtn) {
                closeModal(closeBtn.dataset.closeModal);
                return;
            }

            if (event.target.classList.contains('oc-modal-backdrop')) {
                closeModal(event.target.id);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllModals();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            @if(Session::has('update_msg'))
                if (typeof toastr !== 'undefined') toastr.success(@json(session('update_msg')));
            @endif

            @if(Session::has('updated_msg'))
                if (typeof toastr !== 'undefined') toastr.success(@json(session('updated_msg')));
            @endif

            @if(Session::has('save_msg'))
                if (typeof toastr !== 'undefined') toastr.success(@json(session('save_msg')));
            @endif

            @if(Session::has('delete_msg'))
                if (typeof toastr !== 'undefined') toastr.error(@json(session('delete_msg')));
            @endif

            if (window.feather) {
                window.feather.replace();
            }
        });
    })();
    </script>
@endsection

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/employee_dashboard') }}"
            },
            {
                label: 'Feiertage',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush