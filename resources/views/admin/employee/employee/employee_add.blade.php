@extends('admin.layouts.app')

@section('title') Employee Details @endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --ep-bg: #f3f4f6;
            --ep-card-bg: #ffffff;
            --ep-border: #e5e7eb;
            --ep-primary: var(--sa-accent);
            --ep-muted: #6b7280;
            --ep-heading: #111827;
            --ep-radius: 16px;
            --ep-shadow: 0 10px 25px rgba(15, 23, 42, 0.07);
        }

        .app-content .content-wrapper {
            background: var(--ep-bg);
        }

        .ep-card {
            border-radius: var(--ep-radius);
            box-shadow: var(--ep-shadow);
            border: 1px solid var(--ep-border);
        }

        .ep-card-header {
            border-bottom: 1px solid var(--ep-border);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ep-card-header h4 {
            margin: 0;
            font-weight: 600;
            color: var(--ep-heading);
        }

        .ep-section-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ep-muted);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ep-section-title::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: var(--ep-primary);
        }

        .ep-divider {
            border-top: 1px dashed var(--ep-border);
            margin: 1.5rem 0;
        }

        .color-strip {
            border: none !important;
            height: 6px !important;
            background: linear-gradient(90deg, #8fc73e, #22c55e, #3b82f6);
            border-radius: 999px;
            margin-bottom: 1.5rem;
        }

        .ep-avatar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .ep-avatar-wrapper img {
            border-radius: 999px;
            box-shadow: 0 0 0 3px rgba(143, 199, 62, 0.35);
            object-fit: cover;
        }

        .ep-avatar-actions .btn {
            width: 100%;
        }

        .color-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #e5e7eb;
            background-color: #ffffff;
            display: inline-block;
        }

        .color-select {
            flex: 1;
        }

        .form-group label {
            font-weight: 500;
            color: var(--ep-muted);
            font-size: 0.85rem;
        }

        .form-control,
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border-radius: 0.6rem !important;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db !important;
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endsection

@section('content')
    <div class="app-content"> 

        <div class="content-wrapper">  
            <div class="content-body">
                <section id="employee-create">
                    <div class="row">
                        <div class="col-12">
                            <div class="card ep-card">
                                <div class="ep-card-header">
                                    <h4>Neuer Mitarbeiter</h4>
                                    <a href="{{ url('/emp') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="feather icon-arrow-left"></i> Zurück zur Liste
                                    </a>
                                </div>

                                <div class="card-body">
                                    <hr class="color-strip" id="colorStrip">

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form novalidate method="POST"
                                          action="{{ route('emp.add')}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            {{-- LEFT COLUMN: Avatar + quick info --}}
                                            <div class="col-lg-3 col-md-4 mb-2">
                                                <div class="ep-avatar-wrapper">
                                                    <div style="position: relative; display: inline-block;">
                                                        <img src="{{ asset('images/gender/male.png') }}"
                                                             class="mr-75"
                                                             id="picture"
                                                             alt="profile image"
                                                             height="180"
                                                             width="180">
                                                        <button type="button"
                                                                class="btn btn-icon rounded-circle btn-outline-danger waves-effect waves-light"
                                                                id="removeButton"
                                                                onclick="removePicture()"
                                                                style="position: absolute; top: 0; right: 0; display: none;">
                                                            <i class="feather icon-trash"></i>
                                                        </button>
                                                    </div>

                                                    <div class="ep-avatar-actions w-100">
                                                        <button type="button"
                                                                class="btn btn-primary mb-1"
                                                                data-toggle="modal"
                                                                data-target="#avatarModal">
                                                            <i class="feather icon-image"></i> Profilbild
                                                        </button>
                                                    </div>

                                                    <small class="text-muted text-center">
                                                        Optimal: quadratisches Bild, kleine Dateigröße.
                                                    </small>
                                                </div>
                                            </div>

                                            {{-- RIGHT COLUMN: Form content --}}
                                            <div class="col-lg-9 col-md-8">
                                                {{-- Persönliche Angaben --}}
                                                <div class="ep-section-title">Persönliche Angaben</div>
                                                <div class="row">
                                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="accountSelect">Titel</label>
                                                            <select class="form-control" id="accountSelect" name="title">
                                                                <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                                                <option value="Ms." {{ old('title') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                                                <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                                                <option value="Pro." {{ old('title') == 'Pro.' ? 'selected' : '' }}>Pro.</option>
                                                            </select>
                                                            @error('title') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="account-username">Name</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   id="account-username"
                                                                   name="name"
                                                                   value="{{ old('name') }}"
                                                                   placeholder="Name"
                                                                   required>
                                                            @error('name') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="middle_name">Zweiter Vorname</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   id="middle_name"
                                                                   name="middle_name"
                                                                   value="{{ old('middle_name') }}"
                                                                   placeholder="Zweiter Vorname">
                                                            @error('middle_name') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="lastname">Familienname</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   id="lastname"
                                                                   name="lastname"
                                                                   value="{{ old('lastname') }}"
                                                                   placeholder="Familienname"
                                                                   required>
                                                            @error('lastname') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="gender">Geschlecht</label>
                                                            <select class="form-control" name="gender" id="gender" onchange="changePicture()">
                                                                <option value="Male" {{ old('gender', 'Male') == 'Male' ? 'selected' : '' }}>Männlich</option>
                                                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Weiblich</option>
                                                            </select>
                                                            @error('gender') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="colorPicker">Farbe</label>
                                                            <div class="color-container">
                                                                <div id="colorIcon" class="color-icon"></div>
                                                                <select id="colorPicker" class="form-control color-select" name="color"></select>
                                                            </div>
                                                            @error('color') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="marital_status">Familienstand</label>
                                                            <select class="form-control" name="marital_status" id="marital_status">
                                                                <option value="" disabled {{ old('marital_status') ? '' : 'selected' }}>Bitte wählen</option>
                                                                <option value="Ledig" {{ old('marital_status') == 'Ledig' ? 'selected' : '' }}>Ledig</option>
                                                                <option value="verheiratet" {{ old('marital_status') == 'verheiratet' ? 'selected' : '' }}>Verheiratet</option>
                                                                <option value="Geschieden" {{ old('marital_status') == 'Geschieden' ? 'selected' : '' }}>Geschieden</option>
                                                                <option value="Witwe" {{ old('marital_status') == 'Witwe' ? 'selected' : '' }}>Witwe</option>
                                                            </select>
                                                            @error('marital_status') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="kids">Kinder</label>
                                                            <select class="form-control" name="kids" id="kids">
                                                                <option value="" disabled {{ old('kids') ? '' : 'selected' }}>Hat der Mitarbeiter Kinder?</option>
                                                                <option value="Yes" {{ old('kids') == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                <option value="No" {{ old('kids') == 'No' ? 'selected' : '' }}>Nein</option>
                                                            </select>
                                                            @error('kids') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ep-divider"></div>

                                                {{-- Staatsangehörigkeit / Sprache / Religion --}}
                                                <div class="ep-section-title">Nationalität & Sprache</div>
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="nationality">Staatsangehörigkeit</label>
                                                            <select class="form-control" id="nationality" name="nationality">
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country->id }}"
                                                                        {{ (int) old('nationality') === $country->id ? 'selected' : '' }}>
                                                                        {{ $country->nationality }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('nationality') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="religion">Konfession</label>
                                                            <select class="form-control" name="religion" id="religion">
                                                                @php
$rel = old('religion');
                                                                @endphp
                                                                <option value="Katholisch" {{ $rel == 'Katholisch' ? 'selected' : '' }}>Katholisch</option>
                                                                <option value="Evangelisch" {{ $rel == 'Evangelisch' ? 'selected' : '' }}>Evangelisch</option>
                                                                <option value="Muslimisch" {{ $rel == 'Muslimisch' ? 'selected' : '' }}>Muslimisch</option>
                                                                <option value="Orthodox" {{ $rel == 'Orthodox' ? 'selected' : '' }}>Orthodox</option>
                                                                <option value="Keine" {{ $rel == 'Keine' ? 'selected' : '' }}>Keine</option>
                                                                <option value="Hinduistisch" {{ $rel == 'Hinduistisch' ? 'selected' : '' }}>Hinduistisch</option>
                                                                <option value="Buddhistisch" {{ $rel == 'Buddhistisch' ? 'selected' : '' }}>Buddhistisch</option>
                                                                <option value="Jüdisch" {{ $rel == 'Jüdisch' ? 'selected' : '' }}>Jüdisch</option>
                                                                <option value="Andere" {{ $rel == 'Andere' ? 'selected' : '' }}>Andere</option>
                                                            </select>
                                                            @error('religion') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="dob">Geburtsdatum</label>
                                                            <input type="date"
                                                                   class="form-control"
                                                                   name="dob"
                                                                   id="dob"
                                                                   value="{{ old('dob') }}">
                                                            @error('dob') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="country">Geburtsort (Land)</label>
                                                            <select class="form-control" id="country" name="country_id">
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country->id }}"
                                                                        {{ (int) old('country_id') === $country->id ? 'selected' : '' }}>
                                                                        {{ $country->country }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('country_id') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="mother_tongue">Muttersprache</label>
                                                            <select class="form-control" id="mother_tongue" name="mother_tongue">
                                                                @foreach ($languages as $lang)
                                                                    <option value="{{ $lang->id }}"
                                                                        {{ (int) old('mother_tongue') === $lang->id ? 'selected' : '' }}>
                                                                        {{ $lang->language }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('mother_tongue') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="language">Weitere Sprachen</label>
                                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#new_lang">
                                                                <i class="feather icon-plus primary"></i> Neu
                                                            </a>
                                                            <select class="form-control" id="language" multiple name="language[]">
                                                                @foreach ($languages as $lang)
                                                                    <option value="{{ $lang->id }}"
                                                                        {{ (collect(old('language', []))->contains($lang->id) ? 'selected' : '') }}>
                                                                        {{ $lang->language }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('language') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ep-divider"></div>

                                                {{-- Aufenthalt & Steuern --}}
                                                <div class="ep-section-title">Aufenthalt & Steuern</div>
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="resident_permit">Aufenthaltstitel bei Ausländer</label>
                                                            <select class="form-control" name="resident_permit" id="resident_permit">
                                                                <option value="" disabled {{ old('resident_permit') ? '' : 'selected' }}>
                                                                    Besitzen Sie eine Aufenthaltserlaubnis?
                                                                </option>
                                                                <option value="Yes" {{ old('resident_permit') == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                <option value="No" {{ old('resident_permit') == 'No' ? 'selected' : '' }}>Nein</option>
                                                            </select>
                                                            @error('resident_permit') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="resident_permit_end_date">Enddatum der Aufenthaltserlaubnis</label>
                                                            <input type="date"
                                                                   class="form-control"
                                                                   name="resident_permit_end_date"
                                                                   id="resident_permit_end_date"
                                                                   value="{{ old('resident_permit_end_date') }}">
                                                            @error('resident_permit_end_date') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="work_permit">Arbeitsberechtigung</label>
                                                            <select class="form-control" name="work_permit" id="work_permit">
                                                                <option value="" disabled {{ old('work_permit') ? '' : 'selected' }}>
                                                                    Besitzen Sie eine Arbeitsberechtigung?
                                                                </option>
                                                                <option value="Yes" {{ old('work_permit') == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                <option value="No" {{ old('work_permit') == 'No' ? 'selected' : '' }}>Nein</option>
                                                            </select>
                                                            @error('work_permit') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="work_permit_end_date">Ende der Arbeitserlaubnis</label>
                                                            <input type="date"
                                                                   class="form-control"
                                                                   name="work_permit_end_date"
                                                                   id="work_permit_end_date"
                                                                   value="{{ old('work_permit_end_date') }}">
                                                            @error('work_permit_end_date') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        @if (!empty($taxes) && count($taxes))
                                                            <div class="form-group">
                                                                <label for="tax_class">Steuerklasse</label>
                                                                <select class="form-control @error('tax_class') is-invalid @enderror"
                                                                        name="tax_class"
                                                                        id="tax_class">
                                                                    <option disabled selected>Bitte wählen...</option>
                                                                    @foreach ($taxes as $tax)
                                                                        <option value="{{ $tax->id }}"
                                                                            {{ (int) old('tax_class') === $tax->id ? 'selected' : '' }}>
                                                                            {{ $tax->tax }}% - {{ $tax->class }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('tax_class') <p class="text-danger">{{ $message }}</p> @enderror
                                                            </div>
                                                        @else
                                                            <a class="btn btn-success col-12" href="{{ url('/tax') }}">Steuerklasse hinzufügen</a>
                                                        @endif
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="tax_id">Steuer-ID</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="tax_id"
                                                                   id="tax_id"
                                                                   value="{{ old('tax_id') }}"
                                                                   placeholder="Steuer-ID">
                                                            @error('tax_id') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="pension_no">RN-Nr.</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="pension_no"
                                                                   id="pension_no"
                                                                   value="{{ old('pension_no') }}"
                                                                   placeholder="Rentenversicherungsnummer">
                                                            @error('pension_no') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="bio">Biografie</label>
                                                            <textarea class="form-control"
                                                                      id="bio"
                                                                      name="bio"
                                                                      rows="3"
                                                                      placeholder="Kurzprofil, Erfahrung, besondere Hinweise...">{{ old('bio') }}</textarea>
                                                            @error('bio') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ep-divider"></div>

                                                {{-- Beschäftigung / Vertrag --}}
                                                <div class="ep-section-title">Vertrag & Beschäftigung</div>
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <label for="branch">Zuständigkeitsbereiche / Zweig</label>
                                                        @if(count($branches))
                                                            <select class="form-control" id="branch" name="branch">
                                                                @foreach ($branches as $bran)
                                                                    <option value="{{ $bran->id }}"
                                                                        {{ (int) old('branch') === $bran->id ? 'selected' : '' }}>
                                                                        {{ $bran->branch }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('branch') <p class="text-danger">{{ $message }}</p> @enderror
                                                        @else
                                                            <a class="btn btn-success col-12" href="{{ url('/branch') }}">Zweig hinzufügen</a>
                                                        @endif
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <label for="contract_type_id">Vertragstyp</label>
                                                        @if(count($contracts))
                                                            <select class="form-control" id="contract_type_id" name="contract_type_id">
                                                                @foreach ($contracts as $contract)
                                                                    <option value="{{ $contract->id }}"
                                                                        {{ (int) old('contract_type_id') === $contract->id ? 'selected' : '' }}>
                                                                        {{ $contract->contract_type }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('contract_type_id') <p class="text-danger">{{ $message }}</p> @enderror
                                                        @else
                                                            <a class="btn btn-success col-12" href="{{ url('/contract_type') }}">Vertragsart hinzufügen</a>
                                                        @endif
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="contract_date">Vertragsdatum</label>
                                                            <input type="date"
                                                                   class="form-control"
                                                                   id="contract_date"
                                                                   name="contract_date"
                                                                   value="{{ old('contract_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                                                   required>
                                                            @error('contract_date') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="working_hour">Standard-Arbeitsstunden / Woche</label>
                                                            <input type="number"
                                                                   class="form-control"
                                                                   id="working_hour"
                                                                   name="working_hour"
                                                                   value="{{ old('working_hour', 40) }}"
                                                                   required>
                                                            @error('working_hour') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="daily_start_time">Arbeitsbeginn (Standard)</label>
                                                            <input type="time"
                                                                   class="form-control"
                                                                   id="daily_start_time"
                                                                   name="daily_start_time"
                                                                   value="{{ old('daily_start_time', '07:30') }}">
                                                            @error('daily_start_time') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="daily_end_time">Arbeitsende (Standard)</label>
                                                            <input type="time"
                                                                   class="form-control"
                                                                   id="daily_end_time"
                                                                   name="daily_end_time"
                                                                   value="{{ old('daily_end_time', '16:00') }}">
                                                            @error('daily_end_time') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <label for="working_type">Arbeitstyp</label>
                                                        <select class="form-control" name="working_type" id="working_type">
                                                            <option value="Weekly" {{ old('working_type', 'Weekly') == 'Weekly' ? 'selected' : '' }}>Woche</option>
                                                            <option value="Monthly" {{ old('working_type') == 'Monthly' ? 'selected' : '' }}>Monatlich</option>
                                                            <option value="Contract Base" {{ old('working_type') == 'Contract Base' ? 'selected' : '' }}>Vertrag</option>
                                                        </select>
                                                        @error('working_type') <p class="text-danger">{{ $message }}</p> @enderror
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="salary_per_hour">Lohn pro Stunde (€)</label>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   class="form-control"
                                                                   id="salary_per_hour"
                                                                   name="salary_per_hour"
                                                                   value="{{ old('salary_per_hour', 20) }}"
                                                                   required>
                                                            @error('salary_per_hour') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="leave">Erlaubter Urlaub (Tage/Jahr)</label>
                                                            <input type="number"
                                                                   class="form-control @error('leave') is-invalid @enderror"
                                                                   id="leave"
                                                                   name="leave"
                                                                   value="{{ old('leave', $leave_day ?? 22) }}"
                                                                   required>
                                                            @error('leave') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="sick_leave">Krankenstand (Tage/Jahr)</label>
                                                            <input type="number"
                                                                   class="form-control"
                                                                   id="sick_leave"
                                                                   name="sick_leave"
                                                                   value="{{ old('sick_leave', 10) }}"
                                                                   required>
                                                            @error('sick_leave') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <label for="supervisor">Supervisor/in</label>
                                                        <select class="form-control" id="supervisor" name="supervisor">
                                                            <option value="">Kein Betreuer/in</option>
                                                            @foreach ($supervisor as $super)
                                                                <option value="{{ $super->id }}"
                                                                    {{ (int) old('supervisor') === $super->id ? 'selected' : '' }}>
                                                                    {{ $super->name }} {{ $super->lastname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('supervisor') <p class="text-danger">{{ $message }}</p> @enderror
                                                    </div>

                                                    {{-- Department & Position --}}
                                                    <div class="col-12 mt-1">
                                                        <label class="ep-section-title mb-0">Abteilung & Positionen</label>
                                                    </div>

                                                    <div id="record_table" class="col-12 d-flex flex-wrap p-0 mt-50">
                                                        <div class="original-record col-12 d-flex flex-wrap p-1 rounded" style="background:#f9fafb;">
                                                            <div class="col-lg-5 col-md-5 col-sm-12">
                                                                <label for="department">Abteilung</label>
                                                                @if(count($departments))
                                                                    <select class="form-control department-select"
                                                                            id="department"
                                                                            name="department[0]"
                                                                            style="width:100%">
                                                                        <option disabled selected>Abteilung auswählen</option>
                                                                        @foreach ($departments as $dept)
                                                                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('department') <p class="text-danger">{{ $message }}</p> @enderror
                                                                @endif
                                                            </div>
                                                            <div class="col-lg-5 col-md-5 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="position">Position(en)</label>
                                                                    <select class="form-control position-select"
                                                                            id="position"
                                                                            name="position[0][]"
                                                                            multiple
                                                                            style="width:100%">
                                                                    </select>
                                                                    @error('position') <p class="text-danger">{{ $message }}</p> @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2 col-md-2 col-sm-12 d-flex align-items-end">
                                                                <button type="button"
                                                                        class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light"
                                                                        id="add_record">
                                                                    <i class="feather icon-plus-square"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ep-divider"></div>

                                                {{-- Auszubildende --}}
                                                <div class="ep-section-title">Auszubildenden-Informationen</div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="trainee">Ist er/sie Auszubildende/r?</label>
                                                            <select class="form-control" name="trainee" id="trainee">
                                                                <option value="" disabled {{ old('trainee') ? '' : 'selected' }}>
                                                                    Wenn dies nicht zutrifft, leer lassen.
                                                                </option>
                                                                <option value="Yes" {{ old('trainee') == 'Yes' ? 'selected' : '' }}>Ja</option>
                                                                <option value="No" {{ old('trainee') == 'No' ? 'selected' : '' }}>Nein</option>
                                                            </select>
                                                            @error('trainee') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="trainee_start_date">Startdatum (falls ja)</label>
                                                            <input type="date"
                                                                   class="form-control"
                                                                   name="trainee_start_date"
                                                                   id="trainee_start_date"
                                                                   value="{{ old('trainee_start_date') }}">
                                                            @error('trainee_start_date') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="trainee_end_date">Enddatum</label>
                                                            <input type="date"
                                                                   class="form-control"
                                                                   name="trainee_end_date"
                                                                   id="trainee_end_date"
                                                                   value="{{ old('trainee_end_date') }}">
                                                            @error('trainee_end_date') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ep-divider"></div>

                                                {{-- Krankenkasse & Bank --}}
                                                <div class="ep-section-title">Krankenkasse & Bankverbindung</div>
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="health_insurance">Krankenversicherung</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="health_insurance"
                                                                   id="health_insurance"
                                                                   value="{{ old('health_insurance') }}"
                                                                   required>
                                                            @error('health_insurance') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="insurance_id">Versicherungsnummer</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="insurance_id"
                                                                   id="insurance_id"
                                                                   value="{{ old('insurance_id') }}"
                                                                   required>
                                                            @error('insurance_id') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="bank_name">Bankname</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="bank_name"
                                                                   id="bank_name"
                                                                   value="{{ old('bank_name') }}"
                                                                   required>
                                                            @error('bank_name') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="iban">IBAN</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="iban"
                                                                   id="iban"
                                                                   value="{{ old('iban') }}"
                                                                   required>
                                                            @error('iban') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ep-divider"></div>

                                                {{-- Kontakt --}}
                                                <div class="ep-section-title">Kontaktdetails</div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="email">E-Mail</label>
                                                            <input type="email"
                                                                   class="form-control"
                                                                   name="email"
                                                                   id="email"
                                                                   value="{{ old('email') }}"
                                                                   required>
                                                            @error('email') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="phone">Mobil</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="phone"
                                                                   id="phone"
                                                                   value="{{ old('phone') }}"
                                                                   required>
                                                            @error('phone') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="home_phone">Privat</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="home_phone"
                                                                   id="home_phone"
                                                                   value="{{ old('home_phone') }}">
                                                            @error('home_phone') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="work_phone">Geschäftlich</label>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="work_phone"
                                                                   id="work_phone"
                                                                   value="{{ old('work_phone') }}">
                                                            @error('work_phone') <p class="text-danger">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr class="mt-2">

                                                <div class="d-flex justify-content-end mt-1">
                                                    <button type="reset" class="btn btn-outline-secondary mr-1">Zurücksetzen</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="feather icon-save mr-50"></i> Speichern
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div> {{-- card-body --}}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- Avatar Modal --}}
        <div class="modal fade text-left" id="avatarModal" tabindex="-1" role="dialog" aria-labelledby="avatarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="avatarModalLabel">Profilbild auswählen</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <fieldset>
                            <div class="form-group">
                                <label>
                                    <code><strong>Hinweis: Große Bilder verlangsamen das System.</strong></code>
                                </label>
                                <input type="file" class="form-control" id="upload" name="image" onchange="previewImage()">
                                @error('image') <p class="text-danger">{{ $message }}</p> @enderror
                            </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary">Schließen</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Language Modal (unchanged – just ensure it exists somewhere in layout) --}}
        {{-- #new_lang modal must be defined in your layout or here --}}
    </div>
@endsection

@section('script')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>

    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>

    <script>
        // Color palette for employees
        const EP_COLORS = [
            '#8fc73e', '#16a34a', '#2563eb', '#6366f1',
            '#f97316', '#eab308', '#ec4899', '#0f766e'
        ];

        function initColorPicker() {
            const select = document.getElementById('colorPicker');
            const icon = document.getElementById('colorIcon');
            const oldColor = @json(old('color'));

            EP_COLORS.forEach(color => {
                const opt = document.createElement('option');
                opt.value = color;
                opt.textContent = color;
                opt.style.backgroundColor = color;
                if (oldColor && oldColor === color) {
                    opt.selected = true;
                    icon.style.backgroundColor = color;
                }
                select.appendChild(opt);
            });

            if (!oldColor) {
                icon.style.backgroundColor = EP_COLORS[0];
                select.value = EP_COLORS[0];
            }

            select.addEventListener('change', function () {
                icon.style.backgroundColor = this.value;
            });
        }

        $(document).ready(function () {
            $('#branch').select2();
            $('#supervisor').select2();
            $('#language').select2();
            $('#country').select2();
            $('#nationality').select2();
            $('#mother_tongue').select2();
            $('#department').select2();
            $('#position').select2();

            initColorPicker();
        });
    </script>

    {{-- Department / Position repeater --}}
    <script>
        let deptIndex = 0;

        function loadPositions($positionsSelect, departmentId) {
            if (!departmentId) return;

            $.ajax({
                url: '/get-positions/' + departmentId,
                type: 'GET',
                success: function (data) {
                    $positionsSelect.empty();
                    $.each(data, function (key, value) {
                        $positionsSelect.append('<option value="' + value.id + '">' + value.position + '</option>');
                    });
                    $positionsSelect.trigger('change');
                }
            });
        }

        $(document).ready(function () {
            // first row department change
            $(document).on('change', '#department', function () {
                const departmentId = $(this).val();
                const $positionsSelect = $('#position');
                loadPositions($positionsSelect, departmentId);
            });

            // add new row
            $('#add_record').on('click', function () {
                deptIndex++;
                const rowHtml = `
                    <div class="col-12 d-flex flex-wrap original-record mt-50 p-1 rounded" style="background:#f9fafb;">
                        <div class="col-lg-5 col-md-5 col-sm-12">
                            <label>Abteilung</label>
                            <select class="form-control department-select" id="department-${deptIndex}" name="department[${deptIndex}]" style="width:100%">
                                <option disabled selected>Abteilung auswählen</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label>Position(en)</label>
                                <select class="form-control position-select" id="position-${deptIndex}" name="position[${deptIndex}][]" multiple style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12 d-flex align-items-end">
                            <button type="button" class="btn btn-icon rounded-circle btn-outline-danger waves-effect waves-light remove_record">
                                <i class="feather icon-minus-square"></i>
                            </button>
                        </div>
                    </div>
                `;

                $('#record_table').append(rowHtml);

                const $deptSelect = $('#department-' + deptIndex);
                const $posSelect  = $('#position-' + deptIndex);

                $deptSelect.select2();
                $posSelect.select2();

                $deptSelect.on('change', function () {
                    const depId = $(this).val();
                    loadPositions($posSelect, depId);
                });
            });

            // remove row
            $(document).on('click', '.remove_record', function () {
                $(this).closest('.original-record').remove();
            });
        });
    </script>

    {{-- Language AJAX – unchanged logic, just cleaner --}}
    <script>
        $(document).ready(function () {
            $('#save-language-button').click(function (e) {
                e.preventDefault();
                const language = $('input[name="language"]').val();

                $.ajax({
                    url: '{{ route("save.language") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        language: language
                    },
                    success: function () {
                        toastr.success("Sprache gespeichert");
                        $('#new_lang').modal('hide');
                        loadLanguages();
                    },
                    error: function () {
                        toastr.error("Fehler: Sprache konnte nicht gespeichert werden");
                    }
                });
            });

            function loadLanguages() {
                $.ajax({
                    url: '{{ route("load.languages") }}',
                    type: 'GET',
                    success: function (response) {
                        const select = $('#language');
                        select.empty();
                        $.each(response, function (index, language) {
                            select.append('<option value="' + language.id + '">' + language.language + '</option>');
                        });
                    },
                    error: function () {
                        toastr.error("Fehler: Sprachen konnten nicht geladen werden");
                    }
                });
            }

            loadLanguages();
        });
    </script>

    {{-- Avatar JS --}}
    <script>
        let hasUploadedImage = false;

        function changePicture() {
            if (!hasUploadedImage) {
                const dropdown = document.getElementById("gender");
                const picture  = document.getElementById("picture");
                let src;

                if (dropdown.value === 'Male') {
                    src = '{{ asset('images/gender/male.png') }}';
                } else {
                    src = '{{ asset('images/gender/female.png') }}';
                }

                picture.src = src;
                document.getElementById("removeButton").style.display = 'none';
            }
        }

        function previewImage() {
            const file = document.getElementById("upload").files[0];
            const reader = new FileReader();

            reader.onloadend = function () {
                const picture = document.getElementById("picture");
                picture.src = reader.result;
                hasUploadedImage = true;
                document.getElementById("removeButton").style.display = 'block';
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function removePicture() {
            const picture = document.getElementById("picture");
            const dropdown = document.getElementById("gender");
            hasUploadedImage = false;

            document.getElementById("upload").value = '';

            if (dropdown.value === 'Male') {
                picture.src = '{{ asset('images/gender/male.png') }}';
            } else {
                picture.src = '{{ asset('images/gender/female.png') }}';
            }
            document.getElementById("removeButton").style.display = 'none';
        }
    </script>

    <script>
        $(document).ready(function () {
            @if(Session::has('update_msg'))
                toastr.success("{{ session('updated_msg') }}");
            @endif

            @if(Session::has('save_msg'))
                toastr.success("{{ session('save_msg') }}");
            @endif

            @if(Session::has('delete_msg'))
                toastr.error("{{ session('delete_msg') }}");
            @endif
        });
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
                url: "{{ url('emp?status_tab=active')}}",
            },
            {
                label: 'Nue Anlegen',
                url: "{{url()->current()}}",
                clickable:false
            }

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush