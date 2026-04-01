@extends('admin.layouts.app')
@section('title') WP Angebot Konfiguration @endsection
@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

  <script src="https://cdn.tailwindcss.com"></script>
 <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


  <script>
    // Tailwind config (optional brand tokens)
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: '#8fc73e'
          },
          boxShadow: {
            card: '0 6px 18px rgba(0,0,0,.06)',
          },
          borderRadius: {
            xl: '14px'
          }
        }
      }
    }
  </script>

  <style>
    /* toast */
    #toastWrap{position:fixed;right:1rem;bottom:1rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem; top:20px;}
    .toast{background:#0ea5e9;color:#fff;padding:.6rem .9rem;border-radius:.5rem;box-shadow:0 6px 18px rgba(0,0,0,.15);font-size:.9rem;opacity:0;transform:translateY(10px);transition:all .25s}
    .toast.show{opacity:1;transform:translateY(0)}
    .toast.err{background:#ef4444}
    .toast.ok{background:#10b981}
  </style>

  <style>
    :root { --brand:#0f172a; --ink:#0f172a; }
    * { scrollbar-width: thin; scrollbar-color: #CBD5E1 transparent; }
    *::-webkit-scrollbar { width: 8px; height: 8px; }
    *::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:99px; }
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }

    .btn { @apply inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 bg-white; }
    .btn:hover { @apply bg-slate-50; }
    .btn-primary { background:var(--brand); color:#fff; border-color:var(--brand); }
    .chip { @apply border border-slate-200 rounded-full px-2 py-0.5 text-xs; }
    .card { @apply bg-white border border-slate-200 rounded-xl shadow-card; }
    .table-xs th, .table-xs td { padding:.55rem .6rem; }
    .mono { font-variant-numeric: tabular-nums; }
    .sticky-panel { max-height: calc(100vh - 2rem); overflow: auto; }
    .grid-fit { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:.5rem; }

    /* Wizard */
    .wizard .bar { height:8px; background:#f1f5f9; overflow:hidden; border-radius:999px; }
    .wizard .bar > span { display:block; height:100%; width:0; background:#0f172a; border-radius:999px; transition:width .25s ease; }
    .wizard-steps { display:grid; grid-auto-flow:column; gap:.5rem; }
    .wizard-steps .pill { @apply inline-flex items-center justify-center gap-2 border border-slate-200 rounded-full px-3 py-1 whitespace-nowrap text-sm bg-white cursor-pointer; }
    .wizard-steps .pill.active { background:#8fc73e; color:#fff; border-color:var(--brand); }
    .pill { 
        justify-items: center;
        text-align: center;

    }
    /* Print */
    @media print { .no-print{display:none!important;} .print-area{display:block!important;} .sticky{position:static;} }
    .a4 { width:210mm; min-height:297mm; background:#fff; border:1px solid #e5e7eb; border-radius:12px; margin:auto; box-shadow:0 1px 8px rgba(0,0,0,.08); }
    .a4 .pad { padding:18mm; }
    .watermark { position:absolute; inset:auto 0 20mm 0; text-align:center; font-size:56px; font-weight:800; color:rgba(30,41,59,.06); letter-spacing:.2em; }
  </style>

  <style>
      .btn {
        @apply inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-md shadow-sm transition hover:bg-opacity-90;
      }
      .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700;
      }
    </style>

@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12"> 
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Liste</a></li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Layout: Left Sidebar (sticky) + Center Configurator -->
    <div class="content-body">
      <div class="max-w-screen-3xl mx-auto p-4 md:p-6">
        <div class="grid grid-cols-12 gap-6 items-start">

          <!-- LEFT SIDEBAR -->
          <aside class="col-span-12 lg:col-span-2 space-y-4">
            <!-- Quick Actions -->
              <div class="card p-1 space-y-3 sticky top-4 sticky-panel">
                  <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Aktionen</h3>
                    <span class="chip">v1.0</span>
                  </div>

                  <div class="grid grid-cols-1 gap-1">
                    <button id="btnAutofill" class="btn flex items-center gap-2 p-2">
                      <i data-feather="zap" class="w-4 h-4"></i>
                      <span>Auto-Fill</span>
                    </button>
                    <button id="btnRunTestsTop" class="btn flex items-center gap-2 p-2">
                      <i data-feather="check-circle" class="w-4 h-4"></i>
                      <span>Tests</span>
                    </button>
                    <button id="btnSave" class="btn flex items-center gap-2 p-2">
                      <i data-feather="save" class="w-4 h-4"></i>
                      <span>Speichern</span>
                    </button>
                    <button id="btnLoad" class="btn flex items-center gap-2 p-2">
                      <i data-feather="download" class="w-4 h-4"></i>
                      <span>Laden</span>
                    </button>
                    <button id="btnExport" class="btn flex items-center gap-2 p-2">
                      <i data-feather="share" class="w-4 h-4"></i>
                      <span>Export</span>
                    </button>
                    <button id="btnPrint" class="btn flex items-center gap-2 p-2 no-print">
                      <i data-feather="printer" class="w-4 h-4"></i>
                      <span>Drucken</span>
                    </button>
                  </div>
                </div>


            <!-- Live Technical Summary -->
            <div class="card p-1 sticky top-4 sticky-panel">
              <div class="flex items-center justify-between mb-2"> 
                <button class="btn text-sm no-print" onclick="calcTech()"><i data-feather="refresh-ccw"></i>Refresh</button>
              </div>
              <div id="live_summary" class="text-sm space-y-2 mono"></div>
            </div>

            <!-- Print Preview Panel (hidden until toggled) -->
            <section id="printPanel" class="card p-2 hidden print-area sticky top-4 sticky-panel">
              <div class="a4 relative">
                <div class="pad">
                  <div class="flex items-start justify-between">
                    <div>
                      <h2 class="text-2xl font-semibold">Angebot</h2>
                      <div class="text-slate-500 text-sm">Erstellt am <span id="ppDate"></span></div>
                    </div>
                    <div class="text-right">
                      <div class="text-xl font-bold">Ihre Firma GmbH</div>
                      <div class="text-sm text-slate-600">Musterstraße 1 · 12345 Musterstadt</div>
                      <div class="text-sm text-slate-600">info@ihrefirma.de · +49 123 456</div>
                    </div>
                  </div>
                  <hr class="my-4" />

                  <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                      <div class="font-semibold mb-1">Kundendaten</div>
                      <div id="ppCustomer">PLZ/Ort: —<br />Gebäudetyp: —<br />Baujahr: —</div>
                    </div>
                    <div>
                      <div class="font-semibold mb-1">Projekt</div>
                      <div id="ppProject">Auslegung AT: — °C · Innen: — °C<br />Heizlast: — kW · Empfehlung: — kW</div>
                    </div>
                  </div>

                  <div class="grid md:grid-cols-2 gap-2 mt-4">
                    <div class="text-xs text-slate-600">
                      <div><b>Verbrauch bereinigt:</b> <span id="ppBereinigt">—</span> kWh/a</div>
                      <div><b>Heizenergie:</b> <span id="ppHeizenergie">—</span> kWh/a</div>
                      <div><b>JAZ/COP:</b> <span id="ppJAZ">—</span></div>
                      <div><b>Strom WP/E-Stab:</b> <span id="ppStrom">—</span></div>
                    </div>
                  </div>

                  <h3 class="mt-6 mb-2 font-semibold">Leistungsverzeichnis</h3>
                  <table class="w-full text-sm border border-slate-200">
                    <thead class="bg-slate-50">
                      <tr>
                        <th class="text-left p-2 border-b">Position</th>
                        <th class="text-right p-2 border-b">Menge</th>
                        <th class="text-right p-2 border-b">Einzel (netto)</th>
                        <th class="text-right p-2 border-b">Zeile (netto)</th>
                      </tr>
                    </thead>
                    <tbody id="ppItems"></tbody>
                  </table>

                  <div class="mt-4 grid grid-cols-2">
                    <div class="text-xs text-slate-500 pr-6">
                      <div class="font-semibold mb-1">Hinweise</div>
                      <ul class="list-disc ml-4">
                        <li>Angebot gültig 30 Tage.</li>
                        <li>Lieferzeit vorbehaltlich Verfügbarkeit.</li>
                      </ul>
                    </div>
                    <div class="justify-self-end min-w-[260px]">
                      <div class="flex justify-between"><span>Zwischensumme (netto)</span><span id="ppSubtotal">€0,00</span></div>
                      <div class="flex justify-between"><span>MwSt gesamt</span><span id="ppVat">€0,00</span></div>
                      <div class="flex justify-between font-semibold text-lg"><span>Gesamtsumme (brutto)</span><span id="ppGross">€0,00</span></div>
                    </div>
                  </div>
                </div>
                <div class="watermark select-none">ANGEBOT</div>
              </div>

              <div class="mt-3 no-print flex items-center justify-end gap-2">
                <button class="btn" id="closePreview"><i data-feather="x"></i><span>Vorschau schließen</span></button>
                <button class="btn btn-primary" id="printNow"><i data-feather="printer"></i><span>Jetzt drucken</span></button>
              </div>
            </section>
          </aside>
          <div id="toastWrap" aria-live="polite" aria-atomic="true"></div>


          <!-- CENTER: CONFIGURATOR WIZARD -->
          <main class="col-span-12 lg:col-span-10 space-y-6">
            <!-- Header -->
            <header class="mb-2">
              <div class="flex items-center justify-between gap-1">
                <div>
                  <h1 class="text-3xl font-bold tracking-tight">Wärmepumpen – Gesamt-Konfigurator</h1>
                  <p class="text-slate-600">Objekt → Bestand  → <span class="font-semibold">Verbrauch & Auslegung</span> → Ergebnis → Kalkulation → Angebot.</p>
                </div>
                <div class="flex gap-2 no-print">
                  <button id="btnAutofill-dup" class="btn" onclick="document.getElementById('btnAutofill')?.click()"><i data-feather="zap"></i><span>Auto-Fill</span></button>
                  <button id="btnRunTestsTop-dup" class="btn" onclick="document.getElementById('btnRunTestsTop')?.click()"><i data-feather="check-circle"></i><span>Tests</span></button>
                </div>
              </div>

              <!-- Wizard progress -->
              <div class="wizard mt-4 no-print">
                <div class="bar rounded-full"><span id="wizardProgress"></span></div>
                <div class="wizard-steps mt-3 overflow-x-auto" id="wizardSteps">
                  <div class="pill active" data-step="0"><i data-feather="home"></i><span>Objekt</span></div>
                  <div class="pill" data-step="1"><i data-feather="server"></i><span>Bestand Heizung</span></div>
                  <div class="pill" data-step="2"><i data-feather="database"></i><span>Verbrauch</span></div>
                  <div class="pill" data-step="3"><i data-feather="thermometer"></i><span>Temperaturen</span></div>
                  <div class="pill" data-step="4"><i data-feather="sliders"></i><span>Parameter</span></div>
                  <div class="pill" data-step="5"><i data-feather="activity"></i><span>Ergebnis</span></div>
                  <div class="pill" data-step="6"><i data-feather="file-text"></i><span>Produkt-Stückliste</span></div>
                  <div class="pill" data-step="7"><i data-feather="package"></i><span>Produkte</span></div> 
                  <div class="pill" data-step="8"><i data-feather="percent"></i><span>Kalkulation</span></div>
                  <div class="pill" data-step="9"><i data-feather="bar-chart"></i><span>Finanzplan</span></div>
                </div>
              </div>
            </header>

            <!-- WIZARD STEPS -->
            <section class="space-y-4">
              <!-- Step 0 -->
              <div class="card p-1 space-y-4 wizstep" data-step="0">
                <h2 class="font-semibold text-lg mb-2">A) Objekt & Kunde</h2>
                <div class="grid md:grid-cols-2 gap-1">
                  <label class="text-sm">Gebäudetyp
                    <select id="property_type" name="property_type" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $pt = $detail->property_type ?? 'Einfamilienhaus'; @endphp
                      <option {{ $pt==='Einfamilienhaus'?'selected':'' }}>Einfamilienhaus</option>
                      <option {{ $pt==='Doppelhaushälfte'?'selected':'' }}>Doppelhaushälfte</option>
                      <option {{ $pt==='Wohnung'?'selected':'' }}>Wohnung</option>
                      <option {{ $pt==='Gewerbe'?'selected':'' }}>Gewerbe</option>
                    </select>
                  </label>
                  <label class="text-sm">PLZ / Ort
                    <input id="zip" name="zip" data-offerdetail class="w-full border rounded px-3 py-2"
                          value="{{ $detail->zip ?? '' }}" placeholder="z. B. 60311 Frankfurt" />
                  </label>
                  <label class="text-sm">Baujahr
                    <input id="year_built" name="year_built" type="number" data-offerdetail class="w-full border rounded px-3 py-2"
                          value="{{ $detail->year_built ?? '' }}" placeholder="1995" />
                  </label>
                  <label class="text-sm">Dämmzustand
                    <select id="insulation" name="insulation" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $ins = $detail->insulation ?? 'durchschnittlich'; @endphp
                      <option {{ $ins==='schlecht'?'selected':'' }}>schlecht</option>
                      <option {{ $ins==='durchschnittlich'?'selected':'' }}>durchschnittlich</option>
                      <option {{ $ins==='gut'?'selected':'' }}>gut</option>
                      <option {{ $ins==='renoviert'?'selected':'' }}>renoviert</option>
                    </select>
                  </label>
                  <label class="text-sm">Beheizte Fläche (m²)
                    <input id="area_m2" name="area_m2" type="number" data-offerdetail class="w-full border rounded px-3 py-2"
                          value="{{ $detail->area_m2 ?? '' }}" placeholder="140" />
                  </label>
                  <label class="text-sm">Deckenhöhe (m)
                    <input id="ceiling_m" name="ceiling_m" type="number" step="0.1" data-offerdetail class="w-full border rounded px-3 py-2"
                          value="{{ $detail->ceiling_m ?? '2.5' }}" />
                  </label>
                  <label class="text-sm">Bewohner
                    <input id="occupants" name="occupants" type="number" data-offerdetail class="w-full border rounded px-3 py-2"
                          value="{{ $detail->occupants ?? '3' }}" />
                  </label>
                </div>
                <div class="mt-4 flex justify-end gap-2 no-print">
                  <button class="btn" onclick="wizNext()">Weiter</button>
                </div>
              </div>

              <!-- Step 1 -->
              <div class="card p-1 space-y-4 wizstep hidden" data-step="1">
                <h2 class="font-semibold text-lg mb-2">B) Bestandsanlage</h2>
                <div class="grid md:grid-cols-2 gap-1">
                  <label class="text-sm">Aktueller Wärmeerzeuger
                    <select id="current_source" name="current_source" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $cs = $detail->current_source ?? ''; @endphp
                      <option {{ $cs==='Gas'?'selected':'' }}>Gas</option>
                      <option {{ $cs==='Öl'?'selected':'' }}>Öl</option>
                      <option {{ $cs==='Elektro'?'selected':'' }}>Elektro</option>
                      <option {{ $cs==='Fernwärme'?'selected':'' }}>Fernwärme</option>
                      <option {{ $cs==='Pellet'?'selected':'' }}>Pellet</option>
                      <option {{ $cs==='Keiner'?'selected':'' }}>Keiner</option>
                    </select>
                  </label>
                  <label class="text-sm">Heizflächen
                    <select id="emitters" name="emitters" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $em = $detail->emitters ?? 'Heizkörper'; @endphp
                      <option {{ $em==='Heizkörper'?'selected':'' }}>Heizkörper</option>
                      <option {{ $em==='Fußbodenheizung'?'selected':'' }}>Fußbodenheizung</option>
                      <option {{ $em==='Gemischt'?'selected':'' }}>Gemischt</option>
                    </select>
                  </label>
                  <label class="text-sm">Niedrigste Vorlauftemperatur Winter (°C)
                    <input id="lowest_flow_c" name="lowest_flow_c" type="number" data-offerdetail class="w-full border rounded px-3 py-2"
                          value="{{ $detail->lowest_flow_c ?? '' }}" placeholder="z. B. 45" />
                  </label>
                  <label class="text-sm">Vorhandener Trinkwasserspeicher (L)
                    <input id="dhw_cyl" name="dhw_cyl" type="number" data-offerdetail class="w-full border rounded px-3 py-2"
                          value="{{ $detail->dhw_cyl ?? '' }}" placeholder="z. B. 200" />
                  </label>
                </div>
                <div class="mt-4 flex justify-between gap-2 no-print">
                  <button class="btn" onclick="wizPrev()">Zurück</button>
                  <button class="btn" onclick="wizNext()">Weiter</button>
                </div>
              </div>

              <!-- Step 2 -->
              <div class="card p-1 space-y-4 wizstep hidden" data-step="2">
                <h2 class="font-semibold text-lg mb-2">C) Verbrauch & Anlage</h2>
                <div class="grid md:grid-cols-2 gap-4">
                  <label class="text-sm">Jahresverbrauch gesamt (Heizen+WW) [kWh/a]
                    <input id="annual_kwh" name="annual_kwh" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->annual_kwh ?? '0' }}" min="0">
                    <span class="text-xs text-slate-500">Mittelwert 2–3 Jahre.</span>
                  </label>
                  <label class="text-sm">Alter der Heizungsanlage [Jahre]
                    <input id="age_years" name="age_years" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->age_years ?? '18' }}" min="0">
                    <span class="text-xs text-slate-500">&gt;10: −5 %, &gt;15: −10 %, &gt;20: −15 %</span>
                  </label>
                  <label class="text-sm">Heiztechnik
                    <select id="heating_tech" name="heating_tech" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $ht = $detail->heating_tech ?? 'brennwert'; @endphp
                      <option value="brennwert" {{ $ht==='brennwert'?'selected':'' }}>Brennwert (Gas/Öl)</option>
                      <option value="niedertemp" {{ $ht==='niedertemp'?'selected':'' }}>Niedertemperatur</option>
                      <option value="konstant" {{ $ht==='konstant'?'selected':'' }}>Konstanttemperatur</option>
                      <option value="pellet" {{ $ht==='pellet'?'selected':'' }}>Pellet</option>
                      <option value="sonstiges" {{ $ht==='sonstiges'?'selected':'' }}>Sonstiges</option>
                    </select>
                  </label>
                  <label class="text-sm">Personen im Haushalt [#]
                    <input id="persons" name="persons" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->persons ?? '3' }}" min="1">
                  </label>
                  <label class="text-sm">WW-Bedarf pro Person [kWh/a·Pers]
                    <input id="dhw_per_person" name="dhw_per_person" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->dhw_per_person ?? '700' }}" min="0" step="50">
                    <span class="text-xs text-slate-500">Typisch 500–800.</span>
                  </label>
                </div>
                <div class="mt-4 flex justify-between gap-2 no-print">
                  <button class="btn" onclick="wizPrev()">Zurück</button>
                  <button class="btn" onclick="wizNext()">Weiter</button>
                </div>
              </div>

              <!-- Step 3 -->
              <div class="card p-1 space-y-4 wizstep hidden" data-step="3">
                <h2 class="font-semibold text-lg mb-2">D) Systemdaten & Temperaturen</h2>
                <div class="grid md:grid-cols-2 gap-4">
                  <label class="text-sm">Vorlauf [°C]
                    <input id="vl" name="vl" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->vl ?? '50' }}" step="1">
                  </label>
                  <label class="text-sm">Rücklauf [°C]
                    <input id="rl" name="rl" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->rl ?? '40' }}" step="1">
                  </label>

                  <label class="text-sm">ΔT [°C]
                    <input id="delta" name="delta" type="number" class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->delta ?? '' }}" step="1" readonly>
                  </label>
                  <label class="text-sm">Wärmeverteilung
                    <select id="distribution" name="distribution" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $dist = $detail->distribution ?? 'radiators'; @endphp
                      <option value="fbh" {{ $dist==='fbh'?'selected':'' }}>Fußbodenheizung</option>
                      <option value="radiators" {{ $dist==='radiators'?'selected':'' }}>Heizkörper</option>
                      <option value="both" {{ $dist==='both'?'selected':'' }}>Beides</option>
                    </select>
                  </label>
                  <label class="text-sm">Norm-Außentemperatur (NAT) [°C]
                    <input id="nat" name="nat" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->nat ?? '-12' }}" step="1">
                  </label>
                  <label class="text-sm">Gewünschter Bivalenzpunkt [°C] (−3 … −7)
                    <input id="biv" name="biv" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->biv ?? '-6' }}" step="0.5" min="-7" max="-5">
                  </label>
                  <label class="text-sm">Innenraum-Soll [°C]
                    <input id="ti" name="ti" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->ti ?? '20' }}" step="0.5">
                  </label>
                  <label class="text-sm md:col-span-2">WP-Deckungsanteil Heizen [%] (Rest E-Stab)
                    <input id="wp_share_pct" name="wp_share_pct" type="range" min="70" max="100"
                          value="{{ $detail->wp_share_pct ?? '95' }}" class="w-full" data-offerdetail
                          oninput="updateSliderLabel('wp_share_pct')">
                    <div class="flex justify-between text-xs text-slate-500">
                      <span>70%</span><span id="wp_share_pct_label" class="font-medium">{{ $detail->wp_share_pct ?? 95 }}%</span><span>100%</span>
                    </div>
                  </label>
                </div>
                <div class="mt-4 flex justify-between gap-2 no-print">
                  <button class="btn" onclick="wizPrev()">Zurück</button>
                  <button class="btn" onclick="wizNext()">Weiter</button>
                </div>
              </div>

              <!-- Step 4 -->
              <div class="card p-1 space-y-4 wizstep hidden" data-step="4">
                <h2 class="font-semibold text-lg mb-2">E) Rechenparameter</h2>
                <div class="grid md:grid-cols-2 gap-4">
                  <label class="text-sm">Vollaststunden (FLH) [h/a]
                    <input id="flh" name="flh" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->flh ?? '2200' }}" min="1200" max="3000" step="50">
                    <span class="text-xs text-slate-500">Typisch 2000–2500.</span>
                  </label>
                  <label class="text-sm">Peak-Faktor (NAT-Spitze/Mittel)
                    <input id="peak_factor" name="peak_factor" type="number" data-offerdetail class="w-full border rounded px-3 py-2 mono"
                          value="{{ $detail->peak_factor ?? '1.25' }}" step="0.05" min="1.0" max="1.5">
                  </label>
                  <label class="text-sm">Korrektur Verteilung
                    <select id="dist_mult" name="dist_mult" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $dm = (string)($detail->dist_mult ?? '1.10'); @endphp
                      <option value="0.90" {{ $dm==='0.90'?'selected':'' }}>FBH (−10 %)</option>
                      <option value="1.00" {{ $dm==='1.00'?'selected':'' }}>Beides (±0 %)</option>
                      <option value="1.10" {{ $dm==='1.10'?'selected':'' }}>Heizkörper (+10 %)</option>
                    </select>
                  </label>
                  <label class="text-sm">Korrektur Vorlauf
                    <select id="vl_mult" name="vl_mult" data-offerdetail class="w-full border rounded px-3 py-2">
                      @php $vm = (string)($detail->vl_mult ?? '1.05'); @endphp
                      <option value="0.90" {{ $vm==='0.90'?'selected':'' }}>≤ 35 °C (−10 %)</option>
                      <option value="1.00" {{ $vm==='1.00'?'selected':'' }}>35–45 °C (±0 %)</option>
                      <option value="1.05" {{ $vm==='1.05'?'selected':'' }}>45–55 °C (+5 %)</option>
                      <option value="1.15" {{ $vm==='1.15'?'selected':'' }}>&gt; 55 °C (+15 %)</option>
                    </select>
                  </label>
                </div>

                <div class="mt-4 grid md:grid-cols-3 gap-4">
                  <div class="card p-4">
                    <div class="text-xs text-slate-500">JAZ/COP (berechnet, min 3.0)</div>
                    <div class="text-2xl font-semibold"><span id="jaz_view">—</span></div>
                    <div id="jaz_badge" class="mt-2 hidden text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">angehoben auf Mindest-COP 3,0</div>
                  </div>
                  <div class="card p-4">
                    <div class="text-xs text-slate-500">COP-Restriktion</div>
                    <div class="text-2xl font-semibold">≥ 3,0</div>
                    <div class="mt-2 text-xs text-slate-500">Gilt für Stromberechnung.</div>
                  </div>
                  <div class="card p-4">
                    <div class="text-xs text-slate-500">Bivalenz-Band</div>
                    <div class="text-2xl font-semibold">−7 … −5 °C</div>
                    <div class="mt-2 text-xs text-slate-500">Planungsziel (einstellbar).</div>
                  </div>
                </div>

                <div class="mt-4 flex justify-between gap-2 no-print">
                  <button class="btn" onclick="wizPrev()">Zurück</button>
                  <button class="btn" onclick="wizNext(); calcTech();">Ergebnis</button>
                </div>
              </div>

              <!-- Step 5 -->
              <div class="card p-1 space-y-4 wizstep hidden" data-step="5">
                <div class="flex items-center justify-between mb-2">
                  <h2 class="font-semibold text-lg">F) Ergebnis & Vorschlag</h2>
                  <div class="flex gap-2 no-print">
                    <button class="btn" onclick="copySummary()"><i data-feather="clipboard"></i><span>Kopieren</span></button>
                    <button class="btn" onclick="downloadJSON()"><i data-feather="share"></i><span>JSON</span></button>
                  </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                  <div class="card p-4">
                    <h3 class="font-semibold mb-1"><i class="feather icon-folder primary"></i> Verbrauch & Korrekturen</h3>
                    <div id="box_consumption" class="text-sm space-y-1 mono"></div>
                  </div>
                  <div class="card p-4">
                    <h3 class="font-semibold mb-1"><i class="fa fa-fire primary"></i> Heizlast & Anteile</h3>
                    <div id="box_load" class="text-sm space-y-1 mono"></div>
                  </div>
                  <div class="card p-4">
                    <h3 class="font-semibold mb-1">⚡ Strombedarf (Heizen)</h3>
                    <div id="box_power" class="text-sm space-y-1 mono"></div>
                    <div class="mt-2 text-xs text-slate-500" id="cop_note"></div>
                  </div>
                  <div class="card p-4">
                    <h3 class="font-semibold mb-1"><i class="fa fa-hand-o-right primary"></i> Vorschlag WP-Größe</h3>
                    <div id="box_suggestion" class="text-sm space-y-2"></div>
                    <div class="mt-3 flex gap-2 no-print">
                      <button class="btn btn-primary" onclick="calcTech(); berechneEmpfehlungen(); wizGo(6);">
                        <i class="feather icon-thumbs-up"></i><span>Empfehlungen</span>
                      </button>
                      <button class="btn" onclick="calcTech()">
                        <i data-feather="refresh-ccw"></i><span>Neu berechnen</span>
                      </button>
                    </div>
                  </div>
                </div>

                <p class="text-xs text-slate-500 mt-2">Hinweis: Vereinfachtes, praxisnahes Modell. Für Förderungen/Normnachweise Heizlast nach DIN EN 12831 erstellen.</p>

                <div class="mt-4 flex justify-between gap-2 no-print">
                  <button class="btn" onclick="wizPrev()">Zurück</button>
                  <button class="btn" onclick="wizNext()">Weiter</button>
                </div>
              </div>

              <!-- Step 6 -->
              <div class="card p-1 space-y-4 wizstep hidden" data-step="6">
                <div class="flex items-center justify-between gap-2 mb-2">
                  <h2 class="font-semibold text-lg">H) Angebot zusammenstellen</h2>
                  <div class="hidden md:flex gap-2 no-print">
                    <button id="btnSave-dup" class="btn" onclick="document.getElementById('btnSave')?.click()"><i data-feather="save"></i><span>Speichern</span></button>
                    <button id="btnLoad-dup" class="btn" onclick="document.getElementById('btnLoad')?.click()"><i data-feather="download"></i><span>Laden</span></button>
                    <button id="btnExport-dup" class="btn" onclick="document.getElementById('btnExport')?.click()"><i data-feather="share"></i><span>Export</span></button>
                    <button class="btn" id="togglePreview"><i data-feather="eye"></i><span>Druckvorschau</span></button>
                  </div>
                </div>

                <div class="grid grid-cols-12 gap-4 items-start">
                  <!-- Empfehlungen + Katalog -->
                  <div class="col-span-12 lg:col-span-6 space-y-3">
                    <div class="card p-2">
                      <div class="flex items-center justify-between">
                        <h3 class="font-semibold">Empfohlene Produkte</h3>
                        <div class="flex items-center gap-2">
                          <i data-feather="search"></i>
                          <input id="catalogSearch" class="border rounded px-3 py-1.5" placeholder="Katalog durchsuchen…" />
                        </div>
                      </div>
                      <div id="recs" class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2"></div>
                    </div>

                    <div class="card p-2">
                      <h4 class="text-sm font-semibold text-slate-700">Katalog</h4>
                      <div id="catalog" class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2"></div>
                    </div>
                  </div>

                  <!-- Angebotseditor (Positionen) -->
                  <div class="col-span-12 lg:col-span-6">
                    <div class="card p-1 space-y-3 lg:sticky lg:top-16 sticky-panel" id="angebotEditor" style="position: relative;top: 0px;width: 753px;">
                      <div class="flex items-center justify-between gap-2">
                        <h3 class="font-semibold">Positionen</h3>
                        <button id="btnAddCustom" class="btn"><i data-feather="plus-square"></i><span>Eigene Position</span></button>
                      </div>

                      <div class="overflow-auto border rounded relative">
                        <div class="absolute right-2 -top-8 text-sm text-slate-500 no-print">Tipp: Mit <kbd>Tab</kbd> springen</div>
                        <table class="min-w-full table-xs">
                          <thead class="bg-slate-50">
                            <tr>
                              <th class="text-left">Position</th>
                              <th>Menge</th>
                              <th>Einzel €</th>
                              <th>Zeile Netto €</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody id="offerBody"></tbody>
                        </table>
                      </div>

                      <div class="grid md:grid-cols-2 gap-1">
                        <div class="space-y-2">
                          <label class="block text-sm">Globaler Rabatt %</label>
                          <input id="global_discount" name="global_discount_pct" data-offerdetail type="number"
                                value="{{ $detail->global_discount_pct ?? 0 }}" class="w-full border rounded px-3 py-2" />

                          <label class="block text-sm">Globaler Aufschlag % <span class="text-slate-500 text-xs">(deaktiviert)</span></label>
                          <input id="global_markup" type="number" value="0" class="w-full border rounded px-3 py-2 opacity-50" disabled title="Aufschlag ist aktuell deaktiviert" />

                          <div class="grid grid-cols-3 gap-2 items-end">
                            <div class="col-span-2">
                              <label class="block text-sm">Versand (netto €)</label>
                              <input id="shipping" name="shipping_net_eur" data-offerdetail type="number"
                                    value="{{ $detail->shipping_net_eur ?? 0 }}" class="w-full border rounded px-3 py-2" />
                            </div>
                            <div>
                              <label class="block text-sm">MwSt Versand % <span class="text-slate-500 text-xs">(deaktiviert)</span></label>
                              <input id="shipping_vat" type="number" value="19" class="w-full border rounded px-3 py-2 opacity-50" disabled title="MwSt ist aktuell deaktiviert" />
                            </div>
                          </div>

                          <label class="inline-flex items-center gap-2 text-sm">
                            <input id="apply_global_to_shipping" name="apply_global_to_shipping" data-offerdetail type="checkbox" class="h-4 w-4"
                                  {{ ($detail->apply_global_to_shipping ?? false) ? 'checked' : '' }} />
                            Globalen Rabatt auch auf Versand anwenden
                          </label>
                        </div>

                        <div class="bg-slate-50 border rounded p-1">
                          <div class="flex justify-between"><span>Zwischensumme (netto)</span><span id="sum_subtotal">€0,00</span></div>
                          <div class="flex justify-between"><span>Nach globalen Anpassungen</span><span id="sum_after">€0,00</span></div>
                          <div class="flex justify-between"><span>MwSt gesamt</span><span id="sum_vat">€0,00</span></div>
                          <div class="flex justify-between font-semibold text-lg"><span>Gesamtsumme (brutto)</span><span id="sum_gross">€0,00</span></div>
                          <div class="flex justify-between text-sm text-slate-600 mt-1"><span>Deckungsbeitrag (Marge)</span><span id="sum_margin">€—</span></div>
                        </div>
                      </div>

                      <details class="mt-2">
                        <summary class="cursor-pointer text-sm text-slate-600">Entwickler: Selbsttests</summary>
                        <ul id="testResults" class="list-disc pl-5 text-sm"></ul>
                      </details>
                    </div>

                    <div class="flex items-center justify-between no-print mt-3">
                      <button 
                          class="btn btn-outline-success round mr-1 mb-1 waves-effect waves-light" 
                          id="saveProductList">
                          <i class="feather icon-save"></i> Stückliste speichern
                        </button>
                     </div>
                  </div>
                </div>
              </div> 
              <!-- TAB: Products -->
              <div id="tab-products" class="tab-content wizstep hidden" data-step="7">
                <div class="card p-4 space-y-4">
                  <h3 class="font-semibold mb-2">Produkte (Material)</h3>

                  <div class="overflow-auto border rounded">
                    <table class="min-w-full table-sm">
                      <thead class="bg-slate-50">
                        <tr>
                          <th>Bild</th>
                          <th>Produkt</th>
                          <th>Menge</th>
                          <th class="text-center">Einheit</th>
                          <th>Einzel €</th>
                          <th>Netto €</th>
                          <th>Aktionen</th>
                        </tr>
                      </thead>
                      <tbody id="productRows"></tbody>
                    </table>
                  </div>

                  <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Materialkosten gesamt</span>
                    <span id="products_total" class="font-medium">€0,00</span>
                  </div>
                </div>
              </div> 

              <div id="productGallery" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-4 max-w-3xl w-full space-y-4">
                  <div class="flex justify-between items-center">
                    <h3 class="font-semibold">Produkt auswählen</h3>
                    <button onclick="closeGallery()" class="text-slate-500">✕</button>
                  </div>
                  <input type="text" id="gallerySearch" placeholder="Suche…" class="border px-2 py-1 w-full rounded">
                  <div id="galleryGrid" class="grid grid-cols-4 gap-3 mt-3"></div>
                </div>
              </div> 
                  <!-- TAB: Kalkulation -->
              <div id="tab-kalkulation" class="tab-content wizstep hidden" data-step="8">
                <!-- Material-Kosten -->
                <div class="card p-2 space-y-3 mt-0">
                  <div class="flex justify-between items-center">
                    <h3 class="font-semibold">Materialkosten (EK)</h3>
                    <button id="btnZeroMat" class="btn text-xs"><i data-feather="trash-2"></i> Zurücksetzen</button>
                  </div>
                  <div class="grid md:grid-cols-3 gap-2">
                    <label class="text-sm">Material EK (€)
                          <input id="k_mat_ek" name="k_mat_ek" data-offerdetail type="number"
                                value="{{  $totals->mat_sum ?? 0 }}"
                                class="w-full border rounded px-3 py-2" />
                        </label>

                        <label class="text-sm">Kleinmaterial EK (€)
                          <input id="k_klein_ek" name="k_klein_ek" data-offerdetail type="number"
                                value="{{ $detail->k_klein_ek ?? 0 }}"
                                class="w-full border rounded px-3 py-2" />
                        </label>

                        <label class="text-sm">Transport (€)
                          <input id="k_transport" name="k_transport" data-offerdetail type="number"
                                value="{{ $detail->k_transport ?? 0 }}"
                                class="w-full border rounded px-3 py-2" />
                        </label>

                  </div>
                </div>

                <!-- Team & Positionen (Lohn) -->
                <div class="card p-2 space-y-4">
                  <h3 class="font-semibold">Team & Positionen (Lohn-EK)</h3>

                  <!-- Input row -->
                  <div class="flex flex-wrap gap-2 items-end">
                    <select id="pos_role" class="pos-role border rounded px-2 py-1 text-sm w-48">
                      <option></option> <!-- blank for placeholder -->
                      <!-- will be populated with POSITIONS -->
                    </select>
                    <input id="pos_rate" type="number" class="w-24 border rounded px-2 py-1 text-sm text-right" placeholder="€/h" />
                    <input id="pos_qty" type="number" class="w-16 border rounded px-2 py-1 text-sm text-right" placeholder="Anz" value="1" />
                    <input id="pos_hpp" type="number" class="w-24 border rounded px-2 py-1 text-sm text-right" placeholder="Std/P" value="8" />
                    <button id="btnAddPos" class="btn"><i data-feather="plus"></i><span>Hinzufügen</span></button>
                  </div>


                  <!-- Table -->
                  <div class="overflow-auto border rounded">
                    <table class="min-w-full table-xs">
                      <thead class="bg-slate-50">
                        <tr>
                          <th class="text-left">Rolle</th>
                          <th class="text-right">€/h</th>
                          <th class="text-center">Anz</th>
                          <th class="text-center">Std/P</th>
                          <th class="text-right">Std ges.</th>
                          <th class="text-right">Summe (EK)</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody id="positionRows"></tbody>
                    </table>

                    <button type="button" id="btnSaveEmployees"  class="btn  btnSaveEmployees btn-flat-primary border-primary text-primary mr-1 mb-1 waves-effect waves-light">
                        <i class="feather icon-user"></i> Speichern
                    </button>

                  </div>

                  <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Lohn-EK (Summe)</span>
                    <span id="k_lohn_ek_view" class="font-medium">€0,00</span>
                  </div>
                </div>

                <!-- Tools -->
                <div class="card p-2 space-y-4">
                  <h3 class="font-semibold">Tools / Geräte</h3>

                  <!-- Input row -->
                  <div class="flex flex-wrap gap-2 items-end">
                    <select id="tool_asset" class="border rounded px-2 py-1 text-sm w-64">
                      <option></option>
                      <!-- Dynamically filled with assets -->
                    </select>

                    <input id="tool_rate" type="number"
                          class="w-24 border rounded px-2 py-1 text-sm text-right"
                          placeholder="€/Stk" />

                    <input id="tool_qty" type="number"
                          class="w-16 border rounded px-2 py-1 text-sm text-right"
                          placeholder="Anz"
                          value="1" />

                    <input id="tool_total" type="number"
                          class="w-28 border rounded px-2 py-1 text-sm text-right"
                          placeholder="Gesamt €"
                          readonly />

                    <button id="btnAddTool" class="btn">
                      <i data-feather="plus"></i><span>Hinzufügen</span>
                    </button>
                  </div>

                  <!-- Table -->
                  <div class="overflow-auto border rounded">
                    <table class="min-w-full table-xs">
                      <thead class="bg-slate-50">
                        <tr>
                          <th class="text-left">Name</th>
                          <th class="text-right">€/Stk</th>
                          <th class="text-center">Anz</th>
                          <th class="text-right">Summe (EK)</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody id="toolRows"></tbody>
                    </table>

                        <button type="button" id="btnSaveTools"  class="btn btn-flat-primary border-primary text-primary mr-1 mb-1 waves-effect waves-light">
                        <i class="feather icon-settings"></i> Speichern
                    </button>
                  </div>

                  <!-- Total -->
                  <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Tools-EK (Summe)</span>
                    <span id="k_tools_ek_view" class="font-medium">€0,00</span>
                  </div>
                </div>

                <!-- Gemeinkosten + Zuschläge -->
                <div class="card p-5 space-y-4">
                  <h3 class="font-semibold">Gemeinkosten & Zuschläge</h3>
                  <div class="grid md:grid-cols-3 gap-2">
                    <label class="text-sm">Vertrieb %
                      <input id="k_gk_vertrieb" name="k_gk_vertrieb" data-offerdetail type="number"
                            value="{{ $detail->k_gk_vertrieb ?? 3 }}" class="w-full border rounded px-3 py-2" />
                    </label>
                    <label class="text-sm">Büro/Verwaltung %
                      <input id="k_gk_buv" name="k_gk_buv" data-offerdetail type="number"
                            value="{{ $detail->k_gk_buv ?? 2 }}" class="w-full border rounded px-3 py-2" />
                    </label>
                    <label class="text-sm">Wagnis %
                      <input id="k_gk_wagnis" name="k_gk_wagnis" data-offerdetail type="number"
                            value="{{ $detail->k_gk_wagnis ?? 5 }}" class="w-full border rounded px-3 py-2" />
                    </label>
                  </div>

                  <div class="grid md:grid-cols-2 gap-2">
                    <label class="text-sm">Material-Zuschlag %
                      <input id="k_z_mat" name="k_z_mat" data-offerdetail type="number"
                            value="{{ $detail->k_z_mat ?? 35 }}" class="w-full border rounded px-3 py-2" />
                    </label>
                    <label class="text-sm">Lohn-Zuschlag %
                      <input id="k_z_lohn" name="k_z_lohn" data-offerdetail type="number"
                            value="{{ $detail->k_z_lohn ?? 35 }}" class="w-full border rounded px-3 py-2" />
                    </label>
                  </div>

                  <div class="flex gap-2">
                    <button id="btnCalc" class="btn btn-primary"><i data-feather="cpu"></i><span>Berechnen</span></button>
                    <button id="btnApplyToOffer" class="btn"><i data-feather="plus"></i><span>Übernehmen</span></button>
                  </div>
                </div>
              </div> 
                <!-- Hidden dev tests bridge -->
                <button id="btnRunTests" class="hidden"></button>

                <!-- TAB: Finanzplan -->
                <div id="tab-finanzplan" class="tab-content wizstep" data-step="9">  
                  <div class="card p-4 space-y-4">
                    <h3 class="font-semibold mb-2">Finanzplan Übersicht</h3>

                    <div class="grid grid-cols-3 gap-4 text-sm mt-4">
                        <div>
                          <div class="text-slate-500">Material-EK</div>
                          <div id="ov_mat" class="font-medium">€0,00</div>
                        </div>
                        <div>
                          <div class="text-slate-500">Gemeinkosten</div>
                          <div id="ov_gk" class="font-medium">€0,00</div>
                        </div>
                        <div>
                          <div class="text-slate-500">VK gesamt</div>
                          <div id="ov_vk" class="font-semibold">€0,00</div>
                        </div>
                      </div>

                    <div id="finanzOut" class="text-sm space-y-2"></div>
                  </div>

                  <div class="mt-4 flex justify-between gap-2 no-print">
                    <button class="btn" onclick="wizPrev()">Zurück</button>
                    <button class="btn" onclick="wizNext()">Weiter</button>
                  </div>
                </div> 
            </section>
          </main>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

<!-- CHUNK 8/8 — SCRIPTS + CLOSERS -->
@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  window.BOOTSTRAP = @json($bootstrap ?? []);
  window.__WP_BOOTSTRAP__ = window.BOOTSTRAP; // alias, so both names work
  window.APP = window.BOOTSTRAP;              // you also use window.APP later
   window.ROUTES = {
    masterSets: @json(route('offers.wp.masterSets', ['offer' => $offer->id])),
    breakdownBase: @json(url('/admin/master-sets')),
    assetsList: @json(route('assets.list')),
    offerAssetsIndex: @json(route('offers.assets.index', ['offer'=>$offer->id, 'folder'=>$folder->id])),
    offerAssetsUpdate: @json(route('offers.assets.update', ['offer'=>$offer->id, 'folder'=>$folder->id])),
  };
  window.OFFER_ID  = {{ $offer->id }};
  window.FOLDER_ID = {{ $folder->id }};
  window.CSRF      = '{{ csrf_token() }}';
</script>

<script>

/* ------------------------------- Helpers ------------------------------- */
const n = (v) => { const x = parseFloat(v); return isFinite(x) ? x : 0; };
const money = (nr) => '€' + (n(nr).toFixed(2).replace('.', ','));
const pctTxt = (p) => (n(p)).toFixed(2).replace('.', ',') + '%';
const round = (v, d = 1) => { const p = Math.pow(10, d); return Math.round(v * p) / p; };
const nearestSize = (kW) => [5, 6, 7, 8, 9, 10, 12, 14, 16, 20, 30]
  .reduce((a, b) => Math.abs(b - kW) < Math.abs(a - kW) ? b : a, 5);

