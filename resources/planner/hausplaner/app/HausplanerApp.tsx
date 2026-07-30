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
// AUF-48 Scheibe 4c: Stage/Layer/Rect/Group/Circle sind mit der Buehne umgezogen.
// `Line` und `Text` bleiben: `rasterLinien` und `massElemente` werden HIER gebaut und der
// Buehne als fertige Elemente uebergeben.
import { Line, Text } from 'react-konva';
import type Konva from 'konva';
import { useHausplanerStore } from '../store/hausplanerStore';
import type { ObjectNode, OpeningNode, RoofNode, RoofAnbauMasse, SceneNode, WallNode } from '../domain/scene.types';
// AUF-48 Scheibe 4a: FARBEN steht jetzt neben `T` — der Kopfrahmen braucht sie auch,
// und ein Import aus dieser Datei waere ein Ringschluss. Umgezogen, nicht verdoppelt.
import { T, FARBEN } from './studioDaten';
import { erkenneRaeume } from '../geometry/roomDetection';
import { bemassung } from '../geometry/bemassung';
import { wandLaenge, wandBaender, type Punkt } from '../geometry/wallGeometry';
import { TUER_TYPEN, FENSTER_TYPEN, tuerTyp, fensterTyp, type TuerTyp, type FensterTyp } from '../geometry/oeffnungsTypen';
import { DreiDBereich } from './DreiDBereich';
import { aufloeseAuswahlmodus, wendeAuswahlAn, klickInsLeere } from './tools/auswahlModus';
import { mehrfachUebersicht } from './tools/auswahlUebersicht';
import { EngineFlaeche } from './EngineFlaeche';
import { enginePanel } from './dashboard/enginePanels';
import { faehigkeitNach } from './tools/faehigkeiten';
import { type Pan } from './dashboard/pan';
import { einpassen, knotenPunkte } from './dashboard/einpassen';
import { buehnenHoehe, useGemesseneHoehe } from './dashboard/buehnenHoehe';
import { buehnenBreite, useGemesseneBreite } from './dashboard/buehnenBreite';
import { speicherAnzeige, type AnzeigeArt } from './dashboard/speicherAnzeige';
import { naechsterSchritt, wegweiserSatz } from './tools/naechsterSchritt';
import { TOOL_DEFINITIONS } from './tools/toolRegistry';
import { TOOL_KATALOG } from './tools/toolCatalog';
import { handlungZuGrund } from './tools/vorbedingungen';
import { brauchtOptionen } from './tools/werkzeugVertrag';
// AUF-48 Scheibe 4b: die sieben Werkzeugarten wohnen bei den Werkzeug-Modulen —
// die ausgelagerte Schiene braucht sie auch, und ein Rueckgriff hierher waere ein Ringschluss.
import type { Werkzeug } from './tools/werkzeugArten';
import { SCHIENE_STANDARD, type SchienenReiterId } from './dashboard/schienenReiter';
import { arbeitsbereich } from './dashboard/arbeitsbereiche';
// AUF-48 Scheibe 4a: der obere Rahmen des JSX.
import { Kopfrahmen } from './dashboard/Kopfrahmen';
// AUF-48 Scheibe 4b: die Zeilen unter dem Kopfrahmen und die linke Schiene.
import { ArbeitsbereichZeilen, PlanerSchiene } from './rahmen/GruppenzeileUndSchiene';
// AUF-48 Scheibe 4c: die Konva-Ebenen des 2D-Grundrisses.
import { Buehne } from './rahmen/Buehne';
// AUF-48 Scheibe 4d: das rechte Eigenschaften-Panel.
// `aktiverTab` bleibt HIER (K-02: kein Zustand ins Panel) — deshalb bleibt auch sein Typ.
import { type PanelTabId } from './dashboard/panelTabs';
import { EigenschaftenPanel } from './rahmen/EigenschaftenPanel';
// AUF-48 Scheibe 4e: Statusleiste, Befehlspalette, Engine-Flaeche.
import { FussUndUeberlagerungen } from './rahmen/FussUndUeberlagerungen';
import { useEscapeEbene } from './dashboard/escapeStapel';
import { ladeSchienen, speichereSchienen, SCHIENEN_STANDARD, type SchienenSeite, type SchienenZustand } from './state/schienenSpeicher';
// AUF-48 Scheibe 1: die sieben reinen Funktionen wohnen jetzt daneben — unveraendert, nur umgezogen.
import { uuid, istWand, istOeffnung, lotAufWand } from './reineHelfer';
// AUF-48 Scheibe 2: die reinen Ableitungen. Die useMemo-Huellen und ihre Abhaengigkeitslisten
// bleiben hier stehen — sie sind React-Bindung, kein Rechenweg.
import {
  knotenImGeschoss, waendeAus, raeumeAus, leisteMitAngehefteten, werkzeugKontextAus,
  ermittleWegweiser, fremderBereichVon, palettenGruppenFuer,
} from './ableitungen';
import { gruppenFuer } from './dashboard/werkzeugGruppen';
import { ladeArbeitsbereich, speichereArbeitsbereich } from './state/arbeitsbereichSpeicher';
import {
  FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_WAND_DA,
  FAEHIGKEIT_ANSICHT_BEREIT,
} from './tools/vorbedingungen';
import { projektBaum } from './dashboard/projektBaum';
import { befundeAus } from './dashboard/befunde';
import { palettenGruppen, palettenFlach, type PaletteEintrag } from './dashboard/palette';
import { stapel } from './dashboard/geschossStapel';
import { usePlannerUiStore } from './state/uiState';
import { toolNach, WORKSPACE_IMPORT } from './tools/toolRegistry';
// AUF-48 Scheibe 3: die reine Abbildung Taste -> Absicht.
import { tastenAbsicht } from './tastenAbsicht';
import { zoneTools } from './tools/toolPresentation';
import { WERKZEUG_GRUPPEN } from './dashboard/werkzeugGruppen';
import { ladeAngeheftet, speichereAngeheftet, umschalten } from './state/angeheftet';
import { resolveToolState } from './tools/activation';
import { baueAktivierungsKontext } from './tools/toolContext';
import type { ObjectType, ViewType } from './tools/toolTypes';
import { versetzteWand, spiegelteWand, bbox as punkteBbox, achsenMitte, type Achse } from '../geometry/editierGeometrie';
import { dupliziereGeschoss } from '../geometry/geschossVorlage';
import { treppeZuParametern, parametereZuTreppe, type TreppeParams } from '../geometry/treppeObjekt';

// Basis-URL der Icon-Assets — aus dem Bundle-Standort abgeleitet (traegt Subpfad/Domain).
  // AUF-48 Scheibe 4d: `ICON_BASE` ist mit dem Eigenschaften-Panel umgezogen.


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
  // AUF-48 Scheibe 4d: `PANEL_ID` ist mit dem Eigenschaften-Panel umgezogen.
  // AUF-48 Scheibe 4d: `reiterId` ist mit dem Eigenschaften-Panel umgezogen.

// AUF-48 Scheibe 4b: `SCHIENE_ID` und `schienenReiterId` sind mit der Schiene umgezogen.

/** AUF-34 — dasselbe für den Arbeitsbereich-Wähler. Eigenes Präfix, keine id-Kollision. */
const BEREICH_ID = 'hp-bereich-gruppenzeile';
const bereichReiterId = (id: string): string => `hp-bereich-tab-${id}`;
// AUF-48 Scheibe 4a: `bereichReiter` ist mit dem Bereich-Waehler in den Kopfrahmen gezogen —
// er war der einzige Nutzer. Die Reiter-Daten kommen weiterhin aus ARBEITSBEREICHE.


// L1 Layout-Aktivierung — Navigations-Stile (tokens-konform: neutral, Marke nur als Auswahl-Akzent).
// AUF-48 Scheibe 1: DREI tote Stilkonstanten sind hier ersatzlos entfallen — sie hatten je genau
// ein Vorkommen, ihre eigene Definition (Namen im Auftragsblatt, hier bewusst NICHT wiederholt:
// die Abwesenheits-Zusage K-02 ist ein schlichtes `grep`, und ein Kommentar, der die Namen nennt,
// haette sie rot gemacht — ein erklaerender Satz darf eine Pruefung nicht entwerten).
// AUF-48 Scheibe 4b: `navItem` hatte ihren einzigen Nutzer in der Schiene und ist mitgezogen.
// Batch 0: die frühere FACHPLANER-Attrappe (inerte `geplant`-Labels) ist durch die datengetriebene
// Fähigkeiten-Navi (app/tools/faehigkeiten.ts + FaehigkeitenNavi) ersetzt — eine Wahrheit, mit Zustand.

/**
 * AUF-83-T5 / K-05 — ist das Fenster zu schmal, damit eine Schiene den Platz noch VERDRÄNGEN darf?
 *
 * **Die Schwelle ist 1024 px, wörtlich aus dem Auftrag:** „ab 1024 px verdrängt eine offene
 * Schiene wie heute. Darunter liegt sie über der Bühne." `max-width: 1023px` ist deshalb die
 * Grenze, nicht 1024 — bei genau 1024 gilt noch die verdrängende (heutige) Regel.
 *
 * **`matchMedia` statt ein eigener `resize`-Zuhörer:** das Ereignis `change` feuert nur, wenn die
 * Schwelle wirklich über- oder unterschritten wird, nicht bei jedem Pixel — dieselbe Ökonomie wie
 * der `ResizeObserver` in `buehnenBreite.ts`, nur für eine Schwelle statt eine Zahl.
 */
function useIstSchmal(): boolean {
  const [schmal, setSchmal] = useState(() => (typeof window === 'undefined' ? false : window.innerWidth < 1024));
  useEffect(() => {
    if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') return undefined;
    const abfrage = window.matchMedia('(max-width: 1023px)');
    const aktualisieren = (): void => setSchmal(abfrage.matches);
    aktualisieren();
    abfrage.addEventListener('change', aktualisieren);
    return () => abfrage.removeEventListener('change', aktualisieren);
  }, []);
  return schmal;
}

// AUF-48 Scheibe 4b: `KontextOptionenLeiste` ist mit ihrer Zeile nach
// `rahmen/GruppenzeileUndSchiene.tsx` gezogen — sie hatte hier genau einen Nutzer.

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
  /**
   * AUF-47: **kann** diese Fläche überhaupt speichern? Die Antwort stand immer im Store
   * (`speichernUrl`), wurde aber nie gelesen — deshalb sagte die Plakette „Gespeichert" auf einer
   * Testfläche, die noch nie etwas gespeichert hat.
   */
  const kannSpeichern = useHausplanerStore((s) => Boolean(s.speichernUrl));
  const konfliktRevision = useHausplanerStore((s) => s.konfliktRevision);
  const letzteAblehnung = useHausplanerStore((s) => s.letzteAblehnung);
  const modus = useHausplanerStore((s) => s.modus);
  const store = useHausplanerStore;

  // UI-2: aktives Werkzeug liegt jetzt im GETEILTEN UI-State (state/uiState.ts), nicht mehr lokal —
  // dadurch für Studio-Shell/Kontextleiste/Activation-Engine lesbar. Variablennamen bleiben, damit
  // die bestehenden Nutzungen unverändert bleiben (verhaltensgleich).
  const werkzeug = usePlannerUiStore((s) => s.activeToolId) as Werkzeug;
  const setWerkzeug = React.useCallback((w: Werkzeug) => usePlannerUiStore.getState().setActiveTool(w), []);
  // AUF-60: die Rechte des angemeldeten Nutzers — gelesen aus dem UI-State, den `main.tsx` aus dem
  // Blade befüllt hat. Vorher stand hier ein gesetzter Wert; damit sperrte die Oberfläche nach
  // einer Angabe, die sie sich selbst gab. Grundzustand ist die leere Liste (Minimum).
  const rechte = usePlannerUiStore((s) => s.rechte);
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
  /** AUF-43: ob die Geschoss-Fläche offen ist. Reine Bedien-Anzeige, kein Modellzustand. */
  const [geschossOffen, setGeschossOffen] = useState(false);
  /** AUF-83-T3-N1 — der Überlauf für Übernehmen-Knopf, Staleness-Pille und Speicherstatus. */
  const [objektkopfMenuOffen, setObjektkopfMenuOffen] = useState(false);
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
  /**
   * AUF-51: die Lage der Zeichenfläche. `null` = nie verschoben ⇒ die Standardlage folgt weiter der
   * Fensterhöhe. Vorher stand die Position als gesteuerter Wert OHNE Zustand im JSX — jedes Rendern
   * (und `onMouseMove` rendert bei jeder Bewegung) setzte den Verschub zurück.
   */
  const [pan, setPan] = useState<Pan | null>(null);
  const [rasterAn, setRasterAn] = useState(true);
  const stageRef = useRef<Konva.Stage | null>(null);
  /** AUF-72: das Element, das die Bühne trägt — sein Platz IST die Bühnenhöhe. */
  const inhaltRef = useRef<HTMLDivElement | null>(null);
  const gemesseneHoehe = useGemesseneHoehe(inhaltRef);
  /**
   * AUF-42 — **die Bühnenbreite steht jetzt HIER oben**, weil die Fähigkeit `viewport.ready` sie
   * braucht. Vorher stand sie 600 Zeilen tiefer; die Rechnung ist **unverändert**, nur ihr Ort.
   * *Eine Wahrheit: die Breite wird an einer Stelle bestimmt, nicht zweimal.*
   */
  const gemesseneBreite = useGemesseneBreite(inhaltRef);
  const breite = buehnenBreite(gemesseneBreite);
  const stageBreite = modus === 'split' ? Math.floor(breite / 2) : breite; // P1c: Split teilt die Fläche

  const level = scene?.levels.find((l) => l.id === activeLevelId) ?? scene?.levels[0] ?? null;
  const nodes = useMemo(() => knotenImGeschoss(scene, level), [scene, level]);
  const waende = useMemo(() => waendeAus(nodes), [nodes]);
  /** AUF-35a / Kante 4: Zählung der Auswahl je Typ — rein, getestet, nur Anzeige. */
  const auswahlUebersicht = useMemo(() => mehrfachUebersicht(selectedNodeIds, nodes), [selectedNodeIds, nodes]);

  // UI-3: Aktivierungs-Kontext für die Werkzeugleiste (§21). Sammelt die getrennten Wahrheiten:
  // Arbeitsbereich (UI-State) · Ansicht (store.modus) · Auswahltypen (Modell). Rechte sind im
  // Editor angenommen; die Werkzeugleisten-Werkzeuge (art='werkzeug') prüfen ohnehin keine Rechte.
  const activeWorkspace = usePlannerUiStore((s) => s.activeWorkspace);
  /** AUF-83-T3 / K-01 — der Objektkopf aus dem Blade. Im Studio `null`: dort gibt es kein Objekt. */
  const objektkopf = usePlannerUiStore((s) => s.objektkopf);
  /** AUF-88-P1 — die Referenzunterlage aus dem Blade. Im Studio `null`: dort gibt es kein Objekt. */
  const unterlage = usePlannerUiStore((s) => s.unterlage);
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
  /**
   * AUF-83-T5 / K-04 — der Klappzustand beider Schienen, je Arbeitsbereich gemerkt.
   *
   * **Dasselbe Muster wie `activeWorkspace` oben:** die Wahrheit ist lokaler Zustand
   * (`useState`), `localStorage` ist nur das Gedächtnis über einen Neuladen hinweg. Wechselt der
   * Arbeitsbereich, wird NEU geladen — wer in „Elektro · PV" zugeklappt hat, findet
   * „Architektur" unverändert, weil dessen eigener Eintrag gelesen wird.
   */
  const [schienen, setSchienen] = useState<SchienenZustand>(SCHIENEN_STANDARD);
  useEffect(() => {
    setSchienen(ladeSchienen(activeWorkspace));
  }, [activeWorkspace]);
  const klappeSchiene = React.useCallback((seite: SchienenSeite, offen: boolean) => {
    setSchienen((alt) => {
      const neu = { ...alt, [seite]: offen };
      speichereSchienen(activeWorkspace, neu);
      return neu;
    });
  }, [activeWorkspace]);
  /** AUF-83-T5 / K-05: unter 1024 px legt sich eine offene Schiene über die Bühne. */
  const istSchmal = useIstSchmal();
  /**
   * AUF-83-T5 / K-03 — eine offene Schiene ist nur dann eine „Ebene", wenn sie gerade als
   * Overlay liegt (schmales Fenster). Verdrängend (≥1024 px) ist sie normales, dauerhaftes
   * Layout — ein Escape soll dort nicht versehentlich die Leiste zuklappen, während man zeichnet.
   */
  useEscapeEbene('schiene', istSchmal && schienen.links, () => klappeSchiene('links', false));
  useEscapeEbene('schiene', istSchmal && schienen.rechts, () => klappeSchiene('rechts', false));
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
  const railWerkzeuge = useMemo(() => leisteMitAngehefteten(angeheftet), [angeheftet]);
  const werkzeugKontext = useMemo(
    () => werkzeugKontextAus({
      workspace: activeWorkspace,
      view: modus,
      selectedNodeIds,
      nodes,
      rechte,
      hatSzene: Boolean(scene),
      hatGeschoss: Boolean(level),
      wandZahl: waende.length,
      stageBreite,
    }),
    [activeWorkspace, modus, selectedNodeIds, nodes, scene, level, waende.length, rechte, stageBreite],
  );
  /**
   * AUF-45 — der Wegweiser. **Keine zweite Aktivierungs-Engine:** gezählt werden die Zustände, die
   * `resolveToolState` ohnehin liefert; die Zahl im Satz ist die **gemessene** Differenz zwischen
   * jetzt und dem Zustand nach dem Schritt — dieselbe Engine, nur ein zweites Mal gefragt.
   * Ohne benannte Handlung zu dem Grund schweigt der Wegweiser, statt zu raten.
   */
  const wegweiser = useMemo(() => ermittleWegweiser(werkzeugKontext, nodes), [werkzeugKontext, nodes]);

  /**
   * AUF-34 / Kante 3 — gilt das aktive Werkzeug im gewählten Arbeitsbereich? Der Name des fremden
   * Bereichs, sonst `undefined`. Gelesen wird `supportedWorkspaces`, also **dieselbe** Quelle, die
   * `resolveToolState` als erste Regel prüft — keine zweite Beurteilung.
   */
  const fremderBereich = useMemo(() => fremderBereichVon(werkzeug, activeWorkspace), [werkzeug, activeWorkspace]);

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
  /**
   * AUF-67 — **die Palette fragt die Register, sie weiss nichts selbst.** `baum` und `wegweiser`
   * sind dieselben Ergebnisse, die die Oberflaeche ohnehin anzeigt; der Geschoss-Stapel kommt aus
   * derselben Funktion, die die Geschossflaeche benutzt. Kein Register wird hier zweimal gerechnet.
   */
  const paletteGruppen = useMemo(
    () => palettenGruppenFuer({
      kontext: werkzeugKontext,
      stapel: scene ? stapel(scene.levels, activeLevelId) : null,
      baum,
      schritt: wegweiser,
      filter: paletteFilter,
    }),
    [werkzeugKontext, paletteFilter, scene, activeLevelId, baum, wegweiser],
  );
  /** Eine Liste, zwei Darstellungen: die Tastatur laeuft ueber genau die Reihenfolge, die man sieht. */
  const paletteListe = useMemo(() => palettenFlach(paletteGruppen), [paletteGruppen]);
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
  const raeume = useMemo(() => raeumeAus(waende, level), [waende, level]);
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
  // AUF-48 Scheibe 4d: `aktDach` ist mit dem Eigenschaften-Panel umgezogen.

  // P2b-1: Mauerwerk-Katalog (materialId-Wert + Anzeige) + gängige Wandstärken (mm).
  // AUF-48 Scheibe 4d: `MAUERWERK` ist mit dem Eigenschaften-Panel umgezogen.
  // AUF-48 Scheibe 4d: `WANDSTAERKEN` ist mit dem Eigenschaften-Panel umgezogen.

  // P2b-1: genau EINE ausgewählte Wand ⇒ Mauerwerk-/Stärke-Panel; Änderungen als UPDATE_NODE (additiv).
  const selectedWall = waende.find((w) => primaerId === w.id) ?? null;
  // AUF-48 Scheibe 4d: `aktWand` ist mit dem Eigenschaften-Panel umgezogen.
  // Bearbeiten: Wand-Laenge exakt setzen -> Wandende entlang der Achse verschieben (MOVE_NODE).
  // AUF-48 Scheibe 4d: `setzeLaenge` ist mit dem Eigenschaften-Panel umgezogen.
  // P2b-4: genau EINE ausgewählte Öffnung ⇒ Öffnungs-Panel (Tür: Anschlag/Öffnung); UPDATE_NODE (additiv).
  const selectedOpening = (nodes.find((n) => istOeffnung(n) && primaerId === n.id) ?? null) as OpeningNode | null;
  // AUF-48 Scheibe 4d: `aktOeffnung` ist mit dem Eigenschaften-Panel umgezogen.
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
  // AUF-48 Scheibe 4d: `aktTreppe` ist mit dem Eigenschaften-Panel umgezogen.
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
  // AUF-48 Scheibe 4a: `OP_TOKEN`, `opStil` und `OpBtn` sind mit der Bedien-Werkzeugleiste in
  // den Kopfrahmen gezogen. Sie hatten hier keinen zweiten Nutzer — gemessen, nicht vermutet.
  /**
   * Dashboard v2.5 — Enter in der Palette. Werkzeuge setzen den Modus (wie die Werkzeugleiste),
   * Aktionen rufen die BEREITS VORHANDENEN Funktionen `loescheAuswahl`/`dupliziere`. Es entsteht
   * kein zweiter Ausführungsweg; deaktivierte Einträge werden hier hart abgewiesen.
   */
  /**
   * AUF-67 — **die Palette fuehrt hin; sie erfindet nichts.** Jede Art bildet auf eine Handlung ab,
   * die es ohne die Palette auch gibt: Geschoss wechseln, Bauteil auswaehlen, Bereich wechseln.
   * Keine dieser Zeilen ist eine neue Aktion.
   */
  function aktivierePaletteEintrag(eintrag: PaletteEintrag): void {
    if (!eintrag.enabled) return;
    const id = eintrag.id;
    if (eintrag.art === 'geschoss') { schliessePalette(); store.getState().setActiveLevel(id); return; }
    if (eintrag.art === 'bauteil') { schliessePalette(); store.getState().selectNodes([id]); return; }
    if (eintrag.art === 'bereich') { schliessePalette(); waehleBereich(id); return; }
    if (eintrag.art === 'schritt') {
      // Der Wegweiser nennt einen ORT, keine Aktion — dorthin wird gefuehrt, mehr sagt er nicht.
      schliessePalette();
      if (id === 'schiene') setSchienenTab('werkzeuge');
      return;
    }
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
  // AUF-48 Scheibe 4a: `opSep` und `OpGruppe` sind mit der Bedien-Werkzeugleiste umgezogen.
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
  /**
   * AUF-62 — „Ansicht einpassen". **Anzeige, kein Modellzustand:** setzt `zoom` und `pan`, löst
   * keinen Befehl aus und rührt das Dokument nicht an. Eingepasst wird in `stageBreite` — in der
   * Split-Ansicht ist das die **halbe** Fensterbreite; wer `breite` nähme, passte in eine Fläche
   * ein, die es nicht gibt, und der halbe Grundriss stünde außerhalb.
   */
  function passeAnsichtEin(): void {
    const e = einpassen(knotenPunkte(nodes), stageBreite, hoehe);
    setZoom(e.zoom);
    setPan(e.pan);
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

  // AUF-83-T5 / K-03 — Escape läuft über den geteilten Stapel, nicht über eine feste Reihenfolge
  // von if/else-Zweigen hier. **Rang**: Palette schlägt Werkzeug-Reset, das die niedrigste Stufe
  // der Rangfolge ist (`RANGFOLGE` in `escapeStapel.ts`) — `aktiv=true` fest ist richtig, weil der
  // Stapel selbst entscheidet, ob gerade eine höhere Ebene (Palette/Dialog/Menü/Schiene) dran ist;
  // Werkzeug-Reset gewinnt nur, wenn KEINE von ihnen offen ist.
  useEscapeEbene('palette', paletteOffen, schliessePalette);
  // AUF-83-T5-Nachbesserung: `useCallback` mit leeren Abhängigkeiten — eine Inline-Funktion hier
  // wäre bei JEDEM Render neu, und `HausplanerApp` rendert in Mausbewegungs-Frequenz. Ohne
  // Memoisierung meldete sich diese Ebene bei jeder Bewegung ab und wieder an (`escapeStapel.ts`
  // vergleicht Objektidentität in seinem Effekt-Abhängigkeitsfeld) — funktional meist harmlos,
  // aber unnötige Listener-Kirchgänge, und beim Debuggen einer echten Race genau das Rauschen,
  // das eine Ursache verdeckt.
  const setzeWerkzeugZurueck = React.useCallback(() => {
    setWandStart(null);
    setTreppeStart(null);
    setWerkzeug('auswahl');
  }, []);
  useEscapeEbene('werkzeug-reset', true, setzeWerkzeugZurueck);

  useEffect(() => {
    function taste(e: KeyboardEvent): void {
      // AUF-48 Scheibe 3: WELCHE Absicht die Taste trägt, entscheidet die reine Abbildung
      // `tastenAbsicht`. WER sie ausführt, bleibt hier — dort liegen Store und Zustand.
      const absicht = tastenAbsicht({
        key: e.key,
        ctrlKey: e.ctrlKey,
        metaKey: e.metaKey,
        zielIstEingabe: (e.target as HTMLElement)?.tagName === 'INPUT',
        paletteOffen: paletteOffenRef.current,
      });
      if (absicht.preventDefault) {
        e.preventDefault();
      }
      switch (absicht.art) {
        case 'loeschen':
          for (const id of store.getState().selectedNodeIds) {
            store.getState().executeCommand({ type: 'REMOVE_NODE', nodeId: id });
          }
          store.getState().selectNodes([]);
          break;
        case 'rueckgaengig':
          store.getState().undo();
          break;
        case 'wiederholen':
          store.getState().redo();
          break;
        case 'speichern':
          void store.getState().save();
          break;
        case 'palette-oeffnen':
          oeffnePalette();
          break;
        case 'werkzeug': {
          // UI-3: die Aktivierung wird weiterhin respektiert — sie braucht den Kontext aus den
          // Stores und gehört deshalb hierher, nicht in die Abbildung.
          const tool = toolNach(absicht.werkzeugId!);
          if (!tool) break;
          const ctx = baueAktivierungsKontext({
            workspace: usePlannerUiStore.getState().activeWorkspace,
            view: store.getState().modus as ViewType,
            selectionTypes: [],
            permissions: usePlannerUiStore.getState().rechte,
          });
          if (resolveToolState(tool, ctx).enabled) {
            setWandStart(null);
            setTreppeStart(null);
            setWerkzeug(tool.id as Werkzeug);
          }
          break;
        }
        default:
          break;
      }
    }
    window.addEventListener('keydown', taste);

    return () => window.removeEventListener('keydown', taste);
  }, [store, oeffnePalette]);

  if (!scene || !level) {
    return <div style={{ padding: 24, color: FARBEN.text }}>Szene nicht geladen.</div>;
  }

  // AUF-47: Text, Gewicht und Knopf-Sperre kommen aus `speicherAnzeige` (rein, getestet); hier
  // stehen nur noch die Token je Gewicht. Vorher trug diese Tabelle die Aussage selbst — und kannte
  // den Fall „kann gar nicht speichern" nicht.
  const anzeige = speicherAnzeige(speicherStatus, kannSpeichern, konfliktRevision);
  const ANZEIGE_TOKEN: Record<AnzeigeArt, { farbe: string; grund: string }> = {
    ok: { farbe: FARBEN.erfolg, grund: T.okSoft },
    warnung: { farbe: FARBEN.warnung, grund: T.warnSoft },
    neutral: { farbe: FARBEN.gedaempft, grund: T.hair2 },
    fehler: { farbe: FARBEN.gefahr, grund: T.errSoft },
  };
  const statusPill = { text: anzeige.text, ...ANZEIGE_TOKEN[anzeige.art] };

  /**
   * AUF-70 — der Textknopf liest jetzt **dieselbe** Zustandsregel wie die Icon-Knöpfe.
   *
   * **Der gemessene Mangel:** `Rückgängig` (gesperrt) und `Split` (frei) waren Pixel für Pixel
   * gleich — Deckkraft 1, Mauszeiger `pointer`, Schrift `rgb(55,65,81)`, Rahmen und Hintergrund
   * identisch. Wer die Fläche frisch öffnet, sieht zwei Knöpfe, die aussehen wie alle anderen und
   * nicht reagieren; **die einzig mögliche Deutung ist „kaputt".** Die Umkehr ist aber heil — der
   * Fehler lag in der Darstellung.
   *
   * AUF-59 hat das für `OpBtn` gelöst und `knopf()` dabei liegenlassen. Eine Regel, die nur die
   * halbe Oberfläche erreicht, ist keine Regel. Hier wird deshalb **gelesen, nicht kopiert**:
   * `opKnopfBild` bleibt die einzige Beschreibung der drei Zustände; diese Funktion steuert nur
   * die Geometrie des Textknopfes bei (Polsterung, Schriftgröße) — nicht seine Zustandsfarben.
   */
  // AUF-48 Scheibe 4d: `knopf` ist mit dem Eigenschaften-Panel umgezogen.

  // AUF-48 Scheibe 4d: `panelLabel` ist mit dem Eigenschaften-Panel umgezogen.
  // AUF-48 Scheibe 4d: `panelInput` ist mit dem Eigenschaften-Panel umgezogen.

  const railIcon = (w: string): string => (({ auswahl: '\u2196', wand: '\u25AC', fenster: '\u25A2', tuer: '\u25D7', dach: '\u25B3' } as Record<string, string>)[w] ?? '\u2022');
  const railBtn = (aktiv: boolean): React.CSSProperties => ({
    display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
    height: 46, borderRadius: 9, cursor: 'pointer', fontWeight: 600,
    border: `1px solid ${aktiv ? T.brandInk : 'transparent'}`,
    background: aktiv ? T.brandSoft : 'transparent',
    color: aktiv ? T.brandInk : FARBEN.gedaempft,
  });

  /**
   * AUF-72 — die Bühnenhöhe kommt aus dem Platz, den sie **wirklich** hat.
   *
   * Hier stand die Fensterhöhe minus einer festen 96. Die stammte aus einer Zeit mit **einer** Leiste über
   * der Bühne; seither sind drei dazugekommen (AUF-34, AUF-68, AUF-70), gemessen 323–369 px. Die
   * Folge: die Bühne ragte 227–273 px unter das Fenster, und 28–38 % der Zeichenfläche waren
   * unerreichbar — nicht wegzuscrollen.
   *
   * Gemessen wird die **Inhaltsreihe** (`inhaltRef`): Fenster minus alles darüber, ohne dass
   * irgendwo eine Zahl gepflegt werden muss.
   */
  const hoehe = buehnenHoehe(gemesseneHoehe);

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
    <div style={{ fontFamily: 'Inter, system-ui, sans-serif', color: FARBEN.text, height: '100%', display: 'flex', flexDirection: 'column', background: T.bg }}>
      {/* AUF-48 Scheibe 4a — **der obere Rahmen wohnt jetzt in `dashboard/Kopfrahmen.tsx`.**
          Werkzeugzeile, Objektkopf, Ueberlauf, Arbeitsbereich-Waehler, Einstieg zur Befehlspalette
          und Bedien-Werkzeugleiste standen hier als 192 Zeilen JSX. **Entnommen, nicht umgebaut:**
          das Markup ist zeichengleich, die Eigenschaften sind einzeln benannt.
          Die Gruppenzeile darunter bleibt HIER — sie ist das Ziel der `aria-controls` und haengt
          an `offeneGruppe`/`sichtbareGruppen`; sie ist Scheibe 4b. */}
      <Kopfrahmen
        imStudio={imStudio}
        scene={scene}
        level={level}
        wegweiser={wegweiser}
        geschossOffen={geschossOffen}
        setGeschossOffen={setGeschossOffen}
        dupliziereGeschossJetzt={dupliziereGeschossJetzt}
        objektkopf={objektkopf}
        statusPill={statusPill}
        objektkopfMenuOffen={objektkopfMenuOffen}
        setObjektkopfMenuOffen={setObjektkopfMenuOffen}
        anzeige={anzeige}
        activeWorkspace={activeWorkspace}
        waehleBereich={waehleBereich}
        panelId={BEREICH_ID}
        reiterId={bereichReiterId}
        oeffnePalette={oeffnePalette}
        modus={modus}
        zoom={zoom}
        setZoom={setZoom}
        passeAnsichtEin={passeAnsichtEin}
        rasterAn={rasterAn}
        setRasterAn={setRasterAn}
        selectedNodeIds={selectedNodeIds}
        dupliziere={dupliziere}
        loescheAuswahl={loescheAuswahl}
        waende={waende}
        spiegeleGrundriss={spiegeleGrundriss}
        exportPng={exportPng}
      />

      {/* AUF-48 Scheibe 4b — **die Themen-Gruppen-Zeile und die Kontext-Options-Leiste** wohnen
          jetzt in `rahmen/GruppenzeileUndSchiene.tsx`. Entnommen, nicht umgebaut. */}
      <ArbeitsbereichZeilen
        panelId={BEREICH_ID}
        bereichReiterId={bereichReiterId}
        activeWorkspace={activeWorkspace}
        offeneGruppe={offeneGruppe}
        setOffeneGruppe={setOffeneGruppe}
        werkzeugKontext={werkzeugKontext}
        werkzeug={werkzeug}
        angeheftet={angeheftet}
        heftUm={heftUm}
        sichtbareGruppen={sichtbareGruppen}
        fensterTypWahl={fensterTypWahl}
        tuerTypWahl={tuerTypWahl}
        setFensterTypWahl={setFensterTypWahl}
        setTuerTypWahl={setTuerTypWahl}
        fremderBereich={fremderBereich}
      />

      {/* Canvas: 2D (Konva) + 3D (three) nebeneinander — beide lesen DENSELBEN Store.
          Der 3D-Bereich bleibt über Moduswechsel gemountet (nur ausgeblendet) ⇒ Kamera
          bleibt erhalten; dispose() erst beim Verlassen der Seite (Kante 6). */}
      {/* AUF-72: DIESE Reihe ist das Maßband. Ihre Höhe ist das Fenster minus der Zeilen darüber
          und hängt NICHT von der Bühne ab (`overflow: hidden`) — deshalb kann die Messung sich
          nicht selbst verschieben. */}
      <div ref={inhaltRef} style={{ flex: 1, position: 'relative', overflow: 'hidden', display: 'flex' }}>
        {/* AUF-48 Scheibe 4b — **die Planer-Schiene** wohnt jetzt in
            `rahmen/GruppenzeileUndSchiene.tsx`. **Die Reihe darum bleibt HIER:** sie ist das
            Maszband aus AUF-72 und umschliesst auch Buehne, 3D-Bereich und Eigenschaften-Panel. */}
        <PlanerSchiene
          istSchmal={istSchmal}
          schienen={schienen}
          klappeSchiene={klappeSchiene}
          schienenTab={schienenTab}
          setSchienenTab={setSchienenTab}
          wegweiser={wegweiser}
          activeWorkspace={activeWorkspace}
          unterlage={unterlage}
          stageRef={stageRef}
          weltPunkt={weltPunkt}
          railWerkzeuge={railWerkzeuge}
          werkzeug={werkzeug}
          werkzeugKontext={werkzeugKontext}
          setWerkzeug={setWerkzeug}
          setWandStart={setWandStart}
          setTreppeStart={setTreppeStart}
          setOffeneEngine={setOffeneEngine}
          baum={baum}
          selectedNodeIds={selectedNodeIds}
          waehleAn={waehleAn}
        />
        <div style={{ display: modus === '3d' ? 'none' : 'block', width: stageBreite, borderRight: modus === 'split' ? `1px solid ${T.hair}` : 'none' }}>
        {/* AUF-48 Scheibe 4c — **die Buehne wohnt jetzt in `rahmen/Buehne.tsx`.** Alle Konva-Ebenen,
            292 Zeilen, zeichengleich entnommen. **Die Huelle darum bleibt HIER:** sie blendet die
            Buehne im 3D-Modus aus und traegt die Trennlinie im Split — beides haengt an `modus`,
            nicht an der Zeichnung. */}
        <Buehne
          stageBreite={stageBreite}
          hoehe={hoehe}
          zoom={zoom}
          setZoom={setZoom}
          pan={pan}
          setPan={setPan}
          rasterAn={rasterAn}
          rasterLinien={rasterLinien}
          stageRef={stageRef}
          scene={scene}
          level={level}
          nodes={nodes}
          waende={waende}
          raeume={raeume}
          bandVon={bandVon}
          massElemente={massElemente}
          selectedNodeIds={selectedNodeIds}
          unterlage={unterlage}
          werkzeug={werkzeug}
          cursor={cursor}
          setCursor={setCursor}
          wandStart={wandStart}
          treppeStart={treppeStart}
          klick={klick}
          weltPunkt={weltPunkt}
          mitWinkelSnap={mitWinkelSnap}
          waehleAn={waehleAn}
        />
        </div>
        <DreiDBereich sichtbar={modus !== '2d'} />
        {/* AUF-48 Scheibe 4d — **das Eigenschaften-Panel wohnt jetzt in
            `rahmen/EigenschaftenPanel.tsx`.** 411 Zeilen, zeichengleich entnommen; mit ihm die
            Helfer, die nur es benutzt hat. **Die Reihe darum bleibt HIER** — sie ist das Maszband
            aus AUF-72 und traegt auch Buehne und 3D-Bereich. */}
        <EigenschaftenPanel
          aktiverTab={aktiverTab}
          setAktiverTab={setAktiverTab}
          istSchmal={istSchmal}
          schienen={schienen}
          klappeSchiene={klappeSchiene}
          level={level}
          werkzeug={werkzeug}
          raeume={raeume}
          befunde={befunde}
          auswahlUebersicht={auswahlUebersicht}
          selectedNode={selectedNode}
          selectedWall={selectedWall}
          selectedOpening={selectedOpening}
          selectedRoof={selectedRoof}
          selectedStair={selectedStair}
          selectedStairParams={selectedStairParams}
          selectedObjekt={selectedObjekt}
          dupliziere={dupliziere}
          loescheAuswahl={loescheAuswahl}
        />
      </div>

      {/* AUF-48 Scheibe 4e — **Statusleiste, Befehlspalette und Engine-Flaeche** wohnen jetzt in
          `rahmen/FussUndUeberlagerungen.tsx`. 124 Zeilen, zeichengleich entnommen.
          **Der Zustand der Palette bleibt HIER** (K-02), und der Escape-Weg ebenfalls: er haengt
          am Escape-Stapel weiter oben, nicht am Markup. */}
      <FussUndUeberlagerungen
        cursor={cursor}
        zoom={zoom}
        raeume={raeume}
        werkzeug={werkzeug}
        wandStart={wandStart}
        treppeStart={treppeStart}
        letzteAblehnung={letzteAblehnung}
        paletteOffen={paletteOffen}
        paletteFilter={paletteFilter}
        setPaletteFilter={setPaletteFilter}
        setPaletteIndex={setPaletteIndex}
        paletteGruppen={paletteGruppen}
        paletteListe={paletteListe}
        paletteMarkiert={paletteMarkiert}
        schliessePalette={schliessePalette}
        aktivierePaletteEintrag={aktivierePaletteEintrag}
        offeneEngine={offeneEngine}
        setOffeneEngine={setOffeneEngine}
      />
    </div>
  );
}
