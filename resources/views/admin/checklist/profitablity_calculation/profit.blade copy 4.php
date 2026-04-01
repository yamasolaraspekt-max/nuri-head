<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WERK STUDIO / SOLAR ASPEKT Energiekonzept</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { 
      font-family: 'Inter', sans-serif; 
      counter-reset: page-counter; 
    }

    /* Die Variablen werden nun dynamisch durch JS (updateThemeCSS) überschrieben */
    :root{
      --color-primary: #97937c;
      --color-secondary: #72436b;
      --color-inactive: #97937cb3;
    }

    /* Page Counter */
    .a4-page {
      counter-increment: page-counter;
    }
    .page-number::after {
      content: counter(page-counter);
    }

    @page { size: A4; margin: 0; }

    @media print {
      body {
        background-color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        margin: 0;
        padding: 0;
      }
      .no-print { display: none !important; }
      .a4-page {
        width: 210mm !important;
        height: 297mm !important;
        margin: 0 !important;
        padding: 15mm 20mm 25mm 20mm !important; /* Adjusted bottom padding for footer */
        page-break-after: always;
        box-shadow: none !important;
        border: none !important;
        overflow: hidden !important;
      }
      .a4-page:last-child { page-break-after: auto; }
    }

    @media screen {
      .a4-page {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto 2rem auto;
        padding: 20mm 20mm 25mm 20mm; /* Adjusted bottom padding for footer */
        background: white;
        box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        position: relative;
        overflow: hidden;
      }
    }

    .focus-ring:focus {
      outline: none;
      box-shadow: 0 0 0 2px var(--color-primary);
      border-color: transparent;
    }

    .animate-fade-in {
      animation: fadeIn .25s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(6px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .chart-wrap {
      position: relative;
      width: 100%;
      height: 100%;
    }

    .chart-wrap canvas {
      width: 100% !important;
      height: 100% !important;
    }

    .sidebar-transition {
      transition: transform 0.3s ease;
    }

    .rot-180 { transform: rotate(180deg); }

    .custom-scroll::-webkit-scrollbar { width: 8px; }
    .custom-scroll::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 999px;
    }

   .icon-stroke {
    width: 1em;
    height: 1em;
    display: block;
    flex-shrink: 0;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .icon-box {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .icon-box > svg {
    width: 100%;
    height: 100%;
  }
  </style>
</head>
<body class="bg-white text-slate-600">

  <div id="app"></div>

  <script>
    const backendCustomer = @json($customer ?? null);
    const backendProducts = @json($products ?? []);
    const backendPreset = @json($calculatorPreset ?? []);
    const backendFrontendConfig = @json($frontendConfig ?? null); // add this
    const backendMeta = {
      calculationId: @json($pageMeta['calculation_id'] ?? ($existingCalculation->id ?? null)),
      customerId: @json($pageMeta['customer_id'] ?? ($customer->customer_id ?? null)),
      alternativeId: @json($pageMeta['alternative_id'] ?? ($customer->alternative_id ?? null)),
      productId: @json($pageMeta['product_id'] ?? ($customer->product_id ?? null)),
      serviceId: @json($pageMeta['service_id'] ?? ($customer->service_id ?? null)),
      saveUrl: @json(route('profitability-calculations.save-report')),
    };
    const existingCalculation = @json($existingCalculation ?? null);
  </script>
  <script>
    // =========================================================
    // THEME & LOGO CONFIGURATION
    // =========================================================
    const THEMES = {
      'Werkstudio': {
        name: 'WERK STUDIO BAUKONZEPT',
        logo: "{{ asset('logo/werk-studio.png') }}",
        primary: '#97937c',       // Base/Title
        secondary: '#72436b',     // Subtitles
        inactive: '#97937cb3',    // 70% of primary for inactive
        bgLight: '#97937c1a',     // Light background tint
      },
      'Solar Aspekt': {
        name: 'SOLAR ASPEKT',
        logo: "{{ asset('logo/logo.png') }}",
        primary: '#93c21c',       // Base/Title
        secondary: '#74b2d4',     // Subtitles
        inactive: '#c0d8ea',      // Inactive/Ring chart empty
        bgLight: '#cfe09b',       // Alternate inactive/light
      }
    };

    function getActiveTheme() {
      return THEMES[state.config.company] || THEMES['Werkstudio'];
    }

    function updateThemeCSS() {
      const t = getActiveTheme();
      document.documentElement.style.setProperty('--color-primary', t.primary);
      document.documentElement.style.setProperty('--color-secondary', t.secondary);
      document.documentElement.style.setProperty('--color-inactive', t.inactive);
    }

    // =========================================================
    // ICONS (Lucide-like inline SVG helpers)
    // =========================================================
    const Icons = {
      sun: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>`,
      thermoSnow: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14 14.76V3a2 2 0 0 0-4 0v11.76a4 4 0 1 0 4 0Z"></path><path d="M9 17h6"></path><path d="M17 8l1 1 1-1"></path><path d="M17 14l1 1 1-1"></path></svg>`,
      zap: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"></path></svg>`,
      mapPin: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M12 22s7-4.35 7-12a7 7 0 1 0-14 0c0 7.65 7 12 7 12z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>`,
      info: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>`,
      home: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 9.5V21h14V9.5"></path></svg>`,
      users: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`,
      euro: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M4 10h10"></path><path d="M4 14h10"></path><path d="M14.5 6.5a5.5 5.5 0 1 0 0 11"></path></svg>`,
      checkSquare: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 12l2 2 4-4"></path></svg>`,
      checkCircle2: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>`,
      shieldCheck: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>`,
      printer: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v5a2 2 0 0 1-2 2h-2"></path><path d="M6 14h12v8H6z"></path></svg>`,
      arrowLeft: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>`,
      arrowRight: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>`,
      activity: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M22 12h-4l-3 9-6-18-3 9H2"></path></svg>`,
      leaf: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M11 20A7 7 0 0 1 4 13C4 7 9 4 20 4c0 11-3 16-9 16z"></path><path d="M11 20c1.5-5 4.5-8 9-11"></path></svg>`,
      trendingUp: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M22 7 13.5 15.5l-5-5L2 17"></path><path d="M16 7h6v6"></path></svg>`,
      alertTriangle: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94A2 2 0 0 0 22.18 18L13.71 3.86a2 2 0 0 0-3.42 0z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>`,
      x: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>`,
      sliders: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M4 21v-7"></path><path d="M4 10V3"></path><path d="M12 21v-9"></path><path d="M12 8V3"></path><path d="M20 21v-5"></path><path d="M20 12V3"></path><path d="M2 14h4"></path><path d="M10 8h4"></path><path d="M18 16h4"></path></svg>`,
      save: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><path d="M17 21v-8H7v8"></path><path d="M7 3v5h8"></path></svg>`,
      chevronDown: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="m6 9 6 6 6-6"></path></svg>`,
      lightbulb: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M12 2a7 7 0 0 0-4 12c.7.6 1 1.4 1 2h6c0-.6.3-1.4 1-2A7 7 0 0 0 12 2z"></path></svg>`,
      award: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="8" r="7"></circle><path d="M8.21 13.89 7 22l5-3 5 3-1.21-8.11"></path></svg>`,
      wrench: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-3-3 3-3z"></path></svg>`,
      star: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"></path></svg>`,
      battery: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="2" y="7" width="18" height="10" rx="2"></rect><path d="M22 11v2"></path></svg>`,
      car: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14 16H9m10 0h1a1 1 0 0 0 1-1v-3l-2-5a2 2 0 0 0-2-1H7a2 2 0 0 0-2 1l-2 5v3a1 1 0 0 0 1 1h1"></path><circle cx="6.5" cy="16.5" r="2.5"></circle><circle cx="17.5" cy="16.5" r="2.5"></circle></svg>`,
      network: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><path d="M10 6.5h4"></path><path d="M17.5 10v4"></path><path d="M7 10v8h7"></path></svg>`,
      checkCircle: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>`,
      tag: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M20 10 10 20 2 12V2h10l8 8z"></path><circle cx="7" cy="7" r="1"></circle></svg>`,
      piggyBank: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M19 5c1.5 0 2 1 2 2 0 1.5-1 2-2 2"></path><path d="M3 11a7 7 0 0 1 7-7h5a5 5 0 0 1 5 5v2a6 6 0 0 1-6 6H8l-2 3H4l1-3a6 6 0 0 1-2-4v-2z"></path><path d="M12 7v3"></path></svg>`,
      cpu: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="7" y="7" width="10" height="10" rx="2"></rect><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"></path></svg>`,
      infinity: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M18.18 8c-2.43 0-4.18 4-6.18 4s-3.75-4-6.18-4A3.82 3.82 0 0 0 2 11.82 3.82 3.82 0 0 0 5.82 15c2.43 0 4.18-4 6.18-4s3.75 4 6.18 4A3.82 3.82 0 0 0 22 11.18 3.82 3.82 0 0 0 18.18 8z"></path></svg>`,
      calculator: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><rect x="4" y="2" width="16" height="20" rx="2"></rect><path d="M8 6h8"></path><path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path></svg>`,
      thermometer: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M14 14.76V3a2 2 0 0 0-4 0v11.76a4 4 0 1 0 4 0Z"></path></svg>`,
      maximize: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M8 3H5a2 2 0 0 0-2 2v3"></path><path d="M16 3h3a2 2 0 0 1 2 2v3"></path><path d="M8 21H5a2 2 0 0 1-2-2v-3"></path><path d="M16 21h3a2 2 0 0 0 2-2v-3"></path></svg>`,
      droplet: () => `<svg viewBox="0 0 24 24" class="icon-stroke"><path d="M12 2s7 7 7 12a7 7 0 0 1-14 0c0-5 7-12 7-12z"></path></svg>`
    };

  function icon(name, classes = "w-4 h-4", color = "") {
    return `<span class="icon-box ${classes} ${color}">${Icons[name]()}</span>`;
  }

    // =========================================================
    // CONSTANTS
    // =========================================================
    const MONTHS = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

    const HGT_DISTRIBUTION = [0.18, 0.15, 0.12, 0.08, 0.04, 0.015, 0.015, 0.015, 0.03, 0.06, 0.10, 0.195];
    const PV_DISTRIBUTION = [0.03, 0.05, 0.08, 0.10, 0.12, 0.13, 0.13, 0.11, 0.10, 0.08, 0.04, 0.03];
    const HH_DISTRIBUTION = [0.095, 0.085, 0.085, 0.080, 0.075, 0.070, 0.070, 0.070, 0.080, 0.085, 0.095, 0.110];
    const EV_DISTRIBUTION = [0.09, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.08, 0.09, 0.10];
    const DAYLIGHT_RATIO = [0.35, 0.40, 0.50, 0.55, 0.60, 0.65, 0.65, 0.60, 0.50, 0.45, 0.35, 0.30];

    function num(val, fallback = 0) {
        if (val === null || val === undefined || val === '') return fallback;
        const n = Number(val);
        return Number.isFinite(n) ? n : fallback;
      }

      function str(val, fallback = '') {
        if (val === null || val === undefined) return fallback;
        return String(val);
      }

      function boolish(val, fallback = false) {
        if (typeof val === 'boolean') return val;
        if (val === 1 || val === '1' || val === 'true' || val === 'ja' || val === 'Ja') return true;
        if (val === 0 || val === '0' || val === 'false' || val === 'nein' || val === 'Nein') return false;
        return fallback;
      }
    // =========================================================
    // STATE
    // =========================================================
    const preset = backendPreset || {};
    const customerData = backendCustomer || {};
    const productList = Array.isArray(backendProducts) ? backendProducts : [];

    const customerFullName = [
      str(preset.vorname || customerData.first_name || ''),
      str(preset.nachname || customerData.last_name || '')
    ].filter(Boolean).join(' ').trim();

    const detectedCompany = 'Solar Aspekt'; // or decide dynamically

    const defaultRoofDirection = str(customerData.roof_direction || 'Süd');
    const defaultRoofType = str(customerData.roof_type || 'Ziegel');
    const defaultRoofPitch = num(customerData.roof_pitch, 35);

    const hasWallboxProduct = productList.some(p =>
      str(p.article_group).toLowerCase().includes('wallbox')
    );

    const hasSolarProduct = productList.some(p => {
      const name = str(p.article_group).toLowerCase();
      return name.includes('pv') || name.includes('photovoltaik') || name.includes('solar') || name.includes('speicher');
    });

    const hasWpProduct = productList.some(p => {
      const name = str(p.article_group).toLowerCase();
      return name.includes('wärmepumpe') || name.includes('waermepumpe') || /\bwp\b/.test(name);
    });

    const state = {
      view: 'dashboard', // or 'wizard' if you still want editing first
      wizardStep: 1,
      isSidebarOpen: false,
      sidebarSections: {
        kunde: false,
        dach: false,
        altsystem: false,
        kaminSolar: false,
        preise: false,
        investitionen: false
      },
      config: (typeof backendFrontendConfig !== 'undefined' && backendFrontendConfig)
      ? structuredClone(backendFrontendConfig)
      : { 
        company: 'Solar Aspekt',
        modulePV: backendPreset.includeSolar ?? hasSolarProduct,
        moduleWP: backendPreset.includeWp ?? hasWpProduct,
        moduleWB: backendPreset.includeWallbox ?? hasWallboxProduct,

        name: `${backendPreset.vorname ?? ''} ${backendPreset.nachname ?? ''}`.trim() || customerFullName || 'Kunde',
        gebaeudeArt: backendPreset.gebaeudeArt || 'Einfamilienhaus',
        wohneinheiten: Number(backendPreset.wohneinheitenGesamt ?? 1),
        selbstbewohnteWE: Number(backendPreset.wohneinheitenBewohnt ?? 1),
        weUnter40k: Number(backendPreset.eigentuemerUnter40k ?? 0),
        plz: backendPreset.standortPlz || '',

        dachseiten: [
          {
            id: 1,
            ausrichtung: defaultRoofDirection,
            neigung: defaultRoofPitch,
            eindeckung: defaultRoofType,
            eindeckungTyp: '',
            customKwp: backendPreset.customPvSize || ''
          }
        ],

        heizungArt: backendPreset.heizungsArt === 'Heizöl' ? 'Öl'
          : backendPreset.heizungsArt === 'Pellets' ? 'Holz / Pellets'
          : backendPreset.heizungsArt === 'Nachtspeicher' ? 'Nachtspeicher'
          : 'Gas',

        heizungAlter: Number(backendPreset.heizungsAlter ?? 20),
        heizVerbrauch: Number(backendPreset.heizVerbrauch ?? 20000),
        heizSystem: backendPreset.heizsystem || 'Heizkörper',
        warmwasserArt: 'Zentral',
        personen: Number(backendPreset.personenAnzahl ?? 3),
        zirkulation: false,

        rohrHeizungMaterial: 'Kupfer',
        rohrHeizungDN: '28',
        rohrWWMaterial: 'Kupfer',
        rohrWWDN: '18',
        rohrZirkulationMaterial: 'Kupfer',
        rohrZirkulationDN: '15',

        kaminVorhanden: false,
        kaminWeiterBetreiben: false,
        holzVerbrauch: 3,
        preisHolz: 120,

        solarthermieVorhanden: false,
        solarthermieWeiterBetreiben: false,
        solarthermieArt: 'Flachkollektor',
        solarKollektoren: 2,

        hhStrom: Number(backendPreset.stromverbrauch ?? 4000),
        autoArt: 'Verbrenner',
        fahrleistung: Number(backendPreset.kmProJahr ?? 15000),
        verbrennerVerbrauch: 7,
        preisSprit: Number(backendPreset.spritPreis ?? 1.80),

        preisStrom: Number(backendPreset.evuPreis ?? 0.35),
        preisEinspeisung: 0.08,
        preisHeizMedium: Number(backendPreset.heizPreis ?? 0.11),
        inflationRate: 3.0,
        wartungOld: Number(backendPreset.wartungAlt_pa_input ?? 300),
        netzentgelt: 0.10,

        costWP: Number(backendPreset.customWpKosten ?? 30000),
        costPV: Number(backendPreset.customPvKosten ?? 16000),
        costBattery: Number(backendPreset.customSpeicherKosten ?? 8000),
        costWallbox: Number(backendPreset.customWallboxKosten ?? 1500),

        customWpKw: backendPreset.customWpSize || '',
        customPvKwp: backendPreset.customPvSize || '',
        customBatteryKwh: backendPreset.customSpeicherSize || '',
        customJAZ: backendPreset.customJaz || '',

        discountWP: 1000,
        discountPV: 750,
        discountBattery: 250,
        discountWallbox: 150,

        extraGrantWP: backendPreset.wpZusatzFoerderSumme || '',
        extraGrantPV: backendPreset.pvZusatzFoerderSumme || '',
        extraGrantBattery: backendPreset.speicherZusatzFoerderSumme || '',
        extraGrantWallbox: backendPreset.wallboxZusatzFoerderSumme || '',

        extraGrantSourceWP: backendPreset.wpZusatzFoerderName || '',
        extraGrantSourcePV: backendPreset.pvZusatzFoerderName || '',
        extraGrantSourceBattery: backendPreset.speicherZusatzFoerderName || '',
        extraGrantSourceWallbox: backendPreset.wallboxZusatzFoerderName || ''
      }
    };

    const charts = {};

    // =========================================================
    // HELPERS
    // =========================================================
    function getRegionalFactors(plzStr) {
      const firstDigit = parseInt(String(plzStr).charAt(0)) || 5;
      let pvBaseFactor = 950;
      let wpFactor = 1.0;

      if (firstDigit >= 8) {
        pvBaseFactor = 1050;
        wpFactor = 1.05;
      } else if (firstDigit >= 6) {
        pvBaseFactor = 1000;
        wpFactor = 1.0;
      } else if (firstDigit <= 2) {
        pvBaseFactor = 900;
        wpFactor = 0.95;
      }

      return { pvBaseFactor, wpFactor };
    }

    function getOrientationFactor(ausrichtung) {
      switch(ausrichtung) {
        case 'Süd': return 1.0;
        case 'Süd-Ost':
        case 'Süd-West': return 0.95;
        case 'Ost':
        case 'West': return 0.85;
        case 'Nord-Ost':
        case 'Nord-West': return 0.65;
        case 'Nord': return 0.55;
        default: return 1.0;
      }
    }

    function getKlimaDaten(plzStr) {
      const start = parseInt(String(plzStr).charAt(0)) || 5;
      if (start === 8 || start === 9) return { nat: -14, hgt: 4000, vbh: 2200 };
      if (start === 1 || start === 2) return { nat: -10, hgt: 3200, vbh: 1900 };
      if (start === 0 || start === 3) return { nat: -12, hgt: 3600, vbh: 2100 };
      if (start === 4 || start === 5) return { nat: -10, hgt: 3000, vbh: 1850 };
      return { nat: -12, hgt: 3500, vbh: 2000 };
    }

    function getHeizEinheit(art) {
      if (art === 'Öl') return 'Liter';
      if (art === 'Holz / Pellets') return 'Tonnen';
      return 'kWh';
    }

    function getHeizMediumKwh(menge, art) {
      if (art === 'Öl') return menge * 10;
      if (art === 'Holz / Pellets') return menge * 4800;
      return menge;
    }

    function formatDE(num, decimals = 0) {
      return Number(num || 0).toLocaleString('de-DE', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      });
    }

    function clamp(val, min, max) {
      return Math.max(min, Math.min(max, val));
    }

    function destroyChart(id) {
      if (charts[id]) {
        charts[id].destroy();
        delete charts[id];
      }
    }

    // =========================================================
    // CALCULATIONS
    // =========================================================
    function getDerivedParams() {
      const config = state.config;
      const klima = getKlimaDaten(config.plz);

      const activeHeizVerbrauch = config.moduleWP ? config.heizVerbrauch : 0;
      const activeFahrleistung = config.moduleWB ? config.fahrleistung : 0;
      const activeKamin = config.moduleWP ? config.kaminVorhanden : false;
      const activeSolar = config.moduleWP ? config.solarthermieVorhanden : false;

      let systemVerlust = config.heizungAlter > 20 ? 0.20 : (config.heizungAlter > 10 ? 0.15 : 0.10);
      let thermischHauptsystem = getHeizMediumKwh(activeHeizVerbrauch, config.heizungArt) * (1 - systemVerlust);

      let thermischKaminPotenziell = activeKamin ? config.holzVerbrauch * 2100 * 0.75 : 0;
      let thermischSolarPotenziell = activeSolar ? config.solarKollektoren * 2.5 * (config.solarthermieArt === 'Flachkollektor' ? 350 : 500) : 0;

      const gesamtWaermeBedarfHaus = thermischHauptsystem + thermischKaminPotenziell + thermischSolarPotenziell;
      const berechneteHeizlast = (gesamtWaermeBedarfHaus / klima.vbh).toFixed(1);
        
      let empfohleneWpKw = config.moduleWP ? Math.ceil(gesamtWaermeBedarfHaus / klima.vbh) : 0;
      let wpLeistungKW = config.moduleWP ? (config.customWpKw !== '' ? Number(config.customWpKw) : empfohleneWpKw) : 0;

      const bivalenzpunkt = klima.nat >= -10 ? -5 : -7;

      let wwBedarfThermisch = (config.moduleWP && config.warmwasserArt === 'Zentral') ? config.personen * 800 : 0;
      if (config.moduleWP && config.zirkulation && config.warmwasserArt === 'Zentral') wwBedarfThermisch += 600;

      let heizWärmeBedarf = Math.max(0, thermischHauptsystem - wwBedarfThermisch);

      let heizWärmeNachAbzug = heizWärmeBedarf;
      let wwBedarfNachAbzug = wwBedarfThermisch;

      if (config.moduleWP && config.kaminWeiterBetreiben) heizWärmeNachAbzug -= thermischKaminPotenziell;

      if (config.moduleWP && config.solarthermieWeiterBetreiben) {
        let solarRest = thermischSolarPotenziell;
        let wwAbzug = Math.min(wwBedarfNachAbzug, solarRest);
        wwBedarfNachAbzug -= wwAbzug;
        solarRest -= wwAbzug;
        heizWärmeNachAbzug -= solarRest;
      }

      heizWärmeNachAbzug = Math.max(0, heizWärmeNachAbzug);
      wwBedarfNachAbzug = Math.max(0, wwBedarfNachAbzug);

      let copSH = config.heizSystem === 'Fußbodenheizung' ? 4.2 : (config.heizSystem === 'Beides' ? 3.8 : 3.2);
      let copWW = 3.0;

      let wpStromHeizung = heizWärmeNachAbzug / copSH;
      let wpStromWW = wwBedarfNachAbzug / copWW;
      let berechneterWpStrombedarf = Math.round(wpStromHeizung + wpStromWW);

      let realeWpWaermeBedarf = heizWärmeNachAbzug + wwBedarfNachAbzug;
      let berechneteJaz = berechneterWpStrombedarf > 0 ? (realeWpWaermeBedarf / berechneterWpStrombedarf).toFixed(2) : copSH.toFixed(2);

      let jaz = config.customJAZ !== '' ? Number(config.customJAZ).toFixed(2) : berechneteJaz;
      let cop = Number(jaz);

      let wpStrombedarf = config.moduleWP ? (config.customJAZ !== '' ? Math.round(realeWpWaermeBedarf / cop) : berechneterWpStrombedarf) : 0;
      let umweltEnergie = config.moduleWP ? (realeWpWaermeBedarf - wpStrombedarf) : 0;

      const evStrombedarf = config.moduleWB ? Math.round(activeFahrleistung * 0.2) : 0;
      const gesamtStrombedarf = config.hhStrom + wpStrombedarf + evStrombedarf;

      const { pvBaseFactor, wpFactor } = getRegionalFactors(config.plz);
      const avgYieldFactor = config.dachseiten.reduce((acc, curr) => acc + getOrientationFactor(curr.ausrichtung), 0) / config.dachseiten.length;
      const effectiveYieldPvKwp = config.modulePV ? (pvBaseFactor * avgYieldFactor) : 0;

      const hasOst = config.dachseiten.some(d => d.ausrichtung.includes('Ost'));
      const hasWest = config.dachseiten.some(d => d.ausrichtung.includes('West'));
      const hasSued = config.dachseiten.some(d => d.ausrichtung === 'Süd');
      const isEastWestProfile = hasOst && hasWest;

      let baseBattery = gesamtStrombedarf / 1000;
      let batterySpreadFactor = isEastWestProfile ? 0.8 : (hasSued ? 1.2 : 1.0);
      let empfohleneBatterie = config.modulePV ? Math.max(5, Math.round(baseBattery * batterySpreadFactor)) : 0;
      let batteryCapacity = config.modulePV ? (config.customBatteryKwh !== '' ? Number(config.customBatteryKwh) : empfohleneBatterie) : 0;

      const pvDimensionierungsFaktor = 1.35;
      let empfohlenePv = config.modulePV ? Math.max(3, Math.round((gesamtStrombedarf * pvDimensionierungsFaktor) / effectiveYieldPvKwp * 10) / 10) : 0;

      let manualPvKwpSum = 0;
      config.dachseiten.forEach(d => {
        if (d.customKwp && d.customKwp !== '') manualPvKwpSum += Number(d.customKwp);
      });

      let pvKwp = config.modulePV ? (manualPvKwpSum > 0 ? manualPvKwpSum : (config.customPvKwp !== '' ? Number(config.customPvKwp) : Math.ceil(empfohlenePv))) : 0;

      const distributedDachseiten = config.dachseiten.map(d => ({
        ...d,
        calculatedKwp: config.modulePV ? ((d.customKwp && d.customKwp !== '') ? Number(d.customKwp) : Number((empfohlenePv / config.dachseiten.length).toFixed(1))) : 0
      }));

      const verbrennerLiterKosten = (config.moduleWB && config.autoArt === 'Verbrenner')
        ? (activeFahrleistung / 100) * config.verbrennerVerbrauch * config.preisSprit
        : 0;

      const verbrennerKwhEquivalent = (config.moduleWB && config.autoArt === 'Verbrenner')
        ? (activeFahrleistung / 100) * config.verbrennerVerbrauch * 9
        : 0;

      return {
        klima, wpStrombedarf, cop, jaz, berechneteJaz, copSH, copWW, evStrombedarf, gesamtStrombedarf, realeWpWaermeBedarf, umweltEnergie,
        wpLeistungKW, pvKwp, batteryCapacity, empfohleneWpKw, empfohlenePv, empfohleneBatterie, berechneteHeizlast, gesamtWaermeBedarfHaus, bivalenzpunkt,
        verbrennerLiterKosten, verbrennerKwhEquivalent, wpFactor, effectiveYieldPvKwp, batterySpreadFactor,
        isEastWestProfile, hasSued, avgYieldFactor, distributedDachseiten, manualPvKwpSum,
        kaminKosten: activeKamin ? config.holzVerbrauch * config.preisHolz : 0,
        thermischKaminPotenziell, thermischSolarPotenziell, thermischHauptsystem, heizVerbrauchKwh: getHeizMediumKwh(activeHeizVerbrauch, config.heizungArt),
        systemVerlust, wwBedarfThermisch, heizWärmeBedarf, wwBedarfNachAbzug, heizWärmeNachAbzug
      };
    }

    function getSimulation(derivedParams) {
      const config = state.config;
      const theme = getActiveTheme();

      const kwp = Number(derivedParams?.pvKwp || 0);
      const batteryCapacity = Number(derivedParams?.batteryCapacity || 0);
      const wpJahresVerbrauch = Number(derivedParams?.wpStrombedarf || 0);
      const wpFactor = Number(derivedParams?.wpFactor || 1);
      const evStrombedarf = Number(derivedParams?.evStrombedarf || 0);

      const hhAutarkieFixed = 70;
      const wpAutarkieFixed = 55;
      const evAutarkieFixed = 50;

      const hhDeckung = config.modulePV ? Number(config.hhStrom || 0) * (hhAutarkieFixed / 100) : 0;
      const wpDeckung = (config.modulePV && config.moduleWP) ? wpJahresVerbrauch * (wpAutarkieFixed / 100) : 0;
      const evDeckung = (config.modulePV && config.moduleWB && Number(config.fahrleistung || 0) > 0)
        ? evStrombedarf * (evAutarkieFixed / 100)
        : 0;

      const fixedTotalDeckung = hhDeckung + wpDeckung + evDeckung;

      let totalPV = 0;
      let totalBedarf = 0;
      let totalDirekt = 0;
      let totalBatterie = 0;
      let totalNetzbezug = 0;
      let totalNetzeinspeisung = 0;

      const seasonalAgg = {
        Winter:   { name: 'Winter',   Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
        Frühling: { name: 'Frühling', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
        Sommer:   { name: 'Sommer',   Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
        Herbst:   { name: 'Herbst',   Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 }
      };

      const monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

      const getSeasonByMonthIndex = (index) => {
        if (index === 11 || index <= 1) return 'Winter';
        if (index >= 2 && index <= 4) return 'Frühling';
        if (index >= 5 && index <= 7) return 'Sommer';
        return 'Herbst';
      };

      const chartData = MONTHS.map((month, index) => {
        const days = monthDays[index];

        const pvErtragMo = config.modulePV
          ? kwp * Number(derivedParams.effectiveYieldPvKwp || 0) * PV_DISTRIBUTION[index]
          : 0;

        const hhBedarfMo = Number(config.hhStrom || 0) * HH_DISTRIBUTION[index];
        const wpBedarfMo = config.moduleWP
          ? wpJahresVerbrauch * wpFactor * HGT_DISTRIBUTION[index]
          : 0;
        const evBedarfMo = config.moduleWB
          ? evStrombedarf * EV_DISTRIBUTION[index]
          : 0;

        const gesamtBedarfMo = hhBedarfMo + wpBedarfMo + evBedarfMo;

        const hhTag = (hhBedarfMo / days) * DAYLIGHT_RATIO[index];
        const wpTag = (wpBedarfMo / days) * DAYLIGHT_RATIO[index];
        const evTag = (evBedarfMo / days) * 0.20;

        const bedarfTagDaily = hhTag + wpTag + evTag;
        const bedarfNachtDaily = Math.max(0, (gesamtBedarfMo / days) - bedarfTagDaily);

        const pvDaily = pvErtragMo / days;
        const direktDaily = Math.max(0, Math.min(pvDaily, bedarfTagDaily));
        const chargeDaily = Math.max(0, Math.min(pvDaily - direktDaily, batteryCapacity));
        const dischargeDaily = Math.max(0, Math.min(chargeDaily * 0.9, bedarfNachtDaily));

        const direktDeckung = Math.round(direktDaily * days);
        const batterieLadung = Math.round(chargeDaily * days);
        const batterieDeckung = Math.round(dischargeDaily * days);

        const gesamtDeckungMo = direktDeckung + batterieDeckung;
        const netzbezug = Math.max(0, Math.round(gesamtBedarfMo - gesamtDeckungMo));
        const netzeinspeisung = Math.max(0, Math.round(pvErtragMo - direktDeckung - batterieLadung));

        const season = getSeasonByMonthIndex(index);

        seasonalAgg[season].Solarertrag += pvErtragMo;
        seasonalAgg[season].Gesamtbedarf += gesamtBedarfMo;
        seasonalAgg[season].DirektDeckung += direktDeckung;
        seasonalAgg[season].BatterieDeckung += batterieDeckung;
        seasonalAgg[season].Netzbezug += netzbezug;
        seasonalAgg[season].NetzeinspeisungNeg -= netzeinspeisung;

        totalPV += pvErtragMo;
        totalBedarf += gesamtBedarfMo;
        totalDirekt += direktDeckung;
        totalBatterie += batterieDeckung;
        totalNetzbezug += netzbezug;
        totalNetzeinspeisung += netzeinspeisung;

        return {
          name: month,
          Solarertrag: Math.round(pvErtragMo),
          Gesamtbedarf: Math.round(gesamtBedarfMo),
          DirektDeckung: direktDeckung,
          BatterieDeckung: batterieDeckung,
          Netzbezug: netzbezug,
          BatterieLadungNeg: -batterieLadung,
          NetzeinspeisungNeg: -netzeinspeisung,
          GesamtDeckung: gesamtDeckungMo
        };
      });

      const seasonalData = ['Winter', 'Frühling', 'Sommer', 'Herbst'].map((seasonKey) => {
        const item = seasonalAgg[seasonKey];
        const totalSeasonDeckung = item.DirektDeckung + item.BatterieDeckung;

        const calcSeasonAutarkie = item.Gesamtbedarf > 0
          ? Math.round((totalSeasonDeckung / item.Gesamtbedarf) * 100)
          : (config.modulePV ? 100 : 0);

        return {
          ...item,
          Solarertrag: Math.round(item.Solarertrag),
          Gesamtbedarf: Math.round(item.Gesamtbedarf),
          DirektDeckung: Math.round(item.DirektDeckung),
          BatterieDeckung: Math.round(item.BatterieDeckung),
          Netzbezug: Math.round(item.Netzbezug),
          NetzeinspeisungNeg: Math.round(item.NetzeinspeisungNeg),
          autarkie: config.modulePV ? Math.min(calcSeasonAutarkie, 98) : 0
        };
      });

      let fossilFactor = 0.202;
      if (config.heizungArt === 'Öl') fossilFactor = 0.266;
      if (config.heizungArt === 'Holz / Pellets') fossilFactor = 0.02;
      if (config.heizungArt === 'Nachtspeicher' || config.heizungArt === 'Stromdirektheizung') fossilFactor = 0.4;

      const activeHeizVerbrauchKwh = config.moduleWP ? Number(derivedParams.heizVerbrauchKwh || 0) : 0;
      const activeEvFossilCo2 = (config.moduleWB && config.autoArt === 'Verbrenner')
        ? ((Number(config.fahrleistung || 0) / 100) * Number(config.verbrennerVerbrauch || 0) * 2.37)
        : 0;
      const activeKaminCo2 = (config.moduleWP && config.kaminVorhanden)
        ? Number(config.holzVerbrauch || 0) * 2100 * 0.02
        : 0;

      const oldCo2 = (
        (activeHeizVerbrauchKwh * fossilFactor) +
        (Number(config.hhStrom || 0) * 0.4) +
        activeEvFossilCo2 +
        activeKaminCo2
      );

      const finalNetzbezug = Math.max(0, totalBedarf - fixedTotalDeckung);
      const newCo2 = finalNetzbezug * 0.4;

      let co2SavingsYear = (oldCo2 - newCo2) / 1000;
      if (co2SavingsYear < 0) co2SavingsYear = 0;

      const activeVerbrennerKwhEquivalent = config.moduleWB
        ? Number(derivedParams.verbrennerKwhEquivalent || 0)
        : 0;

      const activeThermischKaminPotenziell = config.moduleWP
        ? Number(derivedParams.thermischKaminPotenziell || 0)
        : 0;

      const oldEnergyKwh =
        activeHeizVerbrauchKwh +
        Number(config.hhStrom || 0) +
        activeVerbrennerKwhEquivalent +
        activeThermischKaminPotenziell;

      const energeticSavingsKwh = Math.round(oldEnergyKwh - finalNetzbezug);

      const bedarfsMix = [
        {
          name: 'Haushalt',
          value: Number(config.hhStrom || 0),
          fill: theme.inactive
        }
      ];

      if (config.moduleWP) {
        bedarfsMix.push({
          name: 'Wärmepumpe',
          value: Number(derivedParams.wpStrombedarf || 0),
          fill: theme.secondary
        });
      }

      if (config.moduleWB && Number(config.fahrleistung || 0) > 0) {
        bedarfsMix.push({
          name: 'E-Auto',
          value: Number(derivedParams.evStrombedarf || 0),
          fill: theme.primary
        });
      }

      const simTotalDeckung = Math.max(1, totalDirekt + totalBatterie);
      const scale = fixedTotalDeckung / simTotalDeckung;

      const fixedTotalDirekt = totalDirekt * scale;
      const fixedTotalBatterie = totalBatterie * scale;
      const newTotalNetzeinspeisung = Math.max(0, totalPV - fixedTotalDeckung);

      const totalPVRounded = Math.round(totalPV);
      const totalBedarfRounded = Math.round(totalBedarf);
      const totalNetzbezugRounded = Math.round(finalNetzbezug);
      const totalNetzeinspeisungRounded = Math.round(newTotalNetzeinspeisung);
      const totalDirektRounded = Math.round(fixedTotalDirekt);
      const totalBatterieRounded = Math.round(fixedTotalBatterie);

      const autarkie = (config.modulePV && totalBedarf > 0)
        ? Math.min(Math.round((fixedTotalDeckung / totalBedarf) * 100), 99)
        : 0;

      const eigenverbrauchQuote = (config.modulePV && totalPV > 0)
        ? Math.min(Math.round((fixedTotalDeckung / totalPV) * 100), 99)
        : 0;

      return {
        chartData,
        seasonalData,
        bedarfsMix,
        kpis: {
          totalPV: totalPVRounded,
          totalBedarf: totalBedarfRounded,
          totalNetzbezug: totalNetzbezugRounded,
          totalNetzeinspeisung: totalNetzeinspeisungRounded,
          totalDirekt: totalDirektRounded,
          totalBatterie: totalBatterieRounded,
          autarkie,
          eigenverbrauchQuote,
          hhDeckung: Math.round(hhDeckung),
          wpDeckung: Math.round(wpDeckung),
          evDeckung: Math.round(evDeckung),
          hhAutarkie: config.modulePV ? hhAutarkieFixed : 0,
          wpAutarkie: config.modulePV ? wpAutarkieFixed : 0,
          evAutarkie: (config.modulePV && config.moduleWB && Number(config.fahrleistung || 0) > 0) ? evAutarkieFixed : 0,
          spezErtrag: Number(derivedParams.effectiveYieldPvKwp || 0),
          oldEnergyKwh: Math.round(oldEnergyKwh),
          energeticSavingsKwh: Math.max(0, energeticSavingsKwh)
        },
        co2: {
          year: co2SavingsYear.toFixed(1),
          tenYears: (co2SavingsYear * 10).toFixed(1),
          twentyYears: (co2SavingsYear * 20).toFixed(1),
          thirtyYears: (co2SavingsYear * 30).toFixed(1),
          trees: Math.round(co2SavingsYear * 80),
          forestArea: Math.round(co2SavingsYear * 1250),
          oldKg: Math.round(oldCo2),
          newKg: Math.round(newCo2),
          savedKg: Math.round(Math.max(0, oldCo2 - newCo2))
        }
      };
    }

    function getFinance(derivedParams, kpis) {
      const config = state.config;

      const cWP = config.moduleWP ? config.costWP : 0;
      const cPV = config.modulePV ? config.costPV : 0;
      const cBat = config.modulePV ? config.costBattery : 0;
      const cWB = config.moduleWB ? config.costWallbox : 0;

      const isOldFossil = ['Öl', 'Kohle', 'Nachtspeicher'].includes(config.heizungArt) ||
        (['Gas', 'Holz / Pellets'].includes(config.heizungArt) && config.heizungAlter >= 20);

      const grundFoerderung = 30;
      const effizienzBonus = 5;
      const klimaBonus = isOldFossil ? 20 : 0;
      const einkommenBonus = 30;

      let weDeckelung = 0;
      if (config.wohneinheiten === 1) weDeckelung = 30000;
      else if (config.wohneinheiten <= 6) weDeckelung = 30000 + (config.wohneinheiten - 1) * 15000;
      else weDeckelung = 30000 + (5 * 15000) + (config.wohneinheiten - 6) * 8000;

      const effectiveWPCost = Math.max(0, cWP - (Number(config.discountWP) || 0));
      const foerderfaehigeKostenWP = Math.min(effectiveWPCost, weDeckelung);
      const costPerWE = foerderfaehigeKostenWP / config.wohneinheiten;

      const rentedWE = config.wohneinheiten - config.selbstbewohnteWE;
      const ownerNoLowIncWE = config.selbstbewohnteWE - config.weUnter40k;
      const ownerLowIncWE = config.weUnter40k;

      const baseProzent = grundFoerderung + effizienzBonus;
      const ownerNoLowIncProzent = Math.min(70, baseProzent + klimaBonus);
      const ownerLowIncProzent = Math.min(70, baseProzent + klimaBonus + einkommenBonus);

      const kfwZuschuss = config.moduleWP ? Math.round(
        costPerWE * ((rentedWE * (baseProzent / 100)) + (ownerNoLowIncWE * (ownerNoLowIncProzent / 100)) + (ownerLowIncWE * (ownerLowIncProzent / 100)))
      ) : 0;

      const maxZuschussProzent = foerderfaehigeKostenWP > 0
        ? Math.round((kfwZuschuss / foerderfaehigeKostenWP) * 100)
        : 0;

      const isKombiBonusActive = config.moduleWP && config.modulePV && config.moduleWB && cWP > 0 && cPV > 0 && cBat > 0 && cWB > 0;

      const discountWPNum = (isKombiBonusActive && config.moduleWP) ? (Number(config.discountWP) || 0) : 0;
      const discountPVNum = (isKombiBonusActive && config.modulePV) ? (Number(config.discountPV) || 0) : 0;
      const discountBatteryNum = (isKombiBonusActive && config.modulePV) ? (Number(config.discountBattery) || 0) : 0;
      const discountWallboxNum = (isKombiBonusActive && config.moduleWB) ? (Number(config.discountWallbox) || 0) : 0;

      const extraGrantWPNum = config.moduleWP ? (Number(config.extraGrantWP) || 0) : 0;
      const extraGrantPVNum = config.modulePV ? (Number(config.extraGrantPV) || 0) : 0;
      const extraGrantBatteryNum = config.modulePV ? (Number(config.extraGrantBattery) || 0) : 0;
      const extraGrantWallboxNum = config.moduleWB ? (Number(config.extraGrantWallbox) || 0) : 0;

      const totalInvest = cWP + cPV + cBat + cWB;
      const totalDiscount = discountWPNum + discountPVNum + discountBatteryNum + discountWallboxNum;
      const totalExtraGrant = extraGrantWPNum + extraGrantPVNum + extraGrantBatteryNum + extraGrantWallboxNum;
      const totalFoerderung = kfwZuschuss + totalExtraGrant;

      const nettoWP = cWP - discountWPNum - extraGrantWPNum - kfwZuschuss;
      const nettoPV = cPV - discountPVNum - extraGrantPVNum;
      const nettoBattery = cBat - discountBatteryNum - extraGrantBatteryNum;
      const nettoWallbox = cWB - discountWallboxNum - extraGrantWallboxNum;

      const nettoInvest = nettoWP + nettoPV + nettoBattery + nettoWallbox;

      const effPvCost = cPV - discountPVNum - extraGrantPVNum;
      const effBatCost = cBat - discountBatteryNum - extraGrantBatteryNum;
      const lcoe = (config.modulePV && kpis.totalPV > 0) ? ((effPvCost + effBatCost) / (kpis.totalPV * 30)).toFixed(2) : '0.00';

      const hhKostenOhne = Math.round(config.hhStrom * config.preisStrom);
      const wpKostenOhne = config.moduleWP ? Math.round(derivedParams.wpStrombedarf * config.preisStrom) : 0;
      const evKostenOhne = config.moduleWB ? Math.round(derivedParams.evStrombedarf * config.preisStrom) : 0;

      const hhNetz = Math.max(0, config.hhStrom - kpis.hhDeckung);
      const wpNetz = Math.max(0, derivedParams.wpStrombedarf - kpis.wpDeckung);
      const evNetz = Math.max(0, derivedParams.evStrombedarf - kpis.evDeckung);

      const evOldCost = config.moduleWB ? (config.autoArt === 'Verbrenner'
        ? derivedParams.verbrennerLiterKosten
        : (config.fahrleistung > 0 ? (config.fahrleistung / 100) * 20 * config.preisStrom : 0)) : 0;

      const heizkostenOld = config.moduleWP ? (config.heizVerbrauch * config.preisHeizMedium) : 0;
      const kaminCostOld = (config.moduleWP && config.kaminVorhanden) ? derivedParams.kaminKosten : 0;
      const stromCostOld = hhKostenOhne;

      const costOldBase = heizkostenOld + stromCostOld + config.wartungOld + evOldCost + kaminCostOld;

      const hasSteuVE = (config.moduleWP && derivedParams.wpStrombedarf > 0) || (config.moduleWB && cWB > 0);
      const steuVeBedarfAllElectric = derivedParams.wpStrombedarf + derivedParams.evStrombedarf;
      const ersparnis14aAllElectric = hasSteuVE
        ? Math.round(Math.max(160, steuVeBedarfAllElectric * config.netzentgelt * 0.6))
        : 0;

      const steuVeNetz = wpNetz + evNetz;
      const ersparnis14a = hasSteuVE
        ? Math.round(Math.max(160, steuVeNetz * config.netzentgelt * 0.6))
        : 0;

      const futureKaminCosts = (config.moduleWP && config.kaminVorhanden && config.kaminWeiterBetreiben) ? derivedParams.kaminKosten : 0;
      const activeHeizkosten = config.moduleWP ? 0 : heizkostenOld;
      const activeEvCost = config.moduleWB ? 0 : evOldCost;

      const costAllElectricBase = (derivedParams.gesamtStrombedarf * config.preisStrom) + config.wartungOld - ersparnis14aAllElectric + futureKaminCosts + activeHeizkosten + activeEvCost;
      const costNewBase = (kpis.totalNetzbezug * config.preisStrom) - (kpis.totalNetzeinspeisung * config.preisEinspeisung) - ersparnis14a + futureKaminCosts + activeHeizkosten + activeEvCost;

      const ersparnisJahr1 = Math.round(costOldBase - costNewBase);
      const ersparnisNurElektrisch = Math.round(costOldBase - costAllElectricBase);
      const ersparnisDurchPV = Math.round(costAllElectricBase - costNewBase);

      const finUnabhProzent = costOldBase > 0 ? Math.round((ersparnisJahr1 / costOldBase) * 100) : 0;

      const cashflow = [];
      let cumulativeCashflow = -nettoInvest;
      let cumulativeErsparnis = 0;

      let oldCostCumulative10 = 0, oldCostCumulative20 = 0, oldCostCumulative30 = 0;
      let electricCostCumulative10 = 0, electricCostCumulative20 = 0, electricCostCumulative30 = 0;
      let newCostCumulative10 = 0, newCostCumulative20 = 0, newCostCumulative30 = 0;

      let amortisationYear = null;
      let cumulativeOldCosts = 0, cumulativeElectricCosts = 0, cumulativeNewCosts = nettoInvest;

      for (let i = 1; i <= 30; i++) {
        const inflationFactor = Math.pow(1 + (config.inflationRate / 100), i - 1);

        const oldCostYear = (heizkostenOld + stromCostOld + evOldCost + kaminCostOld) * inflationFactor + config.wartungOld;
        
        const activeHeizkostenYear = (config.moduleWP ? 0 : heizkostenOld) * inflationFactor;
        const activeEvCostYear = (config.moduleWB ? 0 : evOldCost) * inflationFactor;

        const electricCostYear = (derivedParams.gesamtStrombedarf * config.preisStrom + futureKaminCosts) * inflationFactor + config.wartungOld - ersparnis14aAllElectric + activeHeizkostenYear + activeEvCostYear;
        const newCostYear = (kpis.totalNetzbezug * config.preisStrom - kpis.totalNetzeinspeisung * config.preisEinspeisung + futureKaminCosts) * inflationFactor - ersparnis14a + activeHeizkostenYear + activeEvCostYear + config.wartungOld;

        const currentYearSavings = oldCostYear - newCostYear;

        cumulativeOldCosts += oldCostYear;
        cumulativeElectricCosts += electricCostYear;
        cumulativeNewCosts += newCostYear;

        if (i === 10) { oldCostCumulative10 = cumulativeOldCosts; electricCostCumulative10 = cumulativeElectricCosts; newCostCumulative10 = cumulativeNewCosts; }
        if (i === 20) { oldCostCumulative20 = cumulativeOldCosts; electricCostCumulative20 = cumulativeElectricCosts; newCostCumulative20 = cumulativeNewCosts; }
        if (i === 30) { oldCostCumulative30 = cumulativeOldCosts; electricCostCumulative30 = cumulativeElectricCosts; newCostCumulative30 = cumulativeNewCosts; }

        cumulativeCashflow += currentYearSavings;
        cumulativeErsparnis += currentYearSavings;

        if (cumulativeOldCosts > cumulativeNewCosts && !amortisationYear) amortisationYear = i;

        cashflow.push({
          year: `${i}`,
          yearLabel: `Jahr ${i}`,
          kostenOhne: Math.round(cumulativeOldCosts),
          kostenMit: Math.round(cumulativeNewCosts),
          cashflow: Math.round(cumulativeCashflow),
          ersparnis: Math.round(currentYearSavings),
          kumulierteErsparnis: Math.round(cumulativeErsparnis)
        });
      }

      const avgSavings20 = cashflow[19].kumulierteErsparnis / 20;
      const roi = nettoInvest > 0 ? ((avgSavings20 / nettoInvest) * 100).toFixed(1) : '0.0';

      return {
        maxZuschussProzent, weDeckelung, kfwZuschuss, totalInvest, totalDiscount, totalExtraGrant, totalFoerderung, nettoInvest, lcoe,
        nettoWP, nettoPV, nettoBattery, nettoWallbox,
        discountWPNum, discountPVNum, discountBatteryNum, discountWallboxNum,
        extraGrantWPNum, extraGrantPVNum, extraGrantBatteryNum, extraGrantWallboxNum,
        isKombiBonusActive,
        costOldTotal: costOldBase, costNewTotal: costNewBase, costAllElectricBase, ersparnisJahr1,
        ersparnisNurElektrisch, ersparnisDurchPV, amortisationYear, roi, finUnabhProzent, evOldCost,
        ersparnis10: cashflow[9].kumulierteErsparnis,
        ersparnis20: cashflow[19].kumulierteErsparnis,
        ersparnis30: cashflow[29].kumulierteErsparnis,
        oldCostCumulative10: Math.round(oldCostCumulative10),
        oldCostCumulative20: Math.round(oldCostCumulative20),
        oldCostCumulative30: Math.round(oldCostCumulative30),
        electricCostCumulative10: Math.round(electricCostCumulative10),
        electricCostCumulative20: Math.round(electricCostCumulative20),
        electricCostCumulative30: Math.round(electricCostCumulative30),
        newCostCumulative10: Math.round(newCostCumulative10),
        newCostCumulative20: Math.round(newCostCumulative20),
        newCostCumulative30: Math.round(newCostCumulative30),
        cashflow,
        kfwDetails: { baseProzent, isOldFossil, klimaBonus, einkommenBonus, costPerWE },
        heizkostenOld, ersparnis14aAllElectric, ersparnis14a, futureKaminCosts,
        hhKostenOhne, wpKostenOhne, evKostenOhne,
        hhNetz, wpNetz, evNetz,
        einspeiseVerguetung: Math.round(kpis.totalNetzeinspeisung * config.preisEinspeisung)
      };
    }


    function getComputed() {
      const derivedParams = getDerivedParams();
      const simulation = getSimulation(derivedParams);
      const finance = getFinance(derivedParams, simulation.kpis);

      return {
        derivedParams,
        chartData: simulation.chartData,
        seasonalData: simulation.seasonalData,
        kpis: simulation.kpis,
        co2: simulation.co2,
        bedarfsMix: simulation.bedarfsMix,
        finance
      };
    }

    function round2(val) {
      const n = Number(val || 0);
      return Number.isFinite(n) ? Number(n.toFixed(2)) : 0;
    }

    function getCsrfToken() {
      return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function buildProfitabilityPayload() {
      const computed = getComputed();
      const { finance, co2 } = computed;

      const customerSnapshot = {
        customer_id: backendMeta.customerId,
        alternative_id: backendMeta.alternativeId,
        product_id: backendMeta.productId,
        service_id: backendMeta.serviceId,
        customer: backendCustomer || null,
        products: backendProducts || [],
        preset: backendPreset || {}
      };

      return {
        id: backendMeta.calculationId || null,

        customer_id: backendMeta.customerId,
        alternative_id: backendMeta.alternativeId,
        product_id: backendMeta.productId,
        service_id: backendMeta.serviceId,

        title: `Wirtschaftlichkeitsberechnung ${state.config.name || 'Kunde'} - ${new Date().toLocaleDateString('de-DE')}`,
        status: 'draft',

        // ALT
        current_electricity_cost: round2(finance.hhKostenOhne || 0),
        current_heating_cost: round2(finance.heizkostenOld || 0),
        current_fuel_cost: round2(finance.evOldCost || 0),
        current_total_yearly_cost: round2(finance.costOldTotal || 0),
        current_total_25y_cost: round2(finance.cashflow?.[24]?.kostenOhne || 0),

        // NEU
        future_electricity_cost: round2((computed.kpis.totalNetzbezug || 0) * (state.config.preisStrom || 0)),
        future_heating_cost: round2(0),
        future_ev_cost: round2(state.config.moduleWB ? ((computed.derivedParams.evStrombedarf || 0) * (state.config.preisStrom || 0)) : 0),
        future_total_yearly_cost: round2(finance.costNewTotal || 0),
        future_total_25y_cost: round2(finance.cashflow?.[24]?.kostenMit || 0),

        // savings
        savings_per_year: round2(finance.ersparnisJahr1 || 0),
        savings_over_25_years: round2(
          ((finance.cashflow?.[24]?.kostenOhne || 0) - (finance.cashflow?.[24]?.kostenMit || 0))
        ),

        // investment
        investment_cost: round2(finance.nettoInvest || 0),
        amortisation_years: round2(finance.amortisationYear || 0),
        roi_percent: round2(finance.roi || 0),

        // emissions
        co2_emission_before: round2((Number(co2.year || 0) + ((computed.kpis.totalNetzbezug || 0) * 0.4 / 1000)) * 1000),
        co2_emission_after: round2(((computed.kpis.totalNetzbezug || 0) * 0.4)),
        co2_saved_trees_equiv: Math.round(co2.trees || 0),

        notes: '',
        electricity_price_note: `EVU Preis ${Number(state.config.preisStrom || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €/kWh`,

        config_snapshot: structuredClone(state.config),
        computed_snapshot: structuredClone(computed),
        customer_snapshot: customerSnapshot
      };
    }

    async function saveProfitabilityCalculation() {
      try {
        const payload = buildProfitabilityPayload();

        const res = await fetch(backendMeta.saveUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (!res.ok || !data.ok) {
          console.error('Save failed:', data);
          alert(data.message || 'Speichern fehlgeschlagen.');
          return false;
        }

        backendMeta.calculationId = data.id;
        alert('Wirtschaftlichkeitsberechnung erfolgreich gespeichert.');
        return true;
      } catch (error) {
        console.error(error);
        alert('Beim Speichern ist ein Fehler aufgetreten.');
        return false;
      }
    }

    // =========================================================
    // STATE MUTATIONS
    // =========================================================
      function handleConfigChange(key, value, options = {}) {
        const { render = true, delay = 0 } = options;

        const prevConfig = structuredClone(state.config);
        const nextConfig = {
          ...state.config,
          [key]: value
        };

        if (key === 'gebaeudeArt' && value === 'Einfamilienhaus') {
          nextConfig.wohneinheiten = 1;
          nextConfig.selbstbewohnteWE = 1;
          nextConfig.weUnter40k = 0;
        }

        if (key === 'wohneinheiten') {
          const totalUnits = Math.max(1, Number(value) || 1);

          nextConfig.wohneinheiten = totalUnits;

          if (Number(nextConfig.selbstbewohnteWE || 0) > totalUnits) {
            nextConfig.selbstbewohnteWE = totalUnits;
          }

          if (Number(nextConfig.weUnter40k || 0) > Number(nextConfig.selbstbewohnteWE || 0)) {
            nextConfig.weUnter40k = Number(nextConfig.selbstbewohnteWE || 0);
          }
        }

        if (key === 'selbstbewohnteWE') {
          const ownUnits = Math.max(0, Number(value) || 0);
          const cappedOwnUnits = Math.min(Number(nextConfig.wohneinheiten || 0), ownUnits);

          nextConfig.selbstbewohnteWE = cappedOwnUnits;

          if (Number(nextConfig.weUnter40k || 0) > cappedOwnUnits) {
            nextConfig.weUnter40k = cappedOwnUnits;
          }
        }

        if (key === 'weUnter40k') {
          const lowIncomeUnits = Math.max(0, Number(value) || 0);
          nextConfig.weUnter40k = Math.min(Number(nextConfig.selbstbewohnteWE || 0), lowIncomeUnits);
        }

        if (key === 'heizungArt') {
          switch (value) {
            case 'Gas':
              nextConfig.preisHeizMedium = 0.11;
              break;

            case 'Öl':
              nextConfig.preisHeizMedium = 1.05;
              break;

            case 'Holz / Pellets':
              nextConfig.preisHeizMedium = 280;
              nextConfig.heizVerbrauch = 4;
              break;

            case 'Stromdirektheizung':
            case 'Nachtspeicher':
              nextConfig.preisHeizMedium = Number(nextConfig.preisStrom || 0);
              break;
          }

          if (value !== 'Holz / Pellets' && prevConfig.heizungArt === 'Holz / Pellets') {
            nextConfig.heizVerbrauch = 20000;
          }
        }

        if (key === 'preisStrom') {
          const strompreis = Number(value || 0);

          if (
            nextConfig.heizungArt === 'Stromdirektheizung' ||
            nextConfig.heizungArt === 'Nachtspeicher'
          ) {
            nextConfig.preisHeizMedium = strompreis;
          }
        }

        if (key === 'kaminVorhanden' && !value) {
          nextConfig.kaminWeiterBetreiben = false;
        }

        if (key === 'solarthermieVorhanden' && !value) {
          nextConfig.solarthermieWeiterBetreiben = false;
        }

        state.config = nextConfig;
        updateThemeCSS();

        if (!render) return;

        if (typeof queueRender === 'function') {
          queueRender(delay);
        } else {
          renderApp();
        }
      }

    function toggleSidebarSection(section) {
      state.sidebarSections[section] = !state.sidebarSections[section];
      renderApp();
    }

    function addDachseite() {
      if (state.config.dachseiten.length >= 4) return;
      state.config.dachseiten.push({
        id: Date.now(),
        ausrichtung: 'Ost',
        neigung: 35,
        eindeckung: 'Ziegel',
        eindeckungTyp: '',
        customKwp: ''
      });
      renderApp();
    }

    function updateDachseite(id, field, value) {
      state.config.dachseiten = state.config.dachseiten.map(d => d.id === id ? { ...d, [field]: value } : d);
      renderApp();
    }

    function removeDachseite(id) {
      state.config.dachseiten = state.config.dachseiten.filter(d => d.id !== id);
      renderApp();
    }

    function setWizardStep(step) {
      state.wizardStep = step;
      renderApp();
    }

    function setView(view) {
      state.view = view;
      renderApp();
    }

    function setSidebarOpen(value) {
      state.isSidebarOpen = value;
      renderApp();
    }

    // =========================================================
    // REUSABLE HTML
    // =========================================================
    function ReportHeader(text) {
      const theme = getActiveTheme();
      return `
        <div class="text-[13px] font-bold text-slate-400 tracking-widest mb-6 border-b border-[${theme.primary}] pb-2 flex justify-between items-center" style="border-bottom: 2px solid ${theme.primary};">
          <span class="text-slate-500 uppercase">${text}</span>
          <span><img src="${theme.logo}" style="width: 190px;"></span>
        </div>
      `;
    }

    function ReportFooter() {
      const theme = getActiveTheme();
      return `
        <div class="absolute bottom-[10mm] left-[20mm] right-[20mm] pt-2 pb-1 flex justify-between items-center text-[13px] text-slate-500 bg-white z-50"
             style="border-top: 3px solid ${theme.primary};">
          <span class="uppercase tracking-widest font-bold"></span>
          <span class="font-bold">Seite <span class="page-number"></span></span>
        </div>
      `;
    }

    function sidebarInput({ label, value, type = "text", step = "", rightLabel = "", placeholder = "", disabled = false, oninput = "" }) {
      const theme = getActiveTheme();
      return `
        <div class="flex flex-col gap-1.5 mb-3 ${disabled ? 'opacity-50 pointer-events-none' : ''}">
          <div class="flex justify-between items-end">
            <label class="text-xs font-bold text-slate-700">${label}</label>
            ${rightLabel ? `<span class="text-[13px] font-bold px-1.5 py-0.5 rounded" style="color:${theme.primary};background:${theme.bgLight}">${rightLabel}</span>` : ''}
          </div>
          <input
            type="${type}"
            ${step !== "" ? `step="${step}"` : ""}
            value="${value ?? ''}"
            placeholder="${placeholder}"
            ${disabled ? 'disabled' : ''}
            oninput="${oninput}"
            class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 transition-shadow placeholder:text-slate-400 focus-ring disabled:bg-slate-100"
          />
        </div>
      `;
    }

    // =========================================================
    // RENDER: WIZARD
    // =========================================================
    function renderWizard() {
      const config = state.config;
      const { derivedParams } = getComputed();
      const theme = getActiveTheme();

      return `
        <div class="min-h-screen bg-white flex items-center justify-center p-4 font-sans text-slate-600">
          <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-4xl overflow-hidden">
            <div class="bg-[${theme.primary}] text-white p-8">
              <div class="font-bold text-sm tracking-widest mb-1" style="color:${theme.secondary}">${theme.name}</div>
              <h1 class="text-3xl font-bold mb-2">IHR WEG ZUR EIGENEN ENERGIEAUTARKIE</h1>
              <p class="text-slate-300 text-sm">Konfigurieren Sie Ihr System für den finalen Beratungsbericht.</p>

              <div class="flex items-center mt-8 gap-2">
                ${[1,2,3,4].map(step => `
                  <div class="flex-1 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm mb-2 transition-colors ${state.wizardStep >= step ? 'text-white' : 'bg-slate-800 text-slate-500'}"
                      style="${state.wizardStep >= step ? `background-color:${theme.primary}` : ''}">
                      ${state.wizardStep > step ? icon('checkCircle2', 'w-4 h-4') : step}
                    </div>
                    <div class="h-1 w-full rounded-full ${state.wizardStep >= step ? '' : 'bg-slate-800'}"
                      style="${state.wizardStep >= step ? `background-color:${theme.primary}` : ''}"></div>
                  </div>
                `).join('')}
              </div>
            </div>

            <div class="p-8">
              ${state.wizardStep === 1 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.home()}</span>
                    Gebäude, Systemauswahl & Basisdaten
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2 col-span-1 md:col-span-2 p-4 border rounded-xl bg-white border-slate-200">
                      <label class="text-sm font-semibold">Unternehmen / Logo / Design</label>
                      <select onchange="handleConfigChange('company', this.value)" class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        <option ${config.company === 'Werkstudio' ? 'selected' : ''}>Werkstudio</option>
                        <option ${config.company === 'Solar Aspekt' ? 'selected' : ''}>Solar Aspekt</option>
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Name des Kunden</label>
                      <input type="text" value="${config.name}"
                        oninput="handleConfigChange('name', this.value)"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">PLZ (Standort)</label>
                      <input type="text" maxlength="5" value="${config.plz}"
                        oninput="handleConfigChange('plz', this.value.replace(/\\D/g, ''))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                    
                    <div class="col-span-1 md:col-span-2 p-4 border rounded-xl bg-white border-slate-200 mt-2">
                      <h4 class="text-sm font-bold flex items-center gap-2 mb-3">Auswahl der zu berechnenden Systeme</h4>
                      <div class="flex flex-col md:flex-row gap-4">
                          <label class="flex items-center gap-2 cursor-pointer">
                              <input type="checkbox" ${config.modulePV ? 'checked' : ''} onchange="handleConfigChange('modulePV', this.checked)" class="w-5 h-5" style="accent-color:${theme.primary}" />
                              <span class="text-sm font-bold">Photovoltaik & Speicher</span>
                          </label>
                          <label class="flex items-center gap-2 cursor-pointer">
                              <input type="checkbox" ${config.moduleWP ? 'checked' : ''} onchange="handleConfigChange('moduleWP', this.checked)" class="w-5 h-5" style="accent-color:${theme.primary}" />
                              <span class="text-sm font-bold">Wärmepumpe</span>
                          </label>
                          <label class="flex items-center gap-2 cursor-pointer">
                              <input type="checkbox" ${config.moduleWB ? 'checked' : ''} onchange="handleConfigChange('moduleWB', this.checked)" class="w-5 h-5" style="accent-color:${theme.primary}" />
                              <span class="text-sm font-bold">Wallbox (E-Mobilität)</span>
                          </label>
                      </div>
                    </div>
                  </div>

                  ${config.modulePV ? `
                  <div class="p-4 border rounded-xl bg-white border-slate-200 mt-4">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="text-sm font-bold flex items-center gap-2">
                        <span class="w-4 h-4" style="color:${theme.primary}">${Icons.sun()}</span>
                        Geplante Dachflächen
                      </h4>
                      <button onclick="addDachseite()" ${config.dachseiten.length >= 4 ? 'disabled' : ''}
                        class="text-xs font-bold px-3 py-1.5 rounded-lg text-white bg-slate-800 hover:bg-slate-700 disabled:opacity-50">
                        + Weitere Fläche
                      </button>
                    </div>

                    <div class="space-y-3">
                      ${config.dachseiten.map(dach => `
                        <div class="flex gap-3 items-end p-3 bg-white border border-slate-100 rounded-lg  flex-wrap">
                          <div class="w-[28%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">Ausrichtung</label>
                            <select
                              onchange="updateDachseite(${dach.id}, 'ausrichtung', this.value)"
                              class="w-full p-2 border rounded-md text-sm outline-none focus-ring">
                              ${['Süd','Süd-Ost','Süd-West','Ost','West','Nord-Ost','Nord-West','Nord'].map(opt => `
                                <option ${dach.ausrichtung === opt ? 'selected' : ''}>${opt}</option>
                              `).join('')}
                            </select>
                          </div>

                          <div class="w-[18%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">Neigung (°)</label>
                            <input type="number" value="${dach.neigung}"
                              oninput="updateDachseite(${dach.id}, 'neigung', Number(this.value))"
                              class="w-full p-2 border rounded-md text-sm outline-none focus-ring" />
                          </div>

                          <div class="w-[28%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">Eindeckung</label>
                            <select
                              onchange="updateDachseite(${dach.id}, 'eindeckung', this.value)"
                              class="w-full p-2 border rounded-md text-sm outline-none focus-ring">
                              ${['Ziegel','Blech','Trapezblech','Flachdach/Folie','Schiefer'].map(opt => `
                                <option ${((dach.eindeckung || 'Ziegel') === opt) ? 'selected' : ''}>${opt}</option>
                              `).join('')}
                            </select>
                          </div>

                          <div class="w-[18%] space-y-1">
                            <label class="text-xs text-slate-500 font-semibold">kWp (opt)</label>
                            <input type="number" step="0.1" value="${dach.customKwp || ''}" placeholder="Auto"
                              oninput="updateDachseite(${dach.id}, 'customKwp', this.value)"
                              class="w-full p-2 border rounded-md text-sm outline-none focus-ring" />
                          </div>

                          <div class="w-full flex gap-3 items-end mt-1">
                            <div class="flex-1 space-y-1">
                              <label class="text-xs text-slate-500 font-semibold">Material / Typ (z.B. Beton, Frankfurter Pfanne)</label>
                              <input type="text" value="${dach.eindeckungTyp || ''}"
                                oninput="updateDachseite(${dach.id}, 'eindeckungTyp', this.value)"
                                placeholder="z.B. Beton (Frankfurter Pfanne)"
                                class="w-full p-2 border rounded-md text-sm outline-none focus-ring" />
                            </div>
                            ${config.dachseiten.length > 1 ? `
                              <button onclick="removeDachseite(${dach.id})"
                                class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-md hover:bg-red-100 transition-colors mb-0.5 shrink-0">
                                <span class="w-4 h-4">${Icons.x()}</span>
                              </button>
                            ` : ''}
                          </div>
                        </div>
                      `).join('')}
                    </div>
                  </div>
                  ` : ''}

                  ${config.moduleWP ? `
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-white border border-slate-100 rounded-xl">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold text-slate-700">Gebäudeart</label>
                      <select onchange="handleConfigChange('gebaeudeArt', this.value)" class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        <option ${config.gebaeudeArt === 'Einfamilienhaus' ? 'selected' : ''}>Einfamilienhaus</option>
                        <option ${config.gebaeudeArt === 'Mehrfamilienhaus' ? 'selected' : ''}>Mehrfamilienhaus</option>
                      </select>
                    </div>

                    ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                      <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Nutzungsart des Hauses</label>
                        <select onchange="handleConfigChange('selbstbewohnteWE', this.value === 'Selbstbewohnt' ? 1 : 0)" class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                          <option ${(config.selbstbewohnteWE === 1) ? 'selected' : ''}>Selbstbewohnt</option>
                          <option ${(config.selbstbewohnteWE !== 1) ? 'selected' : ''}>Vermietet</option>
                        </select>
                      </div>
                    ` : `
                      <>
                        <div class="space-y-2">
                          <label class="text-sm font-semibold text-slate-700">Wohneinheiten gesamt</label>
                          <input type="number" min="2" value="${config.wohneinheiten}"
                            oninput="
                              handleConfigChange('wohneinheiten', Number(this.value));
                              if (state.config.selbstbewohnteWE > Number(this.value)) handleConfigChange('selbstbewohnteWE', Number(this.value));
                            "
                            class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                        </div>
                        <div class="space-y-2">
                          <label class="text-sm font-semibold text-slate-700">Davon selbst bewohnt (Eigentümer)</label>
                          <input type="number" min="0" max="${config.wohneinheiten}" value="${config.selbstbewohnteWE}"
                            oninput="handleConfigChange('selbstbewohnteWE', Math.min(state.config.wohneinheiten, Number(this.value)))"
                            class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                          <p class="text-xs text-slate-500">${config.wohneinheiten - config.selbstbewohnteWE} WE gelten als vermietet.</p>
                        </div>
                      </>
                    `}
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Aktuelle Heizung</label>
                      <select onchange="handleConfigChange('heizungArt', this.value)" class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Gas','Öl','Holz / Pellets','Nachtspeicher'].map(opt => `
                          <option ${config.heizungArt === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Alter (Jahre)</label>
                      <input type="number" value="${config.heizungAlter}"
                        oninput="handleConfigChange('heizungAlter', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2 relative">
                      <label class="text-sm font-semibold flex justify-between">Verbrauch <span class="text-slate-400 font-normal">in ${getHeizEinheit(config.heizungArt)}</span></label>
                      <input type="number" value="${config.heizVerbrauch}"
                        oninput="handleConfigChange('heizVerbrauch', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Personen</label>
                      <input type="number" value="${config.personen}"
                        oninput="handleConfigChange('personen', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Heizsystem (Übergabe)</label>
                      <select onchange="handleConfigChange('heizSystem', this.value)" class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Heizkörper','Fußbodenheizung','Beides'].map(opt => `
                          <option ${config.heizSystem === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Warmwasser</label>
                      <select onchange="handleConfigChange('warmwasserArt', this.value)" class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Zentral','Dezentral'].map(opt => `
                          <option ${config.warmwasserArt === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2 flex flex-col justify-center mt-6">
                      <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" ${config.zirkulation ? 'checked' : ''}
                          onchange="handleConfigChange('zirkulation', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-sm font-bold text-slate-600">Zirkulation vorhanden</span>
                      </label>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Rohrsystem Heizung</label>
                      <div class="flex gap-2">
                        <select onchange="handleConfigChange('rohrHeizungMaterial', this.value)" class="w-2/3 p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                          ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                            <option ${config.rohrHeizungMaterial === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                        <div class="w-1/3 relative">
                          <input type="text" value="${config.rohrHeizungDN}" placeholder="DN"
                            oninput="handleConfigChange('rohrHeizungDN', this.value)"
                            class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                          <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                        </div>
                      </div>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Rohrsystem Warmwasser</label>
                      <div class="flex gap-2">
                        <select onchange="handleConfigChange('rohrWWMaterial', this.value)" class="w-2/3 p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                          ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                            <option ${config.rohrWWMaterial === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                        <div class="w-1/3 relative">
                          <input type="text" value="${config.rohrWWDN}" placeholder="DN"
                            oninput="handleConfigChange('rohrWWDN', this.value)"
                            class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                          <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  ${config.zirkulation ? `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                      <div class="space-y-2">
                        <label class="text-sm font-semibold">Rohrsystem Zirkulation</label>
                        <div class="flex gap-2">
                          <select onchange="handleConfigChange('rohrZirkulationMaterial', this.value)" class="w-2/3 p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                            ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                              <option ${config.rohrZirkulationMaterial === opt ? 'selected' : ''}>${opt}</option>
                            `).join('')}
                          </select>
                          <div class="w-1/3 relative">
                            <input type="text" value="${config.rohrZirkulationDN}" placeholder="DN"
                              oninput="handleConfigChange('rohrZirkulationDN', this.value)"
                              class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                            <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  ` : ''}

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div class="p-4 border rounded-xl bg-white border-slate-200">
                      <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" ${config.kaminVorhanden ? 'checked' : ''}
                          onchange="handleConfigChange('kaminVorhanden', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-sm font-bold text-slate-600">Holz-Kamin vorhanden</span>
                      </label>

                      ${config.kaminVorhanden ? `
                        <div class="space-y-3">
                          <div class="flex gap-3">
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-slate-600">Bedarf (Raummeter)</label>
                              <input type="number" value="${config.holzVerbrauch}"
                                oninput="handleConfigChange('holzVerbrauch', Number(this.value))"
                                class="w-full p-2 border rounded-lg" />
                            </div>
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-slate-600">Preis (€/RM)</label>
                              <input type="number" value="${config.preisHolz}"
                                oninput="handleConfigChange('preisHolz', Number(this.value))"
                                class="w-full p-2 border rounded-lg" />
                            </div>
                          </div>
                          <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                            <input type="checkbox" ${config.kaminWeiterBetreiben ? 'checked' : ''}
                              onchange="handleConfigChange('kaminWeiterBetreiben', this.checked)"
                              class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                            <span class="text-xs font-semibold text-slate-700">Wird im Neusystem weiter befeuert</span>
                          </label>
                        </div>
                      ` : ''}
                    </div>

                    <div class="p-4 border rounded-xl bg-white border-slate-200">
                      <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" ${config.solarthermieVorhanden ? 'checked' : ''}
                          onchange="handleConfigChange('solarthermieVorhanden', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-sm font-bold text-slate-600">Solarthermie vorhanden</span>
                      </label>

                      ${config.solarthermieVorhanden ? `
                        <div class="space-y-3">
                          <div class="flex gap-3">
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-slate-600">Kollektor-Art</label>
                              <select onchange="handleConfigChange('solarthermieArt', this.value)" class="w-full p-2 border rounded-lg bg-white">
                                <option ${config.solarthermieArt === 'Flachkollektor' ? 'selected' : ''}>Flachkollektor</option>
                                <option ${config.solarthermieArt === 'Röhrenkollektor' ? 'selected' : ''}>Röhrenkollektor</option>
                              </select>
                            </div>
                            <div class="w-1/2 space-y-1">
                              <label class="text-xs text-slate-600">Anzahl Kollektoren</label>
                              <input type="number" value="${config.solarKollektoren}"
                                oninput="handleConfigChange('solarKollektoren', Number(this.value))"
                                class="w-full p-2 border rounded-lg" />
                            </div>
                          </div>
                          <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                            <input type="checkbox" ${config.solarthermieWeiterBetreiben ? 'checked' : ''}
                              onchange="handleConfigChange('solarthermieWeiterBetreiben', this.checked)"
                              class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                            <span class="text-xs font-semibold text-slate-700">Bleibt auf dem Dach / in Nutzung</span>
                          </label>
                        </div>
                      ` : ''}
                    </div>
                  </div>

                  ${config.selbstbewohnteWE > 0 ? `
                    <div class="pt-4 border-t border-slate-100">
                      ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                        <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-colors" style="border-color:${theme.secondary}50;background:${theme.bgLight}">
                          <input type="checkbox" ${config.weUnter40k === 1 ? 'checked' : ''}
                            onchange="handleConfigChange('weUnter40k', this.checked ? 1 : 0)"
                            class="w-5 h-5 rounded" style="accent-color:${theme.primary}" />
                          <span class="text-sm font-medium" style="color:${theme.primary}">
                            Haushalts-Einkommen liegt unter 40.000 € (Aktiviert 30% KfW-Bonus)
                          </span>
                        </label>
                      ` : `
                        <div class="space-y-2 p-4 border rounded-xl" style="border-color:${theme.secondary}50;background:${theme.bgLight}">
                          <label class="text-sm font-semibold" style="color:${theme.primary}">
                            Wie viele der ${config.selbstbewohnteWE} selbstbewohnten Einheiten haben ein Haushaltseinkommen &lt; 40.000 €?
                          </label>
                          <input type="number" min="0" max="${config.selbstbewohnteWE}" value="${config.weUnter40k}"
                            oninput="handleConfigChange('weUnter40k', Math.min(state.config.selbstbewohnteWE, Number(this.value)))"
                            class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                          <p class="text-xs" style="color:${theme.primary}">Aktiviert den 30% Einkommensbonus anteilig.</p>
                        </div>
                      `}
                    </div>
                  ` : `
                    <div class="pt-4 border-t border-slate-100">
                      <div class="p-4 border border-slate-200 bg-slate-100 rounded-xl flex items-center gap-3 text-slate-500">
                        <span class="w-5 h-5">${Icons.info()}</span>
                        <span class="text-sm">Für voll vermietete Objekte entfallen Klima-/Einkommensbonus (max. 35%).</span>
                      </div>
                    </div>
                  `}
                  ` : ''}
                </div>
              ` : ''}

              ${state.wizardStep === 2 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.users()}</span>
                    Haushalt & Elektromobilität
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Haushaltsstrom (kWh/a)</label>
                      <input type="number" step="100" value="${config.hhStrom}"
                        oninput="handleConfigChange('hhStrom', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    ${config.moduleWB ? `
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Aktuelles Fahrzeug</label>
                      <select onchange="handleConfigChange('autoArt', this.value)" class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring">
                        <option ${config.autoArt === 'Verbrenner' ? 'selected' : ''}>Verbrenner</option>
                        <option ${config.autoArt === 'E-Auto' ? 'selected' : ''}>E-Auto</option>
                      </select>
                    </div>
                    ` : '<div></div>'}
                  </div>

                  ${config.moduleWB ? `
                  <div class="p-5 border rounded-xl bg-white border-slate-200">
                    <h4 class="font-bold text-sm mb-4">Fahrzeugnutzung</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div class="space-y-2">
                        <label class="text-xs font-semibold">Fahrleistung (km/a)</label>
                        <input type="number" step="1000" value="${config.fahrleistung}"
                          oninput="handleConfigChange('fahrleistung', Number(this.value))"
                          class="w-full p-2 border rounded-lg" />
                      </div>

                      ${config.autoArt === 'Verbrenner' ? `
                        <div class="space-y-2">
                          <label class="text-xs font-semibold">Verbrauch (l/100km)</label>
                          <input type="number" step="0.5" value="${config.verbrennerVerbrauch}"
                            oninput="handleConfigChange('verbrennerVerbrauch', Number(this.value))"
                            class="w-full p-2 border rounded-lg" />
                        </div>

                        <div class="space-y-2">
                          <label class="text-xs font-semibold">Spritpreis (€/l)</label>
                          <input type="number" step="0.05" value="${config.preisSprit}"
                            oninput="handleConfigChange('preisSprit', Number(this.value))"
                            class="w-full p-2 border rounded-lg" />
                        </div>
                      ` : ''}
                    </div>

                    ${config.autoArt === 'Verbrenner' ? `
                      <p class="text-xs text-slate-500 mt-4 italic">
                        Für die spätere Anlagen-Dimensionierung kalkulieren wir direkt den Strombedarf für ein zukünftiges E-Auto mit ein, um Sie zukunftssicher aufzustellen.
                      </p>
                    ` : ''}
                  </div>
                  ` : ''}
                </div>
              ` : ''}

              ${state.wizardStep === 3 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.zap()}</span>
                    Energiepreise & Inflation
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Strompreis (€/kWh)</label>
                      <input type="number" step="0.01" value="${config.preisStrom}"
                        oninput="handleConfigChange('preisStrom', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold flex items-center gap-2">Netzentgelt (Arbeitspreis)</label>
                      <input type="number" step="0.01" value="${config.netzentgelt}"
                        oninput="handleConfigChange('netzentgelt', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    ${config.moduleWP ? `
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Preis ${config.heizungArt} (€/${getHeizEinheit(config.heizungArt)})</label>
                      <input type="number" step="0.01" value="${config.preisHeizMedium}"
                        oninput="handleConfigChange('preisHeizMedium', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                    ` : ''}
                    
                    ${config.modulePV ? `
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Einspeisevergütung (€/kWh)</label>
                      <input type="number" step="0.01" value="${config.preisEinspeisung}"
                        oninput="handleConfigChange('preisEinspeisung', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                    ` : ''}

                    <div class="space-y-2">
                      <label class="text-sm font-semibold flex items-center gap-2">Energiepreis-Inflation (%/Jahr)</label>
                      <input type="number" step="0.5" value="${config.inflationRate}"
                        oninput="handleConfigChange('inflationRate', Number(this.value))"
                        class="w-full p-3 border rounded-xl outline-none font-bold focus-ring"
                        style="background:${theme.bgLight};border-color:${theme.secondary}50;color:${theme.primary}" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Aktuelle Nebenkosten (Wartung etc. €/a)</label>
                      <input type="number" step="10" value="${config.wartungOld}"
                        oninput="handleConfigChange('wartungOld', Number(this.value))"
                        class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                  </div>
                </div>
              ` : ''}

              ${state.wizardStep === 4 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${theme.primary}">${Icons.euro()}</span>
                    Geplante Investitionen (Brutto)
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    ${config.moduleWP ? `
                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>Wärmepumpe (€)</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                          Empfehlung: ${derivedParams.empfohleneWpKw} kW
                        </span>
                      </label>
                      <input type="number" step="1000" value="${config.costWP}"
                        oninput="handleConfigChange('costWP', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                    </div>
                    ` : ''}

                    ${config.modulePV ? `
                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>PV-Anlage (€)</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                          Empfehlung: ${derivedParams.empfohlenePv} kWp
                        </span>
                      </label>
                      <input type="number" step="1000" value="${config.costPV}"
                        oninput="handleConfigChange('costPV', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                    </div>

                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>Batteriespeicher (€)</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                          Empfehlung: ${derivedParams.empfohleneBatterie} kWh
                        </span>
                      </label>
                      <input type="number" step="500" value="${config.costBattery}"
                        oninput="handleConfigChange('costBattery', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                    </div>
                    ` : ''}

                    ${config.moduleWB ? `
                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>Wallbox (€)</span>
                        <span class="text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-md text-[11px] font-bold">Optional</span>
                      </label>
                      <input type="number" step="100" value="${config.costWallbox}"
                        oninput="handleConfigChange('costWallbox', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700  outline-none focus-ring" />
                    </div>
                    ` : ''}
                  </div>
                </div>
              ` : ''}

              <div class="flex justify-between mt-10 pt-6 border-t border-slate-100">
                <button onclick="setWizardStep(state.wizardStep - 1)"
                  ${state.wizardStep === 1 ? 'disabled' : ''}
                  class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all ${state.wizardStep === 1 ? 'opacity-0 cursor-default' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}">
                  <span class="w-4 h-4">${Icons.arrowLeft()}</span>
                  Zurück
                </button>

                ${state.wizardStep < 4 ? `
                  <button onclick="setWizardStep(state.wizardStep + 1)"
                    class="flex items-center gap-2 px-6 py-3 text-white rounded-xl font-semibold transition-all shadow-md"
                    style="background:${theme.primary}">
                    Weiter
                    <span class="w-4 h-4">${Icons.arrowRight()}</span>
                  </button>
                ` : `
                  <button onclick="setView('dashboard')"
                    class="flex items-center gap-2 px-8 py-3 text-white rounded-xl font-bold transition-all"
                    style="background:${theme.primary};box-shadow:0 10px 15px -3px ${theme.primary}40">
                    Druckvorschau Report
                    <span class="w-4 h-4">${Icons.printer()}</span>
                  </button>
                `}
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // =========================================================
    // PART 1 ENDPOINT:
    // dashboard render + charts start in part 2
    // =========================================================
    function renderDashboardPlaceholder() {
      const theme = getActiveTheme();
      return `
        <div class="min-h-screen bg-slate-200 text-slate-600 font-sans pb-20 pt-16 print:p-0 print:bg-white relative overflow-x-hidden">
          <div class="fixed top-0 left-0 w-full bg-[${theme.primary}] text-white p-4 z-[80] flex justify-between items-center no-print">
            <div class="font-bold flex items-center gap-3" style="color:${theme.secondary}">
              <span class="w-5 h-5">${Icons.printer()}</span>
              Druckvorschau: ${theme.name} Report
            </div>
            <div class="flex gap-3">
              <button onclick="setView('wizard')" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors">
                Zurück zum Wizard
              </button>

              <button
                onclick="openCustomerProfile()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.secondary}">
                <span class="w-4 h-4">${Icons.users()}</span>
                Kundenprofil
              </button>

              <button onclick="setSidebarOpen(true)" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors" style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.sliders()}</span>
                Parameter anpassen
              </button>

              <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors ml-2" style="background:${theme.primary}">
                PDF-Vorschau
              </button>

              <button onclick="saveProfitabilityCalculation()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.save()}</span>
                Speichern
              </button>
            </div>
          </div>

          <div class="max-w-5xl mx-auto p-10">
            <div class="bg-white rounded-2xl border border-slate-200  p-10 text-center">
              <div class="text-lg font-bold mb-2">Dashboard wird in Teil 2 fortgesetzt</div>
              <div class="text-sm text-slate-500">Hier beginnt direkt der Druckreport, die Sidebar, alle A4-Seiten und die Chart-Initialisierung.</div>
            </div>
          </div>
        </div>
      `;
    }

    // =========================================================
    // ROOT RENDER
    // =========================================================
    function renderApp() {
      updateThemeCSS();
      const app = document.getElementById('app');
      if (!app) return;

      app.innerHTML = state.view === 'wizard'
        ? renderWizard()
        : renderDashboardPlaceholder();
    }

    // Start-Aufruf im Setup
  </script>
  <script>
    // =========================================================
    // DASHBOARD / REPORT RENDER
    // =========================================================
    function renderSidebar(computed) {
      const config = state.config;
      const { derivedParams } = computed;
      const theme = getActiveTheme();

      return `
        ${state.isSidebarOpen ? `
          <div class="fixed inset-0 bg-[${theme.primary}]/40 backdrop-blur-sm z-[90] no-print transition-opacity" onclick="setSidebarOpen(false)"></div>
        ` : ''}

        <div class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl sidebar-transition z-[100] flex flex-col no-print ${state.isSidebarOpen ? 'translate-x-0' : 'translate-x-full'}">
          <div class="p-5 bg-[${theme.primary}] text-white flex justify-between items-center z-10 shadow-md">
            <h2 class="text-lg font-bold flex items-center gap-2" style="color:${theme.secondary}">
              <span class="w-4 h-4">${Icons.sliders()}</span>
              Live-Editor
            </h2>
            <button onclick="setSidebarOpen(false)" class="text-slate-400 hover:text-white transition-colors bg-white/10 p-1.5 rounded-lg">
              <span class="w-5 h-5">${Icons.x()}</span>
            </button>
          </div>

          <div class="p-4 flex-1 space-y-4 overflow-y-auto custom-scroll">

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('kunde')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.home()}</span>
                  Kunde & Gebäude
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.kunde ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.kunde ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Unternehmen / Design</label>
                    <select onchange="handleConfigChange('company', this.value)" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                      <option ${config.company === 'Werkstudio' ? 'selected' : ''}>Werkstudio</option>
                      <option ${config.company === 'Solar Aspekt' ? 'selected' : ''}>Solar Aspekt</option>
                    </select>
                  </div>
                  
                  ${sidebarInput({
                    label: 'Kundenname',
                    type: 'text',
                    value: config.name,
                    oninput: `handleConfigChange('name', this.value)`
                  })}
                  ${sidebarInput({
                    label: 'PLZ',
                    type: 'text',
                    value: config.plz,
                    oninput: `handleConfigChange('plz', this.value)`
                  })}

                  ${config.moduleWP ? `
                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Gebäudeart</label>
                    <select onchange="handleConfigChange('gebaeudeArt', this.value)" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                      <option ${config.gebaeudeArt === 'Einfamilienhaus' ? 'selected' : ''}>Einfamilienhaus</option>
                      <option ${config.gebaeudeArt === 'Mehrfamilienhaus' ? 'selected' : ''}>Mehrfamilienhaus</option>
                    </select>
                  </div>

                  ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                    <div class="flex flex-col gap-1.5 mb-3">
                      <label class="text-xs font-bold text-slate-700">Nutzung</label>
                      <select onchange="handleConfigChange('selbstbewohnteWE', this.value === 'Selbstbewohnt' ? 1 : 0)" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                        <option ${config.selbstbewohnteWE === 1 ? 'selected' : ''}>Selbstbewohnt</option>
                        <option ${config.selbstbewohnteWE !== 1 ? 'selected' : ''}>Vermietet</option>
                      </select>
                    </div>
                  ` : `
                    ${sidebarInput({
                      label: 'Wohneinheiten gesamt',
                      type: 'number',
                      value: config.wohneinheiten,
                      oninput: `handleConfigChange('wohneinheiten', Number(this.value)); if (state.config.selbstbewohnteWE > Number(this.value)) handleConfigChange('selbstbewohnteWE', Number(this.value))`
                    })}
                    ${sidebarInput({
                      label: 'Davon selbst bewohnt',
                      type: 'number',
                      value: config.selbstbewohnteWE,
                      oninput: `handleConfigChange('selbstbewohnteWE', Math.min(state.config.wohneinheiten, Number(this.value)))`
                    })}
                  `}
                  ` : ''}
                </div>
              ` : ''}
            </div>

            ${config.modulePV ? `
            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('dach')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.maximize()}</span>
                  Dachflächen
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.dach ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.dach ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  ${config.dachseiten.map((dach) => `
                    <div class="flex gap-2 mb-3 pb-3 border-b border-slate-100 last:border-0 last:pb-0 last:mb-0 items-end flex-wrap">
                      <div class="w-[45%]">
                        <label class="text-[13px] font-bold text-slate-500 mb-1 block">Ausrichtung</label>
                        <select onchange="updateDachseite(${dach.id}, 'ausrichtung', this.value)" class="w-full p-2 border rounded-md text-xs outline-none">
                          ${['Süd','Süd-Ost','Süd-West','Ost','West','Nord-Ost','Nord-West','Nord'].map(opt => `
                            <option ${dach.ausrichtung === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                      </div>
                      <div class="w-[20%]">
                        <label class="text-[13px] font-bold text-slate-500 mb-1 block">Neigung</label>
                        <input type="number" value="${dach.neigung}" oninput="updateDachseite(${dach.id}, 'neigung', Number(this.value))" class="w-full p-2 border rounded-md text-xs outline-none" />
                      </div>
                      <div class="w-[25%]">
                        <label class="text-[13px] font-bold text-slate-500 mb-1 block">kWp</label>
                        <input type="number" step="0.1" value="${dach.customKwp || ''}" placeholder="Auto" oninput="updateDachseite(${dach.id}, 'customKwp', this.value)" class="w-full p-2 border rounded-md text-xs outline-none focus-ring" />
                      </div>
                      <div class="w-full flex gap-2 mt-1 items-end">
                        <div class="flex-1">
                          <label class="text-[13px] font-bold text-slate-500 mb-1 block">Eindeckung</label>
                          <select onchange="updateDachseite(${dach.id}, 'eindeckung', this.value)" class="w-full p-2 border rounded-md text-xs outline-none">
                            ${['Ziegel','Blech','Trapezblech','Flachdach/Folie','Schiefer'].map(opt => `
                              <option ${((dach.eindeckung || 'Ziegel') === opt) ? 'selected' : ''}>${opt}</option>
                            `).join('')}
                          </select>
                        </div>
                        <div class="flex-1">
                          <label class="text-[13px] font-bold text-slate-500 mb-1 block">Typ / Material</label>
                          <input type="text" value="${dach.eindeckungTyp || ''}" oninput="updateDachseite(${dach.id}, 'eindeckungTyp', this.value)" placeholder="z.B. Beton" class="w-full p-2 border rounded-md text-xs outline-none" />
                        </div>
                        ${config.dachseiten.length > 1 ? `
                          <button onclick="removeDachseite(${dach.id})" class="text-red-500 hover:bg-red-50 p-2 rounded shrink-0 border border-transparent">
                            <span class="w-3.5 h-3.5 inline-block">${Icons.x()}</span>
                          </button>
                        ` : ''}
                      </div>
                    </div>
                  `).join('')}

                  <button onclick="addDachseite()" ${config.dachseiten.length >= 4 ? 'disabled' : ''} class="w-full mt-2 py-2 border border-dashed border-slate-300 rounded-lg text-xs font-bold text-slate-500 hover:bg-white disabled:opacity-50">
                    + Seite hinzufügen
                  </button>
                </div>
              ` : ''}
            </div>
            ` : ''}

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('altsystem')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.users()}</span>
                  Altsystem & Bedarf
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.altsystem ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.altsystem ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  ${config.moduleWP ? `
                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Hauptheizung</label>
                    <select onchange="handleConfigChange('heizungArt', this.value)" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                      ${['Gas','Öl','Holz / Pellets','Nachtspeicher'].map(opt => `
                        <option ${config.heizungArt === opt ? 'selected' : ''}>${opt}</option>
                      `).join('')}
                    </select>
                  </div>

                  <div class="flex gap-2">
                    <div class="w-1/2">
                      ${sidebarInput({
                        label: 'Alter (Jahre)',
                        type: 'number',
                        value: config.heizungAlter,
                        oninput: `handleConfigChange('heizungAlter', Number(this.value))`
                      })}
                    </div>
                    <div class="w-1/2">
                      ${sidebarInput({
                        label: `Verbrauch ${getHeizEinheit(config.heizungArt)}`,
                        type: 'number',
                        step: '500',
                        value: config.heizVerbrauch,
                        oninput: `handleConfigChange('heizVerbrauch', Number(this.value))`
                      })}
                    </div>
                  </div>
                  ` : ''}

                  <div class="flex gap-2">
                    ${config.moduleWP ? `
                    <div class="w-1/2">
                      ${sidebarInput({
                        label: 'Personen',
                        type: 'number',
                        value: config.personen,
                        oninput: `handleConfigChange('personen', Number(this.value))`
                      })}
                    </div>
                    ` : ''}
                    <div class="${config.moduleWP ? 'w-1/2' : 'w-full'}">
                      ${sidebarInput({
                        label: 'Haushaltsstrom',
                        type: 'number',
                        step: '100',
                        value: config.hhStrom,
                        rightLabel: 'kWh',
                        oninput: `handleConfigChange('hhStrom', Number(this.value))`
                      })}
                    </div>
                  </div>

                  ${config.moduleWP ? `
                  <div class="flex flex-col gap-1.5 mb-3 pt-3 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-700">Heizsystem (Übergabe)</label>
                    <select onchange="handleConfigChange('heizSystem', this.value)" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                      ${['Heizkörper','Fußbodenheizung','Beides'].map(opt => `
                        <option ${config.heizSystem === opt ? 'selected' : ''}>${opt}</option>
                      `).join('')}
                    </select>
                  </div>

                  <div class="flex gap-2 mb-3">
                    <div class="w-1/2">
                      <label class="text-xs font-bold text-slate-700 block mb-1.5">Warmwasser</label>
                      <select onchange="handleConfigChange('warmwasserArt', this.value)" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                        <option ${config.warmwasserArt === 'Zentral' ? 'selected' : ''}>Zentral</option>
                        <option ${config.warmwasserArt === 'Dezentral' ? 'selected' : ''}>Dezentral</option>
                      </select>
                    </div>
                    <div class="w-1/2 flex items-center justify-center pt-5">
                      <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" ${config.zirkulation ? 'checked' : ''} onchange="handleConfigChange('zirkulation', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-xs font-bold text-slate-700">Zirkulation</span>
                      </label>
                    </div>
                  </div>

                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Rohrsystem Heizung</label>
                    <div class="flex gap-2">
                      <select onchange="handleConfigChange('rohrHeizungMaterial', this.value)" class="w-2/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                        ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                          <option ${config.rohrHeizungMaterial === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                      <input type="text" value="${config.rohrHeizungDN}" oninput="handleConfigChange('rohrHeizungDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring" />
                    </div>
                  </div>

                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Rohrsystem WW</label>
                    <div class="flex gap-2">
                      <select onchange="handleConfigChange('rohrWWMaterial', this.value)" class="w-2/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                        ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                          <option ${config.rohrWWMaterial === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                      <input type="text" value="${config.rohrWWDN}" oninput="handleConfigChange('rohrWWDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring" />
                    </div>
                  </div>

                  ${config.zirkulation ? `
                    <div class="flex flex-col gap-1.5 mb-3">
                      <label class="text-xs font-bold text-slate-700">Rohrsystem Zirkulation</label>
                      <div class="flex gap-2">
                        <select onchange="handleConfigChange('rohrZirkulationMaterial', this.value)" class="w-2/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                          ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                            <option ${config.rohrZirkulationMaterial === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                        <input type="text" value="${config.rohrZirkulationDN}" oninput="handleConfigChange('rohrZirkulationDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring" />
                      </div>
                    </div>
                  ` : ''}
                  ` : ''}

                  ${config.moduleWB ? `
                  <div class="flex flex-col gap-1.5 mb-3 pt-3 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-700">Fahrzeug</label>
                    <select onchange="handleConfigChange('autoArt', this.value)" class="w-full p-2 bg-white border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-600 focus-ring">
                      <option ${config.autoArt === 'Verbrenner' ? 'selected' : ''}>Verbrenner</option>
                      <option ${config.autoArt === 'E-Auto' ? 'selected' : ''}>E-Auto</option>
                    </select>
                  </div>

                  ${sidebarInput({
                    label: 'Fahrleistung',
                    type: 'number',
                    step: '1000',
                    value: config.fahrleistung,
                    rightLabel: 'km/a',
                    oninput: `handleConfigChange('fahrleistung', Number(this.value))`
                  })}

                  ${config.autoArt === 'Verbrenner' ? `
                    <div class="flex gap-2">
                      <div class="w-1/2">
                        ${sidebarInput({
                          label: 'Verbrauch',
                          type: 'number',
                          step: '0.5',
                          value: config.verbrennerVerbrauch,
                          rightLabel: 'l/100km',
                          oninput: `handleConfigChange('verbrennerVerbrauch', Number(this.value))`
                        })}
                      </div>
                      <div class="w-1/2">
                        ${sidebarInput({
                          label: 'Spritpreis',
                          type: 'number',
                          step: '0.05',
                          value: config.preisSprit,
                          rightLabel: '€/l',
                          oninput: `handleConfigChange('preisSprit', Number(this.value))`
                        })}
                      </div>
                    </div>
                  ` : ''}
                  ` : ''}
                </div>
              ` : ''}
            </div>

            ${config.moduleWP ? `
            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('kaminSolar')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.thermometer()}</span>
                  Zusatzheizung
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.kaminSolar ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.kaminSolar ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  <label class="flex items-center gap-2 cursor-pointer mb-2">
                    <input type="checkbox" ${config.kaminVorhanden ? 'checked' : ''} onchange="handleConfigChange('kaminVorhanden', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                    <span class="text-xs font-bold text-slate-700">Kaminfeuer / Stückholz</span>
                  </label>
                  ${config.kaminVorhanden ? `
                    <div class="space-y-3">
                      <div class="flex gap-3">
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Bedarf (Raummeter)</label><input type="number" value="${config.holzVerbrauch}" oninput="handleConfigChange('holzVerbrauch', Number(this.value))" class="w-full p-2 border rounded-lg" /></div>
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Preis (€/RM)</label><input type="number" value="${config.preisHolz}" oninput="handleConfigChange('preisHolz', Number(this.value))" class="w-full p-2 border rounded-lg" /></div>
                      </div>
                      <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                        <input type="checkbox" ${config.kaminWeiterBetreiben ? 'checked' : ''} onchange="handleConfigChange('kaminWeiterBetreiben', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-xs font-semibold text-slate-700">Wird im Neusystem weiter befeuert</span>
                      </label>
                    </div>
                  ` : ''}

                  <label class="flex items-center gap-2 cursor-pointer mb-2 mt-4 pt-4 border-t border-slate-100">
                    <input type="checkbox" ${config.solarthermieVorhanden ? 'checked' : ''} onchange="handleConfigChange('solarthermieVorhanden', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                    <span class="text-xs font-bold text-slate-700">Solarthermie vorhanden</span>
                  </label>
                  ${config.solarthermieVorhanden ? `
                    <div class="space-y-3">
                      <div class="flex gap-3">
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Kollektor-Art</label><select onchange="handleConfigChange('solarthermieArt', this.value)" class="w-full p-2 border rounded-lg bg-white"><option ${config.solarthermieArt === 'Flachkollektor' ? 'selected' : ''}>Flachkollektor</option><option ${config.solarthermieArt === 'Röhrenkollektor' ? 'selected' : ''}>Röhrenkollektor</option></select></div>
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Anzahl Kollektoren</label><input type="number" value="${config.solarKollektoren}" oninput="handleConfigChange('solarKollektoren', Number(this.value))" class="w-full p-2 border rounded-lg" /></div>
                      </div>
                      <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                        <input type="checkbox" ${config.solarthermieWeiterBetreiben ? 'checked' : ''} onchange="handleConfigChange('solarthermieWeiterBetreiben', this.checked)" class="w-4 h-4 rounded" style="accent-color:${theme.primary}" />
                        <span class="text-xs font-semibold text-slate-700">Bleibt auf dem Dach / in Nutzung</span>
                      </label>
                    </div>
                  ` : ''}
                </div>
              ` : ''}
            </div>
            ` : ''}

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden">
              <button onclick="toggleSidebarSection('preise')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.trendingUp()}</span>
                  Energiepreise
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.preise ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.preise ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  ${sidebarInput({
                    label: 'Strompreis',
                    type: 'number',
                    step: '0.01',
                    value: config.preisStrom,
                    rightLabel: '€/kWh',
                    oninput: `handleConfigChange('preisStrom', Number(this.value))`
                  })}
                  ${sidebarInput({
                    label: 'Netzentgelt (AP)',
                    type: 'number',
                    step: '0.01',
                    value: config.netzentgelt,
                    rightLabel: '€/kWh',
                    oninput: `handleConfigChange('netzentgelt', Number(this.value))`
                  })}
                  ${config.moduleWP ? sidebarInput({
                    label: `Preis ${config.heizungArt}`,
                    type: 'number',
                    step: '0.01',
                    value: config.preisHeizMedium,
                    rightLabel: `€/${getHeizEinheit(config.heizungArt)}`,
                    oninput: `handleConfigChange('preisHeizMedium', Number(this.value))`
                  }) : ''}
                  ${config.modulePV ? sidebarInput({
                    label: 'Einspeisevergütung',
                    type: 'number',
                    step: '0.01',
                    value: config.preisEinspeisung,
                    rightLabel: '€/kWh',
                    oninput: `handleConfigChange('preisEinspeisung', Number(this.value))`
                  }) : ''}
                  ${sidebarInput({
                    label: 'Energie-Inflation',
                    type: 'number',
                    step: '0.5',
                    value: config.inflationRate,
                    rightLabel: '%/a',
                    oninput: `handleConfigChange('inflationRate', Number(this.value))`
                  })}
                  ${sidebarInput({
                    label: 'Wartung & Fixkosten (Altsystem)',
                    type: 'number',
                    step: '10',
                    value: config.wartungOld,
                    rightLabel: '€/a',
                    oninput: `handleConfigChange('wartungOld', Number(this.value))`
                  })}
                </div>
              ` : ''}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl  overflow-hidden mb-4">
              <button onclick="toggleSidebarSection('investitionen')" class="w-full flex justify-between items-center p-3.5 bg-white hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-600 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${theme.primary}">${Icons.euro()}</span>
                  Investitionen
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.investitionen ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.investitionen ? `
                <div class="p-4 border-t border-slate-100 bg-white space-y-5">
                  
                  ${config.moduleWP ? `
                  <div class="p-3 border border-slate-100 bg-white rounded-lg">
                    <h4 class="font-bold text-xs text-slate-600 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.thermoSnow()}</span> Wärmepumpe</h4>
                    ${sidebarInput({ label:'Preis (Brutto)', type:'number', step:'1000', value:config.costWP, rightLabel:'€', oninput:`handleConfigChange('costWP', Number(this.value))` })}
                    ${sidebarInput({ label:'kW (Manuell)', type:'number', step:'1', value:config.customWpKw, rightLabel:`Empf: ${derivedParams.empfohleneWpKw} kW`, placeholder:'Auto', oninput:`handleConfigChange('customWpKw', this.value)` })}
                    ${sidebarInput({ label:'JAZ (Manuell)', type:'number', step:'0.1', value:config.customJAZ, rightLabel:`Auto: ${derivedParams.berechneteJaz}`, placeholder:'Auto', oninput:`handleConfigChange('customJAZ', this.value)` })}
                    ${sidebarInput({ label:'Kombi-Rabatt', type:'number', step:'100', value:config.discountWP, rightLabel:'€', placeholder:'1000', oninput:`handleConfigChange('discountWP', this.value)` })}
                    ${sidebarInput({ label:'Zusätzl. Förderung', type:'number', step:'100', value:config.extraGrantWP, rightLabel:'€', placeholder:'0', oninput:`handleConfigChange('extraGrantWP', this.value)` })}
                    ${sidebarInput({ label:'Förderquelle WP', type:'text', value:config.extraGrantSourceWP, placeholder:'z.B. Stadt Bad Homburg', oninput:`handleConfigChange('extraGrantSourceWP', this.value)` })}
                  </div>
                  ` : ''}

                  ${config.modulePV ? `
                  <div class="p-3 border border-slate-100 bg-white rounded-lg">
                    <h4 class="font-bold text-xs text-slate-600 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.sun()}</span> PV & Speicher</h4>
                    ${sidebarInput({ label:'Preis PV (Brutto)', type:'number', step:'1000', value:config.costPV, rightLabel:'€', oninput:`handleConfigChange('costPV', Number(this.value))` })}
                    ${sidebarInput({ label:'Gesamt kWp (Manuell)', type:'number', step:'1', value:config.customPvKwp, rightLabel:`Empf: ${derivedParams.empfohlenePv} kWp`, placeholder:'Auto', disabled:derivedParams.manualPvKwpSum > 0, oninput:`handleConfigChange('customPvKwp', this.value)` })}
                    ${sidebarInput({ label:'Kombi-Rabatt PV', type:'number', step:'100', value:config.discountPV, rightLabel:'€', placeholder:'750', oninput:`handleConfigChange('discountPV', this.value)` })}
                    ${sidebarInput({ label:'Zusätzl. Förderung PV', type:'number', step:'100', value:config.extraGrantPV, rightLabel:'€', placeholder:'0', oninput:`handleConfigChange('extraGrantPV', this.value)` })}
                    ${sidebarInput({ label:'Förderquelle PV', type:'text', value:config.extraGrantSourcePV, placeholder:'z.B. Kommune', oninput:`handleConfigChange('extraGrantSourcePV', this.value)` })}
                    <div class="mt-3 pt-3 border-t border-slate-200"></div>
                    ${sidebarInput({ label:'Preis Akku (Brutto)', type:'number', step:'500', value:config.costBattery, rightLabel:'€', oninput:`handleConfigChange('costBattery', Number(this.value))` })}
                    ${sidebarInput({ label:'kWh (Manuell)', type:'number', step:'1', value:config.customBatteryKwh, rightLabel:`Empf: ${derivedParams.empfohleneBatterie} kWh`, placeholder:'Auto', oninput:`handleConfigChange('customBatteryKwh', this.value)` })}
                    ${sidebarInput({ label:'Kombi-Rabatt Akku', type:'number', step:'100', value:config.discountBattery, rightLabel:'€', placeholder:'250', oninput:`handleConfigChange('discountBattery', this.value)` })}
                    ${sidebarInput({ label:'Zusätzl. Förderung Akku', type:'number', step:'100', value:config.extraGrantBattery, rightLabel:'€', placeholder:'0', oninput:`handleConfigChange('extraGrantBattery', this.value)` })}
                    ${sidebarInput({ label:'Förderquelle Akku', type:'text', value:config.extraGrantSourceBattery, placeholder:'z.B. Land Hessen', oninput:`handleConfigChange('extraGrantSourceBattery', this.value)` })}
                  </div>
                  ` : ''}

                  ${config.moduleWB ? `
                  <div class="p-3 border border-slate-100 bg-white rounded-lg">
                    <h4 class="font-bold text-xs text-slate-600 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.car()}</span> Wallbox</h4>
                    ${sidebarInput({ label:'Preis (Brutto)', type:'number', step:'100', value:config.costWallbox, rightLabel:'€', oninput:`handleConfigChange('costWallbox', Number(this.value))` })}
                    ${sidebarInput({ label:'Kombi-Rabatt', type:'number', step:'100', value:config.discountWallbox, rightLabel:'€', placeholder:'150', oninput:`handleConfigChange('discountWallbox', this.value)` })}
                    ${sidebarInput({ label:'Zusätzl. Förderung', type:'number', step:'100', value:config.extraGrantWallbox, rightLabel:'€', placeholder:'0', oninput:`handleConfigChange('extraGrantWallbox', this.value)` })}
                    ${sidebarInput({ label:'Förderquelle Wallbox', type:'text', value:config.extraGrantSourceWallbox, placeholder:'z.B. KfW', oninput:`handleConfigChange('extraGrantSourceWallbox', this.value)` })}
                  </div>
                  ` : ''}

                </div>
              ` : ''}
            </div>
          </div>

          <div class="p-5 bg-white border-t border-slate-200 sticky bottom-0 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)]">
            <button onclick="setSidebarOpen(false)" class="w-full flex justify-center items-center gap-2 px-4 py-3 text-white rounded-xl text-sm font-bold transition-colors" style="background:${theme.primary};box-shadow:0 10px 15px -3px ${theme.primary}40">
              <span class="w-4 h-4">${Icons.save()}</span>
              Speichern & Schließen
            </button>
          </div>
        </div>
      `;
    }

    function renderDashboard() {
      const config = state.config;
      const computed = getComputed();
      const { derivedParams, seasonalData, chartData, kpis, bedarfsMix, finance } = computed;
      const theme = getActiveTheme();

      const bedarfTotal = bedarfsMix.reduce((sum, item) => sum + Number(item.value || 0), 0);

      const hhPercent = bedarfTotal > 0 ? (config.hhStrom / bedarfTotal) * 100 : 0;
      const wpPercent = (config.moduleWP && bedarfTotal > 0) ? (derivedParams.wpStrombedarf / bedarfTotal) * 100 : 0;
      const evPercent = (config.moduleWB && config.fahrleistung > 0 && bedarfTotal > 0)
        ? (derivedParams.evStrombedarf / bedarfTotal) * 100
        : 0;

      const percentSum = hhPercent + wpPercent + evPercent;

      const activeModulesCount = (config.modulePV ? 1 : 0) + (config.moduleWP ? 1 : 0) + (config.moduleWB ? 1 : 0);
      const showMiddleStep = config.modulePV && (config.moduleWP || config.moduleWB);
      const gridColsMiddle = showMiddleStep ? 'grid-cols-3' : 'grid-cols-2';

      return `
        <div class="min-h-screen bg-slate-200 text-slate-600 font-sans pb-20 pt-16 print:p-0 print:bg-white relative overflow-x-hidden">
          <div class="fixed top-0 left-0 w-full bg-white text-white p-4 z-[80] flex justify-between items-center no-print">
            <div class="font-bold flex items-center gap-3" style="color:${theme.secondary}">
              <span class="w-5 h-5">${Icons.printer()}</span>
              Druckvorschau: ${theme.name} Report
            </div>
            <div class="flex gap-3">
              <button onclick="setView('wizard')" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors">
                Zurück zum Wizard
              </button>

              <button
                onclick="openCustomerProfile()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.secondary}">
                <span class="w-4 h-4">${Icons.users()}</span>
                Kundenprofil
              </button>

              <button onclick="setSidebarOpen(true)" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors" style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.sliders()}</span>
                Parameter anpassen
              </button>

              <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors ml-2" style="background:${theme.primary}">
                PDF-Vorschau
              </button>

              <button onclick="saveProfitabilityCalculation()"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors"
                style="background:${theme.primary}">
                <span class="w-4 h-4">${Icons.save()}</span>
                Speichern
              </button>
            </div>
          </div>

          ${renderSidebar(computed)}

          <div class="w-full transition-all duration-300 print:m-0 ${state.isSidebarOpen ? 'md:mr-[360px] lg:mx-auto lg:translate-x-[-180px]' : ''}">

            <div class="a4-page flex flex-col relative bg-white justify-center items-center print:bg-white" style="WebkitPrintColorAdjust:exact;printColorAdjust:exact">
              <div class="absolute top-0 inset-x-0 h-[35%] rounded-b-[40%] shadow-2xl" style="background:${theme.primary}"></div> 
              <div class="bg-white p-16 rounded-[40px] shadow-2xl border border-slate-100 text-center z-10 w-[85%] mt-10" style="justify-self:center;">
               <img src="${theme.logo}" alt="Logo" class="h-14 object-contain drop-shadow-md"  style="justify-self:center"/>
                <h1 class="text-4xl md:text-5xl font-black text-[${theme.primary}] mb-6 tracking-tight leading-tight">
                  IHR INDIVIDUELLES<br/>ENERGIEKONZEPT
                </h1>
                <div class="w-20 h-2 mx-auto rounded-full mb-10" style="background:${theme.primary}"></div>
                <p class="text-2xl text-slate-600 font-medium mb-3">Für Familie ${config.name}</p>
                <p class="text-base text-slate-400 flex items-center justify-center gap-2">
                  <span class="w-4.5 h-4.5">${Icons.mapPin()}</span>
                  Objektstandort: ${config.plz}
                </p>
              </div>
              <div class="mt-auto mb-16 text-center z-10">
                <div class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3">Ausgearbeitet von</div>
                <div class="text-xl font-black text-slate-600 tracking-wide">${theme.name}</div>
                <div class="text-sm text-slate-500 mt-2">Meisterbetrieb für Gebäudetechnik & erneuerbare Energien</div>
              </div>
              <div class="absolute bottom-[-100px] left-[-100px] w-[500px] h-[500px] rounded-full blur-[120px] opacity-10" style="background:${theme.primary}"></div>
            </div>

            <div class="a4-page flex flex-col relative bg-white overflow-hidden">
              ${ReportHeader('IHR KONZEPT')}

              <div class="absolute top-0 right-0 w-52 h-52 rounded-full blur-[100px] opacity-10 print:opacity-5" style="background:${theme.primary}"></div>

              <h1 class="text-3xl font-black mb-2 leading-tight" style="color:${theme.primary}">
                IHR WEG ZUR EIGENEN
              </h1>
              <h1 class="text-3xl font-black mb-3 leading-tight" style="color:${theme.secondary}">
                ENERGIEAUTARKIE
              </h1>

              <p class="text-sm font-bold mb-8 tracking-wide leading-relaxed" style="color:${theme.secondary}">
                WENIGER NETZ. MEHR KONTROLLE.<br/>MAXIMALE EFFIZIENZ – JEDEN TAG
              </p>

              <div class="space-y-3 text-[12px] leading-relaxed text-slate-700 mb-5 flex-1">
                <p>Sehr geehrte(r) ${config.name},</p>

                <p>
                  vielen Dank für Ihr Interesse an einer zukunftssicheren und autarken Energieversorgung. Gerne stellen wir Ihnen Ihr maßgeschneidertes Energiekonzept vor.
                </p>

                <p>
                  Auf den folgenden Seiten sehen Sie transparent, wie sich Ihr Energieprofil durch die intelligente Vernetzung Ihrer gewählten Systeme optimieren lässt. Zudem haben wir alle relevanten staatlichen Förderungen integriert, um Ihre Netto-Investition so effizient wie möglich zu gestalten.
                </p>

                <div class="bg-white p-4 rounded-xl border border-slate-200 mt-3">
                  <p class="font-bold mb-3 text-[13px]" style="color:${theme.primary}">
                    Ihr Energiekonzept im Überblick:
                  </p>

                  <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-[11px] text-slate-700">
                    ${[
                      ['Ausgangslage:', 'Ihre heutigen Energiekosten'],
                      ['Lösungsarchitektur:', 'Dimensionierung Ihres Systems'],
                      ['Saisonale Auswertung:', 'Ihre Autarkie im Jahresverlauf'],
                      [`${theme.name.split(' ')[0]} Expertise:`, 'Ihre System-Vorteile'],
                      config.modulePV ? ['Sonnenenergie:', 'Photovoltaik & Batteriespeicher'] : null,
                      config.moduleWP ? ['Wärmepumpen-Technologie:', 'Elektrifizierung der Wärme'] : null,
                      config.moduleWB ? ['E-Mobilität:', 'Zapfen Sie die Sonne an'] : null,
                      activeModulesCount > 1 ? ['Sektorenkopplung:', 'Das intelligente Gesamtsystem'] : null,
                      ['Wirtschaftlichkeit:', 'Investition, Break-Even & ROI'],
                      ['Transparenz Teil I:', 'Technische Berechnungen'],
                      ['Transparenz Teil II:', 'Kennzahlen & Effizienz'],
                      ['Klimaschutz & Ablauf:', 'Nächste Schritte']
                    ].filter(Boolean).map((item, index) => `
                      <div class="flex gap-2.5 items-start">
                        <span class="w-3.5 h-3.5 shrink-0 mt-0.5" style="color:${theme.primary}">
                          ${Icons.checkSquare()}
                        </span>
                        <span class="leading-relaxed">
                          <strong class="block" style="color:${theme.primary}">${index + 1}. ${item[0]}</strong>
                          ${item[1]}
                        </span>
                      </div>
                    `).join('')}
                  </div>
                </div>
              </div>

              <div class="mt-auto text-[12px] text-slate-700 leading-relaxed">
                <p class="mb-4">
                  Für Ihre Fragen und die nächsten Schritte stehen wir Ihnen jederzeit gerne in einem persönlichen Beratungsgespräch zur Verfügung.
                </p>
                <p>Mit freundlichen Grüßen</p>
                <p class="mt-2 font-bold font-serif text-[15px]" style="color:${theme.primary}">
                  Ihr ${theme.name}-Team
                </p>
              </div>

              ${ReportFooter()}
            </div>

            <div class="a4-page flex flex-col relative bg-white overflow-hidden">
              ${ReportHeader('1. AUSGANGSLAGE')}

              <h2 class="text-lg font-black mb-3" style="color:${theme.primary}">
                1. AUSGANGSLAGE & ENERGIE-TRANSFORMATION
              </h2>

              <div class="mb-3">
                <h3 class="text-xl font-black mb-1 leading-tight" style="color:${theme.primary}">
                  ${showMiddleStep ? 'Der 3-Stufen-Vergleich: Warum nur die Komplettlösung schützt' : 'Ihr Vergleich: Altsystem vs. Neues System'}
                </h3>
                <p class="text-[13px] text-slate-600 leading-relaxed">
                  Um die wahre Effizienz unseres Konzepts zu verstehen, betrachten wir ${showMiddleStep ? 'drei' : 'zwei'} Szenarien: Ihr <strong>heutiges ${config.moduleWP ? 'fossiles ' : ''}System</strong>${showMiddleStep ? ', eine <strong>reine Elektrifizierung</strong> (ohne eigene PV)' : ''} und die <strong>${theme.name} ${config.modulePV ? 'Lösung inkl. PV' : 'Lösung'}</strong>.
                </p>
              </div>

              <div class="grid ${gridColsMiddle} gap-3 mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-3 flex flex-col">
                  <h3 class="font-bold text-[11px] mb-2 border-b border-slate-100 pb-1" style="color:${theme.primary}">
                    1. Altsystem (Status Quo)
                  </h3>
                  <table class="w-full text-left text-[8.5px] text-slate-600 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-0.5">Hausstrom<br><span class="text-[7px] text-slate-400">${formatDE(config.hhStrom)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(config.hhStrom * config.preisStrom))} €</td>
                      </tr>
                      ${config.moduleWP ? `
                      <tr>
                        <td class="py-0.5">Heizung (${config.heizungArt})<br><span class="text-[7px] text-slate-400">${formatDE(config.heizVerbrauch)} ${getHeizEinheit(config.heizungArt)} × ${formatDE(config.preisHeizMedium,2)} €/${getHeizEinheit(config.heizungArt)}</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(finance.heizkostenOld))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWP && config.kaminVorhanden ? `
                      <tr>
                        <td class="py-0.5">Kaminholz<br><span class="text-[7px] text-slate-400">${formatDE(config.holzVerbrauch)} RM × ${formatDE(config.preisHolz,2)} €/RM</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWB && config.fahrleistung > 0 ? `
                      <tr>
                        <td class="py-0.5">
                          Auto (${config.autoArt === 'Verbrenner' ? 'Verbrenner' : 'E-Auto'})<br>
                          <span class="text-[7px] text-slate-400">
                            ${config.autoArt === 'Verbrenner'
                              ? `${formatDE(Math.round((config.fahrleistung/100)*config.verbrennerVerbrauch))} l × ${formatDE(config.preisSprit,2)} €/l`
                              : `${formatDE(Math.round((config.fahrleistung/100)*20))} kWh × ${formatDE(config.preisStrom,2)} €/kWh`
                            }
                          </span>
                        </td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(finance.evOldCost))} €</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Wartung & Fixkosten<br><span class="text-[7px] text-slate-400">Pauschale</span></td>
                        <td class="text-right font-medium align-top py-0.5">${config.wartungOld} €</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="mt-auto pt-1.5 border-t border-slate-200 flex justify-between font-black text-slate-700 text-[13px]">
                    <span>Kosten p.a.</span>
                    <span>${formatDE(Math.round(finance.costOldTotal))} €</span>
                  </div>
                </div>

                ${showMiddleStep ? `
                <div class="bg-white border border-slate-200 rounded-xl p-3 flex flex-col">
                  <h3 class="font-bold text-[11px] mb-2 border-b border-slate-100 pb-1" style="color:${theme.primary}">
                    2. Nur Elektrisch (Ohne PV)
                  </h3>
                  <table class="w-full text-left text-[8.5px] text-slate-600 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-0.5">Hausstrom<br><span class="text-[7px] text-slate-400">${formatDE(config.hhStrom)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(config.hhStrom * config.preisStrom))} €</td>
                      </tr>
                      ${config.moduleWP ? `
                      <tr>
                        <td class="py-0.5">Wärmepumpe<br><span class="text-[7px] text-slate-400">${formatDE(derivedParams.wpStrombedarf)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.wpStrombedarf * config.preisStrom))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWP && config.kaminVorhanden && config.kaminWeiterBetreiben ? `
                      <tr>
                        <td class="py-0.5">Kaminholz (Weiterbetrieb)<br><span class="text-[7px] text-slate-400">${formatDE(config.holzVerbrauch)} RM × ${formatDE(config.preisHolz,2)} €/RM</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                      </tr>
                      ` : ''}
                      ${config.moduleWB && config.fahrleistung > 0 ? `
                      <tr>
                        <td class="py-0.5">E-Auto Laden<br><span class="text-[7px] text-slate-400">${formatDE(derivedParams.evStrombedarf)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.evStrombedarf * config.preisStrom))} €</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Wartung & Fixkosten<br><span class="text-[7px] text-slate-400">Pauschale</span></td>
                        <td class="text-right font-medium align-top py-0.5">${config.wartungOld} €</td>
                      </tr>
                      <tr style="color:${theme.primary}">
                        <td class="py-0.5">§14a EnWG Rabatt<br><span class="text-[7px] opacity-80">Modul 1 / Modul 2 (opt.)</span></td>
                        <td class="text-right font-medium align-top py-0.5">-${finance.ersparnis14aAllElectric} €</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="text-[7px] text-slate-400 italic mb-1.5 leading-tight">Ohne PV machen Sie sich komplett abhängig vom Netzstrompreis.</div>
                  <div class="mt-auto pt-1.5 border-t border-slate-200 flex justify-between font-black text-slate-700 text-[13px]">
                    <span>Kosten p.a.</span>
                    <span>${formatDE(Math.round(finance.costAllElectricBase))} €</span>
                  </div>
                </div>
                ` : ''}

                <div class="bg-white border-2 rounded-xl p-3 flex flex-col" style="border-color:${theme.primary}">
                  <h3 class="font-bold text-[11px] mb-2 border-b border-slate-200 pb-1" style="color:${theme.primary}">
                    ${showMiddleStep ? '3. Komplettlösung (Mit PV)' : '2. Neues System'}
                  </h3>
                  <table class="w-full text-left text-[8.5px] text-slate-700 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-0.5 font-semibold">Gesamtstrombedarf<br><span class="text-[7px] text-slate-400 font-normal">Alle gewählten Sektoren</span></td>
                        <td class="text-right font-bold align-top py-0.5">${formatDE(kpis.totalBedarf)} kWh</td>
                      </tr>
                      ${config.modulePV ? `
                      <tr style="color:${theme.primary}">
                        <td class="py-0.5">Kostenlos durch PV<br><span class="text-[7px] opacity-70">Direktverbrauch & Speicher</span></td>
                        <td class="text-right font-bold align-top py-0.5">-${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Rest-Netzbezug<br><span class="text-[7px] text-slate-500">${formatDE(kpis.totalNetzbezug)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(kpis.totalNetzbezug * config.preisStrom))} €</td>
                      </tr>
                      ${config.moduleWP && config.kaminVorhanden && config.kaminWeiterBetreiben ? `
                      <tr>
                        <td class="py-0.5">Kaminholz<br><span class="text-[7px] text-slate-500">Bleibt im System</span></td>
                        <td class="text-right font-medium align-top py-0.5">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                      </tr>
                      ` : ''}
                      <tr>
                        <td class="py-0.5">Wartung & Fixkosten<br><span class="text-[7px] text-slate-500">Pauschale</span></td>
                        <td class="text-right font-medium align-top py-0.5">${config.wartungOld} €</td>
                      </tr>
                      ${(config.moduleWP || config.moduleWB) ? `
                      <tr style="color:${theme.primary}">
                        <td class="py-0.5">§14a EnWG Rabatt<br><span class="text-[7px] opacity-80">Modul 1 / Modul 2 (opt.)</span></td>
                        <td class="text-right font-medium align-top py-0.5">-${finance.ersparnis14a} €</td>
                      </tr>
                      ` : ''}
                      ${config.modulePV ? `
                      <tr style="color:${theme.secondary}">
                        <td class="py-0.5 font-bold">Einspeisevergütung<br><span class="text-[7px] font-normal">${formatDE(kpis.totalNetzeinspeisung)} kWh × ${formatDE(config.preisEinspeisung,2)} €/kWh</span></td>
                        <td class="text-right font-bold align-top py-0.5">-${formatDE(Math.round(kpis.totalNetzeinspeisung * config.preisEinspeisung))} €</td>
                      </tr>
                      ` : ''}
                    </tbody>
                  </table>
                  <div class="mt-auto pt-1.5 border-t border-slate-300 flex justify-between font-black text-[13px]" style="color:${theme.primary}">
                    <span>Restkosten p.a.</span>
                    <span>${formatDE(Math.round(finance.costNewTotal))} €</span>
                  </div>
                </div>
              </div>

              <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-auto flex flex-col">
                <div class="bg-white p-2.5 border-b border-slate-200">
                  <h4 class="font-bold text-[11px] flex items-center gap-2 mb-0.5" style="color:${theme.primary}">
                    <span class="w-4 h-4" style="color:${theme.primary}">${Icons.trendingUp()}</span>
                    Prognose der Kostenentwicklung (inkl. ${config.inflationRate}% Inflation p.a.)
                  </h4>
                  <p class="text-[9px] text-slate-500 leading-relaxed">
                    Diese Tabelle zeigt die Kraft der Kostenersparnis über die Zeit.
                  </p>
                </div>

                <div class="p-0">
                  ${(() => {
                    const pct1 = finance.costOldTotal > 0 ? Math.round(((finance.costOldTotal - finance.costNewTotal) / finance.costOldTotal) * 100) : 0;
                    const pct10 = finance.oldCostCumulative10 > 0 ? Math.round((finance.ersparnis10 / finance.oldCostCumulative10) * 100) : 0;
                    const pct20 = finance.oldCostCumulative20 > 0 ? Math.round((finance.ersparnis20 / finance.oldCostCumulative20) * 100) : 0;
                    const pct30 = finance.oldCostCumulative30 > 0 ? Math.round((finance.ersparnis30 / finance.oldCostCumulative30) * 100) : 0;
                    return `
                      <table class="w-full text-[9px] text-left">
                        <thead class="bg-white text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                          <tr>
                            <th class="p-2 font-semibold">Zeitraum</th>
                            <th class="p-2 font-semibold">Altsystem</th>
                            ${showMiddleStep ? `<th class="p-2 font-semibold">Nur Elektrisch</th>` : ''}
                            <th class="p-2 font-black" style="color:${theme.primary}">${showMiddleStep ? 'Neusystem' : 'Neues System'}</th>
                            <th class="p-2 font-black" style="color:${theme.primary}">Ersparnis</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                          <tr>
                            <td class="p-2 font-bold">1. Jahr</td>
                            <td class="p-2 font-medium">${formatDE(Math.round(finance.costOldTotal))} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(Math.round(finance.costAllElectricBase))} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(Math.round(finance.costNewTotal))} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(Math.round(finance.costOldTotal - finance.costNewTotal))} € <span class="text-[11px]">(${pct1}%)</span></td>
                          </tr>
                          <tr>
                            <td class="p-2 font-bold">10 Jahre</td>
                            <td class="p-2 font-medium">${formatDE(finance.oldCostCumulative10)} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(finance.electricCostCumulative10)} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(finance.newCostCumulative10)} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(finance.ersparnis10)} € <span class="text-[11px]">(${pct10}%)</span></td>
                          </tr>
                          <tr>
                            <td class="p-2 font-bold">20 Jahre</td>
                            <td class="p-2 font-medium">${formatDE(finance.oldCostCumulative20)} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(finance.electricCostCumulative20)} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(finance.newCostCumulative20)} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(finance.ersparnis20)} € <span class="text-[11px]">(${pct20}%)</span></td>
                          </tr>
                          <tr>
                            <td class="p-2 font-bold">30 Jahre</td>
                            <td class="p-2 font-medium">${formatDE(finance.oldCostCumulative30)} €</td>
                            ${showMiddleStep ? `<td class="p-2 font-medium">${formatDE(finance.electricCostCumulative30)} €</td>` : ''}
                            <td class="p-2 font-black" style="color:${theme.primary}">${formatDE(finance.newCostCumulative30)} €</td>
                            <td class="p-2 font-black" style="color:${theme.primary}">+${formatDE(finance.ersparnis30)} € <span class="text-[11px]">(${pct30}%)</span></td>
                          </tr>
                        </tbody>
                      </table>
                    `;
                  })()}
                </div>
              </div>

              <div class="w-full mb-3">
                <h4 class="font-bold text-[11px] mb-1 uppercase tracking-wide" style="color:${theme.primary}">
                  Ihre System-Dimensionierung
                </h4>
                <p class="text-[9px] text-slate-600 leading-relaxed mb-2">
                  Die Systemauslegung orientiert sich punktgenau an Ihrem zukünftigen Gesamtstrombedarf. So bleibt Ihr Netzbezug minimal.
                </p>
              </div>

              <div class="w-full bg-white p-3 rounded-xl border border-slate-200">
                <div class="flex items-center gap-3">
                  <div class="w-20 h-20 relative shrink-0">
                    <div class="chart-wrap"><canvas id="bedarfsmixChart"></canvas></div>
                  </div>

                  <div class="flex-1 space-y-1.5 text-[9px]">
                    <div class="flex justify-between items-center gap-3">
                      <span class="flex items-center gap-1.5 font-medium text-slate-600">
                        <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                        Haushalt
                      </span>
                      <span class="font-bold text-slate-600">${formatDE(config.hhStrom)} kWh - ${formatDE(hhPercent, 1)}%</span>
                    </div>

                    ${config.moduleWP ? `
                    <div class="flex justify-between items-center gap-3">
                      <span class="flex items-center gap-1.5 font-medium text-slate-600">
                        <div class="w-2 h-2 rounded-full" style="background:${theme.secondary}"></div>
                        WP
                      </span>
                      <span class="font-bold text-slate-600">${formatDE(derivedParams.wpStrombedarf)} kWh - ${formatDE(wpPercent, 1)}%</span>
                    </div>
                    ` : ''}

                    ${config.moduleWB && config.fahrleistung > 0 ? `
                    <div class="flex justify-between items-center gap-3">
                      <span class="flex items-center gap-1.5 font-medium text-slate-600">
                        <div class="w-2 h-2 rounded-full" style="background:${theme.primary}"></div>
                        Auto
                      </span>
                      <span class="font-bold text-slate-600">${formatDE(derivedParams.evStrombedarf)} kWh - ${formatDE(evPercent, 1)}%</span>
                    </div>
                    ` : ''}

                    <div class="mt-2 pt-2 border-t border-slate-200 flex justify-between items-center">
                      <span class="text-[13px] font-bold uppercase tracking-wide text-slate-600">Gesamtverbrauch</span>
                      <span class="text-[11px] font-black" style="color:${theme.primary}">
                        ${formatDE(bedarfTotal)} kWh · ${formatDE(percentSum, 1)}%
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-3 pt-4 mt-auto">
                ${config.moduleWP ? `
                <div class="text-center">
                  <span class="font-semibold text-slate-500 text-[9px] uppercase mb-1 block">Wärmepumpe</span>
                  <span class="font-black text-xl" style="color:${theme.primary}">${derivedParams.wpLeistungKW} kW</span>
                </div>
                ` : '<div class="text-center"></div>'}

                ${config.modulePV ? `
                <div class="text-center border-l border-slate-200">
                  <span class="font-semibold text-slate-500 text-[9px] uppercase mb-1 block">Photovoltaik</span>
                  <span class="font-black text-xl" style="color:${theme.primary}">${derivedParams.pvKwp} kWp</span>
                </div>
                <div class="text-center border-l border-slate-200">
                  <span class="font-semibold text-slate-500 text-[9px] uppercase mb-1 block">Speicher</span>
                  <span class="font-black text-xl" style="color:${theme.primary}">${derivedParams.batteryCapacity} kWh</span>
                </div>
                ` : '<div class="text-center"></div><div class="text-center"></div>'}
              </div>

              ${(config.moduleWP || config.moduleWB) ? `
              <div class="text-[11px] text-slate-400 mt-2 pt-2 border-t border-slate-200 leading-relaxed">
                <strong>Hinweis zu §14a EnWG:</strong> Mit Wärmepumpe oder Wallbox profitieren Sie von reduzierten Netzentgelten. Das System berechnet automatisch das günstigste Modell. In Ihrer neuen Anlage beträgt die Netzentgelt-Ersparnis ${finance.ersparnis14a} € pro Jahr.
              </div>
              ` : ''}

              <div class="flex items-end justify-between border-b border-slate-200 pb-1.5 mb-2">
                <div>
                  <h3 class="text-[13px] font-black text-slate-600 uppercase tracking-[0.16em]">
                    Produktion vs. Verbrauch
                  </h3>
                  <p class="text-[11px] text-slate-500 mt-0.5">
                    Überschüsse entstehen, wenn der Solarertrag höher als der Bedarf liegt.
                  </p>
                </div>
                <div class="text-[11px] font-bold px-2 py-0.5 rounded-full border"
                    style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                  Monatsvergleich
                </div>
              </div>

              <div class="h-[150px] w-full mt-auto">
                <div class="chart-wrap"><canvas id="monthlyCompareChart"></canvas></div>
              </div>

              ${ReportFooter()}
            </div>

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('2. LÖSUNGSARCHITEKTUR')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black mb-2 leading-tight" style="color:${theme.primary}">
                  2. SYSTEMAUSLEGUNG & UNABHÄNGIGKEIT
                </h2>

                <p class="text-[9px] text-slate-600 mb-3 leading-relaxed">
                  ${(config.modulePV && config.moduleWP)
                    ? 'Das Geheimnis maximaler Ersparnis liegt in der cleveren Sektorenkopplung: Haushalt, Wärme und Mobilität verschmelzen zu einem intelligenten Kreislauf. Ihr selbst produzierter Solarstrom wird direkt dorthin geleitet, wo er wirtschaftlich den größten Effekt erzielt.'
                    : 'Im Folgenden sehen Sie die detaillierte Systemauslegung, optimal dimensioniert für Ihren Bedarf und auf maximale Effizienz abgestimmt.'}
                </p>

                <h3 class="font-black text-[15px] mb-2.5" style="color:${theme.primary}">
                  Ihre Gesamtbilanz auf einen Blick
                </h3>

                <div class="grid grid-cols-3 gap-3 mb-3">
                  <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                    <div class="relative w-[94px] h-[94px] mb-2">
                      <div class="chart-wrap"><canvas id="donutAutarkie"></canvas></div>
                      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-[16px] font-black text-slate-700">${kpis.autarkie}%</span>
                      </div>
                    </div>

                    <div class="text-center mb-1.5">
                      <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                        Autarkiegrad
                      </h4>
                      <p class="text-[7px] text-slate-600 font-bold">Gesamte Bedarfsdeckung</p>
                    </div>

                    <div class="w-full text-[7.5px] text-slate-600 space-y-1 border-t border-slate-300 pt-1.5">
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="flex space-x-[-4px]">
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.primary}"></div>
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.secondary}"></div>
                          </div>
                          Deckung
                        </span>
                        <span class="font-bold" style="color:${theme.primary}">
                          ${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh
                        </span>
                      </div>
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                          Netzbezug
                        </span>
                        <span class="font-bold text-slate-700">${formatDE(kpis.totalNetzbezug)} kWh</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                    <div class="relative w-[94px] h-[94px] mb-2">
                      <div class="chart-wrap"><canvas id="donutEigenverbrauch"></canvas></div>
                      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-[16px] font-black text-slate-700">${kpis.eigenverbrauchQuote}%</span>
                      </div>
                    </div>

                    <div class="text-center mb-1.5">
                      <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                        Eigenverbrauch
                      </h4>
                      <p class="text-[7px] text-slate-600 font-bold">Nutzung des PV-Stroms</p>
                    </div>

                    <div class="w-full text-[7.5px] text-slate-600 space-y-1 border-t border-slate-300 pt-1.5">
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="flex space-x-[-4px]">
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.primary}"></div>
                            <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.secondary}"></div>
                          </div>
                          Genutzt
                        </span>
                        <span class="font-bold" style="color:${theme.secondary}">
                          ${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh
                        </span>
                      </div>
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                          Einspeisung
                        </span>
                        <span class="font-bold text-slate-700">${formatDE(kpis.totalNetzeinspeisung)} kWh</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                    <div class="relative w-[94px] h-[94px] mb-2">
                      <div class="chart-wrap"><canvas id="donutFinanz"></canvas></div>
                      <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-[16px] font-black text-slate-700">${finance.finUnabhProzent}%</span>
                      </div>
                    </div>

                    <div class="text-center mb-1.5">
                      <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                        Finanz-Unabhängigkeit
                      </h4>
                      <p class="text-[7px] text-slate-600 font-bold">Schutz vor Preisanstieg</p>
                    </div>

                    <div class="w-full text-[7.5px] text-slate-600 space-y-1 border-t border-slate-300 pt-1.5">
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.primary}"></div>
                          Ersparnis
                        </span>
                        <span class="font-bold text-slate-700">+${formatDE(finance.ersparnisJahr1)} €</span>
                      </div>
                      <div class="flex justify-between gap-2">
                        <span class="flex items-center gap-1">
                          <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                          Vorher
                        </span>
                        <span class="font-bold text-slate-700">${formatDE(Math.round(finance.costOldTotal))} €</span>
                      </div>
                    </div>
                  </div>
                </div>

                ${config.moduleWB ? `
                  <div class="mb-3 text-[7px] text-slate-500 uppercase tracking-[0.14em] text-center">
                    Inklusive intelligenter Lade-Infrastruktur für Elektromobilität
                  </div>
                ` : ''}

                <div class="mb-2.5">
                  <h2 class="text-[11px] font-black text-slate-400 mb-0.5 uppercase tracking-[0.16em]">
                    3. Saisonale Verteilung & Autarkie
                  </h2>
                  <h3 class="font-black text-[16px] uppercase tracking-wide leading-tight" style="color:${theme.primary}">
                    Der Verlauf über das Jahr
                  </h3>
                  <p class="text-[7.5px] text-slate-500 mt-1 leading-relaxed">
                    Die vier Jahreszeiten zeigen, wie stark Ihr System Lasten verschiebt, Eigenstrom direkt nutzt und den Netzbezug reduziert.
                  </p>
                </div>

                <div class="grid grid-cols-2 gap-3 flex-1">
                  ${seasonalData.map((season, i) => {
                    const total = season.Gesamtbedarf;
                    const pDeckung = season.autarkie;
                    const pZukauf = Math.max(0, 100 - pDeckung);
                    const pEinspeisung = season.Solarertrag > 0
                      ? Math.round((Math.abs(season.NetzeinspeisungNeg) / season.Solarertrag) * 100)
                      : 0;

                    return `
                      <div class="bg-white border border-slate-200 rounded-xl px-3 py-2.5 flex flex-col items-center">
                        <div class="relative w-[94px] h-[94px] mb-2">
                          <div class="chart-wrap"><canvas id="seasonChart${i}"></canvas></div>
                          <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <span class="text-[16px] font-black text-slate-700">${pDeckung}%</span>
                          </div>
                        </div>

                        <div class="text-center mb-1.5">
                          <h4 class="font-black text-[10px] tracking-[0.14em] uppercase mb-0.5" style="color:${theme.primary}">
                            ${season.name}
                          </h4>
                          <p class="text-[7px] text-slate-600 font-bold">Saisonale Deckung</p>
                        </div>

                        <div class="w-full text-[7.5px] text-slate-600 space-y-1 border-t border-slate-300 pt-1.5">
                          <div class="flex justify-between gap-2">
                            <span>Bedarf</span>
                            <span class="font-bold text-slate-700">${formatDE(total)} kWh</span>
                          </div>

                          <div class="flex justify-between gap-2">
                            <span class="flex items-center gap-1">
                              <div class="flex space-x-[-4px]">
                                <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.primary}"></div>
                                <div class="w-2 h-2 rounded-full border border-white" style="background:${theme.secondary}"></div>
                              </div>
                              PV / Akku
                            </span>
                            <span class="font-bold" style="color:${theme.primary}">
                              ${formatDE(season.DirektDeckung + season.BatterieDeckung)} kWh
                            </span>
                          </div>

                          <div class="flex justify-between gap-2">
                            <span class="flex items-center gap-1">
                              <div class="w-2 h-2 rounded-full" style="background:${theme.inactive}"></div>
                              Netzbezug
                            </span>
                            <span class="font-bold text-slate-700">
                              ${formatDE(season.Netzbezug)} kWh (${pZukauf}%)
                            </span>
                          </div>

                          <div class="flex justify-between gap-2 pt-1 mt-0.5 border-t border-slate-200" style="color:${theme.secondary}">
                            <span>Einspeisung</span>
                            <span class="font-bold">
                              ${formatDE(Math.abs(season.NetzeinspeisungNeg))} kWh (${pEinspeisung}%*)
                            </span>
                          </div>
                        </div>
                      </div>
                    `;
                  }).join('')}
                </div>

                <div class="text-[6px] text-center text-slate-400 font-medium mt-2">
                  * Der Prozentwert der Einspeisung bezieht sich auf den gesamten Solarertrag der jeweiligen Jahreszeit.
                </div>
              </div>

              ${ReportFooter()}
            </div>
 

            <div class="a4-page flex flex-col bg-white overflow-hidden">
              ${ReportHeader('4. IHR PARTNER FÜR DIE ENERGIEWENDE')}

              <h2 class="text-[16px] font-black mb-3 leading-tight" style="color:${theme.primary}">
                4. ${theme.name} & IHRE TECHNOLOGIE-VORTEILE
              </h2>

              <h3 class="text-[11px] font-bold text-slate-600 mb-2 border-b border-slate-200 pb-1">
                Darum ${theme.name} – Ihr Partner für die Energiewende
              </h3>

              <div class="grid grid-cols-2 gap-2 mb-3">
                ${[
                  ['award','Meisterbetrieb SHK & Elektro','Höchste handwerkliche Präzision durch unsere gewerkeübergreifende Meisterkompetenz.'],
                  ['shieldCheck','Alles aus einer Hand','Ein einziger, verlässlicher Ansprechpartner für Beratung, Planung, Fördermittelservice und Installation.'],
                  ['star','Premium Produktqualität','Wir verbauen ausschließlich marktführende, langlebige und erprobte Komponenten.'],
                  ['wrench','Langjährige Erfahrung','Hunderte erfolgreich realisierte Projekte und tiefes technisches Know-how.']
                ].map(item => `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 min-h-[88px]">
                    <span class="w-5 h-5 shrink-0 mt-0.5" style="color:${theme.primary}">
                      ${Icons[item[0]]()}
                    </span>
                    <div>
                      <h4 class="font-bold text-slate-600 text-[13px] mb-1">${item[1]}</h4>
                      <p class="text-[11px] text-slate-600 leading-relaxed">${item[2]}</p>
                    </div>
                  </div>
                `).join('')}
              </div>

              <h3 class="text-[11px] font-bold text-slate-600 mb-2 border-b border-slate-200 pb-1">
                Die Bausteine Ihres intelligenten Systems
              </h3>

              <div class="space-y-2 flex-1">
                ${config.moduleWP ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.thermoSnow()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-slate-600 text-[13px] mb-0.5">Wärmepumpe (Heizen & Kühlen)</h4>
                      <p class="text-[11px] text-slate-600 leading-relaxed">Nutzt kostenlose Umweltenergie hochgradig effizient.</p>
                    </div>
                  </div>
                ` : ''}

                ${config.modulePV ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.sun()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-slate-600 text-[13px] mb-0.5">Photovoltaik & Batteriespeicher</h4>
                      <p class="text-[11px] text-slate-600 leading-relaxed">Macht Ihr Dach zum eigenen Kraftwerk und speichert Sonnenstrom für die Nacht.</p>
                    </div>
                  </div>
                ` : ''}

                ${config.moduleWB ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.car()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-slate-600 text-[13px] mb-0.5">E-Mobilität (Wallbox)</h4>
                      <p class="text-[11px] text-slate-600 leading-relaxed">Ihre private Tankstelle direkt vor der Tür. Tanken Sie Ihr E-Auto bequem zu Hause.</p>
                    </div>
                  </div>
                ` : ''}

                ${activeModulesCount > 1 ? `
                  <div class="bg-white p-3 border border-slate-200 rounded-xl flex gap-3 items-center min-h-[64px]">
                    <div class="p-2 rounded-lg shrink-0" style="background:${theme.bgLight}">
                      <span class="w-4 h-4 block" style="color:${theme.primary}">${Icons.network()}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-slate-600 text-[13px] mb-0.5">Intelligente Sektorenkopplung</h4>
                      <p class="text-[11px] text-slate-600 leading-relaxed">Strom, Wärme und Mobilität werden intelligent vernetzt, damit Ihr Eigenverbrauch maximiert wird.</p>
                    </div>
                  </div>
                ` : ''}
              </div>

              ${ReportFooter()}
            </div>

            ${config.modulePV ? `
                <div class="a4-page flex flex-col bg-white relative overflow-hidden">
                  ${ReportHeader('5. PHOTOVOLTAIK & SPEICHER')}

                  <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                    <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                      5. PHOTOVOLTAIK & BATTERIESPEICHER
                    </h2>

                    <div class="mb-3">
                      <h3 class="text-[19px] font-black mb-1.5 leading-tight" style="color:${theme.primary}">
                        Für alle, die die Sonne optimal nutzen wollen.
                      </h3>
                      <p class="text-[10.5px] text-slate-600 leading-relaxed bg-white px-4 py-3 rounded-xl border border-slate-200">
                        <strong class="text-slate-700 block mb-0.5">Eine schlaue Entscheidung:</strong>
                        Solarstrom zum Eigenverbrauch ist die Energielösung der Zukunft. Mit einer Photovoltaikanlage nutzen Sie den erzeugten Strom tagsüber direkt. Unsere Systeme können durch einen Batteriespeicher sinnvoll ergänzt werden, um die Eigenversorgung weiter zu steigern.
                      </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 mb-3">
                      <div class="rounded-xl p-4 border bg-white flex flex-col" style="border-color:${theme.secondary}50">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit bg-white border" style="border-color:${theme.secondary}50">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.battery()}</span>
                        </div>

                        <h3 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug">
                          Maximaler Nutzen durch Batteriespeicher und Energiemanagement
                        </h3>

                        <p class="text-[10px] text-slate-600 leading-relaxed">
                          <strong class="text-slate-700 block mb-0.5">Effizienter geht’s nicht:</strong>
                          In Kombination mit einem Batteriespeicher wird ein größerer Anteil Ihres Solarstroms direkt im eigenen Haus genutzt. Das steigert den Eigenverbrauch und reduziert die Abhängigkeit vom öffentlichen Netz deutlich.
                        </p>

                        <p class="text-[10px] text-slate-600 leading-relaxed mt-1.5">
                          So entsteht ein modernes Energiesystem, das Erzeugung, Verbrauch und Speicherung intelligent miteinander verbindet.
                        </p>
                      </div>

                      <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.sun()}</span>
                        </div>

                        <h3 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug uppercase tracking-[0.05em]">
                          Mehr Eigenverbrauch bringt mehr Unabhängigkeit
                        </h3>

                        <p class="text-[10px] text-slate-600 leading-relaxed">
                          <strong class="text-slate-700 block mb-0.5">Selbst produziert für die eigene Steckdose:</strong>
                          Der Eigenverbrauch zeigt, wie viel Ihres Strombedarfs direkt durch selbst erzeugten Solarstrom gedeckt wird. Er hängt von Haushaltsgröße, Geräten und Nutzungsgewohnheiten ab.
                        </p>

                        <p class="text-[10px] text-slate-600 leading-relaxed mt-1.5">
                          Die Strommenge Ihrer PV-Anlage wird durch Leistung, Dachausrichtung, Komponentenqualität, Montage, Standort und Wetter beeinflusst.
                        </p>
                      </div>
                    </div>

                    <div class="mt-auto">
                      <div class="rounded-xl p-4 border relative overflow-hidden bg-white" style="border-color:${theme.primary}">
                        <div class="absolute top-0 left-0 w-24 h-24 rounded-full blur-[40px] opacity-20" style="background:${theme.primary}"></div>

                        <div class="relative z-10">
                          <h4 class="font-bold text-[11px] text-slate-700 mb-1.5 uppercase tracking-[0.08em]">
                            Ihr individuelles Dachpotenzial
                          </h4>

                          <p class="text-[10.2px] text-slate-700 leading-relaxed font-medium">
                            ${
                              derivedParams.isEastWestProfile
                                ? `<span>Mit einer <strong style="color:${theme.primary}">Ost-West-Belegung</strong> profitieren Sie von einem breiten Erzeugungsprofil. Der Solarstrom steht morgens früher und abends länger zur Verfügung. Dadurch sinkt der Bedarf an später Batterieleistung und Ihr direkter Eigenverbrauch steigt spürbar.</span>`
                                : derivedParams.hasSued
                                  ? `<span>Mit einer starken Ausrichtung nach <strong style="color:${theme.primary}">Süden</strong> erzielen Sie hohe Stromerträge rund um die Mittagszeit. Diese Überschüsse werden gezielt im Batteriespeicher gepuffert, damit Ihr Haus auch in den Abend- und Nachtstunden möglichst lange mit eigenem Strom versorgt wird.</span>`
                                  : `<span>Durch die optimale Belegung der verfügbaren Dachflächen holen wir das Beste aus der Sonne für Sie heraus. ${theme.name} berechnet die Modulverteilung so, dass Ihr Speicher auch in schwächeren Zeiten möglichst effektiv geladen wird.</span>`
                            }
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  ${ReportFooter()}
                </div>
              ` : ''}

            ${config.moduleWP ? `
                <div class="a4-page flex flex-col bg-white relative">
                  ${ReportHeader('WÄRMEPUMPEN-TECHNOLOGIE')}

                  <div class="flex-1 pb-[22mm]">
                    <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                      DAS PERFEKTE TEAM: INTELLIGENTE AUTARKIE
                    </h2>

                    <div class="mb-3">
                      <h3 class="text-[18px] font-black mb-2 leading-tight" style="color:${theme.primary}">
                        Ein perfektes Team: Wärmepumpen ${config.modulePV ? 'mit Solarstrom versorgen und ' : ''}Heizkosten minimieren.
                      </h3>

                      <div class="text-[10px] text-slate-600 leading-relaxed space-y-1.5">
                        <p>
                          Die optimale Nutzung der Umweltenergie wird durch die Wärmepumpe realisiert. Sie gewinnt Wärme aus Luft oder Boden, um Gebäude zu heizen und Trinkwasser zu erwärmen. Für den Betrieb benötigt sie elektrischen Strom.
                        </p>
                        ${config.modulePV ? `
                          <p>
                            Wird dieser Strom durch die eigene Photovoltaikanlage erzeugt und zusätzlich gespeichert, lässt sich ein Einfamilienhaus in weiten Teilen energieautark versorgen.
                          </p>
                        ` : ''}
                        <p>
                          Eine Wärmepumpe macht unabhängig von fossilen Brennstoffen und trägt aktiv zur Reduzierung des CO₂-Ausstoßes bei.
                        </p>
                        <p class="font-bold px-3 py-2.5 rounded-xl border border-slate-200" style="color:${theme.primary}">
                          Wir zeigen Ihnen, wie leicht man Öl, Gas und Kohle in den Schatten stellen kann – effizient, modern und alltagstauglich mit Wärmepumpensystemen aus dem Hause ${theme.name}.
                        </p>
                      </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 mb-3">
                      <div class="bg-white border border-slate-200 rounded-xl p-3.5 flex flex-col">
                        <div class="mb-2 p-2 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.piggyBank()}</span>
                        </div>
                        <h3 class="text-[14px] font-black text-slate-700 mb-1 leading-snug">
                          Wie Sie mit effizienter Technik sparen können
                        </h3>
                        <p class="text-[10px] text-slate-600 leading-relaxed">
                          Eine Wärmepumpe produziert einen Großteil der Energie aus der Umgebungsluft. Für den elektrischen Antrieb wird nur ein vergleichsweise kleiner Anteil Strom benötigt.
                        </p>
                        <p class="text-[10px] text-slate-600 leading-relaxed mt-1">
                          Die hocheffiziente Nutzung dieser Technologie bringt ökologische und wirtschaftliche Vorteile. Sie senken Ihre laufenden Betriebskosten und machen sich langfristig unabhängiger von steigenden Energiepreisen.
                        </p>
                      </div>

                      <div class="rounded-xl p-3.5 border flex flex-col" style="border-color:${theme.secondary}50">
                        <div class="mb-2 p-2 rounded-lg inline-block w-fit bg-white border" style="border-color:${theme.secondary}50">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.cpu()}</span>
                        </div>
                        <h3 class="text-[14px] font-black text-slate-700 mb-1 leading-snug">
                          Wärmepumpen sind intelligente Heizsysteme
                        </h3>
                        <div class="text-[8px] font-bold tracking-[0.12em] uppercase mb-1.5" style="color:${theme.primary}">
                          Diese Systemeffizienz nennen wir intelligente Autarkie
                        </div>
                        <p class="text-[10px] text-slate-600 leading-relaxed">
                          Eine Wärmepumpe kann mehr als nur umweltfreundlich heizen und Warmwasser bereiten. Je nach Technologie und Ausführung sind zusätzliche Funktionen wie Kühlen oder Lüften möglich.
                        </p>
                        <p class="text-[10px] text-slate-600 leading-relaxed mt-1">
                          So machen Sie Ihr Objekt zukunftssicher – mit einer Technik, die Komfort, Effizienz und Nachhaltigkeit verbindet.
                        </p>
                      </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-3.5 flex flex-col gap-2.5">
                      <h3 class="font-bold text-[11px] text-slate-700 border-b border-slate-100 pb-1.5 flex items-center gap-2">
                        <span class="w-4 h-4" style="color:${theme.primary}">${Icons.thermoSnow()}</span>
                        Witterung & saisonale Verteilung am Standort ${config.plz}
                      </h3>

                      <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                          <div>
                            <h4 class="text-[10px] font-bold text-slate-700 mb-0.5">
                              Normaußentemperatur (NAT: ${derivedParams.klima.nat} °C)
                            </h4>
                            <p class="text-[8.5px] text-slate-600 leading-relaxed">
                              Die NAT ist die tiefste Temperatur, die an Ihrem Wohnort an zwei aufeinanderfolgenden Tagen zu erwarten ist. Sie ist ein zentraler Wert für die sichere Auslegung Ihrer Wärmepumpe.
                            </p>
                          </div>

                          <div>
                            <h4 class="text-[10px] font-bold text-slate-700 mb-0.5">
                              Heizgradtage (HGT: ${derivedParams.klima.hgt} Kd)
                            </h4>
                            <p class="text-[8.5px] text-slate-600 leading-relaxed">
                              Dieser Wert beschreibt, wie streng der Winter an Ihrem Standort ausfällt. Er hilft dabei, den tatsächlichen Energiebedarf regional realistisch einzuordnen.
                            </p>
                          </div>
                        </div>

                        <div class="flex flex-col justify-center">
                          <div class="flex justify-between items-end mb-1">
                            <span class="text-[10px] font-bold text-slate-700">
                              Verteilung Heizbedarf p.a.
                            </span>
                            <span class="text-[8px] font-medium text-slate-500">
                              ${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus))} kWh
                            </span>
                          </div>

                          <div class="flex w-full h-3.5 rounded-full overflow-hidden shadow-inner mb-1">
                            <div class="flex items-center justify-center text-[7px] text-slate-700 font-bold bg-slate-300" style="width:45%">45%</div>
                            <div class="flex items-center justify-center text-[7px] text-slate-700 font-bold" style="width:22%;background:${theme.secondary}">22%</div>
                            <div class="flex items-center justify-center text-[7px] text-white font-bold" style="width:8%;background:${theme.primary}">8%</div>
                            <div class="flex items-center justify-center text-[7px] text-white font-bold" style="width:25%;background:#64748b">25%</div>
                          </div>

                          <div class="grid grid-cols-4 gap-1 text-[7px] text-slate-600 font-medium">
                            <div class="text-center">
                              <span class="block">Winter</span>
                              <span class="opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.45))} kWh</span>
                            </div>
                            <div class="text-center">
                              <span class="block">Frühling</span>
                              <span class="opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.22))} kWh</span>
                            </div>
                            <div class="text-center">
                              <span class="block">Sommer</span>
                              <span class="opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.08))} kWh</span>
                            </div>
                            <div class="text-center">
                              <span class="block">Herbst</span>
                              <span class="opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.25))} kWh</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  ${ReportFooter()}
                </div>
              ` : ''}

            ${config.moduleWB ? `
                <div class="a4-page flex flex-col bg-white relative overflow-hidden">
                  ${ReportHeader('INTELLIGENTE E-MOBILITÄT')}

                  <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                    <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                      E-MOBILITÄT: ZAPFEN SIE DIE SONNE AN
                    </h2>

                    <div class="mb-3">
                      <h3 class="text-[19px] font-black mb-1.5 leading-tight uppercase" style="color:${theme.primary}">
                        Die Zukunft fährt elektrisch
                      </h3>

                      <p class="text-[10.5px] text-slate-600 leading-relaxed bg-white px-4 py-3 rounded-xl border border-slate-200">
                        <strong class="text-slate-700 block mb-0.5">
                          Wie Sie die Energie ${config.modulePV ? 'der Sonne sogar ' : ''}auf die Straße bringen
                        </strong>
                        Mit einer Ladestation für Elektroautos wird moderne Mobilität Teil Ihres Gesamtsystems. Wir planen Ihre Wallbox passend zu Ihrem Bedarf – idealerweise als komfortable „Zapfsäule“ für den eigenen ${config.modulePV ? 'Solarstrom' : 'Hausstrom'}.
                      </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 mb-3">
                      <div class="rounded-xl p-4 border flex flex-col bg-white">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.leaf()}</span>
                        </div>

                        <h4 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug">
                          Ökologisch & wirtschaftlich
                        </h4>

                        <p class="text-[10px] text-slate-600 leading-relaxed">
                          Elektromobilität verbindet Klimaschutz mit Komfort. Wer zu Hause lädt, nutzt Energie bewusster und macht einen wichtigen Schritt in Richtung einer sauberen, CO₂-armen Zukunft.
                        </p>

                        <p class="text-[10px] text-slate-600 leading-relaxed mt-1.5">
                          Gleichzeitig laden Sie Ihr Fahrzeug bequem am eigenen Standort. Das ist alltagstauglich, spart Wege zu öffentlichen Ladesäulen und macht Ihr Zuhause zum eigenen Energiepunkt.
                        </p>
                      </div>

                      <div class="rounded-xl p-4 border flex flex-col bg-white" style="border-color:${theme.secondary}50">
                        <div class="mb-2 p-2.5 rounded-lg inline-block w-fit bg-white border" style="border-color:${theme.secondary}50">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.trendingUp()}</span>
                        </div>

                        <h4 class="text-[14px] font-black text-slate-700 mb-1.5 leading-snug">
                          Rentabilität maximieren
                        </h4>

                        <p class="text-[10px] text-slate-600 leading-relaxed">
                          Mit einem Elektroauto erhöhen Sie den wirtschaftlichen Nutzen Ihres Gesamtsystems. Anstelle schwankender Kraftstoffpreise laden Sie Ihr Fahrzeug günstig über Ihre eigene Ladeinfrastruktur.
                        </p>

                        ${config.modulePV ? `
                          <div class="mt-2 px-3 py-2 rounded-lg text-[9.5px] font-bold leading-relaxed" style="background:${theme.primary};color:white">
                            Besonders effizient: Das Fahrzeug wird bevorzugt mit überschüssigem Sonnenstrom geladen.
                          </div>
                        ` : `
                          <p class="text-[10px] text-slate-600 leading-relaxed mt-1.5">
                            Schon ohne PV schafft eine eigene Wallbox mehr Komfort, mehr Kontrolle und eine verlässliche Grundlage für zukünftige Mobilität.
                          </p>
                        `}
                      </div>
                    </div>

                    <div class="mt-auto">
                      <div class="rounded-xl p-4 border flex gap-4 items-start relative overflow-hidden bg-white" style="border-color:${theme.primary}">
                        <div class="absolute top-0 right-0 w-24 h-24 rounded-full blur-[40px] opacity-20" style="background:${theme.primary}"></div>

                        <div class="p-2.5 rounded-full shrink-0 relative z-10" style="background:${theme.bgLight}">
                          <span class="w-5 h-5" style="color:${theme.primary}">${Icons.car()}</span>
                        </div>

                        <div class="relative z-10">
                          <h4 class="font-bold text-[11px] text-slate-700 mb-1.5 uppercase tracking-[0.08em]">
                            Intelligentes Laden für den Alltag
                          </h4>

                          <p class="text-[10.2px] text-slate-700 leading-relaxed font-medium">
                            <strong style="color:${theme.primary}">
                              Mit intelligenten Ladelösungen, durchdachter Vernetzung und effizienter Steuerung
                            </strong>
                            entsteht eine komfortable, wirtschaftliche und zukunftssichere Ladeumgebung direkt bei Ihnen zu Hause.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>

                  ${ReportFooter()}
                </div>
              ` : ''}

           ${activeModulesCount > 1 ? `
              <div class="a4-page flex flex-col bg-white overflow-hidden relative">
                ${ReportHeader('DAS GESAMTSYSTEM')}

                <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                  <h2 class="text-[17px] font-black text-[${theme.primary}] mb-3 uppercase tracking-[0.14em]">
                    SEKTORENKOPPLUNG: EIGENE ENERGIE
                  </h2>

                  <div class="mb-4">
                    <h3 class="text-[24px] font-black leading-tight" style="color:${theme.primary}">
                      Eigener Strom.
                    </h3>
                    <h3 class="text-[24px] font-black leading-tight" style="color:${theme.inactive}">
                      Eigene Wärme.
                    </h3>
                    <h3 class="text-[24px] font-black mb-2 leading-tight" style="color:${theme.secondary}">
                      Eigene Energie.
                    </h3>

                    <p class="text-[10.5px] text-slate-600 leading-relaxed font-medium bg-white px-4 py-3 rounded-xl border border-slate-200">
                      Ganz autark sein, frei von teuren Energieanbietern und dabei ohne Komfortverlust:
                      Durch die intelligente Kombination Ihrer Systeme entsteht ein persönliches
                      Energie-Effizienzhaus mit klarer Struktur, hoher Eigenversorgung und dauerhaft mehr Kontrolle.
                    </p>
                  </div>

                  <div class="grid grid-cols-1 gap-3 mb-4">
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                      <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                        <span class="w-5 h-5" style="color:${theme.primary}">${Icons.network()}</span>
                      </div>

                      <h4 class="text-[14px] font-black text-slate-700 mb-1.5">
                        Synergien nutzen & Kosten minimieren
                      </h4>

                      <p class="text-[10px] text-slate-600 leading-relaxed">
                        Die ganzheitliche Verbindung von Strom, Wärme und Mobilität hebt die Gesamtenergieeffizienz deutlich an.
                        Energie wird dort eingesetzt, wo sie den größten Nutzen bringt – wirtschaftlich, nachhaltig und technisch sauber abgestimmt.
                      </p>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                      <div class="mb-2 p-2.5 rounded-lg inline-block w-fit" style="background:${theme.bgLight}">
                        <span class="w-5 h-5" style="color:${theme.primary}">${Icons.sun()}</span>
                      </div>

                      <h4 class="text-[14px] font-black text-slate-700 mb-1.5">
                        Das ideale Zusammenspiel
                      </h4>

                      <p class="text-[10px] text-slate-600 leading-relaxed">
                        Das abgestimmte Gesamtsystem steuert Energieflüsse exakt dorthin, wo sie im Alltag gebraucht werden.
                        So werden Eigenverbrauch, Komfort und Unabhängigkeit gleichzeitig verbessert.
                      </p>
                    </div>
                  </div>

                  <div class="mt-auto">
                    <div class="rounded-xl px-5 py-4 border relative overflow-hidden bg-white" style="border-color:${theme.secondary}50">
                      <div class="absolute -right-6 -top-6 opacity-10">
                        <span class="w-[150px] h-[150px] inline-block" style="color:${theme.primary}">
                          ${Icons.infinity()}
                        </span>
                      </div>

                      <div class="relative z-10">
                        <h4 class="text-[14px] font-black mb-1.5" style="color:${theme.primary}">
                          Sektorkopplung funktioniert wie ein starkes Team
                        </h4>
                        <p class="text-[10px] text-slate-700 leading-relaxed">
                          Erst wenn alle Zahnräder präzise ineinandergreifen, entsteht das beste Ergebnis für Ihr Zuhause:
                          mehr Eigenverbrauch, weniger externe Energiebezüge und ein System, das wirtschaftlich wie technisch aus einem Guss arbeitet.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                ${ReportFooter()}
              </div>
            ` : ''}

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('WIRTSCHAFTLICHKEIT')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black mb-3 text-[${theme.primary}] uppercase tracking-[0.14em]">
                  INVESTITION & WIRTSCHAFTLICHKEIT (BREAK-EVEN)
                </h2>

                <div class="bg-white rounded-xl border border-slate-200 mb-2.5 overflow-hidden shrink-0">
                  <div class="px-4 py-2 border-b border-slate-100 bg-white flex justify-between items-center">
                    <h3 class="text-[11px] font-bold text-slate-600 uppercase tracking-[0.12em]">
                      Ihre Netto-Investition im Detail
                    </h3>
                  </div>

                  <div class="p-0 overflow-hidden">
                    <table class="w-full text-[8px] text-left leading-tight">
                      <thead class="bg-white text-slate-500 text-[8px] uppercase tracking-[0.1em] border-b border-slate-100">
                        <tr>
                          <th class="px-3 py-1.5 pl-4 font-semibold">Komponente</th>
                          <th class="px-2 py-1.5 font-semibold text-right">Brutto</th>
                          <th class="px-2 py-1.5 font-semibold text-right" style="color:${theme.primary}">KfW</th>
                          <th class="px-2 py-1.5 font-semibold text-right" style="color:${theme.primary}">Zusatz</th>
                          <th class="px-2 py-1.5 font-semibold text-right" style="color:${theme.primary}">Rabatt*</th>
                          <th class="px-3 py-1.5 pr-4 font-black text-right text-slate-700">Netto</th>
                        </tr>
                      </thead>

                      <tbody class="divide-y divide-slate-100 text-slate-700">
                        ${config.moduleWP ? `
                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Wärmepumpe</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costWP)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.kfwZuschuss)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantWPNum)} €
                            ${config.extraGrantSourceWP ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourceWP}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountWPNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoWP)} €</td>
                        </tr>
                        ` : ''}

                        ${config.modulePV ? `
                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Photovoltaik (${derivedParams.pvKwp} kWp)</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costPV)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantPVNum)} €
                            ${config.extraGrantSourcePV ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourcePV}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountPVNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoPV)} €</td>
                        </tr>

                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Speicher (${derivedParams.batteryCapacity} kWh)</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costBattery)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantBatteryNum)} €
                            ${config.extraGrantSourceBattery ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourceBattery}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountBatteryNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoBattery)} €</td>
                        </tr>
                        ` : ''}

                        ${config.moduleWB ? `
                        <tr>
                          <td class="px-3 py-1.5 pl-4 font-medium">Wallbox</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(config.costWallbox)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">
                            -${formatDE(finance.extraGrantWallboxNum)} €
                            ${config.extraGrantSourceWallbox ? `<span class="block text-[6px] opacity-70 mt-0.5 leading-tight">${config.extraGrantSourceWallbox}</span>` : ''}
                          </td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.discountWallboxNum)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right font-bold" style="color:${theme.primary}">${formatDE(finance.nettoWallbox)} €</td>
                        </tr>
                        ` : ''}
                      </tbody>

                      <tfoot class="bg-white font-black text-[10px] border-t-2 border-slate-200">
                        <tr>
                          <td class="px-3 py-1.5 pl-4">Gesamtinvestition</td>
                          <td class="px-2 py-1.5 text-right">${formatDE(finance.totalInvest)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.kfwZuschuss)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.totalExtraGrant)} €</td>
                          <td class="px-2 py-1.5 text-right" style="color:${theme.primary}">-${formatDE(finance.totalDiscount)} €</td>
                          <td class="px-3 py-1.5 pr-4 text-right" style="color:${theme.primary}">${formatDE(finance.nettoInvest)} €</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                <div class="text-[8.5px] text-slate-500 mb-2.5 leading-relaxed shrink-0">
                  * <strong class="text-slate-700">Kombi-Bonus Bedingung:</strong>
                  Die ausgewiesenen ${theme.name} Rabatte gelten nur bei gleichzeitiger Beauftragung aller Sektoren
                  als Gesamtsystem. Bei Einzelbeauftragungen entfallen diese Sonderkonditionen.
                  ${finance.isKombiBonusActive
                    ? `<span class="font-bold" style="color:${theme.primary}"> In Ihrer aktuellen Konfiguration ist der Kombi-Bonus aktiv.</span>`
                    : `<span class="text-red-500 font-bold"> In Ihrer aktuellen Konfiguration ist der Kombi-Bonus nicht aktiv.</span>`
                  }
                </div>

                <div class="bg-white px-4 py-3 rounded-xl border border-slate-200 mb-2.5 shrink-0">
                  <h3 class="text-[10px] font-bold text-slate-700 mb-1">
                    Ihr finanzieller Break-Even
                  </h3>
                  <p class="text-[8.5px] text-slate-500 mb-2 leading-relaxed">
                    Die farbige Linie zeigt Ihre Kosten im neuen System. Die graue Linie zeigt das heutige System mit Inflation.
                    Der Schnittpunkt markiert den <strong>Break-Even-Point</strong>.
                  </p>

                  <div class="h-[130px] w-full">
                    <div class="chart-wrap"><canvas id="financeLineChart"></canvas></div>
                  </div>
                </div>

                <div class="grid grid-cols-3 gap-2 mt-auto shrink-0">
                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white flex flex-col justify-center">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">
                      ${finance.amortisationYear ? `${finance.amortisationYear} J.` : '&gt; 30 J.'}
                    </div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">
                      Amortisation
                    </div>
                  </div>

                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white flex flex-col justify-center">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">
                      ${finance.roi}%
                    </div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">
                      Rendite p.a.
                    </div>
                  </div>

                  <div class="text-center px-3 py-2.5 border border-slate-200 rounded-xl bg-white relative overflow-hidden flex flex-col justify-center">
                    <div class="text-[20px] font-black leading-none mb-1" style="color:${theme.primary}">
                      ${config.modulePV ? finance.lcoe : '-'} €
                    </div>
                    <div class="text-[8px] font-bold tracking-[0.12em] text-slate-500 uppercase">
                      Stromgestehungskosten / kWh
                    </div>
                    <div class="absolute top-0 right-0 text-[6px] font-bold px-1.5 py-0.5 rounded-bl-lg" style="background:${theme.bgLight};color:${theme.primary}">
                      Basis: 30 Jahre
                    </div>
                  </div>
                </div>
              </div>

              ${ReportFooter()}
            </div>

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('TRANSPARENZ: TECHNISCHE BERECHNUNGEN')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[16px] font-black text-[${theme.primary}] mb-2 uppercase tracking-[0.14em]">
                  Transparenz: Technische Berechnungen
                </h2>

                <p class="text-[9px] text-slate-600 mb-2.5 leading-relaxed">
                  Vertrauen erfordert Nachvollziehbarkeit. Auf dieser Seite legen wir die wichtigsten Berechnungsgrundlagen,
                  Formeln und regionalen Klimadaten für das Objekt in <strong>${config.plz}</strong> offen, die zu Ihrer
                  Systemauslegung geführt haben.
                </p>

                <div class="space-y-2.5 flex-1 min-h-0">

                  ${config.moduleWP ? `
                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.thermometer()}</span>
                      1. Ermittlung des Systemverlusts (Altsystem)
                    </h3>

                    <p class="text-[8.5px] text-slate-600 mb-1.5 leading-relaxed">
                      Jede Heizanlage verliert mit den Jahren an Effizienz. Dieser Verlust wird berücksichtigt, um den
                      <strong>tatsächlichen thermischen Nutzenergiebedarf</strong> des Hauses zu ermitteln.
                    </p>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Aktueller Heizungs-Typ:</span>
                        <strong class="shrink-0">${config.heizungArt}</strong>
                      </div>
                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Alter der Heizung:</span>
                        <strong class="shrink-0">${config.heizungAlter} Jahre</strong>
                      </div>

                      <div class="col-span-2 bg-white p-2 border border-slate-200 rounded mt-0.5">
                        <span class="block text-[7px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">
                          Formel: Systemverlust durch Alterung
                        </span>
                        <div class="text-[8.5px] font-bold text-slate-600 leading-relaxed">
                          Bisheriger Verbrauch (${config.heizVerbrauch} ${getHeizEinheit(config.heizungArt)}) ×
                          angenommener Verlust (${derivedParams.systemVerlust * 100}%) = tatsächlicher Wärmebedarf
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.calculator()}</span>
                      2. Wärmebedarf & Heizlast
                    </h3>

                    <div class="space-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between gap-3">
                        <span>Tatsächlicher Wärmebedarf gesamt (inkl. Warmwasser):</span>
                        <strong class="shrink-0">${formatDE(Math.round(derivedParams.thermischHauptsystem))} kWh</strong>
                      </div>

                      <div class="flex justify-between gap-3 text-slate-500">
                        <span class="flex items-center gap-1.5">
                          <span class="w-3 h-3 shrink-0">${Icons.users()}</span>
                          Anteil Warmwasser (${config.personen} Personen á 800 kWh)
                        </span>
                        <strong class="shrink-0">- ${config.warmwasserArt === 'Zentral' ? config.personen * 800 : 0} kWh</strong>
                      </div>

                      ${config.zirkulation && config.warmwasserArt === 'Zentral' ? `
                        <div class="flex justify-between gap-3 text-slate-500">
                          <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 shrink-0">${Icons.droplet()}</span>
                            Zirkulationsverlust Warmwasser
                          </span>
                          <strong class="shrink-0">- 600 kWh</strong>
                        </div>
                      ` : ''}

                      <div class="flex justify-between gap-3 text-slate-600 border-b border-slate-200 pb-1">
                        <span>Verbleibender Bedarf reine Raumheizung:</span>
                        <strong class="shrink-0">= ${formatDE(Math.round(derivedParams.heizWärmeBedarf))} kWh</strong>
                      </div>

                      <div class="bg-white p-2 border border-slate-200 rounded mt-1">
                        <span class="block text-[7px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">
                          Dimensionierung der Wärmepumpe (Schweizer Formel für ${config.plz})
                        </span>
                        <div class="text-[8.5px] mb-1 leading-relaxed text-slate-700">
                          ${Math.round(derivedParams.gesamtWaermeBedarfHaus)} kWh / ${derivedParams.klima.vbh} Vollbenutzungsstunden =
                          <strong style="color:${theme.primary}">${derivedParams.berechneteHeizlast} kW Heizlast</strong>
                        </div>
                        <div class="text-[7px] text-slate-500 leading-relaxed">
                          Klimabasis: NAT ${derivedParams.klima.nat} °C und Heizgradtage ${derivedParams.klima.hgt} Kd.
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.thermoSnow()}</span>
                      3. Ermittlung der Jahresarbeitszahl (JAZ)
                    </h3>

                    <p class="text-[8.5px] text-slate-600 mb-1.5 leading-relaxed">
                      Die Jahresarbeitszahl (JAZ) beschreibt, wie viele Kilowattstunden Wärme die Anlage aus einer
                      Kilowattstunde Strom erzeugt.
                    </p>

                    <div class="space-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between gap-3">
                        <span>COP Raumheizung (System: ${config.heizSystem})</span>
                        <strong class="shrink-0">${derivedParams.copSH.toFixed(2)}</strong>
                      </div>
                      <div class="flex justify-between gap-3">
                        <span>COP Warmwasserbereitung</span>
                        <strong class="shrink-0">${derivedParams.copWW.toFixed(2)}</strong>
                      </div>
                      <div class="flex justify-between gap-3 font-bold border-t border-slate-200 pt-1">
                        <span>Gewichtete Jahresarbeitszahl (JAZ)</span>
                        <span class="shrink-0" style="color:${theme.primary}">
                          ${config.customJAZ !== '' ? `${derivedParams.jaz} (Manuell)` : derivedParams.jaz}
                        </span>
                      </div>
                    </div>
                  </div>
                  ` : ''}

                  ${config.modulePV ? `
                  <div class="p-2.5 border border-slate-200 rounded-xl bg-white shrink-0">
                    <h3 class="font-bold text-slate-700 text-[10px] mb-1.5 flex items-center gap-2" style="color:${theme.primary}">
                      <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.sun()}</span>
                      4. Berechnung der PV- und Speichergröße
                    </h3>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[8.5px] text-slate-700">
                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Summe Strombedarf (Sektoren):</span>
                        <strong class="shrink-0">${formatDE(derivedParams.gesamtStrombedarf)} kWh</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Ertrag Basisfaktor (PLZ ${config.plz}):</span>
                        <strong class="shrink-0">${getRegionalFactors(config.plz).pvBaseFactor} kWh/kWp</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 col-span-2 items-start gap-4">
                        <span>Dachausrichtungen & Modul-Verteilung:</span>
                        <div class="text-right text-[7px] leading-relaxed">
                          ${derivedParams.distributedDachseiten.map(d => `
                            <div class="font-bold">
                              ${d.ausrichtung} (${d.neigung}°, ${d.eindeckung}):
                              <span style="color:${theme.primary}">${d.calculatedKwp} kWp</span>
                            </div>
                          `).join('')}
                        </div>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Gewichteter Dachfaktor:</span>
                        <strong class="shrink-0">${(derivedParams.avgYieldFactor * 100).toFixed(0)} %</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 gap-3">
                        <span>Effektiver Ertrag Objekt:</span>
                        <strong class="shrink-0">${Math.round(derivedParams.effectiveYieldPvKwp)} kWh/kWp</strong>
                      </div>

                      <div class="flex justify-between border-b border-slate-200 pb-1 font-bold col-span-2 gap-3">
                        <span>Installierte Gesamt-PV-Leistung:</span>
                        <span class="shrink-0" style="color:${theme.primary}">${derivedParams.pvKwp} kWp</span>
                      </div>
                    </div>
                  </div>
                  ` : ''}
                </div>
              </div>

              ${ReportFooter()}
            </div>

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('TRANSPARENZ: KENNZAHLEN & EFFIZIENZ')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[16px] font-black mb-2 uppercase tracking-[0.14em]" style="color:${theme.primary}">
                  Transparenz: Kennzahlen & Effizienz
                </h2>

                <p class="text-[9px] text-slate-600 leading-relaxed mb-2.5">
                  Die folgenden Kennzahlen machen Ihr Energiesystem transparent und vergleichbar. Sie zeigen, wie effizient Ihr
                  selbst erzeugter Strom genutzt wird, wie hoch Ihre Unabhängigkeit vom Netz ausfällt und welche finanziellen
                  Vorteile sich daraus ergeben.
                </p>

                <div class="space-y-2.5 flex-1 min-h-0">
                  <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.activity()}</span>
                        5. Eigenverbrauchsquote
                      </h3>

                      <p class="text-[8.5px] text-slate-600 mb-2 leading-relaxed">
                        Wie viel Prozent Ihres <strong>selbst produzierten Solarstroms</strong> nutzen Sie direkt im Haus oder im
                        Speicher, statt ihn ins Netz einzuspeisen?
                      </p>

                      <div class="bg-white p-2 border border-slate-200 rounded-lg">
                        <span class="block text-[7px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Formel</span>
                        <div class="text-[8.5px] text-slate-700 font-bold mb-1.5 leading-relaxed">
                          (Direktnutzung + Batterieladung) / PV-Gesamtertrag × 100
                        </div>
                        <div class="flex justify-between items-center text-[8.5px] text-slate-600 font-medium gap-3">
                          <span>Ergebnis:</span>
                          <span class="font-black shrink-0" style="color:${theme.primary}">${kpis.eigenverbrauchQuote} %</span>
                        </div>
                      </div>
                    </div>

                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.shieldCheck()}</span>
                        6. Autarkiegrad
                      </h3>

                      <p class="text-[8.5px] text-slate-600 mb-2 leading-relaxed">
                        Wie viel Prozent Ihres <strong>gesamten Strombedarfs</strong> werden durch die eigene PV-Anlage und den
                        Speicher gedeckt?
                      </p>

                      <div class="bg-white p-2 border border-slate-200 rounded-lg">
                        <span class="block text-[7px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Formel</span>
                        <div class="text-[8.5px] text-slate-700 font-bold mb-1.5 leading-relaxed">
                          (Direktnutzung + Batterieentladung) / Gesamtstrombedarf × 100
                        </div>
                        <div class="flex justify-between items-center text-[8.5px] text-slate-600 font-medium gap-3">
                          <span>Ergebnis:</span>
                          <span class="font-black shrink-0" style="color:${theme.primary}">${kpis.autarkie} %</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5" style="color:${theme.primary}">
                        7. Saisonale Autarkie
                      </h3>

                      <p class="text-[8.5px] text-slate-600 leading-relaxed">
                        Ein Jahresdurchschnitt kann täuschen: Im Sommer entstehen hohe Überschüsse, während im Winter Lastspitzen
                        auftreten. Die saisonale Autarkie zeigt, wie stabil Ihr System über das Jahr arbeitet und wie stark der
                        Speicher besonders in den dunkleren Monaten entlastet.
                      </p>
                    </div>

                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5" style="color:${theme.primary}">
                        8. Finanzielle Unabhängigkeit
                      </h3>

                      <p class="text-[8.5px] text-slate-600 leading-relaxed mb-2">
                        Diese Kennzahl zeigt Ihre Ersparnis im Verhältnis zu Ihren bisherigen jährlichen Energiekosten.
                      </p>

                      <div class="bg-white p-2 border border-slate-200 rounded-lg">
                        <span class="block text-[7px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Formel</span>
                        <div class="text-[8.5px] text-slate-700 font-bold mb-1.5 leading-relaxed">
                          (Ersparnis Jahr 1 / alte Energiekosten Jahr 1) × 100
                        </div>
                        <div class="flex justify-between items-center text-[8.5px] text-slate-600 font-medium gap-3">
                          <span>Ergebnis:</span>
                          <span class="font-black shrink-0" style="color:${theme.primary}">${finance.finUnabhProzent} %</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.secondary}">${Icons.zap()}</span>
                        9. §14a EnWG (Netzentgelte)
                      </h3>

                      <div class="space-y-1.5 text-[8.5px] text-slate-700">
                        <div class="flex justify-between gap-3">
                          <span>Angesetztes Netzentgelt (AP):</span>
                          <strong class="shrink-0">${config.netzentgelt.toLocaleString('de-DE')} €/kWh</strong>
                        </div>

                        <div class="flex justify-between gap-3 text-slate-500 border-b border-slate-200 pb-1.5">
                          <span>Steuerbare Einheit (SteuVE):</span>
                          <strong class="shrink-0">${finance.evKostenOhne > 0 || finance.wpKostenOhne > 0 ? 'Ja (WP/Wallbox)' : 'Keine'}</strong>
                        </div>

                        <div class="bg-white p-2 border border-slate-200 rounded-lg mt-0.5 space-y-1">
                          <span class="block text-[7px] text-slate-400 mb-0.5 uppercase tracking-[0.14em]">Modul 1 vs. Modul 2</span>

                          <div class="flex justify-between gap-3">
                            <span>Modul 1 (Pauschale):</span>
                            <strong class="shrink-0">160 €/a</strong>
                          </div>

                          <div class="flex justify-between gap-3">
                            <span>Modul 2 (60% auf Netzbezug):</span>
                            <strong class="shrink-0">${Math.round((finance.wpNetz + finance.evNetz) * config.netzentgelt * 0.6).toLocaleString('de-DE')} €/a</strong>
                          </div>

                          <div class="flex justify-between gap-3 font-bold border-t border-slate-200 pt-1.5" style="color:${theme.primary}">
                            <span>Angewandter Rabatt (Best-of):</span>
                            <span class="shrink-0">${finance.ersparnis14a.toLocaleString('de-DE')} €/a</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    ${config.moduleWP ? `
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white">
                      <h3 class="font-bold text-[10px] mb-1.5 flex items-center gap-1.5" style="color:${theme.primary}">
                        <span class="w-3.5 h-3.5 shrink-0" style="color:${theme.primary}">${Icons.euro()}</span>
                        10. KfW-Fördermittelaufbau
                      </h3>

                      <div class="space-y-1 text-[8.5px] text-slate-700">
                        <div class="flex justify-between gap-3">
                          <span>Basisförderung (Wärmepumpe):</span>
                          <strong class="shrink-0">30 %</strong>
                        </div>

                        <div class="flex justify-between gap-3">
                          <span>Effizienzbonus (natürliches Kältemittel):</span>
                          <strong class="shrink-0">+ 5 %</strong>
                        </div>

                        <div class="flex justify-between gap-3">
                          <span>Klimageschwindigkeitsbonus (Ersatz ${config.heizungArt}):</span>
                          <strong class="shrink-0">${finance.kfwDetails.klimaBonus > 0 ? '+ 20 %' : '+ 0 %'}</strong>
                        </div>

                        ${config.weUnter40k > 0 ? `
                          <div class="flex justify-between gap-3 font-medium" style="color:${theme.primary}">
                            <span>Einkommensbonus (Haushalt &lt; 40k €):</span>
                            <strong class="shrink-0">+ 30 %</strong>
                          </div>
                        ` : ''}

                        <div class="flex justify-between gap-3 font-bold border-t border-slate-200 pt-1.5 mt-0.5">
                          <span>Gesamter Fördersatz (max. 70%):</span>
                          <span class="shrink-0" style="color:${theme.primary}">${finance.maxZuschussProzent} %</span>
                        </div>

                        <div class="text-[7px] text-slate-500 mt-0.5 leading-relaxed">
                          Der Fördersatz wird auf die maximal förderfähigen Kosten von
                          ${finance.weDeckelung.toLocaleString('de-DE')} € bei ${config.wohneinheiten} Wohneinheiten angewendet.
                        </div>
                      </div>
                    </div>
                    ` : `
                    <div class="p-2.5 border border-slate-200 rounded-xl bg-white flex items-center justify-center">
                      <div class="text-center">
                        <div class="text-[9px] font-bold mb-1" style="color:${theme.primary}">Förderübersicht</div>
                        <p class="text-[8.5px] text-slate-500 leading-relaxed">
                          Die KfW-Förderstruktur wird nur angezeigt, wenn das Wärmepumpenmodul aktiv ist.
                        </p>
                      </div>
                    </div>
                    `}
                  </div>
                </div>
              </div>

              ${ReportFooter()}
            </div>

            <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('IHR ABSCHLUSS')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black text-[${theme.primary}] mb-3 uppercase tracking-[0.14em]">
                  KLIMASCHUTZ & WIE ES WEITERGEHT
                </h2>

                <div class="rounded-xl px-4 py-3 border flex items-start gap-4 mb-3" style="border-color:${theme.secondary}50">
                  <span class="w-9 h-9 shrink-0 mt-0.5" style="color:${theme.primary}">
                    ${Icons.leaf()}
                  </span>

                  <div class="flex-1 min-w-0">
                    <h3 class="text-[13px] font-bold mb-1" style="color:${theme.primary}">
                      Ihr aktiver Klimaschutz
                    </h3>

                    <p class="text-[9px] leading-relaxed mb-2.5 text-slate-600">
                      Neben der finanziellen Ersparnis leisten Sie einen spürbaren Beitrag für die nächste Generation.
                      Ihre jährliche Einsparung an CO₂-Emissionen entspricht der Speicherkraft von ca.
                      <strong style="color:${theme.primary}">${computed.co2.trees} Bäumen</strong>
                      oder einer Mischwaldfläche von
                      <strong style="color:${theme.primary}">${computed.co2.forestArea.toLocaleString('de-DE')} m²</strong>.
                    </p>

                    <div class="grid grid-cols-3 gap-2 text-center">
                      <div class="bg-white px-2 py-2 rounded-lg border" style="border-color:${theme.secondary}50">
                        <span class="text-[8px] font-bold uppercase block mb-0.5" style="color:${theme.primary}">
                          Pro Jahr
                        </span>
                        <span class="text-[18px] leading-none font-black" style="color:${theme.primary}">
                          ${computed.co2.year} t
                        </span>
                      </div>

                      <div class="bg-white px-2 py-2 rounded-lg border" style="border-color:${theme.secondary}50">
                        <span class="text-[8px] font-bold uppercase block mb-0.5" style="color:${theme.primary}">
                          10 Jahre
                        </span>
                        <span class="text-[18px] leading-none font-black" style="color:${theme.primary}">
                          ${computed.co2.tenYears} t
                        </span>
                      </div>

                      <div class="bg-white px-2 py-2 rounded-lg border" style="border-color:${theme.secondary}50">
                        <span class="text-[8px] font-bold uppercase block mb-0.5" style="color:${theme.primary}">
                          20 Jahre
                        </span>
                        <span class="text-[18px] leading-none font-black" style="color:${theme.primary}">
                          ${computed.co2.twentyYears} t
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <h3 class="text-[11px] font-bold text-slate-600 mb-2 border-b border-slate-200 pb-1.5">
                  Wie es jetzt für Sie weitergeht
                </h3>

                <div class="grid grid-cols-3 gap-3 mb-3">
                  <div class="flex flex-col items-center text-center px-1">
                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center font-black text-[15px] mb-2"
                        style="background:${theme.primary}">
                      1
                    </div>
                    <h4 class="font-bold text-slate-700 text-[10px] mb-1">Vor-Ort-Analyse</h4>
                    <p class="text-[8px] text-slate-600 leading-relaxed">
                      Wir prüfen die baulichen Gegebenheiten vor Ort und erstellen das finale, verbindliche Festpreisangebot.
                    </p>
                  </div>

                  <div class="flex flex-col items-center text-center relative px-1">
                    <div class="hidden md:block absolute top-[18px] -left-1/2 w-full h-0.5 bg-slate-200 -z-10"></div>
                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center font-black text-[15px] mb-2"
                        style="background:${theme.primary}">
                      2
                    </div>
                    <h4 class="font-bold text-slate-700 text-[10px] mb-1">Fördermittelservice</h4>
                    <p class="text-[8px] text-slate-600 leading-relaxed">
                      Wir übernehmen die Beantragung aller KfW-Zuschüsse, damit Ihre Förderung optimal ausgeschöpft wird.
                    </p>
                  </div>

                  <div class="flex flex-col items-center text-center relative px-1">
                    <div class="hidden md:block absolute top-[18px] -left-1/2 w-full h-0.5 bg-slate-200 -z-10"></div>
                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center mb-2"
                        style="background:${theme.primary};box-shadow:0 8px 14px -4px ${theme.primary}40">
                      <span class="w-4 h-4">${Icons.checkCircle()}</span>
                    </div>
                    <h4 class="font-bold text-slate-700 text-[10px] mb-1">Fachgerechte Installation</h4>
                    <p class="text-[8px] text-slate-600 leading-relaxed">
                      Unsere Meister montieren Ihr System schlüsselfertig. Nach der Inbetriebnahme produzieren Sie sofort eigenen Strom.
                    </p>
                  </div>
                </div>

                <div class="mt-auto rounded-xl border border-slate-200 px-4 py-3 bg-white">
                  <h3 class="text-[15px] font-black uppercase leading-tight" style="color:${theme.primary}">
                    Von der Sonne bekommen Sie die Energie.
                  </h3>
                  <h3 class="text-[15px] font-black mb-1 uppercase leading-tight" style="color:${theme.primary}">
                    Alles andere von uns.
                  </h3>

                  <h4 class="text-[8px] font-bold text-slate-600 mb-1.5 tracking-[0.12em] uppercase">
                    Solar + Wärmepumpe + Wallbox – als abgestimmtes Gesamtsystem aus einer Hand
                  </h4>

                  <p class="text-[8.5px] text-slate-600 leading-relaxed">
                    Die Kombination aus Photovoltaik, Wärmepumpe und Ladestation macht aus einzelnen Lösungen ein stimmiges
                    Gesamtsystem. Ihr Eigenstrom kann Ihren Bedarf sinnvoll abdecken und Sie gewinnen mehr Kontrolle über Ihre
                    Energieversorgung. Wir setzen das System fachgerecht um und begleiten Sie vom ersten Schritt bis zur
                    Inbetriebnahme.
                  </p>
                </div>
              </div>

              ${ReportFooter()}
            </div>

           <div class="a4-page flex flex-col bg-white overflow-hidden relative">
              ${ReportHeader('FINANZIERUNG & HINWEISE')}

              <div class="flex-1 flex flex-col min-h-0 pb-[18mm]">
                <h2 class="text-[17px] font-black text-[${theme.primary}] mb-3 uppercase tracking-[0.14em]">
                  IHR FINANZIERUNGSVORTEIL & RECHTLICHE HINWEISE
                </h2>

                <div class="border-2 bg-white px-4 py-4 rounded-2xl flex justify-between items-stretch gap-4 relative overflow-hidden mb-3"
                    style="border-color:${theme.primary}">
                  <div class="absolute top-0 right-0 w-40 h-40 rounded-full blur-[45px] opacity-15" style="background:${theme.primary}"></div>

                  <div class="relative z-10 flex-1 pr-4 border-r border-slate-200 flex flex-col justify-center min-w-0">
                    <div class="text-[8px] font-bold tracking-[0.14em] uppercase mb-2 inline-block px-2.5 py-1 rounded-full border w-fit"
                        style="color:${theme.primary};background:${theme.bgLight};border-color:${theme.secondary}50">
                      Ihr Finanzierungsvorteil
                    </div>

                    <span class="block text-[8px] font-bold text-slate-500 uppercase tracking-[0.14em] mb-0.5">
                      Ermittelte Gesamtförderung
                    </span>

                    <div class="text-[7px] text-slate-400 font-medium mb-1.5 border-b border-slate-200 pb-1 inline-block leading-relaxed">
                      Formel: ${finance.kfwZuschuss.toLocaleString('de-DE')} € + ${finance.totalExtraGrant.toLocaleString('de-DE')} € =
                    </div>

                    <div class="text-[22px] leading-none font-black" style="color:${theme.primary}">
                      ${finance.totalFoerderung.toLocaleString('de-DE')} €
                    </div>
                  </div>

                  <div class="relative z-10 flex-1 px-4 flex flex-col justify-center items-center text-center min-w-0">
                    <div class="text-[7px] font-bold tracking-[0.14em] uppercase mb-1 text-slate-500 leading-tight">
                      Als Dankeschön für Ihre Entscheidung
                    </div>

                    <h4 class="text-[15px] font-black uppercase mb-0.5" style="color:${theme.primary}">
                      Kombi-Bonus
                    </h4>

                    <div class="text-[8px] font-bold text-slate-600 uppercase mb-0.5">
                      Im Wert von
                    </div>

                    <div class="text-[24px] leading-none font-black mb-1" style="color:${theme.primary}">
                      ${finance.totalDiscount > 0 ? finance.totalDiscount.toLocaleString('de-DE') : '0'} €
                    </div>

                    <div class="text-[7px] font-bold text-slate-500 uppercase px-1 leading-relaxed">
                      Bei gleichzeitiger Beauftragung von<br>
                      Wärmepumpe • Photovoltaik • Wallbox
                    </div>
                  </div>

                  <div class="text-right relative z-10 pl-4 border-l border-slate-200 shrink-0 w-[150px] flex flex-col items-end justify-center">
                    <span class="w-7 h-7 inline-block mb-1.5" style="color:${theme.primary}">
                      ${Icons.award()}
                    </span>

                    <div class="text-[9px] font-black text-slate-700 leading-tight uppercase tracking-[0.12em] mb-2">
                      Ihr starker Partner<br>
                      <span style="color:${theme.primary}">${theme.name}</span>
                    </div>

                    <div class="text-[7px] text-slate-500 space-y-0.5 leading-relaxed">
                      <p class="font-bold text-slate-700">Ansprechpartner: <span class="font-medium text-slate-600">Projektleitung</span></p>
                      <p>Tel.: +49 (0) 123 456 789</p>
                      <p>E-Mail: info@${theme.name.toLowerCase().replace(/\s+/g, '')}.de</p>
                      <p>Web: www.${theme.name.toLowerCase().replace(/\s+/g, '')}.de</p>
                      <p class="pt-1 mt-1 border-t border-slate-200/70">
                        Musterstraße 1<br>12345 Musterstadt
                      </p>
                    </div>
                  </div>
                </div>

                <div class="mt-auto text-[8px] text-slate-500 leading-relaxed text-justify border-t border-slate-200 pt-2">
                  <strong>KOMBI-BONUS:</strong>
                  ${finance.totalDiscount > 0 ? finance.totalDiscount.toLocaleString('de-DE') : '0'} € brutto Preisvorteil bei gleichzeitiger
                  Beauftragung von Photovoltaik, Wärmepumpe und Ladestation im Rahmen eines Auftrags. Der Kombi-Bonus wird als Nachlass
                  auf der Schlussrechnung verrechnet. Keine Barauszahlung, keine Teilbeträge, kein Umtausch. Einmal pro Objekt möglich.
                  Nicht mit anderen Aktionen oder Nachlässen kombinierbar. Gültig bei Auftragserteilung innerhalb der Angebotsfrist;
                  vorbehaltlich technischer Umsetzbarkeit, Freigaben und Verfügbarkeit. Änderungen und Irrtümer vorbehalten.
                  <br><br>
                  Die beigefügte Wirtschaftlichkeitsberechnung ist eine unverbindliche Modellrechnung zur Orientierung. Sie berücksichtigt
                  je nach Angebotsumfang Photovoltaik, Stromspeicher, Wärmepumpe und Ladestation sowie den Vergleich zu einer Versorgung
                  mit Öl bzw. Gas. Grundlage sind technische Daten der geplanten Anlage, standortbezogene Strahlungsdaten, Ihr erwartetes
                  Verbrauchs- und Nutzungsverhalten sowie die zum Zeitpunkt der Angebotserstellung angesetzten Energiepreise.
                  Die tatsächlichen Werte können insbesondere durch Wetter, Anlagenbetrieb und künftige Energiepreisänderungen abweichen.
                  <br><br>
                  <strong>Urheberrechtlicher Hinweis:</strong>
                  Dieses Konzept ist geistiges Eigentum von ${theme.name}. Eine Weitergabe, Vervielfältigung oder Nutzung durch Dritte
                  ist ohne ausdrückliche schriftliche Zustimmung nicht gestattet.
                </div>
              </div>

              ${ReportFooter()}
            </div>

          </div>
        </div>
      `;
    }
    // =========================================================
    // CHARTS
    // =========================================================
    function makeDoughnutChart(id, values, colors, cutout = '68%', showSliceLabels = false) {
        destroyChart(id);
        const el = document.getElementById(id);
        if (!el) return;

        const sliceLabelPlugin = {
          id: `sliceLabelPlugin_${id}`,
          afterDatasetsDraw(chart) {
            if (!showSliceLabels) return;

            const { ctx } = chart;
            const dataset = chart.data.datasets[0];
            const meta = chart.getDatasetMeta(0);
            const total = dataset.data.reduce((sum, val) => sum + Number(val || 0), 0);

            if (!total) return;

            ctx.save();
            ctx.font = 'bold 10px Inter';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            meta.data.forEach((arc, index) => {
              const value = Number(dataset.data[index] || 0);
              if (value <= 0) return;

              const percent = Math.round((value / total) * 100);

              // Kleine Segmente nicht beschriften
              if (percent < 7) return;

              const angle = (arc.startAngle + arc.endAngle) / 2;
              const radius = (arc.innerRadius + arc.outerRadius) / 2;
              const x = arc.x + Math.cos(angle) * radius;
              const y = arc.y + Math.sin(angle) * radius;

              ctx.fillStyle = '#ffffff';
              ctx.fillText(`${percent}%`, x, y);
            });

            ctx.restore();
          }
        };

        charts[id] = new Chart(el, {
          type: 'doughnut',
          plugins: [sliceLabelPlugin],
          data: {
            labels: values.map((_, i) => `item-${i}`),
            datasets: [{
              data: values,
              backgroundColor: colors,
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            cutout,
            plugins: {
              legend: { display: false },
              tooltip: {
                enabled: true,
                callbacks: {
                  label: function(ctx) {
                    const total = ctx.dataset.data.reduce((sum, val) => sum + Number(val || 0), 0);
                    const value = Number(ctx.raw || 0);
                    const percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                    return `${formatDE(value)} kWh (${percent}%)`;
                  }
                }
              }
            }
          }
        });
      }

     function makeBarChart(id, labels, data1, data2) {
    destroyChart(id);
    const el = document.getElementById(id);
    if (!el) return;

    const theme = getActiveTheme();

    charts[id] = new Chart(el, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'PV-Produktion (kWh)',
            data: data1,
            backgroundColor: theme.primary,
            borderColor: theme.primary,
            borderWidth: 0,
            borderRadius: 2
          },
          {
            label: 'Gesamtbedarf (kWh)',
            data: data2,
            backgroundColor: theme.secondary,
            borderColor: theme.secondary,
            borderWidth: 0,
            borderRadius: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: '#94a3b8',
              font: { size: 10 }
            },
            border: { display: false }
          },
          y: {
            grid: { color: '#f1f5f9' },
            ticks: {
              color: '#94a3b8',
              font: { size: 10 }
            },
            border: { display: false }
          }
        },
        plugins: {
          legend: {
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              boxWidth: 8,
              font: { size: 10 }
            }
          },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                return `${ctx.dataset.label}: ${formatDE(ctx.raw)} kWh`;
              }
            }
          }
        }
      }
    });
  }

    function makeLineChart(id, labels, oldData, newData, amortisationYear) {
      destroyChart(id);
      const el = document.getElementById(id);
      if (!el) return;
      const theme = getActiveTheme();

      const annotationPlugin = {
        id: 'breakEvenLine',
        afterDatasetsDraw(chart) {
          if (!amortisationYear) return;
          const xScale = chart.scales.x;
          const yScale = chart.scales.y;
          const idx = labels.indexOf(String(amortisationYear));
          if (idx === -1) return;

          const x = xScale.getPixelForValue(idx);
          const ctx = chart.ctx;
          ctx.save();
          ctx.strokeStyle = theme.primary;
          ctx.lineWidth = 2;
          ctx.setLineDash([4, 4]);
          ctx.beginPath();
          ctx.moveTo(x, yScale.top);
          ctx.lineTo(x, yScale.bottom);
          ctx.stroke();
          ctx.setLineDash([]);
          ctx.fillStyle = theme.primary;
          ctx.font = 'bold 11px Inter';
          ctx.fillText(`Break-Even (Jahr ${amortisationYear})`, x + 6, yScale.top + 14);
          ctx.restore();
        }
      };

      charts[id] = new Chart(el, {
        type: 'line',
        plugins: [annotationPlugin],
        data: {
          labels,
          datasets: [
            {
              label: 'Ohne Systemwechsel (Laufende Kosten)',
              data: oldData,
              borderColor: '#94a3b8',
              backgroundColor: 'transparent',
              borderWidth: 3,
              tension: 0.35,
              pointRadius: 0
            },
            {
              label: 'Mit Systemwechsel (Investition + Restkosten)',
              data: newData,
              borderColor: theme.primary,
              backgroundColor: 'transparent',
              borderWidth: 3,
              tension: 0.35,
              pointRadius: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: false,
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: '#64748b', font: { size: 9 } },
              border: { display: false }
            },
            y: {
              grid: { color: '#e2e8f0' },
              ticks: {
                color: '#64748b',
                font: { size: 9 },
                callback: function(val) { return `${formatDE(val)} €`; }
              },
              border: { display: false }
            }
          },
          plugins: {
            legend: {
              labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                boxWidth: 8,
                font: { size: 10 }
              }
            },
            tooltip: {
              callbacks: {
                label: function(ctx) {
                  return `${ctx.dataset.label}: ${formatDE(ctx.raw)} €`;
                }
              }
            }
          }
        }
      });
    }

    function initDashboardCharts() {
      const computed = getComputed();
      const { kpis, bedarfsMix, seasonalData, chartData, finance } = computed;
      const theme = getActiveTheme();

      makeDoughnutChart('donutAutarkie', [kpis.totalDirekt, kpis.totalBatterie, kpis.totalNetzbezug], [theme.primary, theme.secondary, theme.inactive]);
      makeDoughnutChart('donutEigenverbrauch', [kpis.totalDirekt, kpis.totalBatterie, kpis.totalNetzeinspeisung], [theme.primary, theme.secondary, theme.inactive]);
      makeDoughnutChart('donutFinanz', [finance.finUnabhProzent, Math.max(0, 100 - finance.finUnabhProzent)], [theme.primary, theme.inactive]);

     makeDoughnutChart(
      'bedarfsmixChart',
      bedarfsMix.map(i => i.value),
      bedarfsMix.map(i => i.fill),
      '62%',
      false
    );

      seasonalData.forEach((season, i) => {
        const pieData = [
          season.DirektDeckung,
          season.BatterieDeckung,
          season.Netzbezug
        ].filter(v => v > 0);

        const pieColors = [
          theme.primary,
          theme.secondary,
          theme.inactive
        ].slice(0, pieData.length);

        makeDoughnutChart(`seasonChart${i}`, pieData, pieColors);
      });

      makeBarChart(
        'monthlyCompareChart',
        chartData.map(i => i.name),
        chartData.map(i => i.Solarertrag),
        chartData.map(i => i.Gesamtbedarf)
      );

      makeLineChart(
        'financeLineChart',
        finance.cashflow.map(i => i.year),
        finance.cashflow.map(i => i.kostenOhne),
        finance.cashflow.map(i => i.kostenMit),
        finance.amortisationYear
      );
    }

    function openCustomerProfile() {
      const customerId = backendMeta.customerId || backendCustomer?.customer_id || null;

      if (!customerId) {
        alert('Keine Kunden-ID gefunden.');
        return;
      }

      window.location.href = `/new_lead_profile/${customerId}`;
    }
    // =========================================================
    // ROOT RENDER OVERRIDE
    // =========================================================
    function renderApp() {
      updateThemeCSS();
      const app = document.getElementById('app');
      if (!app) return;

      app.innerHTML = state.view === 'wizard'
        ? renderWizard()
        : renderDashboard();

      if (state.view === 'dashboard') {
        requestAnimationFrame(() => {
          initDashboardCharts();
        });
      }
    }

    renderApp();
  </script>
</body>
</html>