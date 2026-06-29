@extends('admin.layouts.app')

@section('title', 'KUNDEN UND OBJEKTDATEN')

@php
    $imagePath = 'images/employee/';
    $male = 'images/gender/male.png';
    $female = 'images/gender/female.png';

    $servicesCount = is_countable($services ?? []) ? count($services) : collect($services ?? [])->count();
    $productsCount = is_countable($products ?? []) ? count($products) : collect($products ?? [])->count();
    $departmentsCount = is_countable($departments ?? []) ? count($departments) : collect($departments ?? [])->count();
    $branchesCount = is_countable($branch ?? []) ? count($branch) : collect($branch ?? [])->count();

$googleMapsKey = config('services.google.maps_key', '');
@endphp

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

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
            color: var(--primary);
            border-color: var(--primary-light);
            background: var(--primary-light);
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

        .oc-stat-icon.products {
            background: var(--success-light);
            color: var(--success);
        }

        .oc-stat-icon.services {
            background: var(--warning-light);
            color: #d97706;
        }

        .oc-stat-icon.department {
            background: var(--gray-light);
            color: var(--gray);
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

        .oc-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 520px;
            gap: 18px;
            align-items: start;
        }

        @media(max-width:1350px) {
            .oc-grid {
                grid-template-columns: 1fr;
            }
        }

        .oc-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        @media(max-width:900px) {
            .oc-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .oc-form-group {
            min-width: 0;
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

        .oc-input-form,
        .oc-select,
        .oc-textarea {
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

        .oc-textarea {
            min-height: 104px;
            resize: vertical;
        }

        .oc-input-form:focus,
        .oc-select:focus,
        .oc-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .oc-radio-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .oc-radio-card {
            position: relative;
            flex: 1 1 160px;
            min-height: 48px;
        }

        .oc-radio-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .oc-radio-card label {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            font-weight: 900;
            color: #374151;
            transition: var(--transition);
        }

        .oc-radio-card input:checked+label {
            background: var(--primary-light);
            border-color: var(--primary);
            color: #365314;
            box-shadow: 0 0 0 3px rgba(143, 199, 62, .12);
        }

        .oc-checkline {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #f9fafb;
            font-size: 13px;
            font-weight: 800;
            color: #374151;
            cursor: pointer;
        }

        .oc-checkline input {
            width: 17px;
            height: 17px;
            accent-color: var(--primary);
        }

        .oc-map-box {
            height: 440px;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: #e5e7eb;
            position: relative;
        }

        #gmp-map {
            width: 100% !important;
            height: 100% !important;
        }

        .oc-map-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .oc-map-hint {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.45;
            max-width: 380px;
        }

        #screenshot-preview img {
            width: 100%;
            max-height: 240px;
            object-fit: cover;
            border: 1px solid var(--border);
            border-radius: 14px;
            margin-top: 12px;
            background: #fff;
        }

        .oc-table-wrap {
            overflow-x: auto;
        }

        .oc-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            min-width: 1100px;
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

        .oc-table tbody tr {
            background: #fff;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .oc-table tbody td {
            padding: 10px;
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

        .oc-savebar {
            position: sticky;
            bottom: 16px;
            z-index: 80;
            display: flex;
            justify-content: flex-end;
            pointer-events: none;
            margin-top: 18px;
        }

        .oc-savebar-inner {
            pointer-events: auto;
            background: rgba(255, 255, 255, .92);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
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

            .oc-map-box {
                height: 360px;
            }

            .oc-savebar {
                bottom: 8px;
            }

            .oc-savebar-inner {
                width: 100%;
            }

            .oc-savebar-inner .oc-btn {
                width: 100%;
            }
        }

        .pac-container {
            z-index: 999999 !important;
        }

        .pac-container.oc-pac-force-hidden {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="oc-wrap">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="form form-horizontal" method="POST" id="customer_form" action="{{ route('new.lead.store') }}"
            enctype="multipart/form-data">
            @csrf

            <input type="hidden" value="{{ old('id', $inquiry['id'] ?? 'normal') }}" name="from">

            <div class="oc-header">
                <div class="oc-titlebar">
                    <div>
                        <div class="oc-title">Kunden und Objektdaten</div>
                        <div class="oc-sub">
                            Neuen Kunden anlegen, Objektadresse prüfen, Karte erfassen und Produkt-/Dienstleistungsdaten
                            vorbereiten.
                        </div>

                        <div class="oc-breadcrumb">
                            <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                            <span>›</span>
                            <a href="{{ url('/new_lead_view') }}">Kundeliste</a>
                            <span>›</span>
                            <span class="current">Kunde anlegen</span>
                        </div>
                    </div>

                    <button type="submit" class="oc-btn">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </div>

            <div class="oc-analytics">
                <div class="oc-stat">
                    <div class="oc-stat-icon total">
                        <i class="feather icon-user-plus"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Modus</div>
                        <div class="oc-stat-value">Neu</div>
                        <div class="oc-stat-sub">Kunde anlegen</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon products">
                        <i class="feather icon-package"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Produkte</div>
                        <div class="oc-stat-value">{{ $productsCount }}</div>
                        <div class="oc-stat-sub">Verfügbar</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon services">
                        <i class="feather icon-layers"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Services</div>
                        <div class="oc-stat-value">{{ $servicesCount }}</div>
                        <div class="oc-stat-sub">Dienstleistungen</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon department">
                        <i class="feather icon-briefcase"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Abteilungen</div>
                        <div class="oc-stat-value">{{ $departmentsCount }}</div>
                        <div class="oc-stat-sub">Zuordnung</div>
                    </div>
                </div>
            </div>

            <div class="oc-grid">
                <div class="oc-card">
                    <div class="oc-card-header">
                        <div>
                            <h3 class="oc-card-title">Kundendaten</h3>
                            <div class="oc-card-sub">Basisdaten, Kontakt und Herkunft</div>
                        </div>
                    </div>

                    <div class="oc-card-body">
                        <div class="oc-form-grid">
                            <div class="oc-form-group full">
                                <label class="oc-label">Kundentyp</label>

                                <div class="oc-radio-group">
                                    <div class="oc-radio-card">
                                        <input type="radio" class="form-element" name="customer_type" id="customer_type1"
                                            value="privat" checked>
                                        <label for="customer_type1">
                                            <i class="feather icon-user"></i>
                                            Privat
                                        </label>
                                    </div>

                                    <div class="oc-radio-card">
                                        <input type="radio" class="form-element" name="customer_type" id="customer_type2"
                                            value="Gewerbe">
                                        <label for="customer_type2">
                                            <i class="feather icon-briefcase"></i>
                                            Gewerbe
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="oc-form-group full" id="firma-container">
                                <label class="oc-label">Firma</label>
                                <input type="text" id="firma" class="oc-input-form form-element"
                                    value="{{ old('firma', $inquiry['firma'] ?? '') }}" name="firma">
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Anrede</label>
                                <select class="oc-select select2-tags" name="title"
                                    data-placeholder="Anrede auswählen oder eingeben">
                                    <option></option>
                                    <option value="Frau">Frau</option>
                                    <option value="Herr">Herr</option>
                                    <option value="An die">An die</option>
                                    <option value="An den">An den</option>
                                </select>
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Akademischer Titel</label>
                                <select class="oc-select select2-tags" name="academic_title"
                                    data-placeholder="Titel auswählen oder eingeben">
                                    <option></option>
                                    <option value="Dr.">Dr.</option>
                                    <option value="Prof.">Prof.</option>
                                    <option value="Prof. Dr.">Prof. Dr.</option>
                                    <option value="Dipl.-Ing.">Dipl.-Ing.</option>
                                    <option value="Mag.">Mag.</option>
                                </select>
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Vorname</label>
                                <input type="text" id="name" class="oc-input-form form-element"
                                    value="{{ old('name', $inquiry['name'] ?? '') }}" placeholder="Vorname" name="name"
                                    autocomplete="off" list="name-options">
                                <datalist id="name-options"></datalist>
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Nachname</label>
                                <input type="text" id="lastname" class="oc-input-form form-element" placeholder="Nachname"
                                    value="{{ old('lastname', $inquiry['lastname'] ?? '') }}" name="lastname"
                                    autocomplete="off" list="lastname-options">
                                <datalist id="lastname-options"></datalist>
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Festnetz</label>
                                <input type="text" class="oc-input-form form-element"
                                    value="{{ old('telephone', $inquiry['telephone'] ?? '') }}" id="telephone-input"
                                    placeholder="Festnetz" name="telephone">
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Handy</label>
                                <input type="text" class="oc-input-form form-element"
                                    value="{{ old('phone', $inquiry['phone'] ?? '') }}" name="phone" placeholder="Handy"
                                    id="phone-input">
                            </div>

                            <div class="oc-form-group full">
                                <label class="oc-label">E-Mail</label>
                                <input type="email" class="oc-input-form form-element" id="email-input"
                                    value="{{ old('email', $inquiry['email'] ?? '') }}" name="email">
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Objektname</label>
                                <input type="text" class="oc-input-form form-element" id="object_name"
                                    value="{{ old('object_name', 'Privathaus') }}" name="object_name">
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Priorität</label>
                                <select name="periority" class="oc-select form-element">
                                    <option value="Normal" @selected(isset($inquiry->periority) && $inquiry->periority == 'Normal')>Normal</option>
                                    <option value="Dringend" @selected(isset($inquiry->periority) && $inquiry->periority == 'Dringend')>Dringend</option>
                                    <option value="Sehr dringend" @selected(isset($inquiry->periority) && $inquiry->periority == 'Sehr dringend')>Sehr dringend</option>
                                </select>
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Betrieb</label>
                                <select name="branch_id" class="oc-select form-element" style="width:100%">
                                    @foreach ($branch as $br)
                                        <option value="{{ $br->id }}" @selected(isset($inquiry->branch_id) && $inquiry->branch_id == $br->id)>
                                            {{ $br->branch }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Anfragedatum</label>
                                <input type="date" class="oc-input-form form-element" name="request_date"
                                    value="{{ now()->format('Y-m-d') }}">
                                <input type="hidden" name="contact_person" class="form-element"
                                    value="{{ auth()->user()->name }}">
                            </div>

                            <div class="oc-form-group full">
                                <label class="oc-label">Quelle</label>
                                <select name="source" id="source" class="oc-select form-element" style="width:100%">
                                    <option></option>
                                    <option value="Telefonisch">Telefonisch</option>
                                    <option value="Persönlich">Persönlich</option>
                                    <option value="Mail">Mail</option>
                                    <option value="Nachbar">Nachbar</option>
                                    <option value="Empfehlung">Empfehlung</option>
                                    <option value="Solarrechner">Solarrechner</option>
                                    <option value="Herstellerlead">Herstellerlead</option>
                                    <option value="Event">Event</option>
                                    <option value="Messe">Messe</option>
                                    <option value="Hausmesse">Hausmesse</option>
                                    <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit</option>
                                    <option value="Messe/Veranstaltung">Messe/Veranstaltung</option>
                                </select>
                            </div>

                            <div class="oc-form-group full">
                                <label class="oc-label">Kundenadresse</label>

                                <input id="full_address" type="text" class="oc-input-form form-element"
                                    placeholder="Adresse eingeben und aus Google auswählen" name="full_address"
                                    autocomplete="off" value="{{ old('full_address', $inquiry['full_address'] ?? '') }}">

                                <input type="hidden" id="latitude-input" name="latitude"
                                    value="{{ old('latitude', $inquiry['latitude'] ?? '') }}">
                                <input type="hidden" id="longitude-input" name="longitude"
                                    value="{{ old('longitude', $inquiry['longitude'] ?? '') }}">
                                <input type="hidden" id="elevation-input" name="elevation"
                                    value="{{ old('elevation', $inquiry['elevation'] ?? '') }}">
                                <input type="hidden" id="postal_code-input" name="postcode"
                                    value="{{ old('postcode', $inquiry['postcode'] ?? '') }}">
                                <input type="hidden" id="street-input" name="street"
                                    value="{{ old('street', $inquiry['street'] ?? '') }}">
                                <input type="hidden" id="street-name-input" name="street_name"
                                    value="{{ old('street_name', $inquiry['street_name'] ?? '') }}">
                                <input type="hidden" id="street-number-input" name="street_number"
                                    value="{{ old('street_number', $inquiry['street_number'] ?? '') }}">
                                <input type="hidden" id="city-input" name="city"
                                    value="{{ old('city', $inquiry['city'] ?? '') }}">
                                <input type="hidden" id="state-input" name="state"
                                    value="{{ old('state', $inquiry['state'] ?? '') }}">
                                <input type="hidden" id="country-input" name="country"
                                    value="{{ old('country', $inquiry['country'] ?? '') }}">
                            </div>

                            <div class="oc-form-group full">
                                <label class="oc-checkline" for="alternative_address">
                                    <input type="checkbox" name="alternative_address" id="alternative_address" value="true"
                                        checked>
                                    Das Bauvorhaben hat die gleiche Adresse
                                </label>
                            </div>

                            <div class="oc-form-group full" id="street2s">
                                <label class="oc-label">Alternative Objektadresse</label>

                                <input id="full_address2" type="text" class="oc-input-form form-element"
                                    placeholder="Adresse eingeben" name="full_address2" value="{{ old('full_address2') }}">

                                <input type="hidden" id="street-input2" name="street2" value="{{ old('street2') }}">
                                <input type="hidden" id="street-name-input2" name="street_name2"
                                    value="{{ old('street_name2') }}">
                                <input type="hidden" id="street-number-input2" name="street_number2"
                                    value="{{ old('street_number2') }}">
                                <input type="hidden" id="state-input2" name="state2" value="{{ old('state2') }}">
                                <input type="hidden" id="country-input2" name="country2" value="{{ old('country2') }}">
                                <input type="hidden" id="latitude-input2" name="latitude2" value="{{ old('latitude2') }}">
                                <input type="hidden" id="longitude-input2" name="longitude2"
                                    value="{{ old('longitude2') }}">
                                <input type="hidden" id="elevation-input2" name="elevation2"
                                    value="{{ old('elevation2') }}">
                                <input type="hidden" id="postal_code-input2" name="postcode2"
                                    value="{{ old('postcode2') }}">
                                <input type="hidden" id="city-input2" name="city2" value="{{ old('city2') }}">
                            </div>

                            <div class="oc-form-group full">
                                <label class="oc-label">Notizen</label>
                                <textarea name="note"
                                    class="oc-textarea form-element">{{ old('note', $inquiry['note'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="oc-card">
                    <div class="oc-card-header">
                        <div>
                            <h3 class="oc-card-title">Objektbilder</h3>
                            <div class="oc-card-sub">Adresse auf Karte prüfen und Screenshot speichern</div>
                        </div>
                    </div>

                    <div class="oc-card-body">
                        <div class="oc-map-box">
                            <div id="gmp-map"></div>
                        </div>

                        <div class="oc-map-actions">
                            <div class="oc-map-hint">
                                Adresse auswählen oder Marker verschieben. Screenshot wird dem Formular als Datei
                                hinzugefügt.
                            </div>

                            <button type="button" class="oc-btn-soft" id="screenshot-btn">
                                <i class="feather icon-camera"></i>
                                Screenshot
                            </button>
                        </div>

                        <div id="screenshot-preview"></div>
                        <input type="file" class="d-none" id="screenshot-file-input" name="screenshot_file">
                    </div>
                </div>
            </div>

            <div class="oc-card">
                <div class="oc-card-header">
                    <div>
                        <h3 class="oc-card-title">Produkt & Dienstleistung</h3>
                        <div class="oc-card-sub">Produkte, Services, Abteilungen und zuständige Mitarbeiter verwalten</div>
                    </div>

                    <button type="button" class="oc-btn" id="addRow">
                        <i class="feather icon-plus"></i>
                        Zeile hinzufügen
                    </button>
                </div>

                <div class="oc-card-body">
                    <div class="oc-table-wrap">
                        <table class="oc-table" id="inquiryProductTable">
                            <thead>
                                <tr>
                                    <th>Produkt</th>
                                    <th>Dienstleistung</th>
                                    <th>Abteilung</th>
                                    <th>Innendienst</th>
                                    <th>Außendienst</th>
                                    <th>Interesse</th>
                                    <th>Realisierungszeit</th>
                                    <th style="text-align:center;">Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- JS appends rows --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="oc-savebar">
                <div class="oc-savebar-inner">
                    <div id="status-icon"></div>

                    <button type="submit" class="oc-btn">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        "use strict";

        /* ============================================================
        |  HELPERS
        ============================================================ */
        function byId(id) {
            return document.getElementById(id);
        }

        function safeValue(id) {
            return byId(id)?.value?.trim() || '';
        }

        function setValueIfExists(id, value, triggerEvents = true) {
            const el = byId(id);
            if (!el) return;

            el.value = value || '';

            if (triggerEvents) {
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function notifySwal(options) {
            if (typeof Swal !== 'undefined') {
                return Swal.fire(options);
            }

            alert(options?.text || options?.title || 'Hinweis');
            return Promise.resolve({ isConfirmed: true });
        }

        function safeSelect2($el, options = {}) {
            if (!$el || !$el.length || typeof $.fn.select2 !== 'function') return;

            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            $el.select2(options);
        }

        function debounce(fn, delay = 400) {
            let timer = null;

            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        /* ============================================================
        |  CONFIG
        ============================================================ */
        const LEAD_CONFIG = {
            stage: 'lead',
            csrfToken: @json(csrf_token()),
            employeeImageBase: @json(asset('images/employee')),
            routeEmployees: @json(route('inquiry.department.employees')),
            routeCheckCustomer: @json(route('check.customer')),
            storeRoute: @json(route('new.lead.store')),
            googleMapsKey: @json($googleMapsKey),
            icons: {
                green: @json(asset('images/icons/ampel-gruen.svg')),
                yellow: @json(asset('images/icons/ampel-gelb.svg')),
                red: @json(asset('images/icons/ampel-rot.svg')),
            },
            data: {
                services: @json($services ?? []),
                products: @json($products ?? []),
                departments: @json($departments ?? []),
            }
        };

        /* ============================================================
        |  SELECT2 GENERAL
        ============================================================ */
        $(function () {
            safeSelect2($('#source'), {
                tags: true,
                placeholder: 'Quelle auswählen',
                allowClear: true,
                width: '100%'
            });

            $('.select2-tags').each(function () {
                safeSelect2($(this), {
                    tags: true,
                    allowClear: true,
                    width: '100%',
                    placeholder: function () {
                        return $(this).data('placeholder') || '';
                    }
                });
            });
        });

        /* ============================================================
        |  CUSTOMER TYPE
        ============================================================ */
        const CustomerType = (function () {
            function init() {
                document.querySelectorAll('input[name="customer_type"]').forEach(radio => {
                    radio.addEventListener('change', update);
                });

                update();
            }

            function update() {
                const firmaContainer = byId('firma-container');
                const selected = document.querySelector('input[name="customer_type"]:checked')?.value || 'privat';

                if (firmaContainer) {
                    firmaContainer.style.display = selected === 'privat' ? 'none' : 'block';
                }
            }

            return { init };
        })();

        /* ============================================================
        |  PRODUCT / SERVICE / EMPLOYEE TABLE
        |  ✅ This makes "Zeile hinzufügen" work
        ============================================================ */
        const InquiryProducts = (function () {
            let rowIndex = 0;

            const products = Array.isArray(LEAD_CONFIG.data.products) ? LEAD_CONFIG.data.products : [];
            const services = Array.isArray(LEAD_CONFIG.data.services) ? LEAD_CONFIG.data.services : [];
            const departments = Array.isArray(LEAD_CONFIG.data.departments) ? LEAD_CONFIG.data.departments : [];

            function init() {
                const tableBody = document.querySelector('#inquiryProductTable tbody');
                const addBtn = byId('addRow');

                if (!tableBody) {
                    console.error('Missing #inquiryProductTable tbody');
                    return;
                }

                if (addBtn) {
                    addBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        addRow();
                    });
                }

                $(document).on('click', '.removeRow', function () {
                    const $row = $(this).closest('tr');

                    $row.find('select').each(function () {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                    });

                    $row.fadeOut(120, function () {
                        $(this).remove();
                    });
                });

                if (!tableBody.querySelector('tr')) {
                    addRow();
                }
            }

            function addRow(item = {}) {
                rowIndex++;

                const idx = rowIndex;

                const productOptions = products.map(product => {
                    const label = product.article_group || product.product || product.name || product.title || ('Produkt #' + product.id);
                    const selected = String(product.id) === String(item.product_id || '') ? 'selected' : '';

                    return `
                                <option value="${escapeHtml(product.id)}" ${selected} data-img="${escapeHtml(product.image || '')}">
                                    ${escapeHtml(label)}
                                </option>
                            `;
                }).join('');

                const departmentOptions = departments.map(department => {
                    const label = department.department_name || department.name || department.department || ('Abteilung #' + department.id);
                    const selected = String(department.id) === String(item.department_id || '') ? 'selected' : '';

                    return `
                                <option value="${escapeHtml(department.id)}" ${selected}>
                                    ${escapeHtml(label)}
                                </option>
                            `;
                }).join('');

                const rowHtml = `
                            <tr data-index="${idx}">
                                <td>
                                    <select name="product_id[]" class="product-select form-element" data-index="${idx}" style="width:100%">
                                        <option value="">Produkt wählen</option>
                                        ${productOptions}
                                    </select>
                                </td>

                                <td>
                                    <select name="service_id[]" class="service-select form-element" data-index="${idx}" style="width:100%">
                                        <option value="">Dienstleistung wählen</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="department_id[]" class="department-select form-element" data-index="${idx}" style="width:100%">
                                        <option value="">Abteilung wählen</option>
                                        ${departmentOptions}
                                    </select>
                                </td>

                                <td>
                                    <select name="employee_id[]" class="employee-select form-element" data-index="${idx}" style="width:100%">
                                        <option value="">Innendienst wählen</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="field_employee[]" class="field-employee-select form-element" data-index="${idx}" style="width:100%">
                                        <option value="">Außendienst wählen</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="interest[]" class="interest-select form-element" data-index="${idx}" style="width:100%">
                                        <option value="intent" ${item.interest === 'intent' ? 'selected' : ''}>Kaufabsicht</option>
                                        <option value="interest" ${item.interest === 'interest' ? 'selected' : ''}>Kaufinteresse</option>
                                        <option value="option" ${item.interest === 'option' ? 'selected' : ''}>Kaufoption</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="realization_time[]" class="realization-select form-element" data-index="${idx}" style="width:100%">
                                        <option value="">Bitte auswählen</option>
                                        <option value="soon" ${item.realization_time === 'soon' ? 'selected' : ''}>Schnellstmöglich</option>
                                        <option value="3" ${String(item.realization_time || '') === '3' ? 'selected' : ''}>3 Monate</option>
                                        <option value="6" ${String(item.realization_time || '') === '6' ? 'selected' : ''}>6 Monate</option>
                                        <option value="other" ${item.realization_time === 'other' ? 'selected' : ''}>Sonstiges</option>
                                    </select>
                                </td>

                                <td style="text-align:center;">
                                    <button type="button" class="oc-btn-ic danger removeRow" title="Entfernen">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;

                $('#inquiryProductTable tbody').append(rowHtml);

                initRow(idx, item);

                if (window.feather) {
                    window.feather.replace();
                }
            }

            function initRow(idx, item = {}) {
                const $product = $(`.product-select[data-index="${idx}"]`);
                const $service = $(`.service-select[data-index="${idx}"]`);
                const $department = $(`.department-select[data-index="${idx}"]`);
                const $employee = $(`.employee-select[data-index="${idx}"]`);
                const $fieldEmployee = $(`.field-employee-select[data-index="${idx}"]`);
                const $interest = $(`.interest-select[data-index="${idx}"]`);
                const $realization = $(`.realization-select[data-index="${idx}"]`);

                safeSelect2($product, { width: '100%' });
                safeSelect2($service, { width: '100%' });
                safeSelect2($department, { width: '100%' });
                safeSelect2($interest, { width: '100%' });
                safeSelect2($realization, { width: '100%' });

                initEmployeeSelect($employee);
                initEmployeeSelect($fieldEmployee);

                $product.off('change.productRow').on('change.productRow', function () {
                    loadServices(idx);
                    loadEmployees(idx, { autofill: true });
                });

                $service.off('change.productRow').on('change.productRow', function () {
                    loadEmployees(idx, { autofill: false });
                });

                $department.off('change.productRow').on('change.productRow', function () {
                    loadEmployees(idx, { autofill: false });
                });

                if (item.product_id) {
                    loadServices(idx, item.service_id || null);
                    loadEmployees(idx, {
                        autofill: false,
                        presetEmployeeId: item.employee_id || null,
                        presetFieldEmployeeId: item.field_employee || null
                    });
                }
            }

            function loadServices(idx, selectedServiceId = null) {
                const $product = $(`.product-select[data-index="${idx}"]`);
                const $service = $(`.service-select[data-index="${idx}"]`);

                const productId = $product.val();

                $service.empty().append('<option value="">Dienstleistung wählen</option>');

                if (!productId) {
                    $service.val('').trigger('change.select2');
                    return;
                }

                const filtered = services.filter(service => {
                    return String(service.product_id) === String(productId);
                });

                filtered.forEach(service => {
                    const label = translateService(service.phase_section || service.name || service.title || service.service || '');
                    const selected = selectedServiceId && String(selectedServiceId) === String(service.id) ? 'selected' : '';

                    $service.append(`
                                <option value="${escapeHtml(service.id)}" ${selected}>
                                    ${escapeHtml(label)}
                                </option>
                            `);
                });

                if (selectedServiceId && $service.find(`option[value="${selectedServiceId}"]`).length) {
                    $service.val(String(selectedServiceId)).trigger('change.select2');
                } else if (filtered.length === 1) {
                    $service.val(String(filtered[0].id)).trigger('change.select2');
                } else {
                    $service.val('').trigger('change.select2');
                }
            }

            function loadEmployees(idx, options = {}) {
                const autofill = options.autofill === true;

                const $product = $(`.product-select[data-index="${idx}"]`);
                const $department = $(`.department-select[data-index="${idx}"]`);
                const $service = $(`.service-select[data-index="${idx}"]`);
                const $employee = $(`.employee-select[data-index="${idx}"]`);
                const $fieldEmployee = $(`.field-employee-select[data-index="${idx}"]`);

                const productId = $product.val();
                let departmentId = $department.val();
                let serviceId = $service.val();

                if (!productId) {
                    fillEmployeeSelect($employee, [], 'Innendienst wählen', null);
                    fillEmployeeSelect($fieldEmployee, [], 'Außendienst wählen', null);
                    return;
                }

                const previousEmployee = options.presetEmployeeId || $employee.val();
                const previousFieldEmployee = options.presetFieldEmployeeId || $fieldEmployee.val();

                $.ajax({
                    url: LEAD_CONFIG.routeEmployees,
                    method: 'POST',
                    data: {
                        _token: LEAD_CONFIG.csrfToken,
                        product_id: productId,
                        department_id: departmentId || null,
                        service_id: serviceId || null,
                        stage: LEAD_CONFIG.stage
                    }
                })
                    .done(function (response) {
                        let internalEmployees = [];
                        let externalEmployees = [];

                        if (Array.isArray(response)) {
                            internalEmployees = response;
                            externalEmployees = response;
                        } else if (response && typeof response === 'object') {
                            if (autofill && !departmentId && response.department_id) {
                                departmentId = response.department_id;
                                $department.val(String(departmentId)).trigger('change.select2');
                            }

                            if (autofill && !serviceId && response.service_id) {
                                serviceId = response.service_id;

                                if (!$service.find(`option[value="${serviceId}"]`).length) {
                                    loadServices(idx, serviceId);
                                } else {
                                    $service.val(String(serviceId)).trigger('change.select2');
                                }
                            }

                            internalEmployees = response.internal_employees || response.employees || [];
                            externalEmployees = response.external_employees || response.field_employees || response.employees || [];
                        }

                        fillEmployeeSelect($employee, internalEmployees, 'Innendienst wählen', previousEmployee);
                        fillEmployeeSelect($fieldEmployee, externalEmployees, 'Außendienst wählen', previousFieldEmployee);
                    })
                    .fail(function (xhr) {
                        console.error('Mitarbeiter konnten nicht geladen werden:', xhr.responseText || xhr);

                        fillEmployeeSelect($employee, [], 'Innendienst wählen', null);
                        fillEmployeeSelect($fieldEmployee, [], 'Außendienst wählen', null);

                        notifySwal({
                            icon: 'error',
                            title: 'Fehler',
                            text: 'Mitarbeiter konnten nicht geladen werden.'
                        });
                    });
            }

            function initEmployeeSelect($select) {
                safeSelect2($select, {
                    width: '100%',
                    templateResult: formatEmployee,
                    templateSelection: formatEmployeeSelection,
                    escapeMarkup: markup => markup
                });
            }

            function fillEmployeeSelect($select, employees, placeholder, selectedId = null) {
                $select.empty().append(`<option value="">${escapeHtml(placeholder)}</option>`);

                (employees || []).forEach(employee => {
                    const fullName = `${employee.name || ''} ${employee.lastname || ''}`.trim() || employee.full_name || ('Mitarbeiter #' + employee.id);
                    const positions = Array.isArray(employee.positions) ? employee.positions.join(', ') : (employee.positions || '');

                    $select.append(`
                                <option value="${escapeHtml(employee.id)}"
                                        data-img="${escapeHtml(employee.image || '')}"
                                        data-positions="${escapeHtml(positions)}">
                                    ${escapeHtml(fullName)}
                                </option>
                            `);
                });

                initEmployeeSelect($select);

                if (selectedId && $select.find(`option[value="${selectedId}"]`).length) {
                    $select.val(String(selectedId)).trigger('change.select2');
                } else {
                    $select.val('').trigger('change.select2');
                }
            }

            function formatEmployee(option) {
                if (!option.id) return option.text;

                const $option = $(option.element);
                const imageFile = $option.data('img');
                const positions = $option.data('positions') || '';
                const imageUrl = imageFile ? `${LEAD_CONFIG.employeeImageBase}/${imageFile}` : '';

                return `
                            <div style="display:flex;align-items:center;gap:8px;">
                                ${imageUrl
                        ? `<img src="${escapeHtml(imageUrl)}" style="width:34px;height:34px;object-fit:cover;border-radius:999px;border:1px solid #e5e7eb;">`
                        : `<div style="width:34px;height:34px;border-radius:999px;background:#e5e7eb;border:1px solid #d1d5db;"></div>`
                    }
                                <div style="min-width:0;">
                                    <div style="font-weight:800;font-size:13px;color:#111827;">${escapeHtml(option.text)}</div>
                                    <div style="font-size:11px;color:#6b7280;">${escapeHtml(positions)}</div>
                                </div>
                            </div>
                        `;
            }

            function formatEmployeeSelection(option) {
                return option.text || '';
            }

            function translateService(value) {
                if (!value) return '';

                const key = String(value).toLowerCase();

                const map = {
                    complete: 'Komplettlösung',
                    montage: 'Montage',
                    product: 'Produkt',
                    plan: 'Planung',
                    maintenance: 'Wartung',
                    repair: 'Reparatur',
                    emergency: 'Notdienst',
                    others: 'Sonstiges'
                };

                return map[key] || value;
            }

            return { init, addRow };
        })();

        /* ============================================================
        |  TRAFFIC LIGHT
        ============================================================ */
        window.TrafficLightUpdate = function () {
            const fullAddress = safeValue('full_address');
            const postcode = safeValue('postal_code-input');
            const city = safeValue('city-input');
            const telephone = safeValue('telephone-input');
            const phone = safeValue('phone-input');
            const email = safeValue('email-input');

            const statusIcon = byId('status-icon');
            if (!statusIcon) return;

            if (fullAddress && postcode && city && (telephone || phone) && email) {
                statusIcon.innerHTML = `<img src="${LEAD_CONFIG.icons.green}" alt="Qualifiziert" style="width:30px">`;
            } else if (fullAddress || postcode || city || telephone || phone || email) {
                statusIcon.innerHTML = `<img src="${LEAD_CONFIG.icons.yellow}" alt="Teilweise qualifiziert" style="width:30px">`;
            } else {
                statusIcon.innerHTML = `<img src="${LEAD_CONFIG.icons.red}" alt="Nicht qualifiziert" style="width:30px">`;
            }
        };

        /* ============================================================
        |  NAME / LASTNAME SUGGESTIONS
        ============================================================ */
        const LeadSuggestions = (function () {
            function init() {
                bindSuggestionInput('name', 'name-options', '/api/lead-name-suggestions');
                bindSuggestionInput('lastname', 'lastname-options', '/api/lead-lastname-suggestions');
            }

            function bindSuggestionInput(inputId, datalistId, url) {
                const input = byId(inputId);
                const datalist = byId(datalistId);

                if (!input || !datalist) return;

                input.addEventListener('input', debounce(function () {
                    const query = input.value.trim();

                    if (query.length < 2) {
                        datalist.innerHTML = '';
                        return;
                    }

                    fetch(`${url}?query=${encodeURIComponent(query)}`)
                        .then(response => response.ok ? response.json() : [])
                        .then(data => {
                            if (!Array.isArray(data)) {
                                datalist.innerHTML = '';
                                return;
                            }

                            datalist.innerHTML = data
                                .map(item => `<option value="${escapeHtml(item)}"></option>`)
                                .join('');
                        })
                        .catch(() => {
                            datalist.innerHTML = '';
                        });
                }, 300));
            }

            return { init };
        })();

        /* ============================================================
        |  GOOGLE MAPS
        |  Fixed: house number + postcode + city + alternative address
        |  Also: marker drag reverse-geocode, autocomplete closes, screenshot file
        ============================================================ */
        window.initMap = function () {
            const mapEl = byId('gmp-map');

            if (!mapEl || typeof google === 'undefined' || !google.maps) {
                console.error('Google Maps not ready or #gmp-map missing.');
                return;
            }

            const HOUSE_ZOOM = 20;
            const FALLBACK_ZOOM = 6;
            const fallbackCenter = { lat: 51.1657, lng: 10.4515 };

            const geocoder = new google.maps.Geocoder();
            const elevationService = new google.maps.ElevationService();

            const savedLat = parseFloat(safeValue('latitude-input'));
            const savedLng = parseFloat(safeValue('longitude-input'));
            const hasCoords = Number.isFinite(savedLat) && Number.isFinite(savedLng);
            const startCenter = hasCoords ? { lat: savedLat, lng: savedLng } : fallbackCenter;

            let map = new google.maps.Map(mapEl, {
                center: startCenter,
                zoom: hasCoords ? HOUSE_ZOOM : FALLBACK_ZOOM,
                mapTypeId: google.maps.MapTypeId.SATELLITE,
                streetViewControl: true,
                mapTypeControl: true,
                fullscreenControl: true,
                zoomControl: true,
                gestureHandling: 'greedy'
            });

            let streetView = map.getStreetView();
            let marker = new google.maps.Marker({
                map: map,
                position: startCenter,
                draggable: true,
                visible: hasCoords,
                title: 'Objektstandort'
            });

            bindAddressAutocomplete('full_address', '');
            bindAddressAutocomplete('full_address2', '2');

            marker.addListener('dragend', function () {
                const pos = marker.getPosition();
                if (!pos) return;

                const lat = pos.lat();
                const lng = pos.lng();

                writeLatLng('', lat, lng);
                getElevation(lat, lng, '');
                reverseGeocode(lat, lng, '');

                map.panTo({ lat, lng });
                map.setZoom(HOUSE_ZOOM);
            });

            byId('alternative_address')?.addEventListener('change', handleAlternativeAddress);
            byId('screenshot-btn')?.addEventListener('click', takeScreenshot);

            handleAlternativeAddress();

            if (!hasCoords && safeValue('full_address')) {
                geocodeTypedAddress(safeValue('full_address'), '');
            }

            setTimeout(function () {
                google.maps.event.trigger(map, 'resize');
                map.setCenter(startCenter);
                map.setZoom(hasCoords ? HOUSE_ZOOM : FALLBACK_ZOOM);
            }, 300);

            function bindAddressAutocomplete(inputId, suffix) {
                const input = byId(inputId);
                if (!input || !google.maps.places) return;

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    fields: ['address_components', 'formatted_address', 'geometry', 'name', 'place_id'],
                    types: ['address'],
                    componentRestrictions: { country: 'de' }
                });

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();

                    if (!place || !place.geometry || !place.geometry.location) {
                        geocodeTypedAddress(input.value, suffix);
                        closePlacesDropdown(input, true);
                        return;
                    }

                    applyGooglePlace(place, suffix);

                    // Google creates the dropdown outside the input.
                    // A delayed forced close prevents the selected suggestion list from staying open.
                    closePlacesDropdown(input, true);
                    setTimeout(function () {
                        closePlacesDropdown(input, true);
                    }, 100);
                });

                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        closePlacesDropdown(input);
                        geocodeTypedAddress(input.value, suffix);
                    }
                });

                input.addEventListener('blur', debounce(function () {
                    const rawAddress = input.value.trim();
                    if (!rawAddress) return;

                    const latInputId = suffix === '2' ? 'latitude-input2' : 'latitude-input';
                    const lastAddress = input.dataset.lastResolvedAddress || '';

                    if (!safeValue(latInputId) || rawAddress !== lastAddress) {
                        geocodeTypedAddress(rawAddress, suffix);
                    }
                }, 250));
            }

            function closePlacesDropdown(input, forceRemove = false) {
                if (input) {
                    input.blur();
                }

                if (document.activeElement && typeof document.activeElement.blur === 'function') {
                    document.activeElement.blur();
                }

                document.querySelectorAll('.pac-container').forEach(container => {
                    container.classList.add('oc-pac-force-hidden');
                    container.style.display = 'none';
                    container.style.opacity = '0';
                    container.style.pointerEvents = 'none';
                    container.style.visibility = 'hidden';
                });

                if (!forceRemove) {
                    return;
                }

                setTimeout(function () {
                    document.querySelectorAll('.pac-container').forEach(container => {
                        container.remove();
                    });
                }, 250);
            }

            function geocodeTypedAddress(address, suffix) {
                if (!address) return;

                geocoder.geocode({
                    address: address,
                    componentRestrictions: { country: 'DE' }
                }, function (results, status) {
                    if (status !== 'OK' || !results || !results.length) {
                        console.warn('Adresse konnte nicht gefunden werden:', status, address);
                        return;
                    }

                    applyGooglePlace(results[0], suffix);
                });
            }

            function reverseGeocode(lat, lng, suffix) {
                geocoder.geocode({
                    location: { lat: Number(lat), lng: Number(lng) }
                }, function (results, status) {
                    if (status !== 'OK' || !results || !results.length) return;
                    applyGooglePlace(results[0], suffix);
                });
            }

            function applyGooglePlace(place, suffix) {
                const location = place.geometry?.location;
                if (!location) return;

                const lat = location.lat();
                const lng = location.lng();
                const normalizedSuffix = suffix === '2' ? '2' : '';
                const fullAddressId = normalizedSuffix === '2' ? 'full_address2' : 'full_address';

                const originalFormatted = place.formatted_address || byId(fullAddressId)?.value || '';
                const parts = parseAddressComponents(place.address_components || [], originalFormatted);
                const street = [parts.streetName, parts.streetNumber].filter(Boolean).join(' ').trim();
                const fullAddress = buildDisplayAddress(originalFormatted, street, parts.postcode, parts.city, parts.country);

                setValueIfExists(fullAddressId, fullAddress);
                byId(fullAddressId)?.setAttribute('data-last-resolved-address', fullAddress);

                setValueIfExists(`street-input${normalizedSuffix}`, street || parts.streetName);
                setValueIfExists(`street-name-input${normalizedSuffix}`, parts.streetName);
                setValueIfExists(`street-number-input${normalizedSuffix}`, parts.streetNumber);
                setValueIfExists(`postal_code-input${normalizedSuffix}`, parts.postcode);
                setValueIfExists(`city-input${normalizedSuffix}`, parts.city);
                setValueIfExists(`state-input${normalizedSuffix}`, parts.state);
                setValueIfExists(`country-input${normalizedSuffix}`, parts.country);
                writeLatLng(normalizedSuffix, lat, lng);
                getElevation(lat, lng, normalizedSuffix);

                if (normalizedSuffix !== '2') {
                    marker.setPosition({ lat, lng });
                    marker.setVisible(true);
                    map.setCenter({ lat, lng });
                    map.setZoom(HOUSE_ZOOM);

                    if (byId('alternative_address')?.checked) {
                        copyMainAddressToAlternative();
                    }
                }

                if (typeof window.TrafficLightUpdate === 'function') {
                    window.TrafficLightUpdate();
                }
            }

            function parseAddressComponents(components, formattedAddress = '') {
                const result = {
                    streetName: '',
                    streetNumber: '',
                    postcode: '',
                    city: '',
                    state: '',
                    country: ''
                };

                components.forEach(component => {
                    const types = component.types || [];
                    const longName = component.long_name || '';
                    const shortName = component.short_name || longName;

                    if (types.includes('route')) result.streetName = longName;
                    if (types.includes('street_number')) result.streetNumber = longName;
                    if (types.includes('postal_code')) result.postcode = longName;
                    if (types.includes('postal_code_suffix') && result.postcode && !result.postcode.includes('-')) {
                        result.postcode += '-' + longName;
                    }

                    if (!result.city && types.includes('locality')) result.city = longName;
                    if (!result.city && types.includes('postal_town')) result.city = longName;
                    if (!result.city && types.includes('sublocality')) result.city = longName;
                    if (!result.city && types.includes('sublocality_level_1')) result.city = longName;
                    if (!result.city && types.includes('administrative_area_level_3')) result.city = longName;
                    if (!result.city && types.includes('administrative_area_level_2')) result.city = longName;

                    if (types.includes('administrative_area_level_1')) result.state = longName;
                    if (types.includes('country')) result.country = shortName;
                });

                const fallbackStreet = splitStreetFallback(formattedAddress);
                result.streetName = result.streetName || fallbackStreet.streetName;
                result.streetNumber = result.streetNumber || fallbackStreet.streetNumber;

                const fallbackPostcodeCity = splitPostcodeCityFallback(formattedAddress);
                result.postcode = result.postcode || fallbackPostcodeCity.postcode;
                result.city = result.city || fallbackPostcodeCity.city;

                return result;
            }

            function splitStreetFallback(formattedAddress) {
                const firstPart = String(formattedAddress || '').split(',')[0].trim();
                const match = firstPart.match(/^(.+?)\s+(\d+[a-zA-Z]?([\/-]\d+[a-zA-Z]?)?)$/);

                if (!match) {
                    return { streetName: firstPart, streetNumber: '' };
                }

                return { streetName: match[1].trim(), streetNumber: match[2].trim() };
            }

            function splitPostcodeCityFallback(formattedAddress) {
                const match = String(formattedAddress || '').match(/(?:^|,|\s)(\d{5})\s+([^,]+)/);

                if (!match) {
                    return { postcode: '', city: '' };
                }

                return {
                    postcode: match[1].trim(),
                    city: match[2].replace(/Deutschland|Germany/gi, '').trim()
                };
            }

            function buildDisplayAddress(originalFormatted, street, postcode, city, country) {
                const cleanCountry = country === 'DE' ? 'Deutschland' : country;
                const rebuilt = [street, [postcode, city].filter(Boolean).join(' '), cleanCountry]
                    .filter(Boolean)
                    .join(', ');

                return rebuilt || originalFormatted || '';
            }

            function writeLatLng(suffix, lat, lng) {
                setValueIfExists(`latitude-input${suffix}`, Number(lat).toFixed(8));
                setValueIfExists(`longitude-input${suffix}`, Number(lng).toFixed(8));
            }

            function getElevation(lat, lng, suffix) {
                elevationService.getElevationForLocations({
                    locations: [{ lat: Number(lat), lng: Number(lng) }]
                }, function (results, status) {
                    if (status === 'OK' && results && results[0]) {
                        setValueIfExists(`elevation-input${suffix}`, Number(results[0].elevation).toFixed(2));
                    }
                });
            }

            function handleAlternativeAddress() {
                const sameAddress = byId('alternative_address')?.checked;
                const altWrapper = byId('street2s');

                if (sameAddress) {
                    copyMainAddressToAlternative();
                    if (altWrapper) altWrapper.style.display = 'none';
                    return;
                }

                if (altWrapper) altWrapper.style.display = 'block';
            }

            function copyMainAddressToAlternative() {
                const ids = [
                    ['full_address', 'full_address2'],
                    ['street-input', 'street-input2'],
                    ['street-name-input', 'street-name-input2'],
                    ['street-number-input', 'street-number-input2'],
                    ['postal_code-input', 'postal_code-input2'],
                    ['city-input', 'city-input2'],
                    ['state-input', 'state-input2'],
                    ['country-input', 'country-input2'],
                    ['latitude-input', 'latitude-input2'],
                    ['longitude-input', 'longitude-input2'],
                    ['elevation-input', 'elevation-input2']
                ];

                ids.forEach(([from, to]) => setValueIfExists(to, safeValue(from)));
            }

            function takeScreenshot() {
                const btn = byId('screenshot-btn');

                if (!map) {
                    notifySwal({
                        icon: 'warning',
                        title: 'Karte nicht bereit',
                        text: 'Bitte warten Sie, bis die Karte geladen ist.'
                    });
                    return;
                }

                if (btn) {
                    btn.disabled = true;
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="feather icon-loader"></i> Screenshot wird erstellt...';
                    if (window.feather) window.feather.replace();
                }

                const useStreetView = streetView && streetView.getVisible();

                const request = useStreetView
                    ? createStreetViewScreenshot().catch(() => createMapScreenshot()).catch(() => createDomScreenshot())
                    : createMapScreenshot().catch(() => createDomScreenshot());

                request.finally(() => resetScreenshotButton(btn));
            }

            function createStreetViewScreenshot() {
                const panoId = streetView.getPano();
                const pov = streetView.getPov() || {};

                if (!panoId) return Promise.reject(new Error('Street View pano missing.'));

                const url = `https://maps.googleapis.com/maps/api/streetview?size=900x500&pano=${encodeURIComponent(panoId)}&heading=${encodeURIComponent(pov.heading || 0)}&pitch=${encodeURIComponent(pov.pitch || 0)}&fov=80&key=${encodeURIComponent(LEAD_CONFIG.googleMapsKey)}`;
                return fetchScreenshot(url, 'street_view_screenshot.jpg');
            }

            function createMapScreenshot() {
                const target = marker && marker.getVisible() ? marker.getPosition() : map.getCenter();
                if (!target) return Promise.reject(new Error('Map center missing.'));

                const lat = target.lat();
                const lng = target.lng();
                const zoom = map.getZoom() || HOUSE_ZOOM;
                const markerParam = `&markers=color:red%7C${encodeURIComponent(lat + ',' + lng)}`;
                const url = `https://maps.googleapis.com/maps/api/staticmap?center=${encodeURIComponent(lat + ',' + lng)}&zoom=${encodeURIComponent(zoom)}&size=900x500&scale=2&maptype=satellite${markerParam}&key=${encodeURIComponent(LEAD_CONFIG.googleMapsKey)}`;

                return fetchScreenshot(url, 'satellite_screenshot.png');
            }

            function fetchScreenshot(url, filename) {
                return fetch(url, { method: 'GET', mode: 'cors', cache: 'no-store' })
                    .then(response => {
                        if (!response.ok) throw new Error(`Screenshot request failed with status ${response.status}`);
                        return response.blob();
                    })
                    .then(blob => {
                        if (!blob || !String(blob.type || '').startsWith('image/')) {
                            throw new Error('Screenshot response is not an image.');
                        }

                        setScreenshotFile(blob, filename);
                    });
            }

            function createDomScreenshot() {
                if (typeof html2canvas !== 'function') {
                    showScreenshotError();
                    return Promise.reject(new Error('html2canvas missing.'));
                }

                return html2canvas(mapEl, {
                    useCORS: true,
                    allowTaint: false,
                    backgroundColor: null,
                    scale: 2,
                    logging: false
                }).then(canvas => {
                    return new Promise((resolve, reject) => {
                        canvas.toBlob(blob => {
                            if (!blob) {
                                reject(new Error('Canvas blob missing.'));
                                return;
                            }

                            setScreenshotFile(blob, 'map_screenshot.png');
                            resolve();
                        }, 'image/png', 0.95);
                    });
                }).catch(error => {
                    console.error(error);
                    showScreenshotError();
                    throw error;
                });
            }

            function setScreenshotFile(blob, filename) {
                const fileInput = byId('screenshot-file-input');
                const preview = byId('screenshot-preview');

                if (!fileInput || !preview) return;

                const file = new File([blob], filename, { type: blob.type || 'image/png' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                const image = document.createElement('img');
                image.src = URL.createObjectURL(blob);
                image.alt = filename;

                preview.innerHTML = '';
                preview.appendChild(image);

                notifySwal({
                    icon: 'success',
                    title: 'Screenshot erstellt',
                    text: 'Der Screenshot wurde dem Formular hinzugefügt.',
                    timer: 1300,
                    showConfirmButton: false
                });
            }

            function resetScreenshotButton(btn) {
                if (!btn) return;
                btn.disabled = false;
                btn.innerHTML = btn.dataset.originalHtml || '<i class="feather icon-camera"></i> Screenshot';
                if (window.feather) window.feather.replace();
            }

            function showScreenshotError() {
                notifySwal({
                    icon: 'error',
                    title: 'Screenshot Fehler',
                    text: 'Der Screenshot konnte nicht erstellt werden. Bitte prüfen, ob Static Maps API und Street View Static API in Google Cloud aktiviert sind und der API-Key diese APIs nutzen darf.'
                });
            }
        };


        /* ============================================================
        |  FORM SUBMIT
        |  Fixed: redirect must not depend on SweetAlert .then()
        |  Also handles normal Laravel redirects returned through AJAX.
        ============================================================ */
        $(function () {
            const FORCE_LIST_REDIRECT_URL = `{{ url('new_lead_view') }}`;

            function buildLeadListRedirect(response, xhr) {
                const leadId = response?.highlight_id || response?.lead_id || response?.id || null;

                if (response?.redirect) {
                    return response.redirect;
                }

                if (leadId) {
                    return `${FORCE_LIST_REDIRECT_URL}?highlight_id=${encodeURIComponent(leadId)}&created=1`;
                }

                if (xhr?.responseURL && String(xhr.responseURL).includes('new_lead_view')) {
                    return xhr.responseURL;
                }

                return FORCE_LIST_REDIRECT_URL;
            }

            function goToLeadList(response, xhr) {
                const redirectUrl = buildLeadListRedirect(response, xhr);

                window.location.assign(redirectUrl);
            }

            $('#customer_form').off('submit.leadForm').on('submit.leadForm', function (event) {
                event.preventDefault();

                const form = this;
                const $submitButtons = $(form).find('button[type="submit"]');

                $submitButtons.prop('disabled', true).addClass('disabled');

                $.ajax({
                    url: $(form).attr('action') || LEAD_CONFIG.storeRoute,
                    method: $(form).attr('method') || 'POST',
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': LEAD_CONFIG.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .done(function (response, textStatus, xhr) {
                        /*
                         * Case 1: Controller returns the correct JSON:
                         * { success:true, redirect:"/new_lead_view?highlight_id=123&created=1" }
                         */
                        if (response && typeof response === 'object' && response.success) {
                            notifySwal({
                                icon: 'success',
                                title: 'Gespeichert',
                                text: response.message || 'Der Kunde wurde erfolgreich gespeichert.',
                                timer: 650,
                                showConfirmButton: false
                            });

                            setTimeout(function () {
                                goToLeadList(response, xhr);
                            }, 250);

                            return;
                        }

                        /*
                         * Case 2: Laravel returned a normal redirect inside AJAX.
                         * Browser does not navigate automatically after AJAX redirects,
                         * so we manually use xhr.responseURL.
                         */
                        if (xhr?.responseURL && String(xhr.responseURL).includes('new_lead_view')) {
                            window.location.assign(xhr.responseURL);
                            return;
                        }

                        notifySwal({
                            icon: 'error',
                            title: 'Fehler',
                            text: response?.message || 'Der Kunde konnte nicht gespeichert werden. Bitte prüfen Sie die Pflichtfelder.'
                        });

                        $submitButtons.prop('disabled', false).removeClass('disabled');
                    })
                    .fail(function (xhr) {
                        const errors = xhr.responseJSON?.errors || null;

                        const message = errors
                            ? Object.values(errors).flat().join('<br>')
                            : (xhr.responseJSON?.message || 'Ein Fehler ist aufgetreten.');

                        notifySwal({
                            icon: 'error',
                            title: 'Fehler beim Speichern',
                            html: message
                        });

                        console.error(xhr.responseText || xhr);
                        $submitButtons.prop('disabled', false).removeClass('disabled');
                    });
            });
        });

        /* ============================================================
        |  INIT
        ============================================================ */
        document.addEventListener('DOMContentLoaded', function () {
            CustomerType.init();
            InquiryProducts.init();
            LeadSuggestions.init();

            document.addEventListener('input', window.TrafficLightUpdate);
            document.addEventListener('change', window.TrafficLightUpdate);
            window.TrafficLightUpdate();

            if (window.feather) {
                window.feather.replace();
            }
        });
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
        async defer></script>
@endsection

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Kundeliste',
                url: "{{ url('/new_lead_view') }}"
            },
            {
                label: 'Kunde anlegen',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush