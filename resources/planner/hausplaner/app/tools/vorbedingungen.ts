/**
 * AUF-36 — die **12 Vorbedingungen** des Funktionsvertrags, abgebildet auf die Aktivierung.
 *
 * **Die bindende Vorgabe (§3a des Auftrags):** `preconditions` gehen als **Daten** in
 * `resolveToolState` — es entsteht **kein zweites `resolveDisabledReasons`** und kein `runTool`
 * daneben. Deshalb übersetzt dieses Modul jede Vorbedingung in eine `ToolActivationRule`, die die
 * vorhandene Engine ohnehin auswertet. Die Engine bleibt die eine Wahrheit über „darf ich das
 * jetzt?"; dieses Modul liefert ihr nur Futter.
 *
 * ## Alle zwölf sind zugeordnet — keine Zeile „sonstige"
 *
 * **Sieben sind heute erfüllbar** (offener Plan, Geschoss, Wand, Zeichenfläche, Auswahl, Dachfläche,
 * Bearbeitungsrecht). **Fünf sind es nicht** — und die werden nach §3c des Auftrags **nicht
 * ausgelassen und schon gar nicht auf „erfüllt" verdrahtet**, damit eine Kachel klickbar aussieht.
 * Sie sind **unerfüllt mit ehrlichem Grund**: das Werkzeug bleibt sichtbar, ist gesperrt und sagt
 * in einem deutschen Satz, was fehlt. Genau dafür gibt es den Vertrag.
 *
 * **Warum das kein Trick ist:** Die fünf hängen an derselben `capabilities`-/`permissions`-Liste
 * wie die sieben anderen. Sie sind nicht „hart false", sondern schlicht **nicht in der Liste** —
 * weil niemand sie heute messen kann. Führt eines Tages die Auslegung eine freigegebene Heizlast,
 * trägt sie den Wert ein und dieselbe Regel geht von selbst auf grün. **Kein Sonderweg, kein
 * späterer Umbau.**
 *
 * Vier der fünf sind **fachliche Operanden**: `component.thermalRelevant` · `heatingLoad.approved` ·
 * `heatEmitters.sized` · `heatingNetwork.connected`. Sie kommen aus der Auslegung (L2/L3), nicht aus
 * der Geometrie.
 *
 * Die fünfte ist eine **Rechte-Frage**: `permission.import`. Gemessen kennt das CRM nur
 * `Hausplaner,read` und `Hausplaner,update` (`routes/web.php`). Der Auftrag ordnet
 * `permission.edit`/`permission.import` den Rechten zu — also hängt Import an einem Recht, das es
 * noch nicht gibt, und die acht Import-Werkzeuge sind gesperrt statt stillschweigend freigegeben.
 * **Ob Import an `update` hängt oder ein eigenes Recht bekommt, ist eine Rechte-Entscheidung** und
 * geht als Rückgabe (§6) an Planner/Yama.
 *
 * ## Wo die erfüllbaren Werte herkommen
 *
 * `project.open`, `viewport.ready`, `activeLevel.exists` und `hostWall.exists` sind Tatsachen, die
 * `HausplanerApp` ohnehin kennt (Szene geladen, Canvas gemountet, aktives Geschoss, Wände im
 * Geschoss). Sie fließen über die **bereits vorhandene** `capabilities`-Liste des
 * Aktivierungskontexts — der dafür vorgesehene Haken, der bisher leer lag. Kein neues Feld im
 * `AktivierungsKontext`, kein neuer Mechanismus.
 *
 * ## Gründe sind deutsche Sätze, nicht Vokabular (§4.3)
 *
 * „Kein aktives Geschoss." schlägt „Vorbedingung `activeLevel.exists` nicht erfüllt." Der Nutzer
 * liest den Grund, nicht den Feldnamen.
 */
import type { ToolActivationRule } from './toolTypes';

/**
 * Was aus einer Vorbedingung wird. **Jede** der zwölf liefert eine Regel — `heuteErfuellbar`
 * sagt nur, ob sie im heutigen Planer überhaupt zutreffen *kann*. Das ist kein zweiter
 * Mechanismus, sondern eine Aussage über den Datenstand, und sie ist testbar.
 */
export interface VorbedingungAbbildung {
  regel: ToolActivationRule;
  heuteErfuellbar: boolean;
  /**
   * AUF-45: die **Aufforderung** in Klartext („Lege ein Geschoss an"). Sie steht hier und nicht in
   * einem eigenen Register: derselbe Eintrag, der die Sperre erklärt, sagt auch, was sie aufhebt.
   * Fehlt sie, gibt es für diese Vorbedingung keinen Wegweiser — kein erfundener Ratschlag.
   */
  handlung?: string;
  /** Warum sie heute nicht erfüllbar ist — leer, wenn sie es ist. Gehört in Bericht und Test. */
  lueckeGrund?: string;
}

/**
 * Die Fähigkeiten, die `HausplanerApp` in den Kontext gibt. Als Konstanten, damit die Zeichenkette
 * an genau einer Stelle steht: hier wird sie geprüft, dort gesetzt. Ein Tippfehler auf einer der
 * beiden Seiten wäre sonst eine stumm immer-verletzte Bedingung.
 */
export const FAEHIGKEIT_PROJEKT_OFFEN = 'project.open';
export const FAEHIGKEIT_ANSICHT_BEREIT = 'viewport.ready';
export const FAEHIGKEIT_GESCHOSS_DA = 'activeLevel.exists';
export const FAEHIGKEIT_WAND_DA = 'hostWall.exists';

/**
 * Fachstände, die heute **niemand** in den Kontext gibt. Sie stehen hier als Konstanten und nicht
 * als lose Zeichenkette, damit die Auslegung sie eines Tages **an dieser Stelle** eintragen kann —
 * dann geht dieselbe Regel von selbst auf grün, ohne dass jemand Code umbaut.
 */
export const FACHSTAND_BAUTEIL_THERMISCH = 'component.thermalRelevant';
export const FACHSTAND_HEIZLAST_FREIGEGEBEN = 'heatingLoad.approved';
export const FACHSTAND_HEIZFLAECHEN_AUSGELEGT = 'heatEmitters.sized';
export const FACHSTAND_HEIZNETZ_VERBUNDEN = 'heatingNetwork.connected';

/** Das Recht, das die App heute mitgibt — Bestand, nicht neu erfunden. */
export const RECHT_BEARBEITEN = 'Hausplaner,update';
/** Das Recht, das der Auftrag für den Import vorsieht — im CRM heute NICHT vergeben. */
export const RECHT_IMPORTIEREN = 'Hausplaner,import';

const faehigkeit = (wert: string, grund: string, luecke?: string): VorbedingungAbbildung => ({
  regel: { type: 'capability', operator: 'contains', value: wert, grund },
  heuteErfuellbar: !luecke,
  ...(luecke ? { lueckeGrund: luecke } : {}),
});

/**
 * Die vollständige Tabelle. **Alle 12 Einträge stehen hier**, jeder mit Regel und deutschem Grund —
 * auch die fünf, die heute nicht erfüllbar sind. Eine Vorbedingung, die fehlte, würde
 * stillschweigend übergangen; `unbekannteVorbedingungen()` und sein Test verhindern genau das.
 */
export const VORBEDINGUNGEN: Readonly<Record<string, VorbedingungAbbildung>> = {
  'project.open': faehigkeit(
    FAEHIGKEIT_PROJEKT_OFFEN,
    'Es ist kein Plan geöffnet.',
  ),
  'viewport.ready': faehigkeit(
    FAEHIGKEIT_ANSICHT_BEREIT,
    'Die Zeichenfläche ist noch nicht bereit.',
  ),
  'activeLevel.exists': {
    ...faehigkeit(FAEHIGKEIT_GESCHOSS_DA, 'Kein aktives Geschoss.'),
    handlung: 'Lege ein Geschoss an',
  },
  'hostWall.exists': {
    ...faehigkeit(FAEHIGKEIT_WAND_DA, 'Dafür braucht es zuerst eine Wand, in die das Bauteil gesetzt wird.'),
    handlung: 'Zeichne eine Wand',
  },
  'selection.count >= 1': {
    regel: {
      type: 'selection-count', operator: 'greater-than', value: 0,
      grund: 'Dafür muss zuerst etwas ausgewählt sein.',
    },
    heuteErfuellbar: true,
    handlung: 'Wähle ein Bauteil aus',
  },
  'selection.hasRoofFace': {
    regel: {
      type: 'selection-type', operator: 'contains', value: 'roof',
      grund: 'Dafür muss eine Dachfläche ausgewählt sein.',
    },
    heuteErfuellbar: true,
  },
  'permission.edit': {
    regel: {
      type: 'permission', operator: 'contains', value: RECHT_BEARBEITEN,
      grund: 'Keine Berechtigung zum Bearbeiten.',
    },
    heuteErfuellbar: true,
  },

  // --- heute nicht erfüllbar: Regel bleibt, Grund steht da, nichts wird behauptet ---------------
  'permission.import': {
    regel: {
      type: 'permission', operator: 'contains', value: RECHT_IMPORTIEREN,
      grund: 'Keine Berechtigung zum Importieren.',
    },
    heuteErfuellbar: false,
    lueckeGrund: 'Das CRM kennt nur `Hausplaner,read` und `Hausplaner,update`; ein Import-Recht ist '
      + 'nicht vergeben. Ob Import an `update` hängt oder ein eigenes Recht bekommt, ist eine '
      + 'Rechte-Entscheidung und geht als Rückgabe an Planner/Yama.',
  },
  'component.thermalRelevant': faehigkeit(
    FACHSTAND_BAUTEIL_THERMISCH,
    'Nur für thermisch relevante Bauteile — diese Angabe kommt aus der Bauphysik-Auslegung.',
    'Der Planer führt die Eigenschaft „thermisch relevant" heute an keinem Bauteil.',
  ),
  'heatingLoad.approved': faehigkeit(
    FACHSTAND_HEIZLAST_FREIGEGEBEN,
    'Dafür muss die Heizlast berechnet und freigegeben sein.',
    'Eine freigegebene Heizlast ist ein Ergebnis der Auslegung (L2/L3), nicht der Geometrie; der '
      + 'Planer kennt keinen Auslegungsstand.',
  ),
  'heatEmitters.sized': faehigkeit(
    FACHSTAND_HEIZFLAECHEN_AUSGELEGT,
    'Dafür müssen die Heizflächen ausgelegt sein.',
    'Ausgelegte Heizflächen kommen aus der Heizkörper-/FBH-Auslegung; im Planer liegt kein '
      + 'Auslegungsstand.',
  ),
  'heatingNetwork.connected': faehigkeit(
    FACHSTAND_HEIZNETZ_VERBUNDEN,
    'Dafür muss das Heiznetz verbunden sein.',
    'Ein verbundenes Heiznetz setzt die Hydraulik-Verrohrung voraus; sie ist im Modell nicht '
      + 'geführt.',
  ),
};

/**
 * Die Regeln zu einer Liste von Vorbedingungen — **alle**, auch die heute unerfüllbaren.
 * Eine auszulassen hieße, das Werkzeug klickbar zu machen, obwohl seine Voraussetzung fehlt.
 */
export function regelnFuer(vorbedingungen: readonly string[]): ToolActivationRule[] {
  const regeln: ToolActivationRule[] = [];
  for (const v of vorbedingungen) {
    const a = VORBEDINGUNGEN[v];
    if (a) regeln.push(a.regel);
  }
  return regeln;
}

/** Die heute nicht erfüllbaren Vorbedingungen einer Liste — für Bericht und Sichtbarkeit der Lücke. */
export function heuteUnerfuellbar(vorbedingungen: readonly string[]): string[] {
  return vorbedingungen.filter((v) => VORBEDINGUNGEN[v] && !VORBEDINGUNGEN[v].heuteErfuellbar);
}

/** Alle heute unerfüllbaren Vorbedingungen mit ihrem Grund — die Rückgabe-Liste (§6). */
export function offeneLuecken(): Array<{ vorbedingung: string; grund: string }> {
  return Object.entries(VORBEDINGUNGEN)
    .filter(([, a]) => !a.heuteErfuellbar)
    .map(([vorbedingung, a]) => ({ vorbedingung, grund: a.lueckeGrund ?? '' }));
}

/** Vorbedingungen aus dem Vertrag, die in dieser Tabelle fehlen — muss leer sein. */
export function unbekannteVorbedingungen(ausVertraegen: readonly string[]): string[] {
  return ausVertraegen.filter((v) => !(v in VORBEDINGUNGEN));
}

/**
 * AUF-45 — die Aufforderung zu einem **Sperrgrund** (nicht zur Vorbedingung: der Wegweiser kennt
 * nur den Grundtext, den `resolveToolState` geliefert hat). `undefined`, wenn es zu diesem Grund
 * keine benannte Handlung gibt — dann schweigt der Wegweiser, statt zu raten.
 */
export function handlungZuGrund(grund: string): { handlung: string; faehigkeit?: string } | undefined {
  for (const a of Object.values(VORBEDINGUNGEN)) {
    if (a.regel.grund !== grund || !a.handlung) continue;
    return {
      handlung: a.handlung,
      // Nur Fähigkeits-Regeln lassen sich hypothetisch erfüllen (Kontext um einen Wert ergänzen).
      faehigkeit: a.regel.type === 'capability' ? String(a.regel.value) : undefined,
    };
  }
  return undefined;
}
