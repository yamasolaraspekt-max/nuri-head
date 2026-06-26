<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fachwizard Energie</title>
  <style>
    :root{
      --green:#74b91f;
      --blue:#16324f;
      --border:#dbe4ee;
      --bg:#f5f7fb;
      --card:#ffffff;
      --muted:#6b7280;
      --danger:#dc2626;
      --warn:#d97706;
      --ok:#16a34a;
    }

    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:Arial, Helvetica, sans-serif;
      background:var(--bg);
      color:#1f2937;
    }

    .app{
      display:flex;
      flex-direction:column;
      min-height:100vh;
    }

    .topbar{
      background:#fff;
      border-bottom:1px solid var(--border);
      padding:16px 20px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
      position:sticky;
      top:0;
      z-index:20;
    }

    .brand h1{
      margin:0;
      font-size:22px;
      color:var(--blue);
    }

    .brand p{
      margin:4px 0 0;
      color:var(--muted);
      font-size:13px;
    }

    .topstats{
      display:flex;
      gap:12px;
      flex-wrap:wrap;
    }

    .pill{
      background:#fff;
      border:1px solid var(--border);
      border-radius:999px;
      padding:8px 12px;
      font-size:12px;
      font-weight:700;
    }

    .interestbar{
      background:#fff;
      border-bottom:1px solid var(--border);
      padding:10px 20px;
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      align-items:center;
    }

    .interestbar .label{
      font-size:11px;
      font-weight:700;
      color:var(--muted);
      text-transform:uppercase;
      letter-spacing:.08em;
      margin-right:4px;
    }

    .interest-toggle{
      border:1px solid var(--border);
      background:#f8fafc;
      border-radius:999px;
      padding:8px 12px;
      cursor:pointer;
      font-size:12px;
      font-weight:700;
    }

    .interest-toggle.active{
      background:rgba(116,185,31,.12);
      border-color:rgba(116,185,31,.35);
      color:var(--blue);
    }

    .workspace{
      display:grid;
      grid-template-columns:280px 1fr 360px;
      gap:0;
      min-height:calc(100vh - 120px);
    }

    .sidebar,
    .rightbar{
      background:#fff;
      border-right:1px solid var(--border);
      overflow:auto;
    }

    .rightbar{
      border-right:none;
      border-left:1px solid var(--border);
      background:#f8fafc;
    }

    .panel-head{
      padding:14px 16px;
      border-bottom:1px solid var(--border);
      font-size:12px;
      font-weight:700;
      text-transform:uppercase;
      letter-spacing:.08em;
      color:var(--muted);
      background:#fff;
      position:sticky;
      top:0;
      z-index:5;
    }

    .step-list{
      padding:12px;
    }

    .step-btn{
      width:100%;
      text-align:left;
      border:1px solid transparent;
      background:#fff;
      border-radius:16px;
      padding:12px;
      margin-bottom:10px;
      cursor:pointer;
      transition:.2s;
    }

    .step-btn:hover{
      background:#f8fafc;
      border-color:var(--border);
    }

    .step-btn.active{
      background:rgba(116,185,31,.1);
      border-color:rgba(116,185,31,.35);
    }

    .step-title{
      font-weight:700;
      color:var(--blue);
      margin-bottom:4px;
    }

    .step-sub{
      font-size:12px;
      color:var(--muted);
      margin-bottom:8px;
    }

    .progress{
      width:100%;
      height:8px;
      background:#e5e7eb;
      border-radius:999px;
      overflow:hidden;
    }

    .progress > span{
      display:block;
      height:100%;
      background:var(--green);
    }

    .main{
      padding:24px;
      overflow:auto;
    }

    .main-inner{
      max-width:980px;
      margin:0 auto;
    }

    .section-title{
      margin:0 0 6px;
      font-size:28px;
      color:var(--blue);
    }

    .section-sub{
      margin:0 0 20px;
      color:var(--muted);
      font-size:14px;
    }

    .card{
      background:var(--card);
      border:1px solid var(--border);
      border-radius:20px;
      padding:18px;
      margin-bottom:18px;
      box-shadow:0 1px 2px rgba(0,0,0,.03);
    }

    .card h3{
      margin:0 0 6px;
      color:var(--blue);
      font-size:16px;
    }

    .card .meta{
      color:var(--muted);
      font-size:12px;
      margin-bottom:16px;
    }

    .grid{
      display:grid;
      gap:14px;
    }

    .grid-2{grid-template-columns:repeat(2, minmax(0,1fr))}
    .grid-3{grid-template-columns:repeat(3, minmax(0,1fr))}
    .grid-4{grid-template-columns:repeat(4, minmax(0,1fr))}

    .field{
      display:flex;
      flex-direction:column;
      gap:6px;
    }

    .field label{
      font-size:13px;
      font-weight:700;
      color:var(--blue);
    }

    .field input,
    .field select,
    .field textarea{
      width:100%;
      border:1px solid #d1d5db;
      border-radius:12px;
      padding:10px 12px;
      font-size:14px;
      background:#fff;
      outline:none;
    }

    .field textarea{
      min-height:100px;
      resize:vertical;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus{
      border-color:var(--green);
      box-shadow:0 0 0 3px rgba(116,185,31,.15);
    }

    .chips{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
    }

    .chip{
      border:1px solid var(--border);
      background:#fff;
      border-radius:999px;
      padding:8px 12px;
      cursor:pointer;
      font-size:13px;
      font-weight:700;
    }

    .chip.active{
      background:rgba(116,185,31,.14);
      border-color:rgba(116,185,31,.35);
    }

    .stats{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:12px;
      margin-top:16px;
    }

    .stat{
      border:1px solid var(--border);
      background:#fff;
      border-radius:16px;
      padding:14px;
    }

    .stat small{
      display:block;
      color:var(--muted);
      text-transform:uppercase;
      letter-spacing:.06em;
      font-weight:700;
      font-size:10px;
      margin-bottom:6px;
    }

    .stat strong{
      font-size:20px;
      color:var(--blue);
    }

    .actions{
      display:flex;
      justify-content:space-between;
      gap:12px;
      margin-top:24px;
    }

    .btn{
      border:none;
      border-radius:12px;
      padding:12px 18px;
      font-weight:700;
      cursor:pointer;
    }

    .btn-primary{
      background:var(--blue);
      color:#fff;
    }

    .btn-green{
      background:var(--green);
      color:#fff;
    }

    .btn-light{
      background:#fff;
      color:#374151;
      border:1px solid var(--border);
    }

    .btn-danger{
      background:#ef4444;
      color:#fff;
    }

    .muted{
      color:var(--muted);
      font-size:12px;
    }

    .notice{
      border:1px solid #fde68a;
      background:#fffbeb;
      color:#92400e;
      border-radius:14px;
      padding:12px 14px;
      margin-bottom:16px;
      font-size:13px;
    }

    .success{
      border:1px solid #bbf7d0;
      background:#f0fdf4;
      color:#166534;
      border-radius:14px;
      padding:12px 14px;
      margin-bottom:16px;
      font-size:13px;
    }

    .list{
      display:flex;
      flex-direction:column;
      gap:10px;
    }

    .row-card{
      border:1px solid var(--border);
      background:#fff;
      border-radius:16px;
      padding:14px;
    }

    .row-card h4{
      margin:0 0 10px;
      color:var(--blue);
    }

    .row-actions{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      margin-top:10px;
    }

    .tabs{
      display:flex;
      gap:8px;
      padding:12px;
      border-bottom:1px solid var(--border);
      background:#fff;
      position:sticky;
      top:41px;
      z-index:4;
    }

    .tab-btn{
      border:1px solid var(--border);
      background:#fff;
      border-radius:12px;
      padding:8px 12px;
      cursor:pointer;
      font-size:12px;
      font-weight:700;
    }

    .tab-btn.active{
      background:var(--blue);
      color:#fff;
      border-color:var(--blue);
    }

    .right-content{
      padding:14px;
    }

    .history-item{
      border-bottom:1px solid #e5e7eb;
      padding:10px 0;
      font-size:13px;
    }

    .media-grid{
      display:grid;
      grid-template-columns:repeat(3, minmax(0,1fr));
      gap:10px;
    }

    .media-item{
      border:1px solid var(--border);
      background:#fff;
      border-radius:14px;
      overflow:hidden;
      position:relative;
    }

    .media-item img{
      width:100%;
      height:120px;
      object-fit:cover;
      display:block;
    }

    .media-item .cap{
      padding:8px;
      font-size:12px;
    }

    .signature-wrap{
      border:2px dashed #cbd5e1;
      border-radius:16px;
      background:#f8fafc;
      padding:10px;
    }

    #signatureCanvas{
      width:100%;
      height:180px;
      background:#fff;
      border-radius:12px;
      border:1px solid var(--border);
      touch-action:none;
      display:block;
    }

    .hidden{display:none !important;}

    @media (max-width: 1200px){
      .workspace{
        grid-template-columns:240px 1fr;
      }
      .rightbar{
        display:none;
      }
    }

    @media (max-width: 900px){
      .workspace{
        grid-template-columns:1fr;
      }
      .sidebar{
        display:none;
      }
      .grid-2,.grid-3,.grid-4,.stats,.media-grid{
        grid-template-columns:1fr;
      }
      .main{
        padding:16px;
      }
      .topbar{
        flex-direction:column;
        align-items:flex-start;
      }
    }

    @media print{
      .topbar,
      .interestbar,
      .sidebar,
      .rightbar,
      .actions,
      .no-print{
        display:none !important;
      }
      .workspace{
        display:block;
      }
      .main{
        padding:0;
      }
      .card{
        box-shadow:none;
        break-inside:avoid;
      }
    }
  </style>
</head>
<body>
<div class="app">
  <header class="topbar no-print">
    <div class="brand">
      <h1>Fachwizard Energie</h1>
      <p>Single HTML Version ohne React</p>
    </div>

    <div class="topstats">
      <div class="pill">Fortschritt: <span id="globalPercent">0</span>%</div>
      <div class="pill">Gesamtstrom: <span id="gesamtStromTop">0</span> kWh</div>
      <div class="pill">PV: <span id="pvTop">0.0</span> kWp</div>
      <div class="pill">Heizlast: <span id="heizlastTop">0.0</span> kW</div>
    </div>
  </header>

  <div class="interestbar no-print">
    <span class="label">Interesse</span>
    <button class="interest-toggle" data-toggle="zielPV">PV</button>
    <button class="interest-toggle" data-toggle="zielWP">WP</button>
    <button class="interest-toggle" data-toggle="zielSpeicher">Speicher</button>
    <button class="interest-toggle" data-toggle="zielWallbox">Wallbox</button>
    <button class="interest-toggle" data-toggle="zielHeizlast">Heizlast</button>
    <button class="interest-toggle" data-toggle="zielFenster">Fenster</button>
    <button class="interest-toggle" data-toggle="zielTueren">Türen</button>
    <button class="interest-toggle" data-toggle="zielBad">Bad</button>
    <button class="interest-toggle" data-toggle="zielKueche">Küche</button>
  </div>

  <div class="workspace">
    <aside class="sidebar no-print">
      <div class="panel-head">Workflow</div>
      <div class="step-list" id="stepList"></div>
    </aside>

    <main class="main">
      <div class="main-inner">
        <h2 class="section-title" id="stepTitle">Projektstart</h2>
        <p class="section-sub" id="stepSub">Kontakt, Interesse und Objektstart</p>

        <div id="warningsBox"></div>

        <div id="stepContent"></div>

        <div class="actions no-print">
          <button class="btn btn-light" id="prevBtn">Zurück</button>
          <div style="display:flex; gap:8px;">
            <button class="btn btn-green" id="saveBtn">Speichern</button>
            <button class="btn btn-primary" id="nextBtn">Weiter</button>
          </div>
        </div>
      </div>
    </main>
        <aside class="rightbar no-print">
      <div class="panel-head">Auswertung</div>
      <div class="tabs">
        <button class="tab-btn active" data-tab="overview">Übersicht</button>
        <button class="tab-btn" data-tab="history">Historie</button>
        <button class="tab-btn" data-tab="data">Daten</button>
        <button class="tab-btn" data-tab="media">Medien</button>
      </div>
      <div class="right-content" id="rightContent"></div>
    </aside>
  </div>
</div>

<script>
 
 

const MARKE = { gruen:'#74b91f', blau:'#16324f', rand:'#dbe4ee' };

const SCHRITTE = [
  { id:'start', titel:'Projektstart', untertitel:'Kontakt, Interesse und Objektstart' },
  { id:'profil', titel:'Gebäudeprofil', untertitel:'Grunddaten des Objekts' },
  { id:'huelle', titel:'Gebäudehülle', untertitel:'Bauteile, U-Werte & Sanierung' },
  { id:'pv', titel:'Dach & PV-Potenzial', untertitel:'Flächen, Montage & Verschattung' },
  { id:'wp', titel:'Bestandsheizung & WP', untertitel:'Technik, Hydraulik & Heizlast' },
  { id:'netz', titel:'Elektro & Netz', untertitel:'Zähler, VDE & Absicherung' },
  { id:'emob', titel:'E-Mobilität', untertitel:'Fahrzeuge, Wallbox & Ladeleistung' },
  { id:'abschluss', titel:'Abschluss', untertitel:'Unterlagen & Freigabe' }
];

const VERBRAUCHS_LOGIK = {
  wpDivisor: 3.3,
  eautoKwhJe100Km: 20,
  pvUndSpeicherFaktor: 1.25,
  pvKwProQmNetto: 0.25,
  wpFaktorWaermeZuLeistung: 0.9,
  wpVolllaststunden: 2100,
  heizFaktoren: {
    'kWh Wärme': 1,
    'kWh Strom direkt': 1,
    'm³ Erdgas H': 11.1,
    'm³ Erdgas L': 8.9,
    'Liter Heizöl EL': 10.0,
    'kg Pellets': 4.9,
    'kg Flüssiggas': 12.8,
    'kg Scheitholz lufttrocken': 4.0
  }
};

const FIELD_CONFIG = [
  ['anrede','vorname','nachname','email','telefon_mobil','strasse','hausnummer','plz','ort','zielPV','zielWP','zielSpeicher','zielWallbox','zielHeizlast','zielFenster','zielTueren','zielBad','zielKueche','hhStrom','heizRoh','heizEinheit','strompreis','einspeiseverguetung','heizpreis'],
  ['gebTyp','firmenname','baujahr','wohneinheiten','geschosse','wohnflaeche','bauzustand','nutzung'],
  ['mauerwerk','mauerwerk_dicke','fassadendaemmung_art','fassadendaemmung_staerke','dach_daemmung','dach_daemmung_staerke','kellerdaemmung','fensterverglasung','fensterrahmen','lueftung','gebaeude_laenge','gebaeude_breite','fassaden_hoehe','fensterflaeche_gesamt'],
  ['ROOFS','traufhoehe','kabelweg_dc','bestandspv','speicherwunsch','notstrom','invest_pv'],
  ['heiztechnik','alt_heizleistung','waermeverteilung','vorlauf','ww_speicher','ww_speicher_liter','platzAussen','rohrmaterial','zirkulation','wp_erdarbeiten','wp_leitung_laenge','ROOMS','invest_wp','wp_foerderung'],
  ['zaehler_massnahme','zaehler_groesse','sls_schalter','apz_feld','ac_ueberspannung','paragraf_14a','anzahl_zaehler','netzreserve','technik_standort','netzwerk'],
  ['elektroauto','fahrleistung','anzahl_fahrzeuge','wallboxen','ladeleistung','ladeort','zugangskontrolle','starkstrom','leitungsweg','erdarbeiten','bidirektional'],
  ['MEDIA','chk_rechnungen','chk_dachbilder','chk_zaehlerbilder','chk_fensterbilder','chk_heizungsbilder','chk_fassadenbilder','chk_vorort','chk_angebotsreif','abschluss_notiz','signature']
];

function getOrderedSteps() {
  const d = state.data || {};
  const steps = [];

  const hasAnyProduct =
    d.zielPV || d.zielWP || d.zielSpeicher || d.zielWallbox ||
    d.zielHeizlast || d.zielFenster || d.zielTueren ||
    d.zielBad || d.zielKueche;

  // If nothing selected: only Projektstart
  if (!hasAnyProduct) {
    return [0];
  }

  // Start always first
  steps.push(0);

  // WP / Heizlast / Fenster / Türen / Bad / Küche need building data
  if (d.zielWP || d.zielHeizlast || d.zielFenster || d.zielTueren || d.zielBad || d.zielKueche) {
    steps.push(1, 2, 4);
  }

  // PV
  if (d.zielPV) {
    steps.push(3, 5);
  }

  // Speicher usually depends on PV/electrical
  if (d.zielSpeicher) {
    if (!steps.includes(3)) steps.push(3);
    steps.push(5);
  }

  // Wallbox
  if (d.zielWallbox) {
    steps.push(5, 6);
  }

  // remove duplicates, keep order
  const unique = [];
  steps.forEach(step => {
    if (!unique.includes(step)) unique.push(step);
  });

  // Abschluss only when at least one product selected
  unique.push(7);

  return unique;
}

function getVisibleSteps() {
  const ordered = getOrderedSteps();
  return ordered.map(index => ({
    ...SCHRITTE[index],
    originalIndex: index
  }));
}

function ensureValidActiveStep() {
  const ordered = getOrderedSteps();
  if (!ordered.includes(state.activeStep)) {
    state.activeStep = ordered[0];
  }
}


function getCurrentOrderedIndex() {
  return getOrderedSteps().indexOf(state.activeStep);
}

let state = {
  activeStep: 0,
  rightTab: 'overview',
  data: {
    strompreis:'0.35',
    einspeiseverguetung:'0.08',
    heizpreis:'0.11',
    wp_foerderung:'55'
  },
  roofs: [],
  rooms: [],
  media: [],
  history: []
};

function getZeit(){
  return new Date().toLocaleString('de-DE');
}

function logHistory(text){
  state.history.unshift({ text, zeit:getZeit() });
  state.history = state.history.slice(0, 80);
}

function saveDraft(){
  localStorage.setItem('fachwizard-html-draft', JSON.stringify({
    data: state.data,
    roofs: state.roofs,
    rooms: state.rooms,
    media: state.media.filter(m => m.type !== 'imageRaw'),
    history: state.history
  }));
}

function loadDraft(){
  const raw = localStorage.getItem('fachwizard-html-draft');
  if(!raw) return;
  try{
    const parsed = JSON.parse(raw);
    state.data = parsed.data || state.data;
    state.roofs = parsed.roofs || [];
    state.rooms = parsed.rooms || [];
    state.media = parsed.media || [];
    state.history = parsed.history || [];
  }catch(e){
    console.error(e);
  }
}

function num(v){
  return parseFloat(String(v || '').replace(',', '.')) || 0;
}

function calcData(){
  const hhStrom = num(state.data.hhStrom);
  const heizRoh = num(state.data.heizRoh);
  const fahrleistung = num(state.data.fahrleistung);
  const faktor = VERBRAUCHS_LOGIK.heizFaktoren[state.data.heizEinheit] || 1;
  const heatKwh = heizRoh * faktor;

  let jaz = VERBRAUCHS_LOGIK.wpDivisor;
  if(state.data.waermeverteilung === 'Fußbodenheizung') jaz = 4.2;
  if(state.data.waermeverteilung === 'Heizkörper') jaz = 2.8;

  const wpStrom = heatKwh > 0 ? heatKwh / jaz : 0;
  const eautoStrom = (fahrleistung / 100) * VERBRAUCHS_LOGIK.eautoKwhJe100Km;
  const gesamtStrom = hhStrom + wpStrom + eautoStrom;

  const pvExact = (gesamtStrom * VERBRAUCHS_LOGIK.pvUndSpeicherFaktor) / 1000;
  const batExact = pvExact;

  let pvKwpGesamt = 0;
  let pvErtragGesamt = 0;
  let flaecheGesamt = 0;

  state.roofs.forEach(r => {
    const fl = num(r.flaeche);
    flaecheGesamt += fl;
    const kwp = fl * VERBRAUCHS_LOGIK.pvKwProQmNetto;
    pvKwpGesamt += kwp;

    let spezifisch = 950;
    if(r.ausrichtung === 'Süd') spezifisch = 1000;
    if(r.ausrichtung === 'Ost' || r.ausrichtung === 'West') spezifisch = 850;
    if(r.ausrichtung === 'Nord') spezifisch = 600;

    let loss = 1;
    if(r.verschattung === 'Gering') loss = .95;
    if(r.verschattung === 'Mittel') loss = .85;
    if(r.verschattung === 'Hoch') loss = .70;

    pvErtragGesamt += kwp * spezifisch * loss;
  });

  const heizlastGesamtKW = state.rooms.reduce((sum, r) => {
    const fl = num(r.raumflaeche);
    const h = num(r.raumhoehe || 2.5);
    const aw = num(r.aussenwaende || 1);
    const innen = num(r.innen_temp || 20);
    const nat = -10;
    const dt = innen - nat;
    const wand = aw * Math.sqrt(fl || 1) * h;
    const qt = wand * 1.0 * dt;
    const qv = (fl * h) * 0.5 * 0.34 * dt;
    return sum + ((qt + qv) / 1000);
  }, 0);

  const strompreis = num(state.data.strompreis || 0.35);
  const einspeise = num(state.data.einspeiseverguetung || 0.08);
  const heizpreis = num(state.data.heizpreis || 0.11);
  const investPV = num(state.data.invest_pv || pvKwpGesamt * 1600);
  const investWP = num(state.data.invest_wp || (heizlastGesamtKW > 0 ? 28000 : 0));
  const foerder = num(state.data.wp_foerderung || 0);

  const kostenAlt = (hhStrom * strompreis) + (heatKwh * heizpreis);
  const moeglicherEigenverbrauch = Math.min(gesamtStrom, pvErtragGesamt * 0.35);
  const netzbezug = Math.max(0, gesamtStrom - moeglicherEigenverbrauch);
  const einspeisung = Math.max(0, pvErtragGesamt - moeglicherEigenverbrauch);
  const kostenNeu = (netzbezug * strompreis) - (einspeisung * einspeise);
  const ersparnisPa = kostenAlt - kostenNeu;
  const investGesamt = investPV + (investWP * (1 - foerder / 100));
  const amortisation = ersparnisPa > 0 ? (investGesamt / ersparnisPa) : 0;

  const eigenverbrauchQuote = pvErtragGesamt > 0 ? Math.min(100, Math.round((moeglicherEigenverbrauch / pvErtragGesamt) * 100)) : 0;
  const autarkieQuote = gesamtStrom > 0 ? Math.min(100, Math.round((moeglicherEigenverbrauch / gesamtStrom) * 100)) : 0;

  return {
    heatKwh: Math.round(heatKwh),
    wpStrom: Math.round(wpStrom),
    eautoStrom: Math.round(eautoStrom),
    gesamtStrom: Math.round(gesamtStrom),
    pvExact,
    batExact,
    pvKwpGesamt,
    pvErtragGesamt: Math.round(pvErtragGesamt),
    flaecheGesamt,
    heizlastGesamtKW,
    kostenAlt,
    kostenNeu,
    ersparnisPa,
    investGesamt,
    amortisation,
    eigenverbrauchQuote,
    autarkieQuote
  };
}

function globalProgress(){
  let total = 0;
  let filled = 0;

  FIELD_CONFIG.forEach(group => {
    group.forEach(f => {
      total++;
      if(f === 'ROOFS' && state.roofs.length > 0) filled++;
      else if(f === 'ROOMS' && state.rooms.length > 0) filled++;
      else if(f === 'MEDIA' && state.media.length > 0) filled++;
      else if(state.data[f] !== undefined && state.data[f] !== '' && state.data[f] !== false) filled++;
    });
  });

  return {
    total,
    filled,
    percent: total ? Math.round((filled / total) * 100) : 0
  };
}

function stepProgress(index){
  const fields = FIELD_CONFIG[index];
  let total = fields.length;
  let filled = 0;
  fields.forEach(f => {
    if(f === 'ROOFS' && state.roofs.length > 0) filled++;
    else if(f === 'ROOMS' && state.rooms.length > 0) filled++;
    else if(f === 'MEDIA' && state.media.length > 0) filled++;
    else if(state.data[f] !== undefined && state.data[f] !== '' && state.data[f] !== false) filled++;
  });
  return { total, filled, percent: total ? Math.round((filled/total)*100) : 0 };
}

function updateField(name, value){
  state.data[name] = value;

  if(name === 'heizEinheit'){
    if(String(value).includes('Heizöl')) state.data.heiztechnik = 'Öl';
    else if(String(value).includes('Erdgas') || String(value).includes('Flüssiggas')) state.data.heiztechnik = 'Gas';
    else if(String(value).includes('Pellets') || String(value).includes('Scheitholz')) state.data.heiztechnik = 'Pellets';
  }

  if(name === 'waermeverteilung' && !state.data.vorlauf){
    if(value === 'Fußbodenheizung') state.data.vorlauf = '35';
    if(value === 'Heizkörper') state.data.vorlauf = '55';
  }

  if(name === 'elektroauto' && (value === 'Ja' || value === 'Geplant') && !state.data.wallboxen){
    state.data.wallboxen = '1';
  }

  // when all interests are removed, force back to start
  ensureValidActiveStep();

  logHistory(name + ' → ' + value);
  saveDraft();
  render();
}

function field(label, name, type='text', options=null, placeholder=''){
  const value = state.data[name] || '';
  if(type === 'select'){
    return `
      <div class="field">
        <label>${label}</label>
        <select data-field="${name}">
          <option value="">Bitte wählen...</option>
          ${options.map(o => `<option value="${o}" ${value === o ? 'selected' : ''}>${o}</option>`).join('')}
        </select>
      </div>
    `;
  }

  if(type === 'textarea'){
    return `
      <div class="field">
        <label>${label}</label>
        <textarea data-field="${name}" placeholder="${placeholder}">${value}</textarea>
      </div>
    `;
  }

  return `
    <div class="field">
      <label>${label}</label>
      <input type="${type}" data-field="${name}" value="${value}" placeholder="${placeholder}">
    </div>
  `;
}

function yesNoChip(name, values){
  const current = state.data[name] || '';
  return `
    <div class="chips">
      ${values.map(v => `<button type="button" class="chip ${current === v ? 'active' : ''}" data-chip="${name}" data-value="${v}">${v}</button>`).join('')}
    </div>
  `;
}

function renderStepList(){
  const holder = document.getElementById('stepList');
  const visibleSteps = getVisibleSteps();

  holder.innerHTML = visibleSteps.map((s, visibleIndex) => {
    const sp = stepProgress(s.originalIndex);
    return `
      <button class="step-btn ${state.activeStep === s.originalIndex ? 'active' : ''}" data-step="${s.originalIndex}">
        <div class="step-title">${visibleIndex + 1}. ${s.titel}</div>
        <div class="step-sub">${s.untertitel}</div>
        <div class="progress"><span style="width:${sp.percent}%"></span></div>
      </button>
    `;
  }).join('');
}
function renderWarnings(){
  const box = document.getElementById('warningsBox');
  const c = calcData();
  let warnings = [];

  if(state.data.wohnflaeche && c.heatKwh > 0){
    const qm = num(state.data.wohnflaeche);
    if(qm > 0 && (c.heatKwh / qm) > 250){
      warnings.push('Kritischer Verbrauch: mehr als 250 kWh/m²a. Bitte Heizungsdaten oder Fläche prüfen.');
    }
  }

  if(state.data.zielWP && num(state.data.vorlauf) > 55){
    warnings.push('Wärmepumpe: Vorlauftemperatur über 55°C ist oft kritisch.');
  }

  if(state.roofs.some(r => r.ausrichtung === 'Nord' && num(r.neigung) >= 30)){
    warnings.push('PV-Planung: Norddach mit hoher Neigung ist meist wirtschaftlich schwächer.');
  }

  if(!warnings.length){
    box.innerHTML = '';
    return;
  }

  box.innerHTML = warnings.map(w => `<div class="notice">${w}</div>`).join('');
}

function renderStepContent(){
  const s = state.activeStep;
  const c = calcData();
  const target = document.getElementById('stepContent');

  if(s === 0){
    target.innerHTML = `
      <div class="card">
        <h3>Kontaktperson</h3>
        <div class="meta">Kundendaten aufnehmen</div>
        ${yesNoChip('anrede', ['Frau','Herr','Divers'])}
        <div class="grid grid-4" style="margin-top:16px">
          ${field('Vorname','vorname')}
          ${field('Nachname','nachname')}
          ${field('E-Mail','email','email')}
          ${field('Telefon / Mobil','telefon_mobil')}
        </div>
      </div>

      <div class="card">
        <h3>Objektstandort</h3>
        <div class="meta">Adresse und Objekt</div>
        <div class="grid grid-4">
          ${field('Straße','strasse')}
          ${field('Hausnummer','hausnummer')}
          ${field('PLZ','plz')}
          ${field('Ort','ort')}
        </div>
      </div>

      <div class="card">
        <h3>Verbrauch & Preise</h3>
        <div class="meta">Grunddaten für erste Berechnung</div>
        <div class="grid grid-3">
          ${field('Haushaltsstrom (kWh/a)','hhStrom','number')}
          ${field('Heizungsverbrauch','heizRoh','number')}
          ${field('Heizeinheit','heizEinheit','select', Object.keys(VERBRAUCHS_LOGIK.heizFaktoren))}
        </div>
        <div class="grid grid-3" style="margin-top:14px">
          ${field('Strompreis (€/kWh)','strompreis','number')}
          ${field('Einspeisevergütung (€/kWh)','einspeiseverguetung','number')}
          ${field('Heizpreis (€/kWh)','heizpreis','number')}
        </div>

        <div class="stats">
          <div class="stat"><small>kWh Wärme</small><strong>${c.heatKwh}</strong></div>
          <div class="stat"><small>WP-Strom</small><strong>${c.wpStrom}</strong></div>
          <div class="stat"><small>Gesamtstrom</small><strong>${c.gesamtStrom}</strong></div>
          <div class="stat"><small>PV-Vorschlag</small><strong>${c.pvExact.toFixed(1)} kWp</strong></div>
        </div>
      </div>
    `;
  }

  if(s === 1){
    target.innerHTML = `
      <div class="card">
        <h3>Gebäudeprofil</h3>
        <div class="meta">Grunddaten des Objekts</div>
        <div class="grid grid-4">
          ${field('Gebäudetyp','gebTyp','select',['Einfamilienhaus','Mehrfamilienhaus','Gewerbe'])}
          ${field('Firmenname','firmenname')}
          ${field('Baujahr','baujahr','number')}
          ${field('Wohneinheiten','wohneinheiten','number')}
          ${field('Geschosse','geschosse','number')}
          ${field('Wohnfläche (m²)','wohnflaeche','number')}
          ${field('Bauzustand','bauzustand','select',['Bestand','Teilmodernisiert','Kernsaniert','Neubau'])}
          ${field('Nutzungsform','nutzung','select',['Eigennutzung','Vermietung','Gemischt'])}
        </div>
      </div>
    `;
  }

  if(s === 2){
    target.innerHTML = `
      <div class="card">
        <h3>Gebäudehülle</h3>
        <div class="meta">Bauteile und einfache Hüllendaten</div>
        <div class="grid grid-4">
          ${field('Mauerwerk','mauerwerk','select',['Poroton','Vollstein','Beton','Holzbau','Unklar'])}
          ${field('Mauerwerk Dicke (cm)','mauerwerk_dicke','number')}
          ${field('Fassadendämmung','fassadendaemmung_art','select',['Keine / Ungedämmt','EPS / Styropor','Steinwolle','Holzfaser','PIR/PUR','Unklar'])}
          ${field('Fassadendämmung Stärke (cm)','fassadendaemmung_staerke','number')}
          ${field('Dachdämmung','dach_daemmung','select',['Keine / Ungedämmt','Glaswolle/Klemmfilz','Aufsparrendämmung (PUR)','Holzfaser','Unklar'])}
          ${field('Dachdämmung Stärke (cm)','dach_daemmung_staerke','number')}
          ${field('Kellerdämmung','kellerdaemmung','select',['Keine / Ungedämmt','XPS (Perimeter)','EPS','Unklar'])}
          ${field('Fensterverglasung','fensterverglasung','select',['1-fach','2-fach','3-fach'])}
          ${field('Fensterrahmen','fensterrahmen','select',['Kunststoff','Holz','Alu','Holz-Alu'])}
          ${field('Lüftung','lueftung','select',['Fensterlüftung','Zuluft / Abluft','Lüftungsanlage mit WRG'])}
          ${field('Gebäudelänge (m)','gebaeude_laenge','number')}
          ${field('Gebäudebreite (m)','gebaeude_breite','number')}
          ${field('Fassadenhöhe (m)','fassaden_hoehe','number')}
          ${field('Fensterfläche gesamt (m²)','fensterflaeche_gesamt','number')}
        </div>
      </div>
    `;
  }

  if(s === 3){
    target.innerHTML = `
      <div class="card">
        <h3>Dachflächen</h3>
        <div class="meta">Einfache Dach-Erfassung für PV</div>
        <button class="btn btn-primary no-print" id="addRoofBtn">Dachfläche hinzufügen</button>
        <div class="list" id="roofList" style="margin-top:16px"></div>
      </div>

      <div class="card">
        <h3>PV Zusatzdaten</h3>
        <div class="meta">Montage und Zielwerte</div>
        <div class="grid grid-3">
          ${field('Traufhöhe (m)','traufhoehe','number')}
          ${field('Kabelweg DC','kabelweg_dc','select',['Freier Schacht vorhanden','Außen an Fassade','Muss gebohrt werden','Unklar'])}
          ${field('Bestands-PV','bestandspv','select',['Keine','Vorhanden','Erweiterung'])}
          ${field('Speicherwunsch','speicherwunsch','select',['Kein Speicher','Optional','Fest gewünscht'])}
          ${field('Notstrom','notstrom','select',['Nicht relevant','Optional','Fest gewünscht'])}
          ${field('Investition PV (€)','invest_pv','number')}
        </div>

        <div class="stats">
          <div class="stat"><small>Dachfläche</small><strong>${c.flaecheGesamt.toFixed(1)} m²</strong></div>
          <div class="stat"><small>PV kWp</small><strong>${c.pvKwpGesamt.toFixed(1)}</strong></div>
          <div class="stat"><small>PV Ertrag</small><strong>${c.pvErtragGesamt} kWh</strong></div>
          <div class="stat"><small>Speicher</small><strong>${c.batExact.toFixed(1)} kWh</strong></div>
        </div>
      </div>
    `;
  }
    if(s === 4){
    target.innerHTML = `
      <div class="card">
        <h3>Bestandsheizung</h3>
        <div class="meta">Heizung, Wärmeverteilung und Wärmepumpe</div>
        <div class="grid grid-4">
          ${field('Heiztechnik','heiztechnik','select',['Gas','Öl','Pellets','Stromdirekt','Wärmepumpe'])}
          ${field('Alt-Heizleistung (kW)','alt_heizleistung','number')}
          ${field('Wärmeverteilung','waermeverteilung','select',['Fußbodenheizung','Heizkörper','Gemischt'])}
          ${field('Vorlauf (°C)','vorlauf','number')}
          ${field('WW-Speicher','ww_speicher','select',['Ja','Nein (Durchlauferhitzer/Kombi)','Unklar'])}
          ${field('WW-Speicher Liter','ww_speicher_liter','number')}
          ${field('Aufstellort Außen','platzAussen','select',['Ja, Garten/Hof','Nein','Unklar'])}
          ${field('Rohrmaterial','rohrmaterial','select',['Kupfer','Kunststoff / MSVR','Edelstahl','C-Stahl'])}
          ${field('Zirkulation','zirkulation','select',['Ja','Nein','Unklar'])}
          ${field('Erdarbeiten nötig','wp_erdarbeiten','select',['Ja','Nein'])}
          ${field('Leitungslänge (m)','wp_leitung_laenge','number')}
          ${field('Investition WP (€)','invest_wp','number')}
          ${field('Förderung WP (%)','wp_foerderung','number')}
        </div>
      </div>

      <div class="card">
        <h3>Räume</h3>
        <div class="meta">Vereinfachte Heizlast-Erfassung</div>
        <button class="btn btn-primary no-print" id="addRoomBtn">Raum hinzufügen</button>
        <div class="list" id="roomList" style="margin-top:16px"></div>

        <div class="stats">
          <div class="stat"><small>Heizlast</small><strong>${c.heizlastGesamtKW.toFixed(2)} kW</strong></div>
          <div class="stat"><small>Kosten alt</small><strong>${Math.round(c.kostenAlt)} €</strong></div>
          <div class="stat"><small>Kosten neu</small><strong>${Math.round(c.kostenNeu)} €</strong></div>
          <div class="stat"><small>Amortisation</small><strong>${c.amortisation ? c.amortisation.toFixed(1) : '–'} J</strong></div>
        </div>
      </div>
    `;
  }

  if(s === 5){
    target.innerHTML = `
      <div class="card">
        <h3>Elektro & Netz</h3>
        <div class="meta">Zählerschrank und Netzsituation</div>
        <div class="grid grid-4">
          ${field('Maßnahme Zählerschrank','zaehler_massnahme','select',['Belassen','Ertüchtigung','Neu'])}
          ${field('Größe Zählerschrank','zaehler_groesse','select',['800x1100 (Standard)','1100x1100 (Groß)'])}
          ${field('SLS-Schalter','sls_schalter','select',['Nicht vorhanden','35A','50A','63A','Unklar'])}
          ${field('APZ Feld','apz_feld','select',['Ja','Nein'])}
          ${field('AC Überspannungsschutz','ac_ueberspannung','select',['Vorhanden','Muss nachgerüstet werden'])}
          ${field('§14a EnWG','paragraf_14a','select',['Ja, vorbereitet','Nein, muss nachgerüstet werden'])}
          ${field('Anzahl Zähler','anzahl_zaehler','number')}
          ${field('Netzreserve','netzreserve','select',['Ausreichend','Prüfen','Kritisch'])}
          ${field('Technik Standort','technik_standort','select',['Keller','Erdgeschoss','Garage','Sonstiges'])}
          ${field('Netzwerk','netzwerk','select',['Vorhanden','Nicht vorhanden','Geplant'])}
        </div>
      </div>
    `;
  }

  if(s === 6){
    target.innerHTML = `
      <div class="card">
        <h3>E-Mobilität</h3>
        <div class="meta">Fahrzeuge, Wallbox und Ladeplatz</div>
        ${yesNoChip('elektroauto', ['Ja','Nein','Geplant'])}
        <div class="grid grid-4" style="margin-top:16px">
          ${field('Fahrleistung km/a','fahrleistung','number')}
          ${field('Anzahl Fahrzeuge','anzahl_fahrzeuge','number')}
          ${field('Wallboxen','wallboxen','number')}
          ${field('Ladeleistung','ladeleistung','select',['11 kW (Standard)','22 kW (Genehmigungspflichtig)'])}
          ${field('Ladeort','ladeort','select',['Garage','Carport','Außenwand','Stellplatz (Frei)'])}
          ${field('Zugangskontrolle','zugangskontrolle','select',['Ja, gewünscht','Nein, frei zugänglich'])}
          ${field('Starkstrom','starkstrom','select',['Ja','Nein','Unklar'])}
          ${field('Leitungsweg','leitungsweg')}
        </div>

        <div class="chips" style="margin-top:16px">
          <button type="button" class="chip ${state.data.erdarbeiten ? 'active' : ''}" data-bool="erdarbeiten">Erdarbeiten erforderlich</button>
          <button type="button" class="chip ${state.data.bidirektional ? 'active' : ''}" data-bool="bidirektional">Bidirektionales Laden</button>
        </div>
      </div>
    `;
  }

  if(s === 7){
    target.innerHTML = `
      <div class="card">
        <h3>Unterlagen & Checkliste</h3>
        <div class="meta">Abschlussdaten und Freigabe</div>

        <div class="chips">
          <button type="button" class="chip ${state.data.chk_rechnungen ? 'active' : ''}" data-bool="chk_rechnungen">Rechnungen</button>
          <button type="button" class="chip ${state.data.chk_dachbilder ? 'active' : ''}" data-bool="chk_dachbilder">Dachbilder</button>
          <button type="button" class="chip ${state.data.chk_zaehlerbilder ? 'active' : ''}" data-bool="chk_zaehlerbilder">Zählerbilder</button>
          <button type="button" class="chip ${state.data.chk_fensterbilder ? 'active' : ''}" data-bool="chk_fensterbilder">Fensterbilder</button>
          <button type="button" class="chip ${state.data.chk_heizungsbilder ? 'active' : ''}" data-bool="chk_heizungsbilder">Heizungsbilder</button>
          <button type="button" class="chip ${state.data.chk_fassadenbilder ? 'active' : ''}" data-bool="chk_fassadenbilder">Fassadenbilder</button>
          <button type="button" class="chip ${state.data.chk_vorort ? 'active' : ''}" data-bool="chk_vorort">Vor-Ort-Termin</button>
          <button type="button" class="chip ${state.data.chk_angebotsreif ? 'active' : ''}" data-bool="chk_angebotsreif">Planungsbereit</button>
        </div>

        <div style="margin-top:16px">
          ${field('Abschlussbemerkung','abschluss_notiz','textarea',null,'Zusätzliche Hinweise...')}
        </div>
      </div>

      <div class="card">
        <h3>Medien</h3>
        <div class="meta">Bilder und Dateien</div>
        <input type="file" id="mediaInput" multiple class="no-print">
        <div id="mediaManager" style="margin-top:16px"></div>
      </div>

      <div class="card">
        <h3>Unterschrift</h3>
        <div class="meta">Kundenbestätigung</div>
        <div class="signature-wrap">
          <canvas id="signatureCanvas"></canvas>
          <div class="row-actions no-print">
            <button class="btn btn-light" id="clearSignatureBtn">Löschen</button>
            <button class="btn btn-green" id="saveSignatureBtn">Unterschrift speichern</button>
          </div>
          <div class="muted" style="margin-top:8px">Mit der Unterschrift bestätigt der Kunde die Richtigkeit der erfassten Daten.</div>
        </div>
      </div>

      <div class="card no-print">
        <h3>Export</h3>
        <div class="meta">Drucken oder als PDF speichern</div>
        <button class="btn btn-primary" id="printBtn">Drucken / PDF</button>
      </div>
    `;
  }

  bindDynamicInputs();
  renderRoofList();
  renderRoomList();
  renderMediaManager();
  initSignatureIfNeeded();
}

function renderRoofList(){
  const list = document.getElementById('roofList');
  if(!list) return;

  if(!state.roofs.length){
    list.innerHTML = `<div class="row-card muted">Noch keine Dachfläche erfasst.</div>`;
    return;
  }

  list.innerHTML = state.roofs.map((r, i) => `
    <div class="row-card">
      <h4>${r.name || ('Dachfläche ' + (i+1))}</h4>
      <div class="grid grid-4">
        <div class="field"><label>Name</label><input data-roof="${i}" data-key="name" value="${r.name || ''}"></div>
        <div class="field"><label>Ausrichtung</label>
          <select data-roof="${i}" data-key="ausrichtung">
            ${['Süd','Ost','West','Nord','Süd-Ost','Süd-West'].map(v => `<option value="${v}" ${r.ausrichtung === v ? 'selected' : ''}>${v}</option>`).join('')}
          </select>
        </div>
        <div class="field"><label>Neigung</label><input type="number" data-roof="${i}" data-key="neigung" value="${r.neigung || ''}"></div>
        <div class="field"><label>Fläche (m²)</label><input type="number" data-roof="${i}" data-key="flaeche" value="${r.flaeche || ''}"></div>
        <div class="field"><label>Verschattung</label>
          <select data-roof="${i}" data-key="verschattung">
            ${['Keine','Gering','Mittel','Hoch'].map(v => `<option value="${v}" ${r.verschattung === v ? 'selected' : ''}>${v}</option>`).join('')}
          </select>
        </div>
      </div>
      <div class="row-actions">
        <button class="btn btn-danger" data-del-roof="${i}">Löschen</button>
      </div>
    </div>
  `).join('');
}

function renderRoomList(){
  const list = document.getElementById('roomList');
  if(!list) return;

  if(!state.rooms.length){
    list.innerHTML = `<div class="row-card muted">Noch keine Räume erfasst.</div>`;
    return;
  }

  list.innerHTML = state.rooms.map((r, i) => `
    <div class="row-card">
      <h4>${r.raum || ('Raum ' + (i+1))}</h4>
      <div class="grid grid-4">
        <div class="field"><label>Wohnung / Etage</label><input data-room="${i}" data-key="wohnung" value="${r.wohnung || ''}"></div>
        <div class="field"><label>Raum</label><input data-room="${i}" data-key="raum" value="${r.raum || ''}"></div>
        <div class="field"><label>Raumfläche (m²)</label><input type="number" data-room="${i}" data-key="raumflaeche" value="${r.raumflaeche || ''}"></div>
        <div class="field"><label>Raumhöhe (m)</label><input type="number" data-room="${i}" data-key="raumhoehe" value="${r.raumhoehe || '2.5'}"></div>
        <div class="field"><label>Innen-Temp</label><input type="number" data-room="${i}" data-key="innen_temp" value="${r.innen_temp || '20'}"></div>
        <div class="field"><label>Außenwände</label><input type="number" data-room="${i}" data-key="aussenwaende" value="${r.aussenwaende || '1'}"></div>
      </div>
      <div class="row-actions">
        <button class="btn btn-danger" data-del-room="${i}">Löschen</button>
      </div>
    </div>
  `).join('');
}

function renderMediaManager(){
  const holder = document.getElementById('mediaManager');
  if(!holder) return;

  if(!state.media.length){
    holder.innerHTML = `<div class="muted">Noch keine Dateien hochgeladen.</div>`;
    return;
  }

  holder.innerHTML = `
    <div class="media-grid">
      ${state.media.map((m, i) => `
        <div class="media-item">
          ${m.type === 'image' ? `<img src="${m.url}" alt="${m.name}">` : `<div style="padding:30px;text-align:center;font-weight:700;">Dokument</div>`}
          <div class="cap">${m.name}</div>
          <div class="row-actions" style="padding:0 8px 8px;">
            <button class="btn btn-danger" data-del-media="${i}" style="padding:8px 10px;font-size:12px;">Löschen</button>
          </div>
        </div>
      `).join('')}
    </div>
  `;
}

function renderRightBar(){
  const c = calcData();
  const box = document.getElementById('rightContent');

  if(state.rightTab === 'overview'){
    box.innerHTML = `
      <div class="card">
        <h3>Live Übersicht</h3>
        <div class="meta">Wichtige Kennzahlen</div>
        <div class="stats" style="grid-template-columns:1fr 1fr;">
          <div class="stat"><small>Gesamtstrom</small><strong>${c.gesamtStrom} kWh</strong></div>
          <div class="stat"><small>PV</small><strong>${c.pvKwpGesamt.toFixed(1)} kWp</strong></div>
          <div class="stat"><small>Heizlast</small><strong>${c.heizlastGesamtKW.toFixed(2)} kW</strong></div>
          <div class="stat"><small>Ertrag</small><strong>${c.pvErtragGesamt} kWh</strong></div>
          <div class="stat"><small>Eigenverbrauch</small><strong>${c.eigenverbrauchQuote}%</strong></div>
          <div class="stat"><small>Autarkie</small><strong>${c.autarkieQuote}%</strong></div>
        </div>
      </div>
    `;
  }

  if(state.rightTab === 'history'){
    box.innerHTML = `
      <div class="card">
        <h3>Historie</h3>
        <div class="meta">Letzte Änderungen</div>
        ${state.history.length ? state.history.map(h => `
          <div class="history-item">
            <div>${h.text}</div>
            <div class="muted">${h.zeit}</div>
          </div>
        `).join('') : `<div class="muted">Noch keine Einträge.</div>`}
      </div>
    `;
  }

  if(state.rightTab === 'data'){
    box.innerHTML = `
      <div class="card">
        <h3>Datenexport</h3>
        <div class="meta">Alle aktuellen Daten als JSON</div>
        <textarea style="width:100%;min-height:380px;">${JSON.stringify({
          data: state.data,
          roofs: state.roofs,
          rooms: state.rooms
        }, null, 2)}</textarea>
      </div>
    `;
  }

  if(state.rightTab === 'media'){
    box.innerHTML = `
      <div class="card">
        <h3>Medien</h3>
        <div class="meta">${state.media.length} Datei(en)</div>
        ${state.media.length ? state.media.map(m => `<div class="history-item">${m.name}</div>`).join('') : `<div class="muted">Keine Medien vorhanden.</div>`}
      </div>
    `;
  }
}

function bindDynamicInputs(){
  document.querySelectorAll('[data-field]').forEach(el => {
    const event = el.tagName === 'TEXTAREA' || el.tagName === 'INPUT' ? 'input' : 'change';
    el.addEventListener(event, e => updateField(e.target.dataset.field, e.target.value));
  });

  document.querySelectorAll('[data-chip]').forEach(btn => {
    btn.addEventListener('click', () => updateField(btn.dataset.chip, btn.dataset.value));
  });

  document.querySelectorAll('[data-bool]').forEach(btn => {
    btn.addEventListener('click', () => updateField(btn.dataset.bool, !state.data[btn.dataset.bool]));
  });

  const addRoofBtn = document.getElementById('addRoofBtn');
  if(addRoofBtn){
    addRoofBtn.addEventListener('click', () => {
      state.roofs.push({
        name:'Dachfläche ' + (state.roofs.length + 1),
        ausrichtung:'Süd',
        neigung:'35',
        flaeche:'',
        verschattung:'Keine'
      });
      logHistory('Dachfläche hinzugefügt');
      saveDraft();
      render();
    });
  }

  const addRoomBtn = document.getElementById('addRoomBtn');
  if(addRoomBtn){
    addRoomBtn.addEventListener('click', () => {
      state.rooms.push({
        wohnung:'EG',
        raum:'Wohnzimmer',
        raumflaeche:'',
        raumhoehe:'2.5',
        innen_temp:'20',
        aussenwaende:'1'
      });
      logHistory('Raum hinzugefügt');
      saveDraft();
      render();
    });
  }

  document.querySelectorAll('[data-roof]').forEach(el => {
    el.addEventListener('input', e => {
      const i = +e.target.dataset.roof;
      const key = e.target.dataset.key;
      state.roofs[i][key] = e.target.value;
      saveDraft();
      renderTop();
    });
    el.addEventListener('change', e => {
      const i = +e.target.dataset.roof;
      const key = e.target.dataset.key;
      state.roofs[i][key] = e.target.value;
      saveDraft();
      render();
    });
  });

  document.querySelectorAll('[data-room]').forEach(el => {
    el.addEventListener('input', e => {
      const i = +e.target.dataset.room;
      const key = e.target.dataset.key;
      state.rooms[i][key] = e.target.value;
      saveDraft();
      renderTop();
    });
    el.addEventListener('change', e => {
      const i = +e.target.dataset.room;
      const key = e.target.dataset.key;
      state.rooms[i][key] = e.target.value;
      saveDraft();
      render();
    });
  });

  document.querySelectorAll('[data-del-roof]').forEach(btn => {
    btn.addEventListener('click', () => {
      state.roofs.splice(+btn.dataset.delRoof, 1);
      logHistory('Dachfläche gelöscht');
      saveDraft();
      render();
    });
  });

  document.querySelectorAll('[data-del-room]').forEach(btn => {
    btn.addEventListener('click', () => {
      state.rooms.splice(+btn.dataset.delRoom, 1);
      logHistory('Raum gelöscht');
      saveDraft();
      render();
    });
  });

  document.querySelectorAll('[data-del-media]').forEach(btn => {
    btn.addEventListener('click', () => {
      state.media.splice(+btn.dataset.delMedia, 1);
      logHistory('Datei gelöscht');
      saveDraft();
      render();
    });
  });

  const mediaInput = document.getElementById('mediaInput');
  if(mediaInput){
    mediaInput.addEventListener('change', e => {
      const files = Array.from(e.target.files || []);
      files.forEach(file => {
        const isImage = file.type.startsWith('image/');
        state.media.push({
          name:file.name,
          type:isImage ? 'image' : 'document',
          url:isImage ? URL.createObjectURL(file) : '',
        });
      });
      logHistory(files.length + ' Datei(en) hochgeladen');
      saveDraft();
      render();
    });
  }

  const printBtn = document.getElementById('printBtn');
  if(printBtn){
    printBtn.addEventListener('click', () => window.print());
  }
}

function initSignatureIfNeeded(){
  const canvas = document.getElementById('signatureCanvas');
  if(!canvas) return;

  const rect = canvas.getBoundingClientRect();
  canvas.width = rect.width;
  canvas.height = rect.height;

  const ctx = canvas.getContext('2d');
  let drawing = false;

  if(state.data.signature){
    const img = new Image();
    img.onload = () => ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    img.src = state.data.signature;
  }

  function getPos(e){
    const r = canvas.getBoundingClientRect();
    const p = e.touches ? e.touches[0] : e;
    return { x: p.clientX - r.left, y: p.clientY - r.top };
  }

  function start(e){
    drawing = true;
    const p = getPos(e);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
  }

  function move(e){
    if(!drawing) return;
    e.preventDefault();
    const p = getPos(e);
    ctx.lineTo(p.x, p.y);
    ctx.strokeStyle = '#16324f';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.stroke();
  }

  function end(){
    drawing = false;
  }

  canvas.onmousedown = start;
  canvas.onmousemove = move;
  canvas.onmouseup = end;
  canvas.onmouseleave = end;
  canvas.ontouchstart = start;
  canvas.ontouchmove = move;
  canvas.ontouchend = end;

  const clearBtn = document.getElementById('clearSignatureBtn');
  if(clearBtn){
    clearBtn.onclick = () => {
      ctx.clearRect(0,0,canvas.width,canvas.height);
      state.data.signature = '';
      saveDraft();
      logHistory('Unterschrift gelöscht');
    };
  }

  const saveBtn = document.getElementById('saveSignatureBtn');
  if(saveBtn){
    saveBtn.onclick = () => {
      state.data.signature = canvas.toDataURL('image/png');
      saveDraft();
      logHistory('Unterschrift gespeichert');
      render();
    };
  }
}

function renderTop(){
  const c = calcData();
  const gp = globalProgress();

  document.getElementById('globalPercent').textContent = gp.percent;
  document.getElementById('gesamtStromTop').textContent = c.gesamtStrom;
  document.getElementById('pvTop').textContent = c.pvKwpGesamt.toFixed(1);
  document.getElementById('heizlastTop').textContent = c.heizlastGesamtKW.toFixed(1);

  document.querySelectorAll('.interest-toggle').forEach(btn => {
    btn.classList.toggle('active', !!state.data[btn.dataset.toggle]);
  });
}

function render(){
  ensureValidActiveStep();

  const hasAnyProduct =
    state.data.zielPV || state.data.zielWP || state.data.zielSpeicher || state.data.zielWallbox ||
    state.data.zielHeizlast || state.data.zielFenster || state.data.zielTueren ||
    state.data.zielBad || state.data.zielKueche;

  const sidebar = document.querySelector('.sidebar');
  if (sidebar) {
    sidebar.style.display = hasAnyProduct ? 'block' : 'block';
  }

  const visibleSteps = getVisibleSteps();
  const currentOrderedIndex = getCurrentOrderedIndex();
  const step = SCHRITTE[state.activeStep];

  document.getElementById('stepTitle').textContent = (currentOrderedIndex + 1) + '. ' + step.titel;
  document.getElementById('stepSub').textContent = step.untertitel;

  renderTop();
  renderStepList();
  renderWarnings();
  renderStepContent();
  renderRightBar();

  document.querySelectorAll('[data-step]').forEach(btn => {
    btn.addEventListener('click', () => {
      state.activeStep = +btn.dataset.step;
      render();
    });
  });

  document.querySelectorAll('[data-tab]').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.tab === state.rightTab);
    btn.onclick = () => {
      state.rightTab = btn.dataset.tab;
      render();
    };
  });

  document.querySelectorAll('[data-toggle]').forEach(btn => {
    btn.onclick = () => {
      const key = btn.dataset.toggle;
      updateField(key, !state.data[key]);
    };
  });

  document.getElementById('prevBtn').onclick = () => {
    const ordered = getOrderedSteps();
    const currentIndex = ordered.indexOf(state.activeStep);
    if(currentIndex > 0){
      state.activeStep = ordered[currentIndex - 1];
      render();
    }
  };

  document.getElementById('nextBtn').onclick = () => {
    const ordered = getOrderedSteps();
    const currentIndex = ordered.indexOf(state.activeStep);
    if(currentIndex < ordered.length - 1){
      state.activeStep = ordered[currentIndex + 1];
      render();
    }
  };

  document.getElementById('saveBtn').onclick = () => {
    saveDraft();
    logHistory('Entwurf manuell gespeichert');
    render();
  };

  // button text optional
  const ordered = getOrderedSteps();
  const currentIndex = ordered.indexOf(state.activeStep);
  document.getElementById('prevBtn').style.visibility = currentIndex <= 0 ? 'hidden' : 'visible';
  document.getElementById('nextBtn').textContent = currentIndex >= ordered.length - 1 ? 'Abschließen' : 'Weiter';
}
loadDraft();
render();
setInterval(saveDraft, 2000);
</script>
</body>
</html>