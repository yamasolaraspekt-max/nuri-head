@extends('admin.layouts.app')

@section('title', 'KUNDEN UND OBJEKTDATEN')

@php
    $customerType = $data->customer_type ?? 'privat';
    $customerType = in_array($customerType, ['privat', 'Gewerbe']) ? $customerType : 'privat';

    $titles = ['Frau', 'Herr', 'An die', 'An den'];
    $academicTitles = ['Dr.', 'Prof.', 'Prof. Dr.', 'Dipl.-Ing.', 'Mag.'];

    $initialAddress = trim(
        collect([
            $data->street ?? null,
            trim(($data->postcode ?? '') . ' ' . ($data->city ?? '')),
        ])->filter()->implode(', ')
    );

    $initialLat = old('latitude', $data->latitude ?? null);
    $initialLng = old('longitude', $data->longitude ?? null);

    $googleMapsKey = config('services.google.maps_key', '');

    $customerName = trim(($data->title ?? '') . ' ' . ($data->academic_title ?? '') . ' ' . ($data->name ?? '') . ' ' . ($data->lastname ?? ''));
@endphp

@once
    @push('style')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

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
                color: var(--text-main);
                margin: 20px auto;
                padding-right: 79px;
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
                color:var(--primary);
                border-color:var(--primary-light);
                background:var(--primary-light);
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

            .oc-stat-icon.customer { background:var(--blue-light); color:var(--blue); }
            .oc-stat-icon.type { background:var(--success-light); color:var(--success); }
            .oc-stat-icon.source { background:var(--warning-light); color:#d97706; }
            .oc-stat-icon.status { background:var(--gray-light); color:var(--gray); }

            .oc-stat-label {
                font-size:11px;
                font-weight:900;
                color:var(--text-muted);
                text-transform:uppercase;
                letter-spacing:.06em;
            }

            .oc-stat-value {
                font-size:20px;
                font-weight:900;
                color:#111827;
                line-height:1.1;
                margin-top:4px;
                word-break:break-word;
            }

            .oc-stat-sub {
                font-size:12px;
                color:var(--text-muted);
                margin-top:4px;
            }

            .oc-grid {
                display:grid;
                grid-template-columns:minmax(0, 1fr) 460px;
                gap:18px;
                align-items:start;
            }

            @media(max-width:1200px) {
                .oc-grid {
                    grid-template-columns:1fr;
                }
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

            .oc-form-grid {
                display:grid;
                grid-template-columns:repeat(2, minmax(0,1fr));
                gap:16px;
            }

            @media(max-width:900px) {
                .oc-form-grid {
                    grid-template-columns:1fr;
                }
            }

            .oc-form-group {
                min-width:0;
            }

            .oc-form-group.full {
                grid-column:1 / -1;
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

            .oc-input-form,
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
                min-height:150px;
                resize:vertical;
            }

            .oc-input-form:focus,
            .oc-select:focus,
            .oc-textarea:focus {
                border-color:var(--primary);
                box-shadow:0 0 0 3px var(--primary-light);
            }

            .oc-radio-row {
                display:flex;
                align-items:center;
                flex-wrap:wrap;
                gap:12px;
            }

            .oc-radio-card {
                display:flex;
                align-items:center;
                gap:8px;
                border:1px solid var(--border);
                border-radius:12px;
                padding:11px 12px;
                background:#fff;
                min-height:42px;
                cursor:pointer;
                transition:var(--transition);
                font-weight:900;
                color:#111827;
            }

            .oc-radio-card:hover {
                background:#f9fafb;
                border-color:#d1d5db;
            }

            .oc-radio-card input {
                width:16px;
                height:16px;
                accent-color:var(--primary);
            }

            .oc-address-row {
                display:flex;
                gap:10px;
                align-items:center;
            }

            .oc-address-row .oc-input-form {
                flex:1;
            }

            .oc-map-box {
                height:356px;
                width:100%;
                border-radius:16px;
                overflow:hidden;
                border:1px solid var(--border);
                background:#e5e7eb;
                position:relative;
            }

            #gmp-map {
                width:100%;
                height:100%;
            }

            .oc-map-actions {
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:10px;
                flex-wrap:wrap;
                margin-top:12px;
            }

            .oc-map-hint {
                font-size:12px;
                color:var(--text-muted);
                line-height:1.45;
            }

            #screenshot-preview img {
                width:100%;
                max-height:240px;
                object-fit:cover;
                border:1px solid var(--border);
                border-radius:14px;
                margin-top:12px;
                background:#fff;
            }

            .oc-rating-list {
                display:grid;
                grid-template-columns:1fr;
                gap:12px;
            }

            .oc-rating-row {
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:14px;
                background:#f9fafb;
                border:1px solid var(--border);
                border-radius:14px;
                padding:12px;
            }

            .oc-rating-label {
                font-size:12px;
                color:#111827;
                font-weight:900;
                text-transform:uppercase;
                letter-spacing:.04em;
            }

            .star-rating {
                display:flex;
                align-items:center;
                gap:4px;
                font-size:20px;
                cursor:pointer;
                color:#d1d5db;
            }

            .star {
                cursor:pointer;
                color:#d1d5db;
                transition:var(--transition);
            }

            .star.selected_star {
                color:#f8ac00;
            }

            .star.hovered {
                color:#f59e0b;
            }

            .select2-container {
                width:100% !important;
                font-size:13px;
            }

            .select2-container--default .select2-selection--single {
                height:42px;
                border:1px solid var(--border);
                border-radius:10px;
                display:flex;
                align-items:center;
                background:#fff;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height:40px;
                color:#111827;
                padding-left:12px;
                padding-right:30px;
                font-size:13px;
                font-weight:700;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height:40px;
                right:6px;
            }

            .oc-savebar {
                position:sticky;
                bottom:16px;
                z-index:80;
                display:flex;
                justify-content:flex-end;
                pointer-events:none;
                margin-top:18px;
            }

            .oc-savebar-inner {
                pointer-events:auto;
                background:rgba(255,255,255,.92);
                border:1px solid var(--border);
                border-radius:16px;
                box-shadow:var(--shadow);
                padding:10px;
                display:flex;
                align-items:center;
                gap:10px;
                backdrop-filter:blur(10px);
            }

            .oc-status-icon img {
                width:32px;
                height:32px;
                display:block;
            }

            .oc-toast-wrap {
                position:fixed;
                right:20px;
                bottom:20px;
                z-index:9999;
                display:flex;
                flex-direction:column;
                gap:10px;
                pointer-events:none;
            }

            .oc-toast {
                pointer-events:auto;
                min-width:280px;
                max-width:380px;
                background:#fff;
                border:1px solid var(--border);
                border-radius:14px;
                box-shadow:var(--shadow);
                padding:12px;
                display:flex;
                gap:10px;
                align-items:flex-start;
                animation:ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
            }

            @keyframes ocToastIn {
                from { transform:translateX(100%); opacity:0; }
                to { transform:translateX(0); opacity:1; }
            }

            .oc-toast-ic {
                width:34px;
                height:34px;
                border-radius:12px;
                display:flex;
                align-items:center;
                justify-content:center;
                flex:0 0 auto;
            }

            .oc-toast-ic.ok { background:var(--success-light); color:var(--success); }
            .oc-toast-ic.bad { background:var(--danger-light); color:var(--danger); }
            .oc-toast-ic.warn { background:var(--warning-light); color:#d97706; }

            .oc-toast-ttl {
                font-weight:900;
                font-size:13px;
                margin:0;
                color:#111827;
            }

            .oc-toast-msg {
                font-size:12px;
                color:#374151;
                margin:4px 0 0 0;
                line-height:1.4;
            }

            .oc-toast-x {
                margin-left:auto;
                background:transparent;
                border:none;
                cursor:pointer;
                color:var(--text-muted);
                font-size:18px;
                line-height:1;
            }

            .d-none {
                display:none !important;
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

                .oc-map-box {
                    height:360px;
                }

                .oc-savebar {
                    bottom:8px;
                }

                .oc-savebar-inner {
                    width:100%;
                }

                .oc-savebar-inner .oc-btn {
                    width:100%;
                }
            }
        </style>
    @endpush
@endonce

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="oc-wrap">
        <form
            class="form form-horizontal custom-file-upload"
            method="POST"
            id="customer_form"
            action="{{ route('new.lead.details.update') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <input type="hidden" name="id" value="{{ $id }}">

            <div class="oc-header">
                <div class="oc-titlebar">
                    <div>
                        <div class="oc-title">Kunden und Objektdaten</div>
                        <div class="oc-sub">
                            Kundendaten bearbeiten, Kontaktdaten prüfen und Objektadresse exakt auf der Karte setzen.
                        </div>

                        <div class="oc-breadcrumb">
                            <a href="{{ url('/employee_dashboard') }}">Home</a>
                            <span>›</span>
                            <a href="{{ url()->previous() }}">Kunden</a>
                            <span>›</span>
                            <span class="current">Kunden und Objektdaten</span>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="oc-btn">
                            <i class="feather icon-arrow-right"></i>
                            Nächste
                        </button>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="oc-analytics">
                <div class="oc-stat">
                    <div class="oc-stat-icon customer">
                        <i class="feather icon-user"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Kunde</div>
                        <div class="oc-stat-value">{{ $customerName ?: '—' }}</div>
                        <div class="oc-stat-sub">Basisdaten</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon type">
                        <i class="feather icon-briefcase"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Kundentyp</div>
                        <div class="oc-stat-value">{{ $customerType }}</div>
                        <div class="oc-stat-sub">Privat / Gewerbe</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon source">
                        <i class="feather icon-compass"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Quelle</div>
                        <div class="oc-stat-value">{{ $data->source ?: '—' }}</div>
                        <div class="oc-stat-sub">Lead-Herkunft</div>
                    </div>
                </div>

                <div class="oc-stat">
                    <div class="oc-stat-icon status">
                        <i class="feather icon-map-pin"></i>
                    </div>
                    <div>
                        <div class="oc-stat-label">Objekt</div>
                        <div class="oc-stat-value">{{ $data->city ?: '—' }}</div>
                        <div class="oc-stat-sub">{{ $data->postcode ?: 'Adresse prüfen' }}</div>
                    </div>
                </div>
            </div>

            <div class="oc-grid">
                <div>
                    <div class="oc-card">
                        <div class="oc-card-header">
                            <div>
                                <h3 class="oc-card-title">Kundendaten</h3>
                                <div class="oc-card-sub">Anrede, Name, Firma und Kontaktinformationen</div>
                            </div>
                        </div>

                        <div class="oc-card-body">
                            <div class="oc-form-grid">
                                <div class="oc-form-group full">
                                    <label class="oc-label">Kundentyp</label>

                                    <div class="oc-radio-row">
                                        <label class="oc-radio-card">
                                            <input
                                                type="radio"
                                                class="form-element"
                                                name="customer_type"
                                                id="customer_type1"
                                                value="privat"
                                                @checked($customerType === 'privat')
                                            >
                                            Privat
                                        </label>

                                        <label class="oc-radio-card">
                                            <input
                                                type="radio"
                                                class="form-element"
                                                name="customer_type"
                                                id="customer_type2"
                                                value="Gewerbe"
                                                @checked($customerType === 'Gewerbe')
                                            >
                                            Gewerbe
                                        </label>
                                    </div>
                                </div>

                                <div class="oc-form-group">
                                    <label class="oc-label">Anrede</label>
                                    <select
                                        class="oc-select select2-tags form-element"
                                        name="title"
                                        data-placeholder="Anrede auswählen oder eingeben"
                                    >
                                        <option></option>

                                        @foreach($titles as $title)
                                            <option value="{{ $title }}" @selected(($data->title ?? '') === $title)>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                        @if(!empty($data->title) && !in_array($data->title, $titles))
                                            <option value="{{ $data->title }}" selected>{{ $data->title }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="oc-form-group">
                                    <label class="oc-label">Akademischer Titel</label>
                                    <select
                                        class="oc-select select2-tags form-element"
                                        name="academic_title"
                                        data-placeholder="Titel auswählen oder eingeben"
                                    >
                                        <option></option>

                                        @foreach($academicTitles as $title)
                                            <option value="{{ $title }}" @selected(($data->academic_title ?? '') === $title)>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                        @if(!empty($data->academic_title) && !in_array($data->academic_title, $academicTitles))
                                            <option value="{{ $data->academic_title }}" selected>{{ $data->academic_title }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="oc-form-group full" id="firma-container">
                                    <label class="oc-label">Firma</label>
                                    <input
                                        type="text"
                                        id="firma"
                                        class="oc-input-form form-element"
                                        value="{{ old('firma', $data->firma) }}"
                                        name="firma"
                                    >
                                </div>

                                <div class="oc-form-group">
                                    <label class="oc-label">Vorname</label>
                                    <input
                                        type="text"
                                        id="name"
                                        class="oc-input-form form-element"
                                        value="{{ old('name', $data->name) }}"
                                        name="name"
                                        autocomplete="off"
                                        list="name-options"
                                    >
                                    <datalist id="name-options"></datalist>
                                </div>

                                <div class="oc-form-group">
                                    <label class="oc-label">Nachname</label>
                                    <input
                                        type="text"
                                        id="lastname"
                                        class="oc-input-form form-element"
                                        value="{{ old('lastname', $data->lastname) }}"
                                        name="lastname"
                                        autocomplete="off"
                                        list="lastname-options"
                                    >
                                    <datalist id="lastname-options"></datalist>
                                </div>

                                <div class="oc-form-group full">
                                    <label class="oc-label">STR. / NR. / PLZ. / ORT</label>

                                    <div class="oc-address-row">
                                        <input
                                            id="full_address"
                                            type="text"
                                            class="oc-input-form form-element"
                                            placeholder="Adresse eingeben und aus Google auswählen"
                                            name="full_address"
                                            autocomplete="off"
                                            value="{{ old('full_address', $initialAddress) }}"
                                        >

                                        <button type="button" class="oc-btn-ic primary" id="show_map" title="Adresse auf Karte anzeigen">
                                            <i class="feather icon-map"></i>
                                        </button>
                                    </div>

                                    <input id="street-input" type="hidden" class="form-element" name="street" value="{{ old('street', $data->street) }}">
                                    <input id="latitude-input" type="hidden" class="form-element" name="latitude" value="{{ $initialLat }}">
                                    <input id="longitude-input" type="hidden" class="form-element" name="longitude" value="{{ $initialLng }}">
                                    <input id="elevation-input" type="hidden" class="form-element" name="elevation" value="{{ old('elevation', $data->elevation) }}">
                                    <input id="postal_code-input" type="hidden" class="form-element" name="postcode" value="{{ old('postcode', $data->postcode) }}">
                                    <input id="locality-input" type="hidden" class="form-element" name="city" value="{{ old('city', $data->city) }}">
                                </div>

                                <div class="oc-form-group">
                                    <label class="oc-label">Festnetz</label>
                                    <input
                                        type="text"
                                        class="oc-input-form form-element"
                                        value="{{ old('telephone', $data->telephone) }}"
                                        id="telephone-input"
                                        name="telephone"
                                    >
                                </div>

                                <div class="oc-form-group">
                                    <label class="oc-label">Handy</label>
                                    <input
                                        type="text"
                                        class="oc-input-form form-element"
                                        value="{{ old('phone', $data->phone) }}"
                                        name="phone"
                                        id="phone-input"
                                    >
                                </div>

                                <div class="oc-form-group full">
                                    <label class="oc-label">E-Mail</label>
                                    <input
                                        type="email"
                                        class="oc-input-form form-element"
                                        id="email-input"
                                        value="{{ old('email', $data->email) }}"
                                        name="email"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="oc-card">
                        <div class="oc-card-header">
                            <div>
                                <h3 class="oc-card-title">Bewertung & Notizen</h3>
                                <div class="oc-card-sub">Quelle, Interesse, Ernsthaftigkeit und Zusatzinformationen</div>
                            </div>
                        </div>

                        <div class="oc-card-body">
                            <div class="oc-form-grid">
                                <div class="oc-form-group full">
                                    <label class="oc-label">Quelle</label>
                                    <select name="source" id="source" class="oc-select form-element">
                                        <option></option>
                                        <option value="Telefonisch" @selected($data->source === 'Telefonisch')>Telefonisch</option>
                                        <option value="Persönlich" @selected($data->source === 'Persönlich')>Persönlich</option>
                                        <option value="Mail" @selected($data->source === 'Mail')>Mail</option>
                                        <option value="Nachbar" @selected($data->source === 'Nachbar')>Nachbar</option>
                                        <option value="Empfehlung" @selected($data->source === 'Empfehlung')>Empfehlung</option>
                                        <option value="Solarrechner" @selected($data->source === 'Solarrechner')>Solarrechner</option>
                                        <option value="Herstellerlead" @selected($data->source === 'Herstellerlead')>Herstellerlead</option>
                                        <option value="Kunde aus Vergangenheit" @selected($data->source === 'Kunde aus Vergangenheit')>Kunde aus Vergangenheit</option>
                                        <option value="Messe" @selected($data->source === 'Messe')>Messe</option>
                                        <option value="Messe/Veranstaltung" @selected($data->source === 'Messe/Veranstaltung')>Messe/Veranstaltung</option>

                                        @if(
                                                !empty($data->source) && !in_array($data->source, [
                                                    'Telefonisch',
                                                    'Persönlich',
                                                    'Mail',
                                                    'Nachbar',
                                                    'Empfehlung',
                                                    'Solarrechner',
                                                    'Herstellerlead',
                                                    'Kunde aus Vergangenheit',
                                                    'Messe',
                                                    'Messe/Veranstaltung',
                                                ])
                                            )
                                                <option value="{{ $data->source }}" selected>{{ $data->source }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="oc-form-group full">
                                    <div class="oc-rating-list">
                                        <div class="oc-rating-row">
                                            <div class="oc-rating-label">Interesse</div>
                                            <div
                                                class="star-rating form-element"
                                                data-category="interest"
                                                data-input="interest_rating"
                                                data-rating="{{ $data->interest_rating ?? 0 }}"
                                            >
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                            </div>
                                            <input type="hidden" name="interest_rating" value="{{ $data->interest_rating ?? 0 }}">
                                        </div>

                                        <div class="oc-rating-row">
                                            <div class="oc-rating-label">Ernsthaftigkeit</div>
                                            <div
                                                class="star-rating form-element"
                                                data-category="seriousness"
                                                data-input="seriousness_rating"
                                                data-rating="{{ $data->seriousness_rating ?? 0 }}"
                                            >
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                            </div>
                                            <input type="hidden" name="seriousness_rating" value="{{ $data->seriousness_rating ?? 0 }}">
                                        </div>

                                        <div class="oc-rating-row">
                                            <div class="oc-rating-label">Preisinformation</div>
                                            <div
                                                class="star-rating form-element"
                                                data-category="price_information"
                                                data-input="price_information"
                                                data-rating="{{ $data->price_information ?? 0 }}"
                                            >
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                                <span class="star"><i class="fa fa-star"></i></span>
                                            </div>
                                            <input type="hidden" name="price_information" value="{{ $data->price_information ?? 0 }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="oc-form-group full">
                                    <label class="oc-label">Notizen</label>
                                    <textarea name="info" class="oc-textarea form-element">{{ old('info', $data->info) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="oc-card">
                        <div class="oc-card-header">
                            <div>
                                <h3 class="oc-card-title">Objektbilder</h3>
                                <div class="oc-card-sub">Satellitenkarte und Screenshot für die Objektadresse</div>
                            </div>
                        </div>

                        <div class="oc-card-body">
                            <div class="oc-map-box">
                                <div
                                    id="gmp-map"
                                    data-initial-lat="{{ $initialLat }}"
                                    data-initial-lng="{{ $initialLng }}"
                                    data-initial-address="{{ $initialAddress }}"
                                ></div>
                            </div>

                            <div class="oc-map-actions">
                                <div class="oc-map-hint">
                                    Adresse auswählen oder auf Karte anzeigen. Die Karte zoomt automatisch auf das Objekt.
                                </div>

                                <button type="button" class="oc-btn-soft" id="screenshot-btn">
                                    <i class="feather icon-camera"></i>
                                    Screenshot
                                </button>
                            </div>

                            <div id="screenshot-preview"></div>

                            <input
                                type="file"
                                class="d-none"
                                id="screenshot-file-input"
                                name="screenshot_file"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="oc-savebar">
                <div class="oc-savebar-inner">
                    <div id="status-icon" class="oc-status-icon"></div>

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

@once
    @push('scripts')
        <script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>

        <script>
            window.GlobalBreadcrumbs = [
                {
                    label: 'Workspace',
                    url: "{{ url('/employee_dashboard') }}"
                },
                {
                    label: 'Kunden',
                    url: "{{ url()->previous() }}"
                },
                {
                    label: 'Kunden und Objektdaten',
                    url: "{{ url()->current() }}",
                    clickable: false
                }
            ];

            if (window.setGlobalBreadcrumbs) {
                window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
            }
        </script>

        <script>
            window.ocToast = function(kind, title, msg) {
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
                    try {
                        el.remove();
                    } catch(e) {}
                }, 4500);
            };

            window.ocEscape = function(value) {
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

            const CSRF_TOKEN = @json(csrf_token());
            const GOOGLE_MAPS_KEY = @json($googleMapsKey);
            const AMP_GREEN = @json(asset('images/icons/ampel-gruen.svg'));
            const AMP_YELLOW = @json(asset('images/icons/ampel-gelb.svg'));
            const AMP_RED = @json(asset('images/icons/ampel-rot.svg'));

            $(function () {
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#source').select2({
                        tags: true,
                        placeholder: 'Quelle auswählen',
                        allowClear: true,
                        width: '100%'
                    });

                    $('.select2-tags').select2({
                        tags: true,
                        allowClear: true,
                        width: '100%',
                        placeholder: function () {
                            return $(this).data('placeholder');
                        }
                    });
                }

                if (window.feather) {
                    window.feather.replace();
                }
            });
        </script>

        <script>
            "use strict";

            (function () {
                function bindSuggestion(inputId, datalistId, endpoint) {
                    const input = document.getElementById(inputId);
                    const list = document.getElementById(datalistId);

                    if (!input || !list) return;

                    let timer = null;

                    input.addEventListener('input', function () {
                        const value = this.value.trim();

                        clearTimeout(timer);

                        if (value.length < 2) {
                            list.innerHTML = '';
                            return;
                        }

                        timer = setTimeout(() => {
                            fetch(`${endpoint}?query=${encodeURIComponent(value)}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.ok ? response.json() : [])
                            .then(data => {
                                list.innerHTML = Array.isArray(data)
                                    ? data.map(name => `<option value="${ocEscape(name)}">`).join('')
                                    : '';
                            })
                            .catch(error => console.error('Suggestion error:', error));
                        }, 250);
                    });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    bindSuggestion('lastname', 'lastname-options', '/api/lead-lastname-suggestions');
                    bindSuggestion('name', 'name-options', '/api/lead-name-suggestions');
                });
            })();
        </script>

        <script>
            "use strict";

            (function () {
                function updateFirmaVisibility() {
                    const firmaContainer = document.getElementById('firma-container');
                    const selected = document.querySelector('input[name="customer_type"]:checked');

                    if (!firmaContainer || !selected) return;

                    firmaContainer.style.display = selected.value === 'privat' ? 'none' : 'block';
                }

                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('input[name="customer_type"]').forEach(radio => {
                        radio.addEventListener('change', updateFirmaVisibility);
                    });

                    updateFirmaVisibility();
                });
            })();
        </script>

        <script>
            "use strict";

            (function () {
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.star-rating').forEach(rating => {
                        const stars = rating.querySelectorAll('.star');
                        const initialValue = parseInt(rating.dataset.rating, 10) || 0;

                        updateStars(rating, initialValue - 1);

                        stars.forEach((star, index) => {
                            star.addEventListener('click', function () {
                                rating.dataset.rating = index + 1;
                                updateStars(rating, index);
                                updateInput(rating);
                            });

                            star.addEventListener('mouseover', function () {
                                highlightStars(rating, index);
                            });

                            star.addEventListener('mouseout', function () {
                                resetStars(rating);
                            });
                        });
                    });
                });

                function updateStars(rating, index) {
                    rating.querySelectorAll('.star').forEach((star, i) => {
                        if (i <= index) {
                            star.classList.add('selected_star');
                            star.classList.remove('hovered');
                        } else {
                            star.classList.remove('selected_star');
                            star.classList.remove('hovered');
                        }
                    });
                }

                function highlightStars(rating, index) {
                    rating.querySelectorAll('.star').forEach((star, i) => {
                        if (i <= index) {
                            star.classList.add('hovered');
                        } else {
                            star.classList.remove('hovered');
                        }
                    });
                }

                function resetStars(rating) {
                    const ratingValue = parseInt(rating.dataset.rating, 10) || 0;
                    updateStars(rating, ratingValue - 1);
                }

                function updateInput(rating) {
                    const inputName = rating.dataset.input || `${rating.dataset.category}_rating`;
                    const input = document.querySelector(`input[name="${inputName}"]`);

                    if (input) {
                        input.value = rating.dataset.rating || 0;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            })();
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
                        draggable: false,
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
                            fields: ['address_components', 'formatted_address', 'geometry', 'name'],
                            types: ['address'],
                            componentRestrictions: { country: ['de'] }
                        });

                        autocomplete.addListener('place_changed', function () {
                            const place = autocomplete.getPlace();

                            if (!place || !place.geometry || !place.geometry.location) {
                                ocToast('warn', 'Adresse wählen', 'Bitte wählen Sie eine Adresse aus der Google-Liste aus.');
                                return;
                            }

                            applyPlace(place);
                        });

                        addressInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                geocodeAddress(addressInput.value, true);
                            }
                        });

                        addressInput.addEventListener('blur', function () {
                            const value = addressInput.value.trim();

                            if (value) {
                                geocodeAddress(value, false);
                            }
                        });
                    }

                    document.getElementById('show_map')?.addEventListener('click', function(e) {
                        e.preventDefault();

                        const currentAddress = addressInput?.value?.trim();

                        if (currentAddress) {
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

                function applyPlace(place) {
                    const location = place.geometry.location;
                    const lat = location.lat();
                    const lng = location.lng();

                    const components = extractAddressComponents(place.address_components || []);
                    const street = `${components.route} ${components.street_number}`.trim();
                    const city = components.locality || components.postal_town || components.administrative_area_level_2 || '';
                    const postcode = components.postal_code || '';

                    setValue('street-input', street);
                    setValue('locality-input', city);
                    setValue('postal_code-input', postcode);

                    if (place.formatted_address) {
                        setValue('full_address', place.formatted_address);
                    }

                    setMapLocation(lat, lng, HOUSE_ZOOM, true);
                    getElevation(lat, lng);
                    checkCustomer(street, postcode, lat, lng);
                    updateStatusIcon();
                }

                function geocodeAddress(address, notifyIfFailed = false) {
                    if (!address || !geocoder) return;

                    geocoder.geocode(
                        {
                            address: address,
                            componentRestrictions: { country: 'DE' }
                        },
                        function(results, status) {
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

                            const components = extractAddressComponents(result.address_components || []);
                            const street = `${components.route} ${components.street_number}`.trim();
                            const city = components.locality || components.postal_town || components.administrative_area_level_2 || '';
                            const postcode = components.postal_code || '';

                            setValue('full_address', result.formatted_address || address);
                            setValue('street-input', street);
                            setValue('locality-input', city);
                            setValue('postal_code-input', postcode);

                            setMapLocation(lat, lng, HOUSE_ZOOM, true);
                            getElevation(lat, lng);
                            checkCustomer(street, postcode, lat, lng);
                            updateStatusIcon();
                        }
                    );
                }

                function setMapLocation(lat, lng, zoom = HOUSE_ZOOM, showMarker = true) {
                    if (!map || !marker) return;

                    const position = { lat, lng };

                    marker.setPosition(position);
                    marker.setVisible(showMarker);
                    map.setCenter(position);
                    map.setZoom(zoom);

                    setLatLng(lat, lng);
                }

                function setLatLng(lat, lng) {
                    setValue('latitude-input', Number(lat).toFixed(8));
                    setValue('longitude-input', Number(lng).toFixed(8));
                }

                function extractAddressComponents(components) {
                    const data = {
                        street_number: '',
                        route: '',
                        locality: '',
                        postal_town: '',
                        postal_code: '',
                        administrative_area_level_2: ''
                    };

                    components.forEach(component => {
                        (component.types || []).forEach(type => {
                            if (Object.prototype.hasOwnProperty.call(data, type) && !data[type]) {
                                data[type] = component.long_name || '';
                            }
                        });
                    });

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
                        { locations: [{ lat, lng }] },
                        function(results, status) {
                            if (status === 'OK' && results && results[0]) {
                                setValue('elevation-input', Number(results[0].elevation).toFixed(2));
                            }
                        }
                    );
                }

                function checkCustomer(street, postcode, lat, lng) {
                    const name = document.getElementById('name')?.value || '';
                    const lastname = document.getElementById('lastname')?.value || '';

                    if (!street || !postcode || !Number.isFinite(Number(lat)) || !Number.isFinite(Number(lng))) {
                        return;
                    }

                    const url = `/check-new-leads/${encodeURIComponent(name)}/${encodeURIComponent(lastname)}/${encodeURIComponent(street)}/${encodeURIComponent(postcode)}/${encodeURIComponent(lat)}/${encodeURIComponent(lng)}`;

                    fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.ok ? response.json() : null)
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
                    .catch(error => console.error('Kundenprüfung fehlgeschlagen:', error));
                }

                function takeMapScreenshot() {
                    if (!map) {
                        ocToast('bad', 'Karte fehlt', 'Die Karte ist noch nicht geladen.');
                        return;
                    }

                    if (streetView && streetView.getVisible()) {
                        const panoId = streetView.getPano();
                        const pov = streetView.getPov();

                        if (!panoId) {
                            ocToast('warn', 'Street View', 'Kein Street-View-Panorama verfügbar.');
                            return;
                        }

                        const url = `https://maps.googleapis.com/maps/api/streetview?size=900x500&pano=${encodeURIComponent(panoId)}&heading=${encodeURIComponent(pov.heading || 0)}&pitch=${encodeURIComponent(pov.pitch || 0)}&key=${encodeURIComponent(GOOGLE_MAPS_KEY)}`;

                        fetchScreenshot(url, 'street_view_screenshot.jpg');
                        return;
                    }

                    const center = map.getCenter();

                    if (!center) {
                        ocToast('bad', 'Karte fehlt', 'Kartenposition konnte nicht gelesen werden.');
                        return;
                    }

                    const lat = center.lat();
                    const lng = center.lng();
                    const zoom = map.getZoom() || HOUSE_ZOOM;

                    const markerParam = marker && marker.getVisible()
                        ? `&markers=color:red%7C${encodeURIComponent(lat + ',' + lng)}`
                        : '';

                    const url = `https://maps.googleapis.com/maps/api/staticmap?center=${encodeURIComponent(lat + ',' + lng)}&zoom=${encodeURIComponent(zoom)}&size=900x500&scale=2&maptype=satellite${markerParam}&key=${encodeURIComponent(GOOGLE_MAPS_KEY)}`;

                    fetchScreenshot(url, 'satellite_screenshot.png');
                }

                function fetchScreenshot(url, filename) {
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Screenshot request failed.');
                            }

                            return response.blob();
                        })
                        .then(blob => handleScreenshotBlob(blob, filename))
                        .catch(error => {
                            console.error('Screenshot failed:', error);
                            ocToast('bad', 'Screenshot fehlgeschlagen', 'Bitte prüfen Sie den Google Maps API Key und die Static Maps/Street View API.');
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

            function updateStatusIcon() {
                const street = document.getElementById('street-input')?.value.trim() || '';
                const postcode = document.getElementById('postal_code-input')?.value.trim() || '';
                const city = document.getElementById('locality-input')?.value.trim() || '';
                const telephone = document.getElementById('telephone-input')?.value.trim() || '';
                const phone = document.getElementById('phone-input')?.value.trim() || '';
                const email = document.getElementById('email-input')?.value.trim() || '';
                const statusIcon = document.getElementById('status-icon');

                if (!statusIcon) return;

                if (street && postcode && city && (telephone || phone) && email) {
                    statusIcon.innerHTML = `<img src="${AMP_GREEN}" alt="QUALIFIZIERT" data-content="DIE ANFRAGE IST BEREIT ZU QUALIFIZIEREN" data-trigger="hover" data-original-title="QUALIFIZIERT">`;
                } else if (street || postcode || city || telephone || phone || email) {
                    statusIcon.innerHTML = `<img src="${AMP_YELLOW}" alt="NICHT QUALIFIZIERT" data-content="NICHT QUALIFIZIERT" data-trigger="hover" data-original-title="NICHT QUALIFIZIERT">`;
                } else {
                    statusIcon.innerHTML = `<img src="${AMP_RED}" alt="NICHT QUALIFIZIERT" data-content="NICHT QUALIFIZIERT" data-trigger="hover" data-original-title="NICHT QUALIFIZIERT">`;
                }
            }

            document.addEventListener('DOMContentLoaded', updateStatusIcon);
            document.addEventListener('input', updateStatusIcon);
            document.addEventListener('change', updateStatusIcon);
        </script>

        <script>
            "use strict";

            $(function () {
                $('#lastname, #name, #street-input, #postal_code-input, #locality-input').on('change', function() {
                    const lastname = $('#lastname').val();
                    const name = $('#name').val();
                    const street = $('#street-input').val();

                    if (!lastname || !name || !street) {
                        return;
                    }

                    $.ajax({
                        url: '/check-new-leads',
                        type: 'GET',
                        data: {
                            lastname: lastname,
                            name: name,
                            street: street
                        },
                        success: function(response) {
                            if (!response || !response.exists) {
                                return;
                            }

                            Swal.fire({
                                title: 'Der Kunde existiert bereits',
                                html: `
                                    <p>Name: ${ocEscape(response.customer_id)}. ${ocEscape(response.customer_name)} ${ocEscape(response.customer_lastname)}</p>
                                    <p>Adresse: ${ocEscape(response.customer_street)}, ${ocEscape(response.customer_postcode)}, ${ocEscape(response.customer_city)}</p>
                                    <p>Eindeutige Adressnummer: ${ocEscape(response.address_no)}</p>
                                    <p>Klicken Sie unten, um das Kundenprofil anzuzeigen.</p>
                                `,
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Profil anzeigen',
                                cancelButtonText: 'Absagen'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `/new_lead_profile/${encodeURIComponent(response.customer_id)}/${encodeURIComponent(response.customer_postcode)}/${encodeURIComponent(response.address_no)}`;
                                }
                            });
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr.responseText || xhr);
                        }
                    });
                });
            });
        </script>

        <script>
            "use strict";

            $(function () {
                $('#customer_form').on('submit', function (e) {
                    e.preventDefault();

                    const $form = $(this);
                    const $submitButtons = $form.find('button[type="submit"]');
                    const formData = new FormData(this);

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
            });
        </script>

        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places&callback=initMayarGoogleMap"
            async
            defer
        ></script>
    @endpush
@endonce