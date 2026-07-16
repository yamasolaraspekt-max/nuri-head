{{-- resources/views/admin/maintenance/wizard.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Wartungsvertrag anlegen')

@php
    $storeAction = \Illuminate\Support\Facades\Route::has('admin.maintenance.contracts.store_simple')
        ? route('admin.maintenance.contracts.store_simple')
        : route('admin.maintenance.contracts.store');

    $contextCount = collect([
        isset($lead) ? 1 : null,
        isset($alternative) ? 1 : null,
        isset($asset) ? 1 : null,
    ])->filter()->count();
@endphp

@once
@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

<style>
  :root {
    --app-bg:#f3f4f6;
    --card-bg:#ffffff;
    --text-main:#1f2937;
    --text-muted:#6b7280;
    --border:#e5e7eb;
    --primary:var(--sa-accent);
    --primary-hover:var(--sa-accent-hover);
    --primary-light:var(--sa-accent-light);
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
    --radius:14px;
    --transition:all .2s ease-in-out;
  }

  .oc-wrap{
    font-family:Inter, system-ui, -apple-system, sans-serif;
    color:var(--text-main);
    max-width:1500px;
    margin:20px auto;
    padding:39px;
    padding-right:79px;
  }

  .oc-header{margin-bottom:18px;margin-top:103px;}
  .oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }
  .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}
  .oc-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
  }
  .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
  .oc-breadcrumb a:hover{color:var(--text-main);}
  .oc-breadcrumb span.current{color:#111827;font-weight:800;}

  .oc-inline-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}

  .oc-btn{
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
    gap:8px;
    text-decoration:none;
  }
  .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}

  .oc-btn-soft{
    background:#fff;
    color:var(--text-main);
    border:1px solid var(--border);
    padding:10px 14px;
    border-radius:10px;
    font-weight:800;
    cursor:pointer;
    transition:var(--transition);
    text-decoration:none;
  }
  .oc-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

  .oc-btn-ic{
    width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:#fff;
    display:inline-flex;align-items:center;justify-content:center;color:var(--text-muted);
    cursor:pointer;transition:var(--transition);text-decoration:none;
  }
  .oc-btn-ic:hover{background:#f9fafb;color:var(--text-main);border-color:#d1d5db;text-decoration:none;}
  .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .oc-btn-ic.primary:hover{border-color:var(--primary)}
  .oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
  .oc-btn-ic.warning:hover{border-color:#f59e0b}
  .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
  .oc-btn-ic.success:hover{border-color:var(--success)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
  @media(max-width:700px){ .oc-analytics{grid-template-columns:1fr;} }

  .oc-stat{
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
  .oc-stat-icon{
    width:48px;height:48px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;flex:0 0 auto;
  }
  .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
  .oc-stat-icon.context{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.flow{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.safe{background:var(--gray-light);color:var(--gray)}

  .oc-stat-meta{min-width:0}
  .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
  .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

  .oc-alert{
    border-radius:14px;
    padding:14px 16px;
    border:1px solid var(--border);
    background:#fff;
    color:var(--text-main);
    font-size:14px;
    line-height:1.5;
    box-shadow:var(--shadow-sm);
    margin-bottom:16px;
  }
  .oc-alert.danger{background:var(--danger-light);border-color:rgba(239,68,68,.18);color:#991b1b;}
  .oc-alert.info{background:var(--blue-light);border-color:rgba(116,178,212,.25);}
  .oc-alert.success{background:var(--success-light);border-color:rgba(16,185,129,.2);}
  .oc-alert ul{margin:8px 0 0 18px;}

  .oc-stepper{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:900px){ .oc-stepper{grid-template-columns:1fr;} }

  .oc-step{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:14px 16px;
    display:flex;
    gap:12px;
    align-items:flex-start;
    box-shadow:var(--shadow-sm);
    opacity:.75;
    transition:var(--transition);
  }
  .oc-step.is-active{
    border-color:var(--primary);
    background:#fcfdf8;
    opacity:1;
    box-shadow:var(--shadow);
  }
  .oc-step-circle{
    width:40px;height:40px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    background:var(--gray-light);color:var(--gray);font-weight:900;font-size:14px;
    flex:0 0 auto;
  }
  .oc-step.is-active .oc-step-circle{
    background:var(--primary-light);
    color:#365314;
  }
  .oc-step-label-main{
    font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;
  }
  .oc-step-label-sub{
    font-size:14px;font-weight:900;color:#111827;margin-top:4px;line-height:1.35;
  }

  .oc-grid-2{display:grid;grid-template-columns:1.35fr 1fr;gap:16px;}
  .oc-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
  .oc-grid-2-compact{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
  @media(max-width:1023px){.oc-grid-2,.oc-grid-3{grid-template-columns:1fr;}}
  @media(max-width:640px){.oc-grid-2-compact{grid-template-columns:1fr;}}

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }
  .oc-card.soft{background:linear-gradient(180deg,#fff 0%,#fbfdff 100%);}
  .oc-card-h{
    display:flex;gap:12px;align-items:flex-start;justify-content:space-between;
    padding:16px 18px;border-bottom:1px solid var(--border);background:#fafafa;
  }
  .oc-card-h-main{display:flex;gap:12px;align-items:flex-start;}
  .oc-card-ic{
    width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;
    background:var(--blue-light);color:var(--blue);flex:0 0 auto;
  }
  .oc-card-ic.success{background:var(--success-light);color:var(--success);}
  .oc-card-ic.warning{background:var(--warning-light);color:#d97706;}
  .oc-card-ttl{font-size:16px;font-weight:900;color:#111827;line-height:1.2;margin:0;}
  .oc-card-sub{font-size:13px;color:var(--text-muted);margin-top:4px;line-height:1.5;}
  .oc-card-b{padding:18px;}

  .oc-label{
    display:block;
    font-size:12px;
    font-weight:800;
    color:var(--text-main);
    text-transform:uppercase;
    letter-spacing:.04em;
    margin-bottom:6px;
  }
  .oc-label .required{color:var(--danger);}
  .oc-input,.oc-select,.oc-textarea{
    width:100%;
    padding:10px 12px;
    border-radius:10px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
    color:#111827;
  }
  .oc-input:focus,.oc-select:focus,.oc-textarea:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }
  .oc-textarea{min-height:110px;resize:vertical;}
  .oc-help{font-size:12px;color:var(--text-muted);margin-top:6px;line-height:1.45;}
  .oc-field-error{margin-top:6px;font-size:12px;color:#b91c1c;font-weight:800;}
  .oc-input.is-invalid,.oc-select.is-invalid,.oc-textarea.is-invalid{
    border-color:#fb7185;
    box-shadow:0 0 0 3px rgba(251,113,133,.18);
  }

  .oc-chip-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;}
  .oc-chip{
    display:inline-flex;align-items:center;gap:6px;
    padding:6px 10px;border-radius:999px;
    background:var(--blue-light);color:var(--blue);
    border:1px solid rgba(116,178,212,.18);
    font-size:12px;font-weight:800;
  }

  .oc-summary{
    display:grid;
    gap:12px;
    font-size:14px;
    color:var(--text-main);
  }
  .oc-summary-block{
    border:1px solid var(--border);
    border-radius:14px;
    background:#fff;
    padding:14px;
  }
  .oc-summary h4{
    margin:0 0 8px 0;
    font-size:13px;
    font-weight:900;
    color:#111827;
    text-transform:uppercase;
    letter-spacing:.05em;
  }
  .oc-summary .row{display:grid;gap:6px;}
  .oc-summary .muted{color:var(--text-muted);font-weight:800;}

  .oc-actions{
    display:flex;
    justify-content:space-between;
    gap:10px;
    margin-top:16px;
    flex-wrap:wrap;
  }

  .wizard-step{display:none;}
  .wizard-step.active{display:block;}

  .mw-object-result{display:grid;gap:2px;font-size:.80rem;}
  .mw-object-result-main{font-weight:900;color:#111827;display:flex;align-items:center;gap:8px;}
  .mw-object-result-kno{
    font-size:.70rem;padding:2px 8px;border-radius:999px;border:1px solid var(--border);
    background:var(--blue-light);font-weight:900;color:var(--text-main);
  }
  .mw-object-result-sub,.mw-object-result-product{display:flex;align-items:center;gap:6px;color:var(--text-muted);}
  .mw-object-result-icon{width:14px;text-align:center;}
  .mw-object-result-badge{
    display:inline-flex;align-items:center;padding:0 8px;border-radius:999px;font-size:.70rem;
    border:1px solid rgba(15,23,42,.12);margin-left:6px;background:var(--primary-light);font-weight:900;color:#365314;
  }

  .select2-container--default .select2-selection--single{
    border-radius:10px;border:1px solid var(--border);height:42px;padding:5px 6px;font-size:14px;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:30px;color:#111827;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow{height:38px;}
  .select2-container--default .select2-selection--multiple{
    border-radius:10px;border:1px solid var(--border);min-height:42px;padding:4px 6px;
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">WARTUNGSVERTRAG ANLEGEN</div>
        <div class="oc-sub">Erstellen Sie neue Wartungsverträge mit Kundenbezug, Filiale, Verantwortlichen, Checkliste und Technikerplanung.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <a href="{{ route('admin.maintenance.contracts.index') }}">Wartungsverträge</a>
          <span>›</span>
          <span class="current">Neuer Wartungsvertrag</span>
        </div>
      </div>

      <div class="oc-inline-actions">
        <a href="{{ route('admin.maintenance.contracts.index') }}" class="oc-btn-soft">
          <i class="fa-solid fa-arrow-left"></i>
          Zur Übersicht
        </a>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total"><i class="fa-solid fa-file-signature"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Vorgang</div>
        <div class="oc-stat-value">3</div>
        <div class="oc-stat-sub">Schritte bis zum Speichern</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon context"><i class="fa-solid fa-diagram-project"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Kontext</div>
        <div class="oc-stat-value">{{ $contextCount }}</div>
        <div class="oc-stat-sub">Vorbelegte Datensätze erkannt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon flow"><i class="fa-solid fa-building"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Pflichtlogik</div>
        <div class="oc-stat-value">Filiale</div>
        <div class="oc-stat-sub">Zwingend vor dem Speichern</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon safe"><i class="fa-solid fa-shield-halved"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Validierung</div>
        <div class="oc-stat-value">Aktiv</div>
        <div class="oc-stat-sub">Frontend- und Backend-Prüfung</div>
      </div>
    </div>
  </div>

  @if($errors->any())
    <div class="oc-alert danger">
      <strong>Speichern nicht möglich.</strong>
      <div>Bitte die markierten Felder prüfen.</div>
      <ul>
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(isset($lead) || isset($alternative) || isset($asset))
    <div class="oc-alert info">
      <strong>Kontext erkannt.</strong> Vorbelegte Daten aus Lead, Objekt oder Anlage wurden geladen.
      <div class="oc-chip-row">
        @if(isset($lead)) <div class="oc-chip">Lead-ID: {{ $lead->id }}</div> @endif
        @if(isset($alternative)) <div class="oc-chip">Adresse-ID: {{ $alternative->id }}</div> @endif
        @if(isset($asset)) <div class="oc-chip">Asset-ID: {{ $asset->id }}</div> @endif
      </div>

      <div style="margin-top:10px;display:grid;gap:6px;">
        @if(isset($lead))
          <div>
            <strong>Kunde:</strong>
            {{ $lead->firma ?: trim(($lead->name ?? '').' '.($lead->lastname ?? '')) }}
            <span style="color:#6b7280;">· {{ $lead->street }} · {{ $lead->postcode }} {{ $lead->city }}</span>
          </div>
        @endif

        @if(isset($alternative))
          <div>
            <strong>Objekt:</strong>
            {{ $alternative->object_name ?: 'Alternative Adresse' }}
            <span style="color:#6b7280;">· {{ $alternative->street }} · {{ $alternative->postcode }} {{ $alternative->city }}</span>
          </div>
        @endif
      </div>
    </div>
  @endif

  <div class="oc-stepper">
    <div class="oc-step is-active" data-step-indicator="1">
      <div class="oc-step-circle">1</div>
      <div>
        <div class="oc-step-label-main">Schritt 1</div>
        <div class="oc-step-label-sub">Kunde & Anlage</div>
      </div>
    </div>

    <div class="oc-step" data-step-indicator="2">
      <div class="oc-step-circle">2</div>
      <div>
        <div class="oc-step-label-main">Schritt 2</div>
        <div class="oc-step-label-sub">Vertrag</div>
      </div>
    </div>

    <div class="oc-step" data-step-indicator="3">
      <div class="oc-step-circle">3</div>
      <div>
        <div class="oc-step-label-main">Schritt 3</div>
        <div class="oc-step-label-sub">Checkliste & Techniker</div>
      </div>
    </div>
  </div>

  <form id="wizard-form" method="POST" action="{{ $storeAction }}" novalidate>
    @csrf

    <input type="hidden" id="mw-customer-id" name="lead_id" value="{{ old('lead_id', $lead->id ?? '') }}">
    <input type="hidden" id="mw-alternative-id" name="alternative_id" value="{{ old('alternative_id', $alternative->id ?? '') }}">
    <input type="hidden" id="mw-product-id" name="product_id" value="{{ old('product_id', $asset->product_id ?? '') }}">
    <input type="hidden" id="mw-branch-id" name="branch_id" value="{{ old('branch_id') }}">
    <input type="hidden" id="mw-wizard-payload" name="wizard_payload" value="{{ old('wizard_payload') }}">
    <input type="hidden" id="mw-asset-id" name="asset_id" value="{{ old('asset_id', $asset->id ?? '') }}">
    <input type="hidden" id="mw-lead-product-list-id" name="lead_product_list_id" value="{{ old('lead_product_list_id') }}">

    {{-- STEP 1 --}}
    <section class="wizard-step active" data-step="1">
      <div class="oc-grid-2">
        <div>
          <div class="oc-card">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic"><i class="fa-solid fa-house-circle-check"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Kunde, Objekt & Anlage</h3>
                  <div class="oc-card-sub">Schnelle Suche nach Kunde, Adresse und Produkt mit automatischer Vorbelegung.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div style="margin-bottom:14px;">
                <label class="oc-label">Kunde / Objekt / Produkt <span class="required">*</span></label>
                <select id="mw-customer-search" class="mw-object-search-select" style="width:100%;"></select>
                <div class="oc-help">Mindestens 2 Zeichen eingeben: Name, Kundennummer, Adresse oder Produkt.</div>
                <div class="oc-field-error" id="mw-err-customer" style="display:none;"></div>
              </div>

              <div class="oc-grid-2-compact">
                <div>
                  <label class="oc-label">Anlagentyp (intern)</label>
                  <input type="hidden" id="mw-system-type-code" name="systemType" value="{{ old('systemType') }}">
                  <input type="text" id="mw-system-type" class="oc-input" placeholder="Wird aus dem Produkt übernommen" readonly>
                </div>

                <div>
                  <label class="oc-label">Anlagentyp (Klartext)</label>
                  <input type="text" id="mw-system-type-label" name="systemTypeLabel" class="oc-input"
                         value="{{ old('systemTypeLabel') }}" placeholder="z. B. Luft-Wasser-Wärmepumpe">
                  @error('systemTypeLabel') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="oc-label">Produkt / Modell <span class="required">*</span></label>
                  <input type="text" id="mw-product-model" name="productModel" class="oc-input"
                         value="{{ old('productModel') }}" placeholder="z. B. HP-12kW Pro">
                  @error('productModel') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="oc-label">Seriennummer(n)</label>
                  <input type="text" id="mw-serial-numbers" name="serialNumbers" class="oc-input"
                         value="{{ old('serialNumbers') }}" placeholder="z. B. SN12345, SN67890">
                  <div class="oc-help">Mehrere Seriennummern per Komma trennen.</div>
                  @error('serialNumbers') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>
              </div>

              <div style="margin-top:14px;">
                <label class="oc-label">Anlagenadresse</label>
                <input type="text" id="mw-installation-address" name="installationAddressText" class="oc-input"
                       value="{{ old('installationAddressText') }}" placeholder="Straße, PLZ, Ort">
                <div class="oc-help">Wird übernommen und kann angepasst werden.</div>
                @error('installationAddressText') <div class="oc-field-error">{{ $message }}</div> @enderror
              </div>

              <div class="oc-alert info" style="margin-top:14px;margin-bottom:0;">
                <strong>Hinweis:</strong> Ohne Kunden- oder Objektbezug, Produkt/Modell und Filiale ist kein Speichern möglich.
              </div>
            </div>
          </div>
        </div>

        <div>
          <div class="oc-card soft">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic success"><i class="fa-solid fa-building-circle-check"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Zuständige Filiale</h3>
                  <div class="oc-card-sub">Steuert Zuständigkeiten, Servicegebiet und interne Kommunikation.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div style="margin-bottom:14px;">
                <label class="oc-label">Filiale <span class="required">*</span></label>
                <select id="mw-branch-search" class="mw-object-search-select" style="width:100%;"></select>
                <div class="oc-help">Mindestens 1 Zeichen eingeben: Name, PLZ oder Ort.</div>
                <div class="oc-field-error" id="mw-err-branch" style="display:none;"></div>
              </div>

              <div style="display:grid;gap:14px;">
                <div>
                  <label class="oc-label">Filialname</label>
                  <input type="text" id="mw-branch-name" class="oc-input" value="Keine Filiale ausgewählt" readonly>
                </div>

                <div>
                  <label class="oc-label">Filialadresse</label>
                  <div id="mw-branch-address" class="oc-help" style="font-size:14px;">Noch keine Filiale gewählt.</div>
                </div>

                <div class="oc-grid-2-compact">
                  <div>
                    <label class="oc-label">E-Mail</label>
                    <div class="oc-help" id="mw-branch-email" style="font-size:14px;">–</div>
                  </div>
                  <div>
                    <label class="oc-label">Telefon</label>
                    <div class="oc-help" id="mw-branch-phone" style="font-size:14px;">–</div>
                  </div>
                </div>

                <div class="oc-alert info" style="margin-bottom:0;">
                  <strong>Hinweis:</strong> <span id="mw-branch-note">Die Filiale bestimmt SLA, Einsatzgebiet und Ansprechpartner.</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="oc-actions">
        <button type="button" class="oc-btn-soft" id="mw-reset-step1">
          <i class="fa-solid fa-rotate-left"></i>
          Auswahl zurücksetzen
        </button>

        <button type="button" class="oc-btn" data-action="next" data-step="1">
          Weiter
          <i class="fa-solid fa-arrow-right-long"></i>
        </button>
      </div>
    </section>

    {{-- STEP 2 --}}
    <section class="wizard-step" data-step="2">
      <div class="oc-grid-2">
        <div>
          <div class="oc-card">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic"><i class="fa-solid fa-file-signature"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Vertragsdaten</h3>
                  <div class="oc-card-sub">Pflichtfelder müssen vollständig sein, bevor gespeichert wird.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div class="oc-grid-3">
                <div>
                  <label class="oc-label">Vertragsnummer (intern)</label>
                  <input type="text" name="contractNumber" class="oc-input" value="{{ old('contractNumber') }}" placeholder="z. B. WV-2026-0010">
                  @error('contractNumber') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="oc-label">Vertragsbeginn <span class="required">*</span></label>
                  <input type="date" name="contractStartDate" class="oc-input" value="{{ old('contractStartDate') }}">
                  @error('contractStartDate') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="oc-label">Laufzeit (Monate) <span class="required">*</span></label>
                  <input type="number" name="contractDurationMonths" class="oc-input" min="1" value="{{ old('contractDurationMonths') }}" placeholder="z. B. 12">
                  @error('contractDurationMonths') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="oc-label">Abrechnungsmodus <span class="required">*</span></label>
                  <select name="billingMode" class="oc-select">
                    <option value="">Bitte wählen</option>
                    <option value="yearly" @selected(old('billingMode')==='yearly')>Jährlich pauschal</option>
                    <option value="one_time" @selected(old('billingMode')==='one_time')>Einmalig</option>
                    <option value="time_and_material" @selected(old('billingMode')==='time_and_material')>Nach Aufwand</option>
                  </select>
                  @error('billingMode') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="oc-label">Jährlicher Wartungspreis brutto (€)</label>
                  <input type="number" step="0.01" min="0" name="yearlyPriceGross" class="oc-input" value="{{ old('yearlyPriceGross') }}" placeholder="z. B. 249,00">
                  @error('yearlyPriceGross') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                  <label class="oc-label">Nächste vertragliche Wartung</label>
                  <input type="date" name="nextScheduledServiceDate" class="oc-input" value="{{ old('nextScheduledServiceDate') }}">
                  @error('nextScheduledServiceDate') <div class="oc-field-error">{{ $message }}</div> @enderror
                </div>
              </div>

              <div style="margin-top:14px;">
                <label class="oc-label">Verantwortliche Person <span class="required">*</span></label>
                <select name="responsible_employee_id" id="mw-responsible-employee" class="oc-select" style="width:100%;"></select>
                <div class="oc-help">Interner Hauptansprechpartner.</div>
                @error('responsible_employee_id') <div class="oc-field-error">{{ $message }}</div> @enderror
              </div>

              <div style="margin-top:14px;">
                <label class="oc-label">Interne Notizen</label>
                <textarea name="contractInternalNotes" class="oc-textarea" placeholder="Sonderkonditionen, Besonderheiten, Rückfragen...">{{ old('contractInternalNotes') }}</textarea>
                @error('contractInternalNotes') <div class="oc-field-error">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          <div class="oc-card soft" style="margin-top:16px;">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic success"><i class="fa-regular fa-envelope"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Bestätigungs-E-Mail</h3>
                  <div class="oc-card-sub">Optionaler Hinweistext für die automatische Nachricht.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <label class="oc-label">Hinweistext für die Kunden-E-Mail</label>
              <textarea name="customerEmailNote" class="oc-textarea" placeholder="Zusätzlicher Hinweis für den Kunden...">{{ old('customerEmailNote') }}</textarea>
              @error('customerEmailNote') <div class="oc-field-error">{{ $message }}</div> @enderror

              <div class="oc-help" style="margin-top:8px;">
                Der Kunde erhält einen Link zur Einsicht oder Bestätigung, sofern das im Backend aktiv ist.
              </div>

              <label style="display:inline-flex;align-items:center;gap:10px;margin-top:12px;font-size:14px;color:var(--text-main);font-weight:800;">
                <input type="checkbox" name="sendEmail" value="1" @checked(old('sendEmail', '1')=='1')>
                <span>E-Mail nach dem Speichern senden</span>
              </label>
            </div>
          </div>
        </div>

        <div>
          <div class="oc-card soft">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic warning"><i class="fa-solid fa-eye"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Review</h3>
                  <div class="oc-card-sub">Live-Zusammenfassung der bisherigen Eingaben.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div id="mw-summary-preview" class="oc-summary">
                <div class="oc-alert info" style="margin-bottom:0;">Die Zusammenfassung wird automatisch aktualisiert.</div>
              </div>
            </div>
          </div>

          <div class="oc-card" style="margin-top:16px;">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic"><i class="fa-solid fa-lock"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Datenqualität</h3>
                  <div class="oc-card-sub">Pflichtfelder werden vor dem Speichern geprüft.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div class="oc-help" style="font-size:14px;">
                Wenn „Weiter“ oder „Speichern“ nicht funktioniert, fehlen meist:
                <strong>Kunde/Objekt</strong>, <strong>Filiale</strong>, <strong>Produkt/Modell</strong> oder <strong>Verantwortliche Person</strong>.
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="oc-actions">
        <button type="button" class="oc-btn-soft" data-action="prev" data-step="2">
          <i class="fa-solid fa-arrow-left-long"></i>
          Zurück
        </button>

        <button type="button" class="oc-btn" data-action="next" data-step="2">
          Weiter
          <i class="fa-solid fa-arrow-right-long"></i>
        </button>
      </div>
    </section>

    {{-- STEP 3 --}}
    <section class="wizard-step" data-step="3">
      <div class="oc-grid-2">
        <div>
          <div class="oc-card">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic"><i class="fa-solid fa-list-check"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Wartungs-Checkliste</h3>
                  <div class="oc-card-sub">Wähle eine aktive Checkliste. Die Items werden zur Kontrolle geladen.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div style="margin-bottom:14px;">
                <label class="oc-label">Checkliste <span class="required">*</span></label>
                <select id="mw-checklist-search" class="mw-object-search-select" style="width:100%;"></select>
                <div class="oc-help">Suche nach Titel oder Beschreibung.</div>
                <div class="oc-field-error" id="mw-err-checklist" style="display:none;"></div>
              </div>

              <div class="oc-alert info" style="margin-bottom:0;">
                <strong>Vorschau (Items)</strong>
                <div id="mw-checklist-preview" class="oc-help" style="margin-top:8px;">Keine Checkliste ausgewählt.</div>
              </div>
            </div>
          </div>
        </div>

        <div>
          <div class="oc-card soft">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic success"><i class="fa-solid fa-user-gear"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Techniker Planung</h3>
                  <div class="oc-card-sub">Haupttechniker und optionale Zusatztechniker.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div style="margin-bottom:14px;">
                <label class="oc-label">Haupttechniker <span class="required">*</span></label>
                <select id="mw-main-technician" class="oc-select" style="width:100%;"></select>
                <div class="oc-field-error" id="mw-err-maintech" style="display:none;"></div>
              </div>

              <div style="margin-bottom:14px;">
                <label class="oc-label">Zusatztechniker (optional)</label>
                <select id="mw-additional-technicians" class="oc-select" multiple style="width:100%;"></select>
                <div class="oc-help">Mehrfachauswahl möglich.</div>
              </div>

              <div class="oc-grid-2-compact">
                <div>
                  <label class="oc-label">Geplantes Datum</label>
                  <input type="date" id="mw-maintenance-date" class="oc-input">
                </div>
                <div>
                  <label class="oc-label">Zeitfenster</label>
                  <div class="oc-grid-2-compact">
                    <input type="time" id="mw-maintenance-time-from" class="oc-input">
                    <input type="time" id="mw-maintenance-time-to" class="oc-input">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="oc-card soft" style="margin-top:16px;">
            <div class="oc-card-h">
              <div class="oc-card-h-main">
                <div class="oc-card-ic warning"><i class="fa-solid fa-eye"></i></div>
                <div>
                  <h3 class="oc-card-ttl">Finale Kontrolle</h3>
                  <div class="oc-card-sub">Letzte Prüfung vor dem Speichern.</div>
                </div>
              </div>
            </div>

            <div class="oc-card-b">
              <div id="mw-summary-preview-step3" class="oc-summary">
                <div class="oc-alert info" style="margin-bottom:0;">Die Zusammenfassung wird automatisch aktualisiert.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="oc-actions">
        <button type="button" class="oc-btn-soft" data-action="prev" data-step="3">
          <i class="fa-solid fa-arrow-left-long"></i>
          Zurück
        </button>

        <button type="submit" class="oc-btn js-mw-submit">
          <span class="mw-spinner" style="display:none;"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
          <span class="mw-btn-text"><i class="fa-solid fa-check"></i> Vertrag speichern</span>
        </button>
      </div>
    </section>
  </form>
</div>
@endsection

@section('script')
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

  const State = {
    checklist: { id: null, title: null, items: [] },
    technicians: { mainId: null, additionalIds: [] }
  };

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
      window.scrollTo({ top: 0, behavior: "smooth" });
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

      return ok;
    }

    function validateStep2() {
      const responsibleId = getValById("mw-responsible-employee");
      return !!responsibleId;
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

    function summaryBlock(title, rows){
      return (
        '<div class="oc-summary-block">' +
          '<h4>' + esc(title) + '</h4>' +
          '<div class="row">' + rows.join('') + '</div>' +
        '</div>'
      );
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
        summaryBlock("Kunde & Anlage", [
          '<div><span class="muted">Kunde/Objekt:</span> ' + esc(customerText) + '</div>',
          '<div><span class="muted">Adresse:</span> ' + esc(address) + '</div>',
          '<div><span class="muted">Produkt:</span> ' + esc(productModel) + '</div>'
        ]) +
        summaryBlock("Filiale", [
          '<div><span class="muted">Filialname:</span> ' + esc(branchName) + '</div>'
        ]) +
        summaryBlock("Vertrag", [
          '<div><span class="muted">Vertragsnummer:</span> ' + esc(contractNumber) + '</div>',
          '<div><span class="muted">Beginn:</span> ' + esc(start) + '</div>',
          '<div><span class="muted">Laufzeit:</span> ' + esc(duration) + ' Monate</div>',
          '<div><span class="muted">Nächste Wartung:</span> ' + esc(nextService) + '</div>',
          '<div><span class="muted">Verantwortlich:</span> ' + esc(responsibleText) + '</div>'
        ]) +
        summaryBlock("Checkliste & Techniker", [
          '<div><span class="muted">Checkliste:</span> ' + esc(checklistTitle) + '</div>',
          '<div><span class="muted">Haupttechniker:</span> ' + esc(mainTechText) + '</div>',
          '<div><span class="muted">Planung:</span> ' + esc(date) + ' · ' + esc(tFrom) + '–' + esc(tTo) + '</div>'
        ]);

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
          internalNotes: internalNotes || null,
          responsibleEmployeeId: responsibleEmployeeId ? Number(responsibleEmployeeId) : null
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
        el.addEventListener("input", renderSummaryPreview);
        el.addEventListener("change", renderSummaryPreview);
      });

      if (window.jQuery) {
        window.jQuery("#mw-responsible-employee").on("change", renderSummaryPreview);
        window.jQuery("#mw-main-technician").on("change", renderSummaryPreview);
        window.jQuery("#mw-additional-technicians").on("change", renderSummaryPreview);
      }
    }

    function attachSubmitGuard() {
      const form = document.getElementById("wizard-form");
      const payloadInput = document.getElementById("mw-wizard-payload");
      const submitButtons = qsa(".js-mw-submit");
      if (!form) return;

      form.addEventListener("submit", function (e) {
        if (!validateStep1()) { e.preventDefault(); setActiveStep(1); return; }
        if (!validateStep2()) { e.preventDefault(); setActiveStep(2); return; }
        if (!validateStep3()) { e.preventDefault(); setActiveStep(3); return; }

        if (payloadInput) payloadInput.value = JSON.stringify(buildWizardPayload());

        submitButtons.forEach(btn => {
          btn.setAttribute("disabled", "disabled");
          const spinner = btn.querySelector(".mw-spinner");
          if (spinner) spinner.style.display = "inline-block";
        });
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

  function initSelect2($) {
    if (!$) return;

    const ROUTES = {
      customerSearch: @json(route('admin.maintenance.contracts.customer_search')),
      branchSearch:   @json(route('admin.maintenance.contracts.branch_search')),
      technicians:    @json(route('admin.maintenance.contracts.technicians')),
      checklistIndex: @json(route('admin.maintenance.contracts.checklists.ajax_index')),
      checklistShowBase: @json(url('/admin/maintenance/contracts/checklists'))
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
      html += '<div class="mw-object-result-main"><span>' + esc(customer) + '</span>';
      if (customerNo) html += '<span class="mw-object-result-kno">' + esc(customerNo) + '</span>';
      html += '</div>';

      if (address) {
        html += '<div class="mw-object-result-sub"><i class="fa-solid fa-location-dot mw-object-result-icon"></i><span>' + esc(address) + '</span></div>';
      }

      if (productName || productGroup) {
        html += '<div class="mw-object-result-product"><i class="fa-solid fa-box-open mw-object-result-icon"></i><span>' + esc(productName);
        if (productGroup) html += '<span class="mw-object-result-badge">' + esc(productGroup) + '</span>';
        html += '</span></div>';
      }

      html += '</div>';
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
      html += '<div class="mw-object-result-main"><span>' + esc(name) + '</span></div>';

      if (city || postcode) {
        html += '<div class="mw-object-result-sub"><i class="fa-solid fa-location-dot mw-object-result-icon"></i><span>' + esc([postcode, city].filter(Boolean).join(" ")) + '</span></div>';
      }

      if (email || phone) {
        html += '<div class="mw-object-result-product"><i class="fa-regular fa-envelope mw-object-result-icon"></i><span>' + esc([email, phone].filter(Boolean).join(" · ")) + '</span></div>';
      }

      html += '</div>';
      return html;
    }

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

    $.ajax({ url: ROUTES.technicians, type: "GET", dataType: "json" })
      .done(function (resp) {
        const employees = (resp && resp.employees) ? resp.employees : [];

        const $respSel = $("#mw-responsible-employee");
        if ($respSel.length) {
          $respSel.empty().append('<option value="">Bitte wählen</option>');
          employees.forEach(emp => $respSel.append($("<option></option>").val(emp.id).text(emp.name + (emp.meta ? (" – " + emp.meta) : ""))));
          $respSel.select2({ width:"100%", placeholder:"Verantwortliche Person wählen", allowClear:true });

          const oldVal = @json(old('responsible_employee_id'));
          if (oldVal) $respSel.val(String(oldVal)).trigger("change");
        }

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