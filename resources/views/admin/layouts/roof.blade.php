<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SOLAR WIZARD (Smooth HTML)</title>

  <script type="importmap">
  {
    "imports": {
      "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
      "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
    }
  }
  </script>

  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    html, body { height: 100%; }
    .no-print { display: block; }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .page-break { page-break-after: always; }
    }
    .a4-page {
      width: 210mm; height: 297mm;
      background: white; padding: 20mm;
      margin-bottom: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
      position: relative; overflow: hidden;
    }
    /* smoother scroll + less jank */
    .custom-scrollbar { scrollbar-gutter: stable; overscroll-behavior: contain; }
    .custom-scrollbar::-webkit-scrollbar { width: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(100,116,139,.35); border-radius: 999px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

    /* prevent browser gestures from fighting 3D */
    #threeCanvas { touch-action: none; }
  </style>
</head>

<body class="h-screen w-screen overflow-hidden bg-[#F0F4F8] text-slate-800 font-sans">
  <div id="app" class="h-full w-full"></div>

  <script src="https://unpkg.com/lucide@latest"></script>

 <script type="module">
  import * as THREE from "three";
  import { OrbitControls } from "three/addons/controls/OrbitControls.js";

  // =========================================================
  // 1) DATA (DB-like constants)
  // =========================================================
  const MODULE_TYPES = [
    { watts: 440, name: "Trina Solar Vertex S+ 440W", voc: 44.0, isc: 10.5, eff: 22.5, price: 110, area: 1.134 * 1.8 },
    { watts: 450, name: "Ja Solar JAM54S30 450W", voc: 45.0, isc: 10.8, eff: 22.8, price: 115, area: 1.134 * 1.8 },
    { watts: 490, name: "LONGi Hi-MO 6 Explorer 490W", voc: 39.2, isc: 13.98, eff: 22.5, price: 145, area: 1.134 * 1.8 },
  ];

  // module physical dims (meters)
  const MODULE_DIMS = { width: 1.134, height: 1.8, depth: 0.03, tempCoeff: -0.25 };

  const INVERTERS = [
    { model: "Fox ESS T3.0 G3 (Smart)", power: 3000, price: 900, type: "smart", mppt: 2, inputsPerMppt: 1, maxInputV: 1100, startV: 140 },
    { model: "Fox ESS T5.0 G3 (Smart)", power: 5000, price: 1100, type: "smart", mppt: 2, inputsPerMppt: 1, maxInputV: 1100, startV: 140 },
    { model: "Fox ESS T8.0 G3 (Smart)", power: 8000, price: 1350, type: "smart", mppt: 2, inputsPerMppt: 1, maxInputV: 1100, startV: 140 },
    { model: "Fox ESS T10.0 G3 (Smart)", power: 10000, price: 1500, type: "smart", mppt: 2, inputsPerMppt: 1, maxInputV: 1100, startV: 140 },
    { model: "Fox ESS T15.0 G3 (Smart)", power: 15000, price: 1900, type: "smart", mppt: 2, inputsPerMppt: 1, maxInputV: 1100, startV: 140 },
  ];

  // ───────────────────────────────────────────────────────────────────────────────────────────
  // 🔴 F-051 GESPERRT — DIESE ELF ZEITWERTE HABEN KEINE HERKUNFT. NICHT VERWENDEN.
  //
  // Sie sind PLATZHALTER. Der Kommentar direkt darunter sagt es selbst („adjust to your company
  // values") — er wurde an VIER Fundorte mitkopiert und an keinem eingeloest:
  //   1  docs/planner/pv-belegung-referenz/DachplanerProPage.tsx   Prototyp
  //   2  M-02 (Archivbestand)                                     Prototyp
  //   3  M-02-Kopie                                               byte-identisch
  //   4  diese Datei                                              Produktivbaum
  // Vier Fundorte, NULL unabhaengige Herkunftsangaben. Mehrfachvorkommen ist kein Beleg (H-8).
  //
  // WAS JE WERT FEHLT: Quelle · Datum der Erhebung · Gewerk. Ohne diese drei ist jede Zahl hier
  // eine Vermutung, die wie eine Kalkulation aussieht.
  //
  // NICHT AENDERN, bevor Yama die Firmenwerte genannt hat. Eine falsche Zahl durch eine andere
  // falsche zu ersetzen ist keine Korrektur — seine Auflage, woertlich.
  //
  // ZUR REICHWEITE DIESER DATEI, gemessen am 12.08. (A-16-1), damit sie niemand ueberschaetzt:
  //   statisch    KEIN Aufrufer — 'admin.layouts.roof' 0 Treffer in app/, routes/, resources/views/
  //   dynamisch   EINE Stelle im Haus ruft view() mit einer Variablen auf, ProductController.php:443.
  //               Ihr $view wird zwei Zeilen davor auf GENAU ZWEI feste Namen gesetzt
  //               ('…partials.product_cards', '…partials.product_list') — keiner davon dieser hier.
  //   Ergebnis    kein Aufrufer, statisch UND dynamisch geprueft.
  // Die Datei liegt im Produktivbaum und wird trotzdem nicht ausgeliefert: der ORT ist kein Beleg
  // fuer die Wirkung (H-8b).
  //
  // Auftrag und Begruendung: docs/auftraege/aktiv/A-16-time-vars-im-produktivcode.md
  // ───────────────────────────────────────────────────────────────────────────────────────────
  // time assumptions (minutes) – adjust to your company values
  const TIME_VARS = {
    SCAFFOLD_M2: 8,        // minutes per m2 facade area
    HOOK_STD: 6,
    HOOK_GRIND: 5,
    RAIL_M: 4,             // minutes per rail meter
    MOD_MOUNT: 12,         // minutes per module mount
    CABLE_M: 2,            // minutes per cable meter
    INV_SETUP: 90,
    CLEANUP: 60,
    MEASURE: 45,
    DC_BOX: 25,            // optional
    AC_ROUTE: 40,          // optional
  };

  const FASTENER_MAPPING = {
    ziegel:      { name: "K2 SingleHook 3S", type: "hook", grind: true,  desc: "3-fach verstellbar, Edelstahl", timePerUnit: TIME_VARS.HOOK_STD + TIME_VARS.HOOK_GRIND, price: 8.5 },
    schiefer:    { name: "K2 Schieferhaken", type: "hook", grind: false, desc: "Flachstahlhaken für Schieferdeckung", timePerUnit: TIME_VARS.HOOK_STD, price: 9.2 },
    trapezblech: { name: "K2 MultiRail 25",  type: "short_rail", grind: false, desc: "Kurzschiene direkt in Hochsicke", timePerUnit: 3, price: 4.5 },
    bitumen:     { name: "K2 D-Dome 6",      type: "flat_system", grind: false, desc: "Aufständerungssystem 10°", timePerUnit: 8, price: 12.0 },
  };

  // BOM defaults (very simplified, but consistent & extendable)
  const BOM_DEFAULTS = {
    dcCablePerModuleM: 2.5,
    mc4PairsPerString: 2,
    groundingM: 10,
    labelsSet: 1,
    surgeDC: 1,
    surgeAC: 1,
    dataCableM: 30,
    roofHooksPerModule: 2,
    railsMPerModulePerRow: 1.15, // approximate per module per rail-line; multiplied by 2 lines
    clampsPerModule: 4,          // mid/end mix simplified
    endClampsPerRow: 4,
    midClampsPerModule: 2,
    screwsPerHook: 2,
    roofSheetScrewsPerModule: 6,
    ballastKgPerModule: 25,      // flat roof example
  };

  // =========================================================
  // 2) HELPERS
  // =========================================================
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const clamp = (n, a, b) => Math.max(a, Math.min(b, n));

  const rafThrottle = (fn) => {
    let queued = false, lastArgs = null;
    return (...args) => {
      lastArgs = args;
      if (queued) return;
      queued = true;
      requestAnimationFrame(() => { queued = false; fn(...lastArgs); });
    };
  };

  const debounce = (fn, ms = 120) => {
    let t = null;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  function escapeHtml(str) {
    return String(str ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }
  const escapeAttr = (str) => escapeHtml(str).replaceAll("\n", " ");

  function adjustHex(hex, amt) {
    let c = parseInt(hex.replace("#", ""), 16);
    let r = (c >> 16) + amt;
    let g = ((c >> 8) & 0x00ff) + amt;
    let b = (c & 0x0000ff) + amt;
    r = clamp(r, 0, 255); g = clamp(g, 0, 255); b = clamp(b, 0, 255);
    return "#" + (0x1000000 + r * 0x10000 + g * 0x100 + b).toString(16).slice(1);
  }

  function Label(text) {
    return `<div class="text-[10px] font-bold text-slate-400 uppercase mb-1 tracking-wider">${escapeHtml(text)}</div>`;
  }
  function NavBtn(id, iconName, label, activeId) {
    const cls = activeId === id ? "bg-[#5FA5C8] text-white shadow-md" : "text-slate-500 hover:bg-slate-200";
    return `<button data-nav="${id}" class="flex-1 py-2 rounded-2xl flex items-center justify-center gap-2 text-xs font-bold transition-all ${cls}">
      <i data-lucide="${iconName}" class="w-3 h-3"></i> <span>${escapeHtml(label)}</span>
    </button>`;
  }
  function ModeBtn(id, active, iconName, label) {
    const cls = active ? "bg-[#5FA5C8] text-white shadow-md" : "text-slate-500 hover:bg-slate-200";
    return `<button data-mode="${id}" class="flex-1 py-2 text-xs font-bold rounded-lg flex items-center justify-center gap-2 transition-all ${cls}">
      <i data-lucide="${iconName}" class="w-3 h-3"></i> ${escapeHtml(label)}
    </button>`;
  }
  function ToolBtn(id, iconName, label, activeId) {
    const cls = activeId === id ? "bg-[#5FA5C8] text-white shadow-sm" : "text-slate-500 hover:bg-slate-200";
    return `<button data-tool="${id}" class="flex-1 py-2 text-xs font-bold rounded-lg flex items-center justify-center gap-2 transition-all ${cls}">
      <i data-lucide="${iconName}" class="w-3 h-3"></i> ${escapeHtml(label)}
    </button>`;
  }
  function ObsBtn(type, iconName, label, color) {
    return `<button data-obs="${type}" class="p-3 bg-white hover:bg-slate-50 rounded-xl flex flex-col items-center gap-1 transition-transform active:scale-95 border border-slate-200 shadow-sm hover:shadow-md">
      <i data-lucide="${iconName}" class="w-5 h-5 ${color}"></i>
      <span class="text-[10px] font-bold uppercase text-slate-500">${escapeHtml(label)}</span>
    </button>`;
  }
  function Range(id, label, val, min, max, unit = "m", stepOverride = null) {
    const step = stepOverride ?? (unit.trim() === "°" ? 1 : 0.1);
    const v = Number.isFinite(val) ? val : min;
    const display = unit.trim() === "°" ? String(Math.round(v)) : (step >= 1 ? String(Math.round(v)) : v.toFixed(2));
    return `
      <div>
        <div class="flex justify-between text-xs font-semibold text-slate-500 mb-1">
          <span>${escapeHtml(label)}</span>
          <span class="text-[#5FA5C8] font-mono">${display}${escapeHtml(unit)}</span>
        </div>
        <input data-range="${id}" type="range"
          min="${min}" max="${max}" step="${step}" value="${v}"
          class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#5FA5C8]"
        />
      </div>
    `;
  }

  function formatHours(min) {
    const h = min / 60;
    if (!Number.isFinite(h)) return "—";
    if (h < 1) return `${Math.round(min)} min`;
    return `${h.toFixed(1)} h`;
  }

  function sumObj(a, b) {
    const out = { ...a };
    for (const k of Object.keys(b)) out[k] = (out[k] || 0) + (b[k] || 0);
    return out;
  }

  // =========================================================
  // 3) TEXTURE GENERATOR (cached)
  // =========================================================
  const TextureGen = (() => {
    const cache = new Map();
    const keyOf = (color, type) => `${type}|${color}`;

    function makeCanvasTexture(color, type) {
      const s = 512;
      const c = document.createElement("canvas");
      c.width = s; c.height = s;
      const ctx = c.getContext("2d");
      if (!ctx) return new THREE.Texture();

      if (type === "wood") {
        ctx.fillStyle = "#dcb28b"; ctx.fillRect(0, 0, s, s);
        for (let i = 0; i < 60; i++) {
          ctx.fillStyle = `rgba(160,120,80,${Math.random() * 0.2})`;
          ctx.fillRect(Math.random() * s, 0, Math.random() * 30 + 5, s);
        }
      } else if (type === "iso") {
        ctx.fillStyle = "#facc15"; ctx.fillRect(0, 0, s, s);
        ctx.fillStyle = "rgba(255,255,255,0.2)";
        for (let i = 0; i < 1000; i++) ctx.fillRect(Math.random() * s, Math.random() * s, 2, 2);
      } else if (type === "membrane") {
        ctx.fillStyle = "#1e293b"; ctx.fillRect(0, 0, s, s);
        ctx.strokeStyle = "#334155"; ctx.lineWidth = 1;
        ctx.beginPath(); ctx.moveTo(0, 0); ctx.lineTo(s, s); ctx.moveTo(s, 0); ctx.lineTo(0, s); ctx.stroke();
      } else {
        const g = ctx.createLinearGradient(0, 0, 0, s);
        g.addColorStop(0, color);
        g.addColorStop(1, adjustHex(color, -30));
        ctx.fillStyle = g; ctx.fillRect(0, 0, s, s);
      }

      if (type === "tile") {
        const w = 128, h = 64;
        for (let y = 0; y < s; y += h) {
          for (let x = 0; x < s; x += w) {
            const off = (y / h) % 2 === 0 ? 0 : w / 2;
            ctx.fillStyle = "rgba(0,0,0,0.25)"; ctx.fillRect((x + off) % s, y + h - 4, w, 4);
            ctx.fillStyle = "rgba(255,255,255,0.09)"; ctx.fillRect((x + off) % s, y, 2, h);
          }
        }
      } else if (type === "solar") {
        ctx.fillStyle = "#050505"; ctx.fillRect(0, 0, s, s);
        ctx.strokeStyle = "rgba(255,255,255,0.15)"; ctx.lineWidth = 1; ctx.strokeRect(2, 2, s - 4, s - 4);
        ctx.lineWidth = 0.5;
        for (let i = 0; i <= s; i += s / 10) { ctx.beginPath(); ctx.moveTo(i, 0); ctx.lineTo(i, s); ctx.stroke(); }
      } else if (type === "metal") {
        for (let i = 0; i < 1000; i++) {
          ctx.fillStyle = `rgba(255,255,255,${Math.random() * 0.08})`;
          ctx.fillRect(Math.random() * s, Math.random() * s, 2, 2);
        }
      } else if (type === "glass") {
        ctx.fillStyle = color; ctx.fillRect(0, 0, s, s);
        ctx.fillStyle = "rgba(255,255,255,0.28)";
        ctx.beginPath();
        ctx.moveTo(0, s); ctx.lineTo(s, 0); ctx.lineTo(s - 20, 0); ctx.lineTo(0, s - 20);
        ctx.fill();
      }

      const t = new THREE.CanvasTexture(c);
      t.wrapS = t.wrapT = THREE.RepeatWrapping;
      t.anisotropy = 8;
      t.needsUpdate = true;
      return t;
    }

    return {
      create(color, type) {
        const k = keyOf(color, type);
        if (cache.has(k)) return cache.get(k);
        const tex = makeCanvasTexture(color, type);
        cache.set(k, tex);
        return tex;
      },
    };
  })();

  // =========================================================
  // 4) SUN (azimuth/elevation), HOUSE ROTATION, SHADOWS
  // World axes: +X east, +Z north, +Y up
  // =========================================================
  function solarVector({ lat, lon, tzOffset, month, day, hour }) {
    const toRad = (d) => (d * Math.PI) / 180;

    const L = Number(lat ?? 52.52);
    const M = Number(month ?? 6);
    const D = Number(day ?? 21);
    const H = Number(hour ?? 13.0);
    const TZ = Number(tzOffset ?? (-new Date().getTimezoneOffset() / 60));

    const date = new Date(Date.UTC(2026, M - 1, D, 12, 0, 0));
    const start = new Date(Date.UTC(date.getUTCFullYear(), 0, 0));
    const n = Math.floor((date - start) / 86400000);

    const gamma = (2 * Math.PI / 365) * (n - 1 + (H - 12) / 24);

    const decl =
      0.006918
      - 0.399912 * Math.cos(gamma)
      + 0.070257 * Math.sin(gamma)
      - 0.006758 * Math.cos(2 * gamma)
      + 0.000907 * Math.sin(2 * gamma)
      - 0.002697 * Math.cos(3 * gamma)
      + 0.00148 * Math.sin(3 * gamma);

    const eqTime =
      229.18 * (
        0.000075
        + 0.001868 * Math.cos(gamma)
        - 0.032077 * Math.sin(gamma)
        - 0.014615 * Math.cos(2 * gamma)
        - 0.040849 * Math.sin(2 * gamma)
      );

    const timeOffset = eqTime + 4 * lon - 60 * TZ;
    const trueSolarMinutes = (H * 60) + timeOffset;
    const hourAngle = toRad((trueSolarMinutes / 4) - 180);

    const latRad = toRad(L);

    const cosZenith =
      Math.sin(latRad) * Math.sin(decl) +
      Math.cos(latRad) * Math.cos(decl) * Math.cos(hourAngle);

    const zenith = Math.acos(Math.max(-1, Math.min(1, cosZenith)));
    const elevation = (Math.PI / 2) - zenith;

    const sinAz = -(Math.sin(hourAngle) * Math.cos(decl)) / Math.max(1e-6, Math.cos(elevation));
    const cosAz =
      (Math.sin(decl) - Math.sin(latRad) * Math.sin(elevation)) /
      Math.max(1e-6, (Math.cos(latRad) * Math.cos(elevation)));

    let azimuth = Math.atan2(sinAz, cosAz);
    if (azimuth < 0) azimuth += 2 * Math.PI;

    const dir = new THREE.Vector3(
      Math.sin(azimuth) * Math.cos(elevation), // east
      Math.sin(elevation),                    // up
      Math.cos(azimuth) * Math.cos(elevation) // north
    ).normalize();

    return { dir, azimuth, elevation };
  }

  // =========================================================
  // 5) SOLAR ENGINE
  // =========================================================
  class SolarEngine {
    static UP = new THREE.Vector3(0, 1, 0);

    constructor(canvas) {
      this.canvas = canvas;

      this.scene = new THREE.Scene();
      this.scene.background = new THREE.Color(0xdce7f0);

      this.camera = new THREE.PerspectiveCamera(45, 1, 0.1, 1200);
      this.camera.position.set(20, 25, 30);

      this.renderer = new THREE.WebGLRenderer({
        canvas,
        antialias: true,
        preserveDrawingBuffer: true,
        powerPreference: "high-performance",
      });
      this.renderer.setPixelRatio(Math.min(2, window.devicePixelRatio || 1));
      this.renderer.setSize(10, 10, false);

      // shadows + tonemapping
      this.renderer.shadowMap.enabled = true;
      this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
      this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
      this.renderer.toneMappingExposure = 1.0;

      this.controls = new OrbitControls(this.camera, canvas);
      this.controls.enableDamping = true;
      this.controls.dampingFactor = 0.06;
      this.controls.maxPolarAngle = Math.PI / 2 - 0.05;

      this.raycaster = new THREE.Raycaster();

      // groups
      this.root = new THREE.Group();
      this.housePivot = new THREE.Group(); // rotate house orientation here

      this.gStructure = new THREE.Group();
      this.gTruss = new THREE.Group();
      this.gRoofLayers = new THREE.Group();
      this.gRoof = new THREE.Group();          // ray targets
      this.gVisualTiles = new THREE.Group();   // visible roof tiles/surfaces
      this.gObstacles = new THREE.Group();
      this.gSolar = new THREE.Group();

      this.scene.add(this.root);
      this.root.add(this.housePivot);
      this.housePivot.add(
        this.gStructure,
        this.gTruss,
        this.gRoofLayers,
        this.gVisualTiles,
        this.gRoof,
        this.gObstacles,
        this.gSolar
      );

      this.mats = {};
      this.surfaces = new Map();  // id -> surface data
      this.geoData = {};          // convenience (main/south)
      this.modules = new Map();   // id -> {mesh, data}

      this.onObstacleMove = null;
      this.onModuleUpdate = null;

      this.dragPlane = new THREE.Plane();
      this.activeTool = "select";

      this.drag = {
        active: false,
        pointerId: null,
        type: null,
        obj: null,
        surfId: "",
        surf: null,
        startU: 0,
        startV: 0,
        curU: 0,
        curV: 0,
        targetU: 0,
        targetV: 0,
        smooth: 0.22,
        dead: 0.0005,
        snap: 0, // 0 = off, else snap in UV units (0.01 = 1%)
      };

      this.selection = { type: null, id: null, surfId: null }; // type: "module" | "obstacle"
      this.nudge = { step: 0.01, turbo: 0.04 }; // UV step, 1% default


      // vectors reuse
      this._v2 = new THREE.Vector2();
      this._v3 = new THREE.Vector3();
      this._tmp = new THREE.Vector3();
      this._tmp2 = new THREE.Vector3();
      this._q = new THREE.Quaternion();

      // lights + ground
      this.amb = new THREE.AmbientLight(0xffffff, 0.55);

      this.sun = new THREE.DirectionalLight(0xffffff, 1.25);
      this.sun.castShadow = true;
      this.sun.shadow.mapSize.set(2048, 2048);
      this.sun.shadow.camera.near = 0.5;
      this.sun.shadow.camera.far = 350;
      this.sun.shadow.camera.left = -70;
      this.sun.shadow.camera.right = 70;
      this.sun.shadow.camera.top = 70;
      this.sun.shadow.camera.bottom = -70;
      this.sun.shadow.bias = -0.0002;

      this.sunTarget = new THREE.Object3D();
      this.sunTarget.position.set(0, 5, 0);
      this.scene.add(this.sunTarget);
      this.sun.target = this.sunTarget;

      this.sunHelper = new THREE.DirectionalLightHelper(this.sun, 5);
      this.scene.add(this.amb, this.sun, this.sunHelper);

      const grid = new THREE.GridHelper(60, 60, 0xbdc3c7, 0xffffff);
      this.scene.add(grid);

      const groundGeo = new THREE.PlaneGeometry(200, 200);
      const groundMat = new THREE.MeshStandardMaterial({ color: 0xf3f6fb, roughness: 1 });
      this.ground = new THREE.Mesh(groundGeo, groundMat);
      this.ground.rotation.x = -Math.PI / 2;
      this.ground.receiveShadow = true;
      this.ground.position.y = 0;
      this.scene.add(this.ground);

      this.initMaterials();

      // events
      this.onPointerDown = this.onPointerDown.bind(this);
      this.onPointerMove = this.onPointerMove.bind(this);
      this.onPointerUp = this.onPointerUp.bind(this);
      this.onPointerCancel = this.onPointerCancel.bind(this);

      canvas.addEventListener("pointerdown", this.onPointerDown, { passive: false });
      canvas.addEventListener("pointermove", this.onPointerMove, { passive: false });
      canvas.addEventListener("pointerup", this.onPointerUp, { passive: false });
      canvas.addEventListener("pointercancel", this.onPointerCancel, { passive: false });
      canvas.tabIndex = 0;            // allow keyboard focus
      canvas.style.outline = "none";
      canvas.addEventListener("click", () => canvas.focus());


      this._lastNotify = 0;
      this._lastObsNotify = 0;

      this._anim = () => {
        this._raf = requestAnimationFrame(this._anim);

        if (this.drag.active && this.drag.obj && this.drag.surf) {
          const du = this.drag.targetU - this.drag.curU;
          const dv = this.drag.targetV - this.drag.curV;

          if (Math.abs(du) > this.drag.dead) this.drag.curU += du * this.drag.smooth;
          if (Math.abs(dv) > this.drag.dead) this.drag.curV += dv * this.drag.smooth;

          const surf = this.drag.surf;
          const offset = (this.drag.type === "module") ? 0.05 : 0.2;

          this._v3.copy(surf.origin);
          this._v3.addScaledVector(surf.vRight, this.drag.curU * surf.width);
          this._v3.addScaledVector(surf.vDown, this.drag.curV * surf.height);
          this._v3.addScaledVector(surf.vNormal, offset);

          this.drag.obj.position.copy(this._v3);
          this.drag.obj.quaternion.copy(this._q.setFromUnitVectors(SolarEngine.UP, surf.vNormal));

          const now = performance.now();

          if (this.drag.type === "module") {
            const mData = this.modules.get(this.drag.obj.userData.id);
            if (mData) { mData.data.x = this.drag.curU; mData.data.y = this.drag.curV; }
            if (this.onModuleUpdate && (now - this._lastNotify > 70)) {
              this._lastNotify = now;
              this.notifyModuleUpdate();
            }
          } else if (this.drag.type === "obstacle" && this.onObstacleMove) {
            if (now - this._lastObsNotify > 70) {
              this._lastObsNotify = now;
              this.onObstacleMove(this.drag.obj.userData.id, this.drag.surfId, this.drag.curU, this.drag.curV);
            }
          }
        }

        this.controls.update();
        this.renderer.render(this.scene, this.camera);
      };

      this._anim();
    }

    getSelected() {
      if (!this.selection.type || !this.selection.id) return null;
      if (this.selection.type === "module") {
        const m = this.modules.get(this.selection.id);
        return m ? { type: "module", obj: m.mesh, data: m.data } : null;
      }
      if (this.selection.type === "obstacle") {
        const obj = this.gObstacles.children.find(g => g.userData?.id === this.selection.id);
        const data = null; // your obstacle data lives in state, not inside engine
        return obj ? { type: "obstacle", obj, data } : null;
      }
      return null;
    }

    selectTarget(type, id, surfId) {
      this.selection.type = type;
      this.selection.id = id;
      this.selection.surfId = surfId || this.selection.surfId;

      // visual feedback for modules (you already have)
      if (type === "module") {
        if (!id) return;
        this.deselectAll();
        this.selectModule(id);
      }
    }

    getUVForObjectOnSurface(obj, surf) {
      this._tmp.copy(obj.position).sub(surf.origin);
      const u = clamp(this._tmp.dot(surf.vRight) / surf.width, 0, 1);
      const v = clamp(this._tmp.dot(surf.vDown) / surf.height, 0, 1);
      return { u, v };
    }

    nudgeSelected(du, dv) {
      const sel = this.getSelected();
      if (!sel) return;

      const surfId =
        sel.type === "module"
          ? (this.modules.get(sel.obj.userData.id)?.data?.surfaceId)
          : (this.selection.surfId);

      const surf = this.surfaces.get(surfId);
      if (!surf) return;

      const uv = this.getUVForObjectOnSurface(sel.obj, surf);
      let u = clamp(uv.u + du, 0, 1);
      let v = clamp(uv.v + dv, 0, 1);

      if (this.drag.snap > 0) {
        const s = this.drag.snap;
        u = clamp(Math.round(u / s) * s, 0, 1);
        v = clamp(Math.round(v / s) * s, 0, 1);
      }

      // reuse your smooth mover without pointer capture:
      this.drag.active = true;
      this.drag.type = sel.type;
      this.drag.obj = sel.obj;
      this.drag.surfId = surfId;
      this.drag.surf = surf;

      this.drag.curU = uv.u;
      this.drag.curV = uv.v;
      this.drag.targetU = u;
      this.drag.targetV = v;
    }

    endNudge() {
      if (!this.drag.active) return;
      this.drag.active = false;
      this.drag.type = null;
      this.drag.obj = null;
      this.drag.surf = null;
      this.notifyModuleUpdate();
    }


    destroy() {
      cancelAnimationFrame(this._raf);
      this.canvas.removeEventListener("pointerdown", this.onPointerDown);
      this.canvas.removeEventListener("pointermove", this.onPointerMove);
      this.canvas.removeEventListener("pointerup", this.onPointerUp);
      this.canvas.removeEventListener("pointercancel", this.onPointerCancel);

      this.scene.traverse((obj) => {
        if (obj.geometry) obj.geometry.dispose?.();
        if (obj.material) {
          const mats = Array.isArray(obj.material) ? obj.material : [obj.material];
          mats.forEach((m) => {
            if (!m) return;
            for (const k of Object.keys(m)) { const v = m[k]; if (v && v.isTexture) v.dispose?.(); }
            m.dispose?.();
          });
        }
      });

      try { this.renderer.dispose(); } catch (_) {}
      try { this.renderer.forceContextLoss?.(); } catch (_) {}
    }

    resize(w, h) {
      const W = Math.max(10, w);
      const H = Math.max(10, h);
      this.camera.aspect = W / H;
      this.camera.updateProjectionMatrix();
      this.renderer.setSize(W, H, false);
    }

    getSnapshot() {
      this.renderer.render(this.scene, this.camera);
      return this.renderer.domElement.toDataURL("image/png");
    }

    initMaterials() {
      this.mats.wall = new THREE.MeshStandardMaterial({ color: "#e2e8f0", map: TextureGen.create("#e2e8f0", "noise") });

      this.mats.tileRed = new THREE.MeshStandardMaterial({ color: "#a14332", map: TextureGen.create("#a14332", "tile"), roughness: 0.7 });
      this.mats.tileDark = new THREE.MeshStandardMaterial({ color: "#334155", map: TextureGen.create("#334155", "tile"), roughness: 0.7 });
      this.mats.gravel = new THREE.MeshStandardMaterial({ color: "#888888", map: TextureGen.create("#555555", "gravel"), roughness: 1 });

      this.mats.solar = new THREE.MeshStandardMaterial({ color: "#ffffff", map: TextureGen.create("#000000", "solar"), roughness: 0.2, metalness: 0.8 });
      this.mats.solarSelected = new THREE.MeshStandardMaterial({
        color: "#44ff44",
        map: TextureGen.create("#000000", "solar"),
        roughness: 0.2,
        metalness: 0.8,
        emissive: 0x22aa22,
        emissiveIntensity: 0.2,
      });

      this.mats.chimney = new THREE.MeshStandardMaterial({ color: "#884444", map: TextureGen.create("#884444", "noise") });
      this.mats.metal = new THREE.MeshStandardMaterial({ color: "#aaaaaa", map: TextureGen.create("#aaaaaa", "metal"), metalness: 0.7, roughness: 0.3 });
      this.mats.glass = new THREE.MeshStandardMaterial({ color: "#aaddff", map: TextureGen.create("#aaddff", "glass"), transparent: true, opacity: 0.6 });

      this.mats.wood = new THREE.MeshStandardMaterial({ color: "#dcb28b", map: TextureGen.create("#dcb28b", "wood"), roughness: 0.8 });
      this.mats.insulation = new THREE.MeshStandardMaterial({ color: "#facc15", map: TextureGen.create("#facc15", "iso"), roughness: 1 });
      this.mats.membrane = new THREE.MeshStandardMaterial({ color: "#1e293b", map: TextureGen.create("#1e293b", "membrane"), roughness: 0.6, side: THREE.DoubleSide });
      this.mats.batten = new THREE.MeshStandardMaterial({ color: "#8d6e63", roughness: 0.9 });

      this.mats.invisible = new THREE.MeshBasicMaterial({ transparent: true, opacity: 0, depthWrite: false });
    }

    // ---- scene controls ----
    setHouseYaw(deg) {
      const rad = THREE.MathUtils.degToRad(deg || 0);
      this.housePivot.rotation.y = rad;
    }

    setSun(sunCfg) {
      const { dir } = solarVector(sunCfg);
      const dist = 140;
      this.sun.position.copy(dir.clone().multiplyScalar(dist));
      this.sun.intensity = Number(sunCfg?.intensity ?? 1.25);
      this.sunHelper.visible = !!sunCfg?.showHelper;
      this.sunHelper.update();
    }

    setRoofVisibility(vis) {
      this.gVisualTiles.visible = !!vis.tiles;
      this.gTruss.visible = !!vis.truss;
      this.gRoofLayers.visible = !!vis.layers;
      this.gSolar.visible = !!vis.pv;
      this.gObstacles.visible = !!vis.obstacles;

      const opacity = clamp(Number(vis.opacity ?? 1), 0, 1);
      [this.mats.tileRed, this.mats.tileDark].forEach((m) => {
        if (!m) return;
        m.transparent = opacity < 1;
        m.opacity = opacity;
        m.needsUpdate = true;
      });
    }

    setSnap(v) {
      this.drag.snap = Math.max(0, Number(v || 0));
    }

    // ---- input / dragging ----
    getNormalizedPointer(e) {
      const rect = this.renderer.domElement.getBoundingClientRect();
      return {
        x: ((e.clientX - rect.left) / rect.width) * 2 - 1,
        y: -((e.clientY - rect.top) / rect.height) * 2 + 1,
      };
    }

    pickSurfaceByMesh(mesh) {
      for (const [id, s] of this.surfaces) if (s.mesh === mesh) return { id, surf: s };
      return { id: "", surf: null };
    }

    onPointerDown(e) {
        e.preventDefault?.();

        const p = this.getNormalizedPointer(e);
        this.raycaster.setFromCamera(this._v2.set(p.x, p.y), this.camera);

        // 1) Modules hit test
        const modHits = this.raycaster.intersectObjects(this.gSolar.children, true);
        if (modHits.length > 0) {
          const mesh = modHits[0].object;
          const mData = this.modules.get(mesh.userData.id);
          if (!mData) return;

          // keep selection state
          this.selectTarget("module", mData.data.id, mData.data.surfaceId);

          if (this.activeTool === "delete") { this.removeModule(mData.data.id); return; }
          if (this.activeTool === "duplicate") { this.duplicateModule(mData.data.id); return; }

          if (this.activeTool === "select" || this.activeTool === "move") {
            if (!e.shiftKey) this.deselectAll();
            this.selectModule(mData.data.id);

            if (this.activeTool === "move") {
              const surf = this.surfaces.get(mData.data.surfaceId);
              if (!surf) return;

              this.renderer.domElement.setPointerCapture?.(e.pointerId);

              this.drag.active = true;
              this.drag.pointerId = e.pointerId;
              this.drag.type = "module";
              this.drag.obj = mesh;
              this.drag.surfId = mData.data.surfaceId;
              this.drag.surf = surf;

              this.dragPlane.setFromNormalAndCoplanarPoint(surf.vNormal, surf.origin);

              this.drag.startU = mData.data.x;
              this.drag.startV = mData.data.y;
              this.drag.curU = this.drag.startU;
              this.drag.curV = this.drag.startV;
              this.drag.targetU = this.drag.startU;
              this.drag.targetV = this.drag.startV;

              this.controls.enabled = false;
            }
            return;
          }
          return;
        } else {
          if (this.activeTool === "select" && !e.shiftKey) this.deselectAll();
        }

        // 2) Obstacles hit test
        const obsHits = this.raycaster.intersectObjects(this.gObstacles.children, true);
        if (obsHits.length > 0) {
          let obj = obsHits[0].object;
          while (obj.parent && obj.parent !== this.gObstacles) obj = obj.parent;
          if (!obj.userData.id) return;

          const roofHits = this.raycaster.intersectObjects(this.gRoof.children, true);
          if (!roofHits.length) return;

          const hit = roofHits[0];
          const { id: surfId, surf } = this.pickSurfaceByMesh(hit.object);
          if (!surf) return;

          this.renderer.domElement.setPointerCapture?.(e.pointerId);

          this.drag.active = true;
          this.drag.pointerId = e.pointerId;
          this.drag.type = "obstacle";
          this.drag.obj = obj;
          this.drag.surfId = surfId;
          this.drag.surf = surf;

          this.dragPlane.setFromNormalAndCoplanarPoint(surf.vNormal, surf.origin);

          this.selectTarget("obstacle", obj.userData.id, surfId);

          if (this.activeTool !== "move" && this.activeTool !== "select") return;

          this._tmp.copy(obj.position).sub(surf.origin);
          this.drag.startU = clamp(this._tmp.dot(surf.vRight) / surf.width, 0, 1);
          this.drag.startV = clamp(this._tmp.dot(surf.vDown) / surf.height, 0, 1);

          this.drag.curU = this.drag.startU;
          this.drag.curV = this.drag.startV;
          this.drag.targetU = this.drag.startU;
          this.drag.targetV = this.drag.startV;

          this.controls.enabled = false;
        }
      }


    onPointerMove(e) {
      if (!this.drag.active || !this.drag.surf || !this.drag.obj) return;

      const p = this.getNormalizedPointer(e);
      this.raycaster.setFromCamera(this._v2.set(p.x, p.y), this.camera);

      const hitPoint = this._tmp2;
      const ok = this.raycaster.ray.intersectPlane(this.dragPlane, hitPoint);
      if (!ok) return;

      const surf = this.drag.surf;
      this._tmp.copy(hitPoint).sub(surf.origin);

      let u = clamp(this._tmp.dot(surf.vRight) / surf.width, 0, 1);
      let v = clamp(this._tmp.dot(surf.vDown) / surf.height, 0, 1);

      // snap if enabled
      if (this.drag.snap > 0) {
        const s = this.drag.snap;
        u = clamp(Math.round(u / s) * s, 0, 1);
        v = clamp(Math.round(v / s) * s, 0, 1);
      }

      this.drag.targetU = u;
      this.drag.targetV = v;
    }

    onPointerUp(e) {
      this.renderer.domElement.releasePointerCapture?.(e.pointerId);

      if (!this.drag.active) { this.controls.enabled = true; return; }

      this.drag.active = false;
      this.drag.pointerId = null;
      this.drag.type = null;
      this.drag.obj = null;
      this.drag.surf = null;
      this.controls.enabled = true;

      this.notifyModuleUpdate();
    }

    onPointerCancel(e) {
      this.renderer.domElement.releasePointerCapture?.(e.pointerId);
      this.drag.active = false;
      this.drag.pointerId = null;
      this.drag.type = null;
      this.drag.obj = null;
      this.drag.surf = null;
      this.controls.enabled = true;
    }

    // ---- module actions ----
    selectModule(id) {
      const m = this.modules.get(id);
      if (m) { m.data.selected = true; m.mesh.material = this.mats.solarSelected; }
    }

    deselectAll() {
      this.modules.forEach((m) => { m.data.selected = false; m.mesh.material = this.mats.solar; });
    }

    removeModule(id) {
      const m = this.modules.get(id);
      if (!m) return;
      this.gSolar.remove(m.mesh);
      this.modules.delete(id);
      this.notifyModuleUpdate();
    }

    duplicateModule(id) {
      const m = this.modules.get(id);
      if (!m) return;
      const newData = { ...m.data, id: crypto.randomUUID(), x: clamp(m.data.x + 0.05, 0, 1), selected: true };
      this.addModuleMesh(newData);
      this.notifyModuleUpdate();
    }

    addModuleMesh(d) {
      const surf = this.surfaces.get(d.surfaceId);
      if (!surf) return;

      const orientation = d.orientation || "portrait";
      const mW = orientation === "portrait" ? MODULE_DIMS.width : MODULE_DIMS.height;
      const mH = orientation === "portrait" ? MODULE_DIMS.height : MODULE_DIMS.width;

      const geo = new THREE.BoxGeometry(mW, MODULE_DIMS.depth, mH);
      const m = new THREE.Mesh(geo, this.mats.solar);
      m.castShadow = true;
      m.receiveShadow = true;

      this._v3.copy(surf.origin);
      this._v3.addScaledVector(surf.vRight, d.x * surf.width);
      this._v3.addScaledVector(surf.vDown, d.y * surf.height);
      this._v3.addScaledVector(surf.vNormal, 0.05);
      m.position.copy(this._v3);
      m.quaternion.copy(this._q.setFromUnitVectors(SolarEngine.UP, surf.vNormal));

      m.userData = { id: d.id };
      this.gSolar.add(m);
      this.modules.set(d.id, { mesh: m, data: d });

      if (d.selected) this.selectModule(d.id);
    }

    clearModules(notify = true) {
      while (this.gSolar.children.length) {
        const c = this.gSolar.children.pop();
        if (c?.geometry) c.geometry.dispose?.();
        this.gSolar.remove(c);
      }
      this.modules.clear();
      if (notify) this.notifyModuleUpdate();
    }

    notifyModuleUpdate() {
      if (!this.onModuleUpdate) return;
      const arr = Array.from(this.modules.values()).map((m) => m.data);
      this.onModuleUpdate(arr);
    }

    // ---- surfaces / building ----
    addSurface(id, mesh, origin, vRight, vDown, vNormal, w, h, type, extra = {}) {
      this.surfaces.set(id, { mesh, origin, vRight, vDown, vNormal, width: w, height: h, type, ...extra });
      this.gRoof.add(mesh);
      if (id === "south" || id === "main") this.geoData = this.surfaces.get(id);
    }

    updateBuilding(p, covering) {
      [this.gStructure, this.gRoof, this.gTruss, this.gRoofLayers, this.gVisualTiles].forEach((g) => g.clear());
      this.surfaces.clear();

      const roofMat = (["ziegel", "schiefer"].includes(covering))
        ? (covering === "ziegel" ? this.mats.tileRed : this.mats.tileDark)
        : this.mats.gravel;

      if (p.category === "flat") this.buildFlat(p, roofMat);
      else if (p.shape === "sattel") this.buildSattel(p, roofMat);
      else if (p.shape === "pult") this.buildPult(p, roofMat);
      else if (p.shape === "walm") this.buildWalm(p, roofMat);
      else this.buildSattel(p, roofMat);
    }

    createBeam(w, h, l, pos, rot, mat = this.mats.wood) {
      const geo = new THREE.BoxGeometry(w, h, l);
      const mesh = new THREE.Mesh(geo, mat);
      mesh.position.copy(pos);
      mesh.rotation.copy(rot);
      mesh.castShadow = true;
      mesh.receiveShadow = true;
      return mesh;
    }

    buildDetailedRoofPlane(surfId, p, baseCenter, width, slopeLength, rotationX, rotationY, roofMat) {
      const spread = p.layerSpread || 0;
      const dim = {
        rafterW: p.rafterWidth / 100, rafterH: p.rafterHeight / 100, rafterDist: p.rafterSpacing / 100,
        purlinW: 0.14, purlinH: 0.14,
        battenW: 0.03, battenH: 0.04, battenDist: p.battenDist / 100,
        counterW: 0.03, counterH: 0.05,
        insulationH: 0.14, tileH: 0.05,
      };

      const planeRot = new THREE.Euler(rotationX, rotationY, 0);
      const normal = new THREE.Vector3(0, 1, 0).applyEuler(planeRot).normalize();
      const right = new THREE.Vector3(1, 0, 0).applyEuler(planeRot).normalize();
      const down = new THREE.Vector3(0, 0, 1).applyEuler(planeRot).normalize();

      // rafters
      const numRafters = Math.max(1, Math.floor(width / dim.rafterDist));
      for (let i = 0; i <= numRafters; i++) {
        const xOff = -width / 2 + dim.rafterW / 2 + i * ((width - dim.rafterW) / numRafters);
        const rPos = baseCenter.clone().add(right.clone().multiplyScalar(xOff));
        this.gTruss.add(this.createBeam(dim.rafterW, dim.rafterH, slopeLength, rPos, planeRot, this.mats.wood));
      }

      const ridgePos = baseCenter.clone()
        .add(down.clone().multiplyScalar(-slopeLength / 2))
        .add(normal.clone().multiplyScalar(-dim.rafterH / 2 - dim.purlinH / 2));
      this.gTruss.add(this.createBeam(width, dim.purlinH, dim.purlinW, ridgePos, planeRot, this.mats.wood));

      const eavePos = baseCenter.clone()
        .add(down.clone().multiplyScalar(slopeLength / 2))
        .add(normal.clone().multiplyScalar(-dim.rafterH / 2 - dim.purlinH / 2));
      this.gTruss.add(this.createBeam(width, dim.purlinH, dim.purlinW, eavePos, planeRot, this.mats.wood));

      // layers
      let currentH = dim.rafterH / 2;
      currentH += spread * 0.1;

      const membrane = new THREE.Mesh(new THREE.PlaneGeometry(width, slopeLength), this.mats.membrane);
      membrane.castShadow = false;
      membrane.receiveShadow = false;
      membrane.position.copy(baseCenter).add(normal.clone().multiplyScalar(currentH));
      membrane.rotation.copy(planeRot);
      membrane.rotation.x -= Math.PI / 2;
      this.gRoofLayers.add(membrane);

      currentH += dim.counterH / 2 + spread * 0.2;
      for (let i = 0; i <= numRafters; i++) {
        const xOff = -width / 2 + dim.rafterW / 2 + i * ((width - dim.rafterW) / numRafters);
        const cPos = baseCenter.clone()
          .add(right.clone().multiplyScalar(xOff))
          .add(normal.clone().multiplyScalar(currentH));
        this.gRoofLayers.add(this.createBeam(dim.counterW, dim.counterH, slopeLength, cPos, planeRot, this.mats.batten));
      }

      currentH += dim.insulationH / 2 + spread * 0.5;
      const insulation = new THREE.Mesh(new THREE.BoxGeometry(width, dim.insulationH, slopeLength), this.mats.insulation);
      insulation.castShadow = false;
      insulation.receiveShadow = false;
      insulation.position.copy(baseCenter).add(normal.clone().multiplyScalar(currentH));
      insulation.rotation.copy(planeRot);
      this.gRoofLayers.add(insulation);

      currentH += dim.insulationH / 2 + dim.battenH / 2 + spread * 0.2;
      const numBattens = Math.max(1, Math.floor(slopeLength / dim.battenDist));
      for (let j = 0; j <= numBattens; j++) {
        const yOff = -slopeLength / 2 + j * (slopeLength / numBattens);
        const bPos = baseCenter.clone()
          .add(down.clone().multiplyScalar(yOff))
          .add(normal.clone().multiplyScalar(currentH));
        this.gRoofLayers.add(this.createBeam(width, dim.battenH, dim.battenW, bPos, planeRot.clone(), this.mats.batten));
      }

      currentH += dim.battenH / 2 + dim.tileH / 2 + spread * 0.5;
      const tiles = new THREE.Mesh(new THREE.BoxGeometry(width, dim.tileH, slopeLength), roofMat);
      tiles.castShadow = true;
      tiles.receiveShadow = true;
      tiles.position.copy(baseCenter).add(normal.clone().multiplyScalar(currentH));
      tiles.rotation.copy(planeRot);
      this.gVisualTiles.add(tiles);

      // ray target (invisible)
      const rayTarget = new THREE.Mesh(new THREE.BoxGeometry(width, 0.01, slopeLength), this.mats.invisible);
      rayTarget.position.copy(tiles.position);
      rayTarget.rotation.copy(planeRot);

      const origin = rayTarget.position.clone()
        .add(right.clone().multiplyScalar(-width / 2))
        .add(down.clone().multiplyScalar(-slopeLength / 2));

      this.addSurface(surfId, rayTarget, origin, right, down, normal, width, slopeLength, "rect");
    }

    buildSattel(p, mat) {
      const rad = (p.pitch * Math.PI) / 180;
      const rise = (p.width / 2) * Math.tan(rad);
      const yEave = p.height;
      const yRidge = p.height + rise;

      const wall = new THREE.Mesh(new THREE.BoxGeometry(p.length, p.height, p.width), this.mats.wall);
      wall.position.y = p.height / 2;
      wall.castShadow = true;
      wall.receiveShadow = true;
      this.gStructure.add(wall);

      const shape = new THREE.Shape();
      shape.moveTo(-p.width / 2, 0);
      shape.lineTo(p.width / 2, 0);
      shape.lineTo(0, rise);

      const gGeo = new THREE.ExtrudeGeometry(shape, { depth: 0.2, bevelEnabled: false });
      const g1 = new THREE.Mesh(gGeo, this.mats.wall);
      g1.castShadow = true; g1.receiveShadow = true;
      g1.rotation.y = Math.PI / 2;
      g1.position.set(-p.length / 2, yEave, 0.1);
      const g2 = g1.clone();
      g2.position.set(p.length / 2, yEave, 0.1);
      this.gStructure.add(g1, g2);

      const horizontalRun = p.width / 2 + p.overhang;
      const slopeLen = horizontalRun / Math.cos(rad);
      const totalLen = p.length + p.overhangGable * 2;

      const zOff = Math.cos(rad) * slopeLen / 2;
      const yOff = Math.sin(rad) * slopeLen / 2;

      this.buildDetailedRoofPlane("south", p, new THREE.Vector3(0, yRidge - yOff, zOff), totalLen, slopeLen, rad, 0, mat);
      this.buildDetailedRoofPlane("north", p, new THREE.Vector3(0, yRidge - yOff, -zOff), totalLen, slopeLen, -rad, Math.PI, mat);
    }

    buildPult(p, mat) {
      const rad = (p.pitch * Math.PI) / 180;
      const rise = p.width * Math.tan(rad);
      const yLow = p.height;
      const yHigh = p.height + rise;

      const backWall = new THREE.Mesh(new THREE.BoxGeometry(p.length, yHigh, 0.2), this.mats.wall);
      backWall.castShadow = true; backWall.receiveShadow = true;
      backWall.position.set(0, yHigh / 2, -p.width / 2);
      this.gStructure.add(backWall);

      const frontWall = new THREE.Mesh(new THREE.BoxGeometry(p.length, yLow, 0.2), this.mats.wall);
      frontWall.castShadow = true; frontWall.receiveShadow = true;
      frontWall.position.set(0, yLow / 2, p.width / 2);
      this.gStructure.add(frontWall);

      const shape = new THREE.Shape();
      shape.moveTo(p.width / 2, 0);
      shape.lineTo(p.width / 2, yLow);
      shape.lineTo(-p.width / 2, yHigh);
      shape.lineTo(-p.width / 2, 0);

      const sideGeo = new THREE.ExtrudeGeometry(shape, { depth: 0.2, bevelEnabled: false });
      const leftWall = new THREE.Mesh(sideGeo, this.mats.wall);
      leftWall.castShadow = true; leftWall.receiveShadow = true;
      leftWall.rotation.y = -Math.PI / 2;
      leftWall.position.set(-p.length / 2, 0, 0);
      this.gStructure.add(leftWall);

      const rightWall = new THREE.Mesh(sideGeo, this.mats.wall);
      rightWall.castShadow = true; rightWall.receiveShadow = true;
      rightWall.rotation.y = -Math.PI / 2;
      rightWall.position.set(p.length / 2 + 0.2, 0, 0);
      this.gStructure.add(rightWall);

      const totalLen = p.length + p.overhangGable * 2;
      const slopeLen = (p.width + p.overhang * 2) / Math.cos(rad);
      const centerY = yLow + rise / 2;

      this.buildDetailedRoofPlane("main", p, new THREE.Vector3(0, centerY, 0), totalLen, slopeLen, rad, 0, mat);
    }

    buildWalm(p, mat) {
      const rad = (p.pitch * Math.PI) / 180;
      const rise = (p.width / 2) * Math.tan(rad);
      const yEave = p.height;
      const yRidge = p.height + rise;
      const ridgeLen = Math.max(0, p.length - p.width);
      const oh = p.overhang;

      const w = new THREE.Mesh(new THREE.BoxGeometry(p.length, p.height, p.width), this.mats.wall);
      w.castShadow = true; w.receiveShadow = true;
      w.position.y = p.height / 2;
      this.gStructure.add(w);

      const rL = new THREE.Vector3(-ridgeLen / 2, yRidge, 0);
      const rR = new THREE.Vector3(ridgeLen / 2, yRidge, 0);
      const eFL = new THREE.Vector3(-p.length / 2 - oh, yEave, p.width / 2 + oh);
      const eFR = new THREE.Vector3(p.length / 2 + oh, yEave, p.width / 2 + oh);
      const eBL = new THREE.Vector3(-p.length / 2 - oh, yEave, -p.width / 2 - oh);
      const eBR = new THREE.Vector3(p.length / 2 + oh, yEave, -p.width / 2 - oh);

      const slopeLen = Math.sqrt(Math.pow(p.width / 2 + oh, 2) + Math.pow(rise, 2));
      const wBot = p.length + 2 * oh;

      const addFace = (pts, id, uDir, vDir, w, h, extra) => {
        const origin = pts[0].clone();
        const positions = [];
        const uvs = [];

        const uAxis = uDir.clone().normalize();
        const vAxis = vDir.clone().normalize();

        for (const p of pts) {
          positions.push(p.x, p.y, p.z);
          const rel = p.clone().sub(origin);
          uvs.push(rel.dot(uAxis) / Math.max(1e-6, w), rel.dot(vAxis) / Math.max(1e-6, h));
        }

        const geo = new THREE.BufferGeometry();
        geo.setAttribute("position", new THREE.Float32BufferAttribute(positions, 3));
        geo.setAttribute("uv", new THREE.Float32BufferAttribute(uvs, 2));
        geo.computeVertexNormals();

        const mesh = new THREE.Mesh(geo, mat);
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        this.gVisualTiles.add(mesh);

        const rayMesh = new THREE.Mesh(geo, this.mats.invisible);

        const norm = new THREE.Vector3().crossVectors(uDir, vDir).normalize();
        if (norm.y < 0) norm.negate();

        this.addSurface(id, rayMesh, origin, uDir.clone().normalize(), vDir.clone().normalize(), norm, w, h, "trapezoid", extra);
      };

      addFace([rL, eFL, eFR, rL, eFR, rR], "south",
        new THREE.Vector3(1, 0, 0),
        new THREE.Vector3(0, -rise, p.width / 2 + oh).normalize(),
        wBot, slopeLen, { wTop: ridgeLen, wBot }
      );

      addFace([rR, eBR, eBL, rR, eBL, rL], "north",
        new THREE.Vector3(-1, 0, 0),
        new THREE.Vector3(0, -rise, -p.width / 2 - oh).normalize(),
        wBot, slopeLen, { wTop: ridgeLen, wBot }
      );

      const slopeLenGable = Math.sqrt(Math.pow(p.width / 2 + oh, 2) + Math.pow(rise, 2));
      addFace([rL, eBL, eFL], "west",
        new THREE.Vector3(0, 0, 1),
        new THREE.Vector3(-1, -rise / (p.width / 2 + oh), 0).normalize(),
        p.width + 2 * oh, slopeLenGable, {}
      );
      addFace([rR, eFR, eBR], "east",
        new THREE.Vector3(0, 0, -1),
        new THREE.Vector3(1, -rise / (p.width / 2 + oh), 0).normalize(),
        p.width + 2 * oh, slopeLenGable, {}
      );
    }

    buildFlat(p, mat) {
      const shape = new THREE.Shape();
      const L = p.length, B = p.width;

      if (p.shape === "rect") {
        shape.moveTo(-L / 2, -B / 2);
        shape.lineTo(L / 2, -B / 2);
        shape.lineTo(L / 2, B / 2);
        shape.lineTo(-L / 2, B / 2);
        shape.lineTo(-L / 2, -B / 2);
      } else if (p.shape === "l-shape") {
        const LB = p.lengthB, WB = p.widthB;
        shape.moveTo(-L / 2, -B / 2);
        shape.lineTo(L / 2, -B / 2);
        shape.lineTo(L / 2, -B / 2 + WB);
        shape.lineTo(-L / 2 + LB, -B / 2 + WB);
        shape.lineTo(-L / 2 + LB, B / 2);
        shape.lineTo(-L / 2, B / 2);
      }

      const gGeo = new THREE.ExtrudeGeometry(shape, { depth: p.height, bevelEnabled: false });
      const walls = new THREE.Mesh(gGeo, this.mats.wall);
      walls.castShadow = true; walls.receiveShadow = true;
      walls.rotation.x = Math.PI / 2;
      walls.position.y = p.height;
      this.gStructure.add(walls);

      const plane = new THREE.Mesh(new THREE.ShapeGeometry(shape), mat);
      plane.castShadow = true; plane.receiveShadow = true;
      plane.rotation.x = Math.PI / 2;
      plane.position.y = p.height + 0.05;
      this.gVisualTiles.add(plane);

      const rayTarget = new THREE.Mesh(new THREE.ShapeGeometry(shape), this.mats.invisible);
      rayTarget.rotation.x = Math.PI / 2;
      rayTarget.position.y = p.height + 0.06;

      this.addSurface(
        "main",
        rayTarget,
        new THREE.Vector3(-L / 2, p.height, -B / 2),
        new THREE.Vector3(1, 0, 0),
        new THREE.Vector3(0, 0, 1),
        new THREE.Vector3(0, 1, 0),
        L,
        B,
        "poly"
      );
    }

    updateObstacles(list) {
      while (this.gObstacles.children.length) this.gObstacles.remove(this.gObstacles.children[0]);

      list.forEach((obs) => {
        const surf = this.surfaces.get(obs.surfaceId);
        if (!surf) return;

        const grp = new THREE.Group();
        grp.userData = { id: obs.id };

        let mesh;
        if (obs.type === "chimney") {
          mesh = new THREE.Mesh(new THREE.BoxGeometry(obs.width, obs.height, obs.depth), this.mats.chimney);
          mesh.position.y = obs.height / 2;
        } else if (obs.type === "window") {
          mesh = new THREE.Mesh(new THREE.BoxGeometry(obs.width, 0.05, obs.height), this.mats.glass);
          const f = new THREE.Mesh(new THREE.BoxGeometry(obs.width + 0.1, 0.04, obs.height + 0.1), this.mats.wall);
          f.position.y = -0.02;
          mesh.add(f);
        } else if (obs.type === "sat") {
          mesh = new THREE.Group();
          const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.05, 0.8), this.mats.metal);
          pole.rotation.x = Math.PI / 2;
          pole.position.y = 0.4;
          const dish = new THREE.Mesh(new THREE.SphereGeometry(0.4, 32, 16, 0, Math.PI * 2, 0, 0.5), this.mats.metal);
          dish.position.set(0, 0.8, 0.2);
          dish.rotation.x = -Math.PI / 4;
          mesh.add(pole, dish);
        } else if (obs.type === "tree") {
          mesh = new THREE.Group();
          const trunk = new THREE.Mesh(new THREE.CylinderGeometry(obs.width * 0.12, obs.width * 0.14, obs.height * 0.35, 16), this.mats.wood);
          trunk.position.y = (obs.height * 0.35) / 2;
          const crown = new THREE.Mesh(new THREE.ConeGeometry(obs.width * 0.55, obs.height * 0.85, 18), new THREE.MeshStandardMaterial({ color: 0x2ecc71, roughness: 1 }));
          crown.position.y = obs.height * 0.35 + (obs.height * 0.85) / 2;
          trunk.castShadow = trunk.receiveShadow = true;
          crown.castShadow = crown.receiveShadow = true;
          mesh.add(trunk, crown);
        } else {
          mesh = new THREE.Mesh(new THREE.CylinderGeometry(0.1, 0.1, 0.4), this.mats.metal);
          mesh.rotation.x = Math.PI / 2;
        }

        // shadows
        mesh.traverse?.((o) => {
          if (o.isMesh) { o.castShadow = true; o.receiveShadow = true; }
        });
        if (mesh.isMesh) { mesh.castShadow = true; mesh.receiveShadow = true; }

        grp.add(mesh);

        this._v3.copy(surf.origin);
        this._v3.addScaledVector(surf.vRight, obs.x * surf.width);
        this._v3.addScaledVector(surf.vDown, obs.y * surf.height);
        this._v3.addScaledVector(surf.vNormal, 0.2);

        grp.position.copy(this._v3);
        grp.quaternion.copy(this._q.setFromUnitVectors(SolarEngine.UP, surf.vNormal));
        this.gObstacles.add(grp);
      });
    }

    autoLayout(surfaceConfigs, obstacles, moduleOrientation = "portrait") {
      this.clearModules(false);

      this.surfaces.forEach((surf, surfId) => {
        const config = surfaceConfigs[surfId] || surfaceConfigs.default;
        if (!config || !config.enabled) return;

        const { width, height, type, wTop, wBot } = surf;
        const orientation = config.orientation || moduleOrientation;

        const mW = orientation === "portrait" ? MODULE_DIMS.width : MODULE_DIMS.height;
        const mH = orientation === "portrait" ? MODULE_DIMS.height : MODULE_DIMS.width;

        const gap = config.gap / 100;
        const margin = config.margin / 100;

        const usableW = Math.max(0, width - margin * 2);
        const usableH = Math.max(0, height - margin * 2);

        const cols = Math.floor((usableW + gap) / (mW + gap));
        const rows = Math.floor((usableH + gap) / (mH + gap));
        if (cols <= 0 || rows <= 0) return;

        const gridW = cols * mW + (cols - 1) * gap;
        const startX = margin + (usableW - gridW) / 2;
        const startY = margin;

        const surfObs = obstacles.filter((o) => o.surfaceId === surfId);

        for (let r = 0; r < rows; r++) {
          for (let c = 0; c < cols; c++) {
            const cxMeters = startX + c * (mW + gap) + mW / 2;
            const cyMeters = startY + r * (mH + gap) + mH / 2;
            const cx = cxMeters / width;
            const cy = cyMeters / height;

            let valid = true;

            // trapezoid restriction
            if (type === "trapezoid" && wTop !== undefined && wBot !== undefined) {
              const prog = cy;
              const availW = wTop + (wBot - wTop) * prog;
              const centerAxis = width / 2;
              const dist = Math.abs(cxMeters - centerAxis);
              if ((dist + mW / 2) > (availW / 2 - margin)) valid = false;
            }

            // obstacle overlap
            if (valid) {
              for (const o of surfObs) {
                const ox = o.x * width;
                const oy = o.y * height;
                if (
                  (cxMeters - mW / 2 < ox + o.width / 2 + 0.1) &&
                  (cxMeters + mW / 2 > ox - o.width / 2 - 0.1) &&
                  (cyMeters - mH / 2 < oy + o.height / 2 + 0.1) &&
                  (cyMeters + mH / 2 > oy - o.height / 2 - 0.1)
                ) { valid = false; break; }
              }
            }

            if (valid) {
              this.addModuleMesh({
                id: crypto.randomUUID(),
                surfaceId: surfId,
                x: cx,
                y: cy,
                row: r,
                col: c,
                selected: false,
                orientation,
              });
            }
          }
        }
      });

      this.notifyModuleUpdate();
    }
  }

  // =========================================================
  // 6) STATE
  // =========================================================
  const state = {
    step: 0,
    view: "construct",
    tool: "select",
    roofImg: "",

    build: {
      category: "pitched",
      shape: "sattel",
      length: 10, width: 8, height: 5,
      pitch: 35,
      attika: 0.3,
      overhang: 0.5,
      overhangGable: 0.3,
      lengthB: 4, widthB: 4,
      layerSpread: 0,
      rafterSpacing: 70,
      rafterWidth: 8,
      rafterHeight: 16,
      battenDist: 35,
    },

    roofVisibility: { tiles: true, truss: true, layers: true, pv: true, obstacles: true, opacity: 1 },
    snap: { enabled: false, step: 0.02 }, // UV step (2% default)

    sun: {
      lat: 52.52,
      lon: 13.405,
      tzOffset: -new Date().getTimezoneOffset() / 60,
      month: 6,
      day: 21,
      hour: 13.0,
      intensity: 1.25,
      showHelper: false,
    },
    house: { yaw: 0 },

    customer: { name: "", address: "", city: "", zip: "", consumption: 4500, priceKwh: 0.4 },

    electrical: { meterCabinet: "existing", evuRegistration: true, batterySize: 5 },

    // Heating / Heizlast (very simplified estimate; replace later with DIN/EN logic)
    heating: {
      areaM2: 140,
      ceilingH: 2.5,
      buildingYear: 1995,
      insulationLevel: "mittel", // schlecht|mittel|gut|kfw
      tInside: 20,
      tOutside: -10,
      ventilationACH: 0.5,
      hwFactor: 1.1, // hot water / reserve factor
    },

    cover: "ziegel",
    activeSurface: "south",

    surfaceConfigs: {
      default: { enabled: true, orientation: "portrait", gap: 2, margin: 40 },
      south: { enabled: true, orientation: "portrait", gap: 2, margin: 40 },
      north: { enabled: true, orientation: "portrait", gap: 2, margin: 40 },
      east: { enabled: true, orientation: "portrait", gap: 2, margin: 40 },
      west: { enabled: true, orientation: "portrait", gap: 2, margin: 40 },
      main: { enabled: true, orientation: "portrait", gap: 2, margin: 40 },
    },

    obstacles: [],
    modules: [],

    selectedInverter: "",
    selectedModuleIndex: 2,
  };

  // =========================================================
  // 7) CALCULATIONS (PV + BOM + Time + Heizlast)
  // =========================================================
  function computeHeizlast(heating) {
    // Very simplified model:
    // Transmission: q = A * Ueq * dT
    // Ventilation: q = 0.33 * n * V * dT (kW when /1000)
    // Choose Ueq based on insulation level + year
    const A = Math.max(10, Number(heating.areaM2 || 0));
    const h = Math.max(2, Number(heating.ceilingH || 2.5));
    const V = A * h;

    const dT = Math.max(5, (Number(heating.tInside || 20) - Number(heating.tOutside || -10)));

    const mapU = {
      schlecht: 1.4,
      mittel: 1.0,
      gut: 0.75,
      kfw: 0.55,
    };
    let Ueq = mapU[heating.insulationLevel] ?? 1.0;

    // year correction
    const y = Number(heating.buildingYear || 1995);
    if (y < 1978) Ueq *= 1.15;
    else if (y < 1995) Ueq *= 1.05;
    else if (y > 2016) Ueq *= 0.85;

    // envelope area approx (walls + roof) from footprint area:
    const envelopeFactor = 2.6; // coarse factor
    const Aenv = A * envelopeFactor;

    const qTransW = Aenv * Ueq * dT;
    const ach = clamp(Number(heating.ventilationACH ?? 0.5), 0.1, 2.0);
    const qVentW = 0.33 * ach * V * dT * 1000; // 0.33 Wh/m3K => W with *1000? keep consistent: use 0.33*ach*V*dT (W) actually
    // Correct: 0.33 * ach * V * dT gives W (since 0.33 = Wh/m3K; per hour => W)
    const qVentW2 = 0.33 * ach * V * dT;

    const qTotalW = qTransW + qVentW2;
    const hw = clamp(Number(heating.hwFactor ?? 1.1), 1.0, 1.3);
    const qWithReserveW = qTotalW * hw;

    return {
      dT,
      Ueq,
      Aenv,
      qTransKW: qTransW / 1000,
      qVentKW: qVentW2 / 1000,
      heizlastKW: qWithReserveW / 1000,
    };
  }

  function computePV() {
    const activeModule = MODULE_TYPES[state.selectedModuleIndex];
    const count = state.modules.length;

    const kwp = (count * activeModule.watts) / 1000;
    const annualYield = kwp * 950;

    let selfConsumptionRate = 0.3;
    if (state.electrical.batterySize > 0) {
      const storageRatio = state.electrical.batterySize / Math.max(1, annualYield);
      selfConsumptionRate = Math.min(0.75, 0.3 + storageRatio * 4);
    }
    const selfConsumption = Math.min(annualYield * selfConsumptionRate, state.customer.consumption);
    const gridFeedIn = Math.max(0, annualYield - selfConsumption);
    const autarkyRate = state.customer.consumption > 0 ? (selfConsumption / state.customer.consumption) * 100 : 0;

    // choose inverter (simple: first that fits)
    const inv = INVERTERS.find((i) => i.model === state.selectedInverter) || INVERTERS.find((i) => i.power >= kwp * 1000) || INVERTERS[1];

    return { activeModule, count, kwp, annualYield, selfConsumption, gridFeedIn, autarkyRate, inverter: inv };
  }

  function computeBOMAndTime() {
    const pv = computePV();
    const sys = FASTENER_MAPPING[state.cover];
    const count = pv.count;

    // Strings (very rough): assume 12 modules per string
    const perString = 12;
    const strings = Math.max(1, Math.ceil(count / perString));

    // Approx rows: count / columns ~ use orientation and roof width info if available
    const rows = Math.max(1, Math.round(Math.sqrt(count)));

    // BOM
    const bom = {};

    bom["PV Modul"] = count;
    bom["Wechselrichter"] = 1;

    if (state.electrical.batterySize > 0) bom["Batteriespeicher (kWh)"] = state.electrical.batterySize;

    // Mounting system by roof cover
    if (sys.type === "hook") {
      bom[sys.name] = Math.round(count * BOM_DEFAULTS.roofHooksPerModule);
      bom["Dachschrauben (Edelstahl)"] = bom[sys.name] * BOM_DEFAULTS.screwsPerHook;
      const railM = count * BOM_DEFAULTS.railsMPerModulePerRow * 2; // 2 rail lines
      bom["Montageschiene (m)"] = Math.round(railM * 10) / 10;
      bom["Klemmen (Stk)"] = count * BOM_DEFAULTS.clampsPerModule;
    } else if (sys.type === "short_rail") {
      bom[sys.name] = count * 4;
      bom["Blechschrauben (Stk)"] = count * BOM_DEFAULTS.roofSheetScrewsPerModule;
      bom["Klemmen (Stk)"] = count * BOM_DEFAULTS.clampsPerModule;
    } else if (sys.type === "flat_system") {
      bom[sys.name] = Math.ceil(count / 2);
      bom["Ballast (kg)"] = count * BOM_DEFAULTS.ballastKgPerModule;
      bom["Klemmen (Stk)"] = count * BOM_DEFAULTS.clampsPerModule;
    }

    bom["DC Solarkabel (m)"] = Math.round(count * BOM_DEFAULTS.dcCablePerModuleM * 10) / 10;
    bom["MC4 Stecker-Paare"] = strings * BOM_DEFAULTS.mc4PairsPerString;
    bom["Potentialausgleich / Erdung (m)"] = BOM_DEFAULTS.groundingM;
    bom["Beschriftung / Labels (Set)"] = BOM_DEFAULTS.labelsSet;
    bom["Überspannungsschutz DC (Stk)"] = BOM_DEFAULTS.surgeDC;
    bom["Überspannungsschutz AC (Stk)"] = BOM_DEFAULTS.surgeAC;
    bom["Datenkabel / Netzwerk (m)"] = BOM_DEFAULTS.dataCableM;

    // Cost estimation (materials only from your constants; others simple)
    const moduleCost = count * pv.activeModule.price;
    const invCost = pv.inverter?.price || 1500;

    let mountCost = 0;
    if (sys.type === "hook") mountCost += (bom[sys.name] || 0) * sys.price;
    if (sys.type === "short_rail") mountCost += (bom[sys.name] || 0) * sys.price;
    if (sys.type === "flat_system") mountCost += (bom[sys.name] || 0) * sys.price;

    // Electrical
    const electricalCost =
      (state.electrical.meterCabinet === "new" ? 2500 : state.electrical.meterCabinet === "upgrade" ? 900 : 0) +
      (state.electrical.evuRegistration ? 250 : 0);

    const batteryCost = state.electrical.batterySize * 600;

    // Time estimation
    const facadeAreaApprox = (state.build.length + state.build.width) * 2 * state.build.height;
    const scaffoldTime = Math.ceil(facadeAreaApprox * TIME_VARS.SCAFFOLD_M2);

    const totalRailMeters = (bom["Montageschiene (m)"] || 0);
    const hooksCount = (sys.type === "hook") ? (bom[sys.name] || 0) : (sys.type === "short_rail" ? (bom[sys.name] || 0) : 0);

    const installMinutes =
      scaffoldTime +
      TIME_VARS.MEASURE +
      (hooksCount * (sys.timePerUnit || 0)) +
      (totalRailMeters * TIME_VARS.RAIL_M) +
      (count * TIME_VARS.MOD_MOUNT) +
      ((bom["DC Solarkabel (m)"] || 0) * TIME_VARS.CABLE_M) +
      TIME_VARS.INV_SETUP +
      TIME_VARS.CLEANUP;

    // ─────────────────────────────────────────────────────────────────────────────────────────
    // 🔴 F-051 GESPERRT — DER STUNDENSATZ 65 IST EIN EIGENER POSTEN, NICHT EINE ZEITANNAHME.
    //
    // Die elf Werte oben sind Minuten; die 65 ist ein PREIS in Euro je Stunde. Sie ist damit
    // NICHT von derselben Art wie TIME_VARS und wird hier ausdruecklich getrennt vermerkt.
    //
    // WAS FEHLT, dieselben drei Fragen: Quelle · Datum der Erhebung · Gewerk.
    // Und ausdruecklich OHNE Vorschlagswert — ein Vorschlag waere genau die falsche Zahl,
    // die Yamas Auflage verbietet.
    //
    // WAS DIESE ZEILE RECHNET: aus unbelegten Minuten und einem unbelegten Stundensatz einen
    // Lohnkostenbetrag. Beide Faktoren sind Platzhalter — das Ergebnis ist es damit auch.
    //
    // NICHT AENDERN ohne Yamas Firmenwerte. Auftrag: docs/auftraege/aktiv/A-16-time-vars-im-produktivcode.md
    // ─────────────────────────────────────────────────────────────────────────────────────────
    const laborCost = (installMinutes / 60) * 65;
    const misc = 500;

    const totalInvest = moduleCost + invCost + mountCost + electricalCost + batteryCost + laborCost + misc;

    return {
      bom,
      costs: { moduleCost, invCost, mountCost, electricalCost, batteryCost, laborCost, misc, totalInvest },
      time: { scaffoldTime, installMinutes },
      strings,
      rows,
    };
  }

  function computeAll() {
    const pv = computePV();
    const bom = computeBOMAndTime();
    const heat = computeHeizlast(state.heating);

    const yearlyBenefit = (pv.selfConsumption * state.customer.priceKwh) + (pv.gridFeedIn * 0.08);
    const payback = yearlyBenefit > 0 ? bom.costs.totalInvest / yearlyBenefit : Infinity;

    return { pv, bom, heat, yearlyBenefit, payback };
  }

  // =========================================================
  // 8) ENGINE LIFECYCLE
  // =========================================================
  let engine = null;
  let ro = null;

  function syncEngineModules() {
    if (!engine) return;
    engine.clearModules(false);
    (state.modules || []).forEach((m) => engine.addModuleMesh({ ...m }));
  }

  function ensureEngine() {
    const canvas = $("#threeCanvas");
    const wrap = $("#threeWrap");
    if (!canvas || !wrap) return;

    if (engine && engine.canvas !== canvas) {
      try { engine.destroy(); } catch (_) {}
      engine = null;
    }
    if (engine) return;

    engine = new SolarEngine(canvas);
    engine.activeTool = state.tool;

    engine.onObstacleMove = (id, surfId, x, y) => {
      state.obstacles = state.obstacles.map((o) => (o.id === id ? { ...o, surfaceId: surfId, x, y } : o));
      syncEngineObstacles();
      scheduleUIUpdate();
    };

    engine.onModuleUpdate = (mods) => {
      state.modules = [...mods];
      updateMiniStats();
      scheduleUIUpdate();
    };

    ro = new ResizeObserver(rafThrottle(() => {
      const r = wrap.getBoundingClientRect();
      engine.resize(r.width, r.height);
    }));
    ro.observe(wrap);

    syncEngineAll();
  }

  function syncEngineAll() {
    if (!engine) return;
    engine.updateBuilding(state.build, state.cover);
    engine.setRoofVisibility(state.roofVisibility);
    engine.setHouseYaw(state.house.yaw);
    engine.setSun(state.sun);
    engine.setSnap(state.snap.enabled ? state.snap.step : 0);
    engine.updateObstacles(state.obstacles);
    syncEngineModules();
    updateMiniStats();
  }

  function syncEngineObstacles() {
    if (!engine) return;
    engine.updateObstacles(state.obstacles);
  }

  function clearModules() {
    if (!engine) return;
    engine.clearModules(true);
    state.modules = [];
    updateMiniStats();
  }

  function runAutoLayout() {
    if (!engine) return;
    engine.autoLayout(state.surfaceConfigs, state.obstacles);
  }

  function updateMiniStats() {
    const all = computeAll();
    const elCount = $("#miniModuleCount");
    const elKwp = $("#miniKwp");
    const elHeat = $("#miniHeat");
    const elTime = $("#miniTime");

    if (elCount) elCount.textContent = `${state.modules.length} Module`;
    if (elKwp) elKwp.textContent = `${all.pv.kwp.toFixed(2)} kWp`;
    if (elHeat) elHeat.textContent = `${all.heat.heizlastKW.toFixed(1)} kW Heizlast`;
    if (elTime) elTime.textContent = `${formatHours(all.bom.time.installMinutes)}`;
  }

  // =========================================================
  // 9) UI RENDER
  // =========================================================
  const app = $("#app");
  const scheduleUIUpdate = rafThrottle(() => render());

  function WizardNav() {
    const backDisabled = state.step === 0 ? "disabled" : "";
    const backOpacity = state.step === 0 ? "disabled:opacity-50" : "";

    const nextBtn =
      state.step < 2
        ? `<button data-action="next" class="px-6 py-2 rounded-lg flex items-center gap-2 text-sm font-bold bg-[#5FA5C8] text-white hover:bg-[#4F94B7] shadow-sm transition-colors">
            Weiter <i data-lucide="chevron-right" class="w-4 h-4"></i>
          </button>`
        : (state.step === 2
          ? `<button data-action="offer" class="px-6 py-2 rounded-lg flex items-center gap-2 text-sm font-bold bg-[#5FA5C8] text-white hover:bg-[#4F94B7] shadow-sm transition-colors">
              <i data-lucide="printer" class="w-4 h-4"></i> Angebot erstellen
            </button>`
          : ``);

    return `
      <div class="flex justify-between mt-8 pt-4 border-t border-slate-200">
        <button data-action="back" ${backDisabled} class="px-6 py-2 rounded-lg flex items-center gap-2 text-sm font-bold bg-white text-slate-600 border border-slate-300 ${backOpacity} hover:bg-slate-50 transition-colors">
          <i data-lucide="chevron-left" class="w-4 h-4"></i> Zurück
        </button>
        ${nextBtn}
      </div>
    `;
  }

  function renderHeader() {
    const dots = [0, 1, 2].map((i) => {
      const cls = i <= state.step ? "bg-[#5FA5C8]" : "bg-slate-200";
      return `<div class="h-2 w-12 rounded-full transition-colors ${cls}"></div>`;
    }).join("");

    return `
      <header class="h-16 flex items-center justify-between px-8 bg-white border-b border-slate-200 shrink-0 shadow-sm">
        <div class="flex items-center gap-3">
          <i data-lucide="sun" class="text-[#5FA5C8] w-6 h-6"></i>
          <div class="font-bold text-lg text-slate-800">SOLAR WIZARD</div>
        </div>
        <div class="flex gap-2">${dots}</div>
        <div class="text-sm font-medium text-slate-400 bg-slate-100 px-3 py-1 rounded-full">
          Schritt ${state.step + 1} / 3
        </div>
      </header>
    `;
  }

  function renderStep0() {
    const c = state.customer;
    const h = state.heating;

    return `
      <div class="w-full max-w-3xl mx-auto p-12 overflow-y-auto animate-in fade-in slide-in-from-bottom-4 custom-scrollbar">
        <h1 class="text-3xl font-bold mb-8 text-slate-800">Kundendaten & Heizlast</h1>

        <div class="space-y-6 bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
          <div class="grid grid-cols-2 gap-4">
            <label class="block">
              <span class="text-sm font-bold text-slate-500 uppercase">Voller Name</span>
              <input data-field="customer.name" type="text"
                class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                value="${escapeAttr(c.name)}" placeholder="Max Mustermann"/>
            </label>
            <label class="block">
              <span class="text-sm font-bold text-slate-500 uppercase">Jahresverbrauch (kWh)</span>
              <input data-field="customer.consumption" type="number"
                class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                value="${Number.isFinite(c.consumption) ? c.consumption : ""}"/>
            </label>
          </div>

          <label class="block">
            <span class="text-sm font-bold text-slate-500 uppercase">Straße & Hausnummer</span>
            <input data-field="customer.address" type="text"
              class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
              value="${escapeAttr(c.address)}" placeholder="Musterstraße 1"/>
          </label>

          <div class="grid grid-cols-2 gap-4">
            <label class="block">
              <span class="text-sm font-bold text-slate-500 uppercase">PLZ</span>
              <input data-field="customer.zip" type="text"
                class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                value="${escapeAttr(c.zip)}" placeholder="12345"/>
            </label>
            <label class="block">
              <span class="text-sm font-bold text-slate-500 uppercase">Stadt</span>
              <input data-field="customer.city" type="text"
                class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                value="${escapeAttr(c.city)}" placeholder="Berlin"/>
            </label>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <label class="block">
              <span class="text-sm font-bold text-slate-500 uppercase">Strompreis (€/kWh)</span>
              <input data-field="customer.priceKwh" type="number" step="0.01"
                class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                value="${Number.isFinite(c.priceKwh) ? c.priceKwh : ""}"/>
            </label>

            <label class="block">
              <span class="text-sm font-bold text-slate-500 uppercase">Gebäudejahr</span>
              <input data-field="heating.buildingYear" type="number"
                class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                value="${Number.isFinite(h.buildingYear) ? h.buildingYear : ""}"/>
            </label>
          </div>

          <div class="pt-4 border-t border-slate-100">
            <div class="grid grid-cols-3 gap-4">
              <label class="block">
                <span class="text-sm font-bold text-slate-500 uppercase">Wohnfläche (m²)</span>
                <input data-field="heating.areaM2" type="number"
                  class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                  value="${Number.isFinite(h.areaM2) ? h.areaM2 : ""}"/>
              </label>
              <label class="block">
                <span class="text-sm font-bold text-slate-500 uppercase">Raumhöhe (m)</span>
                <input data-field="heating.ceilingH" type="number" step="0.1"
                  class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                  value="${Number.isFinite(h.ceilingH) ? h.ceilingH : ""}"/>
              </label>
              <label class="block">
                <span class="text-sm font-bold text-slate-500 uppercase">Lüftung (ACH)</span>
                <input data-field="heating.ventilationACH" type="number" step="0.1"
                  class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                  value="${Number.isFinite(h.ventilationACH) ? h.ventilationACH : ""}"/>
              </label>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-4">
              <label class="block">
                <span class="text-sm font-bold text-slate-500 uppercase">Innen (°C)</span>
                <input data-field="heating.tInside" type="number"
                  class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                  value="${Number.isFinite(h.tInside) ? h.tInside : ""}"/>
              </label>
              <label class="block">
                <span class="text-sm font-bold text-slate-500 uppercase">Außen (°C)</span>
                <input data-field="heating.tOutside" type="number"
                  class="mt-1 w-full bg-slate-50 border border-slate-200 focus:border-[#5FA5C8] focus:ring-0 rounded p-3 text-slate-800"
                  value="${Number.isFinite(h.tOutside) ? h.tOutside : ""}"/>
              </label>
              <label class="block">
                <span class="text-sm font-bold text-slate-500 uppercase">Dämmung</span>
                <select data-select="insulationLevel" class="mt-1 w-full bg-slate-50 border border-slate-200 rounded p-3 text-slate-800">
                  ${["schlecht","mittel","gut","kfw"].map((k)=>`<option value="${k}" ${h.insulationLevel===k?"selected":""}>${k.toUpperCase()}</option>`).join("")}
                </select>
              </label>
            </div>

            <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
              <div class="font-bold text-slate-700 mb-1">Heizlast (Schnellschätzung)</div>
              ${(() => {
                const r = computeHeizlast(state.heating);
                return `
                  <div class="flex justify-between"><span>ΔT</span><span class="font-mono">${r.dT.toFixed(0)} K</span></div>
                  <div class="flex justify-between"><span>Transmission</span><span class="font-mono">${r.qTransKW.toFixed(1)} kW</span></div>
                  <div class="flex justify-between"><span>Lüftung</span><span class="font-mono">${r.qVentKW.toFixed(1)} kW</span></div>
                  <div class="flex justify-between font-bold pt-2 border-t border-slate-200 mt-2">
                    <span>Gesamt (inkl. Reserve)</span><span class="font-mono text-[#5FA5C8]">${r.heizlastKW.toFixed(1)} kW</span>
                  </div>
                `;
              })()}
              <div class="text-[11px] text-slate-500 mt-2">Hinweis: Diese Heizlast ist eine grobe Schätzung. Für DIN EN 12831 brauchst du Bauteil-U-Werte, Fensterflächen, Wärmebrücken etc.</div>
            </div>
          </div>
        </div>

        ${WizardNav()}
      </div>
    `;
  }

  function renderLeftSidebarStep1() {
    const b = state.build;
    const isPitched = b.category === "pitched";

    const moduleOptions = MODULE_TYPES.map((m, idx) =>
      `<option value="${idx}" ${state.selectedModuleIndex === idx ? "selected" : ""}>${escapeHtml(m.name)}</option>`
    ).join("");

    const inverterOptions = INVERTERS.map((i) =>
      `<option value="${escapeAttr(i.model)}" ${state.selectedInverter === i.model ? "selected" : ""}>${escapeHtml(i.model)}</option>`
    ).join("");

    const nav = `
      <div class="flex p-4 gap-2 border-b border-slate-100 text-[10px] font-bold uppercase overflow-x-auto bg-[#F0F4F8]">
        ${NavBtn("construct", "home", "Haus", state.view)}
        ${NavBtn("visual", "eye", "Visual", state.view)}
        ${NavBtn("obstacles", "box", "Objekte", state.view)}
        ${NavBtn("modules", "layout-template", "PV", state.view)}
        ${NavBtn("sun", "sun", "Sonne", state.view)}
        ${NavBtn("electrical", "plug-zap", "Elektrik", state.view)}
      </div>
    `;

    const construct = `
      <div class="space-y-6" ${state.view === "construct" ? "" : 'style="display:none"'}>
        <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl">
          ${ModeBtn("pitched", isPitched, "arrow-up-right", "Schräg")}
          ${ModeBtn("flat", !isPitched, "layers", "Flach")}
        </div>

        <div class="space-y-4">
          ${Range("length", "Länge (Traufe)", b.length, 5, 25, "m")}
          ${Range("width", "Breite (Giebel)", b.width, 4, 15, "m")}
          ${Range("height", "Traufhöhe", b.height, 3, 10, "m")}
          ${isPitched ? Range("pitch", "Dachneigung", b.pitch, 10, 60, "°") : ""}
          ${isPitched ? Range("overhang", "Überstand (Traufe)", b.overhang, 0, 1.5, "m") : ""}
          ${isPitched ? Range("overhangGable", "Überstand (Ortgang)", b.overhangGable, 0, 1.5, "m") : ""}
        </div>

        <div class="pt-4 border-t border-slate-100">
          ${Label("Eindeckung")}
          <select data-select="cover" class="w-full bg-slate-50 border border-slate-200 rounded p-2 text-sm mt-1 text-slate-700">
            <option value="ziegel" ${state.cover === "ziegel" ? "selected" : ""}>Ziegel (Rot)</option>
            <option value="schiefer" ${state.cover === "schiefer" ? "selected" : ""}>Schiefer</option>
            <option value="trapezblech" ${state.cover === "trapezblech" ? "selected" : ""}>Trapezblech</option>
            <option value="bitumen" ${state.cover === "bitumen" ? "selected" : ""}>Bitumen</option>
          </select>
        </div>
      </div>
    `;

    const visual = `
      <div class="space-y-4" ${state.view === "visual" ? "" : 'style="display:none"'}>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
          ${Label("Roof Visibility")}
          <div class="grid grid-cols-2 gap-2">
            <button data-toggle="tiles" class="px-3 py-2 rounded-lg border ${state.roofVisibility.tiles ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"} text-xs font-bold">Tiles</button>
            <button data-toggle="truss" class="px-3 py-2 rounded-lg border ${state.roofVisibility.truss ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"} text-xs font-bold">Truss</button>
            <button data-toggle="layers" class="px-3 py-2 rounded-lg border ${state.roofVisibility.layers ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"} text-xs font-bold">Layers</button>
            <button data-toggle="pv" class="px-3 py-2 rounded-lg border ${state.roofVisibility.pv ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"} text-xs font-bold">PV</button>
            <button data-toggle="obstacles" class="px-3 py-2 rounded-lg border ${state.roofVisibility.obstacles ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"} text-xs font-bold">Obstacles</button>
            <button data-toggle="sunHelper" class="px-3 py-2 rounded-lg border ${state.sun.showHelper ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"} text-xs font-bold">Sun Helper</button>
          </div>

          <div class="mt-4">
            ${Range("roofOpacity", "Roof Opacity", state.roofVisibility.opacity, 0, 1, "", 0.05)}
          </div>

          <div class="mt-4">
            ${Label("Snap Drag")}
            <div class="flex items-center gap-2">
              <button data-toggle="snap" class="px-3 py-2 rounded-lg border ${state.snap.enabled ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"} text-xs font-bold">Snap</button>
              <div class="flex-1">${Range("snapStep", "Snap Step", state.snap.step, 0.005, 0.1, "", 0.005)}</div>
            </div>
          </div>

          <div class="mt-4">
            ${Range("houseYaw", "House Direction (Yaw)", state.house.yaw, -180, 180, "°")}
          </div>
        </div>
      </div>
    `;

    const obstacles = `
      <div class="space-y-4" ${state.view === "obstacles" ? "" : 'style="display:none"'}>
        ${Label("Dachseite")}
        <div class="flex gap-1 overflow-x-auto pb-2">
          ${["south", "north", "east", "west", "main"].map((sId) => `
            <button data-surface="${sId}" class="px-3 py-1 text-xs font-bold rounded-xl border ${
              state.activeSurface === sId ? "bg-[#5FA5C8] text-white border-[#5FA5C8]" : "bg-white text-slate-600 border-slate-200 hover:bg-slate-50"
            }">${escapeHtml(sId)}</button>
          `).join("")}
        </div>

        <div class="grid grid-cols-2 gap-3">
          ${ObsBtn("chimney", "flame", "Kamin", "text-orange-500")}
          ${ObsBtn("window", "maximize-2", "Fenster", "text-blue-500")}
          ${ObsBtn("vent", "wind", "Lüfter", "text-slate-500")}
          ${ObsBtn("sat", "disc", "Sat", "text-green-500")}
          ${ObsBtn("tree", "tree-pine", "Baum", "text-emerald-600")}
        </div>

        <div class="mt-3 space-y-2">
          ${(state.obstacles || []).map((o, i) => `
            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50">
              <div class="text-xs font-bold text-slate-700">#${i + 1} ${escapeHtml(o.type)} <span class="text-slate-400">(${escapeHtml(o.surfaceId)})</span></div>
              <button data-del-obs="${o.id}" class="px-2 py-1 text-xs font-bold rounded-lg bg-white border border-slate-200 text-red-600 hover:bg-red-50">Löschen</button>
            </div>
          `).join("") || `<div class="text-xs text-slate-400">Noch keine Objekte.</div>`}
        </div>

        <div class="text-xs text-slate-400 bg-slate-50 p-3 rounded-lg text-center">
          Tipp: Objekt anklicken und ziehen. Tool “move” für Module.
        </div>
      </div>
    `;

    const modulesPanel = `
      <div class="space-y-4" ${state.view === "modules" ? "" : 'style="display:none"'}>
        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
          ${Label("Modul-Typ")}
          <select data-select="moduleType" class="w-full bg-transparent text-sm mt-1 outline-none text-slate-700 font-medium">
            ${moduleOptions}
          </select>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
          ${Label("Wechselrichter")}
          <select data-select="inverter" class="w-full bg-transparent text-sm mt-1 outline-none text-slate-700 font-medium">
            <option value="">Auto</option>
            ${inverterOptions}
          </select>
        </div>

        <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
          ${ToolBtn("select", "mouse-pointer-2", "Wählen", state.tool)}
          ${ToolBtn("move", "move", "Schieben", state.tool)}
          ${ToolBtn("duplicate", "copy", "Kopieren", state.tool)}
          ${ToolBtn("delete", "trash-2", "Löschen", state.tool)}
        </div>

        <div class="grid grid-cols-2 gap-2">
          <button data-action="layout" class="w-full py-3 bg-[#E9F3E8] hover:bg-[#dceddb] text-[#6bbf48] border border-[#6bbf48]/20 font-bold rounded-xl flex items-center justify-center gap-2 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Auto-Belegung
          </button>
          <button data-action="clearModules" class="w-full py-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold rounded-xl flex items-center justify-center gap-2 transition-colors">
            <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
          </button>
        </div>

        <div class="text-xs text-slate-600">
          Module: <b>${state.modules.length}</b>
        </div>
      </div>
    `;

    const sunPanel = `
      <div class="space-y-4" ${state.view === "sun" ? "" : 'style="display:none"'}>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
          ${Label("Sonne / Schatten")}
          ${Range("sunHour", "Uhrzeit", state.sun.hour, 0, 24, " h", 0.25)}
          ${Range("sunMonth", "Monat", state.sun.month, 1, 12, "", 1)}
          ${Range("sunDay", "Tag", state.sun.day, 1, 31, "", 1)}
          ${Range("sunIntensity", "Intensity", state.sun.intensity, 0.2, 2.5, "", 0.05)}
        </div>
      </div>
    `;

    const e = state.electrical;
    const electPanel = `
      <div class="space-y-4" ${state.view === "electrical" ? "" : 'style="display:none"'}>
        ${Label("Zählerschrank")}
        <div class="flex flex-col gap-2">
          <button data-meter="existing" class="p-3 rounded-xl border text-left transition-all ${e.meterCabinet === "existing" ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"}">
            <div class="font-bold text-sm">Bestand okay</div>
          </button>
          <button data-meter="upgrade" class="p-3 rounded-xl border text-left transition-all ${e.meterCabinet === "upgrade" ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"}">
            <div class="font-bold text-sm">Upgrade (+900€)</div>
          </button>
          <button data-meter="new" class="p-3 rounded-xl border text-left transition-all ${e.meterCabinet === "new" ? "bg-[#E9F3E8] border-[#6bbf48] text-[#4d8a33]" : "bg-white border-slate-200 text-slate-500"}">
            <div class="font-bold text-sm">Komplett Neu (+2.500€)</div>
          </button>
        </div>

        ${Label("Batteriespeicher (kWh)")}
        ${Range("batterySize", "Speichergröße", e.batterySize, 0, 20, " kWh", 0.5)}
      </div>
    `;

    return `
      <aside class="w-[420px] bg-white border-r border-slate-200 flex flex-col z-10 shadow-xl">
        ${nav}
        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-white">
          ${construct}
          ${visual}
          ${obstacles}
          ${modulesPanel}
          ${sunPanel}
          ${electPanel}
        </div>
        <div class="p-4 border-t border-slate-200 bg-white">
          ${WizardNav()}
        </div>
      </aside>
    `;
  }

  function renderStep1() {
    const all = computeAll();
    const area = (engine?.geoData?.width || 0) * (engine?.geoData?.height || 0);

    return `
      <div class="flex w-full h-full">
        ${renderLeftSidebarStep1()}
        <main id="threeWrap" class="flex-1 bg-gradient-to-b from-[#e3ecf5] to-[#f0f4f8] relative">
          <canvas id="threeCanvas" class="w-full h-full block"></canvas>

          <div class="absolute top-4 right-4 bg-white/90 backdrop-blur text-slate-800 p-4 rounded-xl shadow-lg text-sm border border-slate-200 space-y-1">
            <div id="miniModuleCount" class="font-bold text-lg">${state.modules.length} Module</div>
            <div id="miniKwp" class="text-slate-500">${all.pv.kwp.toFixed(2)} kWp</div>
            <div id="miniHeat" class="text-slate-500">${all.heat.heizlastKW.toFixed(1)} kW Heizlast</div>
            <div id="miniTime" class="text-slate-500">Zeit: ${formatHours(all.bom.time.installMinutes)}</div>
          </div>

          <div class="absolute top-4 left-4 bg-white/80 backdrop-blur rounded-2xl border border-slate-200 shadow-sm p-3">
            <div class="text-[10px] font-bold text-slate-500 uppercase">Dachfläche</div>
            <div class="text-xl font-extrabold text-slate-800">${area.toFixed(0)} m²</div>
          </div>

          <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white/85 backdrop-blur rounded-2xl border border-slate-200 shadow-lg p-3 flex items-center gap-2 no-print">
            <button data-nudge="up" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold">↑</button>
            <button data-nudge="down" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold">↓</button>
            <button data-nudge="left" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold">←</button>
            <button data-nudge="right" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold">→</button>

            <div class="w-px h-8 bg-slate-200 mx-2"></div>

            <button data-nudge="front" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold">In Front</button>
            <button data-nudge="behind" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold">Behind</button>

            <div class="w-px h-8 bg-slate-200 mx-2"></div>

            <div class="text-xs text-slate-600 font-mono">WASD / Arrows</div>
          </div>

        </main>
      </div>
    `;
  }

  function renderStep2() {
    const all = computeAll();
    const pv = all.pv;
    const bom = all.bom;
    const heat = all.heat;

    const bomRows = Object.entries(bom.bom)
      .sort((a, b) => String(a[0]).localeCompare(String(b[0])))
      .map(([k, v]) => `
        <div class="flex justify-between text-sm border-b border-slate-100 py-2">
          <span class="text-slate-600">${escapeHtml(k)}</span>
          <span class="font-mono font-semibold">${escapeHtml(String(v))}</span>
        </div>
      `).join("");

    return `
      <div class="w-full max-w-5xl mx-auto p-8 overflow-y-auto animate-in fade-in slide-in-from-bottom-4 custom-scrollbar">
        <h1 class="text-3xl font-bold mb-8 text-slate-800">Zusammenfassung, Material & Heizlast</h1>

        <div class="grid grid-cols-3 gap-6 mb-8">
          <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-4xl font-black text-green-500 mb-2">${pv.autarkyRate.toFixed(0)}%</div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider">Autarkiegrad</div>
          </div>
          <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-4xl font-black text-[#5FA5C8] mb-2">${Number.isFinite(all.payback) ? all.payback.toFixed(1) : "∞"}</div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider">Amortisation (Jahre)</div>
          </div>
          <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm text-center">
            <div class="text-4xl font-black text-orange-500 mb-2">${heat.heizlastKW.toFixed(1)} kW</div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider">Heizlast (Schätzung)</div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-8">
          <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold mb-4 border-b border-slate-100 pb-2 text-slate-700">Zeit & Aufwand</h3>
            <div class="space-y-2 text-sm text-slate-600">
              <div class="flex justify-between"><span>Gerüst (Schätzung)</span> <span class="font-mono">${formatHours(bom.time.scaffoldTime)}</span></div>
              <div class="flex justify-between"><span>Gesamt Montage</span> <span class="font-mono">${formatHours(bom.time.installMinutes)}</span></div>
              <div class="text-[11px] text-slate-500 pt-2 border-t border-slate-100">Werte sind Richtwerte (min-basierte Heuristik). Passe TIME_VARS + BOM_DEFAULTS an.</div>
            </div>
          </div>

          <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold mb-4 border-b border-slate-100 pb-2 text-slate-700">Kostenübersicht (Indikativ)</h3>
            <div class="space-y-2 text-sm text-slate-600">
              <div class="flex justify-between"><span>Module</span> <span>${bom.costs.moduleCost.toFixed(2)} €</span></div>
              <div class="flex justify-between"><span>Wechselrichter</span> <span>${bom.costs.invCost.toFixed(2)} €</span></div>
              <div class="flex justify-between"><span>Montagematerial</span> <span>${bom.costs.mountCost.toFixed(2)} €</span></div>
              <div class="flex justify-between"><span>Elektrik/Speicher</span> <span>${(bom.costs.electricalCost + bom.costs.batteryCost).toFixed(2)} €</span></div>
              <div class="flex justify-between"><span>Montage (Arbeit)</span> <span>${bom.costs.laborCost.toFixed(2)} €</span></div>
              <div class="flex justify-between font-bold text-lg pt-4 border-t border-slate-100 mt-2">
                <span>Gesamt (Netto)</span> <span class="text-[#5FA5C8]">${bom.costs.totalInvest.toFixed(2)} €</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-8">
          <h3 class="font-bold mb-4 border-b border-slate-100 pb-2 text-slate-700">Materialliste (BOM)</h3>
          <div class="divide-y divide-slate-50">${bomRows || `<div class="text-slate-400 text-sm">Keine Daten</div>`}</div>
        </div>

        ${WizardNav()}
      </div>
    `;
  }

  function renderStep3() {
    // Keep your existing offer/print template if you want.
    // For simplicity, we reuse your step3 block structure with updated calculations.
    const all = computeAll();
    const calcs = all.pv;
    const bom = all.bom;
    const c = state.customer;
    const activeModule = calcs.activeModule;

    const offerNo = `2026-${Math.floor(Math.random() * 1000)}`;
    const today = new Date().toLocaleDateString();

    return `
      <div class="bg-gray-100 min-h-screen p-8 text-black font-sans flex flex-col items-center">
        <div class="mb-4 no-print flex gap-4">
          <button data-action="print" class="bg-[#5FA5C8] text-white px-4 py-2 rounded shadow flex gap-2 items-center">
            <i data-lucide="printer" class="w-4 h-4"></i> Drucken
          </button>
          <button data-action="backFromPrint" class="bg-white text-slate-700 border border-slate-300 px-4 py-2 rounded shadow">Zurück</button>
        </div>

        <div class="a4-page page-break flex flex-col justify-between">
          <div>
            <div class="flex justify-between items-center border-b-2 border-[#5FA5C8] pb-4 mb-12">
              <div class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="sun" class="text-[#5FA5C8] w-6 h-6"></i> SOLAR MASTER
              </div>
              <div class="text-right text-xs text-slate-500">
                Musterstraße 10, 10115 Berlin<br/>
                Tel: 030 1234567<br/>
                info@solarmaster.de
              </div>
            </div>

            <div class="text-sm mb-12">
              <p class="font-bold">${escapeHtml(c.name)}</p>
              <p>${escapeHtml(c.address)}</p>
              <p>${escapeHtml(c.zip)} ${escapeHtml(c.city)}</p>
            </div>

            <div class="text-right mb-8">
              <p class="font-bold text-lg">Angebot #${offerNo}</p>
              <p class="text-sm text-slate-500">Berlin, den ${today}</p>
            </div>

            <h1 class="text-xl font-bold mb-6">Ihr Angebot für eine Photovoltaikanlage (${calcs.kwp.toFixed(2)} kWp)</h1>

            <p class="text-sm leading-relaxed mb-4">
              Anlage: <strong>${state.modules.length} Module</strong> (${escapeHtml(activeModule.name)}), Wechselrichter: <strong>${escapeHtml(calcs.inverter?.model || "Auto")}</strong>.
            </p>

            <div class="text-sm leading-relaxed mb-4">
              <div><b>Jahresertrag:</b> ${calcs.annualYield.toFixed(0)} kWh</div>
              <div><b>Eigenverbrauch:</b> ${calcs.selfConsumption.toFixed(0)} kWh</div>
              <div><b>Autarkie:</b> ${calcs.autarkyRate.toFixed(1)}%</div>
              <div><b>Heizlast (Schätzung):</b> ${all.heat.heizlastKW.toFixed(1)} kW</div>
              <div><b>Montagezeit (Schätzung):</b> ${formatHours(bom.time.installMinutes)}</div>
            </div>
          </div>

          <div class="text-xs text-slate-400 text-center border-t pt-4">
            Solar Master GmbH • Amtsgericht Berlin • HRB 12345 • USt-ID: DE123456789
          </div>
        </div>

        <div class="a4-page flex flex-col">
          <div class="border-b pb-2 mb-4">
            <h2 class="text-lg font-bold text-slate-800">Material & Kosten</h2>
          </div>

          <div class="mb-6 h-64 w-full bg-slate-100 rounded overflow-hidden border border-slate-300">
            ${state.roofImg ? `<img src="${state.roofImg}" alt="Dachbelegung" class="w-full h-full object-cover" />` : ``}
          </div>

          <h3 class="font-bold text-sm mb-2">Kosten (Netto)</h3>
          <div class="text-sm text-slate-700 space-y-2 mb-6">
            <div class="flex justify-between"><span>Gesamt</span><span class="font-mono font-bold">${bom.costs.totalInvest.toFixed(2)} €</span></div>
          </div>

          <h3 class="font-bold text-sm mb-2">Materialliste (BOM)</h3>
          <div class="text-xs border border-slate-200 rounded-xl overflow-hidden mb-8">
            ${Object.entries(bom.bom).map(([k, v]) => `
              <div class="flex justify-between px-3 py-2 border-b border-slate-100">
                <span>${escapeHtml(k)}</span>
                <span class="font-mono">${escapeHtml(String(v))}</span>
              </div>
            `).join("")}
          </div>

          <div class="mt-auto text-xs text-slate-400 text-center">Seite 2 von 2</div>
        </div>
      </div>
    `;
  }

  function renderStep2or3() {
    return state.step === 2 ? renderStep2() : renderStep3();
  }

  function render() {
    if (!app) return;

    app.innerHTML = `
      <div class="h-screen w-screen flex flex-col overflow-hidden">
        ${renderHeader()}
        <div class="flex-1 overflow-hidden">
          ${
            state.step === 0 ? renderStep0() :
            state.step === 1 ? renderStep1() :
            renderStep2or3()
          }
        </div>
      </div>
    `;

    window.lucide?.createIcons?.();

    if (state.step === 1) {
      ensureEngine();
      if (engine) engine.activeTool = state.tool;
      updateMiniStats();
    }
  }

  // =========================================================
  // 10) EVENTS (delegated)
  // =========================================================
  const setStep = (n) => { state.step = clamp(n, 0, 3); render(); };

  const rebuildBuildingSmooth = debounce(() => {
    if (!engine) return;
    engine.updateBuilding(state.build, state.cover);
    engine.setRoofVisibility(state.roofVisibility);
    engine.setHouseYaw(state.house.yaw);
    engine.setSun(state.sun);
    engine.setSnap(state.snap.enabled ? state.snap.step : 0);
    engine.updateObstacles(state.obstacles);
    syncEngineModules();
    updateMiniStats();
  }, 90);

  const rebuildAllHard = () => {
    if (!engine) return;
    engine.updateBuilding(state.build, state.cover);
    engine.setRoofVisibility(state.roofVisibility);
    engine.setHouseYaw(state.house.yaw);
    engine.setSun(state.sun);
    engine.setSnap(state.snap.enabled ? state.snap.step : 0);
    engine.updateObstacles(state.obstacles);
    clearModules();
    updateMiniStats();
  };

  app.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    const action = btn.getAttribute("data-action");
    if (action === "back") return setStep(state.step - 1);
    if (action === "next") return setStep(state.step + 1);

    if (action === "offer") {
      if (engine) state.roofImg = engine.getSnapshot();
      return setStep(3);
    }
    if (action === "print") return window.print();
    if (action === "backFromPrint") return setStep(2);

    if (action === "layout") { runAutoLayout(); return; }
    if (action === "clearModules") { clearModules(); scheduleUIUpdate(); return; }

    const nav = btn.getAttribute("data-nav");
    if (nav) { state.view = nav; scheduleUIUpdate(); return; }

    const mode = btn.getAttribute("data-mode");
    if (mode) {
      state.build.category = mode;
      if (mode === "pitched" && !["sattel", "pult", "walm"].includes(state.build.shape)) state.build.shape = "sattel";
      if (mode === "flat" && !["rect", "l-shape"].includes(state.build.shape)) state.build.shape = "rect";
      rebuildAllHard();
      scheduleUIUpdate();
      return;
    }

    const n = btn.getAttribute("data-nudge");
      if (n && engine) {
        const base = state.snap.enabled ? state.snap.step : 0.01;
        const step = base;

        // mapping:
        // left/right => u-, u+
        // up/down => v-, v+  (vDown points “down” on surface)
        // front/behind => move along v as semantic layer control
        if (n === "left") engine.nudgeSelected(-step, 0);
        if (n === "right") engine.nudgeSelected(step, 0);
        if (n === "up") engine.nudgeSelected(0, -step);
        if (n === "down") engine.nudgeSelected(0, step);
        if (n === "front") engine.nudgeSelected(0, -step);
        if (n === "behind") engine.nudgeSelected(0, step);

        // stop nudge after a short moment so it settles + saves
        clearTimeout(window.__nudgeEndT);
        window.__nudgeEndT = setTimeout(() => engine.endNudge(), 120);

        scheduleUIUpdate();
        return;
      }


    const surf = btn.getAttribute("data-surface");
    if (surf) { state.activeSurface = surf; scheduleUIUpdate(); return; }

    const obs = btn.getAttribute("data-obs");
    if (obs) {
      const dim =
        obs === "chimney" ? { w: 0.6, h: 0.6, d: 1 } :
        obs === "window" ? { w: 0.8, h: 1.2, d: 1 } :
        obs === "sat" ? { w: 0.8, h: 0.8, d: 1 } :
        obs === "tree" ? { w: 1.2, h: 3.5, d: 1.2 } :
        { w: 0.2, h: 0.2, d: 1 };

      state.obstacles.push({
        id: crypto.randomUUID(),
        surfaceId: state.activeSurface || "south",
        type: obs,
        x: 0.5, y: 0.5,
        width: dim.w, height: dim.h, depth: dim.d,
      });

      syncEngineObstacles();
      scheduleUIUpdate();
      return;
    }

    const delObs = btn.getAttribute("data-del-obs");
    if (delObs) {
      state.obstacles = state.obstacles.filter((o) => o.id !== delObs);
      syncEngineObstacles();
      scheduleUIUpdate();
      return;
    }

    const tool = btn.getAttribute("data-tool");
    if (tool) {
      state.tool = tool;
      if (engine) engine.activeTool = tool;
      scheduleUIUpdate();
      return;
    }

    const meter = btn.getAttribute("data-meter");
    if (meter) { state.electrical.meterCabinet = meter; scheduleUIUpdate(); return; }

    const toggle = btn.getAttribute("data-toggle");
    if (toggle) {
      if (toggle === "sunHelper") state.sun.showHelper = !state.sun.showHelper;
      else if (toggle === "snap") state.snap.enabled = !state.snap.enabled;
      else state.roofVisibility[toggle] = !state.roofVisibility[toggle];

      if (engine) {
        engine.setRoofVisibility(state.roofVisibility);
        engine.setSun(state.sun);
        engine.setSnap(state.snap.enabled ? state.snap.step : 0);
      }
      scheduleUIUpdate();
      return;
    }
  });

  app.addEventListener("input", (e) => {
    const el = e.target;

    const field = el.getAttribute?.("data-field");
    if (field) {
      const [root, key] = field.split(".");
      const val = el.value;

      if (root === "customer") {
        if (key === "consumption" || key === "priceKwh") state.customer[key] = parseFloat(val || "0");
        else state.customer[key] = val;
      }

      if (root === "heating") {
        if (["areaM2", "ceilingH", "buildingYear", "tInside", "tOutside", "ventilationACH"].includes(key)) {
          state.heating[key] = parseFloat(val || "0");
        } else {
          state.heating[key] = val;
        }
      }

      updateMiniStats();
      scheduleUIUpdate();
      return;
    }

    if (el.matches?.('select[data-select="insulationLevel"]')) {
      state.heating.insulationLevel = el.value;
      updateMiniStats();
      scheduleUIUpdate();
      return;
    }

    if (el.matches?.('select[data-select="cover"]')) {
      state.cover = el.value;
      rebuildAllHard();
      scheduleUIUpdate();
      return;
    }

    if (el.matches?.('select[data-select="moduleType"]')) {
      state.selectedModuleIndex = parseInt(el.value, 10) || 0;
      updateMiniStats();
      scheduleUIUpdate();
      return;
    }

    if (el.matches?.('select[data-select="inverter"]')) {
      state.selectedInverter = el.value;
      updateMiniStats();
      scheduleUIUpdate();
      return;
    }

    if (el.matches?.("input[data-range]")) {
      const key = el.getAttribute("data-range");
      const v = parseFloat(el.value);

      if (key === "batterySize") {
        state.electrical.batterySize = v;
        updateMiniStats();
        scheduleUIUpdate();
        return;
      }

      if (key === "houseYaw") {
        state.house.yaw = v;
        if (engine) engine.setHouseYaw(v);
        scheduleUIUpdate();
        return;
      }

      if (key === "roofOpacity") {
        state.roofVisibility.opacity = v;
        if (engine) engine.setRoofVisibility(state.roofVisibility);
        scheduleUIUpdate();
        return;
      }

      if (key === "snapStep") {
        state.snap.step = v;
        if (engine) engine.setSnap(state.snap.enabled ? state.snap.step : 0);
        scheduleUIUpdate();
        return;
      }

      if (key === "sunHour") { state.sun.hour = v; if (engine) engine.setSun(state.sun); scheduleUIUpdate(); return; }
      if (key === "sunMonth") { state.sun.month = Math.round(v); if (engine) engine.setSun(state.sun); scheduleUIUpdate(); return; }
      if (key === "sunDay") { state.sun.day = Math.round(v); if (engine) engine.setSun(state.sun); scheduleUIUpdate(); return; }
      if (key === "sunIntensity") { state.sun.intensity = v; if (engine) engine.setSun(state.sun); scheduleUIUpdate(); return; }

      if (key in state.build) {
        state.build[key] = v;
        rebuildBuildingSmooth();
        updateMiniStats();
        scheduleUIUpdate();
        return;
      }
    }
  });

  // =========================================================
  // 11) BOOT
  // =========================================================
  render();

  window.addEventListener("keydown", (e) => {
    if (!engine || state.step !== 1) return;

    // don’t hijack typing in inputs
    const t = e.target;
    const isForm = t && (t.tagName === "INPUT" || t.tagName === "TEXTAREA" || t.tagName === "SELECT" || t.isContentEditable);
    if (isForm) return;

    const base = state.snap.enabled ? state.snap.step : 0.01;
    const step = e.shiftKey ? 0.04 : base;

    let handled = true;

    if (e.key === "ArrowLeft" || e.key.toLowerCase() === "a") engine.nudgeSelected(-step, 0);
    else if (e.key === "ArrowRight" || e.key.toLowerCase() === "d") engine.nudgeSelected(step, 0);
    else if (e.key === "ArrowUp" || e.key.toLowerCase() === "w") engine.nudgeSelected(0, -step);
    else if (e.key === "ArrowDown" || e.key.toLowerCase() === "s") engine.nudgeSelected(0, step);
    else handled = false;

    if (!handled) return;

    e.preventDefault();
    clearTimeout(window.__nudgeEndT);
    window.__nudgeEndT = setTimeout(() => engine.endNudge(), 120);
  });

</script>

</body>
</html>
