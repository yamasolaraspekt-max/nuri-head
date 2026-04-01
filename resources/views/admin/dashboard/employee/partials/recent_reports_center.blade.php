@extends('admin.layouts.app')

@section('title', 'Berichte Dashboard')

@section('style')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts (Outfit) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Scoped Root Variables for this module */
        .rr-wrap {
            --rr-primary: #73b2d4;
            --rr-primary-dark: #5da0c2;
            --rr-primary-light: #c0d8ea;
            --rr-secondary: #93c21c;
            --rr-secondary-light: #cfe09b;

            --rr-text-dark: #2c3e50;
            --rr-text-muted: #7f8c8d;
            --rr-bg-body: #f0f4f8;
            --rr-surface: #ffffff;
            --rr-border: #e2e8f0;

            --rr-status-inquiry-bg: #e3f2fd; --rr-status-inquiry-text: #1565c0;
            --rr-status-lead-bg: var(--rr-secondary-light); --rr-status-lead-text: #5a7d0c;
            --rr-status-task-bg: #f1f5f9; --rr-status-task-text: #475569;
            --rr-status-appt-bg: var(--rr-primary-light); --rr-status-appt-text: #2c5c7a;
            --rr-status-ticket-bg: #fff3cd; --rr-status-ticket-text: #856404;

            font-family: 'Outfit', sans-serif;
            padding: 20px;
            background-color: var(--rr-bg-body);
        }

        /* Local Reset to avoid external CSS messing up layout */
        .rr-wrap * { box-sizing: border-box; }

        .rr-container { max-width: 1400px; margin: 0 auto; }

        /* Utility Classes (Prefixed) */
        .rr-flex-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .rr-mb-2 { margin-bottom: .5rem; }
        .rr-mb-4 { margin-bottom: 1.5rem; }
        .rr-me-2 { margin-right: .5rem; }
        .rr-gap-2 { gap: .5rem; }
        .rr-text-muted { color: var(--rr-text-muted); }
        .rr-small { font-size: .875rem; }
        .rr-fw-bold { font-weight: 700; }
        .rr-fw-medium { font-weight: 500; }
        .rr-text-right { text-align: right; }
        .rr-text-center { text-align: center; }

        .rr-wrap h3 { font-size: 1.5rem; font-weight: 700; color: var(--rr-text-dark); margin: 0; }

        /* Stats Grid */
        .rr-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .rr-stat-card {
            background: var(--rr-surface);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(115, 178, 212, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .rr-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(115, 178, 212, 0.15); }

        .rr-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .rr-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .rr-stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1; margin-bottom: 0.25rem; color: var(--rr-text-dark); }
        .rr-stat-label { font-size: 0.875rem; color: var(--rr-text-muted); font-weight: 500; }

        /* Main Card */
        .rr-main-card {
            background: var(--rr-surface);
            border-radius: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            overflow: hidden;
        }

        /* Filter Bar */
        .rr-filter-bar {
            background: #f8fafc;
            border-bottom: 1px solid var(--rr-border);
            padding: 1.5rem;
        }

        .rr-filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .rr-filter-group { display: flex; flex-direction: column; }
        .rr-filter-grid > * { min-width: 0; } /* IMPORTANT for Select2 width inside grid */

        .rr-filter-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #a0aec0;
            margin-bottom: 6px;
        }

        /* Forms */
        .rr-input-wrapper { position: relative; display: flex; align-items: center; }
        .rr-input-icon { position: absolute; left: 1rem; color: var(--rr-text-muted); pointer-events: none; }

        .rr-form-control, .rr-form-select {
            width: 100%;
            border: 1px solid var(--rr-border);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            font-family: inherit;
            background-color: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            height: 42px;
            color: var(--rr-text-dark);
        }

        .rr-has-icon { padding-left: 2.5rem; }

        .rr-form-control:focus, .rr-form-select:focus {
            border-color: var(--rr-primary);
            box-shadow: 0 0 0 3px rgba(115, 178, 212, 0.2);
        }

        textarea.rr-form-control { height: auto; resize: vertical; }

        /* Buttons (Scoped) */
        .rr-btn {
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-decoration: none;
            height: 42px;
            background: #fff;
            font-size: 1rem;
        }
        .rr-btn-sm { height: 32px; font-size: 0.85rem; padding: 0 0.75rem; }

        .rr-btn-primary-soft { background-color: var(--rr-primary); color: #fff; }
        .rr-btn-primary-soft:hover { background-color: var(--rr-primary-dark); transform: translateY(-1px); color: #fff; }
        .rr-btn-primary-soft:disabled { opacity: 0.7; cursor: not-allowed; }

        .rr-btn-light { background-color: #fff; border: 1px solid var(--rr-border); color: var(--rr-text-dark); }
        .rr-btn-light:hover { background-color: #f8fafc; }

        .rr-btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--rr-text-muted);
            background: transparent;
            border: 1px solid var(--rr-border);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .rr-btn-icon:hover { border-color: var(--rr-primary); color: var(--rr-primary); background: #f0f7fb; }
        .rr-btn-icon[disabled] { opacity: .45; cursor: not-allowed; }

        /* Table */
        .rr-table-wrapper { overflow-x: auto; width: 100%; }
        .rr-table { width: 100%; border-collapse: collapse; min-width: 800px; }

        .rr-table th {
            background: #fff;
            color: var(--rr-text-muted);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid var(--rr-border);
            text-align: left;
        }

        .rr-table td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--rr-border);
            background: #fff;
            color: var(--rr-text-dark);
        }

        .rr-table tr:last-child td { border-bottom: none; }
        .rr-table tr:hover td { background-color: #fcfdfe; }

        /* Badges */
        .rr-status-badge {
            padding: 0.4em 0.8em;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .rr-badge-inquiry { background: var(--rr-status-inquiry-bg); color: var(--rr-status-inquiry-text); }
        .rr-badge-lead { background: var(--rr-status-lead-bg); color: var(--rr-status-lead-text); }
        .rr-badge-task { background: var(--rr-status-task-bg); color: var(--rr-status-task-text); }
        .rr-badge-appointment { background: var(--rr-status-appt-bg); color: var(--rr-status-appt-text); }
        .rr-badge-ticket { background: var(--rr-status-ticket-bg); color: var(--rr-status-ticket-text); }

        .rr-user-cell { display: flex; align-items: center; gap: 0.75rem; }
        .rr-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rr-primary-light), #fff);
            color: var(--rr-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            flex-shrink: 0;
        }

        .rr-text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 280px;
            display: block;
        }

        .rr-table-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--rr-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Sidebar / Offcanvas */
        .rr-sidebar-backdrop {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.3);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s;
        }
        .rr-sidebar-backdrop.active { opacity: 1; visibility: visible; }

        .rr-sidebar {
            position: fixed;
            top: 0; right: 0;
            width: 100%;
            max-width: 450px;
            height: 100%;
            background: #fff;
            z-index: 9999;
            box-shadow: -5px 0 30px rgba(0,0,0,0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }
        .rr-sidebar.active { transform: translateX(0); }

        .rr-sidebar-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--rr-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rr-sidebar-body { padding: 2rem; overflow-y: auto; flex: 1; }

        .rr-close-btn { background: none; border: none; font-size: 1.25rem; color: var(--rr-text-muted); cursor: pointer; }

        .rr-highlight-box {
            background: linear-gradient(135deg, var(--rr-primary-light) 0%, #fff 100%);
            border-radius: 12px;
            padding: 1.25rem;
            border-left: 4px solid var(--rr-primary);
            margin-bottom: 1.5rem;
        }

        /* Radio Group */
        .rr-radio-group { display: flex; gap: 0.5rem; }
        .rr-radio-option { flex: 1; position: relative; }
        .rr-radio-option input { position: absolute; opacity: 0; height: 0; width: 0; }
        .rr-radio-label {
            display: block;
            text-align: center;
            padding: 0.5rem;
            border: 1px solid var(--rr-border);
            border-radius: 8px;
            cursor: pointer;
            color: var(--rr-text-muted);
            font-weight: 500;
            transition: all 0.2s;
        }
        .rr-radio-option input:checked + .rr-radio-label {
            border-color: var(--rr-secondary);
            color: var(--rr-secondary);
            background-color: #f7fcf0;
            font-weight: 600;
        }

        /* Select2 Customization */
        .rr-select2-wrap { position: relative; }
        .rr-select2-badge {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(115,178,212,.15);
            color: var(--rr-primary-dark);
            border: 1px solid rgba(115,178,212,.25);
            pointer-events: none;
            z-index: 1;
        }

        /* Force Select2 Styles to match */
        .rr-wrap .rr-select2-wrap .select2-container { width: 100% !important; min-width: 0 !important; }
        .rr-wrap .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid var(--rr-border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            padding-left: 10px;
            background: #fff;
        }
        .rr-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            color: var(--rr-text-dark);
            padding-left: 8px;
        }
        .rr-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        .rr-wrap .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--rr-primary);
            box-shadow: 0 0 0 3px rgba(115, 178, 212, 0.2);
        }
        .rr-wrap .select2-dropdown {
            border: 1px solid var(--rr-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0,0,0,.08);
            max-width: 100%;
        }
        .rr-wrap .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--rr-border);
            border-radius: 10px;
            padding: 10px 12px;
            outline: none;
        }
        .rr-wrap .select2-results__option { padding: 10px 12px; }
        .rr-wrap .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: rgba(115,178,212,.12);
            color: var(--rr-text-dark);
        }

        /* Employee Template */
        .rr-emp-option { display: flex; align-items: center; gap: 10px; }
        .rr-emp-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rr-primary-light), #fff);
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.06);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--rr-primary-dark);
            flex-shrink: 0;
            font-size: 12px;
        }
        .rr-emp-meta { display: flex; flex-direction: column; line-height: 1.2; }
        .rr-emp-name { font-weight: 600; color: var(--rr-text-dark); }
        .rr-emp-sub { font-size: 12px; color: var(--rr-text-muted); }

        /* Existing reports list in sidebar */
        .rr-existing-box {
            border: 1px solid var(--rr-border);
            border-radius: 12px;
            padding: 12px;
            max-height: 260px;
            overflow: auto;
            background: #fff;
        }
        .rr-report-item { padding: 10px 10px; border-bottom: 1px solid var(--rr-border); }
        .rr-report-item:last-child { border-bottom: none; }
        .rr-report-head { display: flex; justify-content: space-between; gap: 10px; }
        .rr-report-who { font-weight: 700; color: var(--rr-text-dark); font-size: 13px; }
        .rr-report-when { font-size: 12px; color: var(--rr-text-muted); white-space: nowrap; }
        .rr-report-text { margin-top: 6px; font-size: 13px; color: var(--rr-text-dark); white-space: pre-wrap; }

        @media (max-width: 768px) {
            .rr-wrap { padding: 10px; }
            .rr-flex-row { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .rr-table-footer { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .rr-btn-primary-soft { width: 100%; }
        }

        /* FIX: Select2 dropdown too long */
        .rr-wrap .select2-container--open .select2-dropdown { max-height: 320px; }
        .rr-wrap .select2-results__options { max-height: 260px; overflow-y: auto; }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="rr-wrap">
        <div class="rr-container">
            <!-- Header -->
            <div class="rr-flex-row rr-mb-4">
                <div>
                    <h3 class="rr-mb-2">Berichte Übersicht</h3>
                    <span class="rr-text-muted rr-small">Willkommen zurück, Admin</span>
                </div>
                <button class="rr-btn rr-btn-primary-soft" type="button" id="rr-export" disabled>
                    <i class="fa-solid fa-cloud-arrow-down rr-me-2"></i>Exportieren
                </button>
            </div>

            <!-- Stats Grid -->
            <div class="rr-stats-grid">
                <div class="rr-stat-card">
                    <div class="rr-stat-header">
                        <div>
                            <div class="rr-stat-value" id="rr-stat-total">—</div>
                            <div class="rr-stat-label">Treffer gesamt</div>
                        </div>
                        <div class="rr-stat-icon" style="background: var(--rr-primary-light); color: var(--rr-primary);">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                    </div>
                    <div class="rr-small rr-text-muted rr-fw-medium">Aktueller Filter</div>
                </div>

                <div class="rr-stat-card">
                    <div class="rr-stat-header">
                        <div>
                            <div class="rr-stat-value" id="rr-stat-inquiry">—</div>
                            <div class="rr-stat-label">Anfragen</div>
                        </div>
                        <div class="rr-stat-icon" style="background: var(--rr-status-inquiry-bg); color: var(--rr-status-inquiry-text);">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                    </div>
                    <div class="rr-small rr-text-muted rr-fw-medium">Diese Seite</div>
                </div>

                <div class="rr-stat-card">
                    <div class="rr-stat-header">
                        <div>
                            <div class="rr-stat-value" id="rr-stat-appointment">—</div>
                            <div class="rr-stat-label">Termine</div>
                        </div>
                        <div class="rr-stat-icon" style="background: var(--rr-status-appt-bg); color: var(--rr-status-appt-text);">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="rr-small rr-text-muted rr-fw-medium">Diese Seite</div>
                </div>

                <div class="rr-stat-card">
                    <div class="rr-stat-header">
                        <div>
                            <div class="rr-stat-value" id="rr-stat-lead">—</div>
                            <div class="rr-stat-label">Leads</div>
                        </div>
                        <div class="rr-stat-icon" style="background: var(--rr-secondary-light); color: #5a7d0c;">
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                    <div class="rr-small rr-text-muted rr-fw-medium">Diese Seite</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="rr-main-card">
                <!-- Filter Bar -->
                <div class="rr-filter-bar">
                    <div class="rr-filter-grid">
                        <!-- Search -->
                        <div class="rr-filter-group" style="grid-column: span 2; min-width: 250px;">
                            <label class="rr-filter-label">Suche</label>
                            <div class="rr-input-wrapper">
                                <i class="fa-solid fa-search rr-input-icon"></i>
                                <input id="rr-q" type="text" class="rr-form-control rr-has-icon" placeholder="Suchen nach...">
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="rr-filter-group">
                            <label class="rr-filter-label">Typ</label>
                            <select id="rr-type" class="rr-form-select">
                                <option value="">Alle Typen</option>
                                <option value="inquiry">Anfrage</option>
                                <option value="task">Aufgabe</option>
                                <option value="appointment">Termin</option>
                                <option value="ticket">Ticket</option>
                                <option value="lead">Lead</option>
                            </select>
                        </div>

                        <!-- Employee (Select2) -->
                        <div class="rr-filter-group rr-select2-wrap">
                            <label class="rr-filter-label">Mitarbeiter</label>
                            <select id="rr-employee" class="rr-form-select">
                                <option value="">Alle</option>
                                @foreach(($employees ?? []) as $e)
                                    @php $full = trim(($e->name ?? '').' '.($e->lastname ?? '')); @endphp
                                    <option value="{{ $e->id }}" data-sub="#{{ $e->id }}">
                                        {{ $full ?: ('Mitarbeiter #'.$e->id) }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="rr-select2-badge"><i class="fa-solid fa-magnifying-glass rr-me-2"></i>Suche</span>
                        </div>

                        <!-- Date -->
                        <div class="rr-filter-group">
                            <label class="rr-filter-label">Von</label>
                            <input id="rr-from" type="date" class="rr-form-control">
                        </div>
                        <div class="rr-filter-group">
                            <label class="rr-filter-label">Bis</label>
                            <input id="rr-to" type="date" class="rr-form-control">
                        </div>

                        <!-- Reset Button -->
                        <div class="rr-filter-group">
                            <button id="rr-reset" class="rr-btn rr-btn-light w-100" type="button">
                                <i class="fa-solid fa-rotate-left rr-me-2"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="rr-table-wrapper">
                    <table class="rr-table">
                        <thead>
                        <tr>
                            <th style="padding-left: 2rem;">Typ</th>
                            <th>Betreff / Referenz</th>
                            <th>Mitarbeiter</th>
                            <th>Datum</th>
                            <th>Letzter Bericht</th>
                            <th class="rr-text-right" style="padding-right: 2rem;">Aktion</th>
                        </tr>
                        </thead>
                        <tbody id="rr-tbody">
                        <tr>
                            <td colspan="6" class="rr-text-center" style="padding: 2rem; color: var(--rr-text-muted); text-align:center;">
                                Lade Daten...
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="rr-table-footer">
                    <div class="rr-text-muted rr-small rr-fw-medium" id="rr-meta">Lade Daten...</div>
                    <div class="rr-flex-row rr-gap-2" style="justify-content: flex-end;">
                        <button class="rr-btn rr-btn-light rr-btn-sm" id="rr-prev" type="button">Zurück</button>
                        <button class="rr-btn rr-btn-light rr-btn-sm" id="rr-next" type="button">Weiter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Sidebar -->
    <div class="rr-sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="rr-sidebar" id="rrSidebar">
        <div class="rr-sidebar-header">
            <div>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.25rem;">Bericht hinzufügen</h3>
                <small class="rr-text-muted">Erstellen Sie einen neuen Eintrag.</small>
            </div>
            <button class="rr-close-btn" id="sidebarCloseBtn" type="button"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="rr-sidebar-body">
            <form id="rrAddForm">
                @csrf
                <input type="hidden" name="type" id="rrAddType">
                <input type="hidden" name="id" id="rrAddEntityId">

                <div class="rr-highlight-box">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-link" style="color:var(--rr-secondary);"></i>
                        <span class="rr-small rr-fw-bold" style="text-transform: uppercase; color:var(--rr-secondary);">Referenz</span>
                    </div>
                    <div id="rrAddRef" class="rr-fw-bold" style="font-size: 1.1rem; color: var(--rr-text-dark);">...</div>
                </div>

                <!-- Existing reports for selected record -->
                <div class="rr-filter-group rr-mb-4">
                    <label class="rr-filter-label">Vorhandene Berichte</label>
                    <div id="rrExistingReports" class="rr-existing-box">
                        <div class="rr-text-muted rr-small">Wählen Sie einen Eintrag…</div>
                    </div>
                </div>

                <div class="rr-filter-group rr-mb-4">
                    <label class="rr-filter-label">Bericht</label>
                    <textarea name="report" id="rrAddReport" class="rr-form-control" rows="8" required
                              placeholder="Was ist passiert? Schreiben Sie hier..." style="padding: 1rem;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="rr-mb-4">
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">Datum</label>
                        <input type="date" name="report_date" id="rrAddReportDate" class="rr-form-control">
                    </div>
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">Wiedervorlage</label>
                        <input type="date" name="due_date" id="rrAddDueDate" class="rr-form-control">
                    </div>
                </div>

                <div class="rr-filter-group rr-mb-4">
                    <label class="rr-filter-label">Status Update</label>
                    <div class="rr-radio-group">
                        <div class="rr-radio-option">
                            <input type="radio" name="status" id="st1" value="unchanged" checked>
                            <label for="st1" class="rr-radio-label">Unverändert</label>
                        </div>
                        <div class="rr-radio-option">
                            <input type="radio" name="status" id="st2" value="done">
                            <label for="st2" class="rr-radio-label">Erledigt</label>
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 2rem;">
                    <button type="submit" class="rr-btn rr-btn-primary-soft w-100" id="rrAddSubmit" style="padding: 0.8rem;">
                        Speichern bestätigen
                    </button>
                    <button type="button" class="rr-btn rr-btn-light w-100" id="sidebarCancelBtn">Abbrechen</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery (Select2 requirement) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const API = {
                fetch: "{{ route('admin.overdue-center.reports.fetch') }}",
                store: "{{ route('admin.overdue-center.report.store') }}",
                recordReports: "{{ route('admin.recent-reports.record-reports') }}",
            };

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const els = {
                q: document.getElementById('rr-q'),
                type: document.getElementById('rr-type'),
                employee: document.getElementById('rr-employee'),
                from: document.getElementById('rr-from'),
                to: document.getElementById('rr-to'),
                reset: document.getElementById('rr-reset'),

                tbody: document.getElementById('rr-tbody'),
                meta: document.getElementById('rr-meta'),
                prev: document.getElementById('rr-prev'),
                next: document.getElementById('rr-next'),

                statTotal: document.getElementById('rr-stat-total'),
                statInquiry: document.getElementById('rr-stat-inquiry'),
                statAppointment: document.getElementById('rr-stat-appointment'),
                statLead: document.getElementById('rr-stat-lead'),

                sidebar: document.getElementById('rrSidebar'),
                backdrop: document.getElementById('sidebarBackdrop'),
                closeBtn: document.getElementById('sidebarCloseBtn'),
                cancelBtn: document.getElementById('sidebarCancelBtn'),

                addForm: document.getElementById('rrAddForm'),
                addType: document.getElementById('rrAddType'),
                addId: document.getElementById('rrAddEntityId'),
                addRef: document.getElementById('rrAddRef'),
                existingReports: document.getElementById('rrExistingReports'),
                addReport: document.getElementById('rrAddReport'),
                addReportDate: document.getElementById('rrAddReportDate'),
                addDueDate: document.getElementById('rrAddDueDate'),
                addSubmit: document.getElementById('rrAddSubmit'),
            };

            const state = { page: 1, perPage: 20, total: 0, hasMore: false, loading: false, timer: null };

            // Select2 init (employee)
            (function initSelect2Employee() {
                const $sel = $(els.employee);

                function templateOption(data) {
                    if (!data.id) return data.text;
                    const sub = $(data.element).data('sub') || '';
                    const name = data.text || '';
                    const ini = (name.trim().split(/\s+/).map(x => x[0]).slice(0,2).join('') || '?').toUpperCase();

                    return $(`
                        <div class="rr-emp-option">
                            <span class="rr-emp-dot">${ini}</span>
                            <span class="rr-emp-meta">
                                <span class="rr-emp-name">${escapeHtml(name)}</span>
                                <span class="rr-emp-sub">${escapeHtml(sub)}</span>
                            </span>
                        </div>
                    `);
                }

                function templateSelection(data) {
                    if (!data.id) return data.text;
                    const name = data.text || '';
                    const ini = (name.trim().split(/\s+/).map(x => x[0]).slice(0,2).join('') || '?').toUpperCase();
                    return $(`<span style="display:inline-flex;align-items:center;gap:10px;">
                                <span class="rr-emp-dot" style="width:26px;height:26px;font-size:11px;">${ini}</span>
                                <span>${escapeHtml(name)}</span>
                              </span>`);
                }

                $sel.select2({
                    placeholder: 'Alle',
                    allowClear: true,
                    width: '100%',
                    templateResult: templateOption,
                    templateSelection: templateSelection,
                    dropdownAutoWidth: false
                });

                $sel.on('change', () => debouncedReload());
            })();

            function escapeHtml(s) {
                return String(s ?? '')
                    .replaceAll('&','&amp;')
                    .replaceAll('<','&lt;')
                    .replaceAll('>','&gt;')
                    .replaceAll('"','&quot;')
                    .replaceAll("'","&#039;");
            }

            function initials(name) {
                const n = String(name || '').trim();
                if (!n) return '?';
                const p = n.split(/\s+/).filter(Boolean);
                const a = (p[0]?.[0] || '');
                const b = (p[1]?.[0] || '');
                return (a + b).toUpperCase() || a.toUpperCase() || '?';
            }

            function badgeMeta(type) {
                if (type === 'inquiry') return { cls: 'rr-badge-inquiry', icon: 'fa-envelope-open-text', label: 'Anfrage' };
                if (type === 'appointment') return { cls: 'rr-badge-appointment', icon: 'fa-calendar-day', label: 'Termin' };
                if (type === 'lead') return { cls: 'rr-badge-lead', icon: 'fa-star', label: 'Lead' };
                if (type === 'ticket') return { cls: 'rr-badge-ticket', icon: 'fa-life-ring', label: 'Ticket' };
                return { cls: 'rr-badge-task', icon: 'fa-tasks', label: 'Aufgabe' };
            }

            function renderExistingEmpty(msg) {
                els.existingReports.innerHTML = `<div class="rr-text-muted rr-small">${escapeHtml(msg)}</div>`;
            }

            async function loadSidebarReports(type, id) {
                if (!type || !id) return renderExistingEmpty('Keine Daten');

                renderExistingEmpty('Lade Berichte…');

                try {
                    const u = new URL(API.recordReports, window.location.origin);
                    u.searchParams.set('type', type);
                    u.searchParams.set('id', id);

                    const res = await fetch(u.toString(), { headers: { 'Accept': 'application/json' } });
                    const j = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(j?.message || 'Fehler');

                    const rows = Array.isArray(j.rows) ? j.rows : [];
                    if (!rows.length) return renderExistingEmpty('Keine Berichte vorhanden');

                    els.existingReports.innerHTML = rows.map(r => {
                        const who  = escapeHtml(r.employee_name || '—');
                        const when = escapeHtml(String(r.created_at || '').replace('T',' ').slice(0,16));
                        const txt  = escapeHtml(r.report_text || '—');
                        return `
                            <div class="rr-report-item">
                                <div class="rr-report-head">
                                    <div class="rr-report-who">${who}</div>
                                    <div class="rr-report-when">${when}</div>
                                </div>
                                <div class="rr-report-text">${txt}</div>
                            </div>
                        `;
                    }).join('');
                } catch (e) {
                    renderExistingEmpty('Fehler beim Laden');
                }
            }

            function openSidebar(row) {
                els.addType.value = row.type || '';
                els.addId.value = row.entity_id || '';
                els.addRef.textContent = row.ref_title || '...';
                els.addReport.value = '';
                els.addReportDate.valueAsDate = new Date();
                els.addDueDate.value = '';

                loadSidebarReports(row.type, row.entity_id);

                els.sidebar.classList.add('active');
                els.backdrop.classList.add('active');
            }

            function closeSidebar() {
                els.sidebar.classList.remove('active');
                els.backdrop.classList.remove('active');
            }

            els.closeBtn.addEventListener('click', closeSidebar);
            els.cancelBtn.addEventListener('click', closeSidebar);
            els.backdrop.addEventListener('click', closeSidebar);

            function buildFetchUrl() {
                const u = new URL(API.fetch, window.location.origin);

                const params = {
                    q: (els.q.value || '').trim(),
                    type: els.type.value || '',
                    employee_id: $(els.employee).val() || '',
                    from: els.from.value || '',
                    to: els.to.value || '',
                    sort: 'newest',
                    page: state.page,
                    per_page: state.perPage,
                };

                Object.entries(params).forEach(([k, v]) => {
                    if (v !== '' && v != null) u.searchParams.set(k, v);
                });

                return u.toString();
            }

            function renderEmpty(msg) {
                els.tbody.innerHTML = `<tr><td colspan="6" class="rr-text-center" style="padding: 2rem; color: var(--rr-text-muted); text-align:center;">${escapeHtml(msg)}</td></tr>`;
            }

            function render(rows, pagination) {
                const data = Array.isArray(rows) ? rows : [];
                state.total = pagination?.total || 0;
                state.hasMore = !!pagination?.has_more;

                const counts = { inquiry: 0, appointment: 0, lead: 0 };
                data.forEach(r => { if (counts[r.type] !== undefined) counts[r.type]++; });

                els.statTotal.textContent = String(state.total || 0);
                els.statInquiry.textContent = String(counts.inquiry || 0);
                els.statAppointment.textContent = String(counts.appointment || 0);
                els.statLead.textContent = String(counts.lead || 0);

                if (!data.length) {
                    renderEmpty('Keine Ergebnisse gefunden');
                    els.meta.textContent = '0 Einträge';
                    els.prev.disabled = state.page <= 1;
                    els.next.disabled = !state.hasMore;
                    return;
                }

                const start = ((state.page - 1) * state.perPage) + 1;
                const end = Math.min(state.page * state.perPage, state.total);
                els.meta.textContent = `Zeige ${start}-${end} von ${state.total} Einträgen`;

                els.prev.disabled = state.page <= 1;
                els.next.disabled = !state.hasMore;

                els.tbody.innerHTML = data.map(row => {
                    const b = badgeMeta(row.type);
                    const emp = row.employee_name || '—';
                    const av = initials(emp);
                    const created = String(row.created_at || '').replace('T', ' ').slice(0, 16);
                    const report = String(row.report_text || '');
                    const short = report.length > 160 ? (report.slice(0, 160) + '…') : report;

                    const canAdd = !!row.can_add_report;
                   const addBtn = `
                    <button class="rr-btn-icon rr-add-btn"
                            style="border-color: var(--rr-primary); color: var(--rr-primary);"
                            type="button"
                            data-type="${escapeHtml(row.type || '')}"
                            data-entity-id="${escapeHtml(row.entity_id || '')}"
                            data-ref-title="${escapeHtml(row.ref_title || '')}">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                    `;


                    return `
                        <tr>
                            <td style="padding-left: 2rem;">
                                <div class="rr-status-badge ${b.cls}">
                                    <i class="fa-solid ${b.icon}"></i> ${b.label}
                                </div>
                            </td>
                            <td>
                                <div class="rr-fw-bold" style="color: var(--rr-text-dark); margin-bottom: 2px;">${escapeHtml(row.ref_title || '—')}</div>
                                <div class="rr-small rr-text-muted" style="font-size: 0.75rem;">Bericht-ID: #${escapeHtml(row.report_id || '')}</div>
                            </td>
                            <td>
                                <div class="rr-user-cell">
                                    <div class="rr-avatar">${escapeHtml(av)}</div>
                                    <span class="rr-fw-medium rr-small">${escapeHtml(emp)}</span>
                                </div>
                            </td>
                            <td class="rr-text-muted rr-small">${escapeHtml(created || '—')}</td>
                            <td title="${escapeHtml(report)}">
                                <div class="rr-text-muted rr-small rr-text-truncate">${escapeHtml(short || '—')}</div>
                            </td>
                            <td class="rr-text-right" style="padding-right: 2rem;">
                                <a class="rr-btn-icon rr-me-2" href="${escapeHtml(row.link || '#')}" target="_blank" rel="noopener" title="Öffnen">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                ${addBtn}
                            </td>
                        </tr>
                    `;
                }).join('');

                document.querySelectorAll('.rr-add-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const raw = btn.getAttribute('data-row') || '{}';
                        let row = {};
                        try { row = JSON.parse(raw); } catch (e) {}
                        openSidebar(row);
                    });
                });
            }

            async function load() {
                if (state.loading) return;
                state.loading = true;

                renderEmpty('Lade Daten...');
                els.meta.textContent = 'Lade Daten...';

                try {
                    const res = await fetch(buildFetchUrl(), { headers: { 'Accept': 'application/json' } });
                    const j = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(j?.message || 'Fehler beim Laden');
                    render(j.rows, j.pagination);
                } catch (e) {
                    renderEmpty('Fehler beim Laden');
                    els.meta.textContent = '';
                } finally {
                    state.loading = false;
                }
            }

            function debouncedReload() {
                clearTimeout(state.timer);
                state.timer = setTimeout(() => {
                    state.page = 1;
                    load();
                }, 250);
            }

            els.q.addEventListener('input', debouncedReload);
            els.type.addEventListener('change', debouncedReload);
            els.from.addEventListener('change', debouncedReload);
            els.to.addEventListener('change', debouncedReload);

            els.reset.addEventListener('click', () => {
                els.q.value = '';
                els.type.value = '';
                $(els.employee).val(null).trigger('change');
                els.from.value = '';
                els.to.value = '';
                state.page = 1;
                load();
            });

            els.prev.addEventListener('click', () => {
                if (state.page <= 1) return;
                state.page--;
                load();
            });

            els.next.addEventListener('click', () => {
                if (!state.hasMore) return;
                state.page++;
                load();
            });

            els.addForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const fd = new FormData(els.addForm);

                els.addSubmit.disabled = true;
                const old = els.addSubmit.innerHTML;
                els.addSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin rr-me-2"></i>Speichere...';

                try {
                    const res = await fetch(API.store, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: fd
                    });

                    const j = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(j?.message || 'Speichern fehlgeschlagen');

                    // refresh sidebar list for same record
                    const t = els.addType.value;
                    const id = els.addId.value;
                    await loadSidebarReports(t, id);

                    // refresh table
                    await load();

                    // close
                    closeSidebar();
                } catch (err) {
                    alert(err?.message || 'Speichern fehlgeschlagen');
                } finally {
                    els.addSubmit.disabled = false;
                    els.addSubmit.innerHTML = old;
                }
            });

            // Initial load
            load();
        });
    </script>
@endsection
