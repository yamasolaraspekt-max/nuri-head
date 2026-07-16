@extends('admin.layouts.app')

@section('title', 'VERTRAGSARTEN')

@section('style')
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

        .oc-btn-icon {
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

        .oc-btn-icon:hover {
            background: #f9fafb;
            color: var(--text-main);
            border-color: #d1d5db;
            text-decoration: none;
        }

        .oc-btn-icon.primary {
            color: var(--primary);
            border-color: rgba(143, 199, 62, .25);
            background: var(--primary-light);
        }

        .oc-btn-icon.danger {
            color: var(--danger);
            border-color: rgba(239, 68, 68, .18);
            background: var(--danger-light);
        }

        .oc-analytics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        @media(max-width:900px) {
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

        .oc-stat-icon.search {
            background: var(--primary-light);
            color: #365314;
        }

        .oc-stat-icon.duration {
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

        .oc-toolbar {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto auto;
            gap: 12px;
            align-items: end;
        }

        @media(max-width:800px) {
            .oc-toolbar {
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

        .oc-input {
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

        .oc-input:focus {
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
            min-width: 820px;
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

        .oc-table tbody td,
        .oc-table tbody th {
            padding: 12px 10px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            background: #fff;
        }

        .oc-table tbody td:first-child,
        .oc-table tbody th:first-child {
            border-left: 1px solid var(--border);
            border-radius: 14px 0 0 14px;
        }

        .oc-table tbody td:last-child {
            border-right: 1px solid var(--border);
            border-radius: 0 14px 14px 0;
        }

        .oc-id-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--primary-light);
            color: #365314;
            font-weight: 900;
            font-size: 13px;
        }

        .oc-main-text {
            font-size: 14px;
            color: #111827;
            font-weight: 900;
        }

        .oc-muted {
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 700;
        }

        .oc-duration-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--blue-light);
            color: #075985;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
            gap: 6px;
        }

        .oc-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .text-right {
            text-align: right;
        }

        .oc-empty {
            padding: 34px 18px !important;
            text-align: center;
            color: var(--text-muted);
            font-weight: 800;
        }

        .oc-pagination {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .oc-error {
            color: #b91c1c;
            font-size: 12px;
            font-weight: 800;
            margin-top: 6px;
        }

        .oc-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            z-index: 9998;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .oc-modal-backdrop.show {
            display: flex;
        }

        .oc-modal {
            width: 100%;
            max-width: 620px;
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transform: translateY(12px) scale(.98);
            opacity: 0;
            transition: var(--transition);
        }

        .oc-modal-backdrop.show .oc-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .oc-modal.sm {
            max-width: 500px;
        }

        .oc-modal-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .oc-modal-title {
            margin: 0;
            color: #111827;
            font-size: 17px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .oc-modal-close {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            color: #6b7280;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .oc-modal-close:hover {
            background: #f9fafb;
            color: #111827;
        }

        .oc-modal-body {
            padding: 18px;
        }

        .oc-modal-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .oc-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        @media(max-width:650px) {
            .oc-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .oc-alert-danger {
            padding: 13px 14px;
            border-radius: 14px;
            background: var(--danger-light);
            border: 1px solid rgba(239, 68, 68, .20);
            color: #991b1b;
            font-weight: 800;
            font-size: 13px;
            line-height: 1.45;
        }

        .oc-alert-danger strong {
            color: #7f1d1d;
        }

        @media(max-width:768px) {
            .oc-wrap {
                padding: 18px;
                margin: 0;
            }

            .oc-header {
                margin-top: 70px;
            }

            .oc-title {
                font-size: 21px;
            }

            .oc-card-body {
                padding: 14px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="oc-wrap">
        @php
            $totalContracts = method_exists($data, 'total') ? $data->total() : $data->count();
            $searchValue = request('search');
        @endphp

        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">Vertragsarten</div>
                    <div class="oc-sub">
                        Vertragsarten und optionale Laufzeiten zentral verwalten.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                        <span>›</span>
                        <span class="current">Vertragsarten</span>
                    </div>
                </div>

                <button type="button" class="oc-btn" data-open-modal="createContractModal">
                    <i class="feather icon-plus"></i>
                    Neue Vertragsart
                </button>
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-file-text"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Gesamt</div>
                    <div class="oc-stat-value">{{ $totalContracts }}</div>
                    <div class="oc-stat-sub">Vertragsarten im System</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon search">
                    <i class="feather icon-search"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Suche</div>
                    <div class="oc-stat-value">{{ $searchValue ? 'Aktiv' : 'Aus' }}</div>
                    <div class="oc-stat-sub">{{ $searchValue ?: 'Kein Filter aktiv' }}</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon duration">
                    <i class="feather icon-clock"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Laufzeit</div>
                    <div class="oc-stat-value">Optional</div>
                    <div class="oc-stat-sub">Kann leer bleiben</div>
                </div>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Filter</h3>
                    <div class="oc-card-sub">Nach Vertragsart oder Laufzeit suchen</div>
                </div>
            </div>

            <div class="oc-card-body">
                <form action="{{ route('contract.type.info') }}" method="GET" class="oc-toolbar">
                    <div class="oc-form-group">
                        <label class="oc-label">Suche</label>
                        <input type="text" name="search" class="oc-input" placeholder="Vertragsart suchen..."
                            value="{{ request('search') }}">
                    </div>

                    <button class="oc-btn" type="submit">
                        <i class="feather icon-search"></i>
                        Suchen
                    </button>

                    @if(request('search'))
                        <a href="{{ route('contract.type.info') }}" class="oc-btn-soft">
                            <i class="feather icon-x"></i>
                            Zurücksetzen
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-card-header">
                <div>
                    <h3 class="oc-card-title">Vertragsarten</h3>
                    <div class="oc-card-sub">Alle gespeicherten Vertragsarten</div>
                </div>

                <button type="button" class="oc-btn-soft" data-open-modal="createContractModal">
                    <i class="feather icon-plus-circle"></i>
                    Neuer Eintrag
                </button>
            </div>

            <div class="oc-card-body">
                <div class="oc-table-wrap">
                    <table class="oc-table">
                        <thead>
                            <tr>
                                <th style="width:80px;">ID</th>
                                <th>Vertragsart</th>
                                <th>Laufzeit</th>
                                <th class="text-right" style="width:160px;">Aktion</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    <th>
                                        <span class="oc-id-badge">
                                            {{ $item->id }}
                                        </span>
                                    </th>

                                    <td>
                                        <div class="oc-main-text">{{ $item->contract_type }}</div>
                                        <div class="oc-muted">Datensatz #{{ $item->id }}</div>
                                    </td>

                                    <td>
                                        @if(!empty($item->contract_duration))
                                            <span class="oc-duration-pill">
                                                <i class="feather icon-clock"></i>
                                                {{ $item->contract_duration }}
                                            </span>
                                        @else
                                            <span class="oc-muted">Keine Laufzeit angegeben</span>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        <div class="oc-actions">
                                            <button type="button" class="oc-btn-icon primary" title="Bearbeiten"
                                                data-open-modal="editContractModal{{ $item->id }}">
                                                <i class="feather icon-edit"></i>
                                            </button>

                                            <button type="button" class="oc-btn-icon danger" title="Löschen"
                                                data-open-modal="deleteContractModal{{ $item->id }}">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="oc-empty">
                                        <i class="feather icon-inbox"></i>
                                        Keine Vertragsarten gefunden.
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

    {{-- MODAL: NEU --}}
    <div class="oc-modal-backdrop" id="createContractModal" aria-hidden="true">
        <div class="oc-modal" role="dialog" aria-modal="true">
            <div class="oc-modal-header">
                <h3 class="oc-modal-title">Neue Vertragsart</h3>

                <button type="button" class="oc-modal-close" data-close-modal>
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('contract.type.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="oc-modal-body">
                    <div class="oc-form-grid">
                        <div class="oc-form-group">
                            <label class="oc-label">Vertragsart</label>
                            <input type="text" class="oc-input" name="contract_type" value="{{ old('contract_type') }}"
                                placeholder="Beispiel: Vollzeit" required>

                            @if ($errors->has('contract_type'))
                                <div class="oc-error">{{ $errors->first('contract_type') }}</div>
                            @endif
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label">Laufzeit optional</label>
                            <input type="text" class="oc-input" name="duration" value="{{ old('duration') }}"
                                placeholder="Beispiel: 12 Monate">

                            @if ($errors->has('duration'))
                                <div class="oc-error">{{ $errors->first('duration') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="oc-modal-footer">
                    <button type="button" class="oc-btn-soft" data-close-modal>
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

    {{-- MODALS: BEARBEITEN + LÖSCHEN --}}
    @foreach($data as $item)
        <div class="oc-modal-backdrop" id="editContractModal{{ $item->id }}" aria-hidden="true">
            <div class="oc-modal" role="dialog" aria-modal="true">
                <div class="oc-modal-header">
                    <h3 class="oc-modal-title">Vertragsart bearbeiten</h3>

                    <button type="button" class="oc-modal-close" data-close-modal>
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('contract.type.update') }}">
                    @csrf

                    <input type="hidden" name="id" value="{{ $item->id }}">

                    <div class="oc-modal-body">
                        <div class="oc-form-grid">
                            <div class="oc-form-group">
                                <label class="oc-label">Vertragsart</label>
                                <input type="text" class="oc-input" name="contract_type"
                                    value="{{ old('contract_type', $item->contract_type) }}" required>

                                @if ($errors->has('contract_type'))
                                    <div class="oc-error">{{ $errors->first('contract_type') }}</div>
                                @endif
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Laufzeit optional</label>
                                <input type="text" class="oc-input" name="duration"
                                    value="{{ old('duration', $item->contract_duration) }}">

                                @if ($errors->has('duration'))
                                    <div class="oc-error">{{ $errors->first('duration') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="oc-modal-footer">
                        <button type="button" class="oc-btn-soft" data-close-modal>
                            Abbrechen
                        </button>

                        <button type="submit" class="oc-btn">
                            <i class="feather icon-save"></i>
                            Aktualisieren
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="oc-modal-backdrop" id="deleteContractModal{{ $item->id }}" aria-hidden="true">
            <div class="oc-modal sm" role="dialog" aria-modal="true">
                <div class="oc-modal-header">
                    <h3 class="oc-modal-title">Eintrag löschen</h3>

                    <button type="button" class="oc-modal-close" data-close-modal>
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="oc-modal-body">
                    <div class="oc-alert-danger">
                        <strong>Möchten Sie diesen Eintrag wirklich löschen?</strong>
                        <br>
                        Vertragsart: {{ $item->contract_type }}
                        <br>
                        Datensatz-ID: {{ $item->id }}
                    </div>
                </div>

                <div class="oc-modal-footer">
                    <button type="button" class="oc-btn-soft" data-close-modal>
                        Nein
                    </button>

                    <form action="{{ route('contract.type.destroy', $item->id) }}" method="POST" style="display:inline-flex;margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="oc-btn-danger">
                            <i class="feather icon-trash"></i>
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

                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';

                const firstInput = modal.querySelector('input:not([type="hidden"]), button, a');

                if (firstInput) {
                    setTimeout(function () {
                        firstInput.focus();
                    }, 80);
                }

                if (window.feather) {
                    window.feather.replace();
                }
            }

            function closeModal(modal) {
                if (!modal) return;

                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            document.addEventListener('click', function (event) {
                const openButton = event.target.closest('[data-open-modal]');

                if (openButton) {
                    event.preventDefault();
                    openModal(openButton.getAttribute('data-open-modal'));
                    return;
                }

                const closeButton = event.target.closest('[data-close-modal]');

                if (closeButton) {
                    event.preventDefault();
                    closeModal(closeButton.closest('.oc-modal-backdrop'));
                    return;
                }

                if (event.target.classList.contains('oc-modal-backdrop')) {
                    closeModal(event.target);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;

                document.querySelectorAll('.oc-modal-backdrop.show').forEach(function (modal) {
                    closeModal(modal);
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                @if(Session::has('update_msg'))
                    if (typeof toastr !== 'undefined') {
                        toastr.success(@json(session('update_msg')));
                    }
                @endif

                @if(Session::has('updated_msg'))
                    if (typeof toastr !== 'undefined') {
                        toastr.success(@json(session('updated_msg')));
                    }
                @endif

                @if(Session::has('save_msg'))
                    if (typeof toastr !== 'undefined') {
                        toastr.success(@json(session('save_msg')));
                    }
                @endif

                @if(Session::has('delete_msg'))
                    if (typeof toastr !== 'undefined') {
                        toastr.error(@json(session('delete_msg')));
                    }
                @endif

                @if($errors->any())
                    openModal('createContractModal');
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
                url: "{{ url('/') }}"
            },
            {
                label: 'Vertragsarten',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush