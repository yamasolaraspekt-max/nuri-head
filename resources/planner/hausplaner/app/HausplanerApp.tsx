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
import type { ObjectNode, OpeningNode, RoofNode, RoofAnbauMasse, SceneNode, WallNode } from '../domain/scene.types';
import { istVerschneidungsForm } from '../domain/roofShape';
import { T } from './studioDaten';
import { erkenneRaeume } from '../geometry/roomDetection';
import { bemassung } from '../geometry/bemassung';
import { wandLaenge, punktAufWand, wandBaender, tuerBlattGeometrie, type Punkt } from '../geometry/wallGeometry';
import { TUER_TYPEN, FENSTER_TYPEN, tuerTyp, fensterTyp, type TuerTyp, type FensterTyp } from '../geometry/oeffnungsTypen';
import { DreiDBereich } from './DreiDBereich';
import { ZustandBadge } from './studioUi';
import { PANEL_TABS, type PanelTabId } from './dashboard/panelTabs';
import { aufloeseAuswahlmodus, wendeAuswahlAn, klickInsLeere } from './tools/auswahlModus';
import { mehrfachUebersicht } from './tools/auswahlUebersicht';
import { EngineFlaeche } from './EngineFlaeche';
import { enginePanel } from './dashboard/enginePanels';
import { faehigkeitNach } from './tools/faehigkeiten';
import { ReiterLeiste } from './dashboard/ReiterLeiste';
import { SCHIENEN_REITER, SCHIENE_STANDARD, type SchienenReiterId } from './dashboard/schienenReiter';
import { ARBEITSBEREICHE, arbeitsbereich } from './dashboard/arbeitsbereiche';
import { gruppenFuer } from './dashboard/werkzeugGruppen';
import { ladeArbeitsbereich, speichereArbeitsbereich } from './state/arbeitsbereichSpeicher';
import {
  FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_WAND_DA,
  FAEHIGKEIT_ANSICHT_BEREIT, RECHT_BEARBEITEN,
} from './tools/vorbedingungen';
import { projektBaum, PROJEKTBAUM_LEER } from './dashboard/projektBaum';
import { befundeAus, BEFUNDE_LEER, BEFUNDE_UMFANG } from './dashboard/befunde';
import { palettenEintraege, PALETTE_LEER } from './dashboard/palette';
import { usePlannerUiStore } from './state/uiState';
import { toolFuerShortcut, toolNach } from './tools/toolRegistry';
import { zoneTools } from './tools/toolPresentation';
import { WERKZEUG_GRUPPEN } from './dashboard/werkzeugGruppen';
import { WerkzeugGruppenMenue } from './dashboard/WerkzeugGruppenMenue';
import { ladeAngeheftet, speichereAngeheftet, umschalten } from './state/angeheftet';
import { resolveToolState } from './tools/activation';
import { baueAktivierungsKontext } from './tools/toolContext';
import type { ObjectType, ViewType } from './tools/toolTypes';
import { versetzteWand, spiegelteWand, bbox as punkteBbox, achsenMitte, type Achse } from '../geometry/editierGeometrie';
import { dupliziereGeschoss } from '../geometry/geschossVorlage';
import { treppe2DSymbol } from '../geometry/treppe2D';
import { berechneTreppe } from '../geometry/treppenBerechnung';
import { treppeZuParametern, parametereZuTreppe, type TreppeParams } from '../geometry/treppeObjekt';
import { PROFIL_KATALOG, VERGLASUNG_KATALOG, berechneUw, rcMachbar, preisFenster, profilNach, verglasungNach, type OeffnungsArt, type RcKlasse } from '../geometry/fensterProdukt';
import { FENSTER_BAUARTEN, TUER_BAUARTEN, fensterBauartNach, tuerBauartNach } from '../geometry/oeffnungsBauarten';
import { TREPPEN_BAUARTEN, treppenBauartNach } from '../geometry/treppenBauarten';
import { FaehigkeitenNavi } from './FaehigkeitenNavi';

// Basis-URL der Icon-Assets — aus dem Bundle-Standort abgeleitet (traegt Subpfad/Domain).
const ICON_BASE = new URL('.', import.meta.url).href;

type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke';

/**
 * Dashboard v2 / B3 (§20 UI-5, Nacharbeit N3) — die Verknüpfung Reiter ↔ Inhaltsbereich.
 *
 * Es gibt EINEN Inhaltsbereich für alle vier Reiter (der Inhalt wird ausgetauscht, nicht das
 * Element). Deshalb zeigt `aria-controls` jedes Reiters auf DIESELBE, immer vorhandene `id` —
 * ein Verweis ins Leere wäre schlimmer als kein Verweis. Umgekehrt sagt `aria-labelledby` am
 * Panel, welcher Reiter es gerade beschriftet.
 *
 * Kante 5 (ID-Kollision): beide IDs tragen das Präfix `hp-eigenschaften-`. Stünde das Panel
 * eines Tages zweimal im Baum, ist das Präfix die Stelle, an der eine Instanz-Nummer eingezogen
 * wird — nicht 20 verstreute Zeichenketten.
 */
const PANEL_ID = 'hp-eigenschaften-panel';
const reiterId = (id: PanelTabId): string => `hp-eigenschaften-tab-${id}`;

/**
 * AUF-27 — dasselbe für die linke Schiene. Eigenes Präfix, damit die drei Schienen-Reiter und die
 * vier Panel-Reiter im selben Dokument keine id teilen (Kante 5, jetzt zweifach relevant).
 */
const SCHIENE_ID = 'hp-schiene-panel';
const schienenReiterId = (id: string): string => `hp-schiene-tab-${id}`;

/** AUF-34 — dasselbe für den Arbeitsbereich-Wähler. Eigenes Präfix, keine id-Kollision. */
const BEREICH_ID = 'hp-bereich-gruppenzeile';
const bereichReiterId = (id: string): string => `hp-bereich-tab-${id}`;
/** Die Bereiche als Reiter-Daten — einmal auf Modulebene, nicht bei jedem Render neu gebaut. */
const bereichReiter = ARBEITSBEREICHE.map((b) => ({ id: b.id, label: b.label, hinweis: b.hinweis }));

const FARBEN = {
  text: T.ink, gedaempft: T.muted, linie: T.faint, raster: T.canvasGrid, rasterGrob: T.canvasGridStrong,
  wand: T.canvasWall, wandFuellung: T.canvasWallFill, auswahl: T.brand, raum: T.brandGhost,
  warnung: T.warnInk, gefahr: T.errInk, erfolg: T.okInk,
} as const;

// L1 Layout-Aktivierung — Navigations-Stile (tokens-konform: neutral, Marke nur als Auswahl-Akzent).
const navGrp: React.CSSProperties = { fontSize: 10, fontWeight: 700, letterSpacing: '0.06em', textTransform: 'uppercase', color: T.muted, padding: '12px 12px 4px' };
const navItem = (aktiv: boolean): React.CSSProperties => ({ display: 'flex', alignItems: 'center', gap: 9, textAlign: 'left', width: 'calc(100% - 12px)', margin: '1px 6px', padding: '8px 8px', border: 'none', borderRadius: 8, cursor: 'pointer', fontSize: 13, background: aktiv ? T.brandWash : 'transparent', color: aktiv ? T.brandInk : T.ink, fontWeight: aktiv ? 700 : 500 });
const navHub: React.CSSProperties = { fontSize: 12.5, fontWeight: 600, color: T.ink, padding: '8px 12px 2px' };
const navSub: React.CSSProperties = { fontSize: 12.5, color: T.muted, padding: '6px 12px 6px 22px' };
// Batch 0: die frühere FACHPLANER-Attrappe (inerte `geplant`-Labels) ist durch die datengetriebene
// Fähigkeiten-Navi (app/tools/faehigkeiten.ts + FaehigkeitenNavi) ersetzt — eine Wahrheit, mit Zustand.

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
    case 'decke': return svgWrap(<><rect x="3" y="9" width="18" height="5" rx="1" /><path d="M13 9v5" /></>);
    case 'treppe': return svgWrap(<path d="M3 21h4v-4h4v-4h4v-4h4" />);
    default: return svgWrap(<circle cx="12" cy="12" r="3" />);
  }
}
// Bedien-Icons (Ansicht/Bearbeiten/Messen&Export) — je mit Tooltip + Funktionsbeschreibung.
function opIcon(name: string): React.ReactElement {
  switch (name) {
    case 'zoom-in': return svgWrap(<><circle cx="11" cy="11" r="7" /><path d="M21 21l-4.35-4.35M11 8v6M8 11h6" /></>);
    case 'zoom-out': return svgWrap(<><circle cx="11" cy="11" r="7" /><path d="M21 21l-4.35-4.35M8 11h6" /></>);
    case 'zoom-reset': return svgWrap(<><circle cx="12" cy="12" r="8" /><circle cx="12" cy="12" r="2" /></>);
    case 'einpassen': return svgWrap(<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" />);
    case 'grid': return svgWrap(<><rect x="4" y="4" width="16" height="16" rx="1" /><path d="M4 10h16M4 16h16M10 4v16M16 4v16" /></>);
    case 'fang': return svgWrap(<><path d="M12 3v6M12 15v6M3 12h6M15 12h6" /><circle cx="12" cy="12" r="2.5" /></>);
    case 'dup': return svgWrap(<><rect x="9" y="9" width="11" height="11" rx="2" /><path d="M5 15V5a2 2 0 0 1 2-2h8" /></>);
    case 'del': return svgWrap(<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" />);
    case 'mirror-h': return svgWrap(<path d="M12 3v18M8 7l-4 5 4 5M16 7l4 5-4 5" />);
    case 'mirror-v': return svgWrap(<path d="M3 12h18M7 8l5-4 5 4M7 16l5 4 5-4" />);
    case 'drehen': return svgWrap(<><path d="M21 12a9 9 0 1 1-3-6.7" /><path d="M21 3v5h-5" /></>);
    case 'messen': return svgWrap(<><path d="M3 15l12-12 6 6-12 12z" /><path d="M8 8l2 2M11 5l2 2M5 11l2 2" /></>);
    case 'bemassung': return svgWrap(<path d="M3 8h18M5 6v4M19 6v4M9 6v4M13 6v4M8 16h8" />);
    case 'export': return svgWrap(<path d="M12 3v12M8 7l4-4 4 4M5 15v4a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4" />);
    case 'pdf': return svgWrap(<><path d="M7 3h7l4 4v14H7z" /><path d="M9 13h2a1.5 1.5 0 0 0 0-3H9v6M15 11h-2v5" /></>);
    default: return svgWrap(<circle cx="12" cy="12" r="3" />);
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

/**
 * Dashboard v2.1 (§19 / UI-4) — Kontext-Options-Leiste unter der Bedienleiste.
 *
 * Die Zuordnung Werkzeug → Optionen ist EIN `switch` über `activeToolId`: Bedingung und
 * Steuerelement liegen im selben `case` und können nicht auseinanderlaufen. Eine Parallelliste
 * (Werkzeug hier, Option dort) wäre genau die zweite Wahrheit, die wir sonst überall abbauen.
 *
 * **AUF-16 / Befund B1 — warum diese Komponente auf MODULEBENE steht und nicht im Rumpf von
 * `HausplanerApp`:** eine im Rumpf definierte Komponente bekommt bei JEDEM Render eine neue
 * Typ-Identität; React reißt ihren Teilbaum ab und baut ihn neu auf. `onMouseMove` rendert
 * `HausplanerApp` in Mausbewegungs-Frequenz — der `<select>` hier ist fokussierbar, also gingen
 * Fokus und Tastaturbedienung fortlaufend verloren. Bei zustandslosen Rumpf-Komponenten wie
 * `OpBtn` ist dasselbe Muster folgenlos; hier nicht. Deshalb: Modulebene, Werte als **explizite
 * Props** statt über Closure.
 *
 * Erweiterungspunkt (NICHT hier bauen): in v5 wird dieser `switch` durch einen Deskriptor aus
 * der Tool-Registry ersetzt, sobald die Zonen aus `toolPresentation.ts` die Leiste speisen.
 */
function KontextOptionenLeiste({
  werkzeug, fensterTypWahl, tuerTypWahl, setFensterTypWahl, setTuerTypWahl, fremderBereich,
}: {
  werkzeug: Werkzeug;
  fensterTypWahl: FensterTyp;
  tuerTypWahl: TuerTyp;
  setFensterTypWahl: (t: FensterTyp) => void;
  setTuerTypWahl: (t: TuerTyp) => void;
  /** AUF-34 / Kante 3: gesetzt, wenn das aktive Werkzeug im gewählten Arbeitsbereich nicht gilt. */
  fremderBereich?: string;
}): React.ReactElement {
  const aktivesTool = toolNach(werkzeug);
  const optionen = ((): React.ReactElement => {
    switch (werkzeug) {
      case 'fenster':
      case 'tuer': {
        const istFenster = werkzeug === 'fenster';
        return (
          <label style={{ fontSize: 12, color: FARBEN.gedaempft, display: 'flex', alignItems: 'center', gap: 5 }}>
            {istFenster ? 'Fenstertyp' : 'Türtyp'}
            <select
              value={istFenster ? fensterTypWahl : tuerTypWahl}
              onChange={(e) => (istFenster ? setFensterTypWahl(e.target.value as FensterTyp) : setTuerTypWahl(e.target.value as TuerTyp))}
              style={{ fontSize: 12.5, padding: '4px 8px', borderRadius: 8, border: `1px solid ${T.controlBorder}` }}
            >
              {(istFenster ? FENSTER_TYPEN : TUER_TYPEN).map((v) => (
                <option key={v.typ} value={v.typ}>{v.label} · {v.breite}×{v.hoehe} mm</option>
              ))}
            </select>
          </label>
        );
      }
      default:
        return (
          <span style={{ display: 'inline-flex', alignItems: 'center', gap: 7, fontSize: 12, color: FARBEN.gedaempft }}>
            Für dieses Werkzeug sind noch keine Optionen hinterlegt.
            <ZustandBadge zustand="in_entwicklung" />
          </span>
        );
    }
  })();
  return (
    <div style={{ flex: '0 0 auto', display: 'flex', alignItems: 'center', gap: 10, padding: '5px 14px', fontSize: 12, background: T.surface2, borderBottom: `1px solid ${T.hair}` }}>
      <span style={{ fontWeight: 700, color: FARBEN.text }}>{aktivesTool?.label ?? 'Werkzeug'}</span>
      <span style={{ width: 1, height: 16, background: T.hair }} />
      {/* AUF-34 / Kante 3: Ein Bereichswechsel darf das aktive Werkzeug NICHT stillschweigend
          abwählen. Es bleibt gewählt — und sagt hier sichtbar, warum es gerade nicht greift.
          Vorher stand die Begründung nur im `title` der Leiste, also faktisch nirgends. */}
      {fremderBereich ? (
        <span style={{ display: 'inline-flex', alignItems: 'center', gap: 7, fontSize: 12, color: FARBEN.gedaempft, overflowWrap: 'anywhere' }}>
          Gehört zum Arbeitsbereich <strong style={{ color: FARBEN.text }}>{fremderBereich}</strong> — hier nicht verfügbar. Bereich oben wechseln.
          <ZustandBadge zustand="voraussetzung" />
        </span>
      ) : optionen}
    </div>
  );
}

export function HausplanerApp({ imStudio = false }: { imStudio?: boolean } = {}): React.ReactElement {
  const scene = useHausplanerStore((s) => s.scene);
  const activeLevelId = useHausplanerStore((s) => s.activeLevelId);
  const selectedNodeIds = useHausplanerStore((s) => s.selectedNodeIds);
  /**
   * AUF-35a: Das Panel zeigt das **Primärobjekt**, nicht „das eine ausgewählte". Vorher stand an
   * fünf Stellen `selectedNodeIds.length === 1` — damit war jede Mehrfachauswahl blind, obwohl der
   * Store sie längst konnte. Jetzt führt `primaerId`, und bei Mehrfachauswahl zeigt das Panel eine
   * eigene Übersicht statt Einzelfelder zu raten (Kante 4).
   */
  const primaerId = useHausplanerStore((s) => s.primaerId);
  const speicherStatus = useHausplanerStore((s) => s.speicherStatus);
  const konfliktRevision = useHausplanerStore((s) => s.konfliktRevision);
  const letzteAblehnung = useHausplanerStore((s) => s.letzteAblehnung);
  const modus = useHausplanerStore((s) => s.modus);
  const store = useHausplanerStore;

  // UI-2: aktives Werkzeug liegt jetzt im GETEILTEN UI-State (state/uiState.ts), nicht mehr lokal —
  // dadurch für Studio-Shell/Kontextleiste/Activation-Engine lesbar. Variablennamen bleiben, damit
  // die bestehenden Nutzungen unverändert bleiben (verhaltensgleich).
  const werkzeug = usePlannerUiStore((s) => s.activeToolId) as Werkzeug;
  const setWerkzeug = React.useCallback((w: Werkzeug) => usePlannerUiStore.getState().setActiveTool(w), []);
  useEffect(() => { usePlannerUiStore.getState().reset(); }, []); // Mount: wie bisher mit 'auswahl' starten
  // Dashboard v1 §8: bei ungespeicherten Änderungen VOR dem Verlassen bestätigen (kein stiller Verlust).
  useEffect(() => {
    if (speicherStatus !== 'ungespeichert') return undefined;
    const warnen = (e: BeforeUnloadEvent): void => { e.preventDefault(); e.returnValue = ''; };
    window.addEventListener('beforeunload', warnen);
    return () => window.removeEventListener('beforeunload', warnen);
  }, [speicherStatus]);
  const [treppeStart, setTreppeStart] = useState<{ x: number; y: number } | null>(null);
  const [fensterTypWahl, setFensterTypWahl] = useState<FensterTyp>('drehkipp');
  const [tuerTypWahl, setTuerTypWahl] = useState<TuerTyp>('dreh1');
  /** Dashboard v2.2: aktiver Panel-Reiter. Bewusst LOKAL, kein Store-Feld — der Wert hat genau
   *  einen Leser; ob Panelzustand in den UI-State gehört, ist eine v4-Frage (F1). */
  const [aktiverTab, setAktiverTab] = useState<PanelTabId>('allgemein');
  /**
   * AUF-27: aktiver Reiter der linken Schiene. Ebenfalls LOKAL — und bewusst NICHT gespeichert:
   * Kante 2 lässt `localStorage` zu, verlangt es aber nicht. Der Wert ist ein Arbeitskontext für
   * den Moment, keine Vorliebe wie die Anheftung (★); nach dem Neuladen ist der häufigste Job der
   * richtige Startpunkt. Ins Szenendokument gehört er unter keinen Umständen — kein Feld, kein Zod,
   * kein Schema. Sollte er später überleben sollen, ist DIESE Zeile die einzige Stelle dafür.
   */
  const [schienenTab, setSchienenTab] = useState<SchienenReiterId>(SCHIENE_STANDARD);
  /**
   * AUF-33/L2: die geöffnete Engine-Fläche. Lokal — sie hat genau einen Leser und gehört weder
   * ins Szenendokument noch in den UI-Store (dieselbe Begründung wie beim Schienen-Reiter).
   */
  const [offeneEngine, setOffeneEngine] = useState<string | null>(null);
  /** I4: persoenlich angeheftete Werkzeuge (★). Liegt in localStorage, NIE im Szenendokument —
   *  eine Vorliebe des Bedieners ist keine Eigenschaft des Gebaeudes. */
  const [angeheftet, setAngeheftet] = useState<Set<string>>(() => ladeAngeheftet());
  const [offeneGruppe, setOffeneGruppe] = useState<string | null>(null);
  const heftUm = (id: string): void => {
    const neu = umschalten(angeheftet, id);
    setAngeheftet(neu);
    speichereAngeheftet(neu);
  };
  /* B4 (Nacharbeit N3): Die DOM-Referenzen der Reiter liegen seit AUF-27 in `ReiterLeiste` —
     dort, wo die Pfeiltasten sie brauchen. Eine Leiste, zwei Benutzer, eine Verdrahtung. */
  /** Dashboard v2.5: Zustand der Command-Palette. Ebenfalls LOKAL — der Wert hat genau einen
   *  Leser, und v2 ändert den Store nicht. `paletteOffenRef` spiegelt `paletteOffen` für den
   *  globalen Tastatur-Handler, dessen Closure sonst veraltet wäre (Kante 8). */
  const [paletteOffen, setPaletteOffen] = useState(false);
  const [paletteFilter, setPaletteFilter] = useState('');
  const [paletteIndex, setPaletteIndex] = useState(0);
  const paletteOffenRef = useRef(false);
  const [wandStart, setWandStart] = useState<Punkt | null>(null);
  const [cursor, setCursor] = useState<Punkt>({ x: 0, y: 0 });
  const [zoom, setZoom] = useState(0.12); // px pro mm
  const [rasterAn, setRasterAn] = useState(true);
  const stageRef = useRef<Konva.Stage | null>(null);

  const level = scene?.levels.find((l) => l.id === activeLevelId) ?? scene?.levels[0] ?? null;
  const nodes = useMemo(
    () => (scene && level ? scene.nodes.filter((n) => n.levelId === level.id) : []),
    [scene, level],
  );
  const waende = useMemo(() => nodes.filter(istWand), [nodes]);
  /** AUF-35a / Kante 4: Zählung der Auswahl je Typ — rein, getestet, nur Anzeige. */
  const auswahlUebersicht = useMemo(() => mehrfachUebersicht(selectedNodeIds, nodes), [selectedNodeIds, nodes]);

  // UI-3: Aktivierungs-Kontext für die Werkzeugleiste (§21). Sammelt die getrennten Wahrheiten:
  // Arbeitsbereich (UI-State) · Ansicht (store.modus) · Auswahltypen (Modell). Rechte sind im
  // Editor angenommen; die Werkzeugleisten-Werkzeuge (art='werkzeug') prüfen ohnehin keine Rechte.
  const activeWorkspace = usePlannerUiStore((s) => s.activeWorkspace);
  /**
   * AUF-34 — Arbeitsbereich wählen und merken. **Kein zweiter Zustand:** die Wahrheit bleibt
   * `activeWorkspace` im UI-Store; `localStorage` ist nur ein Gedächtnis über den Neuladen hinweg
   * (Kante 4) und **niemals** das Szenendokument. Der Ladeschritt läuft einmal beim Mounten, nach
   * `reset()` — sonst überschriebe der Grundzustand die Wahl sofort wieder.
   */
  const waehleBereich = React.useCallback((id: string) => {
    usePlannerUiStore.getState().setActiveWorkspace(id);
    speichereArbeitsbereich(id);
  }, []);
  useEffect(() => {
    const gemerkt = ladeArbeitsbereich();
    if (gemerkt) usePlannerUiStore.getState().setActiveWorkspace(gemerkt);
  }, []);
  /** Die Gruppen des gewählten Bereichs — durchgängige plus gebundene, in Themen-Reihenfolge. */
  const sichtbareGruppen = useMemo(() => gruppenFuer(activeWorkspace), [activeWorkspace]);
  /**
   * Welle A2 / §8.2 (P9): Die Werkzeuge der Leiste kommen aus der Fix-Zone — **memoisiert am
   * Aufrufort**, nicht im Modul gecacht. Leere Abhängigkeitsliste ist korrekt, weil
   * `TOOL_PRESENTATION_RULES` eine Modul-Konstante ist.
   *
   * Warum kein Modul-Cache (ausdrücklich verboten): die A1-Gegenproben arbeiten über `zoneToolsIn`
   * mit **veränderten** Regelsätzen. Ein Cache in `toolPresentation.ts` würde dort stillschweigend
   * alte Werte liefern und genau die Unterscheidungskraft zerstören, die N1 gerade belegt hat.
   * Ohne diese Memoisierung liefe pro Render eine Sortierung über 63 Regeln statt eines Filters
   * über 9 Registry-Einträge — `onMouseMove` rendert in Mausbewegungs-Frequenz.
   */
  const leistenWerkzeuge = useMemo(() => zoneTools('fix'), []);
  /** I4: die linke Leiste = Pflichtwerkzeuge + persoenlich Angeheftetes. Bewusst NICHT die 110 —
   *  eine Leiste mit 110 Eintraegen ist keine Leiste mehr. */
  const railWerkzeuge = useMemo(() => {
    const fix = zoneTools('fix');
    const feste = new Set(fix.map((w) => w.id));
    const zusatz = WERKZEUG_GRUPPEN.flatMap((g) => g.werkzeuge).filter((w) => angeheftet.has(w.id) && !feste.has(w.id));
    return [...fix, ...zusatz];
  }, [angeheftet]);
  const werkzeugKontext = useMemo(
    () =>
      baueAktivierungsKontext({
        workspace: activeWorkspace,
        view: modus as ViewType,
        selectionTypes: selectedNodeIds
          .map((id) => nodes.find((n) => n.id === id)?.type)
          .filter((t): t is NonNullable<typeof t> => Boolean(t)) as ObjectType[],
        permissions: [RECHT_BEARBEITEN],
        /**
         * AUF-36: die vier **messbaren** Vorbedingungen des Funktionsvertrags. Sie sind keine
         * Erfindung, sondern Tatsachen, die diese Komponente ohnehin kennt: Szene geladen, aktives
         * Geschoss, Wände im Geschoss, Zeichenfläche gemountet. Sie fließen über die **vorhandene**
         * `capabilities`-Liste — der dafür vorgesehene Haken, der bisher leer lag. Was der Planer
         * nicht messen kann (freigegebene Heizlast, ausgelegte Heizflächen …), steht hier
         * bewusst NICHT: ein Wert, den niemand kennt, wird nicht behauptet.
         */
        capabilities: [
          ...(scene ? [FAEHIGKEIT_PROJEKT_OFFEN] : []),
          ...(level ? [FAEHIGKEIT_GESCHOSS_DA] : []),
          ...(waende.length > 0 ? [FAEHIGKEIT_WAND_DA] : []),
          // Die Zeichenfläche ist gemountet, sobald diese Komponente rendert.
          FAEHIGKEIT_ANSICHT_BEREIT,
        ],
      }),
    [activeWorkspace, modus, selectedNodeIds, nodes, scene, level, waende.length],
  );
  /**
   * AUF-34 / Kante 3 — gilt das aktive Werkzeug im gewählten Arbeitsbereich? Der Name des fremden
   * Bereichs, sonst `undefined`. Gelesen wird `supportedWorkspaces`, also **dieselbe** Quelle, die
   * `resolveToolState` als erste Regel prüft — keine zweite Beurteilung.
   */
  const fremderBereich = useMemo(() => {
    const t = toolNach(werkzeug);
    if (!t || t.supportedWorkspaces.length === 0) return undefined;
    if (t.supportedWorkspaces.includes(activeWorkspace)) return undefined;
    return arbeitsbereich(t.supportedWorkspaces[0])?.label ?? t.supportedWorkspaces[0];
  }, [werkzeug, activeWorkspace]);

  // Fällt das aktive Werkzeug im aktuellen Kontext aus (z. B. Zeichnen in 3D), zurück auf Auswahl —
  // damit man nie in einem deaktivierten Werkzeug festhängt (§21/§28).
  // AUF-34 / Kante 3: **außer** wenn nur der Arbeitsbereich nicht passt. Ein Bereichswechsel darf
  // die Werkzeugwahl nicht stillschweigend wegräumen; das Werkzeug bleibt und die Kontextleiste
  // sagt sichtbar, wohin es gehört. Wer zurückwechselt, findet es noch vor.
  useEffect(() => {
    if (fremderBereich) return;
    const t = toolNach(werkzeug);
    if (t && !resolveToolState(t, werkzeugKontext).enabled) {
      usePlannerUiStore.getState().setActiveTool('auswahl');
    }
  }, [werkzeugKontext, werkzeug, fremderBereich]);
  /** Dashboard v2.3: Projektbaum des aktiven Geschosses — reine Funktion, nur LESEN. */
  const baum = useMemo(() => projektBaum(nodes, scene?.roofs, level), [nodes, scene, level]);
  /** Dashboard v2.4: Befunde aus dem heutigen Store-Zustand (heute 0 oder 1). */
  const befunde = useMemo(() => befundeAus(letzteAblehnung), [letzteAblehnung]);
  /** Dashboard v2.5: Paletten-Einträge im AKTUELLEN Werkzeug-Kontext — dieselbe Aktivierungs-
   *  Wahrheit wie die Werkzeugleiste, keine zweite Logik. */
  const paletteListe = useMemo(
    () => palettenEintraege(werkzeugKontext, paletteFilter),
    [werkzeugKontext, paletteFilter],
  );
  /** Markierte Zeile, gegen die Listenlänge geklemmt — die Liste kann sich unter der Palette
   *  ändern (z. B. wenn die Auswahl wegfällt), ohne dass der Index nachgeführt wurde. */
  const paletteMarkiert = paletteListe.length === 0 ? -1 : Math.min(paletteIndex, paletteListe.length - 1);
  const oeffnePalette = React.useCallback(() => {
    setPaletteFilter('');
    setPaletteIndex(0);
    paletteOffenRef.current = true;
    setPaletteOffen(true);
  }, []);
  const schliessePalette = React.useCallback(() => {
    paletteOffenRef.current = false;
    setPaletteOffen(false);
  }, []);
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
  const selectedRoof = scene?.roofs?.find((r) => primaerId === r.id) ?? null;
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
  const selectedWall = waende.find((w) => primaerId === w.id) ?? null;
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
  const selectedOpening = (nodes.find((n) => istOeffnung(n) && primaerId === n.id) ?? null) as OpeningNode | null;
  function aktualisiereOeffnung(changes: Partial<OpeningNode>): void {
    if (selectedOpening) {
      store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedOpening.id, changes });
    }
  }
  // Treppe (objectType 'stair'): genau EIN ausgewaehltes Treppen-Objekt -> Panel; UPDATE_NODE (additiv).
  const selectedStair = (nodes.find(
    (n): n is ObjectNode => n.type === 'object' && n.objectType === 'stair'
      && primaerId === n.id,
  ) ?? null);
  // Generisches Objekt (radiator/wallbox/… außer stair) — Auswahl fürs Panel.
  const selectedObjekt = (nodes.find(
    (n): n is ObjectNode => n.type === 'object' && n.objectType !== 'stair'
      && primaerId === n.id,
  ) ?? null);
  // Dashboard v1 §5: genau EIN selektierter Node (Wand/Öffnung/Objekt/Treppe) → Auge/Schloss verdrahten.
  const selectedNode = primaerId ? (nodes.find((n) => n.id === primaerId) ?? null) : null;
  const selectedStairParams: TreppeParams | null = selectedStair ? parametereZuTreppe(selectedStair.parameters) : null;
  function aktualisiereTreppe(aenderung: Partial<TreppeParams>): void {
    if (!selectedStair || !selectedStairParams) return;
    const neu = { ...selectedStairParams, ...aenderung };
    store.getState().executeCommand({
      type: 'UPDATE_NODE', nodeId: selectedStair.id,
      changes: { parameters: treppeZuParametern(neu) },
    });
  }
  // Editier-Operationen: Bewegen laeuft ueber das Ziehen (unten am Node); hier Loeschen/Duplizieren/Spiegeln.
  function loescheAuswahl(): void {
    for (const id of selectedNodeIds) {
      store.getState().executeCommand({ type: 'REMOVE_NODE', nodeId: id });
    }
    store.getState().selectNodes([]);
  }
  function exportPng(): void {
    const stage = stageRef.current;
    if (!stage) return;
    const uri = stage.toDataURL({ pixelRatio: 2 });
    const a = document.createElement('a');
    a.href = uri;
    a.download = 'grundriss.png';
    a.click();
  }
  const OpBtn = ({ title, onClick, icon, disabled, aktiv, geplant }: { title: string; onClick?: () => void; icon: string; disabled?: boolean; aktiv?: boolean; geplant?: boolean }): React.ReactElement => (
    <button type="button" title={geplant ? `${title} (geplant)` : title} onClick={geplant ? undefined : onClick} disabled={disabled || geplant}
      style={{ display: 'grid', placeItems: 'center', width: 32, height: 30, borderRadius: 8, border: `1px solid ${aktiv ? T.brandInk : T.controlBorder}`, background: aktiv ? T.brandWash : T.surface, color: (disabled || geplant) ? T.faint : FARBEN.text, cursor: (disabled || geplant) ? 'not-allowed' : 'pointer' }}>
      {opIcon(icon)}
    </button>
  );
  /**
   * Dashboard v2.5 — Enter in der Palette. Werkzeuge setzen den Modus (wie die Werkzeugleiste),
   * Aktionen rufen die BEREITS VORHANDENEN Funktionen `loescheAuswahl`/`dupliziere`. Es entsteht
   * kein zweiter Ausführungsweg; deaktivierte Einträge werden hier hart abgewiesen.
   */
  function aktivierePaletteEintrag(id: string, enabled: boolean): void {
    if (!enabled) return;
    schliessePalette();
    const tool = toolNach(id);
    if (!tool) return;
    if (tool.art === 'werkzeug') {
      setWandStart(null);
      setTreppeStart(null);
      setWerkzeug(tool.id as Werkzeug);
      return;
    }
    if (tool.id === 'loeschen') loescheAuswahl();
    else if (tool.id === 'duplizieren') dupliziere();
  }
  const opSep = (): React.ReactElement => <span style={{ width: 1, height: 20, background: T.hair, margin: '0 4px' }} />;
  const opLbl = (t: string): React.ReactElement => <span style={{ fontSize: 10, fontWeight: 700, letterSpacing: '.07em', textTransform: 'uppercase', color: T.muted, marginRight: 2 }}>{t}</span>;
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
  // Geschoss als Vorlage duplizieren (aktiviert die getestete Logik dupliziereGeschoss):
  // neues Geschoss darueber, Waende/Oeffnungen/Dach kopiert, Oeffnungen an die neuen Waende umgehaengt.
  function dupliziereGeschossJetzt(): void {
    if (!level) return;
    const roof = scene?.roofs?.find((r) => r.levelId === level.id) ?? null;
    const dup = dupliziereGeschoss(
      { id: level.id, name: level.name, elevation: level.elevation, defaultWallHeight: level.defaultWallHeight, floorThickness: level.floorThickness, sortOrder: level.sortOrder },
      nodes,
      roof,
      uuid,
      `${level.name} (Kopie)`,
    );
    const st = store.getState();
    if (!st.executeCommand({ type: 'ADD_LEVEL', level: dup.level })) return;
    for (const n of dup.nodes) { st.executeCommand({ type: 'ADD_NODE', node: n }); }
    if (dup.roof) { st.executeCommand({ type: 'ADD_ROOF', roof: dup.roof }); }
    st.setActiveLevel(dup.level.id);
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

  /**
   * AUF-35a — DIE eine Stelle, an der ein Klick zur Auswahl wird.
   *
   * Vorher rief jeder Knoten `selectNodes([id])` und ersetzte damit die Auswahl bedingungslos;
   * die Modifikatortasten wurden beim Klick gar nicht gelesen. Jetzt entscheidet die reine
   * Funktion `aufloeseAuswahlmodus` über den Modus und `wendeAuswahlAn` über den neuen Stand —
   * hier wird nur noch übergeben. Keine zweite Auswahl-Logik in den Renderer-Zweigen.
   */
  const waehleAn = React.useCallback((id: string, ev: MouseEvent): void => {
    const modus = aufloeseAuswahlmodus(ev);
    const s = store.getState();
    const neu = wendeAuswahlAn({ ids: s.selectedNodeIds, primaerId: s.primaerId }, id, modus);
    s.selectNodes(neu.ids, neu.primaerId);
  }, [store]);

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

    if (werkzeug === 'decke') {
      // Feature A: eine Decke je Geschoss über den Gebäude-Umriss; Treppen-Durchbrüche setzt der Command
      // (aus Grundriss). Ein bereits vorhandene Decke lehnt der Command ab (Meldung in der Statusleiste).
      const jetzt = new Date().toISOString();
      store.getState().executeCommand({
        type: 'ADD_CEILING',
        ceiling: {
          id: uuid(), type: 'ceiling', levelId: level.id, visible: true, locked: false, tags: [],
          createdAt: jetzt, updatedAt: jetzt,
          polygon: gebaeudeUmriss(), dickeMm: level.floorThickness,
        },
      });
      setWerkzeug('auswahl');

      return;
    }

    if (werkzeug === 'treppe') {
      if (!treppeStart) {
        setTreppeStart(p);
        return;
      }
      const ende = mitWinkelSnap(treppeStart, p);
      if (ende.x === treppeStart.x && ende.y === treppeStart.y) { return; }
      const jetzt = new Date().toISOString();
      const params = treppeZuParametern({
        startX: treppeStart.x, startY: treppeStart.y, endX: ende.x, endY: ende.y,
        laufbreite: 1000, geschosshoehe: level.defaultWallHeight, bereich: 'wohnung',
      });
      store.getState().executeCommand({
        type: 'ADD_NODE',
        node: {
          id: uuid(), type: 'object', objectType: 'stair', levelId: level.id,
          visible: true, locked: false, tags: [], createdAt: jetzt, updatedAt: jetzt,
          catalogItemId: 'treppe-standard',
          transform: {
            position: { x: treppeStart.x, y: treppeStart.y, z: 0 },
            rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 },
          },
          parameters: params,
        },
      });
      setTreppeStart(null);
      setWerkzeug('auswahl');
      return;
    }

    // Auswahl: Klick auf leere Fläche (Nodes stoppen die Propagation).
    // Kante 5: MIT Modifikator bleibt die Auswahl stehen — sonst verliert man beim Danebentreffen
    // die ganze Mehrfachauswahl.
    const s = store.getState();
    const neu = klickInsLeere({ ids: s.selectedNodeIds, primaerId: s.primaerId }, e.evt);
    if (neu !== undefined && (neu.ids !== s.selectedNodeIds || neu.primaerId !== s.primaerId)) {
      s.selectNodes(neu.ids, neu.primaerId);
    }
  }

  useEffect(() => {
    function taste(e: KeyboardEvent): void {
      if ((e.target as HTMLElement)?.tagName === 'INPUT') {
        return;
      }
      // Kante 8: solange die Palette offen ist, dürfen die Werkzeug-Kürzel NICHT durchschlagen —
      // sonst wechselt ein Tastendruck im Filterfeld das Werkzeug. Esc schließt (auch ohne Fokus
      // im Feld, etwa nach einem Klick auf den Hintergrund).
      if (paletteOffenRef.current) {
        if (e.key === 'Escape') {
          e.preventDefault();
          schliessePalette();
        }
        return;
      }
      if (e.key === 'Escape') {
        setWandStart(null);
        setTreppeStart(null);
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
      } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        // Dashboard v2.5 (§30 / UI-9): Command-Palette. Der Zweig steht VOR dem Kürzel-Zweig,
        // sonst griffe „K" (Registry-Kürzel von „Decke") auch mit Strg/⌘ — der bisherige
        // Kürzel-Zweig prüft die Modifikatoren nicht. OHNE Modifikator bleibt „K" = Decke.
        e.preventDefault();
        oeffnePalette();
      } else {
        // UI-3: Werkzeug-Tastenkürzel aus der Registry (eine Quelle), Aktivierung respektiert.
        const tool = toolFuerShortcut(e.key);
        if (tool && tool.art === 'werkzeug') {
          const ctx = baueAktivierungsKontext({
            workspace: usePlannerUiStore.getState().activeWorkspace,
            view: store.getState().modus as ViewType,
            selectionTypes: [],
            permissions: ['Hausplaner,update'],
          });
          if (resolveToolState(tool, ctx).enabled) {
            setWandStart(null);
            setTreppeStart(null);
            setWerkzeug(tool.id as Werkzeug);
          }
        }
      }
    }
    window.addEventListener('keydown', taste);

    return () => window.removeEventListener('keydown', taste);
  }, [store, oeffnePalette, schliessePalette]);

  if (!scene || !level) {
    return <div style={{ padding: 24, color: FARBEN.text }}>Szene nicht geladen.</div>;
  }

  const statusPill = {
    gespeichert: { text: 'Gespeichert', farbe: FARBEN.erfolg, grund: T.okSoft },
    ungespeichert: { text: 'Ungespeicherte Änderungen', farbe: FARBEN.warnung, grund: T.warnSoft },
    speichert: { text: 'Wird gespeichert …', farbe: FARBEN.gedaempft, grund: T.hair2 },
    konflikt: { text: `Konflikt: Plan wurde von anderer Seite geändert (Revision ${konfliktRevision ?? '?'}) — Seite neu laden`, farbe: FARBEN.gefahr, grund: T.errSoft },
    fehler: { text: 'Speichern fehlgeschlagen — erneut versuchen', farbe: FARBEN.gefahr, grund: T.errSoft },
  }[speicherStatus];

  const knopf = (aktiv: boolean): React.CSSProperties => ({
    padding: '6px 12px', fontSize: 12.5, fontWeight: 600, borderRadius: 8, cursor: 'pointer',
    border: `1px solid ${aktiv ? T.brandInk : T.controlBorder}`,
    background: aktiv ? T.brandSoft : T.surface, color: aktiv ? T.brandInk : T.canvasWall,
  });

  const panelLabel: React.CSSProperties = { display: 'block', color: FARBEN.gedaempft, marginBottom: 8 };
  const panelInput: React.CSSProperties = { width: '100%', marginTop: 3, padding: '5px 8px', borderRadius: 8, border: `1px solid ${T.controlBorder}`, fontSize: 12.5 };

  const railIcon = (w: string): string => (({ auswahl: '\u2196', wand: '\u25AC', fenster: '\u25A2', tuer: '\u25D7', dach: '\u25B3' } as Record<string, string>)[w] ?? '\u2022');
  const railBtn = (aktiv: boolean): React.CSSProperties => ({
    display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
    height: 46, borderRadius: 9, cursor: 'pointer', fontWeight: 600,
    border: `1px solid ${aktiv ? T.brandInk : 'transparent'}`,
    background: aktiv ? T.brandSoft : 'transparent',
    color: aktiv ? T.brandInk : FARBEN.gedaempft,
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
    const mfarbe = T.muted;
    const gfarbe = T.canvasWall;
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
    <div style={{ fontFamily: 'Inter, system-ui, sans-serif', color: FARBEN.text, height: imStudio ? '100%' : '100vh', display: 'flex', flexDirection: 'column', background: T.bg }}>
      {/* Werkzeugleiste — neutral, Marke nur für Primäraktion */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '10px 14px', background: T.surface, borderBottom: `1px solid ${T.hair}` }}>
        {!imStudio && (
          <span style={{ display: 'flex', alignItems: 'center', gap: 9, marginRight: 8 }}>
            <span style={{ width: 26, height: 26, borderRadius: 7, background: FARBEN.auswahl, display: 'grid', placeItems: 'center', color: T.ink }}>{svgWrap(<><path d="M3 11l9-7 9 7" /><path d="M5 10v10h14V10" /></>)}</span>
            <strong style={{ fontSize: 14 }}>Hausplaner <span style={{ fontWeight: 600, color: FARBEN.gedaempft, fontSize: 11.5 }}>· Solar Aspekt</span></strong>
          </span>
        )}
        <button type="button" style={knopf(false)} title="Rückgängig (⌘Z)" aria-label="Rückgängig" onClick={() => store.getState().undo()} disabled={!store.getState().kannUndo()}>↶</button>
        <button type="button" style={knopf(false)} title="Wiederholen (⌘⇧Z)" aria-label="Wiederholen" onClick={() => store.getState().redo()} disabled={!store.getState().kannRedo()}>↷</button>
        <span style={{ width: 1, height: 22, background: T.hair, margin: '0 4px' }} />
        {/* Dashboard v2.1: Die Fenstertyp/Türtyp-Auswahl stand hier und ist byte-treu in die
            Kontext-Options-Leiste unter der Werkzeugleiste gewandert (§19/UI-4). Gleiche States,
            gleiche Optionslisten, gleiches onChange — der Platzierungspfad ist unberührt. */}
        {/* Dashboard v1 §3: Geschoss-Stepper (◀ [Name ▾] ▶) statt Flach-select — ◀/▶ nach sortOrder/elevation,
            native select = Sprung/Tipp-Suche (skaliert bis viele Etagen). setActiveLevel bleibt SSOT; Token-Border. */}
        <span style={{ fontSize: 12, color: FARBEN.gedaempft, display: 'inline-flex', alignItems: 'center', gap: 4 }}>
          Geschoss
          {(() => {
            const sortiert = [...scene.levels].sort((a, b) => a.sortOrder - b.sortOrder || a.elevation - b.elevation);
            const idx = sortiert.findIndex((l) => l.id === level.id);
            const gehe = (d: number): void => { const z = sortiert[idx + d]; if (z) store.getState().setActiveLevel(z.id); };
            const pfeil = (t: string, label: string, d: number, aus: boolean): React.ReactElement => (
              <button type="button" disabled={aus} title={label} aria-label={label}
                style={{ ...knopf(false), padding: '4px 7px', opacity: aus ? 0.4 : 1, cursor: aus ? 'not-allowed' : 'pointer' }}
                onClick={() => gehe(d)}>{t}</button>
            );
            return (
              <>
                {pfeil('◀', 'Geschoss darunter', -1, idx <= 0)}
                <select value={level.id} title="Geschoss wählen" onChange={(e) => store.getState().setActiveLevel(e.target.value)}
                  style={{ fontSize: 12.5, padding: '5px 8px', borderRadius: 8, border: `1px solid ${T.controlBorder}` }}>
                  {sortiert.map((l) => <option key={l.id} value={l.id}>{l.name}</option>)}
                </select>
                {pfeil('▶', 'Geschoss darüber', 1, idx >= sortiert.length - 1)}
              </>
            );
          })()}
        </span>
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
          style={{ width: 104, fontSize: 12.5, padding: '5px 8px', borderRadius: 8, border: `1px solid ${T.controlBorder}` }}
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
          style={knopf(false)}
          title="Aktuelles Geschoss als Vorlage duplizieren — Wände, Öffnungen und Dach werden ein Stockwerk höher kopiert"
          onClick={dupliziereGeschossJetzt}
        >⧉ Geschoss dupl.</button>
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
        <span style={{ width: 1, height: 22, background: T.hair, margin: '0 4px' }} />
        {/* P1c: Modus-Schalter — 3D ist der zweite Renderer DERSELBEN Daten (ein Store). */}
        <button type="button" title="2D-Grundriss" style={knopf(modus === '2d')} onClick={() => store.getState().setModus('2d')}>2D</button>
        <button type="button" title="2D und 3D nebeneinander" style={knopf(modus === 'split')} onClick={() => store.getState().setModus('split')}>Split</button>
        <button type="button" title="3D-Ansicht" style={knopf(modus === '3d')} onClick={() => store.getState().setModus('3d')}>3D</button>
        <span style={{ flex: 1 }} />
        <span style={{ fontSize: 12, fontWeight: 600, padding: '4px 12px', borderRadius: 999, color: statusPill.farbe, background: statusPill.grund }}>{statusPill.text}</span>
        <button
          type="button"
          onClick={() => void store.getState().save()}
          style={{ padding: '7px 16px', fontSize: 13, fontWeight: 700, borderRadius: 8, border: 'none', cursor: 'pointer', background: T.brand, color: T.ink }}
        >
          Speichern (Strg+S)
        </button>
      </div>

      {/* AUF-34: der Arbeitsbereich-Wähler. Er steht ÜBER der Gruppenzeile, weil er sie bestimmt.
          Gerendert von derselben `ReiterLeiste` wie Panel und Schiene (AUF-27): die Bereiche wählen
          aus, welcher Inhalt in der Gruppenzeile steht — genau ein Reiter-Verhalten. Ein dritter
          Mechanismus mit eigener Tastaturbedienung wäre eine zweite Wahrheit. */}
      <div style={{ flex: '0 0 auto', display: 'flex', alignItems: 'baseline', gap: 10, padding: '5px 14px 0', background: T.bg }}>
        <span style={{ fontSize: 10.5, fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase', color: FARBEN.gedaempft, flex: '0 0 auto' }}>Arbeitsbereich</span>
        <div style={{ flex: 1, minWidth: 0 }}>
          <ReiterLeiste
            reiter={bereichReiter}
            aktiv={activeWorkspace}
            setAktiv={waehleBereich}
            ariaLabel="Arbeitsbereiche"
            panelId={BEREICH_ID}
            reiterId={bereichReiterId}
          />
        </div>
      </div>

      {/* Bedien-Werkzeugleiste — Icons, jedes mit Tooltip + Funktionsbeschreibung */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '6px 14px', background: T.bg, borderBottom: `1px solid ${T.hair}`, flex: '0 0 auto' }}>
        {opLbl('Ansicht')}
        <OpBtn title="Vergrößern (Zoom +) — näher an den Grundriss heranzoomen" icon="zoom-in" onClick={() => setZoom((z) => Math.min(1, z * 1.2))} />
        <OpBtn title="Verkleinern (Zoom −) — weiter herauszoomen" icon="zoom-out" onClick={() => setZoom((z) => Math.max(0.02, z / 1.2))} />
        <OpBtn title="Zoom zurücksetzen — Standardmaßstab wiederherstellen" icon="zoom-reset" onClick={() => setZoom(0.12)} />
        <OpBtn title="Ansicht einpassen — gesamten Grundriss ins Bild rücken" icon="einpassen" geplant />
        <OpBtn title="Raster ein-/ausblenden — Hintergrund-Hilfslinien" icon="grid" aktiv={rasterAn} onClick={() => setRasterAn((v) => !v)} />
        <OpBtn title="Fang ein-/ausschalten — an Punkten und Raster einrasten" icon="fang" aktiv={scene.settings.snapEnabled} onClick={() => store.getState().executeCommand({ type: 'UPDATE_SETTINGS', changes: { snapEnabled: !scene.settings.snapEnabled } })} />
        {opSep()}
        {opLbl('Bearbeiten')}
        <OpBtn title="Auswahl duplizieren — Kopie versetzt daneben einfügen" icon="dup" disabled={selectedNodeIds.length === 0} onClick={dupliziere} />
        <OpBtn title="Auswahl löschen (Entf) — markiertes Objekt entfernen" icon="del" disabled={selectedNodeIds.length === 0} onClick={loescheAuswahl} />
        <OpBtn title="Grundriss links/rechts spiegeln" icon="mirror-h" disabled={waende.length === 0} onClick={() => spiegeleGrundriss('vertikal')} />
        <OpBtn title="Grundriss oben/unten spiegeln" icon="mirror-v" disabled={waende.length === 0} onClick={() => spiegeleGrundriss('horizontal')} />
        <OpBtn title="Auswahl um 90° drehen" icon="drehen" geplant />
        {opSep()}
        {opLbl('Messen & Export')}
        <OpBtn title="Messwerkzeug — Abstand zwischen zwei Punkten messen" icon="messen" geplant />
        <OpBtn title="Bemaßung — Maßkette am Grundriss anlegen" icon="bemassung" geplant />
        <OpBtn title="Als PNG-Bild exportieren — aktuelle 2D-Ansicht herunterladen" icon="export" onClick={exportPng} />
        <OpBtn title="Als PDF-Planblatt exportieren" icon="pdf" geplant />
        <span style={{ fontSize: 12, color: FARBEN.gedaempft, marginLeft: 'auto', fontVariantNumeric: 'tabular-nums' }}>Zoom {(zoom * 100).toFixed(0)} %</span>
      </div>

      {/* AUF-34: die Themen-Gruppen des GEWÄHLTEN Arbeitsbereichs — in einer EIGENEN Zeile.
          Vorher standen alle 22 Kategorien hinter den Icon-Knöpfen in derselben Zeile; gemessen
          waren das bei 1440 px drei Zeilen, und der waagerechte Überlauf schob das rechte Panel
          aus dem Bild. Jetzt: 15 Themen, davon 7 durchgängig und 8 an einen Bereich gebunden,
          in einer Zeile, die niemandem mehr den Platz nimmt.
          Dieser Bereich ist das Ziel der `aria-controls` des Bereich-Wählers — deshalb trägt er
          die Rolle und die id; ein Verweis ins Leere wäre schlimmer als kein Verweis. */}
      <div
        role="tabpanel" id={BEREICH_ID} aria-labelledby={bereichReiterId(activeWorkspace)}
        style={{ display: 'flex', alignItems: 'center', gap: 2, padding: '3px 14px 6px', background: T.bg, borderBottom: `1px solid ${T.hair}`, flex: '0 0 auto' }}
      >
        <WerkzeugGruppenMenue
          offen={offeneGruppe}
          setOffen={setOffeneGruppe}
          kontext={werkzeugKontext}
          aktivId={werkzeug}
          angeheftet={angeheftet}
          onAnheften={heftUm}
          gruppen={sichtbareGruppen}
        />
      </div>

      {/* Dashboard v2.1 (§19 / UI-4): Kontext-Options-Leiste — zeigt die Optionen des AKTIVEN
          Werkzeugs. Volle Breite, direkt unter der Bedienleiste, vor dem Canvas.
          AUF-16/B1: Die Komponente steht auf MODULEBENE (s. o.); ihre Werte kommen als Props. */}
      <KontextOptionenLeiste
        werkzeug={werkzeug}
        fensterTypWahl={fensterTypWahl}
        tuerTypWahl={tuerTypWahl}
        setFensterTypWahl={setFensterTypWahl}
        setTuerTypWahl={setTuerTypWahl}
        fremderBereich={fremderBereich}
      />

      {/* Canvas: 2D (Konva) + 3D (three) nebeneinander — beide lesen DENSELBEN Store.
          Der 3D-Bereich bleibt über Moduswechsel gemountet (nur ausgeblendet) ⇒ Kamera
          bleibt erhalten; dispose() erst beim Verlassen der Seite (Kante 6). */}
      <div style={{ flex: 1, overflow: 'hidden', display: 'flex' }}>
        {/* L1: Planer-Schiene — AUF-27: DREI REITER statt drei gestapelter Blöcke.
            Vorher trugen Werkzeuge, Fachplaner und Projektbrowser eine gemeinsame Scroll-Höhe;
            der Projektbrowser war erst nach rund 20 Scroll-Ticks sichtbar. Jetzt ist immer genau
            ein Abschnitt sichtbar, und die Scroll-Höhe gehört dem Abschnitt, nicht der Spalte:
            `overflow` sitzt am Inhaltsbereich, NICHT mehr an dieser Spalte.
            Die Reiterleiste ist die gemeinsame `ReiterLeiste` (dasselbe Muster wie im
            Eigenschaften-Panel, AUF-19) — kein zweiter Tab-Mechanismus. */}
        <div style={{ width: 220, flex: '0 0 auto', background: T.surface, borderRight: `1px solid ${T.hair}`, display: 'flex', flexDirection: 'column', overflow: 'hidden' }}>
          <ReiterLeiste
            reiter={SCHIENEN_REITER}
            aktiv={schienenTab}
            setAktiv={(id) => setSchienenTab(id as SchienenReiterId)}
            ariaLabel="Planer-Bereiche"
            panelId={SCHIENE_ID}
            reiterId={schienenReiterId}
          />
          {/* DER Inhaltsbereich der drei Reiter: eine Rolle, eine id, eigene Scroll-Höhe.
              `aria-labelledby` nennt den gerade aktiven Reiter. */}
          <div
            role="tabpanel" id={SCHIENE_ID} aria-labelledby={schienenReiterId(schienenTab)}
            style={{ flex: 1, minHeight: 0, overflowY: 'auto', display: 'flex', flexDirection: 'column' }}
          >
          {schienenTab === 'werkzeuge' && (<>
          {/* Welle A2 (§19/UI-4): Die Leiste bezieht ihre Zugehörigkeit AUSSCHLIESSLICH aus der
              Präsentationsschicht — `zoneTools('fix')` statt `werkzeugTools()`. Vorher entschieden
              zwei Mechanismen unabhängig über dieselbe Frage (`art` in der Registry UND `zone` in den
              Regeln); sie stimmten nur zufällig überein. Jetzt ist es eine Wahrheit: wer ein Werkzeug
              aus der Leiste nehmen will, ändert eine Regel in `toolPresentation.ts`.
              Aktivierung bleibt `resolveToolState` — kein zweiter Filter. */}
          {railWerkzeuge.map((tool) => {
            const zustand = resolveToolState(tool, werkzeugKontext);
            const aktiv = werkzeug === tool.id;
            return (
              <button key={tool.id} type="button"
                title={zustand.enabled ? `${tool.label} (${tool.shortcut ?? ''}) — ${tool.helpText}` : `${tool.label} — ${zustand.reason}`}
                aria-disabled={!zustand.enabled}
                aria-pressed={aktiv}
                onClick={() => { if (!zustand.enabled) return; setWerkzeug(tool.id as typeof werkzeug); setWandStart(null); setTreppeStart(null); }}
                style={{ ...navItem(aktiv), ...(zustand.enabled ? {} : { opacity: 0.4, cursor: 'not-allowed' }) }}>
                <span style={{ width: 18, height: 18, display: 'grid', placeItems: 'center', flex: '0 0 auto' }}>{werkzeugIcon(tool.id)}</span>
                <span style={{ flex: 1 }}>{tool.label}</span>
                {tool.shortcut && <span style={{ fontSize: 10.5, color: T.muted, border: `1px solid ${T.controlBorder}`, borderRadius: 4, padding: '1px 5px' }}>{tool.shortcut}</span>}
              </button>
            );
          })}
          </>)}

          {/* AUF-27 / NACHTRAG: Der Reiter heisst „Fachplaner", nicht „Fähigkeiten" — derselbe
              Begriff wie in Ebene 2 der Inventur. ÜBERGANGSLÖSUNG BIS L2/L3 (AUF-33): dass die
              Fachgewerke hier in der Werkzeug-Schiene liegen, ist ein Zwischenstand, keine Absicht.
              Wohin sie gehören, ist ungeklärt — es gibt eine gemessene Teil-Doppelung mit den 19
              L4-Fachplaner-Flächen (mind. `engine-fbh`↔`fach-fbh`, `engine-pv`↔`fach-pv-module`,
              `engine-kueche`↔`fach-kueche`), aber keine 1:1-Deckung. Die Zusammenführung ist ein
              eigener, zu messender Posten. Bis dahin bleiben alle 22 Einträge erreichbar. */}
          {schienenTab === 'fachplaner' && (
            <FaehigkeitenNavi
              activeToolId={werkzeug}
              onAktivieren={(id) => { setWerkzeug(id as Werkzeug); setWandStart(null); setTreppeStart(null); }}
              onEngine={(id) => setOffeneEngine(id)}
            />
          )}

          {/* Dashboard v2.3 (§32 / UI-8): Projektbrowser — seit AUF-27 ein eigener REITER derselben
              220-px-Schiene, keine neue Spalte. Damit bleibt die Flächenrechnung
              (innerWidth − 220 − 268) unberührt, und er ist ohne Scrollen erreichbar.
              Inhalt kommt als DATEN aus `projektBaum` (rein, getestet); das Markup bleibt dünn.
              Bewusst KEINE eigene, im Rumpf definierte Komponente (Befund B1 aus dem Batch-1-Votum):
              die Einträge sind fokussierbare Knöpfe und dürften sonst bei jedem Render neu montiert
              werden. Klick nutzt das vorhandene `selectNodes([id])` — keine zweite Auswahl-Wahrheit. */}
          {schienenTab === 'projekt' && (<>
          {baum.length === 0 ? (
            <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-start', gap: 6, padding: '2px 12px 10px', fontSize: 11.5, color: T.muted }}>
              <span>{PROJEKTBAUM_LEER}</span>
              {/* Ehrlich: es fehlt eine VORAUSSETZUNG (Bauteile), nicht eine Funktion. */}
              <ZustandBadge zustand="voraussetzung" />
            </div>
          ) : (
            baum.map((gruppe) => (
              <div key={gruppe.gruppe} style={{ marginBottom: 2 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '6px 12px 2px', fontSize: 11, fontWeight: 700, letterSpacing: '.04em', color: T.muted }}>
                  <span style={{ flex: 1 }}>{gruppe.gruppe}</span>
                  <span style={{ fontVariantNumeric: 'tabular-nums' }}>{gruppe.anzahl}</span>
                </div>
                {gruppe.eingeklappt ? (
                  /* Kante 6: zu viele Einträge — Kopf + Anzahl statt ungebremster Liste.
                     Virtuelles Scrollen ist v6 und wird hier nicht vorweggenommen. */
                  <div style={{ padding: '0 12px 6px 20px', fontSize: 11, color: T.muted }}>
                    Zusammengeklappt – {gruppe.anzahl} Einträge. Auswahl über den Plan.
                  </div>
                ) : (
                  gruppe.eintraege.map((eintrag) => {
                    const gewaehlt = selectedNodeIds.includes(eintrag.id);
                    return (
                      <button
                        key={eintrag.id} type="button"
                        title={`${eintrag.label} – im Plan auswählen`}
                        aria-current={gewaehlt ? 'true' : undefined}
                        // AUF-35a: auch der Projektbrowser geht durch `waehleAn` — Shift/Strg
                        // wirken hier wie im Plan. Zwei Auswahl-Wege mit verschiedenen Regeln
                        // wären genau die zweite Wahrheit, die dieser Posten beseitigt.
                        onClick={(e) => waehleAn(eintrag.id, e.nativeEvent)}
                        style={{
                          display: 'block', textAlign: 'left', width: 'calc(100% - 12px)', margin: '1px 6px',
                          padding: '4px 8px 4px 14px', border: 'none', borderRadius: 6, cursor: 'pointer', fontSize: 12,
                          /* Hervorhebung als Hintergrund UND Schriftschnitt — nicht nur farblich (WCAG 1.4.1). */
                          background: gewaehlt ? T.brandWash : 'transparent',
                          color: gewaehlt ? T.brandInk : T.ink,
                          fontWeight: gewaehlt ? 700 : 500,
                        }}
                      >
                        {eintrag.label}
                      </button>
                    );
                  })
                )}
              </div>
            ))
          )}
          </>)}
          </div>
          {/* Fuss der Schiene: steht UNTER dem Inhaltsbereich, gehört also keinem Reiter und
              scrollt nicht mit — er gilt für alle drei. */}
          <div style={{ padding: '10px 12px', fontSize: 11, color: T.muted, borderTop: `1px solid ${T.canvasGrid}`, flex: '0 0 auto' }}>Erweiterbar – Module folgen.</div>
        </div>
        <div style={{ display: modus === '3d' ? 'none' : 'block', width: stageBreite, borderRight: modus === 'split' ? `1px solid ${T.hair}` : 'none' }}>
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
            {rasterAn && rasterLinien}
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
                  waehleAn(w.id, e.evt);
                }
              };

              return (
                <Group
                  key={w.id}
                  draggable={werkzeug === 'auswahl'}
                  onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(w.id, e.evt); } }}
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
                  onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(o.id, e.evt); } }}
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
                    fill={T.surface} stroke={ausgewaehlt ? FARBEN.auswahl : o.type === 'door' ? FARBEN.gedaempft : FARBEN.linie}
                    strokeWidth={30}
                    onClick={(e) => {
                      if (werkzeug === 'auswahl') {
                        e.cancelBubble = true;
                        waehleAn(o.id, e.evt);
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
              const farbe = ausgewaehlt ? FARBEN.auswahl : T.materialWood;

              return (
                <Group key={r.id}>
                  <Line
                    points={r.polygon.flatMap((p) => [p.x, p.y])}
                    closed stroke={farbe} strokeWidth={40} dash={[300, 200]}
                    onClick={(e) => {
                      if (werkzeug === 'auswahl') {
                        e.cancelBubble = true;
                        waehleAn(r.id, e.evt);
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

            {/* Treppen (objectType 'stair') — Grundriss-Symbol: Umriss + Trittstufen + Aufwaerts-Pfeil. */}
            {nodes.filter((n): n is ObjectNode => n.type === 'object' && n.objectType === 'stair').map((st) => {
              const tp = parametereZuTreppe(st.parameters);
              if (!tp) return null;
              const sym = treppe2DSymbol({
                start: { x: tp.startX, y: tp.startY }, end: { x: tp.endX, y: tp.endY },
                laufbreite: tp.laufbreite, geschosshoehe: tp.geschosshoehe,
                gewuenschteSteigung: tp.gewuenschteSteigung, bereich: tp.bereich,
              });
              const ausgewaehlt = selectedNodeIds.includes(st.id);
              const farbe = ausgewaehlt ? FARBEN.auswahl : (sym.bestanden ? FARBEN.wand : FARBEN.gefahr);
              const mx = (tp.startX + tp.endX) / 2;
              const my = (tp.startY + tp.endY) / 2;
              return (
                <Group
                  key={st.id}
                  draggable={werkzeug === 'auswahl'}
                  onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(st.id, e.evt); } }}
                  onDragEnd={(e) => {
                    const dx = Math.round(e.target.x()); const dy = Math.round(e.target.y());
                    e.target.position({ x: 0, y: 0 });
                    if (dx || dy) {
                      const neu = { ...tp, startX: tp.startX + dx, startY: tp.startY + dy, endX: tp.endX + dx, endY: tp.endY + dy };
                      store.getState().executeCommand({
                        type: 'UPDATE_NODE', nodeId: st.id,
                        changes: {
                          parameters: treppeZuParametern(neu),
                          transform: { ...st.transform, position: { x: neu.startX, y: neu.startY, z: st.transform.position.z } },
                        },
                      });
                    }
                  }}
                  onClick={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(st.id, e.evt); } }}
                >
                  <Line points={sym.umriss.flatMap((q) => [q.x, q.y])} closed stroke={farbe} strokeWidth={40 / zoom} fill={ausgewaehlt ? T.brandWash : T.canvasWallGhost} />
                  {sym.stufen.map((s, i) => (
                    <Line key={i} points={[s[0].x, s[0].y, s[1].x, s[1].y]} stroke={farbe} strokeWidth={25 / zoom} listening={false} />
                  ))}
                  <Line points={[sym.pfeil.von.x, sym.pfeil.von.y, sym.pfeil.bis.x, sym.pfeil.bis.y]} stroke={farbe} strokeWidth={30 / zoom} listening={false} />
                  <Circle x={sym.pfeil.bis.x} y={sym.pfeil.bis.y} radius={90} fill={farbe} listening={false} />
                  <Text x={mx - 700} y={my + 200} width={1400} align="center" scaleY={-1} text={`Treppe · ${sym.anzahlSteigungen}×${Math.round(sym.steigungshoehe)} mm`} fontSize={170} fill={FARBEN.gedaempft} listening={false} />
                </Group>
              );
            })}

            {/* Generische Objekte (radiator/wallbox/… außer stair) — Grundriss-Kasten + Label. */}
            {nodes.filter((n): n is ObjectNode => n.type === 'object' && n.objectType !== 'stair').map((ob) => {
              const px = ob.transform.position.x;
              const py = ob.transform.position.y;
              const laenge = Number(ob.parameters['objekt.laenge']) || 800;
              const tiefe = 150;
              const label = String(ob.parameters['objekt.label'] ?? ob.objectType);
              const ausgewaehlt = selectedNodeIds.includes(ob.id);
              const farbe = ausgewaehlt ? FARBEN.auswahl : FARBEN.wand;
              return (
                <Group key={ob.id} x={px} y={py}
                  draggable={werkzeug === 'auswahl'}
                  onDragStart={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(ob.id, e.evt); } }}
                  onDragEnd={(e) => {
                    const nx = Math.round(e.target.x()); const ny = Math.round(e.target.y());
                    store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: ob.id, changes: { transform: { ...ob.transform, position: { x: nx, y: ny, z: ob.transform.position.z } } } });
                  }}
                  onClick={(e) => { if (werkzeug === 'auswahl') { e.cancelBubble = true; waehleAn(ob.id, e.evt); } }}
                >
                  <Rect x={0} y={0} width={laenge} height={tiefe} cornerRadius={30} stroke={farbe} strokeWidth={40 / zoom} fill={ausgewaehlt ? T.brandWash : T.canvasWallGhost} />
                  <Text x={0} y={-90} width={laenge} align="center" scaleY={-1} text={label} fontSize={150} fill={FARBEN.gedaempft} listening={false} />
                </Group>
              );
            })}

            {/* Vorschau beim Treppezeichnen */}
            {werkzeug === 'treppe' && treppeStart && (
              <Group listening={false}>
                <Line points={[treppeStart.x, treppeStart.y, mitWinkelSnap(treppeStart, cursor).x, mitWinkelSnap(treppeStart, cursor).y]} stroke={FARBEN.auswahl} strokeWidth={50} dash={[200, 120]} />
                <Circle x={treppeStart.x} y={treppeStart.y} radius={90} fill={FARBEN.auswahl} />
              </Group>
            )}

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
        {/* AUF-26/B3: `overflowWrap` + `boxSizing` — Text bricht um, statt im Wort abgeschnitten zu
            werden. Ein Hinweis, der bei „…brauch" endet, ist kein Hinweis. */}
        <div style={{ width: 268, flex: '0 0 auto', background: T.surface, borderLeft: `1px solid ${T.hair}`, padding: 14, overflowY: 'auto', overflowWrap: 'anywhere', boxSizing: 'border-box', fontSize: 12.5, color: FARBEN.text }}>
          <div style={{ fontWeight: 800, fontSize: 11.5, textTransform: 'uppercase', letterSpacing: '.04em', color: FARBEN.gedaempft, marginBottom: 8 }}>Eigenschaften</div>
          {/* Dashboard v2.2 (§20 / UI-5): Reiter aus PANEL_TABS (Daten, nicht Markup). Seit AUF-27
              rendert sie die gemeinsame `ReiterLeiste` — dieselbe Verdrahtung wie die Schiene,
              inklusive der AUF-19-Nacharbeiten (aria-controls, Pfeiltasten, Fokusnachführung).
              `allgemein` zeigt den unveränderten Panelinhalt. */}
          <ReiterLeiste
            reiter={PANEL_TABS}
            aktiv={aktiverTab}
            setAktiv={(id) => setAktiverTab(id as PanelTabId)}
            ariaLabel="Eigenschaften-Bereiche"
            panelId={PANEL_ID}
            reiterId={(id) => reiterId(id as PanelTabId)}
          />
          {/* B3 (Nacharbeit N3): DER Inhaltsbereich der Reiter. Bis hierher trug er keine Rolle —
              die Reiterleiste versprach ein Tabpanel, das im Baum nicht existierte. `aria-labelledby`
              zeigt auf den GERADE aktiven Reiter, `id` ist das Ziel aller vier `aria-controls`. */}
          <div role="tabpanel" id={PANEL_ID} aria-labelledby={reiterId(aktiverTab)}>
          {aktiverTab === 'pruefungen' ? (
            /* Dashboard v2.4 (§34 / UI-10): Prüfungscenter. Die Liste kommt aus `befundeAus`
               (rein, getestet) und hat heute 0 oder 1 Eintrag — der Store hält genau EINE
               Ablehnung. Was NICHT geführt wird, steht ehrlich unter der Liste, statt eine
               Historie vorzutäuschen. */
            <div style={{ color: FARBEN.gedaempft, lineHeight: 1.6 }}>
              {befunde.length === 0 ? (
                <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-start', gap: 6, marginBottom: 12 }}>
                  <span>{BEFUNDE_LEER}</span>
                  <ZustandBadge zustand="verfuegbar" />
                </div>
              ) : (
                <ul style={{ listStyle: 'none', margin: '0 0 12px', padding: 0 }}>
                  {befunde.map((b) => (
                    <li key={b.id} style={{ display: 'flex', gap: 6, alignItems: 'flex-start', padding: '6px 8px', marginBottom: 6, borderRadius: 8, background: T.errSoft, border: `1px solid ${T.errBorder}`, color: T.errInk }}>
                      {/* Schwere als Symbol UND Text, nicht nur als Farbe (A11y). */}
                      <span aria-hidden style={{ fontWeight: 700 }}>✋</span>
                      <span><strong style={{ fontWeight: 700 }}>Abgelehnt</strong> – {b.text}</span>
                    </li>
                  ))}
                </ul>
              )}
              <div style={{ fontSize: 11, color: T.muted, borderTop: `1px solid ${T.hair}`, paddingTop: 8 }}>{BEFUNDE_UMFANG}</div>
            </div>
          ) : aktiverTab !== 'allgemein' ? (
            <div style={{ color: FARBEN.gedaempft, lineHeight: 1.7 }}>
              <div style={{ marginBottom: 8 }}>{PANEL_TABS.find((t) => t.id === aktiverTab)?.hinweis}</div>
              <ZustandBadge zustand={PANEL_TABS.find((t) => t.id === aktiverTab)?.zustand ?? 'in_entwicklung'} />
            </div>
          ) : (
            <>
          {/* AUF-35a / Kante 4: Bei MEHRFACHauswahl zeigt das Panel eine Übersicht mit Anzahl je Typ
              statt Einzelfelder zu raten. Die Zählung kommt aus `mehrfachUebersicht` (rein,
              getestet); das Markup bleibt dünn. Darunter laufen die Einzelfelder wie bisher weiter —
              sie zeigen das PRIMÄROBJEKT, also das zuletzt gewählte. */}
          {auswahlUebersicht.gesamt > 1 && (
            <div style={{ marginBottom: 12, padding: '8px 10px', borderRadius: 8, background: T.surface2, border: `1px solid ${T.hair}` }}>
              <div style={{ fontWeight: 700, marginBottom: 4 }}>{auswahlUebersicht.gesamt} Objekte gewählt</div>
              <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, marginBottom: 6 }}>
                {auswahlUebersicht.typen.map((t) => (
                  <span key={t.typ} style={{ fontSize: 11.5, padding: '2px 7px', borderRadius: 999, background: T.brandWash, color: T.brandInk }}>
                    {t.bezeichnung}
                  </span>
                ))}
              </div>
              {auswahlUebersicht.gesperrt > 0 && (
                <div style={{ fontSize: 11.5, color: FARBEN.gedaempft }}>🔒 {auswahlUebersicht.gesperrt} davon gesperrt — wählbar, aber nicht bearbeitbar.</div>
              )}
              <div style={{ fontSize: 11.5, color: FARBEN.gedaempft }}>
                Unten stehen die Eigenschaften des zuletzt gewählten Objekts.
              </div>
            </div>
          )}
          {/* Dashboard v1 §5: Sicht (Auge) + Sperre (Schloss) je selektiertem Node → vorhandene Commands
              SET_NODES_SICHTBAR/SET_NODES_GESPERRT. Zustand als Text UND Symbol (A11y). Entsperren fragt nach. */}
          {selectedNode && (
            <div style={{ display: 'flex', gap: 6, marginBottom: 12 }}>
              <button type="button" style={knopf(false)} title={selectedNode.visible === false ? 'Einblenden' : 'Ausblenden'} aria-label="Sicht umschalten"
                onClick={() => store.getState().executeCommand({ type: 'SET_NODES_SICHTBAR', nodeIds: [selectedNode.id], sichtbar: selectedNode.visible === false })}>
                {selectedNode.visible === false ? '🙈 Ausgeblendet' : '👁 Sicht'}
              </button>
              <button type="button" style={knopf(selectedNode.locked === true)} title={selectedNode.locked ? 'Entsperren' : 'Sperren'} aria-label="Sperre umschalten"
                onClick={() => { if (selectedNode.locked && !window.confirm('Objekt ist gesperrt — entsperren?')) return; store.getState().executeCommand({ type: 'SET_NODES_GESPERRT', nodeIds: [selectedNode.id], gesperrt: !selectedNode.locked }); }}>
                {selectedNode.locked ? '🔒 Gesperrt' : '🔓 Sperren'}
              </button>
            </div>
          )}
          {selectedRoof ? (
            <>
              <div style={{ fontWeight: 700, marginBottom: 10 }}>Dach</div>
              <label style={panelLabel}>Dachform
                <select value={selectedRoof.roofType} onChange={(e) => aktualisiereDach({ roofType: e.target.value as RoofNode['roofType'] })} style={panelInput}>
                  <option value="sattel">Satteldach</option>
                  <option value="walm">Walmdach</option>
                  <option value="pult">Pultdach</option>
                  <option value="flach">Flachdach</option>
                  <option value="l-shape">L-Dach</option>
                  <option value="t-shape">T-Dach</option>
                  <option value="u-shape">U-Dach</option>
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
              {/* Verdrahtung #1: L/T/U-Anbaumaße (verdrahten den schon abgenommenen Verschneidungs-Render an die UI).
                  Operanden-Gate: fehlende/≤0 Maße rendern NICHT — mit konkretem Grund markiert, kein erfundener Wert. */}
              {istVerschneidungsForm(selectedRoof.roofType) && (() => {
                const a = selectedRoof.anbau;
                const istU = selectedRoof.roofType === 'u-shape';
                const setzeAnbau = (feld: keyof RoofAnbauMasse, wert: number): void => {
                  const basis: RoofAnbauMasse = { length: a?.length ?? 0, width: a?.width ?? 0, lengthB: a?.lengthB, widthB: a?.widthB };
                  basis[feld] = Math.max(0, Math.round(wert));
                  aktualisiereDach({ anbau: basis });
                };
                const fehlt = !a || !(a.length > 0) || !(a.width > 0) || (istU && (!(a.lengthB && a.lengthB > 0) || !(a.widthB && a.widthB > 0)));
                return (
                  <>
                    <div style={{ fontWeight: 700, margin: '12px 0 6px' }}>Anbau / Verschneidung</div>
                    <label style={panelLabel}>Außenmaß Länge (mm)
                      <input type="number" min={0} value={a?.length ?? ''} onChange={(e) => setzeAnbau('length', Number(e.target.value))} style={panelInput} />
                    </label>
                    <label style={panelLabel}>Außenmaß Breite (mm)
                      <input type="number" min={0} value={a?.width ?? ''} onChange={(e) => setzeAnbau('width', Number(e.target.value))} style={panelInput} />
                    </label>
                    {istU && (
                      <>
                        <label style={panelLabel}>Innenhof/Kerbe Länge (mm)
                          <input type="number" min={0} value={a?.lengthB ?? ''} onChange={(e) => setzeAnbau('lengthB', Number(e.target.value))} style={panelInput} />
                        </label>
                        <label style={panelLabel}>Innenhof/Kerbe Breite (mm)
                          <input type="number" min={0} value={a?.widthB ?? ''} onChange={(e) => setzeAnbau('widthB', Number(e.target.value))} style={panelInput} />
                        </label>
                      </>
                    )}
                    {fehlt && (
                      <div style={{ fontSize: 11, color: FARBEN.warnung, marginTop: 2 }}>
                        ⚠ {istU
                          ? 'U-Dach braucht alle vier Maße > 0 (Außen Länge/Breite + Innenhof/Kerbe Länge/Breite) — sonst rendert es nicht.'
                          : 'L/T-Dach braucht Außenmaß Länge und Breite > 0 — sonst rendert es nicht.'}
                      </div>
                    )}
                  </>
                );
              })()}
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
              {(selectedOpening.type === 'window' || selectedOpening.type === 'door') && (() => {
                const istFenster = selectedOpening.type === 'window';
                const katalog = istFenster ? FENSTER_BAUARTEN : TUER_BAUARTEN;
                const aktuellTyp = selectedOpening.produkt?.typ;
                const waehleTyp = (t: (typeof katalog)[number]): void => {
                  const prod = selectedOpening.produkt ?? {};
                  const aend: NonNullable<OpeningNode['produkt']> = { ...prod, typ: t.id };
                  if (t.oeffnungsArt) aend.oeffnungsArt = t.oeffnungsArt;
                  aktualisiereOeffnung({ produkt: aend });
                };
                return (
                  <div style={{ marginBottom: 12 }}>
                    <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '.04em', textTransform: 'uppercase', color: T.muted, marginBottom: 6 }}>Bauart</div>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 6, maxHeight: 220, overflowY: 'auto', paddingRight: 2 }}>
                      {katalog.map((t) => {
                        const aktivT = aktuellTyp === t.id;
                        return (
                          <button key={t.id} type="button" title={t.label} onClick={() => waehleTyp(t)}
                            style={{ display: 'grid', gap: 3, placeItems: 'center', padding: 5, borderRadius: 8, cursor: 'pointer',
                              border: `1.5px solid ${aktivT ? T.brandInk : T.controlBorder}`, background: aktivT ? T.brandWash : T.surface }}>
                            <img src={`${ICON_BASE}icons/${istFenster ? 'fenster' : 'tuer'}/${t.datei}`} alt={t.label} loading="lazy" style={{ width: '100%', height: 'auto', display: 'block' }} />
                            <span style={{ fontSize: 8.5, lineHeight: 1.15, color: aktivT ? FARBEN.text : T.muted, textAlign: 'center', height: 20, overflow: 'hidden' }}>{t.label}</span>
                          </button>
                        );
                      })}
                    </div>
                    {aktuellTyp && (
                      <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 6 }}>Bauart: <strong style={{ color: FARBEN.text }}>{(istFenster ? fensterBauartNach(aktuellTyp) : tuerBauartNach(aktuellTyp))?.label ?? aktuellTyp}</strong></div>
                    )}
                  </div>
                );
              })()}
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
              {(selectedOpening.type === 'window' || selectedOpening.type === 'door') && (() => {
                const prod = selectedOpening.produkt ?? {};
                const prof = profilNach(prod.profilId ?? '') ?? PROFIL_KATALOG[0];
                const verg = verglasungNach(prod.verglasungId ?? '') ?? VERGLASUNG_KATALOG[0];
                const oa = (prod.oeffnungsArt ?? 'dreh-kipp') as OeffnungsArt;
                const rc = (prod.rc ?? 'ohne') as RcKlasse;
                const uw = berechneUw({ breiteMm: selectedOpening.width, hoeheMm: selectedOpening.height, uf: prof.uf, ug: verg.ug, ansichtsbreiteMm: prof.ansichtsbreiteMm });
                const rcOk = rcMachbar(rc, verg);
                const preis = preisFenster({ breiteMm: selectedOpening.width, hoeheMm: selectedOpening.height, profil: prof, verglasung: verg, oeffnungsArt: oa, rc });
                const setP = (aend: Partial<NonNullable<OpeningNode['produkt']>>) => aktualisiereOeffnung({ produkt: { ...prod, ...aend } });
                return (
                  <div style={{ marginTop: 12, paddingTop: 10, borderTop: `1px solid ${T.hair}` }}>
                    <div style={{ fontWeight: 700, marginBottom: 8 }}>Produkt (Fensterbau)</div>
                    <label style={panelLabel}>Profilsystem
                      <select value={prof.id} onChange={(e) => setP({ profilId: e.target.value })} style={panelInput}>
                        {PROFIL_KATALOG.map((p) => (<option key={p.id} value={p.id}>{p.name}</option>))}
                      </select>
                    </label>
                    <label style={panelLabel}>Verglasung
                      <select value={verg.id} onChange={(e) => setP({ verglasungId: e.target.value })} style={panelInput}>
                        {VERGLASUNG_KATALOG.map((v) => (<option key={v.id} value={v.id}>{v.name}</option>))}
                      </select>
                    </label>
                    <label style={panelLabel}>Öffnungsart
                      <select value={oa} onChange={(e) => setP({ oeffnungsArt: e.target.value as OeffnungsArt })} style={panelInput}>
                        <option value="fest">fest</option>
                        <option value="dreh">Dreh</option>
                        <option value="kipp">Kipp</option>
                        <option value="dreh-kipp">Dreh-Kipp</option>
                      </select>
                    </label>
                    <label style={panelLabel}>Einbruchschutz (RC)
                      <select value={rc} onChange={(e) => setP({ rc: e.target.value as RcKlasse })} style={panelInput}>
                        <option value="ohne">ohne</option>
                        <option value="RC1N">RC1N</option>
                        <option value="RC2N">RC2N</option>
                        <option value="RC2">RC2</option>
                        <option value="RC3">RC3</option>
                      </select>
                    </label>
                    <div style={{ marginTop: 8, padding: 10, background: T.bg, border: `1px solid ${T.hair}`, borderRadius: 8, fontSize: 12, lineHeight: 1.7, color: FARBEN.text }}>
                      <div>U-Wert (U<sub>w</sub>): <strong>{uw.uw.toFixed(2)}</strong> W/(m²·K)</div>
                      <div>RC {rc === 'ohne' ? '' : rc}: <strong style={{ color: rc === 'ohne' ? FARBEN.gedaempft : rcOk ? FARBEN.erfolg : FARBEN.gefahr }}>{rc === 'ohne' ? 'kein Nachweis' : rcOk ? 'mit dieser Verglasung möglich' : 'Verglasung reicht nicht'}</strong></div>
                      <div>Preis (netto): <strong>{preis.gesamt.toLocaleString('de-DE')} €</strong></div>
                      <div style={{ fontSize: 10.5, color: FARBEN.gedaempft, marginTop: 4 }}>Rahmen {preis.rahmen} € · Glas {preis.glas} € · Beschlag {preis.beschlag} € · RC {preis.rcAufpreis} €</div>
                      <div style={{ fontSize: 10.5, color: FARBEN.gedaempft, marginTop: 4 }}>Katalogwerte sind Platzhalter bis zu den echten Schüco-Daten.</div>
                    </div>
                  </div>
                );
              })()}
              <div style={{ display: 'flex', gap: 6, marginTop: 8 }}>
                <button type="button" style={{ ...knopf(false), flex: 1 }} onClick={dupliziere}>Duplizieren</button>
                <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
              </div>
            </>
          ) : selectedStair && selectedStairParams ? (
            <>
              <div style={{ fontWeight: 700, marginBottom: 10 }}>Treppe</div>
              {(() => {
                const aktuellTyp = selectedStairParams.typ;
                return (
                  <div style={{ marginBottom: 12 }}>
                    <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '.04em', textTransform: 'uppercase', color: T.muted, marginBottom: 6 }}>Bauart</div>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 6, maxHeight: 200, overflowY: 'auto', paddingRight: 2 }}>
                      {TREPPEN_BAUARTEN.map((t) => {
                        const aktivT = aktuellTyp === t.id;
                        return (
                          <button key={t.id} type="button" title={t.label} onClick={() => aktualisiereTreppe({ typ: t.id })}
                            style={{ display: 'grid', gap: 3, placeItems: 'center', padding: 5, borderRadius: 8, cursor: 'pointer',
                              border: `1.5px solid ${aktivT ? T.brandInk : T.controlBorder}`, background: aktivT ? T.brandWash : T.surface }}>
                            <img src={`${ICON_BASE}icons/treppe/${t.datei}`} alt={t.label} loading="lazy" style={{ width: '100%', height: 'auto', display: 'block' }} />
                            <span style={{ fontSize: 8.5, lineHeight: 1.15, color: aktivT ? FARBEN.text : T.muted, textAlign: 'center', height: 20, overflow: 'hidden' }}>{t.label}</span>
                          </button>
                        );
                      })}
                    </div>
                    {aktuellTyp && (
                      <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 6 }}>Bauart: <strong style={{ color: FARBEN.text }}>{treppenBauartNach(aktuellTyp)?.label ?? aktuellTyp}</strong></div>
                    )}
                  </div>
                );
              })()}
              {(() => {
                const erg = berechneTreppe({ geschosshoehe: selectedStairParams.geschosshoehe, laufbreite: selectedStairParams.laufbreite, gewuenschteSteigung: selectedStairParams.gewuenschteSteigung, bereich: selectedStairParams.bereich, verfuegbareLauflaenge: Math.hypot(selectedStairParams.endX - selectedStairParams.startX, selectedStairParams.endY - selectedStairParams.startY) || undefined });
                return (
                  <div style={{ marginBottom: 10, padding: 10, background: erg.bestanden ? T.okSoft : T.errSoft, border: `1px solid ${erg.bestanden ? T.okBorder : T.errBorder}`, borderRadius: 8, fontSize: 11.5, lineHeight: 1.6, color: FARBEN.text }}>
                    <div><strong>{erg.anzahlSteigungen}</strong> Steigungen · <strong>{erg.anzahlAuftritte}</strong> Auftritte</div>
                    <div>Steigung {erg.steigungshoehe} mm · Auftritt {erg.auftritt} mm</div>
                    <div>Schrittmaß {erg.schrittmass} mm · {erg.bestanden ? 'DIN 18065 erfüllt' : 'DIN 18065 verletzt'}</div>
                  </div>
                );
              })()}
              <label style={panelLabel}>Nutzungsbereich
                <select value={selectedStairParams.bereich} onChange={(e) => aktualisiereTreppe({ bereich: e.target.value as TreppeParams['bereich'] })} style={panelInput}>
                  <option value="wohnung">Wohnung</option>
                  <option value="gebaeude">Gebäude</option>
                  <option value="aussen">außen</option>
                </select>
              </label>
              <label style={panelLabel}>Laufbreite (mm)
                <input type="number" min={500} value={selectedStairParams.laufbreite} onChange={(e) => aktualisiereTreppe({ laufbreite: Math.max(500, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <label style={panelLabel}>Geschosshöhe (mm)
                <input type="number" min={2000} value={selectedStairParams.geschosshoehe} onChange={(e) => aktualisiereTreppe({ geschosshoehe: Math.max(2000, Math.round(Number(e.target.value))) })} style={panelInput} />
              </label>
              <label style={panelLabel}>Ziel-Steigungshöhe (mm, optional)
                <input type="number" min={0} value={selectedStairParams.gewuenschteSteigung ?? ''} onChange={(e) => { const v = Math.round(Number(e.target.value)); aktualisiereTreppe({ gewuenschteSteigung: v > 0 ? v : undefined }); }} style={panelInput} />
              </label>
              <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 8 }}>Stufung wird automatisch nach DIN 18065 gerechnet. Bewegen: Treppe im Plan ziehen.</div>
              <div style={{ display: 'flex', gap: 6, marginTop: 8 }}>
                <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
              </div>
            </>
          ) : selectedObjekt ? (
            <>
              <div style={{ fontWeight: 700, marginBottom: 10 }}>{String(selectedObjekt.parameters['objekt.label'] ?? 'Objekt')}</div>
              <label style={panelLabel}>Länge (mm)
                <input type="number" min={100} value={Number(selectedObjekt.parameters['objekt.laenge']) || 0} onChange={(e) => store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedObjekt.id, changes: { parameters: { ...selectedObjekt.parameters, 'objekt.laenge': Math.max(100, Math.round(Number(e.target.value))) } } })} style={panelInput} />
              </label>
              <label style={panelLabel}>Höhe (mm)
                <input type="number" min={100} value={Number(selectedObjekt.parameters['objekt.hoehe']) || 0} onChange={(e) => store.getState().executeCommand({ type: 'UPDATE_NODE', nodeId: selectedObjekt.id, changes: { parameters: { ...selectedObjekt.parameters, 'objekt.hoehe': Math.max(100, Math.round(Number(e.target.value))) } } })} style={panelInput} />
              </label>
              <div style={{ fontSize: 11, color: FARBEN.gedaempft, marginTop: 8 }}>Bewegen: im Plan ziehen.</div>
              <div style={{ display: 'flex', gap: 6, marginTop: 8 }}>
                <button type="button" style={{ ...knopf(false), flex: 1, color: FARBEN.gefahr, borderColor: FARBEN.gefahr }} onClick={loescheAuswahl}>Löschen</button>
              </div>
            </>
          ) : (
            <div style={{ color: FARBEN.gedaempft, lineHeight: 1.7 }}>
              <div style={{ fontWeight: 700, color: FARBEN.text, marginBottom: 6 }}>Grundriss spiegeln</div>
              <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, marginBottom: 12 }}>
                <button type="button" style={{ ...knopf(false), flex: '1 1 108px' }} onClick={() => spiegeleGrundriss('vertikal')} disabled={waende.length === 0}>↔ Links/Rechts</button>
                <button type="button" style={{ ...knopf(false), flex: '1 1 108px' }} onClick={() => spiegeleGrundriss('horizontal')} disabled={waende.length === 0}>↕ Oben/Unten</button>
              </div>
              <div style={{ fontSize: 11.5, marginBottom: 10 }}>Objekt anklicken (Auswahl-Werkzeug) = markieren; dann ziehen zum Bewegen, oder Duplizieren/Löschen.</div>
              <div style={{ fontSize: 12 }}>Werkzeug: <strong style={{ color: FARBEN.text }}>{werkzeug}</strong></div>
              <div style={{ fontSize: 12 }}>Geschoss: <strong style={{ color: FARBEN.text }}>{level.name}</strong></div>
              <div style={{ fontSize: 12 }}>Räume: {raeume.length} · {(raeume.reduce((acc, r) => acc + r.flaecheMm2, 0) / 1_000_000).toFixed(2)} m²</div>
              <div style={{ marginTop: 12, padding: 10, background: T.bg, border: `1px solid ${T.hair}`, borderRadius: 8, fontSize: 11.5 }}>
                Ein Dach auswählen zeigt hier seine Parameter. Ablauf: Wand ziehen (W) → Dach (D) über den Umriss → 3D.
              </div>
            </div>
          )}
            </>
          )}
          </div>
        </div>
      </div>

      {/* Statusleiste */}
      <div style={{ display: 'flex', gap: 16, alignItems: 'center', padding: '7px 14px', background: T.surface, borderTop: `1px solid ${T.hair}`, fontSize: 12, color: FARBEN.gedaempft }}>
        <span>x {cursor.x} mm · y {cursor.y} mm</span>
        <span>Zoom {(zoom * 100).toFixed(0)} %</span>
        <span>Räume: {raeume.length} · Fläche gesamt: {(raeume.reduce((s, r) => s + r.flaecheMm2, 0) / 1_000_000).toFixed(2)} m²</span>
        {werkzeug === 'wand' && <span style={{ color: FARBEN.text }}>{wandStart ? 'Klick = nächster Wandpunkt · Esc beendet den Zug' : 'Klick setzt den Wandanfang'}</span>}
        {(werkzeug === 'fenster' || werkzeug === 'tuer') && <span style={{ color: FARBEN.text }}>Klick nahe einer Wand platziert die Öffnung</span>}
        {werkzeug === 'dach' && <span style={{ color: FARBEN.text }}>Klick legt ein Dach über den Gebäude-Umriss (ein Dach je Geschoss) — dann in 3D umschalten</span>}
        {werkzeug === 'treppe' && <span style={{ color: FARBEN.text }}>{treppeStart ? 'Klick = Ende der Lauflinie (Richtung = aufwärts) · Esc bricht ab' : 'Klick setzt den Anfang der Treppen-Lauflinie'}</span>}
        <span style={{ flex: 1 }} />
        {letzteAblehnung && <span style={{ color: FARBEN.warnung, fontWeight: 600 }}>✋ {letzteAblehnung}</span>}
        <span style={{ color: T.muted }}>Strg/⌘+K · Befehle</span>
      </div>

      {/* Dashboard v2.5 (§30 / UI-9): Command-Palette. Overlay `position: fixed` — außerhalb des
          Flusses, damit die Studio-Shell nicht überläuft (Kante 10). Bewusst KEINE im Rumpf
          definierte Komponente (Befund B1): das Filterfeld ist fokussierbar und würde sonst bei
          jedem Render neu montiert — der Fokus ginge bei jedem Tastendruck verloren.
          A11y in v2: role=dialog, aria-modal, aria-label, Autofokus, Esc. Ein vollständiger
          Fokus-Käfig (Tab-Zyklus, Fokus-Rückgabe) ist v6 und wird hier NICHT gebaut. */}
      {paletteOffen && (
        <div
          style={{ position: 'fixed', inset: 0, zIndex: 60, background: T.canvasWallGhost, display: 'flex', alignItems: 'flex-start', justifyContent: 'center', paddingTop: '12vh' }}
          onMouseDown={(e) => { if (e.target === e.currentTarget) schliessePalette(); }}
        >
          <div
            role="dialog" aria-modal="true" aria-label="Befehle suchen"
            onMouseDown={(e) => e.stopPropagation()}
            style={{ width: 460, maxWidth: '92vw', background: T.surface, border: `1px solid ${T.controlBorder}`, borderRadius: 12, boxShadow: `0 12px 34px ${T.canvasWallGhost}`, overflow: 'hidden' }}
          >
            <input
              autoFocus type="text" value={paletteFilter}
              aria-label="Befehl filtern"
              placeholder="Befehl suchen … (↑↓ wählen, Enter ausführen, Esc schließt)"
              onChange={(e) => { setPaletteFilter(e.target.value); setPaletteIndex(0); }}
              onKeyDown={(e) => {
                if (e.key === 'Escape') { e.preventDefault(); schliessePalette(); return; }
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                  e.preventDefault();
                  if (paletteListe.length === 0) return;
                  const d = e.key === 'ArrowDown' ? 1 : -1;
                  setPaletteIndex((paletteMarkiert + d + paletteListe.length) % paletteListe.length);
                  return;
                }
                if (e.key === 'Enter') {
                  e.preventDefault();
                  const treffer = paletteListe[paletteMarkiert];
                  if (treffer) aktivierePaletteEintrag(treffer.id, treffer.enabled);
                }
              }}
              style={{ width: '100%', boxSizing: 'border-box', padding: '11px 14px', fontSize: 13.5, border: 'none', borderBottom: `1px solid ${T.hair}`, color: FARBEN.text, background: T.surface }}
            />
            <div style={{ maxHeight: '46vh', overflowY: 'auto', padding: 6 }}>
              {paletteListe.length === 0 ? (
                /* Kante 7: kein leerer Kasten — der Leerzustand spricht aus, was los ist. */
                <div style={{ padding: '14px 10px', fontSize: 12.5, color: T.muted }}>{PALETTE_LEER}</div>
              ) : (
                paletteListe.map((eintrag, i) => {
                  const markiert = i === paletteMarkiert;
                  return (
                    <button
                      key={eintrag.id} type="button" tabIndex={-1}
                      aria-disabled={!eintrag.enabled}
                      title={eintrag.enabled ? eintrag.label : (eintrag.grund ?? eintrag.label)}
                      onMouseEnter={() => setPaletteIndex(i)}
                      onClick={() => aktivierePaletteEintrag(eintrag.id, eintrag.enabled)}
                      style={{
                        display: 'flex', alignItems: 'center', gap: 8, width: '100%', textAlign: 'left',
                        padding: '7px 10px', border: 'none', borderRadius: 8, fontSize: 12.5,
                        /* Markierung als Hintergrund UND Schriftschnitt, nicht nur farblich. */
                        background: markiert ? T.brandWash : 'transparent',
                        fontWeight: markiert ? 700 : 500,
                        color: eintrag.enabled ? FARBEN.text : T.muted,
                        cursor: eintrag.enabled ? 'pointer' : 'not-allowed',
                      }}
                    >
                      <span style={{ flex: '0 0 auto', minWidth: 74 }}>{eintrag.label}</span>
                      {/* Der Grund steht als sichtbarer TEXT, nicht nur als Ausgrauen (§28). */}
                      {!eintrag.enabled && <span style={{ flex: 1, fontSize: 11, color: T.warnInk }}>{eintrag.grund}</span>}
                      {eintrag.enabled && <span style={{ flex: 1 }} />}
                      {eintrag.shortcut && <span style={{ flex: '0 0 auto', fontSize: 10.5, color: T.muted, border: `1px solid ${T.controlBorder}`, borderRadius: 4, padding: '1px 5px' }}>{eintrag.shortcut}</span>}
                    </button>
                  );
                })
              )}
            </div>
          </div>
        </div>
      )}

      {/* AUF-33/L2: die Fläche einer Rechen-Engine. Sie liegt hier und nicht im Studio, weil der
          Auslöser hier liegt — der Fachplaner-Reiter der linken Schiene. Kopf, Zweck, Zurück und
          Escape kommen aus derselben `FlaechenHuelle` wie die L4-Flächen (AUF-25), kein zweiter
          Rahmen. Unbekannte Engine ⇒ nichts, kein Wurf. */}
      {offeneEngine && enginePanel(offeneEngine) && (
        <EngineFlaeche
          panel={enginePanel(offeneEngine)!}
          gruppe={faehigkeitNach(offeneEngine)?.gruppe ?? 'Fachplaner'}
          zustand={faehigkeitNach(offeneEngine)?.zustand ?? 'in_entwicklung'}
          zurueck="Zurück zum Planer"
          onZurueck={() => setOffeneEngine(null)}
        />
      )}
    </div>
  );
}
