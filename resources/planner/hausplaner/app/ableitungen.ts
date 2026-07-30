/**
 * AUF-48 Scheibe 2 — **die reinen Ableitungen aus `HausplanerApp.tsx`.**
 *
 * **Was hier steht:** Rechnungen, die ausschließlich aus ihren Eingaben folgen. Kein Store, kein
 * Kontext, kein React-Zustand, kein Seiteneffekt. *Man kann sie aufrufen, ohne dass eine Oberfläche
 * existiert — genau deshalb sind sie prüfbar.*
 *
 * **Die `useMemo`-Hüllen bleiben in der Komponente.** Aus `useMemo(() => …, [a, b])` wird
 * `useMemo(() => X(a, b), [a, b])` — die Abhängigkeitslisten sind unverändert. **Die Hülle ist
 * React-Bindung, kein Rechenweg**; sie zu verschieben hieße, den Zeitpunkt der Auswertung zu
 * ändern, und das wäre eine Verhaltensänderung.
 *
 * **Was NICHT mitgekommen ist, und warum** — der Auftrag verlangt die Meldung ausdrücklich:
 *
 * | Ableitung | Grund |
 * |---|---|
 * | `waehleBereich` | schreibt in den UI-Store **und** in `localStorage` |
 * | `klappeSchiene` | schreibt React-Zustand (`setSchienen`) **und** `localStorage` |
 * | `oeffnePalette` | setzt React-Zustand und ein `ref` |
 * | `schliessePalette` | setzt React-Zustand und ein `ref` |
 *
 * *Alle vier sind Bedienhandlungen, keine Ableitungen — sie gehören in Scheibe 3.*
 *
 * **Sechs weitere sind hier bewusst NICHT gelandet**, obwohl sie rein sind: `auswahlUebersicht`,
 * `sichtbareGruppen`, `leistenWerkzeuge`, `baum`, `befunde`, `paletteListe` bestehen bereits aus
 * **genau einem Aufruf einer benannten reinen Funktion** (`mehrfachUebersicht`, `gruppenFuer`,
 * `zoneTools`, `projektBaum`, `befundeAus`, `palettenFlach`). *Sie hier zu umhüllen ergäbe eine
 * Funktion, die nichts tut, als eine andere zu rufen — eine Schicht ohne Aussage. Die Zerlegung
 * soll Rechenwege sichtbar machen, nicht Aufrufe zählen.*
 */
import type { Level, SceneDocument, SceneNode, WallNode } from '../domain/scene.types';
import { istWand } from './reineHelfer';
import { erkenneRaeume } from '../geometry/roomDetection';
import { zoneTools } from './tools/toolPresentation';
import { WERKZEUG_GRUPPEN } from './dashboard/werkzeugGruppen';
import { TOOL_DEFINITIONS, toolNach } from './tools/toolRegistry';
import { TOOL_KATALOG } from './tools/toolCatalog';
import { resolveToolState } from './tools/activation';
import { baueAktivierungsKontext } from './tools/toolContext';
import { handlungZuGrund } from './tools/vorbedingungen';
import { naechsterSchritt, wegweiserSatz } from './tools/naechsterSchritt';
import { arbeitsbereich } from './dashboard/arbeitsbereiche';
import { palettenGruppen, type PaletteEintrag } from './dashboard/palette';
import type { AktivierungsKontext, ObjectType, ViewType } from './tools/toolTypes';
import {
  FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_WAND_DA,
  FAEHIGKEIT_ANSICHT_BEREIT,
} from './tools/vorbedingungen';

/** Die Knoten des aktiven Geschosses. Ohne Szene oder ohne Geschoss: leer, nie `null`. */
export function knotenImGeschoss(scene: SceneDocument | null, level: Level | null): SceneNode[] {
  return scene && level ? scene.nodes.filter((n) => n.levelId === level.id) : [];
}

/** Nur die Wände daraus. */
export function waendeAus(nodes: readonly SceneNode[]): WallNode[] {
  return nodes.filter(istWand);
}

/** Die Räume des Geschosses. Ohne Geschoss: leer — die Raumerkennung braucht eine Wandhöhe. */
export function raeumeAus(waende: readonly WallNode[], level: Level | null): ReturnType<typeof erkenneRaeume> {
  return level ? erkenneRaeume(waende as WallNode[], level.defaultWallHeight) : [];
}

/**
 * Die linke Leiste: Pflichtwerkzeuge plus persönlich Angeheftetes.
 *
 * **Bewusst NICHT die 110** — eine Leiste mit 110 Einträgen ist keine Leiste mehr. Die Reihenfolge
 * ist bedeutsam: die festen zuerst, das Angeheftete dahinter.
 */
export function leisteMitAngehefteten(angeheftet: ReadonlySet<string>): ReturnType<typeof zoneTools> {
  const fix = zoneTools('fix');
  const feste = new Set(fix.map((w) => w.id));
  const zusatz = WERKZEUG_GRUPPEN.flatMap((g) => g.werkzeuge).filter((w) => angeheftet.has(w.id) && !feste.has(w.id));

  return [...fix, ...zusatz];
}

/** Die Eingaben des Aktivierungs-Kontexts — alles Werte, nichts Lebendiges. */
export interface KontextEingaben {
  workspace: string;
  view: string;
  selectedNodeIds: readonly string[];
  nodes: readonly SceneNode[];
  rechte: string[];
  hatSzene: boolean;
  hatGeschoss: boolean;
  wandZahl: number;
  stageBreite: number;
}

/**
 * Der Aktivierungs-Kontext der Werkzeugleiste (UI-3, §21).
 *
 * Sammelt die getrennten Wahrheiten: Arbeitsbereich (UI-Zustand) · Ansicht (Modell-Store) ·
 * Auswahltypen (Modell). **Die vier Fähigkeiten sind Tatsachen, keine Erfindung** — Szene geladen,
 * aktives Geschoss, Wände im Geschoss, Zeichenfläche breiter als 0.
 *
 * **`stageBreite > 0` ist die einzige nicht ausgedachte Schwelle** (AUF-42): dort hört die Fläche
 * auf zu existieren. Jede andere Zahl wäre eine Meinung mit Nachkommastelle.
 */
export function werkzeugKontextAus(e: KontextEingaben): AktivierungsKontext {
  return baueAktivierungsKontext({
    workspace: e.workspace,
    view: e.view as ViewType,
    selectionTypes: e.selectedNodeIds
      .map((id) => e.nodes.find((n) => n.id === id)?.type)
      .filter((t): t is NonNullable<typeof t> => Boolean(t)) as ObjectType[],
    permissions: e.rechte,
    capabilities: [
      ...(e.hatSzene ? [FAEHIGKEIT_PROJEKT_OFFEN] : []),
      ...(e.hatGeschoss ? [FAEHIGKEIT_GESCHOSS_DA] : []),
      ...(e.wandZahl > 0 ? [FAEHIGKEIT_WAND_DA] : []),
      ...(e.stageBreite > 0 ? [FAEHIGKEIT_ANSICHT_BEREIT] : []),
    ],
  });
}

/** Was der Wegweiser sagt — oder `null`, wenn es nichts Belegbares zu sagen gibt. */
export interface Wegweiser {
  satz: string;
  ort: string;
}

/**
 * AUF-45 — der nächste sinnvolle Schritt.
 *
 * **Keine zweite Aktivierungs-Engine:** gezählt werden die Zustände, die `resolveToolState` ohnehin
 * liefert; die Zahl im Satz ist die **gemessene** Differenz zwischen jetzt und dem Zustand nach dem
 * Schritt — dieselbe Engine, nur ein zweites Mal gefragt. **Ohne benannte Handlung zu einem Grund
 * schweigt der Wegweiser, statt zu raten.**
 */
export function ermittleWegweiser(kontext: AktivierungsKontext, nodes: readonly SceneNode[]): Wegweiser | null {
  const alle = [...TOOL_DEFINITIONS, ...TOOL_KATALOG];
  const jetzt = alle.map((t) => resolveToolState(t, kontext));
  // Vorhandene Bauteiltypen — gemessen, nicht erfunden. Ohne Bauteile hat „wähle etwas aus"
  // keinen Sinn, egal was die Zählung sagt.
  const typenImPlan = [...new Set(nodes.map((n) => n.type))] as ObjectType[];

  const kandidaten = [...new Set(jetzt.filter((z) => !z.enabled).map((z) => z.reason ?? ''))]
    .map((grund) => ({ grund, h: handlungZuGrund(grund) }))
    .filter((k) => k.h?.ort && (k.h.faehigkeit || (k.h.brauchtAuswahl && typenImPlan.length > 0)))
    .map((k) => ({
      grund: k.grund,
      handlung: k.h!.handlung,
      ort: k.h!.ort!,
      danach: alle.map((t) => resolveToolState(t, k.h!.faehigkeit
        ? { ...kontext, capabilities: [...kontext.capabilities, k.h!.faehigkeit as string] }
        : { ...kontext, selection: { count: 1, types: typenImPlan, states: [] } })),
    }));
  const w = naechsterSchritt(jetzt, kandidaten);
  if (!w) return null;
  const gewaehlt = kandidaten.find((k) => k.grund === w.grund);

  return gewaehlt ? { satz: wegweiserSatz(w, gewaehlt.handlung), ort: gewaehlt.ort } : null;
}

/**
 * AUF-34 / Kante 3 — gilt das aktive Werkzeug im gewählten Arbeitsbereich?
 *
 * Der Name des fremden Bereichs, sonst `undefined`. Gelesen wird `supportedWorkspaces`, also
 * **dieselbe** Quelle, die `resolveToolState` als erste Regel prüft — keine zweite Beurteilung.
 */
export function fremderBereichVon(werkzeug: string, activeWorkspace: string): string | undefined {
  const t = toolNach(werkzeug);
  if (!t || t.supportedWorkspaces.length === 0) return undefined;
  if (t.supportedWorkspaces.includes(activeWorkspace)) return undefined;

  return arbeitsbereich(t.supportedWorkspaces[0])?.label ?? t.supportedWorkspaces[0];
}

/** Die Eingaben der Befehlspalette — alles bereits abgeleitete Werte. */
export interface PaletteEingaben {
  kontext: AktivierungsKontext;
  stapel: Parameters<typeof palettenGruppen>[0]['stapel'];
  baum: Parameters<typeof palettenGruppen>[0]['baum'];
  schritt: Wegweiser | null;
  filter: string;
}

/**
 * AUF-67 — **die Palette fragt die Register, sie weiß nichts selbst.** `baum` und `schritt` sind
 * dieselben Ergebnisse, die die Oberfläche ohnehin anzeigt; der Geschoss-Stapel kommt aus derselben
 * Funktion, die die Geschossfläche benutzt. **Kein Register wird hier zweimal gerechnet.**
 */
export function palettenGruppenFuer(e: PaletteEingaben): ReturnType<typeof palettenGruppen> {
  return palettenGruppen({
    kontext: e.kontext,
    stapel: e.stapel,
    baum: e.baum,
    schritt: e.schritt,
  }, e.filter);
}

export type { PaletteEintrag };
