<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WERK STUDIO Energiekonzept</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Chart.js for vanilla charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    body { font-family: 'Inter', sans-serif; }

    :root{
      --color-brown:#8f8675;
      --color-beige:#c1b7a6;
      --color-light-gray:#e2e8f0;
      --donut-fill:#8f8675;
      --donut-battery:#c1b7a6;
      --donut-empty:#e2e8f0;
      --einspeisung:#c1b7a6;
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
        padding: 15mm 20mm !important;
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
        padding: 20mm;
        background: white;
        box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        position: relative;
        overflow: hidden;
      }
    }

    .focus-ring:focus {
      outline: none;
      box-shadow: 0 0 0 2px var(--color-brown);
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
<body class="bg-slate-50 text-slate-800">

  <div id="app"></div>

  <script>
    // =========================================================
    // ICONS (Lucide-like inline SVG helpers)
    // =========================================================
    const Icons = {
      sun: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <circle cx="12" cy="12" r="4"></circle>
          <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
        </svg>
      `,
      thermoSnow: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M14 14.76V3a2 2 0 0 0-4 0v11.76a4 4 0 1 0 4 0Z"></path>
          <path d="M9 17h6"></path>
          <path d="M17 8l1 1 1-1"></path>
          <path d="M17 14l1 1 1-1"></path>
        </svg>
      `,
      zap: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"></path>
        </svg>
      `,
      mapPin: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M12 22s7-4.35 7-12a7 7 0 1 0-14 0c0 7.65 7 12 7 12z"></path>
          <circle cx="12" cy="10" r="2.5"></circle>
        </svg>
      `,
      info: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M12 16v-4"></path>
          <path d="M12 8h.01"></path>
        </svg>
      `,
      home: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M3 10.5 12 3l9 7.5"></path>
          <path d="M5 9.5V21h14V9.5"></path>
        </svg>
      `,
      users: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
          <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
      `,
      euro: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M4 10h10"></path>
          <path d="M4 14h10"></path>
          <path d="M14.5 6.5a5.5 5.5 0 1 0 0 11"></path>
        </svg>
      `,
      checkSquare: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <rect x="3" y="3" width="18" height="18" rx="2"></rect>
          <path d="M9 12l2 2 4-4"></path>
        </svg>
      `,
      checkCircle2: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M9 12l2 2 4-4"></path>
        </svg>
      `,
      shieldCheck: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          <path d="M9 12l2 2 4-4"></path>
        </svg>
      `,
      printer: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M6 9V2h12v7"></path>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v5a2 2 0 0 1-2 2h-2"></path>
          <path d="M6 14h12v8H6z"></path>
        </svg>
      `,
      arrowLeft: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M19 12H5"></path>
          <path d="M12 19l-7-7 7-7"></path>
        </svg>
      `,
      arrowRight: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M5 12h14"></path>
          <path d="M12 5l7 7-7 7"></path>
        </svg>
      `,
      activity: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M22 12h-4l-3 9-6-18-3 9H2"></path>
        </svg>
      `,
      leaf: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M11 20A7 7 0 0 1 4 13C4 7 9 4 20 4c0 11-3 16-9 16z"></path>
          <path d="M11 20c1.5-5 4.5-8 9-11"></path>
        </svg>
      `,
      trendingUp: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M22 7 13.5 15.5l-5-5L2 17"></path>
          <path d="M16 7h6v6"></path>
        </svg>
      `,
      alertTriangle: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94A2 2 0 0 0 22.18 18L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
          <path d="M12 9v4"></path>
          <path d="M12 17h.01"></path>
        </svg>
      `,
      x: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M18 6 6 18"></path>
          <path d="m6 6 12 12"></path>
        </svg>
      `,
      sliders: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M4 21v-7"></path>
          <path d="M4 10V3"></path>
          <path d="M12 21v-9"></path>
          <path d="M12 8V3"></path>
          <path d="M20 21v-5"></path>
          <path d="M20 12V3"></path>
          <path d="M2 14h4"></path>
          <path d="M10 8h4"></path>
          <path d="M18 16h4"></path>
        </svg>
      `,
      save: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
          <path d="M17 21v-8H7v8"></path>
          <path d="M7 3v5h8"></path>
        </svg>
      `,
      chevronDown: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="m6 9 6 6 6-6"></path>
        </svg>
      `,
      lightbulb: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M9 18h6"></path>
          <path d="M10 22h4"></path>
          <path d="M12 2a7 7 0 0 0-4 12c.7.6 1 1.4 1 2h6c0-.6.3-1.4 1-2A7 7 0 0 0 12 2z"></path>
        </svg>
      `,
      award: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <circle cx="12" cy="8" r="7"></circle>
          <path d="M8.21 13.89 7 22l5-3 5 3-1.21-8.11"></path>
        </svg>
      `,
      wrench: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-3-3 3-3z"></path>
        </svg>
      `,
      star: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"></path>
        </svg>
      `,
      battery: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <rect x="2" y="7" width="18" height="10" rx="2"></rect>
          <path d="M22 11v2"></path>
        </svg>
      `,
      car: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M14 16H9m10 0h1a1 1 0 0 0 1-1v-3l-2-5a2 2 0 0 0-2-1H7a2 2 0 0 0-2 1l-2 5v3a1 1 0 0 0 1 1h1"></path>
          <circle cx="6.5" cy="16.5" r="2.5"></circle>
          <circle cx="17.5" cy="16.5" r="2.5"></circle>
        </svg>
      `,
      network: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <rect x="3" y="3" width="7" height="7"></rect>
          <rect x="14" y="3" width="7" height="7"></rect>
          <rect x="14" y="14" width="7" height="7"></rect>
          <path d="M10 6.5h4"></path>
          <path d="M17.5 10v4"></path>
          <path d="M7 10v8h7"></path>
        </svg>
      `,
      checkCircle: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M9 12l2 2 4-4"></path>
        </svg>
      `,
      tag: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M20 10 10 20 2 12V2h10l8 8z"></path>
          <circle cx="7" cy="7" r="1"></circle>
        </svg>
      `,
      piggyBank: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M19 5c1.5 0 2 1 2 2 0 1.5-1 2-2 2"></path>
          <path d="M3 11a7 7 0 0 1 7-7h5a5 5 0 0 1 5 5v2a6 6 0 0 1-6 6H8l-2 3H4l1-3a6 6 0 0 1-2-4v-2z"></path>
          <path d="M12 7v3"></path>
        </svg>
      `,
      cpu: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <rect x="7" y="7" width="10" height="10" rx="2"></rect>
          <path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"></path>
        </svg>
      `,
      infinity: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M18.18 8c-2.43 0-4.18 4-6.18 4s-3.75-4-6.18-4A3.82 3.82 0 0 0 2 11.82 3.82 3.82 0 0 0 5.82 15c2.43 0 4.18-4 6.18-4s3.75 4 6.18 4A3.82 3.82 0 0 0 22 11.18 3.82 3.82 0 0 0 18.18 8z"></path>
        </svg>
      `,
      calculator: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <rect x="4" y="2" width="16" height="20" rx="2"></rect>
          <path d="M8 6h8"></path>
          <path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path>
        </svg>
      `,
      thermometer: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M14 14.76V3a2 2 0 0 0-4 0v11.76a4 4 0 1 0 4 0Z"></path>
        </svg>
      `,
      maximize: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M8 3H5a2 2 0 0 0-2 2v3"></path>
          <path d="M16 3h3a2 2 0 0 1 2 2v3"></path>
          <path d="M8 21H5a2 2 0 0 1-2-2v-3"></path>
          <path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>
        </svg>
      `,
      droplet: () => `
         <svg viewBox="0 0 24 24" class="icon-stroke">
          <path d="M12 2s7 7 7 12a7 7 0 0 1-14 0c0-5 7-12 7-12z"></path>
        </svg>
      `
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

    const colorBrown = '#8f8675';
    const colorBeige = '#c1b7a6';
    const colorLightGray = '#e2e8f0';

    const donutFillColor = colorBrown;
    const donutBatteryColor = colorBeige;
    const donutEmptyColor = colorLightGray;
    const einspeisungColor = colorBeige;

    // =========================================================
    // STATE
    // =========================================================
    const state = {
      view: 'wizard',
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
      config: {
        name: 'Mustermann',
        gebaeudeArt: 'Einfamilienhaus',
        wohneinheiten: 1,
        selbstbewohnteWE: 1,
        weUnter40k: 0,
        plz: '80331',

        dachseiten: [
          { id: 1, ausrichtung: 'Süd', neigung: 35, eindeckung: 'Ziegel', eindeckungTyp: 'Beton (z.B. Frankfurter Pfanne)', customKwp: '' }
        ],

        heizungArt: 'Gas',
        heizungAlter: 25,
        heizVerbrauch: 20000,
        heizSystem: 'Heizkörper',
        warmwasserArt: 'Zentral',
        personen: 4,
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

        hhStrom: 4000,
        autoArt: 'Verbrenner',
        fahrleistung: 15000,
        verbrennerVerbrauch: 7,
        preisSprit: 1.65,

        preisStrom: 0.35,
        preisEinspeisung: 0.08,
        preisHeizMedium: 0.11,
        inflationRate: 3.0,
        wartungOld: 300,
        netzentgelt: 0.10,

        costWP: 30000,
        costPV: 16000,
        costBattery: 8000,
        costWallbox: 1500,

        customWpKw: '',
        customPvKwp: '',
        customBatteryKwh: '',
        customJAZ: '',

        discountWP: 1000,
        discountPV: 750,
        discountBattery: 250,
        discountWallbox: 150,

        extraGrantWP: '',
        extraGrantPV: '',
        extraGrantBattery: '',
        extraGrantWallbox: '',

        extraGrantSourceWP: '',
        extraGrantSourcePV: '',
        extraGrantSourceBattery: '',
        extraGrantSourceWallbox: ''
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

      let systemVerlust = config.heizungAlter > 20 ? 0.20 : (config.heizungAlter > 10 ? 0.15 : 0.10);
      let thermischHauptsystem = getHeizMediumKwh(config.heizVerbrauch, config.heizungArt) * (1 - systemVerlust);

      let thermischKaminPotenziell = config.kaminVorhanden ? config.holzVerbrauch * 2100 * 0.75 : 0;
      let thermischSolarPotenziell = config.solarthermieVorhanden ? config.solarKollektoren * 2.5 * (config.solarthermieArt === 'Flachkollektor' ? 350 : 500) : 0;

      const gesamtWaermeBedarfHaus = thermischHauptsystem + thermischKaminPotenziell + thermischSolarPotenziell;
      const berechneteHeizlast = (gesamtWaermeBedarfHaus / klima.vbh).toFixed(1);
      const empfohleneWpKw = Math.ceil(gesamtWaermeBedarfHaus / klima.vbh);
      const wpLeistungKW = config.customWpKw !== '' ? Number(config.customWpKw) : empfohleneWpKw;

      const bivalenzpunkt = klima.nat >= -10 ? -5 : -7;

      let wwBedarfThermisch = config.warmwasserArt === 'Zentral' ? config.personen * 800 : 0;
      if (config.zirkulation && config.warmwasserArt === 'Zentral') wwBedarfThermisch += 600;

      let heizWärmeBedarf = Math.max(0, thermischHauptsystem - wwBedarfThermisch);

      let heizWärmeNachAbzug = heizWärmeBedarf;
      let wwBedarfNachAbzug = wwBedarfThermisch;

      if (config.kaminWeiterBetreiben) heizWärmeNachAbzug -= thermischKaminPotenziell;

      if (config.solarthermieWeiterBetreiben) {
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

      let wpStrombedarf = config.customJAZ !== '' ? Math.round(realeWpWaermeBedarf / cop) : berechneterWpStrombedarf;
      let umweltEnergie = realeWpWaermeBedarf - wpStrombedarf;

      const evStrombedarf = Math.round(config.fahrleistung * 0.2);
      const gesamtStrombedarf = config.hhStrom + wpStrombedarf + evStrombedarf;

      const { pvBaseFactor, wpFactor } = getRegionalFactors(config.plz);
      const avgYieldFactor = config.dachseiten.reduce((acc, curr) => acc + getOrientationFactor(curr.ausrichtung), 0) / config.dachseiten.length;
      const effectiveYieldPvKwp = pvBaseFactor * avgYieldFactor;

      const hasOst = config.dachseiten.some(d => d.ausrichtung.includes('Ost'));
      const hasWest = config.dachseiten.some(d => d.ausrichtung.includes('West'));
      const hasSued = config.dachseiten.some(d => d.ausrichtung === 'Süd');
      const isEastWestProfile = hasOst && hasWest;

      let baseBattery = gesamtStrombedarf / 1000;
      let batterySpreadFactor = isEastWestProfile ? 0.8 : (hasSued ? 1.2 : 1.0);
      const empfohleneBatterie = Math.max(5, Math.round(baseBattery * batterySpreadFactor));
      const batteryCapacity = config.customBatteryKwh !== '' ? Number(config.customBatteryKwh) : empfohleneBatterie;

      const pvDimensionierungsFaktor = 1.35;
      const empfohlenePv = Math.max(3, Math.round((gesamtStrombedarf * pvDimensionierungsFaktor) / effectiveYieldPvKwp * 10) / 10);

      let manualPvKwpSum = 0;
      config.dachseiten.forEach(d => {
        if (d.customKwp && d.customKwp !== '') manualPvKwpSum += Number(d.customKwp);
      });

      const pvKwp = manualPvKwpSum > 0 ? manualPvKwpSum : (config.customPvKwp !== '' ? Number(config.customPvKwp) : Math.ceil(empfohlenePv));

      const distributedDachseiten = config.dachseiten.map(d => ({
        ...d,
        calculatedKwp: (d.customKwp && d.customKwp !== '') ? Number(d.customKwp) : Number((empfohlenePv / config.dachseiten.length).toFixed(1))
      }));

      const verbrennerLiterKosten = config.autoArt === 'Verbrenner'
        ? (config.fahrleistung / 100) * config.verbrennerVerbrauch * config.preisSprit
        : 0;

      const verbrennerKwhEquivalent = config.autoArt === 'Verbrenner'
        ? (config.fahrleistung / 100) * config.verbrennerVerbrauch * 9
        : 0;

      return {
        klima, wpStrombedarf, cop, jaz, berechneteJaz, copSH, copWW, evStrombedarf, gesamtStrombedarf, realeWpWaermeBedarf, umweltEnergie,
        wpLeistungKW, pvKwp, batteryCapacity, empfohleneWpKw, empfohlenePv, empfohleneBatterie, berechneteHeizlast, gesamtWaermeBedarfHaus, bivalenzpunkt,
        verbrennerLiterKosten, verbrennerKwhEquivalent, wpFactor, effectiveYieldPvKwp, batterySpreadFactor,
        isEastWestProfile, hasSued, avgYieldFactor, distributedDachseiten, manualPvKwpSum,
        kaminKosten: config.kaminVorhanden ? config.holzVerbrauch * config.preisHolz : 0,
        thermischKaminPotenziell, thermischSolarPotenziell, thermischHauptsystem, heizVerbrauchKwh: getHeizMediumKwh(config.heizVerbrauch, config.heizungArt),
        systemVerlust, wwBedarfThermisch, heizWärmeBedarf, wwBedarfNachAbzug, heizWärmeNachAbzug
      };
    }

    function getSimulation(derivedParams) {
      const config = state.config;
      const kwp = derivedParams.pvKwp;
      const batteryCapacity = derivedParams.batteryCapacity;
      const wpJahresVerbrauch = derivedParams.wpStrombedarf;
      const wpFactor = derivedParams.wpFactor;

      const hhAutarkieFixed = 70;
      const wpAutarkieFixed = 55;
      const evAutarkieFixed = 50;

      const hhDeckung = config.hhStrom * (hhAutarkieFixed / 100);
      const wpDeckung = wpJahresVerbrauch * (wpAutarkieFixed / 100);
      const evDeckung = config.fahrleistung > 0 ? derivedParams.evStrombedarf * (evAutarkieFixed / 100) : 0;

      const fixedTotalDeckung = hhDeckung + wpDeckung + evDeckung;

      let totalPV = 0, totalBedarf = 0, totalDirekt = 0, totalBatterie = 0, totalNetzbezug = 0, totalNetzeinspeisung = 0;

      const seasonalAgg = {
        'Winter': { name: 'Winter', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
        'Frühling': { name: 'Frühling', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
        'Sommer': { name: 'Sommer', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 },
        'Herbst': { name: 'Herbst', Solarertrag: 0, Gesamtbedarf: 0, DirektDeckung: 0, BatterieDeckung: 0, Netzbezug: 0, NetzeinspeisungNeg: 0 }
      };

      const data = MONTHS.map((month, index) => {
        const days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][index];

        const pvErtragMo = kwp * derivedParams.effectiveYieldPvKwp * PV_DISTRIBUTION[index];
        const hhBedarfMo = config.hhStrom * HH_DISTRIBUTION[index];
        const wpBedarfMo = wpJahresVerbrauch * wpFactor * HGT_DISTRIBUTION[index];
        const evBedarfMo = derivedParams.evStrombedarf * EV_DISTRIBUTION[index];
        const gesamtBedarfMo = hhBedarfMo + wpBedarfMo + evBedarfMo;

        const bedarfTagDaily = ((hhBedarfMo / days) * DAYLIGHT_RATIO[index]) +
          ((wpBedarfMo / days) * DAYLIGHT_RATIO[index]) +
          ((evBedarfMo / days) * 0.2);

        const bedarfNachtDaily = (gesamtBedarfMo / days) - bedarfTagDaily;

        const direktDaily = Math.min(pvErtragMo / days, bedarfTagDaily);
        const chargeDaily = Math.min((pvErtragMo / days) - direktDaily, batteryCapacity);
        const dischargeDaily = Math.min(chargeDaily * 0.9, bedarfNachtDaily);

        const direktDeckung = Math.round(direktDaily * days);
        const batterieLadung = Math.round(chargeDaily * days);
        const batterieDeckung = Math.round(dischargeDaily * days);

        const netzeinspeisung = Math.round(Math.max(0, pvErtragMo - direktDeckung - batterieLadung));
        const gesamtDeckungMo = direktDeckung + batterieDeckung;
        const netzbezug = Math.round(gesamtBedarfMo - gesamtDeckungMo);

        let season = 'Herbst';
        if (index === 11 || index <= 1) season = 'Winter';
        else if (index >= 2 && index <= 4) season = 'Frühling';
        else if (index >= 5 && index <= 7) season = 'Sommer';

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

      const seasonalArray = ['Winter', 'Frühling', 'Sommer', 'Herbst'].map(s => {
        const item = seasonalAgg[s];
        const calcSeasonAutarkie = item.Gesamtbedarf > 0 ? Math.round(((item.DirektDeckung + item.BatterieDeckung) / item.Gesamtbedarf) * 100) : 100;
        item.autarkie = Math.min(calcSeasonAutarkie, 98);
        return item;
      });

      let fossilFactor = 0.202;
      if (config.heizungArt === 'Öl') fossilFactor = 0.266;
      if (config.heizungArt === 'Holz / Pellets') fossilFactor = 0.02;
      if (config.heizungArt === 'Nachtspeicher' || config.heizungArt === 'Stromdirektheizung') fossilFactor = 0.4;

      const evFossilCo2 = config.autoArt === 'Verbrenner'
        ? (config.fahrleistung / 100) * config.verbrennerVerbrauch * 2.37
        : 0;

      const kaminCo2 = config.kaminVorhanden ? config.holzVerbrauch * 2100 * 0.02 : 0;

      const oldCo2 = (derivedParams.heizVerbrauchKwh * fossilFactor) + (config.hhStrom * 0.4) + evFossilCo2 + kaminCo2;
      const finalNetzbezug = Math.max(0, totalBedarf - fixedTotalDeckung);
      const newCo2 = (finalNetzbezug * 0.4);
      const co2SavingsYear = (oldCo2 - newCo2) / 1000;

      const oldEnergyKwh = derivedParams.heizVerbrauchKwh + config.hhStrom + derivedParams.verbrennerKwhEquivalent + derivedParams.thermischKaminPotenziell;
      const energeticSavingsKwh = Math.round(oldEnergyKwh - finalNetzbezug);

      const calculatedBedarfsMix = [
        { name: 'Haushalt', value: config.hhStrom, fill: colorLightGray },
        { name: 'Wärmepumpe', value: derivedParams.wpStrombedarf, fill: colorBeige }
      ];

      if (config.fahrleistung > 0) {
        calculatedBedarfsMix.push({ name: 'E-Auto', value: derivedParams.evStrombedarf, fill: colorBrown });
      }

      const simTotalDeckung = totalDirekt + totalBatterie || 1;
      const scale = fixedTotalDeckung / simTotalDeckung;
      const fixedTotalDirekt = totalDirekt * scale;
      const fixedTotalBatterie = totalBatterie * scale;
      const newTotalNetzeinspeisung = Math.max(0, totalPV - fixedTotalDeckung);

      return {
        chartData: data,
        seasonalData: seasonalArray,
        bedarfsMix: calculatedBedarfsMix,
        kpis: {
          totalPV: Math.round(totalPV),
          totalBedarf: Math.round(totalBedarf),
          totalNetzbezug: Math.round(finalNetzbezug),
          totalNetzeinspeisung: Math.round(newTotalNetzeinspeisung),
          totalDirekt: Math.round(fixedTotalDirekt),
          totalBatterie: Math.round(fixedTotalBatterie),
          autarkie: totalBedarf > 0 ? Math.min(Math.round((fixedTotalDeckung / totalBedarf) * 100), 99) : 0,
          eigenverbrauchQuote: totalPV > 0 ? Math.min(Math.round((fixedTotalDeckung / totalPV) * 100), 99) : 0,
          hhDeckung: Math.round(hhDeckung),
          wpDeckung: Math.round(wpDeckung),
          evDeckung: Math.round(evDeckung),
          hhAutarkie: hhAutarkieFixed,
          wpAutarkie: wpAutarkieFixed,
          evAutarkie: config.fahrleistung > 0 ? evAutarkieFixed : 0,
          spezErtrag: derivedParams.effectiveYieldPvKwp,
          oldEnergyKwh,
          energeticSavingsKwh
        },
        co2: {
          year: co2SavingsYear.toFixed(1),
          tenYears: (co2SavingsYear * 10).toFixed(1),
          twentyYears: (co2SavingsYear * 20).toFixed(1),
          thirtyYears: (co2SavingsYear * 30).toFixed(1),
          trees: Math.round(co2SavingsYear * 80),
          forestArea: Math.round(co2SavingsYear * 1250)
        }
      };
    }

    function getFinance(derivedParams, kpis) {
      const config = state.config;

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

      const effectiveWPCost = Math.max(0, config.costWP - (Number(config.discountWP) || 0));
      const foerderfaehigeKostenWP = Math.min(effectiveWPCost, weDeckelung);
      const costPerWE = foerderfaehigeKostenWP / config.wohneinheiten;

      const rentedWE = config.wohneinheiten - config.selbstbewohnteWE;
      const ownerNoLowIncWE = config.selbstbewohnteWE - config.weUnter40k;
      const ownerLowIncWE = config.weUnter40k;

      const baseProzent = grundFoerderung + effizienzBonus;
      const ownerNoLowIncProzent = Math.min(70, baseProzent + klimaBonus);
      const ownerLowIncProzent = Math.min(70, baseProzent + klimaBonus + einkommenBonus);

      const kfwZuschuss = Math.round(
        costPerWE * ((rentedWE * (baseProzent / 100)) + (ownerNoLowIncWE * (ownerNoLowIncProzent / 100)) + (ownerLowIncWE * (ownerLowIncProzent / 100)))
      );

      const maxZuschussProzent = foerderfaehigeKostenWP > 0
        ? Math.round((kfwZuschuss / foerderfaehigeKostenWP) * 100)
        : 0;

      const isKombiBonusActive = config.costWP > 0 && config.costPV > 0 && config.costBattery > 0 && config.costWallbox > 0;

      const discountWPNum = isKombiBonusActive ? (Number(config.discountWP) || 0) : 0;
      const discountPVNum = isKombiBonusActive ? (Number(config.discountPV) || 0) : 0;
      const discountBatteryNum = isKombiBonusActive ? (Number(config.discountBattery) || 0) : 0;
      const discountWallboxNum = isKombiBonusActive ? (Number(config.discountWallbox) || 0) : 0;

      const extraGrantWPNum = Number(config.extraGrantWP) || 0;
      const extraGrantPVNum = Number(config.extraGrantPV) || 0;
      const extraGrantBatteryNum = Number(config.extraGrantBattery) || 0;
      const extraGrantWallboxNum = Number(config.extraGrantWallbox) || 0;

      const totalInvest = config.costWP + config.costPV + config.costBattery + config.costWallbox;
      const totalDiscount = discountWPNum + discountPVNum + discountBatteryNum + discountWallboxNum;
      const totalExtraGrant = extraGrantWPNum + extraGrantPVNum + extraGrantBatteryNum + extraGrantWallboxNum;
      const totalFoerderung = kfwZuschuss + totalExtraGrant;

      const nettoWP = config.costWP - discountWPNum - extraGrantWPNum - kfwZuschuss;
      const nettoPV = config.costPV - discountPVNum - extraGrantPVNum;
      const nettoBattery = config.costBattery - discountBatteryNum - extraGrantBatteryNum;
      const nettoWallbox = config.costWallbox - discountWallboxNum - extraGrantWallboxNum;

      const nettoInvest = nettoWP + nettoPV + nettoBattery + nettoWallbox;

      const effPvCost = config.costPV - discountPVNum - extraGrantPVNum;
      const effBatCost = config.costBattery - discountBatteryNum - extraGrantBatteryNum;
      const lcoe = kpis.totalPV > 0 ? ((effPvCost + effBatCost) / (kpis.totalPV * 30)).toFixed(2) : '0.00';

      const hhKostenOhne = Math.round(config.hhStrom * config.preisStrom);
      const wpKostenOhne = Math.round(derivedParams.wpStrombedarf * config.preisStrom);
      const evKostenOhne = Math.round(derivedParams.evStrombedarf * config.preisStrom);

      const hhNetz = Math.max(0, config.hhStrom - kpis.hhDeckung);
      const wpNetz = Math.max(0, derivedParams.wpStrombedarf - kpis.wpDeckung);
      const evNetz = Math.max(0, derivedParams.evStrombedarf - kpis.evDeckung);

      const evOldCost = config.autoArt === 'Verbrenner'
        ? derivedParams.verbrennerLiterKosten
        : (config.fahrleistung > 0 ? (config.fahrleistung / 100) * 20 * config.preisStrom : 0);

      const heizkostenOld = config.heizVerbrauch * config.preisHeizMedium;
      const kaminCostOld = config.kaminVorhanden ? derivedParams.kaminKosten : 0;
      const stromCostOld = hhKostenOhne;

      const costOldBase = heizkostenOld + stromCostOld + config.wartungOld + evOldCost + kaminCostOld;

      const hasSteuVE = derivedParams.wpStrombedarf > 0 || config.costWallbox > 0;
      const steuVeBedarfAllElectric = derivedParams.wpStrombedarf + derivedParams.evStrombedarf;
      const ersparnis14aAllElectric = hasSteuVE
        ? Math.round(Math.max(160, steuVeBedarfAllElectric * config.netzentgelt * 0.6))
        : 0;

      const steuVeNetz = wpNetz + evNetz;
      const ersparnis14a = hasSteuVE
        ? Math.round(Math.max(160, steuVeNetz * config.netzentgelt * 0.6))
        : 0;

      const futureKaminCosts = (config.kaminVorhanden && config.kaminWeiterBetreiben) ? derivedParams.kaminKosten : 0;
      const costAllElectricBase = (derivedParams.gesamtStrombedarf * config.preisStrom) + config.wartungOld - ersparnis14aAllElectric + futureKaminCosts;
      const costNewBase = (kpis.totalNetzbezug * config.preisStrom) - (kpis.totalNetzeinspeisung * config.preisEinspeisung) - ersparnis14a + futureKaminCosts;

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
        const electricCostYear = (derivedParams.gesamtStrombedarf * config.preisStrom + futureKaminCosts) * inflationFactor + config.wartungOld - ersparnis14aAllElectric;
        const newCostYear = (kpis.totalNetzbezug * config.preisStrom - kpis.totalNetzeinspeisung * config.preisEinspeisung + futureKaminCosts) * inflationFactor - ersparnis14a;

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

    // =========================================================
    // STATE MUTATIONS
    // =========================================================
    function handleConfigChange(key, value) {
      const prev = structuredClone(state.config);
      const newState = { ...state.config, [key]: value };

      if (key === 'gebaeudeArt' && value === 'Einfamilienhaus') {
        newState.wohneinheiten = 1;
        newState.selbstbewohnteWE = 1;
        newState.weUnter40k = 0;
      }

      if (key === 'selbstbewohnteWE' && newState.weUnter40k > value) {
        newState.weUnter40k = value;
      }

      if (key === 'heizungArt') {
        if (value === 'Gas') newState.preisHeizMedium = 0.11;
        if (value === 'Öl') newState.preisHeizMedium = 1.05;

        if (value === 'Holz / Pellets') {
          newState.preisHeizMedium = 280;
          newState.heizVerbrauch = 4;
        } else {
          if (prev.heizungArt === 'Holz / Pellets') newState.heizVerbrauch = 20000;
        }

        if (value === 'Stromdirektheizung' || value === 'Nachtspeicher') {
          newState.preisHeizMedium = newState.preisStrom;
        }
      }

      if (key === 'kaminVorhanden' && !value) newState.kaminWeiterBetreiben = false;
      if (key === 'solarthermieVorhanden' && !value) newState.solarthermieWeiterBetreiben = false;

      state.config = newState;
      renderApp();
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
      return `
        <div class="text-[10px] font-bold text-slate-400 tracking-widest mb-6 border-b border-slate-200 pb-2 flex justify-between items-center">
          <span>WERK STUDIO BAUKONZEPT</span>
          <span class="text-slate-500 uppercase">${text}</span>
        </div>
      `;
    }

    function sidebarInput({ label, value, type = "text", step = "", rightLabel = "", placeholder = "", disabled = false, oninput = "" }) {
      return `
        <div class="flex flex-col gap-1.5 mb-3 ${disabled ? 'opacity-50 pointer-events-none' : ''}">
          <div class="flex justify-between items-end">
            <label class="text-xs font-bold text-slate-700">${label}</label>
            ${rightLabel ? `<span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="color:${colorBrown};background:${colorBeige}30">${rightLabel}</span>` : ''}
          </div>
          <input
            type="${type}"
            ${step !== "" ? `step="${step}"` : ""}
            value="${value ?? ''}"
            placeholder="${placeholder}"
            ${disabled ? 'disabled' : ''}
            oninput="${oninput}"
            class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 transition-shadow placeholder:text-slate-400 focus-ring disabled:bg-slate-100"
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

      return `
        <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4 font-sans text-slate-800">
          <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-4xl overflow-hidden">
            <div class="bg-slate-900 text-white p-8">
              <div class="font-bold text-sm tracking-widest mb-1" style="color:${colorBeige}">WERK STUDIO BAUKONZEPT</div>
              <h1 class="text-3xl font-bold mb-2">IHR WEG ZUR EIGENEN ENERGIEAUTARKIE</h1>
              <p class="text-slate-300 text-sm">Konfigurieren Sie Ihr System für den finalen Beratungsbericht.</p>

              <div class="flex items-center mt-8 gap-2">
                ${[1,2,3,4].map(step => `
                  <div class="flex-1 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm mb-2 transition-colors ${state.wizardStep >= step ? 'text-white' : 'bg-slate-800 text-slate-500'}"
                      style="${state.wizardStep >= step ? `background-color:${colorBrown}` : ''}">
                      ${state.wizardStep > step ? icon('checkCircle2', 'w-4 h-4') : step}
                    </div>
                    <div class="h-1 w-full rounded-full ${state.wizardStep >= step ? '' : 'bg-slate-800'}"
                      style="${state.wizardStep >= step ? `background-color:${colorBrown}` : ''}"></div>
                  </div>
                `).join('')}
              </div>
            </div>

            <div class="p-8">
              ${state.wizardStep === 1 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${colorBrown}">${Icons.home()}</span>
                    Gebäude, Heizung & Einkommen
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Name des Kunden</label>
                      <input type="text" value="${config.name}"
                        oninput="handleConfigChange('name', this.value)"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">PLZ (Standort)</label>
                      <input type="text" maxlength="5" value="${config.plz}"
                        oninput="handleConfigChange('plz', this.value.replace(/\\D/g, ''))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                  </div>

                  <div class="p-4 border rounded-xl bg-slate-50 border-slate-200 mt-4">
                    <div class="flex justify-between items-center mb-3">
                      <h4 class="text-sm font-bold flex items-center gap-2">
                        <span class="w-4 h-4" style="color:${colorBrown}">${Icons.sun()}</span>
                        Geplante Dachflächen
                      </h4>
                      <button onclick="addDachseite()" ${config.dachseiten.length >= 4 ? 'disabled' : ''}
                        class="text-xs font-bold px-3 py-1.5 rounded-lg text-white bg-slate-800 hover:bg-slate-700 disabled:opacity-50">
                        + Weitere Fläche
                      </button>
                    </div>

                    <div class="space-y-3">
                      ${config.dachseiten.map(dach => `
                        <div class="flex gap-3 items-end p-3 bg-white border border-slate-100 rounded-lg shadow-sm flex-wrap">
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

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 border border-slate-100 rounded-xl">
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
                      <select onchange="handleConfigChange('heizungArt', this.value)" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Gas','Öl','Holz / Pellets','Nachtspeicher'].map(opt => `
                          <option ${config.heizungArt === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Alter (Jahre)</label>
                      <input type="number" value="${config.heizungAlter}"
                        oninput="handleConfigChange('heizungAlter', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2 relative">
                      <label class="text-sm font-semibold flex justify-between">Verbrauch <span class="text-slate-400 font-normal">in ${getHeizEinheit(config.heizungArt)}</span></label>
                      <input type="number" value="${config.heizVerbrauch}"
                        oninput="handleConfigChange('heizVerbrauch', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Personen</label>
                      <input type="number" value="${config.personen}"
                        oninput="handleConfigChange('personen', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Heizsystem (Übergabe)</label>
                      <select onchange="handleConfigChange('heizSystem', this.value)" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Heizkörper','Fußbodenheizung','Beides'].map(opt => `
                          <option ${config.heizSystem === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Warmwasser</label>
                      <select onchange="handleConfigChange('warmwasserArt', this.value)" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring">
                        ${['Zentral','Dezentral'].map(opt => `
                          <option ${config.warmwasserArt === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                    </div>

                    <div class="space-y-2 flex flex-col justify-center mt-6">
                      <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" ${config.zirkulation ? 'checked' : ''}
                          onchange="handleConfigChange('zirkulation', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${colorBrown}" />
                        <span class="text-sm font-bold text-slate-800">Zirkulation vorhanden</span>
                      </label>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Rohrsystem Heizung</label>
                      <div class="flex gap-2">
                        <select onchange="handleConfigChange('rohrHeizungMaterial', this.value)" class="w-2/3 p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring">
                          ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                            <option ${config.rohrHeizungMaterial === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                        <div class="w-1/3 relative">
                          <input type="text" value="${config.rohrHeizungDN}" placeholder="DN"
                            oninput="handleConfigChange('rohrHeizungDN', this.value)"
                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                          <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                        </div>
                      </div>
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Rohrsystem Warmwasser</label>
                      <div class="flex gap-2">
                        <select onchange="handleConfigChange('rohrWWMaterial', this.value)" class="w-2/3 p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring">
                          ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                            <option ${config.rohrWWMaterial === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                        <div class="w-1/3 relative">
                          <input type="text" value="${config.rohrWWDN}" placeholder="DN"
                            oninput="handleConfigChange('rohrWWDN', this.value)"
                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
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
                          <select onchange="handleConfigChange('rohrZirkulationMaterial', this.value)" class="w-2/3 p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring">
                            ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                              <option ${config.rohrZirkulationMaterial === opt ? 'selected' : ''}>${opt}</option>
                            `).join('')}
                          </select>
                          <div class="w-1/3 relative">
                            <input type="text" value="${config.rohrZirkulationDN}" placeholder="DN"
                              oninput="handleConfigChange('rohrZirkulationDN', this.value)"
                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                            <span class="absolute right-3 top-3 text-xs text-slate-400">DN</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  ` : ''}

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div class="p-4 border rounded-xl bg-slate-50 border-slate-200">
                      <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" ${config.kaminVorhanden ? 'checked' : ''}
                          onchange="handleConfigChange('kaminVorhanden', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${colorBrown}" />
                        <span class="text-sm font-bold text-slate-800">Holz-Kamin vorhanden</span>
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
                              class="w-4 h-4 rounded" style="accent-color:${colorBrown}" />
                            <span class="text-xs font-semibold text-slate-700">Wird im Neusystem weiter befeuert</span>
                          </label>
                        </div>
                      ` : ''}
                    </div>

                    <div class="p-4 border rounded-xl bg-slate-50 border-slate-200">
                      <label class="flex items-center gap-3 cursor-pointer mb-3">
                        <input type="checkbox" ${config.solarthermieVorhanden ? 'checked' : ''}
                          onchange="handleConfigChange('solarthermieVorhanden', this.checked)"
                          class="w-5 h-5 rounded" style="accent-color:${colorBrown}" />
                        <span class="text-sm font-bold text-slate-800">Solarthermie vorhanden</span>
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
                              class="w-4 h-4 rounded" style="accent-color:${colorBrown}" />
                            <span class="text-xs font-semibold text-slate-700">Bleibt auf dem Dach / in Nutzung</span>
                          </label>
                        </div>
                      ` : ''}
                    </div>
                  </div>

                  ${config.selbstbewohnteWE > 0 ? `
                    <div class="pt-4 border-t border-slate-100">
                      ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                        <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-colors" style="border-color:${colorBeige}50;background:${colorBeige}10">
                          <input type="checkbox" ${config.weUnter40k === 1 ? 'checked' : ''}
                            onchange="handleConfigChange('weUnter40k', this.checked ? 1 : 0)"
                            class="w-5 h-5 rounded" style="accent-color:${colorBrown}" />
                          <span class="text-sm font-medium" style="color:${colorBrown}">
                            Haushalts-Einkommen liegt unter 40.000 € (Aktiviert 30% KfW-Bonus)
                          </span>
                        </label>
                      ` : `
                        <div class="space-y-2 p-4 border rounded-xl" style="border-color:${colorBeige}50;background:${colorBeige}10">
                          <label class="text-sm font-semibold" style="color:${colorBrown}">
                            Wie viele der ${config.selbstbewohnteWE} selbstbewohnten Einheiten haben ein Haushaltseinkommen &lt; 40.000 €?
                          </label>
                          <input type="number" min="0" max="${config.selbstbewohnteWE}" value="${config.weUnter40k}"
                            oninput="handleConfigChange('weUnter40k', Math.min(state.config.selbstbewohnteWE, Number(this.value)))"
                            class="w-full p-3 bg-white border border-slate-200 rounded-xl outline-none focus-ring" />
                          <p class="text-xs" style="color:${colorBrown}">Aktiviert den 30% Einkommensbonus anteilig.</p>
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
                </div>
              ` : ''}

              ${state.wizardStep === 2 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${colorBrown}">${Icons.users()}</span>
                    Haushalt & Elektromobilität
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Haushaltsstrom (kWh/a)</label>
                      <input type="number" step="100" value="${config.hhStrom}"
                        oninput="handleConfigChange('hhStrom', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Aktuelles Fahrzeug</label>
                      <select onchange="handleConfigChange('autoArt', this.value)" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring">
                        <option ${config.autoArt === 'Verbrenner' ? 'selected' : ''}>Verbrenner</option>
                        <option ${config.autoArt === 'E-Auto' ? 'selected' : ''}>E-Auto</option>
                      </select>
                    </div>
                  </div>

                  <div class="p-5 border rounded-xl bg-slate-50 border-slate-200">
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
                </div>
              ` : ''}

              ${state.wizardStep === 3 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${colorBrown}">${Icons.zap()}</span>
                    Energiepreise & Inflation
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Strompreis (€/kWh)</label>
                      <input type="number" step="0.01" value="${config.preisStrom}"
                        oninput="handleConfigChange('preisStrom', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold flex items-center gap-2">Netzentgelt (Arbeitspreis)</label>
                      <input type="number" step="0.01" value="${config.netzentgelt}"
                        oninput="handleConfigChange('netzentgelt', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Preis ${config.heizungArt} (€/${getHeizEinheit(config.heizungArt)})</label>
                      <input type="number" step="0.01" value="${config.preisHeizMedium}"
                        oninput="handleConfigChange('preisHeizMedium', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold flex items-center gap-2">Energiepreis-Inflation (%/Jahr)</label>
                      <input type="number" step="0.5" value="${config.inflationRate}"
                        oninput="handleConfigChange('inflationRate', Number(this.value))"
                        class="w-full p-3 border rounded-xl outline-none font-bold focus-ring"
                        style="background:${colorBeige}10;border-color:${colorBeige}50;color:${colorBrown}" />
                    </div>

                    <div class="space-y-2">
                      <label class="text-sm font-semibold">Aktuelle Nebenkosten (Wartung etc. €/a)</label>
                      <input type="number" step="10" value="${config.wartungOld}"
                        oninput="handleConfigChange('wartungOld', Number(this.value))"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus-ring" />
                    </div>
                  </div>
                </div>
              ` : ''}

              ${state.wizardStep === 4 ? `
                <div class="space-y-6 animate-fade-in">
                  <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                    <span class="w-5 h-5" style="color:${colorBrown}">${Icons.euro()}</span>
                    Geplante Investitionen (Brutto)
                  </h2>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>Wärmepumpe (€)</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${colorBrown};background:${colorBeige}10;border-color:${colorBeige}50">
                          Empfehlung: ${derivedParams.empfohleneWpKw} kW
                        </span>
                      </label>
                      <input type="number" step="1000" value="${config.costWP}"
                        oninput="handleConfigChange('costWP', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 shadow-sm outline-none focus-ring" />
                    </div>

                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>PV-Anlage (€)</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${colorBrown};background:${colorBeige}10;border-color:${colorBeige}50">
                          Empfehlung: ${derivedParams.empfohlenePv} kWp
                        </span>
                      </label>
                      <input type="number" step="1000" value="${config.costPV}"
                        oninput="handleConfigChange('costPV', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 shadow-sm outline-none focus-ring" />
                    </div>

                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>Batteriespeicher (€)</span>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border" style="color:${colorBrown};background:${colorBeige}10;border-color:${colorBeige}50">
                          Empfehlung: ${derivedParams.empfohleneBatterie} kWh
                        </span>
                      </label>
                      <input type="number" step="500" value="${config.costBattery}"
                        oninput="handleConfigChange('costBattery', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 shadow-sm outline-none focus-ring" />
                    </div>

                    <div class="space-y-2 relative">
                      <label class="flex justify-between items-end text-sm font-semibold">
                        <span>Wallbox (€)</span>
                        <span class="text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-md text-[11px] font-bold">Optional</span>
                      </label>
                      <input type="number" step="100" value="${config.costWallbox}"
                        oninput="handleConfigChange('costWallbox', Number(this.value))"
                        class="w-full p-4 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 shadow-sm outline-none focus-ring" />
                    </div>
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
                    style="background:${colorBrown}">
                    Weiter
                    <span class="w-4 h-4">${Icons.arrowRight()}</span>
                  </button>
                ` : `
                  <button onclick="setView('dashboard')"
                    class="flex items-center gap-2 px-8 py-3 text-white rounded-xl font-bold transition-all shadow-lg"
                    style="background:${colorBrown};box-shadow:0 10px 15px -3px ${colorBrown}40">
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
      return `
        <div class="min-h-screen bg-slate-200 text-slate-800 font-sans pb-20 pt-16 print:p-0 print:bg-white relative overflow-x-hidden">
          <div class="fixed top-0 left-0 w-full bg-slate-900 text-white p-4 z-[80] flex justify-between items-center shadow-lg no-print">
            <div class="font-bold flex items-center gap-3" style="color:${colorBeige}">
              <span class="w-5 h-5">${Icons.printer()}</span>
              Druckvorschau: WERK STUDIO Report
            </div>
            <div class="flex gap-3">
              <button onclick="setView('wizard')" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors">
                Zurück zum Wizard
              </button>
              <button onclick="setSidebarOpen(true)" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors shadow-lg" style="background:${colorBrown}">
                <span class="w-4 h-4">${Icons.sliders()}</span>
                Parameter anpassen
              </button>
              <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-slate-900 rounded-lg text-sm font-bold transition-colors ml-2" style="background:${colorBeige}">
                Als PDF speichern
              </button>
            </div>
          </div>

          <div class="max-w-5xl mx-auto p-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10 text-center">
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
      const app = document.getElementById('app');
      if (!app) return;

      app.innerHTML = state.view === 'wizard'
        ? renderWizard()
        : renderDashboardPlaceholder();
    }

    renderApp();
  </script>
    <script>
    // =========================================================
    // DASHBOARD / REPORT RENDER
    // =========================================================
    function renderSidebar(computed) {
      const config = state.config;
      const { derivedParams } = computed;

      return `
        ${state.isSidebarOpen ? `
          <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[90] no-print transition-opacity" onclick="setSidebarOpen(false)"></div>
        ` : ''}

        <div class="fixed top-0 right-0 h-full w-full max-w-sm bg-slate-50 shadow-2xl sidebar-transition z-[100] flex flex-col no-print ${state.isSidebarOpen ? 'translate-x-0' : 'translate-x-full'}">
          <div class="p-5 bg-slate-900 text-white flex justify-between items-center z-10 shadow-md">
            <h2 class="text-lg font-bold flex items-center gap-2" style="color:${colorBeige}">
              <span class="w-4 h-4">${Icons.sliders()}</span>
              Live-Editor
            </h2>
            <button onclick="setSidebarOpen(false)" class="text-slate-400 hover:text-white transition-colors bg-white/10 p-1.5 rounded-lg">
              <span class="w-5 h-5">${Icons.x()}</span>
            </button>
          </div>

          <div class="p-4 flex-1 space-y-4 overflow-y-auto custom-scroll">

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
              <button onclick="toggleSidebarSection('kunde')" class="w-full flex justify-between items-center p-3.5 bg-slate-50 hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${colorBrown}">${Icons.home()}</span>
                  Kunde & Gebäude
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.kunde ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.kunde ? `
                <div class="p-4 border-t border-slate-100 bg-white">
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

                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Gebäudeart</label>
                    <select onchange="handleConfigChange('gebaeudeArt', this.value)" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
                      <option ${config.gebaeudeArt === 'Einfamilienhaus' ? 'selected' : ''}>Einfamilienhaus</option>
                      <option ${config.gebaeudeArt === 'Mehrfamilienhaus' ? 'selected' : ''}>Mehrfamilienhaus</option>
                    </select>
                  </div>

                  ${config.gebaeudeArt === 'Einfamilienhaus' ? `
                    <div class="flex flex-col gap-1.5 mb-3">
                      <label class="text-xs font-bold text-slate-700">Nutzung</label>
                      <select onchange="handleConfigChange('selbstbewohnteWE', this.value === 'Selbstbewohnt' ? 1 : 0)" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
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
                </div>
              ` : ''}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
              <button onclick="toggleSidebarSection('dach')" class="w-full flex justify-between items-center p-3.5 bg-slate-50 hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${colorBrown}">${Icons.maximize()}</span>
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
                        <label class="text-[10px] font-bold text-slate-500 mb-1 block">Ausrichtung</label>
                        <select onchange="updateDachseite(${dach.id}, 'ausrichtung', this.value)" class="w-full p-2 border rounded-md text-xs outline-none">
                          ${['Süd','Süd-Ost','Süd-West','Ost','West','Nord-Ost','Nord-West','Nord'].map(opt => `
                            <option ${dach.ausrichtung === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                      </div>
                      <div class="w-[20%]">
                        <label class="text-[10px] font-bold text-slate-500 mb-1 block">Neigung</label>
                        <input type="number" value="${dach.neigung}" oninput="updateDachseite(${dach.id}, 'neigung', Number(this.value))" class="w-full p-2 border rounded-md text-xs outline-none" />
                      </div>
                      <div class="w-[25%]">
                        <label class="text-[10px] font-bold text-slate-500 mb-1 block">kWp</label>
                        <input type="number" step="0.1" value="${dach.customKwp || ''}" placeholder="Auto" oninput="updateDachseite(${dach.id}, 'customKwp', this.value)" class="w-full p-2 border rounded-md text-xs outline-none focus-ring" />
                      </div>
                      <div class="w-full flex gap-2 mt-1 items-end">
                        <div class="flex-1">
                          <label class="text-[10px] font-bold text-slate-500 mb-1 block">Eindeckung</label>
                          <select onchange="updateDachseite(${dach.id}, 'eindeckung', this.value)" class="w-full p-2 border rounded-md text-xs outline-none">
                            ${['Ziegel','Blech','Trapezblech','Flachdach/Folie','Schiefer'].map(opt => `
                              <option ${((dach.eindeckung || 'Ziegel') === opt) ? 'selected' : ''}>${opt}</option>
                            `).join('')}
                          </select>
                        </div>
                        <div class="flex-1">
                          <label class="text-[10px] font-bold text-slate-500 mb-1 block">Typ / Material</label>
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

                  <button onclick="addDachseite()" ${config.dachseiten.length >= 4 ? 'disabled' : ''} class="w-full mt-2 py-2 border border-dashed border-slate-300 rounded-lg text-xs font-bold text-slate-500 hover:bg-slate-50 disabled:opacity-50">
                    + Seite hinzufügen
                  </button>
                </div>
              ` : ''}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
              <button onclick="toggleSidebarSection('altsystem')" class="w-full flex justify-between items-center p-3.5 bg-slate-50 hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${colorBrown}">${Icons.users()}</span>
                  Altsystem & Bedarf
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.altsystem ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.altsystem ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Hauptheizung</label>
                    <select onchange="handleConfigChange('heizungArt', this.value)" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
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

                  <div class="flex gap-2">
                    <div class="w-1/2">
                      ${sidebarInput({
                        label: 'Personen',
                        type: 'number',
                        value: config.personen,
                        oninput: `handleConfigChange('personen', Number(this.value))`
                      })}
                    </div>
                    <div class="w-1/2">
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

                  <div class="flex flex-col gap-1.5 mb-3 pt-3 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-700">Heizsystem (Übergabe)</label>
                    <select onchange="handleConfigChange('heizSystem', this.value)" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
                      ${['Heizkörper','Fußbodenheizung','Beides'].map(opt => `
                        <option ${config.heizSystem === opt ? 'selected' : ''}>${opt}</option>
                      `).join('')}
                    </select>
                  </div>

                  <div class="flex gap-2 mb-3">
                    <div class="w-1/2">
                      <label class="text-xs font-bold text-slate-700 block mb-1.5">Warmwasser</label>
                      <select onchange="handleConfigChange('warmwasserArt', this.value)" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
                        <option ${config.warmwasserArt === 'Zentral' ? 'selected' : ''}>Zentral</option>
                        <option ${config.warmwasserArt === 'Dezentral' ? 'selected' : ''}>Dezentral</option>
                      </select>
                    </div>
                    <div class="w-1/2 flex items-center justify-center pt-5">
                      <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" ${config.zirkulation ? 'checked' : ''} onchange="handleConfigChange('zirkulation', this.checked)" class="w-4 h-4 rounded" style="accent-color:${colorBrown}" />
                        <span class="text-xs font-bold text-slate-700">Zirkulation</span>
                      </label>
                    </div>
                  </div>

                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Rohrsystem Heizung</label>
                    <div class="flex gap-2">
                      <select onchange="handleConfigChange('rohrHeizungMaterial', this.value)" class="w-2/3 p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
                        ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                          <option ${config.rohrHeizungMaterial === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                      <input type="text" value="${config.rohrHeizungDN}" oninput="handleConfigChange('rohrHeizungDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring" />
                    </div>
                  </div>

                  <div class="flex flex-col gap-1.5 mb-3">
                    <label class="text-xs font-bold text-slate-700">Rohrsystem WW</label>
                    <div class="flex gap-2">
                      <select onchange="handleConfigChange('rohrWWMaterial', this.value)" class="w-2/3 p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
                        ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                          <option ${config.rohrWWMaterial === opt ? 'selected' : ''}>${opt}</option>
                        `).join('')}
                      </select>
                      <input type="text" value="${config.rohrWWDN}" oninput="handleConfigChange('rohrWWDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring" />
                    </div>
                  </div>

                  ${config.zirkulation ? `
                    <div class="flex flex-col gap-1.5 mb-3">
                      <label class="text-xs font-bold text-slate-700">Rohrsystem Zirkulation</label>
                      <div class="flex gap-2">
                        <select onchange="handleConfigChange('rohrZirkulationMaterial', this.value)" class="w-2/3 p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
                          ${['Kupfer','Eisenrohr','Kunststoff','Verbundrohr','Edelstahl'].map(opt => `
                            <option ${config.rohrZirkulationMaterial === opt ? 'selected' : ''}>${opt}</option>
                          `).join('')}
                        </select>
                        <input type="text" value="${config.rohrZirkulationDN}" oninput="handleConfigChange('rohrZirkulationDN', this.value)" placeholder="DN" class="w-1/3 p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring" />
                      </div>
                    </div>
                  ` : ''}

                  <div class="flex flex-col gap-1.5 mb-3 pt-3 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-700">Fahrzeug</label>
                    <select onchange="handleConfigChange('autoArt', this.value)" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-sm font-medium text-slate-800 focus-ring">
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
                </div>
              ` : ''}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
              <button onclick="toggleSidebarSection('kaminSolar')" class="w-full flex justify-between items-center p-3.5 bg-slate-50 hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${colorBrown}">${Icons.thermometer()}</span>
                  Zusatzheizung
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.kaminSolar ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.kaminSolar ? `
                <div class="p-4 border-t border-slate-100 bg-white">
                  <label class="flex items-center gap-2 cursor-pointer mb-2">
                    <input type="checkbox" ${config.kaminVorhanden ? 'checked' : ''} onchange="handleConfigChange('kaminVorhanden', this.checked)" class="w-4 h-4 rounded" style="accent-color:${colorBrown}" />
                    <span class="text-xs font-bold text-slate-700">Kaminfeuer / Stückholz</span>
                  </label>
                  ${config.kaminVorhanden ? `
                    <div class="space-y-3">
                      <div class="flex gap-3">
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Bedarf (Raummeter)</label><input type="number" value="${config.holzVerbrauch}" oninput="handleConfigChange('holzVerbrauch', Number(this.value))" class="w-full p-2 border rounded-lg" /></div>
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Preis (€/RM)</label><input type="number" value="${config.preisHolz}" oninput="handleConfigChange('preisHolz', Number(this.value))" class="w-full p-2 border rounded-lg" /></div>
                      </div>
                      <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                        <input type="checkbox" ${config.kaminWeiterBetreiben ? 'checked' : ''} onchange="handleConfigChange('kaminWeiterBetreiben', this.checked)" class="w-4 h-4 rounded" style="accent-color:${colorBrown}" />
                        <span class="text-xs font-semibold text-slate-700">Wird im Neusystem weiter befeuert</span>
                      </label>
                    </div>
                  ` : ''}

                  <label class="flex items-center gap-2 cursor-pointer mb-2 mt-4 pt-4 border-t border-slate-100">
                    <input type="checkbox" ${config.solarthermieVorhanden ? 'checked' : ''} onchange="handleConfigChange('solarthermieVorhanden', this.checked)" class="w-4 h-4 rounded" style="accent-color:${colorBrown}" />
                    <span class="text-xs font-bold text-slate-700">Solarthermie vorhanden</span>
                  </label>
                  ${config.solarthermieVorhanden ? `
                    <div class="space-y-3">
                      <div class="flex gap-3">
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Kollektor-Art</label><select onchange="handleConfigChange('solarthermieArt', this.value)" class="w-full p-2 border rounded-lg bg-white"><option ${config.solarthermieArt === 'Flachkollektor' ? 'selected' : ''}>Flachkollektor</option><option ${config.solarthermieArt === 'Röhrenkollektor' ? 'selected' : ''}>Röhrenkollektor</option></select></div>
                        <div class="w-1/2 space-y-1"><label class="text-xs text-slate-600">Anzahl Kollektoren</label><input type="number" value="${config.solarKollektoren}" oninput="handleConfigChange('solarKollektoren', Number(this.value))" class="w-full p-2 border rounded-lg" /></div>
                      </div>
                      <label class="flex items-center gap-2 cursor-pointer mt-2 pt-2 border-t border-slate-200">
                        <input type="checkbox" ${config.solarthermieWeiterBetreiben ? 'checked' : ''} onchange="handleConfigChange('solarthermieWeiterBetreiben', this.checked)" class="w-4 h-4 rounded" style="accent-color:${colorBrown}" />
                        <span class="text-xs font-semibold text-slate-700">Bleibt auf dem Dach / in Nutzung</span>
                      </label>
                    </div>
                  ` : ''}
                </div>
              ` : ''}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
              <button onclick="toggleSidebarSection('preise')" class="w-full flex justify-between items-center p-3.5 bg-slate-50 hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${colorBrown}">${Icons.trendingUp()}</span>
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
                  ${sidebarInput({
                    label: `Preis ${config.heizungArt}`,
                    type: 'number',
                    step: '0.01',
                    value: config.preisHeizMedium,
                    rightLabel: `€/${getHeizEinheit(config.heizungArt)}`,
                    oninput: `handleConfigChange('preisHeizMedium', Number(this.value))`
                  })}
                  ${sidebarInput({
                    label: 'Einspeisevergütung',
                    type: 'number',
                    step: '0.01',
                    value: config.preisEinspeisung,
                    rightLabel: '€/kWh',
                    oninput: `handleConfigChange('preisEinspeisung', Number(this.value))`
                  })}
                  ${sidebarInput({
                    label: 'Energie-Inflation',
                    type: 'number',
                    step: '0.5',
                    value: config.inflationRate,
                    rightLabel: '%/a',
                    oninput: `handleConfigChange('inflationRate', Number(this.value))`
                  })}
                </div>
              ` : ''}
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-4">
              <button onclick="toggleSidebarSection('investitionen')" class="w-full flex justify-between items-center p-3.5 bg-slate-50 hover:bg-slate-100 transition-colors">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${colorBrown}">${Icons.euro()}</span>
                  Investitionen
                </h3>
                <span class="w-4 h-4 text-slate-500 transition-transform duration-200 ${state.sidebarSections.investitionen ? 'rot-180' : ''}">
                  ${Icons.chevronDown()}
                </span>
              </button>
              ${state.sidebarSections.investitionen ? `
                <div class="p-4 border-t border-slate-100 bg-white space-y-5">
                  <div class="p-3 border border-slate-100 bg-slate-50 rounded-lg">
                    <h4 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.thermoSnow()}</span> Wärmepumpe</h4>
                    ${sidebarInput({ label:'Preis (Brutto)', type:'number', step:'1000', value:config.costWP, rightLabel:'€', oninput:`handleConfigChange('costWP', Number(this.value))` })}
                    ${sidebarInput({ label:'kW (Manuell)', type:'number', step:'1', value:config.customWpKw, rightLabel:`Empf: ${derivedParams.empfohleneWpKw} kW`, placeholder:'Auto', oninput:`handleConfigChange('customWpKw', this.value)` })}
                    ${sidebarInput({ label:'JAZ (Manuell)', type:'number', step:'0.1', value:config.customJAZ, rightLabel:`Auto: ${derivedParams.berechneteJaz}`, placeholder:'Auto', oninput:`handleConfigChange('customJAZ', this.value)` })}
                    ${sidebarInput({ label:'Kombi-Rabatt', type:'number', step:'100', value:config.discountWP, rightLabel:'€', placeholder:'1000', oninput:`handleConfigChange('discountWP', this.value)` })}
                    ${sidebarInput({ label:'Zusätzl. Förderung', type:'number', step:'100', value:config.extraGrantWP, rightLabel:'€', placeholder:'0', oninput:`handleConfigChange('extraGrantWP', this.value)` })}
                    ${sidebarInput({ label:'Förderquelle WP', type:'text', value:config.extraGrantSourceWP, placeholder:'z.B. Stadt Bad Homburg', oninput:`handleConfigChange('extraGrantSourceWP', this.value)` })}
                  </div>

                  <div class="p-3 border border-slate-100 bg-slate-50 rounded-lg">
                    <h4 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.sun()}</span> PV & Speicher</h4>
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

                  <div class="p-3 border border-slate-100 bg-slate-50 rounded-lg">
                    <h4 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1"><span class="w-3.5 h-3.5">${Icons.car()}</span> Wallbox</h4>
                    ${sidebarInput({ label:'Preis (Brutto)', type:'number', step:'100', value:config.costWallbox, rightLabel:'€', oninput:`handleConfigChange('costWallbox', Number(this.value))` })}
                    ${sidebarInput({ label:'Kombi-Rabatt', type:'number', step:'100', value:config.discountWallbox, rightLabel:'€', placeholder:'150', oninput:`handleConfigChange('discountWallbox', this.value)` })}
                    ${sidebarInput({ label:'Zusätzl. Förderung', type:'number', step:'100', value:config.extraGrantWallbox, rightLabel:'€', placeholder:'0', oninput:`handleConfigChange('extraGrantWallbox', this.value)` })}
                    ${sidebarInput({ label:'Förderquelle Wallbox', type:'text', value:config.extraGrantSourceWallbox, placeholder:'z.B. KfW', oninput:`handleConfigChange('extraGrantSourceWallbox', this.value)` })}
                  </div>
                </div>
              ` : ''}
            </div>
          </div>

          <div class="p-5 bg-white border-t border-slate-200 sticky bottom-0 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)]">
            <button onclick="setSidebarOpen(false)" class="w-full flex justify-center items-center gap-2 px-4 py-3 text-white rounded-xl text-sm font-bold transition-colors shadow-lg" style="background:${colorBrown};box-shadow:0 10px 15px -3px ${colorBrown}40">
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

      return `
        <div class="min-h-screen bg-slate-200 text-slate-800 font-sans pb-20 pt-16 print:p-0 print:bg-white relative overflow-x-hidden">
          <div class="fixed top-0 left-0 w-full bg-slate-900 text-white p-4 z-[80] flex justify-between items-center shadow-lg no-print">
            <div class="font-bold flex items-center gap-3" style="color:${colorBeige}">
              <span class="w-5 h-5">${Icons.printer()}</span>
              Druckvorschau: WERK STUDIO Report
            </div>
            <div class="flex gap-3">
              <button onclick="setView('wizard')" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm font-semibold transition-colors">
                Zurück zum Wizard
              </button>
              <button onclick="setSidebarOpen(true)" class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-bold transition-colors shadow-lg" style="background:${colorBrown}">
                <span class="w-4 h-4">${Icons.sliders()}</span>
                Parameter anpassen
              </button>
              <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-slate-900 rounded-lg text-sm font-bold transition-colors ml-2" style="background:${colorBeige}">
                Als PDF speichern
              </button>
            </div>
          </div>

          ${renderSidebar(computed)}

          <div class="w-full transition-all duration-300 print:m-0 ${state.isSidebarOpen ? 'md:mr-[360px] lg:mx-auto lg:translate-x-[-180px]' : ''}">

            <!-- PAGE 1 -->
            <div class="a4-page flex flex-col relative bg-[#f5f5f4] justify-center items-center print:bg-[#f5f5f4]" style="WebkitPrintColorAdjust:exact;printColorAdjust:exact">
              <div class="absolute top-0 inset-x-0 h-[35%] rounded-b-[40%] shadow-2xl" style="background:${colorBrown}"></div>
              <div class="absolute top-16 font-bold text-2xl tracking-[0.3em] flex items-center gap-3" style="color:${colorBeige}">
                <span class="text-5xl">ॐ</span> WERK STUDIO
              </div>
              <div class="bg-white p-16 rounded-[40px] shadow-2xl border border-slate-100 text-center z-10 w-[85%] mt-10">
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 tracking-tight leading-tight">
                  IHR INDIVIDUELLES<br/>ENERGIEKONZEPT
                </h1>
                <div class="w-20 h-2 mx-auto rounded-full mb-10" style="background:${colorBrown}"></div>
                <p class="text-2xl text-slate-600 font-medium mb-3">Für Familie ${config.name}</p>
                <p class="text-base text-slate-400 flex items-center justify-center gap-2">
                  <span class="w-4.5 h-4.5">${Icons.mapPin()}</span>
                  Objektstandort: ${config.plz}
                </p>
              </div>
              <div class="mt-auto mb-16 text-center z-10">
                <div class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3">Ausgearbeitet von</div>
                <div class="text-xl font-black text-slate-800 tracking-wide">WERK STUDIO BAUKONZEPT</div>
                <div class="text-sm text-slate-500 mt-2">Meisterbetrieb für Gebäudetechnik & erneuerbare Energien</div>
              </div>
              <div class="absolute bottom-[-100px] left-[-100px] w-[500px] h-[500px] rounded-full blur-[120px] opacity-10" style="background:${colorBrown}"></div>
            </div>

            <!-- PAGE 2 -->
            <div class="a4-page flex flex-col relative">
              ${ReportHeader('IHR KONZEPT')}
              <div class="absolute top-0 right-0 w-64 h-64 rounded-full blur-[120px] opacity-10 print:opacity-5" style="background:${colorBrown}"></div>
              <div class="font-bold text-sm tracking-[0.2em] mb-12 flex items-center gap-2" style="color:${colorBrown}">
                <span class="text-2xl">ॐ</span> WERK STUDIO BAUKONZEPT
              </div>
              <h1 class="text-4xl font-black mb-4 leading-tight text-slate-900">IHR WEG ZUR EIGENEN<br/>ENERGIEAUTARKIE</h1>
              <p class="text-lg font-bold mb-16 tracking-wide" style="color:${colorBrown}">
                WENIGER NETZ. MEHR KONTROLLE.<br/>MAXIMALE EFFIZIENZ – JEDEN TAG
              </p>
              <div class="space-y-6 text-[15px] leading-relaxed text-slate-700 mb-12 flex-1">
                <p>Sehr geehrte(r) ${config.name},</p>
                <p>vielen Dank für Ihr Interesse an einer zukunftssicheren und autarken Energieversorgung. Gerne stellen wir Ihnen Ihr maßgeschneidertes Energiekonzept vor.</p>
                <p>Auf den folgenden Seiten sehen Sie transparent, wie sich Ihr Energieprofil durch die intelligente Vernetzung von Wärmepumpe, Photovoltaik und Speicher optimieren lässt. Zudem haben wir alle relevanten staatlichen Förderungen integriert, um Ihre Netto-Investition so effizient wie möglich zu gestalten.</p>
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-100 shadow-sm mt-8">
                  <p class="font-bold text-slate-900 mb-4">Ihr Energiekonzept im Überblick:</p>
                  <div class="grid grid-cols-2 gap-4 text-sm text-slate-700">
                    ${[
                      ['1. Ausgangslage:', 'Ihre heutigen Energiekosten'],
                      ['2. Lösungsarchitektur:', 'Dimensionierung von PV und Speicher'],
                      ['3. Saisonale Auswertung:', 'Autarkie im Jahresverlauf'],
                      ['4. WERK STUDIO Expertise:', 'Ihre System-Vorteile'],
                      ['5. Sonnenenergie:', 'Photovoltaik & Batteriespeicher'],
                      ['6. Wärmepumpen-Technologie:', 'Elektrifizierung der Wärme'],
                      ['7. E-Mobilität:', 'Zapfen Sie die Sonne an'],
                      ['8. Sektorenkopplung:', 'Das intelligente Gesamtsystem'],
                      ['9. Wirtschaftlichkeit:', 'Investition, Break-Even & ROI'],
                      ['10. Transparenz Teil I:', 'Technische Berechnungen'],
                      ['11. Transparenz Teil II:', 'Kennzahlen & Effizienz'],
                      ['12. Klimaschutz & Ablauf:', 'Nächste Schritte']
                    ].map(item => `
                      <div class="flex gap-3">
                        <span class="w-4 h-4 shrink-0 mt-0.5" style="color:${colorBrown}">${Icons.checkSquare()}</span>
                        <span><strong class="text-slate-900 block">${item[0]}</strong>${item[1]}</span>
                      </div>
                    `).join('')}
                  </div>
                </div>
              </div>
              <div class="mt-auto text-[15px] text-slate-700">
                <p class="mb-8">Für Ihre Fragen und die nächsten Schritte stehen wir Ihnen jederzeit gerne in einem persönlichen Beratungsgespräch zur Verfügung.</p>
                <p>Mit freundlichen Grüßen</p>
                <p class="mt-4 font-bold font-serif text-lg text-slate-900">Ihr WERK STUDIO-Team</p>
              </div>
            </div>

            <!-- PAGE 3 -->
            <div class="a4-page flex flex-col bg-slate-50/30">
              ${ReportHeader('1. AUSGANGSLAGE')}
              <h2 class="text-xl font-black text-slate-900 mb-4">1. AUSGANGSLAGE & ENERGIE-TRANSFORMATION</h2>
              <div class="mb-4">
                <h3 class="text-2xl font-black text-slate-900 mb-1">Der 3-Stufen-Vergleich: Warum nur die Komplettlösung schützt</h3>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                  Um die wahre Effizienz unseres Konzepts zu verstehen, betrachten wir drei Szenarien: Ihr <strong>heutiges fossiles System</strong>, eine <strong>reine Elektrifizierung</strong> (Wärmepumpe & E-Auto ohne eigene PV) und die <strong>WERK STUDIO Komplettlösung</strong> inkl. §14a EnWG Netzentgelt-Rabatt.
                </p>
              </div>

              <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col">
                  <h3 class="font-bold text-slate-800 text-xs mb-2 border-b border-slate-100 pb-1">1. Fossiles Altsystem (Status Quo)</h3>
                  <table class="w-full text-left text-[9px] text-slate-600 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-1">Hausstrom<br/><span class="text-[7.5px] text-slate-400">${formatDE(config.hhStrom)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-1">${formatDE(Math.round(config.hhStrom * config.preisStrom))} €</td>
                      </tr>
                      <tr>
                        <td class="py-1">Heizung (${config.heizungArt})<br/><span class="text-[7.5px] text-slate-400">${formatDE(config.heizVerbrauch)} ${getHeizEinheit(config.heizungArt)} × ${formatDE(config.preisHeizMedium,2)} €/${getHeizEinheit(config.heizungArt)}</span></td>
                        <td class="text-right font-medium align-top py-1">${formatDE(Math.round(finance.heizkostenOld))} €</td>
                      </tr>
                      ${config.kaminVorhanden ? `
                        <tr>
                          <td class="py-1">Kaminholz<br/><span class="text-[7.5px] text-slate-400">${formatDE(config.holzVerbrauch)} RM × ${formatDE(config.preisHolz,2)} €/RM</span></td>
                          <td class="text-right font-medium align-top py-1">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                        </tr>
                      ` : ''}
                      ${config.fahrleistung > 0 ? `
                        <tr>
                          <td class="py-1">
                            Auto (${config.autoArt === 'Verbrenner' ? 'Verbrenner' : 'E-Auto'})<br/>
                            <span class="text-[7.5px] text-slate-400">
                              ${config.autoArt === 'Verbrenner'
                                ? `${formatDE(Math.round((config.fahrleistung/100)*config.verbrennerVerbrauch))} l × ${formatDE(config.preisSprit,2)} €/l`
                                : `${formatDE(Math.round((config.fahrleistung/100)*20))} kWh × ${formatDE(config.preisStrom,2)} €/kWh`
                              }
                            </span>
                          </td>
                          <td class="text-right font-medium align-top py-1">${formatDE(Math.round(finance.evOldCost))} €</td>
                        </tr>
                      ` : ''}
                      <tr>
                        <td class="py-1">Wartung & Fixkosten<br/><span class="text-[7.5px] text-slate-400">Pauschale</span></td>
                        <td class="text-right font-medium align-top py-1">${config.wartungOld} €</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="mt-auto pt-2 border-t border-slate-200 flex justify-between font-black text-slate-700 text-[11px]">
                    <span>Kosten p.a.</span><span>${formatDE(Math.round(finance.costOldTotal))} €</span>
                  </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col">
                  <h3 class="font-bold text-slate-800 text-xs mb-2 border-b border-slate-100 pb-1">2. Nur Elektrisch (Ohne PV)</h3>
                  <table class="w-full text-left text-[9px] text-slate-600 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-1">Hausstrom<br/><span class="text-[7.5px] text-slate-400">${formatDE(config.hhStrom)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-1">${formatDE(Math.round(config.hhStrom * config.preisStrom))} €</td>
                      </tr>
                      <tr>
                        <td class="py-1">Wärmepumpe<br/><span class="text-[7.5px] text-slate-400">${formatDE(derivedParams.wpStrombedarf)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-1">${formatDE(Math.round(derivedParams.wpStrombedarf * config.preisStrom))} €</td>
                      </tr>
                      ${config.kaminVorhanden && config.kaminWeiterBetreiben ? `
                        <tr>
                          <td class="py-1">Kaminholz (Weiterbetrieb)<br/><span class="text-[7.5px] text-slate-400">${formatDE(config.holzVerbrauch)} RM × ${formatDE(config.preisHolz,2)} €/RM</span></td>
                          <td class="text-right font-medium align-top py-1">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                        </tr>
                      ` : ''}
                      ${config.fahrleistung > 0 ? `
                        <tr>
                          <td class="py-1">E-Auto Laden<br/><span class="text-[7.5px] text-slate-400">${formatDE(derivedParams.evStrombedarf)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                          <td class="text-right font-medium align-top py-1">${formatDE(Math.round(derivedParams.evStrombedarf * config.preisStrom))} €</td>
                        </tr>
                      ` : ''}
                      <tr>
                        <td class="py-1">Wartung & Fixkosten<br/><span class="text-[7.5px] text-slate-400">Pauschale</span></td>
                        <td class="text-right font-medium align-top py-1">${config.wartungOld} €</td>
                      </tr>
                      <tr class="text-green-600">
                        <td class="py-1">§14a EnWG Rabatt<br/><span class="text-[7.5px] opacity-80">Modul 1 / Modul 2 (opt.)</span></td>
                        <td class="text-right font-medium align-top py-1">-${finance.ersparnis14aAllElectric} €</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="text-[8px] text-slate-400 italic mb-2 leading-tight">Die WP macht Ihre Heizung effizienter, aber Sie machen sich abhängig vom Netzstrompreis.</div>
                  <div class="mt-auto pt-2 border-t border-slate-200 flex justify-between font-black text-slate-700 text-[11px]">
                    <span>Kosten p.a.</span><span>${formatDE(Math.round(finance.costAllElectricBase))} €</span>
                  </div>
                </div>

                <div class="bg-slate-50 border-2 rounded-xl p-4 shadow-sm flex flex-col" style="border-color:${colorBrown}">
                  <h3 class="font-bold text-slate-900 text-xs mb-2 border-b border-slate-200 pb-1">3. Komplettlösung (Mit PV)</h3>
                  <table class="w-full text-left text-[9px] text-slate-700 mb-2">
                    <tbody>
                      <tr>
                        <td class="py-1 font-semibold">Gesamtstrombedarf<br/><span class="text-[7.5px] text-slate-400 font-normal">inkl. WP & E-Auto</span></td>
                        <td class="text-right font-bold align-top py-1">${formatDE(kpis.totalBedarf)} kWh</td>
                      </tr>
                      <tr style="color:${colorBrown}">
                        <td class="py-1">Kostenlos durch PV<br/><span class="text-[7.5px] opacity-70">Direktverbrauch & Speicher</span></td>
                        <td class="text-right font-bold align-top py-1">-${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh</td>
                      </tr>
                      <tr>
                        <td class="py-1">Rest-Netzbezug<br/><span class="text-[7.5px] text-slate-500">${formatDE(kpis.totalNetzbezug)} kWh × ${formatDE(config.preisStrom,2)} €/kWh</span></td>
                        <td class="text-right font-medium align-top py-1">${formatDE(Math.round(kpis.totalNetzbezug * config.preisStrom))} €</td>
                      </tr>
                      ${config.kaminVorhanden && config.kaminWeiterBetreiben ? `
                        <tr>
                          <td class="py-1">Kaminholz<br/><span class="text-[7.5px] text-slate-500">Bleibt im System</span></td>
                          <td class="text-right font-medium align-top py-1">${formatDE(Math.round(derivedParams.kaminKosten))} €</td>
                        </tr>
                      ` : ''}
                      <tr>
                        <td class="py-1">Wartung & Fixkosten<br/><span class="text-[7.5px] text-slate-500">Pauschale</span></td>
                        <td class="text-right font-medium align-top py-1">${config.wartungOld} €</td>
                      </tr>
                      <tr class="text-green-600">
                        <td class="py-1">§14a EnWG Rabatt<br/><span class="text-[7.5px] opacity-80">Modul 1 / Modul 2 (opt.)</span></td>
                        <td class="text-right font-medium align-top py-1">-${finance.ersparnis14a} €</td>
                      </tr>
                      <tr style="color:${colorBeige}">
                        <td class="py-1 font-bold">Einspeisevergütung<br/><span class="text-[7.5px] font-normal">${formatDE(kpis.totalNetzeinspeisung)} kWh × ${formatDE(config.preisEinspeisung,2)} €/kWh</span></td>
                        <td class="text-right font-bold align-top py-1">-${formatDE(Math.round(kpis.totalNetzeinspeisung * config.preisEinspeisung))} €</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="mt-auto pt-2 border-t border-slate-300 flex justify-between font-black text-[12px]" style="color:${colorBrown}">
                    <span>Restkosten p.a.</span><span>${formatDE(Math.round(finance.costNewTotal))} €</span>
                  </div>
                </div>
              </div>

              <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-auto flex flex-col">
                <div class="bg-slate-50 p-3 border-b border-slate-200">
                  <h4 class="font-bold text-slate-800 text-[13px] flex items-center gap-2 mb-1">
                    <span class="w-4 h-4" style="color:${colorBrown}">${Icons.trendingUp()}</span>
                    Prognose der Kostenentwicklung (inkl. ${config.inflationRate}% Inflation p.a.)
                  </h4>
                  <p class="text-[10px] text-slate-500">Diese Tabelle zeigt drastisch: Wer nichts tut, zahlt am meisten. Wer nur auf Strom umstellt, zahlt immer noch viel. Nur die Kombination mit eigener PV-Erzeugung schützt Ihr Vermögen langfristig.</p>
                </div>

                <div class="p-0">
                  ${(() => {
                    const pct1 = finance.costOldTotal > 0 ? Math.round(((finance.costOldTotal - finance.costNewTotal) / finance.costOldTotal) * 100) : 0;
                    const pct10 = finance.oldCostCumulative10 > 0 ? Math.round((finance.ersparnis10 / finance.oldCostCumulative10) * 100) : 0;
                    const pct20 = finance.oldCostCumulative20 > 0 ? Math.round((finance.ersparnis20 / finance.oldCostCumulative20) * 100) : 0;
                    const pct30 = finance.oldCostCumulative30 > 0 ? Math.round((finance.ersparnis30 / finance.oldCostCumulative30) * 100) : 0;
                    return `
                      <table class="w-full text-xs text-left">
                        <thead class="bg-white text-slate-500 text-[10px] uppercase tracking-wider border-b border-slate-200">
                          <tr>
                            <th class="p-3 font-semibold">Zeitraum</th>
                            <th class="p-3 font-semibold">Fossiles Altsystem</th>
                            <th class="p-3 font-semibold">Nur Elektrisch (Ohne PV)</th>
                            <th class="p-3 font-black" style="color:${colorBrown}">Neusystem (Mit PV)</th>
                            <th class="p-3 font-black text-green-600">Gesamtersparnis</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                          <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold">1. Jahr</td>
                            <td class="p-3 font-medium">${formatDE(Math.round(finance.costOldTotal))} €</td>
                            <td class="p-3 font-medium">${formatDE(Math.round(finance.costAllElectricBase))} €</td>
                            <td class="p-3 font-black" style="color:${colorBrown}">${formatDE(Math.round(finance.costNewTotal))} €</td>
                            <td class="p-3 font-black text-green-600">
                              <div class="flex items-center whitespace-nowrap">
                                +${formatDE(Math.round(finance.costOldTotal - finance.costNewTotal))} €
                                <span class="ml-2 text-[9px] bg-green-50 text-green-700 border border-green-100 px-1.5 py-0.5 rounded-md">(${pct1}%)</span>
                              </div>
                            </td>
                          </tr>
                          <tr class="bg-slate-50/30 hover:bg-slate-50">
                            <td class="p-3 font-bold">Über 10 Jahre</td>
                            <td class="p-3 font-medium">${formatDE(finance.oldCostCumulative10)} €</td>
                            <td class="p-3 font-medium">${formatDE(finance.electricCostCumulative10)} €</td>
                            <td class="p-3 font-black" style="color:${colorBrown}">${formatDE(finance.newCostCumulative10)} €</td>
                            <td class="p-3 font-black text-green-600">
                              <div class="flex items-center whitespace-nowrap">
                                +${formatDE(finance.ersparnis10)} €
                                <span class="ml-2 text-[9px] bg-green-50 text-green-700 border border-green-100 px-1.5 py-0.5 rounded-md">(${pct10}%)</span>
                              </div>
                            </td>
                          </tr>
                          <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold">Über 20 Jahre</td>
                            <td class="p-3 font-medium">${formatDE(finance.oldCostCumulative20)} €</td>
                            <td class="p-3 font-medium">${formatDE(finance.electricCostCumulative20)} €</td>
                            <td class="p-3 font-black" style="color:${colorBrown}">${formatDE(finance.newCostCumulative20)} €</td>
                            <td class="p-3 font-black text-green-600">
                              <div class="flex items-center whitespace-nowrap">
                                +${formatDE(finance.ersparnis20)} €
                                <span class="ml-2 text-[9px] bg-green-50 text-green-700 border border-green-100 px-1.5 py-0.5 rounded-md">(${pct20}%)</span>
                              </div>
                            </td>
                          </tr>
                          <tr class="bg-slate-50/30 hover:bg-slate-50">
                            <td class="p-3 font-bold">Über 30 Jahre</td>
                            <td class="p-3 font-medium">${formatDE(finance.oldCostCumulative30)} €</td>
                            <td class="p-3 font-medium">${formatDE(finance.electricCostCumulative30)} €</td>
                            <td class="p-3 font-black" style="color:${colorBrown}">${formatDE(finance.newCostCumulative30)} €</td>
                            <td class="p-3 font-black text-green-600">
                              <div class="flex items-center whitespace-nowrap">
                                +${formatDE(finance.ersparnis30)} €
                                <span class="ml-2 text-[9px] bg-green-50 text-green-700 border border-green-100 px-1.5 py-0.5 rounded-md">(${pct30}%)</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    `;
                  })()}
                </div>
              </div>

              <div class="text-[9px] text-slate-400 mt-3 pt-3 border-t border-slate-200">
                <strong>Hinweis zu §14a EnWG:</strong> Da Sie mit einer Wärmepumpe oder Wallbox "steuerbare Verbrauchseinrichtungen" (SteuVE) nutzen, profitieren Sie gesetzlich von reduzierten Netzentgelten. Das System berechnet automatisch das für Sie günstigste Modell (Pauschale Modul 1 vs. prozentuale Reduzierung des Arbeitspreises Modul 2 um 60% der Netzentgelte). In der "Elektrisch (Ohne PV)" Variante beträgt die Ersparnis ${finance.ersparnis14aAllElectric} €, mit PV-Anlage (durch den geringeren Netzbezug) ${finance.ersparnis14a} €.
              </div>
            </div>

            <!-- PAGE 4 -->
            <div class="a4-page flex flex-col bg-white">
              ${ReportHeader('2. LÖSUNGSARCHITEKTUR')}
              <h2 class="text-xl font-black text-slate-900 mb-2">2. SYSTEMAUSLEGUNG & UNABHÄNGIGKEIT</h2>
              <p class="text-sm text-slate-600 mb-8 leading-relaxed">Das Geheimnis maximaler Ersparnis liegt in der cleveren Sektorenkopplung: Haushalt, Wärme und Mobilität verschmelzen zu einem intelligenten Kreislauf. Ihr selbst produziert Solarstrom wird vom Dach direkt dorthin geleitet, wo er am teuersten wäre, wenn Sie ihn einkaufen müssten.</p>
              <h3 class="font-black text-xl mb-4" style="color:${colorBrown}">Ihre Gesamtbilanz auf einen Blick</h3>

              <div class="grid grid-cols-3 gap-5 mb-8">
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col items-center justify-between">
                  <div class="relative w-28 h-28 mb-4">
                    <div class="chart-wrap"><canvas id="donutAutarkie"></canvas></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                      <span class="text-2xl font-black text-slate-800">${kpis.autarkie}%</span>
                    </div>
                  </div>
                  <div class="text-center mb-4">
                    <h4 class="font-black text-slate-800 text-[11px] tracking-widest uppercase mb-1">Autarkiegrad</h4>
                    <p class="text-[9px] text-slate-800 font-bold">Gesamte Bedarfsdeckung</p>
                  </div>
                  <div class="w-full text-[10px] text-slate-600 space-y-1.5 border-t border-slate-300 pt-3">
                    <div class="flex justify-between">
                      <span class="flex items-center gap-1">
                        <div class="flex space-x-[-4px]">
                          <div class="w-2 h-2 rounded-full border border-white" style="background:${donutFillColor}"></div>
                          <div class="w-2 h-2 rounded-full border border-white" style="background:${donutBatteryColor}"></div>
                        </div>
                        Deckung
                      </span>
                      <span class="font-bold text-slate-800" style="color:${donutFillColor}">${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full" style="background:${donutEmptyColor}"></div>Netzbezug</span>
                      <span class="font-bold text-slate-800">${formatDE(kpis.totalNetzbezug)} kWh</span>
                    </div>
                  </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col items-center justify-between">
                  <div class="relative w-28 h-28 mb-4">
                    <div class="chart-wrap"><canvas id="donutEigenverbrauch"></canvas></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                      <span class="text-2xl font-black text-slate-800">${kpis.eigenverbrauchQuote}%</span>
                    </div>
                  </div>
                  <div class="text-center mb-4">
                    <h4 class="font-black text-slate-800 text-[11px] tracking-widest uppercase mb-1">Eigenverbrauchsquote</h4>
                    <p class="text-[9px] text-slate-800 font-bold">Nutzung des PV-Stroms</p>
                  </div>
                  <div class="w-full text-[10px] text-slate-600 space-y-1.5 border-t border-slate-300 pt-3">
                    <div class="flex justify-between">
                      <span class="flex items-center gap-1">
                        <div class="flex space-x-[-4px]">
                          <div class="w-2 h-2 rounded-full border border-white" style="background:${donutFillColor}"></div>
                          <div class="w-2 h-2 rounded-full border border-white" style="background:${donutBatteryColor}"></div>
                        </div>
                        Genutzt
                      </span>
                      <span class="font-bold" style="color:${einspeisungColor}">${formatDE(kpis.totalDirekt + kpis.totalBatterie)} kWh</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full" style="background:${donutEmptyColor}"></div>Einspeisung</span>
                      <span class="font-bold text-slate-800">${formatDE(kpis.totalNetzeinspeisung)} kWh</span>
                    </div>
                  </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col items-center justify-between">
                  <div class="relative w-28 h-28 mb-4">
                    <div class="chart-wrap"><canvas id="donutFinanz"></canvas></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                      <span class="text-2xl font-black text-slate-800">${finance.finUnabhProzent}%</span>
                    </div>
                  </div>
                  <div class="text-center mb-4">
                    <h4 class="font-black text-slate-800 text-[11px] tracking-widest uppercase mb-1">Finanz-Unabhängigkeit</h4>
                    <p class="text-[9px] text-slate-800 font-bold">Schutz vor Preisanstieg</p>
                  </div>
                  <div class="w-full text-[10px] text-slate-600 space-y-1.5 border-t border-slate-300 pt-3">
                    <div class="flex justify-between">
                      <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full" style="background:${donutFillColor}"></div>Ersparnis</span>
                      <span class="font-bold text-slate-800">+${formatDE(finance.ersparnisJahr1)} €</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full" style="background:${donutEmptyColor}"></div>Vorher</span>
                      <span class="font-bold text-slate-800">${formatDE(Math.round(finance.costOldTotal))} €</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex-1"></div>

              <div class="flex justify-between items-center pb-6 border-b border-slate-300">
                <div class="w-[55%]">
                  <h4 class="font-bold text-slate-800 text-sm mb-2 uppercase tracking-wide">Ihre System-Dimensionierung</h4>
                  <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    <strong class="block mb-1 text-slate-800">Warum empfehlen wir exakt ${derivedParams.pvKwp} kWp PV und ${derivedParams.batteryCapacity} kWh Speicher?</strong>
                    Die Systemauslegung orientiert sich an Ihrem zukünftigen Gesamtbedarf aus Haushaltsstrom, Wärmepumpe und E-Mobilität. So bleibt Ihr Netzbezug minimal, während der Stromspeicher die Nachtstunden und Übergangszeiten zuverlässig für Sie abfedert.
                  </p>
                </div>

                <div class="w-[40%] flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                  <div class="w-20 h-20 relative shrink-0">
                    <div class="chart-wrap"><canvas id="bedarfsmixChart"></canvas></div>
                  </div>
                  <div class="flex-1 space-y-2 text-[10px]">
                    <div class="flex justify-between items-center">
                      <span class="flex items-center gap-1.5 font-medium text-slate-600"><div class="w-2 h-2 rounded-full shadow-sm" style="background:${colorLightGray}"></div>Haushalt</span>
                      <span class="font-bold text-slate-800">${formatDE(config.hhStrom)}</span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="flex items-center gap-1.5 font-medium text-slate-600"><div class="w-2 h-2 rounded-full shadow-sm" style="background:${colorBeige}"></div>WP</span>
                      <span class="font-bold text-slate-800">${formatDE(derivedParams.wpStrombedarf)}</span>
                    </div>
                    ${config.fahrleistung > 0 ? `
                      <div class="flex justify-between items-center">
                        <span class="flex items-center gap-1.5 font-medium text-slate-600"><div class="w-2 h-2 rounded-full shadow-sm" style="background:${colorBrown}"></div>Auto</span>
                        <span class="font-bold text-slate-800">${formatDE(derivedParams.evStrombedarf)}</span>
                      </div>
                    ` : ''}
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-4 pt-6">
                <div class="text-center">
                  <span class="font-semibold text-slate-500 text-[10px] uppercase mb-1 block">Wärmepumpe</span>
                  <span class="font-black text-2xl text-slate-800">${derivedParams.wpLeistungKW} kW</span>
                </div>
                <div class="text-center border-l border-slate-200">
                  <span class="font-semibold text-slate-500 text-[10px] uppercase mb-1 block">Photovoltaik</span>
                  <span class="font-black text-2xl text-slate-800">${derivedParams.pvKwp} kWp</span>
                </div>
                <div class="text-center border-l border-slate-200">
                  <span class="font-semibold text-slate-500 text-[10px] uppercase mb-1 block">Speicher</span>
                  <span class="font-black text-2xl text-slate-800">${derivedParams.batteryCapacity} kWh</span>
                </div>
              </div>

              ${config.costWallbox > 0 ? `
                <div class="mt-4 text-[10px] text-slate-500 uppercase tracking-widest text-center w-full">
                  Inklusive intelligenter Lade-Infrastruktur für Elektromobilität (Wallbox)
                </div>
              ` : ''}
            </div>

            <!-- PAGE 5 -->
            <div class="a4-page flex flex-col">
              ${ReportHeader('3. SAISONALE AUSWERTUNG')}
              <h2 class="text-xl font-black text-slate-400 mb-2 uppercase tracking-widest">3. SAISONALE VERTEILUNG & AUTARKIE</h2>
              <h3 class="font-black text-xl mb-6 uppercase tracking-widest" style="color:${colorBrown}">Der Verlauf über das Jahr</h3>

              <div class="grid grid-cols-2 gap-6 mb-4">
                ${seasonalData.map((season, i) => {
                  const total = season.Gesamtbedarf;
                  const pDeckung = season.autarkie;
                  const pZukauf = 100 - pDeckung;
                  const pEinspeisung = season.Solarertrag > 0 ? Math.round((Math.abs(season.NetzeinspeisungNeg) / season.Solarertrag) * 100) : 0;
                  return `
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col items-center">
                      <span class="font-black tracking-widest uppercase mb-4 text-xs" style="color:${donutFillColor}">${season.name}</span>
                      <div class="relative w-28 h-28 mb-3">
                        <div class="chart-wrap"><canvas id="seasonChart${i}"></canvas></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                          <span class="text-xl font-black text-slate-800">${pDeckung}%</span>
                        </div>
                      </div>
                      <span class="text-[10px] font-black text-slate-800 tracking-widest uppercase mb-4">Deckung</span>

                      <div class="w-full text-[11px] text-slate-600 space-y-2 border-t border-slate-200 pt-4">
                        <div class="flex justify-between items-center"><span>Bedarf</span><span class="font-bold text-slate-800">${formatDE(total)} kWh</span></div>
                        <div class="flex justify-between items-center">
                          <span class="flex items-center gap-1.5">
                            <div class="flex space-x-[-4px]">
                              <div class="w-2.5 h-2.5 rounded-full border border-white" style="background:${donutFillColor}"></div>
                              <div class="w-2.5 h-2.5 rounded-full border border-white" style="background:${donutBatteryColor}"></div>
                            </div>
                            PV/Akku
                          </span>
                          <span class="font-bold" style="color:${donutFillColor}">${formatDE(season.DirektDeckung + season.BatterieDeckung)} kWh (${pDeckung}%)</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full" style="background:${donutEmptyColor}"></div>Zukauf</span>
                          <span class="font-bold text-slate-800">${formatDE(season.Netzbezug)} kWh (${pZukauf}%)</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 mt-1 border-t border-slate-100" style="color:${einspeisungColor}">
                          <span>Einspeisung</span><span class="font-bold">${formatDE(Math.abs(season.NetzeinspeisungNeg))} kWh (${pEinspeisung}%*)</span>
                        </div>
                      </div>
                    </div>
                  `;
                }).join('')}
              </div>

              <div class="text-[9px] text-center text-slate-400 font-medium mb-6">
                * Der Prozentwert der Einspeisung bezieht sich auf den gesamten Solarertrag der jeweiligen Jahreszeit.
              </div>

              <div class="flex justify-between items-end mb-3 border-b border-slate-200 pb-2">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Produktion vs. Verbrauch (Monatsvergleich)</h3>
                <div class="text-[10px] text-slate-500">Wenn die braune Säule höher ist, erwirtschaften Sie Überschüsse.</div>
              </div>

              <div class="h-[220px] w-full mb-4 mt-2">
                <div class="chart-wrap"><canvas id="monthlyCompareChart"></canvas></div>
              </div>
            </div>

            <!-- PAGE 6 -->
            <div class="a4-page flex flex-col bg-slate-50/30">
              ${ReportHeader('4. IHR PARTNER FÜR DIE ENERGIEWENDE')}
              <h2 class="text-xl font-black text-slate-900 mb-8">4. WERK STUDIO & IHRE TECHNOLOGIE-VORTEILE</h2>

              <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-200 pb-2">Darum WERK STUDIO – Ihr Partner für die Energiewende</h3>
              <div class="grid grid-cols-2 gap-4 mb-10">
                ${[
                  ['award','Meisterbetrieb SHK & Elektro','Höchste handwerkliche Präzision durch unsere gewerkeübergreifende Meisterkompetenz (Sanitär, Heizung, Klima & Elektrotechnik).'],
                  ['shieldCheck','Alles aus einer Hand','Ein einziger, verlässlicher Ansprechpartner für Beratung, Planung, Fördermittelservice, Installation und die spätere Wartung.'],
                  ['star','Premium Produktqualität','Wir verbauen ausschließlich marktführende, langlebige und erprobte Komponenten für Ihre maximale Betriebs- und Ausfallsicherheit.'],
                  ['wrench','Langjährige Erfahrung','Hunderte erfolgreich realisierte Projekte und tiefes technical Know-how in der Umsetzung komplexer Energie-Ökosysteme.']
                ].map(item => `
                  <div class="bg-white p-5 border border-slate-200 rounded-xl shadow-sm flex gap-4">
                    <span class="w-7 h-7 shrink-0 mt-1" style="color:${colorBrown}">${Icons[item[0]]()}</span>
                    <div>
                      <h4 class="font-bold text-slate-800 text-sm mb-1.5">${item[1]}</h4>
                      <p class="text-xs text-slate-600 leading-relaxed">${item[2]}</p>
                    </div>
                  </div>
                `).join('')}
              </div>

              <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-200 pb-2">Die Bausteine Ihres intelligenten Systems</h3>
              <div class="space-y-4 flex-1">
                ${[
                  ['thermoSnow','Wärmepumpe (Heizen & Kühlen)','Nutzt kostenlose Umweltenergie hochgradig effizient. Im Sommer kann das System optional über die Flächenheizung zur angenehmen Kühlung Ihres Gebäudes eingesetzt werden.'],
                  ['sun','Photovoltaik-Anlage','Macht Ihr Dach zum eigenen Kraftwerk und liefert sauberen, extrem günstigen Strom über Jahrzehnte hinweg. Ihr stärkster Schutz vor unkalkulierbaren Preissteigerungen.'],
                  ['battery','Batteriespeicher (Inkl. Ersatz- & Notstrom)','Speichert Sonnenstrom für die Nacht. Mit der integrierten Not-/Ersatzstromfunktion bleibt Ihr Haus auch bei einem kompletten Blackout oder Netzausfall sicher und autark versorgt.']
                ].map(item => `
                  <div class="bg-white p-4 border border-slate-200 rounded-xl shadow-sm flex gap-5 items-center">
                    <div class="p-3 rounded-lg" style="background:${colorBeige}20"><span class="w-6 h-6" style="color:${colorBrown}">${Icons[item[0]]()}</span></div>
                    <div><h4 class="font-bold text-slate-800 text-sm mb-1">${item[1]}</h4><p class="text-xs text-slate-600">${item[2]}</p></div>
                  </div>
                `).join('')}

                ${config.costWallbox > 0 ? `
                  <div class="bg-white p-4 border border-slate-200 rounded-xl shadow-sm flex gap-5 items-center">
                    <div class="p-3 rounded-lg" style="background:${colorBeige}20"><span class="w-6 h-6" style="color:${colorBrown}">${Icons.car()}</span></div>
                    <div>
                      <h4 class="font-bold text-slate-800 text-sm mb-1">E-Mobilität (Wallbox)</h4>
                      <p class="text-xs text-slate-600">Ihre private Tankstelle direkt vor der Tür. Tanken Sie Ihr E-Auto bequem mit dem eigenen, sauberen Überschussstrom vom Dach – wesentlich günstiger als an jeder Ladesäule.</p>
                    </div>
                  </div>
                ` : ''}

                <div class="bg-white p-4 border border-slate-200 rounded-xl shadow-sm flex gap-5 items-center">
                  <div class="p-3 rounded-lg" style="background:${colorBeige}20"><span class="w-6 h-6" style="color:${colorBrown}">${Icons.network()}</span></div>
                  <div>
                    <h4 class="font-bold text-slate-800 text-sm mb-1">Intelligente Sektorenkopplung</h4>
                    <p class="text-xs text-slate-600">Das unsichtbare Gehirn Ihrer Anlage: Strom, Wärme und Mobilität werden vollautomatisch so vernetzt, dass Ihr Eigenverbrauch maximiert und teurer Netzbezug auf ein Minimum reduziert wird.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- PAGE 7 -->
            <div class="a4-page flex flex-col bg-white">
              ${ReportHeader('5. PHOTOVOLTAIK & SPEICHER')}
              <h2 class="text-xl font-black text-slate-900 mb-8 uppercase tracking-widest">5. PHOTOVOLTAIK & BATTERIESPEICHER</h2>

              <div class="mb-8">
                <h3 class="text-2xl font-black mb-4 leading-tight" style="color:${colorBrown}">Für alle, die die Sonne optimal nutzen wollen.</h3>
                <p class="text-[13px] text-slate-600 leading-relaxed bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm">
                  <strong class="text-slate-800 block mb-1">Eine schlaue Entscheidung:</strong>
                  Solarstrom zum Eigenverbrauch ist die Energielösung der Zukunft. Mit einer Photovoltaikanlage nutzen Sie den erzeugten Strom tagsüber direkt und können so ohne besondere Zusatzmaßnahmen einen Eigenverbrauchsanteil von bis zu 30% erreichen. All unsere Systeme können auch zu einem späteren Zeitpunkt mit einem Batteriespeicher nachgerüstet werden.
                </p>
              </div>

              <div class="grid grid-cols-2 gap-6 mb-auto">
                <div class="rounded-xl p-6 shadow-sm border flex flex-col" style="background:${colorBeige}10;border-color:${colorBeige}50">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit bg-white border" style="border-color:${colorBeige}50">
                    <span class="w-7 h-7" style="color:${colorBrown}">${Icons.battery()}</span>
                  </div>
                  <h3 class="text-lg font-black text-slate-800 mb-2 leading-snug">Maximaler Nutzen durch Batteriespeicher und Energiemanagement</h3>
                  <p class="text-xs text-slate-600 leading-relaxed"><strong class="text-slate-800 block mb-1">Effizienter geht’s nicht:</strong> Verbindet man das Photovoltaiksystem zusätzlich mit einem Batteriespeicher, wird der Eigenverbrauch auf bis zu 80% gesteigert und die Unabhängigkeit vom Versorgungsnetz noch weiter erhöht.</p>
                  <p class="text-xs text-slate-600 leading-relaxed mt-2">Profitieren Sie von einem modernen, smarten Energiesystem, das Solarstrom-Produktion, Verbrauch und Speicherung intelligent vernetzt.</p>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit" style="background:${colorBeige}20">
                    <span class="w-7 h-7" style="color:${colorBrown}">${Icons.sun()}</span>
                  </div>
                  <h3 class="text-lg font-black text-slate-800 mb-2 leading-snug uppercase tracking-wide">Mehr Eigenverbrauch bringt mehr Unabhängigkeit.</h3>
                  <p class="text-xs text-slate-600 leading-relaxed"><strong class="text-slate-800 block mb-1">Selbst produziert für die eigene Steckdose:</strong> Der Eigenverbrauch gibt an, zu welchem Anteil Ihr Stromverbrauch durch selbst erzeugten Solarstrom gedeckt wird. Der Verbrauchsanteil ergibt sich aus der Anzahl der Elektrogeräte im Haushalt, der Personenzahl und den Nutzungsgewohnheiten.</p>
                  <p class="text-xs text-slate-600 leading-relaxed mt-2">Die Menge des erzeugten Solarstroms hat wiederum viele Faktoren: Größe bzw. Leistung der Solaranlage, Qualität der einzelnen Komponenten, fachgerechte Planung und Montage sowie Standort und Wetter.</p>
                </div>
              </div>

              <div class="mt-8">
                <div class="rounded-xl p-6 border flex gap-5 items-center relative overflow-hidden bg-slate-50 shadow-sm" style="border-color:${colorBrown}">
                  <div class="absolute top-0 left-0 w-32 h-32 rounded-full blur-[50px] opacity-20" style="background:${colorBrown}"></div>
                  <div class="relative z-10 w-full">
                    <h4 class="font-bold text-slate-800 mb-1">Ihr individuelles Dachpotenzial:</h4>
                    <p class="text-[13px] text-slate-700 leading-relaxed font-medium">
                      ${
                        derivedParams.isEastWestProfile
                          ? `<span>Da Sie eine <strong style="color:${colorBrown}">Ost-West-Belegung</strong> (oder ähnlich mehrflächig) gewählt haben, profitieren Sie von einem extrem breiten Erzeugungsprofil. Der Sonnenstrom liegt morgens früher und abends länger an. Dadurch sinkt der abendliche Bedarf an Batterieleistung und Ihr direkter Eigenverbrauch steigt massiv an. Ein sehr effizientes Setup!</span>`
                          : derivedParams.hasSued
                            ? `<span>Mit der starken Ausrichtung nach <strong style="color:${colorBrown}">Süden</strong> erzielen Sie den absoluten maximalen Stromertrag zur Mittagszeit. Um diesen gewaltigen Überschuss optimal zu nutzen, puffern wir diese Spitzenenergie im Batteriespeicher, sodass Ihr Haus die gesamte Nacht hindurch kostenfrei aus diesem Akku versorgt wird.</span>`
                            : `<span>Durch die optimale Belegung der verfügbaren Dachflächen holen wir das Beste aus der Sonne für Sie heraus. WERK STUDIO berechnet die genaue Modulverteilung, damit Ihre Batterie auch im Winter maximal beladen wird.</span>`
                      }
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- PAGE 8 -->
            <div class="a4-page flex flex-col bg-slate-50/30">
              ${ReportHeader('6. WÄRMEPUMPEN-TECHNOLOGIE')}
              <h2 class="text-xl font-black text-slate-900 mb-8 uppercase tracking-widest">6. DAS PERFEKTE TEAM: INTELLIGENTE AUTARKIE</h2>

              <div class="mb-6">
                <h3 class="text-2xl font-black mb-4 leading-tight" style="color:${colorBrown}">Ein perfektes Team: Wärmepumpen mit Solarstrom versorgen und Heizkosten minimieren. Und noch mehr sparen.</h3>
                <div class="text-[13px] text-slate-600 leading-relaxed space-y-4">
                  <p>Die optimale Nutzung der Sonnenenergie wird durch die Kombination aus Photovoltaik und Wärmepumpe realisiert. Wärmepumpen ermöglichen die Wärmegewinnung aus der Luft oder aus dem Boden, um damit ein Gebäude zu heizen oder das Trinkwasser zu erwärmen. Jede Wärmepumpe benötigt elektrischen Strom.</p>
                  <p>Wenn dieser Strom von der eigenen Photovoltaikanlage erzeugt und mit einem Batteriespeicher zusätzlich gespeichert wird, lässt sich ein Einfamilienhaus fast vollständig energieautark versorgen. Eine Wärmepumpe macht unabhängig von fossilen Brennstoffen und trägt aktiv zur Reduzierung des CO₂-Ausstoßes und zum Klimaschutz bei.</p>
                  <p class="font-bold p-4 rounded-xl mt-4" style="background:${colorBeige}10;color:${colorBrown}">Wir zeigen Ihnen, wie leicht man jetzt schon die fossilen Brennstoffe Öl, Gas und Kohle in den Schatten stellen und die Kraft der Sonne richtig nutzen kann. Einfach und effizient in Ihrem Zuhause: Wärmepumpensysteme aus dem Hause Werkstudio.</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-8">
                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit" style="background:${colorBeige}20">
                    <span class="w-7 h-7" style="color:${colorBrown}">${Icons.piggyBank()}</span>
                  </div>
                  <h3 class="text-lg font-black text-slate-800 mb-3 leading-snug">Wie Sie mit selbstproduziertem Solarstrom die Hälfte sparen können</h3>
                  <p class="text-xs text-slate-600 leading-relaxed">Eine Wärmepumpe produziert 80 % Ihrer Energie mit dem emissionsfreundlichsten Energiespender – unserer Sonne! Für den elektrischen Antrieb benötigt sie lediglich 20% Strom.</p>
                  <p class="text-xs text-slate-600 leading-relaxed mt-2">Ökologische und ökonomische Vorteile bietet die Kombination einer Photovoltaikanlage und die Nutzung des eigens produzierten Solarstroms. Sie sparen bares Geld, wenn Sie die Betriebskosten niedrig halten.</p>
                </div>

                <div class="rounded-xl p-6 shadow-sm border flex flex-col" style="background:${colorBeige}10;border-color:${colorBeige}50">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit bg-white border" style="border-color:${colorBeige}50">
                    <span class="w-7 h-7" style="color:${colorBrown}">${Icons.cpu()}</span>
                  </div>
                  <h3 class="text-lg font-black text-slate-800 mb-2 leading-snug">Wärmepumpen sind intelligente Heizsysteme</h3>
                  <div class="text-[10px] font-bold tracking-widest uppercase mb-3" style="color:${colorBrown}">DIESE SYSTEMEFFIZIENZ NENNEN WIR INTELLIGENTE AUTARKIE</div>
                  <p class="text-xs text-slate-600 leading-relaxed">Eine Wärmepumpe kann mehr als nur umweltfreundlich heizen und Warmwasser bereiten. Je nach Technologie und Ausführung sind es Systeme mit vielfältigen Möglichkeiten für Ihr Zuhause.</p>
                  <p class="text-xs text-slate-600 leading-relaxed mt-2">Machen Sie Ihr Objekt zukunftssicher mit smarten Geräten, die darüber hinaus lüften (zusätzliches System) und kühlen können. So sorgen Sie für ein gesundes Wohnklima.</p>
                </div>
              </div>

              <div class="mb-auto mt-6 bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col gap-4">
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
                  <span class="w-4 h-4" style="color:${colorBrown}">${Icons.thermoSnow()}</span>
                  Witterung & Saisonale Verteilung am Standort ${config.plz}
                </h3>

                <div class="grid grid-cols-2 gap-8">
                  <div class="space-y-4">
                    <div>
                      <h4 class="text-xs font-bold text-slate-800 mb-1">Normaußentemperatur (NAT: ${derivedParams.klima.nat} °C)</h4>
                      <p class="text-[10px] text-slate-600 leading-relaxed">Die NAT ist die tiefste Temperatur, die an Ihrem Wohnort an zwei aufeinanderfolgenden Tagen zu erwarten ist. Sie ist der wichtigste Wert für die Dimensionierung, damit Ihre Wärmepumpe das Haus auch am kältesten Tag des Jahres zuverlässig und ohne teuren Heizstab erwärmt.</p>
                    </div>
                    <div>
                      <h4 class="text-xs font-bold text-slate-800 mb-1">Heizgradtage (HGT: ${derivedParams.klima.hgt} Kd)</h4>
                      <p class="text-[10px] text-slate-600 leading-relaxed">Dieser Wert beschreibt, wie "streng" der Winter an Ihrem Standort ausfällt. Er wird genutzt, um den tatsächlichen Energieverbrauch (kWh) exakt auf Ihre regionale Witterung abzustimmen.</p>
                    </div>
                  </div>

                  <div class="flex flex-col justify-center space-y-5">
                    <div>
                      <div class="flex justify-between items-end mb-1.5">
                        <span class="text-xs font-bold text-slate-800">Verteilung Heizbedarf p.a. (${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus))} kWh)</span>
                      </div>
                      <div class="flex w-full h-4 rounded-full overflow-hidden shadow-inner mb-1.5">
                        <div class="flex items-center justify-center text-[8px] text-slate-700 font-bold bg-slate-300" style="width:45%">45%</div>
                        <div class="flex items-center justify-center text-[8px] text-slate-800 font-bold" style="width:22%;background:#c1b7a6">22%</div>
                        <div class="flex items-center justify-center text-[8px] text-white font-bold" style="width:8%;background:#8f8675">8%</div>
                        <div class="flex items-center justify-center text-[8px] text-white font-bold" style="width:25%;background:#64748b">25%</div>
                      </div>
                      <div class="flex justify-between text-[9px] text-slate-600 font-medium px-1">
                        <span class="w-[45%] text-center">Winter<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.45))} kWh</span></span>
                        <span class="w-[22%] text-center">Frühling<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.22))} kWh</span></span>
                        <span class="w-[8%] text-center">Sommer<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.08))} kWh</span></span>
                        <span class="w-[25%] text-center">Herbst<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(derivedParams.gesamtWaermeBedarfHaus * 0.25))} kWh</span></span>
                      </div>
                    </div>

                    <div>
                      <div class="flex justify-between items-end mb-1.5">
                        <span class="text-xs font-bold text-slate-800">Verteilung Solarertrag p.a. (${formatDE(kpis.totalPV)} kWh)</span>
                      </div>
                      <div class="flex w-full h-4 rounded-full overflow-hidden shadow-inner mb-1.5">
                        <div class="flex items-center justify-center text-[8px] text-slate-700 font-bold bg-slate-300" style="width:13%">13%</div>
                        <div class="flex items-center justify-center text-[8px] text-slate-800 font-bold" style="width:25%;background:#c1b7a6">25%</div>
                        <div class="flex items-center justify-center text-[8px] text-white font-bold" style="width:40%;background:#8f8675">40%</div>
                        <div class="flex items-center justify-center text-[8px] text-white font-bold" style="width:22%;background:#64748b">22%</div>
                      </div>
                      <div class="flex justify-between text-[9px] text-slate-600 font-medium px-1">
                        <span class="w-[13%] text-center">Winter<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(kpis.totalPV * 0.13))} kWh</span></span>
                        <span class="w-[25%] text-center">Frühling<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(kpis.totalPV * 0.25))} kWh</span></span>
                        <span class="w-[40%] text-center">Sommer<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(kpis.totalPV * 0.40))} kWh</span></span>
                        <span class="w-[22%] text-center">Herbst<br/><span class="text-[7.5px] opacity-70 block">${formatDE(Math.round(kpis.totalPV * 0.22))} kWh</span></span>
                      </div>
                    </div>

                    <p class="text-[9px] text-slate-500 italic mt-1 leading-tight">Gegenläufige Natur: Im Winter ist der Heizbedarf am höchsten, der Solarertrag am geringsten. Genau hier gleicht die intelligente Steuerung und Speichertechnik das Defizit optimal aus.</p>
                  </div>
                </div>
              </div>

              <div class="mt-auto pt-6">
                <div class="rounded-xl p-6 border flex gap-5 items-center relative overflow-hidden" style="background:${colorBrown};border-color:${colorBrown}">
                  <div class="absolute top-0 right-0 w-48 h-48 rounded-full blur-[50px] opacity-20" style="background:${colorBeige}"></div>
                  <div class="p-4 rounded-full shrink-0 relative z-10 shadow-sm" style="background:rgba(255,255,255,0.1)">
                    <span class="w-8 h-8" style="color:${colorBeige}">${Icons.award()}</span>
                  </div>
                  <div class="relative z-10">
                    <p class="text-xs text-slate-200 leading-relaxed">
                      <strong class="text-white text-sm block mb-1">Als Systemanbieter bieten wir Ihnen maßgeschneiderte Lösungen für Ihr Zuhause:</strong>
                      Bei WERK STUDIO erhalten Sie alle Leistungen aus einer Hand: Photovoltaik, Batteriespeichersysteme, Wärmepumpen und Wallboxen für E-Mobility, sowie die Installation und eine dach- und fachgerechte Montage aller Komponenten. Für heute, morgen und alles, was noch kommt.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- PAGE 9 -->
            <div class="a4-page flex flex-col bg-white">
              ${ReportHeader('7. INTELLIGENTE E-MOBILITÄT')}
              <h2 class="text-xl font-black text-slate-900 mb-8 uppercase tracking-widest">7. E-MOBILITÄT: ZAPFEN SIE DIE SONNE AN</h2>

              <div class="mb-8">
                <h3 class="text-4xl font-black mb-3 leading-tight uppercase" style="color:${colorBrown}">Die Zukunft fährt elektrisch</h3>
                <p class="text-[13px] text-slate-600 leading-relaxed bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm">
                  <strong class="text-slate-800 block mb-1">Wie Sie die Energie der Sonne sogar auf die Straße bringen?</strong>
                  Mit einer Ladestation für Elektroautos. Was vor kurzem noch Zukunftsmusik war, ist heute automobiler Alltag: Lassen Sie sich von uns eine Wallbox nach Ihren Ansprüchen installieren – idealerweise als „Zapfsäule“ für Ihren eigenen Solarstrom. Auch eine „All-in-one“-Lösung mit Solar-Carport stellen wir Ihnen betriebsbereit vor – oder besser gesagt: neben die Haustür.
                </p>
              </div>

              <div class="grid grid-cols-2 gap-6 mb-auto">
                <div class="rounded-xl p-6 shadow-sm border flex flex-col bg-slate-50">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit" style="background:${colorBeige}20"><span class="w-6 h-6" style="color:${colorBrown}">${Icons.leaf()}</span></div>
                  <h4 class="text-lg font-black text-slate-800 mb-2 leading-snug">Ökologisch & Wirtschaftlich</h4>
                  <p class="text-xs text-slate-600 leading-relaxed">Um den Weg in eine saubere und CO₂-neutrale Zukunft zu ebnen, heißt es Energie bewusst zu nutzen und auf Nachhaltigkeit zu setzen. An dieser Stelle wird das Energiemanagement eingesetzt.</p>
                  <p class="text-xs text-slate-600 leading-relaxed mt-2">Sie können den selbst erzeugten Solarstrom Ihrer Photovoltaikanlage auch zum Aufladen Ihres Elektroautos nutzen. Das E-Auto mit eigenem Strom zu laden ist nicht nur ökologisch, sondern auch wirtschaftlich sinnvoll.</p>
                </div>

                <div class="rounded-xl p-6 shadow-sm border flex flex-col" style="background:${colorBeige}10;border-color:${colorBeige}50">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit bg-white border" style="border-color:${colorBeige}50"><span class="w-6 h-6" style="color:${colorBrown}">${Icons.trendingUp()}</span></div>
                  <h4 class="text-lg font-black text-slate-800 mb-2 leading-snug">Rentabilität maximieren</h4>
                  <p class="text-xs text-slate-600 leading-relaxed">Mit Elektroautos erhöhen Sie die Rentabilität Ihrer Solaranlage und nutzen gleichzeitig den Solarstrom zu Ihrem Vorteil. Statt den Solarstrom zu einem unvorteilhaften Preis ins Stromnetz einzuspeisen, wird die Batterie des E-Autos kostengünstig über die Photovoltaikanlage aufgeladen.</p>
                  <div class="mt-3 p-3 rounded-lg text-[10px] font-bold" style="background:${colorBrown};color:white">Erst wenn der Akku vollgeladen ist, wird der zu viel produzierte Strom ins Netz eingespeist.</div>
                </div>
              </div>

              <div class="mt-8">
                <div class="rounded-xl p-6 border flex gap-5 items-center relative overflow-hidden bg-slate-50" style="border-color:${colorBrown}">
                  <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-[50px] opacity-20" style="background:${colorBrown}"></div>
                  <div class="p-3 rounded-full shrink-0 relative z-10" style="background:${colorBeige}20"><span class="w-8 h-8" style="color:${colorBrown}">${Icons.car()}</span></div>
                  <div class="relative z-10">
                    <p class="text-[13px] text-slate-700 leading-relaxed font-medium">
                      <strong style="color:${colorBrown}">Mit intelligenten Ladelösungen, durchdachter Vernetzung und effizienter Steuerung</strong> verbinden wir alle Aspekte für modernes Laden. Elektroauto, Solarcarport mit Ladestation und Batteriespeicher – eine perfekte Kombination, mit der Sie in Zukunft immer gut fahren.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- PAGE 10 -->
            <div class="a4-page flex flex-col bg-slate-50/30">
              ${ReportHeader('8. DAS GESAMTSYSTEM')}
              <h2 class="text-xl font-black text-slate-900 mb-6 uppercase tracking-widest">8. SEKTORENKOPPLUNG: EIGENE ENERGIE</h2>

              <div class="mb-8">
                <h3 class="text-4xl font-black mb-3 leading-tight" style="color:${colorBrown}">
                  Eigener Strom.<br/>Eigene Wärme.<br/>Eigene Energie.
                </h3>
                <p class="text-[13px] text-slate-600 leading-relaxed font-medium bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                  Ganz autark sein, frei von allen Energieanbietern und trotzdem keinen Verzicht auf Komfort üben: Kombinieren Sie die Systeme Photovoltaik, Batteriespeicher, Wärmepumpe, Lüftung und E-Mobilty von WERK STUDIO und verwirklichen so Ihr Energie-Effizienzhaus.
                </p>
              </div>

              <div class="grid grid-cols-2 gap-6 mb-auto">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit" style="background:${colorBeige}20"><span class="w-6 h-6" style="color:${colorBrown}">${Icons.network()}</span></div>
                  <h4 class="text-lg font-black text-slate-800 mb-2">Synergien nutzen & Kosten minimieren</h4>
                  <p class="text-xs text-slate-600 leading-relaxed">Diese ganzheitliche Lösung nutzt diese Synergien und steigert die Gesamtenergieeffizienz deutlich. Das bedeutet kosten- und umweltbewusster Energieverbrauch ohne CO₂-Ausstoß. Die Energiekosten Ihres Hauses können so dauerhaft auf ein Minimum reduziert werden. Das macht Sektorkopplung so erfolgreich.</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                  <div class="mb-4 p-3 rounded-lg inline-block w-fit" style="background:${colorBeige}20"><span class="w-6 h-6" style="color:${colorBrown}">${Icons.sun()}</span></div>
                  <h4 class="text-lg font-black text-slate-800 mb-2">Ein Geschenk des Himmels</h4>
                  <p class="text-xs text-slate-600 leading-relaxed">Aus der Verantwortung gegenüber der Umwelt und künftigen Generationen heraus, ist es das Ziel, den Energiebedarf ausschließlich aus erneuerbaren Energien zu gewinnen. Hierbei ist die Sonne buchstäblich ein Geschenk des Himmels, welches ein effizientes Gesamtsystem für das ganze Gebäude steuert.</p>
                </div>
              </div>

              <div class="mt-8">
                <div class="rounded-xl p-8 border shadow-sm relative overflow-hidden bg-white" style="border-color:${colorBeige}50">
                  <div class="absolute -right-10 -top-10 opacity-10"><span class="w-[250px] h-[250px] inline-block" style="color:${colorBrown}">${Icons.infinity()}</span></div>
                  <div class="relative z-10">
                    <h4 class="text-lg font-black mb-3" style="color:${colorBrown}">Sektorkopplung besteht aus einem gut funktionierenden Team:</h4>
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">Photovoltaik, Batteriespeicher, Wärmepumpe und E-Mobility. Hauptenergielieferant ist die Sonne bzw. die Solaranlage.</p>
                    <div class="inline-block px-4 py-2 rounded-lg font-bold text-white text-sm shadow-sm" style="background:${colorBrown}">
                      Sie deckt mit Hilfe des Batteriespeichers den gesamten Strom- und Heizungsbedarf bis 80% ab.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PAGE 11 -->
            <div class="a4-page flex flex-col bg-slate-50/30">
              ${ReportHeader('9. WIRTSCHAFTLICHKEIT')}
              <h2 class="text-xl font-black mb-8 text-slate-900 uppercase tracking-widest">9. INVESTITION & WIRTSCHAFTLICHKEIT (BREAK-EVEN)</h2>

              <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                  <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest">Ihre Netto-Investition im Detail</h3>
                </div>
                <div class="p-0 overflow-x-auto">
                  <table class="w-full text-xs text-left">
                    <thead class="bg-white text-slate-500 text-[10px] uppercase tracking-wider border-b border-slate-100">
                      <tr>
                        <th class="p-3 pl-5 font-semibold">Systemkomponente</th>
                        <th class="p-3 font-semibold text-right">Brutto</th>
                        <th class="p-3 font-semibold text-right text-green-600">KfW-Förderung</th>
                        <th class="p-3 font-semibold text-right text-teal-600">Zusätzl. Förderung</th>
                        <th class="p-3 font-semibold text-right" style="color:${colorBrown}">Kombi-Rabatt*</th>
                        <th class="p-3 pr-5 font-black text-right text-slate-800">Netto effektiv</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                      <tr class="hover:bg-slate-50">
                        <td class="p-3 pl-5 font-medium">Wärmepumpe</td>
                        <td class="p-3 text-right">${formatDE(config.costWP)} €</td>
                        <td class="p-3 text-right text-green-600">-${formatDE(finance.kfwZuschuss)} €</td>
                        <td class="p-3 text-right text-teal-600">-${formatDE(finance.extraGrantWPNum)} €${config.extraGrantSourceWP ? `<span class="block text-[8px] text-teal-600/70 mt-0.5">${config.extraGrantSourceWP}</span>` : ''}</td>
                        <td class="p-3 text-right" style="color:${colorBrown}">-${formatDE(finance.discountWPNum)} €</td>
                        <td class="p-3 pr-5 text-right font-bold text-slate-900">${formatDE(finance.nettoWP)} €</td>
                      </tr>
                      <tr class="hover:bg-slate-50">
                        <td class="p-3 pl-5 font-medium">Photovoltaik (${derivedParams.pvKwp} kWp)</td>
                        <td class="p-3 text-right">${formatDE(config.costPV)} €</td>
                        <td class="p-3 text-right text-green-600">-</td>
                        <td class="p-3 text-right text-teal-600">-${formatDE(finance.extraGrantPVNum)} €${config.extraGrantSourcePV ? `<span class="block text-[8px] text-teal-600/70 mt-0.5">${config.extraGrantSourcePV}</span>` : ''}</td>
                        <td class="p-3 text-right" style="color:${colorBrown}">-${formatDE(finance.discountPVNum)} €</td>
                        <td class="p-3 pr-5 text-right font-bold text-slate-900">${formatDE(finance.nettoPV)} €</td>
                      </tr>
                      <tr class="hover:bg-slate-50">
                        <td class="p-3 pl-5 font-medium">Speicher (${derivedParams.batteryCapacity} kWh)</td>
                        <td class="p-3 text-right">${formatDE(config.costBattery)} €</td>
                        <td class="p-3 text-right text-green-600">-</td>
                        <td class="p-3 text-right text-teal-600">-${formatDE(finance.extraGrantBatteryNum)} €${config.extraGrantSourceBattery ? `<span class="block text-[8px] text-teal-600/70 mt-0.5">${config.extraGrantSourceBattery}</span>` : ''}</td>
                        <td class="p-3 text-right" style="color:${colorBrown}">-${formatDE(finance.discountBatteryNum)} €</td>
                        <td class="p-3 pr-5 text-right font-bold text-slate-900">${formatDE(finance.nettoBattery)} €</td>
                      </tr>
                      ${config.costWallbox > 0 ? `
                        <tr class="hover:bg-slate-50">
                          <td class="p-3 pl-5 font-medium">Wallbox</td>
                          <td class="p-3 text-right">${formatDE(config.costWallbox)} €</td>
                          <td class="p-3 text-right text-green-600">-</td>
                          <td class="p-3 text-right text-teal-600">-${formatDE(finance.extraGrantWallboxNum)} €${config.extraGrantSourceWallbox ? `<span class="block text-[8px] text-teal-600/70 mt-0.5">${config.extraGrantSourceWallbox}</span>` : ''}</td>
                          <td class="p-3 text-right" style="color:${colorBrown}">-${formatDE(finance.discountWallboxNum)} €</td>
                          <td class="p-3 pr-5 text-right font-bold text-slate-900">${formatDE(finance.nettoWallbox)} €</td>
                        </tr>
                      ` : ''}
                    </tbody>
                    <tfoot class="bg-slate-50 font-black text-[13px] border-t-2 border-slate-200">
                      <tr>
                        <td class="p-4 pl-5">Gesamtinvestition</td>
                        <td class="p-4 text-right">${formatDE(finance.totalInvest)} €</td>
                        <td class="p-4 text-right text-green-600">-${formatDE(finance.kfwZuschuss)} €</td>
                        <td class="p-4 text-right text-teal-600">-${formatDE(finance.totalExtraGrant)} €</td>
                        <td class="p-4 text-right" style="color:${colorBrown}">-${formatDE(finance.totalDiscount)} €</td>
                        <td class="p-4 pr-5 text-right text-slate-900">${formatDE(finance.nettoInvest)} €</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>

              <div class="text-[10px] text-slate-500 mb-10 leading-relaxed">
                * <strong class="text-slate-700">Kombi-Bonus Bedingung:</strong> Die ausgewiesenen WERK STUDIO Rabatte werden ausschließlich bei der gleichzeitigen Beauftragung aller Sektoren (Wärmepumpe, Photovoltaik, Batteriespeicher und Wallbox) als Gesamtsystem gewährt. Bei Einzelbeauftragungen entfallen diese Sonderkonditionen.
                ${finance.isKombiBonusActive
                  ? `<span class="text-green-600 font-bold"> In Ihrer aktuellen Konfiguration ist der Kombi-Bonus aktiv!</span>`
                  : `<span class="text-red-500 font-bold"> In Ihrer aktuellen Konfiguration ist der Kombi-Bonus nicht aktiv (Einzelprojekt).</span>`
                }
              </div>

              <div class="flex-1 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-2">Ihr Finanzieller Break-Even (Kumulierte Gesamtkosten in €)</h3>
                <p class="text-[10px] text-slate-500 mb-6 leading-relaxed">Die farbige Linie zeigt Ihre Kosten im neuen System (startet hoch durch Investition, steigt danach kaum). Die graue Linie zeigt Ihr heutiges System, das durch Inflation explodiert. Der Schnittpunkt ist Ihr <strong>Break-Even-Point</strong> – ab hier erwirtschaftet die Anlage reinen Gewinn.</p>
                <div class="h-[220px] w-full">
                  <div class="chart-wrap"><canvas id="financeLineChart"></canvas></div>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-6 mt-6">
                <div class="text-center p-4 border border-slate-200 rounded-xl bg-white shadow-sm flex flex-col justify-center">
                  <div class="text-3xl font-black mb-1" style="color:${colorBrown}">${finance.amortisationYear ? `${finance.amortisationYear} Jahre` : '&gt; 30 Jahre'}</div>
                  <div class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">Amortisation</div>
                </div>
                <div class="text-center p-4 border border-slate-200 rounded-xl bg-white shadow-sm flex flex-col justify-center">
                  <div class="text-3xl font-black mb-1" style="color:${colorBrown}">${finance.roi}%</div>
                  <div class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">Rendite p.a.</div>
                </div>
                <div class="text-center p-4 border border-slate-200 rounded-xl bg-white shadow-sm relative overflow-hidden flex flex-col justify-center">
                  <div class="text-3xl font-black mb-1" style="color:${colorBrown}">${finance.lcoe} €</div>
                  <div class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">Solarstrom / kWh</div>
                  <div class="absolute top-0 right-0 text-[8px] font-bold px-2 py-1 rounded-bl-lg" style="background:${colorBeige}40;color:${colorBrown}">Basis: 30 Jahre</div>
                </div>
              </div>
            </div>
             <!-- PAGE 12 -->
            <div class="a4-page flex flex-col bg-white">
              ${ReportHeader('10. TRANSPARENZ: TECHNISCHE BERECHNUNGEN')}

              <h2 class="text-xl font-black text-slate-900 mb-6 uppercase tracking-widest">10. TRANSPARENZ: TECHNISCHE BERECHNUNGEN</h2>
              <p class="text-sm text-slate-600 mb-8 leading-relaxed">
                Vertrauen erfordert Nachvollziehbarkeit. Auf dieser Seite legen wir unsere Berechnungsgrundlagen, Formeln und regionalen Klimadaten für das Objekt in <strong>${config.plz}</strong> offen, die zu Ihrer Systemauslegung geführt haben.
              </p>

              <div class="space-y-6 flex-1">

                <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                  <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <span class="w-4 h-4" style="color:${colorBrown}">${Icons.thermometer()}</span>
                    1. Ermittlung des Systemverlusts (Altsystem)
                  </h3>
                  <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                    Jede Heizanlage verliert mit den Jahren an Effizienz (Abgasverluste, Strahlungsverluste). Dieser Verlust muss abgezogen werden, um den <strong>tatsächlichen thermischen Nutzenergiebedarf</strong> des Hauses zu ermitteln, den die neue Wärmepumpe erzeugen muss.
                  </p>
                  <div class="grid grid-cols-2 gap-4 text-xs text-slate-700">
                    <div class="flex justify-between border-b border-slate-200 pb-1"><span>Aktueller Heizungs-Typ:</span><strong>${config.heizungArt}</strong></div>
                    <div class="flex justify-between border-b border-slate-200 pb-1"><span>Alter der Heizung:</span><strong>${config.heizungAlter} Jahre</strong></div>
                    <div class="col-span-2 bg-white p-3 border border-slate-200 rounded mt-1">
                      <span class="block text-[10px] text-slate-400 mb-1 uppercase tracking-widest">Formel: Systemverlust durch Alterung</span>
                      <div class="font-mono text-[11px] font-bold text-slate-800">
                        Bisheriger Verbrauch (${config.heizVerbrauch} ${getHeizEinheit(config.heizungArt)}) × Angenommener Verlust (${derivedParams.systemVerlust * 100}%) = Tatsächlicher Wärmebedarf
                      </div>
                    </div>
                  </div>
                </div>

                <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                  <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <span class="w-4 h-4" style="color:${colorBrown}">${Icons.calculator()}</span>
                    2. Wärmebedarf & Heizlast (Splitting Heizung / WW)
                  </h3>
                  <div class="space-y-3 text-xs text-slate-700">
                    <div class="flex justify-between">
                      <span>Tatsächlicher Wärmebedarf gesamt (inkl. Warmwasser):</span>
                      <strong>${formatDE(Math.round(derivedParams.thermischHauptsystem))} kWh</strong>
                    </div>
                    <div class="flex justify-between text-slate-500">
                      <span class="flex items-center gap-1.5"><span class="w-3 h-3">${Icons.users()}</span> Davon Anteil Warmwasser (${config.personen} Personen á 800 kWh):</span>
                      <strong>- ${config.warmwasserArt === 'Zentral' ? config.personen * 800 : 0} kWh</strong>
                    </div>
                    ${config.zirkulation && config.warmwasserArt === 'Zentral' ? `
                      <div class="flex justify-between text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3">${Icons.droplet()}</span> Davon Zirkulationsverlust Warmwasser:</span>
                        <strong>- 600 kWh</strong>
                      </div>
                    ` : ''}
                    <div class="flex justify-between text-slate-500 border-b border-slate-200 pb-2">
                      <span>Verbleibender Bedarf reine Raumheizung:</span>
                      <strong>= ${formatDE(Math.round(derivedParams.heizWärmeBedarf))} kWh</strong>
                    </div>

                    <div class="bg-white p-3 border border-slate-200 rounded mt-2">
                      <span class="block text-[10px] text-slate-400 mb-1 uppercase tracking-widest">Dimensionierung der Wärmepumpe (Schweizer Formel für ${config.plz})</span>
                      <div class="font-mono text-[11px] mb-2">
                        ${Math.round(derivedParams.gesamtWaermeBedarfHaus)} kWh (Gesamt) / ${derivedParams.klima.vbh} Vollbenutzungsstunden =
                        <strong style="color:${colorBrown}">${derivedParams.berechneteHeizlast} kW Heizlast</strong>
                      </div>
                      <div class="text-[10px] text-slate-500 leading-tight">
                        Die Klimadaten basieren auf der Normaußentemperatur (NAT: ${derivedParams.klima.nat} °C) und den Heizgradtagen (HGT: ${derivedParams.klima.hgt} Kd) Ihres Standorts.
                      </div>
                    </div>
                  </div>
                </div>

                <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                  <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <span class="w-4 h-4" style="color:${colorBrown}">${Icons.thermoSnow()}</span>
                    3. Ermittlung der Jahresarbeitszahl (JAZ)
                  </h3>
                  <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                    Die Jahresarbeitszahl (JAZ) gibt an, wie viele Kilowattstunden (kWh) Wärme die Anlage aus einer kWh Strom erzeugt. Sie ist das Maß für die tatsächliche Effizienz im Jahr und wird aus den unterschiedlichen Vorlauftemperaturen für Raumheizung und Warmwasser gewichtet berechnet.
                  </p>
                  <div class="space-y-2 text-xs text-slate-700">
                    <div class="flex justify-between"><span>COP Raumheizung (System: ${config.heizSystem}):</span><strong>${derivedParams.copSH.toFixed(2)}</strong></div>
                    <div class="flex justify-between"><span>COP Warmwasserbereitung (Systembedingt):</span><strong>${derivedParams.copWW.toFixed(2)}</strong></div>
                    <div class="flex justify-between font-bold border-t border-slate-200 pt-2">
                      <span>Gewichtete Jahresarbeitszahl (JAZ):</span>
                      <span style="color:${colorBrown}">${config.customJAZ !== '' ? `${derivedParams.jaz} (Manuell)` : derivedParams.jaz}</span>
                    </div>
                    <div class="text-[10px] text-slate-500 mt-1 italic">
                      * Je höher der Warmwasser-Anteil am Gesamtwärmebedarf, desto stärker zieht der niedrigere Warmwasser-COP die Gesamt-JAZ nach unten.
                    </div>
                  </div>
                </div>

                <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                  <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <span class="w-4 h-4" style="color:${colorBrown}">${Icons.sun()}</span>
                    4. Berechnung der PV- und Speichergröße
                  </h3>
                  <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-xs text-slate-700">
                    <div class="flex justify-between border-b border-slate-200 pb-1"><span>Summe Strombedarf (Sektoren):</span><strong>${formatDE(derivedParams.gesamtStrombedarf)} kWh</strong></div>
                    <div class="flex justify-between border-b border-slate-200 pb-1"><span>Ertrag Basisfaktor (PLZ ${config.plz}):</span><strong>${getRegionalFactors(config.plz).pvBaseFactor} kWh/kWp</strong></div>
                    <div class="flex justify-between border-b border-slate-200 pb-1 col-span-2">
                      <span>Dachausrichtungen & Modul-Verteilung:</span>
                      <div class="text-right text-[10px]">
                        ${derivedParams.distributedDachseiten.map(d => `
                          <div key="${d.id}" class="font-bold">${d.ausrichtung} (${d.neigung}°, ${d.eindeckung}): <span style="color:${colorBrown}">${d.calculatedKwp} kWp</span></div>
                        `).join('')}
                      </div>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 pb-1"><span>Gewichteter Dachfaktor:</span><strong>${(derivedParams.avgYieldFactor * 100).toFixed(0)} %</strong></div>
                    <div class="flex justify-between border-b border-slate-200 pb-1"><span>Effektiver Ertrag Objekt:</span><strong>${Math.round(derivedParams.effectiveYieldPvKwp)} kWh/kWp</strong></div>
                    <div class="flex justify-between border-b border-slate-200 pb-1 font-bold text-slate-900 col-span-2"><span>Installierte Gesamt-PV Leistung:</span><span style="color:${colorBrown}">${derivedParams.pvKwp} kWp</span></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PAGE 13 -->
            <div class="a4-page flex flex-col bg-white">
              ${ReportHeader('11. TRANSPARENZ: KENNZAHLEN & EFFIZIENZ')}

              <h2 class="text-xl font-black text-slate-900 mb-6 uppercase tracking-widest">11. TRANSPARENZ: KENNZAHLEN & EFFIZIENZ</h2>

              <div class="space-y-6 flex-1">
                <div class="grid grid-cols-2 gap-5">
                  <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                      <span class="w-4 h-4" style="color:${colorBrown}">${Icons.activity()}</span>
                      5. Eigenverbrauchsquote
                    </h3>
                    <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                      Wie viel Prozent Ihres <strong>selbst produzierten Sonnenstroms</strong> nutzen Sie im Haus, anstatt ihn für wenige Cent einzuspeisen?
                    </p>
                    <div class="bg-white p-3 border border-slate-200 rounded">
                      <span class="block text-[10px] text-slate-400 mb-1 uppercase tracking-widest">Formel</span>
                      <div class="font-mono text-xs text-slate-800 font-bold mb-2">
                        (Direktnutzung + Batterieladung) / PV-Gesamtertrag × 100
                      </div>
                      <div class="flex justify-between text-xs text-slate-600 font-medium">
                        <span>Ergebnis:</span>
                        <span style="color:${colorBrown}">${kpis.eigenverbrauchQuote} %</span>
                      </div>
                    </div>
                  </div>

                  <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                      <span class="w-4 h-4" style="color:${colorBrown}">${Icons.shieldCheck()}</span>
                      6. Autarkiegrad
                    </h3>
                    <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                      Wie viel Prozent Ihres <strong>gesamten Strombedarfs</strong> (Haus, WP, Auto) decken Sie durch die eigene PV-Anlage ab?
                    </p>
                    <div class="bg-white p-3 border border-slate-200 rounded">
                      <span class="block text-[10px] text-slate-400 mb-1 uppercase tracking-widest">Formel</span>
                      <div class="font-mono text-xs text-slate-800 font-bold mb-2">
                        (Direktnutzung + Batterieentladung) / Gesamtstrombedarf × 100
                      </div>
                      <div class="flex justify-between text-xs text-slate-600 font-medium">
                        <span>Ergebnis:</span>
                        <span style="color:${colorBrown}">${kpis.autarkie} %</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                  <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3">7. Saisonale Autarkie</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                      Ein Jahresdurchschnittswert ist trügerisch, da im Sommer immense Überschüsse erzeugt werden, während der Heizbedarf im Winter Spitzen erreicht.
                      Die auf Seite 5 dargestellte saisonale Autarkie berechnet diese Werte isoliert pro Quartal (z.B. Winter-Autarkie = PV-Deckung im Winter / Bedarf im Winter). Sie verdeutlicht, wie stark der Speicher Sie auch in den dunkleren Monaten unterstützt.
                    </p>
                  </div>

                  <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3">8. Finanzielle Unabhängigkeit</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">
                      Zeigt Ihre Ersparnis im Verhältnis zu Ihren aktuellen Gesamtkosten.
                    </p>
                    <div class="bg-white p-3 border border-slate-200 rounded">
                      <span class="block text-[10px] text-slate-400 mb-1 uppercase tracking-widest">Formel</span>
                      <div class="font-mono text-[10px] text-slate-800 font-bold mb-2">
                        (Ersparnis Jahr 1 / Alte Energiekosten Jahr 1) × 100
                      </div>
                      <div class="flex justify-between text-xs text-slate-600 font-medium">
                        <span>Ergebnis:</span>
                        <span style="color:${colorBrown}">${finance.finUnabhProzent} %</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                  <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                      <span class="w-4 h-4" style="color:${colorBrown}">${Icons.zap()}</span>
                      9. §14a EnWG (Netzentgelte)
                    </h3>
                    <div class="space-y-3 text-xs text-slate-700">
                      <div class="flex justify-between">
                        <span>Angesetztes Netzentgelt (AP):</span>
                        <strong>${config.netzentgelt.toLocaleString('de-DE')} €/kWh</strong>
                      </div>
                      <div class="flex justify-between text-slate-500 border-b border-slate-200 pb-2">
                        <span>Steuerbare Einheit (SteuVE):</span>
                        <strong>${finance.evKostenOhne > 0 || finance.wpKostenOhne > 0 ? 'Ja (WP/Wallbox)' : 'Keine'}</strong>
                      </div>
                      <div class="bg-white p-3 border border-slate-200 rounded mt-2 space-y-2">
                        <span class="block text-[10px] text-slate-400 mb-1 uppercase tracking-widest">Modul 1 vs Modul 2 Vergleich</span>
                        <div class="flex justify-between">
                          <span>Modul 1 (Pauschale):</span>
                          <strong>160 €/a</strong>
                        </div>
                        <div class="flex justify-between">
                          <span>Modul 2 (60% auf Netzbezug):</span>
                          <strong>${Math.round((finance.wpNetz + finance.evNetz) * config.netzentgelt * 0.6).toLocaleString('de-DE')} €/a</strong>
                        </div>
                        <div class="flex justify-between font-bold border-t border-slate-100 pt-2" style="color:${colorBrown}">
                          <span>Angewandter Rabatt (Best-of):</span>
                          <span>${finance.ersparnis14a.toLocaleString('de-DE')} €/a</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="p-5 border border-slate-200 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                      <span class="w-4 h-4" style="color:${colorBrown}">${Icons.euro()}</span>
                      10. KfW-Fördermittelaufbau
                    </h3>
                    <div class="space-y-2 text-xs text-slate-700">
                      <div class="flex justify-between"><span>Basisförderung (Wärmepumpe):</span><strong>30 %</strong></div>
                      <div class="flex justify-between"><span>Effizienzbonus (Natürliches Kältemittel):</span><strong>+ 5 %</strong></div>
                      <div class="flex justify-between"><span>Klimageschwindigkeitsbonus (Ersatz ${config.heizungArt}):</span><strong>${finance.kfwDetails.klimaBonus > 0 ? '+ 20 %' : '+ 0 %'}</strong></div>
                      ${config.weUnter40k > 0 ? `<div class="flex justify-between text-green-600 font-medium"><span>Einkommensbonus (Haushalt &lt; 40k €):</span><strong>+ 30 %</strong></div>` : ''}

                      <div class="flex justify-between font-bold border-t border-slate-200 pt-2">
                        <span>Gesamter Fördersatz (Gedeckelt auf 70%):</span>
                        <span style="color:${colorBrown}">${finance.maxZuschussProzent} %</span>
                      </div>
                      <div class="text-[10px] text-slate-500 mt-1 leading-relaxed">
                        Der prozentuale Fördersatz wird auf die maximal förderfähigen Kosten von ${finance.weDeckelung.toLocaleString('de-DE')} € (bei ${config.wohneinheiten} WE) angewendet.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PAGE 14 -->
            <div class="a4-page flex flex-col bg-white">
              ${ReportHeader('12. IHR ABSCHLUSS')}

              <h2 class="text-xl font-black text-slate-900 mb-8 uppercase tracking-widest">12. KLIMASCHUTZ & WIE ES WEITERGEHT</h2>

              <div class="rounded-xl p-8 border flex items-center gap-8 mb-10" style="background:${colorBeige}10;border-color:${colorBeige}50">
                <span class="w-12 h-12 shrink-0" style="color:${colorBrown}">${Icons.leaf()}</span>
                <div class="flex-1">
                  <h3 class="text-lg font-bold mb-2" style="color:${colorBrown}">Ihr aktiver Klimaschutz</h3>
                  <p class="text-sm leading-relaxed mb-6" style="color:${colorBrown}">
                    Neben der massiven finanziellen Ersparnis tun Sie etwas Bedeutendes für die nächste Generation. Ihre jährliche Einsparung an CO₂-Emissionen entspricht der Speicherkraft von ca. <strong class="text-slate-900">${computed.co2.trees} Bäumen</strong> oder einer Mischwaldfläche von unglaublichen <strong class="text-slate-900">${computed.co2.forestArea.toLocaleString('de-DE')} m²</strong>.
                  </p>
                  <div class="grid grid-cols-3 gap-6 text-center">
                    <div class="bg-white p-4 rounded-lg border shadow-sm" style="border-color:${colorBeige}50"><span class="text-[10px] font-bold uppercase block mb-1" style="color:${colorBrown}">Pro Jahr</span><span class="text-2xl font-black" style="color:${colorBrown}">${computed.co2.year} t</span></div>
                    <div class="bg-white p-4 rounded-lg border shadow-sm" style="border-color:${colorBeige}50"><span class="text-[10px] font-bold uppercase block mb-1" style="color:${colorBrown}">10 Jahre</span><span class="text-2xl font-black" style="color:${colorBrown}">${computed.co2.tenYears} t</span></div>
                    <div class="bg-white p-4 rounded-lg border shadow-sm" style="border-color:${colorBeige}50"><span class="text-[10px] font-bold uppercase block mb-1" style="color:${colorBrown}">20 Jahre</span><span class="text-2xl font-black" style="color:${colorBrown}">${computed.co2.twentyYears} t</span></div>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-bold text-slate-800 mb-5 border-b border-slate-200 pb-2">Wie es jetzt für Sie weitergeht</h3>
              <div class="grid grid-cols-3 gap-6 mb-8">
                <div class="flex flex-col items-center text-center">
                  <div class="w-12 h-12 bg-slate-900 text-white rounded-full flex items-center justify-center font-black text-xl mb-4 shadow-lg shadow-slate-900/20">1</div>
                  <h4 class="font-bold text-slate-800 text-sm mb-2">Vor-Ort-Analyse</h4>
                  <p class="text-xs text-slate-600 leading-relaxed">Wir prüfen die baulichen Gegebenheiten bei Ihnen vor Ort und erstellen das finale, verbindliche Festpreisangebot.</p>
                </div>
                <div class="flex flex-col items-center text-center relative">
                  <div class="hidden md:block absolute top-6 -left-1/2 w-full h-0.5 bg-slate-200 -z-10"></div>
                  <div class="w-12 h-12 bg-slate-900 text-white rounded-full flex items-center justify-center font-black text-xl mb-4 shadow-lg shadow-slate-900/20">2</div>
                  <h4 class="font-bold text-slate-800 text-sm mb-2">Fördermittelservice</h4>
                  <p class="text-xs text-slate-600 leading-relaxed">Lehnen Sie sich zurück. Wir übernehmen die Beantragung aller KfW-Zuschüsse, um Ihre Förderung maximal auszuschöpfen.</p>
                </div>
                <div class="flex flex-col items-center text-center relative">
                  <div class="hidden md:block absolute top-6 -left-1/2 w-full h-0.5 bg-slate-200 -z-10"></div>
                  <div class="w-12 h-12 text-white rounded-full flex items-center justify-center font-black text-xl mb-4 shadow-lg" style="background:${colorBrown};box-shadow:0 10px 15px -3px ${colorBrown}40"><span class="w-6 h-6">${Icons.checkCircle()}</span></div>
                  <h4 class="font-bold text-slate-800 text-sm mb-2">Fachgerechte Installation</h4>
                  <p class="text-xs text-slate-600 leading-relaxed">Unsere Meister montieren Ihr System schlüsselfertig. Nach der gemeinsamen Inbetriebnahme produzieren Sie sofort eigenen Strom.</p>
                </div>
              </div>

              <div class="mb-4">
                <h3 class="text-xl font-black text-slate-900 uppercase leading-tight">Von der Sonne bekommen Sie die Energie.</h3>
                <h3 class="text-xl font-black mb-2 uppercase leading-tight" style="color:${colorBrown}">Alles andere von uns.</h3>
                <h4 class="text-[11px] font-bold text-slate-800 mb-2 tracking-wide uppercase">Solar + Wärmepumpe + Wallbox - Als abgestimmtes Gesamtsystem, geplant und umgesetzt aus einer Hand.</h4>
                <p class="text-[11px] text-slate-600 leading-relaxed max-w-4xl">
                  Die Kombination aus Photovoltaik, Wärmepumpe und Ladestation macht aus einzelnen Lösungen ein stimmiges Gesamtsystem. Ihr Eigenstrom kann Ihren Bedarf sinnvoll abdecken und Sie gewinnen mehr Kontrolle über Ihre Energieversorgung. Wir setzen das System fachgerecht um und begleiten Sie vom ersten Schritt bis zur Inbetriebnahme.
                </p>
              </div>

              <div class="border-2 bg-slate-50 shadow-sm p-6 rounded-2xl flex justify-between items-stretch mb-auto relative overflow-hidden mt-2" style="border-color:${colorBrown}">
                <div class="absolute top-0 right-0 w-64 h-64 rounded-full blur-[60px] opacity-20" style="background:${colorBrown}"></div>

                <div class="relative z-10 flex-1 pr-6 border-r border-slate-200 flex flex-col justify-center">
                  <div class="text-[10px] font-bold tracking-widest uppercase mb-3 inline-block px-3 py-1 rounded-full border w-fit" style="color:${colorBrown};background:${colorBeige}20;border-color:${colorBeige}">IHR FINANZIERUNGSVORTEIL</div>
                  <div>
                    <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">Ermittelte Gesamtförderung</span>
                    <div class="text-[9px] text-slate-400 font-medium mb-1 border-b border-slate-200 pb-1 inline-block">
                      Formel: ${finance.kfwZuschuss.toLocaleString('de-DE')} € (KfW WP) + ${finance.totalExtraGrant.toLocaleString('de-DE')} € (Kommunale/Zusatz-Förderungen) =
                    </div>
                    <div class="text-2xl font-black text-green-600">${finance.totalFoerderung.toLocaleString('de-DE')} €</div>
                  </div>
                </div>

                <div class="relative z-10 flex-1 px-6 flex flex-col justify-center items-center text-center">
                  <div class="text-[8px] font-bold tracking-widest uppercase mb-1 text-slate-500">Als Dankeschön für Ihre Entscheidung für das Gesamtsystem</div>
                  <h4 class="text-lg font-black uppercase text-slate-900 mb-0.5">Kombi-Bonus</h4>
                  <div class="text-[10px] font-bold text-slate-600 uppercase mb-1">Im Wert von</div>
                  <div class="text-3xl font-black mb-1" style="color:${colorBrown}">${finance.totalDiscount > 0 ? finance.totalDiscount.toLocaleString('de-DE') : '2.500'} €</div>
                  <div class="text-[8px] font-bold text-slate-500 uppercase px-2 text-center">
                    Bei gleichzeitiger Beauftragung von<br/>Wärmepumpe • Photovoltaik • Wallbox
                  </div>
                </div>

                <div class="text-right relative z-10 pl-6 border-l border-slate-200 shrink-0 flex flex-col items-end justify-center">
                  <span class="w-9 h-9 inline-block mb-2" style="color:${colorBrown}">${Icons.award()}</span>
                  <div class="text-xs font-black text-slate-800 leading-tight uppercase tracking-wider mb-3">Ihr starker Partner<br/><span style="color:${colorBrown}">WERK STUDIO</span></div>
                  <div class="text-[9px] text-slate-500 space-y-1">
                    <p class="font-bold text-slate-700">Ansprechpartner: <span class="font-medium text-slate-600">Projektleitung</span></p>
                    <p>Tel.: +49 (0) 123 456 789</p>
                    <p>E-Mail: info@werkstudio.de</p>
                    <p>Web: www.werkstudio.de</p>
                    <p class="pt-1 mt-1 border-t border-slate-200/50">Musterstraße 1<br/>12345 Musterstadt</p>
                  </div>
                </div>
              </div>

              <div class="text-[8px] text-slate-400 leading-relaxed text-justify pt-3 border-t border-slate-200">
                <strong>KOMBI-BONUS:</strong> ${finance.totalDiscount > 0 ? finance.totalDiscount.toLocaleString('de-DE') : '2.500'} € brutto Preisvorteil bei gleichzeitiger Beauftragung von Photovoltaik + Wärmepumpe + Ladestation im Rahmen eines Auftrags. Der Kombi-Bonus wird als Nachlass auf der Solar-Schlussrechnung verrechnet (keine Barauszahlung, keine Auszahlung in Teilbeträgen, kein Umtausch). Einmal pro Objekt möglich. Nicht mit anderen Aktionen, Nachlässen oder Rabatten kombinierbar. Gültig bei Auftragserteilung innerhalb der im Angebot genannten Frist; vorbehaltlich technischer Umsetzbarkeit, behördlicher/technischer Freigaben und Verfügbarkeit. Änderungen und Irrtümer vorbehalten.
                <br/><br/>
                Die beigefügte Wirtschaftlichkeitsberechnung ist eine unverbindliche Modellrechnung zur Orientierung. Sie berücksichtigt je nach Angebotsumfang Photovoltaik, Stromspeicher, Wärmepumpe und Ladestation sowie den Vergleich zu einer Versorgung mit Öl bzw. Gas. Grundlage sind die technischen Daten der geplanten Anlage, standortbezogene Strahlungsdaten, Ihr erwartetes Verbrauchs- und Nutzungsverhalten sowie die zum Zeitpunkt der Angebotserstellung angesetzten Energiepreise inkl. typischer Abgaben. Die tatsächlichen Werte können insbesondere durch Wetter, Anlagenbetrieb sowie durch künftige Änderungen von Energiepreisen abweichen.
                <br/><br/>
                <strong>Urheberrechtlicher Hinweis:</strong> Dieses Konzept ist geistiges Eigentum von WERK STUDIO BAUKONZEPT. Eine Weitergabe, Vervielfältigung oder Nutzung durch Dritte ist ohne ausdrückliche schriftliche Zustimmung nicht gestattet.
              </div>
            </div>

          </div>
        </div>
      `;
    }
    // =========================================================
    // CHARTS
    // =========================================================
    function makeDoughnutChart(id, values, colors, cutout = '68%') {
      destroyChart(id);
      const el = document.getElementById(id);
      if (!el) return;

      charts[id] = new Chart(el, {
        type: 'doughnut',
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
            tooltip: { enabled: false }
          }
        }
      });
    }

    function makeBarChart(id, labels, data1, data2) {
      destroyChart(id);
      const el = document.getElementById(id);
      if (!el) return;

      charts[id] = new Chart(el, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'PV-Produktion (kWh)',
              data: data1,
              backgroundColor: donutFillColor,
              borderRadius: 2
            },
            {
              label: 'Gesamtbedarf (kWh)',
              data: data2,
              backgroundColor: donutEmptyColor,
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
              ticks: { color: '#94a3b8', font: { size: 10 } },
              border: { display: false }
            },
            y: {
              grid: { color: '#f1f5f9' },
              ticks: { color: '#94a3b8', font: { size: 10 } },
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
          ctx.strokeStyle = colorBrown;
          ctx.lineWidth = 2;
          ctx.setLineDash([4, 4]);
          ctx.beginPath();
          ctx.moveTo(x, yScale.top);
          ctx.lineTo(x, yScale.bottom);
          ctx.stroke();
          ctx.setLineDash([]);
          ctx.fillStyle = colorBrown;
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
              borderColor: colorBrown,
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

      makeDoughnutChart('donutAutarkie', [kpis.totalDirekt, kpis.totalBatterie, kpis.totalNetzbezug], [donutFillColor, donutBatteryColor, donutEmptyColor]);
      makeDoughnutChart('donutEigenverbrauch', [kpis.totalDirekt, kpis.totalBatterie, kpis.totalNetzeinspeisung], [donutFillColor, donutBatteryColor, donutEmptyColor]);
      makeDoughnutChart('donutFinanz', [finance.finUnabhProzent, Math.max(0, 100 - finance.finUnabhProzent)], [donutFillColor, donutEmptyColor]);

      makeDoughnutChart(
        'bedarfsmixChart',
        bedarfsMix.map(i => i.value),
        bedarfsMix.map(i => i.fill),
        '62%'
      );

      seasonalData.forEach((season, i) => {
        const pieData = [
          season.DirektDeckung,
          season.BatterieDeckung,
          season.Netzbezug
        ].filter(v => v > 0);

        const pieColors = [
          donutFillColor,
          donutBatteryColor,
          donutEmptyColor
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

    // =========================================================
    // ROOT RENDER OVERRIDE
    // =========================================================
    function renderApp() {
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