const ageLoss = (y) => y > 20 ? 0.15 : y > 15 ? 0.10 : y > 10 ? 0.05 : 0;
const estJAZ = (vl, dist) => { let x = vl <= 35 ? 4.0 : vl <= 45 ? 3.5 : vl <= 55 ? 3.0 : 2.6; if (dist === 'fbh') x += 0.1; if (dist === 'radiators') x -= 0.1; return round(Math.max(2.0, x), 1); };
const fracAtBiv = (Ti, Tnat, Tbiv) => { const d = (Ti - Tnat); if (d <= 0) return 1; let f = (Ti - Tbiv) / d; return Math.min(1.1, Math.max(0, f)); };

function updateSliderLabel(id) {
  const el = document.getElementById(id);
  if (!el) return;
  const value = (el.value ?? '') + '%';
  const byId = document.getElementById(id + '_label');
  if (byId) { byId.textContent = value; return; }
  const container = el.nextElementSibling;
  if (container && container.children && container.children[1]) { container.children[1].textContent = value; }
}

function mapEmittersToDistribution() {
  const e = (document.getElementById('emitters')?.value || 'Heizkörper');
  return e === 'Fußbodenheizung' ? 'fbh' : (e === 'Gemischt' ? 'both' : 'radiators');
}

function setVal(id, val) {
  if (val === null || val === undefined || val === '') return;
  const el = document.getElementById(id); if (!el) return;
  if (el.tagName === 'SELECT') {
    const txt = String(val).toLowerCase();
    const opt = Array.from(el.options).find(o =>
      o.value.toLowerCase() === txt || o.text.toLowerCase() === txt
    );
    el.value = opt ? opt.value : String(val);
  } else {
    el.value = val;
  }
}

function prefillFromBootstrap() {
  const boot = window.__WP_BOOTSTRAP__ || {};
  const pf   = boot.prefill || {};
  const obj  = boot.object || {};
  const heat = obj.heating || {};
  const roof = boot.roof || {};
  const cust = boot.customer || {};

  // Step A — Objekt & Kunde
  setVal('property_type', pf.property_type || 'Einfamilienhaus');
  setVal('zip', pf.zip || (
    cust?.address?.postcode && cust?.address?.city
      ? `${cust.address.postcode} ${cust.address.city}`
      : ''
  ));
  setVal('year_built', pf.year_built || obj.building_year || '');
  setVal('insulation', pf.insulation || 'durchschnittlich');
  setVal('area_m2', pf.area_m2 || obj.heated_area_m2 || '');
  setVal('ceiling_m', 2.5);
  setVal('occupants', pf.occupants || obj.person_count || '');

  // Step B — Bestandsanlage
  // Heizquelle (mapping grob)
  setVal('current_source', (heat.type || '').toLowerCase().includes('öl') ? 'Öl'
    : (heat.type || '').toLowerCase().includes('gas') ? 'Gas'
    : (heat.type || '').toLowerCase().includes('fern') ? 'Fernwärme'
    : (heat.type || '').toLowerCase().includes('pellet') ? 'Pellet'
    : (heat.type || '').toLowerCase().includes('strom') ? 'Elektro'
    : (heat.type || '') || 'Gas');

  // Heizflächen → Verteilung (Fallback Radiatoren)
  const dist = (function () {
    // If you later expose emitters, map here. For now: infer from VL later.
    return 'radiators';
  })();
  setVal('emitters', dist === 'fbh' ? 'Fußbodenheizung' : dist === 'both' ? 'Gemischt' : 'Heizkörper');

  // Niedrigste VL (heuristik nach Verteilung)
  const vlHeuristic = dist === 'fbh' ? 35 : (dist === 'both' ? 45 : 55);
  setVal('lowest_flow_c', vlHeuristic);
  setVal('dhw_cyl', ''); // set when you have storage volume in payload

  // Step C — Verbrauch & Anlage
  const annualHeat = heat.annual_heat_kwh || heat.annual_heating_energy_consumption_kwh || heat.annual_heating_energy_consumption;
  setVal('annual_kwh', annualHeat || 28000);
  // Age: try to infer from building year if we have no system age in payload
  const nowY = (new Date()).getFullYear();
  const ageGuess = (obj.building_year ? Math.max(0, nowY - obj.building_year) : 18);
  setVal('age_years', ageGuess);
  setVal('persons', pf.occupants || obj.person_count || 3);
  setVal('dhw_per_person', 650);

  // Step D — Systemdaten & Temperaturen
  // Set distribution based on emitters select (it also influences COP model)
  setVal('distribution', dist === 'fbh' ? 'fbh' : dist === 'both' ? 'both' : 'radiators');

  // VL/RL heuristics (or pull from payload once available)
  const vl = vlHeuristic;
  const rl = vl - 10;
  setVal('vl', vl);
  setVal('rl', rl);

  // NAT: keep default if you don’t have a climate lookup
  setVal('nat', -12);
  setVal('biv', -6);
  setVal('ti', 20);
  setVal('wp_share', 95); updateSliderLabel('wp_share');

  // Step E — Rechenparameter (keep sensible defaults)
  setVal('flh', 2200);
  setVal('peak_factor', 1.25);
  setVal('dist_mult', dist === 'fbh' ? '0.90' : dist === 'both' ? '1.00' : '1.10');
  setVal('vl_mult', vl <= 35 ? '0.90' : vl <= 45 ? '1.00' : vl <= 55 ? '1.05' : '1.15');

  // Sidebar: append roof info if present
  if (roof && document.getElementById('live_summary')) {
    const html = `
      <div class="border-t pt-2 mt-2">
        <div><b>Dach:</b> ${roof.roof_type || '-'} (${roof.roof_form || '-'})</div>
        <div>Neigung: ${roof.roof_pitch_deg ?? '-'}° · Ausrichtung: ${roof.orientation_text || '-'}</div>
        <div>Fläche: ${roof.roof_area || '-'} · Höhe: ${roof.roof_height ?? '-'}</div>
        ${roof.covering_product ? `<div>Eindeckung: ${roof.covering_product.brand || ''} ${roof.covering_product.name || ''}</div>` : ``}
      </div>`;
    document.getElementById('live_summary').insertAdjacentHTML('beforeend', html);
  }
}


/* ------------------------------- Data ------------------------------- */
const BOOT = window.__WP_BOOTSTRAP__ || {};
const KATALOG_FALLBACK = [
  { sku: "WP-A07", name: "AeroTherm 7 kW Monoblock R32", kategorie: "waermepumpe", typ: "Luft-Wasser", bauart: "Monoblock", kaeltemittel: "R32", phasen: 1, "capacity_kw_A-7_W35": 7.2, scop: 4.5, schall_db: 54, preis: 7200, kosten: 5600, mwst: 19, marke: "Aero" },
  { sku: "WP-A10", name: "AeroTherm 10 kW Monoblock R32", kategorie: "waermepumpe", typ: "Luft-Wasser", bauart: "Monoblock", kaeltemittel: "R32", phasen: 1, "capacity_kw_A-7_W35": 9.8, scop: 4.3, schall_db: 56, preis: 8300, kosten: 6400, mwst: 19, marke: "Aero" },
  { sku: "WP-A12-3P", name: "AeroTherm 12 kW Split R290 (3~)", kategorie: "waermepumpe", typ: "Luft-Wasser", bauart: "Split", kaeltemittel: "R290", phasen: 3, "capacity_kw_A-7_W35": 12.0, scop: 4.6, schall_db: 52, preis: 9800, kosten: 7500, mwst: 19, marke: "Aero" },
  { sku: "WP-V08", name: "BeispielMarke 8 kW R290 Monoblock", kategorie: "waermepumpe", typ: "Luft-Wasser", bauart: "Monoblock", kaeltemittel: "R290", phasen: 1, "capacity_kw_A-7_W35": 8.0, scop: 4.7, schall_db: 49, preis: 9900, kosten: 7800, mwst: 19, marke: "BeispielMarke" },
  { sku: "WP-V12-3P", name: "BeispielMarke 12 kW R290 Monoblock (3~)", kategorie: "waermepumpe", typ: "Luft-Wasser", bauart: "Monoblock", kaeltemittel: "R290", phasen: 3, "capacity_kw_A-7_W35": 12.5, scop: 4.6, schall_db: 50, preis: 11800, kosten: 9300, mwst: 19, marke: "BeispielMarke" },
 
];
let KATALOG = Array.isArray(BOOT.catalog) && BOOT.catalog.length ? BOOT.catalog : [];

/* ------------------------------- State ------------------------------- */
let globalDerived = null;
let globalSuggestKW = null;
let steps = [];
let stepViews = [];
let bar;

/* ------------------------------- Wizard ------------------------------- */
function updateBar(n) {
  if (!bar || !steps.length) return;
  bar.style.width = ((n) / (steps.length - 1)) * 100 + '%';
}
function wizGo(n) {
  steps.forEach(el => el.classList.toggle('active', +el.dataset.step === n));
  stepViews.forEach(el => el.classList.toggle('hidden', +el.dataset.step !== n));
  updateBar(n);
  if (typeof feather !== 'undefined') feather.replace();
  if (n === 7) buildPrintPreview();
  try { calcTech(); } catch (e) {}
  // Gentle scroll to top of content area if present
  document.querySelector('.content-body')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function wizNext() { const cur = +document.querySelector('.pill.active')?.dataset.step || 0; wizGo(Math.min(cur + 1, steps.length - 1)); }
function wizPrev() { const cur = +document.querySelector('.pill.active')?.dataset.step || 0; wizGo(Math.max(cur - 1, 0)); }

/* ------------------------------ Tech calc ------------------------------ */


// ===== Master Set wiring =====
async function api(url) {
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}
function euro(v){ return '€' + (parseFloat(v||0).toFixed(2)).replace('.', ','); }

// Offer boot data from controller
window.APP = window.APP || @json($boot ?? ['offer'=>['id'=>null,'article_group_id'=>null,'service_id'=>null]]);

let CURRENT_SET = null;
let CURRENT_BREAKDOWN = null;

 
async function previewSelectedSet() {
  const id = document.getElementById('ms_select').value;
  if (!id) return;
  const data = await api(`${window.ROUTES.breakdownBase}/${id}/breakdown`);
  CURRENT_SET = data.set?.id;
  CURRENT_BREAKDOWN = data;

  const includeTools = document.getElementById('include_tools').checked;
  const mat = data.totals.material_ek || 0;
  const lohn = data.totals.lohn_ek || 0;
  const tools = includeTools ? (data.totals.tools_ek || 0) : 0;

  document.getElementById('ms_mat').textContent = euro(mat);
  document.getElementById('ms_lohn').textContent = euro(lohn);
  document.getElementById('ms_tools').textContent = euro(tools);

  // enable buttons
  document.getElementById('btnLoadSet').disabled = false;
  document.getElementById('btnPushSetToOffer').disabled = false;
}

function applySetToCalcInputs() {
  if (!CURRENT_BREAKDOWN) return;
  const includeTools = document.getElementById('include_tools').checked;
  const b = CURRENT_BREAKDOWN.totals;
  const matEK = (b.material_ek || 0) + (includeTools ? (b.tools_ek || 0) : 0);

  // Fill Step-G inputs (you can adjust mapping)
  document.getElementById('k_mat_ek').value   = Math.round(matEK);
  document.getElementById('k_klein_ek').value = 0;       // or derive rule-of-thumb
  document.getElementById('k_transport').value= 300;     // default/fixed or compute
  // Lohn-EK: use employee buying (or sum of hours*buy_rate)
  document.getElementById('k_rate_monteur').value = 40;  // keep your defaults; hours already in set
  document.getElementById('k_rate_helfer').value  = 38;
  document.getElementById('k_rate_azubi').value   = 18;
  document.getElementById('k_rate_vk').value      = 30;

  // Put total lohn EK straight, or split into hours * rates. Here: straight.
  document.getElementById('k_h_monteur').value = 0;
  document.getElementById('k_h_helfer').value  = 0;
  document.getElementById('k_h_azubi').value   = 0;
  document.getElementById('k_h_vk').value      = 0;

  // Instead we inject the Lohn-EK total by using rates*hours=0 and letting EK come from "Material-EK" part? 
  // Better: reflect lohnEK by adding as a row later. For Step-G preview: just compute with matEK + lohnEK:
  // Quick hack: reuse "Kleinmaterial EK" to temporarily carry lohnEK into computeCalc
  // If you prefer strictly separated, change computeCalc() to accept a fixed lohnEK override.
  document.getElementById('k_klein_ek').value = (parseFloat(document.getElementById('k_klein_ek').value)||0) + Math.round(b.lohn_ek || 0);

  computeCalc();
    syncCalcFromOfferAndEmployees(CURRENT_BREAKDOWN);

}

function pushSetLinesToOffer() {
  if (!CURRENT_BREAKDOWN) return;
  const B = CURRENT_BREAKDOWN.lines;

  // Products
  (B.products || []).forEach(p => {
    addOfferRow({
      sku: p.sku || '',
      name: p.name || 'Produkt',
      qty: p.qty || 1,
      unit: (p.unit_retail ?? p.unit_purchase ?? 0), // VK fallback to EK
      disc: 0, mark: 0, vat: 19,
      cost: (p.unit_purchase ?? 0)
    });
  });

  // Sub products (Zubehör)
  (B.sub_products || []).forEach(p => {
    addOfferRow({
      sku: p.sku || '',
      name: (p.name || 'Zubehör'),
      qty: p.qty || 1,
      unit: (p.unit_retail ?? p.unit_purchase ?? 0),
      disc: 0, mark: 0, vat: 19,
      cost: (p.unit_purchase ?? 0)
    });
  });

  // Employees (as lines with hours * sale rate)
  (CURRENT_BREAKDOWN.lines.employees || []).forEach(e => {
    const vk = (e.rate_sale && e.hours) ? (e.rate_sale * e.hours) : (e.line_buy || 0) * 1.35; // markup if only buy known
    addOfferRow({
      sku: 'WORK',
      name: `Arbeitszeit: ${e.position || 'Mitarbeiter'} (${e.hours}h)`,
      qty: 1,
      unit: vk,
      disc: 0, mark: 0, vat: 19,
      cost: e.line_buy || 0
    });
  });

  // Tools (if included) – usually as rental/flat fee
  if (document.getElementById('include_tools').checked) {
    const sumTools = (CURRENT_BREAKDOWN.lines.tools || []).reduce((s,t)=> s + (t.total_price || 0), 0);
    if (sumTools > 0) {
      addOfferRow({
        sku: 'TOOLS',
        name: 'Werkzeug-/Gerätekosten (pauschal)',
        qty: 1,
        unit: sumTools, // VK == EK here; add margin later with global discount/markup if desired
        disc: 0, mark: 0, vat: 19,
        cost: sumTools
      });
    }
  }

  recalcAll();
  syncCalcFromOfferAndEmployees(CURRENT_BREAKDOWN);
  wizGo(7); // jump to Angebot
}

// Wire UI
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('btnSuggestSets')?.addEventListener('click', loadSuggestedMasterSets);
  document.getElementById('ms_select')?.addEventListener('change', previewSelectedSet);
  document.getElementById('include_tools')?.addEventListener('change', () => {
    if (CURRENT_BREAKDOWN) previewSelectedSet();
  });
  document.getElementById('btnLoadSet')?.addEventListener('click', applySetToCalcInputs);
  document.getElementById('btnPushSetToOffer')?.addEventListener('click', pushSetLinesToOffer);
});

function autoCalcBiv() {
  const nat = parseFloat(document.getElementById('nat').value) || -12;
  const ti  = parseFloat(document.getElementById('ti').value) || 20;

  const r = globalDerived?.derived;
  if (!r) return;

  const qNat = r.q_nat;          // building load @ NAT
  const qWp  = r.hp_reco_kw;     // recommended WP size

  if (!qNat || !qWp) return;

  // Bivalence calculation
  let tBiv = ti - (qWp / qNat) * (ti - nat);

  // Clamp to your allowed range
  tBiv = Math.max(-7, Math.min(-3, tBiv));

  // Update hidden input
  document.getElementById('biv').value = tBiv.toFixed(1);

  // Also save into results
  r.t_biv = tBiv;
  r.f_biv = qWp / qNat;
}

/* ===== PATCH: Robust getters + auto-detect wp_share id ===== */
  const WP_SHARE_ID = document.getElementById('wp_share_pct') ? 'wp_share_pct' : 'wp_share';
  const valNum = (id, def = 0) => {
    const v = document.getElementById(id)?.value;
    const x = parseFloat(v);
    return Number.isFinite(x) ? x : def;
  };
  const valTxt = (id, def = '') => (document.getElementById(id)?.value ?? def);

  /* keep your existing updateSliderLabel(id) */

  /* Replace your getTechInputs() with this: */
  function getTechInputs() {
    const distSelectVal = document.getElementById('distribution')?.value || mapEmittersToDistribution();
    return {
      annual_kwh:      valNum('annual_kwh', 0),
      age_years:       valNum('age_years', 0),
      persons:         valNum('persons', 1),
      dhw_per_person:  valNum('dhw_per_person', 0),
      vl:              valNum('vl', 0),
      rl:              valNum('rl', 0),
      distribution:    distSelectVal,
      nat:             valNum('nat', -12),
      biv:             valNum('biv', -6),
      ti:              valNum('ti', 20),
      wp_share_pct:    valNum(WP_SHARE_ID, 95),      // <-- auto-detected id
      flh:             Math.max(1200, valNum('flh', 2200)),
      peak_factor:     valNum('peak_factor', 1.25),
      dist_mult:       valNum('dist_mult', 1.0),
      vl_mult:         valNum('vl_mult', 1.0),
    };
  }

  /* Wherever you had updateSliderLabel('wp_share'), replace with: */
  updateSliderLabel(WP_SHARE_ID);

  /* Wherever you prefill the range: */
  setVal(WP_SHARE_ID, 95); updateSliderLabel(WP_SHARE_ID);

/* Replace your “calcTech()” call that set the label with: */
function calcTech() {
  const data = getTechInputs();
  updateSliderLabel(WP_SHARE_ID);

  // --- Run main calculation ---
  const r = computeTech(data);
  globalDerived = { inputs: data, derived: r };
  globalSuggestKW = r.hp_reco_kw;

  // --- Cards / UI updates ---
  const jazView = document.getElementById('jaz_view');
  if (jazView) jazView.textContent = (r.jaz_used ?? 0).toFixed(1);

  const badge = document.getElementById('jaz_badge');
  if (badge) {
    if (r.jaz_used > r.jaz_raw) badge.classList.remove('hidden');
    else badge.classList.add('hidden');
  }

  const boxC = document.getElementById('box_consumption');
  if (boxC) boxC.innerHTML = `
    <div>Verbrauch brutto: <b>${data.annual_kwh.toLocaleString()} kWh/a</b></div>
    <div>Altersverlust (${(r.loss * 100).toFixed(0)}%): <b>−${round(data.annual_kwh * r.loss, 1).toLocaleString()} kWh</b></div>
    <div>= Bereinigt: <b>${round(r.actual_total, 1).toLocaleString()} kWh/a</b></div>
    <div>WW: ${data.persons}×${data.dhw_per_person} = <b>${r.dhw_total.toLocaleString()} kWh/a</b></div>
    <div>= Heizenergie: <b>${round(r.heating_kwh, 1).toLocaleString()} kWh/a</b></div>`;

  const boxL = document.getElementById('box_load');
  if (boxL) boxL.innerHTML = `
    <div>FLH: <b>${data.flh.toLocaleString()} h/a</b></div>
    <div>Ø Heizlast: <b>${round(r.heating_kwh / data.flh, 2)} kW</b></div>
    <div>@NAT (Peak ${data.peak_factor}): <b>${round(r.q_nat, 2)} kW</b></div>
    <div>NAT/Biv/Innen: <b>${data.nat}°C / ${(r.t_biv ?? data.biv).toFixed(1)}°C / ${data.ti}°C</b></div>
    <div>Lastanteil bei NAT: <b>${round((r.f_biv ?? 0) * 100, 0)}%</b></div>
    <div>Verteilung: <b>${data.distribution}</b> (×${data.dist_mult}) | VL: <b>${data.vl}°C</b> (×${data.vl_mult})</div>`;

  const boxP = document.getElementById('box_power');
  if (boxP) boxP.innerHTML = `
    <div>Wärmepumpe (Strom): <b>${Math.round(r.elec_wp).toLocaleString()} kWh/a</b></div>
    <div>E-Stab (Strom): <b>${Math.round(r.elec_estab).toLocaleString()} kWh/a</b></div>
    <div class="border-t pt-1">Gesamt (Heizen): <b>${Math.round(r.elec_total).toLocaleString()} kWh/a</b></div>`;

  const copNote = document.getElementById('cop_note');
  if (copNote) copNote.innerHTML = (r.jaz_used > r.jaz_raw)
    ? `JAZ/COP wurde von ${r.jaz_raw.toFixed(1)} auf <b>${r.jaz_used.toFixed(1)}</b> angehoben (Restriktion ≥ 3,0). Prüfe: niedrigere VL, größere Heizflächen, hydraulischer Abgleich.`
    : `JAZ/COP-Schätzung erfüllt die Restriktion (≥ 3,0).`;

  const boxS = document.getElementById('box_suggestion');
  if (boxS) boxS.innerHTML = `
    <div class="text-lg">Empfohlene WP-Größe: <span class="font-semibold">${r.hp_reco_kw} kW</span></div>
    <div class="text-slate-600">Berechnet: ${r.hp_size_kw} kW → auf marktübliche Größe gerundet.</div>
    <ul class="list-disc ml-5 mt-2 text-slate-700 text-sm">
      <li>Wenn VL dauerhaft &gt;55 °C nötig: Hochtemperatur-WP prüfen.</li>
      <li>Hydraulischer Abgleich &amp; ggf. größere Heizflächen verbessern JAZ.</li>
      <li>Bivalenzpunkt ${r.t_biv.toFixed(1)} °C deckt ~${round(r.f_biv * 100, 0)} % der NAT-Last ab.</li>
    </ul>`;

  const live = document.getElementById('live_summary');
  if (live) live.innerHTML = `
    <div><b>Bereinigt:</b> ${Math.round(r.actual_total).toLocaleString()} kWh/a</div>
    <div><b>Heizenergie:</b> ${Math.round(r.heating_kwh).toLocaleString()} kWh/a</div>
    <div><b>Heizlast @NAT:</b> ${round(r.q_nat, 2)} kW</div>
    <div><b>WP-Anteil:</b> ${Math.round(data.wp_share_pct)}%</div>
    <div><b>JAZ/COP (benutzt):</b> ${r.jaz_used.toFixed(1)}</div>
    <div><b>WP-Strom:</b> ${Math.round(r.elec_wp).toLocaleString()} kWh/a</div>
    <div><b>E-Stab:</b> ${Math.round(r.elec_estab).toLocaleString()} kWh/a</div>
    <div><b>WP-Größe:</b> ${r.hp_reco_kw} kW</div>`;

  // --- Auto-calc Bivalence: write back into input field ---
  autoCalcBiv();

  return r;
}

/* Input listeners: include the detected share id */
['annual_kwh','vl','rl','nat','ti', WP_SHARE_ID, 'flh','peak_factor']
  .forEach(id => document.getElementById(id)?.addEventListener('input', calcTech));

/* On DOMContentLoaded, also call: */
updateSliderLabel(WP_SHARE_ID);

/* sammleAntworten(): store the correct share field */
function sammleAntworten() {
  const get = id => document.getElementById(id)?.value;
  const shareId = WP_SHARE_ID;
  return {
    objekt:  { typ: get('property_type'), plz: get('zip'), baujahr: get('year_built'), daemmung: get('insulation'), flaech_m2: get('area_m2'), decke_m: get('ceiling_m'), bewohner: get('occupants') },
    bestand: { quelle: get('current_source'), heizflaechen: get('emitters'), min_vorlauf: get('lowest_flow_c'), speicher_l: get('dhw_cyl') },
    technik: {
      verbrauch_kwh: get('annual_kwh'), alter: get('age_years'), personen: get('persons'), ww_pp: get('dhw_per_person'),
      vl: get('vl'), rl: get('rl'), verteilung: get('distribution'), nat: get('nat'), biv: get('biv'), ti: get('ti'),
      wp_share: get(shareId), flh: get('flh'), peak: get('peak_factor'), dist_mult: get('dist_mult'), vl_mult: get('vl_mult')
    }
  };
}

/* ΔT sign fix (your bottom helper had it reversed) */
function updateDeltaT() {
  const vl = parseFloat(document.getElementById('vl')?.value || 0);
  const rl = parseFloat(document.getElementById('rl')?.value || 0);
  const delta = vl - rl;                  // <- correct sign
  const el = document.getElementById('delta');
  if (el) el.value = Math.round(delta);
}

function computeTech(inputs) {
  const { annual_kwh, age_years, persons, dhw_per_person,
          vl, distribution, nat, ti, wp_share_pct,
          flh, peak_factor, dist_mult, vl_mult } = inputs;

  const loss = ageLoss(age_years);
  const actual_total = annual_kwh * (1 - loss);
  const dhw_total = persons * dhw_per_person;
  const heating_kwh = Math.max(0, actual_total - dhw_total);

  const q_avg = heating_kwh / flh;       // average load
  const q_nat = q_avg * peak_factor;     // peak load at NAT

  // --- WP sizing ---
  let hp_calc = q_nat * (wp_share_pct/100) * dist_mult * vl_mult;
  const hp_size_kw = round(hp_calc, 2);
  const hp_reco_kw = Math.max(nearestSize(hp_size_kw), 4);

 // --- Bivalence calculation ---
  let f_biv = 0;
  let t_biv = nat; // fallback: assume bivalence at NAT
  if (q_nat > 0 && hp_size_kw > 0) {
    f_biv = hp_size_kw / q_nat;
    t_biv = ti - (hp_size_kw / q_nat) * (ti - nat);
    // Clamp between -7 … -3
    t_biv = Math.max(-7, Math.min(-3, t_biv));
  }


  // --- COP/JAZ ---
  const jaz_raw = estJAZ(vl, distribution);
  const jaz_used = Math.max(3.0, jaz_raw);

  const wp_share = wp_share_pct / 100;
  const elec_wp = (heating_kwh * wp_share) / jaz_used;
  const elec_estab = heating_kwh * (1 - wp_share);
  const elec_total = elec_wp + elec_estab;

  return {
    loss, actual_total, dhw_total, heating_kwh,
    q_avg, q_nat,
    f_biv, t_biv,                // now always returned!
    jaz_raw, jaz_used,
    elec_wp, elec_estab, elec_total,
    hp_size_kw, hp_reco_kw
  };
}

function calcTech() {
  const data = getTechInputs();
  updateSliderLabel('wp_share');
  const r = computeTech(data);
  globalDerived = { inputs: data, derived: r };
  globalSuggestKW = r.hp_reco_kw;

  // Cards
  const jazView = document.getElementById('jaz_view'); if (jazView) jazView.textContent = r.jaz_used.toFixed(1);
  const badge = document.getElementById('jaz_badge'); if (badge) { if (r.jaz_used > r.jaz_raw) badge.classList.remove('hidden'); else badge.classList.add('hidden'); }

  const boxC = document.getElementById('box_consumption'); if (boxC) boxC.innerHTML = `
    <div>Verbrauch brutto: <b>${data.annual_kwh.toLocaleString()} kWh/a</b></div>
    <div>Altersverlust (${(r.loss * 100).toFixed(0)}%): <b>−${round(data.annual_kwh * r.loss, 1).toLocaleString()} kWh</b></div>
    <div>= Bereinigt: <b>${round(r.actual_total, 1).toLocaleString()} kWh/a</b></div>
    <div>WW: ${data.persons}×${data.dhw_per_person} = <b>${r.dhw_total.toLocaleString()} kWh/a</b></div>
    <div>= Heizenergie: <b>${round(r.heating_kwh, 1).toLocaleString()} kWh/a</b></div>`;

  const boxL = document.getElementById('box_load'); if (boxL) boxL.innerHTML = `
    <div>FLH: <b>${data.flh.toLocaleString()} h/a</b></div>
    <div>Ø Heizlast: <b>${round(r.heating_kwh / data.flh, 2)} kW</b></div>
    <div>@NAT (Peak ${data.peak_factor}): <b>${round(r.q_nat, 2)} kW</b></div>
    <div>NAT/Biv/Innen: <b>${data.nat}°C / ${data.biv}°C / ${data.ti}°C</b></div>
    <div>Lastanteil bei NAT: <b>${round(r.f_biv * 100, 0)}%</b></div>
    <div>Verteilung: <b>${data.distribution}</b> (×${data.dist_mult}) | VL: <b>${data.vl}°C</b> (×${data.vl_mult})</div>`;

  const boxP = document.getElementById('box_power'); if (boxP) boxP.innerHTML = `
    <div>Wärmepumpe (Strom): <b>${Math.round(r.elec_wp).toLocaleString()} kWh/a</b></div>
    <div>E-Stab (Strom): <b>${Math.round(r.elec_estab).toLocaleString()} kWh/a</b></div>
    <div class="border-t pt-1">Gesamt (Heizen): <b>${Math.round(r.elec_total).toLocaleString()} kWh/a</b></div>`;

  const copNote = document.getElementById('cop_note'); if (copNote) copNote.innerHTML = (r.jaz_used > r.jaz_raw)
    ? `JAZ/COP wurde von ${r.jaz_raw.toFixed(1)} auf <b>${r.jaz_used.toFixed(1)}</b> angehoben (Restriktion ≥ 3,0). Prüfe: niedrigere VL, größere Heizflächen, hydraulischer Abgleich.`
    : `JAZ/COP-Schätzung erfüllt die Restriktion (≥ 3,0).`;

  const boxS = document.getElementById('box_suggestion'); if (boxS) boxS.innerHTML = `
    <div class="text-lg">Empfohlene WP-Größe: <span class="font-semibold">${r.hp_reco_kw} kW</span></div>
    <div class="text-slate-600">Berechnet: ${r.hp_size_kw} kW → auf marktübliche Größe gerundet.</div>
    <ul class="list-disc ml-5 mt-2 text-slate-700 text-sm">
      <li>Wenn VL dauerhaft &gt;55 °C nötig: Hochtemperatur-WP prüfen.</li>
      <li>Hydraulischer Abgleich &amp; ggf. größere Heizflächen verbessern JAZ.</li>
      <li>Bivalenzpunkt ${(r.t_biv ?? nat).toFixed(1)} °C deckt ~${round((r.f_biv ?? 0) * 100, 0)} % der NAT-Last ab.</li>
    </ul>`;

  // Live summary
  const live = document.getElementById('live_summary'); if (live) live.innerHTML = `
    <div><b>Bereinigt:</b> ${Math.round(r.actual_total).toLocaleString()} kWh/a</div>
    <div><b>Heizenergie:</b> ${Math.round(r.heating_kwh).toLocaleString()} kWh/a</div>
    <div><b>Heizlast @NAT:</b> ${round(r.q_nat, 2)} kW</div>
    <div><b>WP-Anteil:</b> ${Math.round(data.wp_share_pct)}%</div>
    <div><b>JAZ/COP (benutzt):</b> ${r.jaz_used.toFixed(1)}</div>
    <div><b>WP-Strom:</b> ${Math.round(r.elec_wp).toLocaleString()} kWh/a</div>
    <div><b>E-Stab:</b> ${Math.round(r.elec_estab).toLocaleString()} kWh/a</div>
    <div><b>WP-Größe:</b> ${r.hp_reco_kw} kW</div>`;

    autoCalcBiv();

  return r;

}

['annual_kwh','vl','rl','nat','ti','wp_share','flh','peak_factor'].forEach(id => {
  document.getElementById(id)?.addEventListener('input', calcTech);
});



/* ---------------------------- Empfehlungen ---------------------------- */
function productCard(p, empfohlen = false) {
  const div = document.createElement('div');
  div.className = 'card p-1 border border-slate-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow space-y-3';

  // Format price
  const preis = (p.preis ?? 0).toFixed(2).replace('.', ',');
  const name = p.name || 'Unbekanntes Produkt';
  const sku = p.sku || '';
  const marke = p.marke || '';

  // Feature chips
  const chips = [];

  if (p["capacity_kw_A-7_W35"]) 
    chips.push(`${p["capacity_kw_A-7_W35"].toString().replace('.', ',')} kW @A-7/W35`);

  if (p.scop) 
    chips.push(`SCOP ${p.scop}`);

  if (p.schall_db) 
    chips.push(`${p.schall_db} dB`);

  if (p.kaeltemittel) 
    chips.push(p.kaeltemittel);

  if (p.phasen) 
    chips.push(`${p.phasen}~`);

  // Create content
  div.innerHTML = `
    <!-- Header Info -->
    <div class="text-xs text-slate-500">${marke} · ${sku}</div>
    
    <!-- Product Title -->
    <div class="font-medium text-base text-slate-800">${name}</div>

    <!-- Feature Chips -->
    <div class="flex flex-wrap gap-1">
      ${chips.map(c => `
        <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-full">${c}</span>
      `).join('')}
    </div>

    <!-- Price & Action -->
    <div class="flex items-center justify-between mt-2">
      <div class="text-lg font-semibold text-slate-900">€${preis}</div>
      <button 
        class="btn btn-primary flex items-center gap-1 px-3 py-1.5 text-sm" 
        data-sku="${sku}">
        <i data-feather="plus"></i>
        <span>Hinzufügen</span>
      </button>
    </div>

    <!-- Optional Recommendation Badge -->
    ${empfohlen ? `
      <div class="flex items-center gap-1 text-emerald-600 text-xs mt-2">
        <i data-feather="thumbs-up"></i>
        <span>Empfohlen</span>
      </div>` : ''
    }
  `;

  // Add interaction
  div.querySelector('button').addEventListener('click', () => {
    addOfferItemFromProduct(p);
    if (typeof feather !== 'undefined') feather.replace();
  });

  // Initial feather render
  if (typeof feather !== 'undefined') feather.replace();

  return div;
}

function renderRecs(list) { const wrap = document.getElementById('recs'); if (!wrap) return; wrap.innerHTML = ''; list.forEach(p => wrap.appendChild(productCard(p, true))); }
function renderCatalog(list) { const wrap = document.getElementById('catalog'); if (!wrap) return; wrap.innerHTML = ''; list.forEach(p => wrap.appendChild(productCard(p, false))); }

async function berechneEmpfehlungen() {
  if (!globalSuggestKW) { try { calcTech(); } catch (_) {} }
  const kw    = globalSuggestKW || 0;
  const phase = (window.APP?.offer?.service_id ?? '') + '';

  const recsWrap = document.getElementById('recs');
  if (!recsWrap) return;

  const msUrl  = window.ROUTES?.masterSets;
  const bdBase = window.ROUTES?.breakdownBase;
  if (!msUrl || !bdBase) {
    recsWrap.innerHTML = '<div class="text-sm text-red-600">ROUTES.masterSets/breakdownBase fehlen.</div>';
    return;
  }

  recsWrap.innerHTML = '<div class="text-sm text-slate-500">Lade Vorschläge…</div>';

  try {
    const data = await api(`${msUrl}?kw=${encodeURIComponent(kw)}&phase=${encodeURIComponent(phase)}`);
    const sets = Array.isArray(data?.sets) ? data.sets : [];

    if (!sets.length) {
      recsWrap.innerHTML = '<div class="text-sm text-slate-500">Keine passenden Master-Sets gefunden.</div>';
      return;
    }

    recsWrap.innerHTML = '';
    const frag = document.createDocumentFragment();

    sets.forEach(set => {
      const div = document.createElement('div');
      div.className = 'card border p-2 rounded-xl shadow bg-white';

      const matchCls =
        set.match === 'best' ? 'bg-emerald-100 text-emerald-700' :
        set.match === 'okay' ? 'bg-yellow-100 text-yellow-700' :
                               'bg-red-100 text-red-700';

      const safeName = String(set.setname || '').replace(/"/g, '&quot;');

      div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-lg font-semibold text-slate-800">${set.setname}</h3>
          <span class="text-xs px-2 py-0.5 rounded-full ${matchCls}">
            ${set.match === 'best' ? 'Best Match' : set.match === 'okay' ? 'Okay' : 'Schwach'}
          </span>
        </div>
        <small>
          <div class="grid grid-cols-3 gap-1 text-sm">
            <div class="bg-slate-50 rounded p-1 text-center">Material<br><strong>€${set.totals?.material_ek ?? 0}</strong></div>
            <div class="bg-slate-50 rounded p-1 text-center">Lohn<br><strong>€${set.totals?.lohn_ek ?? 0}</strong></div>
            <div class="bg-slate-50 rounded p-1 text-center">Tools<br><strong>€${set.totals?.tools_ek ?? 0}</strong></div>
          </div>
        </small>

        <div class="mt-2 text-xs text-slate-600">
          Produkte: <b>${set.items?.products ?? 0}</b> · MA: <b>${set.items?.employees ?? 0}</b> · Assets: <b>${set.items?.assets ?? 0}</b>
        </div>

        <div class="mt-3 border-t pt-2 flex flex-col items-center">
          <div class="text-lg font-semibold text-slate-900">Gesamt: €${set.totals?.ek_sum ?? 0}</div>
          <button
            class="btn btn-primary add-to-offer"
            data-id="${set.id}"
            data-name="${safeName}"
            data-mat="${set.totals?.material_ek ?? 0}"
            data-lohn="${set.totals?.lohn_ek ?? 0}"
            data-tools="${set.totals?.tools_ek ?? 0}">
            <i class="feather icon-plus"></i> Übernehmen
          </button>
        </div>
      `;

      frag.appendChild(div);
    });

    recsWrap.appendChild(frag);
    if (typeof feather !== 'undefined') feather.replace();

    // Bind click once (delegation)
    if (!recsWrap.dataset.bound) {
      recsWrap.addEventListener('click', async (e) => {
        const btn = e.target.closest('.add-to-offer');
        if (!btn) return;

        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-pulse">Wird übernommen…</span>';

        const setId = btn.dataset.id;
        const setName = btn.dataset.name || `Set ${setId}`;

        // Fallback adding rows from button dataset
        const addFromButtonDataset = () => {
          const mat   = n(btn.dataset.mat);
          const lohn  = n(btn.dataset.lohn);
          const tools = n(btn.dataset.tools);

          if (mat > 0) {
            addOfferRow({
              sku:`SET-${setId}-MAT`,
              name:`${setName}: Material`,
              qty:1, unit:mat, cost:mat,
              type:'Material',
              master_set_id:setId
            });
          }
          if (lohn > 0) {
            addOfferRow({
              sku:`SET-${setId}-WORK`,
              name:`${setName}: Lohnleistung`,
              qty:1, unit:lohn, cost:lohn,
              type:'Lohn',
              master_set_id:setId
            });
          }
          if (tools > 0) {
            addOfferRow({
              sku:`SET-${setId}-TOOLS`,
              name:`${setName}: Werkzeug-/Gerätekosten`,
              qty:1, unit:tools, cost:tools,
              type:'Tools',
              master_set_id:setId
            });
          }
          recalcAll();
        };

        try {
          // Try breakdown API
          const bd = await api(`${bdBase}/${setId}/breakdown`);
          (bd.products || []).forEach(addOfferItemFromProduct);
          (bd.sub_products || []).forEach(addOfferItemFromProduct);

          const toolsSum = (bd.tools || []).reduce((s, t) => s + n(t.total_price), 0);
          if (toolsSum > 0) {
            addOfferRow({
              sku:'TOOLS',
              name:'Werkzeug-/Gerätekosten (pauschal)',
              qty:1, unit:toolsSum, cost:toolsSum,
              type:'Tools',
              master_set_id:setId
            });
          }

          recalcAll();
          syncCalcFromOfferAndEmployees(CURRENT_BREAKDOWN);
          if (typeof wizGo === 'function') wizGo(7);

          btn.innerHTML = '<i data-feather="check"></i> Übernommen';
          if (typeof feather !== 'undefined') feather.replace();
          setTimeout(() => { btn.innerHTML = original; btn.disabled = false; }, 800);
        } catch (err) {
          console.error('Breakdown fehlgeschlagen, nutze Fallback:', err);
          addFromButtonDataset();
          btn.innerHTML = original;
          btn.disabled = false;
          if (typeof feather !== 'undefined') feather.replace();
        }
      });
      recsWrap.dataset.bound = '1';
    }
  } catch (e) {
    console.error('MasterSet API fehlgeschlagen', e);
    recsWrap.innerHTML = '<div class="text-sm text-red-600">MasterSets konnten nicht geladen werden.</div>';
  }
}

/* --------------------------- Angebotseditor --------------------------- */
 /* ---------- Offer table (no discount columns) ---------- */

/* FIX: make bodyEl a function, not an IIFE */
const bodyEl = () => document.getElementById('offerBody');

/* Use whatever price fields exist on the product object (suggest/breakdown/fallback) */
function addOfferItemFromProduct(p) {
  const unit =
    n(p.retail_price) ||
    n(p.price) ||
    n(p.preis) ||
    n(p.purchase_price) ||
    n(p.kosten) ||
    0;
  const qty  = n(p.qty) || 1;
  const cost = n(p.purchase_price) || n(p.kosten) || 0;

  addOfferRow({
    sku:  p.sku || p.article_no || '',
    name: p.name || p.product || 'Position',
    qty,
    unit,
    cost
  });
}

 

function syncRowsToServer() {
  const tbody = bodyEl();
  if (!tbody) return;

  const items = [];

  tbody.querySelectorAll('tr').forEach((tr, idx) => {
    items.push({
      id: tr.dataset.id || null,
      sku: tr.querySelector('.text-slate-500')?.textContent.replace('SKU','').trim() || '',
      name: tr.querySelector('div.font-medium')?.textContent.trim() || '',
      type: tr.dataset.type || null,
      master_set_id: tr.dataset.masterSetId || null,
      notes: tr.querySelector('textarea.notes')?.value || '',
      quantity: parseInt(tr.querySelector('.qty')?.value || 1, 10),
      unit_price: parseFloat(tr.querySelector('.unit')?.value || 0),
      cost: parseFloat(tr.querySelector('.cost')?.textContent || 0)
    });
  });

   $.ajax({
      url: `/offers/${OFFER_ID}/folders/${FOLDER_ID}/details`,   // ✅ include /details
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': CSRF },
      data: { items },
      success: (resp) => {
        toast('Stückliste gespeichert', 'ok');
      },
      error: (xhr) => {
        console.error('Sync error', xhr.responseText);
        toast('Fehler beim Speichern der Stückliste', 'error');
      }
    });

}

document.addEventListener('DOMContentLoaded', () => {
  loadProductList();

  const btnSave = document.getElementById('saveProductList');
  if (btnSave) {
    btnSave.addEventListener('click', () => {
      syncRowsToServer();
    });
  }
});


async function loadProductList() {
  try {
    const resp = await fetch(`/offers/${OFFER_ID}/folders/${FOLDER_ID}/products`);
    if (!resp.ok) throw new Error("Network error");
    const data = await resp.json();

    const tbody = bodyEl();
    if (!tbody) return;
    tbody.innerHTML = ''; // clear old rows

    let firstMaster = null;

    (data.items || []).forEach(item => {
      addOfferRow({
        sku: item.sku,
        name: item.name,
        qty: item.quantity,
        unit: item.unit_price,
        cost: item.cost,
        type: item.type,
        master_set_id: item.master_set_id
      });

      // remember the first master_set_id
      if (!firstMaster && item.master_set_id) {
        firstMaster = item.master_set_id;
      }
    });

    if (firstMaster) {
      window.CURRENT_MASTER_SET_ID = firstMaster;
      console.log("CURRENT_MASTER_SET_ID set to", firstMaster);
    } else {
      console.warn("⚠️ No master_set_id found in product list");
    }

    console.log("Loaded product list", data.items);
  } catch (err) {
    console.error("Load failed", err);
    toast("Fehler beim Laden der Stückliste", "error");
  }
}


async function loadEmployeeList() {
  try {
    const resp = await fetch(`/offers/${OFFER_ID}/folders/${FOLDER_ID}/employees`);
    const data = await resp.json();
    const tbody = document.getElementById('positionRows');
    tbody.innerHTML = '';

    (data.employees || []).forEach(e => {
      addEmployeeRow(e);
    });
  } catch (err) {
    console.error("Error loading employees", err);
  }
}
function addEmployeeRow(e = {}) {
  const tbody = document.getElementById('positionRows');
  if (!tbody) return;

  const qty   = Number(e.qty ?? 1);
  const hpp   = Number(e.hours_per_person ?? 0);
  const rate  = Number(e.rate ?? 0);
  const msId  = e.master_set_id ?? CURRENT_MASTER_SET_ID ?? '';
  const hours = qty * hpp;
  const sum   = hours * rate;

  const tr = document.createElement('tr');
  tr.dataset.masterSetId = msId;

  tr.innerHTML = `
    <td>
      <select class="position-select border rounded px-2 py-1 text-sm w-full">
        ${POSITIONS.map(p =>
          `<option value="${p.id}" ${String(p.id) === String(e.position_id || '') ? 'selected' : ''}>${p.position}</option>`
        ).join('')}
      </select>
    </td>
    <td class="text-right">
      <input type="number" class="rate w-20 text-right border rounded" value="${rate}">
    </td>
    <td class="text-center">
      <input type="number" class="qty w-12 text-center border rounded" value="${qty}">
    </td>
    <td class="text-center">
      <input type="number" class="hpp w-20 text-center border rounded" value="${hpp}">
    </td>
    <td class="text-right hours-total">${hours.toFixed(1)}</td>
    <td class="text-right sum-total">${sum.toFixed(2)}</td>
    <td><button type="button" class="btn btn-xs del">✕</button></td>
  `;

  // remove row
  tr.querySelector('.del').addEventListener('click', () => {
    tr.remove();
    if (typeof recalcPositions === 'function') recalcPositions();
  });

  // live recalc
  const recalcRow = () => {
    const q = Number(tr.querySelector('.qty').value || 0);
    const h = Number(tr.querySelector('.hpp').value || 0);
    const r = Number(tr.querySelector('.rate').value || 0);
    const hrs = q * h;
    const s   = hrs * r;
    tr.querySelector('.hours-total').textContent = hrs.toFixed(1);
    tr.querySelector('.sum-total').textContent   = s.toFixed(2);
    if (typeof recalcPositions === 'function') recalcPositions();
  };
  tr.querySelectorAll('.qty,.hpp,.rate').forEach(el => {
    el.addEventListener('input', recalcRow);
    el.addEventListener('change', recalcRow);
  });

  tbody.appendChild(tr);

  // activate Select2 for the dropdown
  const select = tr.querySelector('.position-select');
  if (select) Select2Boot.initOne(select, e.position_id || null);
}

async function loadAssetsDropdown() {
  try {
    const resp = await fetch(window.ROUTES.assetsList, {
      headers: { 'Accept': 'application/json' }
    });
    if (!resp.ok) throw new Error("HTTP " + resp.status);
    const ct = resp.headers.get('content-type') || '';
    if (!ct.includes('application/json')) throw new Error("Non-JSON response");
    const assets = await resp.json();

    const $sel = $('#tool_asset');
    $sel.empty().append('<option></option>');
    assets.forEach(a => $sel.append(new Option(`${a.item} ${a.model}`, a.id)));

    // Select2 is now guaranteed to exist
    $sel.select2({
      tags: true,
      width: '100%',
      placeholder: 'Asset auswählen oder neu eingeben'
    });
  } catch (err) {
    console.error("Failed to load assets", err);
    (window.toast ? toast : alert)("Fehler beim Laden der Asset-Liste!", "error");
  }
}

 
document.getElementById('btnSaveTools')?.addEventListener('click', () => {
  const rows = [];
  document.querySelectorAll('#toolRows tr').forEach(tr => {
    const id   = tr.dataset.id || null;
    const asset_id = tr.dataset.assetId || null;
    const name = tr.querySelector('.name').textContent.trim();
    const rate = +tr.querySelector('.rate').value;
    const qty  = +tr.querySelector('.qty').value;
    const sum  = rate * qty;

    rows.push({ id, asset_id, name, rate, qty, sum_total: sum });
  });

  $.ajax({
    url: `/offers/${OFFER_ID}/folders/${FOLDER_ID}/assets`,
    method: 'PATCH',
    headers: { 'X-CSRF-TOKEN': CSRF },
    data: { rows },
    success: () => toast("Tools gespeichert"),
    error: (xhr) => {
      console.error("Save tools error", xhr.responseText);
      toast("Fehler beim Speichern der Tools", "error");
    }
  });
});


async function loadAssetList() {
  try {
    const resp = await fetch(window.ROUTES.offerAssetsIndex, {
      headers: { 'Accept': 'application/json' }
    });
    if (!resp.ok) throw new Error("HTTP " + resp.status);
    const data = await resp.json();

    const tbody = document.getElementById('toolRows');
    tbody.innerHTML = '';
    (data.assets || []).forEach(addToolRow);
    recalcTools();
  } catch (err) {
    console.error("Error loading saved tools", err);
    (window.toast ? toast : alert)("Fehler beim Laden der Tools","error");
  }
}

document.getElementById('btnSaveTools')?.addEventListener('click', () => {
  const rows = [];
  document.querySelectorAll('#toolRows tr').forEach(tr => {
    const id   = tr.dataset.id || null;
    const asset_id = tr.dataset.assetId || null;
    const name = tr.querySelector('.name').textContent.trim();
    const rate = +tr.querySelector('.rate').value;
    const qty  = +tr.querySelector('.qty').value;
    const sum  = rate * qty;
    rows.push({ id, asset_id, name, rate, qty, sum_total: sum });
  });

  $.ajax({
    url: window.ROUTES.offerAssetsUpdate,
    method: 'PATCH',
    headers: { 'X-CSRF-TOKEN': CSRF },
    data: { rows },
    success: () => (window.toast ? toast("Tools gespeichert") : alert("Tools gespeichert")),
    error: (xhr) => {
      console.error("Save tools error", xhr.responseText);
      (window.toast ? toast : alert)("Fehler beim Speichern der Tools", "error");
    }
  });
});

function addToolRow(tool = {}) {
  const tbody = document.getElementById('toolRows');
  const tr = document.createElement('tr');

  const qty  = Number(tool.qty || 1);
  const rate = Number(tool.rate || 0);
  const name = tool.name || (tool.asset ? `${tool.asset.item} ${tool.asset.model}` : '');
  const sum  = qty * rate;

  tr.dataset.assetId = tool.asset_id || '';
  tr.dataset.id = tool.id || ''; // keep DB id if exists

  tr.innerHTML = `
    <td class="name">${name}</td>
    <td class="text-right">
      <input type="number" class="rate w-20 text-right border rounded px-1"
             value="${rate}" step="0.01" />
    </td>
    <td class="text-center">
      <input type="number" class="qty w-16 text-center border rounded px-1"
             value="${qty}" min="1" />
    </td>
    <td class="text-right sum-total">€${sum.toFixed(2)}</td>
    <td><button class="btn btn-xs del">✕</button></td>
  `;

  // live recalc when qty or rate changes
  const rateInput = tr.querySelector('.rate');
  const qtyInput  = tr.querySelector('.qty');
  const sumEl     = tr.querySelector('.sum-total');

  const recalcRow = () => {
    const q = Number(qtyInput.value || 0);
    const r = Number(rateInput.value || 0);
    const s = q * r;
    sumEl.textContent = `€${s.toFixed(2)}`;
    recalcTools();
  };

  rateInput.addEventListener('input', recalcRow);
  qtyInput.addEventListener('input', recalcRow);

  // delete row
  tr.querySelector('.del').addEventListener('click', () => {
    tr.remove();
    recalcTools();
  });

  tbody.appendChild(tr);
  recalcTools();
}


function recalcTools() {
  let total = 0;
  document.querySelectorAll('#toolRows tr').forEach(tr => {
    const rate = Number(tr.querySelector('.rate')?.value || 0);
    const qty  = Number(tr.querySelector('.qty')?.value || 0);
    total += rate * qty;
  });
  document.getElementById('k_tools_ek_view').textContent = `€${total.toFixed(2)}`;
}




async function loadProducts() {
  try {
    const url = `/offers/${OFFER_ID}/folders/${FOLDER_ID}/products-list`;
    const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const items = await resp.json();

    const tbody = document.getElementById('productRows');
    if (!tbody) return;
    tbody.innerHTML = '';

    let total = 0;

    items.forEach(item => {
      const lineTotal = (item.quantity * item.unit_price) - (item.discount_abs || 0);
      total += lineTotal;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="p-1">
          <img src="/images/products/${item.image_name || 'placeholder.svg'}"
            class="w-12 h-12 object-cover rounded cursor-pointer"
            onclick="chooseImage(${item.id})">

        </td>
        <td>${item.name}</td>
        <td><input type="number" class="qty border w-16" 
                  value="${item.quantity}" data-id="${item.id}"></td>
        <td>
          <select class="measure border w-20" data-id="${item.id}">
            <option value="">–</option>
            <option value="Stück"   ${item.measure_unit==='Stück'?'selected':''}>Stück</option>
            <option value="m"       ${item.measure_unit==='m'?'selected':''}>m</option>
            <option value="cm"      ${item.measure_unit==='cm'?'selected':''}>cm</option>
            <option value="Package" ${item.measure_unit==='Package'?'selected':''}>Package</option>
            <option value="mm"      ${item.measure_unit==='mm'?'selected':''}>mm</option>
            <option value="Einzel"  ${item.measure_unit==='Einzel'?'selected':''}>Einzel</option>
          </select>
        </td>
        <td><input type="number" class="price border w-24" 
                  value="${item.unit_price}" step="0.01" data-id="${item.id}"></td>
        <td class="text-right line-total">€${lineTotal.toFixed(2)}</td>
        <td><button class="btn-xs del" data-id="${item.id}">✕</button></td>
      `;
      tbody.appendChild(tr);

      // helper: recalc this row + update footer
      const recalcRow = () => {
        const qty   = +tr.querySelector('.qty').value || 0;
        const price = +tr.querySelector('.price').value || 0;
        const line  = qty * price;
        tr.querySelector('.line-total').textContent = "€" + line.toFixed(2);
        recalcTotal();
      };

      // quantity change
      tr.querySelector('.qty').addEventListener('input', e => {
        const val = +e.target.value || 0;
        saveProductChange(item.id, { quantity: val });
        recalcRow();
      });

      // price change
      tr.querySelector('.price').addEventListener('input', e => {
        const val = +e.target.value || 0;
        saveProductChange(item.id, { unit_price: val });
        recalcRow();
      });

      // measure unit change
      tr.querySelector('.measure').addEventListener('change', e => {
        saveProductChange(item.id, { measure_unit: e.target.value });
      });
    });

    // footer total
    const recalcTotal = () => {
      let sum = 0;
      tbody.querySelectorAll('tr').forEach(tr => {
        const qty   = +tr.querySelector('.qty').value || 0;
        const price = +tr.querySelector('.price').value || 0;
        sum += qty * price;
      });
      document.getElementById('products_total').textContent = "€" + sum.toFixed(2);
      try { computeCalc(); } catch (_) {}
    };
    recalcTotal();

  } catch (err) {
    console.error("Failed to load products", err);
  }
}



async function saveProductChange(productId, changes) {
  try {
    const resp = await fetch(`/offers/${OFFER_ID}/folders/${FOLDER_ID}/products/${productId}`, {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(changes)
    });

    if (!resp.ok) throw new Error("Update failed");
    const data = await resp.json();
    console.log("Product saved:", data);
  } catch (err) {
    console.error("Error saving product change", err);
    alert("Fehler beim Speichern!");
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // when switching tabs
   document.getElementById('wizardSteps').addEventListener('click', (e) => {
      const pill = e.target.closest('.pill');
      if (!pill) return;
      const step = +pill.dataset.step;

      wizGo(step);

      if (step === 7) { // Products tab
        loadProducts();
      }
    });

});

async function updateProduct(id, data) {
  const resp = await fetch(`/offers/${OFFER_ID}/folders/${FOLDER_ID}/products/${id}`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': window.CSRF,
    },
    body: JSON.stringify(data),
  });
  if (!resp.ok) throw new Error(await resp.text());
  return resp.json();
}


async function chooseImage(productListId) {
  openGallery();

  const grid = document.getElementById('galleryGrid');
  grid.innerHTML = '<div class="col-span-4 text-center py-6">Lade Bilder…</div>';

  try {
    const resp = await fetch('/products/images', { headers: { 'Accept': 'application/json' }});
    if (!resp.ok) throw new Error('HTTP ' + resp.status);
    const images = await resp.json();

    grid.innerHTML = '';
    images.forEach(img => {
      const el = document.createElement('img');
      el.src = img.url; // absolute, e.g. asset('images/products/'.$img->image)
      el.className = "w-24 h-24 object-cover rounded cursor-pointer hover:scale-105 transition";
      el.title = (img.product || '') + ' ' + (img.model || '');
      el.addEventListener('click', async () => {
        await saveProductChange(productListId, { image_name: img.image });
        closeGallery();
        loadProducts();
      });
      grid.appendChild(el);
    });

    const searchInput = document.getElementById('gallerySearch');
    if (searchInput) {
      searchInput.value = '';
      searchInput.oninput = () => {
        const q = searchInput.value.toLowerCase();
        Array.from(grid.children).forEach(child => {
          child.style.display = child.title.toLowerCase().includes(q) ? '' : 'none';
        });
      };
    }
  } catch (err) {
    console.error('chooseImage failed', err);
    grid.innerHTML = '<div class="col-span-4 text-center py-6 text-red-600">Keine Bilder gefunden.</div>';
  }
}


async function selectImage(productId, imageName) {
  // update DB
  await saveProductChange(productId, { image_name: imageName });
  // refresh table
  loadProducts();
  // close modal
  document.querySelector('.fixed.inset-0')?.remove();
}

async function selectImage(productListId, imageName) {
  await updateProduct(productListId, { image_name: imageName });
  document.querySelector('.fixed').remove(); // close modal
  loadProducts();
}

function openGallery() {
  document.getElementById('productGallery').classList.remove('hidden');
}
function closeGallery() {
  document.getElementById('productGallery').classList.add('hidden');
}


async function loadGallery(q = '') {
  const resp = await fetch(`/products/gallery?q=${encodeURIComponent(q)}`);
  const data = await resp.json();

  const grid = document.getElementById('galleryGrid');
  grid.innerHTML = '';
  data.forEach(p => {
    const div = document.createElement('div');
    div.className = 'border rounded p-2 text-center cursor-pointer hover:bg-slate-50';
    div.innerHTML = `
      <img src="${p.image}" class="w-20 h-20 object-cover mx-auto mb-1 rounded">
      <div class="text-xs">${p.name}</div>
      <div class="text-xs text-slate-500">€${p.price}</div>
    `;
    div.onclick = () => selectProduct(p);
    grid.appendChild(div);
  });
}

document.getElementById('gallerySearch')?.addEventListener('input', e => {
  loadGallery(e.target.value);
});


function selectProduct(p) {
  console.log("Selected product", p);
  closeGallery();

  // Example: fill into inputs
  document.getElementById('selected_product_id').value = p.id;
  document.getElementById('selected_product_img').src = p.image;
}


/* -------- Select2 safe boot loader ---------- */
const Select2Boot = {
  ready: false,
  queue: [],
  // Ensure jQuery + Select2 exist; load if missing
  async ensure() {
    const hasJQ = !!window.jQuery;
    if (!hasJQ) {
      await this.loadScript('https://code.jquery.com/jquery-3.7.1.min.js');
    }
    if (!(window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.select2 === 'function')) {
      // load CSS first (idempotent)
      this.loadCSS('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
      await this.loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
    }
    this.ready = true;
    this.flush();
  },
  initOne(sel, selectedId = null) {
    if (!sel) return;
    if (!this.ready) {
      this.queue.push({ sel, selectedId });
      return;
    }
    const $ = window.jQuery;
    if (!$ || !$.fn || typeof $.fn.select2 !== 'function') {
      console.error('Select2 still not available; leaving native <select>.');
      return;
    }

    const $sel = $(sel);

    // If already initialized, destroy before re-init
    if ($sel.hasClass('select2-hidden-accessible')) {
      try { $sel.select2('destroy'); } catch (_) {}
    }

    $sel.select2({
      tags: true,
      width: '100%',
      placeholder: $sel.data('placeholder') || 'Position auswählen oder neu eingeben',
      allowClear: true
    });

    if (selectedId != null) {
      $sel.val(String(selectedId)).trigger('change');
    }
  },
  flush() {
    this.queue.forEach(({ sel, selectedId }) => this.initOne(sel, selectedId));
    this.queue.length = 0;
  },
  loadScript(src) {
    return new Promise((resolve, reject) => {
      // avoid duplicates
      if ([...document.scripts].some(s => s.src === src)) return resolve();
      const s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });
  },
  loadCSS(href) {
    if ([...document.styleSheets].some(ss => ss.href === href)) return;
    const l = document.createElement('link');
    l.rel = 'stylesheet';
    l.href = href;
    document.head.appendChild(l);
  }
};

 

document.getElementById('btnSaveEmployees')?.addEventListener('click', () => {
  const rows = [];
  document.querySelectorAll('#positionRows tr').forEach(tr => {
    const posSel = $(tr).find('.position-select');
    const position_id = posSel.val();
    const roleText    = posSel.find("option:selected").text();

    const rate = +tr.querySelector('.rate').value;
    const qty  = +tr.querySelector('.qty').value;
    const hpp  = +tr.querySelector('.hpp').value;
    const hoursTotal = qty * hpp;
    const sum = hoursTotal * rate;

    rows.push({
      position_id,
      role: roleText,
      rate,
      qty,
      hours_per_person: hpp,
      hours_total: hoursTotal,
      sum_total: sum,
      master_set_id: CURRENT_MASTER_SET_ID // if available
    });
  });

  $.ajax({
    url: `/offers/${OFFER_ID}/folders/${FOLDER_ID}/employees`,
    method: 'PATCH',
    headers: { 'X-CSRF-TOKEN': CSRF },
    data: { rows },
    success: () => toast("Mitarbeiterliste gespeichert"),
    error: (xhr) => {
      console.error("Employee save error", xhr.responseText);
      toast("Fehler beim Speichern der Mitarbeiterliste", "error");
    }
  });
});


// preload all positions
let POSITIONS = [];

async function loadPositions() {
  try {
    const resp = await fetch('/positions/list');
    POSITIONS = await resp.json();
  } catch (e) {
    console.error("Failed to load positions", e);
  }
}

// init select2 with tags + AJAX create
function initPositionSelect(sel) {
  $(sel).select2({
    tags: true,
    width: '100%',
    placeholder: 'Position auswählen oder neu eingeben',
  }).on('select2:select', async function (e) {
    const data = e.params.data;
    if (data.id === data.text) {
      // new tag → create position in backend
      try {
        const resp = await fetch('/positions', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
          },
          body: JSON.stringify({ position: data.text })
        });
        const pos = await resp.json();
        // replace the option with the saved one
        const option = new Option(pos.position, pos.id, true, true);
        $(this).append(option).trigger('change');
      } catch (err) {
        console.error("Failed to create position", err);
        toast("Fehler beim Erstellen der neuen Position", "error");
      }
    }
  });
}


function syncAllToServer() {
  const items = collectProductRows();      // same as before
  const employees = collectEmployeeRows(); // new function for #positionRows

  $.ajax({
    url: `/offers/${OFFER_ID}/folders/${FOLDER_ID}/details`,
    method: 'PATCH',
    headers: { 'X-CSRF-TOKEN': CSRF },
    data: { items, employees },
    success: () => toast("Alles gespeichert", "ok"),
    error: (xhr) => {
      console.error("Save error", xhr.responseText);
      toast("Fehler beim Speichern", "error");
    }
  });
}

function collectEmployeeRows() {
  const rows = [];
  document.querySelectorAll('#positionRows tr').forEach(tr => {
    const posSel = $(tr).find('.position-select');
    const position_id = posSel.val();
    const roleText = posSel.find("option:selected").text();

    const rate = +tr.querySelector('.rate').value;
    const qty  = +tr.querySelector('.qty').value;
    const hpp  = +tr.querySelector('.hpp').value;
    const hoursTotal = qty * hpp;
    const sum = hoursTotal * rate;

    rows.push({
      position_id: position_id || null,
      role: roleText,
      rate,
      qty,
      hours_per_person: hpp,
      hours_total: hoursTotal,
      sum_total: sum,
      master_set_id: CURRENT_MASTER_SET_ID
    });
  });
  return rows;
}


async function initRoleSelect() {
  const $role = $('#pos_role');

  // Load positions from backend
  try {
    const resp = await fetch('/positions/list');
    POSITIONS = await resp.json();
  } catch (err) {
    console.error("Failed to load positions", err);
    POSITIONS = [];
  }

  // Populate options
  $role.empty().append('<option></option>');
  POSITIONS.forEach(p => {
    $role.append(new Option(p.position, p.id));
  });

  // Initialize Select2 with tagging
  $role.select2({
    tags: true,
    width: '100%',
    placeholder: 'Position suchen oder neu eingeben',
    allowClear: true
  });

  // Intercept when a new tag is added
  $role.on('select2:select', async function (e) {
    const data = e.params.data;
    if (data.id === data.text) {
      // new entry → create in backend
      try {
        const resp = await fetch('/positions', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
          },
          body: JSON.stringify({ position: data.text })
        });
        const pos = await resp.json();
        // Replace option with new DB-backed ID
        const option = new Option(pos.position, pos.id, true, true);
        $role.append(option).trigger('change');
        POSITIONS.push(pos); // keep cache updated
        toast(`Neue Position "${pos.position}" gespeichert`);
      } catch (err) {
        console.error("Failed to create position", err);
        toast("Fehler beim Erstellen der neuen Position", "error");
      }
    }
  });
}


document.getElementById('btnAddPos').addEventListener('click', (e) => {
  e.preventDefault();

  const $role = $('#pos_role');
  const position_id = $role.val();
  const roleText    = $role.find("option:selected").text();
  const rate        = +document.getElementById('pos_rate').value || 0;
  const qty         = +document.getElementById('pos_qty').value || 1;
  const hpp         = +document.getElementById('pos_hpp').value || 0;

  addEmployeeRow({
    position_id,
    role: roleText,
    rate,
    qty,
    hours_per_person: hpp
  });

  // Reset input fields
  $role.val(null).trigger('change');
  document.getElementById('pos_rate').value = '';
  document.getElementById('pos_qty').value  = 1;
  document.getElementById('pos_hpp').value  = 8;
});


document.addEventListener('DOMContentLoaded', () => {
  initRoleSelect();
});

/* New row markup: Position | Menge (+/–) | Einzel € | Zeile Netto € | Actions */
function addOfferRow({
  sku = '',
  name = '',
  qty = 1,
  unit = 0,
  cost = 0,
  type = null,
  master_set_id = null
} = {}) {
  const tbody = bodyEl();
  if (!tbody) return;

  const tr = document.createElement('tr');
  tr.dataset.masterSetId = master_set_id || '';
  tr.dataset.type = type || '';

  tr.innerHTML = `
    <td class="align-top p-1">
      <div class="font-medium">${name || 'Eigene Position'}</div>
      <div class="text-xs text-slate-500">${sku ? ('SKU ' + sku) : ''}</div>
      <textarea class="notes w-full border rounded px-2 py-1 mt-1 text-sm" placeholder="Notizen (optional)"></textarea>
    </td>

    <td class="text-center p-1">
      <div class="inline-flex items-center gap-1">
        <button class="btn btn-xs qty-minus" title="−"><i data-feather="minus"></i></button>
        <input type="number" class="qty w-16 border rounded px-2 py-1 text-sm text-center" value="${qty}">
        <button class="btn btn-xs qty-plus" title="+"><i data-feather="plus"></i></button>
      </div>
    </td>

    <td class="text-center p-1">
      <input type="number" class="unit w-28 border rounded px-2 py-1 text-right" value="${unit}">
    </td>

    <td class="text-right align-top p-1">
      <div class="line font-semibold">€0,00</div>
      <div class="text-xs text-slate-500">Kosten: <span class="cost">${(Number(cost) || 0).toFixed(2)}</span></div>
    </td>

    <td class="text-right align-top p-1">
      <div class="flex gap-1 justify-end">
        <button class="btn insert-after" title="Neue Position darunter"><i data-feather="plus-square"></i></button>
        <button class="btn text-red-600 del" title="Entfernen"><i data-feather="trash-2"></i></button>
      </div>
    </td>
  `;

  // Wire events
  tr.querySelector('.qty-plus').addEventListener('click', () => {
    const i = tr.querySelector('.qty');
    i.value = (n(i.value) + 1);
    recalcAll();
  });

  tr.querySelector('.qty-minus').addEventListener('click', () => {
    const i = tr.querySelector('.qty');
    i.value = Math.max(0, n(i.value) - 1);
    recalcAll();
  });

  tr.querySelector('.qty').addEventListener('input', recalcAll);
  tr.querySelector('.unit').addEventListener('input', recalcAll);

  tr.querySelector('.insert-after').addEventListener('click', () => {
    tr.insertAdjacentElement('afterend', blankOfferRow());
    recalcAll();
  });

  tr.querySelector('.del').addEventListener('click', () => {
    tr.remove();
    recalcAll();
  });

  tbody.appendChild(tr);
  recalcAll();
  if (typeof feather !== 'undefined') feather.replace();
}

/* Helper to insert a blank row quickly */
function blankOfferRow() {
  return (function () {
    const tmp = document.createElement('tbody');
    addOfferRow({ name: 'Neue Position', qty: 1, unit: 0, cost: 0 });
    // last appended row is at the end
    return bodyEl().lastElementChild;
  })();
}

/* Totals (no per-line discounts; global controls still respected if present) */
 function recalcAll() {
  const tbody = bodyEl(); 
  if (!tbody) return;

  let itemsSubtotal = 0;
  let itemsCost = 0;

  // new category sums
  let matSum = 0;
  let lohnSum = 0;
  let toolsSum = 0;

  tbody.querySelectorAll('tr').forEach(tr => {
    const qty  = n(tr.querySelector('.qty')?.value);
    const unit = n(tr.querySelector('.unit')?.value);
    const cost = n(tr.querySelector('.cost')?.textContent);
    const line = qty * unit;

    // Update line UI
    const lineEl = tr.querySelector('.line');
    if (lineEl) lineEl.textContent = money(line);

    itemsSubtotal += line;
    itemsCost     += qty * cost;

    // classify by type (stored in dataset)
    const type = tr.dataset.type || '';
    if (type === 'Material') matSum += line;
    if (type === 'Lohn')     lohnSum += line;
    if (type === 'Tools')    toolsSum += line;
  });

  // Global inputs
  const gdEl  = document.getElementById('global_discount');
  const ship  = n(document.getElementById('shipping')?.value);
  const gDisc = gdEl ? n(gdEl.value) / 100 : 0;
  const applyGlobToShip = !!document.getElementById('apply_global_to_shipping')?.checked;

  const factorItems = (1 - gDisc);
  const factorShip  = applyGlobToShip ? factorItems : 1;

  const itemsAfter = itemsSubtotal * factorItems;
  const shipAfter  = ship * factorShip;
  const afterAll   = itemsAfter + shipAfter;

  const vatSum = 0; // VAT disabled
  const gross  = afterAll;
  const margin = (itemsAfter - itemsCost);

  // Update summary UI
  const setTxt = (id, val) => { 
    const el = document.getElementById(id); 
    if (el) el.textContent = val; 
  };
  setTxt('sum_subtotal', money(itemsSubtotal + ship));
  setTxt('sum_after',    money(afterAll));
  setTxt('sum_vat',      money(vatSum));
  setTxt('sum_gross',    money(gross));
  setTxt('sum_margin',   itemsSubtotal > 0 ? money(margin) : '€—');

  // Update Kalkulation inputs
  const matEl   = document.getElementById('k_mat_ek');
  const lohnEl  = document.getElementById('k_lohn_ek_view');
  const toolsEl = document.getElementById('k_tools_ek_view');

  if (matEl)  matEl.value = matSum.toFixed(2);
  if (lohnEl) lohnEl.textContent = money(lohnSum);
  if (toolsEl) toolsEl.textContent = money(toolsSum);
}


// Init global inputs recalc listeners
['global_discount', 'shipping', 'apply_global_to_shipping'].forEach(id => {
  document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', recalcAll);
      el.addEventListener('change', recalcAll);
    }
  });
});

/* Recalc when globals change (if they exist) */
['global_discount', 'shipping', 'apply_global_to_shipping'].forEach(id => {
  document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById(id); if (!el) return;
    el.addEventListener('input', recalcAll);
    el.addEventListener('change', recalcAll);
  });
});

 

async function loadMasterSetDetails(masterSetId) {
  const resp = await fetch(`/master-sets/${masterSetId}/details`);
  const data = await resp.json();

  console.log("MasterSet", data);

  // Example: render totals
  document.querySelector('#totals').innerHTML = `
    <div>Material: €${data.totals.material}</div>
    <div>Lohn: €${data.totals.lohn}</div>
    <div>Tools: €${data.totals.tools}</div>
  `;

  // Example: render positions
  const posEl = document.querySelector('#positions');
  posEl.innerHTML = data.positions.map(p => `
    <div>${p.position} – ${p.hours}h – €${p.total}</div>
  `).join('');

  // Example: render assets
  const assetEl = document.querySelector('#assets');
  assetEl.innerHTML = data.assets.map(a => `
    <div>${a.name} (${a.model}) × ${a.count} → €${a.price}</div>
  `).join('');
}


/* ------------------------- Kalkulation EK→GK→VK ------------------------ */
function computeCalc() {
  const matEK = n(document.getElementById('k_mat_ek').value) + n(document.getElementById('k_klein_ek').value) + n(document.getElementById('k_transport').value);
  const hM = n(document.getElementById('k_h_monteur').value), hH = n(document.getElementById('k_h_helfer').value), hA = n(document.getElementById('k_h_azubi').value), hV = n(document.getElementById('k_h_vk').value);
  const rM = n(document.getElementById('k_rate_monteur').value), rH = n(document.getElementById('k_rate_helfer').value), rA = n(document.getElementById('k_rate_azubi').value), rV = n(document.getElementById('k_rate_vk').value);
  const lohnEK = hM * rM + hH * rH + hA * rA + hV * rV;
  const gesamtEK = matEK + lohnEK;
  const pVertrieb = n(document.getElementById('k_gk_vertrieb').value) / 100;
  const pBuV = n(document.getElementById('k_gk_buv').value) / 100;
  const pWagnis = n(document.getElementById('k_gk_wagnis').value) / 100;
  const gkVertrieb = gesamtEK * pVertrieb;
  const gkBuV = gesamtEK * pBuV;
  const gkWagnis = gesamtEK * pWagnis;
  const gkSum = gkVertrieb + gkBuV + gkWagnis;
  const gkMat = gesamtEK > 0 ? gkSum * (matEK / gesamtEK) : 0;
  const gkLohn = gkSum - gkMat;
  const zMat = n(document.getElementById('k_z_mat').value) / 100;
  const zLohn = n(document.getElementById('k_z_lohn').value) / 100;
  const margeMat = matEK * zMat; const margeLohn = lohnEK * zLohn; const margeGes = margeMat + margeLohn;
  const vkMatOhneGK = matEK + margeMat; const vkLohnOhneGK = lohnEK + margeLohn;
  const vkMat = vkMatOhneGK + gkMat; const vkLohn = vkLohnOhneGK + gkLohn; const vkGes = vkMat + vkLohn;
  const gesStunden = hM + hH + hA + hV; const umsatzProStd = gesStunden > 0 ? (vkGes / gesStunden) : 0; const margeProStd = gesStunden > 0 ? (margeGes / gesStunden) : 0;
  const out = document.getElementById('calcOut');
  out.innerHTML = `
    <div class="grid grid-cols-2 gap-x-6 gap-y-1">
      <div class="text-slate-500">Material-EK</div><div class="text-right">${money(matEK)}</div>
      <div class="text-slate-500">Lohn-EK</div><div class="text-right">${money(lohnEK)}</div>
      <div class="text-slate-500">Gesamtkosten-EK</div><div class="text-right">${money(gesamtEK)}</div>
      <div class="col-span-2 border-t my-1"></div>
      <div class="text-slate-500">GK Vertrieb ( ${pctTxt(pVertrieb * 100)} von EK )</div><div class="text-right">${money(gkVertrieb)}</div>
      <div class="text-slate-500">GK Büro/Vw ( ${pctTxt(pBuV * 100)} )</div><div class="text-right">${money(gkBuV)}</div>
      <div class="text-slate-500">GK Wagnis ( ${pctTxt(pWagnis * 100)} )</div><div class="text-right">${money(gkWagnis)}</div>
      <div class="text-slate-700 font-medium">Gemeinkosten gesamt</div><div class="text-right font-medium">${money(gkSum)}</div>
      <div class="col-span-2 border-t my-1"></div>
      <div class="text-slate-500">Verteilung GK → Material</div><div class="text-right">${money(gkMat)}</div>
      <div class="text-slate-500">Verteilung GK → Lohn</div><div class="text-right">${money(gkLohn)}</div>
      <div class="col-span-2 border-t my-1"></div>
      <div class="text-slate-500">Material-Zuschlag ( ${pctTxt(zMat * 100)} )</div><div class="text-right">${money(margeMat)}</div>
      <div class="text-slate-500">Lohn-Zuschlag ( ${pctTxt(zLohn * 100)} )</div><div class="text-right">${money(margeLohn)}</div>
      <div class="text-slate-700">Marge gesamt</div><div class="text-right">${money(margeGes)}</div>
      <div class="col-span-2 border-t my-1"></div>
      <div class="text-slate-500">VK Material inkl. GK</div><div class="text-right">${money(vkMat)}</div>
      <div class="text-slate-500">VK Lohn inkl. GK</div><div class="text-right">${money(vkLohn)}</div>
      <div class="text-slate-900 font-semibold">Verkaufspreis gesamt</div><div class="text-right font-semibold">${money(vkGes)}</div>
      <div class="col-span-2 border-t my-1"></div>
      <div class="text-slate-500">Gesamtstunden</div><div class="text-right">${gesStunden}</div>
      <div class="text-slate-500">Umsatz pro Stunde</div><div class="text-right">${money(umsatzProStd)}</div>
      <div class="text-slate-500">Marge pro Stunde</div><div class="text-right">${money(margeProStd)}</div>
      <div class="text-slate-500">Marge gesamt %</div><div class="text-right">${(vkGes ? (margeGes / vkGes * 100).toFixed(2).replace('.', ',') : '0,00')}%</div>
    </div>`;
  return { matEK, lohnEK, gesamtEK, gkVertrieb, gkBuV, gkWagnis, gkSum, gkMat, gkLohn, margeMat, margeLohn, margeGes, vkMat, vkLohn, vkGes, gesStunden, umsatzProStd, margeProStd };
}

/* ------------------------- Save / Load / Export ------------------------ */
const LS_KEY = 'hp_offer_entwurf_v5_combined';
function sammleAntworten() {
  const get = id => document.getElementById(id)?.value;
  return {
    objekt: { typ: get('property_type'), plz: get('zip'), baujahr: get('year_built'), daemmung: get('insulation'), flaech_m2: get('area_m2'), decke_m: get('ceiling_m'), bewohner: get('occupants') },
    bestand: { quelle: get('current_source'), heizflaechen: get('emitters'), min_vorlauf: get('lowest_flow_c'), speicher_l: get('dhw_cyl') },
    technik: {
      verbrauch_kwh: get('annual_kwh'), alter: get('age_years'), personen: get('persons'), ww_pp: get('dhw_per_person'),
      vl: get('vl'), rl: get('rl'), verteilung: get('distribution'), nat: get('nat'), biv: get('biv'), ti: get('ti'),
      wp_share: get('wp_share'), flh: get('flh'), peak: get('peak_factor'), dist_mult: get('dist_mult'), vl_mult: get('vl_mult')
    }
  };
}
function sammleAngebot() {
  const items = [];
  bodyEl()?.querySelectorAll('tr').forEach(tr => {
    items.push({
      name: tr.querySelector('div.font-medium').textContent.trim(),
      notiz: tr.querySelector('textarea').value,
      sku: tr.querySelector('.text-slate-500').textContent.replace('SKU ', '').trim(),
      menge: n(tr.querySelector('.qty').value),
      einzel: n(tr.querySelector('.unit').value),
      rabatt: n(tr.querySelector('.disc').value),
      aufschlag: 0, mwst: 0,
      zeile: tr.querySelector('.line').textContent
    });
  });
  return {
    positionen: items,
    global: { rabatt: n(document.getElementById('global_discount').value), aufschlag: 0, versand: n(document.getElementById('shipping').value), versand_mwst: 0, glob_auf_versand: document.getElementById('apply_global_to_shipping').checked },
    summen: { netto: document.getElementById('sum_subtotal').textContent, nach_aj: document.getElementById('sum_after').textContent, mwst: document.getElementById('sum_vat').textContent, brutto: document.getElementById('sum_gross').textContent, marge: document.getElementById('sum_margin').textContent }
  };
}
function speichereEntwurf() { const data = { antworten: sammleAntworten(), angebot: sammleAngebot(), technisch: globalDerived }; localStorage.setItem(LS_KEY, JSON.stringify(data)); alert('Entwurf lokal gespeichert.'); }
function ladeEntwurf() {
  const raw = localStorage.getItem(LS_KEY); if (!raw) { alert('Kein Entwurf gefunden.'); return; }
  const data = JSON.parse(raw);
  function set(id, val) { if (document.getElementById(id)) document.getElementById(id).value = val ?? ''; }
  const a = data.antworten || {};
  Object.entries(a.objekt || {}).forEach(([k, v]) => set(k === 'typ' ? 'property_type' : k === 'plz' ? 'zip' : k === 'flaech_m2' ? 'area_m2' : k === 'decke_m' ? 'ceiling_m' : k, v));
  Object.entries(a.bestand || {}).forEach(([k, v]) => set(k === 'quelle' ? 'current_source' : k === 'heizflaechen' ? 'emitters' : k === 'min_vorlauf' ? 'lowest_flow_c' : k === 'speicher_l' ? 'dhw_cyl' : k, v));
  if (a.technik) {
    set('annual_kwh', a.technik.verbrauch_kwh); set('age_years', a.technik.alter); set('persons', a.technik.personen); set('dhw_per_person', a.technik.ww_pp);
    set('vl', a.technik.vl); set('rl', a.technik.rl); set('distribution', a.technik.verteilung); set('nat', a.technik.nat); set('biv', a.technik.biv); set('ti', a.technik.ti);
    set('wp_share', a.technik.wp_share); updateSliderLabel('wp_share'); set('flh', a.technik.flh); set('peak_factor', a.technik.peak); set('dist_mult', a.technik.dist_mult); set('vl_mult', a.technik.vl_mult);
  }
  const tbody = bodyEl(); if (tbody) {
    tbody.innerHTML = '';
    (data.angebot?.positionen || []).forEach(it => { addOfferRow({ sku: it.sku, name: it.name, qty: it.menge, unit: it.einzel, disc: it.rabatt, mark: 0, vat: 0, cost: 0 }); tbody.lastElementChild.querySelector('textarea').value = it.notiz || ''; });
  }
  document.getElementById('global_discount').value = data.angebot?.global?.rabatt || 0;
  document.getElementById('shipping').value = data.angebot?.global?.versand || 0;
  document.getElementById('apply_global_to_shipping').checked = !!data.angebot?.global?.glob_auf_versand;
  recalcAll(); calcTech(); berechneEmpfehlungen();
}
function exportJSON() {
  calcTech();
  const data = { erstellt_am: new Date().toISOString(), antworten: sammleAntworten(), technisch: globalDerived, angebot: sammleAngebot() };
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = 'waermepumpe-gesamt-konfigurator.json'; a.click(); URL.revokeObjectURL(url);
}

/* ---------------------------- Print Preview --------------------------- */
function buildPrintPreview() {
  document.getElementById('ppDate').textContent = new Date().toLocaleDateString('de-DE');
  const ans = sammleAntworten();
  const r = calcTech() || globalDerived?.derived;
  document.getElementById('ppCustomer').innerHTML = `PLZ/Ort: ${ans.objekt.plz || '—'}<br/>Gebäudetyp: ${ans.objekt.typ || '—'}<br/>Baujahr: ${ans.objekt.baujahr || '—'}`;
  document.getElementById('ppProject').innerHTML = `Auslegung AT: ${ans.technik.nat || '—'} °C · Innen: ${ans.technik.ti || '—'} °C<br/>Heizlast: ${r ? round(r.q_nat, 2) : '—'} kW · Empfehlung: ${r ? r.hp_reco_kw : '—'} kW`;
  document.getElementById('ppBereinigt').textContent = r ? Math.round(r.actual_total).toLocaleString() : '—';
  document.getElementById('ppHeizenergie').textContent = r ? Math.round(r.heating_kwh).toLocaleString() : '—';
  document.getElementById('ppJAZ').textContent = r ? r.jaz_used.toFixed(1) : '—';
  document.getElementById('ppStrom').textContent = r ? `${Math.round(r.elec_wp).toLocaleString()} / ${Math.round(r.elec_estab).toLocaleString()} kWh/a` : '—';

  const tbody = document.getElementById('ppItems'); tbody.innerHTML = '';
  bodyEl()?.querySelectorAll('tr').forEach(tr => {
    const name = tr.querySelector('div.font-medium').textContent.trim();
    const qty = n(tr.querySelector('.qty').value);
    const unit = n(tr.querySelector('.unit').value);
    const lineTxt = tr.querySelector('.line').textContent;
    const trp = document.createElement('tr');
    trp.innerHTML = `<td class="p-2 border-b">${name}</td><td class="p-2 border-b text-right">${qty}</td><td class="p-2 border-b text-right">${money(unit)}</td><td class="p-2 border-b text-right">${lineTxt}</td>`;
    tbody.appendChild(trp);
  });
  document.getElementById('ppSubtotal').textContent = document.getElementById('sum_subtotal').textContent;
  document.getElementById('ppVat').textContent = document.getElementById('sum_vat').textContent;
  document.getElementById('ppGross').textContent = document.getElementById('sum_gross').textContent;
}

/* ------------------------- Summary & JSON (Tech) ---------------------- */
function copySummary() {
  calcTech();
  const rIn = globalDerived.inputs, r = globalDerived.derived;
  const lines = [
    'Wärmepumpen-Auslegung — Kurzbericht',
    '-----------------------------------',
    `Verbrauch brutto:           ${rIn.annual_kwh} kWh/a`,
    `Altersverlust:              ${(r.loss * 100).toFixed(0)} %`,
    `Bereinigt:                  ${Math.round(r.actual_total)} kWh/a`,
    `WW:                         ${rIn.persons}×${rIn.dhw_per_person} = ${r.dhw_total} kWh/a`,
    ``,
    `FLH:                        ${rIn.flh} h/a`,
    `Heizlast @NAT:              ${round(r.q_nat, 2)} kW (Peak ${rIn.peak_factor})`,
    `NAT / Biv / Innen:          ${rIn.nat}°C / ${rIn.biv}°C / ${rIn.ti}°C`,
    `Lastanteil bei Biv:         ${Math.round(r.f_biv * 100)} %`,
    `WP-Anteil:                  ${Math.round(rIn.wp_share_pct)} %`,
    ``,
    `JAZ/COP (roh):              ${r.jaz_raw.toFixed(1)}`,
    `JAZ/COP (benutzt ≥3):       ${r.jaz_used.toFixed(1)}`,
    `Strom WP (Heizen):          ${Math.round(r.elec_wp)} kWh/a`,
    `Strom E-Stab (Heizen):      ${Math.round(r.elec_estab)} kWh/a`,
    `Gesamt Strom (Heizen):      ${Math.round(r.elec_total)} kWh/a`,
    ``,
    `WP-Größe (berechnet):       ${r.hp_size_kw} kW`,
    `WP-Größe (Vorschlag):       ${r.hp_reco_kw} kW`
  ];
  navigator.clipboard.writeText(lines.join('\n')).then(() => alert('Zusammenfassung kopiert.'));
}
function downloadJSON() {
  calcTech();
  const result = globalDerived;
  const blob = new Blob([JSON.stringify(result, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = 'wp-auslegung.json'; a.click();
  URL.revokeObjectURL(url);
}

/* ------------------------------- Tests -------------------------------- */
function runTests() {
  const out = document.getElementById('testResults'); if (!out) return; out.innerHTML = '';
  const tests = [];
  try { tests.push({ name: 'Katalogschlüssel mit Bindestrich', pass: KATALOG[0]["capacity_kw_A-7_W35"] === 7.2 }); } catch (e) { tests.push({ name: 'Katalogschlüssel mit Bindestrich', pass: false, error: e }); }
  const prevVl = document.getElementById('vl').value; document.getElementById('vl').value = 65; calcTech();
  const used = globalDerived?.derived?.jaz_used || 0; tests.push({ name: 'COP clamp ≥ 3.0', pass: used >= 3.0 });
  document.getElementById('vl').value = prevVl; calcTech();
  const qty = 2, unit = 100, disc = 10; const expected = 180; const got = (qty * unit) * (1 - disc / 100);
  tests.push({ name: 'Zeilenpreis ohne MwSt', pass: Math.abs(got - expected) < 1e-6 });
  recalcAll(); tests.push({ name: 'MwSt Gesamt ist 0', pass: /€0,00/.test(document.getElementById('sum_vat').textContent) });
  berechneEmpfehlungen();
  tests.push({ name: 'Empfehlungen generiert', pass: (document.getElementById('recs')?.children.length || 0) >= 1 });

  tests.forEach(t => { const li = document.createElement('li'); li.innerHTML = `<span class="${t.pass ? 'text-emerald-700' : 'text-red-700'}">${t.pass ? '✔' : '✖'} ${t.name}</span>` + (t.error ? `<pre class="text-xs">${String(t.error)}</pre>` : ''); out.appendChild(li); });
  console.table(tests.map(t => ({ name: t.name, pass: t.pass })));
}

/* ------------------------------ Bootstrapping ------------------------- */
document.addEventListener('DOMContentLoaded', async () => {
  /* -------------------- Wizard Init -------------------- */
  steps     = Array.from(document.querySelectorAll('.pill'));
  stepViews = Array.from(document.querySelectorAll('.wizstep'));
  bar       = document.getElementById('wizardProgress');

  // step navigation
  document.getElementById('wizardSteps')?.addEventListener('click', (e) => {
    const pill = e.target.closest('.pill');
    if (pill) wizGo(+pill.getAttribute('data-step'));
  });

  updateBar(0);
  updateSliderLabel('wp_share');
  if (typeof feather !== 'undefined') feather.replace();

  /* -------------------- Load employees + positions -------------------- */
   await Select2Boot.ensure();
  
 
  await loadAssetsDropdown();

  /* -------------------- Load assets (tools/geräte) -------------------- */
  if (typeof loadAssetList === 'function') {
    await loadAssetList(); 
      
  }

  await loadPositions();       // fills POSITIONS for dropdown
  
  await loadEmployeeList();
 

  /* -------------------- Catalog & Products -------------------- */
  renderCatalog(KATALOG);
  prefillFromBootstrap();

  // render saved product list
  if (Array.isArray(window.BOOTSTRAP?.productList)) {
    const tbody = bodyEl();
    if (tbody) {
      tbody.innerHTML = '';
      window.BOOTSTRAP.productList.forEach(item => {
        addOfferRow({
          sku: item.sku,
          name: item.name,
          qty: item.quantity,
          unit: item.unit_price,
          cost: item.cost,
          type: item.type,
          master_set_id: item.master_set_id
        });
      });
      recalcAll();
    }
  }

  recalcAll();
  calcTech();
  berechneEmpfehlungen();

  /* -------------------- Catalog Search -------------------- */
  document.getElementById('catalogSearch')?.addEventListener('input', (e) => {
    const q = (e.target.value || '').toLowerCase();
    const list = KATALOG.filter(p =>
      (p.name || '').toLowerCase().includes(q) ||
      (p.sku || '').toLowerCase().includes(q) ||
      (p.marke || '').toLowerCase().includes(q)
    );
    renderCatalog(list);
  });

  /* -------------------- Buttons -------------------- */
  document.getElementById('btnCalc')?.addEventListener('click', computeCalc);

  document.getElementById('btnApplyToOffer')?.addEventListener('click', () => {
    const r = computeCalc();
    addOfferRow({
      sku: 'CALC-MAT',
      name: 'Material (Kalkulation)',
      qty: 1, unit: r.vkMat, cost: r.matEK, type: 'Material'
    });
    addOfferRow({
      sku: 'CALC-LOHN',
      name: 'Lohn (Kalkulation)',
      qty: 1, unit: r.vkLohn, cost: r.lohnEK, type: 'Lohn'
    });
    recalcAll();
    wizGo(7);
  });

  document.getElementById('btnSave')?.addEventListener('click', speichereEntwurf);
  document.getElementById('btnLoad')?.addEventListener('click', ladeEntwurf);
  document.getElementById('btnExport')?.addEventListener('click', exportJSON);

  /* -------------------- Print preview -------------------- */
  const previewBtn   = document.getElementById('togglePreview');
  const previewClose = document.getElementById('closePreview');
  const previewPanel = document.getElementById('printPanel');
  const printNow     = document.getElementById('printNow');

  previewBtn?.addEventListener('click', () => {
    buildPrintPreview();
    previewPanel.classList.toggle('hidden');
    if (typeof feather !== 'undefined') feather.replace();
  });
  previewClose?.addEventListener('click', () => previewPanel.classList.add('hidden'));
  printNow?.addEventListener('click', () => { buildPrintPreview(); window.print(); });

  /* -------------------- Tests Mirror -------------------- */
  document.getElementById('btnRunTestsTop')?.addEventListener('click', () => {
    document.getElementById('btnRunTests')?.click();
  });
  document.getElementById('btnRunTests')?.addEventListener('click', runTests);

  /* -------------------- Autofill -------------------- */
  document.getElementById('btnAutofill')?.addEventListener('click', () => {
    prefillFromBootstrap();
    recalcAll();
    calcTech();
    berechneEmpfehlungen();
    alert('Daten aus Angebot/Kunde automatisch übernommen.');
  });

  /* -------------------- Default step -------------------- */
  wizGo(0);
});


function offerBodyEl() {
  return document.getElementById('offerBody');
}

function sumEKFromOfferBody() {
  const tbody = offerBodyEl(); 
  if (!tbody) return { mat: 0, tools: 0 };

  let mat = 0, tools = 0;

  tbody.querySelectorAll('tr').forEach(tr => {
    const name = tr.querySelector('div.font-medium')?.textContent || '';
    const skuTxt = (tr.querySelector('.text-slate-500')?.textContent || '').replace('SKU','').trim().toUpperCase();
    const qty  = n(tr.querySelector('.qty')?.value);
    const cost = n(tr.querySelector('.cost')?.textContent);
    const lineEK = qty * cost;

    const isWork  = skuTxt === 'WORK' || /Arbeitszeit/i.test(name);
    const isTools = skuTxt === 'TOOLS' || /Werkzeug|Geräte/i.test(name);

    if (isWork) return;           // exclude labor from material EK
    if (isTools) tools += lineEK; // tools tracked separately
    else         mat   += lineEK; // pure material EK
  });

  return { mat, tools };
}

/* Put EK from offer rows into Step-G + load employees from breakdown */
function syncCalcFromOfferAndEmployees(bd) {
  const includeTools = !!document.getElementById('include_tools')?.checked;
  const sums = sumEKFromOfferBody();
  const matEK = sums.mat + (includeTools ? sums.tools : 0);

  const setVal = (id, v) => { const el = document.getElementById(id); if (el) el.value = Math.round(v); };
  setVal('k_mat_ek',    matEK);
  setVal('k_klein_ek',  0);
  setVal('k_transport', 300);

  // Fill Step-6 positions from breakdown employees (buy rates & hours)
  const posTbodyEl = document.getElementById('positionRows');
  if (posTbodyEl && bd && typeof window.posRowTemplate === 'function') {
    posTbodyEl.innerHTML = '';
    const emps = bd.lines?.employees || bd.employees || [];
    emps.forEach(e => {
      posTbodyEl.appendChild(window.posRowTemplate({
        role: e.position || 'Mitarbeiter',
        rate: n(e.rate_buy || e.buying_price || 0),
        qty:  1,
        hpp:  n(e.hours || e.work_hour || 0)
      }));
    });
    if (typeof window.recalcPositions === 'function') window.recalcPositions();
  }

  if (typeof window.computeCalc === 'function') window.computeCalc();
}


/* ------------------------ Expose to window (HTML hooks) --------------- */
window.wizGo = wizGo;
window.wizNext = wizNext;
window.wizPrev = wizPrev;
window.calcTech = calcTech;
window.berechneEmpfehlungen = berechneEmpfehlungen;
window.copySummary = copySummary;
window.downloadJSON = downloadJSON;
window.updateSliderLabel = updateSliderLabel;
window.buildPrintPreview = buildPrintPreview;
window.computeCalc = computeCalc;

</script> 


<script>
/* =======================
   STEP 6 Enhancements
   ======================= */

const fmtEUR = (x) => '€' + (Number(x||0).toFixed(2).replace('.', ','));
const byId = (id) => document.getElementById(id);
const posTbody = () => byId('positionRows');

let loadedSet = null; // holds last loaded set breakdown

function posRowTemplate({ role = '', rate = 0, qty = 1, hpp = 8 } = {}) {
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>
      <input class="pos-role w-full border rounded px-2 py-1 text-sm" value="${role}" placeholder="Rolle">
    </td>
    <td class="text-right">
      <input type="number" class="pos-rate w-24 border rounded px-2 py-1 text-sm text-right" value="${rate}">
    </td>
    <td class="text-center">
      <div class="inline-flex items-center gap-1">
        <button class="btn btn-xs pos-qty-minus" title="−"><i data-feather="minus"></i></button>
        <input type="number" class="pos-qty w-16 border rounded px-2 py-1 text-sm text-center" value="${qty}">
        <button class="btn btn-xs pos-qty-plus" title="+"><i data-feather="plus"></i></button>
      </div>
    </td>
    <td class="text-center">
      <input type="number" class="pos-hpp w-24 border rounded px-2 py-1 text-sm text-right" value="${hpp}">
    </td>
    <td class="text-right">
      <div class="pos-hours font-medium">0</div>
    </td>
    <td class="text-right">
      <div class="pos-sum font-semibold">€0,00</div>
    </td>
    <td class="text-right">
      <div class="flex justify-end gap-1">
        <button class="btn pos-dup" title="Duplizieren"><i data-feather="copy"></i></button>
        <button class="btn text-red-600 pos-del" title="Entfernen"><i data-feather="trash-2"></i></button>
      </div>
    </td>
  `;
  attachPosRowEvents(tr);
  return tr;
}

function attachPosRowEvents(tr) {
  const recalc = () => recalcPositions();
  ['pos-role','pos-rate','pos-qty','pos-hpp'].forEach(cls => tr.querySelector('.'+cls).addEventListener('input', recalc));
  tr.querySelector('.pos-dup').addEventListener('click', () => {
    const data = getRowData(tr);
    const clone = posRowTemplate(data);
    posTbody().insertBefore(clone, tr.nextSibling);
    recalcPositions();
  });
  tr.querySelector('.pos-del').addEventListener('click', () => { tr.remove(); recalcPositions(); });
  tr.querySelector('.pos-qty-plus').addEventListener('click', () => { const i = tr.querySelector('.pos-qty'); i.value = (+i.value||0)+1; recalcPositions(); });
  tr.querySelector('.pos-qty-minus').addEventListener('click', () => { const i = tr.querySelector('.pos-qty'); i.value = Math.max(0,(+i.value||0)-1); recalcPositions(); });
}

function getRowData(tr) {
  const role = tr.querySelector('.pos-role').value.trim();
  const rate = +tr.querySelector('.pos-rate').value || 0;
  const qty  = +tr.querySelector('.pos-qty').value  || 0;
  const hpp  = +tr.querySelector('.pos-hpp').value  || 0;
  const hours = qty * hpp;
  const sum   = hours * rate;
  return { role, rate, qty, hpp, hours, sum };
}

function recalcPositions() {
  let lohnEK = 0, totalHours = 0;
  posTbody()?.querySelectorAll('tr').forEach(tr => {
    const d = getRowData(tr);
    totalHours += d.hours;
    lohnEK += d.sum;
    tr.querySelector('.pos-hours').textContent = d.hours.toFixed(1);
    tr.querySelector('.pos-sum').textContent = fmtEUR(d.sum);
  });
  byId('k_lohn_ek_view').textContent = fmtEUR(lohnEK);
  // also reflect in mini overview on the right (if present)
  byId('ov_lohn').textContent = fmtEUR(lohnEK);
  return { lohnEK, totalHours };
}

/* ---------- Master-Set loading ---------- */
// expects window.ROUTES.masterSets and .breakdownBase (setId -> /admin/master-sets/{id}/breakdown)
async function fetchJSON(url) {
  const r = await fetch(url, { headers: { 'Accept':'application/json' } });
  if (!r.ok) throw new Error('HTTP '+r.status);
  return await r.json();
}

async function loadAvailableSets() {
  const url = window.ROUTES?.masterSets;
  if (!url) return alert('ROUTES.masterSets ist nicht gesetzt.');
  try {
    const list = await fetchJSON(url); // expects [{id, setname, ...}, ...]
    const sel = byId('ms_select');
    sel.innerHTML = '<option value="">— Set auswählen —</option>';
    list.forEach(s => {
      const o = document.createElement('option');
      o.value = s.id; o.textContent = s.setname || ('Set #' + s.id);
      sel.appendChild(o);
    });
    byId('btnLoadSet').disabled = true;
  } catch(e) {
    console.error(e);
    alert('Konnte Sets nicht laden.');
  }
}

async function loadSetBreakdown(setId) {
  const base = window.ROUTES?.breakdownBase;
  if (!base) return alert('ROUTES.breakdownBase ist nicht gesetzt.');
  try {
    const data = await fetchJSON(`${base}/${setId}/breakdown`);
    // expected shape (example):
    // { material_ek: number, tools_ek: number, employees: [{role, rate, qty, hpp}], notes: ... }
    loadedSet = data;
    const mat = Number(data.material_ek || 0);
    const tools = byId('include_tools').checked ? Number(data.tools_ek || 0) : 0;
    const lohn = Number((data.employees||[]).reduce((a,e)=>a + (e.rate||0)*(e.qty||0)*(e.hpp||0), 0));
    byId('ms_mat').textContent = fmtEUR(mat);
    byId('ms_lohn').textContent = fmtEUR(lohn);
    byId('ms_tools').textContent = fmtEUR(tools);
    byId('btnLoadSet').disabled = false;
  } catch (e) {
    console.error(e);
    alert('Konnte Set-Details nicht laden.');
  }
}

function applyLoadedSetToInputs() {
  if (!loadedSet) return;

  const includeTools = byId('include_tools').checked;
  const mat   = Number(loadedSet.material_ek || 0);
  const lohn  = Number(loadedSet.lohn_ek || 0);
  const tools = includeTools ? Number(loadedSet.tools_ek || 0) : 0;

  // Autofill Material / Tools into EK fields
  byId('k_mat_ek').value   = (mat).toFixed(2);
  byId('k_klein_ek').value = (tools).toFixed(2);
  byId('k_transport').value = 300; // fixed default (you can override)

  // Autofill employees into Step-6 positions table
  posTbody().innerHTML = '';
  (loadedSet.employees || []).forEach(e => {
    posTbody().appendChild(posRowTemplate({
      role: e.position || e.role || '',
      rate: Number(e.rate || e.buy_rate || 0),
      qty:  Number(e.qty  || 1),
      hpp:  Number(e.hpp  || e.hours || 8)
    }));
  });

  // Autofill products/sub-products into Angebot (Step-7 table)
  const tbody = bodyEl();
  if (tbody) {
    tbody.innerHTML = '';
    (loadedSet.products || []).forEach(p => {
      addOfferRow({
        sku: p.sku || '',
        name: p.name || 'Produkt',
        qty: p.qty || 1,
        unit: p.retail_price || p.purchase_price || 0,
        vat: 19,
        cost: p.purchase_price || 0
      });
    });
    (loadedSet.sub_products || []).forEach(p => {
      addOfferRow({
        sku: p.sku || '',
        name: p.name || 'Zubehör',
        qty: p.qty || 1,
        unit: p.retail_price || p.purchase_price || 0,
        vat: 19,
        cost: p.purchase_price || 0
      });
    });
  }

  // Autofill tools as flat fee line if requested
  if (includeTools && (loadedSet.tools || []).length) {
    const sumTools = loadedSet.tools.reduce((s, t) => s + (t.total_price || 0), 0);
    if (sumTools > 0) {
      addOfferRow({
        sku: 'TOOLS',
        name: 'Werkzeug-/Gerätekosten (pauschal)',
        qty: 1,
        unit: sumTools,
        vat: 19,
        cost: sumTools
      });
    }
  }

  recalcPositions();
  computeCalc(); // refresh totals
}



/* ---------- Buttons / events ---------- */
 document.addEventListener('DOMContentLoaded', () => {
  const sel = byId('ms_select');

  if (sel) {
    sel.addEventListener('change', async (e) => {
      const id = e.target.value;

      // Enable/disable load button
      const btn = byId('btnLoadSet');
      if (btn) btn.disabled = !id;

      if (id) {
        try {
          await loadSetBreakdown(id);
          // auto-fill inputs when a set is chosen
          applyLoadedSetToInputs();
        } catch (err) {
          console.error("Fehler beim Laden des Master-Sets:", err);
          toast("Fehler beim Laden des Master-Sets", "error");
        }
      }
    });
  }

  // Suggest button
  byId('btnSuggestSets')?.addEventListener('click', loadAvailableSets);

  // Manual load button
  byId('btnLoadSet')?.addEventListener('click', () => {
    applyLoadedSetToInputs();
  });

  // Tools include toggle
  byId('include_tools')?.addEventListener('change', () => {
    if (loadedSet && byId('ms_select')?.value) {
      loadSetBreakdown(byId('ms_select').value);
    }
  });

  // Reset material EK
  byId('btnZeroMat')?.addEventListener('click', () => {
    byId('k_mat_ek').value     = 0;
    byId('k_klein_ek').value   = 0;
    byId('k_transport').value  = 0;
    computeCalc();
  });
});


/* ---------- Upgrade computeCalc() to use positions ---------- */
/* This REPLACES the body of your existing computeCalc() function.
   Keep the same name so your buttons still work. */



document.querySelector('[data-step="8"]')?.addEventListener('click', () => {
  const res = computeCalc();   // <-- now we get all values
  renderFinanzplan(res);
});

function renderFinanzplan(res) {
  const finOut = byId('finanzOut');
  if (!finOut || !res) return;

  const {
    matEK, lohnEK, toolsEK, gesamtEK,
    gkVertrieb, gkBuV, gkWagnis, gkSum,
    margeMat, margeLohn, margeGes,
    vkMat, vkLohn, vkTools, vkGes,
    gesStunden, umsatzProStd, margeProStd
  } = res;

  finOut.innerHTML = `
    <div class="grid grid-cols-2 gap-x-6 gap-y-1">
      <div class="text-slate-500">Material-EK</div><div class="text-right">${fmtEUR(matEK)}</div>
      <div class="text-slate-500">Lohn-EK</div><div class="text-right">${fmtEUR(lohnEK)}</div>
      <div class="text-slate-500">Tools-EK</div><div class="text-right">${fmtEUR(toolsEK)}</div>
      <div class="text-slate-700 font-medium">Gesamtkosten EK</div><div class="text-right font-medium">${fmtEUR(gesamtEK)}</div>

      <div class="col-span-2 border-t my-1"></div>

      <div class="text-slate-500">GK Vertrieb</div><div class="text-right">${fmtEUR(gkVertrieb)}</div>
      <div class="text-slate-500">GK Büro/Vw</div><div class="text-right">${fmtEUR(gkBuV)}</div>
      <div class="text-slate-500">GK Wagnis</div><div class="text-right">${fmtEUR(gkWagnis)}</div>
      <div class="text-slate-700 font-medium">Gemeinkosten gesamt</div><div class="text-right font-medium">${fmtEUR(gkSum)}</div>

      <div class="col-span-2 border-t my-1"></div>

      <div class="text-slate-500">Marge Material</div><div class="text-right">${fmtEUR(margeMat)}</div>
      <div class="text-slate-500">Marge Lohn</div><div class="text-right">${fmtEUR(margeLohn)}</div>
      <div class="text-slate-700">Marge gesamt</div><div class="text-right">${fmtEUR(margeGes)}</div>

      <div class="col-span-2 border-t my-1"></div>

      <div class="text-slate-500">VK Material inkl. GK</div><div class="text-right">${fmtEUR(vkMat)}</div>
      <div class="text-slate-500">VK Lohn inkl. GK</div><div class="text-right">${fmtEUR(vkLohn)}</div>
      <div class="text-slate-500">VK Tools inkl. GK</div><div class="text-right">${fmtEUR(vkTools)}</div>
      <div class="text-slate-900 font-semibold">Verkaufspreis gesamt</div><div class="text-right font-semibold">${fmtEUR(vkGes)}</div>

      <div class="col-span-2 border-t my-1"></div>

      <div class="text-slate-500">Gesamtstunden</div><div class="text-right">${gesStunden.toFixed(1)}</div>
      <div class="text-slate-500">Umsatz pro Stunde</div><div class="text-right">${fmtEUR(umsatzProStd)}</div>
      <div class="text-slate-500">Marge pro Stunde</div><div class="text-right">${fmtEUR(margeProStd)}</div>
      <div class="text-slate-500">Marge gesamt %</div>
      <div class="text-right">${vkGes ? ((margeGes/vkGes)*100).toFixed(2).replace('.', ',') : '0,00'}%</div>
    </div>
  `;
}


 

/* ---------- Hook your existing buttons ---------- */
document.addEventListener('DOMContentLoaded', () => {
  byId('btnCalc')?.addEventListener('click', window.computeCalc);
  byId('btnApplyToOffer')?.addEventListener('click', () => {
    const r = window.computeCalc();
    // push 2 lines to the Angebot editor as before
    addOfferRow({ sku: 'CALC-MAT',  name: 'Material (Kalkulation)', qty: 1, unit: r.vkMat,  disc: 0, mark: 0, vat: 0, cost: r.matEK });
    addOfferRow({ sku: 'CALC-LOHN', name: 'Lohn (Kalkulation)',    qty: 1, unit: r.vkLohn, disc: 0, mark: 0, vat: 0, cost: r.lohnEK });
    recalcAll();
    wizGo(7);
  });
});

 

document.addEventListener('DOMContentLoaded', () => {
  const vlInput = document.getElementById('vl');
  const rlInput = document.getElementById('rl');

  if (vlInput && rlInput) {
    vlInput.addEventListener('input', updateDeltaT);
    rlInput.addEventListener('input', updateDeltaT);
    updateDeltaT(); // initial run
  }
});


  
</script>

 <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function() {
    const OFFER_ID  = @json($offer->id);
    const FOLDER_ID = @json($folder->id);
    const UPDATE_URL = `/offers/${OFFER_ID}/folders/${FOLDER_ID}/details`;
    const CSRF = $('meta[name="csrf-token"]').attr('content');

    window.toast = function (msg, type='ok') {
      console.log("TOAST:", msg);
      const wrap = document.getElementById('toastWrap');
      if (!wrap) return;
      const el = document.createElement('div');
      el.className = `toast ${type}`;
      el.textContent = msg;
      wrap.appendChild(el);
      requestAnimationFrame(() => el.classList.add('show'));
      setTimeout(() => {
        el.classList.remove('show');
        setTimeout(() => el.remove(), 250);
      }, 1800);
    };

    // Save one field
    function saveField(name, value){
        console.log("SENDING:", name, value); // Debug Browser
        $.ajax({
            url: UPDATE_URL,
            method: 'PATCH',
            headers: {'X-CSRF-TOKEN': CSRF},
            data: { [name]: value },
            success: function(resp){
                console.log("RESPONSE:", resp);
                if(resp.detail && resp.detail.delta !== undefined){
                    $('#delta').val(resp.detail.delta);
                }
                toast("Gespeichert");
            },
            error: function(xhr){
                console.error("AJAX ERROR", xhr.responseText);
                toast("Fehler beim Speichern","err");
            }
        });
    }

    // Bind all inputs/selects with data-offerdetail
    $('[data-offerdetail]').each(function(){
        const $el = $(this);
        const name = $el.attr('name');
        if(!name) return;

        const handler = function(){
            let val;
            if($el.attr('type') === 'checkbox'){
                val = $el.prop('checked') ? 1 : 0;
            } else {
                val = $el.val();
            }
            saveField(name, val);
        };

        if($el.is('select') || $el.attr('type') === 'range'){
            $el.on('change', handler);
        } else {
            $el.on('blur', handler); // speichern erst beim verlassen
        }
    });
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        const selected = button.dataset.tab;

        tabButtons.forEach(btn => btn.classList.remove('active', 'text-blue-600', 'border-blue-600'));
        button.classList.add('active', 'text-blue-600', 'border-blue-600');

        tabContents.forEach(tab => {
          tab.classList.toggle('hidden', tab.id !== `tab-${selected}`);
        });
      });
    });
  });
</script>


@endsection