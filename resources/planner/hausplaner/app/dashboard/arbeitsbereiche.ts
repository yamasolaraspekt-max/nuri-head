/**
 * AUF-34 — die fünf **Arbeitsbereiche** und ihre Themen.
 *
 * **Der Mangel, gemessen:** die obere Leiste trug 22 Gruppen nebeneinander und lief bei 1440 px
 * über drei Zeilen; `Bearbeiten` hatte 13 Werkzeuge, `TGA` und `Sanitär` je eines. Die fehlende
 * Ebene lag dabei fertig im Code und wurde nicht benutzt: `state/uiState.ts:23` führt
 * `activeWorkspace`, `toolRegistry.ts` kannte genau **einen** Wert.
 *
 * **Zwei Klassen — das ist der Kern dieses Moduls:**
 * - **durchgängig** (7 Themen): in *jedem* Arbeitsbereich sichtbar. Auswählen, Bearbeiten, Ansicht,
 *   Messen, Prüfen, System — und **Wizard/Workflow**, weil der Ablauf nicht am Gewerk hängt,
 *   sondern am Projektfortschritt; ihn in einen Bereich zu sperren zerschnitte ihn.
 * - **gebunden** (8 Themen): nur im zugehörigen Bereich.
 *
 * **Eine Wahrheit:** Die Bindung steht hier und **nur** hier. `paketAdapter.ts` leitet daraus
 * `supportedWorkspaces` je Werkzeug ab (damit `resolveToolState` dieselbe Antwort gibt),
 * `werkzeugGruppen.ts` filtert daraus die Leiste. Keine Kopie, keine zweite Tabelle.
 *
 * **Drei Lücken werden hier NICHT geschlossen** (Auftrag: zurückgeben, nicht überkleben):
 * „Dach" ist im Entwurf ein Bereich, im Paket aber keine eigene Kategorie/kein eigenes Thema;
 * „Heizlast" ist ein Rechenweg (L2/L3), kein Werkzeugbereich; `Bad`/`Küche` hängen vorläufig an
 * Architektur — ob sie einen eigenen Bereich „Ausbau" bekommen, ist Yamas Willensfrage.
 */
import type { WorkspaceId } from '../tools/toolTypes';
import {
  WORKSPACE_IMPORT, WORKSPACE_ARCHITEKTUR, WORKSPACE_BAUPHYSIK,
  WORKSPACE_HEIZUNG, WORKSPACE_ELEKTRO_PV,
} from '../tools/toolRegistry';
import { WERKZEUG_THEMEN, type WerkzeugThema } from '../tools/werkzeugThemen';

export interface Arbeitsbereich {
  id: WorkspaceId;
  label: string;
  /** Ein Satz für den Tooltip: was man in diesem Bereich tut. */
  hinweis: string;
  /** Themen, die NUR hier erscheinen. Die durchgängigen kommen aus `DURCHGAENGIGE_THEMEN`. */
  themen: readonly string[];
}

/**
 * Themen, die in **jedem** Bereich stehen. Sieben von fünfzehn — bewusst, nicht zufällig: ohne
 * Auswahl, Bearbeiten, Ansicht und Messen ist kein Bereich bedienbar, und Prüfung, System und
 * Wizard hängen am Projekt, nicht am Gewerk.
 */
export const DURCHGAENGIGE_THEMEN: readonly string[] = [
  '01-grundbedienung',
  '02-transformieren',
  '04-ansicht-navigation',
  '05-messen-dokumentieren',
  '13-workflow-pruefung',
  '14-pruefung-zusammenarbeit',
  '15-system-export',
];

/** Die fünf Bereiche aus Yamas Entwurf. Keiner erfunden, keiner weggelassen. */
export const ARBEITSBEREICHE: readonly Arbeitsbereich[] = [
  {
    id: WORKSPACE_IMPORT,
    label: 'Import & Nachzeichnen',
    hinweis: 'Vorlage laden, kalibrieren, Grundriss nachzeichnen und erkennen lassen.',
    themen: ['06-import-erkennung'],
  },
  {
    id: WORKSPACE_ARCHITEKTUR,
    label: 'Architektur',
    hinweis: 'Gebäude zeichnen und ausbauen: Wände, Öffnungen, Dach, Material, Bad und Küche.',
    themen: ['03-zeichnen-cad', '07-architektur', '08-material-fassade', '11-bad-kueche'],
  },
  {
    id: WORKSPACE_BAUPHYSIK,
    label: 'Bauphysik',
    hinweis: 'Thermische Hülle, U-Werte, Dämmung und Lüftung.',
    themen: ['09-bauphysik'],
  },
  {
    id: WORKSPACE_HEIZUNG,
    label: 'Heizung',
    hinweis: 'Wärmeerzeuger, Heizflächen, Hydraulik und Verteilung.',
    themen: ['10-heizung-tga'],
  },
  {
    id: WORKSPACE_ELEKTRO_PV,
    label: 'Elektro · PV',
    hinweis: 'Elektroinstallation, PV-Belegung, Speicher und Wallbox.',
    themen: ['12-elektro-pv'],
  },
];

/**
 * Standard ist **Architektur** — der bisher einzige Wert. Nach dem Umbau steht niemand vor einer
 * anderen Leiste als vorher.
 */
export const ARBEITSBEREICH_STANDARD: WorkspaceId = WORKSPACE_ARCHITEKTUR;

/** Ein Bereich nach id (oder undefined). */
export function arbeitsbereich(id: string): Arbeitsbereich | undefined {
  return ARBEITSBEREICHE.find((b) => b.id === id);
}

/**
 * Die Themen eines Bereichs — durchgängige **und** gebundene, in Paket-Reihenfolge (01…15).
 * Unbekannte id ⇒ nur die durchgängigen: die Leiste wird dünn, aber nie leer und wirft nicht.
 */
export function themenFuer(bereichId: string): readonly WerkzeugThema[] {
  const gebunden = arbeitsbereich(bereichId)?.themen ?? [];
  const erlaubt = new Set([...DURCHGAENGIGE_THEMEN, ...gebunden]);
  return WERKZEUG_THEMEN.filter((t) => erlaubt.has(t.id));
}

/**
 * Der Bereich, an den ein Thema gebunden ist — oder `undefined`, wenn es durchgängig ist.
 * Das ist die Quelle für `supportedWorkspaces` im Adapter: **leere Liste heißt weiterhin
 * „überall gültig"**, die bestehende Bedeutung wird nicht geändert, nur endlich benutzt.
 */
export function bereichVonThema(themaId: string): WorkspaceId | undefined {
  return ARBEITSBEREICHE.find((b) => b.themen.includes(themaId))?.id;
}

/** Themen, die weder durchgängig noch gebunden sind — muss leer sein, sonst wäre eines unerreichbar. */
export function themenOhneBereich(): string[] {
  return WERKZEUG_THEMEN
    .filter((t) => !DURCHGAENGIGE_THEMEN.includes(t.id) && !bereichVonThema(t.id))
    .map((t) => t.id);
}
