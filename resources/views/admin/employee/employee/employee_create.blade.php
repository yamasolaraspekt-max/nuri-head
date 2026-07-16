@extends('admin.layouts.app')

@section('title')
Mitarbeiterprofil
@endsection

@php
    use Carbon\Carbon;

    $active = old('active_tab', session('active_tab', 'profile'));

    $employeeFullName = trim(($data->name ?? '') . ' ' . ($data->midname ?? '') . ' ' . ($data->lastname ?? ''));
    $employeeFullName = $employeeFullName !== '' ? $employeeFullName : 'Mitarbeiter #' . ($data->id ?? '');

    $employeeImage = !empty($data->image)
        ? asset('images/employee/' . $data->image)
        : (
            strtolower($data->gender ?? '') === 'male'
                ? asset('images/gender/male.png')
                : asset('images/gender/female.png')
        );

    $selectedLanguages = collect(old('language', isset($emp_language) ? $emp_language->pluck('languages_id')->toArray() : []))
        ->map(fn ($id) => (string) $id)
        ->toArray();

    $formatDate = function ($value) {
        return !empty($value) ? Carbon::parse($value)->format('Y-m-d') : '';
    };

    $tabs = [
        'profile' => [
            'id' => 'account-vertical-general',
            'label' => 'General',
            'icon' => 'feather icon-globe',
        ],
        'department' => [
            'id' => 'account-vertical-department',
            'label' => 'Abteilung & Jobs',
            'icon' => 'fa fa-tree',
        ],
        'time-management' => [
            'id' => 'account-vertical-time-management',
            'label' => 'Arbeitszeit',
            'icon' => 'fa fa-clock-o',
        ],
        'location' => [
            'id' => 'account-vertical-location',
            'label' => 'Standortdienst',
            'icon' => 'fa fa-map-pin',
        ],
        'address' => [
            'id' => 'account-vertical-password',
            'label' => 'Kontakt & Adresse',
            'icon' => 'fa fa-address-card-o',
        ],
        'qualification' => [
            'id' => 'account-vertical-info',
            'label' => 'Qualifikation',
            'icon' => 'fa fa-graduation-cap',
        ],
        'skill' => [
            'id' => 'skill-vertical-tab',
            'label' => 'Fähigkeiten',
            'icon' => 'fa fa-calendar-times-o',
        ],
        'recurring' => [
            'id' => 'recurring-vertical-tab',
            'label' => 'Wiederkehrend',
            'icon' => 'fa fa-calendar-times-o',
        ],
        'leave' => [
            'id' => 'holiday-vertical-tab',
            'label' => 'Urlaub & Abwesenheiten',
            'icon' => 'fa fa-calendar-times-o',
        ],
        'sick' => [
            'id' => 'sick-vertical-tab',
            'label' => 'Krankmeldungen',
            'icon' => 'fa fa-thermometer-full',
        ],
        'handover' => [
            'id' => 'account-vertical-notifications',
            'label' => 'Gegenstände übergeben',
            'icon' => 'feather icon-message-circle',
        ],
        'license' => [
            'id' => 'account-vertical-car',
            'label' => 'Mitarbeiterlizenz',
            'icon' => 'fa fa-car',
        ],
        'cloth' => [
            'id' => 'account-vertical-cloth',
            'label' => 'Kleidungsgröße',
            'icon' => 'fa fa-shirtsinbulk',
        ],
        'document' => [
            'id' => 'account-vertical-document',
            'label' => 'Dokument & Datei',
            'icon' => 'fa fa-upload',
        ],
    ];
@endphp

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}">

    <style>
        :root {
            --emp-bg: #f5f7fb;
            --emp-card: #ffffff;
            --emp-text: #172033;
            --emp-muted: #6b7280;
            --emp-border: #e5e7eb;
            --emp-primary: var(--sa-accent);
            --emp-primary-dark: var(--sa-accent-hover);
            --emp-primary-soft: var(--sa-accent-light);
            --emp-blue: #74b2d4;
            --emp-blue-soft: #eff7fb;
            --emp-danger: #ef4444;
            --emp-danger-soft: #fef2f2;
            --emp-warning-soft: #fffbeb;
            --emp-shadow: 0 18px 45px rgba(15, 23, 42, .08);
            --emp-radius: 18px;
        }

        .emp-page {
            background: var(--emp-bg);
            min-height: calc(100vh - 70px);
            padding: 20px;
            color: var(--emp-text);
        }

        .emp-layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        @media (max-width: 992px) {
            .emp-layout {
                grid-template-columns: 1fr;
            }
        }

        .emp-sidebar,
        .emp-content-card,
        .emp-section {
            background: var(--emp-card);
            border: 1px solid var(--emp-border);
            border-radius: var(--emp-radius);
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        }

        .emp-sidebar {
            position: sticky;
            top: 90px;
            overflow: hidden;
        }

        @media (max-width: 992px) {
            .emp-sidebar {
                position: static;
            }
        }

        .emp-profile-summary {
            padding: 22px 18px;
            text-align: center;
            border-bottom: 1px solid var(--emp-border);
            background: linear-gradient(135deg, #ffffff, #f8fcff);
        }

        .emp-avatar {
            width: 88px;
            height: 88px;
            border-radius: 999px;
            overflow: hidden;
            margin: 0 auto 12px;
            background: #f3f4f6;
            border: 4px solid #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .14);
        }

        .emp-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .emp-name {
            margin: 0;
            font-size: 17px;
            font-weight: 900;
            color: #111827;
            line-height: 1.25;
        }

        .emp-status {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 999px;
            padding: 6px 10px;
            font-weight: 800;
            font-size: 12px;
            background: var(--emp-primary-soft);
            color: #4d7c0f;
        }

        .emp-status.holiday {
            background: var(--emp-warning-soft);
            color: #b45309;
        }

        .emp-photo-btn {
            margin-top: 14px;
            width: 100%;
            border: 0;
            border-radius: 12px;
            background: var(--emp-primary);
            color: #fff;
            font-weight: 900;
            padding: 10px 12px;
            transition: all .2s ease;
        }

        .emp-photo-btn:hover {
            background: var(--emp-primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .emp-tabs {
            padding: 10px;
        }

        .emp-tabs .nav-link {
            border-radius: 12px;
            color: #374151;
            font-weight: 800;
            padding: 11px 12px;
            margin-bottom: 4px;
            transition: all .18s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .emp-tabs .nav-link:hover {
            background: #f9fafb;
            color: #111827;
        }

        .emp-tabs .nav-link.active {
            background: var(--emp-primary);
            color: #fff;
            box-shadow: 0 10px 24px rgba(147, 194, 28, .22);
        }

        .emp-content-card {
            overflow: hidden;
        }

        .emp-content-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--emp-border);
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            align-items: center;
            background: #fff;
        }

        .emp-title {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -.02em;
            color: #111827;
            margin: 0;
        }

        .emp-subtitle {
            color: var(--emp-muted);
            margin-top: 4px;
            font-size: 13px;
        }

        .emp-content-body {
            padding: 18px;
        }

        .emp-section {
            margin-bottom: 16px;
            overflow: hidden;
        }

        .emp-section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--emp-border);
            background: linear-gradient(135deg, #fff, #fbfdff);
        }

        .emp-section-icon {
            width: 38px;
            height: 38px;
            border-radius: 13px;
            background: var(--emp-blue-soft);
            color: var(--emp-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .emp-section-title {
            margin: 0;
            font-weight: 900;
            color: #111827;
            font-size: 16px;
        }

        .emp-section-sub {
            font-size: 12px;
            color: var(--emp-muted);
            margin-top: 2px;
        }

        .emp-section-body {
            padding: 18px;
        }

        .emp-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 14px;
        }

        .emp-col-12 { grid-column: span 12; }
        .emp-col-6 { grid-column: span 6; }
        .emp-col-4 { grid-column: span 4; }
        .emp-col-3 { grid-column: span 3; }
        .emp-col-2 { grid-column: span 2; }

        @media (max-width: 1200px) {
            .emp-col-2,
            .emp-col-3,
            .emp-col-4 {
                grid-column: span 6;
            }
        }

        @media (max-width: 720px) {
            .emp-col-2,
            .emp-col-3,
            .emp-col-4,
            .emp-col-6 {
                grid-column: span 12;
            }
        }

        .emp-field label {
            display: block;
            color: #374151;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-weight: 900;
            margin-bottom: 7px;
        }

        .emp-field .form-control,
        .emp-field select,
        .emp-field textarea {
            border: 1px solid var(--emp-border);
            border-radius: 11px;
            min-height: 42px;
            box-shadow: none;
            color: #111827;
        }

        .emp-field textarea {
            min-height: 105px;
            resize: vertical;
        }

        .emp-field .form-control:focus,
        .emp-field select:focus,
        .emp-field textarea:focus {
            border-color: var(--emp-primary);
            box-shadow: 0 0 0 3px var(--emp-primary-soft);
        }

        .emp-error {
            display: block;
            color: #b91c1c;
            font-size: 12px;
            font-weight: 800;
            margin-top: 6px;
        }

        .emp-save-bar {
            position: sticky;
            bottom: 0;
            z-index: 10;
            margin: 0 -18px -18px;
            padding: 14px 18px;
            background: rgba(255, 255, 255, .95);
            backdrop-filter: blur(8px);
            border-top: 1px solid var(--emp-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .emp-btn {
            border: 0;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            transition: all .18s ease;
        }

        .emp-btn-primary {
            background: var(--emp-primary);
            color: #fff;
            box-shadow: 0 10px 24px rgba(147, 194, 28, .22);
        }

        .emp-btn-primary:hover {
            background: var(--emp-primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .emp-btn-soft {
            background: #fff;
            color: #374151;
            border: 1px solid var(--emp-border);
        }

        .emp-btn-soft:hover {
            background: #f9fafb;
            color: #111827;
        }

        .color-strip {
            width: 100%;
            height: 12px;
            border-radius: 999px;
            background-color: {{ $data->color ?: '#93c21c' }};
            margin-bottom: 16px;
        }

        .color-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px var(--emp-border);
            background-color: {{ $data->color ?: '#93c21c' }};
            flex: 0 0 auto;
        }

        .color-select {
            width: 100%;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid var(--emp-border) !important;
            border-radius: 11px !important;
            min-height: 42px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        .alert ul {
            padding-left: 18px;
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
<div class="app-content">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="emp-page">
                <div class="emp-layout">
                    <aside class="emp-sidebar">
                        <div class="emp-profile-summary">
                            <div class="emp-avatar">
                                <img src="{{ $employeeImage }}" alt="{{ $employeeFullName }}" onerror="this.onerror=null;this.src='{{ asset('images/gender/male.png') }}';">
                            </div>

                            <h3 class="emp-name">{{ $employeeFullName }}</h3>

                            <div class="emp-status {{ $data->status === 'holiday' ? 'holiday' : '' }}">
                                <i class="feather {{ $data->status === 'holiday' ? 'icon-calendar' : 'icon-check-circle' }}"></i>
                                {{ $data->status === 'holiday' ? 'Urlaub' : 'Aktiv' }}
                            </div>

                            @if(!empty($data->status_msg))
                                <div class="text-muted mt-1">{{ $data->status_msg }}</div>
                            @endif

                            <button type="button" class="emp-photo-btn" data-toggle="modal" data-target="#picture">
                                <i class="feather icon-image"></i> Profilbild ändern
                            </button>
                        </div>

                        <ul class="nav nav-pills flex-column emp-tabs">
                            @foreach($tabs as $key => $tab)
                                <li class="nav-item">
                                    <a
                                        class="nav-link {{ $active === $key ? 'active' : '' }}"
                                        id="account-pill-{{ $key }}"
                                        data-toggle="pill"
                                        href="#{{ $tab['id'] }}"
                                        role="tab"
                                        aria-controls="{{ $tab['id'] }}"
                                        aria-selected="{{ $active === $key ? 'true' : 'false' }}"
                                    >
                                        <i class="{{ $tab['icon'] }}"></i>
                                        <span>{{ $tab['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </aside>

                    <main class="emp-content-card">
                        <div class="emp-content-header">
                            <div>
                                <h1 class="emp-title">Mitarbeiterprofil</h1>
                                <div class="emp-subtitle">Stammdaten, Vertrag, Kontakt und interne Angaben von {{ $employeeFullName }} verwalten.</div>
                            </div>
                            <a href="{{ route('emp.info') }}" class="emp-btn emp-btn-soft">
                                <i class="feather icon-arrow-left"></i> Zurück zur Liste
                            </a>
                        </div>

                        <div class="emp-content-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Bitte prüfen Sie die Eingaben:</strong>
                                    <ul class="mt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="tab-content">
                                <div
                                    role="tabpanel"
                                    class="tab-pane fade {{ $active === 'profile' ? 'active show' : '' }}"
                                    id="account-vertical-general"
                                    aria-labelledby="account-pill-profile"
                                >
                                    <form id="employeeProfilePostForm" method="POST" action="{{ route('emp.profile.update') }}" enctype="multipart/form-data">
                                        @csrf

                                        <input type="hidden" name="id" value="{{ $data->id }}">
                                        <input type="hidden" name="active_tab" value="profile">

                                        <div class="color-strip" id="colorStrip"></div>

                                        <section class="emp-section">
                                            <div class="emp-section-head">
                                                <div class="emp-section-icon"><i class="feather icon-user"></i></div>
                                                <div>
                                                    <h2 class="emp-section-title">Persönliche Angaben</h2>
                                                    <div class="emp-section-sub">Name, Sprache, Herkunft und persönliche Daten.</div>
                                                </div>
                                            </div>

                                            <div class="emp-section-body">
                                                <div class="emp-grid">
                                                    <div class="emp-col-2 emp-field">
                                                        <label>Titel</label>
                                                        <select class="form-control js-select2" name="title">
                                                            <option value="">Bitte wählen...</option>
                                                            @foreach(['Mr.', 'Ms.', 'Dr.', 'Pro.'] as $title)
                                                                <option value="{{ $title }}" {{ old('title', $data->title) === $title ? 'selected' : '' }}>{{ $title }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('title') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Name</label>
                                                        <input type="text" class="form-control" name="name" value="{{ old('name', $data->name) }}" placeholder="Name">
                                                        @error('name') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Zweiter Vorname</label>
                                                        <input type="text" class="form-control" name="midname" value="{{ old('midname', $data->midname) }}" placeholder="Optional">
                                                        @error('midname') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-4 emp-field">
                                                        <label>Familienname</label>
                                                        <input type="text" class="form-control" name="lastname" value="{{ old('lastname', $data->lastname) }}" placeholder="Familienname">
                                                        @error('lastname') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Geschlecht</label>
                                                        <select class="form-control js-select2" name="gender">
                                                            <option value="">Bitte wählen...</option>
                                                            <option value="Male" {{ old('gender', $data->gender) === 'Male' ? 'selected' : '' }}>Männlich</option>
                                                            <option value="Female" {{ old('gender', $data->gender) === 'Female' ? 'selected' : '' }}>Weiblich</option>
                                                        </select>
                                                        @error('gender') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Farbe</label>
                                                        <div class="color-container">
                                                            <div id="colorIcon" class="color-icon"></div>
                                                            <select id="colorPicker" class="form-control color-select" name="color"></select>
                                                        </div>
                                                        @error('color') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Familienstand</label>
                                                        <select class="form-control js-select2" name="marital_status">
                                                            <option value="">Bitte wählen...</option>
                                                            @foreach(['Ledig', 'verheiratet', 'Geschieden', 'Witwe'] as $status)
                                                                <option value="{{ $status }}" {{ old('marital_status', $data->marital_status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('marital_status') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Kinder</label>
                                                        <select class="form-control js-select2" name="kids">
                                                            <option value="">Bitte wählen...</option>
                                                            <option value="Yes" {{ old('kids', $data->kids) === 'Yes' ? 'selected' : '' }}>Ja</option>
                                                            <option value="No" {{ old('kids', $data->kids) === 'No' ? 'selected' : '' }}>Nein</option>
                                                        </select>
                                                        @error('kids') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Staatsangehörigkeit</label>
                                                        <select class="form-control js-select2" name="nationality">
                                                            <option value="">Bitte wählen...</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}" {{ (string) old('nationality', $data->nationality) === (string) $country->id ? 'selected' : '' }}>
                                                                    {{ $country->nationality }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('nationality') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Konfession</label>
                                                        <select class="form-control js-select2" name="religion">
                                                            <option value="">Bitte wählen...</option>
                                                            @foreach(['Katholisch', 'Evangelisch', 'Muslimisch', 'Orthodox', 'Keine', 'Hinduistisch', 'Buddhistisch', 'Jüdisch', 'Andere'] as $religion)
                                                                <option value="{{ $religion }}" {{ old('religion', $data->religion) === $religion ? 'selected' : '' }}>{{ $religion }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('religion') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Geburtsdatum</label>
                                                        <input type="date" class="form-control" name="dob" value="{{ old('dob', $formatDate($data->dob)) }}">
                                                        @error('dob') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Geburtsort / Land</label>
                                                        <select class="form-control js-select2" name="country_id">
                                                            <option value="">Bitte wählen...</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}" {{ (string) old('country_id', $data->country_id) === (string) $country->id ? 'selected' : '' }}>
                                                                    {{ $country->country }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('country_id') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Muttersprache</label>
                                                        <select class="form-control js-select2" name="mother_tongue">
                                                            <option value="">Keine Auswahl</option>
                                                            @foreach ($languages as $lang)
                                                                <option value="{{ $lang->id }}" {{ (string) old('mother_tongue', $data->mother_tongue) === (string) $lang->id ? 'selected' : '' }}>
                                                                    {{ $lang->language }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('mother_tongue') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Sprachen</label>
                                                        <select class="form-control js-select2" multiple name="language[]">
                                                            @foreach ($languages as $lang)
                                                                <option value="{{ $lang->id }}" {{ in_array((string) $lang->id, $selectedLanguages, true) ? 'selected' : '' }}>
                                                                    {{ $lang->language }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('language') <span class="emp-error">{{ $message }}</span> @enderror
                                                        @error('language.*') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-12 emp-field">
                                                        <label>Biografie</label>
                                                        <textarea class="form-control" name="bio" placeholder="Ihre Biodaten hier...">{{ old('bio', $data->bio) }}</textarea>
                                                        @error('bio') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section class="emp-section">
                                            <div class="emp-section-head">
                                                <div class="emp-section-icon"><i class="feather icon-briefcase"></i></div>
                                                <div>
                                                    <h2 class="emp-section-title">Vertrag & Arbeitsdaten</h2>
                                                    <div class="emp-section-sub">Filiale, Vertrag, Arbeitszeit, Urlaub und Supervisor.</div>
                                                </div>
                                            </div>

                                            <div class="emp-section-body">
                                                <div class="emp-grid">
                                                    <div class="emp-col-3 emp-field">
                                                        <label>Zuständigkeitsbereich / Zweig</label>
                                                        @if(count($branches))
                                                            <select class="form-control js-select2" name="branch">
                                                                <option value="">Bitte wählen...</option>
                                                                @foreach ($branches as $bran)
                                                                    <option value="{{ $bran->id }}" {{ (string) old('branch', $data->branch) === (string) $bran->id ? 'selected' : '' }}>
                                                                        {{ $bran->branch }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <a class="emp-btn emp-btn-primary w-100" href="{{ url('/branch') }}">Zweig hinzufügen</a>
                                                        @endif
                                                        @error('branch') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Vertragstyp</label>
                                                        @if(count($contracts))
                                                            <select class="form-control js-select2" name="contract_type_id">
                                                                <option value="">Keine Auswahl</option>
                                                                @foreach ($contracts as $contract)
                                                                    <option value="{{ $contract->id }}" {{ (string) old('contract_type_id', $data->contract_type_id) === (string) $contract->id ? 'selected' : '' }}>
                                                                        {{ $contract->contract_type }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <a class="emp-btn emp-btn-primary w-100" href="{{ url('/contract_type') }}">Vertragsart hinzufügen</a>
                                                        @endif
                                                        @error('contract_type_id') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Vertragsdatum</label>
                                                        <input type="date" class="form-control" name="contract_date" value="{{ old('contract_date', $formatDate($data->contract_date)) }}">
                                                        @error('contract_date') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Arbeitsstunden</label>
                                                        <input type="number" step="0.01" class="form-control" name="working_hour" value="{{ old('working_hour', $data->working_hour) }}">
                                                        @error('working_hour') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Arbeitsbeginn</label>
                                                        <input type="time" class="form-control" name="daily_start_time" value="{{ old('daily_start_time', $data->daily_start_time) }}">
                                                        @error('daily_start_time') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Feierabend</label>
                                                        <input type="time" class="form-control" name="daily_end_time" value="{{ old('daily_end_time', $data->daily_end_time) }}">
                                                        @error('daily_end_time') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Vergütungsart</label>
                                                        <select class="form-control js-select2" name="working_type">
                                                            @foreach([
                                                                'monthly' => 'Monatsgehalt',
                                                                'hourly' => 'Stundenlohn',
                                                                'daily' => 'Taglohn',
                                                                'piece_work' => 'Akkordlohn',
                                                                'comission' => 'Provision',
                                                                'mixed' => 'Mischformen',
                                                            ] as $value => $label)
                                                                <option value="{{ $value }}" {{ old('working_type', $data->working_type ?? 'monthly') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('working_type') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Lohn pro Stunde</label>
                                                        <input type="number" step="0.01" class="form-control" name="salary_per_hour" value="{{ old('salary_per_hour', $data->salary_per_hour) }}">
                                                        @error('salary_per_hour') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Urlaubstage</label>
                                                        <input type="number" step="0.01" class="form-control" name="leave" value="{{ old('leave', $data->leave) }}">
                                                        @error('leave') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Krankenstand</label>
                                                        <input type="number" step="0.01" class="form-control" name="sick_leave" value="{{ old('sick_leave', $data->sick_leave) }}">
                                                        @error('sick_leave') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-6 emp-field">
                                                        <label>Supervisor/in</label>
                                                        <select class="form-control js-select2" id="supervisor" name="supervisor">
                                                            <option value="" {{ old('supervisor', $data->supervisor) === null || old('supervisor', $data->supervisor) === '' ? 'selected' : '' }}>
                                                                Kein Betreuer/in
                                                            </option>
                                                            @foreach ($supervisor as $super)
                                                                <option value="{{ $super->id }}" {{ (string) old('supervisor', $data->supervisor) === (string) $super->id ? 'selected' : '' }}>
                                                                    {{ $super->name }} {{ $super->lastname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('supervisor') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section class="emp-section">
                                            <div class="emp-section-head">
                                                <div class="emp-section-icon"><i class="feather icon-file-text"></i></div>
                                                <div>
                                                    <h2 class="emp-section-title">Dokumente & Steuerdaten</h2>
                                                    <div class="emp-section-sub">Aufenthalt, Arbeitserlaubnis, Steuer und Sozialversicherung.</div>
                                                </div>
                                            </div>

                                            <div class="emp-section-body">
                                                <div class="emp-grid">
                                                    <div class="emp-col-3 emp-field">
                                                        <label>Aufenthaltstitel</label>
                                                        <select class="form-control js-select2" name="resident_permit">
                                                            <option value="">Keine Auswahl</option>
                                                            <option value="Yes" {{ old('resident_permit', $data->resident_permit) === 'Yes' ? 'selected' : '' }}>Ja</option>
                                                            <option value="No" {{ old('resident_permit', $data->resident_permit) === 'No' ? 'selected' : '' }}>Nein</option>
                                                        </select>
                                                        @error('resident_permit') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Enddatum Aufenthaltserlaubnis</label>
                                                        <input type="date" class="form-control" name="resident_permit_end_date" value="{{ old('resident_permit_end_date', $formatDate($data->resident_permit_end_date)) }}">
                                                        @error('resident_permit_end_date') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Arbeitsberechtigung</label>
                                                        <select class="form-control js-select2" name="work_permit">
                                                            <option value="">Keine Auswahl</option>
                                                            <option value="Yes" {{ old('work_permit', $data->work_permit) === 'Yes' ? 'selected' : '' }}>Ja</option>
                                                            <option value="No" {{ old('work_permit', $data->work_permit) === 'No' ? 'selected' : '' }}>Nein</option>
                                                        </select>
                                                        @error('work_permit') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Ende Arbeitserlaubnis</label>
                                                        <input type="date" class="form-control" name="work_permit_end_date" value="{{ old('work_permit_end_date', $formatDate($data->work_permit_end_date)) }}">
                                                        @error('work_permit_end_date') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Steuerklasse</label>
                                                        @if(count($taxes))
                                                            <select class="form-control js-select2" name="tax_class">
                                                                <option value="">Keine Auswahl</option>
                                                                @foreach ($taxes as $tax)
                                                                    <option value="{{ $tax->id }}" {{ (string) old('tax_class', $data->tax_class) === (string) $tax->id ? 'selected' : '' }}>
                                                                        {{ $tax->tax }}% - {{ $tax->class }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <a class="emp-btn emp-btn-primary w-100" href="{{ url('/tax') }}">Steuerklasse hinzufügen</a>
                                                        @endif
                                                        @error('tax_class') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Steuer ID</label>
                                                        <input type="text" class="form-control" name="tax_id" value="{{ old('tax_id', $data->tax_id) }}">
                                                        @error('tax_id') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>RN-Nr.</label>
                                                        <input type="text" class="form-control" name="pension_no" value="{{ old('pension_no', $data->pension_no) }}">
                                                        @error('pension_no') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Krankenkasse</label>
                                                        <input type="text" class="form-control" name="health_insurance" value="{{ old('health_insurance', $data->health_insurance) }}">
                                                        @error('health_insurance') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Versicherungsnummer</label>
                                                        <input type="text" class="form-control" name="insurance_id" value="{{ old('insurance_id', $data->insurance_id) }}">
                                                        @error('insurance_id') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Bank Name</label>
                                                        <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name', $data->bank_name) }}">
                                                        @error('bank_name') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-6 emp-field">
                                                        <label>IBAN</label>
                                                        <input type="text" class="form-control" name="iban" value="{{ old('iban', $data->iban) }}">
                                                        @error('iban') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section class="emp-section">
                                            <div class="emp-section-head">
                                                <div class="emp-section-icon"><i class="feather icon-award"></i></div>
                                                <div>
                                                    <h2 class="emp-section-title">Auszubildenden-Informationen</h2>
                                                    <div class="emp-section-sub">Befristeter Vertrag oder Ausbildung.</div>
                                                </div>
                                            </div>

                                            <div class="emp-section-body">
                                                <div class="emp-grid">
                                                    <div class="emp-col-6 emp-field">
                                                        <label>Auszubildende/r oder befristeter Arbeitsvertrag?</label>
                                                        <select class="form-control js-select2" name="trainee">
                                                            <option value="" {{ old('trainee', $data->trainee) === null || old('trainee', $data->trainee) === '' ? 'selected' : '' }}>Keine Auswahl</option>
                                                            <option value="Yes" {{ old('trainee', $data->trainee) === 'Yes' ? 'selected' : '' }}>Ja</option>
                                                            <option value="No" {{ old('trainee', $data->trainee) === 'No' ? 'selected' : '' }}>Nein</option>
                                                        </select>
                                                        @error('trainee') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Startdatum</label>
                                                        <input type="date" class="form-control" name="trainee_start_date" value="{{ old('trainee_start_date', $formatDate($data->trainee_start_date)) }}">
                                                        @error('trainee_start_date') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-3 emp-field">
                                                        <label>Enddatum</label>
                                                        <input type="date" class="form-control" name="trainee_end_date" value="{{ old('trainee_end_date', $formatDate($data->trainee_end_date)) }}">
                                                        @error('trainee_end_date') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section class="emp-section">
                                            <div class="emp-section-head">
                                                <div class="emp-section-icon"><i class="feather icon-phone"></i></div>
                                                <div>
                                                    <h2 class="emp-section-title">Kontaktdaten</h2>
                                                    <div class="emp-section-sub">E-Mail, Telefon und interne Kontakte.</div>
                                                </div>
                                            </div>

                                            <div class="emp-section-body">
                                                <div class="emp-grid">
                                                    <div class="emp-col-6 emp-field">
                                                        <label>E-Mail</label>
                                                        <input type="email" class="form-control" name="email" value="{{ old('email', $data->email) }}">
                                                        @error('email') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-6 emp-field">
                                                        <label>Telefon</label>
                                                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $data->phone) }}">
                                                        @error('phone') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-6 emp-field">
                                                        <label>Privatkontakt</label>
                                                        <input type="text" class="form-control" name="home_phone" value="{{ old('home_phone', $data->home_phone) }}">
                                                        @error('home_phone') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="emp-col-6 emp-field">
                                                        <label>Arbeitskontakt</label>
                                                        <input type="text" class="form-control" name="work_phone" value="{{ old('work_phone', $data->work_phone) }}">
                                                        @error('work_phone') <span class="emp-error">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <div class="emp-save-bar">
                                            <button type="reset" class="emp-btn emp-btn-soft">
                                                <i class="feather icon-rotate-ccw"></i> Zurücksetzen
                                            </button>
                                            <button type="submit" class="emp-btn emp-btn-primary">
                                                <i class="feather icon-save"></i> Speichern
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade {{ $active === 'department' ? 'active show' : '' }}" id="account-vertical-department" role="tabpanel" aria-labelledby="account-pill-department">
                                    @include('admin.employee.employee.create.department')
                                </div>

                                <div class="tab-pane fade {{ $active === 'time-management' ? 'active show' : '' }}" id="account-vertical-time-management" role="tabpanel" aria-labelledby="account-pill-time-management">
                                    @php $employeeId = $data->id; @endphp
                                    @include('admin.employee.employee.create.time_management', ['employeeId' => $employeeId])
                                </div>

                                <div class="tab-pane fade {{ $active === 'location' ? 'active show' : '' }}" id="account-vertical-location" role="tabpanel" aria-labelledby="account-pill-location">
                                    @include('admin.employee.employee.create.location')
                                </div>

                                <div class="tab-pane fade {{ $active === 'address' ? 'active show' : '' }}" id="account-vertical-password" role="tabpanel" aria-labelledby="account-pill-address">
                                    @include('admin.employee.employee.create.address')
                                </div>

                                <div class="tab-pane fade {{ $active === 'qualification' ? 'active show' : '' }}" id="account-vertical-info" role="tabpanel" aria-labelledby="account-pill-qualification">
                                    @include('admin.employee.employee.create.qualification')
                                </div>

                                <div class="tab-pane fade {{ $active === 'skill' ? 'active show' : '' }}" id="skill-vertical-tab" role="tabpanel" aria-labelledby="account-pill-skill">
                                    @include('admin.employee.employee.create.skills')
                                </div>

                                <div class="tab-pane fade {{ $active === 'recurring' ? 'active show' : '' }}" id="recurring-vertical-tab" role="tabpanel" aria-labelledby="account-pill-recurring">
                                    @include('admin.employee.employee.create.recurring')
                                </div>

                                <div class="tab-pane fade {{ $active === 'leave' ? 'active show' : '' }}" id="holiday-vertical-tab" role="tabpanel" aria-labelledby="account-pill-leave">
                                    @include('admin.employee.employee.create.leave')
                                </div>

                                <div class="tab-pane fade {{ $active === 'sick' ? 'active show' : '' }}" id="sick-vertical-tab" role="tabpanel" aria-labelledby="account-pill-sick">
                                    @include('admin.employee.employee.create.sick')
                                </div>

                                <div class="tab-pane fade {{ $active === 'handover' ? 'active show' : '' }}" id="account-vertical-notifications" role="tabpanel" aria-labelledby="account-pill-handover">
                                    @include('admin.employee.employee.create.handover')
                                </div>

                                <div class="tab-pane fade {{ $active === 'license' ? 'active show' : '' }}" id="account-vertical-car" role="tabpanel" aria-labelledby="account-pill-license">
                                    @include('admin.employee.employee.create.license')
                                </div>

                                <div class="tab-pane fade {{ $active === 'cloth' ? 'active show' : '' }}" id="account-vertical-cloth" role="tabpanel" aria-labelledby="account-pill-cloth">
                                    @include('admin.employee.employee.create.cloth')
                                </div>

                                <div class="tab-pane fade {{ $active === 'document' ? 'active show' : '' }}" id="account-vertical-document" role="tabpanel" aria-labelledby="account-pill-document">
                                    @include('admin.employee.employee.create.documents')
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>

            <div class="modal fade text-left" id="picture" tabindex="-1" role="dialog" aria-labelledby="pictureTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="pictureTitle">Profilbild</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('emp.profile.picture') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-warning mb-2">
                                    Die Bildgröße wirkt sich auf Ladezeit und Speicherverbrauch aus.
                                </div>

                                <input type="hidden" name="id" value="{{ $data->id }}">

                                <div class="form-group">
                                    <label>Profilbild wählen</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" required>
                                    @error('image') <span class="emp-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="emp-btn emp-btn-soft" data-dismiss="modal">Abbrechen</button>
                                <button type="submit" class="emp-btn emp-btn-primary">Einreichen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>

    <script>
        $(document).ready(function () {
            if ($.fn.select2) {
                $('.js-select2').select2({
                    width: '100%',
                    allowClear: true
                });

                $('#leave_duration').select2({
                    width: '100%',
                    tags: true
                });
            }

            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000,
                    extendedTimeOut: 2000,
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    positionClass: "toast-top-right"
                };

                @if(Session::has('update_msg'))
                    toastr.success(@json(session('update_msg')));
                @endif

                @if(Session::has('save_msg'))
                    toastr.success(@json(session('save_msg')));
                @endif

                @if(Session::has('delete_msg'))
                    toastr.error(@json(session('delete_msg')));
                @endif
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            const colors = [
                { hex: "006139", name: "Dunkelgrün" },
                { hex: "009640", name: "Grün" },
                { hex: "8abd24", name: "Hellgrün" },
                { hex: "838b2d", name: "Oliv" },
                { hex: "583c7a", name: "Lila" },
                { hex: "891e82", name: "Dunkellila" },
                { hex: "d5007f", name: "Magenta" },
                { hex: "e78cba", name: "Rosa" },
                { hex: "cd1719", name: "Rot" },
                { hex: "e55c70", name: "Hellrot" },
                { hex: "e9500e", name: "Orange" },
                { hex: "ef9500", name: "Hellorange" },
                { hex: "283583", name: "Dunkelblau" },
                { hex: "0070ba", name: "Blau" },
                { hex: "009fe3", name: "Himmelblau" },
                { hex: "71cbf4", name: "Hellblau" },
                { hex: "7d91c9", name: "Grau-Blau" },
                { hex: "009bb1", name: "Türkis" },
                { hex: "4b5320", name: "Moosgrün" },
                { hex: "006400", name: "Dunkles Waldgrün" },
                { hex: "a3d900", name: "Neon-Grün" },
                { hex: "ff1493", name: "Neonpink" },
                { hex: "800000", name: "Kastanienbraun" },
                { hex: "8b0000", name: "Dunkelrot" },
                { hex: "ff4500", name: "Feuerrot" },
                { hex: "ff8c00", name: "Dunkelorange" },
                { hex: "ffd700", name: "Gold" },
                { hex: "ffff00", name: "Gelb" },
                { hex: "c0c0c0", name: "Silber" },
                { hex: "808080", name: "Grau" },
                { hex: "000000", name: "Schwarz" },
                { hex: "ffffff", name: "Weiß" },
                { hex: "8b4513", name: "Schokoladenbraun" },
                { hex: "a52a2a", name: "Braun" },
                { hex: "ffdab9", name: "Pfirsich" },
                { hex: "40e0d0", name: "Türkisblau" }
            ];

            const colorPicker = document.getElementById("colorPicker");
            const colorStrip = document.getElementById("colorStrip");
            const colorIcon = document.getElementById("colorIcon");
            const selectedColor = @json(old('color', $data->color ?: '#93c21c'));

            if (!colorPicker || !colorStrip || !colorIcon) {
                return;
            }

            colors.forEach(function (color) {
                const option = document.createElement("option");
                option.value = `#${color.hex}`;
                option.textContent = color.name;

                if (`#${color.hex}`.toLowerCase() === String(selectedColor).toLowerCase()) {
                    option.selected = true;
                    colorStrip.style.backgroundColor = `#${color.hex}`;
                    colorIcon.style.backgroundColor = `#${color.hex}`;
                }

                colorPicker.appendChild(option);
            });

            if (!colorPicker.value && selectedColor) {
                colorPicker.value = selectedColor;
                colorStrip.style.backgroundColor = selectedColor;
                colorIcon.style.backgroundColor = selectedColor;
            }

            colorPicker.addEventListener("change", function () {
                colorStrip.style.backgroundColor = this.value;
                colorIcon.style.backgroundColor = this.value;
            });
        });

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
                label: 'Profil',
                url: "{{ url()->current() }}",
                clickable: false
            },
            {
                label: @json($employeeFullName),
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endsection
