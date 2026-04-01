@extends('admin.layouts.app')
{{-- === Heatpump Analytics Tabs === --}}
@section('style')
 
<style>
  .tab-card .card-body { padding-top: .75rem; }
  .form-inline .form-group { margin-right: .75rem; }
  .mini input[type="number"]{ max-width: 120px; }
  canvas { max-height: 420px; }
  .table-xs td, .table-xs th { padding: .4rem .5rem; }
</style>

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

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
            <h2 class="content-header-title float-left mb-0">WP</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/product_details/'.$product_id)}}">{{ $product->product }}</a></li>
                <li class="breadcrumb-item">List</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="row" id="table-hover-animation">
        <div class="col-12">
          <div class="card">
            <div class="card-header align-items-center">
              <h4 class="card-title mb-0">WP KONFIGURATOR</h4>
            </div> 
              <div class="card tab-card mt-2">
                <div class="card-header">
                  <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#hp-curves" role="tab">1) Leistungskurven</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hp-range" role="tab">2) Modulationsbereich</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hp-cop" role="tab">3) COP / SCOP</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hp-load" role="tab">4) Gebäude-Last</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hp-cost" role="tab">5) Kosten</a></li>
                  </ul>
                </div>
                <div class="card-body tab-content">

                  {{-- 1) Leistungskurven --}}
                  <div class="tab-pane fade show active" id="hp-curves" role="tabpanel">
                    <div class="d-flex flex-wrap align-items-end mb-2 mini">
                      <div class="form-group">
                        <label class="mb-0">Varianten  <small class="text-muted d-block">Strg/Cmd für Mehrfachwahl</small></label>
                        <select id="curves-models" class="form-control select2" multiple style="width:100%">
                          <option value="8er" selected>8er</option>
                          <option value="9er" selected>9er</option>
                          <option value="10er" selected>10er</option>
                        </select>
                       
                      </div>
                      <div class="form-group ml-1 mr-1">
                        <label class="mb-0">Zeige</label>
                        <select id="curves-mode" class="form-control select2" style="width:100%">
                          <option value="max" selected>Max kW</option>
                          <option value="min">Min kW</option>
                          <option value="band">Band (Min–Max)</option>
                        </select> 
                      </div>
                      <button class="btn btn-outline-primary mb-2" id="btnCurvesUpdate"><i class="feather icon-refresh-ccw"></i> Aktualisieren</button>
                    </div>
                    <canvas id="chart-curves"></canvas>
                    <p class="text-muted mt-1 mb-0">Vergleiche 8er/9er/10er bei 0 °C, 7 °C etc. – wie stark die Leistung mit fallender Außentemperatur abnimmt.</p>
                  </div>

                 
                  <div class="tab-pane fade" id="hp-range" role="tabpanel">
                    <div class="d-flex flex-wrap align-items-end mb-2 mini">
                      <div class="form-group">
                        <label class="mb-0">Varianten</label>
                        <select id="range-models"   multiple class="form-control select2" style="width:100%">
                          <option value="8er" selected>8er</option>
                          <option value="9er" selected>9er</option>
                          <option value="10er" selected>10er</option>
                        </select>
                        <small class="text-muted d-block">Band (Min–Max) wird schattiert</small>
                      </div>
                      <div class="form-group mr-1 ml-1">
                        <label class="mb-0">Hauslast @ −10 °C (kW)</label>
                        <input id="house-load-at--10" type="number" step="0.1" class="form-control" value="6">
                        <small class="text-muted d-block">Lineare Lastkurve bis 15 °C → 0 kW</small>
                      </div>
                      <button class="btn btn-outline-primary mb-2" id="btnRangeUpdate"><i class="feather icon-refresh-ccw"></i> Aktualisieren</button>
                    </div>
                    <canvas id="chart-range"></canvas>
                    <table class="table table-sm table-bordered table-xs mt-2" id="range-summary">
                      <thead><tr><th>Variante</th><th>Deckung bei −10 °C</th><th>Niedriglast-Eignung bei +7 °C</th></tr></thead>
                      <tbody></tbody>
                    </table>
                  </div>

                  {{-- 3) COP / SCOP --}}
                  <div class="tab-pane fade" id="hp-cop" role="tabpanel">
                    <div class="alert alert-secondary">
                      MVP-Modell: Wir schätzen die COP-Kurve linear zwischen zwei Punkten und gewichten mit Klimastunden (einfaches Profil, editierbar).
                    </div>

                    <div class="row mini">
                      <div class="col-md-6">
                        <h6>COP-Eckpunkte (pro Variante identisch; editierbar):</h6>
                        <div class="form-inline mb-1">
                          <div class="form-group">
                            <label class="mr-1">COP @ −7 °C</label>
                            <input id="copMinus7" type="number" step="0.1" class="form-control" value="2.7">
                          </div>
                          <div class="form-group">
                            <label class="mx-2">COP @ +7 °C</label>
                            <input id="copPlus7" type="number" step="0.1" class="form-control" value="4.0">
                          </div>
                        </div>

                        <h6 class="mt-2">Klimastunden (vereinfachte Bins, Summe ≈ 8760):</h6>
                        <table class="table table-sm table-bordered table-xs" id="climate-bins">
                          <thead><tr><th>Temp (°C)</th><th>Stunden/Jahr</th></tr></thead>
                          <tbody>
                            {{-- editable rows --}}
                          </tbody>
                        </table>
                        <button class="btn btn-outline-secondary btn-sm" id="btnResetClimate">Standard laden</button>
                      </div>

                      <div class="col-md-6">
                        <div class="form-inline mb-1">
                          <div class="form-group">
                            <label class="mr-1">Tarif (€/kWh)</label>
                            <input id="tariff" type="number" step="0.01" class="form-control" value="0.32">
                          </div>
                        </div>
                        <button class="btn btn-outline-primary mb-2" id="btnCopCalc"><i class="feather icon-activity"></i> SCOP & Verbrauch berechnen</button>
                        <canvas id="chart-cop"></canvas>
                        <table class="table table-sm table-bordered table-xs mt-2" id="scop-summary">
                          <thead><tr><th>Variante</th><th>SCOP</th><th>Jahresverbrauch (kWh)</th><th>Jahreskosten (€)</th></tr></thead>
                          <tbody></tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  {{-- 4) Gebäude-Last Simulation --}}
                  <div class="tab-pane fade" id="hp-load" role="tabpanel">
                    <div class="d-flex flex-wrap align-items-end mb-2 mini">
                      <div class="form-group">
                        <label class="mb-0">Variante</label>
                        <select id="load-model" class="form-control">
                          <option value="8er" selected>8er</option>
                          <option value="9er">9er</option>
                          <option value="10er">10er</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label class="mb-0">Hauslast @ −10 °C (kW)</label>
                        <input id="sim-load--10" type="number" step="0.1" class="form-control" value="6">
                      </div>
                      <button class="btn btn-outline-primary" id="btnSimUpdate"><i class="feather icon-sliders"></i> Aktualisieren</button>
                    </div>
                    <canvas id="chart-load"></canvas>
                    <p class="text-muted mb-0">Markierungen zeigen Temperaturbereiche, in denen die gewählte Variante die Last nicht deckt.</p>
                  </div>

                  {{-- 5) Kosten / Wirtschaftlichkeit --}}
                  <div class="tab-pane fade" id="hp-cost" role="tabpanel">
                    <div class="d-flex flex-wrap align-items-end mb-2 mini">
                      <div class="form-group">
                        <label class="mb-0">Tarif (€/kWh)</label>
                        <input id="cost-tariff" type="number" step="0.01" class="form-control" value="0.32">
                      </div>
                      <button class="btn btn-outline-primary" id="btnCostCalc"><i class="feather icon-dollar-sign"></i> Neu berechnen</button>
                    </div>
                    <canvas id="chart-cost"></canvas>
                    <table class="table table-sm table-bordered table-xs mt-2" id="cost-summary">
                      <thead><tr><th>Variante</th><th>SCOP</th><th>kWh/Jahr</th><th>€/Jahr</th></tr></thead>
                      <tbody></tbody>
                    </table>
                  </div>

                </div>
              </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>


<script>
    $(document).ready(function() { 

        $('.select2').select2();
    });
</script>
 
<script>
/** ====== Data (same spirit as your defaults; adjust freely) ====== */
const PRODUCT_NAME = @json($product->product ?? 'Heatpump');

const PROFILES = {
  product: PRODUCT_NAME,
  variants: [
    { model: "8er",  performance: [
      { t:-20, max:7.8,  min:1.8 },{ t:-15, max:8.5,  min:2.0 },
      { t:-10, max:8.8,  min:2.1 },{ t:-7,  max:8.9,  min:2.2 },
      { t:-2,  max:9.0,  min:2.4 },{ t:0,   max:9.2,  min:2.5 },
      { t:2,   max:9.6,  min:2.7 },{ t:7,   max:10.0, min:3.0 },
      { t:10,  max:10.3, min:3.2 },{ t:15,  max:10.7, min:3.5 },
    ]},
    { model: "9er",  performance: [
      { t:-20, max:8.6,  min:2.2 },{ t:-15, max:9.5,  min:2.5 },
      { t:-10, max:9.9,  min:2.7 },{ t:-7,  max:10.2, min:2.8 },
      { t:-2,  max:10.4, min:3.0 },{ t:0,   max:10.5, min:3.0 },
      { t:2,   max:10.9, min:3.2 },{ t:7,   max:11.5, min:3.5 },
      { t:10,  max:11.8, min:3.7 },{ t:15,  max:12.2, min:4.0 },
    ]},
    { model: "10er", performance: [
      { t:-20, max:9.6,  min:2.6 },{ t:-15, max:10.5, min:3.0 },
      { t:-10, max:10.9, min:3.2 },{ t:-7,  max:11.2, min:3.3 },
      { t:-2,  max:11.4, min:3.4 },{ t:0,   max:11.5, min:3.5 },
      { t:2,   max:11.9, min:3.7 },{ t:7,   max:12.5, min:4.0 },
      { t:10,  max:12.9, min:4.2 },{ t:15,  max:13.3, min:4.5 },
    ]},
  ]
};

/** Simple helpers */
const colors = ['#2563eb','#16a34a','#f59e0b','#ef4444','#7c3aed','#0891b2'];
function getVariant(name){ return PROFILES.variants.find(v=>v.model===name); }
function uniqTemps(){
  const set = new Set();
  PROFILES.variants.forEach(v=>v.performance.forEach(p=>set.add(p.t)));
  return Array.from(set).sort((a,b)=>a-b);
}
function interpHouseLoad(temp, loadAtMinus10){
  // Linear: at -10°C => given load; at 15°C => 0 kW
  const t1=-10, L1=loadAtMinus10;
  const t2= 15, L2=0;
  const m=(L2-L1)/(t2-t1);
  return L1 + m*(temp - t1);
}
function buildClimateDefault(){
  // Very coarse, illustrative bin hours (approx for DACH temperate climate).
  // Edit freely in UI; rows will be used as-is.
  return [
    { t:-20, h:  50 }, { t:-15, h: 120 }, { t:-10, h: 300 }, { t:-7, h: 450 },
    { t:-2,  h: 800 }, { t:0,   h: 950 }, { t:2,   h: 950 }, { t:7,  h: 1400 },
    { t:10,  h: 1200 },{ t:15,  h: 900 }, { t:18,  h: 640 } // 18°C bucket to soak shoulder season
  ];
}
function readClimateBins(){
  const rows = [];
  document.querySelectorAll('#climate-bins tbody tr').forEach(tr=>{
    const t = parseFloat(tr.querySelector('input[data-t]').value);
    const h = parseFloat(tr.querySelector('input[data-h]').value);
    if(!isNaN(t) && !isNaN(h)) rows.push({t,h});
  });
  return rows;
}
function writeClimateBins(arr){
  const tbody = document.querySelector('#climate-bins tbody');
  tbody.innerHTML = '';
  arr.forEach((row,i)=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type="number" step="1"  class="form-control" data-t value="${row.t}"></td>
      <td><input type="number" step="1"  class="form-control" data-h value="${row.h}"></td>`;
    tbody.appendChild(tr);
  });
}

/** ====== 1) Leistungskurven ====== */
let chartCurves;
function renderCurves(){
  const selected = Array.from(document.getElementById('curves-models').selectedOptions).map(o=>o.value);
  const mode = document.getElementById('curves-mode').value;
  const labels = uniqTemps();

  const datasets = [];
  selected.forEach((m,idx)=>{
    const v = getVariant(m);
    const byT = new Map(v.performance.map(p=>[p.t,p]));
    if(mode==='band'){
      datasets.push({
        label: `${m} max`,
        data: labels.map(t=>byT.get(t)?.max ?? null),
        borderColor: colors[idx%colors.length],
        backgroundColor: colors[idx%colors.length]+'33',
        fill: true, tension: .2
      });
      datasets.push({
        label: `${m} min`,
        data: labels.map(t=>byT.get(t)?.min ?? null),
        borderColor: colors[idx%colors.length],
        backgroundColor: '#00000000',
        fill: '-1', tension: .2
      });
    } else {
      const key = (mode==='max'?'max':'min');
      datasets.push({
        label: `${m} ${key}`,
        data: labels.map(t=>byT.get(t)?.[key] ?? null),
        borderColor: colors[idx%colors.length],
        fill: false, tension: .2
      });
    }
  });

  if(chartCurves) chartCurves.destroy();
  chartCurves = new Chart(document.getElementById('chart-curves'), {
    type: 'line',
    data: { labels, datasets },
    options: {
      responsive: true,
      interaction: { mode: 'nearest', intersect: false },
      scales: {
        x: { title: { display:true, text:'Außenluft (°C)' } },
        y: { title: { display:true, text:'Heizleistung (kW)' }, beginAtZero: false }
      }
    }
  });
}

/** ====== 2) Modulationsbereich & Dimensionierung ====== */
let chartRange;
function renderRange(){
  const selected = Array.from(document.getElementById('range-models').selectedOptions).map(o=>o.value);
  const labels = uniqTemps();
  const loadAtMinus10 = parseFloat(document.getElementById('house-load-at--10').value) || 0;

  const datasets = [];
  selected.forEach((m,idx)=>{
    const v = getVariant(m);
    const byT = new Map(v.performance.map(p=>[p.t,p]));

    // max line (filled)
    datasets.push({
      label: `${m} max`,
      data: labels.map(t=>byT.get(t)?.max ?? null),
      borderColor: colors[idx%colors.length],
      backgroundColor: colors[idx%colors.length]+'33',
      fill: true, tension: .2
    });
    // min line (fills to previous to produce band)
    datasets.push({
      label: `${m} min`,
      data: labels.map(t=>byT.get(t)?.min ?? null),
      borderColor: colors[idx%colors.length],
      backgroundColor: '#00000000',
      fill: '-1', tension: .2
    });
  });

  // house load line
  datasets.push({
    label: 'Hauslast',
    data: labels.map(t=>interpHouseLoad(t, loadAtMinus10)),
    borderDash: [6,4],
    borderColor: '#ef4444',
    fill: false, tension: .1
  });

  if(chartRange) chartRange.destroy();
  chartRange = new Chart(document.getElementById('chart-range'), {
    type: 'line',
    data: { labels, datasets },
    options: {
      responsive: true,
      interaction: { mode:'nearest', intersect:false },
      scales: {
        x: { title:{ display:true, text:'Außenluft (°C)' } },
        y: { title:{ display:true, text:'kW' } }
      }
    }
  });

  // quick textual summary at -10°C and +7°C
  const tbody = document.querySelector('#range-summary tbody');
  tbody.innerHTML = '';
  selected.forEach(m=>{
    const v = getVariant(m);
    const pMinus10 = v.performance.find(p=>p.t===-10);
    const pPlus7   = v.performance.find(p=>p.t===7);
    const coverAtMinus10 = pMinus10 ? (pMinus10.max >= interpHouseLoad(-10, loadAtMinus10) ? 'deckt' : 'deckt NICHT') : 'n/a';
    const lowLoadAtPlus7 = pPlus7 ? (pPlus7.min <= interpHouseLoad(7, loadAtMinus10) ? 'gut modulierbar' : 'evtl. Takten') : 'n/a';
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${m}</td><td>${coverAtMinus10}</td><td>${lowLoadAtPlus7}</td>`;
    tbody.appendChild(tr);
  });
}

/** ====== 3) COP / SCOP ====== */
let chartCop;
function copAtTemp(t, copM7, copP7){
  // linear interpolate COP between -7 and +7; extrapolate beyond
  const t1=-7, c1=copM7, t2=7, c2=copP7;
  const m=(c2-c1)/(t2-t1);
  return c1 + m*(t - t1);
}
function computeScopAndEnergy(tariff){
  const bins = readClimateBins(); // [{t,h}]
  const results = []; // {model, scop, kwh, eur}

  PROFILES.variants.forEach(v=>{
    // For each bin: choose max output at that temp (assume unit runs up to needed),
    // compute input = output / COP(t), sum input*hours → kWh
    // For SCOP: we need weighted output/input; here we use (sum output) / (sum input)
    let sumOut=0, sumIn=0;

    bins.forEach(b=>{
      // find nearest performance point for simplicity (or exact match)
      // better: interpolate between neighbors; here nearest is ok MVP
      const perf = nearestPerf(v.performance, b.t);
      const cop  = Math.max(1.2, copAtTemp(b.t,
        parseFloat(document.getElementById('copMinus7').value)||2.7,
        parseFloat(document.getElementById('copPlus7').value)||4.0
      ));
      const outKw = perf.max;                 // potential output
      const inKw  = outKw / cop;              // electrical input at that temp
      sumOut += outKw * b.h;
      sumIn  += inKw  * b.h;
    });

    const scop = sumOut / sumIn;
    const kwh  = sumIn; // input energy over the year
    const eur  = kwh * tariff;
    results.push({ model:v.model, scop, kwh, eur });
  });
  return results;
}
function nearestPerf(arr, t){
  // pick closest temperature point
  let best = arr[0], bestDiff = Math.abs(arr[0].t - t);
  for(const p of arr){
    const d = Math.abs(p.t - t);
    if(d < bestDiff){ best = p; bestDiff = d; }
  }
  return best;
}
function renderCop(){
  const labels = uniqTemps();
  const copM7 = parseFloat(document.getElementById('copMinus7').value)||2.7;
  const copP7 = parseFloat(document.getElementById('copPlus7').value)||4.0;

  const datasets = PROFILES.variants.map((v,idx)=>({
    label: `${v.model} (COP)`,
    data: labels.map(t=>copAtTemp(t, copM7, copP7)),
    borderColor: colors[idx%colors.length],
    fill: false, tension: .2
  }));

  if(chartCop) chartCop.destroy();
  chartCop = new Chart(document.getElementById('chart-cop'), {
    type: 'line',
    data: { labels, datasets },
    options: {
      responsive:true,
      scales: {
        x:{ title:{display:true,text:'Außenluft (°C)'} },
        y:{ title:{display:true,text:'COP'}, min:1, suggestedMax:5 }
      }
    }
  });

  const tariff = parseFloat(document.getElementById('tariff').value)||0.3;
  const res = computeScopAndEnergy(tariff);
  const tbody = document.querySelector('#scop-summary tbody');
  tbody.innerHTML = '';
  res.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${r.model}</td><td>${r.scop.toFixed(2)}</td><td>${Math.round(r.kwh).toLocaleString()}</td><td>${r.eur.toFixed(0)}</td>`;
    tbody.appendChild(tr);
  });

  // store for cost tab reuse
  window.__hp_cost_cache = res;
}

/** ====== 4) Gebäude-Last Simulation ====== */
let chartLoad;
function renderLoad(){
  const model = document.getElementById('load-model').value;
  const v = getVariant(model);
  const labels = uniqTemps();
  const loadAtMinus10 = parseFloat(document.getElementById('sim-load--10').value)||0;
  const byT = new Map(v.performance.map(p=>[p.t,p]));

  const perf = labels.map(t=>byT.get(t)?.max ?? null);
  const load = labels.map(t=>interpHouseLoad(t, loadAtMinus10));

  if(chartLoad) chartLoad.destroy();
  chartLoad = new Chart(document.getElementById('chart-load'), {
    type: 'line',
    data: {
      labels,
      datasets: [
        { label:`${model} max`, data: perf, borderColor: '#2563eb', fill:false, tension:.2 },
        { label:'Hauslast', data: load, borderDash:[6,4], borderColor:'#ef4444', fill:false, tension:.1 },
      ]
    },
    options: {
      responsive:true,
      plugins:{
        annotation:{
          // optional: add markers later with plugin
        }
      },
      scales:{
        x:{ title:{display:true,text:'Außenluft (°C)'} },
        y:{ title:{display:true,text:'kW'} }
      }
    }
  });
}

/** ====== 5) Kosten / Wirtschaftlichkeit ====== */
let chartCost;
function renderCost(){
  // Reuse SCOP computation (or compute if not present)
  const tariff = parseFloat(document.getElementById('cost-tariff').value)||0.32;
  const base = window.__hp_cost_cache ?? computeScopAndEnergy(tariff);

  const labels = base.map(r=>r.model);
  const kwh = base.map(r=>r.kwh);
  const eur = base.map(r=>r.kwh * tariff);

  if(chartCost) chartCost.destroy();
  chartCost = new Chart(document.getElementById('chart-cost'), {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label:'kWh/Jahr', data:kwh, yAxisID:'y1' },
        { label:'€/Jahr',   data:eur, yAxisID:'y2' },
      ]
    },
    options: {
      responsive:true,
      scales:{
        y1:{ type:'linear', position:'left', title:{display:true,text:'kWh/Jahr'} },
        y2:{ type:'linear', position:'right', grid:{drawOnChartArea:false}, title:{display:true,text:'€/Jahr'} }
      }
    }
  });

  const tbody = document.querySelector('#cost-summary tbody');
  tbody.innerHTML = '';
  base.forEach((r,i)=>{
    const row = document.createElement('tr');
    row.innerHTML = `<td>${r.model}</td><td>${r.scop.toFixed(2)}</td><td>${Math.round(kwh[i]).toLocaleString()}</td><td>${eur[i].toFixed(0)}</td>`;
    tbody.appendChild(row);
  });
}

/** ====== Wire up UI ====== */
$(function(){
  // populate climate default
  writeClimateBins(buildClimateDefault());

  // render initial charts
  renderCurves();
  renderRange();
  renderCop();
  renderLoad();
  renderCost();

  // Events
  $('#btnCurvesUpdate').on('click', renderCurves);
  $('#btnRangeUpdate').on('click', renderRange);
  $('#btnCopCalc').on('click', ()=>{ renderCop(); renderCost(); });
  $('#btnResetClimate').on('click', ()=>{ writeClimateBins(buildClimateDefault()); });

  $('#btnSimUpdate').on('click', renderLoad);
  $('#btnCostCalc').on('click', renderCost);

  // also re-render when switching tabs to ensure perfect sizing
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    const id = $(e.target).attr('href');
    if(id==='#hp-curves') renderCurves();
    if(id==='#hp-range')  renderRange();
    if(id==='#hp-cop')    renderCop();
    if(id==='#hp-load')   renderLoad();
    if(id==='#hp-cost')   renderCost();
  });
});
</script>
@endsection
