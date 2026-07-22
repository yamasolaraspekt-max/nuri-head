/**
 * Hausplaner P0 — 2D-Editor (Konva). Der Renderer LIEST den Store und erzeugt
 * ausschließlich Commands (keine Geschäftsdaten im Renderer — Spec Kap. 10).
 *
 * UX-Rahmen (ux-design-Skill): 90 % Graustufen, Marken-Grün NUR für die Primäraktion
 * (Speichern) und die aktive Werkzeug-Markierung; Statusfarben semantisch und immer
 * Farbe + TEXT; Tastatur: V/W/F/T Werkzeuge, Esc Abbruch, Entf Löschen, Strg+Z/Y, Strg+S.
 * Zustände gestaltet: gespeichert/ungespeichert/speichert/KONFLIKT (409)/Ablehnung.
 */
import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Stage, Layer, Line, Rect, Group, Text, Circle } from 'react-konva';
import type Konva from 'konva';
import { useHausplanerStore } from '../store/hausplanerStore';
import type { OpeningNode, RoofNode, SceneNode, WallNode } from '../domain/scene.types';
import { erkenneRaeume } from '../geometry/roomDetection';
import { bemassung } from '../geometry/bemassung';
import { wandLaenge, punktAufWand, wandBaender, tuerBlattGeometrie, type Punkt } from '../geometry/wallGeometry';
import { TUER_TYPEN, FENSTER_TYPEN, tuerTyp, fensterTyp, type TuerTyp, type FensterTyp } from '../geometry/oeffnungsTypen';
import { DreiDBereich } from './DreiDBereich';
import { versetzteWand, spiegelteWand, bbox as punkteBbox, achsenMitte, type Achse } from '../geometry/editierGeometrie';

type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach';

const FARBEN = {
  text: '#1f2937', gedaempft: '#6b7280', linie: '#9ca3af', raster: '#eef0f2', rasterGrob: '#e2e4e7',
  wand: '#374151', wandFuellung: '#4b5563', auswahl: '#93c21c', raum: 'rgba(147,194,28,0.06)',
  warnung: '#d97706', gefahr: '#b91c1c', erfolg: '#15803d',
} as const;

// L1 Layout-Aktivierung — Navigations-Stile (tokens-konform: neutral, Marke nur als Auswahl-Akzent).
const navGrp: React.CSSProperties = { fontSize: 10, fontWeight: 700, letterSpacing: '0.06em', textTransform: 'uppercase', color: '#9ca3af', padding: '12px 12px 4px' };
const navItem = (aktiv: boolean): React.CSSProperties => ({ display: 'flex', alignItems: 'center', gap: 9, textAlign: 'left', width: 'calc(100% - 12px)', margin: '1px 6px', padding: '8px 8px', border: 'none', borderRadius: 8, cursor: 'pointer', fontSize: 13, background: aktiv ? 'rgba(147,194,28,0.12)' : 'transparent', color: aktiv ? '#3f5a00' : '#1f2937', fontWeight: aktiv ? 700 : 500 });
const navHub: React.CSSProperties = { fontSize: 12.5, fontWeight: 600, color: '#1f2937', padding: '8px 12px 2px' };
const navSub: React.CSSProperties = { fontSize: 12.5, color: '#6b7280', padding: '6px 12px 6px 22px' };
const FACHPLANER: ReadonlyArray<{ name: string; items: readonly string[] }> = [
  { name: 'Haustechnik', items: ['Heizung', 'Heizlastberechnung', 'Lüftung', 'Klima', 'Wärmepumpe', 'Heizkörper', 'Fußbodenheizung', 'Wärme-Contracting'] },
  { name: 'PV-Planer', items: ['PV-Module', 'Speicherauslegung', 'Wallbox', 'Carport', 'Zaun', 'Freiland', 'HEMS', 'Messstellenbetrieb', 'dynamischer Stromtarif', 'Mietstrom'] },
  { name: 'Bauelemente', items: ['Fenster', 'Tür'] },
  { name: 'Bad', items: [] },
  { name: 'Küche', items: [] },
];

// SVG-Icons (frei/Feather-Stil, nachgezeichnet) — einheitlich 24er-Viewbox, stroke=currentColor.
function svgWrap(children: React.ReactNode): React.ReactElement {
  return (
    <svg width={18} height={18} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={1.7} strokeLinecap="round" strokeLinejoin="round">{children}</svg>
  );
}
function werkzeugIcon(w: string): React.ReactElement {
  switch (w) {
    case 'auswahl': return svgWrap(<path d="M5 3l6 16 2-7 7-2z" />);
    case 'wand': return svgWrap(<rect x="3" y="10" width="18" height="4" rx="1" />);
    case 'fenster': return svgWrap(<><rect x="4" y="4" width="16" height="16" rx="1" /><path d="M12 4v16M4 12h16" /></>);
    case 'tuer': return svgWrap(<><path d="M7 21V4h9v17" /><path d="M7 21a9 9 0 0 1 9-9" /></>);
    case 'dach': return svgWrap(<path d="M3 12L12 5l9 7" />);
    default: return svgWrap(<circle cx="12" cy="12" r="3" />);
  }
}
function fachIcon(name: string): React.ReactElement {
  switch (name) {
    case 'Haustechnik': return svgWrap(<path d="M8 3v18M12 3v18M16 3v18" />);
    case 'PV-Planer': return svgWrap(<><rect x="3" y="4" width="18" height="12" rx="1" /><path d="M3 8h18M3 12h18M9 4v12M15 4v12" /></>);
    case 'Bauelemente': return svgWrap(<><rect x="4" y="4" width="16" height="16" rx="1" /><path d="M12 4v16M4 12h16" /></>);
    case 'Bad': return svgWrap(<><path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z" /><path d="M6 12V6a2 2 0 0 1 4 0" /></>);
    case 'Küche': return svgWrap(<><rect x="3" y="3" width="18" height="18" rx="2" /><path d="M3 15h18M8 3v6" /></>);
    default: return svgWrap(<circle cx="12" cy="12" r="8" />);
  }
}

const uuid = (): string =>
  (globalThis.crypto?.randomUUID?.() ?? `id-${Math.random().toString(36).slice(2)}-${Date.now()}`);

function istWand(n: SceneNode): n is WallNode {
  return n.type === 'wall';
}
function istOeffnung(n: SceneNode): n is OpeningNode {
  return n.type === 'window' || n.type === 'door' || n.type === 'opening';
}

/** Abstand Punkt→Strecke + Offset entlang der Strecke (mm). */
function lotAufWand(p: Punkt, w: WallNode): { abstand: number; offset: number } {
  const dx = w.end.x - w.start.x;
  const dy = w.end.y - w.start.y;
  const laengeQ = dx * dx + dy * dy;
  if (laengeQ === 0) {
    return { abstand: Number.POSITIVE_INFINITY, offset: 0 };
  }
  const t = Math.max(0, Math.min(1, ((p.x - w.start.x) * dx + (p.y - w.start.y) * dy) / laengeQ));
  const fx = w.start.x + t * dx;
  const fy = w.start.y + t * dy;

  return { abstand: Math.hypot(p.x - fx, p.y - fy), offset: t * Math.sqrt(laengeQ) };
}

export function HausplanerApp(): React.ReactElement {
  const scene = useHausplanerStore((s) => s.scene);
  const activeLevelId = useHausplanerStore((s) => s.activeLevelId);
  const selectedNodeIds = useHausplanerStore((s) => s.selectedNodeIds);
  const speicherStatus = useHausplanerStore((s) => s.speicherStatus);
  const konfliktRevision = useHausplanerStore((s) => s.konfliktRevision);
  const letzteAblehnung = useHausplanerStore((s) => s.letzteAblehnung);
  const modus = useHausplanerStore((s) => s.modus);
  const store = useHausplanerStore;

  const [werkzeug, setWerkzeug] = useState<Werkzeug>('auswahl');
  const [fensterTypWahl, setFensterTypWahl] = useState<FensterTyp>('drehkipp');
  const [tuerTypWahl, setTuerTypWahl] = useState<TuerTyp>('dreh1');
  const [wandStart, setWandStart] = useState<Punkt | null>(null);
  const [cursor, setCursor] = useState<Punkt>({ x: 0, y: 0 });
  const [zoom, setZoom] = useState(0.12); // px pro mm
  const stageRef = useRef<Konva.Stage | null>(null);

  const level = scene?.levels.find((l) => l.id === activeLevelId) ?? scene?.levels[0] ?? null;
  const nodes = useMemo(
    () => (scene && level ? scene.nodes.filter((n) => n.levelId === level.id) : []),
    [scene, level],
  );
  const waende = useMemo(() => nodes.filter(istWand), [nodes]);
  const raeume = useMemo(
    () => (level ? erkenneRaeume(waende, level.defaultWallHeight) : []),
    [waende, level],
  );
  // P2b-2: Wandbaender (gefuellte Polygone mit Gehrung an 2-Wand-Ecken).
  const bandVon = useMemo(() => {
    const m = new Map<string, ReturnType<typeof wandBaender>[number]>();
    for (const b of wandBaender(waende)) {
      m.set(b.id, b);
    }
    return m;
  }, [waende]);

  // D-c: genau EIN ausgewähltes Dach ⇒ Parameter-Panel; Änderungen laufen als UPDATE_ROOF-Command.
  const selectedRoof = scene?.roofs?.find((r) => selectedNodeIds.length === 1 && selectedNodeIds[0] === r.id) ?? null;
  function aktualisiereDach(changes: Partial<RoofNode>): void {
    if (selectedRoof) {
      store.getState().executeCommand({ type: 'UPDATE_ROOF', roofId: selectedRoof.id, changes });
    }
  }

  // P2b-1: Mauerwerk-Katalog (materialId-Wert + Anzeige) + gängige Wandstärken (mm).
  const MAUERWERK = [
    { id: 'ziegel', label: 'Ziegel (Hochlochziegel)' },
    { id: 'kalksandstein', label: 'Kalksandstein (KS)' },
    { id: 'porenbeton', label: 'Porenbeton' },
    { id: 'leichtbeton', label: 'Leichtbeton (Bims)' },
    { id: 'stahlbeton', label: 'Stahlbeton' },
    { id: 'holzstaender', label: 'Holzständerwand' },
  ] as const;
  const WANDSTAERKEN = [115, 150, 175, 240, 300, 365] as const;

  // P2b-1: genau EINE ausgewählte Wand ⇒ Mauerwerk-/Stärke-Panel; Änderungen als UPDATE_NODE (additiv).
  const selectedWall = waende.find((w) => selectedNodeIds.length === 1 && selectedNodeIds[0] === w.id) ?? null;
  function aktualisiereWand(changes: Partial<WallNode>): void {
    if (selectedWall) {
      store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedWall.id, changes });
    }
  }
  // Bearbeiten: Wand-Laenge exakt setzen -> Wandende entlang der Achse verschieben (MOVE_NODE).
  function setzeWandLaenge(neu: number): void {
    if (!selectedWall || !(neu > 0)) return;
    const dx = selectedWall.end.x - selectedWall.start.x;
    const dy = selectedWall.end.y - selectedWall.start.y;
    const len = Math.hypot(dx, dy);
    if (len === 0) return;
    const end = { x: Math.round(selectedWall.start.x + (dx / len) * neu), y: Math.round(selectedWall.start.y + (dy / len) * neu) };
    store.getState().executeCommand({ type: 'MOVE_NODE', nodeId: selectedWall.id, position: { start: selectedWall.start, end } });
  }
  // P2b-4: genau EINE ausgewählte Öffnung ⇒ Öffnungs-Panel (Tür: Anschlag/Öffnung); UPDATE_NODE (additiv).
  const selectedOpening = (nodes.find((n) => istOeffnung(n) && selectedNodeIds.length === 1 && selectedNodeIds[0] === n.id) ?? null) as OpeningNode | null;
  function aktualisiereOeffnung(changes: Partial<OpeningNode>): void {
    if (selectedOpening) {
      store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedOpening.id, changes });
    }
  }
  // Editier-Operationen: Bewegen laeuft ueber das Ziehen (unten am Node); hier Loeschen/Duplizieren/Spiegeln.
  function loescheAuswahl(): void {
    for (const id of selectedNodeIds) {
      store.getState().executeCommand({ type: 'REMOVE_NODE', nodeId: id });
    }
    store.getState().selectNodes([]);
  }
  function dupliziere(): void {
    const jetzt = new Date().toISOString();
    const neu: string[] = [];
    for (const id of selectedNodeIds) {
      const n = nodes.find((x) => x.id === id);
      if (!n) continue;
      const neueId = uuid();
      if (n.type === 'wall') {
        const g = versetzteWand(n.start, n.end, 500, 500);
        if (store.getState().executeCommand({ type: 'ADD_NODE', node: { ...n, id: neueId, start: g.start, end: g.end, createdAt: jetzt, updatedAt: jetzt } })) neu.push(neueId);
      } else if (istOeffnung(n)) {
        const wand = waende.find((w) => w.id === n.hostWallId);
        if (!wand) continue;
        const laenge = wandLaenge(wand.start, wand.end);
        const off = Math.round(Math.max(0, Math.min(n.offsetFromWallStart + n.width + 100, laenge - n.width)));
        if (store.getState().executeCommand({ type: 'ADD_NODE', node: { ...n, id: neueId, offsetFromWallStart: off, createdAt: jetzt, updatedAt: jetzt } })) neu.push(neueId);
      }
    }
    if (neu.length) store.getState().selectNodes(neu);
  }
  function spiegeleGrundriss(achse: Achse): void {
    const b = punkteBbox(waende.flatMap((w) => [w.start, w.end]));
    if (!b) return;
    const pos = achsenMitte(b, achse);
    for (const w of waende) {
      const g = spiegelteWand(w.start, w.end, achse, pos);
      store.getState().executeCommand({ type: 'MOVE_NODE', nodeId: w.id, position: { start: g.start, end: g.end } });
    }
  }

  /** Default-Traufkontur = Gebäude-Umriss (Bounding-Box der Wände; ohne Wände ein 8×10-m-Rechteck). */
  function gebaeudeUmriss(): Array<{ x: number; y: number }> {
    const pts = waende.flatMap((w) => [w.start, w.end]);
    if (pts.length === 0) {
      return [{ x: 0, y: 0 }, { x: 8000, y: 0 }, { x: 8000, y: 10000 }, { x: 0, y: 10000 }];
    }
    const xs = pts.map((p) => p.x);
    const ys = pts.map((p) => p.y);
    const minX = Math.min(...xs), maxX = Math.max(...xs), minY = Math.min(...ys), maxY = Math.max(...ys);

    return [{ x: minX, y: minY }, { x: maxX, y: minY }, { x: maxX, y: maxY }, { x: minX, y: maxY }];
  }

  /** Bildschirm→Welt (mm) mit Grid-/Endpunkt-Snapping. */
  function weltPunkt(e: Konva.KonvaEventObject<MouseEvent>): Punkt {
    const stage = stageRef.current;
    const zeiger = stage?.getPointerPosition();
    if (!stage || !zeiger || !scene) {
      return { x: 0, y: 0 };
    }
    // y-Achse: Welt hat Nord=+y (oben), Konva wächst nach unten ⇒ spiegeln.
    let x = (zeiger.x - stage.x()) / zoom;
    let y = -((zeiger.y - stage.y()) / zoom);

    if (scene.settings.snapEnabled) {
      // 1) Endpunkt-Snap (150 mm Radius) hat Vorrang.
      for (const w of waende) {
        for (const p of [w.start, w.end]) {
          if (Math.hypot(p.x - x, p.y - y) <= 150) {
            return { x: p.x, y: p.y };
          }
        }
      }
      // 2) Raster-Snap.
      const g = scene.settings.gridSize || 100;
      x = Math.round(x / g) * g;
      y = Math.round(y / g) * g;
    }

    return { x: Math.round(x), y: Math.round(y) };
  }

  /** Winkel-Snap für das Wandzeichnen (angleSnap-Grad-Raster um den Startpunkt). */
  function mitWinkelSnap(start: Punkt, p: Punkt): Punkt {
    if (!scene || !scene.settings.angleSnap) {
      return p;
    }
    const dx = p.x - start.x;
    const dy = p.y - start.y;
    const laenge = Math.hypot(dx, dy);
    if (laenge === 0) {
      return p;
    }
    const schritt = (scene.settings.angleSnap * Math.PI) / 180;
    const winkel = Math.round(Math.atan2(dy, dx) / schritt) * schritt;

    return { x: Math.round(start.x + laenge * Math.cos(winkel)), y: Math.round(start.y + laenge * Math.sin(winkel)) };
  }

  function klick(e: Konva.KonvaEventObject<MouseEvent>): void {
    if (!scene || !level) {
      return;
    }
    const p = weltPunkt(e);

    if (werkzeug === 'wand') {
      if (!wandStart) {
        setWandStart(p);
        return;
      }
      const ende = mitWinkelSnap(wandStart, p);
      const jetzt = new Date().toISOString();
      store.getState().executeCommand({
        type: 'ADD_NODE',
        node: {
          id: uuid(), type: 'wall', levelId: level.id, visible: true, locked: false, tags: [],
          createdAt: jetzt, updatedAt: jetzt,
          start: wandStart, end: ende, thickness: 240, height: level.defaultWallHeight,
        },
      });
      setWandStart(ende); // Polygonzug: weiterzeichnen ab dem Ende
      return;
    }

    if (werkzeug === 'fenster' || werkzeug === 'tuer') {
      let beste: { wand: WallNode; offset: number } | null = null;
      for (const w of waende) {
        const lot = lotAufWand(p, w);
        if (lot.abstand <= 300 && (!beste || lot.abstand < lotAufWand(p, beste.wand).abstand)) {
          beste = { wand: w, offset: lot.offset };
        }
      }
      if (!beste) {
        return;
      }
      const vorlage = werkzeug === 'fenster' ? fensterTyp(fensterTypWahl) : tuerTyp(tuerTypWahl);
      const breite = vorlage.breite;
      const laenge = wandLaenge(beste.wand.start, beste.wand.end);
      const offset = Math.round(Math.max(0, Math.min(beste.offset - breite / 2, laenge - breite)));
      const jetzt = new Date().toISOString();
      store.getState().executeCommand({
        type: 'ADD_NODE',
        node: {
          id: uuid(), type: werkzeug === 'fenster' ? 'window' : 'door', levelId: level.id,
          visible: true, locked: false, tags: [], createdAt: jetzt, updatedAt: jetzt,
          hostWallId: beste.wand.id, offsetFromWallStart: offset, width: breite,
          height: vorlage.hoehe, sillHeight: werkzeug === 'fenster' ? (vorlage.bruestung ?? 0) : 0,
        },
      });
      return;
    }

    if (werkzeug === 'dach') {
      // D-c: ein Dach je Geschoss (▲D1). Default-Kontur = Gebäude-Umriss, Sattel 35°, Überstand 500 mm.
      // Ein bereits vorhandenes Dach lehnt der Command ab (Meldung erscheint in der Statusleiste).
      const jetzt = new Date().toISOString();
      store.getState().executeCommand({
        type: 'ADD_ROOF',
        roof: {
          id: uuid(), type: 'roof', levelId: level.id, visible: true, locked: false, tags: [],
          createdAt: jetzt, updatedAt: jetzt,
          polygon: gebaeudeUmriss(), roofType: 'sattel', neigungGrad: 35, firstAzimutGrad: 0,
          ueberstandMm: 500, traufhoeheMm: level.elevation + level.defaultWallHeight,
        },
      });
      setWerkzeug('auswahl');

      return;
    }

    // Auswahl: Klick auf leere Fläche hebt die Auswahl auf (Nodes stoppen die Propagation).
    store.getState().selectNodes([]);
  }

  useEffect(() => {
    function taste(e: KeyboardEvent): void {
      if ((e.target as HTMLElement)?.tagName === 'INPUT') {
        return;
      }
      if (e.key === 'Escape') {
        setWandStart(null);
        setWerkzeug('auswahl');
      } else if (e.key === 'Delete' || e.key === 'Backspace') {
        for (const id of store.getState().selectedNodeIds) {
          store.getState().executeCommand({ type: 'REMOVE_NODE', nodeId: id });
        }
        store.getState().selectNodes([]);
      } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
        e.preventDefault();
        store.getState().undo();
      } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
        e.preventDefault();
        store.getState().redo();
      } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
        e.preventDefault();
        void store.getState().save();
      } else if (e.key === 'v') {
        setWerkzeug('auswahl');
      } else if (e.key === 'w') {
        setWerkzeug('wand');
      } else if (e.key === 'f') {
        setWerkzeug('fenster');
      } else if (e.key === 't') {
        setWerkzeug('tuer');
      } else if (e.key === 'd') {
        setWerkzeug('dach');
      }
    }
    window.addEventListener('keydown', taste);

    return () => window.removeEventListener('keydown', taste);
  }, [store]);

  if (!scene || !level) {
    return <div style={{ padding: 24, color: FARBEN.text }}>Szene nicht geladen.</div>;
  }

  const statusPill = {
    gespeichert: { text: 'Gespeichert', farbe: FARBEN.erfolg, grund: '#ecfdf5' },
    ungespeichert: { text: 'Ungespeicherte Änderungen', farbe: FARBEN.warnung, grund: '#fff7ed' },
    speichert: { text: 'Wird gespeichert …', farbe: FARBEN.gedaempft, grund: '#f3f4f6' },
    konflikt: { text: `Konflikt: Plan wurde von anderer Seite geändert (Revision ${konfliktRevision ?? '?'}) — Seite neu laden`, farbe: FARBEN.gefahr, grund: '#fef2f2' },
    fehler: { text: 'Speichern fehlgeschlagen — erneut versuchen', farbe: FARBEN.gefahr, grund: '#fef2f2' },
  }[speicherStatus];

  const knopf = (aktiv: boolean): React.CSSProperties => ({
    padding: '6px 12px', fontSize: 12.5, fontWeight: 600, borderRadius: 8, cursor: 'pointer',
    border: `1px solid ${aktiv ? FARBEN.auswahl : '#d1d5db'}`,
    background: aktiv ? '#f4fae7' : '#fff', color: aktiv ? '#4d7c0f' : '#374151',
  });

  const panelLabel: React.CSSProperties = { display: 'block', color: FARBEN.gedaempft, marginBottom: 8 };
  const panelInput: React.CSSProperties = { width: '100%', marginTop: 3, padding: '5px 8px', borderRadius: 8, border: '1px solid #d1d5db', fontSize: 12.5 };

  const railIcon = (w: string): string => (({ auswahl: '\u2196', wand: '\u25AC', fenster: '\u25A2', tuer: '\u25D7', dach: '\u25B3' } as Record<string, string>)[w] ?? '\u2022');
  const railBtn = (aktiv: boolean): React.CSSProperties => ({
    display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
    height: 46, borderRadius: 9, cursor: 'pointer', fontWeight: 600,
    border: `1px solid ${aktiv ? 'var(--sa-accent, #93c21c)' : 'transparent'}`,
    background: aktiv ? 'var(--sa-accent-light, #f4fae7)' : 'transparent',
    color: aktiv ? 'var(--sa-accent-hover, #4d7c0f)' : FARBEN.gedaempft,
  });

  const breite = (typeof window !== 'undefined' ? window.innerWidth : 1200) - 220 - 268; // minus Werkzeugleiste + Panel
  const stageBreite = modus === 'split' ? Math.floor(breite / 2) : breite; // P1c: Split teilt die Fläche
  const hoehe = typeof window !== 'undefined' ? window.innerHeight - 96 : 700;

  // Raster-Linien (nur grobe Linien, Performance).
  const rasterSchritt = Math.max(scene.settings.gridSize * 5, 500);
  const rasterLinien: React.ReactElement[] = [];
  const weltBreite = breite / zoom;
  const weltHoehe = hoehe / zoom;
  for (let x = -weltBreite; x <= weltBreite * 2; x += rasterSchritt) {
    rasterLinien.push(<Line key={`vx${x}`} points={[x, -weltHoehe * 2, x, weltHoehe * 2]} stroke={FARBEN.rasterGrob} strokeWidth={1 / zoom} listening={false} />);
  }
  for (let y = -weltHoehe; y <= weltHoehe * 2; y += rasterSchritt) {
    rasterLinien.push(<Line key={`hy${y}`} points={[-weltBreite * 2, y, weltBreite * 2, y]} stroke={FARBEN.rasterGrob} strokeWidth={1 / zoom} listening={false} />);
  }

  // P2b-3: mehrstufige Bemaßung (nur lesen, kein Command) — INNEN die Öffnungskette (Wandstärken +
  // Fenster/Tür-Öffnungen + lichte Maße), AUSSEN das Gesamt-Außenmaß. Referenzpunkte sauber je Achse.
  const bem = bemassung(
    waende.map((w) => ({ id: w.id, start: w.start, end: w.end, thickness: w.thickness })),
    nodes.filter(istOeffnung).map((o) => ({ hostWallId: o.hostWallId, offsetFromWallStart: o.offsetFromWallStart, width: o.width })),
  );
  const massElemente: React.ReactElement[] = [];
  if (bem.bbox) {
    const bb = bem.bbox;
    const tick = 120;
    const sw = 1 / zoom;
    const mfarbe = '#6b7280';
    const gfarbe = '#374151';
    type Seg = { von: number; bis: number; laenge: number };
    const ketteX = (segs: ReadonlyArray<Seg>, yl: number, kp: string, farbe: string, fs: number) => {
      segs.forEach((seg, i) => {
        massElemente.push(<Line key={`${kp}L${i}`} points={[seg.von, yl, seg.bis, yl]} stroke={farbe} strokeWidth={sw} listening={false} />);
        massElemente.push(<Line key={`${kp}a${i}`} points={[seg.von, yl - tick, seg.von, yl + tick]} stroke={farbe} strokeWidth={sw} listening={false} />);
        massElemente.push(<Text key={`${kp}t${i}`} x={(seg.von + seg.bis) / 2 - 700} y={yl - 200} width={1400} align="center" scaleY={-1} text={`${seg.laenge}`} fontSize={fs} fill={farbe} listening={false} />);
      });
      if (segs.length) { const last = segs[segs.length - 1]; massElemente.push(<Line key={`${kp}end`} points={[last.bis, yl - tick, last.bis, yl + tick]} stroke={farbe} strokeWidth={sw} listening={false} />); }
    };
    const ketteY = (segs: ReadonlyArray<Seg>, xl: number, kp: string, farbe: string, fs: number) => {
      segs.forEach((seg, i) => {
        massElemente.push(<Line key={`${kp}L${i}`} points={[xl, seg.von, xl, seg.bis]} stroke={farbe} strokeWidth={sw} listening={false} />);
        massElemente.push(<Line key={`${kp}a${i}`} points={[xl - tick, seg.von, xl + tick, seg.von]} stroke={farbe} strokeWidth={sw} listening={false} />);
        massElemente.push(<Text key={`${kp}t${i}`} x={xl - 1500} y={(seg.von + seg.bis) / 2 + 90} width={1400} align="right" scaleY={-1} text={`${seg.laenge}`} fontSize={fs} fill={farbe} listening={false} />);
      });
      if (segs.length) { const last = segs[segs.length - 1]; massElemente.push(<Line key={`${kp}end`} points={[xl - tick, last.bis, xl + tick, last.bis]} stroke={farbe} strokeWidth={sw} listening={false} />); }
    };
    ketteX(bem.x.oeffnung, bb.minY - 900, 'ox', mfarbe, 150);
    ketteY(bem.y.oeffnung, bb.minX - 900, 'oy', mfarbe, 150);
    if (bem.x.gesamt) ketteX([bem.x.gesamt], bb.minY - 1900, 'gx', gfarbe, 190);
    if (bem.y.gesamt) ketteY([bem.y.gesamt], bb.minX - 1900, 'gy', gfarbe, 190);
  }

  return (
    <div style={{ fontFamily: 'Inter, system-ui, sans-serif', color: FARBEN.text, height: '100vh', display: 'flex', flexDirection: 'column', background: '#f9fafb' }}>
      {/* Werkzeugleiste — neutral, Marke nur für Primäraktion */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '10px 14px', background: '#fff', borderBottom: '1px solid #e5e7eb' }}>
        <strong style={{ fontSize: 14, marginRight: 8 }}>Hausplaner</strong>
        <button type="button" style={knopf(false)} onClick={() => store.getState().undo()} disabled={!store.getState().kannUndo()}>↶ Rückgängig</button>
        <button type="button" style={knopf(false)} onClick={() => store.getState().redo()} disabled={!store.getState().kannRedo()}>↷ Wiederholen</button>
        <span style={{ width: 1, height: 22, background: '#e5e7eb', margin: '0 4px' }} />
        {(werkzeug === 'fenster' || werkzeug === 'tuer') && (
          <label style={{ fontSize: 12, color: FARBEN.gedaempft, display: 'flex', alignItems: 'center', gap: 5 }}>
            {werkzeug === 'fenster' ? 'Fenstertyp' : 'Türtyp'}
            <select
              value={werkzeug === 'fenster' ? fensterTypWahl : tuerTypWahl}
              onChange={(e) => (werkzeug === 'fenster' ? setFensterTypWahl(e.target.value as FensterTyp) : setTuerTypWahl(e.target.value as TuerTyp))}
              style={{ fontSize: 12.5, padding: '5px 8px', borderRadius: 8, border: '1px solid #d1d5db' }}
            >
              {(werkzeug === 'fenster' ? FENSTER_TYPEN : TUER_TYPEN).map((v) => (
                <option key={v.typ} value={v.typ}>{v.label} · {v.breite}×{v.hoehe} mm</option>
              ))}
            </select>
          </label>
        )}
        <label style={{ fontSize: 12, color: FARBEN.gedaempft }}>
          Geschoss{' '}
          <select value={level.id} onChange={(e) => store.getState().setActiveLevel(e.target.value)} style={{ fontSize: 12.5, padding: '5px 8px', borderRadius: 8, border: '1px solid #d1d5db' }}>
            {scene.levels.map((l) => <option key={l.id} value={l.id}>{l.name}</option>)}
          </select>
        </label>
        {/* P2b-5: Geschoss anlegen / umbenennen / löschen. Löschen NIE still — belegte Geschosse
            lehnt der Command ab (letzteAblehnung wird sichtbar); id bleibt Referenz-Wahrheit. */}
        <input
          key={level.id}
          type="text"
          defaultValue={level.name}
          title="Geschoss umbenennen (Enter bestätigt)"
          onKeyDown={(e) => { if (e.key === 'Enter') (e.target as HTMLInputElement).blur(); }}
          onBlur={(e) => {
            const name = e.target.value.trim();
            if (name && name !== level.name) {
              store.getState().executeCommand({ type: 'UPDATE_LEVEL', levelId: level.id, changes: { name } });
            } else {
              e.target.value = level.name; // leeren/unveränderten Namen zurücksetzen
            }
          }}
          style={{ width: 104, fontSize: 12.5, padding: '5px 8px', borderRadius: 8, border: '1px solid #d1d5db' }}
        />
        <button
          type="button"
          style={knopf(false)}
          title="Neues Geschoss über dem obersten anlegen"
          onClick={() => {
            const oben = scene.levels.reduce((a, b) => (b.sortOrder > a.sortOrder ? b : a));
            const neu = {
              id: uuid(),
              name: `Geschoss ${scene.levels.length + 1}`,
              elevation: oben.elevation + oben.defaultWallHeight + oben.floorThickness,
              defaultWallHeight: oben.defaultWallHeight,
              floorThickness: oben.floorThickness,
              sortOrder: oben.sortOrder + 1,
            };
            if (store.getState().executeCommand({ type: 'ADD_LEVEL', level: neu })) {
              store.getState().setActiveLevel(neu.id);
            }
          }}
        >+ Geschoss</button>
        <button
          type="button"
          style={{ ...knopf(false), opacity: scene.levels.length <= 1 ? 0.4 : 1, cursor: scene.levels.length <= 1 ? 'not-allowed' : 'pointer' }}
          disabled={scene.levels.length <= 1}
          title={scene.levels.length <= 1 ? 'Das letzte Geschoss kann nicht gelöscht werden' : 'Aktives Geschoss löschen (muss leer sein)'}
          onClick={() => {
            const rest = scene.levels.filter((l) => l.id !== level.id);
            if (store.getState().executeCommand({ type: 'REMOVE_LEVEL', levelId: level.id }) && rest[0]) {
              store.getState().setActiveLevel(rest[0].id);
            }
          }}
        >− Geschoss</button>
        <label style={{ fontSize: 12, color: FARBEN.gedaempft, display: 'flex', alignItems: 'center', gap: 4 }}>
          <input type="checkbox" checked={scene.settings.snapEnabled} onChange={(e) => store.getState().executeCommand({ type: 'UPDATE_SETTINGS', changes: { snapEnabled: e.target.checked } })} />
          Fang
        </label>
        <span style={{ width: 1, height: 22, background: '#e5e7eb', margin: '0 4px' }} />
        {/* P1c: Modus-Schalter — 3D ist der zweite Renderer DERSELBEN Daten (ein Store). */}
        <button type="button" style={knopf(modus === '2d')} onClick={() => store.getState().setModus('2d')}>2D</button>
        <button type="button" style={knopf(modus === 'split')} onClick={() => store.getState().setModus('split')}>Split</button>
        <button type="button" style={knopf(modus === '3d')} onClick={() => store.getState().setModus('3d')}>3D</button>
        <span style={{ flex: 1 }} />
        <span style={{ fontSize: 12, fontWeight: 600, padding: '4px 12px', borderRadius: 999, color: statusPill.farbe, background: statusPill.grund }}>{statusPill.text}</span>
        <button
          type="button"
          onClick={() => void store.getState().save()}
          style={{ padding: '7px 16px', fontSize: 13, fontWeight: 700, borderRadius: 8, border: 'none', cursor: 'pointer', background: 'var(--sa-accent, #93c21c)', color: 'var(--sa-accent-ink, #fff)' }}
        >
          Speichern (Strg+S)
        </button>
      </div>

      {/* Canvas: 2D (Konva) + 3D (three) nebeneinander — beide lesen DENSELBEN Store.
          Der 3D-Bereich bleibt über Moduswechsel gemountet (nur ausgeblendet) ⇒ Kamera
          bleibt erhalten; dispose() erst beim Verlassen der Seite (Kante 6). */}
      <div style={{ flex: 1, overflow: 'hidden', display: 'flex' }}>
        {/* L1: Planer-Navigation — Werkzeuge (aktiv) + Fachplaner-Struktur (Navi). */}
        <div style={{ width: 220, flex: '0 0 auto', background: '#fff', borderRight: '1px solid #e5e7eb', display: 'flex', flexDirection: 'column', overflow: 'auto' }}>
          <div style={navGrp}>Werkzeuge</div>
          {([
            ['auswahl', 'V', 'Auswahl', 'Objekte anklicken zum Markieren, ziehen zum Bewegen'],
            ['wand', 'W', 'Wand', 'Wände zeichnen — Punkt für Punkt klicken (Polygonzug)'],
            ['fenster', 'F', 'Fenster', 'Fenster auf eine Wand setzen — Typ oben wählbar'],
            ['tuer', 'T', 'Tür', 'Tür auf eine Wand setzen — Typ oben wählbar'],
            ['dach', 'D', 'Dach', 'Dach über den Gebäudeumriss aufsetzen'],
          ] as ReadonlyArray<readonly [string, string, string, string]>).map(([w, k, label, beschr]) => (
            <button key={w} type="button" title={`${label} (${k}) — ${beschr}`}
              onClick={() => { setWerkzeug(w as typeof werkzeug); if (w === 'wand') { setWandStart(null); } }}
              style={navItem(werkzeug === w)}>
              <span style={{ width: 18, height: 18, display: 'grid', placeItems: 'center', flex: '0 0 auto' }}>{werkzeugIcon(w)}</span>
              <span style={{ flex: 1 }}>{label}</span>
              <span style={{ fontSize: 10.5, color: '#9ca3af' }}>{k}</span>
            </button>
          ))}
          <div style={navGrp}>Fachplaner</div>
          {FACHPLANER.map((g) => (
            <React.Fragment key={g.name}>
              <div style={{ ...navHub, display: 'flex', alignItems: 'center', gap: 8 }} title={`${g.name} — Fachplaner (autark, geplant)`}>
                <span style={{ width: 18, height: 18, display: 'grid', placeItems: 'center', color: '#6b7280', flex: '0 0 auto' }}>{fachIcon(g.name)}</span>
                <span>{g.name}</span>
              </div>
              {g.items.map((it) => (<div key={it} style={navSub} title={`${it} — autark konfigurierbar (geplant)`}>{it}</div>))}
            </React.Fragment>
          ))}
          <div style={{ padding: '10px 12px', fontSize: 11, color: '#9ca3af', borderTop: '1px solid #eef0f2', marginTop: 'auto' }}>Erweiterbar – Module folgen.</div>
        </div>
        <div style={{ display: modus === '3d' ? 'none' : 'block', width: stageBreite, borderRight: modus === 'split' ? '1px solid #e5e7eb' : 'none' }}>
        <Stage
          ref={stageRef as never}
          width={stageBreite}
          height={hoehe}
          draggable={werkzeug === 'auswahl'}
          onClick={klick}
          onMouseMove={(e) => setCursor(weltPunkt(e))}
          onWheel={(e) => {
            e.evt.preventDefault();
            const faktor = e.evt.deltaY < 0 ? 1.1 : 1 / 1.1;
            setZoom((z) => Math.min(1, Math.max(0.02, z * faktor)));
          }}
          scaleX={zoom}
          scaleY={-zoom}
          x={80}
          y={hoehe - 80}
        >
          <Layer>
            {rasterLinien}
            {massElemente}

            {/* Räume: Füllung + Fläche (m², aus mm² gerundet auf 2 Stellen) */}
            {raeume.map((raum, i) => {
              const xs = raum.polygon.map((p) => p.x);
              const ys = raum.polygon.map((p) => p.y);
              const cx = (Math.min(...xs) + Math.max(...xs)) / 2;
              const cy = (Math.min(...ys) + Math.max(...ys)) / 2;

              return (
                <Group key={`raum${i}`} listening={false}>
                  <Line points={raum.polygon.flatMap((p) => [p.x, p.y])} closed fill={FARBEN.raum} stroke="transparent" />
                  <Text
                    x={cx - 600} y={cy + 150} width={1200} align="center"
                    scaleY={-1}
                    text={`${(raum.flaecheMm2 / 1_000_000).toFixed(2)} m²`}
                    fontSize={220 } fill={FARBEN.gedaempft}
                  />
                </Group>
              );
            })}

            {/* Wände + Bemaßung — gefüllte Bänder mit Gehrung (P2b-2), Fallback Linie. */}
            {waende.map((w) => {
              const ausgewaehlt = selectedNodeIds.includes(w.id);
              const mitte = punktAufWand(w.start, w.end, wandLaenge(w.start, w.end) / 2);
              const band = bandVon.get(w.id);
              const klick = (e: Konva.KonvaEventObject<MouseEvent>): void => {
                if (werkzeug === 'auswahl') {
                  e.cancelBubble = true;
                  store.getState().selectNodes([w.id]);
                }
              };

              return (
                <Group
                  key={w.id}
                  draggable={werkzeug === 'auswahl'}
                  onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; store.getState().selectNodes([w.id]); } }}
                  onDragEnd={(e) => {
                    const dx = e.target.x();
                    const dy = e.target.y();
                    e.target.position({ x: 0, y: 0 });
                    if (dx || dy) {
                      const g = versetzteWand(w.start, w.end, dx, dy);
                      store.getState().executeCommand({ type: 'MOVE_NODE', nodeId: w.id, position: { start: g.start, end: g.end } });
                    }
                  }}
                >
                  {band ? (
                    <Line
                      points={band.ecken.flatMap((p) => [p.x, p.y])}
                      closed
                      fill={ausgewaehlt ? FARBEN.auswahl : FARBEN.wandFuellung}
                      stroke={ausgewaehlt ? FARBEN.auswahl : FARBEN.wand}
                      strokeWidth={1.5 / zoom}
                      lineJoin="round"
                      onClick={klick}
                    />
                  ) : (
                    <Line
                      points={[w.start.x, w.start.y, w.end.x, w.end.y]}
                      stroke={ausgewaehlt ? FARBEN.auswahl : FARBEN.wand}
                      strokeWidth={w.thickness}
                      lineCap="butt"
                      onClick={klick}
                    />
                  )}
                  <Text
                    x={mitte.x - 500} y={mitte.y + w.thickness / 2 + 320} width={1000} align="center"
                    scaleY={-1}
                    text={`${Math.round(wandLaenge(w.start, w.end))} mm`}
                    fontSize={180} fill={FARBEN.gedaempft} listening={false}
                  />
                </Group>
              );
            })}

            {/* Öffnungen (Symbol auf der Wand; Status nicht nur Farbe: geklemmt trägt ⚠-Text) */}
            {nodes.filter(istOeffnung).map((o) => {
              const wand = waende.find((w) => w.id === o.hostWallId);
              if (!wand) {
                return null;
              }
              const mitte = punktAufWand(wand.start, wand.end, o.offsetFromWallStart + o.width / 2);
              const winkel = (Math.atan2(wand.end.y - wand.start.y, wand.end.x - wand.start.x) * 180) / Math.PI;
              const ausgewaehlt = selectedNodeIds.includes(o.id);

              return (
                <Group
                  key={o.id} x={mitte.x} y={mitte.y} rotation={winkel}
                  draggable={werkzeug === 'auswahl'}
                  onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; store.getState().selectNodes([o.id]); } }}
                  onDragEnd={(e) => {
                    const lot = lotAufWand({ x: e.target.x(), y: e.target.y() }, wand);
                    const laenge = wandLaenge(wand.start, wand.end);
                    const off = Math.round(Math.max(0, Math.min(lot.offset - o.width / 2, laenge - o.width)));
                    // Fix (Evaluator 4c1cfac): Konva-Position auf die Wandachse zuruecksetzen,
                    // analog zum Wand-Handler. Sonst strandet die Oeffnung bei Quer-Drag neben
                    // der Wand, weil react-konva unveraenderte Positions-Props nicht neu setzt.
                    const neueMitte = punktAufWand(wand.start, wand.end, off + o.width / 2);
                    e.target.position({ x: neueMitte.x, y: neueMitte.y });
                    store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: o.id, changes: { offsetFromWallStart: off } });
                  }}
                >
                  <Rect
                    x={-o.width / 2} y={-(wand.thickness / 2 + 40)} width={o.width} height={wand.thickness + 80}
                    fill="#ffffff" stroke={ausgewaehlt ? FARBEN.auswahl : o.type === 'door' ? FARBEN.gedaempft : FARBEN.linie}
                    strokeWidth={30}
                    onClick={(e) => {
                      if (werkzeug === 'auswahl') {
                        e.cancelBubble = true;
                        store.getState().selectNodes([o.id]);
                      }
                    }}
                  />
                  {o.type === 'window' && <Line points={[-o.width / 2, 0, o.width / 2, 0]} stroke={FARBEN.linie} strokeWidth={20} listening={false} />}
                  {o.type === 'door' && (() => {
                    const tb = tuerBlattGeometrie(o.width, o.anschlag ?? 'links', o.oeffnung ?? 'innen');
                    return (
                      <>
                        <Line points={[tb.angelpunkt.x, tb.angelpunkt.y, tb.blattEnde.x, tb.blattEnde.y]} stroke={ausgewaehlt ? FARBEN.auswahl : FARBEN.gedaempft} strokeWidth={28} listening={false} />
                        <Line points={tb.bogen.flatMap((p) => [p.x, p.y])} stroke={FARBEN.linie} strokeWidth={16} listening={false} />
                      </>
                    );
                  })()}
                  {o.clamped && (
                    <Text x={-o.width / 2} y={wand.thickness / 2 + 380} width={o.width} align="center" scaleY={-1} text="⚠ geklemmt" fontSize={160} fill={FARBEN.warnung} listening={false} />
                  )}
                </Group>
              );
            })}

            {/* Dächer (D-c): Traufkontur (gestrichelt) + First-Linie; die First-Linie zeigt die
                Richtung, aus der die Flächen-Azimute abgeleitet werden. Auswahl öffnet das Panel. */}
            {(scene.roofs ?? []).filter((r) => r.levelId === level.id && r.visible !== false).map((r) => {
              const rad = (r.firstAzimutGrad * Math.PI) / 180;
              const ux = Math.sin(rad), uy = Math.cos(rad);
              const cx = r.polygon.reduce((s, p) => s + p.x, 0) / r.polygon.length;
              const cy = r.polygon.reduce((s, p) => s + p.y, 0) / r.polygon.length;
              let sMin = Infinity, sMax = -Infinity;
              for (const p of r.polygon) {
                const s = p.x * ux + p.y * uy;
                if (s < sMin) sMin = s;
                if (s > sMax) sMax = s;
              }
              const halb = (sMax - sMin) / 2;
              const ausgewaehlt = selectedNodeIds.includes(r.id);
              const farbe = ausgewaehlt ? FARBEN.auswahl : '#b08968';

              return (
                <Group key={r.id}>
                  <Line
                    points={r.polygon.flatMap((p) => [p.x, p.y])}
                    closed stroke={farbe} strokeWidth={40} dash={[300, 200]}
                    onClick={(e) => {
                      if (werkzeug === 'auswahl') {
                        e.cancelBubble = true;
                        store.getState().selectNodes([r.id]);
                      }
                    }}
                  />
                  {r.roofType !== 'flach' && (
                    <Line points={[cx - halb * ux, cy - halb * uy, cx + halb * ux, cy + halb * uy]} stroke={farbe} strokeWidth={90} listening={false} />
                  )}
                  <Text x={cx - 1000} y={cy - 260} width={2000} align="center" scaleY={-1} text={`Dach · ${r.roofType} · ${r.neigungGrad}°`} fontSize={200} fill={FARBEN.gedaempft} listening={false} />
                </Group>
              );
            })}

            {/* Vorschau beim Wandzeichnen */}
            {werkzeug === 'wand' && wandStart && (
              <Group listening={false}>
                <Line points={[wandStart.x, wandStart.y, mitWinkelSnap(wandStart, cursor).x, mitWinkelSnap(wandStart, cursor).y]} stroke={FARBEN.auswahl} strokeWidth={60} dash={[200, 120]} />
                <Circle x={wandStart.x} y={wandStart.y} radius={90} fill={FARBEN.auswahl} />
              </Group>
            )}
          </Layer>
        </Stage>
        </div>
        <DreiDBereich sichtbar={modus !== '2d'} />
        {/* Rechtes Eigenschaften-Panel (immer sichtbar; Dach-Parameter oder Kontext) */}
        <div style={{ width: 268, flex: '0 0 auto', background: '#fff', borderLeft: '1px solid #e5e7eb', padding: 14, overflowY: 'auto', fontSize: 12.5, color: FARBEN.text }}>
          <div style={{ fontWeight: 800, fontSize: 11.5, textTransform: 'uppercase', letterSpacing: '.04em', color: FARBEN.gedaempft, marginBottom: 12 }}>Eigenschaften</div>
          {selectedRoof ? (
            <>
              <div style={{ fontWeight: 700, marginBottom: 10 }}>Dach</div>
              <label style={panelLabel}>Dachform
                <select value={selectedRoof.roofType} onChange={(e) => aktualisiereDach({ roofType: e.target.value as RoofNode['roofType'] })} style={panelInput}>
                  <option value="sattel">Satteldach</option>
                  <option value="walm">Walmdach</option>
                  <option value="pult">Pultdach</option>
                  <option value="flach">Flachdach</option>
                </select>
              </label>
              <label style={panelLabel}>Neigung (°)
                <input type="number" min={0} max={89} value={selectedRoof.neigungGrad} onChange={(e) => aktualisiereDach({ neigungGrad: Math.max(0, Math.min(89, Number(e.target.value))) })} style={panelInput} />
              </label>
              <label style={panelLabel}>First-Richtung (° Azimut, Nord=0)
                <input type="number" value={selectedRoof.firstAzimutGrad} onChange={(e) => aktualisiereDach({ firstAzimutGrad: Number(e.target.value) })} style={panelInput} />
              </label>
              <label style={panelLabel}>Überstand (mm)
                <input type="number" min={0} value={selectedRoof.ueberstandMm} onChange={(e) => aktualisiereDach({ ueberstandMm: Math.max(0, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <button type="button" style={{ ...knopf(false), width: '100%', marginTop: 4 }} onClick={() => { store.getState().executeCommand({ type: 'REMOVE_ROOF', roofId: selectedRoof.id }); store.getState().selectNodes([]); }}>Dach entfernen</button>
            </>
          ) : selectedWall ? (
            <>
              <div style={{ fontWeight: 700, marginBottom: 10 }}>Wand</div>
              <label style={panelLabel}>Mauerwerk
                <select value={selectedWall.construction?.materialId ?? ''} onChange={(e) => aktualisiereWand({ construction: { ...(selectedWall.construction ?? {}), materialId: e.target.value } })} style={panelInput}>
                  <option value="">— wählen —</option>
                  {MAUERWERK.map((m) => (<option key={m.id} value={m.id}>{m.label}</option>))}
                </select>
              </label>
              <label style={panelLabel}>Wandstärke (mm)
                <select value={WANDSTAERKEN.includes(selectedWall.thickness as typeof WANDSTAERKEN[number]) ? selectedWall.thickness : ''} onChange={(e) => aktualisiereWand({ thickness: Number(e.target.value) })} style={panelInput}>
                  {!WANDSTAERKEN.includes(selectedWall.thickness as typeof WANDSTAERKEN[number]) && (<option value="">{selectedWall.thickness} mm (aktuell)</option>)}
                  {WANDSTAERKEN.map((d) => (<option key={d} value={d}>{d} mm</option>))}
                </select>
              </label>
              <label style={panelLabel}>Höhe (mm)
                <input type="number" min={100} value={selectedWall.height} onChange={(e) => aktualisiereWand({ height: Math.max(100, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <label style={panelLabel}>Länge (mm)
                <input type="number" min={1} value={Math.round(Math.hypot(selectedWall.end.x - selectedWall.start.x, selectedWall.end.y - selectedWall.start.y))} onChange={(e) => setzeWandLaenge(Math.max(1, Math.round(Number(e.target.value))))} style={panelInput} />
              </label>
              <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 8 }}>Länge ändern verschiebt das Wandende entlang der Achse. Bewegen: Wand im Plan ziehen.</div>
              <div style={{ display: 'flex', gap: 6, marginTop: 8 }}>
                <button type="button" style={{ ...knopf(false), flex: 1 }} onClick={dupliziere}>Duplizieren</button>
                <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
              </div>
            </>
          ) : selectedOpening ? (
            <>
              <div style={{ fontWeight: 700, marginBottom: 10 }}>{selectedOpening.type === 'door' ? 'Tür' : selectedOpening.type === 'window' ? 'Fenster' : 'Öffnung'}</div>
              {selectedOpening.type === 'door' && (
                <>
                  <label style={panelLabel}>Anschlag (Angel)
                    <select value={selectedOpening.anschlag ?? 'links'} onChange={(e) => aktualisiereOeffnung({ anschlag: e.target.value as 'links' | 'rechts' })} style={panelInput}>
                      <option value="links">links</option>
                      <option value="rechts">rechts</option>
                    </select>
                  </label>
                  <label style={panelLabel}>Öffnungsrichtung
                    <select value={selectedOpening.oeffnung ?? 'innen'} onChange={(e) => aktualisiereOeffnung({ oeffnung: e.target.value as 'innen' | 'aussen' })} style={panelInput}>
                      <option value="innen">nach innen</option>
                      <option value="aussen">nach außen</option>
                    </select>
                  </label>
                </>
              )}
              <label style={panelLabel}>Breite (mm)
                <input type="number" min={100} value={selectedOpening.width} onChange={(e) => aktualisiereOeffnung({ width: Math.max(100, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <label style={panelLabel}>Höhe (mm)
                <input type="number" min={100} value={selectedOpening.height} onChange={(e) => aktualisiereOeffnung({ height: Math.max(100, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <label style={panelLabel}>Brüstungshöhe (mm)
                <input type="number" min={0} value={selectedOpening.sillHeight ?? 0} onChange={(e) => aktualisiereOeffnung({ sillHeight: Math.max(0, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <label style={panelLabel}>Position ab Wandanfang (mm)
                <input type="number" min={0} value={selectedOpening.offsetFromWallStart} onChange={(e) => aktualisiereOeffnung({ offsetFromWallStart: Math.max(0, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 10 }}>Maße direkt hier ändern — oder die Öffnung im Plan entlang der Wand ziehen.</div>
              <div style={{ display: 'flex', gap: 6, marginTop: 8 }}>
                <button type="button" style={{ ...knopf(false), flex: 1 }} onClick={dupliziere}>Duplizieren</button>
                <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
              </div>
            </>
          ) : (
            <div style={{ color: FARBEN.gedaempft, lineHeight: 1.7 }}>
              <div style={{ fontWeight: 700, color: FARBEN.text, marginBottom: 6 }}>Grundriss spiegeln</div>
              <div style={{ display: 'flex', gap: 6, marginBottom: 12 }}>
                <button type="button" style={{ ...knopf(false), flex: 1 }} onClick={() => spiegeleGrundriss('vertikal')} disabled={waende.length === 0}>↔ Links/Rechts</button>
                <button type="button" style={{ ...knopf(false), flex: 1 }} onClick={() => spiegeleGrundriss('horizontal')} disabled={waende.length === 0}>↕ Oben/Unten</button>
              </div>
              <div style={{ fontSize: 11.5, marginBottom: 10 }}>Objekt anklicken (Auswahl-Werkzeug) = markieren; dann ziehen zum Bewegen, oder Duplizieren/Löschen.</div>
              <div style={{ fontSize: 12 }}>Werkzeug: <strong style={{ color: FARBEN.text }}>{werkzeug}</strong></div>
              <div style={{ fontSize: 12 }}>Geschoss: <strong style={{ color: FARBEN.text }}>{level.name}</strong></div>
              <div style={{ fontSize: 12 }}>Räume: {raeume.length} · {(raeume.reduce((acc, r) => acc + r.flaecheMm2, 0) / 1_000_000).toFixed(2)} m²</div>
              <div style={{ marginTop: 12, padding: 10, background: '#f9fafb', border: '1px solid #e5e7eb', borderRadius: 8, fontSize: 11.5 }}>
                Ein Dach auswählen zeigt hier seine Parameter. Ablauf: Wand ziehen (W) → Dach (D) über den Umriss → 3D.
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Statusleiste */}
      <div style={{ display: 'flex', gap: 16, alignItems: 'center', padding: '7px 14px', background: '#fff', borderTop: '1px solid #e5e7eb', fontSize: 12, color: FARBEN.gedaempft }}>
        <span>x {cursor.x} mm · y {cursor.y} mm</span>
        <span>Zoom {(zoom * 100).toFixed(0)} %</span>
        <span>Räume: {raeume.length} · Fläche gesamt: {(raeume.reduce((s, r) => s + r.flaecheMm2, 0) / 1_000_000).toFixed(2)} m²</span>
        {werkzeug === 'wand' && <span style={{ color: FARBEN.text }}>{wandStart ? 'Klick = nächster Wandpunkt · Esc beendet den Zug' : 'Klick setzt den Wandanfang'}</span>}
        {(werkzeug === 'fenster' || werkzeug === 'tuer') && <span style={{ color: FARBEN.text }}>Klick nahe einer Wand platziert die Öffnung</span>}
        {werkzeug === 'dach' && <span style={{ color: FARBEN.text }}>Klick legt ein Dach über den Gebäude-Umriss (ein Dach je Geschoss) — dann in 3D umschalten</span>}
        <span style={{ flex: 1 }} />
        {letzteAblehnung && <span style={{ color: FARBEN.warnung, fontWeight: 600 }}>✋ {letzteAblehnung}</span>}
      </div>
    </div>
  );
}
