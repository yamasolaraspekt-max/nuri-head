@extends('admin.layouts.app')

@section('title', 'Gesetzliche Feiertage')

@php
    $holidayStateCityMap = [
        'Baden-Württemberg' => [
            'Stuttgart',
            'Karlsruhe',
            'Mannheim',
            'Freiburg',
            'Heidelberg',
            'Ulm',
            'Reutlingen',
        ],
        'Bayern' => [
            'München',
            'Nürnberg',
            'Augsburg',
            'Regensburg',
            'Würzburg',
            'Ingolstadt',
            'Neu-Anspach',
        ],
        'Berlin' => [
            'Berlin',
        ],
        'Brandenburg' => [
            'Potsdam',
            'Cottbus',
            'Brandenburg an der Havel',
            'Frankfurt (Oder)',
        ],
        'Bremen' => [
            'Bremen',
            'Bremerhaven',
        ],
        'Hamburg' => [
            'Hamburg',
        ],
        'Hessen' => [
            'Allendorf (Lumda)',
            'Alsfeld',
            'Amöneburg',
            'Aßlar',
            'Babenhausen',
            'Bad Arolsen',
            'Bad Camberg',
            'Bad Hersfeld',
            'Bad Homburg vor der Höhe',
            'Bad Karlshafen',
            'Bad König',
            'Bad Nauheim',
            'Bad Orb',
            'Bad Schwalbach',
            'Bad Soden am Taunus',
            'Bad Soden-Salmünster',
            'Bad Sooden-Allendorf',
            'Bad Vilbel',
            'Bad Wildungen',
            'Battenberg (Eder)',
            'Baunatal',
            'Bebra',
            'Bensheim',
            'Biedenkopf',
            'Borken',
            'Braunfels',
            'Breuberg',
            'Bruchköbel',
            'Büdingen',
            'Bürstadt',
            'Butzbach',
            'Darmstadt',
            'Dieburg',
            'Diemelstadt',
            'Dietzenbach',
            'Dillenburg',
            'Dreieich',
            'Eltville am Rhein',
            'Eppstein',
            'Erbach',
            'Erlensee',
            'Eschborn',
            'Eschwege',
            'Felsberg',
            'Flörsheim am Main',
            'Florstadt',
            'Frankenau',
            'Frankenberg (Eder)',
            'Friedberg',
            'Friedrichsdorf',
            'Fritzlar',
            'Fulda',
            'Gelnhausen',
            'Giessen',
            'Griesheim',
            'Groß-Gerau',
            'Groß-Umstadt',
            'Groß-Zimmern',
            'Grünberg',
            'Gründau',
            'Gudensberg',
            'Guxhagen',
            'Habichtswald',
            'Hanau',
            'Hattersheim am Main',
            'Heppenheim',
            'Herborn',
            'Hofgeismar',
            'Hofheim am Taunus',
            'Homberg (Efze)',
            'Homberg (Ohm)',
            'Hünfeld',
            'Hungen',
            'Idstein',
            'Karben',
            'Kassel',
            'Kelkheim (Taunus)',
            'Korbach',
            'Langen',
            'Lauterbach',
            'Limburg an der Lahn',
            'Linden',
            'Lollar',
            'Maintal',
            'Marburg',
            'Michelstadt',
            'Mörfelden-Walldorf',
            'Mühlheim am Main',
            'Neu-Anspach',
            'Neu-Isenburg',
            'Nidderau',
            'Niedenstein',
            'Obertshausen',
            'Offenbach am Main',
            'Oberursel (Taunus)',
            'Pfungstadt',
            'Raunheim',
            'Reichelsheim',
            'Reinheim',
            'Riedstadt',
            'Rödermark',
            'Rodgau',
            'Rotenburg an der Fulda',
            'Rüsselsheim am Main',
            'Schlüchtern',
            'Schotten',
            'Seligenstadt',
            'Stadtallendorf',
            'Taunusstein',
            'Vellmar',
            'Viernheim',
            'Wächtersbach',
            'Wald-Michelbach',
            'Wetzlar',
            'Wiesbaden',
            'Wolfhagen',
            'Zwingenberg',
        ],
        'Mecklenburg-Vorpommern' => [
            'Schwerin',
            'Rostock',
            'Neubrandenburg',
            'Greifswald',
        ],
        'Niedersachsen' => [
            'Hannover',
            'Braunschweig',
            'Oldenburg',
            'Osnabrück',
        ],
        'Nordrhein-Westfalen' => [
            'Düsseldorf',
            'Köln',
            'Dortmund',
            'Essen',
            'Bonn',
            'Wuppertal',
        ],
        'Rheinland-Pfalz' => [
            'Mainz',
            'Ludwigshafen',
            'Koblenz',
            'Trier',
        ],
        'Saarland' => [
            'Saarbrücken',
            'Neunkirchen',
            'Homburg',
        ],
        'Sachsen' => [
            'Dresden',
            'Leipzig',
            'Chemnitz',
            'Zwickau',
        ],
        'Sachsen-Anhalt' => [
            'Magdeburg',
            'Halle',
            'Dessau-Roßlau',
        ],
        'Schleswig-Holstein' => [
            'Kiel',
            'Lübeck',
            'Flensburg',
            'Neumünster',
        ],
        'Thüringen' => [
            'Erfurt',
            'Jena',
            'Gera',
            'Weimar',
        ],
    ];

    $filterYears = range(((int) date('Y')) - 3, ((int) date('Y')) + 3);
@endphp

@section('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

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

        .oc-btn,
        .oc-btn-success,
        .oc-btn-danger,
        .oc-btn-blue,
        .oc-btn-soft {
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
            white-space: nowrap;
        }

        .oc-btn {
            background: var(--primary);
            color: #fff;
        }

        .oc-btn:hover {
            background: var(--primary-hover);
            color: #fff;
            text-decoration: none;
        }

        .oc-btn-success {
            background: var(--success);
            color: #fff;
        }

        .oc-btn-success:hover {
            background: #059669;
            color: #fff;
            text-decoration: none;
        }

        .oc-btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .oc-btn-danger:hover {
            background: #dc2626;
            color: #fff;
            text-decoration: none;
        }

        .oc-btn-blue {
            background: var(--blue);
            color: #fff;
        }

        .oc-btn-blue:hover {
            background: #559fc7;
            color: #fff;
            text-decoration: none;
        }

        .oc-btn-soft {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .oc-btn-soft:hover {
            background: #f9fafb;
            color: var(--text-main);
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
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-muted);
            text-decoration: none;
            flex: 0 0 auto;
        }

        .oc-btn-icon:hover {
            background: #f9fafb;
            color: #111827;
            text-decoration: none;
        }

        .oc-btn-icon.edit {
            background: var(--blue-light);
            color: #075985;
            border-color: #c0d8ea;
        }

        .oc-btn-icon.delete {
            background: var(--danger-light);
            color: var(--danger);
            border-color: #fecaca;
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

        .oc-stat-icon.year {
            background: var(--primary-light);
            color: #365314;
        }

        .oc-stat-icon.state {
            background: var(--warning-light);
            color: #b45309;
        }

        .oc-stat-icon.calendar {
            background: var(--success-light);
            color: #047857;
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

        .holiday-grid {
            display: grid;
            grid-template-columns: 420px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        @media(max-width:1300px) {
            .holiday-grid {
                grid-template-columns: 1fr;
            }
        }

        .holiday-list-card .oc-card-body {
            max-height: 690px;
            overflow: auto;
        }

        .holiday-filter {
            min-width: 180px;
        }

        .select2-container {
            width: 100% !important;
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            background: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            color: #111827;
            padding-left: 12px;
            padding-right: 30px;
            font-size: 13px;
            font-weight: 700;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 6px;
        }

        .holiday-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .holiday-item {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 14px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            box-shadow: var(--shadow-sm);
        }

        .holiday-item-title {
            font-size: 14px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 4px;
        }

        .holiday-item-meta {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .holiday-item-actions {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            flex: 0 0 auto;
        }

        .holiday-empty {
            border: 1px dashed var(--border);
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            color: var(--text-muted);
            font-weight: 800;
            background: #fafafa;
        }

        .calendar-shell {
            overflow: auto;
        }

        #holidayCalendar {
            min-width: 740px;
        }

        .fc {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .fc .fc-toolbar-title {
            font-size: 20px;
            font-weight: 900;
            color: #111827;
        }

        .fc .fc-button-primary {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
            border-radius: 9px !important;
            font-weight: 800 !important;
            box-shadow: none !important;
        }

        .fc .fc-button-primary:hover {
            background: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }

        .fc .fc-button-primary:disabled {
            background: #d1d5db !important;
            border-color: #d1d5db !important;
        }

        .fc-event {
            cursor: pointer;
        }

        .fc-event-card {
            background: var(--primary);
            color: white;
            border-radius: 8px;
            padding: 5px 7px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .14);
            font-size: 12px;
            line-height: 1.3;
            border: 0;
        }

        .fc-event-title {
            font-weight: 900;
            font-size: 12px;
            white-space: normal;
        }

        .fc-event-meta {
            font-size: 11px;
            opacity: .95;
            white-space: normal;
            margin-top: 2px;
        }

        .fc .fc-daygrid-event {
            border-radius: 8px;
            border: 0;
            padding: 0;
            background: transparent;
        }

        .oc-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        @media(max-width:700px) {
            .oc-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .oc-form-group.full {
            grid-column: 1 / -1;
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

        .oc-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9998;
            background: rgba(15, 23, 42, .56);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .oc-modal-backdrop.is-open {
            display: flex;
        }

        .oc-modal {
            width: 100%;
            max-width: 620px;
            max-height: calc(100vh - 36px);
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
            transform: translateY(10px) scale(.98);
            opacity: 0;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .oc-modal.sm {
            max-width: 520px;
        }

        .oc-modal.lg {
            max-width: 760px;
        }

        .oc-modal-backdrop.is-open .oc-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .oc-modal-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex: 0 0 auto;
        }

        .oc-modal-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111827;
        }

        .oc-modal-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 700;
        }

        .oc-modal-close {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            cursor: pointer;
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .oc-modal-close:hover {
            background: #f9fafb;
            color: #111827;
        }

        .oc-modal-body {
            padding: 18px;
            overflow: auto;
        }

        .oc-modal-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            flex: 0 0 auto;
        }

        .holiday-detail-box,
        .delete-warning {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 14px;
            padding: 14px;
            font-weight: 800;
            line-height: 1.55;
        }

        .holiday-detail-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px dashed #e5e7eb;
            font-size: 13px;
        }

        .holiday-detail-row:last-child {
            border-bottom: 0;
        }

        .holiday-detail-row span {
            color: var(--text-muted);
            font-weight: 800;
        }

        .holiday-detail-row strong {
            color: #111827;
            font-weight: 900;
            text-align: right;
        }

        .delete-warning {
            border-color: #fecaca;
            background: var(--danger-light);
            color: #991b1b;
        }

        .oc-toast-wrap {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 10020;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: min(360px, calc(100vw - 36px));
        }

        .oc-toast {
            background: #fff;
            border: 1px solid var(--border);
            border-left: 5px solid var(--success);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 13px 14px;
            font-size: 13px;
            font-weight: 800;
            color: #111827;
            transition: var(--transition);
        }

        .oc-toast.error {
            border-left-color: var(--danger);
        }

        .oc-toast.warning {
            border-left-color: var(--warning);
        }

        .oc-toast.info {
            border-left-color: var(--blue);
        }

        .select2-container--open {
            z-index: 10030;
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

            .fc .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc .fc-toolbar-title {
                font-size: 17px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">Gesetzliche Feiertage</div>
                    <div class="oc-sub">
                        Feiertage nach Jahr, Bundesland, Stadt und Land verwalten.
                    </div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                        <span>›</span>
                        <span class="current">Gesetzliche Feiertage</span>
                    </div>
                </div>

                <button type="button" class="oc-btn" id="openHolidayModal">
                    <i class="feather icon-plus"></i>
                    Neuer Feiertag
                </button>
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <i class="feather icon-calendar"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Feiertage</div>
                    <div class="oc-stat-value" id="statHolidayCount">0</div>
                    <div class="oc-stat-sub">Im aktuellen Filter</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon year">
                    <i class="feather icon-filter"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Jahr</div>
                    <div class="oc-stat-value" id="statYear">Alle</div>
                    <div class="oc-stat-sub">Aktueller Jahresfilter</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon state">
                    <i class="feather icon-map-pin"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Bundesländer</div>
                    <div class="oc-stat-value">16</div>
                    <div class="oc-stat-sub">Deutschland</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon calendar">
                    <i class="feather icon-grid"></i>
                </div>
                <div>
                    <div class="oc-stat-label">Ansicht</div>
                    <div class="oc-stat-value">Kalender</div>
                    <div class="oc-stat-sub">Jahr, Monat und Liste</div>
                </div>
            </div>
        </div>

        <div class="holiday-grid">
            <div class="oc-card holiday-list-card">
                <div class="oc-card-header">
                    <div>
                        <h3 class="oc-card-title">Feiertagsliste</h3>
                        <div class="oc-card-sub">Alle gespeicherten Feiertage</div>
                    </div>

                    <div class="holiday-filter">
                        <select id="filterYear" class="oc-select select2-main">
                            <option value="">Alle Jahre</option>
                            @foreach ($filterYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="oc-card-body">
                    <div class="holiday-list" id="holidayList"></div>
                </div>
            </div>

            <div class="oc-card">
                <div class="oc-card-header">
                    <div>
                        <h3 class="oc-card-title">Kalender</h3>
                        <div class="oc-card-sub">Jahres-, Monats- und Listenansicht</div>
                    </div>
                </div>

                <div class="oc-card-body">
                    <div class="calendar-shell">
                        <div id="holidayCalendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CREATE / EDIT MODAL --}}
    <div class="oc-modal-backdrop" id="holidayModal" aria-hidden="true">
        <div class="oc-modal" role="dialog" aria-modal="true" aria-labelledby="holidayModalTitle">
            <form id="holidayForm">
                @csrf
                <input type="hidden" name="holiday_id" id="holiday_id">

                <div class="oc-modal-header">
                    <div>
                        <h3 class="oc-modal-title" id="holidayModalTitle">Feiertag erstellen</h3>
                        <div class="oc-modal-sub">Feiertag mit Zeitraum und Ort speichern</div>
                    </div>

                    <button type="button" class="oc-modal-close js-close-modal" data-modal="holidayModal">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="oc-modal-body">
                    <div class="oc-form-grid">
                        <div class="oc-form-group full">
                            <label class="oc-label" for="name">Feiertagsname</label>
                            <input type="text" class="oc-input" id="name" name="name"
                                placeholder="z. B. Tag der Deutschen Einheit" required>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label" for="start_date">Von</label>
                            <input type="date" class="oc-input" id="start_date" name="start_date" required>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label" for="end_date">Bis</label>
                            <input type="date" class="oc-input" id="end_date" name="end_date" required>
                        </div>

                        <div class="oc-form-group full">
                            <label class="oc-label" for="state">Bundesland</label>
                            <select class="oc-select select2-modal" id="state" name="state" required>
                                <option></option>
                                @foreach (array_keys($holidayStateCityMap) as $state)
                                    <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label" for="city">Stadt</label>
                            <select class="oc-select select2-modal" id="city" name="city">
                                <option></option>
                            </select>
                        </div>

                        <div class="oc-form-group">
                            <label class="oc-label" for="country">Land</label>
                            <select class="oc-select select2-modal" id="country" name="country">
                                <option value="Deutschland" selected>Deutschland</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="oc-modal-footer">
                    <button type="button" class="oc-btn-soft js-close-modal" data-modal="holidayModal">
                        Abbrechen
                    </button>

                    <button class="oc-btn-success" type="submit">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DETAILS MODAL --}}
    <div class="oc-modal-backdrop" id="holidayDetailModal" aria-hidden="true">
        <div class="oc-modal sm" role="dialog" aria-modal="true" aria-labelledby="holidayDetailTitle">
            <div class="oc-modal-header">
                <div>
                    <h3 class="oc-modal-title" id="holidayDetailTitle">Feiertag</h3>
                    <div class="oc-modal-sub">Details zum Feiertag</div>
                </div>

                <button type="button" class="oc-modal-close js-close-modal" data-modal="holidayDetailModal">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="oc-modal-body">
                <div class="holiday-detail-box">
                    <div class="holiday-detail-row">
                        <span>Von</span>
                        <strong id="detailStart"></strong>
                    </div>

                    <div class="holiday-detail-row">
                        <span>Bis</span>
                        <strong id="detailEnd"></strong>
                    </div>

                    <div class="holiday-detail-row">
                        <span>Stadt</span>
                        <strong id="detailCity"></strong>
                    </div>

                    <div class="holiday-detail-row">
                        <span>Bundesland</span>
                        <strong id="detailState"></strong>
                    </div>

                    <div class="holiday-detail-row">
                        <span>Land</span>
                        <strong id="detailCountry"></strong>
                    </div>
                </div>
            </div>

            <div class="oc-modal-footer">
                <button type="button" class="oc-btn-soft js-close-modal" data-modal="holidayDetailModal">
                    Schließen
                </button>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div class="oc-modal-backdrop" id="deleteHolidayModal" aria-hidden="true">
        <div class="oc-modal sm" role="dialog" aria-modal="true" aria-labelledby="deleteHolidayTitle">
            <div class="oc-modal-header">
                <div>
                    <h3 class="oc-modal-title" id="deleteHolidayTitle">Feiertag löschen</h3>
                    <div class="oc-modal-sub">Der Feiertag wird dauerhaft entfernt</div>
                </div>

                <button type="button" class="oc-modal-close js-close-modal" data-modal="deleteHolidayModal">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="oc-modal-body">
                <div class="delete-warning">
                    Möchten Sie <strong id="deleteHolidayName"></strong> wirklich löschen?
                </div>
            </div>

            <div class="oc-modal-footer">
                <button type="button" class="oc-btn-soft js-close-modal" data-modal="deleteHolidayModal">
                    Abbrechen
                </button>

                <button type="button" class="oc-btn-danger" id="confirmDeleteHoliday">
                    <i class="feather icon-trash-2"></i>
                    Ja, löschen
                </button>
            </div>
        </div>
    </div>

    <div class="oc-toast-wrap" id="toastWrap"></div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        window.HolidayStateCityMap = @json($holidayStateCityMap);
    </script>

    <script>
        (function () {
            'use strict';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const routes = {
                fetch: @json(route('public-holidays.fetch')),
                store: @json(route('public-holidays.store')),
                showBase: @json(url('/public-holidays')),
                deleteBase: @json(url('/public-holidays/delete')),
            };

            let calendar = null;
            let holidayToDelete = null;

            function byId(id) {
                return document.getElementById(id);
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function refreshIcons() {
                if (window.feather) {
                    window.feather.replace();
                }
            }

            function toast(message, type = 'success') {
                const wrap = byId('toastWrap');
                if (!wrap) return;

                const item = document.createElement('div');
                item.className = `oc-toast ${type}`;
                item.textContent = message;

                wrap.appendChild(item);

                setTimeout(function () {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(8px)';

                    setTimeout(function () {
                        item.remove();
                    }, 220);
                }, 2600);
            }

            function openModal(id) {
                const modal = byId(id);
                if (!modal) return;

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';

                setTimeout(function () {
                    const firstInput = modal.querySelector('input:not([type="hidden"]), select, button');
                    if (firstInput) firstInput.focus();
                }, 80);
            }

            function closeModal(id) {
                const modal = byId(id);
                if (!modal) return;

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');

                if (!document.querySelector('.oc-modal-backdrop.is-open')) {
                    document.body.style.overflow = '';
                }
            }

            function closeAllModals() {
                document.querySelectorAll('.oc-modal-backdrop.is-open').forEach(function (modal) {
                    closeModal(modal.id);
                });
            }

            function resetHolidayForm() {
                const form = byId('holidayForm');
                if (!form) return;

                form.reset();

                byId('holiday_id').value = '';
                byId('holidayModalTitle').textContent = 'Feiertag erstellen';

                $('#state').val(null).trigger('change');
                $('#city').html('<option></option>').val(null).trigger('change');
                $('#country').val('Deutschland').trigger('change');
            }

            function updateCityOptions(state, selectedCity = null) {
                const cities = window.HolidayStateCityMap[state] || [];
                let options = '<option></option>';

                cities.forEach(function (city) {
                    options += `<option value="${escapeHtml(city)}">${escapeHtml(city)}</option>`;
                });

                $('#city').html(options);

                if (selectedCity) {
                    if (!cities.includes(selectedCity)) {
                        $('#city').append(`<option value="${escapeHtml(selectedCity)}">${escapeHtml(selectedCity)}</option>`);
                    }

                    $('#city').val(selectedCity).trigger('change');
                } else {
                    $('#city').val(null).trigger('change');
                }
            }

            function formatDate(value) {
                if (!value) return '-';

                const dateOnly = String(value).substring(0, 10);
                const parts = dateOnly.split('-');

                if (parts.length === 3) {
                    return `${parts[2]}.${parts[1]}.${parts[0]}`;
                }

                return value;
            }

            function initSelect2() {
                if (typeof $.fn.select2 !== 'function') return;

                $('.select2-main').select2({
                    placeholder: 'Bitte auswählen',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('body')
                });

                $('.select2-modal').select2({
                    placeholder: 'Bitte auswählen',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#holidayModal')
                });
            }

            function initCalendar() {
                const calendarEl = byId('holidayCalendar');

                if (!calendarEl || typeof FullCalendar === 'undefined') return;

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'multiMonthYear',
                    height: 'auto',
                    aspectRatio: 1.5,
                    locale: 'de',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,multiMonthYear,listYear'
                    },
                    buttonText: {
                        today: 'Heute',
                        month: 'Monat',
                        multiMonthYear: 'Jahr',
                        list: 'Liste'
                    },
                    views: {
                        multiMonthYear: {
                            type: 'multiMonth',
                            duration: { months: 12 },
                            titleFormat: { year: 'numeric' }
                        },
                        listYear: {
                            type: 'list',
                            duration: { years: 1 },
                            buttonText: 'Liste'
                        }
                    },
                    events: [],
                    eventContent: function (arg) {
                        return {
                            html: `
                                <div class="fc-event-card">
                                    <div class="fc-event-title">${escapeHtml(arg.event.title)}</div>
                                    <div class="fc-event-meta">
                                        ${escapeHtml(arg.event.extendedProps.city || '-')},
                                        ${escapeHtml(arg.event.extendedProps.state || '')}
                                    </div>
                                </div>
                            `
                        };
                    },
                    eventClick: function (info) {
                        openHolidayDetail(info.event);
                    },
                    eventDisplay: 'block',
                    eventColor: '#8fc73e',
                    eventTextColor: '#fff',
                    nowIndicator: true
                });

                calendar.render();
            }

            function openHolidayDetail(event) {
                byId('holidayDetailTitle').textContent = event.title || 'Feiertag';
                byId('detailStart').textContent = formatDate(event.startStr);
                byId('detailEnd').textContent = formatDate(event.endStr);
                byId('detailCity').textContent = event.extendedProps.city || '-';
                byId('detailState').textContent = event.extendedProps.state || '-';
                byId('detailCountry').textContent = event.extendedProps.country || '-';

                openModal('holidayDetailModal');
            }

            function renderCalendarEvents(data) {
                if (!calendar) return;

                calendar.removeAllEvents();

                data.forEach(function (item) {
                    calendar.addEvent({
                        id: item.id,
                        title: item.name,
                        start: item.start_date,
                        end: item.end_date,
                        extendedProps: {
                            city: item.city,
                            state: item.state,
                            country: item.country
                        }
                    });
                });
            }

            function renderHolidayList(data) {
                const list = byId('holidayList');
                if (!list) return;

                if (!data.length) {
                    list.innerHTML = `
                        <div class="holiday-empty">
                            <i class="feather icon-calendar"></i>
                            Keine Feiertage gefunden.
                        </div>
                    `;

                    refreshIcons();
                    return;
                }

                list.innerHTML = data.map(function (h) {
                    return `
                        <div class="holiday-item">
                            <div>
                                <div class="holiday-item-title">${escapeHtml(h.name)}</div>
                                <div class="holiday-item-meta">
                                    ${escapeHtml(formatDate(h.start_date))} – ${escapeHtml(formatDate(h.end_date))}<br>
                                    ${escapeHtml(h.city || '-')}, ${escapeHtml(h.state || '-')}, ${escapeHtml(h.country || '-')}
                                </div>
                            </div>

                            <div class="holiday-item-actions">
                                <button type="button" class="oc-btn-icon edit js-edit-holiday" data-id="${escapeHtml(h.id)}" title="Bearbeiten">
                                    <i class="feather icon-edit"></i>
                                </button>

                                <button
                                    type="button"
                                    class="oc-btn-icon delete js-delete-holiday"
                                    data-id="${escapeHtml(h.id)}"
                                    data-name="${escapeHtml(h.name)}"
                                    title="Löschen"
                                >
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');

                refreshIcons();
            }

            function updateStats(data, year) {
                byId('statHolidayCount').textContent = data.length;
                byId('statYear').textContent = year || 'Alle';
            }

            function fetchAndRender(year = '') {
                $.get(routes.fetch, { year: year })
                    .done(function (data) {
                        const rows = Array.isArray(data) ? data : [];

                        renderCalendarEvents(rows);
                        renderHolidayList(rows);
                        updateStats(rows, year);
                    })
                    .fail(function () {
                        toast('Feiertage konnten nicht geladen werden.', 'error');
                    });
            }

            function fillForm(data) {
                byId('holiday_id').value = data.id || '';
                byId('name').value = data.name || '';
                byId('start_date').value = data.start_date || '';
                byId('end_date').value = data.end_date || '';
                byId('holidayModalTitle').textContent = data.id ? 'Feiertag bearbeiten' : 'Feiertag erstellen';

                $('#state').val(data.state || null).trigger('change');
                updateCityOptions(data.state || '', data.city || null);
                $('#country').val(data.country || 'Deutschland').trigger('change');
            }

            function editHoliday(id) {
                $.get(`${routes.showBase}/${id}`)
                    .done(function (data) {
                        fillForm(data);
                        openModal('holidayModal');
                    })
                    .fail(function () {
                        toast('Feiertag konnte nicht geladen werden.', 'error');
                    });
            }

            function submitHolidayForm(event) {
                event.preventDefault();

                const form = byId('holidayForm');
                if (!form) return;

                $.post({
                    url: routes.store,
                    data: $(form).serialize()
                })
                    .done(function () {
                        closeModal('holidayModal');
                        resetHolidayForm();
                        fetchAndRender($('#filterYear').val() || '');
                        toast('Feiertag wurde gespeichert.');
                    })
                    .fail(function (xhr) {
                        if (xhr.status === 409 && xhr.responseJSON?.status === 'duplicate') {
                            toast(xhr.responseJSON.message || 'Dieser Feiertag existiert bereits.', 'warning');
                            return;
                        }

                        if (xhr.responseJSON?.message) {
                            toast(xhr.responseJSON.message, 'error');
                            return;
                        }

                        toast('Feiertag konnte nicht gespeichert werden.', 'error');
                    });
            }

            function openDeleteModal(id, name) {
                holidayToDelete = id;
                byId('deleteHolidayName').textContent = name || 'diesen Feiertag';
                openModal('deleteHolidayModal');
            }

            function deleteHoliday() {
                if (!holidayToDelete) return;

                $.ajax({
                    url: `${routes.deleteBase}/${holidayToDelete}`,
                    method: 'DELETE',
                    data: {
                        _token: csrfToken
                    }
                })
                    .done(function () {
                        closeModal('deleteHolidayModal');
                        fetchAndRender($('#filterYear').val() || '');
                        toast('Feiertag wurde gelöscht.');
                        holidayToDelete = null;
                    })
                    .fail(function () {
                        toast('Feiertag konnte nicht gelöscht werden.', 'error');
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                initSelect2();
                initCalendar();
                fetchAndRender();

                $('#state').on('change', function () {
                    updateCityOptions($(this).val() || '');
                });

                $('#filterYear').on('change', function () {
                    fetchAndRender($(this).val() || '');
                });

                byId('openHolidayModal')?.addEventListener('click', function () {
                    resetHolidayForm();
                    openModal('holidayModal');
                });

                byId('holidayForm')?.addEventListener('submit', submitHolidayForm);
                byId('confirmDeleteHoliday')?.addEventListener('click', deleteHoliday);

                document.addEventListener('click', function (event) {
                    const closeBtn = event.target.closest('.js-close-modal');

                    if (closeBtn) {
                        closeModal(closeBtn.dataset.modal);
                        return;
                    }

                    if (event.target.classList.contains('oc-modal-backdrop')) {
                        closeModal(event.target.id);
                        return;
                    }

                    const editBtn = event.target.closest('.js-edit-holiday');

                    if (editBtn) {
                        editHoliday(editBtn.dataset.id);
                        return;
                    }

                    const deleteBtn = event.target.closest('.js-delete-holiday');

                    if (deleteBtn) {
                        openDeleteModal(deleteBtn.dataset.id, deleteBtn.dataset.name);
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeAllModals();
                    }
                });

                refreshIcons();
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
                label: 'Gesetzliche Feiertage',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush