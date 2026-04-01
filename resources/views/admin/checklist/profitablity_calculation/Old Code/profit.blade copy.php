<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Solar Aspekt – Wirtschaftlichkeitsrechner</title>

  <script src="https://cdn.tailwindcss.com"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <style>
    :root {
      --brand-blue: #74b2d4;
      --brand-blue-dark: #5c8fa8;
      --brand-green: #93c21c;
      --brand-green-dark: #82ad18;
      --brand-green-soft: #cfe09b;
      --slate-50: #f8fafc;
      --slate-100: #f1f5f9;
      --slate-200: #e2e8f0;
      --slate-300: #cbd5e1;
      --slate-400: #94a3b8;
      --slate-500: #64748b;
      --slate-600: #475569;
      --slate-700: #334155;
      --slate-800: #1e293b;
      --slate-900: #0f172a;
    }

    html, body {
      margin: 0; padding: 0;
      background: var(--slate-50);
      color: var(--slate-700);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    * { box-sizing: border-box; }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type="number"] { -moz-appearance: textfield; }

    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    .app-shell { min-height: 100vh; padding-bottom: 8rem; } /* Padding for sticky footer */

    .shadow-soft { box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
    . { box-shadow: 0 20px 60px rgba(15, 23, 42, 0.14); }

    .btn-primary { background: var(--brand-blue); color: #fff; transition: 0.2s ease; }
    .btn-primary:hover { background: var(--brand-blue-dark); }
    .btn-green { background: var(--brand-green); color: #fff; transition: 0.2s ease; }
    .btn-green:hover { background: var(--brand-green-dark); }

    .ring-brand:focus { outline: none; border-color: var(--brand-blue) !important; box-shadow: 0 0 0 3px rgba(116, 178, 212, 0.12); }
    
    .modal-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 16px; }

    .spinner { width: 84px; height: 84px; border-radius: 999px; border: 6px solid var(--slate-100); border-top-color: var(--brand-green); animation: spin 1s linear infinite; position: relative; }
    .spinner-core { position: absolute; inset: 50%; width: 30px; height: 30px; transform: translate(-50%, -50%); border-radius: 999px; background: rgba(116, 178, 212, 0.18); }
    @keyframes spin { to { transform: rotate(360deg); } }

    .fade-in { animation: fadeIn .3s ease forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Required for HTML2PDF */
    .pdf-page-break { page-break-after: always; }
    @media print { .print-hide { display: none !important; } }
  </style>
</head>
<body class="selection-brand">
  <div id="app" class="app-shell"></div>
  <div id="print-root" class="print-only"></div>

  <script>
    /* =========================================================
       STEP 1/4
       - constants
       - state
       - helpers
       - start/quiz/loading render
       - input glitch fix foundation
    ========================================================== */

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
      gebaeudeArt: 'Einfamilienhaus',
      wohneinheitenGesamt: '2',
      wohneinheitenBewohnt: '1',
      eigentuemerUnter40k: '0',
      einkommenUnter40k: '0',
      personenAnzahl: '3',
      heizungsArt: 'Erdgas',
      heizungsAlter: '> 20 Jahre',
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

      wpZusatzFoerderName: '',
      wpZusatzFoerderSumme: '',
      pvZusatzFoerderName: '',
      pvZusatzFoerderSumme: '',
      speicherZusatzFoerderName: '',
      speicherZusatzFoerderSumme: '',
      wallboxZusatzFoerderName: '',
      wallboxZusatzFoerderSumme: '',

      includeSolar: true,
      includeWp: true,
      evuPreis: '0.35',
      spritPreis: '1.80',
      heizPreis: ENERGIE['Erdgas'].preis.toString(),
      customWpKosten: '',
      customPvKosten: '',
      customSpeicherKosten: '',
      customWallboxKosten: '',
      customWpSize: '',
      customPvSize: '',
      customSpeicherSize: ''
    };

    const state = {
      stage: 'START', // START | QUIZ | LOADING | DASHBOARD
      quizStep: 0,
      isCalculating: false,
      loadingText: 'Analysiere Sonneneinstrahlung...',
      activeTab: 'FINANZEN',
      selectedYears: 20,
      isGeneratingPDF: false,
      showPdfPreview: false,
      showCalculationModal: false,

      formData: structuredClone(INITIAL_FORM_DATA),
      savedProjects: [],
      currentProjectId: null,
      showProjectModal: false,
      newProjectName: '',

      showContactModal: false,
      contactSuccess: false
    };

    const dom = {
      app: document.getElementById('app')
    };

    /* ---------------------------------------------------------
       INPUT GLITCH FIX
       ---------------------------------------------------------
       Root cause in many React->HTML conversions:
       full re-render while user is typing resets:
       - current value
       - caret position
       - IME composition
       This step avoids replacing the active input node on each keystroke.
    --------------------------------------------------------- */

    let activeInputMeta = {
      key: null,
      isComposing: false
    };

    function safeSyncInputValues(root = document) {
      const active = document.activeElement;
      const activeKey = active && active.dataset ? active.dataset.bind : null;

      root.querySelectorAll('[data-bind]').forEach(el => {
        const key = el.dataset.bind;
        if (!key) return;

        // Do not overwrite the currently focused field while typing.
        if (activeKey && key === activeKey && active === el) return;

        const value = getByPath(state, key);
        const normalized = value == null ? '' : String(value);

        if (el.type === 'checkbox') {
          el.checked = !!value;
        } else if (el.value !== normalized) {
          el.value = normalized;
        }
      });
    }

    function getByPath(obj, path) {
      return path.split('.').reduce((acc, part) => acc?.[part], obj);
    }

    function setByPath(obj, path, value) {
      const parts = path.split('.');
      const last = parts.pop();
      let ref = obj;
      for (const part of parts) {
        if (!(part in ref)) ref[part] = {};
        ref = ref[part];
      }
      ref[last] = value;
    }

    function updateData(key, value, options = {}) {
      setByPath(state, `formData.${key}`, value);

      if (!options.skipRender) {
        render();
      } else {
        safeSyncInputValues(dom.app);
      }
    }

    function setState(patch = {}, rerender = true) {
      Object.assign(state, patch);
      if (rerender) render();
    }

    /* ---------------------------------------------------------
       HELPERS
    --------------------------------------------------------- */

    function toNumber(v, fallback = 0) {
      const n = Number(v);
      return Number.isFinite(n) && n >= 0 ? n : fallback;
    }

    function clamp(n, min, max) {
      return Math.min(max, Math.max(min, n));
    }

    function getInterpolatedAtYear(chartData, year, key) {
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
    }

    function getPlzYield(plz) {
      if (!plz || plz.length < 1) return 1000;
      const r = plz.charAt(0);
      if (['0', '1', '2'].includes(r)) return 950;
      if (['3', '4', '5'].includes(r)) return 980;
      if (['6', '7'].includes(r)) return 1050;
      if (['8', '9'].includes(r)) return 1100;
      return 1000;
    }

    function escapeHtml(str = '') {
      return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }

    function renderDonut(percentage, colorHex, label, sublabel, val1Label, val1Text, val2Label, val2Text) {
        const dash = 97.38;
        const offset = dash - (dash * Math.min(100, Math.max(0, percentage)) / 100);
        
        return `
            <div class="bg-slate-50 p-4 rounded-2xl  flex flex-col items-center relative overflow-hidden">
            <div class="relative w-20 h-20 mb-3 flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                <circle cx="18" cy="18" r="15.5" fill="none" stroke="${colorHex}" stroke-width="3" stroke-dasharray="${dash}" stroke-dashoffset="${offset}" stroke-linecap="round"></circle>
                </svg>
                <span class="text-[12pt] font-black text-slate-400 absolute">${Math.round(percentage)}%</span>
            </div>
            <h4 class="text-[9pt] font-black uppercase text-slate-400 mb-0.5 text-center leading-tight">${label}</h4>
            <p class="text-[7pt] text-slate-500 mb-3 text-center">${sublabel}</p>
            <div class="w-full space-y-1.5 border-t border-slate-200 pt-3 text-[8pt]">
                <div class="flex justify-between"><span class="text-slate-500">${val1Label}:</span> <span class="font-bold text-[${colorHex}]">${val1Text}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">${val2Label}:</span> <span class="font-bold">${val2Text}</span></div>
            </div>
            </div>
        `;
        }

        // Missing icons used in Dashboard
        function iconCheckSquare(size = 16) { return `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>`; }
        function iconWind(size = 16) { return `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"></path><path d="M9.6 4.6A2 2 0 1 1 11 8H2"></path><path d="M12.6 19.4A2 2 0 1 0 14 16H2"></path></svg>`; }

    function iconSun(size = 24, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="4"></circle>
          <path d="M12 2v2"></path>
          <path d="M12 20v2"></path>
          <path d="m4.93 4.93 1.41 1.41"></path>
          <path d="m17.66 17.66 1.41 1.41"></path>
          <path d="M2 12h2"></path>
          <path d="M20 12h2"></path>
          <path d="m6.34 17.66-1.41 1.41"></path>
          <path d="m19.07 4.93-1.41 1.41"></path>
        </svg>
      `;
    }


    function iconPrinter(size = 16, cls = '') {
    return `<svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg>`;
    }


    function iconArrowRight(size = 20, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14"></path>
          <path d="m12 5 7 7-7 7"></path>
        </svg>
      `;
    }

    function iconChevronLeft(size = 18, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="m15 18-6-6 6-6"></path>
        </svg>
      `;
    }

    function iconMapPin(size = 16, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
      `;
    }

    function iconFolder(size = 18, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7l-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"></path>
        </svg>
      `;
    }

    function iconShield(size = 16, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 13c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V6l8-4 8 4z"></path>
          <path d="m9 12 2 2 4-4"></path>
        </svg>
      `;
    }

    function iconSparkles(size = 16, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9.9 2.1 8.5 6.5 4.1 7.9l4.4 1.4 1.4 4.4 1.4-4.4 4.4-1.4-4.4-1.4Z"></path>
          <path d="M19 11.5 18.1 14 15.5 15l2.6 1 1 2.5 1-2.5 2.4-1-2.4-1Z"></path>
        </svg>
      `;
    }

    function iconCalculator(size = 20, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
          <rect x="4" y="2" width="16" height="20" rx="2"></rect>
          <line x1="8" y1="6" x2="16" y2="6"></line>
          <line x1="8" y1="10" x2="8" y2="10"></line>
          <line x1="12" y1="10" x2="12" y2="10"></line>
          <line x1="16" y1="10" x2="16" y2="10"></line>
          <line x1="8" y1="14" x2="8" y2="14"></line>
          <line x1="12" y1="14" x2="12" y2="14"></line>
          <line x1="16" y1="14" x2="16" y2="14"></line>
          <line x1="8" y1="18" x2="8" y2="18"></line>
          <line x1="12" y1="18" x2="16" y2="18"></line>
        </svg>
      `;
    }

    function iconX(size = 18, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6 6 18"></path>
          <path d="m6 6 12 12"></path>
        </svg>
      `;
    }

    function iconCheckCircle(size = 18, cls = '') {
      return `
        <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <path d="m9 11 3 3L22 4"></path>
        </svg>
      `;
    }

    /* ---------------------------------------------------------
       PERSISTENCE
    --------------------------------------------------------- */

    function loadProjects() {
      const loaded = localStorage.getItem('solarAspektProjects');
      if (!loaded) return;

      try {
        state.savedProjects = JSON.parse(loaded);
      } catch (e) {
        console.error('Fehler beim Laden der Projekte', e);
        state.savedProjects = [];
      }
    }

    function persistProjects() {
      localStorage.setItem('solarAspektProjects', JSON.stringify(state.savedProjects));
    }

    function saveProject(saveAsNew = false) {
      const d = state.formData;
      const defaultName = d.strasse
        ? `${d.strasse} ${d.hausnummer}, ${d.ort}`.trim()
        : `Projekt ${new Date().toLocaleDateString('de-DE')}`;

      const projectName = (state.newProjectName || '').trim() || defaultName;
      let updatedList = [...state.savedProjects];

      if (state.currentProjectId && !saveAsNew) {
        updatedList = updatedList.map(p =>
          p.id === state.currentProjectId
            ? { ...p, name: projectName, data: structuredClone(state.formData), updatedAt: Date.now() }
            : p
        );
      } else {
        const newProject = {
          id: Date.now().toString(),
          name: projectName,
          data: structuredClone(state.formData),
          createdAt: Date.now(),
          updatedAt: Date.now()
        };
        updatedList.push(newProject);
        state.currentProjectId = newProject.id;
      }

      state.savedProjects = updatedList;
      persistProjects();
      state.newProjectName = '';
      state.showProjectModal = false;
      render();
      alert('Projekt erfolgreich gespeichert!');
    }

    function loadProjectById(id) {
      const project = state.savedProjects.find(p => p.id === id);
      if (!project) return;

      state.formData = structuredClone(project.data);
      state.currentProjectId = project.id;
      state.newProjectName = project.name;
      state.stage = 'DASHBOARD'; // step 4 will fully render it
      state.activeTab = 'FINANZEN';
      state.showProjectModal = false;

      render();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function deleteProjectById(id) {
      if (!window.confirm('Möchtest du dieses Projekt wirklich löschen?')) return;

      state.savedProjects = state.savedProjects.filter(p => p.id !== id);
      if (state.currentProjectId === id) state.currentProjectId = null;
      persistProjects();
      render();
    }

    function startNewProject() {
      state.formData = structuredClone(INITIAL_FORM_DATA);
      state.currentProjectId = null;
      state.newProjectName = '';
      state.showProjectModal = false;
      state.quizStep = 0;
      state.stage = 'QUIZ';
      render();
    }

    /* ---------------------------------------------------------
       LOCATION AUTOFILL
    --------------------------------------------------------- */

    let plzLookupAbort = null;

    async function autoFillOrtIfNeeded() {
      const plz = state.formData.standortPlz;
      if (!plz || plz.length !== 5) return;

      try {
        if (plzLookupAbort) plzLookupAbort.abort();
        plzLookupAbort = new AbortController();

        const res = await fetch(`https://api.zippopotam.us/de/${plz}`, {
          signal: plzLookupAbort.signal
        });

        if (!res.ok) return;
        const data = await res.json();

        if (data?.places?.length) {
          const ort = data.places[0]['place name'] || '';
          if (ort && ort !== state.formData.ort) {
            state.formData.ort = ort;
            safeSyncInputValues(dom.app);
            const regionTag = dom.app.querySelector('[data-region-tag]');
            if (regionTag) {
              regionTag.outerHTML = regionTagHtml();
            } else {
              render();
            }
          }
        }
      } catch (_) {}
    }

    /* ---------------------------------------------------------
       WORKFLOW
    --------------------------------------------------------- */

    function handleCalculate() {
      state.isCalculating = true;
      state.stage = 'LOADING';
      state.loadingText = 'Analysiere Sonneneinstrahlung...';
      render();
      window.scrollTo({ top: 0, behavior: 'smooth' });

      setTimeout(() => {
        state.loadingText = 'Prüfe maximale KfW-Fördermittel...';
        const node = dom.app.querySelector('[data-loading-text]');
        if (node) node.textContent = state.loadingText;
      }, 1200);

      setTimeout(() => {
        state.loadingText = 'Kalkuliere Amortisation und Ertrag...';
        const node = dom.app.querySelector('[data-loading-text]');
        if (node) node.textContent = state.loadingText;
      }, 2400);

      setTimeout(() => {
        state.isCalculating = false;
        state.stage = 'DASHBOARD';
        state.activeTab = 'FINANZEN';
        render();
      }, 3500);
    }

    /* ---------------------------------------------------------
       RENDER HELPERS
    --------------------------------------------------------- */

    function selectWrapper(innerHtml) {
      return `
        <div class="relative">
          ${innerHtml}
          <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">▾</div>
        </div>
      `;
    }

    function numberInputWrapper(unit, innerHtml) {
      return `
        <div class="relative flex items-center">
          ${innerHtml}
          <span class="pointer-events-none absolute right-4 text-slate-400 font-bold text-sm">${escapeHtml(unit)}</span>
        </div>
      `;
    }

    function regionTagHtml() {
      if (!state.formData.ort) return '';
      return `
        <div data-region-tag class="text-xs font-bold text-[#93c21c] flex items-center gap-1.5 bg-[#cfe09b]/20 inline-flex px-3 py-1.5 rounded-lg">
          ${iconMapPin(14)}
          Region erkannt: ${escapeHtml(state.formData.ort)}
        </div>
      `;
    }

    function formatMoney(n) {
    return Math.round(toNumber(n, 0)).toLocaleString('de-DE');
    }

    function formatDecimal(n, digits = 1) {
    return toNumber(n, 0).toFixed(digits).replace('.', ',');
    }

    function formatAmortisationValue(ergebnis) {
    if (!ergebnis || ergebnis.amortisationJahre == null || ergebnis.amortisationJahre > 40) {
        return '> 40 Jahre';
    }
    return `${formatDecimal(ergebnis.amortisationJahre, 1)} Jahre`;
    }

    function calculateResults() {
    const formData = state.formData;

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

    const kesselWirkungsgrad = 0.90;
    const nutzWaermeKWh = waermeBedarfKWhFossil * kesselWirkungsgrad;

    const jaz =
        formData.heizsystem === 'Fußbodenheizung' ? 4.5 :
        formData.heizsystem === 'Beides' ? 4.0 :
        3.5;

    const bedarfWP = (formData.includeWp && jaz > 0) ? (nutzWaermeKWh / jaz) : 0;
    const bedarfGesamtMorgen = bedarfHaus + bedarfAutoKWhMorgen + bedarfWP;

    const baseHeizlast =
        formData.gebaeudeArt === 'Einfamilienhaus'
        ? Math.max(5, Math.ceil(waermeBedarfKWhFossil / 2200))
        : Math.max(10, Math.ceil(waermeBedarfKWhFossil / 2000));

    const heizlast = formData.customWpSize !== ''
        ? toNumber(formData.customWpSize, baseHeizlast)
        : baseHeizlast;

    const autoKostenWP = formData.includeWp
        ? (
            formData.gebaeudeArt === 'Einfamilienhaus'
            ? (22000 + (heizlast * 900))
            : (30000 + (heizlast * 700))
        )
        : 0;

    const pvYieldFactor = getPlzYield(formData.standortPlz);

    const basePvKwp = formData.includeSolar
        ? Math.max(4, Math.ceil((bedarfGesamtMorgen * 1.3 / 1000) * 2) / 2)
        : 0;

    let pvKwp = formData.customPvSize !== ''
        ? toNumber(formData.customPvSize, basePvKwp)
        : basePvKwp;

    const baseSpeicherKwh = formData.includeSolar
        ? clamp(Math.round((pvKwp * 0.8) / 3) * 3, 3, 20)
        : 0;

    let speicherKwh = formData.customSpeicherSize !== ''
        ? toNumber(formData.customSpeicherSize, baseSpeicherKwh)
        : baseSpeicherKwh;

    const pvProduktion = pvKwp * pvYieldFactor;

    const autoPvCost = formData.includeSolar ? Math.round(pvKwp * 1250) : 0;
    const autoSpeicherCost = formData.includeSolar ? Math.round(speicherKwh * 600) : 0;
    const autoWallboxCost = activeAuto && formData.includeSolar ? 1200 : 0;

    const effKostenWP = formData.includeWp
        ? (formData.customWpKosten !== '' ? toNumber(formData.customWpKosten, autoKostenWP) : autoKostenWP)
        : 0;

    const effPvCost = formData.includeSolar
        ? (formData.customPvKosten !== '' ? toNumber(formData.customPvKosten, autoPvCost) : autoPvCost)
        : 0;

    const effSpeicherCost = formData.includeSolar
        ? (formData.customSpeicherKosten !== '' ? toNumber(formData.customSpeicherKosten, autoSpeicherCost) : autoSpeicherCost)
        : 0;

    const effWallboxCost = (activeAuto && formData.includeSolar)
        ? (formData.customWallboxKosten !== '' ? toNumber(formData.customWallboxKosten, autoWallboxCost) : autoWallboxCost)
        : 0;

    const isFossilKlimaEligible =
        ['Heizöl', 'Nachtspeicher'].includes(formData.heizungsArt) ||
        (formData.heizungsArt === 'Erdgas' && formData.heizungsAlter === '> 20 Jahre');

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
        const w_gesamt = Math.max(1, parseInt(formData.wohneinheitenGesamt, 10) || 2);
        let totalFoerderung = 0;
        const numBewohnt = clamp(parseInt(formData.wohneinheitenBewohnt, 10) || 0, 0, w_gesamt);

        const w_selbst_bonus = Math.min(toNumber(formData.eigentuemerUnter40k), numBewohnt);
        const w_selbst_normal = numBewohnt - w_selbst_bonus;

        const p_selbst_bonus = Math.min(BEG_PARAMS.basis + EFFIZIENZ_BONUS + aktKlimaBonus + BEG_PARAMS.einkommen, BEG_PARAMS.maxProzent) / 100;
        const p_selbst_normal = Math.min(BEG_PARAMS.basis + EFFIZIENZ_BONUS + aktKlimaBonus, BEG_PARAMS.maxProzent) / 100;
        const p_vermietet = Math.min(BEG_PARAMS.basis + EFFIZIENZ_BONUS, BEG_PARAMS.maxProzent) / 100;

        const costPerUnit = effKostenWP / w_gesamt;

        for (let i = 1; i <= w_gesamt; i++) {
            const einheitMaxKosten =
            i === 1 ? BEG_PARAMS.cMax.mfhBase :
            i <= 6 ? BEG_PARAMS.cMax.mfhTier2 :
            BEG_PARAMS.cMax.mfhTier3;

            const eligible = Math.min(einheitMaxKosten, costPerUnit);

            let einheitProzent = p_vermietet;
            if (i <= w_selbst_bonus) einheitProzent = p_selbst_bonus;
            else if (i <= w_selbst_bonus + w_selbst_normal) einheitProzent = p_selbst_normal;

            totalFoerderung += eligible * einheitProzent;
        }

        kfwMaxSum = Math.min(totalFoerderung, effKostenWP);
        foerderQuote = effKostenWP > 0 ? (kfwMaxSum / effKostenWP) * 100 : 0;
        }
    }

    const f_wp_zusatz = toNumber(formData.wpZusatzFoerderSumme, 0);
    const f_pv = toNumber(formData.pvZusatzFoerderSumme, 0);
    const f_speicher = toNumber(formData.speicherZusatzFoerderSumme, 0);
    const f_wallbox = toNumber(formData.wallboxZusatzFoerderSumme, 0);

    const investWpNetto = Math.max(0, effKostenWP - kfwMaxSum - f_wp_zusatz);
    const investPvNetto = Math.max(0, effPvCost - f_pv);
    const investSpeicherNetto = Math.max(0, effSpeicherCost - f_speicher);
    const investWallboxNetto = Math.max(0, effWallboxCost - f_wallbox);

    const gesamtInvestBrutto = effKostenWP + effPvCost + effSpeicherCost + effWallboxCost;
    const gesamtFoerderung = kfwMaxSum + f_wp_zusatz + f_pv + f_speicher + f_wallbox;
    const nettoInvestition = Math.max(0, gesamtInvestBrutto - gesamtFoerderung);

    let gutscheinSumme = 0;
    if (formData.includeWp) gutscheinSumme += 1500;
    if (formData.includeSolar) gutscheinSumme += 1000;
    if (formData.includeSolar && activeAuto) gutscheinSumme += 500;

    const wartungPV_pa = formData.includeSolar ? ((effPvCost + effSpeicherCost) * 0.01) : 0;
    const wartungWP_pa = formData.includeWp ? (250 + (effKostenWP * 0.015)) : 0;
    const wartungGesamt_pa = wartungPV_pa + wartungWP_pa;
    const wartungAlt_pa = 250;

    const saisonVerteilung = [
        { name: 'Winter', tage: 90, yD: 0.12, hD: 0.50, bD: 0.28 },
        { name: 'Frühling', tage: 92, yD: 0.28, hD: 0.25, bD: 0.24 },
        { name: 'Sommer', tage: 92, yD: 0.42, hD: 0.05, bD: 0.22 },
        { name: 'Herbst', tage: 91, yD: 0.18, hD: 0.20, bD: 0.26 }
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
        const dailyRestYield = dailyYield - dailyDirect;
        const dailyRestCons = dailyCons - dailyDirect;
        const charge = Math.min(dailyRestYield, speicherKwh * 0.95);
        const batteryUse = Math.min(dailyRestCons, charge * 0.95);
        const dailyTotalCovered = dailyDirect + batteryUse;

        sCovered = dailyTotalCovered * s.tage;

        const maxCovered = sYield * 0.98;
        if (sCovered > maxCovered) sCovered = maxCovered;
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

    let co2EmissionFossil =
        (bedarfHaus * CO2_STROMMIX) +
        (waermeBedarfKWhFossil * ENERGIE[formData.heizungsArt].co2);

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
        const degradation = Math.pow(0.995, i - 1);
        const currentPvProd = pvProduktion * degradation;

        let currentCovered = 0;
        let currentEinspeisung = 0;

        if (currentPvProd > 0 && bedarfGesamtMorgen > 0) {
        const evRatio = pvProduktion > 0 ? (totalCovered / pvProduktion) : 0;
        currentCovered = Math.min(bedarfGesamtMorgen, currentPvProd * evRatio);
        currentEinspeisung = Math.max(0, currentPvProd - currentCovered);
        }

        const currentRestbezug = Math.max(0, bedarfGesamtMorgen - currentCovered);

        const fStrom = Math.pow(1 + INFLATION.STROM, i - 1);
        const fFossil = Math.pow(1 + INFLATION.FOSSIL, i - 1);
        const fMobil = Math.pow(1 + INFLATION.MOBILITAET, i - 1);
        const fWartung = Math.pow(1 + INFLATION.WARTUNG, i - 1);

        const fossilJahr =
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

        if (i <= 30) opex30YearsPV += (wartungPV_pa * fWartung);

        let replacementJahr = 0;

        if (formData.includeSolar && speicherKwh > 0 && i === 15) {
        const ersatzPV = (pvKwp * 200) + (speicherKwh * 400);
        replacementJahr += ersatzPV;
        if (i <= 30) opex30YearsPV += ersatzPV;
        }

        if (formData.includeWp && i === 20) {
        replacementJahr += 12000;
        }

        cumFossil += fossilJahr;
        cumOpexSolar += solarOpexJahr;
        cumReplacement += replacementJahr;

        const totalLaufendNeu = cumOpexSolar + cumReplacement;
        const aktuelleErsparnis = cumFossil - totalLaufendNeu;
        const aktuelleBilanz = cumFossil - (nettoInvestition + totalLaufendNeu);

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

    const fossilKostenJahr1 = opexFossilReihe[1] || 0;
    const ersparnisJahr1 = ersparnisOpexReihe[1] || 0;
    const finanzAutarkie = fossilKostenJahr1 > 0
        ? Math.min(100, Math.max(0, (ersparnisJahr1 / fossilKostenJahr1) * 100))
        : 0;

    const roi = nettoInvestition > 0 ? (ersparnisJahr1 / nettoInvestition) * 100 : 0;

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
    }


    function renderHeader() {
      return `
        <header class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 print-hide">
          <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <button data-action="go-home" class="flex items-center gap-2"> 
              <span class="text-lg font-black tracking-tight text-slate-400">SA<span class="text-[#74b2d4]">DESK</span></span>
            </button>

            <div class="flex items-center gap-3">
              <div class="hidden md:flex items-center gap-1.5 text-[10px] font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                <span class="text-[#93c21c]">${iconShield(12)}</span>
                TÜV-geprüft & Sicher
              </div>

              <button data-action="toggle-projects" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-400 px-4 py-1.5 rounded-full text-sm font-bold transition-colors">
                <span>${iconFolder(16)}</span>
                <span class="hidden sm:inline">Projekte (${state.savedProjects.length})</span>
              </button>
            </div>
          </div>
        </header>
      `;
    }

    function renderProjectModal() {
      if (!state.showProjectModal) return '';

      const canSave = state.stage === 'DASHBOARD' || state.formData.standortPlz.length > 3;

      return `
        <div class="modal-backdrop fade-in print-hide">
          <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b border-slate-200 bg-slate-50">
              <h2 class="text-xl font-black text-slate-400 flex items-center gap-2">
                <span class="text-[#74b2d4]">${iconFolder(22)}</span>
                Meine Projekte
              </h2>
              <button data-action="close-projects" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
                ${iconX(20, 'text-slate-500')}
              </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
              ${canSave ? `
                <div class="bg-white p-5 rounded-2xl border border-[#74b2d4] mb-6">
                  <h3 class="text-sm font-black text-[#74b2d4] mb-3">Aktuellen Stand speichern</h3>
                  <div class="flex flex-col sm:flex-row gap-3">
                    <input
                      data-bind="newProjectName"
                      data-role="project-name"
                      type="text"
                      value="${escapeHtml(state.newProjectName)}"
                      placeholder="Name des Projekts (z.B. Adresse oder Kunde)"
                      class="flex-1 bg-slate-50  p-3 rounded-xl ring-brand font-bold text-sm"
                    />
                    <button data-action="save-project" class="bg-[#74b2d4] hover:bg-[#5c8fa8] text-white font-bold py-3 px-6 rounded-xl transition-colors shrink-0">
                      ${state.currentProjectId ? 'Überschreiben' : 'Speichern'}
                    </button>
                    ${state.currentProjectId ? `
                      <button data-action="save-project-new" class="bg-slate-200 hover:bg-slate-300 text-slate-400 font-bold py-3 px-6 rounded-xl transition-colors shrink-0">
                        Als Neu
                      </button>
                    ` : ''}
                  </div>
                </div>
              ` : ''}

              <h3 class="text-sm font-black text-slate-400 mb-3 uppercase tracking-widest pl-1">Gespeicherte Projekte</h3>

              ${
                state.savedProjects.length === 0
                  ? `<div class="text-center p-8 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 font-medium">Bisher keine Projekte gespeichert. Berechne ein Projekt und speichere es hier ab.</div>`
                  : `
                    <div class="space-y-3">
                      ${state.savedProjects.map(p => `
                        <div class="bg-white p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all ${state.currentProjectId === p.id ? 'border-[#93c21c] shadow-md ring-1 ring-[#93c21c]' : 'border-slate-200'}">
                          <div>
                            <h4 class="font-black text-slate-400 flex items-center gap-2">
                              ${escapeHtml(p.name)}
                              ${state.currentProjectId === p.id ? `<span class="text-[10px] bg-[#93c21c] text-white px-2 py-0.5 rounded-full uppercase tracking-wider">Aktiv</span>` : ''}
                            </h4>
                            <p class="text-xs text-slate-500 font-medium mt-1">
                              Erstellt: ${new Date(p.createdAt).toLocaleDateString('de-DE')}
                              • Letzte Änd.: ${new Date(p.updatedAt).toLocaleDateString('de-DE')}
                            </p>
                          </div>
                          <div class="flex gap-2 shrink-0">
                            <button data-action="load-project" data-id="${p.id}" class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-400 font-bold py-2 px-4 rounded-lg text-sm transition-colors text-center">Laden</button>
                            <button data-action="delete-project" data-id="${p.id}" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-lg transition-colors" title="Projekt löschen">✕</button>
                          </div>
                        </div>
                      `).join('')}
                    </div>
                  `
              }
            </div>

            <div class="p-4 border-t border-slate-200 bg-white flex justify-center">
              <button data-action="new-project" class="flex items-center gap-2 text-slate-500 hover:text-slate-400 font-bold text-sm transition-colors">
                Neues leeres Projekt starten
              </button>
            </div>
          </div>
        </div>
      `;
    }

    function renderStart() {
      return `
        <main class="max-w-3xl mx-auto px-4 py-16 md:py-24 text-center fade-in">
          <div class="inline-flex items-center gap-2 bg-[#cfe09b]/50 border border-[#93c21c]/30 rounded-full px-4 py-1.5 mb-6">
            <span class="text-[#93c21c]">${iconSparkles(14)}</span>
            <span class="text-[10px] font-black text-[#74b2d4] uppercase tracking-wider">Aktuell: Neue BEG Förderung integriert</span>
          </div>

          <h1 class="text-4xl md:text-6xl font-black text-slate-400 tracking-tight leading-tight mb-6">
            Schluss mit der Preisspirale.
            <span class="text-[#93c21c]">Dein Haus. Dein Strom.</span>
          </h1>

          <p class="text-slate-500 font-medium text-lg max-w-xl mx-auto mb-10">
            Finde in 60 Sekunden heraus, wie viel staatlichen Zuschuss du für eine Wärmepumpe erhältst und berechne deine lebenslange Unabhängigkeit inklusive Photovoltaik und Speicher.
          </p>

          <button data-action="start-quiz" class="btn-green text-white text-lg font-black py-5 px-10 rounded-2xl  flex items-center justify-center gap-3 mx-auto">
            Jetzt Wirtschaftlichkeit berechnen
            ${iconArrowRight(18)}
          </button>
        </main>
      `;
    }

    function renderQuizStep0() {
      const d = state.formData;

      return `
        <div class="fade-in text-left">
          <h2 class="text-2xl md:text-3xl font-black text-slate-400 mb-2">Dein Energie-Profil</h2>
          <p class="text-sm text-slate-500 mb-6">Lass uns mit den Basics starten. Die Postleitzahl benötigen wir für die exakte Solar-Ertragsprognose.</p>

          <div class="space-y-6">
            <div>
              <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Gebäudeart</label>
              ${
                selectWrapper(`
                  <select data-bind="formData.gebaeudeArt" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 appearance-none cursor-pointer">
                    <option value="Einfamilienhaus" ${d.gebaeudeArt === 'Einfamilienhaus' ? 'selected' : ''}>Einfamilienhaus</option>
                    <option value="Mehrfamilienhaus" ${d.gebaeudeArt === 'Mehrfamilienhaus' ? 'selected' : ''}>Mehrfamilienhaus</option>
                  </select>
                `)
              }
            </div>

            ${d.gebaeudeArt === 'Einfamilienhaus' ? `
              <div class="mt-6 p-5 bg-slate-100/50 rounded-2xl ">
                <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Zu versteuerndes Haushalts-Einkommen</label>
                ${
                  selectWrapper(`
                    <select data-bind="formData.einkommenUnter40k" class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl ring-brand font-bold text-slate-400 appearance-none cursor-pointer">
                      <option value="0" ${d.einkommenUnter40k === '0' ? 'selected' : ''}>Über 40.000 € / Jahr</option>
                      <option value="1" ${d.einkommenUnter40k === '1' ? 'selected' : ''}>Unter 40.000 € / Jahr</option>
                    </select>
                  `)
                }
              </div>
            ` : ''}

            ${d.gebaeudeArt === 'Mehrfamilienhaus' ? `
              <div class="space-y-6 mt-6 p-5 bg-slate-100/50 rounded-2xl ">
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Wohneinheiten gesamt</label>
                    ${
                      numberInputWrapper('WE', `
                        <input data-bind="formData.wohneinheitenGesamt" type="number" min="2" max="50" value="${escapeHtml(d.wohneinheitenGesamt)}" class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl ring-brand font-bold text-slate-400 pr-12" />
                      `)
                    }
                  </div>
                  <div>
                    <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Davon selbst bewohnt</label>
                    ${
                      numberInputWrapper('WE', `
                        <input data-bind="formData.wohneinheitenBewohnt" type="number" min="0" max="${escapeHtml(d.wohneinheitenGesamt)}" value="${escapeHtml(d.wohneinheitenBewohnt)}" class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl ring-brand font-bold text-slate-400 pr-12" />
                      `)
                    }
                  </div>
                </div>

                ${toNumber(d.wohneinheitenBewohnt) > 0 ? `
                  <div class="pt-4 border-t border-slate-200">
                    <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Wie viele der selbstbewohnten WE haben ein Haushalts-Einkommen &lt; 40.000 €?</label>
                    ${
                      numberInputWrapper('WE', `
                        <input data-bind="formData.eigentuemerUnter40k" type="number" min="0" max="${escapeHtml(d.wohneinheitenBewohnt)}" value="${escapeHtml(d.eigentuemerUnter40k)}" class="w-full bg-white border-2 border-slate-200 p-3 rounded-xl ring-brand font-bold text-slate-400 pr-12" />
                      `)
                    }
                  </div>
                ` : ''}
              </div>
            ` : ''}

            <div class="grid grid-cols-2 gap-6 mt-6">
              <div>
                <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Personen im Haus</label>
                ${
                  numberInputWrapper('Personen', `
                    <input data-bind="formData.personenAnzahl" type="number" min="1" max="50" value="${escapeHtml(d.personenAnzahl)}" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 pr-24" />
                  `)
                }
              </div>

              <div>
                <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Postleitzahl</label>
                <input data-bind="formData.standortPlz" type="text" inputmode="numeric" maxlength="5" value="${escapeHtml(d.standortPlz)}" placeholder="z.B. 10115" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400" />
              </div>
            </div>

            ${regionTagHtml()}
          </div>

          <button data-action="quiz-next" ${d.standortPlz.length < 5 ? 'disabled' : ''} class="mt-10 w-full btn-primary text-lg font-black py-5 rounded-2xl flex items-center justify-center gap-2 ${d.standortPlz.length < 5 ? 'opacity-50 cursor-not-allowed' : ''}">
            Weiter zur Heizung
            ${iconArrowRight(18)}
          </button>
        </div>
      `;
    }

    function renderQuizStep1() {
      const d = state.formData;

      return `
        <div class="fade-in text-left">
          <h2 class="text-2xl md:text-3xl font-black text-slate-400 mb-6">Wie heizt du aktuell?</h2>

          <div class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Heizungsart</label>
                ${
                  selectWrapper(`
                    <select data-bind="formData.heizungsArt" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 appearance-none cursor-pointer">
                      ${Object.keys(ENERGIE).map(k => `<option value="${escapeHtml(k)}" ${d.heizungsArt === k ? 'selected' : ''}>${escapeHtml(k)}</option>`).join('')}
                    </select>
                  `)
                }
              </div>

              <div>
                <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Alter der Anlage</label>
                ${
                  selectWrapper(`
                    <select data-bind="formData.heizungsAlter" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 appearance-none cursor-pointer">
                      <option value="< 20 Jahre" ${d.heizungsAlter === '< 20 Jahre' ? 'selected' : ''}>Unter 20 Jahre</option>
                      <option value="> 20 Jahre" ${d.heizungsAlter === '> 20 Jahre' ? 'selected' : ''}>Über 20 Jahre</option>
                    </select>
                  `)
                }
              </div>
            </div>

            <div>
              <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Wärmeübergabe</label>
              ${
                selectWrapper(`
                  <select data-bind="formData.heizsystem" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 appearance-none cursor-pointer">
                    <option value="Heizkörper" ${d.heizsystem === 'Heizkörper' ? 'selected' : ''}>Klassische Heizkörper</option>
                    <option value="Fußbodenheizung" ${d.heizsystem === 'Fußbodenheizung' ? 'selected' : ''}>Fußbodenheizung</option>
                    <option value="Beides" ${d.heizsystem === 'Beides' ? 'selected' : ''}>Gemischt (Beides)</option>
                  </select>
                `)
              }
            </div>

            <div>
              <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Vorheriger Jahres-Verbrauch (ca.)</label>
              ${
                numberInputWrapper(`${ENERGIE[d.heizungsArt].einheit} / Jahr`, `
                  <input data-bind="formData.heizVerbrauch" type="number" step="100" value="${escapeHtml(d.heizVerbrauch)}" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 pr-24" />
                `)
              }
            </div>
          </div>

          <button data-action="quiz-next" class="mt-10 w-full btn-primary text-lg font-black py-5 rounded-2xl flex items-center justify-center gap-2">
            Weiter zum Strom
            ${iconArrowRight(18)}
          </button>
        </div>
      `;
    }

    function renderQuizStep2() {
      const d = state.formData;

      return `
        <div class="fade-in text-left">
          <h2 class="text-2xl md:text-3xl font-black text-slate-400 mb-6">Strom & Mobilität</h2>

          <div class="space-y-6">
            <div>
              <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Allgemeiner Stromverbrauch (Haus)</label>
              ${
                numberInputWrapper('kWh / Jahr', `
                  <input data-bind="formData.stromverbrauch" type="number" step="100" value="${escapeHtml(d.stromverbrauch)}" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 pr-28" />
                `)
              }
            </div>

            <div>
              <label class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wide">Geplante E-Auto Fahrleistung (0 = Keins)</label>
              ${
                numberInputWrapper('km / Jahr', `
                  <input data-bind="formData.kmProJahr" type="number" step="500" value="${escapeHtml(d.kmProJahr)}" class="w-full bg-slate-50 border-2 border-slate-100 p-4 rounded-xl ring-brand font-bold text-slate-400 pr-28" />
                `)
              }
            </div>
          </div>

          <button data-action="calculate" class="mt-10 w-full btn-green text-white font-black py-5 rounded-2xl  flex items-center justify-center gap-2 text-lg">
            Wirtschaftlichkeit berechnen
            ${iconCalculator(20)}
          </button>
        </div>
      `;
    }

    function renderQuiz() {
      const progress = ((state.quizStep + 1) / 3) * 100;

      let stepHtml = '';
      if (state.quizStep === 0) stepHtml = renderQuizStep0();
      if (state.quizStep === 1) stepHtml = renderQuizStep1();
      if (state.quizStep === 2) stepHtml = renderQuizStep2();

      return `
        <main class="max-w-2xl mx-auto px-4 py-12 text-center">
          <div class="w-full bg-slate-100 h-2 rounded-full mb-8 overflow-hidden">
            <div class="bg-[#93c21c] h-full transition-all duration-500" style="width:${progress}%"></div>
          </div>

          <div class="bg-white rounded-3xl p-8 sm:p-12  border border-slate-100">
            ${stepHtml}

            ${state.quizStep > 0 ? `
              <button data-action="quiz-prev" class="mt-8 text-sm font-bold text-slate-400 flex items-center gap-1 mx-auto hover:text-slate-400 transition-colors">
                ${iconChevronLeft(16)}
                Zurück
              </button>
            ` : ''}
          </div>
        </main>
      `;
    }

    function renderLoading() {
      return `
        <main class="max-w-lg mx-auto px-4 py-32 text-center flex flex-col items-center justify-center">
          <div class="spinner mb-8">
            <div class="spinner-core"></div>
          </div>

          <div class="mb-4 text-[#74b2d4]">${iconSun(34)}</div>

          <h2 class="text-2xl font-black text-slate-400 mb-2">Erstelle individuellen Fahrplan...</h2>
          <p data-loading-text class="text-slate-500 font-bold">${escapeHtml(state.loadingText)}</p>
        </main>
      `;
    }


function renderFinanceTab(ergebnis) {
  const showWp = !!state.formData.includeWp;
  const showSolar = !!state.formData.includeSolar;
  const showWallbox = !!(showSolar && ergebnis.activeAuto);

  return `
    <div class="space-y-10">

      <div>
        <h3 class="text-2xl font-black text-slate-400 mb-6">Investitions-Details</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

          ${showWp ? `
            <div class="bg-white p-6 rounded-3xl  shadow-soft relative overflow-hidden hover:border-[#74b2d4] transition-colors">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#74b2d4]"></div>

              <div class="flex justify-between items-start mb-6">
                <div>
                  <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Wärmepumpe</div>
                  <h4 class="text-xl font-black text-slate-400">System</h4>
                </div>
                <div class="text-right">
                  <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Leistung</div>
                  <div class="flex items-center justify-end gap-1.5">
                    <input
                      data-bind="customWpSize"
                      type="number"
                      value="${escapeHtml(String(state.formData.customWpSize || ergebnis.baseWpGroesse))}"
                      class="w-16 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-400 outline-none"
                    >
                    <span class="font-bold text-slate-400">kW</span>
                  </div>
                </div>
              </div>

              <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                  <span class="font-bold text-slate-500">Investition</span>
                  <div class="flex items-center gap-1.5">
                    <input
                      data-bind="customWpKosten"
                      type="number"
                      value="${escapeHtml(String(state.formData.customWpKosten || ergebnis.autoKostenWP))}"
                      class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-400 outline-none"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="flex justify-between items-center bg-[#cfe09b]/20 p-2 rounded-xl text-[#93c21c]">
                  <span class="font-bold">KfW (${formatDecimal(ergebnis.foerderQuote, 0)}%)</span>
                  <span class="font-black">- ${formatMoney(ergebnis.kfwZuschussMax)} €</span>
                </div>

                <div class="flex justify-between items-center text-[#93c21c]">
                  <input
                    data-bind="wpZusatzFoerderName"
                    type="text"
                    placeholder="Zusatzförderung"
                    value="${escapeHtml(state.formData.wpZusatzFoerderName)}"
                    class="w-28 bg-transparent border-b border-dashed border-slate-300 font-bold text-xs outline-none placeholder:text-slate-400 text-slate-400"
                  >
                  <div class="flex items-center gap-1.5">
                    <span class="font-bold">-</span>
                    <input
                      data-bind="wpZusatzFoerderSumme"
                      type="number"
                      placeholder="0"
                      value="${escapeHtml(String(state.formData.wpZusatzFoerderSumme))}"
                      class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none text-slate-400"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#74b2d4] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#74b2d4]">${formatMoney(ergebnis.investWpNetto)} €</span>
                </div>
              </div>
            </div>
          ` : ''}

          ${showSolar ? `
            <div class="bg-white p-6 rounded-3xl  shadow-soft relative overflow-hidden hover:border-[#93c21c] transition-colors">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#93c21c]"></div>

              <div class="flex justify-between items-start mb-6">
                <div>
                  <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Photovoltaik</div>
                  <h4 class="text-xl font-black text-slate-400">System</h4>
                </div>
                <div class="text-right">
                  <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Größe</div>
                  <div class="flex items-center justify-end gap-1.5">
                    <input
                      data-bind="customPvSize"
                      type="number"
                      value="${escapeHtml(String(state.formData.customPvSize || ergebnis.basePvGroesse))}"
                      class="w-16 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-400 outline-none"
                    >
                    <span class="font-bold text-slate-400">kWp</span>
                  </div>
                </div>
              </div>

              <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                  <span class="font-bold text-slate-500">Investition</span>
                  <div class="flex items-center gap-1.5">
                    <input
                      data-bind="customPvKosten"
                      type="number"
                      value="${escapeHtml(String(state.formData.customPvKosten || ergebnis.autoPvCost))}"
                      class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-400 outline-none"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="flex justify-between items-center text-[#93c21c]">
                  <input
                    data-bind="pvZusatzFoerderName"
                    type="text"
                    placeholder="Zusatzförderung"
                    value="${escapeHtml(state.formData.pvZusatzFoerderName)}"
                    class="w-28 bg-transparent border-b border-dashed border-slate-300 font-bold text-xs outline-none placeholder:text-slate-400 text-slate-400"
                  >
                  <div class="flex items-center gap-1.5">
                    <span class="font-bold">-</span>
                    <input
                      data-bind="pvZusatzFoerderSumme"
                      type="number"
                      placeholder="0"
                      value="${escapeHtml(String(state.formData.pvZusatzFoerderSumme))}"
                      class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none text-slate-400"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#93c21c] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#93c21c]">${formatMoney(ergebnis.investPvNetto)} €</span>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-3xl  shadow-soft relative overflow-hidden hover:border-[#93c21c] transition-colors">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#93c21c]"></div>

              <div class="flex justify-between items-start mb-6">
                <div>
                  <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Speicher</div>
                  <h4 class="text-xl font-black text-slate-400">System</h4>
                </div>
                <div class="text-right">
                  <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Kapazität</div>
                  <div class="flex items-center justify-end gap-1.5">
                    <input
                      data-bind="customSpeicherSize"
                      type="number"
                      value="${escapeHtml(String(state.formData.customSpeicherSize || ergebnis.baseSpeicherGroesse))}"
                      class="w-16 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-400 outline-none"
                    >
                    <span class="font-bold text-slate-400">kWh</span>
                  </div>
                </div>
              </div>

              <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                  <span class="font-bold text-slate-500">Investition</span>
                  <div class="flex items-center gap-1.5">
                    <input
                      data-bind="customSpeicherKosten"
                      type="number"
                      value="${escapeHtml(String(state.formData.customSpeicherKosten || ergebnis.autoSpeicherCost))}"
                      class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-400 outline-none"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="flex justify-between items-center text-[#93c21c]">
                  <input
                    data-bind="speicherZusatzFoerderName"
                    type="text"
                    placeholder="Zusatzförderung"
                    value="${escapeHtml(state.formData.speicherZusatzFoerderName)}"
                    class="w-28 bg-transparent border-b border-dashed border-slate-300 font-bold text-xs outline-none placeholder:text-slate-400 text-slate-400"
                  >
                  <div class="flex items-center gap-1.5">
                    <span class="font-bold">-</span>
                    <input
                      data-bind="speicherZusatzFoerderSumme"
                      type="number"
                      placeholder="0"
                      value="${escapeHtml(String(state.formData.speicherZusatzFoerderSumme))}"
                      class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none text-slate-400"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#93c21c] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#93c21c]">${formatMoney(ergebnis.investSpeicherNetto)} €</span>
                </div>
              </div>
            </div>
          ` : ''}

          ${showWallbox ? `
            <div class="bg-white p-6 rounded-3xl  shadow-soft relative overflow-hidden hover:border-[#74b2d4] transition-colors">
              <div class="absolute top-0 left-0 w-full h-1.5 bg-[#74b2d4]"></div>

              <div class="mb-6">
                <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Wallbox</div>
                <h4 class="text-xl font-black text-slate-400">E-Mobilität</h4>
              </div>

              <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                  <span class="font-bold text-slate-500">Investition</span>
                  <div class="flex items-center gap-1.5">
                    <input
                      data-bind="customWallboxKosten"
                      type="number"
                      value="${escapeHtml(String(state.formData.customWallboxKosten || ergebnis.autoWallboxCost))}"
                      class="w-24 text-right bg-transparent border-b border-dashed border-slate-300 font-black text-slate-400 outline-none"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="flex justify-between items-center text-[#93c21c]">
                  <input
                    data-bind="wallboxZusatzFoerderName"
                    type="text"
                    placeholder="Zusatzförderung"
                    value="${escapeHtml(state.formData.wallboxZusatzFoerderName)}"
                    class="w-28 bg-transparent border-b border-dashed border-slate-300 font-bold text-xs outline-none placeholder:text-slate-400 text-slate-400"
                  >
                  <div class="flex items-center gap-1.5">
                    <span class="font-bold">-</span>
                    <input
                      data-bind="wallboxZusatzFoerderSumme"
                      type="number"
                      placeholder="0"
                      value="${escapeHtml(String(state.formData.wallboxZusatzFoerderSumme))}"
                      class="w-20 text-right bg-transparent border-b border-dashed border-slate-300 font-black outline-none text-slate-400"
                    >
                    <span class="font-black text-slate-400">€</span>
                  </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                  <span class="text-sm font-black text-[#74b2d4] uppercase tracking-widest">Endpreis</span>
                  <span class="text-2xl font-black text-[#74b2d4]">${formatMoney(ergebnis.investWallboxNetto)} €</span>
                </div>
              </div>
            </div>
          ` : ''}

        </div>
      </div>

      <div class="bg-white rounded-3xl  shadow-soft p-6 md:p-8">
        <h3 class="text-lg font-black text-slate-400 mb-5">Betriebskosten-Verlauf</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
          ${[1, 10, 20, 30].map(jahr => `
            <div class="bg-slate-50 p-5 rounded-2xl  hover:border-[#74b2d4] transition-colors">
              <div class="text-sm font-black text-slate-400 mb-3">${jahr} ${jahr === 1 ? 'Jahr' : 'Jahre'}</div>

              <div class="space-y-2 text-sm">
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">Vorher</span>
                  <span class="font-black line-through text-slate-500">${formatMoney(ergebnis.opexFossilReihe[jahr] || 0)} €</span>
                </div>

                <div class="flex justify-between items-center">
                  <span class="text-slate-500">Nachher</span>
                  <span class="font-black text-[#74b2d4]">${formatMoney(ergebnis.opexSolarReihe[jahr] || 0)} €</span>
                </div>

                <div class="flex justify-between items-center border-t border-slate-200 pt-2 mt-2">
                  <span class="text-slate-500">Ersparnis</span>
                  <span class="font-black text-[#93c21c]">+${formatMoney(ergebnis.ersparnisOpexReihe[jahr] || 0)} €</span>
                </div>
              </div>
            </div>
          `).join('')}
        </div>
      </div>

      <div class="mt-8 bg-slate-800 rounded-3xl p-8  flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
        <div class="absolute left-0 top-0 w-2 h-full bg-gradient-to-b from-[#93c21c] to-[#74b2d4]"></div>

        <div class="w-full md:w-auto md:pl-2">
          <h3 class="text-xl font-black text-white mb-2">Deine finale System-Investition</h3>
          <p class="text-sm text-slate-400">Nach Abzug aller staatlichen Fördermittel und Zuschüsse.</p>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8 bg-slate-700/50 p-6 rounded-2xl border border-slate-600 w-full md:w-auto">
          <div class="text-center md:text-left w-full md:w-auto">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Anlagenpreis</span>
            <span class="text-xl font-black text-slate-400">${formatMoney(ergebnis.gesamtInvestBrutto)} €</span>
          </div>

          <div class="hidden md:block w-px h-12 bg-slate-600"></div>

          <div class="text-center md:text-left w-full md:w-auto">
            <span class="text-xs font-bold text-[#93c21c] uppercase tracking-widest block mb-1">Förderungen Total</span>
            <span class="text-xl font-black text-[#93c21c]">- ${formatMoney(ergebnis.gesamtFoerderung)} €</span>
          </div>

          <div class="hidden md:block w-px h-12 bg-slate-600"></div>

          <div class="text-center md:text-left w-full md:w-auto">
            <span class="text-sm font-black text-[#74b2d4] uppercase tracking-widest block mb-1">Gesamtinvestition</span>
            <span class="text-4xl font-black text-white">${formatMoney(ergebnis.nettoInvestition)} €</span>
          </div>
        </div>
      </div>

    </div>
  `;
}

function renderAutarkyTab(ergebnis) {
  const d = state.formData;
  const showSolar = !!d.includeSolar;
  const showWp = !!d.includeWp;
  const activeAuto = !!ergebnis.activeAuto;

  const pctAutarkie = Math.round(toNumber(ergebnis.autarkieQuote, 0));
  const pctEigen = Math.round(toNumber(ergebnis.eigenverbrauchQuote, 0));
  const pctEinsp = Math.round(toNumber(ergebnis.einspeisungQuote, 0));
  const pctFin = Math.round(toNumber(ergebnis.finanzAutarkieQuote, 0));
  const pctRest = clamp(100 - pctAutarkie, 0, 100);

  const donutCirc = 97.38;
  const donutOffset = (pct) => (donutCirc - (donutCirc * clamp(pct, 0, 100) / 100));

  const seasonMeta = (name) => {
    if (name === 'Winter') return { icon: iconSnowflake(16, 'w-4 h-4 text-[#93c21c]'), color: 'text-[#93c21c]', stroke: 'stroke-[#93c21c]' };
    if (name === 'Frühling') return { icon: iconLeaf(16, 'w-4 h-4 text-[#93c21c]'), color: 'text-[#93c21c]', stroke: 'stroke-[#93c21c]' };
    if (name === 'Sommer') return { icon: iconSun(16, 'w-4 h-4 text-[#93c21c]'), color: 'text-[#93c21c]', stroke: 'stroke-[#93c21c]' };
    return { icon: iconWind(16, 'w-4 h-4 text-[#93c21c]'), color: 'text-[#93c21c]', stroke: 'stroke-[#93c21c]' };
  };

  return `
    <div class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-8">

      <!-- 1) Energiebedarf im Vergleich -->
      <div class="bg-white rounded-3xl p-8  ">
        <h3 class="text-lg font-black text-slate-400 mb-6 flex items-center gap-2">
          ${iconPower(18, 'w-5 h-5 text-[#93c21c]')}
          Dein Energiebedarf im Vergleich
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-6">
            <div>
              <p class="text-sm text-slate-500 mb-4">
                Deine alten fossilen Verbräuche (Heizung & Verbrenner) verursachen hohe laufende Kosten, die wir durch den Systemwechsel drastisch reduzieren.
              </p>

              <div class="bg-slate-50 p-4 rounded-xl ">
                <h4 class="text-sm font-black text-slate-400 mb-3 border-b pb-2">Kosten Vorher (pro Jahr)</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-slate-500">Hausstrom (${formatMoney(ergebnis.hausStrombedarf)} kWh)</span>
                    <span class="font-bold">${formatMoney(ergebnis.stromKostenHeute)} €</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-slate-500">Heizung (${escapeHtml(String(d.heizVerbrauch))} ${escapeHtml(ENERGIE[d.heizungsArt]?.einheit || 'kWh')})</span>
                    <span class="font-bold">${formatMoney(ergebnis.heizKostenHeute)} €</span>
                  </div>
                  ${activeAuto ? `
                    <div class="flex justify-between">
                      <span class="text-slate-500">Sprit (~7L/100km)</span>
                      <span class="font-bold">${formatMoney(ergebnis.kostenAutoHeute)} €</span>
                    </div>
                  ` : ''}
                  <div class="flex justify-between">
                    <span class="text-slate-500">Wartung & Reparatur (Alt)</span>
                    <span class="font-bold">${formatMoney(ergebnis.wartungAlt_pa)} €</span>
                  </div>
                  <div class="flex justify-between font-black text-slate-400 border-t border-slate-200 pt-2 mt-2">
                    <span>Gesamtkosten vorher:</span>
                    <span>${formatMoney(ergebnis.stromKostenHeute + ergebnis.heizKostenHeute + (activeAuto ? ergebnis.kostenAutoHeute : 0) + ergebnis.wartungAlt_pa)} € / Jahr</span>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <h4 class="text-sm font-black text-[#74b2d4] mb-3">Neuer Energiebedarf (Vernetzt)</h4>
              <div class="space-y-3">
                <div class="flex justify-between items-center bg-slate-50 p-3 rounded-lg">
                  <span class="text-xs font-bold text-slate-500">Hausstrom (Bestand)</span>
                  <span class="font-black text-slate-400">${formatMoney(ergebnis.hausStrombedarf)} kWh</span>
                </div>

                ${showWp ? `
                  <div class="flex justify-between items-center bg-[#e3effb]/50 p-3 rounded-lg">
                    <span class="text-xs font-bold text-[#74b2d4]">Neuer Wärmepumpen-Strom</span>
                    <span class="font-black text-[#74b2d4]">${formatMoney(ergebnis.wpStrombedarf)} kWh</span>
                  </div>
                ` : ''}

                ${activeAuto ? `
                  <div class="flex justify-between items-center bg-[#e3effb]/50 p-3 rounded-lg">
                    <span class="text-xs font-bold text-[#74b2d4]">Neuer E-Auto Strom</span>
                    <span class="font-black text-[#74b2d4]">${formatMoney(ergebnis.autoStrombedarf)} kWh</span>
                  </div>
                ` : ''}

                <div class="flex justify-between items-center border-t-2 border-slate-200 pt-2">
                  <span class="text-sm font-black text-slate-400">Gesamtstrombedarf Neu</span>
                  <span class="font-black text-lg text-slate-400">${formatMoney(ergebnis.gesamtbedarf)} kWh</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 2) Unabhängigkeit & Donuts -->
          <div class="flex flex-col gap-4">
            <div class="bg-[#cfe09b]/20 p-5 rounded-2xl">
              <h4 class="font-black text-[#93c21c] mb-2 flex items-center gap-2">
                <div class="relative group inline-flex items-center gap-1 cursor-help">
                  <span class="cursor-help flex items-center gap-1 border-b border-dashed border-[#93c21c]">
                    ${iconCheckSquare(16, 'w-4 h-4')}
                    Unabhängigkeit & Netz
                    ${iconInfo(12, 'w-3 h-3')}
                  </span>
                  <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-56 p-3 bg-slate-800 text-white text-xs rounded-xl shadow-2xl z-50 text-left leading-relaxed font-normal">
                    Der Anteil deines gesamten jährlichen Strombedarfs, den deine Photovoltaikanlage abdeckt.
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                  </div>
                </div>
              </h4>

              <div class="space-y-2 text-sm text-slate-400">
                <div class="flex justify-between items-center border-b border-[#cfe09b]/50 pb-1">
                  <span>Energetische Unabhängigkeit:</span>
                  <span class="font-black text-[#93c21c] text-lg">${pctAutarkie} %</span>
                </div>
                <div class="flex justify-between items-center pb-1">
                  <span>Verbleibende Netz-Abhängigkeit:</span>
                  <span class="font-bold text-slate-500">${pctRest} %</span>
                </div>

                ${showSolar ? `
                  <div class="flex justify-between items-center text-xs text-slate-500 pt-2">
                    <span>Eigenverbrauch Solarstrom:</span>
                    <span>${pctEigen} %</span>
                  </div>
                  <div class="flex justify-between items-center text-xs text-slate-500">
                    <span>Einspeisung ins Netz:</span>
                    <span>${pctEinsp} %</span>
                  </div>
                ` : `
                  <div class="text-xs text-slate-500 pt-2">
                    PV ist deaktiviert – Autarkie/Eigenverbrauch beziehen sich auf das aktive Setup.
                  </div>
                `}
              </div>
            </div>

            ${showSolar ? `
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <!-- Donut 1 -->
                <div class="bg-slate-50 p-5 rounded-2xl  flex flex-col items-center relative overflow-hidden hover:border-[#93c21c] transition-colors">
                  <div class="relative w-20 h-20 mb-4 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c]" stroke-width="3" stroke-dasharray="${donutCirc}" stroke-dashoffset="${donutOffset(pctAutarkie)}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-xl font-black text-slate-400 absolute">${pctAutarkie}%</span>
                  </div>
                  <h4 class="text-xs font-black uppercase text-slate-400 mb-1 text-center">Autarkiegrad</h4>
                  <p class="text-[10px] text-slate-500 mb-3 text-center">Energetische Unabhängigkeit</p>
                  <div class="w-full space-y-2 border-t border-slate-200 pt-3 mt-auto text-xs">
                    <div class="flex justify-between"><span class="text-slate-500 font-bold">Deckung:</span> <span class="font-black text-[#93c21c]">${formatMoney(ergebnis.genutzterPvStrom)} kWh</span></div>
                    <div class="flex justify-between"><span class="text-slate-500 font-bold">Netzbezug:</span> <span class="font-black text-slate-400">${formatMoney(ergebnis.restStromBezug)} kWh</span></div>
                  </div>
                </div>

                <!-- Donut 2 -->
                <div class="bg-slate-50 p-5 rounded-2xl  flex flex-col items-center relative overflow-hidden hover:border-[#74b2d4] transition-colors">
                  <div class="relative w-20 h-20 mb-4 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#74b2d4]" stroke-width="3" stroke-dasharray="${donutCirc}" stroke-dashoffset="${donutOffset(pctEigen)}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-xl font-black text-slate-400 absolute">${pctEigen}%</span>
                  </div>
                  <h4 class="text-xs font-black uppercase text-slate-400 mb-1 text-center">Eigenverbrauchsquote</h4>
                  <p class="text-[10px] text-slate-500 mb-3 text-center">Nutzung des PV-Stroms</p>
                  <div class="w-full space-y-2 border-t border-slate-200 pt-3 mt-auto text-xs">
                    <div class="flex justify-between"><span class="text-slate-500 font-bold">Genutzt:</span> <span class="font-black text-[#74b2d4]">${formatMoney(ergebnis.genutzterPvStrom)} kWh</span></div>
                    <div class="flex justify-between"><span class="text-slate-500 font-bold">Einspeisung:</span> <span class="font-black text-slate-400">${formatMoney(ergebnis.netzEinspeisung)} kWh</span></div>
                  </div>
                </div>

                <!-- Donut 3 -->
                <div class="bg-slate-50 p-5 rounded-2xl  flex flex-col items-center relative overflow-hidden hover:border-[#93c21c] transition-colors">
                  <div class="relative w-20 h-20 mb-4 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c]" stroke-width="3" stroke-dasharray="${donutCirc}" stroke-dashoffset="${donutOffset(pctFin)}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-xl font-black text-slate-400 absolute">${pctFin}%</span>
                  </div>
                  <h4 class="text-xs font-black uppercase text-slate-400 mb-1 text-center leading-tight">Finanzielle<br/>Unabhängigkeit</h4>
                  <div class="w-full space-y-2 border-t border-slate-200 pt-3 mt-auto text-xs">
                    <div class="flex justify-between"><span class="text-slate-500 font-bold">Vorher:</span> <span class="font-black text-slate-400 line-through">${formatMoney(ergebnis.fossilKostenJahr1)} €</span></div>
                    <div class="flex justify-between"><span class="text-slate-500 font-bold">Ersparnis:</span> <span class="font-black text-[#93c21c]">+${formatMoney(ergebnis.ersparnisJahr1)} €</span></div>
                  </div>
                </div>

              </div>

              <p class="text-[10px] text-slate-500 leading-relaxed px-2 mt-4">
                <strong>Warum empfehlen wir genau ${escapeHtml(ergebnis.pvGroesse)} kWp PV und ${escapeHtml(ergebnis.speicherGroesse)} kWh Speicher?</strong><br/>
                Da dein zukünftiger Strombedarf primär in der Übergangszeit und im Winter anfällt, muss die Photovoltaik so groß dimensioniert sein, dass sie auch bei schwächerer Sonne ausreichend Strom liefert.
              </p>
            ` : ''}

          </div>
        </div>
      </div>

      <!-- 3) Saisonale Autarkie -->
      <div class="bg-white rounded-3xl p-8  ">
        <h3 class="text-lg font-black text-slate-400 mb-6 flex items-center gap-2">
          ${iconWind(18, 'w-5 h-5 text-[#74b2d4]')}
          Saisonale Autarkie
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          ${ergebnis.saisonDaten.map((s) => {
            const meta = seasonMeta(s.name);
            const dashOffset = donutOffset(s.autarkie);

            return `
              <div class="bg-slate-50 p-5 rounded-2xl  flex flex-col relative overflow-hidden hover:border-[#74b2d4] transition-colors">
                <div class="flex items-center gap-2 mb-4">
                  ${meta.icon}
                  <span class="text-[10pt] font-black uppercase tracking-wider ${meta.color}">${escapeHtml(s.name)}</span>
                </div>

                <div class="flex items-center gap-4 mb-4">
                  <div class="relative w-16 h-16 flex items-center justify-center shrink-0">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="${meta.stroke}" stroke-width="3" stroke-dasharray="${donutCirc}" stroke-dashoffset="${dashOffset}" stroke-linecap="round"></circle>
                    </svg>
                    <span class="text-[11pt] font-black text-slate-400 absolute">${formatMoney(s.autarkie)}%</span>
                  </div>
                  <div><p class="text-[7pt] text-slate-500 font-bold uppercase tracking-widest">Deckung</p></div>
                </div>

                <div class="space-y-1 border-t border-slate-200 pt-3 text-[8pt]">
                  <div class="flex justify-between text-slate-500"><span>Bedarf:</span> <span class="font-bold">${formatMoney(s.verbrauch)} kWh</span></div>
                  <div class="flex justify-between text-[#93c21c]"><span>PV/Akku:</span> <span class="font-bold">${formatMoney(s.covered)} kWh (${formatMoney(s.pctCovered)}%)</span></div>
                  <div class="flex justify-between text-slate-500"><span>Zukauf:</span> <span class="font-bold">${formatMoney(s.restbezug)} kWh (${formatMoney(s.pctRestbezug)}%)</span></div>
                  <div class="flex justify-between text-[#74b2d4] mt-1 border-t border-slate-100 pt-1"><span>Einspeisung:</span> <span class="font-bold">${formatMoney(s.einspeisung)} kWh (${formatMoney(s.pctEinspeisung)}%*)</span></div>
                </div>
              </div>
            `;
          }).join('')}
        </div>

        <p class="text-[8pt] text-slate-400 mt-4 text-center">
          * Der Prozentwert der Einspeisung bezieht sich auf den gesamten PV-Ertrag der jeweiligen Jahreszeit.
        </p>
      </div>

      <!-- 4) Heizbedarf vs. Solarertrag (nur wenn WP + PV aktiv) -->
      ${(showWp && showSolar) ? `
        <div class="bg-white rounded-3xl p-8  ">
          <h3 class="text-lg font-black text-slate-400 mb-2 flex items-center gap-2">
            ${iconPieChart(20, 'w-6 h-6 text-[#93c21c]')}
            Heizbedarf vs. Solarertrag im Jahresverlauf
          </h3>

          <p class="text-sm text-slate-400 mb-8">
            <strong>Wie viel Energie brauche ich für die Wärmepumpe und wie viel erzeuge ich selbst?</strong><br/>
            Der äußere Ring (Grün) zeigt den Anteil des PV-Ertrags pro Jahreszeit. Der innere Ring (Blau) zeigt den Anteil des Heizbedarfs der Wärmepumpe.
          </p>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative mt-6">
            ${ergebnis.saisonDaten.map((s) => {
              const pctErtrag = ergebnis.pvProduktion > 0 ? Math.round((s.ertrag / ergebnis.pvProduktion) * 100) : 0;
              const pctHeiz = ergebnis.wpStrombedarf > 0 ? Math.round((s.heizbedarf / ergebnis.wpStrombedarf) * 100) : 0;

              const outerDash = 97.38;
              const innerDash = 72.25;

              const outerOffset = outerDash - (outerDash * clamp(pctErtrag, 0, 100) / 100);
              const innerOffset = innerDash - (innerDash * clamp(pctHeiz, 0, 100) / 100);

              return `
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl flex flex-col items-center gap-4 relative group hover:border-[#cfe09b] transition-colors shadow-soft">
                  <span class="font-black text-sm text-slate-400 uppercase tracking-widest">${escapeHtml(s.name)}</span>

                  <div class="relative w-28 h-28 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c] transition-all duration-1000 ease-out" stroke-width="3" stroke-dasharray="${outerDash}" stroke-dashoffset="${outerOffset}" stroke-linecap="round"></circle>

                      <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
                      <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#74b2d4] transition-all duration-1000 ease-out" stroke-width="3" stroke-dasharray="${innerDash}" stroke-dashoffset="${innerOffset}" stroke-linecap="round"></circle>
                    </svg>
                  </div>

                  <div class="w-full space-y-1.5 border-t border-slate-200 pt-3 text-[11px] md:text-xs">
                    <div class="flex justify-between items-center">
                      <span class="text-slate-500 font-bold">Ertrag:</span>
                      <span class="font-black text-[#93c21c]">${formatMoney(s.ertrag)} kWh (${pctErtrag}%)</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-slate-500 font-bold">Heizbedarf:</span>
                      <span class="font-black text-[#74b2d4]">${formatMoney(s.heizbedarf)} kWh (${pctHeiz}%)</span>
                    </div>
                  </div>
                </div>
              `;
            }).join('')}
          </div>

          <div class="flex justify-center flex-wrap gap-4 md:gap-8 mt-8 pt-6 border-t border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-4 h-4 border-4 border-[#93c21c] rounded-full"></div>
              <span class="text-xs md:text-sm font-bold text-slate-400">Äußerer Ring: Solarertrag</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-4 h-4 border-4 border-[#74b2d4] rounded-full"></div>
              <span class="text-xs md:text-sm font-bold text-slate-400">Innerer Ring: WP-Heizbedarf</span>
            </div>
          </div>
        </div>
      ` : ''}

      <!-- 5) Klimaschutz -->
      <div class="bg-[#cfe09b]/20 rounded-3xl p-8 ">
        <h3 class="text-lg font-black text-slate-400 mb-4 flex items-center gap-2">
          ${iconLeaf(20, 'w-6 h-6 text-[#93c21c]')}
          Dein aktiver Klimaschutz
        </h3>

        <p class="text-sm text-slate-400 mb-6">
          Durch deinen Systemwechsel sparst du massiv CO₂ ein. Hier ist deine persönliche Umweltbilanz, hochgerechnet auf die Lebensdauer deines Systems:
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white p-4 rounded-2xl text-center shadow-soft hover:scale-105 transition-transform">
            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Pro Jahr</span>
            <span class="block text-2xl font-black text-[#93c21c]">${formatDecimal(ergebnis.co2ErsparnisPerYear, 1)} t</span>
          </div>
          <div class="bg-white p-4 rounded-2xl text-center shadow-soft hover:scale-105 transition-transform">
            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">10 Jahre</span>
            <span class="block text-2xl font-black text-[#93c21c]">${formatDecimal(ergebnis.co2ErsparnisPerYear * 10, 1)} t</span>
          </div>
          <div class="bg-white p-4 rounded-2xl text-center shadow-soft hover:scale-105 transition-transform">
            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">20 Jahre</span>
            <span class="block text-2xl font-black text-[#93c21c]">${formatDecimal(ergebnis.co2ErsparnisPerYear * 20, 1)} t</span>
          </div>
          <div class="bg-white p-4 rounded-2xl text-center shadow-soft hover:scale-105 transition-transform">
            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">30 Jahre</span>
            <span class="block text-2xl font-black text-[#93c21c]">${formatDecimal(ergebnis.co2ErsparnisPerYear * 30, 1)} t</span>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl flex flex-col sm:flex-row items-center gap-5 shadow-soft text-center sm:text-left">
          <div class="bg-[#cfe09b]/30 p-4 rounded-full shrink-0">
            ${iconLeaf(26, 'w-8 h-8 text-[#93c21c]')}
          </div>
          <p class="text-sm text-slate-400 leading-relaxed">
            Deine jährliche Einsparung entspricht der CO₂-Speicherkraft von
            <strong class="text-[#93c21c]">${formatMoney(ergebnis.co2Baeume)} ausgewachsenen Bäumen</strong>
            oder einer Mischwaldfläche von
            <strong class="text-[#93c21c]">${formatMoney(ergebnis.co2FlaecheQm)} Quadratmetern</strong>
            pro Jahr.
          </p>
        </div>
      </div>

    </div>
  `;
}

/* ---------------------------------------------------------
   ICONS used by renderAutarkyTab
   (Add these if they don’t exist in your file yet)
--------------------------------------------------------- */
function iconInfo(size = 16, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"></circle>
      <path d="M12 16v-4"></path>
      <path d="M12 8h.01"></path>
    </svg>
  `;
}
function iconCheckSquare(size = 16, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 10.5V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12.5"></path>
      <path d="m9 11 3 3L22 4"></path>
    </svg>
  `;
}
function iconPower(size = 18, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 2v10"></path>
      <path d="M18.4 6.6a9 9 0 1 1-12.77.04"></path>
    </svg>
  `;
}
function iconWind(size = 18, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12.8 19.6A2 2 0 1 0 14 16H2"></path>
      <path d="M17.5 8a2.5 2.5 0 1 1 2 4H2"></path>
      <path d="M9.8 4.4A2 2 0 1 1 11 8H2"></path>
    </svg>
  `;
}
function iconLeaf(size = 18, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
      <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
      <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
    </svg>
  `;
}
function iconSnowflake(size = 18, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
      <path d="m10 20-1.25-2.5L6 18"></path>
      <path d="M10 4 8.75 6.5 6 6"></path>
      <path d="m14 20 1.25-2.5L18 18"></path>
      <path d="m14 4 1.25 2.5L18 6"></path>
      <path d="m17 21-3-6h-4"></path>
      <path d="m17 3-3 6 1.5 3"></path>
      <path d="M2 12h6.5L10 9"></path>
      <path d="m20 10-1.5 2 1.5 2"></path>
      <path d="M22 12h-6.5L14 15"></path>
      <path d="m4 10 1.5 2L4 14"></path>
      <path d="m7 21 3-6-1.5-3"></path>
      <path d="m7 3 3 6h4"></path>
    </svg>
  `;
}
function iconPieChart(size = 18, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z"></path>
      <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
    </svg>
  `;
}
function renderTechTab(ergebnis) {
  const d = state.formData;
  return `
    <div class="space-y-8 fade-in">
       <div class="bg-white rounded-3xl p-8  shadow-xl">
          <h3 class="text-xl font-black text-slate-400 mb-2 flex items-center gap-2">
             Premium-Hardware für Jahrzehnte
          </h3>
          <p class="text-sm leading-relaxed text-slate-400 mb-8">
             Ein Energie-Systemwechsel ist eine Investition, die über Generationen halten muss. Deshalb verbauen wir ausschließlich High-End-Komponenten, die nicht nur auf dem Datenblatt glänzen, sondern in der harten Praxis bestehen.
          </p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             ${d.includeWp ? `
             <div class="bg-slate-50 p-6 rounded-2xl  hover:border-[#74b2d4] transition-colors group">
                <h4 class="text-lg font-black text-slate-400 mb-3 text-[#74b2d4]">Die intelligente Wärmepumpe</h4>
                <ul class="text-sm text-slate-400 space-y-3 list-none">
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Flüsterleise:</strong> Mit &lt; 35 dB(A) leiser als ein moderner Kühlschrank. Weder du noch die Nachbarn werden sie hören.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Heizen & Kühlen:</strong> Aktive Klimafunktion im Sommer über die Fußbodenheizung inklusive.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Maximaler COP:</strong> Aus 1 kWh Strom werden 4-5 kWh Wärme gewonnen. Reine physikalische Naturkraft.</span></li>
                </ul>
             </div>
             ` : ''}

             ${d.includeSolar ? `
             <div class="bg-slate-50 p-6 rounded-2xl  hover:border-[#93c21c] transition-colors group">
                <h4 class="text-lg font-black text-slate-400 mb-3 text-[#93c21c]">Bifaziale Photovoltaik</h4>
                <ul class="text-sm text-slate-400 space-y-3 list-none">
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Glas-Glas-Technologie:</strong> Absolut sturmfest, keine Mikrorisse, höchste Brandschutzklasse.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#93c21c] shrink-0 mt-0.5')}<span><strong>30 Jahre Garantie:</strong> Lineare Leistungsgarantie für ein langes, sorgenfreies Anlagenleben.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Schwachlicht-Stark:</strong> Hervorragende Erträge auch bei bewölktem Himmel und in der Übergangszeit.</span></li>
                </ul>
             </div>
             ` : ''}

             ${d.includeSolar ? `
             <div class="bg-slate-50 p-6 rounded-2xl  hover:border-[#93c21c] transition-colors group">
                <h4 class="text-lg font-black text-slate-400 mb-3 text-[#93c21c]">LiFePO4 Batteriespeicher</h4>
                <ul class="text-sm text-slate-400 space-y-3 list-none">
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Maximale Sicherheit:</strong> Lithium-Eisenphosphat ist thermisch absolut stabil, brennt und explodiert nicht.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Zyklenfestigkeit:</strong> Entwickelt für 6.000+ Ladezyklen ohne nennenswerten Kapazitätsverlust.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#93c21c] shrink-0 mt-0.5')}<span><strong>Schwarzstartfähig:</strong> Bei Stromausfall versorgt das System dein Haus nahtlos weiter (optional).</span></li>
                </ul>
             </div>
             ` : ''}

             ${(d.includeSolar && ergebnis.activeAuto) ? `
             <div class="bg-slate-50 p-6 rounded-2xl  hover:border-[#74b2d4] transition-colors group">
                <h4 class="text-lg font-black text-slate-400 mb-3 text-[#74b2d4]">Smarte Premium-Wallbox</h4>
                <ul class="text-sm text-slate-400 space-y-3 list-none">
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>Überschussladen:</strong> Lade dein Auto 100% automatisch mit Sonnenstrom, der sonst ins Netz fließt.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>V2H / V2G Ready:</strong> Mach dein Auto zum größten Heimspeicher der Welt. Vorbereitet für bidirektionales Laden.</span></li>
                   <li class="flex gap-2 items-start">${iconCheckCircle(16, 'text-[#74b2d4] shrink-0 mt-0.5')}<span><strong>App & Abrechnung:</strong> Volle Kontrolle via App und einfache Dienstwagen-Abrechnung dank integriertem RFID-Chip.</span></li>
                </ul>
             </div>
             ` : ''}
          </div>
       </div>

       <div class="bg-white rounded-3xl p-8 text-white relative overflow-hidden">
          <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#93c21c] to-[#74b2d4]"></div>
          <h3 class="text-2xl font-black mb-8 flex items-center gap-3 text-[#93c21c]">
             Warum Solar Aspekt?
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
             <div class="flex gap-4">
                <div>
                   <h4 class="text-lg font-black mb-1  text-[#93c21c]">Regionale Meisterqualität</h4>
                   <p class="text-slate-400 text-sm leading-relaxed">Wir arbeiten nicht mit anonymen Subunternehmern. Eigene, zertifizierte Handwerker aus der Region montieren und warten deine Anlage für maximale Sicherheit.</p>
                </div>
             </div>
             <div class="flex gap-4">
                <div>
                   <h4 class="text-lg font-black mb-1  text-[#93c21c]">Förder-Garantie</h4>
                   <p class="text-slate-400 text-sm leading-relaxed">Wir übernehmen den kompletten Behörden-Dschungel. Von der KfW-Zusage über BAFA bis zur Netzanmeldung – du musst dich um nichts kümmern.</p>
                </div>
             </div>
             <div class="flex gap-4">
                <div>
                   <h4 class="text-lg font-black mb-1  text-[#93c21c]">Alles aus einer Hand</h4>
                   <p class="text-slate-400 text-sm leading-relaxed">Planung, Gerüstbau, Dachmontage, Elektrik und Heizungsbau. Ein fester Ansprechpartner, ein Projektleiter, null Schnittstellenprobleme.</p>
                </div>
             </div> 
          </div>
       </div>
    </div>
  `;
}
function renderContactModal(ergebnis) {
  if (!state.showContactModal) return '';

  return `
    <div class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl  max-w-xl w-full overflow-hidden relative">
        <button data-action="close-contact" class="absolute top-4 right-4 p-2 bg-slate-100 hover:bg-slate-200 rounded-full transition-colors z-10">
          ${iconX(18)}
        </button>

        ${state.contactSuccess ? `
          <div class="p-10 text-center">
            <div class="w-20 h-20 bg-[#cfe09b]/30 rounded-full flex items-center justify-center mx-auto mb-5">
              ${iconCheckCircle(42)}
            </div>
            <h3 class="text-2xl font-black text-slate-400 mb-2">Gutschein gesichert</h3>
            <p class="text-slate-400 mb-6">Dein Kontakt ist aufgenommen. Im finalen Step hängen wir hier echte Sendelogik an.</p>
            <button data-action="close-contact" class="bg-slate-100 hover:bg-slate-200 text-slate-400 font-bold py-3 px-6 rounded-xl">Schließen</button>
          </div>
        ` : `
          <div class="bg-[#74b2d4] p-8 text-white text-center">
            <h3 class="text-2xl font-black mb-2">Sichere dir ${formatMoney(ergebnis.gutscheinSumme)} € Rabatt</h3>
            <p class="text-white/85 text-sm">Hinterlasse deine Kontaktdaten für einen kostenlosen Vor-Ort-Check.</p>
          </div>

          <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <input data-bind="vorname" type="text" placeholder="Vorname" value="${escapeHtml(state.formData.vorname)}" class="w-full bg-slate-50  p-3 rounded-xl outline-none">
              <input data-bind="nachname" type="text" placeholder="Nachname" value="${escapeHtml(state.formData.nachname)}" class="w-full bg-slate-50  p-3 rounded-xl outline-none">
            </div>

            <div class="grid grid-cols-3 gap-4">
              <input data-bind="strasse" type="text" placeholder="Straße" value="${escapeHtml(state.formData.strasse)}" class="col-span-2 w-full bg-slate-50  p-3 rounded-xl outline-none">
              <input data-bind="hausnummer" type="text" placeholder="Nr." value="${escapeHtml(state.formData.hausnummer)}" class="w-full bg-slate-50  p-3 rounded-xl outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <input data-bind="email" type="email" placeholder="E-Mail" value="${escapeHtml(state.formData.email)}" class="w-full bg-slate-50  p-3 rounded-xl outline-none">
              <input data-bind="telefon" type="tel" placeholder="Telefon" value="${escapeHtml(state.formData.telefon)}" class="w-full bg-slate-50  p-3 rounded-xl outline-none">
            </div>

            <button data-action="submit-contact" class="w-full bg-[#93c21c] hover:bg-[#82ad18] text-white font-black py-4 rounded-xl">
              Gutschein sichern
            </button>
          </div>
        `}
      </div>
    </div>
  `;
}

function renderCalculationModal(ergebnis) {
  if (!state.showCalculationModal) return '';

  return `
    <div class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl  max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-slate-200 bg-slate-50">
          <h3 class="text-xl font-black text-slate-400">Mathematischer Prüfbericht</h3>
          <button data-action="close-calculation" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
            ${iconX(18)}
          </button>
        </div>

        <div class="overflow-y-auto p-6 space-y-8 text-sm">
          <section class="bg-slate-50 p-5 rounded-2xl ">
            <h4 class="font-black text-[#74b2d4] mb-4">1. Strombedarf</h4>
            <div class="space-y-2">
              <div class="flex justify-between"><span class="text-slate-500">Fossiler Verbrauch in kWh</span><span class="font-black">${formatMoney(ergebnis.waermeBedarfKWhFossil)} kWh</span></div>
              <div class="flex justify-between"><span class="text-slate-500">Nutzwärme (90%)</span><span class="font-black">${formatMoney(ergebnis.nutzWaermeKWh)} kWh</span></div>
              <div class="flex justify-between"><span class="text-slate-500">JAZ</span><span class="font-black">${escapeHtml(String(ergebnis.jaz))}</span></div>
              <div class="flex justify-between"><span class="text-slate-500">WP-Strombedarf</span><span class="font-black text-[#74b2d4]">${formatMoney(ergebnis.wpStrombedarf)} kWh</span></div>
            </div>
          </section>

          <section class="bg-slate-50 p-5 rounded-2xl ">
            <h4 class="font-black text-[#93c21c] mb-4">2. Förderung</h4>
            <div class="space-y-2">
              <div class="flex justify-between"><span class="text-slate-500">Förderfähige Kosten</span><span class="font-black">${formatMoney(ergebnis.report.rep_foerderFaehigeKosten)} €</span></div>
              <div class="flex justify-between"><span class="text-slate-500">Fördersatz</span><span class="font-black">${formatMoney(ergebnis.report.rep_kfwProzent)} %</span></div>
              <div class="flex justify-between"><span class="text-slate-500">Klimabonus</span><span class="font-black">${formatMoney(ergebnis.report.aktKlimaBonus)} %</span></div>
              <div class="flex justify-between"><span class="text-slate-500">KfW Zuschuss</span><span class="font-black text-[#93c21c]">${formatMoney(ergebnis.kfwZuschussMax)} €</span></div>
            </div>
          </section>

          <section class="bg-slate-50 p-5 rounded-2xl ">
            <h4 class="font-black text-slate-400 mb-4">3. PV-Wirtschaftlichkeit</h4>
            <div class="space-y-2">
              <div class="flex justify-between"><span class="text-slate-500">PV-Ertrag</span><span class="font-black">${formatMoney(ergebnis.pvProduktion)} kWh/Jahr</span></div>
              <div class="flex justify-between"><span class="text-slate-500">Solarstrompreis</span><span class="font-black">${formatDecimal(ergebnis.solarstromPreis, 4)} €</span></div>
              <div class="flex justify-between"><span class="text-slate-500">Amortisation</span><span class="font-black">${formatAmortisationValue(ergebnis)}</span></div>
              <div class="flex justify-between"><span class="text-slate-500">ROI</span><span class="font-black text-[#74b2d4]">${escapeHtml(ergebnis.roi)} %</span></div>
            </div>
          </section>
        </div>
      </div>
    </div>
  `;
}


function renderDashboardPlaceholder() {
    const ergebnis = calculateResults();
    const y = state.selectedYears;

    const fossilCounter = Math.round(ergebnis.opexFossilReihe?.[y] || 0);
    const opexSolarCounter = Math.round(ergebnis.opexSolarReihe?.[y] || 0);
    const ersparnisOpexCounter = Math.round(ergebnis.ersparnisOpexReihe?.[y] || 0);

    let tabHtml = '';
    if (state.activeTab === 'FINANZEN') tabHtml = renderFinanceTab(ergebnis);
    if (state.activeTab === 'AUTARKIE') tabHtml = renderAutarkyTab(ergebnis);
    if (state.activeTab === 'TECHNIK') tabHtml = renderTechTab(ergebnis);

    return `
        ${renderPdfPreviewModal(ergebnis)} 
        
        ${renderContactModal(ergebnis)}
        ${renderCalculationModal(ergebnis)}

        <main class="relative z-10 max-w-7xl mx-auto px-4 pb-24 fade-in">
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-strong mb-8 flex flex-col md:flex-row items-center justify-between gap-6 mt-8">
            <div>
            <div class="inline-flex items-center gap-1.5 mb-3 bg-[#cfe09b]/50 px-3 py-1.5 rounded-full text-[#93c21c] text-xs font-black uppercase tracking-widest border border-[#cfe09b]">
                ${iconCheckCircle(14)}
                Auswertung erfolgreich
            </div>
            <h2 class="text-3xl md:text-4xl font-black text-slate-800 mb-2">
                ${state.newProjectName ? `Projekt: ${escapeHtml(state.newProjectName)}` : 'Deine persönliche Auswertung'}
            </h2>
            <p class="text-slate-500 font-medium text-lg">
                Alle Daten für das Gebäude in ${escapeHtml(state.formData.ort || 'deiner Region')} (${escapeHtml(state.formData.standortPlz || '-----')}) wurden berechnet.
            </p>
            </div>

            <div class="flex flex-wrap gap-3">
            <button data-action="toggle-projects" class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-black py-3 px-6 rounded-xl flex items-center gap-2 transition-colors">
                ${iconFolder(18)} Speichern
            </button>
            <button data-action="open-calculation" class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-black py-3 px-6 rounded-xl flex items-center gap-2 transition-colors">
                ${iconCalculator(18)} Berechnung prüfen
            </button>
            <button data-action="open-pdf-preview" class="bg-[#74b2d4] hover:bg-[#5c8fa8] text-white font-black py-3 px-6 rounded-xl flex items-center gap-2 transition-colors shadow-md">
                PDF & Gutschein sichern
            </button>
            </div>
        </div>

        <div class="flex justify-center mb-8">
            <div class="bg-white p-2 rounded-full shadow-soft border border-slate-200 flex flex-wrap justify-center">
            <button data-action="toggle-include-wp" data-value="1" class="px-8 py-3 rounded-full text-sm font-black transition-all ${state.formData.includeWp ? 'bg-[#93c21c] text-white shadow-md' : 'text-slate-500 hover:text-slate-800'}">
                Mit Wärmepumpe (Fördermittel)
            </button>
            <button data-action="toggle-include-wp" data-value="0" class="px-8 py-3 rounded-full text-sm font-black transition-all ${!state.formData.includeWp ? 'bg-[#93c21c] text-white shadow-md' : 'text-slate-500 hover:text-slate-800'}">
                Nur PV-Anlage
            </button>
            </div>
        </div>

        <div class="flex overflow-x-auto hide-scrollbar mb-8 bg-slate-200/50 p-2 rounded-2xl border border-slate-200 max-w-4xl mx-auto">
            ${['FINANZEN', 'AUTARKIE', 'TECHNIK'].map(tab => `
            <button data-action="set-tab" data-tab="${tab}" class="flex-1 min-w-[200px] py-4 px-4 rounded-xl text-sm font-black transition-all flex items-center justify-center gap-2 ${state.activeTab === tab ? 'bg-white text-slate-800 shadow-md' : 'text-slate-500 hover:text-slate-700'}">
                ${tab}
            </button>
            `).join('')}
        </div>

        ${tabHtml}

        </main>
    `;
}
function loadProjectsFromStorage() {
  try {
    const raw = localStorage.getItem('solarAspektProjects');
    if (!raw) return;
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) state.savedProjects = parsed;
  } catch (e) {
    console.error('Fehler beim Laden der Projekte', e);
  }
}

function saveProject(saveAsNew = false) {
  const defaultName =
    state.formData.strasse
      ? `${state.formData.strasse} ${state.formData.hausnummer}, ${state.formData.ort}`.trim()
      : `Projekt ${new Date().toLocaleDateString('de-DE')}`;

  const projectName = (state.newProjectName || '').trim() || defaultName;
  let updated = [...state.savedProjects];

  if (state.currentProjectId && !saveAsNew) {
    updated = updated.map(p =>
      p.id === state.currentProjectId
        ? { ...p, name: projectName, data: { ...state.formData }, updatedAt: Date.now() }
        : p
    );
  } else {
    const id = Date.now().toString();
    updated.push({
      id,
      name: projectName,
      data: { ...state.formData },
      createdAt: Date.now(),
      updatedAt: Date.now()
    });
    state.currentProjectId = id;
  }

  state.savedProjects = updated;
  state.newProjectName = projectName;
  state.showProjectModal = false;
  saveProjects();
}

function loadProjectById(id) {
  const project = state.savedProjects.find(p => p.id === id);
  if (!project) return;

  state.formData = { ...INITIAL_FORM_DATA, ...project.data };
  state.currentProjectId = project.id;
  state.newProjectName = project.name || '';
  state.stage = 'DASHBOARD';
  state.activeTab = 'FINANZEN';
  state.quizStep = 2;
  state.showProjectModal = false;
}

function deleteProjectById(id) {
  const ok = window.confirm('Möchtest du dieses Projekt wirklich löschen?');
  if (!ok) return;

  state.savedProjects = state.savedProjects.filter(p => p.id !== id);
  if (state.currentProjectId === id) {
    state.currentProjectId = null;
    state.newProjectName = '';
  }
  saveProjects();
}

function startNewProject() {
  state.formData = structuredClone(INITIAL_FORM_DATA);
  state.currentProjectId = null;
  state.newProjectName = '';
  state.showProjectModal = false;
  state.quizStep = 0;
  state.stage = 'QUIZ';
}

function cycleLoadingText() {
  const steps = [
    'Analysiere Sonneneinstrahlung...',
    'Prüfe maximale KfW-Fördermittel...',
    'Kalkuliere Amortisation und Ertrag...'
  ];

  let i = 0;
  state.loadingText = steps[0];
  render();

  const timer = setInterval(() => {
    i += 1;
    if (i < steps.length) {
      state.loadingText = steps[i];
      render();
    } else {
      clearInterval(timer);
    }
  }, 1100);

  return timer;
}

function formatDate(ts) {
  if (!ts) return '-';
  try {
    return new Date(ts).toLocaleDateString('de-DE');
  } catch {
    return '-';
  }
}

function formatAmortisationValue(ergebnis) {
  if (ergebnis.amortisationJahre === null || ergebnis.amortisationJahre > 40) return '> 40 Jahre';
  return `${formatDecimal(ergebnis.amortisationJahre, 1)} Jahre`;
}

function iconTrash(size = 18, cls = '') {
  return `
    <svg class="${cls}" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 6h18"></path>
      <path d="M8 6V4h8v2"></path>
      <path d="M19 6l-1 14H6L5 6"></path>
      <path d="M10 11v6"></path>
      <path d="M14 11v6"></path>
    </svg>
  `;
}

function renderProjectModal() {
  if (!state.showProjectModal) return '';

  return `
    <div class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl  max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-slate-200 bg-slate-50">
          <h2 class="text-xl font-black text-slate-400 flex items-center gap-2">
            ${iconFolder(22)} Meine Projekte
          </h2>
          <button data-action="close-projects" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
            ${iconX(18)}
          </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
          ${(state.stage === 'DASHBOARD' || (state.formData.standortPlz || '').length > 3) ? `
            <div class="bg-white p-5 rounded-2xl border border-[#74b2d4] shadow-soft mb-6">
              <h3 class="text-sm font-black text-[#74b2d4] mb-3">Aktuellen Stand speichern</h3>
              <div class="flex flex-col sm:flex-row gap-3">
                <input
                  data-bind="projectNameInput"
                  type="text"
                  placeholder="Name des Projekts"
                  value="${escapeHtml(state.newProjectName)}"
                  class="flex-1 bg-slate-50  p-3 rounded-xl outline-none"
                >
                <button data-action="save-project" class="bg-[#74b2d4] hover:bg-[#5c8fa8] text-white font-bold py-3 px-6 rounded-xl">
                  ${state.currentProjectId ? 'Überschreiben' : 'Speichern'}
                </button>
                ${state.currentProjectId ? `
                  <button data-action="save-project-as-new" class="bg-slate-200 hover:bg-slate-300 text-slate-400 font-bold py-3 px-6 rounded-xl">
                    Als Neu
                  </button>
                ` : ''}
              </div>
            </div>
          ` : ''}

          <h3 class="text-sm font-black text-slate-400 mb-3 uppercase tracking-widest pl-1">Gespeicherte Projekte</h3>

          ${state.savedProjects.length === 0 ? `
            <div class="text-center p-8 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 font-medium">
              Bisher keine Projekte gespeichert.
            </div>
          ` : `
            <div class="space-y-3">
              ${state.savedProjects.map(p => `
                <div class="bg-white p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-4 ${state.currentProjectId === p.id ? 'border-[#93c21c] ring-1 ring-[#93c21c]' : 'border-slate-200'}">
                  <div>
                    <h4 class="font-black text-slate-400 flex items-center gap-2">
                      ${escapeHtml(p.name || 'Unbenannt')}
                      ${state.currentProjectId === p.id ? `<span class="text-[10px] bg-[#93c21c] text-white px-2 py-0.5 rounded-full uppercase tracking-wider">Aktiv</span>` : ''}
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                      Erstellt: ${formatDate(p.createdAt)} • Letzte Änd.: ${formatDate(p.updatedAt)}
                    </p>
                  </div>
                  <div class="flex gap-2 shrink-0">
                    <button data-action="load-project" data-id="${p.id}" class="bg-slate-100 hover:bg-slate-200 text-slate-400 font-bold py-2 px-4 rounded-lg text-sm">
                      Laden
                    </button>
                    <button data-action="delete-project" data-id="${p.id}" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-lg">
                      ${iconTrash(18)}
                    </button>
                  </div>
                </div>
              `).join('')}
            </div>
          `}
        </div>

        <div class="p-4 border-t border-slate-200 bg-white flex justify-center">
          <button data-action="new-project" class="flex items-center gap-2 text-slate-500 hover:text-slate-400 font-bold text-sm transition-colors">
            Neues leeres Projekt starten
          </button>
        </div>
      </div>
    </div>
  `;
}



function renderPdfPreviewModal(ergebnis) {
  if (!state.showPdfPreview) return '';

  const d = state.formData;
  
  // Helper for the double ring chart in the PDF
  const renderDoubleRing = (s, pctErtrag, pctHeiz) => `
    <div class="flex flex-col items-center gap-2">
      <span class="font-bold text-[8pt] text-slate-700 uppercase tracking-widest">${escapeHtml(s.name)}</span>
      <div class="relative w-20 h-20 flex items-center justify-center">
        <svg class="w-full h-full transform -rotate-90 absolute" viewBox="0 0 36 36">
          <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
          <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-[#93c21c]" stroke-width="3" stroke-dasharray="97.38" stroke-dashoffset="${97.38 - (97.38 * Math.min(100, pctErtrag) / 100)}" stroke-linecap="round"></circle>
          <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#e3effb]" stroke-width="3"></circle>
          <circle cx="18" cy="18" r="11.5" fill="none" class="stroke-[#74b2d4]" stroke-width="3" stroke-dasharray="72.25" stroke-dashoffset="${72.25 - (72.25 * Math.min(100, pctHeiz) / 100)}" stroke-linecap="round"></circle>
        </svg>
      </div>
      <div class="text-[7pt] text-center mt-1 space-y-0.5 w-full">
        <div class="flex justify-between w-full"><span class="text-slate-500">Ertrag:</span> <strong class="text-[#93c21c]">${formatMoney(s.ertrag)} kWh (${pctErtrag}%)</strong></div>
        <div class="flex justify-between w-full"><span class="text-slate-500">Heizung:</span> <strong class="text-[#74b2d4]">${formatMoney(s.heizbedarf)} kWh (${pctHeiz}%)</strong></div>
      </div>
    </div>
  `;

  return `
    <div class="fixed inset-0 z-[100] bg-slate-900/90 overflow-y-auto flex flex-col items-center animate-in fade-in print-hide">
      
      <div class="sticky top-0 w-full bg-slate-900/80 backdrop-blur-md p-4 flex justify-center gap-4 z-50 shadow-lg shrink-0">
        <button data-action="download-pdf" ${state.isGeneratingPDF ? 'disabled' : ''} class="bg-[#93c21c] text-white font-black py-3 px-6 rounded-xl flex items-center gap-2 hover:bg-[#82ad18] transition-colors">
          ${state.isGeneratingPDF 
            ? '<div class="w-4 h-4 border-2 border-white/50 border-t-white rounded-full animate-spin"></div> PDF wird erstellt...' 
            : `${iconPrinter(18)} PDF jetzt herunterladen`}
        </button>
        <button data-action="close-pdf-preview" class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 px-6 rounded-xl transition-colors">
          Schließen
        </button>
      </div>

      <div class="w-full pb-10 overflow-x-auto flex flex-col items-center pt-8">
        <div id="pdf-export-wrapper" class="bg-white text-left shadow-2xl mx-auto" style="width: 794px;">
          
          <div style="width: 794px; height: 1123px; box-sizing: border-box;" class="relative bg-white flex flex-col overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-[#f8fafc]">
              <div class="absolute bottom-0 w-full h-32 bg-gradient-to-t from-white to-transparent z-10"></div>
            </div>
            <div class="relative z-20 p-16 flex flex-col h-full">
              <div class="flex items-center gap-3 mb-24">
                <span class="text-[#93c21c]">${iconSun(40)}</span>
                <span class="text-3xl font-black tracking-tighter text-slate-800">SOLAR<span class="text-[#74b2d4]">ASPEKT</span></span>
              </div>
              <div class="mt-auto mb-32">
                <div class="inline-flex items-center gap-2 bg-[#cfe09b]/50 px-4 py-1.5 rounded-full text-[#93c21c] text-[10pt] font-black uppercase tracking-widest border border-[#cfe09b] mb-6">
                  ${iconCheckCircle(20)} Individuelle Potenzialanalyse
                </div>
                <h1 class="text-[40pt] font-black uppercase text-slate-800 tracking-tight leading-[0.95] mb-6">
                  Dein Fahrplan zur <br/><span class="text-[#74b2d4]">Energie-Freiheit.</span>
                </h1>
                <p class="text-[14pt] text-slate-500 font-medium max-w-lg leading-relaxed">
                  Schluss mit der Preisspirale. Mach dich unabhängig von Stromkonzernen und fossilen Brennstoffen.
                </p>
              </div>
              <div class="mt-auto border-t-4 border-[#93c21c] pt-8 grid grid-cols-2">
                <div>
                  <p class="text-[10pt] font-bold text-slate-400 uppercase tracking-widest mb-1">Erstellt für:</p>
                  <p class="text-[14pt] font-black text-slate-800">${escapeHtml(d.strasse)} ${escapeHtml(d.hausnummer)}</p>
                  <p class="text-[12pt] text-slate-600">${escapeHtml(d.standortPlz)} ${escapeHtml(d.ort)}</p>
                </div>
                <div class="text-right">
                  <p class="text-[10pt] font-bold text-slate-400 uppercase tracking-widest mb-1">Datum:</p>
                  <p class="text-[14pt] font-black text-slate-800">${new Date().toLocaleDateString('de-DE')}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="pdf-page-break" style="page-break-after: always;"></div>

          <div style="width: 794px; padding: 40px 56px; box-sizing: border-box;" class="relative bg-white flex flex-col">
            <div class="flex justify-between items-end border-b-2 border-slate-200 pb-2 mb-6">
              <h1 class="text-[14pt] font-black uppercase text-slate-800">1. Ausgangslage & Energie-Transformation</h1>
            </div>

            <div class="mb-6">
              <h2 class="text-[12pt] font-black text-[#74b2d4] mb-2">Wie aus teurem Fossil effizienter Strombedarf wird</h2>
              <p class="text-[9pt] leading-relaxed text-slate-600">
                Um deine Wirtschaftlichkeit präzise zu berechnen, wandeln wir deine alten fossilen Verbrauchswerte in den zukünftigen Strombedarf um. Durch die enorme Effizienz einer Wärmepumpe (COP: ${ergebnis.jaz}) und deines E-Autos sinkt dein reiner Energiebedarf massiv, während der Strombedarf minimal steigt.
              </p>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-8">
               <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                  <h3 class="text-[10pt] font-bold text-slate-700 mb-3 border-b pb-2">Vorher (Fossil)</h3>
                  <div class="space-y-2 text-[8pt]">
                    <div class="flex justify-between"><span class="text-slate-500">Hausstrom (${formatMoney(ergebnis.hausStrombedarf)} kWh à ${parseFloat(d.evuPreis || 0.35).toFixed(2).replace('.',',')} €):</span> <span class="font-bold">${formatMoney(ergebnis.stromKostenHeute)} €</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Heizung (${d.heizVerbrauch} ${ENERGIE[d.heizungsArt]?.einheit} à ${parseFloat(d.heizPreis).toFixed(2).replace('.',',')} €):</span> <span class="font-bold">${formatMoney(ergebnis.heizKostenHeute)} €</span></div>
                    ${ergebnis.activeAuto ? `<div class="flex justify-between"><span class="text-slate-500">Sprit (~7L/100km à ${parseFloat(d.spritPreis || 1.80).toFixed(2).replace('.',',')} €/l):</span> <span class="font-bold">${formatMoney(ergebnis.kostenAutoHeute)} €</span></div>` : ''}
                    <div class="flex justify-between"><span class="text-slate-500">Wartung, Schornsteinfeger & Rep.:</span> <span class="font-bold">${ergebnis.wartungAlt_pa} €</span></div>
                    <div class="flex justify-between font-black text-slate-800 border-t border-slate-200 pt-1 mt-1"><span>Gesamtkosten vorher:</span> <span>${formatMoney(ergebnis.stromKostenHeute + ergebnis.heizKostenHeute + ergebnis.kostenAutoHeute + ergebnis.wartungAlt_pa)} € / Jahr</span></div>
                  </div>
               </div>
               <div class="bg-[#e3effb]/30 p-4 rounded-xl border border-[#c0d8ea]">
                  <h3 class="text-[10pt] font-bold text-[#74b2d4] mb-3 border-b border-[#c0d8ea] pb-2">Neuer Strombedarf (Vernetzt)</h3>
                  <div class="space-y-2 text-[8pt]">
                    <div class="flex justify-between"><span>Allgemeiner Hausstrom:</span> <span class="font-bold">${formatMoney(ergebnis.hausStrombedarf)} kWh</span></div>
                    ${d.includeWp ? `<div class="flex justify-between"><span>Wärmepumpe (Stromanteil):</span> <span class="font-bold">${formatMoney(ergebnis.wpStrombedarf)} kWh</span></div>` : ''}
                    ${ergebnis.activeAuto ? `<div class="flex justify-between"><span>E-Auto (Geplant):</span> <span class="font-bold">${formatMoney(ergebnis.autoStrombedarf)} kWh</span></div>` : ''}
                    <div class="flex justify-between border-t border-[#c0d8ea] pt-1 text-[9pt] font-black text-[#74b2d4]"><span>Neuer Gesamtstrombedarf:</span> <span>${formatMoney(ergebnis.gesamtbedarf)} kWh</span></div>
                  </div>
               </div>
            </div>

            <div class="bg-[#cfe09b]/20 p-5 rounded-xl border border-[#cfe09b]">
               <h3 class="text-[11pt] font-black uppercase text-[#93c21c] mb-2 flex items-center gap-2">${iconCheckSquare(20)} Deine Anlagen-Dimensionierung</h3>
               <p class="text-[9pt] leading-relaxed text-slate-700">
                 <strong>Warum empfehlen wir genau ${ergebnis.pvGroesse} kWp PV und ${ergebnis.speicherGroesse} kWh Speicher?</strong><br/>
                 Der Fehler vieler Standard-Angebote ist es, die Solaranlage nur für den Sommer zu berechnen. Da dein zukünftiger Hauptverbrauch (Heizung) jedoch im Winter und in der Übergangszeit liegt, muss die Solaranlage stark genug dimensioniert sein, um auch bei schwächerer Sonne ausreichend Ertrag zu liefern. Der ${ergebnis.speicherGroesse} kWh Speicher überbrückt die Nächte extrem effizient, um den Netzbezug minimal zu halten.
               </p>
            </div>
          </div>

          <div class="pdf-page-break" style="page-break-after: always;"></div>

          <div style="width: 794px; padding: 20px 56px; box-sizing: border-box;" class="relative bg-white flex flex-col">
            <div class="flex justify-between items-end border-b-2 border-slate-200 pb-2 mb-6">
              <h1 class="text-[14pt] font-black uppercase text-slate-800">2. Deine Unabhängigkeit & Autarkie</h1>
            </div>

            ${d.includeSolar ? `
            <div class="mb-8">
               <h2 class="text-[11pt] font-black uppercase text-[#74b2d4] mb-4">Deine Gesamtbilanz auf einen Blick</h2>
               <div class="grid grid-cols-3 gap-6">
                 ${renderDonut(ergebnis.autarkieQuote, '#93c21c', 'Autarkiegrad', 'Bedarfsdeckung (Autarkie)', 'Deckung', formatMoney(ergebnis.genutzterPvStrom) + ' kWh', 'Netzbezug', formatMoney(ergebnis.restStromBezug) + ' kWh')}
                 ${renderDonut(ergebnis.eigenverbrauchQuote, '#74b2d4', 'Eigenverbrauchsquote', 'Nutzung des PV-Stroms', 'Genutzt', formatMoney(ergebnis.genutzterPvStrom) + ' kWh', 'Einspeisung', formatMoney(ergebnis.netzEinspeisung) + ' kWh')}
                 ${renderDonut(ergebnis.finanzAutarkieQuote, '#93c21c', 'Finanzielle<br/>Unabhängigkeit', 'Schutz vor Preisanstieg', 'Ersparnis', '+'+formatMoney(ergebnis.ersparnisJahr1)+' €', 'Vorher', formatMoney(ergebnis.fossilKostenJahr1)+' €')}
               </div>
            </div>
            ` : ''}

            ${d.includeWp && d.includeSolar ? `
              <div class="mb-6 mt-6">
                 <h2 class="text-[11pt] font-black uppercase text-[#93c21c] mb-2 flex items-center gap-2">Heizbedarf vs. Solarertrag</h2>
                 <p class="text-[8pt] text-slate-600 mb-4 leading-snug">
                   <strong>Wie viel Energie brauche ich und wie viel erzeuge ich selbst?</strong> Diese Gegenüberstellung zeigt deinen Wärmepumpen-Strombedarf und deinen PV-Ertrag im Jahresverlauf als Doppel-Ringdiagramm. Der äußere Ring (Grün) zeigt den Anteil des Solarertrags, der innere (Blau) den Anteil des Heizbedarfs am jeweiligen Jahresgesamtwert.
                 </p>
                 <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="grid grid-cols-4 gap-4 mt-2">
                      ${ergebnis.saisonDaten.map((s) => {
                         const pctErtrag = ergebnis.pvProduktion > 0 ? Math.round((s.ertrag / ergebnis.pvProduktion) * 100) : 0;
                         const pctHeiz = ergebnis.wpStrombedarf > 0 ? Math.round((s.heizbedarf / ergebnis.wpStrombedarf) * 100) : 0;
                         return renderDoubleRing(s, pctErtrag, pctHeiz);
                      }).join('')}
                    </div>
                    <div class="flex justify-center gap-6 mt-4 pt-4 border-t border-slate-200">
                       <div class="flex items-center gap-1.5"><div class="w-3 h-3 border-2 border-[#93c21c] rounded-full"></div><span class="text-[7pt] font-bold text-slate-600">Äußerer Ring: Solarertrag</span></div>
                       <div class="flex items-center gap-1.5"><div class="w-3 h-3 border-2 border-[#74b2d4] rounded-full"></div><span class="text-[7pt] font-bold text-slate-600">Innerer Ring: Heizbedarf WP</span></div>
                    </div>
                 </div>
              </div>
            ` : ''}
          </div>

          <div class="pdf-page-break" style="page-break-after: always;"></div>

          <div style="width: 794px; padding: 40px 56px; box-sizing: border-box;" class="relative bg-white flex flex-col">
            <div class="flex justify-between items-end border-b-2 border-slate-200 pb-2 mb-6">
              <h1 class="text-[14pt] font-black uppercase text-slate-800">3. Investition, Förderung & Wirtschaftlichkeit</h1>
            </div>

            <div class="grid ${d.includeSolar ? 'grid-cols-3' : 'grid-cols-2'} gap-4 mb-8">
               <div class="bg-white p-4 rounded-xl border border-slate-200 text-center flex flex-col justify-center shadow-sm">
                 <span class="text-[14pt] font-black text-[#74b2d4] block">${formatAmortisationValue(ergebnis)}</span>
                 <span class="text-[8pt] text-slate-500 font-bold uppercase tracking-widest mt-1">Amortisation</span>
               </div>
               <div class="bg-[#cfe09b]/20 p-4 rounded-xl border border-[#cfe09b] text-center flex flex-col justify-center shadow-sm">
                 <span class="text-[14pt] font-black text-[#93c21c] block">${ergebnis.roi} %</span>
                 <span class="text-[8pt] text-slate-500 font-bold uppercase tracking-widest mt-1">Rendite p.a. (Start)</span>
               </div>
               ${d.includeSolar ? `
                 <div class="bg-white p-4 rounded-xl border border-[#c0d8ea] shadow-sm flex flex-col justify-center text-center relative overflow-hidden">
                   <div class="absolute top-0 right-0 w-8 h-8 bg-[#c0d8ea] opacity-20 rounded-bl-full"></div>
                   <span class="text-[14pt] font-black text-slate-800 block">${formatDecimal(ergebnis.solarstromPreis, 2)} €</span>
                   <span class="text-[8pt] text-slate-500 font-bold uppercase tracking-widest mt-1">Dein Solarstrompreis</span>
                 </div>
               ` : ''}
            </div>

            <div class="grid grid-cols-2 gap-8 mb-6">
              <div>
                <h2 class="text-[11pt] font-bold uppercase text-[#74b2d4] border-b border-[#c0d8ea] pb-1 mb-4">Investitionsübersicht</h2>
                
                ${d.includeWp ? `
                  <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 shadow-sm mb-3 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#74b2d4]"></div>
                    <h4 class="text-[9pt] font-black text-slate-800 mb-2 pl-2">Wärmepumpe (${ergebnis.wpGroesseKW} kW)</h4>
                    <div class="flex justify-between text-[8pt] font-bold text-slate-600 mb-1 pl-2">
                      <span>Investition</span><span>${formatMoney(ergebnis.investWPBrutto)} €</span>
                    </div>
                    <div class="flex justify-between text-[8pt] font-bold text-[#93c21c] mb-1 pl-2">
                      <span>- KfW-Förderung</span><span>-${formatMoney(ergebnis.kfwZuschussMax)} €</span>
                    </div>
                    ${ergebnis.f_wp_zusatz > 0 ? `
                    <div class="flex justify-between text-[8pt] font-bold text-[#93c21c] mb-1 pl-2">
                      <span>- Zusatz (${escapeHtml(d.wpZusatzFoerderName) || 'Kommunal'})</span><span>-${formatMoney(ergebnis.f_wp_zusatz)} €</span>
                    </div>
                    ` : ''}
                    <div class="flex justify-between text-[9pt] font-black text-[#74b2d4] border-t border-slate-300 pt-1 mt-1 pl-2">
                      <span>Wärmepumpe</span><span>${formatMoney(ergebnis.investWpNetto)} €</span>
                    </div>
                  </div>
                ` : ''}

                ${d.includeSolar ? `
                  <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 shadow-sm mb-3 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#93c21c]"></div>
                    <h4 class="text-[9pt] font-black text-slate-800 mb-2 pl-2">Photovoltaik (${ergebnis.pvGroesse} kWp)</h4>
                    <div class="flex justify-between text-[8pt] font-bold text-slate-600 mb-1 pl-2">
                      <span>Investition</span><span>${formatMoney(ergebnis.investPVOnly)} €</span>
                    </div>
                    ${ergebnis.f_pv > 0 ? `
                    <div class="flex justify-between text-[8pt] font-bold text-[#93c21c] mb-1 pl-2">
                      <span>- Zusatz (${escapeHtml(d.pvZusatzFoerderName) || 'Förderung'})</span><span>-${formatMoney(ergebnis.f_pv)} €</span>
                    </div>
                    ` : ''}
                    <div class="flex justify-between text-[9pt] font-black text-[#93c21c] border-t border-slate-300 pt-1 mt-1 pl-2">
                      <span>Photovoltaik</span><span>${formatMoney(ergebnis.investPvNetto)} €</span>
                    </div>
                  </div>

                  <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 shadow-sm mb-3 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#93c21c]"></div>
                    <h4 class="text-[9pt] font-black text-slate-800 mb-2 pl-2">Speicher (${ergebnis.speicherGroesse} kWh)</h4>
                    <div class="flex justify-between text-[8pt] font-bold text-slate-600 mb-1 pl-2">
                      <span>Investition</span><span>${formatMoney(ergebnis.investSpeicher)} €</span>
                    </div>
                    ${ergebnis.f_speicher > 0 ? `
                    <div class="flex justify-between text-[8pt] font-bold text-[#93c21c] mb-1 pl-2">
                      <span>- Zusatz (${escapeHtml(d.speicherZusatzFoerderName) || 'Förderung'})</span><span>-${formatMoney(ergebnis.f_speicher)} €</span>
                    </div>
                    ` : ''}
                    <div class="flex justify-between text-[9pt] font-black text-[#93c21c] border-t border-slate-300 pt-1 mt-1 pl-2">
                      <span>Speicher</span><span>${formatMoney(ergebnis.investSpeicherNetto)} €</span>
                    </div>
                  </div>
                ` : ''}

                ${(d.includeSolar && ergebnis.activeAuto) ? `
                  <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 shadow-sm mb-3 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#93c21c]"></div>
                    <h4 class="text-[9pt] font-black text-slate-800 mb-2 pl-2">Wallbox</h4>
                    <div class="flex justify-between text-[8pt] font-bold text-slate-600 mb-1 pl-2">
                      <span>Investition</span><span>${formatMoney(ergebnis.investWallbox)} €</span>
                    </div>
                    ${ergebnis.f_wallbox > 0 ? `
                    <div class="flex justify-between text-[8pt] font-bold text-[#93c21c] mb-1 pl-2">
                      <span>- Zusatz (${escapeHtml(d.wallboxZusatzFoerderName) || 'Förderung'})</span><span>-${formatMoney(ergebnis.f_wallbox)} €</span>
                    </div>
                    ` : ''}
                    <div class="flex justify-between text-[9pt] font-black text-[#93c21c] border-t border-slate-300 pt-1 mt-1 pl-2">
                      <span>Wallbox</span><span>${formatMoney(ergebnis.investWallboxNetto)} €</span>
                    </div>
                  </div>
                ` : ''}

                <div class="bg-[#e3effb] p-3 rounded-lg border border-[#c0d8ea] mt-2">
                  <div class="flex justify-between text-[11pt] font-black text-[#74b2d4]">
                    <span>Gesamtinvestition</span><span>${formatMoney(ergebnis.nettoInvestition)} €</span>
                  </div>
                </div>
              </div>

              <div>
                 <h2 class="text-[11pt] font-bold uppercase text-[#74b2d4] border-b border-[#c0d8ea] pb-1 mb-4">Betriebskosten-Verlauf</h2>
                 <div class="flex flex-col gap-3">
                   ${[1, 10, 20, 30].map(jahr => `
                     <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3 relative overflow-hidden">
                        <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-[#93c21c] opacity-80"></div>
                        <div class="bg-slate-50 border border-[#c0d8ea] w-12 h-12 rounded-full flex flex-col items-center justify-center shrink-0">
                           <span class="text-[11pt] font-black text-[#74b2d4] leading-none mb-0.5">${jahr}</span>
                           <span class="text-[6pt] font-bold text-[#74b2d4] uppercase tracking-widest">${jahr === 1 ? 'Jahr' : 'Jahre'}</span>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-x-2 gap-y-1 text-[7.5pt]">
                           <div>
                              <p class="font-bold text-slate-500 uppercase">Vorher</p>
                              <p class="text-[9pt] font-black text-slate-600 line-through">${formatMoney(ergebnis.opexFossilReihe[jahr])} €</p>
                           </div>
                           <div class="border-l border-slate-200 pl-2">
                              <p class="font-bold text-[#74b2d4] uppercase">Nachher</p>
                              <p class="text-[9pt] font-black text-[#74b2d4]">${formatMoney(ergebnis.opexSolarReihe[jahr])} €</p>
                           </div>
                           <div class="col-span-2 border-t border-slate-100 mt-1 pt-1">
                              <p class="font-bold text-[#93c21c] uppercase inline mr-2">Ersparnis:</p>
                              <p class="text-[9pt] font-black text-[#93c21c] inline">+${formatMoney(ergebnis.ersparnisOpexReihe[jahr])} €</p>
                           </div>
                        </div>
                     </div>
                   `).join('')}
                 </div>
              </div>
            </div>
          </div>

          <div class="pdf-page-break" style="page-break-after: always;"></div>

          <div style="width: 794px; padding: 40px 56px; box-sizing: border-box;" class="relative bg-white flex flex-col">
            <div class="flex justify-between items-end border-b-2 border-slate-200 pb-2 mb-6">
              <h1 class="text-[14pt] font-black uppercase text-slate-800">4. Premium-Hardware & Service</h1>
            </div>

            <h2 class="text-[12pt] font-black text-[#74b2d4] mb-4 flex items-center gap-2">
               Kompromisslose Qualität für Jahrzehnte
            </h2>
            <p class="text-[9pt] leading-relaxed text-slate-600 mb-6">
               Ein Energie-Systemwechsel ist eine Investition, die über Generationen halten muss. Deshalb verbauen wir ausschließlich High-End-Komponenten, die nicht nur auf dem Datenblatt glänzen, sondern in der harten Praxis bestehen.
            </p>

            <div class="grid grid-cols-2 gap-6 mb-8">
               ${d.includeWp ? `
               <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                  <h3 class="text-[10pt] font-black text-slate-800 mb-2">Die intelligente Wärmepumpe</h3>
                  <ul class="text-[8pt] text-slate-600 space-y-2 list-disc pl-4 marker:text-[#74b2d4]">
                     <li><strong>Flüsterleise:</strong> Mit &lt; 35 dB(A) leiser als ein moderner Kühlschrank. Weder du noch die Nachbarn werden sie hören.</li>
                     <li><strong>Heizen & Kühlen:</strong> Aktive Klimafunktion im Sommer über die Fußbodenheizung inklusive.</li>
                     <li><strong>Maximaler COP:</strong> Aus 1 kWh Strom werden 4-5 kWh Wärme gewonnen. Reine physikalische Naturkraft.</li>
                  </ul>
               </div>
               ` : ''}
               ${d.includeSolar ? `
               <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                  <h3 class="text-[10pt] font-black text-slate-800 mb-2">Bifaziale Photovoltaik</h3>
                  <ul class="text-[8pt] text-slate-600 space-y-2 list-disc pl-4 marker:text-[#93c21c]">
                     <li><strong>Glas-Glas-Technologie:</strong> Absolut sturmfest, keine Mikrorisse, höchste Brandschutzklasse.</li>
                     <li><strong>30 Jahre Garantie:</strong> Lineare Leistungsgarantie für ein langes, sorgenfreies Anlagenleben.</li>
                     <li><strong>Schwachlicht-Stark:</strong> Hervorragende Erträge auch bei bewölktem Himmel und in der Übergangszeit.</li>
                  </ul>
               </div>
               ` : ''}
               ${d.includeSolar ? `
               <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                  <h3 class="text-[10pt] font-black text-slate-800 mb-2">LiFePO4 Batteriespeicher</h3>
                  <ul class="text-[8pt] text-slate-600 space-y-2 list-disc pl-4 marker:text-[#93c21c]">
                     <li><strong>Maximale Sicherheit:</strong> Lithium-Eisenphosphat ist thermisch stabil, brennt und explodiert nicht.</li>
                     <li><strong>Zyklenfestigkeit:</strong> Entwickelt für 10.000+ Ladezyklen ohne nennenswerten Kapazitätsverlust.</li>
                     <li><strong>Schwarzstartfähig:</strong> Bei Stromausfall versorgt das System dein Haus nahtlos weiter (optional).</li>
                  </ul>
               </div>
               ` : ''}
               ${(d.includeSolar && ergebnis.activeAuto) ? `
               <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                  <h3 class="text-[10pt] font-black text-slate-800 mb-2">Smarte Premium-Wallbox</h3>
                  <ul class="text-[8pt] text-slate-600 space-y-2 list-disc pl-4 marker:text-[#74b2d4]">
                     <li><strong>Überschussladen:</strong> Lade dein Auto 100% automatisch mit Sonnenstrom, der sonst ins Netz fließt.</li>
                     <li><strong>V2H / V2G Ready:</strong> Mach dein Auto zum größten Heimspeicher der Welt. Vorbereitet für bidirektionales Laden.</li>
                     <li><strong>App & Abrechnung:</strong> Volle Kontrolle via App und einfache Dienstwagen-Abrechnung dank integriertem RFID-Chip.</li>
                  </ul>
               </div>
               ` : ''}
            </div>

            <div class="mt-4 border-t border-slate-200 pt-6">
               <h2 class="text-[12pt] font-black text-slate-800 mb-4 flex items-center gap-2">
                  Warum Solar Aspekt?
               </h2>
               <div class="grid grid-cols-2 gap-4">
                  <div class="flex gap-3">
                     <div>
                        <h4 class="text-[9pt] font-black text-slate-800">Regionale Meisterqualität</h4>
                        <p class="text-[8pt] text-slate-600">Wir arbeiten nicht mit anonymen Subunternehmern. Eigene, zertifizierte Handwerker aus der Region montieren und warten deine Anlage.</p>
                     </div>
                  </div>
                  <div class="flex gap-3">
                     <div>
                        <h4 class="text-[9pt] font-black text-slate-800">Förder-Garantie</h4>
                        <p class="text-[8pt] text-slate-600">Wir übernehmen den kompletten Behörden-Dschungel. Von der KfW-Zusage bis zur Netzanmeldung – du musst dich um nichts kümmern.</p>
                     </div>
                  </div>
                  <div class="flex gap-3 mt-2">
                     <div>
                        <h4 class="text-[9pt] font-black text-slate-800">Alles aus einer Hand</h4>
                        <p class="text-[8pt] text-slate-600">Planung, Gerüstbau, Dachmontage, Elektrik und Heizungsbau. Ein Ansprechpartner, ein Projektleiter, null Schnittstellenprobleme.</p>
                     </div>
                  </div>
                  <div class="flex gap-3 mt-2">
                     <div>
                        <h4 class="text-[9pt] font-black text-slate-800">Energiemanagement (HEMS)</h4>
                        <p class="text-[8pt] text-slate-600">Wir vernetzen WP, Speicher und Auto perfekt miteinander. Das System lernt mit und nutzt dynamische Tarife für minimale Kosten.</p>
                     </div>
                  </div>
               </div>
            </div>
          </div>

          <div class="pdf-page-break" style="page-break-after: always;"></div>

          <div style="width: 794px; padding: 40px 56px 56px 56px; box-sizing: border-box;" class="relative bg-white flex flex-col h-full">
            <div class="flex justify-between items-end border-b-2 border-slate-200 pb-2 mb-6">
              <h1 class="text-[14pt] font-black uppercase text-slate-800">5. Klimaschutz & Dein Angebot</h1>
            </div>

            <div class="bg-[#cfe09b]/20 p-6 rounded-2xl border border-[#cfe09b] mb-10">
               <h3 class="text-[12pt] font-black uppercase text-[#93c21c] mb-3 flex items-center gap-2">
                  Dein aktiver Klimaschutz
               </h3>
               <p class="text-[9.5pt] leading-relaxed text-slate-700 mb-5">
                 Durch deinen Systemwechsel sparst du massiv CO₂ ein und entziehst dich den kommenden CO₂-Strafsteuern auf fossile Brennstoffe. Hier ist deine Umweltbilanz:
               </p>
               <div class="grid grid-cols-4 gap-4 mb-5">
                  <div class="bg-white p-3 rounded-lg border border-[#cfe09b] text-center">
                     <span class="block text-[8pt] text-slate-500 font-bold uppercase tracking-widest mb-1">Pro Jahr</span>
                     <span class="block text-[12pt] font-black text-[#93c21c]">${ergebnis.co2ErsparnisPerYear.toFixed(1).replace('.', ',')} t</span>
                  </div>
                  <div class="bg-white p-3 rounded-lg border border-[#cfe09b] text-center">
                     <span class="block text-[8pt] text-slate-500 font-bold uppercase tracking-widest mb-1">10 Jahre</span>
                     <span class="block text-[12pt] font-black text-[#93c21c]">${(ergebnis.co2ErsparnisPerYear * 10).toFixed(1).replace('.', ',')} t</span>
                  </div>
                  <div class="bg-white p-3 rounded-lg border border-[#cfe09b] text-center">
                     <span class="block text-[8pt] text-slate-500 font-bold uppercase tracking-widest mb-1">20 Jahre</span>
                     <span class="block text-[12pt] font-black text-[#93c21c]">${(ergebnis.co2ErsparnisPerYear * 20).toFixed(1).replace('.', ',')} t</span>
                  </div>
                  <div class="bg-white p-3 rounded-lg border border-[#cfe09b] text-center">
                     <span class="block text-[8pt] text-slate-500 font-bold uppercase tracking-widest mb-1">30 Jahre</span>
                     <span class="block text-[12pt] font-black text-[#93c21c]">${(ergebnis.co2ErsparnisPerYear * 30).toFixed(1).replace('.', ',')} t</span>
                  </div>
               </div>
               <div class="bg-white p-4 rounded-xl border border-[#cfe09b] flex items-center gap-4">
                  <p class="text-[9pt] text-slate-700 leading-snug">
                    Deine jährliche Einsparung entspricht der CO₂-Speicherkraft von <strong>${formatMoney(ergebnis.co2Baeume)} Bäumen</strong> oder einer Mischwaldfläche von <strong>${formatMoney(ergebnis.co2FlaecheQm)} m²</strong>. Du leistest damit einen echten, sichtbaren Beitrag zur Energiewende.
                  </p>
               </div>
            </div>

            <div class="bg-[#74b2d4] text-white p-8 rounded-3xl shadow-xl flex flex-col items-center text-center mt-auto mb-10">
              <div class="flex items-center gap-2 mb-6">
                <span class="text-[#93c21c]">${iconSun(32)}</span>
                <span class="text-2xl font-black tracking-tighter">SOLAR<span class="text-white">ASPEKT</span></span>
              </div>
              
              <h2 class="text-[16pt] font-black mb-3">Gehe keine Kompromisse ein.</h2>
              <p class="text-[10pt] max-w-[120mm] mx-auto text-white/90 mb-8">
                Ein Systemwechsel ist eine Entscheidung für Jahrzehnte. Kontaktiere uns jetzt für einen unverbindlichen Vor-Ort-Termin und löse deinen persönlichen Gutschein ein.
              </p>

              <div class="bg-white text-slate-800 p-6 rounded-2xl w-full max-w-lg relative overflow-hidden shadow-lg mb-2 border-4 border-[#93c21c]">
                  <h2 class="text-[16pt] font-black text-[#74b2d4] mb-1 uppercase">Dein Aktions-Gutschein</h2>
                  <p class="text-[9pt] font-bold mb-5">Persönlich reserviert für dein Projekt in ${escapeHtml(d.ort)}</p>
                  <div class="flex justify-center gap-5 mb-5 text-[9pt] font-bold">
                    ${d.includeWp ? `<span class="flex items-center gap-1.5">${iconCheckCircle(16, "text-[#93c21c]")} 1.500 € WP-Rabatt</span>` : ''}
                    ${d.includeSolar ? `<span class="flex items-center gap-1.5">${iconCheckCircle(16, "text-[#93c21c]")} 1.000 € PV-Rabatt</span>` : ''}
                    ${(d.includeSolar && ergebnis.activeAuto) ? `<span class="flex items-center gap-1.5">${iconCheckCircle(16, "text-[#93c21c]")} 500 € Wallbox</span>` : ''}
                  </div>
                  <div class="bg-[#e3effb] p-3 rounded-xl border border-[#c0d8ea] inline-block">
                    <span class="text-[8pt] font-black uppercase text-slate-500 block mb-0.5">Dein Gesamtwert</span>
                    <span class="text-[20pt] font-black text-[#93c21c] leading-none">${formatMoney(ergebnis.gutscheinSumme)} €</span>
                  </div>
              </div>
              <p class="text-[8pt] text-white/60 mt-4">*Gültig bei Buchung eines Vor-Ort-Termins innerhalb von 14 Tagen nach Erstellung dieses Dokuments.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

    function renderApp() {
    return `
        ${renderProjectModal()}
        ${renderHeader()}
        ${state.stage === 'START' ? renderStart() : ''}
        ${state.stage === 'QUIZ' ? renderQuiz() : ''}
        ${state.stage === 'LOADING' ? renderLoading() : ''}
        ${state.stage === 'DASHBOARD' ? renderDashboardPlaceholder() : ''}
    `;
    }

   function renderPrint() {
    const printRoot = document.getElementById('print-root');
    if (!printRoot) return;

    if (state.stage !== 'DASHBOARD') {
        printRoot.innerHTML = '';
        return;
    }

    const ergebnis = calculateResults();
    printRoot.innerHTML = renderPrintReport(ergebnis);
    }

    function render() {
        // 1. Remember the active input and cursor position before re-rendering
        const active = document.activeElement;
        const activeBind = active ? active.getAttribute('data-bind') : null;
        let selStart = null;
        let selEnd = null;

        if (active && (active.type === 'text' || active.type === 'tel' || active.type === 'email')) {
            try {
            selStart = active.selectionStart;
            selEnd = active.selectionEnd;
            } catch(e) {}
        }

        // 2. Render the App
        const app = document.getElementById('app');
        app.innerHTML = renderApp();

        // 3. Put the focus and cursor back exactly where it was
        if (activeBind) {
            const newActive = document.querySelector(`[data-bind="${activeBind}"]`);
            if (newActive) {
            newActive.focus();
            if (selStart !== null && selEnd !== null) {
                try { newActive.setSelectionRange(selStart, selEnd); } catch(e){}
            }
            }
        }
        }
    /* ---------------------------------------------------------
       EVENTS
    --------------------------------------------------------- */

    function attachEvents() {
      dom.app.onclick = handleClick;
      dom.app.onchange = handleChange;
      dom.app.oninput = handleInput;
      dom.app.onfocusin = handleFocusIn;
      dom.app.onfocusout = handleFocusOut;
      dom.app.oncompositionstart = handleCompositionStart;
      dom.app.oncompositionend = handleCompositionEnd;
    }

    function handleClick(e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const action = btn.dataset.action;

        if (action === 'toggle-projects') {
            state.showProjectModal = true;
            render();
            return;
        }

        if (action === 'close-projects') {
            state.showProjectModal = false;
            render();
            return;
        }

        if (action === 'save-project') {
            saveProject(false);
            render();
            return;
        }

        if (action === 'save-project-as-new' || action === 'save-project-new') {
            saveProject(true);
            render();
            return;
        }

        if (action === 'load-project') {
            loadProjectById(btn.dataset.id);
            render();
            return;
        }

        if (action === 'delete-project') {
            deleteProjectById(btn.dataset.id);
            render();
            return;
        }

        if (action === 'new-project') {
            startNewProject();
            render();
            return;
        }
 
        if (action === 'open-pdf-preview') {
            state.showPdfPreview = true;
            render();
            return;
        }

        if (action === 'close-pdf-preview') {
            state.showPdfPreview = false;
            render();
            return;
        }

        if (action === 'download-pdf') {
            // Set state to spin the button inside the modal
            state.isGeneratingPDF = true;
            render(); 
            
            // Wait 100ms for DOM to update
            setTimeout(() => {
                const element = document.getElementById('pdf-export-wrapper');
                if (!element) {
                    state.isGeneratingPDF = false;
                    render();
                    return;
                }
                
                const opt = {
                    margin: 0, 
                    filename: `SolarAspekt_Fahrplan_${state.formData.standortPlz || 'Wirtschaftlichkeit'}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, windowWidth: 794, scrollY: 0, scrollX: 0 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };
                
                // Generate and download
                html2pdf().set(opt).from(element).save().then(() => {
                    state.isGeneratingPDF = false;
                    render(); // Stop spinner
                });
            }, 100);
            return;
        }
        

        if (action === 'toggle-include-wp') {
            state.formData.includeWp = btn.dataset.value === '1';
            render();
            return;
        }

        if (action === 'set-tab') {
            state.activeTab = btn.dataset.tab || 'FINANZEN';
            render();
            return;
        }

        if (action === 'set-years') {
            state.selectedYears = parseInt(btn.dataset.years, 10) || 20;
            render();
            return;
        }

        if (action === 'go-home') {
            state.stage = 'START';
            state.quizStep = 0;
            render();
            return;
        }

        if (action === 'start-quiz') {
            state.stage = 'QUIZ';
            state.quizStep = 0;
            render();
            return;
        }

        if (action === 'quiz-next') {
            if (state.quizStep === 0 && state.formData.standortPlz.length < 5) return;
            state.quizStep = Math.min(2, state.quizStep + 1);
            render();
            return;
        }

        if (action === 'quiz-prev') {
            state.quizStep = Math.max(0, state.quizStep - 1);
            render();
            return;
        }

        if (action === 'calculate') {
            handleCalculate();
            return;
        }

        if (action === 'open-contact') {
            state.showContactModal = true;
            state.contactSuccess = false;
            render();
            return;
        }

        if (action === 'close-contact') {
            state.showContactModal = false;
            state.contactSuccess = false;
            render();
            return;
        }

        if (action === 'submit-contact') {
            state.contactSuccess = true;
            render();
            return;
        }

        if (action === 'open-calculation') {
            state.showCalculationModal = true;
            render();
            return;
        }

        if (action === 'close-calculation') {
            state.showCalculationModal = false;
            render();
            return;
        }
        }

    function handleFocusIn(e) {
      const el = e.target.closest('[data-bind]');
      if (!el) return;
      activeInputMeta.key = el.dataset.bind;
    }

    function handleFocusOut() {
      activeInputMeta.key = null;
      activeInputMeta.isComposing = false;
    }

    function handleCompositionStart(e) {
      const el = e.target.closest('[data-bind]');
      if (!el) return;
      activeInputMeta.isComposing = true;
    }

    function handleCompositionEnd(e) {
      const el = e.target.closest('[data-bind]');
      if (!el) return;
      activeInputMeta.isComposing = false;
      // Finalize IME value
      handleInput(e);
    }

   function handleInput(e) {
      const input = e.target;
      const bind = input.dataset.bind;
      if (!bind) return;

      if (bind === 'projectNameInput') {
        state.newProjectName = input.value;
        return; // No render needed for project name
      }

      let value = input.type === 'checkbox' ? input.checked : input.value;

      if (bind.startsWith('formData.')) {
        const key = bind.replace('formData.', '');

        if (key === 'standortPlz') {
          value = String(value).replace(/\D/g, '').slice(0, 5);
          input.value = value; // Instantly update input visually
        }

        state.formData[key] = value;

        // Auto-calculate fields based on inputs
        if (key === 'personenAnzahl') {
          state.formData.stromverbrauch = String((parseInt(value || '1', 10) * 1200) + 500);
        }
        if (key === 'heizungsArt') {
          state.formData.heizPreis = String(ENERGIE[value].preis);
          state.formData.heizVerbrauch = value === 'Heizöl' ? '2500' : value === 'Pellets' ? '5000' : '20000';
        }

        // FIX: For basic contact fields, don't re-render the page at all. 
        // Just save to memory in the background.
        const textFields = ['vorname', 'nachname', 'email', 'telefon', 'strasse', 'hausnummer', 'ort'];
        if (textFields.includes(key)) {
            return; 
        }

        // FIX: For math numbers, delay the render by 400ms so it doesn't interrupt typing
        clearTimeout(window.renderDebounce);
        window.renderDebounce = setTimeout(() => render(), 400);
        return;
      }

      if (bind in state.formData) {
        state.formData[bind] = value;
        
        clearTimeout(window.renderDebounce);
        window.renderDebounce = setTimeout(() => render(), 400);
        return;
      }

      state[bind] = value;
    }
    function handleChange(e) {
      const el = e.target.closest('[data-bind]');
      if (!el) return;

      const bind = el.dataset.bind;
      let value = el.type === 'checkbox' ? el.checked : el.value;

      if (bind === 'formData.gebaeudeArt') {
        state.formData.gebaeudeArt = value;
        render();
        return;
      }

      if (bind === 'formData.heizungsArt') {
        state.formData.heizungsArt = value;
        state.formData.heizPreis = ENERGIE[value].preis.toString();
        state.formData.heizVerbrauch =
          value === 'Heizöl' ? '2500' :
          value === 'Pellets' ? '5000' :
          '20000';
        render();
        return;
      }

      if (bind === 'formData.wohneinheitenBewohnt') {
        state.formData.wohneinheitenBewohnt = value;
        if (toNumber(state.formData.eigentuemerUnter40k) > toNumber(value)) {
          state.formData.eigentuemerUnter40k = value;
        }
        render();
        return;
      }

      if (bind === 'formData.einkommenUnter40k' ||
          bind === 'formData.heizungsAlter' ||
          bind === 'formData.heizsystem') {
        setByPath(state, bind, value);
        render();
        return;
      }
    }

    /* ---------------------------------------------------------
       INIT
    --------------------------------------------------------- */

    loadProjects();
    attachEvents();
    loadProjectsFromStorage();
    render();
  </script>
</body>
</html>