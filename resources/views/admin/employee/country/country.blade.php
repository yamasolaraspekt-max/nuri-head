@extends('admin.layouts.app')
@section('title', 'Länder und Nationalitäten')

@section('style')
    <style>
        :root {
            --app-bg:#f3f4f6;
            --card-bg:#ffffff;
            --text-main:#1f2937;
            --text-muted:#6b7280;
            --border:#e5e7eb;
            --primary:#8fc73e;
            --primary-hover:#7baa18;
            --primary-light:#f4fae7;
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

        .oc-breadcrumb a:hover { color:var(--text-main); }

        .oc-breadcrumb span.current {
            color:#111827;
            font-weight:900;
        }

        .oc-btn,
        .oc-btn-success,
        .oc-btn-danger,
        .oc-btn-blue,
        .oc-btn-soft {
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

        .oc-analytics {
            display:grid;
            grid-template-columns:repeat(3, minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
        }

        @media(max-width:1000px) {
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
        .oc-stat-icon.country { background:var(--primary-light); color:#365314; }
        .oc-stat-icon.nationality { background:var(--warning-light); color:#b45309; }

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

        .oc-card-body { padding:18px; }

        .oc-filter-form {
            display:grid;
            grid-template-columns:minmax(240px, 1fr) auto;
            gap:12px;
            align-items:end;
        }

        @media(max-width:700px) {
            .oc-filter-form { grid-template-columns:1fr; }
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
            border:1px solid #fecaca;
            background:#fef2f2;
            color:#991b1b;
            padding:14px 16px;
            margin-bottom:18px;
            font-weight:700;
        }

        .oc-alert ul {
            margin:0;
            padding-left:18px;
        }

        .country-add-table-wrap,
        .oc-table-wrap {
            overflow-x:auto;
        }

        .country-add-table,
        .oc-table {
            width:100%;
            border-collapse:separate;
            border-spacing:0 10px;
            min-width:760px;
        }

        .country-add-table thead th,
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

        .country-add-table tbody tr,
        .oc-table tbody tr {
            background:#fff;
            border:1px solid var(--border);
            box-shadow:var(--shadow-sm);
        }

        .country-add-table tbody td,
        .oc-table tbody td {
            padding:10px;
            border-top:1px solid var(--border);
            border-bottom:1px solid var(--border);
            vertical-align:middle;
            background:#fff;
        }

        .country-add-table tbody td:first-child,
        .oc-table tbody td:first-child {
            border-left:1px solid var(--border);
            border-radius:14px 0 0 14px;
        }

        .country-add-table tbody td:last-child,
        .oc-table tbody td:last-child {
            border-right:1px solid var(--border);
            border-radius:0 14px 14px 0;
        }

        .country-name {
            font-weight:900;
            color:#111827;
        }

        .country-nationality {
            display:inline-flex;
            align-items:center;
            padding:5px 9px;
            border-radius:999px;
            background:var(--primary-light);
            color:#365314;
            font-size:12px;
            font-weight:900;
        }

        .country-actions {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
        }

        .text-right { text-align:right !important; }

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

        .country-save-row {
            margin-top:12px;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
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
            max-width:560px;
            background:#fff;
            border-radius:20px;
            border:1px solid var(--border);
            box-shadow:var(--shadow);
            overflow:hidden;
            transform:translateY(10px) scale(.98);
            opacity:0;
            transition:var(--transition);
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

        .delete-warning {
            border:1px solid #fecaca;
            background:var(--danger-light);
            color:#991b1b;
            border-radius:14px;
            padding:14px;
            font-weight:800;
            line-height:1.5;
        }

        @media(max-width:768px) {
            .oc-wrap {
                padding:18px;
                margin:0;
            }

            .oc-header { margin-top:70px; }

            .oc-title { font-size:21px; }

            .oc-card-body { padding:14px; }
        }
    </style>
@endsection

@section('content')
    @php
        $canAddCountry = DB::table('user_rolls')
            ->where('user_rolls.user_id', auth()->user()->name)
            ->where('user_rolls.item_id', 'Employee')
            ->where('user_rolls.is_add', 'on')
            ->exists();

        $countryCount = method_exists($data, 'total') ? $data->total() : $data->count();
    @endphp

    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">Länder und Nationalitäten</div>
                    <div class="oc-sub">
                        Länder, Staatsangehörigkeiten und Suchfilter zentral verwalten.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <span>›</span>
                        <span class="current">Länder</span>
                    </div>
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

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-globe"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Einträge</div>
                    <div class="oc-stat-value">{{ $countryCount }}</div>
                    <div class="oc-stat-sub">Gefundene Länder</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon country">
                    <i class="feather icon-map-pin"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Suchbegriff</div>
                    <div class="oc-stat-value">{{ request('search') ? 'Aktiv' : 'Alle' }}</div>
                    <div class="oc-stat-sub">{{ request('search') ?: 'Keine Suche aktiv' }}</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon nationality">
                    <i class="feather icon-user-check"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Modul</div>
                    <div class="oc-stat-value">HR</div>
                    <div class="oc-stat-sub">Mitarbeiter-Stammdaten</div>
                </div>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Suche</h3>
                    <div class="oc-card-sub">Nach Land oder Staatsangehörigkeit suchen</div>
                </div>
            </div>

            <div class="oc-card-body">
                <form action="{{ route('country.info') }}" method="GET" class="oc-filter-form">
                    <div>
                        <label class="oc-label">Suchbegriff</label>
                        <input
                            type="text"
                            class="oc-input"
                            placeholder="Land oder Staatsangehörigkeit eingeben..."
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

        @if($canAddCountry)
            <div class="oc-card">
                <div class="oc-card-header">
                    <div>
                        <h3 class="oc-card-title">Neues Land hinzufügen</h3>
                        <div class="oc-card-sub">Mehrere Länder können gleichzeitig gespeichert werden</div>
                    </div>

                    <button type="button" class="oc-btn-soft" id="addCountryRow">
                        <i class="feather icon-plus"></i>
                        Weitere Zeile
                    </button>
                </div>

                <div class="oc-card-body">
                    <form novalidate action="{{ route('country.store') }}" method="POST">
                        @csrf

                        <div class="country-add-table-wrap">
                            <table class="country-add-table">
                                <thead>
                                    <tr>
                                        <th>Land</th>
                                        <th>Staatsangehörigkeit</th>
                                        <th class="text-right">Aktion</th>
                                    </tr>
                                </thead>

                                <tbody id="countryRows">
                                    <tr>
                                        <td>
                                            <input
                                                type="text"
                                                class="oc-input"
                                                placeholder="z. B. Deutschland"
                                                name="country[0][country]"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <input
                                                type="text"
                                                class="oc-input"
                                                placeholder="z. B. Deutsch"
                                                name="country[0][nationality]"
                                                required
                                            >
                                        </td>

                                        <td class="text-right">
                                            <button type="button" class="oc-btn-icon edit" id="addCountryRowInline" title="Weitere Zeile">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="country-save-row">
                            <button type="submit" class="oc-btn-success">
                                <i class="feather icon-save"></i>
                                Datensatz speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Länderübersicht</h3>
                    <div class="oc-card-sub">Bestehende Länder bearbeiten oder löschen</div>
                </div>
            </div>

            <div class="oc-card-body">
                <div class="oc-table-wrap">
                    <table class="oc-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Land</th>
                                <th>Staatsangehörigkeit</th>
                                <th class="text-right">Aktion</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $lang)
                                <tr>
                                    <td>
                                        <strong>{{ $lang->id }}</strong>
                                    </td>

                                    <td>
                                        <div class="country-name">{{ $lang->country }}</div>
                                    </td>

                                    <td>
                                        <span class="country-nationality">{{ $lang->nationality }}</span>
                                    </td>

                                    <td>
                                        <div class="country-actions">
                                            <button
                                                type="button"
                                                class="oc-btn-icon edit js-edit-country"
                                                title="Bearbeiten"
                                                data-id="{{ $lang->id }}"
                                                data-country="{{ e($lang->country) }}"
                                                data-nationality="{{ e($lang->nationality) }}"
                                            >
                                                <i class="feather icon-edit"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="oc-btn-icon delete js-delete-country"
                                                title="Löschen"
                                                data-id="{{ $lang->id }}"
                                                data-country="{{ e($lang->country) }}"
                                                data-url="{{ route('country.destroy', ['id' => $lang->id]) }}"
                                            >
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="oc-empty">
                                        Keine Länder gefunden.
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

    {{-- EDIT MODAL --}}
    <div class="oc-modal-backdrop" id="editCountryModal" aria-hidden="true">
        <div class="oc-modal" role="dialog" aria-modal="true" aria-labelledby="editCountryTitle">
            <form method="POST" action="{{ route('country.update') }}">
                @csrf

                <div class="oc-modal-header">
                    <div>
                        <h3 class="oc-modal-title" id="editCountryTitle">Land bearbeiten</h3>
                        <div class="oc-modal-sub">Land und Staatsangehörigkeit aktualisieren</div>
                    </div>

                    <button type="button" class="oc-modal-close js-close-modal" data-modal="editCountryModal">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="oc-modal-body">
                    <input type="hidden" name="id" id="edit_country_id">

                    <div class="mb-2">
                        <label class="oc-label">Land</label>
                        <input
                            type="text"
                            class="oc-input"
                            name="country"
                            id="edit_country_name"
                            required
                        >
                    </div>

                    <div>
                        <label class="oc-label">Staatsangehörigkeit</label>
                        <input
                            type="text"
                            class="oc-input"
                            name="nationality"
                            id="edit_country_nationality"
                            required
                        >
                    </div>
                </div>

                <div class="oc-modal-footer">
                    <button type="button" class="oc-btn-soft js-close-modal" data-modal="editCountryModal">
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

    {{-- DELETE MODAL --}}
    <div class="oc-modal-backdrop" id="deleteCountryModal" aria-hidden="true">
        <div class="oc-modal" role="dialog" aria-modal="true" aria-labelledby="deleteCountryTitle">
            <div class="oc-modal-header">
                <div>
                    <h3 class="oc-modal-title" id="deleteCountryTitle">Land löschen</h3>
                    <div class="oc-modal-sub">Diese Aktion kann nicht automatisch rückgängig gemacht werden</div>
                </div>

                <button type="button" class="oc-modal-close js-close-modal" data-modal="deleteCountryModal">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="oc-modal-body">
                <div class="delete-warning">
                    Möchten Sie das Land <strong id="deleteCountryName"></strong> wirklich löschen?
                </div>
            </div>

            <div class="oc-modal-footer">
                <button type="button" class="oc-btn-soft js-close-modal" data-modal="deleteCountryModal">
                    Abbrechen
                </button>

                <form method="POST" action="#" id="deleteCountryForm" style="display:inline-flex;margin:0;">
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
@endsection

@section('script')
    <script>
    (function () {
        'use strict';

        let rowIndex = 0;

        const rows = document.getElementById('countryRows');
        const addBtn = document.getElementById('addCountryRow');
        const addInlineBtn = document.getElementById('addCountryRowInline');

        function refreshIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        function addCountryRow() {
            if (!rows) return;

            rowIndex++;

            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>
                    <input
                        type="text"
                        class="oc-input"
                        placeholder="z. B. Österreich"
                        name="country[${rowIndex}][country]"
                        required
                    >
                </td>

                <td>
                    <input
                        type="text"
                        class="oc-input"
                        placeholder="z. B. Österreichisch"
                        name="country[${rowIndex}][nationality]"
                        required
                    >
                </td>

                <td class="text-right">
                    <button type="button" class="oc-btn-icon delete js-remove-country-row" title="Zeile entfernen">
                        <i class="feather icon-trash-2"></i>
                    </button>
                </td>
            `;

            rows.appendChild(tr);
            refreshIcons();
        }

        addBtn?.addEventListener('click', addCountryRow);
        addInlineBtn?.addEventListener('click', addCountryRow);

        document.addEventListener('click', function (event) {
            const removeBtn = event.target.closest('.js-remove-country-row');

            if (removeBtn) {
                removeBtn.closest('tr')?.remove();
            }
        });

        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');

            document.body.style.overflow = 'hidden';

            setTimeout(function () {
                const firstInput = modal.querySelector('input:not([type="hidden"])');
                if (firstInput) firstInput.focus();
            }, 100);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');

            document.body.style.overflow = '';
        }

        document.addEventListener('click', function (event) {
            const closeBtn = event.target.closest('.js-close-modal');

            if (closeBtn) {
                closeModal(closeBtn.dataset.modal);
                return;
            }

            if (event.target.classList.contains('oc-modal-backdrop')) {
                closeModal(event.target.id);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;

            document.querySelectorAll('.oc-modal-backdrop.is-open').forEach(function (modal) {
                closeModal(modal.id);
            });
        });

        document.querySelectorAll('.js-edit-country').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('edit_country_id').value = button.dataset.id || '';
                document.getElementById('edit_country_name').value = button.dataset.country || '';
                document.getElementById('edit_country_nationality').value = button.dataset.nationality || '';

                openModal('editCountryModal');
            });
        });

        document.querySelectorAll('.js-delete-country').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('deleteCountryName').textContent = button.dataset.country || '';
                document.getElementById('deleteCountryForm').action = button.dataset.url || '#';

                openModal('deleteCountryModal');
            });
        });

        document.addEventListener('DOMContentLoaded', refreshIcons);

        @if(Session::has('update_msg'))
            if (typeof toastr !== 'undefined') toastr.success(@json(session('updated_msg')));
        @endif

        @if(Session::has('save_msg'))
            if (typeof toastr !== 'undefined') toastr.success(@json(session('save_msg')));
        @endif

        @if(Session::has('delete_msg'))
            if (typeof toastr !== 'undefined') toastr.error(@json(session('delete_msg')));
        @endif
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
                label: 'Mitarbeiter',
                url: "{{ url('emp?status_tab=active') }}"
            },
            {
                label: 'Länder und Nationalitäten',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush