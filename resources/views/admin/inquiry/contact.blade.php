@extends('admin.layouts.app')

@section('title') ANFRAGE AUFNAHME @endsection

@section('style')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root {
        --brand-green: #8dc53d;
        --brand-blue: #73b1d4;
        --brand-lightblue: #8dbfdc;
        --brand-text: #2c3e50;
    }

    body { margin: 0; font-family: 'Roboto', sans-serif; }
    
    /* Global Overrides */
    .btn-primary { background-color: var(--brand-blue) !important; border-color: var(--brand-blue) !important; color: #fff; }
    .btn-primary:hover { background-color: var(--brand-lightblue) !important; border-color: var(--brand-lightblue) !important; }
    
    .btn-success { background-color: var(--brand-green) !important; border-color: var(--brand-green) !important; }
    .btn-success:hover { filter: brightness(0.95); }

    .card { border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .card-header { background: transparent; border-bottom: 1px solid #eee; padding: 1.5rem; }
    .card-header h5 { font-weight: 700; color: var(--brand-text); font-size: 1.1rem; margin:0; display: flex; align-items: center; gap: 10px; }
    .card-header i { color: var(--brand-blue); font-size: 1.2rem; }

    /* Layout specific */
    .form-section-sidebar { max-height: 85vh; overflow-y: auto; padding-right: 5px; }
    
    /* Calendar Specifics */
    #inquiry-calendar-container {
        background: white;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        height: 85vh; /* Full screen height feel */
    }

    .fc { font-family: 'Roboto', sans-serif; }
    .fc-toolbar-title { font-size: 1.5rem !important; color: var(--brand-text); font-weight: 700; }
    .fc-button-primary { background-color: var(--brand-blue) !important; border-color: var(--brand-blue) !important; }
    .fc-button-active { background-color: var(--brand-green) !important; border-color: var(--brand-green) !important; }
    .fc-day-today { background-color: rgba(141, 197, 61, 0.08) !important; } /* Light green background for today */
    
    /* Event Styling for "More Details" */
    .fc-event {
        border: none !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 4px;
        transition: transform 0.1s ease;
    }
    .fc-event:hover { transform: scale(1.01); z-index: 50; }
    
    .fc-event-main-frame {
        display: flex;
        flex-direction: column;
    }
    
    .custom-event-content {
        padding: 2px;
    }
    .custom-event-time { font-size: 0.75rem; font-weight: bold; opacity: 0.9; }
    .custom-event-title { font-size: 0.85rem; font-weight: 700; margin-bottom: 2px; }
    .custom-event-details { font-size: 0.75rem; opacity: 0.9; line-height: 1.2; display: block; border-top: 1px solid rgba(255,255,255,0.3); margin-top: 2px; padding-top: 2px; }

    /* Inputs */
    .form-control { border-radius: 8px; border: 1px solid #ddd; padding: 10px 12px; }
    .form-control:focus { border-color: var(--brand-blue); box-shadow: 0 0 0 3px rgba(115, 177, 212, 0.2); }
    
    .select2-container .select2-selection--single { height: 38px !important; border-radius: 8px !important; border: 1px solid #ddd !important; }
    .select2-selection__rendered { line-height: 36px !important; padding-left: 12px !important; }
    .select2-selection__arrow { height: 36px !important; }

    /* Custom Tables */
    #inquiryProductTable th { background-color: #f8f9fa; border-top: none; color: #666; font-weight: 600; }
    #inquiryProductTable td { vertical-align: middle; }
    
    /* Modals */
    .verify-dot { color: var(--brand-blue); }
    .verify-item.ok .verify-dot { background: rgba(141, 197, 61, 0.15); border-color: rgba(141, 197, 61, 0.25); color: var(--brand-green); }
    .verify-progress-bar { background: linear-gradient(90deg, var(--brand-green), var(--brand-blue)); }

    /* Utility */
    .hidden { display: none !important; }
    .text-brand-blue { color: var(--brand-blue); }
    .bg-brand-green { background-color: var(--brand-green); }
</style>

<style>
/* ... KEEP VERIFY MODAL CSS ... */
.verify-modal.hidden{display:none}
.verify-modal{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:18px}
.verify-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(6px)}
.verify-dialog{position:relative;width:min(720px,96vw);background:#fff;border-radius:18px;box-shadow:0 20px 70px rgba(0,0,0,.35);overflow:hidden;border:1px solid rgba(0,0,0,.08)}
.verify-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;padding:18px 18px 10px;border-bottom:1px solid #eee;background:linear-gradient(180deg,#fbfbfb,#fff)}
.verify-kicker{font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#7b7b7b}
.verify-title{margin:4px 0 0;font-weight:800;font-size:18px;color:#222}
.verify-sub{margin-top:6px;font-size:13px;color:#666}
.verify-x{width:42px;height:42px;border-radius:12px;border:1px solid #eee;background:#fff;display:flex;align-items:center;justify-content:center}
.verify-x:hover{background:#f7f7f7}

.verify-body{padding:14px 18px 16px}
.verify-progress{height:10px;background:#f2f2f2;border-radius:999px;overflow:hidden;border:1px solid #eee}
.verify-progress-bar{height:100%;border-radius:999px;background:linear-gradient(90deg,#95c11f,#73b1d4);transition:width .25s ease}

.verify-existing{margin-top:12px;border:1px solid rgba(229,0,86,.25);background:rgba(229,0,86,.05);border-radius:14px;padding:12px}
.verify-existing .t{font-weight:800;color:#b00042}
.verify-existing .p{font-size:13px;color:#444;margin-top:6px}
.verify-existing .a{margin-top:10px;display:flex;gap:10px;flex-wrap:wrap}

.verify-list{margin-top:14px;display:grid;gap:10px}
.verify-item{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border-radius:14px;border:1px solid #eee;background:#fff}
.verify-item .l{display:flex;align-items:center;gap:10px}
.verify-dot{width:36px;height:36px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border:1px solid #eee}
.verify-item.ok{border-color:rgba(149,193,31,.35);background:rgba(149,193,31,.06)}
.verify-item.ok .verify-dot{background:rgba(149,193,31,.15);border-color:rgba(149,193,31,.25)}
.verify-item.bad{border-color:rgba(229,0,86,.25);background:rgba(229,0,86,.04)}
.verify-item.bad .verify-dot{background:rgba(229,0,86,.12);border-color:rgba(229,0,86,.18)}
.verify-item .txt{font-weight:700;color:#222}
.verify-item .st{font-size:12px;color:#666}

.verify-missing{margin-top:12px;border-radius:14px;padding:12px;border:1px dashed rgba(229,0,86,.35);background:rgba(229,0,86,.04)}
.verify-missing ul{margin:8px 0 0 18px;color:#444;font-size:13px}
.verify-pulse {
  outline: 3px solid rgba(229,0,86,.45);
  box-shadow: 0 0 0 6px rgba(229,0,86,.12);
  border-radius: 8px;
  transition: all .2s ease;
}

.verify-foot{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 18px 16px;border-top:1px solid #eee;background:#fff}
.verify-btn-loading i{animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.hidden{display:none!important}
</style>

<style>
/* APM MODAL CSS - Kept as is */
  .apm-modal.hidden{display:none}
  .apm-modal{position:fixed;inset:0;z-index:10050;display:flex;align-items:center;justify-content:center;padding:18px}
  .apm-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(6px)}
  .apm-dialog{position:relative;width:min(980px,96vw);max-height:92vh;background:#fff;border-radius:18px;box-shadow:0 20px 70px rgba(0,0,0,.35);overflow:hidden;border:1px solid rgba(0,0,0,.08);display:flex;flex-direction:column}
  .apm-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;padding:16px 16px 10px;border-bottom:1px solid #eee;background:linear-gradient(180deg,#fbfbfb,#fff)}
  .apm-kicker{font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#7b7b7b}
  .apm-title{margin:4px 0 0;font-weight:800;font-size:18px;color:#222}
  .apm-sub{margin-top:6px;font-size:13px;color:#666}
  .apm-x{width:42px;height:42px;border-radius:12px;border:1px solid #eee;background:#fff;display:flex;align-items:center;justify-content:center}
  .apm-x:hover{background:#f7f7f7}
  .apm-body{padding:14px 16px;overflow:auto}
  .apm-grid{display:grid;grid-template-columns: 1.15fr .85fr;gap:12px}
  @media(max-width: 992px){ .apm-grid{grid-template-columns:1fr} }
  .apm-card{border:1px solid #eee;border-radius:14px;background:#fff}
  .apm-card .apm-card-h{padding:10px 12px;border-bottom:1px solid #f1f1f1;display:flex;align-items:center;justify-content:space-between}
  .apm-card .apm-card-b{padding:12px}
  .apm-card h6{margin:0;font-weight:800;font-size:13px;color:#222}
  .apm-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  @media(max-width: 576px){ .apm-row{grid-template-columns:1fr} }
  .apm-chipwrap{display:flex;flex-wrap:wrap;gap:8px}
  .apm-chip{display:inline-flex;align-items:center;gap:8px;border:1px solid #eee;background:#fafafa;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;color:#222}
  .apm-chip .dot{width:10px;height:10px;border-radius:99px;background:#95c11f}
  .apm-chip .rm{border:0;background:transparent;cursor:pointer;color:#777}
  .apm-chip .rm:hover{color:#111}
  .apm-table{width:100%;border-collapse:separate;border-spacing:0}
  .apm-table th,.apm-table td{padding:8px 10px;border-bottom:1px solid #f1f1f1;font-size:13px}
  .apm-table th{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#777}
  .apm-empty{padding:10px 12px;border:1px dashed #ddd;border-radius:12px;background:#fcfcfc;color:#666;font-size:13px}
  .apm-foot{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;border-top:1px solid #eee;background:#fff}
  .apm-foot .left{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
  .apm-foot .right{display:flex;align-items:center;gap:10px}
</style>
<style>
    /* Customer Details Modal (matches your modal style) */
    .cs-modal.hidden{display:none}
    .cs-modal{position:fixed;inset:0;z-index:10060;display:flex;align-items:center;justify-content:center;padding:18px}
    .cs-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(6px)}
    .cs-dialog{position:relative;width:min(920px,96vw);max-height:92vh;background:#fff;border-radius:18px;box-shadow:0 20px 70px rgba(0,0,0,.35);overflow:hidden;border:1px solid rgba(0,0,0,.08);display:flex;flex-direction:column}
    .cs-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;padding:16px 16px 10px;border-bottom:1px solid #eee;background:linear-gradient(180deg,#fbfbfb,#fff)}
    .cs-kicker{font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#7b7b7b}
    .cs-title{margin:4px 0 0;font-weight:800;font-size:18px;color:#222}
    .cs-sub{margin-top:6px;font-size:13px;color:#666}
    .cs-x{width:42px;height:42px;border-radius:12px;border:1px solid #eee;background:#fff;display:flex;align-items:center;justify-content:center}
    .cs-x:hover{background:#f7f7f7}
    .cs-body{padding:14px 16px;overflow:auto}
    .cs-foot{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;border-top:1px solid #eee;background:#fff}
    .cs-foot .right{display:flex;align-items:center;gap:10px}

    .cs-grid{display:grid;grid-template-columns: 1.1fr .9fr;gap:12px}
    @media(max-width: 992px){ .cs-grid{grid-template-columns:1fr} }
    .cs-card{border:1px solid #eee;border-radius:14px;background:#fff}
    .cs-card-h{padding:10px 12px;border-bottom:1px solid #f1f1f1;display:flex;align-items:center;justify-content:space-between}
    .cs-card-b{padding:12px}
    .cs-card h6{margin:0;font-weight:800;font-size:13px;color:#222}
    .cs-kpis{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
    @media(max-width: 576px){ .cs-kpis{grid-template-columns:repeat(2,minmax(0,1fr))} }
    .cs-kpi{border:1px solid #eee;border-radius:14px;background:#fafafa;padding:10px}
    .cs-kpi .v{font-weight:900;font-size:18px;color:#222}
    .cs-kpi .l{font-size:12px;color:#666;font-weight:700;margin-top:2px}

    .cs-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    @media(max-width: 576px){ .cs-row{grid-template-columns:1fr} }
    .cs-f{border:1px solid #eee;border-radius:12px;padding:10px}
    .cs-f .k{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#777;font-weight:800}
    .cs-f .v{margin-top:4px;font-size:13px;color:#222;font-weight:700;word-break:break-word}

    .cs-table{width:100%;border-collapse:separate;border-spacing:0}
    .cs-table th,.cs-table td{padding:8px 10px;border-bottom:1px solid #f1f1f1;font-size:13px;vertical-align:top}
    .cs-table th{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#777}

    .cs-pill{display:inline-flex;align-items:center;gap:6px;border:1px solid #eee;border-radius:999px;background:#fff;padding:6px 10px;font-size:12px;font-weight:800;color:#222}
    .cs-dot{width:10px;height:10px;border-radius:99px;background:var(--brand-green)}

    .cs-skel{border:1px dashed #ddd;border-radius:14px;padding:12px;background:#fcfcfc}
    .cs-skel-line{height:12px;border-radius:999px;background:#eee;margin:10px 0}
    .cs-skel-line.w-60{width:60%}
    .cs-skel-line.w-80{width:80%}
    .cs-skel-line.w-90{width:90%}
    </style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper" style="max-width: 98%; margin: 0 auto;">
        
        <!-- Header Section -->
        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">ANFRAGE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a></li>
                                <li class="breadcrumb-item active">Anfrage Details</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-right">
                 <div id="autosaveStatus" class="badge badge-light-primary" style="font-size:12px;">Waiting...</div>
            </div>
        </div>

        <div class="content-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

           <form id="inquiryForm" class="leadForm form-horizontal custom-file-upload"
                 method="post"
                 action="{{ action('App\Http\Controllers\InquiryController@store') }}"
                 enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="inquiry_id" value="{{ (int) ($data->id ?? 0) }}"> 
              <input type="hidden" name="submit_mode" id="submit_mode" value="save">

              <div class="row">
                  <!-- LEFT COLUMN: Forms (Search, Contact, Address) -->
                  <div class="col-xl-3 col-lg-4 col-md-12">
                      <div class="form-section-sidebar"> 
                          {{-- Customer Search --}}
                          <div class="card mb-2">
                            <div class="card-header p-2">
                              <h5 class="mb-0 text-brand-blue"><i class="feather icon-search"></i> Kunde suchen</h5>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group mb-1">
                                    <select id="customerSearch" class="form-control" style="width:100%"></select>
                                </div>
                                <div class="row">
                                    <div class="col-6 pr-1">
                                         <button type="button" class="btn btn-primary btn-block btn-sm" id="btnCustomerSearch">Suchen</button>
                                    </div>
                                    <div class="col-6 pl-1">
                                        <button type="button" class="btn btn-outline-secondary btn-block btn-sm" id="btnCustomerClear">Reset</button>
                                    </div>
                                </div>
                            </div>
                          </div>


                          {{-- Put this right after your "Kunde suchen" card (inside the LEFT sidebar column) --}}
                            <div id="customerDetailsModal" class="cs-modal hidden" aria-hidden="true">
                              <div class="cs-backdrop" data-cs-close></div>

                              <div class="cs-dialog" role="dialog" aria-modal="true" aria-labelledby="csTitle">
                                <div class="cs-head">
                                  <div>
                                    <div class="cs-kicker">KUNDE</div>
                                    <h3 id="csTitle" class="cs-title">Kunde Details</h3>
                                    <div class="cs-sub" id="csSub">Lade…</div>
                                  </div>

                                  <button type="button" class="cs-x" data-cs-close title="Schließen">
                                    <i class="feather icon-x"></i>
                                  </button>
                                </div>

                                <div class="cs-body">
                                  <div id="csContent">
                                    <div class="cs-skel">
                                      <div class="cs-skel-line w-60"></div>
                                      <div class="cs-skel-line w-90"></div>
                                      <div class="cs-skel-line w-80"></div>
                                    </div>
                                  </div>
                                </div>

                                <div class="cs-foot">
                                  <div class="left">
                                    <span class="text-muted" id="csHint"></span>
                                  </div>
                                  <div class="right">
                                    <button type="button" class="btn btn-outline-secondary" data-cs-close>
                                      <i class="feather icon-x"></i> Schließen
                                    </button>
                                    <button type="button" class="btn btn-primary" id="csUseCustomer">
                                      <i class="feather icon-check"></i> Kundendaten übernehmen
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>

                            


                          {{-- Contact Details --}}
                          <div class="card mb-2">
                            <div class="card-header p-2">
                              <h5><i class="feather icon-user"></i> Kontakt Details</h5>
                            </div>
                            <div class="card-body p-2">
                              <div class="form-group mb-1">
                                <label>Art des Kontakts</label>
                                <select name="pre_type" class="form-control select2">
                                  <option value="">Auswählen</option>
                                  <option value="Kunde">Kunde</option>
                                  <option value="Lieferant">Lieferant</option>
                                  <option value="Hersteller">Hersteller</option>
                                  <option value="Kooperationspartner">Kooperationspartner</option>
                                  <option value="Architekt">Architekt</option>
                                  <option value="Nachunternehmer">Nachunternehmer</option>
                                  <option value="Termin">Terminkalender</option>
                                  <option value="Sonstige">Sonstige</option>
                                </select>
                              </div>

                              <div class="form-group mb-1">
                                <label>Betrieb</label>
                                <select name="branch_id" class="form-control select2">
                                  @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
                                  @endforeach
                                </select>
                              </div>

                              <div class="form-group mb-1">
                                <label>Quelle</label>
                                <select name="source" id="source" class="form-control text form-element">
                                  <option selected>Quelle auswählen</option>
                                  <option value="Telefonisch">Telefonisch</option>
                                  <option value="Persönlich">Persönlich</option>
                                  <option value="Mail">Mail</option>
                                  <option value="Empfehlung">Empfehlung</option>
                                  <option value="Solarrechner">Solarrechner</option>
                                </select>
                              </div>

                              <div class="form-group mb-1">
                                <label>Priorität</label>
                                <select name="periority" class="form-control select2">
                                  <option value="normal">Keine</option>
                                  <option value="Dringend">Dringend</option>
                                  <option value="Sehr Dringend">Sehr Dringend</option>
                                </select>
                              </div>
                            </div>
                          </div>

                          {{-- Person & Address --}}
                          <div class="card mb-2">
                            <div class="card-header p-2">
                              <h5><i class="feather icon-users"></i> Person & Adresse</h5>
                            </div>
                            <div class="card-body p-2">
                              <div class="form-group mb-1">
                                <label>Firma</label>
                                <input type="text" class="form-control" name="firma" value="{{ old('firma') }}">
                              </div>

                              <div class="row mb-1">
                                <div class="col-4 pr-1">
                                  <label>Anrede</label>
                                  <select class="form-control p-1" name="title">
                                    <option selected></option>
                                    <option value="Frau">Frau</option>
                                    <option value="Herr">Herr</option>
                                  </select>
                                </div>
                                <div class="col-8 pl-1">
                                  <label>Nachname</label>
                                  <input type="text" class="form-control" name="lastname" id="lastname" list="lastname-options">
                                  <datalist id="lastname-options"></datalist>
                                </div>
                              </div>
                              <div class="form-group mb-1">
                                  <label>Vorname</label>
                                  <input type="text" class="form-control" name="name" id="name" list="name-options">
                                  <datalist id="name-options"></datalist>
                              </div>

                              <div class="form-group mb-1">
                                <label>Adresse</label>
                                <input type="text" class="form-control" id="full_address" name="full_address"
                                       value="{{ old('full_address') }}" placeholder="Adresse eingeben">
                                <!-- Hidden Geo Fields -->
                                <input type="hidden" name="latitude" id="latitude-input" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude-input" value="{{ old('longitude') }}">
                                <input type="hidden" name="street" id="street-input" value="{{ old('street') }}">
                                <input type="hidden" name="postcode" id="postal_code-input" value="{{ old('postcode') }}">
                                <input type="hidden" name="city" id="locality-input" value="{{ old('city') }}">
                                <input type="hidden" name="elevation" id="elevation-input" value="{{ old('elevation') }}">
                              </div>

                              <div class="row mb-1">
                                <div class="col-6 pr-1">
                                  <label>Festnetz</label>
                                  <input type="text" class="form-control" name="telephone" value="{{ old('telephone') }}">
                                </div>
                                <div class="col-6 pl-1">
                                  <label>Mobil</label>
                                  <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                                </div>
                              </div>

                              <div class="form-group mb-1">
                                <label>E-Mail</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                              </div>

                              <div class="form-group mb-1">
                                <label>Notiz</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                              </div>
                            </div>
                          </div>

                      </div>
                  </div>

                  <!-- RIGHT COLUMN: FULL SCREEN CALENDAR -->
                  <div class="col-xl-9 col-lg-8 col-md-12">
                      <div id="inquiry-calendar-container">
                          <!-- The Calendar ID is preserved, but we will config it to be big -->
                          <div id="inquiry-mini-calendar" style="height: 100%;"></div>
                      </div>
                  </div>
              </div>
              
              <!-- BOTTOM ROW: PRODUCTS (Full Width) -->
              <div class="row mt-2">
                  <div class="col-12">
                    <div class="card mb-1">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-brand-blue">
                          <i class="feather icon-box"></i>
                          Produkt, Dienstleistung, Abteilung und Personal hinzufügen
                        </h5>
                        <button type="button" class="btn btn-primary shadow" id="addRow">
                          <i class="feather icon-plus"></i> Zeile hinzufügen
                        </button>
                      </div>

                      <div class="card-body p-0">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover mb-0" id="inquiryProductTable">
                            <thead class="thead-light text-center">
                              <tr>
                                <th style="width: 15%">Produkt</th>
                                <th style="width: 15%">Dienstleisung</th>
                                <th style="width: 15%">Abteilung</th>
                                <th style="width: 15%">Innendienst</th>
                                <th style="width: 15%">Außendienst</th>
                                <th style="width: 15%">Termin</th>
                                <th style="width: 5%">Aktion</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                          </table>
                        </div>
                      </div>

                      <div class="card-footer text-center" style="background: #fafafa;">
                       <!-- Same size / padding / shadow / icon spacing for all buttons -->
                          <button type="submit"
                                  class="btn btn-success btn-lg mr-1 shadow d-inline-flex align-items-center justify-content-center"
                                  data-submit-mode="save">
                            <i class="feather icon-save mr-50"></i>
                            <span>Speichern</span>
                          </button>

                          <button type="submit"
                                  class="btn btn-lg mr-1 shadow d-inline-flex align-items-center justify-content-center btn-brand"
                                  data-submit-mode="save_verify"
                                  id="btnSaveVerify">
                            <i class="feather icon-shield mr-50"></i>
                            <span>Speichern &amp; Verifizieren</span>
                          </button>

                          <button type="button"
                                  class="btn btn-lg mr-1 shadow d-inline-flex align-items-center justify-content-center btn-outline-danger"
                                  id="discardDraftBtn">
                            <i class="feather icon-trash-2 mr-50"></i>
                            <span>Entwurf verwerfen</span>
                          </button>

                          <style>
                            /* Optional: one clean, reusable brand button style (keeps all buttons visually consistent) */
                            :root{
                              --brand-lightblue: var(--brand-lightblue, #38bdf8);
                              --brand-lightblue-hover: #0ea5e9;
                              --brand-lightblue-text: #0f172a;
                            }

                            .btn-brand{
                              background-color: var(--brand-lightblue) !important;
                              border-color: var(--brand-lightblue) !important;
                              color: var(--brand-lightblue-text) !important;
                            }
                            .btn-brand:hover,
                            .btn-brand:focus{
                              background-color: var(--brand-lightblue-hover) !important;
                              border-color: var(--brand-lightblue-hover) !important;
                              color: #0b1220 !important;
                            }

                            /* Make outline-danger also match "btn-lg" feel if your theme doesn't fully apply it */
                            #discardDraftBtn.btn-lg{
                              padding: .9rem 1.35rem;
                              font-weight: 600;
                            }
                          </style>

                      </div>
                    </div>
                  </div>
              </div>

            </form>
        </div>
    </div>
</div>

{{-- Verify Modal --}}
<div id="verifyModal" class="verify-modal hidden" aria-hidden="true">
  <div class="verify-backdrop" data-verify-close></div>

  <div class="verify-dialog" role="dialog" aria-modal="true" aria-labelledby="verifyTitle">
    <div class="verify-head">
      <div>
        <div class="verify-kicker">ANFRAGE</div>
        <h3 id="verifyTitle" class="verify-title">Verifizierung prüfen</h3>
        <div class="verify-sub" id="verifySub">Bitte warten…</div>
      </div>

      <button type="button" class="verify-x" data-verify-close title="Schließen">
        <i class="feather icon-x"></i>
      </button>
    </div>

    <div class="verify-body">
      <div class="verify-progress">
        <div class="verify-progress-bar" id="verifyProgressBar" style="width:0%"></div>
      </div>

      <div id="verifyExistingLead" class="verify-existing hidden"></div>
      <div class="verify-list" id="verifyChecklist"></div>
      <div class="verify-missing hidden" id="verifyMissingBox"></div>
    </div>

    <div class="verify-foot">
      <button type="button" class="btn btn-outline-secondary" id="btnVerifyRefresh">
        <i class="feather icon-refresh-cw"></i> Neu prüfen
      </button>

      <button type="button" class="btn btn-primary" id="btnVerifyConfirm" disabled>
        <span class="verify-btn-text"><i class="feather icon-check-circle"></i> Verifizierung bestätigen</span>
        <span class="verify-btn-loading hidden"><i class="feather icon-loader"></i> Bitte warten…</span>
      </button>
    </div>
  </div>
</div>

<div id="apmModal" class="apm-modal hidden" aria-hidden="true">
  <div class="apm-backdrop" data-apm-close></div>

  <div class="apm-dialog" role="dialog" aria-modal="true" aria-labelledby="apmTitle">
    <div class="apm-head">
      <div>
        <div class="apm-kicker">TERMINKALENDER</div>
        <h3 id="apmTitle" class="apm-title">Termin erstellen</h3>
        <div class="apm-sub" id="apmSub">Slot auswählen → Termin speichern</div>
      </div>

      <button type="button" class="apm-x" data-apm-close title="Schließen">
        <i class="feather icon-x"></i>
      </button>
    </div>

    <div class="apm-body">
      <div class="apm-grid">

        {{-- LEFT: Customer + Objects + Lead products --}}
        <div class="apm-card">
          <div class="apm-card-h">
            <h6><i class="feather icon-user"></i> Kunde & Produkte</h6>
            <div class="custom-control custom-switch custom-switch-primary">
              <input type="checkbox" class="custom-control-input" id="apmLinkInquiry">
              <label class="custom-control-label" for="apmLinkInquiry">Mit Anfrage verbinden</label>
            </div>
          </div>

          <div class="apm-card-b">
            <div class="form-group mb-1">
              <label>Kunde (new_leads)</label>
              <select id="apmCustomer" class="form-control" style="width:100%"></select>
              <small class="text-muted">Suchen und auswählen (Select2 AJAX).</small>
            </div>

            <div class="form-group mb-1">
              <label>Objekt / Alternative Adresse</label>
              <select id="apmAlternative" class="form-control select2" style="width:100%">
                <option value="">Bitte zuerst Kunde wählen…</option>
              </select>
            </div>

            <div class="mt-1">
              <div class="d-flex justify-content-between align-items-center mb-50">
                <label class="mb-0">Produkte aus Lead (lead_product_lists)</label>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="apmReloadLeadProducts">
                  <i class="feather icon-refresh-cw"></i>
                </button>
              </div>

              <div id="apmLeadProductsBox" class="apm-empty">Kein Kunde gewählt.</div>
            </div>

            <hr>

            <div class="mt-1">
              <label class="mb-50">Mitarbeiter (automatisch aus Produktzeilen)</label>
              <div id="apmEmployeesChips" class="apm-chipwrap"></div>
              <div id="apmEmployeesEmpty" class="apm-empty mt-50 hidden">
                Keine Mitarbeiter in Produktzeilen ausgewählt.
              </div>

              {{-- hidden arrays for submit --}}
              <div id="apmEmployeesHidden"></div>
            </div>
          </div>
        </div>

        {{-- RIGHT: Appointment details --}}
        <div class="apm-card">
          <div class="apm-card-h">
            <h6><i class="feather icon-edit-3"></i> Termin Details</h6>
          </div>

          <div class="apm-card-b">
            <div class="form-group mb-1">
              <label>Titel</label>
              <input type="text" id="apmName" class="form-control" placeholder="z.B. Vor-Ort Termin / Beratung">
            </div>

            <div class="apm-row">
              <div class="form-group mb-1">
                <label>Termin-Typ</label>
                <select id="apmAppointmentType" class="form-control select2" style="width:100%">
                  <option value="">—</option>
                  <option value="consulting">Beratung</option>
                  <option value="inspection">Besichtigung</option>
                  <option value="installation">Installation</option>
                  <option value="service">Service</option>
                  <option value="other">Sonstiges</option>
                </select>
              </div>

              <div class="form-group mb-1">
                <label>Ausführung (execution_type)</label>
                <select id="apmExecutionType" class="form-control select2" style="width:100%">
                  <option value="">—</option>
                  <option value="internal">Innendienst</option>
                  <option value="field">Außendienst</option>
                  <option value="mixed">Gemischt</option>
                </select>
              </div>
            </div>

            <div class="apm-row">
              <div class="form-group mb-1">
                <label>Start</label>
                <input type="datetime-local" id="apmStart" class="form-control">
              </div>
              <div class="form-group mb-1">
                <label>Ende</label>
                <input type="datetime-local" id="apmEnd" class="form-control">
              </div>
            </div>

            <div class="apm-row">
              <div class="form-group mb-1">
                <label>Branch</label>
                <select id="apmBranch" class="form-control select2" style="width:100%">
                  <option value="">—</option>
                  @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group mb-1">
                <label>Priorität</label>
                <select id="apmPriority" class="form-control select2" style="width:100%">
                  <option value="">—</option>
                  <option value="normal">Normal</option>
                  <option value="dringend">Dringend</option>
                  <option value="sehr_dringend">Sehr Dringend</option>
                </select>
              </div>
            </div>

            <div class="apm-row">
              <div class="form-group mb-1">
                <label>Farbe</label>
                <input type="text" id="apmColor" class="form-control" placeholder="#95c11f (optional)">
              </div>
              <div class="form-group mb-1">
                <label>Status</label>
                <select id="apmStatus" class="form-control select2" style="width:100%">
                  <option value="planned">Geplant</option>
                  <option value="send">Gesendet</option>
                  <option value="done">Erledigt</option>
                </select>
              </div>
            </div>

            <div class="form-group mb-0">
              <label>Notiz</label>
              <textarea id="apmNote" class="form-control" rows="4" placeholder="Beschreibung / Vorbereitung / Hinweise…"></textarea>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="apm-foot">
      <div class="left">
        <span class="text-muted" id="apmHint"></span>
      </div>

      <div class="right">
        <button type="button" class="btn btn-outline-secondary" data-apm-close>
          <i class="feather icon-x"></i> Abbrechen
        </button>
        <button type="button" class="btn btn-primary" id="apmSaveBtn">
          <i class="feather icon-save"></i> Termin speichern
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.11.3/main.global.min.js"></script>

<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  window.INQUIRY_ID = {{ (int) ($data->id ?? 0) }};
</script>
<script>
async function ensureInquiryId() {
  let id = Number(window.INQUIRY_ID || 0);

  // already have a real inquiry
  if (id > 0) return id;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  const res = await fetch(`/inquiries/start-draft`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    }
  });

  const json = await res.json();

  if (!res.ok || !json?.success || !json?.id) {
    throw new Error(json?.message || 'Draft start failed');
  }

  id = Number(json.id);

  // update global + hidden field
  window.INQUIRY_ID = id;
  const hidden = document.getElementById('inquiry_id');
  if (hidden) hidden.value = id;

  return id;
}
</script>
<script>
$(document).ready(function() {
   $('select[name="periority"]').select2({ placeholder: 'Priorität wählen' });
  $('select[name="pre_type"]').select2({ placeholder: 'Art des Kontakts', allowClear: true });
  $('select[name="branch_id"]').select2({ placeholder: 'Betrieb', allowClear: false });
  $('#source').select2({ tags: true, placeholder: "Quelle auswählen", allowClear: true });
});
</script>

{{-- Name/Lastname suggestions (kept) --}}
<script>
document.getElementById('lastname')?.addEventListener('input', function () {
  let input = this.value;
  if (input.length >= 2) {
    fetch(`/api/lead-lastname-suggestions?query=${encodeURIComponent(input)}`)
      .then(r => r.json())
      .then(data => document.getElementById('lastname-options').innerHTML = data.map(n => `<option value="${n}">`).join(''))
      .catch(console.error);
  }
});
document.getElementById('name')?.addEventListener('input', function () {
  let input = this.value;
  if (input.length >= 2) {
    fetch(`/api/lead-name-suggestions?query=${encodeURIComponent(input)}`)
      .then(r => r.json())
      .then(data => document.getElementById('name-options').innerHTML = data.map(n => `<option value="${n}">`).join(''))
      .catch(console.error);
  }
});
</script>
 
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places&callback=initMap" async defer></script>


<script>
function initMap() {
  initAutocomplete(); // keep your existing logic
}

function initAutocomplete() {
  const fullAddressInput = document.getElementById("full_address");
  const streetInput      = document.getElementById("street-input");
  const latitudeInput    = document.getElementById("latitude-input");
  const longitudeInput   = document.getElementById("longitude-input");
  const elevationInput   = document.getElementById("elevation-input");
  const postalCodeInput  = document.getElementById("postal_code-input");
  const cityInput        = document.getElementById("locality-input");

  if (!fullAddressInput) return;

  // keep dropdown above modals/select2
  const style = document.createElement('style');
  style.innerHTML = `.pac-container{ z-index: 100000 !important; }`;
  document.head.appendChild(style);

  const elevationService = new google.maps.ElevationService();

  const autocomplete = new google.maps.places.Autocomplete(fullAddressInput, {
    fields: ["address_components", "geometry"],
    types: ["address"]
  });

  autocomplete.addListener("place_changed", () => {
    const place = autocomplete.getPlace();

    if (!place || !place.geometry || !place.geometry.location) {
      alert("Kein Standort für diese Adresse gefunden.");
      return;
    }

    const location = place.geometry.location;

    if (latitudeInput)  latitudeInput.value  = location.lat();
    if (longitudeInput) longitudeInput.value = location.lng();

    if (elevationInput) {
      elevationService.getElevationForLocations({ locations: [location] }, (results, status) => {
        elevationInput.value =
          (status === google.maps.ElevationStatus.OK && results && results[0])
            ? Number(results[0].elevation).toFixed(2)
            : "";
      });
    }

    const a = parseAddressComponents(place.address_components || []);
    if (streetInput)      streetInput.value      = [a.route, a.street_number].filter(Boolean).join(' ').trim();
    if (postalCodeInput) postalCodeInput.value = a.postal_code || '';
    if (cityInput)       cityInput.value        = a.locality || a.administrative_area_level_1 || a.administrative_area_level_2 || '';
  });

  function parseAddressComponents(components) {
    const address = {
      street_number: "",
      route: "",
      locality: "",
      postal_code: "",
      administrative_area_level_1: "",
      administrative_area_level_2: ""
    };

    components.forEach(c => {
      if (c.types.includes("street_number")) address.street_number = c.long_name;
      if (c.types.includes("route"))          address.route = c.long_name;
      if (c.types.includes("locality"))       address.locality = c.long_name;
      if (c.types.includes("postal_code"))    address.postal_code = c.long_name;
      if (c.types.includes("administrative_area_level_1")) address.administrative_area_level_1 = c.long_name;
      if (c.types.includes("administrative_area_level_2")) address.administrative_area_level_2 = c.long_name;
    });

    return address;
  }
}
</script>

{{-- UPDATED CALENDAR SCRIPT FOR FULL SCREEN & MORE DETAILS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const calEl = document.getElementById('inquiry-mini-calendar');
  if (!calEl) return;

  const BRAND_GREEN = '#8dc53d';
  const BRAND_BLUE = '#73b1d4';
  const BRAND_LIGHTBLUE = '#8dbfdc';

  const calendar = new FullCalendar.Calendar(calEl, {
    initialView: 'dayGridMonth', // Default to month
    headerToolbar: { 
        left: 'prev,next today', 
        center: 'title', 
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' 
    },
    locale: 'de',
    firstDay: 1,
    slotMinTime: '07:00:00',
    slotMaxTime: '21:00:00',
    allDaySlot: false,
    height: '100%', // Takes parent height (85vh)
    navLinks: true, // Click day to go to day view
    editable: false,
    dayMaxEvents: true,
    
    events: [],
    
    // Custom render to show MORE details in the event box
    eventContent: function(arg) {
        let title = arg.event.title || 'Unbenannter Termin';
        let timeText = arg.timeText;
        // Assume backend sends 'description', 'employee', or 'status' in extendedProps
        let description = arg.event.extendedProps.description || '';
        let employee = arg.event.extendedProps.employee || 'Kein Mitarbeiter';
        
        let contentHtml = `
            <div class="custom-event-content">
                <div class="custom-event-time">${timeText}</div>
                <div class="custom-event-title">${title}</div>
                ${employee ? `<div class="custom-event-details"><i class="feather icon-user"></i> ${employee}</div>` : ''}
                ${description ? `<div class="custom-event-details" style="font-style:italic;">${description.substring(0,30)}...</div>` : ''}
            </div>
        `;
        return { html: contentHtml };
    },
    
    eventDidMount: function(info) {
        // Color coding based on props if needed, or default to Blue
        info.el.style.backgroundColor = BRAND_BLUE;
        info.el.style.borderColor = BRAND_BLUE;
        info.el.style.color = 'white';
    },

    selectable: true,
    selectMirror: true,
    select: function(info){
      window.AppointmentModal?.openFromCalendar(info);
    },
    eventClick: function(info){
      // Optional: Open modal with details
      alert('Event: ' + (info.event.title || 'Unbenannt') + '\nDetails: ' + (info.event.extendedProps.description || 'Keine Beschreibung'));
    },
  });
  calendar.render();

  function gatherSelection() {
    const internal = new Set();
    const external = new Set();
    const dates = [];

    $('#inquiryProductTable tbody tr').each(function () {
      const idx = $(this).data('index');
      const inVal = $(`.employee-select[data-index="${idx}"]`).val();
      const exVal = $(`.field-employee-select[data-index="${idx}"]`).val();
      const dtVal = $(`.termin-input[data-index="${idx}"]`).val();

      if (inVal && !isNaN(inVal)) internal.add(parseInt(inVal,10));
      if (exVal && !isNaN(exVal)) external.add(parseInt(exVal,10));
      if (dtVal) {
        const d = dtVal.split('T')[0];
        if (d) dates.push(d);
      }
    });

    const anchorDate = dates.length ? dates.sort()[0] : new Date().toISOString().slice(0,10);
    return { internal_ids: Array.from(internal), external_ids: Array.from(external), date: anchorDate };
  }

  let lastAnchor = null;
  let seq = 0, newestSeq = 0;

  const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(()=>fn(...a), ms); }; };

  const refreshCalendar = debounce(function () {
    const sel = gatherSelection();

    // If we have a date selected in products, jump there, otherwise stay put
    if (sel.date !== lastAnchor && sel.date) {
      lastAnchor = sel.date;
      calendar.gotoDate(sel.date);
    }

    // Load events
    const params = new URLSearchParams();
    sel.internal_ids.forEach(id => params.append('internal_ids[]', id));
    sel.external_ids.forEach(id => params.append('external_ids[]', id));
    params.append('date', sel.date);

    const mySeq = ++seq;
    newestSeq = mySeq;

    $.getJSON('{{ route("inquiries.calendar.availability") }}?' + params.toString())
      .done(function (resp) {
        if (mySeq !== newestSeq) return;
        calendar.removeAllEvents();
        (resp.events || []).forEach(ev => calendar.addEvent(ev));
      })
      .fail(function (xhr) {
        if (xhr && xhr.statusText === 'abort') return;
        console.error('Kalender-Fehler', xhr?.status, xhr?.responseText || xhr);
      });
  }, 250);

  $(document).on('change', '.employee-select, .field-employee-select, .termin-input', refreshCalendar);
  $(document).on('click', '#addRow', () => setTimeout(refreshCalendar, 200));
  setTimeout(refreshCalendar, 300);
});
</script>

<script>
(function(){
  'use strict';

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // If this page sometimes edits an existing inquiry, set this from backend:
  // In create page => null
  window.__CURRENT_INQUIRY_ID = @json($data->id ?? null);


  // endpoints (define routes accordingly - see section 3)
  const API = {
    customers: "{{ route('api.appointments.customers') }}", // GET ?q=
    alternatives: (leadId) => "{{ url('/api/appointments/customers') }}/" + leadId + "/alternatives",
    leadProducts: (leadId) => "{{ url('/api/appointments/customers') }}/" + leadId + "/products", // GET ?alternative_id=
    store: "{{ route('main_appointments.store_modal') }}" // POST
  };

  // ---------------------------
  // Helpers
  // ---------------------------
  const uniq = (arr) => Array.from(new Set(arr.map(String))).filter(Boolean);
  const toLocalDT = (d) => {
    // format Date -> yyyy-MM-ddTHH:mm (for datetime-local)
    const pad = n => String(n).padStart(2,'0');
    const yyyy = d.getFullYear();
    const MM = pad(d.getMonth()+1);
    const dd = pad(d.getDate());
    const hh = pad(d.getHours());
    const mm = pad(d.getMinutes());
    return `${yyyy}-${MM}-${dd}T${hh}:${mm}`;
  };

  function collectSelectedEmployeesFromInquiryRows(){
    const ids = [];
    $('#inquiryProductTable tbody tr').each(function(){
      const inId = $(this).find('.employee-select').val();
      const outId = $(this).find('.field-employee-select').val();
      if (inId) ids.push(inId);
      if (outId) ids.push(outId);
    });
    return uniq(ids).map(v => parseInt(v,10)).filter(n => !isNaN(n));
  }

  function renderEmployeeChips(employeeItems){
    // employeeItems: [{id, text, color?}]
    const $wrap = $('#apmEmployeesChips').empty();
    const $hidden = $('#apmEmployeesHidden').empty();

    if (!employeeItems.length){
      $('#apmEmployeesEmpty').removeClass('hidden');
      return;
    }
    $('#apmEmployeesEmpty').addClass('hidden');

    employeeItems.forEach(e => {
      $wrap.append(`
        <span class="apm-chip" data-id="${e.id}">
          <span class="dot" style="background:${e.color || '#95c11f'}"></span>
          ${e.text || 'Unbekannt'}
          <button type="button" class="rm" title="Entfernen" data-apm-rm-emp="${e.id}"><i class="feather icon-x"></i></button>
        </span>
      `);
      $hidden.append(`<input type="hidden" name="apm_employee_ids[]" value="${e.id}">`);
    });
  }

  function readEmployeeTextById(id){
    // try to read from existing select options (already loaded in rows)
    // fallback to "ID: X"
    const opt = $(`option[value="${id}"]`).first();
    const t = opt.length ? opt.text().trim() : 'Unbekannter Mitarbeiter';
    return t;
  }

  function getEmployeesFromRowsAsItems(){
    const ids = collectSelectedEmployeesFromInquiryRows();
    return ids.map(id => ({ id, text: readEmployeeTextById(id) }));
  }

  function openModal(){
    $('#apmModal').removeClass('hidden').attr('aria-hidden','false');
  }
  function closeModal(){
    $('#apmModal').addClass('hidden').attr('aria-hidden','true');
  }

  $(document).on('click', '[data-apm-close]', closeModal);

  // remove employee chip
  $(document).on('click','[data-apm-rm-emp]', function(){
    const id = parseInt($(this).attr('data-apm-rm-emp'),10);
    const items = [];
    $('#apmEmployeesChips .apm-chip').each(function(){
      const cid = parseInt($(this).data('id'),10);
      if (cid !== id) items.push({id: cid, text: $(this).text().replace('','').trim()});
    });
    // re-derive clean texts:
    renderEmployeeChips(items.map(x => ({id:x.id, text: readEmployeeTextById(x.id)})));
  });

  // ---------------------------
  // Select2 init (modal)
  // ---------------------------
  function initModalSelect2(){
    // customers ajax
    $('#apmCustomer').select2({
      width: '100%',
      dropdownParent: $('#apmModal .apm-dialog'),
      placeholder: 'Kunde suchen…',
      minimumInputLength: 1,
      ajax: {
        url: API.customers,
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results || [] })
      }
    });

    $('#apmAlternative, #apmAppointmentType, #apmExecutionType, #apmBranch, #apmPriority, #apmStatus').select2({
      width: '100%',
      dropdownParent: $('#apmModal .apm-dialog')
    });
  }

  // ---------------------------
  // Lead products rendering
  // ---------------------------
  function renderLeadProducts(list){
    if (!list || !list.length){
      $('#apmLeadProductsBox').html(`<div class="apm-empty">Keine Produkte gefunden.</div>`);
      return;
    }

    const rows = list.map(x => `
      <tr>
        <td>
          <div style="font-weight:800">${x.product_name || 'Unbenanntes Produkt'}</div>
          <div style="font-size:12px;color:#666">${x.service_name || 'Service nicht angegeben'}</div>
        </td>
        <td>${x.department_name || 'Keine Abteilung'}</td>
        <td>${x.employee_name || 'Kein MA'}${x.field_employee_name ? ' / ' + x.field_employee_name : ''}</td>
        <td class="text-right">
          <label class="mb-0">
            <input type="checkbox" class="apm-lead-product-check" value="${x.id}" checked>
            <span class="ml-25">nutzen</span>
          </label>
        </td>
      </tr>
    `).join('');

    $('#apmLeadProductsBox').html(`
      <div style="border:1px solid #eee;border-radius:12px;overflow:hidden">
        <table class="apm-table">
          <thead>
            <tr>
              <th>Produkt</th>
              <th>Abteilung</th>
              <th>Mitarbeiter</th>
              <th class="text-right">Auswahl</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
    `);
  }

  async function loadAlternatives(leadId){
    $('#apmAlternative').empty().append(`<option value="">Lade…</option>`).trigger('change.select2');
    const r = await fetch(API.alternatives(leadId), { headers: { 'Accept':'application/json' }});
    const data = await r.json();

    const opts = (data.alternatives || []).map(a => {
      const label = a.object_name ? `${a.object_name} — ${a.full_address || 'Adresse unbekannt'}` : (a.full_address || 'Unbenanntes Objekt');
      return `<option value="${a.id}" ${a.main ? 'selected' : ''}>${label}</option>`;
    }).join('');

    $('#apmAlternative').html(`<option value="">—</option>${opts}`).trigger('change.select2');
  }

  async function loadLeadProducts(){
    const leadId = $('#apmCustomer').val();
    const altId = $('#apmAlternative').val();
    if (!leadId){
      $('#apmLeadProductsBox').html(`<div class="apm-empty">Kein Kunde gewählt.</div>`);
      return;
    }

    $('#apmLeadProductsBox').html(`<div class="apm-empty">Lade Produkte…</div>`);
    const url = API.leadProducts(leadId) + (altId ? `?alternative_id=${encodeURIComponent(altId)}` : '');
    const r = await fetch(url, { headers: { 'Accept':'application/json' }});
    const data = await r.json();
    renderLeadProducts(data.products || []);
  }

  $('#apmCustomer').on('select2:select', async function(){
    const leadId = $(this).val();
    await loadAlternatives(leadId);
    await loadLeadProducts();
  });

  $('#apmAlternative').on('change', loadLeadProducts);
  $('#apmReloadLeadProducts').on('click', loadLeadProducts);

  // ---------------------------
  // Link to inquiry toggle
  // ---------------------------
  function syncInquiryLinkUI(){
    const hasInquiry = Number(window.__CURRENT_INQUIRY_ID || window.INQUIRY_ID || 0) > 0;

    const $cb = $('#apmLinkInquiry');

    if (!hasInquiry){
      $cb.prop('checked', false).prop('disabled', true);
      $('#apmHint').text('Hinweis: Keine gespeicherte Anfrage vorhanden → Termin wird ohne Anfrage-Verknüpfung gespeichert.');
    } else {
      $cb.prop('disabled', false).prop('checked', true);
      $('#apmHint').text('Termin wird mit der aktuellen Anfrage verbunden.');
    }
  }

  // ---------------------------
  // Save appointment
  // ---------------------------
async function saveAppointment(){
  try {
    const startVal = $('#apmStart').val();
    const endVal   = $('#apmEnd').val();

    // link toggle (must be defined BEFORE payload)
    const wantsLink = $('#apmLinkInquiry').is(':checked');

    // employees from hidden inputs
    const employeeIds = [];
    $('#apmEmployeesHidden input[name="apm_employee_ids[]"]').each(function(){
      const n = parseInt(this.value, 10);
      if (!isNaN(n)) employeeIds.push(n);
    });

    // lead products (checked)
    const selectedLeadProductIds = [];
    $('#apmLeadProductsBox .apm-lead-product-check:checked').each(function(){
      const n = parseInt(this.value, 10);
      if (!isNaN(n)) selectedLeadProductIds.push(n);
    });

    // ✅ ensure inquiry id exists if we want linking
    let inquiryId = Number(window.__CURRENT_INQUIRY_ID || window.INQUIRY_ID || 0);

    if (wantsLink && inquiryId <= 0) {
      Swal.fire({ title:'Draft wird erstellt…', showConfirmButton:false, allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

      inquiryId = Number(await ensureInquiryId()); // returns id

      // keep everything in sync
      window.INQUIRY_ID = inquiryId;
      window.__CURRENT_INQUIRY_ID = inquiryId;

      const hidden = document.getElementById('inquiry_id');
      if (hidden) hidden.value = inquiryId;

      // refresh hint/checkbox UI if you rely on it
      if (typeof syncInquiryLinkUI === 'function') syncInquiryLinkUI();

      Swal.close();
    }

    const payload = {
      name: $('#apmName').val(),
      appointment_type: $('#apmAppointmentType').val(),
      execution_type: $('#apmExecutionType').val(),
      branch_id: $('#apmBranch').val(),
      priority: $('#apmPriority').val(),
      status: $('#apmStatus').val(),
      color: $('#apmColor').val(),
      note: $('#apmNote').val(),

      // slot
      start: startVal,
      end: endVal,

      // link to customer
      customer_id: $('#apmCustomer').val() || null,
      alternative_id: $('#apmAlternative').val() || null,
      lead_product_list_ids: selectedLeadProductIds,

      // employees
      employee_ids: employeeIds,

      // ✅ link to inquiry
      link_inquiry: wantsLink ? 1 : 0,
      inquiry_id: (wantsLink && inquiryId > 0) ? inquiryId : null,
    };

    // minimal validation
    if (!payload.name){
      Swal.fire({ icon:'warning', title:'Titel fehlt', text:'Bitte Titel eingeben.' });
      return;
    }
    if (!payload.start){
      Swal.fire({ icon:'warning', title:'Start fehlt', text:'Bitte Start-Datum/Zeit setzen.' });
      return;
    }
    if (!payload.employee_ids.length){
      Swal.fire({ icon:'warning', title:'Keine Mitarbeiter', text:'Bitte Mitarbeiter in Produktzeilen auswählen (Innendienst/Außendienst).' });
      return;
    }

    Swal.fire({ title:'Speichern…', showConfirmButton:false, allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

    const r = await fetch(API.store, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'Accept':'application/json',
        'X-Requested-With':'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF
      },
      body: JSON.stringify(payload)
    });

    const data = await r.json().catch(() => ({}));

    Swal.close();

    if (!r.ok){
      const msg =
        data?.message ||
        (data?.errors ? Object.values(data.errors).flat().join(' ') : '') ||
        'Speichern fehlgeschlagen.';
      Swal.fire({ icon:'error', title:'Fehler', text: msg });
      return;
    }

    // add event to calendar (optional)
    if (window.__INQUIRY_CALENDAR && data.event){
      window.__INQUIRY_CALENDAR.addEvent(data.event);
    }

    Swal.fire({ icon:'success', title:'Gespeichert', text:'Termin wurde erstellt.' });
    closeModal();
  } catch (e) {
    Swal.close();
    Swal.fire({ icon:'error', title:'Fehler', text: e?.message || 'Unerwarteter Fehler beim Speichern.' });
    console.error(e);
  }
}

  $('#apmSaveBtn').on('click', saveAppointment);

  // ---------------------------
  // Public API
  // ---------------------------
  window.AppointmentModal = {
    openFromCalendar(info){
      // set slot times
      $('#apmStart').val(toLocalDT(info.start));
      $('#apmEnd').val(info.end ? toLocalDT(info.end) : '');

      // employees from inquiry table
      renderEmployeeChips(getEmployeesFromRowsAsItems());

      // reset some fields
      $('#apmName').val('');
      $('#apmNote').val('');
      $('#apmColor').val('');

      // link toggle
      syncInquiryLinkUI();

      openModal();
    }
  };

  // init once
  $(document).ready(function(){
    initModalSelect2();
    syncInquiryLinkUI();
  });

})();
</script>

{{-- Row add/employee load script (kept) --}}
<script>
(() => {
  'use strict';

  const IMG_EMPLOYEE    = "{{ asset('images/employee/') }}";
  const CSRF_TOKEN      = '{{ csrf_token() }}';
  const ROUTE_EMPLOYEES = '{{ route("inquiry.department.employees") }}';
  const STAGE           = 'inquiry';

  const SERVICES    = @json($services);
  const PRODUCTS    = @json($products);
  const DEPARTMENTS = @json($departments);

  let rowIndex = 0;

  $(function () {

    $('#addRow').on('click', function () {
      const $lastRow = $('#inquiryProductTable tbody tr:last');

      if ($lastRow.length) {
        const i = $lastRow.data('index');
        const missing = [
          { val: $(`.product-select[data-index="${i}"]`).val(), label: 'Produkt' },
          { val: $(`.service-select[data-index="${i}"]`).val(), label: 'Dienstleistung' },
          { val: $(`.department-select[data-index="${i}"]`).val(), label: 'Abteilung' },
          { val: $(`.employee-select[data-index="${i}"]`).val(), label: 'Innendienst' }
        ].filter(f => !f.val).map(f => f.label);

        if (missing.length) {
          Swal.fire({
            icon: 'error',
            title: `Zeile ${i + 1} unvollständig`,
            html: `Bitte füllen Sie folgende Felder aus: <strong>${missing.join(', ')}</strong>`,
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-danger' },
            buttonsStyling: false
          });
          return;
        }
      }

      rowIndex++;
      const idx = rowIndex;

      const row = `
        <tr data-index="${idx}" class="align-middle">
          <td>
            <select class="form-select product-select" name="product_id[]" data-index="${idx}" style="width:100%">
              <option value="">Produkt wählen</option>
              ${PRODUCTS.map(p => `<option value="${p.id}" data-img="${p.image || ''}">${p.article_group}</option>`).join('')}
            </select>
          </td>
          <td>
            <select class="form-select service-select" name="service_id[]" data-index="${idx}" style="width:100%">
              <option value="">Service wählen</option>
            </select>
          </td>
          <td>
            <select class="form-select department-select" name="department_id[]" data-index="${idx}" style="width:100%">
              <option value="">Abteilung wählen</option>
              ${DEPARTMENTS.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('')}
            </select>
          </td>
          <td>
            <select class="form-select employee-select" name="employee_id[]" data-index="${idx}" style="width:100%">
              <option value="">Innendienst wählen</option>
            </select>
          </td>
          <td>
            <select class="form-select field-employee-select" name="field_employee[]" data-index="${idx}" style="width:100%">
              <option value="">Außendienst wählen</option>
            </select>
          </td>
          <td>
            <input type="datetime-local" class="form-control termin-input" name="appointment_date[]" data-index="${idx}">
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm removeRow" title="Entfernen">
              <i class="feather icon-trash"></i>
            </button>
          </td>
        </tr>
      `;

      $('#inquiryProductTable tbody').append(row);
      initSelects(idx);
    });

    function initSelects(i) {
      const $product = $(`.product-select[data-index="${i}"]`);
      const $service = $(`.service-select[data-index="${i}"]`);
      const $dept    = $(`.department-select[data-index="${i}"]`);
      const $emp     = $(`.employee-select[data-index="${i}"]`);
      const $field   = $(`.field-employee-select[data-index="${i}"]`);

      [$product, $service, $dept, $emp, $field].forEach($s => $s.select2({ width:'100%' }));

      $product.on('change', () => {
        loadServices(i);
        loadEmployees(i, {autofill:true});
      });

      $service.on('change', (e, meta) => {
        if (meta && meta.skipReload) return;
        loadEmployees(i, {autofill:false});
      });

      $dept.on('change', (e, meta) => {
        if (meta && meta.skipReload) return;
        loadEmployees(i, {autofill:false});
      });

      [$emp, $field].forEach($s => {
        $s.select2({
          templateResult: formatEmployee,
          templateSelection: opt => opt.text,
          escapeMarkup: m => m,
          width: '100%'
        });
      });
    }

    function loadServices(i) {
      const pid  = $(`.product-select[data-index="${i}"]`).val();
      const $srv = $(`.service-select[data-index="${i}"]`);
      $srv.empty().append('<option value="">Service wählen</option>');

      const list = SERVICES.filter(s => String(s.product_id) === String(pid));
      list.forEach(s => $srv.append(`<option value="${s.id}">${translateService(s.phase_section)}</option>`));
      $srv.trigger('change.select2', [{skipReload:true}]);
    }

    function fillEmployeeSelect($select, employees, placeholder) {
      $select.empty().append(`<option value="">${placeholder}</option>`);
      employees.forEach(emp => {
        $select.append(`
          <option value="${emp.id}" data-img="${emp.image || ''}" data-positions="${(emp.positions || []).join(', ')}">
            ${emp.name} ${emp.lastname}
          </option>
        `);
      });
      $select.select2({
        templateResult: formatEmployee,
        templateSelection: opt => opt.text,
        escapeMarkup: m => m,
        width:'100%'
      });
    }

    function loadEmployees(i, options = {}) {
      const autofill = options.autofill === true;

      const $product = $(`.product-select[data-index="${i}"]`);
      const $dept    = $(`.department-select[data-index="${i}"]`);
      const $service = $(`.service-select[data-index="${i}"]`);
      const $emp     = $(`.employee-select[data-index="${i}"]`);
      const $field   = $(`.field-employee-select[data-index="${i}"]`);

      const pid = $product.val();
      let did = $dept.val() || null;
      let sid = $service.val() || null;

      if (!pid) {
        fillEmployeeSelect($emp, [], 'Innendienst wählen');
        fillEmployeeSelect($field, [], 'Außendienst wählen');
        return;
      }

      $.post(ROUTE_EMPLOYEES, { _token: CSRF_TOKEN, product_id: pid, department_id: did, service_id: sid, stage: STAGE })
        .done(res => {
          const serverDept = res.department_id || null;
          const serverSrv  = res.service_id || null;

          if (autofill) {
            if (!did && serverDept) {
              did = serverDept;
              $dept.val(String(serverDept)).trigger('change.select2', [{skipReload:true}]);
            }
            if (!sid && serverSrv && $service.find(`option[value="${serverSrv}"]`).length) {
              sid = serverSrv;
              $service.val(String(serverSrv)).trigger('change.select2', [{skipReload:true}]);
            }
          }

          fillEmployeeSelect($emp, res.internal_employees || [], 'Innendienst wählen');
          fillEmployeeSelect($field, res.external_employees || [], 'Außendienst wählen');

          if (!(res.internal_employees || []).length && !(res.external_employees || []).length) {
            Swal.fire({
              icon:'warning',
              title:'Keine Mitarbeiter gefunden',
              text:'Für diese Kombination (Stage: inquiry) existieren keine Mitarbeiter.',
              confirmButtonText:'OK',
              customClass:{ confirmButton:'btn btn-warning' },
              buttonsStyling:false
            });
          }
        })
        .fail(() => {
          fillEmployeeSelect($emp, [], 'Innendienst wählen');
          fillEmployeeSelect($field, [], 'Außendienst wählen');
          Swal.fire({
            icon:'error',
            title:'Fehler',
            text:'Mitarbeiter konnten nicht geladen werden.',
            confirmButtonText:'OK',
            customClass:{ confirmButton:'btn btn-danger' },
            buttonsStyling:false
          });
        });
    }

    function formatEmployee(opt) {
      if (!opt.id) return opt.text;
      const $el = $(opt.element);
      const imgFile = $el.data('img');
      const img = imgFile ? `${IMG_EMPLOYEE}/${imgFile}` : '';
      const pos = $el.data('positions') || '';
      return `
        <div style="display:flex;align-items:center;">
          ${img ? `<img src="${img}" class="me-2 rounded-circle" style="width:36px;height:36px;object-fit:cover;">`
               : `<div class="me-2 rounded-circle" style="width:36px;height:36px;background:#e5e7eb;"></div>`}
          <div>
            <strong>${opt.text || 'Unbekannt'}</strong><br>
            <small>${pos}</small>
          </div>
        </div>`;
    }

    function translateService(s) {
      if (!s) return '';
      const key = String(s).toLowerCase();
      const map = { complete:'Komplettlösung', montage:'Montage', product:'Kaufen', plan:'Planung', maintenance:'Wartung', repair:'Reparatur', reclaim:'Reklamation', others:'Sonstiges' };
      return map[key] || s;
    }

    $(document).on('click', '.removeRow', function () {
      $(this).closest('tr').fadeOut(200, function () { $(this).remove(); });
    });
  });
})();
</script>

{{-- VERIFY + SUBMIT (single handler only) --}}
  <script>
(function () {
  'use strict';

  // ============================
  // STATE
  // ============================
  let submitMode = 'save';

  // ============================
  // HELPERS
  // ============================
  const filled = v => v !== null && v !== undefined && String(v).trim() !== '';
  const firstFilled = (...vals) => vals.some(v => filled(v));

  function escapeHtml(s) {
    return String(s ?? '')
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;');
  }

  function resolveTarget(selector) {
    let $t = $(selector).first();
    if ($t.length) return $t;

    // fallback: ".service-select[data-index="2"]" -> 2nd .service-select
    const m = String(selector).match(/^(\.[A-Za-z0-9\-_]+)\[data-index="(\d+)"\]$/);
    if (m) {
      const cls = m[1].slice(1);
      const n = parseInt(m[2], 10);
      const $all = $(`.${cls}`);
      if ($all.length >= n) return $all.eq(n - 1);
    }
    return $();
  }

  function scrollAndPulse($el) {
    const top = $el.offset().top - 120;
    $('html, body').animate({ scrollTop: top }, 250);
    $el.addClass('verify-pulse');
    setTimeout(() => $el.removeClass('verify-pulse'), 1200);
  }

  // ============================
  // SUBMIT MODE
  // ============================
  $(document).on('click', 'button[type="submit"][data-submit-mode]', function () {
    submitMode = $(this).data('submit-mode') || 'save';
    $('#submit_mode').val(submitMode);
  });

  // ============================
  // MODAL
  // ============================
  function openVerifyModal() {
    $('#verifyModal').removeClass('hidden').attr('aria-hidden', 'false');
  }

  function closeVerifyModal() {
    $('#verifyModal').addClass('hidden').attr('aria-hidden', 'true');
    $('.verify-inline-editor').remove();
  }

  $(document).on('click', '[data-verify-close]', closeVerifyModal);

  function setConfirmLoading(isLoading) {
    const $btn = $('#btnVerifyConfirm');
    $btn.prop('disabled', isLoading || $btn.data('disabled-by-checks') === true);
    $btn.find('.verify-btn-text').toggleClass('hidden', isLoading);
    $btn.find('.verify-btn-loading').toggleClass('hidden', !isLoading);
  }

  // ============================
  // GOOGLE ADDRESS AUTOCOMPLETE FOCUS (SPECIAL)
  // ============================
  function openGoogleAutocompleteForAddress() {
    const el = document.getElementById('full_address');
    if (!el) return;

    // Close modal so suggestions are never hidden behind backdrop/dialog
    closeVerifyModal();

    setTimeout(() => {
      el.focus();

      // trigger Places dropdown even if empty
      const v = el.value || ' ';
      el.value = v;
      el.dispatchEvent(new Event('input', { bubbles: true }));
      el.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true }));

      try { el.setSelectionRange(el.value.length, el.value.length); } catch (e) {}
    }, 120);
  }

  // ============================
  // INLINE EDITOR (MODAL)
  // ============================
  function openInlineEditor($item, edit) {
    // SPECIAL: address => open google autocomplete
    if (edit.mode === 'gmap' || edit.type === 'gmap') {
      openGoogleAutocompleteForAddress();
      return;
    }

    // ✅ SPECIAL: contact => show 3 inputs (email + mobile + telephone)
    if (edit.mode === 'contact3') {
      $('.verify-inline-editor').remove();

      const $email = $('input[name="email"]');
      const $mobile = $('input[name="phone"]');       // mobile
      const $tel = $('input[name="telephone"]');      // landline

      const html = `
        <div class="verify-inline-editor" style="margin-top:10px;padding:10px;border:1px solid #eee;border-radius:12px;background:#fafafa">
          <div style="font-size:12px;font-weight:800;color:#444;margin-bottom:10px">Kontakt bearbeiten</div>

          <div class="form-group mb-2">
            <label style="font-size:12px;font-weight:700;color:#444;margin-bottom:4px">E-Mail</label>
            <input type="email" class="form-control verify-inline-email" value="${escapeHtml($email.val())}" placeholder="name@mail.de">
          </div>

          <div class="form-group mb-2">
            <label style="font-size:12px;font-weight:700;color:#444;margin-bottom:4px">Mobil</label>
            <input type="text" class="form-control verify-inline-mobile" value="${escapeHtml($mobile.val())}" placeholder="+49 ...">
          </div>

          <div class="form-group mb-2">
            <label style="font-size:12px;font-weight:700;color:#444;margin-bottom:4px">Festnetz</label>
            <input type="text" class="form-control verify-inline-telephone" value="${escapeHtml($tel.val())}" placeholder="0611 ...">
          </div>

          <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:10px">
            <button type="button" class="btn btn-sm btn-outline-secondary verify-inline-cancel">Abbrechen</button>
            <button type="button" class="btn btn-sm btn-success verify-inline-save-contact">Übernehmen</button>
          </div>
        </div>
      `;

      $item.append(html);

      // focus first field
      setTimeout(() => $item.find('.verify-inline-email').trigger('focus'), 0);
      return;
    }

    // ---------- default single-field editor ----------
    const $target = resolveTarget(edit.selector);
    if (!$target.length) {
      Swal.fire({ icon:'warning', title:'Nicht gefunden', text:`Feld nicht gefunden: ${edit.selector}` });
      return;
    }

    $('.verify-inline-editor').remove();

    const isSelect = $target.is('select');
    const isTextarea = $target.is('textarea');
    const isInput = $target.is('input');
    const inputType = isInput ? ($target.attr('type') || 'text') : 'text';

    let html = `
      <div class="verify-inline-editor" style="margin-top:10px;padding:10px;border:1px solid #eee;border-radius:12px;background:#fafafa">
        <div style="font-size:12px;font-weight:800;color:#444;margin-bottom:6px">Bearbeiten</div>
    `;

    if (isSelect) {
      const opts = $target.find('option').map(function () {
        const v = $(this).attr('value') ?? '';
        const t = $(this).text();
        const sel = this.selected ? 'selected' : '';
        return `<option value="${escapeHtml(v)}" ${sel}>${escapeHtml(t)}</option>`;
      }).get().join('');
      html += `<select class="form-control verify-inline-input" style="width:100%">${opts}</select>`;
    } else if (isTextarea) {
      html += `<textarea class="form-control verify-inline-input" rows="3">${escapeHtml($target.val())}</textarea>`;
    } else {
      html += `<input class="form-control verify-inline-input" type="${escapeHtml(inputType)}" value="${escapeHtml($target.val())}">`;
    }

    html += `
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:10px">
          <button type="button" class="btn btn-sm btn-outline-secondary verify-inline-cancel">Abbrechen</button>
          <button type="button" class="btn btn-sm btn-success verify-inline-save">Übernehmen</button>
        </div>
      </div>
    `;

    $item.append(html);

    const $editor = $item.find('.verify-inline-editor');
    $editor.data('selector', edit.selector);

    const $input = $editor.find('.verify-inline-input');

    if ($input.is('select')) {
      $input.select2({ width:'100%', dropdownParent: $('#verifyModal .verify-dialog') });
      setTimeout(() => $input.select2('open'), 0);
    } else {
      setTimeout(() => $input.trigger('focus'), 0);
    }
  }


  $(document).on('click', '.verify-inline-cancel', function () {
    $(this).closest('.verify-inline-editor').remove();
  });

  $(document).on('click', '.verify-inline-save', function () {
    const $editor = $(this).closest('.verify-inline-editor');
    const selector = $editor.data('selector');
    const $target = resolveTarget(selector);

    if (!$target.length) return;

    const $input = $editor.find('.verify-inline-input');
    const val = $input.val();

    if ($target.is('select')) $target.val(val).trigger('change');
    else $target.val(val).trigger('input').trigger('change');

    $editor.remove();

    // re-check immediately
    renderChecklist(buildClientVerification());
  });

  // ============================
  // CLIENT-SIDE VERIFICATION
  // ============================
  function checkRow(key, label, ok, edit) {
    const row = { key, label, ok: !!ok };
    if (!ok && edit && edit.selector) row.edit = edit;
    return row;
  }

  function buildClientVerification() {
    const checks = [];
    const missing = [];

    // pre_type
    const preTypeOk = filled($('select[name="pre_type"]').val());
    checks.push(checkRow(
      'pre_type',
      'Art des Kontakts (pre_type) gewählt',
      preTypeOk,
      { selector:'select[name="pre_type"]', type:'select2', mode:'inline' }
    ));
    if (!preTypeOk) missing.push('Art des Kontakts (pre_type) fehlt.');

    // name
    const nameOk = filled($('input[name="name"]').val());
    checks.push(checkRow(
      'name',
      'Vorname vorhanden',
      nameOk,
      { selector:'input[name="name"]', type:'focus', mode:'inline' }
    ));
    if (!nameOk) missing.push('Vorname fehlt.');

    // lastname
    const lastnameOk = filled($('input[name="lastname"]').val());
    checks.push(checkRow(
      'lastname',
      'Nachname vorhanden',
      lastnameOk,
      { selector:'input[name="lastname"]', type:'focus', mode:'inline' }
    ));
    if (!lastnameOk) missing.push('Nachname fehlt.');

    // address (SPECIAL: gmap)
    const addressOk = firstFilled($('#full_address').val(), $('#street-input').val());
    checks.push(checkRow(
      'address',
      'Adresse vorhanden',
      addressOk,
      { selector:'#full_address', type:'gmap', mode:'gmap' } // <-- google autocomplete
    ));
    if (!addressOk) missing.push('Adresse fehlt.');

    // contact
    const contactOk = firstFilled(
      $('input[name="phone"]').val(),
      $('input[name="telephone"]').val(),
      $('input[name="email"]').val()
    );
    checks.push(checkRow(
      'contact',
      'Kontakt vorhanden (Mobil/Festnetz/E-Mail)',
      contactOk,
      { selector:'input[name="phone"]', type:'focus', mode:'inline' }
    ));
    if (!contactOk) missing.push('Kontakt fehlt (Mobil oder Festnetz oder E-Mail).');

    // product rows
    const $rows = $('#inquiryProductTable tbody tr');
    const hasRows = $rows.length > 0;
    checks.push(checkRow(
      'products',
      'Mindestens 1 Produktzeile hinzugefügt',
      hasRows,
      { selector:'#addRow', type:'click' }
    ));
    if (!hasRows) missing.push('Produkte fehlen.');

    let rowsOk = true;

    $rows.each(function (idx) {
      const $tr = $(this);
      const dataIndex = $tr.data('index');
      const rowLabel = `Zeile ${idx + 1}`;

      const pid = $tr.find('.product-select').val();
      const sid = $tr.find('.service-select').val();
      const did = $tr.find('.department-select').val();
      const eid = $tr.find('.employee-select').val();

      const prodOk = filled(pid);
      checks.push(checkRow(
        `row.${idx+1}.product`,
        `Produkt (${rowLabel})`,
        prodOk,
        { selector:`.product-select[data-index="${dataIndex}"]`, type:'select2', mode:'inline' }
      ));
      if (!prodOk) { missing.push(`${rowLabel}: Produkt fehlt.`); rowsOk = false; }

      const srvOk = filled(sid);
      checks.push(checkRow(
        `row.${idx+1}.service`,
        `Dienstleistung (${rowLabel})`,
        srvOk,
        { selector:`.service-select[data-index="${dataIndex}"]`, type:'select2', mode:'inline' }
      ));
      if (!srvOk) { missing.push(`${rowLabel}: Dienstleistung fehlt.`); rowsOk = false; }

      const depOk = filled(did);
      checks.push(checkRow(
        `row.${idx+1}.department`,
        `Abteilung (${rowLabel})`,
        depOk,
        { selector:`.department-select[data-index="${dataIndex}"]`, type:'select2', mode:'inline' }
      ));
      if (!depOk) { missing.push(`${rowLabel}: Abteilung fehlt.`); rowsOk = false; }

      const empOk = filled(eid);
      checks.push(checkRow(
        `row.${idx+1}.employee`,
        `Innendienst (${rowLabel})`,
        empOk,
        { selector:`.employee-select[data-index="${dataIndex}"]`, type:'select2', mode:'inline' }
      ));
      if (!empOk) { missing.push(`${rowLabel}: Innendienst fehlt.`); rowsOk = false; }
    });

    checks.push(checkRow(
      'products_details',
      'Produkte vollständig (Produkt/Dienstleistung/Abteilung/Innendienst)',
      hasRows && rowsOk,
      { selector:'#inquiryProductTable', type:'focus' }
    ));

    const ok = preTypeOk && nameOk && lastnameOk && addressOk && contactOk && hasRows && rowsOk;
    return { ok, checks, missing: Array.from(new Set(missing)) };
  }

  function renderChecklist(payload) {
    const checks = payload.checks || [];
    const okCount = checks.filter(c => c.ok).length;
    const pct = checks.length ? Math.round((okCount / checks.length) * 100) : 0;

    $('#verifyProgressBar').css('width', pct + '%');
    $('#verifySub').text(`Status: ${okCount}/${checks.length} erfüllt`);

    const $list = $('#verifyChecklist').empty();

    checks.forEach(c => {
      const cls   = c.ok ? 'ok' : 'bad';
      const icon  = c.ok ? 'icon-check' : 'icon-alert-triangle';
      const state = c.ok ? 'OK' : 'Fehlt';

      const hasEdit = !c.ok && c.edit && c.edit.selector;
      const actionHtml = hasEdit
        ? `<button type="button"
                  class="btn btn-sm btn-outline-primary verify-edit-btn"
                  data-edit='${JSON.stringify(c.edit).replace(/'/g,"&#39;")}'
                  title="Bearbeiten">
              <i class="feather icon-edit-2"></i> Bearbeiten
          </button>`
        : '';

      $list.append(`
        <div class="verify-item ${cls}">
          <div class="l">
            <div class="verify-dot"><i class="feather ${icon}"></i></div>
            <div>
              <div class="txt">${c.label}</div>
              <div class="st">${state}</div>
            </div>
          </div>
          <div class="r">${actionHtml}</div>
        </div>
      `);
    });

    const missing = payload.missing || [];
    if (missing.length) {
      $('#verifyMissingBox').removeClass('hidden')
        .html(`<div style="font-weight:800;color:#b00042">Fehlende Angaben</div><ul>${missing.map(m=>`<li>${escapeHtml(m)}</li>`).join('')}</ul>`);
    } else {
      $('#verifyMissingBox').addClass('hidden').empty();
    }

    // pre-store => no server existing lead box
    $('#verifyExistingLead').addClass('hidden').empty();

    $('#btnVerifyConfirm')
      .data('disabled-by-checks', !payload.ok)
      .prop('disabled', !payload.ok);
  }

  // ============================
  // EDIT BUTTON (INLINE vs SPECIAL)
  // ============================
  $(document).on('click', '.verify-edit-btn', function () {
    let edit = null;
    try { edit = JSON.parse($(this).attr('data-edit')); } catch (e) {}

    if (!edit || !edit.selector) return;

    // address special: google autocomplete
    if (edit.mode === 'gmap' || edit.type === 'gmap') {
      openGoogleAutocompleteForAddress();
      return;
    }

    // inline editor (default)
    if (edit.mode === 'inline') {
      openInlineEditor($(this).closest('.verify-item'), edit);
      return;
    }

    // fallback: jump to field (rare)
    const $el = resolveTarget(edit.selector);
    if (!$el.length) return;
    closeVerifyModal();

    setTimeout(() => {
      scrollAndPulse($el);
      if (edit.type === 'select2' && $el.hasClass('select2-hidden-accessible')) $el.select2('open');
      else $el.focus();
    }, 120);
  });

  // ============================
  // LIVE RECHECK (ONLY WHEN MODAL OPEN + NOT EDITING)
  // ============================
  const recheckDebounced = (function () {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(() => {
        if ($('#verifyModal').hasClass('hidden')) return;
        if ($('.verify-inline-editor').length) return; // do not disturb typing
        renderChecklist(buildClientVerification());
      }, 120);
    };
  })();

  $(document).on(
    'input change',
    '.leadForm input, .leadForm textarea, .leadForm select, #inquiryProductTable input, #inquiryProductTable select',
    recheckDebounced
  );

  // ============================
  // AJAX SUBMIT (ACTUAL STORE)
  // ============================
  function ajaxSubmitForm($form) {
    const formData = new FormData($form[0]);
    formData.set('submit_mode', submitMode);

    return $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function () {
        Swal.fire({
          title: 'Speichern...',
          text: 'Ihre Anfrage wird verarbeitet.',
          showConfirmButton: false,
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
      }
    });
  }

  // Confirm button: validate again, then store
 // Confirm button: store inquiry first, then call verification confirm to create lead + redirect
$('#btnVerifyConfirm').on('click', function () {
  const payload = buildClientVerification();
  renderChecklist(payload);
  if (!payload.ok) return;

  setConfirmLoading(true);

  const $form = $('.leadForm');

  // force the backend to know this is a verify-save
  submitMode = 'save_verify';
  $('#submit_mode').val('save_verify');

  const formData = new FormData($form[0]);
  formData.set('submit_mode', 'save_verify');

  // 1) Store inquiry (must return inquiry_id JSON)
  $.ajax({
    url: $form.attr('action'),
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    dataType: 'json',
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    beforeSend: function () {
      Swal.fire({
        title: 'Speichern...',
        text: 'Ihre Anfrage wird verarbeitet.',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });
    }
  })
  .done(function (resp) {
    const inquiryId = resp?.inquiry_id;
    if (!inquiryId) {
      Swal.close();
      setConfirmLoading(false);
      Swal.fire({ icon:'error', title:'Fehler', text:'inquiry_id fehlt im Store-Response.' });
      return;
    }

    // 2) Create lead + redirect URL via verification confirm
    $.ajax({
      url: `{{ url('/inquiries') }}/${inquiryId}/verification/confirm`,
      type: 'POST',
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      data: { _token: '{{ csrf_token() }}' }
    })
    .done(function (resp2) {
      Swal.close();

      if (resp2?.redirect_url) {
        window.location.href = resp2.redirect_url;
        return;
      }

      setConfirmLoading(false);
      Swal.fire({ icon:'warning', title:'Kein Redirect', text:'confirm hat keine redirect_url geliefert.' });
    })
    .fail(function (xhr) {
      Swal.close();
      setConfirmLoading(false);

      const msg = xhr?.responseJSON?.message || 'Verifizierung fehlgeschlagen.';
      Swal.fire({ icon:'error', title:'Fehler', text: msg });
    });
  })
  .fail(function (xhr) {
    Swal.close();
    setConfirmLoading(false);

    const serverErrors = xhr.responseJSON?.errors;
    let errorMessages = '';
    if (serverErrors) {
      $.each(serverErrors, function (_k, v) { errorMessages += `<li>✅ ${v}</li>`; });
    } else {
      errorMessages = '<li>Es ist ein unerwarteter Fehler aufgetreten.</li>';
    }
    Swal.fire({ icon:'error', title:'Fehler', html:`<ul style="text-align:left;">${errorMessages}</ul>` });
  });
});


// ✅ Save handler for contact3 editor (3 inputs)
$(document).on('click', '.verify-inline-save-contact', function () {
  const $editor = $(this).closest('.verify-inline-editor');

  const emailVal = $editor.find('.verify-inline-email').val();
  const mobileVal = $editor.find('.verify-inline-mobile').val();
  const telVal = $editor.find('.verify-inline-telephone').val();

  // Write back to real fields (separate)
  const $email = $('input[name="email"]');
  const $mobile = $('input[name="phone"]');
  const $tel = $('input[name="telephone"]');

  // Guard against any other listeners that copy values between fields
  window.__verifyInlineWrite = true;

  $email.val(emailVal).trigger('change');
  $mobile.val(mobileVal).trigger('change');
  $tel.val(telVal).trigger('change');

  window.__verifyInlineWrite = false;

  $editor.remove();
  renderChecklist(buildClientVerification());
});

  // ============================
  // SUBMIT HANDLER
  // - save: store immediately
  // - save_verify: open verify modal first (no store yet)
  // ============================
  $(document).ready(function () {
    $('.leadForm').on('submit', function (e) {
      e.preventDefault();

      const $form = $(this);

      if (submitMode === 'save_verify') {
        openVerifyModal();
        renderChecklist(buildClientVerification());
        return; // DO NOT store yet
      }

      ajaxSubmitForm($form)
        .done(function (response) {
          Swal.close();
          Swal.fire({ icon:'success', title:'Erfolg', text:'Die Anfrage wurde erfolgreich gespeichert!' })
            .then(() => {
              if (response && response.redirect_url) window.location.href = response.redirect_url;
            });
        })
        .fail(function (xhr) {
          Swal.close();

          const serverErrors = xhr.responseJSON?.errors;
          let errorMessages = '';

          if (serverErrors) {
            $.each(serverErrors, function (_key, value) { errorMessages += `<li>✅ ${escapeHtml(value)}</li>`; });
          } else {
            errorMessages = '<li>Es ist ein unerwarteter Fehler aufgetreten.</li>';
          }

          Swal.fire({ icon:'error', title:'Fehler', html:`<ul style="text-align:left;">${errorMessages}</ul>` });
        });
    });
  });

})();
</script>

<script>
(function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  let inquiryId = 0;


  let dirty = false;
  let saving = false;
  let t = null;

  // mark dirty on any input change
  document.addEventListener('input', (e) => {
    if (!e.target.closest('form')) return;
    dirty = true;
    scheduleSave();
  });

  function scheduleSave(){
    clearTimeout(t);
    t = setTimeout(saveInquiry, 700);
  }

  async function saveInquiry(){
    if (!dirty || saving) return;
    saving = true;

    try {
      const form = document.querySelector('#inquiryForm');
      const fd = new FormData(form);

      // ✅ ensure draft exists (avoids /0/)
      const inquiryId = await ensureInquiryId();

      const res = await fetch(`/inquiries/${inquiryId}/autosave`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: fd
      });

      if (!res.ok) throw await res.json();

      dirty = false;
      const el = document.querySelector('#autosaveStatus');
      if (el) el.textContent = 'Saved';
    } catch (err) {
      const el = document.querySelector('#autosaveStatus');
      if (el) el.textContent = 'Save failed';
      console.error(err);
    } finally {
      saving = false;
    }
  }


  // Tab close / refresh warning (native browser confirm)
  window.addEventListener('beforeunload', function (e) {
    if (!dirty) return;
    e.preventDefault();
    e.returnValue = '';
  });

})();
</script>
<script>
async function autosaveProducts() {
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const inquiryId = window.INQUIRY_ID;

  // Build items from your UI (adapt selectors to your table)
  const rows = Array.from(document.querySelectorAll('.product-row'));
  const items = rows.map(r => ({
    id: r.dataset.rowId ? parseInt(r.dataset.rowId,10) : null,
    product_id: val(r.querySelector('[name="product_id[]"]')),
    service_id: val(r.querySelector('[name="service_id[]"]')),
    department_id: val(r.querySelector('[name="department_id[]"]')),
    employee_id: val(r.querySelector('[name="employee_id[]"]')),
    field_employee: val(r.querySelector('[name="field_employee[]"]')),
    appointment_date: r.querySelector('[name="appointment_date[]"]')?.value || null,
    status: 'open'
  }));

  function val(el){
    if (!el) return null;
    const v = el.value;
    return (v === '' || v == null) ? null : parseInt(v,10);
  }

  const res = await fetch(`/inquiries/${inquiryId}/autosave/products`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    body: JSON.stringify({ items })
  });

  if (!res.ok) console.error(await res.json());
}

// debounce product saves
let prodT = null;
document.addEventListener('change', (e) => {
  if (!e.target.closest('.product-row')) return;
  clearTimeout(prodT);
  prodT = setTimeout(autosaveProducts, 600);
});
</script>
<script>
(function(){
  let dirty = false; // reuse same dirty variable if you centralize it

  // Example: intercept links with data-guard
  document.addEventListener('click', async (e) => {
    const a = e.target.closest('a[data-guard]');
    if (!a) return;
    if (!dirty) return;

    e.preventDefault();

    // show your modal here
    const choice = await window.confirm('You have unsaved changes. OK=Leave (keep draft), Cancel=Stay');
    if (choice) window.location.href = a.href;
  });
})();
</script>
<script>
document.getElementById('discardDraftBtn').addEventListener('click', async () => {
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const inquiryId = window.INQUIRY_ID;

  const ok = confirm('Discard this draft? This will delete the inquiry.');
  if (!ok) return;

  const res = await fetch(`/inquiries/${inquiryId}/discard`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
  });

  if (res.ok) window.location.href = '/inquiry'; // your list route
});
</script>

 <script>
/**
 * CUSTOMER SEARCH (Select2) + DETAILS MODAL (NURI / Inquiry Page)
 *
 * ✅ Uses YOUR existing routes:
 *   - Search:      GET  {{ route('api.appointments.customers') }}?q=
 *   - Alternatives GET  /api/appointments/customers/{lead}/alternatives
 *   - Products     GET  /api/appointments/customers/{lead}/products?alternative_id=
 *
 * ✅ Includes a "Details Modal" that shows:
 *   - Customer summary
 *   - Counts (objects + products)
 *   - Object list (alternatives) + optional product list for selected object
 *
 * Requires:
 * - jQuery
 * - Select2
 * - SweetAlert2 (Swal)
 *
 * HTML elements expected (already in your blade):
 * - #customerSearch
 * - #btnCustomerSearch
 * - #btnCustomerClear
 *
 * Modal elements expected (from earlier):
 * - #customerDetailsModal
 * - #csTitle, #csSub, #csContent, #csHint
 * - #csUseCustomer
 * - [data-cs-close]
 */
(function () {
  'use strict';

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const API = {
    search: "{{ route('api.appointments.customers') }}",
    alternatives: (leadId) => "{{ url('/api/appointments/customers') }}/" + leadId + "/alternatives",
    products: (leadId) => "{{ url('/api/appointments/customers') }}/" + leadId + "/products",
  };

  // -----------------------
  // Helpers
  // -----------------------
  const esc = (s) => String(s ?? '')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#39;');

  function openModal() {
    $('#customerDetailsModal').removeClass('hidden').attr('aria-hidden', 'false');
  }
  function closeModal() {
    $('#customerDetailsModal').addClass('hidden').attr('aria-hidden', 'true');
  }
  $(document).on('click', '[data-cs-close]', closeModal);

  function setLoadingUI(title = 'Kunde Details', sub = 'Lade…') {
    $('#csTitle').text(title);
    $('#csSub').text(sub);
    $('#csHint').text('');
    $('#csContent').html(`
      <div class="cs-skel">
        <div class="cs-skel-line w-60"></div>
        <div class="cs-skel-line w-90"></div>
        <div class="cs-skel-line w-80"></div>
      </div>
    `);
  }

  async function getJSON(url) {
    const r = await fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {})
      }
    });
    const data = await r.json().catch(() => ({}));
    if (!r.ok) {
      const msg = data?.message || ('Request failed: ' + r.status);
      throw new Error(msg);
    }
    return data;
  }

  // -----------------------
  // Select2 (Customer Search)
  // -----------------------
  function initCustomerSelect2() {
    $('#customerSearch').select2({
      width: '100%',
      placeholder: 'Kunde suchen…',
      allowClear: true,
      minimumInputLength: 1,
      ajax: {
        url: API.search,
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results || [] }),
        cache: true
      }
    });
  }

  // -----------------------
  // Render Modal
  // -----------------------
  function renderModal(payload) {
    const c = payload.customer || {};
    const objects = payload.objects || [];
    const products = payload.products || [];

    const title = payload.title || 'Kunde Details';
    const sub = payload.sub || (c.full_address || '—');

    $('#csTitle').text(title);
    $('#csSub').text(sub);

    // counts
    const counts = {
      objects: objects.length,
      products: products.length
    };

    const kpis = `
      <div class="cs-kpis">
        <div class="cs-kpi"><div class="v">${counts.objects}</div><div class="l">Objekte</div></div>
        <div class="cs-kpi"><div class="v">${counts.products}</div><div class="l">Produkte</div></div>
        <div class="cs-kpi"><div class="v">${esc(c.city || '—')}</div><div class="l">Ort</div></div>
      </div>
    `;

    const fields = `
      <div class="cs-row">
        <div class="cs-f"><div class="k">Firma</div><div class="v">${esc(c.firma || '—')}</div></div>
        <div class="cs-f"><div class="k">Name</div><div class="v">${esc(([c.name, c.lastname].filter(Boolean).join(' ')) || '—')}</div></div>
        <div class="cs-f"><div class="k">PLZ</div><div class="v">${esc(c.postcode || '—')}</div></div>
        <div class="cs-f"><div class="k">Stadt</div><div class="v">${esc(c.city || '—')}</div></div>
      </div>
    `;

    const objRows = objects.length ? objects.map(o => `
      <tr>
        <td>
          <div style="font-weight:900">${esc(o.object_name || 'Objekt')}</div>
          <div style="font-size:12px;color:#666">${esc(o.full_address || '')}</div>
        </td>
        <td class="text-right">
          ${o.main ? `<span class="cs-pill"><span class="cs-dot"></span>Hauptobjekt</span>` : '—'}
        </td>
      </tr>
    `).join('') : `<tr><td colspan="2"><div class="text-muted">Keine Objekte</div></td></tr>`;

    const prodRows = products.length ? products.slice(0, 12).map(p => `
      <tr>
        <td>
          <div style="font-weight:900">${esc(p.product_name || 'Produkt')}</div>
          <div style="font-size:12px;color:#666">${esc(p.service_name || '')}</div>
        </td>
        <td>${esc(p.department_name || '—')}</td>
        <td>${esc(p.employee_name || '—')}${p.field_employee_name ? ' / ' + esc(p.field_employee_name) : ''}</td>
      </tr>
    `).join('') : `<tr><td colspan="3"><div class="text-muted">Keine Produkte</div></td></tr>`;

    $('#csContent').html(`
      <div class="cs-grid">
        <div class="cs-card">
          <div class="cs-card-h"><h6>Überblick</h6></div>
          <div class="cs-card-b">
            ${kpis}
            <div style="height:12px"></div>
            ${fields}
          </div>
        </div>

        <div class="cs-card">
          <div class="cs-card-h" style="display:flex;justify-content:space-between;align-items:center;gap:10px">
            <h6>Objekte</h6>
            <div style="min-width:260px">
              <select id="csObjectSelect" class="form-control" style="width:100%">
                <option value="">Alle Objekte / Standard</option>
                ${objects.map(o => {
                  const label = (o.object_name ? o.object_name + ' — ' : '') + (o.full_address || '');
                  const sel = o.main ? 'selected' : '';
                  return `<option value="${esc(o.id)}" ${sel}>${esc(label)}</option>`;
                }).join('')}
              </select>
            </div>
          </div>

          <div class="cs-card-b">
            <div style="border:1px solid #eee;border-radius:12px;overflow:hidden">
              <table class="cs-table">
                <thead><tr><th>Objekt</th><th class="text-right">Status</th></tr></thead>
                <tbody>${objRows}</tbody>
              </table>
            </div>
          </div>

          <div class="cs-card-h" style="border-top:1px solid #f1f1f1"><h6>Produkte</h6></div>
          <div class="cs-card-b">
            <div id="csProductsBox" style="border:1px solid #eee;border-radius:12px;overflow:hidden">
              <table class="cs-table">
                <thead><tr><th>Produkt</th><th>Abteilung</th><th>Mitarbeiter</th></tr></thead>
                <tbody>${prodRows}</tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    `);

    $('#csHint').text(`Kunde-ID: ${c.id || '—'}`);
    $('#csUseCustomer').data('customer', c);

    // enhance object select with select2 inside modal
    const $objSel = $('#csObjectSelect');
    if ($objSel.length) {
      $objSel.select2({
        width: '100%',
        dropdownParent: $('#customerDetailsModal .cs-dialog')
      });
    }
  }

  // -----------------------
  // Load data for modal using YOUR endpoints
  // -----------------------
  async function openDetailsForLead(leadId, selectedText) {
    const title = selectedText || 'Kunde Details';

    setLoadingUI(title, 'Lade Objekte…');
    openModal();

    try {
      // 1) alternatives
      const altRes = await getJSON(API.alternatives(leadId));
      const objects = altRes.alternatives || [];

      // choose default altId (main if exists)
      const mainObj = objects.find(o => String(o.main) === '1' || o.main === true) || objects[0] || null;
      const altId = mainObj ? mainObj.id : '';

      // 2) products (optional: filtered by altId)
      $('#csSub').text('Lade Produkte…');
      const prodUrl = API.products(leadId) + (altId ? ('?alternative_id=' + encodeURIComponent(altId)) : '');
      const prodRes = await getJSON(prodUrl);
      const products = prodRes.products || [];

      // build minimal customer object from select2 text (because your /customers endpoint returns only text)
      // If you want full customer fields (phone/email/address), add a dedicated details endpoint later.
      const customer = {
        id: leadId,
        firma: null,
        name: null,
        lastname: null,
        city: null,
        postcode: null,
        full_address: null
      };

      renderModal({
        title,
        sub: (mainObj?.full_address || '—'),
        customer,
        objects,
        products
      });

      // when switching object => reload products for that alternative
      $(document).off('change.csObjectSelect').on('change.csObjectSelect', '#csObjectSelect', async function () {
        const alt = $(this).val();
        $('#csProductsBox').html(`
          <div class="cs-skel" style="margin:12px">
            <div class="cs-skel-line w-90"></div>
            <div class="cs-skel-line w-80"></div>
          </div>
        `);

        try {
          const url = API.products(leadId) + (alt ? ('?alternative_id=' + encodeURIComponent(alt)) : '');
          const p = await getJSON(url);
          const list = p.products || [];

          const prodRows = list.length ? list.slice(0, 12).map(x => `
            <tr>
              <td>
                <div style="font-weight:900">${esc(x.product_name || 'Produkt')}</div>
                <div style="font-size:12px;color:#666">${esc(x.service_name || '')}</div>
              </td>
              <td>${esc(x.department_name || '—')}</td>
              <td>${esc(x.employee_name || '—')}${x.field_employee_name ? ' / ' + esc(x.field_employee_name) : ''}</td>
            </tr>
          `).join('') : `<tr><td colspan="3"><div class="text-muted">Keine Produkte</div></td></tr>`;

          $('#csProductsBox').html(`
            <table class="cs-table">
              <thead><tr><th>Produkt</th><th>Abteilung</th><th>Mitarbeiter</th></tr></thead>
              <tbody>${prodRows}</tbody>
            </table>
          `);
        } catch (e) {
          $('#csProductsBox').html(`<div class="alert alert-danger mb-0">Produkte konnten nicht geladen werden.</div>`);
        }
      });

    } catch (e) {
      $('#csContent').html(`<div class="alert alert-danger mb-0">${esc(e.message || 'Fehler')}</div>`);
      $('#csSub').text('Fehler');
    }
  }

  // -----------------------
  // Buttons: Search + Reset
  // -----------------------
  function bindButtons() {
    $('#btnCustomerSearch').on('click', function () {
      const id = $('#customerSearch').val();
      if (!id) {
        Swal.fire({ icon: 'warning', title: 'Kein Kunde gewählt', text: 'Bitte zuerst einen Kunden auswählen.' });
        return;
      }
      const item = $('#customerSearch').select2('data')?.[0] || {};
      openDetailsForLead(id, item.text || ('Kunde #' + id));
    });

    $('#btnCustomerClear').on('click', function () {
      $('#customerSearch').val(null).trigger('change');
    });
  }

  // -----------------------
  // Apply customer data to your form (only what we have reliably)
  // If later you add a proper "details" endpoint, extend this mapping.
  // -----------------------
  function bindApplyButton() {
    $('#csUseCustomer').on('click', function () {
      const c = $(this).data('customer') || {};
      // currently only ID is guaranteed; still keep hooks for later
      if (c.firma) $('input[name="firma"]').val(c.firma).trigger('input');
      if (c.name) $('input[name="name"]').val(c.name).trigger('input');
      if (c.lastname) $('input[name="lastname"]').val(c.lastname).trigger('input');

      closeModal();
      Swal.fire({ icon: 'success', title: 'OK', text: 'Kunde ausgewählt.' });
    });
  }

  // -----------------------
  // Init
  // -----------------------
  $(document).ready(function () {
    initCustomerSelect2();
    bindButtons();
    bindApplyButton();
  });

})();
</script>

@endsection