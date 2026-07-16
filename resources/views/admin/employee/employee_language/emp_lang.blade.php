@extends('admin.layouts.app')
@section('title', 'SPRACHEN')

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
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
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

        .oc-btn {
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
            justify-content:center;
            gap:8px;
            text-decoration:none;
            line-height:1.2;
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
            transition:var(--transition);
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
        }

        .oc-btn-soft:hover {
            background:#f9fafb;
            color:var(--text-main);
            text-decoration:none;
        }

        .oc-btn-danger {
            background:var(--danger);
            color:#fff;
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
        }

        .oc-btn-danger:hover {
            background:#dc2626;
            color:#fff;
            text-decoration:none;
        }

        .oc-btn-ic {
            width:38px;
            height:38px;
            border-radius:10px;
            border:1px solid var(--border);
            background:#fff;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:var(--text-muted);
            cursor:pointer;
            transition:var(--transition);
            text-decoration:none;
            flex:0 0 auto;
        }

        .oc-btn-ic:hover {
            background:#f9fafb;
            color:var(--text-main);
            border-color:#d1d5db;
            text-decoration:none;
        }

        .oc-btn-ic.primary {
            color:var(--blue);
            border-color:var(--blue-light);
            background:var(--blue-light);
        }

        .oc-btn-ic.danger {
            color:var(--danger);
            border-color:rgba(239,68,68,.18);
            background:var(--danger-light);
        }

        .oc-analytics {
            display:grid;
            grid-template-columns:repeat(3, minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
        }

        @media(max-width:1000px) {
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

        .oc-stat-icon.add {
            background:var(--success-light);
            color:var(--success);
        }

        .oc-stat-icon.search {
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

        .oc-filter-row {
            display:grid;
            grid-template-columns:minmax(240px,1fr) auto;
            gap:12px;
            align-items:end;
        }

        @media(max-width:700px) {
            .oc-filter-row {
                grid-template-columns:1fr;
            }
        }

        .oc-form-grid {
            display:grid;
            grid-template-columns:1fr auto;
            gap:12px;
            align-items:end;
        }

        @media(max-width:700px) {
            .oc-form-grid {
                grid-template-columns:1fr;
            }
        }

        .oc-form-group {
            min-width:0;
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

        .oc-input {
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

        .oc-input:focus {
            border-color:var(--primary);
            box-shadow:0 0 0 3px var(--primary-light);
        }

        .oc-alert {
            border-radius:14px;
            padding:14px 16px;
            margin-bottom:16px;
            border:1px solid rgba(239,68,68,.25);
            background:var(--danger-light);
            color:#991b1b;
            font-weight:800;
        }

        .oc-alert ul {
            margin:0;
            padding-left:18px;
        }

        .language-add-table {
            width:100%;
            border-collapse:separate;
            border-spacing:0 10px;
        }

        .language-add-table th {
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

        .language-add-table td {
            padding:10px;
            border-top:1px solid var(--border);
            border-bottom:1px solid var(--border);
            background:#fff;
        }

        .language-add-table td:first-child {
            border-left:1px solid var(--border);
            border-radius:14px 0 0 14px;
        }

        .language-add-table td:last-child {
            width:80px;
            text-align:center;
            border-right:1px solid var(--border);
            border-radius:0 14px 14px 0;
        }

        .oc-table-wrap {
            overflow-x:auto;
        }

        .oc-table {
            width:100%;
            border-collapse:separate;
            border-spacing:0 10px;
            min-width:760px;
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

        .oc-table tbody tr {
            background:#fff;
            border:1px solid var(--border);
            box-shadow:var(--shadow-sm);
        }

        .oc-table tbody td {
            padding:12px 10px;
            border-top:1px solid var(--border);
            border-bottom:1px solid var(--border);
            vertical-align:middle;
            background:#fff;
        }

        .oc-table tbody td:first-child {
            border-left:1px solid var(--border);
            border-radius:14px 0 0 14px;
        }

        .oc-table tbody td:last-child {
            border-right:1px solid var(--border);
            border-radius:0 14px 14px 0;
        }

        .oc-language-name {
            display:flex;
            align-items:center;
            gap:10px;
            font-size:14px;
            font-weight:900;
            color:#111827;
        }

        .oc-language-icon {
            width:38px;
            height:38px;
            border-radius:12px;
            background:var(--blue-light);
            color:var(--blue);
            display:flex;
            align-items:center;
            justify-content:center;
            flex:0 0 auto;
        }

        .oc-actions {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
        }

        .oc-empty {
            padding:34px 18px !important;
            text-align:center;
            color:var(--text-muted);
            font-weight:800;
        }

        .oc-pagination {
            display:flex;
            justify-content:flex-end;
            margin-top:18px;
        }

        .oc-modal {
            position:fixed;
            inset:0;
            z-index:9999;
            display:none;
            align-items:center;
            justify-content:center;
            padding:18px;
        }

        .oc-modal.is-open {
            display:flex;
        }

        .oc-modal-backdrop {
            position:absolute;
            inset:0;
            background:rgba(15,23,42,.55);
            backdrop-filter:blur(6px);
        }

        .oc-modal-panel {
            position:relative;
            z-index:1;
            width:min(560px, 100%);
            max-height:calc(100vh - 36px);
            overflow:auto;
            background:#fff;
            border:1px solid var(--border);
            border-radius:20px;
            box-shadow:var(--shadow);
            animation:ocModalIn .18s ease-out;
        }

        @keyframes ocModalIn {
            from {
                transform:translateY(10px) scale(.98);
                opacity:0;
            }

            to {
                transform:translateY(0) scale(1);
                opacity:1;
            }
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
            font-size:16px;
            font-weight:900;
            color:#111827;
            margin:0;
            text-transform:uppercase;
        }

        .oc-modal-close {
            width:38px;
            height:38px;
            border-radius:10px;
            border:1px solid var(--border);
            background:#fff;
            color:var(--text-muted);
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            transition:var(--transition);
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
            align-items:center;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
        }

        .oc-delete-box {
            background:var(--danger-light);
            border:1px solid rgba(239,68,68,.2);
            border-radius:16px;
            padding:14px;
            color:#7f1d1d;
            font-weight:800;
            line-height:1.5;
        }

        .oc-delete-box strong {
            color:#991b1b;
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
        }
    </style>
@endsection

@section('content')
    @php
        $employeeId = auth()->user()->name;

        $canAddLanguage = DB::table('user_rolls')
            ->where('user_id', $employeeId)
            ->where('item_id', 'Employee')
            ->where('is_add', 'on')
            ->exists();

        $canDeleteLanguage = DB::table('user_rolls')
            ->where('user_id', $employeeId)
            ->where('item_id', 'Employee')
            ->where('is_delete', 'on')
            ->exists();

        $languageCount = method_exists($data, 'total') ? $data->total() : $data->count();
    @endphp

    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">Sprachen</div>
                    <div class="oc-sub">
                        Sprachen für Mitarbeiterprofile verwalten, suchen, bearbeiten und löschen.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                        <span>›</span>
                        <a href="{{ url('emp?status_tab=active') }}">Mitarbeiter</a>
                        <span>›</span>
                        <span class="current">Sprachen</span>
                    </div>
                </div>

                @if($canAddLanguage)
                    <button type="button" class="oc-btn" data-scroll-target="#languageCreateCard">
                        <i class="feather icon-plus"></i>
                        Sprache hinzufügen
                    </button>
                @endif
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-globe"></i>
                </div>

                <div>
                    <div class="oc-stat-label">Sprachen</div>
                    <div class="oc-stat-value">{{ $languageCount }}</div>
                    <div class="oc-stat-sub">Im aktuellen Filter</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon search">
                    <i class="feather icon-search"></i>
                </div>

                <div>
                    <div class="oc-stat-label">Suche</div>
                    <div class="oc-stat-value">{{ request('search') ? 'Aktiv' : 'Aus' }}</div>
                    <div class="oc-stat-sub">{{ request('search') ?: 'Keine Suche aktiv' }}</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon add">
                    <i class="feather icon-user-check"></i>
                </div>

                <div>
                    <div class="oc-stat-label">Berechtigung</div>
                    <div class="oc-stat-value">{{ $canAddLanguage ? 'Ja' : 'Nein' }}</div>
                    <div class="oc-stat-sub">Sprache hinzufügen</div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="oc-alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Suche</h3>
                    <div class="oc-card-sub">Nach vorhandenen Sprachen suchen</div>
                </div>
            </div>

            <div class="oc-card-body">
                <form action="{{ route('language.info') }}" method="GET" class="oc-filter-row">
                    <div class="oc-form-group">
                        <label class="oc-label">Suchbegriff</label>
                        <input
                            type="text"
                            class="oc-input"
                            placeholder="Sprache suchen..."
                            name="search"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <button class="oc-btn" type="submit">
                        <i class="feather icon-search"></i>
                        Suchen
                    </button>
                </form>
            </div>
        </div>

        @if($canAddLanguage)
            <div class="oc-card" id="languageCreateCard">
                <div class="oc-card-header">
                    <div>
                        <h3 class="oc-card-title">Neue Sprache hinzufügen</h3>
                        <div class="oc-card-sub">Eine oder mehrere Sprachen gleichzeitig speichern</div>
                    </div>
                </div>

                <div class="oc-card-body">
                    <form action="{{ route('language.store') }}" method="POST">
                        @csrf

                        <div class="oc-table-wrap">
                            <table class="language-add-table" id="languageTable">
                                <thead>
                                    <tr>
                                        <th>Sprache</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <input
                                                type="text"
                                                class="oc-input"
                                                placeholder="z.B. Deutsch"
                                                name="language[0][language]"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <button type="button" class="oc-btn-ic primary" id="addLanguageRow" title="Weitere Sprache hinzufügen">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="oc-btn">
                            <i class="feather icon-save"></i>
                            Datensatz speichern
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Sprachenliste</h3>
                    <div class="oc-card-sub">Alle gespeicherten Sprachen</div>
                </div>
            </div>

            <div class="oc-card-body">
                <div class="oc-table-wrap">
                    <table class="oc-table">
                        <thead>
                            <tr>
                                <th style="width:90px;">#</th>
                                <th>Sprache</th>
                                <th style="width:180px; text-align:right;">Aktionen</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $lang)
                                <tr>
                                    <td>
                                        <strong>{{ $lang->id }}</strong>
                                    </td>

                                    <td>
                                        <div class="oc-language-name">
                                            <span class="oc-language-icon">
                                                <i class="feather icon-globe"></i>
                                            </span>
                                            <span>{{ $lang->language }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="oc-actions">
                                            <button
                                                type="button"
                                                class="oc-btn-ic primary"
                                                data-modal-open="editLanguageModal{{ $lang->id }}"
                                                title="Bearbeiten"
                                            >
                                                <i class="feather icon-edit"></i>
                                            </button>

                                            @if($canDeleteLanguage)
                                                <button
                                                    type="button"
                                                    class="oc-btn-ic danger"
                                                    data-modal-open="deleteLanguageModal{{ $lang->id }}"
                                                    title="Löschen"
                                                >
                                                    <i class="feather icon-trash-2"></i>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Custom Edit Modal --}}
                                        <div class="oc-modal" id="editLanguageModal{{ $lang->id }}" aria-hidden="true">
                                            <div class="oc-modal-backdrop" data-modal-close></div>

                                            <div class="oc-modal-panel" role="dialog" aria-modal="true">
                                                <div class="oc-modal-header">
                                                    <h4 class="oc-modal-title">Sprache bearbeiten</h4>

                                                    <button type="button" class="oc-modal-close" data-modal-close aria-label="Schließen">
                                                        <i class="feather icon-x"></i>
                                                    </button>
                                                </div>

                                                <form method="POST" action="{{ route('language.update') }}">
                                                    @csrf

                                                    <div class="oc-modal-body">
                                                        <input type="hidden" name="id" value="{{ $lang->id }}">

                                                        <div class="oc-form-group">
                                                            <label class="oc-label">Sprache</label>
                                                            <input
                                                                type="text"
                                                                class="oc-input"
                                                                name="language"
                                                                value="{{ $lang->language }}"
                                                                required
                                                            >
                                                        </div>
                                                    </div>

                                                    <div class="oc-modal-footer">
                                                        <button type="button" class="oc-btn-soft" data-modal-close>
                                                            Abbrechen
                                                        </button>

                                                        <button type="submit" class="oc-btn">
                                                            <i class="feather icon-save"></i>
                                                            Speichern
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Custom Delete Modal --}}
                                        @if($canDeleteLanguage)
                                            <div class="oc-modal" id="deleteLanguageModal{{ $lang->id }}" aria-hidden="true">
                                                <div class="oc-modal-backdrop" data-modal-close></div>

                                                <div class="oc-modal-panel" role="dialog" aria-modal="true">
                                                    <div class="oc-modal-header">
                                                        <h4 class="oc-modal-title">Sprache löschen</h4>

                                                        <button type="button" class="oc-modal-close" data-modal-close aria-label="Schließen">
                                                            <i class="feather icon-x"></i>
                                                        </button>
                                                    </div>

                                                    <form method="POST" action="{{ route('language.destroy', $lang->id) }}">
                                                        @csrf
                                                        @method('DELETE')

                                                        <div class="oc-modal-body">
                                                            <div class="oc-delete-box">
                                                                Möchten Sie die Sprache
                                                                <strong>„{{ $lang->language }}“</strong>
                                                                wirklich löschen?
                                                                <br>
                                                                Dieser Vorgang kann nicht automatisch rückgängig gemacht werden.
                                                            </div>
                                                        </div>

                                                        <div class="oc-modal-footer">
                                                            <button type="button" class="oc-btn-soft" data-modal-close>
                                                                Abbrechen
                                                            </button>

                                                            <button type="submit" class="oc-btn-danger">
                                                                <i class="feather icon-trash-2"></i>
                                                                Ja, löschen
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="oc-empty">
                                        <i class="feather icon-globe"></i>
                                        Keine Sprachen gefunden.
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
@endsection

@section('script')
    <script>
    (function () {
        'use strict';

        document.addEventListener('DOMContentLoaded', function () {
            @if(Session::has('update_msg'))
                if (typeof toastr !== 'undefined') toastr.success(@json(session('updated_msg')));
            @endif

            @if(Session::has('save_msg'))
                if (typeof toastr !== 'undefined') toastr.success(@json(session('save_msg')));
            @endif

            @if(Session::has('delete_msg'))
                if (typeof toastr !== 'undefined') toastr.error(@json(session('delete_msg')));
            @endif

            @if(Session::has('error_msg'))
                if (typeof toastr !== 'undefined') toastr.error(@json(session('error_msg')));
            @endif

            if (window.feather) {
                window.feather.replace();
            }
        });
    })();
    </script>

    <script>
    (function () {
        'use strict';

        let languageIndex = 0;

        const addButton = document.getElementById('addLanguageRow');
        const tableBody = document.querySelector('#languageTable tbody');

        function refreshIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        addButton?.addEventListener('click', function () {
            if (!tableBody) return;

            languageIndex++;

            const row = document.createElement('tr');

            row.innerHTML = `
                <td>
                    <input
                        type="text"
                        class="oc-input"
                        placeholder="z.B. Englisch"
                        name="language[${languageIndex}][language]"
                        required
                    >
                </td>

                <td>
                    <button type="button" class="oc-btn-ic danger" data-remove-language-row title="Zeile entfernen">
                        <i class="feather icon-trash-2"></i>
                    </button>
                </td>
            `;

            tableBody.appendChild(row);
            refreshIcons();
        });

        document.addEventListener('click', function (event) {
            const removeButton = event.target.closest('[data-remove-language-row]');
            if (!removeButton) return;

            const row = removeButton.closest('tr');
            if (row) row.remove();
        });
    })();
    </script>

    <script>
    (function () {
        'use strict';

        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            const firstInput = modal.querySelector('input:not([type="hidden"]), button');
            if (firstInput) {
                setTimeout(function () {
                    firstInput.focus();
                }, 80);
            }
        }

        function closeModal(modal) {
            if (!modal) return;

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');

            if (!document.querySelector('.oc-modal.is-open')) {
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('click', function (event) {
            const openButton = event.target.closest('[data-modal-open]');
            if (openButton) {
                openModal(openButton.getAttribute('data-modal-open'));
                return;
            }

            const closeButton = event.target.closest('[data-modal-close]');
            if (closeButton) {
                closeModal(closeButton.closest('.oc-modal'));
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;

            document.querySelectorAll('.oc-modal.is-open').forEach(function (modal) {
                closeModal(modal);
            });
        });

        document.addEventListener('click', function (event) {
            const scrollButton = event.target.closest('[data-scroll-target]');
            if (!scrollButton) return;

            const target = document.querySelector(scrollButton.getAttribute('data-scroll-target'));
            if (!target) return;

            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    })();
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
                label: 'Mitarbeiterliste',
                url: "{{ url('emp?status_tab=active') }}"
            },
            {
                label: 'Sprachen',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush