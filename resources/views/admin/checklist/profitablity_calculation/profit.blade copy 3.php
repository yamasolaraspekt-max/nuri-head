<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Solar Aspekt – Wirtschaftlichkeitsberechnung</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    :root {
      --brand-blue: #74b2d4;
      --brand-blue-dark: #5c8fa8;
      --brand-green: #93c21c;
      --brand-green-dark: #82ad18;
      --brand-green-light: #cfe09b;
    }

    html, body {
      margin: 0;
      padding: 0;
    }

    body {
      background: #f8fafc;
      color: #334155;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .hide-scrollbar::-webkit-scrollbar {
      display: none;
    }

    .hide-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    input[type=number] {
      -moz-appearance: textfield;
    }

    .animate-fade-in {
      animation: fadeIn .35s ease;
    }

    .animate-slide-up {
      animation: slideUp .35s ease;
    }

    .animate-zoom-in {
      animation: zoomIn .35s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(14px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes zoomIn {
      from { opacity: 0; transform: scale(.98); }
      to { opacity: 1; transform: scale(1); }
    }


    @page {
  size: A4 portrait;
  margin: 0;
}

@media print {
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .pdf-page {
    width: 794px !important;
    min-height: 1123px !important;
    page-break-after: always !important;
    break-after: page !important;
    overflow: hidden !important;
  }

  .pdf-page.no-page-break,
  .pdf-page:last-child {
    page-break-after: auto !important;
    break-after: auto !important;
  }

  .fixed,
  .sticky {
    position: static !important;
  }
}

  </style>  

 
</head>
<body>
  <div id="app"></div>

  <script>
    window.CALCULATOR_PRESET = @json($calculatorPreset ?? null);
  window.APP_LOGO_URL = @json(asset('logo/logo.png'));

  </script> 

  <script>
    // ─────────────────────────────────────────────────────────────
    // KONSTANTEN & ENERGIEWIRTSCHAFTLICHE PARAMETER (BEG 2024/2025/2026)
    // ─────────────────────────────────────────────────────────────
    const INFLATION = { STROM: 0.025, FOSSIL: 0.045, MOBILITAET: 0.035, WARTUNG: 0.02 };
    const PREIS_EINSPEISUNG = 0.081;
    const EFFIZIENZ_BONUS = 5;

    const ENERGIE = {
      'Erdgas': { preis: 0.11, co2: 0.201, faktorKWh: 1, einheit: 'kWh' },
      'Heizöl': { preis: 1.05, co2: 0.266, faktorKWh: 10, einheit: 'Liter' },
      'Pellets': { preis: 0.35, co2: 0.023, faktorKWh: 4.8, einheit: 'kg' },
      'Nachtspeicher': { preis: 0.30, co2: 0.400, faktorKWh: 1, einheit: 'kWh' },
      'Fernwärme': { preis: 0.14, co2: 0.280, faktorKWh: 1, einheit: 'kWh' }
    };

    const BRANDS = {
      solar_aspekt: {
        name: 'SOLAR ASPEKT',
        logo: window.APP_LOGO_URL || '/logo/logo.png', 
        primary: '#93c21c',      // Green
        primarySoft: '#cfe09b',
        secondary: '#74b2d4',    // Blue
        secondarySoft: '#c0d8ea'
      },
      werk_studio: {
        name: 'WERK STUDIO',
        // Change this to your actual Werk Studio logo path!
        logo: '/logo/werk.png', 
        pageLogo: '/logo/werk-studio.png',
        primary: '#96937c',      // Werk Studio Primary
        primarySoft: '#d6d5ce',  // Muted light variant of primary
        secondary: '#662967',    // Werk Studio Secondary
        secondarySoft: '#d1b9d1' // Muted light variant of secondary
      }
    };

    const CO2_STROMMIX = 0.400;

    const BEG_PARAMS = {
      basis: 30,
      klima: 20,
      einkommen: 30,
      maxProzent: 70,
      cMax: {
        efh: 30000,
        mfhBase: 30000,
        mfhTier2: 15000,
        mfhTier3: 8000
      }
    };

    const INITIAL_FORM_DATA = {
      brand: 'solar_aspekt',
      gebaeudeArt: 'Einfamilienhaus',
      wohneinheitenGesamt: '2',
      wohneinheitenBewohnt: '1',
      eigentuemerUnter40k: '0',
      einkommenUnter40k: '0',
      personenAnzahl: '3',
      heizungsArt: 'Erdgas',
      heizungsAlter: '20',
      heizsystem: 'Fußbodenheizung',
      heizVerbrauch: '20000',
      stromverbrauch: '4000',
      kmProJahr: '0',
      standortPlz: '',

      vorname: '',
      nachname: '',
      email: '',
      telefon: '',
      strasse: '',
      hausnummer: '',
      ort: '',

      wpDiscounts: [{ name: 'Solar-Aspekt', value: '1500' }],
      pvDiscounts: [{ name: 'Solar-Aspekt', value: '1000' }],
      speicherDiscounts: [],
      wallboxDiscounts: [{ name: 'Solar-Aspekt', value: '500' }],

      includeSolar: true,
      includeWp: true,
      evuPreis: '0.35',
      spritPreis: '1.80',
      heizPreis: ENERGIE['Erdgas'].preis.toString(),
      wartungAlt_pa_input: '250',
      customWpKosten: '',
      customPvKosten: '',
      customSpeicherKosten: '',
      customWallboxKosten: '',
      customWpSize: '',
      customPvSize: '',
      customSpeicherSize: '',
      customJaz: ''
    };

 
    const BACKEND_PRESET = window.CALCULATOR_PRESET || null;

    const mergeInitialFormData = (base, preset) => {
      if (!preset || typeof preset !== 'object') {
        return structuredClone(base);
      }

      return {
        ...structuredClone(base),
        ...preset
      };
    };

   const hasCompletePresetForAutoStart = (preset) => {
      if (!preset || typeof preset !== 'object') return false;

      const plzOk = String(preset.standortPlz || '').trim().length === 5;
      const heatOk = Number(preset.heizVerbrauch || 0) > 0;
      const powerOk = Number(preset.stromverbrauch || 0) > 0;
      const buildingOk = ['Einfamilienhaus', 'Mehrfamilienhaus'].includes(String(preset.gebaeudeArt || ''));
      const heatingTypeOk = !!String(preset.heizungsArt || '').trim();
      const heatingSystemOk = !!String(preset.heizsystem || '').trim();

      return plzOk && heatOk && powerOk && buildingOk && heatingTypeOk && heatingSystemOk;
    };


    // ─────────────────────────────────────────────────────────────
    // GLOBAL APP STATE
    // ─────────────────────────────────────────────────────────────
    const App = {
      root: document.getElementById('app'),

      state: {
        stage: 'START',
        quizStep: 0,

        isCalculating: false,
        loadingText: 'Analysiere Sonneneinstrahlung...',
        activeTab: 'FINANZEN',
        selectedYears: 20,
        isGeneratingPDF: false,
        showPdfPreview: false,
        showCalculationModal: false,

        formData: mergeInitialFormData(INITIAL_FORM_DATA, BACKEND_PRESET),
        savedProjects: [],
        currentProjectId: null,
        showProjectModal: false,
        newProjectName: '',

        showContactModal: false,
        contactSuccess: false,

        countUpState: {},
        loadingTimers: []
      } 

    };



    const PDF_COLORS = {
      green: '#93c21c',
      greenLight: '#cfe09b',
      blue: '#74b2d4',
      blueLight: '#c0d8ea',
      text: '#4a4a4a',
      textSoft: '#4b5563',
      border: '#dbe3ea'
    };

    App.renderPdfLetterhead = function (pageNo, title) {
      return `
        <div style="margin-bottom:24px;">
          <div class="flex items-start justify-between mb-3">
            <div>
              <div
                class="text-[9pt] font-black uppercase tracking-widest"
                style="color:${PDF_COLORS.green};"
              >
                Individuelle Energieanalyse
              </div>
            </div>

            <div class="flex items-center gap-2">
              <img
                src="${window.APP_LOGO_URL || '/logo/logo.png'}"
                alt="Solar Aspekt"
                style="height:32px; width:190px;"
              />
            </div>
          </div>

          <div
            style="width:100%; height:2px; margin-bottom:24px; background:${PDF_COLORS.green};"
          ></div>

          <div class="flex items-end justify-between">
            <h1
              class="text-[18pt] font-black uppercase"
              style="color:${PDF_COLORS.green}; margin:0;"
            >
              ${escapeHtml(title || '')}
            </h1>

            ${pageNo ? `
              <span
                class="text-[8pt] font-bold uppercase tracking-widest"
                style="color:#64748b;"
              >
                Seite ${pageNo}
              </span>
            ` : ''}
          </div>
        </div>
      `;
    };

    App.renderPdfShellPage = function (children, pageNo, title) {
      return `
        <div
          class="pdf-page bg-white"
          style="
            width:794px;
            min-height:1123px;
            padding:42px 56px 50px 56px;
            box-sizing:border-box;
            background:#ffffff;
            position:relative;
            page-break-after:always;
            break-after:page;
          "
        >
          ${this.renderPdfLetterhead(pageNo, title)}

          <div>${children || ''}</div>

          <div
            style="
              position:absolute;
              left:56px;
              right:56px;
              bottom:24px;
              height:2px;
              background:${PDF_COLORS.green};
            "
          ></div>
        </div>
      `;
    };

   

    // ─────────────────────────────────────────────────────────────
    // HELFER
    // ─────────────────────────────────────────────────────────────
    const toNumber = (v, fallback = 0) => {
      const n = Number(v);
      return Number.isFinite(n) && n >= 0 ? n : fallback;
    };

    App.bootstrapFromPreset = function () {
      if (!hasCompletePresetForAutoStart(BACKEND_PRESET)) {
        this.state.stage = 'START';
        this.state.quizStep = 0;
        return;
      }

      this.state.formData = mergeInitialFormData(INITIAL_FORM_DATA, BACKEND_PRESET);
      this.state.stage = 'DASHBOARD';
      this.state.quizStep = 2;
      this.state.activeTab = 'FINANZEN';
    };
    const clamp = (n, min, max) => Math.min(max, Math.max(min, n));

    const getInterpolatedAtYear = (chartData, year, key) => {
      if (!Array.isArray(chartData) || chartData.length === 0) return 0;
      if (!Number.isFinite(year)) return chartData[0]?.[key] ?? 0;

      const minYear = chartData[0]?.year ?? 0;
      const maxYear = chartData[chartData.length - 1]?.year ?? minYear;
      const y = clamp(year, minYear, maxYear);
      const lo = Math.floor(y);
      const hi = Math.ceil(y);
      const loPoint = chartData.find(d => d.year === lo) || chartData[0];
      const hiPoint = chartData.find(d => d.year === hi) || chartData[chartData.length - 1];
      const loVal = toNumber(loPoint?.[key], 0);
      const hiVal = toNumber(hiPoint?.[key], loVal);

      if (hi === lo) return loVal;

      const t = (y - lo) / (hi - lo);
      return loVal + (hiVal - loVal) * t;
    };

    const escapeHtml = (value) => {
      return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    };

    const formatInt = (n) => Math.round(toNumber(n, 0)).toLocaleString('de-DE');
    const formatFixed2 = (n) => toNumber(n, 0).toFixed(2).replace('.', ',');
    const formatDateDE = (ts) => new Date(ts).toLocaleDateString('de-DE');

    App.setState = function (patch) {
      this.state = { ...this.state, ...patch };
      this.render();
    };

    App.setFormData = function (nextFormData) {
      this.state.formData = nextFormData;
      this.render();
    };

    App.updateData = function (key, value, shouldRender = true) {
      this.state.formData = {
        ...this.state.formData,
        [key]: value
      };

      if (shouldRender) {
        this.render();
      }

      if (key === 'standortPlz' && String(value).length === 5) {
        this.lookupPostcode();
      }
    };

    App.getPlzYield = function (plz) {
      if (!plz || String(plz).length < 1) return 1000;
      const r = String(plz).charAt(0);

      if (['0', '1', '2'].includes(r)) return 950;
      if (['3', '4', '5'].includes(r)) return 980;
      if (['6', '7'].includes(r)) return 1050;
      if (['8', '9'].includes(r)) return 1100;

      return 1000;
    };

App.lookupPostcode = async function () {
  const plz = this.state.formData.standortPlz;
  if (String(plz).length !== 5) return;

  try {
    const res = await fetch(`https://api.zippopotam.us/de/${plz}`);
    if (!res.ok) return;

    const data = await res.json();
    const ort = data?.places?.[0]?.['place name'];

    if (ort && ort !== this.state.formData.ort) {
      this.state.formData = {
        ...this.state.formData,
        ort
      };
      this.render();
    }
  } catch (_) {}
};

    // ─────────────────────────────────────────────────────────────
    // TOOLTIP (vanilla helper render)
    // ─────────────────────────────────────────────────────────────
    App.tooltip = function (text, innerHtml) {
      return `
        <div class="relative group inline-flex items-center gap-1 cursor-help">
          ${innerHtml}
          <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-56 p-3 bg-slate-800 text-white text-xs rounded-xl shadow-2xl z-50 text-left leading-relaxed font-normal">
            ${escapeHtml(text)}
            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
          </div>
        </div>
      `;
    };

    // ─────────────────────────────────────────────────────────────
    // COUNT UP (replacement for useCountUp)
    // ─────────────────────────────────────────────────────────────
    App.countUp = function (key, target, duration = 1500, start = false) {
      const bucket = this.state.countUpState;

      if (!bucket[key]) {
        bucket[key] = {
          value: 0,
          prevTarget: 0,
          raf: null,
          running: false
        };
      }

      const ref = bucket[key];
      const validTarget = target == null ? 0 : Number(target) || 0;

      if (!start) {
        ref.value = validTarget;
        ref.prevTarget = validTarget;
        return ref.value;
      }

      if (ref.prevTarget === validTarget && !ref.running) {
        ref.value = validTarget;
        return ref.value;
      }

      if (!ref.running) {
        ref.running = true;
        const startValue = ref.value;
        const startTime = performance.now();

        const tick = (now) => {
          const elapsed = now - startTime;
          const progress = Math.min(elapsed / duration, 1);
          const ease = 1 - Math.pow(1 - progress, 3);
          const current = startValue + (validTarget - startValue) * ease;

          ref.value = Math.round(current);

          if (progress < 1) {
            ref.raf = requestAnimationFrame(tick);
          } else {
            ref.value = validTarget;
            ref.prevTarget = validTarget;
            ref.running = false;
            ref.raf = null;
            this.render();
          }
        };

        ref.raf = requestAnimationFrame(tick);
      }

      return ref.value;
    };

    // ─────────────────────────────────────────────────────────────
    // WORKFLOW: BERECHNEN MIT LADEANIMATION
    // ─────────────────────────────────────────────────────────────
    App.handleCalculate = function () {
      this.state.loadingTimers.forEach(clearTimeout);
      this.state.loadingTimers = [];

      this.state.isCalculating = true;
      this.state.stage = 'LOADING';
      this.state.loadingText = 'Analysiere Sonneneinstrahlung...';
      window.scrollTo(0, 0);
      this.render();

      this.state.loadingTimers.push(setTimeout(() => {
        this.state.loadingText = 'Prüfe maximale KfW-Fördermittel...';
        this.render();
      }, 1200));

      this.state.loadingTimers.push(setTimeout(() => {
        this.state.loadingText = 'Kalkuliere Amortisation und Ertrag...';
        this.render();
      }, 2400));

      this.state.loadingTimers.push(setTimeout(() => {
        this.state.isCalculating = false;
        this.state.stage = 'DASHBOARD';
        this.state.activeTab = 'FINANZEN';
        this.render();
      }, 3500));
    };

    // ─────────────────────────────────────────────────────────────
    // PROJEKT CRUD
    // ─────────────────────────────────────────────────────────────
    App.loadProjects = function () {
      const loaded = localStorage.getItem('solarAspektProjects');

      if (!loaded) return;

      try {
        this.state.savedProjects = JSON.parse(loaded);
      } catch (e) {
        console.error('Fehler beim Laden der Projekte', e);
      }
    };

    App.persistProjects = function () {
      localStorage.setItem('solarAspektProjects', JSON.stringify(this.state.savedProjects));
    };

    App.saveProject = function (saveAsNew = false) {
      const { formData, savedProjects, currentProjectId, newProjectName } = this.state;
      const defaultName = formData.strasse
        ? `${formData.strasse} ${formData.hausnummer}, ${formData.ort}`
        : `Projekt ${new Date().toLocaleDateString()}`;

      const projectName = (newProjectName || '').trim() || defaultName;
      let updatedList = [...savedProjects];

      if (currentProjectId && !saveAsNew) {
        updatedList = updatedList.map(p =>
          p.id === currentProjectId
            ? { ...p, name: projectName, data: structuredClone(formData), updatedAt: Date.now() }
            : p
        );
      } else {
        const newProject = {
          id: Date.now().toString(),
          name: projectName,
          data: structuredClone(formData),
          createdAt: Date.now(),
          updatedAt: Date.now()
        };

        updatedList.push(newProject);
        this.state.currentProjectId = newProject.id;
      }

      this.state.savedProjects = updatedList;
      this.persistProjects();
      this.state.newProjectName = '';
      this.state.showProjectModal = false;
      alert('Projekt erfolgreich gespeichert!');
      this.render();
    };

    App.loadProject = function (id) {
      const project = this.state.savedProjects.find(p => p.id === id);

      if (!project) return;

      this.state.formData = structuredClone(project.data);
      this.state.currentProjectId = project.id;
      this.state.newProjectName = project.name;
      this.state.stage = 'DASHBOARD';
      this.state.activeTab = 'FINANZEN';
      this.state.showProjectModal = false;
      window.scrollTo(0, 0);
      this.render();
    };

    App.deleteProject = function (id) {
      if (!window.confirm('Möchtest du dieses Projekt wirklich löschen?')) return;

      this.state.savedProjects = this.state.savedProjects.filter(p => p.id !== id);

      if (this.state.currentProjectId === id) {
        this.state.currentProjectId = null;
      }

      this.persistProjects();
      this.render();
    };

    App.startNewProject = function () {
      this.state.formData = structuredClone(INITIAL_FORM_DATA);
      this.state.currentProjectId = null;
      this.state.newProjectName = '';
      this.state.showProjectModal = false;
      this.state.quizStep = 0;
      this.state.stage = 'QUIZ';
      this.render();
    };

    // ─────────────────────────────────────────────────────────────
    // KERN-BERECHNUNGSLOGIK (voll erhalten)
    // ─────────────────────────────────────────────────────────────
    App.calculateErgebnis = function () {
      const formData = this.state.formData;

      const PREIS_STROM = toNumber(formData.evuPreis, 0.35);
      const PREIS_HEIZUNG = toNumber(formData.heizPreis, ENERGIE['Erdgas'].preis);
      const PREIS_SPRIT = toNumber(formData.spritPreis, 1.80);

      const bedarfHaus = toNumber(formData.stromverbrauch, 0);
      const kmJahr = toNumber(formData.kmProJahr, 0);
      const activeHaus = bedarfHaus > 0;
      const activeAuto = kmJahr > 0;

      const kostenAutoHeute = activeAuto ? (kmJahr / 100) * 7 * PREIS_SPRIT : 0;
      const bedarfAutoKWhMorgen = activeAuto ? (kmJahr / 100) * 18 : 0;

      const rawWaermeBedarf = toNumber(formData.heizVerbrauch, 0);
      const waermeBedarfKWhFossil = rawWaermeBedarf * ENERGIE[formData.heizungsArt].faktorKWh;

      const stromKostenHeute = bedarfHaus * PREIS_STROM;
      const heizKostenHeute = rawWaermeBedarf * PREIS_HEIZUNG;

      const alterHeizung = toNumber(formData.heizungsAlter, 20);
      const wirkungsgradVerlust = (alterHeizung / 20) * 0.15;
      const kesselWirkungsgrad = clamp(1.0 - wirkungsgradVerlust, 0.4, 1.0);
      const nutzWaermeKWh = waermeBedarfKWhFossil * kesselWirkungsgrad;

      const defaultJaz =
        formData.heizsystem === 'Fußbodenheizung'
          ? 4.5
          : formData.heizsystem === 'Beides'
            ? 4.0
            : 3.5;

      const jaz = formData.customJaz !== ''
        ? toNumber(formData.customJaz, defaultJaz)
        : defaultJaz;

      const bedarfWP = (formData.includeWp && jaz > 0) ? (nutzWaermeKWh / jaz) : 0;
      const bedarfGesamtMorgen = bedarfHaus + bedarfAutoKWhMorgen + bedarfWP;

      const kostenHausNeu = bedarfHaus * PREIS_STROM;
      const kostenWPNeu = bedarfWP * PREIS_STROM;
      const kostenAutoNeu = bedarfAutoKWhMorgen * PREIS_STROM;
      const kostenGesamtNeu = bedarfGesamtMorgen * PREIS_STROM;

      const ersparnisHeizungPct =
        (heizKostenHeute > 0 && formData.includeWp)
          ? (1 - (kostenWPNeu / heizKostenHeute)) * 100
          : 0;

      const ersparnisAutoPct =
        (kostenAutoHeute > 0 && activeAuto)
          ? (1 - (kostenAutoNeu / kostenAutoHeute)) * 100
          : 0;

      const kostenEnergieAltGesamt = stromKostenHeute + heizKostenHeute + kostenAutoHeute;
      const ersparnisGesamtPct =
        kostenEnergieAltGesamt > 0
          ? (1 - (kostenGesamtNeu / kostenEnergieAltGesamt)) * 100
          : 0;

      const baseHeizlast =
        formData.gebaeudeArt === 'Einfamilienhaus'
          ? Math.max(5, Math.ceil(waermeBedarfKWhFossil / 2200))
          : Math.max(10, Math.ceil(waermeBedarfKWhFossil / 2000));

      const heizlast = formData.customWpSize !== ''
        ? toNumber(formData.customWpSize, baseHeizlast)
        : baseHeizlast;

      const autoKostenWP =
        formData.includeWp
          ? (formData.gebaeudeArt === 'Einfamilienhaus'
              ? (22000 + (heizlast * 900))
              : (30000 + (heizlast * 700)))
          : 0;

      const pvYieldFactor = this.getPlzYield(formData.standortPlz);

      const basePvKwp =
        formData.includeSolar
          ? Math.max(4, Math.ceil((bedarfGesamtMorgen * 1.3 / 1000) * 2) / 2)
          : 0;

      let pvKwp = formData.customPvSize !== ''
        ? toNumber(formData.customPvSize, basePvKwp)
        : basePvKwp;

      const baseSpeicherKwh =
        formData.includeSolar
          ? clamp(Math.round((pvKwp * 0.8) / 3) * 3, 3, 20)
          : 0;

      let speicherKwh = formData.customSpeicherSize !== ''
        ? toNumber(formData.customSpeicherSize, baseSpeicherKwh)
        : baseSpeicherKwh;

      const pvProduktion = pvKwp * pvYieldFactor;

      const autoPvCost = formData.includeSolar ? Math.round(pvKwp * 1250) : 0;
      const autoSpeicherCost = formData.includeSolar ? Math.round(speicherKwh * 600) : 0;
      
      // Wallbox logic updated to respect the delete/add toggle
      const autoWallboxCost = (activeAuto && formData.includeSolar && formData.includeWallbox !== false) ? 1200 : 0;

      const effKostenWP = formData.includeWp
        ? (formData.customWpKosten !== '' ? toNumber(formData.customWpKosten, autoKostenWP) : autoKostenWP)
        : 0;

      const effPvCost = formData.includeSolar
        ? (formData.customPvKosten !== '' ? toNumber(formData.customPvKosten, autoPvCost) : autoPvCost)
        : 0;

      const effSpeicherCost = formData.includeSolar
        ? (formData.customSpeicherKosten !== '' ? toNumber(formData.customSpeicherKosten, autoSpeicherCost) : autoSpeicherCost)
        : 0;

      const effWallboxCost =
        (activeAuto && formData.includeSolar && formData.includeWallbox !== false)
          ? (formData.customWallboxKosten !== '' ? toNumber(formData.customWallboxKosten, autoWallboxCost) : autoWallboxCost)
          : 0;

      // --- BEG FÖRDERLOGIK ---
      const isFossilKlimaEligible =
        ['Heizöl', 'Nachtspeicher'].includes(formData.heizungsArt) ||
        (formData.heizungsArt === 'Erdgas' && toNumber(formData.heizungsAlter) >= 20);

      const aktKlimaBonus = isFossilKlimaEligible ? BEG_PARAMS.klima : 0;

      let kfwMaxSum = 0;
      let foerderQuote = 0;

      let rep_kfwProzent = 0;
      let rep_foerderFaehigeKosten = 0;
      let rep_einkommensBonus = 0;

      if (formData.includeWp) {
        if (formData.gebaeudeArt === 'Einfamilienhaus') {
          rep_einkommensBonus = formData.einkommenUnter40k === '1' ? BEG_PARAMS.einkommen : 0;
          rep_kfwProzent = Math.min(BEG_PARAMS.basis + EFFIZIENZ_BONUS + aktKlimaBonus + rep_einkommensBonus, BEG_PARAMS.maxProzent);
          rep_foerderFaehigeKosten = Math.min(effKostenWP, BEG_PARAMS.cMax.efh);

          const prozentualerZuschuss = rep_foerderFaehigeKosten * (rep_kfwProzent / 100);
          kfwMaxSum = Math.min(prozentualerZuschuss, effKostenWP);
          foerderQuote = effKostenWP > 0 ? (kfwMaxSum / effKostenWP) * 100 : 0;
        } else {
          const w_gesamt = Math.max(1, parseInt(formData.wohneinheitenGesamt) || 2);
          let totalFoerderung = 0;
          const numBewohnt = clamp(parseInt(formData.wohneinheitenBewohnt) || 0, 0, w_gesamt);

          const w_selbst_bonus = Math.min(toNumber(formData.eigentuemerUnter40k), numBewohnt);
          const w_selbst_normal = numBewohnt - w_selbst_bonus;

          const p_selbst_bonus = Math.min(BEG_PARAMS.basis + EFFIZIENZ_BONUS + aktKlimaBonus + BEG_PARAMS.einkommen, BEG_PARAMS.maxProzent) / 100;
          const p_selbst_normal = Math.min(BEG_PARAMS.basis + EFFIZIENZ_BONUS + aktKlimaBonus, BEG_PARAMS.maxProzent) / 100;
          const p_vermietet = Math.min(BEG_PARAMS.basis + EFFIZIENZ_BONUS, BEG_PARAMS.maxProzent) / 100;
          const costPerUnit = effKostenWP / w_gesamt;

          for (let i = 1; i <= w_gesamt; i++) {
            let einheitMaxKosten =
              i === 1 ? BEG_PARAMS.cMax.mfhBase
                : (i <= 6 ? BEG_PARAMS.cMax.mfhTier2 : BEG_PARAMS.cMax.mfhTier3);

            let eligible = Math.min(einheitMaxKosten, costPerUnit);
            let einheitProzent = p_vermietet;

            if (i <= w_selbst_bonus) {
              einheitProzent = p_selbst_bonus;
            } else if (i <= w_selbst_bonus + w_selbst_normal) {
              einheitProzent = p_selbst_normal;
            }

            totalFoerderung += eligible * einheitProzent;
          }

          kfwMaxSum = Math.min(totalFoerderung, effKostenWP);
          foerderQuote = effKostenWP > 0 ? (kfwMaxSum / effKostenWP) * 100 : 0;
        }
      }
 
      // --- DYNAMIC DISCOUNTS CALCULATION ---
      const sumDiscounts = (arr) => (arr || []).reduce((acc, curr) => acc + toNumber(curr.value, 0), 0);

      // Only count discounts if the respective component is active
      const f_wp_zusatz = formData.includeWp ? sumDiscounts(formData.wpDiscounts) : 0;
      const f_pv = formData.includeSolar ? sumDiscounts(formData.pvDiscounts) : 0;
      const f_speicher = formData.includeSolar ? sumDiscounts(formData.speicherDiscounts) : 0;
      const f_wallbox = (activeAuto && formData.includeSolar && formData.includeWallbox !== false) 
        ? sumDiscounts(formData.wallboxDiscounts) 
        : 0;
      // -------------------------------------

      const investWpNetto = Math.max(0, effKostenWP - kfwMaxSum - f_wp_zusatz);
      const investPvNetto = Math.max(0, effPvCost - f_pv);
      const investSpeicherNetto = Math.max(0, effSpeicherCost - f_speicher);
      const investWallboxNetto = Math.max(0, effWallboxCost - f_wallbox);

      const gesamtInvestBrutto = effKostenWP + effPvCost + effSpeicherCost + effWallboxCost;
      const gesamtFoerderung = kfwMaxSum + f_wp_zusatz + f_pv + f_speicher + f_wallbox;
      const nettoInvestition = Math.max(0, gesamtInvestBrutto - gesamtFoerderung);

      // The Gutschein Summe is now exactly the sum of all active custom discounts
      const gutscheinSumme = f_wp_zusatz + f_pv + f_speicher + f_wallbox;

      const wartungPV_pa = formData.includeSolar ? ((effPvCost + effSpeicherCost) * 0.01) : 0;
      const wartungWP_pa = formData.includeWp ? (250 + (effKostenWP * 0.015)) : 0;
      const wartungGesamt_pa = wartungPV_pa + wartungWP_pa;
      const wartungAlt_pa = toNumber(formData.wartungAlt_pa_input, 250);

      const saisonVerteilung = [
        { name: 'Winter', tage: 90, yD: 0.12, hD: 0.50, bD: 0.28, color: 'text-[#93c21c]', bg: 'bg-[#cfe09b]', border: 'border-[#cfe09b]', stroke: 'stroke-[#93c21c]' },
        { name: 'Frühling', tage: 92, yD: 0.28, hD: 0.25, bD: 0.24, color: 'text-[#93c21c]', bg: 'bg-[#cfe09b]', border: 'border-[#cfe09b]', stroke: 'stroke-[#93c21c]' },
        { name: 'Sommer', tage: 92, yD: 0.42, hD: 0.05, bD: 0.22, color: 'text-[#93c21c]', bg: 'bg-[#cfe09b]', border: 'border-[#cfe09b]', stroke: 'stroke-[#93c21c]' },
        { name: 'Herbst', tage: 91, yD: 0.18, hD: 0.20, bD: 0.26, color: 'text-[#93c21c]', bg: 'bg-[#cfe09b]', border: 'border-[#cfe09b]', stroke: 'stroke-[#93c21c]' }
      ];

      let totalCovered = 0;
      let totalEinspeisung = 0;
      const saisonDaten = [];

      saisonVerteilung.forEach(s => {
          const sYield = pvProduktion * s.yD;
          const sConsBase = (bedarfHaus + bedarfAutoKWhMorgen) * s.bD;
          const sConsHeat = bedarfWP * s.hD;
          const sConsTotal = sConsBase + sConsHeat;

          const dailyYield = sYield / s.tage;
          const dailyCons = sConsTotal / s.tage;
          
          let sCovered = 0;
          let sFeedIn = 0;

          if (formData.includeSolar) {
              const directUseRatio = 0.35; 
              const dailyDirect = Math.min(dailyYield, dailyCons * directUseRatio);
              
              const dailyRemainingYield = dailyYield - dailyDirect;
              const dailyRemainingCons = dailyCons - dailyDirect;
              
              const charge = Math.min(dailyRemainingYield, speicherKwh * 0.95);
              const batteryUse = Math.min(dailyRemainingCons, charge * 0.95);
              
              const dailyTotalCovered = dailyDirect + batteryUse;
              
              // 98% Autarky Cap for Summer
              const autarkyCap = (s.name === 'Sommer') ? 0.98 : 1.00;
              const maxSeasonalCovered = sConsTotal * autarkyCap;
              
              sCovered = Math.min(dailyTotalCovered * s.tage, maxSeasonalCovered);
              sFeedIn = Math.max(0, sYield - sCovered);
          }

          const sVorherKosten =
              (stromKostenHeute + kostenAutoHeute) * s.bD +
              (heizKostenHeute * s.hD) +
              (wartungAlt_pa * (s.tage / 365));

          const sRestbezug = Math.max(0, sConsTotal - sCovered);
          const sWartungAnteil = wartungGesamt_pa * (s.tage / 365);
          const sHeizkostenBleiben = !formData.includeWp ? (heizKostenHeute * s.hD) : 0;

          const sNachherKosten =
              (sRestbezug * PREIS_STROM) -
              (sFeedIn * PREIS_EINSPEISUNG) +
              sWartungAnteil +
              sHeizkostenBleiben;

          const sErsparnis = sVorherKosten - sNachherKosten;

          totalCovered += sCovered;
          totalEinspeisung += sFeedIn;

          saisonDaten.push({
              ...s,
              ertrag: Math.round(sYield),
              heizbedarf: Math.round(sConsHeat),
              verbrauch: Math.round(sConsTotal),
              covered: Math.round(sCovered),
              pctCovered: sConsTotal > 0 ? clamp(Math.round((sCovered / sConsTotal) * 100), 0, 100) : 0,
              restbezug: Math.round(sRestbezug),
              pctRestbezug: sConsTotal > 0 ? clamp(Math.round((sRestbezug / sConsTotal) * 100), 0, 100) : 0,
              einspeisung: Math.round(sFeedIn),
              pctEinspeisung: sYield > 0 ? clamp(Math.round((sFeedIn / sYield) * 100), 0, 100) : 0,
              autarkie: sConsTotal > 0 ? clamp(Math.round((sCovered / sConsTotal) * 100), 0, 100) : 0,
              vorherKosten: sVorherKosten,
              nachherKosten: sNachherKosten,
              ersparnis: sErsparnis
          });
      });

      const restStrom = Math.max(0, bedarfGesamtMorgen - totalCovered);
      const eigenverbrauchQuote = pvProduktion > 0 ? (totalCovered / pvProduktion) * 100 : 0;
      const autarkieQuote = bedarfGesamtMorgen > 0 ? (totalCovered / bedarfGesamtMorgen) * 100 : 0;
      const einspeisungQuote = pvProduktion > 0 ? (totalEinspeisung / pvProduktion) * 100 : 0;

      let co2EmissionFossil = (bedarfHaus * CO2_STROMMIX) + (waermeBedarfKWhFossil * ENERGIE[formData.heizungsArt].co2);
      if (activeAuto) co2EmissionFossil += (kmJahr * 0.15);

      let co2EmissionNeu = restStrom * CO2_STROMMIX;
      if (!formData.includeWp) co2EmissionNeu += (waermeBedarfKWhFossil * ENERGIE[formData.heizungsArt].co2);

      let co2ErsparnisPerYear = Math.max(0, (co2EmissionFossil - co2EmissionNeu) / 1000);

      const co2Baeume = Math.round(co2ErsparnisPerYear * 80);
      const co2FlaecheQm = Math.round((co2ErsparnisPerYear / 8) * 10000);

      const opexFossilReihe = {};
      const opexSolarReihe = {};
      const ersparnisOpexReihe = {};
      const tcoSolarReihe = {};
      const bilanzReihe = {};
      const chartData = [{ year: 0, fossil: 0, solar: Math.round(nettoInvestition) }];

      let cumFossil = 0;
      let cumOpexSolar = 0;
      let cumReplacement = 0;
      let amortisation = null;
      let prevBilanz = -nettoInvestition;
      let opex30YearsPV = 0;

      for (let i = 1; i <= 40; i++) {
        let degradation = Math.pow(0.995, i - 1);
        let currentPvProd = pvProduktion * degradation;
        let currentCovered = 0;
        let currentEinspeisung = 0;

        if (currentPvProd > 0 && bedarfGesamtMorgen > 0) {
          const evRatio = totalCovered / pvProduktion;
          currentCovered = Math.min(bedarfGesamtMorgen, currentPvProd * evRatio);
          currentEinspeisung = Math.max(0, currentPvProd - currentCovered);
        }

        let currentRestbezug = Math.max(0, bedarfGesamtMorgen - currentCovered);
        let fStrom = Math.pow(1 + INFLATION.STROM, i - 1);
        let fFossil = Math.pow(1 + INFLATION.FOSSIL, i - 1);
        let fMobil = Math.pow(1 + INFLATION.MOBILITAET, i - 1);
        let fWartung = Math.pow(1 + INFLATION.WARTUNG, i - 1);

        let fossilJahr =
          (bedarfHaus * PREIS_STROM * fStrom) +
          (rawWaermeBedarf * PREIS_HEIZUNG * fFossil) +
          (activeAuto ? (kmJahr / 100) * 7 * PREIS_SPRIT * fMobil : 0) +
          (wartungAlt_pa * fWartung);

        let solarOpexJahr =
          (currentRestbezug * PREIS_STROM * fStrom) -
          (currentEinspeisung * PREIS_EINSPEISUNG) +
          (wartungGesamt_pa * fWartung);

        if (!formData.includeWp) {
          solarOpexJahr += (rawWaermeBedarf * PREIS_HEIZUNG * fFossil);
        }

        if (i <= 30) {
          opex30YearsPV += (wartungPV_pa * fWartung);
        }

        let replacementJahr = 0;

        if (formData.includeSolar && speicherKwh > 0 && i === 15) {
          let ersatzPV = (pvKwp * 200) + (speicherKwh * 400);
          replacementJahr += ersatzPV;

          if (i <= 30) {
            opex30YearsPV += ersatzPV;
          }
        }

        if (formData.includeWp && i === 20) {
          replacementJahr += 12000;
        }

        cumFossil += fossilJahr;
        cumOpexSolar += solarOpexJahr;
        cumReplacement += replacementJahr;

        let totalLaufendNeu = cumOpexSolar + cumReplacement;
        let aktuelleErsparnis = cumFossil - totalLaufendNeu;
        let aktuelleBilanz = cumFossil - (nettoInvestition + totalLaufendNeu);

        if (aktuelleBilanz >= 0 && amortisation === null) {
          amortisation = (i - 1) + (Math.abs(prevBilanz) / (aktuelleBilanz - prevBilanz));
        }

        prevBilanz = aktuelleBilanz;

        if ([1, 10, 15, 20, 25, 30, 40].includes(i)) {
          opexFossilReihe[i] = cumFossil;
          opexSolarReihe[i] = totalLaufendNeu;
          ersparnisOpexReihe[i] = aktuelleErsparnis;
          tcoSolarReihe[i] = nettoInvestition + totalLaufendNeu;
          bilanzReihe[i] = aktuelleBilanz;
        }

        if (i <= 30) {
          chartData.push({
            year: i,
            fossil: Math.round(cumFossil),
            solar: Math.round(nettoInvestition + totalLaufendNeu)
          });
        }
      }

      const totalPvProd30Years = Array
        .from({ length: 30 }, (_, i) => pvProduktion * Math.pow(0.995, i))
        .reduce((a, b) => a + b, 0);

      const solarstromPreis =
        formData.includeSolar && totalPvProd30Years > 0
          ? (investPvNetto + investSpeicherNetto + opex30YearsPV) / totalPvProd30Years
          : PREIS_STROM;

      const fossilKostenJahr1 = opexFossilReihe[1];
      const ersparnisJahr1 = ersparnisOpexReihe[1];
      const finanzAutarkie =
        fossilKostenJahr1 > 0
          ? Math.min(100, Math.max(0, (ersparnisJahr1 / fossilKostenJahr1) * 100))
          : 0;

      const roi =
        nettoInvestition > 0
          ? (ersparnisJahr1 / nettoInvestition) * 100
          : 0;

      return {
        kfwZuschussMax: Math.round(kfwMaxSum),
        stromKostenHeute,
        heizKostenHeute,
        autoKostenHeute: kostenAutoHeute,
        waermeBedarfKWhFossil,
        nutzWaermeKWh,
        bedarfAutoKWhMorgen,
        kostenAutoHeute,
        kmJahr,
        ersparnisJahr1: Math.round(ersparnisJahr1),
        co2ErsparnisPerYear,
        co2Baeume,
        co2FlaecheQm,
        amortisationJahre: amortisation,
        pvGroesse: pvKwp.toFixed(1),
        speicherGroesse: speicherKwh.toFixed(1),
        pvProduktion: Math.round(pvProduktion),
        pvYield: pvYieldFactor,
        baseWpGroesse: baseHeizlast,
        basePvGroesse: basePvKwp.toFixed(1),
        baseSpeicherGroesse: baseSpeicherKwh.toFixed(1),
        saisonDaten,
        eigenverbrauchQuote,
        einspeisungQuote,
        wpGroesseKW: heizlast,
        gutscheinSumme,
        autarkieQuote: Math.round(autarkieQuote),
        finanzAutarkieQuote: Math.round(finanzAutarkie),
        gesamtbedarf: Math.round(bedarfGesamtMorgen),
        wpStrombedarf: Math.round(bedarfWP),
        hausStrombedarf: Math.round(bedarfHaus),
        autoStrombedarf: Math.round(bedarfAutoKWhMorgen),
        genutzterPvStrom: Math.round(totalCovered),
        restStromBezug: Math.round(restStrom),
        netzEinspeisung: Math.round(totalEinspeisung),
        gesamtInvestBrutto: Math.round(gesamtInvestBrutto),
        nettoInvestition: Math.round(nettoInvestition),
        investWpNetto: Math.round(investWpNetto),
        investWPBrutto: Math.round(effKostenWP),
        investPvNetto: Math.round(investPvNetto),
        investPVOnly: Math.round(effPvCost),
        investSpeicherNetto: Math.round(investSpeicherNetto),
        investSpeicher: Math.round(effSpeicherCost),
        investWallboxNetto: Math.round(investWallboxNetto),
        investWallbox: Math.round(effWallboxCost),
        autoKostenWP,
        autoPvCost,
        autoSpeicherCost,
        autoWallboxCost,
        foerderQuote,
        roi: roi.toFixed(1),
        gesamtFoerderung,
        f_wp_zusatz,
        f_pv,
        f_speicher,
        f_wallbox,
        fossilKostenJahr1,
        wartungAlt_pa,
        opexFossilReihe,
        opexSolarReihe,
        ersparnisOpexReihe,
        tcoSolarReihe,
        bilanzReihe,
        solarstromPreis,
        activeHaus,
        activeAuto,
        jaz: jaz.toFixed(1),
        chartData,
        alterHeizung,
        wirkungsgradVerlust,
        kostenHausNeu,
        kostenWPNeu,
        kostenAutoNeu,
        kostenGesamtNeu,
        ersparnisHeizungPct,
        ersparnisAutoPct,
        ersparnisGesamtPct,
        report: {
          nutzWaermeKWh,
          kesselWirkungsgrad,
          rep_kfwProzent,
          rep_foerderFaehigeKosten,
          rep_einkommensBonus,
          aktKlimaBonus,
          opex30YearsPV,
          wartungGesamt_pa
        }
      };
    };
    // ─────────────────────────────────────────────────────────────
    // PDF LOGIK
    // ─────────────────────────────────────────────────────────────
    App.downloadPDF = function () {
      this.state.isGeneratingPDF = true;
      this.render();

      const generate = () => {
        const element = document.getElementById('pdf-export-wrapper');

        if (!element) {
          this.state.isGeneratingPDF = false;
          this.render();
          return;
        }

        const opt = {
          margin: 0,
          filename: `SolarAspekt_Fahrplan_${this.state.formData.standortPlz || 'Wirtschaftlichkeit'}.pdf`,
          image: { type: 'jpeg', quality: 1 },
          html2canvas: {
            scale: 2,
            useCORS: true,
            windowWidth: 794,
            scrollY: 0,
            scrollX: 0,
            backgroundColor: '#ffffff'
          },
          jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait'
          },
          pagebreak: {
            mode: ['css', 'legacy'],
            before: '.pdf-page-break'
          }
        };

        window.html2pdf()
          .set(opt)
          .from(element)
          .save()
          .then(() => {
            this.state.isGeneratingPDF = false;
            this.render();
          })
          .catch(() => {
            this.state.isGeneratingPDF = false;
            this.render();
          });
      };

      if (!window.html2pdf) {
        const existing = document.querySelector('script[data-pdf-loader="1"]');

        if (existing) return;

        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
        script.dataset.pdfLoader = '1';
        script.onload = generate;
        script.onerror = () => {
          this.state.isGeneratingPDF = false;
          this.render();
        };
        document.body.appendChild(script);
      } else {
        generate();
      }
    };

    // ─────────────────────────────────────────────────────────────
    // FORMAT HELFER FÜR DASHBOARD/PDF
    // ─────────────────────────────────────────────────────────────
    App.formatAmortisation = function (ergebnis) {
      if (!ergebnis || ergebnis.amortisationJahre === null || ergebnis.amortisationJahre > 40) {
        return '> 40 Jahre';
      }
      return `${ergebnis.amortisationJahre.toFixed(1).replace('.', ',')} Jahre`;
    };

    App.getDashboardCounters = function (ergebnis) {
        const selectedYears = this.state.selectedYears;

        const kfwCounter = ergebnis.kfwZuschussMax;
        const fossilCounter = Math.round(ergebnis.opexFossilReihe?.[selectedYears] || 0);
        const opexSolarCounter = Math.round(ergebnis.opexSolarReihe?.[selectedYears] || 0);
        const ersparnisOpexCounter = Math.round(ergebnis.ersparnisOpexReihe?.[selectedYears] || 0);

        const pctSavings = fossilCounter > 0 ? Math.round((ersparnisOpexCounter / fossilCounter) * 100) : 0;
        const pctNew = fossilCounter > 0 ? Math.max(0, 100 - pctSavings) : 0;

        return {
          kfwCounter,
          fossilCounter,
          opexSolarCounter,
          ersparnisOpexCounter,
          pctSavings,
          pctNew
        };
      };
  
    // ─────────────────────────────────────────────────────────────
    // INIT (safe for Part 1)
    // ─────────────────────────────────────────────────────────────
    App.loadProjects();

    // ─────────────────────────────────────────────────────────────
    // RENDER HELPERS
    // ─────────────────────────────────────────────────────────────
  App.renderIcon = function (name, cls = 'w-4 h-4') {
    return `<i data-lucide="${name}" class="${cls}"></i>`;
  };

  App.renderSelectWrapper = function (inner) {
    return `
      <div class="relative">
        ${inner}
        ${this.renderIcon('chevron-down', 'absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none')}
      </div>
    `;
  };

  App.renderNumberInputWrapper = function (unit, inner) {
    return `
      <div class="relative flex items-center">
        ${inner}
        <span class="absolute right-4 text-slate-400 font-bold pointer-events-none">${unit}</span>
      </div>
    `;
  };

  App.renderQuizStep = function () {
    const { formData, quizStep } = this.state;

    if (quizStep === 0) {
      return `
        <div class="animate-slide-up text-left">
          <h2 class="text-2xl md:text-3xl font-black text-slate-800 mb-2">Dein Energie-Profil</h2>
          <p class="text-sm text-slate-500 mb-6">
            Lass uns mit den Basics starten. Die Postleitzahl benötigen wir für die exakte Solar-Ertragsprognose.
          </p>

          <div class="space-y-6">
            <div>
              <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Gebäudeart</label>
              ${this.renderSelectWrapper(`
                <select
                  data-model="gebaeudeArt"
                  class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 appearance-none cursor-pointer transition-colors"
                >
                  <option value="Einfamilienhaus" ${formData.gebaeudeArt === 'Einfamilienhaus' ? 'selected' : ''}>Einfamilienhaus</option>
                  <option value="Mehrfamilienhaus" ${formData.gebaeudeArt === 'Mehrfamilienhaus' ? 'selected' : ''}>Mehrfamilienhaus</option>
                </select>
              `)}
            </div>

            ${formData.gebaeudeArt === 'Einfamilienhaus' ? `
              <div class="mt-6 p-5 bg-slate-100/50 rounded-2xl border border-slate-200 animate-fade-in">
                <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Zu versteuerndes Haushalts-Einkommen</label>
                ${this.renderSelectWrapper(`
                  <select
                    data-model="einkommenUnter40k"
                    class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 appearance-none cursor-pointer transition-colors"
                  >
                    <option value="0" ${formData.einkommenUnter40k === '0' ? 'selected' : ''}>Über 40.000 € / Jahr</option>
                    <option value="1" ${formData.einkommenUnter40k === '1' ? 'selected' : ''}>Unter 40.000 € / Jahr</option>
                  </select>
                `)}
              </div>
            ` : ''}

            ${formData.gebaeudeArt === 'Mehrfamilienhaus' ? `
              <div class="space-y-6 mt-6 p-5 bg-slate-100/50 rounded-2xl border border-slate-200 animate-fade-in">
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Wohneinheiten gesamt</label>
                    ${this.renderNumberInputWrapper('WE', `
                      <input
                        type="number"
                        min="2"
                        max="50"
                        data-model="wohneinheitenGesamt"
                        value="${escapeHtml(formData.wohneinheitenGesamt)}"
                        class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-12"
                      />
                    `)}
                  </div>
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Davon selbst bewohnt</label>
                    ${this.renderNumberInputWrapper('WE', `
                      <input
                        type="number"
                        min="0"
                        max="${escapeHtml(formData.wohneinheitenGesamt)}"
                        data-model="wohneinheitenBewohnt"
                        value="${escapeHtml(formData.wohneinheitenBewohnt)}"
                        class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-12"
                      />
                    `)}
                  </div>
                </div>

                ${toNumber(formData.wohneinheitenBewohnt) > 0 ? `
                  <div class="pt-4 border-t border-slate-200">
                    <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">
                      Wie viele der selbstbewohnten WE haben ein Haushalts-Einkommen &lt; 40.000 €?
                    </label>
                    ${this.renderNumberInputWrapper('WE', `
                      <input
                        type="number"
                        min="0"
                        max="${escapeHtml(formData.wohneinheitenBewohnt)}"
                        data-model="eigentuemerUnter40k"
                        value="${escapeHtml(formData.eigentuemerUnter40k)}"
                        class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-12"
                      />
                    `)}
                  </div>
                ` : ''}
              </div>
            ` : ''}

            <div class="grid grid-cols-2 gap-6 mt-6">
              <div>
                <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Personen im Haus</label>
                ${this.renderNumberInputWrapper('Personen', `
                  <input
                    type="number"
                    min="1"
                    max="50"
                    data-model="personenAnzahl"
                    value="${escapeHtml(formData.personenAnzahl)}"
                    class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-24"
                  />
                `)}
              </div>
              <div>
                <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Postleitzahl</label>
                <input
                  type="text"
                  maxlength="5"
                  placeholder="z.B. 10115"
                  data-model="standortPlz"
                  value="${escapeHtml(formData.standortPlz)}"
                  class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors"
                />
              </div>
            </div>

            ${formData.ort ? `
              <div class="text-xs font-bold text-[#93c21c] flex items-center gap-1.5 bg-[#cfe09b]/20 inline-flex px-3 py-1.5 rounded-lg border border-[#cfe09b]">
                ${this.renderIcon('map-pin', 'w-4 h-4')}
                Region erkannt: ${escapeHtml(formData.ort)}
              </div>
            ` : ''}
          </div>

          <button
            data-action="next-quiz-step"
            ${String(formData.standortPlz).length < 5 ? 'disabled' : ''}
            class="mt-10 w-full bg-[#74b2d4] text-white text-lg font-black py-5 rounded-2xl flex items-center justify-center gap-2 disabled:opacity-50 hover:bg-[#5c8fa8] transition-all hover:"
          >
            Weiter zur Heizung
            ${this.renderIcon('arrow-right', 'w-5 h-5')}
          </button>
        </div>
      `;
    }

    if (quizStep === 1) {
      return `
        <div class="animate-slide-up text-left">
          <h2 class="text-2xl md:text-3xl font-black text-slate-800 mb-6">Wie heizt du aktuell?</h2>

          <div class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Heizungsart</label>
                ${this.renderSelectWrapper(`
                  <select
                    data-model="heizungsArt"
                    class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 appearance-none cursor-pointer transition-colors"
                  >
                    ${Object.keys(ENERGIE).map(k => `
                      <option value="${escapeHtml(k)}" ${formData.heizungsArt === k ? 'selected' : ''}>${escapeHtml(k)}</option>
                    `).join('')}
                  </select>
                `)}
              </div>
              <div>
                <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Alter der Altanlage</label>
                ${this.renderNumberInputWrapper('Jahre', `
                  <input
                    type="number"
                    min="0"
                    max="100"
                    data-model="heizungsAlter"
                    value="${escapeHtml(formData.heizungsAlter)}"
                    class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-20"
                  />
                `)}
              </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Wärmeübergabe</label>
                ${this.renderSelectWrapper(`
                  <select
                    data-model="heizsystem"
                    class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 appearance-none cursor-pointer transition-colors"
                  >
                    <option value="Heizkörper" ${formData.heizsystem === 'Heizkörper' ? 'selected' : ''}>Klassische Heizkörper</option>
                    <option value="Fußbodenheizung" ${formData.heizsystem === 'Fußbodenheizung' ? 'selected' : ''}>Fußbodenheizung</option>
                    <option value="Beides" ${formData.heizsystem === 'Beides' ? 'selected' : ''}>Gemischt (Beides)</option>
                  </select>
                `)}
              </div>
              <div>
                <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide flex justify-between">
                  <span>Erwartete JAZ (WP)</span>
                  <span class="text-slate-400 font-normal">Optional</span>
                </label>
                ${this.renderNumberInputWrapper('JAZ', `
                  <input
                    type="number"
                    step="0.1"
                    placeholder="${formData.heizsystem === 'Fußbodenheizung' ? '4.5' : formData.heizsystem === 'Beides' ? '4.0' : '3.5'}"
                    data-model="customJaz"
                    value="${escapeHtml(formData.customJaz)}"
                    class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-16"
                  />
                `)}
              </div>
            </div>

            <div>
              <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">
                Vorheriger Jahres-Verbrauch (ca.)
              </label>
              ${this.renderNumberInputWrapper(`${ENERGIE[formData.heizungsArt].einheit} / Jahr`, `
                <input
                  type="number"
                  step="100"
                  data-model="heizVerbrauch"
                  value="${escapeHtml(formData.heizVerbrauch)}"
                  class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-24"
                />
              `)}
            </div>
          </div>

          <button
            data-action="next-quiz-step"
            class="mt-10 w-full bg-[#74b2d4] text-white text-lg font-black py-5 rounded-2xl flex items-center justify-center gap-2 hover:bg-[#5c8fa8] transition-all hover:"
          >
            Weiter zum Strom
            ${this.renderIcon('arrow-right', 'w-5 h-5')}
          </button>
        </div>
      `;
    }

    return `
      <div class="animate-slide-up text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 mb-6">Strom & Mobilität</h2>

        <div class="space-y-6">
          <div>
            <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Allgemeiner Stromverbrauch (Haus)</label>
            ${this.renderNumberInputWrapper('kWh / Jahr', `
              <input
                type="number"
                step="100"
                data-model="stromverbrauch"
                value="${escapeHtml(formData.stromverbrauch)}"
                class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-28"
              />
            `)}
          </div>
          <div>
            <label class="text-xs font-bold text-slate-600 block mb-2 uppercase tracking-wide">Geplante E-Auto Fahrleistung (0 = Keins)</label>
            ${this.renderNumberInputWrapper('km / Jahr', `
              <input
                type="number"
                step="500"
                data-model="kmProJahr"
                value="${escapeHtml(formData.kmProJahr)}"
                class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-slate-700 transition-colors pr-28"
              />
            `)}
          </div>
        </div>

        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <button
            data-action="calculate-complete"
            class="w-full bg-[#93c21c] text-white font-black py-5 rounded-2xl shadow-[0_8px_20px_rgba(147,194,28,0.3)] hover:shadow-[0_12px_25px_rgba(147,194,28,0.4)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2 text-sm md:text-base"
          >
            Komplettsystem (Inkl. PV)
            ${this.renderIcon('calculator', 'w-5 h-5')}
          </button>
          <button
            data-action="calculate-wp-only"
            class="w-full bg-[#74b2d4] text-white font-black py-5 rounded-2xl shadow-[0_8px_20px_rgba(116,178,212,0.3)] hover:shadow-[0_12px_25px_rgba(116,178,212,0.4)] hover:-translate-y-1 transition-all flex items-center justify-center gap-2 text-sm md:text-base"
          >
            Nur Wärmepumpe (Ohne PV)
            ${this.renderIcon('target', 'w-5 h-5')}
          </button>
        </div>
      </div>
    `;
  };

  App.renderProjectModal = function () {
    const { showProjectModal, savedProjects, currentProjectId, newProjectName, stage, formData } = this.state;
    if (!showProjectModal) return '';

    return `
      <div class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
          <div class="flex justify-between items-center p-6 border-b border-slate-200 bg-slate-50">
            <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
              ${this.renderIcon('folder-open', 'w-6 h-6 text-[#74b2d4]')}
              Meine Projekte
            </h2>
            <button data-action="close-project-modal" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
              ${this.renderIcon('x', 'w-6 h-6 text-slate-500')}
            </button>
          </div>

          <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
            ${(stage === 'DASHBOARD' || String(formData.standortPlz).length > 3) ? `
              <div class="bg-white p-5 rounded-2xl border border-[#74b2d4] shadow-sm mb-6">
                <h3 class="text-sm font-black text-[#74b2d4] mb-3 flex items-center gap-2">
                  ${this.renderIcon('save', 'w-4 h-4')}
                  Aktuellen Stand speichern
                </h3>
                <div class="flex flex-col sm:flex-row gap-3">
                  <input
                    type="text"
                    data-project-name
                    placeholder="Name des Projekts (z.B. Adresse oder Kunde)"
                    value="${escapeHtml(newProjectName)}"
                    class="flex-1 bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-sm"
                  />
                  <button data-action="save-project" class="bg-[#74b2d4] hover:bg-[#5c8fa8] text-white font-bold py-3 px-6 rounded-xl transition-colors shrink-0">
                    ${currentProjectId ? 'Überschreiben' : 'Speichern'}
                  </button>
                  ${currentProjectId ? `
                    <button data-action="save-project-new" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl transition-colors shrink-0 flex items-center gap-2" title="Als neues Projekt anlegen">
                      ${this.renderIcon('file-plus', 'w-4 h-4')}
                      Als Neu
                    </button>
                  ` : ''}
                </div>
              </div>
            ` : ''}

            <h3 class="text-sm font-black text-slate-700 mb-3 uppercase tracking-widest pl-1">Gespeicherte Projekte</h3>

            ${savedProjects.length === 0 ? `
              <div class="text-center p-8 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 font-medium">
                Bisher keine Projekte gespeichert. Berechne ein Projekt und speichere es hier ab.
              </div>
            ` : `
              <div class="space-y-3">
                ${savedProjects.map(p => `
                  <div class="bg-white p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all ${currentProjectId === p.id ? 'border-[#93c21c]  ring-1 ring-[#93c21c]' : 'border-slate-200 hover:border-[#74b2d4]'}">
                    <div>
                      <h4 class="font-black text-slate-800 flex items-center gap-2">
                        ${escapeHtml(p.name)}
                        ${currentProjectId === p.id ? `<span class="text-[10px] bg-[#93c21c] text-white px-2 py-0.5 rounded-full uppercase tracking-wider">Aktiv</span>` : ''}
                      </h4>
                      <p class="text-xs text-slate-500 font-medium mt-1">
                        Erstellt: ${formatDateDE(p.createdAt)} • Letzte Änd.: ${formatDateDE(p.updatedAt)}
                      </p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                      <button data-action="load-project" data-id="${escapeHtml(p.id)}" class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-lg text-sm transition-colors text-center">
                        Laden
                      </button>
                      <button data-action="delete-project" data-id="${escapeHtml(p.id)}" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-lg transition-colors" title="Projekt löschen">
                        ${this.renderIcon('trash-2', 'w-5 h-5')}
                      </button>
                    </div>
                  </div>
                `).join('')}
              </div>
            `}
          </div>

          <div class="p-4 border-t border-slate-200 bg-white flex justify-center">
            <button data-action="new-empty-project" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 font-bold text-sm transition-colors">
              ${this.renderIcon('plus', 'w-4 h-4')}
              Neues leeres Projekt starten
            </button>
          </div>
        </div>
      </div>
    `;
  };

  App.renderContactModal = function (ergebnis) {
    const { showContactModal, contactSuccess, formData } = this.state;
    if (!showContactModal) return '';

    return `
      <div class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
        <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full overflow-hidden flex flex-col relative">
          <button data-action="close-contact-modal" class="absolute top-4 right-4 p-2 bg-slate-100 hover:bg-slate-200 rounded-full transition-colors z-10">
            ${this.renderIcon('x', 'w-5 h-5 text-slate-500')}
          </button>

          ${contactSuccess ? `
            <div class="p-10 text-center flex flex-col items-center">
              <div class="w-20 h-20 bg-[#93c21c]/20 rounded-full flex items-center justify-center mb-6">
                ${this.renderIcon('check-circle-2', 'w-12 h-12 text-[#93c21c]')}
              </div>
              <h2 class="text-2xl font-black text-slate-800 mb-2">Gutschein gesichert!</h2>
              <p class="text-slate-600 mb-6">
                Vielen Dank für deine Anfrage. Einer unserer Experten wird sich in Kürze unter der angegebenen Nummer bei dir melden, um deinen Vor-Ort-Termin zu vereinbaren.
              </p>
              <button data-action="close-contact-modal" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 px-8 rounded-xl transition-colors">
                Schließen
              </button>
            </div>
          ` : ` 
              <div class="bg-[#74b2d4] p-8 text-white text-center">
                ${this.renderIcon('gift', 'w-12 h-12 text-white/80 mx-auto mb-3')}
                <h2 class="text-2xl font-black mb-1">Sichere dir ${formatInt(ergebnis.gutscheinSumme)} € Rabatt</h2>
                <p class="text-white/80 text-sm">
                  Hinterlasse uns deine Kontaktdaten für einen kostenlosen Vor-Ort-Check. Der Gutschein wird sofort auf deinen Namen reserviert.
                </p>
              </div>

              <div class="p-6 md:p-8 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-1">Vorname</label>
                    <input type="text" data-model="vorname" value="${escapeHtml(formData.vorname)}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-1">Nachname</label>
                    <input type="text" data-model="nachname" value="${escapeHtml(formData.nachname)}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-sm"/>
                  </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                  <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-600 block mb-1">Straße</label>
                    <input type="text" data-model="strasse" value="${escapeHtml(formData.strasse)}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-sm"/>
                  </div>
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-1">Hausnummer</label>
                    <input type="text" data-model="hausnummer" value="${escapeHtml(formData.hausnummer)}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-sm"/>
                  </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-1">PLZ</label>
                    <input type="text" value="${escapeHtml(formData.standortPlz)}" disabled class="w-full bg-slate-100 border border-slate-200 p-3 rounded-xl text-slate-500 font-bold text-sm"/>
                  </div>
                  <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-600 block mb-1">Ort</label>
                    <input type="text" value="${escapeHtml(formData.ort)}" disabled class="w-full bg-slate-100 border border-slate-200 p-3 rounded-xl text-slate-500 font-bold text-sm"/>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-1">E-Mail Adresse</label>
                    <div class="relative">
                      ${this.renderIcon('mail', 'absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400')}
                      <input type="email" data-model="email" value="${escapeHtml(formData.email)}" class="w-full bg-slate-50 border border-slate-200 p-3 pl-10 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-sm"/>
                    </div>
                  </div>
                  <div>
                    <label class="text-xs font-bold text-slate-600 block mb-1">Telefonnummer</label>
                    <div class="relative">
                      ${this.renderIcon('phone', 'absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400')}
                      <input type="tel" data-model="telefon" value="${escapeHtml(formData.telefon)}" class="w-full bg-slate-50 border border-slate-200 p-3 pl-10 rounded-xl outline-none focus:border-[#74b2d4] font-bold text-sm"/>
                    </div>
                  </div>
                </div>

                <button data-action="submit-contact-lead" class="mt-4 w-full bg-[#93c21c] hover:bg-[#82ad18] text-white font-black py-4 rounded-xl  transition-transform hover:scale-[1.02] flex items-center justify-center gap-2">
                  Gutschein sichern & Termin anfragen
                  ${this.renderIcon('arrow-right', 'w-5 h-5')}
                </button>

                <p class="text-[10px] text-center text-slate-400">
                  Deine Daten werden sicher übertragen und nur für die Kontaktaufnahme verwendet.
                </p>
              </div>
            
          `}
        </div>
      </div>
    `;
  };

  

  App.renderCalculationModal = function (ergebnis) {
    if (!this.state.showCalculationModal) return '';
    const { formData } = this.state;

    return `
      <div class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
        <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
          <div class="flex justify-between items-center p-6 border-b border-slate-200 bg-slate-50">
            <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
              ${this.renderIcon('calculator', 'w-6 h-6 text-[#74b2d4]')}
              Mathematischer Prüfbericht
            </h2>
            <button data-action="close-calc-modal" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
              ${this.renderIcon('x', 'w-6 h-6 text-slate-500')}
            </button>
          </div>

          <div class="overflow-y-auto p-6 space-y-8">
            <section>
              <h3 class="text-sm font-black text-[#74b2d4] uppercase tracking-widest border-b border-[#c0d8ea] pb-2 mb-4 flex items-center gap-2">
                ${this.renderIcon('flame', 'w-4 h-4')}
                1. Ermittlung Strombedarf
              </h3>
              <div class="bg-slate-50 p-4 rounded-xl font-mono text-sm space-y-3 text-slate-700">
                <div class="flex justify-between"><span>Verbrauch Fossil (Vorher):</span> <span>${escapeHtml(formData.heizVerbrauch)} ${escapeHtml(ENERGIE[formData.heizungsArt].einheit)}</span></div>
                <div class="flex justify-between"><span>Umrechnung in kWh (Faktor ${ENERGIE[formData.heizungsArt].faktorKWh}):</span> <span>${formatInt(ergebnis.waermeBedarfKWhFossil)} kWh</span></div>
                <div class="flex justify-between text-slate-500"><span>Abzug Kesselverluste (Alter ${ergebnis.alterHeizung} J. = ${Math.round(ergebnis.wirkungsgradVerlust * 100)} % Verlust):</span> <span>- ${formatInt(ergebnis.waermeBedarfKWhFossil * ergebnis.wirkungsgradVerlust)} kWh</span></div>
                <div class="flex justify-between font-bold border-t border-slate-200 pt-2"><span>= Reale Nutzwärme (Gebäude):</span> <span>${formatInt(ergebnis.report.nutzWaermeKWh)} kWh</span></div>

                ${formData.includeWp ? `
                  <div class="flex justify-between"><span>Geteilt durch Effizienz (Faktor JAZ: ${ergebnis.jaz}):</span> <span>÷ ${ergebnis.jaz}</span></div>
                  <div class="flex justify-between font-bold text-[#74b2d4] border-t border-slate-200 pt-2"><span>= Strombedarf Wärmepumpe:</span> <span>${formatInt(ergebnis.wpStrombedarf)} kWh</span></div>
                ` : `
                  <div class="flex justify-between text-slate-400 mt-2"><span>*Wärmepumpe abgewählt. Kein WP-Strom berechnet.</span></div>
                `}

                ${ergebnis.activeAuto ? `
                  <div class="flex justify-between font-bold text-[#74b2d4] border-t border-slate-200 pt-2 mt-2"><span>+ Strombedarf E-Auto (${escapeHtml(formData.kmProJahr)} km):</span> <span>${formatInt(ergebnis.autoStrombedarf)} kWh</span></div>
                ` : ''}
              </div>
            </section>

            ${formData.includeWp ? `
              <section>
                <h3 class="text-sm font-black text-[#93c21c] uppercase tracking-widest border-b border-[#cfe09b] pb-2 mb-4 flex items-center gap-2">
                  ${this.renderIcon('variable', 'w-4 h-4')}
                  2. BEG-Förderlogik (KfW)
                </h3>
                <div class="bg-slate-50 p-4 rounded-xl font-mono text-sm space-y-3 text-slate-700">
                  ${formData.gebaeudeArt === 'Einfamilienhaus' ? `
                    <div class="flex justify-between"><span>Tatsächliche Investition (NUR WP):</span> <span>${formatInt(ergebnis.investWPBrutto)} €</span></div>
                    <div class="flex justify-between text-slate-500"><span>Förderfähige Kosten (Deckelung BEG):</span> <span>max. ${formatInt(ergebnis.report.rep_foerderFaehigeKosten)} €</span></div>

                    <div class="py-2 ml-4 border-l-2 border-slate-200 pl-4 space-y-1 text-xs">
                      <div class="flex justify-between"><span>Grundförderung:</span> <span>30 %</span></div>
                      <div class="flex justify-between"><span>Natürliches Kältemittel-Bonus (R290):</span> <span>+5 %</span></div>
                      <div class="flex justify-between"><span>Klimageschwindigkeits-Bonus:</span> <span>+${ergebnis.report.aktKlimaBonus} %</span></div>
                      ${ergebnis.report.rep_einkommensBonus > 0 ? `<div class="flex justify-between"><span>Einkommens-Bonus (&lt; 40.000 €):</span> <span>+${ergebnis.report.rep_einkommensBonus} %</span></div>` : ''}
                      <div class="flex justify-between font-bold text-[#93c21c] border-t border-slate-200 pt-1"><span>Summe Fördersatz (Gedeckelt auf 70 %):</span> <span>${ergebnis.report.rep_kfwProzent} %</span></div>
                    </div>

                    <div class="flex justify-between font-bold text-[#93c21c] border-t border-slate-200 pt-2"><span>= Maximaler Zuschuss (WP):</span> <span>${formatInt(ergebnis.kfwZuschussMax)} €</span></div>
                  ` : `
                    <div class="space-y-2">
                      <p class="text-slate-600 font-sans leading-relaxed">Detaillierte Mehrfamilienhaus-Staffelung angewandt:</p>
                      <ul class="list-disc pl-5 text-slate-500 font-sans text-xs space-y-1">
                        <li><strong>Deckel pro Einheit:</strong> 30.000 € (1. WE), 15.000 € (2.-6. WE), 8.000 € (ab 7. WE)</li>
                        <li><strong>Eigennutzung (${escapeHtml(formData.wohneinheitenBewohnt)} WE):</strong> Max. ${Math.min(BEG_PARAMS.maxProzent, 30 + EFFIZIENZ_BONUS + ergebnis.report.aktKlimaBonus)}%</li>
                        ${toNumber(formData.eigentuemerUnter40k) > 0 ? `<li><strong>Davon mit Einkommens-Bonus (${escapeHtml(formData.eigentuemerUnter40k)} WE):</strong> Max. ${Math.min(BEG_PARAMS.maxProzent, 30 + EFFIZIENZ_BONUS + ergebnis.report.aktKlimaBonus + BEG_PARAMS.einkommen)}%</li>` : ''}
                        <li><strong>Vermietung:</strong> Max. ${30 + EFFIZIENZ_BONUS}%</li>
                      </ul>
                      <div class="flex justify-between font-bold text-[#93c21c] border-t border-slate-200 pt-2 mt-2 font-mono text-sm"><span>= Maximaler Zuschuss Gesamt</span> <span>${formatInt(ergebnis.kfwZuschussMax)} €</span></div>
                    </div>
                  `}
                </div>
              </section>
            ` : ''}

            ${formData.includeSolar ? `
              <section>
                <h3 class="text-sm font-black text-slate-600 uppercase tracking-widest border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                  ${this.renderIcon('check-square', 'w-4 h-4')}
                  3. Eigener Strompreis (Erzeugungskosten)
                </h3>
                <div class="bg-slate-50 p-4 rounded-xl font-mono text-sm space-y-3 text-slate-700">
                  <div class="flex justify-between"><span>Netto-Investition PV + Speicher:</span> <span>${formatInt(ergebnis.investPvNetto + ergebnis.investSpeicherNetto)} €</span></div>
                  <div class="flex justify-between text-slate-500"><span>+ Betriebs- & Wartungskosten (30 J.):</span> <span>+${formatInt(ergebnis.report.opex30YearsPV)} €</span></div>
                  <div class="flex justify-between font-bold border-t border-slate-200 pt-2"><span>= Gesamtkosten PV-System (30 J.):</span> <span>${formatInt(ergebnis.investPvNetto + ergebnis.investSpeicherNetto + ergebnis.report.opex30YearsPV)} €</span></div>
                  <div class="flex justify-between mt-2"><span>Erwarteter Gesamtertrag (30 J.):</span> <span>÷ ${Array.from({length: 30}, (_, i) => ergebnis.pvProduktion * Math.pow(0.995, i)).reduce((a, b) => a + b, 0).toLocaleString('de-DE', {maximumFractionDigits:0})} kWh</span></div>
                  <div class="flex justify-between font-bold text-[#74b2d4] border-t border-slate-200 pt-2"><span>= Gestehungskosten pro kWh:</span> <span>${ergebnis.solarstromPreis.toFixed(4).replace('.', ',')} €</span></div>
                </div>
              </section>
            ` : ''}

            <section>
              <h3 class="text-sm font-black text-slate-600 uppercase tracking-widest border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                ${this.renderIcon('bar-chart-3', 'w-4 h-4')}
                4. Gesamtkosten (inkl. Anlage & Strom)
              </h3>
              <div class="bg-slate-50 p-4 rounded-xl font-sans text-sm space-y-3 text-slate-700">
                <p>
                  Die <strong>"Gesamtkosten (Neu)"</strong> im Dashboard und PDF berechnen sich aus der anfänglichen Netto-Investition zzgl. der aufsummierten laufenden Kosten (inkl. Wartung ${formatInt(ergebnis.report.wartungGesamt_pa)} € / Jahr, Netzstrom und Ersatzinvestitionen).
                </p>
                <p>
                  Die <strong>"Gesamt-Bilanz"</strong> vergleicht diese Gesamtkosten mit den fiktiven, rein kumulierten Altkosten. Sobald dieser Wert ins Plus dreht, hast du den Amortisationszeitpunkt erreicht.
                </p>
              </div>
            </section>
          </div>
        </div>
      </div>
    `;
  };

  App.renderPdfPage = function (inner, extraClass = '') {
    return `
      <section
        class="pdf-page bg-white ${extraClass}"
        style="
          width:794px;
          min-height:1123px;
          box-sizing:border-box;
          padding:56px;
          page-break-after: always;
          break-after: page;
        "
      >
        ${inner}
      </section>
    `;
  };

 
  App.getPdfBrand = function () {
  const formData = this.state.formData || {};
  const brand = BRANDS[formData.brand] || BRANDS.solar_aspekt;

  return {
    name: brand.name || 'SOLAR ASPEKT',
    primary: brand.primary || '#93c21c',
    primarySoft: brand.primarySoft || '#cfe09b',
    secondary: brand.secondary || '#74b2d4',
    secondarySoft: brand.secondarySoft || '#c0d8ea',
    logo: brand.logo || window.APP_LOGO_URL || '/logo/logo.png',
    pageLogo: brand.pageLogo || brand.logo || window.APP_LOGO_URL || '/logo/logo.png'
  };
};

App.renderPdfHeaderBrand = function (pageNo, title, brand) {
  return `
    <div style="margin-bottom:24px; font-family:Inter,Arial,sans-serif;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
          <div style="font-size:9pt; font-weight:900; text-transform:uppercase; letter-spacing:.18em; color:${brand.primary};">
            Individuelle Energieanalyse
          </div>
          <div style="font-size:18pt; font-weight:900; color:#0f172a; margin-top:10px; text-transform:uppercase;">
            ${escapeHtml(title || '')}
          </div>
        </div>

        <div style="display:flex; align-items:center; justify-content:flex-end;">
          <img
            src="${brand.pageLogo}"
            alt="${escapeHtml(brand.name)}"
            style="max-width:200px; max-height:44px; object-fit:contain;"
          />
        </div>
      </div>

      <div style="height:3px; width:100%; background:${brand.primary}; border-radius:999px;"></div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
        <div style="font-size:8pt; color:#64748b; font-weight:700;">
          ${escapeHtml(brand.name)} – Wirtschaftlichkeitsberechnung
        </div>
        <div style="font-size:8pt; color:#64748b; font-weight:700;">
          Seite ${pageNo}
        </div>
      </div>
    </div>
  `;
};

App.renderPdfFooterBrand = function (brand) {
  return `
    <div style="position:absolute; left:56px; right:56px; bottom:22px; font-family:Inter,Arial,sans-serif;">
      <div style="height:2px; background:${brand.primary}; border-radius:999px; margin-bottom:8px;"></div>
      <div style="display:flex; justify-content:space-between; align-items:center; font-size:8pt; color:#64748b; font-weight:700;">
        <span>${escapeHtml(brand.name)}</span>
        <span>www.solar-aspekt.de</span>
      </div>
    </div>
  `;
};

App.renderPdfPageShell = function ({ title, pageNo, content, brand, noBreak = false, bg = '#ffffff' }) {
  return `
    <section
      class="pdf-page ${noBreak ? 'no-page-break' : ''}"
      style="
        width:794px;
        min-height:1123px;
        box-sizing:border-box;
        padding:42px 56px 56px 56px;
        background:${bg};
        position:relative;
        page-break-after:${noBreak ? 'auto' : 'always'};
        break-after:${noBreak ? 'auto' : 'page'};
        overflow:hidden;
        font-family:Inter,Arial,sans-serif;
      "
    >
      ${this.renderPdfHeaderBrand(pageNo, title, brand)}
      <div>${content || ''}</div>
      ${this.renderPdfFooterBrand(brand)}
    </section>
  `;
};

App.renderPdfCoverPage = function (ergebnis, brand) {
  const formData = this.state.formData || {};
  const customerName = [formData.vorname, formData.nachname].filter(Boolean).join(' ').trim() || 'Ihr Objekt';

  return `
    <section
      class="pdf-page"
      style="
        width:794px;
        height:1123px;
        background:#f8fafc;
        position:relative;
        overflow:hidden;
        page-break-after:always;
        break-after:page;
        font-family:Inter,Arial,sans-serif;
      "
    >
      <div style="
        position:absolute;
        top:0; left:0; right:0;
        height:320px;
        background:${brand.primary};
        border-bottom-left-radius:140px;
        border-bottom-right-radius:140px;
      "></div>

      <div style="position:absolute; top:58px; left:60px; right:60px; display:flex; justify-content:center;">
        <img
          src="${brand.logo}"
          alt="${escapeHtml(brand.name)}"
          style="max-height:70px; max-width:280px; object-fit:contain; filter:brightness(0) invert(1);"
        />
      </div>

      <div style="
        position:absolute;
        left:50%;
        top:250px;
        transform:translateX(-50%);
        width:640px;
        background:#fff;
        border-radius:36px;
        padding:56px 46px;
        box-shadow:0 24px 60px rgba(15,23,42,.12);
        text-align:center;
      ">
        <div style="font-size:11pt; font-weight:900; letter-spacing:.22em; color:${brand.secondary}; text-transform:uppercase; margin-bottom:16px;">
          Ihr individuelles Energiekonzept
        </div>

        <h1 style="margin:0; font-size:33pt; line-height:1.08; font-weight:900; color:#0f172a; text-transform:uppercase;">
          Wirtschaftlichkeits-<br>berechnung
        </h1>

        <div style="width:70px; height:6px; margin:24px auto; border-radius:999px; background:${brand.primary};"></div>

        <div style="font-size:18pt; color:#334155; font-weight:700; margin-bottom:10px;">
          Für ${escapeHtml(customerName)}
        </div>

        <div style="font-size:11pt; color:#64748b;">
          Standort: ${escapeHtml(formData.standortPlz || '—')} ${formData.ort ? '· ' + escapeHtml(formData.ort) : ''}
        </div>
      </div>

      <div style="position:absolute; bottom:90px; left:0; right:0; text-align:center;">
        <div style="font-size:10pt; color:#94a3b8; font-weight:800; text-transform:uppercase; letter-spacing:.15em; margin-bottom:10px;">
          Ausgearbeitet von
        </div>
        <div style="font-size:18pt; font-weight:900; color:#0f172a;">
          ${escapeHtml(brand.name)}
        </div>
        <div style="font-size:10pt; color:#64748b; margin-top:6px;">
          Photovoltaik · Wärmepumpe · Speicher · E-Mobilität
        </div>
      </div>
    </section>
  `;
};

App.renderPdfRing = function (percent, color, size = 96, stroke = 10) {
  const pct = Math.max(0, Math.min(100, Math.round(percent || 0)));
  const r = (size - stroke) / 2;
  const c = 2 * Math.PI * r;
  const offset = c - (c * pct / 100);

  return `
    <div style="position:relative; width:${size}px; height:${size}px; margin:0 auto;">
      <svg viewBox="0 0 ${size} ${size}" style="width:${size}px; height:${size}px; transform:rotate(-90deg);">
        <circle cx="${size / 2}" cy="${size / 2}" r="${r}" fill="none" stroke="#e2e8f0" stroke-width="${stroke}"></circle>
        <circle cx="${size / 2}" cy="${size / 2}" r="${r}" fill="none" stroke="${color}" stroke-width="${stroke}" stroke-linecap="round" stroke-dasharray="${c}" stroke-dashoffset="${offset}"></circle>
      </svg>
      <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-weight:900; color:#0f172a; font-size:18px;">
        ${pct}%
      </div>
    </div>
  `;
};

App.renderPdfBarChart = function (chartData, brand) {
  if (!Array.isArray(chartData) || !chartData.length) return '';

  const w = 610;
  const h = 180;
  const barW = 12;
  const gap = w / chartData.length;
  const maxVal = Math.max(...chartData.map(d => Math.max(d.Solarertrag || 0, d.Gesamtbedarf || 0, 1))) * 1.1;

  const monthMap = { Jan:'Jan', Feb:'Feb', Mär:'Mrz', Apr:'Apr', Mai:'Mai', Jun:'Jun', Jul:'Jul', Aug:'Aug', Sep:'Sep', Okt:'Okt', Nov:'Nov', Dez:'Dez' };

  const bars = chartData.map((d, i) => {
    const pv = Number(d.Solarertrag || 0);
    const need = Number(d.Gesamtbedarf || 0);
    const pvH = (pv / maxVal) * h;
    const needH = (need / maxVal) * h;
    const x = i * gap + (gap / 2) - barW;

    return `
      <rect x="${x}" y="${h - pvH}" width="${barW}" height="${pvH}" fill="${brand.primary}" rx="2"></rect>
      <rect x="${x + barW + 3}" y="${h - needH}" width="${barW}" height="${needH}" fill="#cbd5e1" rx="2"></rect>
      <text x="${x + barW}" y="${h + 15}" font-size="9" font-family="Arial,sans-serif" fill="#64748b" text-anchor="middle">
        ${monthMap[d.name] || d.name}
      </text>
    `;
  }).join('');

  return `
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px;">
      <svg viewBox="0 0 ${w} ${h + 22}" style="width:100%; height:220px; overflow:visible;">
        ${bars}
      </svg>
      <div style="display:flex; gap:20px; justify-content:center; margin-top:10px; font-size:10pt; font-weight:700; color:#475569;">
        <span style="display:flex; align-items:center; gap:6px;"><span style="width:10px; height:10px; border-radius:999px; background:${brand.primary}; display:inline-block;"></span> PV-Ertrag</span>
        <span style="display:flex; align-items:center; gap:6px;"><span style="width:10px; height:10px; border-radius:999px; background:#cbd5e1; display:inline-block;"></span> Bedarf</span>
      </div>
    </div>
  `;
};

App.renderPdfLineChart = function (ergebnis, brand) {
  const w = 610;
  const h = 190;
  const points = [];

  for (let i = 1; i <= 30; i++) {
    points.push({
      year: i,
      fossil: Number(ergebnis.opexFossilReihe?.[i] || 0),
      solar: Number(ergebnis.tcoSolarReihe?.[i] || 0)
    });
  }

  const maxVal = Math.max(...points.map(p => Math.max(p.fossil, p.solar, 1))) * 1.05;

  let pathFossil = '';
  let pathSolar = '';

  points.forEach((p, i) => {
    const x = (i / (points.length - 1)) * w;
    const y1 = h - (p.fossil / maxVal) * h;
    const y2 = h - (p.solar / maxVal) * h;

    pathFossil += `${i === 0 ? 'M' : 'L'} ${x} ${y1} `;
    pathSolar += `${i === 0 ? 'M' : 'L'} ${x} ${y2} `;
  });

  return `
    <div style="background:#ffffff; border-radius:22px; padding:20px; border:1px solid #e2e8f0;">
      <div style="font-size:14px; font-weight:900; color:#0f172a; margin-bottom:12px;">
        Kostenentwicklung über 30 Jahre
      </div>

      <svg viewBox="0 0 ${w} ${h + 32}" style="width:100%; height:235px; overflow:visible;">
        <line x1="0" y1="${h}" x2="${w}" y2="${h}" stroke="#cbd5e1" stroke-width="1"></line>

        ${[0.25, 0.5, 0.75].map(ratio => {
          const y = h - (h * ratio);
          return `<line x1="0" y1="${y}" x2="${w}" y2="${y}" stroke="#eef2f7" stroke-width="1"></line>`;
        }).join('')}

        <path d="${pathFossil}" fill="none" stroke="${brand.secondary}" stroke-width="3"></path>
        <path d="${pathSolar}" fill="none" stroke="${brand.primary}" stroke-width="4"></path>

        ${[1, 10, 20, 30].map(year => {
          const x = ((year - 1) / 29) * w;
          return `
            <line x1="${x}" y1="${h}" x2="${x}" y2="${h + 5}" stroke="#94a3b8" stroke-width="1"></line>
            <text x="${x}" y="${h + 18}" fill="#64748b" font-size="10" text-anchor="middle">${year}</text>
          `;
        }).join('')}
      </svg>

      <div style="display:flex; gap:18px; justify-content:center; margin-top:10px; font-size:10pt; font-weight:700; color:#475569;">
        <span style="display:flex; align-items:center; gap:6px;">
          <span style="width:10px; height:10px; border-radius:999px; background:${brand.secondary}; display:inline-block;"></span>
          Ohne Wechsel
        </span>
        <span style="display:flex; align-items:center; gap:6px;">
          <span style="width:10px; height:10px; border-radius:999px; background:${brand.primary}; display:inline-block;"></span>
          Mit System
        </span>
      </div>
    </div>
  `;
};

App.renderPdfTechAdvantagesPage = function (ergebnis, brand) {
  return `
    <div style="margin-bottom:22px;">
      <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 10px 0;">
        Werk Studio & Ihre Technologie-Vorteile
      </h2>
      <p style="font-size:13px; line-height:1.75; color:#475569; margin:0;">
        Als ganzheitlicher Systemanbieter verbinden wir Planung, Förderung, Installation und intelligente Systemvernetzung zu einer wirtschaftlichen Gesamtlösung.
      </p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
      ${[
        ['Meisterbetrieb SHK & Elektro', 'Höchste handwerkliche Präzision durch gewerkeübergreifende Kompetenz.'],
        ['Alles aus einer Hand', 'Ein Ansprechpartner für Beratung, Planung, Förderung, Montage und Betreuung.'],
        ['Premium Produktqualität', 'Langlebige, erprobte und effiziente Komponenten für Jahrzehnte.'],
        ['Langjährige Erfahrung', 'Praxisbewährte Umsetzung komplexer Energiesysteme mit Fokus auf Wirtschaftlichkeit.']
      ].map(item => `
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px;">
          <div style="font-size:15px; font-weight:900; color:#0f172a; margin-bottom:8px;">${item[0]}</div>
          <div style="font-size:12px; line-height:1.7; color:#64748b;">${item[1]}</div>
        </div>
      `).join('')}
    </div>

    <div style="display:grid; gap:14px;">
      ${[
        ['Wärmepumpe', 'Effizientes Heizen und optionales Kühlen mit moderner Regelungstechnik.'],
        ['Photovoltaik', 'Eigenes Kraftwerk auf dem Dach für günstigen Solarstrom über Jahrzehnte.'],
        ['Batteriespeicher', 'Zwischenspeicherung des Solarstroms für Nacht, Spitzenlast und Notstromoptionen.'],
        ['Intelligente Vernetzung', 'Haushalt, Wärmepumpe, Speicher und Mobilität arbeiten im Energiemanagement zusammen.']
      ].map(item => `
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:16px;">
          <div style="font-size:13px; font-weight:900; color:${brand.primary}; margin-bottom:6px;">${item[0]}</div>
          <div style="font-size:12px; line-height:1.7; color:#475569;">${item[1]}</div>
        </div>
      `).join('')}
    </div>
  `;
};

App.renderPdfPvBatteryPage = function (ergebnis, brand) {
  return `
    <div style="margin-bottom:22px;">
      <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 10px 0;">
        Photovoltaik & Batteriespeicher
      </h2>
      <p style="font-size:13px; line-height:1.75; color:#475569; margin:0;">
        Photovoltaik senkt den laufenden Strombezug direkt. Mit Speicher steigt der Eigenverbrauch deutlich, weil Überschüsse in die Abend- und Nachtstunden verschoben werden.
      </p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:24px;">
      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:16px; font-weight:900; color:#0f172a; margin-bottom:10px;">Photovoltaik</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          <p style="margin:0 0 10px 0;">Ihre Anlage erzeugt voraussichtlich <strong>${formatInt(ergebnis.pvProduktion)} kWh</strong> Solarstrom pro Jahr.</p>
          <p style="margin:0;">Je höher der direkte Eigenverbrauch, desto stärker sinkt Ihre Abhängigkeit vom Energieversorger.</p>
        </div>
      </div>

      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:16px; font-weight:900; color:#0f172a; margin-bottom:10px;">Speicher</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          <p style="margin:0 0 10px 0;">Mit <strong>${escapeHtml(ergebnis.speicherGroesse)} kWh</strong> Speicherkapazität lässt sich Solarstrom zeitversetzt nutzen.</p>
          <p style="margin:0;">Das verbessert Eigenverbrauch, Autarkie und die Wirtschaftlichkeit des Gesamtsystems.</p>
        </div>
      </div>
    </div>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
      <div style="font-size:13px; font-weight:900; color:${brand.primary}; margin-bottom:10px;">Zentrale Kennzahlen</div>
      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px; text-align:center;">
          <div style="font-size:10px; color:#64748b; text-transform:uppercase; font-weight:900;">PV-Größe</div>
          <div style="font-size:22px; font-weight:900; color:#0f172a;">${escapeHtml(ergebnis.pvGroesse)} kWp</div>
        </div>
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px; text-align:center;">
          <div style="font-size:10px; color:#64748b; text-transform:uppercase; font-weight:900;">Speicher</div>
          <div style="font-size:22px; font-weight:900; color:#0f172a;">${escapeHtml(ergebnis.speicherGroesse)} kWh</div>
        </div>
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px; text-align:center;">
          <div style="font-size:10px; color:#64748b; text-transform:uppercase; font-weight:900;">Eigenverbrauch</div>
          <div style="font-size:22px; font-weight:900; color:#0f172a;">${Math.round(ergebnis.eigenverbrauchQuote)} %</div>
        </div>
      </div>
    </div>
  `;
};
App.renderPdfPerfectTeamPage = function (ergebnis, brand) {
  return `
    <div style="margin-bottom:22px;">
      <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 10px 0;">
        Das perfekte Team: intelligente Autarkie
      </h2>
      <p style="font-size:13px; line-height:1.75; color:#475569; margin:0;">
        Wärmepumpe, Photovoltaik, Speicher und optional E-Mobilität entfalten ihren wirtschaftlichen Vorteil erst dann maximal, wenn sie als Gesamtsystem gedacht werden.
      </p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:22px;">
      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:15px; font-weight:900; color:${brand.primary}; margin-bottom:8px;">Wärmepumpe + Solarstrom</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          Die Wärmepumpe nutzt Strom besonders effizient. Wird dieser teilweise oder überwiegend vom eigenen Dach geliefert, sinken die Heizkosten massiv.
        </div>
      </div>

      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:15px; font-weight:900; color:${brand.secondary}; margin-bottom:8px;">Speicher + Lastverschiebung</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          Tagsüber erzeugte Überschüsse stehen dann zur Verfügung, wenn Haushalt, Auto oder Wärmepumpe Energie benötigen.
        </div>
      </div>
    </div>

    <div style="background:${brand.primarySoft}22; border:1px solid ${brand.primarySoft}; border-radius:18px; padding:20px;">
      <div style="font-size:14px; font-weight:900; color:${brand.primary}; margin-bottom:8px;">Systemeffizienz</div>
      <div style="font-size:12px; line-height:1.8; color:#475569;">
        Genau diese vernetzte Betriebsweise nennen wir intelligente Autarkie: eigener Strom, eigener Speicher, eigene Wärmeerzeugung und optional eigene Ladeinfrastruktur.
      </div>
    </div>
  `;
};

App.renderPdfEmobilityPage = function (ergebnis, brand) {
  const hasAuto = Number(this.state.formData.kmProJahr || 0) > 0;

  return `
    <div style="margin-bottom:22px;">
      <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 10px 0;">
        E-Mobilität: Zapfen Sie die Sonne an
      </h2>
      <p style="font-size:13px; line-height:1.75; color:#475569; margin:0;">
        Mit einer intelligenten Wallbox wird Ihr Fahrzeug zum weiteren Baustein Ihrer Unabhängigkeit. Besonders wirtschaftlich ist das Laden mit eigenem Solarstrom.
      </p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:24px;">
      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:15px; font-weight:900; color:#0f172a; margin-bottom:8px;">Ökologisch & wirtschaftlich</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          Solarstrom für das Fahrzeug erhöht den Eigenverbrauch und reduziert den Bedarf an teurem Netzstrom oder fossilen Kraftstoffen.
        </div>
      </div>

      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:15px; font-weight:900; color:#0f172a; margin-bottom:8px;">Rentabilität steigern</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          Überschüsse werden nicht nur eingespeist, sondern können direkt in Mobilität umgewandelt werden – das steigert die Wirtschaftlichkeit des Gesamtsystems.
        </div>
      </div>
    </div>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:20px; text-align:center;">
      <div style="font-size:10px; text-transform:uppercase; color:#64748b; font-weight:900;">Aktueller Mobilitätsbedarf</div>
      <div style="font-size:28px; font-weight:900; color:${brand.secondary}; margin-top:8px;">
        ${hasAuto ? formatInt(ergebnis.autoStrombedarf) + ' kWh/Jahr' : 'Kein E-Auto berücksichtigt'}
      </div>
    </div>
  `;
};

App.renderPdfSectorCouplingPage = function (ergebnis, brand) {
  return `
    <div style="margin-bottom:22px;">
      <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 10px 0;">
        Sektorkopplung: Eigene Energie
      </h2>
      <p style="font-size:13px; line-height:1.75; color:#475569; margin:0;">
        In einem modernen Energiesystem werden Strom, Wärme und Mobilität nicht getrennt betrachtet. Genau daraus entsteht der große Effizienz- und Kostenvorteil.
      </p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:24px;">
      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:15px; font-weight:900; color:${brand.primary}; margin-bottom:8px;">Synergien nutzen</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          Die Solaranlage versorgt Haushalt, Wärmepumpe und Fahrzeug. Der Speicher puffert Lastspitzen und erhöht die Nutzbarkeit des erzeugten Stroms.
        </div>
      </div>

      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
        <div style="font-size:15px; font-weight:900; color:${brand.secondary}; margin-bottom:8px;">Kosten minimieren</div>
        <div style="font-size:12px; line-height:1.8; color:#475569;">
          Genau dadurch sinken laufende Energiekosten, während Ihre Unabhängigkeit von Preissteigerungen steigt.
        </div>
      </div>
    </div>

    <div style="border:1px solid ${brand.primarySoft}; background:${brand.primarySoft}22; border-radius:20px; padding:22px;">
      <div style="font-size:18px; font-weight:900; color:#0f172a; margin-bottom:8px;">
        Eigener Strom. Eigene Wärme. Eigene Energie.
      </div>
      <div style="font-size:12px; line-height:1.8; color:#475569;">
        Photovoltaik, Speicher, Wärmepumpe und E-Mobilität bilden zusammen Ihr persönliches Energie-Ökosystem.
      </div>
    </div>
  `;
};

App.renderPdfInvestmentDeepPage = function (ergebnis, brand) {
  return `
    <div style="margin-bottom:22px;">
      <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 10px 0;">
        Investition & Wirtschaftlichkeit
      </h2>
      <p style="font-size:13px; line-height:1.75; color:#475569; margin:0;">
        Diese Seite bündelt die Netto-Investition, den Break-Even und den Langzeitvergleich Ihrer Gesamtkosten.
      </p>
    </div>

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px; margin-bottom:20px;">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
        <div>
          <div style="font-size:12px; font-weight:900; color:#64748b; text-transform:uppercase; margin-bottom:10px;">Investitionsübersicht</div>
          <div style="display:grid; gap:8px; font-size:13px;">
            <div style="display:flex; justify-content:space-between;"><span>Gesamtinvestition</span><strong>${formatInt(ergebnis.gesamtInvestBrutto)} €</strong></div>
            <div style="display:flex; justify-content:space-between; color:${brand.primary};"><span>Förderungen & Rabatte</span><strong>- ${formatInt(ergebnis.gesamtFoerderung)} €</strong></div>
            <div style="display:flex; justify-content:space-between; border-top:1px solid #e2e8f0; padding-top:10px; font-size:22px; font-weight:900;"><span>Netto-Investition</span><span>${formatInt(ergebnis.nettoInvestition)} €</span></div>
          </div>
        </div>

        <div style="text-align:center; display:flex; flex-direction:column; justify-content:center; border-left:1px solid #e2e8f0; padding-left:18px;">
          <div style="font-size:11px; color:#64748b; font-weight:900; text-transform:uppercase;">Amortisation</div>
          <div style="font-size:30px; font-weight:900; color:${brand.primary}; margin:8px 0;">
            ${this.formatAmortisation(ergebnis)}
          </div>
          <div style="font-size:13px; color:#475569;">Rendite p.a.: <strong>${escapeHtml(ergebnis.roi)} %</strong></div>
        </div>
      </div>
    </div>

    ${this.renderPdfLineChart(ergebnis, brand)}
  `;
};

App.renderPdfClosingPage = function (ergebnis, brand) {
  return `
    <div style="margin-bottom:24px;">
      <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 10px 0;">
        Klimaschutz & Abschluss
      </h2>
      <p style="font-size:13px; line-height:1.75; color:#475569; margin:0;">
        Neben der finanziellen Entlastung leisten Sie auch einen konkreten Beitrag zum Klimaschutz.
      </p>
    </div>

    <div style="background:${brand.primarySoft}22; border:1px solid ${brand.primarySoft}; border-radius:20px; padding:22px; margin-bottom:24px;">
      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
        <div style="background:#fff; border:1px solid ${brand.primarySoft}; border-radius:14px; padding:14px; text-align:center;">
          <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Pro Jahr</div>
          <div style="font-size:24px; font-weight:900; color:${brand.primary};">${(ergebnis.co2ErsparnisPerYear).toFixed(1).replace('.', ',')} t</div>
        </div>
        <div style="background:#fff; border:1px solid ${brand.primarySoft}; border-radius:14px; padding:14px; text-align:center;">
          <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">10 Jahre</div>
          <div style="font-size:24px; font-weight:900; color:${brand.primary};">${(ergebnis.co2ErsparnisPerYear * 10).toFixed(1).replace('.', ',')} t</div>
        </div>
        <div style="background:#fff; border:1px solid ${brand.primarySoft}; border-radius:14px; padding:14px; text-align:center;">
          <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Bäume</div>
          <div style="font-size:24px; font-weight:900; color:${brand.primary};">${formatInt(ergebnis.co2Baeume)}</div>
        </div>
      </div>
    </div>

    <div style="margin-bottom:24px;">
      <div style="font-size:18px; font-weight:900; color:#0f172a; margin-bottom:16px;">Wie es weitergeht</div>
      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px;">
        ${[
          ['1', 'Vor-Ort-Analyse', 'Prüfung der Gegebenheiten und Feinplanung des Systems.'],
          ['2', 'Fördermittelservice', 'Unterstützung bei Zuschüssen, KfW und Netzanmeldung.'],
          ['✓', 'Umsetzung', 'Montage, Inbetriebnahme und Übergabe aus einer Hand.']
        ].map(item => `
          <div style="text-align:center;">
            <div style="width:46px; height:46px; border-radius:999px; background:${item[0] === '✓' ? brand.primary : '#0f172a'}; color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 12px auto; font-weight:900;">${item[0]}</div>
            <div style="font-weight:900; color:#0f172a; margin-bottom:6px;">${item[1]}</div>
            <div style="font-size:12px; line-height:1.6; color:#64748b;">${item[2]}</div>
          </div>
        `).join('')}
      </div>
    </div>

    <div style="border:2px solid ${brand.primary}; background:#f8fafc; border-radius:22px; padding:24px; display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <div>
        <div style="font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.14em; color:${brand.primary}; margin-bottom:8px;">Sonderaktion</div>
        <div style="font-size:30px; font-weight:900; color:#0f172a;">KOMBI-BONUS: 2.500 €</div>
        <div style="font-size:13px; color:#64748b; margin-top:8px;">Bei Kombination von Wärmepumpe, Photovoltaik und passender Zusatztechnik.</div>
      </div>
      <img src="${brand.logo}" alt="${escapeHtml(brand.name)}" style="max-width:140px; max-height:60px; object-fit:contain;" />
    </div>

    <div style="font-size:9px; line-height:1.7; color:#94a3b8; border-top:1px solid #e2e8f0; padding-top:12px;">
      Die dargestellte Wirtschaftlichkeitsberechnung ist eine Modellrechnung auf Basis der aktuell angesetzten Eingaben, Energiepreise und typischer Verbrauchsannahmen. Reale Werte können je nach Nutzungsverhalten, Witterung, Laufzeiten und Preisentwicklung abweichen.
    </div>
  `;
};


App.renderPdfPreview = function (ergebnis) {
  if (!this.state.showPdfPreview) return '';

  const { formData } = this.state;
  const brand = this.getPdfBrand();

  const customerName = [formData.vorname, formData.nachname].filter(Boolean).join(' ').trim() || 'Ihr Objekt';
  const oldTotalYear = ergebnis.stromKostenHeute + ergebnis.heizKostenHeute + ergebnis.kostenAutoHeute + ergebnis.wartungAlt_pa;
  const newTotalYear = ergebnis.kostenGesamtNeu + ergebnis.report.wartungGesamt_pa;
  const energySaved = Math.max(0, ergebnis.waermeBedarfKWhFossil + ergebnis.hausStrombedarf - ergebnis.restStromBezug);

  const coverPage = this.renderPdfCoverPage(ergebnis, brand);

  const page1 = this.renderPdfPageShell({
    title: 'Ausgangslage & Transformation',
    pageNo: 1,
    brand,
    content: `
      <div style="margin-bottom:22px;">
        <h2 style="font-size:26px; font-weight:900; color:#0f172a; margin:0 0 8px 0;">
          Die Zukunft ist elektrisch
        </h2>
        <p style="font-size:14px; line-height:1.75; color:#475569; margin:0;">
          Für die Wirtschaftlichkeitsberechnung wird Ihr bisheriger Energieverbrauch in einen zukünftigen elektrischen Gesamtbedarf überführt. So wird sichtbar, wie Haushalt, Wärmepumpe und Mobilität intelligent mit eigenem Solarstrom versorgt werden können.
        </p>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:20px;">
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px;">
          <div style="font-size:14px; font-weight:900; color:#0f172a; margin-bottom:12px;">Ihr aktueller Energieverbrauch</div>
          <div style="display:grid; gap:10px; font-size:12px; color:#475569;">
            <div style="display:flex; justify-content:space-between;"><span>Hausstrom</span><strong>${formatInt(ergebnis.hausStrombedarf)} kWh</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>Heizung (${escapeHtml(formData.heizungsArt)})</span><strong>${formatInt(ergebnis.waermeBedarfKWhFossil)} kWh</strong></div>
            ${ergebnis.activeAuto ? `<div style="display:flex; justify-content:space-between;"><span>Mobilität</span><strong>${formatInt(ergebnis.kmJahr)} km</strong></div>` : ''}
            <div style="display:flex; justify-content:space-between; border-top:1px solid #e2e8f0; padding-top:10px; font-size:18px; font-weight:900; color:#0f172a;">
              <span>Gesamtkosten p.a.</span><span>${formatInt(oldTotalYear)} €</span>
            </div>
          </div>
        </div>

        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px;">
          <div style="font-size:14px; font-weight:900; color:#0f172a; margin-bottom:12px;">Elektrifizierte Zukunft</div>
          <div style="display:grid; gap:10px; font-size:12px; color:#475569;">
            <div style="display:flex; justify-content:space-between;"><span>Gesamtstrombedarf neu</span><strong>${formatInt(ergebnis.gesamtbedarf)} kWh</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>Davon PV gedeckt</span><strong style="color:${brand.primary};">${formatInt(ergebnis.genutzterPvStrom)} kWh</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>Reststrombezug</span><strong>${formatInt(ergebnis.restStromBezug)} kWh</strong></div>
            <div style="display:flex; justify-content:space-between; border-top:1px solid #e2e8f0; padding-top:10px; font-size:18px; font-weight:900; color:${brand.secondary};">
              <span>Neue Kosten p.a.</span><span>${formatInt(newTotalYear)} €</span>
            </div>
          </div>
        </div>
      </div>

      <div style="background:${brand.primary}; color:#fff; border-radius:20px; padding:22px; display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
        <div style="text-align:center;">
          <div style="font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.16em; opacity:.85;">Monetäre Ersparnis</div>
          <div style="font-size:34px; font-weight:900; margin-top:8px;">+${formatInt(ergebnis.ersparnisJahr1)} €</div>
          <div style="font-size:12px; margin-top:6px;">im ersten Jahr</div>
        </div>
        <div style="text-align:center;">
          <div style="font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.16em; opacity:.85;">Energetische Autarkie</div>
          <div style="font-size:34px; font-weight:900; margin-top:8px;">${formatInt(ergebnis.autarkieQuote)} %</div>
          <div style="font-size:12px; margin-top:6px;">Eigenversorgung</div>
        </div>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; overflow:hidden;">
        <div style="padding:14px 18px; border-bottom:1px solid #e2e8f0; font-size:13px; font-weight:900; color:#0f172a;">
          Die Kosten des Nichtstuns
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
          <thead>
            <tr style="background:#f8fafc; color:#64748b; text-transform:uppercase; font-size:10px;">
              <th style="padding:12px; text-align:left;">Zeitraum</th>
              <th style="padding:12px; text-align:left;">Ohne Wechsel</th>
              <th style="padding:12px; text-align:left;">Mit System</th>
              <th style="padding:12px; text-align:left;">Ersparnis</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-top:1px solid #e2e8f0;">
              <td style="padding:12px; font-weight:700;">1 Jahr</td>
              <td style="padding:12px;">${formatInt(ergebnis.opexFossilReihe[1])} €</td>
              <td style="padding:12px;">${formatInt(ergebnis.tcoSolarReihe[1])} €</td>
              <td style="padding:12px; font-weight:900; color:${brand.primary};">+${formatInt(ergebnis.bilanzReihe[1])} €</td>
            </tr>
            <tr style="border-top:1px solid #e2e8f0;">
              <td style="padding:12px; font-weight:700;">10 Jahre</td>
              <td style="padding:12px;">${formatInt(ergebnis.opexFossilReihe[10])} €</td>
              <td style="padding:12px;">${formatInt(ergebnis.tcoSolarReihe[10])} €</td>
              <td style="padding:12px; font-weight:900; color:${brand.primary};">+${formatInt(ergebnis.bilanzReihe[10])} €</td>
            </tr>
            <tr style="border-top:1px solid #e2e8f0;">
              <td style="padding:12px; font-weight:700;">20 Jahre</td>
              <td style="padding:12px;">${formatInt(ergebnis.opexFossilReihe[20])} €</td>
              <td style="padding:12px;">${formatInt(ergebnis.tcoSolarReihe[20])} €</td>
              <td style="padding:12px; font-weight:900; color:${brand.primary};">+${formatInt(ergebnis.bilanzReihe[20])} €</td>
            </tr>
            <tr style="border-top:1px solid #e2e8f0;">
              <td style="padding:12px; font-weight:700;">30 Jahre</td>
              <td style="padding:12px;">${formatInt(ergebnis.opexFossilReihe[30])} €</td>
              <td style="padding:12px;">${formatInt(ergebnis.tcoSolarReihe[30])} €</td>
              <td style="padding:12px; font-weight:900; color:${brand.primary};">+${formatInt(ergebnis.bilanzReihe[30])} €</td>
            </tr>
          </tbody>
        </table>
      </div>
    `
  });

  const page2 = this.renderPdfPageShell({
    title: 'Systemauslegung & Unabhängigkeit',
    pageNo: 2,
    brand,
    content: `
      <div style="margin-bottom:20px;">
        <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 8px 0;">
          Ihre Gesamtbilanz auf einen Blick
        </h2>
        <p style="font-size:13px; color:#475569; line-height:1.75; margin:0;">
          Das Gesamtsystem wird auf Ihren zukünftigen Bedarf abgestimmt. So entstehen hohe Eigenverbrauchsquoten, ein niedriger Netzbezug und eine stabile Wirtschaftlichkeit.
        </p>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px; margin-bottom:22px;">
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px; text-align:center;">
          ${this.renderPdfRing(ergebnis.autarkieQuote, brand.primary)}
          <div style="font-size:12px; font-weight:900; color:#0f172a; margin-top:10px;">Autarkiegrad</div>
          <div style="font-size:11px; color:#64748b; margin-top:6px;">${formatInt(ergebnis.genutzterPvStrom)} kWh gedeckt</div>
        </div>

        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px; text-align:center;">
          ${this.renderPdfRing(ergebnis.eigenverbrauchQuote, brand.secondary)}
          <div style="font-size:12px; font-weight:900; color:#0f172a; margin-top:10px;">Eigenverbrauchsquote</div>
          <div style="font-size:11px; color:#64748b; margin-top:6px;">${formatInt(ergebnis.netzEinspeisung)} kWh Einspeisung</div>
        </div>

        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px; text-align:center;">
          ${this.renderPdfRing(ergebnis.finanzAutarkieQuote, brand.primary)}
          <div style="font-size:12px; font-weight:900; color:#0f172a; margin-top:10px;">Finanzielle Unabhängigkeit</div>
          <div style="font-size:11px; color:#64748b; margin-top:6px;">+${formatInt(ergebnis.ersparnisJahr1)} € / Jahr</div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1.1fr .9fr; gap:20px; margin-bottom:22px;">
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
          <div style="font-size:14px; font-weight:900; color:#0f172a; margin-bottom:10px;">Systemdimensionierung</div>
          <div style="font-size:12px; line-height:1.8; color:#475569; margin-bottom:14px;">
            Die empfohlene Auslegung orientiert sich an Haushaltsstrom, Wärmepumpe und optionaler Elektromobilität.
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:14px; text-align:center;">
              <div style="font-size:10px; font-weight:900; color:#64748b; text-transform:uppercase;">Wärmepumpe</div>
              <div style="font-size:24px; font-weight:900; color:#0f172a;">${escapeHtml(ergebnis.wpGroesseKW)} kW</div>
            </div>
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:14px; text-align:center;">
              <div style="font-size:10px; font-weight:900; color:#64748b; text-transform:uppercase;">PV</div>
              <div style="font-size:24px; font-weight:900; color:#0f172a;">${escapeHtml(ergebnis.pvGroesse)} kWp</div>
            </div>
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:14px; text-align:center;">
              <div style="font-size:10px; font-weight:900; color:#64748b; text-transform:uppercase;">Speicher</div>
              <div style="font-size:24px; font-weight:900; color:#0f172a;">${escapeHtml(ergebnis.speicherGroesse)} kWh</div>
            </div>
          </div>
        </div>

        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:20px;">
          <div style="font-size:14px; font-weight:900; color:#0f172a; margin-bottom:10px;">Bedarfsmix</div>
          <div style="display:grid; gap:10px; font-size:12px;">
            <div style="display:flex; justify-content:space-between;"><span>Haushalt</span><strong>${formatInt(ergebnis.hausStrombedarf)} kWh</strong></div>
            ${formData.includeWp ? `<div style="display:flex; justify-content:space-between;"><span>Wärmepumpe</span><strong>${formatInt(ergebnis.wpStrombedarf)} kWh</strong></div>` : ''}
            ${ergebnis.activeAuto ? `<div style="display:flex; justify-content:space-between;"><span>E-Auto</span><strong>${formatInt(ergebnis.autoStrombedarf)} kWh</strong></div>` : ''}
            <div style="display:flex; justify-content:space-between; border-top:1px solid #e2e8f0; padding-top:10px; font-size:18px; font-weight:900;">
              <span>Gesamt</span><span>${formatInt(ergebnis.gesamtbedarf)} kWh</span>
            </div>
          </div>
        </div>
      </div>

      <div style="background:${brand.secondarySoft}; border:1px solid ${brand.secondary}; border-radius:18px; padding:18px; font-size:12px; line-height:1.8; color:#334155;">
        <strong style="color:${brand.secondary};">Warum genau diese Größe?</strong><br>
        Die Systemgröße ist so gewählt, dass Ihr zukünftiger Bedarf in möglichst vielen Stunden des Jahres direkt oder über den Speicher abgedeckt werden kann. Ziel ist nicht nur maximale Produktion, sondern maximale wirtschaftliche Nutzbarkeit.
      </div>
    `
  });

  const page3 = this.renderPdfPageShell({
    title: 'Saisonale Auswertung',
    pageNo: 3,
    brand,
    content: `
      <div style="margin-bottom:20px;">
        <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 8px 0;">
          Saisonale Verteilung & Autarkie
        </h2>
        <p style="font-size:13px; color:#475569; line-height:1.75; margin:0;">
          Ertrag und Bedarf verhalten sich über das Jahr unterschiedlich. Die saisonale Betrachtung zeigt, wann das System hohe Überschüsse erzeugt und wann Reststrombezug notwendig bleibt.
        </p>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:22px;">
        ${ergebnis.saisonDaten.map((s) => `
          <div style="background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
              <div style="font-size:13px; font-weight:900; color:${brand.primary}; text-transform:uppercase;">${escapeHtml(s.name)}</div>
              <div style="font-size:18px; font-weight:900; color:#0f172a;">${s.autarkie}%</div>
            </div>

            <div style="display:grid; gap:8px; font-size:12px; color:#475569;">
              <div style="display:flex; justify-content:space-between;"><span>Bedarf</span><strong>${formatInt(s.verbrauch)} kWh</strong></div>
              <div style="display:flex; justify-content:space-between;"><span>PV/Akku gedeckt</span><strong style="color:${brand.primary};">${formatInt(s.covered)} kWh</strong></div>
              <div style="display:flex; justify-content:space-between;"><span>Restbezug</span><strong>${formatInt(s.restbezug)} kWh</strong></div>
              <div style="display:flex; justify-content:space-between;"><span>Einspeisung</span><strong style="color:${brand.secondary};">${formatInt(s.einspeisung)} kWh</strong></div>
            </div>
          </div>
        `).join('')}
      </div>

      ${this.renderPdfBarChart([
        { name:'Jan', Solarertrag: ergebnis.saisonDaten[0]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[0]?.verbrauch * 0.33 || 0 },
        { name:'Feb', Solarertrag: ergebnis.saisonDaten[0]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[0]?.verbrauch * 0.33 || 0 },
        { name:'Mär', Solarertrag: ergebnis.saisonDaten[1]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[1]?.verbrauch * 0.33 || 0 },
        { name:'Apr', Solarertrag: ergebnis.saisonDaten[1]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[1]?.verbrauch * 0.33 || 0 },
        { name:'Mai', Solarertrag: ergebnis.saisonDaten[1]?.ertrag * 0.34 || 0, Gesamtbedarf: ergebnis.saisonDaten[1]?.verbrauch * 0.34 || 0 },
        { name:'Jun', Solarertrag: ergebnis.saisonDaten[2]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[2]?.verbrauch * 0.33 || 0 },
        { name:'Jul', Solarertrag: ergebnis.saisonDaten[2]?.ertrag * 0.34 || 0, Gesamtbedarf: ergebnis.saisonDaten[2]?.verbrauch * 0.34 || 0 },
        { name:'Aug', Solarertrag: ergebnis.saisonDaten[2]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[2]?.verbrauch * 0.33 || 0 },
        { name:'Sep', Solarertrag: ergebnis.saisonDaten[3]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[3]?.verbrauch * 0.33 || 0 },
        { name:'Okt', Solarertrag: ergebnis.saisonDaten[3]?.ertrag * 0.33 || 0, Gesamtbedarf: ergebnis.saisonDaten[3]?.verbrauch * 0.33 || 0 },
        { name:'Nov', Solarertrag: ergebnis.saisonDaten[3]?.ertrag * 0.34 || 0, Gesamtbedarf: ergebnis.saisonDaten[3]?.verbrauch * 0.34 || 0 },
        { name:'Dez', Solarertrag: ergebnis.saisonDaten[0]?.ertrag * 0.34 || 0, Gesamtbedarf: ergebnis.saisonDaten[0]?.verbrauch * 0.34 || 0 }
      ], brand)}
    `
  });

  const page4 = this.renderPdfPageShell({
    title: 'Investition & Wirtschaftlichkeit',
    pageNo: 4,
    brand,
    bg: '#ffffff',
    content: `
      <div style="margin-bottom:20px;">
        <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 8px 0;">
          Investition & Förderstruktur
        </h2>
        <p style="font-size:13px; color:#475569; line-height:1.75; margin:0;">
          Hier sehen Sie die Investitionsstruktur Ihres Systems inklusive Zuschüssen und zusätzlicher Nachlässe.
        </p>
      </div>

      <div style="background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:22px; margin-bottom:22px;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
          <div style="display:grid; gap:10px; font-size:13px;">
            ${formData.includeWp ? `<div style="display:flex; justify-content:space-between;"><span>Wärmepumpe</span><strong>${formatInt(ergebnis.investWPBrutto)} €</strong></div>` : ''}
            ${formData.includeSolar ? `<div style="display:flex; justify-content:space-between;"><span>Photovoltaik</span><strong>${formatInt(ergebnis.investPVOnly)} €</strong></div>` : ''}
            ${formData.includeSolar ? `<div style="display:flex; justify-content:space-between;"><span>Speicher</span><strong>${formatInt(ergebnis.investSpeicher)} €</strong></div>` : ''}
            ${(formData.includeSolar && ergebnis.activeAuto && formData.includeWallbox !== false) ? `<div style="display:flex; justify-content:space-between;"><span>Wallbox</span><strong>${formatInt(ergebnis.investWallbox)} €</strong></div>` : ''}
          </div>

          <div style="display:grid; gap:10px; font-size:13px;">
            <div style="display:flex; justify-content:space-between;"><span>Gesamtinvestition</span><strong>${formatInt(ergebnis.gesamtInvestBrutto)} €</strong></div>
            <div style="display:flex; justify-content:space-between; color:${brand.primary};"><span>KfW-Zuschuss</span><strong>- ${formatInt(ergebnis.kfwZuschussMax)} €</strong></div>
            <div style="display:flex; justify-content:space-between; color:${brand.primary};"><span>Zusatzrabatte</span><strong>- ${formatInt(ergebnis.gutscheinSumme)} €</strong></div>
            <div style="display:flex; justify-content:space-between; border-top:1px solid #e2e8f0; padding-top:10px; font-size:24px; font-weight:900; color:#0f172a;">
              <span>Netto-Investition</span><span>${formatInt(ergebnis.nettoInvestition)} €</span>
            </div>
          </div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:20px;">
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:18px; text-align:center;">
          <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Amortisation</div>
          <div style="font-size:28px; font-weight:900; color:${brand.primary}; margin-top:8px;">${this.formatAmortisation(ergebnis)}</div>
        </div>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:18px; text-align:center;">
          <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Rendite p.a.</div>
          <div style="font-size:28px; font-weight:900; color:${brand.secondary}; margin-top:8px;">${escapeHtml(ergebnis.roi)} %</div>
        </div>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:18px; text-align:center;">
          <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Solarstrompreis</div>
          <div style="font-size:28px; font-weight:900; color:#0f172a; margin-top:8px;">${Number(ergebnis.solarstromPreis || 0).toFixed(2).replace('.', ',')} €</div>
        </div>
      </div>

      ${this.renderPdfLineChart(ergebnis, brand)}
    `
  });

  const page5 = this.renderPdfPageShell({
    title: 'Klimaschutz & nächste Schritte',
    pageNo: 5,
    brand,
    content: `
      <div style="margin-bottom:22px;">
        <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 8px 0;">
          Ihr aktiver Klimaschutz
        </h2>
        <p style="font-size:13px; color:#475569; line-height:1.75; margin:0;">
          Neben der finanziellen Entlastung reduziert Ihr neues System dauerhaft CO₂-Emissionen und stärkt Ihre Versorgungssicherheit.
        </p>
      </div>

      <div style="background:${brand.primarySoft}22; border:1px solid ${brand.primarySoft}; border-radius:20px; padding:22px; margin-bottom:24px;">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:14px;">
          <div style="background:#fff; border:1px solid ${brand.primarySoft}; border-radius:14px; padding:14px; text-align:center;">
            <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Pro Jahr</div>
            <div style="font-size:24px; font-weight:900; color:${brand.primary};">${(ergebnis.co2ErsparnisPerYear).toFixed(1).replace('.', ',')} t</div>
          </div>
          <div style="background:#fff; border:1px solid ${brand.primarySoft}; border-radius:14px; padding:14px; text-align:center;">
            <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">10 Jahre</div>
            <div style="font-size:24px; font-weight:900; color:${brand.primary};">${(ergebnis.co2ErsparnisPerYear * 10).toFixed(1).replace('.', ',')} t</div>
          </div>
          <div style="background:#fff; border:1px solid ${brand.primarySoft}; border-radius:14px; padding:14px; text-align:center;">
            <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Bäume</div>
            <div style="font-size:24px; font-weight:900; color:${brand.primary};">${formatInt(ergebnis.co2Baeume)}</div>
          </div>
          <div style="background:#fff; border:1px solid ${brand.primarySoft}; border-radius:14px; padding:14px; text-align:center;">
            <div style="font-size:10px; color:#64748b; font-weight:900; text-transform:uppercase;">Fläche</div>
            <div style="font-size:24px; font-weight:900; color:${brand.primary};">${formatInt(ergebnis.co2FlaecheQm)} m²</div>
          </div>
        </div>
      </div>

      <div style="margin-bottom:24px;">
        <div style="font-size:18px; font-weight:900; color:#0f172a; margin-bottom:16px;">Wie es weitergeht</div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px;">
          ${[
            ['1', 'Vor-Ort-Analyse', 'Prüfung der Gegebenheiten und Feinplanung des Systems.'],
            ['2', 'Fördermittelservice', 'Unterstützung bei Zuschüssen, KfW und Netzanmeldung.'],
            ['3', 'Installation', 'Montage, Inbetriebnahme und Übergabe aus einer Hand.']
          ].map(item => `
            <div style="text-align:center; background:#fff; border:1px solid #e2e8f0; border-radius:18px; padding:18px;">
              <div style="width:46px; height:46px; border-radius:999px; background:${brand.primary}; color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 12px auto; font-weight:900;">${item[0]}</div>
              <div style="font-weight:900; color:#0f172a; margin-bottom:6px;">${item[1]}</div>
              <div style="font-size:12px; line-height:1.6; color:#64748b;">${item[2]}</div>
            </div>
          `).join('')}
        </div>
      </div>

      <div style="border:2px solid ${brand.primary}; background:#f8fafc; border-radius:22px; padding:24px; display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
          <div style="font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.14em; color:${brand.primary}; margin-bottom:8px;">Reservierter Vorteil</div>
          <div style="font-size:30px; font-weight:900; color:#0f172a;">${formatInt(ergebnis.gutscheinSumme)} €</div>
          <div style="font-size:13px; color:#64748b; margin-top:8px;">Individuell angesetzte Zusatzrabatte und Aktionen.</div>
        </div>
        <img src="${brand.logo}" alt="${escapeHtml(brand.name)}" style="max-width:140px; max-height:60px; object-fit:contain;" />
      </div>
    `
  });

  const page6 = this.renderPdfPageShell({
    title: 'Werk Studio & Technologie-Vorteile',
    pageNo: 6,
    brand,
    content: this.renderPdfTechAdvantagesPage(ergebnis, brand)
  });

  const page7 = this.renderPdfPageShell({
    title: 'Photovoltaik & Batteriespeicher',
    pageNo: 7,
    brand,
    content: this.renderPdfPvBatteryPage(ergebnis, brand)
  });

  const page8 = this.renderPdfPageShell({
    title: 'Das perfekte Team',
    pageNo: 8,
    brand,
    content: this.renderPdfPerfectTeamPage(ergebnis, brand)
  });

  const page9 = this.renderPdfPageShell({
    title: 'E-Mobilität',
    pageNo: 9,
    brand,
    content: this.renderPdfEmobilityPage(ergebnis, brand)
  });

  const page10 = this.renderPdfPageShell({
    title: 'Sektorkopplung',
    pageNo: 10,
    brand,
    content: this.renderPdfSectorCouplingPage(ergebnis, brand)
  });

  const page11 = this.renderPdfPageShell({
    title: 'Investition & Wirtschaftlichkeit',
    pageNo: 11,
    brand,
    bg: '#ffffff',
    content: this.renderPdfInvestmentDeepPage(ergebnis, brand)
  });

  const page12 = this.renderPdfPageShell({
    title: 'Klimaschutz & Abschluss',
    pageNo: 12,
    brand,
    noBreak: true,
    content: this.renderPdfClosingPage(ergebnis, brand)
  });

  return `
    <div class="fixed inset-0 z-[100] bg-slate-900/90 overflow-y-auto flex flex-col items-center animate-fade-in">
      <div class="sticky top-0 w-full bg-slate-900/85 backdrop-blur-md p-4 flex justify-center gap-4 z-20">
        <button data-action="print-pdf-direct" class="bg-white text-slate-800 font-black py-3 px-6 rounded-xl flex items-center gap-2 hover:bg-slate-100 transition-colors">
          ${this.renderIcon('printer', 'w-4 h-4')} Drucken / Als PDF speichern
        </button>
        <button data-action="close-pdf-preview" class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 px-6 rounded-xl transition-colors">
          Schließen
        </button>
      </div>

      <div class="w-full pb-10 overflow-x-auto flex flex-col items-center pt-8">
        <div id="pdf-export-wrapper" class="bg-white text-left shadow-2xl mx-auto" style="width:794px; color:#1f2937;">
          ${coverPage}
          ${page1}
          ${page2}
          ${page3}
          ${page4}
          ${page5}
          ${page6}
          ${page7}
          ${page8}
          ${page9}
          ${page10}
          ${page11}
          ${page12}
        </div>
      </div>
    </div>
  `;
};
  App.renderDashboardHeader = function (ergebnis) {
    const { formData, newProjectName } = this.state;

    return `
      <div class="bg-white rounded-3xl p-8 border border-slate-200  mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
          <div class="inline-flex items-center gap-1.5 mb-3 bg-[#cfe09b]/50 px-3 py-1.5 rounded-full text-[#93c21c] text-xs font-black uppercase tracking-widest border border-[#cfe09b]">
            ${this.renderIcon('check-circle-2', 'w-4 h-4')}
            Auswertung Erfolgreich
          </div>
          <h2 class="text-3xl md:text-4xl font-black text-slate-800 mb-2">
            ${newProjectName ? `Projekt: ${escapeHtml(newProjectName)}` : 'Deine persönliche Auswertung'}
          </h2>
          <p class="text-slate-500 font-medium text-lg">
            Alle Daten für das Gebäude in ${escapeHtml(formData.ort)} (${escapeHtml(formData.standortPlz)}) wurden berechnet.
          </p>
        </div>

        <div class="flex flex-wrap gap-3">

          <button data-action="back-to-quiz" class="bg-white border border-slate-200 text-slate-700 font-black py-3 px-6 rounded-xl flex items-center gap-2 hover:bg-slate-100 transition-colors">
            ${this.renderIcon('chevron-left', 'w-5 h-5')}
            Eingaben bearbeiten
          </button>
          <button data-action="save-project" class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-black py-3 px-6 rounded-xl flex items-center gap-2 transition-colors">
            ${this.renderIcon('save', 'w-5 h-5')}
            Speichern
          </button>
          <button data-action="open-calc-modal" class="bg-slate-100 border border-slate-200 text-slate-600 font-black py-3 px-6 rounded-xl flex items-center gap-2 hover:bg-slate-200 transition-colors">
            ${this.renderIcon('calculator', 'w-5 h-5')}
            Berechnung prüfen
          </button>
          <button data-action="open-pdf-preview" class="bg-[#74b2d4] text-white font-black py-3 px-6 rounded-xl flex items-center gap-2 hover:bg-[#5c8fa8] transition-colors  hover:">
            ${this.renderIcon('file-text', 'w-5 h-5')}
            Vorschau
          </button> 

          
        </div>
      </div>
    `;
  };

  App.renderDashboardSystemControls = function (ergebnis) {
    const { formData } = this.state;

    return `
      <div class="flex flex-col items-center mb-10 gap-5">
        <div class="bg-white p-2 rounded-full  border border-slate-200 flex flex-wrap justify-center gap-1">
          <button data-action="set-system" data-system="complete" class="px-6 py-3 rounded-full text-sm font-black transition-all ${formData.includeWp && formData.includeSolar ? 'bg-[#93c21c] text-white ' : 'text-slate-500 hover:text-slate-800'}">
            Komplettsystem (WP + PV)
          </button>
          <button data-action="set-system" data-system="wp-only" class="px-6 py-3 rounded-full text-sm font-black transition-all ${formData.includeWp && !formData.includeSolar ? 'bg-[#74b2d4] text-white ' : 'text-slate-500 hover:text-slate-800'}">
            Nur Wärmepumpe
          </button>
          <button data-action="set-system" data-system="pv-only" class="px-6 py-3 rounded-full text-sm font-black transition-all ${!formData.includeWp && formData.includeSolar ? 'bg-[#93c21c] text-white ' : 'text-slate-500 hover:text-slate-800'}">
            Nur Photovoltaik
          </button>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col gap-4 text-sm animate-fade-in w-full max-w-5xl">
          <span class="font-black text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-2">
            ${this.renderIcon('edit-3', 'w-4 h-4 text-[#74b2d4]')}
            Parameter & Tarife live anpassen:
          </span>

          <div class="flex flex-wrap items-center justify-center gap-6">
            <div class="flex items-center gap-2">
              <label class="font-bold text-slate-500">Strompreis:</label>
              <div class="relative">
                <input type="number" step="0.01" data-model="evuPreis" value="${escapeHtml(formData.evuPreis)}" class="w-24 bg-slate-50 border border-slate-200 p-2 pr-6 rounded-lg outline-none focus:border-[#74b2d4] font-black text-slate-700 text-right"/>
                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 font-bold">€</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <label class="font-bold text-slate-500">${escapeHtml(formData.heizungsArt)}:</label>
              <div class="relative">
                <input type="number" step="0.01" data-model="heizPreis" value="${escapeHtml(formData.heizPreis)}" class="w-24 bg-slate-50 border border-slate-200 p-2 pr-6 rounded-lg outline-none focus:border-[#74b2d4] font-black text-slate-700 text-right"/>
                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 font-bold">€</span>
              </div>
            </div>

            ${ergebnis.activeAuto ? `
              <div class="flex items-center gap-2">
                <label class="font-bold text-slate-500">Sprit:</label>
                <div class="relative">
                  <input type="number" step="0.01" data-model="spritPreis" value="${escapeHtml(formData.spritPreis)}" class="w-24 bg-slate-50 border border-slate-200 p-2 pr-6 rounded-lg outline-none focus:border-[#74b2d4] font-black text-slate-700 text-right"/>
                  <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 font-bold">€</span>
                </div>
              </div>
            ` : ''}

            <div class="flex items-center gap-2">
              <label class="font-bold text-slate-500">Wartung (Alt):</label>
              <div class="relative">
                <input type="number" step="10" data-model="wartungAlt_pa_input" value="${escapeHtml(formData.wartungAlt_pa_input)}" class="w-20 bg-slate-50 border border-slate-200 p-2 pr-6 rounded-lg outline-none focus:border-[#74b2d4] font-black text-slate-700 text-right"/>
                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">€</span>
              </div>
            </div>

            <div class="flex items-center gap-2 border-l border-slate-200 pl-6">
              <label class="font-bold text-slate-500">Alter Heizung:</label>
              <div class="relative">
                <input type="number" step="1" data-model="heizungsAlter" value="${escapeHtml(formData.heizungsAlter)}" class="w-20 bg-slate-50 border border-slate-200 p-2 pr-6 rounded-lg outline-none focus:border-[#74b2d4] font-black text-slate-700 text-right"/>
                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">J.</span>
              </div>
            </div>

            <div class="flex items-center gap-2 border-l border-slate-200 pl-6">
              <label class="font-bold text-slate-500">Design:</label>
              <select data-model="brand" class="bg-slate-50 border border-slate-200 p-2 rounded-lg outline-none focus:border-[#74b2d4] font-black text-slate-700 cursor-pointer">
                <option value="solar_aspekt" ${formData.brand === 'solar_aspekt' ? 'selected' : ''}>Solar Aspekt</option>
                <option value="werk_studio" ${formData.brand === 'werk_studio' ? 'selected' : ''}>Werk Studio</option>
              </select>
            </div>


            ${formData.includeWp ? `
              <div class="flex items-center gap-2">
                <label class="font-bold text-slate-500">JAZ (WP):</label>
                <input type="number" step="0.1" data-model="customJaz" value="${escapeHtml(formData.customJaz || ergebnis.jaz)}" class="w-20 bg-slate-50 border border-slate-200 p-2 rounded-lg outline-none focus:border-[#74b2d4] font-black text-slate-700 text-center"/>
              </div>
            ` : ''}
          </div>
        </div>
      </div>
    `;
  };

  App.renderDashboardTabsNav = function () {
    const { activeTab } = this.state;

    return `
      <div class="flex overflow-x-auto hide-scrollbar mb-8 bg-slate-200/50 p-2 rounded-2xl border border-slate-200 max-w-4xl mx-auto">
        <button data-action="set-tab" data-tab="FINANZEN" class="flex-1 min-w-[200px] py-4 px-4 rounded-xl text-sm font-black transition-all flex items-center justify-center gap-2 ${activeTab === 'FINANZEN' ? 'bg-white text-[#74b2d4] ' : 'text-slate-500 hover:text-slate-700'}">
          ${this.renderIcon('wallet', 'w-5 h-5')}
          Finanzen & Förderung
        </button>
        <button data-action="set-tab" data-tab="AUTARKIE" class="flex-1 min-w-[200px] py-4 px-4 rounded-xl text-sm font-black transition-all flex items-center justify-center gap-2 ${activeTab === 'AUTARKIE' ? 'bg-white text-[#93c21c] ' : 'text-slate-500 hover:text-slate-700'}">
          ${this.renderIcon('wind', 'w-5 h-5')}
          Autarkie & Unabhängigkeit
        </button>
        <button data-action="set-tab" data-tab="TECHNIK" class="flex-1 min-w-[200px] py-4 px-4 rounded-xl text-sm font-black transition-all flex items-center justify-center gap-2 ${activeTab === 'TECHNIK' ? 'bg-white text-slate-800 ' : 'text-slate-500 hover:text-slate-700'}">
          ${this.renderIcon('shield-check', 'w-5 h-5')}
          Technik & Premium-Hardware
        </button>
      </div>
    `;
  };

  App.renderDashboard = function () {
    const ergebnis = this.calculateErgebnis();
    const counters = this.getDashboardCounters(ergebnis);

    return ` 
        <div class="fixed bottom-0 left-0 w-full bg-slate-900 text-white z-50 shadow-[0_-10px_40px_rgba(0,0,0,0.2)] border-t-4 border-[#93c21c]">
          <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
              <div class="bg-[#93c21c]/20 p-3 rounded-full hidden sm:block">
                ${this.renderIcon('check-circle-2', 'w-6 h-6 text-[#93c21c]')}
              </div>
              <div>
                <p class="text-sm text-slate-400 font-bold">
                  Deine Ersparnis (${this.state.selectedYears} Jahre):
                  <span class="text-[#93c21c]">+${formatInt(counters.ersparnisOpexCounter)} €</span>
                </p>
                <p class="text-xs text-slate-400">
                  Reservierter Gutscheinwert:
                  <strong class="text-white">${formatInt(ergebnis.gutscheinSumme)} €</strong>
                </p>
              </div>
            </div>

            <button data-action="open-contact-modal" class="w-full md:w-auto bg-[#93c21c] hover:bg-[#82ad18] text-white font-black py-3 px-8 rounded-xl  transition-transform hover:scale-105 flex items-center justify-center gap-2">
              Experten-Gespräch & Gutschein sichern
              ${this.renderIcon('arrow-right', 'w-5 h-5')}
            </button>
          </div>
        </div>

        <main class="relative z-10 max-w-7xl mx-auto px-4 pb-24">
          ${this.renderDashboardHeader(ergebnis)}
          ${this.renderDashboardSystemControls(ergebnis)}
          ${this.renderDashboardTabsNav()}

          <div id="dashboard-tab-content">
            ${this.renderDashboardTabContent ? this.renderDashboardTabContent(ergebnis, counters) : ''}
          </div>
        </main>

        ${this.renderContactModal(ergebnis)}
        ${this.renderPdfPreview(ergebnis)}
        ${this.renderCalculationModal(ergebnis)}
     
    `;
  };
 


  App.render = function () {
    const { stage, quizStep, savedProjects } = this.state;
    const ergebnis = this.calculateErgebnis();

    this.root.innerHTML = `
      <div class="min-h-screen bg-slate-50 text-slate-700 font-sans selection:bg-[#93c21c] selection:text-white pb-32">
        <header class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 shadow-sm">
          <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2 cursor-pointer" data-action="go-home">
              ${this.renderIcon('sun', 'w-6 h-6 text-[#93c21c]')}
              <span class="text-lg font-black tracking-tighter text-slate-800">SOLAR<span class="text-[#74b2d4]">ASPEKT</span></span>
            </div>

            <div class="flex items-center gap-3">
              <div class="hidden md:flex items-center gap-1.5 text-[10px] font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                ${this.renderIcon('shield-check', 'w-3 h-3 text-[#93c21c]')}
                TÜV-geprüft & Sicher
              </div>

              <button data-action="open-project-modal" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-1.5 rounded-full text-sm font-bold transition-colors">
                ${this.renderIcon('folder-open', 'w-4 h-4')}
                <span class="hidden sm:inline">Projekte (${savedProjects.length})</span>
              </button>
            </div>
          </div>
        </header>

        ${this.renderProjectModal()}

        ${stage === 'START' ? `
          <main class="max-w-3xl mx-auto px-4 py-16 md:py-24 text-center animate-zoom-in">
            <div class="inline-flex items-center gap-2 bg-[#cfe09b]/50 border border-[#93c21c]/30 rounded-full px-4 py-1.5 mb-6">
              ${this.renderIcon('sparkles', 'w-4 h-4 text-[#93c21c]')}
              <span class="text-[10px] font-black text-[#74b2d4] uppercase tracking-wider">Aktuell: Neue BEG Förderung integriert</span>
            </div>

            <h1 class="text-4xl md:text-6xl font-black text-slate-800 tracking-tight leading-tight mb-6">
              Schluss mit der Preisspirale. <span class="text-[#93c21c]">Dein Haus. Dein Strom.</span>
            </h1>

            <p class="text-slate-500 font-medium text-lg max-w-xl mx-auto mb-10">
              Finde in 60 Sekunden heraus, wie viel staatlichen Zuschuss du für eine Wärmepumpe erhältst und berechne deine lebenslange Unabhängigkeit inklusive Photovoltaik und Speicher.
            </p>

            <button data-action="start-quiz" class="bg-[#93c21c] hover:bg-[#82ad18] text-white text-lg font-black py-5 px-10 rounded-2xl shadow-[0_8px_30px_rgba(147,194,28,0.4)] transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 mx-auto group">
              Jetzt Wirtschaftlichkeit berechnen
              ${this.renderIcon('arrow-right', 'w-5 h-5')}
            </button>
          </main>
        ` : ''}


        ${stage === 'QUIZ' ? `
          <main class="max-w-2xl mx-auto px-4 py-12 text-center">
            <div class="w-full bg-slate-100 h-2 rounded-full mb-8 overflow-hidden">
              <div class="bg-[#93c21c] h-full transition-all duration-500" style="width:${((quizStep + 1) / 3) * 100}%"></div>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-2xl border border-slate-100">
              ${this.renderQuizStep()}

              ${quizStep > 0 ? `
                <button data-action="prev-quiz-step" class="mt-8 text-sm font-bold text-slate-400 flex items-center gap-1 mx-auto hover:text-slate-600 transition-colors">
                  ${this.renderIcon('chevron-left', 'w-4 h-4')}
                  Zurück
                </button>
              ` : ''}
            </div>
          </main>
        ` : ''}

        ${stage === 'LOADING' ? `
          <main class="max-w-lg mx-auto px-4 py-32 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 mb-8 relative">
              <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
              <div class="absolute inset-0 border-4 border-[#93c21c] rounded-full border-t-transparent animate-spin"></div>
              ${this.renderIcon('sun', 'w-8 h-8 text-[#74b2d4] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2')}
            </div>
            <h2 class="text-2xl font-black text-slate-800 mb-2">Erstelle individuellen Fahrplan...</h2>
            <p class="text-slate-500 font-bold animate-pulse">${escapeHtml(this.state.loadingText)}</p>
          </main>
        ` : ''}

        ${stage === 'DASHBOARD' ? this.renderDashboard() : ''}
      </div>
    `;

      const bonusEl = document.getElementById('bonusValue');
    if (bonusEl) {
      bonusEl.textContent = formatInt(ergebnis.gutscheinSumme) + ' €';
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }

    if (typeof this.bindEvents === 'function') {
      this.bindEvents();
    }
  };

  // ─────────────────────────────────────────────────────────────
// DASHBOARD TAB RENDERERS
// ─────────────────────────────────────────────────────────────
App.renderFinanceTab = function (ergebnis, counters) {
  const { selectedYears, formData } = this.state;
  const pctSavings = counters.fossilCounter > 0
    ? Math.round((counters.ersparnisOpexCounter / counters.fossilCounter) * 100)
    : 0;

  return `
    <div class="animate-fade-in space-y-10">
      <div class="flex justify-center">
        <div class="bg-slate-100 p-2 rounded-full border border-slate-200 flex flex-wrap items-center justify-center gap-2">
          <span class="text-sm font-bold text-slate-500 pl-4 pr-2 uppercase tracking-widest">Zeitraum wählen:</span>
          ${[1, 10, 20, 30].map(y => `
            <button
              data-action="set-years"
              data-years="${y}"
              class="px-6 py-2 rounded-full text-sm font-black transition-all ${selectedYears === y ? 'bg-[#74b2d4] text-white ' : 'text-slate-500 hover:bg-white'}"
            >
              ${y} ${y === 1 ? 'Jahr' : 'Jahre'}
            </button>
          `).join('')}
        </div>
      </div>

      <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col">
        <div class="grid grid-cols-1 md:grid-cols-4 border-b border-slate-100">
          <div class="p-8 md:p-10 border-b md:border-b-0 md:border-r border-slate-100 flex flex-col justify-center">
            <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-2">
              ${this.renderIcon('trending-down', 'w-4 h-4 text-slate-400')}
              Kosten Vorher
            </p>
            <p class="text-4xl font-black text-slate-700 line-through decoration-slate-300">${formatInt(counters.fossilCounter)} €</p>
          </div>

          <div class="p-8 md:p-10 border-b md:border-b-0 md:border-r border-slate-100 flex flex-col justify-center bg-[#e3effb]/30">
            <p class="text-xs font-black text-[#74b2d4] uppercase tracking-widest mb-2 flex items-center gap-2">
              ${this.renderIcon('wallet', 'w-4 h-4')}
              Neue Betriebskosten
            </p>
            <p class="text-4xl font-black text-[#74b2d4]">${formatInt(counters.opexSolarCounter)} €</p>
          </div>

          <div class="p-8 md:p-10 border-b md:border-b-0 md:border-r border-slate-100 flex flex-col justify-center bg-[#cfe09b]/20 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-[#93c21c] opacity-10 rounded-bl-full"></div>
            <p class="text-xs font-black text-[#93c21c] uppercase tracking-widest mb-2 flex items-center gap-2">
              ${this.renderIcon('arrow-down-to-line', 'w-4 h-4')}
              Deine Ersparnis
            </p>
            <p class="text-4xl font-black text-[#93c21c]">+${formatInt(counters.ersparnisOpexCounter)} €</p>
          </div>

          <div class="p-8 md:p-10 flex flex-col justify-center bg-[#74b2d4] text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">${this.renderIcon('target', 'w-32 h-32')}</div>
            <p class="text-xs font-black text-white/80 uppercase tracking-widest mb-2 relative z-10 flex items-center gap-2">
              ${this.renderIcon('award', 'w-4 h-4')}
              Staatl. Förderung
            </p>
            <p class="text-4xl font-black relative z-10">${formatInt(counters.kfwCounter)} €</p>
          </div>
        </div>

        <div class="p-8 md:p-10 bg-slate-50/50 flex flex-col relative">
          <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div>
              <p class="text-xs text-slate-500 uppercase font-black tracking-widest mb-1 flex items-center gap-1.5">
                ${this.renderIcon('info', 'w-4 h-4 text-[#74b2d4]')}
                Amortisation in
              </p>
              <p class="text-3xl font-black text-[#74b2d4]">${escapeHtml(this.formatAmortisation(ergebnis))}</p>
            </div>

            <div>
              <p class="text-xs text-slate-500 uppercase font-black tracking-widest mb-1 flex items-center gap-1.5">
                ${this.renderIcon('info', 'w-4 h-4 text-[#93c21c]')}
                Rendite Pro Jahr (Start)
              </p>
              <p class="text-3xl font-black text-[#93c21c]">${escapeHtml(ergebnis.roi)} %</p>
            </div>

            ${formData.includeSolar ? `
              <div class="col-span-2 md:col-span-1 border-t md:border-t-0 md:border-l border-slate-200 pt-6 md:pt-0 md:pl-6">
                <p class="text-xs text-slate-500 uppercase font-black tracking-widest mb-1">Dein Solarstrompreis</p>
                <div class="flex items-end gap-3">
                  <p class="text-3xl font-black text-slate-800">${ergebnis.solarstromPreis.toFixed(2).replace('.', ',')} €</p>
                  <p class="text-sm font-bold text-slate-400 pb-1">vs ${toNumber(formData.evuPreis, 0.35).toFixed(2).replace('.', ',')} € (Netz)</p>
                </div>
              </div>
            ` : ''}
          </div>
        </div>
      </div>

      <div>
        <h3 class="text-2xl font-black text-slate-800 mb-6 flex items-center gap-3 ml-2">
          ${this.renderIcon('wallet', 'w-6 h-6 text-[#74b2d4]')}
          Investitions-Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          ${formData.includeWp ? `
            <div class="bg-white p-6 rounded-3xl border border-slate-200  relative overflow-hidden flex flex-col hover:border-[#74b2d4] hover:shadow-2xl transition-all">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#74b2d4]"></div>

              <div class="flex justify-between items-start mb-6">
                <div class="bg-[#e3effb] p-3 rounded-2xl">${this.renderIcon('flame', 'w-6 h-6 text-[#74b2d4]')}</div>
                <div class="text-right">
                  <span class="text-xs font-black text-slate-400 uppercase tracking-widest block mb-1">Leistung</span>
                  <div class="flex items-center justify-end gap-1.5">
                    <input type="number" data-model="customWpSize" value="${escapeHtml(formData.customWpSize || ergebnis.baseWpGroesse)}" class="w-16 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-700 outline-none focus:border-[#74b2d4] pb-0.5"/>
                    <span class="font-bold text-slate-700 shrink-0">kW</span>
                  </div>
                </div>
              </div>

              <h4 class="text-xl font-black text-slate-800 mb-4">Wärmepumpe</h4>

              <div class="space-y-3 mt-auto">
                <div class="flex justify-between items-center text-sm">
                  <span class="font-bold text-slate-500">Investition:</span>
                  <div class="flex items-center gap-1.5">
                    <input type="number" data-model="customWpKosten" value="${escapeHtml(formData.customWpKosten || ergebnis.autoKostenWP)}" class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-600 outline-none focus:border-[#74b2d4] pb-0.5"/>
                    <span class="font-black text-slate-500 shrink-0">€</span>
                  </div>
                </div>

                <div class="flex justify-between items-center text-sm text-[#93c21c] bg-[#cfe09b]/20 p-2 rounded-lg">
                  <span class="font-bold">- KfW (${ergebnis.foerderQuote.toFixed(0)}%):</span>
                  <span class="font-black">-${formatInt(ergebnis.kfwZuschussMax)} €</span>
                </div>

                <div class="space-y-2 mt-2">
                  ${(formData.wpDiscounts || []).map((discount, idx) => `
                    <div class="flex justify-between items-center text-sm text-[#93c21c] p-2 bg-slate-50 rounded-lg border border-slate-100">
                      <input type="text" data-discount-type="wp" data-index="${idx}" data-field="name" placeholder="Rabatt Name" value="${escapeHtml(discount.name)}" class="w-24 bg-transparent border-b border-dashed border-slate-300 font-bold outline-none focus:border-[#93c21c] placeholder:text-[#93c21c]/50 text-xs pb-0.5"/>
                      <div class="flex items-center gap-1.5 shrink-0">
                        <span class="font-bold">-</span>
                        <input type="number" data-discount-type="wp" data-index="${idx}" data-field="value" placeholder="0" value="${escapeHtml(discount.value)}" class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none focus:border-[#93c21c] pb-0.5"/>
                        <span class="font-black">€</span>
                        <button data-action="remove-discount" data-type="wp" data-index="${idx}" class="text-red-400 hover:text-red-600 ml-1 transition-colors" title="Rabatt entfernen">
                          ${this.renderIcon('trash-2', 'w-4 h-4')}
                        </button>
                      </div>
                    </div>
                  `).join('')}
                  <button data-action="add-discount" data-type="wp" class="w-full py-2 mt-1 text-xs font-bold text-slate-400 hover:text-[#74b2d4] bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-dashed border-slate-200 flex items-center justify-center gap-1">
                    ${this.renderIcon('plus', 'w-3 h-3')} Weiteren Rabatt hinzufügen
                  </button>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#74b2d4] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#74b2d4]">${formatInt(ergebnis.investWpNetto)} €</span>
                </div>
              </div>
            </div>
          ` : ''}

          ${formData.includeSolar ? `
            <div class="bg-white p-6 rounded-3xl border border-slate-200  relative overflow-hidden flex flex-col hover:border-[#93c21c] hover:shadow-2xl transition-all">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#93c21c]"></div>

              <div class="flex justify-between items-start mb-6">
                <div class="bg-[#cfe09b]/30 p-3 rounded-2xl">${this.renderIcon('sun', 'w-6 h-6 text-[#93c21c]')}</div>
                <div class="text-right">
                  <span class="text-xs font-black text-slate-400 uppercase tracking-widest block mb-1">Leistung</span>
                  <div class="flex items-center justify-end gap-1.5">
                    <input type="number" data-model="customPvSize" value="${escapeHtml(formData.customPvSize || ergebnis.basePvGroesse)}" class="w-16 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-700 outline-none focus:border-[#93c21c] pb-0.5"/>
                    <span class="font-bold text-slate-700 shrink-0">kWp</span>
                  </div>
                </div>
              </div>

              <h4 class="text-xl font-black text-slate-800 mb-4">Photovoltaik</h4>

              <div class="space-y-3 mt-auto">
                <div class="flex justify-between items-center text-sm">
                  <span class="font-bold text-slate-500">Investition:</span>
                  <div class="flex items-center gap-1.5">
                    <input type="number" data-model="customPvKosten" value="${escapeHtml(formData.customPvKosten || ergebnis.autoPvCost)}" class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-600 outline-none focus:border-[#93c21c] pb-0.5"/>
                    <span class="font-black text-slate-500 shrink-0">€</span>
                  </div>
                </div>

                <div class="space-y-2 mt-2">
                  ${(formData.pvDiscounts || []).map((discount, idx) => `
                    <div class="flex justify-between items-center text-sm text-[#93c21c] p-2 bg-slate-50 rounded-lg border border-slate-100">
                      <input type="text" data-discount-type="pv" data-index="${idx}" data-field="name" placeholder="Rabatt Name" value="${escapeHtml(discount.name)}" class="w-24 bg-transparent border-b border-dashed border-slate-300 font-bold outline-none focus:border-[#93c21c] placeholder:text-[#93c21c]/50 text-xs pb-0.5"/>
                      <div class="flex items-center gap-1.5 shrink-0">
                        <span class="font-bold">-</span>
                        <input type="number" data-discount-type="pv" data-index="${idx}" data-field="value" placeholder="0" value="${escapeHtml(discount.value)}" class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none focus:border-[#93c21c] pb-0.5"/>
                        <span class="font-black">€</span>
                        <button data-action="remove-discount" data-type="pv" data-index="${idx}" class="text-red-400 hover:text-red-600 ml-1 transition-colors" title="Rabatt entfernen">
                          ${this.renderIcon('trash-2', 'w-4 h-4')}
                        </button>
                      </div>
                    </div>
                  `).join('')}
                  <button data-action="add-discount" data-type="pv" class="w-full py-2 mt-1 text-xs font-bold text-slate-400 hover:text-[#93c21c] bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-dashed border-slate-200 flex items-center justify-center gap-1">
                    ${this.renderIcon('plus', 'w-3 h-3')} Weiteren Rabatt hinzufügen
                  </button>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#93c21c] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#93c21c]">${formatInt(ergebnis.investPvNetto)} €</span>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200  relative overflow-hidden flex flex-col hover:border-[#93c21c] hover:shadow-2xl transition-all">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#93c21c]"></div>

              <div class="flex justify-between items-start mb-6">
                <div class="bg-[#cfe09b]/30 p-3 rounded-2xl">${this.renderIcon('battery-medium', 'w-6 h-6 text-[#93c21c]')}</div>
                <div class="text-right">
                  <span class="text-xs font-black text-slate-400 uppercase tracking-widest block mb-1">Kapazität</span>
                  <div class="flex items-center justify-end gap-1.5">
                    <input type="number" data-model="customSpeicherSize" value="${escapeHtml(formData.customSpeicherSize || ergebnis.baseSpeicherGroesse)}" class="w-16 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-700 outline-none focus:border-[#93c21c] pb-0.5"/>
                    <span class="font-bold text-slate-700 shrink-0">kWh</span>
                  </div>
                </div>
              </div>

              <h4 class="text-xl font-black text-slate-800 mb-4">Speicher</h4>

              <div class="space-y-3 mt-auto">
                <div class="flex justify-between items-center text-sm">
                  <span class="font-bold text-slate-500">Investition:</span>
                  <div class="flex items-center gap-1.5">
                    <input type="number" data-model="customSpeicherKosten" value="${escapeHtml(formData.customSpeicherKosten || ergebnis.autoSpeicherCost)}" class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-600 outline-none focus:border-[#93c21c] pb-0.5"/>
                    <span class="font-black text-slate-500 shrink-0">€</span>
                  </div>
                </div>

                <div class="space-y-2 mt-2">
                  ${(formData.speicherDiscounts || []).map((discount, idx) => `
                    <div class="flex justify-between items-center text-sm text-[#93c21c] p-2 bg-slate-50 rounded-lg border border-slate-100">
                      <input type="text" data-discount-type="speicher" data-index="${idx}" data-field="name" placeholder="Rabatt Name" value="${escapeHtml(discount.name)}" class="w-24 bg-transparent border-b border-dashed border-slate-300 font-bold outline-none focus:border-[#93c21c] placeholder:text-[#93c21c]/50 text-xs pb-0.5"/>
                      <div class="flex items-center gap-1.5 shrink-0">
                        <span class="font-bold">-</span>
                        <input type="number" data-discount-type="speicher" data-index="${idx}" data-field="value" placeholder="0" value="${escapeHtml(discount.value)}" class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none focus:border-[#93c21c] pb-0.5"/>
                        <span class="font-black">€</span>
                        <button data-action="remove-discount" data-type="speicher" data-index="${idx}" class="text-red-400 hover:text-red-600 ml-1 transition-colors" title="Rabatt entfernen">
                          ${this.renderIcon('trash-2', 'w-4 h-4')}
                        </button>
                      </div>
                    </div>
                  `).join('')}
                  <button data-action="add-discount" data-type="speicher" class="w-full py-2 mt-1 text-xs font-bold text-slate-400 hover:text-[#93c21c] bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-dashed border-slate-200 flex items-center justify-center gap-1">
                    ${this.renderIcon('plus', 'w-3 h-3')} Weiteren Rabatt hinzufügen
                  </button>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#93c21c] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#93c21c]">${formatInt(ergebnis.investSpeicherNetto)} €</span>
                </div>
              </div>
            </div>
          ` : ''}

          ${(formData.includeSolar && ergebnis.activeAuto && formData.includeWallbox !== false) ? `
            <div class="bg-white p-6 rounded-3xl border border-slate-200  relative overflow-hidden flex flex-col hover:border-[#74b2d4] hover:shadow-2xl transition-all">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#74b2d4]"></div>

              <div class="flex justify-between items-start mb-6">
                <div class="bg-[#e3effb] p-3 rounded-2xl">${this.renderIcon('car', 'w-6 h-6 text-[#74b2d4]')}</div>
                <button data-action="toggle-wallbox" class="text-red-400 hover:text-red-600 bg-red-50 p-2 rounded-xl transition-colors" title="Wallbox entfernen">
                  ${this.renderIcon('trash-2', 'w-4 h-4')}
                </button>
              </div>

              <h4 class="text-xl font-black text-slate-800 mb-4">Smarte Wallbox</h4>

              <div class="space-y-3 mt-auto">
                <div class="flex justify-between items-center text-sm">
                  <span class="font-bold text-slate-500">Investition:</span>
                  <div class="flex items-center gap-1.5">
                    <input type="number" data-model="customWallboxKosten" value="${escapeHtml(formData.customWallboxKosten || ergebnis.autoWallboxCost)}" class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-600 outline-none focus:border-[#74b2d4] pb-0.5"/>
                    <span class="font-black text-slate-500 shrink-0">€</span>
                  </div>
                </div>

                <div class="space-y-2 mt-2">
                  ${(formData.wallboxDiscounts || []).map((discount, idx) => `
                    <div class="flex justify-between items-center text-sm text-[#93c21c] p-2 bg-slate-50 rounded-lg border border-slate-100">
                      <input type="text" data-discount-type="wallbox" data-index="${idx}" data-field="name" placeholder="Rabatt Name" value="${escapeHtml(discount.name)}" class="w-24 bg-transparent border-b border-dashed border-slate-300 font-bold outline-none focus:border-[#93c21c] placeholder:text-[#93c21c]/50 text-xs pb-0.5"/>
                      <div class="flex items-center gap-1.5 shrink-0">
                        <span class="font-bold">-</span>
                        <input type="number" data-discount-type="wallbox" data-index="${idx}" data-field="value" placeholder="0" value="${escapeHtml(discount.value)}" class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none focus:border-[#93c21c] pb-0.5"/>
                        <span class="font-black">€</span>
                        <button data-action="remove-discount" data-type="wallbox" data-index="${idx}" class="text-red-400 hover:text-red-600 ml-1 transition-colors" title="Rabatt entfernen">
                          ${this.renderIcon('trash-2', 'w-4 h-4')}
                        </button>
                      </div>
                    </div>
                  `).join('')}
                  <button data-action="add-discount" data-type="wallbox" class="w-full py-2 mt-1 text-xs font-bold text-slate-400 hover:text-[#74b2d4] bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-dashed border-slate-200 flex items-center justify-center gap-1">
                    ${this.renderIcon('plus', 'w-3 h-3')} Weiteren Rabatt hinzufügen
                  </button>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#74b2d4] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#74b2d4]">${formatInt(ergebnis.investWallboxNetto)} €</span>
                </div>
              </div>
            </div>
          ` : (formData.includeSolar && ergebnis.activeAuto) ? `
            <div data-action="toggle-wallbox" class="bg-slate-50 p-6 rounded-3xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-slate-100 hover:border-[#74b2d4] transition-all min-h-[300px]">
              <div class="bg-white p-4 rounded-full mb-4 shadow-sm text-slate-400">${this.renderIcon('plus', 'w-8 h-8')}</div>
              <h4 class="text-lg font-black text-slate-600">Wallbox hinzufügen</h4>
              <p class="text-xs text-slate-400 mt-2 max-w-[200px]">Klicke hier, um eine Ladelösung in die Berechnung aufzunehmen.</p>
            </div>
          ` : ''}
        </div>

        <div class="mt-8 bg-slate-800 rounded-3xl p-8 shadow-2xl flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
          <div class="absolute left-0 top-0 w-2 h-full bg-gradient-to-b from-[#93c21c] to-[#74b2d4]"></div>
          <div>
            <h3 class="text-xl font-black text-white mb-2">Deine finale System-Investition</h3>
            <p class="text-sm text-slate-400">Nach Abzug aller staatlichen Fördermittel und Zuschüsse.</p>
          </div>

          <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8 bg-slate-700/50 p-6 rounded-2xl border border-slate-600 w-full md:w-auto">
            <div class="text-center md:text-left w-full md:w-auto">
              <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Anlagenpreis</span>
              <span class="text-xl font-black text-slate-300">${formatInt(ergebnis.gesamtInvestBrutto)} €</span>
            </div>

            <div class="hidden md:block w-px h-12 bg-slate-600"></div>

            <div class="text-center md:text-left w-full md:w-auto">
              <span class="text-xs font-bold text-[#93c21c] uppercase tracking-widest block mb-1">Förderungen Total</span>
              <span class="text-xl font-black text-[#93c21c]">- ${formatInt(ergebnis.gesamtFoerderung)} €</span>
            </div>

            <div class="hidden md:block w-px h-12 bg-slate-600"></div>

            <div class="text-center md:text-left w-full md:w-auto">
              <span class="text-sm font-black text-[#74b2d4] uppercase tracking-widest block mb-1">Gesamtinvestition</span>
              <span class="text-4xl font-black text-white">${formatInt(ergebnis.nettoInvestition)} €</span>
            </div>
          </div>
        </div>

        <div class="mt-8 bg-white rounded-3xl border border-slate-200  p-8">
          <div class="flex items-center justify-between gap-4 mb-4">
            <h3 class="text-xl font-black text-slate-800">Kostenvergleich nach ${selectedYears} ${selectedYears === 1 ? 'Jahr' : 'Jahren'}</h3>
            <span class="text-sm font-black px-4 py-2 rounded-full ${pctSavings >= 50 ? 'bg-[#cfe09b]/30 text-[#93c21c]' : 'bg-[#e3effb] text-[#74b2d4]'}">
              ${pctSavings}% Einsparung
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
              <p class="text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Fossil kumuliert</p>
              <p class="text-3xl font-black text-slate-700">${formatInt(counters.fossilCounter)} €</p>
            </div>
            <div class="bg-[#e3effb]/30 rounded-2xl p-5 border border-[#c0d8ea]">
              <p class="text-xs font-black uppercase tracking-widest text-[#74b2d4] mb-2">Neu kumuliert</p>
              <p class="text-3xl font-black text-[#74b2d4]">${formatInt(counters.opexSolarCounter)} €</p>
            </div>
            <div class="bg-[#cfe09b]/20 rounded-2xl p-5 border border-[#cfe09b]">
              <p class="text-xs font-black uppercase tracking-widest text-[#93c21c] mb-2">Ersparnis</p>
              <p class="text-3xl font-black text-[#93c21c]">+${formatInt(counters.ersparnisOpexCounter)} €</p>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  `;
};

App.renderAutarkieTab = function (ergebnis) {
  const { formData } = this.state;

  return `
    <div class="animate-fade-in space-y-8">
      <div class="bg-white rounded-3xl p-8 border border-slate-200 ">
        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
          ${this.renderIcon('power', 'w-5 h-5 text-[#93c21c]')}
          Dein Energiebedarf im Vergleich
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-6">
            <div>
              <p class="text-sm text-slate-500 mb-4">
                Deine alten fossilen Verbräuche verursachen hohe laufende Kosten, die wir durch den Systemwechsel drastisch reduzieren.
              </p>

              <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h4 class="text-sm font-black text-slate-700 mb-3 border-b pb-2">Kosten Vorher (pro Jahr)</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between"><span class="text-slate-500">Hausstrom:</span><span class="font-bold">${formatInt(ergebnis.stromKostenHeute)} €</span></div>
                  <div class="flex justify-between"><span class="text-slate-500">Heizung:</span><span class="font-bold">${formatInt(ergebnis.heizKostenHeute)} €</span></div>
                  ${ergebnis.activeAuto ? `<div class="flex justify-between"><span class="text-slate-500">Sprit:</span><span class="font-bold">${formatInt(ergebnis.kostenAutoHeute)} €</span></div>` : ''}
                  <div class="flex justify-between"><span class="text-slate-500">Wartung:</span><span class="font-bold">${formatInt(ergebnis.wartungAlt_pa)} €</span></div>
                  <div class="flex justify-between font-black text-slate-800 border-t border-slate-200 pt-2 mt-2">
                    <span>Gesamtkosten vorher:</span>
                    <span>${formatInt(ergebnis.stromKostenHeute + ergebnis.heizKostenHeute + ergebnis.kostenAutoHeute + ergebnis.wartungAlt_pa)} € / Jahr</span>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <h4 class="text-sm font-black text-[#74b2d4] mb-3">Neuer Energiebedarf (Vernetzt)</h4>
              <div class="space-y-3">
                <div class="flex justify-between items-center bg-slate-50 p-3 rounded-lg">
                  <span class="text-xs font-bold text-slate-500">Hausstrom</span>
                  <div class="text-right leading-tight">
                    <span class="font-black text-slate-700">${formatInt(ergebnis.hausStrombedarf)} kWh</span>
                    <span class="block text-[10px] text-slate-400 font-bold">= ${formatInt(ergebnis.kostenHausNeu)} €</span>
                  </div>
                </div>

                ${formData.includeWp ? `
                  <div class="flex justify-between items-center bg-[#e3effb]/50 p-3 rounded-lg">
                    <span class="text-xs font-bold text-[#74b2d4]">Wärmepumpe</span>
                    <div class="text-right leading-tight">
                      <span class="font-black text-[#74b2d4]">${formatInt(ergebnis.wpStrombedarf)} kWh</span>
                      <span class="block text-[10px] text-[#74b2d4] font-bold">
                        = ${formatInt(ergebnis.kostenWPNeu)} €
                        ${ergebnis.heizKostenHeute > 0 ? `<span class="text-[#93c21c] ml-1">(-${Math.round(ergebnis.ersparnisHeizungPct)}%)</span>` : ''}
                      </span>
                    </div>
                  </div>
                ` : ''}

                ${ergebnis.activeAuto ? `
                  <div class="flex justify-between items-center bg-[#e3effb]/50 p-3 rounded-lg">
                    <span class="text-xs font-bold text-[#74b2d4]">E-Auto</span>
                    <div class="text-right leading-tight">
                      <span class="font-black text-[#74b2d4]">${formatInt(ergebnis.autoStrombedarf)} kWh</span>
                      <span class="block text-[10px] text-[#74b2d4] font-bold">
                        = ${formatInt(ergebnis.kostenAutoNeu)} €
                        ${ergebnis.kostenAutoHeute > 0 ? `<span class="text-[#93c21c] ml-1">(-${Math.round(ergebnis.ersparnisAutoPct)}%)</span>` : ''}
                      </span>
                    </div>
                  </div>
                ` : ''}

                <div class="flex justify-between items-center border-t-2 border-slate-200 pt-2">
                  <span class="text-sm font-black text-slate-800">Gesamtstrombedarf Neu</span>
                  <div class="text-right leading-tight">
                    <span class="font-black text-lg text-slate-800">${formatInt(ergebnis.gesamtbedarf)} kWh</span>
                    <span class="block text-xs text-slate-500 font-bold">
                      = ${formatInt(ergebnis.kostenGesamtNeu)} € / Jahr
                      ${ergebnis.ersparnisGesamtPct > 0 ? `<span class="text-[#93c21c] ml-1">(-${Math.round(ergebnis.ersparnisGesamtPct)}%)</span>` : ''}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-4">
            <div class="bg-[#cfe09b]/20 p-5 rounded-2xl border border-[#cfe09b]">
              <h4 class="font-black text-[#93c21c] mb-2 flex items-center gap-2">
                ${this.renderIcon('check-square', 'w-4 h-4')}
                Unabhängigkeit & Netz
              </h4>

              <div class="space-y-2 text-sm text-slate-700">
                <div class="flex justify-between items-center border-b border-[#cfe09b]/50 pb-1">
                  <span>Energetische Unabhängigkeit:</span>
                  <span class="font-black text-[#93c21c] text-lg">${formatInt(ergebnis.autarkieQuote)} %</span>
                </div>
                <div class="flex justify-between items-center pb-1">
                  <span>Verbleibende Netz-Abhängigkeit:</span>
                  <span class="font-bold text-slate-500">${100 - Math.round(ergebnis.autarkieQuote)} %</span>
                </div>
                <div class="flex justify-between items-center text-xs text-slate-500 pt-2"><span>Eigenverbrauch Solarstrom:</span><span>${Math.round(ergebnis.eigenverbrauchQuote)} %</span></div>
                <div class="flex justify-between items-center text-xs text-slate-500"><span>Einspeisung ins Netz:</span><span>${Math.round(ergebnis.einspeisungQuote)} %</span></div>
              </div>
            </div>

            ${formData.includeSolar ? `
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 flex flex-col items-center shadow-sm">
                  <div class="relative w-20 h-20 mb-4 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c]" stroke-width="3" stroke-dasharray="97.38" stroke-dashoffset="${97.38 - (97.38 * Math.min(100, Math.max(0, ergebnis.autarkieQuote)) / 100)}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-xl font-black text-slate-700 absolute">${Math.round(ergebnis.autarkieQuote)}%</span>
                  </div>
                  <h4 class="text-xs font-black uppercase text-slate-800 mb-1 text-center">Autarkiegrad</h4>
                  <p class="text-[10px] text-slate-500 mb-3 text-center">Energetische Unabhängigkeit</p>
                </div>

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 flex flex-col items-center shadow-sm">
                  <div class="relative w-20 h-20 mb-4 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#74b2d4]" stroke-width="3" stroke-dasharray="97.38" stroke-dashoffset="${97.38 - (97.38 * Math.min(100, Math.max(0, ergebnis.eigenverbrauchQuote)) / 100)}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-xl font-black text-slate-700 absolute">${Math.round(ergebnis.eigenverbrauchQuote)}%</span>
                  </div>
                  <h4 class="text-xs font-black uppercase text-slate-800 mb-1 text-center">Eigenverbrauchsquote</h4>
                  <p class="text-[10px] text-slate-500 mb-3 text-center">Nutzung des PV-Stroms</p>
                </div>

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 flex flex-col items-center shadow-sm">
                  <div class="relative w-20 h-20 mb-4 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c]" stroke-width="3" stroke-dasharray="97.38" stroke-dashoffset="${97.38 - (97.38 * Math.min(100, Math.max(0, ergebnis.finanzAutarkieQuote)) / 100)}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-xl font-black text-slate-700 absolute">${Math.round(ergebnis.finanzAutarkieQuote)}%</span>
                  </div>
                  <h4 class="text-xs font-black uppercase text-slate-800 mb-1 text-center">Finanzielle Unabhängigkeit</h4>
                  <p class="text-[10px] text-slate-500 mb-3 text-center">Schutz vor Preisanstieg</p>
                </div>
              </div>
            ` : ''}

            <p class="text-[10px] text-slate-500 leading-relaxed px-2 mt-4">
              <strong>Warum empfehlen wir genau ${escapeHtml(ergebnis.pvGroesse)} kWp PV und ${escapeHtml(ergebnis.speicherGroesse)} kWh Speicher?</strong><br/>
              Da dein zukünftiger Strombedarf primär in der Übergangszeit und im Winter anfällt, muss die Photovoltaik so groß dimensioniert sein, dass sie auch bei schwächerer Sonne ausreichend Strom liefert.
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-8 border border-slate-200 ">
        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
          ${this.renderIcon('wind', 'w-5 h-5 text-[#74b2d4]')}
          Saisonale Autarkie
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          ${ergebnis.saisonDaten.map((s) => {
            const icon = s.name === 'Winter' ? 'snowflake' : s.name === 'Frühling' ? 'leaf' : s.name === 'Sommer' ? 'sun' : 'wind';
            const dashOffset = 97.38 - (97.38 * s.autarkie / 100);

            return `
              <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 flex flex-col relative overflow-hidden">
                <div class="flex items-center gap-2 mb-4">
                  ${this.renderIcon(icon, `w-4 h-4 ${s.color}`)}
                  <span class="text-[10pt] font-black uppercase tracking-wider ${s.color}">${escapeHtml(s.name)}</span>
                </div>

                <div class="flex items-center gap-4 mb-4">
                  <div class="relative w-16 h-16 flex items-center justify-center shrink-0">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="${s.stroke}" stroke-width="3" stroke-dasharray="97.38" stroke-dashoffset="${dashOffset}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-[10px] font-black text-slate-700 absolute">${s.autarkie}%</span>
                  </div>
                  <div><p class="text-[7pt] text-slate-500 font-bold uppercase tracking-widest">Deckung</p></div>
                </div>

                <div class="space-y-1 border-t border-slate-200 pt-3 text-[8pt]">
                  <div class="flex justify-between text-slate-500"><span>Bedarf</span><span class="font-bold">${formatInt(s.verbrauch)} kWh</span></div>
                  <div class="flex justify-between text-[#93c21c]"><span>PV/Akku</span><span class="font-bold">${formatInt(s.covered)} kWh (${s.pctCovered}%)</span></div>
                  <div class="flex justify-between text-slate-500"><span>Zukauf</span><span class="font-bold">${formatInt(s.restbezug)} kWh (${s.pctRestbezug}%)</span></div>
                  <div class="flex justify-between text-[#74b2d4] mt-1 border-t border-slate-100 pt-1"><span>Einspeisung:</span><span class="font-bold">${formatInt(s.einspeisung)} kWh (${s.pctEinspeisung}%*)</span></div>
                </div>
              </div>
            `;
          }).join('')}
        </div>

        <p class="text-[8pt] text-slate-400 mt-4 text-center">
          * Der Prozentwert der Einspeisung bezieht sich auf den gesamten Solarertrag der jeweiligen Jahreszeit.
        </p>
      </div>

      ${(formData.includeWp && formData.includeSolar) ? `
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl mt-8">
          <h3 class="text-lg font-black text-slate-800 mb-2 flex items-center gap-2">
            ${this.renderIcon('pie-chart', 'w-6 h-6 text-[#93c21c]')} Wärmebedarf vs. Solarertrag
          </h3>
          <p class="text-sm text-slate-600 mb-8">
            <strong>Wie viel Energie benötigst du und wie viel erzeugst du selbst?</strong><br/>
            Diese Gegenüberstellung visualisiert das dynamische Zusammenspiel deines Wärmepumpen-Lastprofils und deines Solarertrags im Jahresverlauf. Der äußere Ring (Grün) zeigt den Anteil des Solarertrags, der innere (Blau) den Anteil des Heizbedarfs am jeweiligen Jahresgesamtwert.
          </p>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative mt-6">
            ${ergebnis.saisonDaten.map((s) => {
              const pctErtrag = ergebnis.pvProduktion > 0 ? Math.round((s.ertrag / ergebnis.pvProduktion) * 100) : 0;
              const pctHeiz = ergebnis.wpStrombedarf > 0 ? Math.round((s.heizbedarf / ergebnis.wpStrombedarf) * 100) : 0;
              const outerDash = 97.38;
              const innerDash = 72.25;

              return `
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl flex flex-col items-center gap-4 relative group hover:border-[#cfe09b] transition-colors shadow-sm">
                  <span class="font-black text-sm text-slate-700 uppercase tracking-widest">${escapeHtml(s.name)}</span>
                  <div class="relative w-28 h-28 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c]" stroke-width="3" stroke-dasharray="${outerDash}" stroke-dashoffset="${outerDash - (outerDash * Math.min(100, pctErtrag) / 100)}" stroke-linecap="round"></circle>
                      <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#74b2d4]" stroke-width="3" stroke-dasharray="${innerDash}" stroke-dashoffset="${innerDash - (innerDash * Math.min(100, pctHeiz) / 100)}" stroke-linecap="round"></circle>
                    </svg>
                  </div>
                  <div class="w-full space-y-1.5 border-t border-slate-200 pt-3 text-[11px] md:text-xs">
                    <div class="flex justify-between items-center"><span class="text-slate-500 font-bold">Ertrag</span> <span class="font-black text-[#93c21c]">${formatInt(s.ertrag)} kWh (${pctErtrag}%)</span></div>
                    <div class="flex justify-between items-center"><span class="text-slate-500 font-bold">Heizung</span> <span class="font-black text-[#74b2d4]">${formatInt(s.heizbedarf)} kWh (${pctHeiz}%)</span></div>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
          <div class="flex justify-center flex-wrap gap-4 md:gap-8 mt-8 pt-6 border-t border-slate-100">
            <div class="flex items-center gap-2"><div class="w-4 h-4 border-4 border-[#93c21c] rounded-full"></div><span class="text-xs md:text-sm font-bold text-slate-600">Äußerer Ring: Solarertrag</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 border-4 border-[#74b2d4] rounded-full"></div><span class="text-xs md:text-sm font-bold text-slate-600">Innerer Ring: Wärmebedarf WP</span></div>
          </div>
        </div>
      ` : ''}

      ${formData.includeSolar ? `
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl mt-8">
          <h3 class="text-lg font-black text-slate-800 mb-2 flex items-center gap-2">
            ${this.renderIcon('pie-chart', 'w-6 h-6 text-[#74b2d4]')} Gesamtbedarf vs. Solarertrag
          </h3>
          <p class="text-sm text-slate-600 mb-8">
            <strong>Wie verteilt sich dein kompletter Strombedarf (Haus, Auto, WP) im Vergleich zur Sonne?</strong><br/>
            Diese Gegenüberstellung zeigt deinen gesamten Strombedarf und deinen Solarertrag im Jahresverlauf. Der äußere Ring (Grün) zeigt deinen Ertragsanteil in der jeweiligen Jahreszeit. Der innere Ring (Blau) zeigt, wie viel Prozent deines Jahres-Gesamtbedarfs in diese Zeit fallen.
          </p>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative mt-6">
            ${ergebnis.saisonDaten.map((s) => {
              const pctErtrag = ergebnis.pvProduktion > 0 ? Math.round((s.ertrag / ergebnis.pvProduktion) * 100) : 0;
              const pctGesamt = ergebnis.gesamtbedarf > 0 ? Math.round((s.verbrauch / ergebnis.gesamtbedarf) * 100) : 0;
              const outerDash = 97.38;
              const innerDash = 72.25;

              return `
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl flex flex-col items-center gap-4 relative group hover:border-[#cfe09b] transition-colors shadow-sm">
                  <span class="font-black text-sm text-slate-700 uppercase tracking-widest">${escapeHtml(s.name)}</span>
                  <div class="relative w-28 h-28 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c]" stroke-width="3" stroke-dasharray="${outerDash}" stroke-dashoffset="${outerDash - (outerDash * Math.min(100, pctErtrag) / 100)}" stroke-linecap="round"></circle>
                      <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#74b2d4]" stroke-width="3" stroke-dasharray="${innerDash}" stroke-dashoffset="${innerDash - (innerDash * Math.min(100, pctGesamt) / 100)}" stroke-linecap="round"></circle>
                    </svg>
                  </div>
                  <div class="w-full space-y-1.5 border-t border-slate-200 pt-3 text-[11px] md:text-xs">
                    <div class="flex justify-between items-center"><span class="text-slate-500 font-bold">Ertrag</span> <span class="font-black text-[#93c21c]">${formatInt(s.ertrag)} kWh (${pctErtrag}%)</span></div>
                    <div class="flex justify-between items-center"><span class="text-slate-500 font-bold">Gesamt</span> <span class="font-black text-[#74b2d4]">${formatInt(s.verbrauch)} kWh (${pctGesamt}%)</span></div>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
          <div class="flex justify-center flex-wrap gap-4 md:gap-8 mt-8 pt-6 border-t border-slate-100">
            <div class="flex items-center gap-2"><div class="w-4 h-4 border-4 border-[#93c21c] rounded-full"></div><span class="text-xs md:text-sm font-bold text-slate-600">Äußerer Ring: Solarertrag</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 border-4 border-[#74b2d4] rounded-full"></div><span class="text-xs md:text-sm font-bold text-slate-600">Innerer Ring: Gesamtbedarf</span></div>
          </div>
        </div>
      ` : ''}

      <div class="bg-[#cfe09b]/20 rounded-3xl p-8 border border-[#cfe09b] ">
        <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
          ${this.renderIcon('leaf', 'w-6 h-6 text-[#93c21c]')}
          Dein aktiver Klimaschutz
        </h3>

        <p class="text-sm text-slate-600 mb-6">
          Durch deinen Systemwechsel sparst du massiv CO₂ ein. Hier ist deine persönliche Umweltbilanz, hochgerechnet auf die Lebensdauer deines Systems:
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          ${[1, 10, 20, 30].map(years => `
            <div class="bg-white p-4 rounded-2xl border border-[#cfe09b] text-center shadow-sm">
              <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">${years === 1 ? 'Pro Jahr' : `${years} Jahre`}</span>
              <span class="block text-2xl font-black text-[#93c21c]">${(ergebnis.co2ErsparnisPerYear * years).toFixed(1).replace('.', ',')} t</span>
            </div>
          `).join('')}
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#cfe09b] flex flex-col sm:flex-row items-center gap-5 shadow-sm text-center sm:text-left">
          <div class="bg-[#cfe09b]/30 p-4 rounded-full shrink-0">${this.renderIcon('leaf', 'w-8 h-8 text-[#93c21c]')}</div>
          <p class="text-sm text-slate-700 leading-relaxed">
            Deine jährliche Einsparung entspricht der CO₂-Speicherkraft von
            <strong class="text-[#93c21c]">${formatInt(ergebnis.co2Baeume)} ausgewachsenen Bäumen</strong>
            oder einer Mischwaldfläche von
            <strong class="text-[#93c21c]">${formatInt(ergebnis.co2FlaecheQm)} Quadratmetern</strong>
            pro Jahr.
          </p>
        </div>
      </div>
    </div>
  `;
};
App.renderTechnikTab = function (ergebnis) {
  const { formData } = this.state;

  return `
    <div class="animate-fade-in space-y-8">
      <div class="bg-white rounded-3xl p-8 border border-slate-200 ">
        <h3 class="text-xl font-black text-slate-800 mb-2 flex items-center gap-2">
          ${this.renderIcon('award', 'w-6 h-6 text-[#74b2d4]')}
          Premium-Hardware für Jahrzehnte
        </h3>

        <p class="text-sm leading-relaxed text-slate-600 mb-8">
          Ein Energie-Systemwechsel ist eine Investition, die über Generationen halten muss. Deshalb verbauen wir ausschließlich High-End-Komponenten, die nicht nur auf dem Datenblatt glänzen, sondern in der harten Praxis bestehen.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          ${formData.includeWp ? `
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm">
              ${this.renderIcon('flame', 'w-10 h-10 text-[#74b2d4] mb-4')}
              <h4 class="text-lg font-black text-slate-800 mb-3">Die intelligente Wärmepumpe</h4>
              <ul class="text-sm text-slate-600 space-y-3 list-none">
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Flüsterleise:</strong> Mit &lt; 35 dB(A) leiser als ein moderner Kühlschrank.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Heizen & Kühlen:</strong> Aktive Klimafunktion im Sommer über die Fußbodenheizung inklusive.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Maximaler COP:</strong> Aus 1 kWh Strom werden 4-5 kWh Wärme gewonnen.</span></li>
              </ul>
            </div>
          ` : ''}

          ${formData.includeSolar ? `
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm">
              ${this.renderIcon('sun', 'w-10 h-10 text-[#93c21c] mb-4')}
              <h4 class="text-lg font-black text-slate-800 mb-3">Bifaziale Photovoltaik</h4>
              <ul class="text-sm text-slate-600 space-y-3 list-none">
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Glas-Glas-Technologie:</strong> Absolut sturmfest, keine Mikrorisse, höchste Brandschutzklasse.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#93c21c] shrink-0 mt-0.5')}<span><strong>30 Jahre Garantie:</strong> Lineare Leistungsgarantie für ein langes Anlagenleben.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Schwachlicht-Stark:</strong> Hervorragende Erträge auch bei bewölktem Himmel.</span></li>
              </ul>
            </div>

            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm">
              ${this.renderIcon('battery-medium', 'w-10 h-10 text-[#93c21c] mb-4')}
              <h4 class="text-lg font-black text-slate-800 mb-3">LiFePO4 Stromspeicher</h4>
              <ul class="text-sm text-slate-600 space-y-3 list-none">
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Maximale Sicherheit:</strong> Thermisch stabil, brennt und explodiert nicht.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Zyklenfestigkeit:</strong> Entwickelt für 10.000+ Ladezyklen.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Schwarzstartfähig:</strong> Optional versorgt das System dein Haus nahtlos weiter.</span></li>
              </ul>
            </div>
          ` : ''}

          ${(formData.includeSolar && ergebnis.activeAuto) ? `
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm">
              ${this.renderIcon('car', 'w-10 h-10 text-[#74b2d4] mb-4')}
              <h4 class="text-lg font-black text-slate-800 mb-3">Smarte Premium-Wallbox</h4>
              <ul class="text-sm text-slate-600 space-y-3 list-none">
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Überschussladen:</strong> Lädt automatisch mit Sonnenstrom.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>V2H / V2G Ready:</strong> Vorbereitet für bidirektionales Laden.</span></li>
                <li class="flex gap-2 items-start">${this.renderIcon('check-circle-2', 'w-4 h-4 text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>App & Abrechnung:</strong> Volle Kontrolle via App und RFID.</span></li>
              </ul>
            </div>
          ` : ''}
        </div>
      </div>

      <div class="bg-slate-800 rounded-3xl p-8  text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#93c21c] to-[#74b2d4]"></div>

        <h3 class="text-2xl font-black mb-8 flex items-center gap-3">
          ${this.renderIcon('shield-check', 'w-8 h-8 text-[#93c21c]')}
          Warum Solar Aspekt?
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="flex gap-4">
            <div class="bg-slate-700 p-3 rounded-xl h-fit shrink-0">${this.renderIcon('hard-hat', 'w-6 h-6 text-[#93c21c]')}</div>
            <div>
              <h4 class="text-lg font-black mb-1">Regionale Meisterqualität</h4>
              <p class="text-slate-300 text-sm leading-relaxed">Eigene, zertifizierte Handwerker aus der Region montieren und warten deine Anlage.</p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="bg-slate-700 p-3 rounded-xl h-fit shrink-0">${this.renderIcon('file-text', 'w-6 h-6 text-[#74b2d4]')}</div>
            <div>
              <h4 class="text-lg font-black mb-1">Förder-Garantie</h4>
              <p class="text-slate-300 text-sm leading-relaxed">Wir übernehmen den kompletten Behörden-Dschungel von KfW bis Netzanmeldung.</p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="bg-slate-700 p-3 rounded-xl h-fit shrink-0">${this.renderIcon('wrench', 'w-6 h-6 text-[#74b2d4]')}</div>
            <div>
              <h4 class="text-lg font-black mb-1">Alles aus einer Hand</h4>
              <p class="text-slate-300 text-sm leading-relaxed">Planung, Gerüstbau, Dachmontage, Elektrik und Heizungsbau aus einem Guss.</p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="bg-slate-700 p-3 rounded-xl h-fit shrink-0">${this.renderIcon('zap', 'w-6 h-6 text-[#93c21c]')}</div>
            <div>
              <h4 class="text-lg font-black mb-1">Energiemanagement (HEMS)</h4>
              <p class="text-slate-300 text-sm leading-relaxed">WP, Speicher und Auto werden perfekt vernetzt und intelligent gesteuert.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
};

App.renderDashboardTabContent = function (ergebnis, counters) {
  const { activeTab } = this.state;

  if (activeTab === 'FINANZEN') return this.renderFinanceTab(ergebnis, counters);
  if (activeTab === 'AUTARKIE') return this.renderAutarkieTab(ergebnis, counters);
  return this.renderTechnikTab(ergebnis, counters);
};

App.downloadPDF = function () {
  if (this.state.isGeneratingPDF) return;

  this.setState({ isGeneratingPDF: true });

  const generate = () => {
    const element = document.getElementById('pdf-export-wrapper');

    if (!element || !window.html2pdf) {
      this.setState({ isGeneratingPDF: false });
      return;
    }

    const opt = {
      margin: 0,
      filename: `SolarAspekt_Fahrplan_${this.state.formData.standortPlz || 'Wirtschaftlichkeit'}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: {
        scale: 2,
        useCORS: true,
        windowWidth: 794,
        scrollX: 0,
        scrollY: 0,
        backgroundColor: '#ffffff'
      },
      jsPDF: {
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
      },
      pagebreak: {
        mode: ['css', 'legacy']
      }
    };

    window.html2pdf()
      .set(opt)
      .from(element)
      .save()
      .then(() => this.setState({ isGeneratingPDF: false }))
      .catch(() => this.setState({ isGeneratingPDF: false }));
  };

  if (!window.html2pdf) {
    const existing = document.querySelector('script[data-pdf-loader="1"]');
    if (existing) return;

    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
    script.dataset.pdfLoader = '1';
    script.onload = generate;
    script.onerror = () => this.setState({ isGeneratingPDF: false });
    document.body.appendChild(script);
  } else {
    generate();
  }
};

App.printPdfDirect = function () {
  const ergebnis = this.calculateErgebnis();

  let source = document.getElementById('pdf-export-wrapper');
  let tempHost = null;

  // Build a hidden print source if preview is currently closed
  if (!source) {
    const prevShow = this.state.showPdfPreview;
    this.state.showPdfPreview = true;

    const previewHtml = this.renderPdfPreview(ergebnis).trim();

    this.state.showPdfPreview = prevShow;

    const tpl = document.createElement('template');
    tpl.innerHTML = previewHtml;

    const built = tpl.content.querySelector('#pdf-export-wrapper');

    if (!built) {
      console.error('Print failed: #pdf-export-wrapper could not be built.');
      return;
    }

    tempHost = document.createElement('div');
    tempHost.style.position = 'fixed';
    tempHost.style.left = '-99999px';
    tempHost.style.top = '0';
    tempHost.style.width = '794px';
    tempHost.style.opacity = '0';
    tempHost.style.pointerEvents = 'none';
    tempHost.appendChild(built.cloneNode(true));
    document.body.appendChild(tempHost);

    const tempBonusEl = tempHost.querySelector('#bonusValue');
    if (tempBonusEl) {
      tempBonusEl.textContent = formatInt(ergebnis.gutscheinSumme) + ' €';
    }
    source = tempHost.querySelector('#pdf-export-wrapper');
  }

  const printWindow = window.open('', '_blank', 'width=900,height=1200');
  if (!printWindow) {
    if (tempHost) tempHost.remove();
    return;
  }

  const currentStyles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]'))
    .map((node) => node.outerHTML)
    .join('\n');

  printWindow.document.open();
  printWindow.document.write(`
    <!DOCTYPE html>
    <html lang="de">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Druckansicht</title>
      ${currentStyles}
      <style>
        @page {
          size: A4 portrait;
          margin: 0;
        }

        html, body {
          margin: 0 !important;
          padding: 0 !important;
          background: #fff !important;
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }

        #print-root {
          width: 794px !important;
          margin: 0 auto !important;
          background: #fff !important;
        }

        .pdf-page {
          width: 794px !important;
          min-height: 1123px !important;
          box-sizing: border-box !important;
          page-break-after: always !important;
          break-after: page !important;
          overflow: hidden !important;
        }

        .pdf-page.no-page-break,
        .pdf-page:last-child {
          page-break-after: auto !important;
          break-after: auto !important;
        }

        .fixed,
        .sticky {
          position: static !important;
        }
      </style>
    </head>
    <body>
      <div id="print-root">${source.outerHTML}</div>
    </body>
    </html>
  `);
  printWindow.document.close();

  const cleanup = () => {
    if (tempHost) tempHost.remove();
  };

  const triggerPrint = () => {
    setTimeout(() => {
      try {
        printWindow.focus();
        printWindow.print();
      } catch (e) {
        console.error(e);
      }
    }, 400);
  };

  const imgs = Array.from(printWindow.document.images || []);
  if (!imgs.length) {
    triggerPrint();
  } else {
    let done = 0;
    const finish = () => {
      done += 1;
      if (done >= imgs.length) triggerPrint();
    };

    imgs.forEach((img) => {
      if (img.complete) {
        finish();
      } else {
        img.addEventListener('load', finish, { once: true });
        img.addEventListener('error', finish, { once: true });
      }
    });

    setTimeout(triggerPrint, 2000);
  }

  printWindow.onafterprint = () => {
    cleanup();
    setTimeout(() => {
      try {
        printWindow.close();
      } catch (_) {}
    }, 200);
  };
};
// ─────────────────────────────────────────────────────────────
// MODEL SIDE EFFECTS
// ─────────────────────────────────────────────────────────────
App.handleModelUpdate = function (key, value) {
      let next = value;
      if (key === 'standortPlz') {
        next = String(value).replace(/\D/g, '').slice(0, 5);
      }

      this.state.formData[key] = next;
      this.render(); // Renders safely now, because it only fires when leaving the field

      if (key === 'standortPlz' && String(next).length === 5) {
        this.lookupPostcode();
      }
    };
// ─────────────────────────────────────────────────────────────
// EVENT BINDING
// ─────────────────────────────────────────────────────────────
App.bindEvents = function () {
  if (this._bound) return;
  this._bound = true;

  this.root.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;

    const action = btn.dataset.action;
    const id = btn.dataset.id;
    const tab = btn.dataset.tab;
    const years = btn.dataset.years ? parseInt(btn.dataset.years, 10) : null;
    const system = btn.dataset.system;

    switch (action) {
      case 'go-home':
        this.setState({ stage: 'START', quizStep: 0 });
        window.scrollTo(0, 0);
        break;

      case 'start-quiz':
        this.setState({ stage: 'QUIZ', quizStep: 0 });
        window.scrollTo(0, 0);
        break;

        case 'print-a4-preview':
        case 'print-pdf-direct':
          this.printPdfDirect();
          break;

        case 'back-to-quiz':
        this.setState({
          stage: 'QUIZ',
          quizStep: 0,
          showContactModal: false,
          showPdfPreview: false,
          showCalculationModal: false
        });
        window.scrollTo(0, 0);
        break;

      case 'next-quiz-step':
        this.setState({ quizStep: Math.min(2, this.state.quizStep + 1) });
        break;

      case 'prev-quiz-step':
        this.setState({ quizStep: Math.max(0, this.state.quizStep - 1) });
        break;

      case 'calculate-complete':
        this.updateData('includeSolar', true);
        this.updateData('includeWp', true);
        this.handleCalculate();
        break;

      case 'calculate-wp-only':
        this.updateData('includeSolar', false);
        this.updateData('includeWp', true);
        this.handleCalculate();
        break;

      case 'open-project-modal':
        this.setState({ showProjectModal: true });
        break;

      case 'close-project-modal':
        this.setState({ showProjectModal: false });
        break;

      case 'save-project':
        this.saveProject(false);
        break;

      case 'save-project-new':
        this.saveProject(true);
        break;

      case 'load-project':
        if (id) this.loadProject(id);
        break;

      case 'delete-project':
        if (id) this.deleteProject(id);
        break;

      case 'new-empty-project':
        this.startNewProject();
        break;

      case 'open-contact-modal':
        this.setState({ showContactModal: true, contactSuccess: false });
        break;

      case 'close-contact-modal':
        this.setState({ showContactModal: false, contactSuccess: false });
        break;

      case 'submit-contact-lead':
        this.setState({ contactSuccess: true });
        break;

      case 'open-calc-modal':
        this.setState({ showCalculationModal: true });
        break;

      case 'close-calc-modal':
        this.setState({ showCalculationModal: false });
        break;

      case 'open-pdf-preview':
        this.setState({ showPdfPreview: true });
        break;

      case 'close-pdf-preview':
        this.setState({ showPdfPreview: false });
        break;

      case 'download-pdf':
        this.downloadPDF();
        break;

      case 'set-tab':
        if (tab) this.setState({ activeTab: tab });
        break;

      case 'set-years':
        if (years) this.setState({ selectedYears: years });
        break;

      case 'set-system':
        if (system === 'complete') {
          this.updateData('includeWp', true);
          this.updateData('includeSolar', true);
        } else if (system === 'wp-only') {
          this.updateData('includeWp', true);
          this.updateData('includeSolar', false);
        } else if (system === 'pv-only') {
          this.updateData('includeWp', false);
          this.updateData('includeSolar', true);
        }
        break;

        case 'toggle-wallbox':
        const isWallboxActive = this.state.formData.includeWallbox !== false;
        this.updateData('includeWallbox', !isWallboxActive);
        break;

        case 'add-discount': {
        const type = btn.dataset.type;
        const key = type + 'Discounts';
        const arr = [...(this.state.formData[key] || [])];
        arr.push({ name: '', value: '' });
        this.updateData(key, arr);
        break;
      }

      case 'remove-discount': {
        const type = btn.dataset.type;
        const idx = parseInt(btn.dataset.index, 10);
        const key = type + 'Discounts';
        const arr = [...(this.state.formData[key] || [])];
        arr.splice(idx, 1);
        this.updateData(key, arr);
        break;
      }
    }
  });

this.root.addEventListener('input', (e) => {
    const el = e.target;

    // 1. Standard Form Inputs
    if (el.matches('[data-model]')) {
      // Save values silently in the background while typing (prevents focus loss)
      const key = el.getAttribute('data-model');
      let val = el.value;
      if (key === 'standortPlz') {
        val = String(val).replace(/\D/g, '').slice(0, 5);
        el.value = val;
      }
      this.state.formData[key] = val;
      return;
    }

    // 2. Project Name Input
    if (el.matches('[data-project-name]')) {
      this.state.newProjectName = el.value; // Save silently
      return;
    }

    // 3. Dynamic Discount Inputs (Save silently while typing)
    if (el.matches('[data-discount-type]')) {
      const type = el.getAttribute('data-discount-type');
      const idx = parseInt(el.getAttribute('data-index'), 10);
      const field = el.getAttribute('data-field');
      const key = type + 'Discounts';
      
      if (!this.state.formData[key]) this.state.formData[key] = [];
      if (this.state.formData[key][idx]) {
        this.state.formData[key][idx][field] = el.value;
      }
      return;
    }
  });

  this.root.addEventListener('change', (e) => {
    const el = e.target;
    
    // 1. Standard Form Inputs (Triggers full recalculation and re-render)
    if (el.matches('[data-model]')) {
      this.handleModelUpdate(el.getAttribute('data-model'), el.value);
      return;
    }

    // 2. Dynamic Discount Inputs (Triggers full recalculation and re-render)
    if (el.matches('[data-discount-type]')) {
      const type = el.getAttribute('data-discount-type');
      const idx = parseInt(el.getAttribute('data-index'), 10);
      const field = el.getAttribute('data-field');
      const key = type + 'Discounts';
      
      const arr = [...(this.state.formData[key] || [])];
      if (arr[idx]) {
        arr[idx][field] = el.value;
        this.updateData(key, arr);
      }
    }
  });

}; // End of App.bindEvents

// ─────────────────────────────────────────────────────────────
// APP BOOTSTRAP
// ─────────────────────────────────────────────────────────────
App.init = function (selector = '#app') {
  this.root = document.querySelector(selector);

  if (!this.root) {
    throw new Error(`App root not found: ${selector}`);
  }

  this.loadProjects();
  this.bootstrapFromPreset();
  this.render();
};

window.SolarAspektApp = App;

document.addEventListener('DOMContentLoaded', () => {
  App.init('#app');
});

  </script>
  
</body>
</html>