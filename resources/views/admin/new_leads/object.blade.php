@extends('admin.layouts.app')

@section('title', 'KUNDEN UND OBJEKTDATEN')

@php
    $initialAddress = trim(
        collect([
            $leads->street ?? null,
            trim(($leads->postcode ?? '') . ' ' . ($leads->city ?? '')),
        ])->filter()->implode(', ')
    );

    $googleMapsKey = config('services.google.maps_key');

    $productsCount = is_countable($products ?? []) ? count($products) : collect($products ?? [])->count();
    $servicesCount = is_countable($services ?? []) ? count($services) : collect($services ?? [])->count();
    $departmentsCount = is_countable($departments ?? []) ? count($departments) : collect($departments ?? [])->count();
@endphp

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

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
            grid-template-columns: 420px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        @media(max-width:1200px) {
            .oc-grid {
                grid-template-columns: 1fr;
            }
        }

        .oc-info-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        @media(max-width:700px) {
            .oc-info-list {
                grid-template-columns: 1fr;
            }
        }

        .oc-info {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px;
            min-width: 0;
        }

        .oc-info-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 5px;
        }

        .oc-info-value {
            font-size: 13px;
            font-weight: 800;
            color: #111827;
            line-height: 1.45;
            word-break: break-word;
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

        .oc-address-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .oc-address-row .oc-input-form {
            flex: 1;
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
            width: 100%;
            height: 100%;
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

        .oc-toast-wrap {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .oc-toast {
            pointer-events: auto;
            min-width: 280px;
            max-width: 380px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 12px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            animation: ocToastIn .3s cubic-bezier(.175, .885, .32, 1.275) forwards;
        }

        @keyframes ocToastIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .oc-toast-ic {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .oc-toast-ic.ok {
            background: var(--success-light);
            color: var(--success);
        }

        .oc-toast-ic.bad {
            background: var(--danger-light);
            color: var(--danger);
        }

        .oc-toast-ic.warn {
            background: var(--warning-light);
            color: #d97706;
        }

        .oc-toast-ttl {
            font-weight: 900;
            font-size: 13px;
            margin: 0;
            color: #111827;
        }

        .oc-toast-msg {
            font-size: 12px;
            color: #374151;
            margin: 4px 0 0 0;
            line-height: 1.4;
        }

        .oc-toast-x {
            margin-left: auto;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 18px;
            line-height: 1;
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
    </style>
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="oc-wrap">
        <form class="form form-horizontal custom-file-upload" method="POST" id="customer_form"
            action="{{ route('store.object.leads') }}" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="lead_id" value="{{ $leads->id }}">
            <input type="hidden" name="alternative_id" value="{{ request()->alternative }}">
            <input id="input_name" type="hidden" name="name" value="{{ $leads->name }}">
            <input id="input_lastname" type="hidden" name="lastname" value="{{ $leads->lastname }}">

            <div class="oc-header">
                <div class="oc-titlebar">
                    <div>
                        <div class="oc-title">Kunden und Objektdaten</div>
                        <div class="oc-sub">
                            Neue Objektdaten erfassen, Adresse exakt über Google Maps setzen und Produkte/Dienstleistungen
                            zuordnen.
                        </div>

                        <div class="oc-breadcrumb">
                            <a href="{{ url('/employee_dashboard') }}">Home</a>
                            <span>›</span>
                            <span class="current">Objekt erstellen</span>
                        </div>
                    </div>

                    <button type="submit" class="oc-btn">
                        <i class="feather icon-arrow-right"></i>
                        Nächste
                    </button>
                </div>
            </div>

            <div class="oc-analytics">
                <div class="oc-stat">
                    <div class="oc-stat-icon total">
                        <i class="feather icon-user"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Kunde</div>
                        <div class="oc-stat-value">{{ $leads->customer_no ?: '—' }}</div>
                        <div class="oc-stat-sub">{{ $leads->customer_type ?: 'Kundendaten' }}</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon products">
                        <i class="feather icon-package"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Produkte</div>
                        <div class="oc-stat-value">{{ $productsCount }}</div>
                        <div class="oc-stat-sub">Verfügbare Produkte</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon services">
                        <i class="feather icon-layers"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Dienstleistungen</div>
                        <div class="oc-stat-value">{{ $servicesCount }}</div>
                        <div class="oc-stat-sub">Verfügbare Services</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon department">
                        <i class="feather icon-briefcase"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Abteilungen</div>
                        <div class="oc-stat-value">{{ $departmentsCount }}</div>
                        <div class="oc-stat-sub">Für Mitarbeiterzuordnung</div>
                    </div>
                </div>
            </div>

            <div class="oc-grid">
                <div class="oc-card">
                    <div class="oc-card-header">
                        <div>
                            <h3 class="oc-card-title">Kundendaten</h3>
                            <div class="oc-card-sub">Basisdaten und Kontaktinformationen</div>
                        </div>
                    </div>

                    <div class="oc-card-body">
                        @php
                            $user_name = DB::table('employees')
                                ->join('users', 'users.name', '=', 'employees.id')
                                ->select('employees.name', 'employees.lastname')
                                ->where('users.name', '=', $leads->contact_person)
                                ->first();
                        @endphp

                        <div class="oc-info-list">
                            <div class="oc-info">
                                <div class="oc-info-label">Kunde-Typ</div>
                                <div class="oc-info-value">{{ $leads->customer_type ?: '—' }}</div>
                            </div>

                            <div class="oc-info">
                                <div class="oc-info-label">Kunde-Nr.</div>
                                <div class="oc-info-value">{{ $leads->customer_no ?: '—' }}</div>
                            </div>

                            <div class="oc-info">
                                <div class="oc-info-label">Firma</div>
                                <div class="oc-info-value">{{ $leads->firma ?: '—' }}</div>
                            </div>

                            <div class="oc-info">
                                <div class="oc-info-label">Kunde</div>
                                <div class="oc-info-value">
                                    {{ trim(($leads->title ?? '') . ' ' . ($leads->name ?? '') . ' ' . ($leads->lastname ?? '')) ?: '—' }}
                                </div>
                            </div>

                            <div class="oc-info" style="grid-column:1 / -1;">
                                <div class="oc-info-label">Adresse</div>
                                <div class="oc-info-value">
                                    {{ trim(($leads->street ?? '') . ' ' . ($leads->postcode ?? '') . ', ' . ($leads->city ?? '')) ?: '—' }}
                                </div>
                            </div>

                            <div class="oc-info">
                                <div class="oc-info-label">Quelle</div>
                                <div class="oc-info-value">{{ $leads->source ?: '—' }}</div>
                            </div>

                            <div class="oc-info">
                                <div class="oc-info-label">Info</div>
                                <div class="oc-info-value">{{ $leads->info ?: '—' }}</div>
                            </div>

                            <div class="oc-info" style="grid-column:1 / -1;">
                                <div class="oc-info-label">Erste Kontaktperson</div>
                                <div class="oc-info-value">
                                    @if($user_name)
                                        {{ $user_name->name }} {{ $user_name->lastname }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>

                            <div class="oc-info" style="grid-column:1 / -1;">
                                <div class="oc-info-label">Kontakt</div>
                                <div class="oc-info-value">
                                    <div><i class="feather icon-phone-call"></i> {{ $leads->telephone ?: '—' }}</div>
                                    <div><i class="feather icon-smartphone"></i> {{ $leads->phone ?: '—' }}</div>
                                    <div><i class="feather icon-mail"></i> {{ $leads->email ?: '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="oc-card">
                    <div class="oc-card-header">
                        <div>
                            <h3 class="oc-card-title">Objektdaten</h3>
                            <div class="oc-card-sub">Adresse, Karte, Priorität und Notizen</div>
                        </div>
                    </div>

                    <div class="oc-card-body">
                        <div class="oc-form-grid">
                            <div class="oc-form-group">
                                <label class="oc-label">Anfrage-Datum</label>
                                <input type="date" class="oc-input-form" name="request_date"
                                    value="{{ old('request_date', \Carbon\Carbon::today()->toDateString()) }}">
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Objektname</label>
                                <input type="text" class="oc-input-form" name="object_name"
                                    value="{{ old('object_name', 'Privathaus') }}">
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Dringlichkeit</label>
                                <select name="periority" class="oc-select">
                                    <option value="Normal" @selected(old('periority', 'Normal') === 'Normal')>Normal</option>
                                    <option value="Dringend" @selected(old('periority') === 'Dringend')>Dringend</option>
                                    <option value="Sehr dringend" @selected(old('periority') === 'Sehr dringend')>Sehr
                                        dringend</option>
                                </select>
                            </div>

                            <div class="oc-form-group">
                                <label class="oc-label">Betrieb</label>
                                <select name="branch_id" class="oc-select">
                                    @foreach ($branch as $br)
                                        <option value="{{ $br->id }}" @selected(old('branch_id', $leads->branch ?? '') == $br->id)>
                                            {{ $br->branch }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="oc-form-group" style="grid-column:1 / -1;">
                                <label class="oc-label">STR. / NR. / PLZ. / ORT</label>

                                <div class="oc-address-row">
                                    <input id="full_address" type="text" class="oc-input-form"
                                        placeholder="Adresse eingeben und aus Google auswählen" name="full_address"
                                        autocomplete="off" value="{{ old('full_address', $initialAddress) }}">

                                    <button type="button" class="oc-btn-ic primary" id="show_map"
                                        title="Adresse auf Karte anzeigen">
                                        <i class="feather icon-map"></i>
                                    </button>
                                </div>

                                <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude') }}">
                                <input type="hidden" id="elevation-input" name="elevation" value="{{ old('elevation') }}">
                                <input type="hidden" id="postal_code-input" name="postcode" value="{{ old('postcode') }}">
                                <input type="hidden" id="locality-input" name="city" value="{{ old('city') }}">
                                <input type="hidden" id="street-input" name="street" value="{{ old('street') }}">
                                <input type="hidden" id="street-name-input" name="street_name"
                                    value="{{ old('street_name') }}">
                                <input type="hidden" id="street-number-input" name="street_number"
                                    value="{{ old('street_number') }}">
                            </div>

                            <div class="oc-form-group" style="grid-column:1 / -1;">
                                <label class="oc-label">Notizen</label>
                                <textarea name="info" rows="4" class="oc-textarea">{{ old('info', old('note')) }}</textarea>
                            </div>

                            <div class="oc-form-group" style="grid-column:1 / -1;">
                                <label class="oc-label">Karte</label>

                                <div class="oc-map-box">
                                    <div id="gmp-map" data-initial-address="{{ old('full_address', $initialAddress) }}"
                                        data-initial-lat="{{ old('latitude') }}" data-initial-lng="{{ old('longitude') }}">
                                    </div>
                                </div>

                                <div class="oc-map-actions">
                                    <div class="oc-map-hint">
                                        Adresse auswählen oder Marker verschieben. Die Karte zoomt automatisch auf das Haus.
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
                                    <th>Realisierungszeit</th>
                                    <th>Interesse</th>
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
                        <i class="feather icon-arrow-right"></i>
                        Speichern & Nächste
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>

    <script>
        window.ocToast = function (kind, title, msg) {
            const wrap = document.getElementById('toast-wrap');
            if (!wrap) return;

            const icons = {
                ok: `<i class="feather icon-check"></i>`,
                bad: `<i class="feather icon-x"></i>`,
                warn: `<i class="feather icon-alert-triangle"></i>`
            };

            const el = document.createElement('div');
            el.className = 'oc-toast';
            el.innerHTML = `
                        <div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
                        <div style="flex:1;">
                            <p class="oc-toast-ttl">${title || ''}</p>
                            <p class="oc-toast-msg">${msg || ''}</p>
                        </div>
                        <button class="oc-toast-x" type="button" onclick="this.parentElement.remove()">×</button>
                    `;

            wrap.appendChild(el);

            if (window.feather) {
                window.feather.replace();
            }

            setTimeout(() => {
                try { el.remove(); } catch (e) { }
            }, 4500);
        };

        window.ocEscape = function (value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        };

        @if(session('update_msg') || session('updated_msg'))
            ocToast('ok', 'Aktualisiert', @json(session('update_msg') ?? session('updated_msg')));
        @endif

        @if(session('save_msg'))
            ocToast('ok', 'Gespeichert', @json(session('save_msg')));
        @endif

        @if(session('delete_msg'))
            ocToast('bad', 'Gelöscht', @json(session('delete_msg')));
        @endif
    </script>

    <script>
        "use strict";

        const STAGE = 'lead';
        const IMG_EMPLOYEE = "{{ asset('images/employee') }}";
        const CSRF_TOKEN = @json(csrf_token());
        const ROUTE_EMPLOYEES = @json(route('inquiry.department.employees'));

        $(function () {
            if (typeof $.fn.select2 === 'undefined') {
                console.error('Select2 is not loaded.');
                ocToast('bad', 'Select2 fehlt', 'Bitte select2.min.js korrekt laden.');
                return;
            }

            let rowIndex = 0;

            const SERVICES = @json($services ?? []);
            const PRODUCTS = @json($products ?? []);
            const DEPARTMENTS = @json($departments ?? []);

            function translateService(s) {
                if (!s) return '';
                const key = String(s).toLowerCase();
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
                return map[key] || s;
            }

            function formatEmployee(opt) {
                if (!opt.id) return opt.text;

                const $el = $(opt.element);
                const imgFile = $el.data('img');
                const img = imgFile ? `${IMG_EMPLOYEE}/${imgFile}` : '';
                const pos = $el.data('positions') || '';

                return `
                        <div style="display:flex;align-items:center;gap:9px;">
                            ${img
                        ? `<img src="${ocEscape(img)}" style="width:34px;height:34px;object-fit:cover;border-radius:999px;border:1px solid #e5e7eb;">`
                        : `<div style="width:34px;height:34px;border-radius:999px;background:#e5e7eb;border:1px solid #d1d5db;"></div>`
                    }
                            <div style="min-width:0;">
                                <div style="font-weight:800;font-size:13px;color:#111827;">${ocEscape(opt.text)}</div>
                                <div style="font-size:11px;color:#6b7280;">${ocEscape(pos)}</div>
                            </div>
                        </div>
                    `;
            }

            function formatEmployeeSelection(opt) {
                return opt && opt.text ? opt.text : '';
            }

            function addRow() {
                rowIndex++;
                const idx = rowIndex;

                const productOptions = PRODUCTS.map(p => `
                        <option value="${ocEscape(p.id)}" data-img="${ocEscape(p.image || '')}">
                            ${ocEscape(p.article_group)}
                        </option>
                    `).join('');

                const departmentOptions = DEPARTMENTS.map(d => `
                        <option value="${ocEscape(d.id)}">
                            ${ocEscape(d.department_name)}
                        </option>
                    `).join('');

                const html = `
                        <tr data-index="${idx}">
                            <td>
                                <select name="product_id[]" class="product-select" data-index="${idx}">
                                    <option value="">Produkt wählen</option>
                                    ${productOptions}
                                </select>
                            </td>

                            <td>
                                <select name="service_id[]" class="service-select" data-index="${idx}">
                                    <option value="">Dienstleistung wählen</option>
                                </select>
                            </td>

                            <td>
                                <select name="department_id[]" class="department-select" data-index="${idx}">
                                    <option value="">Abteilung wählen</option>
                                    ${departmentOptions}
                                </select>
                            </td>

                            <td>
                                <select name="employee_id[]" class="employee-select" data-index="${idx}">
                                    <option value="">Innendienst wählen</option>
                                </select>
                            </td>

                            <td>
                                <select name="field_employee[]" class="field-employee-select" data-index="${idx}">
                                    <option value="">Außendienst wählen</option>
                                </select>
                            </td>

                            <td>
                                <select name="realization_time[]" class="realization-select" data-index="${idx}">
                                    <option value="">Bitte auswählen</option>
                                    <option value="soon">Schnellstmöglich</option>
                                    <option value="3">3 Monate</option>
                                    <option value="6">6 Monate</option>
                                    <option value="other">Sonstiges</option>
                                </select>
                            </td>

                            <td>
                                <select name="interest[]" class="interest-select" data-index="${idx}">
                                    <option value="intent">Kaufabsicht</option>
                                    <option value="interest">Kaufinteresse</option>
                                    <option value="option">Kaufoption</option>
                                </select>
                            </td>

                            <td style="text-align:center;">
                                <button type="button" class="oc-btn-ic danger removeRow" title="Entfernen">
                                    <i class="feather icon-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                $('#inquiryProductTable tbody').append(html);
                initRow(idx);

                if (window.feather) {
                    window.feather.replace();
                }
            }

            function initRow(idx) {
                const $product = $(`.product-select[data-index="${idx}"]`);
                const $service = $(`.service-select[data-index="${idx}"]`);
                const $dept = $(`.department-select[data-index="${idx}"]`);
                const $emp = $(`.employee-select[data-index="${idx}"]`);
                const $field = $(`.field-employee-select[data-index="${idx}"]`);
                const $real = $(`.realization-select[data-index="${idx}"]`);
                const $interest = $(`.interest-select[data-index="${idx}"]`);

                [$product, $service, $dept, $real, $interest].forEach($s => {
                    if ($s.length && !$s.data('select2')) {
                        $s.select2({ width: '100%' });
                    }
                });

                [$emp, $field].forEach($s => {
                    if ($s.length && !$s.data('select2')) {
                        $s.select2({
                            width: '100%',
                            templateResult: formatEmployee,
                            templateSelection: formatEmployeeSelection,
                            escapeMarkup: m => m
                        });
                    }
                });

                $product.off('change.inquiry').on('change.inquiry', function () {
                    loadServices(idx);
                    loadEmployees(idx, { autofill: true });
                });

                $service.off('change.inquiry').on('change.inquiry', function () {
                    loadEmployees(idx, { autofill: false });
                });

                $dept.off('change.inquiry').on('change.inquiry', function () {
                    loadEmployees(idx, { autofill: false });
                });
            }

            function loadServices(idx) {
                const pid = $(`.product-select[data-index="${idx}"]`).val();
                const $service = $(`.service-select[data-index="${idx}"]`);

                $service.empty().append('<option value="">Dienstleistung wählen</option>');

                if (!pid) {
                    $service.val('').trigger('change.select2');
                    return;
                }

                const list = SERVICES.filter(s => String(s.product_id) === String(pid));

                list.forEach(s => {
                    $service.append(`
                            <option value="${ocEscape(s.id)}">
                                ${ocEscape(translateService(s.phase_section))}
                            </option>
                        `);
                });

                if (list.length === 1) {
                    $service.val(String(list[0].id)).trigger('change.select2');
                } else {
                    $service.val('').trigger('change.select2');
                }
            }

            function loadEmployees(idx, options = {}) {
                const autofill = options.autofill === true;

                const $product = $(`.product-select[data-index="${idx}"]`);
                const $dept = $(`.department-select[data-index="${idx}"]`);
                const $service = $(`.service-select[data-index="${idx}"]`);
                const $emp = $(`.employee-select[data-index="${idx}"]`);
                const $field = $(`.field-employee-select[data-index="${idx}"]`);

                const pid = $product.val();
                let did = $dept.val();
                let sid = $service.val();

                if (!pid) {
                    clearEmployees($emp, $field);
                    return;
                }

                $.post(ROUTE_EMPLOYEES, {
                    _token: CSRF_TOKEN,
                    product_id: pid,
                    department_id: did || null,
                    service_id: sid || null,
                    stage: STAGE
                })
                    .done(res => {
                        let internalEmployees = [];
                        let externalEmployees = [];

                        if (Array.isArray(res)) {
                            internalEmployees = res;
                            externalEmployees = res;
                        } else {
                            if (autofill && !did && res.department_id) {
                                did = res.department_id;
                                $dept.val(String(did)).trigger('change.select2');
                            }

                            if (autofill && !sid && res.service_id) {
                                sid = res.service_id;

                                if (!$service.find(`option[value="${sid}"]`).length) {
                                    loadServices(idx);
                                }

                                if ($service.find(`option[value="${sid}"]`).length) {
                                    $service.val(String(sid)).trigger('change.select2');
                                }
                            }

                            internalEmployees = res.internal_employees || [];
                            externalEmployees = res.external_employees || [];
                        }

                        fillEmployeeSelect($emp, internalEmployees, 'Innendienst wählen');
                        fillEmployeeSelect($field, externalEmployees, 'Außendienst wählen');

                        if (!internalEmployees.length && !externalEmployees.length) {
                            ocToast('warn', 'Keine Mitarbeiter', 'Für diese Auswahl existieren keine Mitarbeiter.');
                        }
                    })
                    .fail(xhr => {
                        console.error('loadEmployees error:', xhr);
                        clearEmployees($emp, $field);
                        ocToast('bad', 'Fehler', 'Mitarbeiter konnten nicht geladen werden.');
                    });
            }

            function fillEmployeeSelect($select, employees, placeholder) {
                $select.empty().append(`<option value="">${ocEscape(placeholder)}</option>`);

                employees.forEach(emp => {
                    const positions = Array.isArray(emp.positions) ? emp.positions.join(', ') : (emp.positions || '');

                    $select.append(`
                            <option
                                value="${ocEscape(emp.id)}"
                                data-img="${ocEscape(emp.image || '')}"
                                data-positions="${ocEscape(positions)}"
                            >
                                ${ocEscape((emp.name || '') + ' ' + (emp.lastname || ''))}
                            </option>
                        `);
                });

                $select.val('').trigger('change.select2');
            }

            function clearEmployees($emp, $field) {
                fillEmployeeSelect($emp, [], 'Innendienst wählen');
                fillEmployeeSelect($field, [], 'Außendienst wählen');
            }

            $('#addRow').on('click', function (e) {
                e.preventDefault();

                const $lastRow = $('#inquiryProductTable tbody tr:last');

                if ($lastRow.length) {
                    const missing = [];
                    const index = $lastRow.data('index');

                    if (!$(`.product-select[data-index="${index}"]`).val()) missing.push('Produkt');
                    if (!$(`.service-select[data-index="${index}"]`).val()) missing.push('Dienstleistung');
                    if (!$(`.department-select[data-index="${index}"]`).val()) missing.push('Abteilung');
                    if (!$(`.employee-select[data-index="${index}"]`).val()) missing.push('Innendienst');

                    if (missing.length) {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Zeile unvollständig',
                                html: `Bitte füllen Sie folgende Felder aus: <strong>${missing.join(', ')}</strong>`,
                                confirmButtonText: 'OK'
                            });
                        } else {
                            ocToast('warn', 'Zeile unvollständig', missing.join(', '));
                        }
                        return;
                    }
                }

                addRow();
            });

            $(document).on('click', '.removeRow', function () {
                const $row = $(this).closest('tr');

                $row.find('select').each(function () {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                });

                $row.fadeOut(120, function () {
                    $(this).remove();
                });
            });

            addRow();
        });
    </script>

    <script>
        "use strict";

        (function () {
            let map = null;
            let marker = null;
            let autocomplete = null;
            let geocoder = null;
            let elevationService = null;
            let streetView = null;

            const HOUSE_ZOOM = 20;
            const FALLBACK_CENTER = { lat: 51.5136, lng: 7.4653 };

            window.initMayarGoogleMap = function () {
                const mapEl = document.getElementById('gmp-map');
                const addressInput = document.getElementById('full_address');

                if (!mapEl || !window.google || !google.maps) {
                    return;
                }

                geocoder = new google.maps.Geocoder();
                elevationService = new google.maps.ElevationService();

                const savedLat = parseFloat(document.getElementById('latitude-input')?.value || mapEl.dataset.initialLat || '');
                const savedLng = parseFloat(document.getElementById('longitude-input')?.value || mapEl.dataset.initialLng || '');
                const savedAddress = addressInput?.value?.trim() || mapEl.dataset.initialAddress || '';

                const hasSavedCoords = Number.isFinite(savedLat) && Number.isFinite(savedLng);
                const initialCenter = hasSavedCoords ? { lat: savedLat, lng: savedLng } : FALLBACK_CENTER;

                map = new google.maps.Map(mapEl, {
                    center: initialCenter,
                    zoom: hasSavedCoords ? HOUSE_ZOOM : 13,
                    mapTypeId: google.maps.MapTypeId.SATELLITE,
                    tilt: 0,
                    streetViewControl: true,
                    mapTypeControl: true,
                    fullscreenControl: true,
                    zoomControl: true,
                    gestureHandling: 'greedy'
                });

                streetView = map.getStreetView();

                marker = new google.maps.Marker({
                    map: map,
                    position: initialCenter,
                    draggable: true,
                    visible: hasSavedCoords,
                    title: 'Objektstandort'
                });

                if (hasSavedCoords) {
                    setLatLng(savedLat, savedLng);
                    getElevation(savedLat, savedLng);
                    map.panTo(initialCenter);
                    map.setZoom(HOUSE_ZOOM);
                } else if (savedAddress) {
                    geocodeAddress(savedAddress, false);
                }

                if (addressInput && google.maps.places) {
                    autocomplete = new google.maps.places.Autocomplete(addressInput, {
                        fields: ['address_components', 'formatted_address', 'geometry', 'name', 'place_id'],
                        types: ['address'],
                        componentRestrictions: { country: 'de' }
                    });

                    autocomplete.addListener('place_changed', function () {
                        const place = autocomplete.getPlace();

                        if (!place || !place.geometry || !place.geometry.location) {
                            closeGoogleAutocomplete(addressInput);
                            geocodeAddress(addressInput.value, true);
                            return;
                        }

                        applyPlace(place);

                        setTimeout(function () {
                            closeGoogleAutocomplete(addressInput);
                        }, 80);
                    });

                    addressInput.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            closeGoogleAutocomplete(addressInput);
                            geocodeAddress(addressInput.value, true);
                        }
                    });

                    addressInput.addEventListener('blur', function () {
                        setTimeout(function () {
                            closeGoogleAutocomplete(addressInput, false);
                        }, 180);
                    });
                }

                marker.addListener('dragend', function () {
                    const pos = marker.getPosition();
                    if (!pos) return;

                    const lat = pos.lat();
                    const lng = pos.lng();

                    setLatLng(lat, lng);
                    getElevation(lat, lng);
                    reverseGeocode(lat, lng);
                    map.panTo({ lat, lng });
                    map.setZoom(HOUSE_ZOOM);
                });

                document.getElementById('show_map')?.addEventListener('click', function (e) {
                    e.preventDefault();

                    const currentAddress = addressInput?.value?.trim();

                    if (currentAddress) {
                        closeGoogleAutocomplete(addressInput);
                        geocodeAddress(currentAddress, true);
                        return;
                    }

                    const lat = parseFloat(document.getElementById('latitude-input')?.value || '');
                    const lng = parseFloat(document.getElementById('longitude-input')?.value || '');

                    if (Number.isFinite(lat) && Number.isFinite(lng)) {
                        setMapLocation(lat, lng, HOUSE_ZOOM, true);
                    } else {
                        ocToast('warn', 'Adresse fehlt', 'Bitte geben Sie zuerst eine Objektadresse ein.');
                    }
                });

                document.getElementById('screenshot-btn')?.addEventListener('click', takeMapScreenshot);

                setTimeout(() => {
                    google.maps.event.trigger(map, 'resize');

                    if (hasSavedCoords) {
                        map.setCenter(initialCenter);
                        map.setZoom(HOUSE_ZOOM);
                    }
                }, 300);
            };

            function closeGoogleAutocomplete(input, removeContainers = true) {
                if (input) input.blur();

                try {
                    document.activeElement?.blur?.();
                } catch (e) { }

                document.querySelectorAll('.pac-container').forEach(container => {
                    container.classList.add('oc-pac-force-hidden');
                    container.style.display = 'none';
                    container.style.opacity = '0';
                    container.style.pointerEvents = 'none';
                    container.style.visibility = 'hidden';
                });

                if (removeContainers) {
                    setTimeout(function () {
                        document.querySelectorAll('.pac-container').forEach(container => {
                            try { container.remove(); } catch (e) { }
                        });
                    }, 220);
                }
            }

            function applyPlace(place) {
                const location = place.geometry.location;
                const lat = location.lat();
                const lng = location.lng();

                const components = extractAddressComponents(place.address_components || [], place.formatted_address || '');
                const street = [components.route, components.street_number].filter(Boolean).join(' ').trim();
                const city = components.locality
                    || components.postal_town
                    || components.sublocality
                    || components.administrative_area_level_3
                    || components.administrative_area_level_2
                    || components.administrative_area_level_1
                    || '';
                const postcode = components.postal_code || '';

                setValue('street-input', street || components.route);
                setValue('street-name-input', components.route);
                setValue('street-number-input', components.street_number);
                setValue('locality-input', city);
                setValue('postal_code-input', postcode);

                if (place.formatted_address) {
                    setValue('full_address', place.formatted_address);
                }

                setMapLocation(lat, lng, HOUSE_ZOOM, true);
                getElevation(lat, lng);
                checkCustomer(street, postcode, lat, lng);
            }

            function geocodeAddress(address, notifyIfFailed = false) {
                if (!address || !geocoder) return;

                geocoder.geocode(
                    {
                        address: address,
                        componentRestrictions: { country: 'DE' }
                    },
                    function (results, status) {
                        if (status !== 'OK' || !results || !results.length) {
                            if (notifyIfFailed) {
                                ocToast('warn', 'Adresse nicht gefunden', 'Bitte prüfen Sie die Adresse oder wählen Sie sie aus der Google-Liste.');
                            }
                            return;
                        }

                        const result = results[0];
                        const location = result.geometry.location;
                        const lat = location.lat();
                        const lng = location.lng();

                        const components = extractAddressComponents(result.address_components || [], result.formatted_address || address);
                        const street = [components.route, components.street_number].filter(Boolean).join(' ').trim();
                        const city = components.locality
                            || components.postal_town
                            || components.sublocality
                            || components.administrative_area_level_3
                            || components.administrative_area_level_2
                            || components.administrative_area_level_1
                            || '';
                        const postcode = components.postal_code || '';

                        setValue('full_address', result.formatted_address || address);
                        setValue('street-input', street || components.route);
                        setValue('street-name-input', components.route);
                        setValue('street-number-input', components.street_number);
                        setValue('locality-input', city);
                        setValue('postal_code-input', postcode);

                        setMapLocation(lat, lng, HOUSE_ZOOM, true);
                        getElevation(lat, lng);
                        checkCustomer(street, postcode, lat, lng);
                    }
                );
            }

            function reverseGeocode(lat, lng) {
                if (!geocoder) return;

                geocoder.geocode(
                    { location: { lat, lng } },
                    function (results, status) {
                        if (status !== 'OK' || !results || !results.length) return;

                        const result = results[0];
                        const components = extractAddressComponents(result.address_components || [], result.formatted_address || '');
                        const street = [components.route, components.street_number].filter(Boolean).join(' ').trim();
                        const city = components.locality
                            || components.postal_town
                            || components.sublocality
                            || components.administrative_area_level_3
                            || components.administrative_area_level_2
                            || components.administrative_area_level_1
                            || '';
                        const postcode = components.postal_code || '';

                        setValue('full_address', result.formatted_address || '');
                        setValue('street-input', street || components.route);
                        setValue('street-name-input', components.route);
                        setValue('street-number-input', components.street_number);
                        setValue('locality-input', city);
                        setValue('postal_code-input', postcode);

                        checkCustomer(street, postcode, lat, lng);
                    }
                );
            }

            function setMapLocation(lat, lng, zoom = HOUSE_ZOOM, showMarker = true) {
                if (!map || !marker) return;

                const position = { lat: Number(lat), lng: Number(lng) };

                marker.setPosition(position);
                marker.setVisible(showMarker);
                map.setCenter(position);
                map.setZoom(zoom);

                setLatLng(position.lat, position.lng);
            }

            function setLatLng(lat, lng) {
                setValue('latitude-input', Number(lat).toFixed(8));
                setValue('longitude-input', Number(lng).toFixed(8));
            }

            function extractAddressComponents(components, formattedAddress = '') {
                const data = {
                    street_number: '',
                    route: '',
                    locality: '',
                    postal_town: '',
                    postal_code: '',
                    sublocality: '',
                    administrative_area_level_1: '',
                    administrative_area_level_2: '',
                    administrative_area_level_3: ''
                };

                components.forEach(component => {
                    (component.types || []).forEach(type => {
                        if (Object.prototype.hasOwnProperty.call(data, type) && !data[type]) {
                            data[type] = component.long_name || '';
                        }
                    });
                });

                if ((!data.route || !data.street_number) && formattedAddress) {
                    const firstPart = String(formattedAddress).split(',')[0].trim();
                    const match = firstPart.match(/^(.+?)\s+(\d+[a-zA-Z]?([\/-]\d+[a-zA-Z]?)?)$/);

                    if (match) {
                        data.route = data.route || match[1].trim();
                        data.street_number = data.street_number || match[2].trim();
                    } else if (!data.route) {
                        data.route = firstPart;
                    }
                }

                return data;
            }

            function setValue(id, value) {
                const el = document.getElementById(id);
                if (el) {
                    el.value = value ?? '';
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            function getElevation(lat, lng) {
                if (!elevationService) return;

                elevationService.getElevationForLocations(
                    { locations: [{ lat: Number(lat), lng: Number(lng) }] },
                    function (results, status) {
                        if (status === 'OK' && results && results[0]) {
                            setValue('elevation-input', Number(results[0].elevation).toFixed(2));
                        }
                    }
                );
            }

            function checkCustomer(street, postcode, lat, lng) {
                if (!street || !postcode || !Number.isFinite(Number(lat)) || !Number.isFinite(Number(lng))) {
                    return;
                }

                const name = document.getElementById('input_name')?.value || 'Unbekannt';
                const lastname = document.getElementById('input_lastname')?.value || 'Unbekannt';

                const url = `/check-new-leads/${encodeURIComponent(name)}/${encodeURIComponent(lastname)}/${encodeURIComponent(street)}/${encodeURIComponent(postcode)}/${encodeURIComponent(lat)}/${encodeURIComponent(lng)}`;

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(r => r.ok ? r.json() : null)
                    .then(data => {
                        if (!data || !['duplicate', 'neighbor'].includes(data.status)) return;

                        let tableHTML = `
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Nachname</th>
                                            <th>Adresse</th>
                                            <th>Radius (km)</th>
                                            <th>Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        if (data.status === 'duplicate' && data.customer) {
                            const c = data.customer;
                            tableHTML += `
                                <tr>
                                    <td>${ocEscape(c.name)}</td>
                                    <td>${ocEscape(c.lastname)}</td>
                                    <td>${ocEscape(c.full_address)}</td>
                                    <td>—</td>
                                    <td><a href="/new_lead_profile/${encodeURIComponent(c.id)}" class="btn btn-primary">Profil anzeigen</a></td>
                                </tr>
                            `;
                        }

                        if (data.status === 'neighbor' && Array.isArray(data.customers)) {
                            data.customers.forEach(c => {
                                tableHTML += `
                                    <tr>
                                        <td>${ocEscape(c.name)}</td>
                                        <td>${ocEscape(c.lastname)}</td>
                                        <td>${ocEscape(c.full_address)}</td>
                                        <td>${Number(c.distance || 0).toFixed(2)}</td>
                                        <td><a href="/new_lead_profile/${encodeURIComponent(c.id)}" class="btn btn-primary">Profil anzeigen</a></td>
                                    </tr>
                                `;
                            });
                        }

                        tableHTML += `</tbody></table></div>`;

                        if (window.Swal) {
                            Swal.fire({
                                title: data.status === 'duplicate' ? 'Doppelter Eintrag gefunden!' : 'Nachbarn gefunden!',
                                html: tableHTML,
                                icon: 'info',
                                width: '70%',
                                showCloseButton: true,
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(e => console.error('Kundenprüfung fehlgeschlagen:', e));
            }

            function takeMapScreenshot() {
                if (!map) {
                    ocToast('bad', 'Karte fehlt', 'Die Karte ist noch nicht geladen.');
                    return;
                }

                const apiKey = @json($googleMapsKey);

                if (!apiKey) {
                    ocToast('bad', 'API Key fehlt', 'GOOGLE_MAPS_KEY ist nicht korrekt in services.php konfiguriert.');
                    return;
                }

                if (streetView && streetView.getVisible()) {
                    const panoId = streetView.getPano();
                    const pov = streetView.getPov();

                    if (!panoId) {
                        ocToast('warn', 'Street View', 'Kein Street-View-Panorama verfügbar.');
                        return;
                    }

                    const url = `https://maps.googleapis.com/maps/api/streetview?size=900x500&pano=${encodeURIComponent(panoId)}&heading=${encodeURIComponent(pov.heading || 0)}&pitch=${encodeURIComponent(pov.pitch || 0)}&key=${encodeURIComponent(apiKey)}`;
                    fetchScreenshot(url, 'street_view_screenshot.jpg');
                    return;
                }

                const target = marker && marker.getVisible() ? marker.getPosition() : map.getCenter();

                if (!target) {
                    ocToast('bad', 'Karte fehlt', 'Kartenposition konnte nicht gelesen werden.');
                    return;
                }

                const lat = target.lat();
                const lng = target.lng();
                const zoom = map.getZoom() || HOUSE_ZOOM;

                const markerParam = marker && marker.getVisible()
                    ? `&markers=color:red%7C${encodeURIComponent(lat + ',' + lng)}`
                    : '';

                const url = `https://maps.googleapis.com/maps/api/staticmap?center=${encodeURIComponent(lat + ',' + lng)}&zoom=${encodeURIComponent(zoom)}&size=900x500&scale=2&maptype=satellite${markerParam}&key=${encodeURIComponent(apiKey)}`;
                fetchScreenshot(url, 'satellite_screenshot.png');
            }

            function fetchScreenshot(url, filename) {
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Screenshot request failed.');
                        return response.blob();
                    })
                    .then(blob => handleScreenshotBlob(blob, filename))
                    .catch(error => {
                        console.error('Screenshot failed:', error);
                        ocToast('bad', 'Screenshot fehlgeschlagen', 'Bitte prüfen Sie Static Maps API, Street View API und Billing.');
                    });
            }

            function handleScreenshotBlob(blob, filename) {
                const file = new File([blob], filename, { type: blob.type || 'image/png' });

                const fileInput = document.getElementById('screenshot-file-input');
                const previewContainer = document.getElementById('screenshot-preview');

                if (fileInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                }

                if (previewContainer) {
                    const imgPreview = document.createElement('img');
                    imgPreview.src = URL.createObjectURL(blob);
                    imgPreview.alt = filename;

                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(imgPreview);
                }

                ocToast('ok', 'Screenshot gespeichert', 'Der Screenshot wurde dem Formular hinzugefügt.');
            }
        })();
    </script>

    <script>
        "use strict";

        $(function () {
            $('#customer_form').on('submit', function (e) {
                e.preventDefault();

                const form = this;
                const $form = $(form);
                const $submitButtons = $form.find('button[type="submit"]');
                const formData = new FormData(form);

                /*
                 * IMPORTANT FIX
                 * The empty default row creates product_id[]=null, service_id[]=null, etc.
                 * That caused MySQL error: lead_product_lists.product_id cannot be null.
                 * We remove all product array values from the FormData and append only valid rows.
                 */
                [
                    'product_id[]',
                    'service_id[]',
                    'department_id[]',
                    'employee_id[]',
                    'field_employee[]',
                    'realization_time[]',
                    'interest[]',
                    'product'
                ].forEach(key => formData.delete(key));

                const products = [];
                let hasInvalidProductRow = false;

                $('#inquiryProductTable tbody tr').each(function () {
                    const index = $(this).data('index');
                    if (!index) return;

                    const product_id = $(`.product-select[data-index="${index}"]`).val() || '';
                    const service_id = $(`.service-select[data-index="${index}"]`).val() || '';
                    const department_id = $(`.department-select[data-index="${index}"]`).val() || '';
                    const employee_id = $(`.employee-select[data-index="${index}"]`).val() || '';
                    const field_employee = $(`.field-employee-select[data-index="${index}"]`).val() || '';
                    const realization = $(`.realization-select[data-index="${index}"]`).val() || '';
                    const interest = $(`.interest-select[data-index="${index}"]`).val() || 'intent';

                    const rowHasAnyValue = Boolean(
                        product_id || service_id || department_id || employee_id || field_employee || realization
                    );

                    if (!rowHasAnyValue) {
                        return;
                    }

                    if (!product_id) {
                        hasInvalidProductRow = true;
                        return;
                    }

                    products.push({
                        product_id,
                        service_id: service_id || null,
                        department_id: department_id || null,
                        employee_id: employee_id || null,
                        field_employee: field_employee || null,
                        realization_time: realization || null,
                        interest
                    });
                });

                if (hasInvalidProductRow) {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Produkt fehlt',
                            text: 'Bitte wählen Sie ein Produkt aus oder entfernen Sie die unvollständige Zeile.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        ocToast('warn', 'Produkt fehlt', 'Bitte wählen Sie ein Produkt aus oder entfernen Sie die unvollständige Zeile.');
                    }
                    return;
                }

                products.forEach(row => {
                    formData.append('product_id[]', row.product_id);
                    formData.append('service_id[]', row.service_id || '');
                    formData.append('department_id[]', row.department_id || '');
                    formData.append('employee_id[]', row.employee_id || '');
                    formData.append('field_employee[]', row.field_employee || '');
                    formData.append('realization_time[]', row.realization_time || '');
                    formData.append('interest[]', row.interest || 'intent');
                });

                formData.append('product', JSON.stringify(products));

                $submitButtons.prop('disabled', true).addClass('disabled');

                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method') || 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (response) {
                        if (response && response.success) {
                            if (window.Swal) {
                                Swal.fire({
                                    title: 'Gespeichert!',
                                    text: response.message || 'Die Daten wurden erfolgreich gespeichert.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            } else {
                                ocToast('ok', 'Gespeichert', response.message || 'Die Daten wurden erfolgreich gespeichert.');
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            }

                            return;
                        }

                        const message = response?.message || 'Etwas ist schiefgelaufen.';

                        if (window.Swal) {
                            Swal.fire('Fehler', message, 'error');
                        } else {
                            ocToast('bad', 'Fehler', message);
                        }
                    },
                    error: function (xhr) {
                        let message = 'Ein Fehler ist aufgetreten.';

                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        if (window.Swal) {
                            Swal.fire({
                                title: 'Fehler',
                                html: message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            ocToast('bad', 'Fehler', String(message).replaceAll('<br>', ' '));
                        }

                        console.error(xhr.responseText || xhr);
                    },
                    complete: function () {
                        $submitButtons.prop('disabled', false).removeClass('disabled');
                    }
                });
            });

            if (window.feather) {
                window.feather.replace();
            }
        });
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places&callback=initMayarGoogleMap"
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
                label: "{{ trim(($leads->title ?? '') . ' ' . ($leads->name ?? '') . ' ' . ($leads->lastname ?? '')) ?: '—' }}",
                url: "{{ url('/new_lead_profile/' . $leads->id) }}"
            },
            {
                label: 'Nue Objekt',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush