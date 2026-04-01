{{-- resources/views/admin/maintenance/wizard.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Wartungsvertrag anlegen')

@section('style')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

    <style>
        /* ============================================================
           ENTERPRISE PALETTE (White/Black + Requested Accents)
           ============================================================ */
        :root{
            --c-black: #0b0f14;
            --c-black-2:#111827;
            --c-white: #ffffff;

            --c-primary: #74b2d4;   /* requested */
            --c-primary-soft:#c0d8ea;/* requested */
            --c-lime: #93c21c;      /* requested */
            --c-lime-soft:#cfe09b;  /* requested */
            --c-ice: #e3effb;       /* requested */

            /* text */
            --t-strong:#0b0f14;
            --t-main:#111827;
            --t-muted:#334155;

            /* borders/shadows */
            --b-strong: rgba(15,23,42,.20);
            --b-soft: rgba(15,23,42,.12);
            --shadow: 0 24px 70px rgba(0,0,0,.35);

            --radius-lg: 18px;
            --radius-xl: 24px;
        }

        /* ============================================================
           Layout
           ============================================================ */
        .mw-shell{
            border-radius: var(--radius-xl);
            border: 1px solid rgba(255,255,255,.10);
            overflow:hidden;
          
        }

        .mw-shell-header{
            padding:18px 18px;
            display:flex;
            gap:16px;
            align-items:flex-start;
            justify-content:space-between;
            border-bottom:1px solid rgba(255,255,255,.10);
            background:
                linear-gradient(120deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
        }

        .mw-shell-title{display:flex; gap:12px; align-items:flex-start;}

        .mw-logo-pill{
            width:44px;height:44px;border-radius:16px;
            display:flex;align-items:center;justify-content:center;
            color: var(--c-white);
            background:
                radial-gradient(circle at 0% 0%, rgba(116,178,212,.35), transparent 60%),
                radial-gradient(circle at 100% 100%, rgba(147,194,28,.30), transparent 60%),
                rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.14);
        }

        .mw-title-main{
            font-size:1.08rem;
            font-weight:800;
            letter-spacing:-.02em;
            color: var(--c-white);
            line-height:1.2;
        }

        .mw-title-sub{
            margin-top:4px;
            font-size:.82rem;
            color: rgba(255,255,255,.82);
            max-width:760px;
            line-height:1.45;
        }

        .mw-shell-meta{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
            justify-content:flex-end;
        }

        .mw-pill{
            display:inline-flex;
            gap:8px;
            align-items:center;
            padding:7px 12px;
            border-radius:999px;
            font-size:.74rem;
            font-weight:700;
            color: rgba(255,255,255,.92);
            border:1px solid rgba(255,255,255,.14);
            background: rgba(255,255,255,.06);
        }

        .mw-page{padding:18px 16px 26px;}
        .mw-container{max-width:1140px;margin:0 auto;}

        .mw-grid-2{display:grid;grid-template-columns:1.35fr 1fr;gap:16px;}
        .mw-grid-3{display:grid;grid-template-columns:repeat(3, minmax(0, 1fr));gap:12px;}
        .mw-grid-2-compact{display:grid;grid-template-columns:repeat(2, minmax(0, 1fr));gap:12px;}
        @media (max-width: 1023px){.mw-grid-2,.mw-grid-3{grid-template-columns:1fr;}}
        @media (max-width: 640px){.mw-grid-2-compact{grid-template-columns:1fr;}}

        /* ============================================================
           Cards (clear text)
           ============================================================ */
        .mw-card{
            background: var(--c-white);
            border-radius: var(--radius-lg);
            border:1px solid var(--b-strong);
            padding:18px;
        }
        .mw-card-soft{
            border-color: var(--b-soft);
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }
        .mw-card-header{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
        .mw-card-icon{
            width:34px;height:34px;border-radius:999px;
            display:flex;align-items:center;justify-content:center;
            font-size:.92rem;border:1px solid var(--b-soft);
        }
        .mw-icon-primary{background: var(--c-ice); color: #0a4d6a;}
        .mw-icon-lime{background: rgba(207,224,155,.55); color: #3a5f00;}
        .mw-icon-ice{background: var(--c-ice); color: #0f172a;}

        .mw-card-title{
            font-size:.95rem;
            font-weight:900;
            color: var(--t-strong);
            line-height:1.15;
        }
        .mw-card-subtitle{
            font-size:.78rem;
            color: var(--t-muted);
            margin-top:3px;
            line-height:1.45;
        }

        /* ============================================================
           Alerts (enterprise/clean)
           ============================================================ */
        .mw-alert{
            border-radius:14px;
            padding:12px 14px;
            border:1px solid;
            font-size:.80rem;
            line-height:1.45;
            color: var(--t-main);
            background: var(--c-ice);
            border-color: rgba(116,178,212,.45);
        }
        .mw-alert-danger{background:#fff1f2;border-color:#fecdd3;color:#7f1d1d;}
        .mw-alert-success{background:#ecfdf5;border-color:#bbf7d0;color:#14532d;}
        .mw-alert ul{margin:6px 0 0; padding-left:18px;}

        /* ============================================================
           Stepper
           ============================================================ */
        .mw-stepper-shell{margin:14px 0 18px;}
        .mw-stepper{display:flex;flex-direction:column;gap:10px;}
        @media (min-width: 768px){.mw-stepper{flex-direction:row;align-items:flex-start;gap:10px;}}

        .mw-step{
            display:flex;align-items:center;gap:10px;
            opacity:.55;
            transition:opacity .18s ease,transform .18s ease;
        }
        .mw-step.is-active{opacity:1;transform:translateY(-1px);}

        .mw-step-circle{
            width:34px;height:34px;border-radius:999px;
            display:flex;align-items:center;justify-content:center;
            font-size:.82rem;font-weight:900;
            border:1px solid rgba(255,255,255,.22);
            background: rgba(255,255,255,.10);
            color: rgba(255,255,255,.90);
        }
        .mw-step.is-active .mw-step-circle{
            border-color: rgba(255,255,255,.25);
            background: var(--c-primary);
            color: var(--c-black);
        }

        .mw-step-label-main{
            font-size:.70rem;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.09em;
            color: #93c21c;
        }
        .mw-step-label-sub{
            font-size:.75rem;
            color: #93c21c;
            margin-top:2px;
            max-width:360px;
            line-height:1.45;
        }

        /* ============================================================
           Forms (clear + high contrast)
           ============================================================ */
        .mw-label{
            display:block;
            font-size:.74rem;
            font-weight:900;
            color: var(--t-strong);
            margin-bottom:4px;
        }
        .mw-label .required{color:#b91c1c;}

        .mw-input,.mw-select,.mw-textarea{
            width:100%;
            box-sizing:border-box;
            border-radius:12px;
            border:1px solid rgba(15,23,42,.25);
            background:#fff;
            padding:10px 11px;
            font-size:.82rem;
            color: var(--t-main);
            outline:none;
            transition:border-color .15s ease, box-shadow .15s ease;
        }
        .mw-input:focus,.mw-select:focus,.mw-textarea:focus{
            border-color: rgba(116,178,212,.95);
            box-shadow:0 0 0 3px rgba(116,178,212,.25);
        }
        .mw-textarea{min-height:92px; resize:vertical;}
        .mw-help{font-size:.74rem;color: var(--t-muted);margin-top:4px;line-height:1.45;}
        .mw-field-error{margin-top:6px;font-size:.74rem;color:#b91c1c;font-weight:700;}
        .mw-input.is-invalid,.mw-select.is-invalid,.mw-textarea.is-invalid{
            border-color:#fb7185;
            box-shadow:0 0 0 3px rgba(251,113,133,.18);
        }

        /* ============================================================
           Buttons (primary: #74b2d4, success-accent: #93c21c)
           ============================================================ */
        .mw-actions{display:flex;justify-content:space-between;gap:10px;margin-top:16px;flex-wrap:wrap;}

        .mw-btn{
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            border-radius:999px;
            border:1px solid transparent;
            padding:10px 18px;
            font-size:.82rem;
            font-weight:900;
            cursor:pointer;
            white-space:nowrap;
            transition:transform .08s ease, filter .12s ease, background .12s ease, border-color .12s ease;
        }
        .mw-btn:active{transform:translateY(1px);}
        .mw-btn-primary{background: var(--c-primary); color: var(--c-black);}
        .mw-btn-primary:hover{filter:brightness(1.03);}
        .mw-btn-secondary{background:#fff;border-color: rgba(15,23,42,.25);color: var(--c-black);}
        .mw-btn-secondary:hover{background: var(--c-ice);}
        .mw-btn-ghost{background:transparent;border-color: rgba(15,23,42,.25);color: var(--t-muted);}
        .mw-btn-ghost:hover{background: var(--c-ice);color: var(--c-black);}

        .mw-btn-success{background: var(--c-lime); color: var(--c-black);}
        .mw-btn-success:hover{filter:brightness(1.03);}

        .mw-btn[disabled]{opacity:.6;cursor:not-allowed;}
        .mw-btn .mw-spinner{display:none;}
        .mw-btn.is-loading .mw-spinner{display:inline-block;}
        .mw-btn.is-loading .mw-btn-text{opacity:.9;}

        /* Chips */
        .mw-chip-row{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;}
        .mw-chip{
            border-radius:999px;
            border:1px solid rgba(15,23,42,.20);
            padding:4px 8px;
            font-size:.72rem;
            color: var(--t-main);
            background: var(--c-ice);
            font-weight:700;
        }

        /* Select2 polish */
        .select2-container--default .select2-selection--single{
            border-radius:12px;
            border:1px solid rgba(15,23,42,.25);
            height:40px;
            padding:4px 6px;
            font-size:.82rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:30px;color:var(--t-main);}
        .select2-container--default .select2-selection--single .select2-selection__arrow{height:34px;}

        .mw-object-result{display:grid;gap:2px;font-size:.80rem;}
        .mw-object-result-main{font-weight:900;color:var(--t-strong);display:flex;align-items:center;gap:8px;}
        .mw-object-result-kno{
            font-size:.70rem;
            padding:2px 8px;
            border-radius:999px;
            border:1px solid rgba(15,23,42,.20);
            background: var(--c-ice);
            font-weight:900;
            color: var(--t-main);
        }
        .mw-object-result-sub,.mw-object-result-product{display:flex;align-items:center;gap:6px;color:var(--t-muted);}
        .mw-object-result-icon{width:14px;text-align:center;}
        .mw-object-result-badge{
            display:inline-flex;align-items:center;
            padding:0 8px;
            border-radius:999px;
            font-size:.70rem;
            border:1px solid rgba(15,23,42,.18);
            margin-left:6px;
            background: rgba(192,216,234,.55);
            font-weight:900;
            color: var(--t-strong);
        }

        /* Wizard */
        .wizard-step{display:none;}
        .wizard-step.active{display:block;}

        .mw-summary{font-size:.80rem;display:grid;gap:8px;color:var(--t-main);}
        .mw-summary h4{margin:0;font-size:.84rem;font-weight:900;color:var(--t-strong);}
        .mw-summary .row{display:grid;gap:5px;}
        .mw-summary .muted{color:var(--t-muted);font-weight:800;}
    </style>
@endsection

@section('content')
    @php
        $storeAction = \Illuminate\Support\Facades\Route::has('admin.maintenance.contracts.store_simple')
            ? route('admin.maintenance.contracts.store_simple')
            : route('admin.maintenance.contracts.store');
    @endphp

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Wartungsvertrag anlegen</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/personal/task/' . auth()->user()->name) }}">Aufgabeliste</a></li>
                            <li class="breadcrumb-item active">Neuer Wartungsvertrag</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <div class="mw-container">
                    <div class="mw-shell">  
                        <div class="mw-page">
                            @if($errors->any())
                                <div class="mw-alert mw-alert-danger" style="margin-bottom:14px;">
                                    <strong>Speichern nicht möglich.</strong>
                                    <div>Bitte die markierten Felder prüfen.</div>
                                    <ul>
                                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($lead) || isset($alternative) || isset($asset))
                                <div class="mw-card mw-card-soft" style="margin-bottom:14px;">
                                    <div class="mw-card-header">
                                        <div class="mw-card-icon mw-icon-lime"><i class="fa-solid fa-circle-info"></i></div>
                                        <div>
                                            <div class="mw-card-title">Kontext (optional)</div>
                                            <div class="mw-card-subtitle">Vorbelegte Daten aus Lead/Adresse/Asset.</div>
                                        </div>
                                    </div>

                                    <div style="display:grid;gap:6px;">
                                        @if(isset($lead))
                                            <div style="font-size:.82rem;color:var(--t-main);">
                                                <strong style="color:var(--t-strong);">Kunde:</strong>
                                                {{ $lead->firma ?: trim(($lead->name ?? '').' '.($lead->lastname ?? '')) }}
                                                <span style="color:var(--t-muted);">· {{ $lead->street }} · {{ $lead->postcode }} {{ $lead->city }}</span>
                                            </div>
                                        @endif
                                        @if(isset($alternative))
                                            <div style="font-size:.82rem;color:var(--t-main);">
                                                <strong style="color:var(--t-strong);">Objekt:</strong>
                                                {{ $alternative->object_name ?: 'Alternative Adresse' }}
                                                <span style="color:var(--t-muted);">· {{ $alternative->street }} · {{ $alternative->postcode }} {{ $alternative->city }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mw-chip-row">
                                        @if(isset($lead)) <div class="mw-chip">Lead-ID: {{ $lead->id }}</div> @endif
                                        @if(isset($alternative)) <div class="mw-chip">Adresse-ID: {{ $alternative->id }}</div> @endif
                                        @if(isset($asset)) <div class="mw-chip">Asset-ID: {{ $asset->id }}</div> @endif
                                    </div>
                                </div>
                            @endif

                            <div class="mw-stepper-shell">
                                <div class="mw-stepper">
                                    <div class="mw-step is-active" data-step-indicator="1">
                                        <div class="mw-step-circle">1</div>
                                        <div>
                                            <div class="mw-step-label-main">Kunde & Anlage</div>
                                            <div class="mw-step-label-sub">Objekt/Produkt & Filiale wählen. Basisdaten werden übernommen.</div>
                                        </div>
                                    </div>

                                    <div class="mw-step" data-step-indicator="2">
                                        <div class="mw-step-circle">2</div>
                                        <div>
                                            <div class="mw-step-label-main">Vertrag</div>
                                            <div class="mw-step-label-sub">Laufzeit, Preis, Verantwortliche Person und Notizen.</div>
                                        </div>
                                    </div>

                                    <div class="mw-step" data-step-indicator="3">
                                        <div class="mw-step-circle">3</div>
                                        <div>
                                            <div class="mw-step-label-main">Checkliste & Techniker</div>
                                            <div class="mw-step-label-sub">Checkliste zuweisen + Haupt-/Zusatztechniker einplanen.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <form id="wizard-form" method="POST" action="{{ $storeAction }}" novalidate>
                                @csrf

                                <input type="hidden" id="mw-customer-id" name="lead_id" value="{{ old('lead_id', $lead->id ?? '') }}">
                                <input type="hidden" id="mw-alternative-id" name="alternative_id" value="{{ old('alternative_id', $alternative->id ?? '') }}">
                                <input type="hidden" id="mw-product-id" name="product_id" value="{{ old('product_id', $asset->product_id ?? '') }}">
                                <input type="hidden" id="mw-branch-id" name="branch_id" value="{{ old('branch_id') }}">
                                <input type="hidden"  id="mw-wizard-payload"  name="wizard_payload" value="{{ old('wizard_payload') }}">
                                <input type="hidden" id="mw-asset-id" name="asset_id" value="{{ old('asset_id', $asset->id ?? '') }}">
                                <input type="hidden" id="mw-lead-product-list-id" name="lead_product_list_id" value="{{ old('lead_product_list_id') }}">


                                {{-- Step 1 --}}
                                <section class="wizard-step active" data-step="1">
                                    <div class="mw-grid-2">
                                        <div>
                                            <div class="mw-card">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-lime"><i class="fa-solid fa-house-circle-check"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Kunde, Objekt & Anlage</div>
                                                        <div class="mw-card-subtitle">Schnelle Suche nach Kunde/Adresse/Produkt. Pflichtfelder sind erforderlich.</div>
                                                    </div>
                                                </div>

                                                <div style="margin-bottom:10px;">
                                                    <label class="mw-label">Kunde / Objekt / Produkt <span class="required">*</span></label>
                                                    <select id="mw-customer-search" class="mw-object-search-select" style="width:100%;"></select>
                                                    <div class="mw-help">Mindestens 2 Zeichen eingeben (Name, Kundennr., Adresse oder Produkt).</div>
                                                    <div class="mw-field-error" id="mw-err-customer" style="display:none;"></div>
                                                </div>

                                                <div class="mw-grid-2-compact" style="margin-top:10px;">
                                                    <div>
                                                        <label class="mw-label">Anlagentyp (intern)</label>
                                                        <input type="hidden" id="mw-system-type-code" name="systemType" value="{{ old('systemType') }}">
                                                        <input type="text" id="mw-system-type" class="mw-input" placeholder="Wird aus dem Produkt übernommen" readonly>
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Anlagentyp (Klartext)</label>
                                                        <input type="text" id="mw-system-type-label" name="systemTypeLabel" class="mw-input"
                                                               value="{{ old('systemTypeLabel') }}"
                                                               placeholder="z. B. Luft-Wasser-Wärmepumpe">
                                                        @error('systemTypeLabel') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Produkt / Modell <span class="required">*</span></label>
                                                        <input type="text" id="mw-product-model" name="productModel" class="mw-input"
                                                               value="{{ old('productModel') }}"
                                                               placeholder="z. B. HP-12kW Pro">
                                                        @error('productModel') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Seriennummer(n)</label>
                                                        <input type="text" id="mw-serial-numbers" name="serialNumbers" class="mw-input"
                                                               value="{{ old('serialNumbers') }}"
                                                               placeholder="z. B. SN12345, SN67890">
                                                        <div class="mw-help">Mehrere Seriennummern per Komma trennen.</div>
                                                        @error('serialNumbers') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <div style="margin-top:12px;">
                                                    <label class="mw-label">Anlagenadresse</label>
                                                    <input type="text" id="mw-installation-address" name="installationAddressText" class="mw-input"
                                                           value="{{ old('installationAddressText') }}"
                                                           placeholder="Straße, PLZ, Ort">
                                                    <div class="mw-help">Wird übernommen und kann angepasst werden.</div>
                                                    @error('installationAddressText') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="mw-alert" style="margin-top:12px;">
                                                    <strong>Hinweis:</strong> Ohne Kunden/Objekt und Produkt ist kein Speichern möglich.
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="mw-card mw-card-soft">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-primary"><i class="fa-solid fa-building-circle-check"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Zuständige Filiale</div>
                                                        <div class="mw-card-subtitle">Steuert Zuständigkeiten, Servicegebiet und interne Kommunikation.</div>
                                                    </div>
                                                </div>

                                                <div style="margin-bottom:10px;">
                                                    <label class="mw-label">Filiale <span class="required">*</span></label>
                                                    <select id="mw-branch-search" class="mw-object-search-select" style="width:100%;"></select>
                                                    <div class="mw-help">Mindestens 1 Zeichen eingeben (Name, PLZ, Ort).</div>
                                                    <div class="mw-field-error" id="mw-err-branch" style="display:none;"></div>
                                                </div>

                                                <div style="display:grid;gap:10px;font-size:.80rem;color:var(--t-main);">
                                                    <div>
                                                        <label class="mw-label">Filialname</label>
                                                        <input type="text" id="mw-branch-name" class="mw-input" value="Keine Filiale ausgewählt" readonly>
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Filialadresse</label>
                                                        <div id="mw-branch-address" class="mw-help" style="line-height:1.45;">Noch keine Filiale gewählt.</div>
                                                    </div>

                                                    <div style="display:grid;gap:6px;">
                                                        <div style="display:flex;align-items:center;gap:8px;">
                                                            <i class="fa-regular fa-envelope" style="color:#0f172a;"></i>
                                                            <span id="mw-branch-email">–</span>
                                                        </div>
                                                        <div style="display:flex;align-items:center;gap:8px;">
                                                            <i class="fa-solid fa-phone" style="color:#0f172a;"></i>
                                                            <span id="mw-branch-phone">–</span>
                                                        </div>
                                                    </div>

                                                    <div class="mw-alert" style="background: rgba(192,216,234,.40); border-color: rgba(116,178,212,.55);">
                                                        <strong>Hinweis:</strong>
                                                        <span id="mw-branch-note">Die Filiale bestimmt SLA, Einsatzgebiet und Ansprechpartner.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mw-actions">
                                        <button type="button" class="mw-btn mw-btn-ghost" id="mw-reset-step1">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            <span>Auswahl zurücksetzen</span>
                                        </button>

                                        <button type="button" class="mw-btn mw-btn-primary" data-action="next" data-step="1">
                                            <span>Weiter</span>
                                            <i class="fa-solid fa-arrow-right-long"></i>
                                        </button>
                                    </div>
                                </section>

                                {{-- Step 2 --}}
                                <section class="wizard-step" data-step="2">
                                    <div class="mw-grid-2">
                                        <div>
                                            <div class="mw-card">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-primary"><i class="fa-solid fa-file-signature"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Vertragsdaten</div>
                                                        <div class="mw-card-subtitle">Pflichtfelder müssen vollständig sein, bevor gespeichert wird.</div>
                                                    </div>
                                                </div>

                                                <div class="mw-grid-3">
                                                    <div>
                                                        <label class="mw-label">Vertragsnummer (intern)</label>
                                                        <input type="text" name="contractNumber" class="mw-input"
                                                               value="{{ old('contractNumber') }}"
                                                               placeholder="z. B. WV-2026-0010">
                                                        @error('contractNumber') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Vertragsbeginn <span class="required">*</span></label>
                                                        <input type="date" name="contractStartDate" class="mw-input"
                                                               value="{{ old('contractStartDate') }}">
                                                        @error('contractStartDate') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Laufzeit (Monate) <span class="required">*</span></label>
                                                        <input type="number" name="contractDurationMonths" class="mw-input" min="1"
                                                               value="{{ old('contractDurationMonths') }}"
                                                               placeholder="z. B. 12">
                                                        @error('contractDurationMonths') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Abrechnungsmodus <span class="required">*</span></label>
                                                        <select name="billingMode" class="mw-select">
                                                            <option value="">Bitte wählen</option>
                                                            <option value="yearly" @selected(old('billingMode')==='yearly')>Jährlich pauschal</option>
                                                            <option value="one_time" @selected(old('billingMode')==='one_time')>Einmalig</option>
                                                            <option value="time_and_material" @selected(old('billingMode')==='time_and_material')>Nach Aufwand (Zeit/Material)</option>
                                                        </select>
                                                        @error('billingMode') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Jährlicher Wartungspreis brutto (€)</label>
                                                        <input type="number" step="0.01" min="0" name="yearlyPriceGross" class="mw-input"
                                                               value="{{ old('yearlyPriceGross') }}"
                                                               placeholder="z. B. 249,00">
                                                        @error('yearlyPriceGross') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="mw-label">Nächste vertragliche Wartung</label>
                                                        <input type="date" name="nextScheduledServiceDate" class="mw-input"
                                                               value="{{ old('nextScheduledServiceDate') }}">
                                                        @error('nextScheduledServiceDate') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <div style="margin-top:14px;">
                                                    <label class="mw-label">Verantwortliche Person <span class="required">*</span></label>
                                                    <select name="responsible_employee_id" id="mw-responsible-employee" class="mw-select" style="width:100%;"></select>
                                                    <div class="mw-help">Ansprechpartner intern (und optional CC).</div>
                                                    @error('responsible_employee_id') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                </div>

                                                <div style="margin-top:12px;">
                                                    <label class="mw-label">Interne Notizen</label>
                                                    <textarea name="contractInternalNotes" class="mw-textarea"
                                                              placeholder="Sonderkonditionen, Besonderheiten, Rückfragen...">{{ old('contractInternalNotes') }}</textarea>
                                                    @error('contractInternalNotes') <div class="mw-field-error">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="mw-card mw-card-soft" style="margin-top:14px;">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-lime"><i class="fa-regular fa-envelope"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Bestätigungs-E-Mail (optional)</div>
                                                        <div class="mw-card-subtitle">Kann beim Speichern automatisch ausgelöst werden.</div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="mw-label">Hinweistext für die Kunden-E-Mail</label>
                                                    <textarea name="customerEmailNote" class="mw-textarea"
                                                              placeholder="Zusätzlicher Hinweis für den Kunden...">{{ old('customerEmailNote') }}</textarea>
                                                    @error('customerEmailNote') <div class="mw-field-error">{{ $message }}</div> @enderror

                                                    <div class="mw-help" style="margin-top:8px;">
                                                        Der Kunde erhält einen Link zur Einsicht/Bestätigung (sofern im Backend implementiert).
                                                    </div>

                                                    <label style="display:inline-flex;align-items:center;gap:10px;margin-top:12px;font-size:.80rem;color:var(--t-main);font-weight:900;">
                                                        <input type="checkbox" name="sendEmail" value="1" @checked(old('sendEmail', '1')=='1')>
                                                        <span>E-Mail nach dem Speichern senden</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="mw-card mw-card-soft">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-primary"><i class="fa-solid fa-eye"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Review</div>
                                                        <div class="mw-card-subtitle">Kontrolle vor dem Speichern (Live-Zusammenfassung).</div>
                                                    </div>
                                                </div>

                                                <div id="mw-summary-preview" class="mw-summary">
                                                    <div class="mw-alert">Die Zusammenfassung wird automatisch aktualisiert.</div>
                                                </div>
                                            </div>

                                            <div class="mw-card" style="margin-top:14px;">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-lime"><i class="fa-solid fa-lock"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Datenqualität</div>
                                                        <div class="mw-card-subtitle">Pflichtfelder werden vor dem Speichern geprüft.</div>
                                                    </div>
                                                </div>
                                                <div class="mw-help">
                                                    Wenn “Weiter” oder “Speichern” nicht funktioniert, fehlen meist:
                                                    <strong>Kunde/Objekt</strong>, <strong>Filiale</strong>, <strong>Produkt/Modell</strong> oder <strong>Verantwortliche Person</strong>.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mw-actions">
                                        <button type="button" class="mw-btn mw-btn-secondary" data-action="prev" data-step="2">
                                            <i class="fa-solid fa-arrow-left-long"></i>
                                            <span>Zurück</span>
                                        </button>

                                        <button type="submit" class="mw-btn mw-btn-success" id="mw-submit-btn">
                                            <span class="mw-spinner"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                                            <span class="mw-btn-text"><i class="fa-solid fa-check"></i> Vertrag speichern</span>
                                        </button>
                                    </div>
                                </section>


                                {{-- Step 3 --}}
                                <section class="wizard-step" data-step="3">
                                    <div class="mw-grid-2">
                                        <div>
                                            <div class="mw-card">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-primary"><i class="fa-solid fa-list-check"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Wartungs-Checkliste zuweisen</div>
                                                        <div class="mw-card-subtitle">Wähle eine aktive Checkliste. Items werden als Vorschau geladen.</div>
                                                    </div>
                                                </div>

                                                <div style="margin-bottom:10px;">
                                                    <label class="mw-label">Checkliste <span class="required">*</span></label>
                                                    <select id="mw-checklist-search" class="mw-object-search-select" style="width:100%;"></select>
                                                    <div class="mw-help">Suche nach Titel / Beschreibung.</div>
                                                    <div class="mw-field-error" id="mw-err-checklist" style="display:none;"></div>
                                                </div>

                                                <div class="mw-card mw-card-soft" style="padding:14px;">
                                                    <div class="mw-card-title" style="font-size:.85rem;">Vorschau (Items)</div>
                                                    <div class="mw-help" id="mw-checklist-preview" style="margin-top:6px;">
                                                        Keine Checkliste ausgewählt.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="mw-card mw-card-soft">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-lime"><i class="fa-solid fa-user-gear"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Techniker Planung</div>
                                                        <div class="mw-card-subtitle">Haupttechniker + optionale Zusatztechniker.</div>
                                                    </div>
                                                </div>

                                                <div style="margin-bottom:10px;">
                                                    <label class="mw-label">Haupttechniker <span class="required">*</span></label>
                                                    <select id="mw-main-technician" class="mw-select" style="width:100%;"></select>
                                                    <div class="mw-field-error" id="mw-err-maintech" style="display:none;"></div>
                                                </div>

                                                <div style="margin-bottom:10px;">
                                                    <label class="mw-label">Zusatztechniker (optional)</label>
                                                    <select id="mw-additional-technicians" class="mw-select" multiple style="width:100%;"></select>
                                                    <div class="mw-help">Mehrfachauswahl möglich.</div>
                                                </div>

                                                <div class="mw-grid-2-compact" style="margin-top:12px;">
                                                    <div>
                                                        <label class="mw-label">Geplantes Datum (optional)</label>
                                                        <input type="date" id="mw-maintenance-date" class="mw-input">
                                                    </div>
                                                    <div>
                                                        <label class="mw-label">Zeit (optional)</label>
                                                        <div class="mw-grid-2-compact">
                                                            <input type="time" id="mw-maintenance-time-from" class="mw-input" placeholder="Von">
                                                            <input type="time" id="mw-maintenance-time-to" class="mw-input" placeholder="Bis">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mw-card mw-card-soft" style="margin-top:14px;">
                                                <div class="mw-card-header">
                                                    <div class="mw-card-icon mw-icon-primary"><i class="fa-solid fa-eye"></i></div>
                                                    <div>
                                                        <div class="mw-card-title">Review</div>
                                                        <div class="mw-card-subtitle">Letzte Kontrolle vor dem Speichern.</div>
                                                    </div>
                                                </div>
                                                <div id="mw-summary-preview-step3" class="mw-summary">
                                                    <div class="mw-alert">Die Zusammenfassung wird automatisch aktualisiert.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mw-actions">
                                        <button type="button" class="mw-btn mw-btn-secondary" data-action="prev" data-step="3">
                                            <i class="fa-solid fa-arrow-left-long"></i>
                                            <span>Zurück</span>
                                        </button>

                                        <button type="submit" class="mw-btn mw-btn-success" id="mw-submit-btn">
                                            <span class="mw-spinner"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                                            <span class="mw-btn-text"><i class="fa-solid fa-check"></i> Vertrag speichern</span>
                                        </button>
                                    </div>
                                </section>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

 

@section('script')
    {{-- Select2 --}}
    <script src="{{ asset('js/select2.min.js') }}"></script>
 <script>
(function () {
  "use strict";

  function ensureJQuery(cb) {
    if (window.jQuery) return cb(window.jQuery);

    const existing = document.querySelector('script[data-mw-jq="1"]');
    if (existing) {
      const t = setInterval(() => {
        if (window.jQuery) { clearInterval(t); cb(window.jQuery); }
      }, 40);
      return;
    }

    const s = document.createElement("script");
    s.src = "https://code.jquery.com/jquery-3.7.1.min.js";
    s.async = true;
    s.setAttribute("data-mw-jq", "1");
    s.onload = () => cb(window.jQuery);
    s.onerror = () => { console.error("MaintenanceWizard: jQuery failed."); cb(null); };
    document.head.appendChild(s);
  }

  function qs(sel){ return document.querySelector(sel); }
  function qsa(sel){ return Array.from(document.querySelectorAll(sel)); }

  function getValById(id){
    const el = document.getElementById(id);
    return el ? String(el.value || "").trim() : "";
  }
  function setValById(id, v){
    const el = document.getElementById(id);
    if (el) el.value = v ?? "";
  }
  function getVal(sel){
    const el = qs(sel);
    return el ? String(el.value || "").trim() : "";
  }
  function esc(v){
    if (v === null || v === undefined) return "";
    return String(v)
      .replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")
      .replace(/"/g,"&quot;").replace(/'/g,"&#039;");
  }
  function showInlineError(id, msg){
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = msg ? "block" : "none";
    el.textContent = msg || "";
  }
  function markInvalid(selector, invalid){
    const el = qs(selector);
    if (!el) return;
    el.classList.toggle("is-invalid", !!invalid);
  }

  // -------------------------------------------------------------------------
  // STATE (checklist + technicians)
  // -------------------------------------------------------------------------
  const State = {
    checklist: {
      id: null,
      title: null,
      items: []
    },
    technicians: {
      mainId: null,
      additionalIds: []
    }
  };

  // -------------------------------------------------------------------------
  // Wizard Core
  // -------------------------------------------------------------------------
  const Wizard = (function () {
    let currentStepIndex = 1;
    const maxStepIndex = 3;

    function setActiveStep(step) {
      currentStepIndex = Math.min(Math.max(step, 1), maxStepIndex);

      qsa(".wizard-step").forEach(el => {
        const s = parseInt(el.getAttribute("data-step"), 10);
        el.classList.toggle("active", s === currentStepIndex);
      });

      qsa("[data-step-indicator]").forEach(el => {
        const s = parseInt(el.getAttribute("data-step-indicator"), 10);
        el.classList.toggle("is-active", s === currentStepIndex);
      });

      renderSummaryPreview();
    }

    function validateStep1() {
      const leadId   = getValById("mw-customer-id");
      const altId    = getValById("mw-alternative-id");
      const branchId = getValById("mw-branch-id");
      const productModel = getValById("mw-product-model");

      let ok = true;

      if (!leadId && !altId) { ok = false; showInlineError("mw-err-customer", "Bitte Kunde oder Objekt auswählen."); }
      else showInlineError("mw-err-customer", "");

      if (!branchId) { ok = false; showInlineError("mw-err-branch", "Bitte eine Filiale auswählen."); }
      else showInlineError("mw-err-branch", "");

      markInvalid("#mw-product-model", !productModel);
      if (!productModel) ok = false;

      if (!ok) window.scrollTo({ top: 0, behavior: "smooth" });
      return ok;
    }

    function validateStep2() {
      // responsible employee is required in your UI
      const responsibleId = getValById("mw-responsible-employee");
      let ok = true;

      if (!responsibleId) ok = false;

      return ok;
    }

    function validateStep3() {
      let ok = true;

      if (!State.checklist.id) {
        ok = false;
        showInlineError("mw-err-checklist", "Bitte eine Checkliste auswählen.");
      } else {
        showInlineError("mw-err-checklist", "");
      }

      if (!State.technicians.mainId) {
        ok = false;
        showInlineError("mw-err-maintech", "Bitte Haupttechniker auswählen.");
      } else {
        showInlineError("mw-err-maintech", "");
      }

      return ok;
    }

    function renderSummaryPreview() {
      const box2 = document.getElementById("mw-summary-preview");
      const box3 = document.getElementById("mw-summary-preview-step3");

      const branchName   = getVal("#mw-branch-name") || "–";
      const address      = getVal("#mw-installation-address") || "–";
      const productModel = getVal("#mw-product-model") || "–";

      const contractNumber = getVal('input[name="contractNumber"]') || "–";
      const start          = getVal('input[name="contractStartDate"]') || "–";
      const duration       = getVal('input[name="contractDurationMonths"]') || "–";
      const nextService    = getVal('input[name="nextScheduledServiceDate"]') || "–";

      let customerText = "–";
      let responsibleText = "–";
      if (window.jQuery) {
        customerText = window.jQuery("#mw-customer-search").find("option:selected").text() || "–";
        responsibleText = window.jQuery("#mw-responsible-employee").find("option:selected").text() || "–";
      }

      const checklistTitle = State.checklist.title || "–";
      const mainTechText = (window.jQuery && window.jQuery("#mw-main-technician").length)
        ? (window.jQuery("#mw-main-technician").find("option:selected").text() || "–")
        : "–";

      const date = getVal("#mw-maintenance-date") || "–";
      const tFrom = getVal("#mw-maintenance-time-from") || "–";
      const tTo = getVal("#mw-maintenance-time-to") || "–";

      const html =
        '<div class="row">' +
          "<h4>Kunde & Anlage</h4>" +
          '<div><span class="muted">Kunde/Objekt:</span> ' + esc(customerText) + "</div>" +
          '<div><span class="muted">Adresse:</span> ' + esc(address) + "</div>" +
          '<div><span class="muted">Produkt:</span> ' + esc(productModel) + "</div>" +
        "</div>" +
        '<div class="row" style="margin-top:10px;">' +
          "<h4>Filiale</h4>" +
          '<div><span class="muted">Filialname:</span> ' + esc(branchName) + "</div>" +
        "</div>" +
        '<div class="row" style="margin-top:10px;">' +
          "<h4>Vertrag</h4>" +
          '<div><span class="muted">Vertragsnummer:</span> ' + esc(contractNumber) + "</div>" +
          '<div><span class="muted">Beginn:</span> ' + esc(start) + "</div>" +
          '<div><span class="muted">Laufzeit:</span> ' + esc(duration) + " Monate</div>" +
          '<div><span class="muted">Nächste Wartung:</span> ' + esc(nextService) + "</div>" +
          '<div><span class="muted">Verantwortlich:</span> ' + esc(responsibleText) + "</div>" +
        "</div>" +
        '<div class="row" style="margin-top:10px;">' +
          "<h4>Checkliste & Techniker</h4>" +
          '<div><span class="muted">Checkliste:</span> ' + esc(checklistTitle) + "</div>" +
          '<div><span class="muted">Haupttechniker:</span> ' + esc(mainTechText) + "</div>" +
          '<div><span class="muted">Planung:</span> ' + esc(date) + " · " + esc(tFrom) + "–" + esc(tTo) + "</div>" +
        "</div>";

      if (box2) box2.innerHTML = html;
      if (box3) box3.innerHTML = html;
    }

    function buildWizardPayload() {
      const leadId   = getValById("mw-customer-id");
      const altId    = getValById("mw-alternative-id");
      const assetId  = getValById("mw-asset-id");
      const productId = getValById("mw-product-id");
      const leadProductListId = getValById("mw-lead-product-list-id");
      const branchId = getValById("mw-branch-id");

      const systemTypeCode  = getValById("mw-system-type-code");
      const systemTypeLabel = getValById("mw-system-type-label");
      const productModel    = getValById("mw-product-model");
      const serialNumbers   = getValById("mw-serial-numbers");
      const addressText     = getValById("mw-installation-address");

      const serialArray = serialNumbers ? serialNumbers.split(",").map(s => s.trim()).filter(Boolean) : [];

      const contractNumber = getVal('input[name="contractNumber"]');
      const startDate      = getVal('input[name="contractStartDate"]');
      const durationMonths = getVal('input[name="contractDurationMonths"]');
      const billingMode    = getVal('select[name="billingMode"]');
      const yearlyPriceGross = getVal('input[name="yearlyPriceGross"]');
      const nextScheduledServiceDate = getVal('input[name="nextScheduledServiceDate"]');
      const internalNotes  = getVal('textarea[name="contractInternalNotes"]');

      const responsibleEmployeeId = getValById("mw-responsible-employee");

      const maintenanceDate = getVal("#mw-maintenance-date");
      const timeFrom = getVal("#mw-maintenance-time-from");
      const timeTo   = getVal("#mw-maintenance-time-to");

      return {
        context: {
          leadId: leadId ? Number(leadId) : null,
          siteId: altId ? Number(altId) : null,
          assetId: assetId ? Number(assetId) : null,
          branchId: branchId ? Number(branchId) : null
        },

        installation: {
          systemType: systemTypeCode || null,
          systemTypeLabel: systemTypeLabel || null,
          productModel: productModel || null,
          serialNumbers: serialArray,
          installationAddressText: addressText || null,
          installationLocation: { roomOrArea: null, notes: addressText || null },
          installationDate: null
        },

        maintenanceCurrent: {
          date: maintenanceDate || null,
          timeFrom: timeFrom || null,
          timeTo: timeTo || null,
          mainTechnician: State.technicians.mainId ? { employeeId: Number(State.technicians.mainId) } : null,
          additionalTechnicians: (State.technicians.additionalIds || []).map(id => ({ employeeId: Number(id) }))
        },

        // ✅ checklists array (store() expects checklist[0].checklistId)
        checklist: State.checklist.id ? [{
          checklistId: Number(State.checklist.id),
          title: State.checklist.title || null,
          items: State.checklist.items || []
        }] : [],

        contract: {
          contractNumber: contractNumber || null,
          startDate: startDate || null,
          durationMonths: durationMonths ? Number(durationMonths) : null,
          billingMode: billingMode || null,
          nextScheduledServiceDate: nextScheduledServiceDate || null,
          internalNotes: internalNotes || null
        },

        summary: {
          nextServiceDateRecommended: nextScheduledServiceDate || null,
          summaryForCustomer: null,
          notesInternal: internalNotes || null,
          recommendedIntervalMonths: null,
          statusOverall: null
        },

        system: { version: 1 },

        product: { productId: productId ? Number(productId) : null },
        leadProduct: { leadProductListId: leadProductListId ? Number(leadProductListId) : null },

        price: {
          yearlyPriceGross: yearlyPriceGross ? Number(String(yearlyPriceGross).replace(",", ".")) : null,
          currency: "EUR"
        }
      };
    }

    function attachLiveSummary() {
      const watch = [
        "#mw-installation-address",
        "#mw-product-model",
        'input[name="contractNumber"]',
        'input[name="contractStartDate"]',
        'input[name="contractDurationMonths"]',
        'input[name="nextScheduledServiceDate"]',
        'select[name="billingMode"]',
        'input[name="yearlyPriceGross"]',
        'textarea[name="contractInternalNotes"]',
        "#mw-maintenance-date",
        "#mw-maintenance-time-from",
        "#mw-maintenance-time-to"
      ];

      watch.forEach(sel => {
        const el = qs(sel);
        if (!el) return;
        const fn = () => renderSummaryPreview();
        el.addEventListener("input", fn);
        el.addEventListener("change", fn);
      });

      if (window.jQuery) {
        window.jQuery("#mw-responsible-employee").on("change", renderSummaryPreview);
        window.jQuery("#mw-main-technician").on("change", renderSummaryPreview);
        window.jQuery("#mw-additional-technicians").on("change", renderSummaryPreview);
      }
    }

    function attachSubmitGuard() {
      const form = document.getElementById("wizard-form");
      const btn  = document.getElementById("mw-submit-btn");
      const payloadInput = document.getElementById("mw-wizard-payload");
      if (!form) return;

      form.addEventListener("submit", function (e) {
        // validate all steps
        if (!validateStep1()) { e.preventDefault(); setActiveStep(1); return; }
        if (!validateStep2()) { e.preventDefault(); setActiveStep(2); return; }
        if (!validateStep3()) { e.preventDefault(); setActiveStep(3); return; }

        if (payloadInput) payloadInput.value = JSON.stringify(buildWizardPayload());

        if (btn) {
          btn.classList.add("is-loading");
          btn.setAttribute("disabled", "disabled");
        }
      });
    }

    function attachWizardNav(jq) {
      qsa('[data-action="next"]').forEach(btn => {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          const step = parseInt(btn.getAttribute("data-step") || "1", 10);

          if (step === 1 && !validateStep1()) return;
          if (step === 2 && !validateStep2()) return;

          setActiveStep(step + 1);
        });
      });

      qsa('[data-action="prev"]').forEach(btn => {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          const step = parseInt(btn.getAttribute("data-step") || "2", 10);
          setActiveStep(step - 1);
        });
      });

      const resetBtn = document.getElementById("mw-reset-step1");
      if (resetBtn) {
        resetBtn.addEventListener("click", function () {
          ["mw-customer-id","mw-alternative-id","mw-product-id","mw-branch-id","mw-lead-product-list-id"].forEach(id => setValById(id, ""));
          ["mw-system-type-code","mw-system-type","mw-system-type-label","mw-product-model","mw-serial-numbers","mw-installation-address"].forEach(id => setValById(id, ""));

          if (jq) {
            jq("#mw-customer-search").val(null).trigger("change");
            jq("#mw-branch-search").val(null).trigger("change");
          }

          showInlineError("mw-err-customer","");
          showInlineError("mw-err-branch","");
          markInvalid("#mw-product-model", false);

          renderSummaryPreview();
        });
      }
    }

    function init(jq) {
      setActiveStep(1);
      attachWizardNav(jq);
      attachLiveSummary();
      attachSubmitGuard();

      window.__mw_setActiveStep = setActiveStep;
      window.__mw_renderSummaryPreview = renderSummaryPreview;
    }

    return { init, renderSummaryPreview };
  })();

  // -------------------------------------------------------------------------
  // Select2 + AJAX
  // -------------------------------------------------------------------------
  function initSelect2($) {
    if (!$) return;

    const ROUTES = {
      customerSearch: @json(route('admin.maintenance.contracts.customer_search')),
      branchSearch:   @json(route('admin.maintenance.contracts.branch_search')),
      technicians:    @json(route('admin.maintenance.contracts.technicians')),
      checklistIndex: @json(route('admin.maintenance.contracts.checklists.ajax_index')),
      checklistShowBase: @json(url('/admin/maintenance/contracts/checklists')) // + /{id}
    };

    const ctx = {
      $customerSearch: $("#mw-customer-search"),
      $branchSearch: $("#mw-branch-search"),

      $leadId: $("#mw-customer-id"),
      $altId: $("#mw-alternative-id"),
      $productId: $("#mw-product-id"),
      $leadProductListId: $("#mw-lead-product-list-id"),
      $branchId: $("#mw-branch-id"),

      $systemTypeCode: $("#mw-system-type-code"),
      $systemType: $("#mw-system-type"),
      $systemTypeLabel: $("#mw-system-type-label"),
      $productModel: $("#mw-product-model"),
      $serialNumbers: $("#mw-serial-numbers"),
      $installationAddress: $("#mw-installation-address"),

      $branchName: $("#mw-branch-name"),
      $branchAddress: $("#mw-branch-address"),
      $branchEmail: $("#mw-branch-email"),
      $branchPhone: $("#mw-branch-phone"),
      $branchNote: $("#mw-branch-note")
    };

    function templateCustomerResult(data) {
      if (!data.id) return data.text;
      const customer = data.customer_name || data.text || "";
      const customerNo = data.customer_no ? ("#" + data.customer_no) : "";
      const address = data.address_text || "";
      const productName = data.product_name || data.product_model || "";
      const productGroup = data.product_group || "";

      let html = '<div class="mw-object-result">';
      html += '<div class="mw-object-result-main">';
      html += "<span>" + esc(customer) + "</span>";
      if (customerNo) html += '<span class="mw-object-result-kno">' + esc(customerNo) + "</span>";
      html += "</div>";

      if (address) {
        html += '<div class="mw-object-result-sub">';
        html += '<i class="fa-solid fa-location-dot mw-object-result-icon"></i>';
        html += "<span>" + esc(address) + "</span>";
        html += "</div>";
      }

      if (productName || productGroup) {
        html += '<div class="mw-object-result-product">';
        html += '<i class="fa-solid fa-box-open mw-object-result-icon"></i>';
        html += "<span>" + esc(productName);
        if (productGroup) html += '<span class="mw-object-result-badge">' + esc(productGroup) + "</span>";
        html += "</span></div>";
      }

      html += "</div>";
      return html;
    }

    function templateBranchResult(data) {
      if (!data.id) return data.text;
      const name = data.name || data.text || "";
      const city = data.city || "";
      const postcode = data.postcode || "";
      const email = data.email || "";
      const phone = data.phone || "";

      let html = '<div class="mw-object-result">';
      html += '<div class="mw-object-result-main"><span>' + esc(name) + "</span></div>";

      if (city || postcode) {
        html += '<div class="mw-object-result-sub">';
        html += '<i class="fa-solid fa-location-dot mw-object-result-icon"></i>';
        html += "<span>" + esc([postcode, city].filter(Boolean).join(" ")) + "</span>";
        html += "</div>";
      }

      if (email || phone) {
        html += '<div class="mw-object-result-product">';
        html += '<i class="fa-regular fa-envelope mw-object-result-icon"></i>';
        html += "<span>" + esc([email, phone].filter(Boolean).join(" · ")) + "</span>";
        html += "</div>";
      }

      html += "</div>";
      return html;
    }

    // Customer/Object/Product
    if (ctx.$customerSearch.length) {
      ctx.$customerSearch.select2({
        placeholder: "Kunde / Objekt / Produkt suchen…",
        allowClear: true,
        minimumInputLength: 2,
        width: "100%",
        ajax: {
          url: ROUTES.customerSearch,
          dataType: "json",
          delay: 250,
          data: params => ({ q: params.term }),
          processResults: data => ({ results: (data && data.results) ? data.results : [] })
        },
        templateResult: templateCustomerResult,
        templateSelection: function (data) {
          if (!data.id) return data.text;
          const customer = data.customer_name || data.text || "";
          const productName = data.product_name || data.product_model || "";
          const productGroup = data.product_group || "";
          let label = customer;
          if (productName) label += " – " + productName;
          if (productGroup) label += " (" + productGroup + ")";
          return label;
        },
        escapeMarkup: m => m
      });

      ctx.$customerSearch.on("select2:select", function (e) {
        const d = (e && e.params && e.params.data) ? e.params.data : {};

        ctx.$leadId.val(d.customer_id || "");
        ctx.$altId.val(d.alternative_id || "");
        ctx.$productId.val(d.product_id || "");
        ctx.$leadProductListId.val(d.lead_product_list_id || d.id || "");

        const sysCode = d.system_type || "";
        const sysLabel = d.system_type_label || d.product_group || d.system_type || "";

        ctx.$systemTypeCode.val(sysCode);
        ctx.$systemType.val(sysLabel);
        ctx.$systemTypeLabel.val(sysLabel);

        if (d.product_model) ctx.$productModel.val(d.product_model);
        else if (d.product_name) ctx.$productModel.val(d.product_name);

        if (d.serial_numbers) ctx.$serialNumbers.val(d.serial_numbers);

        const addrText = d.installation_address_text || d.address_text || "";
        if (addrText) ctx.$installationAddress.val(addrText);

        showInlineError("mw-err-customer", "");
        if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
      });

      ctx.$customerSearch.on("select2:clear", function () {
        ctx.$leadId.val("");
        ctx.$altId.val("");
        ctx.$productId.val("");
        ctx.$leadProductListId.val("");

        ctx.$systemTypeCode.val("");
        ctx.$systemType.val("");
        ctx.$systemTypeLabel.val("");
        ctx.$productModel.val("");
        ctx.$serialNumbers.val("");
        ctx.$installationAddress.val("");

        if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
      });
    }

    // Branch
    if (ctx.$branchSearch.length) {
      ctx.$branchSearch.select2({
        placeholder: "Filiale suchen…",
        allowClear: true,
        minimumInputLength: 1,
        width: "100%",
        ajax: {
          url: ROUTES.branchSearch,
          dataType: "json",
          delay: 250,
          data: params => ({ q: params.term }),
          processResults: data => ({ results: (data && data.results) ? data.results : [] })
        },
        templateResult: templateBranchResult,
        templateSelection: function (data) {
          if (!data.id) return data.text;
          const name = data.name || data.text || "Filiale";
          const city = data.city || "";
          return city ? (name + " – " + city) : name;
        },
        escapeMarkup: m => m
      });

      ctx.$branchSearch.on("select2:select", function (e) {
        const d = (e && e.params && e.params.data) ? e.params.data : {};
        ctx.$branchId.val(d.branch_id || "");

        const street = d.street || "";
        const postcode = d.postcode || "";
        const city = d.city || "";
        const country = d.country || "";

        const lines = [];
        if (street) lines.push(esc(street));
        const line2 = [postcode, city].filter(Boolean).join(" ");
        if (line2) lines.push(esc(line2));
        if (country) lines.push(esc(country));

        ctx.$branchName.val(d.name || "Filiale");
        ctx.$branchAddress.html(lines.join("<br>") || "Noch keine Filiale gewählt.");
        ctx.$branchEmail.text(d.email || "–");
        ctx.$branchPhone.text(d.phone || "–");
        ctx.$branchNote.text(d.note || "Die Filiale bestimmt SLA, Einsatzgebiet und Ansprechpartner.");

        showInlineError("mw-err-branch", "");
        if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
      });

      ctx.$branchSearch.on("select2:clear", function () {
        ctx.$branchId.val("");
        ctx.$branchName.val("Keine Filiale ausgewählt");
        ctx.$branchAddress.html("Noch keine Filiale gewählt.");
        ctx.$branchEmail.text("–");
        ctx.$branchPhone.text("–");
        ctx.$branchNote.text("Die Filiale bestimmt SLA, Einsatzgebiet und Ansprechpartner.");

        if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
      });
    }

    // Load technicians once -> fill responsible + main + additional
    $.ajax({ url: ROUTES.technicians, type: "GET", dataType: "json" })
      .done(function (resp) {
        const employees = (resp && resp.employees) ? resp.employees : [];

        // Responsible (step2)
        const $respSel = $("#mw-responsible-employee");
        if ($respSel.length) {
          $respSel.empty().append('<option value="">Bitte wählen</option>');
          employees.forEach(emp => $respSel.append($("<option></option>").val(emp.id).text(emp.name + (emp.meta ? (" – " + emp.meta) : ""))));
          $respSel.select2({ width:"100%", placeholder:"Verantwortliche Person wählen", allowClear:true });

          const oldVal = @json(old('responsible_employee_id'));
          if (oldVal) $respSel.val(String(oldVal)).trigger("change");
        }

        // Main technician (step3)
        const $main = $("#mw-main-technician");
        if ($main.length) {
          $main.empty().append('<option value="">Bitte wählen</option>');
          employees.forEach(emp => $main.append($("<option></option>").val(emp.id).text(emp.name + (emp.meta ? (" – " + emp.meta) : ""))));
          $main.select2({ width:"100%", placeholder:"Haupttechniker wählen", allowClear:true });

          $main.on("change", function () {
            State.technicians.mainId = $(this).val() || null;
            if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
          });
        }

        // Additional technicians (step3)
        const $add = $("#mw-additional-technicians");
        if ($add.length) {
          $add.empty();
          employees.forEach(emp => $add.append($("<option></option>").val(emp.id).text(emp.name + (emp.meta ? (" – " + emp.meta) : ""))));
          $add.select2({ width:"100%", placeholder:"Zusatztechniker wählen" });

          $add.on("change", function () {
            State.technicians.additionalIds = ($(this).val() || []).map(String);
            if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
          });
        }
      })
      .fail(function () {
        $("#mw-responsible-employee").html('<option value="">Mitarbeiter konnten nicht geladen werden</option>');
        $("#mw-main-technician").html('<option value="">Mitarbeiter konnten nicht geladen werden</option>');
      });

    // Checklist select2 + load items
    const $check = $("#mw-checklist-search");
    if ($check.length) {
      $check.select2({
        placeholder: "Checkliste suchen…",
        allowClear: true,
        minimumInputLength: 1,
        width: "100%",
        ajax: {
          url: ROUTES.checklistIndex,
          dataType: "json",
          delay: 250,
          data: params => ({ q: params.term }),
          processResults: data => ({
            results: (data && data.checklists ? data.checklists : []).map(c => ({
              id: c.id,
              text: c.title,
              description: c.description || "",
              type: c.type || "",
              status: c.status || ""
            }))
          })
        }
      });

      $check.on("select2:select", function (e) {
        const d = e.params.data || {};
        State.checklist.id = d.id;
        State.checklist.title = d.text;

        showInlineError("mw-err-checklist", "");

        // load items
        const url = ROUTES.checklistShowBase + "/" + encodeURIComponent(d.id);
        $.getJSON(url).done(function (resp) {
          const items = (resp && resp.items) ? resp.items : [];
          State.checklist.items = items;

          const prev = document.getElementById("mw-checklist-preview");
          if (prev) {
            if (!items.length) {
              prev.textContent = "Diese Checkliste hat keine Items.";
            } else {
              prev.innerHTML =
                "<ul style='margin:6px 0 0; padding-left:18px;'>" +
                items.slice(0, 20).map(it => "<li><strong>" + esc(it.label) + "</strong> <span style='color:#64748b'>(" + esc(it.field_type) + ")</span></li>").join("") +
                (items.length > 20 ? "<li>…</li>" : "") +
                "</ul>";
            }
          }

          if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
        });
      });

      $check.on("select2:clear", function () {
        State.checklist.id = null;
        State.checklist.title = null;
        State.checklist.items = [];
        const prev = document.getElementById("mw-checklist-preview");
        if (prev) prev.textContent = "Keine Checkliste ausgewählt.";
        if (window.__mw_renderSummaryPreview) window.__mw_renderSummaryPreview();
      });
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    ensureJQuery(function ($) {
      Wizard.init($);
      initSelect2($);
    });
  });
})();
</script>

@endsection
